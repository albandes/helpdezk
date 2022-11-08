<?php

require_once('../../../../includes/config.php');
require_once('../../../../Connections/conexao.php');

/* Vem do config */
$url = $url_helpdesk . "atendimento/solicita_detalhes.php?COD_SOLICITACAO=";

session_start(); 
$analista = $_SESSION['SES_COD_USUARIO']; 

	
	/* Paging */
	$sLimit = "";
	if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
	{
		$sLimit = "LIMIT ".mysql_real_escape_string( $_GET['iDisplayStart'] ).", ".
			mysql_real_escape_string( $_GET['iDisplayLength'] );
	}
	
	/* Ordering */
	if ( isset( $_GET['iSortCol_0'] ) )
	{
		$sOrder = "ORDER BY  ";
		for ( $i=0 ; $i<mysql_real_escape_string( $_GET['iSortingCols'] ) ; $i++ )
		{
			$sOrder .= fnColumnToField(mysql_real_escape_string( $_GET['iSortCol_'.$i] ))."
			 	".mysql_real_escape_string( $_GET['sSortDir_'.$i] ) .", ";
		}
		$sOrder = substr_replace( $sOrder, "", -2 );
	}
	
	/* Filtering */
	$sWhere = "";
	if ( $_GET['sSearch'] != "" )
	{
		$sWhere = 	"
					WHERE h.COD_USUARIO = j.COD_USUARIO
						 AND h.assunto LIKE '%".mysql_real_escape_string( $_GET['sSearch'] )."%' 
						 AND h.COD_STATUS = g.COD_STATUS
						 AND g.COD_PAI in(1)
						 AND h.COD_SOLICITACAO = f.COD_SOLICITACAO
						 AND ((f.COD_GRUPO = i.COD_GRUPO
							   AND i.COD_USUARIO = $analista)
							   OR f.COD_ANALISTA = $analista)
					";	
	} 
	else
	{
		$sWhere = 	"
					WHERE h.COD_USUARIO = j.COD_USUARIO
						 AND h.COD_STATUS = g.COD_STATUS
						 AND g.COD_PAI in(1)
						 AND h.COD_SOLICITACAO = f.COD_SOLICITACAO
						 AND ((f.COD_GRUPO = i.COD_GRUPO
							   AND i.COD_USUARIO = $analista)
							   OR f.COD_ANALISTA = $analista)
					
					";
	}	
	
	$sQuery = 	"
				select DISTINCT
				   h.COD_SOLICITACAO,
				   concat(date_format(substring(h.DAT_CADASTRO,1,8),'%d/%m/%Y'),' ',substring(h.DAT_CADASTRO,9,2),'h',substring(h.DAT_CADASTRO,11,2),'min') as dthora,
				   h.NOM_ASSUNTO     as assunto,
				   concat(date_format(substring(h.DAT_VENCIMENTO_ATENDIMENTO,1,8),'%d/%m/%Y'),' ',substring(h.DAT_VENCIMENTO_ATENDIMENTO,9,2),'h',substring(h.DAT_VENCIMENTO_ATENDIMENTO,11,2),'min') as dtvencimento
				from hdk_solicitacao_grupo f,
				   hdk_solicitacao_status g,
				   hdk_solicitacao h,
				   hdk_usuario_grupo i,
				   hdk_usuario j
				$sWhere
				$sOrder
				$sLimit  
				";
	$rResult = $conexao->Execute($sQuery) or die($conexao->ErrorMsg());
	//die($sQuery);
	
	$iFilteredTotal = $rResult->RecordCount();
	$sQuery = 	"
				select
				   count(DISTINCT h.COD_SOLICITACAO) as total
				from hdk_solicitacao_grupo f,
				   hdk_solicitacao_status g,
				   hdk_solicitacao h,
				   hdk_usuario_grupo i,
				   hdk_usuario j
				WHERE h.COD_USUARIO = j.COD_USUARIO
					 AND h.COD_STATUS = g.COD_STATUS
					 AND g.COD_PAI in(1)
					 AND h.COD_SOLICITACAO = f.COD_SOLICITACAO
					 AND ((f.COD_GRUPO = i.COD_GRUPO
						   AND i.COD_USUARIO = $analista)
						   OR f.COD_ANALISTA = $analista)				 
				";
	
	$rResultTotal = $conexao->Execute($sQuery) or die($conexao->ErrorMsg());
	$iTotal = $rResultTotal->fields['total'];
	
	
	$sOutput = '{';
	$sOutput .= '"sEcho": '.intval($_GET['sEcho']).', ';
	$sOutput .= '"iTotalRecords": '.$iTotal.', ';
	$sOutput .= '"iTotalDisplayRecords": '.$iFilteredTotal.', ';
	$sOutput .= '"aaData": [ ';
	
	$i=0;
	while (!$rResult->EOF) 
	{
		$sOutput .= "[";
		$sOutput .= '"<img src=\"widgets/classes/jquery.datatables/images/details_open.png\">",';
		$sOutput .= '"'.str_replace('"', '\"', $rResult->fields['dthora']).'",';
		$sOutput .= '"'.utf8_encode(str_replace('"', '\"', $rResult->fields['assunto'])).'",';
		$sOutput .= '"'. '<a href=\"' .$url.$rResult->fields['COD_SOLICITACAO'].'\">'.utf8_encode('Acessar solicitação'). ' - ' .$rResult->fields['COD_SOLICITACAO']. '</a>",';
		$sOutput .= '"'.$rResult->fields['dtvencimento'].'" ';
		$i++;
	
		$sOutput .= "],";
	
		$rResult->MoveNext();
	}
	$sOutput = substr_replace( $sOutput, "", -1 );
	$sOutput .= '] }';

	echo $sOutput;
	
	function fnColumnToField( $i )
	{
		/* Note that column 0 is the details column */
		if ( $i == 0 ||$i == 1 )
			return "engine";
		else if ( $i == 2 )
			return "browser";
		else if ( $i == 3 )
			return "platform";
		else if ( $i == 4 )
			return "version";
		else if ( $i == 5 )
			return "grade";
	}
	
	//Função que formatar o numero para data
	function FormatarData($data,$tipo){
		$new_date = "";
		if ($data != ''){
			$ano = substr($data,2,2);
			$mes = substr($data,4,2);
			$dia = substr($data,6,2);
			
			$hora = substr($data,8,2);
			$min  = substr($data,10,2);
			$seg  = substr($data,12,2);
								
			$new_date = $dia."/".$mes."/".$ano;
			if ($tipo == "full")
				$new_date .= " ".$hora.":".$min; 
		}
		return $new_date;
	}	
?>