<?php
session_start();
require_once('../../../../includes/config.php');
require_once('../../../../Connections/conexao.php');
//error_reporting(E_ALL);

session_start(); 
$analista = $_SESSION['SES_COD_USUARIO']; 
//$analista = 689 ;

$sql =	"
			SELECT
				a.nome
			FROM dsh_tbwidget a,
				dsh_tbwidget_has_dsh_tbwidgetusuario b,
				dsh_tbwidgetusuario c
			WHERE a.idwidget = b.dsh_tbwidget_idwidget
				AND b.dsh_tbwidgetusuario_idwidgetusuario = c.idwidgetusuario
				AND c.idusuario = ".$analista."
		";
$rs = $conexao->Execute($sql) or die($conexao->ErrorMsg());

$aWidgets = array();
while (!$rs->EOF) 
{
	array_push($aWidgets,$rs->Fields("nome"));
	$rs->MoveNext(); 
}
//print_r($aWidgets);
$output['texto']= $aWidgets;
echo json_encode($output);


?>