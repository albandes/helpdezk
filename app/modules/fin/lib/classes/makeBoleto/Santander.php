<?php

class Santander
{
	public function __construct($data)
    {
      $this->data = $data;
	}
	
	public function setParams()
	{
		$aRet["banco"] = $this->data['banco'];
		
		// DADOS DO BOLETO 
		$taxa_boleto = 0;
		$data_venc = date('Y-m-d') ; 
		$now  	= date('Y-m-d') ; 

		$multa 	= $this->data['multa'];
		$juro 	= $this->data['juro'];
		$valor 	= $this->data['valor'];

		list($diaL,$mesL,$anoL) = explode('/',$this->data['vencimento']);
		$dataVenc = $anoL.'-'.$mesL.'-'.$diaL;
		$data_limite = date('d/m/Y', strtotime("+8 days",strtotime($dataVenc)));

		$juro_e = str_replace(".", ",",$this->data['juro']);
		$multa_e = str_replace(".", ",",$this->data['multa']);
		$valorapagar = $valor  ;
		$aRet["instrucoes1"] = "";
		$aRet["instrucoes2"] = "Multa de $multa_e% .";
		$aRet["instrucoes3"] = "Juros de $juro_e% ao mês.";
		
		if($this->data['flagprotesto'] == 'S'){
			$aRet["instrucoes4"] = "Não receber após $data_limite.";
			$aRet["instrucoes5"] = "Sujeito a protesto após $data_limite.";
		}else{
			$aRet["instrucoes4"] = "";
			$aRet["instrucoes5"] = "";
		}
		
		
		$aRet["data_vencimento"] = $this->data['vencimento']; // Data de Vencimento do Boleto - REGRA: Formato DD/MM/AAAA

		$valor_cobrado = $valorapagar; // Valor - REGRA: Sem pontos na milhar e tanto faz com "." ou "," ou com 1 ou 2 ou sem casa decimal
		$valor_cobrado = str_replace(",", ".",$valor_cobrado);
		$valor_boleto=number_format($valor_cobrado+$taxa_boleto, 2, ',', '');

		$nossonumero = substr($this->data['nossonumero'],0,-2);
		$nossonumero = substr($nossonumero, -7); 

		$aRet["nosso_numero"] = $nossonumero;  // Nosso numero sem o DV - REGRA: Máximo de 7 caracteres!
		$aRet["numero_documento"] = $this->data['idboleto'];	// Num do pedido ou nosso numero

		$aRet["data_documento"] = date("d/m/Y"); // Data de emissão do Boleto
		$aRet["data_processamento"] = date("d/m/Y"); // Data de processamento do boleto (opcional)
		$aRet["valor_boleto"] = $valor_boleto; 	// Valor do Boleto - REGRA: Com vírgula e sempre com duas casas depois da virgula

		// DADOS DO SEU CLIENTE
		$aRet["sacado"] = utf8_decode($this->data['sacado']);

		$aRet["endereco1"] = utf8_decode($this->data['enderecocobranca']);
		$aRet["endereco2"] = $this->data['cep'] . " - " . $this->data['cidade'];

		// INFORMACOES PARA O CLIENTE
		$aRet["demonstrativo1"] = "";
		$aRet["demonstrativo2"] = "";
		$aRet["demonstrativo3"] = "";


		// DADOS OPCIONAIS DE ACORDO COM O BANCO OU CLIENTE
		$aRet["quantidade"] = "";
		$aRet["valor_unitario"] = "";
		$aRet["aceite"] = "N";		
		$aRet["especie"] = "R$";
		$aRet["especie_doc"] = "DM";

		// ---------------------- DADOS FIXOS DE CONFIGURAÇÃO DO SEU BOLETO --------------- //

		list($anoNN,$numberNN) = explode('/',$this->data['nossonumero']);
		$aRet["inicio_nosso_numero"] = $anoNN;	// Ano da geração do título ex: 07 para 2007 
		$aRet = substr($this->data['idboleto'], -5);
		$aRet["nosso_numero"] = $nossonumero;  // // Nosso numero (máx. 5 digitos) - Numero sequencial de controle.

		// DADOS PERSONALIZADOS - SICREDI
		$aRet["byte_idt"]= "2";	  	// Byte de identificação do cedente do bloqueto utilizado para compor o nosso número.
									// 1 - Idtf emitente: Cooperativa | 2 a 9 - Idtf emitente: Cedente
		$aRet["carteira"] = "A";   	//  
		
		// DADOS DA SUA CONTA - SICREDI
		$aRet["agencia"] = $this->data['agencia']; 	// Num da agencia (4 digitos), sem Digito Verificador
		$aRet["conta"] = $this->data['contacedente']; 	// Num da conta (5 digitos), sem Digito Verificador
		$aRet["conta_dv"] = $this->data['dvcontacedente']; 	// Digito Verificador do Num da conta
		$aRet["especie_doc"] = "DMI";

		$cnpj = $this->mask($this->data['cnpjcedente'],'##.###.###/####-##');
		$aRet["cedente"] = utf8_decode($this->data['nomecedente']);
		$aRet["cpf_cnpj"] = $cnpj;

		$codigobanco = "748";
		$codigo_banco_com_dv = $this->geraCodigoBanco($codigobanco);
		$nummoeda = "9";
		$fator_vencimento = $this->fator_vencimento($this->data['vencimento']);

		$valor = $this->formata_numero($valor_boleto,10,0,"valor");//valor tem 10 digitos, sem virgula
		$agencia = $this->formata_numero($this->data['agencia'],4,0);//agencia é 4 digitos
		$posto = $this->formata_numero("07",2,0);//posto da cooperativa de credito é dois digitos
		$conta = $this->formata_numero($this->data['contacedente'],5,0);//conta é 5 digitos
		$conta_dv = $this->formata_numero($this->data['dvcontacedente'],1,0);//dv da conta
		$carteira = "A"; //carteira é 2 caracteres - Código da Carteira: A (Simples)
		$filler1 = 1;//fillers - zeros Obs: filler1 contera 1 quando houver valor expresso no campo valor
		$filler2 = 0;
		$byteidt = "2";// Byte de Identificação do cedente 1 - Cooperativa; 2 a 9 - Cedente
		$tipo_cobranca = 1;// Codigo referente ao tipo de cobrança: "3" - SICREDI
		$tipo_carteira = 1;// Codigo referente ao tipo de carteira: "1" - Carteira Simples 

		$nnum = $anoNN . $byteidt . $this->formata_numero($nossonumero,5,0);//nosso número (sem dv) é 8 digitos
		$dv_nosso_numero = $this->digitoVerificador_nossonumero("$agencia$posto$conta$nnum");//calculo do DV do nosso número
		$nossonumero_dv ="$nnum$dv_nosso_numero";

		$campolivre = "$tipo_cobranca$tipo_carteira$nossonumero_dv$agencia$posto$conta$filler1$filler2";//formação do campo livre
		//die($campolivre) ;
		$campolivre_dv = $campolivre . $this->digitoVerificador_campolivre($campolivre); 

		$dv = $this->digitoVerificador_barra("$codigobanco$nummoeda$fator_vencimento$valor$campolivre_dv", 9, 0);// 43 numeros para o calculo do digito verificador do codigo de barras
		$linha = "$codigobanco$nummoeda$dv$fator_vencimento$valor$campolivre_dv";// Numero para o codigo de barras com 44 digitos

		// Formata strings para impressao no boleto
		$nossonumero = substr($nossonumero_dv,0,2).'/'.substr($nossonumero_dv,2,6).'-'.substr($nossonumero_dv,8,1);
		$agencia_codigo = $agencia.".". $posto.".".$conta;

		$dadosboleto["codigo_barras"] = $linha;
		$dadosboleto["linha_digitavel"] = $this->monta_linha_digitavel($linha);
		$dadosboleto["agencia_codigo"] = $agencia_codigo;
		$dadosboleto["nosso_numero"] = $nossonumero;
		$dadosboleto["codigo_banco_com_dv"] = $codigo_banco_com_dv;

        if(isset($this->data['valor_desconto']))
            $aRet['valor_desconto'] = $this->data['valor_desconto'];

        if(isset($this->data['valor_cobrado']))
            $aRet['valor_cobrado'] = $this->data['valor_cobrado'];
		
		return $aRet;
	}
	
