<?php
/*

O arquivo original funcoes_santander_banespa.php foi dividido em 2:
- santander.inc.php 		(contem as linhas inicias com tratamento de variáveis)
- funcoes.santander.php 	(contem somente as funções)

Isto foi necessário para que possam ser impressos mais de um boleto.
Se deixassemos como estava as funções seriam redeclaradas o que ocasionaria erro.

Sempre que implantar um novo banco TEM que fazer esta separação


*/
$codigobanco = "353";
$codigo_banco_com_dv = geraCodigoBanco($codigobanco);
$nummoeda = "9";
$fixo     = "9";   // Numero fixo para a posição 05-05
$ios	  = "0";   // IOS - somente para Seguradoras (Se 7% informar 7, limitado 9%)
				   // Demais clientes usar 0 (zero)
$fator_vencimento = fator_vencimento($dadosboleto["data_vencimento"]);

//valor tem 10 digitos, sem virgula
$valor = formata_numero($dadosboleto["valor_boleto"],10,0,"valor");
//Modalidade Carteira
$carteira = $dadosboleto["carteira"];
//codigocedente deve possuir 7 caracteres
$codigocliente = formata_numero($dadosboleto["codigo_cliente"],7,0);

//nosso número (sem dv) é 11 digitos
$nnum = formata_numero($dadosboleto["nosso_numero"],7,0);
//dv do nosso número
$dv_nosso_numero = modulo_11($nnum,9,0);
// nosso número (com dvs) são 13 digitos
$nossonumero = "00000".$nnum.$dv_nosso_numero;

$vencimento = $dadosboleto["data_vencimento"];

$vencjuliano = dataJuliano($vencimento);

// 43 numeros para o calculo do digito verificador do codigo de barras
$barra = "$codigobanco$nummoeda$fator_vencimento$valor$fixo$codigocliente$nossonumero$ios$carteira";

//$barra = "$codigobanco$nummoeda$fixo$codigocliente$nossonumero$ios$carteira";
$dv = digitoVerificador_barra($barra);
// Numero para o codigo de barras com 44 digitos
$linha = substr($barra,0,4) . $dv . substr($barra,4);

$dadosboleto["codigo_barras"] = $linha;
$dadosboleto["linha_digitavel"] = monta_linha_digitavel($linha);
$dadosboleto["nosso_numero"] = $nossonumero;
$dadosboleto["codigo_banco_com_dv"] = $codigo_banco_com_dv;


?>
