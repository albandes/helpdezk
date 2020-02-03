<?php

class tms_estoquequestoes_model extends Model
{

    public function getUsuarioPorAssunto($data,$limit)
    {
        /*
            $sql =  "
                    SELECT
                       a.idperson,
                       b.name,
                       SUM(a.total) AS total
                    FROM
                       tms_tbestquestao a,
                       tbperson b
                    WHERE a.data >= $data
                       AND a.idperson = b.idperson
                       GROUP BY a.idperson
                       ORDER BY total DESC
                       LIMIT $limit
                    ";
        */
        $sql =  "
                SELECT
                   a.idperson,
                   b.name,
                   COUNT(a.idquestao) AS total
                FROM
                   tms_tbquestao a,
                   tbperson b
                WHERE a.dtcadastro >= '$data'
                   AND a.idperson = b.idperson
                   GROUP BY a.idperson
                   ORDER BY total DESC
                   LIMIT $limit
                ";
        return $this->select($sql);
    }

    public function getQuestoesPorDia($idperson,$data)
    {
        $sql = "
                SELECT
                   a.dtcadastro,
                   COUNT(a.idquestao) AS total
                FROM
                   tms_tbquestao a,
                   tbperson b
                WHERE a.idperson = $idperson
                   AND a.dtcadastro >= $data
                   AND a.idperson = b.idperson
                GROUP BY DATE(a.dtcadastro)
                ORDER BY DATE(a.dtcadastro) ASC
               ";
        return $this->select($sql);
    }

    public function getUserByRequests($year,$limit) {
		$sql = 	"
				select
				   a.idperson_creator,
				   b.name,
				   count(a.idperson_creator) as total
				from hdk_tbrequest a,
				   tbperson b
				where year(a.entry_date) >= $year
					 and a.idperson_creator = b.idperson
				group by idperson_creator
				order by total desc
				limit $limit
				";

		return $this->select($sql);
    }

	public function getRequestsByDay($idperson,$year) {
		$sql = "
				select
				   date(a.entry_date) as entry_date,
				   count(a.idrequest) as total
				from hdk_tbrequest a,
				   tbperson b
				where year(a.entry_date) >= $year
					 and a.idperson_creator = $idperson
					 and a.idperson_creator = b.idperson
				group by date(a.entry_date)
				order by date(a.entry_date)ASC			
				";
			return $this->select($sql);
	}

}

?>
