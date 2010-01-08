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
// | Authors:  Alan Knowles <alan@akbkhome.com>                           |
// +----------------------------------------------------------------------+
//
// $Id$
//
//  The Source Lex file. (Tokenizer.lex) and the Generated one (Tokenizer.php)
// You should always work with the .lex file and generate by
//
// #mono phpLex/phpLex.exe Tokenizer.lex
// The lexer is available at http://sourceforge.net/projects/php-sharp/
// 
// or the equivialant .NET runtime on windows...
//
//  Note need to change a few of these defines, and work out
// how to modifiy the lexer to handle the changes..
//
define('HTML_TEMPLATE_FLEXY_TOKEN_NONE',1);
define('HTML_TEMPLATE_FLEXY_TOKEN_OK',2);
define('HTML_TEMPLATE_FLEXY_TOKEN_ERROR',3);
define("YYINITIAL"     ,0);
define("IN_SINGLEQUOTE"     ,   1) ;
define("IN_TAG"     ,           2)  ;
define("IN_ATTR"     ,          3);
define("IN_ATTRVAL"     ,       4) ;
define("IN_NETDATA"     ,       5);
define("IN_ENDTAG"     ,        6);
define("IN_DOUBLEQUOTE"     ,   7);
define("IN_MD"     ,            8);
define("IN_COM"     ,           9);
define("IN_DS",                 10);
define("IN_FLEXYMETHOD"     ,   11);
define("IN_FLEXYMETHODQUOTED"  ,12);
define("IN_FLEXYMETHODQUOTED_END" ,13);
define("IN_SCRIPT",             14);
define("IN_CDATA"     ,         15);
define("IN_DSCOM",              16);
define("IN_PHP",                17);
define("IN_COMSTYLE"     ,      18);
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


class HTML_Template_Flexy_Tokenizer
{

    /**
    * options array : meanings:
    *    ignore_html - return all tags as  text tokens
    *
    *
    * @var      boolean  public
    * @access   public
    */
    var $options = array(
        'ignore_html' => false,
        'token_factory'  => array('HTML_Template_Flexy_Token','factory'),
    );
    /**
    * flag if inside a style tag. (so comments are ignored.. )
    *
    * @var boolean
    * @access private
    */
    var $inStyle = false;
    /**
    * the start position of a cdata block
    *
    * @var int
    * @access private
    */
    var $yyCdataBegin = 0;
     /**
    * the start position of a comment block
    *
    * @var int
    * @access private
    */
    var $yyCommentBegin = 0;
    /**
    * the name of the file being parsed (used by error messages)
    *
    * @var string
    * @access public
    */
    var $fileName;
    /**
    * the string containing an error if it occurs..
    *
    * @var string
    * @access public
    */
    var $error;
    /**
    * Flexible constructor
    *
    * @param   string       string to tokenize
    * @param   array        options array (see options above)       
    * 
    *
    * @return   HTML_Template_Flexy_Tokenizer
    * @access   public
    */
    function &construct($data,$options= array()) 
    {
        $t = new HTML_Template_Flexy_Tokenizer($data);
        foreach($options as $k=>$v) {
            if (is_object($v) || is_array($v)) {
                $t->options[$k] = &$v;
                continue;
            }
            $t->options[$k] = $v;
        }
        return $t;
    }
    /**
    * raise an error: = return an error token and set the error variable.
    *
    * 
    * @param   string           Error type
    * @param   string           Full Error message
    * @param   boolean          is it fatal..
    *
    * @return   int the error token.
    * @access   public
    */
    function raiseError($s,$n='',$isFatal=false) 
    {
        $this->error = "ERROR $n in File {$this->fileName} on Line {$this->yyline} Position:{$this->yy_buffer_end}: $s\n";
        return HTML_TEMPLATE_FLEXY_TOKEN_ERROR;
    }
    /**
    * return text
    *
    * Used mostly by the ignore HTML code. - really a macro :)
    *
    * @return   int   token ok.
    * @access   public
    */
    function returnSimple() 
    {
        $this->value = $this->createToken('TextSimple');
        return HTML_TEMPLATE_FLEXY_TOKEN_OK;
    }
    /**
    * Create a token based on the value of $this->options['token_call']
    *
    *
    * @return   Object   some kind of token..
    * @access   public
    */
    function createToken($token, $value = false, $line = false, $charPos = false) 
    {
        if ($value === false) {
            $value = $this->yytext();
        }
        if ($line === false) {
            $line = $this->yyline;
        }
        if ($charPos === false) {
            $charPos = $this->yy_buffer_start;
        }
        return call_user_func_array($this->options['token_factory'],array($token,$value,$line,$charPos));
    }


    var $yy_reader;
    var $yy_buffer_index;
    var $yy_buffer_read;
    var $yy_buffer_start;
    var $_fatal = false;
    var $yy_buffer_end;
    var $yy_buffer;
    var $yychar;
    var $yyline;
    var $yyEndOfLine;
    var $yy_at_bol;
    var $yy_lexical_state;

    function HTML_Template_Flexy_Tokenizer($data) 
    {
        $this->yy_buffer = $data;
        $this->yy_buffer_read = strlen($data);
        $this->yy_buffer_index = 0;
        $this->yy_buffer_start = 0;
        $this->yy_buffer_end = 0;
        $this->yychar = 0;
        $this->yyline = 0;
        $this->yy_at_bol = true;
        $this->yy_lexical_state = YYINITIAL;
    }

    var $yy_state_dtrans = array  ( 
        0,
        73,
        20,
        104,
        116,
        121,
        124,
        127,
        38,
        47,
        137,
        139,
        161,
        175,
        176,
        177,
        66,
        67,
        69
    );


    function yybegin ($state)
    {
        $this->yy_lexical_state = $state;
    }



    function yy_advance ()
    {
        if ($this->yy_buffer_index < $this->yy_buffer_read) {
            return ord($this->yy_buffer{$this->yy_buffer_index++});
        }
        return YY_EOF;
    }


    function yy_move_end ()
    {
        if ($this->yy_buffer_end > $this->yy_buffer_start && 
            '\n' == $this->yy_buffer{$this->yy_buffer_end-1})
        {
            $this->yy_buffer_end--;
        }
        if ($this->yy_buffer_end > $this->yy_buffer_start &&
            '\r' == $this->yy_buffer{$this->yy_buffer_end-1})
        {
            $this->yy_buffer_end--;
        }
    }


    var $yy_last_was_cr=false;


    function yy_mark_start ()
    {
        for ($i = $this->yy_buffer_start; $i < $this->yy_buffer_index; $i++) {
            if ($this->yy_buffer{$i} == "\n" && !$this->yy_last_was_cr) {
                $this->yyline++; $this->yyEndOfLine = $this->yychar;
            }
            if ($this->yy_buffer{$i} == "\r") {
                $this->yyline++; $this->yyEndOfLine = $this->yychar;
                $this->yy_last_was_cr=true;
            } else {
                $this->yy_last_was_cr=false;
            }
        }
        $this->yychar = $this->yychar + $this->yy_buffer_index - $this->yy_buffer_start;
        $this->yy_buffer_start = $this->yy_buffer_index;
    }


    function yy_mark_end ()
    {
        $this->yy_buffer_end = $this->yy_buffer_index;
    }


    function  yy_to_mark ()
    {
        $this->yy_buffer_index = $this->yy_buffer_end;
        $this->yy_at_bol = ($this->yy_buffer_end > $this->yy_buffer_start) &&
            ($this->yy_buffer{$this->yy_buffer_end-1} == '\r' ||
            $this->yy_buffer{$this->yy_buffer_end-1} == '\n');
    }


    function yytext()
    {
        return substr($this->yy_buffer,$this->yy_buffer_start,$this->yy_buffer_end - $this->yy_buffer_start);
    }


    function yylength ()
    {
        return $this->yy_buffer_end - $this->yy_buffer_start;
    }


    var $yy_error_string = array(
        "Error: Internal error.\n",
        "Error: Unmatched input - \""
        );


    function yy_error ($code,$fatal)
    {
        if (method_exists($this,'raiseError')) { 
	        $this->_fatal = $fatal;
            $msg = $this->yy_error_string[$code];
            if ($code == 1) {
                $msg .= $this->yy_buffer[$this->yy_buffer_start] . "\"";
            }
 		    return $this->raiseError($msg, $code, $fatal); 
 		}
        echo $this->yy_error_string[$code];
        if ($fatal) {
            exit;
        }
    }


