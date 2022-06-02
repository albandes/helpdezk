<?php

if(class_exists('Model')) {
    class DynamicITMMac_model extends Model {}
} elseif(class_exists('cronModel')) {
    class DynamicITMMac_model extends cronModel {}
} elseif(class_exists('apiModel')) {
    class DynamicITMMac_model extends apiModel {}
}

class mac_model extends DynamicITMMac_model
{


    public $database;

    public function __construct()
    {
        parent::__construct();
        $this->database = $this->getConfig('db_connect');
    }

    public function selectHost($where, $order, $limit){
        if ($this->database == 'mysqli') {
            $qry =  "
                    SELECT idhost, b.name netusertype, a.name hostname, mac, ip, description, host_status
                      FROM itm_tbhost a, itm_tbnetusertype b
                     WHERE a.idnetusertype = b.idnetusertype
                    $where $order $limit
            ";

        }
        //echo($qry);
        $ret = $this->db->Execute($qry) ;
        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg() . "<BR>Query: " . $qry;
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function countHost($where){
        $qry =  "
                    SELECT COUNT(idhost) as total
                      FROM itm_tbhost a, itm_tbnetusertype b
                     WHERE a.idnetusertype = b.idnetusertype
                      $where
            ";
        //die($qry);
        $ret = $this->db->Execute($qry) ;
        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg() . "<BR>Query: " . $qry;
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function getNetUserType($where=null){
        if ($this->database == 'mysqli') {
            $qry =  "SELECT idnetusertype,`name` FROM itm_tbnetusertype $where ORDER BY `name`";
        }
        //die($qry);
        $ret = $this->db->Execute($qry) ;
        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg() . "<BR>Query: " . $qry;
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function getHostData($fields,$table,$where,$order=''){
        $qry =  "SELECT $fields FROM $table WHERE $where $order";
        //echo($qry);
        $ret = $this->db->Execute($qry) ;
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg() . "<BR>Query: " . $qry;
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    /**
     * Insert Host data (pc or mobile device) in table itm_tbhost
     *
     * @access public
     * @param string $host_name - Host name
     * @param string $mac_number - Host MAC address
     * @param string $ip IP address assign to host
     * @param string $ip_aton - IP address in numeric format
     * @param string $description - Host description
     * @param int $idnetusertype - Host type ID
     * @param int $idnetuser - Host owner ID (Internal users only)
     * @param datetime $timedeactivate - Time to deactivate host
     * @return object
     **/
    public function insertHost($host_name,$mac_number,$ip,$ip_aton,$description,$idnetusertype,$idnetuser,$timedeactivate){
        $qry = "INSERT INTO itm_tbhost(name,mac,ip,ip_aton,description,idnetusertype,idnetuser,timedeactivate,insert_date)
                VALUES('$host_name','$mac_number','$ip','$ip_aton','$description',$idnetusertype,'$idnetuser','$timedeactivate',NOW())";

        //echo($qry);
        $ret = $this->db->Execute($qry) ;
        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg() . "<BR>Query: " . $qry;
            $this->error($sError);
            return false;
        }

        return $this->db->insert_Id();

    }

    /**
     * Insert Host data (pc or mobile device) in table itm_tbcheck
     *
     * @access public
     * @param string $host_name - Host name
     * @param string $mac_number - Host MAC address
     * @param string $description - Host description
     * @param int $idnetusertype - Host type ID
     * @param string $idnetuser - Host owner ID (Internal users only)
     * @return object
     **/
    public function insertRadCheck($host_name,$mac_number,$description,$idnetusertype,$idnetuser){

        $qry = "INSERT INTO itm_tbcheck (username,name,idnetusertype,idnetuser,description)
                VALUES('$mac_number','$host_name','$idnetusertype','$idnetuser','$description')";

        //echo($qry);
        $ret = $this->db->Execute($qry) ;
        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg() . "<BR>Query: " . $qry;
            $this->error($sError);
            return false;
        }

        return $ret;

    }

    /**
     * Insert Host data (pc or mobile device) in table itm_tbreply
     *
     * @access public
     * @param string $mac_number - Host MAC address
     * @param string $attribute
     * @param string $attrvalue
     * @return object
     **/
    public function insertRadReply($mac_number,$attribute,$attrvalue){
        $qry = "INSERT INTO itm_tbreply (username,attribute,op,value)
                     VALUES('$mac_number','$attribute','=','$attrvalue')";

        //echo($qry);
        $ret = $this->db->Execute($qry) ;
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg() . "<BR>Query: " . $qry;
            $this->error($sError);
            return false;
        }

        return $ret;

    }

    /**
     * Update Host data (pc or mobile device) in table itm_tbhost
     *
     * @access public
     * @param string $host_name - Nome do host
     * @param string $mac_number - Endereço MAC do host
     * @param string $description - Descrição do Host
     * @param int $idnetusertype - ID do tipo do host (Aluno, Pai/Mãe, Funcionário/Professor, etc)
     * @param string $idnetuser - ID do aluno (caso tipos 'Aluno' e 'Pai/Mãe') ou Email para demais tipos
     * @param int $idhost - ID do host na tabela it_tbhost
     * @return object
     **/
    public function updateHost($host_name,$mac_number,$description,$idnetusertype,$idnetuser,$timedeactivate,$idhost){
        $qry = "UPDATE itm_tbhost 
                   SET `name` = '$host_name', 
                        mac = '$mac_number' , 
                        description = '$description',
                        idnetusertype = $idnetusertype, 
                        idnetuser = '$idnetuser',
                        timedeactivate = '$timedeactivate'
                WHERE idhost = $idhost";

        //echo($qry);
        $ret = $this->db->Execute($qry) ;
        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg() . "<BR>Query: " . $qry;
            $this->error($sError);
            return false;
        }

        return $ret;

    }

