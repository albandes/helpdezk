<?php

class hdk_harduser_model extends Model 
{
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
