<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2002 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.02 of the PHP license,      |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors:  Alan Knowles <alan@akbkhome>                               |
// +----------------------------------------------------------------------+
//
// $Id$
 
/**
* A standard HTML Tag = eg. Table/Body etc.
*
* @abstract 
* This is the generic HTML tag 
* a simple one will have some attributes and a name.
*
*/

class HTML_Template_Flexy_Token_Tag extends HTML_Template_Flexy_Token {
        
    /**
    * HTML Tag: eg. Body or /Body
    *
    * @var string
    * @access public
    */
    var $tag = '';
    /**
    * Associative array of attributes.
    *
    * key is the left, value is the right..
    * note:
    *     values are raw (eg. include "")
    *     valuse can be 
    *                text = standard
    *                array (a parsed value with flexy tags in)
    *                object (normally some PHP code that generates the key as well..)
    *
    *
    * @var array
    * @access public
    */

    var $attributes = array();
    /**
    * postfix tokens 
    * used to add code to end of tags
    *
    * @var array
    * @access public
    */
    var $postfix = '';
     /**
    * prefix tokens 
    * used to add code to beginning of tags TODO
    *
    * @var array
    * @access public
    */
    var $prefix = '';
    
        
    /**
    * Alias to closing tag (built externally).
    * used to add < ? } ? > code to dynamic tags.
    * @var object alias
    * @access public
    */
    var $close; // alias to closing tag.
    
    /**
    * flag to only output the children
    *
    * @var boolean
    * @access public
    */
    var $startChildren = false;
    
    /**
    * Setvalue - gets name, attribute as an array
    * @see parent::setValue()
    */
  
    function setValue($value) {
        global $_HTML_TEMPLATE_FLEXY_TOKEN;
        $this->tag = $value[0];
        if (isset($value[1])) {
            $this->attributes = $value[1];
        }
        
        
        
       
    }
    /**
    * toString - display tag, attributes, postfix and any code in attributes.
    * Note first thing it does is call any parseTag Method that exists..
    *
    * 
    * @see parent::toString()
    */
    function toString() {
        
        global $_HTML_TEMPLATE_FLEXY_TOKEN;
        
        
        // if the FLEXYSTARTCHILDREN flag was set, only do children
        // normally set in BODY tag.
        if ($this->startChildren) {
            return $this->childrenToString();
        }
        
        $flexyignore = $_HTML_TEMPLATE_FLEXY_TOKEN['flexyIgnore'];
        
        if ($this->getAttribute('FLEXYIGNORE')) {
            
            $_HTML_TEMPLATE_FLEXY_TOKEN['flexyIgnore'] = true;
            unset($this->attributes['FLEXYIGNORE']);
        }
        
        
        $method = 'parseTag'.ucfirst(strtolower($this->tag));
        
        if (!$_HTML_TEMPLATE_FLEXY_TOKEN['flexyIgnore'] && method_exists($this,$method)) {
            $this->$method();
        }
    
        $ret = '';
        if ($foreach = $this->getAttribute('FOREACH')) {
            $foreachObj =  $this->factory('Foreach',
                    explode(',',$foreach),
                    $this->line);
            $ret = $foreachObj->toString();
            // does it have a closetag?
            
            $this->close->postfix = array($this->factory("End",
                    '',
                    $this->line));
            unset($this->attributes['FOREACH']);
        }
    
        $ret .=  "<". $this->tag;
        foreach ($this->attributes as $k=>$v) {
            if ($v === true) {
                $ret .= " $k";
                continue;
            }
            
            // if it's a string just dump it.
            if (is_string($v)) {
                $ret .=  " {$k}={$v}";
                continue;
            }
            
            // normally the value is an array of string, however
            // if it is an object - then it's a conditional key.
            // eg.  if (something) echo ' SELECTED';
            // the object is responsible for adding it's space..
            
            if (is_object($v)) {
                $ret .= $v->toString();
                continue;
            }
            
            
            $ret .=  " {$k}=";
            foreach($v as $item) {
                if (is_string($item)) {
                    $ret .= $item;
                    continue;
                }
                $ret .= $item->toString();
            }
        }
        $ret .= ">";
        if ($this->postfix) {
            foreach ($this->postfix as $e) {
                $ret .= $e->toString();
            }
        }
        $ret .= $this->childrenToString();
        if ($this->close) {
            $ret .= $this->close->toString();
        }
        // reset flexyignore
        
        $_HTML_TEMPLATE_FLEXY_TOKEN['flexyIgnore'] = $flexyignore;
        
        return $ret;
    }
    
