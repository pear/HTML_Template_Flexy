<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">




</head>

<script language="javascript">

// some sample javascript that might cause problemss

function CheckDuplicates (AddListContainer, RemoveListContainer) { 
    var AddList = eval('document.main_form.'+AddListContainer); 
    var RemoveList = eval('document.main_form.'+RemoveListContainer); 
    var TempAddList = AddList.value; 
    var TempRemoveList = RemoveList.value; 
    if (TempAddList>''&&TempRemoveList>'') { 
        TempAddList = TempAddList.substring(0,TempAddList.length-1); 
    }
}
 
</script>
<!--

// and now just commented out stuff.. that may cause problems

function CheckDuplicates (AddListContainer, RemoveListContainer) { 
    var AddList = eval('document.main_form.'+AddListContainer); 
    var RemoveList = eval('document.main_form.'+RemoveListContainer); 
    var TempAddList = AddList.value; 
    var TempRemoveList = RemoveList.value; 
    if (TempAddList>''&&TempRemoveList>'') { 
        TempAddList = TempAddList.substring(0,TempAddList.length-1); 
    }
}
  dont use - - two dashes together - it's illegal in HTML :)
  -> xxx
  

-->
 
<?php require_once 'HTML/Javascript/Convert.php';?>
<script language='javascript'>
<?php echo HTML_Javascript_Convert::convertVar('test_abc_abcg',$t->xyz,true);?>
<?php echo HTML_Javascript_Convert::convertVar('test_abc_abcd',$t->xyz,true);?>
<?php echo HTML_Javascript_Convert::convertVar('test_abc_srcXxx',$t->xyz,true);?>
</script>


<body>
<p>Example Template for HTML_Template_Flexy</p>

 a full string example ~!@#$%^&*() |": ?\][;'/.,=-_+ ~` abcd....
 asfasfdas

<h2>Variables</H2>

<p>Standard variables 
<?php echo htmlspecialchars($t->hello);?> 
<?php echo $t->world;?>
<?php echo urlencode($t->test);?>
<?php echo htmlspecialchars($t->object->var);?>
<?php echo htmlspecialchars($t->array[0]);?>
<?php echo htmlspecialchars($t->array['entry']);?>
<?php echo htmlspecialchars($t->multi['array'][0]);?>
<?php echo htmlspecialchars($t->object['array']->with['objects']);?>

Everything: <?php echo '<pre>'; echo htmlspecialchars(print_r($t,true)); echo '</pre>';;?>
an Object: <?php echo '<pre>'; echo htmlspecialchars(print_r($t->object,true)); echo '</pre>';;?>


<img src="<?php echo htmlspecialchars($t->getImageDir);?>/someimage.jpg">
<img src="<?php echo $t->getImageDir;?>/someimage.jpg">
<img src="<?php echo urlencode($t->getImageDir);?>/someimage.jpg">

<img src="<?php echo htmlspecialchars($t->getImageDir);?>/someimage.jpg">
<img src="<?php echo htmlspecialchars($t->getImageDir);?>/someimage.jpg">
</p>

<h2>Methods</H2>
<p>Calling a method <?php if (isset($t->a) && method_exists($t->a,'helloWorld')) echo htmlspecialchars($t->a->helloWorld());?></p>
<p>or <?php if (isset($t) && method_exists($t,'includeBody')) echo $t->includeBody();?></P>
<img src="<?php if (isset($t) && method_exists($t,'getImageDir')) echo htmlspecialchars($t->getImageDir());?>/someimage.jpg">
<img src="<?php if (isset($t) && method_exists($t,'getImageDir')) echo $t->getImageDir();?>/someimage.jpg">
<img src="<?php if (isset($t) && method_exists($t,'getImageDir')) echo urlencode($t->getImageDir());?>/someimage.jpg">

<img src="<?php if (isset($t) && method_exists($t,'getImageDir')) echo htmlspecialchars($t->getImageDir());?>/someimage.jpg">
<img src="<?php if (isset($t) && method_exists($t,'getImageDir')) echo htmlspecialchars($t->getImageDir());?>/someimage.jpg">

