<?php

namespace system\tests\classes;

use PHPUnit\Framework\TestCase;
use system\classes\HighlightHelper;

/**
 * @covers HighlightHelper
 */
class HighlightHelperTest extends TestCase
{

    public function testHighlighted()
    {
        $expected = "Cuz I told you my <b class='highlight'>level</b> of <b class='highlight'>concern</b> but you walked by like you never heard";
        $line = "Cuz I told you my level of concern but you walked by like you never heard";
        $this->assertEquals($expected, HighlightHelper::highlight(['level', 'concern'], $line));
    }

    public function testHighlightedWithCustomPattern()
    {
        $expected = "Wish we could turn back <b class='custom'>time</b>, to the good old days, when our momma sang us to sleep, but now we're <b class='custom'>stressed out</b>!";
        $line = "Wish we could turn back time, to the good old days, when our momma sang us to sleep, but now we're stressed out!";
        $this->assertEquals($expected, HighlightHelper::highlight(['time', 'stressed out'], $line, "<b class='custom'>$0</b>"));
    }

}