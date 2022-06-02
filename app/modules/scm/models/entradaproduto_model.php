<?php

//class emailconfig_model extends Model {
if(class_exists('Model')) {
    class DynamicScmEntradaProduto_model extends Model {}
} elseif(class_exists('cronModel')) {
    class DynamicScmEntradaProduto_model extends cronModel {}
} elseif(class_exists('apiModel')) {
    class DynamicScmEntradaProduto_model extends apiModel {}
}

class entradaproduto_model extends DynamicScmEntradaProduto_model
{


    public $database;

    public function __construct()
    {
        parent::__construct();
        $this->database = $this->getConfig('db_connect');
    }

    public function insertEntradaProduto($idPerson,$tipo,$numeroPedido,$numeroNotaFiscal,$valorestotais,$valorestotaisnotafiscal,$dtnotafiscal)
    {
        $insert ="";
        $values ="";



        if($idPerson != 0){
            $values .= " $idPerson,";
            $insert .= " idperson,";
        }
         if($numeroPedido != ""){
                $values .= " '".$numeroPedido."',";
                $insert .=  " numeropedido,";
        }
         if($numeroNotaFiscal != "") {
             $values .= " ' ".$numeroNotaFiscal."',";
             $insert .=  "numeronotafiscal,";
         }




                $sql =  "
                INSERT INTO scm_tbentradaproduto (
                  tipo,
                  $insert
                  valortotal,
                  valornota,
                  datacadastro,
                  dtnotafiscal
                )
                values
                (
                   '".$tipo."',
                    $values
                   $valorestotais,
                   $valorestotaisnotafiscal,
                   NOW(),
                   $dtnotafiscal 
                );

                ";

        $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        }

