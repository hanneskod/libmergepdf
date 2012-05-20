libmergepdf
===========

PHP library for merging multiple PDFs


##Installation

libmergepdf depends och FPDI and FPDF. Dependecies are handled with composer.
To install cd into libmergepdf root dir. To install dependencies and rund tests
using Phing:

    $ phing test

Or to run more analysis simple type:

    $ phing

To run tests without Phing:

    $ curl -s http://getcomposer.org/installer | php
    $ php composer.phar install
    $ phpunit --bootstrap vendor/autoload.php tests


##Installation using composer

Simply add itbz/libmergepdf to your list of required libraries


##Usage

Append the first ten pages of *bar.pdf* to *foo.pdf*:

    $m = new \itbz\libmergepdf\Merger();
    $m->addFromFile('foo.pdf');
    $m->addFromFile('bar.pdf', new \itbz\libmergepdf\Pages('1-10'));
    file_put_contents('foobar.pdf', $m->merge());
