<?php

declare(strict_types = 1);

namespace iio\libmergepdf\Source;

use iio\libmergepdf\PagesInterface;
use iio\libmergepdf\Pages;
use iio\libmergepdf\Exception;

/**
 * Pdf source from file
 */
final class FileSource implements SourceInterface
{
    /**
     * @var string
     */
    private $filename;

    /**
     * @var PagesInterface
     */
    private $pages;

    /**
     * @var bool
     */
    private $duplex;

    public function __construct(string $filename, PagesInterface $pages = null, bool $duplex = false)
    {
        if (!is_file($filename) || !is_readable($filename)) {
            throw new Exception("Invalid file '$filename'");
        }

        $this->filename = $filename;
        $this->pages = $pages ?: new Pages;
        $this->duplex = $duplex;
    }

    public function getName(): string
    {
        return $this->filename;
    }

    public function getContents(): string
    {
        return (string)file_get_contents($this->filename);
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
