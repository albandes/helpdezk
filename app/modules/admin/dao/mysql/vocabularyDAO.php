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

    /**
     * Returns logo's data by name
     *
     * @param  vocabularyModel $vocabularyModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function getVocabulary(vocabularyModel $vocabularyModel): array
    {
        
        $sql = "SELECT idvocabulary, a.idlocale, b.name locale_name, b.value locale_desc, 
                        key_name, key_value
                  FROM tbvocabulary a, tblocale b
                 WHERE a.idlocale = b.idlocale
                   AND key_name = :keyName
                   AND b.name = :locale";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':keyName', $vocabularyModel->getKeyName());
            $stmt->bindParam(':locale', $vocabularyModel->getLocaleName());
            $stmt->execute();
            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC); 
            
            $vocabularyModel->setIdvocabulary(!$aRet ? 0 : $aRet['idvocabulary'])
                                ->setKeyName(!$aRet ? "" : $aRet['key_name'])
                                ->setKeyValue(!$aRet ? "" : $aRet['key_value']);
            
            $ret = true;
            $result = array("message"=>"","object"=>$vocabularyModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting vocabulary ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * Return an array with vocabulary to display in grid
     *
     * @param  string $where
     * @param  string $group
     * @param  string $order
     * @param  string $limit
     * @return array
     */

    public function queryVocabularies($where=null,$group=null,$order=null,$limit=null): array
    {
        $sql = " SELECT idvocabulary, a.idlocale, b.name locale_name, b.value locale_desc, 
                        key_name, key_value
                   FROM tbvocabulary a, tblocale b
                  WHERE a.idlocale = b.idlocale
                 $where $group $order $limit";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            
            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $vocabulary = new vocabularyModel(); 
            $vocabulary->setGridList($aRet);

            $ret = true;
            $result = array("message"=>"","object"=>$vocabulary);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting vocabulary ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
}