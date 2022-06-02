<?php


class candidate_model extends Model
{
    public $database;

    public function __construct()
    {
        parent::__construct();
        $this->database = $this->getConfig('db_connect');
    }

    public function getCandidate($where = null, $order = null , $group = null , $limit = null )
    {

        $query =   " SELECT * FROM hur_viewCurriculum
                    $where $order $group $limit
                    ";
        //echo $query;
        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;

    }

    public function getNumCandidates($where = null)
    {

        $query =   " SELECT
                      count(idcurriculum) total
                    FROM
                      hur_viewCurriculum
                    $where
                    ";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret->fields['total'];

    }

    public function getArea($where = null, $order = null , $group = null , $limit = null )
    {

        $query =   "  SELECT * FROM hur_tbarea $where $group $order $limit ";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;

    }

    public function getRole($where = null, $order = null , $group = null , $limit = null )
    {

        $query =   "  SELECT * FROM hur_tbrole $where $group $order $limit ";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;

    }

    public function getCandidateFile($where = null, $order = null , $group = null , $limit = null )
    {

        $query =   "  SELECT * FROM hur_tbcurriculumfile $where $group $order $limit ";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;

    }

    public function getCandidateInitialGrid($where = null, $order = null , $group = 'name' , $limit = null )
    {

        $query =   " SELECT idcurriculum,`name`,IF((COUNT(ssn_cpf) > 1),COUNT(ssn_cpf),role) role,
	                        IF((COUNT(ssn_cpf) > 1),'',exp_role_year) exp_role_year,
	                        dtbirth, dtentry 
                       FROM hur_viewCurriculum
                       $where
                   GROUP BY $group
                     $order  $limit
                    ";
        //echo $query;
        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;

    }
}