<?php
class evaluation_model extends Model{
    public function selectEvaluation($where, $order, $limit){
        return $this->db->Execute("Select idevaluation, name, icon_name, status from hdk_tbevaluation $where $order $limit");
    }
    public function countEvaluation($where, $order, $limit){
        return $this->db->Execute("select count(idevaluation) as total from hdk_tbevaluation $where $order $limit");
    }
    public function selectQuestion(){
        return $this->db->Execute("select idquestion, question from hdk_tbevaluationquestion");
    }
    public function insertEvaluation($idquest, $name, $icon){
        return $this->db->Execute("insert into hdk_tbevaluation (idquestion, name, icon_name) values ($idquest,'$name','$icon')");
    }
	
	public function checkToken($token){
		return $this->db->Execute("SELECT code_request FROM hdk_tbevaluation_token WHERE token = '$token'");
	}
	
	public function getToken($code){
		return $this->db->Execute("SELECT token FROM hdk_tbevaluation_token WHERE code_request = '$code'");
	}
	
	public function removeTokenByToken($token){
		return $this->db->Execute("DELETE FROM hdk_tbevaluation_token WHERE token = '$token'");
	}
	
	public function removeTokenByCode($code){
		return $this->db->Execute("DELETE FROM hdk_tbevaluation_token WHERE code_request = '$code'");
	}
	
	public function insertToken($code){
		$token = sha1(time().$code);
		return $this->db->Execute("INSERT INTO hdk_tbevaluation_token (code_request,token) values ('$code', '$token')");
	}
	
}
?>
