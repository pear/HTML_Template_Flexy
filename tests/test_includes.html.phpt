--TEST--
Template Test: includes.html
--FILE--
<?php
require_once 'testsuite.php';
compilefile('includes.html');

--EXPECTF--
===Compiling includes.html===



===Compiled file: includes.html===


===With data file: includes.html===