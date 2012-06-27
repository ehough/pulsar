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

require_once dirname(__FILE__) . '/../../../../main/php/ehough/pulsar/PulsarClassLoader.php';

class ehough_pulsar_PulsarClassLoaderTest extends PHPUnit_Framework_TestCase {

    private $_sut;

    public function setup()
    {
        $this->_sut = new ehough_pulsar_PulsarClassLoader();
    }

    public function testRegisterNamespace()
    {
        $this->assertFalse(class_exists('ehough\pulsar\FakeClass'));

        $this->_sut->setIncludePath(dirname(__FILE__) . '/../../');
        $this->_sut->register();

        new ehough\pulsar\FakeClass();

        $this->assertTrue(true);

        $this->_sut->unregister();
    }

    public function testRegisterNoNamespace()
    {
        $this->assertFalse(class_exists('ehough_pulsar_FakeClass2'));

        $this->_sut->setIncludePath(dirname(__FILE__) . '/../../');
        $this->_sut->register();

        new ehough_pulsar_FakeClass2();

        $this->assertTrue(true);

        $this->_sut->unregister();
    }

    public function testInitialExtension()
    {
        $this->assertTrue('.php' === $this->_sut->getFileExtension(), 'Initial file extension does not equal ".php"');
    }

    public function testInitialIncludePath()
    {
        $this->assertTrue(null === $this->_sut->getIncludePath(), 'Initial include path is not null');
    }

    public function testInitialNamespaceSeparator()
    {
        $this->assertTrue('\\' === $this->_sut->getNamespaceSeparator(), 'Initial namespace seperator is not \\');
    }

    public function testSetExtension()
    {
        $this->_sut->setFileExtension('.foobar');
        $this->assertTrue($this->_sut->getFileExtension() === '.foobar', 'setFileExtension() is broken');
    }

    public function testSetIncludePath()
    {
        $this->_sut->setIncludePath('something');
        $this->assertTrue($this->_sut->getIncludePath() === 'something', 'setIncludePath() is broken');
    }

    public function testSetNamespaceSeparator()
    {
        $this->_sut->setNamespaceSeparator('xxx');
        $this->assertTrue($this->_sut->getNamespaceSeparator() === 'xxx', 'setNamespaceSeparator() is broken');
    }
}