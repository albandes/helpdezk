<?php

class hdk_sla_model extends Model 
{
    public function getSla($where) 
	{
	
		$sql = 	"
				select
				   intime,
				   outoftime,
				   datetimeupdate
				from dsh_tbsla
				";
				
		return $this->select($sql);
		
    }

}

?>
