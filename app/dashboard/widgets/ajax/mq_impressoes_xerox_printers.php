<?php
require_once('../../../../includes/config.php');
require_once('../../../../Connections/conexao.php');

$sql =	"
	select 
		*
	from 
		dsh_tbwidget
	where 
		nome = 'mq-impressoes-xerox-printers'
		";
$rs = $conexao->Execute($sql);

$user  = $rs->fields['dbuser'] ;
$senha = $rs->fields['dbsenha'] ;
$banco = $rs->fields['dbbanco'] ;
$host  = $rs->fields['dbhost'] ;
$dInicial = $rs->fields['campo1'] ;

$days=30;

$dsn = 'mysql://'.$user.':'.$senha.'@'.$host.'/'.$banco.''; 
$db = NewADOConnection($dsn);
if (!$db) die("Falhou a conexão com o banco");   





$sql = 	"
		SELECT
		   DevData,
		   sum(Copias) AS copias
		FROM xerox_solicita
		WHERE Pronta = 'S'
			 AND Aprovado = 'S'
			 AND DevData BETWEEN DATE_SUB(CURDATE(),INTERVAL $days DAY)
			 and now()
		GROUP BY DevData
		order by DevData
		";

$rs = $db->Execute($sql);
if(!$rs) die('<b>Erro:</b> <br> Linha '.__LINE__.' do arquivo ['.__FILE__.']' . '<br>Msg Banco: ' . $db->ErrorMsg() . '<br> SQL: ' . $sql);

while (!$rs->EOF) 
{
	$aNumXerox[$rs->fields['DevData'] ]	= $rs->fields['copias'] ;
	$totXerox = $totXerox + $rs->fields['copias'] ;
	$rs->MoveNext();
}

$sql = 	"
		select
		   date(date) as dtprinter,
		   count(copies*pages) as paginas
		from jobs_log
		WHERE DATE_SUB(CURDATE(),INTERVAL $days DAY) <= date
		group by date(date)
		";

$rs = $db->Execute($sql);
if(!$rs) die('<b>Erro:</b> <br> Linha '.__LINE__.' do arquivo ['.__FILE__.']' . '<br>Msg Banco: ' . $db->ErrorMsg() . '<br> SQL: ' . $sql);

while (!$rs->EOF) 
{
	$aNumPrinter[$rs->fields['dtprinter'] ]	= $rs->fields['paginas'] ;
	$totPrinter = $totPrinter + $rs->fields['paginas'] ;
	$rs->MoveNext();
}


$timestamp = strtotime('-'.$days. ' days');
$dtinicio = date('Y-m-d', $timestamp) ;

for ($i=1;$i <= $days;$i++)
{

	$adiciona = $dtinicio ."+". $i . " days";
	$compara = strtotime($adiciona) ;
	$dtcompara = date('Y-m-d', $compara);

	if (array_key_exists($dtcompara, $aNumXerox) )
	{
		$xerox = $aNumXerox[$dtcompara] ;
	} 
		else 
	{
		$xerox = 0 ;		
	}
	if (array_key_exists($dtcompara, $aNumPrinter) )
	{
		$imp = $aNumPrinter[$dtcompara] ;
	} 
		else 
	{
		$imp = 0 ;		
	}
	
	$aXerox[$i] 	= $xerox; 		
	$aPrinter[$i]	= $imp;
 	$aEixoX[$i] 	= strtotime($dtcompara . " UTC") * 1000; 	
	
}

$totGeral = $totXerox + $totPrinter ;
$prcXerox = round(($totXerox * 100)/$totGeral,2);
$mostratotxerox = number_format($totXerox, 0, ',','.') . " [".number_format($prcXerox, 2, ',','.')."%]";;

$prcPrinter = round(($totPrinter * 100)/$totGeral,2);
$mostratotprinter = number_format($totPrinter, 0, ',','.') . " [".number_format($prcPrinter, 2, ',','.')."%]";;

$json  = "[";
$json .= montajson(utf8_encode("Xerox " . $mostratotxerox),$aXerox,$aEixoX);
$json .= ",";
$json .= montajson(utf8_encode("Impressões ".$mostratotprinter),$aPrinter,$aEixoX);
$json .= "]";
echo $json;

exit;




function montajson($label,$array,$hora) {

	$max = count($array);
	$json .= "{";	
	$json .= "label: \"" . $label . "\",";
	$k=0;
	for($i=$max; $i>=1; $i--)
	{
		if ($i==$max) {
			$json .= "data:[";
		}
		$json .= "[" . $hora[$i] . "," . $array[$i] . "]";
		if ($i > 1) 
		{
			$json .= ", " ; 
		}
		if ($i == 1) 
		{
			$json .= "]" ; 
		}
		$k++;
		//print $minimo[$i] . "<br />";
	}
	$json .= "}";	
	
	return $json;
}
?>