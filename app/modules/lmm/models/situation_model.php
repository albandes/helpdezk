<?php

if(class_exists('Model')){
    class dsituation_model extends Model{}
}elseif(class_exists('cronModel')){
    class dsituation_model extends cronModel{}
}elseif(class_exists('apiModel')){
    class dsituation_model extends apiModel{}
}

class situation_model extends dsituation_model{
    public $database;

    public function __construct()
    {
        parent::__construct();
        $this->database = $this->getConfig('db_connect');

    }

    //Para as operações seguintes deve ser criada a tabela do programa
    public function getSituation($where=null,$order=null,$limit=null,$group=null) {
        $sql = "SELECT `idsituation`,`name`,`status`,`default`
        FROM lmm_tbsituation
        $where $group $order $limit";

        $ret = $this->db->Execute($sql); 

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');

    }

    public function insertSituation($situation,$status,$default) {
        $sql = "INSERT INTO lmm_tbsituation(`name`,`status`,`default`) 
                VALUES('$situation',$status,$default)";
    
        $ret = $this->db->Execute($sql); 
    
            if($ret)
                return array('success' => true, 'message' => '', 'id' => $this->db->Insert_ID());
            else
                return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }

    public function updateSituation($idsituation,$situation,$default){
        $sql = "UPDATE lmm_tbsituation
                    SET `name` = '$situation',`default`='$default'
                    WHERE idsituation = $idsituation";
    
        $ret = $this->db->Execute($sql); 
    
        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
        
    }


    public function deleteSituation($idsituation) {
        $sql = "DELETE FROM lmm_tbsituation WHERE idsituation=$idsituation";

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

		$sql = "SELECT idstatus, `name` FROM tbsituation $where ORDER BY `name`";
		$ret = $this->db->Execute($sql); 

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
	}


    public function changeStatus($lmmID,$newStatus)
    {

        $sql = "UPDATE lmm_tbsituation SET `status` = '{$newStatus}' WHERE idsituation = {$lmmID}";
        $ret = $this->db->Execute($sql);  

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }


    public function changePadrao($lmmID)
    {

        $sql = "UPDATE lmm_tbsituation SET `default` = 'Y' WHERE idsituation = {$lmmID}";
        $ret = $this->db->Execute($sql); 

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }


    
    public function removerPadrao()
    {

        $sql = "UPDATE lmm_tbsituation SET `default` = 'N'";
        $ret = $this->db->Execute($sql); 

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }

}

?>