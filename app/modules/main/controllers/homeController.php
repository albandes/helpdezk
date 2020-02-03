<?php

session_start();

class Home extends Controllers {
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
    }

    public function index()
    {
        $smarty = $this->retornaSmarty();
        $this->makeNavVariables($smarty,'main');

        $smarty->display('main.tpl');
    }


    public function logout()
    {
        $this->sessionDestroy();
        header('Location:' . path . '/admin/login');
    }

    public function lockscreen()
    {

        $smarty = $this->retornaSmarty();
        $this->makeNavVariables($smarty);

        $cod_usu = $_SESSION['SES_COD_USUARIO'];
        $imgFormat = $this->getImageFileFormat('/app/uploads/photos/'.$cod_usu);

        if ($imgFormat) {
            $imgPhoto = $cod_usu.'.'.$imgFormat;
        } else {
            $imgPhoto = 'default/no_photo.png';
        }

        $smarty->assign('person_login', $_SESSION['SES_LOGIN_PERSON']);
        $smarty->assign('login', $this->helpdezkUrl . '/admin/login');
        $smarty->assign('person_photo', $this->getHelpdezkUrl().'/app/uploads/photos/' . $imgPhoto);

        $this->sessionDestroy();
        $smarty->display('lockscreen.tpl');

    }

}

?>
