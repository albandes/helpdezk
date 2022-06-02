<?php

if(class_exists('Model')) {
    class dynamicEMQFeature_model extends Model {}
} elseif(class_exists('cronModel')) {
    class dynamicEMQFeature_model extends cronModel {}
} elseif(class_exists('apiModel')) {
    class dynamicEMQFeature_model extends apiModel {}
}

class emqfeature_model extends dynamicEMQFeature_model
{
    public $database;

    public function __construct()
    {
        parent::__construct();
        $this->database = $this->getConfig('db_connect');

    }

    public function getEmqFeaturesData($where = NULL, $group = NULL, $order = NULL, $limit = NULL){
        $sql = "SELECT session_name, `value` FROM emq_tbconfig 	
                $where $group $order $limit";
        $ret = $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql );
        else
            return $ret;
    }


}