    var  $yy_acpt = array (
        /* 0 */   YY_NOT_ACCEPT,
        /* 1 */   YY_NO_ANCHOR,
        /* 2 */   YY_NO_ANCHOR,
        /* 3 */   YY_NO_ANCHOR,
        /* 4 */   YY_NO_ANCHOR,
        /* 5 */   YY_NO_ANCHOR,
        /* 6 */   YY_NO_ANCHOR,
        /* 7 */   YY_NO_ANCHOR,
        /* 8 */   YY_NO_ANCHOR,
        /* 9 */   YY_NO_ANCHOR,
        /* 10 */   YY_NO_ANCHOR,
        /* 11 */   YY_NO_ANCHOR,
        /* 12 */   YY_NO_ANCHOR,
        /* 13 */   YY_NO_ANCHOR,
        /* 14 */   YY_NO_ANCHOR,
        /* 15 */   YY_NO_ANCHOR,
        /* 16 */   YY_NO_ANCHOR,
        /* 17 */   YY_NO_ANCHOR,
        /* 18 */   YY_NO_ANCHOR,
        /* 19 */   YY_NO_ANCHOR,
        /* 20 */   YY_NO_ANCHOR,
        /* 21 */   YY_NO_ANCHOR,
        /* 22 */   YY_NO_ANCHOR,
        /* 23 */   YY_NO_ANCHOR,
        /* 24 */   YY_NO_ANCHOR,
        /* 25 */   YY_NO_ANCHOR,
        /* 26 */   YY_NO_ANCHOR,
        /* 27 */   YY_NO_ANCHOR,
        /* 28 */   YY_NO_ANCHOR,
        /* 29 */   YY_NO_ANCHOR,
        /* 30 */   YY_NO_ANCHOR,
        /* 31 */   YY_NO_ANCHOR,
        /* 32 */   YY_NO_ANCHOR,
        /* 33 */   YY_NO_ANCHOR,
        /* 34 */   YY_NO_ANCHOR,
        /* 35 */   YY_NO_ANCHOR,
        /* 36 */   YY_NO_ANCHOR,
        /* 37 */   YY_NO_ANCHOR,
        /* 38 */   YY_NO_ANCHOR,
        /* 39 */   YY_NO_ANCHOR,
        /* 40 */   YY_NO_ANCHOR,
        /* 41 */   YY_NO_ANCHOR,
        /* 42 */   YY_NO_ANCHOR,
        /* 43 */   YY_NO_ANCHOR,
        /* 44 */   YY_NO_ANCHOR,
        /* 45 */   YY_NO_ANCHOR,
        /* 46 */   YY_NO_ANCHOR,
        /* 47 */   YY_NO_ANCHOR,
        /* 48 */   YY_NO_ANCHOR,
        /* 49 */   YY_NO_ANCHOR,
        /* 50 */   YY_NO_ANCHOR,
        /* 51 */   YY_NO_ANCHOR,
        /* 52 */   YY_NO_ANCHOR,
        /* 53 */   YY_NO_ANCHOR,
        /* 54 */   YY_NO_ANCHOR,
        /* 55 */   YY_NO_ANCHOR,
        /* 56 */   YY_NO_ANCHOR,
        /* 57 */   YY_NO_ANCHOR,
        /* 58 */   YY_NO_ANCHOR,
        /* 59 */   YY_NO_ANCHOR,
        /* 60 */   YY_NO_ANCHOR,
        /* 61 */   YY_NO_ANCHOR,
        /* 62 */   YY_NO_ANCHOR,
        /* 63 */   YY_NO_ANCHOR,
        /* 64 */   YY_NO_ANCHOR,
        /* 65 */   YY_NO_ANCHOR,
        /* 66 */   YY_NO_ANCHOR,
        /* 67 */   YY_NO_ANCHOR,
        /* 68 */   YY_NO_ANCHOR,
        /* 69 */   YY_NO_ANCHOR,
        /* 70 */   YY_NO_ANCHOR,
        /* 71 */   YY_NO_ANCHOR,
        /* 72 */   YY_NO_ANCHOR,
        /* 73 */   YY_NOT_ACCEPT,
        /* 74 */   YY_NO_ANCHOR,
        /* 75 */   YY_NO_ANCHOR,
        /* 76 */   YY_NO_ANCHOR,
        /* 77 */   YY_NO_ANCHOR,
        /* 78 */   YY_NO_ANCHOR,
        /* 79 */   YY_NO_ANCHOR,
        /* 80 */   YY_NO_ANCHOR,
        /* 81 */   YY_NO_ANCHOR,
        /* 82 */   YY_NO_ANCHOR,
        /* 83 */   YY_NO_ANCHOR,
        /* 84 */   YY_NO_ANCHOR,
        /* 85 */   YY_NO_ANCHOR,
        /* 86 */   YY_NO_ANCHOR,
        /* 87 */   YY_NO_ANCHOR,
        /* 88 */   YY_NO_ANCHOR,
        /* 89 */   YY_NO_ANCHOR,
        /* 90 */   YY_NO_ANCHOR,
        /* 91 */   YY_NO_ANCHOR,
        /* 92 */   YY_NO_ANCHOR,
        /* 93 */   YY_NO_ANCHOR,
        /* 94 */   YY_NO_ANCHOR,
        /* 95 */   YY_NO_ANCHOR,
        /* 96 */   YY_NO_ANCHOR,
        /* 97 */   YY_NO_ANCHOR,
        /* 98 */   YY_NO_ANCHOR,
        /* 99 */   YY_NO_ANCHOR,
        /* 100 */   YY_NO_ANCHOR,
        /* 101 */   YY_NOT_ACCEPT,
        /* 102 */   YY_NO_ANCHOR,
        /* 103 */   YY_NO_ANCHOR,
        /* 104 */   YY_NO_ANCHOR,
        /* 105 */   YY_NO_ANCHOR,
        /* 106 */   YY_NO_ANCHOR,
        /* 107 */   YY_NO_ANCHOR,
        /* 108 */   YY_NO_ANCHOR,
        /* 109 */   YY_NO_ANCHOR,
        /* 110 */   YY_NOT_ACCEPT,
        /* 111 */   YY_NO_ANCHOR,
        /* 112 */   YY_NO_ANCHOR,
        /* 113 */   YY_NO_ANCHOR,
        /* 114 */   YY_NO_ANCHOR,
        /* 115 */   YY_NO_ANCHOR,
        /* 116 */   YY_NOT_ACCEPT,
        /* 117 */   YY_NO_ANCHOR,
        /* 118 */   YY_NO_ANCHOR,
        /* 119 */   YY_NO_ANCHOR,
        /* 120 */   YY_NO_ANCHOR,
        /* 121 */   YY_NOT_ACCEPT,
        /* 122 */   YY_NO_ANCHOR,
        /* 123 */   YY_NO_ANCHOR,
        /* 124 */   YY_NOT_ACCEPT,
        /* 125 */   YY_NO_ANCHOR,
        /* 126 */   YY_NO_ANCHOR,
        /* 127 */   YY_NOT_ACCEPT,
        /* 128 */   YY_NO_ANCHOR,
        /* 129 */   YY_NO_ANCHOR,
        /* 130 */   YY_NOT_ACCEPT,
        /* 131 */   YY_NO_ANCHOR,
        /* 132 */   YY_NO_ANCHOR,
        /* 133 */   YY_NOT_ACCEPT,
        /* 134 */   YY_NO_ANCHOR,
        /* 135 */   YY_NOT_ACCEPT,
        /* 136 */   YY_NO_ANCHOR,
        /* 137 */   YY_NOT_ACCEPT,
        /* 138 */   YY_NOT_ACCEPT,
        /* 139 */   YY_NOT_ACCEPT,
        /* 140 */   YY_NOT_ACCEPT,
        /* 141 */   YY_NOT_ACCEPT,
        /* 142 */   YY_NOT_ACCEPT,
        /* 143 */   YY_NOT_ACCEPT,
        /* 144 */   YY_NOT_ACCEPT,
        /* 145 */   YY_NOT_ACCEPT,
        /* 146 */   YY_NOT_ACCEPT,
        /* 147 */   YY_NOT_ACCEPT,
        /* 148 */   YY_NOT_ACCEPT,
        /* 149 */   YY_NOT_ACCEPT,
        /* 150 */   YY_NOT_ACCEPT,
        /* 151 */   YY_NOT_ACCEPT,
        /* 152 */   YY_NOT_ACCEPT,
        /* 153 */   YY_NOT_ACCEPT,
        /* 154 */   YY_NOT_ACCEPT,
        /* 155 */   YY_NOT_ACCEPT,
        /* 156 */   YY_NOT_ACCEPT,
        /* 157 */   YY_NOT_ACCEPT,
        /* 158 */   YY_NOT_ACCEPT,
        /* 159 */   YY_NOT_ACCEPT,
        /* 160 */   YY_NOT_ACCEPT,
        /* 161 */   YY_NOT_ACCEPT,
        /* 162 */   YY_NOT_ACCEPT,
        /* 163 */   YY_NOT_ACCEPT,
        /* 164 */   YY_NOT_ACCEPT,
        /* 165 */   YY_NOT_ACCEPT,
        /* 166 */   YY_NOT_ACCEPT,
        /* 167 */   YY_NOT_ACCEPT,
        /* 168 */   YY_NOT_ACCEPT,
        /* 169 */   YY_NOT_ACCEPT,
        /* 170 */   YY_NOT_ACCEPT,
        /* 171 */   YY_NOT_ACCEPT,
        /* 172 */   YY_NOT_ACCEPT,
        /* 173 */   YY_NOT_ACCEPT,
        /* 174 */   YY_NOT_ACCEPT,
        /* 175 */   YY_NOT_ACCEPT,
        /* 176 */   YY_NOT_ACCEPT,
        /* 177 */   YY_NOT_ACCEPT,
        /* 178 */   YY_NOT_ACCEPT,
        /* 179 */   YY_NOT_ACCEPT,
        /* 180 */   YY_NOT_ACCEPT,
        /* 181 */   YY_NOT_ACCEPT,
        /* 182 */   YY_NOT_ACCEPT,
        /* 183 */   YY_NOT_ACCEPT,
        /* 184 */   YY_NOT_ACCEPT,
        /* 185 */   YY_NOT_ACCEPT,
        /* 186 */   YY_NOT_ACCEPT,
        /* 187 */   YY_NOT_ACCEPT,
        /* 188 */   YY_NOT_ACCEPT,
        /* 189 */   YY_NOT_ACCEPT,
        /* 190 */   YY_NOT_ACCEPT,
        /* 191 */   YY_NOT_ACCEPT,
        /* 192 */   YY_NOT_ACCEPT,
        /* 193 */   YY_NOT_ACCEPT,
        /* 194 */   YY_NOT_ACCEPT,
        /* 195 */   YY_NOT_ACCEPT,
        /* 196 */   YY_NOT_ACCEPT,
        /* 197 */   YY_NOT_ACCEPT,
        /* 198 */   YY_NOT_ACCEPT,
        /* 199 */   YY_NOT_ACCEPT,
        /* 200 */   YY_NOT_ACCEPT,
        /* 201 */   YY_NOT_ACCEPT,
        /* 202 */   YY_NOT_ACCEPT,
        /* 203 */   YY_NOT_ACCEPT,
        /* 204 */   YY_NOT_ACCEPT,
        /* 205 */   YY_NOT_ACCEPT,
        /* 206 */   YY_NOT_ACCEPT,
        /* 207 */   YY_NOT_ACCEPT,
        /* 208 */   YY_NOT_ACCEPT,
        /* 209 */   YY_NOT_ACCEPT,
        /* 210 */   YY_NO_ANCHOR,
        /* 211 */   YY_NO_ANCHOR,
        /* 212 */   YY_NO_ANCHOR,
        /* 213 */   YY_NO_ANCHOR,
        /* 214 */   YY_NO_ANCHOR,
        /* 215 */   YY_NO_ANCHOR,
        /* 216 */   YY_NO_ANCHOR,
        /* 217 */   YY_NO_ANCHOR,
        /* 218 */   YY_NO_ANCHOR,
        /* 219 */   YY_NO_ANCHOR,
        /* 220 */   YY_NOT_ACCEPT,
        /* 221 */   YY_NOT_ACCEPT,
        /* 222 */   YY_NOT_ACCEPT,
        /* 223 */   YY_NOT_ACCEPT,
        /* 224 */   YY_NOT_ACCEPT,
        /* 225 */   YY_NOT_ACCEPT,
        /* 226 */   YY_NOT_ACCEPT,
        /* 227 */   YY_NOT_ACCEPT,
        /* 228 */   YY_NOT_ACCEPT,
        /* 229 */   YY_NOT_ACCEPT,
        /* 230 */   YY_NOT_ACCEPT,
        /* 231 */   YY_NO_ANCHOR,
        /* 232 */   YY_NO_ANCHOR,
        /* 233 */   YY_NO_ANCHOR,
        /* 234 */   YY_NO_ANCHOR,
        /* 235 */   YY_NO_ANCHOR,
        /* 236 */   YY_NO_ANCHOR,
        /* 237 */   YY_NO_ANCHOR,
        /* 238 */   YY_NOT_ACCEPT,
        /* 239 */   YY_NOT_ACCEPT,
        /* 240 */   YY_NOT_ACCEPT,
        /* 241 */   YY_NO_ANCHOR,
        /* 242 */   YY_NO_ANCHOR,
        /* 243 */   YY_NO_ANCHOR,
        /* 244 */   YY_NO_ANCHOR,
        /* 245 */   YY_NO_ANCHOR,
        /* 246 */   YY_NOT_ACCEPT,
        /* 247 */   YY_NOT_ACCEPT,
        /* 248 */   YY_NOT_ACCEPT,
        /* 249 */   YY_NO_ANCHOR,
        /* 250 */   YY_NO_ANCHOR,
        /* 251 */   YY_NOT_ACCEPT,
        /* 252 */   YY_NOT_ACCEPT,
        /* 253 */   YY_NO_ANCHOR,
        /* 254 */   YY_NO_ANCHOR,
        /* 255 */   YY_NOT_ACCEPT,
        /* 256 */   YY_NOT_ACCEPT,
        /* 257 */   YY_NO_ANCHOR,
        /* 258 */   YY_NOT_ACCEPT,
        /* 259 */   YY_NOT_ACCEPT,
        /* 260 */   YY_NO_ANCHOR,
        /* 261 */   YY_NOT_ACCEPT,
        /* 262 */   YY_NOT_ACCEPT,
        /* 263 */   YY_NO_ANCHOR,
        /* 264 */   YY_NO_ANCHOR,
        /* 265 */   YY_NO_ANCHOR,
        /* 266 */   YY_NO_ANCHOR,
        /* 267 */   YY_NO_ANCHOR,
        /* 268 */   YY_NO_ANCHOR,
        /* 269 */   YY_NO_ANCHOR,
        /* 270 */   YY_NO_ANCHOR,
        /* 271 */   YY_NO_ANCHOR,
        /* 272 */   YY_NO_ANCHOR,
        /* 273 */   YY_NO_ANCHOR,
        /* 274 */   YY_NO_ANCHOR,
        /* 275 */   YY_NO_ANCHOR,
        /* 276 */   YY_NOT_ACCEPT,
        /* 277 */   YY_NOT_ACCEPT,
        /* 278 */   YY_NO_ANCHOR,
        /* 279 */   YY_NO_ANCHOR,
        /* 280 */   YY_NOT_ACCEPT,
        /* 281 */   YY_NO_ANCHOR,
        /* 282 */   YY_NO_ANCHOR,
        /* 283 */   YY_NOT_ACCEPT,
        /* 284 */   YY_NO_ANCHOR,
        /* 285 */   YY_NOT_ACCEPT,
        /* 286 */   YY_NO_ANCHOR,
        /* 287 */   YY_NOT_ACCEPT,
        /* 288 */   YY_NO_ANCHOR,
        /* 289 */   YY_NOT_ACCEPT,
        /* 290 */   YY_NO_ANCHOR,
        /* 291 */   YY_NO_ANCHOR,
        /* 292 */   YY_NO_ANCHOR,
        /* 293 */   YY_NO_ANCHOR,
        /* 294 */   YY_NO_ANCHOR,
        /* 295 */   YY_NO_ANCHOR,
        /* 296 */   YY_NO_ANCHOR,
        /* 297 */   YY_NO_ANCHOR,
        /* 298 */   YY_NO_ANCHOR,
        /* 299 */   YY_NOT_ACCEPT,
        /* 300 */   YY_NO_ANCHOR,
        /* 301 */   YY_NO_ANCHOR,
        /* 302 */   YY_NO_ANCHOR,
        /* 303 */   YY_NO_ANCHOR,
        /* 304 */   YY_NO_ANCHOR,
        /* 305 */   YY_NO_ANCHOR,
        /* 306 */   YY_NO_ANCHOR,
        /* 307 */   YY_NO_ANCHOR,
        /* 308 */   YY_NO_ANCHOR,
        /* 309 */   YY_NO_ANCHOR,
        /* 310 */   YY_NO_ANCHOR,
        /* 311 */   YY_NO_ANCHOR,
        /* 312 */   YY_NO_ANCHOR,
        /* 313 */   YY_NO_ANCHOR,
        /* 314 */   YY_NO_ANCHOR,
        /* 315 */   YY_NO_ANCHOR,
        /* 316 */   YY_NO_ANCHOR,
        /* 317 */   YY_NO_ANCHOR,
        /* 318 */   YY_NO_ANCHOR,
        /* 319 */   YY_NO_ANCHOR
        );


    var  $yy_cmap = array(
        32, 32, 32, 32, 32, 32, 32, 32,
        32, 13, 5, 32, 32, 12, 32, 32,
        32, 32, 32, 32, 32, 32, 32, 32,
        32, 32, 32, 32, 32, 32, 32, 32,
        11, 15, 31, 2, 33, 26, 1, 30,
        33, 22, 33, 33, 33, 16, 7, 9,
        3, 3, 3, 3, 3, 44, 3, 53,
        3, 3, 10, 4, 8, 29, 14, 25,
        32, 20, 45, 18, 19, 6, 6, 6,
        6, 39, 6, 6, 6, 6, 6, 6,
        41, 6, 38, 34, 21, 6, 6, 6,
        6, 6, 6, 17, 27, 23, 32, 28,
        32, 51, 46, 36, 47, 50, 48, 3,
        52, 40, 3, 3, 3, 3, 3, 49,
        42, 3, 37, 35, 43, 3, 3, 3,
        3, 3, 3, 24, 32, 32, 32, 32,
        6, 6, 6, 6, 6, 6, 6, 6,
        6, 6, 6, 6, 6, 6, 6, 6,
        6, 6, 6, 6, 6, 6, 6, 6,
        6, 6, 6, 6, 6, 6, 6, 6,
        6, 6, 6, 6, 6, 6, 6, 6,
        6, 6, 6, 6, 6, 6, 6, 6,
        6, 6, 6, 6, 6, 6, 6, 6,
        6, 6, 6, 6, 6, 6, 6, 6,
        6, 6, 6, 6, 6, 6, 6, 6,
        6, 6, 6, 6, 6, 6, 6, 6,
        6, 6, 6, 6, 6, 6, 6, 6,
        6, 6, 6, 6, 6, 6, 6, 6,
        6, 6, 6, 6, 6, 6, 6, 6,
        6, 6, 6, 6, 6, 6, 6, 6,
        6, 6, 6, 6, 6, 6, 6, 6,
        6, 6, 6, 6, 6, 6, 6, 6,
        32, 0, 0 
         );


    var $yy_rmap = array(
        0, 1, 2, 3, 4, 5, 6, 7,
        8, 5, 9, 5, 10, 11, 5, 5,
        5, 5, 12, 13, 14, 1, 1, 15,
        16, 17, 1, 1, 18, 19, 18, 18,
        18, 18, 20, 1, 1, 21, 22, 1,
        23, 1, 1, 24, 25, 26, 27, 28,
        29, 30, 1, 31, 1, 1, 32, 33,
        1, 1, 34, 34, 35, 1, 1, 1,
        1, 1, 36, 37, 38, 39, 40, 40,
        40, 41, 5, 5, 42, 43, 44, 45,
        46, 1, 47, 18, 48, 49, 50, 51,
        52, 53, 54, 55, 56, 57, 58, 59,
        60, 61, 62, 38, 40, 13, 63, 64,
        65, 66, 67, 68, 69, 70, 16, 71,
        72, 56, 73, 74, 75, 76, 58, 77,
        78, 79, 80, 81, 82, 83, 84, 85,
        86, 33, 72, 87, 88, 67, 89, 56,
        90, 91, 92, 93, 58, 81, 94, 95,
        96, 97, 98, 99, 100, 101, 102, 103,
        104, 105, 106, 107, 108, 109, 110, 111,
        112, 113, 33, 114, 115, 116, 117, 118,
        119, 120, 121, 122, 123, 124, 125, 126,
        127, 128, 129, 130, 131, 132, 133, 134,
        135, 136, 137, 138, 139, 140, 141, 142,
        143, 144, 145, 146, 147, 148, 149, 150,
        151, 152, 153, 154, 155, 40, 156, 157,
        158, 159, 160, 161, 77, 162, 163, 164,
        165, 166, 167, 168, 103, 169, 170, 171,
        133, 172, 145, 173, 174, 175, 176, 177,
        176, 87, 178, 179, 180, 181, 115, 182,
        183, 184, 185, 186, 187, 188, 121, 189,
        190, 167, 191, 132, 192, 193, 194, 137,
        195, 196, 144, 197, 198, 149, 199, 200,
        180, 201, 202, 188, 203, 204, 205, 206,
        207, 177, 208, 209, 210, 211, 212, 213,
        214, 193, 215, 216, 217, 218, 219, 220,
        221, 222, 201, 223, 203, 224, 225, 226,
        227, 228, 229, 230, 231, 232, 233, 234,
        235, 236, 237, 238, 239, 240, 241, 242,
        243, 244, 245, 246, 247, 248, 249, 250
        );


