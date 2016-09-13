<?php
date_default_timezone_set('Brazil/East');

$dbaux = "oracle";
//DATABASE CONFIGURATION

$config["db_connect"]	= 	"mysqlt"; //mysqlt = MYSQL | oci8po = Oracle
$config["db_hostname"]	= 	"localhost";
$config["db_port"]		= 	"3306";
$config["db_name"] 		= 	"hd_install";
$config["db_username"]	= 	"root";
$config["db_password"]	= 	"";
//$config["db_sn"]		= 	"";



if($config["db_connect"] == "oci8po") {
	putenv("NLS_LANG=PORTUGUESE_BRAZIL.WE8ISO8859P1") or die("Falha ao inserir a variavel de ambiente");
	$config["oracle_format_date"]	= 	"DD/MM/YYYY";
    $config["oracle_format_hour"]	= 	"HH24:MI";
}

//SYSTEM CONFIGURATION
$config['hdk_url'] 	 	= 	"http://localhost/community/";
$config['path_default']	= 	"community";
$config['demo']			= 	false;
$config['enterprise']	= 	false;
$config['lang'] 		= 	"pt_BR";
$config['theme'] 		= 	"orange";
$config['page_title'] 	= 	"[HELPDEZK] - Parracho";
$config['license'] 		= 	201001012;

//LOCATION CONFIGURATION
if($config['lang'] == 'pt_BR') {
	$config['id_mask']		= 	" ?999.999.999-99";
	$config['ein_mask']		=	"?99.999.999/9999-99";
    $config['date_format']	=	"%d/%m/%Y";
	$config['hour_format']	=	"%H:%i";
}
if($config['lang'] == 'en_US') {
	$config['id_mask']		= 	" ?999-99-9999";
	$config['ein_mask']		=	"?99-9999999";
	$config['date_format']	=	"%m/%d/%Y";
	$config['hour_format']	=	"%h:%i %p";

}
