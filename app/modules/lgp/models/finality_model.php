<?php

if(class_exists('Model')) {
    class DynamicFinalityData_model extends Model {}
} elseif(class_exists('cronModel')) {
    class DynamicFinalityData_model extends cronModel {}
} elseif(class_exists('apiModel')) {
    class DynamicFinalityData_model extends apiModel {}
}

class finality_model extends DynamicFinalityData_model{
    public $database;

    public function __construct()
    {
        parent::__construct();
        $this->database = $this->getConfig('db_connect');

    }   

	
	public function getFinality($where=null,$order=null,$limit=null,$group=null)
	{
		$sql = "SELECT idfinalidade, nome, `status`, `default`
                  FROM lgp_tbfinalidade
				  
                
                 $where $group $order $limit";
		$ret = $this->db->Execute($sql); //echo "{$sql}\n";

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
	}

    public function insertFinality($finalityName,$default) {
        $sql = "INSERT INTO lgp_tbfinalidade(`nome`,`default`) 
                  VALUES('$finalityName',$default)";

        $ret = $this->db->Execute($sql); //echo "{$sql}\n";

        if($ret)
            return array('success' => true, 'message' => '', 'id' => $this->db->Insert_ID());
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }

    public function updateFinality($finalityID,$finalityName,$default) {
        $sql = "UPDATE lgp_tbfinalidade
                   SET `nome` = '$finalityName',
                        `default` = '$default'
                 WHERE idfinalidade = '$finalityID'";

        $ret = $this->db->Execute($sql); //echo "{$sql}\n";

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }

    

    public function changeStatus($finalityID,$newStatus)
    {
        $sql = "UPDATE lgp_tbfinalidade SET `status` = '{$newStatus}' WHERE idfinalidade = {$finalityID}";
        $ret = $this->db->Execute($sql); //echo "{$sql}\n";

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }

}