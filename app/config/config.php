<?php

require_once('functions.php');
$common_config = require '../config.php';

return [

    //Общий конфиг из корня сайта
    'common' => $common_config,

    //"debug" - отображение специальных ошибок на уровне фреймворка (не создан controller, action и тд.). "db_debug" - вывод выполненных SQL-запросов на текущей странице и ошибок SQL
    'debug' => true,
    'db_debug' => true,

    //Используется ли мультиязычность
    'multilang' => false,
    'lang' => 'ru',
    'langs' => [
        'ru' => 'рус',
        'en' => 'eng'
    ],
    'useFile' => true,

    //Все routes приложения
    'routes' => require_once('../app/web/routes.php'),
    
    //Текущий URL страницы без GET-параметров
    'url' => explode('?', $_SERVER['REQUEST_URI'], 2)[0],
    // TODO: Remove Server
    //Объект для работы с серверными данными
    'server' => new system\models\Server(),

    //С какого устройства была загружена страница ( mobile / desktop )
    'device' => (preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"])) ? 'mobile' : 'desktop',

    //Массив данных вебсайта
    'website' => [
        'prefix' => '', //Обязательное условие - слеш в начале, если не пусто
        'root' => $_SERVER['DOCUMENT_ROOT'],
        'img' => '/app/files/images',
    ],
	
	//CSS и JS файлы, их местоположение, кеширование
	'cssFolder' => '/app/files/css',
	'jsFolder' => '/app/files/js',
	'disableCache' => true,
	'links' => [
		'css' => [
			'system.css' => ['main/index', 'page/page'],
            'errors.css' => ['error']
		],
		'js' => [
			'common.js',
		],
	],
    'errorsCss' => APP_DIR . '/files/css/errors.css',

    //Массив данных, используемых в функциях по обеспечению безопасности передаваемых данных
    'safety' => [
        'beginSalt' => 'dk85nx312mh9bs4dsj5',
        'endSalt' => 'fhgs0567k32c9s8vfnj6',
        'csrfProtection' => false,
        'xFrameOptions' => 'DENY', // DENY или SAMEORIGIN
        'sessionCookieName' => 's_token'
    ],

	/*
    'email' => [
        'init' => new system\models\Email(),
        'from' => 'websiteadmin@comfstud.ru',
        'type' => 'text/html',
        'charset' => 'utf-8',
    ]*/
    
];