<H2>Conditions</H2>
<p>a condition <?php if ($t->condition)  {?> hello <?php } else {?> world <?php }?></p>


<h2>Looping</h2>


<p>a loop <?php if (is_array($t->loop)) foreach($t->loop as $a) {?> <?php echo htmlspecialchars($a);?> <?php }?></p>
<p>a loop with 2 vars <?php if (is_array($t->loop)) foreach($t->loop as $a => $b) {?> 
    <?php echo htmlspecialchars($a);?> , 
    <?php echo htmlspecialchars($b);?>
<?php }?></p>

Bug #84
<?php if (is_array($t->list)) foreach($t->list as $i) {?><?php if (isset($t) && method_exists($t,'method')) echo htmlspecialchars($t->method($i));?><?php }?>


<table>
    <?php if (is_array($t->xyz)) foreach($t->xyz as $abcd => $def) {?><tr>
        <td><?php echo htmlspecialchars($abcd);?>, <?php if (isset($t) && method_exists($t,'test')) echo htmlspecialchars($t->test($def));?></td>
    </tr><?php }?>
</table>

<h2>Full Method testing</h2>

<?php if (isset($t) && method_exists($t,'abc')) echo htmlspecialchars($t->abc($t->abc,$t->def,$t->hij));?>

<?php if (isset($t) && method_exists($t,'abc')) echo htmlspecialchars($t->abc($t->abc,"def","hij"));?>

<?php if (isset($t) && method_exists($t,'abc')) echo htmlspecialchars($t->abc($t->abc,$t->def,"hij"));?>

<?php if (isset($t) && method_exists($t,'abc')) echo htmlspecialchars($t->abc("abc",$t->def,$t->hij));?>

<?php if (isset($t) && method_exists($t,'abc')) echo $t->abc($t->abc,$t->def,$t->hij);?>

<?php if (isset($t) && method_exists($t,'abc')) echo $t->abc($t->abc,"def","hij");?>

<?php if (isset($t) && method_exists($t,'abc')) echo $t->abc($t->abc,$t->def,"hij");?>

<?php if (isset($t) && method_exists($t,'abc')) echo $t->abc("abc",$t->def,$t->hij);?>


<?php if (isset($t) && method_exists($t,'abc')) echo urlencode($t->abc($t->abc,$t->def,$t->hij));?>

<?php if (isset($t) && method_exists($t,'abc')) echo urlencode($t->abc($t->abc,"def","hij"));?>

<?php if (isset($t) && method_exists($t,'abc')) echo urlencode($t->abc($t->abc,$t->def,"hij"));?>

<?php if (isset($t) && method_exists($t,'abc')) echo urlencode($t->abc("abc",$t->def,$t->hij));?>

A Full on test!


