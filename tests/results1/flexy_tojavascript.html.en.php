<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
 
 
 
<?php require_once 'HTML/Javascript/Convert.php';?>
<script type='text/javascript'>
<?php $__tmp = HTML_Javascript_Convert::convertVar($t->xyz,'test_abc_abcg',true);echo (PEAR::isError($__tmp)) ? ("<pre>".print_r($__tmp,true)."</pre>") : $__tmp;?>
<?php $__tmp = HTML_Javascript_Convert::convertVar($t->xyz,'test_abc_abcd',true);echo (PEAR::isError($__tmp)) ? ("<pre>".print_r($__tmp,true)."</pre>") : $__tmp;?>
<?php $__tmp = HTML_Javascript_Convert::convertVar($t->xyz,'test_abc_srcXxx',true);echo (PEAR::isError($__tmp)) ? ("<pre>".print_r($__tmp,true)."</pre>") : $__tmp;?>
</script>




<?php require_once 'HTML/Javascript/Convert.php';?>
<script type='text/javascript'>
<?php $__tmp = HTML_Javascript_Convert::convertVar($t->xyz,'abcg',true);echo (PEAR::isError($__tmp)) ? ("<pre>".print_r($__tmp,true)."</pre>") : $__tmp;?>
</script>





<body>
<p>Example of flexy:toJavascript with default values.</p>
</body></html>