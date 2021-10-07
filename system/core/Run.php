<?php

namespace system\core;

class Run extends Console
{

    public function run()
    {
        global $argv;
        $method = $argv[1] ?? 'help';
        $params = array_slice($argv, 2);

        if (method_exists($this, $method)) {
            $this->$method($params);
        } else {
            echo $this->red("Несуществующая команда \"$method\".");
        }
    }

}
