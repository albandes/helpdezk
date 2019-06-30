<?php
include('deploy.php');
include('log.php');

$init = parse_ini_file("config.ini",true);

/**
 * Full path and filename to log file
 *
 * @var string
 */
$logFile = dirname(__FILE__). '/'. 'deploy.log';

/**
 * The TimeZone format used for logging.
 * @var Timezone
 * @link    http://php.net/manual/en/timezones.php
 */
date_default_timezone_set($init['application']['app_timezone']);

/**
 * The Secret Key so that it's a bit more secure to run this script
 *
 * @var string
 */
$secretKey = $init['git']['hook_key'];

/**
 * The Options
 * Only 'directory' is required.
 * @var array
 */
$options = array(
    'directory'     => $init['git']['directory'],
    'work_dir'      => $init['git']['work_dir'],
    'log'           => $logFile,
    'branch'        => $init['git']['branch'],
    'remote'        => $init['git']['remote'],
    'date_format'   => $init['git']['date_format'],
    'syncSubmodule' => $init['git']['sync_submodule'],
    'git_bin_path'  => $init['git']['git_bin_path']
    );

$log = new log($logFile);
$deploy = new Deploy($options);

$appDevelopment = $init['application']['app_development'];


$headers = getallheaders();
$hubSignature = $headers['X-Hub-Signature'];

if (!validateHubSignature($secretKey,$hubSignature)) {
    $log->logIt('Error secret key invalid. ',3) ;
    echo 'Error secret key invalid.';
} else {
    $log->logIt('Hook fom GitHub.',6);
}

$deploy->execute();



echo 'Run in server.';

exit;


/**
 * Function to check if HMAC hex digest of the payload matches GitHub's.
 *
 * @return bool
 */
function validateHubSignature($SecretKey, $hubSignature)
{
    // http://isometriks.com/verify-github-webhooks-with-php

    // Split signature into algorithm and hash
    list($algo, $hash) = explode('=', $hubSignature, 2);
    // Get payload
    $payload = file_get_contents('php://input');
    // Calculate hash based on payload and the secret
    $payloadHash = hash_hmac($algo, $payload, $SecretKey);
    // Check if hashes are equivalent
    if ($hash !== $payloadHash) {
        return false;
    } else {
        return true;
    }

}

