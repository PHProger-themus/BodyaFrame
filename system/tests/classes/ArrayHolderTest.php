<?php

namespace system\tests\classes;

use PHPUnit\Framework\TestCase;
use system\classes\ArrayHolder;

/**
 * @covers ArrayHolder
 */
class ArrayHolderTest extends TestCase
{

    public function testEmptyArrayWasPassed()
    {
        $holder = ArrayHolder::new([]);
        $this->assertNull($holder);
    }

    public function testArrayHolderWasCreated()
    {
        $array = ['int' => 1, 'double' => 2.7, 'string' => 'Hello World!'];
        $holder = ArrayHolder::new($array);
        $this->assertInstanceOf(ArrayHolder::class, $holder);
        $this->assertEquals($holder->int, $array['int']);
        $this->assertEquals($holder->string, $array['string']);
        $arrayHolder = (array)$holder;
        $this->assertCount(3, $arrayHolder);
        return $holder;
    }

    /**
     * @depends testArrayHolderWasCreated
     */
    public function testArrayWasCreatedFromArrayHolderInstance(ArrayHolder $holder)
    {
        $array = (array)$holder;
        $this->assertIsArray($array);
        $this->assertEquals($array['double'], $holder->double);
    }

}