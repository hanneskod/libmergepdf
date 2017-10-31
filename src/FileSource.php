<?php

namespace iio\libmergepdf;

use setasign\Fpdi\PdfParser\StreamReader;

/**
 * Pdf source from file
 */
class FileSource implements SourceInterface
{
    /**
     * @var string
     */
    private $filename;

    /**
     * @var Pages
     */
    private $pages;

    /**
     * @param string $filename
     * @param Pages  $pages
     */
    public function __construct($filename, Pages $pages = null)
    {
        $this->filename = $filename;
        $this->pages = $pages ?: new Pages;
    }

    public function getName()
    {
        return $this->filename;
    }

    public function getStreamReader()
    {
        return StreamReader::createByFile($this->filename);
    }

    public function getPages()
    {
        return $this->pages;
    }
}
