## pulsar

[![Build Status](https://secure.travis-ci.org/ehough/pulsar.png)](http://travis-ci.org/ehough/pulsar)
[![Project Status: Unsupported - The project has reached a stable, usable state but the author(s) have ceased all work on it. A new maintainer may be desired.](http://www.repostatus.org/badges/latest/unsupported.svg)](http://www.repostatus.org/#unsupported)
[![Latest Stable Version](https://poser.pugx.org/ehough/pulsar/v/stable)](https://packagist.org/packages/ehough/pulsar)
[![License](https://poser.pugx.org/ehough/pulsar/license)](https://packagist.org/packages/ehough/pulsar)

**This library is no longer supported or maintained as PHP 5.2 usage levels have finally dropped below 10%**

Fork of [Symfony's ClassLoader component](https://github.com/symfony/ClassLoader) compatible with PHP 5.2+.

### Motivation

[Symfony's ClassLoader component](https://github.com/symfony/ClassLoader) is a fantastic classloading library,
but it's only compatible with PHP 5.3+. While 99% of PHP servers run PHP 5.2 or higher,
12% of all servers are still running PHP 5.2 or lower ([source](http://w3techs.com/technologies/details/pl-php/5/all)).

### Differences from [Symfony's ClassLoader component](https://github.com/symfony/ClassLoader)

The primary difference is naming conventions of Symfony's classes. Instead of the `\Symfony\Component\ClassLoader` namespace
(and sub-namespaces), instead prefix the class names with `ehough_pulsar` and follow the [PEAR
naming convention](http://pear.php.net/manual/en/standards.php).

A few examples of class naming conversions:

    \Symfony\Component\ClassLoader\ApcClassLoader        ----->    ehough_pulsar_ApcClassLoader
    \Symfony\Component\ClassLoader\UniversalClassLoader  ----->    ehough_pulsar_UniversalClassLoader

### How to use

Please see the [Symfony documentation](https://github.com/symfony/ClassLoader) for general use instructions.

### ComposerClassLoader

`pulsar` also includes a novel class for Composer-based projects, `ehough_pulsar_ComposerClassLoader`, which features:

* All functionality of the [UniversalClassLoader](https://github.com/symfony/ClassLoader/blob/master/UniversalClassLoader.php)
* Recognition and proper handling of [Composer autoloading](http://getcomposer.org/doc/01-basic-usage.md#autoloading)

Simple to create and use. Just point it to your `vendor` directory:

```php
$classLoader = new ehough_pulsar_ComposerClassloader('/path/to/your/vendor/directory');
```

### Releases and Versioning

Releases are synchronized with the upstream Symfony repository. e.g. `ehough/pulsar v2.3.1` has merged the code
from `symfony/ClassLoader v2.3.1`.
