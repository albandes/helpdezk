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

require_once(HELPDEZK_PATH . 'app/modules/cron-jobs/lib/classes/eem-api/eem.php');
require_once(HELPDEZK_PATH . 'app/modules/cron-jobs/lib/classes/zoom/zoom.php');

/*
 *  Models
 */
require_once (HELPDEZK_PATH.'app/modules/fin/models/bankslipemail_model.php');
require_once (HELPDEZK_PATH.'app/modules/emq/models/emqfeature_model.php');
require_once (HELPDEZK_PATH.'app/modules/acd/models/acdclass_model.php');
require_once (HELPDEZK_PATH.'app/modules/acd/models/acdstudent_model.php');
require_once (HELPDEZK_PATH.'app/modules/admin/models/index_model.php');
require_once (HELPDEZK_PATH.'app/modules/admin/models/tracker_model.php');

require_once (HELPDEZK_PATH.'system/common.php');

$cronSystem = new cronSystem();

$cronSystem->setCronSession() ;
$cronSystem->SetEnvironment();
$cronSystem->_tokenOperatorLink = false;

setLogVariables($cronSystem);
$lineBreak = $cronSystem->getLineBreak();

$dbBankSlip = new bankslipemail_model();
$dbClasses = new acdclass_model();
$dbEMQFeature = new emqfeature_model();
$dbStudent = new acdstudent_model();

$cronSystem->logIt("Start at ".date("d/m/Y H:i:s").' - Program: '. __FILE__ ,6,'general');

$zoom = returnZoomInstance($dbEMQFeature,$cronSystem);
if(!$zoom){
    echo "Undefined instance of Zoom API  \n";
    $cronSystem->logIt("Undefined instance of Zoom API - program: " . __FILE__ , 3, 'general', __LINE__);
    exit;
}

$paramsSize = sizeof($argv);

/*if($paramsSize <= 1){
    echo "There are no defined parameters \n";
    $cronSystem->logIt("There are no defined parameters - program: " . __FILE__ , 3, 'general', __LINE__);
    exit;
}*/

switch ($paramsSize){
    case 1:
        $from =  date("Y-m-d", mktime (0, 0, 0, date("m"), date("d")-1, date("Y")));
        $to =  date("Y-m-d", mktime (0, 0, 0, date("m"), date("d")-1, date("Y")));
        break;
    case 2:
        $from = $argv[1];
        $to = $argv[1];
        break;
    default:
        $from = $argv[1];
        $to = isset($argv[2]) ? $argv[2] : $argv[1];
        break;
}

$starttime = date("d/m/Y H:i:s");
$cronSystem->logIt("Start at {$starttime} - program: ". __FILE__ ,6,'general');

$n = 0;
$aMeetings = array(
    "subject" => "Participantes Zoom",
    "recipients" => array(
        array(
            "identificador" => "valentin.acosta",
            "nome" => 'Valentín Ismael León Acosta',
            "tipoDestinatario" => "USUARIO"
        )
    )
);

$aDefaultMettings = defaultMeetings($cronSystem);

$query = array(
    "type" => 'past',
    "from" => $from,
    "to" => $to,
    "page_size" => isset($params['pagesize']) ? $params['pagesize'] : 300
);

