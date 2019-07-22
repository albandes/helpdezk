<?php
if(class_exists('Model')) {
    class DynamicHome_model extends Model {}
} elseif(class_exists('cronModel')) {
    class DynamicHome_model extends cronModel {}
} elseif(class_exists('apiModel')) {
    class DynamicHome_model extends apiModel {}
}

class home_model extends DynamicHome_model
{


    public $database;

    public function __construct()
    {
        parent::__construct();
        $this->database = $this->getConfig('db_connect');

    }

    public function getRequestsCount($idperson,$idstatus){
        $ret = $this->select("SELECT COUNT(distinct a.code_request) as count
								FROM hdk_tbrequest a, hdk_tbstatus b, hdk_tbrequest_in_charge c
								WHERE a.idstatus = b.idstatus
								AND b.idstatus_source = $idstatus
								AND a.idperson_owner = $idperson
								and c.ind_in_charge = 1
								AND c.code_request = a.code_request");
        return $ret->fields['count'];
    }

    public function getTotalRequestsByPerson($idperson){
        $ret = $this->select("SELECT COUNT(distinct a.code_request) as count
								FROM hdk_tbrequest a, hdk_tbstatus b, hdk_tbrequest_in_charge c
								WHERE a.idstatus = b.idstatus
								AND a.idperson_owner = $idperson
								and c.ind_in_charge = 1
								AND c.code_request = a.code_request");
        return $ret->fields['count'];
    }
}