<?php

namespace App\modules\exp\dao\mysql;

use App\core\Database;
use App\modules\exp\models\mysql\cityModel;

class cityDAO extends Database
{
    
    public function __construct()
    {
        parent::__construct(); 
    }
    
    /**
     * Return an array with cities to display in grid
     *
     * @param  string $where
     * @param  string $group
     * @param  string $order
     * @param  string $limit
     * @return array
     */
    public function queryCities($where=null,$group=null,$order=null,$limit=null): array
    {
        
        $sql = "SELECT idcity, a.idstate, a.name city, b.name uf_name, dtfoundation, `default`, a.status, b.abbr uf
                  FROM exp_tbcity a, tbstate b
                 WHERE a.idstate = b.idstate
                $where $group $order $limit";
        //echo "{$sql}\n";
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $city = new cityModel(); 
            $city->setGridList($aRet);

            $ret = true;
            $result = array("message"=>"","object"=>$city);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting cities ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
        
    /**
     * Insert city's data into the database
     *
     * @param  mixed $cityModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function insertCity(cityModel $cityModel): array
    {        
        $sql = "INSERT INTO exp_tbcity (idstate,`name`,dtfoundation,`default`)
                     VALUES(:uf,:name,:dtFoundation,:flgDefault)";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':uf', $cityModel->getIdState());
            $stmt->bindParam(':name', $cityModel->getName());
            $stmt->bindParam(':dtFoundation', $cityModel->getDtFoundation());
            $stmt->bindParam(':flgDefault', $cityModel->getIsDefault());
            $stmt->execute();

            $cityModel->setIdCity($this->db->lastInsertId());
            $ret = true;
            $result = array("message"=>"","object"=>$cityModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error trying insert city ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }         
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * Link the city with uploaded image
     *
     * @param  mixed $cityModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function insertCityImage(cityModel $cityModel): array
    {        
        $sql = "INSERT INTO exp_tbcity_image (idcity,filename)
                     VALUES(:cityID,:fileName)";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':cityID', $cityModel->getIdCity());
            $stmt->bindParam(':fileName', $cityModel->getFileName());
            $stmt->execute();

            $cityModel->setIdImage($this->db->lastInsertId());
            $ret = true;
            $result = array("message"=>"","object"=>$cityModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error trying link city with uploaded image ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * Returns a object with city data
     *
     * @param  cityModel $cityModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function getCity(cityModel $cityModel): array
    {        
        $sql = "SELECT idcity,a.idstate,b.name state_name,a.`name`,dtfoundation,`default`,`status`
                  FROM exp_tbcity a, tbstate b
                 WHERE a.idstate = b.idstate
                   AND idcity = :cityID";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':cityID', $cityModel->getIdCity());
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            $cityModel->setIdState($aRet['idstate'])
                      ->setStateName($aRet['state_name'])
                      ->setName($aRet['name'])
                      ->setDtFoundation($aRet['dtfoundation'])
                      ->setIsDefault($aRet['default'])
                      ->setStatus($aRet['status']);

            $ret = true;
            $result = array("message"=>"","object"=>$cityModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error getting holiday data ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * Update the city into the database
     *
     * @param  cityModel $cityModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function updateCity(cityModel $cityModel): array
    {        
        $sql = "UPDATE exp_tbcity
                   SET idstate = :uf,
                        `name` = :name,
                        dtfoundation = :dtFoundation,
                        `default` = :flgDefault
                 WHERE idcity = :cityID";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':uf', $cityModel->getIdState());
            $stmt->bindParam(':name', $cityModel->getName());
            $stmt->bindParam(':dtFoundation', $cityModel->getDtFoundation());
            $stmt->bindParam(':flgDefault', $cityModel->getIsDefault());
            $stmt->bindParam(':cityID', $cityModel->getIdCity());
            $stmt->execute();

            $ret = true;
            $result = array("message"=>"","object"=>$cityModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error trying update city ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
    }
    
        
    /**
     * Returns array with the uploaded image linked with the city
     *
     * @param  cityModel $cityModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchCityImage(cityModel $cityModel): array
    {        
        $sql = "SELECT idimage,idcity,filename,fileuploaded
                  FROM `exp_tbcity_image`
                 WHERE idcity = :cityID";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':cityID', $cityModel->getIdCity());
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $cityModel->setAttachments($aRet);

            $ret = true;
            $result = array("message"=>"","object"=>$cityModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting uploded image ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * Update city's status
     *
     * @param  cityModel $cityModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function updateStatus(cityModel $cityModel): array
    {        
        $sql = "UPDATE exp_tbcity
                   SET `status` = :newStatus
                 WHERE idcity = :cityID";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':newStatus', $cityModel->getStatus());
            $stmt->bindParam(':cityID', $cityModel->getIdCity());
            $stmt->execute();

            $ret = true;
            $result = array("message"=>"","object"=>$cityModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error trying update city's status", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
        
    /**
     * Delete city from DB
     *
     * @param  cityModel $cityModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function deleteCity(cityModel $cityModel): array
    {        
        $sql = "DELETE FROM exp_tbcity WHERE idcity = :cityID";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':cityID', $cityModel->getIdCity());
            $stmt->execute();

            $ret = true;
            $result = array("message"=>"","object"=>$cityModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error trying delete city ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * Delete the uploaded image from the database
     *
     * @param  cityModel $cityModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function deleteCityImage(cityModel $cityModel): array
    {        
        $sql = "DELETE FROM exp_tbcity_image WHERE idimage = :imageID";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':imageID', $cityModel->getIdImage());
            $stmt->execute();

            $ret = true;
            $result = array("message"=>"","object"=>$cityModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error trying delete uploaded image ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * Update uploaded image name
     *
     * @param  cityModel $cityModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function updateCityImageName(cityModel $cityModel): array
    {        
        $sql = "UPDATE exp_tbcity_image
                   SET fileuploaded = :newName
                 WHERE idimage = :imageID";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':newName', $cityModel->getNewFileName());
            $stmt->bindParam(':imageID', $cityModel->getIdImage());
            $stmt->execute();

            $ret = true;
            $result = array("message"=>"","object"=>$cityModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error trying update loaded image name ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * Delete the uploaded image from the database
     *
     * @param  cityModel $cityModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function deleteCityImageByCity(cityModel $cityModel): array
    {        
        $sql = "DELETE FROM exp_tbcity_image WHERE idcity = :cityID";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':cityID', $cityModel->getIdCity());
            $stmt->execute();

            $ret = true;
            $result = array("message"=>"","object"=>$cityModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error trying delete uploaded image ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
    }

}