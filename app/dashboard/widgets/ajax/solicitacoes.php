<?php
	/* MySQL connection */
	
	$url = "http://hd.marioquintana.com.br/atendimento/solicita_detalhes.php?COD_SOLICITACAO="; 	
	$gaSql['link'] =  mysql_pconnect( 'localhost', 'root', ''  ) or 	die( 'Could not open connection to server' );
	
	mysql_select_db( 'hd', $gaSql['link'] ) or 	die( 'Could not select database '. 'hd' );
	
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
					WHERE
					f.COD_ANALISTA = 689
					and assunto LIKE '%".mysql_real_escape_string( $_GET['sSearch'] )."%' 
					and h.COD_SOLICITACAO = f.COD_SOLICITACAO
					and h.COD_STATUS = g.COD_STATUS
					and h.cod_status = h.cod_status
					and f.cod_analista <> 0
					and g.COD_PAI in(5)
					";	
	} 
	else
	{
		$sWhere = 	"
					WHERE
					f.COD_ANALISTA = 689
					and h.COD_SOLICITACAO = f.COD_SOLICITACAO
					and h.COD_STATUS = g.COD_STATUS
					and h.cod_status = h.cod_status
					and g.COD_PAI in(5)
					and f.cod_analista <> 0
					";
	}	
	
	$sQuery = 	"
				select
					concat(date_format(substring(h.DAT_ABERTURA,1,8),'%d/%m/%Y'),' ',substring(h.DAT_ABERTURA,9,2),'h',substring(h.DAT_ABERTURA,11,2),'min') as dthora,
					h.NOM_ASSUNTO as assunto,
					h.COD_SOLICITACAO,
					concat(date_format(substring(h.DAT_VENCIMENTO_ATENDIMENTO,1,8),'%d/%m/%Y'),' ',substring(h.DAT_VENCIMENTO_ATENDIMENTO,9,2),'h',substring(h.DAT_VENCIMENTO_ATENDIMENTO,11,2),'min') as dtvencimento
				from hdk_solicitacao_grupo f,
					hdk_solicitacao_status g,
				hdk_solicitacao h
				$sWhere
				$sOrder
				$sLimit  
				";
	//die($sQuery);		
	$rResult = mysql_query( $sQuery, $gaSql['link'] ) or die(mysql_error());
	
	$sQuery = "
		SELECT FOUND_ROWS()
	";
	$rResultFilterTotal = mysql_query( $sQuery, $gaSql['link'] ) or die(mysql_error());
	$aResultFilterTotal = mysql_fetch_array($rResultFilterTotal);
	$iFilteredTotal = $aResultFilterTotal[0];
	
	$sQuery = "
			select
			COUNT(h.cod_solicitacao)
			from hdk_solicitacao_grupo f,
			   hdk_solicitacao_status g,
			   hdk_solicitacao h
			WHERE f.COD_ANALISTA = 689
				 and h.COD_SOLICITACAO = f.COD_SOLICITACAO
				 and h.COD_STATUS = g.COD_STATUS
				 and h.cod_status = h.cod_status
				 and f.cod_analista <> 0
				 and g.COD_PAI in(5)
			";
	//die($sQuery);				
	$rResultTotal = mysql_query( $sQuery, $gaSql['link'] ) or die(mysql_error());
	$aResultTotal = mysql_fetch_array($rResultTotal);
	$iTotal = $aResultTotal[0];
	
	
	$sOutput = '{';
	$sOutput .= '"sEcho": '.intval($_GET['sEcho']).', ';
	$sOutput .= '"iTotalRecords": '.$iTotal.', ';
	$sOutput .= '"iTotalDisplayRecords": '.$iFilteredTotal.', ';
	$sOutput .= '"aaData": [ ';
	while ( $aRow = mysql_fetch_array( $rResult ) )
	{
		$sOutput .= "[";
		$sOutput .= '"<img src=\"widgets/classes/jquery.datatables/images/details_open.png\">",';
		$sOutput .= '"'.str_replace('"', '\"', $aRow['dthora']).'",';
		$sOutput .= '"'.utf8_encode(str_replace('"', '\"', $aRow['assunto'])).'",';
		$sOutput .= '"'. '<a href=\"' .$url.$aRow['COD_SOLICITACAO'].'\">'.utf8_encode('Acessar solicitação'). ' - ' .$aRow['COD_SOLICITACAO']. '</a>",';
		$sOutput .= '"'.$aRow['dtvencimento'].'",';
		// http://hd.marioquintana.com.br/atendimento/solicita_detalhes.php?COD_SOLICITACAO=2010020171
		// <a href="http://www.w3schools.com">Visit W3Schools</a>
		$sOutput .= "],";
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