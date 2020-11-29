<?php

//class emailconfig_model extends Model {
if(class_exists('Model')) {
    class DynamicEmailConfig_model extends Model {}
} elseif(class_exists('cronModel')) {
    class DynamicEmailConfig_model extends cronModel {}
} elseif(class_exists('apiModel')) {
    class DynamicEmailConfig_model extends apiModel {}
}

class emailconfig_model extends DynamicEmailConfig_model {

    public function selectConfigs($where) {
        $query = "SELECT * FROM hdk_tbconfig WHERE idconfigcategory = 3 $where";

        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: " . $query;
            $this->error($sError);
            return false;
        }

        return $ret;
    }

    public function countConfigs($where = NULL) {
        $query = "SELECT count(idconfig) as total from hdk_tbconfig where idconfigcategory = 3 $where";

        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: " . $query;
            $this->error($sError);
            return false;
        }

        return $ret;
    }

    public function getTemplate($id) {
        $query = "SELECT idtemplate FROM hdk_tbconfig_has_template WHERE idconfig = '$id'";

        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: " . $query;
            $this->error($sError);
            return false;
        }

        return $ret;
    }

    public function getTemplateData($id) {
        $query = "SELECT name, description FROM hdk_tbtemplate_email WHERE idtemplate = $id";

        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: " . $query;
            $this->error($sError);
            return false;
        }

        return $ret;
    }

    public function updateTemplate($id, $name, $description) {
        $query = "UPDATE hdk_tbtemplate_email SET `name` = '$name', description = '$description' WHERE idtemplate ='$id'";

        $ret = $this->db->Execute($query);
        //echo $query;

        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: " . $query;
            $this->error($sError);
            return false;
        }

        return $ret;
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

    public function getIdPersonfromGroupOperators($idgroup) {
        return $this->select("select
								  pers.idperson
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
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
            return false;
        }
        return $ret->fields['idtemplate'];    	
    }

    public function changeConfStatus($id,$newstatus){
    	$query = "UPDATE hdk_tbconfig SET `status` = '$newstatus' WHERE idconfig = $id";

        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: " . $query;
            $this->error($sError);
            return false;
        }

        return $ret;    	
    }

}

?>
