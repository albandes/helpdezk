<?php
/**
 * Created by PhpStorm.
 * User: Rogério
 * Date: 25/05/15
 * Time: 17:28
 */

class ReturnCnab400Sicredi {
    /**
     * @property int HEADER_ARQUIVO Define o valor que identifica uma coluna do tipo HEADER DE ARQUIVO
     */
    const HEADER_ARQUIVO = 0;
    /**
     * @property int DETALHE Define o valor que identifica uma coluna do tipo DETALHE
     */
    const DETAIL = 1;
    /**
     * @property int TRAILER_ARQUIVO Define o valor que identifica uma coluna do tipo TRAILER DE ARQUIVO
     */
    const TRAILER_ARQUIVO = 9;

    /**
     * Processa a linha header do arquivo
     *
     * @param string $linha Linha do header de arquivo processado
     * @return array<mixed> Retorna um vetor contendo os dados dos campos do header do arquivo.
     */
    function processarHeaderArquivo($linha)
    {
        $vlinha = array();
        $vlinha["registro"]         = substr($linha,  1,  1); //9 Identificação do Registro Header: “0”
        $vlinha["tipo_operacao"]    = substr($linha,  2,  1); //9 Tipo de Operação: “2”
        $vlinha["id_tipo_operacao"] = substr($linha,  3,  7); //X Identificação Tipo de Operação “RETORNO”
        $vlinha["id_tipo_servico"]  = substr($linha, 10,  2); //9 Identificação do Tipo de Serviço: “01”
        $vlinha["tipo_servico"]     = substr($linha, 12, 15); //X Identificação por Extenso do Tipo de Serviço: “COBRANCA”
        $vlinha["codigo_cedente"]   = substr($linha, 27,  5); //9 Código do cedente
        $vlinha["cgc_cedente"]      = substr($linha, 32, 14); //9 CGC do cedente
        $vlinha["filler"]           = substr($linha, 46, 31); //X Brancos
        $vlinha["num_sicredi"]      = substr($linha, 77,  3); //9 Número do Sicredi = 748
        $vlinha["nom_sicredi"]      = substr($linha, 80, 15); //X BANSICREDI
        $vlinha["data_gravacao"]    = substr($linha, 95,  8); //X AAAAMMDD
        $vlinha["filler_1"]         = substr($linha,103,  8); //X Brancos
        $vlinha["num_retorno"]      = substr($linha,111,  7); //9 Número do Retorno
        $vlinha["filler_2"]         = substr($linha,118,272); //X Brancos
        $vlinha["versao"]           = substr($linha,390,  5); //X 99.99
        $vlinha["sequencial_reg"]   = substr($linha,395,  6); //9 Seqüencial do Registro: ”000001”
        return $vlinha;
    }