Invoice number: <?php if (isset($t) && method_exists($t,'getelem')) echo htmlspecialchars($t->getelem($t->invoice,"number"));?> Place: 
<?php if (isset($t) && method_exists($t,'getelem')) echo htmlspecialchars($t->getelem($t->invoice,"place"));?> Date: <?php if (isset($t) && method_exists($t,'getelem')) echo htmlspecialchars($t->getelem($t->invoice,"date"));?> Payment: 
<?php if (isset($t) && method_exists($t,'getelem')) echo htmlspecialchars($t->getelem($t->invoice,"payment"));?> Payment date: 
<?php if (isset($t) && method_exists($t,'getelem')) echo htmlspecialchars($t->getelem($t->invoice,"payment_date"));?> Seller: Name 1: 
<?php if (isset($t) && method_exists($t,'getelem')) echo htmlspecialchars($t->getelem($t->seller,"name1"));?> Name 2: <?php if (isset($t) && method_exists($t,'getelem')) echo htmlspecialchars($t->getelem($t->seller,"name2"));?> NIP: 
<?php if (isset($t) && method_exists($t,'getelem')) echo htmlspecialchars($t->getelem($t->seller,"nip"));?> Street: <?php if (isset($t) && method_exists($t,'getelem')) echo htmlspecialchars($t->getelem($t->seller,"street"));?> City: 
<?php if (isset($t) && method_exists($t,'getelem')) echo htmlspecialchars($t->getelem($t->seller,"code"));?> <?php if (isset($t) && method_exists($t,'getelem')) echo htmlspecialchars($t->getelem($t->seller,"city"));?> Buyer: Name 1: 
<?php if (isset($t) && method_exists($t,'getelem')) echo htmlspecialchars($t->getelem($t->buyer,"name1"));?> Name 2: <?php if (isset($t) && method_exists($t,'getelem')) echo htmlspecialchars($t->getelem($t->buyer,"name2"));?> NIP: 
<?php if (isset($t) && method_exists($t,'getelem')) echo htmlspecialchars($t->getelem($t->buyer,"nip"));?> Street: <?php if (isset($t) && method_exists($t,'getelem')) echo htmlspecialchars($t->getelem($t->buyer,"street"));?> City: 
<?php if (isset($t) && method_exists($t,'getelem')) echo htmlspecialchars($t->getelem($t->buyer,"code"));?> <?php if (isset($t) && method_exists($t,'getelem')) echo htmlspecialchars($t->getelem($t->buyer,"city"));?>
# 	Name <?php if ($t->show_pkwiu)  {?>	PKWIU<?php }?> 	Count 	Netto 	VAT 	Brutto
<?php if (is_array($t->positions)) foreach($t->positions as $position) {?> <?php if (isset($t) && method_exists($t,'getelem')) echo htmlspecialchars($t->getelem($position,"nr"));?> 
<?php if (isset($t) && method_exists($t,'getelem')) echo htmlspecialchars($t->getelem($position,"name"));?> <?php if ($t->show_pkwiu)  {?> 
<?php if (isset($t) && method_exists($t,'getelem')) echo htmlspecialchars($t->getelem($position,"pkwiu"));?><?php }?> 	<?php if (isset($t) && method_exists($t,'getelem')) echo htmlspecialchars($t->getelem($position,"count"));?> 
<?php if (isset($t) && method_exists($t,'getelem')) echo htmlspecialchars($t->getelem($position,"netto"));?> 	<?php if (isset($t) && method_exists($t,'getelem')) echo htmlspecialchars($t->getelem($position,"vat"));?> 
<?php if (isset($t) && method_exists($t,'getelem')) echo htmlspecialchars($t->getelem($position,"brutto"));?>
<?php }?> <?php if ($t->edit_positions)  {?> # 	Name <?php if ($t->show_pkwiu)  {?>	PKWIU<?php }?> 	Count 
<?php if (isset($t) && method_exists($t,'getelem')) if ($t->getelem($t->position,"netto_mode")) { ?>	Netto<?php } else {?>	<?php }?> 	VAT 
<?php if (isset($t) && method_exists($t,'getelem')) if ($t->getelem($t->position,"netto_mode")) { ?>	<?php } else {?>	Brutto<?php }?>
<?php if (is_array($t->edit_positions)) foreach($t->edit_positions as $k => $position) {?> <?php if (isset($t) && method_exists($t,'getelem')) echo htmlspecialchars($t->getelem($position,"nr"));?> 
<?php if ($t->show_pkwiu)  {?> 	<?php }?> 	<?php if (isset($t) && method_exists($t,'getelem')) if ($t->getelem($position,"netto_mode")) { ?> 	<?php } else {?> 
<?php }?> 	<?php if (isset($t) && method_exists($t,'getelem')) if ($t->getelem($position,"netto_mode")) { ?> 	<?php } else {?>	<?php }?>
<?php }?> <?php }?> # 	
<?php if (is_array($t->sum)) foreach($t->sum as $sum) {?> <?php if (isset($t) && method_exists($t,'getelem')) echo htmlspecialchars($t->getelem($sum,"nr"));?> 		<?php if (isset($t) && method_exists($t,'getelem')) echo htmlspecialchars($t->getelem($sum,"netto"));?> 
<?php if (isset($t) && method_exists($t,'getelem')) echo htmlspecialchars($t->getelem($sum,"vat"));?> 	<?php if (isset($t) && method_exists($t,'getelem')) echo htmlspecialchars($t->getelem($sum,"brutto"));?>
<?php }?>


