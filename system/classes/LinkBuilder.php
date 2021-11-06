<?php

namespace system\classes;

use system\core\Errors;
use Cfg;
use system\core\PHP;

class LinkBuilder
{

    public const QUERY_BEGIN = 0;
    public const QUERY_END = 1;
    public const QUERY_REPLACE = 1;
    public const QUERY_APPEND = 0;
    public const QUERY_REMOVE = 1;
    public const QUERY_CUT = 0;

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
        return $prefix . (!empty($path) ? '/' : '') . $path . (isset($vars['anchor']) ? '#' . $vars['anchor'] : '');

    }

    public static function raw(string $url, string $lang = '')
    {
        $lang = (!empty($lang) ? $lang : Cfg::$get->lang) . (!empty($url) ? '/' : '');
        $url = trim($url, '/');
        return Cfg::$get->website['prefix'] . '/' . $lang . $url;
    }

    public static function redirect(string $url, string $lang = '')
    {
        header("Location: " . self::raw($url, $lang));
        die();
    }

    public static function filterUrl(string $url)
    {
        return filter_var($url, FILTER_SANITIZE_ENCODED);
    }

    private static function completeQuery(array $get, string $url, int $position)
    {
        $built_query = http_build_query($get); // Собираем строку запроса из массива
        $query = parse_url($url, PHP_URL_QUERY); // Получаем строку запроса из браузера
        if ($query) { // Если строка запроса на странице присутствует, то нужно добавить к ней новую строку в нужное место
            if ($position) {
                $url .= "&$built_query";
            } else {
                $url = parse_url($url, PHP_URL_PATH) . "?$built_query&$query";
            }
        } else { // Иначе просто добавить к URL.
            $url .= "?$built_query";
        }
        return $url;
    }

    public static function addGet(array $get, int $position = self::QUERY_END, int $mode = self::QUERY_REPLACE)
    {
        if ($mode) { // Если режим добавления - замена
            $url = self::removeGet(array_keys($get)); // Убираем старый вариант параметра
            $url = self::completeQuery($get, $url, $position); // Добавляем новый в нужное место
        } else { // Если же режим добавления - дополнение
            $url = PHP::getServer('REQUEST_URI'); // Берем URL
            foreach($get as $key => $value) { // Перебираем массив с параметрами
                if (!preg_match("~[?&]$key=.*?(\+*$value)(\+|&|$){1}~", $url)) { // Если в адресной строке у нужного нам параметра нет такого значения
                    $regex = "~([?&]$key=[^&]*)~"; // То добавим его
                    if (preg_match($regex, $url)) { // Если параметр присутствует с другими значениями
                        $url = preg_replace($regex, "$0+$value", $url); // То добавим его через +
                    } else {
                        $url = self::completeQuery($get, $url, $position); // Иначе просто используем добавление в начало или в конец
                    }
                }
            }
        }

        return $url;
    }

    public static function removeGet(array $get, int $mode = self::QUERY_REMOVE)
    {
        $url = PHP::getServer('REQUEST_URI');
        if ($mode) { // Если режим - полностью убрать параметр
            foreach ($get as $key) {
                $url = preg_replace("~[&?]$key=[^&]*~", "", $url); // То просто заменяем его на пустоту
            }
            $url_without_query = Cfg::$get->url;
            $begin_query = $url[strlen($url_without_query)] ?? null;
            if ($begin_query == '&') { // Если после того, как параметр был удален, осталась строка запроса и в начале ее оказался &
                $url[strlen($url_without_query)] = '?'; // Заменим его на ?
            }
        } else { // Если режим - удалить значение из параметра
            foreach ($get as $key => $value) {
                $url = preg_replace_callback("~([?&]$key=.*?)(\+*$value)(\+|&|$){1}~", function ($matches) { // Убираем данное значение (схлопываем строки до него и после)
                    array_shift($matches); // Лишний первый элемент
                    return $matches[0] . $matches[2];
                }, $url);
                $url = str_replace('=+', '=', $url); // Если данный параметр был первым, то получится такая фигня, которую надо убрать
                $url = preg_replace_callback("~([?]*)(&*$key=)(&|$)~", function ($matches) { // Если данный параметр был единственным, останется имя параметра со знаком равно, убираем
                    array_shift($matches);
                    return $matches[0] . $matches[2];
                }, $url);
                $url = str_replace('?&', '?', $url); // Если параметр был полностью убран из начала запроса, то устраняем еще один недочет
                if (str_ends_with($url, '?')) { // А если это был единственный параметр, то уберем остаток от него
                    $url = substr($url, 0, -1);
                }
            }
        }
        return $url;
    }


}