	public function digitoVerificador_nossonumero($numero) {
		$resto2 = $this->modulo_11($numero, 9, 1);
		// esta rotina sofrer algumas alterações para ajustar no layout do SICREDI
		$digito = 11 - $resto2;
		if ($digito > 9 ) {
			$dv = 0;
		} else {
			$dv = $digito;
		}
		return $dv;
	}

	function digitoVerificador_campolivre($numero) {
		$resto2 = $this->modulo_11($numero, 9, 1);
		// esta rotina sofreu algumas alterações para ajustar no layout do SICREDI
		if ($resto2 <=1){
		$dv = 0;
		}else{
		$dv = 11 - $resto2;
		}
		return $dv;
	}

	function digitoVerificador_barra($numero) {
		$resto2 = $this->modulo_11($numero, 9, 1);
		// esta rotina sofrer algumas alterações para ajustar no layout do SICREDI
		$digito = 11 - $resto2;
		if ($digito <= 1 || $digito >= 10 ) {
			$dv = 1;
		} else {
			$dv = $digito;
		}
		return $dv;
	}

	// FUN��ES
	// Algumas foram retiradas do Projeto PhpBoleto e modificadas para atender as particularidades de cada banco
	function formata_numero($numero,$loop,$insert,$tipo = "geral") {
		if ($tipo == "geral") {
		$numero = str_replace(",","",$numero);
		while(strlen($numero)<$loop){
			$numero = $insert . $numero;
		}
		}
		if ($tipo == "valor") {
		/*
		retira as virgulas
		formata o numero
		preenche com zeros
		*/
		$numero = str_replace(",","",$numero);
		while(strlen($numero)<$loop){
			$numero = $insert . $numero;
		}
		}
		if ($tipo == "convenio") {
		while(strlen($numero)<$loop){
			$numero = $numero . $insert;
		}
		}
		return $numero;
	}

