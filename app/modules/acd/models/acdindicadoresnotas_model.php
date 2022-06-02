<?php

class acdindicadoresnotas_model extends Model
{

    public $database;

    public function __construct()
    {
        parent::__construct();
        $this->database = $this->getConfig('db_connect');
    }

    public function dropTempTableNotas()
    {
        $sql =  "DROP TABLE IF EXISTS acdindicadornotatmp";

        $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            return true;
        }

    }

    public function createTempTableNotas()
    {
        $sql =  "CREATE TEMPORARY TABLE acdindicadornotatmp (
	                    idacdindicadornotatmp INT PRIMARY KEY AUTO_INCREMENT,
                        ALUMatricula INT, 
                        ALUNome VARCHAR(80),
                        PERCodigo INT, 
                        CURCodigo VARCHAR(5),
                        SERNumero INT,
                        TURNome VARCHAR(10),
                        DISNome VARCHAR(50),
                        DISSigla VARCHAR(5),
                        mediaetapa1 DECIMAL(10,2),
                        mediaetapa2 DECIMAL(10,2),
                        mediaetapa3 DECIMAL(10,2),
                        recetapa1 DECIMAL(10,2),
                        recetapa2 DECIMAL(10,2),
                        recetapa3 DECIMAL(10,2),
                        mediaanual DECIMAL(10,2)
                  )";

        $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            return true;
        }

    }

    public function insertTempTableNotas($idaluno,$alunonome,$periodo,$idcurso,$serie,$turma,$discnome,$dissigla,$media1,$media2,$media3,$rec1,$rec2,$rec3,$mediaanual)
    {

        $query =   "INSERT INTO acdindicadornotatmp 
                                (ALUMatricula,ALUNome,PERCodigo,CURCodigo,SERNumero,TURNome,DISNome,DISSigla,
                                 mediaetapa1,mediaetapa2,mediaetapa3,recetapa1,recetapa2,recetapa3,mediaanual) 
                    VALUES($idaluno,'$alunonome',$periodo,'$idcurso',$serie,'$turma','$discnome','$dissigla',
                           $media1,$media2,$media3,$rec1,$rec2,$rec3,$mediaanual)
                    ";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(),$query);
            return false ;
        } else {
            return true;
        }

    }

    public function getTempNotas($fields,$where = null, $order = null , $group = null , $limit = null)
    {

        $query =   " SELECT ALUMatricula, ALUNome, PERCodigo, CURCodigo, SERNumero, TURNome,
	                        DISNome, DISSigla, 
	                        $fields 
	                        mediaanual
                       FROM acd_tbdisciplina a, acd_tbareaconhecimento b, acdindicadornotatmp c
                      WHERE a.idareaconhecimento = b.idareaconhecimento
                        AND c.DISSigla = a.sigla
                      $where $order $group $limit
                    ";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;

    }

    public function getTempAlunos($where = null, $order = null , $group = null , $limit = null)
    {

        $query =   " SELECT DISTINCT ALUMatricula, ALUNome, TURNome
                       FROM acd_tbdisciplina a, acd_tbareaconhecimento b, acdindicadornotatmp c
                      WHERE a.idareaconhecimento = b.idareaconhecimento
                        AND c.DISSigla = a.sigla
                      $where $order $group $limit
                    ";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;

    }

    public function getCurso($where = null, $order = null , $group = null , $limit = null)
    {

        $query =   " SELECT idcurso, descricao
                       FROM
                            acd_tbcurso
                      $where $order $group $limit
                    ";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;

    }

    public function getSerie($where = null, $order = null , $group = null , $limit = null)
    {

        $query =   " SELECT idserie, numero, descricao, descricaoabrev
                       FROM
                            acd_tbserie
                      $where $order $group $limit
                    ";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;

    }

    public function getArea($where = null, $order = null , $group = null , $limit = null)
    {

        $query =   " SELECT idareaconhecimento, descricao, descricaoabrev, cor
                       FROM
                            acd_tbareaconhecimento
                      $where $order $group $limit
                    ";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;

    }

    public function getDisciplina($where = null, $order = null , $group = null , $limit = null)
    {

        $query =   " SELECT iddisciplina, nome, nomeabrev, sigla, idareaconhecimento, cor
                       FROM
                            acd_tbdisciplina
                      $where $order $group $limit
                    ";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;

    }

    public function getTempDisc($where = null)
    {

        $query =   " SELECT DISTINCT sigla, pipeLatinToUtf8(b.descricao)
                       FROM acd_tbdisciplina a, acd_tbareaconhecimento b, acdindicadornotatmp c
                      WHERE a.idareaconhecimento = b.idareaconhecimento
                        AND c.DISSigla = a.sigla 
                        $where
                   ORDER BY pipeLatinToUtf8(b.descricao), sigla
                    ";

        $ret = $this->db->Execute($query);
        //echo $query;
        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;

    }

    public function getAverage($fields,$where){
        $query =   " SELECT PERCodigo, CURCodigo, SERNumero, DISNome, DISSigla, 
                            $fields 
                       FROM acd_tbdisciplina a, acd_tbareaconhecimento b, acdindicadornotatmp c 
                      WHERE a.idareaconhecimento = b.idareaconhecimento 
                        AND c.DISSigla = a.sigla
                        $where
                    ";

        $ret = $this->db->Execute($query);
        //echo $query;
        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;
    }

    public function getTempArea($where = null)
    {

        $query =   " SELECT DISTINCT a.idareaconhecimento, pipeLatinToUtf8(b.descricao) descricao, pipeLatinToUtf8(b.descricaoabrev) descricaoabrev
                       FROM acd_tbdisciplina a, acd_tbareaconhecimento b, acdindicadornotatmp c
                      WHERE a.idareaconhecimento = b.idareaconhecimento
                        AND c.DISSigla = a.sigla 
                        $where
                   ORDER BY pipeLatinToUtf8(b.descricao)
                    ";

        $ret = $this->db->Execute($query);
        //echo $query;
        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;

    }

    public function getTempMediaArea($fields,$where = null, $order = null , $group = null , $limit = null)
    {

        $query =   " SELECT ALUMatricula, ALUNome, PERCodigo, CURCodigo, SERNumero, TURNome, 
                            b.descricao, b.descricaoabrev, 
	                        $fields 
                       FROM acd_tbdisciplina a, acd_tbareaconhecimento b, acdindicadornotatmp c 
                      WHERE a.idareaconhecimento = b.idareaconhecimento 
                        AND c.DISSigla = a.sigla
                      $where $order $group $limit
                    ";
        //echo $query;
        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;

    }

    public function getAvgMediaArea($fields,$where = null, $order = null , $group = null , $limit = null)
    {

        $query =   " SELECT DISTINCT PERCodigo, CURCodigo, SERNumero, TURNome, 
                            b.descricao, b.descricaoabrev, 
	                        $fields
                       FROM acd_tbdisciplina a, acd_tbareaconhecimento b, acdindicadornotatmp c 
                      WHERE a.idareaconhecimento = b.idareaconhecimento 
                        AND c.DISSigla = a.sigla
                      $where $order $group $limit
                    ";
        //echo $query;
        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;

    }

    public function dropTempProfDisc()
    {
        $sql =  "DROP TABLE IF EXISTS acdindicaprofdisctmp";

        $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            return true;
        }

    }

    public function createTempProfDisc()
    {
        $sql =  "CREATE TEMPORARY TABLE acdindicaprofdisctmp (
	                    idacdindicaprofdisctmp INT PRIMARY KEY AUTO_INCREMENT,
                        PROCodigo INT, 
                        PRONome VARCHAR(80),
                        PERCodigo INT, 
                        CURCodigo VARCHAR(5),
                        SERNumero INT,
                        DISCodigo INT,
                        DISSigla VARCHAR(5),
                        dtstart DATE,
                        dtend DATE
                  )";

        $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            return true;
        }

    }

    public function insertTempProfDisc($idprof,$profnome,$periodo,$idcurso,$serie,$iddisciplina,$dissigla,$dtstart,$dtend)
    {

        $query =   "INSERT INTO acdindicaprofdisctmp 
                                (PROCodigo,PRONome,PERCodigo,CURCodigo,SERNumero,DISCodigo,DISSigla,dtstart,dtend) 
                    VALUES($idprof,'$profnome',$periodo,'$idcurso',$serie,$iddisciplina,'$dissigla','$dtstart','$dtend')
                    ";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(),$query);
            return false ;
        } else {
            return true;
        }

    }

    public function getProfessorDisciplina($periodo,$idcurso,$serie,$dissigla)
    {

        $query =   " SELECT DISTINCT PROCodigo, PRONome, DATE_FORMAT(dtstart,'%d/%m/%Y') dtstart, 
                            DATE_FORMAT(dtend,'%d/%m/%Y') dtend
                       FROM acdindicaprofdisctmp 
                      WHERE PERCodigo = '$periodo'
                        AND CURCodigo IN ($idcurso)
                        AND SERNumero = $serie
                        AND DISSigla = '$dissigla'
                   ORDER BY  dtend ASC, PRONome
                    ";

        $ret = $this->db->Execute($query);
        //echo $query;
        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;

    }

}