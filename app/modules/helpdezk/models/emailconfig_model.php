<?php

class emailconfig_model extends Model {

    public function selectConfigs($where) {
        return $this->db->Execute("select * from hdk_tbconfig where idconfigcategory = 3 $where");
    }

    public function countConfigs($where = NULL) {
        $sel = $this->select("SELECT count(idconfig) as total from hdk_tbconfig where idconfigcategory = 3 $where");
        return $sel;
    }

    public function getTemplate($id) {
        return $this->db->Execute("select idtemplate from hdk_tbconfig_has_template where idconfig = '$id'");
    }

    public function getTemplateData($id) {
        return $this->db->Execute("select name, description from hdk_tbtemplate_email where idtemplate = $id");
    }

    public function updateTemplate($id, $name, $description) {
        return $this->db->Execute("update hdk_tbtemplate_email set name = '$name', description = '$description' where idtemplate ='$id'");
    }

    public function getGroupInCharge($id) {
        return $this->select("select id_in_charge, type from hdk_tbrequest_in_charge where ind_in_charge = 1 and code_request = '$id' ");
    }

    public function getEmailsfromGroupOperators($idgroup) {
        return $this->select("select
								  pers.email,
								  grpname.name
								from tbperson pers,
								  tbperson grpname,
								  hdk_tbgroup grp,
								  hdk_tbgroup_has_person pergrp
								where pers.idperson = pergrp.idperson
								AND pers.status = 'A'
								and grp.idgroup = pergrp.idgroup
								AND grpname.idperson = grp.idperson
								and grpname.idperson = '$idgroup'");
    }

    public function getUserEmail($iduser) {
        return $this->select("select
  pers.email
from tbperson pers
where pers.idperson = '$iduser'");
    }

    public function getRequesterEmail($code_request) {
        return $this->select("select
  pers.email,
  pers.name,
  pers.idtypeperson
from tbperson pers,
  hdk_tbrequest req
where pers.idperson = req.idperson_owner
and req.code_request = '$code_request'");
    }
    
    public function getEmailIdBySession($session){
    	$ret = $this->db->Execute("SELECT c.idtemplate
									FROM hdk_tbconfig a, hdk_tbconfig_has_template b, hdk_tbtemplate_email c
									WHERE a.idconfig = b.idconfig
									AND b.idtemplate = c.idtemplate
									AND session_name = '$session'");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
            return false;
        }
        return $ret->fields['idtemplate'];    	
    }

}

?>
