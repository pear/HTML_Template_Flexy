<PRE>html_template_flexy_token_tag Object
(
    [token] => Tag
    [value] => 
    [line] => 118
    [close] => 
    [children] => Array
        (
        )

    [ignoreChildren] => 
    [tag] => INPUT
    [oTag] => input
    [attributes] => Array
        (
            [name] => test123a
            [type] => 'hidden'
            [value] => '123'
        )

    [ucAttributes] => Array
        (
            [NAME] => test123a
            [TYPE] => 'hidden'
            [VALUE] => '123'
        )

    [postfix] => 
    [prefix] => 
    [foreach] => 
    [startChildren] => 
    [id] => 229
)
</PRE>
Notice: Undefined index:  filename in /usr/src/php/pear/HTML_Template_Flexy/Flexy/Token/Tag.php on line 405

Call Stack:
    0.0000     111728   1. {main}() /usr/src/php/pear/HTML_Template_Flexy/Flexy/Test.php:0
    0.0983    1383336   2. html_template_flexy_test->html_template_flexy_test() /usr/src/php/pear/HTML_Template_Flexy/Flexy/Test.php:116
    0.0988    1384968   3. html_template_flexy_test->parse() /usr/src/php/pear/HTML_Template_Flexy/Flexy/Test.php:43
    4.3034    1969896   4. html_template_flexy_token->tostring() /usr/src/php/pear/HTML_Template_Flexy/Flexy/Test.php:91
    4.3034    1969896   5. html_template_flexy_token->childrentostring() /usr/src/php/pear/HTML_Template_Flexy/Flexy/Token.php:132
    4.3151    1969984   6. html_template_flexy_token_tag->tostring() /usr/src/php/pear/HTML_Template_Flexy/Flexy/Token.php:160
    4.3161    1970016   7. html_template_flexy_token_tag->childrentostring() /usr/src/php/pear/HTML_Template_Flexy/Flexy/Token/Tag.php:227
    4.3224    1970208   8. html_template_flexy_token_tag->tostring() /usr/src/php/pear/HTML_Template_Flexy/Flexy/Token.php:160
    4.3233    1970240   9. html_template_flexy_token_tag->childrentostring() /usr/src/php/pear/HTML_Template_Flexy/Flexy/Token/Tag.php:227
    4.5551    1978488  10. html_template_flexy_token_tag->tostring() /usr/src/php/pear/HTML_Template_Flexy/Flexy/Token.php:160
    4.5556    1978488  11. html_template_flexy_token_tag->parsetags() /usr/src/php/pear/HTML_Template_Flexy/Flexy/Token/Tag.php:165
    4.5557    1978520  12. html_template_flexy_token_tag->parsetagform() /usr/src/php/pear/HTML_Template_Flexy/Flexy/Token/Tag.php:381
    4.5565    1978696  13. html_template_flexy_token_tag->childrentostring() /usr/src/php/pear/HTML_Template_Flexy/Flexy/Token/Tag.php:520
    4.5618    1980304  14. html_template_flexy_token_tag->tostring() /usr/src/php/pear/HTML_Template_Flexy/Flexy/Token.php:160
    4.5623    1980304  15. html_template_flexy_token_tag->parsetags() /usr/src/php/pear/HTML_Template_Flexy/Flexy/Token/Tag.php:165
    4.5624    1980576  16. html_template_flexy_token_tag->parsetaginput() /usr/src/php/pear/HTML_Template_Flexy/Flexy/Token/Tag.php:381
    4.5627    1980576  17. html_template_flexy_token_tag->aselement() /usr/src/php/pear/HTML_Template_Flexy/Flexy/Token/Tag.php:462
Error: on Line 118 &lt;INPUT&gt;: 
             Dynamic tags have already used ID test123a
