<?php

namespace iio\libmergepdf;

use setasign\Fpdi\PdfParser\StreamReader;

class RawSourceTest extends \PHPUnit_Framework_TestCase
{
    public function testGetName()
    {
        $this->assertSame(
            (new RawSource(''))->getName(),
            'raw-content'
        );
    }

    public function testGetStreamReader()
    {
        $streamReader = (new RawSource('foobar'))->getStreamReader();
        $this->assertEquals(
            $streamReader->readBytes($streamReader->getTotalLength(), 0),
            'foobar'
        );
    }

    public function testGetPages()
    {
        $this->assertSame(
            (new RawSource('', $pages = new Pages))->getPages(),
            $pages
        );
    }
}
