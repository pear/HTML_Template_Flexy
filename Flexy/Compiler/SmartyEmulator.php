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
//  Smarty Compilation wrapper
//  Calls the relivent smarty compilers, then calls flexy to do the rest..



class HTML_Template_Flexy_Compiler_SmartyEmulator extends HTML_Template_Flexy_Compiler {
    
    function compile(&$flexy,$string=false) 
    {
        $data = $string;
        if ($string === false) {
            $data = file_get_contents($flexy->currentTemplate);
        }
        
        
        // do the smarty stuff...
        
        $leftq = preg_quote('{', '!');
        $rightq = preg_quote('}', '!');
         
        preg_match_all("!" . $leftq . "\s*(.*?)\s*" . $rightq . "!s", $data, $matches);
        $tags = $matches[1];
        // find all the tags/text...
        $text = preg_split("!" . $leftq . ".*?" . $rightq . "!s", $data);
        
        $max_text = count($text);
        $max_tags = count($tags);
        
        for ($i = 0 ; $i < $max_tags ; $i++) {
            $compiled_tags[] = $this->_compileTag($tags[$i]);
        }
        // error handling for closing tags.
        
         
        $data = '';
        for ($i = 0; $i < $max_tags; $i++) {
            $data .= $text[$i].$compiled_tags[$i];
        }
        $data .= $text[$i];
        
        echo "DATA: $data";
        return;
        require_once 'HTML/Template/Flexy/Compiler/Standard.php';
        
        $flexyCompiler = HTML_Template_Flexy_Compiler_Standard;
        $flexyCompiler->compile($flexy,$data);
        return true;
    }
        
    /**
    * stack for conditional and closers.
    *
    * @var array
    * @access public
    */
    var $stack = array(
            'if' => 0,
        );
    
    function _compileTag($str) 
    {
        // skip comments
        if (($str{0} == '*') && (substr($str,-1,1) == '*')) {
            return '';
        }
        
        
        switch($str{0}) {
            case '$':
                // its a var
                return $this->_convertVar($str);
            case '#':
                // its a config var
                return $this->_convertConfigVar($str);
            case '%':
                // wtf does this do
                return "<!-- what is this? $str -->";
        }
                
            
        
        
        
        
        // this is where it gets messy
        // this is very slow - but what the hell 
        //   - its only done once
        //   - its alot more readable than a long regext.
        //   - it doesnt infringe on copyright...
        switch(true) {
            case (preg_match('/^config_load\s/', $str)):
                // convert to $t->TemplateConfigLoad()
                $args = $this->convertAttributesToKeyVal(substr($str,strpos( $str,' ')));
                return '{templateConfigLoad(#'.$args['file'].'#,#'.$args['section'].')}';
           
            case ($str == 'ldelim'):
                return '{';
            case ($str == 'rdelim'):
                return '}';
                
                
            case (preg_match('/^if \$(\S+)$/', $str,$matches)):
            case (preg_match('/^if \$(\S+)\seq\s""$/', $str,$matches)):
                // simple if variable..
                // convert to : {if:sssssss}
                $this->stack['if']++;
                $var =  $this->_convertVar('$'.$matches[1]);
                return '{if:'.substr($var,1);
                
            case (preg_match('/^if #(\S+)#$/', $str,$matches)):
            case (preg_match('/^if #(\S+)#\sne\s""$/', $str,$matches)):
                // simple if variable..
                // convert to : {if:sssssss}
                $this->stack['if']++;
                $var =  $this->_convertConfigVar('#'.$matches[1].'#');
                return '{if:'.substr($var,1);
            
            // negative matches
            case (preg_match('/^if\s!\s\$(\S+)$/', $str,$matches)):
            case (preg_match('/^if \$(\S+)\seq\s""$/', $str,$matches)):
                // simple if variable..
                // convert to : {if:sssssss}
                $this->stack['if']++;
                $var =  $this->_convertVar('$'.$matches[1]);
                return '{if:!'.substr($var,1);
                
             case ($str == 'else'):
                if (!$this->stack['if']) {
                    break;
                }
                return '{else:}';
                
                
            case ($str == '/if'):
                if (!$this->stack['if']) {
                    break;
                }
                $this->stack['if']--;
                return '{end:}';
            
            
        }
        
        return "<!--   UNSUPPORTED TAG: $str FOUND -->";
                
        
    
    
    }
    
    /**
    * convert a smarty var into a flexy one.
    *
    * @param   string       the inside of the smart tag
    *
    * @return   string      a flexy version of it.
    * @access   private
    */
  
    function _convertVar($str) 
    {
        // look for modfiers first.
        $mods = explode('|', $str);
        $var = array_shift($mods);
        $var = substr($var,1); // strip $
        
        // various formats :
        // aaaa.bbbb.cccc => aaaa[bbbb][cccc]
        // aaaa[bbbb] => aaa[bbbb]
        // aaaa->bbbb => aaaa.bbbb
        
        $bits = explode('.',$var);
        $var = array_shift($bits);
        foreach($bits as $k) {
            $var.= '['.$k .']';
        }
        $bits = explode('->',$var);
        $var = implode('.',$bits);
        $mods = implode('|',$mods);
        if (strlen($mods)) {
            $mods = "<!--  UNSUPPORTED MODIFIERS: $mods -->";
        }
        return '{'.$var .'}' . $mods;
    }
    /**
    * convert a smarty key="value" string into a key value array
    * cheap and cheerfull - doesnt handle spaces inside the strings...
    *
    * @param   string       the key value part of the tag..
    *
    * @return   array      key value array
    * @access   private
    */
    function convertAttributesToKeyVal($str) 
    {
        $atts = explode(' ', $str);
        $ret = array();
        foreach($atts as $bit) {
            $bits = explode('=',$bit);
            // loose stuff!!!
            if (count($bits) != 2) {
                continue;
            }
            $ret[$bits[0]] = ($bits[1]{0} == '"') ? substr($bits[1],1,-1) : $bits[1]; 
        }
        return $ret;
    }
     /**
    * convert a smarty config var into a flexy one.
    *
    * @param   string       the inside of the smart tag
    *
    * @return   string      a flexy version of it.
    * @access   private
    */
  
    function _convertConfigVar($str) 
    {
        $mods = explode('|', $str);
        $var = array_shift($mods);
        $var = substr($var,1,-1); // strip #'s
        $mods = implode('|',$mods);
        if (strlen($mods)) {
            $mods = "<!-- UNSUPPORTED MODIFIERS: $mods -->";
        }
        return '{configVars.'.$var .'}' . $mods;
    }
}
