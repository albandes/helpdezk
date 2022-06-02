<?php

class atleta_model extends Model
{


    public $database;

    public function __construct()
    {
        parent::__construct();
        $this->database = $this->getConfig('db_connect');
    }

    public function insertAtleta($idTypelogin,$tidTpeperson,$idNature,$idTheme,$nome,$login,$email,$senha,$status,$userVip,$telefone,$ramal,$celular,$idLocation,$timeValue,$overTime,$changePass,$idCondicao,$idDepartamento,$idPosicao,$apelido)
    {
        $sql =  "
                CALL spm_insertAtleta( $idTypelogin,
                                       $tidTpeperson,
                                       $idNature,
                                       $idTheme,
                                       '".$nome."',
                                       '".$login."',
                                       '".$email."',
                                       '".$senha."',
                                       '".$status."',
                                       '".$userVip."',
                                       '".$telefone."',
                                       '".$ramal."',
                                       '".$celular."',
                                       $idLocation,
                                       $timeValue,
                                       $overTime,
                                       $changePass,
                                       $idCondicao,
                                       $idDepartamento,
                                       $idPosicao,
                                       '".$apelido."',
                                       @output);
                ";

        $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            $rs = $this->db->Execute('SELECT @output AS idperson;');
            return $rs->fields['idperson'];
        }

    }

    public function updateAtleta($idPerson,$name,$email,$phoneNumber,$celPhone,$idCondicao,$idDepartamento,$idPosicao,$apelido)
    {
        $sql =  "
                UPDATE tbperson, spm_person_condicao, spm_person_departamento, spm_person_posicao, spm_tbapelido
                SET tbperson.name = '$name',
                    tbperson.email = '$email',
                    tbperson.phone_number = '$phoneNumber',
                    tbperson.cel_phone = '$celPhone',
                    spm_person_condicao.idcondicao = '$idCondicao',
                    spm_person_departamento.iddepartamento = '$idDepartamento',
                    spm_person_posicao.idposicao = '$idPosicao',
                    spm_tbapelido.nome = '$apelido'
                WHERE tbperson.idperson = $idPerson
                AND spm_person_condicao.idperson = $idPerson
                AND spm_person_departamento.idperson = $idPerson
                AND spm_person_posicao.idperson = $idPerson
                AND spm_tbapelido.idperson = $idPerson
                ";

        $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            return true;
        }
    }

    public function getAtleta($where = null, $order = null , $group = null , $limit = null )
    {

        $query =   "  SELECT * FROM spm_vwAtleta  $where $order $group $limit ";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;

    }

    public function getAtletaPosicao($where = null, $order = null , $group = null , $limit = null )
    {

        $query =    "
                    SELECT
                      spm_tbposicao.idposicao,
                      spm_tbposicao.nome
                    FROM spm_tbposicao
                    $where $order $group $limit
                    ";
        $ret = $this->db->Execute($query);
        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;
    }

    public function getAtletaCondicao($where = null, $order = null , $group = null , $limit = null )
    {

        $query =    "
                    SELECT
                      spm_tbcondicao.idcondicao,
                      spm_tbcondicao.nome
                    FROM spm_tbcondicao
                    $where $order $group $limit
                    ";
        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;
    }

    public function getAtletaDepartamento($where = null, $order = null , $group = null , $limit = null )
    {

        $query =    "
                    SELECT
                      spm_tbdepartamento.iddepartamento,
                      spm_tbdepartamento.nome
                    FROM spm_tbdepartamento
                    $where $order $group $limit
                    ";
        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;
    }

}