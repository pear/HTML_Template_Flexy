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
    * is it in if statement with a method?
    *
    * @var boolean
    * @access public
    */
    var $isConditional;
    /**
    * if the statement is negative = eg. !somevar..
    * @var string
    * @access public
    */
    var $isNegative = '';
 
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
        //var_dump($value);
        $method = $value[0];
        if (substr($value[0],0,3) == 'if:') {
            $this->isConditional = true;
            if ($value[0]{3} == '!') {
                $this->isNegative = '!';
                $method = substr($value[0],4);
            } else {
                $method = substr($value[0],3);
            }
        }
        
        if (strpos($method,":")) {
            list($method,$this->modifier) = explode(':',$method);
        }
        $this->method = $method;
        
        $this->args = $value[1];
        // modifier TODO!
        
    }
     /**
    * toString - generate PHP code 
    * @see parent::toString(), $this->pullState()
    */
    
    function toString() {

        
        // ignore modifier at present!!
        $prefix = 'echo ';
        $suffix = '';
        switch ($this->modifier) {
            case 'h':
                break;
            case 'u':
                $prefix = "echo urlencode(";
                $suffix = ')';
                break;
            default:
                $prefix = "echo htmlspecialchars(";
                // add language ?
                $suffix = ')';
        }
        
        
        
        
        
        
        if ($this->isConditional) {
            $prefix = 'if ('.$this->isNegative;
            $this->pushState();
            $suffix = ')';
        }  
        
        
        // check that method exists..
        // if (method_exists($object,'method');
        $bits = explode('.',$this->method);
        $method = array_pop($bits);
        
        $object = implode('.',$bits);
        
        $prefix = 'if (isset('.$this->toVar($object).
            ') && method_exists('.$this->toVar($object) .",'{$method}')) " . $prefix;
        
        
        
        $ret = '<?php ' . $prefix;
        $ret .=  $this->toVar($this->method) . "(";
        $s =0;
        
        foreach($this->args as $a) {
             
            if ($s) {
                $ret .= ",";
            }
            $s =1;
            if ($a{0} == '#') {
                $ret .= '"'. addslashes(substr($a,1,-2)) . '"';
                continue;
            }
            $ret .= $this->toVar($a);
            
        }
        $ret .= ")" . $suffix;
        
        if ($this->isConditional) {
            $ret .= ' { ';
        } else {
            $ret .= ";";
        }
        $ret .= ' ?>';
        
        
        return $ret;
            
        
        
    }

}


 
   
?>