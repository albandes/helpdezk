<?php

$debug = true ;

if ($debug)
    error_reporting(E_ERROR | E_WARNING | E_PARSE);
else
    error_reporting(0);


session_start();

if (isCli()) {
    $CLI = true ;
    ini_set('html_errors', false);
}

$cron_path = getRootPath('cron-jobs') ;
define ('HELPDEZK_PATH', $cron_path) ;

$lineBreak = $CLI ? PHP_EOL : '<br>';

define('SMARTY', HELPDEZK_PATH.'includes/Smarty/');
require_once(HELPDEZK_PATH.'app/modules/cron-jobs/cronSystem.php');
require_once(HELPDEZK_PATH.'app/modules/cron-jobs/cronController.php');
require_once(HELPDEZK_PATH.'app/modules/cron-jobs/cronModel.php');


require_once(HELPDEZK_PATH . 'includes/classes/pipegrep/syslog.php');

require_once (HELPDEZK_PATH.'app/modules/admin/models/index_model.php');

require_once (HELPDEZK_PATH.'system/common.php');

$cronSystem = new cronSystem();

$cronSystem->setCronSession() ;
$cronSystem->SetEnvironment();
$cronSystem->_tokenOperatorLink = false;

setLogVariables($cronSystem);
$lineBreak = $cronSystem->getLineBreak();

/*
 *  Models
 */
if(!setRequire(HELPDEZK_PATH.'app/modules/admin/models/features_model.php',$cronSystem)){exit;}
if(!setRequire(HELPDEZK_PATH.'app/modules/bin/models/georeferencing_model.php',$cronSystem)){exit;}
if(!setRequire(HELPDEZK_PATH.'app/modules/emq/models/emails_model.php',$cronSystem)){exit;}

$admFeature = new features_model();
$dbGeoref = new georeferencing_model();
$dbEmail = new emails_model();

$retFeat = getAPIFeature("bin",1,$admFeature,$cronSystem); 
$currentYear = date("Y");
$limit  = 39;

$cronSystem->logIt("Start at ".date("d/m/Y H:i:s")." - Program: ". __FILE__ ,6,'general');

$link = "/georefdata/$currentYear";
$ret = request('GET',$link,false,$cronSystem);
if(!$ret['success']){
    $cronSystem->logIt("No result return. Program: " . __FILE__ , 3, 'general', __LINE__);
    $cronSystem->logIt("Finish at ".date("d/m/Y H:i:s").'. No records to process. Program: '. __FILE__ ,6,'general');
    exit;
}

$i = 0;
$aOK = array();
$aMensagens = array("EI"=>array(),"EF9"=>array(),"EM"=>array());
foreach($ret['return'] as $key=>$value) {
    //echo "",print_r($value,true),"\n";
    $total++;

    if(in_array($value['matricula'],$aOK)){
        if(!in_array($value['stmatricula'],array('NOV','REM','REB'))){
            $cronSystem->logIt("Already recorded, student: {$value['matricula']} - {$value['nome']} - {$value['stmatricula']}. Program: ". __FILE__ ,6,'general');
            continue;
        }
    }
    
    if(validateData($value)){
        $cronSystem->logIt("Missing data, student: {$value['matricula']} - {$value['nome']}. Program: ". __FILE__ ,6,'general');
        continue;
    }

    //echo "Student: {$value['matricula']} - {$value['nome']} - {$value['stmatricula']}.\n";
    
    $addressTMP ="{$value['tipologradouro']} {$value['logradouro']}";
    $addressEmail ="{$value['tipologradouro']} {$value['logradouro']}, {$value['numero']}";
    $address = "{$value['tipologradouro']} {$value['logradouro']} {$value['numero']} {$value['cidade']} {$value['uf']} Brasil";
    $address = str_replace(" ", "+", $address);
    
    $attempts = 0 ;
    $success = false;
    //echo "{$address}\n";
    while($success != true && $attempts < 3) {
        $urlApiGoogle = "{$retFeat['url']}geocode/json?sensor=false&key={$retFeat['key']}&address={$address}";
        $response = file_get_contents($urlApiGoogle);
        
        $json = json_decode($response,TRUE); 
        
        if( $json["status"] == 'OK') {
            break;
        }

        $attempts++;
        sleep(2);
        
        $cronSystem->logIt("Student {$value['matricula']} - {$value['nome']} attempt {$attempts}. Program: ". __FILE__ ,6,'general');
    }
    
    if( $json["status"] != 'OK') {
        $cronSystem->logIt("Address: {$address}, erro Google API: {$json['status']}!!!. Program: ". __FILE__ ,3,'general');
        $bus = array (
            "studentName"    => utf8_decode(ucwords($value['nome'])),
            "address"        => utf8_decode($addressEmail),
            "googleAddress" => "Sem resultado no Google Maps"
        );
        array_push($aMensagens[$value['idcurso']], $bus);
        continue;
    }
    
    $retAddress = array(
        $json["results"][0]["address_components"][1]["long_name"],
        $json["results"][0]["address_components"][1]["short_name"]
    );
    
    if(!in_array($addressTMP,$retAddress)){
        $bus = array (
            "studentName"    => utf8_decode(ucwords($value['nome'])),
            "address"        => utf8_decode($addressEmail),
            "googleAddress" => utf8_decode("{$json["results"][0]["address_components"][1]["short_name"]}, {$json["results"][0]["address_components"][0]["short_name"]}")
        );
        array_push($aMensagens[$value['idcurso']], $bus);
    }

    $value['google_lat'] = $json["results"][0]["geometry"]["location"]["lat"] ;
    $value['google_lng'] = $json["results"][0]["geometry"]["location"]["lng"];
    $value['google_city'] = utf8_decode($json["results"][0]["address_components"][2]["long_name"]);
    $google_state = $json["results"][0]["address_components"][4]["short_name"];
    if(empty($google_state)) {
        $$google_state = '';
        $cronSystem->logIt("Student: {$value['matricula']} - Address: {$address} {$google_state} returned empty!!! - . Program: ". __FILE__ ,3,'general');
    }
    
    $value['google_formatted_address'] = addslashes($json["results"][0]["formatted_address"]);
    
    $i++;
    if ($i > $limit)
        break;
}

