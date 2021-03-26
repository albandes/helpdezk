<?php
if(class_exists('Model')) {
    class DynamicHome_model extends Model {}
} elseif(class_exists('cronModel')) {
    class DynamicHome_model extends cronModel {}
} elseif(class_exists('apiModel')) {
    class DynamicHome_model extends apiModel {}
}

class home_model extends DynamicHome_model
{


    public $database;

    public function __construct()
    {
        parent::__construct();
        $this->database = $this->getConfig('db_connect');

    }

    public function getOperatorRequestStats($idPerson, $groupsId,$andQuery = "",$locale = "en_US")
    {
      $condGroup = strlen($groupsId) > 0 
        ? " (
          (c.ind_in_charge = 1 AND c.id_in_charge IN ({$groupsId})) 
          OR (c.ind_operator_aux = 1 AND c.id_in_charge = {$idPerson}) 
          OR (c.id_in_charge IN ({$groupsId}) AND c.ind_track = 1)
          ) " : " (c.ind_operator_aux = 1 AND c.id_in_charge = {$idPerson}) ";
    
      $sql =  "
          SELECT 
            t1.requests,
            t2.in_atendance,
            t3.new_requests,
            t4.finished,
            t5.waiting_approval,
            t6.total_requests 
          FROM
            (SELECT 
            FORMAT(COUNT(DISTINCT a.code_request),0,'{$locale}') AS requests      
            FROM
            (
              hdk_tbrequest a,
              hdk_tbstatus b,
              hdk_tbrequest_in_charge c,
              tbperson d,
              tbperson e
            ) 
            WHERE {$condGroup}
            {$andQuery}
            AND c.id_in_charge = d.idperson 
            AND a.idstatus = b.idstatus 
            AND a.code_request = c.code_request 
            AND a.idperson_owner = e.idperson 
            AND b.idstatus = a.idstatus 
            AND b.idstatus_source <= 5) AS t1,
            (SELECT 
            COUNT(DISTINCT a.code_request) AS in_atendance 
            FROM
            (
              hdk_tbrequest a,
              hdk_tbstatus b,
              hdk_tbrequest_in_charge c,
              tbperson d,
              tbperson e
            ) 
            WHERE {$condGroup} 
            {$andQuery}
            AND c.id_in_charge = d.idperson 
            AND a.idstatus = b.idstatus 
            AND a.code_request = c.code_request 
            AND a.idperson_owner = e.idperson 
            AND b.idstatus = a.idstatus 
            AND b.idstatus_source = 3) AS t2,
            (SELECT 
            COUNT(DISTINCT a.code_request) AS new_requests 
            FROM
            (
              hdk_tbrequest a,
              hdk_tbstatus b,
              hdk_tbrequest_in_charge c,
              tbperson d,
              tbperson e
            ) 
            WHERE {$condGroup} 
            {$andQuery}
            AND c.id_in_charge = d.idperson 
            AND a.idstatus = b.idstatus 
            AND a.code_request = c.code_request 
            AND a.idperson_owner = e.idperson 
            AND b.idstatus = a.idstatus 
            AND b.idstatus_source = 1) AS t3,
            (SELECT 
            COUNT(DISTINCT a.code_request) AS finished 
            FROM
            (
              hdk_tbrequest a,
              hdk_tbstatus b,
              hdk_tbrequest_in_charge c,
              tbperson d,
              tbperson e
            ) 
            WHERE {$condGroup} 
            {$andQuery}
            AND c.id_in_charge = d.idperson 
            AND a.idstatus = b.idstatus 
            AND a.code_request = c.code_request 
            AND a.idperson_owner = e.idperson 
            AND b.idstatus = a.idstatus 
            AND b.idstatus_source = 5) AS t4,
            (SELECT 
            COUNT(DISTINCT a.code_request) AS waiting_approval 
            FROM
            (
              hdk_tbrequest a,
              hdk_tbstatus b,
              hdk_tbrequest_in_charge c,
              tbperson d,
              tbperson e
            ) 
            WHERE {$condGroup}
            {$andQuery}
            AND c.id_in_charge = d.idperson 
            AND a.idstatus = b.idstatus 
            AND a.code_request = c.code_request 
            AND a.idperson_owner = e.idperson 
            AND b.idstatus = a.idstatus 
            AND b.idstatus_source = 4) AS t5,
            (SELECT 
            FORMAT(COUNT(DISTINCT a.code_request),0,'{$locale}') AS total_requests 
            FROM
            hdk_tbrequest a,
            hdk_tbstatus b,
            hdk_tbrequest_in_charge c 
            WHERE a.idstatus = b.idstatus 
            {$andQuery}
            AND c.ind_in_charge = 1 
            AND c.code_request = a.code_request 
            AND b.idstatus_source <= 5) AS t6         
      
          ";
    
    
      $ret = $this->db->Execute($sql);
      
      if ($this->db->ErrorNo() != 0) {
        $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
        return false ;
      }
    
