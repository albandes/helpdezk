<?php
class BankReturnSicrediStatement 
{
	/**
	* @property int HEADER_ARQUIVO 
	*/
	const HEADER_FILE = 0;
	/**
	* @property int HEADER_LOTE 
	*/
	const HEADER_LOT = 1;
	/**
	* @property int DETALHE 
	*/
	const DETAIL = 3;
	/**
	* @property int TRAILER_LOTE 
	*/
	const TRAILER_LOT = 5;
	/** 
	* @property int TRAILER_ARQUIVO 
	*/
	const TRAILER_FILE = 9;
	
	/** Processa uma line_arquivo_retorno.
      * @param int $numLn Número_line a ser processada
	  * @param string $line String contendo a line a ser processada
	  * @return array Retorna um vetor associativo contendo os valores_line processada.
	 **/
	function runLine($numLn, $line, $segment = '')
    {
        $line = " $line";
        $typeLn = substr($line,  8,  1);

        if($typeLn == BankReturnSicrediStatement::HEADER_FILE)
             $arrLine = $this->runHeaderFile($line);
        else if($typeLn == BankReturnSicrediStatement::HEADER_LOT)
            $arrLine = $this->runHeaderLot($line);
        else if($typeLn == BankReturnSicrediStatement::DETAIL)
            $arrLine = $this->runDetail($line,$segment);
        else if($typeLn == BankReturnSicrediStatement::TRAILER_LOT)
            $arrLine = $this->runTrailerLot($line,$segment);
        else if($typeLn == BankReturnSicrediStatement::TRAILER_FILE)
            $arrLine = $this->runTrailerFile($line);

        return $arrLine;


	}	


	function runHeaderFile($line)
    {
	    $arrLine = array();
		$arrLine["banco"] 				  	= substr($line,  1,   3); //NUMERICO //Código do Banco na Compensação
		$arrLine["lote"]			  		= substr($line,  4,   4); //num - default 0000 //Lote de Serviço
		$arrLine["registro"]     		    = substr($line,  8,   1); //num - default 0 //Tipo de Registro
		$arrLine["cnab1"]                   = substr($line,  9,   9); //BRANCOS //Uso Exclusivo FEBRABAN / CNAB
		$arrLine["tipo_inscricao_empresa"]  = substr($line, 18,   1); //num - 1-CPF, 2-CGC //Tipo de Inscrição da Empresa
		$arrLine["num_inscricao_empresa"]   = substr($line, 19,  14); //numerico  //Número de Inscrição da Empresa
		$arrLine["cod_convenio"] 			= substr($line, 33,  20); //alfanumerico  //Código do Convênio no Banco
		$arrLine["agencia"] 				= substr($line, 53,   5); //numerico //Agência Mantenedora da Conta
		$arrLine["dv_agencia"] 				= substr($line, 58,   1); //alfanumerico //DV da Agência
		$arrLine["conta_corrente"] 			= substr($line, 59,  12); //numerico //Número da Conta Corrente
		$arrLine["dv_conta"] 				= substr($line, 71,   1); //alfanumerico  //DV da Conta Corrent
		$arrLine["dv_ag_conta"]				= substr($line, 72,   1); //alfanumerico 
		$arrLine["nome_empresa"] 			= substr($line, 73,  30); //alfanumerico 
		$arrLine["nome_banco"] 				= substr($line, 103, 30); //alfanumerico 
		$arrLine["uso_febraban_cnab2"] 		= substr($line, 133, 10); //brancos //Uso Exclusivo FEBRABAN / CNAB
		$arrLine["cod_arq"] 				= substr($line, 143,  1); //num - 1-REM E 2-RET ?? //Código do arquivo de remessa/retorno
		$arrLine["data_geracao_arq"] 		= substr($line, 144,  8); //num - formato ddmmaaaa
		$arrLine["hora_geracao_arq"] 		= substr($line, 152,  6); //num - formato hhmmss
		$arrLine["sequencia"] 				= substr($line, 158,  6); //numerico //Número Sequencial do Arquivo
		$arrLine["versao_layout_arq"] 		= substr($line, 164,  3); //num 084 //Num da Versão do Layout do Arquivo
		$arrLine["densidade"]				= substr($line, 167,  5); //numerico //Densidade de Gravação do Arquivo
		$arrLine["reservado_banco"] 		= substr($line, 172, 20); //alfanumerico //Para Uso Reservado do Banco
		$arrLine["reservado_empresa"] 		= substr($line, 192, 20); //alfanumerico //Para Uso Reservado da Empresa
		$arrLine["uso_febraban_cnab3"] 		= substr($line, 212, 29); //brancos //Uso Exclusivo FEBRABAN / CNAB

	    return $arrLine;
	}