	function esquerda($entra,$comp){
		return substr($entra,0,$comp);
	}

	function direita($entra,$comp){
		return substr($entra,strlen($entra)-$comp,$comp);
	}

	function fator_vencimento($data) {
		$data = explode("/",$data);
		$ano = $data[2];
		$mes = $data[1];
		$dia = $data[0];
		return(abs(($this->_dateToDays("1997","10","07")) - ($this->_dateToDays($ano, $mes, $dia))));
	}

	function _dateToDays($year,$month,$day) {
		$century = substr($year, 0, 2);
		$year = substr($year, 2, 2);
		if ($month > 2) {
			$month -= 3;
		} else {
			$month += 9;
			if ($year) {
				$year--;
			} else {
				$year = 99;
				$century --;
			}
		}
		return ( floor((  146097 * $century)    /  4 ) +
				floor(( 1461 * $year)        /  4 ) +
				floor(( 153 * $month +  2) /  5 ) +
					$day +  1721119);
	}

	function modulo_10($num) { 
		$numtotal10 = 0;
			$fator = 2;
			// Separacao dos numeros
			for ($i = strlen($num); $i > 0; $i--) {
				// pega cada numero isoladamente
				$numeros[$i] = substr($num,$i-1,1);
				// Efetua multiplicacao do numero pelo (falor 10)
				// 2002-07-07 01:33:34 Macete para adequar ao Mod10 do Ita�
				$temp = $numeros[$i] * $fator; 
				$temp0=0;
				foreach (preg_split('//',$temp,-1,PREG_SPLIT_NO_EMPTY) as $k=>$v){ $temp0+=$v; }
				$parcial10[$i] = $temp0; //$numeros[$i] * $fator;
				// monta sequencia para soma dos digitos no (modulo 10)
				$numtotal10 += $parcial10[$i];
				if ($fator == 2) {
					$fator = 1;
				} else {
					$fator = 2; // intercala fator de multiplicacao (modulo 10)
				}
			}
		
			// várias linhas removidas, vide função original
			// Calculo do modulo 10
			$resto = $numtotal10 % 10;
			$digito = 10 - $resto;
			if ($resto == 0) {
				$digito = 0;
			}
		
			return $digito;
		
	}

