<?php

namespace system\interfaces;

interface ViewInterface {

    /**
     * Производит рендер страницы view из папки views. Метод вызывается в контроллере, если требуется вывести html-страницу на экран.
     * @param array $vars Ассоциативный массив, ключи которого - название переменной, значения - значения.   
    */
    public function render($vars = array()) : void;

//    /**
//     * Производит редирект на страницу.
//     * @param string $url URL.
//    */
//    public static function goToPage($url) : void;
    
}
