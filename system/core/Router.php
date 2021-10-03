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

    public function match()
    {
        $url = str_replace(Cfg::$get->website['prefix'], '', Cfg::$get->url);

        if (Cfg::$get->multilang && strpos($url, 'sv_') === false) {

            $lang = explode('/', $url)[1];

            if (isset(Cfg::$get->langs[$lang])) { //Если в конфиге нет нужной локали, мы не используем
                Cfg::$get->lang = $lang;
                $url = substr($url, strlen($lang) + 2);
            } else {
                LinkBuilder::raw($url, Cfg::$get->lang);
                $url = trim($url, '/');
            }

        } else {
            $url = trim($url, '/');
        }

        $names = array();

        foreach ($this->routes as $query => $params) {

            $pattern = preg_replace_callback("/{([A-Za-z]*)}/", function ($matches) use (&$names) {
                $names[] = $matches[1];
                return "([0-9A-Za-z-]+)";
            }, $query);

            if (preg_match_all("~^$pattern$~", $url, $matches)) {

                $this->params = $params;

                array_shift($matches);
                for ($i = 0; $i < count($matches); $i++) {
                    $this->vars[$names[$i]] = $matches[$i][0];
                }

                $breadcrumb = $params['breadcrumb'];
                foreach ($this->vars as $var => $val) {
                    $breadcrumb = str_replace('{' . $var . '}', $val, $breadcrumb);
                }
                Cfg::$get->route = new Page($params['controller'], $params['action'], $breadcrumb, $query);
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
            $controller_class = '\\app\\controllers\\' . $controller;
            $action = $this->params['action'] . "Action";

            if (!class_exists($controller_class)) {
                if (Cfg::$get->debug) {
                    throw new \Error("Не найден контроллер " . $controller);
                } else {
                    Errors::code(404);
                }
            } elseif (!method_exists($controller_class, $action)) {
                if (Cfg::$get->debug) {
                    throw new \Error("Не найден метод " . $action . " в контроллере " . $controller);
                } else {
                    Errors::code(404);
                }
            } else {
                $controller_object = new $controller_class();

                $rules_vars = null;
                if (method_exists($controller_object, 'rules')) {
                    $rules_array = $controller_object->rules();
                    $rules_result = $this->checkRules($rules_array, $this->params['action']);
                    if (!is_null($rules_result)) $rules_vars = $rules_result;
                }

                if ($this->enabledAndNotRestricted() || $this->disabledAndUseFolder()) {
                    Lang::init($this->params['controller'], $this->params['action']);
                }
                $controller_object->$action(ArrayHolder::new($this->vars), $rules_vars);
            }
        } else
            Errors::code(404);
    }

    private function enabledAndNotRestricted()
    {
        return Cfg::$get->multilang && !isset($this->params['nolang']);
    }

    private function disabledAndUseFolder()
    {
        return !Cfg::$get->multilang && isset(Cfg::$get->useFile) && Cfg::$get->useFile;
    }

    public function checkRules($rules_array, $action)
    {
        foreach ($rules_array as $rule => $action_array) {
            if (in_array($action, $action_array)) {
                $rule_class = '\\app\\user\\rules\\' . ucfirst($rule) . 'Rule';
                if (!class_exists($rule_class) && Cfg::$get->debug) {
                    throw new \Error("Не найдено правило " . $rule_class);
                } else {
                    return (new $rule_class())->apply($this->vars);
                }
            }
        }
    }

}
