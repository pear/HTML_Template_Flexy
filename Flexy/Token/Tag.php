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
    * HTML Tag: eg. Body or /Body - uppercase
    *
    * @var string
    * @access public
    */
    var $tag = '';
    /**
    * HTML Tag: (original case)
    *
    * @var string
    * @access public
    */
    var $oTag = '';
    /**
    * Associative array of attributes. (original case)
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
    * Associative array of attributes ucase to Original Case for attributes..
    *
    * @var array
    * @access public
    */

    var $ucAttributes = array();
    
    
    
    /**
    * postfix tokens 
    * used to add code to end of tags "<xxxx>here....children .. <close tag>"
    *
    * @var array
    * @access public
    */
    var $postfix = '';
     /**
    * prefix tokens 
    * used to add code to beginning of tags TODO  "here<xxxx>....children .. <close tag>"
    *
    * @var array
    * @access public
    */
    var $prefix = '';
     /**
    * foreach attribute value for this tag
    * used to ensure that if and foreach are not used together.
    *
    * @var array
    * @access public
    */
    var $foreach = '';
    
        
    /**
    * Alias to closing tag (built externally).
    * used to add < ? } ? > code to dynamic tags.
    * @var object alias
    * @access public
    */
    var $close; // alias to closing tag.
    
    /**
    * flag to only output the children - set in the core parser.
    *
    * @var boolean
    * @access public
    */
    var $startChildren = false;
    
    /**
    * Setvalue - gets name, attribute as an array
    * @see parent::setValue()
    */
  
    function setValue($value) 
    {
        global $_HTML_TEMPLATE_FLEXY_TOKEN;
        $this->tag = strtoupper($value[0]);
        $this->oTag = $value[0];
        if (isset($value[1])) {
            $this->attributes = $value[1];
        }
        foreach(array_keys($this->attributes) as $k) {
            $this->ucAttributes[strtoupper($k)] =&  $this->attributes[$k];
        }
       
    }
    /**
    * toString - display tag, attributes, postfix and any code in attributes.
    * Note first thing it does is call any parseTag Method that exists..
    *
    * 
    * @see parent::toString()
    */
    function toString() 
    {
        
        global $_HTML_TEMPLATE_FLEXY_TOKEN;
        
        
        // if the FLEXYSTARTCHILDREN flag was set, only do children
        // normally set in BODY tag.
        if ($this->startChildren) {
            return $this->childrenToString();
        }
        
        $flexyignore = $this->parseAttributeIgnore();
        
        // rewriting should be done with a tag.../flag.
        
        $this->reWriteURL("HREF");
        $this->reWriteURL("SRC");
        
        // handle quickforms
        if (($ret =$this->parseTags()) !== false) {
            return $ret;
        }
        
        $ret  = $this->parseAttributeForeach();
        $ret .= $this->parseAttributeIf();
        
        // spit ou the tag and attributes.
        
        $ret .=  "<". $this->oTag;
        foreach ($this->attributes as $k=>$v) {
            // if it's a flexy tag ignore it.
            
            if (strtoupper(substr($k,0,5)) == 'FLEXY') {
                continue;
            }
            
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
        
        // post stuff this is probably in the wrong place...
        
        if ($this->postfix) {
            foreach ($this->postfix as $e) {
                $ret .= $e->toString();
            }
        }
        // output the children.
        
        $ret .= $this->childrenToString();
        
        // output the closing tag.
        
        if ($this->close) {
            $ret .= $this->close->toString();
        }
        // reset flexyignore
        
        $_HTML_TEMPLATE_FLEXY_TOKEN['flexyIgnore'] = $flexyignore;
        
        return $ret;
    }
    /**
    * Reads an flexy:foreach attribute - 
    *
    *
    * @return   string to add to output.
    * @access   public
    */
    
    function parseAttributeIgnore() 
    {
    
        global $_HTML_TEMPLATE_FLEXY_TOKEN;
        
        $flexyignore = $_HTML_TEMPLATE_FLEXY_TOKEN['flexyIgnore'];
        
        if ($this->getAttribute('FLEXYIGNORE') || $this->getAttribute('FLEXY:IGNORE')) {
            
            $_HTML_TEMPLATE_FLEXY_TOKEN['flexyIgnore'] = true;
            $this->clearAttribute('FLEXYIGNORE');
            $this->clearAttribute('FLEXY:IGNORE');
        }
        return $flexyignore;

    }
    
    /**
    * Reads an flexy:foreach attribute - 
    *
    *
    * @return   string to add to output.
    * @access   public
    */
    
    function parseAttributeForeach() 
    {
        $foreach = $this->getAttribute('FOREACH') . $this->getAttribute('FLEXY:FOREACH');
        if (!$foreach) {
            return '';
        }
        $this->foreach = $foreach;
        
        $foreachObj =  $this->factory('Foreach',
                explode(',',$foreach),
                $this->line);
        // does it have a closetag?
        if (!$this->close) {
            PEAR::raiseError(
                "An flexy:if attribute was found in &lt;{$this->name} tag without a corresponding &lt;/{$this->name}
                    tag on Line {$this->line} &lt;{$this->tag}&gt;",
                 null, PEAR_ERROR_DIE);
        }
        $this->close->postfix = array($this->factory("End", '', $this->line));
        $this->clearAttribute('FOREACH');
        $this->clearAttribute('FLEXY:FOREACH');
        return $foreachObj->toString();

    }
    /**
    * Reads an flexy:if attribute - 
    *
    *
    * @return   string to add to output.
    * @access   public
    */
    
    function parseAttributeIf() 
    {
        // dont use the together, if is depreciated..
        $if = $this->getAttribute('IF') . $this->getAttribute('FLEXY:IF');
        
        if (!$if) {
            return '';
        }
        
        if ($this->foreach) {
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
        // these checks should really be in the if/method class..!!!
        
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
        
        // does it have a closetag? - you must have one - so you will have to hack in <span flexy:if=..><img></span> on tags
        // that do not have close tags - it's done this way to try and avoid mistakes.
        if (!$this->close) {
            PEAR::raiseError(
                "An flexy:if attribute was found in &lt;{$this->name} tag without a corresponding &lt;/{$this->name}
                    tag on Line {$this->line} &lt;{$this->tag}&gt;",
                 null, PEAR_ERROR_DIE);
        }
        $this->close->postfix = array($this->factory("End",'', $this->line));
        $this->clearAttribute('IF');
        $this->clearAttribute('FLEXY:IF');
        return  $ifObj->toString();
    }
    
     /**
    * Reads Tags - and realays
    *
    *
    * @return   string | false = html output or ignore (just output the tag)
    * @access   public
    */
    
    
    function parseTags() 
    {
        global $_HTML_TEMPLATE_FLEXY_TOKEN;
        // doesnt really need strtolower etc. as php functions are not case sensitive!
        $method = 'parseTag'.$this->tag;
        if (!$_HTML_TEMPLATE_FLEXY_TOKEN['flexyIgnore'] && method_exists($this,$method)) {
            return $this->$method();
            // allow the parse methods to return output.
        }
        return false;
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
        
        static $buildId =0;
        
        $type = strtoupper($this->getAttribute('TYPE'));
            
        $e = false;
        
        
        if (!$_HTML_TEMPLATE_FLEXY['quickform']) {
            return false;
        }
        
        
        
        switch ($type) {
            case "CHECKBOX":
                $e = &$_HTML_TEMPLATE_FLEXY['quickform']->addElementDef(
                    array($buildId , 'checkbox',   $name,  $this->getAttribute('FLEXY:LABEL')  , '',  $this->getAttributes() ),
                    array('setChecked' => $this->getAttribute('CHECKED'))
                );
                break;
            
            case "RADIO":
                $e = &$_HTML_TEMPLATE_FLEXY['quickform']->addElementDef(
                    array($buildId , 'radio',   $name,  $this->getAttribute('FLEXY:LABEL')  ,   '',
                            $this->getAttribute('VALUE')   ,    $this->getAttributes() ),
                    array('setChecked' => $this->getAttribute('CHECKED'))
                );
                break;
            
            case "RESET":
                $e = &$_HTML_TEMPLATE_FLEXY['quickform']->addElementDef(
                    array( $buildId , 'reset' , $name,  $this->getAttribute('VALUE') , $this->getAttributes()  )
                );
                break;
                
            case "SUBMIT":
                $e = &$_HTML_TEMPLATE_FLEXY['quickform']->addElementDef(
                    array( $buildId , 'submit' ,  $name,  $this->getAttribute('VALUE') ,  $this->getAttributes() )
                );
                break;
                
            case "BUTTON":            
                $e = &$_HTML_TEMPLATE_FLEXY['quickform']->addElementDef(
                    array($buildId , 'button' , $name,  $this->getAttribute('VALUE') , $this->getAttributes() ) 
                );
                break;
                
            case "PASSWORD":     
                $e = &$_HTML_TEMPLATE_FLEXY['quickform']->addElementDef(
                   array($buildId , 'password' ,  $name,  '' ,   $this->getAttributes()) 
                );
               
                break;

            case "FILE":
                $e = &$_HTML_TEMPLATE_FLEXY['quickform']->addElementDef(
                   array($buildId , 'file' ,  $name,  '',   $this->getAttributes()) 
                );
            
                break;


            case "HIDDEN":
                $e = &$_HTML_TEMPLATE_FLEXY['quickform']->addElementDef(
                    array($buildId , 'hidden' , $name,$this->getAttribute('VALUE'),$this->getAttributes()),
                    array('setValue' => $this->getAttribute('VALUE'))
                );
                // hidden elements are displayed after the form tag.
                return '';
            
            default:
                
                $e = &$_HTML_TEMPLATE_FLEXY['quickform']->addElementDef(
                    array($buildId , 'text' ,$name,'',  $this->getAttributes()),
                    array(
                        'setSize'       => $this->getAttribute('SIZE'),
                        'setMaxLength'  => $this->getAttribute('MAXLENGTH'),
                        'setValue'      => $this->getAttribute('VALUE')
                    )
                );
                break;
            
        }
        $this->_quickFormCalls();
        
        
        // we need to use id's to stop things like radio buttons overlapping.
        
        
        return '<?php echo $this->quickform->elementToHtml("",'.$buildId++ .'); ?>';
    
        
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
             
            $e = &$GLOBALS['_HTML_TEMPLATE_FLEXY']['quickform']->addElementDef(
                array( 'textarea', $this->getAttribute('NAME'),'', $this->getAttributes()),
                array( 
                    'setValue' => $this->childrenToString(),
                    'setWrap' => $this->getAttribute('WRAP'),
                    'setRows' => $this->getAttribute('ROWS'),
                    'setCols' => $this->getAttribute('COLS')
                )
            );
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
        $GLOBALS['_HTML_TEMPLATE_FLEXY_TOKEN_TAG']['selectOptions'] = false;
        if ($GLOBALS['_HTML_TEMPLATE_FLEXY']['quickform']) {
          
                
            $GLOBALS['_HTML_TEMPLATE_FLEXY_TOKEN_TAG']['selectOptions'] = array(); 
            $GLOBALS['_HTML_TEMPLATE_FLEXY_TOKEN_TAG']['selectOptionSelected'] = '';
        } else {
            return false;
        }
        // build the options.
        $this->childrenToString();
         
        $GLOBALS['_HTML_TEMPLATE_FLEXY']['quickform']->addElementDef(
            array('select',      $this->getAttribute('NAME'),  ''),
            array(
                'setSize'        =>   $this->getAttribute('SIZE'),
                'setMultiple' =>  $this->getAttribute('MULTIPLE'),
                'SetSelected' => $GLOBALS['_HTML_TEMPLATE_FLEXY_TOKEN_TAG']['selectOptionSelected']
            ),
            $GLOBALS['_HTML_TEMPLATE_FLEXY_TOKEN_TAG']['selectOptions']  // options..
        );
                
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
        
        if ($_HTML_TEMPLATE_FLEXY_TOKEN_TAG['selectOptions'] !== false) {
            $GLOBALS['_HTML_TEMPLATE_FLEXY_TOKEN_TAG']['selectOptions'][] = array($text, $value);
            if ($this->getAttribute('SELECTED')) {
                $GLOBALS['_HTML_TEMPLATE_FLEXY_TOKEN_TAG']['selectOptionSelected'] = $value;
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
        if (isset($this->ucAttributes['FLEXYIGNORE'])) {
            return false;
        }
        if (isset($this->ucAttributes['FLEXY:IGNORE'])) {
            return false;
        }
        
        
        require_once 'HTML/Template/Flexy/QuickForm.php';
        if (!isset($GLOBALS['_HTML_TEMPLATE_FLEXY']['quickform'])) {
            $GLOBALS['_HTML_TEMPLATE_FLEXY']['quickform'] = new HTML_Template_Flexy_QuickForm;
        }
        $GLOBALS['_HTML_TEMPLATE_FLEXY']['quickform']->addElementDef(
            array(
                'form',
                $this->getAttribute('NAME'),
                $this->getAttribute('METHOD'),
                $this->getAttribute('ACTION'),
                $this->getAttribute('TARGET') ,
                $this->getAttributes() // need to do some filtering on this..
            )
        ); 
        
       
        return '<?php $this->setActiveQuickForm('. ((int) $_HTML_TEMPLATE_FLEXY_TOKEN['activeFormId']++).');' .
            'echo $this->quickform->formHeadToHtml(); ?>' .
            $this->childrenToString() .
            '</form>';
            
       
       
        //print_r($GLOBALS['_HTML_TEMPLATE_FLEXY']['quickform']);
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
        $this->ucAttributes[$which] = '"'. $new . '"';
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
        
        if (@$this->ucAttributes[$key] === true) {
            return true;
        }
        
        if (!isset($this->ucAttributes[$key])) {
            return;
        }
        // general assumption - none of the tools can do much with dynamic
        // attributes - eg. stuff with flexy tags in it.
        if (!is_string($this->ucAttributes[$key])) {
            return;
        }
        $v = $this->ucAttributes[$key];
         
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
            if (substr(strtoupper($k),0,6) == 'FLEXY:') {
                continue;
            }
            $ret[$k] = $this->getAttribute($k);
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
        foreach($this->ucAttributes as $k=>$v) {
            if (substr($k,0,6) != 'FLEXY:') {
                continue;
            }
            $kk = substr($k,6);
            
            if (substr($kk,0,4) == 'RULE') {
                $args = explode('|',$this->getAttribute($k));
                array_unshift($args,$name);
                array_unshift($args,'addRule');
                
                $_HTML_TEMPLATE_FLEXY['quickform']->addElementDef($args);
            }
            if (substr($kk,0,4) == 'FILTER') {
                $_HTML_TEMPLATE_FLEXY['quickform']->addElementDef(
                    array('addFilter',$name,$this->getAttribute($k))
                );
                
            }
        }
    }
    /**
    * clearAttributes = removes an attribute from the object.
    *
    *
    * @return   array
    * @access   string
    */
    function clearAttribute($string) {
        if (isset($this->attributes[$string])) {
            unset($this->attributes[$string]);
        }
    }
    
}

 
 
   
?>