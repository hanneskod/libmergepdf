<?php

declare(strict_types = 1);

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Behat\Tester\Exception\PendingException;
use iio\libmergepdf\Merger;
use iio\libmergepdf\Pages;
use setasign\Fpdi\Tcpdf\Fpdi;
use setasign\Fpdi\PdfParser\StreamReader;

final class FeatureContext implements Context
{
    /**
     * @var Merger
     */
    private $merger;

    /**
     * @var string
     */
    private $generatedPdf;

    /**
     * @var Fpdi
     */
    private $fpdi;

    public function __construct()
    {
        $this->fpdi = new Fpdi;
    }

    /**
     * @Given the :driver driver
     */
    public function theDriver(string $driver)
    {
        $driverClass = "iio\libmergepdf\Driver\\$driver";
        $this->merger = new Merger(new $driverClass);
    }

    /**
     * @Given a pdf
     */
    public function aPdf()
    {
        $this->aPdfOfVersion('1.4');
    }

    /**
     * @Given a pdf with pages :pages
     */
    public function aPdfWithPages($pages)
    {
        $this->aPdfOfVersionWithPages('1.4', $pages);
    }

    /**
     * @Given a pdf of version :version
     */
    public function aPdfOfVersion(string $version)
    {
        $this->aPdfOfVersionWithPages($version, '');
    }

    /**
     * @Given a pdf of version :version with pages :pages
     */
    public function aPdfOfVersionWithPages(string $version, string $pages)
    {
        $this->merger->addFile(__DIR__ . "/../files/$version.pdf", new Pages($pages));
    }

    /**
     * @When I merge
     */
    public function iMerge()
    {
        $this->generatedPdf = $this->merger->merge();
    }

    /**
     * @Then a pdf is generated
     */
    public function aPdfIsGenerated()
    {
        $this->fpdi->setSourceFile(StreamReader::createByString($this->generatedPdf));
    }

    /**
     * @Then a pdf with :expectedCount pages is generated
     */
    public function aPdfWithPagesIsGenerated(string $expectedCount)
    {
        $pageCount = $this->fpdi->setSourceFile(StreamReader::createByString($this->generatedPdf));
        if ($pageCount != $expectedCount) {
            throw new Exception("A pdf with $pageCount pages was created, expected $expectedCount pages.");
        }
    }
}
