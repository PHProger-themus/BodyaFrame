<?php

namespace system\models;

use system\core\Cfg;

class InputParser
{

    private array $fields = [];
    private array $custom_errors = [];
    private array $default_errors = [];

    public function __construct($model)
    {
        if (method_exists($model, 'fields')) {
            $this->fields = $model->fields();
        }
        $validation_file = APP_DIR . "/lang/" . Cfg::$get->lang . "/validation.php";
        if (file_exists($validation_file)) {
            $this->custom_errors = require($validation_file);
        }
        $this->default_errors = require(SYSTEM_DIR . "/lang/" . Cfg::$get->lang . "/validation.php");

    }

    protected function setErrorText($input_name, $rule_name, $params = [])
    {
        $custom_input_name = $this->fields[$input_name] ?? $input_name;
        $custom_error_text = $this->custom_errors[$input_name][$rule_name] ?? $this->custom_errors[$rule_name] ?? $this->default_errors[$rule_name] ?? $this->default_errors['null'];
        $custom_error_text = str_replace('*field*', ucfirst($custom_input_name), $custom_error_text);
        if (!empty($params)) {
            $custom_error_text = str_replace('*values*', ((is_string($params) || is_int($params)) ? $params : implode(', ', $params)), $custom_error_text);
            $custom_error_text = preg_replace_callback("/\*([0-9]+)\*/", function ($matches) use ($params) {
                return $params[$matches[1] - 1];
            }, $custom_error_text);
        }
        return $custom_error_text;
    }

    public function required($value, $input_name, &$errors)
    {
        if (empty($value)) {
            $errors[$input_name] = $this->setErrorText($input_name, __FUNCTION__);
        }
    }

    public function isEmail($value, $input_name, &$errors)
    {
        if (!empty($value) && !preg_match("/^[\w-]+([.][\w\-]+)*@[\w\-]+([.][\w\-]+)*$/", $value)) {
            $errors[$input_name] = $this->setErrorText($input_name, __FUNCTION__);
        }
    }

    public function isNumber($value, $input_name, &$errors)
    {
        if (!preg_match("/[0-9]+/", $value) && !empty($value)) {
            $errors[$input_name] = $this->setErrorText($input_name, __FUNCTION__);
        }
    }

    public function isFloat($value, $input_name, &$errors)
    {
        if (!preg_match("/^[\d]+\.[\d]+$/", $value) && !empty($value)) {
            $errors[$input_name] = $this->setErrorText($input_name, __FUNCTION__);
        }
    }

    public function isDate($value, $input_name, &$errors, $params)
    {
        if (empty($params) || !is_string($params)) {
            $errors[$input_name] = $this->default_errors['undefined'];
        } else {
            try {
                $formatted = new \DateTime($value);
                if ($formatted->format($params) !== $value && !empty($value)) {
                    throw new \Exception(true);
                }
            } catch (\Exception) { // Ошибки в записи формата даты класса DateTime отлавливаются только исключением
                $errors[$input_name] = $this->setErrorText($input_name, __FUNCTION__, $params);
            }
        }
    }

    public function range($value, $input_name, &$errors, $params)
    {
        if (empty($params) || !is_array($params) || count($params) > 2) {
            $errors[$input_name] = $this->default_errors['undefined'];
        } else {
            $length = mb_strlen($value);
            if (($length < $params[0] || $length > $params[1]) && !empty($value)) {
                $errors[$input_name] = $this->setErrorText($input_name, __FUNCTION__, $params);
            }
        }
    }

    public function rangeTo($value, $input_name, &$errors, $params)
    {
        if (!isset($params) || !is_int($params) || $params < 1) {
            $errors[$input_name] = $this->default_errors['undefined'];
        } else {
            if (mb_strlen($value) > $params && !empty($value)) {
                $errors[$input_name] = $this->setErrorText($input_name, __FUNCTION__, $params);
            }
        }
    }

    public function rangeFrom($value, $input_name, &$errors, $params)
    {
        if (!isset($params) || !is_int($params) || $params < 1) {
            $errors[$input_name] = $this->default_errors['undefined'];
        } else {
            if (mb_strlen($value) < $params && !empty($value)) {
                $errors[$input_name] = $this->setErrorText($input_name, __FUNCTION__, $params);
            }
        }
    }

