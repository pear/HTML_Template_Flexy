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
 

    
/**
* Global variable for gettext replacement
* static object vars will be nice in PHP5 :)
*
* @var array
* @access private
*/

$GLOBALS['_HTML_TEMPLATE_FLEXY_TOKEN_TEXT']['clean'] = array(
    '$' => '\$',
    '"' => '\"',
    "'" => '\\\'',
    '\\' => '\\\\',
    "\n" => '\n',
    "\t" => '\t',
    "\r" => '\r'
);
$GLOBALS['_HTML_TEMPLATE_FLEXY_TOKEN_TEXT']['unclean'] = array_flip($GLOBALS['_HTML_TEMPLATE_FLEXY_TOKEN_TEXT']['clean']);

/**
* Class to Text - implements gettext support.
* 
*
*/

class HTML_Template_Flexy_Token_Text extends HTML_Template_Flexy_Token {
    /**
    * toString - generate PHP code 
    * this calls gettext on the string prior to outputing it.
    * does weird things to try and make sensible strings for gettext
    *
    * @see parent::toString(), $this->pullState()
    */
    
    
    function toString() {
        // if it's XML then quote it..
        
        
        if (!strlen(trim($this->value) )) {
            return $this->value;
        }
        if (!count($this->argTokens) && !$this->isWord()) {
            return $this->value;
        }
        
        $front = '';
        $rear = '';
        for ($i=0;$i<strlen($this->value); $i++) {
            if (strpos(" \n\t\r\0\x0B", $this->value{$i}) !== false) {
                $front .= $this->value{$i};
                continue;
            }
            break;
        }
         
        for ($i=strlen($this->value)-1;$i>-1; $i--) {
            if (strpos(" \n\t\r\0\x0B", $this->value{$i}) !== false) {
                $rear = $this->value{$i} . $rear;
                continue;
            }
            break;            
        }
        
        $value = trim($this->value);
        
        
        
        $value = strtr($value,$GLOBALS['_HTML_TEMPLATE_FLEXY_TOKEN_TEXT']['clean']);
        
        if (!count($this->argTokens)) {
           
            
            $GLOBALS['_HTML_TEMPLATE_FLEXY_TOKEN']['gettextStrings'][] = $value;
            if (function_exists('gettext')) {
                $value = gettext($value);
            }
            $value = strtr($value,$GLOBALS['_HTML_TEMPLATE_FLEXY_TOKEN_TEXT']['unclean']);
        
            return $front .  $value  . $rear;
        }
        //print_r($this->argTokens );
        $args = array();
        $argsMake = '';
        foreach($this->argTokens as $i=>$token) {
            
            $args[] =$token->toString();
            
        }
        $GLOBALS['_HTML_TEMPLATE_FLEXY_TOKEN']['gettextStrings'][] = $value;
        if (function_exists('gettext')) {
            $value = gettext($value);
        }
        $value = strtr($value,$GLOBALS['_HTML_TEMPLATE_FLEXY_TOKEN_TEXT']['unclean']);
        
        
        
        $bits = explode('%s',$value);
        $ret = $front;
        foreach($bits as $i=>$v) {
            $ret.=$v.@$args[$i];
        }
        
        return  $ret . $rear;
        
        
        
        
    }
      
    
      
      
    /**
    * List of argument tokens.
    *
    * @var array
    * @access public
    */
    var $argTokens = array();
    
    /**
    * Search backwards for whitespace and flexy tags to add to string.
    *
    * @return   none
    * @access   public
    */
  
    function backSearch() {
        // if this is an empty string ignore it?
        if (!strlen(trim($this->value))) {
            return;
        }
        global $_HTML_TEMPLATE_FLEXY_TOKEN;
        
        $i = $this->id -1;
        while ($i > 0) {
            if (empty($_HTML_TEMPLATE_FLEXY_TOKEN['tokens'][$i])) {
                return;
            }
            $token = $_HTML_TEMPLATE_FLEXY_TOKEN['tokens'][$i];
            
            switch (get_class($token)) {
                case 'html_template_flexy_token_text';
                    $this->value = $token->value . $this->value;
                    
                    unset($_HTML_TEMPLATE_FLEXY_TOKEN['tokens'][$i]);
                    break;
                    
                //case 'html_template_flexy_token_method';
                case 'html_template_flexy_token_var';
                    $this->value = '%s'. $this->value;
                    array_unshift($this->argTokens,$token);
                    unset($_HTML_TEMPLATE_FLEXY_TOKEN['tokens'][$i]);
                    break;
                
               
                default:
                
                    // found a stop point.
                    
                
                
                    return;
            }
            $i--;
        }
    
    }
    /**
    * Search forwards for whitespace and flexy tags to add to string.
    *
    * @param   int - id of last tag
    * @return   int - id of next tag.
    * @access   public
    */
    
    function forwardSearch($max) {
       
        global $_HTML_TEMPLATE_FLEXY_TOKEN;
        $i = $this->id +1;
        while ($i < $max) {
        
            $token = $_HTML_TEMPLATE_FLEXY_TOKEN['tokens'][$i];
            
            switch (get_class($token)) {
                case 'html_template_flexy_token_text';
                    $this->value .= $token->value;
                    unset($_HTML_TEMPLATE_FLEXY_TOKEN['tokens'][$i]);
                    break;
                    
                //case 'html_template_flexy_token_method';
                case 'html_template_flexy_token_var';
                    $this->value .= '%s';
                    $this->argTokens[] = $token;
                    unset($_HTML_TEMPLATE_FLEXY_TOKEN['tokens'][$i]);
                    break;
                default:
                    return $i - 1;
            }
            $i++;
        }
        return $i - 1;
    }
     /**
    * Simple check to see if this piece of text is a word 
    * so that gettext and the merging tricks dont try
    * - merge white space with a flexy tag
    * - gettext doesnt translate &nbsp; etc.
    *
    * @return   boolean  true if this is a word
    * @access   public
    */
    function isWord() {
        if (!strlen(trim($this->value))) {
            return false;
        }
        if (preg_match('/^\&[a-z0-9]+;$/i',trim($this->value))) {
            return false;
        }
        return  preg_match('/[a-z]/i',$this->value);
    }
    /**
    * Convert flexy tokens to HTML_Elements. (in this case - it's just a string)
    *
    *
    * @return   array
    * @access   string
    */
    function toElement() {
        return  $this->value;
    }
}


 
 
   
?>