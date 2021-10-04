<?php

namespace system\core;

abstract class Cfg {

    /**
     * @var App
     */
    public static $get;

    public static function init($cfg_data, $common_cfg_data)
    {
        self::$get = new App(array_merge($cfg_data, $common_cfg_data));
    }

}