	/**
	 *   @author Pablo Costa <pablo@users.sourceforge.net>
	 *
	 *   Calculo do Modulo 11 para geracao do digito verificador 
	 *   de boletos bancarios conforme documentos obtidos 
	 *   da Febraban - www.febraban.org.br 
	 *
	 *   @param  mixed $num string numérica para a qual se deseja calcularo digito verificador;
	 *   @param  mixed $base valor maximo de multiplicacao [2-$base]
	 *   @param  mixed $r quando especificado um devolve somente o resto
	 *
	 *   @return void Retorna o Digito verificador.
	 *
	 *   Observações:
	 *     - Script desenvolvido sem nenhum reaproveitamento de código pré existente.
	 *     - Assume-se que a verificação do formato das variáveis de entrada é feita antes da execução deste script.
	 */
	function modulo_11($num, $base=9, $r=0)  {                                        
		$soma = 0;
		$fator = 2;
		/* Separacao dos numeros */
		for ($i = strlen($num); $i > 0; $i--) {
			// pega cada numero isoladamente
			$numeros[$i] = substr($num,$i-1,1);
			// Efetua multiplicacao do numero pelo falor
			$parcial[$i] = $numeros[$i] * $fator;
			// Soma dos digitos
			$soma += $parcial[$i];
			if ($fator == $base) {
				// restaura fator de multiplicacao para 2 
				$fator = 1;
			}
			$fator++;
		}
		/* Calculo do modulo 11 */
		if ($r == 0) {
			$soma *= 10;
			$digito = $soma % 11;
			return $digito;
		} elseif ($r == 1){
			// esta rotina sofrer algumas alterações para ajustar no layout do SICREDI
			$r_div = (int)($soma/11);
			$digito = ($soma - ($r_div * 11));
			return $digito;
		}
	}

	function monta_linha_digitavel($codigo) {
		
		// COMPOSICAO DO CODIGO		
		// Posição | Larg | Conteúdo
		// --------+------+---------------
		// 1 a 3   |  03  | Identificação do banco
		// 4       |  01  | Código da Moeda - 9 para R$
		// 5       |  01  | Digito verificador geral do Código de Barras
		// 6 a 9   |  04  | Fator de Vencimento
		// 10 a 19 |  10  | Valor (8 inteiros e 2 decimais)
		// 20 a 44 |  25  | Campo Livre definido por cada banco (25 caracteres)
		//COMPOSICAO DA LINHA DIGITAVEL
			
		// 1. Campo - composto pelo código do banco, código da moeda, as cinco primeiras posições
		// do campo livre e DV (modulo10) deste campo
		$p1 = substr($codigo, 0, 4);
		$p2 = substr($codigo, 19, 5);
		$p3 = $this->modulo_10("$p1$p2");
		$p4 = "$p1$p2$p3";
		$p5 = substr($p4, 0, 5);
		$p6 = substr($p4, 5);
		$campo1 = "$p5.$p6";
		// 2. Campo - composto pelas posições 6 a 15 do campo livre
		// e livre e DV (modulo10) deste campo
		$p1 = substr($codigo, 24, 10);
		$p2 = $this->modulo_10($p1);
		$p3 = "$p1$p2";
		$p4 = substr($p3, 0, 5);
		$p5 = substr($p3, 5);
		$campo2 = "$p4.$p5";
		// 3. Campo composto pelas posições 16 a 25 do campo livre
		// e livre e DV (modulo10) deste campo
		$p1 = substr($codigo, 34, 10);
		$p2 = $this->modulo_10($p1);
		$p3 = "$p1$p2";
		$p4 = substr($p3, 0, 5);
		$p5 = substr($p3, 5);
		$campo3 = "$p4.$p5";
		// 4. Campo - digito verificador do código de barras
		$campo4 = substr($codigo, 4, 1);
		// 5. Campo composto pelo fator vencimento e valor nominal do documento, sem
		// indicacao de zeros a esquerda e sem edicao (sem ponto e virgula). Quando se
		// tratar de valor zerado, a representacao deve ser 000 (tres zeros).
		$p1 = substr($codigo, 5, 4);
		$p2 = substr($codigo, 9, 10);
		$campo5 = "$p1$p2";
		
		return "$campo1 $campo2 $campo3 $campo4 $campo5"; 
	}

	function geraCodigoBanco($numero) {
		$parte1 = substr($numero, 0, 3);
		//$parte2 = modulo_11($parte1);
		return $parte1 . "-X";
	}

	function mask($val, $mask){
		$maskared = '';
		$k = 0;
		for($i = 0; $i<=strlen($mask)-1; $i++){
			if($mask[$i] == '#'){
				if(isset($val[$k]))
				$maskared .= $val[$k++];
			}
			else{
				if(isset($mask[$i]))
				$maskared .= $mask[$i];
			}
		}
		return $maskared;
	}

}



