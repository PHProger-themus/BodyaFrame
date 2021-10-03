<?php

namespace console\controllers;

use system\core\ConsoleController;

class FileController extends ConsoleController
{
    public function mAction()
    {
        echo "Do you want to " . $this->color('greenity', 'green');
    }
}
