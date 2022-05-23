<?php

namespace App\modules\helpdezk\dao\mysql;

use App\core\Database;

use App\modules\helpdezk\models\mysql\hdkServiceModel;

class hdkServiceDAO extends Database
{
    public function __construct()
    {
        parent::__construct(); 
    }

    /**
     * Return an array with a area's list
     *
     * @param  hdkServiceModel $hdkServiceModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchArea(hdkServiceModel $hdkServiceModel): array
    {        
        $sql = "SELECT idarea, `name`, `default`
                  FROM hdk_tbcore_area
                 WHERE `status` = 'A'
              ORDER BY `name` ASC";
        //echo "{$sql}\n";
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $hdkServiceModel->setAreaList($aRet);

            $ret = true;
            $result = array("message"=>"","object"=>$hdkServiceModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting areas ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * Return an array with a type's list
     *
     * @param  hdkServiceModel $hdkServiceModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchTypeByArea(hdkServiceModel $hdkServiceModel): array
    {        
        $sql = "SELECT idtype, `name`, `selected` AS `default`
                  FROM hdk_tbcore_type
                 WHERE idarea = :areaID
                   AND `status` = 'A'
              ORDER BY `name` ASC";
        //echo "{$sql}\n";
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':areaID', $hdkServiceModel->getIdArea());
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $hdkServiceModel->setTypeList($aRet);

            $ret = true;
            $result = array("message"=>"","object"=>$hdkServiceModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting types ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * Return an array with a item's list
     *
     * @param  hdkServiceModel $hdkServiceModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchItemByType(hdkServiceModel $hdkServiceModel): array
    {        
        $sql = "SELECT iditem, `name`, `selected` AS `default`
                  FROM hdk_tbcore_item
                 WHERE idtype =  = :typeID
                   AND `status` = 'A'
              ORDER BY `name` ASC";
        //echo "{$sql}\n";
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':typeID', $hdkServiceModel->getIdType());
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $hdkServiceModel->setItemList($aRet);

            $ret = true;
            $result = array("message"=>"","object"=>$hdkServiceModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting items ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * Return an array with a item's list
     *
     * @param  hdkServiceModel $hdkServiceModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchServiceByItem(hdkServiceModel $hdkServiceModel): array
    {        
        $sql = "SELECT idservice, `name`, `selected` AS `default`
                  FROM hdk_tbcore_service
                 WHERE iditem = :itemID
                   AND `status` = 'A'
              ORDER BY `name` ASC";
        //echo "{$sql}\n";
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':itemID', $hdkServiceModel->getIdItem());
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $hdkServiceModel->setServiceList($aRet);

            $ret = true;
            $result = array("message"=>"","object"=>$hdkServiceModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting items ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
}