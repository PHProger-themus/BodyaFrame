<?php

namespace system\core;

use system\classes\FilesHelper;
use function PHPUnit\Framework\directoryExists;

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

        if (!array_key_exists($type, $types)) {
            echo "Неизвестная сущность";
        } else {
            $file_info = $types[$type];

            switch ($type) {
                case 'controller';
                case 'consoleController' :
                    {
                        $name = preg_replace_callback('/([a-z]+)$/', function($matches) {
                            return ucfirst($matches[1]) . 'Controller';
                        }, $brief_name);
                    };
                    break;
                case 'content';
                case 'model';
                case 'consoleModel' :
                    {
                        $name = preg_replace_callback('/([a-z]+)$/', function($matches) {
                            return ucfirst($matches[1]);
                        }, $brief_name);
                    };
                    break;
                case 'view' :
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

            $full_name = $file_info[0] . DIRECTORY_SEPARATOR . $name . '.php';

            if (!file_exists($full_name)) {
                $this->createFile($full_name, $name, $file_info);
                echo "Файл создан";
            } else {
                echo "Файл " . $name . " уже существует";
            }
        }
    }

    private function createFile($full_name, $name, $file_info)
    {
        $path = pathinfo($name);
        $dirname = ($path['dirname'] == '.' ? '' : $path['dirname']);
        $filename = $path['filename'];
        $full_path = $file_info[0] . '/' . $dirname; // Полный путь без имени файла

        if (!file_exists($full_path)) {
            mkdir($full_path, 0777, true);
        }
        $file_info[1]($full_name, $filename, $dirname);

        $file_contents = file_get_contents($full_name);
        if (!empty($file_contents)) {
            $namespace = str_replace('/', '\\', $dirname);
            $keywords = [
                '*NAME*' => $filename,
                '*NAMESPACE*' => (!empty($namespace) ? '\\' : '') . $namespace
            ];
            file_put_contents($full_name, strtr($file_contents, $keywords));
        }
    }

}
