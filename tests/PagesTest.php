<?php

declare(strict_types = 1);

namespace kadudutra\libmergepdf;

class PagesTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider pageNumbersProvider
     */
    public function testPageNumbers($expressionString, array $expected)
    {
        $this->assertSame(
            $expected,
            (new Pages($expressionString))->getPageNumbers()
        );
    }

    public function pageNumbersProvider()
    {
        return [
            ['', []],
            ['1', [1]],
            ['1,2', [1,2]],
            ['5-7', [5,6,7]],
            ['7-5', [7,6,5]],
            ['1,2-5,4,7-5', [1,2,3,4,5,4,7,6,5]],
            [' 1, 2- 5,, 4 ,7 -5,,', [1,2,3,4,5,4,7,6,5]],
        ];
    }

    public function testInvalidString()
    {
        $this->expectException(Exception::CLASS);
        new Pages('12,*');
    }
}
