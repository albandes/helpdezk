<?php
class relRequestEvaluate_model extends Model{
    public function getRequestsEvaluate($where = NULL, $obs){
        return $this->select("
        	
        	select
			  req.code_request,
			  pers_ope.name AS operator,
			  pers.name AS user,
			  (SELECT tbperson.name FROM hdk_tbdepartment_has_person,hdk_tbdepartment,tbperson WHERE hdk_tbdepartment_has_person.idperson = pers.idperson AND hdk_tbdepartment_has_person.iddepartment = hdk_tbdepartment.iddepartment AND tbperson.idperson = hdk_tbdepartment.idperson) as company,
			  req.subject,
			  DATE_FORMAT(req.entry_date, '%d/%m/%Y') as date,
			  (SELECT GROUP_CONCAT(tbeva.name) FROM hdk_tbrequest_evaluation eva, hdk_tbevaluation tbeva where eva.code_request = req.code_request AND eva.idevaluation = tbeva.idevaluation) as evaluation,
			  (SELECT count(*) FROM hdk_tbnote WHERE description LIKE '%<strong>$obs:</strong>%' AND hdk_tbnote.code_request = req.code_request) AS obs
			from hdk_tbrequest as req,
			  tbperson AS pers,
			  hdk_tbrequest_evaluation reqeva,
			  hdk_tbrequest_in_charge req_charge,
			  tbperson AS pers_ope
			where req.idperson_owner = pers.idperson
			AND req_charge.code_request = req.code_request
			AND pers_ope.idperson = req_charge.id_in_charge
			AND req_charge.ind_in_charge = 1
			AND reqeva.code_request = req.code_request
			$where
			GROUP BY req.code_request
			ORDER BY req.entry_date DESC
        	
        ");
    }
    
    public function getObsEvaluate($code, $obsLang){
        return $this->select("SELECT description, entry_date FROM hdk_tbnote WHERE description LIKE '%<strong>".$obsLang.":</strong>%' AND hdk_tbnote.code_request = '$code'");
    }
}
?>
