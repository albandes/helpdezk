<?php

require_once(HELPDEZK_PATH . '/app/modules/scm/controllers/scmCommonController.php');

class home extends scmCommon {
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

        //
        $this->modulename = 'suprimentos' ;
        //


        $dbCommon = new common();
        $id = $dbCommon->getIdModule($this->modulename) ;
        if(!$id) {
            die('Module don\'t exists in tbmodule !!!') ;
        } else {
            $this->idmodule = $id ;
        }

        $this->accessExceptions = explode(',', $_SESSION['scm']['SCM_ACCESS_USER_EXCEPTIONS']);
        //$this->loadModel('home_model');
        //$dbHome = new home_model();
        //$this->dbHome = $dbHome;
    }

    public function index()
    {
        $idProduto = $_SESSION['SES_COD_USUARIO'];

        $smarty = $this->retornaSmarty();

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavScm($smarty);

        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        $smarty->assign('access', array());

        if($_SESSION['scm']['SCM_MAINTENANCE'] == 1 && !in_array($_SESSION['SES_COD_USUARIO'],$this->accessExceptions)){
            $smarty->assign('flgDisplay', 'hide');
            $smarty->assign('maintenanceMsg', $_SESSION['scm']['SCM_MAINTENANCE_MSG']);
            $smarty->display('scm-maintenance.tpl');
        }else{
            $smarty->display('scm-main.tpl');
        }


    }





}

?>
