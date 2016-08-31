<?php
class relRequestsAddInfo_model extends Model{
    	
    public function getSummarizedReq($date_format, $where = null){
        $sel = $this->select("
						        SELECT
								  sol.code_request,
								  p.name           as person,
								  DATE_FORMAT(sol.entry_date, '$date_format') as date,
								  b.name           as addinfo
								FROM hdk_tbrequest_addinfo a,
								  hdk_tbaddinfo b,
								  hdk_tbrequest sol,
								  tbperson p
								WHERE a.idaddinfo = b.idaddinfo
								    AND p.idperson = sol.idperson_owner
								    AND sol.code_request = a.code_request
								    $where
								ORDER BY sol.entry_date DESC
						     ");
        return $sel;
    }
}


