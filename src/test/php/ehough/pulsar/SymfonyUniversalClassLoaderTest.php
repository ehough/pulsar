<?php
/**
 * Copyright 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of pulsar (https://github.com/ehough/pulsar)
 *
 * pulsar is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * pulsar is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

/**
 * For Symfony...
 *
 * Copyright (c) 2004-2012 Fabien Potencier
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

require_once dirname(__FILE__) . '/../../../../main/php/ehough/pulsar/SymfonyUniversalClassLoader.php';

class ehough_pulsar_SymfonyUniversalClassLoaderTest extends PHPUnit_Framework_TestCase {

    private $_sut;

    private static $_fixturesDir;

    public function setup()
    {
        $this->_sut = new ehough_pulsar_SymfonyUniversalClassLoader();
    }

    public static function setUpBeforeClass()
    {
        self::$_fixturesDir = __DIR__ . DIRECTORY_SEPARATOR . '../../../resources/fixtures';
    }

    /**
     * @dataProvider getLoadClassTests
     */
    public function testLoadClass($className, $testClassName, $message)
    {
        $this->assertTrue($this->_sut->getRegisteredDirectories() === array());

        $this->_sut->registerDirectory('Namespaced', self::$_fixturesDir);

        $this->assertTrue($this->_sut->getRegisteredDirectories() === array('Namespaced' => array(self::$_fixturesDir)));

        $this->_sut->registerDirectory('Pearlike_', self::$_fixturesDir);

        $this->assertTrue($this->_sut->getRegisteredDirectories() === array(
            'Namespaced' => array(self::$_fixturesDir),
            'Pearlike_' => array(self::$_fixturesDir)));

        $this->_sut->loadClass($testClassName);

        $this->assertTrue(class_exists($className), $message);
    }

    /**
     * @dataProvider getLoadClassTests
     */
    public function testRegister($className, $testClassName, $message)
    {
        $this->_sut->registerDirectory('Namespaced', self::$_fixturesDir);

        $this->_sut->registerDirectory('Pearlike_', self::$_fixturesDir);

        $this->_sut->register();

        new $className;

        $this->assertTrue(true);
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

    /**
     * @dataProvider getLoadClassFromFallbackTests
     */
    public function testLoadClassFromFallback($className, $testClassName, $message)
    {
        $this->_sut->registerDirectory('Namespaced', self::$_fixturesDir);

        $this->_sut->registerDirectory('Pearlike_', self::$_fixturesDir);

        $this->_sut->registerFallbackDirectories(array(self::$_fixturesDir . '/fallback'));

        $this->_sut->registerFallbackDirectories(array(self::$_fixturesDir . '/fallback'));

        $this->_sut->loadClass($testClassName);

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

        $this->_sut->registerFallbackDirectory(self::$_fixturesDir . '/fallback');

        $this->assertEquals(array(self::$_fixturesDir . '/fallback'), $this->_sut->getFallbackDirectories());
    }

    public function testRegisterNamespaceFallback()
    {

        $this->_sut->registerFallbackDirectory(self::$_fixturesDir . '/Namespaced/fallback');

        $this->assertEquals(array(self::$_fixturesDir . '/Namespaced/fallback'), $this->_sut->getFallbackDirectories());
    }

    /**
     * @dataProvider getLoadClassNamespaceCollisionTests
     */
    public function testLoadClassNamespaceCollision($namespaces, $className, $message)
    {

        $this->_sut->registerDirectories($namespaces);

        $this->_sut->loadClass($className);

        $this->assertTrue(class_exists($className), $message);
    }

    public function getLoadClassNamespaceCollisionTests()
    {
        return array(
            array(
                array(
                    'NamespaceCollision\\A' => __DIR__.DIRECTORY_SEPARATOR . '../../../resources/fixtures/alpha',
                    'NamespaceCollision\\A\\B' => __DIR__.DIRECTORY_SEPARATOR . '../../../resources/fixtures/beta',
                ),
                'NamespaceCollision\A\Foo',
                '->loadClass() loads NamespaceCollision\A\Foo from alpha.',
            ),
            array(
                array(
                    'NamespaceCollision\\A\\B' => __DIR__.DIRECTORY_SEPARATOR . '../../../resources/fixtures/beta',
                    'NamespaceCollision\\A' => __DIR__.DIRECTORY_SEPARATOR . '../../../resources/fixtures/alpha',
                ),
                'NamespaceCollision\A\Bar',
                '->loadClass() loads NamespaceCollision\A\Bar from alpha.',
            ),
            array(
                array(
                    'NamespaceCollision\\A' => __DIR__.DIRECTORY_SEPARATOR . '../../../resources/fixtures/alpha',
                    'NamespaceCollision\\A\\B' => __DIR__.DIRECTORY_SEPARATOR . '../../../resources/fixtures/beta',
                ),
                'NamespaceCollision\A\B\Foo',
                '->loadClass() loads NamespaceCollision\A\B\Foo from beta.',
            ),
            array(
                array(
                    'NamespaceCollision\\A\\B' => __DIR__.DIRECTORY_SEPARATOR . '../../../resources/fixtures/beta',
                    'NamespaceCollision\\A' => __DIR__.DIRECTORY_SEPARATOR . '../../../resources/fixtures/alpha',
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
        $this->_sut->registerDirectories($prefixes);

        $this->_sut->loadClass($className);

        $this->assertTrue(class_exists($className), $message);
    }

    public function getLoadClassPrefixCollisionTests()
    {
        return array(
            array(
                array(
                    'PrefixCollision_A_' => __DIR__.DIRECTORY_SEPARATOR . '../../../resources/fixtures/alpha',
                    'PrefixCollision_A_B_' => __DIR__.DIRECTORY_SEPARATOR . '../../../resources/fixtures/beta',
                ),
                'PrefixCollision_A_Foo',
                '->loadClass() loads PrefixCollision_A_Foo from alpha.',
            ),
            array(
                array(
                    'PrefixCollision_A_B_' => __DIR__.DIRECTORY_SEPARATOR . '../../../resources/fixtures/beta',
                    'PrefixCollision_A_' => __DIR__.DIRECTORY_SEPARATOR . '../../../resources/fixtures/alpha',
                ),
                'PrefixCollision_A_Bar',
                '->loadClass() loads PrefixCollision_A_Bar from alpha.',
            ),
            array(
                array(
                    'PrefixCollision_A_' => __DIR__.DIRECTORY_SEPARATOR . '../../../resources/fixtures/alpha',
                    'PrefixCollision_A_B_' => __DIR__.DIRECTORY_SEPARATOR . '../../../resources/fixtures/beta',
                ),
                'PrefixCollision_A_B_Foo',
                '->loadClass() loads PrefixCollision_A_B_Foo from beta.',
            ),
            array(
                array(
                    'PrefixCollision_A_B_' => __DIR__.DIRECTORY_SEPARATOR . '../../../resources/fixtures/beta',
                    'PrefixCollision_A_' => __DIR__.DIRECTORY_SEPARATOR . '../../../resources/fixtures/alpha',
                ),
                'PrefixCollision_A_B_Bar',
                '->loadClass() loads PrefixCollision_A_B_Bar from beta.',
            ),
        );
    }
}