<?php

namespace App\modules\admin\dao\mysql;

use App\core\Database;
use App\modules\admin\models\mysql\apiTokenModel;

class apiTokenDAO extends Database
{
    
    public function __construct()
    {
        parent::__construct(); 
    }

    /**
     * Return an array with apiToken to display in grid
     *
     * @param  string $where
     * @param  string $group
     * @param  string $order
     * @param  string $limit
     * @return array
     */
    public function queryApiToken($where=null,$group=null,$order=null,$limit=null): array
    {
        
        $sql = "SELECT idtoken, app, company, email, token,expiration
                  FROM tbtoken
                $where $group $order $limit";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $apiToken = new apiTokenModel(); 
            $apiToken->setGridList($aRet);

            $ret = true;
            $result = array("message"=>"","object"=>$apiToken);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error query apiToken ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * Return an array with apiToken to display in grid
     *
     * @param  string $where
     * @return array
     */
    public function countApiToken($where=null): array
    {
        
        $sql = "SELECT COUNT(idtoken) total
                  FROM tbtoken
                  WHERE idtoken= idtoken 
                $where";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            $apiToken = new apiTokenModel(); 
            $apiToken->setTotalRows($aRet['total']);

            $ret = true;
            $result = array("message"=>"","object"=>$apiToken);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error counting apiTokens ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    public function insertApiToken(apiTokenModel $apiTokenModel): array
    {    
        $sql = "INSERT INTO tbtoken(`app`,`company`, `email`, `token`, `expiration`) 
                   VALUES(:app,:company,:email,:apiToken,:validity)";
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':app', $apiTokenModel->getApp());            
            $stmt->bindParam(':company', $apiTokenModel->getCompany());
            $stmt->bindParam(':email', $apiTokenModel->getEmail());
            $stmt->bindParam(':apiToken', $apiTokenModel->getApiToken());
            $stmt->bindParam(':validity', $apiTokenModel->getValidity());
            $stmt->execute();

            $apiTokenModel->setIdApiToken($this->db->lastInsertId());
            $ret = true;
            $result = array("message"=>"","object"=>$apiTokenModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error insert costCenter ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    public function getApiToken(apiTokenModel $apiTokenModel): array
    {  

        $sql = "SELECT idtoken, app, company, email, token,expiration
                FROM tbtoken
                WHERE idtoken = :apiTokenID";
                 
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':apiTokenID', $apiTokenModel->getIdApiToken());
            $stmt->execute();
           
            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            $apiTokenModel->setIdApiToken($aRet['idtoken'])
                                ->setApp($aRet['app'])
                                ->setCompany($aRet['company'])
                                ->setEmail($aRet['email'])
                                ->setApiToken($aRet['token'])
                                ->setValidity($aRet['expiration']);

            $ret = true;
            $result = array("message"=>"","object"=>$apiTokenModel);
        }catch(\PDOException $ex){ $msg = $ex->getMessage();
            $this->loggerDB->error('Error get apiToken ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
        
    } 
    
    public function updateApiToken(apiTokenModel $apiTokenModel): array
    {        
        $sql = "UPDATE tbtoken
                SET `app` = :app,
                    `company` = :company,
                    `email` = :email,
                    `token` = :apiToken,
                    `expiration` = :validity
                WHERE idtoken = :apiTokenID"; 

        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':apiTokenID', $apiTokenModel->getIdApiToken());
            $stmt->bindParam(':app', $apiTokenModel->getApp());
            $stmt->bindParam(':company', $apiTokenModel->getCompany());
            $stmt->bindParam(':email', $apiTokenModel->getEmail());
            $stmt->bindParam(':apiToken', $apiTokenModel->getApiToken());
            $stmt->bindParam(':validity', $apiTokenModel->getValidity());           
            $stmt->execute();

            $ret = true;
            $result = array("message"=>"","object"=>$apiTokenModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error update apiToken', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    public function updateToken(apiTokenModel $apiTokenModel): array
    {        
        $sql = "UPDATE tbtoken
                SET  `token` = :apiToken
                WHERE idtoken = :apiTokenID"; 
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':apiTokenID', $apiTokenModel->getIdApiToken());
            $stmt->bindParam(':apiToken', $apiTokenModel->getApiToken());           
            $stmt->execute();

            $ret = true;
            $result = array("message"=>"","object"=>$apiTokenModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error update Token', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    public function deleteApiToken(apiTokenModel $apiTokenModel): array
    {        
        $sql = "DELETE FROM tbtoken
                WHERE idtoken = :apiTokenID";        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':apiTokenID', $apiTokenModel->getIdApiToken());
            $stmt->execute();

            $ret = true;
            $result = array("message"=>"","object"=>$apiTokenModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error delete apiToken ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
}