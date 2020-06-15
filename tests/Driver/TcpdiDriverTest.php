<?php

declare(strict_types = 1);

namespace kadudutra\libmergepdf\Driver;

use kadudutra\libmergepdf\Source\SourceInterface;
use kadudutra\libmergepdf\Exception;
use kadudutra\libmergepdf\Pages;

class TcpdiDriverTest extends \PHPUnit\Framework\TestCase
{
    public function testExceptionOnFailure()
    {
        $tcpdi = $this->prophesize(\TCPDI::CLASS);
        $tcpdi->setSourceData('foobar')->willThrow(new \Exception('message'));

        $source = $this->prophesize(SourceInterface::CLASS);
        $source->getName()->willReturn('file');
        $source->getContents()->willReturn('foobar');

        $this->expectException(Exception::CLASS);
        $this->expectExceptionMessage("'message' in 'file'");

        (new TcpdiDriver($tcpdi->reveal()))->merge($source->reveal());
    }

    public function testMerge()
    {
        $tcpdi = $this->prophesize(\TCPDI::CLASS);

        $tcpdi->setSourceData('data')->willReturn(2);

        $tcpdi->setPrintHeader(false)->shouldBeCalled();
        $tcpdi->setPrintFooter(false)->shouldBeCalled();

        $tcpdi->importPage(1)->willReturn('page_1');
        $tcpdi->getTemplateSize('page_1')->willReturn(['w' => 1, 'h' => 2]);
        $tcpdi->AddPage('P', [1, 2])->shouldBeCalled();
        $tcpdi->useTemplate('page_1')->shouldBeCalled();

        $tcpdi->importPage(2)->willReturn('page_2');
        $tcpdi->getTemplateSize('page_2')->willReturn(['w' => 2, 'h' => 1]);
        $tcpdi->AddPage('L', [2, 1])->shouldBeCalled();
        $tcpdi->useTemplate('page_2')->shouldBeCalled();

        $tcpdi->Output('', 'S')->willReturn('created-pdf');

        $source = $this->prophesize(SourceInterface::CLASS);
        $source->getName()->willReturn('');
        $source->getContents()->willReturn('data');
        $source->getPages()->willReturn(new Pages('1, 2'));

        $this->assertSame(
            'created-pdf',
            (new TcpdiDriver($tcpdi->reveal()))->merge($source->reveal())
        );
    }
}
