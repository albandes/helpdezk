<?php

if(class_exists('Model')) {
    class dynamicParent_model extends Model {}
} elseif(class_exists('cronModel')) {
    class dynamicParent_model extends cronModel {}
} elseif(class_exists('apiModel')) {
    class dynamicParent_model extends apiModel {}
}

class parent_model extends dynamicParent_model
{
    public $database;

    public function __construct()
    {
        parent::__construct();
        $this->database = $this->getConfig('db_connect');

    }

    public function getParents($where = NULL, $order = NULL, $limit = NULL){
        $sql = "SELECT idparent, `name`, email
                  FROM acd_tbparent a, tbperson_profile b
                 WHERE a.idperson_profile = b.idperson_profile $where $order $limit";
        $ret = $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql );
        else
            return $ret;
    }

    public function countParents($where = NULL){
        $sql = "SELECT count(idparent) total  
                  FROM acd_tbparent a, tbperson_profile b
                 WHERE a.idperson_profile = b.idperson_profile $where";
        $ret = $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql );
        else
            return $ret;
    }

    public function getGender($where=NULL,$order=NULL){
        $sql = "SELECT idgender, description, abbrev FROM hur_tbgender $where $order";
        $ret = $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql );
        else
            return $ret;
    }

    public function insertParent($parentName,$parentGender,$parentCpf,$parentEmail){
        $sql = "INSERT INTO tbperson_profile (`name`, cpf, email,idgender) 
                  VALUES('$parentName','$parentCpf','$parentEmail',$parentGender)";
        $ret = $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql );
        else
            return $this->db->insert_Id();
    }

    public function insertParentProfile($idperson){
        $sql = "INSERT INTO acd_tbparent (idperson_profile) VALUES($idperson)";
        $ret = $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql );
        else
            return $this->db->insert_Id();
    }

    public function insertBindConfigs($idstudent,$idparent,$idkinship,$emailSms,$bankTicket,$accessApp){
        $sql = "INSERT INTO acd_tbstudent_has_acd_tbparent (idstudent,idparent,idkinship,email_sms,bank_ticket,access_app) 
                  VALUES($idstudent,$idparent,$idkinship,'$emailSms','$bankTicket','$accessApp')";
        $ret = $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql );
        else
            return $ret;
    }

    public function getBindConfigs($idparent){
        $sql = "SELECT idstudent,idkinship,email_sms,bank_ticket,access_app 
                  FROM acd_tbstudent_has_acd_tbparent 
                 WHERE idparent = $idparent";
        $ret = $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql );
        else
            return $ret;
    }

    public function updateParent($idperson_profile,$parentName,$parentGender,$parentCpf,$parentEmail){
        $sql = "UPDATE tbperson_profile 
                   SET `name` = '$parentName', 
                        cpf = '$parentCpf', 
                        email = '$parentEmail',
                        idgender =  $parentGender
                 WHERE idperson_profile = $idperson_profile";
        $ret = $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql );
        else
            return $ret;
    }

    public function deleteBindConfigs($idparent){
        $sql = "DELETE FROM acd_tbstudent_has_acd_tbparent WHERE idparent = $idparent";
        $ret = $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql );
        else
            return $ret;
    }

    public function getBindsByYear($year){
        $sql = "SELECT DISTINCT a.idparent, cpf, `name`, IFNULL(c.idintranet,c.idperseus) matricula, a.idstudent
                  FROM acd_tbstudent_has_acd_tbparent a, acd_tbenrollment b, acd_tbstudent c, acd_tbparent d, tbperson_profile e
                 WHERE a.idstudent = b.idstudent
                   AND (a.idparent = d.idparent AND a.idstudent = c.idstudent)
                   AND d.idperson_profile = e.idperson_profile
                   AND b.year = $year
                   AND record_status = 'A'
                   AND (cpf IS NOT NULL AND cpf != '')
              ORDER BY `name`";
        $ret = $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql );
        else
            return $ret;
    }

    public function importBindConfs($idparent,$idstudent,$emailsms,$bankticket,$accessapp){
        $sql = "UPDATE acd_tbstudent_has_acd_tbparent
                   SET email_sms = '$emailsms',
                       bank_ticket = '$bankticket',
                       access_app = '$accessapp'
                   WHERE idparent = $idparent
                   AND idstudent = $idstudent";
        $ret = $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql );
        else
            return $ret;
    }

}
