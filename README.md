libmergepdf
===========

[![Build Status](https://travis-ci.org/iio/libmergepdf.png?branch=master)](https://travis-ci.org/iio/libmergepdf)

PHP library for merging multiple PDFs using [fpdi/FPDI](https://github.com/iio/fpdi).


##Installation using composer

Simply add `iio/libmergepdf` to your list of required libraries.


##Usage

Append the first ten pages of *bar.pdf* to *foo.pdf*:

	use iio\libmergepdf\Merger;
	use iio\libmergepdf\Pages;

    $m = new Merger();
    $m->addFromFile('foo.pdf');
    $m->addFromFile('bar.pdf', new Pages('1-10'));
    file_put_contents('foobar.pdf', $m->merge());

Bulk add files from an iterator:

    use iio\libmergepdf\Merger;
    $m = new Merger();
    $m->addIterator(array('A.pdf', 'B.pdf'));
    file_put_contents('AB.pdf', $m->merge());

Bulk add files using [symfony finder](http://symfony.com/doc/current/components/finder.html):

    use iio\libmergepdf\Merger;
    use Symfony\Component\Finder\Finder;

    $finder = new Finder();
    $finder->files()->in(__DIR__)->name('*.pdf')->sortByName();

    $m = new Merger();
    $m->addFinder($finder);

    file_put_contents('finder.pdf', $m->merge());


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

2.3.1 Added merger->addFinder().

2.3.0 Injecting FPDI is now optional. Added merger->addIterator().

2.2.0 Pages now support addPage() and addRange().

2.1.1 Now allows merging of Landscape and Portrait pages (thanks to @willoller).

2.0 As of version 2.0 FPDI must be injected when creating a new libmergepdf
instance.
