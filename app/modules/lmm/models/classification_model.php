<?php

if(class_exists('Model')){
    class dclassification_model extends Model{}
}elseif(class_exists('cronModel')){
    class dclassification_model extends cronModel{}
}elseif(class_exists('apiModel')){
    class dclassification_model extends apiModel{}
}

class classification_model extends dclassification_model{
    public $database;

    public function __construct()
    {
        parent::__construct();
        $this->database = $this->getConfig('db_connect');

    }

    //Para as operações seguintes deve ser criada a tabela do programa
    public function getClassification($where=null,$order=null,$limit=null,$group=null) {
        $sql = "SELECT `idclassification`,`name`,`status`,`default`
        FROM lmm_tbclassification
        $where $group $order $limit";

        $ret = $this->db->Execute($sql); 

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');

    }

    public function insertClassification($classification,$status,$default) {
        $sql = "INSERT INTO lmm_tbclassification(`name`,`status`,`default`) 
                VALUES('$classification',$status,$default)";
    
        $ret = $this->db->Execute($sql); 
    
            if($ret)
                return array('success' => true, 'message' => '', 'id' => $this->db->Insert_ID());
            else
                return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }

    public function updateClassification($idclassification,$classification,$default){
        $sql = "UPDATE lmm_tbclassification
                    SET `name` = '$classification', `default` = '$default'
                    WHERE idclassification = $idclassification";
    
        $ret = $this->db->Execute($sql); 
    
        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
        
    }

    public function deleteclassification($idclassification) {
        $sql = "DELETE FROM lmm_tbclassification WHERE idclassification=$idclassification";

        $ret = $this->db->Execute($sql); 

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }


    public function getStatus($where = '')
	{
		if (empty($where))
			$where = ' where idstatus != 1';

		$sql = "SELECT idstatus, `name` FROM tbclassification $where ORDER BY `name`";
		$ret = $this->db->Execute($sql); 

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
	}


    public function changeStatus($lmmID,$newStatus)
    {
        $sql = "UPDATE lmm_tbclassification SET `status` = '{$newStatus}' WHERE idclassification = {$lmmID}";
        $ret = $this->db->Execute($sql);

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }


    public function changePadrao($lmmID)
    {
        $sql = "UPDATE lmm_tbclassification SET `default` = 'Y' WHERE idclassification = {$lmmID}";
        $ret = $this->db->Execute($sql); 

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }


    public function removerPadrao()
    {
        $sql = "UPDATE lmm_tbclassification SET `default` = 'N'";
        $ret = $this->db->Execute($sql); 

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }

}

?>