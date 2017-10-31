<?php

namespace iio\libmergepdf;

use setasign\Fpdi\Fpdi;
use Symfony\Component\Finder\Finder;
use Prophecy\Argument;

class MergerTest extends \PHPUnit_Framework_TestCase
{
    public function testExceptionOnInvalidFile()
    {
        $this->expectException(Exception::CLASS);
        $merger = new Merger;
        $merger->addFile(__DIR__ . '/nonexistingfile');
        $merger->merge();
    }

    public function testExceptionOnInvalidIterator()
    {
        $this->expectException(Exception::CLASS);
        (new Merger)->addIterator(null);
    }

    public function testAddIterator()
    {
        $merger = $this->getMockBuilder(Merger::class)
            ->setMethods(['addFile'])
            ->getMock();

        $merger->expects($this->exactly(2))
            ->method('addFile');

        $merger->addIterator(['A', 'B']);
    }

    public function testAddIteratorWithPagesArgument()
    {
        $merger = $this->getMockBuilder(Merger::class)
            ->setMethods(['addFile'])
            ->getMock();

        $pages = $this->getMockBuilder(Pages::class)->getMock();

        $merger->expects($this->exactly(1))
            ->method('addFile')
            ->with('foo', $pages);

        $merger->addIterator(['foo'], $pages);
    }

    public function testAddFinder()
    {
        $merger = $this->getMockBuilder(Merger::class)
            ->setMethods(['addFile'])
            ->getMock();

        $merger->expects($this->exactly(2))
            ->method('addFile')
            ->with(__FILE__);

        $finder = $this->getMockBuilder(Finder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $file = new \SplFileInfo(__FILE__);

        $finder->expects($this->once())
            ->method('getIterator')
            ->will($this->returnValue(new \ArrayIterator([$file, $file])));

        $merger->addFinder($finder);
    }

    public function testAddFinderWithPagesArgument()
    {
        $pages = $this->getMockBuilder(Pages::class)->getMock();

        $merger = $this->getMockBuilder(Merger::class)
            ->setMethods(['addFile'])
            ->getMock();

        $merger->expects($this->exactly(2))
            ->method('addFile')
            ->with(__FILE__, $pages);

        $finder = $this->getMockBuilder(Finder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $file = new \SplFileInfo(__FILE__);

        $finder->expects($this->once())
            ->method('getIterator')
            ->will($this->returnValue(new \ArrayIterator([$file, $file])));

        $merger->addFinder($finder, $pages);
    }

    public function testMerge()
    {
        $fpdi = $this->prophesize(Fpdi::CLASS);

        $fpdi->setSourceFile(Argument::any())->willReturn(2);

        $fpdi->importPage(1)->willReturn('page_1');
        $fpdi->getTemplateSize('page_1')->willReturn(['width' => 1, 'height' => 2]);
        $fpdi->AddPage('P', [1, 2])->shouldBeCalled();
        $fpdi->useTemplate('page_1')->shouldBeCalled();

        $fpdi->importPage(2)->willReturn('page_2');
        $fpdi->getTemplateSize('page_2')->willReturn(['width' => 2, 'height' => 1]);
        $fpdi->AddPage('L', [2, 1])->shouldBeCalled();
        $fpdi->useTemplate('page_2')->shouldBeCalled();

        $fpdi->Output('', 'S')->willReturn('created-pdf');

        $merger = new Merger($fpdi->reveal());
        $merger->addFile(__FILE__, new Pages('1, 2'));
        $this->assertSame('created-pdf', $merger->merge());
    }

    public function testExceptionOnFailure()
    {
        $fpdi = $this->prophesize(Fpdi::CLASS);
        $fpdi->setSourceFile(Argument::any())->willThrow(new \Exception('message'));

        $merger = new Merger($fpdi->reveal());

        $this->expectException(Exception::CLASS);
        $this->expectExceptionMessage("'message' in '" . __FILE__ . "'");

        $merger->addFile(__FILE__);
        $merger->merge();
    }

    public function testReset()
    {
        $fpdi = $this->prophesize(Fpdi::CLASS);
        $fpdi->Output('', 'S')->willReturn('');

        $fpdi->setSourceFile(Argument::any())->shouldNotBeCalled();

        $merger = new Merger($fpdi->reveal());
        $merger->addFile(__FILE__);
        $merger->reset();
        $merger->merge();
    }
}
