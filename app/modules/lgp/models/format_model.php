<?php

if(class_exists('Model')) {
    class DynamicFormatData_model extends Model {}
} elseif(class_exists('cronModel')) {
    class DynamicFormatData_model extends cronModel {}
} elseif(class_exists('apiModel')) {
    class DynamicFormatData_model extends apiModel {}
}

class format_model extends DynamicFormatData_model{
    public $database;

    public function __construct()
    {
        parent::__construct();
        $this->database = $this->getConfig('db_connect');

    }   

	
	public function getFormat($where=null,$order=null,$limit=null,$group=null)
	{
		$sql = "SELECT idformatocoleta, nome, `status`, `default`
                  FROM lgp_tbformatocoleta
				  
                
                 $where $group $order $limit";
		$ret = $this->db->Execute($sql); //echo "{$sql}\n";

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
	}

    public function insertFormat($formatName,$default) {
        $sql = "INSERT INTO lgp_tbformatocoleta(`nome`,`default`) 
                  VALUES('$formatName',$default)";

        $ret = $this->db->Execute($sql); //echo "{$sql}\n";

        if($ret)
            return array('success' => true, 'message' => '', 'id' => $this->db->Insert_ID());
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }

    public function updateFormat($formatID,$formatName,$default) {
        $sql = "UPDATE lgp_tbformatocoleta
                   SET `nome` = '$formatName',
                        `default` = '$default'
                 WHERE idformatocoleta = '$formatID'";

        $ret = $this->db->Execute($sql); //echo "{$sql}\n";

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }

    

    public function changeStatus($formatID,$newStatus)
    {
        $sql = "UPDATE lgp_tbformatocoleta SET `status` = '{$newStatus}' WHERE idformatocoleta = {$formatID}";
        $ret = $this->db->Execute($sql); //echo "{$sql}\n";

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }

}