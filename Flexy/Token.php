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
//
//  This is the master Token file for The New Token driver Engine.
//  All the Token output, and building routines are in here.
//
//  Note overriden methods are not documented unless they differ majorly from
//  The parent.
//

$GLOBALS['_HTML_TEMPLATE_FLEXY_TOKEN']['state'] = 0;
$GLOBALS['_HTML_TEMPLATE_FLEXY_TOKEN']['statevars'] = array();

/**
* Base Class for all Tokens.
*
* @abstract Provides the static Create Method, and default toHTML() methods
*
*/

class HTML_Template_Flexy_Token {
    
    /**
    * the token type (Depreciated when we have classes for all tokens
    *
    * @var string
    * @access public
    */
    var $token;
    /**
    * the default value (normally a string)
    *
    * @var string
    * @access public
    */
    var $value;
    var $line;
    
    /**
    * Create a Token
    *
    * Standard factory method.. - with object vars.
    * ?? rename me to factory?
    * @param   string      Token type
    * @param   mixed       Initialization settings for token
    * @param   int   line that the token is defined.
    * 
    *
    * @return   object    Created Object
    * @access   public
    */
  
    function create($token,$value,$line) {
        $c = 'HTML_Template_Flexy_Token_'.$token;
        $t = new HTML_Template_Flexy_Token;
        if (class_exists($c)) {
            $t = new $c;
        }
        $t->token = $token;
        $t->setValue($value);
        $t->line = $line;
        
        return $t;
    }
    
    /**
    * Standard Value iterpretor
    *
    * @param   mixed    value recieved from factory method

    * @return   none
    * @access   public
    */
  
    function setValue($value) {
        $this->value = $value;
    }

    /**
    * generate HTML after doing flexy manipulations..
    *
    * @return   string   HTML
    * @access   public
    */
      
    function toHTML() {
        if (is_array($this->value)) {
            var_dump($this);
            exit;
        }
        return $this->value;
    }
    /* ======================================================== */
    /* variable STATE management 
    *
    * raw variables are assumed to be $this->, unless defined by foreach..
    * it also monitors syntax - eg. end without an if/foreach etc.
    */

    /**
    * reset the state = do this before parsing another file.
    *
    * @access   public
    */
    function reset() {
        $GLOBALS['_HTML_TEMPLATE_FLEXY_TOKEN']['state'] = 0;
    }
    
    /**
    * tell the generator you are entering a block
    *
    * @access   public
    */
    function pushState() {
        $GLOBALS['_HTML_TEMPLATE_FLEXY_TOKEN']['state']++;
        $s = $GLOBALS['_HTML_TEMPLATE_FLEXY_TOKEN']['state'];
        
        $GLOBALS['_HTML_TEMPLATE_FLEXY_TOKEN']['statevars'][$s] = array(); // initialize statevars
    }
    /**
    * tell the generator you are entering a block
    *
    * @return  boolean  parse error - out of bounds
    * @access   public
    */
    function pullState() {
        $s = $GLOBALS['_HTML_TEMPLATE_FLEXY_TOKEN']['state'];
        $GLOBALS['_HTML_TEMPLATE_FLEXY_TOKEN']['statevars'][$s] = array(); // initialize statevars
        $GLOBALS['_HTML_TEMPLATE_FLEXY_TOKEN']['state']--;
        if ($s<0) {
            return false;
        }
        return true;
    }
     /**
    * get the real variable name formated x.y.z => $this->x->y->z
    * if  a variable is in the stack it return $x->y->z
    *
    * @return  string PHP variable 
    * @access   public
    */
    
    function toVar($s) {
    
        $parts = explode(".",$s);
        $ret = $this->findVar($parts[0]);
        array_shift($parts);
        if (!$parts) {
            return $ret;
        }
        foreach($parts as $p) {
            $ret .= "->{$p}";
        }
        return $ret;
    }
    /**
    * do the stack lookup on the variable
    *
    * @return  string PHP variable 
    * @access   public
    */
    
    function findVar($string) {
        for ($s = $GLOBALS['_HTML_TEMPLATE_FLEXY_TOKEN']['state']; $s > 0; $s--) {
            if (in_array($string, $GLOBALS['_HTML_TEMPLATE_FLEXY_TOKEN']['statevars'][$s])) {
                return '$'.$string;
            }
        }
        return '$this->'.$string;
    }
    /**
    * add a variable to the stack.
    *
    * @param  string PHP variable 
    * @access   public
    */
    
