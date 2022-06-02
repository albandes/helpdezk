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
if(!setRequire(HELPDEZK_PATH.'app/modules/fin/models/bankslipemail_model.php',$cronSystem)){exit;}
if(!setRequire(HELPDEZK_PATH.'app/modules/emq/models/emqfeature_model.php',$cronSystem)){exit;}
if(!setRequire(HELPDEZK_PATH.'app/modules/acd/models/acdclass_model.php',$cronSystem)){exit;}
if(!setRequire(HELPDEZK_PATH.'app/modules/acd/models/acdstudent_model.php',$cronSystem)){exit;}

$dbBankSlip = new bankslipemail_model();
$dbClasses = new acdclass_model();
$dbEMQFeature = new emqfeature_model();
$dbStudent = new acdstudent_model();

$apiEemBaseUrl = getAPIFeature('eem_base_url',$dbEMQFeature,$cronSystem);
$apiEemToken = getAPIFeature('eem_token',$dbEMQFeature,$cronSystem);
$currentYear = date("Y");

$eem = new EeM();
$eem->setToken($apiEemToken);

postStudentNewEnrollment($dbStudent,$currentYear,$eem,$apiEemBaseUrl,$cronSystem);
postUserStudentNewEnrollment($dbStudent,$currentYear,$eem,$apiEemBaseUrl,$cronSystem);
postUserParentNewEnrollment($dbStudent,$currentYear,$eem,$apiEemBaseUrl,$cronSystem);
postNewStudentInClass($dbStudent,$currentYear,$eem,$apiEemBaseUrl,$cronSystem);
postBindStudentUserNewEnrollment($dbStudent,$currentYear,$eem,$apiEemBaseUrl,$cronSystem);

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

