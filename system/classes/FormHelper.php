<?php

namespace system\classes;

use Cfg;
use system\classes\SafetyManager;

class FormHelper
{

    private static string $form = "";
    private static string $CSRFToken = "";

    private static function generateCsrfToken(): string
    {
        return SafetyManager::generateRandomString(25);
    }

    public static function createForm(string $form_name)
    {
        if (Cfg::$get->safety['csrfProtection']) {
            if (self::$CSRFToken == "") {
                $token = self::generateCsrfToken();
                self::$CSRFToken = $token;
                Server::setSession(['csrfToken' => $token]);
            }
            self::$form = "<input type='hidden' name='_csrfToken' value='" . self::$CSRFToken . "' />" . self::$form;
        }

        $enctype = str_contains(self::$form, "input type='file'") ? " enctype='multipart/form-data'" : '';
        $formCreated = "<form method='POST'$enctype><input type='hidden' name='_formName' value='$form_name' />" . self::$form . "</form>";
        echo self::$CSRFToken . "\n";
        echo $formCreated;
        self::$form = '';
    }

    public static function addInput($type, $name, $attributes = [])
    {
        self::$form .= "<input type='$type' name='$name'" . self::processAttributes($attributes) . " />";
    }

    public static function addSubmit($name, $value, $attributes = [])
    {
        self::$form .= "<input type='submit' name='$name' value='$value'" . self::processAttributes($attributes) . " />";
    }

    private static function processAttributes($attributes)
    {
        $attributes_str = "";
        foreach ($attributes as $attr => $value) {
            $attributes_str .= " $attr='$value'";
        }
        return $attributes_str;
    }

    public static function getValue($input_name, $form_name, $initial_value = null)
    {
        $session_key = $form_name . '_' . $input_name;
        if (Server::issetSession($session_key)) {
            return Server::extractSession($session_key);
        }
        return $initial_value;
    }

}
