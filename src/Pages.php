<?php

declare(strict_types = 1);

namespace iio\libmergepdf;

/**
 * Parse page numbers from string
 */
final class Pages implements PagesInterface
{
    /**
     * @var int[] Added integer page numbers
     */
    private $pages = [];

    /**
     * Parse page numbers from expression string
     *
     * Pages should be formatted as 1,3,6 or 12-16 or combined. Note that pages
     * are merged in the order that you provide them. If you put pages 12-14
     * before 1-5 then 12-14 will be placed first.
     */
    public function __construct(string $expressionString = '')
    {
        $expressions = explode(
            ',',
            str_replace(' ', '', $expressionString)
        );

        foreach ($expressions as $expr) {
            if (empty($expr)) {
                continue;
            }
            if (ctype_digit($expr)) {
                $this->addPage((int)$expr);
                continue;
            }
            if (preg_match("/^(\d+)-(\d+)/", $expr, $matches)) {
                $this->addRange((int)$matches[1], (int)$matches[2]);
                continue;
            }
            throw new Exception("Invalid page number(s) for expression '$expr'");
        }
    }

    /**
     * Add a single page
     */
    public function addPage(int $page): void
    {
        $this->pages[] = $page;
    }

    /**
     * Add a range of pages
     */
    public function addRange(int $start, int $end): void
    {
        $this->pages = array_merge($this->pages, range($start, $end));
    }

    public function getPageNumbers(): array
    {
        return $this->pages;
    }
}
