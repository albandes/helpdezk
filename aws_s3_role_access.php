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


$appSrc = new appServices();
// create a log channel
$formatter = new LineFormatter(null, $_ENV['LOG_DATE_FORMAT']);

$stream = $appSrc->_getStreamHandler();
$stream->setFormatter($formatter);

$awslogger  = new Logger('helpdezk');
$awslogger->pushHandler($stream);

$bucket = $_ENV['S3BUCKET_NAME'];


// Establish connection with DreamObjects with an S3 client.        
/* try {

    $client = new S3Client([
        'version'     => 'latest',
        'region'      => $_ENV['S3BUCKET_REGION']
    ]);

    $awslogger->info("AWS S3 connection successful",['Program' => __FILE__, 'Line' => __LINE__]);

} catch (S3Exception $e) {

    $eCode = $e->getAwsErrorCode();
    $eMessage = $e->getAwsErrorMessage();
    $awslogger->error("Error connecting to AWS S3, Error Code: " . $eCode . " Error Message: " . $eMessage,['Program' => __FILE__, 'Line' => __LINE__]);
    exit;

}

// Get folders from S3 bucket
try{
    $ret = $client->listObjectsV2([
        'Bucket'    => $bucket,
        'Delimiter'    => "/",
        'Prefix'    => 'helpdezk'              
    ]);
    
    $objectList = (!empty($ret['CommonPrefixes'])) ? array_column($ret['CommonPrefixes'],'Prefix') : "";
    echo "",print_r($objectList,true),"\n";
    $awslogger->info(print_r($objectList),['Program' => __FILE__, 'Line' => __LINE__]);

} catch (S3Exception $e) {
    $eCode = $e->getAwsErrorCode();
    $eMessage = $e->getAwsErrorMessage();
    $awslogger->error("Error getting objects from {$bucket}, Error Code: " . $eCode . " Error Message: " . $eMessage,['Program' => __FILE__, 'Line' => __LINE__]);
    exit;
} */

echo getenv('AWS_ACCESS_KEY_ID') ."\n" . getenv('AWS_SECRET_ACCESS_KEY') ."\n";

exit;