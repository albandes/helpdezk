<?php

$debug = true ;

if ($debug)
    error_reporting(E_ERROR | E_WARNING | E_PARSE);
else
    error_reporting(0);

set_time_limit(0);
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

/*
 *  Models
 */
require_once (HELPDEZK_PATH.'app/modules/fin/models/bankslipemail_model.php');
require_once (HELPDEZK_PATH.'app/modules/acd/models/acdclass_model.php');
require_once (HELPDEZK_PATH.'app/modules/acd/models/acdstudent_model.php');
require_once (HELPDEZK_PATH.'app/modules/emq/models/parent_model.php');
require_once (HELPDEZK_PATH.'app/modules/emq/models/emails_model.php');
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
$dbPerson = new acdstudent_model();
$dbEmail = new emails_model();

if(!isset($argv[1])){
    $cronSystem->logIt("No parameter was provided for the process. Program: " . __FILE__ , 3, 'general', __LINE__);
    $cronSystem->logIt("Finish at ".date("d/m/Y H:i:s").'. Program: '. __FILE__ ,6,'general');
    exit;
}

$type = $argv[1];
$year = $type == 'current' ? date("Y") : date("Y")+1;
$status = $type == 'current' ? 'all' : 'NOV';
$wdisable = $type == 'current' ? "WHERE `year` = $year AND record_status = 'A'" : "WHERE `year` = $year AND record_status = 'A' AND idstatusenrollment = 1";
$h = date("H");
$dow = date("w");

$cronSystem->logIt("Start, type {$type} at ".date("d/m/Y H:i:s").' - Program: '. __FILE__ ,6,'general');

$link = "/acdprofiles/{$year}/all/{$status}";
$ret = request('GET',$link,false,$cronSystem);
if(!$ret['success']){
    $cronSystem->logIt("No result return. {$ret['message']} Program: " . __FILE__ , 3, 'general', __LINE__);
    $cronSystem->logIt("Finish at ".date("d/m/Y H:i:s").'. No records to process. Program: '. __FILE__ ,6,'general');
    exit;
}

if($ret['return']){
    $disable = $dbPerson->disableAllEnrollmentRec($wdisable);
    if (!$disable){
        $cronSystem->logIt('Can\'t disable all students enrollment - program: ' . __FILE__ , 3, 'general', __LINE__);
        $cronSystem->logIt("Finish at ".date("d/m/Y H:i:s").'. Program: '. __FILE__ ,6,'general');
        exit;
    }

    $aParentLegacy = array();
    $aCompanyList = array("1"=>array(),"2"=>array(),"3"=>array());
    $i = 1;

    foreach($ret['return'] as $item) {

        $retProc = $item['role'] == 'S' ? profileStudent($item,$dbPerson,$cronSystem) : profileParent($item,$dbPerson,$cronSystem);

        if($item['role'] == 'P' && ($retProc['success'] && $retProc['nocpfemail']) && !in_array($item['idperseus'],$aParentLegacy)){
            array_push($aParentLegacy,$item['idperseus']);
            //echo "{$i}:: {$ret['data']['parentName']} - {$ret['data']['nocpf']} - {$ret['data']['noemail']} - {$ret['data']['courselist']}\n";
            $aCourse = explode(",",$retProc['data']['courselist']);
            foreach ($aCourse as $course){
                array_push($aCompanyList[$course],$retProc['data']);
            }
            $i++;
        }

    }

    if($dow == 1 && ($h >= 6 && $h < 8)){
        sendNoCPFEmailAlert($aCompanyList,$dbEmail,$cronSystem);
    }

    $cronSystem->logIt("Finish, type {$type} at ".date("d/m/Y H:i:s").". Total records read ".sizeof($ret['return']).' - program: '. __FILE__ ,6,'general');
}else{
    $cronSystem->logIt( "No data to process, finish at ".date("d/m/Y H:i:s")." - Program: ". __FILE__ ,6,'general');
}

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

function encodeString($string)
{
    return urlencode(urlencode($string));

}

