<?php

class features_model extends Model {

    public function getConfigs($cats) {
        return $this->db->Execute("select conf.idconfig, conf.status, conf.value,conf.name as config_name, cat.smarty as cat_smarty, conf.field_type, conf.smarty, cat.name as cat_name, cat.idconfigcategory as cate from hdk_tbconfig as conf, hdk_tbconfig_category as cat where conf.idconfigcategory in($cats) and conf.idconfigcategory = cat.idconfigcategory order by cate");
    }

    public function activateConfig($id) {
        return $this->db->Execute("update hdk_tbconfig set value = '1' where idconfig ='$id'");
    }

    public function deactivateConfig($id) {
        return $this->db->Execute("update hdk_tbconfig set value = '0' where idconfig ='$id'");
    }

    public function changeVal($id, $value) {
        return $this->db->Execute("update hdk_tbconfig set value = $value where idconfig = '$id'");
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

    public function updateEmailConfigs($host, $domain, $user, $pass, $auth, $sender, $header, $footer) {
        $this->db->Execute("update hdk_tbconfig set `value` = '$host' where session_name = 'EM_HOST'");
        $this->db->Execute("update hdk_tbconfig set `value` = '$domain' where session_name = 'EM_DOMAIN'");
        $this->db->Execute("update hdk_tbconfig set `value` = '$user' where session_name = 'EM_USER'");
        $this->db->Execute("update hdk_tbconfig set `value` = '$pass' where session_name = 'EM_PASSWORD'");
        $this->db->Execute("update hdk_tbconfig set `value` = '$sender' where session_name = 'EM_SENDER'");
        $this->db->Execute("update hdk_tbconfig set `value` = '$auth' where session_name = 'EM_AUTH'");
        $this->db->Execute("update hdk_tbconfig set description = '$header' where session_name = 'EM_HEADER'");
        $foot = $this->db->Execute("update hdk_tbconfig set description = '$footer' where session_name = 'EM_FOOTER';");
        
        return $foot;
    }

}

?>
