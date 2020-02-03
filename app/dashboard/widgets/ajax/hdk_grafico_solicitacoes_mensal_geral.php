<?php
session_start();
require_once('../../../../includes/config.php');
require_once('../../../../Connections/conexao.php');
//error_reporting(E_ALL);


$sql =	"
		SELECT
		   anomes,
		   sum(total) as total
		from dsh_tbestatatendentemensal
		-- where idusuario  = ".$idusuario."
		group by anomes
		order by anomes desc
		limit 13
		";

$rs = $conexao->Execute($sql);

if(!$rs) die('<b>Erro:</b> <br> Linha '.__LINE__.' do arquivo ['.__FILE__.']' . '<br>Msg Banco: ' . $db->ErrorMsg() . '<br> SQL: ' . $sql);
	


$i=1;
while (!$rs->EOF) {
		$atendente[$i] 	= $rs->fields['total'] ;
		
		$dtedit     = substr($rs->fields['anomes'],0,4) ."-". substr($rs->fields['anomes'],4,2) . "-01"  ;
		$hora[$i] 	= $d1=strtotime($dtedit) * 1000;
		
		$rs->MoveNext();
		$i++;
}
	
$json  = "[";
$json .= montajson(utf8_encode("Total de Solicitações"),$atendente,$hora);
$json .= "]";

echo $json;



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