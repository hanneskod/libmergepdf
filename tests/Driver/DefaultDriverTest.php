<?php

declare(strict_types = 1);

namespace iio\libmergepdf\Driver;

use iio\libmergepdf\Source\SourceInterface;
use Prophecy\PhpUnit\ProphecyTrait;

class DefaultDriverTest extends \PHPUnit\Framework\TestCase
{
    use ProphecyTrait;
    public function testMerge()
    {
        $wrapped = $this->prophesize(DriverInterface::CLASS);

        $source1 = $this->createMock(SourceInterface::CLASS);
        $source2 = $this->createMock(SourceInterface::CLASS);

        $wrapped->merge($source1, $source2)->willReturn('foo')->shouldBeCalled();

        $driver = new DefaultDriver($wrapped->reveal());

        $this->assertEquals(
            'foo',
            $driver->merge($source1, $source2)
        );
    }
}
