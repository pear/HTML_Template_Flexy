<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<p>Example Template for HTML_Template_Flexy</p>
<p>Standard variables <?php echo htmlspecialchars($t->hello); ?> <?php echo htmlspecialchars($t->world); ?></p>
<p>a loop <?php if (is_array($t->loop)) foreach($t->loop as $a) { ?> <?php echo htmlspecialchars($a); ?> <?php } ?></p>
<p>a condition <?php if(@$t->condition)  { ?> hello <?php } else {?> world <?php } ?></p>
<p>HTML tags example using foreach=&quot;loop,a&quot; or the tr</p>
<table width="100%" border="0">
  <?php if (is_array($t->loop)) foreach($t->loop as $a) { ?><tr foreach="loop,a"> 
    <td>a is</td>
    <td><?php echo htmlspecialchars($a); ?></td>
  </tr><?php } ?>
</table>
<p>Calling a method <?php echo htmlspecialchars($t->a->helloWorld()); ?></p>
<p>&nbsp;</p>
</body>
</html>
