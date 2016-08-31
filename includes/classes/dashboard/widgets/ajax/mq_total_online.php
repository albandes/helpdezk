<?php

require_once('../../../../includes/config.php');
require_once('../../../../Connections/conexao.php');

$sql =	"
	select 
		*
	from 
		dsh_tbwidget
	where 
		nome = 'mq-total-online'
		";
$rs = $conexao->Execute($sql);

$user  = $rs->fields['dbuser'] ;
$senha = $rs->fields['dbsenha'] ;
$banco = $rs->fields['dbbanco'] ;
$host  = $rs->fields['dbhost'] ;
$dInicial = $rs->fields['campo1'] ;

//$dInicial = "22/02/2011" ;
$dFinal   = date("d/m/Y") ;

$dsn = 'mysql://'.$user.':'.$senha.'@'.$host.'/'.$banco.''; 
$db = NewADOConnection($dsn);
if (!$db) die("Falhou a conexão com o banco");   

$sql =	"
		select
		   diasemana,
		   count(*) as total
		from (select *
			  from horario_infantil hi
			  where hi.CoPessoa != 1 union(select *
										   from horario h
										   where h.CoPessoa != 1)) c
		where DiaSemana <= 5
		group by DiaSemana
		order by DiaSemana
		";
$rs = $db->Execute($sql);
if(!$rs) die('<b>Erro:</b> <br> Linha '.__LINE__.' do arquivo ['.__FILE__.']' . '<br>Msg Banco: ' . $db->ErrorMsg() . '<br> SQL: ' . $sql);

while (!$rs->EOF) {
	$aNumAulas[$rs->fields['diasemana']] = $rs->fields['total'] ;
	$rs->MoveNext();
}

$aDtInicial = explode("/", $dInicial);
$aDtFinal   = explode("/", $dFinal);

$UnixInicial = mktime(0, 0, 0, $aDtInicial[1],$aDtInicial[0] , $aDtInicial[2]);
$UnixFinal   = mktime(0, 0, 0, $aDtFinal[1],$aDtFinal[0] , $aDtFinal[2]);

$UnixDif = $UnixFinal  - $UnixInicial;
$dias = round(($UnixDif/60/60/24));

$aMeses = array();
$total = 0;
$primeiro = true ;
for($i = 0; $i <= $dias; $i++)
{
	if ($primeiro) 
	{
		$mes = date("m", mktime(0, 0, 0, $aDtInicial[1],$aDtInicial[0] + $i, $aDtInicial[2]) );	
		array_push($aMeses,$mes);
		$primeiro = false;
	}
	if($mes != date("m", mktime(0, 0, 0, $aDtInicial[1],$aDtInicial[0] + $i, $aDtInicial[2]))) 
	{
		$mes = date("m", mktime(0, 0, 0, $aDtInicial[1],$aDtInicial[0] + $i, $aDtInicial[2]) );	
		array_push($aMeses,$mes);
	}
	
	$diasemana = date("w", mktime(0, 0, 0, $aDtInicial[1],$aDtInicial[0] + $i, $aDtInicial[2]) );	

	switch($diasemana) {
		case"1": 
			$total = $total + $aNumAulas[1] ;
			$aAulasMes[$mes] = $aAulasMes[$mes] + $aNumAulas[1];  
		break;
		case"2": 
			$total = $total + $aNumAulas[2] ;
			$aAulasMes[$mes] = $aAulasMes[$mes] + $aNumAulas[2];
		break;
		case"3": 
			$total = $total + $aNumAulas[3] ;
			$aAulasMes[$mes] = $aAulasMes[$mes] + $aNumAulas[3];
			break;
		case"4": 
			$total = $total + $aNumAulas[4] ;
			$aAulasMes[$mes] = $aAulasMes[$mes] + $aNumAulas[4];
		  break;
		case"5": 
			$total = $total + $aNumAulas[5] ;
			$aAulasMes[$mes] = $aAulasMes[$mes] + $aNumAulas[5];
			break;
	}
}

// print_r($aAulasMes); print "<br>";

$i = 1; 
foreach ($aAulasMes as $k => $v) {
	$aTotalAulas[$i] = $v;
	$aMes[$i] = $k;
    $i++;
}

// print_r($aTotalAulas); print "<br>"; print_r($aMes); print "<br>";

$DtinicialEdit = $aDtInicial[2] . "-" . $aDtInicial[1] . "-". $aDtInicial[0] ;
$sql = 	"
		SELECT
		   month(a.Data) as mes,
		   CONCAT_WS('-', year(a.Data), month(a.Data), '01') dtconcat,
		   count(*) as total
		FROM ao_horario a
		where year(a.data) = year(now())
			 and a.Aprovada = 'S'
			 and (month(a.Data) >= month('".$DtinicialEdit."')
				  and a.Data <= now())
			 and a.Aula <> 'Reunião'
			 and a.Aula <> 'ESPORTE'
			 and a.Aula <> 'Dança'
		group by month(a.Data)

		";
$rs = $db->Execute($sql);
if(!$rs) die('<b>Erro:</b> <br> Linha '.__LINE__.' do arquivo ['.__FILE__.']' . '<br>Msg Banco: ' . $db->ErrorMsg() . '<br> SQL: ' . $sql);

$aMostraMes = array();
$i=1;
while (!$rs->EOF) {
	$aNumOnline[$i] 	= $rs->fields['total'] ;
	$online 			= $online +  $rs->fields['total'] ;
	$aMostraMes[$i] 	= strtotime($rs->fields['dtconcat']) * 1000;
	$rs->MoveNext();
	$i++;
}		

$perc = round(($online * 100)/$total,2);
$mostraonline = number_format($online, 0, ',','.') . " [".number_format($perc, 2, ',','.')."%]";;

$json  = "[";
$json .= montajson(utf8_encode("Total:").number_format($total, 0, ',','.'),$aTotalAulas,$aMostraMes);
$json .= ",";
$json .= montajson(utf8_encode("Online: ").$mostraonline,$aNumOnline,$aMostraMes);
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