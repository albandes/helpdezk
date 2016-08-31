<?php

/*

O arquivo original funcoes_bradesco.php foi dividido em 2:
- bradesco.inc.php 		(contem as linhas inicias com tratamento de variáveis)
- funcoes.bradesco.php 	(contem somente as funções)

Isto foi necessário para que possam ser impressos mais de um boleto.
Se deixassemos como estava as funções seriam redeclaradas o que ocasionaria erro.

Sempre que implantar um novo banco TEM que fazer esta separação


*/

$codigobanco = "237";
$codigo_banco_com_dv = geraCodigoBanco($codigobanco);
$nummoeda = "9";
$fator_vencimento = fator_vencimento($dadosboleto["data_vencimento"]);

//valor tem 10 digitos, sem virgula
$valor = formata_numero($dadosboleto["valor_boleto"],10,0,"valor");
//agencia é 4 digitos
$agencia = formata_numero($dadosboleto["agencia"],4,0);
//conta é 6 digitos
$conta = formata_numero($dadosboleto["conta"],6,0);
//dv da conta
$conta_dv = formata_numero($dadosboleto["conta_dv"],1,0);
//carteira é 2 caracteres
$carteira = $dadosboleto["carteira"];

//nosso número (sem dv) é 11 digitos
$nnum = formata_numero($dadosboleto["carteira"],2,0).formata_numero($dadosboleto["nosso_numero"],11,0);
//dv do nosso número
$dv_nosso_numero = digitoVerificador_nossonumero($nnum);

//conta cedente (sem dv) é 7 digitos
$conta_cedente = formata_numero($dadosboleto["conta_cedente"],7,0);
//dv da conta cedente
$conta_cedente_dv = formata_numero($dadosboleto["conta_cedente_dv"],1,0);

//$ag_contacedente = $agencia . $conta_cedente;

// 43 numeros para o calculo do digito verificador do codigo de barras
$dv = digitoVerificador_barra("$codigobanco$nummoeda$fator_vencimento$valor$agencia$nnum$conta_cedente".'0', 9, 0);
// Numero para o codigo de barras com 44 digitos
$linha = "$codigobanco$nummoeda$dv$fator_vencimento$valor$agencia$nnum$conta_cedente"."0";

$nossonumero = substr($nnum,0,2).'/'.substr($nnum,2).'-'.$dv_nosso_numero;
$agencia_codigo = $agencia."-".$dadosboleto["agencia_dv"]." / ". $conta_cedente ."-". $conta_cedente_dv;


$dadosboleto["codigo_barras"] = $linha;
$dadosboleto["linha_digitavel"] = monta_linha_digitavel($linha);
$dadosboleto["agencia_codigo"] = $agencia_codigo;
$dadosboleto["nosso_numero"] = $nossonumero;
$dadosboleto["codigo_banco_com_dv"] = $codigo_banco_com_dv;


?>
