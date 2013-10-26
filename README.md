libmergepdf
===========

PHP library for merging multiple PDFs using fpdi/FPDI.


##Installation using composer

Simply add `iio/libmergepdf` to your list of required libraries.


##Usage

Append the first ten pages of *bar.pdf* to *foo.pdf*:

    $m = new \iio\libmergepdf\Merger(new \fpdi\FPDI);
    $m->addFromFile('foo.pdf');
    $m->addFromFile('bar.pdf', new \iio\libmergepdf\Pages('1-10'));
    file_put_contents('foobar.pdf', $m->merge());


##Run tests

Execute unit tests by typing `phpunit`. The unis tests requires FPDI to be
installed using composer.

	$ curl -sS https://getcomposer.org/installer | php
    $ php composer.phar install
    $ phpunit


#CI

Installing dependencies, running tests and other code analysis tools can be
handled using `phing`. Type

    $ phing

to run CI tests. Then point your browser to `build/index.html`. For more
information try

    $ phing help


##Changelog

2.1.1 Now allows merging of Landscape and Portrait pages (thanks to willoller)

2.0 As of version 2.0 FPDI must be injected when creating a new libmergepdf
instance.
