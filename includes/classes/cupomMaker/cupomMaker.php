<?php

/**
 * Created by PhpStorm.
 * User: valentin.acosta
 * Date: 11/10/2018
 * Time: 14:45
 */
class cupomMaker
{
    var $n_colunas;

    /**
     * Adiciona a quantidade necessaria de espaços no inicio
     * da string informada para deixa-la centralizada na tela
     *
     * @global int $n_colunas Numero maximo de caracteres aceitos
     * @param string $info String a ser centralizada
     * @return string
     */
    function centraliza($info)
    {
        $this->n_colunas = 40; // 40 colunas por linha;

        $aux = strlen($info);

        if ($aux < $this->n_colunas) {
            // calcula quantos espaços devem ser adicionados
            // antes da string para deixa-la centralizada
            $espacos = floor(($this->n_colunas - $aux) / 2);

            $espaco = '';
            for ($i = 0; $i < $espacos; $i++){
                $espaco .= ' ';
            }

            // retorna a string com os espaços necessários para centraliza-la
            return $espaco.$info;

        } else {
            // se for maior ou igual ao número de colunas
            // retorna a string cortada com o número máximo de colunas.
            return substr($info, 0, $this->n_colunas);
        }

    }

    /**
     * Adiciona a quantidade de espaços informados na String
     * passada na possição informada.
     *
     * Se a string informada for maior que a quantidade de posições
     * informada, então corta a string para ela ter a quantidade
     * de caracteres exata das posições.
     *
     * @param string $string String a ter os espaços adicionados.
     * @param int $posicoes Qtde de posições da coluna
     * @param string $onde Onde será adicionar os espaços. I (inicio) ou F (final).
     * @return string
     */
    function addEspacos($string, $posicoes, $onde)
    {

        $aux = strlen($string);

        if ($aux >= $posicoes)
            return substr ($string, 0, $posicoes);

        $dif = $posicoes - $aux;

        $espacos = '';

        for($i = 0; $i < $dif; $i++) {
            $espacos .= ' ';
        }

        if ($onde === 'I')
            return $espacos.$string;
        else
            return $string.$espacos;

    }

}