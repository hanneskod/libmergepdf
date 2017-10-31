<?php

namespace iio\libmergepdf;

use setasign\Fpdi\PdfParser\StreamReader;

/**
 * Interface defining a source pdf to merge
 */
interface SourceInterface
{
    /**
     * Get name of file or source
     *
     * @return string
     */
    public function getName();

    /**
     * Get strom pf pdf content
     *
     * @return StreamReader
     */
    public function getStreamReader();

    /**
     * Get pages to fetch from source
     *
     * @return Pages
     */
    public function getPages();
}
