<?php
require_once('../../../../includes/config.php');
require_once('../../../../Connections/conexao.php');

// Pegar do banco 
$sql =	"
	select 
		campo1, campo2
	from 
		dsh_tbwidget
	where 
		nome = 'cms-adsense'
		";
$rs = $conexao->Execute($sql);


	require_once('../classes/adsense/adsense.php');

	date_default_timezone_set('America/Sao_Paulo');
	
	$username = $rs->fields['campo1'] ; 
	$password = $rs->fields['campo2'] ; 

	$adsense = new AdSense();
	if (!$adsense->connect($username, $password)) {
		die('Could not login to AdSense account.');
	};
	
	// Array ( [impressions] => 29.308 [clicks] => 48 [ctr] => 0,16% [ecpm] => US$0,09 [earnings] => US$2,65 ) 
	$ret = $adsense->today();
	foreach ($ret as $key => $value) 
	{ 
		$today[$key] = str_replace(Array("US$", "%", "."), "", $value); 
		$today[$key] = str_replace(",", ".", $ret[$key]); 
	} 

	$ret = $adsense->yesterday();
	foreach ($ret as $key => $value) 
	{ 
		$yesterday[$key] = str_replace(Array("US$", "%", "."), "", $value); 
		$yesterday[$key] = str_replace(",", ".", $ret[$key]); 
	} 
	
	$ret = $adsense->last7days();
	foreach ($ret as $key => $value) 
	{ 
		$last7days[$key] = str_replace(Array("US$", "%", "."), "", $value); 
		$last7days[$key] = str_replace(",", ".", $ret[$key]); 
	} 

	$ret = $adsense->thismonth();
	foreach ($ret as $key => $value) 
	{ 
		$thismonth[$key] = str_replace(Array("US$", "%", "."), "", $value); 
		$thismonth[$key] = str_replace(",", ".", $ret[$key]); 
	} 	

	$ret = $adsense->lastmonth();
	foreach ($ret as $key => $value) 
	{ 
		$lastmonth[$key] = str_replace(Array("US$", "%", "."), "", $value); 
		$lastmonth[$key] = str_replace(",", ".", $ret[$key]); 
	} 

	$ret = $adsense->sincelastpayment();
	foreach ($ret as $key => $value) 
	{ 
		$sincelastpayment[$key] = str_replace(Array("US$", "%", "."), "", $value); 
		$sincelastpayment[$key] = str_replace(",", ".", $ret[$key]); 
	} 



$volta = "
<div style=\"padding-top: 5px;\">
<table id=\"adsense\" align=\"center\">
<tr>
  <th>Posição [". date("H:i:s") ."]</th>
  <th>Impressões</th>
  <th>Cliques</th>
  <th>CTR</th>
  <th>eCMP</th>
  <th>Ganhos</th>
</tr>
<tr>
	<td>Hoje</td>
	<td>". $today['impressions']. "</td>
	<td>". $today['clicks']. "</td>
	<td>". $today['ctr']. "</td>
	<td>". $today['ecpm']. "</td>
	<td>". $today['earnings']. "</td>
</tr>
<tr>
	<td>Ontem </td>
	<td>". $yesterday['impressions']. "</td>
	<td>". $yesterday['clicks']. "</td>
	<td>". $yesterday['ctr']. "</td>
	<td>". $yesterday['ecpm']. "</td>
	<td>". $yesterday['earnings']. "</td>
</tr>
<tr>
	<td>Últimos 7 dias</td>
	<td>". $last7days['impressions']. "</td>
	<td>". $last7days['clicks']. "</td>
	<td>". $last7days['ctr']. "</td>
	<td>". $last7days['ecpm']. "</td>
	<td>". $last7days['earnings']. "</td>
</tr>
<tr>
	<td>Este mês</td>
	<td>". $thismonth['impressions']. "</td>
	<td>". $thismonth['clicks']. "</td>
	<td>". $thismonth['ctr']. "</td>
	<td>". $thismonth['ecpm']. "</td>
	<td>". $thismonth['earnings']. "</td>
</tr>
<tr>
	<td>Último mês</td>
	<td>". $lastmonth['impressions']. "</td>
	<td>". $lastmonth['clicks']. "</td>
	<td>". $lastmonth['ctr']. "</td>
	<td>". $lastmonth['ecpm']. "</td>
	<td>". $lastmonth['earnings']. "</td>
</tr>
<tr>
	<td>Desde o último pgto.</td>
	<td>". $sincelastpayment['impressions']. "</td>
	<td>". $sincelastpayment['clicks']. "</td>
	<td>". $sincelastpayment['ctr']. "</td>
	<td>". $sincelastpayment['ecpm']. "</td>
	<td>". $sincelastpayment['earnings']. "</td>
</tr>
</table>
	
";



$output['texto']= $volta;
echo json_encode($output);
	
