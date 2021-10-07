<?php

namespace system\core;

abstract class Console extends Commands
{

    protected function red(string $text)
    {
        return "\033[31m$text\033[0m";
    }

    protected function green(string $text)
    {
        return "\033[92m$text\033[0m";
    }

    protected function yellow(string $text)
    {
        return "\033[93m$text\033[0m";
    }

    protected function blue(string $text)
    {
        return "\033[94m$text\033[0m";
    }

    protected function magenta(string $text)
    {
        return "\033[95m$text\033[0m";
    }

    protected function cyan(string $text)
    {
        return "\033[96m$text\033[0m";
    }

    protected function gray(string $text)
    {
        return "\033[90m$text\033[0m";
    }

    protected function execute(string $command)
    {
        $commands = explode(' ', $command);
        $method = $commands[1];
        if (method_exists($this, $method)) {
            $this->$method(array_slice($commands, 2));
        } else {
            echo $this->red("Несуществующая команда \"$method\".");
        }
    }

}