    var $yy_nxt = array(
        array( 1, 2, 74, 74, 74, 74, 74, 74,
            102, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 214,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, 74, 111, 74, 74, 74, 3, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 3, 3, 3, 3, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 3, 74, 74, 74, 3, 3,
            74, 3, 74, 74, 74, 3, 74, 74,
            74, 74, 74, 74, 74, 74 ),
        array( -1, 74, 74, 74, 75, 75, 3, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 3, 3, 3, 3, 74, 74,
            74, 74, 74, 74, 3, 74, 74, 74,
            74, 74, 3, 74, 74, 74, 3, 3,
            74, 3, 74, 74, 74, 3, 74, 74,
            74, 74, 74, 74, 74, 74 ),
        array( -1, 74, 74, 74, 74, 76, 4, 74,
            74, 74, 4, 76, 76, 76, 74, 74,
            74, 74, 4, 4, 4, 4, 74, 74,
            74, 74, 74, 74, 4, 74, 74, 74,
            74, 74, 4, 74, 74, 74, 4, 4,
            74, 4, 74, 74, 74, 4, 74, 74,
            74, 74, 74, 74, 74, 74 ),
        array( -1, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74 ),
        array( -1, 74, 74, 74, 74, 77, 13, 74,
            74, 74, 74, 77, 77, 77, 74, 74,
            74, 74, 13, 13, 13, 13, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 13, 74, 74, 74, 13, 13,
            74, 13, 74, 74, 74, 13, 74, 74,
            74, 74, 74, 74, 74, 74 ),
        array( -1, 74, 74, 74, 74, 74, 7, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 7, 7, 7, 7, 74, 74,
            74, 74, 74, 74, 7, 74, 74, 74,
            74, 74, 7, 74, 74, 74, 7, 7,
            74, 7, 74, 74, 74, 7, 74, 74,
            74, 74, 74, 74, 74, 74 ),
        array( -1, 74, 74, 74, 74, 78, 8, 74,
            74, 128, 8, 78, 78, 78, 74, 74,
            74, 74, 8, 8, 8, 8, 74, 74,
            74, 74, 74, 74, 8, 74, 74, 74,
            74, 74, 8, 74, 74, 74, 8, 8,
            74, 8, 74, 74, 74, 8, 74, 74,
            74, 74, 74, 74, 74, 74 ),
        array( -1, 74, 74, 74, 74, 79, 10, 74,
            74, 74, 74, 79, 79, 79, 74, 74,
            74, 74, 10, 10, 10, 10, 74, 74,
            74, 74, 74, 74, 10, 74, 74, 74,
            74, 74, 10, 74, 74, 74, 10, 10,
            74, 10, 74, 74, 74, 10, 74, 74,
            74, 74, 74, 74, 74, 74 ),
        array( -1, 74, 74, 74, 74, 310, 74, 74,
            74, 74, 74, 310, 310, 310, 74, 74,
            74, 74, 309, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74 ),
        array( -1, 74, 74, 74, 74, 80, 13, 74,
            74, 74, 74, 80, 80, 80, 74, 74,
            74, 74, 13, 13, 13, 13, 74, 74,
            74, 74, 74, 74, 13, 74, 74, 74,
            74, 74, 13, 74, 74, 74, 13, 13,
            74, 13, 74, 74, 74, 13, 74, 74,
            74, 74, 74, 74, 74, 74 ),
        array( -1, 18, 18, 18, 18, 18, 18, 18,
            18, 18, 18, 18, 18, 18, 18, 18,
            18, 18, 18, 18, 18, 18, 18, 18,
            18, 18, 18, 101, 18, 18, 18, 18,
            18, 18, 18, 18, 18, 18, 18, 18,
            18, 18, 18, 18, 18, 18, 18, 18,
            18, 18, 18, 18, 18, 18 ),
        array( -1, 18, 18, 18, 18, 18, 18, 18,
            18, 18, 18, 18, 18, 18, 18, 18,
            18, 18, 18, 18, 18, 18, 18, 18,
            18, 18, 18, 18, 18, 18, 18, 18,
            18, 18, 18, 18, 18, 18, 18, 18,
            18, 18, 18, 18, 18, 18, 18, 18,
            18, 18, 18, 18, 18, 18 ),
        array( 1, 81, 81, 81, 81, 82, 81, 81,
            21, 81, 81, 82, 82, 82, 22, 81,
            81, 81, 81, 81, 81, 81, 81, 81,
            81, 81, 81, 81, 81, 81, 81, 81,
            81, 81, 81, 81, 81, 81, 81, 81,
            81, 81, 81, 81, 81, 81, 81, 81,
            81, 81, 81, 81, 81, 81 ),
        array( -1, -1, -1, -1, -1, 84, 23, -1,
            -1, -1, 23, 84, 84, 84, -1, -1,
            -1, -1, 23, 23, 23, 23, -1, -1,
            -1, -1, -1, -1, 23, 25, -1, -1,
            -1, -1, 23, -1, -1, -1, 23, 23,
            -1, 23, -1, -1, -1, 23, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 110, -1, -1,
            -1, -1, -1, 110, 110, 110, 26, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 25, -1, -1,
            -1, -1, -1, 25, 25, 25, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, 28, 28, 28, 28, 85, 28, 28,
            28, 28, 28, 28, 85, 85, 28, 28,
            28, 28, 28, 28, 28, 28, 28, 28,
            28, 28, 28, 28, 28, 28, 28, 28,
            28, 28, 28, 28, 28, 28, 28, 28,
            28, 28, 28, 28, 28, 28, 28, 28,
            28, 28, 28, 28, 28, 28 ),
        array( -1, 28, 28, 28, 28, 86, 29, 28,
            28, 28, 28, 105, 86, 86, 28, 28,
            28, 28, 29, 29, 29, 29, 28, 28,
            28, 28, 28, 28, 29, 28, 28, 28,
            28, 28, 29, 28, 28, 28, 29, 29,
            28, 29, 28, 28, 28, 29, 28, 28,
            28, 28, 28, 28, 28, 28 ),
        array( -1, -1, -1, -1, -1, 34, -1, -1,
            -1, -1, -1, 34, 34, 34, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, 37, 37, 37, 37, 37, 37, 37,
            37, 37, 37, 37, 37, 37, 37, 37,
            37, 37, 37, 37, 37, 37, 37, 37,
            37, 37, 37, 130, 37, 37, 37, 37,
            37, 37, 37, 37, 37, 37, 37, 37,
            37, 37, 37, 37, 37, 37, 37, 37,
            37, 37, 37, 37, 37, 37 ),
        array( 1, 39, 39, 39, 39, 87, 40, 39,
            39, 39, 39, 87, 87, 87, 41, 39,
            39, 42, 40, 40, 40, 40, 39, 39,
            39, 39, 88, 39, 43, 39, 106, 113,
            39, 39, 40, 39, 39, 39, 40, 40,
            39, 40, 39, 39, 39, 40, 39, 39,
            39, 39, 39, 39, 39, 39 ),
        array( -1, -1, -1, -1, -1, 89, 40, -1,
            -1, -1, -1, 89, 89, 89, -1, -1,
            -1, -1, 40, 40, 40, 40, -1, -1,
            -1, -1, -1, -1, 40, -1, -1, -1,
            -1, -1, 40, -1, -1, -1, 40, 40,
            -1, 40, -1, -1, -1, 40, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 90, 43, -1,
            -1, -1, -1, 90, 90, 90, -1, -1,
            -1, -1, 43, 43, 43, 43, -1, -1,
            -1, -1, -1, -1, 43, -1, -1, -1,
            -1, -1, 43, -1, -1, -1, 43, 43,
            -1, 43, -1, -1, -1, 43, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 44, -1, -1,
            -1, -1, -1, 44, 44, 44, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, 91, 91, 45, -1,
            -1, -1, -1, 91, 91, 91, -1, -1,
            -1, -1, 45, 45, 45, 45, -1, -1,
            -1, -1, -1, -1, 45, -1, -1, -1,
            -1, -1, 45, -1, -1, -1, 45, 45,
            -1, 45, -1, -1, -1, 45, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, 133, 133, 133, 133, 46, 133, 133,
            133, 133, 133, 46, 46, 46, 133, 133,
            133, 133, 133, 133, 133, 133, 133, 133,
            133, 133, 133, 133, 133, 133, 46, 133,
            133, 133, 133, 133, 133, 133, 133, 133,
            133, 133, 133, 133, 133, 133, 133, 133,
            133, 133, 133, 133, 133, 133 ),
        array( 1, 93, 93, 93, 93, 93, 93, 93,
            93, 93, 93, 93, 93, 93, 93, 93,
            93, 93, 93, 93, 93, 93, 93, 93,
            93, 93, 93, 93, 93, 93, 93, 93,
            93, 93, 93, 93, 93, 93, 93, 93,
            93, 93, 93, 93, 93, 93, 93, 93,
            93, 93, 93, 93, 93, 93 ),
        array( -1, 48, 48, 48, 48, 48, 48, 48,
            48, 48, 48, 48, 48, 48, 48, 48,
            48, 48, 48, 48, 48, 48, 48, -1,
            48, 48, 48, 48, 48, 48, 48, 48,
            48, 48, 48, 48, 48, 48, 48, 48,
            48, 48, 48, 48, 48, 48, 48, 48,
            48, 48, 48, 48, 48, 48 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 138,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, 140, 51, 140, 140, 140, 140, 140,
            140, 140, 140, 140, 140, 140, 140, 140,
            140, 140, 140, 140, 140, 140, 140, 140,
            140, 140, 140, 140, 140, 140, 140, 140,
            140, 94, 140, 140, 140, 140, 140, 140,
            140, 140, 140, 140, 140, 140, 140, 140,
            140, 140, 140, 140, 140, 140 ),
        array( -1, -1, -1, -1, -1, -1, 54, 163,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 164, 54, 54, 54, 54, -1, -1,
            -1, -1, 276, -1, 54, -1, -1, -1,
            -1, -1, 54, -1, -1, -1, 54, 54,
            -1, 54, -1, -1, -1, 54, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, 162, 55, 162, 162, 162, 162, 162,
            162, 162, 162, 162, 162, 162, 162, 162,
            162, 162, 162, 162, 162, 162, 162, 162,
            162, 162, 162, 162, 162, 162, 162, 162,
            162, 162, 162, 162, 162, 162, 162, 162,
            162, 162, 162, 162, 162, 162, 162, 162,
            162, 162, 162, 162, 162, 162 ),
        array( -1, 58, 58, 58, 58, 58, 58, 58,
            58, 58, 58, 58, 58, 58, 58, 58,
            58, 58, 58, 58, 58, 58, 58, 58,
            58, 58, 58, 58, 58, 58, 58, 58,
            58, 58, 58, 58, 58, 58, 58, 58,
            58, 58, 58, 58, 58, 58, 58, 58,
            58, 58, 58, 58, 58, 58 ),
        array( -1, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, -1,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60 ),
        array( 1, 98, 98, 98, 98, 98, 98, 98,
            98, 98, 98, 98, 98, 98, 98, 98,
            98, 98, 98, 98, 98, 98, 98, 98,
            98, 98, 98, 98, 98, 98, 98, 98,
            98, 98, 98, 98, 98, 98, 98, 98,
            98, 98, 98, 98, 98, 98, 98, 98,
            98, 98, 98, 98, 98, 98 ),
        array( 1, 99, 99, 99, 99, 99, 99, 99,
            99, 99, 99, 99, 99, 99, 99, 99,
            99, 99, 99, 99, 99, 99, 99, 99,
            99, 108, 99, 99, 99, 99, 99, 99,
            99, 99, 99, 99, 99, 99, 99, 99,
            99, 99, 99, 99, 99, 99, 99, 99,
            99, 99, 99, 99, 99, 99 ),
        array( -1, 99, 99, 99, 99, 99, 99, 99,
            99, 99, 99, 99, 99, 99, 99, 99,
            99, 99, 99, 99, 99, 99, 99, 99,
            99, 99, 99, 99, 99, 99, 99, 99,
            99, 99, 99, 99, 99, 99, 99, 99,
            99, 99, 99, 99, 99, 99, 99, 99,
            99, 99, 99, 99, 99, 99 ),
        array( 1, 70, 70, 70, 70, 100, 70, 70,
            70, 70, 70, 100, 100, 100, 70, 70,
            213, 70, 70, 70, 70, 70, 70, 70,
            232, 70, 70, 70, 70, 70, 70, 70,
            70, 70, 70, 70, 70, 70, 70, 70,
            70, 70, 70, 70, 70, 70, 70, 70,
            70, 70, 70, 70, 70, 70 ),
        array( -1, 109, 109, 109, 109, 109, 109, 109,
            109, 109, 109, 109, 109, 109, 109, 109,
            109, 109, 109, 109, 109, 109, 109, 109,
            109, 109, 109, 109, 109, 109, 109, 109,
            109, 109, 109, 109, 109, 109, 109, 109,
            109, 109, 109, 109, 109, 109, 109, 109,
            109, 109, 109, 109, 109, 109 ),
        array( 1, 18, 18, 18, 18, 18, 18, 18,
            18, 18, 18, 18, 18, 18, 18, 18,
            18, 18, 18, 18, 18, 18, 18, 18,
            18, 18, 18, 19, 18, 18, 18, 18,
            18, 18, 18, 18, 18, 18, 18, 18,
            18, 18, 18, 18, 18, 18, 18, 18,
            18, 18, 18, 18, 18, 18 ),
        array( -1, 74, 74, 74, 74, 76, 74, 74,
            74, 74, 74, 76, 76, 76, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74 ),
        array( -1, 74, 74, 74, 74, 77, 74, 74,
            74, 74, 74, 77, 77, 77, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74 ),
        array( -1, 74, 74, 74, 74, 78, 74, 74,
            74, 128, 74, 78, 78, 78, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74 ),
        array( -1, 74, 74, 74, 74, 79, 74, 74,
            74, 74, 74, 79, 79, 79, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74 ),
        array( -1, 74, 74, 74, 74, 80, 74, 74,
            74, 74, 74, 80, 80, 80, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74 ),
        array( -1, -1, -1, -1, -1, 82, -1, -1,
            -1, -1, -1, 82, 82, 82, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 84, -1, -1,
            -1, -1, -1, 84, 84, 84, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 25, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 85, -1, -1,
            -1, -1, -1, 85, 85, 85, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 86, -1, -1,
            -1, -1, -1, 86, 86, 86, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 87, -1, -1,
            -1, -1, -1, 87, 87, 87, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 44, 45, -1,
            -1, -1, -1, 44, 44, 44, -1, -1,
            -1, -1, 45, 45, 45, 45, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 45, -1, -1, -1, 45, 45,
            -1, 45, -1, -1, -1, 45, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 89, -1, -1,
            -1, -1, -1, 89, 89, 89, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 90, -1, -1,
            -1, -1, -1, 90, 90, 90, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 91, -1, -1,
            -1, -1, -1, 91, 91, 91, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, 135, 135, 135, 135, 135, 135, 135,
            135, 135, 135, 135, 135, 135, 135, 135,
            135, 135, 135, 135, 135, 135, 135, 135,
            135, 135, 135, 135, 135, 135, 135, 92,
            135, 135, 135, 135, 135, 135, 135, 135,
            135, 135, 135, 135, 135, 135, 135, 135,
            135, 135, 135, 135, 135, 135 ),
        array( -1, 93, 93, 93, 93, 93, 93, 93,
            93, 93, 93, 93, 93, 93, 93, 93,
            93, 93, 93, 93, 93, 93, 93, 93,
            93, 93, 93, 93, 93, 93, 93, 93,
            93, 93, 93, 93, 93, 93, 93, 93,
            93, 93, 93, 93, 93, 93, 93, 93,
            93, 93, 93, 93, 93, 93 ),
        array( -1, 140, 51, 140, 140, 140, 140, 140,
            140, 140, 140, 140, 140, 140, 140, 140,
            140, 140, 140, 140, 140, 140, 140, 140,
            140, 140, 140, 140, 140, 140, 140, 140,
            140, 140, 140, 140, 140, 140, 140, 140,
            140, 140, 140, 140, 140, 140, 140, 140,
            140, 140, 140, 140, 140, 140 ),
        array( -1, -1, -1, -1, -1, -1, -1, 163,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 164, -1, -1, -1, -1, -1, -1,
            -1, -1, 276, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, 58, 58, 58, 58, 58, 58, 58,
            58, 58, 58, 58, 58, 58, 59, 58,
            58, 58, 58, 58, 58, 58, 58, 58,
            58, 58, 58, 58, 58, 58, 58, 58,
            58, 58, 58, 58, 58, 58, 58, 58,
            58, 58, 58, 58, 58, 58, 58, 58,
            58, 58, 58, 58, 58, 58 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 107,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, 98, 98, 98, 98, 98, 98, 98,
            98, 98, 98, 98, 98, 98, 98, 98,
            98, 98, 98, 98, 98, 98, 98, 98,
            98, 98, 98, 98, 98, 98, 98, 98,
            98, 98, 98, 98, 98, 98, 98, 98,
            98, 98, 98, 98, 98, 98, 98, 98,
            98, 98, 98, 98, 98, 98 ),
        array( -1, 74, 74, 74, 74, 74, 4, 74,
            74, 117, 74, 74, 74, 74, 5, 122,
            74, 74, 4, 4, 4, 4, 74, 74,
            74, 6, 74, 74, 74, 74, 74, 74,
            74, 74, 4, 74, 74, 74, 4, 4,
            74, 4, 74, 74, 74, 4, 74, 74,
            74, 74, 74, 74, 74, 74 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 27, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( 1, 81, 81, 81, 81, 82, 23, 81,
            21, 24, 81, 82, 82, 82, 22, 81,
            81, 81, 23, 23, 23, 23, 81, 81,
            81, 103, 81, 81, 81, 81, 81, 81,
            81, 81, 23, 81, 81, 81, 23, 23,
            81, 23, 81, 81, 81, 23, 81, 81,
            81, 81, 81, 81, 81, 81 ),
        array( -1, 28, 28, 28, 28, 86, 28, 28,
            28, 28, 28, 105, 86, 86, 28, 28,
            28, 28, 28, 28, 28, 28, 28, 28,
            28, 28, 28, 28, 28, 28, 28, 28,
            28, 28, 28, 28, 28, 28, 28, 28,
            28, 28, 28, 28, 28, 28, 28, 28,
            28, 28, 28, 28, 28, 28 ),
        array( -1, 133, 133, 133, 133, 133, 133, 133,
            133, 133, 133, 133, 133, 133, 133, 133,
            133, 133, 133, 133, 133, 133, 133, 133,
            133, 133, 133, 133, 133, 133, 46, 133,
            133, 133, 133, 133, 133, 133, 133, 133,
            133, 133, 133, 133, 133, 133, 133, 133,
            133, 133, 133, 133, 133, 133 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 61, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, 99, 99, 99, 99, 99, 99, 99,
            99, 99, 99, 99, 99, 99, 68, 99,
            99, 99, 99, 99, 99, 99, 99, 99,
            99, 99, 99, 99, 99, 99, 99, 99,
            99, 99, 99, 99, 99, 99, 99, 99,
            99, 99, 99, 99, 99, 99, 99, 99,
            99, 99, 99, 99, 99, 99 ),
        array( -1, 205, 205, 205, 205, 100, 205, 205,
            205, 205, 205, 100, 100, 100, 205, 205,
            205, 205, 205, 205, 205, 205, 205, 205,
            205, 205, 205, 205, 205, 205, 205, 205,
            205, 205, 205, 205, 205, 205, 205, 205,
            205, 205, 205, 205, 205, 205, 205, 205,
            205, 205, 205, 205, 205, 205 ),
        array( -1, 74, 74, 74, 74, 74, 7, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 7, 7, 7, 7, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 7, 74, 74, 74, 7, 7,
            74, 7, 74, 74, 74, 7, 74, 74,
            74, 74, 74, 74, 74, 74 ),
        array( -1, 37, 37, 37, 37, 37, 37, 37,
            37, 37, 37, 37, 37, 37, 37, 37,
            37, 37, 37, 37, 37, 37, 37, 37,
            37, 37, 37, -1, 37, 37, 37, 37,
            37, 37, 37, 37, 37, 37, 37, 37,
            37, 37, 37, 37, 37, 37, 37, 37,
            37, 37, 37, 37, 37, 37 ),
        array( -1, 60, 60, 60, 60, 60, 114, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            114, 60, 114, 114, 114, 114, 60, 178,
            60, 60, 301, 60, 114, 60, 60, 60,
            60, 60, 114, 60, 60, 60, 114, 114,
            60, 114, 60, 60, 60, 114, 60, 60,
            60, 60, 60, 60, 60, 60 ),
        array( -1, 71, 71, 71, 71, 100, 71, 71,
            71, 71, 71, 100, 100, 100, 71, 71,
            71, 71, 71, 71, 71, 71, 71, 71,
            71, 71, 71, 71, 71, 71, 71, 71,
            71, 71, 71, 71, 71, 71, 71, 71,
            71, 71, 71, 71, 71, 71, 71, 71,
            71, 71, 71, 71, 71, 71 ),
        array( 1, 28, 28, 28, 28, -1, 29, 28,
            83, 30, 28, 28, -1, 81, 31, 28,
            28, 28, 29, 29, 29, 29, 28, 28,
            28, 28, 28, 28, 29, 28, 32, 33,
            28, 28, 29, 28, 28, 28, 29, 29,
            28, 29, 28, 28, 28, 29, 28, 28,
            28, 28, 28, 28, 28, 28 ),
        array( -1, 74, 74, 74, 74, 233, 8, 74,
            74, 128, 74, 131, 131, 131, 9, 74,
            74, 74, 8, 8, 8, 8, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 8, 74, 74, 74, 8, 8,
            74, 8, 74, 74, 74, 8, 74, 74,
            74, 74, 74, 74, 74, 74 ),
        array( -1, 60, 60, 60, 60, 60, 114, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            114, 60, 114, 114, 114, 114, 60, -1,
            60, 60, 60, 60, 114, 60, 60, 60,
            60, 60, 114, 60, 60, 60, 114, 114,
            60, 114, 60, 60, 60, 114, 60, 60,
            60, 60, 60, 60, 60, 60 ),
        array( -1, 205, 205, 205, 205, 100, 206, 230,
            205, 205, 207, 100, 100, 100, 205, 205,
            205, 229, 206, 206, 206, 206, 205, 205,
            205, 205, 277, 205, 206, 205, 205, 205,
            72, 205, 206, 205, 205, 205, 206, 206,
            205, 206, 205, 205, 205, 206, 205, 205,
            205, 205, 205, 205, 205, 205 ),
        array( 1, 81, 81, 81, 81, 34, 81, 81,
            81, 81, 81, 34, 34, 34, 81, 81,
            81, 81, 81, 81, 81, 81, 81, 81,
            81, 81, 81, 81, 81, 81, 81, 81,
            81, 81, 81, 81, 81, 81, 81, 81,
            81, 81, 81, 81, 81, 81, 81, 81,
            81, 81, 81, 81, 81, 81 ),
        array( -1, 74, 74, 74, 74, 74, 10, 74,
            74, 74, 74, 74, 74, 74, 11, 74,
            134, 12, 10, 10, 10, 10, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 10, 74, 74, 74, 10, 10,
            74, 10, 74, 74, 74, 10, 74, 74,
            74, 74, 74, 74, 74, 74 ),
        array( -1, -1, -1, -1, -1, -1, 141, 142,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 143, 141, 141, 141, 141, 144, -1,
            -1, -1, 145, -1, 141, -1, -1, -1,
            -1, 52, 141, -1, -1, -1, 141, 141,
            -1, 141, -1, -1, -1, 141, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( 1, 35, 35, 35, 35, -1, 35, 35,
            35, 35, 35, 35, -1, 35, 36, 35,
            35, 35, 35, 35, 35, 35, 35, 35,
            35, 35, 35, 35, 35, 35, 35, 35,
            35, 35, 35, 35, 35, 35, 35, 35,
            35, 35, 35, 35, 35, 35, 35, 35,
            35, 35, 35, 35, 35, 35 ),
        array( -1, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 14, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 146, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            53, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( 1, 37, 37, 37, 37, 37, 37, 37,
            37, 37, 37, 37, 37, 37, 37, 37,
            37, 37, 37, 37, 37, 37, 37, 37,
            37, 37, 37, 112, 37, 37, 37, 37,
            37, 37, 37, 37, 37, 37, 37, 37,
            37, 37, 37, 37, 37, 37, 37, 37,
            37, 37, 37, 37, 37, 37 ),
        array( -1, 74, 74, 74, 74, 74, 74, 74,
            15, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74 ),
        array( -1, 74, 74, 74, 74, 233, 74, 74,
            74, 128, 74, 131, 131, 131, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 165, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 166, -1, -1, -1, -1, -1,
            56, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            16, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74 ),
        array( -1, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 17, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74 ),
        array( 1, 48, 48, 48, 48, 48, 48, 48,
            48, 48, 48, 48, 48, 48, 48, 48,
            48, 48, 48, 48, 48, 48, 48, 49,
            48, 48, 48, 48, 48, 48, 48, 48,
            48, 48, 48, 48, 48, 48, 48, 48,
            48, 48, 48, 48, 48, 48, 48, 48,
            48, 48, 48, 48, 48, 48 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 50, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( 1, 81, 118, 81, 81, -1, 123, 81,
            81, 81, 81, 81, -1, 81, 81, 81,
            81, 81, 123, 123, 123, 123, 126, 81,
            81, 81, 81, 81, 123, 81, 81, 81,
            81, 81, 123, 81, 81, 81, 123, 123,
            81, 123, 81, 81, 81, 123, 81, 81,
            81, 81, 81, 81, 81, 81 ),
        array( -1, -1, -1, -1, -1, -1, 147, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 147, 147, 147, 147, -1, -1,
            -1, -1, -1, -1, 147, -1, -1, -1,
            -1, -1, 147, -1, -1, -1, 147, 147,
            -1, 147, -1, -1, -1, 147, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 148, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            148, -1, 148, 148, 148, 148, -1, -1,
            -1, -1, -1, -1, 148, -1, -1, -1,
            -1, -1, 148, -1, -1, -1, 148, 148,
            -1, 148, -1, -1, -1, 148, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 221, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            52, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 149, -1, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 150, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 150, 150, 150, 150, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 150, -1, -1, -1, 150, 150,
            -1, 150, -1, -1, -1, 150, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 147, 142,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 151, 147, 147, 147, 147, 144, -1,
            -1, -1, 152, -1, 147, -1, -1, -1,
            -1, 52, 147, -1, -1, -1, 147, 147,
            -1, 147, -1, -1, -1, 147, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 148, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            148, -1, 148, 148, 148, 148, -1, 153,
            -1, -1, 154, -1, 148, -1, -1, -1,
            -1, -1, 148, -1, -1, -1, 148, 148,
            -1, 148, -1, -1, -1, 148, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 143, 143, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 150, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 150, 150, 150, 150, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            53, -1, 150, -1, -1, -1, 150, 150,
            -1, 150, -1, -1, -1, 150, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 156, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            156, -1, 156, 156, 156, 156, -1, -1,
            -1, -1, -1, -1, 156, -1, -1, -1,
            -1, -1, 156, -1, -1, -1, 156, 156,
            -1, 156, -1, -1, -1, 156, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 222, -1, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, 142,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 143, -1, -1, -1, -1, 144, -1,
            -1, -1, 145, -1, -1, -1, -1, -1,
            -1, 52, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 157, -1, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 155, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 155, 155, 155, 155, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            52, -1, 155, -1, -1, -1, 155, 155,
            -1, 155, -1, -1, -1, 155, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 156, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            156, -1, 156, 156, 156, 156, -1, 158,
            -1, -1, 159, -1, 156, -1, -1, -1,
            -1, -1, 156, -1, -1, -1, 156, 156,
            -1, 156, -1, -1, -1, 156, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 153, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 153,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, 142,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 151, -1, -1, -1, -1, 144, -1,
            -1, -1, 152, -1, -1, -1, -1, -1,
            -1, 52, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 160, -1, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 158, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 158,
            -1, -1, -1, -1, -1, -1 ),
        array( 1, 81, 129, 81, 81, -1, 54, 81,
            81, 81, 81, 81, -1, 81, 81, 81,
            81, 81, 54, 54, 54, 54, 132, 81,
            81, 81, 81, 81, 54, 81, 81, 81,
            81, 81, 54, 81, 81, 81, 54, 54,
            81, 54, 81, 81, 81, 54, 81, 81,
            81, 81, 81, 81, 81, 81 ),
        array( -1, -1, -1, -1, -1, -1, 210, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 210, 210, 210, 210, -1, -1,
            -1, -1, -1, -1, 210, -1, -1, -1,
            -1, -1, 210, -1, -1, -1, 210, 210,
            -1, 210, -1, -1, -1, 210, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 167, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            167, -1, 167, 167, 167, 167, -1, -1,
            -1, -1, -1, -1, 167, -1, -1, -1,
            -1, -1, 167, -1, -1, -1, 167, 167,
            -1, 167, -1, -1, -1, 167, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 168, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 168, 168, 168, 168, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 168, -1, -1, -1, 168, 168,
            -1, 168, -1, -1, -1, 168, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 169 ),
        array( -1, -1, -1, -1, -1, -1, 167, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            167, -1, 167, 167, 167, 167, -1, 95,
            -1, -1, 171, -1, 167, -1, -1, -1,
            -1, -1, 167, -1, -1, -1, 167, 167,
            -1, 167, -1, -1, -1, 167, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 168, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 168, 168, 168, 168, -1, -1,
            -1, -1, 166, -1, -1, -1, -1, -1,
            56, -1, 168, -1, -1, -1, 168, 168,
            -1, 168, -1, -1, -1, 168, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 56, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 56,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 172, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            172, -1, 172, 172, 172, 172, -1, -1,
            -1, -1, -1, -1, 172, -1, -1, -1,
            -1, -1, 172, -1, -1, -1, 172, 172,
            -1, 172, -1, -1, -1, 172, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 173, -1, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 172, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            172, -1, 172, 172, 172, 172, -1, 215,
            -1, -1, 174, -1, 172, -1, -1, -1,
            -1, -1, 172, -1, -1, -1, 172, 172,
            -1, 172, -1, -1, -1, 172, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 95, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 95,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 223, -1, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( 1, 81, 81, 81, 81, -1, 81, 81,
            81, 81, 81, 81, -1, 81, 81, 81,
            81, 81, 81, 81, 81, 81, 132, 81,
            81, 81, 81, 81, 81, 81, 81, 81,
            81, 57, 81, 81, 81, 81, 81, 81,
            81, 81, 81, 81, 81, 81, 81, 81,
            81, 81, 81, 81, 81, 81 ),
        array( 1, 58, 58, 58, 58, 58, 58, 58,
            315, 58, 58, 58, 58, 58, 58, 58,
            58, 58, 58, 58, 58, 58, 58, 58,
            58, 58, 58, 58, 58, 58, 58, 58,
            58, 58, 58, 58, 58, 58, 58, 58,
            58, 58, 58, 58, 58, 58, 58, 58,
            58, 58, 58, 58, 58, 58 ),
        array( 1, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, 97,
            297, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60 ),
        array( -1, -1, -1, -1, -1, -1, -1, 179,
            -1, -1, 180, -1, -1, -1, -1, -1,
            -1, 181, -1, -1, -1, -1, -1, -1,
            -1, -1, 283, -1, -1, -1, -1, -1,
            62, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 183, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 183, 183, 183, 183, -1, -1,
            -1, -1, -1, -1, 183, -1, -1, -1,
            -1, -1, 183, -1, -1, -1, 183, 183,
            -1, 183, -1, -1, -1, 183, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 184, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 184, 184, 184, 184, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 184, -1, -1, -1, 184, 184,
            -1, 184, -1, -1, -1, 184, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 185, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            185, -1, 185, 185, 185, 185, -1, -1,
            -1, -1, -1, -1, 185, -1, -1, -1,
            -1, -1, 185, -1, -1, -1, 185, 185,
            -1, 185, -1, -1, -1, 185, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, 179,
            -1, -1, 180, -1, -1, -1, -1, -1,
            -1, 186, -1, -1, -1, -1, -1, -1,
            -1, -1, 285, -1, -1, -1, -1, -1,
            62, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 183, 179,
            -1, -1, 180, -1, -1, -1, -1, -1,
            -1, 186, 183, 183, 183, 183, -1, -1,
            -1, -1, 285, -1, 183, -1, -1, -1,
            62, -1, 183, -1, -1, -1, 183, 183,
            -1, 183, -1, -1, -1, 183, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 184, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 184, 184, 184, 184, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            62, -1, 184, -1, -1, -1, 184, 184,
            -1, 184, -1, -1, -1, 184, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 185, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            185, -1, 185, 185, 185, 185, -1, 178,
            -1, -1, 187, -1, 185, -1, -1, -1,
            -1, -1, 185, -1, -1, -1, 185, 185,
            -1, 185, -1, -1, -1, 185, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 188, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            188, -1, 188, 188, 188, 188, -1, -1,
            -1, -1, -1, -1, 188, -1, -1, -1,
            -1, -1, 188, -1, -1, -1, 188, 188,
            -1, 188, -1, -1, -1, 188, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 189, -1, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 188, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            188, -1, 188, 188, 188, 188, -1, 182,
            -1, -1, 190, -1, 188, -1, -1, -1,
            -1, -1, 188, -1, -1, -1, 188, 188,
            -1, 188, -1, -1, -1, 188, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 178, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 178,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 225, -1, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, 192,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 193, -1, -1, -1, -1, -1, -1,
            -1, -1, 287, -1, -1, -1, -1, -1,
            63, 227, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 195, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 195, 195, 195, 195, -1, -1,
            -1, -1, -1, -1, 195, -1, -1, -1,
            -1, -1, 195, -1, -1, -1, 195, 195,
            -1, 195, -1, -1, -1, 195, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 196, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            196, -1, 196, 196, 196, 196, -1, -1,
            -1, -1, -1, -1, 196, -1, -1, -1,
            -1, -1, 196, -1, -1, -1, 196, 196,
            -1, 196, -1, -1, -1, 196, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, 192,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 198, -1, -1, -1, -1, -1, -1,
            -1, -1, 289, -1, -1, -1, -1, -1,
            63, 227, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 195, 192,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 198, 195, 195, 195, 195, -1, -1,
            -1, -1, 289, -1, 195, -1, -1, -1,
            63, 227, 195, -1, -1, -1, 195, 195,
            -1, 195, -1, -1, -1, 195, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 196, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            196, -1, 196, 196, 196, 196, -1, 191,
            -1, -1, 199, -1, 196, -1, -1, -1,
            -1, -1, 196, -1, -1, -1, 196, 196,
            -1, 196, -1, -1, -1, 196, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 197, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 197, 197, 197, 197, -1, -1,
            -1, -1, -1, -1, 197, -1, -1, -1,
            64, 200, 197, -1, -1, -1, 197, 197,
            -1, 197, -1, -1, -1, 197, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 201, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            201, -1, 201, 201, 201, 201, -1, -1,
            -1, -1, -1, -1, 201, -1, -1, -1,
            -1, -1, 201, -1, -1, -1, 201, 201,
            -1, 201, -1, -1, -1, 201, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 202, -1, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 203, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 203, 203, 203, 203, -1, -1,
            -1, -1, -1, -1, 203, -1, -1, -1,
            -1, -1, 203, -1, -1, -1, 203, 203,
            -1, 203, -1, -1, -1, 203, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 201, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            201, -1, 201, 201, 201, 201, -1, 194,
            -1, -1, 204, -1, 201, -1, -1, -1,
            -1, -1, 201, -1, -1, -1, 201, 201,
            -1, 201, -1, -1, -1, 201, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 191, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 191,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 203, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 203, 203, 203, 203, -1, -1,
            -1, -1, -1, -1, 203, -1, -1, -1,
            65, -1, 203, -1, -1, -1, 203, 203,
            -1, 203, -1, -1, -1, 203, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 228, -1, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, 109, 109, 109, 109, 109, 120, 216,
            109, 109, 234, 109, 109, 109, 109, 109,
            109, 243, 120, 120, 120, 120, 109, 109,
            109, 109, 275, 109, 120, 109, 109, 109,
            109, 109, 120, 109, 109, 109, 120, 120,
            109, 120, 109, 109, 109, 120, 109, 109,
            109, 109, 109, 109, 109, 109 ),
        array( -1, 109, 109, 109, 109, 109, 219, 109,
            109, 109, 109, 109, 109, 109, 109, 109,
            109, 109, 219, 219, 219, 219, 109, 109,
            109, 109, 109, 109, 109, 109, 109, 109,
            109, 109, 219, 109, 109, 109, 219, 219,
            109, 219, 109, 109, 109, 219, 109, 109,
            109, 109, 109, 109, 109, 109 ),
        array( -1, 109, 109, 109, 109, 109, 250, 109,
            109, 109, 109, 109, 109, 109, 109, 109,
            250, 109, 250, 250, 250, 250, 109, 237,
            109, 109, 254, 109, 250, 109, 109, 109,
            109, 109, 250, 109, 109, 109, 250, 250,
            109, 250, 109, 109, 109, 250, 109, 109,
            109, 109, 109, 109, 109, 109 ),
        array( -1, 109, 109, 109, 109, 109, 109, 109,
            109, 109, 109, 109, 109, 109, 109, 109,
            109, 109, 109, 237, 109, 109, 109, 109,
            109, 109, 109, 109, 109, 109, 109, 109,
            109, 109, 109, 109, 109, 109, 109, 109,
            109, 109, 109, 109, 109, 109, 109, 237,
            109, 109, 109, 109, 109, 109 ),
        array( -1, -1, -1, -1, -1, -1, 210, 163,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 170, 210, 210, 210, 210, -1, -1,
            -1, -1, 280, -1, 210, -1, -1, -1,
            -1, -1, 210, -1, -1, -1, 210, 210,
            -1, 210, -1, -1, -1, 210, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, 58, 58, 58, 58, 58, 58, 58,
            58, 58, 58, 58, 58, 58, 58, 58,
            58, 58, 58, 58, 58, 96, 58, 58,
            58, 58, 58, 58, 58, 58, 58, 58,
            58, 58, 58, 58, 58, 58, 58, 58,
            58, 58, 58, 96, 58, 58, 58, 58,
            58, 58, 58, 58, 58, 58 ),
        array( -1, 109, 109, 109, 109, 109, 109, 109,
            109, 109, 109, 109, 109, 109, 109, 109,
            115, 109, 109, 109, 109, 109, 109, 109,
            109, 109, 109, 109, 109, 109, 109, 109,
            109, 109, 109, 109, 109, 109, 109, 109,
            109, 109, 109, 109, 109, 109, 109, 109,
            109, 109, 109, 109, 109, 109 ),
        array( -1, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 125,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74 ),
        array( -1, -1, -1, -1, -1, -1, -1, 163,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 170, -1, -1, -1, -1, -1, -1,
            -1, -1, 280, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, 205, 205, 205, 205, 100, 206, 205,
            205, 205, 205, 100, 100, 100, 205, 205,
            205, 205, 206, 206, 206, 206, 205, 205,
            205, 205, 205, 205, 206, 205, 205, 205,
            205, 205, 206, 205, 205, 205, 206, 206,
            205, 206, 205, 205, 205, 206, 205, 205,
            205, 205, 205, 205, 205, 205 ),
        array( -1, 60, 60, 60, 60, 60, 217, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            217, 60, 217, 217, 217, 217, 60, 182,
            60, 60, 302, 60, 217, 60, 60, 60,
            60, 60, 217, 60, 60, 60, 217, 217,
            60, 217, 60, 60, 60, 217, 60, 60,
            60, 60, 60, 60, 60, 60 ),
        array( -1, 60, 60, 60, 60, 60, 217, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            217, 60, 217, 217, 217, 217, 60, -1,
            60, 60, 60, 60, 217, 60, 60, 60,
            60, 60, 217, 60, 60, 60, 217, 217,
            60, 217, 60, 60, 60, 217, 60, 60,
            60, 60, 60, 60, 60, 60 ),
        array( -1, 205, 205, 205, 205, 100, 207, 205,
            205, 205, 205, 100, 100, 100, 205, 205,
            205, 205, 207, 207, 207, 207, 205, 205,
            205, 205, 205, 205, 205, 205, 205, 205,
            72, 205, 207, 205, 205, 205, 207, 207,
            205, 207, 205, 205, 205, 207, 205, 205,
            205, 205, 205, 205, 205, 205 ),
        array( -1, -1, -1, -1, -1, -1, 155, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 155, 155, 155, 155, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 155, -1, -1, -1, 155, 155,
            -1, 155, -1, -1, -1, 155, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 220, 220, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 215, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 215,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 224, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 224,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 197, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 197, 197, 197, 197, -1, -1,
            -1, -1, -1, -1, 197, -1, -1, -1,
            -1, -1, 197, -1, -1, -1, 197, 197,
            -1, 197, -1, -1, -1, 197, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 226, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 226,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, 109, 109, 109, 109, 109, 250, 109,
            109, 109, 109, 109, 109, 109, 109, 109,
            250, 109, 250, 250, 250, 250, 109, 109,
            109, 109, 109, 109, 250, 109, 109, 109,
            109, 109, 250, 109, 109, 109, 250, 250,
            109, 250, 109, 109, 109, 250, 109, 109,
            109, 109, 109, 109, 109, 109 ),
        array( -1, 109, 109, 109, 109, 109, 120, 109,
            109, 109, 109, 109, 109, 109, 109, 109,
            109, 109, 120, 120, 120, 120, 109, 109,
            109, 109, 109, 109, 120, 109, 109, 109,
            109, 109, 120, 109, 109, 109, 120, 120,
            109, 120, 109, 109, 109, 120, 109, 109,
            109, 109, 109, 109, 109, 109 ),
        array( -1, 60, 60, 60, 60, 60, 273, 300,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 212, 273, 273, 273, 273, 60, -1,
            60, 60, 278, 60, 231, 60, 60, 60,
            60, 60, 273, 60, 60, 60, 273, 273,
            60, 273, 60, 60, 60, 273, 60, 60,
            60, 60, 60, 60, 60, 60 ),
        array( -1, 205, 205, 205, 205, 100, 207, 205,
            205, 205, 205, 100, 100, 100, 205, 205,
            205, 205, 207, 207, 207, 207, 205, 205,
            205, 205, 205, 205, 205, 205, 205, 205,
            205, 205, 207, 205, 205, 205, 207, 207,
            205, 207, 205, 205, 205, 207, 205, 205,
            205, 205, 205, 205, 205, 205 ),
        array( -1, 60, 60, 60, 60, 60, 235, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            235, 60, 235, 235, 235, 235, 60, 191,
            60, 60, 305, 60, 235, 60, 60, 60,
            60, 60, 235, 60, 60, 60, 235, 235,
            60, 235, 60, 60, 60, 235, 60, 60,
            60, 60, 60, 60, 60, 60 ),
        array( -1, 60, 60, 60, 60, 60, 235, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            235, 60, 235, 235, 235, 235, 60, -1,
            60, 60, 60, 60, 235, 60, 60, 60,
            60, 60, 235, 60, 60, 60, 235, 235,
            60, 235, 60, 60, 60, 235, 60, 60,
            60, 60, 60, 60, 60, 60 ),
        array( -1, 205, 205, 205, 205, 100, 205, 230,
            205, 205, 207, 100, 100, 100, 205, 205,
            205, 229, 205, 205, 205, 205, 205, 205,
            205, 205, 277, 205, 205, 205, 205, 205,
            72, 205, 205, 205, 205, 205, 205, 205,
            205, 205, 205, 205, 205, 205, 205, 205,
            205, 205, 205, 205, 205, 205 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 238, 238, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, 109, 109, 109, 109, 109, 109, 109,
            109, 109, 109, 109, 109, 109, 109, 109,
            109, 109, 109, 109, 109, 109, 109, 109,
            109, 109, 109, 109, 109, 109, 109, 109,
            109, 109, 109, 109, 109, 109, 109, 109,
            109, 109, 109, 109, 109, 243, 243, 109,
            109, 109, 109, 109, 109, 109 ),
        array( -1, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, -1,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 119, 119, 60,
            60, 60, 60, 60, 60, 60 ),
        array( -1, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 136, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74 ),
        array( -1, 205, 205, 205, 205, 100, 208, 205,
            205, 205, 205, 100, 100, 100, 205, 205,
            208, 205, 208, 208, 208, 208, 205, 205,
            205, 205, 205, 205, 208, 205, 205, 205,
            205, 205, 208, 205, 205, 205, 208, 208,
            205, 208, 205, 205, 205, 208, 205, 205,
            205, 205, 205, 205, 205, 205 ),
        array( -1, 60, 60, 60, 60, 60, 244, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            244, 60, 244, 244, 244, 244, 60, 194,
            60, 60, 306, 60, 244, 60, 60, 60,
            60, 60, 244, 60, 60, 60, 244, 244,
            60, 244, 60, 60, 60, 244, 60, 60,
            60, 60, 60, 60, 60, 60 ),
        array( -1, 60, 60, 60, 60, 60, 244, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            244, 60, 244, 244, 244, 244, 60, -1,
            60, 60, 60, 60, 244, 60, 60, 60,
            60, 60, 244, 60, 60, 60, 244, 244,
            60, 244, 60, 60, 60, 244, 60, 60,
            60, 60, 60, 60, 60, 60 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 246, 246, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, 109, 109, 109, 109, 109, 109, 216,
            109, 109, 234, 109, 109, 109, 109, 109,
            109, 243, 109, 109, 109, 109, 109, 109,
            109, 109, 275, 109, 109, 109, 109, 109,
            109, 109, 109, 109, 109, 109, 109, 109,
            109, 109, 109, 109, 109, 109, 109, 109,
            109, 109, 109, 109, 109, 109 ),
        array( -1, 205, 205, 205, 205, 100, 208, 205,
            205, 205, 205, 100, 100, 100, 205, 205,
            208, 205, 208, 208, 208, 208, 205, 248,
            205, 205, 299, 205, 208, 205, 205, 205,
            205, 205, 208, 205, 205, 205, 208, 208,
            205, 208, 205, 205, 205, 208, 205, 205,
            205, 205, 205, 205, 205, 205 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 251, 251, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, 60, 60, 60, 60, 60, 281, 300,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 249, 281, 281, 281, 281, 60, -1,
            60, 60, 284, 60, 253, 60, 60, 60,
            60, 60, 281, 60, 60, 60, 281, 281,
            60, 281, 60, 60, 60, 281, 60, 60,
            60, 60, 60, 60, 60, 60 ),
        array( -1, 205, 205, 205, 205, 100, 205, 205,
            205, 205, 205, 100, 100, 100, 205, 205,
            205, 205, 205, 205, 205, 205, 205, 205,
            205, 205, 205, 205, 205, 205, 205, 205,
            205, 205, 205, 205, 205, 205, 205, 205,
            205, 205, 205, 205, 209, 205, 205, 205,
            205, 205, 205, 205, 205, 205 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 255, 255, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, -1,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 218, 218, 60,
            60, 60, 60, 60, 60, 60 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 258, 258, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, 60, 60, 60, 60, 60, 60, 300,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 212, 60, 60, 60, 60, 60, -1,
            60, 60, 278, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 261, 261, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, 60, 60, 60, 60, 60, 60, 300,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 249, 60, 60, 60, 60, 60, -1,
            60, 60, 284, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60 ),
        array( -1, 60, 60, 60, 60, 60, 290, 304,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 264, 290, 290, 290, 290, 60, -1,
            60, 60, 291, 60, 265, 60, 60, 60,
            60, 60, 290, 60, 60, 60, 290, 290,
            60, 290, 60, 60, 60, 290, 60, 60,
            60, 60, 60, 60, 60, 60 ),
        array( -1, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, -1,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 236, 236, 60,
            60, 60, 60, 60, 60, 60 ),
        array( -1, 60, 60, 60, 60, 60, 292, 304,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 267, 292, 292, 292, 292, 60, -1,
            60, 60, 293, 60, 268, 60, 60, 60,
            60, 60, 292, 60, 60, 60, 292, 292,
            60, 292, 60, 60, 60, 292, 60, 60,
            60, 60, 60, 60, 60, 60 ),
        array( -1, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, -1,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 245, 245, 60,
            60, 60, 60, 60, 60, 60 ),
        array( -1, 60, 60, 60, 60, 60, 60, 304,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 264, 60, 60, 60, 60, 60, -1,
            60, 60, 291, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60 ),
        array( -1, 60, 60, 60, 60, 60, 60, 304,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 267, 60, 60, 60, 60, 60, -1,
            60, 60, 293, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60 ),
        array( -1, 58, 58, 58, 58, 58, 58, 58,
            58, 58, 58, 58, 58, 58, 58, 58,
            58, 58, 58, 58, 58, 58, 58, 58,
            58, 58, 58, 58, 58, 58, 58, 58,
            58, 58, 58, 58, 58, 58, 58, 58,
            58, 211, 211, 58, 58, 58, 58, 58,
            58, 58, 58, 58, 58, 58 ),
        array( -1, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 242, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74 ),
        array( -1, 205, 205, 205, 205, 100, 205, 205,
            205, 205, 205, 100, 100, 100, 205, 205,
            205, 205, 205, 205, 205, 205, 205, 205,
            205, 205, 205, 205, 205, 205, 205, 205,
            205, 205, 205, 205, 205, 205, 205, 205,
            205, 205, 205, 205, 240, 205, 205, 205,
            205, 205, 205, 205, 205, 205 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 239, -1, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, 109, 109, 109, 109, 109, 109, 109,
            109, 109, 109, 109, 109, 109, 109, 109,
            109, 109, 109, 109, 109, 109, 109, 109,
            109, 109, 109, 109, 109, 109, 109, 109,
            109, 109, 109, 109, 109, 109, 109, 109,
            109, 109, 109, 109, 279, 109, 109, 109,
            109, 109, 109, 109, 109, 109 ),
        array( -1, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, -1,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 241, 60, 60, 60,
            60, 60, 60, 60, 60, 60 ),
        array( -1, 205, 205, 205, 205, 100, 205, 205,
            205, 205, 205, 100, 100, 100, 205, 205,
            205, 205, 205, 205, 205, 205, 205, 205,
            205, 205, 205, 205, 205, 205, 205, 205,
            205, 205, 205, 205, 205, 205, 205, 205,
            205, 205, 205, 205, 205, 229, 229, 205,
            205, 205, 205, 205, 205, 205 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 247, -1, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, 205, 205, 205, 205, 100, 205, 205,
            205, 205, 205, 100, 100, 100, 205, 205,
            205, 205, 205, 248, 205, 205, 205, 205,
            205, 205, 205, 205, 205, 205, 205, 205,
            205, 205, 205, 205, 205, 205, 205, 205,
            205, 205, 205, 205, 205, 205, 205, 248,
            205, 205, 205, 205, 205, 205 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 252, -1, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, -1,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 257, 60, 60, 60,
            60, 60, 60, 60, 60, 60 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 256, -1, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 260, 60, 60, 60, -1,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, 260,
            60, 60, 60, 60, 60, 60 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 259, -1, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 263, 60, 60, 60, -1,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, 263,
            60, 60, 60, 60, 60, 60 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 262, -1, -1, -1,
            -1, -1, -1, -1, -1, -1 ),
        array( -1, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, -1,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 266, 60, 60, 60,
            60, 60, 60, 60, 60, 60 ),
        array( -1, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, -1,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 269, 60, 60, 60,
            60, 60, 60, 60, 60, 60 ),
        array( -1, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 270, 60, 60, 60, -1,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, 270,
            60, 60, 60, 60, 60, 60 ),
        array( -1, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 271, 60, 60, 60, -1,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, 271,
            60, 60, 60, 60, 60, 60 ),
        array( -1, 58, 58, 58, 58, 58, 58, 58,
            58, 58, 58, 58, 58, 58, 58, 58,
            58, 58, 58, 58, 58, 58, 58, 58,
            58, 58, 58, 58, 58, 58, 58, 58,
            58, 58, 58, 58, 58, 58, 58, 272,
            272, 58, 58, 58, 58, 58, 58, 58,
            58, 58, 58, 58, 58, 58 ),
        array( -1, 60, 60, 60, 60, 60, 273, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 273, 273, 273, 273, 60, -1,
            60, 60, 60, 60, 273, 60, 60, 60,
            60, 60, 273, 60, 60, 60, 273, 273,
            60, 273, 60, 60, 60, 273, 60, 60,
            319, 60, 60, 60, 60, 60 ),
        array( -1, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 274, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74 ),
        array( -1, 109, 109, 109, 109, 109, 109, 109,
            109, 109, 109, 109, 109, 109, 109, 109,
            109, 109, 109, 109, 109, 109, 109, 109,
            109, 109, 109, 109, 109, 109, 109, 109,
            109, 109, 109, 109, 109, 109, 109, 109,
            109, 109, 109, 109, 282, 109, 109, 109,
            109, 109, 109, 109, 109, 109 ),
        array( -1, 60, 60, 60, 60, 60, 281, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 281, 281, 281, 281, 60, -1,
            60, 60, 60, 60, 281, 60, 60, 60,
            60, 60, 281, 60, 60, 60, 281, 281,
            60, 281, 60, 60, 60, 281, 60, 60,
            60, 60, 60, 60, 60, 60 ),
        array( -1, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, -1,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 286, 60, 60, 60,
            60, 60, 60, 60, 60, 60 ),
        array( -1, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, -1,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 288, 60, 60, 60,
            60, 60, 60, 60, 60, 60 ),
        array( -1, 60, 60, 60, 60, 60, 290, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 290, 290, 290, 290, 60, -1,
            60, 60, 60, 60, 290, 60, 60, 60,
            60, 60, 290, 60, 60, 60, 290, 290,
            60, 290, 60, 60, 60, 290, 60, 60,
            60, 60, 60, 60, 60, 60 ),
        array( -1, 60, 60, 60, 60, 60, 292, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 292, 292, 292, 292, 60, -1,
            60, 60, 60, 60, 292, 60, 60, 60,
            60, 60, 292, 60, 60, 60, 292, 292,
            60, 292, 60, 60, 60, 292, 60, 60,
            60, 60, 60, 60, 60, 60 ),
        array( -1, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, -1,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 294, 60, 60, 60,
            60, 60, 60, 60, 60, 60 ),
        array( -1, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, -1,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 295, 60, 60, 60,
            60, 60, 60, 60, 60, 60 ),
        array( -1, 58, 58, 58, 58, 58, 58, 58,
            58, 58, 58, 58, 58, 58, 58, 58,
            58, 58, 58, 58, 58, 58, 58, 58,
            58, 58, 58, 58, 58, 58, 58, 58,
            58, 58, 58, 58, 58, 296, 296, 58,
            58, 58, 58, 58, 58, 58, 58, 58,
            58, 58, 58, 58, 58, 58 ),
        array( -1, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 303, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, -1,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60 ),
        array( -1, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 298, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74 ),
        array( -1, 74, 74, 74, 74, 310, 74, 74,
            74, 74, 74, 310, 310, 310, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74 ),
        array( -1, 58, 58, 58, 58, 58, 58, 58,
            58, 58, 58, 58, 58, 58, 58, 58,
            58, 58, 307, 58, 58, 58, 58, 58,
            58, 58, 58, 58, 58, 58, 58, 58,
            58, 58, 58, 58, 307, 58, 58, 58,
            58, 58, 58, 58, 58, 58, 58, 58,
            58, 58, 58, 58, 58, 58 ),
        array( -1, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, -1,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 308, 60 ),
        array( -1, 58, 58, 58, 58, 58, 58, 58,
            58, 58, 58, 58, 58, 58, 58, 58,
            58, 58, 58, 58, 58, 58, 58, 58,
            58, 58, 58, 58, 58, 58, 58, 58,
            58, 58, 311, 311, 58, 58, 58, 58,
            58, 58, 58, 58, 58, 58, 58, 58,
            58, 58, 58, 58, 58, 58 ),
        array( -1, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, -1,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 312, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60 ),
        array( -1, 58, 58, 58, 58, 58, 58, 58,
            58, 313, 58, 58, 58, 58, 58, 58,
            58, 58, 58, 58, 58, 58, 58, 58,
            58, 58, 58, 58, 58, 58, 58, 58,
            58, 58, 58, 58, 58, 58, 58, 58,
            58, 58, 58, 58, 58, 58, 58, 58,
            58, 58, 58, 58, 58, 58 ),
        array( -1, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, -1,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 314, 60, 60 ),
        array( -1, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, -1,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 316, 60, 60, 60 ),
        array( -1, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, -1,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 317, 60, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60 ),
        array( -1, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, -1,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 60, 60, 60, 60, 60, 60, 60,
            60, 318, 60, 60, 60, 60 )
        );


    function  yylex()
    {
        $yy_lookahead = '';
        $yy_anchor = YY_NO_ANCHOR;
        $yy_state = $this->yy_state_dtrans[$this->yy_lexical_state];
        $yy_next_state = YY_NO_STATE;
         $yy_last_accept_state = YY_NO_STATE;
        $yy_initial = true;
        $yy_this_accept = 0;
        
        $this->yy_mark_start();
        $yy_this_accept = $this->yy_acpt[$yy_state];
        if (YY_NOT_ACCEPT != $yy_this_accept) {
            $yy_last_accept_state = $yy_state;
            $this->yy_buffer_end = $this->yy_buffer_index;
        }
        while (true) {
            if ($yy_initial && $this->yy_at_bol) {
                $yy_lookahead =  YY_BOL;
            } else {
                $yy_lookahead = $this->yy_advance();
            }
            $yy_next_state = $this->yy_nxt[$this->yy_rmap[$yy_state]][$this->yy_cmap[$yy_lookahead]];
            if (YY_EOF == $yy_lookahead && $yy_initial) {
                return false;            }
            if (YY_F != $yy_next_state) {
                $yy_state = $yy_next_state;
                $yy_initial = false;
                $yy_this_accept = $this->yy_acpt[$yy_state];
                if (YY_NOT_ACCEPT != $yy_this_accept) {
                    $yy_last_accept_state = $yy_state;
                    $this->yy_buffer_end = $this->yy_buffer_index;
                }
            } else {
                if (YY_NO_STATE == $yy_last_accept_state) {
                    $this->yy_error(1,1);
                    if ($this->_fatal) {
                        return;
                    }
                } else {
                    $yy_anchor = $this->yy_acpt[$yy_last_accept_state];
                    if (0 != (YY_END & $yy_anchor)) {
                        $this->yy_move_end();
                    }
                    $this->yy_to_mark();
                    if ($yy_last_accept_state < 0) {
                        if ($yy_last_accept_state < 320) {
                            $this->yy_error(YY_E_INTERNAL, false);
                            if ($this->_fatal) {
                                return;
                            }
                        }
                    } else {

                        switch ($yy_last_accept_state) {
case 2:
{
    //abcd -- data characters  
    // { and ) added for flexy
    $this->value = $this->createToken('Text');
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 3:
{
    // &abc;
    $this->value = $this->createToken('Text');
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 4:
{
    //<name -- start tag */
    if ($this->options['ignore_html']) {
        return $this->returnSimple();
    }
    $this->tagName = trim(substr($this->yytext(),1));
    $this->tokenName = 'Tag';
    $this->value = '';
    $this->attributes = array();
    $this->yybegin(IN_ATTR);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 5:
{  
    // <> -- empty start tag */
    if ($this->options['ignore_html']) {
        return $this->returnSimple();
    }
    return $this->raiseError("empty tag"); 
}
case 6:
{ 
    /* <? php start.. */
    //echo "STARTING PHP?\n";
    $this->yyPhpBegin = $this->yy_buffer_start;
    $this->yybegin(IN_PHP);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 7:
{
    // &#abc;
    $this->value = $this->createToken('Text');
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 8:
{
    /* </title> -- end tag */
    if ($this->options['ignore_html']) {
        return $this->returnSimple();
    }
    if ($this->inStyle) {
        $this->inStyle = false;
    }
    $this->tagName = trim(substr($this->yytext(),1));
    $this->tokenName = 'EndTag';
    $this->yybegin(IN_ENDTAG);
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 9:
{
    /* </> -- empty end tag */  
    if ($this->options['ignore_html']) {
        return $this->returnSimple();
    }
    return $this->raiseError("empty end tag not handled");
}
case 10:
{
    /* <!DOCTYPE -- markup declaration */
    if ($this->options['ignore_html']) {
        return $this->returnSimple();
    }
    $this->value = $this->createToken('Doctype');
    $this->yybegin(IN_MD);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 11:
{
    /* <!> */
    if ($this->options['ignore_html']) {
        return $this->returnSimple();
    }
    return $this->raiseError("empty markup tag not handled"); 
}
case 12:
{
    /* <![ -- marked section */
    return $this->returnSimple();
}
case 13:
{ 
    /* eg. <?xml-stylesheet, <?php ... */
    $t = $this->yytext();
    $tagname = trim(strtoupper(substr($t,2)));
   // echo "STARTING XML? $t:$tagname\n";
    if ($tagname == 'PHP') {
        $this->yyPhpBegin = $this->yy_buffer_start;
        $this->yybegin(IN_PHP);
        return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
    }
    // not php - it's xlm or something...
    // we treat this like a tag???
    // we are going to have to escape it eventually...!!!
    $this->tagName = trim(substr($t,1));
    $this->tokenName = 'Tag';
    $this->value = '';
    $this->attributes = array();
    $this->yybegin(IN_ATTR);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 14:
{ 
    /* ]]> -- marked section end */
    return $this->returnSimple();
}
case 15:
{
    if ($this->options['ignore_html']) {
        return $this->returnSimple();
    }
    /* </name <  -- unclosed end tag */
    return $this->raiseError("Unclosed  end tag");
}
case 16:
{
    /* <!--  -- comment declaration */
    if ($this->options['ignore_html']) {
        return $this->returnSimple();
    }
    if ($this->inStyle) {
        $this->value = $this->createToken('Comment');
        $this->yybegin(IN_COMSTYLE);
        return HTML_TEMPLATE_FLEXY_TOKEN_OK;
    }
    $this->yyCommentBegin = $this->yy_buffer_end;
    //$this->value = $this->createToken('Comment',$this->yytext(),$this->yyline);
    $this->yybegin(IN_COM);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 17:
{
    /* <![ -- marked section */
    $this->value = $this->createToken('Cdata',$this->yytext(), $this->yyline);
    $this->yybegin(IN_CDATA);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 18:
{
    $this->attrVal[] = $this->yytext();
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 19:
{
    return $this->raiseError("unexpected something: (".$this->yytext() .") character: 0x" . dechex(ord($this->yytext())));
}
case 20:
{
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 21:
{
    // <foo^<bar> -- unclosed start tag */
    return $this->raiseError("Unclosed tags not supported"); 
}
case 22:
{
    $this->value = $this->createToken($this->tokenName, array($this->tagName,$this->attributes));
    if (strtoupper($this->tagName) == 'SCRIPT') {
        $this->yybegin(IN_SCRIPT);
        return HTML_TEMPLATE_FLEXY_TOKEN_OK;
    }
    if (strtoupper($this->tagName) == 'STYLE') {
        $this->inStyle = true;
    } else {
        $this->inStyle = false;
    }
    $this->yybegin(YYINITIAL);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 23:
{
    // <img src="xxx" ...ismap...> the ismap */
    $this->attributes[trim($this->yytext())] = true;
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 24:
{
    // <em^/ -- NET tag */
    $this->yybegin(IN_NETDATA);
    $this->attributes["/"] = true;
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 25:
{
   // <a ^href = "xxx"> -- attribute name 
    $this->attrKey = substr(trim($this->yytext()),0,-1);
    $this->yybegin(IN_ATTRVAL);
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 26:
{
    // <em^/ -- NET tag */
    $this->attributes["/"] = true;
    $this->value = $this->createToken($this->tokenName, array($this->tagName,$this->attributes));
    $this->yybegin(YYINITIAL);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 27:
{
    // <em^/ -- NET tag */
    $this->attributes["?"] = true;
    $this->value = $this->createToken($this->tokenName, array($this->tagName,$this->attributes));
    $this->yybegin(YYINITIAL);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 28:
{
    // <a href = ^http://foo/> -- unquoted literal HACK */                          
    $this->attributes[$this->attrKey] = trim($this->yytext());
    $this->yybegin(IN_ATTR);
    //   $this->raiseError("attribute value needs quotes");
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 29:
{
    // <a name = ^12pt> -- number token */
    $this->attributes[$this->attrKey] = trim($this->yytext());
    $this->yybegin(IN_ATTR);
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 30:
{
    // <em^/ -- NET tag */
    return $this->raiseError("attribute value missing"); 
}
case 31:
{ 
    return $this->raiseError("Tag close found where attribute value expected"); 
}
case 32:
{
	//echo "STARTING SINGLEQUOTE";
    $this->attrVal = array( "'");
    $this->yybegin(IN_SINGLEQUOTE);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 33:
{
    //echo "START QUOTE";
    $this->attrVal =array("\"");
    $this->yybegin(IN_DOUBLEQUOTE);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 34:
{ 
    // whitespace switch back to IN_ATTR MODE.
    $this->value = '';
    $this->yybegin(IN_ATTR);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 35:
{ 
    return $this->raiseError("extraneous character in end tag"); 
}
case 36:
{ 
    $this->value = $this->createToken($this->tokenName, array($this->tagName));
        array($this->tagName);
    $this->yybegin(YYINITIAL);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 37:
{
    //echo "GOT DATA:".$this->yytext();
    $this->attrVal[] = $this->yytext();
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 38:
{ 
    $this->value = $this->createToken('WhiteSpace');
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 39:
{
    return $this->raiseError("illegal character in markup declaration (0x".dechex(ord($this->yytext())).')');
}
case 40:
{ 
    $this->value = $this->createToken('Name');
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 41:
{   
    $this->value = $this->createToken('CloseTag');
    $this->yybegin(YYINITIAL); 
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 42:
{
    // <!doctype foo ^[  -- declaration subset */
    $this->value = $this->createToken('BeginDS');
    $this->yybegin(IN_DS);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 43:
{ 
    $this->value = $this->createToken('NameT');
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 44:
{
    // <!entity ^% foo system "..." ...> -- parameter entity definition */
    $this->value = $this->createToken('EntityPar');
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 45:
{
    // <!doctype ^%foo;> -- parameter entity reference */
    $this->value = $this->createToken('EntityRef');
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 46:
{ 
    $this->value = $this->createToken('Literal');
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 47:
{
    // inside a comment (not - or not --
    // <!^--...-->   -- comment */   
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 48:
{ 
    $this->value = $this->createToken('Declaration');
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 49:
{ 
    // ] -- declaration subset close */
    $this->value = $this->createToken('DSEndSubset');
    $this->yybegin(IN_DSCOM); 
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 50:
{
    // ]]> -- marked section end */
     $this->value = $this->createToken('DSEnd');
    $this->yybegin(YYINITIAL);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 51:
{
    $t = $this->yytext();
    if ($t{strlen($t)-1} == ",") {
        // add argument
        $this->flexyArgs[] = substr($t,0,-1);
        return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
    }
    $this->flexyArgs[] = $t;
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 52:
{
    $t = $this->yytext();
    if ($t{strlen($t)-1} == ",") {
        // add argument
        $this->flexyArgs[] = substr($t,0,-1);
        return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
    }
    if ($c = strpos($t,':')) {
        $this->flexyMethod .= substr($t,$c,-1);
        $t = substr($t,0,$c-1);
    } else {
        $t = substr($t,0,-2);
    }
    $this->flexyArgs[] = $t;
    $this->value = $this->createToken('Method'  , array($this->flexyMethod,$this->flexyArgs));
    $this->yybegin(YYINITIAL);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 53:
{
    $t = $this->yytext();
    if ($t{1} == ':') {
        $this->flexyMethod .= substr($t,1,-1);
    }
    $this->value = $this->createToken('Method'  , array($this->flexyMethod,$this->flexyArgs));
    $this->yybegin(YYINITIAL);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 54:
{
    $t = $this->yytext();
    // add argument
    $this->flexyArgs[] = $t;
    $this->yybegin(IN_FLEXYMETHODQUOTED_END);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 55:
{
    $t = $this->yytext();
    $this->flexyArgs[] =$t;
    $this->yybegin(IN_FLEXYMETHODQUOTED_END);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 56:
{
    $t = $this->yytext();
    if ($p = strpos($t,':')) {
        $this->flexyMethod .= substr($t,$p,-1);
    }
    $this->attrVal[] = $this->createToken('Method'  , array($this->flexyMethod,$this->flexyArgs));
    $this->yybegin($this->flexyMethodState);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 57:
{
    $this->yybegin(IN_FLEXYMETHODQUOTED);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 58:
{
    // general text in script..
    $this->value = $this->createToken('Text');
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 59:
{
    // </script>
    $this->value = $this->createToken('EndTag', array('/script'));
    $this->yybegin(YYINITIAL);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 60:
{ 
    $this->value = $this->createToken('Cdata',$this->yytext(), $this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 61:
{ 
    /* ]]> -- marked section end */
    $this->value = $this->createToken('Cdata',$this->yytext(), $this->yyline);
    $this->yybegin(YYINITIAL);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 62:
{
    $t =  $this->yytext();
    $t = substr($t,1,-1);
    $this->value = $this->createToken('Var'  , $t);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 63:
{
    return $this->raiseError('invalid syntax for Foreach','',true);
}
case 64:
{
    $this->value = $this->createToken('Foreach', explode(',',substr($this->yytext(),9,-1)));
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 65:
{
    $this->value = $this->createToken('Foreach',  explode(',',substr($this->yytext(),9,-1)));
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 66:
{
    // inside a comment (not - or not --
    // <!^--...-->   -- comment */   
    $this->value = $this->createToken('DSComment');
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 67:
{     
    /* anything inside of php tags */
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 68:
{ 
    /* php end */
    $this->value = $this->createToken('Php',
        substr($this->yy_buffer,$this->yyPhpBegin ,$this->yy_buffer_end - $this->yyPhpBegin ),
        $this->yyline,$this->yyPhpBegin);
    $this->yybegin(YYINITIAL);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 69:
{
    // inside a style comment (not - or not --
    // <!^--...-->   -- comment */   
    $this->value = $this->createToken('Comment');
	return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 70:
{
    // we allow anything inside of comstyle!!!
    $this->value = $this->createToken('Comment');
	return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 71:
{
	// inside style comment -- without a >
    $this->value = $this->createToken('Comment');
	return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 72:
{
    // var in commented out style bit..
    $t =  $this->yytext();
    $t = substr($t,1,-1);
    $this->value = $this->createToken('Var', $t);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 74:
{
    //abcd -- data characters  
    // { and ) added for flexy
    $this->value = $this->createToken('Text');
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 75:
{
    // &abc;
    $this->value = $this->createToken('Text');
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 76:
{
    //<name -- start tag */
    if ($this->options['ignore_html']) {
        return $this->returnSimple();
    }
    $this->tagName = trim(substr($this->yytext(),1));
    $this->tokenName = 'Tag';
    $this->value = '';
    $this->attributes = array();
    $this->yybegin(IN_ATTR);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 77:
{ 
    /* <? php start.. */
    //echo "STARTING PHP?\n";
    $this->yyPhpBegin = $this->yy_buffer_start;
    $this->yybegin(IN_PHP);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 78:
{
    /* </title> -- end tag */
    if ($this->options['ignore_html']) {
        return $this->returnSimple();
    }
    if ($this->inStyle) {
        $this->inStyle = false;
    }
    $this->tagName = trim(substr($this->yytext(),1));
    $this->tokenName = 'EndTag';
    $this->yybegin(IN_ENDTAG);
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 79:
{
    /* <!DOCTYPE -- markup declaration */
    if ($this->options['ignore_html']) {
        return $this->returnSimple();
    }
    $this->value = $this->createToken('Doctype');
    $this->yybegin(IN_MD);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 80:
{ 
    /* eg. <?xml-stylesheet, <?php ... */
    $t = $this->yytext();
    $tagname = trim(strtoupper(substr($t,2)));
   // echo "STARTING XML? $t:$tagname\n";
    if ($tagname == 'PHP') {
        $this->yyPhpBegin = $this->yy_buffer_start;
        $this->yybegin(IN_PHP);
        return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
    }
    // not php - it's xlm or something...
    // we treat this like a tag???
    // we are going to have to escape it eventually...!!!
    $this->tagName = trim(substr($t,1));
    $this->tokenName = 'Tag';
    $this->value = '';
    $this->attributes = array();
    $this->yybegin(IN_ATTR);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 81:
{
    return $this->raiseError("unexpected something: (".$this->yytext() .") character: 0x" . dechex(ord($this->yytext())));
}
case 82:
{
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 83:
{
    // <foo^<bar> -- unclosed start tag */
    return $this->raiseError("Unclosed tags not supported"); 
}
case 84:
{
    // <img src="xxx" ...ismap...> the ismap */
    $this->attributes[trim($this->yytext())] = true;
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 85:
{
    // <a href = ^http://foo/> -- unquoted literal HACK */                          
    $this->attributes[$this->attrKey] = trim($this->yytext());
    $this->yybegin(IN_ATTR);
    //   $this->raiseError("attribute value needs quotes");
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 86:
{
    // <a name = ^12pt> -- number token */
    $this->attributes[$this->attrKey] = trim($this->yytext());
    $this->yybegin(IN_ATTR);
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 87:
{ 
    $this->value = $this->createToken('WhiteSpace');
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 88:
{
    return $this->raiseError("illegal character in markup declaration (0x".dechex(ord($this->yytext())).')');
}
case 89:
{ 
    $this->value = $this->createToken('Name');
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 90:
{ 
    $this->value = $this->createToken('NameT');
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 91:
{
    // <!doctype ^%foo;> -- parameter entity reference */
    $this->value = $this->createToken('EntityRef');
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 92:
{ 
    $this->value = $this->createToken('Literal');
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 93:
{
    // inside a comment (not - or not --
    // <!^--...-->   -- comment */   
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 94:
{
    $t = $this->yytext();
    if ($t{strlen($t)-1} == ",") {
        // add argument
        $this->flexyArgs[] = substr($t,0,-1);
        return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
    }
    $this->flexyArgs[] = $t;
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 95:
{
    $t = $this->yytext();
    // add argument
    $this->flexyArgs[] = $t;
    $this->yybegin(IN_FLEXYMETHODQUOTED_END);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 96:
{
    // general text in script..
    $this->value = $this->createToken('Text');
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 97:
{ 
    $this->value = $this->createToken('Cdata',$this->yytext(), $this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 98:
{
    // inside a comment (not - or not --
    // <!^--...-->   -- comment */   
    $this->value = $this->createToken('DSComment');
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 99:
{     
    /* anything inside of php tags */
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 100:
{
    // inside a style comment (not - or not --
    // <!^--...-->   -- comment */   
    $this->value = $this->createToken('Comment');
	return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 102:
{
    //abcd -- data characters  
    // { and ) added for flexy
    $this->value = $this->createToken('Text');
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 103:
{
    return $this->raiseError("unexpected something: (".$this->yytext() .") character: 0x" . dechex(ord($this->yytext())));
}
case 104:
{
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 105:
{
    // <a name = ^12pt> -- number token */
    $this->attributes[$this->attrKey] = trim($this->yytext());
    $this->yybegin(IN_ATTR);
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 106:
{
    return $this->raiseError("illegal character in markup declaration (0x".dechex(ord($this->yytext())).')');
}
case 107:
{ 
    $this->value = $this->createToken('Cdata',$this->yytext(), $this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 108:
{     
    /* anything inside of php tags */
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 109:
{
    // inside a style comment (not - or not --
    // <!^--...-->   -- comment */   
    $this->value = $this->createToken('Comment');
	return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 111:
{
    //abcd -- data characters  
    // { and ) added for flexy
    $this->value = $this->createToken('Text');
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 112:
{
    return $this->raiseError("unexpected something: (".$this->yytext() .") character: 0x" . dechex(ord($this->yytext())));
}
case 113:
{
    return $this->raiseError("illegal character in markup declaration (0x".dechex(ord($this->yytext())).')');
}
case 114:
{ 
    $this->value = $this->createToken('Cdata',$this->yytext(), $this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 115:
{
    // inside a style comment (not - or not --
    // <!^--...-->   -- comment */   
    $this->value = $this->createToken('Comment');
	return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 117:
{
    //abcd -- data characters  
    // { and ) added for flexy
    $this->value = $this->createToken('Text');
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 118:
{
    return $this->raiseError("unexpected something: (".$this->yytext() .") character: 0x" . dechex(ord($this->yytext())));
}
case 119:
{ 
    $this->value = $this->createToken('Cdata',$this->yytext(), $this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 120:
{
    // inside a style comment (not - or not --
    // <!^--...-->   -- comment */   
    $this->value = $this->createToken('Comment');
	return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 122:
{
    //abcd -- data characters  
    // { and ) added for flexy
    $this->value = $this->createToken('Text');
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 123:
{
    return $this->raiseError("unexpected something: (".$this->yytext() .") character: 0x" . dechex(ord($this->yytext())));
}
case 125:
{
    //abcd -- data characters  
    // { and ) added for flexy
    $this->value = $this->createToken('Text');
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 126:
{
    return $this->raiseError("unexpected something: (".$this->yytext() .") character: 0x" . dechex(ord($this->yytext())));
}
case 128:
{
    //abcd -- data characters  
    // { and ) added for flexy
    $this->value = $this->createToken('Text');
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 129:
{
    return $this->raiseError("unexpected something: (".$this->yytext() .") character: 0x" . dechex(ord($this->yytext())));
}
case 131:
{
    //abcd -- data characters  
    // { and ) added for flexy
    $this->value = $this->createToken('Text');
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 132:
{
    return $this->raiseError("unexpected something: (".$this->yytext() .") character: 0x" . dechex(ord($this->yytext())));
}
case 134:
{
    //abcd -- data characters  
    // { and ) added for flexy
    $this->value = $this->createToken('Text');
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 136:
{
    //abcd -- data characters  
    // { and ) added for flexy
    $this->value = $this->createToken('Text');
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 210:
{
    $t = $this->yytext();
    // add argument
    $this->flexyArgs[] = $t;
    $this->yybegin(IN_FLEXYMETHODQUOTED_END);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 211:
{
    // general text in script..
    $this->value = $this->createToken('Text');
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 212:
{ 
    $this->value = $this->createToken('Cdata',$this->yytext(), $this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 213:
{
    // we allow anything inside of comstyle!!!
    $this->value = $this->createToken('Comment');
	return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 214:
{
    //abcd -- data characters  
    // { and ) added for flexy
    $this->value = $this->createToken('Text');
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 215:
{
    $t = $this->yytext();
    // add argument
    $this->flexyArgs[] = $t;
    $this->yybegin(IN_FLEXYMETHODQUOTED_END);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 216:
{
    // inside a style comment (not - or not --
    // <!^--...-->   -- comment */   
    $this->value = $this->createToken('Comment');
	return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 217:
{ 
    $this->value = $this->createToken('Cdata',$this->yytext(), $this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 218:
{ 
    $this->value = $this->createToken('Cdata',$this->yytext(), $this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 219:
{
    // inside a style comment (not - or not --
    // <!^--...-->   -- comment */   
    $this->value = $this->createToken('Comment');
	return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 231:
{ 
    $this->value = $this->createToken('Cdata',$this->yytext(), $this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 232:
{
    // we allow anything inside of comstyle!!!
    $this->value = $this->createToken('Comment');
	return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 233:
{
    //abcd -- data characters  
    // { and ) added for flexy
    $this->value = $this->createToken('Text');
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 234:
{
    // inside a style comment (not - or not --
    // <!^--...-->   -- comment */   
    $this->value = $this->createToken('Comment');
	return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 235:
{ 
    $this->value = $this->createToken('Cdata',$this->yytext(), $this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 236:
{ 
    $this->value = $this->createToken('Cdata',$this->yytext(), $this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 237:
{
    // inside a style comment (not - or not --
    // <!^--...-->   -- comment */   
    $this->value = $this->createToken('Comment');
	return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 241:
{ 
    $this->value = $this->createToken('Cdata',$this->yytext(), $this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 242:
{
    //abcd -- data characters  
    // { and ) added for flexy
    $this->value = $this->createToken('Text');
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 243:
{
    // inside a style comment (not - or not --
    // <!^--...-->   -- comment */   
    $this->value = $this->createToken('Comment');
	return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 244:
{ 
    $this->value = $this->createToken('Cdata',$this->yytext(), $this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 245:
{ 
    $this->value = $this->createToken('Cdata',$this->yytext(), $this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 249:
{ 
    $this->value = $this->createToken('Cdata',$this->yytext(), $this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 250:
{
    // inside a style comment (not - or not --
    // <!^--...-->   -- comment */   
    $this->value = $this->createToken('Comment');
	return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 253:
{ 
    $this->value = $this->createToken('Cdata',$this->yytext(), $this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 254:
{
    // inside a style comment (not - or not --
    // <!^--...-->   -- comment */   
    $this->value = $this->createToken('Comment');
	return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 257:
{ 
    $this->value = $this->createToken('Cdata',$this->yytext(), $this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 260:
{ 
    $this->value = $this->createToken('Cdata',$this->yytext(), $this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 263:
{ 
    $this->value = $this->createToken('Cdata',$this->yytext(), $this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 264:
{ 
    $this->value = $this->createToken('Cdata',$this->yytext(), $this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 265:
{ 
    $this->value = $this->createToken('Cdata',$this->yytext(), $this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 266:
{ 
    $this->value = $this->createToken('Cdata',$this->yytext(), $this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 267:
{ 
    $this->value = $this->createToken('Cdata',$this->yytext(), $this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 268:
{ 
    $this->value = $this->createToken('Cdata',$this->yytext(), $this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 269:
{ 
    $this->value = $this->createToken('Cdata',$this->yytext(), $this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 270:
{ 
    $this->value = $this->createToken('Cdata',$this->yytext(), $this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 271:
{ 
    $this->value = $this->createToken('Cdata',$this->yytext(), $this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 272:
{
    // general text in script..
    $this->value = $this->createToken('Text');
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 273:
{ 
    $this->value = $this->createToken('Cdata',$this->yytext(), $this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 274:
{
    //abcd -- data characters  
    // { and ) added for flexy
    $this->value = $this->createToken('Text');
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 275:
{
    // inside a style comment (not - or not --
    // <!^--...-->   -- comment */   
    $this->value = $this->createToken('Comment');
	return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 278:
{ 
    $this->value = $this->createToken('Cdata',$this->yytext(), $this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 279:
{
    // inside a style comment (not - or not --
    // <!^--...-->   -- comment */   
    $this->value = $this->createToken('Comment');
	return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 281:
{ 
    $this->value = $this->createToken('Cdata',$this->yytext(), $this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 282:
{
    // inside a style comment (not - or not --
    // <!^--...-->   -- comment */   
    $this->value = $this->createToken('Comment');
	return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 284:
{ 
    $this->value = $this->createToken('Cdata',$this->yytext(), $this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 286:
{ 
    $this->value = $this->createToken('Cdata',$this->yytext(), $this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 288:
{ 
    $this->value = $this->createToken('Cdata',$this->yytext(), $this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 290:
{ 
    $this->value = $this->createToken('Cdata',$this->yytext(), $this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 291:
{ 
    $this->value = $this->createToken('Cdata',$this->yytext(), $this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 292:
{ 
    $this->value = $this->createToken('Cdata',$this->yytext(), $this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 293:
{ 
    $this->value = $this->createToken('Cdata',$this->yytext(), $this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 294:
{ 
    $this->value = $this->createToken('Cdata',$this->yytext(), $this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 295:
{ 
    $this->value = $this->createToken('Cdata',$this->yytext(), $this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 296:
{
    // general text in script..
    $this->value = $this->createToken('Text');
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 297:
{ 
    $this->value = $this->createToken('Cdata',$this->yytext(), $this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 298:
{
    //abcd -- data characters  
    // { and ) added for flexy
    $this->value = $this->createToken('Text');
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 300:
{ 
    $this->value = $this->createToken('Cdata',$this->yytext(), $this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 301:
{ 
    $this->value = $this->createToken('Cdata',$this->yytext(), $this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 302:
{ 
    $this->value = $this->createToken('Cdata',$this->yytext(), $this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 303:
{ 
    $this->value = $this->createToken('Cdata',$this->yytext(), $this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 304:
{ 
    $this->value = $this->createToken('Cdata',$this->yytext(), $this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 305:
{ 
    $this->value = $this->createToken('Cdata',$this->yytext(), $this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 306:
{ 
    $this->value = $this->createToken('Cdata',$this->yytext(), $this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 307:
{
    // general text in script..
    $this->value = $this->createToken('Text');
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 308:
{ 
    $this->value = $this->createToken('Cdata',$this->yytext(), $this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 309:
{
    //abcd -- data characters  
    // { and ) added for flexy
    $this->value = $this->createToken('Text');
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 310:
{
    /* <![ -- marked section */
    return $this->returnSimple();
}
case 311:
{
    // general text in script..
    $this->value = $this->createToken('Text');
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 312:
{ 
    $this->value = $this->createToken('Cdata',$this->yytext(), $this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 313:
{
    // general text in script..
    $this->value = $this->createToken('Text');
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 314:
{ 
    $this->value = $this->createToken('Cdata',$this->yytext(), $this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 315:
{
    // general text in script..
    $this->value = $this->createToken('Text');
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 316:
{ 
    $this->value = $this->createToken('Cdata',$this->yytext(), $this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 317:
{ 
    $this->value = $this->createToken('Cdata',$this->yytext(), $this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 318:
{ 
    $this->value = $this->createToken('Cdata',$this->yytext(), $this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 319:
{ 
    $this->value = $this->createToken('Cdata',$this->yytext(), $this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}

                        }
                    }
                    if ($this->_fatal) {
                        return;
                    }
                    $yy_initial = true;
                    $yy_state = $this->yy_state_dtrans[$this->yy_lexical_state];
                    $yy_next_state = YY_NO_STATE;
                    $yy_last_accept_state = YY_NO_STATE;
                    $this->yy_mark_start();
                    $yy_this_accept = $this->yy_acpt[$yy_state];
                    if (YY_NOT_ACCEPT != $yy_this_accept) {
                        $yy_last_accept_state = $yy_state;
                        $this->yy_buffer_end = $this->yy_buffer_index;
                    }
                }
            }
        }
        return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
    }
}
