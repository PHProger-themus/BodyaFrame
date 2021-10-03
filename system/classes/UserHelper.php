<?php

namespace system\classes;

use QueryBuilder;
use Cfg;
use system\interfaces\ModelInterface;
use View;

class UserHelper extends QueryBuilder
{

    public static function signupUser(ModelInterface $model, array $columns, string $table = null) {
        $insertData = [];
        foreach ($columns as $column) {
            if ($column == 'password') $model->data->post->$column = SafetyManager::encryptPassword($model->data->post->$column);
            $insertData[$column] = $model->data->post->$column;
        }
        if (is_null($table)) $table = $model->table();
        $model->insert($insertData, $table);
    }

    public static function signinUser(ModelInterface $model, array $columns, string $table = null) {
        $model->get($table);
        $started = false;
        foreach ($columns as $column) {
            if ($column != 'password') {
                if (!$started) {
                    $started = true;
                } else {
                    $model->add(QueryBuilder::DB_AND);
                }
                $model->where($column, '=', $model->data->post->$column);
            }
        }
        $rows = $model->execute(true);
        if (count($rows) == 0 || !SafetyManager::checkPassword($model->data->post->password, $rows[0]->password)) {
            return "Данная запись не найдена";
        } else {
            $userInfo = [];
            foreach ($rows[0] as $row => $value) {
                $userInfo[$row] = $value;
            }
            Cfg::$get->server->setSession(['userSigned' => true, 'userInfo' => $userInfo]);
        }
    }

    public static function getUserInfo() {
        $userSigned = Cfg::$get->server->issetSession('userSigned');
        $userInfo = ['userSigned' => $userSigned ? true : false];
        if ($userSigned) {
            $userInfo['userInfo'] = Cfg::$get->server->getSession('userInfo');
        }
        return ArrayHolder::new($userInfo);
    }

    public static function logout(string $redirectUrl) {
        if (Cfg::$get->server->issetSession('userSigned')) {
            Cfg::$get->server->unsetSession(['userSigned', 'userInfo']);
        }
        View::goToPage($redirectUrl);
    }

}