<?php
/*
 *  E:\XAMPP\php\php.exe E:\home\rogerio\htdocs\git\commercial\app\modules\cron-jobs\hdk_pushover.php
 */

$idPerson=18;
$user='uzv7ct6s1ox8ck9dqfi6e5bcyuhdz1';
$token='adugrfi5e8c4uo2khqbisuap5a7s3k';
error_reporting(E_ERROR | E_WARNING | E_PARSE);

session_start();

if (isCli()) {
    $lineBreak =  PHP_EOL ;
    ini_set('html_errors', false);
} else {
    $lineBreak = '<br>';
}

$cron_path = getRootPath('cron-jobs') ;
define ('HELPDEZK_PATH', $cron_path) ;

define('SMARTY', HELPDEZK_PATH.'includes/Smarty/');
require_once(HELPDEZK_PATH.'app/modules/cron-jobs/cronSystem.php');
require_once(HELPDEZK_PATH.'app/modules/cron-jobs/cronController.php');
require_once(HELPDEZK_PATH.'app/modules/cron-jobs/cronModel.php');

require_once (HELPDEZK_PATH.'includes/classes/class.pushover/Pushover.php');

// Controllers
require_once(HELPDEZK_PATH. 'app/modules/helpdezk/controllers/hdkCommonController.php');

require_once(HELPDEZK_PATH . 'includes/classes/pipegrep/syslog.php');

/*
 *  Models
 */
require_once (HELPDEZK_PATH.'app/modules/helpdezk/models/ticket_model.php');
require_once (HELPDEZK_PATH.'app/modules/helpdezk/models/trigger_model.php');
require_once (HELPDEZK_PATH.'app/modules/admin/models/index_model.php');

require_once (HELPDEZK_PATH.'system/common.php');

$cronSystem = new cronSystem();

$cronSystem->setCronSession() ;
$cronSystem->SetEnvironment();

setLogVariables($cronSystem);
$lineBreak = $cronSystem->getLineBreak();


$cronSystem->logIt('Run Pushover Alerts by Cron - program: ' . __FILE__,6,'general');


$hdkCommon = new hdkCommon();
$dbTicket = new ticket_model();
$dbIndex = new index_model();
$dbTrigger = new trigger_model();



$i = 0;
$rsGroups = $dbIndex->selectPersonGroups($idPerson);

while (!$rsGroups->EOF) {
    $arr[$i] = $rsGroups->fields['idgroup'];
    $i++;
    $rsGroups->MoveNext();
}

$groups = implode(',', $arr);

$idPersonGroups = '';
if($groups){
    $rsIdPersonGroups = $dbTicket->getIdPersonGroup($groups);
    while (!$rsIdPersonGroups->EOF) {
        $idPersonGroups .=  $rsIdPersonGroups->fields['idperson'].",";
        $rsIdPersonGroups->MoveNext();
    }
    $idPersonGroups = substr($idPersonGroups,0,-1);
}

// Prepare query

$cond = ($idPersonGroups != '') ? $idPerson.','.$idPersonGroups : $idPerson;
$wheretip = " ((inch.ind_in_charge = 1
                            AND inch.id_in_charge IN($cond))
                            OR (inch.ind_operator_aux = 1
                                AND inch.id_in_charge = $idPerson)
                            OR (inch.id_in_charge IN($cond)
                                AND inch.ind_track = 1)) ";
$entry_date  = "DATE_FORMAT(a.entry_date, '%d/%m/%Y %H:%i') AS fmt_entry_date";
$expire_date = "DATE_FORMAT(a.expire_date, '%d/%m/%Y %H:%i') AS expire_date";
$wheredata   = '';
$where = " AND b.idstatus_source = 1 ";
$where .= "AND a.code_request NOT IN (   SELECT
                                          code_request
                                        FROM hdk_tbtrigger_alerts
                                        WHERE
                                            code_request = a.code_request
                                        AND
                                           idstatus = 1 AND send = 1
                                        AND
                                           iddeliveryprotocol = 2 ) ";
// End query


$rsTicket = $dbTicket->getRequests($idPerson, $entry_date, $expire_date, $wheredata, $where, $wheretip,0, 10, 'a.code_request', 'ASC');

if ($rsTicket->RecordCount() == 0) {
    $cronSystem->logIt('Pushover Alerts By Cron: No alerts to send. ',6,'general');
    die('Nothing to be done. ') ;
}


$push = new Pushover();

$push->setToken($token);
$push->setUser($user);
$push->setTitle('Helpdesk Alert');
$push->setHtml(1);
$push->setPriority(0);
$push->setTimestamp(time());
$push->setDebug(true);
$push->setSound('pushover');

$i=0;
$idTrigger = 1;
$idStatus = $rsTicket->fields['idstatus_source'];
$idDeliveryProtocol = '2';
$send = 1;
while (!$rsTicket->EOF) {
    echo $rsTicket->fields['subject'] . $lineBreak;

    $push->setMessage('Novo Chamado: ' . $rsTicket->fields['subject'] . $lineBreak );

    $go = $push->send();
    $xml = $go['output'];
    $observation =  $xml->request ;

    $i++;

    $ret = $dbTrigger->insertAlertSended($rsTicket->fields['code_request'], $idTrigger,$idStatus, $idDeliveryProtocol,date('Y-m-d H:i:s'), $send,$observation);
    $rsTicket->MoveNext();
}

$cronSystem->logIt('Ran Send Pushover Alerts By Cron - Sent alerts: ' .$i ,6,'general');

die(' OK ') ;

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

    $cron_path = getRootPath('cron-jobs');

    define('path', $cronSystem->_getPathDefault());

    if (isCli()) {
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


