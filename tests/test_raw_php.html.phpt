--TEST--
Template Test: raw_php.html
--FILE--
<?php
require_once 'testsuite.php';
compilefile('raw_php.html');

--EXPECTF--
===Compiling raw_php.html===



===Compiled file: raw_php.html===





<script language="php">

for($i=0;$i<10;$i++) { 

echo "hello world";
}
</script>


===With data file: raw_php.html===





hello worldhello worldhello worldhello worldhello worldhello worldhello worldhello worldhello worldhello world