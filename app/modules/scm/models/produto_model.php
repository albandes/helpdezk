<?php

//class emailconfig_model extends Model {
if(class_exists('Model')) {
    class DynamicScmProduto_model extends Model {}
} elseif(class_exists('cronModel')) {
    class DynamicScmProduto_model extends cronModel {}
} elseif(class_exists('apiModel')) {
    class DynamicScmProduto_model extends apiModel {}
}

class produto_model extends DynamicScmProduto_model
{

    public $database;

    public function __construct()
    {
        parent::__construct();
        $this->database = $this->getConfig('db_connect');
    }

    public function insertProduto($nome,$descricao,$idUnidade,$estoqueInicial,$estoqueAtual,$estoqueMinimo,$codigoBarras)
    {
        $sql =  "
                INSERT INTO scm_tbproduto (
                  nome,
                  descricao,
                  idunidade,
                  estoque_inicial,
                  estoque_atual,
                  estoque_minimo,
                  codigo_barras,
                  status 
                )
                values
                  (
                   '".$nome."',
                   '".$descricao."',
                   '".$idUnidade."',
                   ".$estoqueInicial.",
                   ".$estoqueAtual.",
                   ".$estoqueMinimo.",
                   '".$codigoBarras."',
                   'A'
                  );
                ";

        $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        }

        return $this->db->Insert_ID( );

    }

    public function updateProduto($idProduto,$nome,$descricao,$idUnidade,$estoqueInicial=NULL,$estoqueAtual=NULL,$estoqueMinimo,$codigoBarras)
    {
        $sql =  "
                UPDATE scm_tbproduto
                SET nome            = '$nome',
                    descricao       = '$descricao',
                    idunidade       = '$idUnidade',";

        if($estoqueInicial){$sql .= "estoque_inicial = $estoqueInicial,";}
        if($estoqueAtual){$sql .= "estoque_atual = $estoqueAtual,";}

        $sql .= "estoque_minimo  = $estoqueMinimo,
                    codigo_barras   = '$codigoBarras'
                WHERE idproduto     =  $idProduto
                ";

        $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            return true;
        }
    }

    public function insereImagemProduto($idProduto,$nome)
    {
        $sql =  "
              INSERT INTO scm_tbimagem_produto
                    (idproduto,
                      nome)
                VALUES
                    ($idProduto,
                    '".$nome."');
              
                ";


        $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        }

        return $this->db->Insert_ID( );
    }

    public function updateImagemProduto($id,$nome){
        $sql =  "
                UPDATE scm_tbimagem_produto
                    SET
                      nome = '".$nome."'
                WHERE idimagem = ". $id.";
              
                ";
        $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        }

        return $id;
    }

    public function deleteImagemProduto($idProduto)
    {
        $sql =  "
              DELETE 
              FROM 
                scm_tbimagem_produto
              WHERE 
                idproduto =".$idProduto.";"
        ;


        $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        }

        return $idProduto;
    }

    public function getProduto($where = null, $order = null , $group = null , $limit = null )
    {

        $query =   "  SELECT scm_tbproduto.*, scm_tbunidade.nome as unidade 
                        FROM scm_tbproduto inner join scm_tbunidade on scm_tbproduto.idunidade = scm_tbunidade.idunidade 
                        $where $order $group $limit ";
        $this->logIt('token gerado: '.$query.'  - program: '.$this->program.' - method: '. __METHOD__ ,7,'general',__LINE__);
        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;

    }

    public function getImagemProduto($where = null, $order = null , $group = null , $limit = null )
    {

        $query =   "  SELECT scm_tbimagem_produto.* 
                        FROM scm_tbimagem_produto  
                        $where $order $group $limit ";
        $this->logIt('token gerado: '.$query.'  - program: '.$this->program.' - method: '. __METHOD__ ,7,'general',__LINE__);
        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;

    }

    public function changeStatus($idProduto,$newStatus)
    {
        return $this->db->Execute("UPDATE scm_tbproduto set status = '".$newStatus."' where idproduto = ".$idProduto);
    }

    public function deleteImagem($idimage)
    {
        $sql =  "
              DELETE 
              FROM 
                scm_tbimagem_produto
              WHERE 
                idimagem = $idimage";

        $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        }

        return $idimage;
    }

    public function updateStock($idproduct,$quantity,$type)
    {
        $sql =  "
                CALL scm_updateStock($idproduct,$quantity,$type)
                ";

        $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            return true;
        }
    }

    public function insertProductLog($productID,$oldStock,$newStock,$personID,$ope)
    {
        $sql =  "INSERT INTO scm_tbprodutolog (idproduto,estoque_old,estoque_new,idperson,operation)
                  VALUES($productID,$oldStock,IFNULL(NULL, $newStock),$personID,'$ope')";

        $ret = $this->db->Execute($sql);

        if($ret)
            return array('success' => true, 'message' => '', 'id' => $this->db->Insert_ID());
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }

}