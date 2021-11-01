<?php

namespace system\core;

use system\classes\ArrayHolder;
use system\classes\LinkBuilder;use system\classes\SafetyManager;
use system\classes\Server;

class View
{

    private $route;
    private $breadcrumbs;
    private string $app_prefix, $css_folder, $js_folder;
    private string|array $css_files, $js_files;
    private bool $disable_cache;
    private string $page_name;

    public function __construct(int $page_type = 1, ?string $page_name = null)
    {
        if (!$page_type) {
            $this->route = 'error';
            $this->page_name = $page_name;
        } else {
            $this->route = Cfg::$get->route->getController() . '/' . Cfg::$get->route->getAction();
        }
        $this->app_prefix = Cfg::$get->website['prefix'];
        $this->css_folder = Cfg::$get->cssFolder;
        $this->css_files = Cfg::$get->links['css'];
        $this->js_folder = Cfg::$get->jsFolder;
        $this->js_files = Cfg::$get->links['js'];
        $this->disable_cache = Cfg::$get->disableCache;
    }

    public function render($vars = array()): void
    {
        extract($this->filterData($vars));

        $page = Cfg::$get->route;
        $page->css = $this->getCSSList();
        $page->js = $this->getJSList();

        $page->alternate = $this->alternate();

        $view_file = $this->getPage();
        if (file_exists($view_file)) {
            ob_start();
            require $view_file;
            $content = ob_get_clean();
        } else {
             throw new \Error("Не найден файл View " . $this->route . ".php");
        }

        $this->sendHeaders();

        require APP_DIR . "/views/common/wrapper.php";
    }

    private function getPage()
    {
        $meta = require APP_DIR . "/lang/" . Cfg::$get->lang . "/meta.php";
        if ($this->route == 'error') {
            $meta_system = require SYSTEM_DIR . "/lang/" . Cfg::$get->lang . "/meta.php";
            $error_meta_data = 'error' . ucfirst($this->page_name);
            Cfg::$get->route->init($meta[$error_meta_data] ?? $meta_system[$error_meta_data]);
            return APP_DIR . "/views/error/" . $this->page_name . ".php";
        } else {
            if (array_key_exists($this->route, $meta)) {
                Cfg::$get->route->init($meta[$this->route]);
            } else {
                Cfg::$get->route->init(['title' => $this->route]);
            }
            return APP_DIR . "/views/{$this->route}.php";
        }
    }

    private function alternate()
    {
        $an_string = "";
        if (Cfg::$get->multilang && $this->route != 'error') {
            foreach (Cfg::$get->langs as $lang => $local) {
                $an_string .= "\n<link rel='alternate' href='" . LinkBuilder::url(Cfg::$get->route->getController(), Cfg::$get->route->getAction(), ['lang' => $lang]) . "' hreflang='$lang' />";
            }
        }
        return $an_string;
    }

    private function getCSSList()
    {
        $css = "";
        foreach ($this->css_files as $css_key => $css_file) {
            $this->includeFiles($css_key, $css_file, $css, 'css'); // Перебираем каждый файл и начинаем его проверку
        }
        return $css;
    }

    private function getJSList()
    {
        $js = "";
        foreach ($this->js_files as $js_key => $js_file) {
            $this->includeFiles($js_key, $js_file, $js, 'js');
        }
        return $js;
    }

    private function routeIsAllowed(array $file) // Если нет подмассива исключенных маршрутов, значит массив содержит только разрешенные маршруты. Проверяем, есть ли там наш.
    {
        return !isset($file['exclude']) && in_array($this->route, $file);
    }

    private function routeIsNotProhibited(array $file) // Если же подмассив исключенных маршрутов есть, смотрим, нет ли там нашего.
    {
        return isset($file['exclude']) && !in_array($this->route, $file['exclude']);
    }

    private function css(string $file)
    {
        return "<link rel='stylesheet' href='" . $this->app_prefix . $this->css_folder . "/" . $file . (!$this->disable_cache ? "?v=" . date('YmdHis') : '') . "' type='text/css'>\n";
    }

    private function js(string $file)
    {
        return "<script src='" . $this->app_prefix . $this->js_folder . "/" . $file . (!$this->disable_cache ? "?v=" . date('YmdHis') : '') . "'></script>\n";
    }

