<?php


/**
* Compiler That deals with standard HTML Tag output.
* Since it's pretty complex it has it's own class.
* I guess this class should deal with the main namespace
* and the parent (standard compiler can redirect other namespaces to other classes.
*
* one instance of these exists for each namespace.
*
*
* @version    $Id$
*/

class HTML_Template_Flexy_Compiler_Standard_Tag {

        
    /**
    * Parent Compiler for 
    *
    * @var  object  HTML_Template_Flexy_Compiler  
    * 
    * @access public
    */
    var $compiler;

    /**
    *   
    * Factory method to create Tag Handlers
    *
    * $type = namespace eg. <flexy:toJavascript loads Flexy.php
    * the default is this... (eg. Tag)
    * 
    * 
    * @param   string    Namespace handler for element.
    * @param   object   HTML_Template_Flexy_Compiler  
    * 
    *
    * @return    object    tag compiler
    * @access   public
    */
    
    function &factory($type,&$compiler) {
        if (!$type) {
            $type = 'Tag';
        }
        include_once 'HTML/Template/Flexy/Compiler/Standard/' . ucfirst(strtolower($type)) . '.php';
        
        $class = 'HTML_Template_Flexy_Compiler_Standard_' . $type;
        if (!class_exists($class)) {
            return false;
        }
        $ret = new $class;
        $ret->compiler = &$compiler;
        return $ret;
    }
        
        
    /**
    * The current element to parse..
    *
    * @var object
    * @access public
    */    
    var $element;
    
    /**
    * Flag to indicate has attribute flexy:foreach (so you cant mix it with flexy:if!)
    *
    * @var boolean
    * @access public
    */    
    var $hasForeach = false;
    
    
    
    
    /**
    * toString - display tag, attributes, postfix and any code in attributes.
    * Note first thing it does is call any parseTag Method that exists..
    *
    * 
    * @see parent::toString()
    */
    function toString($element) 
    {
        
        global $_HTML_TEMPLATE_FLEXY_TOKEN;
         
        // store the element in a variable
        $this->element = $element;
       // echo "toString: Line {$this->element->line} &lt;{$this->element->tag}&gt;\n"; 
        
        // if the FLEXYSTARTCHILDREN flag was set, only do children
        // normally set in BODY tag.
        // this will probably be superseeded by the Class compiler.
         
        if ($element->startChildren) {
            
            return $element->compileChildren($this->compiler);
        }
        
        $flexyignore = $this->parseAttributeIgnore();
        
        // rewriting should be done with a tag.../flag.
        
        $this->reWriteURL("HREF");
        $this->reWriteURL("SRC");
        
        // handle elements
        if (($ret =$this->parseTags()) !== false) {
            //echo "PARSETAGS RET";
            return $ret;
        }
        // these add to the close tag..
        
        $ret  = $this->parseAttributeForeach();
        $ret .= $this->parseAttributeIf();
        
        // spit ou the tag and attributes.
        
        $ret .=  "<". $element->oTag;
      
        foreach ($element->attributes as $k=>$v) {
            // if it's a flexy tag ignore it.
            
            
            if (strtoupper($k) == 'FLEXY:RAW') {
                if (!is_array($v) || !isset($v[1]) || !is_object($v[1])) {
                    PEAR::raiseError(
                    'flexy:raw only accepts a variable or method call as an argument, eg.'.
                    ' flexy:raw="{somevalue}" you provided something else.' .
                    " Error on Line {$this->element->line} &lt;{$this->element->tag}&gt;",
                     null, PEAR_ERROR_DIE);
                }
                 
                $ret .= ' ' . $v[1]->compile($this->compiler);
                continue;
            
            }
            
            if (strtoupper(substr($k,0,6)) == 'FLEXY:') {
                continue;
            }
            // true == an attribute without a ="xxx"
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
                $ret .= $v->compile($this->compiler);
                continue;
            }
            
            // otherwise its a key="sometext{andsomevars}"
            
            $ret .=  " {$k}=";
            
            foreach($v as $item) {
                if (is_string($item)) {
                    $ret .= $item;
                    continue;
                }
                 
                $ret .= $item->compile($this->compiler);
            }
        }
        $ret .= ">";
        
        // post stuff this is probably in the wrong place...
        
        if ($element->postfix) {
            foreach ($element->postfix as $e) {
                $ret .= $e->compile($this->compiler);
            }
        } else if ($this->element->postfix) { // if postfixed by self..
            foreach ($this->element->postfix as $e) {
                $ret .= $e->compile($this->compiler);
            }
        }
        // output the children.
        
        $ret .= $element->compileChildren($this->compiler);
        
        // output the closing tag.
        
