pulsar [![Build Status](https://secure.travis-ci.org/ehough/pulsar.png)](http://travis-ci.org/ehough/pulsar)
======

Intelligent classloaders PHP 5. The bulk of this library is a fork of [Symfony's ClassLoader component](https://github.com/symfony/ClassLoader)
the primary difference being that `pulsar` is compatible with PHP 5.1.3 and above.

`pulsar` loads your project classes automatically if they follow some
standard PHP conventions.

`pulsar` is able to autoload classes that implement the PSR-0
standard or the PEAR naming convention.

First, register the autoloader:

    require_once __DIR__ . '/src/main/php/ehough/pulsar/ComposerClassLoader.php';

    $loader = new ehough_pulsar_ComposerClassLoader();
    $loader->register();

Then, register some namespaces with the `registerDirectory()` method:

    $loader->registerDirectory('Symfony', __DIR__ . '/src');
    $loader->registerDirectory('Monolog', __DIR__ . '/vendor/monolog/src');

The `registerDirectory()` method takes a namespace prefix and a path where to
look for the classes as arguments.

You can also register a sub-namespaces:

    $loader->registerDirectory('Doctrine\\Common', __DIR__.'/vendor/doctrine-common/lib');

The order of registration is significant and the first registered namespace
takes precedence over later registered one.

You can also register more than one path for a given namespace:

    $loader->registerDirectory('Symfony', array(__DIR__.'/src', __DIR__.'/symfony/src'));

Alternatively, you can use the `registerDirectories()` method to register more
than one namespace at once:

    $loader->registerDirectories(array(
        'Symfony'          => array(__DIR__.'/src', __DIR__.'/symfony/src'),
        'Doctrine\\Common' => __DIR__.'/vendor/doctrine-common/lib',
        'Doctrine'         => __DIR__.'/vendor/doctrine/lib',
        'Monolog'          => __DIR__.'/vendor/monolog/src',
    ));

For better performance, you can use the APC based version of the universal
class loader:

    require_once __DIR__.'/src/Symfony/Component/ClassLoader/UniversalClassLoader.php';
    require_once __DIR__.'/src/Symfony/Component/ClassLoader/ApcUniversalClassLoader.php';

    $loader = new ApcUniversalClassLoader('apc.prefix.');

Furthermore, the component provides tools to aggregate classes into a single
file, which is especially useful to improve performance on servers that do not
provide byte caches.

Resources
---------

You can run the unit tests with the following command:

    $ composer.phar install --dev
    $ phpunit