    function runHeaderLot($line) {
        $arrLine = array();
        $arrLine["banco"] 		            = substr($line,  1,  3); //numerico //Código do Banco na Compensação
        $arrLine["lote"]                    = substr($line,  4,  4); //numerico //Lote de Serviço
        $arrLine["registro"]                = substr($line,  8,  1); //num - default 1 //Tipo de Registro
        $arrLine["operacao"]                = substr($line,  9,  1); //alfanumerico - default C //Tipo da Operação
        $arrLine["servico"]                 = substr($line, 10,  2); //num  //Tipo do Serviço
        $arrLine["forma_lancamento"]        = substr($line, 12,  2); //num //Forma de Lançamento
        $arrLine["layout_lote"]             = substr($line, 14,  3); //num - default '030' //No da Versão do Layout do Lote
        $arrLine["cnab1"]                   = substr($line, 17,  1); //alfa - default brancos  //Uso Exclusivo da FEBRABAN/CNAB

        $arrLine["tipo_inscricao_empresa"]  = substr($line, 18,  1); //num - 1-CPF, 2-CGC //Tipo de Inscrição da Empresa
        $arrLine["num_inscricao_empresa"]   = substr($line, 19, 14); //numerico //Número de Inscrição da Empresa
        $arrLine["cod_convenio"]            = substr($line, 33, 20); //alfanumerico //Código do Convênio no Banco

        $arrLine["agencia"]       		    = substr($line, 53,  5); //numerico //Agência Mantenedora da Conta
        $arrLine["dv_agencia"]              = substr($line, 58 , 1); //alfanumerico //DV da Agência Mantenedora da Conta
        $arrLine["conta_corrente"] 	        = substr($line, 59, 12); //numerico
        $arrLine["dv_conta"] 				= substr($line, 71,  1); //alfanumerico
        $arrLine["dv_ag_conta"] 			= substr($line, 72,  1); //alfanumerico //Dígito Verificador da Ag/Conta
        $arrLine["nome_empresa"]			= substr($line, 73, 30); //alfanumerico
        $arrLine["mensagem1"]				= substr($line,103, 40); //alfanumerico

        $arrLine["logradouro_empresa"]		= substr($line,143, 30); //alfa //Logradouro da Empresa - Nome da Rua, Av, Pça, Etc
        $arrLine["numero_empresa"]			= substr($line,173,  5); //num //Número do endereço da empresa
        $arrLine["complemento_empresa"]		= substr($line,178, 15); //alfa //Complemento - Casa, Apto, Sala, Etc
        $arrLine["cidade_empresa"]			= substr($line,193, 20); //alfa //Cidade da Empresa
        $arrLine["cep_empresa"]				= substr($line,213,  5); //num //5 primeiros dígitos do CEP da Empresa
        $arrLine["complemento_cep_empresa"]	= substr($line,218,  3); //alfa //3 últimos dígitos do CEP da empresa
        $arrLine["estado"]					= substr($line,221,  2); //  alfa  //Sigla do Estado
        $arrLine["cnab"]					= substr($line,223,  8); // alfa - default brancos //Uso Exclusivo da FEBRABAN/CNAB
        $arrLine["ocorrencias"]				= substr($line,231, 10); //alfa //Código das Ocorrências p/ Retorno


        return $arrLine;
    }

