<?php

class funcionario_model extends Model
{


    public $database;

    public function __construct()
    {
        parent::__construct();
        $this->database = $this->getConfig('db_connect');
    }

    public function insertFuncionario($idEmpresa,$nome,$cargo,$rg,$sexo,$dtnasc,$setor,$empresa,$cpf,$dtadmissao)
    {
        $sql =  "
                INSERT INTO hur_tbfuncionario (
                  idempresa,
                  nome,
                  cargo,
                  identidade,
                  sexo,
                  dtnasc,
                  setor,
                  empresa,
                  cpf,
                  dtadmissao
                )
                VALUES
                  (
                    $idEmpresa,
                    '$nome',
                    '$cargo',
                    '$rg',
                    '$sexo',
                     '$dtnasc',
                    '$setor',
                    '$empresa',
                    '$cpf',
                    '$dtadmissao'
                  ) ;


                ";

        $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            return true;
        }

    }

    public function deleteFuncionario($idEmpresa)
    {
        $sql =  "
                DELETE
                FROM
                  hur_tbfuncionario
                WHERE idempresa = '$idEmpresa' ;

                ";

        $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            return true;
        }
    }

    public function getFuncionario($where = null, $order = null , $group = null , $limit = null )
    {

        $query =   " SELECT
                      idfuncionario,
                      idempresa,
                      nome,
                      cargo,
                      identidade,
                      sexo,
                      dtnasc,
                      DATE_FORMAT(dtnasc, '%d/%m/%Y') AS dtnasc_fmt,
                      setor,
                      empresa,
                      cpf,
                      dtadmissao,
                      DATE_FORMAT(dtadmissao, '%d/%m/%Y') AS dtadmissao_fmt
                    FROM
                      hur_tbfuncionario
                    $where $order $group $limit
                    ";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;

    }

    public function getNumeroDeFuncionarios($where = null)
    {

        $query =   " SELECT
                      count(idempresa) total
                    FROM
                      hur_tbfuncionario
                    $where
                    ";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret->fields['total'];

    }


    public function getTempoAtualizacao()
    {

        $query =    "SELECT TIMESTAMPDIFF(HOUR, (SELECT atualiza FROM hur_atualiza), NOW()) AS tempo;";
        $ret = $this->db->Execute($query);
        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret->fields['tempo'];
    }


    public function updateDataHoraAtualizacao()
    {
        $query =    "UPDATE hur_atualiza SET atualiza=NOW()";
        $ret = $this->db->Execute($query);
        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return true;

    }

    public function getDataAtualizacao()
    {

        $query =    " SELECT pipeFormatDateTime('pt_BR',atualiza) dtatual FROM hur_atualiza ";

        $ret = $this->db->Execute($query);
        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret->fields['dtatual'];
    }

    public function getITMEmployeeData($where=null,$order=null)
    {
        $sql =  "SELECT DISTINCT nome, cpf 
                   FROM hur_tbfuncionario
                 $where $order";

        $ret = $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            return $ret;
        }

    }


}