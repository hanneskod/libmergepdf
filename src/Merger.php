<?php

declare(strict_types = 1);

namespace iio\libmergepdf;

use iio\libmergepdf\Source\SourceInterface;
use iio\libmergepdf\Source\FileSource;
use iio\libmergepdf\Source\RawSource;

/**
 * Merge existing pdfs into one
 *
 * Note that your PDFs are merged in the order that you add them
 */
class Merger
{
    /**
     * @var SourceInterface[] List of pdf sources to merge
     */
    private $sources = [];

    /**
     * @var \TCPDI
     */
    private $tcpdi;

    public function __construct(\TCPDI $tcpdi = null)
    {
        $this->tcpdi = $tcpdi ?: new \TCPDI;
    }

    /**
     * Add raw PDF from string
     */
    public function addRaw(string $content, PagesInterface $pages = null): void
    {
        $this->sources[] = new RawSource($content, $pages);
    }

    /**
     * Add PDF from file
     */
    public function addFile(string $filename, PagesInterface $pages = null): void
    {
        $this->sources[] = new FileSource($filename, $pages);
    }

    /**
     * Add files using iterator
     *
     * Note that optional pages constraint is used for every added pdf
     */
    public function addIterator(iterable $iterator, PagesInterface $pages = null): void
    {
        foreach ($iterator as $filename) {
            $this->addFile($filename, $pages);
        }
    }

    /**
     * Merges your provided PDFs and get raw string
     *
     * @todo A note on the $resetAfterMerge flag. Prior to 3.1 the internal
     * state was always reset after merge. This behaviour is deprecated. In
     * version 4 the internal state will never be automatically reset. the
     * $resetAfterMerge flag can be used to mimic the comming behaviour
     */
    public function merge(bool $resetAfterMerge = true): string
    {
        /** @var string Name of source being processed */
        $name = '';

        try {
            $tcpdi = clone $this->tcpdi;

            foreach ($this->sources as $source) {
                $name = $source->getName();

                /** @var int Total number of pages in pdf */
                $nrOfPagesInPdf = $tcpdi->setSourceData($source->getContents());

                /** @var PagesInterface The set of pages to merge, defaults to all pages */
                $pagesToMerge = $source->getPages()->isEmpty() ? $source->getPages() : new Pages("1-$nrOfPagesInPdf");

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
     */
    public function reset(): void
    {
        $this->sources = [];
    }
}