    function pushVar($string) {
        $s = $GLOBALS['_HTML_TEMPLATE_FLEXY_TOKEN']['state'];
        $GLOBALS['_HTML_TEMPLATE_FLEXY_TOKEN']['statevars'][$s][] = $string;
    }
    
     
}

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
    var $closetag; // alias to closing tag.
    
    
    
    /**
    * Setvalue - gets name, attribute as an array
    * @see parent::setValue()
    */
  
    function setValue($value) {
        
        $this->tag = $value[0];
        $this->UCtag = strtoupper($value[0]);
        if (isset($value[1])) {
            $this->attributes = $value[1];
        }
        if (in_array($this->UCtag,array("INPUT","TEXTAREA"))) {
            $this->toFormElement();
        }
    }
    /**
    * toHTML - display tag, attributes, postfix and any code in attributes.
    * @see parent::toHTML()
    */
    function toHTML() {
        $ret =  "<". $this->tag;
        foreach ($this->attributes as $k=>$v) {
            if ($v === null) {
                $ret .= " $k";
                continue;
            }
            if (is_string($v)) {
                $ret .=  " {$k}={$v}";
                continue;
            }
            if (is_object($v)) {
                $ret .= " " .$v->toHTML();
                continue;
            }
                
            
            $ret .=  " {$k}=";
            foreach($v as $item) {
                if (is_string($item)) {
                    $ret .= $item;
                    continue;
                }
                $ret .= $item->toHTML();
            }
        }
        $ret .= ">";
        if ($this->postfix) {
            foreach ($this->postfix as $e) {
                $ret .= $e->toHTML();
            }
        }
        return $ret;
    }
    
    /**
    * toFormElement  = takes an input field and generates related PHP code.
    *
    * Eg. filling in the value with  $this->{fieldname}, adding in 
    * echo $this->errors['fieldname'] at the end.
    * TODO : formating using DIV tags, and support for 'required tag'
    *
    * @return   none
    * @access   public
    */
  
    function toFormElement() {
        // form elements : format:
        //value - fill out as PHP CODE
        $name = $this->getAttribute('name');
        $type = strtoupper($this->getAttribute('type'));
        switch ($type) {
            case "CHECKBOX":
                $this->attributes['checked'] = 
                    $this->create("PHP",
                    "<?php if (\$this->{$name}) { ?>CHECKED<?php } ?>",
                    $this->line);
                break;
            default:
                $this->attributes['value'] = array(
                    "\"",
                    $this->create("Var","{".$name.":u}",$this->line),
                    "\"");
               break;
            
        }
        if ($type == "HIDDEN") {
            return;
        }
        
        $this->postfix = array(
            $this->create("PHP", "<?php if (isset(\$this->errors['".$name."'])) { ".
                "echo  htmlspecialchars(\$this->errors['".$name. "']); } ?>",$this->line));
        // this should use <div name="form.error"> or something...
            
        
    }
    /**
    * getAttribute = reads an attribute value and strips the quotes 
    *
    * TODO : sort out case issues...
    * does not handle valuse with flexytags in
    *
    * @return   none
    * @access   string
    */
    function getAttribute($key) {
        // really need to do a foreach/ strtoupper
        if (!isset($this->attributes[$key])) {
            return '';
        }
        $v = $this->attributes[$key];
        switch($v{0}) {
            case "\"":
            case "'":
                return substr($v,1,-1);
            default:
                return $v;
        }
    }
    
    
        
}


/**
* The closing HTML Tag = eg. /Table or /Body etc.
*
* @abstract 
* This just extends the generic HTML tag 
*
*/

class HTML_Template_Flexy_Token_EndTag extends HTML_Template_Flexy_Token_Tag { }


/**
* Class to handle If statements
*
*
*/
class HTML_Template_Flexy_Token_If extends HTML_Template_Flexy_Token{ 
    /**
    * Condition for the if statement.
    * @var string // a variable
    * @access public
    */
    
    var $condition;
    /**
    * Setvalue - a string
    * @see parent::setValue()
    */
    function setValue($value) {
        //var_dump($value);
        $this->condition=$value;
    }
    /**
    * toHTML - generate PHP code 
    * @see parent::toHTML(), $this->toVar()
    */
    function toHTML() {
        $ret = "<?php if(@".$this->toVar($this->condition) .")  { ?>";
        $this->pushState();
        return $ret;
        
    }

}


/**
* Class to handle Else
*
*
*/
class HTML_Template_Flexy_Token_Else extends HTML_Template_Flexy_Token {
    /**
    * toHTML - generate PHP code 
    * @see parent::toHTML(), $this->pullState()
    */
    function toHTML() {
        // pushpull states to make sure we are in an area.. - should really check to see 
        // if the state it is pulling is a if...
        if ($this->pullState() === false) {
            echo "Unmatched End on Line {$this->line}";
            return false;
        }
        $this->pushState();
        return "<?php } else {?>";
    }


}



