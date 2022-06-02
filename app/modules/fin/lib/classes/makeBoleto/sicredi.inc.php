<?php
/*

O arquivo original funcoes_santander_banespa.php foi dividido em 2:
- santander.inc.php 		(contem as linhas inicias com tratamento de variсveis)
- funcoes.santander.php 	(contem somente as funчѕes)

Isto foi necessсrio para que possam ser impressos mais de um boleto.
Se deixassemos como estava as funчѕes seriam redeclaradas o que ocasionaria erro.

Sempre que implantar um novo banco TEM que fazer esta separaчуo


*/

$codigobanco = "748";
$codigo_banco_com_dv = geraCodigoBanco($codigobanco);
$nummoeda = "9";
$fator_vencimento = fator_vencimento($dadosboleto["data_vencimento"]);
//valor tem 10 digitos, sem virgula
$valor = formata_numero($dadosboleto["valor_boleto"],10,0,"valor");
//agencia щ 4 digitos
$agencia = formata_numero($dadosboleto["agencia"],4,0);
//posto da cooperativa de credito щ dois digitos
$posto = formata_numero($dadosboleto["posto"],2,0);
//conta щ 5 digitos
$conta = formata_numero($dadosboleto["conta"],5,0);
//dv da conta
$conta_dv = formata_numero($dadosboleto["conta_dv"],1,0);
//carteira щ 2 caracteres
$carteira = $dadosboleto["carteira"];
//fillers - zeros Obs: filler1 contera 1 quando houver valor expresso no campo valor
$filler1 = 1;
$filler2 = 0;
// Byte de Identificaчуo do cedente 1 - Cooperativa; 2 a 9 - Cedente
$byteidt = $dadosboleto["byte_idt"];
// Codigo referente ao tipo de cobranчa: "3" - SICREDI
$tipo_cobranca = 1;
// Codigo referente ao tipo de carteira: "1" - Carteira Simples 
$tipo_carteira = 1;

//nosso nњmero (sem dv) щ 8 digitos
$nnum = $dadosboleto["inicio_nosso_numero"] . $byteidt . formata_numero($dadosboleto["nosso_numero"],5,0);
//calculo do DV do nosso nњmero
$dv_nosso_numero = digitoVerificador_nossonumero("$agencia$posto$conta$nnum");
$nossonumero_dv ="$nnum$dv_nosso_numero";

//formaчуo do campo livre
$campolivre = "$tipo_cobranca$tipo_carteira$nossonumero_dv$agencia$posto$conta$filler1$filler2";
//die($campolivre) ;
$campolivre_dv = $campolivre . digitoVerificador_campolivre($campolivre); 
// 43 numeros para o calculo do digito verificador do codigo de barras
$dv = digitoVerificador_barra("$codigobanco$nummoeda$fator_vencimento$valor$campolivre_dv", 9, 0);
// Numero para o codigo de barras com 44 digitos
$linha = "$codigobanco$nummoeda$dv$fator_vencimento$valor$campolivre_dv";
// Formata strings para impressao no boleto
$nossonumero = substr($nossonumero_dv,0,2).'/'.substr($nossonumero_dv,2,6).'-'.substr($nossonumero_dv,8,1);
$agencia_codigo = $agencia.".". $posto.".".$conta;
$dadosboleto["codigo_barras"] = $linha;
$dadosboleto["linha_digitavel"] = monta_linha_digitavel($linha);
$dadosboleto["agencia_codigo"] = $agencia_codigo;
$dadosboleto["nosso_numero"] = $nossonumero;
$dadosboleto["codigo_banco_com_dv"] = $codigo_banco_com_dv;

?>