<?php

use App\core\Controller;
use App\src\appServices;
use App\src\localeServices;

class Home extends Controller
{
	public function __construct()
    {
        parent::__construct();
		
		$appSrc = new appServices();
		$appSrc->_sessionValidate();
        
    }

	/**
	 *  en_us Calls the method that renders the module's home template
	 * 
	 *  pt_br Chama o método que renderiza o template da home do módulo
	 */
	public function index()
	{
		$params = $this->makeScreenMainHome();
		
		$this->view('main','main',$params);
		
	}
	
	/**
	 *  en_us Configure program screens
	 * 
	 *  pt_br Configura as telas do programa
	 */
	public function makeScreenMainHome()
    {
        $appSrc = new appServices();
		$params = $appSrc->_getDefaultParams();
		$params = $adminSrc->_makeNavAdm($params);
		
        return $params;
    }

	public function logout()
    {
        $appSrc = new appServices();
		$appSrc->_sessionDestroy();
        header('Location:' . $_ENV['HDK_URL'] . '/admin/login');
    }

	public function lockscreen()
    {
		$params = $this->makeScreenMainHome();
		
		$this->view('main','lockscreen',$params);

        /*$smarty = $this->retornaSmarty();
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
        $smarty->display('lockscreen.tpl');*/

    }

    public function translateLabel()
    {
        $translator = new localeServices();
        $label = trim($_POST['label']);

        echo json_encode($translator->translate($label));
    }

}