<?php

return [

    'controller' => [APP_DIR . '\\controllers', function ($name) {
        copy(SYSTEM_DIR . '/templates/controller.fwtt', $name);
    }],

    'consoleController' => [HOME_DIR . '\\console\\controllers', function ($name) {
        copy(SYSTEM_DIR . '/templates/consoleController.fwtt', $name);
    }],

    'content' => [APP_DIR . '\\content', function ($name, $file) {
        touch(APP_DIR . '/content/views/' . strtolower($file) . '.php');
        copy(SYSTEM_DIR . '/templates/contentController.fwtt', $name);
    }],

    'model' => [APP_DIR . '\\models', function ($name) {
        copy(SYSTEM_DIR . '/templates/model.fwtt', $name);
    }],

    'consoleModel' => [HOME_DIR . '\\console\\models', function ($name) {
        copy(SYSTEM_DIR . '/templates/consoleModel.fwtt', $name);
    }],

    'rule' => [APP_DIR . '\\user\\rules', function ($name) {
        copy(SYSTEM_DIR . '/templates/rule.fwtt', $name);
    }],

    'view' => [APP_DIR . '\\views', function ($name, $filename, $dirname) {
        touch($name);
        if (Cfg::$cmd->multilang) {
            $langs = Cfg::$cmd->langs;
            foreach ($langs as $key => $lang) {
                copy(SYSTEM_DIR . '/templates/lang.fwtt', APP_DIR . "/lang/$key/$dirname/$filename.php");
            }
        }
    }],

];