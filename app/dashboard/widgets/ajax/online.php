<?php
error_reporting(E_ALL);
include("../../../admin/config/config.php");
include(PATH . "classes/adodb/adodb.inc.php");
require(PATH .'classes/padrao/padrao.php');
include(PATH . 'modulos/site/classes/site.class.php');

date_default_timezone_set('America/Sao_Paulo');

//require('../../../../admin/modulos/site/setup/setup.php');

$idsite = 1;


$padrao = new padrao ;
$site   = new site;
$conex = $padrao->abrebanco($banco,PATH) ;

$IDTEMPLATE = (int)$_GET['F_IDTEMPLATE'];


$online = $site->visitantesOnline($conex,$idsite);
//$output['text']= $online . " visitantes as " . date("H") . "h ". date("i") . "min " . date("s") . "s" ;    
$output['texto']=  "<br>On line: " .$online . utf8_encode(" às ") . date("H:i:s");
echo json_encode($output);

die();

?>