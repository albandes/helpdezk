<?php

if(class_exists('Model')){
    class dcolor_model extends Model{}
}elseif(class_exists('cronModel')){
    class dcolor_model extends cronModel{}
}elseif(class_exists('apiModel')){
    class dcolor_model extends apiModel{}
}

class color_model extends dcolor_model{
    public $database;

    public function __construct()
    {
        parent::__construct();
        $this->database = $this->getConfig('db_connect');

    }

    //Para as operações seguintes deve ser criada a tabela do programa
    public function getColor($where=null,$order=null,$limit=null,$group=null) {
        $sql = "SELECT `idcolor`,`name`,`status`,`default`
        FROM lmm_tbcolor
        $where $group $order $limit";

        $ret = $this->db->Execute($sql); 

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');

    }

    public function insertColor($color,$status,$default) {
        $sql = "INSERT INTO lmm_tbcolor(`name`,`status`,`default`) 
                VALUES('$color',$status,'$default')";
    
        $ret = $this->db->Execute($sql); 
    
            if($ret)
                return array('success' => true, 'message' => '', 'id' => $this->db->Insert_ID());
            else
                return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }

    public function updateColor($idcolor,$color,$default){
        $sql = "UPDATE lmm_tbcolor
                    SET `name` = '$color', `default` = '$default'
                    WHERE idcolor = $idcolor";
    
        $ret = $this->db->Execute($sql); 
    
        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
        
    }

    public function deletecolor($idcolor) {
        $sql = "DELETE FROM lmm_tbcolor WHERE idcolor=$idcolor";

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

		$sql = "SELECT idstatus, `name` FROM tbcolor $where ORDER BY `name`";
		$ret = $this->db->Execute($sql); 

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
	}

    public function changeStatus($lmmID,$newStatus)
    {

        $sql = "UPDATE lmm_tbcolor SET `status` = '{$newStatus}' WHERE idcolor = {$lmmID}";
        $ret = $this->db->Execute($sql);  

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }


    public function changePadrao($lmmID)
    {
        $sql = "UPDATE lmm_tbcolor SET `default` = 'Y' WHERE idcolor = {$lmmID}";
        $ret = $this->db->Execute($sql); 

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }


    public function removerPadrao()
    {
        $sql = "UPDATE lmm_tbcolor SET `default` = 'N'";
        $ret = $this->db->Execute($sql);

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }
      

}?>