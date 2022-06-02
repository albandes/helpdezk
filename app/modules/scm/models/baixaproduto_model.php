<?php

//class emailconfig_model extends Model {
if(class_exists('Model')) {
    class DynamicScmBaixaProduto_model extends Model {}
} elseif(class_exists('cronModel')) {
    class DynamicScmBaixaProduto_model extends cronModel {}
} elseif(class_exists('apiModel')) {
    class DynamicScmBaixaProduto_model extends apiModel {}
}

class baixaproduto_model extends DynamicScmBaixaProduto_model
{


    public $database;

    public function __construct()
    {
        parent::__construct();
        $this->database = $this->getConfig('db_connect');
    }

    public function insertBaixa($tipo,$idperson,$motivo,$destino)
    {
        $query =   "INSERT INTO scm_tbbaixa (tipo,idperson,motivo,iddestinobaixa,dtcadastro) 
                         VALUES('{$tipo}',{$idperson},'{$motivo}',$destino,NOW())";
        $ret = $this->db->Execute($query);

        if($ret)
            return array('success' => true, 'message' => '', 'id' => $this->db->Insert_ID());
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$query}", 'data' => '');

    }

    public function insertItemBaixa($idbaixa,$idProduto,$quantidade)
    {
        $query =   "INSERT INTO scm_tbitembaixa (idbaixa,idproduto,quantidade) 
                         VALUES({$idbaixa},{$idProduto},{$quantidade})";
        $ret = $this->db->Execute($query);

        if($ret)
            return array('success' => true, 'message' => '', 'id' => $this->db->Insert_ID());
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$query}", 'data' => '');
    }

    public function updateBaixa($baixaID,$motivo,$destino)
    {
        $query =   "UPDATE scm_tbbaixa 
                       SET motivo = '$motivo',
                            iddestinobaixa = $destino 
                     WHERE idbaixa = $baixaID";
        $ret = $this->db->Execute($query);

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$query}", 'data' => '');

    }

    public function deleteAllItemBaixa($baixaID)
    {
        $query =   "DELETE FROM scm_tbitembaixa WHERE idbaixa = $baixaID";
        $ret = $this->db->Execute($query); //echo "{$query}\n";

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$query}", 'data' => '');
    }

    public function getBaixas($where=null,$order=null,$limit=null,$group=null)
    {
        $query =   "SELECT idbaixa, tipo, IF(tipo = 'D','Doação','Descarte') tipo_fmt,
                            a.idperson, b.name responsavel, a.iddestinobaixa, dtcadastro, motivo,
                            c.nome destino
                      FROM scm_tbbaixa a, tbperson b, scm_tbdestinobaixa c
                     WHERE a.idperson = b.idperson
                       AND a.iddestinobaixa = c.iddestinobaixa
                    $where $group $order $limit";
        $ret = $this->db->Execute($query); //echo "{$query}\n";

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$query}", 'data' => '');

    }

    public function getItemBaixa($where=null,$order=null,$limit=null,$group=null)
    {
        $query =   "SELECT iditembaixa, idbaixa,a.idproduto, quantidade, b.nome nome_produto
                      FROM scm_tbitembaixa a, scm_tbproduto b
                     WHERE a.idproduto = b.idproduto
                    $where $group $order $limit";
        $ret = $this->db->Execute($query); //echo "{$query}\n";

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$query}", 'data' => '');

    }

    public function getDestinoBaixa($where=null,$order=null,$limit=null,$group=null){
        $query =   "SELECT iddestinobaixa,nome FROM scm_tbdestinobaixa $where $group $order $limit";
        $ret = $this->db->Execute($query);

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$query}", 'data' => '');
    }

    public function insertDestination($destName)
    {

        $query =   "INSERT INTO scm_tbdestinobaixa (nome) VALUES('{$destName}')";
        $ret = $this->db->Execute($query);

        if($ret)
            return array('success' => true, 'message' => '', 'id' => $this->db->Insert_ID());
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$query}", 'data' => '');

    }

    public function deleteBaixa($baixaID)
    {
        $query =   "DELETE FROM scm_tbbaixa WHERE idbaixa = $baixaID";
        $ret = $this->db->Execute($query); //echo "{$query}\n";

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$query}", 'data' => '');
    }

    public function updateStock($idproduct,$quantity,$type)
    {
        $query = "CALL scm_updateStock($idproduct,$quantity,$type)";
        $ret = $this->db->Execute($query);

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$query}", 'data' => '');
    }

    public function deleteEntradaProduto($identrada)
    {
        $sql =  "DELETE FROM scm_tbentradaproduto WHERE identradaproduto = $identrada";

        $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            return true;
        }
    }

}