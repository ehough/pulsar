<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once dirname(__FILE__).'/../../../resources/Fixtures/ClassesWithParents/GInterface.php';
require_once dirname(__FILE__).'/../../../resources/Fixtures/ClassesWithParents/CInterface.php';
require_once dirname(__FILE__).'/../../../resources/Fixtures/ClassesWithParents/B.php';
require_once dirname(__FILE__).'/../../../resources/Fixtures/ClassesWithParents/A.php';

class ehough_pulsar_ClassCollectionLoaderTest extends PHPUnit_Framework_TestCase
{
    public function testTraitDependencies()
    {
        if (version_compare(phpversion(), '5.4', '<')) {
            $this->markTestSkipped('Requires PHP > 5.4');

            return;
        }

        require_once dirname(__FILE__).'/../../../resources/Fixtures/deps/traits.php';

        $r = new ReflectionClass('ehough_pulsar_ClassCollectionLoader');
        $m = $r->getMethod('getOrderedClasses');
        $m->setAccessible(true);

        $ordered = $m->invoke('ehough_pulsar_ClassCollectionLoader', array('CTFoo'));

        $this->assertEquals(
            array('TD', 'TC', 'TB', 'TA', 'TZ', 'CTFoo'),
            array_map(array($this, '__callbackGetClassName'), $ordered)
        );

        $ordered = $m->invoke('ehough_pulsar_ClassCollectionLoader', array('CTBar'));

        $this->assertEquals(
            array('TD', 'TZ', 'TC', 'TB', 'TA', 'CTBar'),
            array_map(array($this, '__callbackGetClassName'), $ordered)
        );
    }

    /**
     * @dataProvider getDifferentOrders
     */
    public function testClassReordering(array $classes)
    {
        if (version_compare(phpversion(), '5.3', '<')) {
            $this->markTestSkipped('Requires PHP > 5.3');

            return;
        }

        $expected = array(
            'ClassesWithParents\\GInterface',
            'ClassesWithParents\\CInterface',
            'ClassesWithParents\\B',
            'ClassesWithParents\\A',
        );

        $r = new ReflectionClass('ehough_pulsar_ClassCollectionLoader');
        $m = $r->getMethod('getOrderedClasses');
        $m->setAccessible(true);

        $ordered = $m->invoke('ehough_pulsar_ClassCollectionLoader', $classes);

        $this->assertEquals($expected, array_map(array($this, '__callbackGetClassName'), $ordered));
    }

    public function __callbackGetClassName($class)
    {
        return $class->getName();
    }

    public function getDifferentOrders()
    {
        return array(
            array(array(
                'ClassesWithParents\\A',
                'ClassesWithParents\\CInterface',
                'ClassesWithParents\\GInterface',
                'ClassesWithParents\\B',
            )),
            array(array(
                'ClassesWithParents\\B',
                'ClassesWithParents\\A',
                'ClassesWithParents\\CInterface',
            )),
            array(array(
                'ClassesWithParents\\CInterface',
                'ClassesWithParents\\B',
                'ClassesWithParents\\A',
            )),
            array(array(
                'ClassesWithParents\\A',
            )),
        );
    }

    /**
     * @dataProvider getDifferentOrdersForTraits
     */
    public function testClassWithTraitsReordering(array $classes)
    {
        if (version_compare(phpversion(), '5.4', '<')) {
            $this->markTestSkipped('Requires PHP > 5.4');

            return;
        }

        require_once dirname(__FILE__).'/../../../resources/Fixtures/ClassesWithParents/ATrait.php';
        require_once dirname(__FILE__).'/../../../resources/Fixtures/ClassesWithParents/BTrait.php';
        require_once dirname(__FILE__).'/../../../resources/Fixtures/ClassesWithParents/CTrait.php';
        require_once dirname(__FILE__).'/../../../resources/Fixtures/ClassesWithParents/D.php';
        require_once dirname(__FILE__).'/../../../resources/Fixtures/ClassesWithParents/E.php';

        $expected = array(
            'ClassesWithParents\\GInterface',
            'ClassesWithParents\\CInterface',
            'ClassesWithParents\\ATrait',
            'ClassesWithParents\\BTrait',
            'ClassesWithParents\\CTrait',
            'ClassesWithParents\\B',
            'ClassesWithParents\\A',
            'ClassesWithParents\\D',
            'ClassesWithParents\\E',
        );

        $r = new \ReflectionClass('ehough_pulsar_ClassCollectionLoader');
        $m = $r->getMethod('getOrderedClasses');
        $m->setAccessible(true);

        $ordered = $m->invoke('ehough_pulsar_ClassCollectionLoader', $classes);

        $this->assertEquals($expected, array_map(array($this, '__callbackGetClassName'), $ordered));
    }

