<?php

if(class_exists('Model')) {
    class DynamicScmUnidade_model extends Model {}
} elseif(class_exists('cronModel')) {
    class DynamicScmUnidade_model extends cronModel {}
} elseif(class_exists('apiModel')) {
    class DynamicScmUnidade_model extends apiModel {}
}

class unidade_model extends DynamicScmUnidade_model
{


    public $database;

    public function __construct()
    {
        parent::__construct();
        $this->database = $this->getConfig('db_connect');
    }

    public function insertUnidade($nome)
    {
        $sql =  "
                INSERT INTO scm_tbunidade (
                  nome
                                    
                )
                values
                  (
                   
                   '".$nome."'
                   
                  ) ;

                ";

        $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        }

        return $this->db->Insert_ID( );

    }

    public function updateUnidade($idUnidade,$nome)
    {
        $sql =  "
                UPDATE scm_tbunidade
                SET nome = '$nome'
                WHERE idunidade = $idUnidade
                ";

        $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            return true;
        }
    }

    public function getUnidade($where = null, $order = null , $group = null , $limit = null )
    {

        $query =   "  SELECT * FROM scm_tbunidade $where $order $group $limit ";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;

    }
}