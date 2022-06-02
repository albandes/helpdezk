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

$cronSystem->logIt("Start at ".date("d/m/Y H:i:s").' - Program: '. __FILE__ ,6,'general');

$apiEemBaseUrl = getAPIFeature('eem_base_url',$dbEMQFeature,$cronSystem);
$apiEemToken = getAPIFeature('eem_token',$dbEMQFeature,$cronSystem);
$currentYear = date("Y");

$eem = new EeM();
$eem->setToken($apiEemToken);

$where = "AND e.year = {$currentYear}";
$order = "ORDER BY nome";
$ret = $dbStudent->getStudentDataApp($where,$order);
$aSend = array();
$n = 0;

while(!$ret->EOF){
    $retStActive = $dbStudent->getTotalStatusActive($ret->fields['idstudent'],$currentYear);
    if(!$retStActive){
        $cronSystem->logIt("Can't get Student Data - program: " . __FILE__, 3, 'general', __LINE__);
        continue;
    }

    $recordSt = $retStActive->fields['total_active'] > 0 ? 1 : 0;

    $bus = array (
        "registroAcademico"     => $ret->fields['registroacademico'],
        "nome"                  => utf8_encode($ret->fields['nome']),
        "apelido"               => null,
        "dataNascimento"        => $ret->fields['datanascimento'],
        "sexo"                  => ($ret->fields['sexo'] == "Masculino" ? "M" : "F"),
        "ativo"                 => $recordSt,
        "idIntegracao"          => $ret->fields['idstudent']
    );

    array_push($aSend,$bus);
    $n++;
    $ret->MoveNext();
}
//echo"",print_r($aSend,true),"\n";
$data = $eem->makeData(time(),$aSend);

$sended = json_decode($eem->httpPost($apiEemBaseUrl . 'api/aluno/integracao/v1',$data),true);

if(isset($sended['status']) && $sended['status'] == 0){
    $msg = "Finish at ".date("d/m/Y H:i:s")." Total records integrated: {$n}. Program: ". __FILE__;
}else{
    $msg = "Finish at ".date("d/m/Y H:i:s").". Status: {$sended['status']}. Error Message: {$sended['mensagemRetorno']}. Program: ". __FILE__;
}
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
