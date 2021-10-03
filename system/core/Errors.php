<?php

namespace system\core;

use system\classes\ArrayHolder;

abstract class Errors
{

    const SUCCESS = 'success';
    const NOTICE = 'notice';
    const WARNING = 'warning';
    CONST ERROR = 'error';

    public static function error(string $message, array $extra)
    {
        //$extra['trace'] = debug_backtrace();
        $params = array_merge(['message' => $message], $extra);
        System::renderError('Error', $params);
        die();
    }

    public static function code($response_code): void
    {
        http_response_code($response_code);
        $view = new View();
        $view->render();
        die();
    }


}