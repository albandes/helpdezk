<?php
if(class_exists('Model')) {
    class dynamicAcdAttendance_model extends Model {}
} elseif(class_exists('cronModel')) {
    class dynamicAcdAttendance_model extends cronModel {}
} elseif(class_exists('apiModel')) {
    class dynamicAcdAttendance_model extends apiModel {}
}

class acdattendance_model extends dynamicAcdAttendance_model
{


    public $database;

    public function __construct()
    {
        parent::__construct();
        $this->database = $this->getConfig('db_connect');
    }

    public function getAttendance($dtentry,$where=null,$order=null,$limit=null)
	{
		$sql = "SELECT DISTINCT a.idattendance,`subject`,`description`,a.idstudent, c.`name` student_name, 
                        personlist,a.dtstart, $dtentry, a.dtestimatedend,a.dtend,
                        GROUP_CONCAT(d.idparent ORDER BY d.idparent) parent_ids, 
                        GROUP_CONCAT(f.name ORDER BY e.idparent) parent_name, hourend, minend
                  FROM acd_tbattendance a
                  JOIN acd_tbstudent b
                    ON (a.idstudent = b.idstudent)
                  JOIN tbperson_profile c
                    ON (b.idperson_profile = c.idperson_profile)
                  JOIN acd_tbattendance_has_parent d
                    ON (a.idattendance = d.idattendance)
                  JOIN acd_tbparent e
                    ON (d.idparent = e.idparent)
                  JOIN tbperson_profile f
                    ON (e.idperson_profile = f.idperson_profile)
                 $where 
                 GROUP BY a.idattendance
                 $order $limit";
		//echo "{$sql}\n";
        return $this->selectPDO($sql);
	}

    public function insertAttendace($studentID,$subject,$personList,$dtStart,$dtEstimated,$hourEnd,$minEnd) {
        $sql = "INSERT INTO acd_tbattendance (idstudent,subject,personlist,dtstart,dtestimatedend,hourend,minend) 
                VALUES (:studentID,:subject,:personList,:dtStart,:dtEstimated,:hourEnd,:minEnd)";
        
        try{
            $this->BeginTransPDO();            
            $sth = $this->dbPDO->prepare($sql);
            $sth->bindParam(":studentID",$studentID);
            $sth->bindParam(":subject",$subject);
            $sth->bindParam(":personList",$personList);
            $sth->bindParam(":dtStart",$dtStart);
            $sth->bindParam(":dtEstimated",$dtEstimated);
            $sth->bindParam(":hourEnd",$hourEnd);
            $sth->bindParam(":minEnd",$minEnd);
            $sth->execute();
            $this->CommitTransPDO();
        }catch(PDOException $ex){
            $this->RollbackTransPDO();
            return array("success"=>false,"message"=>$ex->getMessage()." {$sql}");
        }

        return array("success"=>true,"message"=>"","id"=>$this->lastPDO('acd_tbattendance'));
    }

    public function insertAttParent($attendanceID,$parentID) {
        $sql = "INSERT INTO acd_tbattendance_has_parent (idattendance,idparent) 
                VALUES (:attendanceID,:parentID)";
        
        try{
            $this->BeginTransPDO();            
            $sth = $this->dbPDO->prepare($sql);
            $sth->bindParam(":attendanceID",$attendanceID);
            $sth->bindParam(":parentID",$parentID);
            $sth->execute();
            $this->CommitTransPDO();
        }catch(PDOException $ex){
            $this->RollbackTransPDO();
            return array("success"=>false,"message"=>$ex->getMessage()." {$sql}");
        }

        return array("success"=>true,"message"=>"","id"=>"");
    }

    public function removeAttParent($attendanceID)
    {
        $sql =  "DELETE FROM acd_tbattendance_has_parent WHERE idattendance = :attendanceID";

        try{
            $this->BeginTransPDO();            
            $sth = $this->dbPDO->prepare($sql);
            $sth->bindParam(":attendanceID",$attendanceID);
            $sth->execute();
            $this->CommitTransPDO();
        }catch(PDOException $ex){
            $this->RollbackTransPDO();
            return array("success"=>false,"message"=>$ex->getMessage()." {$sql}");
        }

        return array("success"=>true,"message"=>"","data"=>"");
    }

    public function updateAttendace($attendanceID,$subject,$description,$personList,$dtStart,$dtEstimated,$hourEnd,$minEnd)
    {
        $sql =  "UPDATE acd_tbattendance
                    SET subject = :subject,
                        description = :description,
                        personlist = :personList,
                        dtstart = :dtStart,
                        dtestimatedend = :dtEstimated,
                        hourend = :hourEnd,
                        minend = :minEnd 
                  WHERE idattendance = :attendanceID";

        try{
            $this->BeginTransPDO();            
            $sth = $this->dbPDO->prepare($sql);
            $sth->bindParam(":subject",$subject);
            $sth->bindParam(":description",$description);
            $sth->bindParam(":personList",$personList);
            $sth->bindParam(":dtStart",$dtStart);
            $sth->bindParam(":dtEstimated",$dtEstimated);
            $sth->bindParam(":hourEnd",$hourEnd);
            $sth->bindParam(":minEnd",$minEnd);
            $sth->bindParam(":attendanceID",$attendanceID);
            $sth->execute();
            $this->CommitTransPDO();
        }catch(PDOException $ex){
            $this->RollbackTransPDO();
            return array("success"=>false,"message"=>$ex->getMessage()." {$sql}");
        }

        return array("success"=>true,"message"=>"","data"=>"");
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

    public function getClassesByEnrollmentApp($where=null,$order=null)
    {
        $sql =  "
                  SELECT DISTINCT a.idturma, pipeLatinToUtf8(a.nome) nome, pipeLatinToUtf8(a.abrev) abrev, a.idserie
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

    public function getSerieApp($where=null,$order=null)
    {
        $sql =  "SELECT (CASE idserie
                            WHEN 22  THEN pipeLatinToUtf8(CONCAT(descricaoabrev,' - Bilíngue'))
                            WHEN 3  THEN pipeLatinToUtf8(descricao)
                            WHEN 16  THEN pipeLatinToUtf8(descricao)
                            ELSE pipeLatinToUtf8(descricaoabrev)
                        END) AS nome, 
                        idserie , idcurso 
                   FROM acd_tbserie
                  $where $order";

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