<?php

namespace App\modules\admin\dao\mysql;
use App\core\Database;

class featureDAO extends Database
{
    public function __construct()
    {
        parent::__construct(); 
    }

    public function fetchPopConfigs(): array
    {
        
        $sql = "SELECT session_name, `value` FROM tbconfig WHERE idconfigcategory = 12";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
        }catch(\PDOException $ex){
            $this->loggerDB->error('Error getting all hdk groups ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $ex->getMessage()]);
            return null;
        }
        
        $row = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        foreach($row as $k=>$v){
            echo "{$k}\n";
            //$groups = "{$v['idgroup']},";           
        }

        //return $rows;
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

    public function fetchUserSettings(int $userID): array
    {        
        $sql = "SELECT idconfiguser,idperson,lang,theme,grid_operator,grid_operator_width,
                        grid_user,grid_user_width
                  FROM hdk_tbconfig_user
                 WHERE idperson = :userID";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':userID', $userID);
            $stmt->execute();
        }catch(\PDOException $ex){
            $this->loggerDB->error('Error getting all hdk groups ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $ex->getMessage()]);
            return null;
        }
        
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $rows;
    }
}