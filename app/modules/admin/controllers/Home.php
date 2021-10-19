<?php

use App\core\Controller;

class Home extends Controller
{
	/*
	* chama a view index.php do  /home   ou somente   /
	*/
	public function index()
	{
		$this->view(
			'admin',
			'main',
			array(
				"path"		=> $this->getPath(),
				"lang"		=> strtolower($_ENV['LANG']),
				"title" 	=> "Teste Título",
				"layout"	=> $this->getLayoutTemplate(),
				"version" 	=> "helpdezk-community-1.1.10",
				"navBar"	=> $this->getNavbarTemplate(),
				"footer"	=> $this->getFooterTemplate()
			)
		);
		
	}

}