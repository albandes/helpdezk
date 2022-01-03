<?php

namespace App\modules\main\dao\mysql;

use App\core\Database;
use App\modules\main\models\mysql\usersettingsModel;

class usersettingsDAO extends Database
{
    
    public function __construct()
    {
        parent::__construct(); 
    }
    
    /**
     * Returns a list of external apps
     *
     * @param  usersettingsModel $usersettingsModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchUserSettings(usersettingsModel $usersettingsModel): array
    {

        $sql = "SELECT idconfiguser,idperson,idlocale,idtheme,grid_operator,grid_operator_width,
                        grid_user,grid_user_width,display_grid
                  FROM hdk_tbconfig_user";

        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $usersettingsModel->setGridList($aRet);
            
            $ret = true;
            $result = array("message"=>"","object"=>$usersettingsModel);
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
     * @param  usersettingsModel $usersettingsModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function getUserSettingsByUser(usersettingsModel $usersettingsModel): array
    {
        
        $sql = "SELECT idconfiguser,idperson,idlocale,idtheme,grid_operator,grid_operator_width,
                        grid_user,grid_user_width,display_grid
                  FROM hdk_tbconfig_user 
                 WHERE idperson = :userID";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":userID",$usersettingsModel->getUserID());
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            $usersettingsModel->setUserSettingID($aRet ? $aRet['idconfiguser'] : 0);
            $ret = true;
            $result = array("message"=>"","object"=>$usersettingsModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error trying get external app data ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * Insert user's settings into the database
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
     * Update user's settings into the database
     *
     * @param  usersettingsModel $usersettingsModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function updateUserSettings(usersettingsModel $usersettingsModel): array
    {        
        $sql = "UPDATE hdk_tbconfig_user
                   SET idlocale = :localeID,
                        idtheme = :themeID,
                        display_grid = :displayGrid
                 WHERE idconfiguser = :userSettingID";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':localeID', $usersettingsModel->getIdlocale());
            $stmt->bindParam(':themeID', $usersettingsModel->getIdtheme());
            $stmt->bindParam(':displayGrid', $usersettingsModel->getDisplayGrid());
            $stmt->bindParam(':userSettingID', $usersettingsModel->getUserSettingID());
            $stmt->execute();

            $ret = true;
            $result = array("message"=>"","object"=>$usersettingsModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error trying update user's settings ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
    }

}