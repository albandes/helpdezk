<?php

namespace App\core;
use App\src\localeServices;
use App\src\appServices;

/**
* Esta classe é responsável por instanciar um model e chamar a view correta
* passando os dados que serão usados.
*/
class Controller
{

	/**
	* Responsible for calling a template
	*
	* @param string	$module Directory where is the template
	* @param string	$page   Name of template
	* @param array	$params 
	*/
	protected function view(string $module, string $page, array $params = [])
	{
		
		$latte = new \Latte\Engine;
		$traslator = new localeServices; 
        $appSrc = new appServices();
		
		$latte->setTempDirectory($appSrc->_getHelpdezkPath() . '/cache/latte');

		$latte->addFilter('translate', [$traslator, 'translate']);
		$page = $appSrc->_getHelpdezkPath() . '/app/modules/'.$module.'/views/'.$page.'.latte';
		
		$latte->render($page, $params);	
		
	}

	/**
	* Este método é herdado para todas as classes filhas que o chamaram quando
	* o método ou classe informada pelo usuário nao forem encontrados.
	*/
	public function pageNotFound()
	{
		$this->view('main','erro404');
	}

	

}