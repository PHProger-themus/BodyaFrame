<?php
try {
    //TODO: Type-hinting, interfaces for all functions, PHPDocs, Reformatting, add variables to loops, if( -> if (
    require '../system/config/env.php';
    require HOME_DIR . '/system/autoload.php';

    $config = require HOME_DIR . '/app/config/config.php';
    $common_config = require '../config.php';
    Cfg::init($config, $common_config);
    phpInit();

    session_name(Cfg::$get->safety['sessionCookieName']?:null);
    session_start();

    (new \system\core\Router())->run();

} catch (Error $e) {
    Errors::error($e->getMessage(), ['trace' => $e->getTrace(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
}

