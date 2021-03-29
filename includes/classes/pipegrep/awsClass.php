<?php

require  'includes/sdks/aws/aws-autoloader.php';

use Aws\S3\S3Client;    
use Aws\Exception\AwsException;
use Aws\S3\Exception\S3Exception;


class awsClass
{
    public $_region;
    public $_accessKey;
    public $_secretKey;
    
    function __construct($bucketRegion,$bucketAccesKey,$bucketSecretKey) {
        $this->_region      = $bucketRegion;
        $this->_accessKey   = $bucketAccesKey;
        $this->_secretKey   = $bucketSecretKey;
    }

    function getS3Connection() 
    {
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

}