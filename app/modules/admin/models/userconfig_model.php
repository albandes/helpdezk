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
	
	public function getConf(array $data, $id = null){ 
		$where = ($id != null ? "idconfiguser = $id" : null);
		$ret = $this->read($data, 'hdk_tbconfig_user', $where);
		
		if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
            return false;
        }
		return $ret;
	}
	
	public function getColumns(){
		$database = $this->getConfig('db_connect');
		if($database == 'mysqlt') {	
			$ret = $this->db->Execute("SHOW COLUMNS FROM hdk_tbconfig_user");
		} elseif($database == 'oci8po') {
                $ret = $this->db->Execute('SELECT COLUMN_NAME FROM all_tab_columns WHERE table_name = \'HDK_TBCONFIG_USER\'');
		}	
		
		if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
            return false;
        }
		return $ret;
	}
    
	
}