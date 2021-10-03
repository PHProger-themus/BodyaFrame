<?php
try {
    //TODO: Type-hinting, interfaces for all functions, PHPDocs, Reformatting, add variables to loops
    require '../system/config/env.php';
    require HOME_DIR . '/system/autoload.php';

    $config = require HOME_DIR . '/app/config/config.php';
    $common_config = require '../config.php';
    Cfg::appInit($config, $common_config);
    phpInit();

    if (isset(Cfg::$get->safety['sessionCookieName'])) {
        session_name(Cfg::$get->safety['sessionCookieName']);
    }
    session_start();

    if (Cfg::$get->debug) {
        ini_set('display_errors', true);
        error_reporting(E_ALL);
    }

    (new \system\core\Router())->run();
} catch (Error $e) {
    Errors::error($e->getMessage(), ['trace' => $e->getTrace(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
}

