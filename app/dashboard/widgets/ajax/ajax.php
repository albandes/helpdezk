<?php

//$volta = "Voltou do ajax";

$array = array('hdk-grafico-solicitacoes-mensal', 'mq-total-online', 'hdk-solicitacoes','srv-banda','mq-online-motivo','cms-adsense');
//print_r($array);

$volta = $array;

$output['texto']= $volta;
echo json_encode($output);

?>
