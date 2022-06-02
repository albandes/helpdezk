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


// Controllers
require_once(HELPDEZK_PATH. 'app/modules/helpdezk/controllers/hdkCommonController.php');

require_once(HELPDEZK_PATH . 'includes/classes/pipegrep/syslog.php');

/*
 *  Models
 */
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

$hdkCommon = new hdkCommon();
$dbTicket = new ticket_model();
$dbEmail = new emails_model();

$cronSystem->logIt('Run Cron - program: ' . __FILE__,6,'email');

$aNotSend = array();

$where = "WHERE f.idemail_status = 1";
$group = "GROUP BY b.idspool_recipient";
$order = "ORDER BY b.dtentry";
$limit = "LIMIT 200";

$ret = $dbEmail->getSpoolToSend($where,$group,$order,$limit);

if(!$ret){
    $cronSystem->logIt('Ran By Cron  [emq_sendemail.php] - Can\'t get emails data' ,3,'email');
    exit;
}

$i = 0;
while(!$ret->EOF){
    $atts = explode(',',$ret->fields['attachments']);
    $idatts = explode(',',$ret->fields['idattachments']);
    $sendAvailable = true;


    $arrAtt = array();

    if($ret->fields['attachments'] != ''){
        $fileAvailable = existAttachment($atts,$idatts);

        if($fileAvailable == 0)
            $sendAvailable = false;

        foreach ($atts as $k=>$v){
            $extension = strrchr($v, ".");
            $v = $ret->fields['idemail_server'] == 2 ? '=?UTF-8?B?'.base64_encode($v).'?=' : $v;

            $bus = array(
                "filepath" => HELPDEZK_PATH . '/app/uploads/emq/attachments/' . $idatts[$k].$extension,
                "filename" => $v
            );

            array_push($arrAtt,$bus);
        }
    }

    $addressTMP = $ret->fields['idemail_server'] == 2 
                    ? $ret->fields['recipient_email'] 
                    : array(array('to_name'=> $ret->fields['recipient_name'],'to_address' => $ret->fields['recipient_email']));

    $params = array("subject" => $ret->fields['subject'],
        "contents" => $ret->fields['body'],
        "sender_name" => $ret->fields['sender_title'],
        "sender" => $ret->fields['sender'],
        "address" => $addressTMP,
        "attachment" => $arrAtt,
        "idemail" => $ret->fields['idemail'],
        "idmodule" => $ret->fields['idmodule'],
        "modulename" => $ret->fields['module_name'],
        "msg" => "",
        "msg2" => "",
        "tracker" => false
    );
    
    if($sendAvailable){
        $done = $ret->fields['idemail_server'] == 2 ? $cronSystem->sendEmailDefault($params) : sendEmailByMandrill($params,$cronSystem);

        if($done){
            if($ret->fields['idemail_server'] == 2)
                $cronSystem->logIt("Ran By Cron  [emq_sendemail.php], Email Succesfully Sent, to {$ret->fields['recipient_email']} with SMTP"  ,6,'email');

            $upd = $dbEmail->updateRecipientSent($ret->fields['idspool_recipient']);
            if(!$upd){
                $cronSystem->logIt("Ran By Cron  [emq_sendemail.php] - Can't update E-mail Spool Recipient.",3,'email',__LINE__);
            }

            updateEmailSendTime($ret->fields['idemail'],$cronSystem);
            $i++;
        }else{
            $cronSystem->logIt("Ran By Cron  [emq_sendemail.php] - Not Send E-mail Spool.",3,'email',__LINE__);
        }
    }else{
        if(!in_array($ret->fields['idspool'],$aNotSend))
            array_push($aNotSend,$ret->fields['idspool']);

        $updNotSend = updateStatusNotSend($ret->fields['idspool_recipient'],$cronSystem);
        if(!$updNotSend){
            $cronSystem->logIt("Ran By Cron  [emq_sendemail.php] - Status not updated. ID recipient {$ret->fields['idspool_recipient']}.",3,'email',__LINE__);
        }
    }


    $ret->MoveNext();
}

