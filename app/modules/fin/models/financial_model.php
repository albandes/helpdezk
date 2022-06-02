<?php

if(class_exists('Model')) {
    class dynamicFinancial_model extends Model {}
} elseif(class_exists('cronModel')) {
    class dynamicFinancial_model extends cronModel {}
} elseif(class_exists('apiModel')) {
    class dynamicFinancial_model extends apiModel {}
}

class financial_model extends dynamicFinancial_model
{
    public $database;

    public function __construct()
    {
        parent::__construct();
        $this->database = $this->getConfig('db_connect');

    }

    public function getOpeningBalance($idcompany,$dtentry)
    {
        $query = "SELECT balance_value 
                    FROM fin_tbcash_balance 
                   WHERE dtentry < '$dtentry' 
                     AND idperson = $idcompany 
                ORDER BY dtentry DESC LIMIT 1";
        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0) {
            return $this->makeErrorMessage( __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        }

        return $ret->fields['balance_value'];
    }

    public function getCashBalance($where=null,$group=null,$order=null,$limit=null)
    {
        $query = "SELECT idcashbalance, balance_value 
                    FROM fin_tbcash_balance 
                    $where $group $order $limit";
        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0) {
            return $this->makeErrorMessage( __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        }

        return $ret;
    }

    public function insertBalance($companyID,$dtEntry,$balance)
    {
        $query = "INSERT INTO fin_tbcash_balance (idperson, dtentry, balance_value)
                    VALUES($companyID,'$dtEntry',$balance)";
        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0) {
            return $this->makeErrorMessage( __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        }

        return $this->db->Insert_ID();
    }

    public function updateBalance($balance,$id)
    {
        $query = "UPDATE fin_tbcash_balance SET balance_value = $balance
                    WHERE idcashbalance = $id";
        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0) {
            return $this->makeErrorMessage( __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        }

        return $ret;
    }

    public function saveBalanceLog($id,$balance,$iduser)
    {
        $query = "INSERT INTO fin_tbcash_balance_log (idcashbalance, balance_value, idperson, dtentry)
                      VALUES($id,$balance,$iduser,NOW())";
        $ret = $this->db->Execute($query);

        if ($this->db->ErrorNo() != 0) {
            return $this->makeErrorMessage( __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        }

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

}
