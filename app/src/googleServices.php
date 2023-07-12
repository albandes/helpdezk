<?php

namespace App\src;

use App\src\appServices;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

use Google\Client;
use Google\Service\Oauth2 as ServiceOauth2;
use GuzzleHttp\Client as GuzzleClient;
use Google\Service\Oauth2\Userinfo;

class googleServices
{
    /**
     * @var object
     */
    protected $googlelogger;
    
    /**
     * @var object
     */
    protected $googleEmailLogger;
    
    /**
     * @var Client
     */
    public $client;

    /**
     * @var Userinfo
     */
    private $data;

    public function __construct($region=null,$bucket=null,$key=null,$secret=null)
    {
        $appSrc = new appServices();
        // create a log channel
        $formatter = new LineFormatter(null, $_ENV['LOG_DATE_FORMAT']);
        
        $stream = $appSrc->_getStreamHandler();
        $stream->setFormatter($formatter);

        $this->googlelogger  = new Logger('helpdezk');
        $this->googlelogger->pushHandler($stream);
        
        $this->client = new Client;
    }
    
    /**
     * init
     * 
     * en_us Initializes the authentication client
     * pt_br Inicializa o cliente de autenticação
     *
     * @return void
     */
    public function init()
    {
        $appSrc = new appServices();

        $guzzleClient = new GuzzleClient(['curl' => [CURLOPT_SSL_VERIFYPEER => false]]);

        $credentials = ['client_id' => $_ENV['GOOGLE_OAUTH_CLIENT_ID'],'client_secret' => $_ENV['GOOGLE_OAUTH_CLIENT_SECRET']];
        
        $this->client->setHttpClient($guzzleClient);
        $this->client->setAuthConfig($credentials);
        $this->client->setRedirectUri("{$_ENV['HDK_URL']}/admin/login/auth/");
        $this->client->addScope('email');
        $this->client->addScope('profile');
    }
    
    /**
     * authorized
     * 
     * en_us Get authorized account data
     * pt_br Obtem os dados da conta autorizada
     *
     * @return void
     */
    public function authorized($code)
    {
        $token = $this->client->fetchAccessTokenWithAuthCode($code);
        $this->client->setAccessToken($token['access_token']);
        $googleService = new ServiceOauth2($this->client);
        
        $this->data = $googleService->userinfo->get();
        return true;
    }
    
    /**
     * en_us Get the value of data
     * pt_br Retorna os valores do objeto data
     *
     * @return Userinfo
     */
    public function getData()
    {
        return $this->data;
    }
    
    /**
     * generateAuthLink
     * 
     * en_us Generates the link to the authentication button
     * pt_br Gera o link para o botão de autenticação
     *
     * @return void
     */
    public function generateAuthLink()
    {
        return $this->client->createAuthUrl();
    }

}