sendAlert($aMensagens,$dbEmail,$cronSystem);
$cronSystem->logIt("Finish at ".date("d/m/Y H:i:s")." - {$total} records processed - Program: ". __FILE__ ,6,'general');
die("Ran OK \n") ;

function setLogVariables($cronSystem)
{
    $objSyslog = new Syslog();
    $log  = $objSyslog->setLogStatus() ;
    if ($log) {
        $objSyslog->SetFacility(18);
        $cronSystem->_logLevel = $objSyslog->setLogLevel();
        $cronSystem->_logHost = $objSyslog->setLogHost();
        if($cronSystem->_logHost == 'remote')
            $cronSystem->_logRemoteServer = $objSyslog->setLogRemoteServer();
    }
}

function getRootPath ($module)
{

    $path =  str_replace(DIRECTORY_SEPARATOR ."app".DIRECTORY_SEPARATOR ."modules". DIRECTORY_SEPARATOR . $module ,"",__DIR__ ) ;
    return $path . DIRECTORY_SEPARATOR;
}

function cronSets($cronSystem)
{
    //environment
    global $CLI;
    $cron_path = getRootPath('cron-jobs');

    define('path', $cronSystem->_getPathDefault());

    if ($CLI) {
        define('DOCUMENT_ROOT', substr( $cron_path,0, strpos($cron_path,$cronSystem->pathDefault)).'/');
    } else {
        define('DOCUMENT_ROOT', $cronSystem->_getDocumentRoot());
    }
    define('LANGUAGE',$cronSystem->getConfig("lang"));
    define('HELPDEZK_PATH', realpath(DOCUMENT_ROOT.path)) ;
}

function isCli()
{
    if ( defined('STDIN') )
    {
        return true;
    }

    if ( php_sapi_name() === 'cli' )
    {
        return true;
    }

    if ( array_key_exists('SHELL', $_ENV) ) {
        return true;
    }

    if ( empty($_SERVER['REMOTE_ADDR']) and !isset($_SERVER['HTTP_USER_AGENT']) and count($_SERVER['argv']) > 0)
    {
        return true;
    }

    if ( !array_key_exists('REQUEST_METHOD', $_SERVER) )
    {
        return true;
    }

    return false;
}

function getAPIFeature($prefix,$categoryID,$admFeature,$cronSystem)
{
    $aRet = array();
    $ret = $admFeature->getConfigs($prefix,$categoryID);
    if(!$ret){
        $cronSystem->logIt("Can't get Config Data - Program: " . __FILE__ , 3, 'general', __LINE__);
        return false;
    }
    
    while(!$ret->EOF){
        if($ret->fields['config_name'] == 'maps_api_url')
            $aRet['url'] = $ret->fields['value'];
        if($ret->fields['config_name'] == 'maps_api_key')
            $aRet['key'] = $ret->fields['value'];

        $ret->MoveNext();
    }
    
    return $aRet;

}

