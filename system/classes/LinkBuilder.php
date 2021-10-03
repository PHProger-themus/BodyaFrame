<?php

namespace system\classes;

use system\core\Errors;
use Cfg;

class LinkBuilder
{

    public static function addLang(string $lang = null)
    {
        if (Cfg::$get->multilang) {
            if (!is_null($lang)) {
                return $lang;
            } else {
                return Cfg::$get->lang;
            }
        } else {
            return '';
        }
    }

    public static function url(string $controller, string $action, array $vars = [])
    {
        $routes = Cfg::$get->routes;

        foreach ($routes as $route => $params) {

            if ($params['controller'] == $controller && $params['action'] == $action) {
                if (str_contains($route, '{')) {
                    if (isset($vars['url']) && substr_count($route, '{') == count($vars['url'])) {
                        $url_array = $vars['url'];
                        $route = preg_replace_callback("/{([A-Za-z]*)}/", function ($matches) use (&$url_array) {
                            return array_shift($url_array);
                        }, $route);
                    } else {
                        if (Cfg::$get->debug) {
                            throw new \Error('Не задан подмассив url или неверное количество параметров');
                        }
                    }
                }

                if (isset($vars['get'])) $route .= '?' . http_build_query($vars['get']);

                $lang = self::addLang(($vars['lang'] ?? null));
                return Cfg::$get->website['prefix'] . '/' . $lang . (!empty($route) ? '/' : '') . $route;
            }

        }

        return '#';
    }

    public static function raw(string $url, string $lang = '')
    {
        $lang = (!empty($lang) ? $lang . '/' : '');
        $url = trim($url, '/');
        header('Location: ' . Cfg::$get->website['prefix'] . '/' . $lang . self::filterUrl($url));
        die();
    }

    public static function filterUrl(string $url)
    {
        return filter_var($url, FILTER_SANITIZE_ENCODED);
    }

}
