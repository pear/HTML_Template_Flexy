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
// Plugin API provides support for  <?= $this->plugin(".....",.....); ?>
//  or {this.plugin(#xxxxx#,#xxxx#):h}
//
// BASICALLY THIS IS SAVANT'S PLUGIN PROVIDER.
// @author Paul M. Jones <pmjones@ciaweb.net>
 
 
class HTML_Template_Flexy_Plugin {

    var $flexy; // reference to flexy.
    var $pluginCache = array(); // store of instanced plugins..
    
    function call($args)
    {
        
        $requested_name = $args[0];
        // attempt to load the plugin on-the-fly
        $result = $this->_loadPlugin($requested_name);
        
        if (is_a('PEAR_Error',$result)) {
            return $result;
        }
          
         
        // first argument is always the plugin name; shift the first
        // argument off the front of the array and reduce the number of
        // array elements.
        array_shift($args);
        
        // add a reference to this Savant instance to the arguments
        // ***** Not sure if we need a reference to flexy, or the pluing manager!! ****
        array_unshift($args, $this->flexy);
        
        
        
        
        if (!isset($this->pluginCache[$classname])) {
            $this->pluginCache[$classname] = & new $classname($this->flexy);
        }
        
        return call_user_func_array(array($this->pluginCache[$classname],$method, $args);
    }
    
    
    
    
    
    
    function _loadPlugin($name) {
        // name can be:
        // ahref = maps to {class_prefix}_ahref::ahref
        $class = $method = $name;
        if ((strpos($name,':') !== false) {
            list($class,$method) = explode('::',$name);
        }
        
        if (file_exists(dirname(__FILE__)."/Plugin/$class.php")) {
            require_once dirname(__FILE__)."/Plugin/$class.php";
            return array($class,$name);
        }
        return HTML_Template_Flexy::raiseError('could not find plugin');
    }
    
    
}