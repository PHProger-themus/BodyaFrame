<?php

namespace system\core;

use system\classes\FilesHelper;

class Run
{
    // TODO: Создание файлов классов
    public function run()
    {

        global $argv;
        $method = $argv[1];
        $params = array_slice($argv, 2);

        if (method_exists($this, $method)) {
            $this->$method($params);
        } else {
            echo "\033[31mНесуществующая команда. php run help для помощи\033[0m\n";
        }


//        for ($i = 1; $i < count($arguments); $i++) {
//
//            switch ($arguments[$i]) {
//
//                case '-h':
//
//                    echo "\nПомощь по командам\n=====================\n\n\t-f\tвыполнить файл, ниже приведен список доступных:\n\n\t";
//                    $controllers_folder = 'console\controllers';
//                    $files = scandir($controllers_folder);
//                    $method_ending = 'Action';
//                    foreach ($files as $file) {
//                        if (preg_match('/[\w]+Controller\.php$/', $file)) {
//                            $class_name = '\\' . $controllers_folder . '\\' . substr($file, 0, -4);
//                            if (class_exists($class_name)) { // Файл может не содержать класса
//                                foreach (get_class_methods($class_name) as $method) {
//                                    if (preg_match('/[\w]+Action$/', $method))
//                                        echo lcfirst(preg_replace ('/Controller\.php$/', '', $file)) . '/' . substr($method, 0, -6) . "\n\t";
//                                }
//                            }
//                        }
//                    }
//                    echo "\n\t-h\tпомощь по консоли\n\n\t";
//
//            }
//
//        }

    }

    private function file($params)
    {
        $file = explode('/', $params[0]);
        $controller_class = '\\console\\controllers\\' . ucfirst($file[0]) . 'Controller';
        $action = $file[1] . 'Action';
        $count = count($params);
        $vars = [];

        if (!class_exists($controller_class)) {
            echo "Контроллер " . $file[0] . " не найден\n";
        } elseif (!method_exists($controller_class, $action)) {
            echo "Метод " . $file[1] . " не найден в контроллере " . $file[0] . "\n";
        } else {
            if ($count > 1) { // Если есть дополнительные параметры
                for ($i = 1; $i < $count; $i++) {
                    $split = explode('=', $params[$i]);
                    $vars[$split[0]] = $split[1];
                }
            }
            (new $controller_class())->$action($vars);
        }
    }

    private function create($params)
    {
        $type = $params[0];
        $brief_name = $params[1];

        $file = dirname(__DIR__) . '\\templates\\' . $type . '.fwtt';
        if (!file_exists($file)) {
            echo "Сущность для создания не определена";
        } else {
            switch ($type) {
                case 'controller';
                case 'cmdController' :
                    {
                        $name = ucfirst($brief_name) . 'Controller';
                    };
                    break;
                case 'contentController';
                case 'model';
                case 'cmdModel' :
                    {
                        $name = ucfirst($brief_name);
                    };
                    break;
                case 'view';
                case 'lang';
                case 'content' :
                    {
                        $name = $brief_name;
                    };
                    break;
                case 'rule' :
                    {
                        $name = ucfirst($brief_name) . 'Rule';
                    };
                    break;
            }
            $method = $type . 'Exists';
            if ($newfile = $this->$method($name)) {
                copy($file, $newfile);
                $file_contents = file_get_contents($newfile);
                $file_contents = str_replace('*NAME*', $name, $file_contents);
                file_put_contents($newfile, $file_contents);
                echo "Файл создан";
            } else {
                echo "Файл " . $name . " уже существует";
            }
        }
    }

    private function checkForFile($dir, $name) {
        $file = $dir . '\\' . $name . '.php';
        if (!file_exists($file)) {
            return $file;
        }
        return false;
    }

    private function controllerExists($name)
    {
        return $this->checkForFile(APP_DIR . '\\controllers', $name);
    }

    private function cmdControllerExists($name)
    {
        return $this->checkForFile(HOME_DIR . '\\console\\controllers', $name);
    }

    private function contentControllerExists($name)
    {
        return $this->checkForFile(APP_DIR . '\\content', $name);
    }

    private function modelExists($name)
    {
        return $this->checkForFile(APP_DIR . '\\models', $name);
    }

    private function cmdModelExists($name)
    {
        return $this->checkForFile(HOME_DIR . '\\console\\models', $name);
    }

    private function ruleExists($name)
    {
        return $this->checkForFile(APP_DIR . '\\user\\rules', $name);
    }

}
