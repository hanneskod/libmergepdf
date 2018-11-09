<?php

declare(strict_types = 1);

namespace iio\libmergepdf\Driver;

use iio\libmergepdf\Source\SourceInterface;
use iio\libmergepdf\Pages;
use iio\libmergepdf\Exception;

class TcpdiDriver implements DriverInterface
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
        /** @var string Name of source being processed */
        $name = '';

        try {
            $tcpdi = clone $this->tcpdi;

            foreach ($sources as $source) {
                $name = $source->getName();
                $pageCount = $tcpdi->setSourceData($source->getContents());
                $pageNumbers = $source->getPages()->getPageNumbers() ?: range(1, $pageCount);

                foreach ($pageNumbers as $pageNr) {
                    $template = $tcpdi->importPage($pageNr);
                    $size = $tcpdi->getTemplateSize($template);
                    $tcpdi->AddPage(
                        $size['w'] > $size['h'] ? 'L' : 'P',
                        [$size['w'], $size['h']]
                    );
                    $tcpdi->useTemplate($template);
                }
            }

            return $tcpdi->Output('', 'S');
        } catch (\Exception $e) {
            throw new Exception("'{$e->getMessage()}' in '{$name}'", 0, $e);
        }
    }
}
