<?php

namespace system\classes;

class TextParser {

    public static function parse(string $text, array $params) {
        foreach ($params as $original => $replace) {
            $text = str_replace("{{$original}}", $replace, $text);
        }
        return $text;
    }

}
