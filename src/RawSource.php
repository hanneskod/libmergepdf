<?php

namespace iio\libmergepdf;

use setasign\Fpdi\PdfParser\StreamReader;

/**
 * Pdf source from raw string
 */
class RawSource implements SourceInterface
{
    /**
     * @var string
     */
    private $contents;

    /**
     * @var Pages
     */
    private $pages;

    /**
     * @param string $contents
     * @param Pages  $pages
     */
    public function __construct($contents, Pages $pages = null)
    {
        $this->contents = $contents;
        $this->pages = $pages ?: new Pages;
    }

    public function getName()
    {
        return "raw-content";
    }

    public function getStreamReader()
    {
        return StreamReader::createByString($this->contents);
    }

    public function getPages()
    {
        return $this->pages;
    }
}
