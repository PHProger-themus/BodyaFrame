<?php
try {
    //TODO: Type-hinting, interfaces for all functions, PHPDocs, Reformatting, add variables to loops
    require dirname(__DIR__) . '/system/_init.php';
    bodyaframeInit();
    (new \system\core\Router())->run();
} catch (Error $e) {
    Errors::error($e->getMessage(), ['trace' => $e->getTrace(), 'file' => $e->getFile(), 'line' => $e->getLine()]); //TODO: Make this nice
}

