<?php

require_once(HELPDEZK_PATH . '/cron/cronCommon.php');

class cronLanguageSetup extends cronCommon {
    /**
     * Create an instance, check session time
     *
     * @access public
     */
    public function __construct()
    {

        parent::__construct();
        error_reporting(E_ERROR|E_PARSE);
        // Log settings
        $this->log = parent::$_logStatus;
        $this->program  = basename( __FILE__ );

        $this->loadModel('admin/home_model');
        $this->dbHome = new home_model();

        $this->modulename = 'Admin' ;
        $this->idmodule =  $this->getIdModule($this->modulename);

    }

    public function updateLanguageFile()
    {
        if (substr(php_sapi_name(), 0, 3) != 'cli') { 
            $this->logIt("This program runs only in the command line - program: ".$this->program ,6,'general');
            exit;
        }
        error_reporting(0);

        $this->logIt("Start ". __METHOD__ ." at ".date("d/m/Y H:i:s").'. Program: '.$this->program ,6,'general'); 

        $arrayLanguage = $this->dbHome->getLanguages();
        if (!$arrayLanguage['success']) {
            $this->logIt($arrayLanguage['message'] . ' - program: ' . $this->program . ' - method: ' . __METHOD__, 3, 'general', __LINE__);
            exit;
        }

        $first = true ;
        $rsLanguage = $arrayLanguage['id'];

        while (!$rsLanguage->EOF) {
            if ($first) {
                $first = false ;
                $localeTest = $rsLanguage->fields['locale_name'];
                $file = $this->_getHelpdezkPath() . '/app/lang/' . $rsLanguage->fields['locale_name'] . '.txt' ;
                file_put_contents( $file , '# --- Generated at ' . date("F j, Y, g:i a") . ' --- ' . PHP_EOL, LOCK_EX);
            }

            $localeName = $rsLanguage->fields['locale_name'];

            if($localeTest != $localeName) {
                $localeTest = $rsLanguage->fields['locale_name'];
                $file = $this->_getHelpdezkPath() . '/app/lang/' . $rsLanguage->fields['locale_name'] . '.txt' ;
                file_put_contents( $file , '# --- Generated at ' . date("F j, Y, g:i a"). ' --- ' .PHP_EOL, LOCK_EX);
            }

            $keyName  = $rsLanguage->fields['key_name'];
            $keyValue = $rsLanguage->fields['key_value'];

            $data = trim($keyName) . ' = ' . '"' . $keyValue .'"' . PHP_EOL ;

            file_put_contents( $file , $data, FILE_APPEND | LOCK_EX);

            $rsLanguage->MoveNext();
        }

        // Version >= 1.2 does not need to do some tests.
        $lang_default = $this->getConfig("lang");
        $license      =  $this->getConfig("license");
        $smartConfigFile = $this->_getHelpdezkPath().'/app/lang/' . $lang_default . '.txt';

        $smarty = $this->retornaSmarty();
        if (!$smarty) {
            $this->logIt('Can\'t return smarty instance - program: ' . $this->program . ' - method: ' . __METHOD__, 3, 'general', __LINE__);
            exit;
        }
        $smarty->configLoad($smartConfigFile, $license);
        //

        $this->logIt("Languages files update - program: " . $this->program . ' - method: ' . __METHOD__, 6, 'general', __LINE__);

        $mascdatetime = str_replace("%","",$this->dateFormat) . " " . str_replace("%","",$this->hourFormat);
        file_put_contents($this->helpdezkPath."/logs/updatelanguagefile.txt",date($mascdatetime),LOCK_EX );
        
        $this->logIt("Finish ". __METHOD__ ." at ".date("d/m/Y H:i:s").'. Program: '.$this->program ,6,'general');        

    }

    public function clearSmartyCache()
    {
        if (substr(php_sapi_name(), 0, 3) != 'cli') { 
            $this->logIt("This program runs only in the command line - program: ".$this->program ,6,'general');
            exit;
        }

        $this->logIt("Start ". __METHOD__ ." at ".date("d/m/Y H:i:s").'. Program: '.$this->program ,6,'general'); 

        $smarty = $this->retornaSmarty();

        if ($smarty->caching) {
            if (version_compare($this->getSmartyVersionNumber(), '3', '>=' )) {
                $smarty->clearAllCache();
            } else {
                $smarty->clear_all_cache();
            }

            $message = "Smarty Cache was successfully cleared: {$smarty->cache_dir}.";
        } else {

            $files = glob($smarty->compile_dir . '*.php');
            foreach($files as $file){
                if(is_file($file))
                    unlink($file);
            }

            $message = "The cache is disabled, the directory {$smarty->compile_dir}, was cleaned.";
        }

        $this->logIt("{$message} - program: " . $this->program . ' - method: ' . __METHOD__, 6, 'general', __LINE__);

        $mascdatetime = str_replace("%","",$this->dateFormat) . " " . str_replace("%","",$this->hourFormat);
        file_put_contents($this->helpdezkPath."/logs/clearsmartycache.txt",date($mascdatetime),LOCK_EX );
        
        $this->logIt("Finish ". __METHOD__ ." at ".date("d/m/Y H:i:s").'. Program: '.$this->program ,6,'general');        

    }


}

?>
