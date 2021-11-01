<?php
try {
    //TODO: Type-hinting, interfaces for all functions, PHPDocs, Reformatting, add variables to loops
    require '../system/config/env.php';
    require SYSTEM_DIR . '/autoload.php';

    $config = require APP_DIR . '/config/config.php';
    $common_config = require HOME_DIR . '/config.php';
    Cfg::init($config, $common_config);
    phpInit();

    session_name(Cfg::$get->safety['sessionCookieName']?:null);
    session_start();

    (new \system\core\Router())->run();

} catch (Error $e) {
    Errors::error($e->getMessage(), ['trace' => $e->getTrace(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
}

