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
        }catch(\PDOException $ex){
            $this->loggerDB->error("Error getting holidays ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $ex->getMessage()]);
            return array();
        }
        
        $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        if(!$aRet){
            return array();
        }
        
        return $aRet;
    }
        
    /**
     * Insert city's data into the database
     *
     * @param  int $uf
     * @param  string $name
     * @param  string $dtFoundation
     * @param  int $flgDefault
     * @return cityModel
     */
    public function insertCity(int $uf, string $name, string $dtFoundation, int $flgDefault): ?cityModel
    {        
        $sql = "INSERT INTO exp_tbcity (idstate,`name`,dtfoundation,`default`)
                     VALUES(:uf,:name,:dtFoundation,:flgDefault)";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':uf', $uf);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':dtFoundation', $dtFoundation);
            $stmt->bindParam(':flgDefault', $flgDefault);
            $stmt->execute();
        }catch(\PDOException $ex){
            $this->loggerDB->error('Error trying insert city ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $ex->getMessage()]);
            return null;
        }

        $city = new cityModel(); 
        $city->setIdcity($this->db->lastInsertId()); 
        
        return $city;
    }
    
    /**
     * Link the city with uploaded image
     *
     * @param  int $cityID
     * @param  string $fileName
     * @return cityModel
     */
    public function insertCityImage(int $cityID, string $fileName): ?cityModel
    {        
        $sql = "INSERT INTO exp_tbcity_image (idcity,filename)
                     VALUES(:cityID,:fileName)";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':cityID', $cityID);
            $stmt->bindParam(':fileName', $fileName);
            $stmt->execute();
        }catch(\PDOException $ex){
            $this->loggerDB->error('Error trying link city with uploaded image ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $ex->getMessage()]);
            return null;
        }

        $city = new cityModel(); 
        $city->setIdimage($this->db->lastInsertId()); 
        
        return $city;
    }
    
    /**
     * Returns a object with city data
     *
     * @param  int $cityID
     * @return cityModel
     */
    public function getCity(int $cityID): ?cityModel
    {        
        $sql = "SELECT idcity,a.idstate,b.name state_name,a.`name`,dtfoundation,`default`,`status`
                  FROM exp_tbcity a, tbstate b
                 WHERE a.idstate = b.idstate
                   AND idcity = :cityID";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':cityID', $cityID);
            $stmt->execute();
        }catch(\PDOException $ex){
            $this->loggerDB->error('Error getting holiday data ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $ex->getMessage()]);
            return null;
        }

        $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if(!$aRet){
            return null;
        }
        
        $city = new cityModel();
        $city->setIdcity($aRet['idcity'])
             ->setIdstate($aRet['idstate'])
             ->setStatename($aRet['state_name'])
             ->setName($aRet['name'])
             ->setDtfoundation($aRet['dtfoundation'])
             ->setIsdefault($aRet['default'])
             ->setStatus($aRet['status']);
        
        return $city;
    }

    /**
     * Update the city into the database
     *
     * @param  mixed $cityID
     * @param  mixed $uf
     * @param  mixed $name
     * @param  mixed $dtFoundation
     * @param  mixed $flgDefault
     * @return cityModel
     */
    public function updateCity(int $cityID, int $uf, string $name, string $dtFoundation, int $flgDefault): ?cityModel
    {        
        $sql = "UPDATE exp_tbcity
                   SET idstate = :uf,
                        `name` = :name,
                        dtfoundation = :dtFoundation,
                        `default` = :flgDefault
                 WHERE idcity = :cityID";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':uf', $uf);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':dtFoundation', $dtFoundation);
            $stmt->bindParam(':flgDefault', $flgDefault);
            $stmt->bindParam(':cityID', $cityID);
            $stmt->execute();
        }catch(\PDOException $ex){
            $this->loggerDB->error('Error trying update city ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $ex->getMessage()]);
            return null;
        }

        $city = new cityModel(); 
        $city->setIdcity($cityID); 
        
        return $city;
    }
    
        
    /**
     * Returns array with the uploaded image linked with the city
     *
     * @param  mixed $cityID
     * @return array
     */
    public function fetchCityImage(int $cityID): array
    {        
        $sql = "SELECT idimage,idcity,filename,fileuploaded
                  FROM `exp_tbcity_image`
                 WHERE idcity = :cityID";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':cityID', $cityID);
            $stmt->execute();
        }catch(\PDOException $ex){
            $this->loggerDB->error("Error getting uploded image ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $ex->getMessage()]);
            return null;
        }
        
        $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        if(!$aRet){
            return array();
        }
        
        return $aRet;
    }
    
    /**
     * Update city's status
     *
     * @param  int $cityID
     * @param  string $newStatus
     * @return cityModel
     */
    public function updateStatus(int $cityID, string $newStatus): ?cityModel
    {        
        $sql = "UPDATE exp_tbcity
                   SET `status` = :newStatus
                 WHERE idcity = :cityID";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':newStatus', $newStatus);
            $stmt->bindParam(':cityID', $cityID);
            $stmt->execute();
        }catch(\PDOException $ex){
            $this->loggerDB->error("Error trying update city's status", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $ex->getMessage()]);
            return null;
        }

        $city = new cityModel(); 
        $city->setIdcity($cityID); 
        
        return $city;
    }

        
    /**
     * Delete city from DB
     *
     * @param  int $cityID
     * @return cityModel
     */
    public function deleteCity(int $cityID): ?cityModel
    {        
        $sql = "DELETE FROM exp_tbcity WHERE idcity = :cityID";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':cityID', $cityID);
            $stmt->execute();
        }catch(\PDOException $ex){
            $this->loggerDB->error('Error trying delete city ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $ex->getMessage()]);
            return null;
        }

        $city = new cityModel(); 
        $city->setIdcity($cityID); 
        
        return $city;
    }

    /**
     * Delete the uploaded image from the database
     *
     * @param  int $imageID
     * @return cityModel
     */
    public function deleteCityImage(int $imageID): ?cityModel
    {        
        $sql = "DELETE FROM exp_tbcity_image WHERE idimage = :imageID";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':imageID', $imageID);
            $stmt->execute();
        }catch(\PDOException $ex){
            $this->loggerDB->error('Error trying delete uploaded image ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $ex->getMessage()]);
            return null;
        }

        $city = new cityModel(); 
        $city->setIdimage($imageID); 
        
        return $city;
    }
    
    /**
     * Update uploaded image name
     *
     * @param  int $imageID
     * @param  string $newName
     * @return cityModel
     */
    public function updateCityImageName(int $imageID, string $newName): ?cityModel
    {        
        $sql = "UPDATE exp_tbcity_image
                   SET fileuploaded = :newName
                 WHERE idimage = :imageID";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':newName', $newName);
            $stmt->bindParam(':imageID', $imageID);
            $stmt->execute();
        }catch(\PDOException $ex){
            $this->loggerDB->error('Error trying update loaded image name ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $ex->getMessage()]);
            return null;
        }

        $city = new cityModel(); 
        $city->setIdimage($imageID); 
        
        return $city;
    }

}