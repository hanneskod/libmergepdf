<?php

declare(strict_types = 1);

namespace iio\libmergepdf;

use iio\libmergepdf\Driver\TcpdiDriver;

final class MergerFactory
{
    // TODO det här är väl fint
        // men hur ska vi göra med Merger::__construct då? Ska driver vara optional??

    public static function createMerger(): Merger
    {
        return self::createTcpdiMerger();
    }

    public static function createFpdiMerger(): Merger
    {
        return new Merger(new FpdiDriver);
    }

    public static function createTcpdiMerger(): Merger
    {
        return new Merger(new TcpdiDriver);
    }
}