function profileStudent($item,$dbPerson,$cronSystem){
    $arrCourses = array('EIB','EFB');
    $idgender = $dbPerson->getGenderIdByAbbrv($item['gender']);
    $name = addslashes($item['name']);
    $idintranet = $item['idintranet'] != '' ? $item['idintranet'] : 'NULL';

    $check = $dbPerson->getStudentID("WHERE idperseus = ". $item['idperseus']);

    if($check->fields['idperson_profile']){
        $ret = $dbPerson->updateProfile($name,$item['cpf'],$item['cardid'],$item['dtbirth'],$idgender,$item['email'],$check->fields['idperson_profile']);
        if(!$ret){
            $cronSystem->logIt("Can't update Student Profile: {$item['name']} - program: ". __FILE__ ,3,'general',__LINE__);
        }
        $studentID = $check->fields['idstudent'];
    }else{
        $ret = $dbPerson->insertProfile($name,$item['cpf'],$item['cardid'],$item['dtbirth'],$idgender,$item['email']);
        if(!$ret){
            $cronSystem->logIt("Can't insert Student Profile: {$item['name']} - program: ". __FILE__ ,3,'general',__LINE__);
        }
        $studentID = $dbPerson->insertStudent($ret,$item['idperseus'],$idintranet);
    }

    if(in_array($item['course'],$arrCourses) && $item['typeenrollment'] == 'NOV'){
        $retCheck = confirmEnrollment($item,$cronSystem);
        if($retCheck){
            $retEnrollment = studentEnrollment($studentID,$item,$dbPerson,$cronSystem);
        }
    }else{
        $retEnrollment = studentEnrollment($studentID,$item,$dbPerson,$cronSystem);
    }

    return array("success"=>true);
}

function profileParent($item,$dbPerson,$cronSystem){
    $idgender = $dbPerson->getGenderIdByAbbrv($item['gender']);
    $name = addslashes($item['name']);
    $fglCpfEmail = ((!$item['cpf'] || $item['cpf'] == "" || $item['cpf'] == " ") || (!$item['email'] || $item['email'] == "" || $item['email'] == " ")) ? true : false;
    $aParent = array(
        "parentName" => $item['name'],
        "nocpf" => (!$item['cpf'] || $item['cpf'] == "" || $item['cpf'] == " ") ? true : false,
        "noemail" => (!$item['email'] || $item['email'] == "" || $item['email'] == " ") ? true : false,
        "isRFI" => $item['isrfi'] == 1 ? true : false
    );

    $check = $dbPerson->getParentID($item['idperseus']);

    if($check->fields['idperson_profile']){
        $ret = $dbPerson->updateProfile($name,$item['cpf'],$item['cardid'],$item['dtbirth'],$idgender,$item['email'],$check->fields['idperson_profile']);
        if(!$ret){
            $cronSystem->logIt("Can't update Parent Profile: {$item['name']} - program: ". __FILE__ ,3,'general',__LINE__);
            array("success"=>false,"message"=>"Can't update parent data: {$item['name']}");
        }
        $parentID = $check->fields['idparent'];
    }else{
        $ret = $dbPerson->insertProfile($name,$item['cpf'],$item['cardid'],$item['dtbirth'],$idgender,$item['email']);
        if(!$ret){
            $cronSystem->logIt("Insert Parent Profile: {$item['name']} - program: ". __FILE__ ,3,'general',__LINE__);
            array("success"=>false,"message"=>"Can't insert parent data: {$item['name']}");
        }
        $parentID = $dbPerson->insertParent($ret,$item['idperseus']);
    }

    $retBind = bindStudent($parentID,$item['idperseus'],$item['year'],$item,$dbPerson,$cronSystem);
    if($retBind['success'])
        $aParent['courselist'] = $retBind['courselist'];

    return array("success"=>true,"nocpfemail"=>$fglCpfEmail,"data"=>$aParent);

}

