<?php
define('ga_email','rogerio.albandes@gmail.com');
define('ga_password','Piroc@.2009');

require 'gapi.class.php';

$ga = new gapi(ga_email,ga_password);

$ga->requestAccountData();

foreach($ga->getResults() as $result)
{
  echo $result . ' (' . $result->getProfileId() . ")<br />";
}


// ID do perfil do site
$id = '19222677';

/*
// Define o periodo do relat�rio
$inicio = date('Y-m-01', strtotime('-1 month')); // 1� dia do m�s passado
$fim = date('Y-m-t', strtotime('-1 month')); // �ltimo dia do m�s passado

// Busca os pageviews e visitas (total do m�s passado)
$ga->requestReportData($id, 'month', array('pageviews', 'visits'), null, null, $inicio, $fim);
foreach ($ga->getResults() as $dados) {
	echo 'M�s ' . $dados . ': ' . $dados->getVisits() . ' Visita(s) e ' . $dados->getPageviews() . ' Pageview(s)<br />';
}

echo '<br />';

// Busca os pageviews e visitas de cada dia do �ltimo m�s
$ga->requestReportData($id, 'day', array('pageviews', 'visits'), 'day', null, $inicio, $fim, 1, 50);
foreach ($ga->getResults() as $dados) {
	echo 'Dia ' . $dados . ': ' . $dados->getVisits() . ' Visita(s) e ' . $dados->getPageviews() . ' Pageview(s)<br />';
}


*/

// Busca os pageviews e visitas (total do m�s passado)
$ga->requestReportData(19222677, 'hour', array('pageviews', 'visits'), hour, null, '2010-09-28', '2010-09-28');
foreach ($ga->getResults() as $dados) {
	echo 'futeboldaqui - ' . $dados . ': ' . $dados->getVisits() . ' Visita(s) e ' . $dados->getPageviews() . ' Pageview(s)<br />';
}

$ga->requestReportData(36544647, 'day', array('pageviews', 'visits'), day, null, '2010-09-27', '2010-09-27');
foreach ($ga->getResults() as $dados) {
	echo 'grandearea -  ' . $dados . ': ' . $dados->getVisits() . ' Visita(s) e ' . $dados->getPageviews() . ' Pageview(s)<br />';
}
?>