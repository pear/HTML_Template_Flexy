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
//  The Source Lex file. (Tokenizer.lex) and the Generated one (Tokenizer.php)
// You should always work with the .lex file and generate by
//
// #mono phpLex/phpLex.exe Tokenizer.lex
//
//
// or the equivialant .NET runtime on windows...
//
//  Note need to change a few of these defines, and work out
// how to modifiy the lexer to handle the changes..
//

require_once 'HTML/Template/Flexy/Token.php';


define('HTML_TEMPLATE_FLEXY_TOKEN_NONE',1);
define('HTML_TEMPLATE_FLEXY_TOKEN_OK',2);
define('HTML_TEMPLATE_FLEXY_TOKEN_ERROR',3);

 

define("YYINITIAL"     ,0);
define("IN_SINGLEQUOTE"     ,1) ;
define("IN_TAG"     ,2)  ;
define("IN_ATTR"     ,3);
define("IN_ATTRVAL"     ,4) ;
define("IN_NETDATA"     ,5);
define("IN_ENDTAG"     ,6);
define("IN_DOUBLEQUOTE"     ,7);
define("IN_MD"     ,8);
define("IN_COM"     ,9);
define("IN_DS"     ,10);
 
define("IN_FLEXYMETHOD"     ,11);
 
define("IN_FLEXYMETHODQUOTED"     ,11);


define('YY_E_INTERNAL', 0);
define('YY_E_MATCH',  1);
define('YY_BUFFER_SIZE', 4096);
define('YY_F' , -1);
define('YY_NO_STATE', -1);
define('YY_NOT_ACCEPT' ,  0);
define('YY_START' , 1);
define('YY_END' , 2);
define('YY_NO_ANCHOR' , 4);
define('YY_BOL' , 257);
define('YY_EOF' , 258);
   
%%
%namespace HTML_Template_Flexy_Tokenizer
%public
%class HTML_Template_Flexy_Tokenizer
%implements yyParser.yyInput 
%type int
%ignore_token  HTML_TEMPLATE_FLEXY_TOKEN_NONE
%eofval{
	return TOKEN_EOF;
%eofval}


%{
    
        
    function dump () {
        foreach(get_object_vars($this) as  $k=>$v) {
            if (is_string($v)) { continue; }
            if (is_array($v)) { continue; }
            echo "$k = $v\n";
        }
    }
    
    
	function error($n,$s) {
        echo "Error $n: $s\n";
    }
     
    
     
   

%}

%line
%full
%char
%state IN_SINGLEQUOTE IN_TAG IN_ATTR IN_ATTRVAL IN_NETDATA IN_ENDTAG IN_DOUBLEQUOTE IN_MD IN_COM IN_DS IN_FLEXYMETHOD IN_FLEXYMETHODQUOTED

 





DIGIT   =		[0-9]
LCLETTER =	[a-z]

UCLETTER =	[A-Z]


LCNMCHAR	= [\.-]
UCNMCHAR	= [\.-]
RE		 = \n
RS		 = \r
SEPCHAR	 = \011
SPACECHAR	=	\040


COM 	="--"
CRO 	="&#"
DSC	    ="]"
DSO	    ="["
ERO 	="&"
ETAGO	="</"
LIT	    = \"
LITA    = "'"

/* ' hack comment to make syntax highlighting to work in scintilla*/

MDO	    = "<!"
MSC	    = "]]"
NET     = "/"
PERO    = "%"
PIC	    = ">"
PIO	    = "<?"
REFC    = ";"
STAGO   = "<"
TAGC    = ">"

NAME_START_CHARACTER	= ({LCLETTER}|{UCLETTER})
NAME_CHARACTER		    = ({NAME_START_CHARACTER}|{DIGIT}|{LCNMCHAR}|{UCNMCHAR})

NAME		            = ({NAME_START_CHARACTER}{NAME_CHARACTER}*)
NUMBER		            = {DIGIT}+
NUMBER_TOKEN            = {DIGIT}+{NAME_CHARACTER}*
NAME_TOKEN	            = {NAME_CHARACTER}+

SPACE	                = ({SPACECHAR}|{RE}|{RS}|{SEPCHAR})
SPACES		            = ({SPACECHAR}|{RE}|{RS}|{SEPCHAR})+

