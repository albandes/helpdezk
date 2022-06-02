<?php

//class emailconfig_model extends Model {
if(class_exists('Model')) {
    class DynamicScmCentroCusto_model extends Model {}
} elseif(class_exists('cronModel')) {
    class DynamicScmCentroCusto_model extends cronModel {}
} elseif(class_exists('apiModel')) {
    class DynamicScmCentroCusto_model extends apiModel {}
}

class centrocusto_model extends DynamicScmCentroCusto_model
{


    public $database;

    public function __construct()
    {
        parent::__construct();
        $this->database = $this->getConfig('db_connect');
    }

    public function insertCentroCusto($idPerson,$nome,$tipo,$codigo)
    {
        $sql =  "
                INSERT INTO scm_tbcentrocusto (
                  idperson,
                  nome,
                  tipo,
                  codigo,
                  status
                  
                )
                values
                  (
                    $idPerson,
                   '".$nome."',
                   '".$tipo."',
                   '".$codigo."',
                   'A'
                  ) ;

                ";

        $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        }

        return $this->db->Insert_ID( );

    }

    public function updateCentroCusto($idcentrocusto,$nome,$tipo,$codigo)
    {
        $sql =  "
                UPDATE scm_tbcentrocusto
                SET nome = '$nome',
                    tipo = '$tipo',
                    codigo = '$codigo'
                WHERE idcentrocusto = $idcentrocusto
                ";

        $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            return true;
        }
    }

    public function getCentroCusto($where = null, $order = null , $group = null , $limit = null )
    {

        $query =   "  SELECT * FROM scm_tbcentrocusto $where $order $group $limit ";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;

    }

    public function changeStatus($idCentroCusto,$newStatus)
    {
        return $this->db->Execute("UPDATE scm_tbcentrocusto set status = '".$newStatus."' where idcentrocusto = ".$idCentroCusto);
    }

    public function getCentroCustoByUserId($where = null, $order = null , $group = null , $limit = null )
    {

        $query =   "  SELECT DISTINCT b.idcentrocusto,c.codigo,c.nome
                        FROM scm_tbcontacontabil_has_aprovador a, scm_tbcontacontabil b, scm_tbcentrocusto c
                       WHERE a.idcontacontabil = b.idcontacontabil
                         AND b.idcentrocusto = c.idcentrocusto 
                      $where $order $group $limit ";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;

    }


}