        if ($element->close) {
            
            $ret .= $element->close->compile($this->compiler);
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
        
        if ($this->element->getAttribute('FLEXY:IGNORE') !== false) {
            $_HTML_TEMPLATE_FLEXY_TOKEN['flexyIgnore'] = true;
            $this->element->clearAttribute('FLEXY:IGNORE');
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
        $foreach = $this->element->getAttribute('FLEXY:FOREACH');
        if ($foreach === false) {
            return '';
        }
        //var_dump($foreach);
        
        $this->element->hasForeach = true;
        // create a foreach element to wrap this with.
        $foreachObj =  $this->element->factory('Foreach',
                explode(',',$foreach),
                $this->element->line);
                
        // does it have a closetag?
        if (!$this->element->close) {
            PEAR::raiseError(
                "An flexy:if attribute was found in &lt;{$this->element->name} tag without a corresponding &lt;/{$this->element->name}
                    tag on Line {$this->element->line} &lt;{$this->element->tag}&gt;",
                 null, PEAR_ERROR_DIE);
        }
        $this->element->close->postfix = array($this->element->factory("End", '', $this->element->line));

        $this->element->clearAttribute('FLEXY:FOREACH');
        return $foreachObj->compile($this->compiler);
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
        $if = $this->element->getAttribute('FLEXY:IF');
        
        if ($if === false) {
            return '';
        }
        
        if (isset($this->element->hasForeach)) {
            PEAR::raiseError(
                "You may not use FOREACH and IF tags in the same tag on Line {$this->element->line} &lt;{$this->element->tag}&gt;",
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
        
        if (!preg_match('/^[_A-Z][A-Z0-9_]*(\[[0-9]+\])?((\[|%5B)[A-Z0-9_]+(\]|%5D))*(\.[_A-Z][A-Z0-9_]*((\[|%5B)[A-Z0-9_]+(\]|%5D))*)*(\(\))?$/i',$if)) {
            PEAR::raiseError(
                "IF tags only accept simple object.variable or object.method() values on Line {$this->element->line} &lt;{$this->element->tag}&gt;",
                 null, PEAR_ERROR_DIE);
        }
        
        if (substr($if,-1) == ')') {
            $ifObj =  $this->element->factory('Method',
                    array('if:'.$ifnegative.substr($if,0,-2), array()),
                    $this->element->line);
        } else {
            $ifObj =  $this->element->factory('If', $ifnegative.$if, $this->element->line);
        }
        
        // does it have a closetag? - you must have one - so you will have to hack in <span flexy:if=..><img></span> on tags
        // that do not have close tags - it's done this way to try and avoid mistakes.
        
        
        if (!$this->element->close) {
            //echo "<PRE>";print_R($this->element);
            
            if ($this->element->getAttribute('/') !== false) {
                $this->element->postfix = array($this->element->factory("End",'', $this->element->line));
            } else {
            
                PEAR::raiseError(
                    "An flexy:if attribute was found in &lt;{$this->element->name} tag without a corresponding &lt;/{$this->element->name}
                        tag on Line {$this->element->line} &lt;{$this->element->tag}&gt;",
                     null, PEAR_ERROR_DIE);
                }
        } else {
        
            $this->element->close->postfix = array($this->element->factory("End",'', $this->element->line));
        }
        $this->element->clearAttribute('FLEXY:IF');
        return $ifObj->compile($this->compiler);
    }
    
     /**
    * Reads Tags - and ralays to parseTagXXXXXXX
    *
    *
    * @return   string | false = html output or ignore (just output the tag)
    * @access   public
    */
    
    
    function parseTags() 
    {
        global $_HTML_TEMPLATE_FLEXY_TOKEN;
        // doesnt really need strtolower etc. as php functions are not case sensitive!
        
        if ($this->element->getAttribute('FLEXY:DYNAMIC')) {
            return $this->compiler->appendPhp(
                $this->getElementPhp( $this->element->getAttribute('ID') )
            );
            
        }
            
        if ($this->element->getAttribute('FLEXY:IGNOREONLY') !== false) {
            return false;
        }
        $method = 'parseTag'.$this->element->tag;
        if (!$_HTML_TEMPLATE_FLEXY_TOKEN['flexyIgnore'] && method_exists($this,$method)) {
            return $this->$method();
            // allow the parse methods to return output.
        }
        return false;
    }
           
    /**
    * produces the code for dynamic elements
    *
    * @return   string | false = html output or ignore (just output the tag)
    * @access   public
    */
        
    function getElementPhp($id,$mergeWithName=false) {
        
        global $_HTML_TEMPLATE_FLEXY;
        static $tmpId=0;
        if (!$id) {
            
             PEAR::raiseError("Error:{$_HTML_TEMPLATE_FLEXY['filename']} on Line {$this->element->line} &lt;{$this->element->tag}&gt;: 
             Dynamic tags require an ID value",
             null, PEAR_ERROR_DIE);
        }
         
        if ((strtolower($this->element->getAttribute('type')) == 'checkbox' ) && 
                (substr($this->element->getAttribute('name'),-2) == '[]')) {
            if ($this->element->getAttribute('id') === false) {
                $id = 'tmpId'. (++$tmpId);
                $this->element->attributes['id'] = $id;
                $this->element->ucAttributes['ID'] = $id;
            } 
            $mergeWithName =  true;
        }
        
        
        
        if (isset($_HTML_TEMPLATE_FLEXY['elements'][$id])) {
           // echo "<PRE>";print_r($this);print_r($_HTML_TEMPLATE_FLEXY['elements']);echo "</PRE>";
             PEAR::raiseError("Error:{$_HTML_TEMPLATE_FLEXY['filename']} on Line {$this->element->line} in Tag &lt;{$this->element->tag}&gt;:<BR> 
             The Dynamic tag Name '$id' has already been used previously by  tag &lt;{$_HTML_TEMPLATE_FLEXY['elements'][$id]->tag}&gt;",
             null, PEAR_ERROR_DIE);
        }
        
        // this is for a case where you can use a sprintf as the name, and overlay it with a variable element..
        $_HTML_TEMPLATE_FLEXY['elements'][$id] = $this->toElement($this->element);
        
        if ($var = $this->element->getAttribute('FLEXY:NAMEUSES')) {
            
            $var = 'sprintf(\''.$id .'\','.$this->element->toVar($var) .')';
            return  
                'if (!isset($this->elements['.$var.'])) $this->elements['.$var.']= $this->elements[\''.$id.'\'];
                $this->elements['.$var.'] = $this->mergeElement($this->elements[\''.$id.'\'],$this->elements['.$var.']);
                $this->elements['.$var.']->attributes[\'name\'] = '.$var. ';
                echo $this->elements['.$var.']->toHtml();'; 
        } elseif ($mergeWithName) {
            $name = $this->element->getAttribute('NAME');
          return  
                '$element = $this->elements[\''.$id.'\'];
                $element = $this->mergeElement($element,$this->elements[\''.$name.'\']);
                echo  $element->toHtml();'; 
        
        
        } else {
           return 'echo $this->elements[\''.$id.'\']->toHtml();';
        }
    }
    
    
    /**
    * Reads an Input tag - build a element object for it
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
        
        // as a general rule, this uses name, rather than ID except on 
        // radio
        $mergeWithName = false;
        $id = $this->element->getAttribute('NAME');
        // checkboxes need more work.. - at the momemnt assume one with the same value...
        if (in_array(strtoupper($this->element->getAttribute('TYPE')), array('RADIO'))) {
            $id = $this->element->getAttribute('ID');
            if (!$id) {
                PEAR::raiseError("Error on Line {$this->element->line} &lt;{$this->element->tag}&gt: 
                 Radio Input's require an ID tag..",
                 null, PEAR_ERROR_DIE);
            }
            $mergeWithName = true;
            
        }
        if (!$id) {
            return false;
        }
        return $this->compiler->appendPhp($this->getElementPhp( $id,$mergeWithName));

    }
    
    /**
    * Deal with a TextArea tag - build a element object for it
    *
    * @return   string | false = html output or ignore (just output the tag)
    * @access   public
    */
  
    function parseTagTextArea() 
    {
         
        return $this->compiler->appendPhp(
            $this->getElementPhp( $this->element->getAttribute('NAME')));
            
        
        
    }
    /**
    * Deal with Selects - build a element object for it (unless flexyignore is set)
    *
    *
    * @return   string | false = html output or ignore (just output the tag)
    * @access   public
    */
  
    function parseTagSelect() 
    {
        return$this->compiler->appendPhp(
            $this->getElementPhp( $this->element->getAttribute('NAME')));
    }
      
    
    
    
     /**
    * Reads an Form tag - and set up the element object header etc.
    *    
    * @return   string | false = html output or ignore (just output the tag)
    * @access   public
    */
  
    function parseTagForm() 
    {
        global $_HTML_TEMPLATE_FLEXY;
        $copy = $this->element;
        $copy->children = array();
        $id = $this->element->getAttribute('NAME');
        if (!$id) {
            $id = 'form';
        }
        
        // this adds the element to the elements array.
        $old = $this->element;
        $this->element = $copy;
        $this->getElementPhp($id);
        $this->element= $old;
        
        
        return 
            $this->compiler->appendPhp('echo $this->elements[\''.$id.'\']->toHtmlnoClose();') .
            $this->element->compileChildren($this->compiler) .
            $this->compiler->appendHtml( '</form>');
    
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
        
        
        if (!is_string($original = $this->element->getAttribute($which))) {
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
        $this->element->ucAttributes[$which] = '"'. $new . '"';
    } 
    
    /**
    * Convert flexy tokens to HTML_Template_Flexy_Elements.
    *
    * @param    object token to convert into a element.
    * @return   object HTML_Template_Flexy_Element
    * @access   public
    */
    function toElement($element) {
        require_once 'HTML/Template/Flexy/Element.php';
        $ret = new HTML_Template_Flexy_Element;
        
        if (get_class($element) != 'html_template_flexy_token_tag') {
            return $element->value;
        }
        
        
        $ret->tag = strtolower($element->tag);
        $ret->attributes = $element->getAttributes();
        if (!$element->children) {
            return $ret;
        }
        //print_r($this->children);
        foreach(array_keys($element->children) as $i) {
            // not quite sure why this happens - but it does.
            if (!is_object($element->children[$i])) {
                continue;
            }
            $ret->children[] = $this->toElement($element->children[$i]);
        }
        return $ret;
    }
        
    

}

 