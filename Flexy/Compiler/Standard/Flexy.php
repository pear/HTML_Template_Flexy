<?php


/**
* the <flexy:XXXX namespace
* 
* 
* at present it handles
*       <flexy:toJavascript flexy:prefix="Javascript_prefix"  javscriptName="PHPvar" .....>
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
    * Note first thing it does is call any parseTag Method that exists..
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
        if (!method_exists($this,$method.'toString')) {
            return '';
        }
        return $this->{$method.'toString'}($element);
        
    }
    
    
    function toJavascripttoString($element) {
        $ret = "<?php require_once 'HTML/Javascript/Convert.php'; ?>\n";
        $ret .= "<script language='javascript'>\n";
        $prefix = ''. $element->getAttribute('FLEXY:PREFIX');
        
        
        foreach ($element->attributes as $k=>$v) {
            // skip directives..
            if (strpos($k,':')) {
                continue;
            }
            $v = substr($v,1,-1);
            $ret .= '<?php echo HTML_Javascript_Convert::convertVar(\''.$prefix . $k.'\','.$element->toVar($v) .',true);?>'. "\n";
        }
        $ret .= "</script>";
        return $ret;
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

?>