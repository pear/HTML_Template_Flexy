<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<p>Example Template for HTML_Template_Flexy</p>

 a full string example ~!@#$%^&*() |": ?\][;'/.,=-_+ ~` abcd....
 asfasfdas

<h2>Variables</H2>

<p>Standard variables 
<?php echo htmlspecialchars($t->hello); ?> 
<?php echo $t->world; ?>
<?php echo urlencode($t->test); ?>
<img src="<?php echo htmlspecialchars($t->getImageDir); ?>/someimage.jpg">
<img src="<?php echo $t->getImageDir; ?>/someimage.jpg">
<img src="<?php echo urlencode($t->getImageDir); ?>/someimage.jpg">

<img src="<?php echo htmlspecialchars($t->getImageDir); ?>/someimage.jpg">
<img src="<?php echo htmlspecialchars($t->getImageDir); ?>/someimage.jpg">
</p>

<h2>Methods</H2>
<p>Calling a method <?php if (isset($t->a) && method_exists($t->a,'helloWorld')) echo htmlspecialchars($t->a->helloWorld()); ?></p>
<p>or <?php if (isset($t) && method_exists($t,'includeBody')) echo $t->includeBody(); ?></P>
<img src="<?php if (isset($t) && method_exists($t,'getImageDir')) echo htmlspecialchars($t->getImageDir()); ?>/someimage.jpg">
<img src="<?php if (isset($t) && method_exists($t,'getImageDir')) echo $t->getImageDir(); ?>/someimage.jpg">
<img src="<?php if (isset($t) && method_exists($t,'getImageDir')) echo urlencode($t->getImageDir()); ?>/someimage.jpg">

<img src="<?php if (isset($t) && method_exists($t,'getImageDir')) echo htmlspecialchars($t->getImageDir()); ?>/someimage.jpg">
<img src="<?php if (isset($t) && method_exists($t,'getImageDir')) echo htmlspecialchars($t->getImageDir()); ?>/someimage.jpg">

<H2>Conditions</H2>
<p>a condition <?php if ($t->condition)  { ?> hello <?php } else {?> world <?php } ?></p>


<h2>Looping</h2>


<p>a loop <?php if (is_array($t->loop)) foreach($t->loop as $a) { ?> <?php echo htmlspecialchars($a); ?> <?php } ?></p>
<p>a loop with 2 vars <?php if (is_array($t->loop)) foreach($t->loop as $a => $b) { ?> 
    <?php echo htmlspecialchars($a); ?> , 
    <?php echo htmlspecialchars($b); ?>
<?php } ?></p>

<table>
    <?php if (is_array($t->xyz)) foreach($t->xyz as $abcd => $def) { ?><tr flexy:foreach="xyz,abcd,def">
        <td><?php echo htmlspecialchars($abcd); ?>, <?php if (isset($t) && method_exists($t,'test')) echo htmlspecialchars($t->test($def)); ?></td>
    </tr><?php } ?>
</table>

<h2>Full Method testing</h2>

<?php if (isset($t) && method_exists($t,'abc')) echo htmlspecialchars($t->abc($t->abc,$t->def,$t->hij)); ?>

<?php if (isset($t) && method_exists($t,'abc')) echo htmlspecialchars($t->abc($t->abc,"def","hij")); ?>

<?php if (isset($t) && method_exists($t,'abc')) echo htmlspecialchars($t->abc($t->abc,$t->def,"hij")); ?>

<?php if (isset($t) && method_exists($t,'abc')) echo htmlspecialchars($t->abc("abc",$t->def,$t->hij)); ?>

<?php if (isset($t) && method_exists($t,'abc')) echo $t->abc($t->abc,$t->def,$t->hij); ?>

<?php if (isset($t) && method_exists($t,'abc')) echo $t->abc($t->abc,"def","hij"); ?>

<?php if (isset($t) && method_exists($t,'abc')) echo $t->abc($t->abc,$t->def,"hij"); ?>

<?php if (isset($t) && method_exists($t,'abc')) echo $t->abc("abc",$t->def,$t->hij); ?>


