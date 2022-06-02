<?php

if(class_exists('Model')){
    class dmaterialtype_model extends Model{}
}elseif(class_exists('cronModel')){
    class dmaterialtype_model extends cronModel{}
}elseif(class_exists('apiModel')){
    class dmaterialtype_model extends apiModel{}
}

class materialtype_model extends dmaterialtype_model{
    public $database;

    public function __construct()
    {
        parent::__construct();
        $this->database = $this->getConfig('db_connect');

    }

    //Para as operações seguintes deve ser criada a tabela do programa
    public function getMaterialtype($where=null,$order=null,$limit=null,$group=null) {
        $sql = "SELECT `idmaterialtype`,`name`,`status` 
        FROM lmm_tbmaterialtype
        $where $group $order $limit";

        $ret = $this->db->Execute($sql); 

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');

    }

    public function insertMaterialtype($nome,$status) {
        $sql = "INSERT INTO lmm_tbmaterialtype(`name`,`status`) 
                VALUES('$nome',$status)";
    
        $ret = $this->db->Execute($sql); 
    
            if($ret)
                return array('success' => true, 'message' => '', 'id' => $this->db->Insert_ID());
            else
                return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }

    public function updateMaterialtype($idmaterialtype,$nome){
        $sql = "UPDATE lmm_tbmaterialtype
                    SET `name` = '$nome'
                    WHERE idmaterialtype = $idmaterialtype";
    
        $ret = $this->db->Execute($sql); 
    
        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
        
    }

    public function deleteMaterialtype($idmaterialtype) {
        $sql = "DELETE FROM lmm_tbmaterialtype where idmaterialtype=$idmaterialtype";

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

		$sql = "SELECT idstatus, `name` FROM tbmaterialtype $where ORDER BY `name`";
		$ret = $this->db->Execute($sql); 

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
	}


    public function changeStatus($lmmID,$newStatus)
    {

        $sql = "UPDATE lmm_tbmaterialtype SET `status` = '{$newStatus}' WHERE idmaterialtype = {$lmmID}";
        $ret = $this->db->Execute($sql); 

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }

}

?>