function studentEnrollment($id,$item,$dbPerson,$cronSystem){
    $arrCourses = array('EIB','EFB');
    $year = $item['year']; 
    $item['typeenrollment'] = $item['typeenrollment'] == 'REB' ? 'REM' :  $item['typeenrollment'];
    $rsstatus = $dbPerson->getEnrollmentStatusData("WHERE abbreviation = '{$item['typeenrollment']}'");
    $rsturma = $dbPerson->getClass("AND c.idperseus = '{$item['course']}' AND a.idperseus LIKE '%". $item['class']."%'");
    
    $where = "AND d.idperseus = '{$item['course']}'
              AND a.idturma = {$rsturma->fields['idturma']}
              AND a.year = $year 
              AND a.idstudent = $id";

    $tmp = $dbPerson->getEnrollmentData($where);

    if(!$tmp->fields['idenrollment']){
        $insEnrollment = $dbPerson->insertEnrollment($id,$rsturma->fields['idturma'],$year,$item['dtregister'],$rsstatus->fields['idstatusenrollment']);
        if(!$insEnrollment){
            $cronSystem->logIt("Can't insert Student Enrollment - program: ". __FILE__ ,3,'general',__LINE__);
        }
    }else{
        $upTmp = $dbPerson->changeStatusEnrollmentRec($tmp->fields['idenrollment'],'A');
        if(!$upTmp){
            $cronSystem->logIt("Can't activate Enrollment Status - program: ". __FILE__ ,3,'general',__LINE__);
        }
    }

    return 'ok';
}

function bindStudent($id,$idlegacy,$year,$item,$dbPerson,$cronSystem)
{
    $link = "/getchildren/{$year}/{$idlegacy}";
    $ret = request('GET',$link,false,$cronSystem);
    if(!$ret['success']){
        $cronSystem->logIt("No result return. {$ret['message']}. Program: " . __FILE__ , 3, 'general', __LINE__);
        return array("success" => false, "data" => "");
    }

    if($ret['return']){
        $aRet = array();
        $courseIDs = "";

        foreach ($ret['return'] as $item){
            if($item['kinship'] && $item['kinship'] != ''){
                //$idstudent = $dbPerson->getStudentData("AND idperseus = {$item['idstudent']}");
                $idstudent = $dbPerson->getEnrollmentData("AND e.idperseus = {$item['idstudent']} AND a.year = {$year} AND a.record_status = 'A' AND c.idcurso IN (1,2,3)");
                if(!$idstudent){
                    $cronSystem->logIt("Can't get children data - program: ". __FILE__ ,3,'general',__LINE__);
                    continue;
                }

                $retkinship = $dbPerson->getKinshipData("WHERE pipeLatinToUtf8(description) LIKE pipeLatinToUtf8('%{$item['kinship']}%')");
                if(!$retkinship){
                    $cronSystem->logIt("Can't get kinship data - program: ". __FILE__ ,3,'general',__LINE__);
                    continue;
                }
                $idkinship = (!$retkinship || $retkinship->fields['idkinship'] == '') ? 11 : $retkinship->fields['idkinship'];

                if(!$idstudent->fields['idstudent'])
                    continue;

                if(!in_array($idstudent->fields['idcurso'],$aRet)){
                    array_push($aRet,$idstudent->fields['idcurso']);
                    $courseIDs .= "{$idstudent->fields['idcurso']},";
                }

                $checkBind = $dbPerson->getBindData($idstudent->fields['idstudent'],$id);
                if(!$checkBind){
                    $cronSystem->logIt("Can't get bind data - program: ". __FILE__ ,3,'general',__LINE__);
                    continue;
                }

                if($checkBind->RecordCount() <= 0){
                    $ret = $dbPerson->insertBindStudent($idstudent->fields['idstudent'],$id,$idkinship);
                    if(!$ret){
                        $cronSystem->logIt('Insert Bind Student  - program: '. __FILE__ ,3,'general',__LINE__);
                    }
                }
            }

        }

        $courseIDs = substr($courseIDs,0,-1);
    }else{
        return array("success" => false, "data" => "");
    }

    return array("success" => true, "courselist" => $courseIDs);
}

function confirmEnrollment($item,$cronSystem)
{
    $paramID = $item['typeenrollment'] == 'NOV' ? 24 : 25;
    $classID = formatClassID($item['class']);
    $link = "/confirmenrollment/{$item['year']}/{$item['course']}/{$item['series']}/{$classID}/{$item['idperseus']}/{$paramID}";
    
    $ret = request('GET',$link,false,$cronSystem);
    if(!$ret['success']){
        $cronSystem->logIt("No result return. {$ret['message']}. Program: " . __FILE__ , 3, 'general', __LINE__);
        return array("success" => false, "data" => "");
    }

    if($ret['return']) {
        foreach ($ret['return'] as $itemret){
            $item['dtregister'] = $itemret['dtpayment'];
        }
        return true;
    }else{
        return false;
    }
}

