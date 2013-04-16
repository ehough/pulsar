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

//use Symfony\Component\ClassLoader\ClassMapGenerator;

class ehough_pulsar_ClassMapGeneratorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var string $workspace
     */
    private $workspace = null;

    public function prepare_workspace()
    {
        $this->workspace = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.time().rand(0, 1000);
        mkdir($this->workspace, 0777, true);
        $this->workspace = realpath($this->workspace);
    }

    /**
     * @param string $file
     */
    private function clean($file)
    {
        if (is_dir($file) && !is_link($file)) {
            $dir = new \FilesystemIterator($file);
            foreach ($dir as $childFile) {
                $this->clean($childFile);
            }

            rmdir($file);
        } else {
            unlink($file);
        }
    }

    /**
     * @dataProvider getTestCreateMapTests
     */
    public function testDump($directory, $expected)
    {
        $this->prepare_workspace();

        $file = $this->workspace.'/file';

        $generator = new ehough_pulsar_ClassMapGenerator();
        $generator->dump($directory, $file);
        $this->assertFileExists($file);

        $this->clean($this->workspace);
    }

    /**
     * @dataProvider getTestCreateMapTests
     */
    public function testCreateMap($directory, $expected)
    {
        $this->assertEqualsNormalized($expected, ehough_pulsar_ClassMapGenerator::createMap($directory));
    }

    public function getTestCreateMapTests()
    {
        $data = array(
            array(dirname(__FILE__).'/../../../resources/Fixtures/Namespaced', array(
                'Namespaced\\Bar'          => realpath(dirname(__FILE__).'/../../../resources/Fixtures/Namespaced/Bar.php'),
                'Namespaced\\Foo'          => realpath(dirname(__FILE__).'/../../../resources/Fixtures/Namespaced/Foo.php'),
                'Namespaced\\Baz'          => realpath(dirname(__FILE__).'/../../../resources/Fixtures/Namespaced/Baz.php'),
                'Namespaced\\WithComments' => realpath(dirname(__FILE__).'/../../../resources/Fixtures/Namespaced/WithComments.php'),
                )
            ),
            array(dirname(__FILE__).'/../../../resources/Fixtures/beta/NamespaceCollision', array(
                'NamespaceCollision\\A\\B\\Bar' => realpath(dirname(__FILE__).'/../../../resources/Fixtures/beta/NamespaceCollision/A/B/Bar.php'),
                'NamespaceCollision\\A\\B\\Foo' => realpath(dirname(__FILE__).'/../../../resources/Fixtures/beta/NamespaceCollision/A/B/Foo.php'),
                'NamespaceCollision\\C\\B\\Bar' => realpath(dirname(__FILE__).'/../../../resources/Fixtures/beta/NamespaceCollision/C/B/Bar.php'),
                'NamespaceCollision\\C\\B\\Foo' => realpath(dirname(__FILE__).'/../../../resources/Fixtures/beta/NamespaceCollision/C/B/Foo.php'),
            )),
            array(dirname(__FILE__).'/../../../resources/Fixtures/Pearlike', array(
                'Pearlike_Foo'          => realpath(dirname(__FILE__).'/../../../resources/Fixtures/Pearlike/Foo.php'),
                'Pearlike_Bar'          => realpath(dirname(__FILE__).'/../../../resources/Fixtures/Pearlike/Bar.php'),
                'Pearlike_Baz'          => realpath(dirname(__FILE__).'/../../../resources/Fixtures/Pearlike/Baz.php'),
                'Pearlike_WithComments' => realpath(dirname(__FILE__).'/../../../resources/Fixtures/Pearlike/WithComments.php'),
            )),
            array(dirname(__FILE__).'/../../../resources/Fixtures/classmap', array(
                'Foo\\Bar\\A'             => realpath(dirname(__FILE__).'/../../../resources/Fixtures/classmap/sameNsMultipleClasses.php'),
                'Foo\\Bar\\B'             => realpath(dirname(__FILE__).'/../../../resources/Fixtures/classmap/sameNsMultipleClasses.php'),
                'A'                       => realpath(dirname(__FILE__).'/../../../resources/Fixtures/classmap/multipleNs.php'),
                'Alpha\\A'                => realpath(dirname(__FILE__).'/../../../resources/Fixtures/classmap/multipleNs.php'),
                'Alpha\\B'                => realpath(dirname(__FILE__).'/../../../resources/Fixtures/classmap/multipleNs.php'),
                'Beta\\A'                 => realpath(dirname(__FILE__).'/../../../resources/Fixtures/classmap/multipleNs.php'),
                'Beta\\B'                 => realpath(dirname(__FILE__).'/../../../resources/Fixtures/classmap/multipleNs.php'),
                'ClassMap\\SomeInterface' => realpath(dirname(__FILE__).'/../../../resources/Fixtures/classmap/SomeInterface.php'),
                'ClassMap\\SomeParent'    => realpath(dirname(__FILE__).'/../../../resources/Fixtures/classmap/SomeParent.php'),
                'ClassMap\\SomeClass'     => realpath(dirname(__FILE__).'/../../../resources/Fixtures/classmap/SomeClass.php'),
            )),
        );

        if (version_compare(PHP_VERSION, '5.4', '>=')) {
            $data[] = array(dirname(__FILE__).'/../../../resources/Fixtures/php5.4', array(
                'TFoo' => realpath(dirname(__FILE__).'/../../../resources/Fixtures/php5.4/traits.php'),
                'CFoo' => realpath(dirname(__FILE__).'/../../../resources/Fixtures/php5.4/traits.php'),
                'Foo\\TBar' => realpath(dirname(__FILE__).'/../../../resources/Fixtures/php5.4/traits.php'),
                'Foo\\IBar' => realpath(dirname(__FILE__).'/../../../resources/Fixtures/php5.4/traits.php'),
                'Foo\\TFooBar' => realpath(dirname(__FILE__).'/../../../resources/Fixtures/php5.4/traits.php'),
                'Foo\\CBar' => realpath(dirname(__FILE__).'/../../../resources/Fixtures/php5.4/traits.php'),
            ));
        }

        return $data;
    }

    public function testCreateMapFinderSupport()
    {
        if (!class_exists('Symfony\\Component\\Finder\\Finder')) {
            $this->markTestSkipped('Finder component is not available');
        }

        $finder = new \Symfony\Component\Finder\Finder();
        $finder->files()->in(dirname(__FILE__) . '/../../../resources/Fixtures/beta/NamespaceCollision');

        $this->assertEqualsNormalized(array(
            'NamespaceCollision\\A\\B\\Bar' => realpath(dirname(__FILE__).'/../../../resources/Fixtures/beta/NamespaceCollision/A/B/Bar.php'),
            'NamespaceCollision\\A\\B\\Foo' => realpath(dirname(__FILE__).'/../../../resources/Fixtures/beta/NamespaceCollision/A/B/Foo.php'),
            'NamespaceCollision\\C\\B\\Bar' => realpath(dirname(__FILE__).'/../../../resources/Fixtures/beta/NamespaceCollision/C/B/Bar.php'),
            'NamespaceCollision\\C\\B\\Foo' => realpath(dirname(__FILE__).'/../../../resources/Fixtures/beta/NamespaceCollision/C/B/Foo.php'),
        ), ehough_pulsar_ClassMapGenerator::createMap($finder));
    }

    protected function assertEqualsNormalized($expected, $actual, $message = null)
    {
        foreach ($expected as $ns => $path) {
            $expected[$ns] = strtr($path, '\\', '/');
        }
        foreach ($actual as $ns => $path) {
            $actual[$ns] = strtr($path, '\\', '/');
        }
        $this->assertEquals($expected, $actual, $message);
    }
}
