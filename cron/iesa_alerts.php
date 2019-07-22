<?php
/**
 * Created by PhpStorm.
 * User: rogerio.albandes
 * Date: 31/10/2018
 * Time: 10:14
 */

//if (substr(php_sapi_name(), 0, 3) != 'cli') { die("This program runs only in the command line"); }

/* Configs */

$apiToken = "";
$chatId = '';

$debug_screen = true ;
error_reporting(1);

$path_parts = pathinfo(dirname(__FILE__));
$cron_path = $path_parts['dirname'] ;

$adodb_path = '/includes/adodb/adodb-5.20.9/adodb.inc.php';

/* Includes */
include($cron_path . "/includes/config/config.php") ;
include($cron_path . $adodb_path);
include($cron_path . "/cron/cron_mail.php");

$license = $config['license'];
$lang_default = $config['lang'];
$date_format = $config['date_format'];
$hour_format = $config['hour_format'];
$db_hostname =  $config["db_hostname"];
$db_port = $config["db_port"];
$db_username = $config["db_username"];
$db_password = $config["db_password"];
$db_name = $config["db_name"];

$print_date = str_replace("%","",$date_format) . " " . str_replace("%","",$hour_format);

$db = NewADOConnection('mysqli');
if ($db_port) {
    $db_server = $db_hostname.":" . $db_port ;
} else {
    $db_server = $db_hostname;
}
if (!$db->Connect( $db_server, $db_username , $db_password, $db_name)) {
    die("$lb Database Error : " . $db->ErrorNo() . " - " . $db->ErrorMsg());
}

$sql = "
        SELECT code_request,DATE_FORMAT(expire_date, '%d/%m/%Y %H:%i:%S') as expire, NOW(), TIMEDIFF(NOW(),expire_date)  FROM hdk_tbrequest
        WHERE expire_date >= (NOW() - INTERVAL 5 HOUR)
        ORDER BY expire_date DESC
        LIMIT 10
        ";

$sql = "
        SELECT code_request,DATE_FORMAT(expire_date, '%d/%m/%Y %H:%i') as expire, NOW(), TIMEDIFF(NOW(),expire_date)  FROM hdk_tbrequest
        ORDER BY expire_date DESC
        LIMIT 5
       ";
$rs = $db->Execute($sql);

if ($rs->RecordCOunt() > 0) {
    $msg = "Chamados <b>vencendo</b> em 5 horas: ". chr(10);
    while (!$rs->EOF) {
        $id = $rs->fields['code_request'];
        $msg .= ' ' . $id . ' - ' . $rs->fields['expire'] . chr(10);

        $rs->MoveNext();
    }
    //echo $msg;
    $msg = urldecode($msg);


    $ret = sendTelegram($apiToken, $chatId,$msg);
    die($ret);
} else {
    die('Não tem ');
}



function sendTelegram($apiToken, $chatId,$msg)
{

    $data = [
        'chat_id' => $chatId,
        'text' => $msg
    ];

    $response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) . '&parse_mode=html' );

    return $response;
}
