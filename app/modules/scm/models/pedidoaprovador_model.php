<?php

//class emailconfig_model extends Model {
if(class_exists('Model')) {
    class DynamicScmPedidoAprov_model extends Model {}
} elseif(class_exists('cronModel')) {
    class DynamicScmPedidoAprov_model extends cronModel {}
} elseif(class_exists('apiModel')) {
    class DynamicScmPedidoAprov_model extends apiModel {}
}

class pedidoaprovador_model extends DynamicScmPedidoAprov_model
{


    public $database;

    public function __construct()
    {
        parent::__construct();
        $this->database = $this->getConfig('db_connect');
    }

    public function insertItemPedidoAprovador($idPedidoAprovador,$idProduto,$quantidade)
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
                    $idPedidoAprovador,
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

        return $idPedidoAprovador;

    }

    public function updatePedidoAprovador($idPedidoAprovador,$idStatus)
    {
        $sql =  "
                UPDATE scm_tbpedido
                   SET idstatus    =  $idStatus
                 WHERE idpedido    =  $idPedidoAprovador
                ";

        $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            return true;
        }
    }

    public function updateItemPedidoAprovador($iditempedido,$idStatus)
    {
        $sql =  "
                UPDATE scm_tbitempedido
                   SET idstatus     =  $idStatus
                 WHERE iditempedido =  $iditempedido
                ";

        $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            return true;
        }
    }

    public function updateCotacaoAprovador($idCotacao,$flg_aprovado)
    {
        $sql =  "
                UPDATE scm_tbcotacao
                   SET flg_aprovado    =  $flg_aprovado
                 WHERE idcotacao       =  $idCotacao
                ";

        $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            return true;
        }
    }

    public function deleteAllItemPedidoAprovador($idPedidoAprovador)
    {
        $sql =  "
                DELETE FROM scm_tbitempedido
                 WHERE idpedido  = $idPedidoAprovador
                ";

        $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            return true;
        }
    }

    public function getPedidoAprovador($where = null, $order = null , $group = null , $limit = null )
    {
        $query =   "  SELECT * FROM scm_viewPedido $where $order $group $limit ";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;

    }

    public function getItemPedidoAprovador($where = null, $order = null , $group = null , $limit = null)
    {

        $query =   "SELECT 
                           a.iditempedido,
                           a.idproduto,
                           b.nome,
                           c.nome unidade,
                           a.quantidade,
                           a.idstatus,
                           d.nome AS 'nomestatusitem'
                        FROM
                           scm_tbitempedido a, scm_tbproduto b, scm_tbunidade c, scm_tbstatus d
                       WHERE a.idproduto = b.idproduto
                         AND a.`idstatus` = d.`idstatus`
                         AND b.idunidade = c.idunidade  $where
                   
                    ";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;


    }

    public function getItemPedidoAprovadorEcho($where = null, $order = null , $group = null , $limit = null)
    {

        $query ="
                  SELECT 
                        scm_tbcotacao.idcotacao, 
                        scm_tbcotacao.idperson,
                        scm_tbcotacao.iditempedido, 
                        scm_tbcotacao.valor_unitario, 
                        scm_tbcotacao.valor_total,
                        scm_tbcotacao.arquivo,
                        scm_tbcotacao.flg_aprovado,
                        scm_tbitempedido.*,
                        tbperson.name as 'nomefornecedor'
                    FROM
                       scm_tbitempedido,
                       scm_tbproduto,
                       scm_tbcotacao
                    inner join tbperson
                    on
                    `scm_tbcotacao`.`idperson` = `tbperson`.`idperson`   
                    WHERE
                       scm_tbitempedido.idproduto = scm_tbproduto.idproduto
                    AND
                        scm_tbcotacao.iditempedido =scm_tbitempedido.iditempedido
                    AND
                       scm_tbitempedido.idpedido = $where
                    ORDER BY scm_tbcotacao.iditempedido;
                ";


        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;


    }

    public function changeStatus($idPedidoAprovador,$newStatus)
    {
        return $this->db->Execute("UPDATE scm_tbpedido set status = '".$newStatus."' where idpedido = ".$idPedidoAprovador);
    }

    public function getIdContaContabilAprovador($where = null, $order = null , $group = null , $limit = null)
    {

        $query =   "  SELECT idcontacontabil FROM scm_tbcontacontabil_has_aprovador $where $order $group $limit";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;


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

    public function insertApprovalLog($idpedido,$iduser,$idstatus)
    {
        $sql =  "INSERT INTO scm_tbapprovallog (idpedido,idperson,idstatus,approval_date)
                VALUES ($idpedido,$iduser,$idstatus,NOW())";
        //echo $sql;
        $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            return true;
        }
    }

    public function getAprovador($where = null, $order = null , $group = null , $limit = null )
    {
        $query =   "  SELECT a.idperson, `name` 
                        FROM scm_tbperson_types a
                        JOIN tbperson p
                          ON p.idperson = a.idperson 
                       WHERE a.idtypeperson = 2
                    $where $order $group $limit ";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;

    }

    public function getInCharge($idpedido) {
        $query = "SELECT id_in_charge, `name`, ind_repass
                    FROM scm_tbpedido_in_charge a, tbperson b
                   WHERE a.id_in_charge = b.idperson
                     AND a.idpedido = $idpedido
                     AND a.ind_in_charge = 1";
        $ret = $this->db->Execute($query);
        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query);
            return false ;
        } else {
            return $ret;
        }
    }

    public function insertInCharge($idpedido, $person, $ind, $rep) {
        $query = "INSERT INTO scm_tbpedido_in_charge (idpedido,id_in_charge,ind_in_charge,ind_repass) values ('$idpedido','$person','$ind','$rep')";
        $ret = $this->db->Execute($query);
        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query);
            return false ;
        } else {
            return $ret;
        }
    }

    public function removeIncharge($idpedido) {
        $query = "UPDATE scm_tbpedido_in_charge SET ind_in_charge = '0' WHERE idpedido = '$idpedido'";
        $ret = $this->db->Execute($query);
        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query);
            return false ;
        } else {
            return $ret;
        }
    }

    public function getPedidoAprovadorGrid($where = null, $order = null , $group = null , $limit = null )
    {
        $query =   "SELECT DISTINCT vwp.*, pht.idturma, tur.nome 
                      FROM scm_viewPedido vwp
           LEFT OUTER JOIN scm_tbpedido_has_acd_tbturma pht
                        ON pht.idpedido = vwp.idpedido
           LEFT OUTER JOIN acd_tbturma tur
                        ON tur.idturma = pht.idturma
                      JOIN scm_tbpedido_in_charge pic
                        ON pic.idpedido = vwp.idpedido
                    $where $group $order $limit ";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;

    }

    public function updateItemQuantidade($iditempedido,$qt)
    {
        $sql =  "
                UPDATE scm_tbitempedido
                   SET quantidade     =  $qt
                 WHERE iditempedido =  $iditempedido
                ";

        $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            return true;
        }
    }

}