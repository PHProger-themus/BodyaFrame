<?php

namespace system\core;

abstract class ConsoleController
{

    private $foreground_colors = [
        'red' => '0;31',
        'green' => '1;32',
        'yellow' => '1;33',
        'blue' => '1;34',
        'magenta' => '1;35',
        'cyan' => '1;36',
        'grey' => '1;30',
        'white' => '1;37',
        'black' => '0;30'
    ];

    private $background_colors = [
        'red' => '41',
        'green' => '42',
        'yellow' => '43',
        'blue' => '44',
        'magenta' => '45',
        'cyan' => '46',
        'grey' => '47',
        'black' => '40'
    ];

    protected function color(string $text, string $fore, string $back = 'black')
    {
        return "\033[{$this->foreground_colors[$fore]}m\033[{$this->background_colors[$back]}m$text\033[0m";
    }

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

}