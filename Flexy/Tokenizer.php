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
    *    ignore_php - default is to remove all PHP code from template.
    *           although this may not produce a tidy result - eg. close ?> in comments
    *           it will have the desired effect of blocking injection of PHP from templates.
    *    ignore_html - return all tags as  text tokens
    *
    *
    * @var      boolean  public
    * @access   public
    */
    var $options = array(
        'ignore_html' => false,
        'ignore_php'  => true,
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
        $this->value = $this->createToken('TextSimple',$this->yytext(),$this->yyline);
        return HTML_TEMPLATE_FLEXY_TOKEN_OK;
    }
    /**
    * Create a token based on the value of $this->options['token_call']
    *
    *
    * @return   Object   some kind of token..
    * @access   public
    */
    function createToken() 
    {
        $a = func_get_args();
        return call_user_func_array($this->options['token_factory'],$a);
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
        222,
        35,
        131,
        244,
        245,
        246,
        247,
        54,
        65,
        254,
        256,
        275,
        289,
        290,
        298,
        83,
        85,
        87
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
        /* 86 */   YY_NO_ANCHOR,
        /* 87 */   YY_NO_ANCHOR,
        /* 88 */   YY_NO_ANCHOR,
        /* 89 */   YY_NO_ANCHOR,
        /* 90 */   YY_NO_ANCHOR,
        /* 91 */   YY_NOT_ACCEPT,
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
        /* 118 */   YY_NO_ANCHOR,
        /* 119 */   YY_NO_ANCHOR,
        /* 120 */   YY_NO_ANCHOR,
        /* 121 */   YY_NO_ANCHOR,
        /* 122 */   YY_NO_ANCHOR,
        /* 123 */   YY_NO_ANCHOR,
        /* 124 */   YY_NO_ANCHOR,
        /* 125 */   YY_NO_ANCHOR,
        /* 126 */   YY_NO_ANCHOR,
        /* 127 */   YY_NOT_ACCEPT,
        /* 128 */   YY_NO_ANCHOR,
        /* 129 */   YY_NO_ANCHOR,
        /* 130 */   YY_NO_ANCHOR,
        /* 131 */   YY_NO_ANCHOR,
        /* 132 */   YY_NO_ANCHOR,
        /* 133 */   YY_NO_ANCHOR,
        /* 134 */   YY_NO_ANCHOR,
        /* 135 */   YY_NO_ANCHOR,
        /* 136 */   YY_NO_ANCHOR,
        /* 137 */   YY_NO_ANCHOR,
        /* 138 */   YY_NOT_ACCEPT,
        /* 139 */   YY_NO_ANCHOR,
        /* 140 */   YY_NO_ANCHOR,
        /* 141 */   YY_NO_ANCHOR,
        /* 142 */   YY_NOT_ACCEPT,
        /* 143 */   YY_NO_ANCHOR,
        /* 144 */   YY_NO_ANCHOR,
        /* 145 */   YY_NOT_ACCEPT,
        /* 146 */   YY_NO_ANCHOR,
        /* 147 */   YY_NO_ANCHOR,
        /* 148 */   YY_NOT_ACCEPT,
        /* 149 */   YY_NO_ANCHOR,
        /* 150 */   YY_NO_ANCHOR,
        /* 151 */   YY_NOT_ACCEPT,
        /* 152 */   YY_NO_ANCHOR,
        /* 153 */   YY_NO_ANCHOR,
        /* 154 */   YY_NOT_ACCEPT,
        /* 155 */   YY_NO_ANCHOR,
        /* 156 */   YY_NOT_ACCEPT,
        /* 157 */   YY_NO_ANCHOR,
        /* 158 */   YY_NOT_ACCEPT,
        /* 159 */   YY_NO_ANCHOR,
        /* 160 */   YY_NOT_ACCEPT,
        /* 161 */   YY_NO_ANCHOR,
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
        /* 320 */   YY_NO_ANCHOR,
        /* 321 */   YY_NO_ANCHOR,
        /* 322 */   YY_NO_ANCHOR,
        /* 323 */   YY_NO_ANCHOR,
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
        /* 340 */   YY_NOT_ACCEPT,
        /* 341 */   YY_NOT_ACCEPT,
        /* 342 */   YY_NOT_ACCEPT,
        /* 343 */   YY_NOT_ACCEPT,
        /* 344 */   YY_NOT_ACCEPT,
        /* 345 */   YY_NOT_ACCEPT,
        /* 346 */   YY_NOT_ACCEPT,
        /* 347 */   YY_NOT_ACCEPT,
        /* 348 */   YY_NOT_ACCEPT,
        /* 349 */   YY_NOT_ACCEPT,
        /* 350 */   YY_NOT_ACCEPT,
        /* 351 */   YY_NOT_ACCEPT,
        /* 352 */   YY_NOT_ACCEPT,
        /* 353 */   YY_NOT_ACCEPT,
        /* 354 */   YY_NOT_ACCEPT,
        /* 355 */   YY_NOT_ACCEPT,
        /* 356 */   YY_NOT_ACCEPT,
        /* 357 */   YY_NOT_ACCEPT,
        /* 358 */   YY_NOT_ACCEPT,
        /* 359 */   YY_NOT_ACCEPT,
        /* 360 */   YY_NOT_ACCEPT,
        /* 361 */   YY_NOT_ACCEPT,
        /* 362 */   YY_NOT_ACCEPT,
        /* 363 */   YY_NOT_ACCEPT,
        /* 364 */   YY_NOT_ACCEPT,
        /* 365 */   YY_NOT_ACCEPT,
        /* 366 */   YY_NOT_ACCEPT,
        /* 367 */   YY_NOT_ACCEPT,
        /* 368 */   YY_NOT_ACCEPT,
        /* 369 */   YY_NOT_ACCEPT,
        /* 370 */   YY_NOT_ACCEPT,
        /* 371 */   YY_NOT_ACCEPT,
        /* 372 */   YY_NOT_ACCEPT,
        /* 373 */   YY_NOT_ACCEPT,
        /* 374 */   YY_NOT_ACCEPT,
        /* 375 */   YY_NOT_ACCEPT
        );


    var  $yy_cmap = array(
        31, 31, 31, 31, 31, 31, 31, 31,
        31, 11, 5, 31, 31, 12, 31, 31,
        31, 31, 31, 31, 31, 31, 31, 31,
        31, 31, 31, 31, 31, 31, 31, 31,
        11, 14, 30, 2, 32, 24, 1, 29,
        46, 21, 32, 32, 52, 15, 7, 9,
        3, 3, 3, 3, 3, 42, 3, 55,
        3, 3, 10, 4, 8, 28, 13, 23,
        31, 19, 43, 17, 18, 6, 6, 6,
        6, 38, 6, 6, 6, 6, 6, 6,
        40, 6, 37, 33, 20, 6, 6, 6,
        6, 6, 6, 16, 25, 22, 31, 26,
        31, 50, 43, 35, 44, 49, 47, 6,
        51, 39, 6, 6, 54, 6, 53, 48,
        40, 6, 36, 34, 41, 6, 6, 6,
        6, 6, 6, 27, 31, 45, 31, 31,
        31, 31, 31, 31, 31, 31, 31, 31,
        31, 31, 31, 31, 31, 31, 31, 31,
        31, 31, 31, 31, 31, 31, 31, 31,
        31, 31, 31, 31, 31, 31, 31, 31,
        31, 31, 31, 31, 31, 31, 31, 31,
        31, 31, 31, 31, 31, 31, 31, 31,
        31, 31, 31, 31, 31, 31, 31, 31,
        31, 31, 31, 31, 31, 31, 31, 31,
        31, 31, 31, 31, 31, 31, 31, 31,
        31, 31, 31, 31, 31, 31, 31, 31,
        31, 31, 31, 31, 31, 31, 31, 31,
        31, 31, 31, 31, 31, 31, 31, 31,
        31, 31, 31, 31, 31, 31, 31, 31,
        31, 31, 31, 31, 31, 31, 31, 31,
        31, 31, 31, 31, 31, 31, 31, 31,
        31, 31, 31, 31, 31, 31, 31, 31,
        31, 0, 0 
         );


    var $yy_rmap = array(
        0, 1, 2, 3, 4, 5, 1, 6,
        7, 8, 9, 1, 10, 1, 11, 12,
        1, 3, 1, 1, 1, 1, 1, 1,
        1, 1, 1, 1, 1, 1, 1, 13,
        1, 1, 1, 14, 1, 1, 15, 16,
        17, 1, 1, 18, 19, 18, 1, 1,
        1, 20, 1, 1, 21, 1, 22, 1,
        23, 24, 25, 1, 1, 26, 27, 28,
        29, 30, 1, 1, 31, 32, 1, 33,
        1, 1, 34, 1, 1, 1, 35, 36,
        1, 37, 1, 38, 1, 39, 1, 40,
        1, 1, 1, 41, 42, 43, 1, 44,
        45, 1, 1, 46, 47, 48, 49, 50,
        51, 18, 52, 53, 54, 50, 55, 56,
        57, 58, 59, 60, 61, 1, 62, 63,
        1, 64, 65, 66, 67, 68, 69, 70,
        71, 72, 73, 74, 75, 73, 76, 77,
        1, 78, 79, 1, 80, 81, 82, 83,
        84, 85, 86, 68, 87, 88, 89, 90,
        91, 92, 93, 94, 95, 96, 97, 98,
        99, 100, 101, 102, 103, 104, 105, 106,
        107, 108, 109, 110, 111, 112, 113, 114,
        115, 116, 117, 118, 119, 120, 121, 122,
        123, 124, 125, 126, 127, 128, 129, 130,
        131, 132, 133, 134, 135, 136, 137, 138,
        139, 140, 141, 142, 143, 144, 145, 146,
        147, 148, 149, 150, 151, 152, 153, 154,
        155, 156, 157, 158, 159, 160, 161, 71,
        162, 163, 164, 165, 166, 167, 168, 169,
        170, 171, 172, 173, 174, 175, 176, 177,
        178, 179, 180, 16, 181, 182, 183, 184,
        86, 76, 81, 185, 186, 63, 187, 188,
        189, 88, 91, 190, 191, 192, 193, 194,
        195, 196, 197, 198, 199, 200, 201, 202,
        203, 204, 205, 206, 96, 207, 208, 209,
        210, 211, 212, 213, 214, 215, 216, 217,
        218, 219, 220, 221, 222, 223, 224, 225,
        226, 227, 228, 229, 230, 231, 232, 233,
        68, 234, 235, 236, 69, 237, 238, 239,
        240, 241, 242, 243, 244, 245, 246, 247,
        248, 75, 249, 250, 251, 73, 114, 252,
        253, 254, 255, 256, 126, 257, 258, 135,
        259, 260, 147, 261, 151, 262, 166, 263,
        172, 264, 191, 265, 197, 266, 208, 267,
        214, 268, 236, 269, 240, 270, 271, 272,
        273, 274, 275, 276, 277, 278, 279, 280,
        281, 282, 283, 284, 285, 286, 287, 288
        );


    var $yy_nxt = array(
        array( 1, 2, 3, 3, 3, 3, 3, 3,
            92, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 93, 320, 3,
            3, 3, 3, 129, 3, 3, 3, 3,
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
        array( -1, -1, 91, 3, 3, 3, 4, 3,
            -1, 3, 3, 3, 3, 3, 3, 3,
            3, 4, 4, 4, 4, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 3,
            3, 4, 4, 4, 4, 4, 4, 4,
            4, 4, 3, 4, 4, 3, 3, 4,
            4, 4, 4, 4, 3, 4, 4, 3 ),
        array( -1, 127, 3, 3, 3, 3, 3, 3,
            138, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, -1, 3, 3,
            3, 3, 3, -1, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 3 ),
        array( -1, -1, -1, 4, 94, 94, 4, 4,
            -1, -1, -1, -1, -1, -1, -1, 4,
            -1, 4, 4, 4, 4, -1, -1, -1,
            -1, -1, 4, -1, -1, -1, -1, -1,
            -1, 4, 4, 4, 4, 4, 4, 4,
            4, 4, 4, 4, 4, -1, -1, 4,
            4, 4, 4, 4, -1, 4, 4, 4 ),
        array( -1, -1, -1, 5, -1, 95, 5, 5,
            -1, -1, 5, 95, 95, -1, -1, 5,
            -1, 5, 5, 5, 5, -1, -1, -1,
            -1, -1, 5, -1, -1, -1, -1, -1,
            -1, 5, 5, 5, 5, 5, 5, 5,
            5, 5, 5, 5, 5, -1, -1, 5,
            5, 5, 5, 5, -1, 5, 5, 5 ),
        array( -1, -1, -1, -1, -1, 96, 15, -1,
            -1, -1, -1, 96, 96, -1, -1, -1,
            -1, 15, 15, 15, 15, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 15, 15, 15, 15, 15, 15, 15,
            15, 15, -1, 15, 15, -1, -1, 15,
            15, 15, 15, 15, -1, 15, 15, -1 ),
        array( -1, -1, -1, 8, 97, 97, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 8, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 8 ),
        array( -1, -1, -1, 9, 98, 98, 9, 9,
            -1, -1, -1, -1, -1, -1, -1, 9,
            -1, 9, 9, 9, 9, -1, -1, -1,
            -1, -1, 9, -1, -1, -1, -1, -1,
            -1, 9, 9, 9, 9, 9, 9, 9,
            9, 9, 9, 9, 9, -1, -1, 9,
            9, 9, 9, 9, -1, 9, 9, 9 ),
        array( -1, -1, -1, 10, -1, 99, 10, 10,
            -1, 158, 10, 99, 99, -1, -1, 10,
            -1, 10, 10, 10, 10, -1, -1, -1,
            -1, -1, 10, -1, -1, -1, -1, -1,
            -1, 10, 10, 10, 10, 10, 10, 10,
            10, 10, 10, 10, 10, -1, -1, 10,
            10, 10, 10, 10, -1, 10, 10, 10 ),
        array( -1, -1, -1, 12, -1, 100, 12, 12,
            -1, -1, -1, 100, 100, -1, -1, 12,
            -1, 12, 12, 12, 12, -1, -1, -1,
            -1, -1, 12, -1, -1, -1, -1, -1,
            -1, 12, 12, 12, 12, 12, 12, 12,
            12, 12, 12, 12, 12, -1, -1, 12,
            12, 12, 12, 12, -1, 12, 12, 12 ),
        array( -1, -1, -1, -1, -1, 101, -1, -1,
            -1, -1, -1, 101, 101, -1, -1, -1,
            -1, 167, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 15, -1, 102, 15, 15,
            -1, -1, -1, 102, 102, -1, -1, 15,
            -1, 15, 15, 15, 15, -1, -1, -1,
            -1, -1, 15, -1, -1, -1, -1, -1,
            -1, 15, 15, 15, 15, 15, 15, 15,
            15, 15, 15, 15, 15, -1, -1, 15,
            15, 15, 15, 15, -1, 15, 15, 15 ),
        array( -1, 31, 31, 31, 31, 31, 31, 31,
            31, 31, 31, 31, 31, 31, 31, 31,
            31, 31, 31, 31, 31, 31, 31, 31,
            -1, 223, 31, -1, 31, -1, 31, 31,
            31, 31, 31, 31, 31, 31, 31, 31,
            31, 31, 31, 31, 31, 31, 31, 31,
            31, 31, 31, 31, 31, 31, 31, 31 ),
        array( 1, 139, 139, 139, 139, 104, 139, 139,
            36, 139, 139, 104, 104, 37, 139, 139,
            139, 139, 139, 139, 139, 139, 139, 139,
            139, 139, 139, 139, 139, 139, 139, 139,
            139, 139, 139, 139, 139, 139, 139, 139,
            139, 139, 139, 139, 139, 139, 139, 139,
            139, 139, 139, 139, 139, 139, 139, 139 ),
        array( -1, -1, -1, 38, -1, 106, 38, 38,
            -1, -1, 38, 106, 106, -1, -1, 38,
            -1, 38, 38, 38, 38, -1, -1, -1,
            -1, -1, 38, -1, 40, -1, -1, -1,
            -1, 38, 38, 38, 38, 38, 38, 38,
            38, 38, 38, 38, 38, -1, -1, 38,
            38, 38, 38, 38, -1, 38, 38, 38 ),
        array( -1, -1, -1, -1, -1, 243, -1, -1,
            -1, -1, -1, 243, 243, 41, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 40, -1, -1,
            -1, -1, -1, 40, 40, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, 43, 43, 43, 43, 107, 43, 43,
            43, 43, 43, 107, 107, -1, 43, 43,
            43, 43, 43, 43, 43, 43, 43, 43,
            43, 43, 43, 43, 43, -1, -1, 43,
            43, 43, 43, 43, 43, 43, 43, 43,
            43, 43, 43, 43, 43, 43, 43, 43,
            43, 43, 43, 43, 43, 43, 43, 43 ),
        array( -1, 43, 43, 44, 43, 108, 44, 44,
            43, 43, 43, 108, 108, -1, 43, 44,
            43, 44, 44, 44, 44, 43, 43, 43,
            43, 43, 44, 43, 43, -1, -1, 43,
            43, 44, 44, 44, 44, 44, 44, 44,
            44, 44, 44, 44, 44, 43, 43, 44,
            44, 44, 44, 44, 43, 44, 44, 44 ),
        array( -1, -1, -1, -1, -1, 49, -1, -1,
            -1, -1, -1, 49, 49, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, 52, 52, 52, 52, 52, 52, 52,
            52, 52, 52, 52, 52, 52, 52, 52,
            52, 52, 52, 52, 52, 52, 52, 52,
            -1, 248, 52, -1, 52, 52, -1, 52,
            52, 52, 52, 52, 52, 52, 52, 52,
            52, 52, 52, 52, 52, 52, 52, 52,
            52, 52, 52, 52, 52, 52, 52, 52 ),
        array( 1, 55, 55, 56, 55, 110, 57, 58,
            55, 55, 55, 110, 110, 59, 55, 58,
            60, 57, 57, 57, 57, 55, 55, 55,
            111, 55, 58, 55, 55, 134, 141, 55,
            55, 57, 57, 57, 57, 57, 57, 57,
            57, 57, 56, 57, 57, 55, 55, 57,
            57, 57, 57, 57, 55, 57, 57, 56 ),
        array( -1, -1, -1, 56, -1, 112, 61, 61,
            -1, -1, -1, 112, 112, -1, -1, 61,
            -1, 61, 61, 61, 61, -1, -1, -1,
            -1, -1, 61, -1, -1, -1, -1, -1,
            -1, 61, 61, 61, 61, 61, 61, 61,
            61, 61, 56, 61, 61, -1, -1, 61,
            61, 61, 61, 61, -1, 61, 61, 56 ),
        array( -1, -1, -1, 57, -1, 113, 57, 57,
            -1, -1, -1, 113, 113, -1, -1, 57,
            -1, 57, 57, 57, 57, -1, -1, -1,
            -1, -1, 57, -1, -1, -1, -1, -1,
            -1, 57, 57, 57, 57, 57, 57, 57,
            57, 57, 57, 57, 57, -1, -1, 57,
            57, 57, 57, 57, -1, 57, 57, 57 ),
        array( -1, -1, -1, 58, -1, 114, 58, 58,
            -1, -1, -1, 114, 114, -1, -1, 58,
            -1, 58, 58, 58, 58, -1, -1, -1,
            -1, -1, 58, -1, -1, -1, -1, -1,
            -1, 58, 58, 58, 58, 58, 58, 58,
            58, 58, 58, 58, 58, -1, -1, 58,
            58, 58, 58, 58, -1, 58, 58, 58 ),
        array( -1, -1, -1, 61, -1, 115, 61, 61,
            -1, -1, -1, 115, 115, -1, -1, 61,
            -1, 61, 61, 61, 61, -1, -1, -1,
            -1, -1, 61, -1, -1, -1, -1, -1,
            -1, 61, 61, 61, 61, 61, 61, 61,
            61, 61, 61, 61, 61, -1, -1, 61,
            61, 61, 61, 61, -1, 61, 61, 61 ),
        array( -1, -1, -1, -1, -1, 62, -1, -1,
            -1, -1, -1, 62, 62, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 63, 116, 116, 63, 63,
            -1, -1, -1, 116, 116, -1, -1, 63,
            -1, 63, 63, 63, 63, -1, -1, -1,
            -1, -1, 63, -1, -1, -1, -1, -1,
            -1, 63, 63, 63, 63, 63, 63, 63,
            63, 63, 63, 63, 63, -1, -1, 63,
            63, 63, 63, 63, -1, 63, 63, 63 ),
        array( -1, -1, -1, -1, -1, 64, -1, -1,
            -1, -1, -1, 64, 64, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( 1, 118, 118, 118, 118, 118, 118, 118,
            118, 118, 118, 118, 118, 118, 118, 144,
            118, 118, 118, 118, 118, 118, 118, 118,
            118, 118, 118, 118, 118, 118, 118, 118,
            118, 118, 118, 118, 118, 118, 118, 118,
            118, 118, 118, 118, 118, 118, 118, 118,
            118, 118, 118, 118, 118, 118, 118, 118 ),
        array( -1, 68, 68, 68, 68, 68, 68, 68,
            68, 68, 68, 68, 68, 68, 68, 68,
            68, 68, 68, 68, 68, 68, -1, 68,
            68, 68, 68, 68, 68, 68, 68, 68,
            68, 68, 68, 68, 68, 68, 68, 68,
            68, 68, 68, 68, 68, 68, 68, 68,
            68, 68, 68, 68, 68, 68, 68, 68 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 255, -1,
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
            -1, -1, -1, -1, 120, -1, -1, -1 ),
        array( -1, -1, -1, 74, -1, -1, 74, 277,
            -1, -1, -1, -1, -1, -1, -1, -1,
            278, 74, 74, 74, 74, -1, -1, -1,
            368, -1, 74, -1, -1, -1, -1, -1,
            -1, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 74, 74, 74, -1, -1, 74,
            74, 74, 74, 74, -1, 74, 74, 74 ),
        array( -1, 78, 78, 78, 78, 78, 78, 78,
            -1, 78, 78, 78, 78, 78, 78, 78,
            78, 78, 78, 78, 78, 78, 78, 78,
            78, 78, 78, 78, 78, 78, 78, 78,
            78, 78, 78, 78, 78, 78, 78, 78,
            78, 78, 78, 78, 78, 78, 78, 78,
            78, 78, 78, 78, 78, 78, 78, 78 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 291, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, 81, 81, 81, 81, 81, 81, 81,
            81, 81, 81, 81, 81, 81, 81, 81,
            81, 81, 81, 81, 81, 81, -1, 81,
            81, 81, 81, 81, 81, 81, 81, 81,
            81, 81, 81, 81, 81, 81, 81, 81,
            81, 81, 81, 81, 81, 81, 81, 81,
            81, 81, 81, 81, 81, 81, 81, 81 ),
        array( 1, 123, 123, 123, 123, 123, 123, 123,
            123, 123, 123, 123, 123, 123, 123, 161,
            123, 123, 123, 123, 123, 123, 123, 123,
            123, 123, 123, 123, 123, 123, 123, 123,
            123, 123, 123, 123, 123, 123, 123, 123,
            123, 123, 123, 123, 123, 123, 123, 123,
            123, 123, 123, 123, 123, 123, 123, 123 ),
        array( 1, 124, 124, 124, 124, 124, 124, 124,
            124, 124, 124, 124, 124, 124, 124, 124,
            124, 124, 124, 124, 124, 124, 124, 301,
            124, 124, 124, 124, 124, 124, 124, 124,
            124, 124, 124, 124, 124, 124, 124, 124,
            124, 124, 124, 124, 124, 124, 124, 124,
            124, 124, 124, 124, 124, 124, 124, 124 ),
        array( 1, 147, 147, 147, 147, 125, 147, 147,
            147, 147, 147, 125, 125, 147, 147, 150,
            147, 147, 147, 147, 147, 147, 147, 147,
            147, 147, 147, 153, 147, 147, 147, 147,
            147, 147, 147, 147, 147, 147, 147, 147,
            147, 147, 147, 147, 147, 147, 147, 147,
            147, 147, 147, 147, 147, 147, 147, 147 ),
        array( -1, -1, -1, 8, -1, -1, 9, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 9, 9, 9, 9, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 9, 9, 9, 9, 9, 9, 9,
            9, 9, 8, 9, 9, -1, -1, 9,
            9, 9, 9, 9, -1, 9, 9, 8 ),
        array( -1, -1, -1, -1, -1, 3, 5, -1,
            -1, 142, -1, 3, 3, 6, 145, -1,
            3, 5, 5, 5, 5, -1, 3, 7,
            -1, 3, 3, 3, -1, -1, -1, 3,
            -1, 5, 5, 5, 5, 5, 5, 5,
            5, 5, -1, 5, 5, 3, -1, 5,
            5, 5, 5, 5, -1, 5, 5, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 148, -1, -1, -1, -1, -1,
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
        array( -1, -1, -1, -1, -1, 96, -1, -1,
            -1, -1, -1, 96, 96, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 99, -1, -1,
            -1, 158, -1, 99, 99, -1, -1, -1,
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
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 224 ),
        array( -1, -1, -1, -1, -1, 104, -1, -1,
            -1, -1, -1, 104, 104, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 106, -1, -1,
            -1, -1, -1, 106, 106, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 40, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 107, -1, -1,
            -1, -1, -1, 107, 107, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 108, -1, -1,
            -1, -1, -1, 108, 108, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 110, -1, -1,
            -1, -1, -1, 110, 110, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 62, 63, -1,
            -1, -1, -1, 62, 62, -1, -1, -1,
            -1, 63, 63, 63, 63, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 63, 63, 63, 63, 63, 63, 63,
            63, 63, -1, 63, 63, -1, -1, 63,
            63, 63, 63, 63, -1, 63, 63, -1 ),
        array( -1, -1, -1, -1, -1, 112, -1, -1,
            -1, -1, -1, 112, 112, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 113, -1, -1,
            -1, -1, -1, 113, 113, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 114, -1, -1,
            -1, -1, -1, 114, 114, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 115, -1, -1,
            -1, -1, -1, 115, 115, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 116, -1, -1,
            -1, -1, -1, 116, 116, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, 118, 118, 118, 118, 118, 118, 118,
            118, 118, 118, 118, 118, 118, 118, 251,
            118, 118, 118, 118, 118, 118, 118, 118,
            118, 118, 118, 118, 118, 118, 118, 118,
            118, 118, 118, 118, 118, 118, 118, 118,
            118, 118, 118, 118, 118, 118, 118, 118,
            118, 118, 118, 118, 118, 118, 118, 118 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 67, -1, 253,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, 277,
            -1, -1, -1, -1, -1, -1, -1, -1,
            278, -1, -1, -1, -1, -1, -1, -1,
            368, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 135, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, 123, 123, 123, 123, 123, 123, 123,
            123, 123, 123, 123, 123, 123, 123, 299,
            123, 123, 123, 123, 123, 123, 123, 123,
            123, 123, 123, 123, 123, 123, 123, 123,
            123, 123, 123, 123, 123, 123, 123, 123,
            123, 123, 123, 123, 123, 123, 123, 123,
            123, 123, 123, 123, 123, 123, 123, 123 ),
        array( -1, 124, 124, 124, 124, 124, 124, 124,
            124, 124, 124, 124, 124, 124, 124, 124,
            124, 124, 124, 124, 124, 124, 124, -1,
            124, 124, 124, 124, 124, 124, 124, 124,
            124, 124, 124, 124, 124, 124, 124, 124,
            124, 124, 124, 124, 124, 124, 124, 124,
            124, 124, 124, 124, 124, 124, 124, 124 ),
        array( -1, 137, 137, 137, 137, 137, 137, 137,
            137, 137, 137, 137, 137, 137, 137, -1,
            137, 137, 137, 137, 137, 137, 137, 137,
            137, 137, 137, 137, 137, 137, 137, 137,
            137, 137, 137, 137, 137, 137, 137, 137,
            137, 137, 137, 137, 137, 137, 137, 137,
            137, 137, 137, 137, 137, 137, 137, 137 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 89, -1, 308,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 3, 3, 3, -1, 3,
            -1, 3, 3, 3, 3, 3, 3, 3,
            3, -1, -1, -1, -1, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 3,
            3, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 3, -1, -1, 3, 3, -1,
            -1, -1, -1, -1, 3, -1, -1, 3 ),
        array( -1, 31, 31, 31, 31, 31, 31, 31,
            31, 31, 31, 31, 31, 31, 31, 31,
            31, 31, 31, 31, 31, 31, 31, 31,
            31, 31, 31, 31, 31, -1, 31, 31,
            31, 31, 31, 31, 31, 31, 31, 31,
            31, 31, 31, 31, 31, 31, 31, 31,
            31, 31, 31, 31, 31, 31, 31, 31 ),
        array( -1, -1, -1, -1, -1, -1, 151, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 151, 151, 151, 151, -1, -1, -1,
            -1, -1, 154, -1, -1, -1, -1, -1,
            -1, 151, 151, 151, 151, 151, 151, 324,
            151, 151, -1, 151, 151, -1, -1, 375,
            151, 358, 151, 151, -1, 151, 151, -1 ),
        array( -1, -1, -1, -1, -1, -1, 225, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 225, 225, 225, 225, -1, -1, -1,
            -1, -1, 225, -1, -1, -1, -1, -1,
            -1, 225, 225, 225, 225, 225, 225, 225,
            225, 225, -1, 225, 225, -1, -1, 225,
            225, 225, 225, 225, -1, 225, 225, -1 ),
        array( 1, 139, 139, 139, 139, 104, 38, 139,
            36, 39, 139, 104, 104, 37, 139, 139,
            139, 38, 38, 38, 38, 139, 139, 143,
            139, 139, 139, 139, 139, 139, 139, 139,
            139, 38, 38, 38, 38, 38, 38, 38,
            38, 38, 139, 38, 38, 139, 139, 38,
            38, 38, 38, 38, 139, 38, 38, 139 ),
        array( -1, 43, 43, 132, 43, 108, 132, 132,
            43, 43, 43, 108, 108, -1, 43, 132,
            43, 132, 132, 132, 132, 43, 43, 43,
            43, 43, 132, 43, 43, -1, -1, 43,
            43, 132, 132, 132, 132, 132, 132, 132,
            132, 132, 132, 132, 132, 43, 43, 132,
            132, 132, 132, 132, 43, 132, 132, 132 ),
        array( -1, 249, 249, 249, 249, 249, 249, 249,
            249, 249, 249, 249, 249, 249, 249, 249,
            249, 249, 249, 249, 249, 249, 249, 249,
            249, 249, 249, 249, 249, 64, 249, 249,
            249, 249, 249, 249, 249, 249, 249, 249,
            249, 249, 249, 249, 249, 249, 249, 249,
            249, 249, 249, 249, 249, 249, 249, 249 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 82, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, 304, 304, 304, 304, 125, 304, 304,
            304, 304, 304, 125, 125, 304, 304, 304,
            304, 304, 304, 304, 304, 304, 304, 304,
            304, 304, 304, -1, 304, 304, 304, 304,
            304, 304, 304, 304, 304, 304, 304, 304,
            304, 304, 304, 304, 304, 304, 304, 304,
            304, 304, 304, 304, 304, 304, 304, 304 ),
        array( -1, -1, -1, -1, -1, 3, -1, -1,
            -1, -1, -1, 3, 3, -1, -1, -1,
            3, -1, -1, -1, -1, -1, 3, -1,
            -1, 3, 3, 3, -1, -1, -1, 3,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 3, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, 127, 3, 3, 3, 3, 3, 3,
            138, 3, 3, 3, 3, 17, 3, 3,
            3, 3, 3, 3, 3, -1, 3, 3,
            3, 3, 3, -1, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 3 ),
        array( -1, 250, 250, 250, 250, 250, 250, 250,
            250, 250, 250, 250, 250, 250, 250, 250,
            250, 250, 250, 250, 250, 250, 250, 250,
            250, 250, 250, 250, 250, 250, 117, 250,
            250, 250, 250, 250, 250, 250, 250, 250,
            250, 250, 250, 250, 250, 250, 250, 250,
            250, 250, 250, 250, 250, 250, 250, 250 ),
        array( -1, -1, -1, -1, -1, 156, 10, -1,
            -1, 158, -1, 156, 156, 11, -1, -1,
            -1, 10, 10, 10, 10, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 10, 10, 10, 10, 10, 10, 10,
            10, 10, -1, 10, 10, -1, -1, 10,
            10, 10, 10, 10, -1, 10, 10, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 42, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, 118, 118, 118, 118, 118, 118, 118,
            118, 118, 118, 118, 118, 118, 118, 252,
            118, 118, 118, 118, 118, 118, 118, 118,
            118, 118, 118, 118, 118, 118, 118, 118,
            118, 118, 118, 118, 118, 118, 118, 118,
            118, 118, 118, 118, 118, 118, 118, 118,
            118, 118, 118, 118, 118, 118, 118, 118 ),
        array( -1, -1, -1, -1, -1, -1, 12, -1,
            -1, -1, -1, -1, -1, 13, -1, 160,
            14, 12, 12, 12, 12, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 12, 12, 12, 12, 12, 12, 12,
            12, 12, -1, 12, 12, -1, -1, 12,
            12, 12, 12, 12, -1, 12, 12, -1 ),
        array( -1, 52, 52, 52, 52, 52, 52, 52,
            52, 52, 52, 52, 52, 52, 52, 52,
            52, 52, 52, 52, 52, 52, 52, 52,
            52, -1, 52, 52, 52, 52, -1, 52,
            52, 52, 52, 52, 52, 52, 52, 52,
            52, 52, 52, 52, 52, 52, 52, 52,
            52, 52, 52, 52, 52, 52, 52, 52 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 16, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, 257, 71, 257, 257, 257, 257, 257,
            257, 257, 257, 257, 257, 257, 257, 257,
            257, 257, 257, 257, 257, 257, 257, 257,
            257, 257, 257, 257, 257, 257, 257, 257,
            257, 257, 257, 257, 257, 257, 257, 257,
            257, 257, 257, 257, 257, 257, 257, 257,
            257, 257, 257, 257, 257, 257, 257, 257 ),
        array( -1, 137, 137, 137, 137, 137, 137, 137,
            137, 137, 137, 137, 137, 137, 137, 302,
            137, 137, 137, 137, 137, 137, 137, 137,
            137, 137, 137, 137, 137, 137, 137, 137,
            137, 137, 137, 137, 137, 137, 137, 137,
            137, 137, 137, 137, 137, 137, 137, 137,
            137, 137, 137, 137, 137, 137, 137, 137 ),
        array( -1, -1, -1, 151, -1, -1, 151, 162,
            -1, -1, 163, -1, -1, -1, -1, -1,
            164, 151, 151, 151, 151, -1, -1, -1,
            165, -1, 151, -1, -1, -1, -1, -1,
            -1, 151, 151, 151, 151, 151, 151, 151,
            151, 151, 151, 151, 151, 18, 19, 151,
            151, 151, 151, 151, -1, 151, 151, 151 ),
        array( -1, -1, -1, 258, -1, -1, 258, 259,
            -1, -1, -1, -1, -1, -1, -1, -1,
            260, 258, 258, 258, 258, 261, -1, -1,
            366, -1, 258, -1, -1, -1, -1, -1,
            -1, 258, 258, 258, 258, 258, 258, 258,
            258, 258, 258, 258, 258, -1, -1, 258,
            258, 258, 258, 258, 72, 258, 258, 258 ),
        array( -1, -1, -1, -1, -1, -1, 303, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 303, 303, 303, 303, -1, -1, -1,
            -1, -1, 303, -1, -1, -1, -1, -1,
            -1, 303, 303, 303, 303, 303, 303, 303,
            303, 303, -1, 303, 303, -1, -1, 303,
            303, 303, 303, 303, -1, 303, 303, -1 ),
        array( -1, -1, -1, 151, -1, -1, 151, 162,
            -1, -1, 163, -1, -1, -1, -1, -1,
            164, 151, 151, 151, 151, -1, -1, -1,
            165, -1, 151, -1, -1, -1, -1, -1,
            -1, 151, 151, 151, 151, 151, 151, 151,
            151, 151, 151, 151, 151, 18, 20, 151,
            151, 151, 151, 151, -1, 151, 151, 151 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 262, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 73, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, 156, -1, -1,
            -1, 158, -1, 156, 156, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, 276, 75, 276, 276, 276, 276, 276,
            276, 276, 276, 276, 276, 276, 276, 276,
            276, 276, 276, 276, 276, 276, 276, 276,
            276, 276, 276, 276, 276, 276, 276, 276,
            276, 276, 276, 276, 276, 276, 276, 276,
            276, 276, 276, 276, 276, 276, 276, 276,
            276, 276, 276, 276, 276, 276, 276, 276 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            21, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 279, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            280, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 76, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 22,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, 123, 123, 123, 123, 123, 123, 123,
            123, 123, 123, 123, 123, 123, 123, 300,
            123, 123, 123, 123, 123, 123, 123, 123,
            123, 123, 123, 123, 123, 123, 123, 123,
            123, 123, 123, 123, 123, 123, 123, 123,
            123, 123, 123, 123, 123, 123, 123, 123,
            123, 123, 123, 123, 123, 123, 123, 123 ),
        array( -1, -1, -1, -1, -1, -1, 168, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 168, 168, 168, 168, -1, -1, -1,
            -1, -1, 168, -1, -1, -1, -1, -1,
            -1, 168, 168, 168, 168, 168, 168, 168,
            168, 168, -1, 168, 168, -1, -1, 168,
            168, 168, 168, 168, -1, 168, 168, -1 ),
        array( -1, -1, -1, -1, -1, -1, 169, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 169, 169, 169, 169, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 169, 169, 169, 169, 169, 169, 169,
            169, 169, -1, 169, 169, -1, -1, 169,
            169, 169, 169, 169, -1, 169, 169, -1 ),
        array( -1, -1, -1, 170, -1, -1, 170, -1,
            -1, -1, -1, -1, -1, -1, -1, 170,
            -1, 170, 170, 170, 170, -1, -1, -1,
            -1, -1, 170, -1, -1, -1, -1, -1,
            -1, 170, 170, 170, 170, 170, 170, 170,
            170, 170, 170, 170, 170, -1, -1, 170,
            170, 170, 170, 170, -1, 170, 170, 170 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 171, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 151, -1, -1, 151, 162,
            -1, -1, 172, -1, -1, -1, -1, -1,
            164, 151, 151, 151, 151, -1, -1, -1,
            165, -1, 151, -1, -1, -1, -1, -1,
            -1, 151, 151, 151, 151, 151, 151, 151,
            151, 151, 151, 151, 151, 18, 19, 151,
            151, 151, 151, 151, -1, 151, 151, 151 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 174, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 168, -1, -1, 168, 162,
            -1, -1, 163, -1, -1, -1, -1, -1,
            175, 168, 168, 168, 168, -1, -1, -1,
            176, -1, 168, -1, -1, -1, -1, -1,
            -1, 168, 168, 168, 168, 168, 168, 168,
            168, 168, 168, 168, 168, 18, 19, 168,
            168, 168, 168, 168, -1, 168, 168, 168 ),
        array( -1, -1, -1, -1, -1, -1, 169, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 169, 169, 169, 169, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 169, 169, 169, 169, 169, 169, 169,
            169, 169, -1, 169, 169, 18, -1, 169,
            169, 169, 169, 169, -1, 169, 169, -1 ),
        array( -1, -1, -1, 170, -1, -1, 170, -1,
            -1, -1, -1, -1, -1, -1, -1, 170,
            -1, 170, 170, 170, 170, -1, 177, -1,
            178, -1, 170, -1, -1, -1, -1, -1,
            -1, 170, 170, 170, 170, 170, 170, 170,
            170, 170, 170, 170, 170, -1, -1, 170,
            170, 170, 170, 170, -1, 170, 170, 170 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 164, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 179, -1,
            -1, -1, -1, -1, -1, -1, 180, -1,
            -1, 179, 179, 179, 179, -1, -1, -1,
            -1, -1, 179, -1, -1, -1, -1, -1,
            -1, 179, 179, 179, 179, 179, 179, 179,
            179, 179, -1, 179, 179, -1, -1, 179,
            179, 179, 179, 179, -1, 179, 179, -1 ),
        array( -1, -1, -1, 151, -1, -1, 151, 162,
            -1, -1, 181, -1, -1, -1, -1, -1,
            164, 151, 151, 151, 151, -1, -1, -1,
            165, -1, 151, -1, -1, -1, -1, -1,
            -1, 151, 151, 151, 151, 151, 151, 151,
            151, 151, 151, 151, 151, 18, 19, 151,
            151, 151, 151, 151, -1, 151, 151, 151 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 183, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 184, -1, -1, 184, -1,
            -1, -1, -1, -1, -1, -1, -1, 184,
            -1, 184, 184, 184, 184, -1, -1, -1,
            -1, -1, 184, -1, -1, -1, -1, -1,
            -1, 184, 184, 184, 184, 184, 184, 184,
            184, 184, 184, 184, 184, -1, -1, 184,
            184, 184, 184, 184, -1, 184, 184, 184 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 327, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, 162,
            -1, -1, 163, -1, -1, -1, -1, -1,
            164, -1, -1, -1, -1, -1, -1, -1,
            165, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 18, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 185, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 179, -1, -1, 179, 186,
            -1, -1, -1, -1, -1, -1, -1, -1,
            187, 179, 179, 179, 179, -1, -1, -1,
            359, -1, 179, -1, -1, -1, -1, -1,
            -1, 179, 179, 179, 179, 179, 179, 179,
            179, 179, 179, 179, 179, 23, 24, 179,
            179, 179, 179, 179, -1, 179, 179, 179 ),
        array( -1, -1, -1, -1, -1, -1, 179, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 179, 179, 179, 179, -1, -1, -1,
            -1, -1, 179, -1, -1, -1, -1, -1,
            -1, 179, 179, 179, 179, 179, 179, 179,
            179, 179, -1, 179, 179, -1, -1, 179,
            179, 179, 179, 179, -1, 179, 179, -1 ),
        array( -1, -1, -1, -1, -1, -1, 169, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 169, 169, 169, 169, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 169, 169, 169, 169, 169, 169, 169,
            169, 169, -1, 169, 169, 25, -1, 169,
            169, 169, 169, 169, -1, 169, 169, -1 ),
        array( -1, -1, -1, 151, -1, -1, 151, 162,
            -1, -1, 188, -1, -1, -1, -1, -1,
            164, 151, 151, 151, 151, -1, -1, -1,
            165, -1, 151, -1, -1, -1, -1, -1,
            -1, 151, 151, 151, 151, 151, 151, 151,
            151, 151, 151, 151, 151, 18, 19, 151,
            151, 151, 151, 151, -1, 151, 151, 151 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 189, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 184, -1, -1, 184, -1,
            -1, -1, -1, -1, -1, -1, -1, 184,
            -1, 184, 184, 184, 184, -1, 190, -1,
            191, -1, 184, -1, -1, -1, -1, -1,
            -1, 184, 184, 184, 184, 184, 184, 184,
            184, 184, 184, 184, 184, -1, -1, 184,
            184, 184, 184, 184, -1, 184, 184, 184 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 177, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 177, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 192, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 192, 192, 192, 192, -1, -1, -1,
            -1, -1, 192, -1, -1, -1, -1, -1,
            -1, 192, 192, 192, 192, 192, 192, 192,
            192, 192, -1, 192, 192, -1, -1, 192,
            192, 192, 192, 192, -1, 192, 192, -1 ),
        array( -1, -1, -1, 193, -1, -1, 193, -1,
            -1, -1, -1, -1, -1, -1, -1, 193,
            -1, 193, 193, 193, 193, -1, -1, -1,
            -1, -1, 193, -1, -1, -1, -1, -1,
            -1, 193, 193, 193, 193, 193, 193, 193,
            193, 193, 193, 193, 193, -1, -1, 193,
            193, 193, 193, 193, -1, 193, 193, 193 ),
        array( -1, -1, -1, -1, -1, -1, 169, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 169, 169, 169, 169, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 169, 169, 169, 169, 169, 169, 169,
            169, 169, -1, 169, 169, 26, -1, 169,
            169, 169, 169, 169, -1, 169, 169, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 194, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, 162,
            -1, -1, 163, -1, -1, -1, -1, -1,
            175, -1, -1, -1, -1, -1, -1, -1,
            176, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 18, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 195, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 192, -1, -1, 192, 186,
            -1, -1, -1, -1, -1, -1, -1, -1,
            196, 192, 192, 192, 192, -1, -1, -1,
            362, -1, 192, -1, -1, -1, -1, -1,
            -1, 192, 192, 192, 192, 192, 192, 192,
            192, 192, 192, 192, 192, 23, 24, 192,
            192, 192, 192, 192, -1, 192, 192, 192 ),
        array( -1, -1, -1, 193, -1, -1, 193, -1,
            -1, -1, -1, -1, -1, -1, -1, 193,
            -1, 193, 193, 193, 193, -1, 197, -1,
            198, -1, 193, -1, -1, -1, -1, -1,
            -1, 193, 193, 193, 193, 193, 193, 193,
            193, 193, 193, 193, 193, -1, -1, 193,
            193, 193, 193, 193, -1, 193, 193, 193 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            27, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 190, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 190, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 200, -1, -1, 200, -1,
            -1, -1, -1, -1, -1, -1, -1, 200,
            -1, 200, 200, 200, 200, -1, -1, -1,
            -1, -1, 200, -1, -1, -1, -1, -1,
            -1, 200, 200, 200, 200, 200, 200, 200,
            200, 200, 200, 200, 200, -1, -1, 200,
            200, 200, 200, 200, -1, 200, 200, 200 ),
        array( -1, -1, -1, -1, -1, -1, -1, 186,
            -1, -1, -1, -1, -1, -1, -1, -1,
            187, -1, -1, -1, -1, -1, -1, -1,
            359, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 23, 24, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 201, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 151, -1, -1, 151, 162,
            -1, -1, 202, -1, -1, -1, -1, -1,
            164, 151, 151, 151, 151, -1, -1, -1,
            165, -1, 151, -1, -1, -1, -1, -1,
            -1, 151, 151, 151, 151, 151, 151, 151,
            151, 151, 151, 151, 151, 18, 19, 151,
            151, 151, 151, 151, -1, 151, 151, 151 ),
        array( -1, -1, -1, 200, -1, -1, 200, -1,
            -1, -1, -1, -1, -1, -1, -1, 200,
            -1, 200, 200, 200, 200, -1, 203, -1,
            204, -1, 200, -1, -1, -1, -1, -1,
            -1, 200, 200, 200, 200, 200, 200, 200,
            200, 200, 200, 200, 200, -1, -1, 200,
            200, 200, 200, 200, -1, 200, 200, 200 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 197, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 197, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 205, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 205, 205, 205, 205, -1, -1, -1,
            -1, -1, 205, -1, -1, -1, -1, -1,
            -1, 205, 205, 205, 205, 205, 205, 205,
            205, 205, -1, 205, 205, -1, -1, 205,
            205, 205, 205, 205, -1, 205, 205, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, 186,
            -1, -1, -1, -1, -1, -1, -1, -1,
            196, -1, -1, -1, -1, -1, -1, -1,
            362, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 23, 24, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 206, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 205, -1, -1, 205, 207,
            -1, -1, -1, -1, -1, -1, -1, -1,
            208, 205, 205, 205, 205, -1, -1, -1,
            364, -1, 205, -1, -1, -1, -1, -1,
            -1, 205, 205, 205, 205, 205, 205, 205,
            205, 205, 205, 205, 205, 28, -1, 205,
            205, 205, 205, 205, 328, 205, 205, 205 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 203, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 203, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 209, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 209, 209, 209, 209, -1, -1, -1,
            -1, -1, 209, -1, -1, -1, -1, -1,
            -1, 209, 209, 209, 209, 209, 209, 209,
            209, 209, -1, 209, 209, -1, -1, 209,
            209, 209, 209, 209, -1, 209, 209, -1 ),
        array( -1, -1, -1, 210, -1, -1, 210, -1,
            -1, -1, -1, -1, -1, -1, -1, 210,
            -1, 210, 210, 210, 210, -1, -1, -1,
            -1, -1, 210, -1, -1, -1, -1, -1,
            -1, 210, 210, 210, 210, 210, 210, 210,
            210, 210, 210, 210, 210, -1, -1, 210,
            210, 210, 210, 210, -1, 210, 210, 210 ),
        array( -1, -1, -1, 209, -1, -1, 209, 207,
            -1, -1, -1, -1, -1, -1, -1, -1,
            212, 209, 209, 209, 209, -1, -1, -1,
            365, -1, 209, -1, -1, -1, -1, -1,
            -1, 209, 209, 209, 209, 209, 209, 209,
            209, 209, 209, 209, 209, 28, -1, 209,
            209, 209, 209, 209, 328, 209, 209, 209 ),
        array( -1, -1, -1, 210, -1, -1, 210, -1,
            -1, -1, -1, -1, -1, -1, -1, 210,
            -1, 210, 210, 210, 210, -1, 213, -1,
            214, -1, 210, -1, -1, -1, -1, -1,
            -1, 210, 210, 210, 210, 210, 210, 210,
            210, 210, 210, 210, 210, -1, -1, 210,
            210, 210, 210, 210, -1, 210, 210, 210 ),
        array( -1, -1, -1, 211, -1, -1, 211, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 211, 211, 211, 211, -1, -1, -1,
            -1, -1, 211, -1, -1, -1, -1, -1,
            -1, 211, 211, 211, 211, 211, 211, 211,
            211, 211, 211, 211, 211, 29, -1, 211,
            211, 211, 211, 211, 215, 211, 211, 211 ),
        array( -1, -1, -1, 216, -1, -1, 216, -1,
            -1, -1, -1, -1, -1, -1, -1, 216,
            -1, 216, 216, 216, 216, -1, -1, -1,
            -1, -1, 216, -1, -1, -1, -1, -1,
            -1, 216, 216, 216, 216, 216, 216, 216,
            216, 216, 216, 216, 216, -1, -1, 216,
            216, 216, 216, 216, -1, 216, 216, 216 ),
        array( -1, -1, -1, -1, -1, -1, -1, 207,
            -1, -1, -1, -1, -1, -1, -1, -1,
            208, -1, -1, -1, -1, -1, -1, -1,
            364, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 28, -1, -1,
            -1, -1, -1, -1, 328, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 217, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 218, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 218, 218, 218, 218, -1, -1, -1,
            -1, -1, 218, -1, -1, -1, -1, -1,
            -1, 218, 218, 218, 218, 218, 218, 218,
            218, 218, -1, 218, 218, -1, -1, 218,
            218, 218, 218, 218, -1, 218, 218, -1 ),
        array( -1, -1, -1, 216, -1, -1, 216, -1,
            -1, -1, -1, -1, -1, -1, -1, 216,
            -1, 216, 216, 216, 216, -1, 219, -1,
            220, -1, 216, -1, -1, -1, -1, -1,
            -1, 216, 216, 216, 216, 216, 216, 216,
            216, 216, 216, 216, 216, -1, -1, 216,
            216, 216, 216, 216, -1, 216, 216, 216 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 213, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 213, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 218, -1, -1, 218, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 218, 218, 218, 218, -1, -1, -1,
            -1, -1, 218, -1, -1, -1, -1, -1,
            -1, 218, 218, 218, 218, 218, 218, 218,
            218, 218, 218, 218, 218, 30, -1, 218,
            218, 218, 218, 218, -1, 218, 218, 218 ),
        array( -1, -1, -1, -1, -1, -1, -1, 207,
            -1, -1, -1, -1, -1, -1, -1, -1,
            212, -1, -1, -1, -1, -1, -1, -1,
            365, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 28, -1, -1,
            -1, -1, -1, -1, 328, -1, -1, -1 ),
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
        array( 1, 31, 31, 31, 31, 31, 31, 31,
            31, 31, 31, 31, 31, 31, 31, 31,
            31, 31, 31, 31, 31, 31, 31, 31,
            103, 128, 31, 130, 31, 32, 31, 31,
            31, 31, 31, 31, 31, 31, 31, 31,
            31, 31, 31, 31, 31, 31, 31, 31,
            31, 31, 31, 31, 31, 31, 31, 31 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 325, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 225, -1, -1, 225, 226,
            -1, -1, 227, -1, -1, -1, -1, -1,
            228, 225, 225, 225, 225, -1, -1, -1,
            229, -1, 225, -1, -1, -1, -1, -1,
            -1, 225, 225, 225, 225, 225, 225, 225,
            225, 225, 225, 225, 225, 33, 34, 225,
            225, 225, 225, 225, -1, 225, 225, 225 ),
        array( -1, -1, -1, -1, -1, -1, 230, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 230, 230, 230, 230, -1, -1, -1,
            -1, -1, 230, -1, -1, -1, -1, -1,
            -1, 230, 230, 230, 230, 230, 230, 230,
            230, 230, -1, 230, 230, -1, -1, 230,
            230, 230, 230, 230, -1, 230, 230, -1 ),
        array( -1, -1, -1, -1, -1, -1, 231, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 231, 231, 231, 231, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 231, 231, 231, 231, 231, 231, 231,
            231, 231, -1, 231, 231, -1, -1, 231,
            231, 231, 231, 231, -1, 231, 231, -1 ),
        array( -1, -1, -1, 232, -1, -1, 232, -1,
            -1, -1, -1, -1, -1, -1, -1, 232,
            -1, 232, 232, 232, 232, -1, -1, -1,
            -1, -1, 232, -1, -1, -1, -1, -1,
            -1, 232, 232, 232, 232, 232, 232, 232,
            232, 232, 232, 232, 232, -1, -1, 232,
            232, 232, 232, 232, -1, 232, 232, 232 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 343, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 233 ),
        array( -1, -1, -1, 230, -1, -1, 230, 226,
            -1, -1, 227, -1, -1, -1, -1, -1,
            234, 230, 230, 230, 230, -1, -1, -1,
            360, -1, 230, -1, -1, -1, -1, -1,
            -1, 230, 230, 230, 230, 230, 230, 230,
            230, 230, 230, 230, 230, 33, 34, 230,
            230, 230, 230, 230, -1, 230, 230, 230 ),
        array( -1, -1, -1, -1, -1, -1, 231, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 231, 231, 231, 231, -1, -1, -1,
            235, -1, -1, -1, -1, -1, -1, -1,
            -1, 231, 231, 231, 231, 231, 231, 231,
            231, 231, -1, 231, 231, 33, -1, 231,
            231, 231, 231, 231, -1, 231, 231, -1 ),
        array( -1, -1, -1, 232, -1, -1, 232, -1,
            -1, -1, -1, -1, -1, -1, -1, 232,
            -1, 232, 232, 232, 232, -1, 236, -1,
            237, -1, 232, -1, -1, -1, -1, -1,
            -1, 232, 232, 232, 232, 232, 232, 232,
            232, 232, 232, 232, 232, -1, -1, 232,
            232, 232, 232, 232, -1, 232, 232, 232 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 33, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 33, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 238, -1, -1, 238, -1,
            -1, -1, -1, -1, -1, -1, -1, 238,
            -1, 238, 238, 238, 238, -1, -1, -1,
            -1, -1, 238, -1, -1, -1, -1, -1,
            -1, 238, 238, 238, 238, 238, 238, 238,
            238, 238, 238, 238, 238, -1, -1, 238,
            238, 238, 238, 238, -1, 238, 238, 238 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 233 ),
        array( -1, -1, -1, -1, -1, -1, -1, 226,
            -1, -1, 227, -1, -1, -1, -1, -1,
            228, -1, -1, -1, -1, -1, -1, -1,
            229, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 33, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 239, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 238, -1, -1, 238, -1,
            -1, -1, -1, -1, -1, -1, -1, 238,
            -1, 238, 238, 238, 238, -1, 240, -1,
            241, -1, 238, -1, -1, -1, -1, -1,
            -1, 238, 238, 238, 238, 238, 238, 238,
            238, 238, 238, 238, 238, -1, -1, 238,
            238, 238, 238, 238, -1, 238, 238, 238 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 236, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 236, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, 226,
            -1, -1, 227, -1, -1, -1, -1, -1,
            234, -1, -1, -1, -1, -1, -1, -1,
            360, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 33, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 242, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 240, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 240, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( 1, 43, 43, 44, 43, -1, 321, 321,
            105, 45, 43, 139, -1, 46, 43, 321,
            43, 321, 321, 321, 321, 43, 43, 43,
            43, 43, 321, 43, 43, 47, 48, 43,
            43, 321, 321, 321, 321, 321, 321, 321,
            321, 321, 44, 321, 321, 43, 43, 321,
            321, 321, 321, 321, 43, 321, 321, 44 ),
        array( 1, 139, 139, 139, 139, 49, 139, 139,
            139, 139, 139, 49, 49, 139, 139, 139,
            139, 139, 139, 139, 139, 139, 139, 139,
            139, 139, 139, 139, 139, 139, 139, 139,
            139, 139, 139, 139, 139, 139, 139, 139,
            139, 139, 139, 139, 139, 139, 139, 139,
            139, 139, 139, 139, 139, 139, 139, 139 ),
        array( 1, 50, 50, 50, 50, -1, 50, 50,
            50, 50, 50, 50, -1, 51, 50, 50,
            50, 50, 50, 50, 50, 50, 50, 50,
            50, 50, 50, 50, 50, 50, 50, 50,
            50, 50, 50, 50, 50, 50, 50, 50,
            50, 50, 50, 50, 50, 50, 50, 50,
            50, 50, 50, 50, 50, 50, 50, 50 ),
        array( 1, 52, 52, 52, 52, 52, 52, 52,
            52, 52, 52, 52, 52, 52, 52, 52,
            52, 52, 52, 52, 52, 52, 52, 52,
            109, 146, 52, 133, 52, 52, 53, 52,
            52, 52, 52, 52, 52, 52, 52, 52,
            52, 52, 52, 52, 52, 52, 52, 52,
            52, 52, 52, 52, 52, 52, 52, 52 ),
        array( -1, 118, 118, 118, 118, 118, 118, 118,
            118, 118, 118, 118, 118, 118, 118, -1,
            118, 118, 118, 118, 118, 118, 118, 118,
            118, 118, 118, 118, 118, 118, 118, 118,
            118, 118, 118, 118, 118, 118, 118, 118,
            118, 118, 118, 118, 118, 118, 118, 118,
            118, 118, 118, 118, 118, 118, 118, 118 ),
        array( -1, 66, 66, 66, 66, 66, 66, 66,
            66, 66, 66, 66, 66, 67, 66, 119,
            66, 66, 66, 66, 66, 66, 66, 66,
            66, 66, 66, 66, 66, 66, 66, 66,
            66, 66, 66, 66, 66, 66, 66, 66,
            66, 66, 66, 66, 66, 66, 66, 66,
            66, 66, 66, 66, 66, 66, 66, 66 ),
        array( 1, 68, 68, 68, 68, 68, 68, 68,
            68, 68, 68, 68, 68, 68, 68, 68,
            68, 68, 68, 68, 68, 68, 69, 68,
            68, 68, 68, 68, 68, 68, 68, 68,
            68, 68, 68, 68, 68, 68, 68, 68,
            68, 68, 68, 68, 68, 68, 68, 68,
            68, 68, 68, 68, 68, 68, 68, 68 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 70, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( 1, 139, 149, 139, 139, -1, 152, 139,
            139, 139, 139, 139, -1, 139, 139, 139,
            139, 152, 152, 152, 152, 155, 139, 139,
            139, 139, 152, 139, 139, 139, 139, 139,
            139, 152, 152, 152, 152, 152, 152, 152,
            152, 152, 139, 152, 152, 139, 139, 152,
            152, 152, 152, 152, 139, 152, 152, 139 ),
        array( -1, -1, -1, -1, -1, -1, 263, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 263, 263, 263, 263, -1, -1, -1,
            -1, -1, 263, -1, -1, -1, -1, -1,
            -1, 263, 263, 263, 263, 263, 263, 263,
            263, 263, -1, 263, 263, -1, -1, 263,
            263, 263, 263, 263, -1, 263, 263, -1 ),
        array( -1, -1, -1, 264, -1, -1, 264, -1,
            -1, -1, -1, -1, -1, -1, -1, 264,
            -1, 264, 264, 264, 264, -1, -1, -1,
            -1, -1, 264, -1, -1, -1, -1, -1,
            -1, 264, 264, 264, 264, 264, 264, 264,
            264, 264, 264, 264, 264, -1, -1, 264,
            264, 264, 264, 264, -1, 264, 264, 264 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 329, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 72, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 265, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 265, 265, 265, 265, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 265, 265, 265, 265, 265, 265, 265,
            265, 265, -1, 265, 265, -1, -1, 265,
            265, 265, 265, 265, -1, 265, 265, -1 ),
        array( -1, -1, -1, 263, -1, -1, 263, 259,
            -1, -1, -1, -1, -1, -1, -1, -1,
            266, 263, 263, 263, 263, 261, -1, -1,
            367, -1, 263, -1, -1, -1, -1, -1,
            -1, 263, 263, 263, 263, 263, 263, 263,
            263, 263, 263, 263, 263, -1, -1, 263,
            263, 263, 263, 263, 72, 263, 263, 263 ),
        array( -1, -1, -1, 264, -1, -1, 264, -1,
            -1, -1, -1, -1, -1, -1, -1, 264,
            -1, 264, 264, 264, 264, -1, 267, -1,
            268, -1, 264, -1, -1, -1, -1, -1,
            -1, 264, 264, 264, 264, 264, 264, 264,
            264, 264, 264, 264, 264, -1, -1, 264,
            264, 264, 264, 264, -1, 264, 264, 264 ),
        array( -1, -1, -1, -1, -1, -1, 265, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 265, 265, 265, 265, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 265, 265, 265, 265, 265, 265, 265,
            265, 265, -1, 265, 265, 73, -1, 265,
            265, 265, 265, 265, -1, 265, 265, -1 ),
        array( -1, -1, -1, 270, -1, -1, 270, -1,
            -1, -1, -1, -1, -1, -1, -1, 270,
            -1, 270, 270, 270, 270, -1, -1, -1,
            -1, -1, 270, -1, -1, -1, -1, -1,
            -1, 270, 270, 270, 270, 270, 270, 270,
            270, 270, 270, 270, 270, -1, -1, 270,
            270, 270, 270, 270, -1, 270, 270, 270 ),
        array( -1, -1, -1, -1, -1, -1, -1, 259,
            -1, -1, -1, -1, -1, -1, -1, -1,
            260, -1, -1, -1, -1, 261, -1, -1,
            366, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 72, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 271, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 269, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 269, 269, 269, 269, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 269, 269, 269, 269, 269, 269, 269,
            269, 269, -1, 269, 269, 72, -1, 269,
            269, 269, 269, 269, -1, 269, 269, -1 ),
        array( -1, -1, -1, 270, -1, -1, 270, -1,
            -1, -1, -1, -1, -1, -1, -1, 270,
            -1, 270, 270, 270, 270, -1, 272, -1,
            273, -1, 270, -1, -1, -1, -1, -1,
            -1, 270, 270, 270, 270, 270, 270, 270,
            270, 270, 270, 270, 270, -1, -1, 270,
            270, 270, 270, 270, -1, 270, 270, 270 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 267, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 267, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, 259,
            -1, -1, -1, -1, -1, -1, -1, -1,
            266, -1, -1, -1, -1, 261, -1, -1,
            367, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 72, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 274, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 272, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 272, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( 1, 139, 157, 139, 139, -1, 74, 139,
            139, 139, 139, 139, -1, 139, 139, 139,
            139, 74, 74, 74, 74, 159, 139, 139,
            139, 139, 74, 139, 139, 139, 139, 139,
            139, 74, 74, 74, 74, 74, 74, 74,
            74, 74, 139, 74, 74, 139, 139, 74,
            74, 74, 74, 74, 139, 74, 74, 139 ),
        array( -1, -1, -1, -1, -1, -1, 322, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 322, 322, 322, 322, -1, -1, -1,
            -1, -1, 322, -1, -1, -1, -1, -1,
            -1, 322, 322, 322, 322, 322, 322, 322,
            322, 322, -1, 322, 322, -1, -1, 322,
            322, 322, 322, 322, -1, 322, 322, -1 ),
        array( -1, -1, -1, 281, -1, -1, 281, -1,
            -1, -1, -1, -1, -1, -1, -1, 281,
            -1, 281, 281, 281, 281, -1, -1, -1,
            -1, -1, 281, -1, -1, -1, -1, -1,
            -1, 281, 281, 281, 281, 281, 281, 281,
            281, 281, 281, 281, 281, -1, -1, 281,
            281, 281, 281, 281, -1, 281, 281, 281 ),
        array( -1, -1, -1, -1, -1, -1, 282, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 282, 282, 282, 282, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 282, 282, 282, 282, 282, 282, 282,
            282, 282, -1, 282, 282, -1, -1, 282,
            282, 282, 282, 282, -1, 282, 282, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 283 ),
        array( -1, -1, -1, 281, -1, -1, 281, -1,
            -1, -1, -1, -1, -1, -1, -1, 281,
            -1, 281, 281, 281, 281, -1, 121, -1,
            285, -1, 281, -1, -1, -1, -1, -1,
            -1, 281, 281, 281, 281, 281, 281, 281,
            281, 281, 281, 281, 281, -1, -1, 281,
            281, 281, 281, 281, -1, 281, 281, 281 ),
        array( -1, -1, -1, -1, -1, -1, 282, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 282, 282, 282, 282, -1, -1, -1,
            280, -1, -1, -1, -1, -1, -1, -1,
            -1, 282, 282, 282, 282, 282, 282, 282,
            282, 282, -1, 282, 282, 76, -1, 282,
            282, 282, 282, 282, -1, 282, 282, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 76, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 76, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 286, -1, -1, 286, -1,
            -1, -1, -1, -1, -1, -1, -1, 286,
            -1, 286, 286, 286, 286, -1, -1, -1,
            -1, -1, 286, -1, -1, -1, -1, -1,
            -1, 286, 286, 286, 286, 286, 286, 286,
            286, 286, 286, 286, 286, -1, -1, 286,
            286, 286, 286, 286, -1, 286, 286, 286 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 287, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 286, -1, -1, 286, -1,
            -1, -1, -1, -1, -1, -1, -1, 286,
            -1, 286, 286, 286, 286, -1, 323, -1,
            288, -1, 286, -1, -1, -1, -1, -1,
            -1, 286, 286, 286, 286, 286, 286, 286,
            286, 286, 286, 286, 286, -1, -1, 286,
            286, 286, 286, 286, -1, 286, 286, 286 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 121, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 121, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 330, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( 1, 139, 139, 139, 139, -1, 139, 139,
            139, 139, 139, 139, -1, 139, 139, 139,
            139, 139, 139, 139, 139, 159, 139, 139,
            139, 139, 139, 139, 139, 139, 139, 139,
            139, 139, 139, 139, 139, 139, 139, 139,
            139, 139, 139, 139, 139, 139, 139, 139,
            139, 139, 139, 139, 77, 139, 139, 139 ),
        array( 1, 78, 78, 78, 78, 78, 78, 78,
            79, 78, 78, 78, 78, 78, 78, 78,
            78, 78, 78, 78, 78, 78, 78, 78,
            78, 78, 78, 78, 78, 78, 78, 78,
            78, 78, 78, 78, 78, 78, 78, 78,
            78, 78, 78, 78, 78, 78, 78, 78,
            78, 78, 78, 78, 78, 78, 78, 78 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 292, 292, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 293, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 293, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 294, 294, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 295, 295,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            296, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 297, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 297, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 80, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( 1, 81, 81, 81, 81, 81, 81, 81,
            81, 81, 81, 81, 81, 81, 81, 81,
            81, 81, 81, 81, 81, 81, 122, 81,
            81, 81, 81, 81, 81, 81, 81, 81,
            81, 81, 81, 81, 81, 81, 81, 81,
            81, 81, 81, 81, 81, 81, 81, 81,
            81, 81, 81, 81, 81, 81, 81, 81 ),
        array( -1, 123, 123, 123, 123, 123, 123, 123,
            123, 123, 123, 123, 123, 123, 123, -1,
            123, 123, 123, 123, 123, 123, 123, 123,
            123, 123, 123, 123, 123, 123, 123, 123,
            123, 123, 123, 123, 123, 123, 123, 123,
            123, 123, 123, 123, 123, 123, 123, 123,
            123, 123, 123, 123, 123, 123, 123, 123 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 84, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, 136, 136, 136, 136, 136, 136, 136,
            136, 136, 136, 136, 136, 86, 136, 136,
            136, 136, 136, 136, 136, 136, 136, 136,
            136, 136, 136, 136, 136, 136, 136, 136,
            136, 136, 136, 136, 136, 136, 136, 136,
            136, 136, 136, 136, 136, 136, 136, 136,
            136, 136, 136, 136, 136, 136, 136, 136 ),
        array( -1, 88, 88, 88, 88, 88, 88, 88,
            88, 88, 88, 88, 88, 89, 88, 126,
            88, 88, 88, 88, 88, 88, 88, 88,
            88, 88, 88, 88, 88, 88, 88, 88,
            88, 88, 88, 88, 88, 88, 88, 88,
            88, 88, 88, 88, 88, 88, 88, 88,
            88, 88, 88, 88, 88, 88, 88, 88 ),
        array( -1, -1, -1, 303, -1, -1, 303, 305,
            -1, -1, 306, -1, -1, -1, -1, -1,
            307, 303, 303, 303, 303, -1, -1, -1,
            370, -1, 303, -1, -1, -1, -1, -1,
            -1, 303, 303, 303, 303, 303, 303, 303,
            303, 303, 303, 303, 303, 90, -1, 303,
            303, 303, 303, 303, -1, 303, 303, 303 ),
        array( -1, -1, -1, -1, -1, -1, 309, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 309, 309, 309, 309, -1, -1, -1,
            -1, -1, 309, -1, -1, -1, -1, -1,
            -1, 309, 309, 309, 309, 309, 309, 309,
            309, 309, -1, 309, 309, -1, -1, 309,
            309, 309, 309, 309, -1, 309, 309, -1 ),
        array( -1, -1, -1, -1, -1, -1, 310, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 310, 310, 310, 310, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 310, 310, 310, 310, 310, 310, 310,
            310, 310, -1, 310, 310, -1, -1, 310,
            310, 310, 310, 310, -1, 310, 310, -1 ),
        array( -1, -1, -1, 311, -1, -1, 311, -1,
            -1, -1, -1, -1, -1, -1, -1, 311,
            -1, 311, 311, 311, 311, -1, -1, -1,
            -1, -1, 311, -1, -1, -1, -1, -1,
            -1, 311, 311, 311, 311, 311, 311, 311,
            311, 311, 311, 311, 311, -1, -1, 311,
            311, 311, 311, 311, -1, 311, 311, 311 ),
        array( -1, -1, -1, 309, -1, -1, 309, 305,
            -1, -1, 306, -1, -1, -1, -1, -1,
            312, 309, 309, 309, 309, -1, -1, -1,
            371, -1, 309, -1, -1, -1, -1, -1,
            -1, 309, 309, 309, 309, 309, 309, 309,
            309, 309, 309, 309, 309, 90, -1, 309,
            309, 309, 309, 309, -1, 309, 309, 309 ),
        array( -1, -1, -1, -1, -1, -1, 310, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 310, 310, 310, 310, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 310, 310, 310, 310, 310, 310, 310,
            310, 310, -1, 310, 310, 90, -1, 310,
            310, 310, 310, 310, -1, 310, 310, -1 ),
        array( -1, -1, -1, 311, -1, -1, 311, -1,
            -1, -1, -1, -1, -1, -1, -1, 311,
            -1, 311, 311, 311, 311, -1, 313, -1,
            314, -1, 311, -1, -1, -1, -1, -1,
            -1, 311, 311, 311, 311, 311, 311, 311,
            311, 311, 311, 311, 311, -1, -1, 311,
            311, 311, 311, 311, -1, 311, 311, 311 ),
        array( -1, -1, -1, 315, -1, -1, 315, -1,
            -1, -1, -1, -1, -1, -1, -1, 315,
            -1, 315, 315, 315, 315, -1, -1, -1,
            -1, -1, 315, -1, -1, -1, -1, -1,
            -1, 315, 315, 315, 315, 315, 315, 315,
            315, 315, 315, 315, 315, -1, -1, 315,
            315, 315, 315, 315, -1, 315, 315, 315 ),
        array( -1, -1, -1, -1, -1, -1, -1, 305,
            -1, -1, 306, -1, -1, -1, -1, -1,
            307, -1, -1, -1, -1, -1, -1, -1,
            370, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 90, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 316, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 315, -1, -1, 315, -1,
            -1, -1, -1, -1, -1, -1, -1, 315,
            -1, 315, 315, 315, 315, -1, 317, -1,
            318, -1, 315, -1, -1, -1, -1, -1,
            -1, 315, 315, 315, 315, 315, 315, 315,
            315, 315, 315, 315, 315, -1, -1, 315,
            315, 315, 315, 315, -1, 315, 315, 315 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 313, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 313, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, 305,
            -1, -1, 306, -1, -1, -1, -1, -1,
            312, -1, -1, -1, -1, -1, -1, -1,
            371, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, 90, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 319, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 317, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 317, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, 127, 3, 3, 3, 3, 3, 3,
            138, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, -1, 140, 3,
            3, 3, 3, -1, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 3,
            3, 3, 3, 3, 3, 3, 3, 3 ),
        array( -1, -1, -1, 322, -1, -1, 322, 277,
            -1, -1, -1, -1, -1, -1, -1, -1,
            284, 322, 322, 322, 322, -1, -1, -1,
            369, -1, 322, -1, -1, -1, -1, -1,
            -1, 322, 322, 322, 322, 322, 322, 322,
            322, 322, 322, 322, 322, -1, -1, 322,
            322, 322, 322, 322, -1, 322, 322, 322 ),
        array( -1, -1, -1, -1, -1, -1, -1, 277,
            -1, -1, -1, -1, -1, -1, -1, -1,
            284, -1, -1, -1, -1, -1, -1, -1,
            369, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 151, -1, -1, 151, 162,
            -1, -1, 163, -1, -1, -1, -1, -1,
            164, 151, 151, 151, 151, -1, -1, -1,
            165, -1, 151, -1, -1, -1, -1, -1,
            -1, 151, 151, 151, 151, 151, 151, 151,
            151, 151, 151, 151, 151, 18, 19, 166,
            151, 151, 151, 151, -1, 151, 151, 151 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 326, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 211, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 211, 211, 211, 211, -1, -1, -1,
            -1, -1, 211, -1, -1, -1, -1, -1,
            -1, 211, 211, 211, 211, 211, 211, 211,
            211, 211, -1, 211, 211, -1, -1, 211,
            211, 211, 211, 211, -1, 211, 211, -1 ),
        array( -1, -1, -1, -1, -1, -1, 269, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 269, 269, 269, 269, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 269, 269, 269, 269, 269, 269, 269,
            269, 269, -1, 269, 269, -1, -1, 269,
            269, 269, 269, 269, -1, 269, 269, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 323, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 323, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 151, -1, -1, 151, 162,
            -1, -1, 163, -1, -1, -1, -1, -1,
            164, 151, 151, 151, 151, -1, -1, -1,
            165, -1, 151, -1, -1, -1, -1, -1,
            -1, 151, 151, 151, 151, 151, 151, 151,
            151, 151, 151, 151, 173, 18, 19, 151,
            151, 151, 151, 151, -1, 151, 151, 151 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 332, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 151, -1, -1, 151, 162,
            -1, -1, 163, -1, -1, -1, -1, -1,
            164, 151, 151, 151, 151, -1, -1, -1,
            165, -1, 151, -1, -1, -1, -1, -1,
            -1, 151, 151, 151, 151, 151, 151, 151,
            151, 151, 151, 151, 151, 18, 19, 151,
            151, 182, 151, 151, -1, 151, 151, 151 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 335, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 151, -1, -1, 151, 162,
            -1, -1, 163, -1, -1, -1, -1, -1,
            164, 151, 151, 151, 151, -1, -1, -1,
            165, -1, 151, -1, -1, -1, -1, -1,
            -1, 151, 151, 151, 151, 151, 151, 151,
            151, 151, 151, 151, 151, 18, 19, 151,
            151, 151, 151, 199, -1, 151, 151, 151 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 338, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 340, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 342, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 344, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 346, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 348, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 350, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 352, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 354, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 356, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 151, -1, -1, 151, 162,
            -1, -1, 163, -1, -1, -1, -1, -1,
            164, 151, 151, 151, 151, -1, -1, -1,
            165, -1, 151, -1, -1, -1, -1, -1,
            -1, 151, 151, 151, 151, 151, 151, 151,
            151, 151, 151, 151, 151, 18, 19, 151,
            151, 151, 151, 151, -1, 331, 361, 151 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 333, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 345, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 233 ),
        array( -1, -1, -1, 151, -1, -1, 151, 162,
            -1, -1, 163, -1, -1, -1, -1, -1,
            164, 151, 151, 151, 151, -1, -1, -1,
            165, -1, 151, -1, -1, -1, -1, -1,
            -1, 151, 334, 151, 151, 151, 151, 151,
            151, 151, 151, 151, 151, 18, 19, 151,
            151, 151, 151, 151, -1, 151, 151, 151 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 336, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 151, -1, -1, 151, 162,
            -1, -1, 163, -1, -1, -1, -1, -1,
            164, 151, 151, 151, 151, -1, -1, -1,
            165, -1, 151, -1, -1, -1, -1, -1,
            -1, 151, 151, 337, 151, 151, 151, 151,
            151, 151, 151, 151, 151, 18, 19, 151,
            151, 151, 151, 151, -1, 151, 151, 151 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 339, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 341, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 347, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 349, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 351, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 353, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 355, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 357, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1 ),
        array( -1, -1, -1, 151, -1, -1, 151, 162,
            -1, -1, 163, -1, -1, -1, -1, -1,
            164, 151, 151, 151, 151, -1, -1, -1,
            165, -1, 151, -1, -1, -1, -1, -1,
            -1, 151, 151, 151, 151, 151, 151, 151,
            151, 151, 151, 151, 151, 18, 19, 151,
            151, 151, 363, 151, -1, 151, 151, 151 ),
        array( -1, -1, -1, 151, -1, -1, 151, 162,
            -1, -1, 163, -1, -1, -1, -1, -1,
            164, 151, 151, 151, 151, -1, -1, -1,
            165, -1, 151, -1, -1, -1, -1, -1,
            -1, 151, 151, 151, 151, 151, 151, 151,
            151, 151, 151, 151, 151, 18, 19, 151,
            151, 372, 151, 151, -1, 151, 151, 151 ),
        array( -1, -1, -1, 151, -1, -1, 151, 162,
            -1, -1, 163, -1, -1, -1, -1, -1,
            164, 151, 151, 151, 151, -1, -1, -1,
            165, -1, 151, -1, -1, -1, -1, -1,
            -1, 151, 151, 151, 373, 151, 151, 151,
            151, 151, 151, 151, 151, 18, 19, 151,
            151, 151, 151, 151, -1, 151, 151, 151 ),
        array( -1, -1, -1, 151, -1, -1, 151, 162,
            -1, -1, 163, -1, -1, -1, -1, -1,
            164, 151, 151, 151, 151, -1, -1, -1,
            165, -1, 151, -1, -1, -1, -1, -1,
            -1, 151, 151, 151, 151, 151, 151, 151,
            151, 151, 151, 151, 151, 18, 19, 151,
            374, 151, 151, 151, -1, 151, 151, 151 )
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
                       if ($yy_last_accept_state < 376) {
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
    $this->value = $this->createToken('Text',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 4:
{
    // &abc;
    $this->value = $this->createToken('Text',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 5:
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
case 6:
{  
    // <> -- empty start tag */
    if ($this->options['ignore_html']) {
        return $this->returnSimple();
    }
    return $this->raiseError("empty tag"); 
}
case 7:
{ 
    /* <? php start.. */
    //echo "STARTING PHP?\n";
    $this->yyPhpBegin = $this->yy_buffer_start;
    $this->yybegin(IN_PHP);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 8:
{
    // &#123;
    $this->value = $this->createToken('Text',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 9:
{
    // &#abc;
    $this->value = $this->createToken('Text',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 10:
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
case 11:
{
    /* </> -- empty end tag */  
    if ($this->options['ignore_html']) {
        return $this->returnSimple();
    }
    return $this->raiseError("empty end tag not handled");
}
case 12:
{
    /* <!DOCTYPE -- markup declaration */
    if ($this->options['ignore_html']) {
        return $this->returnSimple();
    }
    $this->value = $this->createToken('Doctype',$this->yytext(),$this->yyline);
    $this->yybegin(IN_MD);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 13:
{
    /* <!> */
    if ($this->options['ignore_html']) {
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
case 16:
{
    $this->value = $this->createToken('GetTextEnd','',$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 17:
{ 
    /* ]]> -- marked section end */
    return $this->returnSimple();
}
case 18:
{
    $t =  $this->yytext();
    $t = substr($t,1,-1);
    $this->value = $this->createToken('Var'  , $t, $this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 19:
{
    $this->value =  '';
    $this->flexyMethod = substr($this->yytext(),1,-1);
    $this->flexyArgs = array();
    $this->yybegin(IN_FLEXYMETHOD);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 20:
{
    $this->value = $this->createToken('GetTextStart','',$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 21:
{
    if ($this->options['ignore_html']) {
        return $this->returnSimple();
    }
    /* </name <  -- unclosed end tag */
    return $this->raiseError("Unclosed  end tag");
}
case 22:
{
    /* <!--  -- comment declaration */
    if ($this->options['ignore_html']) {
        return $this->returnSimple();
    }
    if ($this->inStyle) {
        $this->value = $this->createToken('Text',$this->yytext(),$this->yyline);
        $this->yybegin(IN_COMSTYLE);
        return HTML_TEMPLATE_FLEXY_TOKEN_OK;
    }
    $this->yyCommentBegin = $this->yy_buffer_end;
    //$this->value = $this->createToken('Comment',$this->yytext(),$this->yyline);
    $this->yybegin(IN_COM);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 23:
{
    $this->value = $this->createToken('If',substr($this->yytext(),4,-1),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 24:
{
    $this->value =  '';
    $this->flexyMethod = substr($this->yytext(),1,-1);
    $this->flexyArgs = array();
    $this->yybegin(IN_FLEXYMETHOD);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 25:
{
    $this->value = $this->createToken('End', '',$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 26:
{
    $this->value = $this->createToken('Else', '',$this->yyline);
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
    $this->value = $this->createToken('Foreach', explode(',',substr($this->yytext(),9,-1)),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 30:
{
    $this->value = $this->createToken('Foreach',  explode(',',substr($this->yytext(),9,-1)),$this->yyline);
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
    $this->attrVal[] = $this->createToken('Var'  , $n, $this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 34:
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
    $this->value = $this->createToken($this->tokenName,
        array($this->tagName,$this->attributes),
        $this->yyline);
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
case 38:
{
    // <img src="xxx" ...ismap...> the ismap */
    $this->attributes[trim($this->yytext())] = true;
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 39:
{
    // <em^/ -- NET tag */
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
    // <em^/ -- NET tag */
    $this->attributes["/"] = true;
    $this->value = $this->createToken($this->tokenName,
        array($this->tagName,$this->attributes),
        $this->yyline);
    $this->yybegin(YYINITIAL);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 42:
{
    // <em^/ -- NET tag */
    $this->attributes["?"] = true;
    $this->value = $this->createToken($this->tokenName,
        array($this->tagName,$this->attributes),
        $this->yyline);
    $this->yybegin(YYINITIAL);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 43:
{
    // <a href = ^http://foo/> -- unquoted literal HACK */                          
    $this->attributes[$this->attrKey] = trim($this->yytext());
    $this->yybegin(IN_ATTR);
    //   $this->raiseError("attribute value needs quotes");
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 44:
{
    // <a name = ^12pt> -- number token */
    $this->attributes[$this->attrKey] = trim($this->yytext());
    $this->yybegin(IN_ATTR);
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 45:
{
    // <em^/ -- NET tag */
    return $this->raiseError("attribute value missing"); 
}
case 46:
{ 
    return $this->raiseError("Tag close found where attribute value expected"); 
}
case 47:
{
	//echo "STARTING SINGLEQUOTE";
    $this->attrVal = array( "'");
    $this->yybegin(IN_SINGLEQUOTE);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 48:
{
    //echo "START QUOTE";
    $this->attrVal =array("\"");
    $this->yybegin(IN_DOUBLEQUOTE);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 49:
{ 
    // whitespace switch back to IN_ATTR MODE.
    $this->value = '';
    $this->yybegin(IN_ATTR);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 50:
{ 
    return $this->raiseError("extraneous character in end tag"); 
}
case 51:
{ 
    $this->value = $this->createToken($this->tokenName,
        array($this->tagName),
        $this->yyline);
        array($this->tagName);
    $this->yybegin(YYINITIAL);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 52:
{
    //echo "GOT DATA:".$this->yytext();
    $this->attrVal[] = $this->yytext();
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 53:
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
case 54:
{ 
    $this->value = $this->createToken('WhiteSpace',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 55:
{
    return $this->raiseError("illegal character in markup declaration");
}
case 56:
{   
    $this->value = $this->createToken('Number',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 57:
{ 
    $this->value = $this->createToken('Name',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 58:
{ 
    $this->value = $this->createToken('NameT',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 59:
{   
    $this->value = $this->createToken('CloseTag',$this->yytext(),$this->yyline);
    $this->yybegin(YYINITIAL); 
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 60:
{
    // <!doctype foo ^[  -- declaration subset */
    $this->value = $this->createToken('BeginDS',$this->yytext(),$this->yyline);
    $this->yybegin(IN_DS);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 61:
{ 
    $this->value = $this->createToken('NumberT',$this->yytext(),$this->yyline);    
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 62:
{
    // <!entity ^% foo system "..." ...> -- parameter entity definition */
    $this->value = $this->createToken('EntityPar',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 63:
{
    // <!doctype ^%foo;> -- parameter entity reference */
    $this->value = $this->createToken('EntityRef',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 64:
{ 
    $this->value = $this->createToken('Literal',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 65:
{
    // inside a comment (not - or not --
    // <!^--...-->   -- comment */   
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 66:
{
	// inside comment -- without a >
	return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 67:
{   
    $this->value = $this->createToken('Comment',
        '<!--'. substr($this->yy_buffer,$this->yyCommentBegin ,$this->yy_buffer_end - $this->yyCommentBegin),
        $this->yyline
    );
    $this->yybegin(YYINITIAL); 
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 68:
{ 
    $this->value = $this->createToken('Declaration',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 69:
{ 
    // ] -- declaration subset close */
    $this->value = $this->createToken('DSEndSubset',$this->yytext(),$this->yyline);
    $this->yybegin(IN_DSCOM); 
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 70:
{
    // ]]> -- marked section end */
     $this->value = $this->createToken('DSEnd',$this->yytext(),$this->yyline);
    $this->yybegin(YYINITIAL);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 71:
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
case 72:
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
    $this->value = $this->createToken('Method'  , array($this->flexyMethod,$this->flexyArgs), $this->yyline);
    $this->yybegin(YYINITIAL);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 73:
{
    $t = $this->yytext();
    if ($t{1} == ':') {
        $this->flexyMethod .= substr($t,1,-1);
    }
    $this->value = $this->createToken('Method'  , array($this->flexyMethod,$this->flexyArgs), $this->yyline);
    $this->yybegin(YYINITIAL);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 74:
{
    $t = $this->yytext();
    // add argument
    $this->flexyArgs[] = $t;
    $this->yybegin(IN_FLEXYMETHODQUOTED_END);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 75:
{
    $t = $this->yytext();
    $this->flexyArgs[] =$t;
    $this->yybegin(IN_FLEXYMETHODQUOTED_END);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 76:
{
    $t = $this->yytext();
    if ($p = strpos($t,':')) {
        $this->flexyMethod .= substr($t,$p,2);
    }
    $this->attrVal[] = $this->createToken('Method'  , array($this->flexyMethod,$this->flexyArgs), $this->yyline);    
    $this->yybegin($this->flexyMethodState);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 77:
{
    $this->yybegin(IN_FLEXYMETHODQUOTED);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 78:
{
    // general text in script..
    $this->value = $this->createToken('Text',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 79:
{
    // just < .. 
    $this->value = $this->createToken('Text',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 80:
{
    // </script>
    $this->value = $this->createToken('EndTag',
        array('/script'),
        $this->yyline);
    $this->yybegin(YYINITIAL);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 81:
{ 
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 82:
{ 
    /* ]]> -- marked section end */
    $this->value = $this->createToken('Cdata',
        substr($this->yy_buffer,$this->yyCdataBegin ,$this->yy_buffer_end - $this->yyCdataBegin - 3 ),
        $this->yyline);
    $this->yybegin(YYINITIAL);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 83:
{
    // inside a comment (not - or not --
    // <!^--...-->   -- comment */   
    $this->value = $this->createToken('DSComment',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 84:
{   
    $this->value = $this->createToken('DSEnd', $this->yytext(),$this->yyline);
    $this->yybegin(YYINITIAL); 
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 85:
{     
    /* anything inside of php tags */
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 86:
{ 
    /* php end */
    if ($this->options['ignore_php']) {
        $this->yybegin(YYINITIAL);
        return HTML_TEMPLATE_FLEXY_TOKEN_NONE;    
    }
    $this->value = $this->createToken('Php',
        substr($this->yy_buffer,$this->yyPhpBegin ,$this->yy_buffer_end - $this->yyPhpBegin ),
        $this->yyline);
    $this->yybegin(YYINITIAL);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 87:
{
    // inside a style comment (not - or not --
    // <!^--...-->   -- comment */   
    $this->value = $this->createToken('Comment', $this->yytext(),$this->yyline);
	return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 88:
{
	// inside style comment -- without a >
    $this->value = $this->createToken('Comment', $this->yytext(),$this->yyline);
	return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 89:
{   
    // --> inside a style tag.
    $this->value = $this->createToken('Comment',  $this->yytext(),$this->yyline);
    $this->yybegin(YYINITIAL); 
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 90:
{
    // var in commented out style bit..
    $t =  $this->yytext();
    $t = substr($t,1,-1);
    $this->value = $this->createToken('Var'  , $t, $this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 92:
{
    return $this->raiseError("unexpected something: (".$this->yytext() .") character: 0x" . dechex(ord($this->yytext())));
}
case 93:
{
    //abcd -- data characters  
    // { and ) added for flexy
    $this->value = $this->createToken('Text',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 94:
{
    // &abc;
    $this->value = $this->createToken('Text',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 95:
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
case 96:
{ 
    /* <? php start.. */
    //echo "STARTING PHP?\n";
    $this->yyPhpBegin = $this->yy_buffer_start;
    $this->yybegin(IN_PHP);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 97:
{
    // &#123;
    $this->value = $this->createToken('Text',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 98:
{
    // &#abc;
    $this->value = $this->createToken('Text',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 99:
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
case 100:
{
    /* <!DOCTYPE -- markup declaration */
    if ($this->options['ignore_html']) {
        return $this->returnSimple();
    }
    $this->value = $this->createToken('Doctype',$this->yytext(),$this->yyline);
    $this->yybegin(IN_MD);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 101:
{
    /* <![ -- marked section */
    return $this->returnSimple();
}
case 102:
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
case 103:
{
    $this->attrVal[] = $this->yytext();
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 104:
{
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 105:
{
    // <foo^<bar> -- unclosed start tag */
    return $this->raiseError("Unclosed tags not supported"); 
}
case 106:
{
    // <img src="xxx" ...ismap...> the ismap */
    $this->attributes[trim($this->yytext())] = true;
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 107:
{
    // <a href = ^http://foo/> -- unquoted literal HACK */                          
    $this->attributes[$this->attrKey] = trim($this->yytext());
    $this->yybegin(IN_ATTR);
    //   $this->raiseError("attribute value needs quotes");
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 108:
{
    // <a name = ^12pt> -- number token */
    $this->attributes[$this->attrKey] = trim($this->yytext());
    $this->yybegin(IN_ATTR);
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 109:
{
    //echo "GOT DATA:".$this->yytext();
    $this->attrVal[] = $this->yytext();
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 110:
{ 
    $this->value = $this->createToken('WhiteSpace',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 111:
{
    return $this->raiseError("illegal character in markup declaration");
}
case 112:
{   
    $this->value = $this->createToken('Number',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 113:
{ 
    $this->value = $this->createToken('Name',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 114:
{ 
    $this->value = $this->createToken('NameT',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 115:
{ 
    $this->value = $this->createToken('NumberT',$this->yytext(),$this->yyline);    
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 116:
{
    // <!doctype ^%foo;> -- parameter entity reference */
    $this->value = $this->createToken('EntityRef',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 117:
{ 
    $this->value = $this->createToken('Literal',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK; 
}
case 118:
{
    // inside a comment (not - or not --
    // <!^--...-->   -- comment */   
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 119:
{
	// inside comment -- without a >
	return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 120:
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
case 121:
{
    $t = $this->yytext();
    // add argument
    $this->flexyArgs[] = $t;
    $this->yybegin(IN_FLEXYMETHODQUOTED_END);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 122:
{ 
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 123:
{
    // inside a comment (not - or not --
    // <!^--...-->   -- comment */   
    $this->value = $this->createToken('DSComment',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 124:
{     
    /* anything inside of php tags */
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 125:
{
    // inside a style comment (not - or not --
    // <!^--...-->   -- comment */   
    $this->value = $this->createToken('Comment', $this->yytext(),$this->yyline);
	return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 126:
{
	// inside style comment -- without a >
    $this->value = $this->createToken('Comment', $this->yytext(),$this->yyline);
	return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 128:
{
    return $this->raiseError("unexpected something: (".$this->yytext() .") character: 0x" . dechex(ord($this->yytext())));
}
case 129:
{
    //abcd -- data characters  
    // { and ) added for flexy
    $this->value = $this->createToken('Text',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 130:
{
    $this->attrVal[] = $this->yytext();
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 131:
{
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 132:
{
    // <a name = ^12pt> -- number token */
    $this->attributes[$this->attrKey] = trim($this->yytext());
    $this->yybegin(IN_ATTR);
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 133:
{
    //echo "GOT DATA:".$this->yytext();
    $this->attrVal[] = $this->yytext();
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 134:
{
    return $this->raiseError("illegal character in markup declaration");
}
case 135:
{ 
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 136:
{     
    /* anything inside of php tags */
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 137:
{
    // inside a style comment (not - or not --
    // <!^--...-->   -- comment */   
    $this->value = $this->createToken('Comment', $this->yytext(),$this->yyline);
	return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 139:
{
    return $this->raiseError("unexpected something: (".$this->yytext() .") character: 0x" . dechex(ord($this->yytext())));
}
case 140:
{
    //abcd -- data characters  
    // { and ) added for flexy
    $this->value = $this->createToken('Text',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 141:
{
    return $this->raiseError("illegal character in markup declaration");
}
case 143:
{
    return $this->raiseError("unexpected something: (".$this->yytext() .") character: 0x" . dechex(ord($this->yytext())));
}
case 144:
{
    return $this->raiseError("illegal character in markup declaration");
}
case 146:
{
    return $this->raiseError("unexpected something: (".$this->yytext() .") character: 0x" . dechex(ord($this->yytext())));
}
case 147:
{
    return $this->raiseError("illegal character in markup declaration");
}
case 149:
{
    return $this->raiseError("unexpected something: (".$this->yytext() .") character: 0x" . dechex(ord($this->yytext())));
}
case 150:
{
    return $this->raiseError("illegal character in markup declaration");
}
case 152:
{
    return $this->raiseError("unexpected something: (".$this->yytext() .") character: 0x" . dechex(ord($this->yytext())));
}
case 153:
{
    return $this->raiseError("illegal character in markup declaration");
}
case 155:
{
    return $this->raiseError("unexpected something: (".$this->yytext() .") character: 0x" . dechex(ord($this->yytext())));
}
case 157:
{
    return $this->raiseError("unexpected something: (".$this->yytext() .") character: 0x" . dechex(ord($this->yytext())));
}
case 159:
{
    return $this->raiseError("unexpected something: (".$this->yytext() .") character: 0x" . dechex(ord($this->yytext())));
}
case 161:
{
    return $this->raiseError("unexpected something: (".$this->yytext() .") character: 0x" . dechex(ord($this->yytext())));
}
case 320:
{
    //abcd -- data characters  
    // { and ) added for flexy
    $this->value = $this->createToken('Text',$this->yytext(),$this->yyline);
    return HTML_TEMPLATE_FLEXY_TOKEN_OK;
}
case 321:
{
    // <a name = ^12pt> -- number token */
    $this->attributes[$this->attrKey] = trim($this->yytext());
    $this->yybegin(IN_ATTR);
    $this->value = '';
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 322:
{
    $t = $this->yytext();
    // add argument
    $this->flexyArgs[] = $t;
    $this->yybegin(IN_FLEXYMETHODQUOTED_END);
    return HTML_TEMPLATE_FLEXY_TOKEN_NONE;
}
case 323:
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
