<?php


namespace system\core;


abstract class Lang
{

    private static array $lang = [];

    public static function init(string $filename)
    {
        $path_to_lang_file = APP_DIR . "/lang/" . Cfg::$get->lang . "/$filename.php";
        if (!file_exists($path_to_lang_file)) {
            $path_to_system = SYSTEM_DIR . "/lang/" . Cfg::$get->lang . "/$filename.php";
            if (!file_exists($path_to_system)) {
                throw new \Error("Не найден файл локализации " . $path_to_lang_file);
            } else {
                $page_lang = require_once($path_to_system);
            }
        } else {
            $page_lang = require_once($path_to_lang_file);
        }
        $wrapper_lang = require_once(APP_DIR . "/lang/" . Cfg::$get->lang . "/wrapper.php");
		self::$lang = array_merge($page_lang, $wrapper_lang);
    }

    public static function get(string $key)
    {
        return str_replace("\n", "<br><br>", self::$lang[$key]);
    }

}