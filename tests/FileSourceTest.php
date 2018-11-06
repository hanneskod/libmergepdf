<?php

namespace iio\libmergepdf;

class FileSourceTest extends \PHPUnit_Framework_TestCase
{
    public function testGetName()
    {
        $this->assertSame(
            'foobar',
            (new FileSource('foobar'))->getName()
        );
    }

    public function testgetContents()
    {
        $this->assertSame(
            file_get_contents(__FILE__),
            (new FileSource(__FILE__))->getContents()
        );
    }

    public function testGetPages()
    {
        $pages = new Pages;
        $this->assertSame(
            $pages,
            (new FileSource('', $pages))->getPages()
        );
    }
}
