<?php
error_reporting(E_ALL);
require_once('../../../../includes/config.php');
require_once('../../../../Connections/conexao.php');

// Pegar do banco 
$sql =	"
	select 
		campo1
	from 
		dsh_tbwidget
	where 
		nome = 'srv-banda'
		";
$rs = $conexao->Execute($sql);

if (ipprivado())
{
 $url =  $rs->fields['campo1'] ;
}
else
{
 $url = "http://cacti.marioquintana.com.br:8080/embratel.png";
} 

//$url = 	$rs->fields['campo1'] ; 

//$url = "http://cacti.marioquintana.com.br:8080/embratel.png";

$output['texto'] =  "<div style=\"padding-top: 5px;\"><div id=\"centraliza\"><img src='".$url."?token=". sha1(time().rand(0, 100)). " '/> </div>";
echo json_encode($output);


function ipprivado()
{
	$ip;
	if (getenv("HTTP_CLIENT_IP")) $ip = getenv("HTTP_CLIENT_IP");
	else if (getenv("REMOTE_ADDR")) $ip = getenv("REMOTE_ADDR");
	else if (getenv("HTTP_X_FORWARDED_FOR")) $ip = getenv("HTTP_X_FORWARDED_FOR");
	else $ip = "UNKNOWN";
	$private_ip = array("/^0\./", 
						"/^127\.0\.0\.1/", 
						"/^192\.168\..*/",
						"/^172\.((1[6-9])|(2[0-9])|(3[0-1]))\..*/", 
						"/^10\..*/", 
						"/^224\..*/",
						"/^240\..*/"
						);
	while (list ($key, $val) = each ($private_ip)) {
		if (preg_match($val, $ip))
		{
		 return true;
		}
	}

	return false;
}

?>
