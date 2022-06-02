<?php
//class emailconfig_model extends Model {
if(class_exists('Model')) {
    class DynamicScmGrupoBens_model extends Model {}
} elseif(class_exists('cronModel')) {
    class DynamicScmGrupoBens_model extends cronModel {}
} elseif(class_exists('apiModel')) {
    class DynamicScmGrupoBens_model extends apiModel {}
}

class grupodebens_model extends DynamicScmGrupoBens_model
{


    public $database;

    public function __construct()
    {
        parent::__construct();
        $this->database = $this->getConfig('db_connect');
    }

    public function insertGrupodeBens($descricao,$depreciacao,$depreciacaoporcentagem,$iddepreciacaoconta,$iddepreciacaoacumuladaconta,$iddepreciacaobensconta,$iddepreciacaocustodabaixa)
    {
        $campos  = '';
        $valores = '';

        if($iddepreciacaoconta != null) {
            $campos  .= ',iddepreciacaoconta';
            $valores .= ','.$iddepreciacaoconta;
        }

        if($iddepreciacaoacumuladaconta != null) {
            $campos  .= ',iddepreciacaoacumuladaconta';
            $valores .= ','.$iddepreciacaoacumuladaconta;
        }

        if($iddepreciacaobensconta != null) {
            $campos  .= ',iddepreciacaobensconta';
            $valores .= ','.$iddepreciacaobensconta;
        }

        if($iddepreciacaocustodabaixa != null) {
            $campos  .= ',iddepreciacaocustodabaixa';
            $valores .= ','.$iddepreciacaocustodabaixa;
        }

        $sql =  "
                INSERT INTO scm_tbgrupodebens (
                  descricao,
                  depreciacao,
                  depreciacaoporcentagem,
                  status
                  $campos
                  
                 
                   
                )
                values
                  (
                   '".$descricao."',
                   '".$depreciacao."',
                   '".$depreciacaoporcentagem."',
                   'A'
                   $valores
                     
                   
                  );
                ";


        $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        }

        return $this->db->Insert_ID( );

    }

    public function updateGrupoDeBens($idGrupoDeBens,$descricao,$depreciacao,$depreciacaoporcentagem,$iddepreciacaoconta,$iddepreciacaoacumuladaconta,$iddepreciacaobensconta,$iddepreciacaocustodabaixa)
    {
        $valores = '';

        if($iddepreciacaoconta != null) {

            $valores .= ",iddepreciacaoconta = $iddepreciacaoconta";
        }

        if($iddepreciacaoacumuladaconta != null) {

            $valores .= ",iddepreciacaoacumuladaconta =  $iddepreciacaoacumuladaconta";
        }

        if($iddepreciacaobensconta != null) {

            $valores .= ",iddepreciacaobensconta = $iddepreciacaobensconta";
        }

        if($iddepreciacaocustodabaixa != null) {
            $valores .= ",iddepreciacaocustodabaixa   =  $iddepreciacaocustodabaixa";
        }

        $sql =  "
                UPDATE scm_tbgrupodebens
                SET descricao              = '$descricao',
                    depreciacao            = '$depreciacao', 
                    depreciacaoporcentagem = '$depreciacaoporcentagem'
                    $valores
                WHERE idGrupoDeBens        =  $idGrupoDeBens
                ";

        $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            return true;
        }
    }

    public function getGrupoDeBens($where = null, $order = null , $group = null , $limit = null )
    {

        $query =   "  SELECT  
                        scm_tbgrupodebens.*,
		                tbdepreciacaoconta.codigo as 'codigodepreciacaoconta',
                        tbdepreciacaoconta.nome   as 'nomedepreciacaoconta',
                        CONCAT( tbdepreciacaoconta.codigo,' - ' , tbdepreciacaoconta.nome ) as 'codigonomedepreciacaoconta',
                        tbdepreciacaoacumuladaconta.codigo as 'codigodepreciacaoacumuladaconta',
                        tbdepreciacaoacumuladaconta.nome   as 'nomedepreciacaoacumuladaconta',
                        CONCAT( tbdepreciacaoacumuladaconta.codigo,' - ' , tbdepreciacaoacumuladaconta.nome ) as 'codigonomedepreciacaoacumuladaconta',
                        tbdepreciacaobensconta.codigo  as 'codigodepreciacaobensconta',
                        tbdepreciacaobensconta.nome    as 'nomedepreciacaobensconta',
                        CONCAT( tbdepreciacaobensconta.codigo,' - ' , tbdepreciacaobensconta.nome ) as 'codigonomedepreciacaobensconta',
                        tbdepreciacaocustodabaixa.codigo  as 'codigodepreciacaocustodabaixa',
                        tbdepreciacaocustodabaixa.nome    as 'nomedepreciacaocustodabaixa',
                        CONCAT( tbdepreciacaocustodabaixa.codigo,' - ' , tbdepreciacaocustodabaixa.nome ) as 'codigonomedepreciacaocustodabaixa'
                        FROM
                        scm_tbgrupodebens
                        LEFT JOIN scm_tbcontacontabil tbdepreciacaoconta
                        ON
							tbdepreciacaoconta.idcontacontabil = scm_tbgrupodebens.iddepreciacaoconta
                        
						LEFT JOIN scm_tbcontacontabil tbdepreciacaoacumuladaconta
						ON 
						    tbdepreciacaoacumuladaconta.idcontacontabil = scm_tbgrupodebens.iddepreciacaoacumuladaconta
                        LEFT JOIN scm_tbcontacontabil tbdepreciacaobensconta
						ON 
						    tbdepreciacaobensconta.idcontacontabil = scm_tbgrupodebens.iddepreciacaobensconta
                        LEFT JOIN scm_tbcontacontabil tbdepreciacaocustodabaixa
						ON 
						    tbdepreciacaocustodabaixa.idcontacontabil = scm_tbgrupodebens.iddepreciacaocustodabaixa $where $order $group $limit ";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;

    }

    public function changeStatus($idGrupoDeBens,$newStatus)
    {
        return $this->db->Execute("UPDATE scm_tbgrupodebens set status = '".$newStatus."' where idgrupodebens = ".$idGrupoDeBens);
    }

}