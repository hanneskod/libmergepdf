<?php
namespace itbz\libmergepdf;

use fpdi\FPDI;

class MergerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException itbz\libmergepdf\Exception
     */
    public function testUnableToCreateTempFileError()
    {
        $m = $this->getMock(
            '\itbz\libmergepdf\Merger',
            array('getTempFname'),
            array(new FPDI)
        );

        $m->expects($this->once())
            ->method('getTempFname')
            ->will(
                $this->returnValue(
                    __DIR__ . 'nonexisting' . DIRECTORY_SEPARATOR . 'filename'
                )
            );

        $m->addRaw('');
    }

    /**
     * @expectedException itbz\libmergepdf\Exception
     */
    public function testUnvalicFileNameError()
    {
        $m = new Merger(new FPDI);
        $m->addFromFile(__DIR__ . '/nonexistingfile');
    }

    /**
     * @expectedException itbz\libmergepdf\Exception
     */
    public function testNoPdfsAddedError()
    {
        $m = new Merger(new FPDI);
        $m->merge();
    }

    /**
     * @expectedException itbz\libmergepdf\Exception
     */
    public function testInvalidPageError()
    {
        $m = new Merger(new FPDI);
        $m->addFromFile(__DIR__ . "/../data/A.pdf", new Pages('2'));
        $m->merge();
    }

    /**
     * From files data/A.pdf and data/B.pdf this should create
     * data/AAB.pdf and data/BAA.pdf
     */
    public function testMerge()
    {
        $m = new Merger(new FPDI);
        $a = file_get_contents(__DIR__ . "/data/A.pdf");
        $m->addRaw($a);
        $m->addRaw($a);
        $m->addFromFile(__DIR__ . "/data/B.pdf");
        $aab = $m->merge();
        file_put_contents(__DIR__ . "/data/AAB.pdf", $aab);

        $m->addRaw($aab, new Pages('3-1'));
        file_put_contents(__DIR__ . "/data/BAA.pdf", $m->merge());
    }

    /**
     * @expectedException itbz\libmergepdf\Exception
     */
    public function testFpdiException()
    {
        $fpdi = $this->getMock(
            '\fpdi\FPDI',
            array('setSourceFile')
        );

        $fpdi->expects($this->once())
            ->method('setSourceFile')
            ->will($this->throwException(new \RuntimeException));

        $m = new Merger($fpdi);
        $a = file_get_contents(__DIR__ . "/data/A.pdf");
        $m->addRaw($a);
        $m->merge();
    }
}
