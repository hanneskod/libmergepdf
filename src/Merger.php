<?php

namespace iio\libmergepdf;

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
     * @var \TCPDI
     */
    private $tcpdi;

    /**
     * @var string Directory path used for temporary files
     */
    private $tempDir;

    public function __construct(\TCPDI $tcpdi = null)
    {
        $this->tcpdi = $tcpdi ?: new \TCPDI;
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
        $this->sources[] = new RawSource($content, $pages);
    }

    /**
     * Add PDF from file
     *
     * Note that your PDFs are merged in the order that you add them
     *
     * @param  string $filename Name of file to add
     * @param  Pages  $pages    Pages to add from file
     * @return void
     */
    public function addFile($filename, Pages $pages = null)
    {
        $this->sources[] = new FileSource($filename, $pages);
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
     * @todo A note on the $resetAfterMerge flag. Prior to 3.1 the internal
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
            $tcpdi = clone $this->tcpdi;

            foreach ($this->sources as $source) {
                $name = $source->getName();

                /** @var int Total number of pages in pdf */
                $nrOfPagesInPdf = $tcpdi->setSourceData($source->getContents());

                /** @var Pages The set of pages to merge, defaults to all pages */
                $pagesToMerge = $source->getPages()->hasPages() ? $source->getPages() : new Pages("1-$nrOfPagesInPdf");

                // Add specified pages
                foreach ($pagesToMerge as $pageNr) {
                    $template = $tcpdi->importPage($pageNr);
                    $size = $tcpdi->getTemplateSize($template);
                    $tcpdi->AddPage(
                        $size['w'] > $size['h'] ? 'L' : 'P',
                        [$size['w'], $size['h']]
                    );
                    $tcpdi->useTemplate($template);
                }
            }

            if ($resetAfterMerge) {
                $this->reset();
            }

            return $tcpdi->Output('', 'S');

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
}
