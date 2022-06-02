<?php

if(class_exists('Model')){
    class dcdd_model extends Model{}
}elseif(class_exists('cronModel')){
    class dcdd_model extends cronModel{}
}elseif(class_exists('apiModel')){
    class dcdd_model extends apiModel{}
}

class cdd_model extends dcdd_model{
    public $database;

    public function __construct()
    {
        parent::__construct();
        $this->database = $this->getConfig('db_connect');

    }

    //Para as operações seguintes deve ser criada a tabela do programa
    public function getCDD($where=null,$order=null,$limit=null,$group=null) {
        $sql = "SELECT `idcdd`,`code`,`description`,`status` 
        FROM lmm_tbcdd
        $where $group $order $limit";

        $ret = $this->db->Execute($sql); 

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');

    }

    public function insertCDD($code,$descr,$status) {
        $sql = "INSERT INTO lmm_tbcdd(`code`,`description`,`status`) 
                VALUES('$code','$descr',$status)";
    
        $ret = $this->db->Execute($sql); 
    
            if($ret)
                return array('success' => true, 'message' => '', 'id' => $this->db->Insert_ID());
            else
                return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }

    public function updateCDD($idcdd,$code,$descr){
        $sql = "UPDATE lmm_tbcdd
                    SET `code` = '$code',`description`='$descr'
                    WHERE idcdd = $idcdd";
    
        $ret = $this->db->Execute($sql); 
    
        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
        
    }

    public function deleteCDD($idcdd) {
        $sql = "DELETE FROM lmm_tbcdd WHERE idcdd=$idcdd";

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

		$sql = "SELECT idstatus, `code` FROM tbcdd $where ORDER BY `code`";
		$ret = $this->db->Execute($sql); 

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
	}


    public function changeStatus($lmmID,$newStatus)
    {
        $sql = "UPDATE lmm_tbcdd SET `status` = '{$newStatus}' WHERE idcdd = {$lmmID}";
        $ret = $this->db->Execute($sql);

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }

}

?>