$retMeetings = $zoom->getPastMeetings($query);
//echo "",print_r($retMeetings,true),"\n";
if(!$retMeetings['success']){
    $cronSystem->logIt("API Error: {$retMeetings['message']}. - program: " . __FILE__ , 3, 'general', __LINE__);
}else{
    $meetingList = "";
    
    foreach ($retMeetings['return']['meetings'] as $key=>$value){
        //echo "{$value['id']}\n";
        if(in_array($value['id'],$aDefaultMettings)){

            $ret = $zoom->getMeetingParticipants(encodeID($value['uuid']),$query);
            if(!$ret['success']){
                $cronSystem->logIt("API Error: {$ret['message']}. - program: " . __FILE__ , 3, 'general', __LINE__);
            }elseif($ret['success'] && isset($ret['return']['code'])){
                $cronSystem->logIt("{$ret['return']['message']} - program: " . __FILE__ , 3, 'general', __LINE__);
            }

            $meetingList .= "ID: {$value['id']} \t Sala: {$value['topic']} \t Registros: {$ret['return']['total_records']} \n";

            foreach ($ret['return']['participants'] as $k=>$v){
                
                if(isset($v['id'])){
                    $retStudentID = getStudentID($v['id'],$v['user_name'],$zoom,$cronSystem,$dbStudent);
                }else{
                    $retStudentID = getStudentIDByZoomUserName($v['user_name'],$cronSystem,$dbStudent);
                }
                $studentIntraID = !$retStudentID ? "" : $retStudentID;
                
                if(!isset($v['id']) && $studentIntraID == "")
                    $cronSystem->logIt("User: {$v['id']} - {$v['user_name']} - {$studentIntraID}",6,'general',__LINE__);

                $body = array(
                    "id" => $v['id'],
                    "user_id" => $v['user_id'],
                    "user_name" =>  $v['user_name'],
                    "device" =>  $v['device'],
                    "location" =>  $v['location'],
                    "network_type" =>  $v['network_type'],
                    "join_time" =>  $v['join_time'],
                    "leave_time" =>  $v['leave_time'],
                    "pc_name" =>  $v['pc_name'],
                    "CoPessoa" => $studentIntraID,
                    "zoomid" => $value['id']
                );
                //echo"",print_r($body,true),"\n";
                $link2 = "/insertparticipant";
                $ret2 = request('POST',$link2,$body,$cronSystem);
                //echo "<pre>", print_r($ret2,true), "</pre> \n";

                if($ret2['success']) {
                    if(!$ret2['return']['error'])
                        $n++;
                }else{
                    $cronSystem->logIt("Didn't insert participant in db. - program: " . __FILE__ , 3, 'general', __LINE__);
                }

            }

            if($ret['return']['next_page_token'] != '')
                processNextPage($value['uuid'],$ret['return']['next_page_token'],$value['id'],$zoom,$cronSystem,$dbStudent);

        }

    }
}

$endtime = date("d/m/Y H:i:s");
$methodName = "getparticipants";
$msgAlert = "Programa: acd_getzoomparticipants\tMétodo:{$methodName}\n\nRodou das {$starttime} às {$endtime}.\n\n";
$msgAlert .= $meetingList != "" ? "Data da Aulas: {$cronSystem->formatDate($from)}\nSalas Processadas:\n{$meetingList}" : "";
$aMeetings['body'] = $msgAlert;
if(sendAlert($aMeetings,$cronSystem))
    $cronSystem->logIt("Message sent by push",6,'general');

$msg = "Finish at {$endtime}. Program: ". __FILE__;
$cronSystem->logIt($msg,6,'general');

die('Ran OK ') ;

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

function returnZoomInstance($dbEMQFeature,$cronSystem)
{
    $ret = $dbEMQFeature->getEmqFeaturesData();
    if(!$ret){
        $cronSystem->logIt("Can't get Config Data - program: " . __FILE__ . " - function: returnZoomInstance", 3, 'general', __LINE__);
        return false;
    }

    $apiKey = "";
    $apiSecret = "";
    while(!$ret->EOF){
        switch ($ret->fields['session_name']){
            case 'zoom_api_key':
                $apiKey = $ret->fields['value'];
                break;
            case'zoom_api_secret':
                $apiSecret = $ret->fields['value'];
            default:
                break;
        }
        $ret->MoveNext();
    }

    if($apiKey != '' && $apiSecret != ''){
        return new zoom($apiKey, $apiSecret);
    }else{
        return false;
    }

}

function getAPIFeature($featName,$cronSystem)
{
    $dbEMQFeature = new emqfeature_model();

    $ret = $dbEMQFeature->getEmqFeaturesData("WHERE session_name = '{$featName}'");
    if(!$ret){
        $cronSystem->logIt("Can't get Config Data - Program: " . __FILE__ , 3, 'general', __LINE__);
        return false;
    }

    return $ret->fields['value'];

}

