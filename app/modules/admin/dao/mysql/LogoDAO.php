<?php

namespace App\modules\admin\dao\mysql;
use App\core\Database;

class LogoDAO extends Database
{
    public function __construct()
    {
        parent::__construct(); 
    }

    public function getLogoByName(string $name): array
    {
        
        $sql = "SELECT idlogo, `name`, height, width, file_name FROM tblogos WHERE `name` = :name";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":name",$name);
            $stmt->execute();
        }catch(\PDOException $ex){
            return array("success"=>false,"message"=>$ex->getMessage()." {$sql}");
        }
        
        $aRet = $stmt->fetch(\PDO::FETCH_ASSOC); 

        return array("success"=>true,"message"=>"","data"=>$aRet);
    }
}