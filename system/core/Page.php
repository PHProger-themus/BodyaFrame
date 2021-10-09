<?php

namespace system\core;

class Page
{

    private string $title;
    private string $description;
    private string $keywords;

    public function __construct(
        private string|bool $controller = false,
        private string|bool $action = false,
        private string|bool $breadcrumb = false,
        private string|bool $path = false
    ) {}

    public function init(array $meta_array) {
        $this->title = $meta_array['title'];
        $this->description = ($meta_array['description'] ?? '');
        $this->keywords = (isset($meta_array['keywords']) ? mb_strtolower($meta_array['keywords']) : '');
    }

    public function getController() {
        return $this->controller;
    }

    public function getAction() {
        return $this->action;
    }

    public function getBreadcrumb() {
        return $this->breadcrumb;
    }

    public function getPath() {
        return $this->path;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getKeywords() {
        return $this->keywords;
    }

}