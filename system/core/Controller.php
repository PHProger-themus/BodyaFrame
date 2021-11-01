<?php

namespace system\core;

use system\classes\ArrayHolder;

abstract class Controller
{

    protected View $view;
    private ?ArrayHolder $get = null;
    private ?ArrayHolder $post = null;

    public function __construct()
    {
        $this->view = new View();
        if (!empty($_GET)) {
            $this->get = ArrayHolder::new($_GET);
        }
        if (!empty($_POST)) {
            $this->post = ArrayHolder::new($_POST);
        }
    }

    /**
     * Использован ли метод POST для запроса страницы
     * @return bool Да / Нет.
     */
    protected function isPost(): bool {
        return $_SERVER['REQUEST_METHOD'] == 'POST';
    }

    /**
     * Использован ли метод GET для запроса страницы
     * @return bool Да / Нет.
     */
    protected function isGet(): bool {
        return $_SERVER['REQUEST_METHOD'] == 'GET';
    }

    protected function get($key = null)
    {
        if (is_null($key)) {
            return $this->view->processValue($this->get);
        }
        return $this->view->processValue($this->get->$key);
    }

    protected function post($key = null)
    {
        if (is_null($key)) {
            return $this->view->processValue($this->post);
        }
        return $this->view->processValue($this->post->$key);
    }

    protected function issetPost($key) {
        return isset($this->post->$key);
    }

    protected function issetGet($key) {
        return isset($this->get->$key);
    }

}