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
<?php echo urlencode($t->test); ?>
<IMG SRC="<?php echo htmlspecialchars($t->getImageDir); ?>/someimage.jpg">
<IMG SRC="<?php echo $t->getImageDir; ?>/someimage.jpg">
<IMG SRC="<?php echo urlencode($t->getImageDir); ?>/someimage.jpg">

<IMG SRC="<?php echo htmlspecialchars($t->getImageDir); ?>/someimage.jpg">
<IMG SRC="<?php echo htmlspecialchars($t->getImageDir); ?>/someimage.jpg">
</P>

<H2>Methods</H2>
<P>Calling a method <?php if (isset($t->a) && method_exists($t->a,'helloWorld')) echo htmlspecialchars($t->a->helloWorld()); ?></P>
<P>or <?php if (isset($t) && method_exists($t,'includeBody')) echo $t->includeBody(); ?></P>
<IMG SRC="<?php if (isset($t) && method_exists($t,'getImageDir')) echo htmlspecialchars($t->getImageDir()); ?>/someimage.jpg">
<IMG SRC="<?php if (isset($t) && method_exists($t,'getImageDir')) echo $t->getImageDir(); ?>/someimage.jpg">
<IMG SRC="<?php if (isset($t) && method_exists($t,'getImageDir')) echo urlencode($t->getImageDir()); ?>/someimage.jpg">

<IMG SRC="<?php if (isset($t) && method_exists($t,'getImageDir')) echo htmlspecialchars($t->getImageDir()); ?>/someimage.jpg">
<IMG SRC="<?php if (isset($t) && method_exists($t,'getImageDir')) echo htmlspecialchars($t->getImageDir()); ?>/someimage.jpg">

<H2>Conditions</H2>
<P>a condition <?php if ($t->condition)  { ?> hello <?php } else {?> world <?php } ?></P>


<H2>Looping</H2>


<P>a loop <?php if (is_array($t->loop)) foreach($t->loop as $a) { ?> <?php echo htmlspecialchars($a); ?> <?php } ?></P>
<P>a loop with 2 vars <?php if (is_array($t->loop)) foreach($t->loop as $a => $b) { ?> 
    <?php echo htmlspecialchars($a); ?> , 
    <?php echo htmlspecialchars($b); ?>
<?php } ?></P>

<TABLE>
    <?php if (is_array($t->xyz)) foreach($t->xyz as $abcd => $def) { ?><TR>
        <TD><?php echo htmlspecialchars($abcd); ?>, <?php if (isset($t) && method_exists($t,'test')) echo htmlspecialchars($t->test($def)); ?></TD>
    </TR><?php } ?>
</TABLE>


