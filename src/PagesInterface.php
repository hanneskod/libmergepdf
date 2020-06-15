<?php

namespace kadudutra\libmergepdf;

interface PagesInterface
{
    /**
     * @return int[]
     */
    public function getPageNumbers(): array;
}