function getAPIFeature($featName,$dbEMQFeature,$cronSystem)
{
    $ret = $dbEMQFeature->getEmqFeaturesData("WHERE session_name = '{$featName}'");
    if(!$ret){
        $cronSystem->logIt("Can't get Config Data - Program: " . __FILE__ , 3, 'general', __LINE__);
        return false;
    }

    return $ret->fields['value'];

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

function postStudentNewEnrollment($dbStudent,$currentYear,$eem,$apiEemBaseUrl,$cronSystem){

    $cronSystem->logIt("Start postStudentNewEnrollment, at ".date("d/m/Y H:i:s").' - Program: ' . __FILE__ ,6,'general');
    $nextYear = $currentYear + 1;

    $where = "AND e.year = {$nextYear} AND e.idstatusenrollment = 1 AND e.record_status = 'A'  AND ser.idcurso IN (1,2,3)";
    $order = "ORDER BY nome";
    $ret = $dbStudent->getStudentDataApp($where,$order);
    $aSend = array();
    $n = 0;

    while(!$ret->EOF){
        $retStActive = $dbStudent->getTotalStatusActive($ret->fields['idstudent'],$nextYear);
        if(!$retStActive){
            $cronSystem->logIt("Can't get Student Data - Method: postStudentNewEnrollment - Program: "  . __FILE__ , 3, 'general', __LINE__);
            continue;
        }

        $recordSt = $retStActive->fields['total_active'] > 0 ? 1 : 0;

        $bus = array (
            "registroAcademico"     => $ret->fields['registroacademico'],
            "nome"                  => utf8_encode($ret->fields['nome']),
            "apelido"               => null,
            "dataNascimento"        => $ret->fields['datanascimento'],
            "sexo"                  =>  ($ret->fields['sexo'] == "Masculino" ? "M" : "F"),
            "ativo"                 => $recordSt,
            "idIntegracao"          => $ret->fields['idstudent']
        );

        array_push($aSend,$bus);
        $n++;
        $ret->MoveNext();
    }
    //$row=sizeof($aSend); echo "{$row}\n", print_r($aSend,true),"\n";
    $data = $eem->makeData(time(),$aSend);

    $sended = json_decode($eem->httpPost($apiEemBaseUrl . 'api/aluno/integracao/v1',$data),true);

    if(isset($sended['status']) && $sended['status'] == 0){
        $msg = "Finish postStudentNewEnrollment, at ".date("d/m/Y H:i:s")." Total records integrated: {$n}. Program: " . __FILE__;
    }else{
        $msg = "Finish postStudentNewEnrollment, at ".date("d/m/Y H:i:s").". Status: {$sended['status']}. Error Message: {$sended['mensagemRetorno']}. Program: " . __FILE__;
    }
    $cronSystem->logIt($msg,6,'general');

}

function postUserStudentNewEnrollment($dbStudent,$currentYear,$eem,$apiEemBaseUrl,$cronSystem){

    $cronSystem->logIt("Start postUserStudentNewEnrollment, at ".date("d/m/Y H:i:s")." - Program: " . __FILE__ ,6,'general');
    $nextYear = $currentYear + 1;

    $where = "AND e.year = {$nextYear} AND e.idstatusenrollment = 1 AND e.record_status = 'A'  AND ser.idcurso IN (1,2,3)";
    $order = "ORDER BY nome";
    $ret = $dbStudent->getStudentDataApp($where,$order);
    $aSend = array();
    $n = 0;

    while(!$ret->EOF){
        $retStActive = $dbStudent->getTotalStatusActive($ret->fields['idstudent'],$nextYear);
        if(!$retStActive){
            $cronSystem->logIt("Can't get Student Data  - Method: postUserStudentNewEnrollment - Program: " . __FILE__ , 3, 'general', __LINE__);
            continue;
        }

        $recordSt = $retStActive->fields['total_active'] > 0 ? 1 : 0;

        $bus = array (
            "nome"                  => utf8_encode($ret->fields['nome']),
            "login"                 => $ret->fields['registroacademico'],
            "email"                 => "",
            "idPerfil"              => 6,
            "telefoneFixo"          => "",
            "celular"               => "",
            "ativo"                 => $recordSt,
            "idIntegracao"          => "aluno-".$ret->fields['idstudent'],
            "isAutenticacaoInterna" => 0
        );

        array_push($aSend,$bus);
        $n++;
        $ret->MoveNext();
    }
    //$row=sizeof($aSend); echo "{$row}\n", print_r($aSend,true),"\n";
    $data = $eem->makeData(time(),$aSend);

    $sended = json_decode($eem->httpPost($apiEemBaseUrl . 'api/usuario/integracao/v1',$data),true);

    if(isset($sended['status']) && $sended['status'] == 0){
        $msg = "Finish postUserStudentNewEnrollment, at ".date("d/m/Y H:i:s")." Total records integrated: {$n}. Program: " . __FILE__;
    }else{
        $msg = "Finish postUserStudentNewEnrollment, at ".date("d/m/Y H:i:s").". Status: {$sended['status']}. Error Message: {$sended['mensagemRetorno']}. Program: " . __FILE__;
    }
    $cronSystem->logIt($msg,6,'general');

}

function postUserParentNewEnrollment($dbStudent,$currentYear,$eem,$apiEemBaseUrl,$cronSystem){
    
    $cronSystem->logIt("Start postUserParentNewEnrollment, at ".date("d/m/Y H:i:s").' - Program: ' . __FILE__ ,6,'general');
    $nextYear = $currentYear + 1;

    $where = "AND e.`year` = {$nextYear}
                AND shp.access_app = 'Y'
                AND (p.cpf != '' AND p.cpf IS NOT NULL)
                AND e.record_status = 'A'
                AND e.idstatusenrollment = 1";
    $group = "GROUP BY shp.idparent";
    $order = "ORDER BY `name`";


    $ret = $dbStudent->getParentApp($where,$group,$order);
    $aSend = array();
    $n = 0;

    while(!$ret->EOF){
        //echo "<pre>", print_r($ret->fields, true), "</pre> \n";
        $aChilds = explode(",",$ret->fields['childs']);
        $countSt = 0;

        foreach ($aChilds as $key=>$value){

            $retStActive = $dbStudent->getTotalStatusActive($value,$nextYear);
            if(!$retStActive){
                $cronSystem->logIt("Can't get child's enrollment status - Method: postUserParentNewEnrollment - Program: " . __FILE__ , 3, 'general', __LINE__);
                continue;
            }

            if($retStActive->fields['total_active'] > 0) $countSt++;
        }

        $recordSt = $countSt > 0 ? 1 : 0;

        $bus = array (
            "nome"                  => utf8_encode($ret->fields['nome']),
            "login"                 => $ret->fields['login'],
            "email"                 => $ret->fields['email'],
            "idPerfil"              => 5,
            "telefoneFixo"          => "",
            "celular"               => "",
            "ativo"                 => $recordSt,
            "idIntegracao"          => "responsavel-".$ret->fields['idparent'],
            "isAutenticacaoInterna" => 0
        );

        array_push($aSend,$bus);
        $n++;
        $ret->MoveNext();
    }
    //$row=sizeof($aSend); echo "{$row}\n", print_r($aSend,true),"\n";
    $data = $eem->makeData(time(),$aSend);

    $sended = json_decode($eem->httpPost($apiEemBaseUrl . 'api/usuario/integracao/v1',$data),true);

    if(isset($sended['status']) && $sended['status'] == 0){
        $msg = "Finish postUserParentNewEnrollment, at ".date("d/m/Y H:i:s")." Total records integrated: {$n}. Program: " . __FILE__;
    }else{
        $msg = "Finish postUserParentNewEnrollment, at ".date("d/m/Y H:i:s").". Status: {$sended['status']}. Error Message: {$sended['mensagemRetorno']}. Program: " . __FILE__;
    }
    $cronSystem->logIt($msg,6,'general');

}

function postNewStudentInClass($dbStudent,$currentYear,$eem,$apiEemBaseUrl,$cronSystem){
    
    $cronSystem->logIt("Start postNewStudentInClass, at ".date("d/m/Y H:i:s").' - Program: '. __FILE__ ,6,'general');
    $nextYear = $currentYear + 1;

    $where = "AND e.year = {$nextYear} AND e.idstatusenrollment = 1 AND e.record_status = 'A'  AND s.idcurso IN (1,2,3)";
    $ret = $dbStudent->getStudentInClass($where);
    $aSend = array();
    $n = 0;

    while(!$ret->EOF){
        $recordSt = $ret->fields['record_status'] == "A" ? 1 : 0;

        $bus = array (
            "idIntegracaoTurma"     => 'nov'.$nextYear,
            "idIntegracaoAluno"     => $ret->fields['idstudent'],
            "ativo" => $recordSt
        );

        array_push($aSend,$bus);
        $n++;
        $ret->MoveNext();
    }
    //$row=sizeof($aSend); echo "{$row}\n", print_r($aSend,true),"\n";
    $data = $eem->makeData(time(),$aSend);

    $sended = json_decode($eem->httpPost($apiEemBaseUrl . 'api/TurmaAluno/integracao/v1',$data),true);

    if(isset($sended['status']) && $sended['status'] == 0){
        $msg = "Finish postNewStudentInClass, at ".date("d/m/Y H:i:s")." Total records integrated: {$n}. Program: " . __FILE__;
    }else{
        $msg = "Finish postNewStudentInClass, at ".date("d/m/Y H:i:s").". Status: {$sended['status']}. Error Message: {$sended['mensagemRetorno']}. Program: " . __FILE__;
    }
    $cronSystem->logIt($msg,6,'general');

}

function postBindStudentUserNewEnrollment($dbStudent,$currentYear,$eem,$apiEemBaseUrl,$cronSystem){

    $cronSystem->logIt("Start postBindStudentUserNewEnrollment, at ".date("d/m/Y H:i:s").' - Program: ' . __FILE__ ,6,'general');
    $nextYear = $currentYear + 1;

    $ret = $dbStudent->getBindStudentUserNewEnrollment($nextYear);
    $aSend = array();
    $n = 0; $i = 1;

    while(!$ret->EOF){
        //echo "{$i} :: {$ret->fields['user_legacy']} - {$ret->fields['student_legacy']} - {$ret->fields['tipo']}\n"; $i++;
        if($ret->fields['tipo'] == 'R'){

            $link = "/respfinanceiro/{{$ret->fields['user_legacy']}}/{$ret->fields['student_legacy']}/{$nextYear}";
            $retIsResp = request('GET',$link,false,$cronSystem);
            if(!$retIsResp['success']){
                if ($this->log)
                    $cronSystem->logIt("No result return - User: " . $_SESSION['SES_LOGIN_PERSON'] . ' - Method: postBindStudentUserNewEnrollment - Program: ' . __FILE__, 3, 'general', __LINE__);
            }
            
            if($retIsResp['success'] && !empty($retIsResp['return'])){
                $isRFI = 1;
                $perms = "7,8,43,125";
            }else{
                $isRFI = 0;
                $perms = "7,8,43";
            }

        }else{
            $isRFI = 0;
            $perms = "7,8,43";
        }

        $bus = array (
            "idIntegracaoAluno"     => $ret->fields['idstudent'],
            "idIntegracaoUsuario"   => $ret->fields['iduser'],
            "permissoes"            => $perms,
            "tipo"                  => $ret->fields['tipo'],
            "ativo"                 => 1,
            "IsResponsavelFinanceiro" => $isRFI
        );

        array_push($aSend,$bus);
        $n++;

        $ret->MoveNext();
    }
    //$row=sizeof($aSend); echo "{$row}\n", print_r($aSend,true),"\n";
    $data = $eem->makeData(time(),$aSend);
    //echo "",print_r($data,true),"\n";
    $sended = json_decode($eem->httpPost($apiEemBaseUrl . 'api/AlunoUsuario/integracao/v1',$data),true);
    //echo "",print_r($sended,true),"\n";
    if(isset($sended['status']) && $sended['status'] == 0){
        $msg = "Finish postBindStudentUserNewEnrollment, at ".date("d/m/Y H:i:s")." Total records integrated: {$n}. Program: " . __FILE__;
    }else{
        $msg = "Finish postBindStudentUserNewEnrollment, at ".date("d/m/Y H:i:s").". Status: {$sended['status']}. Error Message: {$sended['mensagemRetorno']}. Program: " . __FILE__;
    }
    $cronSystem->logIt($msg,6,'general');

}
