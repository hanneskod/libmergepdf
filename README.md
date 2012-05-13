libmergepdf
===========

PHP library for merging multiple PDFs


##Installation

libmergepdf depends och FPDI and FPDF. Dependecies are handled with composer.
To install cd into libmergepdf root dir. Get composer:

    $ curl -s http://getcomposer.org/installer | php

Install dependencies:

    $ php composer.phar install
    
If you have phpunit installed you can run unittests to check that all is ok

    $ phpunit --bootstrap tests/bootstrap.php tests/libmergepdf/


##Installation using composer

Simply add itbz/libmergepdf to your list of required libraries


##Usage

Append the first ten pages of *bar.pdf* to *foo.pdf*:

    $m = new \itbz\libmergepdf\Merger();
    $m->addFromFile('foo.pdf');
    $m->addFromFile('bar.pdf', new \itbz\libmergepdf\Pages('1-10'));
    file_put_contents('foobar.pdf', $m->merge());
