<?php
//
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
// | Author:  Alan Knowles <alan@akbkhome.com>
// | Original Author: Wolfram Kriesing <wolfram@kriesing.de>             |
// +----------------------------------------------------------------------+
//
 
/**
*   @package    HTML_Template_Flexy
*/
// prevent disaster when used with xdebug! 
@ini_set('xdebug.max_nesting_level', 1000);



require_once 'PEAR.php'; 
/*
* Global variable - used to store active options when compiling a template.
*/
$GLOBALS['_HTML_TEMPLATE_FLEXY'] = array(); 

/**
* A Flexible Template engine - based on simpletemplate  
*
* @abstract Long Description
*  Have a look at the package description for details.
*
* usage: 
*
* // $options can be blank if so, it is read from 
* // PEAR::getStaticProperty('HTML_Template_Flexy','options');
* $template = new HTML_Template_Flexy($options);
* 
* $template->compiler('/name/of/template.html');
*
* // $this - should be a class whose variable are used
* // in the template eg. $this->xxx maps to {xxx}
* // $elements is an array of HTML_Template_Flexy_Elements
* // eg. array('name'=> new HTML_Template_Flexy_Element('',array('value'=>'fred blogs'));
* 
* $template->outputObject($this,$elements)
*
*
*
* @version    $Id$
*/
class HTML_Template_Flexy  
{

    /*
    *   @var    array   $options    the options for initializing the template class
    */
    var $options = array(   'compileDir'    =>  '',      // where do you want to write to..
                            'templateDir'   =>  '',     // where are your templates
                            'locale'        => 'en',    // works with gettext
                            'forceCompile'  =>  false,  // only suggested for debugging

                            'debug'         => false,   // prints a few errors
                            
                            'nonHTML'       => false,  // dont parse HTML tags (eg. email templates)
                            'allowPHP'      => false,   // allow PHP in template
                            'compiler'      => 'Standard', // which compiler to use.
                            'compileToString' => false,    // should the compiler return a string 
                                                            // rather than writing to a file.
                            'filters'       => array(),    // used by regex compiler..
                            'numberFormat'  => ",2,'.',','",  // default number format  = eg. 1,200.00
                            
                        );

    
    
        
    /**
    * emailBoundary  - to use put {this.emailBoundary} in template
    *
    * @var string
    * @access public
    */
    var $emailBoundary;
    /**
    * emaildate - to use put {this.emaildate} in template
    *
    * @var string
    * @access public
    */
    var $emaildate;
    /**
    * The compiled template filename (Full path)
    *
    * @var string
    * @access public
    */
    var $compiledTemplate;
    /**
    * The source template filename (Full path)
    *
    * @var string
    * @access public
    */
    
    
    var $currentTemplate;
    
    /**
    * The getTextStrings Filename
    *
    * @var string
    * @access public
    */
    var $gettextStringsFile;
    /**
    * The serialized elements array file.
    *
    * @var string
    * @access public
    */
    var $elementsFile;
    
     
    /**
    * Array of HTML_elements to merge with form
    * 
    *
    * @var array of  HTML_Template_Flexy_Elements
    * @access public
    */
    var $elements = array();
    /**
    *   Constructor 
    *
    *   Initializes the Template engine, for each instance, accepts options or
    *   reads from PEAR::getStaticProperty('HTML_Template_Flexy','options');
    *
    *   @access public
    *   @param    array    $options (Optional)
    */
    

    
    function HTML_Template_Flexy( $options=array() )
    {
        $baseoptions = &PEAR::getStaticProperty('HTML_Template_Flexy','options');
       
        if ($baseoptions ) {
            foreach( $baseoptions as  $key=>$aOption)  {
                $this->options[$key] = $aOption;
            }
        }
        
        foreach( $options as $key=>$aOption)  {
           $this->options[$key] = $aOption;
        }
        
        $filters = $this->options['filters'];
        if (is_string($filters)) {
            $this->options['filters']= explode(',',$filters);
        }
        
        if (is_string($this->options['templateDir'])) {
            $this->options['templateDir'] = explode(';',$this->options['templateDir'] );
        }
        
        
        if(!@is_dir($this->options['compileDir']) ) {
            return new PEAR_Error('The compile-directory doesnt exist yet!');
        }
    }

