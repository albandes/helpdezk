<?php

class tms_mediaturmas_model extends Model
{
    public function getExpiraSolicitacao($data) {
		$sql = 	"
				select
				   count(idrequest) as valor
				from hdk_tbrequest
				where date(expire_date) = '$data'
				";
		return $this->select($sql);
    }

}

?>
