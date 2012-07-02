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
 * along with pulsar.  If not, see <http://www.gnu.org/licenses/>.
 *
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

class_exists('ehough_pulsar_SymfonyUniversalClassLoader') || require 'SymfonyUniversalClassLoader.php';

class ehough_pulsar_ComposerClassLoader extends ehough_pulsar_SymfonyUniversalClassLoader
{
    private $_vendorDir;

    private $_classMap = array();

    public function __construct($vendorDir)
    {
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
     *
     */
    public final function addToClassMap($classMap)
    {
        if (! is_array($classMap)) {

            return;
        }

        $this->_classMap = array_merge($this->_classMap, $classMap);
    }

    protected final function findFileDefiningClass($class)
    {
        if (isset($this->_classMap[$class])) {

            return $this->_classMap[$class];
        }

        return null;
    }

    protected final function onBeforeRegister()
    {
        $this->_performComposerAutoload();
    }

    private function _performComposerAutoload()
    {
        $providedVendorDir = $this->_vendorDir;

        if (! is_dir($providedVendorDir)) {

            return;
        }

        $nameSpaceMap = include "$providedVendorDir/composer/autoload_namespaces.php";

        foreach ($nameSpaceMap as $nameSpace => $path) {

            if ($nameSpace) {

                $this->registerNamespace($nameSpace, $path);

            } else {

                $this->registerNamespaceFallback($path);
                $this->registerPrefixFallback($path);
            }
        }

        $classMap = include "$providedVendorDir/composer/autoload_classmap.php";

        if ($classMap) {

            $this->addToClassMap($classMap);
        }
    }
}