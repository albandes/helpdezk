<?php

//class emailconfig_model extends Model {
if(class_exists('Model')) {
    class DynamicScmStatus_model extends Model {}
} elseif(class_exists('cronModel')) {
    class DynamicScmStatus_model extends cronModel {}
} elseif(class_exists('apiModel')) {
    class DynamicScmStatus_model extends apiModel {}
}

class status_model extends DynamicScmStatus_model
{


    public $database;

    public function __construct()
    {
        parent::__construct();
        $this->database = $this->getConfig('db_connect');
    }

    public function getStatus($where = null, $group = null , $order = null , $limit = null )
    {

        $query =   "  SELECT a.idstatus, a.nome
                        FROM scm_tbstatus a, scm_tbstatus_has_scm_tbtypestatus b
                       WHERE a.idstatus = b.idstatus 
                      $where $group $order $limit ";

        $ret = $this->db->Execute($query);


        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;

    }

}