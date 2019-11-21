<?php


if(class_exists('Model')) {
    class DynamicPerson_model extends Model {}
} elseif(class_exists('cronModel')) {
    class DynamicPerson_model extends cronModel {}
} elseif(class_exists('apiModel')) {
    class DynamicPerson_model extends apiModel {}
}





class person_model extends DynamicPerson_model
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
            $query = "update tbperson set password = '$password', change_pass = '$change_pass' where idperson = '$id'";
        } elseif ($database == 'oci8po') {
            $query = "update tbperson set password = UPPER('$password'), change_pass = '$change_pass' where idperson = '$id'";
        }

        $ret = $this->select($query);
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg() . "<br>Query: " . $query    ;
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function selectPerson($where = NULL, $order = NULL, $limit = NULL) {

        $sql =               "
							select
								  tbp.idperson,
								  tbp.name,
								  tbp.login,
								  tbp.email,
								  tbp.status,
								  tbp.user_vip,
								  tbp.phone_number as telephone,
								  tbp.branch_number,
								  tbp.cel_phone as cellphone,
								  tbtp.name         as typeperson,
								  tbtp.idtypeperson,
								  ctry.printablename as country,
								  ctry.idcountry,
								  stt.name as state,
								  stt.abbr as state_abbr,
								  stt.idstate,
								  nbh.name as neighborhood,
								  nbh.idneighborhood,
								  ct.name as city,
								  ct.idcity,
								  tpstr.name as typestreet,
								  tpstr.idtypestreet,
								  st.name as street,
								  addr.number,
								  addr.complement,
								  addr.zipcode,
								  pipeMask (addr.zipcode, '#####-###') AS zipcode_fmt,
								  nat.ssn_cpf,
								  pipeMask(nat.ssn_cpf,'###.###.###-##') AS cpf_fmt,
  								  pipeMask(nat.ssn_cpf,'###-##-####') AS ssn_fmt,
								  nat.rg,
								  nat.rgoexp,
								  nat.dtbirth,
								  nat.mother,
								  nat.father,
								  nat.gender,
								  a.iddepartment,
                                  b.name AS department,
                                  (SELECT `name` FROM tbperson WHERE idperson = b.idperson ) AS company,
	                              b.idperson idcompany,
	                              tbp.idtypelogin,
	                              DATE_FORMAT(nat.dtbirth,'%d/%m/%Y') AS dtbirth_fmt,
	                              addr.idstreet
                            from tbperson tbp,
								  tbtypeperson tbtp,
								  tbaddress addr,
								  tbcity ct,
								  tbcountry ctry,
								  tbstate stt,
								  tbstreet st,
								  tbneighborhood nbh,
								  tbtypeaddress tpad,
								  tbtypestreet tpstr,
								  tbnaturalperson nat,
								  hdk_tbdepartment_has_person a,
                                  hdk_tbdepartment b
                            where tbp.idtypeperson = tbtp.idtypeperson
                                  AND a.idperson = tbp.idperson
                                  AND a.iddepartment = b.iddepartment
                                  AND tbp.idperson = nat.idperson
                                  AND addr.idperson = tbp.idperson
                                  AND addr.idcity = ct.idcity
                                  AND addr.idneighborhood = nbh.idneighborhood
                                  AND addr.idstreet = st.idstreet
                                  AND addr.idtypeaddress = tpad.idtypeaddress
                                  AND st.idtypestreet = tpstr.idtypestreet
                                  AND ct.idstate = stt.idstate
                                  AND stt.idcountry = ctry.idcountry
                                  $where $order $limit";

        $ret = $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql );
        else
            return $ret;
    }

    public function updatePersonUser($idPerson, $name, $email, $phone, $branch, $mobile, $location=null)
    {
        //if(empty($location)) $location = 'NULL';

        $sql = "update tbperson set name = '$name', email = '$email', phone_number = '$phone', branch_number = '$branch', cel_phone = '$mobile', cod_location = NULLIF('$location','') where idperson ='$idPerson'";

        $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql );
        else
            return true;
    }

    public function insertStreet($idTypeStreet,$nameStreet)
    {
        $sql = "CALL hdk_updateStreet(".$idTypeStreet.",'".$nameStreet."',@id);";

        $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            $rs = $this->db->Execute('SELECT @id as idstreet');
            return $rs->fields['idstreet'];
        }

    }

    public function updateAdressData($idPerson, $idCity, $idNeighborhood, $number, $complement, $zipcode,$idTypeStreet,$nameStreet)
    {

        $sql = "
                CALL hdk_updateAddress($idPerson,$idCity,$idNeighborhood,'".$number."','".$complement."','".$zipcode."',$idTypeStreet,'".$nameStreet."');
               ";

        $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql );
        else
            return true;
    }

    public function updateNaturalData($idPerson, $cpf, $dtbirth, $gender)
    {
        if(!$dtbirth)
            $dtbirth ="''";

        $sql = "update tbnaturalperson set ssn_cpf = '$cpf', dtbirth = $dtbirth, gender = '$gender' where idperson = '$idPerson'";

        $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql );
        else
            return true;

    }

    public function getStreet($where='',$group='',$order='',$limit='')
    {

        $sql = " SELECT   idstreet,idtypestreet,`name` FROM tbstreet $where $group $order $limit";
        return $this->db->Execute($sql);
    }

    public function getCity($where='',$group='',$order='',$limit='')
    {

        $sql = " SELECT idcity,idstate,`name` FROM tbcity $where $group $order $limit";

        return $this->db->Execute($sql);
    }

    public function insertPerson($logintype, $typeperson, $natureperson, $idtheme, $name, $email, $dtcreate, $status, $vip, $telephone, $branch, $mobile, $login = NULL, $password = NULL, $time_value = 0, $overtime = 0, $location = NULL, $change_pass = 0) {
        if(empty($location)) $location = 'NULL';
        if(empty($time_value)) $time_value = 0;
        if(empty($overtime)) $overtime = 0;

        $database = $this->getConfig('db_connect');
        if ($this->isMysql($database)) {
            if ($typeperson == 4 || $typeperson == 5){
                $this->select("insert into tbperson (idtypelogin,idtypeperson,idnatureperson,idtheme,name,email,dtcreate,status,user_vip,phone_number,branch_number,fax) values ('$logintype','$typeperson','$natureperson','$idtheme','$name','$email','$dtcreate','$status','$vip','$telephone', '$branch', '$mobile')");
                return $this->db->Insert_ID();
            }
            else{
                $this->select("insert into tbperson (idtypelogin,idtypeperson,idnatureperson,idtheme,name,login,password,email,dtcreate,status,user_vip,phone_number,branch_number,cel_phone,cod_location,time_value,overtime,change_pass) values ('$logintype','$typeperson','$natureperson','$idtheme','$name','$login','$password','$email','$dtcreate','$status','$vip','$telephone', '$branch', '$mobile',$location,$time_value,$overtime,'$change_pass')");
                return $this->db->Insert_ID();
            }
        } elseif ($database == 'oci8po') {
            if ($typeperson == 4 || $typeperson == 5){
                $this->select("insert into tbperson (idtypelogin,idtypeperson,idnatureperson,idtheme,name,email,dtcreate,status,user_vip,phone_number,branch_number,fax) values ($logintype,$typeperson,$natureperson,$idtheme,'$name','$email',SYSDATE,'$status','$vip','$telephone', '$branch', '$mobile')");
                return $this->TableMaxID("tbperson","idperson");
            }
            else{
                $this->select("insert into tbperson (idtypelogin,idtypeperson,idnatureperson,idtheme,name,login,password,email,dtcreate,status,user_vip,phone_number,branch_number,cel_phone,cod_location,time_value,overtime,change_pass) values ($logintype,$typeperson,$natureperson,$idtheme,'$name','$login',UPPER('$password'),'$email',SYSDATE,'$status','$vip','$telephone', '$branch', '$mobile',$location,$time_value,$overtime,'$change_pass')");
                return $this->TableMaxID("tbperson","idperson");
            }
        }
    }

    public function insertNaturalData($idperson, $cpf, $dtbirth, $gender){
        if(!$dtbirth) $dtbirth ="''";
        $sql = "insert into tbnaturalperson (idperson, ssn_cpf, dtbirth, gender) values ('$idperson', '$cpf', $dtbirth, '$gender')";

        $rs = $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            return $rs;
        }
    }

    public function insertInDepartment($idperson, $iddepartment)
    {
        return $this->db->Execute("insert into hdk_tbdepartment_has_person (idperson, iddepartment) values ('$idperson','$iddepartment')");
    }

    public function insertNeighborhood($idCity,$nameNeighborhood)
    {
        $sql = "CALL hdk_updateNeighborhood(".$idCity.",'".$nameNeighborhood."',@id);";

        $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            $rs = $this->db->Execute('SELECT @id as idNeighborhood');
            return $rs->fields['idNeighborhood'];
        }

    }

    public function insertAddress($idPerson,$idCity,$idNeighborhood,$idTypeaddress,$number,$complement,$zipcode,$idTypeStreet,$nameStreet)
    {
        $sql =  "
                 CALL hdk_insertAddress( $idPerson,
                                     $idCity,
                                     $idNeighborhood,
                                     $idTypeaddress,
                                     '".$number."',
                                     '".$complement."',
                                     '".$zipcode."',
                                     $idTypeStreet,
                                     '".$nameStreet."',
                                     @id
                                    );
                ";

        $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            $rs = $this->db->Execute('SELECT @id as idaddress');
            return $rs->fields['idaddress'];
        }
    }

    public function isLogin($login)
    {
        $sql = "SELECT idperson FROM tbperson WHERE login = '".$login."' ";

        $rs = $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            if ($rs->RecordCOunt() > 0 )
                return true;
            else
                return false;
        }
    }

    public function changeStatus($idPerson,$newStatus)
    {
        if ($newStatus == 'I')
            $newStatus = 'N';
        return $this->db->Execute("UPDATE tbperson set status = '$newStatus' where idperson in ($idPerson)");
    }

    public function getErpCompanies($where = null,$order = null)
    {
        return $this->select("SELECT  idperson as idcompany, name FROM tbperson $where $order");
    }

    public function selectPersonName($id) {
        $ret = $this->select("select name from tbperson where idperson = $id");
        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg() . "<br> QUERY: " . $query;
            $this->error($sError);
            return false;
        }
        return $ret->fields['name'];
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

    public function getDepartment($where=null,$group=null,$order=null,$limit=null){
        $query = "SELECT iddepartment, name from hdk_tbdepartment $where $group $order $limit";
        $ret = $this->select($query);
        
        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg() . "<br> QUERY: " . $query;
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function getTypePerson($where=null,$group=null,$order=null,$limit=null){
        $query = "SELECT idtypeperson, name FROM tbtypeperson $where $group $order $limit";
        $ret = $this->select($query);
        
        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg() . "<br> QUERY: " . $query;
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function getLocation($where=null,$group=null,$order=null,$limit=null){
        $query = "SELECT idlocation, name FROM tblocation $where $group $order $limit";
        $ret = $this->select($query);
        
        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg() . "<br> QUERY: " . $query;
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function checkLogin($login) {
        $query = "SELECT idperson FROM tbperson WHERE login = '$login'";
        $ret = $this->select($query);
        
        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg() . "<br> QUERY: " . $query;
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function insertPersonAdmin($logintype, $typeperson, $natureperson, $idtheme, $name, $email, $dtcreate, $status, $vip, $telephone, $branch, $mobile, $fax, $login = NULL, $password = NULL, $time_value = 0, $overtime = 0, $location = NULL, $change_pass = 0) {
        if(empty($location)) $location = 'NULL';
        if(empty($time_value)) $time_value = 0;
        if(empty($overtime)) $overtime = 0;

        $database = $this->getConfig('db_connect');
        if ($this->isMysql($database)) {
            if ($typeperson == 4 || $typeperson == 5 || $typeperson == 8){
                $sql = "insert into tbperson (idtypelogin,idtypeperson,idnatureperson,idtheme,name,email,dtcreate,status,user_vip,phone_number,branch_number,fax) values ('$logintype','$typeperson','$natureperson','$idtheme','$name','$email','$dtcreate','$status','$vip','$telephone', '$branch', '$fax')";
            }
            else{
                $sql = "insert into tbperson (idtypelogin,idtypeperson,idnatureperson,idtheme,name,login,password,email,dtcreate,status,user_vip,phone_number,branch_number,cel_phone,cod_location,time_value,overtime,change_pass) values ('$logintype','$typeperson','$natureperson','$idtheme','$name','$login','$password','$email','$dtcreate','$status','$vip','$telephone', '$branch', '$mobile',$location,$time_value,$overtime,'$change_pass')";
            }
        } elseif ($database == 'oci8po') {
            if ($typeperson == 4 || $typeperson == 5 || $typeperson == 8){
                $sql = "insert into tbperson (idtypelogin,idtypeperson,idnatureperson,idtheme,name,email,dtcreate,status,user_vip,phone_number,branch_number,fax) values ($logintype,$typeperson,$natureperson,$idtheme,'$name','$email',SYSDATE,'$status','$vip','$telephone', '$branch', '$fax')";
            }
            else{
                $sql = "insert into tbperson (idtypelogin,idtypeperson,idnatureperson,idtheme,name,login,password,email,dtcreate,status,user_vip,phone_number,branch_number,cel_phone,cod_location,time_value,overtime,change_pass) values ($logintype,$typeperson,$natureperson,$idtheme,'$name','$login',UPPER('$password'),'$email',SYSDATE,'$status','$vip','$telephone', '$branch', '$mobile',$location,$time_value,$overtime,'$change_pass')";
            }
        }

        $this->db->Execute($sql); //echo $sql;

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql );
        else
            if ($this->isMysql($database)) {
                return $this->db->Insert_ID();
            }else{
                return $this->TableMaxID("tbperson","idperson");
            }

    }

    public function insertJuridicalData($idperson, $cnpj, $contact, $observation)
	{
        $sql = "insert into tbjuridicalperson (idperson, ein_cnpj, contact_person, observation) values ($idperson,'$cnpj','$contact', '$observation')";

        $rs = $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            return $rs;
        }
    }

    public function updateJuridicalData($id, $cnpj, $contact, $obs)
	{
        $sql = "update tbjuridicalperson set ein_cnpj = '$cnpj', contact_person = '$contact', observation = '$obs' where idperson = $id";

        $rs = $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            return $rs;
        }
        return $this->db->Execute();
    }

    public function insertDepartment($idperson, $name) 
	{
        return $this->db->Execute("insert into hdk_tbdepartment (idperson, cod_area, name) values ($idperson,0,'$name')");
    }

    public function insertPersonTypes($idperson,$idtypeperson)
    {
        $sql = "
                INSERT INTO
                  tbpersontypes (idperson, idtypeperson)
                VALUES
                   ('$idperson', '$idtypeperson')
                ";
        $ret = $this->db->Execute($sql); //echo $sql;
        if (!$ret) {
            $sError = "Error in:  " . __FILE__ . ", line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg() . "<BR>Query: " . $sql;
            $this->error($sError);
            die();
        } else {
            return $ret;
        }
    }

    public function getPersonTypes($idperson)
    {
        return  $this->select("
                SELECT
                  idtypeperson
                FROM
                  tbpersontypes
                WHERE
                   idperson = '$idperson'
                ");
    }

    public function insertAddressData($idperson, $idcity, $idneighborhood, $idstreet, $typeaddress, $number, $complement, $zipcode) 
	{
		$database = $this->getConfig('db_connect');
		if ($database == 'mysqli') {
			$query = "insert into tbaddress (idperson,idcity,idneighborhood,idstreet,idtypeaddress,number,complement,zipcode) values ($idperson,$idcity,$idneighborhood,$idstreet,$typeaddress,'$number','$complement','$zipcode')";
		} elseif ($database == 'oci8po') {
			$query = "insert into tbaddress (idperson,idcity,idneighborhood,idstreet,idtypeaddress,number_,complement,zipcode) values ($idperson,$idcity,$idneighborhood,$idstreet,$typeaddress,'$number','$complement','$zipcode')" ;
		}		
        
        $ret = $this->db->Execute($query);
		if (!$ret) {
            $sError = "Error in:  " . __FILE__ . ", line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg() . "<BR>Query: " . $query;
            $this->error($sError);
            die();
        } else {
            return $ret;
        }
    }

    public function selectTypeNature($id)
    {
        $query = "SELECT  tbnp.idnatureperson, tbnp.name
					FROM  tbperson tbp, tbnatureperson tbnp
				   WHERE  tbp.idnatureperson = tbnp.idnatureperson AND idperson = '$id'
							 ";
        $ret = $this->db->Execute($query);
        if (!$ret) {
            $sError = "Error in:  " . __FILE__ . ", line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg() . "<BR>Query: " . $query;
            $this->error($sError);
            die();
        } else {
            return $ret;
        }
    }

    public function selectCompanyData($where = NULL, $order = NULL, $limit = NULL){
        $database = $this->getConfig('db_connect');

        if ( $this->isMysql($database) ) {
            $number = "addr.number";
        } elseif ($database == 'oci8po') {
            $number = "addr.number_";
        }

        $sql = "SELECT  tbp.idperson,
                        tbp.name,
                        tbp.email,
                        tbp.status,
                        tbp.phone_number   AS telephone,
                        tbp.branch_number,
                        tbp.fax            AS fax,
                        tbtp.name          AS typeperson,
                        tbtp.idtypeperson,
                        ctry.printablename AS country,
                        ctry.idcountry,
                        stt.name AS state,
                        stt.abbr AS state_abbr,
                        stt.idstate,
                        nbh.name AS neighborhood,
                        nbh.idneighborhood, ct.name AS city,
                        ct.idcity,
                        tpstr.name AS typestreet,
                        tpstr.idtypestreet,
                        st.name AS street,
                        addr.idstreet,
                        $number,
                        addr.complement,
                        addr.zipcode,
                        pipeMask (addr.zipcode, '#####-###') AS zipcode_fmt,
                        jur.ein_cnpj,
                        pipeMask(jur.ein_cnpj,'###.###.###-##') AS cnpj_fmt,
                        jur.contact_person,
                        jur.observation
                  FROM  tbperson  tbp,
                        tbtypeperson  tbtp,
                        tbaddress addr,
                        tbcity ct,
                        tbcountry ctry,
                        tbstate stt,
                        tbstreet st,
                        tbneighborhood nbh,
                        tbtypeaddress tpad,
                        tbtypestreet tpstr,
                        tbjuridicalperson  jur
                 WHERE  tbp.idtypeperson = tbtp.idtypeperson
                   AND jur.idperson = tbp.idperson
                   AND addr.idperson = tbp.idperson
                   AND addr.idcity = ct.idcity
                   AND addr.idneighborhood = nbh.idneighborhood
                   AND addr.idstreet = st.idstreet
                   AND addr.idtypeaddress = tpad.idtypeaddress
                   AND st.idtypestreet = tpstr.idtypestreet
                   AND ct.idstate = stt.idstate
                   AND stt.idcountry = ctry.idcountry
                   $where $order $limit";

        $ret = $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql );
        else
            return $ret;
    }

    public function updatePerson($set,$where){
        $sql = "update tbperson set $set where $where";
        $ret = $this->db->Execute($sql); //echo $sql;

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql );
        else
            return $ret;
    }

    public function updateAddressData($id, $city, $idneighborhood, $idstreet, $number, $complement, $zipcode)
	{
		$database = $this->getConfig('db_connect');
		if ($database == 'mysqli') {
			$query = "update tbaddress set idcity = $city, idneighborhood = $idneighborhood, idstreet = $idstreet, number = '$number', complement = '$complement', zipcode = '$zipcode' where idperson = $id";
		} elseif ($database == 'oci8po') {
			$query = "update tbaddress set idcity = $city, idneighborhood = $idneighborhood, idstreet = $idstreet, number_ = '$number', complement = '$complement', zipcode = '$zipcode' where idperson = $id";
		}
        
        $ret =  $this->db->Execute($query);
        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;
    }

    public function updatePersonDepartment($id, $department){
        $sql = "UPDATE hdk_tbdepartment_has_person SET iddepartment = '$department' WHERE idperson = '$id'";
        $ret = $this->db->Execute($sql); //echo $sql;

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql );
        else
            return $ret;
    }

    public function delPersonTypes($idperson)
    {
        $sql = "DELETE FROM tbpersontypes WHERE idperson = '$idperson'";
        $ret = $this->db->Execute($sql);
        if (!$ret) {
            $sError = "Error in:  " . __FILE__ . ", line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg() . "<BR>Query: " . $sql;
            $this->error($sError);
        } else {
            return $ret;
        }
    }

    public function selectPersonGrid($where = NULL, $order = NULL, $limit = NULL){
        if ($this->database == 'mysqli') {
            $query = "SELECT
								  tbp.idperson      as idperson,
								  tbp.name          as name,
								  tbp.login         as login,
								  tbp.email         as email,
								  tbp.status        as status,
								  tbtp.idtypeperson as idtypeperson,
								  tbtp.name         as typeperson,
								  comp.name         as company,
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
									and tbp.idtypeperson < 6
								 $where $order $limit";
        } elseif ($this->database == 'oci8po') {
            $limit = str_replace('LIMIT', "", $limit);
            $p     = explode(",", $limit);
            $start = $p[0]+1;
            $end   = $p[0]+$p[1];
            $query =	"
						SELECT   *
						  FROM   (SELECT                                          
										a  .*, ROWNUM rnum
									FROM   (  SELECT   tbp.idperson AS idperson,
													   tbp.name AS name,
													   tbp.login AS login,
													   tbp.email AS email,
													   tbp.status AS status,
													   tbtp.idtypeperson AS idtypeperson,
													   tbtp.name AS typeperson,
													   comp.name AS company,
													   dep.name AS department
												FROM               tbperson tbp
																LEFT JOIN
																   tbtypeperson tbtp
																ON tbp.idtypeperson = tbtp.idtypeperson
															 LEFT JOIN
																hdk_tbdepartment_has_person depP
															 ON tbp.idperson = depP.idperson
														  LEFT JOIN
															 hdk_tbdepartment dep
														  ON depP.iddepartment = dep.iddepartment
													   LEFT JOIN
														  tbperson comp
													   ON dep.idperson = comp.idperson
											   WHERE   tbp.idperson != 1 AND tbp.idtypeperson < 6
											$order) a
								   WHERE   ROWNUM <= $end)
						 WHERE   rnum >= $start			
						";

        }

        $ret = $this->db->Execute($query);
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg() . "<br>Query: " . $query  ;
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function countPersonGrid($where = NULL){
        if ($this->database == 'mysqli') {
            $query = "SELECT count(tbp.idperson) as total
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
									and tbp.idtypeperson < 6
								 $where";
        } elseif ($this->database == 'oci8po') {
            $query = "SELECT   count(tbp.idperson) as total
								  FROM tbperson tbp
										  LEFT JOIN
											 tbtypeperson tbtp
										  ON tbp.idtypeperson = tbtp.idtypeperson
										 LEFT JOIN
										  hdk_tbdepartment_has_person depP
										 ON tbp.idperson = depP.idperson
										LEFT JOIN
										 hdk_tbdepartment dep
										ON depP.iddepartment = dep.iddepartment
									   LEFT JOIN
										tbperson comp
									   ON dep.idperson = comp.idperson
								   WHERE   tbp.idperson != 1 AND tbp.idtypeperson <= 6
								 $where";
        }

        $ret = $this->select($query);
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg() . "<br>Query: " . $query    ;
            $this->error($sError);
            return false;
        }
        return $ret->fields['total'];
    }

    public function getPersonGroups($id) {
        $query = "SELECT b.idgroup, c.idperson, c.name 
								FROM hdk_tbgroup_has_person a, hdk_tbgroup b, tbperson c
								WHERE a.idgroup = b.idgroup
								AND b.idperson = c.idperson
								AND a.idperson = $id";
        $ret = $this->select($query);
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg() . "<br>Query: " . $query    ;
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function insertLocation($value) {
        $query = "INSERT INTO tblocation (name) VALUES ('$value')";
        $ret = $this->select($query);
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg() . "<br>Query: " . $query    ;
            $this->error($sError);
            return false;
        }
        return $this->db->Insert_ID();
    }

    public function insertState($country, $name, $abbr=NULL){
        if($abbr){$fields = ",abbr"; $val = ",'$abbr'";}
        $query = "insert into tbstate (idcountry $fields,name) values ('$country' $val,'$name')";
        $ret = $this->select($query);
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg() . "<br>Query: " . $query    ;
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function selectMaxState(){
        $query = "select max(idstate) as total from tbstate";
        $ret = $this->select($query);
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg() . "<br>Query: " . $query    ;
            $this->error($sError);
            return false;
        }
        return $ret->fields['total'];
    }

    public function insertCity($state, $name){
        $sql = "CALL hdk_updateCity(".$state.",'".$name."',@id);";
        $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            $rs = $this->db->Execute('SELECT @id as idCity');
            return $rs->fields['idCity'];
        }
    }

    public function selectPersonFromName($name) {

        $ret = $this->select("select idperson from tbperson where name = TRIM('$name')");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
        }
        return $ret->fields['idperson'];
    }

    public function selectPersonFromLogin($login) {
        return $this->select("select idperson from tbperson where login = TRIM('$login')");
    }

    public function getIdTypePerson($id) {
        $ret = $this->select("select idtypeperson from tbperson where idperson = $id");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
        }
        return $ret->fields['idtypeperson'];
    }

    public function getPersonReportData($where = NULL,  $group = NULL, $order = NULL, $limit = NULL) {
        $query = "SELECT
                          a.idperson,
                          a.login,
                          a.name,
                          b.name  AS typeperson,
                          b.idtypeperson,
                          IF(b.idtypeperson IN (1,2,3), 
                            g.name,
                          (IF(b.idtypeperson IN (6),f.name, NULL ) ) ) AS `company`
                    FROM tbperson a
                    JOIN tbtypeperson b
                      ON a.idtypeperson = b.idtypeperson
         LEFT OUTER JOIN hdk_tbdepartment_has_person c
                      ON c.idperson = a.idperson
         LEFT OUTER JOIN hdk_tbdepartment d
                      ON d.iddepartment = c.iddepartment
         LEFT OUTER JOIN hdk_tbgroup e
                      ON e.idperson = a.idperson
         LEFT OUTER JOIN tbperson f
                      ON f.idperson = e.idcustomer
         LEFT OUTER JOIN tbperson g
                      ON g.idperson = d.idperson
                  $where $group $order $limit";
        $ret = $this->select($query);
        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg() . "<br>Query: ". $query;
            $this->error($sError);
        }
        return $ret;
    }

    public function deletePersonGroups($idperson)
    {
        $sql = "DELETE FROM hdk_tbgroup_has_person WHERE idperson = '$idperson'";
        $ret = $this->db->Execute($sql);
        if (!$ret) {
            $sError = "Error in:  " . __FILE__ . ", line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg() . "<BR>Query: " . $sql;
            $this->error($sError);
        } else {
            return $ret;
        }
    }

    public function insertGroupPerson($group, $person){
        $query = "INSERT INTO hdk_tbgroup_has_person (idgroup,idperson) VALUES ('$group','$person')";

        $ret = $this->db->Execute($query);

        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg(). "<br>Query: ".$query;
            $this->error($sError);
            return false;
        }

        return $ret;
    }

}
