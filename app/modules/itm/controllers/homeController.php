<?php

require_once(HELPDEZK_PATH . '/app/modules/itm/controllers/itmCommonController.php');

class home extends itmCommon {
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

        $this->modulename = 'itm' ;


        $dbCommon = new common();
        $id = $dbCommon->getIdModule($this->modulename) ;
        if(!$id) {
            die('Module don\'t exists in tbmodule !!!') ;
        } else {
            $this->idmodule = $id ;
        }

        //$this->loadModel('home_model');
        //$dbHome = new home_model();
        //$this->dbHome = $dbHome;
    }

    public function index()
    {
        $curr_url = $_GET['url'];
        $curr_url = explode("/", $curr_url);

        $smarty = $this->retornaSmarty();

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavItm($smarty);

        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        $smarty->display('itm-main.tpl');

    }





}

?>
