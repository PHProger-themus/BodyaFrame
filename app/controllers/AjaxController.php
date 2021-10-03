<?php

namespace app\controllers;

use app\models\ajax\Ajax;
use system\core\Controller;
use system\core\Errors;
use system\classes\ArrayHolder;

class AjaxController extends Controller
{
    public function indexAction($vars)
    {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
            $method = ($this->issetPost('func') ? $this->post('func') : ($this->issetGet('func') ? $this->get('func') : null));
            $method = 'ajax' . $method;

            unset($vars->url);

            $ajax = new Ajax();
            $ajax->$method($this->post());
        } else {
            Errors::code(404);
        }
    }
}