function updateStatusNotSend($idrecipient,$cronSystem)
{
    $dbEmail2 = new bankslipemail_model();
    $ret = $dbEmail2->updateRecipientStatus($idrecipient,4);

    if(!$ret){
        $cronSystem->logIt("Can't update status recipient {$idrecipient}. Program: ". __FILE__,3,'general',__LINE__);
        return false;
    }

    return true;
}

function uploadFile($filePath,$cronSystem)
{
    $apiEemBaseUrl = getAPIFeature('eem_base_url',$cronSystem);
    $apiEemToken = getAPIFeature('eem_token',$cronSystem);

    $eem = new EeM();
    $eem->setToken($apiEemToken);

    return $eem->postFile($apiEemBaseUrl.'api/arquivo/upload',$filePath);
}

function sendAlert($params,$cronSystem)
{
    $aMsg = array(
        "tipoMensagem" => "COMUNICADO",
        "sumario" => $params['subject'],
        "corpo" => $params['body'],
        "tipoResposta" => "SEM RESPOSTA",
        "diasEsperandoResposta" => 0,
        "segmentoTipoDestinatario" => "RESPONSAVEIS",
        "destinatarios" => $params['recipients'],
        "anexos" => array()
    );

    $apiEemBaseUrl = getAPIFeature('eem_base_url',$cronSystem);
    $apiEemToken = getAPIFeature('eem_token',$cronSystem);

    $eem = new EeM();
    $eem->setToken($apiEemToken);

    $ret = $eem->postMessage($apiEemBaseUrl.'api/mensagem/enviar/v1',$aMsg);
    $ret = json_decode($ret,true);

    if (!$ret || ($ret && $ret['mensagemRetorno'])) {
        $cronSystem->logIt($ret['mensagemRetorno'] . ' - Program: '. __FILE__ ,3,'general',__LINE__);
        return false;
    }
    return true;
}

function defaultMeetings($cronSystem)
{

    $link = "/getdefaultmeetings";
    $ret = request('GET',$link,false,$cronSystem);
    
    $aRet = array();
    foreach ($ret['return'] as $item=>$value) {
        array_push($aRet,$value['zoomid']);
    }

    return $aRet;

}

function getStudentID($zoomUserID,$zoomUserName,$zoom,$cronSystem,$dbStudent)
{
    $zoomUserName = addslashes(utf8_decode($zoomUserName));

    $retEmail = $zoom->getParticipantData(encodeID($zoomUserID));
    if(!$retEmail['success']){
        $cronSystem->logIt("API Not success - Error: {$retEmail['message']}. - program: " . __FILE__ , 3, 'general', __LINE__);
        $where = "AND pipeLatinToUtf8(b.name) LIKE '{$zoomUserName}'";
    }else{
        if(!isset($retEmail['return']['email'])){
            $cronSystem->logIt("API Error: {$retEmail['return']['message']} - {$zoomUserID}/{$zoomUserName}. - program: " . __FILE__ , 3, 'general', __LINE__);
            $where = "AND pipeLatinToUtf8(b.name) LIKE '{$zoomUserName}'";
        }else{
            $where = "AND b.company_email LIKE '{$retEmail['return']['email']}'";
        }
    }

    $retLogin = $dbStudent->getStudentData($where);
    if(!$retLogin){
        $cronSystem->logIt("Can't get Student Data - program: " . __FILE__ , 3, 'general', __LINE__);
        return false;
    }

    if($retLogin->fields['idintranet'] || $retLogin->fields['idperseus']){
        $login = ($retLogin->fields['idintranet'] && ($retLogin->fields['idintranet'] != "" || $retLogin->fields['idintranet'] != " ")) ? $retLogin->fields['idintranet'] : $retLogin->fields['idperseus'];
        $link = "/getstudentidintra/{$login}";
        $ret = request('GET',$link,false,$cronSystem);
        
        return $ret['return'][0]['CoPessoa'];
    }else{
        return false;
    }

}

