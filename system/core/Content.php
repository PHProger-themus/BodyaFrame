<?php

namespace system\core;

abstract class Content {
    
    public static function output($content_file_name_5936442, $vars = array()) {
        
        $c_name = "\\app\\content\\" . ucfirst($content_file_name_5936442);
        $content = new $c_name;
        $content->cname6865943 = $content_file_name_5936442;
        foreach ($vars as $var => $val) {
            $content->$var = $val;
        }
        $content->init();
        
    }
    
    public function render($vars = array()) {
        
        extract($vars);
        require APP_DIR . "/content/views/" . $this->cname6865943 . ".php";
        
    }
    
}
