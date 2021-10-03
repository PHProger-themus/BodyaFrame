<?php

namespace console\controllers;

use system\core\Cfg;

class HelloWorldController
{

    public function runAction($vars)
    {
        cmd($vars);
    }

    public function makeAction()
    {
        echo "Hey";
    }
}
