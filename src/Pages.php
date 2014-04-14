<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace iio\libmergepdf;

/**
 * Parse page numbers from string
 * 
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class Pages
{
    /**
     * @var array Array of integer page numbers
     */
    private $pages = array();

    /**
     * Constructor
     *
     * Pages should be formatted as 1,3,6 or 12-16 or combined. Note that pages
     * are merged in the order that you provide them. If you put pages 12-14
     * before 1-5 then 12-14 will be placed first.
     *
     * @param  string    $expressionString
     * @throws Exception If unable to parse page numbers
     */
    public function __construct($expressionString = '')
    {
        $expressions = explode(
            ',',
            str_replace(' ', '', $expressionString)
        );

        foreach ($expressions as $expr) {
            if (empty($expr)) {
                continue;
            } elseif (ctype_digit($expr)) {
                $this->addPage($expr);
            } elseif (preg_match("/^(\d+)-(\d+)/", $expr, $matches)) {
                $this->addRange($matches[1], $matches[2]);
            } else {
                throw new Exception("Invalid page number(s) for expression '$expr'");
            }
        }
    }

    /**
     * Add page to collection
     *
     * @param  int|string $page
     * @return void
     */
    public function addPage($page)
    {
        assert('is_numeric($page)');
        $this->pages[] = intval($page);
    }

    /**
     * Add range of pages
     *
     * @param  int|string $start
     * @param  int|string $end
     * @return void
     */
    public function addRange($start, $end)
    {
        assert('is_numeric($start)');
        assert('is_numeric($end)');
        $this->pages = array_merge($this->pages, range($start, $end));
    }

    /**
     * Get array of integer page numbers
     * 
     * @return array
     */
    public function getPages()
    {
        return $this->pages;
    }
}