function setRequire($requireFile,$cronSystem){
    if (!file_exists($requireFile)) {
        $cronSystem->logIt("{$requireFile} does not exist - Program: ". __FILE__ ,3,'email',__LINE__);
        return false;
    }else{
        return require_once($requireFile);
    }
}

function request ($type, $request, $args = false, $cronSystem) {
    if (!$args) {
        $args = array();
    } elseif (!is_array($args)) {
        $args = array($args);
    }

    $url = $cronSystem->getConfig('server_api') . $request;
    
    $c = curl_init();
    curl_setopt($c, CURLOPT_HEADER, 0);
    curl_setopt($c, CURLOPT_VERBOSE, 0);
    curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($c, CURLOPT_URL, $url);

    if (count($args)) curl_setopt($c, CURLOPT_POSTFIELDS , http_build_query($args));

    switch ($type) {
        case 'POST':
            curl_setopt($c, CURLOPT_POST, 1);
            break;
        case 'GET':
            curl_setopt($c, CURLOPT_HTTPGET, 1);
            break;
        default:
            curl_setopt($c, CURLOPT_CUSTOMREQUEST, $type);
    }

    $data = curl_exec($c);

    if(!curl_errno($c)) {
        $info = curl_getinfo($c); 
        if ($info['http_code'] == 401) {
            $message = 'Got error, http code: ' . $info['http_code'] . ' - ' . getHttpErrorCode($info['http_code']) ;
            $arrayRet = array('success' => false, 'message' => $message, 'return' => '');
        } else {
            $res = json_decode($data,true);
            $arrayRet = array('success' => true, 'message' => '', 'return' => $res['result']);
        }

    } else {
        $message = 'Error making API request, curl error: '. curl_error($c) . ' ' . getCurlErrorCode(curl_errno($c));
        $arrayRet = array('success' => false, 'message' => $message, 'return' => '');
    }

    curl_close($c);

    return $arrayRet;

}

function getHttpErrorCode($code)
{
    $http_codes = array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        103 => 'Checkpoint',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Switch Proxy',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        449 => 'Retry With',
        450 => 'Blocked by Windows Parental Controls',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        509 => 'Bandwidth Limit Exceeded',
        510 => 'Not Extended'
    );
    return $http_codes[$code];
}

