<?

/**
* The original SimpleTemplate emulation filter
* NOT DOCUMENTED = as it's depreciated.
*   @package    Template_Flexy
*
*/
 

class Template_Flexy_Filter_Tags {
    var $engine; // the engine (with options)

    function Filter_tags (&$engine) {
        $this->engine = &$engine;
    }

    function variables ($input) {
        

        /*
        // variables 
        //mega simple templating engine.....
        //user sees : <?=$xyz.abc.yx?>
        //&lt;?=([^\s:]+)?&gt;  -> <?=htmlspecialchars(".str_replace(".","->",$1).")?>
        */
        $input = preg_replace(
            "/&lt;\?=([^\s:]+)\?&gt;/e",
            "'<?=htmlspecialchars('.str_replace('.','->','\\1').')?>'",
            $input);
            
        
        /*
        
        user sees : <?=$xyz.abc.yx:h?>
        &lt;?=([^\s:]+):h?&gt;  -> <?=".str_replace(".","->",$1)."?>
        */
        $input = preg_replace(
            "/&lt;\?=([^\s:]+)\:h?&gt;/e",
            "'<?='.str_replace('.','->','\\1').'?>'",
            $input);
        /*
                
        user sees : <?=$xyz.abc.yx:u?>
        &lt;?=([^\s:]+)?&gt;  -> <?=urlencode(".str_replace(".","->",$1).")?>
        */
        $input = preg_replace(
            "/&lt;\?=([^\s:]+):u\?&gt;/e",
            "'<?=urlencode('.str_replace('.','->','\\1').')?>'",
            $input);
            
        return $input;
    }
    
    function foreach_loop($input) {
    
        /*
        user sees : <?foreach($abc.xxxx,$somevar){?>
        // foreach loops
         -> <? if (" . str_replace(".","->",$1).  ") foreach( " . str_replace(".","->",$1) . " as " . str_replace(".","->",$2) . ") { ?>"
         */

        $input = preg_replace(
            "/&lt;\?foreach\(([^,\s]+),([^)\s]+)\){\?&gt;/e",
            "'<? if (' . str_replace('.','->',\\1) . ') foreach( ' . str_replace('.','->','\\1') . ' as ' . str_replace('.','->','\\2') . ') { ?>'",
            $input);
        return $input;
    }
    
    function end_loop($input) {
    
        /*
        // end loops
        user sees : <?}?>
        &lt;?&gt; -> <?}?>
        */
        $input = str_replace("&lt;?&gt;",'<?}?>', $input);
        return $input;
        
    }    
    
    function simple_conditional($input) {
    
        /*
        // conditionals BOOLEAN ONLY
        user sees: <?if($abc.xyz.zzz){?>
        &lt;?if\(([^\)\s]+)\){?&gt; -> <?if( " . str_replace(".","->",$1) . ") { ?>"
        */
        $input = preg_replace(
            "/&lt;\?if\(([^)\s]+)\){\?&gt;/e",
            "'<? if (' . str_replace('.','->',\\1) . ') { ?>'",
            $input);
        return $input;
    }
    
    
    function method_conditional($input) {
    
        /*
         // conditional method calls BOOLEAN ONLY

        user sees: <?if($abc.xyz.zzz()){?>
        &lt;?if\(([^\(\s]+)\(\)\){?&gt; -> <?if( " . str_replace(".","->",$1) . "()) { ?>"
       */
        $input = preg_replace(
            "/&lt;\?if\(([^(\s]+)\(\)\){\?&gt;/e",
            "'<? if (' . str_replace('.','->',\\1) . '()) { ?>'",
            $input);
        return $input;
    }
    
    function include_template($input) {
        /*
        // include blocks
        user sees <?include(abcdef_ssss)?>
        &lt;?include([a-z_]+)?&gt; -> <? include('".$compiled_templates_dir . "....
        */
        $input = preg_replace(
            "/&lt;\?include\(([a-z0-9]+)\(\)\){\?&gt;/",
            "'<? include('" .  $this->engine->getOption('compileDir') ."\\1');?>'",
            $input);
        
    }





}