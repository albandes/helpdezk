<?php
class requestrules_model extends Model{
    	
    public function getUsers($iditem, $idservice){
        return $this->db->Execute("SELECT per.idperson, per.name FROM tbperson per WHERE per.idperson NOT IN (SELECT DISTINCT app.idperson FROM hdk_tbapproval_rule app WHERE app.iditem = $iditem AND app.idservice = $idservice) AND per.idtypeperson IN('1','3') AND per.status = 'A' ORDER BY per.name ASC");
    }
	
	public function getUsersApprove($iditem, $idservice){
        $database = $this->getConfig('db_connect');
        if ($database == 'mysqlt') {
            return $this->db->Execute("SELECT per.idperson, per.name, app.fl_recalculate FROM tbperson per, hdk_tbapproval_rule app WHERE per.idperson = app.idperson AND app.iditem = $iditem AND app.idservice = $idservice ORDER BY app.order ASC");
        } elseif ($database == 'oci8po') {
            return $this->db->Execute("SELECT per.idperson, per.name, app.fl_recalculate FROM tbperson per, hdk_tbapproval_rule app WHERE per.idperson = app.idperson AND app.iditem = $iditem AND app.idservice = $idservice ORDER BY app.order_ ASC");
        }
    }
    
	public function insertUsersApprove($iditem, $idservice, $idperson, $order, $recalculate){
        $database = $this->getConfig('db_connect');
        if ($database == 'mysqlt') {
            return $this->db->Execute("insert into hdk_tbapproval_rule (iditem,idservice,idperson,`order`,fl_recalculate) values ('$iditem','$idservice','$idperson','$order','$recalculate')");
        } elseif ($database == 'oci8po') {
            return $this->db->Execute("insert into hdk_tbapproval_rule (iditem,idservice,idperson,order_,fl_recalculate) values ('$iditem','$idservice','$idperson','$order','$recalculate')");
        }
    }
	
	public function deleteUsersApprove($iditem, $idservice){
        return $this->db->Execute("DELETE FROM  hdk_tbapproval_rule WHERE idservice = '$idservice' and iditem = '$iditem'");
    }
}
?>
