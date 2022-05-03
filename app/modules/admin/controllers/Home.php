<?php

use App\core\Controller;
use App\src\appServices;
use App\modules\admin\src\adminServices;

class Home extends Controller
{
	public function __construct()
    {
        parent::__construct();

		$this->appSrc->_sessionValidate();	
        
    }

	/**
	 *  en_us Calls the method that renders the module's home template
	 * 
	 *  pt_br Chama o método que renderiza o template da home do módulo
	 */
	public function index()
	{
		$params = $this->makeScreenAdmHome();
		
		$this->view('admin','main',$params);
		
	}
	
	/**
	 *  en_us Configure program screens
	 * 
	 *  pt_br Configura as telas do programa
	 */
	public function makeScreenAdmHome()
    {
        $adminSrc = new adminServices();
		$params = $this->appSrc->_getDefaultParams();
		$params = $adminSrc->_makeNavAdm($params);
		
        return $params;
    }

}