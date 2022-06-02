<?php

class hdk_outoftime_model extends Model 
{
    public function getTotalRequests($where) {
		$sql = 	"
				select
				  count(idrequest) as total 
				from hdk_tbrequest
				". $where ."
				";
		die($sql);
    }

}

?>
