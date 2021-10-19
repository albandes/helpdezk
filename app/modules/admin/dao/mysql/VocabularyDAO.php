<?php

namespace App\modules\admin\dao\mysql;
use App\core\Database;

class VocabularyDAO extends Database
{
    public function __construct()
    {
        parent::__construct(); 
    }

    public function getVocabulary(string $keyName, string $locale): array
    {

        $sql = "SELECT idvocabulary, key_name, key_value
                  FROM tbvocabulary a, tblocale b
                 WHERE a.idlocale = b.idlocale
                   AND key_name = :keyName
                   AND b.name = :locale";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':keyName', $keyName);
            $stmt->bindParam(':locale', $locale);
            $stmt->execute();
        }catch(\PDOException $ex){
            return array("success"=>false,"message"=>$ex->getMessage()." {$sql}");
        }
        
        $aRet = $stmt->fetch(\PDO::FETCH_ASSOC); 

        return array("success"=>true,"message"=>"","data"=>$aRet);
    }
}