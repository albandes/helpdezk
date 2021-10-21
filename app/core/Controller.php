<?php

namespace App\core;

use App\src\localeServices;
use App\src\appServices;
use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;


/**
* Esta classe é responsável por instanciar um model e chamar a view correta
* passando os dados que serão usados.
*/
class Controller
{
    
    /**
     * @var object
     */
    protected $logger;
    
    public function __construct()
    {
        $appSrc = new appServices();
        
        $this->logger = new Logger('helpdezk'); 
        $rotating = new RotatingFileHandler($appSrc->_getHelpdezkPath(). "/storage/logs/helpdezk.log", 0, Logger::DEBUG);
        $this->logger->pushHandler($rotating);
    }
    
    /**
     * en_us Renders selected template
     * 
     * pt_br Renderiza o template selecionado
     *
     * @param string    $module Directory where is the template
     * @param string	$page   Name of template
     * @param array	    $params
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