<?php

namespace system\core;

abstract class Errors
{

    const SUCCESS = 'success';
    const NOTICE = 'notice';
    const WARNING = 'warning';
    CONST ERROR = 'error';

    public static function error(string $message, array $extra)
    {
        $params = array_merge(['message' => $message], $extra);
        if (Cfg::$get->debug) {
            System::renderError('Error', $params);
        } else {
            self::code(404);
        }
        die();
    }

    public static function code($response_code): void
    {
        http_response_code($response_code);
        self::renderPage($response_code);
    }

    public static function renderPage(string $page)
    {
        Lang::init("error/$page");
        $view = new View(0, $page);
        $view->render();
        die();
    }

}