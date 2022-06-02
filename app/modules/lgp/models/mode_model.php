<?php

if(class_exists('Model')) {
    class DynamicModeData_model extends Model {}
} elseif(class_exists('cronModel')) {
    class DynamicModeData_model extends cronModel {}
} elseif(class_exists('apiModel')) {
    class DynamicModeData_model extends apiModel {}
}

class mode_model extends DynamicModeData_model{
    public $database;

    public function __construct()
    {
        parent::__construct();
        $this->database = $this->getConfig('db_connect');

    }   

	
	public function getMode($where=null,$order=null,$limit=null,$group=null)
	{
		$sql = "SELECT idformacoleta, nome, `status`, `default`
                  FROM lgp_tbformacoleta
				  
                
                 $where $group $order $limit";
		$ret = $this->db->Execute($sql); //echo "{$sql}\n";

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
	}

    public function insertMode($modeName,$default) {
        $sql = "INSERT INTO lgp_tbformacoleta(`nome`,`default`) 
                  VALUES('$modeName',$default)";

        $ret = $this->db->Execute($sql); //echo "{$sql}\n";

        if($ret)
            return array('success' => true, 'message' => '', 'id' => $this->db->Insert_ID());
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }

    public function updateMode($modeID,$modeName,$default) {
        $sql = "UPDATE lgp_tbformacoleta
                   SET `nome` = '$modeName',
                        `default` = '$default'
                 WHERE idformacoleta = '$modeID'";

        $ret = $this->db->Execute($sql); //echo "{$sql}\n";

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }

    

    public function changeStatus($modeID,$newStatus)
    {
        $sql = "UPDATE lgp_tbformacoleta SET `status` = '{$newStatus}' WHERE idformacoleta = {$modeID}";
        $ret = $this->db->Execute($sql); //echo "{$sql}\n";

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }

}