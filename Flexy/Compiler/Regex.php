<?php

class HTML_Template_Flexy_Compiler_Regex {

    function compile()
    {
        // read the entire file into one variable
        if ( $input = @file($this->currentTemplate) ) {
            $fileContent = implode( '' , $input );
        } else {
            $fileContent = '';                      // if the file doesnt exist, write a template anyway, an empty one but write one
        }
        
         
        //  apply pre filter
        $fileContent = $this->applyFilters( $fileContent , "/^pre_/i" );
        $fileContent = $this->applyFilters( $fileContent , "/^(pre_|post_)/i",TRUE);
        $fileContent = $this->applyFilters( $fileContent , "/^post_/i" );
        // write the compiled template into the compiledTemplate-File
        if( ($cfp = fopen( $this->compiledTemplate , 'w' )) ) {
            fwrite($cfp,$fileContent);
            fclose($cfp);
            @chmod($this->compiledTemplate,0775);
        }

        return true;
    }
}
?>
