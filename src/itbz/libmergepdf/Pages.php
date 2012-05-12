<?php
/**
 * This file is part of the libmergepdf package
 *
 * Copyright (c) 2012 Hannes Forsgård
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package libmergepdf
 */
namespace itbz\libmergepdf;


/**
 * Parse page numbers from string
 * @package libmergepdf
 */
class Pages
{

    /**
     * Array of integer page numbers
     * @var array $pages
     */
    private $pages;


    /**
     * Parse page numbers from string
     *
     * Pages should be formatted as 1,3,6 or 12-16 or combined. Note that pages
     * are merged in the order that you provide them. If you put pages 12-14
     * before 1-5 then 12-14 will be placed first.
     *
     * @param string $pageNumbers
     * @throw Exception if unable to parse page numbers
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
     * Parse $pages to array of individual page numbers. Individual pages or
     * ranges may be specified. CSV.
     * @throws Exceptin If page ranges are malformed.
     * @param string $pages
     * @return array
     * /
    private static function parsePages($pages){
        assert('is_string($pages)');
        
        $pages = str_replace(' ', '', $pages);
        $arPages = array();
        
        foreach( explode(',', $pages) as $exp ){
            if ( empty($exp) ) continue;
            $arExp = explode('-', $exp);
            $expLen = count($arExp);
            assert('is_numeric($arExp[0])');

            //parse range
            if ( $expLen == 2 ) {
                assert('is_numeric($arExp[1])');
                
                $iStart = intval($arExp[0]);
                $iEnd = intval($arExp[1]);
                
                if ( $iStart < $iEnd ) {
                    while ( $iStart <= $iEnd ) {
                        $arPages[] = $iStart;
                        $iStart++;
                    }
                } else {
                    while ( $iEnd <= $iStart ) {
                        $arPages[] = $iStart;
                        $iStart--;
                    }
                }
                

            //single page
            } elseif ( $expLen == 1 ) {
                $arPages[] = intval($arExp[0]);
            
            //argument formatting error
            } else {
                throw new \InvalidArgumentException("Page range syntax error for expression '$exp'.");
            }
        }
        
        return $arPages;
    }*/


    /**
     * Get array of integer page numbers
     * @return array
     */
    public function getPages()
    {
        return $this->pages;
    }


    /**
     * Check if no pages are specified
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->pages);
    }

}