    function runDetail($line,$segment) 
    {

        $arrLine = array();
        
        if ($segment == 'E') {
            // Segmento E - Extrato Bancário            
            
            //-- Controle --//
            $arrLine["banco"]                   = substr($line,   1,  3); // Num  Código no Banco da Compensação
            $arrLine["lote"]                    = substr($line,   4,  4); // Num  Lote de Serviço
            $arrLine["registro"]                = substr($line,   8,  1); // Num  default '3' //Tipo de Registro
            //-- Servico --//
            $arrLine["num_registro_lote"]       = substr($line,   9,  5); // Num  No Sequencial do Registro no Lote
            $arrLine["segmento"]                = substr($line,  14,  1); // Alfa default 'J' //Código de Segmento no Reg. Detalhe
            
            $arrLine["cnab"]                    = substr($line,  15,  3); // Alfa
            
            // -- Empresa -- //
            $arrLine["tipo_inscricao_empresa"]  = substr($line,  18,  1); // Num - 1-CPF, 2-CGC //Tipo de Inscrição da Empresa
            $arrLine["num_inscricao_empresa"]   = substr($line,  19, 14); // Num Número de Inscrição da Empresa
            $arrLine["cod_convenio"]            = substr($line,  33, 20); // Alfa Código do Convênio no Banco
            $arrLine["agencia"]       		    = substr($line,  53,  5); // Num Agência Mantenedora da Conta
            $arrLine["dv_agencia"]              = substr($line,  58,  1); // Alfa DV da Agência Mantenedora da Conta
            $arrLine["conta_corrente"] 			= substr($line,  59, 12); // Num
            $arrLine["dv_conta"] 				= substr($line,  71,  1); // Alfa
            $arrLine["dv_ag_conta"] 			= substr($line,  72,  1); // Alfa Dígito Verificador da Ag/Conta
            $arrLine["nome_empresa"]			= substr($line,  73, 30); // Alfa
          
            $arrLine["cnab_1"]                  = substr($line, 103,  6); //
            $arrLine["natureza"]                = substr($line, 109,  3); // Alfa  Natureza do Lancamento
            $arrLine["tipo_complemento"]        = substr($line, 112,  2); // Alfa  Tipo do Complemento Lancamento
            $arrLine["complemento"]             = substr($line, 114, 20); // Alfa  Complemento do Lancamento
            $arrLine["cpmf"]                    = substr($line, 134,  1); // Alfa  Identificacao de isencao do CPMF
            $arrLine["data_contabil"]           = substr($line, 135,  8); // Num   Data Contabil
            
            // -- Lancamento -- //
            $arrLine["data_lancamento"]         = substr($line, 143,  8); // Num   Data do Lancamento
            $arrLine["valor_lancamento"]        = substr($line, 151, 18); // Num, 2 casas decimais  Valor do Lancamento
            $arrLine["tipo_lancamento"]         = substr($line, 169,  1); // Alfa Tipo Lancamento [Debito ou Credito]
            $arrLine["categoria_lancamento"]    = substr($line, 170,  3); // Alfa Categoria do Lancamento
            $arrLine["codigo_historico"]        = substr($line, 173,  4); // Alfa Codigo Historico no Banco
            $arrLine["historico"]               = substr($line, 177, 25); // Alfa Descricao Historico
            $arrLine["num_documento"]           = substr($line, 202, 39); // Alfa Numero Documento/Complemento
            
        } else if ($segment == 'J') {
            //LIQUIDACAO_TITULOS_CARTEIRA_COBRANCA - SEGMENTO J (Pagamento de Títulos de Cobrança) REMESSA/RETORNO
            $arrLine["banco"]             = substr($line,   1,  3); //   Num //Código no Banco da Compensação     
            $arrLine["lote"]              = substr($line,   4,  4); //   Num //Lote de Serviço                    
            $arrLine["registro"]          = substr($line,   8,  1); //   Num  default '3' //Tipo de Registro                   
            $arrLine["num_registro_lote"] = substr($line,   9,  5); //   Num  //No Sequencial do Registro no Lote  
            $arrLine["segmento"]          = substr($line,  14,  1); //   Alfa  default 'J' //Código de Segmento no Reg. Detalhe 
            $arrLine["tipo_movimento"]    = substr($line,  15,  1); //   Num //Tipo de Movimento 
            $arrLine["cod_movimento"]     = substr($line,  16,  2); //   Num  //Código da Instrução p/ Movimento   
            $arrLine["cod_barras"]        = substr($line,  18, 44); //   Num           
            $arrLine["nome_cedente"]      = substr($line,  62, 30); //   Alfa          
            $arrLine["data_vencimento"]   = substr($line,  92,  8); //   Num  //Data do Vencimento (Nominal)       
            $arrLine["valor_titulo"]      = substr($line, 100, 13); //   Num, 2 casas decimais //Valor do Título (Nominal)          
            $arrLine["desconto"]          = substr($line, 115, 13); //   Num, 2 casas decimais //Valor do Desconto + Abatimento     
            $arrLine["acrescimos"]        = substr($line, 130, 13); //   Num, 2 casas decimais //Valor da Mora + Multa              
            $arrLine["data_pagamento"]    = substr($line, 145,  8); //   Num           
            $arrLine["valor_pagamento"]   = substr($line, 153, 13); //   Num, 2 casas decimais
            $arrLine["quantidade_moeda"]  = substr($line, 168, 10); //   Num, 5 casas decimais
            $arrLine["referencia_sacado"] = substr($line, 183, 20); //   Alfa //Num. do Documento Atribuído pela Empresa 
            $arrLine["nosso_numero"]      = substr($line, 203, 20); //   Alfa //Num. do Documento Atribuído pelo Banco
            $arrLine["cod_moeda"]         = substr($line, 223,  2); //   Num 
            $arrLine["cnab"]              = substr($line, 225,  6); //   Alfa - default Brancos //Uso Exclusivo FEBRABAN/CNAB
            $arrLine["ocorrencias"]       = substr($line, 231, 10); //   Alfa //Códigos das Ocorrências p/ Retorno


        }
        return $arrLine;
    }

