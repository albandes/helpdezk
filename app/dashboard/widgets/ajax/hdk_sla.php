<?php
require_once('../../../../includes/config.php');
require_once('../../../../Connections/conexao.php');
//error_reporting(E_ALL);

session_start(); 
$analista = $_SESSION['SES_COD_USUARIO']; 
//$analista = 689 ;

$sql =	"
		SELECT count(DISTINCT 
		  sol.COD_SOLICITACAO) as TOTAL_MINHAS_VENCIDAS
		FROM (hdk_solicitacao sol,
		   hdk_usuario usu,
		   hdk_solicitacao_status stat,
		   hdk_usuario_grupo usu_grupo,
		   hdk_solicitacao_grupo sol_grupo)
		WHERE sol.COD_USUARIO = usu.COD_USUARIO
			AND sol.COD_STATUS = stat.COD_STATUS
			AND stat.COD_PAI in(3)
			AND sol.DAT_VENCIMENTO_ATENDIMENTO <= ".date("YmdHi")."
			AND sol.COD_SOLICITACAO = sol_grupo.COD_SOLICITACAO
			AND (sol_grupo.COD_ANALISTA = $analista)
		";
$rs = $conexao->Execute($sql);
$total_vencidas = 	$rs->fields['TOTAL_MINHAS_VENCIDAS'] ; 


$sql =	"
	SELECT count(DISTINCT 
	  sol.COD_SOLICITACAO) as TOTAL_MINHAS_A_VENCER
	FROM (hdk_solicitacao sol,
	   hdk_usuario usu,
	   hdk_solicitacao_status stat,
	   hdk_usuario_grupo usu_grupo,
	   hdk_solicitacao_grupo sol_grupo)
	WHERE sol.COD_USUARIO = usu.COD_USUARIO
		AND sol.COD_STATUS = stat.COD_STATUS
		AND sol.DAT_VENCIMENTO_ATENDIMENTO >= ".date("YmdHi")."
		AND sol.COD_SOLICITACAO = sol_grupo.COD_SOLICITACAO
		AND (sol_grupo.COD_ANALISTA = $analista)
		";
$rs = $conexao->Execute($sql);
$total_avencer = 	$rs->fields['TOTAL_MINHAS_A_VENCER'] + 3 ; 

$sql =	"
		SELECT
		   count(DISTINCT sol.COD_SOLICITACAO) as TOTAL_NOVAS
		FROM (hdk_solicitacao sol,
			hdk_usuario usu,
			hdk_solicitacao_status stat,
			hdk_usuario_grupo usu_grupo,
			hdk_solicitacao_grupo sol_grupo)
		WHERE sol.COD_USUARIO = usu.COD_USUARIO
			 AND sol.COD_STATUS = stat.COD_STATUS
			 AND stat.COD_PAI in(1)
			 AND sol.COD_SOLICITACAO = sol_grupo.COD_SOLICITACAO
			 AND ((sol_grupo.COD_GRUPO = usu_grupo.COD_GRUPO
				   AND usu_grupo.COD_USUARIO = $analista)
				   OR sol_grupo.COD_ANALISTA = $analista)
		";
$rs = $conexao->Execute($sql);
$novas = 	$rs->fields['TOTAL_NOVAS'] ; 		

$json  = "[" ;
$json .= "{label:'Novas', data:".$novas."}"  . "," ;
$json .= "{label:'Vencidas', data:".$total_vencidas."}"  . "," ;
$json .= "{label:'A Vencer', data:".$total_avencer."}"   ;
$json .= "]";

echo $json;

?>