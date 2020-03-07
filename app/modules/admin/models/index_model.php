<?php

if(class_exists('Model')) {
    class DynamicIndex_model extends Model {}
} elseif(class_exists('cronModel')) {
    class DynamicIndex_model extends cronModel {}
} elseif(class_exists('apiModel')) {
    class DynamicIndex_model extends apiModel {}
}

class index_model extends DynamicIndex_model {
//class index_model extends Model {

    public function selectDataLogin($F_LOGIN, $F_SENHA) {
		$database = $this->getConfig('db_connect');
		if ($this->isMysql($database)) {
			$password = $F_SENHA;
		} elseif ($database == 'oci8po') {
			$password = strtoupper($F_SENHA) ;
		}
        $ret = $this->select("SELECT idperson
			FROM tbperson
			WHERE
			(login = '" . $F_LOGIN . "'  AND (password = '" . $password . "' OR password IS NULL)and status = 'A')
			");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }

        return $ret->fields['idperson'];

    }



    public function checkUser($F_LOGIN) {
        $des_login = $this->select("SELECT login, status from tbperson where login = '$F_LOGIN'");
        if ($des_login->fields) {
            if ($des_login->fields['status'] == "A") {
            	return "A";
            } else {
            	return "I";
            }
        } 
    }

    public function selectDataSession($id) {
        $ret = $this->select("select
          person.idtypeperson as idtypeperson,
          person.name         as name,
          person.login        as login,
          juridical.idperson  as idjuridical,
          juridical.name as company
        from tbperson  person,
          tbperson  juridical,
          hdk_tbdepartment_has_person  rela,
          hdk_tbdepartment  dep
        where person.idperson = '$id'
            and person.idperson = rela.idperson
            AND juridical.idperson = dep.idperson
            AND dep.iddepartment = rela.iddepartment");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function selectTypePerson($idperson) {
		$database = $this->getConfig('db_connect');
		if ($this->isMysql($database)) {
			$ret = $this->db->Execute( 	"
										select
										  tp.name, tp.idtypeperson
										from tbtypeperson as tp,
										  tbperson as per,
										  tbtypepersonpermission as p,
										  tbprogram as pr,
										  tbprogramcategory as cat,
										  tbmodule as m
										where per.idtypeperson = tp.idtypeperson
											and pr.idprogramcategory = cat.idprogramcategory
											and cat.idmodule = m.idmodule
											and p.idprogram = pr.idprogram
											AND per.idtypeperson = p.idtypeperson
											and per.idperson = '$idperson'
										limit 1
										");
		} elseif ($database == 'oci8po') {
			$ret = $this->db->Execute( 	"
											select
											  tp.name, tp.idtypeperson
											from tbtypeperson  tp,
											  tbperson  per,
											  tbtypepersonpermission  p,
											  tbprogram  pr,
											  tbprogramcategory  cat,
											  tbmodule  m
											where per.idtypeperson = tp.idtypeperson
												and pr.idprogramcategory = cat.idprogramcategory
												and cat.idmodule = m.idmodule
												and p.idprogram = pr.idprogram
												AND per.idtypeperson = p.idtypeperson
												and per.idperson = $idperson
												and rownum = 1
											"
											);
		}	

        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
		
        return $ret;
    }

    public function selectPersonGroups($idperson) {

        $ret = $this->db->Execute("SELECT pers.name as personname,
                                        pers.idperson,
                                        pers.name as groupname,
                                        grp.idgroup
                                        from
                                        hdk_tbgroup as grp,
                                        tbperson as pers,
                                        hdk_tbgroup_has_person as relat
                                        where
                                        grp.idgroup = relat.idgroup
                                        and pers.idperson = relat.idperson
                                        and pers.idperson = '$idperson'
                                        order by grp.idgroup");

        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function getModules()
    {

    }

    public function getConfigDataByModule($abbrModule){
        $tableName = $abbrModule . '_tbconfig';
        if ( $this->tableExists($tableName) == 0 )
            return false;
        $ret = $this->select("select session_name, value from $tableName");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }



    public function getConfigGlobalData(){

        $sql = "select session_name, value from tbconfig" ;

        $ret = $this->select($sql);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql );
        else
            return $ret;
    }

    public function getConfigData(){
        $ret = $this->select("select session_name, value from hdk_tbconfig");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function selectAllGroups(){
        $ret = $this->select(	"
								select
								   pers.idperson,
								   pers.name      groupname,
								   grp.idgroup
								from hdk_tbgroup  grp,
								   tbperson 	 pers
								where pers.idperson = grp.idperson
								order by grp.idgroup		
								"
							);
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }
 
    /**
     * returns the login type of the person 
     *
     * @param  string $login       Access login
     * @return object              Login type of the person 
     */      
    public function getTypeLogin($login)
    {
        $ret = $this->select("select idtypelogin from tbperson where login = '$login'");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    /**
     * returns the Id of the person 
     *
     * @param  string $login       Access login
     * @return int                 Id of the person 
     */             
    public function getIdPerson($login)
    {
        $ret =  $this->select("select idperson from tbperson where login = '$login'");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return  $ret->fields['idperson'];
    }    

    /**
     * returns the e-mail of the person 
     *
     * @param  string $login       Access login
     * @return string              E-mail of the person 
	 *
     */             
    public function getEmailPerson($login)
    {
        $ret =  $this->select("select email from tbperson where login = '$login'");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return  $ret->fields['email'];
    }

    /**
     * returns the number of request by person
     *
     * @param  int     $idperson   Person Id
     * @return int                 Numbers of requests
     *
     */
    public function getRequestsByPerson($idperson)
    {
        $ret =  $this->select("SELECT COUNT(*) AS amount FROM  hdk_tbrequest WHERE idperson_creator =  '$idperson'");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return  $ret->fields['amount'];
    }

    /**
     * checks if the user is  request's creator
     *
     * @param  int     $idperson        Person Id
     * @param  int     $code_request    Request Id
     * @return int                      Numbers of requests
     *
     */
    public function checkPersonRequest($idperson,$code_request)
    {
        $ret =  $this->select("SELECT COUNT(*) as amount FROM hdk_tbrequest WHERE code_request = '$code_request' AND idperson_creator = '$idperson'");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return  $ret->fields['amount'];
    }

    /**
     * returns the Id of the person's department
     *
     * @param  string $login       Access login
     * @return int                 Id of the person's department
     * @since Version 1.2
     * @author Rogerio Albandes <rogerio.albandes@helpdezk.cc>
     */
    public function getIdPersonDepartment($login)
    {
        $ret =  $this->select("
                                SELECT
                                  b.iddepartment
                                FROM
                                  tbperson a,
                                  hdk_tbdepartment_has_person b
                                WHERE a.login = '$login'
                                  AND a.idperson = b.idperson
                            ");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return  $ret->fields['iddepartment'];
    }


    /**
     * returns the value of the config in hdk_tbconfig
     * use when session isn´t seted
     *
     * @param  string $name        Session´s name
     * @return string              Value of session
     * @since Version 1.2
     * @author Rogerio Albandes <rogerio.albandes@helpdezk.cc>
     */

    public function getConfigValue($name){
        $ret = $this->select("select value from hdk_tbconfig where session_name = '$name' ");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret->fields['value'];
    }

    // Use by API
    // Since May 16, 2017
    public function setToken($id,$token)
    {
        $ret =  $this->select("UPDATE tbperson SET token='$token' WHERE idperson = $id");
        return  $ret;
    }

    // Use by API
    // Since May 16, 2017
    public function getUserIdByToken($token)
    {
        $ret =  $this->select("SELECT idperson FROM tbperson WHERE token='$token'");
        return  $ret->fields['idperson'];
    }

    // Use by API
    // Since May 17, 2017
    public function getLoginByToken($token)
    {
        $ret =  $this->select("SELECT login FROM tbperson WHERE token='$token'");
        return  $ret->fields['login'];
    }

}

?>
