<?php

require_once(HELPDEZK_PATH . '/app/modules/emq/controllers/emqCommonController.php');
/*require_once(HELPDEZK_PATH . '/includes/classes/pipegrep/pipeDateTime.php');*/

class home extends emqCommon {
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
        $this->modulename = 'Intranet' ;
        //

        //$this
        $dbCommon = new common();
        $id = $dbCommon->getIdModule($this->modulename) ;
        if(!$id) {
            die('Module ' .$this->modulename. ' don\'t exists in tbmodule !!!') ;
        } else {
            $this->idmodule = $id ;
        }

        /*
        $this->loadModel('home_model');
        $dbHome = new home_model();
        $this->dbHome = $dbHome;
        */


    }

    public function index()
    {
        $cod_usu = $_SESSION['SES_COD_USUARIO'];
        //echo "<pre>"; print_r($_SESSION); echo "</pre>";
        $smarty = $this->retornaSmarty();
        $langVars = $smarty->getConfigVars();

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavEmq($smarty);

        // $this->makeDash($smarty);

        //$this->makeMessages($smarty);

        $smarty->assign('jquery_version', $this->jquery);

        // -- navbar
        $smarty->assign('navBar', 'file:'.$this->getHelpdezkPath().'/app/modules/main/views/nav-main.tpl');

        $smarty->display('emq-main.tpl');



    }






}

?>