      return $ret;
    }

    public function getUserRequestStats($idPerson, $andQuery = "",$locale = "en_US")
    {
        $sql =  "
                SELECT 
                  t1.requests,
                  t2.in_atendance,
                  t3.new_requests ,
                  t4.finished,
                  t5.waiting_approval,
                  t6.total_requests      
                FROM
                  (SELECT 
                    FORMAT(COUNT(DISTINCT a.code_request),0,'{$locale}') AS requests 
                  FROM
                    hdk_tbrequest a,
                    hdk_tbstatus b,
                    hdk_tbrequest_in_charge c 
                  WHERE a.idstatus = b.idstatus 
                    {$andQuery}
                    AND a.idperson_owner = {$idPerson} 
                    AND c.ind_in_charge = 1 
                    AND c.code_request = a.code_request) AS t1,
                  (SELECT 
                    COUNT(DISTINCT a.code_request) AS in_atendance 
                  FROM
                    hdk_tbrequest a,
                    hdk_tbstatus b,
                    hdk_tbrequest_in_charge c 
                  WHERE a.idstatus = b.idstatus 
                    {$andQuery}
                    AND b.idstatus_source IN (2,3) 
                    AND a.idperson_owner = {$idPerson} 
                    AND c.ind_in_charge = 1 
                    AND c.code_request = a.code_request) AS t2,
                  (SELECT 
                    COUNT(DISTINCT a.code_request) AS new_requests 
                  FROM
                    hdk_tbrequest a,
                    hdk_tbstatus b,
                    hdk_tbrequest_in_charge c 
                  WHERE a.idstatus = b.idstatus 
                    {$andQuery}
                    AND b.idstatus_source = 1 
                    AND a.idperson_owner = {$idPerson} 
                    AND c.ind_in_charge = 1 
                    AND c.code_request = a.code_request) AS t3,
                  (SELECT 
                    COUNT(DISTINCT a.code_request) AS finished 
                  FROM
                    hdk_tbrequest a,
                    hdk_tbstatus b,
                    hdk_tbrequest_in_charge c 
                  WHERE a.idstatus = b.idstatus 
                    {$andQuery}
                    AND b.idstatus_source = 5 
                    AND a.idperson_owner = {$idPerson} 
                    AND c.ind_in_charge = 1 
                    AND c.code_request = a.code_request) AS t4,
                  (SELECT 
                    COUNT(DISTINCT a.code_request) AS waiting_approval 
                  FROM
                    hdk_tbrequest a,
                    hdk_tbstatus b,
                    hdk_tbrequest_in_charge c 
                  WHERE a.idstatus = b.idstatus 
                    {$andQuery}
                    AND b.idstatus_source = 4 
                    AND a.idperson_owner = {$idPerson} 
                    AND c.ind_in_charge = 1 
                    AND c.code_request = a.code_request) AS t5,
                (SELECT 
                    FORMAT(COUNT(DISTINCT a.code_request),0,'{$locale}') AS total_requests 
                  FROM
                    hdk_tbrequest a,
                    hdk_tbstatus b,
                    hdk_tbrequest_in_charge c 
                  WHERE a.idstatus = b.idstatus 
                    {$andQuery}
                    AND c.ind_in_charge = 1 
                    AND c.code_request = a.code_request) AS t6                        
                             
                ";

        $ret = $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        }

        return $ret;
    }
    public function getRequestsCount($idperson,$idstatus){
        $ret = $this->select("SELECT COUNT(distinct a.code_request) as count
								FROM hdk_tbrequest a, hdk_tbstatus b, hdk_tbrequest_in_charge c
								WHERE a.idstatus = b.idstatus
								AND b.idstatus_source = $idstatus
								AND a.idperson_owner = $idperson
								and c.ind_in_charge = 1
								AND c.code_request = a.code_request");
        return $ret->fields['count'];
    }

    public function getTotalRequestsByPerson($idperson){
        $ret = $this->select("SELECT COUNT(distinct a.code_request) as count
								FROM hdk_tbrequest a, hdk_tbstatus b, hdk_tbrequest_in_charge c
								WHERE a.idstatus = b.idstatus
								AND a.idperson_owner = $idperson
								and c.ind_in_charge = 1
								AND c.code_request = a.code_request");
        return $ret->fields['count'];
    }

    public function getUserPhoto($iduser){
        $query = "SELECT idpersonphoto, filename FROM tbpersonphoto WHERE idperson = $iduser";

        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: " . $query;
            $this->error($sError);
            return false;
        }

        return $ret;
    }

    public function saveUserPhoto($iduser,$fileName){
        $query = "INSERT INTO tbpersonphoto (idperson,filename) VALUES ($iduser,'$fileName')";

        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: " . $query;
            $this->error($sError);
            return false;
        }

        return $this->db->Insert_ID();
    }

    public function checkUserPass($idperson,$newpass){
        $query = "SELECT idperson, password
                    FROM tbperson 
                   WHERE idperson = $idperson 
                     AND password = '$newpass'";
        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query);
            return false ;
        }
        return $ret;
    }

}