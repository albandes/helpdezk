<?php
class department_model extends Model{
    public function selectCorporations($where = NULL, $order = NULL, $limit = NULL) {
        $database = $this->getConfig('db_connect');
        if ($database == 'mysqlt') {
            $query = "SELECT idperson, name from tbperson where idtypeperson = 4 $where $order $limit" ;
        } elseif ($database == 'oci8po') {
            $core  = "SELECT idperson, name from tbperson where idtypeperson = 4 $where $order";
            if($limit){
                $limit = str_replace('LIMIT', "", $limit);
                $p     = explode(",", $limit);
                $start = $p[0] + 1; 
                $end   = $p[0] +  $p[1]; 
                $query =    "
                            SELECT   *
                              FROM   (SELECT                                          
                                            a  .*, ROWNUM rnum
                                        FROM   (  
                                                  
                                                $core 

                                                ) a
                                       WHERE   ROWNUM <= $end)
                             WHERE   rnum >= $start         
                            ";
            }else{
                $query = $core;
            }
        }
        return $this->db->Execute($query);
    }
    public function selectDepartment($where = NULL, $order = NULL, $limit = NULL){
        $database = $this->getConfig('db_connect');
        if ($database == 'mysqlt') {
            $query = "SELECT tbd.iddepartment, tbp.name, tbd.status, tbd.name as department from hdk_tbdepartment tbd, tbperson tbp where tbd.idperson = tbp.idperson $where $order $limit" ;
        } elseif ($database == 'oci8po') {
            $core  = "SELECT tbd.iddepartment, tbp.name, tbd.status, tbd.name as department from hdk_tbdepartment tbd, tbperson tbp where tbd.idperson = tbp.idperson $where $order";
            if($limit){
                $limit = str_replace('LIMIT', "", $limit);
                $p     = explode(",", $limit);
                $start = $p[0] + 1; 
                $end   = $p[0] +  $p[1]; 
                $query =    "
                            SELECT   *
                              FROM   (SELECT                                          
                                            a  .*, ROWNUM rnum
                                        FROM   (  
                                                  
                                                $core 

                                                ) a
                                       WHERE   ROWNUM <= $end)
                             WHERE   rnum >= $start         
                            ";
            }else{
                $query = $core;
            }
        }
        return $this->db->Execute($query);
    }
    public function countDepartment($where = NULL){
        $sel = $this->select("SELECT count(iddepartment) as total from hdk_tbdepartment tbd, tbperson tbp where tbd.idperson = tbp.idperson $where");
        return $sel;
    }
    public function insertDepartment($name,$company){
        return $this->db->Execute("insert into hdk_tbdepartment (idperson,name) values ('$company','$name')");
    }
    public function selectDepartmentData($id){
        return $this->select("SELECT name, idperson from hdk_tbdepartment where iddepartment='$id'");
    }
    public function updateDepartment($id,$name,$company){
        return $this->db->Execute("UPDATE hdk_tbdepartment set name='$name', idperson='$company' where iddepartment='$id'");
    }
    public function departmentDeactivate($id){
        return $this->db->Execute("UPDATE hdk_tbdepartment set status = 'N' where iddepartment in ($id)");
    }
    public function departmentActivate($id){
        return $this->db->Execute("UPDATE hdk_tbdepartment set status = 'A' where iddepartment in ($id)");
    }
    public function departmentDelete($id){
        return $this->db->Execute("delete from hdk_tbdepartment where iddepartment in ($id)");
    }	
	public function checkDepartmentName($id, $name){
        $sel = $this->select("SELECT COUNT(*) as total FROM hdk_tbdepartment WHERE name = '$name' AND idperson = $id");
    	return $sel->fields['total'];
    }
	public function getIdCompany($iddepartment){
        return $this->db->Execute("SELECT idperson from hdk_tbdepartment where iddepartment = $iddepartment");
    }
}
?>
