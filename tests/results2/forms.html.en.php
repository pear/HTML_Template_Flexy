
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
<?php echo $this->elements['form']->toHtmlnoClose();?>
<?php echo $this->elements['testing']->toHtml();?>
<?php echo $this->elements['_submit[2]']->toHtml();?>
</form>

<form action="<?php echo htmlspecialchars($t->someurl);?>">
<input name="testing" value="<?php echo htmlspecialchars($t->somevalue);?>">
<?php echo $this->elements['_submit[1]']->toHtml();?>
</form>

