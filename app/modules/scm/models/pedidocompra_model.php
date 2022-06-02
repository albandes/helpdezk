<?php

//class emailconfig_model extends Model {
if(class_exists('Model')) {
    class DynamicScmPedido_model extends Model {}
} elseif(class_exists('cronModel')) {
    class DynamicScmPedido_model extends cronModel {}
} elseif(class_exists('apiModel')) {
    class DynamicScmPedido_model extends apiModel {}
}

class pedidocompra_model extends DynamicScmPedido_model
{


    public $database;

    public function __construct()
    {
        parent::__construct();
        $this->database = $this->getConfig('db_connect');
    }

    public function insertPedidoCompra($idPerson,$dataEntrega,$motivo,$aula,$idcreator)
    {

                $sql =  "
                INSERT INTO scm_tbpedido (
                  idperson,
                  idstatus,
                  datapedido,
                  dataentrega,
                  motivo,
                  status,
                  aula,
                  idpersoncreator
                  
                )
                values
                  (
                    $idPerson,
                    1,
                    CURRENT_TIMESTAMP, 
                   '".$dataEntrega."',
                   '".$motivo."',
                   'A',
                   '$aula',
                   $idcreator
                  ) ;

                ";

        $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        }

        return $this->db->Insert_ID( );

    }

    public function insertItemPedidoCompra($idPedidoCompra,$idProduto,$quantidade)
    {
        $sql =  "
                INSERT INTO scm_tbitempedido (
                  idpedido,
                  idproduto,
                  idstatus,
                  quantidade                  
                )
                values
                  (
                    $idPedidoCompra,
                    $idProduto,
                    1,
                    $quantidade
                  ) ;
                ";

        $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        }

        return $idPedidoCompra;

    }

    public function updatePedidoCompra($idPedidoCompra,$dataEntrega,$motivo,$idstatus)
    {
        $sql =  "
                UPDATE scm_tbpedido
                SET dataentrega = '$dataEntrega',
                    motivo      = '$motivo',
                    idstatus    =  $idstatus
                WHERE idpedido  = $idPedidoCompra
                ";

        $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            return true;
        }
    }

    public function deleteAllItemPedidoCompra($idPedidoCompra)
    {
        $sql =  "
                DELETE FROM scm_tbitempedido
                 WHERE idpedido  = $idPedidoCompra
                ";

        $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            return true;
        }
    }

    public function getPedidoCompra($where = null, $order = null , $group = null , $limit = null )
    {

        $query =   "  SELECT * FROM scm_viewPedido $where $order $group $limit ";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;

    }

    public function getRequestDataSemCotacao($where = null, $order = null , $group = null , $limit = null )
    {
        $query =   "  SELECT  
                        scm_tbpedido.idpedido, 
                        scm_tbpedido.idperson, 
                        scm_tbpedido.status,
                        scm_tbpedido.idstatus, 
                        DATE_FORMAT(scm_tbpedido.datapedido,'%d/%m/%Y %H:%i:%s') as 'datapedido', 
                        DATE_FORMAT(scm_tbpedido.dataentrega,'%d/%m/%Y') as 'dataentrega', 
                        scm_tbpedido.motivo,
                        scm_tbcentrocusto.idcentrocusto,
                        scm_tbpedido.idcontacontabil, 
                        scm_tbcontacontabil.nome as 'nomecontacontabil',
                        scm_tbcontacontabil.codigo as 'codigocontacontabil',
                        CONCAT( scm_tbcontacontabil.codigo,' - ' ,scm_tbcontacontabil.nome ) as 'codigonomecontacontabil',
                        scm_tbcentrocusto.nome as 'nomecentrodecusto',
                        scm_tbcentrocusto.codigo as 'codigocentrodecusto',
                        CONCAT( scm_tbcentrocusto.codigo,' - ' ,scm_tbcentrocusto.nome ) as 'codigonomecentrodecusto',
                        scm_tbstatuspedido.nome as 'nomestatus',
                        tbperson.name as 'nomepessoa',
                        scm_tbproduto.idproduto,
                        scm_tbproduto.nome as 'nomeproduto',
                        scm_tbitempedido.idpedido,
                        scm_tbitempedido.idproduto,
                        scm_tbitempedido.quantidade as 'quantidadeproduto',
                        scm_tbitempedido.idstatus,
                        scm_tbstatusitempedido.nome as 'nomestatusitem',
                        CASE WHEN DATEDIFF(scm_tbpedido.dataentrega,CURDATE()) >= 10 THEN 'N' ELSE 'S' END as 'foradoprazo'
                        scm_tbpedido.idpersoncreator,
                        scm_tbpedido.aula
                        FROM
                        scm_tbpedido
                        LEFT JOIN tbperson
                        ON
							tbperson.idperson = scm_tbpedido.idperson
                        INNER JOIN  scm_tbstatus scm_tbstatuspedido
                        ON
							scm_tbstatuspedido.idstatus = scm_tbpedido.idstatus 
                        INNER JOIN  scm_tbcontacontabil
                        ON
                            scm_tbpedido.idcontacontabil = scm_tbcontacontabil.idcontacontabil
                        LEFT JOIN  scm_tbcentrocusto
                        ON
                            scm_tbcentrocusto.idcentrocusto = scm_tbcontacontabil.idcentrocusto
                        INNER JOIN scm_tbitempedido
                        ON 
                             scm_tbpedido.idpedido = scm_tbitempedido.idpedido  
                        INNER JOIN scm_tbproduto
                        ON
                             scm_tbitempedido.idproduto = scm_tbproduto.idproduto
                        INNER JOIN  scm_tbstatus scm_tbstatusitempedido
                        ON
							scm_tbstatusitempedido.idstatus = scm_tbitempedido.idstatus $where $order $group $limit ";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;

    }

    public function getRequestDataComCotacao($where = null, $order = null , $group = null , $limit = null )
    {
        $query =   "  SELECT  
                        scm_tbpedido.idpedido, 
                        scm_tbpedido.idperson, 
                        scm_tbpedido.status,
                        scm_tbpedido.idstatus, 
                        DATE_FORMAT(scm_tbpedido.datapedido,'%d/%m/%Y %H:%i:%s') as 'datapedido', 
                        DATE_FORMAT(scm_tbpedido.dataentrega,'%d/%m/%Y') as 'dataentrega', 
                        scm_tbpedido.motivo,
                        scm_tbcentrocusto.idcentrocusto,
                        scm_tbpedido.idcontacontabil, 
                        scm_tbcontacontabil.nome as 'nomecontacontabil',
                        scm_tbcontacontabil.codigo as 'codigocontacontabil',
                        CONCAT( scm_tbcontacontabil.codigo,' - ' ,scm_tbcontacontabil.nome ) as 'codigonomecontacontabil',
                        scm_tbcentrocusto.nome as 'nomecentrodecusto',
                        scm_tbcentrocusto.codigo as 'codigocentrodecusto',
                        CONCAT( scm_tbcentrocusto.codigo,' - ' ,scm_tbcentrocusto.nome ) as 'codigonomecentrodecusto',
                        scm_tbstatuspedido.nome as 'nomestatus',
                        tbperson.name as 'nomepessoa',
                        tbfornecedor.name as 'nomefornecedor',
                        scm_tbproduto.idproduto,
                        scm_tbproduto.nome as 'nomeproduto',
                        scm_tbitempedido.idpedido,
                        scm_tbitempedido.iditempedido,
                        scm_tbitempedido.quantidade as 'quantidadeproduto',
                        scm_tbitempedido.idstatus,
                        scm_tbstatusitempedido.nome as 'nomestatusitem',
                        scm_tbcotacao.idcotacao, 
                        scm_tbcotacao.idperson as 'fornecedor',
                        scm_tbcotacao.valor_unitario, 
                        scm_tbcotacao.valor_total,
                        scm_tbcotacao.arquivo,
                        scm_tbcotacao.flg_aprovado,
                        CASE WHEN DATEDIFF(scm_tbpedido.dataentrega,CURDATE()) >= 10 THEN 'N' ELSE 'S' END as 'foradoprazo'
                        FROM
                        scm_tbpedido
                        LEFT JOIN tbperson
                        ON
							tbperson.idperson = scm_tbpedido.idperson
                        INNER JOIN  scm_tbstatus scm_tbstatuspedido
                        ON
							scm_tbstatuspedido.idstatus = scm_tbpedido.idstatus 
                        INNER JOIN  scm_tbcontacontabil
                        ON
                            scm_tbpedido.idcontacontabil = scm_tbcontacontabil.idcontacontabil
                        LEFT JOIN  scm_tbcentrocusto
                        ON
                            scm_tbcentrocusto.idcentrocusto = scm_tbcontacontabil.idcentrocusto
                        INNER JOIN scm_tbitempedido
                        ON 
                             scm_tbpedido.idpedido = scm_tbitempedido.idpedido  
                        INNER JOIN scm_tbproduto
                        ON 
                             scm_tbitempedido.idproduto = scm_tbproduto.idproduto
                        INNER JOIN  scm_tbstatus scm_tbstatusitempedido
                        ON
							scm_tbstatusitempedido.idstatus = scm_tbitempedido.idstatus 
						LEFT JOIN scm_tbcotacao
						ON 
						    scm_tbitempedido.iditempedido = scm_tbcotacao.iditempedido
						INNER JOIN tbperson tbfornecedor
						ON 
						    tbfornecedor.idperson = scm_tbcotacao.idperson $where $order $group $limit ";

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
                       scm_tbitempedido.quantidade,
                       scm_tbproduto.estoque_atual
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

    public function changeStatus($idPedidoCompra,$newStatus)
    {
        return $this->db->Execute("UPDATE scm_tbpedido set idstatus = $newStatus where idpedido = ".$idPedidoCompra);
    }

    public function updateMotivo($idpedidocompra,$motivocancelamento)
    {
        $sql =  "
            UPDATE 
                scm_tbpedido
            SET
                motivocancelamento = '".$motivocancelamento."'
            WHERE 
                idpedido = $idpedidocompra;
                ";
        //echo $sql;
        $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            return true;
        }
    }

    public function insertPedidoTurma($idPedidoCompra,$idTurma)
    {
        $sql =  "
                INSERT INTO scm_tbpedido_has_acd_tbturma (idpedido,idturma)
                VALUES($idPedidoCompra,$idTurma)
                ";

        $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        }

        return $idPedidoCompra;

    }

    public function getPedidoTurma($where = null, $order = null , $group = null , $limit = null)
    {

        $query =   "  SELECT idcurso, a.idturma, abrev, b.idserie, c.numero serie, descricao, descricaoabrev  
                        FROM scm_tbpedido_has_acd_tbturma a, acd_tbturma b, acd_tbserie c
                       WHERE a.idturma = b.idturma
                         AND b.idserie = c.idserie
                       $where $order $group $limit";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;


    }

    public function getIdGroup($where = null, $order = null , $group = null , $limit = null)
    {

        $query =   "  SELECT idgroup FROM scm_tbserie_has_group $where $order $group $limit";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;


    }

    public function getHolidays($where = null, $order = null , $group = null , $limit = null)
    {

        $query =   "  SELECT idholiday, holiday_date, DATE_FORMAT(holiday_date,'%d/%m/%Y') holiday_br, holiday_description  
                        FROM tbholiday
                       $where $order $group $limit";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;


    }

    public function deletePedidoCompra($idPedidoCompra)
    {
        $sql =  "
                DELETE FROM scm_tbpedido
                 WHERE idpedido  = $idPedidoCompra
                ";

        $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            return true;
        }
    }

    public function deleteTurmaPedidoCompra($idPedidoCompra)
    {
        $sql =  "
                DELETE FROM scm_tbpedido_has_acd_tbturma
                 WHERE idpedido  = $idPedidoCompra
                ";

        $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            return true;
        }
    }

    public function getPersonDepartment($where = null, $order = null , $group = null , $limit = null)
    {

        $query =   " SELECT a.iddepartment, name 
                       FROM hdk_tbdepartment_has_person a, hdk_tbdepartment b
                      WHERE a.iddepartment = b.iddepartment
                       $where $order $group $limit";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;


    }

    public function getPedidoMaster($where = null, $order = null , $group = null , $limit = null)
    {

        $query =   "SELECT vw.*, DATE_FORMAT(vw.datapedido,'%d/%m/%Y %H:%i:%s') as 'fmt_datapedido', 
                            DATE_FORMAT(vw.dataentrega,'%d/%m/%Y') as 'fmt_dataentrega' 
                      FROM scm_viewPedido vw
           LEFT OUTER JOIN acd_tbturma t
                        ON t.idturma = vw.idturma
           LEFT OUTER JOIN scm_tbserie_has_group shg
                        ON shg.idserie = t.idserie
                       $where $order $group $limit";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;


    }

    public function getPedidoDetail($where = null, $order = null , $group = null , $limit = null)
    {

        $query =   "SELECT 
                            a.iditempedido,
                            c.nome nomeproduto,
                            a.quantidade,
                            b.nome stitem
                      FROM scm_tbitempedido a
                      JOIN scm_tbstatus b
                        ON b.idstatus = a.idstatus
                      JOIN scm_tbproduto c
                        ON a.idproduto = c.idproduto
           LEFT OUTER JOIN scm_tbcotacao d
                        ON d.iditempedido = a.iditempedido
                    $where $group $order $limit";


        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;


    }

    public function getPedidoProduto($idproduto)
    {

        $query =   "SELECT idpedido FROM scm_tbitempedido WHERE idproduto = $idproduto";
        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;


    }

    public function getIdPedidoCarrier($where = null, $order = null , $group = null , $limit = null)
    {

        $query =   "SELECT DISTINCT b.idpedido 
                      FROM scm_tbcotacao a, scm_tbitempedido b, scm_viewPedido c
                     WHERE a.iditempedido = b.iditempedido
                     AND b.idpedido = c.idpedido
                     $where $group $order $limit";
        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;


    }

    public function getAllowOpenOther($where = null, $order = null , $group = null , $limit = null)
    {
        $query =   "SELECT idopenrequestother 
                      FROM scm_tbopen_request_other 
                    $where $group $order $limit";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            return $this->makeErrorMessage( __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;

    }

    function makeErrorMessage($line,$method,$error,$query='')
    {
        $aRet = array(
            "status" => 'Error',
            "message" => "[DB Error] method: " . $method . ", line: " . $line . ", Db message: " . $error . ", Query: " . $query
        );
        return $aRet;
    }

    public function getPersonsByDepartment($where = null, $order = null , $group = null , $limit = null)
    {

        $query =   " SELECT DISTINCT a.idperson, b.`name` 
                       FROM hdk_tbdepartment_has_person a, tbperson b
                      WHERE a.idperson = b.idperson
                       $where $order $group $limit";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            return $this->makeErrorMessage( __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;


    }

    public function checkProductStock($where = null, $order = null , $group = null , $limit = null)
    {
        $sql =   "SELECT idproduto, estoque_atual 
                    FROM scm_tbproduto
                  $where $order $group $limit";

        $ret = $this->db->Execute($sql);

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');


    }
}