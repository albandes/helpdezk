<?php

if(class_exists('Model')) {
    class DynamicClassificationData_model extends Model {}
} elseif(class_exists('cronModel')) {
    class DynamicClassificationData_model extends cronModel {}
} elseif(class_exists('apiModel')) {
    class DynamicClassificationData_model extends apiModel {}
}

class classification_model extends DynamicClassificationData_model{
    public $database;

    public function __construct()
    {
        parent::__construct();
        $this->database = $this->getConfig('db_connect');

    }   

	
	public function getClassification($where=null,$order=null,$limit=null,$group=null)
	{
		$sql = "SELECT idclassificacao, nome, `status`
                  FROM lgp_tbclassificacao
				  
                
                 $where $group $order $limit";
		$ret = $this->db->Execute($sql); //echo "{$sql}\n";

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
	}

    public function insertClassification($classificationName,$default) {
        $sql = "INSERT INTO lgp_tbclassificacao(`nome`,`default`) 
                  VALUES('$classificationName',$default)";

        $ret = $this->db->Execute($sql); //echo "{$sql}\n";

        if($ret)
            return array('success' => true, 'message' => '', 'id' => $this->db->Insert_ID());
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }

    public function updateClassification($classificationID,$classificationName,$default) {
        $sql = "UPDATE lgp_tbclassificacao
                   SET `nome` = '$classificationName',
                        `default` = '$default'
                 WHERE idclassificacao = '$classificationID'";

        $ret = $this->db->Execute($sql); //echo "{$sql}\n";

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }

    

    public function changeStatus($classificationID,$newStatus)
    {
        $sql = "UPDATE lgp_tbclassificacao SET `status` = '{$newStatus}' WHERE idclassificacao = {$classificationID}";
        $ret = $this->db->Execute($sql); //echo "{$sql}\n";

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }

}