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
// | Authors: Alan Knowles <alan@akbkhome.com>                            |
// +----------------------------------------------------------------------+
//
// $Id$
//
//  Base Compiler Class
//  Standard 'Original Flavour' Flexy compiler

 


require_once 'HTML/Template/Flexy/Tokenizer.php';

// cache for po files..
$GLOBALS['_html_template_flexy_compiler_standard']['PO'] = array();

class HTML_Template_Flexy_Compiler_Standard extends HTML_Template_Flexy_Compiler {
    
    
    
    /**
    * The compile method.
    * 
    * @params   object HTML_Template_Flexy
    * @params   string|false string to compile of false to use a file.
    * @return   string   filename of template
    * @access   public
    */
    function compile(&$flexy,$string=false) 
    {
        // read the entire file into one variable
        
        // note this should be moved to new HTML_Template_Flexy_Token
        // and that can then manage all the tokens in one place..
        global $_HTML_TEMPLATE_FLEXY_COMPILER;
        
        $gettextStrings = &$_HTML_TEMPLATE_FLEXY_COMPILER['gettextStrings'];
        $gettextStrings = array(); // reset it.
        
        if (@$this->options['debug']) {
            echo "compiling template $flexy->currentTemplate<BR>";
            
        }
         
        // reset the elements.
        $flexy->_elements = array();
        
        // replace this with a singleton??
        
        $GLOBALS['_HTML_TEMPLATE_FLEXY']['currentOptions'] = $this->options;
        $GLOBALS['_HTML_TEMPLATE_FLEXY']['elements'] = array();
        $GLOBALS['_HTML_TEMPLATE_FLEXY']['filename'] = $flexy->currentTemplate;
        
        
        if (is_array($this->options['Translation2'])) {
            require_once 'Translation2.php';
            $this->options['Translation2'] = new Translation2(
                $this->options['Translation2']['driver'],
                @$this->options['Translation2']['options']
            );
        }
        
        
        if (is_a($this->options['Translation2'],'Translation2')) {
            $this->options['Translation2']->setLang($this->options['locale']);
            // fixme - needs to be more specific to which template to use..
            $this->options['Translation2']->setPageID(basename($flexy->currentTemplate));
        } else {
            setlocale(LC_ALL, $this->options['locale']);
        }
        
        
        
        
        
        
        
        $data = $string;
        if ($string === false) {
            $data = file_get_contents($flexy->currentTemplate);
        }
        
        
        
        // PRE PROCESS {_(.....)} translation markers.
        $got_gettext_markup = false;
        
        if (preg_match_all('/\{_\((.*)\)_\}/', $data,$matches)) {
            // echo '<PRE>';print_r($matches);
            // we may need to do some house cleaning here...
            $gettextStrings = $matches[1];
            $got_gettext_markup = true;
            
            
            // replace them now..  
            // ** leaving in the tag (which should be ignored by the parser..
            // we then get rid of the tags during the toString method in this class.
            foreach($matches[1] as $k=>$v) {
                $data = str_replace('{_('.$v.')_}', '{_('.$this->translateString($v).')_}',$data);
            }
            
        }
            
        
        
        
        
        
        
        $tokenizer = new HTML_Template_Flexy_Tokenizer($data);
        $tokenizer->fileName = $flexy->currentTemplate;
        
        
        //$tokenizer->debug=1;
        if ($this->options['nonHTML']) {
            $tokenizer->ignoreHTML = true;
        }
        if ($this->options['allowPHP']) {
            $tokenizer->ignorePHP = false;
        }
        
        $res = HTML_Template_Flexy_Token::buildTokens($tokenizer);
            
        // turn tokens into Template..
        
        $data = $res->compile($this);
        
        
        
        
        if (   @$this->options['debug']) {
            echo "<B>Result: </B><PRE>".htmlspecialchars($data)."</PRE><BR>";
            
        }

        if ($this->options['nonHTML']) {
           $data =  str_replace("?>\n","?>\n\n",$data);
        }
        
         
        
        
        // at this point we are into writing stuff...
        if ($this->options['compileToString']) {
            $flexy->elements =  $GLOBALS['_HTML_TEMPLATE_FLEXY']['elements'];
            return $data;
        }
        
        
        
        
        // error checking?
        if( ($cfp = fopen( $flexy->compiledTemplate , 'w' )) ) {
            if (@$this->options['debug']) {
                echo "<B>Writing: </B>".htmlspecialchars($data)."<BR>";
                
            }
            fwrite($cfp,$data);
            fclose($cfp);
             chmod($flexy->compiledTemplate,0775);
            // make the timestamp of the two items match.
            clearstatcache();
            touch($flexy->compiledTemplate, filemtime($flexy->currentTemplate));
            
        } else {
            PEAR::raiseError('HTML_Template_Flexy::failed to write to '.$flexy->compiledTemplate,null,PEAR_ERROR_DIE);
        }
        // gettext strings
        if (file_exists($flexy->getTextStringsFile)) {
            unlink($flexy->getTextStringsFile);
        }
        
        if($gettextStrings && ($cfp = fopen( $flexy->getTextStringsFile, 'w') ) ) {
            
            fwrite($cfp,serialize(array_unique($gettextStrings)));
            fclose($cfp);
        }
        
        // elements
        if (file_exists($flexy->elementsFile)) {
            unlink($flexy->elementsFile);
        }
        
        if( $GLOBALS['_HTML_TEMPLATE_FLEXY']['elements'] &&
            ($cfp = fopen( $flexy->elementsFile, 'w') ) ) {
            fwrite($cfp,serialize( $GLOBALS['_HTML_TEMPLATE_FLEXY']['elements']));
            fclose($cfp);
            // now clear it.
        
        }
        
        return true;
    }
    
