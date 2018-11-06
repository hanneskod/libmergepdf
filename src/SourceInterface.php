<?php

namespace iio\libmergepdf;

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
     * Get pages to fetch from source
     *
     * @return Pages
     */
    public function getPages();
}
