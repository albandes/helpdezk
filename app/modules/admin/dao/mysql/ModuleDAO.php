<?php

namespace App\modules\admin\dao\mysql;
use App\core\Database;

class ModuleDAO extends Database
{
    public function __construct()
    {
        parent::__construct(); 
    }

    public function getModuleDefault(): array
    {        
        $sql = "SELECT idmodule, `name`, `index`, `status`, path, smarty, class, headerlogo, 
                        reportslogo, tableprefix, defaultmodule
                  FROM tbmodule
                 WHERE defaultmodule='YES'";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
        }catch(\PDOException $ex){
            return array("success"=>false,"message"=>$ex->getMessage()." {$sql}");
        }
        
        $aRet =$stmt->fetch(\PDO::FETCH_ASSOC);

        return array("success"=>true,"message"=>"","data"=>$aRet);
    }
}