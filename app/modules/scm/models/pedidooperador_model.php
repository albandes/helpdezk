<?php

if(class_exists('Model')) {
    class DynamicScmPedidoOpe_model extends Model {}
} elseif(class_exists('cronModel')) {
    class DynamicScmPedidoOpe_model extends cronModel {}
} elseif(class_exists('apiModel')) {
    class DynamicScmPedidoOpe_model extends apiModel {}
}

class pedidooperador_model extends DynamicScmPedidoOpe_model
{

    public $database;

    public function __construct()
    {
        parent::__construct();
        $this->database = $this->getConfig('db_connect');
    }

    public function insertItemPedidoOperador($idPedidoOperador,$idProduto,$quantidade)
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
                    $idPedidoOperador,
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

        return $idPedidoOperador;

    }

    public function insertPedidoOperadorCotacao($iditempedidos,$fornecedores,$valoresunitarios,$valorestotais,$valoresfrete,$flacarrier=null)
    {
        if($flacarrier){$fields =", flg_carrier"; $val = ", '$flacarrier'";}
        else{$fields =", flg_carrier"; $val = ", 'N'";}
        $sql =  "
                INSERT INTO scm_tbcotacao
                    (
                        idperson,
                        iditempedido,
                        valor_unitario,
                        valor_total,
                        valor_frete
                        $fields
                    )
                VALUES
                    (
                        $fornecedores,
                        $iditempedidos,
                        $valoresunitarios,
                        $valorestotais,
                        $valoresfrete
                        $val
                    );
                ";

        $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        }

        return $this->db->Insert_ID( );

    }

    public function updatePedidoOperadorCotacao($idCotacao,$fornecedores,$valoresunitarios,$valorestotais,$valoresfrete,$flacarrier=null)
    {
        if($flacarrier){$fields =", flg_carrier = '$flacarrier'";}
        else{$fields =", flg_carrier = 'N'";}
        $sql =  "
                UPDATE 
                    scm_tbcotacao
                SET
                    idperson       =    $fornecedores,
                    valor_unitario =    $valoresunitarios,
                    valor_total    =    $valorestotais,
                    valor_frete    =    $valoresfrete
                    $fields
                WHERE idcotacao    =    $idCotacao
                ";

        $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        }

        return $idCotacao;

    }

    public function updatePedidoOperadorCotacaoNomeArquivo($idcotacao,$nomeArquivo)
    {
        $sql =  '
                UPDATE scm_tbcotacao
                SET
                    arquivo = "'.$nomeArquivo.'"
                    
                WHERE idcotacao = '.$idcotacao.';
                ';

        $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        }

        return $idcotacao;

    }

    public function deletePedidoOperadorCotacao($iditempedidos)
    {
        $sql =  "
                DELETE FROM 
                      scm_tbcotacao
                WHERE
                      iditempedido = '.$iditempedidos.'
                ";

        $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        }else {
            return true;
        }
    }

    public function updateItemPedidoOperadorStatus($iditempedido,$idStatus)
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

    public function updatePedidoOperador($idPedidoOperador,$dataEntrega,$motivo,$idStatus,$idContacontabil=null)
    {
        if($idContacontabil){$condCC = ', idcontacontabil = '.$idContacontabil;}
        else{$condCC = '';}

        $sql =  "
                UPDATE scm_tbpedido
                   SET dataentrega     = '$dataEntrega',
                       motivo          = '$motivo',
                       idstatus        =  $idStatus
                       $condCC
                 WHERE idpedido        =  $idPedidoOperador
                ";

        $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            return true;
        }
    }

    public function updateItemPedidoOperador($idItemPedidoOperador,$idproduto,$quantity)
    {
        $sql =  "
                UPDATE scm_tbitempedido
                   SET idproduto = $idproduto,
                       quantidade      = $quantity
                 WHERE iditempedido    = $idItemPedidoOperador
                ";

        $ret = $this->db->Execute($sql);

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }

    public function deleteAllItemPedidoOperador($idPedidoOperador)
    {
        $sql =  "
                DELETE FROM scm_tbitempedido
                 WHERE idpedido  = $idPedidoOperador
                ";

        $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            return true;
        }
    }

    public function getPedidoOperador($where = null, $order = null , $group = null , $limit = null )
    {
        $query =   "  SELECT * FROM scm_viewPedido $where $order $group $limit ";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;

    }

    public function getItemPedidoOperador($where = null, $order = null , $group = null , $limit = null)
    {

        $query =   "  SELECT * FROM scm_tbitempedido $where $order $group $limit";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;


    }

    public function getItemPedidoOperadorEcho($where = null, $order = null , $group = null , $limit = null)
    {

        $query =   "SELECT 
                           a.iditempedido,
                           a.idproduto,
                           b.nome,
                           c.nome unidade,
                           a.quantidade,
                           a.idstatus,
                           d.nome AS 'nomestatusitem',
                           b.estoque_atual
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

    public function getItemPedidoOperadorCotacaoEcho($where = null, $order = null , $group = null , $limit = null)
    {
        $query ="
                  SELECT 
                        scm_tbcotacao.idcotacao, 
                        scm_tbcotacao.idperson,
                        scm_tbcotacao.iditempedido, 
                        scm_tbcotacao.valor_unitario, 
                        scm_tbcotacao.valor_total,
                        scm_tbcotacao.valor_frete,
                        scm_tbcotacao.arquivo,
                        scm_tbcotacao.flg_aprovado,
                        scm_tbcotacao.flg_carrier,
                        scm_tbitempedido.*,
                        IF((vwsup.fantasy_name IS NOT NULL AND vwsup.fantasy_name != ''),vwsup.fantasy_name,vwsup.name) AS 'nomefornecedor'
                    FROM
                       scm_tbitempedido,
                       scm_tbproduto,
                       scm_tbcotacao
                    INNER JOIN scm_viewSupplier vwsup
                    on
                    `scm_tbcotacao`.`idperson` = `vwsup`.`idperson`  
                    WHERE
                       scm_tbitempedido.idproduto = scm_tbproduto.idproduto
                    AND
                        scm_tbcotacao.iditempedido =scm_tbitempedido.iditempedido
                    AND
                       scm_tbitempedido.idpedido = $where
                    ORDER BY scm_tbcotacao.iditempedido
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
                        CASE WHEN DATEDIFF(scm_tbpedido.dataentrega,CURDATE()) >= 10 THEN 'N' ELSE 'S' END as 'foradoprazo',
                        pht.idturma, tur.nome as 'nometurma',
                        scm_tbitempedido.iditempedido
                        FROM
                        scm_tbpedido
                        LEFT JOIN tbperson
                        ON
							tbperson.idperson = scm_tbpedido.idperson
                        INNER JOIN  scm_tbstatus scm_tbstatuspedido
                        ON
							scm_tbstatuspedido.idstatus = scm_tbpedido.idstatus 
                        LEFT OUTER JOIN  scm_tbcontacontabil
                        ON
                            scm_tbpedido.idcontacontabil = scm_tbcontacontabil.idcontacontabil
                        LEFT OUTER JOIN  scm_tbcentrocusto
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
						LEFT OUTER JOIN scm_tbpedido_has_acd_tbturma pht
                        ON pht.idpedido = scm_tbpedido.idpedido
                        LEFT OUTER JOIN acd_tbturma tur
                        ON tur.idturma = pht.idturma $where $order $group $limit ";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;

    }

    public function changeStatus($idPedidoOperador,$newStatus)
    {
        return $this->db->Execute("UPDATE scm_tbpedido set idstatus = ".$newStatus." where idpedido = ".$idPedidoOperador);
    }

    public function getGrupoOperador($where = null, $order = null , $group = null , $limit = null)
    {
        $query =   "SELECT ghp.idgroup, pg.name
                      FROM hdk_tbgroup_has_person ghp, hdk_tbgroup grp, tbperson p, tbperson pg
                     WHERE ghp.idgroup = grp.idgroup
                       AND grp.idperson = pg.idperson
                       AND ghp.idperson = p.idperson
                       $where $order $group $limit";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;

    }

    public function getSCMTypePerson($where = null, $order = null , $group = null , $limit = null)
    {
        $query =   "SELECT a.idtypeperson 
                      FROM scm_tbperson_types a, tbperson b 
                     WHERE a.idperson = b.idperson
                     $where $order $group $limit";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret->fields['idtypeperson'];

    }

    public function getTurmaGrupo($idgroup)
    {
        $query =   "SELECT idturma, nome, b.idcurso, b.numero
                      FROM scm_tbserie_has_group a, acd_tbserie b, acd_tbturma c
                     WHERE a.idserie = b.idserie
                       AND b.idserie = c.idserie
                       AND a.idgroup IN ($idgroup)
                    ORDER BY b.idcurso,b.numero,c.numero";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;

    }

    public function getPedidoOperadorGrid($where = null, $order = null , $group = null , $limit = null )
    {
        $query =   "SELECT vwp.*, pht.idturma, tur.nome 
                      FROM scm_viewPedido vwp
           LEFT OUTER JOIN scm_tbpedido_has_acd_tbturma pht
                        ON pht.idpedido = vwp.idpedido
           LEFT OUTER JOIN acd_tbturma tur
                        ON tur.idturma = pht.idturma
                    $where $order $group $limit ";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;

    }

    public function getIdAprovador($where = null, $order = null , $group = null , $limit = null)
    {

        $query =   "  SELECT idperson FROM scm_tbcontacontabil_has_aprovador $where $order $group $limit";

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

    public function insertPedidoNote($code, $person, $note, $date, $visibility)
    {

        if($this->database == 'oci8po'){
            //$vSQL = "insert into scm_tbnote (code_request,idperson,description,entry_date,minutes,start_hour,finish_hour,execution_date,hour_type,service_value,public_,idtype,ip_adress,callback, idnote_attachment) values ('$code', '$person', TO_CLOB('$note'), $date, '$totalminutes', '$starthour', '$finishour', $execdate, '$hourtype', $serviceval, '$public', '$idtype', '$ipadress', '$callback', $idanexo)";
            $vSQL = "
                    DECLARE
                        clobVar CLOB := '$note';
                    BEGIN
                        INSERT INTO scm_tbnote (idpedido,idperson,description,entry_date,user_visibility) VALUES('$code', '$person',clobVar, $date,'$visibility');
                    END;
                    " ;

        }elseif($this->isMysql($this->database)){
            $vSQL = "insert into scm_tbnote (idpedido,idperson,description,entry_date,user_visibility) values ('$code', '$person', '$note', $date,'$visibility')";
        }

        $ret = $this->db->Execute($vSQL);
        if (!$ret) {
            $sError = $vSQL." File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function insertPedidoNoteLastID() {
        return $this->db->Insert_ID();
    }

    public function getPedidoNotes($where = null) {
        $vSQL = "
                    SELECT
                      nt.idnote,
                      pers.idperson,
                      pers.name,
                      nt.description,
                      nt.entry_date,
                      nt.user_visibility
                    FROM (scm_tbnote AS nt,
                          tbperson AS pers)
                    WHERE pers.idperson = nt.idperson
                    $where
                    ORDER BY idnote DESC
                    ";

        $ret = $this->db->Execute($vSQL);

        if (!$ret) {
            $sError = $vSQL." File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }

    public function getRequesterByGroup($idgroup,$status=null)
    {
        $where = isset($status) ? "AND b.status = '$status'" : "";
        $query =   "SELECT DISTINCT a.idperson, `name`
                      FROM scm_tbpedido a, tbperson b, scm_tbpedido_has_acd_tbturma c, acd_tbturma d, scm_tbserie_has_group e
                     WHERE a.idperson = b.idperson
                       AND a.idpedido = c.idpedido
                       AND c.idturma = d.idturma
                       AND d.idserie = e.idserie
                       AND e.idgroup IN ($idgroup)
                       $where
                     UNION
                    SELECT DISTINCT a.idperson, `name`
                      FROM hdk_tbgroup_has_person a, tbperson b
                     WHERE a.idperson = b.idperson
                       AND a.idgroup IN ($idgroup)
                       $where";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;

    }

    public function getItemCotacao($where = null, $order = null , $group = null , $limit = null)
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
                        tbperson.name AS 'nomefornecedor',
                        (CASE tbperson.idnatureperson
                            WHEN  1 THEN  np.name
                            ELSE jp.name
                        END) nomefantasia
                    FROM
                       scm_tbitempedido,
                       scm_tbproduto,
                       scm_tbcotacao
              INNER JOIN tbperson
                      ON
                      `scm_tbcotacao`.`idperson` = `tbperson`.`idperson` 
         LEFT OUTER JOIN tbnaturalperson np
                      ON np.idperson = tbperson.idperson
         LEFT OUTER JOIN tbjuridicalperson jp
                      ON jp.idperson = tbperson.idperson
                   WHERE
                       scm_tbitempedido.idproduto = scm_tbproduto.idproduto
                     AND
                        scm_tbcotacao.iditempedido = scm_tbitempedido.iditempedido
                    $where $group $order $limit
                ";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;


    }

    public function getMsgError($where = null, $order = null , $group = null , $limit = null)
    {
        $query ="
                  SELECT path, `code`, a.`name`, description, a.smarty, CONCAT(path,'-',`code`) code_fmt
                    FROM tbmsgerror a, tbmodule b
                   WHERE a.idmodule = b.idmodule
                    $where $group $order $limit
                ";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;


    }

    public function getUsersByGroup($where = null, $order = null , $group = null , $limit = null)
    {
        $query ="SELECT DISTINCT a.idperson, `name`
                   FROM hdk_tbgroup_has_person a, tbperson b
                  WHERE a.idperson = b.idperson
                    $where $group $order $limit
                ";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;


    }

    public function insertItemLog($idItemPedidoOperador,$idprodutoold,$quantityold,$idprodutonew,$quantitynew,$iduser)
    {
        $sql = "INSERT INTO scm_tbitempedidolog (iditempedido,old_idproduto,old_quantidade,new_idproduto,new_quantidade,idperson)
                VALUES ($idItemPedidoOperador,$idprodutoold,$quantityold,$idprodutonew,$quantitynew,$iduser)";

        $ret = $this->db->Execute($sql);

        if($ret)
            return array('success' => true, 'message' => '', 'data' => $ret);
        else
            return array('success' => false, 'message' => "{$this->db->ErrorMsg()}\t{$sql}", 'data' => '');
    }

}