WHITESPACE		        = ({SPACECHAR}|{RE}|{RS}|{SEPCHAR})*

REFERENCE_END	        = ({REFC}|{RE})
LITERAL		            = ({LIT}[^\"]*{LIT})|({LITA}[^\']*{LITA})

 



FLEXY_START         = ("%7B"|"%7b"|"{")
FLEXY_VALID_CHARS   = ({LCLETTER}|{UCLETTER}|"_"|"."|{DIGIT})
FLEXY_VAR           = ({NAME_START_CHARACTER}{FLEXY_VALID_CHARS}*)
FLEXY_SIMPLEVAR     = ({NAME_START_CHARACTER}({LCLETTER}|{UCLETTER}|"_"|{DIGIT})*)
FLEXY_END            = ("%7D"|"%7d"|"}")
FLEXY_LITERAL       = [^#]*
FLEXY_MODIFIER      = [hur]



%%

 
// "


<YYINITIAL>{CRO}{NUMBER}{REFERENCE_END}?	 {
    // &#123;
    $this->value = HTML_Template_Flexy_Token::create('Ref',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}


<YYINITIAL>{CRO}{NAME}{REFERENCE_END}?		{
    // &#abc;
    $this->value = HTML_Template_Flexy_Token::create('Ref',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}

  
<YYINITIAL>{ERO}{NAME}{REFERENCE_END}?	{
    // &abc;
    $this->value = HTML_Template_Flexy_Token::create('Ref',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}

  
<YYINITIAL>{ETAGO}{NAME}?{WHITESPACE}"/"{STAGO} {
    /* </name <  -- unclosed end tag */
    $this->error(0,"Unclosed  end tag");
    return HTML_TEMPLATE_FLEXY_ERROR;
}

  
<YYINITIAL>{ETAGO}{NAME}{WHITESPACE} {
    /* </title> -- end tag */
    $this->tagName = trim(substr($this->yytext(),1));
    $this->tokenName = 'EndTag';
    $this->yybegin(IN_ENDTAG);
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}

 

<YYINITIAL>{ETAGO}{TAGC}			{
	/* </> -- empty end tag */		
    $this->error(0,"empty end tag not handled");
    return HTML_TEMPLATE_FLEXY_ERROR;
}
            
<YYINITIAL>{MDO}{NAME}{WHITESPACE}			{
    /* <!DOCTYPE -- markup declaration */
    $this->value = HTML_Template_Flexy_Token::create('Doctype',$this->yytext(),$this->yyline);
    $this->yybegin(IN_MD);
    
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}

  
<YYINITIAL>{MDO}{TAGC}			{ 
    /* <!> */
    $this->error(SGML_ERROR,"empty markup tag not handled"); 
    return HTML_TEMPLATE_FLEXY_ERROR;
}



<YYINITIAL>{MDO}{COM}			{
    /* <!--  -- comment declaration */
    
    $this->value = HTML_Template_Flexy_Token::create('Comment',$this->yytext(),$this->yyline);
    $this->yybegin(IN_COM);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}


<YYINITIAL>{MDO}{DSO}{WHITESPACE}			{
    /* <![ -- marked section */
    $this->error(SGML_ERROR,"marked section not handled"); 
    return HTML_TEMPLATE_FLEXY_ERROR;
}


<YYINITIAL>{MSC}{TAGC}		{ 
    /* ]]> -- marked section end */
    $this->error(SGML_ERROR,"unmatched marked sections end"); 
    return HTML_TEMPLATE_FLEXY_ERROR;
}
    
  
<YYINITIAL>{STAGO}"?"[^>]*{TAGC}			{ 
    /* <? ...> -- processing instruction */
    // this is a little odd cause technically we dont allow it!!
    // really we only want to handle < ? xml 
    $this->value = HTML_Template_Flexy_Token::create('PHP',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
 
				 
  
<YYINITIAL>{STAGO}{NAME}{WHITESPACE}		{
    //<name -- start tag */
     $this->tagName = trim(substr($this->yytext(),1));
    $this->tokenName = 'Tag';
    $this->value = '';
    $this->attributes = array();
    $this->yybegin(IN_ATTR);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}


  
<YYINITIAL>{STAGO}{TAGC}			{  
    // <> -- empty start tag */
    $this->error(0,"empty tag"); 
    return HTML_TEMPLATE_FLEXY_ERROR;
}

  
<YYINITIAL>([^\<\&\{]|(<[^<&a-zA-Z!->?])|(&[^<&#a-zA-Z]))+|"{"     {
    //abcd -- data characters  
    // { added for flexy
    $this->value = HTML_Template_Flexy_Token::create('Text',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}




  
<IN_NETDATA>{SPACES} { 
    // whitespace switch back to IN_ATTR MODE.
    $this->value = '';
    $this->yybegin(IN_ATTR);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}


  
<IN_ATTR>{NAME}{SPACE}*={WHITESPACE}		{
   // <a ^href = "xxx"> -- attribute name 
    $this->attrKey = substr(trim($this->yytext()),0,-1);
    $this->yybegin(IN_ATTRVAL);
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}

  
<IN_ATTR>{NAME}{WHITESPACE}		{
    // <img src="xxx" ^ismap> -- name */
    $this->attributes[trim($this->yytext())] = null;
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}


<IN_ATTRVAL>{NAME_TOKEN}{WHITESPACE}	{
    // <a name = ^12pt> -- number token */
    $this->attributes[$this->attrKey] = trim($this->yytext());
    $this->yybegin(IN_ATTR);
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}

  
<IN_ATTRVAL>{NUMBER_TOKEN}{WHITESPACE}	{
    // <a name = ^xyz> -- name token */
    $this->attributes[$this->attrKey] = trim($this->yytext());
    $this->yybegin(IN_ATTR);
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
                 


//<IN_ATTRVAL>{LITERAL}{WHITESPACE}		{
    // <a href = ^"a b c"> -- literal */
    // TODO add flexy parsing in here!!!
//    $this->attributes[$this->attrKey] = trim($this->yytext());
//    $this->yybegin(IN_ATTR);
//    $this->value = '';
//    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
//}
 
<IN_ATTRVAL>"'"    {
    $this->attrVal = array( "'");
    $this->yybegin(IN_SINGLEQUOTE);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}

<IN_ATTRVAL>\"    {
    //echo "START QUOTE";
    $this->attrVal =array("\"");
    
    $this->yybegin(IN_DOUBLEQUOTE);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}

<IN_SINGLEQUOTE>(([^\{\%\'\]+|\\[^\']|"%"|"{")+)	{
    
    $this->attrVal[] = $this->yytext();
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}

<IN_SINGLEQUOTE>"'" {
    $this->attrVal[] = "'";
    //var_dump($this->attrVal);
    $s = "";
    foreach($this->attrVal as $v) {
        if (!is_string($v)) {
            $this->attributes[$this->attrKey] = $this->attrVal;
            $this->yybegin(IN_ATTR);
            return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
        }
        $s .= $v;
    }
    $this->attributes[$this->attrKey] = $s;
    $this->yybegin(IN_ATTR);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}

<IN_DOUBLEQUOTE>([^\{\%\"\\]|\\[^\"\\])+|"%"|"{" {
    //echo "GOT DATA:".$this->yytext();
    $this->attrVal[] = $this->yytext();
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
} 

<IN_DOUBLEQUOTE>\" {
    //echo "GOT END DATA:".$this->yytext();
    $this->attrVal[] = "\"";
    
    $s = "";
    foreach($this->attrVal as $v) {
        if (!is_string($v)) {
            $this->attributes[$this->attrKey] = $this->attrVal;
            $this->yybegin(IN_ATTR);
            return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
        }
        $s .= $v;
    }
    $this->attributes[$this->attrKey] = $s;
    $this->yybegin(IN_ATTR);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
    
}

 






  // <a name= ^> -- illegal tag close */
<IN_ATTRVAL>{TAGC}			{ 
    $this->error(0, "Tag close found where attribute value expected"); 
    return HTML_TEMPLATE_FLEXY_ERROR;
}

  // <a name=foo ^>,</foo^> -- tag close */
<IN_ATTR,IN_TAG>{TAGC}		{
    $this->value = HTML_Template_Flexy_Token::create($this->tokenName,
        array($this->tagName,$this->attributes),
        $this->yyline);
    
    $this->yybegin(YYINITIAL);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}

  // <em^/ -- NET tag */
<IN_ATTRVAL>{NET}	{
    $this->error(0,"attribute value missing"); 
    return HTML_TEMPLATE_FLEXY_ERROR;
}

  // <em^/ -- NET tag */
<IN_ATTR>{NET}	{
    $this->yybegin(IN_NETDATA);
    $this->attributes["/"] = null;
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
} 

<IN_ATTR>{NET}{WHITESPACE}{TAGC}	{
    $this->attributes["/"] = null;
    $this->value = HTML_Template_Flexy_Token::create($this->tokenName,
        array($this->tagName,$this->attributes),
        $this->yyline);
        
    $this->yybegin(YYINITIAL);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
 
  
<IN_ATTR,IN_ATTRVAL,IN_TAG> {STAGO}	{
    // <foo^<bar> -- unclosed start tag */
    $this->error(0,"Unclosed tags not supported"); 
    return HTML_TEMPLATE_FLEXY_ERROR;
}

  
  
<IN_ATTRVAL> ([^ \"\t\n\r>]+){WHITESPACE}	{
    // <a href = ^http://foo/> -- unquoted literal HACK */                          
    $this->attributes[$this->attrKey] = trim($this->yytext());
    $this->yybegin(IN_ATTR);
    //   $this->error(SGML_ERROR,"attribute value needs quotes");
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
// "



<IN_TAG,IN_ATTR> {WHITESPACE}	{
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}


<IN_ATTR,IN_ATTRVAL,IN_TAG> .	{
    $this->error(0,"ERROR: unexpected : character in tag: (".$this->yytext().") 0x" . dechex(ord($this->yytext())));
    return HTML_TEMPLATE_FLEXY_ERROR;
}

  // end tag -- non-permissive */
<IN_ENDTAG>{TAGC} { 
    $this->value = HTML_Template_Flexy_Token::create($this->tokenName,
        array($this->tagName),
        $this->yyline);
        array($this->tagName);
    $this->yybegin(YYINITIAL);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}

<IN_ENDTAG>. { 
    $this->error(0,"extraneous character in end tag"); 
    return HTML_TEMPLATE_FLEXY_ERROR;
}

 

 // 10 Markup Declarations: General */

 
<IN_COM>([^-]|-[^-])*{WHITESPACE}	{
    // <!^--...-->   -- comment */   
    $this->value = HTML_Template_Flexy_Token::create('Comment',$this->yytext(),$this->yyline);
     
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}

 
<IN_MD>{PERO}{NAME}{REFERENCE_END}?{WHITESPACE}		{
    // <!doctype ^%foo;> -- parameter entity reference */
    $this->value = HTML_Template_Flexy_Token::create('EntityRef',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}

 
<IN_MD>{PERO}{SPACES}			{
    // <!entity ^% foo system "..." ...> -- parameter entity definition */
    $this->value = HTML_Template_Flexy_Token::create('EntityPar',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
 
<IN_MD>{NUMBER}{WHITESPACE}		{   
    $this->value = HTML_Template_Flexy_Token::create('Number',$this->yytext(),$this->yyline);
    
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
<IN_MD>{NAME}{WHITESPACE}			{ 
    $this->value = HTML_Template_Flexy_Token::create('Name',$this->yytext(),$this->yyline);
    
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
<IN_MD>{NUMBER_TOKEN}{WHITESPACE}		{ 
    $this->value = HTML_Template_Flexy_Token::create('NumberT',$this->yytext(),$this->yyline);    
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
<IN_MD>{NAME_TOKEN}{WHITESPACE}		{ 
    $this->value = HTML_Template_Flexy_Token::create('NameT',$this->yytext(),$this->yyline);
    
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
<IN_MD>{LITERAL}{WHITESPACE}	        { 
    $this->value = HTML_Template_Flexy_Token::create('Literal',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}

<IN_COM>{COM}{TAGC}			{   
    $this->value = HTML_Template_Flexy_Token::create('CloseTag',$this->yytext(),$this->yyline);
    $this->yybegin(YYINITIAL); 
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
<IN_MD>{TAGC}			{   
    $this->value = HTML_Template_Flexy_Token::create('CloseTag',$this->yytext(),$this->yyline);
    $this->yybegin(YYINITIAL); 
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}


//other constructs are errors. 
  
<IN_MD>{DSO}			{
    // <!doctype foo ^[  -- declaration subset */
    $this->value = HTML_Template_Flexy_Token::create('BeginDS',$this->yytext(),$this->yyline);
    $this->yybegin(IN_DS);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}

<IN_MD,IN_COM>.  {
    $this->error(0, "illegal character in markup declaration");
    return HTML_TEMPLATE_FLEXY_ERROR;
}

 


<IN_DS>{MSC}{TAGC}			{
    // ]]> -- marked section end */
     $this->value = HTML_Template_Flexy_Token::create('DSEnd',$this->yytext(),$this->yyline);
    $this->yybegin(YYINITIAL);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
  
<IN_DS>{DSC}			{ 
    // ] -- declaration subset close */
    $this->value = HTML_Template_Flexy_Token::create('DSEndSubset',$this->yytext(),$this->yyline);
    $this->yybegin(IN_COM); 
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}

<IN_DS>[^\]]+			{ 
    $this->value = HTML_Template_Flexy_Token::create('Declaration',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}

 // EXCERPT ACTIONS: STOP */

   
 
<YYINITIAL>"{if:"{FLEXY_VAR}"}" {
    $this->value = HTML_Template_Flexy_Token::create('If',substr($this->yytext(),4,-1),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}

<YYINITIAL>"{if:"{FLEXY_VAR}"(" {
    $this->value =  '';
    $this->flexyMethod = substr($this->yytext(),1,-1);
    $this->flexyArgs = array();
     
    $this->yybegin(IN_FLEXYMETHOD);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
 





<YYINITIAL>"{foreach:"{FLEXY_VAR}"}" {
    $this->value = HTML_Template_Flexy_Token::create('Foreach',array(substr($this->yytext(),9,-1)),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
<YYINITIAL>"{foreach:"{FLEXY_VAR}","{FLEXY_SIMPLEVAR}"}" {
    $this->value = HTML_Template_Flexy_Token::create('Foreach', explode(',',substr($this->yytext(),9,-1)),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
<YYINITIAL>"{foreach:"{FLEXY_VAR}","{FLEXY_SIMPLEVAR}","{FLEXY_SIMPLEVAR}"}" {
    $this->value = HTML_Template_Flexy_Token::create('Foreach',  explode(',',substr($this->yytext(),9,-1)),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
<YYINITIAL>"{end:}" {
    $this->value = HTML_Template_Flexy_Token::create('End', '',$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}

<YYINITIAL>"{else:}" {
    $this->value = HTML_Template_Flexy_Token::create('Else', '',$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}

<YYINITIAL>"{include:"{FLEXY_VAR}"}" {
    $this->value = HTML_Template_Flexy_Token::create('Include', substr($this->yytext(),9,-1),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}

// needs to deal with \# - excaped #'s

<YYINITIAL>"{include:#"{FLEXY_LITERAL}"#}" {
    $this->value = HTML_Template_Flexy_Token::create('Include', substr($this->yytext(),9,-1),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}



// variables
// need to work out how to do this with attribute values..

 
 
<IN_DOUBLEQUOTE,IN_SINGLEQUOTE> ({FLEXY_START}{FLEXY_VAR}":"{FLEXY_MODIFIER}{FLEXY_END})|({FLEXY_START}{FLEXY_VAR}{FLEXY_END}) {

    $n = $this->yytext();
    if ($n{0} != '{') {
        $n = substr($n,3);
    }
    if ($n{strlen($n)-1} != '}') {
        $n = substr($n,0,-3);
    }
    $this->attrVal[] = HTML_Template_Flexy_Token::create('Var'  , $n, $this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}


<YYINITIAL>("{"{FLEXY_VAR}":"{FLEXY_MODIFIER}"}")|("{"{FLEXY_VAR}"}") {
    $t =  $this->yytext();
    $t = substr($t,1,-1);

    $this->value = HTML_Template_Flexy_Token::create('Var'  , $t, $this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}







// methods

<YYINITIAL>"{"{FLEXY_VAR}"(" {
    $this->value =  '';
    $this->flexyMethod = substr($this->yytext(),1,-1);
    $this->flexyArgs = array();
    $this->yybegin(IN_FLEXYMETHOD);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}

<IN_FLEXYMETHOD>(")}"|"):"{FLEXY_MODIFIER}"}") {
    
    $t = $this->yytext();
    if ($t{1} == ':') {
        $this->flexyMethod .= substr($t,1,-1);
    }
        
    $this->value = HTML_Template_Flexy_Token::create('Method'  , array($this->flexyMethod,$this->flexyArgs), $this->yyline);
    $this->yybegin(YYINITIAL);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}



<IN_FLEXYMETHOD>{FLEXY_VAR}(","|")}"|"):"{FLEXY_MODIFIER}"}") {
    
    $t = $this->yytext();
    
    if ($t{stlen($t-1)} == ",") {
        // add argument
        $this->flexyArgs[] = substr($t,0,-1);
        return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
    }
    if ($c = strpos($t,':')) {
        $this->flexyMethod .= substr($t,$c,-1);
        $t = substr($t,0,$c-2);
    } else {
        $t = substr($t,0,-2);
    }
    
    $this->flexyArgs[] = $t;
    $this->value = HTML_Template_Flexy_Token::create('Method'  , array($this->flexyMethod,$this->flexyArgs), $this->yyline);
    $this->yybegin(YYINITIAL);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}

<IN_FLEXYMETHOD>"#"{FLEXY_LITERAL}("#,"|"#)}") {
    $t = $this->yytext();
    if ($t{stlen($t-1)} == ",") {
        // add argument
        $this->flexyArgs[] = substr($t,0,-1);
        return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
    }
    $this->flexyArgs[] = substr($t,0,-1);
    $this->value = HTML_Template_Flexy_Token::create('Method'  , array($this->flexyMethod,$this->flexyArgs), $this->yyline);
    $this->yybegin(YYINITIAL);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
<IN_FLEXYMETHOD> . {
    $this->error(0,"ERROR: unexpected something: (".$this->yytext() .") character: 0x" . dechex(ord($this->yytext())));
    return HTML_TEMPLATE_FLEXY_ERROR;
} 



// methods inside quotes..


<IN_DOUBLEQUOTE,IN_SINGLEQUOTE>{FLEXY_START}{FLEXY_VAR}"(" {
    $this->value =  '';
    $n = $this->yytext();
    if ($n{0} != "{") {
        $n = substr($n,3);
    }
    $this->flexyMethod = substr($n,0,-1);
    $this->flexyArgs = array();
    $this->flexyMethodState = $this->yy_lexical_state;
    $this->yybegin(IN_FLEXYMETHODQUOTED);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}


<IN_FLEXYMETHODQUOTED>{FLEXY_VAR}(","|")"{FLEXY_END}) {
    
    $t = $this->yytext();
    
    if ($t{stlen($t-1)} == ",") {
        // add argument
        $this->flexyArgs[] = substr($t,0,-1);
        return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
    }
    $this->flexyArgs[] = substr($t,0,-2);
    $this->attrVal[] = HTML_Template_Flexy_Token::create('Method'  , array($this->flexyMethod,$this->flexyArgs), $this->yyline);
    $this->yybegin($this->flexyMethodState);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}

<IN_FLEXYMETHODQUOTED>"#"{FLEXY_LITERAL}("#,"|"#)"{FLEXY_END}) {
    $t = $this->yytext();
    if ($t{ stlen($t-1) } == ",") {
        // add argument
        $this->flexyArgs[] = substr($t,0,-1);
        return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
    }
    $this->flexyArgs[] = substr($t,0,-1);
    $this->attrVal[] = HTML_Template_Flexy_Token::create('Method'  , array($this->flexyMethod,$this->flexyArgs), $this->yyline);
    $this->yybegin($this->flexyMethodState);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}


 


<YYINITIAL,IN_TAG,IN_ATTR,IN_ATTRVAL> . {
    $this->error(0,"ERROR: unexpected something: (".$this->yytext() .") character: 0x" . dechex(ord($this->yytext())));
    return HTML_TEMPLATE_FLEXY_ERROR;
} 