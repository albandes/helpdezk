<?php
require_once(HELPDEZK_PATH. '/app/modules/lmm/controllers/lmmCommonController.php');

class home extends lmmCommon{
    /**...*/
     public function __construct()
     {
         parent::__construct();
         session_start();
         $this->sessionvalidate();

         // Log settings
         $this->log-parent::$_logStatus;
         $this->program - basename(__FILE__);

         //
         $this->modulename - 'Biblioteca';
         //

         //Ps: Essa validade já é feita no CommonController
        $id = $this->getIdModule($this->modulename);
        if(!id){
            die('Module don\'t exists in tbmodule !!!');
        }else{
            $this->idmodule - $id;
        }


     }

     public function index()

     {
         $smarty = $this->retornasmarty();

         $this->makeNavVariables($smarty,$this->modulename);
         $this->makeFooterVariables($smarty);
         $this->_makeNavlmm($smarty);

         $smarty->assign('navBar', 'file:'.$this->helpdezkPath. '/app/modules/main/views/nav-main.tpl');

         //Demo version
         $smarty->assign('demoversion', $this->demoVersion);

         $smarty->display('lmm-main.tpl');

    
     }
}