<?php

namespace system\core;

abstract class Commands
{

    protected function help()
    {
        echo $this->green("HELP команд\n=====================\n");
        echo "php run...
        \nfile\tВыполнить файл. Список доступных:\n\t";
        $controllers_folder = 'console\controllers';
        $files = scandir($controllers_folder);
        foreach ($files as $file) {
            if (str_ends_with($file, 'Controller.php')) {
                $class_name = '\\' . $controllers_folder . '\\' . substr($file, 0, -4);
                if (class_exists($class_name)) { // Файл может не содержать класса
                    $this->getMethods($class_name, $file);
                }
            }
        }
        echo "\ncreate\tСоздать сущность. Список доступных:\n\t";
        $templates = require_once(SYSTEM_DIR . '/config/templates.php');
        foreach ($templates as $template => $params) {
            echo "$template\n\t";
        }
    }

    private function getMethods($class_name, $file)
    {
        foreach (get_class_methods($class_name) as $method) {
            if (str_ends_with($method, 'Action')) {
                echo lcfirst(str_replace('Controller.php', '', $file)) . '/' . str_replace('Action', '', $method) . "\n\t";
            }
        }
    }

    protected function file($params)
    {
        $file = explode('/', $params[0]);
        $controller_class = '\\console\\controllers\\' . ucfirst($file[0]) . 'Controller';
        $action = $file[1] . 'Action';
        $count = count($params);
        $vars = [];

        if (!class_exists($controller_class)) {
            echo $this->red("Контроллер " . $file[0] . " не найден");
        } elseif (!method_exists($controller_class, $action)) {
            echo $this->red("Метод " . $file[1] . " не найден в контроллере " . $file[0]);
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

    protected function create($params)
    {
        $type = $params[0];
        $brief_name = $params[1];
        $types = require_once(SYSTEM_DIR . '/config/templates.php');

        if (!array_key_exists($type, $types)) {
            echo $this->red("Неизвестная сущность");
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
                case 'migration' :
                    {
                        $name = preg_replace_callback('/([a-z_]+)$/', function ($matches) {
                            return "m_" . date('ymdHis') . "_$matches[1]";
                        }, $brief_name);
                    };
                    break;
            }

            $full_name = $file_info[0] . DIRECTORY_SEPARATOR . $name . '.php';

            if (!file_exists($full_name)) {
                $this->createFile($full_name, $name, $file_info);
                echo $this->green("Файл создан");
            } else {
                echo $this->green("Файл " . $name . " уже существует");
            }
        }
    }

    private function createFile($full_name, $name, $file_info)
    {
        $path = pathinfo($name);
        $dirname = ($path['dirname'] == '.' ? '' : $path['dirname']);
        $filename = $path['filename'];
        $full_path = $file_info[0] . '/' . $dirname; // Полный путь без имени файла, для проверки директории

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

    protected function migrate(array $params)
    {
        $this->applyMigrations($params, "up");
    }

    protected function rollback(array $params)
    {
        $this->applyMigrations($params, "down");
    }

    private function applyMigrations(array $params, string $method)
    {
        $migrations_folder = HOME_DIR . "/console/migrations";
        if (empty($params)) {
            $migrations = array_slice(scandir($migrations_folder), 2);
            $this->invokeMigrationsMethod($migrations, $method);
        } else {
            array_walk($params, function (&$migration_name) {
                $migration_name = "m_$migration_name.php";
            });
            $this->invokeMigrationsMethod($params, $method);
        }
    }

    private function invokeMigrationsMethod(array $migrations, string $method)
    {
        if ($method == "down") {
            rsort($migrations);
        }
        foreach ($migrations as $migration) {
            $class = "\\console\\migrations\\" . substr($migration, 0, -4);
            (new $class())->$method();

            if ($method == "up") {
                echo $this->green("Миграция $migration была применена") . "\n";
            } else {
                echo $this->red("Миграция $migration была отменена") . "\n";
            }
        }
    }

}