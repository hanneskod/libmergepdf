libmergepdf
===========

PHP library for merging multiple PDFs

Usage:

    $m = new \itbz\libmergepdf\Merger();
    $m->addFromFile('foo.pdf');
    $m->addFromFile('bar.pdf', new \itbz\libmergepdf\Pages('1-10'));
    file_put_contents('foobar.pdf', $m->merge());

Append the first ten pages of *bar.pdf* to *foo.pdf*.
