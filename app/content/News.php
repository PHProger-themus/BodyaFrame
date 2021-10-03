<?php

namespace app\content;

use system\core\Content;
use Cfg;

class News extends Content {
    
    public function init() {
        
        $ex = Cfg::$get->lang::DATA_FROM_DB;
        $this->render(['data' => $this->data, 'fdb' => $ex]);
        
    }
    
}
