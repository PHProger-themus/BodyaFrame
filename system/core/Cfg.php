<?php

namespace system\core;

abstract class Cfg {

    /**
     * @var App
     */
    public static $get;

    /**
     * @var Console
     */
    public static $cmd;

    public static function appInit($cfg_data, $common_cfg_data)
    {
        self::$get = new App(array_merge($cfg_data, $common_cfg_data));
    }

    public static function consoleInit($cfg_data, $common_cfg_data)
    {
        self::$cmd = new Console(array_merge($cfg_data, $common_cfg_data));
    }

}
