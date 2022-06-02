<?php

require_once(HELPDEZK_PATH . '/app/modules/lgp/controllers/lgpCommonController.php');

class home extends lgpCommon {
    /**
     * Create an instance, check session time
     *
     * @access public
     */
    public function __construct()
    {

        parent::__construct();
        session_start();
        $this->sessionValidate();

        // Log settings
        $this->log = parent::$_logStatus;
        $this->program  = basename( __FILE__ );

        $id = $this->getIdModule($this->modulename) ;
        if(!$id) {
            die('Module don\'t exists in tbmodule !!!') ;
        } else {
            $this->idmodule = $id ;
        }

    }
    
    public function index()
    {
        $smarty = $this->retornaSmarty();

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavLgp($smarty);

        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);

        $smarty->display('lgp-main.tpl');

    }

}