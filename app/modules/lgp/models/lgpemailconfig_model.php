<?php

//class emailconfig_model extends Model {
if(class_exists('Model')) {
    class DynLgpEmailConfig_model extends Model {}
} elseif(class_exists('cronModel')) {
    class DynLgpEmailConfig_model extends cronModel {}
} elseif(class_exists('apiModel')) {
    class DynLgpEmailConfig_model extends apiModel {}
}

class lgpemailconfig_model extends DynLgpEmailConfig_model {

    public $database;

    public function __construct()
    {
        parent::__construct();
        $this->database = $this->getConfig('db_connect');

    }

    public function selectConfigs($where) {
        $sql = "SELECT * FROM lgp_tbconfig WHERE idconfigcategory = 3 $where";

        return $this->selectPDO($sql);
    }

    public function countConfigs($where = NULL) {
        $sql = "SELECT count(idconfig) AS total FROM lgp_tbconfig WHERE idconfigcategory = 3 $where";

        return $this->selectPDO($sql);
    }

    public function getTemplate($id) {
        $sql = "SELECT idtemplate FROM lgp_tbconfig_has_template WHERE idconfig = '$id'";

        return $this->selectPDO($sql);
    }

    public function getTemplateData($id) {
        $sql = "SELECT `name`, `description` FROM lgp_tbtemplate_email WHERE idtemplate = $id";

        return $this->selectPDO($sql);
    }

    public function updateTemplate($id, $name, $description) {
        $query = "UPDATE lgp_tbtemplate_email SET `name` = '$name', description = '$description' WHERE idtemplate ='$id'";

        $ret = $this->db->Execute($query);
        //echo $query;

        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: " . $query;
            $this->error($sError);
            return false;
        }

        return $ret;
    }

    public function getInCharge($ticketID,$flgTrack=null) {
        $sql = "SELECT id_in_charge, type FROM lgp_tbrequest_in_charge WHERE (ind_in_charge = 1{$flgTrack}) AND code_request = '$ticketID'";
        //echo "{$sql}\n";
        return $this->selectPDO($sql);
    }

    public function getEmailsfromGroupOperators($idgroup) {
        $sql = "SELECT pers.email, pers.name grpmember, grpname.name grpname
				  FROM tbperson pers, tbperson grpname, lgp_tbgroup grp, lgp_tbgroup_has_person pergrp
				 WHERE pers.idperson = pergrp.idperson
				   AND pers.status = 'A'
				   AND grp.idgroup = pergrp.idgroup
				   AND grpname.idperson = grp.idperson
				   AND grpname.idperson = '$idgroup'";

        return $this->selectPDO($sql);
    }

    public function getIdPersonfromGroupOperators($idgroup) {
        $sql = "SELECT pers.idperson
				  FROM tbperson pers, tbperson grpname, lgp_tbgroup grp, lgp_tbgroup_has_person pergrp
				 WHERE pers.idperson = pergrp.idperson
				   AND pers.status = 'A'
				   AND grp.idgroup = pergrp.idgroup
				   AND grpname.idperson = grp.idperson
				   AND grpname.idperson = '$idgroup'";

        return $this->selectPDO($sql);
    }

    public function getUserEmail($iduser) {
        $sql = "SELECT pers.email, pers.name FROM tbperson pers WHERE pers.idperson = '$iduser'";

        return $this->selectPDO($sql);
    }

    public function getRequesterEmail($code_request) {
        $sql = "SELECT pers.email, pers.name, pers.idtypeperson
                  FROM tbperson pers, lgp_tbrequest req
                 WHERE pers.idperson = req.idperson_owner
                   AND req.code_request = '$code_request'";

        return $this->selectPDO($sql);
    }
    
    public function getTemplateBySession($session){
    	$sql = "SELECT c.idtemplate, c.name template_name, c.description template_body
				  FROM lgp_tbconfig a, lgp_tbconfig_has_template b, lgp_tbtemplate_email c
				 WHERE a.idconfig = b.idconfig
				   AND b.idtemplate = c.idtemplate
				   AND session_name = '$session'";
        
        return $this->selectPDO($sql);    	
    }

    public function changeConfStatus($id,$newstatus){
    	$query = "UPDATE lgp_tbconfig SET `status` = '$newstatus' WHERE idconfig = $id";

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
