<?php

return [

    'db' => [
        'active' => true,
        'databases' => [
            'db' => [
                'host' => 'localhost',
                'username' => 'comfstud',
                'password' => 'Blurryface5656',
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

];