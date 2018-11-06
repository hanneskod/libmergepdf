<?php

declare(strict_types = 1);

namespace iio\libmergepdf;

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

    public function __construct(string $contents, Pages $pages = null)
    {
        $this->contents = $contents;
        $this->pages = $pages ?: new Pages;
    }

    public function getName(): string
    {
        return "raw-content";
    }

    public function getContents(): string
    {
        return $this->contents;
    }

    public function getPages(): Pages
    {
        return $this->pages;
    }
}
