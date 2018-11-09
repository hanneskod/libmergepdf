<?php

namespace iio\libmergepdf;

interface PagesInterface
{
    /**
     * @return int[]
     */
    public function getPageNumbers(): array;
}
