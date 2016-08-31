<?php
class department_model extends Model{
    public function selectCorporations() {
        $ret = $this->db->Execute("SELECT idperson,name from tbperson where idtypeperson = 4");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
            return false;
        }
        return $ret;
    }
     public function selectDepartment($where = NULL, $order = NULL, $limit = NULL){
        $ret = $this->db->Execute("SELECT tbd.iddepartment, tbp.name, tbd.status, tbd.name as department from hdk_tbdepartment as tbd, tbperson as tbp where tbd.idperson = tbp.idperson $where $order $limit");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
            return false;
        }
        return $ret;
    }
    public function countDepartment($where = NULL, $order = NULL, $limit = NULL){
        $sel = $this->select("SELECT count(iddepartment) as total from hdk_tbdepartment $where $order $limit");
        if(!$sel) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
            return false;
        }
        return $sel;
    }
    public function insertDepartment($name,$company){
        $ret = $this->db->Execute("insert into hdk_tbdepartment (idperson,name) values ('$company','$name')");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
            return false;
        }
        return $ret;
    }
    public function selectDepartmentData($id){
        $ret  = $this->select("SELECT name, idperson from hdk_tbdepartment where iddepartment='$id';");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
            return false;
        }
        return $ret;
    }
    public function updateDepartment($id,$name,$company){
        $ret = $this->db->Execute("UPDATE hdk_tbdepartment set name='$name', idperson='$company' where iddepartment='$id'");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
            return false;
        }
        return $ret;
    }
    public function departmentDeactivate($id){
        $ret = $this->db->Execute("UPDATE hdk_tbdepartment set status = 'N' where iddepartment in ($id)");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
            return false;
        }
        return $ret;
    }
    public function departmentActivate($id){
        $ret = $this->db->Execute("UPDATE hdk_tbdepartment set status = 'A' where iddepartment in ($id)");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
            return false;
        }
        return $ret;
    }
    
	public function getIdDepartment($id_person){
		$ret = $this->db->Execute("SELECT iddepartment FROM hdk_tbdepartment_has_person WHERE idperson = $id_person");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
            return false;
        }
        return $ret->fields['iddepartment'];
	}
	
}
?>