    /**
    *   Outputs an object as $t 
    *
    *   for example the using simpletags the object's variable $t->test
    *   would map to {test}
    *
    *   @version    01/12/14
    *   @access     public
    *   @author     Alan Knowles
    *   @param    object   to output  
    *   @param    array  HTML_Template_Flexy_Elements (or any object that implements toHtml())
    *   @return     none
    */
    
    
    function outputObject(&$t,$elements=array()) 
    {
        if (!is_array($elements)) {
            return PEAR::raiseError(
                'second Argument to HTML_Template_Flexy::outputObject() was an '.gettype($elements) . ', not an array',
                null,PEAR_ERROR_DIE);
        }
        if (@$this->options['debug']) {
            echo "output $this->compiledTemplate<BR>";
        }
  
        // this may disappear later it's a BC fudge to try and deal with 
        // the old way of setting $this->elements to be merged.
        // the correct behavior is to use the extra field in outputObject.
       
        if (count($this->elements) && !count($elements)) {
            $elements = $this->elements;
        }
       
        $this->elements = $this->getElements();
        
        // overlay elements..
       
        foreach($elements as $k=>$v) {
            if (!$v) {
                unset($this->elements[$k]);
            }
            if (!isset($this->elements[$k])) {
                $this->elements[$k] = $v;
                continue;
            }
            $this->elements[$k] = $this->mergeElement($this->elements[$k] ,$v);
        }
        
      
        
        // we use PHP's error handler to hide errors in the template.
        // this may be removed later, or replace with
        // options['strict'] - so you have to declare
        // all variables
        
        
        $_error_reporting = false;
        if (!$this->options['debug']) {
            $_error_reporting = error_reporting(E_ALL ^ E_NOTICE);
        }
        if (!is_readable($this->compiledTemplate)) {
              PEAR::raiseError( "Could not open the template: <b>'{$this->compiledTemplate}'</b><BR>".
                            "Please check the file permisons on the directory and file ",
                            null, PEAR_ERROR_DIE);
        }
        
        include($this->compiledTemplate);
        
        // restore error handler.
        
        if ($_error_reporting !== false) {
            error_reporting($_error_reporting);
        }
    }
    /**
    *   Outputs an object as $t, buffers the result and returns it.
    *
    *   See outputObject($t) for more details.
    *
    *   @version    01/12/14
    *   @access     public
    *   @author     Alan Knowles
    *   @param      object object to output as $t
    *   @return     string - result
    */
    function bufferedOutputObject(&$t,$elements=array()) 
    {
        ob_start();
        $this->outputObject($t,$elements);
        $data = ob_get_contents();
        ob_end_clean();
        return $data;
    }
    /**
    * static version which does new, compile and output all in one go.
    *
    *   See outputObject($t) for more details.
    *
    *   @version    01/12/14
    *   @access     public
    *   @author     Alan Knowles
    *   @param      object object to output as $t
    *   @param      filename of template
    *   @return     string - result
    */
    function &staticQuickTemplate($file,&$t) 
    {
        $template = new HTML_Template_Flexy;
        $template->compile($file);
        $template->outputObject($t);
    }
    
      
 
 
    /**
    *   compile the template
    *
    *   @access     public
    *   @version    01/12/03
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      string  $file   relative to the 'templateDir' which you set when calling the constructor
    *   @param      boolean $fixForMail - replace ?>\n with ?>\n\n
    *   @return    boolean true on success. false on failure..
    */
    
    
    
