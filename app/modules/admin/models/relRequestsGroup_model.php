<?php
class relRequestsGroup_model extends Model{
    function getService($code_request){
        $ret = $this->select("Select idservice from hdk_tbquest where cod_request='".$code_request."'");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }
	
	
	function getReport($date_format, $date = null, $where = null){
       // $ret = $this->select("select req.code_request, pers.name, (SELECT tbperson.name FROM hdk_tbdepartment_has_person, hdk_tbdepartment, tbperson WHERE hdk_tbdepartment_has_person.idperson = pers.idperson AND hdk_tbdepartment_has_person.iddepartment = hdk_tbdepartment.iddepartment AND tbperson.idperson = hdk_tbdepartment.idperson) as company, req.subject, DATE_FORMAT(req.entry_date, '$date_format') as date, pr.name as priority, st.name as status from hdk_tbrequest as req, hdk_tbpriority as pr, hdk_tbstatus as st, tbperson AS pers where pr.idpriority = req.idpriority and st.idstatus = req.idstatus $where AND req.idperson_owner = pers.idperson $date ORDER BY req.code_request ASC");
		
		$ret = $this->select("	select
								  req.code_request,
								  pers.name,
								  (SELECT
								     tbperson.name
								   FROM hdk_tbdepartment_has_person,
								     hdk_tbdepartment,
								     tbperson
								   WHERE hdk_tbdepartment_has_person.idperson = pers.idperson
								       AND hdk_tbdepartment_has_person.iddepartment = hdk_tbdepartment.iddepartment
								       AND tbperson.idperson = hdk_tbdepartment.idperson) as company,
								  req.subject,
								  DATE_FORMAT(req.entry_date, '$date_format') as date,
								  pr.name          as priority,
								  st.name          as status,
								  st.idstatus_source,
								  st.idstatus,
								  ope.name AS operator,
								  (SELECT count(ind_repass) FROM hdk_tbrequest_in_charge WHERE code_request = req.code_request AND ind_repass = 'Y') as repass
								from hdk_tbrequest as req,
								  hdk_tbpriority as pr,
								  hdk_tbstatus as st,
								  tbperson AS pers,
								  tbperson AS ope,
								  hdk_tbrequest_in_charge AS inc
								where pr.idpriority = req.idpriority
								    AND st.idstatus = req.idstatus
								    $where
								    AND req.idperson_owner = pers.idperson
								    AND inc.code_request = req.code_request
								    AND inc.ind_in_charge = 1
								    AND ope.idperson = inc.id_in_charge
								    AND ope.idtypeperson IN (6)
									$date
								ORDER BY operator, req.code_request ASC");


        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }
    
    function getRepassVal($cod_request){
        $ret = $this->select("SELECT ind_repass FROM hdk_tbrequest_in_charge WHERE code_request = $cod_request AND ind_repass = 'Y'");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }
}
	
	  