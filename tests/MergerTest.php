<?php
namespace iio\libmergepdf;

class MergerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException iio\libmergepdf\Exception
     */
    public function testUnableToCreateTempFileError()
    {
        $m = $this->getMock(
            '\iio\libmergepdf\Merger',
            array('getTempFname')
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
     * @expectedException iio\libmergepdf\Exception
     */
    public function testUnvalidFileNameError()
    {
        $m = new Merger();
        $m->addFromFile(__DIR__ . '/nonexistingfile');
    }

    /**
     * @expectedException iio\libmergepdf\Exception
     */
    public function testNoPdfsAddedError()
    {
        $m = new Merger();
        $m->merge();
    }

    public function testMerge()
    {
        $fpdi = $this->getMock(
            '\fpdi\FPDI',
            array(
                'setSourceFile',
                'importPage',
                'getTemplateSize',
                'AddPage',
                'useTemplate',
                'Output'
            )
        );

        $fpdi->expects($this->at(2))
            ->method('importPage')
            ->will($this->returnValue(2));

        $fpdi->expects($this->at(4))
            ->method('getTemplateSize')
            ->will($this->returnValue(array(10,20)));

        $fpdi->expects($this->once())
            ->method('Output')
            ->will($this->returnValue('merged'));

        $m = new Merger($fpdi);
        $m->addRaw('');
        $m->addRaw('');
        $this->assertEquals('merged', $m->merge());
    }

    /**
     * @expectedException iio\libmergepdf\Exception
     */
    public function testInvalidPageError()
    {
        $fpdi = $this->getMock(
            '\fpdi\FPDI',
            array('importPage', 'setSourceFile')
        );

        $fpdi->expects($this->once())
            ->method('importPage')
            ->will($this->throwException(new \RuntimeException));

        $m = new Merger($fpdi);
        $m->addRaw('', new Pages('2'));
        $m->merge();
    }

    /**
     * @expectedException iio\libmergepdf\Exception
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
        $m->addRaw('');
        $m->merge();
    }
}
