<?php

if(class_exists('Model')) {
    class DynamicOperatorData_model extends Model {}
} elseif(class_exists('cronModel')) {
    class DynamicOperatorData_model extends cronModel {}
} elseif(class_exists('apiModel')) {
    class DynamicOperatorData_model extends apiModel {}
}

class operator_model extends DynamicOperatorData_model{
    public $database;

    public function __construct()
    {
        parent::__construct();
        $this->database = $this->getConfig('db_connect');

    }   

	
	public function getOperator($where=null,$order=null,$limit=null,$group=null)
	{
		$sql = "SELECT a.idperson, 
                       a.idnatureperson, 
                       b.name naturetype, 
                       a.name operator, 
                       phone_number, 
                       cel_phone, 
                       contact_person,
                       a.status
            FROM tbperson a
            JOIN tbnatureperson b
            ON b.idnatureperson = a.idnatureperson
            LEFT OUTER JOIN tbjuridicalperson c
            ON c.idperson = a.idperson				  
                
                 $where $group $order $limit";
		$ret = $this->db->Execute($sql); 
        //echo "{$sql}\n";

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
	}

    public function insertOperator($typePersonID,$typeID,$operatorName, $phone, $celPhone) {
        $sql = "INSERT INTO tbperson(idtypelogin, idtypeperson, idnatureperson, idtheme, name, phone_number, cel_phone) 
                  VALUES(3, {$typePersonID}, $typeID, 1 ,'$operatorName', '$phone', '$celPhone')";

        $ret = $this->db->Execute($sql); //echo "{$sql}\n";

        if($ret)
            return array('success' => true, 'message' => '', 'id' => $this->db->Insert_ID());
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }

    public function insertOperatorContact($operatorID,$contact) {
        $sql = " INSERT INTO tbjuridicalperson (idperson,contact_person)
                     VALUES ($operatorID,'$contact')";

        $ret = $this->db->Execute($sql); //echo "{$sql}\n";

        if($ret)
            return array('success' => true, 'message' => '', 'id' => $this->db->Insert_ID());
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }



    public function updateOperator($operatorID,$operatorName, $operatorPhone, $operatorMobile) {
        $sql = "UPDATE tbperson
                   SET name = '$operatorName',
                       phone_number = '$operatorPhone', 
                       cel_phone = '$operatorMobile'
                 WHERE idperson = '$operatorID'";

        $ret = $this->db->Execute($sql); //echo "{$sql}\n";

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }

    public function updatetOperatorContact($operatorID,$contact) {
        $sql = " UPDATE tbjuridicalperson 
                    SET contact_person = '$contact'
                 WHERE idperson = '$operatorID'";
        $ret = $this->db->Execute($sql); //echo "{$sql}\n";

            if($ret)
                return array('success' => true, 'message' => '', 'data' => $ret);
            else
                return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }
    

     public function changeStatus($operatorID,$newStatus)
     {
         $sql = "UPDATE tbperson SET `status` = '{$newStatus}' WHERE idperson = {$operatorID}";
         $ret = $this->db->Execute($sql); //echo "{$sql}\n";

         if($ret)
             return array('success' => true, 'message' => '', 'data' => $ret);
         else
             return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
     }

     public function getLoginTypes($where = null,$order = null)
    {       
        if ($this->database == 'mysqli') {
            $query = "select idtypelogin,`name` from tbtypelogin $where $order";
        } elseif ($this->database == 'oci8po') {
            $query = "select idtypelogin, name from tbtypelogin $where $order";
        }

        $ret = $this->select($query);
        
        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg() . "<br> QUERY: " . $query;
            $this->error($sError);
            return false;
        }
        return $ret;
    }

}