function getCurlErrorCode($code)
{
    $curl_error_codes = array (
        0 => 'CURLE_OK',
        1 => 'CURLE_UNSUPPORTED_PROTOCOL',
        2 => 'CURLE_FAILED_INIT',
        3 => 'CURLE_URL_MALFORMAT',
        4 => 'CURLE_NOT_BUILT_IN',
        5 => 'CURLE_COULDNT_RESOLVE_PROXY',
        6 => 'CURLE_COULDNT_RESOLVE_HOST',
        7 => 'CURLE_COULDNT_CONNECT',
        8 => 'CURLE_FTP_WEIRD_SERVER_REPLY',
        9 => 'CURLE_REMOTE_ACCESS_DENIED',
        10 => 'CURLE_FTP_ACCEPT_FAILED',
        11 => 'CURLE_FTP_WEIRD_PASS_REPLY',
        12 => 'CURLE_FTP_ACCEPT_TIMEOUT',
        13 => 'CURLE_FTP_WEIRD_PASV_REPLY',
        14 => 'CURLE_FTP_WEIRD_227_FORMAT',
        15 => 'CURLE_FTP_CANT_GET_HOST',
        17 => 'CURLE_FTP_COULDNT_SET_TYPE',
        18 => 'CURLE_PARTIAL_FILE',
        19 => 'CURLE_FTP_COULDNT_RETR_FILE',
        21 => 'CURLE_QUOTE_ERROR',
        22 => 'CURLE_HTTP_RETURNED_ERROR',
        23 => 'CURLE_WRITE_ERROR',
        25 => 'CURLE_UPLOAD_FAILED',
        26 => 'CURLE_READ_ERROR',
        27 => 'CURLE_OUT_OF_MEMORY',
        28 => 'CURLE_OPERATION_TIMEDOUT',
        30 => 'CURLE_FTP_PORT_FAILED',
        31 => 'CURLE_FTP_COULDNT_USE_REST',
        33 => 'CURLE_RANGE_ERROR',
        34 => 'CURLE_HTTP_POST_ERROR',
        35 => 'CURLE_SSL_CONNECT_ERROR',
        36 => 'CURLE_BAD_DOWNLOAD_RESUME',
        37 => 'CURLE_FILE_COULDNT_READ_FILE',
        38 => 'CURLE_LDAP_CANNOT_BIND',
        39 => 'CURLE_LDAP_SEARCH_FAILED',
        41 => 'CURLE_FUNCTION_NOT_FOUND',
        42 => 'CURLE_ABORTED_BY_CALLBACK',
        43 => 'CURLE_BAD_FUNCTION_ARGUMENT',
        45 => 'CURLE_INTERFACE_FAILED',
        47 => 'CURLE_TOO_MANY_REDIRECTS',
        48 => 'CURLE_UNKNOWN_OPTION',
        49 => 'CURLE_TELNET_OPTION_SYNTAX',
        51 => 'CURLE_PEER_FAILED_VERIFICATION',
        52 => 'CURLE_GOT_NOTHING',
        53 => 'CURLE_SSL_ENGINE_NOTFOUND',
        54 => 'CURLE_SSL_ENGINE_SETFAILED',
        55 => 'CURLE_SEND_ERROR',
        56 => 'CURLE_RECV_ERROR',
        58 => 'CURLE_SSL_CERTPROBLEM',
        59 => 'CURLE_SSL_CIPHER',
        60 => 'CURLE_SSL_CACERT',
        61 => 'CURLE_BAD_CONTENT_ENCODING',
        62 => 'CURLE_LDAP_INVALID_URL',
        63 => 'CURLE_FILESIZE_EXCEEDED',
        64 => 'CURLE_USE_SSL_FAILED',
        65 => 'CURLE_SEND_FAIL_REWIND',
        66 => 'CURLE_SSL_ENGINE_INITFAILED',
        67 => 'CURLE_LOGIN_DENIED',
        68 => 'CURLE_TFTP_NOTFOUND',
        69 => 'CURLE_TFTP_PERM',
        70 => 'CURLE_REMOTE_DISK_FULL',
        71 => 'CURLE_TFTP_ILLEGAL',
        72 => 'CURLE_TFTP_UNKNOWNID',
        73 => 'CURLE_REMOTE_FILE_EXISTS',
        74 => 'CURLE_TFTP_NOSUCHUSER',
        75 => 'CURLE_CONV_FAILED',
        76 => 'CURLE_CONV_REQD',
        77 => 'CURLE_SSL_CACERT_BADFILE',
        78 => 'CURLE_REMOTE_FILE_NOT_FOUND',
        79 => 'CURLE_SSH',
        80 => 'CURLE_SSL_SHUTDOWN_FAILED',
        81 => 'CURLE_AGAIN',
        82 => 'CURLE_SSL_CRL_BADFILE',
        83 => 'CURLE_SSL_ISSUER_ERROR',
        84 => 'CURLE_FTP_PRET_FAILED',
        85 => 'CURLE_RTSP_CSEQ_ERROR',
        86 => 'CURLE_RTSP_SESSION_ERROR',
        87 => 'CURLE_FTP_BAD_FILE_LIST',
        88 => 'CURLE_CHUNK_FAILED',
        89 => 'CURLE_NO_CONNECTION_AVAILABLE'
    );
    return $curl_error_codes[$code];
}

function RemoveAcentos($string) 
{
	$a = array(
				'/[ÂÀÁÄÃ]/'=>'A',
				'/[âãàáä]/'=>'a',
				'/[ÊÈÉË]/'=>'E',
				'/[êèéë]/'=>'e',
				'/[ÎÍÌÏ]/'=>'I',
				'/[îíìï]/'=>'i',
				'/[ÔÕÒÓÖ]/'=>'O',
				'/[ôõòóö]/'=>'o',
				'/[ÛÙÚÜ]/'=>'U',
				'/[ûúùü]/'=>'u',
				'/ç/'=>'c',
				'/Ç/'=> 'C'
				);
	
	// Tira o acento pela chave do array
	return preg_replace(array_keys($a), array_values($a), $string);
}

function validateData($data){
    return (empty($data['cep']) or empty($data['logradouro']) or empty($data['numero']) or empty($data['cidade']) or empty($data['uf']) or empty($data['bairro']));
}