    public function getDifferentOrdersForTraits()
    {
        return array(
            array(array(
                'ClassesWithParents\\E',
                'ClassesWithParents\\ATrait',
            )),
            array(array(
                'ClassesWithParents\\E',
            )),
        );
    }

    /**
     * @dataProvider getFixNamespaceDeclarationsData
     */
    public function testFixNamespaceDeclarations($source, $expected)
    {
        $this->assertEquals('<?php '.$expected, ehough_pulsar_ClassCollectionLoader::fixNamespaceDeclarations('<?php '.$source));
    }

    public function getFixNamespaceDeclarationsData()
    {
        return array(
            array("namespace;\nclass Foo {}\n", "namespace\n{\nclass Foo {}\n}"),
            array("namespace Foo;\nclass Foo {}\n", "namespace Foo\n{\nclass Foo {}\n}"),
            array("namespace   Bar ;\nclass Foo {}\n", "namespace Bar\n{\nclass Foo {}\n}"),
            array("namespace Foo\Bar;\nclass Foo {}\n", "namespace Foo\Bar\n{\nclass Foo {}\n}"),
            array("namespace Foo\Bar\Bar\n{\nclass Foo {}\n}\n", "namespace Foo\Bar\Bar\n{\nclass Foo {}\n}"),
            array("namespace\n{\nclass Foo {}\n}\n", "namespace\n{\nclass Foo {}\n}"),
        );
    }

    /**
     * @dataProvider getFixNamespaceDeclarationsDataWithoutTokenizer
     */
    public function testFixNamespaceDeclarationsWithoutTokenizer($source, $expected)
    {
        ehough_pulsar_ClassCollectionLoader::enableTokenizer(false);
        $this->assertEquals('<?php '.$expected, ehough_pulsar_ClassCollectionLoader::fixNamespaceDeclarations('<?php '.$source));
        ehough_pulsar_ClassCollectionLoader::enableTokenizer(true);
    }

    public function getFixNamespaceDeclarationsDataWithoutTokenizer()
    {
        return array(
            array("namespace;\nclass Foo {}\n", "namespace\n{\nclass Foo {}\n}\n"),
            array("namespace Foo;\nclass Foo {}\n", "namespace Foo\n{\nclass Foo {}\n}\n"),
            array("namespace   Bar ;\nclass Foo {}\n", "namespace   Bar\n{\nclass Foo {}\n}\n"),
            array("namespace Foo\Bar;\nclass Foo {}\n", "namespace Foo\Bar\n{\nclass Foo {}\n}\n"),
            array("namespace Foo\Bar\Bar\n{\nclass Foo {}\n}\n", "namespace Foo\Bar\Bar\n{\nclass Foo {}\n}\n"),
            array("namespace\n{\nclass Foo {}\n}\n", "namespace\n{\nclass Foo {}\n}\n"),
        );
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testUnableToLoadClassException()
    {
        if (is_file($file = sys_get_temp_dir().'/foo.php')) {
            unlink($file);
        }

        ehough_pulsar_ClassCollectionLoader::load(array('SomeNotExistingClass'), sys_get_temp_dir(), 'foo', false);
    }

    public function testCommentStripping()
    {
        if (is_file($file = sys_get_temp_dir().'/bar.php')) {
            unlink($file);
        }
        spl_autoload_register($r = array($this, '__callback_testCommentStripping'));

        ehough_pulsar_ClassCollectionLoader::load(
            array('Namespaced\\WithComments', 'Pearlike_WithComments'),
            sys_get_temp_dir(),
            'bar',
            false
        );

        spl_autoload_unregister($r);

        $this->assertEquals(<<<EOF
namespace Namespaced
{
class WithComments
{
public static \$loaded = true;
}
\$string ='string shoult not be   modified {\$string}';
\$heredoc = (<<<HD


Heredoc should not be   modified {\$string}


HD
);
\$nowdoc =<<<'ND'


Nowdoc should not be   modified {\$string}


ND
;
}
namespace
{
class Pearlike_WithComments
{
public static \$loaded = true;
}
}
EOF
        , str_replace("<?php \n", '', file_get_contents($file)));

        unlink($file);
    }

    public function __callback_testCommentStripping($class)
    {
        if (0 === strpos($class, 'Namespaced') || 0 === strpos($class, 'Pearlike_')) {
            require_once dirname(__FILE__).'/../../../resources/Fixtures/'.str_replace(array('\\', '_'), '/', $class).'.php';
        }
    }
}
