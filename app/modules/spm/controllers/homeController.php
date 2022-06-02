<?php

require_once(HELPDEZK_PATH . '/app/modules/spm/controllers/spmCommonController.php');

class home extends spmCommon {
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
        $this->modulename = 'sportsmedicine' ;
        //
        //$this
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
        $idPerson = $_SESSION['SES_COD_USUARIO'];

        $smarty = $this->retornaSmarty();

        $this->makeNavVariables($smarty,$this->modulename);
        $this->_makeNavSpm($smarty);

        //$rsPerson = $this->getPersonById($idPerson);

        $imgFormat = $this->getImageFileFormat('/app/uploads/photos/'.$idPerson);

        if ($imgFormat) {
            $imgPhoto = $idPerson.'.'.$imgFormat;
        } else {
            $imgPhoto = 'default/no_photo.png';
        }

        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        $smarty->display('spm-main.tpl');



    }





}

?>
