<?php

namespace system\core;

/**
 * @property array common
 * @property bool debug
 * @property bool db_debug
 * @property bool multilang
 * @property string lang
 * @property array routes
 * @property string url
 * @property string device
 * @property array website
 * @property string cssFolder
 * @property string jsFolder
 * @property bool disableCache
 * @property array links
 * @property array safety
 * @property Page route
 * @property array langs
 * @property bool useFile
 * @property int code
 * @property array db
 * @property bool active
 * @property array allowedFor
 */
class App
{

    public function __construct($config) {
        foreach ($config as $key => $value) {
            $this->$key = $value;
        }
    }

}