function sendAlert($list,$dbEmail,$cronSystem){
    $txtSubject = "Endereços BI";

    foreach ($list as $key=>$value){
        switch ($key){
            case "EF9":
                $companyTitle = utf8_decode('Três Vendas');
                $courseID = 1;
                break;
            case "EM":
                $companyTitle = 'Mario Quintana';
                $courseID = 2;
                break;
            default:
                $companyTitle = 'MQ';
                $courseID = 3;
                break;
        }

        $tabList = "";
        foreach ($value as $item=>$val) {
            $tabList .= "<tr><td>{$val['studentName']}</td><td>{$val['address']}</td><td>{$val['googleAddress']}</td></tr>";
        }
        $size = sizeof($value);
        
        $body = $size > 0 ? formatBody($tabList,$size,$companyTitle) : formatBodyNoData($companyTitle);

        $retRecip = getAlertRecipients($courseID,$dbEmail,$cronSystem);
        if(!$retRecip){
            $cronSystem->logIt("Can't get recipients data. Program: ". __FILE__,3,'general',__LINE__);
            return false;
        }

        $params = array("subject" => $txtSubject,
            "contents" => $body,
            "sender_name" => "Tecnologia da Informação",
            "sender" => "ti@marioquintana.com.br",
            "address" => $retRecip,
            "idmodule" => $cronSystem->getIdModule("BIN"),
            "modulename" => "BIN",
            "msg" => "",
            "msg2" => "",
            "tracker" => false
        );

        $done = $cronSystem->sendEmailDefault($params);

        if($done){
            $cronSystem->logIt("Alert send. Company: {$companyTitle}. Program: ". __FILE__ ,6,'general');

        }else{
            $cronSystem->logIt("Alert not send. Company: {$companyTitle}. Program: ". __FILE__ ,3,'general',__LINE__);

        }
    }
}

