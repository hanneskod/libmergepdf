<?php

declare(strict_types = 1);

namespace kadudutra\libmergepdf;

use kadudutra\libmergepdf\Driver\DriverInterface;
use kadudutra\libmergepdf\Source\FileSource;
use kadudutra\libmergepdf\Source\RawSource;

class MergerTest extends \PHPUnit\Framework\TestCase
{
    public function testAddRaw()
    {
        $pages = $this->createMock(PagesInterface::CLASS);

        $driver = $this->prophesize(DriverInterface::CLASS);
        $driver->merge(new RawSource('foo', $pages))->willReturn('')->shouldBeCalled();

        $merger = new Merger($driver->reveal());
        $merger->addRaw('foo', $pages);
        $merger->merge();
    }

    public function testAddFile()
    {
        $pages = $this->createMock(PagesInterface::CLASS);

        $driver = $this->prophesize(DriverInterface::CLASS);
        $driver->merge(new FileSource(__FILE__, $pages))->willReturn('')->shouldBeCalled();

        $merger = new Merger($driver->reveal());
        $merger->addFile(__FILE__, $pages);
        $merger->merge();
    }

    public function testAddIterator()
    {
        $pages = $this->createMock(PagesInterface::CLASS);

        $driver = $this->prophesize(DriverInterface::CLASS);
        $driver->merge(new FileSource(__FILE__, $pages))->willReturn('')->shouldBeCalled();

        $merger = new Merger($driver->reveal());
        $merger->addIterator([__FILE__], $pages);
        $merger->merge();
    }

    public function testReset()
    {
        $pages = $this->createMock(PagesInterface::CLASS);

        $driver = $this->prophesize(DriverInterface::CLASS);
        $driver->merge()->willReturn('')->shouldBeCalled();

        $merger = new Merger($driver->reveal());
        $merger->addRaw('foo', $pages);
        $merger->reset();
        $merger->merge();
    }
}
