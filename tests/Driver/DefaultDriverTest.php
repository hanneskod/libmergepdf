<?php

declare(strict_types = 1);

namespace kadudutra\libmergepdf\Driver;

use kadudutra\libmergepdf\Source\SourceInterface;

class DefaultDriverTest extends \PHPUnit\Framework\TestCase
{
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
