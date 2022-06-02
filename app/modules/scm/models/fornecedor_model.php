<?php

//class emailconfig_model extends Model {
if(class_exists('Model')) {
    class DynamicScmFornecedor_model extends Model {}
} elseif(class_exists('cronModel')) {
    class DynamicScmFornecedor_model extends cronModel {}
} elseif(class_exists('apiModel')) {
    class DynamicScmFornecedor_model extends apiModel {}
}

class fornecedor_model extends DynamicScmFornecedor_model
{


    public $database;

    public function __construct()
    {
        parent::__construct();
        $this->database = $this->getConfig('db_connect');
    }

    public function insertFornecedor($idTypelogin,$idTypePerson,$idNaturePerson,$idTheme,$name,$email,$cel_phone,$phone_number,$userVip)
    {
            $sql =  "
                    INSERT INTO tbperson (
                      idtypelogin,
                      idtypeperson,
                      idnatureperson,
                      idtheme,
                      name,
                      email,
                      cel_phone,
                      phone_number,
                      user_vip,
                      status
                                          
                    )
                    values
                      (
                       ".$idTypelogin.",
                       ".$idTypePerson.",
                       ".$idNaturePerson.",
                       ".$idTheme.",
                       '".$name."',
                       '".$email."',
                       '".$cel_phone."',
                       '".$phone_number."',
                       '".$userVip."',
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

    /*public function insertJuridicalData($idPerson,$cnpj,$inscricaoEstadual)
    {

        $sql =  "
                    INSERT INTO tbjuridicalperson (
                      idperson,
                      ein_cnpj,
                      iestadual
                                                   
                    )
                    values
                      (
                       ".$idPerson.",
                       '".$cnpj."',
                       '".$inscricaoEstadual."'
                                                                 
                      );
                    ";

        $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        }

        return $this->db->Insert_ID( );

    }*/

    public function insertJuridicalData($idperson,$name,$ein_cnpj,$iestadual,$contact,$observation)
    {

        $sql =  "CALL hdk_insUpdJuridicalPerson($idperson,'$name','$ein_cnpj','$iestadual','$contact','$observation')";

        $ret = $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        }

        return $ret;

    }

    public function updateFornecedor($idPerson,$nomepessoa,$idnature,$email,$cel_phone,$phone_number)
    {

            $sql = "
                UPDATE tbperson
                SET 
                    name = '$nomepessoa',
                    idnatureperson = $idnature,
                    email = '$email',
                    phone_number = '$phone_number',
                    cel_phone = '$cel_phone'
                WHERE idperson = $idPerson;
             
                ";

            $this->db->Execute($sql);

            if ($this->db->ErrorNo() != 0) {
                $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
                return false ;
            } else {
                return true;
            }




    }

    public function updateJuridicalData($idPerson,$ein_cnpj,$iestadual)
    {

        $sql = "
                UPDATE tbjuridicalperson
                SET 
                    iestadual = '$iestadual',
                    ein_cnpj = '$ein_cnpj'
                WHERE idperson = $idPerson;
             
                ";

        $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            return true;
        }

    }

