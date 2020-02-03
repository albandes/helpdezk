<?php
session_start();
require_once('../../../../includes/config.php');
require_once('../../../../Connections/conexao.php');
//error_reporting(E_ALL);

$idusuario = $_SESSION['SES_COD_USUARIO']; 
$max = 13;

$sql =	"
		SELECT
		   anomes,
		   sum(total) as total
		from dsh_tbestatgrupomensal
		where idgrupo in(select
							cod_grupo
						 from hdk_usuario_grupo
						 where cod_usuario = ".$idusuario.")
		group by anomes
		order by anomes desc
		limit $max
		";
		
$rs = $conexao->Execute($sql);
	
if(!$rs) die("Erro: " . $conexao->ErrorMsg() . "<br>" . $sql );
$i=1;
while (!$rs->EOF) {
		$grupo[$i] 	= $rs->fields['total'] ;
		$dtedit     = substr($rs->fields['anomes'],0,4) ."-". substr($rs->fields['anomes'],4,2) . "-01"  ;
		$hora[$i] 	= $d1=strtotime($dtedit) * 1000;
		$rs->MoveNext();
		$i++;
}

$sql =	"
		SELECT
		   anomes,
		   sum(total) as total
		from dsh_tbestatatendentemensal
		where idusuario  = ".$idusuario."
		group by anomes
		order by anomes desc
		limit $max
		";
$rs = $conexao->Execute($sql);
	
if(!$rs) die("Erro: " . $conexao->ErrorMsg() . "<br>" . $sql );
$i=1;
while (!$rs->EOF) {
		$atendente[$i] 	= $rs->fields['total'] ;
		$dtedit     = substr($rs->fields['anomes'],0,4) ."-". substr($rs->fields['anomes'],4,2) . "-01"  ;
		$rs->MoveNext();
		$i++;
}
	
$json  = "[";
$json .= montajson(utf8_encode("grupo"),$grupo,$hora);
$json .= ",";
$json .= montajson(utf8_encode("atendente"),$atendente,$hora);
//$json .= ",";
//$json .= montajson(utf8_encode("máximo"),$maximo,$hora);
$json .= "]";
echo $json;
/*
$d1=strtotime("2002-02-20 UTC") * 1000;
$d2=strtotime("2002-03-20 UTC") * 1000;
die($d1 . " " . $d2);
*/

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