<?php

if(class_exists('Model')) {
    class DynamicStorageData_model extends Model {}
} elseif(class_exists('cronModel')) {
    class DynamicStorageData_model extends cronModel {}
} elseif(class_exists('apiModel')) {
    class DynamicStorageData_model extends apiModel {}
}

class storage_model extends DynamicStorageData_model{
    public $database;

    public function __construct()
    {
        parent::__construct();
        $this->database = $this->getConfig('db_connect');

    }   

	
	public function getStorage($where=null,$order=null,$limit=null,$group=null)
	{
		$sql = "SELECT idarmazenamento, nome, `status`, `default`
                  FROM lgp_tbarmazenamento
				  
                
                 $where $group $order $limit";
		$ret = $this->db->Execute($sql); //echo "{$sql}\n";

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
	}

    public function insertStorage($storageName,$default) {
        $sql = "INSERT INTO lgp_tbarmazenamento(`nome`,`default`) 
                  VALUES('$storageName',$default)";

        $ret = $this->db->Execute($sql); //echo "{$sql}\n";

        if($ret)
            return array('success' => true, 'message' => '', 'id' => $this->db->Insert_ID());
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }

    public function updateStorage($storageID,$storageName,$default) {
        $sql = "UPDATE lgp_tbarmazenamento
                   SET `nome` = '$storageName',
                        `default` = '$default'
                 WHERE idarmazenamento = '$storageID'";

        $ret = $this->db->Execute($sql); //echo "{$sql}\n";

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }

    

    public function changeStatus($storageID,$newStatus)
    {
        $sql = "UPDATE lgp_tbarmazenamento SET `status` = '{$newStatus}' WHERE idarmazenamento = {$storageID}";
        $ret = $this->db->Execute($sql); //echo "{$sql}\n";

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }

}