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
 * Autoloader that implements the technical interoperability
 * standards for PHP 5.3 namespaces and class names.
 *
 * https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md
 *
 *     // Example which loads classes for the Doctrine Common package in the
 *     // Doctrine\Common namespace.
 *     $classLoader = new SplClassLoader('Doctrine\Common', '/path/to/doctrine');
 *     $classLoader->register();
 *
 * Based strongly on https://gist.github.com/221634 and https://gist.github.com/1335891
 */
class ehough_pulsar_PulsarClassLoader
{
    private $_fileExtension = '.php';

    private $_namespace;

    private $_includePath;

    private $_namespaceSeparator = '\\';

    /**
     * Creates a new <tt>ehough_pulsar_PulsarClassLoader</tt> that loads classes of the
     * specified namespace.
     *
     * @param string            $ns The namespace to use.
     * @param string|null|array $includePath One or more include paths to use
     */
    public function __construct($ns = null, $includePath = null)
    {
        $this->_namespace   = $ns;
        $this->_includePath = $includePath;
    }

    /**
     * Sets the namespace separator used by classes in the namespace of this class loader.
     *
     * @param string $sep The separator to use.
     */
    public function setNamespaceSeparator($sep)
    {
        $this->_namespaceSeparator = $sep;
    }

    /**
     * Gets the namespace seperator used by classes in the namespace of this class loader.
     *
     * @return string
     */
    public function getNamespaceSeparator()
    {
        return $this->_namespaceSeparator;
    }

    /**
     * Sets the base include path for all class files in the namespace of this class loader.
     *
     * @param string $includePath The include path
     */
    public function setIncludePath($includePath)
    {
        $this->_includePath = $includePath;
    }

    /**
     * Gets the base include path for all class files in the namespace of this class loader.
     *
     * @return string
     */
    public function getIncludePath()
    {
        return $this->_includePath;
    }

    /**
     * Sets the file extension of class files in the namespace of this class loader.
     *
     * @param string $fileExtension
     */
    public function setFileExtension($fileExtension)
    {
        $this->_fileExtension = $fileExtension;
    }

    /**
     * Gets the file extension of class files in the namespace of this class loader.
     *
     * @return string $fileExtension
     */
    public function getFileExtension()
    {
        return $this->_fileExtension;
    }

    /**
     * Installs this class loader on the SPL autoload stack.
     *
     * @return bool
     */
    public function register()
    {
        return spl_autoload_register(array($this, 'loadClass'));
    }

    /**
     * Uninstalls this class loader from the SPL autoloader stack.
     *
     * @return bool
     */
    public function unregister()
    {
        return spl_autoload_unregister(array($this, 'loadClass'));
    }

    /**
     * Loads the given class or interface.
     *
     * @param string $className The name of the class to load.
     *
     * @return bool Success status
     */
    public function loadClass($className)
    {
        $isFound = false;

        if ($this->_namespace === null || $this->_namespace . $this->_namespaceSeparator === substr($className, 0, strlen($this->_namespace . $this->_namespaceSeparator))) {

            $fileName  = '';
            $namespace = '';

            if (($lastNsPos = strripos($className, $this->_namespaceSeparator)) !== false) {

                $namespace = substr($className, 0, $lastNsPos);

                $className = substr($className, $lastNsPos + 1);

                $fileName  = str_replace($this->_namespaceSeparator, DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
            }

            $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . $this->_fileExtension;
            $filePath = ($this->_includePath !== null ? $this->_includePath . DIRECTORY_SEPARATOR : '') . $fileName;

            if (file_exists($filePath)) {

                require $filePath;
            }
        }

        return $isFound;
    }
}