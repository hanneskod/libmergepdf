# libmergepdf

[![Packagist Version](https://img.shields.io/packagist/v/kadudutra/libmergepdf.svg?style=flat-square)](https://packagist.org/packages/kadudutra/libmergepdf)
[![Build Status](https://img.shields.io/travis/hanneskod/libmergepdf/master.svg?style=flat-square)](https://travis-ci.org/hanneskod/libmergepdf)
[![Quality Score](https://img.shields.io/scrutinizer/g/hanneskod/libmergepdf.svg?style=flat-square)](https://scrutinizer-ci.com/g/hanneskod/libmergepdf)

PHP library for merging multiple PDFs.

## Installation

```shell
composer require kadudutra/libmergepdf
```

## Usage

Append the first ten pages of **bar.pdf** to **foo.pdf**:

```php
use kadudutra\libmergepdf\Merger;
use kadudutra\libmergepdf\Pages;

$merger = new Merger;
$merger->addFile('foo.pdf');
$merger->addFile('bar.pdf', new Pages('1-10'));
$createdPdf = $merger->merge();
```

Bulk add files from an iterator:

```php
use kadudutra\libmergepdf\Merger;

$merger = new Merger;
$merger->addIterator(['A.pdf', 'B.pdf']);
$createdPdf = $merger->merge();
```

### Merging pdfs of version 1.5 and later

The default `FPDI` driver is not able handle compressed pdfs of version 1.5 or later.
Circumvent this limitation by using the slightly more experimental `TCPDI` driver.

```php
use kadudutra\libmergepdf\Merger;
use kadudutra\libmergepdf\Driver\TcpdiDriver;

$merger = new Merger(new TcpdiDriver);
```

### Using an immutable merger

Immutability may be achieved by using a `driver` directly.

```php
use kadudutra\libmergepdf\Driver\Fpdi2Driver;
use kadudutra\libmergepdf\Source\FileSource;
use kadudutra\libmergepdf\Pages;

$merger = new Fpdi2Driver;

$createdPdf = $merger->merge(
    new FileSource('foo.pdf'),
    new FileSource('bar.pdf', new Pages('1-10'))
);
```

## Known issues

* Links and other content outside a page content stream is removed at merge.
  This is due to limitations in FPDI and not possible to resolve with the
  current strategy. For more information see [FPDI](https://www.setasign.com/support/faq/fpdi/after-importing-a-page-all-links-are-gone/#question-84).
