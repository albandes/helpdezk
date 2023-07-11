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
     * en_us 
     * pt_br
     *
     * @return void
     */
    public function init()
    {
        $appSrc = new appServices();

        $guzzleClient = new GuzzleClient(['curl' => [CURLOPT_SSL_VERIFYPEER => false]]);

        $this->client->setHttpClient($guzzleClient);
        $this->client->setAuthConfig("{$appSrc->_getHelpdezkPath()}/storage/credentials/google_oauth_credentials.json");
        $this->client->setRedirectUri("{$_ENV['HDK_URL']}/admin/login/auth/");
        $this->client->addScope('email');
        $this->client->addScope('profile');
    }
    
    /**
     * authorized
     * 
     * en_us
     * pt_br
     *
     * @return void
     */
    public function authorized($code)
    {
        $token = $this->client->fetchAccessTokenWithAuthCode($code);
        $this->client->setAccessToken($token['access_token']);
        $googleService = new ServiceOauth2($this->client);
        
        $this->data = $googleService->userinfo->get();
        /* echo "{$this->data}";
        return $this->data; */
        //echo"<pre>",print_r($this->data,true),"</pre>";
        return true;
    }
    
    /**
     * Get the value of data
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
     * en_us
     * pt_br
     *
     * @return void
     */
    public function generateAuthLink()
    {
        return $this->client->createAuthUrl();
    }

}