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
// The lexer is available at http://sourceforge.net/projects/php-sharp/
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
    * ignoreHTML flag
    *
    * @var      boolean  public
    * @access   public
    */
    var $ignoreHTML = false;
    /**
    * ignorePHP flag - default is to remove all PHP code from template.
    * although this may not produce a tidy result - eg. close ?> in comments
    * it will have the desired effect of blocking injection of PHP from templates.
    *
    * @var      boolean  public
    * @access   public
    */
    var $ignorePHP = true;
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
    function raiseError($s,$n='',$isFatal=false) {
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
    function returnSimple() {
        $this->value = HTML_Template_Flexy_Token::factory('TextSimple',$this->yytext(),$this->yyline);
        return HTML_TEMPLATE_FLEXY_TOKEN_OK;
    }


    var $yy_reader;
    var $yy_buffer_index;
    var $yy_buffer_read;
    var $yy_buffer_start;
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
        210,
        35,
        122,
        232,
        233,
        234,
        235,
        53,
        64,
        242,
        244,
        263,
        277,
        278,
        286,
        82,
        84
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
        "Error: Unmatched input.\n"
        );


    function yy_error ($code,$fatal)
    {
        if (method_exists($this,'raiseError')) { 
 	    return $this->raiseError($code, $this->yy_error_string[$code], $fatal); 
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
        /* 73 */   YY_NO_ANCHOR,
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
        /* 86 */   YY_NOT_ACCEPT,
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
        /* 101 */   YY_NO_ANCHOR,
        /* 102 */   YY_NO_ANCHOR,
        /* 103 */   YY_NO_ANCHOR,
        /* 104 */   YY_NO_ANCHOR,
        /* 105 */   YY_NO_ANCHOR,
        /* 106 */   YY_NO_ANCHOR,
        /* 107 */   YY_NO_ANCHOR,
        /* 108 */   YY_NO_ANCHOR,
        /* 109 */   YY_NO_ANCHOR,
        /* 110 */   YY_NO_ANCHOR,
        /* 111 */   YY_NO_ANCHOR,
        /* 112 */   YY_NO_ANCHOR,
        /* 113 */   YY_NO_ANCHOR,
        /* 114 */   YY_NO_ANCHOR,
        /* 115 */   YY_NO_ANCHOR,
        /* 116 */   YY_NO_ANCHOR,
        /* 117 */   YY_NO_ANCHOR,
        /* 118 */   YY_NOT_ACCEPT,
        /* 119 */   YY_NO_ANCHOR,
        /* 120 */   YY_NO_ANCHOR,
        /* 121 */   YY_NO_ANCHOR,
        /* 122 */   YY_NO_ANCHOR,
        /* 123 */   YY_NO_ANCHOR,
        /* 124 */   YY_NO_ANCHOR,
        /* 125 */   YY_NO_ANCHOR,
        /* 126 */   YY_NO_ANCHOR,
        /* 127 */   YY_NO_ANCHOR,
        /* 128 */   YY_NOT_ACCEPT,
        /* 129 */   YY_NO_ANCHOR,
        /* 130 */   YY_NO_ANCHOR,
        /* 131 */   YY_NO_ANCHOR,
        /* 132 */   YY_NOT_ACCEPT,
        /* 133 */   YY_NO_ANCHOR,
        /* 134 */   YY_NO_ANCHOR,
        /* 135 */   YY_NOT_ACCEPT,
        /* 136 */   YY_NO_ANCHOR,
        /* 137 */   YY_NOT_ACCEPT,
        /* 138 */   YY_NO_ANCHOR,
        /* 139 */   YY_NOT_ACCEPT,
        /* 140 */   YY_NO_ANCHOR,
        /* 141 */   YY_NOT_ACCEPT,
        /* 142 */   YY_NO_ANCHOR,
        /* 143 */   YY_NOT_ACCEPT,
        /* 144 */   YY_NO_ANCHOR,
        /* 145 */   YY_NOT_ACCEPT,
        /* 146 */   YY_NO_ANCHOR,
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
        /* 210 */   YY_NOT_ACCEPT,
        /* 211 */   YY_NOT_ACCEPT,
        /* 212 */   YY_NOT_ACCEPT,
        /* 213 */   YY_NOT_ACCEPT,
        /* 214 */   YY_NOT_ACCEPT,
        /* 215 */   YY_NOT_ACCEPT,
        /* 216 */   YY_NOT_ACCEPT,
        /* 217 */   YY_NOT_ACCEPT,
        /* 218 */   YY_NOT_ACCEPT,
        /* 219 */   YY_NOT_ACCEPT,
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
        /* 231 */   YY_NOT_ACCEPT,
        /* 232 */   YY_NOT_ACCEPT,
        /* 233 */   YY_NOT_ACCEPT,
        /* 234 */   YY_NOT_ACCEPT,
        /* 235 */   YY_NOT_ACCEPT,
        /* 236 */   YY_NOT_ACCEPT,
        /* 237 */   YY_NOT_ACCEPT,
        /* 238 */   YY_NOT_ACCEPT,
        /* 239 */   YY_NOT_ACCEPT,
        /* 240 */   YY_NOT_ACCEPT,
        /* 241 */   YY_NOT_ACCEPT,
        /* 242 */   YY_NOT_ACCEPT,
        /* 243 */   YY_NOT_ACCEPT,
        /* 244 */   YY_NOT_ACCEPT,
        /* 245 */   YY_NOT_ACCEPT,
        /* 246 */   YY_NOT_ACCEPT,
        /* 247 */   YY_NOT_ACCEPT,
        /* 248 */   YY_NOT_ACCEPT,
        /* 249 */   YY_NOT_ACCEPT,
        /* 250 */   YY_NOT_ACCEPT,
        /* 251 */   YY_NOT_ACCEPT,
        /* 252 */   YY_NOT_ACCEPT,
        /* 253 */   YY_NOT_ACCEPT,
        /* 254 */   YY_NOT_ACCEPT,
        /* 255 */   YY_NOT_ACCEPT,
        /* 256 */   YY_NOT_ACCEPT,
        /* 257 */   YY_NOT_ACCEPT,
        /* 258 */   YY_NOT_ACCEPT,
        /* 259 */   YY_NOT_ACCEPT,
        /* 260 */   YY_NOT_ACCEPT,
        /* 261 */   YY_NOT_ACCEPT,
        /* 262 */   YY_NOT_ACCEPT,
        /* 263 */   YY_NOT_ACCEPT,
        /* 264 */   YY_NOT_ACCEPT,
        /* 265 */   YY_NOT_ACCEPT,
        /* 266 */   YY_NOT_ACCEPT,
        /* 267 */   YY_NOT_ACCEPT,
        /* 268 */   YY_NOT_ACCEPT,
        /* 269 */   YY_NOT_ACCEPT,
        /* 270 */   YY_NOT_ACCEPT,
        /* 271 */   YY_NOT_ACCEPT,
        /* 272 */   YY_NOT_ACCEPT,
        /* 273 */   YY_NOT_ACCEPT,
        /* 274 */   YY_NOT_ACCEPT,
        /* 275 */   YY_NOT_ACCEPT,
        /* 276 */   YY_NOT_ACCEPT,
        /* 277 */   YY_NOT_ACCEPT,
        /* 278 */   YY_NOT_ACCEPT,
        /* 279 */   YY_NOT_ACCEPT,
        /* 280 */   YY_NOT_ACCEPT,
        /* 281 */   YY_NOT_ACCEPT,
        /* 282 */   YY_NOT_ACCEPT,
        /* 283 */   YY_NOT_ACCEPT,
        /* 284 */   YY_NOT_ACCEPT,
        /* 285 */   YY_NOT_ACCEPT,
        /* 286 */   YY_NOT_ACCEPT,
        /* 287 */   YY_NOT_ACCEPT,
        /* 288 */   YY_NOT_ACCEPT,
        /* 289 */   YY_NOT_ACCEPT,
        /* 290 */   YY_NO_ANCHOR,
        /* 291 */   YY_NO_ANCHOR,
        /* 292 */   YY_NO_ANCHOR,
        /* 293 */   YY_NO_ANCHOR,
        /* 294 */   YY_NOT_ACCEPT,
        /* 295 */   YY_NOT_ACCEPT,
        /* 296 */   YY_NOT_ACCEPT,
        /* 297 */   YY_NOT_ACCEPT,
        /* 298 */   YY_NOT_ACCEPT,
        /* 299 */   YY_NOT_ACCEPT,
        /* 300 */   YY_NOT_ACCEPT,
        /* 301 */   YY_NOT_ACCEPT,
        /* 302 */   YY_NOT_ACCEPT,
        /* 303 */   YY_NOT_ACCEPT,
        /* 304 */   YY_NOT_ACCEPT,
        /* 305 */   YY_NOT_ACCEPT,
        /* 306 */   YY_NOT_ACCEPT,
        /* 307 */   YY_NOT_ACCEPT,
        /* 308 */   YY_NOT_ACCEPT,
        /* 309 */   YY_NOT_ACCEPT,
        /* 310 */   YY_NOT_ACCEPT,
        /* 311 */   YY_NOT_ACCEPT,
        /* 312 */   YY_NOT_ACCEPT,
        /* 313 */   YY_NOT_ACCEPT,
        /* 314 */   YY_NOT_ACCEPT,
        /* 315 */   YY_NOT_ACCEPT,
        /* 316 */   YY_NOT_ACCEPT,
        /* 317 */   YY_NOT_ACCEPT,
        /* 318 */   YY_NOT_ACCEPT,
        /* 319 */   YY_NOT_ACCEPT,
        /* 320 */   YY_NOT_ACCEPT,
        /* 321 */   YY_NOT_ACCEPT,
        /* 322 */   YY_NOT_ACCEPT,
        /* 323 */   YY_NOT_ACCEPT,
        /* 324 */   YY_NOT_ACCEPT,
        /* 325 */   YY_NOT_ACCEPT,
        /* 326 */   YY_NOT_ACCEPT,
        /* 327 */   YY_NOT_ACCEPT,
        /* 328 */   YY_NOT_ACCEPT,
        /* 329 */   YY_NOT_ACCEPT,
        /* 330 */   YY_NOT_ACCEPT,
        /* 331 */   YY_NOT_ACCEPT,
        /* 332 */   YY_NOT_ACCEPT,
        /* 333 */   YY_NOT_ACCEPT,
        /* 334 */   YY_NOT_ACCEPT,
        /* 335 */   YY_NOT_ACCEPT,
        /* 336 */   YY_NOT_ACCEPT,
        /* 337 */   YY_NOT_ACCEPT,
        /* 338 */   YY_NOT_ACCEPT,
        /* 339 */   YY_NOT_ACCEPT,
        /* 340 */   YY_NOT_ACCEPT
        );


    var  $yy_cmap = array(
        34, 34, 34, 34, 34, 34, 34, 34,
        34, 11, 5, 34, 34, 12, 34, 34,
        34, 34, 34, 34, 34, 34, 34, 34,
        34, 34, 34, 34, 34, 34, 34, 34,
        11, 14, 33, 2, 35, 28, 1, 32,
        46, 21, 35, 35, 56, 15, 7, 9,
        3, 3, 3, 3, 3, 49, 3, 58,
        3, 3, 10, 4, 8, 31, 13, 23,
        34, 19, 50, 17, 18, 6, 6, 6,
        6, 41, 6, 6, 26, 25, 6, 6,
        43, 6, 40, 36, 20, 6, 6, 6,
        24, 6, 6, 16, 29, 22, 34, 45,
        34, 54, 50, 38, 51, 53, 48, 6,
        55, 42, 6, 6, 27, 25, 57, 52,
        43, 6, 39, 37, 44, 6, 6, 6,
        24, 6, 6, 30, 34, 47, 34, 34,
        34, 34, 34, 34, 34, 34, 34, 34,
        34, 34, 34, 34, 34, 34, 34, 34,
        34, 34, 34, 34, 34, 34, 34, 34,
        34, 34, 34, 34, 34, 34, 34, 34,
        34, 34, 34, 34, 34, 34, 34, 34,
        34, 34, 34, 34, 34, 34, 34, 34,
        34, 34, 34, 34, 34, 34, 34, 34,
        34, 34, 34, 34, 34, 34, 34, 34,
        34, 34, 34, 34, 34, 34, 34, 34,
        34, 34, 34, 34, 34, 34, 34, 34,
        34, 34, 34, 34, 34, 34, 34, 34,
        34, 34, 34, 34, 34, 34, 34, 34,
        34, 34, 34, 34, 34, 34, 34, 34,
        34, 34, 34, 34, 34, 34, 34, 34,
        34, 34, 34, 34, 34, 34, 34, 34,
        34, 34, 34, 34, 34, 34, 34, 34,
        34, 0, 0 
         );


    var $yy_rmap = array(
        0, 1, 2, 3, 4, 5, 1, 6,
        7, 8, 9, 1, 10, 1, 11, 1,
        3, 1, 1, 1, 1, 1, 1, 1,
        1, 1, 1, 1, 1, 1, 1, 12,
        1, 1, 1, 13, 1, 1, 14, 15,
        16, 1, 17, 18, 17, 1, 1, 1,
        19, 1, 1, 20, 1, 21, 1, 22,
        23, 24, 1, 1, 25, 26, 27, 28,
        29, 1, 1, 30, 31, 1, 32, 1,
        1, 33, 1, 1, 1, 34, 35, 1,
        36, 1, 37, 1, 38, 1, 39, 40,
        41, 1, 42, 1, 1, 43, 44, 45,
        46, 47, 17, 48, 49, 50, 46, 51,
        52, 53, 54, 55, 56, 57, 1, 58,
        59, 1, 60, 61, 62, 63, 64, 65,
        66, 67, 68, 69, 67, 70, 71, 1,
        72, 1, 73, 74, 75, 76, 77, 78,
        79, 80, 81, 82, 83, 84, 85, 86,
        87, 88, 89, 90, 91, 92, 93, 94,
        95, 96, 97, 98, 99, 100, 101, 102,
        103, 104, 105, 106, 107, 108, 109, 110,
        111, 112, 113, 114, 115, 116, 117, 118,
        119, 120, 121, 122, 123, 124, 125, 126,
        127, 128, 129, 130, 131, 132, 133, 134,
        135, 136, 137, 138, 139, 140, 141, 142,
        143, 144, 145, 146, 147, 148, 149, 150,
        151, 152, 153, 65, 154, 155, 156, 157,
        158, 159, 160, 161, 162, 163, 164, 165,
        166, 167, 168, 169, 170, 171, 172, 15,
        173, 174, 175, 176, 76, 70, 74, 177,
        178, 59, 179, 180, 181, 79, 81, 182,
        183, 184, 185, 186, 187, 188, 189, 190,
        191, 192, 193, 194, 195, 196, 197, 198,
        85, 199, 200, 201, 202, 203, 204, 205,
        206, 207, 208, 209, 210, 211, 212, 213,
        214, 215, 216, 217, 218, 219, 220, 221,
        222, 223, 224, 69, 225, 226, 227, 67,
        107, 228, 229, 230, 231, 232, 233, 118,
        234, 235, 127, 236, 237, 139, 238, 143,
        239, 158, 240, 164, 241, 183, 242, 189,
        243, 200, 244, 206, 245, 246, 247, 248,
        249, 250, 251, 252, 253, 254, 255, 256,
        257, 258, 259, 260, 261 
        );


    var $yy_nxt = array(
        array( 1, 2, 3, 3, 3, 3, 3, 3,
            87, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 88, 290, 3,
            3, 3, 3, 3, 3, 3, 120, 3,
            3, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, 86, 3, 3, 3, 4, 3,
            -1, 3, 3, 3, 3, 3, 3, 3,
            3, 4, 4, 4, 4, 3, 3, 3,
            4, 4, 4, 4, 3, 3, 3, 3,
            3, 3, 3, 3, 4, 4, 4, 4,
            4, 4, 4, 4, 4, 3, 3, 3,
            4, 3, 4, 4, 4, 4, 4, 4,
            3, 4, 3 ),
        array( -1, 118, 3, 3, 3, 3, 3, 3,
            128, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, -1, 3, 3,
            3, 3, 3, 3, 3, 3, -1, 3,
            3, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3 ),
        array( -1, -1, -1, 4, 89, 89, 4, 4,
            -1, -1, -1, -1, -1, -1, -1, 4,
            -1, 4, 4, 4, 4, -1, -1, -1,
            4, 4, 4, 4, -1, -1, -1, -1,
            -1, -1, -1, -1, 4, 4, 4, 4,
            4, 4, 4, 4, 4, -1, -1, -1,
            4, 4, 4, 4, 4, 4, 4, 4,
            -1, 4, 4 ),
        array( -1, -1, -1, 5, -1, 90, 5, 5,
            -1, -1, 5, 90, 90, -1, -1, 5,
            -1, 5, 5, 5, 5, -1, -1, -1,
            5, 5, 5, 5, -1, -1, -1, -1,
            -1, -1, -1, -1, 5, 5, 5, 5,
            5, 5, 5, 5, 5, -1, -1, -1,
            5, 5, 5, 5, 5, 5, 5, 5,
            -1, 5, 5 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            148, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, 8, 91, 91, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 8, -1, -1, -1, -1, -1, -1,
            -1, -1, 8 ),
        array( -1, -1, -1, 9, 92, 92, 9, 9,
            -1, -1, -1, -1, -1, -1, -1, 9,
            -1, 9, 9, 9, 9, -1, -1, -1,
            9, 9, 9, 9, -1, -1, -1, -1,
            -1, -1, -1, -1, 9, 9, 9, 9,
            9, 9, 9, 9, 9, -1, -1, -1,
            9, 9, 9, 9, 9, 9, 9, 9,
            -1, 9, 9 ),
        array( -1, -1, -1, 10, -1, 93, 10, 10,
            -1, 145, 10, 93, 93, -1, -1, 10,
            -1, 10, 10, 10, 10, -1, -1, -1,
            10, 10, 10, 10, -1, -1, -1, -1,
            -1, -1, -1, -1, 10, 10, 10, 10,
            10, 10, 10, 10, 10, -1, -1, -1,
            10, 10, 10, 10, 10, 10, 10, 10,
            -1, 10, 10 ),
        array( -1, -1, -1, 12, -1, 94, 12, 12,
            -1, -1, -1, 94, 94, -1, -1, 12,
            -1, 12, 12, 12, 12, -1, -1, -1,
            12, 12, 12, 12, -1, -1, -1, -1,
            -1, -1, -1, -1, 12, 12, 12, 12,
            12, 12, 12, 12, 12, -1, -1, -1,
            12, 12, 12, 12, 12, 12, 12, 12,
            -1, 12, 12 ),
        array( -1, -1, -1, -1, -1, 95, -1, -1,
            -1, -1, -1, 95, 95, -1, -1, -1,
            -1, 154, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, 31, 31, 31, 31, 31, 31, 31,
            31, 31, 31, 31, 31, 31, 31, 31,
            31, 31, 31, 31, 31, 31, 31, 31,
            31, 31, 31, 31, -1, 211, -1, 31,
            -1, 31, 31, 31, 31, 31, 31, 31,
            31, 31, 31, 31, 31, 31, 31, 31,
            31, 31, 31, 31, 31, 31, 31, 31,
            31, 31, 31 ),
        array( 1, 129, 129, 129, 129, 97, 129, 129,
            36, 129, 129, 97, 97, 37, 129, 129,
            129, 129, 129, 129, 129, 129, 129, 129,
            129, 129, 129, 129, 129, 129, 129, 129,
            129, 129, 129, 129, 129, 129, 129, 129,
            129, 129, 129, 129, 129, 129, 129, 129,
            129, 129, 129, 129, 129, 129, 129, 129,
            129, 129, 129 ),
        array( -1, -1, -1, 38, -1, 99, 38, 38,
            -1, -1, 38, 99, 99, -1, -1, 38,
            -1, 38, 38, 38, 38, -1, -1, -1,
            38, 38, 38, 38, -1, -1, -1, 40,
            -1, -1, -1, -1, 38, 38, 38, 38,
            38, 38, 38, 38, 38, -1, -1, -1,
            38, 38, 38, 38, 38, 38, 38, 38,
            -1, 38, 38 ),
        array( -1, -1, -1, -1, -1, 231, -1, -1,
            -1, -1, -1, 231, 231, 41, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 40, -1, -1,
            -1, -1, -1, 40, 40, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, 42, 42, 42, 42, 100, 42, 42,
            42, 42, 42, 100, 100, -1, 42, 42,
            42, 42, 42, 42, 42, 42, 42, 42,
            42, 42, 42, 42, 42, 42, 42, 42,
            -1, -1, 42, 42, 42, 42, 42, 42,
            42, 42, 42, 42, 42, 42, 42, 42,
            42, 42, 42, 42, 42, 42, 42, 42,
            42, 42, 42 ),
        array( -1, 42, 42, 43, 42, 101, 43, 43,
            42, 42, 42, 101, 101, -1, 42, 43,
            42, 43, 43, 43, 43, 42, 42, 42,
            43, 43, 43, 43, 42, 42, 42, 42,
            -1, -1, 42, 42, 43, 43, 43, 43,
            43, 43, 43, 43, 43, 42, 42, 42,
            43, 43, 43, 43, 43, 43, 43, 43,
            42, 43, 43 ),
        array( -1, -1, -1, -1, -1, 48, -1, -1,
            -1, -1, -1, 48, 48, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, 51, 51, 51, 51, 51, 51, 51,
            51, 51, 51, 51, 51, 51, 51, 51,
            51, 51, 51, 51, 51, 51, 51, 51,
            51, 51, 51, 51, -1, 236, -1, 51,
            51, -1, 51, 51, 51, 51, 51, 51,
            51, 51, 51, 51, 51, 51, 51, 51,
            51, 51, 51, 51, 51, 51, 51, 51,
            51, 51, 51 ),
        array( 1, 54, 54, 55, 54, 103, 56, 57,
            54, 54, 54, 103, 103, 58, 54, 57,
            59, 56, 56, 56, 56, 54, 54, 54,
            56, 56, 56, 56, 104, 54, 54, 54,
            125, 131, 54, 54, 56, 56, 56, 56,
            56, 56, 56, 56, 56, 54, 54, 54,
            56, 55, 56, 56, 56, 56, 56, 56,
            54, 56, 55 ),
        array( -1, -1, -1, 55, -1, 105, 60, 60,
            -1, -1, -1, 105, 105, -1, -1, 60,
            -1, 60, 60, 60, 60, -1, -1, -1,
            60, 60, 60, 60, -1, -1, -1, -1,
            -1, -1, -1, -1, 60, 60, 60, 60,
            60, 60, 60, 60, 60, -1, -1, -1,
            60, 55, 60, 60, 60, 60, 60, 60,
            -1, 60, 55 ),
        array( -1, -1, -1, 56, -1, 106, 56, 56,
            -1, -1, -1, 106, 106, -1, -1, 56,
            -1, 56, 56, 56, 56, -1, -1, -1,
            56, 56, 56, 56, -1, -1, -1, -1,
            -1, -1, -1, -1, 56, 56, 56, 56,
            56, 56, 56, 56, 56, -1, -1, -1,
            56, 56, 56, 56, 56, 56, 56, 56,
            -1, 56, 56 ),
        array( -1, -1, -1, 57, -1, 107, 57, 57,
            -1, -1, -1, 107, 107, -1, -1, 57,
            -1, 57, 57, 57, 57, -1, -1, -1,
            57, 57, 57, 57, -1, -1, -1, -1,
            -1, -1, -1, -1, 57, 57, 57, 57,
            57, 57, 57, 57, 57, -1, -1, -1,
            57, 57, 57, 57, 57, 57, 57, 57,
            -1, 57, 57 ),
        array( -1, -1, -1, 60, -1, 108, 60, 60,
            -1, -1, -1, 108, 108, -1, -1, 60,
            -1, 60, 60, 60, 60, -1, -1, -1,
            60, 60, 60, 60, -1, -1, -1, -1,
            -1, -1, -1, -1, 60, 60, 60, 60,
            60, 60, 60, 60, 60, -1, -1, -1,
            60, 60, 60, 60, 60, 60, 60, 60,
            -1, 60, 60 ),
        array( -1, -1, -1, -1, -1, 61, -1, -1,
            -1, -1, -1, 61, 61, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, 62, 109, 109, 62, 62,
            -1, -1, -1, 109, 109, -1, -1, 62,
            -1, 62, 62, 62, 62, -1, -1, -1,
            62, 62, 62, 62, -1, -1, -1, -1,
            -1, -1, -1, -1, 62, 62, 62, 62,
            62, 62, 62, 62, 62, -1, -1, -1,
            62, 62, 62, 62, 62, 62, 62, 62,
            -1, 62, 62 ),
        array( -1, -1, -1, -1, -1, 63, -1, -1,
            -1, -1, -1, 63, 63, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( 1, 111, 111, 111, 111, 111, 111, 111,
            111, 111, 111, 111, 111, 111, 111, 134,
            111, 111, 111, 111, 111, 111, 111, 111,
            111, 111, 111, 111, 111, 111, 111, 111,
            111, 111, 111, 111, 111, 111, 111, 111,
            111, 111, 111, 111, 111, 111, 111, 111,
            111, 111, 111, 111, 111, 111, 111, 111,
            111, 111, 111 ),
        array( -1, 67, 67, 67, 67, 67, 67, 67,
            67, 67, 67, 67, 67, 67, 67, 67,
            67, 67, 67, 67, 67, 67, -1, 67,
            67, 67, 67, 67, 67, 67, 67, 67,
            67, 67, 67, 67, 67, 67, 67, 67,
            67, 67, 67, 67, 67, 67, 67, 67,
            67, 67, 67, 67, 67, 67, 67, 67,
            67, 67, 67 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 243, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            113, -1, -1 ),
        array( -1, -1, -1, 73, -1, -1, 73, 265,
            -1, -1, -1, -1, -1, -1, -1, -1,
            266, 73, 73, 73, 73, -1, -1, -1,
            73, 73, 73, 73, 335, -1, -1, -1,
            -1, -1, -1, -1, 73, 73, 73, 73,
            73, 73, 73, 73, 73, 73, -1, -1,
            73, 73, 73, 73, 73, 73, 73, 73,
            -1, 73, 73 ),
        array( -1, 77, 77, 77, 77, 77, 77, 77,
            -1, 77, 77, 77, 77, 77, 77, 77,
            77, 77, 77, 77, 77, 77, 77, 77,
            77, 77, 77, 77, 77, 77, 77, 77,
            77, 77, 77, 77, 77, 77, 77, 77,
            77, 77, 77, 77, 77, 77, 77, 77,
            77, 77, 77, 77, 77, 77, 77, 77,
            77, 77, 77 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 279, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, 80, 80, 80, 80, 80, 80, 80,
            80, 80, 80, 80, 80, 80, 80, 80,
            80, 80, 80, 80, 80, 80, -1, 80,
            80, 80, 80, 80, 80, 80, 80, 80,
            80, 80, 80, 80, 80, 80, 80, 80,
            80, 80, 80, 80, 80, 80, 80, 80,
            80, 80, 80, 80, 80, 80, 80, 80,
            80, 80, 80 ),
        array( 1, 116, 116, 116, 116, 116, 116, 116,
            116, 116, 116, 116, 116, 116, 116, 146,
            116, 116, 116, 116, 116, 116, 116, 116,
            116, 116, 116, 116, 116, 116, 116, 116,
            116, 116, 116, 116, 116, 116, 116, 116,
            116, 116, 116, 116, 116, 116, 116, 116,
            116, 116, 116, 116, 116, 116, 116, 116,
            116, 116, 116 ),
        array( 1, 117, 117, 117, 117, 117, 117, 117,
            117, 117, 117, 117, 117, 117, 117, 117,
            117, 117, 117, 117, 117, 117, 117, 289,
            117, 117, 117, 117, 117, 117, 117, 117,
            117, 117, 117, 117, 117, 117, 117, 117,
            117, 117, 117, 117, 117, 117, 117, 117,
            117, 117, 117, 117, 117, 117, 117, 117,
            117, 117, 117 ),
        array( -1, -1, -1, 8, -1, -1, 9, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 9, 9, 9, 9, -1, -1, -1,
            9, 9, 9, 9, -1, -1, -1, -1,
            -1, -1, -1, -1, 9, 9, 9, 9,
            9, 9, 9, 9, 9, -1, -1, -1,
            9, 8, 9, 9, 9, 9, 9, 9,
            -1, 9, 8 ),
        array( -1, -1, -1, -1, -1, 3, 5, -1,
            -1, 132, -1, 3, 3, 6, 135, -1,
            3, 5, 5, 5, 5, -1, 3, 7,
            5, 5, 5, 5, -1, 3, 3, -1,
            -1, -1, 3, -1, 5, 5, 5, 5,
            5, 5, 5, 5, 5, 3, -1, 3,
            5, -1, 5, 5, 5, 5, 5, 5,
            -1, 5, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 137, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 90, -1, -1,
            -1, -1, -1, 90, 90, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 93, -1, -1,
            -1, 145, -1, 93, 93, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 94, -1, -1,
            -1, -1, -1, 94, 94, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 95, -1, -1,
            -1, -1, -1, 95, 95, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 212 ),
        array( -1, -1, -1, -1, -1, 97, -1, -1,
            -1, -1, -1, 97, 97, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 99, -1, -1,
            -1, -1, -1, 99, 99, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 40,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 100, -1, -1,
            -1, -1, -1, 100, 100, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 101, -1, -1,
            -1, -1, -1, 101, 101, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 103, -1, -1,
            -1, -1, -1, 103, 103, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 61, 62, -1,
            -1, -1, -1, 61, 61, -1, -1, -1,
            -1, 62, 62, 62, 62, -1, -1, -1,
            62, 62, 62, 62, -1, -1, -1, -1,
            -1, -1, -1, -1, 62, 62, 62, 62,
            62, 62, 62, 62, 62, -1, -1, -1,
            62, -1, 62, 62, 62, 62, 62, 62,
            -1, 62, -1 ),
        array( -1, -1, -1, -1, -1, 105, -1, -1,
            -1, -1, -1, 105, 105, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 106, -1, -1,
            -1, -1, -1, 106, 106, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 107, -1, -1,
            -1, -1, -1, 107, 107, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 108, -1, -1,
            -1, -1, -1, 108, 108, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 109, -1, -1,
            -1, -1, -1, 109, 109, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, 111, 111, 111, 111, 111, 111, 111,
            111, 111, 111, 111, 111, 111, 111, 239,
            111, 111, 111, 111, 111, 111, 111, 111,
            111, 111, 111, 111, 111, 111, 111, 111,
            111, 111, 111, 111, 111, 111, 111, 111,
            111, 111, 111, 111, 111, 111, 111, 111,
            111, 111, 111, 111, 111, 111, 111, 111,
            111, 111, 111 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 66, -1, 241,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, 265,
            -1, -1, -1, -1, -1, -1, -1, -1,
            266, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 335, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 126, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, 116, 116, 116, 116, 116, 116, 116,
            116, 116, 116, 116, 116, 116, 116, 287,
            116, 116, 116, 116, 116, 116, 116, 116,
            116, 116, 116, 116, 116, 116, 116, 116,
            116, 116, 116, 116, 116, 116, 116, 116,
            116, 116, 116, 116, 116, 116, 116, 116,
            116, 116, 116, 116, 116, 116, 116, 116,
            116, 116, 116 ),
        array( -1, 117, 117, 117, 117, 117, 117, 117,
            117, 117, 117, 117, 117, 117, 117, 117,
            117, 117, 117, 117, 117, 117, 117, -1,
            117, 117, 117, 117, 117, 117, 117, 117,
            117, 117, 117, 117, 117, 117, 117, 117,
            117, 117, 117, 117, 117, 117, 117, 117,
            117, 117, 117, 117, 117, 117, 117, 117,
            117, 117, 117 ),
        array( -1, -1, -1, 3, 3, 3, -1, 3,
            -1, 3, 3, 3, 3, 3, 3, 3,
            3, -1, -1, -1, -1, 3, 3, 3,
            -1, -1, -1, -1, 3, 3, 3, 3,
            3, 3, 3, 3, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 3, 3, 3,
            -1, 3, -1, -1, -1, -1, -1, -1,
            3, -1, 3 ),
        array( -1, 31, 31, 31, 31, 31, 31, 31,
            31, 31, 31, 31, 31, 31, 31, 31,
            31, 31, 31, 31, 31, 31, 31, 31,
            31, 31, 31, 31, 31, 31, 31, 31,
            -1, 31, 31, 31, 31, 31, 31, 31,
            31, 31, 31, 31, 31, 31, 31, 31,
            31, 31, 31, 31, 31, 31, 31, 31,
            31, 31, 31 ),
        array( -1, -1, -1, -1, -1, -1, 139, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 139, 139, 139, 139, -1, -1, -1,
            139, 139, 139, 139, -1, -1, -1, -1,
            -1, -1, -1, -1, 139, 139, 139, 139,
            139, 139, 294, 139, 139, 141, -1, -1,
            340, -1, 139, 139, 139, 325, 139, 139,
            -1, 139, -1 ),
        array( -1, -1, -1, -1, -1, -1, 213, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 213, 213, 213, 213, -1, -1, -1,
            213, 213, 213, 213, -1, -1, -1, -1,
            -1, -1, -1, -1, 213, 213, 213, 213,
            213, 213, 213, 213, 213, 213, -1, -1,
            213, -1, 213, 213, 213, 213, 213, 213,
            -1, 213, -1 ),
        array( 1, 129, 129, 129, 129, 97, 38, 129,
            36, 39, 129, 97, 97, 37, 129, 129,
            129, 38, 38, 38, 38, 129, 129, 129,
            38, 38, 38, 38, 129, 129, 129, 129,
            129, 129, 129, 129, 38, 38, 38, 38,
            38, 38, 38, 38, 38, 129, 129, 129,
            38, 129, 38, 38, 38, 38, 38, 38,
            129, 38, 129 ),
        array( -1, 42, 42, 123, 42, 101, 123, 123,
            42, 42, 42, 101, 101, -1, 42, 123,
            42, 123, 123, 123, 123, 42, 42, 42,
            123, 123, 123, 123, 42, 42, 42, 42,
            -1, -1, 42, 42, 123, 123, 123, 123,
            123, 123, 123, 123, 123, 42, 42, 42,
            123, 123, 123, 123, 123, 123, 123, 123,
            42, 123, 123 ),
        array( -1, 237, 237, 237, 237, 237, 237, 237,
            237, 237, 237, 237, 237, 237, 237, 237,
            237, 237, 237, 237, 237, 237, 237, 237,
            237, 237, 237, 237, 237, 237, 237, 237,
            63, 237, 237, 237, 237, 237, 237, 237,
            237, 237, 237, 237, 237, 237, 237, 237,
            237, 237, 237, 237, 237, 237, 237, 237,
            237, 237, 237 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 81, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 3, -1, -1,
            -1, -1, -1, 3, 3, -1, -1, -1,
            3, -1, -1, -1, -1, -1, 3, -1,
            -1, -1, -1, -1, -1, 3, 3, -1,
            -1, -1, 3, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 3, -1, 3,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, 118, 3, 3, 3, 3, 3, 3,
            128, 3, 3, 3, 3, 16, 3, 3,
            3, 3, 3, 3, 3, -1, 3, 3,
            3, 3, 3, 3, 3, 3, -1, 3,
            3, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3 ),
        array( -1, 238, 238, 238, 238, 238, 238, 238,
            238, 238, 238, 238, 238, 238, 238, 238,
            238, 238, 238, 238, 238, 238, 238, 238,
            238, 238, 238, 238, 238, 238, 238, 238,
            238, 110, 238, 238, 238, 238, 238, 238,
            238, 238, 238, 238, 238, 238, 238, 238,
            238, 238, 238, 238, 238, 238, 238, 238,
            238, 238, 238 ),
        array( -1, -1, -1, -1, -1, 143, 10, -1,
            -1, 145, -1, 143, 143, 11, -1, -1,
            -1, 10, 10, 10, 10, -1, -1, -1,
            10, 10, 10, 10, -1, -1, -1, -1,
            -1, -1, -1, -1, 10, 10, 10, 10,
            10, 10, 10, 10, 10, -1, -1, -1,
            10, -1, 10, 10, 10, 10, 10, 10,
            -1, 10, -1 ),
        array( -1, 51, 51, 51, 51, 51, 51, 51,
            51, 51, 51, 51, 51, 51, 51, 51,
            51, 51, 51, 51, 51, 51, 51, 51,
            51, 51, 51, 51, 51, -1, 51, 51,
            51, -1, 51, 51, 51, 51, 51, 51,
            51, 51, 51, 51, 51, 51, 51, 51,
            51, 51, 51, 51, 51, 51, 51, 51,
            51, 51, 51 ),
        array( -1, 111, 111, 111, 111, 111, 111, 111,
            111, 111, 111, 111, 111, 111, 111, 240,
            111, 111, 111, 111, 111, 111, 111, 111,
            111, 111, 111, 111, 111, 111, 111, 111,
            111, 111, 111, 111, 111, 111, 111, 111,
            111, 111, 111, 111, 111, 111, 111, 111,
            111, 111, 111, 111, 111, 111, 111, 111,
            111, 111, 111 ),
        array( -1, -1, -1, -1, -1, -1, 12, -1,
            -1, -1, -1, -1, -1, 13, -1, 147,
            14, 12, 12, 12, 12, -1, -1, -1,
            12, 12, 12, 12, -1, -1, -1, -1,
            -1, -1, -1, -1, 12, 12, 12, 12,
            12, 12, 12, 12, 12, -1, -1, -1,
            12, -1, 12, 12, 12, 12, 12, 12,
            -1, 12, -1 ),
        array( -1, 245, 70, 245, 245, 245, 245, 245,
            245, 245, 245, 245, 245, 245, 245, 245,
            245, 245, 245, 245, 245, 245, 245, 245,
            245, 245, 245, 245, 245, 245, 245, 245,
            245, 245, 245, 245, 245, 245, 245, 245,
            245, 245, 245, 245, 245, 245, 245, 245,
            245, 245, 245, 245, 245, 245, 245, 245,
            245, 245, 245 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 15,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, 246, -1, -1, 246, 247,
            -1, -1, -1, -1, -1, -1, -1, -1,
            248, 246, 246, 246, 246, 249, -1, -1,
            246, 246, 246, 246, 333, -1, -1, -1,
            -1, -1, -1, -1, 246, 246, 246, 246,
            246, 246, 246, 246, 246, 246, -1, -1,
            246, 246, 246, 246, 246, 246, 246, 246,
            71, 246, 246 ),
        array( -1, -1, -1, 139, -1, -1, 139, 149,
            -1, -1, 150, -1, -1, -1, -1, -1,
            151, 139, 139, 139, 139, -1, -1, -1,
            139, 139, 139, 139, 152, -1, -1, -1,
            -1, -1, -1, -1, 139, 139, 139, 139,
            139, 139, 139, 139, 139, 139, 17, 18,
            139, 139, 139, 139, 139, 139, 139, 139,
            -1, 139, 139 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 250, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 72,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, 139, -1, -1, 139, 149,
            -1, -1, 150, -1, -1, -1, -1, -1,
            151, 139, 139, 139, 139, -1, -1, -1,
            139, 139, 139, 139, 152, -1, -1, -1,
            -1, -1, -1, -1, 139, 139, 139, 139,
            139, 139, 139, 139, 139, 139, 19, 18,
            139, 139, 139, 139, 139, 139, 139, 139,
            -1, 139, 139 ),
        array( -1, 264, 74, 264, 264, 264, 264, 264,
            264, 264, 264, 264, 264, 264, 264, 264,
            264, 264, 264, 264, 264, 264, 264, 264,
            264, 264, 264, 264, 264, 264, 264, 264,
            264, 264, 264, 264, 264, 264, 264, 264,
            264, 264, 264, 264, 264, 264, 264, 264,
            264, 264, 264, 264, 264, 264, 264, 264,
            264, 264, 264 ),
        array( -1, -1, -1, -1, -1, 143, -1, -1,
            -1, 145, -1, 143, 143, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 267, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 268, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 75,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            20, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, 116, 116, 116, 116, 116, 116, 116,
            116, 116, 116, 116, 116, 116, 116, 288,
            116, 116, 116, 116, 116, 116, 116, 116,
            116, 116, 116, 116, 116, 116, 116, 116,
            116, 116, 116, 116, 116, 116, 116, 116,
            116, 116, 116, 116, 116, 116, 116, 116,
            116, 116, 116, 116, 116, 116, 116, 116,
            116, 116, 116 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 21,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 155, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 156, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 156, 156, 156, 156, -1, -1, -1,
            156, 156, 156, 156, -1, -1, -1, -1,
            -1, -1, -1, -1, 156, 156, 156, 156,
            156, 156, 156, 156, 156, 156, -1, -1,
            156, -1, 156, 156, 156, 156, 156, 156,
            -1, 156, -1 ),
        array( -1, -1, -1, -1, -1, -1, 157, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 157, 157, 157, 157, -1, -1, -1,
            157, 157, 157, 157, -1, -1, -1, -1,
            -1, -1, -1, -1, 157, 157, 157, 157,
            157, 157, 157, 157, 157, -1, -1, -1,
            157, -1, 157, 157, 157, 157, 157, 157,
            -1, 157, -1 ),
        array( -1, -1, -1, 158, -1, -1, 158, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 158, 158, 158, 158, -1, -1, -1,
            158, 158, 158, 158, -1, -1, -1, -1,
            -1, -1, -1, -1, 158, 158, 158, 158,
            158, 158, 158, 158, 158, 158, -1, -1,
            158, 158, 158, 158, 158, 158, 158, 158,
            -1, 158, 158 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 159, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, 139, -1, -1, 139, 149,
            -1, -1, 160, -1, -1, -1, -1, -1,
            151, 139, 139, 139, 139, -1, -1, -1,
            139, 139, 139, 139, 152, -1, -1, -1,
            -1, -1, -1, -1, 139, 139, 139, 139,
            139, 139, 139, 139, 139, 139, 17, 18,
            139, 139, 139, 139, 139, 139, 139, 139,
            -1, 139, 139 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 162, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 163, 163, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, 156, -1, -1, 156, 149,
            -1, -1, 150, -1, -1, -1, -1, -1,
            164, 156, 156, 156, 156, -1, -1, -1,
            156, 156, 156, 156, 165, -1, -1, -1,
            -1, -1, -1, -1, 156, 156, 156, 156,
            156, 156, 156, 156, 156, 156, 17, 18,
            156, 156, 156, 156, 156, 156, 156, 156,
            -1, 156, 156 ),
        array( -1, -1, -1, -1, -1, -1, 157, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 157, 157, 157, 157, -1, -1, -1,
            157, 157, 157, 157, -1, -1, -1, -1,
            -1, -1, -1, -1, 157, 157, 157, 157,
            157, 157, 157, 157, 157, -1, -1, 18,
            157, -1, 157, 157, 157, 157, 157, 157,
            -1, 157, -1 ),
        array( -1, -1, -1, 158, -1, -1, 158, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 158, 158, 158, 158, -1, 166, -1,
            158, 158, 158, 158, 167, -1, -1, -1,
            -1, -1, -1, -1, 158, 158, 158, 158,
            158, 158, 158, 158, 158, 158, -1, -1,
            158, 158, 158, 158, 158, 158, 158, 158,
            -1, 158, 158 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 151, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 168, -1,
            -1, -1, -1, -1, -1, -1, 169, -1,
            -1, 168, 168, 168, 168, -1, -1, -1,
            168, 168, 168, 168, -1, -1, -1, -1,
            -1, -1, -1, -1, 168, 168, 168, 168,
            168, 168, 168, 168, 168, 168, -1, -1,
            168, -1, 168, 168, 168, 168, 168, 168,
            -1, 168, -1 ),
        array( -1, -1, -1, 139, -1, -1, 139, 149,
            -1, -1, 170, -1, -1, -1, -1, -1,
            151, 139, 139, 139, 139, -1, -1, -1,
            139, 139, 139, 139, 152, -1, -1, -1,
            -1, -1, -1, -1, 139, 139, 139, 139,
            139, 139, 139, 139, 139, 139, 17, 18,
            139, 139, 139, 139, 139, 139, 139, 139,
            -1, 139, 139 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 171, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, 163, 163, 163, 163, 163, 163, 163,
            163, 163, 163, 163, 163, 22, 163, 163,
            163, 163, 163, 163, 163, 163, 163, 163,
            163, 163, 163, 163, 163, 163, 163, 163,
            163, 163, 163, 163, 163, 163, 163, 163,
            163, 163, 163, 163, 163, 163, 163, 163,
            163, 163, 163, 163, 163, 163, 163, 163,
            163, 163, 163 ),
        array( -1, -1, -1, 172, -1, -1, 172, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 172, 172, 172, 172, -1, -1, -1,
            172, 172, 172, 172, -1, -1, -1, -1,
            -1, -1, -1, -1, 172, 172, 172, 172,
            172, 172, 172, 172, 172, 172, -1, -1,
            172, 172, 172, 172, 172, 172, 172, 172,
            -1, 172, 172 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 297, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, 149,
            -1, -1, 150, -1, -1, -1, -1, -1,
            151, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 152, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 18,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 173, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, 168, -1, -1, 168, 174,
            -1, -1, -1, -1, -1, -1, -1, -1,
            175, 168, 168, 168, 168, -1, -1, -1,
            168, 168, 168, 168, 326, -1, -1, -1,
            -1, -1, -1, -1, 168, 168, 168, 168,
            168, 168, 168, 168, 168, 168, 23, 24,
            168, 168, 168, 168, 168, 168, 168, 168,
            -1, 168, 168 ),
        array( -1, -1, -1, -1, -1, -1, 168, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 168, 168, 168, 168, -1, -1, -1,
            168, 168, 168, 168, -1, -1, -1, -1,
            -1, -1, -1, -1, 168, 168, 168, 168,
            168, 168, 168, 168, 168, 168, -1, -1,
            168, -1, 168, 168, 168, 168, 168, 168,
            -1, 168, -1 ),
        array( -1, -1, -1, -1, -1, -1, 157, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 157, 157, 157, 157, -1, -1, -1,
            157, 157, 157, 157, -1, -1, -1, -1,
            -1, -1, -1, -1, 157, 157, 157, 157,
            157, 157, 157, 157, 157, -1, -1, 25,
            157, -1, 157, 157, 157, 157, 157, 157,
            -1, 157, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 177, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, 172, -1, -1, 172, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 172, 172, 172, 172, -1, 178, -1,
            172, 172, 172, 172, 179, -1, -1, -1,
            -1, -1, -1, -1, 172, 172, 172, 172,
            172, 172, 172, 172, 172, 172, -1, -1,
            172, 172, 172, 172, 172, 172, 172, 172,
            -1, 172, 172 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 166, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 166, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 180, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 180, 180, 180, 180, -1, -1, -1,
            180, 180, 180, 180, -1, -1, -1, -1,
            -1, -1, -1, -1, 180, 180, 180, 180,
            180, 180, 180, 180, 180, 180, -1, -1,
            180, -1, 180, 180, 180, 180, 180, 180,
            -1, 180, -1 ),
        array( -1, -1, -1, 181, -1, -1, 181, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 181, 181, 181, 181, -1, -1, -1,
            181, 181, 181, 181, -1, -1, -1, -1,
            -1, -1, -1, -1, 181, 181, 181, 181,
            181, 181, 181, 181, 181, 181, -1, -1,
            181, 181, 181, 181, 181, 181, 181, 181,
            -1, 181, 181 ),
        array( -1, -1, -1, -1, -1, -1, 157, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 157, 157, 157, 157, -1, -1, -1,
            157, 157, 157, 157, -1, -1, -1, -1,
            -1, -1, -1, -1, 157, 157, 157, 157,
            157, 157, 157, 157, 157, -1, -1, 26,
            157, -1, 157, 157, 157, 157, 157, 157,
            -1, 157, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 182, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, 149,
            -1, -1, 150, -1, -1, -1, -1, -1,
            164, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 165, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 18,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 183, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, 180, -1, -1, 180, 174,
            -1, -1, -1, -1, -1, -1, -1, -1,
            184, 180, 180, 180, 180, -1, -1, -1,
            180, 180, 180, 180, 329, -1, -1, -1,
            -1, -1, -1, -1, 180, 180, 180, 180,
            180, 180, 180, 180, 180, 180, 23, 24,
            180, 180, 180, 180, 180, 180, 180, 180,
            -1, 180, 180 ),
        array( -1, -1, -1, 181, -1, -1, 181, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 181, 181, 181, 181, -1, 185, -1,
            181, 181, 181, 181, 186, -1, -1, -1,
            -1, -1, -1, -1, 181, 181, 181, 181,
            181, 181, 181, 181, 181, 181, -1, -1,
            181, 181, 181, 181, 181, 181, 181, 181,
            -1, 181, 181 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            27, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 178, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 178, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, 188, -1, -1, 188, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 188, 188, 188, 188, -1, -1, -1,
            188, 188, 188, 188, -1, -1, -1, -1,
            -1, -1, -1, -1, 188, 188, 188, 188,
            188, 188, 188, 188, 188, 188, -1, -1,
            188, 188, 188, 188, 188, 188, 188, 188,
            -1, 188, 188 ),
        array( -1, -1, -1, -1, -1, -1, -1, 174,
            -1, -1, -1, -1, -1, -1, -1, -1,
            175, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 326, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 23, 24,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 189, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, 139, -1, -1, 139, 149,
            -1, -1, 190, -1, -1, -1, -1, -1,
            151, 139, 139, 139, 139, -1, -1, -1,
            139, 139, 139, 139, 152, -1, -1, -1,
            -1, -1, -1, -1, 139, 139, 139, 139,
            139, 139, 139, 139, 139, 139, 17, 18,
            139, 139, 139, 139, 139, 139, 139, 139,
            -1, 139, 139 ),
        array( -1, -1, -1, 188, -1, -1, 188, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 188, 188, 188, 188, -1, 191, -1,
            188, 188, 188, 188, 192, -1, -1, -1,
            -1, -1, -1, -1, 188, 188, 188, 188,
            188, 188, 188, 188, 188, 188, -1, -1,
            188, 188, 188, 188, 188, 188, 188, 188,
            -1, 188, 188 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 185, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 185, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 193, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 193, 193, 193, 193, -1, -1, -1,
            193, 193, 193, 193, -1, -1, -1, -1,
            -1, -1, -1, -1, 193, 193, 193, 193,
            193, 193, 193, 193, 193, 193, -1, -1,
            193, -1, 193, 193, 193, 193, 193, 193,
            -1, 193, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, 174,
            -1, -1, -1, -1, -1, -1, -1, -1,
            184, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 329, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 23, 24,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 194, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, 193, -1, -1, 193, 195,
            -1, -1, -1, -1, -1, -1, -1, -1,
            196, 193, 193, 193, 193, -1, -1, -1,
            193, 193, 193, 193, 331, -1, -1, -1,
            -1, -1, -1, -1, 193, 193, 193, 193,
            193, 193, 193, 193, 193, 193, -1, 28,
            193, 193, 193, 193, 193, 193, 193, 193,
            299, 193, 193 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 191, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 191, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 197, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 197, 197, 197, 197, -1, -1, -1,
            197, 197, 197, 197, -1, -1, -1, -1,
            -1, -1, -1, -1, 197, 197, 197, 197,
            197, 197, 197, 197, 197, 197, -1, -1,
            197, -1, 197, 197, 197, 197, 197, 197,
            -1, 197, -1 ),
        array( -1, -1, -1, 198, -1, -1, 198, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 198, 198, 198, 198, -1, -1, -1,
            198, 198, 198, 198, -1, -1, -1, -1,
            -1, -1, -1, -1, 198, 198, 198, 198,
            198, 198, 198, 198, 198, 198, -1, -1,
            198, 198, 198, 198, 198, 198, 198, 198,
            -1, 198, 198 ),
        array( -1, -1, -1, 197, -1, -1, 197, 195,
            -1, -1, -1, -1, -1, -1, -1, -1,
            200, 197, 197, 197, 197, -1, -1, -1,
            197, 197, 197, 197, 332, -1, -1, -1,
            -1, -1, -1, -1, 197, 197, 197, 197,
            197, 197, 197, 197, 197, 197, -1, 28,
            197, 197, 197, 197, 197, 197, 197, 197,
            299, 197, 197 ),
        array( -1, -1, -1, 198, -1, -1, 198, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 198, 198, 198, 198, -1, 201, -1,
            198, 198, 198, 198, 202, -1, -1, -1,
            -1, -1, -1, -1, 198, 198, 198, 198,
            198, 198, 198, 198, 198, 198, -1, -1,
            198, 198, 198, 198, 198, 198, 198, 198,
            -1, 198, 198 ),
        array( -1, -1, -1, 199, -1, -1, 199, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 199, 199, 199, 199, -1, -1, -1,
            199, 199, 199, 199, -1, -1, -1, -1,
            -1, -1, -1, -1, 199, 199, 199, 199,
            199, 199, 199, 199, 199, 199, -1, 29,
            199, 199, 199, 199, 199, 199, 199, 199,
            203, 199, 199 ),
        array( -1, -1, -1, 204, -1, -1, 204, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 204, 204, 204, 204, -1, -1, -1,
            204, 204, 204, 204, -1, -1, -1, -1,
            -1, -1, -1, -1, 204, 204, 204, 204,
            204, 204, 204, 204, 204, 204, -1, -1,
            204, 204, 204, 204, 204, 204, 204, 204,
            -1, 204, 204 ),
        array( -1, -1, -1, -1, -1, -1, -1, 195,
            -1, -1, -1, -1, -1, -1, -1, -1,
            196, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 331, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 28,
            -1, -1, -1, -1, -1, -1, -1, -1,
            299, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 205, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 206, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 206, 206, 206, 206, -1, -1, -1,
            206, 206, 206, 206, -1, -1, -1, -1,
            -1, -1, -1, -1, 206, 206, 206, 206,
            206, 206, 206, 206, 206, 206, -1, -1,
            206, -1, 206, 206, 206, 206, 206, 206,
            -1, 206, -1 ),
        array( -1, -1, -1, 204, -1, -1, 204, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 204, 204, 204, 204, -1, 207, -1,
            204, 204, 204, 204, 208, -1, -1, -1,
            -1, -1, -1, -1, 204, 204, 204, 204,
            204, 204, 204, 204, 204, 204, -1, -1,
            204, 204, 204, 204, 204, 204, 204, 204,
            -1, 204, 204 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 201, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 201, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, 206, -1, -1, 206, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 206, 206, 206, 206, -1, -1, -1,
            206, 206, 206, 206, -1, -1, -1, -1,
            -1, -1, -1, -1, 206, 206, 206, 206,
            206, 206, 206, 206, 206, 206, -1, 30,
            206, 206, 206, 206, 206, 206, 206, 206,
            -1, 206, 206 ),
        array( -1, -1, -1, -1, -1, -1, -1, 195,
            -1, -1, -1, -1, -1, -1, -1, -1,
            200, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 332, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 28,
            -1, -1, -1, -1, -1, -1, -1, -1,
            299, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 209, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 207, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 207, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( 1, 31, 31, 31, 31, 31, 31, 31,
            31, 31, 31, 31, 31, 31, 31, 31,
            31, 31, 31, 31, 31, 31, 31, 31,
            31, 31, 31, 31, 96, 119, 121, 31,
            32, 31, 31, 31, 31, 31, 31, 31,
            31, 31, 31, 31, 31, 31, 31, 31,
            31, 31, 31, 31, 31, 31, 31, 31,
            31, 31, 31 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 295, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, 213, -1, -1, 213, 214,
            -1, -1, 215, -1, -1, -1, -1, -1,
            216, 213, 213, 213, 213, -1, -1, -1,
            213, 213, 213, 213, 217, -1, -1, -1,
            -1, -1, -1, -1, 213, 213, 213, 213,
            213, 213, 213, 213, 213, 213, 33, 34,
            213, 213, 213, 213, 213, 213, 213, 213,
            -1, 213, 213 ),
        array( -1, -1, -1, -1, -1, -1, 218, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 218, 218, 218, 218, -1, -1, -1,
            218, 218, 218, 218, -1, -1, -1, -1,
            -1, -1, -1, -1, 218, 218, 218, 218,
            218, 218, 218, 218, 218, 218, -1, -1,
            218, -1, 218, 218, 218, 218, 218, 218,
            -1, 218, -1 ),
        array( -1, -1, -1, -1, -1, -1, 219, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 219, 219, 219, 219, -1, -1, -1,
            219, 219, 219, 219, -1, -1, -1, -1,
            -1, -1, -1, -1, 219, 219, 219, 219,
            219, 219, 219, 219, 219, -1, -1, -1,
            219, -1, 219, 219, 219, 219, 219, 219,
            -1, 219, -1 ),
        array( -1, -1, -1, 220, -1, -1, 220, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 220, 220, 220, 220, -1, -1, -1,
            220, 220, 220, 220, -1, -1, -1, -1,
            -1, -1, -1, -1, 220, 220, 220, 220,
            220, 220, 220, 220, 220, 220, -1, -1,
            220, 220, 220, 220, 220, 220, 220, 220,
            -1, 220, 220 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 314, -1, -1, -1, -1, -1, -1,
            -1, -1, 221 ),
        array( -1, -1, -1, 218, -1, -1, 218, 214,
            -1, -1, 215, -1, -1, -1, -1, -1,
            222, 218, 218, 218, 218, -1, -1, -1,
            218, 218, 218, 218, 327, -1, -1, -1,
            -1, -1, -1, -1, 218, 218, 218, 218,
            218, 218, 218, 218, 218, 218, 33, 34,
            218, 218, 218, 218, 218, 218, 218, 218,
            -1, 218, 218 ),
        array( -1, -1, -1, -1, -1, -1, 219, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 219, 219, 219, 219, -1, -1, -1,
            219, 219, 219, 219, 223, -1, -1, -1,
            -1, -1, -1, -1, 219, 219, 219, 219,
            219, 219, 219, 219, 219, -1, -1, 34,
            219, -1, 219, 219, 219, 219, 219, 219,
            -1, 219, -1 ),
        array( -1, -1, -1, 220, -1, -1, 220, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 220, 220, 220, 220, -1, 224, -1,
            220, 220, 220, 220, 225, -1, -1, -1,
            -1, -1, -1, -1, 220, 220, 220, 220,
            220, 220, 220, 220, 220, 220, -1, -1,
            220, 220, 220, 220, 220, 220, 220, 220,
            -1, 220, 220 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 34, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 34, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, 226, -1, -1, 226, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 226, 226, 226, 226, -1, -1, -1,
            226, 226, 226, 226, -1, -1, -1, -1,
            -1, -1, -1, -1, 226, 226, 226, 226,
            226, 226, 226, 226, 226, 226, -1, -1,
            226, 226, 226, 226, 226, 226, 226, 226,
            -1, 226, 226 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 221 ),
        array( -1, -1, -1, -1, -1, -1, -1, 214,
            -1, -1, 215, -1, -1, -1, -1, -1,
            216, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 217, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 34,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 227, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, 226, -1, -1, 226, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 226, 226, 226, 226, -1, 228, -1,
            226, 226, 226, 226, 229, -1, -1, -1,
            -1, -1, -1, -1, 226, 226, 226, 226,
            226, 226, 226, 226, 226, 226, -1, -1,
            226, 226, 226, 226, 226, 226, 226, 226,
            -1, 226, 226 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 224, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 224, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, 214,
            -1, -1, 215, -1, -1, -1, -1, -1,
            222, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 327, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 34,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 230, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 228, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 228, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( 1, 42, 42, 43, 42, -1, 291, 291,
            98, 44, 42, 129, -1, 45, 42, 291,
            42, 291, 291, 291, 291, 42, 42, 42,
            291, 291, 291, 291, 42, 42, 42, 42,
            46, 47, 42, 42, 291, 291, 291, 291,
            291, 291, 291, 291, 291, 42, 42, 42,
            291, 43, 291, 291, 291, 291, 291, 291,
            42, 291, 43 ),
        array( 1, 129, 129, 129, 129, 48, 129, 129,
            129, 129, 129, 48, 48, 129, 129, 129,
            129, 129, 129, 129, 129, 129, 129, 129,
            129, 129, 129, 129, 129, 129, 129, 129,
            129, 129, 129, 129, 129, 129, 129, 129,
            129, 129, 129, 129, 129, 129, 129, 129,
            129, 129, 129, 129, 129, 129, 129, 129,
            129, 129, 129 ),
        array( 1, 49, 49, 49, 49, -1, 49, 49,
            49, 49, 49, 49, -1, 50, 49, 49,
            49, 49, 49, 49, 49, 49, 49, 49,
            49, 49, 49, 49, 49, 49, 49, 49,
            49, 49, 49, 49, 49, 49, 49, 49,
            49, 49, 49, 49, 49, 49, 49, 49,
            49, 49, 49, 49, 49, 49, 49, 49,
            49, 49, 49 ),
        array( 1, 51, 51, 51, 51, 51, 51, 51,
            51, 51, 51, 51, 51, 51, 51, 51,
            51, 51, 51, 51, 51, 51, 51, 51,
            51, 51, 51, 51, 102, 133, 124, 51,
            51, 52, 51, 51, 51, 51, 51, 51,
            51, 51, 51, 51, 51, 51, 51, 51,
            51, 51, 51, 51, 51, 51, 51, 51,
            51, 51, 51 ),
        array( -1, 111, 111, 111, 111, 111, 111, 111,
            111, 111, 111, 111, 111, 111, 111, -1,
            111, 111, 111, 111, 111, 111, 111, 111,
            111, 111, 111, 111, 111, 111, 111, 111,
            111, 111, 111, 111, 111, 111, 111, 111,
            111, 111, 111, 111, 111, 111, 111, 111,
            111, 111, 111, 111, 111, 111, 111, 111,
            111, 111, 111 ),
        array( -1, 65, 65, 65, 65, 65, 65, 65,
            65, 65, 65, 65, 65, 66, 65, 112,
            65, 65, 65, 65, 65, 65, 65, 65,
            65, 65, 65, 65, 65, 65, 65, 65,
            65, 65, 65, 65, 65, 65, 65, 65,
            65, 65, 65, 65, 65, 65, 65, 65,
            65, 65, 65, 65, 65, 65, 65, 65,
            65, 65, 65 ),
        array( 1, 67, 67, 67, 67, 67, 67, 67,
            67, 67, 67, 67, 67, 67, 67, 67,
            67, 67, 67, 67, 67, 67, 68, 67,
            67, 67, 67, 67, 67, 67, 67, 67,
            67, 67, 67, 67, 67, 67, 67, 67,
            67, 67, 67, 67, 67, 67, 67, 67,
            67, 67, 67, 67, 67, 67, 67, 67,
            67, 67, 67 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 69, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( 1, 129, 136, 129, 129, -1, 138, 129,
            129, 129, 129, 129, -1, 129, 129, 129,
            129, 138, 138, 138, 138, 140, 129, 129,
            138, 138, 138, 138, 129, 129, 129, 129,
            129, 129, 129, 129, 138, 138, 138, 138,
            138, 138, 138, 138, 138, 138, 129, 129,
            138, 129, 138, 138, 138, 138, 138, 138,
            129, 138, 129 ),
        array( -1, -1, -1, -1, -1, -1, 251, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 251, 251, 251, 251, -1, -1, -1,
            251, 251, 251, 251, -1, -1, -1, -1,
            -1, -1, -1, -1, 251, 251, 251, 251,
            251, 251, 251, 251, 251, 251, -1, -1,
            251, -1, 251, 251, 251, 251, 251, 251,
            -1, 251, -1 ),
        array( -1, -1, -1, 252, -1, -1, 252, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 252, 252, 252, 252, -1, -1, -1,
            252, 252, 252, 252, -1, -1, -1, -1,
            -1, -1, -1, -1, 252, 252, 252, 252,
            252, 252, 252, 252, 252, 252, -1, -1,
            252, 252, 252, 252, 252, 252, 252, 252,
            -1, 252, 252 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 300, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 71,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 253, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 253, 253, 253, 253, -1, -1, -1,
            253, 253, 253, 253, -1, -1, -1, -1,
            -1, -1, -1, -1, 253, 253, 253, 253,
            253, 253, 253, 253, 253, -1, -1, -1,
            253, -1, 253, 253, 253, 253, 253, 253,
            -1, 253, -1 ),
        array( -1, -1, -1, 251, -1, -1, 251, 247,
            -1, -1, -1, -1, -1, -1, -1, -1,
            254, 251, 251, 251, 251, 249, -1, -1,
            251, 251, 251, 251, 334, -1, -1, -1,
            -1, -1, -1, -1, 251, 251, 251, 251,
            251, 251, 251, 251, 251, 251, -1, -1,
            251, 251, 251, 251, 251, 251, 251, 251,
            71, 251, 251 ),
        array( -1, -1, -1, 252, -1, -1, 252, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 252, 252, 252, 252, -1, 255, -1,
            252, 252, 252, 252, 256, -1, -1, -1,
            -1, -1, -1, -1, 252, 252, 252, 252,
            252, 252, 252, 252, 252, 252, -1, -1,
            252, 252, 252, 252, 252, 252, 252, 252,
            -1, 252, 252 ),
        array( -1, -1, -1, -1, -1, -1, 253, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 253, 253, 253, 253, -1, -1, -1,
            253, 253, 253, 253, -1, -1, -1, -1,
            -1, -1, -1, -1, 253, 253, 253, 253,
            253, 253, 253, 253, 253, -1, -1, 72,
            253, -1, 253, 253, 253, 253, 253, 253,
            -1, 253, -1 ),
        array( -1, -1, -1, 258, -1, -1, 258, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 258, 258, 258, 258, -1, -1, -1,
            258, 258, 258, 258, -1, -1, -1, -1,
            -1, -1, -1, -1, 258, 258, 258, 258,
            258, 258, 258, 258, 258, 258, -1, -1,
            258, 258, 258, 258, 258, 258, 258, 258,
            -1, 258, 258 ),
        array( -1, -1, -1, -1, -1, -1, -1, 247,
            -1, -1, -1, -1, -1, -1, -1, -1,
            248, -1, -1, -1, -1, 249, -1, -1,
            -1, -1, -1, -1, 333, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            71, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 259, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 257, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 257, 257, 257, 257, -1, -1, -1,
            257, 257, 257, 257, -1, -1, -1, -1,
            -1, -1, -1, -1, 257, 257, 257, 257,
            257, 257, 257, 257, 257, -1, -1, 71,
            257, -1, 257, 257, 257, 257, 257, 257,
            -1, 257, -1 ),
        array( -1, -1, -1, 258, -1, -1, 258, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 258, 258, 258, 258, -1, 260, -1,
            258, 258, 258, 258, 261, -1, -1, -1,
            -1, -1, -1, -1, 258, 258, 258, 258,
            258, 258, 258, 258, 258, 258, -1, -1,
            258, 258, 258, 258, 258, 258, 258, 258,
            -1, 258, 258 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 255, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 255, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, 247,
            -1, -1, -1, -1, -1, -1, -1, -1,
            254, -1, -1, -1, -1, 249, -1, -1,
            -1, -1, -1, -1, 334, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            71, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 262, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 260, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 260, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( 1, 129, 142, 129, 129, -1, 73, 129,
            129, 129, 129, 129, -1, 129, 129, 129,
            129, 73, 73, 73, 73, 144, 129, 129,
            73, 73, 73, 73, 129, 129, 129, 129,
            129, 129, 129, 129, 73, 73, 73, 73,
            73, 73, 73, 73, 73, 73, 129, 129,
            73, 129, 73, 73, 73, 73, 73, 73,
            129, 73, 129 ),
        array( -1, -1, -1, -1, -1, -1, 292, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 292, 292, 292, 292, -1, -1, -1,
            292, 292, 292, 292, -1, -1, -1, -1,
            -1, -1, -1, -1, 292, 292, 292, 292,
            292, 292, 292, 292, 292, 292, -1, -1,
            292, -1, 292, 292, 292, 292, 292, 292,
            -1, 292, -1 ),
        array( -1, -1, -1, 269, -1, -1, 269, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 269, 269, 269, 269, -1, -1, -1,
            269, 269, 269, 269, -1, -1, -1, -1,
            -1, -1, -1, -1, 269, 269, 269, 269,
            269, 269, 269, 269, 269, 269, -1, -1,
            269, 269, 269, 269, 269, 269, 269, 269,
            -1, 269, 269 ),
        array( -1, -1, -1, -1, -1, -1, 270, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 270, 270, 270, 270, -1, -1, -1,
            270, 270, 270, 270, -1, -1, -1, -1,
            -1, -1, -1, -1, 270, 270, 270, 270,
            270, 270, 270, 270, 270, -1, -1, -1,
            270, -1, 270, 270, 270, 270, 270, 270,
            -1, 270, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 271 ),
        array( -1, -1, -1, 269, -1, -1, 269, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 269, 269, 269, 269, -1, 114, -1,
            269, 269, 269, 269, 273, -1, -1, -1,
            -1, -1, -1, -1, 269, 269, 269, 269,
            269, 269, 269, 269, 269, 269, -1, -1,
            269, 269, 269, 269, 269, 269, 269, 269,
            -1, 269, 269 ),
        array( -1, -1, -1, -1, -1, -1, 270, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 270, 270, 270, 270, -1, -1, -1,
            270, 270, 270, 270, 268, -1, -1, -1,
            -1, -1, -1, -1, 270, 270, 270, 270,
            270, 270, 270, 270, 270, -1, -1, 75,
            270, -1, 270, 270, 270, 270, 270, 270,
            -1, 270, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 75, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 75, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, 274, -1, -1, 274, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 274, 274, 274, 274, -1, -1, -1,
            274, 274, 274, 274, -1, -1, -1, -1,
            -1, -1, -1, -1, 274, 274, 274, 274,
            274, 274, 274, 274, 274, 274, -1, -1,
            274, 274, 274, 274, 274, 274, 274, 274,
            -1, 274, 274 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 275, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, 274, -1, -1, 274, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 274, 274, 274, 274, -1, 293, -1,
            274, 274, 274, 274, 276, -1, -1, -1,
            -1, -1, -1, -1, 274, 274, 274, 274,
            274, 274, 274, 274, 274, 274, -1, -1,
            274, 274, 274, 274, 274, 274, 274, 274,
            -1, 274, 274 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 114, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 114, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 301, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( 1, 129, 129, 129, 129, -1, 129, 129,
            129, 129, 129, 129, -1, 129, 129, 129,
            129, 129, 129, 129, 129, 144, 129, 129,
            129, 129, 129, 129, 129, 129, 129, 129,
            129, 129, 129, 129, 129, 129, 129, 129,
            129, 129, 129, 129, 129, 129, 129, 129,
            129, 129, 129, 129, 129, 129, 129, 129,
            76, 129, 129 ),
        array( 1, 77, 77, 77, 77, 77, 77, 77,
            78, 77, 77, 77, 77, 77, 77, 77,
            77, 77, 77, 77, 77, 77, 77, 77,
            77, 77, 77, 77, 77, 77, 77, 77,
            77, 77, 77, 77, 77, 77, 77, 77,
            77, 77, 77, 77, 77, 77, 77, 77,
            77, 77, 77, 77, 77, 77, 77, 77,
            77, 77, 77 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 280, 280, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 281, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 281, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 282,
            282, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 283, 283, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 284, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 285, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 285, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 79, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( 1, 80, 80, 80, 80, 80, 80, 80,
            80, 80, 80, 80, 80, 80, 80, 80,
            80, 80, 80, 80, 80, 80, 115, 80,
            80, 80, 80, 80, 80, 80, 80, 80,
            80, 80, 80, 80, 80, 80, 80, 80,
            80, 80, 80, 80, 80, 80, 80, 80,
            80, 80, 80, 80, 80, 80, 80, 80,
            80, 80, 80 ),
        array( -1, 116, 116, 116, 116, 116, 116, 116,
            116, 116, 116, 116, 116, 116, 116, -1,
            116, 116, 116, 116, 116, 116, 116, 116,
            116, 116, 116, 116, 116, 116, 116, 116,
            116, 116, 116, 116, 116, 116, 116, 116,
            116, 116, 116, 116, 116, 116, 116, 116,
            116, 116, 116, 116, 116, 116, 116, 116,
            116, 116, 116 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 83, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, 127, 127, 127, 127, 127, 127, 127,
            127, 127, 127, 127, 127, 85, 127, 127,
            127, 127, 127, 127, 127, 127, 127, 127,
            127, 127, 127, 127, 127, 127, 127, 127,
            127, 127, 127, 127, 127, 127, 127, 127,
            127, 127, 127, 127, 127, 127, 127, 127,
            127, 127, 127, 127, 127, 127, 127, 127,
            127, 127, 127 ),
        array( -1, 118, 3, 3, 3, 3, 3, 3,
            128, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, -1, 130, 3,
            3, 3, 3, 3, 3, 3, -1, 3,
            3, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3 ),
        array( -1, -1, -1, 292, -1, -1, 292, 265,
            -1, -1, -1, -1, -1, -1, -1, -1,
            272, 292, 292, 292, 292, -1, -1, -1,
            292, 292, 292, 292, 336, -1, -1, -1,
            -1, -1, -1, -1, 292, 292, 292, 292,
            292, 292, 292, 292, 292, 292, -1, -1,
            292, 292, 292, 292, 292, 292, 292, 292,
            -1, 292, 292 ),
        array( -1, -1, -1, -1, -1, -1, -1, 265,
            -1, -1, -1, -1, -1, -1, -1, -1,
            272, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 336, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, 139, -1, -1, 139, 149,
            -1, -1, 150, -1, -1, -1, -1, -1,
            151, 139, 139, 139, 139, -1, -1, -1,
            139, 139, 139, 139, 152, -1, -1, -1,
            -1, -1, -1, -1, 139, 139, 139, 139,
            139, 139, 139, 139, 139, 139, 17, 18,
            153, 139, 139, 139, 139, 139, 139, 139,
            -1, 139, 139 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 296, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, 139, -1, -1, 139, 149,
            -1, -1, 176, -1, -1, -1, -1, -1,
            151, 139, 139, 139, 139, -1, -1, -1,
            139, 139, 139, 139, 152, -1, -1, -1,
            -1, -1, -1, -1, 139, 139, 139, 139,
            139, 139, 139, 139, 139, 139, 17, 18,
            139, 139, 139, 139, 139, 139, 139, 139,
            -1, 139, 139 ),
        array( -1, -1, -1, -1, -1, -1, 199, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 199, 199, 199, 199, -1, -1, -1,
            199, 199, 199, 199, -1, -1, -1, -1,
            -1, -1, -1, -1, 199, 199, 199, 199,
            199, 199, 199, 199, 199, 199, -1, -1,
            199, -1, 199, 199, 199, 199, 199, 199,
            -1, 199, -1 ),
        array( -1, -1, -1, -1, -1, -1, 257, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 257, 257, 257, 257, -1, -1, -1,
            257, 257, 257, 257, -1, -1, -1, -1,
            -1, -1, -1, -1, 257, 257, 257, 257,
            257, 257, 257, 257, 257, -1, -1, -1,
            257, -1, 257, 257, 257, 257, 257, 257,
            -1, 257, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 293, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 293, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, 139, -1, -1, 139, 149,
            -1, -1, 150, -1, -1, -1, -1, -1,
            151, 139, 139, 139, 139, -1, -1, -1,
            139, 139, 139, 139, 152, -1, -1, -1,
            -1, -1, -1, -1, 139, 139, 139, 139,
            139, 139, 139, 139, 139, 139, 17, 18,
            139, 139, 139, 161, 139, 139, 139, 139,
            -1, 139, 139 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 303, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, 139, -1, -1, 139, 149,
            -1, -1, 150, -1, -1, -1, -1, -1,
            151, 139, 139, 139, 139, -1, -1, -1,
            139, 139, 139, 139, 152, -1, -1, -1,
            -1, -1, -1, -1, 139, 139, 139, 139,
            139, 139, 139, 139, 139, 139, 17, 18,
            139, 139, 139, 139, 139, 298, 139, 139,
            -1, 139, 139 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 306, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, 139, -1, -1, 139, 149,
            -1, -1, 150, -1, -1, -1, -1, -1,
            151, 139, 139, 139, 139, -1, -1, -1,
            139, 139, 139, 139, 152, -1, -1, -1,
            -1, -1, -1, -1, 139, 139, 139, 139,
            139, 139, 139, 139, 139, 139, 17, 18,
            139, 139, 139, 139, 139, 139, 139, 187,
            -1, 139, 139 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 309, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 311, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 313, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 315, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 317, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 319, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 321, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 323, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, 139, -1, -1, 139, 149,
            -1, -1, 150, -1, -1, -1, -1, -1,
            151, 139, 139, 139, 139, -1, -1, -1,
            139, 139, 139, 328, 152, -1, -1, -1,
            -1, -1, -1, -1, 139, 139, 139, 139,
            139, 139, 139, 139, 139, 139, 17, 18,
            139, 139, 139, 139, 139, 139, 139, 139,
            -1, 302, 139 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 304, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 316, -1, -1, -1, -1, -1, -1,
            -1, -1, 221 ),
        array( -1, -1, -1, 139, -1, -1, 139, 149,
            -1, -1, 150, -1, -1, -1, -1, -1,
            151, 139, 139, 139, 139, -1, -1, -1,
            139, 139, 139, 139, 152, -1, -1, -1,
            -1, -1, -1, -1, 139, 305, 139, 139,
            139, 139, 139, 139, 139, 139, 17, 18,
            139, 139, 139, 139, 139, 139, 139, 139,
            -1, 139, 139 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 307, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, 139, -1, -1, 139, 149,
            -1, -1, 150, -1, -1, -1, -1, -1,
            151, 139, 139, 139, 139, -1, -1, -1,
            139, 139, 139, 139, 152, -1, -1, -1,
            -1, -1, -1, -1, 139, 139, 308, 139,
            139, 139, 139, 139, 139, 139, 17, 18,
            139, 139, 139, 139, 139, 139, 139, 139,
            -1, 139, 139 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 310, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 312, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 318, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 320, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 322, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 324, -1, -1, -1, -1, -1, -1,
            -1, -1, -1 ),
        array( -1, -1, -1, 139, -1, -1, 139, 149,
            -1, -1, 150, -1, -1, -1, -1, -1,
            151, 139, 139, 139, 139, -1, -1, -1,
            139, 139, 139, 139, 152, -1, -1, -1,
            -1, -1, -1, -1, 139, 139, 139, 139,
            139, 139, 139, 139, 139, 139, 17, 18,
            139, 139, 139, 139, 139, 139, 330, 139,
            -1, 139, 139 ),
        array( -1, -1, -1, 139, -1, -1, 139, 149,
            -1, -1, 150, -1, -1, -1, -1, -1,
            151, 139, 139, 139, 139, -1, -1, -1,
            139, 139, 139, 139, 152, -1, -1, -1,
            -1, -1, -1, -1, 139, 139, 139, 139,
            139, 139, 139, 139, 139, 139, 17, 18,
            139, 139, 139, 139, 139, 337, 139, 139,
            -1, 139, 139 ),
        array( -1, -1, -1, 139, -1, -1, 139, 149,
            -1, -1, 150, -1, -1, -1, -1, -1,
            151, 139, 139, 139, 139, -1, -1, -1,
            139, 139, 139, 139, 152, -1, -1, -1,
            -1, -1, -1, -1, 139, 139, 139, 338,
            139, 139, 139, 139, 139, 139, 17, 18,
            139, 139, 139, 139, 139, 139, 139, 139,
            -1, 139, 139 ),
        array( -1, -1, -1, 139, -1, -1, 139, 149,
            -1, -1, 150, -1, -1, -1, -1, -1,
            151, 139, 139, 139, 139, -1, -1, -1,
            139, 139, 139, 139, 152, -1, -1, -1,
            -1, -1, -1, -1, 139, 139, 139, 139,
            139, 139, 139, 139, 139, 139, 17, 18,
            139, 139, 139, 139, 339, 139, 139, 139,
            -1, 139, 139 )
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
                } else {
                    $yy_anchor = $this->yy_acpt[$yy_last_accept_state];
                    if (0 != (YY_END & $yy_anchor)) {
                        $this->yy_move_end();
                    }
                    $this->yy_to_mark();
                    if ($yy_last_accept_state < 0) {
                       if ($yy_last_accept_state < 341) {
                           $this->yy_error(YY_E_INTERNAL, false);
                       }
                    } else {

                        switch ($yy_last_accept_state) {
case 2:
{
    return $this->raiseError("unexpected something: (".$this->yytext() .") character: 0x" . dechex(ord($this->yytext())));
}
case 3:
{
    //abcd -- data characters  
    // { and ) added for flexy
    $this->value = HTML_Template_Flexy_Token::factory('Text',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 4:
{
    // &abc;
    $this->value = HTML_Template_Flexy_Token::factory('Text',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 5:
{
    //<name -- start tag */
    if ($this->ignoreHTML) {
        return $this->returnSimple();
    }
    $this->tagName = trim(substr($this->yytext(),1));
    $this->tokenName = 'Tag';
    $this->value = '';
    $this->attributes = array();
    $this->yybegin(IN_ATTR);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 6:
{  
    // <> -- empty start tag */
    if ($this->ignoreHTML) {
        return $this->returnSimple();
    }
    return $this->raiseError("empty tag"); 
}
case 7:
{ 
    /* <? php start.. */
    $this->yyPhpBegin = $this->yy_buffer_end -2;
    $this->yybegin(IN_PHP);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 8:
{
    // &#123;
    $this->value = HTML_Template_Flexy_Token::factory('Text',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 9:
{
    // &#abc;
    $this->value = HTML_Template_Flexy_Token::factory('Text',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 10:
{
    /* </title> -- end tag */
    if ($this->ignoreHTML) {
        return $this->returnSimple();
    }
    $this->tagName = trim(substr($this->yytext(),1));
    $this->tokenName = 'EndTag';
    $this->yybegin(IN_ENDTAG);
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 11:
{
    /* </> -- empty end tag */  
    if ($this->ignoreHTML) {
        return $this->returnSimple();
    }
    return $this->raiseError("empty end tag not handled");
}
case 12:
{
    /* <!DOCTYPE -- markup declaration */
    if ($this->ignoreHTML) {
        return $this->returnSimple();
    }
    $this->value = HTML_Template_Flexy_Token::factory('Doctype',$this->yytext(),$this->yyline);
    $this->yybegin(IN_MD);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 13:
{
    /* <!> */
    if ($this->ignoreHTML) {
        return $this->returnSimple();
    }
    return $this->raiseError("empty markup tag not handled"); 
}
case 14:
{
    /* <![ -- marked section */
    return $this->returnSimple();
}
case 15:
{
    $this->value = HTML_Template_Flexy_Token::factory('GetTextEnd','',$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 16:
{ 
    /* ]]> -- marked section end */
    return $this->returnSimple();
}
case 17:
{
    $this->value =  '';
    $this->flexyMethod = substr($this->yytext(),1,-1);
    $this->flexyArgs = array();
    $this->yybegin(IN_FLEXYMETHOD);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 18:
{
    $t =  $this->yytext();
    $t = substr($t,1,-1);
    $this->value = HTML_Template_Flexy_Token::factory('Var'  , $t, $this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 19:
{
    $this->value = HTML_Template_Flexy_Token::factory('GetTextStart','',$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 20:
{
    if ($this->ignoreHTML) {
        return $this->returnSimple();
    }
    /* </name <  -- unclosed end tag */
    return $this->raiseError("Unclosed  end tag");
}
case 21:
{
    /* <!--  -- comment declaration */
    if ($this->ignoreHTML) {
        return $this->returnSimple();
    }
    $this->yyCommentBegin = $this->yy_buffer_end;
    //$this->value = HTML_Template_Flexy_Token::factory('Comment',$this->yytext(),$this->yyline);
    $this->yybegin(IN_COM);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 22:
{ 
    /* <?xml ...> -- processing instruction */
    $t = $this->yytext();
    $this->value = HTML_Template_Flexy_Token::factory('Processing',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 23:
{
    $this->value =  '';
    $this->flexyMethod = substr($this->yytext(),1,-1);
    $this->flexyArgs = array();
    $this->yybegin(IN_FLEXYMETHOD);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 24:
{
    $this->value = HTML_Template_Flexy_Token::factory('If',substr($this->yytext(),4,-1),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 25:
{
    $this->value = HTML_Template_Flexy_Token::factory('End', '',$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 26:
{
    $this->value = HTML_Template_Flexy_Token::factory('Else', '',$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 27:
{
    /* <![ -- marked section */
    $this->yybegin(IN_CDATA);
    $this->yyCdataBegin = $this->yy_buffer_end;
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 28:
{
    return $this->raiseError('invalid sytnax for Foreach','',true);
}
case 29:
{
    $this->value = HTML_Template_Flexy_Token::factory('Foreach', explode(',',substr($this->yytext(),9,-1)),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 30:
{
    $this->value = HTML_Template_Flexy_Token::factory('Foreach',  explode(',',substr($this->yytext(),9,-1)),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 31:
{
    $this->attrVal[] = $this->yytext();
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 32:
{
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
case 33:
{
    $this->value =  '';
    $n = $this->yytext();
    if ($n{0} != "{") {
        $n = substr($n,2);
    }
    $this->flexyMethod = substr($n,1,-1);
    $this->flexyArgs = array();
    $this->flexyMethodState = $this->yy_lexical_state;
    $this->yybegin(IN_FLEXYMETHODQUOTED);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 34:
{
    $n = $this->yytext();
    if ($n{0} != '{') {
        $n = substr($n,3);
    } else {
        $n = substr($n,1);
    }
    if ($n{strlen($n)-1} != '}') {
        $n = substr($n,0,-3);
    } else {
        $n = substr($n,0,-1);
    }
    $this->attrVal[] = HTML_Template_Flexy_Token::factory('Var'  , $n, $this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 35:
{
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 36:
{
    // <foo^<bar> -- unclosed start tag */
    return $this->raiseError("Unclosed tags not supported"); 
}
case 37:
{
    $this->value = HTML_Template_Flexy_Token::factory($this->tokenName,
        array($this->tagName,$this->attributes),
        $this->yyline);
    if (strtoupper($this->tagName) == 'SCRIPT') {
        $this->yybegin(IN_SCRIPT);
        return HTML_TEMPLATE_FLEXY_TOKEN_OK;
    }
    $this->yybegin(YYINITIAL);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 38:
{
    // <img src="xxx" ...ismap...> the ismap */
    $this->attributes[trim($this->yytext())] = true;
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 39:
{
    $this->yybegin(IN_NETDATA);
    $this->attributes["/"] = true;
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 40:
{
   // <a ^href = "xxx"> -- attribute name 
    $this->attrKey = substr(trim($this->yytext()),0,-1);
    $this->yybegin(IN_ATTRVAL);
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 41:
{
    $this->attributes["/"] = true;
    $this->value = HTML_Template_Flexy_Token::factory($this->tokenName,
        array($this->tagName,$this->attributes),
        $this->yyline);
    $this->yybegin(YYINITIAL);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 42:
{
    // <a href = ^http://foo/> -- unquoted literal HACK */                          
    $this->attributes[$this->attrKey] = trim($this->yytext());
    $this->yybegin(IN_ATTR);
    //   $this->raiseError("attribute value needs quotes");
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 43:
{
    // <a name = ^12pt> -- number token */
    $this->attributes[$this->attrKey] = trim($this->yytext());
    $this->yybegin(IN_ATTR);
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 44:
{
    return $this->raiseError("attribute value missing"); 
}
case 45:
{ 
    return $this->raiseError("Tag close found where attribute value expected"); 
}
case 46:
{
	//echo "STARTING SINGLEQUOTE";
    $this->attrVal = array( "'");
    $this->yybegin(IN_SINGLEQUOTE);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 47:
{
    //echo "START QUOTE";
    $this->attrVal =array("\"");
    $this->yybegin(IN_DOUBLEQUOTE);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 48:
{ 
    // whitespace switch back to IN_ATTR MODE.
    $this->value = '';
    $this->yybegin(IN_ATTR);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 49:
{ 
    return $this->raiseError("extraneous character in end tag"); 
}
case 50:
{ 
    $this->value = HTML_Template_Flexy_Token::factory($this->tokenName,
        array($this->tagName),
        $this->yyline);
        array($this->tagName);
    $this->yybegin(YYINITIAL);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 51:
{
    //echo "GOT DATA:".$this->yytext();
    $this->attrVal[] = $this->yytext();
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 52:
{
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
case 53:
{ 
    $this->value = HTML_Template_Flexy_Token::factory('WhiteSpace',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 54:
{
    return $this->raiseError("illegal character in markup declaration");
}
case 55:
{   
    $this->value = HTML_Template_Flexy_Token::factory('Number',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 56:
{ 
    $this->value = HTML_Template_Flexy_Token::factory('Name',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 57:
{ 
    $this->value = HTML_Template_Flexy_Token::factory('NameT',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 58:
{   
    $this->value = HTML_Template_Flexy_Token::factory('CloseTag',$this->yytext(),$this->yyline);
    $this->yybegin(YYINITIAL); 
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 59:
{
    // <!doctype foo ^[  -- declaration subset */
    $this->value = HTML_Template_Flexy_Token::factory('BeginDS',$this->yytext(),$this->yyline);
    $this->yybegin(IN_DS);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 60:
{ 
    $this->value = HTML_Template_Flexy_Token::factory('NumberT',$this->yytext(),$this->yyline);    
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 61:
{
    // <!entity ^% foo system "..." ...> -- parameter entity definition */
    $this->value = HTML_Template_Flexy_Token::factory('EntityPar',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 62:
{
    // <!doctype ^%foo;> -- parameter entity reference */
    $this->value = HTML_Template_Flexy_Token::factory('EntityRef',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 63:
{ 
    $this->value = HTML_Template_Flexy_Token::factory('Literal',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 64:
{
    // inside a comment (not - or not --
    // <!^--...-->   -- comment */   
    //$this->value = HTML_Template_Flexy_Token::factory('Comment',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 65:
{
	// inside comment -- without a >
	return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 66:
{   
    $this->value = HTML_Template_Flexy_Token::factory('Comment',
        '<!--'. substr($this->yy_buffer,$this->yyCommentBegin ,$this->yy_buffer_end - $this->yyCommentBegin),
        $this->yyline
    );
    $this->yybegin(YYINITIAL); 
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 67:
{ 
    $this->value = HTML_Template_Flexy_Token::factory('Declaration',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 68:
{ 
    // ] -- declaration subset close */
    $this->value = HTML_Template_Flexy_Token::factory('DSEndSubset',$this->yytext(),$this->yyline);
    $this->yybegin(IN_DSCOM); 
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 69:
{
    // ]]> -- marked section end */
     $this->value = HTML_Template_Flexy_Token::factory('DSEnd',$this->yytext(),$this->yyline);
    $this->yybegin(YYINITIAL);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 70:
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
case 71:
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
    $this->value = HTML_Template_Flexy_Token::factory('Method'  , array($this->flexyMethod,$this->flexyArgs), $this->yyline);
    $this->yybegin(YYINITIAL);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 72:
{
    $t = $this->yytext();
    if ($t{1} == ':') {
        $this->flexyMethod .= substr($t,1,-1);
    }
    $this->value = HTML_Template_Flexy_Token::factory('Method'  , array($this->flexyMethod,$this->flexyArgs), $this->yyline);
    $this->yybegin(YYINITIAL);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 73:
{
    $t = $this->yytext();
    // add argument
    $this->flexyArgs[] = $t;
    $this->yybegin(IN_FLEXYMETHODQUOTED_END);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 74:
{
    $t = $this->yytext();
    $this->flexyArgs[] =$t;
    $this->yybegin(IN_FLEXYMETHODQUOTED_END);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 75:
{
    $t = $this->yytext();
    if ($p = strpos($t,':')) {
        $this->flexyMethod .= substr($t,$p,2);
    }
    $this->attrVal[] = HTML_Template_Flexy_Token::factory('Method'  , array($this->flexyMethod,$this->flexyArgs), $this->yyline);    
    $this->yybegin($this->flexyMethodState);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 76:
{
    $this->yybegin(IN_FLEXYMETHODQUOTED);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 77:
{
    // general text in script..
    $this->value = HTML_Template_Flexy_Token::factory('Text',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 78:
{
    // just < .. 
    $this->value = HTML_Template_Flexy_Token::factory('Text',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 79:
{
    // </script>
    $this->value = HTML_Template_Flexy_Token::factory('EndTag',
        array('/script'),
        $this->yyline);
    $this->yybegin(YYINITIAL);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 80:
{ 
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 81:
{ 
    /* ]]> -- marked section end */
    $this->value = HTML_Template_Flexy_Token::factory('Cdata',
        substr($this->yy_buffer,$this->yyCdataBegin ,$this->yy_buffer_end - $this->yyCdataBegin - 3 ),
        $this->yyline);
    $this->yybegin(YYINITIAL);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 82:
{
    // inside a comment (not - or not --
    // <!^--...-->   -- comment */   
    $this->value = HTML_Template_Flexy_Token::factory('DSComment',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 83:
{   
    $this->value = HTML_Template_Flexy_Token::factory('DSEnd', $this->yytext(),$this->yyline);
    $this->yybegin(YYINITIAL); 
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 84:
{     
    /* anything inside of php tags */
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 85:
{ 
    /* php end */
    if ($this->ignorePHP) {
        $this->yybegin(YYINITIAL);
        return HTML_TEMPLATE_FLEXY_TOKEN_NONE;    
    }
    $this->value = HTML_Template_Flexy_Token::factory('Text',
        substr($this->yy_buffer,$this->yyPhpBegin ,$this->yy_buffer_end - $this->yyPhpBegin ),
        $this->yyline);
    $this->yybegin(YYINITIAL);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 87:
{
    return $this->raiseError("unexpected something: (".$this->yytext() .") character: 0x" . dechex(ord($this->yytext())));
}
case 88:
{
    //abcd -- data characters  
    // { and ) added for flexy
    $this->value = HTML_Template_Flexy_Token::factory('Text',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 89:
{
    // &abc;
    $this->value = HTML_Template_Flexy_Token::factory('Text',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 90:
{
    //<name -- start tag */
    if ($this->ignoreHTML) {
        return $this->returnSimple();
    }
    $this->tagName = trim(substr($this->yytext(),1));
    $this->tokenName = 'Tag';
    $this->value = '';
    $this->attributes = array();
    $this->yybegin(IN_ATTR);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 91:
{
    // &#123;
    $this->value = HTML_Template_Flexy_Token::factory('Text',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 92:
{
    // &#abc;
    $this->value = HTML_Template_Flexy_Token::factory('Text',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 93:
{
    /* </title> -- end tag */
    if ($this->ignoreHTML) {
        return $this->returnSimple();
    }
    $this->tagName = trim(substr($this->yytext(),1));
    $this->tokenName = 'EndTag';
    $this->yybegin(IN_ENDTAG);
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 94:
{
    /* <!DOCTYPE -- markup declaration */
    if ($this->ignoreHTML) {
        return $this->returnSimple();
    }
    $this->value = HTML_Template_Flexy_Token::factory('Doctype',$this->yytext(),$this->yyline);
    $this->yybegin(IN_MD);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 95:
{
    /* <![ -- marked section */
    return $this->returnSimple();
}
case 96:
{
    $this->attrVal[] = $this->yytext();
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 97:
{
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 98:
{
    // <foo^<bar> -- unclosed start tag */
    return $this->raiseError("Unclosed tags not supported"); 
}
case 99:
{
    // <img src="xxx" ...ismap...> the ismap */
    $this->attributes[trim($this->yytext())] = true;
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 100:
{
    // <a href = ^http://foo/> -- unquoted literal HACK */                          
    $this->attributes[$this->attrKey] = trim($this->yytext());
    $this->yybegin(IN_ATTR);
    //   $this->raiseError("attribute value needs quotes");
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 101:
{
    // <a name = ^12pt> -- number token */
    $this->attributes[$this->attrKey] = trim($this->yytext());
    $this->yybegin(IN_ATTR);
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 102:
{
    //echo "GOT DATA:".$this->yytext();
    $this->attrVal[] = $this->yytext();
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 103:
{ 
    $this->value = HTML_Template_Flexy_Token::factory('WhiteSpace',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 104:
{
    return $this->raiseError("illegal character in markup declaration");
}
case 105:
{   
    $this->value = HTML_Template_Flexy_Token::factory('Number',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 106:
{ 
    $this->value = HTML_Template_Flexy_Token::factory('Name',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 107:
{ 
    $this->value = HTML_Template_Flexy_Token::factory('NameT',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 108:
{ 
    $this->value = HTML_Template_Flexy_Token::factory('NumberT',$this->yytext(),$this->yyline);    
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 109:
{
    // <!doctype ^%foo;> -- parameter entity reference */
    $this->value = HTML_Template_Flexy_Token::factory('EntityRef',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 110:
{ 
    $this->value = HTML_Template_Flexy_Token::factory('Literal',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 111:
{
    // inside a comment (not - or not --
    // <!^--...-->   -- comment */   
    //$this->value = HTML_Template_Flexy_Token::factory('Comment',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 112:
{
	// inside comment -- without a >
	return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 113:
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
case 114:
{
    $t = $this->yytext();
    // add argument
    $this->flexyArgs[] = $t;
    $this->yybegin(IN_FLEXYMETHODQUOTED_END);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 115:
{ 
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 116:
{
    // inside a comment (not - or not --
    // <!^--...-->   -- comment */   
    $this->value = HTML_Template_Flexy_Token::factory('DSComment',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 117:
{     
    /* anything inside of php tags */
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 119:
{
    return $this->raiseError("unexpected something: (".$this->yytext() .") character: 0x" . dechex(ord($this->yytext())));
}
case 120:
{
    //abcd -- data characters  
    // { and ) added for flexy
    $this->value = HTML_Template_Flexy_Token::factory('Text',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 121:
{
    $this->attrVal[] = $this->yytext();
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 122:
{
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 123:
{
    // <a name = ^12pt> -- number token */
    $this->attributes[$this->attrKey] = trim($this->yytext());
    $this->yybegin(IN_ATTR);
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 124:
{
    //echo "GOT DATA:".$this->yytext();
    $this->attrVal[] = $this->yytext();
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 125:
{
    return $this->raiseError("illegal character in markup declaration");
}
case 126:
{ 
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 127:
{     
    /* anything inside of php tags */
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 129:
{
    return $this->raiseError("unexpected something: (".$this->yytext() .") character: 0x" . dechex(ord($this->yytext())));
}
case 130:
{
    //abcd -- data characters  
    // { and ) added for flexy
    $this->value = HTML_Template_Flexy_Token::factory('Text',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 131:
{
    return $this->raiseError("illegal character in markup declaration");
}
case 133:
{
    return $this->raiseError("unexpected something: (".$this->yytext() .") character: 0x" . dechex(ord($this->yytext())));
}
case 134:
{
    return $this->raiseError("illegal character in markup declaration");
}
case 136:
{
    return $this->raiseError("unexpected something: (".$this->yytext() .") character: 0x" . dechex(ord($this->yytext())));
}
case 138:
{
    return $this->raiseError("unexpected something: (".$this->yytext() .") character: 0x" . dechex(ord($this->yytext())));
}
case 140:
{
    return $this->raiseError("unexpected something: (".$this->yytext() .") character: 0x" . dechex(ord($this->yytext())));
}
case 142:
{
    return $this->raiseError("unexpected something: (".$this->yytext() .") character: 0x" . dechex(ord($this->yytext())));
}
case 144:
{
    return $this->raiseError("unexpected something: (".$this->yytext() .") character: 0x" . dechex(ord($this->yytext())));
}
case 146:
{
    return $this->raiseError("unexpected something: (".$this->yytext() .") character: 0x" . dechex(ord($this->yytext())));
}
case 290:
{
    //abcd -- data characters  
    // { and ) added for flexy
    $this->value = HTML_Template_Flexy_Token::factory('Text',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 291:
{
    // <a name = ^12pt> -- number token */
    $this->attributes[$this->attrKey] = trim($this->yytext());
    $this->yybegin(IN_ATTR);
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 292:
{
    $t = $this->yytext();
    // add argument
    $this->flexyArgs[] = $t;
    $this->yybegin(IN_FLEXYMETHODQUOTED_END);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 293:
{
    $t = $this->yytext();
    // add argument
    $this->flexyArgs[] = $t;
    $this->yybegin(IN_FLEXYMETHODQUOTED_END);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}

                        }
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