    /**
     * Processa uma linha detalhe do arquivo.
     *
     * @param string $linha Linha detalhe do arquivo processado
     * @return array<mixed> Retorna um vetor contendo os dados dos campos da linha detalhe.
     */
    function processarDetalhe($linha)
    {
        $vlinha = array();

        $vlinha["registro"]     = substr($linha,  1,  1); //9  Id do Registro Detalhe: 1 p/ convênios de 6 dígitos e 7 para convênios de 7 dígitos
        $vlinha["filler"]       = substr($linha,  2, 12); //X Brancos
        $vlinha["tp_cobranca"]  = substr($linha, 14,  1); //X  A = Com Registro , C = Sem Registro

        if ($vlinha["tp_cobranca"] == "C") {
            $vlinha["cod_sac_coop"]         = substr($linha, 15,  5); //9  Código do sacado na cooperativa cedente
            $vlinha["cod_sca_cli"]          = substr($linha, 20,  5); //X  Código do sacado junto ao cliente
            $vlinha["filler_1"]             = substr($linha, 25, 23); //X  Brancos
            $vlinha["nosso_numero"]         = substr($linha, 48,  9); //9  Nosso Número Sicredi sem edição
            $vlinha["filler_2"]             = substr($linha, 57, 52); //X  Brancos
            $vlinha["ocorrencia"]           = substr($linha,109,  2); //9  Código da ocorrência
            $vlinha["data_ocorrencia"]      = substr($linha,111,  6); //9  Data da ocorrência DDMMAA
            $vlinha["seu_numero"]           = substr($linha,117, 10); //9  Seu número
            $vlinha["filler_3"]             = substr($linha,127, 26); //X  Brancos
            $vlinha["valor"]                = substr($linha,153, 13); //9  v99 Valor do título
            $vlinha["filler_4"]             = substr($linha,166, 62); //X  Brancos
            $vlinha["valor_abatimento"]     = substr($linha,228, 13); //9  v99 Valor do abatimento
            $vlinha["desconto_concedido"]   = substr($linha,241, 13); //9  v99 Desconto concedido
            $vlinha["valor_recebido"]       = substr($linha,254, 13); //9  v99 Valor recebido (valor recebido parcial)
            $vlinha["juros_mora"]           = substr($linha,267, 13); //9  v99 Juros de mora
            $vlinha["multa"]                = substr($linha,280, 13); //9  v99 Outros recebimentos
            $vlinha["filler_5"]             = substr($linha,293, 26); //X  Brancos
            $vlinha["motivo_ocorrencia"]    = substr($linha,319,  2); //X  Brancos
            $vlinha["filler_6"]             = substr($linha,321,  8); //X  Brancos
            $vlinha["data_credito"]         = substr($linha,329,  8); //9  Data do crédito (AAAAMMDD)
            $vlinha["filler_7"]             = substr($linha,337, 58); //X  Brancos
        }

        $vlinha["sequencial"]               = substr($linha, 395, 006); //9 Seqüencial do registro

        return $vlinha;
    }

    /**
     * Processa a linha trailer do arquivo.
     *
     * @param string $linha Linha trailer do arquivo processado
     * @return array<mixed> Retorna um vetor contendo os dados dos campos da linha trailer do arquivo.
     */
    function processarTrailerArquivo($linha)
    {
        $vlinha = array();

        $vlinha["registro"]                 = substr($linha,  1,  1);  //9  Identificação do Registro Trailer: “9”
        $vlinha["retorno"]                  = substr($linha,  2,  1);  //9  “2”
        $vlinha["cod_banco"]                = substr($linha,  3,  3);  //9  “748”
        $vlinha["cod_cedente"]              = substr($linha,  6,  5); //9
        $vlinha["filler"]                   = substr($linha, 11,384); //X  Brancos
        $vlinha["sequencial"]               = substr($linha,395,  6);  //9  Seqüencial do registro

        return $vlinha;
    }

    /**
     * Processa uma linha do arquivo de retorno.
     *
     * @param int $numLn Número_linha a ser processada
     * @param string $linha String contendo a linha a ser processada
     * @return array Retorna um vetor associativo contendo os valores_linha processada.
     */
    public function runLine($numLn, $linha)
    {
        $tamLinha = 400; //total de caracteres das linhas do arquivo
        //o +2 é utilizado para contar o \r\n no final da linha
        if (strlen($linha) != $tamLinha and strlen($linha) != $tamLinha + 2)
            die("A linha $numLn não tem $tamLinha posições. Possui " . strlen($linha));
        if (trim($linha) == "")
            die("A linha $numLn está vazia.");

        // Adicionado um espaço vazio no início_linha para poder trabalhar com índices iniciando_1, no lugar_zero,
        // ficando igual ao manual CNAB400
        $linha = " $linha";
        $tipoLn = substr($linha, 1, 1);
        if ($tipoLn == ReturnCnab400Sicredi::HEADER_ARQUIVO) {
            $vlinha = $this->processarHeaderArquivo($linha);
        } else if ($tipoLn == ReturnCnab400Sicredi::DETAIL) {
            $vlinha = $this->processarDetalhe($linha);
        } else if ($tipoLn == ReturnCnab400Sicredi::TRAILER_ARQUIVO) {
            $vlinha = $this->processarTrailerArquivo($linha);
        } else {
            $vlinha = NULL;
        }

        return $vlinha;
    }
} 