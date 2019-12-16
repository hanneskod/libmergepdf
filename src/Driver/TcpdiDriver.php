<?php

declare(strict_types = 1);

namespace iio\libmergepdf\Driver;

use iio\libmergepdf\Exception;
use iio\libmergepdf\Source\SourceInterface;

final class TcpdiDriver implements DriverInterface
{
    /**
     * @var \TCPDI
     */
    private $tcpdi;

    public function __construct(\TCPDI $tcpdi = null)
    {
        $this->tcpdi = $tcpdi ?: new \TCPDI;
    }

    public function merge(SourceInterface ...$sources): string
    {
        $sourceName = '';

        try {
            $tcpdi = clone $this->tcpdi;

            foreach ($sources as $source) {
                $sourceName = $source->getName();
                $pageCount = $tcpdi->setSourceData($source->getContents());
                $pageNumbers = $source->getPages()->getPageNumbers() ?: range(1, $pageCount);

                foreach ($pageNumbers as $pageNr) {
                    $template = $tcpdi->importPage($pageNr);
                    $size = $tcpdi->getTemplateSize($template);
                    $tcpdi->SetPrintHeader(false);
                    $tcpdi->SetPrintFooter(false);
                    $tcpdi->AddPage(
                        $size['w'] > $size['h'] ? 'L' : 'P',
                        [$size['w'], $size['h']]
                    );
                    $tcpdi->useTemplate($template);
                }
            }

            return $tcpdi->Output('', 'S');
        } catch (\Exception $e) {
            throw new Exception("'{$e->getMessage()}' in '$sourceName'", 0, $e);
        }
    }
}