    /**
     * Update Host data (pc or mobile device) in table rad_tbcheck
     *
     * @access public
     * @param string $host_name - Nome do Host
     * @param string $mac_number - Endereço MAC do host
     * @param string $description - Descrição do Host
     * @param int $idnetusertype - ID do tipo do host (Aluno, Pai/Mãe, Funcionário/Professor, etc)
     * @param string $idnetuser - ID do aluno (caso tipos 'Aluno' e 'Pai/Mãe') ou Email para demais tipos
     * @param string $idrad - ID do host na tabela rad_tbcheck
     * @return object
     **/
    public function updateRadCheck($host_name,$mac_number,$description,$idnetusertype,$idnetuser,$idrad){

        $qry = "UPDATE itm_tbcheck 
                   SET username = '$mac_number', 
                         `name` = '$host_name', 
                  idnetusertype = '$idnetusertype',
                      idnetuser = '$idnetuser',
                    description = '$description'
                WHERE username = '$idrad'";

        //echo($qry);
        $ret = $this->db->Execute($qry) ;
        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg() . "<BR>Query: " . $qry;
            $this->error($sError);
            return false;
        }

        return $ret;

    }

    /**
     * Update Host data (pc or mobile device) in table itm_tbreply
     *
     * @access public
     * @param string $mac_number - Endereço MAC do host
     * @param string $idrad - ID do host na tabela rad_tbreply
     * @return object
     **/
    public function updateRadReply($mac_number,$idrad){
        $qry = "UPDATE itm_tbreply 
                   SET username = '$mac_number' 
                 WHERE username = '$idrad'";

        //echo($qry);
        $ret = $this->db->Execute($qry) ;
        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg() . "<BR>Query: " . $qry;
            $this->error($sError);
            return false;
        }

        return $ret;

    }

    public function getHostVerification($where){
        $qry =  "SELECT idhost, name, host_status FROM itm_tbhost WHERE $where";
        //echo($qry);
        $ret = $this->db->Execute($qry) ;
        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg() . "<BR>Query: " . $qry;
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    /**
     * Disable/Enables Host (pc or mobile device) in table itm_tbhost
     *
     * @access public
     * @param string $ip IP address assigned to host
     * @param string $ip_aton - IP address in numeric format
     * @param string $status - Host Status (Active or Deactive)
     * @param int $idhost - Host ID
     * @return object
     **/
    public function setStatusHost($ip,$ip_aton,$status,$idhost){
        $qry = "UPDATE itm_tbhost 
                   SET ip = '$ip', 
                        ip_aton = '$ip_aton' , 
                        host_status = '$status'
                WHERE idhost = $idhost";

        //echo($qry);
        $ret = $this->db->Execute($qry) ;
        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg() . "<BR>Query: " . $qry;
            $this->error($sError);
            return false;
        }

        return $ret;

    }

    /**
     * Disable/Enables Host (pc or mobile device) in tables itm_tbcheck / itm_tbreply
     *
     * @access public
     * @param string $ip IP address assigned to host
     * @param string $ip_aton - IP address in numeric format
     * @param string $status - Host Status (Active or Deactive)
     * @param int $idhost - Host ID
     * @return object
     **/
    public function deaRad($fields,$table,$where){
        $qry = "UPDATE $table SET $fields WHERE $where";

        //echo($qry);
        $ret = $this->db->Execute($qry) ;
        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg() . "<BR>Query: " . $qry;
            $this->error($sError);
            return false;
        }

        return $ret;

    }

    public function getRateLimit($mac){
        $qry = "SELECT value FROM itm_tbreply WHERE username = '$mac' AND attribute = 'Mikrotik-Rate-Limit'";

        //echo($qry);
        $ret = $this->db->Execute($qry) ;
        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg() . "<BR>Query: " . $qry;
            $this->error($sError);
            return false;
        }

        return $ret;

    }

    public function updateRateLimit($attrvalue,$idrad){
        $qry = "UPDATE itm_tbreply 
                   SET value = '$attrvalue' 
                 WHERE username = '$idrad'
                   AND attribute = 'Mikrotik-Rate-Limit'";

        //echo($qry);
        $ret = $this->db->Execute($qry) ;
        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg() . "<BR>Query: " . $qry;
            $this->error($sError);
            return false;
        }

        return $ret;

    }

    public function deleteRateLimit($idrad){
        $qry = "DELETE FROM itm_tbreply WHERE username = '$idrad' AND attribute = 'Mikrotik-Rate-Limit'";

        //echo($qry);
        $ret = $this->db->Execute($qry) ;
        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg() . "<BR>Query: " . $qry;
            $this->error($sError);
            return false;
        }

        return $ret;

    }

    public function insertExternalUser($nameExternal,$cpfExternal,$cardIdExternal){
        $qry = "INSERT INTO itm_tbexternal_user(`name`,cpf,id_card)
                VALUES('$nameExternal','$cpfExternal','$cardIdExternal')";

        //echo($qry);
        $ret = $this->db->Execute($qry) ;
        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg() . "<BR>Query: " . $qry;
            $this->error($sError);
            return false;
        }

        return $this->db->insert_Id();

    }

    public function getExternalUsers($where=null,$order=null){
        $qry =  "SELECT * FROM itm_tbexternal_user $where $order";
        //echo($qry);
        $ret = $this->db->Execute($qry) ;
        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg() . "<BR>Query: " . $qry;
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function getNetUserByName($where=null,$order=null)
    {
        $sql =  "SELECT a.idperson_profile id, `name`, cpf, b.id_card, birth_date, 
                        DATE_FORMAT(birth_date,'%d/%m/%Y') fmt_dtbirth, c.description gender, c.abbrev gender_abbr 
                   FROM acd_tbstudent a, tbperson_profile b, hur_tbgender c
                  WHERE a.idperson_profile = b.idperson_profile
                    AND b.idgender = c.idgender
                    AND b.name $where
                  UNION
                 SELECT a.idperson_profile id, `name`, cpf, b.id_card, birth_date, 
                        DATE_FORMAT(birth_date,'%d/%m/%Y') fmt_dtbirth, c.description gender, c.abbrev gender_abbr 
                   FROM acd_tbparent a, tbperson_profile b, hur_tbgender c
                  WHERE a.idperson_profile = b.idperson_profile
                    AND b.idgender = c.idgender
                    AND b.name $where
                  UNION
                 SELECT cpf id,  nome `name`, cpf, identidade id_card, dtnasc birth_date, 
                        DATE_FORMAT(dtnasc,'%d/%m/%Y') fmt_dtbirth, b.description gender, sexo gender_abbr 
                   FROM hur_tbfuncionario a, hur_tbgender b
                  WHERE a.sexo = b.abbrev
                    AND a.nome $where
                  UNION
                 SELECT idexternal_user id, `name`, cpf, id_card, '' birth_date, 
                        DATE_FORMAT('','%d/%m/%Y') fmt_dtbirth, '' gender, '' gender_abbr 
                   FROM itm_tbexternal_user a
                  WHERE a.name $where
                  $order";

        $ret = $this->db->Execute($sql); //echo $sql.'<br>';

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            return $ret;
        }

    }

}