    function compile( $file )
    {
        if (!$file) {
            PEAR::raiseError('HTML_Template_Flexy::compile no file selected',null,PEAR_ERROR_DIE);
        }
        if (!@$this->options['locale']) {
            $this->options['locale']='en';
        }
        // on windows the base directory will be C:!
        // so you have to hard code the path (no relatives on windows
        if (DIRECTORY_SEPARATOR == "/") {
            // if the compileDir doesnt start with a / then its under the template dir    
            if ( $this->options['compileDir']{0} !=  DIRECTORY_SEPARATOR ) {
                $this->options['compileDir'] =  $this->options['templateDir'].'/'.$this->options['compileDir'];
            }
        }

        // remove the slash if there is one in front, just to be clean
        if ( $file{0} == DIRECTORY_SEPARATOR  ) {
            $file = substr($file,1);
        }

        $compileDest = $this->options['compileDir'];
        if ( !@is_dir($compileDest) ) {               // check if the compile dir has been created
            PEAR::raiseError(   "'compileDir' could not be accessed<br>".
                                "Please create the 'compileDir' which is: <b>'$compileDest'</b><br>2. give write-rights to it",
                                null, PEAR_ERROR_DIE);
        }

    

        $directory = dirname( $file );
        $filename = basename($file);

        // extract dirname to create directori(es) in compileDir in case they dont exist yet
        // we just keep the directory structure as the application uses it, so we dont get into conflict with names
        // i dont see no reason for hashing the directories or the filenames
        if( $directory!='.' )  { // it is '.' also if no dir is given
            $path = explode(DIRECTORY_SEPARATOR ,$directory);
            foreach( $path as $aDir ) {
                $compileDest = $compileDest. DIRECTORY_SEPARATOR . $aDir;
                if( !@is_dir($compileDest) ) {
                    umask(0000);          // make that the users of this group (mostly 'nogroup') 
                                          // can erase the compiled templates too
                    if( !@mkdir($compileDest, 0770) ) {
                        PEAR::raiseError(   "couldn't make directory: <b>'$aDir'</b> under <b>'".$this->options['compileDir']."'</b><br>".
                                            "Please give write permission to the 'compileDir', so HTML_Template_Flexy can create directories inside",
                                             null, PEAR_ERROR_DIE);
                    }
                }
            }
        }
        
        /* 
        
            incoming file looks like xxxxxxxx.yyyy
            if xxxxxxxx.{locale}.yyy exists - use that...
        */
        $parts = array();
        if (preg_match('/(.*)(\.[a-z]+)$/i',$file,$parts)) {
            $newfile = $parts[1].'.'.$this->options['locale'] .$parts[2];
            foreach ($this->options['templateDir'] as $tmplDir) {
                if (@file_exists($tmplDir . DIRECTORY_SEPARATOR .$newfile)) {
                    $file = $newfile;
                }
            }
        }
        
        // look in all the posible locations for the template directory..
        $this->currentTemplate  = false;
        
        if (is_array($this->options['templateDir'])) {
            
            foreach ($this->options['templateDir'] as $tmplDir) {
                if (!@file_exists($tmplDir . DIRECTORY_SEPARATOR . $file))  {
                    continue;
                }
                if ($this->currentTemplate  !== false) {
                    return PEAR::raiseError("You have more than one template Named {$file} in your paths, found in both".
                        "<BR>{$this->currentTemplate }<BR>{$tmplDir}" . DIRECTORY_SEPARATOR . $file,  null, PEAR_ERROR_DIE);
                    
                }
                $this->currentTemplate = $tmplDir . DIRECTORY_SEPARATOR . $file;
            }
        }
        if ($this->currentTemplate === false)  {
            // check if the compile dir has been created
            return PEAR::raiseError("Could not find Template {$file} in any of the directories<br>" . 
                implode("<BR>",$this->options['templateDir']) ,  null, PEAR_ERROR_DIE);
        }
         
 
        
        
        $this->compiledTemplate    = $compileDest.DIRECTORY_SEPARATOR .$filename.'.'.$this->options['locale'].'.php';
        $this->getTextStringsFile  = $compileDest.DIRECTORY_SEPARATOR .$filename.'.gettext.serial';
        $this->elementsFile        = $compileDest.DIRECTORY_SEPARATOR .$filename.'.elements.serial';
        
        
        
        $recompile = false;
        if( @$this->options['forceCompile'] ) {
            $recompile = true;
        }

        if( $recompile==false )  {                    // if recompile is true dont bother to check if template has changed
            if( !$this->isUpToDate() ) {                 // check if the template has changed
                $recompile = true;
            }
        }
        
        
        
        if(! $recompile )  {             // or any of the config files
            return true;
        }
        
        if( !is_writeable($compileDest)) {
            PEAR::raiseError(   "can not write to 'compileDir', which is <b>'$compileDest'</b><br>".
                            "Please give write and enter-rights to it",
                            null, PEAR_ERROR_DIE);
        }
        
        // compile it..
        
        require_once 'HTML/Template/Flexy/Compiler.php';
        $compiler = HTML_Template_Flexy_Compiler::factory($this->options);
        return $compiler->compile($this);
        
        //return $this->$method();
        
    }

