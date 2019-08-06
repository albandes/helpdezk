<?php
if(class_exists('Model')) {
    class DynamicFeatures_model extends Model {}
} elseif(class_exists('cronModel')) {
    class DynamicFeatures_model extends cronModel {}
} elseif(class_exists('apiModel')) {
    class DynamicFeatures_model extends apiModel {}
}

class features_model extends DynamicFeatures_model {

    //class features_model extends Model {

    public $database;

    public function __construct()
    {
        parent::__construct();
        $this->database = $this->getConfig('db_connect');


    }

    public function getConfigs($prefix,$cats) {
        $query = "SELECT conf.idconfig, conf.status, conf.value,conf.name as config_name, cat.smarty as cat_smarty, 
                         conf.field_type, conf.smarty, cat.name as cat_name, cat.idconfigcategory as cate 
                    FROM {$prefix}_tbconfig conf, {$prefix}_tbconfig_category cat 
                   WHERE conf.idconfigcategory IN($cats) 
                     AND conf.idconfigcategory = cat.idconfigcategory 
                ORDER BY cate";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query);
            return false ;
        }else{
            return $ret;
        }
    }

    public function activateConfig($id) {
        return $this->db->Execute("update hdk_tbconfig set value = '1' where idconfig ='$id'");
    }

    public function deactivateConfig($id) {
        return $this->db->Execute("update hdk_tbconfig set value = '0' where idconfig ='$id'");
    }

    public function changeVal($id, $value) {
        return $this->db->Execute("update hdk_tbconfig set value = '$value' where idconfig = '$id'");
    }
	
	public function getArrayConfigs($id) {
        $query = "SELECT session_name,value FROM tbconfig WHERE idconfigcategory = $id";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query);
            return false ;
        }else{
            while (!$ret->EOF) {
                $ses = $ret->fields['session_name'];
                $val = $ret->fields['value'];
                $emailConfs[$ses] = $val;
                $ret->MoveNext();
            }
            return $emailConfs;
        }
        
    }

    public function getEmailConfigs() {
        $conf = $this->select("select session_name,value from hdk_tbconfig where idconfigcategory = 5");
        while (!$conf->EOF) {
            $ses = $conf->fields['session_name'];
            $val = $conf->fields['value'];
            $emailConfs[$ses] = $val;
            $conf->MoveNext();
        }
        return $emailConfs;
    }
    
    public function getPopConfigs() {
        $conf = $this->select("select session_name,value from tbconfig where idconfigcategory = 12");
        while (!$conf->EOF) {
            $ses = $conf->fields['session_name'];
            $val = $conf->fields['value'];
            $emailConfs[$ses] = $val;
            $conf->MoveNext();
        }
        return $emailConfs;
    }

    public function getLdapConfigs() {
        $conf = $this->select("select session_name,value from hdk_tbconfig where idconfigcategory = 13");
        while (!$conf->EOF) {
            $ses = $conf->fields['session_name'];
            $val = $conf->fields['value'];
            $emailConfs[$ses] = $val;
            $conf->MoveNext();
        }
        return $emailConfs;
    }
	
	
    public function getTempEmail() {
        $query = "SELECT session_name,description AS value FROM tbconfig WHERE idconfigcategory = 11";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query);
            return false ;
        }else{
            while (!$ret->EOF) {
                $ses = $ret->fields['session_name'];
                $val = $ret->fields['value'];
                $tempConfs[$ses] = $val;
                $ret->MoveNext();
            }
            return $tempConfs;
        }
    }

    public function getValueBySessionName($session_name) {
        $conf = $this->select("select value from hdk_tbconfig where session_name = '$session_name'");
        return $conf->fields['value'];
    }

    public function getIdBySessionName($session_name) {
        $query = "SELECT idconfig FROM hdk_tbconfig WHERE session_name = '$session_name'";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query);
            return false ;
        }else{
            return $ret->fields['idconfig'];
        }
    }

    public function updateConfigsVals($sessionName, $value) {
        if ($this->database == 'mysqli') {
            $query = "UPDATE tbconfig SET `value` = '$value' WHERE session_name = '$sessionName'";

            $ret = $this->db->Execute($query);

            if ($this->db->ErrorNo() != 0) {
                $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query);
                return false ;
            }
            
        } elseif ($this->database == 'oci8po') {
            $query = "UPDATE tbconfig SET `value` = '$value' WHERE session_name = '$sessionName'";

            $ret = $this->db->Execute($query);

            if ($this->db->ErrorNo() != 0) {
                $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query);
                return false ;
            }
        }
        //echo $query;        
        return $ret;
    }
    
    public function updatePopConfigs($pophost, $popport, $poptype, $popdomain) {
        $database = $this->getConfig('db_connect');  
        if ($database == 'mysqlt') {
            $this->db->Execute("update hdk_tbconfig set `value` = '$pophost' where session_name = 'POP_HOST'");
            $this->db->Execute("update hdk_tbconfig set `value` = '$popport' where session_name = 'POP_PORT'");
            $this->db->Execute("update hdk_tbconfig set `value` = '$popdomain' where session_name = 'POP_DOMAIN'");
            $foot = $this->db->Execute("update hdk_tbconfig set `value` = '$poptype' where session_name = 'POP_TYPE'");
        } elseif ($database == 'oci8po') {
            $this->db->Execute("update hdk_tbconfig set value = '$pophost' where session_name = 'POP_HOST'");
            $this->db->Execute("update hdk_tbconfig set value = '$popport' where session_name = 'POP_PORT'");
            $this->db->Execute("update hdk_tbconfig set value = '$popdomain' where session_name = 'POP_DOMAIN'");
            $foot = $this->db->Execute("update hdk_tbconfig set value = '$poptype' where session_name = 'POP_TYPE'");   
        }
        return $foot;
    }
    
    public function updateLdapConfigs($ldapserver, $ldapdn, $ldapdomain, $ldapfield, $ldaptype) {
        $database = $this->getConfig('db_connect');  
        if ($database == 'mysqlt') {
        	$this->db->Execute("UPDATE hdk_tbconfig set `value` = '$ldaptype' where session_name = 'SES_LDAP_AD'");
        	$this->db->Execute("UPDATE hdk_tbconfig set `value` = '$ldapserver' where session_name = 'SES_LDAP_SERVER'");
			$this->db->Execute("UPDATE hdk_tbconfig set `value` = '$ldapdn' where session_name = 'SES_LDAP_DN'");
			$this->db->Execute("UPDATE hdk_tbconfig set `value` = '$ldapdomain' where session_name = 'SES_LDAP_DOMAIN'");
			$foot = $this->db->Execute("UPDATE hdk_tbconfig set `value` = '$ldapfield' where session_name = 'SES_LDAP_FIELD'");
        } elseif ($database == 'oci8po') {
        	$this->db->Execute("update hdk_tbconfig set value = '$ldaptype' where session_name = 'SES_LDAP_AD'");
            $this->db->Execute("update hdk_tbconfig set value = '$ldapserver' where session_name = 'SES_LDAP_SERVER'");
            $this->db->Execute("update hdk_tbconfig set value = '$ldapdn' where session_name = 'SES_LDAP_DN'");
            $this->db->Execute("update hdk_tbconfig set value = '$ldapdomain' where session_name = 'SES_LDAP_DOMAIN'");
            $foot = $this->db->Execute("update hdk_tbconfig set value = '$ldapfield' where session_name = 'SES_LDAP_FIELD'");   
        }
        return $foot;
    }

    public function updateMaintenance($msg, $session_name){
        return $this->db->Execute("update hdk_tbconfig set value = '$msg' where session_name = '$session_name'");
    }

    // Since May 31, 2017
    public function setDeploy($server,$state,$created_on)
    {
        return $this->db->Execute("INSERT INTO tbdeploy (gitserver, dttrigger,state,created_on) VALUES ('$server', NOW(),'$state','$created_on')");
    }

    // Since May 30, 2017
    public function getDeployTrigger()
    {
        return $this->db->Execute("SELECT iddeploy FROM tbdeploy WHERE  dtdone = '0000-00-00 00:00:00' LIMIT 1") ;
    }

    // Since May 30, 2017
    public function updateDeploy($idDeploy)
    {
        return $this->db->Execute("UPDATE tbdeploy SET dtdone = NOW() WHERE iddeploy = '$idDeploy' ") ;
    }

    public function updateConfig($id,$stval) {
        $query = "UPDATE hdk_tbconfig SET value = '$stval' WHERE idconfig ='$id'";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query);
            return false ;
        }else{
            return $ret;
        }
    }

    public function updateEmailConfigsHF($sessionName, $value) {
        if ($this->database == 'mysqli') {
            $query = "UPDATE tbconfig SET `description` = '$value' WHERE session_name = '$sessionName'";

            $ret = $this->db->Execute($query);

            if ($this->db->ErrorNo() != 0) {
                $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query);
                return false ;
            }
            
        } elseif ($this->database == 'oci8po') {
            $query = "UPDATE tbconfig SET `description` = RAWTOHEX('$value') WHERE session_name = '$sessionName'";

            $ret = $this->db->Execute($query);

            if ($this->db->ErrorNo() != 0) {
                $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query);
                return false ;
            }
        }        
        return $ret;
    }
}