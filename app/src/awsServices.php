<?php

namespace App\src;

use App\src\appServices;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

use Aws\Credentials\Credentials;
use Aws\S3\S3Client;    
use Aws\Exception\AwsException;
use Aws\S3\Exception\S3Exception;

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
     * @var string
     */
    protected $_region;

    /**
     * @var string
     */
    protected $_bucket;

    /**
     * @var object
     */
    protected $_credentials;

    public function __construct()
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

        //access aws s3 settings
        $this->_region      = $_ENV['S3BUCKET_REGION'];
        $this->_bucket      = $_ENV['S3BUCKET_NAME'];
        $this->_credentials = new Credentials($_ENV['S3BUCKET_ACCESS_KEY'],$_ENV['S3BUCKET_SECRET_KEY']);      

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
        $client = new S3Client([
            'version'       => 'latest',
            'region'        => $this->_region,
            'credentials'   => $this->_credentials
        ]);
        
        return $client;                
    }
   
    /**
     * 
     * Copy file to S3 Bucket
     * 
     * @param string    $sourceFile  File to be copied to bucket
     * @param string    $targetFile  Filename in bucket
     * @param string    $acl         Bucket ACL
     * 
     * @return array    Array with status and message
     *
     * @since 1.1.11 First time this was introduced.
     *
     * @author Rogerio Albandes <rogerio.albandes@helpdezk.cc>
     */

    public function _copyToBucket($sourceFile,$targetFile,$acl='public-read')
    {
        
        $s3Obj = $this->_getS3Connection();
        
        try{
            
            $s3Obj->putObject([
                'Bucket'     => $this->_bucket,
                'Key'        => $targetFile,
                'SourceFile' => $sourceFile,
                'ACL'        => $acl
                
            ]); 
            
        } catch (S3Exception $e) {
            
            return array("success"=>false,"message"=>"Error save file to AWS S3");    

        }
        
        return array("success"=>true, "message"=>"");

    }

    /**
     * 
     * Rename file in the S3 Bucket
     * 
     * @param string    $oldFile  Filename that is in the bucket
     * @param string    $newFile  New filename
     * @param string    $acl      Bucket ACL
     * 
     * @return array    Array with status and message
     *
     * @since           1.1.11 First time this was introduced.
     * @link            https://docs.aws.amazon.com/AmazonS3/latest/userguide/object-keys.html
     * 
     * @author          Rogerio Albandes <rogerio.albandes@helpdezk.cc>
     */

    public function _renameFile($oldFile,$newFile,$acl='public-read')
    {
        
        $pos = strripos($oldFile, '/');
        if ($pos !== false) {
            $firstPart  = substr($oldFile, 0, $pos);
            $file       = substr($oldFile, $pos+1);
            $copySource = "{$firstPart}/".urlencode($file);
        } else {
            $copySource = urlencode($oldFile);    
        }    
        
        $s3ObjRen = $this->_getS3Connection();
        
        try{ 
            $ret = $s3ObjRen->copyObject([
                'Bucket'     => $this->_bucket,
                'Key'        => $newFile,    
                'CopySource' => "{$this->_bucket}/{$copySource}",
                'ACL'        => $acl               
            ]);
        } catch (S3Exception $e) {
            return array("success"=>false,"message"=>"Error copy file to AWS S3");    
        }
        
        
        $s3ObjRen->deleteObject(array(
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
                'Key'    => $file,
            ));
        } catch (S3Exception $e) {
            return array("success"=>false,"message"=>"Error remove file from AWS S3");    
        }        

        return array("success"=>true, "message"=>"");
        
    }

    function _s3Download() {
        $s3Obj = $this->_getS3Connection();
        $signed_url = $s3Obj->getObjectUrl(
            'pipegrep-001', 
            'helpdezk/attachments/45.png', '+30 minutes', 
            array(
                'ResponseContentType' => 'application/octet-stream',
                'ResponseContentDisposition' => 'attachment; filename="your-file-name-here.png"'
            )
        );
    
    }

    function getRegion() {
        return $this->_region;
    }

}