<?php
if(class_exists('Model')) {
    class DynamicDepartment_model extends Model {}
} elseif(class_exists('cronModel')) {
    class DynamicDepartment_model extends cronModel {}
} elseif(class_exists('apiModel')) {
    class DynamicDepartment_model extends apiModel {}
}

class department_model extends DynamicDepartment_model {

    //class features_model extends Model {

    public $database;

    public function __construct()
    {
        parent::__construct();
        $this->database = $this->getConfig('db_connect');

    }

    public function selectDepartment($where=NULL, $group=NULL, $order=NULL, $limit=NULL){
        
        if ($this->database == 'mysqli') {
            $query = "SELECT tbd.iddepartment, tbp.name company, tbd.status, tbd.name AS department, 
                             tbd.idperson AS idcompany
                        FROM hdk_tbdepartment tbd, tbperson tbp 
                       WHERE tbd.idperson = tbp.idperson
                       $where $group $order $limit" ;
        } elseif ($this->database == 'oci8po') {
            $core  = "SELECT tbd.iddepartment, tbp.name company, tbd.status, tbd.name AS department, 
                             tbd.idperson AS idcompany 
                        FROM hdk_tbdepartment tbd, tbperson tbp 
                       WHERE tbd.idperson = tbp.idperson 
                       $where $group $order";
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
        
        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query);
            return false ;
        }else{
            return $ret;
        }
    }
	
	public function insertDepartment($company,$name){

        $query = "INSERT INTO hdk_tbdepartment (idperson,`name`) VALUES ('$company','$name')";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query);
            return false ;
        }
        
        return $ret;        
        
    }
    	
	public function updateDepartment($iddepartment, $idcompany, $department){

        $query = "UPDATE hdk_tbdepartment SET 
                            idperson = $idcompany,
                            `name` = '$department'
                   WHERE iddepartment = $iddepartment";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query);
            return false ;
        }
        
        return $ret;
    }

    public function updateDepartmentStatus($id,$newStatus){
        $query = "UPDATE hdk_tbdepartment SET `status` = '$newStatus' WHERE iddepartment IN ($id)";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query);
            return false ;
        }
        return $ret;
    }
	
	public function departmentDelete($iddepartment){
        $query = "DELETE FROM hdk_tbdepartment WHERE iddepartment = $iddepartment";
        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query);
            return false ;
        }

        return $ret;
    }
	
	public function getDepartmentData($where=null){
        $query = "SELECT iddepartment, `name`, idperson FROM hdk_tbdepartment
                  $where";
        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query);
            return false ;
        }
        return $ret;
    }

    public function checkDepartment($where=null){
        $query = "SELECT iddepartment, `name`
                    FROM hdk_tbdepartment 
                   $where";
        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query);
            return false ;
        }
        return $ret;
    }

    public function getPersonByDepartment($iddepartment){
        $query = "SELECT idperson FROM hdk_tbdepartment_has_person WHERE iddepartment = $iddepartment";
        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query);
            return false ;
        }
        return $ret;
    }

    public function updatePersonDepartment($iddepartment, $idnew){

        $query = "UPDATE hdk_tbdepartment_has_person SET 
                            iddepartment = $idnew
                   WHERE iddepartment = $iddepartment";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query);
            return false ;
        }

        return $ret;
    }


    public function InsertID() {
        return $this->db->Insert_ID();
    }

}