<?php if (isset($t) && method_exists($t,'abc')) echo urlencode($t->abc($t->abc,$t->def,$t->hij)); ?>

<?php if (isset($t) && method_exists($t,'abc')) echo urlencode($t->abc($t->abc,"def","hij")); ?>

<?php if (isset($t) && method_exists($t,'abc')) echo urlencode($t->abc($t->abc,$t->def,"hij")); ?>

<?php if (isset($t) && method_exists($t,'abc')) echo urlencode($t->abc("abc",$t->def,$t->hij)); ?>



<p>HTML tags example using foreach=&quot;loop,a&quot; or the tr</p>
<table width="100%" border="0">
  <?php if (is_array($t->loop)) foreach($t->loop as $a) { ?><tr foreach="loop,a"> 
    <td>a is</td>
    <td><?php echo htmlspecialchars($a); ?></td>
  </tr><?php } ?>
</table>

<p>HTML tags example using foreach=&quot;loop,a,b&quot; or the tr</p>
<table width="100%" border="0">
  <?php if (is_array($t->loop)) foreach($t->loop as $a => $b) { ?><tr foreach="loop,a,b"> 
    <td><?php echo htmlspecialchars($a); ?></td>
    <td><?php echo htmlspecialchars($b); ?></td>
  </tr><?php } ?>
</table>

<h2>Form Not Parsed</h2>

<form name="test" flexyignore>
    <input name=test123>
    <select name="aaa">
        <option>bb</option>
    </select>
</form>

<h2>Parsed</h2>


<?php $this->setActiveQuickForm(0);echo $this->quickform->formHeadToHtml(); ?>
    Input<?php echo $this->quickform->elementToHtml("",0); ?>
    Checkbox <?php echo $this->quickform->elementToHtml("",1); ?>
    Hidden 
    <?php echo $this->quickform->elementToHtml("fred"); ?>
    <?php echo $this->quickform->elementToHtml("aaa1"); ?>
    <select name="aaa2" flexyignore>
        <option>aa</option>
	<option selected>bb</option>
        <option>cc</option>

    </select>
    <?php echo $this->quickform->elementToHtml("aaa3"); ?>
</form>