    /**
    * Reads an Input tag and converts it to show variables based on the current form name
    *
    * Eg. filling in the value with  $this->{fieldname}, adding in 
    * echo $this->errors['fieldname'] at the end.
    * TODO : formating using DIV tags, and support for 'required tag'
    *
    * @return   none
    * @access   public
    */
  
    function parseTagInput() 
    {
        global $_HTML_TEMPLATE_FLEXY_TOKEN;
        // form elements : format:
        //value - fill out as PHP CODE
        
        $name =    $this->getAttribute('NAME');
        if ($_HTML_TEMPLATE_FLEXY_TOKEN['activeForm']) {
            $name = $_HTML_TEMPLATE_FLEXY_TOKEN['activeForm'] .'.'.$name;
        }
        
        $type = strtoupper($this->getAttribute('TYPE'));
        $thisvar = str_replace(']','',$name);
        $thisvar = str_replace('[','.',$thisvar);
        
        $posterror = array(
            $this->factory("PHP", "<?php if (isset(\$this->errors['".urlencode($thisvar)."'])) { ".
                "echo  htmlspecialchars(\$this->errors['".urlencode($thisvar). "']); } ?>",$this->line));
        
        
        switch ($type) {
            case "CHECKBOX":
                $this->attributes['CHECKED'] = 
                    $this->factory("PHP",
                    "<?php if (". $this->toVar($thisvar).") { ?>CHECKED<?php } ?>",
                    $this->line);
                $this->postfix = $posterror;
                break;
                
            case "SUBMIT":
                return;
 




            case "HIDDEN":
                $this->attributes['VALUE'] = array(
                    "\"",
                    $this->factory("Var",$thisvar,$this->line),
                    "\"");
                return;
            
            default:
                $this->attributes['VALUE'] = array(
                    "\"",
                    $this->factory("Var",$thisvar,$this->line),
                    "\"");
               
               $this->postfix = $posterror;
               return;
            
        }
        
        
        $this->postfix = $posterror;
        // this should use <div name="form.error"> or something...
            
        
    }
    
    /**
    * Deal with a TextArea tag - empty the contents (eg. flag ignoreChildren), and add code..
    *
    * Eg. filling in the value with  $this->{fieldname}, adding in 
    * echo $this->errors['fieldname'] at the end.
    * TODO : formating using DIV tags, and support for 'required tag'
    *
    * @return   none
    * @access   public
    */
  
    function parseTagTextArea() 
    {
        global $_HTML_TEMPLATE_FLEXY_TOKEN;
        // form elements : format:
        //value - fill out as PHP CODE
        
        $name =    $this->getAttribute('NAME');
        if ($_HTML_TEMPLATE_FLEXY_TOKEN['activeForm']) {
            $name = $_HTML_TEMPLATE_FLEXY_TOKEN['activeForm'] .'.'.$name;
        }
         
        $thisvar = str_replace(']','',$name);
        $thisvar = str_replace('[','.',$thisvar);
        
        $posterror = array(
            $this->factory("PHP", "<?php if (isset(\$this->errors['".urlencode($thisvar)."'])) { ".
                "echo  htmlspecialchars(\$this->errors['".urlencode($thisvar). "']); } ?>",$this->line));
        
        
        $this->postfix = array(
            $this->factory("Var",$thisvar ,$this->line)
            );
        $this->close->postfix = $posterror;
        $this->children = array();
        return;
 
            
        
        
    }
    /**
    * Deal with Selects
    *
    * if you set static="true" in the select tag - it will get left alone
    * (you can also turn it off by setting the flexy option ignoreTags TODO!
    *
    * the value of the select is going to be $t->theform->the_name_of_the_tag
    *
    * the options is the pullldown will have to be
    *        $t->theform->getOptions('the_name_of_the_tag')
    *
    * 
    * I did look at HTML_Select - but since we output tags - this does most of the work anyway.
    * - for a key=>value array.
    * foreach($t->theform->getOptions('name') as $_k=>$_v) {
    *    printf('<OPTION VALUE="%s"%s>%s</OPTION>',
    *               htmlspecialchars($k), 
    *               ($k == $this->theform->thename_of_the_tag) ? ' SELECTED' : '', 
    *               htmlspecialchars($v)
    *           )
    *    
    * }
    * 
    *
    * @return   none
    * @access   public
    */
  