<p>HTML tags example using foreach=&quot;loop,a&quot; or the tr</p>
<table width="100%" border="0">
  <?php if (is_array($t->loop)) foreach($t->loop as $a) {?><tr class="<?php echo htmlspecialchars($a->hightlight);?>"> 
    <td>a is</td>
    <?php if ($a->showtext)  {?><td><?php echo htmlspecialchars($a->text);?></td><?php }?>
    <?php if (!$a->showtext)  {?><td><?php echo number_format($a->price,2,'.',',');?></td><?php }?>
  </tr><?php }?>
</table>

Example error messages..
<?php if ($t->error_message)  {?><span><font color="red"><B>Opps</B></font></span><?php }?>


<p>HTML tags example using foreach=&quot;loop,a,b&quot; or the tr</p>
<table width="100%" border="0">
  <?php if (is_array($t->loop)) foreach($t->loop as $a => $b) {?><tr> 
    <td><?php echo htmlspecialchars($a);?></td>
    <td><?php echo htmlspecialchars($b);?></td>
  </tr><?php }?>
</table>

<h2>Form Not Parsed</h2>

<form name="test">
    <input name=test123>
    <select name="aaa">
        <option>bb</option>
    </select>
</form>

<h2>Parsed</h2>


<?php echo $this->elements['test']->toHtmlnoClose();?>
    Input<?php echo $this->elements['test123']->toHtml();?>
    Checkbox <?php echo $this->elements['test123a']->toHtml();?>
    Hidden <?php echo $this->elements['test123ab']->toHtml();?>
    <?php echo $this->elements['fred']->toHtml();?>
    <?php echo $this->elements['aaa1']->toHtml();?>
    <select name="aaa2">
        <option>aa</option>
	<option selected>bb</option>
        <option>cc</option>

    </select>
    <?php echo $this->elements['aaa3']->toHtml();?>
    
    <?php echo $this->elements['testupload']->toHtml();?>
</form>



<H1>Internal Methods Testing<H1>
 

<?php 
$x = new HTML_Template_Flexy($this->options);
$x->compile('test.html');
$x->outputObject($t);
?>

<?php 
$x = new HTML_Template_Flexy($this->options);
$x->compile('test.html');
$x->outputObject($t);
?>


<?php require_once 'HTML/Javascript/Convert.php';?>
<script language='javascript'>
<?php echo HTML_Javascript_Convert::convertVar('test_abc_abcg',$t->xyz,true);?>
<?php echo HTML_Javascript_Convert::convertVar('test_abc_abcd',$t->xyz,true);?>
<?php echo HTML_Javascript_Convert::convertVar('test_abc_srcXxx',$t->xyz,true);?>
</script>

<?php require_once 'HTML/Javascript/Convert.php';?>
<script language='javascript'>
<?php echo HTML_Javascript_Convert::convertVar('test_abc_abcg',$t->xyz,true);?>
<?php echo HTML_Javascript_Convert::convertVar('test_abc_abcd',$t->xyz,true);?>
<?php echo HTML_Javascript_Convert::convertVar('test_abc_srcXxx',$t->xyz,true);?>
</script>


