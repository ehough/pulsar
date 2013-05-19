<?php

__throwback::$config = array(

    'name'         => 'ehough_pulsar',
    'autoload'     => dirname(__FILE__) . '/../../main/php',
    'dependencies' => array(

        array('symfony/finder', 'https://github.com/symfony/Finder', '', 'Symfony/Component/Finder'),
    )
);