<?php

namespace iio\libmergepdf;

use setasign\Fpdi\Fpdi;
use Symfony\Component\Finder\Finder;

class MergerTest extends \PHPUnit_Framework_TestCase
{
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
        $m = $this->getMockBuilder(Merger::class)
            ->setMethods(['addFromFile'])
            ->getMock();

        $m->expects($this->exactly(2))
            ->method('addFromFile');

        $m->addIterator(['A', 'B']);
    }

    public function testAddFinder()
    {
        $m = $this->getMockBuilder(Merger::class)
            ->setMethods(['addFromFile'])
            ->getMock();

        $m->expects($this->exactly(2))
            ->method('addFromFile')
            ->with(__FILE__);

        $finder = $this->getMockBuilder(Finder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $file = new \SplFileInfo(__FILE__);

        $finder->expects($this->once())
            ->method('getIterator')
            ->will($this->returnValue(new \ArrayIterator([$file, $file])));

        $m->addFinder($finder);
    }

    public function testAddFinderWithPagesArgument()
    {
        $pages = $this->getMockBuilder(Pages::class)->getMock();

        $m = $this->getMockBuilder(Merger::class)
            ->setMethods(['addFromFile'])
            ->getMock();

        $m->expects($this->exactly(2))
            ->method('addFromFile')
            ->with(__FILE__, $pages);

        $finder = $this->getMockBuilder(Finder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $file = new \SplFileInfo(__FILE__);

        $finder->expects($this->once())
            ->method('getIterator')
            ->will($this->returnValue(new \ArrayIterator([$file, $file])));

        $m->addFinder($finder, $pages);
    }

    public function testMerge()
    {
        $fpdi = $this->getMockBuilder(Fpdi::class)
            ->setMethods([
                'setSourceFile',
                'importPage',
                'getTemplateSize',
                'AddPage',
                'useTemplate',
                'Output'
            ])
            ->getMock();

        $fpdi->expects($this->at(2))
            ->method('importPage')
            ->will($this->returnValue(2));

        $fpdi->expects($this->at(4))
            ->method('getTemplateSize')
            ->will($this->returnValue([10, 20]));

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
        $fpdi = $this->getMockBuilder(Fpdi::class)
            ->setMethods(['importPage', 'setSourceFile'])
            ->getMock();

        $fpdi->expects($this->once())
            ->method('importPage')
            ->will($this->throwException(new \RuntimeException));

        $m = new Merger($fpdi);
        $m->addRaw('', new Pages('2'));
        $m->merge();
    }

    /**
     * @expectedException        iio\libmergepdf\Exception
     * @expectedExceptionMessage Fpdi: 'message'
     */
    public function testFpdiException()
    {
        $fpdi = $this->getMockBuilder(Fpdi::class)
            ->setMethods(['setSourceFile'])
            ->getMock();

        $fpdi->expects($this->once())
            ->method('setSourceFile')
            ->will($this->throwException(new \RuntimeException('message')));

        $m = new Merger($fpdi);
        $m->addRaw('');
        $m->merge();
    }
}