<p>&nbsp;</p>
</body>
</html>
Array
(
    [0] => Untitled Document
    [1] => Example Template for HTML_Template_Flexy
    [2] => a full string example ~!@#\$%^&*() |\": ?\\][;\'/.,=-_+ ~` abcd....\n asfasfdas
    [3] => Variables
    [4] => Standard variables \n%s \n%s\n%s
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
    [15] => Full Method testing
    [16] => HTML tags example using foreach=&quot;loop,a&quot; or the tr
    [17] => a is
    [18] => HTML tags example using foreach=&quot;loop,a,b&quot; or the tr
    [19] => Form Not Parsed
    [20] => bb
    [21] => Parsed
    [22] => Input
    [23] => Checkbox
    [24] => Hidden
    [25] => some text
    [26] => aa
    [28] => cc
)
html_template_flexy_quickform Object
(
    [_elementTemplate] => {label}{element}
         <!-- BEGIN required --><span class="QuickFormRequired">*</span><!-- END required -->
         <!-- BEGIN error --><div class="QuickFormError">{error}</div><!-- END error -->
    [elements] => 
    [elementDefArray] => Array
        (
            [0] => Array
                (
                    [0] => Array
                        (
                            [0] => form
                            [1] => test
                            [2] => 
                            [3] => 
                            [4] => 
                            [5] => Array
                                (
                                    [name] => test
                                )

                        )

                )

            [1] => Array
                (
                    [0] => Array
                        (
                            [0] => 0
                            [1] => text
                            [2] => test123
                            [3] => 
                            [4] => Array
                                (
                                    [name] => test123
                                    [ruleA] => Test Text is a required field|required
                                    [ruleB] => Test TextArea must be at least 5 characters|minlength|5
                                )

                        )

                    [1] => Array
                        (
                            [setSize] => 
                            [setMaxLength] => 
                            [setValue] => 
                        )

                )

            [2] => Array
                (
                    [0] => Array
                        (
                            [0] => 1
                            [1] => checkbox
                            [2] => test123a
                            [3] => 
                            [4] => 
                            [5] => Array
                                (
                                    [name] => test123a
                                    [type] => checkbox
                                    [checked] => 1
                                )

                        )

                    [1] => Array
                        (
                            [setChecked] => 1
                        )

                )

            [3] => Array
                (
                    [0] => Array
                        (
                            [0] => 2
                            [1] => hidden
                            [2] => test123a
                            [3] => 123
                            [4] => Array
                                (
                                    [name] => test123a
                                    [type] => hidden
                                    [value] => 123
                                )

                        )

                    [1] => Array
                        (
                            [setValue] => 123
                        )

                )

            [4] => Array
                (
                    [0] => Array
                        (
                            [0] => textarea
                            [1] => fred
                            [2] => 
                            [3] => Array
                                (
                                    [name] => fred
                                )

                        )

                    [1] => Array
                        (
                            [setValue] => some text
                            [setWrap] => 
                            [setRows] => 
                            [setCols] => 
                        )

                )

            [5] => Array
                (
                    [0] => Array
                        (
                            [0] => select
                            [1] => aaa1
                            [2] => 
                        )

                    [1] => Array
                        (
                            [setSize] => 
                            [setMultiple] => 
                            [SetSelected] => bb
                        )

                    [2] => Array
                        (
                            [0] => Array
                                (
                                    [0] => aa
                                    [1] => aa
                                )

                            [1] => Array
                                (
                                    [0] => bb
                                    [1] => bb
                                )

                            [2] => Array
                                (
                                    [0] => cc
                                    [1] => cc
                                )

                        )

                )

            [6] => Array
                (
                    [0] => Array
                        (
                            [0] => select
                            [1] => aaa3
                            [2] => 
                        )

                    [1] => Array
                        (
                            [setSize] => 
                            [setMultiple] => 
                            [SetSelected] => bb
                        )

                    [2] => Array
                        (
                            [0] => Array
                                (
                                    [0] => aa
                                    [1] => aa
                                )

                            [1] => Array
                                (
                                    [0] => bb
                                    [1] => bb
                                )

                            [2] => Array
                                (
                                    [0] => cc
                                    [1] => cc
                                )

                        )

                )

        )

    [_buildIndex] => Array
        (
        )

    [_elements] => Array
        (
        )

    [_elementIndex] => Array
        (
        )

    [_duplicateIndex] => Array
        (
        )

    [_required] => Array
        (
        )

    [_jsPrefix] => Invalid information entered.
    [_jsPostfix] => Please correct these fields.
    [_defaultValues] => Array
        (
        )

    [_constantValues] => Array
        (
        )

    [_submitValues] => Array
        (
        )

    [_submitFiles] => Array
        (
        )

    [_maxFileSize] => 1048576
    [_freezeAll] => 
    [_rules] => Array
        (
        )

    [_filters] => Array
        (
        )

    [_errors] => Array
        (
        )

    [_requiredNote] => <font size="1" color="#FF0000">*</font><font size="1"> denotes required field</font>
    [_registeredTypes] => Array
        (
            [group] => Array
                (
                    [0] => HTML/QuickForm/group.php
                    [1] => HTML_QuickForm_group
                )

            [hidden] => Array
                (
                    [0] => HTML/QuickForm/hidden.php
                    [1] => HTML_QuickForm_hidden
                )

            [reset] => Array
                (
                    [0] => HTML/QuickForm/reset.php
                    [1] => HTML_QuickForm_reset
                )

            [checkbox] => Array
                (
                    [0] => HTML/QuickForm/checkbox.php
                    [1] => HTML_QuickForm_checkbox
                )

            [file] => Array
                (
                    [0] => HTML/QuickForm/file.php
                    [1] => HTML_QuickForm_file
                )

            [image] => Array
                (
                    [0] => HTML/QuickForm/image.php
                    [1] => HTML_QuickForm_image
                )

            [password] => Array
                (
                    [0] => HTML/QuickForm/password.php
                    [1] => HTML_QuickForm_password
                )

            [radio] => Array
                (
                    [0] => HTML/QuickForm/radio.php
                    [1] => HTML_QuickForm_radio
                )

            [button] => Array
                (
                    [0] => HTML/QuickForm/button.php
                    [1] => HTML_QuickForm_button
                )

            [submit] => Array
                (
                    [0] => HTML/QuickForm/submit.php
                    [1] => HTML_QuickForm_submit
                )

            [select] => Array
                (
                    [0] => HTML/QuickForm/select.php
                    [1] => HTML_QuickForm_select
                )

            [hiddenselect] => Array
                (
                    [0] => HTML/QuickForm/hiddenselect.php
                    [1] => HTML_QuickForm_hiddenselect
                )

            [text] => Array
                (
                    [0] => HTML/QuickForm/text.php
                    [1] => HTML_QuickForm_text
                )

            [textarea] => Array
                (
                    [0] => HTML/QuickForm/textarea.php
                    [1] => HTML_QuickForm_textarea
                )

            [link] => Array
                (
                    [0] => HTML/QuickForm/link.php
                    [1] => HTML_QuickForm_link
                )

            [advcheckbox] => Array
                (
                    [0] => HTML/QuickForm/advcheckbox.php
                    [1] => HTML_QuickForm_advcheckbox
                )

            [date] => Array
                (
                    [0] => HTML/QuickForm/date.php
                    [1] => HTML_QuickForm_date
                )

            [static] => Array
                (
                    [0] => HTML/QuickForm/static.php
                    [1] => HTML_QuickForm_static
                )

            [header] => Array
                (
                    [0] => HTML/QuickForm/header.php
                    [1] => HTML_QuickForm_header
                )

            [html] => Array
                (
                    [0] => HTML/QuickForm/html.php
                    [1] => HTML_QuickForm_html
                )

        )

    [_registeredRules] => Array
        (
            [required] => Array
                (
                    [0] => regex
                    [1] => /(\s|\S)/
                )

            [maxlength] => Array
                (
                    [0] => regex
                    [1] => /^(\s|\S){0,%data%}$/
                )

            [minlength] => Array
                (
                    [0] => regex
                    [1] => /^(\s|\S){%data%,}$/
                )

            [rangelength] => Array
                (
                    [0] => regex
                    [1] => /^(\s|\S){%data%}$/
                )

            [regex] => Array
                (
                    [0] => regex
                    [1] => %data%
                )

            [email] => Array
                (
                    [0] => regex
                    [1] => /^[a-zA-Z0-9\._-]+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/
                )

            [emailorblank] => Array
                (
                    [0] => regex
                    [1] => /(^$)|(^[a-zA-Z0-9\._-]+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$)/
                )

            [lettersonly] => Array
                (
                    [0] => regex
                    [1] => /^[a-zA-Z]+$/
                )

            [alphanumeric] => Array
                (
                    [0] => regex
                    [1] => /^[a-zA-Z0-9]+$/
                )

            [numeric] => Array
                (
                    [0] => regex
                    [1] => /(^-?\d\d*\.\d*$)|(^-?\d\d*$)|(^-?\.\d\d*$)/
                )

            [nopunctuation] => Array
                (
                    [0] => regex
                    [1] => /^[^().\/\*\^\?#!@$%+=,\"'><~\[\]{}]+$/
                )

            [nonzero] => Array
                (
                    [0] => regex
                    [1] => /^[1-9][0-9]+/
                )

            [uploadedfile] => Array
                (
                    [0] => function
                    [1] => _ruleIsUploadedFile
                )

            [maxfilesize] => Array
                (
                    [0] => function
                    [1] => _ruleCheckMaxFileSize
                )

            [mimetype] => Array
                (
                    [0] => function
                    [1] => _ruleCheckMimeType
                )

            [filename] => Array
                (
                    [0] => function
                    [1] => _ruleCheckFileName
                )

        )

    [_attributes] => Array
        (
            [action] => Test.php
            [method] => post
            [name] => 
            [id] => 
        )

    [_tabOffset] => 0
    [_comment] => 
)