    public function in($value, $input_name, &$errors, $params)
    {
        if (!isset($params) || !is_array($params) || count($params) == 0) {
            $errors[$input_name] = $this->default_errors['undefined'];
        } else if (!empty($value)) {
            if (array_search($value, $params) === false) {
                $errors[$input_name] = $this->setErrorText($input_name, __FUNCTION__, $params);
            }
        }
    }

    public function length($value, $input_name, &$errors, $params)
    {
        if (!isset($params) || !is_int($params) || $params < 1) {
            $errors[$input_name] = $this->default_errors['undefined'];
        } else if (mb_strlen($value) != $params && !empty($value)) {
            $errors[$input_name] = $this->setErrorText($input_name, __FUNCTION__, $params);;
        }
    }

    public function regex($value, $input_name, &$errors, $params)
    {
        if (!isset($params) || !is_string($params)) {
            $errors[$input_name] = $this->default_errors['undefined'];
        } else if (!preg_match("~^$params$~", $value) && !empty($value)) {
            $errors[$input_name] = $this->setErrorText($input_name, __FUNCTION__, $params);;
        }
    }

    private function isWrongArray($params)
    {
        return !is_array($params) || count($params) > 3 || !in_array('system\interfaces\QueryBuilderInterface', class_implements($params[0]));
    }

    public function unique($value, $input_name, &$errors, $params)
    {
        if (!isset($params) || $this->isWrongArray($params)) {
            $errors[$input_name] = $this->default_errors['undefined'];
        } else {
            $model = $params[0];
            $column = $params[2] ?? $input_name;

            $rows = $model->all([$column], ($params[1] ?? null))->where($column, $value)->rowsCount();
            if ($rows && !empty($value)) {
                $errors[$input_name] = $this->setErrorText($input_name, __FUNCTION__);
            }
        }
    }

    public function allowed($value, $input_name, &$errors, $params)
    {
        if (!isset($params) || !isset($value['type'])) {
            $errors[$input_name] = $this->default_errors['undefined'];
        } else {
            if (!in_array($value['type'], $params)) {
                $errors[$input_name] = $this->setErrorText($input_name, __FUNCTION__);
            }
        }
    }

    public function prohibited($value, $input_name, &$errors, $params)
    {
        if (!isset($params) || !isset($value['type'])) {
            $errors[$input_name] = $this->default_errors['undefined'];
        } else {
            if (in_array($value['type'], $params)) {
                $errors[$input_name] = $this->setErrorText($input_name, __FUNCTION__);
            }
        }
    }

    public function size($value, $input_name, &$errors, $params)
    {
        if (!isset($params) || !is_string($params) || !isset($value['size'])) {
            $errors[$input_name] = $this->default_errors['undefined'];
        } else {

            $props = explode(' ', $params);
            $sign = $props[0];
            $unit = $props[2];
            $size = $this->$unit($props[1]);
            $fileSize = $value['size'];
            $str = "$props[1] $props[2]";

            switch($sign) {
                case '>' : {
                    if ($fileSize < $size) {
                        $errors[$input_name] = $this->setErrorText($input_name, 'sizeMustBeLarger', $str);
                    }
                    break;
                }
                case '<' : {
                    if ($fileSize > $size) {
                        $errors[$input_name] = $this->setErrorText($input_name, 'sizeMustBeSmaller', $str);
                    }
                    break;
                }
                case '=' : {
                    if ($fileSize != $size) {
                        $errors[$input_name] = $this->setErrorText($input_name, 'sizeMustBeEqual', $str);
                    }
                }
            }

        }
    }

    private function bit(string $size)
    {
        return $size / 8;
    }

    private function B(string $size)
    {
        return $size;
    }

    private function KB(string $size)
    {
        return $size * 1024;
    }

    private function MB(string $size)
    {
        return $size * 1048576;
    }

    private function GB(string $size)
    {
        return $size * 1073741824;
    }

}
