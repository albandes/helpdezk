<?php

namespace App\modules\admin\dao\mysql;

use App\core\Database;
use App\modules\admin\models\mysql\vocabularyModel;

class vocabularyDAO extends Database
{
    public function __construct()
    {
        parent::__construct(); 
    }

    public function getVocabulary(string $keyName, string $locale): ?vocabularyModel
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
            $this->loggerDB->error("Error getting vocabulary ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $ex->getMessage()]);
            return null;
        }
        
        $aRet = $stmt->fetch(\PDO::FETCH_ASSOC); 

        if(!$aRet){
            return null;
        }

        $vocabulary = new vocabularyModel(); 
        $vocabulary->setIdvocabulary($aRet['idvocabulary'])
                   ->setKeyName($aRet['key_name'])
                   ->setKeyValue($aRet['key_value']); 
        
        return $vocabulary;
    }
}