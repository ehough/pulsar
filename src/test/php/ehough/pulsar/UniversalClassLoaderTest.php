<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//namespace Symfony\Component\ClassLoader\Tests;

//use Symfony\Component\ClassLoader\UniversalClassLoader;

class ehough_pulsar_UniversalClassLoaderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getLoadClassTests
     */
    public function testLoadClass($className, $testClassName, $message)
    {
        $loader = new ehough_pulsar_UniversalClassLoader();
        $loader->registerNamespace('Namespaced', dirname(__FILE__).DIRECTORY_SEPARATOR.'../../../resources/Fixtures');
        $loader->registerPrefix('Pearlike_', dirname(__FILE__).DIRECTORY_SEPARATOR.'../../../resources/Fixtures');
        $this->assertTrue($loader->loadClass($testClassName));
        $this->assertTrue(class_exists($className), $message);
    }

    public function getLoadClassTests()
    {
        return array(
            array('\\Namespaced\\Foo', 'Namespaced\\Foo',   '->loadClass() loads Namespaced\Foo class'),
            array('\\Pearlike_Foo',    'Pearlike_Foo',      '->loadClass() loads Pearlike_Foo class'),
            array('\\Namespaced\\Bar', '\\Namespaced\\Bar', '->loadClass() loads Namespaced\Bar class with a leading slash'),
            array('\\Pearlike_Bar',    '\\Pearlike_Bar',    '->loadClass() loads Pearlike_Bar class with a leading slash'),
        );
    }

    public function testUseIncludePath()
    {
        $loader = new ehough_pulsar_UniversalClassLoader();
        $this->assertFalse($loader->getUseIncludePath());

        $this->assertNull($loader->findFile('Foo'));

        $includePath = get_include_path();

        $loader->useIncludePath(true);
        $this->assertTrue($loader->getUseIncludePath());

        set_include_path(realpath(dirname(__FILE__).'/../../../resources/Fixtures/includepath') . PATH_SEPARATOR . $includePath);

        $this->assertEquals(realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'../../../resources/Fixtures').DIRECTORY_SEPARATOR.'includepath'.DIRECTORY_SEPARATOR.'Foo.php', $loader->findFile('Foo'));

        set_include_path($includePath);
    }

    public function testGetNamespaces()
    {
        $loader = new ehough_pulsar_UniversalClassLoader();
        $loader->registerNamespace('Foo', dirname(__FILE__).DIRECTORY_SEPARATOR.'../../../resources/Fixtures');
        $loader->registerNamespace('Bar', dirname(__FILE__).DIRECTORY_SEPARATOR.'../../../resources/Fixtures');
        $loader->registerNamespace('Bas', dirname(__FILE__).DIRECTORY_SEPARATOR.'../../../resources/Fixtures');
        $namespaces = $loader->getNamespaces();
        $this->assertArrayHasKey('Foo', $namespaces);
        $this->assertArrayNotHasKey('Foo1', $namespaces);
        $this->assertArrayHasKey('Bar', $namespaces);
        $this->assertArrayHasKey('Bas', $namespaces);
    }

    public function testGetPrefixes()
    {
        $loader = new ehough_pulsar_UniversalClassLoader();
        $loader->registerPrefix('Foo', dirname(__FILE__).DIRECTORY_SEPARATOR.'../../../resources/Fixtures');
        $loader->registerPrefix('Bar', dirname(__FILE__).DIRECTORY_SEPARATOR.'../../../resources/Fixtures');
        $loader->registerPrefix('Bas', dirname(__FILE__).DIRECTORY_SEPARATOR.'../../../resources/Fixtures');
        $prefixes = $loader->getPrefixes();
        $this->assertArrayHasKey('Foo', $prefixes);
        $this->assertArrayNotHasKey('Foo1', $prefixes);
        $this->assertArrayHasKey('Bar', $prefixes);
        $this->assertArrayHasKey('Bas', $prefixes);
    }

    /**
     * @dataProvider getLoadClassFromFallbackTests
     */
    public function testLoadClassFromFallback($className, $testClassName, $message)
    {
        $loader = new ehough_pulsar_UniversalClassLoader();
        $loader->registerNamespace('Namespaced', dirname(__FILE__).DIRECTORY_SEPARATOR.'../../../resources/Fixtures');
        $loader->registerPrefix('Pearlike_', dirname(__FILE__).DIRECTORY_SEPARATOR.'../../../resources/Fixtures');
        $loader->registerNamespaceFallbacks(array(dirname(__FILE__).DIRECTORY_SEPARATOR.'../../../resources/Fixtures/fallback'));
        $loader->registerPrefixFallbacks(array(dirname(__FILE__).DIRECTORY_SEPARATOR.'../../../resources/Fixtures/fallback'));
        $this->assertTrue($loader->loadClass($testClassName));
        $this->assertTrue(class_exists($className), $message);
    }

    public function getLoadClassFromFallbackTests()
    {
        return array(
            array('\\Namespaced\\Baz',    'Namespaced\\Baz',    '->loadClass() loads Namespaced\Baz class'),
            array('\\Pearlike_Baz',       'Pearlike_Baz',       '->loadClass() loads Pearlike_Baz class'),
            array('\\Namespaced\\FooBar', 'Namespaced\\FooBar', '->loadClass() loads Namespaced\Baz class from fallback dir'),
            array('\\Pearlike_FooBar',    'Pearlike_FooBar',    '->loadClass() loads Pearlike_Baz class from fallback dir'),
        );
    }

    public function testRegisterPrefixFallback()
    {
        $loader = new ehough_pulsar_UniversalClassLoader();
        $loader->registerPrefixFallback(dirname(__FILE__).DIRECTORY_SEPARATOR.'Fixtures/fallback');
        $this->assertEquals(array(dirname(__FILE__).DIRECTORY_SEPARATOR.'Fixtures/fallback'), $loader->getPrefixFallbacks());
    }

    public function testRegisterNamespaceFallback()
    {
        $loader = new ehough_pulsar_UniversalClassLoader();
        $loader->registerNamespaceFallback(dirname(__FILE__).DIRECTORY_SEPARATOR.'Fixtures/Namespaced/fallback');
        $this->assertEquals(array(dirname(__FILE__).DIRECTORY_SEPARATOR.'Fixtures/Namespaced/fallback'), $loader->getNamespaceFallbacks());
    }

    /**
     * @dataProvider getLoadClassNamespaceCollisionTests
     */
    public function testLoadClassNamespaceCollision($namespaces, $className, $message)
    {
        $loader = new ehough_pulsar_UniversalClassLoader();
        $loader->registerNamespaces($namespaces);

        $this->assertTrue($loader->loadClass($className));
        $this->assertTrue(class_exists($className), $message);
    }

    public function getLoadClassNamespaceCollisionTests()
    {
        return array(
            array(
                array(
                    'NamespaceCollision\\A' => dirname(__FILE__).DIRECTORY_SEPARATOR.'../../../resources/Fixtures/alpha',
                    'NamespaceCollision\\A\\B' => dirname(__FILE__).DIRECTORY_SEPARATOR.'../../../resources/Fixtures/beta',
                ),
                'NamespaceCollision\A\Foo',
                '->loadClass() loads NamespaceCollision\A\Foo from alpha.',
            ),
            array(
                array(
                    'NamespaceCollision\\A\\B' => dirname(__FILE__).DIRECTORY_SEPARATOR.'../../../resources/Fixtures/beta',
                    'NamespaceCollision\\A' => dirname(__FILE__).DIRECTORY_SEPARATOR.'../../../resources/Fixtures/alpha',
                ),
                'NamespaceCollision\A\Bar',
                '->loadClass() loads NamespaceCollision\A\Bar from alpha.',
            ),
            array(
                array(
                    'NamespaceCollision\\A' => dirname(__FILE__).DIRECTORY_SEPARATOR.'../../../resources/Fixtures/alpha',
                    'NamespaceCollision\\A\\B' => dirname(__FILE__).DIRECTORY_SEPARATOR.'../../../resources/Fixtures/beta',
                ),
                'NamespaceCollision\A\B\Foo',
                '->loadClass() loads NamespaceCollision\A\B\Foo from beta.',
            ),
            array(
                array(
                    'NamespaceCollision\\A\\B' => dirname(__FILE__).DIRECTORY_SEPARATOR.'../../../resources/Fixtures/beta',
                    'NamespaceCollision\\A' => dirname(__FILE__).DIRECTORY_SEPARATOR.'../../../resources/Fixtures/alpha',
                ),
                'NamespaceCollision\A\B\Bar',
                '->loadClass() loads NamespaceCollision\A\B\Bar from beta.',
            ),
        );
    }

    /**
     * @dataProvider getLoadClassPrefixCollisionTests
     */
    public function testLoadClassPrefixCollision($prefixes, $className, $message)
    {
        $loader = new ehough_pulsar_UniversalClassLoader();
        $loader->registerPrefixes($prefixes);

        $this->assertTrue($loader->loadClass($className));
        $this->assertTrue(class_exists($className), $message);
    }

    public function getLoadClassPrefixCollisionTests()
    {
        return array(
            array(
                array(
                    'PrefixCollision_A_' => dirname(__FILE__).DIRECTORY_SEPARATOR.'../../../resources/Fixtures/alpha',
                    'PrefixCollision_A_B_' => dirname(__FILE__).DIRECTORY_SEPARATOR.'../../../resources/Fixtures/beta',
                ),
                'PrefixCollision_A_Foo',
                '->loadClass() loads PrefixCollision_A_Foo from alpha.',
            ),
            array(
                array(
                    'PrefixCollision_A_B_' => dirname(__FILE__).DIRECTORY_SEPARATOR.'../../../resources/Fixtures/beta',
                    'PrefixCollision_A_' => dirname(__FILE__).DIRECTORY_SEPARATOR.'../../../resources/Fixtures/alpha',
                ),
                'PrefixCollision_A_Bar',
                '->loadClass() loads PrefixCollision_A_Bar from alpha.',
            ),
            array(
                array(
                    'PrefixCollision_A_' => dirname(__FILE__).DIRECTORY_SEPARATOR.'../../../resources/Fixtures/alpha',
                    'PrefixCollision_A_B_' => dirname(__FILE__).DIRECTORY_SEPARATOR.'../../../resources/Fixtures/beta',
                ),
                'PrefixCollision_A_B_Foo',
                '->loadClass() loads PrefixCollision_A_B_Foo from beta.',
            ),
            array(
                array(
                    'PrefixCollision_A_B_' => dirname(__FILE__).DIRECTORY_SEPARATOR.'../../../resources/Fixtures/beta',
                    'PrefixCollision_A_' => dirname(__FILE__).DIRECTORY_SEPARATOR.'../../../resources/Fixtures/alpha',
                ),
                'PrefixCollision_A_B_Bar',
                '->loadClass() loads PrefixCollision_A_B_Bar from beta.',
            ),
        );
    }
}
