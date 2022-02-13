<?php

namespace nabilanam\libmergepdf\Driver;

use nabilanam\libmergepdf\Source\SourceInterface;

interface DriverInterface
{
    /**
     * Merge multiple sources
     */
    public function merge(SourceInterface ...$sources): string;


    public function getPageCounts(): array;
}
