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

    /**
     * @expectedException iio\libmergepdf\Exception
     */
    public function testAddInvalidIterator()
    {
        $m = new Merger();
        $m->addIterator(null);
    }

    public function testAddIterator()
    {
        $m = $this->getMock(
            '\iio\libmergepdf\Merger',
            array('addFromFile')
        );

        $m->expects($this->exactly(2))
            ->method('addFromFile');

        $m->addIterator(array('A', 'B'));
    }

    public function testAddFinder()
    {
        $m = $this->getMock(
            '\iio\libmergepdf\Merger',
            array('addFromFile')
        );

        $m->expects($this->exactly(2))
            ->method('addFromFile')
            ->with(__FILE__);

        $finder = $this->getMockBuilder('\Symfony\Component\Finder\Finder')
            ->disableOriginalConstructor()
            ->getMock();

        $file = new \SplFileInfo(__FILE__);

        $finder->expects($this->once())
            ->method('getIterator')
            ->will($this->returnValue(new \ArrayIterator(array($file, $file))));

        $m->addFinder($finder);
    }

    public function testMerge()
    {
        $fpdi = $this->getMock(
            '\FPDI',
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
            '\FPDI',
            array('importPage', 'setSourceFile')
        );

        $fpdi->expects($this->once())
            ->method('importPage')
            ->will($this->throwException(new \Exception));

        $m = new Merger($fpdi);
        $m->addRaw('', new Pages('2'));
        $m->merge();
    }

    /**
     * @expectedException        iio\libmergepdf\Exception
     * @expectedExceptionMessage FPDI: 'message' in '
     */
    public function testFpdiException()
    {
        $fpdi = $this->getMock(
            '\FPDI',
            array('setSourceFile')
        );

        $fpdi->expects($this->once())
            ->method('setSourceFile')
            ->will($this->throwException(new \Exception('message')));

        $m = new Merger($fpdi);
        $m->addRaw('');
        $m->merge();
    }

    public function testSetGetTempDir()
    {
        $m = new Merger;

        $this->assertSame(
            sys_get_temp_dir(),
            $m->getTempDir()
        );

        $newTempDir = "foobar";
        $m->setTempDir($newTempDir);

        $this->assertSame(
            $newTempDir,
            $m->getTempDir()
        );
    }
}
