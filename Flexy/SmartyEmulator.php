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
//  Description this class emulates the Smarty API to attempt to enable 
//  upgrading to flexy.
//  
//  I've no idea how complete this will end up being..
//
//  Technically Smarty is LGPL - so theortically no code in here should
//  use the copy & paste the original smarty code....
//

//  BIG NOTE: phpdoc comments to be added prior to inclusion in package!!!
//


// to use as a full emulator : 
// try 
// class Smarty extends HTML_Template_Flexy_SmartyEmulator {
//   function Smarty() { parent::construct(); } 
// }


// not implemented: 
/*
append_by_ref
append
register_function / unregister_function
register_object / register_object
register_block / unregister_block
register_compiler_function / unregister_compiler_function
register_modifier / unregister_modifier
register_resource / unregister_resource
register_prefilter / unregister_prefilter
register_postfilter / unregister_postfilter
register_outputfilter / unregister_outputfilter
load_filter 
clear_cache
clear_all_cache
is_cached
template_exists
get_template_vars
get_config_vars
trigger_error

fetch
get_registered_object
config_load
clear_config
_* (all the privates)
*/


class HTML_Template_Flexy_SmartyEmulator {

    var $vars; // store the vars to output here in an array

    function construct() // constructor 
    {
        
        $this->vars = array();
        $this->vars['SCRIPT_NAME'] =  $_SERVER['SCRIPT_NAME'];
    }
    
    function assign($k,$v) 
    {
        if (is_array($k)) {
            $this->vars = $k + $this->vars;
            return;
        }
        $this->vars[$k] = $v;
    }
    
    function assign_by_ref($k, &$v)
    {
        $this->vars[$k] = &$v;
    }
    
    function clear_assign($k) 
    {
        if (is_array($k)) {
            foreach ($k as $kk) {
                $this->clear_assign($kk);
            }
            return;
        }
    
        if (isset($this->vars[$k])) {
            unset($this->vars[$k]);
        }
    }
    
    function clear_all_assign() 
    {
       $this->vars = array(); 
    
    }
    
    
    function display($templatename,$object=null) 
    {
        $o = PEAR::getStaticProperty('HTML_Template_Flexy','options');
        if (!file_exists($o['templateDir'].'/'.$templatename.'.html')) {
            require_once 'Smarty.class.php';
            $x = new Smarty;
            echo "USING SMARTY? - could not find " .$o['templateDir'].'/'.$templatename.'.html';
            $x->assign($this->vars);
            $x->template_dir = $o['templateDir'];
            $x->compile_dir = ini_get('session.save_path').'/smartyEmulator';
            if (!file_exists($x->compile_dir)) {
                
                require_once 'System.php';
                System::mkdir($x->compile_dir);
            }
            return $x->display($templatename);
        }
        
        require_once 'HTML/Template/Flexy.php';
        $t = new HTML_Template_Flexy(array(
            //'compiler' => 'SmartyEmulator'
        ));
        $t->compile($templatename . '.html');
        if ($object) {
            foreach($this->vars as $k=>$v) {
                $object->$k = $v;
            }
        }
        $t->outputObject($object);
    }
    
}
    