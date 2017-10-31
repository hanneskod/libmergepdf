<?php

namespace iio\libmergepdf;

use setasign\Fpdi\PdfParser\StreamReader;

class FileSourceTest extends \PHPUnit_Framework_TestCase
{
    public function testGetName()
    {
        $this->assertSame(
            (new FileSource('foobar'))->getName(),
            'foobar'
        );
    }

    public function testGetStreamReader()
    {
        $streamReader = (new FileSource(__FILE__))->getStreamReader();
        $this->assertEquals(
            $streamReader->readBytes($streamReader->getTotalLength(), 0),
            file_get_contents(__FILE__)
        );
    }

    public function testGetPages()
    {
        $this->assertSame(
            (new FileSource('', $pages = new Pages))->getPages(),
            $pages
        );
    }
}
