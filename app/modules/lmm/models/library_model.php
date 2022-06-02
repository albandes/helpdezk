<?php

if(class_exists('Model')){
    class dlibrary_model extends Model{}
}elseif(class_exists('cronModel')){
    class dlibrary_model extends cronModel{}
}elseif(class_exists('apiModel')){
    class dlibrary_model extends apiModel{}
}

class library_model extends dlibrary_model{
    public $database;

    public function __construct()
    {
        parent::__construct();
        $this->database = $this->getConfig('db_connect');

    }

    //Para as operações seguintes deve ser criada a tabela do programa
    public function getLibrary($where=null,$order=null,$limit=null,$group=null) {
        $sql = "SELECT `idlibrary`,`name`,`status`,`default`
        FROM lmm_tblibrary
        $where $group $order $limit";

        $ret = $this->db->Execute($sql); 

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');

    }

    public function insertLibrary($library,$status,$default) {
        $sql = "INSERT INTO lmm_tblibrary(`name`,`status`,`default`) 
                VALUES('$library',$status,$default)";
    
        $ret = $this->db->Execute($sql); 
    
            if($ret)
                return array('success' => true, 'message' => '', 'id' => $this->db->Insert_ID());
            else
                return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }

    public function updateLibrary($idlibrary,$library,$default){
        $sql = "UPDATE lmm_tblibrary
                    SET `name` = '$library',`default`='$default'
                    WHERE idlibrary = $idlibrary";
    
        $ret = $this->db->Execute($sql);
    
        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
        
    }

    public function deleteLibrary($idlibrary) {
        $sql = "DELETE FROM lmm_tblibrary WHERE idlibrary=$idlibrary";

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

		$sql = "SELECT idstatus, `name` FROM tblibrary $where ORDER BY `name`";
		$ret = $this->db->Execute($sql); 

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
	}


    public function changeStatus($lmmID,$newStatus)
    {
        $sql = "UPDATE lmm_tblibrary SET `status` = '{$newStatus}' WHERE idlibrary = {$lmmID}";
        $ret = $this->db->Execute($sql); 

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }

    public function changePadrao($lmmID)
    {

        $sql = "UPDATE lmm_tblibrary SET `default` = 'Y' WHERE idlibrary = {$lmmID}";
        $ret = $this->db->Execute($sql); 

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }


    public function removerPadrao()
    {

        $sql = "UPDATE lmm_tblibrary SET `default` = 'N'";
        $ret = $this->db->Execute($sql);  

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }

}

?>