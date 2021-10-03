<?php

namespace system\core;

/**
 * @property array common
 */
class Console
{

    public function __construct($config) {
        foreach ($config as $key => $value) {
            $this->$key = $value;
        }
    }

}