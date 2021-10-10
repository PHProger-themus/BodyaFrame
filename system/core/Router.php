<?php

namespace system\core;

use system\classes\ArrayHolder;
use system\classes\LinkBuilder;

class Router
{

    private $routes;
    private $params;
    private $vars = array();

    public function __construct()
    {
        $this->routes = Cfg::$get->routes;
    }

    private function notAvailableForAllIPs()
    {
        $IPs = Cfg::$get->allowedFor;
        return !(count($IPs) == 1 && $IPs[0] == '*');
    }

    private function maintenance()
    {
        if (!Cfg::$get->active && ($this->notAvailableForAllIPs() && !in_array($_SERVER['REMOTE_ADDR'], Cfg::$get->allowedFor))) {
            Errors::renderPage('maintenance');
        }
    }

    private function removeLocale(string $url)
    {
        if (Cfg::$get->multilang) {
            $lang = explode('/', $url)[1];
            if (isset(Cfg::$get->langs[$lang])) { //Если в конфиге нет нужной локали, мы не используем
                Cfg::$get->lang = $lang;
                return substr($url, strlen($lang) + 2);
            } else {
                LinkBuilder::redirect($url, Cfg::$get->lang);
            }
        }
        return trim($url, '/');
    }

    private function removePrefix(string $url)
    {
        $prefix = Cfg::$get->website['prefix'];
        if (!empty($prefix)) {
            if (str_starts_with($url, $prefix)) {
                $url = substr(Cfg::$get->url, strlen($prefix));
                if (empty($url)) {
                    $url = "/";
                }
            } else {
                Errors::code(404);
            }
        }
        return $url;
    }

    public function match()
    {
        Cfg::$get->route = new Page();
        $this->maintenance();
        $url = Cfg::$get->url;
        $url = $this->removePrefix($url);
        $url = $this->removeLocale($url);

        foreach ($this->routes as $query => $params) {

            $pattern = preg_replace_callback("/{([A-Za-z]*)}/", function ($matches) use (&$names) {
                $names[] = $matches[1];
                return "([0-9A-Za-z-]+)";
            }, $query);

            if (preg_match_all("~^$pattern$~", $url, $matches)) {

                $this->params = $params;

                array_shift($matches);
                for ($i = 0, $vars = count($matches); $i < $vars; $i++) {
                    $this->vars[$names[$i]] = $matches[$i][0];
                }

//                $breadcrumb = $params['breadcrumb'];
//                foreach ($this->vars as $var => $val) {
//                    $breadcrumb = str_replace('{' . $var . '}', $val, $breadcrumb);
//                }
                Cfg::$get->route = new Page($params['controller'], $params['action'], "", $query);
                return true;
            }

            $names = array();

        }
        Cfg::$get->route = new Page();
        return false;
    }

    public function run()
    {
        if ($this->match()) {
            $controller = ucfirst($this->params['controller']) . 'Controller';
            $controller_class = "\\app\\controllers\\$controller";
            $action = $this->params['action'] . "Action";

            if (!class_exists($controller_class)) {
                throw new \Error("Не найден контроллер \"$controller\"");
            } elseif (!method_exists($controller_class, $action)) {
                throw new \Error("Не найден метод \"$action\" в контроллере \"$controller\"");
            } else {
                $controller_object = new $controller_class();
                if ($this->enabledAndNotRestricted() || $this->disabledAndUseFolder()) {
                    Lang::init($this->params['controller'] . '/' .$this->params['action']);
                }
                $controller_object->$action(ArrayHolder::new($this->vars), $this->getRulesResults($controller_object));
            }
        } else {
            Errors::code(404);
        }
    }

    private function enabledAndNotRestricted()
    {
        return Cfg::$get->multilang && !isset($this->params['nolang']) && !isset($this->params['service']);
    }

    private function disabledAndUseFolder()
    {
        return !Cfg::$get->multilang && isset(Cfg::$get->useFile) && Cfg::$get->useFile;
    }

    private function getRulesResults(Controller $controller_object)
    {
        if (method_exists($controller_object, 'rules')) {
            $rules_array = $controller_object->rules();
            $rules_result = $this->checkRules($rules_array, $this->params['action']);
            if (!is_null($rules_result)) {
                return $rules_result;
            }
        }
    }

    private function checkRules($rules_array, $action)
    {
        foreach ($rules_array as $rule => $action_array) {
            if (in_array($action, $action_array)) {
                $rule_class = "\\app\\user\\rules\\" . ucfirst($rule) . "Rule";
                if (!class_exists($rule_class)) {
                    throw new \Error("Не найдено правило \"$rule_class\"");
                } else {
                    return (new $rule_class())->apply($this->vars);
                }
            }
        }
    }

}
