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
     * Get pdf content
     *
     * @return string
     */
    public function getContents();

    /**
     * Get stream of pdf content
     *
     * @return StreamReader
     * @deprecated Will be removed in version 4. Use getContents() instead..
     */
    public function getStreamReader();

    /**
     * Get pages to fetch from source
     *
     * @return Pages
     */
    public function getPages();
}
