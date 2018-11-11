<?php

declare(strict_types = 1);

namespace iio\libmergepdf\Driver;

use iio\libmergepdf\Exception;
use iio\libmergepdf\Pages;
use iio\libmergepdf\Source\SourceInterface;
use setasign\Fpdi\Tcpdf\Fpdi;
use Prophecy\Argument;

class Fpdi2DriverTest extends \PHPUnit\Framework\TestCase
{
    public function testExceptionOnFailure()
    {
        $fpdi = $this->prophesize(Fpdi::CLASS);
        $fpdi->setSourceFile(Argument::any())->willThrow(new \Exception('message'));

        $source = $this->prophesize(SourceInterface::CLASS);
        $source->getName()->willReturn('file');
        $source->getContents()->willReturn('');

        $this->expectException(Exception::CLASS);
        $this->expectExceptionMessage("'message' in 'file'");

        (new Fpdi2Driver($fpdi->reveal()))->merge($source->reveal());
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

        $source = $this->prophesize(SourceInterface::CLASS);
        $source->getName()->willReturn('');
        $source->getContents()->willReturn('');
        $source->getPages()->willReturn(new Pages('1, 2'));

        $this->assertSame(
            'created-pdf',
            (new Fpdi2Driver($fpdi->reveal()))->merge($source->reveal())
        );
    }
}