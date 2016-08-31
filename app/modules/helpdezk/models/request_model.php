<?php
class request_model extends Model{
	public function getRequestInfo($code){
        return $this->db->Execute("SELECT subject, c.name
									FROM hdk_tbrequest a, hdk_tbrequest_in_charge b, tbperson c
									WHERE a.code_request = '$code'
									AND b.code_request = a.code_request
									AND b.ind_in_charge = 1
									AND c.idperson = b.id_in_charge");
    }
	
	
	public function getRequestUser($code){
        return $this->db->Execute("SELECT idperson_owner FROM hdk_tbrequest WHERE code_request = '$code'");
    }
	
}
?>