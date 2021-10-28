<?php

namespace App\modules\admin\dao\mysql;

use App\core\Database;
use App\modules\admin\models\mysql\moduleModel;

class moduleDAO extends Database
{
    public function __construct()
    {
        parent::__construct(); 
    }

    public function getModuleDefault(): ?moduleModel
    {        
        $sql = "SELECT idmodule, `name`, IFNULL(`index`,0) `index`, `status`, path, smarty, 
                        IFNULL(class,'') class,IFNULL(headerlogo,'') headerlogo, IFNULL(reportslogo,'') reportslogo, 
                        IFNULL(tableprefix,'') tableprefix,IFNULL(defaultmodule,'NO') defaultmodule
                  FROM tbmodule
                 WHERE defaultmodule='YES'";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
        }catch(\PDOException $ex){
            $this->loggerDB->error('Error getting default module ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $ex->getMessage()]);
            return null;
        }
        
        $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);

        $module = new moduleModel(); 
        $module->setIdmodule($aRet['idmodule'])
               ->setName($aRet['name'])
               ->setIndex($aRet['index'])
               ->setStatus($aRet['status'])
               ->setPath($aRet['path'])
               ->setSmarty($aRet['smarty'])
               ->setClass($aRet['class'])
               ->setHeaderlogo($aRet['headerlogo'])
               ->setReportslogo($aRet['reportslogo'])
               ->setTableprefix($aRet['tableprefix'])
               ->setIsdefault($aRet['defaultmodule']); 
        
        return $module;
    }

    public function fetchActiveModules(): array
    {        
        $sql = "SELECT idmodule,`name`,`index`,path,smarty,headerlogo,reportslogo,tableprefix 
                  FROM tbmodule
                 WHERE `status` = 'A'";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
        }catch(\PDOException $ex){
            $this->loggerDB->error('Error getting active modules ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $ex->getMessage()]);
            return null;
        }
        
        $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        return $aRet;
    }

    public function fetchConfigDataByModule(string $prefix): array
    {        
        $prefix = $prefix . '_tbconfig';
        $sql = "SELECT session_name, value FROM $prefix";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
        }catch(\PDOException $ex){
            $this->loggerDB->error('Error getting module settings ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $ex->getMessage()]);
            return null;
        }
        
        $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        return $aRet;
    }
}