    function parseTagSelect() 
    {
        
        global $_HTML_TEMPLATE_FLEXY_TOKEN;
          
        $call_object = '$t';
        
        $basename =    $this->getAttribute('NAME');
        $name = $basename;
        if ($_HTML_TEMPLATE_FLEXY_TOKEN['activeForm']) {
            $name = $_HTML_TEMPLATE_FLEXY_TOKEN['activeForm'] .'.'.$name;
            $call_object =  $this->toVar($_HTML_TEMPLATE_FLEXY_TOKEN['activeForm']);
        }
        
        $_HTML_TEMPLATE_FLEXY_TOKEN['activeSelect'] = $name;
        
        if ($this->getAttribute('STATIC')) {
            unset($this->attributes['STATIC']);
            return;
        }
         
         
        
        
        
        
        $this->children =   array(
                $this->factory("PHP", 
                    "<?php if (method_exists({$call_object},'getOptions'))
                    foreach(". $call_object ."->getOptions('{$basename}') as \$_k=>\$_v) {
                        printf(\"<OPTION VALUE=\\\"%s\\\"%s>%s</OPTION>\",
                                   htmlspecialchars(\$_k), 
                                   (\$_k == " . $this->toVar($name) .") ? ' SELECTED' : '', 
                                   htmlspecialchars(\$_v)
                               ); } ?>",
                    $this->line)
            );
        
    }
     /**
    * Deal with Options (on a static select list)
    *
    * bascially add an attribute tag:
    *   if ($this->theform->thename_of_the_Tag == 'name/or contents') echo " SELECTED";
    * 
    *
    * @return   none
    * @access   public
    */  
    function parseTagOption() 
    {
        
        global $_HTML_TEMPLATE_FLEXY_TOKEN;
          
         
        $name =    $_HTML_TEMPLATE_FLEXY_TOKEN['activeSelect'];
 
        $php = '<? ';
        
        if ($this->getAttribute('SELECTED')) {
           // then it's the default..
           // if no value is available for $this->the_form->the name of the tag...
           // just leave it allown
           $php .= "if (empty(".$this->toVar($name).")) echo ' SELECTED';";
        }
        $value =    $this->getAttribute('value');
        if (empty($value)) {
            $value = $this->childrenToString();
        }
        
        
        $php .= "if (".$this->toVar($name)." == \"". addslashes($value) . "\") echo ' SELECTED';";
        $php .= "?>";
        
        $this->attributes['SELECTED'] =  $this->factory("PHP", $php, $this->line);
        
    }
    
    
    
    
     /**
    * Reads an Form tag and stores the current name (used as a prefix for input
    * in the form.
    *
    
    * Eg. 
    * <form name="theform"><input name="an_input">
    * gets converted to:
    * <form name="theform"><input name="an_input" value="<? echo htmlspecialchars($t->theform->an-input); ?>">
    *
    * @return   none
    * @access   public
    */
  
    function parseTagForm() 
    {
        global $_HTML_TEMPLATE_FLEXY_TOKEN;
     
        if ($name = $this->getAttribute('NAME')) {
            $_HTML_TEMPLATE_FLEXY_TOKEN['activeForm'] = $name;
        }
        // override with flexy object
        if (isset($this->attributes['FLEXYOBJECT'])) {
            $_HTML_TEMPLATE_FLEXY_TOKEN['activeForm'] =  $this->getAttribute('FLEXYOBJECT');
            unset($this->attributes['FLEXYOBJECT']);
        }
        
        
    
    }
    /**
    * getAttribute = reads an attribute value and strips the quotes 
    *
    * TODO 
    * does not handle values with flexytags in them
    *
    * @return   none
    * @access   string
    */
    function getAttribute($key) {
        // all attribute keys are stored Upper Case,
        // however just to make sure we have not done a typo :)
        $key = strtoupper($key); 
        //echo "looking for $key\n";
        //var_dump($this->attributes);
        
        // this is weird case isset() returns false on this being null!
        
        if (@$this->attributes[$key] === true) {
            return true;
        }
        
        if (!isset($this->attributes[$key])) {
            return;
        }
        $v = $this->attributes[$key];
        
        // unlikely :)
        if ($v=='') {
            return $v;
        }
        
        switch($v{0}) {
            case "\"":
            case "'":
                return substr($v,1,-1);
            default:
                return $v;
        }
    }
    
    
        
}

 
 
   
?>