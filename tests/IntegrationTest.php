<?php

namespace iio\libmergepdf;

class IntegrationTest extends \PHPUnit_Framework_TestCase
{
    public function testMerge()
    {
        $target = __DIR__ . '/AA.pdf';
        @unlink($target);

        $m = new Merger();
        $m->addFromFile(__DIR__ . '/A.pdf');
        $m->addFromFile(__DIR__ . '/A.pdf', new Pages('1'));
        file_put_contents($target, $m->merge());

        $this->assertTrue(file_exists($target), "$target should be created");
    }
}
