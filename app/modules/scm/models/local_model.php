<?php

if(class_exists('Model')) {
    class DynamicScmLocal_model extends Model {}
} elseif(class_exists('cronModel')) {
    class DynamicScmLocal_model extends cronModel {}
} elseif(class_exists('apiModel')) {
    class DynamicScmLocal_model extends apiModel {}
}

class local_model extends DynamicScmLocal_model
{


    public $database;

    public function __construct()
    {
        parent::__construct();
        $this->database = $this->getConfig('db_connect');
    }

    public function insertLocal($nome)
    {
        $sql =  "
                INSERT INTO scm_tblocal (
                  nome,
                  status
                  
                )
                values
                  (
                   
                   '".$nome."',
                   'A'
                  ) ;

                ";

        $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        }

        return $this->db->Insert_ID( );

    }

    public function updateLocal($idlocal,$nome)
    {
        $sql =  "
                UPDATE scm_tblocal
                SET nome = '$nome'
                WHERE idlocal = $idlocal
                ";

        $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            return true;
        }
    }

    public function getLocal($where = null, $order = null , $group = null , $limit = null )
    {

        $query =   "  SELECT * FROM scm_tblocal $where $order $group $limit ";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;

    }



    public function changeStatus($idLocal,$newStatus)
    {
        return $this->db->Execute("UPDATE scm_tblocal set status = '".$newStatus."' where idlocal = ".$idLocal);
    }


}