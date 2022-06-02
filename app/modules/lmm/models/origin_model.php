<?php

if(class_exists('Model')){
    class dorigin_model extends Model{}
}elseif(class_exists('cronModel')){
    class dorigin_model extends cronModel{}
}elseif(class_exists('apiModel')){
    class dorigin_model extends apiModel{}
}

class origin_model extends dorigin_model{
    public $database;

    public function __construct()
    {
        parent::__construct();
        $this->database = $this->getConfig('db_connect');

    }

    //Para as operações seguintes deve ser criada a tabela do programa
    public function getOrigin($where=null,$order=null,$limit=null,$group=null) {
        $sql = "SELECT `idorigin`,`name`,`status`,`default`
        FROM lmm_tborigin
        $where $group $order $limit";

        $ret = $this->db->Execute($sql); 

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');

    }

    public function insertOrigin($origin,$status,$default) {
        $sql = "INSERT INTO lmm_tborigin(`name`,`status`,`default`) 
                VALUES('$origin',$status,$default)";
    
        $ret = $this->db->Execute($sql);
    
            if($ret)
                return array('success' => true, 'message' => '', 'id' => $this->db->Insert_ID());
            else
                return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }

    public function updateOrigin($idorigin,$origin,$default){
        $sql = "UPDATE lmm_tborigin
                    SET `name` = '$origin',`default`='$default'
                    WHERE idorigin = $idorigin";
    
        $ret = $this->db->Execute($sql); 
    
        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
        
    }

    public function deleteOrigin($idorigin) {
        $sql = "DELETE FROM lmm_tborigin WHERE idorigin=$idorigin";

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

		$sql = "SELECT idstatus, `name` FROM tborigin $where ORDER BY `name`";
		$ret = $this->db->Execute($sql); 

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
	}


    public function changeStatus($lmmID,$newStatus)
    {
        $sql = "UPDATE lmm_tborigin SET `status` = '{$newStatus}' WHERE idorigin = {$lmmID}";
        $ret = $this->db->Execute($sql);  

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }



    public function changePadrao($lmmID)
    {
        $sql = "UPDATE lmm_tborigin SET `default` = 'Y' WHERE idorigin = {$lmmID}";
        $ret = $this->db->Execute($sql); 

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }


    public function removerPadrao()
    {

        $sql = "UPDATE lmm_tborigin SET `default` = 'N'";
        $ret = $this->db->Execute($sql); 

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }

}

?>