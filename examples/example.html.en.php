<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
<HEAD>
<TITLE><?php echo gettext("Untitled Document"); ?></TITLE>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">
</HEAD>

<BODY>
<P><?php echo gettext("Example Template for HTML_Template_Flexy"); ?></P>
<?php echo gettext("a full string example ~!@#\$%^&*() |\": ?\\][;\'/.,=-_+ ~` abcd....\n asfasfdas"); ?>
<H2><?php echo gettext("Variables"); ?></H2>

<P><?php printf(gettext("Standard variables \n%s \n%s\n%s"),); ?><IMG SRC="<?php echo htmlspecialchars($t->getImageDir); ?>/someimage.jpg">
<IMG SRC="<?php echo $t->getImageDir; ?>/someimage.jpg">
<IMG SRC="<?php echo urlencode($t->getImageDir); ?>/someimage.jpg">

<IMG SRC="<?php echo htmlspecialchars($t->getImageDir); ?>/someimage.jpg">
<IMG SRC="<?php echo htmlspecialchars($t->getImageDir); ?>/someimage.jpg">
</P>

<H2><?php echo gettext("Methods"); ?></H2>
<P><?php if (isset($t->a) && method_exists($t->a,'helloWorld')) echo htmlspecialchars($t->a->helloWorld()); ?><?php printf(gettext("Calling a method %s"),$_r0); ?></P>
<P><?php if (isset($t) && method_exists($t,'includeBody')) echo $t->includeBody(); ?><?php printf(gettext("or %s"),$_r0); ?></P>
<IMG SRC="<?php if (isset($t) && method_exists($t,'getImageDir')) echo htmlspecialchars($t->getImageDir()); ?>/someimage.jpg">
<IMG SRC="<?php if (isset($t) && method_exists($t,'getImageDir')) echo $t->getImageDir(); ?>/someimage.jpg">
<IMG SRC="<?php if (isset($t) && method_exists($t,'getImageDir')) echo urlencode($t->getImageDir()); ?>/someimage.jpg">

<IMG SRC="<?php if (isset($t) && method_exists($t,'getImageDir')) echo htmlspecialchars($t->getImageDir()); ?>/someimage.jpg">
<IMG SRC="<?php if (isset($t) && method_exists($t,'getImageDir')) echo htmlspecialchars($t->getImageDir()); ?>/someimage.jpg">

<H2><?php echo gettext("Conditions"); ?></H2>
<P><?php echo gettext("a condition"); ?> <?php if ($t->condition)  { ?> <?php echo gettext("hello"); ?><?php } else {?> <?php echo gettext("world"); ?><?php } ?></P>


<H2><?php echo gettext("Looping"); ?></H2>


<P><?php echo gettext("a loop"); ?> <?php if (is_array($t->loop)) foreach($t->loop as $a) { ?> <?php echo htmlspecialchars($a); ?> <?php } ?></P>
<P><?php echo gettext("a loop with 2 vars"); ?> <?php if (is_array($t->loop)) foreach($t->loop as $a => $b) { ?> 
    <?php echo htmlspecialchars($a); ?> <?php echo gettext(","); ?><?php echo htmlspecialchars($b); ?>
<?php } ?></P>


<P><?php echo gettext("HTML tags example using foreach="); ?>&quot;<?php echo gettext("loop,a"); ?>&quot; <?php echo gettext("or the tr"); ?></P>
<TABLE WIDTH="100%" BORDER="0">
  <?php if (is_array($t->loop)) foreach($t->loop as $a) { ?><TR> 
    <TD><?php echo gettext("a is"); ?> </TD>
    <TD><?php echo htmlspecialchars($a); ?></TD>
  </TR><?php } ?>
</TABLE>

<P><?php echo gettext("HTML tags example using foreach="); ?>&quot;<?php echo gettext("loop,a,b"); ?>&quot; <?php echo gettext("or the tr"); ?></P>
<TABLE WIDTH="100%" BORDER="0">
  <?php if (is_array($t->loop)) foreach($t->loop as $a => $b) { ?><TR> 
    <TD><?php echo htmlspecialchars($a); ?></TD>
    <TD><?php echo htmlspecialchars($b); ?></TD>
  </TR><?php } ?>
</TABLE>

<H2><?php echo gettext("Form Not Parsed"); ?></H2>

<FORM NAME="test">
    <INPUT NAME=test123>
    <SELECT NAME="aaa">
        <OPTION><?php echo gettext("bb"); ?></OPTION>
    </SELECT>
</FORM>

<H2><?php echo gettext("Parsed"); ?></H2>


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
        <OPTION><?php echo gettext("bb"); ?></OPTION>
    </SELECT>
    <SELECT NAME="aaa">
        <OPTION<? if ($t->test->aaa == "<?php echo gettext(\"bb\"); ?>") echo ' SELECTED';?>><?php echo gettext("bb"); ?></OPTION>
    </SELECT>
</FORM>


<P>&nbsp;</P>
</BODY>
</HTML>
