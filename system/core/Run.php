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
        $types = require_once(SYSTEM_DIR . '/config/templates.php');

        $file = dirname(__DIR__) . '\\templates\\' . $type . '.fwtt';
        if (!array_key_exists($type, $types)) {
            echo "Неизвестная сущность";
        } else {
            $folder = $types[$type];
            $name = explode('/', $brief_name);
            $name = $name[count($name) - 1];

            switch ($type) {
                case 'controller';
                case 'consoleController' :
                    {
                        $name = preg_replace_callback('/([a-z]+)$/', function($matches) {
                            return ucfirst($matches[1]) . 'Controller';
                        }, $brief_name);
                    };
                    break;
                case 'contentController';
                case 'model';
                case 'consoleModel';
                case 'content' :
                    {
                        $name = preg_replace_callback('/([a-z]+)$/', function($matches) {
                            return ucfirst($matches[1]);
                        }, $brief_name);
                    };
                    break;
                case 'view';
                case 'lang';
                    {
                        $name = $brief_name;
                    };
                    break;
                case 'rule' :
                    {
                        $name = preg_replace_callback('/([a-z]+)$/', function($matches) {
                            return ucfirst($matches[1]) . 'Rule';
                        }, $brief_name);
                    };
                    break;
            }

            if ($new_file = $this->checkForFile($folder, $name)) {
                $this->createFile($file, $new_file, $name, $folder);
                echo "Файл создан";
            } else {
                echo "Файл " . $name . " уже существует";
            }
        }
    }

    private function createFile($file, $new_file, $name, $folder)
    {
        $path = pathinfo($name);
        if (!file_exists($path['dirname'])) {
            mkdir($folder . '/' . $path['dirname'], 0777, true);
        }
        copy($file, $new_file);

        $file_contents = file_get_contents($new_file);
        if (!empty($file_contents)) {
            $file_name = explode('/', $name);
            $file_name = $file_name[count($file_name) - 1];
            $namespace = str_replace('/', '\\', substr($name, 0, -1 * (strlen($file_name) + 1)));

            $file_contents = str_replace('*NAME*', $file_name, $file_contents);
            $file_contents = str_replace('*NAMESPACE*', (!empty($namespace) ? '\\' : '') . $namespace, $file_contents);
        }

        file_put_contents($new_file, $file_contents);
    }

    private function checkForFile($path, $name)
    {
        $name = str_replace('/', '\\', $name);
        $file = $path . '\\' . $name . '.php';
        if (!file_exists($file)) {
            return $file;
        }
        return false;
    }

}
