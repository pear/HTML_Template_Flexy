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



class HTML_Template_Flexy_Compiler_Standard extends HTML_Template_Flexy_Compiler {
    
    function compile(&$flexy,$string=false) 
    {
        $data = $string;
        if ($string === false) {
            $data = file_get_contents($flexy->currentTemplate);
        }
        
        
        // do the smarty stuff...
        require_once 'Smarty_Compiler.class.php';
        $s = new Smarty_Compiler();
        
        $leftq = preg_quote('{', '!');
        $rightq = preg_quote('}', '!');
         
        preg_match_all("!" . $leftq . "\s*(.*?)\s*" . $rightq . "!s", $data, $matches);
        $tags = $matches[1];
        // find all the tags/text...
        $text = preg_split("!" . $leftq . ".*?" . $rightq . "!s", $data);
        
        $max_text = count($text);
        $max_tags = count($tags);
        
        for ($i = 0 ; $i < $max ; $i++) {
            $compiled_tags[] = $s->_compile_tag($tags[$i]);
        }
        // error handling for closing tags.
        
        if (count($s->_tag_stack) > 0) {
            $last = end($this->_tag_stack);
            return PEAR::raiseError("Smarty Emulator: Error Unclosed Tag: {$last[0]} in {$flexy->currentTemplate}",PEAR_ERROR_DIE);
        }
        $data = '';
        for ($i = 0; $i < $max_tags; $i++) {
            $data .= $text[$i].$tags[$i];
        }
        $data .= $text[$i];
        
       
        
        require_once 'HTML/Template/Flexy/Compiler/Standard.php';
        
        $flexyCompiler = HTML_Template_Flexy_Compiler_Standard;
        $flexyCompiler->compile($flexy,$data);
        return true;
    }
}
