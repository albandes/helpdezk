<?php 

if(class_exists('Model')) {
    class DynamicLgpreport_model extends Model {}
} elseif(class_exists('cronModel')) {
    class DynamicLgpreport_model extends cronModel {}
} elseif(class_exists('apiModel')) {
    class DynamicLgpreport_model extends apiModel {}
}

class lgpreport_model extends DynamicLgpreport_model{
    public $database;

    public function __construct()
    {
        parent::__construct();
        $this->database = $this->getConfig('db_connect');

    }
    
    public function getDataMapping($where=null,$order=null,$limit=null, $typeUser) { 
      //echo $id_typePerson;

        $sql = "SELECT a.iddado, a.nome, compartilhado, a.idtipotitular, b.nome tipotitular, a.idtipodado, c.nome tipo, 
                            GROUP_CONCAT(DISTINCT m.idfinalidade ORDER BY m.nome) finalidadeids, GROUP_CONCAT(DISTINCT m.nome ORDER BY m.nome) finalidade,
                            GROUP_CONCAT(DISTINCT o.idformatocoleta ORDER BY o.nome) formatoids, GROUP_CONCAT(DISTINCT o.nome ORDER BY o.nome) formato,
                            GROUP_CONCAT(DISTINCT n.idformacoleta ORDER BY n.nome) formaids, GROUP_CONCAT(DISTINCT n.nome ORDER BY n.nome) forma,
                            GROUP_CONCAT(DISTINCT l.idbaselegal ORDER BY l.nome) baseids, GROUP_CONCAT(DISTINCT l.nome ORDER BY l.nome) base,
                            GROUP_CONCAT(DISTINCT k.idarmazenamento ORDER BY k.nome) armazenamentoids, GROUP_CONCAT(DISTINCT k.nome ORDER BY k.nome) armazenamento,
                            GROUP_CONCAT(DISTINCT CONCAT(p.idperson,'|',i.type) ORDER BY p.name) personaccids, GROUP_CONCAT(DISTINCT p.name ORDER BY p.name) personacc,
                            GROUP_CONCAT(DISTINCT r.idperson ORDER BY r.name) operadorids, GROUP_CONCAT(DISTINCT r.name ORDER BY r.name) operador
                    FROM lgp_tbdado a
                    JOIN lgp_tbtipotitular b
                        ON a.idtipotitular = b.idtipotitular
                    JOIN lgp_tbtipodado c
                        ON a.idtipodado = c.idtipodado
                    JOIN lgp_tbdado_has_armazenamento d
                        ON a.iddado = d.iddado
                    JOIN lgp_tbdado_has_baselegal e
                        ON a.iddado = e.iddado
                    JOIN lgp_tbdado_has_finalidade f
                        ON a.iddado = f.iddado
                    JOIN lgp_tbdado_has_formacoleta g
                        ON a.iddado = g.iddado
                    JOIN lgp_tbdado_has_formatocoleta h
                        ON a.iddado = h.iddado
                    JOIN lgp_tbdado_has_person i
                        ON a.iddado = i.iddado
        LEFT OUTER JOIN lgp_tbdado_has_operador j
                        ON a.iddado = j.iddado
                    JOIN lgp_tbarmazenamento k
                        ON d.idarmazenamento = k.idarmazenamento
                    JOIN lgp_tbbaselegal l
                        ON e.idbaselegal = l.idbaselegal
                    JOIN lgp_tbfinalidade m
                        ON f.idfinalidade = m.idfinalidade
                    JOIN lgp_tbformacoleta n
                        ON g.idformacoleta = n.idformacoleta
                    JOIN lgp_tbformatocoleta o
                        ON h.idformatocoleta = o.idformatocoleta
                    JOIN (SELECT idperson, `name`, 'P' `type`,`status`
                            FROM tbperson
                            WHERE idtypeperson IN (2,3,$typeUser)
                            UNION 
                            SELECT a.idgroup idperson, `name`, 'G' `type`,a.`status`
                            FROM `lgp_tbgroup` a, tbperson b 
                            WHERE b.idperson = a.idperson) p
                        ON (i.idperson = p.idperson AND
                        i.`type` = p.`type`) 
        LEFT OUTER JOIN tbperson r
                        ON j.idperson = r.idperson
                    $where
                GROUP BY a.iddado
                    $order $limit"; //echo "{$sql}\n";

            return $this->selectPDO($sql);

    }

    public function getLgpTypePerson($typeName) {

        $sql = "SELECT idtypeperson FROM tbtypeperson WHERE `name` = '{$typeName}'";

        return $this->selectPDO($sql);

    }

    public function getPersonName($idperson){

        $sql = "SELECT `name` FROM tbperson WHERE `idperson` = $idperson";

        return $this->selectPDO($sql);

    }

    public function getPersonGroups($where=null,$order=null,$limit=null,$group=null){

        $sql = "SELECT a.idgroup, b.idperson, b.name group_name,c.idperson idcompany, c.name company_name, a.status, b.idtypeperson
                  FROM lgp_tbgroup a, tbperson b, tbperson c, lgp_tbgroup_has_person d
                 WHERE a.idperson = b.idperson
                   AND a.idcompany = c.idperson
                   AND a.idgroup = d.idgroup
                   $where $group $order $limit";

        return $this->selectPDO($sql);

    }

}