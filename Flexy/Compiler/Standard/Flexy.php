<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2003 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.02 of the PHP license,      |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors:  Alan Knowles <alan@akkbhome.com>                           |
// +----------------------------------------------------------------------+
//
// $Id$
//
//  Handler code for the <flexy: namespace
//

/**
* the <flexy:XXXX namespace
* 
* 
* at present it handles
*       <flexy:toJavascript flexy:prefix="Javascript_prefix"  javscriptName="PHPvar" .....>
*       <flexy:include src="xxx.htm">
*
*
*
* @version    $Id$
*/

class HTML_Template_Flexy_Compiler_Standard_Flexy  {

        
    /**
    * Parent Compiler for 
    *
    * @var  object  HTML_Template_Flexy_Compiler  
    * 
    * @access public
    */
    var $compiler;

   
    /**
    * The current element to parse..
    *
    * @var object
    * @access public
    */    
    var $element;
    
    
    
    
    
    /**
    * toString - display tag, attributes, postfix and any code in attributes.
    * Relays into namspace::method to get results..
    *
    * 
    * @see parent::toString()
    */
    function toString($element) 
    {
        
        list($namespace,$method) = explode(':',$element->oTag);
        if (!strlen($method)) {
            return '';
        }
        // things we dont handle...
        if (!method_exists($this,$method.'ToString')) {
            return '';
        }
        return $this->{$method.'ToString'}($element);
        
    }
   /**
    * toJavascript handler
    * <flexy:toJavascript flexy:prefix="some_prefix_"  javascriptval="php.val" ....>
    * 
    * @see parent::toString()
    */
    
    function toJavascriptToString($element) {
        $ret = $this->compiler->appendPhp( "require_once 'HTML/Javascript/Convert.php';");
        $ret .= $this->compiler->appendHTML("\n<script type='text/javascript'>\n");
        $prefix = ''. $element->getAttribute('FLEXY:PREFIX');
        
        
        foreach ($element->attributes as $k=>$v) {
            // skip directives..
            if (strpos($k,':')) {
                continue;
            }
            if ($k == '/') {
                continue;
            }
            $v = substr($v,1,-1);
            $ret .= $this->compiler->appendPhp(
                '$__tmp = HTML_Javascript_Convert::convertVar('.$element->toVar($v) .',\''.$prefix . $k.'\',true);'.
                'echo (PEAR::isError($__tmp)) ? ("<pre>".print_r($__tmp,true)."</pre>") : $__tmp;');
            $ret .= $this->compiler->appendHTML("\n");
        }
        $ret .= $this->compiler->appendHTML("</script>");
        return $ret;
    }
    /**
    * include handler
    * <flexy:include src="test.html">
    * 
    * @see parent::toString()
    */
    function includeToString($element) {
        // this is disabled by default...
        // we ignore modifier pre/suffix
    
    
    
       
        $arg = $element->getAttribute('SRC');
        if (!$arg) {
            return $this->compiler->appendHTML("<B>Flexy:Include without a src=filename</B>");
        }
        
        // compile the child template....
        // output... include $this->options['compiled_templates'] . $arg . $this->options['locale'] . '.php'
        return $this->compiler->appendPHP( "\n".
                "\$x = new HTML_Template_Flexy(\$this->options);\n".
                "\$x->compile('{$arg}');\n".
                "\$x->outputObject(\$t);\n"
            );
    
    }
    
    /**
    * Convert flexy tokens to HTML_Template_Flexy_Elements.
    *
    * @param    object token to convert into a element.
    * @return   object HTML_Template_Flexy_Element
    * @access   public
    */
    function toElement($element) {
       return '';
    }
        
    

}

 