function formatBody($body,$totalList,$companyTitle)
{
    $html = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
                <html xmlns='http://www.w3.org/1999/xhtml'>
                <head>
                    <meta name='viewport' content='width=device-width' />
                    <meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
                    <style type='text/css'>
                        /* -------------------------------------
                            GLOBAL
                            A very basic CSS reset
                        ------------------------------------- */
                        * {
                            margin: 0;
                            padding: 0;
                            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
                            box-sizing: border-box;
                            font-size: 14px;
                        }
                
                        img {
                            max-width: 100%;
                        }
                
                        body {
                            -webkit-font-smoothing: antialiased;
                            -webkit-text-size-adjust: none;
                            width: 100% !important;
                            height: 100%;
                            line-height: 1.6;
                        }
                
                        /* Let's make sure all tables have defaults */
                        table td {
                            vertical-align: top;
                        }
                
                        /* -------------------------------------
                            BODY & CONTAINER
                        ------------------------------------- */
                        body {
                            background-color: #f6f6f6;
                        }
                
                        .body-wrap {
                            background-color: #f6f6f6;
                            width: 100%;
                        }
                
                        .container {
                            display: block !important;
                            max-width: 1100px !important;
                            margin: 0 auto !important;
                            /* makes it centered */
                            clear: both !important;
                        }
                
                        .content {
                            max-width: 1100px;
                            margin: 0 auto;
                            display: block;
                            padding: 20px;
                        }
                
                        /* -------------------------------------
                            HEADER, FOOTER, MAIN
                        ------------------------------------- */
                        .main {
                            background: #fff;
                            border: 1px solid #e9e9e9;
                            border-radius: 3px;
                        }
                
                        .content-wrap {
                            padding: 20px;
                        }
                
                        .content-block {
                            padding: 0 0 20px;
                        }
                
                        .header {
                            width: 100%;
                            margin-bottom: 20px;
                        }
                
                        .footer {
                            width: 100%;
                            clear: both;
                            color: #999;
                            padding: 20px;
                        }
                        .footer a {
                            color: #999;
                        }
                        .footer p, .footer a, .footer unsubscribe, .footer td {
                            font-size: 12px;
                        }
                
                        /* -------------------------------------
                            TYPOGRAPHY
                        ------------------------------------- */
                        h1, h2, h3 {
                            font-family: 'Helvetica Neue', Helvetica, Arial, 'Lucida Grande', sans-serif;
                            color: #000;
                            margin: 40px 0 0;
                            line-height: 1.2;
                            font-weight: 400;
                        }
                
                        h1 {
                            font-size: 32px;
                            font-weight: 500;
                        }
                
                        h2 {
                            font-size: 24px;
                        }
                
                        h3 {
                            font-size: 18px;
                        }
                
                        h4 {
                            font-size: 14px;
                            font-weight: 600;
                        }
                
                        p, ul, ol {
                            margin-bottom: 10px;
                            font-weight: normal;
                        }
                        p li, ul li, ol li {
                            margin-left: 5px;
                            list-style-position: inside;
                        }
                
                        /* -------------------------------------
                            LINKS & BUTTONS
                        ------------------------------------- */
                        a {
                            color: #1ab394;
                            text-decoration: underline;
                        }
                
                        .btn-primary {
                            text-decoration: none;
                            color: #FFF;
                            background-color: #1ab394;
                            border: solid #1ab394;
                            border-width: 5px 10px;
                            line-height: 2;
                            font-weight: bold;
                            text-align: center;
                            cursor: pointer;
                            display: inline-block;
                            border-radius: 5px;
                            text-transform: capitalize;
                        }
                
                        /* -------------------------------------
                            OTHER STYLES THAT MIGHT BE USEFUL
                        ------------------------------------- */
                        .last {
                            margin-bottom: 0;
                        }
                
                        .first {
                            margin-top: 0;
                        }
                
                        .aligncenter {
                            text-align: center;
                        }
                
                        .alignright {
                            text-align: right;
                        }
                
                        .alignleft {
                            text-align: left;
                        }
                
                        .clear {
                            clear: both;
                        }
                        
                        .alignjustify {
                            text-align: justify;
                            text-justify: inter-word;
                        }	
                        
                
                        /* -------------------------------------
                            ALERTS
                            Change the class depending on warning email, good email or bad email
                        ------------------------------------- */
                        .alert {
                            font-size: 16px;
                            color: #fff;
                            font-weight: 500;
                            padding: 20px;
                            text-align: center;
                            border-radius: 3px 3px 0 0;
                        }
                        .alert a {
                            color: #fff;
                            text-decoration: none;
                            font-weight: 500;
                            font-size: 16px;
                        }
                        .alert.alert-warning {
                            background: #f8ac59;
                        }
                        .alert.alert-bad {
                            background: #ed5565;
                        }
                        .alert.alert-good {
                            background: #1ab394;
                        }
                
                        /* -------------------------------------
                            INVOICE
                            Styles for the billing table
                        ------------------------------------- */
                        .invoice {
                            margin: 40px auto;
                            text-align: left;
                            width: 100%;
                        }
                        .invoice td {
                            padding: 5px 0;
                        }
                        .invoice .invoice-items {
                            width: 100%;
                        }
                        .invoice .invoice-items td {
                            border-top: #eee 1px solid;
                        }
                        .invoice .invoice-items .total td {
                            border-top: 2px solid #333;
                            border-bottom: 2px solid #333;
                            font-weight: 700;
                        }
                
                        /* -------------------------------------
                            RESPONSIVE AND MOBILE FRIENDLY STYLES
                        ------------------------------------- */
                        @media only screen and (max-width: 640px) {
                            h1, h2, h3, h4 {
                                font-weight: 600 !important;
                                margin: 20px 0 5px !important;
                            }
                
                            h1 {
                                font-size: 22px !important;
                            }
                
                            h2 {
                                font-size: 18px !important;
                            }
                
                            h3 {
                                font-size: 16px !important;
                            }
                
                            .container {
                                width: 100% !important;
                            }
                
                            .content, .content-wrap {
                                padding: 10px !important;
                            }
                
                            .invoice {
                                width: 100% !important;
                            }
                        }
                    </style>
                </head>
                
                <body>
                    <table class='body-wrap'>
                        <tr>
                            <td></td>
                            <td class='container'>
                                <div class='content'>
                                    <table class='main' width='100%' cellpadding='0' cellspacing='0'>
                                        <tr>
                                            <td class='content-wrap aligncenter'>
                                                <table width='100%' cellpadding='0' cellspacing='0'>
                                                    <tr>
                                                        <td class='content-block'>
                                                            <h3>ALUNOS COM ENDERE&Ccedil;O INV&Aacute;LIDO</h3>
                                                        </td>
                                                    </tr>
                                                     <tr>
                                                        <td class='content-block'>
                                                            <h5>Unidade: {$companyTitle}</h5>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class='content-block'>
                                                            <table class='invoice'>
                                                                <tr>
                                                                    <td class='alignjustify'>Informamos que os alunos a seguir, possuem seus endere&ccedil;os inv&aacute;lidos.</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <table class='invoice-items' cellpadding='0' cellspacing='0'>
                                                                            <tr><td class='aligncenter'><h4>ALUNO</h4></td><td class='aligncenter'><h4>ENDERE&Ccedil;O CADASTRADO</h4></td><td class='aligncenter'><h4>CORRE&Ccedil;&Atilde;O</h4></td></tr>";

    $html .= $body;

    $html .= "                                                          </table>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class='alignright'>TOTAL</td>
                                                                    <td>$totalList</td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                    <div class='footer'>
                                        <table width='100%'>
                                            <tr>                                                
                                            </tr>
                                        </table>
                                    </div>
                                 </div>
                            </td>
                            <td></td>
                        </tr>
                    </table>";



    $html .= "</body>
              </html>";

    return $html;
}

