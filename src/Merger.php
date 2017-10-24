<?php

namespace iio\libmergepdf;

use setasign\Fpdi\Fpdi;
use Symfony\Component\Finder\Finder;
use setasign\Fpdi\PdfParser\StreamReader;

/**
 * Merge existing pdfs into one
 */
class Merger
{
    /**
     * Array of files to be merged.
     *
     * Values for each files are file contents and Pages object.
     */
    private $files = [];

    /**
     * @var Fpdi Fpdi object
     */
    private $fpdi;

    /**
     * @var string Directory path used for temporary files
     */
    private $tempDir;

    /**
     * Constructor
     *
     * @param Fpdi $fpdi
     */
    public function __construct(Fpdi $fpdi = null)
    {
        $this->fpdi = $fpdi ?: new Fpdi;
    }

    /**
     * Add raw PDF from string
     *
     * Note that your PDFs are merged in the order that you add them
     *
     * @param  string $content Raw pdf content
     * @param  Pages  $pages   Specification of the pages to add
     * @return void
     */
    public function addRaw($content, Pages $pages = null)
    {
        $this->files[] = [
            (string)$content,
            $pages ?: new Pages
        ];
    }

    /**
     * Add PDF from filesystem path
     *
     * Note that your PDFs are merged in the order that you add them
     *
     * @param  string    $fname   Name of file to add
     * @param  Pages     $pages   Pages to add from file
     * @return void
     * @throws Exception If $fname is not a valid file
     */
    public function addFromFile($fname, Pages $pages = null, $cleanup = null)
    {
        if (!is_null($cleanup)) {
            trigger_error('Use of $cleanup argument is deprecated', E_USER_DEPRECATED);
        }

        if (!is_file($fname) || !is_readable($fname)) {
            throw new Exception("'$fname' is not a valid file");
        }

        $this->addRaw(file_get_contents($fname), $pages);
    }

    /**
     * Add files using iterator
     *
     * @param  iterable  $iterator Iterator or array with names of files to merge
     * @param  Pages     $pages    Optional pages constraint used for every added pdf
     * @return void
     * @throws Exception If $iterator is not valid
     */
    public function addIterator($iterator, Pages $pages = null)
    {
        if (!is_array($iterator) && !$iterator instanceof \Traversable) {
            throw new Exception("\$iterator must be traversable");
        }

        foreach ($iterator as $fname) {
            $this->addFromFile($fname, $pages);
        }
    }

    /**
     * Add files using a symfony finder
     *
     * @param  Finder $finder
     * @param  Pages  $pages  Optional pages constraint used for every added pdf
     * @return void
     */
    public function addFinder(Finder $finder, Pages $pages = null)
    {
        foreach ($finder as $fileInfo) {
            $this->addFromFile($fileInfo->getRealpath(), $pages);
        }
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
            throw new Exception("Unable to merge, no PDFs added");
        }

        try {
            $fpdi = clone $this->fpdi;

            foreach ($this->files as $fileData) {
                list($contents, $pagesToMerge) = $fileData;
                $nrOfPagesInPdf = $fpdi->setSourceFile(StreamReader::createByString($contents));

                // If no pages are specified, add all pages
                if (!$pagesToMerge->hasPages()) {
                    $pagesToMerge = range(1, $nrOfPagesInPdf);
                }

                // Add specified pages
                foreach ($pagesToMerge as $pageNr) {
                    $template = $fpdi->importPage($pageNr);
                    $size = $fpdi->getTemplateSize($template);
                    $fpdi->AddPage(
                        $size['width'] > $size['height'] ? 'L' : 'P',
                        [$size['width'], $size['height']]
                    );
                    $fpdi->useTemplate($template);
                }
            }

            $this->files = [];

            return $fpdi->Output('', 'S');

        } catch (\Exception $e) {
            throw new Exception("Fpdi: '{$e->getMessage()}'", 0, $e);
        }
    }

    /**
     * Create temporary file and return name
     *
     * @deprecated Since version 3.1
     */
    public function getTempFname()
    {
        trigger_error(
            'Use of getTempFname() is deprecated as temporare files are no longer created',
            E_USER_DEPRECATED
        );

        return tempnam($this->getTempDir(), "libmergepdf");
    }

    /**
     * Get directory path for temporary files
     *
     * @deprecated Since version 3.1
     */
    public function getTempDir()
    {
        trigger_error(
            'Use of getTempDir() is deprecated as temporare files are no longer created',
            E_USER_DEPRECATED
        );

        return $this->tempDir ?: sys_get_temp_dir();
    }

    /**
     * Set directory path for temporary files
     *
     * @deprecated Since version 3.1
     */
    public function setTempDir($dirname)
    {
        trigger_error(
            'Use of setTempDir() is deprecated as temporare files are no longer created',
            E_USER_DEPRECATED
        );

        $this->tempDir = $dirname;
    }
}
