<?php
require_once('../../../../includes/config.php');
require_once('../../../../Connections/conexao.php');

$sql =	"
	select 
		*
	from 
		dsh_tbwidget
	where 
		nome = 'mq-impressoes-usuario'
		";
$rs = $conexao->Execute($sql);

$user  = $rs->fields['dbuser'] ;
$senha = $rs->fields['dbsenha'] ;
$banco = $rs->fields['dbbanco'] ;
$host  = $rs->fields['dbhost'] ;
$dInicial = $rs->fields['campo1'] ;

$days=30;
$limit = 5;
//$aDtInicial = explode("/", $dInicial);
//$DataInicial =  $aDtInicial[2]."-".$aDtInicial[1]."-".$aDtInicial[0];

$dsn = 'mysql://'.$user.':'.$senha.'@'.$host.'/'.$banco.''; 
$db = NewADOConnection($dsn);
if (!$db) die("Falhou a conexão com o banco");   


// -- Total de impressoes por dia --
/*
$query=	"
		select
		   date(date) as dtjob,
		   sum(pages*copies) as paginas
		from jobs_log
		WHERE DATE_SUB(CURDATE(),INTERVAL 30 DAY) <= date
		group by date(date)
		";
$rs = $db->Execute($query);
if(!$rs) die('<b>Erro:</b> <br> Linha '.__LINE__.' do arquivo ['.__FILE__.']' . '<br>Msg Banco: ' . $db->ErrorMsg() . '<br> SQL: ' . $query);
while (!$rs->EOF) {
	$rs->MoveNext();
}
*/		
//---------------


$sql = 	"
		select
		   user,
	       date(date),
		   sum(pages) as paginas,
		   (
		    select
			sum(pages)
			from jobs_log
			WHERE DATE_SUB(CURDATE(),INTERVAL $days DAY) <= date 
			) as total
		from jobs_log
		WHERE DATE_SUB(CURDATE(),INTERVAL $days DAY) <= date 
		group by user
		order by paginas desc
		limit $limit
		";
//print $sql . "<br>";
$rs = $db->Execute($sql);
if(!$rs) die('<b>Erro:</b> <br> Linha '.__LINE__.' do arquivo ['.__FILE__.']' . '<br>Msg Banco: ' . $db->ErrorMsg() . '<br> SQL: ' . $sql);

$totalimpressoes = $rs->fields['total'];




$i=1;
while (!$rs->EOF) {
	$sql = "
			select
			   user,
			   date(date) as dtjob,
			   sum(pages*copies) as paginas
			from jobs_log
			WHERE DATE_SUB(CURDATE(),INTERVAL $days DAY) <= date 
			
				 and user = '" . $rs->fields['user'] . "'
			group by date(date)
		";
	
	$rset = $db->Execute($sql);
	if(!$rset) die('<b>Erro:</b> <br> Linha '.__LINE__.' do arquivo ['.__FILE__.']' . '<br>Msg Banco: ' . $db->ErrorMsg() . '<br> SQL: ' . $sql);
	$k=1;
	while (!$rset->EOF) 
	{
		//$aNumPaginas[$rs->fields['user']][$k] 	= $rset->fields['paginas'] ;
		//$aDatas[$rs->fields['user']][$k] 	= $rset->fields['dtjob'] ;
		$aDatas[$rs->fields['user']][$rset->fields['dtjob']] 	= $rset->fields['paginas'] ;
		$k++;
		$rset->MoveNext();
	}
		$aHora[$i] 	= strtotime($rs->fields['dtjob'] . " UTC") * 1000 ; 	
	$rs->MoveNext();
	$i++;
}



//print_r($aDatas); print "<br>";


$timestamp = strtotime('-'.$days. ' days');
$dtinicio = date('Y-m-d', $timestamp) ;
/*
echo date('Y-m-d', $timestamp) . "<br>";
echo date('Y-m-d', strtotime('+2 days', strtotime($dtinicio))) . "<br>";
*/


$json  = "[";
foreach ($aDatas as $i1 => $n1)     
{
	//print $i1 . "<br>";
	
	for ($k = 1; $k <= $days; $k++) 
	{
			$adiciona = $dtinicio ."+". $k . " days";
			$compara = strtotime($adiciona) ;
			$dtcompara = date('Y-m-d', $compara);
			//echo $dtcompara . "<br>";
			
			if (array_key_exists($dtcompara, $aDatas[$i1]) )
			{
				//print $dtcompara . " - " . $aDatas[$i1][$dtcompara]. "<br>";
				$imp = $aDatas[$i1][$dtcompara] ;
			} 
			else 
			{
				//print $dtcompara . " - 0 <br>" ; 
				$imp = 0 ;		
			}
		$aImpressoes[$k] 	= $imp; 		
		$aEixoX[$k] 		= strtotime($dtcompara . " UTC") * 1000; 
	}
	
		
	$json .= montajson(utf8_encode($i1),$aImpressoes,$aEixoX);
	if ($k != $days)
	{
		$json .= ",";
	}	

}
$json .= "]";

echo $json;

exit ;




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