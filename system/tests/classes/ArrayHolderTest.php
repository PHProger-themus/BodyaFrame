<?php

namespace system\tests\classes;

use PHPUnit\Framework\TestCase;
use system\classes\ArrayHolder;

class ArrayHolderTest extends TestCase
{

    /**
     * @group tests
     */
    public function testArrayHolderWasCreated()
    {
        $array = ['int' => 1, 'double' => 2.7, 'string' => 'Hello World!'];
        $holder = ArrayHolder::new($array);
        $this->assertInstanceOf(ArrayHolder::class, $holder);
    }

}