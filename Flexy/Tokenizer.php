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
        196,
        30,
        114,
        199,
        200,
        201,
        202,
        50,
        61,
        227,
        229,
        248,
        262,
        263,
        271,
        79
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
        /* 81 */   YY_NOT_ACCEPT,
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
        /* 111 */   YY_NOT_ACCEPT,
        /* 112 */   YY_NO_ANCHOR,
        /* 113 */   YY_NO_ANCHOR,
        /* 114 */   YY_NO_ANCHOR,
        /* 115 */   YY_NO_ANCHOR,
        /* 116 */   YY_NO_ANCHOR,
        /* 117 */   YY_NO_ANCHOR,
        /* 118 */   YY_NO_ANCHOR,
        /* 119 */   YY_NOT_ACCEPT,
        /* 120 */   YY_NO_ANCHOR,
        /* 121 */   YY_NO_ANCHOR,
        /* 122 */   YY_NOT_ACCEPT,
        /* 123 */   YY_NO_ANCHOR,
        /* 124 */   YY_NO_ANCHOR,
        /* 125 */   YY_NOT_ACCEPT,
        /* 126 */   YY_NO_ANCHOR,
        /* 127 */   YY_NOT_ACCEPT,
        /* 128 */   YY_NO_ANCHOR,
        /* 129 */   YY_NOT_ACCEPT,
        /* 130 */   YY_NO_ANCHOR,
        /* 131 */   YY_NOT_ACCEPT,
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
        /* 274 */   YY_NO_ANCHOR,
        /* 275 */   YY_NO_ANCHOR,
        /* 276 */   YY_NO_ANCHOR,
        /* 277 */   YY_NO_ANCHOR,
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
        /* 325 */   YY_NOT_ACCEPT
        );


    var  $yy_cmap = array(
        29, 29, 29, 29, 29, 29, 29, 29,
        29, 11, 5, 29, 29, 12, 29, 29,
        29, 29, 29, 29, 29, 29, 29, 29,
        29, 29, 29, 29, 29, 29, 29, 29,
        11, 14, 28, 2, 30, 24, 1, 27,
        46, 55, 30, 30, 51, 15, 7, 9,
        3, 3, 3, 3, 3, 42, 3, 54,
        3, 3, 10, 4, 8, 26, 13, 23,
        29, 19, 43, 17, 18, 6, 6, 6,
        6, 36, 6, 6, 6, 6, 6, 6,
        38, 6, 35, 31, 20, 6, 6, 6,
        6, 6, 6, 16, 25, 22, 29, 41,
        29, 49, 43, 33, 44, 48, 40, 6,
        50, 37, 6, 6, 53, 6, 52, 47,
        38, 6, 34, 32, 39, 6, 6, 6,
        6, 6, 6, 21, 29, 45, 29, 29,
        29, 29, 29, 29, 29, 29, 29, 29,
        29, 29, 29, 29, 29, 29, 29, 29,
        29, 29, 29, 29, 29, 29, 29, 29,
        29, 29, 29, 29, 29, 29, 29, 29,
        29, 29, 29, 29, 29, 29, 29, 29,
        29, 29, 29, 29, 29, 29, 29, 29,
        29, 29, 29, 29, 29, 29, 29, 29,
        29, 29, 29, 29, 29, 29, 29, 29,
        29, 29, 29, 29, 29, 29, 29, 29,
        29, 29, 29, 29, 29, 29, 29, 29,
        29, 29, 29, 29, 29, 29, 29, 29,
        29, 29, 29, 29, 29, 29, 29, 29,
        29, 29, 29, 29, 29, 29, 29, 29,
        29, 29, 29, 29, 29, 29, 29, 29,
        29, 29, 29, 29, 29, 29, 29, 29,
        29, 29, 29, 29, 29, 29, 29, 29,
        29, 0, 0 
         );


    var $yy_rmap = array(
        0, 1, 2, 3, 4, 5, 1, 6,
        7, 8, 1, 9, 1, 10, 1, 1,
        1, 3, 1, 1, 1, 1, 1, 1,
        1, 1, 1, 1, 11, 1, 12, 1,
        1, 13, 14, 15, 1, 16, 17, 16,
        1, 1, 1, 18, 1, 1, 19, 1,
        1, 1, 20, 1, 21, 22, 23, 1,
        1, 24, 25, 26, 27, 28, 1, 1,
        29, 30, 1, 31, 1, 1, 32, 1,
        1, 1, 33, 34, 1, 35, 1, 36,
        1, 37, 38, 39, 1, 40, 1, 1,
        41, 42, 43, 44, 16, 45, 46, 47,
        48, 49, 50, 51, 52, 53, 54, 55,
        1, 56, 57, 1, 58, 59, 60, 61,
        62, 63, 64, 65, 66, 67, 68, 69,
        1, 70, 71, 72, 73, 74, 75, 76,
        77, 78, 79, 80, 81, 82, 83, 84,
        85, 86, 87, 88, 89, 90, 91, 92,
        93, 94, 95, 96, 97, 98, 99, 100,
        101, 102, 103, 104, 105, 106, 107, 108,
        109, 110, 111, 112, 113, 114, 115, 116,
        117, 118, 119, 120, 121, 122, 123, 124,
        125, 126, 127, 128, 129, 130, 131, 132,
        133, 134, 135, 136, 137, 138, 139, 140,
        141, 142, 143, 144, 145, 62, 14, 146,
        147, 148, 149, 72, 150, 151, 152, 153,
        154, 155, 156, 157, 158, 159, 160, 161,
        162, 163, 164, 165, 166, 167, 67, 70,
        168, 169, 57, 170, 171, 172, 75, 77,
        173, 174, 175, 176, 177, 178, 179, 180,
        181, 182, 183, 184, 185, 186, 187, 188,
        189, 81, 190, 191, 192, 193, 194, 195,
        196, 197, 198, 199, 200, 201, 202, 203,
        204, 205, 206, 207, 208, 209, 210, 211,
        212, 213, 214, 65, 215, 216, 217, 218,
        98, 219, 220, 221, 222, 223, 224, 48,
        110, 225, 226, 119, 227, 228, 131, 229,
        135, 230, 153, 231, 159, 232, 174, 233,
        180, 234, 191, 235, 197, 236, 237, 238,
        239, 240, 241, 242, 243, 244, 245, 246,
        247, 248, 249, 250, 251, 252 
        );


    var $yy_nxt = array(
        array( 1, 2, 3, 3, 3, 3, 3, 3,
            82, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 83, 274, 3,
            3, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 3 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, 81, 3, 3, 3, 4, 3,
            -1, 3, 3, 3, 3, 3, 3, 3,
            3, 4, 4, 4, 4, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 4,
            4, 4, 4, 4, 4, 4, 4, 4,
            4, 3, 3, 4, 4, 3, 3, 4,
            4, 4, 4, 3, 4, 4, 3, 3 ),
        array( -1, 111, 3, 3, 3, 3, 3, 3,
            119, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, -1, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 3 ),
        array( -1, -1, -1, 4, 84, 84, 4, 4,
            -1, -1, -1, -1, -1, -1, -1, 4,
            -1, 4, 4, 4, 4, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 4,
            4, 4, 4, 4, 4, 4, 4, 4,
            4, -1, 4, 4, 4, -1, -1, 4,
            4, 4, 4, -1, 4, 4, 4, -1 ),
        array( -1, -1, -1, 5, -1, 85, 5, 5,
            -1, -1, 5, 85, 85, -1, -1, 5,
            -1, 5, 5, 5, 5, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 5,
            5, 5, 5, 5, 5, 5, 5, 5,
            5, -1, 5, 5, 5, -1, -1, 5,
            5, 5, 5, -1, 5, 5, 5, -1 ),
        array( -1, -1, -1, 7, 86, 86, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 7, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 7, -1 ),
        array( -1, -1, -1, 8, 87, 87, 8, 8,
            -1, -1, -1, -1, -1, -1, -1, 8,
            -1, 8, 8, 8, 8, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 8,
            8, 8, 8, 8, 8, 8, 8, 8,
            8, -1, 8, 8, 8, -1, -1, 8,
            8, 8, 8, -1, 8, 8, 8, -1 ),
        array( -1, -1, -1, 9, -1, 88, 9, 9,
            -1, 133, 9, 88, 88, -1, -1, 9,
            -1, 9, 9, 9, 9, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 9,
            9, 9, 9, 9, 9, 9, 9, 9,
            9, -1, 9, 9, 9, -1, -1, 9,
            9, 9, 9, -1, 9, 9, 9, -1 ),
        array( -1, -1, -1, 11, -1, 89, 11, 11,
            -1, -1, -1, 89, 89, -1, -1, 11,
            -1, 11, 11, 11, 11, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 11,
            11, 11, 11, 11, 11, 11, 11, 11,
            11, -1, 11, 11, 11, -1, -1, 11,
            11, 11, 11, -1, 11, 11, 11, -1 ),
        array( -1, -1, -1, -1, -1, 90, -1, -1,
            -1, -1, -1, 90, 90, -1, -1, -1,
            -1, 141, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, 28, 28, 28, 28, 28, 28, 28,
            28, 28, 28, 28, 28, 28, 28, 28,
            28, 28, 28, 28, 28, 28, 28, 28,
            28, 197, 28, -1, 28, 28, 28, 28,
            28, 28, 28, 28, 28, 28, 28, 28,
            28, 28, 28, 28, 28, 28, 28, 28,
            28, 28, 28, 28, 28, 28, 28, 28 ),
        array( 1, 120, 120, 120, 120, 91, 120, 120,
            31, 120, 120, 91, 91, 32, 120, 120,
            120, 120, 120, 120, 120, 120, 120, 120,
            120, 120, 120, 120, 120, 120, 120, 120,
            120, 120, 120, 120, 120, 120, 120, 120,
            120, 120, 120, 120, 120, 120, 120, 120,
            120, 120, 120, 120, 120, 120, 120, 120 ),
        array( -1, -1, -1, 33, -1, 93, 33, 33,
            -1, -1, 33, 93, 93, -1, -1, 33,
            -1, 33, 33, 33, 33, -1, -1, -1,
            -1, -1, 35, -1, -1, -1, -1, 33,
            33, 33, 33, 33, 33, 33, 33, 33,
            33, -1, 33, 33, 33, -1, -1, 33,
            33, 33, 33, -1, 33, 33, 33, -1 ),
        array( -1, -1, -1, -1, -1, 198, -1, -1,
            -1, -1, -1, 198, 198, 36, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 35, -1, -1,
            -1, -1, -1, 35, 35, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, 37, 37, 37, 37, 94, 37, 37,
            37, 37, 37, 94, 94, -1, 37, 37,
            37, 37, 37, 37, 37, 37, 37, 37,
            37, 37, 37, -1, -1, 37, 37, 37,
            37, 37, 37, 37, 37, 37, 37, 37,
            37, 37, 37, 37, 37, 37, 37, 37,
            37, 37, 37, 37, 37, 37, 37, 37 ),
        array( -1, 37, 37, 38, 37, 95, 38, 38,
            37, 37, 37, 95, 95, -1, 37, 38,
            37, 38, 38, 38, 38, 37, 37, 37,
            37, 37, 37, -1, -1, 37, 37, 38,
            38, 38, 38, 38, 38, 38, 38, 38,
            38, 37, 38, 38, 38, 37, 37, 38,
            38, 38, 38, 37, 38, 38, 38, 37 ),
        array( -1, -1, -1, -1, -1, 43, -1, -1,
            -1, -1, -1, 43, 43, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, 46, 46, 46, 46, 46, 46, 46,
            46, 46, 46, 46, 46, 46, 46, 46,
            46, 46, 46, 46, 46, -1, 46, 46,
            -1, 203, 46, 46, -1, 46, 46, 46,
            46, 46, 46, 46, 46, 46, 46, 46,
            46, 46, 46, 46, 46, 46, 46, 46,
            46, 46, 46, 46, 46, 46, 46, 46 ),
        array( 1, 51, 51, 52, 51, 97, 53, 54,
            51, 51, 51, 97, 97, 55, 51, 54,
            56, 53, 53, 53, 53, 51, 51, 51,
            98, 51, 51, 117, 121, 51, 51, 53,
            53, 53, 53, 53, 53, 53, 53, 53,
            53, 51, 52, 53, 53, 51, 51, 53,
            53, 53, 53, 51, 53, 53, 52, 51 ),
        array( -1, -1, -1, 52, -1, 99, 57, 57,
            -1, -1, -1, 99, 99, -1, -1, 57,
            -1, 57, 57, 57, 57, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 57,
            57, 57, 57, 57, 57, 57, 57, 57,
            57, -1, 52, 57, 57, -1, -1, 57,
            57, 57, 57, -1, 57, 57, 52, -1 ),
        array( -1, -1, -1, 53, -1, 100, 53, 53,
            -1, -1, -1, 100, 100, -1, -1, 53,
            -1, 53, 53, 53, 53, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 53,
            53, 53, 53, 53, 53, 53, 53, 53,
            53, -1, 53, 53, 53, -1, -1, 53,
            53, 53, 53, -1, 53, 53, 53, -1 ),
        array( -1, -1, -1, 54, -1, 101, 54, 54,
            -1, -1, -1, 101, 101, -1, -1, 54,
            -1, 54, 54, 54, 54, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 54,
            54, 54, 54, 54, 54, 54, 54, 54,
            54, -1, 54, 54, 54, -1, -1, 54,
            54, 54, 54, -1, 54, 54, 54, -1 ),
        array( -1, -1, -1, 57, -1, 102, 57, 57,
            -1, -1, -1, 102, 102, -1, -1, 57,
            -1, 57, 57, 57, 57, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 57,
            57, 57, 57, 57, 57, 57, 57, 57,
            57, -1, 57, 57, 57, -1, -1, 57,
            57, 57, 57, -1, 57, 57, 57, -1 ),
        array( -1, -1, -1, -1, -1, 58, -1, -1,
            -1, -1, -1, 58, 58, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 59, 103, 103, 59, 59,
            -1, -1, -1, 103, 103, -1, -1, 59,
            -1, 59, 59, 59, 59, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 59,
            59, 59, 59, 59, 59, 59, 59, 59,
            59, -1, 59, 59, 59, -1, -1, 59,
            59, 59, 59, -1, 59, 59, 59, -1 ),
        array( -1, -1, -1, -1, -1, 60, -1, -1,
            -1, -1, -1, 60, 60, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( 1, 105, 105, 105, 105, 105, 105, 105,
            105, 105, 105, 105, 105, 105, 105, 124,
            105, 105, 105, 105, 105, 105, 105, 105,
            105, 105, 105, 105, 105, 105, 105, 105,
            105, 105, 105, 105, 105, 105, 105, 105,
            105, 105, 105, 105, 105, 105, 105, 105,
            105, 105, 105, 105, 105, 105, 105, 105 ),
        array( -1, 64, 64, 64, 64, 64, 64, 64,
            64, 64, 64, 64, 64, 64, 64, 64,
            64, 64, 64, 64, 64, 64, -1, 64,
            64, 64, 64, 64, 64, 64, 64, 64,
            64, 64, 64, 64, 64, 64, 64, 64,
            64, 64, 64, 64, 64, 64, 64, 64,
            64, 64, 64, 64, 64, 64, 64, 64 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 228, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 107, -1, -1, -1, -1 ),
        array( -1, -1, -1, 70, -1, -1, 70, 250,
            -1, -1, -1, -1, -1, -1, -1, -1,
            251, 70, 70, 70, 70, -1, -1, -1,
            320, -1, -1, -1, -1, -1, -1, 70,
            70, 70, 70, 70, 70, 70, 70, 70,
            70, 70, 70, 70, 70, -1, -1, 70,
            70, 70, 70, -1, 70, 70, 70, -1 ),
        array( -1, 74, 74, 74, 74, 74, 74, 74,
            -1, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 264, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, 77, 77, 77, 77, 77, 77, 77,
            77, 77, 77, 77, 77, 77, 77, 77,
            77, 77, 77, 77, 77, 77, -1, 77,
            77, 77, 77, 77, 77, 77, 77, 77,
            77, 77, 77, 77, 77, 77, 77, 77,
            77, 77, 77, 77, 77, 77, 77, 77,
            77, 77, 77, 77, 77, 77, 77, 77 ),
        array( 1, 110, 110, 110, 110, 110, 110, 110,
            110, 110, 110, 110, 110, 110, 110, 136,
            110, 110, 110, 110, 110, 110, 110, 110,
            110, 110, 110, 110, 110, 110, 110, 110,
            110, 110, 110, 110, 110, 110, 110, 110,
            110, 110, 110, 110, 110, 110, 110, 110,
            110, 110, 110, 110, 110, 110, 110, 110 ),
        array( -1, -1, -1, 7, -1, -1, 8, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 8, 8, 8, 8, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 8,
            8, 8, 8, 8, 8, 8, 8, 8,
            8, -1, 7, 8, 8, -1, -1, 8,
            8, 8, 8, -1, 8, 8, 7, -1 ),
        array( -1, -1, -1, -1, -1, 3, 5, -1,
            -1, 122, -1, 3, 3, 6, 125, -1,
            3, 5, 5, 5, 5, 3, 3, 127,
            -1, 3, -1, -1, -1, 3, -1, 5,
            5, 5, 5, 5, 5, 5, 5, 5,
            5, 3, -1, 5, 5, 3, -1, 5,
            5, 5, 5, -1, 5, 5, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 129, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 129, 129, 129, 129, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 129,
            129, 129, 129, 129, 129, 278, 129, 129,
            325, -1, -1, 129, 129, -1, -1, 129,
            310, 129, 129, -1, 129, 129, -1, -1 ),
        array( -1, -1, -1, -1, -1, 85, -1, -1,
            -1, -1, -1, 85, 85, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 88, -1, -1,
            -1, 133, -1, 88, 88, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 89, -1, -1,
            -1, -1, -1, 89, 89, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 90, -1, -1,
            -1, -1, -1, 90, 90, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 91, -1, -1,
            -1, -1, -1, 91, 91, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 93, -1, -1,
            -1, -1, -1, 93, 93, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 35, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 94, -1, -1,
            -1, -1, -1, 94, 94, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 95, -1, -1,
            -1, -1, -1, 95, 95, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 204, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 204, 204, 204, 204, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 204,
            204, 204, 204, 204, 204, 204, 204, 204,
            204, -1, -1, 204, 204, -1, -1, 204,
            204, 204, 204, -1, 204, 204, -1, -1 ),
        array( -1, -1, -1, -1, -1, 97, -1, -1,
            -1, -1, -1, 97, 97, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 58, 59, -1,
            -1, -1, -1, 58, 58, -1, -1, -1,
            -1, 59, 59, 59, 59, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 59,
            59, 59, 59, 59, 59, 59, 59, 59,
            59, -1, -1, 59, 59, -1, -1, 59,
            59, 59, 59, -1, 59, 59, -1, -1 ),
        array( -1, -1, -1, -1, -1, 99, -1, -1,
            -1, -1, -1, 99, 99, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 100, -1, -1,
            -1, -1, -1, 100, 100, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 101, -1, -1,
            -1, -1, -1, 101, 101, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 102, -1, -1,
            -1, -1, -1, 102, 102, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 103, -1, -1,
            -1, -1, -1, 103, 103, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, 105, 105, 105, 105, 105, 105, 105,
            105, 105, 105, 105, 105, 105, 105, 224,
            105, 105, 105, 105, 105, 105, 105, 105,
            105, 105, 105, 105, 105, 105, 105, 105,
            105, 105, 105, 105, 105, 105, 105, 105,
            105, 105, 105, 105, 105, 105, 105, 105,
            105, 105, 105, 105, 105, 105, 105, 105 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 63, -1, 226,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, 250,
            -1, -1, -1, -1, -1, -1, -1, -1,
            251, -1, -1, -1, -1, -1, -1, -1,
            320, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 118, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, 110, 110, 110, 110, 110, 110, 110,
            110, 110, 110, 110, 110, 110, 110, 272,
            110, 110, 110, 110, 110, 110, 110, 110,
            110, 110, 110, 110, 110, 110, 110, 110,
            110, 110, 110, 110, 110, 110, 110, 110,
            110, 110, 110, 110, 110, 110, 110, 110,
            110, 110, 110, 110, 110, 110, 110, 110 ),
        array( -1, -1, -1, 3, 3, 3, -1, 3,
            -1, 3, 3, 3, 3, 3, 3, 3,
            3, -1, -1, -1, -1, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 3, 3, -1, -1, 3, 3, -1,
            -1, -1, -1, 3, -1, -1, 3, 3 ),
        array( -1, 28, 28, 28, 28, 28, 28, 28,
            28, 28, 28, 28, 28, 28, 28, 28,
            28, 28, 28, 28, 28, 28, 28, 28,
            28, 28, 28, -1, 28, 28, 28, 28,
            28, 28, 28, 28, 28, 28, 28, 28,
            28, 28, 28, 28, 28, 28, 28, 28,
            28, 28, 28, 28, 28, 28, 28, 28 ),
        array( -1, 111, 3, 3, 3, 3, 3, 3,
            119, 3, 3, 3, 3, 17, 3, 3,
            3, 3, 3, 3, 3, -1, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 3 ),
        array( 1, 120, 120, 120, 120, 91, 33, 120,
            31, 34, 120, 91, 91, 32, 120, 120,
            120, 33, 33, 33, 33, 120, 120, 120,
            120, 120, 120, 120, 120, 120, 120, 33,
            33, 33, 33, 33, 33, 33, 33, 33,
            33, 120, 120, 33, 33, 120, 120, 33,
            33, 33, 33, 120, 33, 33, 120, 120 ),
        array( -1, 37, 37, 115, 37, 95, 115, 115,
            37, 37, 37, 95, 95, -1, 37, 115,
            37, 115, 115, 115, 115, 37, 37, 37,
            37, 37, 37, -1, -1, 37, 37, 115,
            115, 115, 115, 115, 115, 115, 115, 115,
            115, 37, 115, 115, 115, 37, 37, 115,
            115, 115, 115, 37, 115, 115, 115, 37 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 205, -1 ),
        array( -1, 222, 222, 222, 222, 222, 222, 222,
            222, 222, 222, 222, 222, 222, 222, 222,
            222, 222, 222, 222, 222, 222, 222, 222,
            222, 222, 222, 60, 222, 222, 222, 222,
            222, 222, 222, 222, 222, 222, 222, 222,
            222, 222, 222, 222, 222, 222, 222, 222,
            222, 222, 222, 222, 222, 222, 222, 222 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 78, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 3, -1, -1,
            -1, -1, -1, 3, 3, -1, -1, -1,
            3, -1, -1, -1, -1, 3, 3, -1,
            -1, 3, -1, -1, -1, 3, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 3, -1, -1, -1, 3, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, 223, 223, 223, 223, 223, 223, 223,
            223, 223, 223, 223, 223, 223, 223, 223,
            223, 223, 223, 223, 223, 223, 223, 223,
            223, 223, 223, 223, 104, 223, 223, 223,
            223, 223, 223, 223, 223, 223, 223, 223,
            223, 223, 223, 223, 223, 223, 223, 223,
            223, 223, 223, 223, 223, 223, 223, 223 ),
        array( -1, -1, -1, -1, -1, 131, 9, -1,
            -1, 133, -1, 131, 131, 10, -1, -1,
            -1, 9, 9, 9, 9, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 9,
            9, 9, 9, 9, 9, 9, 9, 9,
            9, -1, -1, 9, 9, -1, -1, 9,
            9, 9, 9, -1, 9, 9, -1, -1 ),
        array( -1, 46, 46, 46, 46, 46, 46, 46,
            46, 46, 46, 46, 46, 46, 46, 46,
            46, 46, 46, 46, 46, 46, 46, 46,
            46, -1, 46, 46, -1, 46, 46, 46,
            46, 46, 46, 46, 46, 46, 46, 46,
            46, 46, 46, 46, 46, 46, 46, 46,
            46, 46, 46, 46, 46, 46, 46, 46 ),
        array( -1, 105, 105, 105, 105, 105, 105, 105,
            105, 105, 105, 105, 105, 105, 105, 225,
            105, 105, 105, 105, 105, 105, 105, 105,
            105, 105, 105, 105, 105, 105, 105, 105,
            105, 105, 105, 105, 105, 105, 105, 105,
            105, 105, 105, 105, 105, 105, 105, 105,
            105, 105, 105, 105, 105, 105, 105, 105 ),
        array( -1, -1, -1, -1, -1, -1, 11, -1,
            -1, -1, -1, -1, -1, 12, -1, 135,
            13, 11, 11, 11, 11, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 11,
            11, 11, 11, 11, 11, 11, 11, 11,
            11, -1, -1, 11, 11, -1, -1, 11,
            11, 11, 11, -1, 11, 11, -1, -1 ),
        array( -1, 230, 67, 230, 230, 230, 230, 230,
            230, 230, 230, 230, 230, 230, 230, 230,
            230, 230, 230, 230, 230, 230, 230, 230,
            230, 230, 230, 230, 230, 230, 230, 230,
            230, 230, 230, 230, 230, 230, 230, 230,
            230, 230, 230, 230, 230, 230, 230, 230,
            230, 230, 230, 230, 230, 230, 230, 230 ),
        array( -1, 127, 127, 127, 127, 127, 127, 127,
            127, 127, 127, 127, 127, 14, 127, 127,
            127, 127, 127, 127, 127, 127, 127, 127,
            127, 127, 127, 127, 127, 127, 127, 127,
            127, 127, 127, 127, 127, 127, 127, 127,
            127, 127, 127, 127, 127, 127, 127, 127,
            127, 127, 127, 127, 127, 127, 127, 127 ),
        array( -1, -1, -1, 231, -1, -1, 231, 232,
            -1, -1, -1, -1, -1, -1, -1, -1,
            233, 231, 231, 231, 231, -1, -1, -1,
            318, -1, -1, -1, -1, -1, -1, 231,
            231, 231, 231, 231, 231, 231, 231, 231,
            231, 231, 231, 231, 231, -1, -1, 231,
            231, 231, 231, 68, 231, 231, 231, 234 ),
        array( -1, -1, -1, 129, -1, -1, 129, 137,
            -1, -1, 279, -1, -1, -1, -1, -1,
            138, 129, 129, 129, 129, -1, -1, -1,
            139, -1, -1, -1, -1, -1, -1, 129,
            129, 129, 129, 129, 129, 129, 129, 129,
            129, 129, 129, 129, 129, 15, 16, 129,
            129, 129, 129, -1, 129, 129, 129, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 284, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 69, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 131, -1, -1,
            -1, 133, -1, 131, 131, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, 249, 71, 249, 249, 249, 249, 249,
            249, 249, 249, 249, 249, 249, 249, 249,
            249, 249, 249, 249, 249, 249, 249, 249,
            249, 249, 249, 249, 249, 249, 249, 249,
            249, 249, 249, 249, 249, 249, 249, 249,
            249, 249, 249, 249, 249, 249, 249, 249,
            249, 249, 249, 249, 249, 249, 249, 249 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            18, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 252, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            253, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 72, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 19,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, 110, 110, 110, 110, 110, 110, 110,
            110, 110, 110, 110, 110, 110, 110, 273,
            110, 110, 110, 110, 110, 110, 110, 110,
            110, 110, 110, 110, 110, 110, 110, 110,
            110, 110, 110, 110, 110, 110, 110, 110,
            110, 110, 110, 110, 110, 110, 110, 110,
            110, 110, 110, 110, 110, 110, 110, 110 ),
        array( -1, -1, -1, -1, -1, -1, 142, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 142, 142, 142, 142, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 142,
            142, 142, 142, 142, 142, 142, 142, 142,
            142, -1, -1, 142, 142, -1, -1, 142,
            142, 142, 142, -1, 142, 142, -1, -1 ),
        array( -1, -1, -1, 144, -1, -1, 144, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 144, 144, 144, 144, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 144,
            144, 144, 144, 144, 144, 144, 144, 144,
            144, 144, 144, 144, 144, -1, -1, 144,
            144, 144, 144, -1, 144, 144, 144, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 145, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 129, -1, -1, 129, 137,
            -1, -1, 146, -1, -1, -1, -1, -1,
            138, 129, 129, 129, 129, -1, -1, -1,
            139, -1, -1, -1, -1, -1, -1, 129,
            129, 129, 129, 129, 129, 129, 129, 129,
            129, 129, 129, 129, 129, 15, 16, 129,
            129, 129, 129, -1, 129, 129, 129, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 148, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 142, -1, -1, 142, 137,
            -1, -1, 279, -1, -1, -1, -1, -1,
            149, 142, 142, 142, 142, -1, -1, -1,
            150, -1, -1, -1, -1, -1, -1, 142,
            142, 142, 142, 142, 142, 142, 142, 142,
            142, 142, 142, 142, 142, 15, 16, 142,
            142, 142, 142, -1, 142, 142, 142, -1 ),
        array( -1, -1, -1, -1, -1, -1, 143, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 143, 143, 143, 143, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 143,
            143, 143, 143, 143, 143, 143, 143, 143,
            143, -1, -1, 143, 143, 15, -1, 143,
            143, 143, 143, -1, 143, 143, -1, -1 ),
        array( -1, -1, -1, 144, -1, -1, 144, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 144, 144, 144, 144, -1, 151, -1,
            152, -1, -1, -1, -1, -1, -1, 144,
            144, 144, 144, 144, 144, 144, 144, 144,
            144, 144, 144, 144, 144, -1, -1, 144,
            144, 144, 144, -1, 144, 144, 144, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 138, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 153, -1,
            -1, -1, -1, -1, -1, -1, 154, -1,
            -1, 153, 153, 153, 153, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 153,
            153, 153, 153, 153, 153, 153, 153, 153,
            153, -1, -1, 153, 153, -1, -1, 153,
            153, 153, 153, -1, 153, 153, -1, -1 ),
        array( -1, -1, -1, 129, -1, -1, 129, 137,
            -1, -1, 155, -1, -1, -1, -1, -1,
            138, 129, 129, 129, 129, -1, -1, -1,
            139, -1, -1, -1, -1, -1, -1, 129,
            129, 129, 129, 129, 129, 129, 129, 129,
            129, 129, 129, 129, 129, 15, 16, 129,
            129, 129, 129, -1, 129, 129, 129, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 157, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 158, -1, -1, 158, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 158, 158, 158, 158, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 158,
            158, 158, 158, 158, 158, 158, 158, 158,
            158, 158, 158, 158, 158, -1, -1, 158,
            158, 158, 158, -1, 158, 158, 158, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 281, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, 137,
            -1, -1, 279, -1, -1, -1, -1, -1,
            138, -1, -1, -1, -1, -1, -1, -1,
            139, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 15, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 159, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 153, -1, -1, 153, 160,
            -1, -1, -1, -1, -1, -1, -1, -1,
            161, 153, 153, 153, 153, -1, -1, -1,
            311, -1, -1, -1, -1, -1, -1, 153,
            153, 153, 153, 153, 153, 153, 153, 153,
            153, 153, 153, 153, 153, 20, 21, 153,
            153, 153, 153, -1, 153, 153, 153, -1 ),
        array( -1, -1, -1, -1, -1, -1, 153, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 153, 153, 153, 153, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 153,
            153, 153, 153, 153, 153, 153, 153, 153,
            153, -1, -1, 153, 153, -1, -1, 153,
            153, 153, 153, -1, 153, 153, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 143, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 143, 143, 143, 143, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 143,
            143, 143, 143, 143, 143, 143, 143, 143,
            143, -1, -1, 143, 143, 22, -1, 143,
            143, 143, 143, -1, 143, 143, -1, -1 ),
        array( -1, -1, -1, 129, -1, -1, 129, 137,
            -1, -1, 162, -1, -1, -1, -1, -1,
            138, 129, 129, 129, 129, -1, -1, -1,
            139, -1, -1, -1, -1, -1, -1, 129,
            129, 129, 129, 129, 129, 129, 129, 129,
            129, 129, 129, 129, 129, 15, 16, 129,
            129, 129, 129, -1, 129, 129, 129, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 163, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 158, -1, -1, 158, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 158, 158, 158, 158, -1, 164, -1,
            165, -1, -1, -1, -1, -1, -1, 158,
            158, 158, 158, 158, 158, 158, 158, 158,
            158, 158, 158, 158, 158, -1, -1, 158,
            158, 158, 158, -1, 158, 158, 158, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 151, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 151, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 166, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 166, 166, 166, 166, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 166,
            166, 166, 166, 166, 166, 166, 166, 166,
            166, -1, -1, 166, 166, -1, -1, 166,
            166, 166, 166, -1, 166, 166, -1, -1 ),
        array( -1, -1, -1, 167, -1, -1, 167, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 167, 167, 167, 167, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 167,
            167, 167, 167, 167, 167, 167, 167, 167,
            167, 167, 167, 167, 167, -1, -1, 167,
            167, 167, 167, -1, 167, 167, 167, -1 ),
        array( -1, -1, -1, -1, -1, -1, 143, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 143, 143, 143, 143, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 143,
            143, 143, 143, 143, 143, 143, 143, 143,
            143, -1, -1, 143, 143, 23, -1, 143,
            143, 143, 143, -1, 143, 143, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 168, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, 137,
            -1, -1, 279, -1, -1, -1, -1, -1,
            149, -1, -1, -1, -1, -1, -1, -1,
            150, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 15, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 169, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 166, -1, -1, 166, 160,
            -1, -1, -1, -1, -1, -1, -1, -1,
            170, 166, 166, 166, 166, -1, -1, -1,
            314, -1, -1, -1, -1, -1, -1, 166,
            166, 166, 166, 166, 166, 166, 166, 166,
            166, 166, 166, 166, 166, 20, 21, 166,
            166, 166, 166, -1, 166, 166, 166, -1 ),
        array( -1, -1, -1, 167, -1, -1, 167, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 167, 167, 167, 167, -1, 171, -1,
            172, -1, -1, -1, -1, -1, -1, 167,
            167, 167, 167, 167, 167, 167, 167, 167,
            167, 167, 167, 167, 167, -1, -1, 167,
            167, 167, 167, -1, 167, 167, 167, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            24, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 164, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 164, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 174, -1, -1, 174, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 174, 174, 174, 174, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 174,
            174, 174, 174, 174, 174, 174, 174, 174,
            174, 174, 174, 174, 174, -1, -1, 174,
            174, 174, 174, -1, 174, 174, 174, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, 160,
            -1, -1, -1, -1, -1, -1, -1, -1,
            161, -1, -1, -1, -1, -1, -1, -1,
            311, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 20, 21, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 175, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 129, -1, -1, 129, 137,
            -1, -1, 176, -1, -1, -1, -1, -1,
            138, 129, 129, 129, 129, -1, -1, -1,
            139, -1, -1, -1, -1, -1, -1, 129,
            129, 129, 129, 129, 129, 129, 129, 129,
            129, 129, 129, 129, 129, 15, 16, 129,
            129, 129, 129, -1, 129, 129, 129, -1 ),
        array( -1, -1, -1, 174, -1, -1, 174, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 174, 174, 174, 174, -1, 177, -1,
            178, -1, -1, -1, -1, -1, -1, 174,
            174, 174, 174, 174, 174, 174, 174, 174,
            174, 174, 174, 174, 174, -1, -1, 174,
            174, 174, 174, -1, 174, 174, 174, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 171, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 171, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 179, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 179, 179, 179, 179, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 179,
            179, 179, 179, 179, 179, 179, 179, 179,
            179, -1, -1, 179, 179, -1, -1, 179,
            179, 179, 179, -1, 179, 179, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, 160,
            -1, -1, -1, -1, -1, -1, -1, -1,
            170, -1, -1, -1, -1, -1, -1, -1,
            314, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 20, 21, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 180, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 179, -1, -1, 179, 181,
            -1, -1, -1, -1, -1, -1, -1, -1,
            182, 179, 179, 179, 179, -1, -1, -1,
            316, -1, -1, -1, -1, -1, -1, 179,
            179, 179, 179, 179, 179, 179, 179, 179,
            179, 179, 179, 179, 179, 25, -1, 179,
            179, 179, 179, 282, 179, 179, 179, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 177, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 177, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 183, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 183, 183, 183, 183, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 183,
            183, 183, 183, 183, 183, 183, 183, 183,
            183, -1, -1, 183, 183, -1, -1, 183,
            183, 183, 183, -1, 183, 183, -1, -1 ),
        array( -1, -1, -1, 184, -1, -1, 184, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 184, 184, 184, 184, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 184,
            184, 184, 184, 184, 184, 184, 184, 184,
            184, 184, 184, 184, 184, -1, -1, 184,
            184, 184, 184, -1, 184, 184, 184, -1 ),
        array( -1, -1, -1, 183, -1, -1, 183, 181,
            -1, -1, -1, -1, -1, -1, -1, -1,
            186, 183, 183, 183, 183, -1, -1, -1,
            317, -1, -1, -1, -1, -1, -1, 183,
            183, 183, 183, 183, 183, 183, 183, 183,
            183, 183, 183, 183, 183, 25, -1, 183,
            183, 183, 183, 282, 183, 183, 183, -1 ),
        array( -1, -1, -1, 184, -1, -1, 184, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 184, 184, 184, 184, -1, 187, -1,
            188, -1, -1, -1, -1, -1, -1, 184,
            184, 184, 184, 184, 184, 184, 184, 184,
            184, 184, 184, 184, 184, -1, -1, 184,
            184, 184, 184, -1, 184, 184, 184, -1 ),
        array( -1, -1, -1, 185, -1, -1, 185, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 185, 185, 185, 185, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 185,
            185, 185, 185, 185, 185, 185, 185, 185,
            185, 185, 185, 185, 185, 26, -1, 185,
            185, 185, 185, 189, 185, 185, 185, -1 ),
        array( -1, -1, -1, 190, -1, -1, 190, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 190, 190, 190, 190, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 190,
            190, 190, 190, 190, 190, 190, 190, 190,
            190, 190, 190, 190, 190, -1, -1, 190,
            190, 190, 190, -1, 190, 190, 190, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, 181,
            -1, -1, -1, -1, -1, -1, -1, -1,
            182, -1, -1, -1, -1, -1, -1, -1,
            316, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 25, -1, -1,
            -1, -1, -1, 282, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 191, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 192, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 192, 192, 192, 192, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 192,
            192, 192, 192, 192, 192, 192, 192, 192,
            192, -1, -1, 192, 192, -1, -1, 192,
            192, 192, 192, -1, 192, 192, -1, -1 ),
        array( -1, -1, -1, 190, -1, -1, 190, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 190, 190, 190, 190, -1, 193, -1,
            194, -1, -1, -1, -1, -1, -1, 190,
            190, 190, 190, 190, 190, 190, 190, 190,
            190, 190, 190, 190, 190, -1, -1, 190,
            190, 190, 190, -1, 190, 190, 190, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 187, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 187, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 192, -1, -1, 192, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 192, 192, 192, 192, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 192,
            192, 192, 192, 192, 192, 192, 192, 192,
            192, 192, 192, 192, 192, 27, -1, 192,
            192, 192, 192, -1, 192, 192, 192, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, 181,
            -1, -1, -1, -1, -1, -1, -1, -1,
            186, -1, -1, -1, -1, -1, -1, -1,
            317, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 25, -1, -1,
            -1, -1, -1, 282, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 195, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 193, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 193, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( 1, 28, 28, 28, 28, 28, 28, 28,
            28, 28, 28, 28, 28, 28, 28, 28,
            28, 28, 28, 28, 28, 28, 28, 28,
            28, 112, 28, 29, 28, 28, 28, 28,
            28, 28, 28, 28, 28, 28, 28, 28,
            28, 28, 28, 28, 28, 28, 28, 28,
            28, 28, 28, 28, 28, 28, 28, 28 ),
        array( 1, 37, 37, 38, 37, -1, 275, 275,
            92, 39, 37, 120, -1, 40, 37, 275,
            37, 275, 275, 275, 275, 37, 37, 37,
            37, 37, 37, 41, 42, 37, 37, 275,
            275, 275, 275, 275, 275, 275, 275, 275,
            275, 37, 38, 275, 275, 37, 37, 275,
            275, 275, 275, 37, 275, 275, 38, 37 ),
        array( 1, 120, 120, 120, 120, 43, 120, 120,
            120, 120, 120, 43, 43, 120, 120, 120,
            120, 120, 120, 120, 120, 120, 120, 120,
            120, 120, 120, 120, 120, 120, 120, 120,
            120, 120, 120, 120, 120, 120, 120, 120,
            120, 120, 120, 120, 120, 120, 120, 120,
            120, 120, 120, 120, 120, 120, 120, 120 ),
        array( 1, 44, 44, 44, 44, -1, 44, 44,
            44, 44, 44, 44, -1, 45, 44, 44,
            44, 44, 44, 44, 44, 44, 44, 44,
            44, 44, 44, 44, 44, 44, 44, 44,
            44, 44, 44, 44, 44, 44, 44, 44,
            44, 44, 44, 44, 44, 44, 44, 44,
            44, 44, 44, 44, 44, 44, 44, 44 ),
        array( 1, 46, 46, 46, 46, 46, 46, 46,
            46, 46, 46, 46, 46, 46, 46, 46,
            46, 46, 46, 46, 46, 96, 46, 46,
            116, 123, 46, 46, 47, 46, 46, 46,
            46, 46, 46, 46, 46, 46, 46, 46,
            46, 46, 46, 46, 46, 46, 46, 46,
            46, 46, 46, 46, 46, 46, 46, 46 ),
        array( -1, -1, -1, 204, -1, -1, 204, 206,
            -1, -1, 283, -1, -1, -1, -1, -1,
            207, 204, 204, 204, 204, -1, -1, -1,
            208, -1, -1, -1, -1, -1, -1, 204,
            204, 204, 204, 204, 204, 204, 204, 204,
            204, 204, 204, 204, 204, 48, 49, 204,
            204, 204, 204, -1, 204, 204, 204, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 287, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 209, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 209, 209, 209, 209, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 209,
            209, 209, 209, 209, 209, 209, 209, 209,
            209, -1, -1, 209, 209, -1, -1, 209,
            209, 209, 209, -1, 209, 209, -1, -1 ),
        array( -1, -1, -1, 211, -1, -1, 211, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 211, 211, 211, 211, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 211,
            211, 211, 211, 211, 211, 211, 211, 211,
            211, 211, 211, 211, 211, -1, -1, 211,
            211, 211, 211, -1, 211, 211, 211, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 299, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 212, -1 ),
        array( -1, -1, -1, 209, -1, -1, 209, 206,
            -1, -1, 283, -1, -1, -1, -1, -1,
            213, 209, 209, 209, 209, -1, -1, -1,
            312, -1, -1, -1, -1, -1, -1, 209,
            209, 209, 209, 209, 209, 209, 209, 209,
            209, 209, 209, 209, 209, 48, 49, 209,
            209, 209, 209, -1, 209, 209, 209, -1 ),
        array( -1, -1, -1, -1, -1, -1, 210, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 210, 210, 210, 210, -1, -1, -1,
            214, -1, -1, -1, -1, -1, -1, 210,
            210, 210, 210, 210, 210, 210, 210, 210,
            210, -1, -1, 210, 210, 48, -1, 210,
            210, 210, 210, -1, 210, 210, -1, -1 ),
        array( -1, -1, -1, 211, -1, -1, 211, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 211, 211, 211, 211, -1, 215, -1,
            216, -1, -1, -1, -1, -1, -1, 211,
            211, 211, 211, 211, 211, 211, 211, 211,
            211, 211, 211, 211, 211, -1, -1, 211,
            211, 211, 211, -1, 211, 211, 211, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 48, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 48, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 217, -1, -1, 217, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 217, 217, 217, 217, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 217,
            217, 217, 217, 217, 217, 217, 217, 217,
            217, 217, 217, 217, 217, -1, -1, 217,
            217, 217, 217, -1, 217, 217, 217, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 212, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, 206,
            -1, -1, 283, -1, -1, -1, -1, -1,
            207, -1, -1, -1, -1, -1, -1, -1,
            208, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 48, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 218, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 217, -1, -1, 217, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 217, 217, 217, 217, -1, 219, -1,
            220, -1, -1, -1, -1, -1, -1, 217,
            217, 217, 217, 217, 217, 217, 217, 217,
            217, 217, 217, 217, 217, -1, -1, 217,
            217, 217, 217, -1, 217, 217, 217, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 215, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 215, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, 206,
            -1, -1, 283, -1, -1, -1, -1, -1,
            213, -1, -1, -1, -1, -1, -1, -1,
            312, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 48, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 221, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 219, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 219, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, 105, 105, 105, 105, 105, 105, 105,
            105, 105, 105, 105, 105, 105, 105, -1,
            105, 105, 105, 105, 105, 105, 105, 105,
            105, 105, 105, 105, 105, 105, 105, 105,
            105, 105, 105, 105, 105, 105, 105, 105,
            105, 105, 105, 105, 105, 105, 105, 105,
            105, 105, 105, 105, 105, 105, 105, 105 ),
        array( -1, 62, 62, 62, 62, 62, 62, 62,
            62, 62, 62, 62, 62, 63, 62, 106,
            62, 62, 62, 62, 62, 62, 62, 62,
            62, 62, 62, 62, 62, 62, 62, 62,
            62, 62, 62, 62, 62, 62, 62, 62,
            62, 62, 62, 62, 62, 62, 62, 62,
            62, 62, 62, 62, 62, 62, 62, 62 ),
        array( 1, 64, 64, 64, 64, 64, 64, 64,
            64, 64, 64, 64, 64, 64, 64, 64,
            64, 64, 64, 64, 64, 64, 65, 64,
            64, 64, 64, 64, 64, 64, 64, 64,
            64, 64, 64, 64, 64, 64, 64, 64,
            64, 64, 64, 64, 64, 64, 64, 64,
            64, 64, 64, 64, 64, 64, 64, 64 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 66, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( 1, 120, 126, 120, 120, -1, 128, 120,
            120, 120, 120, 120, -1, 120, 120, 120,
            120, 128, 128, 128, 128, 120, 120, 120,
            120, 120, 120, 120, 120, 120, 120, 128,
            128, 128, 128, 128, 128, 128, 128, 128,
            128, 120, 120, 128, 128, 120, 120, 128,
            128, 128, 128, 120, 128, 128, 120, 130 ),
        array( -1, -1, -1, -1, -1, -1, 235, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 235, 235, 235, 235, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 235,
            235, 235, 235, 235, 235, 235, 235, 235,
            235, -1, -1, 235, 235, -1, -1, 235,
            235, 235, 235, -1, 235, 235, -1, -1 ),
        array( -1, -1, -1, 236, -1, -1, 236, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 236, 236, 236, 236, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 236,
            236, 236, 236, 236, 236, 236, 236, 236,
            236, 236, 236, 236, 236, -1, -1, 236,
            236, 236, 236, -1, 236, 236, 236, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 237, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 68, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 235, -1, -1, 235, 232,
            -1, -1, -1, -1, -1, -1, -1, -1,
            239, 235, 235, 235, 235, -1, -1, -1,
            319, -1, -1, -1, -1, -1, -1, 235,
            235, 235, 235, 235, 235, 235, 235, 235,
            235, 235, 235, 235, 235, -1, -1, 235,
            235, 235, 235, 68, 235, 235, 235, 234 ),
        array( -1, -1, -1, 236, -1, -1, 236, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 236, 236, 236, 236, -1, 240, -1,
            241, -1, -1, -1, -1, -1, -1, 236,
            236, 236, 236, 236, 236, 236, 236, 236,
            236, 236, 236, 236, 236, -1, -1, 236,
            236, 236, 236, -1, 236, 236, 236, -1 ),
        array( -1, -1, -1, -1, -1, -1, 242, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 242, 242, 242, 242, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 242,
            242, 242, 242, 242, 242, 242, 242, 242,
            242, -1, -1, 242, 242, -1, -1, 242,
            242, 242, 242, -1, 242, 242, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 238, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 238, 238, 238, 238, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 238,
            238, 238, 238, 238, 238, 238, 238, 238,
            238, -1, -1, 238, 238, 69, -1, 238,
            238, 238, 238, -1, 238, 238, -1, -1 ),
        array( -1, -1, -1, 243, -1, -1, 243, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 243, 243, 243, 243, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 243,
            243, 243, 243, 243, 243, 243, 243, 243,
            243, 243, 243, 243, 243, -1, -1, 243,
            243, 243, 243, -1, 243, 243, 243, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, 232,
            -1, -1, -1, -1, -1, -1, -1, -1,
            233, -1, -1, -1, -1, -1, -1, -1,
            318, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 68, -1, -1, -1, 234 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 244, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 242, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 242, 242, 242, 242, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 242,
            242, 242, 242, 242, 242, 242, 242, 242,
            242, -1, -1, 242, 242, 68, -1, 242,
            242, 242, 242, -1, 242, 242, -1, -1 ),
        array( -1, -1, -1, 243, -1, -1, 243, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 243, 243, 243, 243, -1, 245, -1,
            246, -1, -1, -1, -1, -1, -1, 243,
            243, 243, 243, 243, 243, 243, 243, 243,
            243, 243, 243, 243, 243, -1, -1, 243,
            243, 243, 243, -1, 243, 243, 243, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 240, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 240, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, 232,
            -1, -1, -1, -1, -1, -1, -1, -1,
            239, -1, -1, -1, -1, -1, -1, -1,
            319, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 68, -1, -1, -1, 234 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 247, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 245, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 245, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( 1, 120, 132, 120, 120, -1, 70, 120,
            120, 120, 120, 120, -1, 120, 120, 120,
            120, 70, 70, 70, 70, 120, 120, 120,
            120, 120, 120, 120, 120, 120, 120, 70,
            70, 70, 70, 70, 70, 70, 70, 70,
            70, 120, 120, 70, 70, 120, 120, 70,
            70, 70, 70, 120, 70, 70, 120, 134 ),
        array( -1, -1, -1, -1, -1, -1, 276, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 276, 276, 276, 276, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 276,
            276, 276, 276, 276, 276, 276, 276, 276,
            276, -1, -1, 276, 276, -1, -1, 276,
            276, 276, 276, -1, 276, 276, -1, -1 ),
        array( -1, -1, -1, 254, -1, -1, 254, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 254, 254, 254, 254, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 254,
            254, 254, 254, 254, 254, 254, 254, 254,
            254, 254, 254, 254, 254, -1, -1, 254,
            254, 254, 254, -1, 254, 254, 254, -1 ),
        array( -1, -1, -1, -1, -1, -1, 255, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 255, 255, 255, 255, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 255,
            255, 255, 255, 255, 255, 255, 255, 255,
            255, -1, -1, 255, 255, -1, -1, 255,
            255, 255, 255, -1, 255, 255, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 256, -1 ),
        array( -1, -1, -1, 254, -1, -1, 254, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 254, 254, 254, 254, -1, 108, -1,
            258, -1, -1, -1, -1, -1, -1, 254,
            254, 254, 254, 254, 254, 254, 254, 254,
            254, 254, 254, 254, 254, -1, -1, 254,
            254, 254, 254, -1, 254, 254, 254, -1 ),
        array( -1, -1, -1, -1, -1, -1, 255, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 255, 255, 255, 255, -1, -1, -1,
            253, -1, -1, -1, -1, -1, -1, 255,
            255, 255, 255, 255, 255, 255, 255, 255,
            255, -1, -1, 255, 255, 72, -1, 255,
            255, 255, 255, -1, 255, 255, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 72, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 72, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 259, -1, -1, 259, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 259, 259, 259, 259, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 259,
            259, 259, 259, 259, 259, 259, 259, 259,
            259, 259, 259, 259, 259, -1, -1, 259,
            259, 259, 259, -1, 259, 259, 259, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 260, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 259, -1, -1, 259, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 259, 259, 259, 259, -1, 277, -1,
            261, -1, -1, -1, -1, -1, -1, 259,
            259, 259, 259, 259, 259, 259, 259, 259,
            259, 259, 259, 259, 259, -1, -1, 259,
            259, 259, 259, -1, 259, 259, 259, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 108, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 108, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 285, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( 1, 120, 120, 120, 120, -1, 120, 120,
            120, 120, 120, 120, -1, 120, 120, 120,
            120, 120, 120, 120, 120, 120, 120, 120,
            120, 120, 120, 120, 120, 120, 120, 120,
            120, 120, 120, 120, 120, 120, 120, 120,
            120, 120, 120, 120, 120, 120, 120, 120,
            120, 120, 120, 73, 120, 120, 120, 134 ),
        array( 1, 74, 74, 74, 74, 74, 74, 74,
            75, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, 74, 74, 74 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 265,
            265, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 266, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 266, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 267, 267, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 268, 268, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 269, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 270, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 270,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 76, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( 1, 77, 77, 77, 77, 77, 77, 77,
            77, 77, 77, 77, 77, 77, 77, 77,
            77, 77, 77, 77, 77, 77, 109, 77,
            77, 77, 77, 77, 77, 77, 77, 77,
            77, 77, 77, 77, 77, 77, 77, 77,
            77, 77, 77, 77, 77, 77, 77, 77,
            77, 77, 77, 77, 77, 77, 77, 77 ),
        array( -1, 110, 110, 110, 110, 110, 110, 110,
            110, 110, 110, 110, 110, 110, 110, -1,
            110, 110, 110, 110, 110, 110, 110, 110,
            110, 110, 110, 110, 110, 110, 110, 110,
            110, 110, 110, 110, 110, 110, 110, 110,
            110, 110, 110, 110, 110, 110, 110, 110,
            110, 110, 110, 110, 110, 110, 110, 110 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 80, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, 111, 3, 3, 3, 3, 3, 3,
            119, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, -1, 113, 3,
            3, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 3 ),
        array( -1, -1, -1, 276, -1, -1, 276, 250,
            -1, -1, -1, -1, -1, -1, -1, -1,
            257, 276, 276, 276, 276, -1, -1, -1,
            321, -1, -1, -1, -1, -1, -1, 276,
            276, 276, 276, 276, 276, 276, 276, 276,
            276, 276, 276, 276, 276, -1, -1, 276,
            276, 276, 276, -1, 276, 276, 276, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, 250,
            -1, -1, -1, -1, -1, -1, -1, -1,
            257, -1, -1, -1, -1, -1, -1, -1,
            321, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 129, -1, -1, 129, 137,
            -1, -1, 279, -1, -1, -1, -1, -1,
            138, 129, 129, 129, 129, -1, -1, -1,
            139, -1, -1, -1, -1, -1, -1, 129,
            129, 129, 129, 129, 129, 129, 129, 129,
            140, 129, 129, 129, 129, 15, 16, 129,
            129, 129, 129, -1, 129, 129, 129, -1 ),
        array( -1, -1, -1, -1, -1, -1, 143, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 143, 143, 143, 143, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 143,
            143, 143, 143, 143, 143, 143, 143, 143,
            143, -1, -1, 143, 143, -1, -1, 143,
            143, 143, 143, -1, 143, 143, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 280, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 185, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 185, 185, 185, 185, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 185,
            185, 185, 185, 185, 185, 185, 185, 185,
            185, -1, -1, 185, 185, -1, -1, 185,
            185, 185, 185, -1, 185, 185, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 210, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 210, 210, 210, 210, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 210,
            210, 210, 210, 210, 210, 210, 210, 210,
            210, -1, -1, 210, 210, -1, -1, 210,
            210, 210, 210, -1, 210, 210, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 238, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 238, 238, 238, 238, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 238,
            238, 238, 238, 238, 238, 238, 238, 238,
            238, -1, -1, 238, 238, -1, -1, 238,
            238, 238, 238, -1, 238, 238, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 277, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 277, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 129, -1, -1, 129, 137,
            -1, -1, 279, -1, -1, -1, -1, -1,
            138, 129, 129, 129, 129, -1, -1, -1,
            139, -1, -1, -1, -1, -1, -1, 129,
            129, 129, 129, 129, 129, 129, 129, 129,
            129, 129, 129, 129, 147, 15, 16, 129,
            129, 129, 129, -1, 129, 129, 129, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 288, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 129, -1, -1, 129, 137,
            -1, -1, 279, -1, -1, -1, -1, -1,
            138, 129, 129, 129, 129, -1, -1, -1,
            139, -1, -1, -1, -1, -1, -1, 129,
            129, 129, 129, 129, 129, 129, 129, 129,
            129, 129, 129, 129, 129, 15, 16, 129,
            156, 129, 129, -1, 129, 129, 129, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 291, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 129, -1, -1, 129, 137,
            -1, -1, 279, -1, -1, -1, -1, -1,
            138, 129, 129, 129, 129, -1, -1, -1,
            139, -1, -1, -1, -1, -1, -1, 129,
            129, 129, 129, 129, 129, 129, 129, 129,
            129, 129, 129, 129, 129, 15, 16, 129,
            129, 129, 173, -1, 129, 129, 129, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 294, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 296, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 298, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 300, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 302, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 304, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 306, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 308, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 129, -1, -1, 129, 137,
            -1, -1, 279, -1, -1, -1, -1, -1,
            138, 129, 129, 129, 129, -1, -1, -1,
            139, -1, -1, -1, -1, -1, -1, 129,
            129, 129, 129, 129, 129, 129, 129, 129,
            129, 129, 129, 129, 129, 15, 16, 129,
            129, 129, 129, -1, 286, 313, 129, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 289, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 301, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 212, -1 ),
        array( -1, -1, -1, 129, -1, -1, 129, 137,
            -1, -1, 279, -1, -1, -1, -1, -1,
            138, 129, 129, 129, 129, -1, -1, -1,
            139, -1, -1, -1, -1, -1, -1, 129,
            290, 129, 129, 129, 129, 129, 129, 129,
            129, 129, 129, 129, 129, 15, 16, 129,
            129, 129, 129, -1, 129, 129, 129, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 292, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 129, -1, -1, 129, 137,
            -1, -1, 279, -1, -1, -1, -1, -1,
            138, 129, 129, 129, 129, -1, -1, -1,
            139, -1, -1, -1, -1, -1, -1, 129,
            129, 293, 129, 129, 129, 129, 129, 129,
            129, 129, 129, 129, 129, 15, 16, 129,
            129, 129, 129, -1, 129, 129, 129, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 295, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 297, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 303, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 305, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 307, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 309, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 129, -1, -1, 129, 137,
            -1, -1, 279, -1, -1, -1, -1, -1,
            138, 129, 129, 129, 129, -1, -1, -1,
            139, -1, -1, -1, -1, -1, -1, 129,
            129, 129, 129, 129, 129, 129, 129, 129,
            129, 129, 129, 129, 129, 15, 16, 129,
            129, 315, 129, -1, 129, 129, 129, -1 ),
        array( -1, -1, -1, 129, -1, -1, 129, 137,
            -1, -1, 279, -1, -1, -1, -1, -1,
            138, 129, 129, 129, 129, -1, -1, -1,
            139, -1, -1, -1, -1, -1, -1, 129,
            129, 129, 129, 129, 129, 129, 129, 129,
            129, 129, 129, 129, 129, 15, 16, 129,
            322, 129, 129, -1, 129, 129, 129, -1 ),
        array( -1, -1, -1, 129, -1, -1, 129, 137,
            -1, -1, 279, -1, -1, -1, -1, -1,
            138, 129, 129, 129, 129, -1, -1, -1,
            139, -1, -1, -1, -1, -1, -1, 129,
            129, 129, 323, 129, 129, 129, 129, 129,
            129, 129, 129, 129, 129, 15, 16, 129,
            129, 129, 129, -1, 129, 129, 129, -1 ),
        array( -1, -1, -1, 129, -1, -1, 129, 137,
            -1, -1, 279, -1, -1, -1, -1, -1,
            138, 129, 129, 129, 129, -1, -1, -1,
            139, -1, -1, -1, -1, -1, -1, 129,
            129, 129, 129, 129, 129, 129, 129, 129,
            129, 129, 129, 129, 129, 15, 16, 324,
            129, 129, 129, -1, 129, 129, 129, -1 )
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
                       if ($yy_last_accept_state < 326) {
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
    $t =  $this->yytext();
    $t = substr($t,1,-1);
    $this->value = HTML_Template_Flexy_Token::factory('Var'  , $t, $this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 16:
{
    $this->value =  '';
    $this->flexyMethod = substr($this->yytext(),1,-1);
    $this->flexyArgs = array();
    $this->yybegin(IN_FLEXYMETHOD);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 17:
{ 
    /* ]]> -- marked section end */
    return $this->returnSimple();
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
    $this->yyCommentBegin = $this->yy_buffer_end;
    //$this->value = HTML_Template_Flexy_Token::factory('Comment',$this->yytext(),$this->yyline);
    $this->yybegin(IN_COM);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
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
    /* <![ -- marked section */
    $this->yybegin(IN_CDATA);
    $this->yyCdataBegin = $this->yy_buffer_end;
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 25:
{
    return $this->raiseError('invalid sytnax for Foreach','',true);
}
case 26:
{
    $this->value = HTML_Template_Flexy_Token::factory('Foreach', explode(',',substr($this->yytext(),9,-1)),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 27:
{
    $this->value = HTML_Template_Flexy_Token::factory('Foreach',  explode(',',substr($this->yytext(),9,-1)),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 28:
{
    $this->attrVal[] = $this->yytext();
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 29:
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
case 30:
{
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 31:
{
    // <foo^<bar> -- unclosed start tag */
    return $this->raiseError("Unclosed tags not supported"); 
}
case 32:
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
case 33:
{
    // <img src="xxx" ...ismap...> the ismap */
    $this->attributes[trim($this->yytext())] = true;
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 34:
{
    $this->yybegin(IN_NETDATA);
    $this->attributes["/"] = true;
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 35:
{
   // <a ^href = "xxx"> -- attribute name 
    $this->attrKey = substr(trim($this->yytext()),0,-1);
    $this->yybegin(IN_ATTRVAL);
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 36:
{
    $this->attributes["/"] = true;
    $this->value = HTML_Template_Flexy_Token::factory($this->tokenName,
        array($this->tagName,$this->attributes),
        $this->yyline);
    $this->yybegin(YYINITIAL);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 37:
{
    // <a href = ^http://foo/> -- unquoted literal HACK */                          
    $this->attributes[$this->attrKey] = trim($this->yytext());
    $this->yybegin(IN_ATTR);
    //   $this->raiseError("attribute value needs quotes");
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 38:
{
    // <a name = ^12pt> -- number token */
    $this->attributes[$this->attrKey] = trim($this->yytext());
    $this->yybegin(IN_ATTR);
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 39:
{
    return $this->raiseError("attribute value missing"); 
}
case 40:
{ 
    return $this->raiseError("Tag close found where attribute value expected"); 
}
case 41:
{
	//echo "STARTING SINGLEQUOTE";
    $this->attrVal = array( "'");
    $this->yybegin(IN_SINGLEQUOTE);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 42:
{
    //echo "START QUOTE";
    $this->attrVal =array("\"");
    $this->yybegin(IN_DOUBLEQUOTE);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 43:
{ 
    // whitespace switch back to IN_ATTR MODE.
    $this->value = '';
    $this->yybegin(IN_ATTR);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 44:
{ 
    return $this->raiseError("extraneous character in end tag"); 
}
case 45:
{ 
    $this->value = HTML_Template_Flexy_Token::factory($this->tokenName,
        array($this->tagName),
        $this->yyline);
        array($this->tagName);
    $this->yybegin(YYINITIAL);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 46:
{
    //echo "GOT DATA:".$this->yytext();
    $this->attrVal[] = $this->yytext();
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 47:
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
case 48:
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
case 49:
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
case 50:
{ 
    $this->value = HTML_Template_Flexy_Token::factory('WhiteSpace',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 51:
{
    return $this->raiseError("illegal character in markup declaration");
    return HTML_TEMPLATE_FLEXY_TOKEN_ERROR;
}
case 52:
{   
    $this->value = HTML_Template_Flexy_Token::factory('Number',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 53:
{ 
    $this->value = HTML_Template_Flexy_Token::factory('Name',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 54:
{ 
    $this->value = HTML_Template_Flexy_Token::factory('NameT',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 55:
{   
    $this->value = HTML_Template_Flexy_Token::factory('CloseTag',$this->yytext(),$this->yyline);
    $this->yybegin(YYINITIAL); 
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 56:
{
    // <!doctype foo ^[  -- declaration subset */
    $this->value = HTML_Template_Flexy_Token::factory('BeginDS',$this->yytext(),$this->yyline);
    $this->yybegin(IN_DS);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 57:
{ 
    $this->value = HTML_Template_Flexy_Token::factory('NumberT',$this->yytext(),$this->yyline);    
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 58:
{
    // <!entity ^% foo system "..." ...> -- parameter entity definition */
    $this->value = HTML_Template_Flexy_Token::factory('EntityPar',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 59:
{
    // <!doctype ^%foo;> -- parameter entity reference */
    $this->value = HTML_Template_Flexy_Token::factory('EntityRef',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 60:
{ 
    $this->value = HTML_Template_Flexy_Token::factory('Literal',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 61:
{
    // inside a comment (not - or not --
    // <!^--...-->   -- comment */   
    //$this->value = HTML_Template_Flexy_Token::factory('Comment',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 62:
{
	// inside comment -- without a >
	return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 63:
{   
    $this->value = HTML_Template_Flexy_Token::factory('Comment',
        '<!--'. substr($this->yy_buffer,$this->yyCommentBegin ,$this->yy_buffer_end - $this->yyCommentBegin),
        $this->yyline
    );
    $this->yybegin(YYINITIAL); 
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 64:
{ 
    $this->value = HTML_Template_Flexy_Token::factory('Declaration',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 65:
{ 
    // ] -- declaration subset close */
    $this->value = HTML_Template_Flexy_Token::factory('DSEndSubset',$this->yytext(),$this->yyline);
    $this->yybegin(IN_DSCOM); 
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 66:
{
    // ]]> -- marked section end */
     $this->value = HTML_Template_Flexy_Token::factory('DSEnd',$this->yytext(),$this->yyline);
    $this->yybegin(YYINITIAL);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 67:
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
case 68:
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
case 69:
{
    $t = $this->yytext();
    if ($t{1} == ':') {
        $this->flexyMethod .= substr($t,1,-1);
    }
    $this->value = HTML_Template_Flexy_Token::factory('Method'  , array($this->flexyMethod,$this->flexyArgs), $this->yyline);
    $this->yybegin(YYINITIAL);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 70:
{
    $t = $this->yytext();
    // add argument
    $this->flexyArgs[] = $t;
    $this->yybegin(IN_FLEXYMETHODQUOTED_END);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 71:
{
    $t = $this->yytext();
    $this->flexyArgs[] =$t;
    $this->yybegin(IN_FLEXYMETHODQUOTED_END);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 72:
{
    $t = $this->yytext();
    if ($p = strpos($t,':')) {
        $this->flexyMethod .= substr($t,$p,2);
    }
    $this->attrVal[] = HTML_Template_Flexy_Token::factory('Method'  , array($this->flexyMethod,$this->flexyArgs), $this->yyline);    
    $this->yybegin($this->flexyMethodState);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 73:
{
    $this->yybegin(IN_FLEXYMETHODQUOTED);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 74:
{
    // general text in script..
    $this->value = HTML_Template_Flexy_Token::factory('Text',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 75:
{
    // just < .. 
    $this->value = HTML_Template_Flexy_Token::factory('Text',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 76:
{
    // </script>
    $this->value = HTML_Template_Flexy_Token::factory('EndTag',
        array('/script'),
        $this->yyline);
    $this->yybegin(YYINITIAL);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 77:
{ 
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 78:
{ 
    /* ]]> -- marked section end */
    $this->value = HTML_Template_Flexy_Token::factory('Cdata',
        substr($this->yy_buffer,$this->yyCdataBegin ,$this->yy_buffer_end - $this->yyCdataBegin - 3 ),
        $this->yyline);
    $this->yybegin(YYINITIAL);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 79:
{
    // inside a comment (not - or not --
    // <!^--...-->   -- comment */   
    $this->value = HTML_Template_Flexy_Token::factory('DSComment',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 80:
{   
    $this->value = HTML_Template_Flexy_Token::factory('DSEnd', $this->yytext(),$this->yyline);
    $this->yybegin(YYINITIAL); 
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 82:
{
    return $this->raiseError("unexpected something: (".$this->yytext() .") character: 0x" . dechex(ord($this->yytext())));
}
case 83:
{
    //abcd -- data characters  
    // { added for flexy
    $this->value = HTML_Template_Flexy_Token::factory('Text',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 84:
{
    // &abc;
    $this->value = HTML_Template_Flexy_Token::factory('Text',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 85:
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
case 86:
{
    // &#123;
    $this->value = HTML_Template_Flexy_Token::factory('Text',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 87:
{
    // &#abc;
    $this->value = HTML_Template_Flexy_Token::factory('Text',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 88:
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
case 89:
{
    /* <!DOCTYPE -- markup declaration */
    if ($this->ignoreHTML) {
        return $this->returnSimple();
    }
    $this->value = HTML_Template_Flexy_Token::factory('Doctype',$this->yytext(),$this->yyline);
    $this->yybegin(IN_MD);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 90:
{
    /* <![ -- marked section */
    return $this->returnSimple();
}
case 91:
{
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 92:
{
    // <foo^<bar> -- unclosed start tag */
    return $this->raiseError("Unclosed tags not supported"); 
}
case 93:
{
    // <img src="xxx" ...ismap...> the ismap */
    $this->attributes[trim($this->yytext())] = true;
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 94:
{
    // <a href = ^http://foo/> -- unquoted literal HACK */                          
    $this->attributes[$this->attrKey] = trim($this->yytext());
    $this->yybegin(IN_ATTR);
    //   $this->raiseError("attribute value needs quotes");
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 95:
{
    // <a name = ^12pt> -- number token */
    $this->attributes[$this->attrKey] = trim($this->yytext());
    $this->yybegin(IN_ATTR);
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 96:
{
    //echo "GOT DATA:".$this->yytext();
    $this->attrVal[] = $this->yytext();
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 97:
{ 
    $this->value = HTML_Template_Flexy_Token::factory('WhiteSpace',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 98:
{
    return $this->raiseError("illegal character in markup declaration");
    return HTML_TEMPLATE_FLEXY_TOKEN_ERROR;
}
case 99:
{   
    $this->value = HTML_Template_Flexy_Token::factory('Number',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 100:
{ 
    $this->value = HTML_Template_Flexy_Token::factory('Name',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 101:
{ 
    $this->value = HTML_Template_Flexy_Token::factory('NameT',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 102:
{ 
    $this->value = HTML_Template_Flexy_Token::factory('NumberT',$this->yytext(),$this->yyline);    
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 103:
{
    // <!doctype ^%foo;> -- parameter entity reference */
    $this->value = HTML_Template_Flexy_Token::factory('EntityRef',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 104:
{ 
    $this->value = HTML_Template_Flexy_Token::factory('Literal',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 105:
{
    // inside a comment (not - or not --
    // <!^--...-->   -- comment */   
    //$this->value = HTML_Template_Flexy_Token::factory('Comment',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 106:
{
	// inside comment -- without a >
	return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 107:
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
case 108:
{
    $t = $this->yytext();
    // add argument
    $this->flexyArgs[] = $t;
    $this->yybegin(IN_FLEXYMETHODQUOTED_END);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 109:
{ 
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 110:
{
    // inside a comment (not - or not --
    // <!^--...-->   -- comment */   
    $this->value = HTML_Template_Flexy_Token::factory('DSComment',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 112:
{
    return $this->raiseError("unexpected something: (".$this->yytext() .") character: 0x" . dechex(ord($this->yytext())));
}
case 113:
{
    //abcd -- data characters  
    // { added for flexy
    $this->value = HTML_Template_Flexy_Token::factory('Text',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 114:
{
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 115:
{
    // <a name = ^12pt> -- number token */
    $this->attributes[$this->attrKey] = trim($this->yytext());
    $this->yybegin(IN_ATTR);
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 116:
{
    //echo "GOT DATA:".$this->yytext();
    $this->attrVal[] = $this->yytext();
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 117:
{
    return $this->raiseError("illegal character in markup declaration");
    return HTML_TEMPLATE_FLEXY_TOKEN_ERROR;
}
case 118:
{ 
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 120:
{
    return $this->raiseError("unexpected something: (".$this->yytext() .") character: 0x" . dechex(ord($this->yytext())));
}
case 121:
{
    return $this->raiseError("illegal character in markup declaration");
    return HTML_TEMPLATE_FLEXY_TOKEN_ERROR;
}
case 123:
{
    return $this->raiseError("unexpected something: (".$this->yytext() .") character: 0x" . dechex(ord($this->yytext())));
}
case 124:
{
    return $this->raiseError("illegal character in markup declaration");
    return HTML_TEMPLATE_FLEXY_TOKEN_ERROR;
}
case 126:
{
    return $this->raiseError("unexpected something: (".$this->yytext() .") character: 0x" . dechex(ord($this->yytext())));
}
case 128:
{
    return $this->raiseError("unexpected something: (".$this->yytext() .") character: 0x" . dechex(ord($this->yytext())));
}
case 130:
{
    return $this->raiseError("unexpected something: (".$this->yytext() .") character: 0x" . dechex(ord($this->yytext())));
}
case 132:
{
    return $this->raiseError("unexpected something: (".$this->yytext() .") character: 0x" . dechex(ord($this->yytext())));
}
case 134:
{
    return $this->raiseError("unexpected something: (".$this->yytext() .") character: 0x" . dechex(ord($this->yytext())));
}
case 136:
{
    return $this->raiseError("unexpected something: (".$this->yytext() .") character: 0x" . dechex(ord($this->yytext())));
}
case 274:
{
    //abcd -- data characters  
    // { added for flexy
    $this->value = HTML_Template_Flexy_Token::factory('Text',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 275:
{
    // <a name = ^12pt> -- number token */
    $this->attributes[$this->attrKey] = trim($this->yytext());
    $this->yybegin(IN_ATTR);
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 276:
{
    $t = $this->yytext();
    // add argument
    $this->flexyArgs[] = $t;
    $this->yybegin(IN_FLEXYMETHODQUOTED_END);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 277:
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
