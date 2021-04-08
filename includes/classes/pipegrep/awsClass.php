<?php


if(!file_exists('includes/sdks/aws/aws-autoloader.php'))
   die('ERROR: includes/sdks/aws/aws-autoloader.php not found !!!');
else
    require_once('E:/home/rogerio/htdocs/git/staging'. '/includes/sdks/aws/aws-autoloader.php');

use Aws\S3\S3Client;    
use Aws\Exception\AwsException;
use Aws\S3\Exception\S3Exception;

class aws {
   
    public $_region;
    public $_accessKey;
    public $_secretKey;
    public $_bucket;   
    
    function __construct($bucketRegion,$bucketAccesKey,$bucketSecretKey,$bucketName) {
        $this->_region      = $bucketRegion;
        $this->_accessKey   = $bucketAccesKey;
        $this->_secretKey   = $bucketSecretKey;
        $this->_bucket      = $bucketName;

    }   
   
    /**
     * Get AWS S3 Client
     * Connects to aws s3
     * 
     * @return objec AWS S3 Object
     *
     * @since 1.1.11 First time this was introduced.
     *
     * @author Rogerio Albandes <rogerio.albandes@helpdezk.cc>
     */
    public function getS3Connection() 
    {
        //die($this->_accessKey);

        // Establish connection with DreamObjects with an S3 client.
        
        $client = new Aws\S3\S3Client([
            'version'     => 'latest',
            'region'      => $this->_region,
            'credentials' => [
                'key'      => $this->_accessKey,
                'secret'   => $this->_secretKey,
            ]
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

    public function copyToBucket($sourceFile, $targetFile, $acl = 'public-read')
    {
        
        $s3Obj = $this->getS3Connection();

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
     * @since 1.1.11 First time this was introduced.
     *
     * @author Rogerio Albandes <rogerio.albandes@helpdezk.cc>
     */

    public function renameFile($oldFile, $newFile, $acl = 'public-read')
    {
                
        $s3Obj = $this->getS3Connection();
        
        try{
            $s3Obj->copyObject([
                'Bucket'     => $this->_bucket,
                'CopySource' => "{$this->_bucket}/$oldFile",
                'Key'        => $newFile,
                'ACL'        => $acl
                
            ]);
        } catch (S3Exception $e) {
            return array("success"=>false,"message"=>"Error copy file to AWS S3");    
        }

        $s3Obj->deleteObject(array(
            'Bucket' => $this->_bucket,
            'Key'    => $oldFile,
        ));

        return array("success"=>true, "message"=>"");


    }    

    function s3Download() {
        $s3Obj = $this->getS3Connection();
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
?>