     /**
    *  compiles all templates
    *  used for offline batch compilation (eg. if your server doesnt have write access to the filesystem)
    *
    *   @access     public
    *   @author     Alan Knowles <alan@akbkhome.com>
    *
    */
    function compileAll($dir = '',$regex='/.html$/')
    {
        
        $base =  $this->options['templateDir'];
        $dh = opendir($base . DIRECTORY_SEPARATOR  . $dir);
        while (($name = readdir($dh)) !== false) {
            if (!$name) {  // empty!?
                continue;
            }
            if ($name{0} == '.') {
                continue;
            }
             
            if (is_dir($base . DIRECTORY_SEPARATOR  . $dir . DIRECTORY_SEPARATOR  . $name)) {
                $this->compileAll($dir . DIRECTORY_SEPARATOR  . $name,$regex);
                continue;
            }
            
            if (!preg_match($regex,$name)) {
                continue;
            }
            echo "Compiling $dir". DIRECTORY_SEPARATOR  . "$name \n";
            $this->compile($dir . DIRECTORY_SEPARATOR  . $name);
        }
        
    }
    /**
    *   checks if the compiled template is still up to date
    *
    *   @access     private
    *   @version    01/12/03
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      string      $fileToCheckAgainst if given this file is checked if it is newer than the compiled template
    *                                               this is useful if for example only an xml-config file has changed but not the
    *                                               template itself
    *   @return     boolean     true if it is still up to date
    */
    function isUpToDate( $fileToCheckAgainst='' )
    {
        if( $fileToCheckAgainst == '' ) {
            $checkFile = $this->currentTemplate;
        } else {
            $checkFile = $fileToCheckAgainst;
        }

        if( !file_exists( $this->compiledTemplate ) ||
            filemtime( $checkFile ) != filemtime( $this->compiledTemplate )
          ) {
            return false;
        }

        return true;
    }

      
    
     
    
    
    
     /**
    *   if debugging is on, print the debug info to the screen
    *
    *   @access     public
    *   @author     Alan Knowles <alan@akbkhome.com>
    *   @param      string  $string       output to display
    *   @return     none
    */
    function debug($string) 
    {  
        
        if (!$this->options['debug']) {
            return;
        }
        echo "<PRE><B>FLEXY DEBUG:</B> $string</PRE>";
        
    }
     
    /**
     * A general Utility method that merges HTML_Template_Flexy_Elements
     
     *
     * @param    HTML_Template_Flexy_Element   $original  (eg. from getElements())
     * @param    HTML_Template_Flexy_Element   $new (with data to replace/merge)
     * @return   HTML_Template_Flexy_Element   the combined/merged data.
     * @access   public
     */
     
    function mergeElement($original,$new)
    {
     
        
        // changing tags.. - should this be valid?
        // hidden is one use of this....
        if (!$original) {
            return $new;
        }
        
        if (!$new) {
            return $original;
        }
        
        if ($new->tag && ($new->tag != $original->tag)) {
            $original->tag = $new->tag;
        }
        
        if ($new->override !== false) {
            $original->override = $new->override;
        }
        //if $from is not an object:
        // then it's a value set....
        
        if (count($new->children)) {
            //echo "<PRE> COPY CHILDREN"; print_r($from->children);
            $original->children = $new->children;
        }
        
        if (is_array($new->attributes)) {
        
            foreach ($new->attributes as $key => $value) {
                $original->attributes[$key] = $value;
            }
        }
        $original->prefix = $new->prefix;
        $original->suffix = $new->suffix;  
        if ($new->value !== null) {
            //echo "<PRE>";print_r($original);
            $original->setValue($new->value);
        } 
       
        return $original;
        
    }  
     
     
    /**
    * Get an array of elements from the template
    *
    * All form elements and anything marked as dynamic are converted in to elements
    * (simliar to XML_Tree_Node) - you can then modify or merge them at the output stage
 
    *
    * @return   array   of HTML_Template_Flexy_Element sDescription
    * @access   public
    */
    
    function getElements() {
    
        if ($this->elementsFile && file_exists($this->elementsFile)) {
            require_once 'HTML/Template/Flexy/Element.php';
            return unserialize(file_get_contents($this->elementsFile));
        }
        return array();
    }
    
    
}
?>
