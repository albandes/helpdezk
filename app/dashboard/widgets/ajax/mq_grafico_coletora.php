<?php
//require("../../../../adodb/adodb.inc.php");
require_once('../../../../includes/config.php');
require_once('../../../../Connections/conexao.php');

$sql =	"
	select 
		*
	from 
		dsh_tbwidget
	where 
		nome = 'mq-grafico-coletora'
		";
$rs = $conexao->Execute($sql);

$user  = $rs->fields['dbuser'] ;
$senha = $rs->fields['dbsenha'] ;
$banco = $rs->fields['dbbanco'] ;
$host  = $rs->fields['dbhost'] ;




date_default_timezone_set('America/Sao_Paulo');

$dsn = 'mysql://'.$user.':'.$senha.'@'.$host.'/'.$banco.''; 
$db = NewADOConnection($dsn);
if (!$db) die("Falhou a conexão com o banco"); 

//$inicio = date('Y-m-d', strtotime('-30 days')); // 30 dias atras
//$fim = date('Y-m-d', strtotime('now')); // Hoje

$sql =	"
		select
		   dtestatcoletora,
		   valor
		from tbestatcoletora
		where date(dtestatcoletora) >= DATE_ADD(date(NOW()), INTERVAL - 1 MONTH)
			 and date(dtestatcoletora) <= DATE_ADD(date(NOW()), INTERVAL - 1 DAY)
		order by dtestatcoletora desc
		";
		
$rs = $db->Execute($sql);
if(!$rs) die('<b>Erro:</b> <br> Linha '.__LINE__.' do arquivo ['.__FILE__.']' . '<br>Msg Banco: ' . $db->ErrorMsg() . '<br> SQL: ' . $sql);

$i=1;
while (!$rs->EOF) {
		$valor[$i] 	= $rs->fields['valor']; 
		$hora[$i] 	= strtotime($rs->fields['dtestatcoletora'] . " UTC") * 1000 ; 
		$rs->MoveNext();
		$i++;
}
	
$json  = "[";
$json .= montajson(utf8_encode("Alunos"),$valor,$hora);
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