<?php

declare(strict_types = 1);

namespace iio\libmergepdf;

class IntegrationTest extends \PHPUnit\Framework\TestCase
{
    public function testMerge()
    {
        $target = __DIR__ . '/A.tmp.pdf';
        @unlink($target);

        $merger = new Merger;
        $merger->addFile(__DIR__ . '/A.pdf');
        $merger->addFile(__DIR__ . '/A.pdf', new Pages('1'));
        file_put_contents($target, $merger->merge());

        $merger = new Merger;
        $merger->addFile($target);
        $merger->addFile($target, new Pages('1'));
        file_put_contents($target, $merger->merge());

        $this->assertTrue(file_exists($target), "$target should be created");
    }

    public function testMergePdf15()
    {
        $target = __DIR__ . '/PDF15.tmp.pdf';
        @unlink($target);

        $merger = new Merger;
        $merger->addFile(__DIR__ . '/PDF15.pdf');
        file_put_contents($target, $merger->merge());

        $this->assertTrue(file_exists($target), "$target should be created");
    }

    public function testMergePdf16()
    {
        $target = __DIR__ . '/PDF16.tmp.pdf';
        @unlink($target);

        $merger = new Merger;
        $merger->addFile(__DIR__ . '/PDF16.pdf');
        file_put_contents($target, $merger->merge());

        $this->assertTrue(file_exists($target), "$target should be created");
    }

    public function testMergePdf17()
    {
        $target = __DIR__ . '/PDF17.tmp.pdf';
        @unlink($target);

        $merger = new Merger;
        $merger->addFile(__DIR__ . '/PDF17.pdf');
        file_put_contents($target, $merger->merge());

        $this->assertTrue(file_exists($target), "$target should be created");
    }
}
