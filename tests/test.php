<?php


/* Mini test suite */
require_once 'HTML/Template/Flexy.php';



function compileAll($options, $files=array()) {
    
    // loop through 
    $dh = opendir(dirname(__FILE__).'/templates/');
    while (false !== ($file = readdir($dh))) {
        if ($file{0} == '.') {
            continue;
        }
        if (is_dir(dirname(__FILE__).'/tests/'.$file)) {
            continue;
        }
        // skip if not listed in files (and it's an array)
        if ($files && !in_array($file,$files)) {
            continue;
        }
        
        $x = new HTML_Template_Flexy($options);
        echo "compile $file\n";
        $res = $x->compile($file);
        if ($res !== true) {
            echo "Compile failure: ".$res->toString() . "\n";
        }
    }
    
}



$options =  array(
    
    'templateDir'   =>  dirname(__FILE__) .'/templates',            // where are your templates
    'forceCompile'  =>  true,  // only suggested for debugging
    'fatalError'  =>  PEAR_ERROR_RETURN,  // only suggested for debugging
                                                                                   
    
);
// basic options..

$options['compileDir']    =  dirname(__FILE__) .'/results1';


$a = $_SERVER['argv'];
array_shift($a);
compileAll($options,$a);

// test allowPHP 
$options['compileDir']    =  dirname(__FILE__) .'/results2';
$options['allowPHP']      =  true;
compileAll($options,array('raw_php.html'));


// test GLOBALS, privates etc.
$options['globals']         =  true;
$options['privates']        =  true;
$options['globalfunctions'] =  true;
compileAll($options,array('globals.html'));

if ($a) {
    exit;
}





$x = new HTML_Template_Flexy($options);
$x->compile('forms.html');

$tmp = new StdClass;
$tmp->xyz = "testing 123";

$elements['List'] = new HTML_Template_Flexy_Element('select');
$elements['List']->setValue(2001);
$elements['picture'] = new HTML_Template_Flexy_Element('img', "width='400' height='400' src='any.gif'");


$elements['xhtmllisttest'] = new HTML_Template_Flexy_Element;
$elements['xhtmllisttest']->setOptions(array('0'=>'--select something--','bbb'=>'somevalue'));
$elements['xhtmllisttest']->setValue('bbb');


// write the data to a file.
$data = $x->bufferedOutputObject($tmp,$elements);
$fh = fopen(dirname(__FILE__) . '/results2/forms.result.html','w');
fwrite($fh,$data);
fclose($fh);