    /**
    * Flag indicating compiler is inside {_( .... )_} block, and should not
    * add to the gettextstrings array.
    *
    * @var boolean 
    * @access public
    */
    var $inGetTextBlock = false;
    
    /**
    * This is the base toString Method, it relays into toString{TokenName}
    *
    * @param    object    HTML_Template_Flexy_Token_*
    * 
    * @return   string     string to build a template
    * @access   public 
    * @see      toString*
    */
  

    function toString($element) 
    {
        static $len = 26; // strlen('HTML_Template_Flexy_Token_');
        //echo $element->token . ':' . htmlspecialchars($element->value)."<BR>";
        if ($element->token == 'GetTextStart') {
            $this->inGetTextBlock = true;
            return '';
        }
        if ($element->token == 'GetTextEnd') {
            $this->inGetTextBlock = false;
            return '';
        }
        
            
        $class = get_class($element);
        if (strlen($class) >= $len) {
            $type = substr($class,$len);
            //echo "CALL $type";
            return $this->{'toString'.$type}($element);
        }
        
        $ret = $element->value;
        $ret .= $element->compileChildren($this);
        if ($element->close) {
            $ret .= $element->close->compile($this);
        }
        
        return $ret;
        
        
    }


    /**
    *   HTML_Template_Flexy_Token_Else toString 
    *
    * @param    object    HTML_Template_Flexy_Token_Else
    * 
    * @return   string     string to build a template
    * @access   public 
    * @see      toString*
    */
  

    function toStringElse($element) 
     {
        // pushpull states to make sure we are in an area.. - should really check to see 
        // if the state it is pulling is a if...
        if ($element->pullState() === false) {
            return $this->appendHTML(
                "<font color=\"red\">Unmatched {else:} on line: {$element->line}</font>"
                );
        }
        $element->pushState();
        return $this->appendPhp("} else {");
    }
    
    /**
    *   HTML_Template_Flexy_Token_End toString 
    *
    * @param    object    HTML_Template_Flexy_Token_Else
    * 
    * @return   string     string to build a template
    * @access   public 
    * @see      toString*
    */
  
    function toStringEnd($element) 
    {
        // pushpull states to make sure we are in an area.. - should really check to see 
        // if the state it is pulling is a if...
        if ($element->pullState() === false) {
            return $this->appendHTML(
                "<font color=\"red\">Unmatched {end:} on line: {$element->line}</font>"
                );
        }
         
        return $this->appendPhp("}");
    }

    /**
    *   HTML_Template_Flexy_Token_EndTag toString 
    *
    * @param    object    HTML_Template_Flexy_Token_EndTag
    * 
    * @return   string     string to build a template
    * @access   public 
    * @see      toString*
    */
  


    function toStringEndTag($element) 
    {
        return $this->toStringTag($element);
    }
        
    
    
