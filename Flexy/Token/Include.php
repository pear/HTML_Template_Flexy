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
* Class to handle include statements
* TODO!!!
*
* Include is an odd baby.. - since we are supposed to be dealing with compiled templates..
* It is the responsibility of the appliacation to make sure it has compiled the 
* included template.
* Various types of Includes should be supported
*       include:someobject.someval
*                   - maps to if (isset(....) && file_exists(....))  include ( value . '.{lang}.html')
*
*       include: somevar
*
*
*
*/
class HTML_Template_Flexy_Token_Include extends HTML_Template_Flexy_Token{ 

    
    
    function toString() {
        $v = $this->toVar($this->value);
         
        $options = $GLOBALS['_HTML_TEMPLATE_FLEXY']['currentOptions'];
       
        $ret = "<?php  if (isset($v) &&
                    file_exists(\"{$options['compileDir']}/\".{$v}.\".{$options['locale']}.php\")) 
                    include(\"{$options['compileDir']}/\".{$v}.\".{$options['locale']}.php\"); ?>";
            
        return $ret;     
    
    }




}
 
 
   
?>