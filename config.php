<?php

return [

    'db' => [
        'active' => true,
        'databases' => [
            'db' => [
                'host' => 'localhost',
                'username' => 'root',
                'password' => 'root',
                'database' => 'mybase',
                'prefix' => 'fr_',
                'trusted_tables' => ['active', 'users', 'objects']
            ],
            'database' => [
                'host' => 'localhost',
                'username' => 'root',
                'password' => 'root',
                'database' => 'mybase',
                'prefix' => 'new_',
                'trusted_tables' => ['table']
            ],
        ],
        'useAttributes' => false,
    ],

    //Используется ли мультиязычность
    'multilang' => false,
    'lang' => 'ru',
    'langs' => [
        'ru' => 'рус',
        'en' => 'eng'
    ],
    'useFile' => true,

    //"debug" - отображение специальных ошибок на уровне фреймворка (не создан controller, action и тд.). "db_debug" - вывод выполненных SQL-запросов на текущей странице и ошибок SQL
    'debug' => true,
    'db_debug' => true,

];