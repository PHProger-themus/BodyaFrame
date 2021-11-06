<?php

namespace system\core;

class PHP
{

    private static $stubs = [
        'REMOTE_ADDR' => '127.0.0.1',
        'REQUEST_URI' => '/ru/testing/page?id=1&name=BodyaFrame&token=jGilb5^hj4',
        'HTTP_USER_AGENT' => 'Mozilla/4.5 [en] (X11; U; Linux 2.2.9 i586)'
    ];

    public static function getServer(string $key) : mixed
    {
        $key = strtoupper($key);
        if (defined('TESTING')) {
            return self::$stubs[$key] ?? null;
        } else {
            return $_SERVER[$key];
        }
    }

    public static function setServer(string $key, mixed $value) : void
    {
        $key = strtoupper($key);
        if (defined('TESTING')) {
            self::$stubs[$key] = $value;
        }
    }

}