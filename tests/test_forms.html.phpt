--TEST--
Template Test: forms.html
--FILE--
<?php
require_once 'testsuite.php';
require_once 'HTML/Template/Flexy/Factory.php';

$elements = HTML_Template_Flexy_Factory::fromArray(array(
    'test123' => 'hello',
    'test12a' => 'hello',
    'test12ab' => 'hello',
    'fred' => 'hello',
    'aaa1' => 'hello',
    'List' => '2000',
    'testingxhtml' => 'checked',
));


compilefile('forms.html',
    array(),
    array(
        'show_elements' => true
    ),
    $elements
);

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
Array
(
    [test] => %s Object
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

    [test123] => %s Object
        (
            [tag] => input
            [attributes] => Array
                (
                    [name] => test123
                )

            [children] => Array
                (
                )

            [override] => 
            [prefix] => 
            [suffix] => 
            [value] => 
        )

    [test123a] => %s Object
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

    [test123ab] => %s Object
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

    [fred] => %s Object
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

    [aaa1] => %s Object
        (
            [tag] => select
            [attributes] => Array
                (
                    [name] => aaa1
                )

            [children] => Array
                (
                    [0] => 
        
                    [1] => %s Object
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
	
                    [3] => %s Object
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
        
                    [5] => %s Object
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

    [aaa3] => %s Object
        (
            [tag] => select
            [attributes] => Array
                (
                    [name] => aaa3
                )

            [children] => Array
                (
                    [0] => 
        
                    [1] => %s Object
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
	
                    [3] => %s Object
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
        
                    [5] => %s Object
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

    [List] => %s Object
        (
            [tag] => select
            [attributes] => Array
                (
                    [name] => List
                )

            [children] => Array
                (
                    [0] => 
        
                    [1] => %s Object
                        (
                            [tag] => option
                            [attributes] => Array
                                (
                                    [value] => 2000
                                )

                            [children] => Array
                                (
                                    [0] => 2000
                                )

                            [override] => 
                            [prefix] => 
                            [suffix] => 
                            [value] => 
                        )

                    [2] => 
        
                    [3] => %s Object
                        (
                            [tag] => option
                            [attributes] => Array
                                (
                                    [value] => 2001
                                )

                            [children] => Array
                                (
                                    [0] => 2001
                                )

                            [override] => 
                            [prefix] => 
                            [suffix] => 
                            [value] => 
                        )

                    [4] => 
        
                    [5] => %s Object
                        (
                            [tag] => option
                            [attributes] => Array
                                (
                                    [value] => 2002
                                )

                            [children] => Array
                                (
                                    [0] => 2002
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

    [_submit[4]] => %s Object
        (
            [tag] => input
            [attributes] => Array
                (
                    [type] => submit
                    [name] => _submit[4]
                    [value] => Next >>
                )

            [children] => Array
                (
                )

            [override] => 
            [prefix] => 
            [suffix] => 
            [value] => 
        )

    [_submit[5]] => %s Object
        (
            [tag] => input
            [attributes] => Array
                (
                    [type] => submit
                    [name] => _submit[5]
                    [value] => Next >>
                )

            [children] => Array
                (
                )

            [override] => 
            [prefix] => 
            [suffix] => 
            [value] => 
        )

    [testupload] => %s Object
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

    [picture] => %s Object
        (
            [tag] => img
            [attributes] => Array
                (
                    [name] => picture
                    [id] => picture
                )

            [children] => Array
                (
                )

            [override] => 
            [prefix] => 
            [suffix] => 
            [value] => 
        )

    [testing] => %s Object
        (
            [tag] => input
            [attributes] => Array
                (
                    [name] => testing
                    [value] => test
                )

            [children] => Array
                (
                )

            [override] => 
            [prefix] => 
            [suffix] => 
            [value] => 
        )

    [_submit[2]] => %s Object
        (
            [tag] => input
            [attributes] => Array
                (
                    [type] => submit
                    [value] => x
                    [name] => _submit[2]
                )

            [children] => Array
                (
                )

            [override] => 
            [prefix] => 
            [suffix] => 
            [value] => 
        )

    [testing2] => %s Object
        (
            [tag] => input
            [attributes] => Array
                (
                    [name] => testing2
                )

            [children] => Array
                (
                )

            [override] => 
            [prefix] => 
            [suffix] => 
            [value] => 
        )

    [_submit[1]] => %s Object
        (
            [tag] => input
            [attributes] => Array
                (
                    [type] => submit
                    [name] => _submit[1]
                )

            [children] => Array
                (
                )

            [override] => 
            [prefix] => 
            [suffix] => 
            [value] => 
        )

    [testingxhtml] => %s Object
        (
            [tag] => input
            [attributes] => Array
                (
                    [type] => checkbox
                    [name] => testingxhtml
                    [checked] => 1
                    [flexy:xhtml] => 1
                )

            [children] => Array
                (
                )

            [override] => 
            [prefix] => 
            [suffix] => 
            [value] => 
        )

    [xhtmllisttest] => %s Object
        (
            [tag] => select
            [attributes] => Array
                (
                    [name] => xhtmllisttest
                    [flexy:xhtml] => 1
                )

            [children] => Array
                (
                    [0] => 


                )

            [override] => 
            [prefix] => 
            [suffix] => 
            [value] => 
        )

    [test_mix] => %s Object
        (
            [tag] => form
            [attributes] => Array
                (
                    [name] => test_mix
                )

            [children] => Array
                (
                )

            [override] => 
            [prefix] => 
            [suffix] => 
            [value] => 
        )

    [testing5] => %s Object
        (
            [tag] => input
            [attributes] => Array
                (
                    [name] => testing5
                )

            [children] => Array
                (
                )

            [override] => 
            [prefix] => 
            [suffix] => 
            [value] => 
        )

    [_submit[3]] => %s Object
        (
            [tag] => input
            [attributes] => Array
                (
                    [type] => submit
                    [name] => _submit[3]
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


===With data file: forms.html===

<h2>Form Not Parsed</h2>

<form name="test">
    <input name=test123>
    <select name="aaa">
        <option>bb</option>
    </select>
</form>

<h2>Parsed</h2>


<form name="test">    Input<input name="test123" value="hello">    Checkbox <input name="test123a" id="test123ab" type="checkbox" checked>    Hidden <input name="test123ab" type="hidden" value="123">    <textarea name="fred">hello</textarea>    <select name="aaa1">
        <option>aa</option>
	<option>bb</option>
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
        <option value="2000" selected>2000</option>
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
<input type="checkbox" name="testingxhtml"><select name="xhtmllisttest">

</select>


<form name="test_mix" action=""><input name="testing5" value=""><input type="submit" name="_submit[3]"></form>