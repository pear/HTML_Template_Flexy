<?php

/*

usage : 

$t = new HTML_Template_Flexy_Translator(array(
    'baseLang'      => 'en',
    'targetLangs'   => array('es','fr','zh'),

));
$t->process($_GET['lang'],isset($_POST ? $_POST : array()); // read data.. etc.

print_R($t);

*/

class HTML_Template_Flexy_Translator {

    var $options = array(
        'baseLang'  => 'en',
        'targetLangs' => array('fr'),
        'templateDir' => '',
        'compileDir'  => '',
        'url_rewrite'     => '', // for image rewriting.. -- needs better thinking through!
        'appURL'            => '',
    );
    var $appURL;
    var $languages = array();
    
    function HTML_Template_Flexy_Translator($options) {
        foreach($options as $k=>$v) {
            $this->options[$k]  = $v;
        }
        if (!in_array($this->options['baseLang'], $this->options['targetLangs'])) {
            $this->options['targetLangs'][] = $this->options['baseLang'];
        }
        $o = PEAR::getStaticProperty('HTML_Template_Flexy','options');
        if (!strlen($this->options['templateDir'])) {
            $this->options['templateDir'] = $o['templateDir'];
        }
        if (!strlen($this->options['compileDir'])) {
            $this->options['compileDir'] = $o['compileDir'];
        }
        if (!strlen($this->options['url_rewrite'])) {
            $this->options['url_rewrite'] = $o['url_rewrite'];
        }
        $this->appURL = $this->options['appURL'];
        $this->languages = $this->options['targetLangs'];
    }
    
    
    
    
    
    var $template = 'translate.tpl.html';
    var $words= array(); // parsed from templates.
    var $status = array();
    
    var $translate = ''; // language being displayed /edited.
    
    function process($get,$post) {
        //DB_DataObject::debugLevel(1);
        $displayLang = isset($get['translate']) ? $get['translate'] : 
            (isset($post['translate']) ? $post['translate'] : false);
            
        if ($displayLang === false) {
          
            return;
        }
        require_once 'Translation2/Admin.php';
        $trd = &new Translation2_Admin('dataobjectsimple', 'translations' );
        //$trd->setDecoratedLang('en');
        foreach($this->options['targetLangs'] as $l) {
            $trd->createNewLang(array('lang_id'=>$l));
        }
        
        // back to parent if no language selected..
        
        if (!in_array($displayLang, $this->options['targetLangs'] )) {
            require_once 'PEAR.php';
            return PEAR::raiseError('Unknown Language :' .$displayLang);
        }
        
        $this->translate = $displayLang;
        
        
        if (isset($post['_apply'])) {
            $this->clearTemplateCache($displayLang);
             
        }
        
        require_once 'Translation2.php';
        $tr = &new Translation2('dataobjectsimple','translations');
        $tr->setLang($displayLang);
        
        $suggestions = &new Translation2('dataobjectsimple','translations');
        $suggestions->setLang($displayLang);
        $this->compileAll();
        //$tr->setPageID('test.html');
        // delete them after we have compiled them!!
        if (isset($post['_apply'])) {
            $this->clearTemplateCache($displayLang);
        }
        
        
        $all = array();
        foreach($this->words as $page=>$words) {
            $status[$page] = array();
            $tr->setPageID($page);
            // pages....
            
            foreach ($words as $word) {
            
                if (!trim(strlen($word))) { 
                    continue;
                }
                
                $md5 = md5($page.':'.$word);
                
                $value = $tr->get($word);
                // we posted something..
                if (isset($post[$displayLang][$md5])) {
                    $nval = get_magic_quotes_gpc() ? stripslashes($post[$displayLang][$md5]) : $post[$displayLang][$md5];
                    
                    if ($value != $nval) {
                    
                        $trd->add($word,$page,array($displayLang=>$nval));
                        $value = $nval;
                    }
                }
                
                if ($value == '') {
                    // try the old gettext...
                    if (isset($old[addslashes($word)])) {
                        $trd->add($word,$page,array($displayLang=>$old[addslashes($word)]));
                        $value = $old[addslashes($word)];
                    }
                
                
                }
                
                $add = new StdClass;
                 
                $add->from = $word;
                $add->to   = $value;
                if (!$add->to || ($add->from == $add->to)) {
                    $add->untranslated = true;
                    $suggest = $suggestions->get($word);
                    if ($suggest && ($suggest  != $word)) {
                        $add->suggest = $suggestions->get($word);
                    }
                }

                $add->md5 = $md5;
                $add->short = (bool) (strlen($add->from) < 30);
                $status[$page][] = $add;
            
                 
            }
            
        }
        
        $this->status = $status;
          
             
    
    }
    
   
    
    


    function compileAll($d='') {
        set_time_limit(0); // this could take quite a while!!!
        
        $words = array();
        $dname = $d ? $this->options['templateDir'] .'/'.$d  : $this->options['templateDir'];
        //echo "Open $dname<BR>";
        $dh = opendir( $dname);
        require_once 'HTML/Template/Flexy.php';
        $o = $this->options;
        $o['fatalError'] = PEAR_ERROR_RETURN;
        $o['locale'] = 'en';
        while (($name = readdir($dh)) !== false) {
            $fname = $d ? $d .'/'. $name : $name;
            
            if ($name{0} == '.') {
                continue;
            }
            
            if (is_dir($this->options['templateDir'] . '/'. $fname)) {
                $this->compileAll($fname);
                continue;
            }
                
                
            if (!preg_match('/\.html$/',$name)) {
                continue;
            }
             
            $x = new HTML_Template_Flexy( $o );
            $r = $x->compile($fname);
            if (is_a($r,'PEAR_Error')) {
                echo "compile failed on $fname<BR>";
                echo $r->toString();
                continue;
            }
            $this->words[$fname] = file_exists($x->getTextStringsFile) ?
                unserialize(file_get_contents($x->getTextStringsFile)) :
                array();
        }
        //echo '<PRE>';print_R($words);exit;
        
        ksort($this->words);
    }


    function clearTemplateCache($lang='en',$d = '') {
        
        $dname = $d ? $this->options['templateDir'] .'/'.$d  : $this->options['templateDir'];
       
        $dh = opendir($dname);
        while (($name = readdir($dh)) !== false) {
            $fname = $d ? $d .'/'. $name : $name;
            
            if ($name{0} == '.') {
                continue;
            }
            
            if (is_dir($this->options['templateDir'] . '/'. $fname)) {
                $this->clearTemplateCache($lang,$fname);
                continue;
            }
            if (!preg_match('/\.html$/',$name)) {
                continue;
            }
      
            $file = "{$this->options['compileDir']}/{$fname}.{$lang}.php";
            
            if (file_exists($file)) {
               // echo "DELETE $file?";
                unlink($file);
            }
        }
        clearstatcache();
    }

    function outputDefaultTemplate() {
        $o = array(
            'compileDir' => ini_get('session.save_path') . '/HTML_Template_Flexy_Translate',
            'templateDir' => dirname(__FILE__).'/templates'
        );
        $x = new HTML_Template_Flexy( $o );
        $x->compile('translator.html');
        $x->outputObject($this);
    }
        
      

}