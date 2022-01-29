<?php

function debug(...$data)
{
    $info = "";
    foreach ($data as $item) {
        $info .= "<div style='padding:10px;color:#0000dc;background:#e8e8e8;margin:10px;border-radius:10px;z-index:200'><pre style='margin:0'>";
        $info .= print_r($item, true);
        $info .= "</pre></div>";
    }
    die($info);
}

function db_debug($data)
{
    $info = "<div style='padding:10px;color:#d00000;background:#e8e8e8;margin:10px;border-radius:10px;z-index:200'><pre style='margin:0'>";
    $info .= print_r($data, true);
    $info .= "</pre></div>";
    echo $info;
}

function cmd(...$data)
{
    foreach ($data as $item) {
        var_dump($item);
    }
    die();
}

function configInit()
{
    $app_config = require APP_DIR . '/config/config.php';
    $common_config = require HOME_DIR . '/config.php';
    Cfg::init($app_config, $common_config);
}

function displayErrors()
{
    ini_set('display_errors', true);
    error_reporting(E_ALL);
}

function bodyaframeInit()
{
    if (Cfg::$get->debug) {
        displayErrors();
    }
    if (Cfg::$get->useSessions) {
        ini_set('session.cookie_httponly', 1);
        session_name(Cfg::$get->safety['sessionCookieName'] ?: null);
        session_start();
    }
    set_error_handler(function ($errno, $errstr) {
        throw new Error($errstr);
    });
}