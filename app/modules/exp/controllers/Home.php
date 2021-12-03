<?php

use App\core\Controller;
use App\src\appServices;
use App\modules\admin\src\adminServices;
use App\modules\exp\src\expServices;

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
		$params = $this->makeScreenExpHome();
		
		$this->view('exp','main',$params);
	}
	
	/**
	 *  en_us Configure program screens
	 * 
	 *  pt_br Configura as telas do programa
	 */
	public function makeScreenExpHome()
    {
        $appSrc = new appServices();
		$expSrc = new expServices();
		$params = $appSrc->_getDefaultParams();
		$params = $expSrc->_makeNavExp($params);
		
        return $params;
    }

}