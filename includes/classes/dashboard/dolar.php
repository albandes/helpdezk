<?php
 
//CODIGO HTML: <td ALIGN="CENTER" class="fundoPadraoBClaro2">11/06/2010</td><td ALIGN="right" class="fundoPadraoBClaro2">1,8117</td>
 
 
//URL DO SITE A SER CAPTURADO
$url = 'http://www4.bcb.gov.br/pec/taxas/batch/taxas.asp?id=txdolar&id=txdolar';
 
//PEGAR TODO CÓDIGO HTML PARA UMA VARIAVEL STRING
$site = file_get_contents($url);
 
//PEGAR A DATA
$data1 = explode('<td ALIGN="CENTER" class="fundoPadraoBClaro2">', $site);
$data2 = explode('</td>',$data1[1]);
 
$data = $data2[0];
 
//PEGAR COTAÇÃO
$cotacao1 = explode('<td ALIGN="right" class="fundoPadraoBClaro2">', $site);
$cotacao2 = explode('</td>',$cotacao1[1]);
 
$cotacao = $cotacao2[0];
 
print 'Data: '.$data.' Cotação: R$ '.$cotacao;
 
?>
?> 