        return $this->db->Insert_ID( );

    }

    public function insertItemEntradaProduto($idPedidoCompra,$idProduto,$quantidade,$valor)
    {
        $sql =  "
                
                INSERT INTO scm_tbitementradaproduto
                (
                    identradaproduto,
                    idproduto,
                    quantidade,
                    valor
                )
                VALUES
                (
                    $idPedidoCompra,
                    $idProduto,
                    $quantidade,
                    $valor
                );
        ";

        $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        }

        return $idPedidoCompra;

    }

    public function updateEntradaPedido($idEntradaProduto,$idfornecedor,$numeropedido,$numeronotafiscal,$valorestotais,$valorestotaisnotafiscal,$dtnotafiscal)
    {
        $values = "";
        if($idfornecedor != 0){
            $values .= " idperson = $idfornecedor,";

        }
        if($numeropedido != ""){
            $values .= "numeropedido = '".$numeropedido."',";

        }
        if($numeronotafiscal != "") {
            $values .= "numeronotafiscal = '".$numeronotafiscal."',";

        }



        $sql =  "
                UPDATE scm_tbentradaproduto
                SET
                   $values
                    valornota = $valorestotaisnotafiscal,
                    valortotal = $valorestotais,
                    dtnotafiscal = $dtnotafiscal
                WHERE identradaproduto = $idEntradaProduto;
                ";

        $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            return true;
        }

    }

    public function updateItemEntradaPedido($iditementradaproduto,$produto,$quantidade,$valor)
    {

        $sql =  "
                UPDATE scm_tbitementradaproduto
                SET
                    idproduto = $produto,
                    quantidade = $quantidade,
                    valor = $valor
                WHERE iditementradaproduto = $iditementradaproduto;
                ";

        $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            return true;
        }

    }

    public function deleteAllItemEntradaPedido($idEntradaProduto)
    {
        $sql =  "
                DELETE FROM scm_tbitementradaproduto
                 WHERE identradaproduto  = $idEntradaProduto
                ";

        $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            return true;
        }
    }

    public function getEntradaProduto($where = null, $order = null , $group = null , $limit = null )
    {

        $query =   "SELECT  a.*, IFNULL(b.fantasy_name,b.name) AS 'nomefornecedor' 
                      FROM scm_tbentradaproduto a
                 LEFT JOIN scm_viewSupplier b ON b.idperson = a.idperson $where $order $group $limit ";

        $ret = $this->db->Execute($query); //echo "{$query}\n";

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;

    }

    public function getItemEntradaProduto($where = null, $order = null , $group = null , $limit = null )
    {

        $query =   "
                    SELECT 
	                    * 
                    FROM 
                        scm_tbitementradaproduto
                    WHERE  
                       
                         $where $order $group $limit ";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;

    }

    public function getEchoItemEntradaProduto($where = null, $order = null , $group = null , $limit = null )
    {

        $query =   "
                    SELECT 
                       scm_tbproduto.idproduto,
                       scm_tbproduto.nome,
                       scm_tbitementradaproduto.quantidade,
                       scm_tbitementradaproduto.valor
                    FROM
                       scm_tbitementradaproduto,
                       scm_tbproduto
                    WHERE
                       scm_tbitementradaproduto.idproduto = scm_tbproduto.idproduto
                    AND
                       scm_tbitementradaproduto.identradaproduto = $where
                       
                          $order $group $limit ";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;

    }

    public function buscaPerson($idperson){
        $query =   "SELECT name FROM tbperson where idperson = $idperson";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;
    }

    public function getItemPedidoCompra($where = null, $order = null , $group = null , $limit = null)
    {

        $query =   "  SELECT * FROM scm_tbitempedido $where $order $group $limit";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;


    }

    public function getItemPedidoCompraEcho($where = null, $order = null , $group = null , $limit = null)
    {

        $query =   "SELECT 
                       scm_tbproduto.idproduto,
                       scm_tbproduto.nome,
                       scm_tbitempedido.quantidade
                    FROM
                       scm_tbitempedido,
                       scm_tbproduto
                    WHERE
                       scm_tbitempedido.idproduto = scm_tbproduto.idproduto
                    AND
                       scm_tbitempedido.idpedido = $where
                   
                    ";


        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;


    }

    public function getRequestDataImprimir($where = null, $order = null , $group = null , $limit = null )
    {

        $query =   "  SELECT  
                        scm_tbentradaproduto.identradaproduto, 
                        scm_tbentradaproduto.idperson, 
                        scm_tbentradaproduto.tipo,
                        scm_tbentradaproduto.numeropedido,
                        scm_tbentradaproduto.numeronotafiscal,
                        scm_tbentradaproduto.valornota,
                        scm_tbentradaproduto.valortotal,
                        scm_tbentradaproduto.datacadastro,
                        DATE_FORMAT(scm_tbentradaproduto.datacadastro,'%d/%m/%Y') as 'datadecadastro', 
                        tbperson.idperson,
                        tbperson.name as 'nomefornecedor',
                        scm_tbproduto.idproduto,
                        scm_tbproduto.nome as 'nomeproduto',
                        scm_tbitementradaproduto.identradaproduto,
                        scm_tbitementradaproduto.idproduto,
                        scm_tbitementradaproduto.quantidade as 'quantidadeproduto',
                        scm_tbitementradaproduto.valor
                        FROM
							scm_tbentradaproduto
                        INNER JOIN scm_tbitementradaproduto
                        ON 
                             scm_tbentradaproduto.identradaproduto = scm_tbitementradaproduto.identradaproduto  
                        LEFT JOIN scm_tbproduto
                        ON
                             scm_tbitementradaproduto.idproduto = scm_tbproduto.idproduto
                        LEFT JOIN tbperson
                        ON
							tbperson.idperson = scm_tbentradaproduto.idperson $where $order $group $limit ";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;

    }

    public function changeStatus($idPedidoCompra,$newStatus)
    {
        return $this->db->Execute("UPDATE scm_tbpedido set status = '".$newStatus."' where idpedido = ".$idPedidoCompra);
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