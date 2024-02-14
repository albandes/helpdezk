<?php

namespace App\src;

use App\src\appServices;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

class eemServices
{
    /**
     * @var object
     */
    protected $eemLogger;

    /**
     * @var string
     */
    private $_token;

    /**
     * Default constructor
     */
    public function __construct ()
    {
        $appSrc = new appServices();
        // create a log channel
        $formatter = new LineFormatter(null, $_ENV['LOG_DATE_FORMAT']);
        
        $stream = $appSrc->_getStreamHandler();
        $stream->setFormatter($formatter);

        $this->eemLogger  = new Logger('helpdezk');
        $this->eemLogger->pushHandler($stream);
    }

    /**
     * Set API token
     *
     * @param string $token Your app API key.
     *
     * @return void
     */
    public function setToken ($token) {
        $this->_token = (string)$token;
    }

    /**
     * Get API token
     *
     * @return string
     */
    public function getToken () {
        return $this->_token;
    }
		
	/**
	 * makeData
	 *
	 * @param  mixed $integracaoId
	 * @param  mixed $aData
	 * @return void
	 */
	function makeData($integracaoId,$aData)
    {
        return array( "token" => $this->getToken(),
                      "versaoApp" => "1.0",
                      "timestampEnvio" => date('Y-m-d H:i:s'),
                      "idIntegracao" => $integracaoId,
                      "dados" => $aData);
    }
    
    /**
     * httpPost
     *
     * @param  mixed $url
     * @param  mixed $data
     * @return void
     */
    function httpPost($url,$data)
    {
        //create a new cURL resource
        $ch = curl_init($url);

        $payload = json_encode($data);

        //attach encoded JSON string to the POST fields
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

        //set the content type to application/json
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

        //return response instead of outputting
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        //execute the POST request
        $result = curl_exec($ch);

        //close cURL resource
        curl_close($ch);

        return $result;
    }
    
    /**
     * postMessage
     *
     * @param  mixed $url
     * @param  mixed $data
     * @return void
     */
    function postMessage($url,$data)
    {
        //create a new cURL resource
        $ch = curl_init();

        $payload = json_encode($data);

        curl_setopt_array($ch, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => array(
                "X-Authorization: {$this->getToken()}",
                "content-type: application/json; charset=utf-8"
            ),
        ));

        //execute the POST request
        $result = curl_exec($ch);

        //close cURL resource
        curl_close($ch);

        return $result;
    }
    
    /**
     * postFile
     *
     * @param  mixed $url
     * @param  mixed $data
     * @return void
     */
    function postFile($url,$data)
    {
        //create a new cURL resource
        $ch = curl_init();

        curl_setopt_array($ch, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => array('arquivo'=> new \CURLFILE($data)),
            CURLOPT_HTTPHEADER => array(
                "Content-Type: multipart/form-data",
                "Authorization: Bearer {$this->getToken()}"
            ),
        ));

        //execute the POST request
        $result = curl_exec($ch);

        //close cURL resource
        curl_close($ch);

        return $result;
    }

}