--TEST--
Template Test: forms.html
--FILE--
<?php
require_once 'testsuite.php';
compilefile('forms.html');

--EXPECTF--
===Compiling forms.html===



===Compiled file: forms.html===

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
    
    
    
    
    <?php echo $this->elements['List']->toHtml();?>
    <?php echo $this->elements['_submit[4]']->toHtml();?>
    <?php echo $this->elements['_submit[5]']->toHtml();?>
    
    <?php echo $this->elements['testupload']->toHtml();?>
</form>

<?php echo $this->elements['picture']->toHtml();?>

<h2>Bug 1120:</h2>
<form action="test">
<?php echo $this->elements['testing']->toHtml();?>
<?php echo $this->elements['_submit[2]']->toHtml();?>
</form>

<form action="<?php echo htmlspecialchars($t->someurl);?>">
<?php 
if (!isset($this->elements['testing2']->attributes['value'])) {
    $this->elements['testing2']->attributes['value'] = '';
    $this->elements['testing2']->attributes['value'] .=  htmlspecialchars($t->somevalue);
}
$_attributes_used = array('value');
echo $this->elements['testing2']->toHtml();
if (isset($_attributes_used)) {  foreach($_attributes_used as $_a) {
    unset($this->elements['testing2']->attributes[$_a]);
}}
?>
<?php echo $this->elements['_submit[1]']->toHtml();?>
</form>

<H2> Bug 1275 XHTML output </H2>
<?php echo $this->elements['testingxhtml']->toHtml();?>
<?php echo $this->elements['xhtmllisttest']->toHtml();?>



<?php 
if (!isset($this->elements['test_mix']->attributes['action'])) {
    $this->elements['test_mix']->attributes['action'] = '';
    $this->elements['test_mix']->attributes['action'] .=  htmlspecialchars($t->someurl);
}
$_attributes_used = array('action');
echo $this->elements['test_mix']->toHtmlnoClose();
if (isset($_attributes_used)) {  foreach($_attributes_used as $_a) {
    unset($this->elements['test_mix']->attributes[$_a]);
}}
?>
<?php 
if (!isset($this->elements['testing5']->attributes['value'])) {
    $this->elements['testing5']->attributes['value'] = '';
    $this->elements['testing5']->attributes['value'] .=  htmlspecialchars($t->somevalue);
}
$_attributes_used = array('value');
echo $this->elements['testing5']->toHtml();
if (isset($_attributes_used)) {  foreach($_attributes_used as $_a) {
    unset($this->elements['testing5']->attributes[$_a]);
}}
?>
<?php echo $this->elements['_submit[3]']->toHtml();?>
</form>


===With data file: forms.html===

<h2>Form Not Parsed</h2>

<form name="test">
    <input name=test123>
    <select name="aaa">
        <option>bb</option>
    </select>
</form>

<h2>Parsed</h2>


<form name="test">    Input<input name="test123">    Checkbox <input name="test123a" id="test123ab" type="checkbox" checked>    Hidden <input name="test123ab" type="hidden" value="123">    <textarea name="fred">some text</textarea>    <select name="aaa1">
        <option>aa</option>
	<option selected>bb</option>
        <option>cc</option>
    </select>    <select name="aaa2">
        <option>aa</option>
	<option selected>bb</option>
        <option>cc</option>

    </select>
    <select name="aaa3">
        <option>aa</option>
	<option selected>bb</option>
        <option>cc</option>

    </select>    
    
    
    
    <select name="List">
        <option value="2000">2000</option>
        <option value="2001">2001</option>
        <option value="2002">2002</option>
    </select>    <input type="submit" name="_submit[4]" value="Next &gt;&gt;">    <input type="submit" name="_submit[5]" value="Next &gt;&gt;">    
    <input type="file" name="testupload"></form>

<img name="picture" id="picture">
<h2>Bug 1120:</h2>
<form action="test">
<input name="testing" value="test"><input type="submit" value="x" name="_submit[2]"></form>

<form action="">
<input name="testing2" value=""><input type="submit" name="_submit[1]"></form>

<H2> Bug 1275 XHTML output </H2>
<input type="checkbox" name="testingxhtml" checked="checked"><select name="xhtmllisttest">

</select>


<form name="test_mix" action=""><input name="testing5" value=""><input type="submit" name="_submit[3]"></form>