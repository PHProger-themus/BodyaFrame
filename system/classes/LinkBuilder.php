<?php

namespace system\classes;

use system\core\Errors;
use Cfg;

class LinkBuilder
{

    public static function addPrefix(string $lang = null)
    {
        $prefix = Cfg::$get->website['prefix'];
        if (Cfg::$get->multilang) {
            if (!is_null($lang)) {
                return "$prefix/$lang";
            } else {
                return "$prefix/" . Cfg::$get->lang;
            }
        } else {
            return $prefix;
        }
    }

    public static function url(string $controller, string $action, array $vars = [])
    {
        $routes = Cfg::$get->routes;
        $path = '';

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
                            throw new \Error("Не задан подмассив url или неверное количество параметров");
                        }
                    }
                }

                if (isset($vars['get'])) $route .= '?' . http_build_query($vars['get']);
                $path = $route;

            }

        }

        $prefix = self::addPrefix(($vars['lang'] ?? null));
        return $prefix . (!empty($path) ? '/' : '') . $path;

    }

    public static function redirect(string $url, string $lang = null)
    {
        $url = trim($url, "/");
        header("Location: " . self::addPrefix($lang) . "/{$url}");
        die();
    }

    public static function filterUrl(string $url)
    {
        return filter_var($url, FILTER_SANITIZE_ENCODED);
    }

}
