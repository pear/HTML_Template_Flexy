--TEST--
Template Test: forms.html
--FILE--
<?php
require_once 'testsuite.php';
require_once 'HTML/Template/Flexy/Factory.php';

$elements = HTML_Template_Flexy_Factory::fromArray(array(
    'test123' => 'hello',
    'test12a' => 'hello',
    'test12ab' => 'hello',
    'fred' => 'hello',
    'aaa1' => 'hello',
    'List' => '2000',
    'testingxhtml' => 'checked',
));


compilefile('forms.html',
    array(),
    array(
        'show_elements' => true
    ),
    $elements
);

--EXPECTF--
