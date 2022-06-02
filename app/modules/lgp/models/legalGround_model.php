<?php

if(class_exists('Model')) {
    class DynamicLegalGroundData_model extends Model {}
} elseif(class_exists('cronModel')) {
    class DynamicLegalGroundData_model extends cronModel {}
} elseif(class_exists('apiModel')) {
    class DynamicLegalGroundData_model extends apiModel {}
}

class legalGround_model extends DynamicLegalGroundData_model{
    public $database;

    public function __construct()
    {
        parent::__construct();
        $this->database = $this->getConfig('db_connect');

    }   

	
	public function getLegalGround($where=null,$order=null,$limit=null,$group=null)
	{
		$sql = "SELECT idbaselegal, nome, `status`, `default`
                  FROM lgp_tbbaselegal
				  
                
                 $where $group $order $limit";
		$ret = $this->db->Execute($sql); //echo "{$sql}\n";

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
	}

    public function insertLegalGround($legalGroundName,$default) {
        $sql = "INSERT INTO lgp_tbbaselegal(`nome`,`default`) 
                  VALUES('$legalGroundName',$default)";

        $ret = $this->db->Execute($sql); //echo "{$sql}\n";

        if($ret)
            return array('success' => true, 'message' => '', 'id' => $this->db->Insert_ID());
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }

    public function updateLegalGround($legalGroundID,$legalGroundName,$default) {
        $sql = "UPDATE lgp_tbbaselegal
                   SET `nome` = '$legalGroundName',
                        `default` = '$default'
                 WHERE idbaselegal = '$legalGroundID'";

        $ret = $this->db->Execute($sql); //echo "{$sql}\n";

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }

    

    public function changeStatus($legalGroundID,$newStatus)
    {
        $sql = "UPDATE lgp_tbbaselegal SET `status` = '{$newStatus}' WHERE idbaselegal = {$legalGroundID}";
        $ret = $this->db->Execute($sql); //echo "{$sql}\n";

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }

}