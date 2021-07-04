<?php

namespace iio\libmergepdf\Driver;

use iio\libmergepdf\Source\SourceInterface;

interface DriverInterface
{
    /**
     * Merge multiple sources
     */
    public function merge(SourceInterface ...$sources): string;

    /**
     * Get the Sources that didn't merge
     */
    public function getFailedSources(): array;
}