    private function includeFiles($key, $file, &$str, string $type)
    {
        if (is_string($file)) {
            $str .= $this->$type($file); // Если в конфиге файл как строка, подключаем его, он для всех маршрутов. $type - какую функцию вызывать, js или css.
        } elseif (is_array($file) && ($this->routeIsAllowed($file) || $this->routeIsNotProhibited($file))) { // Иначе если он массив, и выполняются 2 условия, то подключаем ключ этого массива, где содержится файл.
            $str .= $this->$type($key);
        }
    }

    private function sendHeaders()
    {
        $csp = $this->configureCSP();
        if ($csp) {
            header($csp);
        }
        if (isset(Cfg::$get->safety['xFrameOptions'])) {
            header("X-Frame-Options: " . Cfg::$get->safety['xFrameOptions']);
        }
    }

    private function configureCSP()
    {
        if (!isset(Cfg::$get->safety['csp']) || is_null(Cfg::$get->safety['csp'])) {
            return false;
        } else {
            $csp = Cfg::$get->safety['csp'];
            $csp_header = "Content-Security-Policy:";

            $csp_rules = $csp['rules'];
            if (isset($csp_rules[$this->route])) {
                $this->addCSPRule($csp_rules[$this->route], $csp_header);
            } elseif (isset($csp_rules['none']) && array_search($this->route, $csp_rules['none']) !== false) {
                return false;
            } elseif (isset($csp_rules['*'])) {
                $this->addCSPRule($csp_rules['*'], $csp_header);
            } else {
                return false;
            }

            if (isset($csp['report'])) {
                $csp_header .= " report-uri {$csp['report']}";
            }

            return $csp_header;
        }
    }

    private function addCSPRule($route_rules, &$csp_header)
    {
        foreach ($route_rules as $rule => $value) {
            $csp_header .= " {$rule}";
            foreach ($value as $url) {
                if ($url == 'self') $url = "'self'";
                $csp_header .= " {$url}";
            }
            $csp_header .= ";";
        }
    }

    public function filterObject($obj)
    {
        foreach ($obj as $key => &$value) {
            $value = $this->processValue($value);
            $obj->$key = $value;
        }
        return $obj;
    }

    public function filterData($data)
    {
        $filtered = []; // Создаем дубликат
        foreach ($data as $key => $value) {
            $key = SafetyManager::filterString($key);
            $value = $this->processValue($value);
            $filtered[$key] = $value;
        }
        return $filtered; // Подменяем оригинальный массив дубликатом (передача по ссылке не поможет нам заменить ключи массива)
    }

    public function processValue($value)
    {
        if (is_array($value)) {
            return $this->filterData($value);
        } elseif (is_object($value)) {
            return $this->filterObject($value);
        }
        return SafetyManager::filterString($value);
    }

//    public function setBreadcrumbs(array $breadcrumbs)
//    {
//        $this->breadcrumbs = $breadcrumbs;
//    }
//
//    public function getBreadcrumbs()
//    {
//
//        if ($this->breadcrumbs) {
//
//            return $this->breadcrumbs;
//
//        } else {
//
//            $routes = Cfg::$get->routes;
//            $url = null;
//            $url_parts = explode('/', Cfg::$get->route->getPath());
//            $url_parts_filled = explode('/', trim(Cfg::$get->url, '/'));
//            $breadcrumbs = array(Cfg::$get->website['main_page'] => $routes[Cfg::$get->website['main_page']]['title']);
//            $url_params = array();
//
//            for ($i = 0; $i < count($url_parts); $i++) {
//                if ($url_parts[$i] != $url_parts_filled[$i]) $url_params[$url_parts[$i]] = $url_parts_filled[$i];
//            }
//
//            foreach ($url_parts as $path) {
//                $url .= ((is_null($url)) ? $path : '/' . $path);
//                if (array_key_exists($url, $routes)) {
//                    $url_to_replace = $url;
//                    $title_to_replace = $routes[$url]['title'];
//                    foreach ($url_params as $var => $val) {
//                        $url_to_replace = str_replace($var, $val, $url_to_replace);
//                        $title_to_replace = str_replace($var, $val, $title_to_replace);
//                    }
//                    $breadcrumbs[$url_to_replace] = $title_to_replace;
//                }
//            }
//
//            return $breadcrumbs;
//        }
//
//    }

    public static function setPopupMessage(string $message, string $type = Errors::SUCCESS)
    {
        Server::setSession(['popupMessage' => $message, 'popupMessageType' => $type]);
    }

    public static function getPopupMessage()
    {
        if (Server::issetSession('popupMessage')) {
            $messageMethod = Server::getSession('popupMessageType') . 'Popup';
            System::$messageMethod();
            Server::unsetSession(['popupMessage', 'popupMessageType']);
        }
    }

}
