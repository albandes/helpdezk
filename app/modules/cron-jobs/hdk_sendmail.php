<?php
/*
 *  E:\XAMPP\php\php.exe E:\home\rogerio\htdocs\git\commercial\app\modules\cron-jobs\hdk_sendmail.php
 */
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

echo '---------------------------------------------------' . $lineBreak;
echo 'HELPDEZK_PATH: ' . HELPDEZK_PATH . $lineBreak;
echo '--------------------------------------------------' . $lineBreak;

define('SMARTY', HELPDEZK_PATH .'includes/Smarty/');
require_once(HELPDEZK_PATH.'app/modules/cron-jobs/cronSystem.php');
require_once(HELPDEZK_PATH.'app/modules/cron-jobs/cronController.php');
require_once(HELPDEZK_PATH.'app/modules/cron-jobs/cronModel.php');

// Controllers
require_once(HELPDEZK_PATH. 'app/modules/helpdezk/controllers/hdkCommonController.php');

// Classes
require_once(HELPDEZK_PATH . 'includes/classes/pipegrep/syslog.php');

/*
 *  Models
 */
require_once (HELPDEZK_PATH.'app/modules/helpdezk/models/ticket_model.php');
require_once (HELPDEZK_PATH.'app/modules/admin/models/index_model.php');

require_once (HELPDEZK_PATH.'system/common.php');

$cronSystem = new cronSystem();


$cronSystem->setCronSession() ;
$cronSystem->SetEnvironment();

setLogVariables($cronSystem);

$lineBreak = $cronSystem->getLineBreak();


$cronSystem->logIt('Run Send Email by Cron - program: ' . __FILE__,6,'email');



$hdkCommon = new hdkCommon();
$dbTicket = new ticket_model();

$i = 0 ;
$rsEmails = $dbTicket->getEmailCron('WHERE send = 0'); // Pega os e-mails para enviar

$i = 0;

while (!$rsEmails->EOF)
{

    $ret = $hdkCommon->_sendEmail($rsEmails->fields['operation'] , $rsEmails->fields['code_request']) ;

    //echo '<pre>'; var_dump($ret); echo '</pre>'; echo $lineBreak;

    if ( $ret ) {
        $dbTicket->updateEmailCron('SET date_out = NOW(), send=1 WHERE idrequest_emailcron = ' . $rsEmails->fields['idrequest_emailcron'] ) ;
        echo 'Enviando email da solicitacao: '. $rsEmails->fields['code_request'] . $lineBreak;
        $i++;
    }


    $rsEmails->MoveNext();

}

$cronSystem->logIt('Ran Send Email By Cron - Number of emails to send: ' .$rsEmails->RecordCount() ,6,'email');
$cronSystem->logIt('Ran Send Email By Cron - Sent emails: ' .$i ,6,'email');

echo 'Number of emails to send: ' . $rsEmails->RecordCount() . $lineBreak;
echo 'Sent '.$i.' emails !' . $lineBreak;

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

