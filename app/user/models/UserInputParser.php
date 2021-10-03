<?php

namespace app\user\models;

use system\models\InputParser;

class UserInputParser extends InputParser
{

    public function isUrl($value, $input_name, &$errors)
    {
        if (!preg_match("/^((https|http|ftp):[\/]{2})*[a-zа-я\-_]+(\.[a-zа-я\-_]+)+([\/]+[a-zа-я\-_]*)*$/", $value) && !empty($value)) {
            $errors[] = $this->setErrorText($input_name, 'isUrl');
        }
    }

}