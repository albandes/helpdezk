<?php

namespace App\core;


/**
* Esta classe é responsável por obter da URL o controller, método (ação) e os parâmetros
* e verificar a existência dos mesmo.
*/
class App
{
	protected $module = '';
	protected $controller = 'Home';
	protected $method = 'index';
	protected $page404 = false;
	protected $params = [];

	// constructor method
	public function __construct()
	{
		$this->setUrl();
		$URL_ARRAY = $this->parseUrl(); //echo "<pre>",print_r($URL_ARRAY,true),"</pre>";
		$this->setModule($URL_ARRAY);
		$this->setController($URL_ARRAY);
		$this->setMethod($URL_ARRAY);
		$this->setParams($URL_ARRAY);

		// chama um método de uma classe passando os parâmetros
		call_user_func_array([$this->controller, $this->method], $this->params);
	}

	private function setUrl()
    {
        $_GET['url'] = (isset($_GET['url']) ? $_GET['url'] : '/admin/');
        $this->_url = $_GET['url'];
        //die($this->_url) ;
        if ($_GET['url'] == 'admin/' || $_GET['url'] == '/admin/') {
            $path_default = $_ENV["PATH_DEFAULT"];
            if (substr($path_default, 0, 1) != '/') {
                $path_default = '/' . $path_default;
            }
            if ($path_default == "/..") {
                $path_default = "";
            }
            header('Location:' . $path_default . '/admin/home');
        }
    }

	/**
	* Este método pega as informações da URL (após o dominio do site) e retorna esses dados
	*
	* @return array
	*/
	private function parseUrl()
	{
		$REQUEST_URI = explode('/', $_GET['url']);
		return $REQUEST_URI;
	}

	private function setModule($url)
    {
        $this->_module = $url[0];
    }

	/**
	* Este método verifica se o array informado possui dados na psoição 0 (controlador)
	* caso exista, verifica se existe um arquivo com aquele nome no diretório Application/controllers
	* e instancia um objeto contido no arquivo, caso contrário a variável $page404 recebe true.
	*
	* @param  array  $url   Array contendo informações ou não do controlador, método e parâmetros
	*/
	private function setController($url)
	{ 
		if ( !empty($url[0]) && isset($url[0]) ) {
			if ( file_exists('./app/modules/'. $url[0]  .'/controllers/' . ucfirst($url[1])  . '.php') ) {
				$this->controller = ucfirst($url[1]);
			} else {
				$this->page404 = true;
			}
		}

		require './app/modules/'. $url[0]  .'/controllers/' . $this->controller . '.php';
		$this->controller = new $this->controller();

	}

	/**
	* Este método verifica se o array informado possui dados na psoição 1 (método)
	* caso exista, verifica se o método existe naquele determinado controlador
	* e atribui a variável $method da classe.
	*
	* @param  array  $url   Array contendo informações ou não do controlador, método e parâmetros
	*/
	private function setMethod($url)
	{
		if ( !empty($url[2]) && isset($url[2]) ) {
			if ( method_exists($this->controller, $url[2]) && !$this->page404) {
				$this->method = $url[2];
			} else {
				// caso a classe ou o método informado não exista, o método pageNotFound
				// do Controller é chamado.
				$this->method = 'pageNotFound';
			}
		}
	}

	/**
	* Este método verifica se o array informador possui a quantidade de elementos maior que 2
	* ($url[0] é o controller e $url[1] o método/ação a executar), caso seja, é atrbuido
	* a variável $params da classe um novo array  apartir da posição 2 do $url
	*
	* @param  array  $url   Array contendo informações ou não do controlador, método e parâmetros
	*/
	private function setParams($url)
	{
		//echo count($url)."<br>"; echo "<pre>",print_r($url,true),"</pre>";
		if (count($url) > 3) {
			$this->params = array_slice($url, 3);
		}
		//echo "<pre>",print_r($this->params,true),"</pre>";
	}

	
}