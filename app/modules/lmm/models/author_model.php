<?php

if(class_exists('Model')){
    class dauthor_model extends Model{}
}elseif(class_exists('cronModel')){
    class dauthor_model extends cronModel{}
}elseif(class_exists('apiModel')){
    class dauthor_model extends apiModel{}
}

class author_model extends dauthor_model{
    public $database;

    public function __construct()
    {
        parent::__construct();
        $this->database = $this->getConfig('db_connect');

    }

    //Para as operações seguintes deve ser criada a tabela do programa
    public function getAuthor($where=null,$order=null,$limit=null,$group=null) {
        $sql = "SELECT `idauthor`,`name`,`cutter`,`status` 
        FROM lmm_tbauthor
        $where $group $order $limit";

        $ret = $this->db->Execute($sql); 

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');

    }

    public function insertAuthor($author,$cutter,$status) {
        $sql = "INSERT INTO lmm_tbauthor(`name`,`cutter`,`status`) 
                VALUES('$author','$cutter',$status)";
    
        $ret = $this->db->Execute($sql); 
    
            if($ret)
                return array('success' => true, 'message' => '', 'id' => $this->db->Insert_ID());
            else
                return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }

    public function updateAuthor($idauthor,$author,$cutter){
        $sql = "UPDATE lmm_tbauthor
                    SET `name` = '$author',`cutter`='$cutter'
                    WHERE idauthor = $idauthor";
    
        $ret = $this->db->Execute($sql); 
    
        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
        
    }


    public function deleteAuthor($idauthor) {
        $sql = "DELETE FROM lmm_tbauthor WHERE idauthor=$idauthor";

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

		$sql = "SELECT idstatus, `name` FROM tbauthor $where ORDER BY `name`";
		$ret = $this->db->Execute($sql); 

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
	}

    public function changeStatus($lmmID,$newStatus)
    {

        $sql = "UPDATE lmm_tbauthor SET `status` = '{$newStatus}' WHERE idauthor = {$lmmID}";
        $ret = $this->db->Execute($sql);  

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }

}

?>