    /**
    *   HTML_Template_Flexy_Token_Foreach toString 
    *
    * @param    object    HTML_Template_Flexy_Token_Foreach 
    * 
    * @return   string     string to build a template
    * @access   public 
    * @see      toString*
    */
  
    
    function toStringForeach($element) 
    {
    
        
    
        $ret = 'if (is_array('.
            $element->toVar($element->loopOn) . ")  || " .
            'is_object(' . $element->toVar($element->loopOn) . ')) ' .
            'foreach(' . $element->toVar($element->loopOn) . " ";
            
        $ret .= "as \${$element->key}";
        
        if ($element->value) {
            $ret .=  " => \${$element->value}";
        }
        $ret .= ") {";
        
        $element->pushState();
        $element->pushVar($element->key);
        $element->pushVar($element->value);
        return $this->appendPhp($ret);
    }
    /**
    *   HTML_Template_Flexy_Token_If toString 
    *
    * @param    object    HTML_Template_Flexy_Token_If
    * 
    * @return   string     string to build a template
    * @access   public 
    * @see      toString*
    */
  
    function toStringIf($element) 
    {
        $ret = "if (".$element->isNegative . $element->toVar($element->condition) .")  {";
        $element->pushState();
        return $this->appendPhp($ret);
    }

   /**
    *  get Modifier Wrapper 
    *
    * converts :h, :u, :r , .....
    * @param    object    HTML_Template_Flexy_Token_Method|Var
    * 
    * @return   array prefix,suffix
    * @access   public 
    * @see      toString*
    */

    function getModifierWrapper($element) 
    {
        $prefix = 'echo ';
        
        $suffix = '';
        $modifier = $element->modifier . ' ';
        switch ($modifier{0}) {
            case 'h':
                break;
            case 'u':
                $prefix = 'echo urlencode(';
                $suffix = ')';
                break;
            case 'r':
                $prefix = 'echo \'<pre>\'; echo htmlspecialchars(print_r(';
                $suffix = ',true)); echo \'</pre>\';';
                break;                
            case 'n': 
                // blank or value..
                $numberformat = @$GLOBALS['_HTML_TEMPLATE_FLEXY']['currentOptions']['numberFormat'];
                $prefix = 'echo number_format(';
                $suffix = $GLOBALS['_HTML_TEMPLATE_FLEXY']['currentOptions']['numberFormat'] . ')';
                break;
            case 'b': // nl2br + htmlspecialchars
                $prefix = 'echo nl2br(htmlspecialchars(';
                
                // add language ?
                $suffix = '))';
                break;
            default:
                $prefix = 'echo htmlspecialchars(';
                // add language ?
                $suffix = ')';
        }
        
        return array($prefix,$suffix);
    }



  /**
    *   HTML_Template_Flexy_Token_Var toString 
    *
    * @param    object    HTML_Template_Flexy_Token_Method
    * 
    * @return   string     string to build a template
    * @access   public 
    * @see      toString*
    */
  
    function toStringVar($element) 
    {
        // ignore modifier at present!!
        list($prefix,$suffix) = $this->getModifierWrapper($element);
        return $this->appendPhp( $prefix . $element->toVar($element->value) . $suffix .';');
    }
   /**
    *   HTML_Template_Flexy_Token_Method toString 
    *
    * @param    object    HTML_Template_Flexy_Token_Method
    * 
    * @return   string     string to build a template
    * @access   public 
    * @see      toString*
    */
  
    function toStringMethod($element) 
    {

              
        // set up the modifier at present!!
        list($prefix,$suffix) = $this->getModifierWrapper($element);
        
        // add the '!' to if
        
        if ($element->isConditional) {
            $prefix = 'if ('.$element->isNegative;
            $element->pushState();
            $suffix = ')';
        }  
        
        
        // check that method exists..
        // if (method_exists($object,'method');
        $bits = explode('.',$element->method);
        $method = array_pop($bits);
        
        $object = implode('.',$bits);
        
        $prefix = 'if (isset('.$element->toVar($object).
            ') && method_exists('.$element->toVar($object) .",'{$method}')) " . $prefix;
        
        
        
        $ret  =  $prefix;
        $ret .=  $element->toVar($element->method) . "(";
        $s =0;
         
       
         
        foreach($element->args as $a) {
             
            if ($s) {
                $ret .= ",";
            }
            $s =1;
            if ($a{0} == '#') {
                $ret .= '"'. addslashes(substr($a,1,-1)) . '"';
                continue;
            }
            $ret .= $element->toVar($a);
            
        }
        $ret .= ")" . $suffix;
        
        if ($element->isConditional) {
            $ret .= ' { ';
        } else {
            $ret .= ";";
        }
        
        
        
        return $this->appendPhp($ret); 
        
         

   }
   /**
    *   HTML_Template_Flexy_Token_Processing toString 
    *
    * @param    object    HTML_Template_Flexy_Token_Processing 
    * 
    * @return   string     string to build a template
    * @access   public 
    * @see      toString*
    */


