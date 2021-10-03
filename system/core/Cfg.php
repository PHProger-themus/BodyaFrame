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

    public static function appInit($cfg_data)
    {
        self::$get = new App($cfg_data);
    }

    public static function consoleInit($cfg_data)
    {
        self::$cmd = new Console($cfg_data);
    }

}
