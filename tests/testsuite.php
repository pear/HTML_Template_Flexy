<?php

require_once 'HTML/Template/Flexy.php';
// simple testsuite..

function compilefile($file,$data =array(),$options=array()) {
    
    $options = $options + array(
        
        'templateDir'   =>  dirname(__FILE__) .'/templates',            // where are your templates
        'forceCompile'  =>  true,  // only suggested for debugging
        'fatalError'  =>  HTML_TEMPLATE_FLEXY_ERROR_RETURN,  // only suggested for debugging
        'url_rewrite' => 'images/:/myproject/images/',
    );

// basic options..
    echo "\n\n===Compiling $file===\n\n";
    $options['compileDir']    =  dirname(__FILE__) .'/results1';
    //$options['allowPHP']      =  true;
    $x = new HTML_Template_Flexy($options);
    $res = $x->compile($file);
    if ($res !== true) {
        echo "===Compile failure==\n".$res->toString() . "\n";
        return;
    }
    echo "\n\n===Compiled file: $file===\n";
    echo file_get_contents($x->compiledTemplate);
    echo "\n\n===With data file: $file===\n";
    $data = (object)$data;
    $x->outputObject($data);
}
    
    