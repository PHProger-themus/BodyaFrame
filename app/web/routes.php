<?php

return [
    '' => [
        'controller' => 'main',
        'action' => 'index',
        'breadcrumb' => 'Главная в файле',
    ],
    'page/{num}' => [
        'controller' => 'main',
        'action' => 'getPage',
        'breadcrumb' => 'Страница №{num}'
    ],
    'log_report' => [
        'controller' => 'log',
        'action' => 'index',
        'breadcrumb' => 'Страница'
    ],
];
