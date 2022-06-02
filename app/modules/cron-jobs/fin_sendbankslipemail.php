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

/*
 *  Models
 */
require_once (HELPDEZK_PATH.'app/modules/fin/models/bankslipemail_model.php');
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

$cronSystem->logIt('Run Cron - program: ' . __FILE__,6,'email');

$aNotSend = array();
$competence = date("m/Y");

$where = "WHERE a.competence = '{$competence}' AND f.idemail_status = 1 AND (send_ticket_type IN ('E','M'))";
$group = "GROUP BY b.idspool_recipient";
$order = "ORDER BY b.dtentry";
$limit = "LIMIT 200";
$i=0;

$ret = $dbBankSlip->getSpoolToSend($where,$group,$order,$limit);
if (!$ret['success']) {
    $cronSystem->logIt('Ran By Cron  [fin_sendbankslipemail.php] - Error: '. $ret['message'] ,3,'email');
    exit;
}

$rs = $ret['data'];
$rowsRead = $rs->RecordCount();
if($rowsRead <= 0){
    $cronSystem->logIt('Ran By Cron  [fin_sendbankslipemail.php] - Number of emails to send: '.$rowsRead ,6,'email');
    $cronSystem->logIt('Ran By Cron  [fin_sendbankslipemail.php] - Sent emails: ' .$i ,6,'email');
    exit;
}

while(!$rs->EOF){
    $atts = explode(',',$rs->fields['attachments']);
    $idatts = explode(',',$rs->fields['idattachments']);
    $diratts = explode(',',$rs->fields['attach_dir']);
    $sendAvailable = true;

    $arrAtt = array();

    if($rs->fields['attachments'] != ''){
        $fileAvailable = existAttachment($atts,$idatts,$diratts);

        if($fileAvailable == 0)
            $sendAvailable = false;

        foreach ($atts as $k=>$v){
            $extension = strrchr($v, ".");
            $v = $rs->fields['idemail_server'] == 2 ? '=?UTF-8?B?'.base64_encode($v).'?=' : $v;

            $bus = array(
                "filepath" => HELPDEZK_PATH . $diratts[$k] ."/". $v,
                "filename" => $v
            );

            array_push($arrAtt,$bus);

        }
    }

    $addressTMP = $rs->fields['idemail_server'] == 2 
                    ? $rs->fields['recipient_email'] 
                    : array(array('to_name'=> $rs->fields['recipient_name'],'to_address' => $rs->fields['recipient_email']));

    $bodyFinal = formatBody($rs->fields['body']);
    $params = array("subject" => $rs->fields['subject'],
        "contents" => $bodyFinal,
        "sender_name" => $rs->fields['sender_title'],
        "sender" => $rs->fields['sender'],
        "address" => $addressTMP,
        "attachment" => $arrAtt,
        "idemail" => $rs->fields['idemail'],
        "idmodule" => $rs->fields['idmodule'],
        "modulename" => $rs->fields['module_name'],
        "msg" => "",
        "msg2" => "",
        "tracker" => false
    );
    
    if($sendAvailable){
        $done = $rs->fields['idemail_server'] == 2 ? $cronSystem->sendEmailDefault($params) : sendEmailByMandrill($params,$cronSystem);

        if($done){
            if($rs->fields['idemail_server'] == 2)
                $cronSystem->logIt("Ran By Cron [fin_sendbankslipemail.php], Email Succesfully Sent, to {$rs->fields['recipient_email']} with SMTP"  ,6,'email');

            $upd = $dbBankSlip->updateRecipientSent($rs->fields['idspool_recipient']);
            if (!$upd['success']) {
                $cronSystem->logIt("Ran By Cron [fin_sendbankslipemail.php] - ".$upd['message'],3,'email',__LINE__);
            }
            $i++;
        }else{
            $cronSystem->logIt("Ran By Cron [fin_sendbankslipemail.php] - Can't send E-mail Spool - spoolRecipientID: {$rs->fields['idspool_recipient']}." ,3,'email',__LINE__);
        }

    }else{
        if(!in_array($rs->fields['idspool'],$aNotSend))
            array_push($aNotSend,$rs->fields['idspool']);

        $updNotSend = updateStatusNotSend($rs->fields['idspool_recipient'],$cronSystem);
        if(!$updNotSend){
            $cronSystem->logIt("Ran By Cron [fin_sendbankslipemail.php] - Status not updated. ID recipient {$rs->fields['idspool_recipient']}.",3,'general',__LINE__);
        }
    }

    $rs->MoveNext();
}

$cronSystem->logIt('Ran By Cron [fin_sendbankslipemail.php] - Number of emails to send: ' . $rowsRead ,6,'email');
$cronSystem->logIt('Ran By Cron [fin_sendbankslipemail.php] - Sent emails: ' .$i ,6,'email');

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

function formatBody($body)
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
                
                <body>";

    $html .= $body;

    $html .= "</body>
                </html>";

    return $html;
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
        $cronSystem->logIt("Email Succesfully Sent, ".$params['to'][0]['email']." with Mandrill"  ,6,'email');
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
