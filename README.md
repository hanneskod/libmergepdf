libmergepdf
===========

PHP library for merging multiple PDFs


##Installation

libmergepdf depends on FPDI. Dependecies are handled with composer.
To install cd into libmergepdf root dir and type:

    $ curl -s http://getcomposer.org/installer | php
    $ php composer.phar install


To install dependencies and rund tests using Phing:

    $ phing test

Or to run more analysis simple type:

    $ phing


##Installation using composer

Simply add iio/libmergepdf to your list of required libraries


##Usage

Append the first ten pages of *bar.pdf* to *foo.pdf*:

    $m = new \iio\libmergepdf\Merger(new \fpdi\FPDI);
    $m->addFromFile('foo.pdf');
    $m->addFromFile('bar.pdf', new \iio\libmergepdf\Pages('1-10'));
    file_put_contents('foobar.pdf', $m->merge());


##Changelog

2.1.1 Now allows merging of Landscape and Portrait pages (thanks to willoller)

2.0 As of version 2.0 FPDI must be injected when creating a new libmergepdf
instance.
