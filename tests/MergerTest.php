<?php

declare(strict_types = 1);

namespace iio\libmergepdf;

use Symfony\Component\Finder\Finder;
use Prophecy\Argument;

class MergerTest extends \PHPUnit\Framework\TestCase
{
    public function testExceptionOnInvalidFile()
    {
        $this->expectException(Exception::CLASS);
        $merger = new Merger;
        $merger->addFile(__DIR__ . '/nonexistingfile');
        $merger->merge();
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

        $pages = $this->getMockBuilder(PagesInterface::class)->getMock();

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
        $pages = $this->getMockBuilder(PagesInterface::class)->getMock();

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
        $tcpdi = $this->prophesize(\TCPDI::CLASS);

        $tcpdi->setSourceData(Argument::any())->willReturn(2);

        $tcpdi->importPage(1)->willReturn('page_1');
        $tcpdi->getTemplateSize('page_1')->willReturn(['w' => 1, 'h' => 2]);
        $tcpdi->AddPage('P', [1, 2])->shouldBeCalled();
        $tcpdi->useTemplate('page_1')->shouldBeCalled();

        $tcpdi->importPage(2)->willReturn('page_2');
        $tcpdi->getTemplateSize('page_2')->willReturn(['w' => 2, 'h' => 1]);
        $tcpdi->AddPage('L', [2, 1])->shouldBeCalled();
        $tcpdi->useTemplate('page_2')->shouldBeCalled();

        $tcpdi->Output('', 'S')->willReturn('created-pdf');

        $merger = new Merger($tcpdi->reveal());
        $merger->addFile(__FILE__, new Pages('1, 2'));
        $this->assertSame('created-pdf', $merger->merge());
    }

    public function testExceptionOnFailure()
    {
        $tcpdi = $this->prophesize(\TCPDI::CLASS);
        $tcpdi->setSourceData(Argument::any())->willThrow(new \Exception('message'));

        $merger = new Merger($tcpdi->reveal());

        $this->expectException(Exception::CLASS);
        $this->expectExceptionMessage("'message' in '" . __FILE__ . "'");

        $merger->addFile(__FILE__);
        $merger->merge();
    }

    public function testReset()
    {
        $tcpdi = $this->prophesize(\TCPDI::CLASS);
        $tcpdi->Output('', 'S')->willReturn('');

        $tcpdi->setSourceData(Argument::any())->shouldNotBeCalled();

        $merger = new Merger($tcpdi->reveal());
        $merger->addFile(__FILE__);
        $merger->reset();
        $merger->merge();
    }
}
