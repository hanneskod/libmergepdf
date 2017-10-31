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
     * List of pdf sources to merge
     *
     * @var SourceInterface[]
     */
    private $sources = [];

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
        $this->sources[] = new RawSource($content, $pages ?: new Pages);
    }

    /**
     * Add PDF from file
     *
     * Note that your PDFs are merged in the order that you add them
     *
     * @param  string    $filename Name of file to add
     * @param  Pages     $pages    Pages to add from file
     * @return void
     */
    public function addFile($filename, Pages $pages = null)
    {
        $this->sources[] = new FileSource($filename, $pages ?: new Pages);
    }

    /**
     * Add PDF from file
     *
     * @deprecated Since version 3.1
     */
    public function addFromFile($fname, Pages $pages = null, $cleanup = null)
    {
        trigger_error('addFromFile() is deprecated, use addFile() instead', E_USER_DEPRECATED);
        $this->addFile($fname, $pages);
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

        foreach ($iterator as $filename) {
            $this->addFile($filename, $pages);
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
            $this->addFile($fileInfo->getRealpath(), $pages);
        }
    }

    /**
     * Merges your provided PDFs and get raw string
     *
     * A note on the $resetAfterMerge flag. Prior to version 3.1 the internal
     * state was always reset after merge. This behaviour is deprecated. In
     * version 4 the internal state will never be automatically reset. the
     * $resetAfterMerge flag can be used to mimic the comming behaviour
     *
     * @param  boolean   $resetAfterMerge Flag if internal state should reset after merge
     * @return string
     * @throws Exception On failure
     */
    public function merge($resetAfterMerge = true)
    {
        /** @var string Name of source being processed */
        $name = '';

        try {
            $fpdi = clone $this->fpdi;

            foreach ($this->sources as $source) {
                $name = $source->getName();

                /** @var int Total number of pages in pdf */
                $nrOfPagesInPdf = $fpdi->setSourceFile($source->getStreamReader());

                /** @var Pages The set of pages to merge, defaults to all pages */
                $pagesToMerge = $source->getPages()->hasPages() ? $source->getPages() : new Pages("1-$nrOfPagesInPdf");

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

            if ($resetAfterMerge) {
                $this->reset();
            }

            return $fpdi->Output('', 'S');

        } catch (\Exception $e) {
            throw new Exception("'{$e->getMessage()}' in '{$name}'", 0, $e);
        }
    }

    /**
     * Reset internal state
     *
     * @return void
     */
    public function reset()
    {
        $this->sources = [];
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
