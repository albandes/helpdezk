<?php

//class emailconfig_model extends Model {
if(class_exists('Model')) {
    class DynamicScmMarca_model extends Model {}
} elseif(class_exists('cronModel')) {
    class DynamicScmMarca_model extends cronModel {}
} elseif(class_exists('apiModel')) {
    class DynamicScmMarca_model extends apiModel {}
}

class marca_model extends DynamicScmMarca_model
{


    public $database;

    public function __construct()
    {
        parent::__construct();
        $this->database = $this->getConfig('db_connect');
    }

    public function insertMarca($nome)
    {
        $sql =  "
                INSERT INTO scm_tbmarca (
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

    public function updateMarca($idMarca,$nome)
    {
        $sql =  "
                UPDATE scm_tbmarca
                SET nome = '$nome'
                WHERE idmarca = $idMarca
                ";

        $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            return true;
        }
    }

    public function getMarca($where = null, $order = null , $group = null , $limit = null )
    {

        $query =   "  SELECT * FROM scm_tbmarca $where $order $group $limit ";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;

    }

}