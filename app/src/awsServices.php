<?php

namespace App\src;

use App\src\appServices;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

use Aws\Credentials\Credentials;
use Aws\S3\S3Client;
use Aws\Ses\SesClient;
use Aws\Exception\AwsException;
use Aws\S3\Exception\S3Exception;
use Aws\Ses\Exception\SesException;

class awsServices
{
    /**
     * @var object
     */
    protected $awslogger;
    
    /**
     * @var object
     */
    protected $awsEmailLogger;
    
    /**
     * @var mixed
     */
    protected $_region;

    /**
     * @var mixed
     */
    protected $_accessKey; 

    /**
     * @var mixed
     */
    protected $_secretKey;

    /**
     * @var mixed
     */
    protected $_bucket;

    /**
     * @var object
     */
    protected $_credentials;

    public function __construct($region=null,$bucket=null,$key=null,$secret=null)
    {
        $appSrc = new appServices();
        // create a log channel
        $formatter = new LineFormatter(null, $_ENV['LOG_DATE_FORMAT']);
        
        $stream = $appSrc->_getStreamHandler();
        $stream->setFormatter($formatter);

        $this->awslogger  = new Logger('helpdezk');
        $this->awslogger->pushHandler($stream);
        
        // Clone the first one to only change the channel
        $this->awsEmailLogger = $this->awslogger->withName('email');

        $region = (!is_null($region)) ? $region : $_ENV['S3BUCKET_REGION'];
        $bucket = (!is_null($bucket)) ? $bucket : $_ENV['S3BUCKET_NAME'];
        $key    = (!is_null($key)) ? $key : $_ENV['S3BUCKET_ACCESS_KEY'];
        $secret = (!is_null($secret)) ? $secret : $_ENV['S3BUCKET_SECRET_KEY'];

        //access aws s3 settings
        $this->_region      = $region;
        $this->_bucket      = $bucket;
        $this->_credentials = new Credentials($key,$secret);

    }

    /**
     * Get AWS S3 Client
     * Connects to aws s3
     * 
     * @return object AWS S3 Object
     *
     * @since 1.1.11 First time this was introduced.
     *
     * @author Rogerio Albandes <rogerio.albandes@helpdezk.cc>
     */
    public function _getS3Connection()
    {
        // Establish connection with DreamObjects with an S3 client.        
        try {

            $client = new S3Client([
                'version'     => 'latest',
                'region'      => $this->_region,
                'credentials' => $this->_credentials
            ]);

        } catch (S3Exception $e) {

            $eCode = $e->getAwsErrorCode();
            $eMessage = $e->getAwsErrorMessage();
            $this->awslogger->error("Error connecting to AWS S3, Error Code: " . $eCode . " Error Message: " . $eMessage,['Class' => __CLASS__, 'Method' => __METHOD__]);
            return array("success"=>false,"message"=>"Error connecting to AWS S3, Error Code: " . $eCode);

        }
        
        return $client;                
    }
   
    /**
     * 
     * Copy file to S3 Bucket
     * 
     * @param string    $sourceFile  File to be copied to bucket
     * @param string    $targetFile  Filename in bucket
     * 
     * @return array    Array with status and message
     *
     * @since 1.1.11 First time this was introduced.
     *
     * @author Rogerio Albandes <rogerio.albandes@helpdezk.cc>
     */
    public function _copyToBucket($sourceFile,$targetFile)
    {
        
        $s3Obj = $this->_getS3Connection();
        
        try{
            
            $s3Obj->putObject([
                'Bucket'     => $this->_bucket,
                'Key'        => $targetFile,
                'SourceFile' => $sourceFile                
            ]); 
            
        } catch (S3Exception $e) {

            $eCode = $e->getAwsErrorCode();
            $eMessage = $e->getAwsErrorMessage();
            $this->awslogger->error("Error putting file to AWS S3, Error Code: " . $eCode . " Error Message: " . $eMessage,['Class' => __CLASS__, 'Method' => __METHOD__]);
            return array("success"=>false,"message"=>"Error putting file to AWS S3, Error Code: " . $eCode); 

        }
        
        return array("success"=>true, "message"=>"");

    }

