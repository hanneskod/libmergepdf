# libmergepdf

[![Packagist Version](https://img.shields.io/packagist/v/iio/libmergepdf.svg?style=flat-square)](https://packagist.org/packages/iio/libmergepdf)
[![Build Status](https://img.shields.io/travis/hanneskod/libmergepdf/master.svg?style=flat-square)](https://travis-ci.org/hanneskod/libmergepdf)
[![Quality Score](https://img.shields.io/scrutinizer/g/hanneskod/libmergepdf.svg?style=flat-square)](https://scrutinizer-ci.com/g/hanneskod/libmergepdf)
[![Dependency Status](https://img.shields.io/gemnasium/hanneskod/libmergepdf.svg?style=flat-square)](https://gemnasium.com/hanneskod/libmergepdf)

PHP library for merging multiple PDFs using [FPDI](https://github.com/Setasign/FPDI)

Installation
------------
Install using [composer](http://getcomposer.org/).

```shell
composer require iio/libmergepdf:^3.1
```

Usage
-----
Append the first ten pages of *bar.pdf* to *foo.pdf*:

```php
use iio\libmergepdf\Merger;
use iio\libmergepdf\Pages;

$merger = new Merger;
$merger->addFile('foo.pdf');
$merger->addFile('bar.pdf', new Pages('1-10'));
$createdPdf = $merger->merge();
```

Bulk add files from an iterator:

```php
use iio\libmergepdf\Merger;
$merger = new Merger;
$merger->addIterator(['A.pdf', 'B.pdf']);
$createdPdf = $merger->merge();
```

Bulk add files using [symfony finder](http://symfony.com/doc/current/components/finder.html):

```php
use iio\libmergepdf\Merger;
use Symfony\Component\Finder\Finder;

$finder = new Finder;
$finder->files()->in(__DIR__)->name('*.pdf')->sortByName();

$merger = new Merger;
$merger->addFinder($finder);

$createdPdf = $merger->merge();
```

Known issues
------------
* Links and other content outside a page content stream is removed at merge.
  This is due to limitations in FPDI and not possible to resolve with the
  current strategy. For more information see [FPDI](https://www.setasign.com/support/faq/fpdi/after-importing-a-page-all-links-are-gone/#question-84).

Testing
-------
Unit tests requires dependencies to be installed using composer:

```shell
composer install
vendor/bin/phpunit
```

Credits
-------
libmergepdf is covered under the [WTFPL](http://www.wtfpl.net/).

@author Hannes Forsgård (hannes.forsgard@fripost.org)
