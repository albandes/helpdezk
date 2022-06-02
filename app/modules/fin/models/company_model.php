<?php
class company_model extends Model
{
    public $database;

    public function __construct()
    {
        parent::__construct();
        $this->database = $this->getConfig('db_connect');

    }

    public function changePassword($id, $password, $change_pass = 0){
        $database = $this->getConfig('db_connect');
        if ($this->isMysql($database)) {
            return $this->db->Execute("update tbperson set password = '$password', change_pass = '$change_pass' where idperson = '$id'");
        } elseif ($database == 'oci8po') {
            return $this->db->Execute("update tbperson set password = UPPER('$password'), change_pass = '$change_pass' where idperson = '$id'");
        }
    }

    public function getErpCompanies($where = null,$order = null)
    {
        $query = "SELECT  idperson as idcompany, name FROM tbperson $where $order";
        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query);
            return false ;
        }

        return $ret;
    }

    public function getCompanyLegacyID($idcompany)
    {
        $query = "SELECT idperseus,iddominio,history_code, ein_cnpj, course_condition 
                    FROM fin_tbcompany_has_legacy a, tbjuridicalperson b
                    WHERE a.idperson = b.idperson 
                      AND a.idperson = $idcompany";
        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0) {
            return $this->makeErrorMessage( __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        }

        return $ret;
    }

    function makeErrorMessage($line,$method,$error,$query='')
    {
        $aRet = array(
            "status" => 'Error',
            "message" => "[DB Error] method: " . $method . ", line: " . $line . ", Db message: " . $error . ", Query: " . $query
        );
        return $aRet;
    }

    public function getDominioUserID($iduser)
    {
        $query = "SELECT dominiouser FROM fin_tbdominiouser WHERE idperson = $iduser";
        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0) {
            return $this->makeErrorMessage( __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        }

        return $ret;
    }

    public function getCompanyStudents($where=null,$group=null,$order=null,$limit=null)
    {
        $query = "SELECT a.idstudent, LOWER(pipeLatinToUtf8(`name`)) `name`,
                         b.idperseus, b.idintranet,
                         pipeLatinToUtf8(c.abrev) class
                    FROM acd_tbenrollment a, acd_tbstudent b, acd_tbturma c, tbperson_profile d,
                        acd_tbserie e
                    WHERE a.idstudent = b.idstudent
                    AND a.idturma = c.idturma
                    AND b.idperson_profile = d.idperson_profile
                    AND c.idserie = e.idserie
                  $where $group $order $limit ";
        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0) {
            return $this->makeErrorMessage( __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        }

        return $ret;
    }


    public function getErpCompanyData($where=null,$group=null,$order=null,$limit=null)
    {
        $query = "SELECT a.idperson idcompany, a.`name` company_name, c.`name` street, b.`number`,
                         d.`name` neighborhood, e.`name` city, f.abbr uf
                    FROM tbperson a, tbaddress b, tbstreet c, tbneighborhood d, tbcity e, tbstate f
                   WHERE a.idperson = b.idperson
                     AND b.idstreet = c.idstreet
                     AND b.idneighborhood = d.idneighborhood
                     AND b.idcity = e.idcity
                     AND e.idstate = f.idstate 
                    $where $group $order $limit";
        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query);
            return false ;
        }

        return $ret;
    }


}
