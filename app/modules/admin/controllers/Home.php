<?php

use App\core\Controller;
use App\src\appServices;
use App\modules\admin\src\adminServices;

class Home extends Controller
{
	public function __construct()
    {
        parent::__construct();
		
        
    }

	/**
	 *  en_us Calls the method that renders the module's home template
	 * 
	 *  pt_br Chama o método que renderiza o template da home do módulo
	 */
	public function index()
	{
		$appSrc = new appServices();
		$params = $appSrc->_getDefaultParams();
		
		$params = $this->makeScreenAdmHome();
		
		$this->view(
			'admin',
			'main',
			$params
		);
		
	}

	public function makeScreenAdmHome()
    {
        $appSrc = new appServices();
		$adminSrc = new adminServices();
		$params = $appSrc->_getDefaultParams();
		$adminSrc->_makeNavAdm();

        return $params;
    }

}