    function toStringProcessing($element) 
    {
        // if it's XML then quote it..
        if (strtoupper(substr($element->value,2,3)) == 'XML') { 
            return $this->appendPhp("echo '" . str_replace("'","\\"."'", $element->value) . "';");
        }
        // otherwise it's PHP code - so echo it..
        return $element->value;
    }
    
    /**
    *   HTML_Template_Flexy_Token_Text toString 
    *
    * @param    object    HTML_Template_Flexy_Token_Text
    * 
    * @return   string     string to build a template
    * @access   public 
    * @see      toString*
    */



    function toStringText($element) 
    {
        // if it's XML then quote it..
        
        /**
        * Global variable for gettext replacement
        * static object vars will be nice in PHP5 :)
        *
        * @var array
        * @access private
        */
        global $_HTML_TEMPLATE_FLEXY_COMPILER;
        
        $gettextStrings = &$_HTML_TEMPLATE_FLEXY_COMPILER['gettextStrings'];
        
        static $cleanArray = array(
            '$' => '\$',
            '"' => '\"',
            "'" => '\\\'',
            '\\' => '\\\\',
            "\n" => '\n',
            "\t" => '\t',
            "\r" => '\r'
        );
        static $uncleanArray = false;
        if (!$uncleanArray ) {
            $uncleanArray = array_flip($cleanArray);
        }

         
        if (!strlen(trim($element->value) )) {
            return $this->appendHtml($element->value);
        }
        // dont add comments to translation lists.
         
        if (substr($element->value,0,4) == '<!--') {
            return $this->appendHtml($element->value);
        }
        if (!count($element->argTokens) && !$element->isWord()) {
            return $this->appendHtml($element->value);
        }
        
        // ignore anything wrapped with {_( .... )_}
        if ($this->inGetTextBlock) {
            return $this->appendHtml($element->value);
        }
        
        
        
        $front = '';
        $rear = '';
        // trim whitespace front
        for ($i=0;$i<strlen($element->value); $i++) {
            if (strpos(" \n\t\r\0\x0B", $element->value{$i}) !== false) {
                $front .= $element->value{$i};
                continue;
            }
            break;
        }
        // trim whitespace rear 
        for ($i=strlen($element->value)-1;$i>-1; $i--) {
            if (strpos(" \n\t\r\0\x0B", $element->value{$i}) !== false) {
                $rear = $element->value{$i} . $rear;
                continue;
            }
            break;            
        }
        
        $value = trim($element->value);
        
        
        // convert to escaped chars.. (limited..)
        $value = strtr($value,$cleanArray);
        
        
        // its a simple word!
        if (!count($element->argTokens)) {
            $gettextStrings[] = $value;
            $value = $this->translateString($value);
            $value = strtr($value,$uncleanArray);
        
            return $this->appendHtml($front .  $value  . $rear);
        }
        
        
        // there are subtokens..
        // print_r($element->argTokens );
        $args = array();
        $argsMake = '';
        // these should only be text or vars..
        foreach($element->argTokens as $i=>$token) {
            $args[] = $token->compile($this);
        }
        
        $gettextStrings[] = $value;
        $value = $this->translateString($value);
        $value = strtr($value,$uncleanArray);
        
        $bits = explode('%s',$value);
        $ret = $front;
        foreach($bits as $i=>$v) {
            $ret.=$v.@$args[$i];
        }
        
        return  $ret . $rear;
        
    }
    
    /**
    * translateString - a gettextWrapper
    *
    * tries to do gettext or falls back on File_Gettext
    * This has !!!NO!!! error handling - if it fails you just get english.. 
    * no questions asked!!!
    * 
    * @param   string       string to translate
    *
    * @return   string      translated string..
    * @access   public
    */
  
