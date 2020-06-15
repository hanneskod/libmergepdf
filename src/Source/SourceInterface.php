<?php

declare(strict_types = 1);

namespace kadudutra\libmergepdf\Source;

use kadudutra\libmergepdf\PagesInterface;

interface SourceInterface
{
    /**
     * Get name of file or source
     */
    public function getName(): string;

    /**
     * Get pdf content
     */
    public function getContents(): string;

    /**
     * Get pages to fetch from source
     */
    public function getPages(): PagesInterface;
}
