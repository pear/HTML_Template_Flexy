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

 
/*
* Global variable - used to store active options when compiling a template.
*/
$GLOBALS['_HTML_TEMPLATE_FLEXY'] = array(); 

/**
* A Flexible Template engine - based on simpletemplate  
*
* @abstract Long Description
*  This is  a rip of of Wolfram's Simple Template class - heavily simplified,
*  with a modified filter loading mechanism.
*  notebly 
*       - no xml config stuff
*       - simplified filters - so you just tell it which filter (classes) to use, 
*            not individual methods
*       - a smarty like tag library, 
*       - heavily focused on displaying objects as pages.
* (so you can document your tags.)
*
* @version    $Id$
*/
class HTML_Template_Flexy  
{

    /*
    *   @var    array   $options    the options for initializing the template class
    */
    var $options = array(   'compileDir'    =>  '',      // by default its always the same one as where the template lies in, this might not be desired
                            'templateDir'   =>  '',
                            'forceCompile'  =>  false,  // only suggested for debugging
                            'filters'       => array(),
                            'debug'         => false,
                            'locale'        => 'en',
                            'useLegacy'     => false,  // use old preg_replace code
                            'nonHTML'       => false,  // dont parse HTML tags (eg. email templates)
                            'allowPHP'      => false   // allow PHP in template
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
    * The getTextStrings Filename
    *
    * @var string
    * @access public
    */
    var $gettextStringsFilename;
    /**
    * The quickform wrapper object - only used when forms are available..
    * Not quite sure how to deal with multiple forms in one template - probably better to 
    * just use muliple templates for multiple forms.
    *
    * @var object HTML_Template_Flexy_QuickForm
    * @access public
    */
    var $quickform;
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
            foreach( $baseoptions as  $key=>$aOption )  {
                $this->options[$key] = $aOption;
            }
        }
        
        foreach( $options as $key=>$aOption )  {
           $this->options[$key] = $aOption;
        }
        
        $filters = $this->options['filters'];
        if (is_string($filters)) {
            $this->options['filters']= explode(',',$filters);
        }
        
