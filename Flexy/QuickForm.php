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
    * relaying of constructor - has to be done manually !!!
    *
    * @param    see HTML_Quickform..
    * @return   none
    * @access   public
    */
    
    function HTML_Template_Flexy_QuickForm($a=null,$b=null,$c=null,$d=null,$e=null) {
    
        parent::HTML_QuickForm($a,$b,$c,$d,$e);
    
    }
    
    
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
        if (empty($this->_elementIndex)) {
            return PEAR::raiseError(
                'Flexy Template encounted an Form with no elements - 
                check the source for extra FORM tags :',null,PEAR_ERROR_DIE);
        }
        foreach ($this->_elementIndex as $name => $id) {
            echo "$name => $id<BR>";
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
        $ret ='<script language="javascript"><!--'.$this->getValidationScript() . '--></script>';
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
    
    function elementToHtml($elementname='',$buildId = 0) 
    {
        
        
        if ($elementname == '') {
            $index = $this->_buildIndex[$buildId];
        } else{
            $index = $this->_elementIndex[$elementname];
        }
        
        if (isset($this->_elements[$index]->hide) &&
            $this->_elements[$index]->hide) {
                return '';
        }
        
        if (!is_object($this->_elements[$index])) {
            //echo "<PRE>";print_r($this);
            echo "unable to find element $elementname\n";
            if (!isset($this->_elementIndex[$elementname])) {
                echo "its not in element Index!";
            } else {
                echo "it's not in elements";
            }
            return;
        }
        
        return $this->_buildElement($this->_elements[$index]);
    }
   
    /**
     * Builds the element as part of the form
     *
     * @param     array     $element    Array of element information
     * @since     1.0       
     * @access    private
     * @return    void
     * @throws    
     */
    function _buildElement(&$element)
    {
        $label       = $element->getLabel();
        $elementName = $element->getName();
        $required    = ($this->isElementRequired($elementName) && $this->_freezeAll == false);
        $error       = $this->getElementError($elementName);
        if ($element->getType() != 'hidden') {
            return $this->_wrapElement($element, $label, $required, $error);
        } else {
            return "\n" .  $element->toHtml();
        }
    } // end func _buildElement
    /**
     * Html Wrapper method for form elements (inputs...)
     *
     * @param     object    $element    Element to be wrapped
     * @since     1.0
     * @access    private
     * @return    void
     * @throws    
     */
    function _wrapElement(&$element, $label=null, $required=false, $error=null)
    {
        
        $html = '';
        if (isset($this->_templates[$element->getName()])) {
            $html = str_replace('{label}', $label, $this->_templates[$element->getName()]);
        } else {
            $html = str_replace('{label}', $label, $this->_elementTemplate);
        }
        if ($required) {
            $html = str_replace('<!-- BEGIN required -->', '', $html);
            $html = str_replace('<!-- END required -->', '', $html);
        } else {
            $html = preg_replace("/([ \t\n\r]*)?<!-- BEGIN required -->(\s|\S)*<!-- END required -->([ \t\n\r]*)?/i", '', $html);
        }
        if (isset($error)) {
            $html = str_replace('{error}', $error, $html);
            $html = str_replace('<!-- BEGIN error -->', '', $html);
            $html = str_replace('<!-- END error -->', '', $html);
        } else {
            $html = preg_replace("/([ \t\n\r]*)?<!-- BEGIN error -->(\s|\S)*<!-- END error -->([ \t\n\r]*)?/i", '', $html);
        }
        $html = str_replace('{element}', $element->toHtml(), $html);
        return $html;
    } // end func _wrapElement
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
        //echo "<PRE>STORE:";print_r($this->elementDefArray);
    }
   
    /**
    * As the form can have multiple entries for things like radio buttons etc. - this should deal with it..
    *
    * @var array associative array of {id when it was parsed} => {id when it was realized}
    * @access public
    */  
   
    var $_buildIndex = array();
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
            if (is_string($array[0][0]) && $array[0][0] == 'form') {
                //echo "GOT FORM?";
                $form++;
                
                
                //array_shift($array[0]);
                $ret[$form] = new HTML_Template_Flexy_QuickForm(@$array[0][1],@$array[0][2],@$array[0][3],@$array[0][4],@$array[0][5]);
                
                // create it..
                //echo "<PRE>";print_r($array[0]);
                //call_user_func_array(array($ret[$form],'HTML_QuickForm'), $array[0]);
                // set the defaults.\
                //echo "<PRE>";print_r($ret[$form]);
                if (isset($defaults[$form])) {
                    $ret[$form]->setDefaults((array) $defaults[$form]);
                } else if (isset($defaults[0])) {
                    $ret[$form]->setDefaults((array) $defaults[0]);
                }
                continue;
            }
            //echo "PAST FORM";
            //if (!strlen($ret[$form])) {
                // technically this is an error condition.
            //    continue;
            //}
            if (is_string($array[0][0]) && ($array[0][0] == 'addRule' || $array[0][0] == 'addFilter' )) {
                $method = array_shift($array[0]);
                //echo "<PRE>addrule";print_r(array(array($method), $array[0]));echo "</PRE>";
                $rr = call_user_func_array(array(&$ret[$form],$method), $array[0]);
                //echo "<PRE>addrule";print_r($rr);echo "</PRE>";
                continue;
            }
            //echo "<PRE>BUILD:";print_r($array);echo "</PRE>";
            $buildId = false;
            if (is_int($array[0][0])) {
                $buildId = array_shift($array[0]);
            }
            
            $e = call_user_func_array(array(&$ret[$form],'createElement'),$array[0]);
            
            //array_pop($ret->_elements);
            if (isset($array[2])) { // options..
                foreach ($array[2] as $v) {
                    $e->addOption($v[0],$v[1]);
                }
            }
            if (!isset($array[1])) {
                $ret[$form]->addElement($e);
            
                if ($buildId !== false) {
                    //echo "setting buildIndex $buildId";
                    $ret[$form]->_buildIndex[$buildId] = count($ret[$form]->_elements)-1;
                    //echo "<PRE>PARSE:";print_r($ret[$form]);echo "</PRE>";
                }
                
                continue;
            }
            foreach ($array[1] as $k=>$v) {
                //echo "<PRE>USR FUNC:";print_r(array( array($e,$k),$v));echo "</PRE>";
                call_user_func(array(&$e,$k),$v);
            }
            $ret[$form]->addElement($e);
            
            
            if ($buildId !== false) {
               // echo "setting buildIndex $buildId";
                $ret[$form]->_buildIndex[$buildId] = count($ret[$form]->_elements)-1;
                //echo "<PRE>PARSE:";print_r($ret[$form]);echo "</PRE>";
            }
            
        }
        //echo "<PRE>PARSE:";print_r($ret);echo "</PRE>";
        return $ret;
        
         
    }

}


?>
