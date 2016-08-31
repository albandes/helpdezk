<?php
class relOperator_model extends Model{
    public function getSummarizedOperator($idperson, $data_hour = NULL, $data_request = NULL){
		// changed 30/09/2014 09:27 Rafael Stoever
        $sel = $this->select("
        	SELECT
        	(SELECT name FROM hdk_tbdepartment_has_person a, hdk_tbdepartment b WHERE a.idperson = $idperson AND a.iddepartment = b.iddepartment) as department,
        	(SELECT tbperson.name FROM hdk_tbdepartment_has_person, hdk_tbdepartment, tbperson WHERE hdk_tbdepartment_has_person.idperson = $idperson AND hdk_tbdepartment_has_person.iddepartment = hdk_tbdepartment.iddepartment AND tbperson.idperson = hdk_tbdepartment.idperson) as company,
			(SELECT SUM(minutes) FROM tbperson usu, hdk_tbnote apont, hdk_tbrequest sol WHERE usu.idperson = apont.idperson and apont.code_request = sol.code_request AND sol.idsource = 2 AND usu.idperson = $idperson $data_hour AND apont.minutes > 0) AS TOTAL_TELEPHONE,
			(SELECT SUM(minutes) FROM tbperson usu, hdk_tbnote apont WHERE usu.idperson = apont.idperson AND usu.idperson = $idperson $data_hour AND apont.hour_type = 1 AND apont.minutes > 0) AS TOTAL_NORMAL,
			(SELECT SUM(minutes) FROM tbperson usu, hdk_tbnote apont WHERE usu.idperson = apont.idperson AND usu.idperson = $idperson $data_hour AND apont.hour_type = 2 AND apont.minutes > 0) AS TOTAL_EXTRA, 
			(SELECT COUNT(distinct solicitacao.code_request) FROM hdk_tbrequest solicitacao, hdk_tbrequest_in_charge solgrupo WHERE solicitacao.code_request = solgrupo.code_request AND solicitacao.idstatus = 1 AND solgrupo.id_in_charge = $idperson $data_request AND solgrupo.ind_in_charge = 1) AS NEW,
			(SELECT COUNT(distinct solicitacao.code_request) FROM hdk_tbrequest solicitacao, hdk_tbrequest_in_charge solgrupo WHERE solicitacao.code_request = solgrupo.code_request AND solicitacao.idstatus = 2 AND solgrupo.id_in_charge = $idperson $data_request AND solgrupo.ind_in_charge = 1) AS REPASSED,
			(SELECT COUNT(distinct solicitacao.code_request) FROM hdk_tbrequest solicitacao, hdk_tbrequest_in_charge solgrupo WHERE solicitacao.code_request = solgrupo.code_request AND solicitacao.idstatus = 3 AND solgrupo.id_in_charge = $idperson $data_request AND solgrupo.ind_in_charge = 1) AS ON_ATTENDANCE,
			(SELECT COUNT(distinct solicitacao.code_request) FROM hdk_tbrequest solicitacao, hdk_tbrequest_in_charge solgrupo WHERE solicitacao.code_request = solgrupo.code_request AND solicitacao.idstatus in ( 4, 5 ) AND solgrupo.id_in_charge = $idperson $data_request AND solgrupo.ind_in_charge = 1) AS FINISH
			FROM DUAL
        ");

        return $sel;
    }
}
?>
