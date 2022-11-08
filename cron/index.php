<?php
/*
|---------------------------------------------------------------
| CASTING argc AND argv INTO LOCAL VARIABLES
|---------------------------------------------------------------
|
*/
$argc = $_SERVER['argc'];
$argv = $_SERVER['argv'];

//echo $argc. ' - '. $argv;

// INTERPRETTING INPUT
if ($argc > 1 && isset($argv[1])) {
    //die($argv[1]);
    $_SERVER['PATH_INFO']   = $argv[1];
    $_SERVER['REQUEST_URI'] = $argv[1];
} else {
    $_SERVER['PATH_INFO']   = '/cron/index';
    $_SERVER['REQUEST_URI'] = '/cron/index';
}

/*
|---------------------------------------------------------------
| PHP SCRIPT EXECUTION TIME ('0' means Unlimited)
|---------------------------------------------------------------
|
*/
set_time_limit(0);
//error_reporting(0);

$path_parts = pathinfo(dirname(__FILE__));
define ('HELPDEZK_PATH', $path_parts['dirname']) ;

$curr_url = $_SERVER['REQUEST_URI'];
$curr_url = explode("/", $curr_url);

define('CONTROLLERS', 'cron/');

require_once('cronSystem.php');
require_once('cronModel.php');
require_once(HELPDEZK_PATH . '/system/common.php');

$system = new cronSystem();
$system->run();


?>
