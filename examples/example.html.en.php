<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
<HEAD>
<TITLE>Untitled Document</TITLE>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">
</HEAD>

<BODY>
<P>Example Template for HTML_Template_Flexy</P>
a full string example ~!@#$%^&*() |": ?\][;'/.,=-_+ ~` abcd....
 asfasfdas
<H2>Variables</H2>

<P>Standard variables 
<?php echo htmlspecialchars($t->hello); ?> 
<?php echo $t->world; ?>
<?php echo urlencode($t->test); ?><IMG SRC="<?php echo htmlspecialchars($t->getImageDir); ?>/someimage.jpg">
<IMG SRC="<?php echo $t->getImageDir; ?>/someimage.jpg">
<IMG SRC="<?php echo urlencode($t->getImageDir); ?>/someimage.jpg">

<IMG SRC="<?php echo htmlspecialchars($t->getImageDir); ?>/someimage.jpg">
<IMG SRC="<?php echo htmlspecialchars($t->getImageDir); ?>/someimage.jpg">
</P>

<H2>Methods</H2>
<P>Calling a method<?php if (isset($t->a) && method_exists($t->a,'helloWorld')) echo htmlspecialchars($t->a->helloWorld()); ?></P>
<P>or<?php if (isset($t) && method_exists($t,'includeBody')) echo $t->includeBody(); ?></P>
<IMG SRC="<?php if (isset($t) && method_exists($t,'getImageDir')) echo htmlspecialchars($t->getImageDir()); ?>/someimage.jpg">
<IMG SRC="<?php if (isset($t) && method_exists($t,'getImageDir')) echo $t->getImageDir(); ?>/someimage.jpg">
<IMG SRC="<?php if (isset($t) && method_exists($t,'getImageDir')) echo urlencode($t->getImageDir()); ?>/someimage.jpg">

<IMG SRC="<?php if (isset($t) && method_exists($t,'getImageDir')) echo htmlspecialchars($t->getImageDir()); ?>/someimage.jpg">
<IMG SRC="<?php if (isset($t) && method_exists($t,'getImageDir')) echo htmlspecialchars($t->getImageDir()); ?>/someimage.jpg">

<H2>Conditions</H2>
<P>a condition <?php if ($t->condition)  { ?> hello<?php } else {?> world<?php } ?></P>


<H2>Looping</H2>


<P>a loop <?php if (is_array($t->loop)) foreach($t->loop as $a) { ?> <?php echo htmlspecialchars($a); ?> <?php } ?></P>
<P>a loop with 2 vars <?php if (is_array($t->loop)) foreach($t->loop as $a => $b) { ?> 
    <?php echo htmlspecialchars($a); ?> ,<?php echo htmlspecialchars($b); ?>
<?php } ?></P>


<P>HTML tags example using foreach=&quot;loop,a&quot; or the tr</P>
<TABLE WIDTH="100%" BORDER="0">
  <?php if (is_array($t->loop)) foreach($t->loop as $a) { ?><TR> 
    <TD>a is </TD>
    <TD><?php echo htmlspecialchars($a); ?></TD>
  </TR><?php } ?>
</TABLE>

<P>HTML tags example using foreach=&quot;loop,a,b&quot; or the tr</P>
<TABLE WIDTH="100%" BORDER="0">
  <?php if (is_array($t->loop)) foreach($t->loop as $a => $b) { ?><TR> 
    <TD><?php echo htmlspecialchars($a); ?></TD>
    <TD><?php echo htmlspecialchars($b); ?></TD>
  </TR><?php } ?>
</TABLE>

<H2>Form Not Parsed</H2>

<FORM NAME="test">
    <INPUT NAME=test123>
    <SELECT NAME="aaa">
        <OPTION>bb</OPTION>
    </SELECT>
</FORM>

<H2>Parsed</H2>


<FORM NAME="test">
    <INPUT NAME=test123 VALUE="<?php echo htmlspecialchars($t->test->test123); ?>"><?php if (isset($t->flexyError->test123)) { echo  htmlspecialchars($t->flexyError->test123); } ?>
    <TEXTAREA NAME="fred"><?php echo htmlspecialchars($t->test->fred); ?></TEXTAREA><?php if (isset($t->flexyError->fred)) { echo  htmlspecialchars($t->flexyError->fred); } ?>
    <SELECT NAME="aaa"><?php if (method_exists($t->test,'getOptions'))
                    foreach($t->test->getOptions('aaa') as $_k=>$_v) {
                        printf("<OPTION VALUE=\"%s\"%s>%s</OPTION>",
                                   htmlspecialchars($_k), 
                                   ($_k == $t->test->aaa) ? ' SELECTED' : '', 
                                   htmlspecialchars($_v)
                               ); } ?></SELECT>
    <SELECT NAME="aaa">
        <OPTION>bb</OPTION>
    </SELECT>
    <SELECT NAME="aaa">
        <OPTION<? if ($t->test->aaa == "bb") echo ' SELECTED';?>>bb</OPTION>
    </SELECT>
</FORM>


<P>&nbsp;</P>
</BODY>
</HTML>
Array
(
    [0] => Untitled Document
    [1] => Example Template for HTML_Template_Flexy
    [2] => a full string example ~!@#\$%^&*() |\": ?\\][;\'/.,=-_+ ~` abcd....\r\n asfasfdas
    [3] => Variables
    [4] => Standard variables \r\n%s \r\n%s\r\n%s
    [5] => Methods
    [6] => Calling a method
    [7] => or
    [8] => Conditions
    [9] => a condition
    [10] => hello
    [11] => world
    [12] => Looping
    [13] => a loop
    [14] => a loop with 2 vars
    [15] => ,
    [16] => HTML tags example using foreach=&quot;loop,a&quot; or the tr
    [17] => a is
    [18] => HTML tags example using foreach=&quot;loop,a,b&quot; or the tr
    [19] => Form Not Parsed
    [20] => bb
    [21] => Parsed
    [25] => &nbsp;
)
