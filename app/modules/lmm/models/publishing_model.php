<?php

if(class_exists('Model')){
    class dpublishing_model extends Model{}
}elseif(class_exists('cronModel')){
    class dpublishing_model extends cronModel{}
}elseif(class_exists('apiModel')){
    class dpublishing_model extends apiModel{}
}

class publishing_model extends dpublishing_model{
    public $database;

    public function __construct()
    {
        parent::__construct();
        $this->database = $this->getConfig('db_connect');

    }

    //Para as operações seguintes deve ser criada a tabela do programa
    public function getPublishing($where=null,$order=null,$limit=null,$group=null) {
        $sql = "SELECT `idpublishingcompany`,`name`,`status` 
        FROM lmm_tbpublishingcompany
        $where $group $order $limit";

        $ret = $this->db->Execute($sql); 

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');

    }

    public function insertPublishing($publishing,$status) {
        $sql = "INSERT INTO lmm_tbpublishingcompany(`name`,`status`) 
                VALUES('$publishing',$status)";
    
        $ret = $this->db->Execute($sql); 
    
            if($ret)
                return array('success' => true, 'message' => '', 'id' => $this->db->Insert_ID());
            else
                return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }


    public function updatePublishing($idpublishing,$publishing){
        $sql = "UPDATE lmm_tbpublishingcompany
                    SET `name` = '$publishing'
                    WHERE idpublishingcompany = $idpublishing";
    
        $ret = $this->db->Execute($sql); 
    
        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
        
    }


    public function deletePublishing($idpublishing) {
        $sql = "DELETE FROM lmm_tbpublishingcompany WHERE idpublishingcompany=$idpublishing";

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

		$sql = "SELECT idstatus, `name` FROM tbpublishingcompany $where ORDER BY `name`";
		$ret = $this->db->Execute($sql); 

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
	}

    
    public function changeStatus($lmmID,$newStatus)
    {

        $sql = "UPDATE lmm_tbpublishingcompany SET `status` = '{$newStatus}' WHERE idpublishingcompany = {$lmmID}";
        $ret = $this->db->Execute($sql); 

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }

}

?>