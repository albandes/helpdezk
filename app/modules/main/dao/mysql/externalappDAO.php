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
            
            $externalappModel->setIdexternalapp($aRet['idexternalapp'])
                             ->setAppName($aRet['appname'])
                             ->setAppUrl($aRet['url']);
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
                  FROM hdk_tbexternallapp 
                 WHERE idperson = :userID
                   AND idexternalapp = :extappID";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":userID",$externalappModel->getUserID());
            $stmt->bindParam(":extappID",$externalappModel->getIdexternalapp());
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            $externalappModel->setIdexternalsetting($aRet ? $aRet['idexternalsetting'] : 0);
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

}