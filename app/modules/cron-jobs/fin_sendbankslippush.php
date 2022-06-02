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

/*
 *  Models
 */
require_once (HELPDEZK_PATH.'app/modules/fin/models/bankslipemail_model.php');
require_once (HELPDEZK_PATH.'app/modules/emq/models/emqfeature_model.php');
require_once (HELPDEZK_PATH.'app/modules/acd/models/acdclass_model.php');
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

$cronSystem->logIt("Start at ".date("d/m/Y H:i:s").' - Program: '. __FILE__ ,6,'general');

$aNotSend = array();
$competence = date("m/Y");

$where = "WHERE a.competence = '{$competence}'  AND f.push_status = 1 AND (send_ticket_type IN ('A','M'))";
$group = "GROUP BY b.idspool_recipient";
$order = "ORDER BY b.dtentry";
$limit = "LIMIT 200";

$ret = $dbBankSlip->getSpoolToSend($where,$group,$order,$limit);
if (!$ret['success']) {
    $cronSystem->logIt($ret['message'] . ' - Program: '. __FILE__ ,3,'general',__LINE__);
    exit;
}

$rs = $ret['data'];
if($rs->RecordCount() <= 0){
    $cronSystem->logIt( "No notification to send, finish at ".date("d/m/Y H:i:s")." - Program: ". __FILE__ ,6,'general');
    exit;
}

while(!$rs->EOF){
    $atts = explode(',',$rs->fields['attachments']);
    $idatts = explode(',',$rs->fields['idattachments']);
    $diratts = explode(',',$rs->fields['attach_dir']);
    $sendAvailable = true;
    $pushAvailable = false;

    $arrAtt = array();
    $aAttPush = array();

    if($rs->fields['attachments'] != ''){
        $fileAvailable = existAttachment($atts,$idatts,$diratts);

        if($fileAvailable == 0)
            $sendAvailable = false;

        foreach ($atts as $k=>$v){
            $extension = strrchr($v, ".");

            $bus = array(
                "filepath" => HELPDEZK_PATH . $diratts[$k] ."/". $v,
                "filename" => $v
            );

            $retFile = uploadFile(HELPDEZK_PATH . $diratts[$k] ."/". $v,$cronSystem);
            $retFile = json_decode($retFile,true);
            if($retFile && $retFile['id'] != ''){
                $pushAvailable = true;
                $busPush = array(
                    "tipo" => "ARQUIVO",
                    "id" => $retFile['id']
                );
                array_push($aAttPush,$busPush);
            }

            array_push($arrAtt,$bus);

        }
    }

    $aAlert = array(
        "subject" => $rs->fields['subject'],
        "body" => $rs->fields['body_push'],
        "recipients" => array(
            array(
                "identificador" => $rs->fields['idrecipient_push'],
                "nome" => $rs->fields['recipient_name'],
                "tipoDestinatario" => "USUARIO"
            )
        ),
        "attachments" => $aAttPush
    );
    
    if($sendAvailable){
        if($pushAvailable){
            $retPush = sendAlert($aAlert,$cronSystem);

            if($retPush){
                $upd = $dbBankSlip->updatePushStatus($rs->fields['idspool_recipient'],2);
                if (!$upd['success']) {
                    $cronSystem->logIt($upd['message'] .  "\nUpdate Push Notification status (Sent) - spoolRecipientID: {$rs->fields['idspool_recipient']} - User: ".$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
                }
            }else{
                $upd = $dbBankSlip->updatePushStatus($rs->fields['idspool_recipient'],6);
                if (!$upd['success']) {
                    $cronSystem->logIt($upd['message'] .  "\nUpdate Push Notification status (Not sent) - User: ".$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
                }

                $cronSystem->logIt("Can't send Push Notification - spoolRecipientID: {$rs->fields['idspool_recipient']}. Name: {$rs->fields['recipient_name']}. Program: {$this->program} - method: ". __METHOD__ ,3,'general',__LINE__);
            }

        }else{
            $cronSystem->logIt("Can't send Push Notification. Attachment not exists - spoolRecipientID: {$rs->fields['idspool_recipient']} - program: {$this->program} - method: ". __METHOD__ ,3,'general',__LINE__);
        }

    }else{
        if(!in_array($rs->fields['idspool'],$aNotSend))
            array_push($aNotSend,$rs->fields['idspool']);

        $updNotSend = $dbBankSlip->updatePushStatus($rs->fields['idspool_recipient'],4);
        if (!$updNotSend['success']) {
            $cronSystem->logIt($updNotSend['message'] . "\nPush Notification - spoolRecipientID: {$rs->fields['idspool_recipient']} - User: ".$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
        }
    }

    $rs->MoveNext();
}

$cronSystem->logIt("Finish at ".date("d/m/Y H:i:s").". Total records read ".$rs->RecordCount().' - program: '. __FILE__ ,6,'general');

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

function existAttachment($arrAttName,$arrAttId,$arrAttDir)
{
    $send = 1;
    foreach ($arrAttName as $k=>$v){
        $extension = strrchr($v, ".");
        $findPath = HELPDEZK_PATH . $arrAttDir[$k] ."/". $v;

        if(!file_exists($findPath))
            $send = 0;
    }

    return $send;
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
        "anexos" => $params['attachments']
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
