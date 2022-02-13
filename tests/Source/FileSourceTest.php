<?php

declare(strict_types=1);

namespace nabilanam\libmergepdf\Source;

use nabilanam\libmergepdf\PagesInterface;
use nabilanam\libmergepdf\Exception;

class FileSourceTest extends \PHPUnit\Framework\TestCase
{
    public function testExceptionOnInvalidName()
    {
        $this->expectException(Exception::CLASS);
        new FileSource('this-file-does-not-exist');
    }

    public function testGetName()
    {
        $this->assertSame(
            __FILE__,
            (new FileSource(__FILE__))->getName()
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
        $pages = $this->createMock(PagesInterface::CLASS);
        $this->assertSame(
            $pages,
            (new FileSource(__FILE__, $pages))->getPages()
        );
    }
}
