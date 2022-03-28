<?php

declare(strict_types=1);

namespace nabilanam\libmergepdf;

use nabilanam\libmergepdf\Driver\DriverInterface;
use nabilanam\libmergepdf\Driver\DefaultDriver;
use nabilanam\libmergepdf\Source\SourceInterface;
use nabilanam\libmergepdf\Source\FileSource;
use nabilanam\libmergepdf\Source\RawSource;
use setasign\Fpdi\Tcpdf\Fpdi;

class PageNumber extends Fpdi
{
    private $totalPage;

    public function setTotalPage($totalPage)
    {
        $this->totalPage = $totalPage;
    }

    public function Footer()
    {
        $this->SetY(-15);
        $this->Cell(0, 0, 'Page ' . $this->PageNo() . "of {$this->totalPage}", 0, 0, 'C');
    }
}
