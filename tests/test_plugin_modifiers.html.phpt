--TEST--
Template Test: plugin_modifiers.html
--FILE--
<?php
require_once 'testsuite.php';
compilefile('plugin_modifiers.html');

--EXPECTF--
===Compiling plugin_modifiers.html===



===Compiled file: plugin_modifiers.html===
<H1>Testing Plugin Modifiers</H1>


<?php echo $this->plugin("formatdate",$t->abcd);?>
<?php echo $this->plugin("formatnumber",$t->abcd);?>



===With data file: plugin_modifiers.html===
<H1>Testing Plugin Modifiers</H1>



Warning: Invalid argument supplied for foreach() in /var/svn_live/pear/HTML_Template_Flexy/Flexy/Plugin.php on line 105
Object
Warning: Invalid argument supplied for foreach() in /var/svn_live/pear/HTML_Template_Flexy/Flexy/Plugin.php on line 105
Object