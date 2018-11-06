<?php

namespace iio\libmergepdf;

use setasign\Fpdi\PdfParser\StreamReader;

class RawSourceTest extends \PHPUnit\Framework\TestCase
{
    public function testGetName()
    {
        $this->assertSame(
            'raw-content',
            (new RawSource(''))->getName()
        );
    }

    public function testgetContents()
    {
        $this->assertSame(
            'foobar',
            (new RawSource('foobar'))->getContents()
        );
    }

    public function testGetPages()
    {
        $pages = new Pages;
        $this->assertSame(
            $pages,
            (new RawSource('', $pages))->getPages()
        );
    }
}
