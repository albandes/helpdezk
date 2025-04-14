<?php

error_reporting(E_ALL);
require __DIR__ . '/vendor/autoload.php';

//Load environment settings
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

date_default_timezone_set($_ENV['TIME_ZONE']);

use App\src\appServices;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

use Aws\Credentials\Credentials;
use Aws\S3\S3Client;
use Aws\Ses\SesClient;
use Aws\SesV2\SesV2Client;
use Aws\Exception\AwsException;
use Aws\S3\Exception\S3Exception;
use Aws\Ses\Exception\SesException;
use Aws\SesV2\Exception\SesV2Exception;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

use App\modules\admin\dao\mysql\featureDAO;
use App\modules\admin\dao\mysql\emailServerDAO;
use App\modules\admin\models\mysql\emailServerModel;
use App\modules\admin\models\mysql\emailSettingsModel;


$appSrc = new appServices();
// create a log channel
$formatter = new LineFormatter(null, $_ENV['LOG_DATE_FORMAT']);

$stream = $appSrc->_getStreamHandler();
$stream->setFormatter($formatter);

$awslogger  = new Logger('helpdezk');
$awslogger->pushHandler($stream);


$featureDAO = new featureDAO();
$emailSettingModel = new emailSettingsModel();

$ret = $featureDAO->getEmailSettings($emailSettingModel);
if(!$ret['status']){
    $awslogger->error("Can't get email settings. Error: {$ret['push']['message']}",['Program' => __FILE__, 'Line' => __LINE__]);
    exit;
}

$emailSrvDAO = new emailServerDAO();
$emailSrvModel = new emailServerModel();
$where = "WHERE a.default = 'Y'";

$retEmailServer = $emailSrvDAO->queryEmailServers($where);
if(!$retEmailServer['status']){
    $awslogger->error("Can't get email server settings. Error: {$retEmailServer['push']['message']}",['Program' => __FILE__, 'Line' => __LINE__]);
    exit;
}

$params = array();
$aEmailSrvObj = $ret['push']['object'];
$aEmailSrvSettings = $retEmailServer['push']['object']->getGridList();
$params = array_merge($aEmailSrvSettings[0],$params);
//echo "",print_r($aEmailSrvObj,true),"\n";
$mailTitle     = '=?UTF-8?B?'.base64_encode($aEmailSrvObj->getTitle()).'?=';
$mailMethod    = 'smtp';
$mailHost      = $params['apiendpoint'];
$mailDomain    = $aEmailSrvObj->getDomain();
$mailAuth      = $aEmailSrvObj->getAuth();
$mailUsername  = $params['user'];
$mailPassword  = $params['password'];
$mailSender    = $aEmailSrvObj->getSender();
$mailHeader    = $aEmailSrvObj->getHeader();
$mailFooter    = $aEmailSrvObj->getFooter();
$mailPort      = 587;


$mail = new PHPMailer(true);

$mail->CharSet = 'utf-8';

$mail->setFrom($mailSender, $mailTitle);
$mail->Host = $mailHost;
$mail->Port = $mailPort;

$mail->Mailer = $mailMethod;
$mail->SMTPAuth = $mailAuth;
$mail->SMTPSecure = 'tls';

$mail->Username = $mailUsername;
$mail->Password = $mailPassword;
$mail->AddAddress("develop@marioquintana.com.br");

$mail->AltBody 	= "HTML";
$mail->Subject 	= '=?UTF-8?B?'.base64_encode("TESTE ENVIO E-MAIL").'?=';

$mail->Body = "Teste,<br> Favor desconsiderar.";

try{
    $mail->send();
    $awslogger->info("Email Succesfully Sent.",['Program' => __FILE__, 'Line' => __LINE__]);
}catch(Exception $e){
    $awslogger->error("The message could not be sent. PHPMailer error: {$mail->ErrorInfo}",['Program' => __FILE__, 'Line' => __LINE__]);
}