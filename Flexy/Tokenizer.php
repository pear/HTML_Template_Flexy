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
define("IN_FLEXYMETHODQUOTED"     ,12);
define("IN_FLEXYMETHODQUOTED_END"     ,13);
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
    * the name of the file being parsed (used by error messages)
    *
    * @var string
    * @access public
    */
    var $fileName;
    function dump () {
        foreach(get_object_vars($this) as  $k=>$v) {
            if (is_string($v)) { continue; }
            if (is_array($v)) { continue; }
            echo "$k = $v\n";
        }
    }
    function raiseError($s,$n='',$isFatal=false) {
        echo "ERROR $n in File {$this->fileName} on Line {$this->yyline} Position:{$this->yy_buffer_end}: $s\n";
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
        179,
        29,
        101,
        182,
        183,
        184,
        185,
        49,
        60,
        216,
        218,
        237,
        252
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
        /* 72 */   YY_NOT_ACCEPT,
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
        /* 98 */   YY_NOT_ACCEPT,
        /* 99 */   YY_NO_ANCHOR,
        /* 100 */   YY_NO_ANCHOR,
        /* 101 */   YY_NO_ANCHOR,
        /* 102 */   YY_NO_ANCHOR,
        /* 103 */   YY_NO_ANCHOR,
        /* 104 */   YY_NO_ANCHOR,
        /* 105 */   YY_NO_ANCHOR,
        /* 106 */   YY_NOT_ACCEPT,
        /* 107 */   YY_NO_ANCHOR,
        /* 108 */   YY_NO_ANCHOR,
        /* 109 */   YY_NOT_ACCEPT,
        /* 110 */   YY_NO_ANCHOR,
        /* 111 */   YY_NO_ANCHOR,
        /* 112 */   YY_NOT_ACCEPT,
        /* 113 */   YY_NO_ANCHOR,
        /* 114 */   YY_NOT_ACCEPT,
        /* 115 */   YY_NO_ANCHOR,
        /* 116 */   YY_NOT_ACCEPT,
        /* 117 */   YY_NO_ANCHOR,
        /* 118 */   YY_NOT_ACCEPT,
        /* 119 */   YY_NO_ANCHOR,
        /* 120 */   YY_NOT_ACCEPT,
        /* 121 */   YY_NO_ANCHOR,
        /* 122 */   YY_NOT_ACCEPT,
        /* 123 */   YY_NOT_ACCEPT,
        /* 124 */   YY_NOT_ACCEPT,
        /* 125 */   YY_NOT_ACCEPT,
        /* 126 */   YY_NOT_ACCEPT,
        /* 127 */   YY_NOT_ACCEPT,
        /* 128 */   YY_NOT_ACCEPT,
        /* 129 */   YY_NOT_ACCEPT,
        /* 130 */   YY_NOT_ACCEPT,
        /* 131 */   YY_NOT_ACCEPT,
        /* 132 */   YY_NOT_ACCEPT,
        /* 133 */   YY_NOT_ACCEPT,
        /* 134 */   YY_NOT_ACCEPT,
        /* 135 */   YY_NOT_ACCEPT,
        /* 136 */   YY_NOT_ACCEPT,
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
        /* 253 */   YY_NO_ANCHOR,
        /* 254 */   YY_NO_ANCHOR,
        /* 255 */   YY_NO_ANCHOR,
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
        /* 290 */   YY_NOT_ACCEPT,
        /* 291 */   YY_NOT_ACCEPT,
        /* 292 */   YY_NOT_ACCEPT,
        /* 293 */   YY_NOT_ACCEPT,
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
        /* 313 */   YY_NOT_ACCEPT
        );


    var  $yy_cmap = array(
        25, 25, 25, 25, 25, 25, 25, 25,
        25, 11, 5, 25, 25, 12, 25, 25,
        25, 25, 25, 25, 25, 25, 25, 25,
        25, 25, 25, 25, 25, 25, 25, 25,
        11, 14, 24, 2, 26, 20, 1, 23,
        35, 47, 26, 26, 42, 15, 7, 9,
        3, 3, 3, 3, 3, 30, 3, 46,
        3, 3, 10, 4, 8, 22, 13, 18,
        25, 6, 31, 6, 32, 6, 6, 6,
        6, 6, 6, 6, 6, 6, 6, 6,
        6, 6, 6, 6, 6, 6, 6, 6,
        6, 6, 6, 16, 21, 17, 25, 29,
        25, 39, 31, 40, 33, 38, 28, 6,
        41, 27, 6, 6, 44, 6, 43, 36,
        6, 6, 37, 45, 6, 6, 6, 6,
        6, 6, 6, 19, 25, 34, 25, 25,
        25, 25, 25, 25, 25, 25, 25, 25,
        25, 25, 25, 25, 25, 25, 25, 25,
        25, 25, 25, 25, 25, 25, 25, 25,
        25, 25, 25, 25, 25, 25, 25, 25,
        25, 25, 25, 25, 25, 25, 25, 25,
        25, 25, 25, 25, 25, 25, 25, 25,
        25, 25, 25, 25, 25, 25, 25, 25,
        25, 25, 25, 25, 25, 25, 25, 25,
        25, 25, 25, 25, 25, 25, 25, 25,
        25, 25, 25, 25, 25, 25, 25, 25,
        25, 25, 25, 25, 25, 25, 25, 25,
        25, 25, 25, 25, 25, 25, 25, 25,
        25, 25, 25, 25, 25, 25, 25, 25,
        25, 25, 25, 25, 25, 25, 25, 25,
        25, 25, 25, 25, 25, 25, 25, 25,
        25, 25, 25, 25, 25, 25, 25, 25,
        25, 0, 0 
         );


    var $yy_rmap = array(
        0, 1, 2, 3, 4, 5, 1, 6,
        7, 8, 1, 9, 1, 10, 1, 3,
        1, 1, 1, 1, 1, 1, 1, 1,
        1, 1, 1, 11, 1, 12, 1, 1,
        13, 14, 15, 1, 16, 17, 16, 1,
        1, 1, 18, 1, 1, 19, 1, 1,
        1, 20, 1, 21, 22, 23, 1, 1,
        24, 25, 26, 27, 28, 1, 29, 30,
        1, 31, 1, 1, 32, 1, 1, 1,
        33, 34, 35, 1, 36, 1, 1, 37,
        38, 39, 16, 40, 41, 42, 43, 44,
        45, 46, 47, 48, 49, 50, 1, 51,
        1, 52, 53, 54, 55, 56, 57, 58,
        59, 60, 61, 1, 62, 63, 64, 65,
        66, 67, 68, 69, 70, 71, 72, 73,
        74, 75, 76, 77, 78, 79, 80, 81,
        82, 83, 84, 85, 86, 87, 88, 89,
        90, 91, 92, 93, 94, 95, 96, 97,
        98, 99, 100, 101, 102, 103, 104, 105,
        106, 107, 108, 109, 110, 111, 112, 113,
        114, 115, 116, 117, 118, 119, 120, 121,
        122, 123, 124, 125, 126, 127, 128, 129,
        130, 131, 132, 133, 54, 14, 134, 135,
        136, 137, 64, 138, 139, 140, 141, 142,
        143, 144, 145, 146, 147, 148, 149, 150,
        151, 152, 153, 154, 155, 156, 157, 158,
        159, 160, 161, 162, 59, 62, 163, 164,
        165, 166, 167, 67, 69, 168, 169, 170,
        171, 172, 173, 174, 175, 176, 177, 178,
        179, 180, 181, 182, 183, 184, 73, 185,
        186, 187, 188, 189, 190, 191, 192, 193,
        194, 195, 196, 197, 198, 199, 57, 200,
        201, 202, 87, 203, 204, 205, 206, 207,
        208, 209, 43, 97, 210, 211, 212, 103,
        213, 214, 215, 216, 118, 217, 123, 218,
        141, 219, 147, 220, 155, 221, 169, 222,
        175, 223, 186, 224, 192, 225, 226, 227,
        228, 215, 229, 230, 231, 232, 233, 234,
        235, 236, 237, 238, 239, 240, 241, 242,
        243, 244 
        );


    var $yy_nxt = array(
        array( 1, 2, 3, 3, 3, 3, 3, 3,
            73, 3, 3, 3, 3, 3, 3, 3,
            3, 253, 3, 74, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 3 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, 72, 3, 3, 3, 4, 3,
            -1, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, 4, 4, 3, 3, 4,
            4, 4, 3, 3, 4, 4, 4, 4,
            4, 4, 3, 4, 4, 4, 3, 3 ),
        array( -1, 98, 3, 3, 3, 3, 3, 3,
            106, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, -1, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 3 ),
        array( -1, -1, -1, 4, 75, 75, 4, 4,
            -1, -1, -1, -1, -1, -1, -1, 4,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 4, 4, -1, 4, 4,
            4, 4, -1, -1, 4, 4, 4, 4,
            4, 4, -1, 4, 4, 4, 4, -1 ),
        array( -1, -1, -1, 5, -1, 76, 5, 5,
            -1, -1, 5, 76, 76, -1, -1, 5,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 5, 5, -1, 5, 5,
            5, 5, -1, -1, 5, 5, 5, 5,
            5, 5, -1, 5, 5, 5, 5, -1 ),
        array( -1, -1, -1, 7, 77, 77, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 7, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 7, -1 ),
        array( -1, -1, -1, 8, 78, 78, 8, 8,
            -1, -1, -1, -1, -1, -1, -1, 8,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 8, 8, -1, 8, 8,
            8, 8, -1, -1, 8, 8, 8, 8,
            8, 8, -1, 8, 8, 8, 8, -1 ),
        array( -1, -1, -1, 9, -1, 79, 9, 9,
            -1, 120, 9, 79, 79, -1, -1, 9,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 9, 9, -1, 9, 9,
            9, 9, -1, -1, 9, 9, 9, 9,
            9, 9, -1, 9, 9, 9, 9, -1 ),
        array( -1, -1, -1, 11, -1, 80, 11, 11,
            -1, -1, -1, 80, 80, -1, -1, 11,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 11, 11, -1, 11, 11,
            11, 11, -1, -1, 11, 11, 11, 11,
            11, 11, -1, 11, 11, 11, 11, -1 ),
        array( -1, -1, -1, -1, -1, 13, -1, -1,
            -1, -1, -1, 13, 13, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, 27, 27, 27, 27, 27, 27, 27,
            27, 27, 27, 27, 27, 27, 27, 27,
            27, 27, 27, 27, 27, 180, 27, -1,
            27, 27, 27, 27, 27, 27, 27, 27,
            27, 27, 27, 27, 27, 27, 27, 27,
            27, 27, 27, 27, 27, 27, 27, 27 ),
        array( 1, 107, 107, 107, 107, 81, 107, 107,
            30, 107, 107, 81, 81, 31, 107, 107,
            107, 107, 107, 107, 107, 107, 107, 107,
            107, 107, 107, 107, 107, 107, 107, 107,
            107, 107, 107, 107, 107, 107, 107, 107,
            107, 107, 107, 107, 107, 107, 107, 107 ),
        array( -1, -1, -1, 32, -1, 83, 32, 32,
            -1, -1, 32, 83, 83, -1, -1, 32,
            -1, -1, -1, -1, -1, -1, 34, -1,
            -1, -1, -1, 32, 32, -1, 32, 32,
            32, 32, -1, -1, 32, 32, 32, 32,
            32, 32, -1, 32, 32, 32, 32, -1 ),
        array( -1, -1, -1, -1, -1, 181, -1, -1,
            -1, -1, -1, 181, 181, 35, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 34, -1, -1,
            -1, -1, -1, 34, 34, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, 36, 36, 36, 36, 84, 36, 36,
            36, 36, 36, 84, 84, -1, 36, 36,
            36, 36, 36, 36, 36, 36, 36, -1,
            -1, 36, 36, 36, 36, 36, 36, 36,
            36, 36, 36, 36, 36, 36, 36, 36,
            36, 36, 36, 36, 36, 36, 36, 36 ),
        array( -1, 36, 36, 37, 36, 85, 37, 37,
            36, 36, 36, 85, 85, -1, 36, 37,
            36, 36, 36, 36, 36, 36, 36, -1,
            -1, 36, 36, 37, 37, 36, 37, 37,
            37, 37, 36, 36, 37, 37, 37, 37,
            37, 37, 36, 37, 37, 37, 37, 36 ),
        array( -1, -1, -1, -1, -1, 42, -1, -1,
            -1, -1, -1, 42, 42, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, 45, 45, 45, 45, 45, 45, 45,
            45, 45, 45, 45, 45, 45, 45, 45,
            45, 45, 45, -1, -1, 186, 45, 45,
            -1, 45, 45, 45, 45, 45, 45, 45,
            45, 45, 45, 45, 45, 45, 45, 45,
            45, 45, 45, 45, 45, 45, 45, 45 ),
        array( 1, 50, 50, 51, 50, 87, 52, 53,
            50, 50, 50, 87, 87, 54, 50, 53,
            55, 50, 50, 50, 88, 50, 50, 104,
            108, 50, 50, 52, 52, 50, 51, 52,
            52, 52, 50, 50, 52, 52, 52, 52,
            52, 52, 50, 52, 52, 52, 51, 50 ),
        array( -1, -1, -1, 51, -1, 89, 56, 56,
            -1, -1, -1, 89, 89, -1, -1, 56,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 56, 56, -1, 51, 56,
            56, 56, -1, -1, 56, 56, 56, 56,
            56, 56, -1, 56, 56, 56, 51, -1 ),
        array( -1, -1, -1, 52, -1, 90, 52, 52,
            -1, -1, -1, 90, 90, -1, -1, 52,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 52, 52, -1, 52, 52,
            52, 52, -1, -1, 52, 52, 52, 52,
            52, 52, -1, 52, 52, 52, 52, -1 ),
        array( -1, -1, -1, 53, -1, 91, 53, 53,
            -1, -1, -1, 91, 91, -1, -1, 53,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 53, 53, -1, 53, 53,
            53, 53, -1, -1, 53, 53, 53, 53,
            53, 53, -1, 53, 53, 53, 53, -1 ),
        array( -1, -1, -1, 56, -1, 92, 56, 56,
            -1, -1, -1, 92, 92, -1, -1, 56,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 56, 56, -1, 56, 56,
            56, 56, -1, -1, 56, 56, 56, 56,
            56, 56, -1, 56, 56, 56, 56, -1 ),
        array( -1, -1, -1, -1, -1, 57, -1, -1,
            -1, -1, -1, 57, 57, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 58, 93, 93, 58, 58,
            -1, -1, -1, 93, 93, -1, -1, 58,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 58, 58, -1, 58, 58,
            58, 58, -1, -1, 58, 58, 58, 58,
            58, 58, -1, 58, 58, 58, 58, -1 ),
        array( -1, -1, -1, -1, -1, 59, -1, -1,
            -1, -1, -1, 59, 59, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( 1, 95, 95, 95, 95, 95, 95, 95,
            95, 95, 95, 95, 95, 95, 95, 111,
            95, 95, 95, 95, 95, 95, 95, 95,
            95, 95, 95, 95, 95, 95, 95, 95,
            95, 95, 95, 95, 95, 95, 95, 95,
            95, 95, 95, 95, 95, 95, 95, 95 ),
        array( -1, 62, 62, 62, 62, 62, 62, 62,
            62, 62, 62, 62, 62, 62, 62, 62,
            62, -1, 62, 62, 62, 62, 62, 62,
            62, 62, 62, 62, 62, 62, 62, 62,
            62, 62, 62, 62, 62, 62, 62, 62,
            62, 62, 62, 62, 62, 62, 62, 62 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 217, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 96, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 68, -1, -1, 68, 239,
            -1, -1, -1, -1, -1, -1, -1, -1,
            240, -1, -1, -1, 308, -1, -1, -1,
            -1, -1, -1, 68, 68, 68, 68, 68,
            68, 68, -1, -1, 68, 68, 68, 68,
            68, 68, -1, 68, 68, 68, 68, -1 ),
        array( -1, -1, -1, 7, -1, -1, 8, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 8, 8, -1, 7, 8,
            8, 8, -1, -1, 8, 8, 8, 8,
            8, 8, -1, 8, 8, 8, 7, -1 ),
        array( -1, -1, -1, -1, -1, 3, 5, -1,
            -1, 109, -1, 3, 3, 6, 112, -1,
            3, 3, 114, 3, -1, 3, -1, -1,
            -1, 3, -1, 5, 5, 3, -1, 5,
            5, 5, 3, -1, 5, 5, 5, 5,
            5, 5, -1, 5, 5, 5, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 116, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 256, 313, -1, -1, 116,
            116, 116, -1, -1, 116, 116, 294, 116,
            116, 116, -1, 116, 116, 116, -1, -1 ),
        array( -1, -1, -1, -1, -1, 76, -1, -1,
            -1, -1, -1, 76, 76, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 79, -1, -1,
            -1, 120, -1, 79, 79, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 80, -1, -1,
            -1, -1, -1, 80, 80, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 81, -1, -1,
            -1, -1, -1, 81, 81, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 83, -1, -1,
            -1, -1, -1, 83, 83, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 34, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 84, -1, -1,
            -1, -1, -1, 84, 84, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 85, -1, -1,
            -1, -1, -1, 85, 85, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 187, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 187, 187, -1, -1, 187,
            187, 187, -1, -1, 187, 187, 187, 187,
            187, 187, -1, 187, 187, 187, -1, -1 ),
        array( -1, -1, -1, -1, -1, 87, -1, -1,
            -1, -1, -1, 87, 87, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 57, 58, -1,
            -1, -1, -1, 57, 57, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 58, 58, -1, -1, 58,
            58, 58, -1, -1, 58, 58, 58, 58,
            58, 58, -1, 58, 58, 58, -1, -1 ),
        array( -1, -1, -1, -1, -1, 89, -1, -1,
            -1, -1, -1, 89, 89, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 90, -1, -1,
            -1, -1, -1, 90, 90, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 91, -1, -1,
            -1, -1, -1, 91, 91, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 92, -1, -1,
            -1, -1, -1, 92, 92, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 93, -1, -1,
            -1, -1, -1, 93, 93, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, 95, 95, 95, 95, 95, 95, 95,
            95, 95, 95, 95, 95, 95, 95, 214,
            95, 95, 95, 95, 95, 95, 95, 95,
            95, 95, 95, 95, 95, 95, 95, 95,
            95, 95, 95, 95, 95, 95, 95, 95,
            95, 95, 95, 95, 95, 95, 95, 95 ),
        array( -1, -1, -1, -1, -1, -1, -1, 239,
            -1, -1, -1, -1, -1, -1, -1, -1,
            240, -1, -1, -1, 308, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 3, 3, 3, -1, 3,
            -1, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, -1, -1, 3, 3, -1,
            -1, -1, 3, 3, -1, -1, -1, -1,
            -1, -1, 3, -1, -1, -1, 3, 3 ),
        array( -1, 27, 27, 27, 27, 27, 27, 27,
            27, 27, 27, 27, 27, 27, 27, 27,
            27, 27, 27, 27, 27, 27, 27, -1,
            27, 27, 27, 27, 27, 27, 27, 27,
            27, 27, 27, 27, 27, 27, 27, 27,
            27, 27, 27, 27, 27, 27, 27, 27 ),
        array( -1, 98, 3, 3, 3, 3, 3, 3,
            106, 3, 3, 3, 3, 15, 3, 3,
            3, 3, 3, -1, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 3 ),
        array( 1, 107, 107, 107, 107, 81, 32, 107,
            30, 33, 107, 81, 81, 31, 107, 107,
            107, 107, 107, 107, 107, 107, 107, 107,
            107, 107, 107, 32, 32, 107, 107, 32,
            32, 32, 107, 107, 32, 32, 32, 32,
            32, 32, 107, 32, 32, 32, 107, 107 ),
        array( -1, 36, 36, 102, 36, 85, 102, 102,
            36, 36, 36, 85, 85, -1, 36, 102,
            36, 36, 36, 36, 36, 36, 36, -1,
            -1, 36, 36, 102, 102, 36, 102, 102,
            102, 102, 36, 36, 102, 102, 102, 102,
            102, 102, 36, 102, 102, 102, 102, 36 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 188, -1 ),
        array( -1, 212, 212, 212, 212, 212, 212, 212,
            212, 212, 212, 212, 212, 212, 212, 212,
            212, 212, 212, 212, 212, 212, 212, 59,
            212, 212, 212, 212, 212, 212, 212, 212,
            212, 212, 212, 212, 212, 212, 212, 212,
            212, 212, 212, 212, 212, 212, 212, 212 ),
        array( -1, -1, -1, -1, -1, -1, -1, 239,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 3, -1, -1,
            -1, -1, -1, 3, 3, -1, -1, -1,
            3, 3, -1, 3, -1, 3, -1, -1,
            -1, 3, -1, -1, -1, 3, -1, -1,
            -1, -1, 3, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, 213, 213, 213, 213, 213, 213, 213,
            213, 213, 213, 213, 213, 213, 213, 213,
            213, 213, 213, 213, 213, 213, 213, 213,
            94, 213, 213, 213, 213, 213, 213, 213,
            213, 213, 213, 213, 213, 213, 213, 213,
            213, 213, 213, 213, 213, 213, 213, 213 ),
        array( -1, -1, -1, -1, -1, 118, 9, -1,
            -1, 120, -1, 118, 118, 10, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 9, 9, -1, -1, 9,
            9, 9, -1, -1, 9, 9, 9, 9,
            9, 9, -1, 9, 9, 9, -1, -1 ),
        array( -1, 45, 45, 45, 45, 45, 45, 45,
            45, 45, 45, 45, 45, 45, 45, 45,
            45, 45, 45, 45, 45, -1, 45, 45,
            -1, 45, 45, 45, 45, 45, 45, 45,
            45, 45, 45, 45, 45, 45, 45, 45,
            45, 45, 45, 45, 45, 45, 45, 45 ),
        array( -1, 95, 95, 95, 95, 95, 95, 95,
            95, 95, 95, 95, 95, 95, 95, 215,
            95, 95, 95, 95, 95, 95, 95, 95,
            95, 95, 95, 95, 95, 95, 95, 95,
            95, 95, 95, 95, 95, 95, 95, 95,
            95, 95, 95, 95, 95, 95, 95, 95 ),
        array( -1, -1, -1, -1, -1, -1, 11, -1,
            -1, -1, -1, -1, -1, 12, -1, 122,
            13, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 11, 11, -1, -1, 11,
            11, 11, -1, -1, 11, 11, 11, 11,
            11, 11, -1, 11, 11, 11, -1, -1 ),
        array( -1, 219, 65, 219, 219, 219, 219, 219,
            219, 219, 219, 219, 219, 219, 219, 219,
            219, 219, 219, 219, 219, 219, 219, 219,
            219, 219, 219, 219, 219, 219, 219, 219,
            219, 219, 219, 219, 219, 219, 219, 219,
            219, 219, 219, 219, 219, 219, 219, 219 ),
        array( -1, 114, 114, 114, 114, 114, 114, 114,
            114, 114, 114, 114, 114, 14, 114, 114,
            114, 114, 114, 114, 114, 114, 114, 114,
            114, 114, 114, 114, 114, 114, 114, 114,
            114, 114, 114, 114, 114, 114, 114, 114,
            114, 114, 114, 114, 114, 114, 114, 114 ),
        array( -1, -1, -1, 220, -1, -1, 220, 221,
            -1, -1, -1, -1, -1, -1, -1, -1,
            222, -1, -1, -1, 306, -1, -1, -1,
            -1, -1, -1, 220, 220, 220, 220, 220,
            220, 220, -1, -1, 220, 220, 220, 220,
            220, 220, 66, 220, 220, 220, 220, 223 ),
        array( -1, -1, -1, 116, -1, -1, 116, 123,
            -1, -1, 257, -1, -1, -1, -1, -1,
            124, -1, -1, -1, 125, -1, -1, -1,
            -1, -1, -1, 116, 116, 116, 116, 116,
            116, 116, 16, 17, 116, 116, 116, 116,
            116, 116, -1, 116, 116, 116, 116, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 264, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 67, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 118, -1, -1,
            -1, 120, -1, 118, 118, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, 238, 69, 238, 238, 238, 238, 238,
            238, 238, 238, 238, 238, 238, 238, 238,
            238, 238, 238, 238, 238, 238, 238, 238,
            238, 238, 238, 238, 238, 238, 238, 238,
            238, 238, 238, 238, 238, 238, 238, 238,
            238, 238, 238, 238, 238, 238, 238, 238 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            18, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 241, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 242, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 70, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 19,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 127, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 127, 127, -1, -1, 127,
            127, 127, -1, -1, 127, 127, 127, 127,
            127, 127, -1, 127, 127, 127, -1, -1 ),
        array( -1, -1, -1, 129, -1, -1, 129, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 129, 129, 129, 129, 129,
            129, 129, -1, -1, 129, 129, 129, 129,
            129, 129, -1, 129, 129, 129, 129, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 130, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 116, -1, -1, 116, 123,
            -1, -1, 131, -1, -1, -1, -1, -1,
            124, -1, -1, -1, 125, -1, -1, -1,
            -1, -1, -1, 116, 116, 116, 116, 116,
            116, 116, 16, 17, 116, 116, 116, 116,
            116, 116, -1, 116, 116, 116, 116, -1 ),
        array( -1, -1, -1, 127, -1, -1, 127, 123,
            -1, -1, 257, -1, -1, -1, -1, -1,
            133, -1, -1, -1, 134, -1, -1, -1,
            -1, -1, -1, 127, 127, 127, 127, 127,
            127, 127, 16, 17, 127, 127, 127, 127,
            127, 127, -1, 127, 127, 127, 127, -1 ),
        array( -1, -1, -1, -1, -1, -1, 128, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 128, 128, -1, -1, 128,
            128, 128, 16, -1, 128, 128, 128, 128,
            128, 128, -1, 128, 128, 128, -1, -1 ),
        array( -1, -1, -1, 129, -1, -1, 129, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 135, -1, -1, 136, -1, -1, -1,
            -1, -1, -1, 129, 129, 129, 129, 129,
            129, 129, -1, -1, 129, 129, 129, 129,
            129, 129, -1, 129, 129, 129, 129, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 124,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 137, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 137, 137, -1, -1, 137,
            137, 137, -1, -1, 137, 137, 137, 137,
            137, 137, -1, 137, 137, 137, -1, -1 ),
        array( -1, -1, -1, 116, -1, -1, 116, 123,
            -1, -1, 138, -1, -1, -1, -1, -1,
            124, -1, -1, -1, 125, -1, -1, -1,
            -1, -1, -1, 116, 116, 116, 116, 116,
            116, 116, 16, 17, 116, 116, 116, 116,
            116, 116, -1, 116, 116, 116, 116, -1 ),
        array( -1, -1, -1, 140, -1, -1, 140, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 140, 140, 140, 140, 140,
            140, 140, -1, -1, 140, 140, 140, 140,
            140, 140, -1, 140, 140, 140, 140, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 259, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, 123,
            -1, -1, 257, -1, -1, -1, -1, -1,
            124, -1, -1, -1, 125, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 16, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 141, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 137, -1, -1, 137, 142,
            -1, -1, -1, -1, -1, -1, -1, -1,
            143, -1, -1, -1, 296, -1, -1, -1,
            -1, -1, -1, 137, 137, 137, 137, 137,
            137, 137, 20, 21, 137, 137, 137, 137,
            137, 137, -1, 137, 137, 137, 137, -1 ),
        array( -1, -1, -1, -1, -1, -1, 128, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 128, 128, -1, -1, 128,
            128, 128, 22, -1, 128, 128, 128, 128,
            128, 128, -1, 128, 128, 128, -1, -1 ),
        array( -1, -1, -1, 116, -1, -1, 116, 123,
            -1, -1, 144, -1, -1, -1, -1, -1,
            124, -1, -1, -1, 125, -1, -1, -1,
            -1, -1, -1, 116, 116, 116, 116, 116,
            116, 116, 16, 17, 116, 116, 116, 116,
            116, 116, -1, 116, 116, 116, 116, -1 ),
        array( -1, -1, -1, 140, -1, -1, 140, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 145, -1, -1, 146, -1, -1, -1,
            -1, -1, -1, 140, 140, 140, 140, 140,
            140, 140, -1, -1, 140, 140, 140, 140,
            140, 140, -1, 140, 140, 140, 140, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            135, 135, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 147, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 147, 147, -1, -1, 147,
            147, 147, -1, -1, 147, 147, 147, 147,
            147, 147, -1, 147, 147, 147, -1, -1 ),
        array( -1, -1, -1, 148, -1, -1, 148, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 148, 148, 148, 148, 148,
            148, 148, -1, -1, 148, 148, 148, 148,
            148, 148, -1, 148, 148, 148, 148, -1 ),
        array( -1, -1, -1, -1, -1, -1, 128, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 128, 128, -1, -1, 128,
            128, 128, 23, -1, 128, 128, 128, 128,
            128, 128, -1, 128, 128, 128, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, 123,
            -1, -1, 257, -1, -1, -1, -1, -1,
            149, -1, -1, -1, 300, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 16, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 150, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 147, -1, -1, 147, 142,
            -1, -1, -1, -1, -1, -1, -1, -1,
            297, -1, -1, -1, 303, -1, -1, -1,
            -1, -1, -1, 147, 147, 147, 147, 147,
            147, 147, 20, 21, 147, 147, 147, 147,
            147, 147, -1, 147, 147, 147, 147, -1 ),
        array( -1, -1, -1, 148, -1, -1, 148, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 151, -1, -1, 152, -1, -1, -1,
            -1, -1, -1, 148, 148, 148, 148, 148,
            148, 148, -1, -1, 148, 148, 148, 148,
            148, 148, -1, 148, 148, 148, 148, -1 ),
        array( -1, -1, -1, 154, -1, -1, 154, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 154, 154, 154, 154, 154,
            154, 154, -1, -1, 154, 154, 154, 154,
            154, 154, -1, 154, 154, 154, 154, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            145, 145, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, 142,
            -1, -1, -1, -1, -1, -1, -1, -1,
            143, -1, -1, -1, 296, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 20, 21, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 155, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 116, -1, -1, 116, 123,
            -1, -1, 156, -1, -1, -1, -1, -1,
            124, -1, -1, -1, 125, -1, -1, -1,
            -1, -1, -1, 116, 116, 116, 116, 116,
            116, 116, 16, 17, 116, 116, 116, 116,
            116, 116, -1, 116, 116, 116, 116, -1 ),
        array( -1, -1, -1, 154, -1, -1, 154, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 157, -1, -1, 158, -1, -1, -1,
            -1, -1, -1, 154, 154, 154, 154, 154,
            154, 154, -1, -1, 154, 154, 154, 154,
            154, 154, -1, 154, 154, 154, 154, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            151, 151, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 160, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 160, 160, -1, -1, 160,
            160, 160, -1, -1, 160, 160, 160, 160,
            160, 160, -1, 160, 160, 160, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, 161,
            -1, -1, -1, -1, -1, -1, -1, -1,
            149, -1, -1, -1, 300, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 162, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, 142,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 20, 21, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 160, -1, -1, 160, 262,
            -1, -1, -1, -1, -1, -1, -1, -1,
            164, -1, -1, -1, 304, -1, -1, -1,
            -1, -1, -1, 160, 160, 160, 160, 160,
            160, 160, 24, -1, 160, 160, 160, 160,
            160, 160, 269, 160, 160, 160, 160, -1 ),
        array( -1, -1, -1, -1, -1, -1, 165, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 165, 165, -1, -1, 165,
            165, 165, -1, -1, 165, 165, 165, 165,
            165, 165, -1, 165, 165, 165, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            157, 157, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            159, 159, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 167, -1, -1, 167, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 167, 167, 167, 167, 167,
            167, 167, -1, -1, 167, 167, 167, 167,
            167, 167, -1, 167, 167, 167, 167, -1 ),
        array( -1, -1, -1, 165, -1, -1, 165, 161,
            -1, -1, -1, -1, -1, -1, -1, -1,
            149, -1, -1, -1, 300, -1, -1, -1,
            -1, -1, -1, 165, 165, 165, 165, 165,
            165, 165, -1, 17, 165, 165, 165, 165,
            165, 165, -1, 165, 165, 165, 165, -1 ),
        array( -1, -1, -1, 166, -1, -1, 166, 262,
            -1, -1, -1, -1, -1, -1, -1, -1,
            169, -1, -1, -1, 305, -1, -1, -1,
            -1, -1, -1, 166, 166, 166, 166, 166,
            166, 166, 24, -1, 166, 166, 166, 166,
            166, 166, 269, 166, 166, 166, 166, -1 ),
        array( -1, -1, -1, 167, -1, -1, 167, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 170, -1, -1, 171, -1, -1, -1,
            -1, -1, -1, 167, 167, 167, 167, 167,
            167, 167, -1, -1, 167, 167, 167, 167,
            167, 167, -1, 167, 167, 167, 167, -1 ),
        array( -1, -1, -1, 168, -1, -1, 168, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 168, 168, 168, 168, 168,
            168, 168, 25, -1, 168, 168, 168, 168,
            168, 168, 172, 168, 168, 168, 168, -1 ),
        array( -1, -1, -1, 173, -1, -1, 173, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 173, 173, 173, 173, 173,
            173, 173, -1, -1, 173, 173, 173, 173,
            173, 173, -1, 173, 173, 173, 173, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, 262,
            -1, -1, -1, -1, -1, -1, -1, -1,
            164, -1, -1, -1, 304, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 24, -1, -1, -1, -1, -1,
            -1, -1, 269, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 174, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 175, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 175, 175, -1, -1, 175,
            175, 175, -1, -1, 175, 175, 175, 175,
            175, 175, -1, 175, 175, 175, -1, -1 ),
        array( -1, -1, -1, 173, -1, -1, 173, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 176, -1, -1, 177, -1, -1, -1,
            -1, -1, -1, 173, 173, 173, 173, 173,
            173, 173, -1, -1, 173, 173, 173, 173,
            173, 173, -1, 173, 173, 173, 173, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            170, 170, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 175, -1, -1, 175, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 175, 175, 175, 175, 175,
            175, 175, 26, -1, 175, 175, 175, 175,
            175, 175, -1, 175, 175, 175, 175, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, 262,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 24, -1, -1, -1, -1, -1,
            -1, -1, 269, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 178, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            176, 176, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( 1, 27, 27, 27, 27, 27, 27, 27,
            27, 27, 27, 27, 27, 27, 27, 27,
            27, 27, 27, 27, 27, 99, 27, 28,
            27, 27, 27, 27, 27, 27, 27, 27,
            27, 27, 27, 27, 27, 27, 27, 27,
            27, 27, 27, 27, 27, 27, 27, 27 ),
        array( 1, 36, 36, 37, 36, -1, 254, 254,
            82, 38, 36, 107, -1, 39, 36, 254,
            36, 36, 36, 36, 36, 36, 36, 40,
            41, 36, 36, 254, 254, 36, 37, 254,
            254, 254, 36, 36, 254, 254, 254, 254,
            254, 254, 36, 254, 254, 254, 37, 36 ),
        array( 1, 107, 107, 107, 107, 42, 107, 107,
            107, 107, 107, 42, 42, 107, 107, 107,
            107, 107, 107, 107, 107, 107, 107, 107,
            107, 107, 107, 107, 107, 107, 107, 107,
            107, 107, 107, 107, 107, 107, 107, 107,
            107, 107, 107, 107, 107, 107, 107, 107 ),
        array( 1, 43, 43, 43, 43, -1, 43, 43,
            43, 43, 43, 43, -1, 44, 43, 43,
            43, 43, 43, 43, 43, 43, 43, 43,
            43, 43, 43, 43, 43, 43, 43, 43,
            43, 43, 43, 43, 43, 43, 43, 43,
            43, 43, 43, 43, 43, 43, 43, 43 ),
        array( 1, 45, 45, 45, 45, 45, 45, 45,
            45, 45, 45, 45, 45, 45, 45, 45,
            45, 45, 45, 86, 103, 110, 45, 45,
            46, 45, 45, 45, 45, 45, 45, 45,
            45, 45, 45, 45, 45, 45, 45, 45,
            45, 45, 45, 45, 45, 45, 45, 45 ),
        array( -1, -1, -1, 187, -1, -1, 187, 189,
            -1, -1, 263, -1, -1, -1, -1, -1,
            190, -1, -1, -1, 191, -1, -1, -1,
            -1, -1, -1, 187, 187, 187, 187, 187,
            187, 187, 47, 48, 187, 187, 187, 187,
            187, 187, -1, 187, 187, 187, 187, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 266,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 192, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 192, 192, -1, -1, 192,
            192, 192, -1, -1, 192, 192, 192, 192,
            192, 192, -1, 192, 192, 192, -1, -1 ),
        array( -1, -1, -1, 194, -1, -1, 194, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 194, 194, 194, 194, 194,
            194, 194, -1, -1, 194, 194, 194, 194,
            194, 194, -1, 194, 194, 194, 194, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 281, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 195, -1 ),
        array( -1, -1, -1, 192, -1, -1, 192, 189,
            -1, -1, 263, -1, -1, -1, -1, -1,
            196, -1, -1, -1, 298, -1, -1, -1,
            -1, -1, -1, 192, 192, 192, 192, 192,
            192, 192, 47, 48, 192, 192, 192, 192,
            192, 192, -1, 192, 192, 192, 192, -1 ),
        array( -1, -1, -1, -1, -1, -1, 193, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 197, -1, -1, -1,
            -1, -1, -1, 193, 193, -1, -1, 193,
            193, 193, 47, -1, 193, 193, 193, 193,
            193, 193, -1, 193, 193, 193, -1, -1 ),
        array( -1, -1, -1, 194, -1, -1, 194, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 198, -1, -1, 199, -1, -1, -1,
            -1, -1, -1, 194, 194, 194, 194, 194,
            194, 194, -1, -1, 194, 194, 194, 194,
            194, 194, -1, 194, 194, 194, 194, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            47, 47, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 200, -1, -1, 200, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 200, 200, 200, 200, 200,
            200, 200, -1, -1, 200, 200, 200, 200,
            200, 200, -1, 200, 200, 200, 200, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 195, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, 189,
            -1, -1, 263, -1, -1, -1, -1, -1,
            190, -1, -1, -1, 191, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 47, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 201, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 200, -1, -1, 200, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 202, -1, -1, 203, -1, -1, -1,
            -1, -1, -1, 200, 200, 200, 200, 200,
            200, 200, -1, -1, 200, 200, 200, 200,
            200, 200, -1, 200, 200, 200, 200, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            198, 198, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, 189,
            -1, -1, 263, -1, -1, -1, -1, -1,
            204, -1, -1, -1, 301, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 47, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 205, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 206, -1, -1, 206, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 206, 206, 206, 206, 206,
            206, 206, -1, -1, 206, 206, 206, 206,
            206, 206, -1, 206, 206, 206, 206, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            202, 202, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 206, -1, -1, 206, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 207, -1, -1, 208, -1, -1, -1,
            -1, -1, -1, 206, 206, 206, 206, 206,
            206, 206, -1, -1, 206, 206, 206, 206,
            206, 206, -1, 206, 206, 206, 206, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, 209,
            -1, -1, -1, -1, -1, -1, -1, -1,
            204, -1, -1, -1, 295, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 210, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 211, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 211, 211, -1, -1, 211,
            211, 211, -1, -1, 211, 211, 211, 211,
            211, 211, -1, 211, 211, 211, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            207, 207, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 211, -1, -1, 211, 209,
            -1, -1, -1, -1, -1, -1, -1, -1,
            204, -1, -1, -1, 295, -1, -1, -1,
            -1, -1, -1, 211, 211, 211, 211, 211,
            211, 211, -1, 48, 211, 211, 211, 211,
            211, 211, -1, 211, 211, 211, 211, -1 ),
        array( -1, 95, 95, 95, 95, 95, 95, 95,
            95, 95, 95, 95, 95, 95, 95, -1,
            95, 95, 95, 95, 95, 95, 95, 95,
            95, 95, 95, 95, 95, 95, 95, 95,
            95, 95, 95, 95, 95, 95, 95, 95,
            95, 95, 95, 95, 95, 95, 95, 95 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 61, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( 1, 62, 62, 62, 62, 62, 62, 62,
            62, 62, 62, 62, 62, 62, 62, 62,
            62, 63, 62, 62, 62, 62, 62, 62,
            62, 62, 62, 62, 62, 62, 62, 62,
            62, 62, 62, 62, 62, 62, 62, 62,
            62, 62, 62, 62, 62, 62, 62, 62 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 64, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( 1, 107, 113, 107, 107, -1, 115, 107,
            107, 107, 107, 107, -1, 107, 107, 107,
            107, 107, 107, 107, 107, 107, 107, 107,
            107, 107, 107, 115, 115, 107, 107, 115,
            115, 115, 107, 107, 115, 115, 115, 115,
            115, 115, 107, 115, 115, 115, 107, 117 ),
        array( -1, -1, -1, -1, -1, -1, 224, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 224, 224, -1, -1, 224,
            224, 224, -1, -1, 224, 224, 224, 224,
            224, 224, -1, 224, 224, 224, -1, -1 ),
        array( -1, -1, -1, 225, -1, -1, 225, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 225, 225, 225, 225, 225,
            225, 225, -1, -1, 225, 225, 225, 225,
            225, 225, -1, 225, 225, 225, 225, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 226, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 66, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 224, -1, -1, 224, 221,
            -1, -1, -1, -1, -1, -1, -1, -1,
            228, -1, -1, -1, 307, -1, -1, -1,
            -1, -1, -1, 224, 224, 224, 224, 224,
            224, 224, -1, -1, 224, 224, 224, 224,
            224, 224, 66, 224, 224, 224, 224, 223 ),
        array( -1, -1, -1, 225, -1, -1, 225, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 229, -1, -1, 230, -1, -1, -1,
            -1, -1, -1, 225, 225, 225, 225, 225,
            225, 225, -1, -1, 225, 225, 225, 225,
            225, 225, -1, 225, 225, 225, 225, -1 ),
        array( -1, -1, -1, -1, -1, -1, 231, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 231, 231, -1, -1, 231,
            231, 231, -1, -1, 231, 231, 231, 231,
            231, 231, -1, 231, 231, 231, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 227, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 227, 227, -1, -1, 227,
            227, 227, 67, -1, 227, 227, 227, 227,
            227, 227, -1, 227, 227, 227, -1, -1 ),
        array( -1, -1, -1, 232, -1, -1, 232, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 232, 232, 232, 232, 232,
            232, 232, -1, -1, 232, 232, 232, 232,
            232, 232, -1, 232, 232, 232, 232, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, 221,
            -1, -1, -1, -1, -1, -1, -1, -1,
            222, -1, -1, -1, 306, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 66, -1, -1, -1, -1, 223 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 233, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 231, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 231, 231, -1, -1, 231,
            231, 231, 66, -1, 231, 231, 231, 231,
            231, 231, -1, 231, 231, 231, -1, -1 ),
        array( -1, -1, -1, 232, -1, -1, 232, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 234, -1, -1, 235, -1, -1, -1,
            -1, -1, -1, 232, 232, 232, 232, 232,
            232, 232, -1, -1, 232, 232, 232, 232,
            232, 232, -1, 232, 232, 232, 232, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            229, 229, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, 221,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 66, -1, -1, -1, -1, 223 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 236, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            234, 234, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( 1, 107, 119, 107, 107, -1, 68, 107,
            107, 107, 107, 107, -1, 107, 107, 107,
            107, 107, 107, 107, 107, 107, 107, 107,
            107, 107, 107, 68, 68, 107, 107, 68,
            68, 68, 107, 107, 68, 68, 68, 68,
            68, 68, 107, 68, 68, 68, 107, 121 ),
        array( -1, -1, -1, -1, -1, -1, 255, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 255, 255, -1, -1, 255,
            255, 255, -1, -1, 255, 255, 255, 255,
            255, 255, -1, 255, 255, 255, -1, -1 ),
        array( -1, -1, -1, 243, -1, -1, 243, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 243, 243, 243, 243, 243,
            243, 243, -1, -1, 243, 243, 243, 243,
            243, 243, -1, 243, 243, 243, 243, -1 ),
        array( -1, -1, -1, -1, -1, -1, 244, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 244, 244, -1, -1, 244,
            244, 244, -1, -1, 244, 244, 244, 244,
            244, 244, -1, 244, 244, 244, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 245, -1 ),
        array( -1, -1, -1, 243, -1, -1, 243, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 97, -1, -1, 247, -1, -1, -1,
            -1, -1, -1, 243, 243, 243, 243, 243,
            243, 243, -1, -1, 243, 243, 243, 243,
            243, 243, -1, 243, 243, 243, 243, -1 ),
        array( -1, -1, -1, -1, -1, -1, 244, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 242, -1, -1, -1,
            -1, -1, -1, 244, 244, -1, -1, 244,
            244, 244, 70, -1, 244, 244, 244, 244,
            244, 244, -1, 244, 244, 244, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            70, 70, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 248, -1, -1, 248, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 248, 248, 248, 248, 248,
            248, 248, -1, -1, 248, 248, 248, 248,
            248, 248, -1, 248, 248, 248, 248, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 249, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 248, -1, -1, 248, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 105, -1, -1, 250, -1, -1, -1,
            -1, -1, -1, 248, 248, 248, 248, 248,
            248, 248, -1, -1, 248, 248, 248, 248,
            248, 248, -1, 248, 248, 248, 248, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            97, 97, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 251, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            105, 105, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( 1, 107, 107, 107, 107, -1, 107, 107,
            107, 107, 107, 107, -1, 107, 107, 107,
            107, 107, 107, 107, 107, 107, 107, 107,
            107, 107, 107, 107, 107, 107, 107, 107,
            107, 107, 107, 107, 107, 107, 107, 107,
            107, 107, 71, 107, 107, 107, 107, 121 ),
        array( -1, 98, 3, 3, 3, 3, 3, 3,
            106, 3, 3, 3, 3, 3, 3, 3,
            3, 100, 3, -1, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 3 ),
        array( -1, -1, -1, 255, -1, -1, 255, 239,
            -1, -1, -1, -1, -1, -1, -1, -1,
            246, -1, -1, -1, 309, -1, -1, -1,
            -1, -1, -1, 255, 255, 255, 255, 255,
            255, 255, -1, -1, 255, 255, 255, 255,
            255, 255, -1, 255, 255, 255, 255, -1 ),
        array( -1, -1, -1, 116, -1, -1, 116, 123,
            -1, -1, 257, -1, -1, -1, -1, -1,
            124, -1, -1, -1, 125, -1, -1, -1,
            -1, -1, -1, 116, 126, 116, 116, 116,
            116, 116, 16, 17, 116, 116, 116, 116,
            116, 116, -1, 116, 116, 116, 116, -1 ),
        array( -1, -1, -1, -1, -1, -1, 128, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 128, 128, -1, -1, 128,
            128, 128, -1, -1, 128, 128, 128, 128,
            128, 128, -1, 128, 128, 128, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 258,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 260, -1, -1, 260, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 159, -1, -1, 261, -1, -1, -1,
            -1, -1, -1, 260, 260, 260, 260, 260,
            260, 260, -1, -1, 260, 260, 260, 260,
            260, 260, -1, 260, 260, 260, 260, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 163, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 166, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 166, 166, -1, -1, 166,
            166, 166, -1, -1, 166, 166, 166, 166,
            166, 166, -1, 166, 166, 166, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 193, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 193, 193, -1, -1, 193,
            193, 193, -1, -1, 193, 193, 193, 193,
            193, 193, -1, 193, 193, 193, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 227, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 227, 227, -1, -1, 227,
            227, 227, -1, -1, 227, 227, 227, 227,
            227, 227, -1, 227, 227, 227, -1, -1 ),
        array( -1, -1, -1, 116, -1, -1, 116, 123,
            -1, -1, 257, -1, -1, -1, -1, -1,
            124, -1, -1, -1, 125, -1, -1, -1,
            -1, -1, -1, 116, 116, 116, 116, 116,
            116, 132, 16, 17, 116, 116, 116, 116,
            116, 116, -1, 116, 116, 116, 116, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 267,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 168, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 168, 168, -1, -1, 168,
            168, 168, -1, -1, 168, 168, 168, 168,
            168, 168, -1, 168, 168, 168, -1, -1 ),
        array( -1, -1, -1, 116, -1, -1, 116, 123,
            -1, -1, 257, -1, -1, -1, -1, -1,
            124, -1, -1, -1, 125, -1, -1, -1,
            -1, -1, -1, 116, 116, 116, 116, 116,
            116, 116, 16, 17, 116, 116, 139, 116,
            116, 116, -1, 116, 116, 116, 116, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 271,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 116, -1, -1, 116, 123,
            -1, -1, 257, -1, -1, -1, -1, -1,
            124, -1, -1, -1, 125, -1, -1, -1,
            -1, -1, -1, 116, 116, 116, 116, 116,
            116, 116, 16, 17, 116, 116, 116, 116,
            116, 153, -1, 116, 116, 116, 116, -1 ),
        array( -1, -1, -1, 260, -1, -1, 260, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 260, 260, 260, 260, 260,
            260, 260, -1, -1, 260, 260, 260, 260,
            260, 260, -1, 260, 260, 260, 260, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 274,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 276,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 278,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 280,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 282,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 284,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 286,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 288,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 290,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 292,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 116, -1, -1, 116, 123,
            -1, -1, 257, -1, -1, -1, -1, -1,
            124, -1, -1, -1, 125, -1, -1, -1,
            -1, -1, -1, 116, 116, 116, 116, 116,
            116, 116, 16, 17, 116, 116, 116, 116,
            116, 116, -1, 265, 299, 116, 116, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 285, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 268, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 283, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 195, -1 ),
        array( -1, -1, -1, 116, -1, -1, 116, 123,
            -1, -1, 257, -1, -1, -1, -1, -1,
            124, -1, -1, -1, 125, -1, -1, -1,
            -1, -1, -1, 116, 116, 116, 116, 116,
            116, 116, 16, 17, 116, 116, 116, 116,
            116, 116, -1, 116, 116, 270, 116, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 272, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 285, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 195, -1 ),
        array( -1, -1, -1, 116, -1, -1, 116, 123,
            -1, -1, 257, -1, -1, -1, -1, -1,
            124, -1, -1, -1, 125, -1, -1, -1,
            -1, -1, -1, 116, 116, 116, 116, 116,
            116, 116, 16, 17, 116, 116, 116, 116,
            273, 116, -1, 116, 116, 116, 116, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 275, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 277, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 279, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 287, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 289, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 291, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 293, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 116, -1, -1, 116, 123,
            -1, -1, 257, -1, -1, -1, -1, -1,
            124, -1, -1, -1, 125, -1, -1, -1,
            -1, -1, -1, 116, 116, 116, 116, 116,
            116, 116, 16, 17, 116, 116, 116, 302,
            116, 116, -1, 116, 116, 116, 116, -1 ),
        array( -1, -1, -1, 116, -1, -1, 116, 123,
            -1, -1, 257, -1, -1, -1, -1, -1,
            124, -1, -1, -1, 125, -1, -1, -1,
            -1, -1, -1, 116, 116, 116, 116, 116,
            116, 116, 16, 17, 116, 116, 310, 116,
            116, 116, -1, 116, 116, 116, 116, -1 ),
        array( -1, -1, -1, 116, -1, -1, 116, 123,
            -1, -1, 257, -1, -1, -1, -1, -1,
            124, -1, -1, -1, 125, -1, -1, -1,
            -1, -1, -1, 116, 116, 116, 116, 116,
            116, 116, 16, 17, 116, 311, 116, 116,
            116, 116, -1, 116, 116, 116, 116, -1 ),
        array( -1, -1, -1, 116, -1, -1, 116, 123,
            -1, -1, 257, -1, -1, -1, -1, -1,
            124, -1, -1, -1, 125, -1, -1, -1,
            -1, -1, -1, 116, 116, 116, 116, 116,
            116, 116, 16, 17, 312, 116, 116, 116,
            116, 116, -1, 116, 116, 116, 116, -1 )
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
                       if ($yy_last_accept_state < 314) {
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
    // { added for flexy
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
    // &#123;
    $this->value = HTML_Template_Flexy_Token::factory('Text',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 8:
{
    // &#abc;
    $this->value = HTML_Template_Flexy_Token::factory('Text',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 9:
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
case 10:
{
    /* </> -- empty end tag */  
    if ($this->ignoreHTML) {
        return $this->returnSimple();
    }
    return $this->raiseError("empty end tag not handled");
}
case 11:
{
    /* <!DOCTYPE -- markup declaration */
    if ($this->ignoreHTML) {
        return $this->returnSimple();
    }
    $this->value = HTML_Template_Flexy_Token::factory('Doctype',$this->yytext(),$this->yyline);
    $this->yybegin(IN_MD);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 12:
{
    /* <!> */
    if ($this->ignoreHTML) {
        return $this->returnSimple();
    }
    return $this->raiseError("empty markup tag not handled"); 
}
case 13:
{
    /* <![ -- marked section */
    return $this->returnSimple();
    //$this->value = HTML_Template_Flexy_Token::factory('Text',$this->yytext(),$this->yyline);
    //return HTML_TEMPLATE_FLEXY_TOKEN_OK;
    // At the momemnt just ignore this!
    return $this->raiseError("marked section not handled"); 
}
case 14:
{ 
    /* <? ...> -- processing instruction */
    // this is a little odd cause technically we dont allow it!!
    // really we only want to handle < ? xml 
    $t = $this->yytext();
    // only allow 'xml'
    if ($this->ignorePHP && (strtoupper(substr($t,2,3)) != 'XML')) {
        return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
    }
    $this->value = HTML_Template_Flexy_Token::factory('Processing',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 15:
{ 
    /* ]]> -- marked section end */
    return $this->returnSimple();
    //$this->value = HTML_Template_Flexy_Token::factory('Text',$this->yytext(),$this->yyline);
    //return HTML_TEMPLATE_FLEXY_TOKEN_OK;
    // At the momemnt just ignore this!
    return $this->raiseError("unmatched marked sections end"); 
}
case 16:
{
    $t =  $this->yytext();
    $t = substr($t,1,-1);
    $this->value = HTML_Template_Flexy_Token::factory('Var'  , $t, $this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
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
    if ($this->ignoreHTML) {
        return $this->returnSimple();
    }
    /* </name <  -- unclosed end tag */
    return $this->raiseError("Unclosed  end tag");
}
case 19:
{
    /* <!--  -- comment declaration */
    if ($this->ignoreHTML) {
        return $this->returnSimple();
    }
    $this->value = HTML_Template_Flexy_Token::factory('Comment',$this->yytext(),$this->yyline);
    $this->yybegin(IN_COM);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 20:
{
    $this->value = HTML_Template_Flexy_Token::factory('If',substr($this->yytext(),4,-1),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 21:
{
    $this->value =  '';
    $this->flexyMethod = substr($this->yytext(),1,-1);
    $this->flexyArgs = array();
    $this->yybegin(IN_FLEXYMETHOD);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 22:
{
    $this->value = HTML_Template_Flexy_Token::factory('End', '',$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 23:
{
    $this->value = HTML_Template_Flexy_Token::factory('Else', '',$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 24:
{
    $this->value = HTML_Template_Flexy_Token::factory('Foreach',array(substr($this->yytext(),9,-1)),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 25:
{
    $this->value = HTML_Template_Flexy_Token::factory('Foreach', explode(',',substr($this->yytext(),9,-1)),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 26:
{
    $this->value = HTML_Template_Flexy_Token::factory('Foreach',  explode(',',substr($this->yytext(),9,-1)),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 27:
{
    $this->attrVal[] = $this->yytext();
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 28:
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
case 29:
{
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 30:
{
    // <foo^<bar> -- unclosed start tag */
    return $this->raiseError("Unclosed tags not supported"); 
}
case 31:
{
    $this->value = HTML_Template_Flexy_Token::factory($this->tokenName,
        array($this->tagName,$this->attributes),
        $this->yyline);
    $this->yybegin(YYINITIAL);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 32:
{
    // <img src="xxx" ...ismap...> the ismap */
    $this->attributes[trim($this->yytext())] = true;
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 33:
{
    $this->yybegin(IN_NETDATA);
    $this->attributes["/"] = true;
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 34:
{
   // <a ^href = "xxx"> -- attribute name 
    $this->attrKey = substr(trim($this->yytext()),0,-1);
    $this->yybegin(IN_ATTRVAL);
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 35:
{
    $this->attributes["/"] = true;
    $this->value = HTML_Template_Flexy_Token::factory($this->tokenName,
        array($this->tagName,$this->attributes),
        $this->yyline);
    $this->yybegin(YYINITIAL);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 36:
{
    // <a href = ^http://foo/> -- unquoted literal HACK */                          
    $this->attributes[$this->attrKey] = trim($this->yytext());
    $this->yybegin(IN_ATTR);
    //   $this->raiseError("attribute value needs quotes");
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 37:
{
    // <a name = ^12pt> -- number token */
    $this->attributes[$this->attrKey] = trim($this->yytext());
    $this->yybegin(IN_ATTR);
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 38:
{
    return $this->raiseError("attribute value missing"); 
}
case 39:
{ 
    return $this->raiseError("Tag close found where attribute value expected"); 
}
case 40:
{
	//echo "STARTING SINGLEQUOTE";
    $this->attrVal = array( "'");
    $this->yybegin(IN_SINGLEQUOTE);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 41:
{
    //echo "START QUOTE";
    $this->attrVal =array("\"");
    $this->yybegin(IN_DOUBLEQUOTE);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 42:
{ 
    // whitespace switch back to IN_ATTR MODE.
    $this->value = '';
    $this->yybegin(IN_ATTR);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 43:
{ 
    return $this->raiseError("extraneous character in end tag"); 
}
case 44:
{ 
    $this->value = HTML_Template_Flexy_Token::factory($this->tokenName,
        array($this->tagName),
        $this->yyline);
        array($this->tagName);
    $this->yybegin(YYINITIAL);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 45:
{
    //echo "GOT DATA:".$this->yytext();
    $this->attrVal[] = $this->yytext();
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 46:
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
case 47:
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
case 48:
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
case 49:
{ 
    $this->value = HTML_Template_Flexy_Token::factory('WhiteSpace',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 50:
{
    return $this->raiseError("illegal character in markup declaration");
    return HTML_TEMPLATE_FLEXY_TOKEN_ERROR;
}
case 51:
{   
    $this->value = HTML_Template_Flexy_Token::factory('Number',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 52:
{ 
    $this->value = HTML_Template_Flexy_Token::factory('Name',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 53:
{ 
    $this->value = HTML_Template_Flexy_Token::factory('NameT',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 54:
{   
    $this->value = HTML_Template_Flexy_Token::factory('CloseTag',$this->yytext(),$this->yyline);
    $this->yybegin(YYINITIAL); 
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 55:
{
    // <!doctype foo ^[  -- declaration subset */
    $this->value = HTML_Template_Flexy_Token::factory('BeginDS',$this->yytext(),$this->yyline);
    $this->yybegin(IN_DS);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 56:
{ 
    $this->value = HTML_Template_Flexy_Token::factory('NumberT',$this->yytext(),$this->yyline);    
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 57:
{
    // <!entity ^% foo system "..." ...> -- parameter entity definition */
    $this->value = HTML_Template_Flexy_Token::factory('EntityPar',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 58:
{
    // <!doctype ^%foo;> -- parameter entity reference */
    $this->value = HTML_Template_Flexy_Token::factory('EntityRef',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 59:
{ 
    $this->value = HTML_Template_Flexy_Token::factory('Literal',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 60:
{
    // <!^--...-->   -- comment */   
    $this->value = HTML_Template_Flexy_Token::factory('Comment',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 61:
{   
    $this->value = HTML_Template_Flexy_Token::factory('CloseTag',$this->yytext(),$this->yyline);
    $this->yybegin(YYINITIAL); 
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 62:
{ 
    $this->value = HTML_Template_Flexy_Token::factory('Declaration',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 63:
{ 
    // ] -- declaration subset close */
    $this->value = HTML_Template_Flexy_Token::factory('DSEndSubset',$this->yytext(),$this->yyline);
    $this->yybegin(IN_COM); 
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 64:
{
    // ]]> -- marked section end */
     $this->value = HTML_Template_Flexy_Token::factory('DSEnd',$this->yytext(),$this->yyline);
    $this->yybegin(YYINITIAL);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 65:
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
case 66:
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
case 67:
{
    $t = $this->yytext();
    if ($t{1} == ':') {
        $this->flexyMethod .= substr($t,1,-1);
    }
    $this->value = HTML_Template_Flexy_Token::factory('Method'  , array($this->flexyMethod,$this->flexyArgs), $this->yyline);
    $this->yybegin(YYINITIAL);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 68:
{
    $t = substr($this->yytext(),0,-1);
    // add argument
    $this->flexyArgs[] = $t;
    $this->yybegin(IN_FLEXYMETHODQUOTED_END);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 69:
{
    $t = $this->yytext();
    $this->flexyArgs[] =$t;
    $this->yybegin(IN_FLEXYMETHODQUOTED_END);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 70:
{
    $t = $this->yytext();
    if ($p = strpos($t,':')) {
        $this->flexyMethod .= substr($t,$p,2);
    }
    $this->attrVal[] = HTML_Template_Flexy_Token::factory('Method'  , array($this->flexyMethod,$this->flexyArgs), $this->yyline);    
    $this->yybegin($this->flexyMethodState);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 71:
{
    $this->yybegin(IN_FLEXYMETHODQUOTED);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 73:
{
    return $this->raiseError("unexpected something: (".$this->yytext() .") character: 0x" . dechex(ord($this->yytext())));
}
case 74:
{
    //abcd -- data characters  
    // { added for flexy
    $this->value = HTML_Template_Flexy_Token::factory('Text',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 75:
{
    // &abc;
    $this->value = HTML_Template_Flexy_Token::factory('Text',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 76:
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
case 77:
{
    // &#123;
    $this->value = HTML_Template_Flexy_Token::factory('Text',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 78:
{
    // &#abc;
    $this->value = HTML_Template_Flexy_Token::factory('Text',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 79:
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
case 80:
{
    /* <!DOCTYPE -- markup declaration */
    if ($this->ignoreHTML) {
        return $this->returnSimple();
    }
    $this->value = HTML_Template_Flexy_Token::factory('Doctype',$this->yytext(),$this->yyline);
    $this->yybegin(IN_MD);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 81:
{
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 82:
{
    // <foo^<bar> -- unclosed start tag */
    return $this->raiseError("Unclosed tags not supported"); 
}
case 83:
{
    // <img src="xxx" ...ismap...> the ismap */
    $this->attributes[trim($this->yytext())] = true;
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 84:
{
    // <a href = ^http://foo/> -- unquoted literal HACK */                          
    $this->attributes[$this->attrKey] = trim($this->yytext());
    $this->yybegin(IN_ATTR);
    //   $this->raiseError("attribute value needs quotes");
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 85:
{
    // <a name = ^12pt> -- number token */
    $this->attributes[$this->attrKey] = trim($this->yytext());
    $this->yybegin(IN_ATTR);
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 86:
{
    //echo "GOT DATA:".$this->yytext();
    $this->attrVal[] = $this->yytext();
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 87:
{ 
    $this->value = HTML_Template_Flexy_Token::factory('WhiteSpace',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 88:
{
    return $this->raiseError("illegal character in markup declaration");
    return HTML_TEMPLATE_FLEXY_TOKEN_ERROR;
}
case 89:
{   
    $this->value = HTML_Template_Flexy_Token::factory('Number',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 90:
{ 
    $this->value = HTML_Template_Flexy_Token::factory('Name',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 91:
{ 
    $this->value = HTML_Template_Flexy_Token::factory('NameT',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 92:
{ 
    $this->value = HTML_Template_Flexy_Token::factory('NumberT',$this->yytext(),$this->yyline);    
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 93:
{
    // <!doctype ^%foo;> -- parameter entity reference */
    $this->value = HTML_Template_Flexy_Token::factory('EntityRef',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 94:
{ 
    $this->value = HTML_Template_Flexy_Token::factory('Literal',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 95:
{
    // <!^--...-->   -- comment */   
    $this->value = HTML_Template_Flexy_Token::factory('Comment',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 96:
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
case 97:
{
    $t = substr($this->yytext(),0,-1);
    // add argument
    $this->flexyArgs[] = $t;
    $this->yybegin(IN_FLEXYMETHODQUOTED_END);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 99:
{
    return $this->raiseError("unexpected something: (".$this->yytext() .") character: 0x" . dechex(ord($this->yytext())));
}
case 100:
{
    //abcd -- data characters  
    // { added for flexy
    $this->value = HTML_Template_Flexy_Token::factory('Text',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 101:
{
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 102:
{
    // <a name = ^12pt> -- number token */
    $this->attributes[$this->attrKey] = trim($this->yytext());
    $this->yybegin(IN_ATTR);
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 103:
{
    //echo "GOT DATA:".$this->yytext();
    $this->attrVal[] = $this->yytext();
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 104:
{
    return $this->raiseError("illegal character in markup declaration");
    return HTML_TEMPLATE_FLEXY_TOKEN_ERROR;
}
case 105:
{
    $t = substr($this->yytext(),0,-1);
    // add argument
    $this->flexyArgs[] = $t;
    $this->yybegin(IN_FLEXYMETHODQUOTED_END);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 107:
{
    return $this->raiseError("unexpected something: (".$this->yytext() .") character: 0x" . dechex(ord($this->yytext())));
}
case 108:
{
    return $this->raiseError("illegal character in markup declaration");
    return HTML_TEMPLATE_FLEXY_TOKEN_ERROR;
}
case 110:
{
    return $this->raiseError("unexpected something: (".$this->yytext() .") character: 0x" . dechex(ord($this->yytext())));
}
case 111:
{
    return $this->raiseError("illegal character in markup declaration");
    return HTML_TEMPLATE_FLEXY_TOKEN_ERROR;
}
case 113:
{
    return $this->raiseError("unexpected something: (".$this->yytext() .") character: 0x" . dechex(ord($this->yytext())));
}
case 115:
{
    return $this->raiseError("unexpected something: (".$this->yytext() .") character: 0x" . dechex(ord($this->yytext())));
}
case 117:
{
    return $this->raiseError("unexpected something: (".$this->yytext() .") character: 0x" . dechex(ord($this->yytext())));
}
case 119:
{
    return $this->raiseError("unexpected something: (".$this->yytext() .") character: 0x" . dechex(ord($this->yytext())));
}
case 121:
{
    return $this->raiseError("unexpected something: (".$this->yytext() .") character: 0x" . dechex(ord($this->yytext())));
}
case 253:
{
    //abcd -- data characters  
    // { added for flexy
    $this->value = HTML_Template_Flexy_Token::factory('Text',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 254:
{
    // <a name = ^12pt> -- number token */
    $this->attributes[$this->attrKey] = trim($this->yytext());
    $this->yybegin(IN_ATTR);
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 255:
{
    $t = substr($this->yytext(),0,-1);
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
