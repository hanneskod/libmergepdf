<?php

declare(strict_types = 1);

namespace iio\libmergepdf;

interface PagesInterface extends \IteratorAggregate
{
    /**
     * Get iterator of page numbers
     *
     * @return iterable & int[]
     */
    public function getIterator(): iterable;

    /**
     * Check if this collection is empty
     */
    public function isEmpty(): bool;
}
