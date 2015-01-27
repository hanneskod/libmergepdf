<?php

namespace iio\libmergepdf;

class PagesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider pageNumbersProvider
     */
    public function testPageNumbers($pages, array $result)
    {
        $p = new Pages($pages);
        $this->assertEquals($result, $p->getPages());
    }

    public function pageNumbersProvider()
    {
        return array(
            array('', array()),
            array('1', array(1)),
            array('1,2', array(1,2)),
            array('5-7', array(5,6,7)),
            array('7-5', array(7,6,5)),
            array('1,2-5,4,7-5', array(1,2,3,4,5,4,7,6,5)),
            array(' 1, 2- 5,, 4 ,7 -5,,', array(1,2,3,4,5,4,7,6,5))
        );
    }

    /**
     * @expectedException iio\libmergepdf\Exception
     */
    public function testInvalidString()
    {
        new Pages('12,*');
    }
}
