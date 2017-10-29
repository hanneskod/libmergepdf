<?php

namespace iio\libmergepdf;

class PagesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider pageNumbersProvider
     */
    public function testPageNumbers($expressionString, array $expected)
    {
        $this->assertSame(
            $expected,
            iterator_to_array(new Pages($expressionString))
        );
    }

    public function pageNumbersProvider()
    {
        return [
            ['', []],
            ['1', [1]],
            ['1,2', [1,2]],
            ['5-7', [5,6,7]],
            ['7-5', [7,6,5]],
            ['1,2-5,4,7-5', [1,2,3,4,5,4,7,6,5]],
            [' 1, 2- 5,, 4 ,7 -5,,', [1,2,3,4,5,4,7,6,5]],
        ];
    }

    /**
     * @expectedException iio\libmergepdf\Exception
     */
    public function testInvalidString()
    {
        new Pages('12,*');
    }

    public function testIsIterabla()
    {
        $this->assertSame(
            [1, 2],
            iterator_to_array(new Pages('1, 2'))
        );
    }

    public function testHasPages()
    {
        $this->assertFalse((new Pages)->hasPages());
        $this->assertTrue((new Pages('1'))->hasPages());
    }
}
