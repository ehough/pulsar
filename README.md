pulsar [![Build Status](https://secure.travis-ci.org/ehough/pulsar.png)](http://travis-ci.org/ehough/pulsar)
======

Intelligent classloaders PHP 5. This library is a fork of [Symfony's ClassLoader component](https://github.com/symfony/ClassLoader),
the primary difference being that `pulsar` is compatible with PHP 5.1.3 and above.

`pulsar` also includes a novel class for Composer-based projects, `ehough_pulsar_ComposerClassLoader`, which features:

* Includes all functionality of the [UniversalClassLoader](https://github.com/symfony/ClassLoader/blob/master/UniversalClassLoader.php)
* Recognition and proper handling of [Composer autoloading](http://getcomposer.org/doc/01-basic-usage.md#autoloading)
* Use of APC or XCache, if available, to cache class lookups

Simple to create and use:

    $classLoader = new ehough_pulsar_ComposerClassloader('/path/to/your/vendor/directory');