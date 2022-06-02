<?php

if(class_exists('Model')){
    class dcollection_model extends Model{}
}elseif(class_exists('cronModel')){
    class dcollection_model extends cronModel{}
}elseif(class_exists('apiModel')){
    class dcollection_model extends apiModel{}
}

class collection_model extends dcollection_model{
    public $database;

    public function __construct()
    {
        parent::__construct();
        $this->database = $this->getConfig('db_connect');

    }

    //Para as operações seguintes deve ser criada a tabela do programa
    public function getCollection($where=null,$order=null,$limit=null,$group=null) {
        $sql = "SELECT `idcollection`,`name`,`status` 
        FROM lmm_tbcollection
        $where $group $order $limit";

        $ret = $this->db->Execute($sql); 

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');

    }

    public function insertCollection($collection,$status) {
        $sql = "INSERT INTO lmm_tbcollection(`name`,`status`) 
                VALUES('$collection',$status)";
    
        $ret = $this->db->Execute($sql); 
    
            if($ret)
                return array('success' => true, 'message' => '', 'id' => $this->db->Insert_ID());
            else
                return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }

    public function updateCollection($idcollection,$collection){
        $sql = "UPDATE lmm_tbcollection
                    SET `name` = '$collection'
                    WHERE idcollection = $idcollection";
    
        $ret = $this->db->Execute($sql); 
    
        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
        
    }

    public function deleteCollection($idcollection) {
        $sql = "DELETE FROM lmm_tbcollection WHERE idcollection=$idcollection";

        $ret = $this->db->Execute($sql); //echo "{$sql}\n";

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }

    public function getStatus($where = '')
	{
		if (empty($where))
			$where = ' where idstatus != 1';

		$sql = "SELECT idstatus, `name` FROM tbcollection $where ORDER BY `name`";
		$ret = $this->db->Execute($sql); 

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
	}

    public function changeStatus($lmmID,$newStatus)
    {

        $sql = "UPDATE lmm_tbcollection SET `status` = '{$newStatus}' WHERE idcollection = {$lmmID}";
        $ret = $this->db->Execute($sql); 

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }

}

?>