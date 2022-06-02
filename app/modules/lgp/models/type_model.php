<?php

if(class_exists('Model')) {
    class DynamicTypeData_model extends Model {}
} elseif(class_exists('cronModel')) {
    class DynamicTypeData_model extends cronModel {}
} elseif(class_exists('apiModel')) {
    class DynamicTypeData_model extends apiModel {}
}

class type_model extends DynamicTypeData_model{
    public $database;

    public function __construct()
    {
        parent::__construct();
        $this->database = $this->getConfig('db_connect');

    }   

	
	public function getTipo($where=null,$order=null,$limit=null,$group=null)
	{
		$sql = "SELECT idtipodado, nome, `status`, `default`
                  FROM lgp_tbtipodado
				  
                
                 $where $group $order $limit";
		$ret = $this->db->Execute($sql); //echo "{$sql}\n";

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
	}

    public function insertType($tipoName,$tipoDefault) {
        $sql = "INSERT INTO lgp_tbtipodado(`nome`,`default`) 
                  VALUES('$tipoName',$tipoDefault)";

        $ret = $this->db->Execute($sql); //echo "{$sql}\n";

        if($ret)
            return array('success' => true, 'message' => '', 'id' => $this->db->Insert_ID());
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }

    public function updateType($tipoID,$tipoName,$tipoDefault) {
        $sql = "UPDATE lgp_tbtipodado
                   SET `nome` = '$tipoName',
                        `default` = '$tipoDefault'
                 WHERE idtipodado = '$tipoID'";

        $ret = $this->db->Execute($sql); //echo "{$sql}\n";

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }

    

    public function changeStatus($tipoID,$newStatus)
    {
        $sql = "UPDATE lgp_tbtipodado SET `status` = '{$newStatus}' WHERE idtipodado = {$tipoID}";
        $ret = $this->db->Execute($sql); //echo "{$sql}\n";

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }

}