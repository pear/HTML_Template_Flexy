<?php


/* Mini test suite */
require_once 'HTML/Template/Flexy.php';


$x = new HTML_Template_Flexy(array(
                'compileDir'    =>  dirname(__FILE__),      // where do you want to write to..
                'templateDir'   =>  dirname(__FILE__),     // where are your templates
                'locale'        => 'en',    // works with gettext
                'forceCompile'  =>  true,  // only suggested for debugging
                                                                                               
                'debug'         => false,   // prints a few errors
                             
                'nonHTML'       => false,  // dont parse HTML tags (eg. email templates)
                'allowPHP'      => false,   // allow PHP in template
                'compiler'      => 'Standard', // which compiler to use.
                'compileToString' => false,    // should the compiler return a string
                                                            // rather than writing to a file.
                'filters'       => array(),    // used by regex compiler..
                'numberFormat'  => ",2,'.',','",  // default number format  = eg. 1,200.00 ( {xxx:n} )
                'flexyIgnore'   => 0,        // turn on/off the tag to element code
                'fatalError'    => PEAR_ERROR_RETURN,
            ));

$res = $x->compile('example.html');
if ($res !== true) {
    echo "Compile failure: ".$res->toString() . "\n";

}

// now try with privates.
$x = new HTML_Template_Flexy(array(
                'compileDir'    =>  dirname(__FILE__),      // where do you want to write to..
                'templateDir'   =>  dirname(__FILE__),     // where are your templates
                'locale'        => 'en',    // works with gettext
                'forceCompile'  =>  true,  // only suggested for debugging
                                                                                               
                'debug'         => false,   // prints a few errors
                             
                'nonHTML'       => false,  // dont parse HTML tags (eg. email templates)
                'allowPHP'      => false,   // allow PHP in template
                'compiler'      => 'Standard', // which compiler to use.
                'compileToString' => false,    // should the compiler return a string
                                                            // rather than writing to a file.
                'filters'       => array(),    // used by regex compiler..
                'numberFormat'  => ",2,'.',','",  // default number format  = eg. 1,200.00 ( {xxx:n} )
                'flexyIgnore'   => 0,        // turn on/off the tag to element code
                'privates'      => true, // allow privates
                'globals'       => true, // allow globals

            ));

$res = $x->compile('example.html');





$tmp = new StdClass;
$tmp->xyz = "testing 123";

$elements['List'] = new HTML_Template_Flexy_Element('select');
$elements['List']->setValue(2001);

$elements['picture'] = new HTML_Template_Flexy_Element('img', "width='400' height='400' src='any.gif'");
 




$data = $x->bufferedOutputObject($tmp,$elements);


$fh = fopen(dirname(__FILE__) . '/example.result.html','w');
fwrite($fh,$data);
fclose($fh);