function getStudentIDByZoomUserName($zoomUserName,$cronSystem,$dbStudent)
{
    $zoomUserName = addslashes(utf8_decode($zoomUserName));

    $retLogin = $dbStudent->getStudentData("AND pipeLatinToUtf8(b.name) LIKE '{$zoomUserName}'");
    if(!$retLogin){
        $cronSystem->logIt("Can't get Student Data - program: " . __FILE__ , 3, 'general', __LINE__);
        return false;
    }

    if($retLogin->fields['idintranet'] || $retLogin->fields['idperseus']){
        $login = ($retLogin->fields['idintranet'] && ($retLogin->fields['idintranet'] != "" || $retLogin->fields['idintranet'] != " ")) ? $retLogin->fields['idintranet'] : $retLogin->fields['idperseus'];
        $link = "/getstudentidintra/{$login}";
        $ret = request('GET',$link,false,$cronSystem);

        return $ret['return'][0]['CoPessoa'];
    }else{
        return false;
    }

}

function processNextPage($meetingUUID,$token,$meetingID,$zoom,$cronSystem,$dbStudent)
{
    $query = array(
        "type" => 'past',
        "page_size" => 300,
        "next_page_token" => $token
    );

    $ret = $zoom->getMeetingParticipants(encodeID($meetingUUID),$query);
    if(!$ret['success']){
        $cronSystem->logIt("API Error: {$ret['message']}. - program: " . __FILE__ . ' - function: processNextPage', 3, 'general', __LINE__);
    }elseif($ret['success'] && isset($ret['return']['code'])){
        $cronSystem->logIt("{$ret['return']['message']} - program: " . __FILE__ . ' - function: processNextPage', 3, 'general', __LINE__);
    }

    foreach ($ret['return']['participants'] as $k=>$v){

        if(isset($v['id'])){
            $retStudentID = getStudentID($v['id'],$v['user_name'],$zoom,$cronSystem,$dbStudent);
        }else{
            $retStudentID = getStudentIDByZoomUserName($v['user_name'],$cronSystem,$dbStudent);
        }
        $studentIntraID = !$retStudentID ? "" : $retStudentID;
        
        if(!isset($v['id']) && $studentIntraID == "")
            $cronSystem->logIt("User: {$v['id']} - {$v['user_name']} - {$studentIntraID}",6,'general',__LINE__);

        $body = array(
            "id" => $v['id'],
            "user_id" => $v['user_id'],
            "user_name" =>  $v['user_name'],
            "device" =>  $v['device'],
            "location" =>  $v['location'],
            "network_type" =>  $v['network_type'],
            "join_time" =>  $v['join_time'],
            "leave_time" =>  $v['leave_time'],
            "pc_name" =>  $v['pc_name'],
            "CoPessoa" => $studentIntraID,
            "zoomid" => $meetingID
        );
        //echo"",print_r($body,true),"\n";
        $link2 = "/insertparticipant";
        $ret2 = request('POST',$link2,$body,$cronSystem);
        //echo "<pre>", print_r($ret2,true), "</pre> \n";

        if($ret2['success']) {
            if($ret2['return']['error'])
                $cronSystem->logIt("Didn't insert participant {$v['user_name']} in db. Error: {$ret2['return']['error']} - program: " . __FILE__ . ' - function: processNextPage', 3, 'general', __LINE__);
        }else{
            $cronSystem->logIt("Didn't insert participant {$v['user_name']} in db. Error: {$ret2['message']} - program: " . __FILE__ . ' - function: processNextPage', 3, 'general', __LINE__);
        }

    }

    if($ret['return']['next_page_token'] != '')
        processNextPage($meetingUUID,$ret['return']['next_page_token'],$meetingID,$zoom,$cronSystem,$dbStudent);
    else
        return;

}

function encodeID($string)
{
    return urlencode(urlencode($string));
}