    public function updateNaturalData($idPerson, $cpf, $rg)
    {

        $sql = "
                UPDATE tbnaturalperson
                SET 
                    tbnaturalperson.ssn_cpf = '$cpf', 
                    tbnaturalperson.rg = '$rg'
                WHERE tbnaturalperson.idperson = $idPerson;
             
                ";

        $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        } else {
            return true;
        }

    }

    public function getFornecedor($where = null, $order = null , $group = null , $limit = null )
    {
        $query =   "  SELECT * FROM scm_viewSupplier $where $order $group $limit ";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;

    }

    public function getPerson($where = null, $group = null , $order = null , $limit = null )
    {

        $query =   "  SELECT * FROM tbperson $where $group $order $limit ";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;

    }

    public function getGridFornecedor($where = null, $order = null , $group = null , $limit = null )
    {

        $query ='SELECT a.name, a.fantasy_name,a.idperson, b.email,
                        a.naturetype tipo, a.cpf_cnpj, b.status,
                        b.phone_number
                   FROM scm_viewSupplier a, tbperson b                       
                  WHERE a.idperson = b.idperson
                      '.$where.' '.$order.' '.$group.' '.$limit.'   
                    
                ';

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;

    }

    public function getFornecedorUpdateEcho($where = null, $order = null , $group = null , $limit = null )
    {

        $query =   "SELECT 
                        tbperson.idperson,
                        tbperson.name,
                        tbnaturalperson.name as nomefantasia,
                        tbperson.phone_number,
                        tbnaturalperson.idnaturalperson,
                        tbperson.email,
                        tbperson.cel_phone,
                        tbnaturalperson.rg,
                        tbnaturalperson.ssn_cpf,
                        tbaddress.zipcode,
                        tbaddress.complement,
                        tbaddress.number,
                        tbstreet.name as 'logradouro_nome',
                        tbcity.name as cidade,
                        tbstate.name as estado,
                        tbcountry.name as printablename,
                        tbneighborhood.idneighborhood,
                        tbneighborhood.name as bairro,
                        tbcity.idcity,
                        tbstate.idstate,
                        tbcountry.idcountry
                    FROM 
                        tbperson, 
                        tbaddress,
                        tbnaturalperson,
                        tbneighborhood,
                        tbstreet,
                        tbcity,
                        tbstate,
                        tbcountry
                        
                    WHERE 
                        tbstreet.idstreet = tbaddress.idstreet
                    AND
                        tbneighborhood.idcity = tbcity.idcity
                    AND
                        tbneighborhood.idneighborhood = tbaddress.idneighborhood
                    AND
                        tbnaturalperson.idperson = tbperson.idperson
                    AND
                        tbaddress.idperson = tbperson.idperson
                    AND
                        tbcity.idstate = tbstate.idstate
                    AND
                        tbstate.idcountry = tbcountry.idcountry
                    AND
                        tbperson.idtypeperson = 17
                    AND
                        $where";
        $ret = $this->db->Execute($query);

        if(empty($ret->fields)){
            $query =   "SELECT
                        tbperson.idperson,
                        tbjuridicalperson.idjuridicalperson,
                        tbperson.email,
                        tbperson.name,
                        tbjuridicalperson.name as nomefantasia,
                        tbperson.phone_number,
                        tbperson.cel_phone,
                        tbstreet.name as 'logradouro_nome',
                        tbjuridicalperson.iestadual,
                        tbjuridicalperson.ein_cnpj,
                        tbaddress.zipcode,
                        tbaddress.complement,
                        tbaddress.number,
                        tbneighborhood.idneighborhood,
                        tbneighborhood.name as bairro,
                        tbcity.idcity,
                        tbcity.name as cidade,
                        tbstate.name as estado,
                        tbcountry.name as printablename,
                        tbstate.idstate,
                        tbcountry.idcountry
                    FROM 
                        tbperson, 
                        tbaddress,
                        tbjuridicalperson,
                        tbneighborhood,
                        tbstreet,
                        tbcity,
                        tbstate,
                        tbcountry
                        
                    WHERE 
                          tbstreet.idstreet = tbaddress.idstreet
                    AND 
                            tbneighborhood.idcity = tbcity.idcity
                    AND
                            tbneighborhood.idneighborhood = tbaddress.idneighborhood
                    AND
                            tbjuridicalperson.idperson = tbperson.idperson
                    AND
                            tbaddress.idperson = tbperson.idperson
                    AND
                            tbperson.idtypeperson = 17
                    AND
                          tbcity.idstate = tbstate.idstate
                    AND
                             tbstate.idcountry = tbcountry.idcountry
                    and
                          $where";

            $ret = $this->db->Execute($query);
        }

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;

    }

    public function getlogradouro($where = null, $order = null , $group = null , $limit = null )
    {

        $query =   "  SELECT * FROM tbtypestreet $where $order $group $limit ";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;

    }

    function getCnpj($where = null, $order = null , $group = null , $limit = null )
    {
        $query =   "SELECT a.idjuridicalperson, a.idperson, b.name 
                      FROM tbjuridicalperson a, tbperson b
                     WHERE a.idperson = b.idperson $where $order $group $limit ";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;

    }

    function getCpf($where = null, $order = null , $group = null , $limit = null )
    {
        $query =   "SELECT a.idnaturalperson, a.idperson, b.name 
                      FROM tbnaturalperson a, tbperson b
                     WHERE a.idperson = b.idperson $where $order $group $limit ";

        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0)
            $this->dbError( __FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        else
            return $ret;

    }

    public function insertNaturalData($idperson,$name,$ssn_cpf,$rg,$rgoexp,$dtbirth,$mother,$father,$gender)
    {

        $sql =  "CALL hdk_insUpdNaturalPerson($idperson,'$name','$ssn_cpf','$rg','$rgoexp','$dtbirth','$mother','$father','$gender')";

        $ret = $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        }

        return $ret;

    }

    public function deleteNatureData($idperson, $flagNature)
    {

        $sql =  "CALL hdk_deleteNatureperson($idperson,$flagNature)";

        $ret = $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0) {
            $this->dbError(__FILE__, __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql);
            return false ;
        }

        return $ret;

    }

}