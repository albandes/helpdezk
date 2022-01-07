<?php

use App\core\Controller;

class ErrorHandler extends Controller
{
    public function __construct()
    {
        parent::__construct();
		
		session_start();
		$this->appSrc->_sessionValidate();
        
    }

    /**
	 *  en_us Calls the method that renders the module's home template
	 * 
	 *  pt_br Chama o método que renderiza o template da home do módulo
	 */
	public function index()
	{
		
	}
	

}