<?php
/**
 * This file is part of the libmergepdf package
 *
 * Copyright (c) 2012 Hannes Forsgård
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace iio\libmergepdf;

/**
 * Parse page numbers from string
 * 
 * @author  Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package libmergepdf
 */
class Pages
{
    /**
     * @var array Array of integer page numbers
     */
    private $pages;

    /**
     * Constructor
     *
     * Pages should be formatted as 1,3,6 or 12-16 or combined. Note that pages
     * are merged in the order that you provide them. If you put pages 12-14
     * before 1-5 then 12-14 will be placed first.
     *
     * @param  string    $pageNumbers
     * @throws Exception If unable to parse page numbers
     */
    public function __construct($pageNumbers = '')
    {
        assert('is_string($pageNumbers)');

        $pageNumbers = str_replace(' ', '', $pageNumbers);
        $this->pages = array();

        foreach (explode(',', $pageNumbers) as $part) {
            if (empty($part)) {
                continue;
            } elseif (ctype_digit($part)) {
                $this->pages[] = intval($part);
            } elseif (preg_match("/^\d+-\d+/", $part)) {
                // Get pages from range
                list($start, $end) = explode('-', $part);
                if ( $start < $end ) {
                    while ( $start <= $end ) {
                        $this->pages[] = $start;
                        $start++;
                    }
                } else {
                    while ( $end <= $start ) {
                        $this->pages[] = $start;
                        $start--;
                    }
                }
            } else {
                $msg = "Invalid page number(s) for '$part'";
                throw new Exception($msg);
            }
        }
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