function formatBodyNoData($companyTitle)
{
    $html = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
                <html xmlns='http://www.w3.org/1999/xhtml'>
                <head>
                    <meta name='viewport' content='width=device-width' />
                    <meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
                    <style type='text/css'>
                        /* -------------------------------------
                            GLOBAL
                            A very basic CSS reset
                        ------------------------------------- */
                        * {
                            margin: 0;
                            padding: 0;
                            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
                            box-sizing: border-box;
                            font-size: 14px;
                        }
                
                        img {
                            max-width: 100%;
                        }
                
                        body {
                            -webkit-font-smoothing: antialiased;
                            -webkit-text-size-adjust: none;
                            width: 100% !important;
                            height: 100%;
                            line-height: 1.6;
                        }
                
                        /* Let's make sure all tables have defaults */
                        table td {
                            vertical-align: top;
                        }
                
                        /* -------------------------------------
                            BODY & CONTAINER
                        ------------------------------------- */
                        body {
                            background-color: #f6f6f6;
                        }
                
                        .body-wrap {
                            background-color: #f6f6f6;
                            width: 100%;
                        }
                
                        .container {
                            display: block !important;
                            max-width: 1100px !important;
                            margin: 0 auto !important;
                            /* makes it centered */
                            clear: both !important;
                        }
                
                        .content {
                            max-width: 1100px;
                            margin: 0 auto;
                            display: block;
                            padding: 20px;
                        }
                
                        /* -------------------------------------
                            HEADER, FOOTER, MAIN
                        ------------------------------------- */
                        .main {
                            background: #fff;
                            border: 1px solid #e9e9e9;
                            border-radius: 3px;
                        }
                
                        .content-wrap {
                            padding: 20px;
                        }
                
                        .content-block {
                            padding: 0 0 20px;
                        }
                
                        .header {
                            width: 100%;
                            margin-bottom: 20px;
                        }
                
                        .footer {
                            width: 100%;
                            clear: both;
                            color: #999;
                            padding: 20px;
                        }
                        .footer a {
                            color: #999;
                        }
                        .footer p, .footer a, .footer unsubscribe, .footer td {
                            font-size: 12px;
                        }
                
                        /* -------------------------------------
                            TYPOGRAPHY
                        ------------------------------------- */
                        h1, h2, h3 {
                            font-family: 'Helvetica Neue', Helvetica, Arial, 'Lucida Grande', sans-serif;
                            color: #000;
                            margin: 40px 0 0;
                            line-height: 1.2;
                            font-weight: 400;
                        }
                
                        h1 {
                            font-size: 32px;
                            font-weight: 500;
                        }
                
                        h2 {
                            font-size: 24px;
                        }
                
                        h3 {
                            font-size: 18px;
                        }
                
                        h4 {
                            font-size: 14px;
                            font-weight: 600;
                        }
                
                        p, ul, ol {
                            margin-bottom: 10px;
                            font-weight: normal;
                        }
                        p li, ul li, ol li {
                            margin-left: 5px;
                            list-style-position: inside;
                        }
                
                        /* -------------------------------------
                            LINKS & BUTTONS
                        ------------------------------------- */
                        a {
                            color: #1ab394;
                            text-decoration: underline;
                        }
                
                        .btn-primary {
                            text-decoration: none;
                            color: #FFF;
                            background-color: #1ab394;
                            border: solid #1ab394;
                            border-width: 5px 10px;
                            line-height: 2;
                            font-weight: bold;
                            text-align: center;
                            cursor: pointer;
                            display: inline-block;
                            border-radius: 5px;
                            text-transform: capitalize;
                        }
                
                        /* -------------------------------------
                            OTHER STYLES THAT MIGHT BE USEFUL
                        ------------------------------------- */
                        .last {
                            margin-bottom: 0;
                        }
                
                        .first {
                            margin-top: 0;
                        }
                
                        .aligncenter {
                            text-align: center;
                        }
                
                        .alignright {
                            text-align: right;
                        }
                
                        .alignleft {
                            text-align: left;
                        }
                
                        .clear {
                            clear: both;
                        }
                        
                        .alignjustify {
                            text-align: justify;
                            text-justify: inter-word;
                        }	
                        
                
                        /* -------------------------------------
                            ALERTS
                            Change the class depending on warning email, good email or bad email
                        ------------------------------------- */
                        .alert {
                            font-size: 16px;
                            color: #fff;
                            font-weight: 500;
                            padding: 20px;
                            text-align: center;
                            border-radius: 3px 3px 0 0;
                        }
                        .alert a {
                            color: #fff;
                            text-decoration: none;
                            font-weight: 500;
                            font-size: 16px;
                        }
                        .alert.alert-warning {
                            background: #f8ac59;
                        }
                        .alert.alert-bad {
                            background: #ed5565;
                        }
                        .alert.alert-good {
                            background: #1ab394;
                        }
                
                        /* -------------------------------------
                            INVOICE
                            Styles for the billing table
                        ------------------------------------- */
                        .invoice {
                            margin: 40px auto;
                            text-align: left;
                            width: 100%;
                        }
                        .invoice td {
                            padding: 5px 0;
                        }
                        .invoice .invoice-items {
                            width: 100%;
                        }
                        .invoice .invoice-items td {
                            border-top: #eee 1px solid;
                        }
                        .invoice .invoice-items .total td {
                            border-top: 2px solid #333;
                            border-bottom: 2px solid #333;
                            font-weight: 700;
                        }
                
                        /* -------------------------------------
                            RESPONSIVE AND MOBILE FRIENDLY STYLES
                        ------------------------------------- */
                        @media only screen and (max-width: 640px) {
                            h1, h2, h3, h4 {
                                font-weight: 600 !important;
                                margin: 20px 0 5px !important;
                            }
                
                            h1 {
                                font-size: 22px !important;
                            }
                
                            h2 {
                                font-size: 18px !important;
                            }
                
                            h3 {
                                font-size: 16px !important;
                            }
                
                            .container {
                                width: 100% !important;
                            }
                
                            .content, .content-wrap {
                                padding: 10px !important;
                            }
                
                            .invoice {
                                width: 100% !important;
                            }
                        }
                    </style>
                </head>
                
                <body>
                    <table class='body-wrap'>
                        <tr>
                            <td></td>
                            <td class='container'>
                                <div class='content'>
                                    <table class='main' width='100%' cellpadding='0' cellspacing='0'>
                                        <tr>
                                            <td class='content-wrap aligncenter'>
                                                <table width='100%' cellpadding='0' cellspacing='0'>
                                                    <tr>
                                                        <td class='content-block'>
                                                            <h3>ALUNOS COM ENDERE&Ccedil;O INV&Aacute;LIDO</h3>
                                                        </td>
                                                    </tr>
                                                     <tr>
                                                        <td class='content-block'>
                                                            <h5>Unidade: {$companyTitle}</h5>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class='content-block'>
                                                            <table class='invoice'>
                                                                <tr>
                                                                    <td class='alignjustify'>Informamos que a unidade n&atilde;o possui alunos com endere&ccedil;os inv&aacute;lidos.</td>
                                                                </tr>
                                                                
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                    <div class='footer'>
                                        <table width='100%'>
                                            <tr>                                                
                                            </tr>
                                        </table>
                                    </div>
                                 </div>
                            </td>
                            <td></td>
                        </tr>
                    </table>";



    $html .= "</body>
              </html>";

    return $html;
}

function getAlertRecipients($courseID,$dbEmail,$cronSystem)
{
    $aRet = array();

    $ret = $dbEmail->getAcdAlertRecip("AND a.idcurso = {$courseID} AND b.status = 'A'");
    if(!$ret['success']){
        $cronSystem->logIt("{$ret['message']} - program: ". __FILE__ ." - function: getAlertRecipients",3,'general',__LINE__);
        return false;
    }

    $aRet = array();
    while(!$ret['data']->EOF){
        array_push($aRet,$ret['data']->fields['email']);

        $ret['data']->MoveNext();
    }

    //array_push($aRet,'valentin.acosta@marioquintana.com.br');

    return implode(';',$aRet);

}