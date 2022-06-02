<?php

//class emailconfig_model extends Model {
if(class_exists('Model')) {
    class DynamicScmEstado_model extends Model {}
} elseif(class_exists('cronModel')) {
    class DynamicScmEstado_model extends cronModel {}
} elseif(class_exists('apiModel')) {
    class DynamicScmEstado_model extends apiModel {}
}

class estado_model extends DynamicScmEstado_model
{


    public $database;

    public function __construct()
    {
        parent::__construct();
        $this->database = $this->getConfig('db_connect');
    }

    public function insertEstado($nome)
    {
        $sql =  "
                INSERT INTO scm_tbestado (
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

    public function updateEstado($idEstado,$nome)
    {
        $sql =  "
                UPDATE scm_tbestado
                SET nome = '$nome'
                WHERE idestado = $idEstado
                ";

        $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            return true;
        }
    }

    public function getEstado($where = null, $order = null , $group = null , $limit = null )
    {

        $query =   "  SELECT * FROM scm_tbestado $where $order $group $limit ";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;

    }

}