<?php

namespace app\controllers;

use system\classes\SafetyManager;
use system\core\Controller;
use system\core\Errors;

class LogController extends Controller
{

    public function indexAction()
    {
        if ($this->isPost()) {
            SafetyManager::sendCSPReport();
        } else {
            Errors::code(404);
        }
    }

}
