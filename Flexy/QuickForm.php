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
        '{label}{element}
         <!-- BEGIN required --><span class="QuickFormRequired">*</span><!-- END required -->
         <!-- BEGIN error --><div class="QuickFormError">{error}</div><!-- END error -->';
    
       /* -----------------------     PUBLIC METHODS AND VARS - used outside engine. ---------------------------- */
    
    
     /**
    * flag an element as hidden (so do not display it)
    *
    * @param    string element name - name of element to hide
    * @return   none
    * @access   public
    */
    function hideElement($elementname) 
    {
        if (!isset($this->_elementIndex[$elementname])) {
            return false;
        }
        $this->_elements[$this->_elementIndex[$elementname]]->hide = true;
    }
     /**
    * A nice easy way to access elements!
    *
    * @var array associative array of name => element.
    * @access public
    */  
    
    var $elements;
    
    /* -----------------------     semi private methods - used by engine. ---------------------------- */
    
    /**
    * build the friendly elements array
    *
    * @return   none
    * @access   public
    */
    function buildElementsArray() 
    {
        foreach ($this->_elementIndex as $name => $id) {
            $this->elements[$name] = & $this->_elements[$id];
        }
    }
 
    
      
   
    
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
      
        foreach($this->_elementIndex as $name => $id) {
            if ($this->_elements[$id]->getType() == 'hidden') {
                $ret .= $this->_buildElement($this->_elements[$id]);
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
        if (isset($this->_elements[$this->_elementIndex[$elementname]]->hide) &&
            $this->_elements[$this->_elementIndex[$elementname]]->hide) {
                return '';
        }
       
        return $this->_buildElement($this->_elements[$this->_elementIndex[$elementname]]);
    }
   
    
    var $elementDefArray = array();
    /**
    * get the HTML for an element by name. 
    *
    * @param string name of element to get HTML for
    *
    * @return   string    
    * @access   public
    */
    
    function addElementDef() 
    {
        $this->elementDefArray[] = func_get_args();
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
    function loadFromSerialFile($filename,$defaults) 
    {
        // does our file exist.
        if (!file_exists($filename)) {
            return PEAR::raiseError('Flexy Quickform wrapper attempted to load non existent file :'. $filename,null,PEAR_ERROR_DIE);
            
        }
         
        $ret = false;
        
        // double load defintion for quickform..
        $data = unserialize(file_get_contents($filename));
        $form = -1;
        //echo "<PRE>LOAD:";print_r($data);echo "</PRE>";
        foreach($data  as $array) {
            //echo "<PRE>PARSE:";print_r($array);echo "</PRE>";
            if ($array[0][0] == 'form') {
                $form++;
                $ret[$form] = new HTML_Template_Flexy_QuickForm;
                array_shift($array[0]);
                // create it..
                call_user_func_array(array($ret[$form],'HTML_QuickForm'), $array[0]);
                // set the defaults.
                if (isset($defaults[$form])) {
                    $ret[$form]->setDefaults($defaults[$form]);
                } else if (isset($defaults[0])) {
                    $ret[$form]->setDefaults($defaults[0]);
                }
                continue;
            }
            if (!$ret[$form]) {
                // technically this is an error condition.
                continue;
            }
            if ($array[0][0] == 'addRule' || $array[0][0] == 'addFilter' ) {
                $method = array_shift($array[0]);
                //echo "<PRE>addrule";print_r(array(array($method), $array[0]));echo "</PRE>";
                $rr = call_user_func_array(array(&$ret[$form],$method), $array[0]);
                //echo "<PRE>addrule";print_r($rr);echo "</PRE>";
                continue;
            }
            
            
            
            $e = call_user_func_array(array($ret[$form],'createElement'),$array[0]);
           
             
            //array_pop($ret->_elements);
            if (isset($array[2])) { // options..
                foreach ($array[2] as $v) {
                     $e->addOption($v[0],$v[1]);
                }
            }
            if (!isset($array[1])) {
                $ret[$form]->addElement($e);
                
                continue;
            }
            foreach ($array[1] as $k=>$v) {
                //echo "<PRE>USR FUNC:";print_r(array( array($e,$k),$v));echo "</PRE>";
                call_user_func(array( $e,$k),$v);
            }
            $ret[$form]->addElement($e);
            
            
        }
        return $ret;
        
         
    }

}


?>
