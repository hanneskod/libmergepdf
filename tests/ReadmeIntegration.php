<?php
namespace hanneskod\libmergepdf;

class ReadmeIntegration extends \PHPUnit_Framework_TestCase
{
    public function testReadmeIntegrationTests()
    {
        if (!class_exists('hanneskod\readmetester\PHPUnit\AssertReadme')) {
            $this->markTestSkipped('Readme-tester is not available.');
        }

        $asserter = new \hanneskod\readmetester\PHPUnit\AssertReadme($this);
        $asserter->assertReadme('README.md');
    }
}
