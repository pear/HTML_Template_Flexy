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
// | Authors:  Alan Knowles <alan@akbkhome.com>                           |
// +----------------------------------------------------------------------+
//
// 
require_once 'HTML/QuickForm.php';
/**
* QuickForm Wrapper for HTML_Template_Flexy
*
* @abstract 
* Provides useful public methods for HTML_Template_Flexy to work with HTML_QuickForm
*
* @version    $Id$
*/
class HTML_Template_Flexy_QuickForm extends HTML_QuickForm {
    
    /**
    * Standard Form Element Template  - disappeard from QuickForm?
    * slightly different in Flexy as 
    *  a) no Table tags are used.
    *  b) uses div/class rather than fonts 
    *
    * @var string
    * @access private (although this doesnt make much sense..)
    */    
    var $_elementTemplate =
        '<!-- BEGIN required --><div class="QuickFormRequired">*</div><!-- END required -->
         <!-- BEGIN error --><div class="QuickFormError">{error}</div><br><!-- END error -->
         {element}';
    
    
    
    /**
    * get the HTML for the the form head (including javascript and hidden elements)
    *
    * @return   string    
    * @access   public
    */
  
    
    function formHeadtoHtml() 
    {
        $ret ='<script language="javascript"><!--'.$this->_buildRules() . '--></script>';
        $ret .= '<form ' . $this->_getAttrString($this->_attributes) . '>';
        foreach(array_keys($this->_elements) as $name) {
            if ($this->_element[$name]->getType() == 'hidden') {
                $ret .= $this->_element[$name]->buildElement($name);
            }
        }
        return $ret;
    }
    /**
    * get the HTML for an element by name. 
    *
    * @param string name of element to get HTML for
    *
    * @return   string    
    * @access   public
    */
    
    function elementToHtml($elementname) 
    {
        return $this->_buildElement($this->_elements[$elementname]);
    }
    /**
    * get a reference to the Elements (so you can modify them/ set stuff)
    *
    * @return   array   associative array of elementsname => element (by reference)
    * @access   public
    */
    function &getElements() 
    {
        return $this->_elements;
    }
    /**
    * load cached quickform (this) from a file - and automatically reload the classes required 
    * for the elements
    *
    * the double pass technique is about the best I could come up with.. 
    * - since the forms probably wont be that big it shouldnt be a killer on performance.
    *
    * @param    string filename of serialized data
    *
    * @return   object HTML_Template_Flexy_QuickForm
    * @access   public
    */
    function loadFromSerialFile($filename) 
    {
        // does our file exist.
        if (!file_exists($filename)) {
            return;
        }
        // double load defintion for quickform..
        $data = unserialize(file_get_contents($filename));
        foreach($data->_elements as $e=>$object) {
            $class = $object->__PHP_Incomplete_Class_Name;
            if (class_exists($class)) {
                continue;
            }
            $filename = str_replace('HTML_Template_','',$class) . '.php';
            require_once 'HTML/Template/' . $filename;
        }
        return unserialize(file_get_contents($filename));
    }

}


?>