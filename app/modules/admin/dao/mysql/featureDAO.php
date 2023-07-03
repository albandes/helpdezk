<?php

namespace App\modules\admin\dao\mysql;

use App\core\Database;

use App\modules\admin\models\mysql\popConfigModel;
use App\modules\admin\models\mysql\moduleModel;
use App\modules\admin\models\mysql\featureModel;
use App\modules\admin\models\mysql\emailSettingsModel;

class featureDAO extends Database
{
    public function __construct()
    {
        parent::__construct(); 
    }
    
    /**
     * Returns object with POP settings
     *
     * @param  popConfigModel $popConfigModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchPopConfigs(popConfigModel $popSettings): array
    {
        
        $sql = "SELECT session_name, `value` FROM tbconfig WHERE idconfigcategory = 12";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $row = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            foreach($row as $k=>$v){
                switch($v['session_name']){
                    case 'POP_HOST':
                        $popSettings->setHost($v['value']);
                        break;
                    case 'POP_PORT':
                        $popSettings->setPort($v['value']);
                        break;
                    case 'POP_TYPE':
                        $popSettings->setType($v['value']);
                        break;
                    case 'POP_DOMAIN':
                        $popSettings->setDomain($v['value']);
                        break;
                    default:
                        break;
                }          
            }

            $ret = true;
            $result = array("message"=>"","object"=>$popSettings);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error getting all hdk groups ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

        
    /**
     * fetchConfigsByCategory
     * 
     * en_us Returns array with settings data by category
     * pt_br Retorna array com dados de configurações por categoria
     *
     * @param  featureModel $featureModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchConfigsByCategory(featureModel $featureModel): array
    {
        
        $sql = "SELECT session_name, `value` FROM tbconfig WHERE idconfigcategory = :settingCatId";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':settingCatId', $featureModel->getSettingCatId());
            $stmt->execute();

            while($row = $stmt->fetch(\PDO::FETCH_ASSOC)){
                $ses = $row['session_name'];
                $val = $row['value'];
                $confs[$ses] = $val;            
            }

            $featureModel->setSettingsList((!is_null($confs) && count($confs) > 0) ? $confs : array());

            $ret = true;
            $result = array("message"=>"","object"=>$featureModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error getting settings by category ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * fetchConfigsByCategory
     * 
     * en_us Returns array with settings data by category
     * pt_br Retorna array com dados de configurações por categoria
     *
     * @param  featureModel $featureModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchEmailTempSettings(featureModel $featureModel): array
    {
        
        $sql = "SELECT session_name, `description` AS `value` FROM tbconfig WHERE idconfigcategory = 11";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            while($row = $stmt->fetch(\PDO::FETCH_ASSOC)){
                $ses = $row['session_name'];
                $val = $row['value'];
                $confs[$ses] = $val;            
            }

            $featureModel->setSettingsList((!is_null($confs) && count($confs) > 0) ? $confs : array());

            $ret = true;
            $result = array("message"=>"","object"=>$featureModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error getting email header and footer settings ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * updateSettingValue
     * 
     * en_us Updates setting's changes in tbconfig table
     * pt_br Grava as alterações das configurações na tabela tbconfig
     *
     * @param  mixed $featureModel
     * @return array
     */
    public function updateSettingValue(featureModel $featureModel): array
    {   
        if(in_array($featureModel->getSessionName(),array("EM_HEADER","EM_FOOTER"))){
            $sql = "UPDATE tbconfig SET `description` = :settingValue WHERE session_name = :sessionName";
        }else{
            $sql = "UPDATE tbconfig SET `value` = :settingValue WHERE session_name = :sessionName";
        }        

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":settingValue",$featureModel->getSettingValue());
        $stmt->bindValue(":sessionName",$featureModel->getSessionName());
        $stmt->execute();

