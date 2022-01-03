<?php

namespace App\modules\main\dao\mysql;

use App\core\Database;
use App\modules\main\models\mysql\externalappModel;
use App\modules\main\models\mysql\externalappfieldModel;

class externalappDAO extends Database
{
    
    public function __construct()
    {
        parent::__construct(); 
    }
    
    /**
     * Returns a list of external apps
     *
     * @param  externalappModel $externalappModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchExternalApps(externalappModel $externalappModel): array
    {

        $sql = "SELECT idexternakapp, appname, url
                  FROM hdk_tbexternallapp";

        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $externalappModel->setGridList($aRet);
            
            $ret = true;
            $result = array("message"=>"","object"=>$externalappModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting external apps  ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * Returns logo's data by name
     *
     * @param  externalappModel $externalappModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function getExternalAppByName(externalappModel $externalappModel): array
    {
        
        $sql = "SELECT idexternalapp,appname,url FROM hdk_tbexternallapp WHERE appname = :name";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":name",$externalappModel->getAppName());
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            $externalappModel->setIdExternalApp($aRet ? $aRet['idexternalapp'] : 0)
                             ->setAppName($aRet ? $aRet['appname'] : "")
                             ->setAppUrl($aRet ? $aRet['url'] : "");
            $ret = true;
            $result = array("message"=>"","object"=>$externalappModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error trying get external app data ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * Returns logo's data by name
     *
     * @param  externalappModel $externalappModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function getExtAppSettingByUser(externalappModel $externalappModel): array
    {
        $sql = "SELECT idexternalsetting,idexternalapp,idperson 
                  FROM hdk_tbexternalsettings
                 WHERE idperson = :userID
                   AND idexternalapp = :extappID";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":userID",$externalappModel->getUserID());
            $stmt->bindParam(":extappID",$externalappModel->getIdExternalApp());
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            $externalappModel->setIdExternalSetting($aRet ? $aRet['idexternalsetting'] : 0);
            $ret = true;
            $result = array("message"=>"","object"=>$externalappModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error trying get user external app setting ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * Insert user's external app settings into the database
     *
     * @param  usersettingsModel $usersettingsModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function insertUserSettings(usersettingsModel $usersettingsModel): array
    {        
        $sql = "INSERT INTO hdk_tbconfig_user (idperson,idlocale,idtheme,display_grid)
                     VALUES(:userID,:localeID,:themeID,:displayGrid)";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':userID', $usersettingsModel->getUserID());
            $stmt->bindParam(':localeID', $usersettingsModel->getIdlocale());
            $stmt->bindParam(':themeID', $usersettingsModel->getIdtheme());
            $stmt->bindParam(':displayGrid', $usersettingsModel->getDisplayGrid());
            $stmt->execute();

            $usersettingsModel->setUserSettingID($this->db->lastInsertId());
            $ret = true;
            $result = array("message"=>"","object"=>$usersettingsModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error trying insert user's settings ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }         
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * Returns logo's data by name
     *
     * @param  externalappfieldModel $externalappfieldModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function getExtAppFieldByName(externalappfieldModel $externalappfieldModel): array
    {
        $sql = "SELECT idexternalfield,idexternalsettings,fieldname,`value` 
                  FROM hdk_tbexternalfield
                 WHERE idexternalsettings = :extSettingID
                   AND fieldname = :fieldName";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":extSettingID",$externalappfieldModel->getIdExternalSetting());
            $stmt->bindParam(":fieldName",$externalappfieldModel->getFieldName());
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            $externalappfieldModel->setIdExternalField($aRet ? $aRet['idexternalfield'] : 0);
            $ret = true;
            $result = array("message"=>"","object"=>$externalappfieldModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error trying get external app field ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * Insert user's external app setting into the database
     *
     * @param  externalappModel $externalappModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function insertUserExternalApp(externalappModel $externalappModel): array
    {        
        $sql = "INSERT INTO hdk_tbexternalsettings (idexternalapp,idperson)
                     VALUES(:extappID,:userID)";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":userID",$externalappModel->getUserID());
            $stmt->bindParam(":extappID",$externalappModel->getIdExternalApp());
            $stmt->execute();

            $externalappModel->setIdExternalSetting($this->db->lastInsertId());
            $ret = true;
            $result = array("message"=>"","object"=>$externalappModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error trying insert external app field ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }         
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * Insert user's external app field into the database
     *
     * @param  externalappfieldModel $externalappfieldModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function insertExternalAppField(externalappfieldModel $externalappfieldModel): array
    {        
        $sql = "INSERT INTO hdk_tbexternalfield (idexternalsettings,fieldname,`value`)
                     VALUES(:extSettingID,:fieldName,:fieldValue)";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":extSettingID",$externalappfieldModel->getIdExternalSetting());
            $stmt->bindParam(":fieldName",$externalappfieldModel->getFieldName());
            $stmt->bindParam(":fieldValue",$externalappfieldModel->getFieldValue());
            $stmt->execute();

            $externalappfieldModel->setIdExternalField($this->db->lastInsertId());
            $ret = true;
            $result = array("message"=>"","object"=>$externalappfieldModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error trying insert external app field ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }         
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * Update user's settings into the database
     *
     * @param  externalappfieldModel $externalappfieldModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function updateExternalAppField(externalappfieldModel $externalappfieldModel): array
    {        
        $sql = "UPDATE hdk_tbexternalfield
                   SET fieldname = :fieldName,
                       `value` = :fieldValue
                 WHERE idexternalfield = :externalFieldID";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":fieldName",$externalappfieldModel->getFieldName());
            $stmt->bindParam(":fieldValue",$externalappfieldModel->getFieldValue());
            $stmt->bindParam(':externalFieldID', $externalappfieldModel->getIdExternalField());
            $stmt->execute();

            $ret = true;
            $result = array("message"=>"","object"=>$externalappfieldModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error trying update external app field ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
    }

}