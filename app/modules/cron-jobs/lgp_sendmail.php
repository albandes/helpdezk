<?php
//error_reporting(E_ERROR | E_WARNING | E_PARSE);


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
define('HELPDEZK_PATH', $cron_path) ;

$lineBreak = $CLI ? PHP_EOL : '<br>';
/*
if ($CLI) {
    $lineBreak =  PHP_EOL ;
} else {
    $lineBreak = '<br>';
}
*/

//echo '---------------------------------------------------' . $lineBreak;
//echo 'HELPDEZK_PATH: ' . HELPDEZK_PATH . $lineBreak;
//echo '--------------------------------------------------' . $lineBreak;

define('SMARTY', HELPDEZK_PATH .'includes/Smarty/');
require_once(HELPDEZK_PATH.'app/modules/cron-jobs/cronSystem.php');
require_once(HELPDEZK_PATH.'app/modules/cron-jobs/cronController.php');
require_once(HELPDEZK_PATH.'app/modules/cron-jobs/cronModel.php');

// Classes
require_once(HELPDEZK_PATH . 'includes/classes/pipegrep/syslog.php');


require_once (HELPDEZK_PATH.'system/common.php');

require_once(HELPDEZK_PATH.'app/modules/admin/models/index_model.php');

$cronSystem = new cronSystem();


$cronSystem->setCronSession() ;
$cronSystem->SetEnvironment();

setLogVariables($cronSystem);

$lineBreak = $cronSystem->getLineBreak();

// Controllers
if(!setRequire(HELPDEZK_PATH. 'app/modules/lgp/controllers/lgpCommonController.php',$cronSystem)){exit;}

//Models
if(!setRequire(HELPDEZK_PATH.'app/modules/lgp/models/lgpemailconfig_model.php',$cronSystem)){exit;} 
if(!setRequire(HELPDEZK_PATH.'app/modules/main/models/mainemail_model.php',$cronSystem)){exit;}
if(!setRequire(HELPDEZK_PATH.'app/modules/admin/models/features_model.php',$cronSystem)){exit;}


$cronSystem->logIt('Run Cron - program: ' . __FILE__,6,'email');
$moduleID = $cronSystem->getIdModule("LGPD");

$lgpCommon = new lgpCommon();
$dbEmail = new mainemail_model();
$dbFeature = new features_model();
$lgpCommon->_serverApi = getLgpAPIConfig($dbFeature,$cronSystem);

$i = 0 ;
$rsEmails = $dbEmail->getEmailCron("WHERE idmodule = {$moduleID} AND send = 0"); // Pega os e-mails para enviar
if(!$rsEmails['success']){
    $cronSystem->logIt("Ran By Cron [lgp_sendemail.php] - Can't get emails data - {$rsEmails['message']}" ,3,'email');
    exit;
}
$rows = $rsEmails['data']->RecordCount();


$i = 0;
while (!$rsEmails['data']->EOF)
{
    $ret = $lgpCommon->_sendEmail($rsEmails['data']->fields['tag'] , $rsEmails['data']->fields['code']) ;
    //echo '<pre>'; var_dump($ret); echo '</pre>'; echo $lineBreak;
    if ( $ret ) {
        $dbEmail->updateEmailCron('SET date_out = NOW(), send=1 WHERE idemailcron = ' . $rsEmails['data']->fields['idemailcron'] ) ;
        echo 'Enviando email da solicitacao: '. $rsEmails['data']->fields['code'] . $lineBreak;
        $i++;
    }

    $rsEmails['data']->MoveNext();

}

$cronSystem->logIt('Ran By Cron [lgp_sendmail.php] - Number of emails to send: ' . $rows ,6,'email');
$cronSystem->logIt('Ran By Cron [lgp_sendmail.php] - Sent emails: ' .$i ,6,'email');

echo 'Number of emails to send: ' . $rows . $lineBreak;
echo 'Sent '.$i.' emails !' . $lineBreak;

die("Ran OK\n") ;

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

function getLgpAPIConfig($dbFeature,$cronSystem){
    $table = 'lgp_tbconfig_category';
    $where = "WHERE flgsetup = 'Y'";
    $order = "ORDER BY idconfigcategory";

    $rsCategories = $dbFeature->getConfigCategories($table,$where,$order);
    if(!$rsCategories){
        $cronSystem->logIt("Can't get Module's Confs Categories - program: ". __FILE__ ,3,'email',__LINE__);
        return false;
    }
    
    $categoriesID = '';
    while(!$rsCategories->EOF){
        $categoriesID .= $rsCategories->fields['idconfigcategory'].',';
        $rsCategories->MoveNext();
    }

    $categoriesID = substr($categoriesID,0,-1);
    //die("{$categoriesID}\n");
    $get = $dbFeature->getConfigs("lgp",$categoriesID);
    if(!$get){
        $cronSystem->logIt("Can't get Module's Confs - program: ". __FILE__ ,3,'email',__LINE__);
        return false;
    }

    if($get->RecordCount() > 0){
        while (!$get->EOF) {
            //echo "{$get->fields['idconfig']}: {$get->fields['config_name']} - {$get->fields['value']}\n";
            if($get->fields['config_name'] == 'api')
                return $get->fields['value'];
            $get->MoveNext();
        }
    }else{
        return false;
    }
}

function setRequire($requireFile,$cronSystem){
    if (!file_exists($requireFile)) {
        $cronSystem->logIt("{$requireFile} does not exist - Program: ". __FILE__ ,3,'email',__LINE__);
        return false;
    }else{
        return require_once($requireFile);
    }
}
