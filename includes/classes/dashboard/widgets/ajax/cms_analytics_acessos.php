<?php
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

$user  = $rs->fields['campo1'] ;
$senha = $rs->fields['campo2'] ;
$id = $rs->fields['campo3'] ;

// pegar da tabela de widgets -
$user  = 'rogerio.albandes@gmail.com' ;
$senha = 'Piroc@.2009';
$id = 46811633; // MQ

date_default_timezone_set('America/Sao_Paulo');

/**
 ** GAPI
 **/ 
define('ga_email','rogerio.albandes@gmail.com');
define('ga_password','Piroc@.2009');
require '../classes/gapi/gapi.class.php';
$ga = new gapi(ga_email,ga_password);

// Busca os pageviews e visitas (últimos 30 dias)  
$ga->requestReportData($id, 'date', array('pageviews', 'visits'), date, null, $inicio, $fim,null,null);
$i=1;
foreach ($ga->getResults() as $dados) 
{
	//echo  date('Y-m-d', strtotime('+'. $i .'days')). "<br>";
	//echo 'Dia ' . date('Y-m-d', strtotime($dados)) .  ': ' . $dados->getVisits() . ' Visita(s) e ' . $dados->getPageviews() . ' Pageview(s)<br />';
	$acessos[$i] 	= $dados->getVisits() ;
	$pageviews[$i] 	= $dados->getPageviews() ;
	$data[$i] 		= strtotime(date('Y-m-d', strtotime($dados)) . " UTC") * 1000 ;
	$i++;
}


/*
$i=1;
while (!$rs->EOF) {
		$minimo[$i] 	= $rs->fields['minimo'] ;
		$media[$i] 		= $rs->fields['media']; 
		$maximo[$i] 	= $rs->fields['maximo']; 
		$hora[$i] 		= strtotime($rs->fields['datahora'] . " UTC") * 1000 ; 

		$rs->MoveNext();
		$i++;
}
*/
	
$json  = "[";
$json .= montajson(utf8_encode("Visitas"),$acessos,$data);
$json .= ",";
$json .= montajson(utf8_encode("Page Views"),$pageviews,$data);
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