    function translateString($string)
    {
         
        
        
        if (is_a($this->options['Translation2'],'Translation2')) {
            $result = $this->options['Translation2']->get($string);
            if (!empty($result)) {
                return $result;
            }
            return $string;
        }
        
        
        
        $prefix = basename($GLOBALS['_HTML_TEMPLATE_FLEXY']['filename']).':';
        if (@$this->options['debug']) {
            echo __CLASS__.":TRANSLATING $string<BR>";
        }
        if (function_exists('gettext') && !$this->options['textdomain']) {
            if (@$this->options['debug']) {
                echo __CLASS__.":USING GETTEXT?<BR>";
            }
            $t = gettext($string);
            if ($t != $string) {
                return $t;
            }
            $tt = gettext($prefix.$string);
            if ($tt != $prefix.$string) {
                return $tt;
            }
            // give up it's not translated anywhere...
            return $t;
             
        }
        if (!$this->options['textdomain'] || !$this->options['textdomainDir']) {
            // text domain is not set..
            if (@$this->options['debug']) {
                echo __CLASS__.":MISSING textdomain settings<BR>";
            }
            return $string;
        }
        $pofile = $this->options['textdomainDir'] . 
                '/' . $this->options['locale'] . 
                '/LC_MESSAGES/' . $this->options['textdomain'] . '.po';
        
        
        // did we try to load it already..
        if (@$GLOBALS['_'.__CLASS__]['PO'][$pofile] === false) {
            if (@$this->options['debug']) {
                echo __CLASS__.":LOAD failed (Cached):<BR>";
            }
            return $string;
        }
        if (!@$GLOBALS['_'.__CLASS__]['PO'][$pofile]) {
            // default - cant load it..
            $GLOBALS['_'.__CLASS__]['PO'][$pofile] = false;
            if (!file_exists($pofile)) {
                 if (@$this->options['debug']) {
                echo __CLASS__.":LOAD failed: {$pofile}<BR>";
            }
                return $string;
            }
            
            if (!@include_once 'File/Gettext.php') {
                if (@$this->options['debug']) {
                    echo __CLASS__.":LOAD no File_gettext:<BR>";
                }
                return $string;
            }
            
            $GLOBALS['_'.__CLASS__]['PO'][$pofile] = File_Gettext::factory('PO',$pofile);
            $GLOBALS['_'.__CLASS__]['PO'][$pofile]->load();
            //echo '<PRE>'.htmlspecialchars(print_r($GLOBALS['_'.__CLASS__]['PO'][$pofile]->strings,true));
            
        }
        $po = &$GLOBALS['_'.__CLASS__]['PO'][$pofile];
        // we should have it loaded now...
        // this is odd - data is a bit messed up with CR's
        $string = str_replace('\n',"\n",$string);
        
        if (isset($po->strings[$prefix.$string])) {
            return $po->strings[$prefix.$string];
        }
        
        if (!isset($po->strings[$string])) {
            if (@$this->options['debug']) {
                    echo __CLASS__.":no match:<BR>";
            }
            return $string;
        }
        if (@$this->options['debug']) {
            echo __CLASS__.":MATCHED: {$po->strings[$string]}<BR>";
        }
        
        // finally we have a match!!!
        return $po->strings[$string];
        
    } 
     /**
    *   HTML_Template_Flexy_Token_Tag toString 
    *
    * @param    object    HTML_Template_Flexy_Token_Tag
    * 
    * @return   string     string to build a template
    * @access   public 
    * @see      toString*
    */
  
    function toStringTag($element) {
        if (strpos($element->tag,':') === false) {
            $namespace = 'Tag';
        } else {
            $bits =  explode(':',$element->tag);
            $namespace = $bits[0];
        }
	if ($namespace{0} == '/') {
		return '';
        }
        if (empty($this->tagHandlers[$namespace])) {
            
            require_once 'HTML/Template/Flexy/Compiler/Standard/Tag.php';
            $this->tagHandlers[$namespace] = &HTML_Template_Flexy_Compiler_Standard_Tag::factory($namespace,$this);
            if (!$this->tagHandlers[$namespace] ) {
                PEAR::raiseError('HTML_Template_Flexy::failed to create Namespace Handler '.$namespace . 
                ' in file ' . $GLOBALS['_HTML_TEMPLATE_FLEXY']['filename'],null,PEAR_ERROR_DIE);
            }
                
        }
        return $this->tagHandlers[$namespace]->toString($element);
        
        
    }
    

}
