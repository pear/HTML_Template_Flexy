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
        192,
        30,
        109,
        195,
        196,
        197,
        198,
        50,
        61,
        229,
        231,
        250,
        265,
        266,
        274
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
        /* 78 */   YY_NOT_ACCEPT,
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
        /* 101 */   YY_NO_ANCHOR,
        /* 102 */   YY_NO_ANCHOR,
        /* 103 */   YY_NO_ANCHOR,
        /* 104 */   YY_NO_ANCHOR,
        /* 105 */   YY_NO_ANCHOR,
        /* 106 */   YY_NOT_ACCEPT,
        /* 107 */   YY_NO_ANCHOR,
        /* 108 */   YY_NO_ANCHOR,
        /* 109 */   YY_NO_ANCHOR,
        /* 110 */   YY_NO_ANCHOR,
        /* 111 */   YY_NO_ANCHOR,
        /* 112 */   YY_NO_ANCHOR,
        /* 113 */   YY_NO_ANCHOR,
        /* 114 */   YY_NOT_ACCEPT,
        /* 115 */   YY_NO_ANCHOR,
        /* 116 */   YY_NO_ANCHOR,
        /* 117 */   YY_NOT_ACCEPT,
        /* 118 */   YY_NO_ANCHOR,
        /* 119 */   YY_NO_ANCHOR,
        /* 120 */   YY_NOT_ACCEPT,
        /* 121 */   YY_NO_ANCHOR,
        /* 122 */   YY_NOT_ACCEPT,
        /* 123 */   YY_NO_ANCHOR,
        /* 124 */   YY_NOT_ACCEPT,
        /* 125 */   YY_NO_ANCHOR,
        /* 126 */   YY_NOT_ACCEPT,
        /* 127 */   YY_NO_ANCHOR,
        /* 128 */   YY_NOT_ACCEPT,
        /* 129 */   YY_NO_ANCHOR,
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
        /* 276 */   YY_NO_ANCHOR,
        /* 277 */   YY_NO_ANCHOR,
        /* 278 */   YY_NO_ANCHOR,
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
        /* 336 */   YY_NOT_ACCEPT
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
        1, 24, 25, 26, 27, 28, 1, 29,
        30, 1, 31, 1, 1, 32, 1, 1,
        1, 33, 34, 1, 35, 1, 36, 37,
        38, 1, 39, 1, 1, 40, 41, 42,
        43, 16, 44, 45, 46, 47, 48, 49,
        50, 51, 52, 53, 54, 1, 55, 1,
        56, 57, 58, 59, 60, 61, 62, 63,
        64, 65, 66, 1, 67, 68, 69, 70,
        71, 72, 73, 74, 75, 76, 77, 78,
        79, 80, 81, 82, 83, 84, 85, 86,
        87, 88, 89, 90, 91, 92, 93, 94,
        95, 96, 97, 98, 99, 100, 101, 102,
        103, 104, 105, 106, 107, 108, 109, 110,
        111, 112, 113, 114, 115, 116, 117, 118,
        119, 120, 121, 122, 123, 124, 125, 126,
        127, 128, 129, 130, 131, 132, 133, 134,
        135, 136, 137, 138, 139, 140, 141, 142,
        143, 59, 14, 144, 145, 146, 147, 69,
        148, 149, 150, 151, 152, 153, 154, 155,
        156, 157, 158, 159, 160, 161, 162, 163,
        164, 165, 166, 167, 168, 169, 170, 171,
        172, 64, 67, 173, 174, 175, 176, 177,
        72, 74, 178, 179, 180, 181, 182, 183,
        184, 185, 186, 187, 188, 189, 190, 191,
        192, 193, 194, 78, 195, 196, 197, 198,
        199, 200, 201, 202, 203, 204, 205, 206,
        207, 208, 209, 210, 211, 212, 213, 214,
        215, 216, 217, 218, 219, 62, 220, 221,
        222, 94, 223, 224, 225, 226, 227, 228,
        229, 47, 105, 230, 231, 232, 113, 233,
        234, 235, 236, 128, 237, 133, 238, 151,
        239, 157, 240, 165, 241, 179, 242, 185,
        243, 196, 244, 202, 245, 246, 247, 248,
        235, 249, 250, 251, 252, 253, 254, 255,
        256, 257, 258, 259, 260, 261, 262, 263,
        264 
        );


    var $yy_nxt = array(
        array( 1, 2, 3, 3, 3, 3, 3, 3,
            79, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 80, 276, 3,
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
        array( -1, -1, 78, 3, 3, 3, 4, 3,
            -1, 3, 3, 3, 3, 3, 3, 3,
            3, 4, 4, 4, 4, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 4,
            4, 4, 4, 4, 4, 4, 4, 4,
            4, 3, 3, 4, 4, 3, 3, 4,
            4, 4, 4, 3, 4, 4, 3, 3 ),
        array( -1, 106, 3, 3, 3, 3, 3, 3,
            114, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, -1, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 3 ),
        array( -1, -1, -1, 4, 81, 81, 4, 4,
            -1, -1, -1, -1, -1, -1, -1, 4,
            -1, 4, 4, 4, 4, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 4,
            4, 4, 4, 4, 4, 4, 4, 4,
            4, -1, 4, 4, 4, -1, -1, 4,
            4, 4, 4, -1, 4, 4, 4, -1 ),
        array( -1, -1, -1, 5, -1, 82, 5, 5,
            -1, -1, 5, 82, 82, -1, -1, 5,
            -1, 5, 5, 5, 5, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 5,
            5, 5, 5, 5, 5, 5, 5, 5,
            5, -1, 5, 5, 5, -1, -1, 5,
            5, 5, 5, -1, 5, 5, 5, -1 ),
        array( -1, -1, -1, 7, 83, 83, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 7, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 7, -1 ),
        array( -1, -1, -1, 8, 84, 84, 8, 8,
            -1, -1, -1, -1, -1, -1, -1, 8,
            -1, 8, 8, 8, 8, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 8,
            8, 8, 8, 8, 8, 8, 8, 8,
            8, -1, 8, 8, 8, -1, -1, 8,
            8, 8, 8, -1, 8, 8, 8, -1 ),
        array( -1, -1, -1, 9, -1, 85, 9, 9,
            -1, 128, 9, 85, 85, -1, -1, 9,
            -1, 9, 9, 9, 9, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 9,
            9, 9, 9, 9, 9, 9, 9, 9,
            9, -1, 9, 9, 9, -1, -1, 9,
            9, 9, 9, -1, 9, 9, 9, -1 ),
        array( -1, -1, -1, 11, -1, 86, 11, 11,
            -1, -1, -1, 86, 86, -1, -1, 11,
            -1, 11, 11, 11, 11, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 11,
            11, 11, 11, 11, 11, 11, 11, 11,
            11, -1, 11, 11, 11, -1, -1, 11,
            11, 11, 11, -1, 11, 11, 11, -1 ),
        array( -1, -1, -1, -1, -1, 87, -1, -1,
            -1, -1, -1, 87, 87, -1, -1, -1,
            -1, 135, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, 28, 28, 28, 28, 28, 28, 28,
            28, 28, 28, 28, 28, 28, 28, 28,
            28, 28, 28, 28, 28, 28, 28, 28,
            28, 193, 28, -1, 28, 28, 28, 28,
            28, 28, 28, 28, 28, 28, 28, 28,
            28, 28, 28, 28, 28, 28, 28, 28,
            28, 28, 28, 28, 28, 28, 28, 28 ),
        array( 1, 115, 115, 115, 115, 88, 115, 115,
            31, 115, 115, 88, 88, 32, 115, 115,
            115, 115, 115, 115, 115, 115, 115, 115,
            115, 115, 115, 115, 115, 115, 115, 115,
            115, 115, 115, 115, 115, 115, 115, 115,
            115, 115, 115, 115, 115, 115, 115, 115,
            115, 115, 115, 115, 115, 115, 115, 115 ),
        array( -1, -1, -1, 33, -1, 90, 33, 33,
            -1, -1, 33, 90, 90, -1, -1, 33,
            -1, 33, 33, 33, 33, -1, -1, -1,
            -1, -1, 35, -1, -1, -1, -1, 33,
            33, 33, 33, 33, 33, 33, 33, 33,
            33, -1, 33, 33, 33, -1, -1, 33,
            33, 33, 33, -1, 33, 33, 33, -1 ),
        array( -1, -1, -1, -1, -1, 194, -1, -1,
            -1, -1, -1, 194, 194, 36, -1, -1,
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
        array( -1, 37, 37, 37, 37, 91, 37, 37,
            37, 37, 37, 91, 91, -1, 37, 37,
            37, 37, 37, 37, 37, 37, 37, 37,
            37, 37, 37, -1, -1, 37, 37, 37,
            37, 37, 37, 37, 37, 37, 37, 37,
            37, 37, 37, 37, 37, 37, 37, 37,
            37, 37, 37, 37, 37, 37, 37, 37 ),
        array( -1, 37, 37, 38, 37, 92, 38, 38,
            37, 37, 37, 92, 92, -1, 37, 38,
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
            -1, 199, 46, 46, -1, 46, 46, 46,
            46, 46, 46, 46, 46, 46, 46, 46,
            46, 46, 46, 46, 46, 46, 46, 46,
            46, 46, 46, 46, 46, 46, 46, 46 ),
        array( 1, 51, 51, 52, 51, 94, 53, 54,
            51, 51, 51, 94, 94, 55, 51, 54,
            56, 53, 53, 53, 53, 51, 51, 51,
            95, 51, 51, 112, 116, 51, 51, 53,
            53, 53, 53, 53, 53, 53, 53, 53,
            53, 51, 52, 53, 53, 51, 51, 53,
            53, 53, 53, 51, 53, 53, 52, 51 ),
        array( -1, -1, -1, 52, -1, 96, 57, 57,
            -1, -1, -1, 96, 96, -1, -1, 57,
            -1, 57, 57, 57, 57, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 57,
            57, 57, 57, 57, 57, 57, 57, 57,
            57, -1, 52, 57, 57, -1, -1, 57,
            57, 57, 57, -1, 57, 57, 52, -1 ),
        array( -1, -1, -1, 53, -1, 97, 53, 53,
            -1, -1, -1, 97, 97, -1, -1, 53,
            -1, 53, 53, 53, 53, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 53,
            53, 53, 53, 53, 53, 53, 53, 53,
            53, -1, 53, 53, 53, -1, -1, 53,
            53, 53, 53, -1, 53, 53, 53, -1 ),
        array( -1, -1, -1, 54, -1, 98, 54, 54,
            -1, -1, -1, 98, 98, -1, -1, 54,
            -1, 54, 54, 54, 54, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 54,
            54, 54, 54, 54, 54, 54, 54, 54,
            54, -1, 54, 54, 54, -1, -1, 54,
            54, 54, 54, -1, 54, 54, 54, -1 ),
        array( -1, -1, -1, 57, -1, 99, 57, 57,
            -1, -1, -1, 99, 99, -1, -1, 57,
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
        array( -1, -1, -1, 59, 100, 100, 59, 59,
            -1, -1, -1, 100, 100, -1, -1, 59,
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
        array( 1, 102, 102, 102, 102, 102, 102, 102,
            102, 102, 102, 102, 102, 102, 102, 119,
            102, 102, 102, 102, 102, 102, 102, 102,
            102, 102, 102, 102, 102, 102, 102, 102,
            102, 102, 102, 102, 102, 102, 102, 102,
            102, 102, 102, 102, 102, 102, 102, 102,
            102, 102, 102, 102, 102, 102, 102, 102 ),
        array( -1, 63, 63, 63, 63, 63, 63, 63,
            63, 63, 63, 63, 63, 63, 63, 63,
            63, 63, 63, 63, 63, 63, -1, 63,
            63, 63, 63, 63, 63, 63, 63, 63,
            63, 63, 63, 63, 63, 63, 63, 63,
            63, 63, 63, 63, 63, 63, 63, 63,
            63, 63, 63, 63, 63, 63, 63, 63 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 230, -1,
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
            -1, -1, -1, 103, -1, -1, -1, -1 ),
        array( -1, -1, -1, 69, -1, -1, 69, 252,
            -1, -1, -1, -1, -1, -1, -1, -1,
            253, 69, 69, 69, 69, -1, -1, -1,
            331, -1, -1, -1, -1, -1, -1, 69,
            69, 69, 69, 69, 69, 69, 69, 69,
            69, 69, 69, 69, 69, -1, -1, 69,
            69, 69, 69, -1, 69, 69, 69, -1 ),
        array( -1, 73, 73, 73, 73, 73, 73, 73,
            -1, 73, 73, 73, 73, 73, 73, 73,
            73, 73, 73, 73, 73, 73, 73, 73,
            73, 73, 73, 73, 73, 73, 73, 73,
            73, 73, 73, 73, 73, 73, 73, 73,
            73, 73, 73, 73, 73, 73, 73, 73,
            73, 73, 73, 73, 73, 73, 73, 73 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 267, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, 76, 76, 76, 76, 76, 76, 76,
            76, 76, 76, 76, 76, 76, 76, 76,
            76, 76, 76, 76, 76, 76, -1, 76,
            76, 76, 76, 76, 76, 76, 76, 76,
            76, 76, 76, 76, 76, 76, 76, 76,
            76, 76, 76, 76, 76, 76, 76, 76,
            76, 76, 76, 76, 76, 76, 76, 76 ),
        array( -1, -1, -1, 7, -1, -1, 8, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 8, 8, 8, 8, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 8,
            8, 8, 8, 8, 8, 8, 8, 8,
            8, -1, 7, 8, 8, -1, -1, 8,
            8, 8, 8, -1, 8, 8, 7, -1 ),
        array( -1, -1, -1, -1, -1, 3, 5, -1,
            -1, 117, -1, 3, 3, 6, 120, -1,
            3, 5, 5, 5, 5, 3, 3, 122,
            -1, 3, -1, -1, -1, 3, -1, 5,
            5, 5, 5, 5, 5, 5, 5, 5,
            5, 3, -1, 5, 5, 3, -1, 5,
            5, 5, 5, -1, 5, 5, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 124, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 124, 124, 124, 124, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 124,
            124, 124, 124, 124, 124, 279, 124, 124,
            336, -1, -1, 124, 124, -1, -1, 124,
            317, 124, 124, -1, 124, 124, -1, -1 ),
        array( -1, -1, -1, -1, -1, 82, -1, -1,
            -1, -1, -1, 82, 82, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 85, -1, -1,
            -1, 128, -1, 85, 85, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 86, -1, -1,
            -1, -1, -1, 86, 86, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 87, -1, -1,
            -1, -1, -1, 87, 87, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 88, -1, -1,
            -1, -1, -1, 88, 88, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 90, -1, -1,
            -1, -1, -1, 90, 90, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 35, -1, -1, -1, -1, -1,
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
        array( -1, -1, -1, -1, -1, 92, -1, -1,
            -1, -1, -1, 92, 92, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 200, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 200, 200, 200, 200, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 200,
            200, 200, 200, 200, 200, 200, 200, 200,
            200, -1, -1, 200, 200, -1, -1, 200,
            200, 200, 200, -1, 200, 200, -1, -1 ),
        array( -1, -1, -1, -1, -1, 94, -1, -1,
            -1, -1, -1, 94, 94, -1, -1, -1,
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
        array( -1, -1, -1, -1, -1, 96, -1, -1,
            -1, -1, -1, 96, 96, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 97, -1, -1,
            -1, -1, -1, 97, 97, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 98, -1, -1,
            -1, -1, -1, 98, 98, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
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
        array( -1, 102, 102, 102, 102, 102, 102, 102,
            102, 102, 102, 102, 102, 102, 102, 227,
            102, 102, 102, 102, 102, 102, 102, 102,
            102, 102, 102, 102, 102, 102, 102, 102,
            102, 102, 102, 102, 102, 102, 102, 102,
            102, 102, 102, 102, 102, 102, 102, 102,
            102, 102, 102, 102, 102, 102, 102, 102 ),
        array( -1, -1, -1, -1, -1, -1, -1, 252,
            -1, -1, -1, -1, -1, -1, -1, -1,
            253, -1, -1, -1, -1, -1, -1, -1,
            331, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 77, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
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
        array( -1, 106, 3, 3, 3, 3, 3, 3,
            114, 3, 3, 3, 3, 17, 3, 3,
            3, 3, 3, 3, 3, -1, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 3 ),
        array( 1, 115, 115, 115, 115, 88, 33, 115,
            31, 34, 115, 88, 88, 32, 115, 115,
            115, 33, 33, 33, 33, 115, 115, 115,
            115, 115, 115, 115, 115, 115, 115, 33,
            33, 33, 33, 33, 33, 33, 33, 33,
            33, 115, 115, 33, 33, 115, 115, 33,
            33, 33, 33, 115, 33, 33, 115, 115 ),
        array( -1, 37, 37, 110, 37, 92, 110, 110,
            37, 37, 37, 92, 92, -1, 37, 110,
            37, 110, 110, 110, 110, 37, 37, 37,
            37, 37, 37, -1, -1, 37, 37, 110,
            110, 110, 110, 110, 110, 110, 110, 110,
            110, 37, 110, 110, 110, 37, 37, 110,
            110, 110, 110, 37, 110, 110, 110, 37 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 201, -1 ),
        array( -1, 225, 225, 225, 225, 225, 225, 225,
            225, 225, 225, 225, 225, 225, 225, 225,
            225, 225, 225, 225, 225, 225, 225, 225,
            225, 225, 225, 60, 225, 225, 225, 225,
            225, 225, 225, 225, 225, 225, 225, 225,
            225, 225, 225, 225, 225, 225, 225, 225,
            225, 225, 225, 225, 225, 225, 225, 225 ),
        array( -1, -1, -1, -1, -1, -1, -1, 252,
            -1, -1, -1, -1, -1, -1, -1, -1,
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
        array( -1, 226, 226, 226, 226, 226, 226, 226,
            226, 226, 226, 226, 226, 226, 226, 226,
            226, 226, 226, 226, 226, 226, 226, 226,
            226, 226, 226, 226, 101, 226, 226, 226,
            226, 226, 226, 226, 226, 226, 226, 226,
            226, 226, 226, 226, 226, 226, 226, 226,
            226, 226, 226, 226, 226, 226, 226, 226 ),
        array( -1, -1, -1, -1, -1, 126, 9, -1,
            -1, 128, -1, 126, 126, 10, -1, -1,
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
        array( -1, 102, 102, 102, 102, 102, 102, 102,
            102, 102, 102, 102, 102, 102, 102, 228,
            102, 102, 102, 102, 102, 102, 102, 102,
            102, 102, 102, 102, 102, 102, 102, 102,
            102, 102, 102, 102, 102, 102, 102, 102,
            102, 102, 102, 102, 102, 102, 102, 102,
            102, 102, 102, 102, 102, 102, 102, 102 ),
        array( -1, -1, -1, -1, -1, -1, 11, -1,
            -1, -1, -1, -1, -1, 12, -1, 130,
            13, 11, 11, 11, 11, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 11,
            11, 11, 11, 11, 11, 11, 11, 11,
            11, -1, -1, 11, 11, -1, -1, 11,
            11, 11, 11, -1, 11, 11, -1, -1 ),
        array( -1, 232, 66, 232, 232, 232, 232, 232,
            232, 232, 232, 232, 232, 232, 232, 232,
            232, 232, 232, 232, 232, 232, 232, 232,
            232, 232, 232, 232, 232, 232, 232, 232,
            232, 232, 232, 232, 232, 232, 232, 232,
            232, 232, 232, 232, 232, 232, 232, 232,
            232, 232, 232, 232, 232, 232, 232, 232 ),
        array( -1, 122, 122, 122, 122, 122, 122, 122,
            122, 122, 122, 122, 122, 14, 122, 122,
            122, 122, 122, 122, 122, 122, 122, 122,
            122, 122, 122, 122, 122, 122, 122, 122,
            122, 122, 122, 122, 122, 122, 122, 122,
            122, 122, 122, 122, 122, 122, 122, 122,
            122, 122, 122, 122, 122, 122, 122, 122 ),
        array( -1, -1, -1, 233, -1, -1, 233, 234,
            -1, -1, -1, -1, -1, -1, -1, -1,
            235, 233, 233, 233, 233, -1, -1, -1,
            329, -1, -1, -1, -1, -1, -1, 233,
            233, 233, 233, 233, 233, 233, 233, 233,
            233, 233, 233, 233, 233, -1, -1, 233,
            233, 233, 233, 67, 233, 233, 233, 236 ),
        array( -1, -1, -1, 124, -1, -1, 124, 131,
            -1, -1, 280, -1, -1, -1, -1, -1,
            132, 124, 124, 124, 124, -1, -1, -1,
            133, -1, -1, -1, -1, -1, -1, 124,
            124, 124, 124, 124, 124, 124, 124, 124,
            124, 124, 124, 124, 124, 15, 16, 124,
            124, 124, 124, -1, 124, 124, 124, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 287, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 68, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 126, -1, -1,
            -1, 128, -1, 126, 126, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, 251, 70, 251, 251, 251, 251, 251,
            251, 251, 251, 251, 251, 251, 251, 251,
            251, 251, 251, 251, 251, 251, 251, 251,
            251, 251, 251, 251, 251, 251, 251, 251,
            251, 251, 251, 251, 251, 251, 251, 251,
            251, 251, 251, 251, 251, 251, 251, 251,
            251, 251, 251, 251, 251, 251, 251, 251 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            18, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 254, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            255, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 71, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 19,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 136, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 136, 136, 136, 136, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 136,
            136, 136, 136, 136, 136, 136, 136, 136,
            136, -1, -1, 136, 136, -1, -1, 136,
            136, 136, 136, -1, 136, 136, -1, -1 ),
        array( -1, -1, -1, 138, -1, -1, 138, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 138, 138, 138, 138, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 138,
            138, 138, 138, 138, 138, 138, 138, 138,
            138, 138, 138, 138, 138, -1, -1, 138,
            138, 138, 138, -1, 138, 138, 138, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 139, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 124, -1, -1, 124, 131,
            -1, -1, 140, -1, -1, -1, -1, -1,
            132, 124, 124, 124, 124, -1, -1, -1,
            133, -1, -1, -1, -1, -1, -1, 124,
            124, 124, 124, 124, 124, 124, 124, 124,
            124, 124, 124, 124, 124, 15, 16, 124,
            124, 124, 124, -1, 124, 124, 124, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 142, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 136, -1, -1, 136, 131,
            -1, -1, 280, -1, -1, -1, -1, -1,
            143, 136, 136, 136, 136, -1, -1, -1,
            144, -1, -1, -1, -1, -1, -1, 136,
            136, 136, 136, 136, 136, 136, 136, 136,
            136, 136, 136, 136, 136, 15, 16, 136,
            136, 136, 136, -1, 136, 136, 136, -1 ),
        array( -1, -1, -1, -1, -1, -1, 137, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 137, 137, 137, 137, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 137,
            137, 137, 137, 137, 137, 137, 137, 137,
            137, -1, -1, 137, 137, 15, -1, 137,
            137, 137, 137, -1, 137, 137, -1, -1 ),
        array( -1, -1, -1, 138, -1, -1, 138, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 138, 138, 138, 138, -1, 145, -1,
            146, -1, -1, -1, -1, -1, -1, 138,
            138, 138, 138, 138, 138, 138, 138, 138,
            138, 138, 138, 138, 138, -1, -1, 138,
            138, 138, 138, -1, 138, 138, 138, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 132, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 147, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 147, 147, 147, 147, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 147,
            147, 147, 147, 147, 147, 147, 147, 147,
            147, -1, -1, 147, 147, -1, -1, 147,
            147, 147, 147, -1, 147, 147, -1, -1 ),
        array( -1, -1, -1, 124, -1, -1, 124, 131,
            -1, -1, 148, -1, -1, -1, -1, -1,
            132, 124, 124, 124, 124, -1, -1, -1,
            133, -1, -1, -1, -1, -1, -1, 124,
            124, 124, 124, 124, 124, 124, 124, 124,
            124, 124, 124, 124, 124, 15, 16, 124,
            124, 124, 124, -1, 124, 124, 124, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 150, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 151, -1, -1, 151, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 151, 151, 151, 151, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 151,
            151, 151, 151, 151, 151, 151, 151, 151,
            151, 151, 151, 151, 151, -1, -1, 151,
            151, 151, 151, -1, 151, 151, 151, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 282, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, 131,
            -1, -1, 280, -1, -1, -1, -1, -1,
            132, -1, -1, -1, -1, -1, -1, -1,
            133, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 15, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 152, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 147, -1, -1, 147, 153,
            -1, -1, -1, -1, -1, -1, -1, -1,
            154, 147, 147, 147, 147, -1, -1, -1,
            319, -1, -1, -1, -1, -1, -1, 147,
            147, 147, 147, 147, 147, 147, 147, 147,
            147, 147, 147, 147, 147, 20, 21, 147,
            147, 147, 147, -1, 147, 147, 147, -1 ),
        array( -1, -1, -1, -1, -1, -1, 137, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 137, 137, 137, 137, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 137,
            137, 137, 137, 137, 137, 137, 137, 137,
            137, -1, -1, 137, 137, 22, -1, 137,
            137, 137, 137, -1, 137, 137, -1, -1 ),
        array( -1, -1, -1, 124, -1, -1, 124, 131,
            -1, -1, 155, -1, -1, -1, -1, -1,
            132, 124, 124, 124, 124, -1, -1, -1,
            133, -1, -1, -1, -1, -1, -1, 124,
            124, 124, 124, 124, 124, 124, 124, 124,
            124, 124, 124, 124, 124, 15, 16, 124,
            124, 124, 124, -1, 124, 124, 124, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 156, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 151, -1, -1, 151, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 151, 151, 151, 151, -1, 157, -1,
            158, -1, -1, -1, -1, -1, -1, 151,
            151, 151, 151, 151, 151, 151, 151, 151,
            151, 151, 151, 151, 151, -1, -1, 151,
            151, 151, 151, -1, 151, 151, 151, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 145, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 145, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 159, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 159, 159, 159, 159, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 159,
            159, 159, 159, 159, 159, 159, 159, 159,
            159, -1, -1, 159, 159, -1, -1, 159,
            159, 159, 159, -1, 159, 159, -1, -1 ),
        array( -1, -1, -1, 160, -1, -1, 160, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 160, 160, 160, 160, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 160,
            160, 160, 160, 160, 160, 160, 160, 160,
            160, 160, 160, 160, 160, -1, -1, 160,
            160, 160, 160, -1, 160, 160, 160, -1 ),
        array( -1, -1, -1, -1, -1, -1, 137, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 137, 137, 137, 137, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 137,
            137, 137, 137, 137, 137, 137, 137, 137,
            137, -1, -1, 137, 137, 23, -1, 137,
            137, 137, 137, -1, 137, 137, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 161, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, 131,
            -1, -1, 280, -1, -1, -1, -1, -1,
            162, -1, -1, -1, -1, -1, -1, -1,
            323, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 15, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 163, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 159, -1, -1, 159, 153,
            -1, -1, -1, -1, -1, -1, -1, -1,
            320, 159, 159, 159, 159, -1, -1, -1,
            326, -1, -1, -1, -1, -1, -1, 159,
            159, 159, 159, 159, 159, 159, 159, 159,
            159, 159, 159, 159, 159, 20, 21, 159,
            159, 159, 159, -1, 159, 159, 159, -1 ),
        array( -1, -1, -1, 160, -1, -1, 160, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 160, 160, 160, 160, -1, 164, -1,
            165, -1, -1, -1, -1, -1, -1, 160,
            160, 160, 160, 160, 160, 160, 160, 160,
            160, 160, 160, 160, 160, -1, -1, 160,
            160, 160, 160, -1, 160, 160, 160, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            24, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 167, -1, -1, 167, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 167, 167, 167, 167, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 167,
            167, 167, 167, 167, 167, 167, 167, 167,
            167, 167, 167, 167, 167, -1, -1, 167,
            167, 167, 167, -1, 167, 167, 167, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 157, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 157, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, 153,
            -1, -1, -1, -1, -1, -1, -1, -1,
            154, -1, -1, -1, -1, -1, -1, -1,
            319, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 20, 21, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 168, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 124, -1, -1, 124, 131,
            -1, -1, 169, -1, -1, -1, -1, -1,
            132, 124, 124, 124, 124, -1, -1, -1,
            133, -1, -1, -1, -1, -1, -1, 124,
            124, 124, 124, 124, 124, 124, 124, 124,
            124, 124, 124, 124, 124, 15, 16, 124,
            124, 124, 124, -1, 124, 124, 124, -1 ),
        array( -1, -1, -1, 167, -1, -1, 167, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 167, 167, 167, 167, -1, 170, -1,
            171, -1, -1, -1, -1, -1, -1, 167,
            167, 167, 167, 167, 167, 167, 167, 167,
            167, 167, 167, 167, 167, -1, -1, 167,
            167, 167, 167, -1, 167, 167, 167, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 164, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 164, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 173, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 173, 173, 173, 173, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 173,
            173, 173, 173, 173, 173, 173, 173, 173,
            173, -1, -1, 173, 173, -1, -1, 173,
            173, 173, 173, -1, 173, 173, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, 174,
            -1, -1, -1, -1, -1, -1, -1, -1,
            162, -1, -1, -1, -1, -1, -1, -1,
            323, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 175, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, 153,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 20, 21, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 173, -1, -1, 173, 285,
            -1, -1, -1, -1, -1, -1, -1, -1,
            177, 173, 173, 173, 173, -1, -1, -1,
            327, -1, -1, -1, -1, -1, -1, 173,
            173, 173, 173, 173, 173, 173, 173, 173,
            173, 173, 173, 173, 173, 25, -1, 173,
            173, 173, 173, 292, 173, 173, 173, -1 ),
        array( -1, -1, -1, -1, -1, -1, 178, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 178, 178, 178, 178, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 178,
            178, 178, 178, 178, 178, 178, 178, 178,
            178, -1, -1, 178, 178, -1, -1, 178,
            178, 178, 178, -1, 178, 178, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 170, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 170, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 172, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 172, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 180, -1, -1, 180, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 180, 180, 180, 180, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 180,
            180, 180, 180, 180, 180, 180, 180, 180,
            180, 180, 180, 180, 180, -1, -1, 180,
            180, 180, 180, -1, 180, 180, 180, -1 ),
        array( -1, -1, -1, 178, -1, -1, 178, 174,
            -1, -1, -1, -1, -1, -1, -1, -1,
            162, 178, 178, 178, 178, -1, -1, -1,
            323, -1, -1, -1, -1, -1, -1, 178,
            178, 178, 178, 178, 178, 178, 178, 178,
            178, 178, 178, 178, 178, -1, 16, 178,
            178, 178, 178, -1, 178, 178, 178, -1 ),
        array( -1, -1, -1, 179, -1, -1, 179, 285,
            -1, -1, -1, -1, -1, -1, -1, -1,
            182, 179, 179, 179, 179, -1, -1, -1,
            328, -1, -1, -1, -1, -1, -1, 179,
            179, 179, 179, 179, 179, 179, 179, 179,
            179, 179, 179, 179, 179, 25, -1, 179,
            179, 179, 179, 292, 179, 179, 179, -1 ),
        array( -1, -1, -1, 180, -1, -1, 180, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 180, 180, 180, 180, -1, 183, -1,
            184, -1, -1, -1, -1, -1, -1, 180,
            180, 180, 180, 180, 180, 180, 180, 180,
            180, 180, 180, 180, 180, -1, -1, 180,
            180, 180, 180, -1, 180, 180, 180, -1 ),
        array( -1, -1, -1, 181, -1, -1, 181, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 181, 181, 181, 181, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 181,
            181, 181, 181, 181, 181, 181, 181, 181,
            181, 181, 181, 181, 181, 26, -1, 181,
            181, 181, 181, 185, 181, 181, 181, -1 ),
        array( -1, -1, -1, 186, -1, -1, 186, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 186, 186, 186, 186, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 186,
            186, 186, 186, 186, 186, 186, 186, 186,
            186, 186, 186, 186, 186, -1, -1, 186,
            186, 186, 186, -1, 186, 186, 186, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, 285,
            -1, -1, -1, -1, -1, -1, -1, -1,
            177, -1, -1, -1, -1, -1, -1, -1,
            327, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 25, -1, -1,
            -1, -1, -1, 292, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 187, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 188, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 188, 188, 188, 188, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 188,
            188, 188, 188, 188, 188, 188, 188, 188,
            188, -1, -1, 188, 188, -1, -1, 188,
            188, 188, 188, -1, 188, 188, -1, -1 ),
        array( -1, -1, -1, 186, -1, -1, 186, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 186, 186, 186, 186, -1, 189, -1,
            190, -1, -1, -1, -1, -1, -1, 186,
            186, 186, 186, 186, 186, 186, 186, 186,
            186, 186, 186, 186, 186, -1, -1, 186,
            186, 186, 186, -1, 186, 186, 186, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 183, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 183, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 188, -1, -1, 188, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 188, 188, 188, 188, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 188,
            188, 188, 188, 188, 188, 188, 188, 188,
            188, 188, 188, 188, 188, 27, -1, 188,
            188, 188, 188, -1, 188, 188, 188, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, 285,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 25, -1, -1,
            -1, -1, -1, 292, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 191, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 189, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 189, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( 1, 28, 28, 28, 28, 28, 28, 28,
            28, 28, 28, 28, 28, 28, 28, 28,
            28, 28, 28, 28, 28, 28, 28, 28,
            28, 107, 28, 29, 28, 28, 28, 28,
            28, 28, 28, 28, 28, 28, 28, 28,
            28, 28, 28, 28, 28, 28, 28, 28,
            28, 28, 28, 28, 28, 28, 28, 28 ),
        array( 1, 37, 37, 38, 37, -1, 277, 277,
            89, 39, 37, 115, -1, 40, 37, 277,
            37, 277, 277, 277, 277, 37, 37, 37,
            37, 37, 37, 41, 42, 37, 37, 277,
            277, 277, 277, 277, 277, 277, 277, 277,
            277, 37, 38, 277, 277, 37, 37, 277,
            277, 277, 277, 37, 277, 277, 38, 37 ),
        array( 1, 115, 115, 115, 115, 43, 115, 115,
            115, 115, 115, 43, 43, 115, 115, 115,
            115, 115, 115, 115, 115, 115, 115, 115,
            115, 115, 115, 115, 115, 115, 115, 115,
            115, 115, 115, 115, 115, 115, 115, 115,
            115, 115, 115, 115, 115, 115, 115, 115,
            115, 115, 115, 115, 115, 115, 115, 115 ),
        array( 1, 44, 44, 44, 44, -1, 44, 44,
            44, 44, 44, 44, -1, 45, 44, 44,
            44, 44, 44, 44, 44, 44, 44, 44,
            44, 44, 44, 44, 44, 44, 44, 44,
            44, 44, 44, 44, 44, 44, 44, 44,
            44, 44, 44, 44, 44, 44, 44, 44,
            44, 44, 44, 44, 44, 44, 44, 44 ),
        array( 1, 46, 46, 46, 46, 46, 46, 46,
            46, 46, 46, 46, 46, 46, 46, 46,
            46, 46, 46, 46, 46, 93, 46, 46,
            111, 118, 46, 46, 47, 46, 46, 46,
            46, 46, 46, 46, 46, 46, 46, 46,
            46, 46, 46, 46, 46, 46, 46, 46,
            46, 46, 46, 46, 46, 46, 46, 46 ),
        array( -1, -1, -1, 200, -1, -1, 200, 202,
            -1, -1, 286, -1, -1, -1, -1, -1,
            203, 200, 200, 200, 200, -1, -1, -1,
            204, -1, -1, -1, -1, -1, -1, 200,
            200, 200, 200, 200, 200, 200, 200, 200,
            200, 200, 200, 200, 200, 48, 49, 200,
            200, 200, 200, -1, 200, 200, 200, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 289, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 205, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 205, 205, 205, 205, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 205,
            205, 205, 205, 205, 205, 205, 205, 205,
            205, -1, -1, 205, 205, -1, -1, 205,
            205, 205, 205, -1, 205, 205, -1, -1 ),
        array( -1, -1, -1, 207, -1, -1, 207, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 207, 207, 207, 207, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 207,
            207, 207, 207, 207, 207, 207, 207, 207,
            207, 207, 207, 207, 207, -1, -1, 207,
            207, 207, 207, -1, 207, 207, 207, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 304, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 208, -1 ),
        array( -1, -1, -1, 205, -1, -1, 205, 202,
            -1, -1, 286, -1, -1, -1, -1, -1,
            209, 205, 205, 205, 205, -1, -1, -1,
            321, -1, -1, -1, -1, -1, -1, 205,
            205, 205, 205, 205, 205, 205, 205, 205,
            205, 205, 205, 205, 205, 48, 49, 205,
            205, 205, 205, -1, 205, 205, 205, -1 ),
        array( -1, -1, -1, -1, -1, -1, 206, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 206, 206, 206, 206, -1, -1, -1,
            210, -1, -1, -1, -1, -1, -1, 206,
            206, 206, 206, 206, 206, 206, 206, 206,
            206, -1, -1, 206, 206, 48, -1, 206,
            206, 206, 206, -1, 206, 206, -1, -1 ),
        array( -1, -1, -1, 207, -1, -1, 207, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 207, 207, 207, 207, -1, 211, -1,
            212, -1, -1, -1, -1, -1, -1, 207,
            207, 207, 207, 207, 207, 207, 207, 207,
            207, 207, 207, 207, 207, -1, -1, 207,
            207, 207, 207, -1, 207, 207, 207, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 48, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 48, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 213, -1, -1, 213, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 213, 213, 213, 213, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 213,
            213, 213, 213, 213, 213, 213, 213, 213,
            213, 213, 213, 213, 213, -1, -1, 213,
            213, 213, 213, -1, 213, 213, 213, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 208, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, 202,
            -1, -1, 286, -1, -1, -1, -1, -1,
            203, -1, -1, -1, -1, -1, -1, -1,
            204, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 48, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 214, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 213, -1, -1, 213, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 213, 213, 213, 213, -1, 215, -1,
            216, -1, -1, -1, -1, -1, -1, 213,
            213, 213, 213, 213, 213, 213, 213, 213,
            213, 213, 213, 213, 213, -1, -1, 213,
            213, 213, 213, -1, 213, 213, 213, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 211, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 211, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, 202,
            -1, -1, 286, -1, -1, -1, -1, -1,
            217, -1, -1, -1, -1, -1, -1, -1,
            324, -1, -1, -1, -1, -1, -1, -1,
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
        array( -1, -1, -1, 219, -1, -1, 219, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 219, 219, 219, 219, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 219,
            219, 219, 219, 219, 219, 219, 219, 219,
            219, 219, 219, 219, 219, -1, -1, 219,
            219, 219, 219, -1, 219, 219, 219, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 215, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 215, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 219, -1, -1, 219, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 219, 219, 219, 219, -1, 220, -1,
            221, -1, -1, -1, -1, -1, -1, 219,
            219, 219, 219, 219, 219, 219, 219, 219,
            219, 219, 219, 219, 219, -1, -1, 219,
            219, 219, 219, -1, 219, 219, 219, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, 222,
            -1, -1, -1, -1, -1, -1, -1, -1,
            217, -1, -1, -1, -1, -1, -1, -1,
            318, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 223, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 224, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 224, 224, 224, 224, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 224,
            224, 224, 224, 224, 224, 224, 224, 224,
            224, -1, -1, 224, 224, -1, -1, 224,
            224, 224, 224, -1, 224, 224, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 220, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 220, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 224, -1, -1, 224, 222,
            -1, -1, -1, -1, -1, -1, -1, -1,
            217, 224, 224, 224, 224, -1, -1, -1,
            318, -1, -1, -1, -1, -1, -1, 224,
            224, 224, 224, 224, 224, 224, 224, 224,
            224, 224, 224, 224, 224, -1, 49, 224,
            224, 224, 224, -1, 224, 224, 224, -1 ),
        array( -1, 102, 102, 102, 102, 102, 102, 102,
            102, 102, 102, 102, 102, 102, 102, -1,
            102, 102, 102, 102, 102, 102, 102, 102,
            102, 102, 102, 102, 102, 102, 102, 102,
            102, 102, 102, 102, 102, 102, 102, 102,
            102, 102, 102, 102, 102, 102, 102, 102,
            102, 102, 102, 102, 102, 102, 102, 102 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 62, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( 1, 63, 63, 63, 63, 63, 63, 63,
            63, 63, 63, 63, 63, 63, 63, 63,
            63, 63, 63, 63, 63, 63, 64, 63,
            63, 63, 63, 63, 63, 63, 63, 63,
            63, 63, 63, 63, 63, 63, 63, 63,
            63, 63, 63, 63, 63, 63, 63, 63,
            63, 63, 63, 63, 63, 63, 63, 63 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 65, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( 1, 115, 121, 115, 115, -1, 123, 115,
            115, 115, 115, 115, -1, 115, 115, 115,
            115, 123, 123, 123, 123, 115, 115, 115,
            115, 115, 115, 115, 115, 115, 115, 123,
            123, 123, 123, 123, 123, 123, 123, 123,
            123, 115, 115, 123, 123, 115, 115, 123,
            123, 123, 123, 115, 123, 123, 115, 125 ),
        array( -1, -1, -1, -1, -1, -1, 237, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 237, 237, 237, 237, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 237,
            237, 237, 237, 237, 237, 237, 237, 237,
            237, -1, -1, 237, 237, -1, -1, 237,
            237, 237, 237, -1, 237, 237, -1, -1 ),
        array( -1, -1, -1, 238, -1, -1, 238, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 238, 238, 238, 238, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 238,
            238, 238, 238, 238, 238, 238, 238, 238,
            238, 238, 238, 238, 238, -1, -1, 238,
            238, 238, 238, -1, 238, 238, 238, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 239, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 67, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 237, -1, -1, 237, 234,
            -1, -1, -1, -1, -1, -1, -1, -1,
            241, 237, 237, 237, 237, -1, -1, -1,
            330, -1, -1, -1, -1, -1, -1, 237,
            237, 237, 237, 237, 237, 237, 237, 237,
            237, 237, 237, 237, 237, -1, -1, 237,
            237, 237, 237, 67, 237, 237, 237, 236 ),
        array( -1, -1, -1, 238, -1, -1, 238, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 238, 238, 238, 238, -1, 242, -1,
            243, -1, -1, -1, -1, -1, -1, 238,
            238, 238, 238, 238, 238, 238, 238, 238,
            238, 238, 238, 238, 238, -1, -1, 238,
            238, 238, 238, -1, 238, 238, 238, -1 ),
        array( -1, -1, -1, -1, -1, -1, 244, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 244, 244, 244, 244, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 244,
            244, 244, 244, 244, 244, 244, 244, 244,
            244, -1, -1, 244, 244, -1, -1, 244,
            244, 244, 244, -1, 244, 244, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 240, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 240, 240, 240, 240, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 240,
            240, 240, 240, 240, 240, 240, 240, 240,
            240, -1, -1, 240, 240, 68, -1, 240,
            240, 240, 240, -1, 240, 240, -1, -1 ),
        array( -1, -1, -1, 245, -1, -1, 245, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 245, 245, 245, 245, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 245,
            245, 245, 245, 245, 245, 245, 245, 245,
            245, 245, 245, 245, 245, -1, -1, 245,
            245, 245, 245, -1, 245, 245, 245, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, 234,
            -1, -1, -1, -1, -1, -1, -1, -1,
            235, -1, -1, -1, -1, -1, -1, -1,
            329, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 67, -1, -1, -1, 236 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 246, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 244, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 244, 244, 244, 244, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 244,
            244, 244, 244, 244, 244, 244, 244, 244,
            244, -1, -1, 244, 244, 67, -1, 244,
            244, 244, 244, -1, 244, 244, -1, -1 ),
        array( -1, -1, -1, 245, -1, -1, 245, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 245, 245, 245, 245, -1, 247, -1,
            248, -1, -1, -1, -1, -1, -1, 245,
            245, 245, 245, 245, 245, 245, 245, 245,
            245, 245, 245, 245, 245, -1, -1, 245,
            245, 245, 245, -1, 245, 245, 245, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 242, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 242, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, 234,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 67, -1, -1, -1, 236 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 249, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 247, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 247, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( 1, 115, 127, 115, 115, -1, 69, 115,
            115, 115, 115, 115, -1, 115, 115, 115,
            115, 69, 69, 69, 69, 115, 115, 115,
            115, 115, 115, 115, 115, 115, 115, 69,
            69, 69, 69, 69, 69, 69, 69, 69,
            69, 115, 115, 69, 69, 115, 115, 69,
            69, 69, 69, 115, 69, 69, 115, 129 ),
        array( -1, -1, -1, -1, -1, -1, 278, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 278, 278, 278, 278, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 278,
            278, 278, 278, 278, 278, 278, 278, 278,
            278, -1, -1, 278, 278, -1, -1, 278,
            278, 278, 278, -1, 278, 278, -1, -1 ),
        array( -1, -1, -1, 256, -1, -1, 256, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 256, 256, 256, 256, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 256,
            256, 256, 256, 256, 256, 256, 256, 256,
            256, 256, 256, 256, 256, -1, -1, 256,
            256, 256, 256, -1, 256, 256, 256, -1 ),
        array( -1, -1, -1, -1, -1, -1, 257, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 257, 257, 257, 257, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 257,
            257, 257, 257, 257, 257, 257, 257, 257,
            257, -1, -1, 257, 257, -1, -1, 257,
            257, 257, 257, -1, 257, 257, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 258, -1 ),
        array( -1, -1, -1, 256, -1, -1, 256, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 256, 256, 256, 256, -1, 104, -1,
            260, -1, -1, -1, -1, -1, -1, 256,
            256, 256, 256, 256, 256, 256, 256, 256,
            256, 256, 256, 256, 256, -1, -1, 256,
            256, 256, 256, -1, 256, 256, 256, -1 ),
        array( -1, -1, -1, -1, -1, -1, 257, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 257, 257, 257, 257, -1, -1, -1,
            255, -1, -1, -1, -1, -1, -1, 257,
            257, 257, 257, 257, 257, 257, 257, 257,
            257, -1, -1, 257, 257, 71, -1, 257,
            257, 257, 257, -1, 257, 257, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 71, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 71, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 261, -1, -1, 261, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 261, 261, 261, 261, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 261,
            261, 261, 261, 261, 261, 261, 261, 261,
            261, 261, 261, 261, 261, -1, -1, 261,
            261, 261, 261, -1, 261, 261, 261, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 262, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 261, -1, -1, 261, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 261, 261, 261, 261, -1, 113, -1,
            263, -1, -1, -1, -1, -1, -1, 261,
            261, 261, 261, 261, 261, 261, 261, 261,
            261, 261, 261, 261, 261, -1, -1, 261,
            261, 261, 261, -1, 261, 261, 261, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 104, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 104, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 264, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 113, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 113, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( 1, 115, 115, 115, 115, -1, 115, 115,
            115, 115, 115, 115, -1, 115, 115, 115,
            115, 115, 115, 115, 115, 115, 115, 115,
            115, 115, 115, 115, 115, 115, 115, 115,
            115, 115, 115, 115, 115, 115, 115, 115,
            115, 115, 115, 115, 115, 115, 115, 115,
            115, 115, 115, 72, 115, 115, 115, 129 ),
        array( 1, 73, 73, 73, 73, 73, 73, 73,
            74, 73, 73, 73, 73, 73, 73, 73,
            73, 73, 73, 73, 73, 73, 73, 73,
            73, 73, 73, 73, 73, 73, 73, 73,
            73, 73, 73, 73, 73, 73, 73, 73,
            73, 73, 73, 73, 73, 73, 73, 73,
            73, 73, 73, 73, 73, 73, 73, 73 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 268,
            268, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 269, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 269, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 270, 270, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 271, 271, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 272, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 273, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 273,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 75, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( 1, 76, 76, 76, 76, 76, 76, 76,
            76, 76, 76, 76, 76, 76, 76, 76,
            76, 76, 76, 76, 76, 76, 275, 76,
            76, 76, 76, 76, 76, 76, 76, 76,
            76, 76, 76, 76, 76, 76, 76, 76,
            76, 76, 76, 76, 76, 76, 76, 76,
            76, 76, 76, 76, 76, 76, 76, 76 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 105, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, 106, 3, 3, 3, 3, 3, 3,
            114, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, -1, 108, 3,
            3, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 3 ),
        array( -1, -1, -1, 278, -1, -1, 278, 252,
            -1, -1, -1, -1, -1, -1, -1, -1,
            259, 278, 278, 278, 278, -1, -1, -1,
            332, -1, -1, -1, -1, -1, -1, 278,
            278, 278, 278, 278, 278, 278, 278, 278,
            278, 278, 278, 278, 278, -1, -1, 278,
            278, 278, 278, -1, 278, 278, 278, -1 ),
        array( -1, -1, -1, 124, -1, -1, 124, 131,
            -1, -1, 280, -1, -1, -1, -1, -1,
            132, 124, 124, 124, 124, -1, -1, -1,
            133, -1, -1, -1, -1, -1, -1, 124,
            124, 124, 124, 124, 124, 124, 124, 124,
            134, 124, 124, 124, 124, 15, 16, 124,
            124, 124, 124, -1, 124, 124, 124, -1 ),
        array( -1, -1, -1, -1, -1, -1, 137, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 137, 137, 137, 137, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 137,
            137, 137, 137, 137, 137, 137, 137, 137,
            137, -1, -1, 137, 137, -1, -1, 137,
            137, 137, 137, -1, 137, 137, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 281, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 283, -1, -1, 283, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 283, 283, 283, 283, -1, 172, -1,
            284, -1, -1, -1, -1, -1, -1, 283,
            283, 283, 283, 283, 283, 283, 283, 283,
            283, 283, 283, 283, 283, -1, -1, 283,
            283, 283, 283, -1, 283, 283, 283, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 176, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 179, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 179, 179, 179, 179, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 179,
            179, 179, 179, 179, 179, 179, 179, 179,
            179, -1, -1, 179, 179, -1, -1, 179,
            179, 179, 179, -1, 179, 179, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 206, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 206, 206, 206, 206, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 206,
            206, 206, 206, 206, 206, 206, 206, 206,
            206, -1, -1, 206, 206, -1, -1, 206,
            206, 206, 206, -1, 206, 206, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 240, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 240, 240, 240, 240, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 240,
            240, 240, 240, 240, 240, 240, 240, 240,
            240, -1, -1, 240, 240, -1, -1, 240,
            240, 240, 240, -1, 240, 240, -1, -1 ),
        array( -1, -1, -1, 124, -1, -1, 124, 131,
            -1, -1, 280, -1, -1, -1, -1, -1,
            132, 124, 124, 124, 124, -1, -1, -1,
            133, -1, -1, -1, -1, -1, -1, 124,
            124, 124, 124, 124, 124, 124, 124, 124,
            124, 124, 124, 124, 141, 15, 16, 124,
            124, 124, 124, -1, 124, 124, 124, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 290, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 181, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 181, 181, 181, 181, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 181,
            181, 181, 181, 181, 181, 181, 181, 181,
            181, -1, -1, 181, 181, -1, -1, 181,
            181, 181, 181, -1, 181, 181, -1, -1 ),
        array( -1, -1, -1, 124, -1, -1, 124, 131,
            -1, -1, 280, -1, -1, -1, -1, -1,
            132, 124, 124, 124, 124, -1, -1, -1,
            133, -1, -1, -1, -1, -1, -1, 124,
            124, 124, 124, 124, 124, 124, 124, 124,
            124, 124, 124, 124, 124, 15, 16, 124,
            149, 124, 124, -1, 124, 124, 124, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 294, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 124, -1, -1, 124, 131,
            -1, -1, 280, -1, -1, -1, -1, -1,
            132, 124, 124, 124, 124, -1, -1, -1,
            133, -1, -1, -1, -1, -1, -1, 124,
            124, 124, 124, 124, 124, 124, 124, 124,
            124, 124, 124, 124, 124, 15, 16, 124,
            124, 124, 166, -1, 124, 124, 124, -1 ),
        array( -1, -1, -1, 283, -1, -1, 283, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 283, 283, 283, 283, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 283,
            283, 283, 283, 283, 283, 283, 283, 283,
            283, 283, 283, 283, 283, -1, -1, 283,
            283, 283, 283, -1, 283, 283, 283, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 297, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 299, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 301, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 303, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 305, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 307, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 309, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 311, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 313, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 315, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 124, -1, -1, 124, 131,
            -1, -1, 280, -1, -1, -1, -1, -1,
            132, 124, 124, 124, 124, -1, -1, -1,
            133, -1, -1, -1, -1, -1, -1, 124,
            124, 124, 124, 124, 124, 124, 124, 124,
            124, 124, 124, 124, 124, 15, 16, 124,
            124, 124, 124, -1, 288, 322, 124, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 308, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 291, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 306, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 208, -1 ),
        array( -1, -1, -1, 124, -1, -1, 124, 131,
            -1, -1, 280, -1, -1, -1, -1, -1,
            132, 124, 124, 124, 124, -1, -1, -1,
            133, -1, -1, -1, -1, -1, -1, 124,
            293, 124, 124, 124, 124, 124, 124, 124,
            124, 124, 124, 124, 124, 15, 16, 124,
            124, 124, 124, -1, 124, 124, 124, -1 ),
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
            -1, -1, 308, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 208, -1 ),
        array( -1, -1, -1, 124, -1, -1, 124, 131,
            -1, -1, 280, -1, -1, -1, -1, -1,
            132, 124, 124, 124, 124, -1, -1, -1,
            133, -1, -1, -1, -1, -1, -1, 124,
            124, 296, 124, 124, 124, 124, 124, 124,
            124, 124, 124, 124, 124, 15, 16, 124,
            124, 124, 124, -1, 124, 124, 124, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 298, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 300, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 302, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 310, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 312, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 314, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 316, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 124, -1, -1, 124, 131,
            -1, -1, 280, -1, -1, -1, -1, -1,
            132, 124, 124, 124, 124, -1, -1, -1,
            133, -1, -1, -1, -1, -1, -1, 124,
            124, 124, 124, 124, 124, 124, 124, 124,
            124, 124, 124, 124, 124, 15, 16, 124,
            124, 325, 124, -1, 124, 124, 124, -1 ),
        array( -1, -1, -1, 124, -1, -1, 124, 131,
            -1, -1, 280, -1, -1, -1, -1, -1,
            132, 124, 124, 124, 124, -1, -1, -1,
            133, -1, -1, -1, -1, -1, -1, 124,
            124, 124, 124, 124, 124, 124, 124, 124,
            124, 124, 124, 124, 124, 15, 16, 124,
            333, 124, 124, -1, 124, 124, 124, -1 ),
        array( -1, -1, -1, 124, -1, -1, 124, 131,
            -1, -1, 280, -1, -1, -1, -1, -1,
            132, 124, 124, 124, 124, -1, -1, -1,
            133, -1, -1, -1, -1, -1, -1, 124,
            124, 124, 334, 124, 124, 124, 124, 124,
            124, 124, 124, 124, 124, 15, 16, 124,
            124, 124, 124, -1, 124, 124, 124, -1 ),
        array( -1, -1, -1, 124, -1, -1, 124, 131,
            -1, -1, 280, -1, -1, -1, -1, -1,
            132, 124, 124, 124, 124, -1, -1, -1,
            133, -1, -1, -1, -1, -1, -1, 124,
            124, 124, 124, 124, 124, 124, 124, 124,
            124, 124, 124, 124, 124, 15, 16, 335,
            124, 124, 124, -1, 124, 124, 124, -1 )
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
                       if ($yy_last_accept_state < 337) {
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
    /* <![ -- marked section */
    $this->yybegin(IN_CDATA);
    $this->yyCdataBegin = $this->yy_buffer_end;
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 25:
{
    $this->value = HTML_Template_Flexy_Token::factory('Foreach',array(substr($this->yytext(),9,-1)),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
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
    // <!^--...-->   -- comment */   
    $this->value = HTML_Template_Flexy_Token::factory('Comment',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 62:
{   
    $this->value = HTML_Template_Flexy_Token::factory('CloseTag',$this->yytext(),$this->yyline);
    $this->yybegin(YYINITIAL); 
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 63:
{ 
    $this->value = HTML_Template_Flexy_Token::factory('Declaration',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 64:
{ 
    // ] -- declaration subset close */
    $this->value = HTML_Template_Flexy_Token::factory('DSEndSubset',$this->yytext(),$this->yyline);
    $this->yybegin(IN_COM); 
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 65:
{
    // ]]> -- marked section end */
     $this->value = HTML_Template_Flexy_Token::factory('DSEnd',$this->yytext(),$this->yyline);
    $this->yybegin(YYINITIAL);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 66:
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
case 67:
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
case 68:
{
    $t = $this->yytext();
    if ($t{1} == ':') {
        $this->flexyMethod .= substr($t,1,-1);
    }
    $this->value = HTML_Template_Flexy_Token::factory('Method'  , array($this->flexyMethod,$this->flexyArgs), $this->yyline);
    $this->yybegin(YYINITIAL);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 69:
{
    $t = substr($this->yytext(),0,-1);
    // add argument
    $this->flexyArgs[] = $t;
    $this->yybegin(IN_FLEXYMETHODQUOTED_END);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 70:
{
    $t = $this->yytext();
    $this->flexyArgs[] =$t;
    $this->yybegin(IN_FLEXYMETHODQUOTED_END);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 71:
{
    $t = $this->yytext();
    if ($p = strpos($t,':')) {
        $this->flexyMethod .= substr($t,$p,2);
    }
    $this->attrVal[] = HTML_Template_Flexy_Token::factory('Method'  , array($this->flexyMethod,$this->flexyArgs), $this->yyline);    
    $this->yybegin($this->flexyMethodState);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 72:
{
    $this->yybegin(IN_FLEXYMETHODQUOTED);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 73:
{
    // general text in script..
    $this->value = HTML_Template_Flexy_Token::factory('Text',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 74:
{
    // just < .. 
    $this->value = HTML_Template_Flexy_Token::factory('Text',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 75:
{
    // </script>
    $this->value = HTML_Template_Flexy_Token::factory('EndTag',
        array('/script'),
        $this->yyline);
    $this->yybegin(YYINITIAL);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 76:
{ 
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 77:
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
    return $this->raiseError("unexpected something: (".$this->yytext() .") character: 0x" . dechex(ord($this->yytext())));
}
case 80:
{
    //abcd -- data characters  
    // { added for flexy
    $this->value = HTML_Template_Flexy_Token::factory('Text',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 81:
{
    // &abc;
    $this->value = HTML_Template_Flexy_Token::factory('Text',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 82:
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
case 83:
{
    // &#123;
    $this->value = HTML_Template_Flexy_Token::factory('Text',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 84:
{
    // &#abc;
    $this->value = HTML_Template_Flexy_Token::factory('Text',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 85:
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
case 86:
{
    /* <!DOCTYPE -- markup declaration */
    if ($this->ignoreHTML) {
        return $this->returnSimple();
    }
    $this->value = HTML_Template_Flexy_Token::factory('Doctype',$this->yytext(),$this->yyline);
    $this->yybegin(IN_MD);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 87:
{
    /* <![ -- marked section */
    return $this->returnSimple();
}
case 88:
{
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 89:
{
    // <foo^<bar> -- unclosed start tag */
    return $this->raiseError("Unclosed tags not supported"); 
}
case 90:
{
    // <img src="xxx" ...ismap...> the ismap */
    $this->attributes[trim($this->yytext())] = true;
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 91:
{
    // <a href = ^http://foo/> -- unquoted literal HACK */                          
    $this->attributes[$this->attrKey] = trim($this->yytext());
    $this->yybegin(IN_ATTR);
    //   $this->raiseError("attribute value needs quotes");
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 92:
{
    // <a name = ^12pt> -- number token */
    $this->attributes[$this->attrKey] = trim($this->yytext());
    $this->yybegin(IN_ATTR);
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 93:
{
    //echo "GOT DATA:".$this->yytext();
    $this->attrVal[] = $this->yytext();
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 94:
{ 
    $this->value = HTML_Template_Flexy_Token::factory('WhiteSpace',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 95:
{
    return $this->raiseError("illegal character in markup declaration");
    return HTML_TEMPLATE_FLEXY_TOKEN_ERROR;
}
case 96:
{   
    $this->value = HTML_Template_Flexy_Token::factory('Number',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 97:
{ 
    $this->value = HTML_Template_Flexy_Token::factory('Name',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 98:
{ 
    $this->value = HTML_Template_Flexy_Token::factory('NameT',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 99:
{ 
    $this->value = HTML_Template_Flexy_Token::factory('NumberT',$this->yytext(),$this->yyline);    
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 100:
{
    // <!doctype ^%foo;> -- parameter entity reference */
    $this->value = HTML_Template_Flexy_Token::factory('EntityRef',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 101:
{ 
    $this->value = HTML_Template_Flexy_Token::factory('Literal',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 102:
{
    // <!^--...-->   -- comment */   
    $this->value = HTML_Template_Flexy_Token::factory('Comment',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 103:
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
case 104:
{
    $t = substr($this->yytext(),0,-1);
    // add argument
    $this->flexyArgs[] = $t;
    $this->yybegin(IN_FLEXYMETHODQUOTED_END);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 105:
{ 
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 107:
{
    return $this->raiseError("unexpected something: (".$this->yytext() .") character: 0x" . dechex(ord($this->yytext())));
}
case 108:
{
    //abcd -- data characters  
    // { added for flexy
    $this->value = HTML_Template_Flexy_Token::factory('Text',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 109:
{
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 110:
{
    // <a name = ^12pt> -- number token */
    $this->attributes[$this->attrKey] = trim($this->yytext());
    $this->yybegin(IN_ATTR);
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 111:
{
    //echo "GOT DATA:".$this->yytext();
    $this->attrVal[] = $this->yytext();
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 112:
{
    return $this->raiseError("illegal character in markup declaration");
    return HTML_TEMPLATE_FLEXY_TOKEN_ERROR;
}
case 113:
{
    $t = substr($this->yytext(),0,-1);
    // add argument
    $this->flexyArgs[] = $t;
    $this->yybegin(IN_FLEXYMETHODQUOTED_END);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 115:
{
    return $this->raiseError("unexpected something: (".$this->yytext() .") character: 0x" . dechex(ord($this->yytext())));
}
case 116:
{
    return $this->raiseError("illegal character in markup declaration");
    return HTML_TEMPLATE_FLEXY_TOKEN_ERROR;
}
case 118:
{
    return $this->raiseError("unexpected something: (".$this->yytext() .") character: 0x" . dechex(ord($this->yytext())));
}
case 119:
{
    return $this->raiseError("illegal character in markup declaration");
    return HTML_TEMPLATE_FLEXY_TOKEN_ERROR;
}
case 121:
{
    return $this->raiseError("unexpected something: (".$this->yytext() .") character: 0x" . dechex(ord($this->yytext())));
}
case 123:
{
    return $this->raiseError("unexpected something: (".$this->yytext() .") character: 0x" . dechex(ord($this->yytext())));
}
case 125:
{
    return $this->raiseError("unexpected something: (".$this->yytext() .") character: 0x" . dechex(ord($this->yytext())));
}
case 127:
{
    return $this->raiseError("unexpected something: (".$this->yytext() .") character: 0x" . dechex(ord($this->yytext())));
}
case 129:
{
    return $this->raiseError("unexpected something: (".$this->yytext() .") character: 0x" . dechex(ord($this->yytext())));
}
case 276:
{
    //abcd -- data characters  
    // { added for flexy
    $this->value = HTML_Template_Flexy_Token::factory('Text',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 277:
{
    // <a name = ^12pt> -- number token */
    $this->attributes[$this->attrKey] = trim($this->yytext());
    $this->yybegin(IN_ATTR);
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 278:
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
