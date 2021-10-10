<?php

namespace app\user\rules;

use Cfg;
use system\classes\LinkBuilder;
use View;
use system\classes\Server;

class AccessRule
{

    public function apply() {
        if (Server::issetSession('userSigned')) {
            LinkBuilder::redirect('/');
        }
    }

}