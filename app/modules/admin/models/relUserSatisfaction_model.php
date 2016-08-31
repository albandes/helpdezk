<?php
class relUserSatisfaction_model extends Model{
    public function getUserSatisfaction($where = NULL){
        $sel = $this->select("
        	
        	select eva.idevaluation, eva.name, count(distinct req.code_request) as total
			FROM 	hdk_tbevaluation eva, 
					hdk_tbrequest_evaluation req_eva, 
					hdk_tbrequest req, 
					hdk_tbrequest_in_charge req_charge
			WHERE 	eva.idevaluation = req_eva.idevaluation
			AND 	req.code_request = req_eva.code_request
			AND 	req_charge.code_request = req.code_request
			
			$where			
			
			GROUP BY eva.name
			ORDER BY eva.idquestion ASC, total DESC 
        	
        ");
        return $sel;
    }
}
?>
