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
$GLOBALS['_HTML_TEMPLATE_FLEXY_TOKEN']['activeForm'] = '';
$GLOBALS['_HTML_TEMPLATE_FLEXY_TOKEN']['tokens'] = array();
/**
* Base Class for all Tokens.
*
* @abstract Provides the static Create Method, and default toString() methods
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
    /**
    * the line the token is from
    *
    * @var int
    * @access public
    */
    
    
    var $line;
    
    /**
    * factory a Token
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
  
    function factory($token,$value,$line) {
        // try not to reload the same class to often
        static $loaded = array();
        
        if (!isset($loaded[$token])) {
            @include 'HTML/Template/Flexy/Token/'.$token.'.php';
            $loaded[$token] = true;
        }
            
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
      
    function toString() {
        if (is_array($this->value)) {
            var_dump($this);
            exit;
        }
        $ret = $this->value;
        $ret .= $this->childrentoString();
        if ($this->close) {
            $ret .= $this->close->toString();
        }
        
        return $ret;
    }
    
    /**
    * generate HTML from children after doing flexy manipulations..
    *
    * @return   string   HTML
    * @access   public
    */
    function childrentoString() {
        if (!$this->children) {
            return '';
        }
        if ($this->ignoreChildren) {
            return;
        }
        $ret = '';
        //echo "output $this->id";
        //new Gtk_VarDump($this);
        foreach ($this->children as $child) {
            if (!$child) {
                continue;
            }
            $ret .= $child->toString();
        }
        return $ret;
    }
    
    
    
    /* ======================================================= */
    /* Token Managmenet = parse and store all the tokens in 
     * an associative array and tree.
     */
   
    /**
    * Run a Tokenizer and Store its results
    * It should build a DOM Tree of the HTML
    * 
    * @param   object    Tokenizer to run.. - Theoretically other Tokenizers could be done for email,rtf etc.
    *
    * @access   public
    * @return   base token (really a dummy token, which contains the tree)
    * @static
    */
  
    function buildTokens($tokenizer) 
    {
    
        global $_HTML_TEMPLATE_FLEXY_TOKEN;
        
        // first record is a filler - to stick all the children on !
        // reset my globals..
  
        $_HTML_TEMPLATE_FLEXY_TOKEN['statevars'] = array();
        $_HTML_TEMPLATE_FLEXY_TOKEN['state'] = 0;
        
        
        $_HTML_TEMPLATE_FLEXY_TOKEN['tokens'] = array(new HTML_Template_Flexy_Token);
        $_HTML_TEMPLATE_FLEXY_TOKEN['tokens'][0]->id =0;
        $i=1;
        
        
        // initialize state - this trys to make sure that
        // you dont do to many elses etc.
       
        
        // step one just tokenize it.
        while ($t = $tokenizer->yylex()) {  
            
            if ($t == HTML_TEMPLATE_FLEXY_TOKEN_ERROR) {
                PEAR::raiseError('HTML_Template_Flexy::Syntax error in Template line:'. $t->line,
                    null,PEAR_ERROR_DIE);
            }
            if ($t == HTML_TEMPLATE_FLEXY_TOKEN_NONE) {
                continue;
            }
           
            $i++;
            $_HTML_TEMPLATE_FLEXY_TOKEN['tokens'][$i] = $tokenizer->value;
            $_HTML_TEMPLATE_FLEXY_TOKEN['tokens'][$i]->id = $i;
            //print_r($res[$i]);
             
        }
        $res = &$_HTML_TEMPLATE_FLEXY_TOKEN['tokens'];
        // connect parent and child tags.
        $stack = array();
        $total = $i +1;
        for($i=1;$i<$total;$i++) {
            //echo "Checking TAG $i\n";
            if (!@$res[$i]->tag) {
                continue;
            }
            if ($res[$i]->tag{0} == '/') { // it's a close tag..
                //echo "GOT END TAG: {$res[$i]->tag}\n";
                $tag = strtoupper(substr($res[$i]->tag,1));
                if (!isset($stack[$tag]['pos'])) {
                    continue; // unmatched
                }
                $npos = $stack[$tag]['pos'];
                //echo "matching it to {$stack[$tag][$npos]}\n";
                $_HTML_TEMPLATE_FLEXY_TOKEN['tokens'][$stack[$tag][$npos]]->close = &$_HTML_TEMPLATE_FLEXY_TOKEN['tokens'][$i];
                $stack[$tag]['pos']--;
                if ($stack[$tag]['pos'] < 0) {
                    // too many closes - just ignore it..
                    $stack[$tag]['pos'] = 0;
                }
                continue;
            }
            // new entry on stack..
            $tag = strtoupper($res[$i]->tag);
            
            if (!isset($stack[$tag])) {
                $npos = $stack[$tag]['pos'] = 0;
            } else {
                $npos = ++$stack[$tag]['pos'];
            }
            $stack[$tag][$npos] = $i;
        }
                
        // create a dummy close for the end
        $i = $total;
        $_HTML_TEMPLATE_FLEXY_TOKEN['tokens'][$i] = new HTML_Template_Flexy_Token;
        $_HTML_TEMPLATE_FLEXY_TOKEN['tokens'][$i]->id = $total;
        $_HTML_TEMPLATE_FLEXY_TOKEN['tokens'][0]->close = &$_HTML_TEMPLATE_FLEXY_TOKEN['tokens'][$total];
        
        // now is it possible to connect children...
        // now we need to GLOBALIZE!! - 
        
        
        $_HTML_TEMPLATE_FLEXY_TOKEN['tokens'] = $res;
        
        HTML_Template_Flexy_Token::buildChildren(0);
        //new Gtk_VarDump($_HTML_TEMPLATE_FLEXY_TOKEN['tokens'][0]);
       
        return $_HTML_TEMPLATE_FLEXY_TOKEN['tokens'][0];
    }
    /**
    * Matching closing tag for a Token
    *
    * @var object|none  optional closing tag
    * @access public 
    */
    
  
    var $close;
           
    /**
    * array of children to each object. 
    *
    * @var array
    * @access public|private
    */
    
  
    var $children = array();
    
    /**
    * Build the child array for each element.
    * 
    * @param   int  id of node to add children to.
    *
    * @access   public
    * @static
    */
    function buildChildren($id) 
    {
        global $_HTML_TEMPLATE_FLEXY_TOKEN;
        
        $base = &$_HTML_TEMPLATE_FLEXY_TOKEN['tokens'][$id];
        $base->children = array();
        $start = $base->id +1;
        $end = $base->close->id;
        for ($i=$start; $i<$end; $i++) {
            $base->children[] = &$_HTML_TEMPLATE_FLEXY_TOKEN['tokens'][$i];
            if (isset($_HTML_TEMPLATE_FLEXY_TOKEN['tokens'][$i]->close)) {
            
                // if the close id is greater than my id - ignore it! - 
                if ($_HTML_TEMPLATE_FLEXY_TOKEN['tokens'][$i]->close->id > $end) {
                    continue;
                }
                HTML_Template_Flexy_Token::buildChildren($i);
                $i = $_HTML_TEMPLATE_FLEXY_TOKEN['tokens'][$i]->close->id;
            }
        }
    }
            
            
            
    /**
    * Flag to ignore children - Used to block output for select/text area etc.
    * may not be required as I moved the Tag parsing into the toString ph
    *
    * @var boolean ingore children
    * @access public
    */
    
  
    var $ignoreChildren = false;
    
    
    
     
    /* ======================================================== */
    /* variable STATE management 
    *
    * raw variables are assumed to be $this->, unless defined by foreach..
    * it also monitors syntax - eg. end without an if/foreach etc.
    */
 
    
    /**
    * tell the generator you are entering a block
    *
    * @access   public
    */
    function pushState() 
    {
        global $_HTML_TEMPLATE_FLEXY_TOKEN;
        
        $_HTML_TEMPLATE_FLEXY_TOKEN['state']++;
        $s = $_HTML_TEMPLATE_FLEXY_TOKEN['state'];
        
        $_HTML_TEMPLATE_FLEXY_TOKEN['statevars'][$s] = array(); // initialize statevars
    }
    /**
    * tell the generator you are entering a block
    *
    * @return  boolean  parse error - out of bounds
    * @access   public
    */
    function pullState() 
    {
        global $_HTML_TEMPLATE_FLEXY_TOKEN;
        
        $s = $_HTML_TEMPLATE_FLEXY_TOKEN['state'];
        $_HTML_TEMPLATE_FLEXY_TOKEN['statevars'][$s] = array(); // initialize statevars
        $_HTML_TEMPLATE_FLEXY_TOKEN['state']--;
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
        $ret =  $this->findVar($parts[0]);
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
    
    function findVar($string) 
    {
        global $_HTML_TEMPLATE_FLEXY_TOKEN;
    
        if ($string == 't') {
            return '$t';
        }
        for ($s = $_HTML_TEMPLATE_FLEXY_TOKEN['state']; $s > 0; $s--) {
            if (in_array($string, $_HTML_TEMPLATE_FLEXY_TOKEN['statevars'][$s])) {
                return '$'.$string;
            }
        }
        return '$t->'.$string;
    }
    /**
    * add a variable to the stack.
    *
    * @param  string PHP variable 
    * @access   public
    */
    
    function pushVar($string) 
    {
        global $_HTML_TEMPLATE_FLEXY_TOKEN;
        $s = $_HTML_TEMPLATE_FLEXY_TOKEN['state'];
        $_HTML_TEMPLATE_FLEXY_TOKEN['statevars'][$s][] = $string;
    }
    
     
}
 
   
?>