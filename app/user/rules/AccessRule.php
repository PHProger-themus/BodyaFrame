<?php

namespace app\user\rules;

use Cfg;
use View;

class AccessRule
{

    public function apply() {
        if (Cfg::$get->server->issetSession('userSigned')) {
            View::goToPage('/');
        }
    }

}