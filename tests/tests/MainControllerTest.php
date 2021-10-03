<?php

namespace tests;

use PHPUnit\Framework\TestCase;
use app\controllers\MainController;

class MainControllerTest extends TestCase
{

    public function testTestMethod()
    {
        $a = 10;
        $this->assertEquals(1024, (new MainController())->testMethod($a));
    }

}