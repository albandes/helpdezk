<?php

require_once(HELPDEZK_PATH . '/app/modules/hur/controllers/hurCommonController.php');
/*require_once(HELPDEZK_PATH . '/includes/classes/pipegrep/pipeDateTime.php');*/

class home extends hurCommon {
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
        $this->modulename = 'RecursosHumanos' ;
        //
        
        //$this
        $dbCommon = new common();
        $id = $dbCommon->getIdModule($this->modulename) ;
        if(!$id) {
            die('Module ' .$this->modulename. ' don\'t exists in tbmodule !!!') ;
        } else {
            $this->idmodule = $id ;
        }

        $this->loadModel('home_model');
        $dbHome = new home_model();
        $this->dbHome = $dbHome;

        /*$this->loadModel('ticket_model');
        $dbTicket = new ticket_model();
        $this-$dbTicket = $dbTicket;*/
    }

    public function index()
    {
        $cod_usu = $_SESSION['SES_COD_USUARIO'];

        $smarty = $this->retornaSmarty();
        $langVars = $smarty->getConfigVars();

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavHur($smarty);

        $this->makeDash($smarty);

        //$this->makeMessages($smarty);

        $smarty->assign('jquery_version', $this->jquery);

        // -- navbar
        $smarty->assign('navBar', 'file:'.$this->getHelpdezkPath().'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);

        $smarty->display('hur-main.tpl');



    }

    function makeDash($smarty)
    {
        $smarty->assign('dtatualiza', $this->_getDataAtualizacao());
        $smarty->assign('total_mq', $this->_getNumFuncEmpresa(2));
        $smarty->assign('total_mario', $this->_getNumFuncEmpresa(1));
        $smarty->assign('total_tresv', $this->_getNumFuncEmpresa(3));
    }












}

?>
