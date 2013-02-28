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

use fpdi\FPDI;
use RuntimeException;

/**
 * Merge existing pdfs into one
 * 
 * @author  Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package libmergepdf 
 */
class Merger
{
    /**
     * Array of files to be merged.
     * 
     * Values for each files are filename, Pages object and a boolean value
     * indicating if the file should be deleted after merging is complete.
     * 
     * @var array
     */
    private $files = array();

    /**
     * @var FPDI Fpdi object
     */
    private $fpdi;

    /**
     * Constructor
     * 
     * @param FPDI $fpdi
     */
    public function __construct(FPDI $fpdi)
    {
        $this->fpdi = $fpdi;
    }

    /**
     * Add raw PDF from string
     *
     * Note that your PDFs are merged in the order that you add them
     *
     * @param  string    $pdf
     * @param  Pages     $pages
     * @return void
     * @throws Exception if unable to create temporary file
     */
    public function addRaw($pdf, Pages $pages = null)
    {
        assert('is_string($pdf)');

        // Create temporary file
        $fname = $this->getTempFname();
        if (@file_put_contents($fname, $pdf) === false) {
            $msg = "Unable to create temporary file";
            throw new Exception($msg);
        }

        $this->addFromFile($fname, $pages, true);
    }

    /**
     * Add PDF from filesystem path
     *
     * Note that your PDFs are merged in the order that you add them
     *
     * @param  string    $fname
     * @param  Pages     $pages
     * @param  bool      $cleanup Flag if file should be deleted after merging
     * @return void
     * @throws Exception If $fname is not a valid file
     */
    public function addFromFile($fname, Pages $pages = null, $cleanup = false)
    {
        assert('is_string($fname)');
        assert('is_bool($cleanup)');

        if (!is_file($fname) || !is_readable($fname)) {
            $msg = "'$fname' is not a valid file";
            throw new Exception($msg);
        }

        if (!$pages) {
            $pages = new Pages();
        }

        $this->files[] = array($fname, $pages, $cleanup);
    }

    /**
     * Merges your provided PDFs and get raw string
     * 
     * @return string
     * @throws Exception If no PDFs were added
     * @throws Exception If a specified page does not exist
     */
    public function merge()
    {
        if (empty($this->files)) {
            $msg = "Unable to merge, no PDFs added";
            throw new Exception($msg);
        }

        try {
            $fpdi = clone $this->fpdi;

            foreach ($this->files as $fileData) {
                list($fname, $pages, $cleanup) = $fileData;
                $pages = $pages->getPages();
                $iPageCount = $fpdi->setSourceFile($fname);

                if (empty($pages)) {
                    // Add all pages
                    for ($i=1; $i<=$iPageCount; $i++) {
                        $template = $fpdi->importPage($i);
                        $size = $fpdi->getTemplateSize($template);
                        $orientation = ($size['w'] > $size['h']) ? 'L' : 'P';
                        $fpdi->AddPage($orientation, array($size['w'], $size['h']));
                        $fpdi->useTemplate($template);
                    }
                } else {
                    // Add specified pages
                    foreach ($pages as $page) {
                        $template = $fpdi->importPage($page);
                        $size = $fpdi->getTemplateSize($template);
                        $orientation = ($size['w'] > $size['h']) ? 'L' : 'P';
                        $fpdi->AddPage($orientation, array($size['w'], $size['h']));
                        $fpdi->useTemplate($template);
                    }
                }

                if ($cleanup) {
                    unlink($fname);
                }
            }

            $this->files = array();

            return $fpdi->Output('', 'S');

        } catch (RuntimeException $e) {
            // FPDI always throws RuntimeExceptions...
            $msg = "FPDI: " . $e->getMessage();
            throw new Exception($msg, 0, $e);
        }
    }

    /**
     * Create temporary file and return name
     * 
     * @return string
     */
    public function getTempFname()
    {
        return tempnam(sys_get_temp_dir(), "libmergepdf");
    }
}
