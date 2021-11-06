<?php

namespace system\classes;

class HighlightHelper
{

    public static function highlight(array $words, string $text, $pattern = "<b class='highlight'>$0</b>") : string
    {
        $words = implode('|', $words);
        return preg_replace('/(' . $words . ')/ui', $pattern, $text);
    }

}