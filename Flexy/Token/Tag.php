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
 
 
$GLOBALS['_HTML_TEMPLATE_FLEXY_TOKEN_TAG']['activeSelect'] = false;
 
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
        
        // rewriting should be done with a tag.../flag.
        
        $this->reWriteURL("HREF");
        $this->reWriteURL("SRC");
        
        
        $method = 'parseTag'.ucfirst(strtolower($this->tag));
        
        
        if (!$_HTML_TEMPLATE_FLEXY_TOKEN['flexyIgnore'] && method_exists($this,$method)) {
            $ret = $this->$method();
            // allow the parse methods to return output.
            
            if ($ret !== false) {
                return $ret;
            }
        }
        
        $ret = '';
        if ($foreach = $this->getAttribute('FOREACH')) {
            $foreachObj =  $this->factory('Foreach',
                    explode(',',$foreach),
                    $this->line);
            $ret = $foreachObj->toString();
            // does it have a closetag?
            
            $this->close->postfix = array($this->factory("End", '', $this->line));
            unset($this->attributes['FOREACH']);
        }
        
        
        if ($if = $this->getAttribute('IF')) {
            if ($foreach) {
                PEAR::raiseError(
                    "You may not use FOREACH and IF tags in the same tag on Line {$this->line} &lt;{$this->tag}&gt;",
                     null, PEAR_ERROR_DIE);
            }
            
            // allow if="!somevar"
            $ifnegative = '';
            if ($if{0} == '!') {
                $ifnegative = '!';    
                $if = substr($if,1);
            }
            // if="xxxxx"
            // if="xxxx.xxxx()" - should create a method prefixed with 'if:'
            if (!preg_match('/^[_A-Z][A-Z0-9_]*(\[[0-9]+\])?(\.[_A-Z][A-Z0-9_]*(\[[0-9]+\])?)*(\(\))?$/i',$if)) {
                PEAR::raiseError(
                    "IF tags only accept simple object.variable or object.method() values on Line {$this->line} &lt;{$this->tag}&gt;",
                     null, PEAR_ERROR_DIE);
            }
            
            if (substr($if,-1) == ')') {
                $ifObj =  $this->factory('Method',
                        array('if:'.$ifnegative.substr($if,0,-2), array()),
                        $this->line);
            } else {
                $ifObj =  $this->factory('If', $ifnegative.$if, $this->line);
            }
            
            $ret = $ifObj->toString();
            // does it have a closetag?
            
            $this->close->postfix = array($this->factory("End",'', $this->line));
            unset($this->attributes['IF']);
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
    * Reads an Input tag - build a quickform object for it
    *
    *
    * @return   string | false = html output or ignore (just output the tag)
    * @access   public
    */
  
    function parseTagInput() 
    {
        global $_HTML_TEMPLATE_FLEXY;
        // form elements : format:
        //value - fill out as PHP CODE
        
        $name =    $this->getAttribute('NAME');
        if ($name == '') {
            return false;
        }
         
        
        $type = strtoupper($this->getAttribute('TYPE'));
            
        $e = false;
        
        
        if (!$_HTML_TEMPLATE_FLEXY['quickform']) {
            return false;
        }
        
        switch ($type) {
            case "CHECKBOX":
                 $e = &$_HTML_TEMPLATE_FLEXY['quickform']->addElement(
                    'checkbox' 
                    $name,
                    ''  , // label?
                    '' , // test 
                    $this->getAttributes()  // wrapper needed...
                );
                // technically this should be a bit more complex
                // it needs to compare the 'value' field of the checkbox 
                // against the current value.
                $e->setChecked($this->getAttribute('CHECKED'));
                break;
            
            case "RESET":
                $e = &$_HTML_TEMPLATE_FLEXY['quickform']->addElement(
                    'reset' 
                    $name,
                    $this->getAttribute('VALUE') ,
                    $this->getAttributes()  // wrapper needed...
                );
                break;
                
            case "SUBMIT":
                $e = &$_HTML_TEMPLATE_FLEXY['quickform']->addElement(
                    'submit' 
                    $name,
                    $this->getAttribute('VALUE') , // the text.
                    $this->getAttributes()  // wrapper needed...
                );
                break;
                
            case "BUTTON":            
                $e = &$_HTML_TEMPLATE_FLEXY['quickform']->addElement(
                    'text' 
                    $name,
                    $this->getAttribute('VALUE') ,
                    $this->getAttributes()  // wrapper needed...
                );
                break;
                
            case "PASSWORD":     
                $e = &$_HTML_TEMPLATE_FLEXY['quickform']->addElement(
                    'password' 
                    $name,
                    '' ,
                    $this->getAttributes()  // wrapper needed...
                );
                break;




            case "HIDDEN":
                $e = &$_HTML_TEMPLATE_FLEXY['quickform']->addElement(
                    'hidden' 
                    $name,
                    $this->getAttribute('VALUE'),
                    $this->getAttributes()  // wrapper needed...
                );
                $e->setValue($this->getAttribute('VALUE'));
                // hidden elements are displayed after the form tag.
                return '';
            
            default:
                $e = &$_HTML_TEMPLATE_FLEXY['quickform']->addElement(
                    'text' 
                    $name,
                    ''  , // the text.
                    $this->getAttributes()  // wrapper needed...
                );
                $e->setSize($this->getAttribute('SIZE'));
                $e->setMaxLength($this->getAttribute('MAXLENGTH'));
                $e->setValue($this->getAttribute('VALUE'));
                break;
            
        }
        $this->_quickFormCalls();
        
        return '<?php echo $this->quickform->elementToHtml("'.$name .'"); ?>';
    
        
    }
    
    /**
    * Deal with a TextArea tag - build a quickform object for it
    *
    * @return   string | false = html output or ignore (just output the tag)
    * @access   public
    */
  
    function parseTagTextArea() 
    {
        global $_HTML_TEMPLATE_FLEXY_TOKEN;
        // form elements : format:
        //value - fill out as PHP CODE
        
        $name =    $this->getAttribute('NAME');
        
        if ($GLOBALS['_HTML_TEMPLATE_FLEXY']['quickform']) {
             
            $e = &$GLOBALS['_HTML_TEMPLATE_FLEXY']['quickform']->addElement(
                'textarea', 
                $this->getAttribute('NAME'),
                ''  , // the text.
                $this->getAttributes()  // wrapper needed...
            );
            $e->setValue($this->childrenToString());
            $e->setWrap($this->getAttribute('WRAP'));
            $e->setRows($this->getAttribute('ROWS'));
            $e->setCols($this->getAttribute('ROWS'));
        } else {
            return false;
        }
        
        return '<?php echo $this->quickform->elementToHtml("'.$name .'"); ?>';
 
            
        
        
    }
    /**
    * Deal with Selects - build a quickform object for it (unless flexyignore is set)
    *
    *
    * @return   string | false = html output or ignore (just output the tag)
    * @access   public
    */
  
    function parseTagSelect() 
    {
        
        global $_HTML_TEMPLATE_FLEXY_TOKEN;
                  
        $name =    $this->getAttribute('NAME');
         
        // this ones for quickforms...
        
        if ($GLOBALS['_HTML_TEMPLATE_FLEXY']['quickform']) {
           
            $e = &$GLOBALS['_HTML_TEMPLATE_FLEXY']['quickform']->addElement(
                'select', 
                $this->getAttribute('NAME'),
                '' // , // the text.
                //$this->getAttributes() // wrapper needed...
            );
            $GLOBALS['_HTML_TEMPLATE_FLEXY_TOKEN_TAG']['activeSelect'] = &$e; 
            $e->setSize($this->getAttribute('SIZE'));
            $e->setMultiple($this->getAttribute('MULTIPLE'));
        } else {
            return false;
        }
        // build the options.
        $this->childrenToString();
        
        // hopefully this will clear the reference and not the original..
        unset($GLOBALS['_HTML_TEMPLATE_FLEXY_TOKEN_TAG']['activeSelect']);
        $GLOBALS['_HTML_TEMPLATE_FLEXY_TOKEN_TAG']['activeSelect'] = false;
        
        return '<?php echo $this->quickform->elementToHtml("'.$name .'"); ?>';
        
        
        
    }
     /**
    * Deal with Options - fills in the quickform with the data..
    *
    * 
    *
    * @return   string | false = html output or ignore (just output the tag)
    * @access   public
    */  
    function parseTagOption() 
    {
        
        global $_HTML_TEMPLATE_FLEXY_TOKEN_TAG;
          
         
        
        $value = $this->getAttribute('VALUE');
        $text = $this->childrenToString();
        if (!$value) {
            $value = $text;
        }
        
        if (!empty($_HTML_TEMPLATE_FLEXY_TOKEN_TAG['activeSelect'])) {
            $_HTML_TEMPLATE_FLEXY_TOKEN_TAG['activeSelect']->addOption($text, $value);
            if ($this->getAttribute('SELECTED')) {
                $_HTML_TEMPLATE_FLEXY_TOKEN_TAG['activeSelect']->setSelected( $value);
            }
        } else {
            return false;
        }
    }
    
    
    
    
     /**
    * Reads an Form tag - and set up the quickform object header etc.
    *    
    * @return   string | false = html output or ignore (just output the tag)
    * @access   public
    */
  
    function parseTagForm() 
    {
        global $_HTML_TEMPLATE_FLEXY_TOKEN;
     
        if ($name = $this->getAttribute('NAME')) {
            $_HTML_TEMPLATE_FLEXY_TOKEN['activeForm'] = $name;
        }
        // override with flexy object
        if (isset($this->attributes['FLEXYIGNORE'])) {
            return false;
        }
        
        
        
        require_once 'HTML/Template/Flexy/QuickForm.php';
        $GLOBALS['_HTML_TEMPLATE_FLEXY']['quickform'] = new HTML_Template_Flexy_QuickForm(
            $this->getAttribute('NAME'),
            $this->getAttribute('METHOD'),
            $this->getAttribute('ACTION'),
            $this->getAttribute('TARGET') ,
            $this->getAttributes() // need to do some filtering on this..
        ); 
        
       
        
        return '<?php echo $this->quickform->formHeadToHtml(); ?>' .
            $this->childrenToString() .
            '</form>';
        
    
    }
    
    
    /**
    * reWriteURL - can using the config option 'url_rewrite'
    *  format "from:to,from:to"
    * only handle left rewrite. 
    * so 
    *  "/images:/myroot/images"
    * would change
    *   /images/xyz.gif to /myroot/images/xyz.gif
    *   /images/stylesheet/imagestyles.css to  /myroot/images/stylesheet/imagestyles.css
    *   note /imagestyles did not get altered.
    * will only work on strings (forget about doing /images/{someimage}
    *
    *
    * @param    string attribute to rewrite
    * @return   none
    * @access   public
    */
    function reWriteURL($which) 
    {
        global  $_HTML_TEMPLATE_FLEXY;
        
        
        if (!is_string($original = $this->getAttribute($which))) {
            return;
        }
        
        if ($original == '') {
            return;
        }
        
        if (empty($_HTML_TEMPLATE_FLEXY['currentOptions']['url_rewrite'])) {
            return;
        }
        
        $bits = explode(",",$_HTML_TEMPLATE_FLEXY['currentOptions']['url_rewrite']);
        $new = $original;
        
        foreach ($bits as $bit) {
            $parts = explode (':', $bit);
            $new = preg_replace('#^'.$parts[0].'#',$parts[1], $new);
        }
        
        
        if ($original == $new) {
            return;
        }
        $this->attributes[$which] = '"'. $new . '"';
    } 
    
    
        
    
    
    /**
    * getAttribute = reads an attribute value and strips the quotes 
    *
    * TODO 
    * does not handle values with flexytags in them
    *
    * @return   string ( 
    * @access   public
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
        // general assumption - none of the tools can do much with dynamic
        // attributes - eg. stuff with flexy tags in it.
        if (!is_string($this->attributes[$key])) {
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
      
    /**
    * getAttributes = returns all the attributes key/value without quotes
    *
    *
    * @return   array
    * @access   string
    */
    
    function getAttributes() {
        $ret = array();
        foreach($this->attributes as $k=>$v) {
            $ret[strtolower($k)] = $this->getAttribute($k);
        }
        return $ret;
    }
    /**
    * check for any quickform tags - filter* and rule*
    * arguments are seperated with a | (pipe)
    * eg. ruleB="Test TextArea must be at least 5 characters|minlength|5"
    * eg. filerA="trim"
    * 
    * if you add a filter to form  - it will assume that it affects elements
    *
    * @return   none
    * @access   string
    */
       
    function _quickFormCalls()   
    {
        
        global $_HTML_TEMPLATE_FLEXY;
        $name = $this->getAttribute('NAME');
        if ($this->tag == 'FORM') {
            $name = '__ALL__';
        }
        foreach($this->attributes as $k=>$v) {
            if (substr($k,0,4) == 'RULE') {
                $args = explode('|',$this->getAttribute($k));
                array_unshift($args,$name);
                call_user_func_array(array(&$_HTML_TEMPLATE_FLEXY['quickform'],'addRule'), $args);
            }
            if (substr($k,0,4) == 'FILTER') {
                $_HTML_TEMPLATE_FLEXY['quickform']->addFilter($name,$this->getAttribute($k));
            }
        }
    }
}

 
 
   
?>