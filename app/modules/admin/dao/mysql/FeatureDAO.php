<?php

namespace App\modules\admin\dao\mysql;
use App\core\Database;

class FeatureDAO extends Database
{
    public function __construct()
    {
        parent::__construct(); 
    }

    public function getPopConfigs(): array
    {
        
        $sql = "SELECT session_name, `value` FROM tbconfig WHERE idconfigcategory = 12";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
        }catch(\PDOException $ex){
            return array("success"=>false,"message"=>$ex->getMessage()." {$sql}");
        }
        
        $aRet = array();
        while($row = $stmt->fetch(\PDO::FETCH_ASSOC)){
            $ses = $row['session_name'];
            $val = $row['value'];
            $confs[$ses] = $val;            
        }

        return array("success"=>true,"message"=>"","data"=>$confs);
    }

    public function getArrayConfigs(int $confCategoryID): array
    {
        
        $sql = "SELECT session_name, `value` FROM tbconfig WHERE idconfigcategory = :confCategoryID";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':confCategoryID', $confCategoryID);
            $stmt->execute();
        }catch(\PDOException $ex){
            return array("success"=>false,"message"=>$ex->getMessage()." {$sql}");
        }
        
        $aRet = array();
        while($row = $stmt->fetch(\PDO::FETCH_ASSOC)){
            $ses = $row['session_name'];
            $val = $row['value'];
            $confs[$ses] = $val;            
        }

        return array("success"=>true,"message"=>"","data"=>$confs);
    }

    public function getConfigValue(string $confName): array
    {        
        $sql = "SELECT `value` FROM tbconfig WHERE session_name = :confName";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':confName', $confName);
            $stmt->execute();
        }catch(\PDOException $ex){
            return array("success"=>false,"message"=>$ex->getMessage()." {$sql}");
        }
        
        $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);

        return array("success"=>true,"message"=>"","data"=>$aRet);
    }
}