<?php
class relSumAddInfo_model extends Model{
    	
    public function getSummarizedReq($where = null){
        $sel = $this->select("
						        SELECT COUNT(a.idaddinfo) AS total, b.name
								FROM hdk_tbrequest_addinfo a,
								  hdk_tbaddinfo b,
								 hdk_tbrequest sol
								WHERE a.idaddinfo = b.idaddinfo
								AND sol.code_request = a.code_request
								$where
								GROUP BY a.idaddinfo
						     ");
        return $sel;
    }
}


