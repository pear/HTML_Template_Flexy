--TEST--
Template Test: plugin_modifiers.html
--FILE--
<?php
require_once 'testsuite.php';
compilefile('plugin_modifiers.html', 
	array(
		'numbertest' =>  10000.123,
		'datetest' =>  '2004-01-12'
	), 
	array('plugins'=>array('Savant'))
);

--EXPECTF--
===Compiling plugin_modifiers.html===



===Compiled file: plugin_modifiers.html===
<H1>Testing Plugin Modifiers</H1>


<?php echo $this->plugin("dateformat",$t->datetest);?>

<?php echo $this->plugin("numberformat",$t->numbertest);?>



===With data file: plugin_modifiers.html===
<H1>Testing Plugin Modifiers</H1>


12 Jan 2004
10,000.12