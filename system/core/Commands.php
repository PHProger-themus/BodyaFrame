<?php

namespace system\core;

abstract class Commands
{

    protected function file($params)
    {
        $file = explode('/', $params[0]);
        $controller_class = "\\console\\controllers\\" . ucfirst($file[0]) . "Controller";
        $action = "$file[1]Action";
        $count = count($params);
        $vars = [];

        if (!class_exists($controller_class)) {
            echo $this->red("Контроллер \"$file[0]\" не найден.");
        } elseif (!method_exists($controller_class, $action)) {
            echo $this->red("Метод \"$file[1]\" не найден в контроллере \"$file[0]\".");
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
        $name = $params[1];
        $types = require_once(SYSTEM_DIR . "/config/templates.php");

        if (!array_key_exists($type, $types)) {
            echo $this->red("Неизвестная сущность \"$type\".");
        } else {
            list($path, $name_method, $code_callable) = $types[$type];
            $name = (!is_null($name_method) ? $this->$name_method($name) : $name) . ".php";
            if (!file_exists("$path/$name")) {
                $this->createFile($name, $path, $code_callable);
                echo $this->green("Файл создан.");
            } else {
                echo $this->green("Файл \"$name\" уже существует.");
            }
        }
    }

    private function createFile($name, $path, $code_callable)
    {
        $pathinfo = pathinfo($name);
        $short_dirname = $pathinfo['dirname']; // Относительный путь, для генерации пространства имен
        $full_dirname = "$path\\" . $pathinfo['dirname']; // Абсолютный путь, для создания файла и его изменения
        $filename = $pathinfo['filename']; // Имя файла без пути и расширения, для генерации имени класса
        $full_filename = "{$full_dirname}/{$filename}.php"; // Абсолютное имя файла, с путем, для его изменения после создания

        if (!file_exists($full_dirname)) {
            mkdir($full_dirname, 0777, true);
        }
        $code_callable($full_dirname, $filename, $short_dirname); // Вызван метод для создания файла из конфигурации system/config/templates.php

        $file_contents = file_get_contents($full_filename);
        if (!empty($file_contents)) {
            if ($short_dirname == '.') {
                $namespace = "";
            } else {
                $namespace = "\\" . str_replace('/', '\\', $short_dirname);
            }
            $keywords = [
                '*NAME*' => $filename,
                '*NAMESPACE*' => $namespace
            ];
            file_put_contents($full_filename, strtr($file_contents, $keywords));
        }
    }

    private function n_Controller($name)
    {
        return preg_replace_callback('/([a-z]+)$/', function($matches) {
            return ucfirst($matches[1]) . 'Controller';
        }, $name);
    }

    private function n_Capitalize($name)
    {
        return preg_replace_callback('/([a-z]+)$/', function($matches) {
            return ucfirst($matches[1]);
        }, $name);
    }

    private function n_Migration($name)
    {
        return preg_replace_callback('/([a-z_]+)$/', function($matches) {
            return "m_" . date('ymdHis') . "_$matches[1]";
        }, $name);
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
                echo $this->green("Миграция \"$migration\" была применена.") . "\n";
            } else {
                echo $this->red("Миграция \"$migration\" была отменена.") . "\n";
            }
        }
    }

}