    /**
     * 
     * Rename file in the S3 Bucket
     * 
     * @param string    $oldFile  Filename that is in the bucket
     * @param string    $newFile  New filename
     * 
     * @return array    Array with status and message
     *
     * @since           1.1.11 First time this was introduced.
     * @link            https://docs.aws.amazon.com/AmazonS3/latest/userguide/object-keys.html
     * 
     * @author          Rogerio Albandes <rogerio.albandes@helpdezk.cc>
     */
    public function _renameFile($oldFile,$newFile)
    {
        
        $pos = strripos($oldFile, '/');
        if ($pos !== false) {
            $firstPart  = substr($oldFile, 0, $pos);
            $file       = substr($oldFile, $pos+1);
            $copySource = "{$firstPart}/".urlencode($file);
        } else {
            $copySource = urlencode($oldFile);    
        }    
        
        $s3Obj = $this->_getS3Connection();
        
        try{ 
            $ret = $s3Obj->copyObject([
                'Bucket'     => $this->_bucket,
                'Key'        => $newFile,    
                'CopySource' => "{$this->_bucket}/{$copySource}"               
            ]);
        } catch (S3Exception $e) {
            $eCode = $e->getAwsErrorCode();
            $eMessage = $e->getAwsErrorMessage();
            $this->awslogger->error("Error copying file to AWS S3, Error Code: " . $eCode . " Error Message: " . $eMessage,['Class' => __CLASS__, 'Method' => __METHOD__]);
            return array("success"=>false,"message"=>"Error putting file's copy to AWS S3, Error Code: " . $eCode);    
        }        
        
        $s3Obj->deleteObject(array(
            'Bucket' => $this->_bucket,
            'Key'    => $oldFile,
        ));
        
        return array("success"=>true, "message"=>"");

    }    

    /**
     * 
     * Remove file in the S3 Bucket
     * 
     * @param string    $file  Filename that is in the bucket
     * 
     * @return array    Array with status and message
     *
     * @since           1.1.11 First time this was introduced.
     * @link            https://docs.aws.amazon.com/AmazonS3/latest/userguide/object-keys.html
     * 
     * @author          Valentin Acosta <vilaxr@gmail.com>
     */
    public function _removeFile($file)
    {    

        $s3Obj = $this->_getS3Connection();
        
        try{
            $s3Obj->deleteObject(array(
                'Bucket' => $this->_bucket,
                'Key'    => $file
            ));
        } catch (S3Exception $e) {
            $eCode = $e->getAwsErrorCode();
            $eMessage = $e->getAwsErrorMessage();
            $this->awslogger->error("Error removing the file from AWS S3, Error Code: " . $eCode . " Error Message: " . $eMessage,['Class' => __CLASS__, 'Method' => __METHOD__]);
            return array("success"=>false,"message"=>"Error removing the file from AWS S3, Error Code: " . $eCode);    
        }        

        return array("success"=>true, "message"=>"");
        
    }

    function getRegion() {
        return $this->_region;
    }
    
    /**
     * en_us Creates and returns a presigned URL
     * 
     * pt_br Cria e retorna um URL pré-assinado
     *
     * @param string    $file  Filename that is in the bucket
     * @param int       $time  Time in minutes
     * @return array    Array with status and message
     *
     * @since           1.1.11 First time this was introduced.
     * @link            https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/s3-presigned-url.html
     * 
     * @author          Valentin Acosta <vilaxr@gmail.com>
     */
    function _getFile($file,$time=1) {
        $s3Obj = $this->_getS3Connection();
        //Creating a presigned URL
        $cmd = $s3Obj->getCommand('GetObject', [
            'Bucket' => $this->_bucket,
            'Key' => $file
        ]);        

        try{
            $request = $s3Obj->createPresignedRequest($cmd, "+{$time} minutes");
        } catch (S3Exception $e) {
            $eCode = $e->getAwsErrorCode();
            $eMessage = $e->getAwsErrorMessage();
            $this->awslogger->error("Error creating file's presigned URL on AWS S3, Error Code: " . $eCode . " Error Message: " . $eMessage,['Class' => __CLASS__, 'Method' => __METHOD__]);
            return array("success"=>false,"message"=>"Error creating file's presigned URL on AWS S3, Error Code: " . $eCode,"fileUrl"=>"");    
        }
        
        // Get the actual presigned-url
        $presignedUrl = (string)$request->getUri();
        return array("success"=>true, "message"=>"","fileUrl"=>$presignedUrl);       
    
    }
    
