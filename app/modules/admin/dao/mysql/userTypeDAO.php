<?php

namespace App\modules\admin\dao\mysql;

use App\core\Database;
use App\modules\admin\models\mysql\userTypeModel;

class userTypeDAO extends Database
{
    
    public function __construct()
    {
        parent::__construct(); 
    }
    
    /**
     * Return an array with userType to display in grid
     *
     * @param  string $where
     * @param  string $group
     * @param  string $order
     * @param  string $limit
     * @return array
     */  
    
    public function queryUserType($where=null,$group=null,$order=null,$limit=null): array
    {
        
        $sql = "SELECT idtypeperson,`name`,permissiongroup , lang_key_name, `status`
                FROM tbtypeperson 
                $where $group $order $limit";
              // echo "{$sql}\n";
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $userType = new userTypeModel(); 
            $userType->setgridList($aRet);

            $ret = true;
            $result = array("message"=>"","object"=>$userType);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error query userType ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    public function countUserType($where=null): array
    {        
        $sql = "SELECT COUNT(idtypeperson) total
                FROM tbtypeperson 
                $where";
    
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            $userType = new userTypeModel();
            $userType->setTotalRows($aRet['total']);

            $ret = true;
            $result = array("message"=>"","object"=>$userType);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error counting userType ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    public function getUserType(userTypeModel $userTypeModel): array
    {  

        $sql = "SELECT idtypeperson,`name`,permissiongroup , lang_key_name, `status`
                FROM tbtypeperson 
                WHERE idtypeperson=:userTypeID";
                 
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':userTypeID', $userTypeModel->getIdUserType());
            $stmt->execute();
           
            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            $userTypeModel->setIdUserType($aRet['idtypeperson'])
                                ->setUserType($aRet['name'])
                                ->setPermissionGroup($aRet['permissiongroup'])
                                ->setLangKeyName($aRet['lang_key_name']);

            $ret = true;
            $result = array("message"=>"","object"=>$userTypeModel);
        }catch(\PDOException $ex){ $msg = $ex->getMessage();
            $this->loggerDB->error('Error get userType ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
        
    } 

    /**
     * Insert userType's data into the database
     * @param  string $userType
     * @param  string $default
     * @return userTypeModel
     */

    public function insertUserType(userTypeModel $userTypeModel): array
    {        
        $sql = "INSERT INTO tbtypeperson(`name`,`permissiongroup`,`lang_key_name`)
                VALUES(:userType,:permissionGroup,:langKeyName)";
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':userType', $userTypeModel->getUserType());
            $stmt->bindParam(':permissionGroup', $userTypeModel->getPermissionGroup());
            $stmt->bindParam(':langKeyName', $userTypeModel->getLangKeyName());
            $stmt->execute();

            $userTypeModel->setIdUserType($this->db->lastInsertId());
            $ret = true;
            $result = array("message"=>"","object"=>$userTypeModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error insert userType's", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    public function updateUserType(userTypeModel $userTypeModel): array
    {        
        $sql = "UPDATE tbtypeperson
                SET `name` = :userType,
                `permissiongroup` = :permissionGroup, 
                `lang_key_name` = :langKeyName                   
                WHERE idtypeperson = :userTypeID";  
                
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':userTypeID', $userTypeModel->getIdUserType());
            $stmt->bindParam(':userType', $userTypeModel->getUserType());
            $stmt->bindParam(':permissionGroup', $userTypeModel->getPermissionGroup());
            $stmt->bindParam(':langKeyName', $userTypeModel->getLangKeyName());
            $stmt->execute();

            $ret = true;
            $result = array("message"=>"","object"=>$userTypeModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error update userType', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    public function updateStatus(userTypeModel $userTypeModel): array
    {        
        $sql = "UPDATE tbtypeperson
                SET status = :newStatus
                WHERE idtypeperson = :userTypeID";
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':userTypeID', $userTypeModel->getIdUserType());
            $stmt->bindParam(':newStatus', $userTypeModel->getStatus());
            $stmt->execute();

            $ret = true;
            $result = array("message"=>"","object"=>$userTypeModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error update userType's status", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    public function deleteUserType(userTypeModel $userTypeModel): array
    {        
        $sql = "DELETE FROM tbtypeperson
                WHERE idtypeperson = :userTypeID";        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':userTypeID', $userTypeModel->getIdUserType());
            $stmt->execute();

            $ret = true;
            $result = array("message"=>"","object"=>$userTypeModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error delete userType ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
}