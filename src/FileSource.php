<?php

declare(strict_types = 1);

namespace iio\libmergepdf;

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

    public function __construct(string $filename, Pages $pages = null)
    {
        $this->filename = $filename;
        $this->pages = $pages ?: new Pages;
    }

    public function getName(): string
    {
        return $this->filename;
    }

    public function getContents(): string
    {
        return file_get_contents($this->filename);
    }

    public function getPages(): Pages
    {
        return $this->pages;
    }
}
