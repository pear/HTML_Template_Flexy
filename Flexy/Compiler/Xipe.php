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
// | Authors:  nobody <nobody@localhost>                                  |
// +----------------------------------------------------------------------+
//
// $Id$
//
//  Description
//
 
// this is an attempt to provide Support for Xipe templates, 
// 


class HTML_Template_Flexy_Compiler_Xipe {
        
    var $options = array(
                            'preFilters' => array(
                                'TagLib::allPreFilters' 
                                'Basic::allPreFilters' 
                            ),
                            
                            'filters' => array(
                                'Internal::autoBraces',
                                'Internal::makePhpTags',
                                'Internal::removeXmlConfigString',
                                )
                            
                            'postFilters' => array(
                                'Basic::allPostFilters' 
                            ),
                            
                            'delimiter'     =>  array('{','}'),
                             
                            
                            'xmlConfigFile' =>  'config.xml',
                            'locale'        =>  'en',
                             
                            'filterLevel'   =>  10,
                              
                            'verbose'       =>  true,
                           
                       
                        );
    /**
    *   saves the preFilters which will be applied before compilation
    *
    *   @access private
    *   @var    array   methods/functions that will be called as prefilter
    */
    var $_preFilters = array();

    /**
    *   saves the postFilters which will be applied after compilation
    *
    *   @access private
    *   @var    array   methods/functions that will be called as postfilter
    */
    var $_postFilters = array();
    /**
    * The main flexy engine
    *
    * @var object HTML_Template_Flexy
    * @access public
    */
    
    var $flexy;
    /**
    *   compile the template
    *
    *   @access     public
    *   @version    01/12/03
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      string  $file   relative to the 'templateDir' which you set when calling the constructor
    *   @return
    */
    function compile(&$flexy)
    {
        $this->flexy = &$flexy;
        
        // do something about the options!!!
        
        // read the entire file into one variable
        $fileContent = file_get_contents($flexy->currentTemplate);
         
         
        
        /* --------- Xipe code --- */
        
        
        // pass option to know the delimiter in the filter, but parse the xml-config before!!!, see line above
        
        //  apply pre filter
        $fileContent = $this->applyFilters( $fileContent , $this->options['preFilters'] );

        // core filters..
        $fileContent = $this->applyFilters( $fileContent ,  $this->options['filters'] );

        //  apply post filter
        $fileContent = $this->applyFilters( $fileContent ,$this->options['postFilters'] );


        // write the compiled template into the compiledTemplate-File
        if( ($cfp = fopen( $flexy->compiledTemplate , 'w' )) )
        {
            fwrite($cfp,$fileContent);
            fclose($cfp);
            @chmod($flexy->compiledTemplate,0775);
        }

        return true;
        
        
    }
    
    
      /**
    *   actually it will only be used to apply the pre and post filters
    *
    *   @access     public
    *   @version    01/12/10
    *   @author     Wolfram Kriesing <wolfram@kriesing.de>
    *   @param      string  $input      the string to filter
    *   @param      array   $filters    an array of filters to apply
    *   @return     string  the filtered string
    */
    function applyFilters( $input , $filters )
    {
        if (!$filters) {
            return $input;
        }
        
        foreach( $filters as $i=>$aFilter ){
            // FIXXME use log class
            $startTime = split(" ",microtime());
            $startTime = $startTime[1]+$startTime[0];
            $sizeBefore = strlen($input);
            
            
            $call = $this->buildCall($aFilter);
            $call[1][] = $input;
            $input = call_user_func_array( $call[0] , $call[1]);
 

            $sizeAfter = strlen($input);
            // FIXXME use log class
            $endTime = split(" ",microtime());
            $endTime = $endTime[1]+$endTime[0];
            $itTook = ($endTime - $startTime)*100;

            HTML_Template_Flexy::debug("applying filter: '$i' \ttook=$itTook ms, \tsize before: $sizeBefore Byte, \tafter: $sizeAfter Byte");
        }


        return $input;
    }


    
    function getOptions($k,$kk=false) 
    {
        if ($kk === false) {
            return $this->options[$k];
        }
        return $this->options[$k][$kk];
    }
     
    
    function buildCall($filter)
    {
        // formats accepted for loading filters:
        // array('someclass','somemethod', ......)
        // 'somefunction'    = a function
        // 'Internal::yyyyy' = built in, autoloaded filter..
        // array(            = user defined, lazy loading.
        //       'class' => 'xxxxxx'
        //      'file' => 'xxxx.php'
        //      'function' => 'xxxx' <- only manditory..
        //      'args'     => array(),
        // )
        $return = array('printf','filter not found');
        // a check for user defined lazy load..
        
        if (is_array($filter) && isset($filter['function'])) {
            
            if (isset($filter['file'])) {
                require_once $filter['file'];
            }
            if (isset($filter['class']) && !isset($this->filters[$filter['class']])) {
                $c = $filter['class'];
                $this->filters[$filter['class']] = new $c;
                $this->filters[$filter['class']] ->options = 
                    $this->flexy->options + $this->filters[$filter['class']] ->options;
                
            }
            if (isset($filter['class'])) {
                $return[0] = array($c,  $filter['function']);
            } else {
                $return[0] = $filter['function'];
            }
            if (isset($filter['args'])) {
                // this has to be an array
                $return[1] = $filter['args'];
            } else {
                $return[1] = array();
            }
            return $return;
        }
        
        // internal method.. ::
        if (is_string($filter) && strpos($filter,':')) {
            list($subclass,$method) = explode('::',$filter);\
            $class = "HTML_Template_Flexy_Compile_Xipe_{$subclass}";
            
            if (!isset($this->filters[$class])) {
                require_once "HTML/Template/Flexy/Compile/Xipe/{$subclass}.php";
             
                $this->filters[$class] = new $class;
                $this->filters[$class] ->options = 
                    $this->flexy->options + $this->filters[$class]->options;
            }
             
            return array(array($class,$method),array());
        }
        // a simple function.. 
        if (is_string($filter)) {
            return array($filter,array());
        }
        
        
    
    
   
}


