<?php

declare(strict_types = 1);

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Behat\Tester\Exception\PendingException;
use kadudutra\libmergepdf\Merger;
use kadudutra\libmergepdf\Pages;
use Smalot\PdfParser\Parser as PdfParser;

final class FeatureContext implements Context
{
    /** @var Merger */
    private $merger;

    /** @var string */
    private $generatedPdf = '';

    /** @var ?\Exception */
    private $mergeException;

    public function __construct(string $driverName)
    {
        $driverClass = "kadudutra\libmergepdf\Driver\\$driverName";

        /** @var \kadudutra\libmergepdf\Driver\DriverInterface $driver */
        $driver = new $driverClass;

        $this->merger = new Merger($driver);
    }

    /**
     * @Given the :driver driver
     */
    public function theDriver(string $driver)
    {
        $driverClass = "kadudutra\libmergepdf\Driver\\$driver";
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
     * @Given a pdf with a header including text HEADER
     */
    public function aPdfWithAHeaderIncludingTextHeader()
    {
        $this->merger->addFile(__DIR__ . "/../files/header.pdf");
    }

    /**
     * @Given a blank pdf
     */
    public function aBlankPdf()
    {
        $this->merger->addFile(__DIR__ . "/../files/blank.pdf");
    }

    /**
     * @When I merge
     */
    public function iMerge()
    {
        try {
            $this->generatedPdf = $this->merger->merge();
            $this->mergeException = null;
        } catch (\Exception $e) {
            $this->mergeException = $e;
        }
    }

    /**
     * @Then there is no error
     */
    public function thereIsNoError()
    {
        if ($this->mergeException) {
            throw $this->mergeException;
        }
    }

    /**
     * @Then there is an error
     */
    public function thereIsAnError()
    {
        if (!$this->mergeException) {
            throw new \Exception('Expecting error during merge');
        }
    }

    /**
     * @Then a pdf is generated
     */
    public function aPdfIsGenerated()
    {
        $this->thereIsNoError();
        (new PdfParser)->parseContent($this->generatedPdf);
    }

    /**
     * @Then a pdf with :expectedCount pages is generated
     */
    public function aPdfWithPagesIsGenerated(string $expectedCount)
    {
        $this->thereIsNoError();

        $pageCount = @count((new PdfParser)->parseContent($this->generatedPdf)->getPages());

        if ($pageCount != $expectedCount) {
            throw new Exception("A pdf with $pageCount pages was created, expected $expectedCount pages.");
        }
    }

    /**
     * @Then a pdf including text :expectedText is generated
     */
    public function aPdfIncludingTextIsGenerated(string $expectedText)
    {
        $this->thereIsNoError();

        $text = @(new PdfParser)->parseContent($this->generatedPdf)->getText();

        $regexp = preg_quote($expectedText, '/');

        if (!preg_match("/$regexp/", $text)) {
            throw new Exception("A pdf with text '$text' was created, expected '$expectedText'.");
        }
    }

    /**
     * @Then a pdf not including text :unexpectedText is generated
     */
    public function aPdfNotIncludingTextIsGenerated(string $unexpectedText)
    {
        $this->thereIsNoError();

        $text = @(new PdfParser)->parseContent($this->generatedPdf)->getText();

        $regexp = preg_quote($unexpectedText, '/');

        if (preg_match("/$regexp/", $text)) {
            throw new Exception("A pdf with unexpected text '$unexpectedText' was created.");
        }
    }
}
