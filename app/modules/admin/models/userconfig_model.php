<?php


if(class_exists('Model')) {
	class DynamicUserconfig_model extends Model {}
} elseif(class_exists('cronModel')) {
	class DynamicUserconfig_model extends cronModel {}
} elseif(class_exists('apiModel')) {
	class DynamicUserconfig_model extends apiModel {}
}



class userconfig_model extends DynamicUserconfig_model
{

	public function existApiConfigTables()
	{

		if ($this->tableExists('hdk_tbexternalfield') == 0 or
			$this->tableExists('hdk_tbexternallapp') == 0 or
			$this->tableExists('hdk_tbexternalsettings') == 0
		) {
			return false;
		} else {
			return true;
		}

	}

	public function getExternalSettings($idPerson){
		$sql = "SELECT
				  a.idexternalapp,
				  b.idperson,
				  c.fieldname,
				  c.value
				FROM
				  hdk_tbexternallapp a,
				  hdk_tbexternalsettings b,
				  hdk_tbexternalfield c
				WHERE a.idexternalapp = b.idexternalapp
				  AND c.idexternalsettings = b.idexternalsetting
				  AND b.idperson = $idPerson ";

		$rs = $this->db->Execute($sql);

		if ($this->db->ErrorNo() != 0) {
			return array('success' => false, 'message' => $this->db->ErrorMsg(), 'id' => '');
		}

		return array('success' => true, 'message' => '', 'id' => $rs);


	}

	public function insertExternalSettings($idExternalApp,$idperson)
	{

		$rs = $this->db->Execute("SELECT idexternalsetting  FROM  hdk_tbexternalsettings WHERE idperson = $idperson AND idexternalapp = $idExternalApp") ;

		if ($rs->recordCount() == 0) {

			$sql = "INSERT INTO hdk_tbexternalsettings (idexternalapp,idperson) VALUES ($idExternalApp,$idperson)";

			$this->db->Execute($sql) ;

			if ($this->db->ErrorNo() != 0) {
				//$this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);

				return array('success' => false, 'message' => $this->db->ErrorMsg(), 'id' => '');
			}

			return array('success' => true, 'message' => '', 'id' => $this->db->Insert_ID());

		} else {

			return array('success' => true, 'message' => 'Record exists', 'id' => $rs->fields['idexternalsetting']);

		}


	}

	public function insertExternalField($idExternalsettings,$fieldName,$value)
	{
		$sql = "SELECT
				  idexternalfield
				FROM
				  hdk_tbexternalfield
				WHERE idexternalsettings = $idExternalsettings
				  AND fieldname = '$fieldName' ";

		$rs = $this->db->Execute($sql);

		if ($rs->recordCount() == 0) {

			$sql = "INSERT INTO hdk_tbexternalfield (idexternalsettings, fieldname, `value`) VALUES ($idExternalsettings, '$fieldName', '$value')";

			$this->db->Execute($sql);

			if ($this->db->ErrorNo() != 0) {
				//$this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
				return array('success' => false, 'message' => 'insert: ' .$this->db->ErrorMsg(), 'id' => '');
			}

		} else {

			$sql = "UPDATE hdk_tbexternalfield SET `value` = '$value' WHERE idexternalsettings = '$idExternalsettings' AND fieldname = '$fieldName';";

			$this->db->Execute($sql);

			if ($this->db->ErrorNo() != 0) {
				//$this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
				return array('success' => false, 'message' => 'update: ' . $this->db->ErrorMsg(), 'id' => '');
			}

		}

		return array('success' => true, 'message' => $this->db->ErrorMsg(), 'id' => $this->db->Insert_ID());

	}

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
		if($this->isMysql($database)) {
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