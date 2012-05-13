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
 * Test class that create unvalid temporary file names
 * @package libmergepdf
 * @subpackage Tests
 */
class MergerUnvalidTempName extends Merger
{

    protected function getTempFname()
    {
        return __DIR__ . 'nonexisting' . DIRECTORY_SEPARATOR . 'filename';
    }

}


/**
 * Test the Merger class
 * @package libmergepdf
 * @subpackage Tests
 */
class MergerTest extends PHPUnit_Framework_TestCase
{

    /**
     * @expectedException itbz\libmergepdf\Exception
     */
    function testUnableToCreateTempFileError()
    {
        $m = new MergerUnvalidTempName();
        $m->addRaw('');
    }


    /**
     * @expectedException itbz\libmergepdf\Exception
     */
    function testUnvalicFileNameError()
    {
        $m = new Merger();
        $m->addFromFile(__DIR__ . '/nonexistingfile');
    }


    /**
     * @expectedException itbz\libmergepdf\Exception
     */
    function testNoPdfsAddedError()
    {
        $m = new Merger();
        $m->merge();
    }


    /**
     * @expectedException itbz\libmergepdf\Exception
     */
    function testInvalidPageError()
    {
        $m = new Merger();
        $m->addFromFile(__DIR__ . "/../data/A.pdf", new Pages('2'));
        $m->merge();
    }


    /**
     * From files data/A.pdf and data/B.pdf this should create
     * data/AAB.pdf and data/BAA.pdf
     */
    function testMerge()
    {
        $m = new Merger();
        $a = file_get_contents(__DIR__ . "/../data/A.pdf");
        $m->addRaw($a);
        $m->addRaw($a);
        $m->addFromFile(__DIR__ . "/../data/B.pdf");
        $aab = $m->merge();
        file_put_contents(__DIR__ . "/../data/AAB.pdf", $aab);
        
        $m->addRaw($aab, new Pages('3-1'));
        file_put_contents(__DIR__ . "/../data/BAA.pdf", $m->merge());
    }

}