    /**
     * en_us Lists the subfolders contained in the selected folder
     * pt_br Lista as subpastas contidas na pasta selecionada
     *
     * @param  mixed $prefix Folder name to list from
     * @return array 
     */
    function _getFolders($prefix) 
    {
        $s3Obj = $this->_getS3Connection();

        try{
            $ret = $s3Obj->listObjectsV2([
                'Bucket'    => $this->_bucket,
                'Delimiter'    => "/",
                'Prefix'    => $prefix              
            ]);
            
            $objectList = (!empty($ret['CommonPrefixes'])) ? array_column($ret['CommonPrefixes'],'Prefix') : "";

        } catch (S3Exception $e) {
            $eCode = $e->getAwsErrorCode();
            $eMessage = $e->getAwsErrorMessage();
            $this->awslogger->error("Error getting objects from {$this->_bucket}, Error Code: " . $eCode . " Error Message: " . $eMessage,['Class' => __CLASS__, 'Method' => __METHOD__]);
            return array("success"=>false,"message"=>"Error getting objects from {$this->_bucket}, Error Code: " . $eCode,"objectList"=>"");    
        }

        return array("success"=>true, "message"=>"","objectList"=>$objectList);       
    
    }
    
    /**
     * en_us Makes a folder in bucket
     * pt_br Cria uma pasta no bucket
     *
     * @param  string $dir
     * @return void
     */
    function _setFolders($dir) {
        $s3Obj = $this->_getS3Connection();

        try{
            $s3Obj->putObject([
                'Bucket'     => $this->_bucket,
                'Key'        => $dir                
            ]);

        } catch (S3Exception $e) {
            $eCode = $e->getAwsErrorCode();
            $eMessage = $e->getAwsErrorMessage();
            $this->awslogger->error("Error putting folder on {$this->_bucket}, Error Code: " . $eCode . " Error Message: " . $eMessage,['Class' => __CLASS__, 'Method' => __METHOD__]);
            return array("success"=>false,"message"=>"Error putting folder on {$this->_bucket}, Error Code: " . $eCode);    
        }

        return array("success"=>true, "message"=>"");       
    
    }

    /**
     * _getSesClient
     * 
     * en_us Connects to AWS SES
     * pt_br Conecta ao AWS SES API
     * 
     * @return object AWS SES Object
     *
     * @since 09.29.2023
     *
     * @author Valentin L Acosta <vilaxr@gmail.com>
     */
    public function _getSesClient()
    {
        try {

            $client = new SesClient([
                'version'     => 'latest',
                'region'      => $this->_region,
                'credentials' => $this->_credentials
            ]);

        } catch (SesException $e) {

            $eCode = $e->getAwsErrorCode();
            $eMessage = $e->getAwsErrorMessage();
            $this->awslogger->error("Error connecting to AWS SES, Error Code: " . $eCode . " Error Message: " . $eMessage,['Class' => __CLASS__, 'Method' => __METHOD__]);
            return array("success"=>false,"message"=>"Error connecting to AWS SES, Error Code: " . $eCode);

        }
        
        return $client;                
    }
    
    /**
     * _sendEmailBySes
     * 
     * en_us Sends email by AWS SES
     * pt_br Envia e-mail pelo AWS SES API
     *
     * @param  mixed $params
     * @return array
     *
     * @since 09.29.2023
     *
     * @author Valentin L Acosta <vilaxr@gmail.com>
     */
    public function _sendEmailBySes($params): array
    {
        $sesObj = $this->_getSesClient();
        $recipientEmails = array_column($params['address'],'to_address');

        try{
            $ret = $sesObj->sendEmail([
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
        }

        return array("success"=>$st, "message"=>$msg,"emailId"=>$emailId);    
    }

    /**
     * _sendEmailBySes
     * 
     * en_us Sends email by AWS SES
     * pt_br Envia e-mail pelo AWS SES API
     *
     * @param  mixed $message
     * @return array
     *
     * @since 09.29.2023
     *
     * @author Valentin L Acosta <vilaxr@gmail.com>
     */
    public function _sendSesRawEmail($message): array
    {
        $sesObj = $this->_getSesClient();

        try{
            $ret = $sesObj->sendRawEmail([
                'RawMessage' => [
                    'Data' => $message
                ]
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
        }

        return array("success"=>$st, "message"=>$msg,"emailId"=>$emailId);    
    }

}