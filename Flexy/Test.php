#!/usr/bin/php
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
//  This is a temporary file - it includes some of the 
// Code that will have to go in the Engine eventually..
// Used to test parsing and generation.
//

ini_set('include_path', realpath(dirname(__FILE__) . '/../../..'));
require_once 'Gtk/VarDump.php';
require_once 'Console/Getopt.php';
require_once 'HTML/Template/Flexy/Tokenizer.php';
require_once 'HTML/Template/Flexy/Token.php';
// this is the main runable...
 
class HTML_Template_Flexy_Test {


    function HTML_Template_Flexy_Test () {
        // for testing!
        $GLOBALS['_HTML_TEMPLATE_FLEXY']['currentOptions'] = array(
            'compileDir' => dirname(__FILE__),
            'locale' => 'en');
            
        $this->parseArgs();
        $this->parse();
        
    }
    
    
    
    function parseArgs() {
        // mapp of keys to values..
      
        
        $args = Console_Getopt::ReadPHPArgV();
        $vals = Console_Getopt::getopt($args,'');
        //print_r($vals);
        $files = $vals[1];
        
        if (!$files) {
            $this->error(0,"No Files supplied");
        }
        
        foreach($files as $file) {
            $realpath = realpath($file);
            if (!$realpath) {
                 $this->error(0,"File $path Does not exist");
            }
            $this->files[] = $realpath;
        }
        
        
    }
    
    var $files; // array of files to compile
    
    function parse() {
        foreach($this->files as $file) {
        
            
            //$this->debug(1, "Tokenizing ". $file);
            $data = file_get_contents($file);
            //echo strlen($data);
            $tokenizer = new HTML_Template_Flexy_Tokenizer($data);
            $tokenizer->debug=1;
            $i=0;
            $l = -1;
            $tokenizer->value = '';
            $res = array();
            while ($t = $tokenizer->yylex()) {  
                //if ($tokenizer->value === '') {
                //    continue;
               // }
                
                if ($t == HTML_TEMPLATE_FLEXY_TOKEN_ERROR) {
                    $this->error(0,'GOT ERROR');
                }
                if ($t == HTML_TEMPLATE_FLEXY_TOKEN_NONE) {
                    continue;
                }
               
                $i++;
                $res[$i] = $tokenizer->value;
                $res[$i]->id = $i;
                //print_r($res[$i]);
                
                $tokenizer->value = '';
                
            }
            $stack = array();
            for($i=0;$i<count($res);$i++) {
                if (!@$res[$i]->tag) {
                    continue;
                }
                if ($res[$i]->tag{0} == '/') { // it's a close tag..
                    //echo "GOT END TAG: {$res[$i]->tag}\n";
                    $tag = strtoupper(substr($res[$i]->tag,1));
                    if (!isset($stack[$tag]['pos'])) {
                        continue; // unmatched
                    }
                    $npos = $stack[$tag]['pos'];
                    //echo "matching it to {$stack[$tag][$npos]}\n";
                    $res[$stack[$tag][$npos]]->close = &$res[$i];
                    $stack[$tag]['pos']--;
                    if ($stack[$tag]['pos'] < 0) {
                        // too many closes - just ignore it..
                        $stack[$tag]['pos'] = 0;
                    }
                    continue;
                }
                // new entry on stack..
                $tag = strtoupper($res[$i]->tag);
                
                if (!isset($stack[$tag])) {
                    $npos = $stack[$tag]['pos'] = 0;
                } else {
                    $npos = ++$stack[$tag]['pos'];
                }
                $stack[$tag][$npos] = $i;
            }
                
            
            new Gtk_VarDump($res);
            foreach($res as $v) {
                echo $v->toHTML();
            }
        }
        
        
        
        
        
    }
       
    function error($id,$msg) {
        echo "ERROR $id : $msg\n";
        exit(255);
    }
      
    function debug($id,$msg) {
        echo "Debug Message ($id) : $msg\n";
    }
    
    
}



new HTML_Template_Flexy_Test;
?>