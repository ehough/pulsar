<?php
/**
 * Copyright 2006 - 2013 Eric D. Hough (http://ehough.com)
 *
 * This file is part of pulsar (https://github.com/ehough/pulsar)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * This class is heavily based on the ClassLoader from Composer. The only differences are
 * that it's compliant with PHP < 5.3 and there are a few code style changes.
 *
 * https://github.com/composer/composer/blob/master/src/Composer/Autoload/ClassLoader.php
 * https://github.com/composer/composer/blob/master/LICENSE
 *
 */

/**
 * For Composer...
 *
 * Copyright (c) 2011 Nils Adermann, Jordi Boggiano
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

class_exists('ehough_pulsar_UniversalClassLoader') || require 'UniversalClassLoader.php';

/**
 * Performs some Composer-specific classloading functionality.
 */
class ehough_pulsar_ComposerClassLoader extends ehough_pulsar_UniversalClassLoader
{
    private $_vendorDir;

    private $_classMap = array();

    /**
     * Constructor.
     *
     * @param string $vendorDir The absolute path of Composer's "vendor" directory.
     *
     * @throws InvalidArgumentException If the given vendor directory isn't correct.
     */
    public function __construct($vendorDir)
    {
        if (!is_dir($vendorDir) || basename($vendorDir) !== 'vendor') {

            throw new InvalidArgumentException(
                "$vendorDir does not appear to be a valid Composer \"vendor\" directory"
            );
        }

        $this->_vendorDir = $vendorDir;
    }

    /**
     * Gets the current classmap (may be empty).
     *
     * @return array An associative array of classes to locations.
     */
    public final function getClassMap()
    {
        return $this->_classMap;
    }

    /**
     * Adds a map of classes to their locations.
     *
     * @param array $classMap An associative array of classes to their locations.
     *
     * @return void
     */
    public final function addToClassMap(array $classMap)
    {
        $this->_classMap = array_merge($this->_classMap, $classMap);
    }

    /**
     * Try to perform a "quick lookup" for the file containing the given class.
     *
     * @param string $class The class name to look up.
     *
     * @return null|string Null if the class can't be found, otherwise the absolute path of the file.
     */
    public function findFile($class)
    {
        if (isset($this->_classMap[$class])) {

            return $this->_classMap[$class];
        }

        return parent::findFile($class);
    }

    /**
     * Registers this instance as an autoloader.
     *
     * @param Boolean $prepend Whether to prepend the autoloader or not
     *
     * @api
     */
    public function register($prepend = false)
    {
        $this->_performComposerAutoload();

        parent::register($prepend);
    }

    /**
     * Perform Composer's autoloading.
     *
     * @return void
     */
    private function _performComposerAutoload()
    {
        $providedVendorDir = $this->_vendorDir;

        if (! is_dir($providedVendorDir)) {

            return;
        }

        /** @noinspection PhpIncludeInspection */
        $nameSpaceMap = include "$providedVendorDir/composer/autoload_namespaces.php";

        foreach ($nameSpaceMap as $nameSpace => $path) {

            if ($nameSpace) {

                $this->registerNamespace($nameSpace, $path);
                $this->registerPrefix($nameSpace, $path);

            } else {

                $this->registerNamespaceFallback($path);
                $this->registerPrefixFallback($path);
            }
        }

        /** @noinspection PhpIncludeInspection */
        $classMap = include "$providedVendorDir/composer/autoload_classmap.php";

        if ($classMap) {

            $this->addToClassMap($classMap);
        }
    }
}