<?php

namespace kadudutra\libmergepdf\Driver;

use kadudutra\libmergepdf\Source\SourceInterface;

interface DriverInterface
{
    /**
     * Merge multiple sources
     */
    public function merge(SourceInterface ...$sources): string;
}
