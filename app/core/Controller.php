<?php

namespace App\core;

use App\src\localeServices;
use App\src\appServices;
use Monolog\Logger;
//use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;


/**
* en_us This class is responsible for instantiating a model and calling the correct view passing the data that will be used.
* 
* pt_br Esta classe é responsável por instanciar um model e chamar a view correta passando os dados que serão usados.
*/
class Controller
{
    
    /**
     * @var object
     */
    protected $logger;
    /**
     * @var object
     */
    protected $emailLogger;
    
    public function __construct()
    {
        session_start();
        $appSrc = new appServices();
        
        // create a log channel
        $formatter = new LineFormatter(null, $_ENV['LOG_DATE_FORMAT']);
        
        $stream = $appSrc->_getStreamHandler();
        $stream->setFormatter($formatter);


        $this->logger  = new Logger('helpdezk');
        $this->logger->pushHandler($stream);
        
        // Clone the first one to only change the channel
        $this->emailLogger = $this->logger->withName('email');

       
        
        // Erro do DAO
        //$this->logger->error('Error updating scheduler ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $apiService->getDaoError($arrDao)]);
        
        // Erro comum
        //$this->logger->error('Error updating scheduler ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        // Info
        //$this->logger->info('Add new user ', ['username' => 'albandes']);
        //$this->logger->info('Run ', ['Class' => __CLASS__, 'Method' => __METHOD__]);
        // Email 
        // Tem que criar um getEmailError
        //->error('Error sending email ', ['message' => $retSmtp['push']['result']['error'][0]['message']]);


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
        
        if(!is_writable($appSrc->_getHelpdezkPath() . '/cache/latte')) {
            die('cache/latte not writable !');
        }

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