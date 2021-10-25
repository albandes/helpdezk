<?php

use App\core\Controller;
use App\src\appServices;

class Home extends Controller
{
	/**
	 *  en_us Calls the method that renders the module's home template
	 * 
	 *  pt_br Chama o método que renderiza o template da home do módulo
	 */
	public function index()
	{
		$appSrc = new appServices();
		$params = $appSrc->_getDefaultParams();

		$this->logger->info('Run ', ['Class' => __CLASS__, 'Method' => __METHOD__]);

		$this->view(
			'admin',
			'main',
			$params
		);
		
	}

}