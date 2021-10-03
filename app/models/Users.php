<?php

namespace app\models;

use QueryBuilder;
use app\attributes\ToDatabase;
use system\classes\SafetyManager;
use Cfg;

class Users extends QueryBuilder
{

//    public function rules()
//    {
//        return [
//            'login' => ['required', 'regex' => ['values' => '[0-9A-Z]+', 'if' => function() {
//                return Cfg::$get->debug;
//            }]],
//            'lastname' => ['length' => ['values' => 4]]
//        ];
//    }

    public function table() {
        return 'users';
    }

    //#[ToDatabase('db')] // New way to assign db to function
    public function getUsers() {
        if ($this->correct()) {
            return $this
                ->all(['login'])
                ->getRows();
        } else {
            debug($this->getErrors());
        }
    }

    //#[ToDatabase('database')]
    public function getUser()
    {
        return $this->all()->getRows();
    }

    public function sendData()
    {
        $this->insert(['login' => $this->form->name, 'password' => $this->form->password]);
    }

    public function upload()
    {
        if ($this->correct()) {
            $this->uploadFile($_FILES['name'], HOME_DIR . '/files/', function () {
                return SafetyManager::generateRandomString(5);
            });
        } else debug($this->getErrors());
    }

}