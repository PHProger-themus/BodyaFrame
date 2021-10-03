<?php

namespace app\controllers;

use app\models\Users;
use Cfg;
use system\core\Controller;

class MainController extends Controller
{

    public function indexAction()
    {

        if ($this->isPost()) {
            $usersModel = new Users(data: $this->post());
            $usersModel->getUsers();
        } else {
            $usersModel = new Users();
            $this->view->render();
        }
    }
    public function getPageAction($params)
    {
        $this->view->render();
    }

}
