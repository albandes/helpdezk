<?php

class tms_estoqueassuntos_model extends Model
{

    public function getAssuntosPorUsuario($data,$idperson,$limit)
    {
        $sql =  "
                SELECT
                  a.idsubassunto,
                  b.nome,
                  COUNT(a.idquestao) AS total
                FROM
                  tms_tbquestao a,
                  tms_tbsubassunto b
                WHERE a.dtcadastro >= '$data'
                  AND a.idperson = $idperson
                  AND a.idsubassunto = b.idsubassunto
                GROUP BY a.idsubassunto
                ORDER BY total DESC
                LIMIT $limit
                ";
        return $this->select($sql);
    }

    public function getQuestoesPorDia($idperson,$idsubassunto,$data)
    {
        $sql = "
                SELECT
                   a.dtcadastro,
                   COUNT(a.idquestao) AS total
                FROM
                   tms_tbquestao a,
                   tbperson b
                WHERE a.idperson = $idperson
                   AND a.dtcadastro >= '$data'
                   AND a.idsubassunto = $idsubassunto
                   AND a.idperson = b.idperson
                GROUP BY DATE(a.dtcadastro)
                ORDER BY DATE(a.dtcadastro) ASC
               ";
        return $this->select($sql);
    }




}

?>
