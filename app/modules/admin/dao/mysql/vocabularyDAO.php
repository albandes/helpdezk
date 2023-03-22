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
     * en_us Returns vocabulary data for translation
     * pt_br Retorna dados de vocabulário para tradução
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
     * en_us Returns an array with vocabularies to display in grid
     * pt_br Retorna um array com vocabulários para mostrar no grid
     *
     * @param  string $where
     * @param  string $group
     * @param  string $order
     * @param  string $limit
     * @return array  Parameters returned in array: 
     *                [status = true/false
     *                 push =  [message = PDO Exception message 
     *                          object = model's object]]
     */
    public function queryVocabularies($where=null,$group=null,$order=null,$limit=null): array
    {
        $sql = " SELECT idvocabulary, a.idlocale, b.name locale_name, b.value locale_desc, 
                        a.idmodule, c.name module_name, 
                        key_name, key_value, a.status
                   FROM tbvocabulary a, tblocale b, tbmodule c
                  WHERE a.idlocale = b.idlocale
                    AND c.idmodule = a.idmodule
                 $where $group $order $limit";// echo "{$sql}\n";
        
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

    /**
     * en_us Returns an array with total of vocabularies to display in grid
     * pt_br Retorna um array com o total de vocabulários para mostrart no grid
     *
     * @param  string $where
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function countVocabularies($where=null): array
    {        
        $sql = "SELECT COUNT(idvocabulary) total
                  FROM tbvocabulary a, tblocale b, tbmodule c
                  WHERE a.idlocale = b.idlocale
                    AND c.idmodule = a.idmodule
                $where";

        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            $vocabulary = new vocabularyModel(); 
            $vocabulary->setTotalRows($aRet['total']);

            $ret = true;
            $result = array("message"=>"","object"=>$vocabulary);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error counting vocabulary ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Inserts vocabulary data into tbvocabulary
     * pt_br Insere os dados do vocabulário no tbvocabulary
     *
     * @param  vocabularyModel $vocabularyModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function insertVocabulary(vocabularyModel $vocabularyModel): array
    {        
        $sql = "INSERT INTO tbvocabulary (idlocale,idmodule,key_name,key_value) 
                     VALUES (:localeId, :moduleId, :langKey, :keyValue)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":localeId",$vocabularyModel->getIdLocale());
        $stmt->bindValue(":moduleId",$vocabularyModel->getIdModule());
        $stmt->bindValue(":langKey",$vocabularyModel->getKeyName());
        $stmt->bindValue(":keyValue",$vocabularyModel->getKeyValue());
        $stmt->execute();

        $vocabularyModel->setIdVocabulary($this->db->lastInsertId());

        $ret = true;
        $result = array("message"=>"","object"=>$vocabularyModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Saves the new vocabulary into DB
     * pt_br Grava o novo vocabulario no banco de dados
     *
     * @param  vocabularyModel $vocabularyModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function saveVocabulary(vocabularyModel $vocabularyModel): array
    {   
        $aLocale = $vocabularyModel->getLocaleList();
        $aValue = $vocabularyModel->getKeyValueList();
        
        try{
            $this->db->beginTransaction();

            foreach($aLocale as $k=>$v){
                $vocabularyModel->setIdLocale($v)
                                ->setKeyValue(trim(strip_tags($aValue[$k])));

                $this->insertVocabulary($vocabularyModel);
            }
            
            $ret = true;
            $result = array("message"=>"","object"=>$vocabularyModel);
            $this->db->commit();
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error trying save vocabulary data ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
            $this->db->rollBack();
        }         
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Returns vocabulary data
     * pt_br Retorna dados de vocabulário
     *
     * @param  vocabularyModel $vocabularyModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function getVocabularyById(vocabularyModel $vocabularyModel): array
    {
        
        $sql = "SELECT idvocabulary, a.idlocale, b.name locale_name, b.value locale_desc, 
                       a.idmodule, c.name module_name, 
                       key_name, key_value, a.status
                  FROM tbvocabulary a, tblocale b, tbmodule c
                 WHERE a.idlocale = b.idlocale
                   AND c.idmodule = a.idmodule
                   AND idvocabulary = :vocabularyId";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':vocabularyId', $vocabularyModel->getIdVocabulary());
            $stmt->execute();
            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC); 
            
            $vocabularyModel->setGridList((!is_null($aRet) && !empty($aRet)) ?  $aRet: array())
                            ->setLocaleName((!is_null($aRet['locale_name']) && !empty($aRet['locale_name'])) ?  $aRet['locale_name']: "")
                            ->setIdModule((!is_null($aRet['idmodule']) && !empty($aRet['idmodule'])) ?  $aRet['idmodule']: 0)
                            ->setModuleName((!is_null($aRet['module_name']) && !empty($aRet['module_name'])) ?  $aRet['module_name']: "")
                            ->setKeyName((!is_null($aRet['key_name']) && !empty($aRet['key_name'])) ?  $aRet['key_name']: "")
                            ->setKeyValue((!is_null($aRet['key_value']) && !empty($aRet['key_value'])) ?  $aRet['key_value']: "");
            
            $ret = true;
            $result = array("message"=>"","object"=>$vocabularyModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting vocabulary by id ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Returns vocabulary data
     * pt_br Retorna dados de vocabulário
     *
     * @param  vocabularyModel $vocabularyModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchVocabularyByName(vocabularyModel $vocabularyModel): array
    {
        
        $sql = "SELECT idvocabulary, a.idlocale, b.name locale_name, b.value locale_desc, 
                       a.idmodule, c.name module_name, 
                       key_name, key_value, a.status
                  FROM tbvocabulary a, tblocale b, tbmodule c
                 WHERE a.idlocale = b.idlocale
                   AND c.idmodule = a.idmodule
                   AND key_name = :langKey";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":langKey",$vocabularyModel->getKeyName());
            $stmt->execute();
            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC); 
            
            $vocabularyModel->setGridList((!is_null($aRet) && !empty($aRet)) ?  $aRet: array());
            
            $ret = true;
            $result = array("message"=>"","object"=>$vocabularyModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting vocabulary by key name", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Updates vocabulary data into tbvocabulary
     * pt_br Atualiza os dados do vocabulário no tbvocabulary
     *
     * @param  vocabularyModel $vocabularyModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function updateVocabulary(vocabularyModel $vocabularyModel): array
    {        
        $sql = "UPDATE tbvocabulary 
                   SET idlocale = :localeId,
                       idmodule = :moduleId,
                       key_name = :langKey,
                       key_value = :keyValue
                 WHERE idvocabulary = :vocabularyId";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":localeId",$vocabularyModel->getIdLocale());
        $stmt->bindValue(":moduleId",$vocabularyModel->getIdModule());
        $stmt->bindValue(":langKey",$vocabularyModel->getKeyName());
        $stmt->bindValue(":keyValue",$vocabularyModel->getKeyValue());
        $stmt->bindValue(":vocabularyId",$vocabularyModel->getIdVocabulary());
        $stmt->execute();

        $vocabularyModel->setIdVocabulary($this->db->lastInsertId());

        $ret = true;
        $result = array("message"=>"","object"=>$vocabularyModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Updates vocabulary's data into DB
     * pt_br Atualiza os dados do vocabulario no banco de dados
     *
     * @param  vocabularyModel $vocabularyModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function saveUpdateVocabulary(vocabularyModel $vocabularyModel): array
    {   
        $aVocabularies = $vocabularyModel->getVocabularyIdList();
        $aLocale = $vocabularyModel->getLocaleList();
        $aValue = $vocabularyModel->getKeyValueList();
        
        try{
            $this->db->beginTransaction();

            foreach($aVocabularies as $k=>$v){
                $vocabularyModel->setIdLocale($aLocale[$k])
                                ->setKeyValue(trim(strip_tags($aValue[$k])));
                
                if($v == 0){
                    $this->insertVocabulary($vocabularyModel);
                }else{
                    $vocabularyModel->setIdVocabulary($v);
                    $this->updateVocabulary($vocabularyModel);
                }                
            }
            
            $ret = true;
            $result = array("message"=>"","object"=>$vocabularyModel);
            $this->db->commit();
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error trying update vocabulary data ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
            $this->db->rollBack();
        }         
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Updates vocabulary's status
     * pt_br Atualiza o status do vocabulário
     *
     * @param  vocabularyModel $vocabularyModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function changeStatus(vocabularyModel $vocabularyModel): array
    {   
        $sql = "UPDATE tbvocabulary SET status = :newStatus WHERE idvocabulary = :vocabularyId";

        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":vocabularyId",$vocabularyModel->getIdVocabulary());
            $stmt->bindParam(':newStatus', $vocabularyModel->getStatus());
            $stmt->execute();

            $ret = true;
            $result = array("message"=>"","object"=>$vocabularyModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error update vocabulary's status", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }         
        
        return array("status"=>$ret,"push"=>$result);
    }
}