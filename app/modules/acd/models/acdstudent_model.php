<?php
if(class_exists('Model')) {
    class dynamicStudent_model extends Model {}
} elseif(class_exists('cronModel')) {
    class dynamicStudent_model extends cronModel {}
} elseif(class_exists('apiModel')) {
    class dynamicStudent_model extends apiModel {}
}

class acdstudent_model extends dynamicStudent_model
{


    public $database;

    public function __construct()
    {
        parent::__construct();
        $this->database = $this->getConfig('db_connect');
    }

    public function insertProfile($name,$cpf,$cardid,$dtbirth,$idgender,$email)
    {
        $sql =  "
                  INSERT INTO tbperson_profile (`name`,cpf,id_card,birth_date,idgender,email) 
                  VALUES ('$name','$cpf','$cardid','$dtbirth',$idgender,'$email')
                ";

        $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            $rs = $this->db->insert_Id();
            return $rs;
        }

    }

    public function updateProfile($name,$cpf,$cardid,$dtbirth,$idgender,$email,$id)
    {
        $sql =  "
                  UPDATE tbperson_profile 
                     SET `name` = '$name',
                         cpf = '$cpf',
                         id_card = '$cardid',
                         birth_date = '$dtbirth',
                         idgender = $idgender,
                         email = '$email'
                   WHERE idperson_profile = $id
                ";

        $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            return $id;
        }

    }

    public function insertStudent($idprofile,$idperseus,$idintranet=NULL)
    {
        $sql =  "
                  INSERT INTO acd_tbstudent (idperson_profile,idperseus,idintranet) 
                  VALUES ($idprofile,$idperseus,$idintranet)
                ";

        $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            $rs = $this->db->insert_Id();
            return $rs;
        }

    }

    public function insertParent($idprofile,$idperseus)
    {
        $sql =  "
                  INSERT INTO acd_tbparent (idperson_profile,idperseus) 
                  VALUES ($idprofile,$idperseus)
                ";

        $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            $rs = $this->db->insert_Id();
            return $rs;
        }
    }

    public function getGenderIdByAbbrv($abbrv)
    {
        $sql =  "SELECT idgender FROM hur_tbgender WHERE abbrev = '$abbrv'";

        $ret = $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            return $ret->fields['idgender'];
        }

    }

    public function getStudentID($where=null)
    {
        $sql =  "SELECT idstudent,idperson_profile FROM acd_tbstudent $where";

        $ret = $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            return $ret;
        }

    }

    public function getParentID($idlegacy)
    {
        $sql =  "SELECT idparent,idperson_profile FROM acd_tbparent WHERE idperseus = $idlegacy";

        $ret = $this->db->Execute($sql); //echo $sql.'<br>';

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            return $ret;
        }

    }

    public function getClass($where=null)
    {
        $sql =  "SELECT a.idturma, a.nome FROM acd_tbturma a, acd_tbserie b, acd_tbcurso c
                  WHERE a.idserie = b.idserie
                    AND b.idcurso = c.idcurso
                    $where";

        $ret = $this->db->Execute($sql); //echo $sql;

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            return $ret;
        }

    }

    public function getEnrollmentData($where=null,$group=null,$order=null,$limit=null)
    {
        $sql =  "SELECT idenrollment, a.idturma, a.idstatusenrollment, record_status, 
	                    a.idstudent,f.`name`, b.abrev, g.description, a.dtentry, e.idperseus, e.idintranet,
	                    c.idcurso, d.descricao coursename  
                   FROM acd_tbenrollment a, acd_tbturma b, acd_tbserie c, acd_tbcurso d, 
	                    acd_tbstudent e, tbperson_profile f, acd_tbstatusenrollment g
                  WHERE a.idturma = b.idturma
                    AND b.idserie = c.idserie
                    AND c.idcurso = d.idcurso
                    AND a.idstudent = e.idstudent 
                    AND e.idperson_profile = f.idperson_profile 
                    AND a.idstatusenrollment = g.idstatusenrollment
                    $where $group $order $limit";

        $ret = $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            return $ret;
        }

    }

    public function getEnrollmentStatusData($where=null)
    {
        $sql =  "SELECT idstatusenrollment, description, abbreviation FROM acd_tbstatusenrollment $where";

        $ret = $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            return $ret;
        }

    }

    public function insertEnrollment($idstudent,$idturma,$year,$dtentry,$idstatus)
    {
        $sql =  "INSERT INTO acd_tbenrollment (idstudent,idturma,`year`,idstatusenrollment,dtentry,dtmodified)
                    VALUES ($idstudent,$idturma,$year,$idstatus,'$dtentry',NOW())";

        $ret = $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            return $ret;
        }

    }

    public function changeStatusEnrollmentRec($id,$newstatus)
    {
        $sql =  "UPDATE acd_tbenrollment SET record_status = '$newstatus', dtmodified = NOW() WHERE idenrollment = $id";

        $ret = $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            return $ret;
        }

    }

    public function getStudentData($where=null,$order=null)
    {
        $sql =  "SELECT idstudent,a.idperson_profile, `name`, idperseus, idintranet, b.company_email 
                   FROM acd_tbstudent a, tbperson_profile b
                  WHERE a.idperson_profile = b.idperson_profile
                   $where $order";

        $ret = $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            return $ret;
        }

    }

    public function getParentData($where=null,$order=null)
    {
        $sql =  "SELECT idparent,a.idperson_profile, `name`, cpf, id_card, a.idperseus, idgender, email 
                   FROM acd_tbparent a, tbperson_profile b
                  WHERE a.idperson_profile = b.idperson_profile
                   $where $order";

        $ret = $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            return $ret;
        }

    }

    public function getKinshipData($where=null,$order=null)
    {
        $sql =  "SELECT idkinship,`description` FROM acd_tbkinship $where $order";

        $ret = $this->db->Execute($sql); //echo $sql.'<br>';

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            return $ret;
        }

    }

    public function insertBindStudent($idstudent,$idparent,$idkinship,$flgEmailSMS='Y',$flgBankSlip='Y',$flgAccessApp='Y')
    {
        $sql =  "INSERT INTO acd_tbstudent_has_acd_tbparent (idstudent,idparent,idkinship,email_sms,bank_ticket,access_app) 
                      VALUES ($idstudent,$idparent,$idkinship,'{$flgEmailSMS}','{$flgBankSlip}','{$flgAccessApp}')";

        $ret = $this->db->Execute($sql); //echo $sql.'<br>';

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            return $ret;
        }

    }

    public function getBindData($idstudent,$idparent)
    {
        $sql =  "SELECT * FROM acd_tbstudent_has_acd_tbparent 
                  WHERE idstudent = $idstudent AND idparent = $idparent";

        $ret = $this->db->Execute($sql); //echo $sql.'<br>';

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            return $ret;
        }

    }

    public function getInternalUserByIDCard($IDcard)
    {
        $sql =  "SELECT idstudent id, a.idperson_profile, `name`, cpf, b.id_card, birth_date, 
                        DATE_FORMAT(birth_date,'%d/%m/%Y') fmt_dtbirth, c.description gender, c.abbrev gender_abbr 
                   FROM acd_tbstudent a, tbperson_profile b, hur_tbgender c
                  WHERE a.idperson_profile = b.idperson_profile
                    AND b.idgender = c.idgender
                    AND (b.cpf = '$IDcard' OR b.id_card = '$IDcard' OR a.idperseus = '$IDcard' OR a.idintranet = '$IDcard')
                  UNION
                 SELECT idparent id, a.idperson_profile, `name`, cpf, b.id_card, birth_date, 
                        DATE_FORMAT(birth_date,'%d/%m/%Y') fmt_dtbirth, c.description gender, c.abbrev gender_abbr 
                   FROM acd_tbparent a, tbperson_profile b, hur_tbgender c
                  WHERE a.idperson_profile = b.idperson_profile
                    AND b.idgender = c.idgender
                    AND (b.cpf = '$IDcard' OR b.id_card = '$IDcard' OR a.idperseus = '$IDcard')
                  UNION
                 SELECT idfuncionario id, '' idperson_profile, nome `name`, cpf, identidade id_card, dtnasc birth_date, 
                        DATE_FORMAT(dtnasc,'%d/%m/%Y') fmt_dtbirth, b.description gender, sexo gender_abbr 
                   FROM hur_tbfuncionario a, hur_tbgender b
                  WHERE a.sexo = b.abbrev
                    AND (cpf = '$IDcard' OR identidade = '$IDcard') ";

        $ret = $this->db->Execute($sql); //echo $sql.'<br>';

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            return $ret;
        }

    }

    public function getParentIDByBind($where=null)
    {
        $sql =  "SELECT a.idparent, b.idperson_profile  
                    FROM acd_tbstudent_has_acd_tbparent a, acd_tbparent b, acd_tbstudent c, acd_tbkinship d
                    WHERE a.idparent = b.idparent
                    AND a.idstudent = c.idstudent
                    AND a.idkinship = d.idkinship
                    $where
                    ";

        $ret = $this->db->Execute($sql); //echo $sql.'<br>';

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            return $ret;
        }

    }

    public function getEnrollmentStatus($where=null,$group=null, $order=null,$limit=null)
    {
        $sql =  "SELECT * FROM acd_tbstatusenrollment $where $group $order $limit";

        $ret = $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            return $ret;
        }

    }

    public function disableAllEnrollmentRec($where)
    {
        $sql =  "UPDATE acd_tbenrollment SET record_status = 'N', dtmodified = NOW() $where";

        $ret = $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            return $ret;
        }

    }

    public function getStudentDataApp($where=null,$order=null)
    {
        $sql =  "SELECT DISTINCT IFNULL(s.idintranet,s.idperseus) registroacademico, `pipeLatinToUtf8`(p.`name`) nome,
                        DATE_FORMAT(p.birth_date,'%d-%m-%Y') datanascimento, g.description sexo,
                        s.idstudent
                   FROM acd_tbstudent s, tbperson_profile p, hur_tbgender g, acd_tbenrollment e, 
                        acd_tbturma t, acd_tbserie ser
                  WHERE p.idperson_profile = s.idperson_profile
                    AND g.idgender = p.idgender
                    AND e.idstudent = s.idstudent
                    AND t.idturma = e.idturma
                    AND ser.idserie = t.idserie
                   $where $order";

        $ret = $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            return $ret;
        }

    }

    public function getTotalStatusActive($idstudent,$year)
    {
        $sql =  "SELECT COUNT(idenrollment) total_active 
                   FROM acd_tbenrollment 
                  WHERE idstudent = {$idstudent} 
                    AND record_status = 'A' 
                    AND `year` = {$year}";

        $ret = $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            return $ret;
        }

    }

    public function getParentApp($where=null,$group=null,$order=null)
    {
        $sql =  "SELECT DISTINCT pipeLatinToUtf8(`name`) nome , par.idparent, cpf login, email, 
                        GROUP_CONCAT(DISTINCT st.idstudent) childs
                   FROM acd_tbstudent_has_acd_tbparent shp, acd_tbparent par, tbperson_profile p,
                        acd_tbenrollment e, acd_tbturma t, acd_tbserie s, acd_tbstudent st
                  WHERE shp.idparent = par.idparent
                    AND shp.idstudent = e.idstudent
                    AND par.idperson_profile = p.idperson_profile
                    AND e.idturma = t.idturma
                    AND t.idserie = s.idserie
                    AND shp.idstudent = st.idstudent
                    $where $group $order
                    ";

        $ret = $this->db->Execute($sql); //echo $sql.'<br>';

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            return $ret;
        }

    }

    public function getTeacherApp($where=null,$group=null,$order=null)
    {
        $sql =  "SELECT p.idperson, pipeLatinToUtf8(`name`) nome, login, email, phone_number, cel_phone, `status`
                   FROM tbperson p, hdk_tbdepartment_has_person dhp
                  WHERE dhp.idperson = p.idperson
                    $where $group $order
                    ";

        $ret = $this->db->Execute($sql); //echo $sql.'<br>';

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            return $ret;
        }

    }

    public function getStudentInClass($where=null,$group=null,$order=null)
    {
        $sql =  "SELECT e.idturma, idstudent, record_status
                   FROM acd_tbenrollment e, acd_tbturma t, acd_tbserie s
                  WHERE e.idturma = t.idturma
                    AND t.idserie = s.idserie
                    $where $group $order
                    ";

        $ret = $this->db->Execute($sql); //echo $sql.'<br>';

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            return $ret;
        }

    }

    public function classHasBilingual($idclass)
    {
        $sql =  "SELECT idturma, idbilingue
                   FROM acd_tbturma_has_turmabilingue
                  WHERE idturma = $idclass";

        $ret = $this->db->Execute($sql); //echo $sql.'<br>';

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            return $ret;
        }

    }

    public function getBindStudentUser($year)
    {
        $sql =  "SELECT DISTINCT st.idstudent, CONCAT('aluno-',st.idstudent) iduser, 'A' tipo, 
                        st.idperseus user_legacy, st.idperseus student_legacy
                   FROM acd_tbstudent st, acd_tbenrollment e, acd_tbturma t, acd_tbserie s
                  WHERE e.idstudent = st.idstudent
                    AND e.idturma = t.idturma
                    AND t.idserie = s.idserie
                    AND e.year = {$year}
                    AND e.record_status = 'A'
                
                  UNION
                
                 SELECT DISTINCT shp.idstudent, CONCAT('responsavel-',shp.idparent) iduser, 'R' tipo, 
			            par.idperseus user_legacy, st.idperseus student_legacy
                   FROM acd_tbstudent_has_acd_tbparent shp, acd_tbparent par, tbperson_profile p,
                       acd_tbenrollment e, acd_tbturma t, acd_tbserie s, acd_tbstudent st
                  WHERE shp.idparent = par.idparent
                    AND shp.idstudent = e.idstudent
                    AND par.idperson_profile = p.idperson_profile
                    AND e.idturma = t.idturma
                    AND t.idserie = s.idserie
                    AND e.idstudent = st.idstudent
                    AND e.`year` = {$year}
                    AND e.record_status = 'A'
                    AND (p.cpf != '' AND p.cpf IS NOT NULL)
                    AND shp.access_app = 'Y'
               ORDER BY idstudent";

        $ret = $this->db->Execute($sql); //echo $sql.'<br>';

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            return $ret;
        }

    }

    public function updateBindStudent($idstudent,$idparent,$idkinship,$flgEmail='Y',$flgSMS='Y',$flgBankSlip='Y',$flgAccessApp='Y')
    {
        $sql =  "UPDATE acd_tbstudent_has_acd_tbparent 
                    SET idkinship = $idkinship,
                        flg_email = '{$flgEmail}',
                        flg_sms = '{$flgSMS}',
                        bank_ticket = '{$flgBankSlip}',
                        access_app = '{$flgAccessApp}'
                  WHERE idstudent = $idstudent
                    AND idparent = $idparent";

        $ret = $this->db->Execute($sql); //echo $sql.'<br>';

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');

    }

    public function getStudentLegalDocHead($where=null,$order=null,$limit=null)
    {
        $sql =  "SELECT DISTINCT s.idintranet, s.idperseus, pipeLatinToUtf8(p.`name`) name,
                        DATE_FORMAT(p.birth_date,'%d/%m/%Y') dtbirth, g.description gender,
                        s.idstudent,se.idcurso, p.id_card,
                        (SELECT `name` 
                           FROM acd_tbstudent_has_acd_tbparent shm, acd_tbparent pm, tbperson_profile m
                          WHERE shm.idparent = pm.idparent
                            AND pm.idperson_profile = m.idperson_profile
                            AND shm.idkinship = 1
                            AND shm.idstudent = s.idstudent) mom_name,
                         (SELECT `name` 
                           FROM acd_tbstudent_has_acd_tbparent shf, acd_tbparent pf, tbperson_profile f
                          WHERE shf.idparent = pf.idparent
                            AND pf.idperson_profile = f.idperson_profile
                            AND shf.idkinship = 2
                            AND shf.idstudent = s.idstudent) dad_name,
                          IFNULL(s.idintranet,s.idperseus) enrollmentID, c.idperseus idcourse
                    FROM acd_tbstudent s, tbperson_profile p, hur_tbgender g, acd_tbenrollment e, acd_tbturma t,
                        acd_tbserie se, acd_tbcurso c
                    WHERE p.idperson_profile = s.idperson_profile
                    AND g.idgender = p.idgender
                    AND e.idstudent = s.idstudent
                    AND e.idturma = t.idturma
                    AND t.idserie = se.idserie
                   $where $order $limit";

        $ret = $this->db->Execute($sql);

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');

    }

    public function getBindStudentUserNewEnrollment($year)
    {
        $sql =  "SELECT DISTINCT st.idstudent, CONCAT('aluno-',st.idstudent) iduser, 'A' tipo, 
                        st.idperseus user_legacy, st.idperseus student_legacy
                   FROM acd_tbstudent st, acd_tbenrollment e, acd_tbturma t, acd_tbserie s
                  WHERE e.idstudent = st.idstudent
                    AND e.idturma = t.idturma
                    AND t.idserie = s.idserie
                    AND e.year = {$year}
                    AND e.record_status = 'A'
                    AND e.idstatusenrollment = 1
                
                  UNION
                
                 SELECT DISTINCT shp.idstudent, CONCAT('responsavel-',shp.idparent) iduser, 'R' tipo, 
			            par.idperseus user_legacy, st.idperseus student_legacy
                   FROM acd_tbstudent_has_acd_tbparent shp, acd_tbparent par, tbperson_profile p,
                       acd_tbenrollment e, acd_tbturma t, acd_tbserie s, acd_tbstudent st
                  WHERE shp.idparent = par.idparent
                    AND shp.idstudent = e.idstudent
                    AND par.idperson_profile = p.idperson_profile
                    AND e.idturma = t.idturma
                    AND t.idserie = s.idserie
                    AND e.idstudent = st.idstudent
                    AND e.`year` = {$year}
                    AND e.record_status = 'A'
                    AND e.idstatusenrollment = 1
                    AND (p.cpf != '' AND p.cpf IS NOT NULL)
                    AND shp.access_app = 'Y'
               ORDER BY idstudent";

        $ret = $this->db->Execute($sql); //echo $sql.'<br>';

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            return $ret;
        }

    }

    public function getCoordinationApp()
    {
        $sql =  "SELECT (CASE
                            WHEN idperson = 55 THEN CONCAT('professor-',idperson)
                            WHEN idperson IN (450,567,799,462,768,80,1004,937) THEN CONCAT('scoord-',idperson)
                            WHEN idperson IN (234,535,770,771,772,832) THEN CONCAT('fin-',idperson)
                            WHEN idperson IN (573,889) THEN CONCAT('sec-',idperson)
                            ELSE CONCAT('coord-',idperson)
                        END) idintegracao, idperson, pipeLatinToUtf8(`name`) nome
                   FROM tbperson 
                  WHERE idperson IN (4,5,55,59,450,462,567,566,578,768,799,234,535,573,889,80,1004,770,771,772,832,937)";

        $ret = $this->db->Execute($sql); //echo $sql.'<br>';

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            return $ret;
        }

    }

    public function getDeactivatedStudents($where=null,$order=null,$limit=null,$group=null)
    {
        $sql =  "SELECT DISTINCT a.idstudent,
                        LOWER(CONVERT(CAST(CONVERT(`name` USING  latin1) AS BINARY) USING utf8)) `name`,
                        company_email 
                   FROM acd_tbenrollment a, acd_tbstudent b, tbperson_profile c, acd_tbturma d, acd_tbserie e
                  WHERE a.idstudent = b.idstudent
                    AND c.idperson_profile = b.idperson_profile
                    AND a.idturma = d.idturma
                    AND d.idserie = e.idserie
                 $where $group $order $limit";

        $ret = $this->db->Execute($sql); //echo $sql."\n";

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            return $ret;
        }

    }

    public function getlinkStudentUser($whereSt=null,$whereP=null,$order=null)
    {
        $sql =  "SELECT DISTINCT st.idstudent, CONCAT('aluno-',st.idstudent) iduser, 'A' tipo, 
                        st.idperseus user_legacy, st.idperseus student_legacy
                   FROM acd_tbstudent st, acd_tbenrollment e, acd_tbturma t, acd_tbserie s
                  WHERE e.idstudent = st.idstudent
                    AND e.idturma = t.idturma
                    AND t.idserie = s.idserie
                    $whereSt
                
                  UNION
                
                 SELECT DISTINCT shp.idstudent, CONCAT('responsavel-',shp.idparent) iduser, 'R' tipo, 
			            par.idperseus user_legacy, st.idperseus student_legacy
                   FROM acd_tbstudent_has_acd_tbparent shp, acd_tbparent par, tbperson_profile p,
                       acd_tbenrollment e, acd_tbturma t, acd_tbserie s, acd_tbstudent st
                  WHERE shp.idparent = par.idparent
                    AND shp.idstudent = e.idstudent
                    AND par.idperson_profile = p.idperson_profile
                    AND e.idturma = t.idturma
                    AND t.idserie = s.idserie
                    AND e.idstudent = st.idstudent
                    $whereP
               $order";

        $ret = $this->db->Execute($sql); //echo $sql."\n";

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            return $ret;
        }

    }

    public function getStudent($where=null,$order=null,$limit=null,$group=null) {
        $query = "SELECT DISTINCT IFNULL(s.idintranet,s.idperseus) idenrollment, p.`name`,
                            DATE_FORMAT(p.birth_date,'%d/%m/%Y') birth_date, g.description gender,
                            s.idstudent, t.abrev class_name
                    FROM acd_tbstudent s, tbperson_profile p, hur_tbgender g, acd_tbenrollment e, 
                            acd_tbturma t, acd_tbserie ser
                   WHERE p.idperson_profile = s.idperson_profile
                     AND g.idgender = p.idgender
                     AND e.idstudent = s.idstudent
                     AND t.idturma = e.idturma
                     AND ser.idserie = t.idserie
                    $where $group $order $limit";
        //echo "{$query}\n";
        return $this->selectPDO($query);
    }

    public function getStudentParent($where=null,$order=null,$limit=null,$group=null) {
        $query = "SELECT a.idparent, c.`name`
                    FROM acd_tbstudent_has_acd_tbparent a, acd_tbparent b, tbperson_profile c
                   WHERE a.idparent = b.idparent
                     AND b.idperson_profile = c.idperson_profile
                  $where $group $order $limit";
        //echo "{$query}\n";
        return $this->selectPDO($query);
    }

}