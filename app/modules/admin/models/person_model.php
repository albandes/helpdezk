<?php

class person_model extends Model {

    public function selectPerson($where = NULL, $order = NULL, $limit = NULL) {
    	$database = $this->getConfig('db_connect');
		if ($database == 'mysqlt') {
			$number = "addr.number";
		} elseif ($database == 'oci8po') {
			$number = "addr.number_";
		}

        $ret = $this->select("
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
								  stt.idstate,
								  nbh.name as neighborhood,
								  ct.name as city,
								  ct.idcity,
								  tpstr.name as typestreet,
								  tpstr.idtypestreet,              
								  st.name as street,
								  $number,
								  addr.complement,
								  addr.zipcode
                            from tbperson tbp,
								  tbtypeperson tbtp,
								  tbaddress addr,
								  tbcity ct,
								  tbcountry ctry,
								  tbstate stt,
								  tbstreet st,
								  tbneighborhood nbh,
								  tbtypeaddress tpad,
								  tbtypestreet tpstr
                            where tbp.idtypeperson = tbtp.idtypeperson
									AND addr.idperson = tbp.idperson
									AND addr.idcity = ct.idcity
									AND addr.idneighborhood = nbh.idneighborhood
									AND addr.idstreet = st.idstreet
									AND addr.idtypeaddress = tpad.idtypeaddress
									AND st.idtypestreet = tpstr.idtypestreet
									AND ct.idstate = stt.idstate
									and stt.idcountry = ctry.idcountry $where $order $limit"
							);
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
        }
        return $ret;
    }
    
    public function selectPersonForJson($where = NULL, $order = NULL, $limit = NULL){
		$database = $this->getConfig('db_connect');
		if ($database == 'mysqlt') {
			$ret = $this->select("SELECT
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
									and tbp.idtypeperson != 6
								 $where $order $limit");
		} elseif ($database == 'oci8po') {
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
											   WHERE   tbp.idperson != 1 AND tbp.idtypeperson != 6
											$order) a
								   WHERE   ROWNUM <= $end)
						 WHERE   rnum >= $start			
						";
			$ret = $this->db->Execute($query);
		}
		
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
            return false;
        }
        return $ret;
    }
    public function countPersonForJson($where = NULL){
        $database = $this->getConfig('db_connect');
        if ($database == 'mysqlt') {
            $ret = $this->select("SELECT count(tbp.idperson) as total
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
									and tbp.idtypeperson != 6
								 $where");
        } elseif ($database == 'oci8po') {
            $ret = $this->select("SELECT   count(tbp.idperson) as total
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
								   WHERE   tbp.idperson != 1 AND tbp.idtypeperson != 6
								 $where");
        }
        if(!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " .  $this->db->ErrorMsg()  ;
            $this->error($sError);
            return false;
        }
        return $ret;
    }
    public function countPerson($where2 = NULL) {
        $sel = $this->select("SELECT count(tbp.idperson) as total from tbperson as tbp,
                  tbtypeperson as tbtp,
                  tbaddress as addr,
                  tbcity as ct,
                  tbcountry as ctry,
                  tbstate as stt,
                  tbstreet as st,
                  tbneighborhood as nbh,
                  tbtypeaddress as tpad,
                  tbtypestreet as tpstr
                where tbp.idtypeperson = tbtp.idtypeperson
                    AND addr.idperson = tbp.idperson
                    AND addr.idcity = ct.idcity
                    AND addr.idneighborhood = nbh.idneighborhood
                    AND addr.idstreet = st.idstreet
                    AND addr.idtypeaddress = tpad.idtypeaddress
                    AND st.idtypestreet = tpstr.idtypestreet
                    AND ct.idstate = stt.idstate
                    and stt.idcountry = ctry.idcountry $where2");
        return $sel;
    }

    public function deletePerson($where) {
        return $this->delete('tbperson', $where);
    }

    public function getTypePerson($where = NULL) 
	{
        return $this->select("select idtypeperson, name from tbtypeperson $where");
		
    }

    public function getCountry() 
	{
        return $this->select("select idcountry, printablename from tbcountry where idcountry != 1 order by name");
    }

    public function getPersonFromType($COD_TIPO) {
        return $this->select("select p.idperson as idperson, p.name as person, pd.iddepartment as iddepartment, d.name as department, j.name as juridical
                                from tbperson as p, tbperson as j, hdk_tbperson_has_department as pd
                                ,tbperson_has_juridical as pj, hdk_tbdepartment as d
                                where pd.idperson=p.idperson and pd.iddepartment=d.iddepartment and pj.idperson=p.idperson and pj.juridical=j.idperson
                                and pd.iddepartment=d.iddepartment and p.idtypeperson in ($COD_TIPO) order by p.name");
    }

    public function getLoginTypes() 
	{		
		$database = $this->getConfig('db_connect');
		if ($database == 'mysqlt') {
			return $this->select("select   idtypelogin,`name` from tbtypelogin");
		} elseif ($database == 'oci8po') {
			return $this->select("select   idtypelogin, name from tbtypelogin");
		}
    }

	
    public function getCompanies($where = null)
	{
        return $this->select("SELECT  idperson as idcompany, name FROM tbperson WHERE idtypeperson=4 $where ORDER BY name");
    }

    public function getDepartment($where = NULL) {
        return $this->select("SELECT iddepartment, name from hdk_tbdepartment $where");
    }

    public function getTypeStreet() 
	{
        return $this->select("SELECT idtypestreet, name from tbtypestreet");
    }

    public function insertPerson($logintype, $typeperson, $natureperson, $idtheme, $name, $email, $dtcreate, $status, $vip, $telephone, $branch, $mobile, $login = NULL, $password = NULL, $time_value = 0, $overtime = 0, $location = NULL, $change_pass = 0) {
		if(empty($location)) $location = 'NULL';
		if(empty($time_value)) $time_value = 0;
		if(empty($overtime)) $overtime = 0;
		
		$database = $this->getConfig('db_connect');
		if ($database == 'mysqlt') {
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

    public function selectstate($where = NULL) {
        return $this->select("select idstate, name from tbstate $where order by name");
    }

    public function selectCity($where = NULL) {
        return $this->select("select idcity, name from tbcity $where order by name");
    }

    public function selectNeighborhood($where = NULL) {
        return $this->select("select idneighborhood, name from tbneighborhood $where");
    }

    public function getStreets($and=NULL) {
        return $this->select("select idstreet, name from tbstreet where idstreet > 1 $and");
    }
    
    public function getNeighborhoods($idcity) 
	{
        return $this->select("select idneighborhood, name from tbneighborhood where idneighborhood > 1 and idcity = $idcity");
    }

    public function selectPersonName($id) {
        $ret = $this->select("select name from tbperson where idperson = $id");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
        }
        return $ret->fields['name'];
    }

    public function getLocation() 
	{
        return $this->select("select idlocation, name from tblocation");
    }

    public function selectAll() {
        return $this->select("select
        a.login,
        a.name,
        b.name  as typeperson
        from tbperson a,
        tbtypeperson b
        where a.idtypeperson = b.idtypeperson
        order by a.login ASC");
    }

    public function selectFromType() {
        return $this->select("select
        a.login,
        a.name,
        b.name  as typeperson
        from tbperson a,
        tbtypeperson b
        where a.idtypeperson = b.idtypeperson
        and b.idtypeperson = $id
        order by a.login ASC");
    }

    public function insertInDepartment($idperson, $iddepartment) {
        return $this->db->Execute("insert into hdk_tbdepartment_has_person (idperson, iddepartment) values ($idperson,$iddepartment)");
    }

    public function insertDepartment($idperson, $name) 
	{
        return $this->db->Execute("insert into hdk_tbdepartment (idperson, cod_area, name) values ($idperson,0,'$name')");
    }

    public function getMaxPerson() 
	{
        $sel = $this->db->Execute("select max(idperson) as total from tbperson");
        return $sel->fields['total'];
    }

    public function insertAdressData($idperson, $idcity, $idneighborhood, $idstreet, $typeaddress, $number, $complement, $zipcode) 
	{
		$database = $this->getConfig('db_connect');
		if ($database == 'mysqlt') {
			$query = "insert into tbaddress (idperson,idcity,idneighborhood,idstreet,idtypeaddress,number,complement,zipcode) values ($idperson,$idcity,$idneighborhood,$idstreet,$typeaddress,'$number','$complement','$zipcode')";
		} elseif ($database == 'oci8po') {
			$query = "insert into tbaddress (idperson,idcity,idneighborhood,idstreet,idtypeaddress,number_,complement,zipcode) values ($idperson,$idcity,$idneighborhood,$idstreet,$typeaddress,'$number','$complement','$zipcode')" ;
		}		
		$ret = $this->db->Execute($query);
		return $ret;
    }

    public function insertNeighborhood($idcity, $name) {
        return $this->db->Execute("insert into tbneighborhood (idcity, name) values ($idcity,'$name')");
    }

    public function insertStreet($type, $name) 
	{
        $ret = $this->db->Execute("insert into tbstreet (idtypestreet,name) values ($type,'$name')");
        if (!$ret)             die("Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg());
		return $ret ;	
		
    }

    public function checkNeighborhood($name) 
	{
        return $this->select("select idneighborhood from tbneighborhood where name = '$name'");
    }

    public function checkStreet($name) 
	{
        $ret = $this->select("select idstreet from tbstreet where name = '$name'");
        if (!$ret)             die("Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg());
		return $ret ;	
    }

    public function maxNeighborhood() 
	{
        $ret = $this->select("select max(idneighborhood) as total from tbneighborhood");
        return $ret->fields['total'];
    }

    public function maxStreet() 
	{
        $ret = $this->select("select max(idstreet) as total from tbstreet");
        return $ret->fields['total'];
    }

    public function checkLogin($login) {
        return $this->select("select idperson from tbperson where login = '$login'");
    }

    public function selectPersonData($id) {
    	$database = $this->getConfig('db_connect');
		if ($database == 'mysqlt') {
			$num = "addr.number as num";
		} elseif ($database == 'oci8po') {
			$num = "addr.number_ as num";
		}		

        return $this->select("	
								select
								  tbp.idperson,
								  tbp.name,
								  tbp.login,
								  tbp.idtypelogin,
								  tbp.email,
								  tbp.password,
								  tbp.status,
								  tbp.user_vip,
								  tbp.phone_number as telephone,
								  tbp.branch_number,
								  tbp.cel_phone as cellphone,
								  tbp.cod_location,
								  tbp.time_value,
								  tbp.overtime,
								  tbtp.name         as typeperson,
								  tbtp.idtypeperson,
								  ctry.printablename as country,
								  ctry.idcountry,
								  stt.name as state,
								  stt.idstate,
								  nbh.name as neighborhood,
								  ct.name as city,
								  ct.idcity,
								  tpstr.name as typestreet,
								  tpstr.idtypestreet,              
								  st.name as street,
								  $num,
								  addr.complement,
								  addr.zipcode,
								  nat.ssn_cpf,
								  nat.dtbirth,
								  nat.gender,
								  comp.idperson as company,
								  dep.iddepartment as department
								from tbperson tbp,
								  tbtypeperson tbtp,
								  hdk_tbdepartment dep,
								  tbperson comp,
								  tbaddress addr,
								  hdk_tbdepartment_has_person dep_pers,
								  tbcity ct,
								  tbcountry ctry,
								  tbstate stt,
								  tbstreet st,
								  tbneighborhood nbh,
								  tbtypeaddress tpad,
								  tbtypestreet tpstr,
								  tbnaturalperson nat
								where tbp.idtypeperson = tbtp.idtypeperson
									and dep.idperson = comp.idperson
									AND dep_pers.iddepartment = dep.iddepartment
									AND dep_pers.idperson = tbp.idperson
									AND addr.idperson = tbp.idperson
									AND addr.idcity = ct.idcity
									AND addr.idneighborhood = nbh.idneighborhood
									AND addr.idstreet = st.idstreet
									AND addr.idtypeaddress = tpad.idtypeaddress
									AND st.idtypestreet = tpstr.idtypestreet
									AND ct.idstate = stt.idstate
									and stt.idcountry = ctry.idcountry
									AND nat.idperson = tbp.idperson
									AND tbp.idperson = '$id'"
							);
    }

    public function selectTypePerson($id) 
	{
        return $this->select("
							SELECT   tbtp.idtypeperson
							  FROM   tbperson tbp, tbtypeperson tbtp
							 WHERE   tbp.idtypeperson = tbtp.idtypeperson AND idperson = '$id'
							 ");		
    }
    
    public function insertNaturalData($idperson, $cpf, $dtbirth, $gender){
    	$database = $this->getConfig('db_connect');
		if ($database == 'mysqlt') {
			if(!$dtbirth) $dtbirth ="''";
			return $this->db->Execute("insert into tbnaturalperson (idperson, ssn_cpf, dtbirth, gender) values ('$idperson', '$cpf', $dtbirth, '$gender')");
		} elseif ($database == 'oci8po') {
			if(!$dtbirth) $dtbirth = 'NULL';
			return $this->db->Execute("insert into tbnaturalperson (idperson, ssn_cpf, dtbirth, gender) values ('$idperson', '$cpf', $dtbirth, '$gender')");
		}

    }
    
    public function updatePerson($id, $idtypelogin, $typeuser, $name, $email, $vip, $phone, $branch, $mobile, $location, $time_value, $overtime){
    	if(empty($location)) $location = 'NULL';
		if(empty($time_value)) $time_value = 0;
		if(empty($overtime)) $overtime = 0;
		if ( !$idtypelogin) $idtypelogin = 3;
        return $this->db->Execute("update tbperson set idtypelogin = '$idtypelogin', idtypeperson = '$typeuser', name = '$name', email = '$email', user_vip = '$vip', phone_number = '$phone', branch_number = '$branch', cel_phone = '$mobile', cod_location = $location, time_value = $time_value, overtime = $overtime where idperson ='$id'");
    }
    
    public function updatePersonUser($id, $name, $email, $phone, $branch, $mobile, $location){
    	if(empty($location)) $location = 'NULL';
        return $this->db->Execute("update tbperson set name = '$name', email = '$email', phone_number = '$phone', branch_number = '$branch', cel_phone = '$mobile', cod_location = '$location' where idperson ='$id'");
    }
    
    public function updateAdressData($id, $city, $idneighborhood, $idstreet, $number, $complement, $zipcode)
	{
		$database = $this->getConfig('db_connect');
		if ($database == 'mysqlt') {
			$query = "update tbaddress set idcity = $city, idneighborhood = $idneighborhood, idstreet = $idstreet, number = '$number', complement = '$complement', zipcode = '$zipcode' where idperson = $id";
		} elseif ($database == 'oci8po') {
			$query = "update tbaddress set idcity = $city, idneighborhood = $idneighborhood, idstreet = $idstreet, number_ = '$number', complement = '$complement', zipcode = '$zipcode' where idperson = $id";
		}
        return $this->db->Execute($query);
    }
    
    public function updateNaturalData($id, $cpf, $dtbirth, $gender){
    	$database = $this->getConfig('db_connect');
		if ($database == 'mysqlt') {
			if(!$dtbirth) $dtbirth ="''";
			return $this->db->Execute("update tbnaturalperson set ssn_cpf = '$cpf', dtbirth = $dtbirth, gender = '$gender' where idperson = '$id'");
		} elseif ($database == 'oci8po') {
			if(!$dtbirth) $dtbirth = 'NULL';
			return $this->db->Execute("update tbnaturalperson set ssn_cpf = '$cpf', dtbirth = $dtbirth, gender = '$gender' where idperson = '$id'");
		}
    }
    
    public function updatePersonDepartment($id, $department){
        return $this->db->Execute("update hdk_tbdepartment_has_person set iddepartment = '$department' where idperson = '$id'");
    }
    
    public function personDeactivate($id) {
        return $this->db->Execute("UPDATE tbperson set status = 'N' where idperson in ($id)");
    }

    public function personActivate($id) {
        return $this->db->Execute("UPDATE tbperson set status = 'A' where idperson in ($id)");
    }
    
    public function insertJuridicalData($idperson, $cnpj, $contact, $observation)
	{
        return $this->db->Execute("insert into tbjuridicalperson (idperson, ein_cnpj, contact_person, observation) values ($idperson,'$cnpj','$contact', '$observation')");
    }
    
  /*  public function selectCompanyData($id){
       return $this->db->Execute("select
  tbp.idperson,
  tbp.name,
  tbp.email,
  tbp.status,
  tbp.phone_number as telephone,
  tbp.branch_number,
  tbp.fax as fax,
  tbtp.name         as typeperson,
  tbtp.idtypeperson,
  ctry.printablename as country,
  ctry.idcountry,
  stt.name as state,
  stt.idstate,
  nbh.name as neighborhood,
  ct.name as city,
  ct.idcity,
  tpstr.name as typestreet,
  tpstr.idtypestreet,              
  st.name as street,
  addr.number,
  addr.complement,
  addr.zipcode,
  jur.ein_cnpj,
  jur.contact_person, 
  jur.observation
from tbperson as tbp,
  tbtypeperson as tbtp,
  tbaddress as addr,
  tbcity as ct,
  tbcountry as ctry,
  tbstate as stt,
  tbstreet as st,
  tbneighborhood as nbh,
  tbtypeaddress as tpad,
  tbtypestreet as tpstr,
  tbjuridicalperson as jur
where tbp.idtypeperson = tbtp.idtypeperson
    AND addr.idperson = tbp.idperson
    AND addr.idcity = ct.idcity
    AND addr.idneighborhood = nbh.idneighborhood
    AND addr.idstreet = st.idstreet
    AND addr.idtypeaddress = tpad.idtypeaddress
    AND st.idtypestreet = tpstr.idtypestreet
    AND ct.idstate = stt.idstate
    and stt.idcountry = ctry.idcountry
    AND jur.idperson = tbp.idperson
    and tbp.idperson = '$id'");
    }*/
    
    public function selectCompanyData($id){
       return $this->db->Execute("select
									  tbp.idperson,
									  tbp.name,
									  tbp.email,
									  tbp.status,
									  tbp.phone_number   as telephone,
									  tbp.branch_number,
									  tbp.fax            as fax,
									  tbtp.name          as typeperson,
									  tbtp.idtypeperson,
									  addr.idaddress,
									  jur.ein_cnpj,
									  jur.contact_person,
									  jur.observation
									from tbperson  tbp,
									  tbtypeperson  tbtp
									  LEFT join tbjuridicalperson  jur
									    on jur.idperson = $id
									  LEFT join tbaddress  addr
									    on addr.idperson = $id
									where tbp.idtypeperson = tbtp.idtypeperson
									    and tbp.idperson = $id");
    }
    
    public function selectFullAddress($id){
    	return $this->db->Execute("SELECT
									  addr.number,
									  addr.complement,
									  addr.zipcode,
									  ct.name            as city,
									  ct.idcity,
									  nbh.name           as neighborhood,
									  st.name            as street,
									  tpstr.name         as typestreet,
									  tpstr.idtypestreet,
									  stt.name           as state,
									  stt.idstate,
									  ctry.printablename as country,
									  ctry.idcountry
									FROM tbaddress addr
									  LEFT JOIN tbcity as ct
									    ON addr.idcity = ct.idcity
									  LEFT JOIN tbneighborhood as nbh
									    ON addr.idneighborhood = nbh.idneighborhood
									  LEFT JOIN tbstreet as st
									    ON addr.idstreet = st.idstreet
									  LEFT JOIN tbtypestreet as tpstr
									    ON st.idtypestreet = tpstr.idtypestreet
									  LEFT JOIN tbstate as stt
									    ON ct.idstate = stt.idstate
									  LEFT JOIN tbcountry as ctry
									    ON stt.idcountry = ctry.idcountry
									WHERE idaddress = $id");
    }

    public function updateCompany($id, $typeuser, $name, $email, $phone, $branch, $mobile){
        return $this->db->Execute("update tbperson set idtypeperson = $typeuser, name = '$name', email = '$email', phone_number = '$phone', branch_number = '$branch', fax = '$mobile' where idperson =$id");
    }
    
    public function updateJuridicalData($id, $cnpj, $contact, $obs)
	{
        return $this->db->Execute("update tbjuridicalperson set ein_cnpj = '$cnpj', contact_person = '$contact', observation = '$obs' where idperson = $id");
    }
    
    public function checkJuridicalData($id)
	{
        return $this->db->Execute("SELECT idperson FROM tbjuridicalperson where idperson = $id");
    }
    
    public function insertState($country, $abbr, $name){
        return $this->db->Execute("insert into tbstate (idcountry,abbr,name) values ('$country','$abbr','$name')");
    }
    
    public function selectMaxState(){
        $ret = $this->db->Execute("select max(idstate) as total from tbstate");
        return $ret->fields['total'];
    }
    
    public function insertCity($state, $name){
        return $this->db->Execute("insert into tbcity (idstate,name) values ($state,'$name')");
    }
    
    public function selectMaxCity(){
        $ret = $this->db->Execute("select max(idcity) as total from tbcity");
        return $ret->fields['total'];
    }
    
    public function getCurrentPassword($id){
        $ret = $this->select("select password from tbperson where idperson = '$id'");
        return $ret->fields['password'];
    }
    
    public function getLoginType($id){
        $ret = $this->select("select idtypelogin from tbperson where idperson = '$id'");
        return $ret->fields['idtypelogin'];
    }
	
    public function changePassword($id, $password, $change_pass = 0){
        $database = $this->getConfig('db_connect');
		if ($database == 'mysqlt') {
			return $this->db->Execute("update tbperson set password = '$password', change_pass = '$change_pass' where idperson = '$id'");
		} elseif ($database == 'oci8po') {
			return $this->db->Execute("update tbperson set password = UPPER('$password'), change_pass = '$change_pass' where idperson = '$id'");
		}
    }
	
	
	public function getTableReports($where = NULL) {
        //return $this->select("SELECT a.login, a.name, b.name as typeperson from tbperson a, tbtypeperson b WHERE a.idtypeperson = b.idtypeperson $where ORDER BY typeperson, name ASC");
        return $this->select("	SELECT
								  a.idperson,
								  a.login,
								  a.name,
								  b.name  as typeperson,
								  b.idtypeperson,
								  IF(b.idtypeperson IN (1,2,3), 
									(SELECT tbperson.name FROM hdk_tbdepartment_has_person, hdk_tbdepartment, tbperson WHERE hdk_tbdepartment_has_person.idperson = a.idperson AND hdk_tbdepartment_has_person.iddepartment = hdk_tbdepartment.iddepartment AND tbperson.idperson = hdk_tbdepartment.idperson),
								  (IF(b.idtypeperson IN (6),(SELECT tbperson.name FROM hdk_tbgroup, tbperson WHERE hdk_tbgroup.idperson = a.idperson AND tbperson.idperson = hdk_tbgroup.idcustomer), NULL ) ) ) AS `company`
								FROM tbperson a,
								  tbtypeperson b
								WHERE a.idtypeperson = b.idtypeperson
								$where
								ORDER BY typeperson, name ASC");
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
	
	public function getPersonGroups($id) {		
        $ret = $this->select("SELECT b.idgroup, c.idperson, c.name 
								FROM hdk_tbgroup_has_person a, hdk_tbgroup b, tbperson c
								WHERE a.idgroup = b.idgroup
								AND b.idperson = c.idperson
								AND a.idperson = $id");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
        }
        return $ret;
    }


    public function getErpCompanies($where = null,$order = null)
    {
        return $this->select("SELECT  idperson as idcompany, name FROM tbperson $where $order");
    }

    public function getCompanyFull($where,$order){
        return $this->select("SELECT * FROM vw_CompanyFull $where $order");
    }

    public function getPersonSecret($idperson)
    {
        $ret = $this->select("SELECT token FROM tbperson WHERE idperson = '$idperson'");
        return $ret->fields['token'];
    }


    public function getPersonLogin($id)
    {
        $ret = $this->select("select login from tbperson where idperson = '$id'");
        return $ret->fields['login'];
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

    public function insertPersonTypes($idperson,$idtypeperson)
    {
        $sql = "
                INSERT INTO
                  tbpersontypes (idperson, idtypeperson)
                VALUES
                   ('$idperson', '$idtypeperson')
                ";
        $ret = $this->db->Execute($sql);
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
}

?>
