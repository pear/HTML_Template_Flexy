<?php 
function _html_template_flexy_compiler_standard_flexy_test1($t,$this) {
?>this is the contents of test1<?php 
}
?>
<H1>Example of function block definitions</H1>


<?php if ($t->false)  {?><table>
<tr><td>
    
</td></tr>
</table><?php }?>
<table>
<tr><td>
   <?php  _html_template_flexy_compiler_standard_flexy_test1($t,$this);?>
   <?php  call_user_func_array('_html_template_flexy_compiler_standard_flexy_'.$t->a_value,array($t,$this));?>
</td></tr>
</table>

    