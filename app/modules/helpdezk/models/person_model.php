<?php
class person_model extends Model {

    public $database;

    public function __construct(){
        parent::__construct();
        $this->database = $this->getConfig('db_connect');
    }

    public function selectPerson($where = NULL, $order = NULL, $limit = NULL){
       $ret = $this->select("SELECT tbp.idperson, tbp.name, tbp.login, tbp.email, tbp.status, tbtp.name as typeperson  from tbperson as tbp, tbtypeperson as tbtp where tbp.idtypeperson = tbtp.idtypeperson $where $order $limit");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
            return false;
        }
        return $ret;
    }
    public function selectPersonData($id){
        $ret = $this->select("SELECT
                              tbp.idperson,
                              tbp.name,
                              tbp.login,
                              tbp.email,
                              tbp.status,
                              dep.name as department,
                              comp.name as company,
                              tbtp.name    as typeperson,
                              tbp.phone_number,
                              tbp.cel_phone,
                              tbp.branch_number,
                              tbp.token
                            from tbperson tbp,
                              tbtypeperson tbtp,
                              hdk_tbdepartment_has_person deprelat,
                              hdk_tbdepartment dep,
                              tbperson comp
                            where tbp.idtypeperson = tbtp.idtypeperson
                                AND comp.idperson = dep.idperson
                                AND deprelat.idperson = tbp.idperson
                                AND dep.iddepartment = deprelat.iddepartment
                                            AND tbp.idperson = '$id'");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
            return false;
        }
        return $ret;
    }
    public function countPerson($where2 = NULL, $order = NULL, $limit = NULL){
        $sel = $this->select("SELECT count(IDPERSON) as total from tbperson $where2 $order $limit");
        if(!$set) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
            return false;
        }
        return $sel;
    }
    public function deletePerson($where){
        $ret = $this->delete('tbperson', $where);
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
            return false;
        }
        return $ret;
    }
    public function getTypePerson($where = NULL){
        $ret = $this->select("select idtypeperson, name from tbtypeperson $where");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
            return false;
        }
        return $ret;
    }
    public function getCountry(){
        $ret = $this->select("select idcountry, printablename from tbcountry");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
            return false;
        }
        return $ret;
    }
    public function getPersonFromType($COD_TIPO){
        $ret = $this->select("	SELECT
								tbp.idperson      as idperson,
								tbp.name          as person,
								dep.iddepartment  as iddepartment,
								dep.name          as department
								from (tbperson as tbp,
								tbtypeperson as tbtp)
								left join hdk_tbdepartment_has_person as depP
								on (tbp.idperson = depP.idperson)
								left join hdk_tbdepartment as dep
								on (depP.iddepartment = dep.iddepartment)
								left join tbperson as comp
								on (dep.idperson = comp.idperson)
								where tbp.idtypeperson = tbtp.idtypeperson
								and tbp.idperson != 1
								and tbp.idtypeperson in ($COD_TIPO)
								ORDER BY tbp.name");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
            return false;
        }
        return $ret;
    }
    public function getJuridical(){
        $ret = $this->select("SELECT  idperson as idjuridical, name FROM tbperson WHERE idnatureperson=2 ORDER BY idperson");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
            return false;
        }
        return $ret;
    }
    public function getDepartment(){
        $ret = $this->select("SELECT iddepartment, name from hdk_tbdepartment");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
            return false;
        }
        return $ret;
    }
    public function getTypeStreet(){
        $ret = $this->select("SELECT idtypestreet, name from tbtypestreet");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
            return false;
        }
        return $ret;
    }
	
	/**
	* Returns object containing people�s id and name who are not, or are as ind_in_charge and ind_operator_aux,
    * depending on the parameter of the condition	 	
	*
	* @access public
	* @param int $code_request Request Id.
	* @param string $in_notin Condition Parameter.
	* @return array Person's Information
	*/	
	public function getOperatorAuxCombo($code_request, $in_notin) 
	{
        $sql = ($this->database == 'oci8po') ? "select
				   a.idperson,
				   ltrim(a.name) as name
				from tbperson a
				where a.idperson " : "select
				   a.idperson,
				   ltrim(a.name) as `name`
				from tbperson a
				where a.idperson ";

		
		if	($in_notin == 'in' ) {
			$sql .= "in" ;
		} elseif ($in_notin == 'not') {
			$sql .= "not in";
		}	
		$sql .=							"	(
										select
										   a.id_in_charge
										from hdk_tbrequest_in_charge a
										where a.code_request = '$code_request'

											 and a.ind_operator_aux = 1
											 and a.type = 'P'
										)
					 and a.idtypeperson IN (1,3)
					 and a.status = 'A'
				order by a.name asc
				";

        //die($sql);
		return $this->select($sql);		
	}
	
    public function selectstate($where = NULL){
        $ret = $this->select("select idstate, name from tbstate $where");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
            return false;
        }
        return $ret;
    }
    public function selectCity($where = NULL){
        $ret = $this->select("select idcity, name from tbcity $where");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
            return false;
        }
        return $ret;
    }
    public function selectNeighborhood($where = NULL){
        $ret = $this->select("select idneighborhood, name from tbneighborhood $where");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
            return false;
        }
        return $ret;
    }
    
    public function getStreets($where=NULL){
        $ret = $this->select("select idstreet, name from tbstreet $where");
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
            return false;
        }
        return $ret;
    }
    
    public function selectPersonName($id){
       $ret = $this->select("select name from tbperson where idperson = '$id'"); 
       if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
       }
       return $ret->fields['name'];
    }

    public function getIdTypePerson($id) {
        $ret = $this->select("select idtypeperson from tbperson where idperson = $id");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
        }
        return $ret->fields['idtypeperson'];
    }

    public function updateToken($idperson,$token)
    {
        $qry =  "
                UPDATE
                  tbperson a
                SET
                  a.token = '$token'
                WHERE a.idperson = '$idperson'
                ";

        return $this->db->Execute($qry);
    }
}
?>