/**
* Class to handle End statements (eg. close brakets)
*
*
*/
class HTML_Template_Flexy_Token_End extends HTML_Template_Flexy_Token { 
    /**
    * toHTML - generate PHP code 
    * @see parent::toHTML(), $this->pullState()
    */
    function toHTML() {
        // pushpull states to make sure we are in an area.. - should really check to see 
        // if the state it is pulling is a if...
        if ($this->pullState() === false) {
            echo "Unmatched End on Line {$this->line}";
            return false;
        }
         
        return "<?php } ?>";
    }

}



/**
* Class to handle foreach statements
*
*
*/
class HTML_Template_Flexy_Token_Foreach extends HTML_Template_Flexy_Token {
    
    /**
    * variable to loop on. 
    *
    * @var string
    * @access public
    */
    var $loopOn = '';
    /**
    * key value
    *
    * @var string
    * @access public
    */
    var $key    = '';
    /**
    * optional value (in key=>value pair)
    *
    * @var string
    * @access public
    */
    var $value  = ''; 
    
    /**
    * Setvalue - a array of all three (last one optional)
    * @see parent::setValue()
    */
  
    function setValue($value) {
        $this->loopOn=$value[0];
        $this->key=$value[1];
        $this->value=@$value[2];
    }
    /**
    * toHTML - generate PHP code 
    * @see parent::toHTML(), $this->pullState()
    */
    function toHTML() {
        $ret = "<?php if (is_array(".
            $this->toVar($this->loopOn) . ")) " .
            "foreach(@".$this->toVar($this->loopOn). " ";
        $ret .= "as \${$this->key}";
        if ($this->value) {
            $ret .=  " => \${$this->value}";
        }
        $ret .= ") { ?>";
        
        $this->pushState();
        $this->pushVar($this->key);
        $this->pushVar($this->value);
        return $ret;
    }

}

/**
* Class to handle include statements
* TODO!!!
*
*
*/
class HTML_Template_Flexy_Token_Include extends HTML_Template_Flexy_Token{ }

/**
* Class to handle variable output
*  *
*
*/

class HTML_Template_Flexy_Token_Var extends HTML_Template_Flexy_Token { 
    
    /**
    * variable modifier (h = raw, u = urlencode, none = htmlspecialchars)
    *
    * @var char
    * @access public
    */
    var $modifier;
    /**
    * Setvalue - at present raw text.. - needs sorting out..
    * @see parent::setValue()
    */
    function setValue($value) {
        // comes in as raw {xxxx}, {xxxx:h} or {xxx.yyyy:h}
        $raw = substr($value,1,-1);
        if (strpos($raw,":")) {
            list($raw,$this->modifier) = explode(':',$raw);
        }
        $this->value = $raw;
    }
    /**
    * toHTML - generate PHP code 
    * @see parent::toHTML(), $this->pullState()
    */
    function toHTML() {
        // ignore modifier at present!!
        return "<?php echo htmlspecialchars(" . $this->toVar($this->value) . "); ?>";
    }

}
/**
* Class to handle method calls
*  *
*
*/

class HTML_Template_Flexy_Token_Method extends HTML_Template_Flexy_Token { 
    /**
    * variable modifier (h = raw, u = urlencode, none = htmlspecialchars)
    * TODO
    * @var char
    * @access public
    */
    var $modifier;
    /**
    * Method name
    *
    * @var char
    * @access public
    */
    var $method;
     /**
    * arguments, either variables or literals eg. #xxxxx yyyy#
    * 
    * @var array
    * @access public
    */
    var $args= array();
    /**
    * setvalue - at present array method, args (need to add modifier)
    * @see parent::setValue()
    */
    
    function setValue($value) {
        $this->method = $value[0];
        array_shift($value);
        $this->arguments = $value;
        // modifier TODO!
        
    }
     /**
    * toHTML - generate PHP code 
    * @see parent::toHTML(), $this->pullState()
    */
    
    function toHTML() {
        // ignore modifier at present!!
        $ret = "<?php echo htmlspecialchars(" . $this->toVar($this->value) . "(".
        $s =0;
        foreach($this->args as $a) {
            if ($s) {
                $ret .= ",";
            }
            $ret .= $this->toVar($a);
            $s =1;
        }
        $ret .= ")); ?>";
        return $ret;
            
        
        
    }

}


 
   
?>