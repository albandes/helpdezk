<?php
//class emailconfig_model extends Model {
if(class_exists('Model')) {
    class DynamicScmContaContabil_model extends Model {}
} elseif(class_exists('cronModel')) {
    class DynamicScmContaContabil_model extends cronModel {}
} elseif(class_exists('apiModel')) {
    class DynamicScmContaContabil_model extends apiModel {}
}

class contacontabil_model extends DynamicScmContaContabil_model
{


    public $database;

    public function __construct()
    {
        parent::__construct();
        $this->database = $this->getConfig('db_connect');
    }

    public function insertContaContabil($idCentroCusto,$nome,$codigo)
    {
        $sql =  "
                INSERT INTO scm_tbcontacontabil (
                  idcentrocusto,
                  nome,
                  codigo,
                  status 
                )
                values
                  (
                   ".$idCentroCusto.",
                   '".$nome."',
                   '".$codigo."',
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

    public function updateContaContabil($idContaContabil,$idCentroCusto,$nome,$codigo)
    {
        $sql =  "
                UPDATE scm_tbcontacontabil
                SET idcentrocusto = $idCentroCusto, 
                    nome = '$nome',
                    codigo = '$codigo'
                WHERE idContaContabil = $idContaContabil
                ";

        $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            return true;
        }
    }

    public function getContaContabil($where = null, $order = null , $group = null , $limit = null )
    {

        $query =   "  SELECT * FROM scm_tbcontacontabil $where $order $group $limit ";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;

    }

    public function changeStatus($idContaContabil,$newStatus)
    {
        return $this->db->Execute("UPDATE scm_tbcontacontabil set status = '".$newStatus."' where idcontacontabil = ".$idContaContabil);
    }

    public function getContaContabilByUserId($where = null, $order = null , $group = null , $limit = null )
    {

        $query =   "  SELECT DISTINCT b.idcontacontabil,b.codigo,b.nome,b.idcentrocusto,status
                        FROM scm_tbcontacontabil_has_aprovador a, scm_tbcontacontabil b
                       WHERE a.idcontacontabil = b.idcontacontabil 
                      $where $order $group $limit ";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;

    }

}