<p>&nbsp;</p>
</body>
</html>
Array
(
)
Array
(
    [test] => html_template_flexy_element Object
        (
            [tag] => form
            [attributes] => Array
                (
                    [name] => test
                )

            [children] => Array
                (
                )

            [override] => 
            [prefix] => 
            [suffix] => 
            [value] => 
        )

    [test123] => html_template_flexy_element Object
        (
            [tag] => input
            [attributes] => Array
                (
                    [name] => test123
                    [ruleA] => Test Text is a required field|required
                    [ruleB] => Test TextArea must be at least 5 characters|minlength|5
                )

            [children] => Array
                (
                )

            [override] => 
            [prefix] => 
            [suffix] => 
            [value] => 
        )

    [test123a] => html_template_flexy_element Object
        (
            [tag] => input
            [attributes] => Array
                (
                    [name] => test123a
                    [id] => test123ab
                    [type] => checkbox
                    [checked] => 1
                )

            [children] => Array
                (
                )

            [override] => 
            [prefix] => 
            [suffix] => 
            [value] => 
        )

    [test123ab] => html_template_flexy_element Object
        (
            [tag] => input
            [attributes] => Array
                (
                    [name] => test123ab
                    [type] => hidden
                    [value] => 123
                )

            [children] => Array
                (
                )

            [override] => 
            [prefix] => 
            [suffix] => 
            [value] => 
        )

    [fred] => html_template_flexy_element Object
        (
            [tag] => textarea
            [attributes] => Array
                (
                    [name] => fred
                )

            [children] => Array
                (
                    [0] => some text
                )

            [override] => 
            [prefix] => 
            [suffix] => 
            [value] => 
        )

    [aaa1] => html_template_flexy_element Object
        (
            [tag] => select
            [attributes] => Array
                (
                    [name] => aaa1
                )

            [children] => Array
                (
                    [0] => 
        
                    [1] => html_template_flexy_element Object
                        (
                            [tag] => option
                            [attributes] => Array
                                (
                                )

                            [children] => Array
                                (
                                    [0] => aa
                                )

                            [override] => 
                            [prefix] => 
                            [suffix] => 
                            [value] => 
                        )

                    [2] => 
	
                    [3] => html_template_flexy_element Object
                        (
                            [tag] => option
                            [attributes] => Array
                                (
                                    [selected] => 1
                                )

                            [children] => Array
                                (
                                    [0] => bb
                                )

                            [override] => 
                            [prefix] => 
                            [suffix] => 
                            [value] => 
                        )

                    [4] => 
        
                    [5] => html_template_flexy_element Object
                        (
                            [tag] => option
                            [attributes] => Array
                                (
                                )

                            [children] => Array
                                (
                                    [0] => cc
                                )

                            [override] => 
                            [prefix] => 
                            [suffix] => 
                            [value] => 
                        )

                    [6] => 
    
                )

            [override] => 
            [prefix] => 
            [suffix] => 
            [value] => 
        )

    [aaa3] => html_template_flexy_element Object
        (
            [tag] => select
            [attributes] => Array
                (
                    [name] => aaa3
                    [static] => 1
                )

            [children] => Array
                (
                    [0] => 
        
                    [1] => html_template_flexy_element Object
                        (
                            [tag] => option
                            [attributes] => Array
                                (
                                )

                            [children] => Array
                                (
                                    [0] => aa
                                )

                            [override] => 
                            [prefix] => 
                            [suffix] => 
                            [value] => 
                        )

                    [2] => 
	
                    [3] => html_template_flexy_element Object
                        (
                            [tag] => option
                            [attributes] => Array
                                (
                                    [selected] => 1
                                )

                            [children] => Array
                                (
                                    [0] => bb
                                )

                            [override] => 
                            [prefix] => 
                            [suffix] => 
                            [value] => 
                        )

                    [4] => 
        
                    [5] => html_template_flexy_element Object
                        (
                            [tag] => option
                            [attributes] => Array
                                (
                                )

                            [children] => Array
                                (
                                    [0] => cc
                                )

                            [override] => 
                            [prefix] => 
                            [suffix] => 
                            [value] => 
                        )

                    [6] => 

    
                )

            [override] => 
            [prefix] => 
            [suffix] => 
            [value] => 
        )

    [testupload] => html_template_flexy_element Object
        (
            [tag] => input
            [attributes] => Array
                (
                    [type] => file
                    [name] => testupload
                )

            [children] => Array
                (
                )

            [override] => 
            [prefix] => 
            [suffix] => 
            [value] => 
        )

)