    function runTrailerLot($line,$segment){
        
        $arrLine = array();
        if($segment == 'J') {
            $arrLine["banco"]                   = substr($line,  1,  3); //numerico  //Código do Banco na Compensação
            $arrLine["lote"]                    = substr($line,  4,  4); //numerico //Lote de Serviço
            $arrLine["registro"]                = substr($line,  8,  1); //num - default 5 //Tipo de Registro
            $arrLine["cnab1"]                   = substr($line,  9,  9); //alfa - default brancos Uso Exclusivo FEBRABAN/CNAB
            $arrLine["quant_regs"]              = substr($line, 18,  6); //numerico //Quantidade de Registros do Lote
            $arrLine["valor"]      		        = substr($line, 24, 16); //numerico, 2 casas decimais  //Somatória dos Valores
            $arrLine["quant_moedas"]            = substr($line, 42, 13); //numerico, 5 casas decimais  //Somatória de Quantidade de Moedas
            $arrLine["num_aviso_debito"]        = substr($line, 60,  6); //numerico //Número Aviso de Débito
            $arrLine["cnab2"]      		        = substr($line, 66,165); //alfa, default brancos //Uso Exclusivo FEBRABAN/CNAB
            $arrLine["ocorrencias"]             = substr($line,231, 10); //alfa  //Códigos das Ocorrências para Retorno
        } else if ($segment == 'E') {
            $arrLine = array();
            $arrLine["banco"]                   = substr($line,  1,  3); //numerico  //Código do Banco na Compensação
            $arrLine["lote"]                    = substr($line,  4,  4); //numerico //Lote de Serviço
            $arrLine["registro"]                = substr($line,  8,  1); //num - default 5 //Tipo de Registro
            $arrLine["cnab1"]                   = substr($line,  9,  9); //alfa - default brancos Uso Exclusivo FEBRABAN/CNAB

            $arrLine["tipo_inscricao_empresa"]  = substr($line, 18, 1); // Num - 1-CPF, 2-CGC //Tipo de Inscrição da Empresa
            $arrLine["num_inscricao_empresa"]   = substr($line, 19, 14); // Num Número de Inscrição da Empresa
            $arrLine["cod_convenio"]            = substr($line, 33, 20); // Alfa Código do Convênio no Banco
            $arrLine["agencia"]       		    = substr($line, 53,  5); // Num Agência Mantenedora da Conta
            $arrLine["dv_agencia"]              = substr($line, 58,  1); // Alfa DV da Agência Mantenedora da Conta
            $arrLine["conta_corrente"] 			= substr($line, 59, 12); // Num
            $arrLine["dv_conta"] 				= substr($line, 71,  1); // Alfa
            $arrLine["dv_ag_conta"] 			= substr($line, 72,  1); // Alfa Dígito Verificador da Ag/Conta
            $arrLine["cnab_2"]			        = substr($line, 73, 16); // Alfa

            $arrLine["vlr_bloqueado_24"]   		= substr($line, 89, 18); // Num, 2 casas decimais  Saldo bloqueado acima de 24 horas
            $arrLine["limite"]      		    = substr($line,107, 18); // Num, 2 casas decimais  Limite da conta
            $arrLine["vlr_bloqueado"]      		= substr($line,125, 18); // Num, 2 casas decimais  Saldo bloqueado até de 24 horas

            $arrLine["data_saldo"]              = substr($line,143,  8); //   Num Data do saldo final
            $arrLine["vlr_saldo"]      		    = substr($line,151, 18); // Num, 2 casas decimais  Valor do saldo final
            $arrLine["situacao"]			    = substr($line,169,  1); // Alfa Situacao do saldo final
            $arrLine["status"]			        = substr($line,170,  1); // Alfa Posicao do saldo final

            $arrLine["qtd_registros"]			= substr($line,171,  6); // Num Quantidade de registros do lote
            $arrLine["vlr_debitos"]      		= substr($line,177, 18); // Num, 2 casas decimais  Somatório dos valores a débito
            $arrLine["vlr_creditos"]      		= substr($line,195, 18); // Num, 2 casas decimais  Somatório dos valores a crédito

            $arrLine["cnab_3"]			        = substr($line,213, 28); // Alfa
        }
        return $arrLine;
    }

    function runTrailerFile($line)
    {
        $arrLine = array();
        $arrLine["banco"]               = substr($line,  1,  3); //numerico  //Código do Banco na Compensação
        $arrLine["lote"]                = substr($line,  4,  4); // num - default 9999  //Lote de Serviço
        $arrLine["registro"]            = substr($line,  8,  1); //num - default 9   //Tipo de Registro
        $arrLine["cnab1"]               = substr($line,  9,  9); //alpha - default brancos //Uso Exclusivo FEBRABAN/CNAB
        $arrLine["quant_lotes"]         = substr($line, 18,  6); //num. //Quantidade de Lotes do Arquivo
        $arrLine["quant_regs"]          = substr($line, 24,  6); //num. //Quantidade de Registros do Arquivo
        $arrLine["quant_contas_conc"]   = substr($line, 30,  6); //num. //Qtde de Contas p/ Conc. (Lotes)
        $arrLine["cnab2"]     		    = substr($line, 36,205); //alpha - default brancos  //Uso Exclusivo FEBRABAN/CNAB
        return $arrLine;
        
        
    }    
}
?>