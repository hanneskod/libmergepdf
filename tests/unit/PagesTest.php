<?php
/**
 * This file is part of the libmergepdf package
 *
 * Copyright (c) 2012 Hannes Forsgård
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package libmergepdf
 * @subpackage Tests
 */
namespace itbz\libmergepdf;
use PHPUnit_Framework_TestCase;


/**
 * Test the Pages class
 * @package libmergepdf
 * @subpackage Tests
 */
class PagesTest extends PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider pageNumbersProvider
     */
    function testPageNumbers($pages, array $result)
    {
        $p = new Pages($pages);
        $this->assertEquals($result, $p->getPages());
    }


    function pageNumbersProvider()
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
     * @expectedException itbz\libmergepdf\Exception
     */
    function testInvalidString()
    {
        $p = new Pages('12,*');
    }
    
}
