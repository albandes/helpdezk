<?php
include("../../../admin/config/config.php");
include(PATH . "classes/adodb/adodb.inc.php");
require(PATH .'classes/padrao/padrao.php');
require(PATH . 'modulos/site/setup/setup.php');

$padrao = new padrao ;

$conex = $padrao->abrebanco($banco,PATH) ;

$max = 25;
$sql =	"
		select
		   datahora ,
		   date_format(datahora,'%H:%i') as hora_edit,
		   minimo,
		   media,
		   maximo
		from cms_tbestathora
		where idsite = $idsite
		order by datahora desc
		limit $max
		";
if ($cachesql ) {
	$rs = $conex->CacheExecute(300,$sql);
} else {
	$rs = $conex->Execute($sql);
}	
if(!$rs) die("Erro: " . $conex->ErrorMsg() . "<br>" . $sql );
$i=1;
while (!$rs->EOF) {
		$minimo[$i] 	= $rs->fields['minimo'] ;
		$media[$i] 		= $rs->fields['media']; 
		$maximo[$i] 	= $rs->fields['maximo']; 
		$hora[$i] 		= strtotime($rs->fields['datahora'] . " UTC") * 1000 ; 

		$rs->MoveNext();
		$i++;
}
	
$json  = "[";
$json .= montajson(utf8_encode("mínimo"),$minimo,$hora);
$json .= ",";
$json .= montajson(utf8_encode("média"),$media,$hora);
$json .= ",";
$json .= montajson(utf8_encode("máximo"),$maximo,$hora);
$json .= "]";
echo $json;

/*
echo '
[
{label: "minimo", data:[[0, 4], [1, 6],[2, 3], [6, 5], [12,9]]},
{label: "medio", data:[[0, 10], [1, 12],[2, 16], [3, 6], [4,8]]}
]
';

*/

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