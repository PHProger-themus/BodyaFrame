<?php

return [

    'controller' => [APP_DIR . '\\controllers', 'n_Controller', function ($full_dirname, $filename) {
        copy(SYSTEM_DIR . '/templates/controller.fwtt', "{$full_dirname}/{$filename}.php");
    }],

    'consoleController' => [HOME_DIR . '\\console\\controllers', 'n_Controller', function ($full_dirname, $filename) {
        copy(SYSTEM_DIR . '/templates/consoleController.fwtt', "{$full_dirname}/{$filename}.php");
    }],

    'content' => [APP_DIR . '\\content', 'n_Capitalize', function ($full_dirname, $filename) {
        touch(APP_DIR . "/content/views/{$filename}.php");
        copy(SYSTEM_DIR . '/templates/contentController.fwtt', "{$full_dirname}/{$filename}.php");
    }],

    'model' => [APP_DIR . '\\models', 'n_Capitalize', function ($full_dirname, $filename) {
        copy(SYSTEM_DIR . '/templates/model.fwtt', "{$full_dirname}/{$filename}.php");
    }],

    'consoleModel' => [HOME_DIR . '\\console\\models', 'n_Capitalize', function ($full_dirname, $filename) {
        copy(SYSTEM_DIR . '/templates/consoleModel.fwtt', "{$full_dirname}/{$filename}.php");
    }],

    'rule' => [APP_DIR . '\\user\\rules', null, function ($full_dirname, $filename) {
        copy(SYSTEM_DIR . '/templates/rule.fwtt', "{$full_dirname}/{$filename}.php");
    }],

    'view' => [APP_DIR . '\\views', null, function ($full_dirname, $filename, $short_dirname) {
        touch("{$full_dirname}/{$filename}.php");
        if (Cfg::$get->multilang) {
            $langs = Cfg::$get->langs;
            foreach ($langs as $key => $lang) {
                $lang_dir = APP_DIR . "/lang/$key/$short_dirname";
                if (!file_exists($lang_dir)) {
                    mkdir($lang_dir, 0777, true);
                }
                copy(SYSTEM_DIR . '/templates/lang.fwtt', "$lang_dir/$filename.php");
            }
        }
    }],

    'migration' => [HOME_DIR . '\\console\\migrations', 'n_Migration', function ($full_dirname, $filename) {
        copy(SYSTEM_DIR . '/templates/migration.fwtt', "{$full_dirname}/{$filename}.php");
    }]

];