<?php

namespace iio\libmergepdf;

class IntegrationTest extends \PHPUnit_Framework_TestCase
{
    public function testMerge()
    {
        $target = __DIR__ . '/AA.pdf';
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
}
