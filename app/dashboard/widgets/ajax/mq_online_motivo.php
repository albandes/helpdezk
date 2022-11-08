<?php
require_once('../../../../includes/config.php');
require_once('../../../../Connections/conexao.php');

$sql =	"
	select 
		*
	from 
		dsh_tbwidget
	where 
		nome = 'mq-online-motivo'
		";
$rs = $conexao->Execute($sql);

$user  = $rs->fields['dbuser'] ;
$senha = $rs->fields['dbsenha'] ;
$banco = $rs->fields['dbbanco'] ;
$host  = $rs->fields['dbhost'] ;
$dInicial = $rs->fields['campo1'] ;



$aDtInicial = explode("/", $dInicial);
$DataInicial =  $aDtInicial[2]."-".$aDtInicial[1]."-".$aDtInicial[0];

// print 'data inicial: ' . $DataInicial . "<br>";
$dsn = 'mysql://'.$user.':'.$senha.'@'.$host.'/'.$banco.''; 
$db = NewADOConnection($dsn);
if (!$db) die("Falhou a conexão com o banco");   

$sql =	"
		SELECT count(CoAoHorario) as total
		FROM ao_horario a
		where year(a.data) = year(now())
			 and a.Aprovada = 'S'
			 and (month(a.Data) >= month('".$DataInicial."')
				  and a.Data <= now())
			 and a.Aula <> 'Reunião'
			 and a.Aula <> 'ESPORTE'
			 and a.Aula <> 'Dança'
		";
$rs = $db->Execute($sql);
if(!$rs) die('<b>Erro:</b> <br> Linha '.__LINE__.' do arquivo ['.__FILE__.']' . '<br>Msg Banco: ' . $db->ErrorMsg() . '<br> SQL: ' . $sql);
$totalaulasonline = $rs->fields['total'];

// print "Total de aulas online: " . $totalaulasonline . "<br>";

$sql = 	"
		SELECT
		   a.CoMotivo,
		   b.DsMotivo,
		   count( a.CoMotivo) as total
		FROM ao_horario a,
		   ao_motivo b
		WHERE (Data BETWEEN '".$DataInicial."'
			   AND now())
			 AND CoMotivo > 0
			 AND Ok != ''
			 and a.CoMotivo = b.CoAoMotivo
		group by a.CoMotivo
		ORDER BY total desc
		";
$rs = $db->Execute($sql);
if(!$rs) die('<b>Erro:</b> <br> Linha '.__LINE__.' do arquivo ['.__FILE__.']' . '<br>Msg Banco: ' . $db->ErrorMsg() . '<br> SQL: ' . $sql);

$i = 1;
$max = 4;
$outros = 0;

$json  = "[" ;
while (!$rs->EOF) {
    if ($i <= $max) {
		$json .= "{label:'".utf8_encode($rs->fields['DsMotivo'])." ', data:".$rs->fields['total']."}"  . "," ;
	} else {
	    $outros = $outros + $rs->fields['total'] ;
	} 
	$rs->MoveNext();
	$i++;
}
if ($i > $max) {
		$json .= "{label:'Outros ', data:".$outros."}"  . "," ;
}		
$json .= "]";

// print_r($aNumOnline); print "<br>";

//$json = "[{label:'Cancelada pelo Coordenador ', data:741},{label:'Coordenação Esqueceu de Aprovar ', data:462},{label:'Troca de Horário ', data:266},{label:'Fora do Prazo ', data:222},{label:'Outros ', data:474}]";
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