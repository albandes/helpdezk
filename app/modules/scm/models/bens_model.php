<?php

if(class_exists('Model')) {
    class DynamicScmBens_model extends Model {}
} elseif(class_exists('cronModel')) {
    class DynamicScmBens_model extends cronModel {}
} elseif(class_exists('apiModel')) {
    class DynamicScmBens_model extends apiModel {}
}

class bens_model extends DynamicScmBens_model
{

    public $database;

    public function __construct()
    {
        parent::__construct();
        $this->database = $this->getConfig('db_connect');
    }

    public function insertBens($descricao,$numeropatrimonio,$idmarca,$idestado,$idlocal,$idgrupodebens,$idperson,$dataaquisicao,$doacao,$nfentrada,$numeroserie,$valor,$datagarantia,$quantidade,$baixa)
    {

        $campos  = '';
        $valores = '';

        if($idmarca != null) {
            $campos  .= ',idmarca';
            $valores .= ','.$idmarca;
        }

        if($idestado != null) {
            $campos  .= ',idestado';
            $valores .= ','.$idestado;
        }

        if($idlocal != null) {
            $campos  .= ',idlocal';
            $valores .= ','.$idlocal;
        }

        if($idgrupodebens != null) {
            $campos  .= ',idgrupodebens';
            $valores .= ','.$idgrupodebens;
        }

        if($idperson != null) {
            $campos  .= ',idperson';
            $valores .= ','.$idperson;
        }

        if(empty($dataaquisicao)) {
            $dataaquisicao1 = "NULL,";
        }else{
            $dataaquisicao1 = "'".$dataaquisicao."',";
        }

        if(empty($datagarantia)) {
            $datagarantia1 = "NULL,";
        }else{
            $datagarantia1 = "'".$datagarantia."',";
        }

        if(!$quantidade) {
            $quantidade1 = " NULL,";
        }else{
            $quantidade1 = "'".$quantidade."',";
        }

        if(!$valor) {
            $valor1 = "NULL,";
        }else{
            $valor1 =  "'".$valor."',";
        }

            $sql = "
                    INSERT INTO scm_tbbens (
                      descricao,
                      numeropatrimonio,
                      dataaquisicao,
                      doacao, 
                      nfentrada, 
                      numeroserie,
                      valor,
                      datagarantia,
                      quantidade, 
                      baixa, 
                      status
                      $campos
                      
                    )
                    values
                      (
                       '" . $descricao . "',
                       '" . $numeropatrimonio . "',
                        $dataaquisicao1
                       '" . $doacao . "',
                       '" . $nfentrada . "',
                       '" . $numeroserie . "',
                        $valor1
                        $datagarantia1
                        $quantidade1
                       '" . $baixa . "',
                       'A'
                       $valores
                      ) ;
    
                    ";
        $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        }

