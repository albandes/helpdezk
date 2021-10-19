<?php

namespace App\modules\admin\dao\mysql;

use App\core\Database;
use App\modules\admin\models\mysql\logoModel;

class logoDAO extends Database
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
        $logo = new logoModel();
        $logo->setId($aRet['idlogo'])
             ->setName($aRet['name'])
             ->setHeight($aRet['height'])
             ->setWidth($aRet['width'])
             ->setFileName($aRet['file_name']);             

        return array("success"=>true,"message"=>"","data"=>$logo);
    }
}