wkhtmltopdf-osx
================

This repository contains the static compiled binaries from the [wkhtmltopdf project](http://wkhtmltopdf.org/).
More about the functionality of wkhtmltopdf and wkthmltoimage can be found there.

Binaries for __Linux i386__, also installable with composer, can be found here: [https://github.com/h4cc/wkhtmltopdf-i386](https://github.com/h4cc/wkhtmltopdf-i386)

Binaries for __Linux AMD64__, also installable with composer, can be found here: [https://github.com/h4cc/wkhtmltopdf-amd64](https://github.com/h4cc/wkhtmltopdf-amd64)

Binaries for __Microsoft Windows__, also installable with composer, can be found here: [github.com/wemersonjanuario/wkhtmltopdf-windows](https://github.com/wemersonjanuario/wkhtmltopdf-windows)

Binaries for __CentOS 7__, also installable with composer, can be found here: [github.com/rvanlaak/wkhtmltopdf-amd64-centos7](https://github.com/rvanlaak/wkhtmltopdf-amd64-centos7)

## Installation

_Hint_:
The version of the binary is equal to the git tag.
To install the latest version, use '0.12.4'.

### Packagist

This package can be found on [Packagist](http://packagist.org) and installed with [Composer](https://getcomposer.org/).

Require the package for _macos_ with:

    php composer.phar require oleg-andreyev/wkhtmltopdf-osx "0.12.5"

The binary will then be located at:

    vendor/oleg-andreyev/wkhtmltopdf-osx/bin/wkhtmltopdf-amd64
    vendor/oleg-andreyev/wkhtmltopdf-osx/bin/wkhtmltopdf-i386

Also a symlink will be created in your configured bin/ folder, for example:

    vendor/bin/wkhtmltopdf-amd64
    vendor/bin/wkhtmltopdf-i386

### Usage

You can use the path constant to easily locate the binary in the PHP codebase: 

``` php
$path = \OAndreyev\WKHTMLToPDF\WKHTMLToPDF::PATH_AMD64;
```

For realpath use following script

``` php
$realpath = realpath(\OAndreyev\WKHTMLToPDF\WKHTMLToPDF::PATH_AMD64);
```
