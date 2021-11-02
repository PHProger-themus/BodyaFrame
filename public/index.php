<?php
try {
    //TODO: Type-hinting, interfaces for all functions, PHPDocs, Reformatting, add variables to loops
    require dirname(__DIR__) . '/system/_init.php';

    phpInit();

    session_name(Cfg::$get->safety['sessionCookieName']?:null);
    session_start();

    (new \system\core\Router())->run();

} catch (Error $e) {
    Errors::error($e->getMessage(), ['trace' => $e->getTrace(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
}

