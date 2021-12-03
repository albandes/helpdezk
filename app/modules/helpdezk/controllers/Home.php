<?php

use App\core\Controller;
use App\src\appServices;
use App\modules\helpdezk\src\hdkServices;

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
		$params = $this->makeScreenHdkHome();
		
		$this->view('helpdezk','main',$params);
		
	}
	
	/**
	 *  en_us Configure program screens
	 * 
	 *  pt_br Configura as telas do programa
	 */
	public function makeScreenHdkHome()
    {
        $appSrc = new appServices();
		//$hdkSrc = new hdkServices();
		$params = $appSrc->_getDefaultParams();
		//$params = $hdkSrc->_makeNavHdk($params);
		
        return $params;
    }

}