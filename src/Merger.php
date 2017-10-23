<?php

namespace iio\libmergepdf;

use setasign\Fpdi\Fpdi;
use Symfony\Component\Finder\Finder;

/**
 * Merge existing pdfs into one
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
            throw new Exception("Unable to create temporary file");
        }

        $this->addFromFile($fname, $pages, true);
    }

    /**
     * Add PDF from filesystem path
     *
     * Note that your PDFs are merged in the order that you add them
     *
     * @param  string    $fname   Name of file to add
     * @param  Pages     $pages   Pages to add from file
     * @param  bool      $cleanup Flag if file should be deleted after merging
     * @return void
     * @throws Exception If $fname is not a valid file
     */
    public function addFromFile($fname, Pages $pages = null, $cleanup = false)
    {
        assert('is_string($fname)');
        assert('is_bool($cleanup)');

        if (!is_file($fname) || !is_readable($fname)) {
            throw new Exception("'$fname' is not a valid file");
        }

        if (!$pages) {
            $pages = new Pages();
        }

        $this->files[] = array($fname, $pages, $cleanup);
    }

    /**
     * Add files using iterator
     *
     * @param  array|\Traversable $iterator
     * @return void
     * @throws Exception If $iterator is not valid
     */
    public function addIterator($iterator)
    {
        if (!is_array($iterator) && !$iterator instanceof \Traversable) {
            throw new Exception("\$iterator must be traversable");
        }

        foreach ($iterator as $fname) {
            $this->addFromFile($fname);
        }
    }

    /**
     * Add files using symfony finder
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

        $fname = '';
        try {
            $fpdi = clone $this->fpdi;

            foreach ($this->files as $fileData) {
                list($fname, $pages, $cleanup) = $fileData;
                $pages = $pages->getPages();
                $iPageCount = $fpdi->setSourceFile($fname);

                // If no pages are specified, add all pages
                if (empty($pages)) {
                    $pages = range(1, $iPageCount);
                }

                // Add specified pages
                foreach ($pages as $page) {
                    $template = $fpdi->importPage($page);
                    $size = $fpdi->getTemplateSize($template);
                    $orientation = ($size['width'] > $size['height']) ? 'L' : 'P';
                    $fpdi->AddPage($orientation, array($size['width'], $size['height']));
                    $fpdi->useTemplate($template);
                }
            }

            $output = $fpdi->Output('', 'S');

            foreach ($this->files as $fileData) {
                list($fname, $pages, $cleanup) = $fileData;
                if ($cleanup) {
                    unlink($fname);
                }
            }

            $this->files = array();

            return $output;

        } catch (\Exception $e) {
            throw new Exception("Fpdi: '{$e->getMessage()}' in '$fname'", 0, $e);
        }
    }

    /**
     * Create temporary file and return name
     *
     * @return string
     */
    public function getTempFname()
    {
        return tempnam($this->getTempDir(), "libmergepdf");
    }

    /**
     * Get directory path for temporary files
     *
     * Set path using setTempDir(), defaults to sys_get_temp_dir().
     *
     * @return string
     */
    public function getTempDir()
    {
        return $this->tempDir ?: sys_get_temp_dir();
    }

    /**
     * Set directory path for temporary files
     *
     * @param  string $dirname
     * @return void
     */
    public function setTempDir($dirname)
    {
        $this->tempDir = $dirname;
    }
}