        $ret = true;
        $result = array("message"=>"","object"=>$featureModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * saveSettingsChanges
     * 
     * en_us Saves setting's changes in DB
     * pt_br Grava as alterações das configurações no BD
     *
     * @param  featureModel $featureModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function saveSettingsChanges(featureModel $featureModel): array
    {        
        $aSettings = $featureModel->getSettingsList();
        try{
            $this->db->beginTransaction();

            foreach($aSettings as $k=>$v){
                $featureModel->setSessionName($k)
                             ->setSettingValue($v);

                $this->updateSettingValue($featureModel);
            }            

            $ret = true;
            $result = array("message"=>"","object"=>$featureModel);

            $this->db->commit();
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error updating settings ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);

            $this->db->rollBack();
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * Returns user's settings
     *
     * @param  featureModel $featureModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchUserSettings(featureModel $featureModel): array
    {        
        $sql = "SELECT idconfiguser,idperson,idlocale,idtheme,grid_operator,grid_operator_width,
                        grid_user,grid_user_width
                  FROM hdk_tbconfig_user
                 WHERE idperson = :userID";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':userID', $featureModel->getUserID());
            $stmt->execute();
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            $featureModel->setUserSettingsList($rows);
            $ret = true;
            $result = array("message"=>"","object"=>$featureModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting user's settings ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * Check if a table exists in DB
     *
     * @param  featureModel $featureModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function tableExists(featureModel $featureModel): array
    {        
        $sql = "SELECT COUNT(*) as exist
                  FROM information_schema.tables 
                 WHERE table_schema = '{$_ENV['DB_NAME']}' 
                   AND table_name = :tableName";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':tableName', $featureModel->getTableName());
            $stmt->execute();
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);

            $flg = ($row['exist'] > 0) ? true : false;
            $featureModel->setExistTable($flg);
            $ret = true;
            $result = array("message"=>"","object"=>$featureModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error checking table exists ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * Return module path by user type
     *
     * @param  featureModel $featureModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function getPathModuleByTypePerson(featureModel $featureModel): array
    {        
        $sql = "SELECT `path`
                  FROM tbmodule a
       LEFT OUTER JOIN tbtypeperson_has_module b
                    ON b.idmodule = a.idmodule
                 WHERE b.idtypeperson =  :typePerson";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':typePerson', $featureModel->getUserType());
            $stmt->execute();
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);

            $featureModel->setPath($row['path']);
            $ret = true;
            $result = array("message"=>"","object"=>$featureModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting module's path", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * Returns email's settings
     *
     * @param  emailSettingsModel $emailSettingsModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function getEmailSettings(emailSettingsModel $emailSettingsModel): array
    {        
        $sql = "SELECT session_name,IF(`value` IS NULL OR `value` = '',description,`value`) `value` 
                  FROM tbconfig 
                 WHERE idconfigcategory IN (5,11)";
        
        try{
            $settingsList = array("EM_TITLE","EM_DOMAIN","EM_SENDER","EM_AUTH","EM_HEADER","EM_FOOTER","EM_TLS");

            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            foreach($rows as $k=>$v){
                if(in_array($v['session_name'],$settingsList)){
                    switch($v['session_name']){
                        case "EM_TITLE":
                            $emailSettingsModel->setTitle($v['value']);
                            break;
                        case "EM_DOMAIN":
                            $emailSettingsModel->setDomain($v['value']);
                            break;
                        case "EM_SENDER":
                            $emailSettingsModel->setSender($v['value']);
                            break;
                        case "EM_AUTH":
                            $emailSettingsModel->setAuth($v['value']);
                            break;
                        case "EM_HEADER":
                            $emailSettingsModel->setHeader($v['value']);
                            break;
                        case "EM_FOOTER":
                            $emailSettingsModel->setFooter($v['value']);
                            break;
                        case "EM_TLS":
                            $emailSettingsModel->setTls($v['value']);
                            break;
                    }

                }
            }

            $ret = true;
            $result = array("message"=>"","object"=>$emailSettingsModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting user's settings ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * fetchModuleSettingCategories
     * 
     * en_us Returns module's settings categories
     * pt_br Retorna as categorias de configurações do módulo
     *
     * @param  featureModel $featureModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchModuleSettingCategories(featureModel $featureModel): array
    {        
        $table = $featureModel->getTableName();
        $sql = "SELECT idconfigcategory, `name`, smarty FROM $table WHERE flgsetup = 'Y' ORDER BY `name`";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $featureModel->setSettingsCatList($aRet);
            
            $ret = true;
            $result = array("message"=>"","object"=>$featureModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error getting module settings categories', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * fetchModuleSettings
     * 
     * en_us Returns module's settings by category
     * pt_br Retorno as configurações do módulo pela categoria
     *
     * @param  featureModel $featureModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchModuleSettings(featureModel $featureModel): array
    {        
        $table = $featureModel->getTableName();
        $sql = "SELECT idconfig, `status`, `value`, `name`, smarty, field_type, allowremove 
                  FROM $table 
                 WHERE idconfigcategory = :settingCatId 
              ORDER BY `name`";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':settingCatId', $featureModel->getSettingCatId());
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $featureModel->setSettingsList($aRet);
            
            $ret = true;
            $result = array("message"=>"","object"=>$featureModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error getting module settings categories', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * updateSettingValueById
     *
     * en_us Updates module's settings by id
     * pt_br Atualiza a configuração do módulo pelo id
     *
     * @param  featureModel $featureModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function updateSettingValueById(featureModel $featureModel): array
    {        
        $table = $featureModel->getTableName();
        $sql = "UPDATE $table SET `value` = :newValue WHERE idconfig = :settingId";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':newValue', $featureModel->getSettingValue());
            $stmt->bindValue(':settingId', $featureModel->getSettingId());
            $stmt->execute();

            $ret = true;
            $result = array("message"=>"","object"=>$featureModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error updating setting's value", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * deleteSetting
     *
     * en_us Removes module's settings
     * pt_br Deleta a configuração do módulo
     *
     * @param  featureModel $featureModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function deleteSetting(featureModel $featureModel): array
    {        
        $table = $featureModel->getTableName();
        $sql = "DELETE FROM $table WHERE idconfig = :settingId";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':settingId', $featureModel->getSettingId());
            $stmt->execute();

            $ret = true;
            $result = array("message"=>"","object"=>$featureModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error deleting setting", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * getFeatureCategoryByName
     *
     * en_us Returns feature's category by name
     * pt_br Retorna a categoria de configuração pelo nome
     *
     * @param  featureModel $featureModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function getFeatureCategoryByName(featureModel $featureModel): array
    {        
        $table = $featureModel->getTableName();
        $sql = "SELECT idconfigcategory FROM $table WHERE `name` = :name";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':name', $featureModel->getSettingCatName());
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            $featureModel->setSettingCatId((!is_null($aRet['idconfigcategory']) && !empty($aRet['idconfigcategory'])) ? $aRet['idconfigcategory'] : 0);
            
            $ret = true;
            $result = array("message"=>"","object"=>$featureModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error getting feature category', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * insertNewCategory
     *
     * en_us Saves feature's new category
     * pt_br Grava a nova categoria de configuração
     *
     * @param  featureModel $featureModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function insertNewCategory(featureModel $featureModel): array
    {        
        $table = $featureModel->getTableName();
        $sql = "INSERT INTO $table (`name`,smarty,flgsetup) VALUES (:name,:langKey,:flgSetup)";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':name', $featureModel->getSettingCatName());
            $stmt->bindValue(':langKey', $featureModel->getSettingCatLangKey());
            $stmt->bindValue(':flgSetup', $featureModel->getSettingCatFlgSetup());
            $stmt->execute();

            $featureModel->setSettingCatId($this->db->lastInsertId());

            $ret = true;
            $result = array("message"=>"","object"=>$featureModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error inserting setting's category", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * getFeatureIdByField
     *
     * en_us Returns feature ID by field value
     * pt_br Retorna o ID da configuração pelo valor do campo
     *
     * @param  featureModel $featureModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function getFeatureIdByField(featureModel $featureModel): array
    {        
        $table = $featureModel->getTableName();
        $field = $featureModel->getFieldName();

        $sql = "SELECT idconfig FROM $table WHERE $field = :fieldValue";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':fieldValue', $featureModel->getFieldValue());
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            $featureModel->setSettingId((!is_null($aRet['idconfig']) && !empty($aRet['idconfig'])) ? $aRet['idconfig'] : 0);
            
            $ret = true;
            $result = array("message"=>"","object"=>$featureModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error getting feature by name', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * insertNewFeature
     *
     * en_us Saves feature's data
     * pt_br Grava os dados da configuração
     *
     * @param  featureModel $featureModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function insertNewFeature(featureModel $featureModel): array
    {        
        $table = $featureModel->getTableName();
        $sql = "INSERT INTO $table (`name`,`description`,idconfigcategory,session_name,field_type,`status`,smarty,`value`,allowremove) 
                     VALUES (:name,:description,:settingCatId,:sessionName,:fieldType,'A',:langKey,:settingValue,:default)";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':name', $featureModel->getSettingName());
            $stmt->bindValue(':description', $featureModel->getSettingDescription());
            $stmt->bindValue(':settingCatId', $featureModel->getSettingCatId());
            $stmt->bindValue(':sessionName', $featureModel->getSessionName());
            $stmt->bindValue(':fieldType', $featureModel->getFieldType());
            $stmt->bindValue(':langKey', $featureModel->getSettingLangKey());
            $stmt->bindValue(':settingValue', $featureModel->getSettingValue());
            $stmt->bindValue(':default', $featureModel->getFlagDefault());
            $stmt->execute();

            $featureModel->setSettingId($this->db->lastInsertId());

            $ret = true;
            $result = array("message"=>"","object"=>$featureModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error inserting setting's data", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
}