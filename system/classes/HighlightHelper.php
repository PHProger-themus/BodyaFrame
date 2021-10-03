<?php

namespace system\classes;

class HighlightHelper
{

    public static function highlight(array $words, string $text, $pattern = null) {

        if (is_null($pattern)) {
            $pattern = "<b class='highlight'>\\1</b>";
        }

        $words = implode('|', $words);
        $text = preg_replace('/(' . $words . ')/ui', $pattern, $text);

        return $text;

    }

}