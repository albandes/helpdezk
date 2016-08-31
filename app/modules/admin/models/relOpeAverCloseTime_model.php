<?php
class relOpeAverCloseTime_model extends Model{
	
	
	function getResponseTime($date_interval, $condition)
	{
		$sql = 	"
				select
				  d.name,
				   (SELECT
				     tbperson.name
				   FROM hdk_tbdepartment_has_person,
				     hdk_tbdepartment,
				     tbperson
				   WHERE hdk_tbdepartment_has_person.idperson = d.idperson
				       AND hdk_tbdepartment_has_person.iddepartment = hdk_tbdepartment.iddepartment
				       AND tbperson.idperson = hdk_tbdepartment.idperson) as company,
				  min(b.MIN_CLOSURE_TIME) as min_time,
				  round(avg(b.MIN_CLOSURE_TIME),2) as avg_time,
				  max(b.MIN_CLOSURE_TIME) as max_time
				from hdk_tbrequest a,
				  hdk_tbrequest_times b,
				  hdk_tbrequest_in_charge c,
				  tbperson d
				where c.type = 'P'
					$date_interval
					and b.CODE_REQUEST = a.code_request
					and c.code_request = b.CODE_REQUEST
					and d.idperson = c.id_in_charge
					and c.ind_in_charge = 1
					and b.MIN_OPENING_TIME > 0
					$condition
				group by c.id_in_charge		
				order by d.name asc
				";
				
		
        $ret = $this->select($sql);
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
		
        return $ret;
    }
}
	
	  