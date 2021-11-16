<?php

namespace App\core;


/**
* en_us This class is responsible for getting the controller, method (action) and parameters from the URL and verifying their existence.
* 
* pt_br Esta classe é responsável por obter da URL o controller, método (ação) e os parâmetros e verificar a existência dos mesmo.
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
        $URL_ARRAY = $this->parseUrl();
        $this->setModule($URL_ARRAY);
        $this->setController($URL_ARRAY);
        $this->setMethod($URL_ARRAY);
        $this->setParams($URL_ARRAY);
        
        // calls a method of a class passing the parameters
        call_user_func_array([$this->controller, $this->method], $this->params);
    }
        
    /**
     * en_us Configure the URL to redirect
     * 
     * pt_br Configura a URL para redirecionar
     *
     * @return void
     */
    private function setUrl()
    {
        $_GET['url'] = (isset($_GET['url']) ? $_GET['url'] : '/admin/');
        
        $docRoot = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT');
        $dirName = str_replace("\\","/",dirname(__DIR__,PATHINFO_BASENAME));
        $path_default = str_replace($docRoot,'',$dirName);
        
        if ($_GET['url'] == 'admin/' || $_GET['url'] == '/admin/') {            
            
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
     * en_us This method takes the information from the URL (after the site's domain) and returns that data.
     * 
     * pt_br Este método pega as informações da URL (após o dominio do site) e retorna esses dados
     *
     *  @return array
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
     * en_us This method checks if the informed array has data in position 1 (controller) 
     * if it exists, checks if there is a file with that name in the app/controllers directory 
     * and instantiates an object contained in the file, otherwise the variable $page404 receives true.
     * 
     * pt_br Este método verifica se o array informado possui dados na posição 1 (controlador)
     * caso exista, verifica se existe um arquivo com aquele nome no diretório app/controllers
     * e instancia um objeto contido no arquivo, caso contrário a variável $page404 recebe true.
     *
     *  @param  array  $url   Array containing information or not about the controller, method and parameters.
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
     * en_us This method checks if the informed array has data in position 2 (method) 
     * if it exists, checks if the method exists in that particular controller and assigns the class variable $method.
     * 
     * pt_br Este método verifica se o array informado possui dados na posição 2 (método)
     * caso exista, verifica se o método existe naquele determinado controlador e atribui a variável $method da classe.
     *
     * @param  array  $url   Array containing information or not about the controller, method and parameters.
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
     * en_us TThis method checks if the informer array has the number of elements greater than 3 
     * ($url[1] is the controller and $url[2] the method/action to execute), if so, 
     * the variable $params of class one is assigned new array from position 3 of $url
     * 
     * pt_br Este método verifica se o array informador possui a quantidade de elementos maior que 3
     * ($url[1] é o controller e $url[2] o método/ação a executar), caso seja, é atrbuido
     * a variável $params da classe um novo array a partir da posição 3 do $url
     *
     * @param  array  $url   Array containing information or not about the controller, method and parameters.
     */
    private function setParams($url)
    {
        if (count($url) > 3) {
            $this->params = array_slice($url, 3);
        }
    }

}