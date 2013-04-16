# pulsar [![Build Status](https://secure.travis-ci.org/ehough/pulsar.png)](http://travis-ci.org/ehough/pulsar)

Fork of [Symfony's ClassLoader component](https://github.com/symfony/ClassLoader) compatible with PHP 5.2 and above.

`pulsar` also includes a novel class for Composer-based projects, `ehough_pulsar_ComposerClassLoader`, which features:

* Includes all functionality of the [UniversalClassLoader](https://github.com/symfony/ClassLoader/blob/master/UniversalClassLoader.php)
* Recognition and proper handling of [Composer autoloading](http://getcomposer.org/doc/01-basic-usage.md#autoloading)
* Use of APC or XCache, if available, to cache class lookups

Simple to create and use:

```php
$classLoader = new ehough_pulsar_ComposerClassloader('/path/to/your/vendor/directory');
```