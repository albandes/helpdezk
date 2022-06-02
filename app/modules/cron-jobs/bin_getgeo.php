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

$admFeature = new features_model();
$dbGeoref = new georeferencing_model();

$retFeat = getAPIFeature("bin",1,$admFeature,$cronSystem); 
$currentYear = date("Y");
$limit  = 9999 ;

$cronSystem->logIt("Start at ".date("d/m/Y H:i:s")." - Program: ". __FILE__ ,6,'general');

$link = "/georefdata/$currentYear";
$ret = request('GET',$link,false,$cronSystem);
if(!$ret['success']){
    $cronSystem->logIt("No result return. Program: " . __FILE__ , 3, 'general', __LINE__);
    $cronSystem->logIt("Finish at ".date("d/m/Y H:i:s").'. No records to process. Program: '. __FILE__ ,6,'general');
    exit;
}

$retStGeo = $dbGeoref->resetGeoStatus("WHERE ano = {$currentYear}");
if(!$retStGeo['success']){
    $cronSystem->logIt("Can't reset enrollment status. - Program: " . __FILE__ , 3, 'general', __LINE__);
}

$i = 0;
$aOK = array();
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

    echo "Student: {$value['matricula']} - {$value['nome']} - {$value['stmatricula']}.\n";
    $action = isItToRecord($value,$dbGeoref,$cronSystem);
    if(!$action){
        continue;
    }
    
    if ($action == 'nothing') {
        $cronSystem->logIt("Data OK, do not update student: {$value['matricula']} - {$value['nome']}. Program: ". __FILE__ ,6,'general');
        continue;
    }

    $address = "{$value['cep']} {$value['tipologradouro']} {$value['logradouro']} {$value['numero']} {$value['cidade']} {$value['uf']} Brasil";
    $address = str_replace(" ", "+", $address);
    
    $attempts = 0 ;
    $success = false;
    
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
        array_push($aMensagens, "Sem resultado no Google ". $value['matricula'] . " - " . utf8_decode($value['nome']) . " End: " . utf8_decode($address));
        continue;
    }

    $value['google_lat'] = $json["results"][0]["geometry"]["location"]["lat"] ;
    $value['google_lng'] = $json["results"][0]["geometry"]["location"]["lng"];
    $value['google_city'] = utf8_decode($json["results"][0]["address_components"][2]["long_name"]);
    $google_state = $json["results"][0]["address_components"][4]["short_name"];
    if(empty($google_state)) {
        $$google_state = '';
        $cronSystem->logIt("Student: {$value['matricula']} - Address: {$address} {$google_state} returned empty!!! - . Program: ". __FILE__ ,3,'general');
    }

    $$value['google_formatted_address'] = addslashes($json["results"][0]["formatted_address"]);

    if($action == 'create') {
        $sql = 	createStudentGeo($value,$dbGeoref,$cronSystem);
    } elseif($action == 'update') {
        $sql = 	updateStudentGeo($value,$dbGeoref,$cronSystem);
    }

    if($ret['success']) {
        $cronSystem->logIt("Updated!! Student: {$value['matricula']} - {$value['nome']}. Program: ". __FILE__ ,6,'general');
        array_push($aOK,$value['matricula']);
    }

    $i++;
    if ($i > $limit)
        break;
}

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
        $message = 'Error making API request, curl error: ' . getCurlErrorCode(curl_error($c)); echo"{$message}\n";
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

function isItToRecord($data,$dbGeoref,$cronSystem)
{
	$rsLvl1 = $dbGeoref->getAlunoGeo("WHERE idperseus = '{$data['matricula']}' and ano = {$data['ano']}");
    if(!$rsLvl1){
        $cronSystem->logIt("Can't get student data - Program: " . __FILE__ , 3, 'general', __LINE__);
        return false;
    }
    
    if($rsLvl1->RecordCount() == 0) 
		return 'create';
    
    $where = "WHERE idperseus ='{$data['matricula']}'
                AND ((idcurso != '{$data['idcurso']}' OR NULLIF(idcurso, ' ') IS NULL) OR
                     (serie != '1' OR NULLIF(serie, ' ') IS NULL) OR
                     ((turma != '1' AND turma != '{$data['turma']}') OR NULLIF(turma, ' ') IS NULL) OR
                     (tipologradouro != '1' OR NULLIF(tipologradouro, ' ') IS NULL) OR
                     (logradouro != '1' OR NULLIF(logradouro, ' ') IS NULL) OR
                     (numero != '1' OR NULLIF(numero, ' ') IS NULL) OR
                     (complemento != '1' OR NULLIF(complemento, ' ') IS NULL) OR
                     (cidade != '1' OR NULLIF(cidade, ' ') IS NULL) OR
                     (bairro != '1' OR NULLIF(bairro, ' ') IS NULL) OR
                     (uf != '1' OR NULLIF(uf, ' ') IS NULL) OR
                     (cep != '1' OR NULLIF(cep, ' ') IS NULL) OR
                     (NULLIF(google_lat, ' ') IS NULL) OR
                     (NULLIF(google_lng, ' ') IS NULL) OR 
                     (ano != '1' OR NULLIF(ano, ' ') IS NULL OR ano IS NULL) OR 
                     (status_matricula != '1' OR NULLIF(status_matricula, ' ') IS NULL 
                      OR status_matricula IS NULL))
                AND ano = {$data['ano']}
                AND status_matricula = '{$data['stmatricula']}'";
    $rsLvl2 = $dbGeoref->getAlunoGeo($where);
    if(!$rsLvl2){
        $cronSystem->logIt("Can't get student data - Program: " . __FILE__ , 3, 'general', __LINE__);
        return false;
    }

    return ($rsLvl2->RecordCount() == 0) ? 'update' : 'nothing';	
	
}

function validateData($data){
    return (empty($data['cep']) or empty($data['logradouro']) or empty($data['numero']) or empty($data['cidade']) or empty($data['uf']) or empty($data['bairro']));
}

function createStudentGeo($data,$dbGeoref,$cronSystem)
{	
    $ret = $dbGeoref->insertGeoData($data['matricula'],$data['nome'],$data['google_lat'],$data['google_lng'],$data['google_formatted_address'],$data['idcurso'],$data['serie'],$data['turma'],$data['idempresa'],$data['nomeempresa'],$data['tipologradouro'],$data['logradouro'],$data['numero'],$data['complemento'],$data['cidade'],$data['bairro'],$data['uf'],$data['cep'],$data['ano'],$data['stmatricula']);
    if(!$ret['success']){
        $cronSystem->logIt("Can't insert student data. - Program: " . __FILE__ , 3, 'general', __LINE__);
    }
	
    return $ret;
}

function updateStudentGeo($data,$dbGeoref,$cronSystem)
{
	$ret = $dbGeoref->updateGeoData($data['matricula'],$data['nome'],$data['google_lat'],$data['google_lng'],$data['google_formatted_address'],$data['idcurso'],$data['serie'],$data['turma'],$data['idempresa'],$data['nomeempresa'],$data['tipologradouro'],$data['logradouro'],$data['numero'],$data['complemento'],$data['cidade'],$data['bairro'],$data['uf'],$data['cep'],$data['ano'],$data['stmatricula']);
    if(!$ret['success']){
        $cronSystem->logIt("Can't update student data. - Program: " . __FILE__ , 3, 'general', __LINE__);
    }
    
    return $ret;	
}