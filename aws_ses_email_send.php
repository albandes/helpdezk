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


$appSrc = new appServices();
// create a log channel
$formatter = new LineFormatter(null, $_ENV['LOG_DATE_FORMAT']);

$stream = $appSrc->_getStreamHandler();
$stream->setFormatter($formatter);

$awslogger  = new Logger('helpdezk');
$awslogger->pushHandler($stream);

/*$bucket = $_ENV['S3BUCKET_NAME'];

// Configuración del cliente de AWS SES
if(in_array($_ENV['AWS_CREDENTIALS_TYPE'],array('ENV_FILE','ENV_SERVER'))){
    $key = getenv('AWS_ACCESS_KEY_ID');
    $secret = getenv('AWS_SECRET_ACCESS_KEY');
    $credentials = new Credentials($key,$secret);

    try {

        $client = new SesClient([
            'version'     => 'latest',
            'region'      => $_ENV['S3BUCKET_REGION'],
            'credentials' => $credentials
        ]);
    
        $awslogger->info("AWS SES connection successful",['Program' => __FILE__, 'Line' => __LINE__]);
    
    } catch (S3Exception $e) {
    
        $eCode = $e->getAwsErrorCode();
        $eMessage = $e->getAwsErrorMessage();
        $awslogger->error("Error connecting to AWS S3, Error Code: " . $eCode . " Error Message: " . $eMessage,['Program' => __FILE__, 'Line' => __LINE__]);
        exit;
    
    }
    
}else{
    try {

        $client = new SesClient([
            'version'     => 'latest',
            'region'      => $_ENV['S3BUCKET_REGION']
        ]);
    
        $awslogger->info("AWS SES connection successful",['Program' => __FILE__, 'Line' => __LINE__]);
    
    } catch (S3Exception $e) {
    
        $eCode = $e->getAwsErrorCode();
        $eMessage = $e->getAwsErrorMessage();
        $awslogger->error("Error connecting to AWS S3, Error Code: " . $eCode . " Error Message: " . $eMessage,['Program' => __FILE__, 'Line' => __LINE__]);
        exit;
    
    }
}

try{
    $ret = $client->sendEmail([
        'Destination' => [
            'ToAddresses' => $recipientEmails,
        ],
        'ReplyToAddresses' => [$params['sender']],
        'Source' => $params['sender'],
        'Message' => [        
            'Body' => [
                'Html' => [
                    'Charset' => $params['charset'],
                    'Data' => $params['contents'],
                ],
                'Text' => [
                    'Charset' => $params['charset'],
                    'Data' => 'Escola Mario Quintana',
                ],
            ],
            'Subject' => [
                'Charset' => $params['charset'],
                'Data' => $params['subject'],
            ],
            'Attachments' => $params['attachment'],
        ],
    ]);

    $st = true;
    $msg = "";
    $emailId = $ret['MessageId'];
} catch (SesException $e) {
    $eCode = $e->getAwsErrorCode();
    $eMessage = $e->getAwsErrorMessage();
    $this->awslogger->error("Can't send email. Error Code: " . $eCode . " Error Message: " . $eMessage,['Class' => __CLASS__, 'Method' => __METHOD__]);
    echo "Can't send email.  Error Code: " . $eCode . " Error Message: " . $eMessage . "\n";
    $st = false;
    $msg = $eMessage;
    $emailId = "";    
}*/

$mail = new PHPMailer(true);

$mail->CharSet = 'utf-8';

$mailSender = "valentin.acosta@marioquintana.com.br";
$mailTitle = '=?UTF-8?B?'.base64_encode("Valentín L Acosta").'?=';

$mail->setFrom($mailSender, $mailTitle);
$mail->Host = "email-smtp.us-east-1.amazonaws.com";
$mail->Port = 587;

$mail->Mailer = 'smtp';
$mail->SMTPAuth = true;
$mail->SMTPSecure = 'tls';

$mail->Username = "AKIARJ4QWWA5H44I54VB";
$mail->Password = "BLFGZbqJB6M9+KkDej1puwUDQ8YsW5LpDBbcb94Rmar8";
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