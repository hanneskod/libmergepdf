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
namespace libmergepdf;

/*
    * break out pages to a separate class
*/


/**
 * Merge existing pdfs into one
 *
 * Uses FPDI 1.3.1 from Setasign and FPDF 1.6 by Olivier Plathey with the
 * FPDF_TPL 1.1.3 extension by Setasign. Merger has all the limitations of the
 * FPDI package: it cannot import dynamic content such as form fields, links or
 * page annotations (anything not a part of the page content stream).
 *
 * @package libmergepdf 
 */
class Merger
{

    /**
     * Array of files to be merged. Values for each files are filename
     * pages unparsed pages string and a boolean value indicating if
     * the file should be deleted after merging is complete.
     * @var array $files
     */
    private $files = array();


    /**
     * Include FPDF and FPDI. Optionally specify a path. If used path should
     * work as $pathToFPDF/fpdf/fpdf.php and $pathToFPDF/fpdi/fpdi.php.
     * Always use trailing slash.
     * @param string $pathToFPDF NOTE always use trailing slash.
     */
    public function __construct($pathToFPDF=''){
        assert('is_string($pathToFPDF)');
        require_once("{$pathToFPDF}fpdf/fpdf.php");
        require_once("{$pathToFPDF}fpdi/fpdi.php");
    }
    

    /**
     * Add a PDF for inclusion. Pages should be formatted as 1,3,6 or 12-16 or combined.
     * Note that your PDFs are merged in the order that you provide them, same as the pages.
     * If you put pages 12-14 before 1-5 then 12-15 will be placed first in the output.
     * @param string $pdf
     * @param string $pages
     * @return void
     */
    public function add($pdf, $pages='') {
        assert('is_string($pdf)');
        assert('is_string($pages)');

        //Write pdf to temporary file
        $tmpDir = sys_get_temp_dir();
        $tmpName = tempnam($tmpDir, "phpdf_");
        if ( file_put_contents($tmpName, $pdf) === false ) {
            trigger_error("Unable to write to sys_get_temp_dir.", E_USER_ERROR);
        }
        
        $this->addFile($tmpName, $pages, true);
    }

    
    /**
     * Add a PDF for inclusion from a filesystem path. Se add() for a description
     * of the $pages format.
     * @param string $fname
     * @param string $pages
     * @param bool $deleteAfterMerge Boolean value indicating if
     * the file should be deleted after merging is complete.
     * @return void
     */
    public function addFile($fname, $pages='', $deleteAfterMerge=false){
        assert('is_readable($fname)');
        assert('is_string($pages)');
        assert('is_bool($deleteAfterMerge)');
        $this->files[] = array($fname, $pages, $deleteAfterMerge);
    }



    /**
     * Merges your provided PDFs and outputs to specified location.
     * @uses FPDI
     * @throws RangeException if a specified page does not exist
     * @return string
     */
    public function get(){
        assert('!empty($this->files)');
        $fpdi = new \FPDI;
        
        foreach ( $this->files as $arFile ) {
            list($fname, $pages, $deleteAfterMerge) = $arFile;
            
            $iPageCount = $fpdi->setSourceFile($fname);
            
            //add all pages
            if ( empty($pages) ) {
                for ( $i=1; $i<=$iPageCount; $i++) {
                    $template = $fpdi->importPage($i);
                    $size = $fpdi->getTemplateSize($template);
                    $fpdi->AddPage('P', array($size['w'], $size['h']));
                    $fpdi->useTemplate($template);
                }
            
            //add selected pages
            } else {
                foreach ( self::parsePages($pages) as $page ) {
                    if( !$template = $fpdi->importPage($page) ) {
                        throw new \RangeException("Could not load page '$page' from '$fname'. Check that the page exists.");
                    }
                    $size = $fpdi->getTemplateSize($template);
                    $fpdi->AddPage('P', array($size['w'], $size['h']));
                    $fpdi->useTemplate($template);
                }
            }
            
            if ( $deleteAfterMerge ) {
                unlink($fname);
            }
        }
        
        //Reset files array
        $this->files = array();
        
        return $fpdi->Output('', 'S');
    }


    /**
     * Parse $pages to array of individual page numbers. Individual pages or
     * ranges may be specified. CSV.
     * @throws \InvalidArgumentException If page ranges are malformed.
     * @param string $pages
     * @return array
     */
    private static function parsePages($pages){
        assert('is_string($pages)');
        
        $pages = str_replace(' ', '', $pages);
        $arPages = array();
        
        foreach( explode(',', $pages) as $exp ){
            if ( empty($exp) ) continue;
            $arExp = explode('-', $exp);
            $expLen = count($arExp);
            assert('is_numeric($arExp[0]) /* $pages correct? */');

            //parse range
            if ( $expLen == 2 ) {
                assert('is_numeric($arExp[1]) /* $pages correct? */');
                
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
    }
    
}
