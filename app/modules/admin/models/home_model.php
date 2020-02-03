<?php
if(class_exists('Model')) {
    class DynamicFeatures_model extends Model {}
} elseif(class_exists('cronModel')) {
    class DynamicFeatures_model extends cronModel {}
} elseif(class_exists('apiModel')) {
    class DynamicFeatures_model extends apiModel {}
}

class home_model extends DynamicFeatures_model {

//class features_model extends Model {

    public function getMysqlVersion()
    {
        $rs = $this->db->Execute("SELECT VERSION() AS mysqlversion");
        return $rs->fields['mysqlversion'];

    }
    public function getConfigs($cats) {
        return $this->db->Execute("select conf.idconfig, conf.status, conf.value,conf.name as config_name, cat.smarty as cat_smarty, conf.field_type, conf.smarty, cat.name as cat_name, cat.idconfigcategory as cate from hdk_tbconfig conf, hdk_tbconfig_category cat where conf.idconfigcategory in($cats) and conf.idconfigcategory = cat.idconfigcategory order by cate");
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
        $conf = $this->select("select session_name,value from hdk_tbconfig where idconfigcategory = $id");
        while (!$conf->EOF) {
            $ses = $conf->fields['session_name'];
            $val = $conf->fields['value'];
            $emailConfs[$ses] = $val;
            $conf->MoveNext();
        }
        return $emailConfs;
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
        $conf = $this->select("select session_name,description as value from hdk_tbconfig where idconfigcategory = 11");
        while (!$conf->EOF) {
            $ses = $conf->fields['session_name'];
            $val = $conf->fields['value'];
            $tempConfs[$ses] = $val;
            $conf->MoveNext();
        }
        return $tempConfs;
    }

    public function getValueBySessionName($session_name) {
        $conf = $this->select("select value from hdk_tbconfig where session_name = '$session_name'");
        return $conf->fields['value'];
    }

    public function getIdBySessionName($session_name) {
        $conf = $this->select("select idconfig from hdk_tbconfig where session_name = '$session_name'");
        return $conf->fields['idconfig'];
    }

    public function updateEmailConfigs($title, $host, $domain, $user, $pass, $auth, $sender, $header, $footer, $mailport) {
        $database = $this->getConfig('db_connect');  
        if ($database == 'mysqlt') {
            $this->db->Execute("update hdk_tbconfig set `value` = '$host' where session_name = 'EM_HOSTNAME'");
            $this->db->Execute("update hdk_tbconfig set `value` = '$domain' where session_name = 'EM_DOMAIN'");
            $this->db->Execute("update hdk_tbconfig set `value` = '$user' where session_name = 'EM_USER'");
            $this->db->Execute("update hdk_tbconfig set `value` = '$pass' where session_name = 'EM_PASSWORD'");
            $this->db->Execute("update hdk_tbconfig set `value` = '$sender' where session_name = 'EM_SENDER'");
            $this->db->Execute("update hdk_tbconfig set `value` = '$auth' where session_name = 'EM_AUTH'");      
            $this->db->Execute("update hdk_tbconfig set description = '$header' where session_name = 'EM_HEADER'");
            $this->db->Execute("update hdk_tbconfig set `value` = '$title' where session_name = 'EM_TITLE'");
            $this->db->Execute("update hdk_tbconfig set `value` = '$mailport' where session_name = 'EM_PORT'");


            $foot = $this->db->Execute("update hdk_tbconfig set description = '$footer' where session_name = 'EM_FOOTER'");
        } elseif ($database == 'oci8po') {
            $this->db->Execute("update hdk_tbconfig set value = '$host' where session_name = 'EM_HOSTNAME'");
            $this->db->Execute("update hdk_tbconfig set value = '$domain' where session_name = 'EM_DOMAIN'");
            $this->db->Execute("update hdk_tbconfig set value = '$user' where session_name = 'EM_USER'");
            $this->db->Execute("update hdk_tbconfig set value = '$pass' where session_name = 'EM_PASSWORD'");
            $this->db->Execute("update hdk_tbconfig set value = '$sender' where session_name = 'EM_SENDER'");
            $this->db->Execute("update hdk_tbconfig set value = '$auth' where session_name = 'EM_AUTH'");      
            $this->db->Execute("update hdk_tbconfig set description = RAWTOHEX('$header') where session_name = 'EM_HEADER'");
            $this->db->Execute("update hdk_tbconfig set value = '$title' where session_name = 'EM_TITLE'");
            $this->db->Execute("update hdk_tbconfig set value`= '$mailport' where session_name = 'EM_PORT'");
            $foot = $this->db->Execute("update hdk_tbconfig set description = RAWTOHEX('$footer') where session_name = 'EM_FOOTER'");
        }        
        return $foot;
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
}