        return $this->db->Insert_ID( );

    }

    public function updateBens($idBens,$descricao,$numeropatrimonio,$idmarca,$idestado,$idlocal,$idgrupodebens,$idperson,$dataaquisicao,$doacao,$nfentrada,$numeroserie,$valor,$datagarantia,$quantidade,$baixa)
    {

        $valores = '';

        if($idmarca != null) {

            $valores .= ",idmarca = $idmarca";
        }

        if($idestado != null) {

            $valores .= ",idestado =  $idestado";
        }

        if($idlocal != null) {

            $valores .= ",idlocal = $idlocal";
        }

        if($idgrupodebens != null) {
            $valores .= ",idgrupodebens   =  $idgrupodebens";
        }

        if($idperson != null) {
            $valores .= ",idperson   =  $idperson";
        }

        if(empty($dataaquisicao)) {
            $dataaquisicao1 = "dataaquisicao      =    NULL,";
        }else{
            $dataaquisicao1 = "dataaquisicao      =    '".$dataaquisicao."',";
        }

        if(empty($datagarantia)) {
            $datagarantia1 = "datagarantia     =    NULL,";
        }else{
            $datagarantia1 = "datagarantia     =    '".$datagarantia."',";
        }

        if(!$quantidade) {
            $quantidade1 = "quantidade      =    NULL,";
        }else{
            $quantidade1 =  "quantidade     =    '".$quantidade."',";
        }

        if(!$valor) {
            $valor1 = "valor      =    NULL,";
        }else{
            $valor1 =  "valor     =    '".$valor."',";
        }

        $sql =  "
                UPDATE scm_tbbens
                SET  descricao         = '".$descricao."',
                     numeropatrimonio  = '".$numeropatrimonio."',
                     $dataaquisicao1
                     doacao            = '".$doacao."',
                     nfentrada         = '".$nfentrada."',
                     numeroserie       = '".$numeroserie."',
                     $valor1
                     $datagarantia1
                     $quantidade1
                     baixa             = '".$baixa."'
                     $valores
                WHERE idbens = $idBens
                ";

        $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            return true;
        }
    }

    public function getBens($where = null, $order = null , $group = null , $limit = null )
    {

        $query =   "  SELECT 
                         scm_tbbens.idbens,
                         scm_tbbens.numeropatrimonio,
                         scm_tbbens.descricao,
                         scm_tbmarca.idmarca,
                         scm_tbmarca.nome AS 'nomemarca',
                         scm_tbestado.idestado,
                         scm_tbestado.nome AS 'nomeestado',
                         scm_tblocal.idlocal,
                         scm_tblocal.nome AS 'nomelocal',
                         scm_tbgrupodebens.idgrupodebens,
                         scm_tbgrupodebens.descricao AS 'nomegrupodebens',
                         scm_tbbens.dataaquisicao,
                         DATE_FORMAT(scm_tbbens.dataaquisicao,'%d/%m/%Y') AS 'datacomp',
                         tbperson.idperson,
                         tbperson.name AS 'nomefornecedor',
                         scm_tbbens.doacao,
                         scm_tbbens.nfentrada,
                         scm_tbbens.numeroserie,
                         scm_tbbens.valor,
                         scm_tbbens.datagarantia,
                         scm_tbbens.quantidade,
                         scm_tbbens.baixa,
                         scm_tbbens.status
                         FROM
                           scm_tbbens
                         LEFT JOIN scm_tbmarca
                         ON
                            scm_tbmarca.idmarca = scm_tbbens.idmarca
                         LEFT JOIN scm_tbestado
                         ON 
                            scm_tbestado.idestado = scm_tbbens.idestado
                         LEFT JOIN scm_tblocal
                         ON 
                            scm_tblocal.idlocal = scm_tbbens.idlocal   
                         LEFT JOIN scm_tbgrupodebens
                         ON 
                            scm_tbgrupodebens.idgrupodebens = scm_tbbens.idgrupodebens
                         LEFT JOIN tbperson
                         ON 
                            tbperson.idperson = scm_tbbens.idperson $where $order $group $limit ";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;

    }

    public function getRequestDataImprimir($where = null, $order = null , $group = null , $limit = null )
    {

        $query =   "  SELECT 
                         scm_tbbens.idbens,
                         scm_tbbens.numeropatrimonio,
                         scm_tbbens.descricao,
                         scm_tbmarca.idmarca,
                         scm_tbmarca.nome AS 'nomemarca',
                         scm_tbestado.idestado,
                         scm_tbestado.nome AS 'nomeestado',
                         scm_tblocal.idlocal,
                         scm_tblocal.nome AS 'nomelocal',
                         scm_tbgrupodebens.idgrupodebens,
                         scm_tbgrupodebens.descricao AS 'nomegrupodebens',
                         DATE_FORMAT(scm_tbbens.dataaquisicao,'%d/%m/%Y') AS 'dataaquisicao',
                         tbperson.idperson,
                         tbperson.name AS 'nomefornecedor',
                         scm_tbbens.doacao,
                         scm_tbbens.nfentrada,
                         scm_tbbens.numeroserie,
                         scm_tbbens.valor,
                         DATE_FORMAT(scm_tbbens.datagarantia,'%d/%m/%Y') AS 'datagarantia',
                         scm_tbbens.quantidade,
                         scm_tbbens.baixa,
                         scm_tbbens.status
                         FROM
                           scm_tbbens
                         LEFT JOIN scm_tbmarca
                         ON
                            scm_tbmarca.idmarca = scm_tbbens.idmarca
                         LEFT JOIN scm_tbestado
                         ON 
                            scm_tbestado.idestado = scm_tbbens.idestado
                         LEFT JOIN scm_tblocal
                         ON 
                            scm_tblocal.idlocal = scm_tbbens.idlocal   
                         LEFT JOIN scm_tbgrupodebens
                         ON 
                            scm_tbgrupodebens.idgrupodebens = scm_tbbens.idgrupodebens
                         LEFT JOIN tbperson
                         ON 
                            tbperson.idperson = scm_tbbens.idperson $where $order $group $limit ";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;

    }

    public function changeStatus($idBens,$newStatus)
    {
        return $this->db->Execute("UPDATE scm_tbbens set status = '".$newStatus."' where idbens = ".$idBens);
    }


}