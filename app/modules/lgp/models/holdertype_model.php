<?php

if(class_exists('Model')) {
    class DynamicHoldertype_model extends Model {}
} elseif(class_exists('cronModel')) {
    class DynamicHoldertype_model extends cronModel {}
} elseif(class_exists('apiModel')) {
    class DynamicHoldertype_model extends apiModel {}
}

class holdertype_model extends DynamicHoldertype_model{
    public $database;

    public function __construct()
    {
        parent::__construct();
        $this->database = $this->getConfig('db_connect');

    }   

	
	public function getHoldertype($where=null,$order=null,$limit=null,$group=null)
	{
		$sql = "SELECT idtipotitular, nome, `status`
                  FROM lgp_tbtipotitular
				  
                
                 $where $group $order $limit";
		$ret = $this->db->Execute($sql); //echo "{$sql}\n";

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
	}

    public function insertHoldertype($holdertypeName,$default) {
        $sql = "INSERT INTO lgp_tbtipotitular(`nome`,`default`) 
                  VALUES('$holdertypeName',$default)";

        $ret = $this->db->Execute($sql); //echo "{$sql}\n";

        if($ret)
            return array('success' => true, 'message' => '', 'id' => $this->db->Insert_ID());
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }

    public function updateHoldertype($holdertypeID,$holdertypeName,$default) {
        $sql = "UPDATE lgp_tbtipotitular
                   SET `nome` = '$holdertypeName',
                        `default` = '$default'
                 WHERE idtipotitular = '$holdertypeID'";

        $ret = $this->db->Execute($sql); //echo "{$sql}\n";

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }

    

    public function changeStatus($holdertypeID,$newStatus)
    {
        $sql = "UPDATE lgp_tbtipotitular SET `status` = '{$newStatus}' WHERE idtipotitular = {$holdertypeID}";
        $ret = $this->db->Execute($sql); //echo "{$sql}\n";

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }

}