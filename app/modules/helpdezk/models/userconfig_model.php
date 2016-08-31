<?php
class userconfig_model extends Model{    	
		
    public function checkConf($idperson){
        $ret = $this->db->Execute("SELECT idconfiguser FROM hdk_tbconfig_user WHERE idperson = $idperson");
		if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
            return false;
        }		
		$id = $ret->fields['idconfiguser'];
		if(!$id){
			$id = $this->insertConf($idperson);
		}
		return $id;		
    }
	
	public function insertConf($idperson){
		$ret = $this->db->Execute("INSERT INTO hdk_tbconfig_user (idperson) VALUES ($idperson)");
		if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
            return false;
        }
		return $this->db->Insert_ID();
	}
	
	public function setConfigValue($id, $value, $field ){
		$ret = $this->db->Execute("UPDATE hdk_tbconfig_user SET $field = '$value' WHERE idconfiguser = $id ");
		if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
            return false;
        }
		return $ret;
	}
	
}