        if(!@is_dir($this->options['compileDir']) )
            return new PEAR_Error('The compile-directory doesnt exist yet!');
    }

    /**
    *   Outputs an object as $t 
    *
    *   for example the using simpletags the object's variable $t->test
    *   would map to {t.test}
    *
    *   maps ** these are all depreciated and should not be used!
    *   the area only available if useLegacy is set.
    *   $t->o_*           maps to {o.*}  // used for output
    *   $t->input         maps to {i.*} // used for input
    *   $t->modules[xxxx] maps to {m.xxxx} // used for modules
    *   $t->config        maps to {c.*}  // used for config
    *   PEAR::getStaticProperty('Auth','singleton') maps to  {a.*}
    *   {email_boundary} // for email boundaries  
    *   {email_date}    // for email dates
    *
    *
    *   @version    01/12/14
    *   @access     public
    *   @author     Alan Knowles
    *   @param    object object to output as $t
    *   @return     none
    */
    
    
    function outputObject(&$t) 
    {
        $options = PEAR::getStaticProperty('HTML_Template_Flexy','options');
        if (@$options['debug']) {
            echo "output $this->compiledTemplate<BR>";
            
        }
        
        $this->emailBoundary = md5("FlexyMail".microtime());
        $this->emailDate = date("D j M Y G:i:s O");
        
        if ($this->options['useLegacy']) {
            
            $m = new StdClass;
            // compile modules
             
            /* these are to be depreciated !!!*/
            
            
            $c = & $t->config;
            if (@$t->input) {
                $i = & $t->input;
            }
            $a = &PEAR::getStaticProperty('Auth','singleton');
            
            /* expose o_ as $o */
            $o = new StdClass;
            foreach (get_object_vars($t) as $k=>$v) {
                if ($k{0} != "o") {
                    continue;
                }
                if ($k{1} != "_") {
                    continue;
                }
                $kk = substr($k,2);
                $o->$kk =& $t->$k;
            }
            
            
            /* usefull stuff for doing emails in Template Flexy */
            $email_boundary = $this->emailBoundary;
            $email_date = $this->emailDate;
        }
        // we use PHP's error handler to hide errors in the template.
        // this may be removed later, or replace with
        // options['strict'] - so you have to declare
        // all variables
        
        $_error_reporting = false;
        if (!$options['debug']) {
            $_error_reporting = error_reporting(E_ALL ^ E_NOTICE);
        }
        include($this->compiledTemplate);
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
    function &bufferedOutputObject(&$t) 
    {
        ob_start();
        $this->outputObject($t);
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
    *   classicParse - the older regex based code generator.
    *   here all the replacing, filtering and writing of the compiled file is done
    *   well this is not much work, but still its in here :-)
    *
    *   @access     private
    *   @version    01/12/03
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @author     Alan Knowles <alan@akbkhome.com>
    *   @return
    */
    function _classicParse()
    {
        // read the entire file into one variable
        if ( $input = @file($this->currentTemplate) ) {
            $fileContent = implode( '' , $input );
        } else {
            $fileContent = '';                      // if the file doesnt exist, write a template anyway, an empty one but write one
        }
        
         
        //  apply pre filter
        $fileContent = $this->applyFilters( $fileContent , "/^pre_/i" );
        $fileContent = $this->applyFilters( $fileContent , "/^(pre_|post_)/i",TRUE);
        $fileContent = $this->applyFilters( $fileContent , "/^post_/i" );
        // write the compiled template into the compiledTemplate-File
        if( ($cfp = fopen( $this->compiledTemplate , 'w' )) ) {
            fwrite($cfp,$fileContent);
            fclose($cfp);
            @chmod($this->compiledTemplate,0775);
        }

        return true;
    }

    /**
    *   parse - the new tokenizer based generator
    *
    *   @access     private
    *   @version    01/12/03
    *   @author     Alan Knowles <alan@akbkhome.com>
    *   @param      fixForEmail - post processes and replaces ?>\n with ?>\n\n
    *   @return
    */




    function _parse() 
    { 
        // read the entire file into one variable
        
        // note this should be moved to new HTML_Template_Flexy_Token
        // and that can then manage all the tokens in one place..
        
        require_once 'HTML/Template/Flexy/Tokenizer.php';
        
        $GLOBALS['_HTML_TEMPLATE_FLEXY']['currentOptions'] = $this->options;
        $GLOBALS['_HTML_TEMPLATE_FLEXY']['quickform'] = &$this->quickform;
        
        
        setlocale(LC_ALL, $this->options['locale']);
        
        $data = file_get_contents($this->currentTemplate);
            //echo strlen($data);
        $tokenizer = new HTML_Template_Flexy_Tokenizer($data);
        $tokenizer->fileName = $this->currentTemplate;
        //$tokenizer->debug=1;
        if ($this->options['nonHTML']) {
            $tokenizer->ignoreHTML = true;
        }
        if ($this->options['allowPHP']) {
            $tokenizer->ignorePHP = false;
        }
        
        $res = HTML_Template_Flexy_Token::buildTokens($tokenizer);
            
           
        $data = $res->toString();
        
        if ($this->options['nonHTML']) {
           $data =  str_replace("?>\n","?>\n\n",$data);
        }
        
        
        // error checking?
        if( ($cfp = fopen( $this->compiledTemplate , 'w' )) ) {
            fwrite($cfp,$data);
            fclose($cfp);
            @chmod($this->compiledTemplate,0775);
        }
        if( ($cfp = fopen( $this->gettextStringsFile, 'w' )) ) {
            fwrite($cfp,serialize(array_unique($GLOBALS['_HTML_TEMPLATE_FLEXY_TOKEN']['gettextStrings'])));
            fclose($cfp);
        }
        
        
        
        return true;
    }

    /**
    *   compile the template
    *
    *   @access     public
    *   @version    01/12/03
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      string  $file   relative to the 'templateDir' which you set when calling the constructor
    *   @param      boolean $fixForMail - replace ?>\n with ?>\n\n
    *   @return
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
                                "1. please create the 'compileDir' which is: <b>'$compileDest'</b><br>2. give write-rights to it",
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
                    umask(0000);                        // make that the users of this group (mostly 'nogroup') can erase the compiled templates too
                    if( !@mkdir($compileDest,0770) ) {
                        PEAR::raiseError(   "couldn't make directory: <b>'$aDir'</b> under <b>'".$this->options['compileDir']."'</b><br>".
                                            "1. please give write permission to the 'compileDir', so SimpleTemplate can create directories inside",
                                             null, PEAR_ERROR_DIE);
                    }
                }
            }
        }
        
        /* 
        
            incomming file looks like xxxxxxxx.yyyy
            if xxxxxxxx.{locale}.yyy exists - use that...
        */
        $parts = array();
        if (preg_match('/(.*)(\.[a-z]+)$/i',$file,$parts)) {
            $newfile = $parts[1].'.'.$this->options['locale'] .$parts[2];
            if (@file_exists($this->options['templateDir']. DIRECTORY_SEPARATOR .$newfile)) {
                $file = $newfile;
            }
        }
        
        
        $this->currentTemplate = $this->options['templateDir'].DIRECTORY_SEPARATOR .$file;
        
        if( !@file_exists($this->currentTemplate ))  {
            // check if the compile dir has been created
            PEAR::raiseError("Template {$this->currentTemplate} does not exist<br>",  null, PEAR_ERROR_DIE);
        }
         
 
        
        
        $this->compiledTemplate    = $compileDest.DIRECTORY_SEPARATOR .$filename.'.'.$this->options['locale'].'.php';
        $this->getTextStringsFile  = $compileDest.DIRECTORY_SEPARATOR .$filename.'.gettext.serial';
  
        $recompile = false;
        if( @$this->options['forceCompile'] ) {
            $recompile = true;
        }

        if( $recompile==false )  {                    // if recompile is true dont bother to check if template has changed
            if( !$this->isUpToDate() ) {                 // check if the template has changed
                $recompile = true;
            }
        }
        $method = '_parse';
        if (@$this->options['useLegacy']) {
            $method = '_classicParse';
        }
        if( $recompile )  {             // or any of the config files
        
            if( !is_writeable($compileDest)) {
                PEAR::raiseError(   "can not write to 'compileDir', which is <b>'$compileDest'</b><br>".
                                "1. please give write and enter-rights to it",
                                null, PEAR_ERROR_DIE);
            }
        
            if( !$this->$method() ) {
                return false;
            }
        }
        
        return true;
    }

     /**
    *  compiles all templates
    *  used for offline batch compilation (eg. if your server doesnt have write access to the filesystem)
    *
    *   @access     public
    *   @author     Alan Knowles <alan@akbkhome.com>
    *
    */
    function compileAll($dir = '')
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
                $this->compileAll($dir . DIRECTORY_SEPARATOR  . $name);
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
            filemtime( $checkFile ) > filemtime( $this->compiledTemplate )
          ) {
            return false;
        }

        return true;
    }

      
    /**
    *   actually it will only be used to apply the pre and post filters
    *
    *   @access     public
    *   @version    01/12/10
    *   @author     Alan Knowles <alan@akbkhome.com>
    *   @param      string  $input      the string to filter
    *   @param      array   $prefix     the subset of methods to use.
    *   @return     string  the filtered string
    */
    function applyFilters( $input , $prefix = "",$negate=FALSE)
    {
        $this->debug("APPLY FILTER $prefix<BR>");
        $filters = $this->options['filters'];
        $this->debug(serialize($filters)."<BR>");
        foreach($filters as $filtername) {
            $class = "HTML_Template_Flexy_Filter_{$filtername}";
            require_once("HTML/Template/Flexy/Filter/{$filtername}.php");
            
            if (!class_exists($class)) {
                return $this->raiseError("Failed to load filter $filter",null,PEAR_ERROR_DIE);
            }
            
            if (!@$this->filter_objects[$class])  {
                $this->filter_objects[$class] = new $class;
                $this->filter_objects[$class]->_set_engine($this);
            }
            $filter = &$this->filter_objects[$class];
            $methods = get_class_methods($class);
            $this->debug("METHODS:");
            $this->debug(serialize($methods)."<BR>");
            foreach($methods as $method) {
                if ($method{0} == "_") {
                    continue; // private
                }
                if ($method  == $class) {
                    continue; // constructor
                }
                $this->debug("TEST: $negate $prefix : $method");
                if ($negate &&  preg_match($prefix,$method)) {
                    continue;
                }
                if (!$negate && !preg_match($prefix,$method)) {
                    continue;
                }
                
                $this->debug("APPLYING $filtername $method<BR>");
                $input = $filter->$method($input);
            }
        }

        return $input;
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
     

     
    
    
}
?>