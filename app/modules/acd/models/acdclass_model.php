<?php
if(class_exists('Model')) {
    class dynamicClass_model extends Model {}
} elseif(class_exists('cronModel')) {
    class dynamicClass_model extends cronModel {}
} elseif(class_exists('apiModel')) {
    class dynamicClass_model extends apiModel {}
}

class acdclass_model extends dynamicClass_model
{


    public $database;

    public function __construct()
    {
        parent::__construct();
        $this->database = $this->getConfig('db_connect');
    }

    public function getClassesByEnrollment($where=null,$order=null)
    {
        $sql =  "
                  SELECT DISTINCT a.idturma, nome, abrev
                    FROM acd_tbturma a, acd_tbserie b, acd_tbenrollment c
                   WHERE a.idserie = b.idserie
                     AND a.idturma = c.idturma
                  $where $order 
                ";

        $ret = $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        }

        return $ret;

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

    public function getClass($where=null,$group=null,$order=null,$limit=null)
    {
        $sql =  "SELECT a.idturma, a.nome FROM acd_tbturma a, acd_tbserie b, acd_tbcurso c
                  WHERE a.idserie = b.idserie
                    AND b.idcurso = c.idcurso
                    $where $group $order $limit";

        $ret = $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            return $ret;
        }

    }

    public function getEnrollmentData($where)
    {
        $sql =  "SELECT idenrollment, a.idturma, idstatusenrollment, record_status 
                   FROM acd_tbenrollment a, acd_tbturma b, acd_tbserie c, acd_tbcurso d
                  WHERE a.idturma = b.idturma
                    AND b.idserie = c.idserie
                    AND c.idcurso = d.idcurso
                    $where";

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

    public function insertEnrollment($idstudent,$idturma,$year,$idstatus)
    {
        $sql =  "INSERT INTO acd_tbenrollment (idstudent,idturma,`year`,idstatusenrollment,dtentry,dtmodified)
                    VALUES ($idstudent,$idturma,$year,$idstatus,NOW(),NOW())";

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
        $sql =  "UPDATE acd_tbenrollment SET record_status = '$newstatus' WHERE idenrollment = $id";

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
        $sql =  "SELECT idstudent,a.idperson_profile, `name`, idperseus, idintranet 
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
        $sql =  "SELECT idparent,a.idperson_profile, `name`, cpf, id_card, a.idperseus 
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

    public function insertBindStudent($idstudent,$idparent,$idkinship)
    {
        $sql =  "INSERT INTO acd_tbstudent_has_acd_tbparent (idstudent,idparent,idkinship) 
                      VALUES ($idstudent,$idparent,$idkinship)";

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
        $sql =  "SELECT a.idparent, a.idstudent, b.idperson_profile, e.name, e.email, cpf, id_card, a.send_bank_ticket  
                    FROM acd_tbstudent_has_acd_tbparent a, acd_tbparent b, acd_tbstudent c, 
                         acd_tbkinship d, tbperson_profile e
                    WHERE a.idparent = b.idparent
                    AND a.idstudent = c.idstudent
                    AND a.idkinship = d.idkinship
                    AND b.idperson_profile = e.idperson_profile
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

    public function getStudentByClass($where=null,$order=null)
    {
        $sql =  "
                  SELECT DISTINCT a.idstudent, `name`
                    FROM acd_tbenrollment a, acd_tbstudent b, tbperson_profile c
                    WHERE a.idstudent = b.idstudent
                    AND b.idperson_profile = c.idperson_profile                    
                  $where $order 
                ";

        $ret = $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        }

        return $ret;

    }

    public function getClassesByEnrollmentApp($where1=null,$where2=null,$order=null)
    {
        $sql =  "SELECT DISTINCT a.idturma, nomeapp, abrevapp, a.idserie, idcurso, a.numero cnumero, 
                        COUNT(IF(c.record_status = 'A', 1, NULL)) total
                   FROM acd_tbturma a
        LEFT OUTER JOIN acd_tbserie b
                     ON a.idserie = b.idserie
        LEFT OUTER JOIN acd_tbenrollment c
                     ON a.idturma = c.idturma
                    $where1
                    GROUP BY a.idturma
                    UNION
                    SELECT DISTINCT a.idturma, nomeapp, abrevapp, a.idserie, idcurso, a.numero, COUNT(IF(d.record_status = 'A', 1, NULL)) total
                    FROM acd_tbturma a
                    LEFT OUTER JOIN acd_tbserie b
                    ON a.idserie = b.idserie
                    LEFT OUTER JOIN `acd_tbturma_has_turmabilingue` c
                    ON a.idturma = c.idbilingue
                    LEFT OUTER JOIN acd_tbenrollment d
                    ON c.idturma = d.idturma
                    $where2
                    GROUP BY a.idturma
                    $order 
                ";

        $ret = $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        }

        return $ret;

    }

    public function getSerieApp($where1=null,$where2=null,$order=null)
    {
        $sql =  "SELECT b.idserie,idcurso,descricaoapp, COUNT(idenrollment) total
                   FROM acd_tbserie a
        LEFT OUTER JOIN acd_tbturma b
                     ON b.idserie = a.idserie
        LEFT OUTER JOIN acd_tbenrollment c
                     ON c.idturma = b.idturma
                $where1
               GROUP BY a.idserie
                  UNION
                 SELECT b.idserie,idcurso,descricaoapp, COUNT(idenrollment) total
                   FROM acd_tbserie a
        LEFT OUTER JOIN acd_tbturma b
                     ON a.idserie = b.idserie
        LEFT OUTER JOIN `acd_tbturma_has_turmabilingue` c
                     ON b.idturma = c.idbilingue
        LEFT OUTER JOIN acd_tbenrollment d
                     ON d.idturma = c.idturma
                $where2
               GROUP BY b.idserie
                 $order";

        $ret = $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        }

        return $ret;

    }

    public function getBilingualClassesApp($where=null,$order=null)
    {
        $sql =  "SELECT DISTINCT (CASE IFNULL(e.idserie,a.idserie) 
                                    WHEN 22 THEN pipeLatinToUtf8(CONCAT(IFNULL(e.nome,a.nome),' - Bilíngue'))
                                    ELSE IFNULL(pipeLatinToUtf8(e.nome),pipeLatinToUtf8(a.nome))
                                 END) nome, 
                        (CASE IFNULL(e.idserie,a.idserie) 
                            WHEN 22 THEN pipeLatinToUtf8(CONCAT(IFNULL(e.abrev,a.abrev),' - Bilíngue'))
                            ELSE IFNULL(pipeLatinToUtf8(e.abrev),pipeLatinToUtf8(a.abrev))
                        END) abrev , 
                        IFNULL(e.idserie,a.idserie) idserie,
                        IFNULL(e.idturma,a.idturma) idturma 
                   FROM acd_tbturma a 
                   JOIN acd_tbserie b 
                     ON a.idserie = b.idserie
                   JOIN acd_tbenrollment c
                     ON a.idturma = c.idturma
        LEFT OUTER JOIN acd_tbturma_has_turmabilingue d
                     ON a.idturma = d.idturma
        LEFT OUTER JOIN acd_tbturma e
                     ON e.idturma = d.idbilingue
                  $where $order";

        $ret = $this->db->Execute($sql); //echo "{$sql}\n";

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        }

        return $ret;

    }

    public function getCoordinationClasses($where=null,$order=null)
    {
        $sql =  "SELECT idturma, idcurso, a.abrev
                   FROM acd_tbturma a, acd_tbserie b
                  WHERE a.idserie = b.idserie
                 $where $order";

        $ret = $this->db->Execute($sql); //echo "{$sql}\n";

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        }

        return $ret;

    }

    public function getGrid($where=null, $order=null, $limit=null, $group=null){

        $sql = "SELECT b.idcurso idcurso, c.idserie idserie, a.idturma, b.descricao course, c.descricao serie, a.nome nome, a.abrev name_abrev, a.status
        FROM acd_tbturma a, acd_tbcurso b, acd_tbserie c WHERE a.idserie = c.idserie AND c.idcurso = b.idcurso $where $group $order $limit"; //echo "$sql\n";

        $ret = $this->db->Execute($sql); //echo $sql;

        if($ret)
                return array('success' => true, 'message' => '', 'data' => $ret);
            else
                return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }

    public function statusClass($classID,$newStatus){

        $sql = "UPDATE acd_tbturma SET `status` = '$newStatus' WHERE `idturma` = $classID"; //echo $sql;

        $ret = $this->db->Execute($sql); //echo "{$sql}\n";

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');

    }

    public function insertClass($class_name,$name_abrev,$serie) 
    {

        $sql =  "INSERT INTO acd_tbturma(idturma,nome,abrev,idserie) VALUES (default,'$class_name','$name_abrev',$serie)"; //echo $sql; die();

        $ret = $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        }

        return $ret;

    }

    public function updateClass($classID,$class_name,$name_abrev,$serie)
    {

        $sql =  "UPDATE acd_tbturma SET `nome` = '$class_name', `abrev` = '$name_abrev', `idserie` = $serie WHERE `idturma` = $classID"; //echo "$sql\n";

        $ret = $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        }

        return $ret;

    }

}