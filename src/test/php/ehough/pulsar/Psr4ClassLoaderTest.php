<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class ehough_pulsar_Psr4ClassLoaderTest extends PHPUnit_Framework_TestCase
{
    private $_fixturesDirectory;

    public function setUp()
    {
        if (version_compare(PHP_VERSION, '5.3') < 0) {

            $this->markTestSkipped('PSR-4 requires namespaces.');
        }

        $this->_fixturesDirectory = dirname(__FILE__) . '/../../../resources/Fixtures';
    }

    /**
     * @param string $className
     * @dataProvider getLoadClassTests
     */
    public function testLoadClass($className)
    {
        $loader = new ehough_pulsar_Psr4ClassLoader();
        $loader->addPrefix(
            'Acme\\DemoLib',
            $this->_fixturesDirectory . DIRECTORY_SEPARATOR . 'psr-4'
        );
        $loader->loadClass($className);
        $this->assertTrue(class_exists($className), sprintf('loadClass() should load %s', $className));
    }

    /**
     * @return array
     */
    public function getLoadClassTests()
    {
        return array(
            array('Acme\\DemoLib\\Foo'),
            array('Acme\\DemoLib\\Class_With_Underscores'),
            array('Acme\\DemoLib\\Lets\\Go\\Deeper\\Foo'),
            array('Acme\\DemoLib\\Lets\\Go\\Deeper\\Class_With_Underscores')
        );
    }

    /**
     * @param string $className
     * @dataProvider getLoadNonexistentClassTests
     */
    public function testLoadNonexistentClass($className)
    {
        $loader = new ehough_pulsar_Psr4ClassLoader();
        $loader->addPrefix(
            'Acme\\DemoLib',
            $this->_fixturesDirectory . DIRECTORY_SEPARATOR . 'psr-4'
        );
        $loader->loadClass($className);
        $this->assertFalse(class_exists($className), sprintf('loadClass() should not load %s', $className));
    }

    /**
     * @return array
     */
    public function getLoadNonexistentClassTests()
    {
        return array(
            array('Acme\\DemoLib\\I_Do_Not_Exist'),
            array('UnknownVendor\\SomeLib\\I_Do_Not_Exist')
        );
    }
}
