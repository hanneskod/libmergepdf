# libmergepdf

[![Packagist Version](https://img.shields.io/packagist/v/iio/libmergepdf.svg?style=flat-square)](https://packagist.org/packages/iio/libmergepdf)
[![Build Status](https://img.shields.io/travis/hanneskod/libmergepdf/master.svg?style=flat-square)](https://travis-ci.org/hanneskod/libmergepdf)
[![Quality Score](https://img.shields.io/scrutinizer/g/hanneskod/libmergepdf.svg?style=flat-square)](https://scrutinizer-ci.com/g/hanneskod/libmergepdf)
[![Dependency Status](https://img.shields.io/gemnasium/hanneskod/libmergepdf.svg?style=flat-square)](https://gemnasium.com/hanneskod/libmergepdf)

PHP library for merging multiple PDFs using [FPDI](https://github.com/Setasign/FPDI)

Installation
------------
Install using [composer](http://getcomposer.org/). Exists as
[iio/libmergepdf](https://packagist.org/packages/iio/libmergepdf)
in the [packagist](https://packagist.org/) repository.

    composer require iio/libmergepdf:~3.0

Usage
-----
Append the first ten pages of *bar.pdf* to *foo.pdf*:

```php
use iio\libmergepdf\Merger;
use iio\libmergepdf\Pages;

$m = new Merger();
$m->addFromFile('foo.pdf');
$m->addFromFile('bar.pdf', new Pages('1-10'));
file_put_contents('foobar.pdf', $m->merge());
```

Bulk add files from an iterator:

```php
use iio\libmergepdf\Merger;
$m = new Merger();
$m->addIterator(array('A.pdf', 'B.pdf'));
file_put_contents('AB.pdf', $m->merge());
```

Bulk add files using [symfony finder](http://symfony.com/doc/current/components/finder.html):

```php
use iio\libmergepdf\Merger;
use Symfony\Component\Finder\Finder;

$finder = new Finder();
$finder->files()->in(__DIR__)->name('*.pdf')->sortByName();

$m = new Merger();
$m->addFinder($finder);

file_put_contents('finder.pdf', $m->merge());
```

Testing
-------
Unit tests requires dependencies to be installed using composer:

    $ curl -sS https://getcomposer.org/installer | php
    $ php composer.phar install
    $ phpunit

Changelog
---------
* 3.0 Now using the official FPDI package
* 2.4.0 Added `setTempDir()` to Merger.
* 2.3.1 Added `addFinder()` to Merger.
* 2.3.0 Injecting FPDI is now optional. Added `addIterator()` to Merger.
* 2.2.0 Pages now support `addPage()` and `addRange()`.
* 2.1.1 Now allows merging of Landscape and Portrait pages (thanks to @willoller).
* 2.0 As of version 2.0 FPDI must be injected when creating a new libmergepdf instance.

Credits
-------
libmergepdf is covered under the [WTFPL](http://www.wtfpl.net/).

@author Hannes Forsgård (hannes.forsgard@fripost.org)