<P>HTML tags example using foreach=&quot;loop,a&quot; or the tr</P>
<TABLE WIDTH="100%" BORDER="0">
  <?php if (is_array($t->loop)) foreach($t->loop as $a) { ?><TR> 
    <TD>a is</TD>
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


<?php echo $this->quickform->formHeadToHtml(); ?>
    Input<?php echo $this->quickform->elementToHtml("test123"); ?>
    Checkbox <?php echo $this->quickform->elementToHtml("test123a"); ?>
    Hidden 
    <?php echo $this->quickform->elementToHtml("fred"); ?>
    <?php echo $this->quickform->elementToHtml("aaa1"); ?>
    <SELECT NAME="aaa2">
        <OPTION>aa</OPTION>
	<OPTION SELECTED>bb</OPTION>
        <OPTION>cc</OPTION>

    </SELECT>
    <?php echo $this->quickform->elementToHtml("aaa3"); ?>
</form>


<P>&nbsp;</P>
</BODY>
</HTML>
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
    [15] => HTML tags example using foreach=&quot;loop,a&quot; or the tr
    [16] => a is
    [17] => HTML tags example using foreach=&quot;loop,a,b&quot; or the tr
    [18] => Form Not Parsed
    [19] => bb
    [20] => Parsed
    [21] => Input
    [22] => Checkbox
    [23] => Hidden
    [24] => some text
    [25] => aa
    [27] => cc
)
html_template_flexy_quickform Object
(
    [_elementTemplate] => <!-- BEGIN required --><div class="QuickFormRequired">*</div><!-- END required -->
         <!-- BEGIN error --><div class="QuickFormError">{error}</div><br><!-- END error -->
         {element}
    [_elements] => Array
        (
            [0] => html_quickform_text Object
                (
                    [_attributes] => Array
                        (
                            [name] => test123
                            [rulea] => Test Text is a required field|required
                            [ruleb] => Test TextArea must be at least 5 characters|minlength|5
                            [type] => text
                            [size] => 
                            [maxlength] => 
                            [value] => 
                        )

                    [_tabOffset] => 0
                    [_comment] => 
                    [_label] => 
                    [_type] => text
                    [_flagFrozen] => 
                    [_persistantFreeze] => 1
                )

            [1] => html_quickform_checkbox Object
                (
                    [_attributes] => Array
                        (
                            [name] => test123a
                            [type] => checkbox
                            [checked] => checked
                            [value] => 1
                        )

                    [_tabOffset] => 0
                    [_comment] => 
                    [_label] => 
                    [_type] => checkbox
                    [_flagFrozen] => 
                    [_persistantFreeze] => 1
                    [_text] => 
                )

            [2] => html_quickform_hidden Object
                (
                    [_attributes] => Array
                        (
                            [name] => test123a
                            [type] => hidden
                            [value] => 123
                        )

                    [_tabOffset] => 0
                    [_comment] => 
                    [_label] => 
                    [_type] => hidden
                    [_flagFrozen] => 
                    [_persistantFreeze] => 
                )

            [3] => html_quickform_textarea Object
                (
                    [_attributes] => Array
                        (
                            [name] => fred
                            [wrap] => 
                            [rows] => 
                            [cols] => 
                        )

                    [_tabOffset] => 0
                    [_comment] => 
                    [_label] => 
                    [_type] => textarea
                    [_flagFrozen] => 
                    [_persistantFreeze] => 1
                    [_value] => some text
                )

            [4] => html_quickform_select Object
                (
                    [_attributes] => Array
                        (
                            [name] => aaa1
                            [size] => 
                        )

                    [_tabOffset] => 0
                    [_comment] => 
                    [_label] => 
                    [_type] => select
                    [_flagFrozen] => 
                    [_persistantFreeze] => 1
                    [_options] => Array
                        (
                            [0] => Array
                                (
                                    [text] => aa
                                    [attr] => Array
                                        (
                                            [value] => aa
                                        )

                                )

                            [1] => Array
                                (
                                    [text] => bb
                                    [attr] => Array
                                        (
                                            [value] => bb
                                        )

                                )

                            [2] => Array
                                (
                                    [text] => cc
                                    [attr] => Array
                                        (
                                            [value] => cc
                                        )

                                )

                        )

                    [_values] => Array
                        (
                            [0] => bb
                        )

                )

            [5] => html_quickform_select Object
                (
                    [_attributes] => Array
                        (
                            [name] => aaa3
                            [size] => 
                        )

                    [_tabOffset] => 0
                    [_comment] => 
                    [_label] => 
                    [_type] => select
                    [_flagFrozen] => 
                    [_persistantFreeze] => 1
                    [_options] => Array
                        (
                            [0] => Array
                                (
                                    [text] => aa
                                    [attr] => Array
                                        (
                                            [value] => aa
                                        )

                                )

                            [1] => Array
                                (
                                    [text] => bb
                                    [attr] => Array
                                        (
                                            [value] => bb
                                        )

                                )

                            [2] => Array
                                (
                                    [text] => cc
                                    [attr] => Array
                                        (
                                            [value] => cc
                                        )

                                )

                        )

                    [_values] => Array
                        (
                            [0] => bb
                        )

                )

        )

    [_elementIndex] => Array
        (
            [test123] => 0
            [test123a] => 2
            [fred] => 3
            [aaa1] => 4
            [aaa3] => 5
        )

    [_required] => Array
        (
            [0] => test123
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
            [test123] => Array
                (
                    [0] => Array
                        (
                            [type] => required
                            [format] => 
                            [message] => Test Text is a required field
                            [validation] => server
                            [reset] => 
                        )

                    [1] => Array
                        (
                            [type] => minlength
                            [format] => 5
                            [message] => Test TextArea must be at least 5 characters
                            [validation] => server
                            [reset] => 
                        )

                )

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
                    [1] => /^.+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/
                )

            [emailorblank] => Array
                (
                    [0] => regex
                    [1] => /(^$)|(^.+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$)/
                )

            [lettersonly] => Array
                (
                    [0] => regex
                    [1] => /^[a-zA-Z]*$/
                )

            [alphanumeric] => Array
                (
                    [0] => regex
                    [1] => /^[a-zA-Z0-9]*$/
                )

            [numeric] => Array
                (
                    [0] => regex
                    [1] => /(^-?\d\d*\.\d*$)|(^-?\d\d*$)|(^-?\.\d\d*$)/
                )

            [nopunctuation] => Array
                (
                    [0] => regex
                    [1] => /^[^().\/\*\^\?#!@$%+=,\"'><~\[\]{}]*$/
                )

            [nonzero] => Array
                (
                    [0] => regex
                    [1] => /^[1-9][0-9]*/
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

    [_registeredFilters] => Array
        (
            [trim] => _filterTrim
            [intval] => _filterIntval
            [strval] => _filterStrval
            [doubleval] => _filterDoubleval
            [boolval] => _filterBoolval
            [stripslashes] => _filterStripslashes
            [addslashes] => _filterAddslashes
        )

    [_headerTemplate] => 
	<tr>
		<td nowrap="nowrap" align="left" valign="top" colspan="2" bgcolor="#CCCCCC"><b>{header}</b></td>
	</tr>
    [_formTemplate] => 
<table border="0">
	<form{attributes}>{content}
	</form>
</table>
    [_requiredNoteTemplate] => 
	<tr>
		<td></td>
	<td align="left" valign="top">{requiredNote}</td>
	</tr>
    [_templates] => Array
        (
        )

    [_attributes] => Array
        (
            [name] => test
            [action] => Test.php
            [method] => POST
            [target] => 
        )

    [_tabOffset] => 0
    [_comment] => 
)
