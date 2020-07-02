<?php

if(class_exists('Model')) {
    class DynamicVocabulary_model extends Model {}
} elseif(class_exists('cronModel')) {
    class DynamicVocabulary_model extends cronModel {}
} elseif(class_exists('apiModel')) {
    class DynamicVocabulary_model extends apiModel {}
}

class vocabulary_model extends DynamicVocabulary_model{


    public function selectVocabulary($where = NULL, $order = NULL, $limit = NULL) {
        $sql = "
                SELECT 
                  a.idlocale,
                  b.name,
                  a.key_name,
                  a.key_value 
                FROM
                  tbvocabulary a,
                  tblocale b 
                WHERE $where
                ";

        $this->execute("set names 'utf8'");

        $ret = $this->select($sql);
        if (!$ret) {
            $sError = "File: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
    }


    public function getVocabulary($where=NULL,$group=NULL,$order=NULL,$limit=NULL) {

        $sql = "SELECT idvocabulary, a.idlocale, b.name locale_name, a.idmodule, c.name module_name, key_name, key_value, a.status
                  FROM tbvocabulary a, tblocale b, tbmodule c
                 WHERE b.idlocale = a.idlocale
                   AND c.idmodule = a.idmodule
                $where $group $order $limit";

        $ret = $this->db->Execute($sql);

        if ($this->db->ErrorNo() != 0)
            return $this->makeErrorMessage( __LINE__, __METHOD__, $this->db->ErrorMsg(), $sql );
        else
            return $ret;
    }

    public function countVocabulary($where = NULL){
        $query = "SELECT count(a.idvocabulary) as total
					FROM tbvocabulary a, tblocale b, tbmodule c
                   WHERE b.idlocale = a.idlocale
                     AND c.idmodule = a.idmodule
				  $where";

        $ret = $this->db->Execute($query);
        if(!$ret) {
            return $this->makeErrorMessage( __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        }
        return $ret->fields['total'];
    }

    public function getLocale($where=NULL,$group=NULL,$order=NULL,$limit=NULL){
        $query = "SELECT idlocale, `name`, `value`
					FROM tblocale
				  $where $group $order $limit";

        $ret = $this->db->Execute($query);
        if(!$ret) {
            return $this->makeErrorMessage( __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        }
        return $ret;
    }


    public function insertVocabulary($localeID,$moduleID,$keyName,$keyValue){
        $query = "INSERT INTO tbvocabulary (idlocale,idmodule,key_name,key_value,`status`)
                       VALUES ($localeID,$moduleID,'{$keyName}','{$keyValue}','A')";

        $ret = $this->db->Execute($query);
        if(!$ret) {
            return $this->makeErrorMessage( __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        }
        return $this->db->Insert_ID();
    }

    public function updateVocabulary($vocabularyID,$localeID,$moduleID,$keyName,$keyValue){
        $query = "UPDATE tbvocabulary 
                     SET idlocale = $localeID,
                         idmodule = $moduleID,
                         key_name = '{$keyName}',
                         key_value = '{$keyValue}'
                   WHERE idvocabulary = $vocabularyID";

        $ret = $this->db->execute($query);
        if(!$ret) {
            return $this->makeErrorMessage( __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        }
        return $ret;
    }

    public function changeStatus($vocabularyID,$status){
        $query = "UPDATE tbvocabulary 
                     SET `status` = '{$status}'
                   WHERE idvocabulary = $vocabularyID";

        $ret = $this->db->Execute($query);
        if(!$ret) {
            return $this->makeErrorMessage( __LINE__, __METHOD__, $this->db->ErrorMsg(), $query );
        }
        return $ret;
    }

    function makeErrorMessage($line,$method,$error,$query='')
    {
        $aRet = array(
            "status" => 'Error',
            "message" => "[DB Error] Method: " . $method . ", Line: " . $line . ", Message: " . $error . ", Query: " . $query
        );
        return $aRet;
    }

}