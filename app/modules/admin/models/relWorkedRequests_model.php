<?php
class relWorkedRequests_model extends Model{
    
	
	function getReport($date_format, $date = null, $where = null){
       
		$ret = $this->select("	SELECT 
									a.code_request, 
									a.subject,
									DATE_FORMAT(l.date, '$date_format') as date, 
									s.name as status, 
									p.name 
								FROM 
									hdk_tbrequest a, 
									hdk_tbrequest_log l, 
									hdk_tbstatus s, 
									tbperson p
								WHERE a.code_request = l.cod_request
								AND l.idstatus = s.idstatus
								AND l.idperson = p.idperson
								AND p.idtypeperson IN (1,3)
								$where
								$date
								ORDER BY p.name, l.date DESC");


        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }
    
    
}
	
	  