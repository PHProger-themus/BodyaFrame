<?php

namespace system\classes;

class ArrayHolder
{

    public static function new(array $array)
    {
        if (!empty($array)) {
            $array_object = new ArrayHolder();
            foreach ($array as $key => $data) {
                $array_object->$key = $data;
            }
            return $array_object;
        }
    }

    public static function old(ArrayHolder $holder)
    {
        return (array)$holder;
    }

}
