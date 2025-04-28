<?php
$debug = true;
if ($debug)
    error_reporting(E_ERROR | E_WARNING | E_PARSE);
else
    error_reporting(0);

//Required to autoload
$pathInfo = pathinfo(dirname(__DIR__,PATHINFO_BASENAME));
require_once($pathInfo['dirname']. '/vendor/autoload.php');

//Load environment settings
$dotenv = Dotenv\Dotenv::createImmutable($pathInfo['dirname']);
$dotenv->safeLoad();

date_default_timezone_set($_ENV['TIME_ZONE']);

use App\src\appServices;
use App\src\localeServices;
use App\modules\helpdezk\src\hdkServices;

use App\modules\cronjobs\src\cronJobsServices;

use App\modules\admin\dao\mysql\emailServerDAO;
use App\modules\admin\models\mysql\emailServerModel;

$appSrc = new appServices();
$translator = new localeServices();
$cronJobsSrc = new cronJobsServices();
$cronLogger = $cronJobsSrc->getCronJobsLogger();

if($cronJobsSrc->_isCli()){
    $CLI = true;
    ini_set('html_errors', false);
}

$lineBreak = $CLI ? PHP_EOL : '<br>';

$cronLogger->info("Start at ".date("d/m/Y H:i:s"), ['Cron-job' => 'hdk_sendmail','Line' => __LINE__]);
$cronJobsSrc->_setCronSession();

$emailSrvDAO = new emailServerDAO();
$emailSrvDTO = new emailServerModel();


$ret = $emailSrvDAO->fetchEmailToSend($emailSrvDTO);
if(!$ret['status']){
    $cronLogger->error("Can't get emails.", ['Cron-job' => 'hdk_sendmail','Line' => __LINE__,'Error' => $ret['push']['message']]);
    $cronLogger->info("Finish at ".date("d/m/Y H:i:s").". No records to process.", ['Cron-job' => 'hdk_sendmail','Line' => __LINE__]);
    exit;
}

$aEmails =  $ret['push']['object']->getGridList();
$numEmails = count($aEmails);
if($numEmails <= 0){
    $cronLogger->info("Finish at ".date("d/m/Y H:i:s").". No records to process.", ['Cron-job' => 'hdk_sendmail','Line' => __LINE__]);
    exit;
}

$i = 0;
foreach($aEmails as $key=>$value){
    switch($value['tableprefix']){
        case 'hdk':
            $hdkSrc = new hdkServices();
            $retSend = $hdkSrc->_sendTicketEmail($value['tag'],$value['code']);
            break;
    }
    
    if(!$retSend){
        $cronLogger->error("Can't send email. Register # {$value['code']}. Tag: {$value['tag']}", ['Cron-job' => 'hdk_sendmail','Line' => __LINE__]);
        continue;
    }

    $cronLogger->info("Email sent. Register # {$value['code']}", ['Cron-job' => 'hdk_sendmail','Line' => __LINE__]);
    $emailSrvDTO->setIdEmailCron($value['idemailcron']);

    $upd = $emailSrvDAO->updateEmailCronStatus($emailSrvDTO);//update email cron status
    if(!$upd['status']){
        $cronLogger->error("Can't update email to send. Register # {$value['code']}", ['Cron-job' => 'hdk_sendmail','Line' => __LINE__,'Error' => $upd['push']['message']]);
    }   

    $i++;
}

$cronLogger->info("Finish at ".date("d/m/Y H:i:s").". Number of emails to send: {$numEmails}. Sent emails: {$i}", ['Cron-job' => 'hdk_sendmail','Line' => __LINE__]);
echo "Number of emails to send: {$numEmails}.{$lineBreak}Sent emails: {$i}{$lineBreak}";