<?php

declare(strict_types = 1);

namespace iio\libmergepdf\Source;

use iio\libmergepdf\PagesInterface;
use iio\libmergepdf\Pages;

/**
 * Pdf source from raw string
 */
final class RawSource implements SourceInterface
{
    /**
     * @var string
     */
    private $contents;

    /**
     * @var PagesInterface
     */
    private $pages;

    /**
     * @var bool
     */
    private $duplex;

    public function __construct(string $contents, PagesInterface $pages = null, bool $duplex = false)
    {
        $this->contents = $contents;
        $this->pages = $pages ?: new Pages;
        $this->duplex = $duplex;
    }

    public function getName(): string
    {
        return "raw-content";
    }

    public function getContents(): string
    {
        return $this->contents;
    }

    public function getPages(): PagesInterface
    {
        return $this->pages;
    }

    public function getDuplex(): bool
    {
        return $this->duplex;
    }
}