/*if(sizeof($aNotSend) > 0)
    alertEmailNotSend($aNotSend);*/
$rowsRead = $ret->RecordCount();
$cronSystem->logIt('Ran By Cron  [emq_sendemail.php] - Number of emails to send: ' . $rowsRead ,6,'email');
$cronSystem->logIt('Ran By Cron  [emq_sendemail.php] - Sent emails: ' .$i ,6,'email');

echo 'Number of emails to send: ' . $rowsRead . $lineBreak;
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


function existAttachment($arrAttName,$arrAttId)
{
    $send = 1;
    foreach ($arrAttName as $k=>$v){
        $extension = strrchr($v, ".");
        $findPath = HELPDEZK_PATH . '/app/uploads/emq/attachments/' . $arrAttId[$k].$extension;

        if(!file_exists($findPath))
            $send = 0;
    }

    return $send;
}

function sendEmailByMandrill($params,$cronSystem){
    $arrAtt = array();
    foreach($params['attachment'] as $key=>$value){
        $bus = array(
            'type' => mime_content_type($value['filepath']),
            'name' => $value['filename'],
            'content' => base64_encode(file_get_contents($value['filepath']))
        );

        array_push($arrAtt,$bus);
    }

    foreach ($params['address'] as $key => $sendEmailTo) {
        $params['to'] = array(array('email' => $sendEmailTo['to_address'],
                'name' => $sendEmailTo['to_name'],
                'type' => 'to'));
    }

    $message = array(
        'html' => $params['contents'],
        'subject' => $params['subject'],
        'from_email' => $params['sender'],
        'from_name' => $params['senderName'],
        'to' => $params['to'],
        'headers' => $params['extra_headers'],
        'important' => false,
        'track_opens' => null,
        'track_clicks' => null,
        'auto_text' => null,
        'auto_html' => null,
        'inline_css' => null,
        'url_strip_qs' => null,
        'preserve_recipients' => null,
        'view_content_link' => null,
        'tracking_domain' => null,
        'signing_domain' => null,
        'return_path_domain' => null,
        'merge' => true,
        'merge_language' => 'mailchimp',
        'global_merge_vars' => $params['global_merge_vars'],
        'merge_vars' => $params['merge_vars'],
        'tags' => $params['tags'],
        'google_analytics_domains' => $params['analytics_domains'],
        'google_analytics_campaign' => 'teste',
        'metadata' => $params['metadata'],
        'recipient_metadata' => $params['recipient_metadata'],
        'attachments' => $arrAtt,
        'images' => $params['images']
    );
    //echo "",print_r($message,true),"\n";
    $done = $cronSystem->sendMandrill($message);
    
    if($done['status'] != 'error'){
        $cronSystem->logIt("Ran By Cron  [emq_sendemail.php], Email Succesfully Sent, to ".$params['to'][0]['email']." with Mandrill"  ,6,'email');
        saveMandrillID($params['idemail'],$done['result'][0]['_id'],$cronSystem);
        
        return true;

    }else{
        return false;
    }
}

function saveMandrillID($idemail,$idmandrill,$cronSystem)
{
    $dbTracker = new tracker_model();

    $ret = $dbTracker->insertMadrillID($idemail,$idmandrill);
    if(!$ret) {
        $cronSystem->logIt("Can't insert Mandrill ID. Program: ". __FILE__,3,'general');
        return false;
    } else {
        return 'ok';
    }

}

function updateEmailSendTime($idemail,$cronSystem)
{
    $dbTracker = new tracker_model();

    $ret = $dbTracker->updateEmailSendTime($idemail);
    if(!$ret) {
        $cronSystem->logIt("Can't update E-mail send time. Program: ". __FILE__,3,'general');
        return false;
    } else {
        return 'ok';
    }

}

function updateStatusNotSend($idrecipient,$cronSystem)
{
    $dbEmail2 = new emails_model();
    $ret = $dbEmail2->updateRecipientStatus($idrecipient,4);

    if(!$ret){
        $cronSystem->logIt("Can't update status recipient {$idrecipient}. Program: ". __FILE__,3,'general',__LINE__);
        return false;
    }

    return true;
}