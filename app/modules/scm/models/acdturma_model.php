<?php

if(class_exists('Model')) {
    class DynamicScmTurma_model extends Model {}
} elseif(class_exists('cronModel')) {
    class DynamicScmTurma_model extends cronModel {}
} elseif(class_exists('apiModel')) {
    class DynamicScmTurma_model extends apiModel {}
}

class acdturma_model extends DynamicScmTurma_model
{


    public $database;

    public function __construct()
    {
        parent::__construct();
        $this->database = $this->getConfig('db_connect');
    }

    public function getTurma($where = null, $order = null , $group = null , $limit = null )
    {
        $sql =  "
                SELECT idturma, nome, abrev, idserie, numero FROM scm_tbturma  
                 $where $group $order $limit";

        $ret = $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        }

        return $ret;

    }

    public function getTurmaData($where = null, $order = null , $group = null , $limit = null )
    {
        $sql =  "
                SELECT idturma, nome, b.idcurso, abrev, a.idserie, b.numero serie, a.numero
                      FROM scm_tbturma a, scm_tbserie b
                     WHERE a.idserie = b.idserie  
                 $where $group $order $limit";

        $ret = $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        }

        return $ret;

    }

    public function getTurmaDePara($where = null, $order = null , $group = null , $limit = null )
    {
        $sql =  "
                SELECT * FROM scm_tbdepara_bilingue_esporte $where $group $order $limit";

        $ret = $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        }

        return $ret;

    }

    public function getTurmaCondition($where = null, $order = null , $group = null , $limit = null )
    {
        $sql =  "
                SELECT * FROM scm_tbturma_condition $where $group $order $limit";

        $ret = $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        }

        return $ret;

    }

}