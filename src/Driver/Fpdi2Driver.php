<?php

declare(strict_types=1);

namespace nabilanam\libmergepdf\Driver;

use nabilanam\libmergepdf\Exception;
use nabilanam\libmergepdf\Source\SourceInterface;
use setasign\Fpdi\Fpdi as FpdiFpdf;
use setasign\Fpdi\Tcpdf\Fpdi as FpdiTcpdf;
use setasign\Fpdi\PdfParser\StreamReader;

final class Fpdi2Driver implements DriverInterface
{
    /**
     * @var FpdiFpdf|FpdiTcpdf
     */
    private $fpdi;

    private $pageCounts = [];

    /**
     * @param FpdiFpdf|FpdiTcpdf $fpdi
     */
    public function __construct($fpdi = null)
    {
        // Tcpdf generates warnings due to argument ordering with php 8
        // suppressing errors is a dirty hack until tcpdf is patched
        $this->fpdi = $fpdi ?: @new FpdiTcpdf;

        if (!($this->fpdi instanceof FpdiFpdf) && !($this->fpdi instanceof FpdiTcpdf)) {
            throw new \InvalidArgumentException('Constructor argument must be an FPDI instance.');
        }
    }

    public function merge(SourceInterface ...$sources): string
    {
        $sourceName = '';

        try {
            $fpdi = clone $this->fpdi;

            foreach ($sources as $source) {
                $sourceName = $source->getName();
                $pageCount = $fpdi->setSourceFile(StreamReader::createByString($source->getContents()));
                $pageNumbers = $source->getPages()->getPageNumbers() ?: range(1, $pageCount);
                $this->pageCounts[$sourceName] = count($pageNumbers);

                foreach ($pageNumbers as $pageNr) {
                    $template = $fpdi->importPage($pageNr);
                    $size = $fpdi->getTemplateSize($template);
                    $fpdi->SetPrintHeader(false);
                    $fpdi->SetPrintFooter(false);
                    $fpdi->AddPage(
                        $size['width'] > $size['height'] ? 'L' : 'P',
                        [$size['width'], $size['height']]
                    );
                    $fpdi->useTemplate($template);
                }
            }

            return $fpdi->Output('', 'S');
        } catch (\Exception $e) {
            throw new Exception("'{$e->getMessage()}' in '$sourceName'", 0, $e);
        }
    }

    public function getPageCounts(): array
    {
        return $this->pageCounts;
    }
}