function formatClassID($string){
    $searchArr = array("º "," ");
    $replaceArr = array("_","_");
    return str_replace($searchArr,$replaceArr,$string);

}

function sendNoCPFEmailAlert($list,$dbEmail,$cronSystem){
    $txtSubject = "Responsáveis com dados cadastrais incompletos";

    foreach ($list as $key=>$value){
        switch ($key){
            case 1:
                $companyTitle = 'Três Vendas';
                break;
            case 2:
                $companyTitle = 'Mario Quintana';
                break;
            default:
                $companyTitle = 'MQ';
                break;
        }

        $tabList = "";
        foreach ($value as $item=>$val) {
            $flgEmail = $val['noemail'] ? "X" : "";
            $flgCpf = $val['nocpf'] ? "X" : "";
            $flgRFI = $val['isRFI'] ? "X" : "";
            $tabList .= "<tr><td>{$val['parentName']}</td><td class='aligncenter'>{$flgCpf}</td><td class='aligncenter'>{$flgEmail}</td><td class='aligncenter'>{$flgRFI}</td></tr>";
        }
        $size = sizeof($value);
        //echo "{$key} - {$companyTitle} - {$size}\n";
        $body = $size > 0 ? formatBody($tabList,$size,$companyTitle) : formatBodyNoData($companyTitle);

        $retRecip = getAlertRecipients($key,$dbEmail,$cronSystem);
        if(!$retRecip){
            $cronSystem->logIt("Can't get recipients data. Program: ". __FILE__,3,'general',__LINE__);
            return false;
        }

        $params = array("subject" => $txtSubject,
            "contents" => $body,
            "sender_name" => "Tecnologia da Informação",
            "sender" => "ti@marioquintana.com.br",
            "address" => $retRecip,
            "idmodule" => $cronSystem->getIdModule("Academico"),
            "modulename" => "Academico",
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
                            max-width: 600px !important;
                            margin: 0 auto !important;
                            /* makes it centered */
                            clear: both !important;
                        }
                
                        .content {
                            max-width: 600px;
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
                            width: 80%;
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
                            <td class='container' width='600'>
                                <div class='content'>
                                    <table class='main' width='100%' cellpadding='0' cellspacing='0'>
                                        <tr>
                                            <td class='content-wrap aligncenter'>
                                                <table width='100%' cellpadding='0' cellspacing='0'>
                                                    <tr>
                                                        <td class='content-block'>
                                                            <h3>RESPONS&Aacute;VEIS COM DADOS CADASTRAIS INCOMPLETOS</h3>
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
                                                                    <td class='alignjustify'>Informamos que os Respons&aacute;veis a seguir, possuem seus cadastros incompletos.</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <table class='invoice-items' cellpadding='0' cellspacing='0'>
                                                                            <tr><td class='aligncenter'><h4>RESPONS&Aacute;VEL</h4></td><td class='aligncenter'><h4>SEM CPF</h4></td><td class='aligncenter'><h4>SEM E-MAIL</h4></td><td class='aligncenter'><h4>RESP. FIN.</h4></td></tr>";

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
                            max-width: 600px !important;
                            margin: 0 auto !important;
                            /* makes it centered */
                            clear: both !important;
                        }
                
                        .content {
                            max-width: 600px;
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
                            width: 80%;
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
                            <td class='container' width='600'>
                                <div class='content'>
                                    <table class='main' width='100%' cellpadding='0' cellspacing='0'>
                                        <tr>
                                            <td class='content-wrap aligncenter'>
                                                <table width='100%' cellpadding='0' cellspacing='0'>
                                                    <tr>
                                                        <td class='content-block'>
                                                            <h3>RESPONS&Aacute;VEIS COM DADOS CADASTRAIS INCOMPLETOS</h3>
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
                                                                    <td class='alignjustify'>Informamos que a unidade n&atilde;o possui Respons&aacute;veis com cadastros incompletos.</td>
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