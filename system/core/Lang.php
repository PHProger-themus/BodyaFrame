<?php


namespace system\core;


abstract class Lang
{

    private static array $lang = [];

    public static function init($controller, $action)
    {
        $path_to_lang_file = '../app/lang/' . Cfg::$get->lang . '/' . $controller . '/' . $action . '.php';

        if (!file_exists($path_to_lang_file)) {
            if (Cfg::$get->debug) {
                throw new \Error("Не найден файл локализации " . $path_to_lang_file);
            } else {
                Errors::code(404);
            }
        }

        $page_lang = require_once($path_to_lang_file);
		$wrapper_lang = require_once('../app/lang/' . Cfg::$get->lang . '/wrapper.php');
		self::$lang = array_merge($page_lang, $wrapper_lang);
    }

    public static function get(string $key)
    {
        return str_replace("\n", '<br><br>', self::$lang[$key]);
    }

}