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

    public function fetchModulesCategoryAtive(int $userID, int $userType, int $moduleID): array
    {        
        if($userID == 1 || $userType == 1){
            $cond = " AND tp.idtypeperson = 1";
        }else{
            $cond = " AND tp.idtypeperson IN
                        (SELECT idtypeperson
                           FROM tbpersontypes
                          WHERE idperson = '{$userID}')";
        }
        
        $sql = "(SELECT DISTINCT cat.name AS category, cat.idprogramcategory AS category_id, cat.smarty AS cat_smarty
                   FROM tbperson p, tbtypepersonpermission g, tbaccesstype a, tbprogram pr, tbmodule m,
                        tbprogramcategory cat, tbtypeperson tp
                  WHERE g.idaccesstype = a.idaccesstype
                    AND g.idprogram = pr.idprogram
                    AND m.idmodule = cat.idmodule
                    AND cat.idprogramcategory = pr.idprogramcategory
                    AND tp.idtypeperson = g.idtypeperson
                    AND m.status = 'A'
                    AND pr.status = 'A'
                    AND p.idperson = :userID
                    $cond
                    AND g.idaccesstype = '1'
                    AND g.allow = 'Y'
                    AND m.idmodule = :moduleID)
                  UNION
                (SELECT DISTINCT cat.name AS category, cat.idprogramcategory AS category_id, cat.smarty AS cat_smarty
                   FROM tbperson per, tbpermission p, tbprogram pr, tbmodule m, tbprogramcategory cat, tbaccesstype acc
                  WHERE m.idmodule = cat.idmodule
                    AND pr.idprogramcategory = cat.idprogramcategory
                    AND per.idperson = p.idperson
                    AND pr.idprogram = p.idprogram
                    AND m.status = 'A'
                    AND pr.status = 'A'
                    AND p.idperson = :userID
                    AND p.idaccesstype = acc.idaccesstype
                    AND p.idaccesstype = '1'
                    AND p.allow = 'Y'
                    AND m.idmodule = :moduleID)";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':userID', $userID);
            $stmt->bindParam(':moduleID', $moduleID);
            $stmt->execute();
        }catch(\PDOException $ex){
            $this->loggerDB->error("Error getting module's active categories", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $ex->getMessage()]);
            return null;
        }
        
        $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        if(!$aRet){
            return array();
        }
        
        return $aRet;
    }

    public function fetchPermissionMenu(int $userID, int $userType, int $moduleID, int $categoryID): array
    {        
        if($userID == 1 || $userType == 1){
            $cond = " AND tp.idtypeperson = 1";
        }else{
            $cond = " AND tp.idtypeperson IN
                        (SELECT idtypeperson
                           FROM tbpersontypes
                          WHERE idperson = '{$userID}')";
        }

        $andModule = " m.idmodule = {$moduleID} AND cat.idprogramcategory = {$categoryID}";
        
        $sql = "(SELECT m.idmodule as idmodule_pai, m.name as module, m.path as path, cat.idmodule as idmodule_origem,
                        cat.name as category, cat.idprogramcategory as category_pai, cat.smarty as cat_smarty,
                        pr.idprogramcategory as idcategory_origem, pr.name as program, pr.controller as controller,
                        pr.smarty   as pr_smarty, pr.idprogram as idprogram, g.allow
                   FROM tbperson p, tbtypepersonpermission g, tbaccesstype a, tbprogram pr, tbmodule m,
                        tbprogramcategory cat, tbtypeperson tp
                  WHERE g.idaccesstype = a.idaccesstype
                    AND g.idprogram = pr.idprogram
                    AND m.idmodule = cat.idmodule
                    AND cat.idprogramcategory = pr.idprogramcategory
                    AND tp.idtypeperson = g.idtypeperson
                    AND m.status = 'A'
                    AND pr.status = 'A'
                    AND p.idperson = :userID
                    $cond
                    AND g.idaccesstype = '1'
                    AND g.allow = 'Y'
                    AND $andModule)
                  UNION
                (SELECT m.idmodule as idmodule_pai, m.name as module, m.path as path, cat.idmodule as idmodule_origem,
                        cat.name as category, cat.idprogramcategory as category_pai, cat.smarty as cat_smarty,
                        pr.idprogramcategory as idcategory_origem, pr.name as program, pr.controller as controller,
                        pr.smarty as pr_smarty, pr.idprogram as idprogram, p.allow
                   FROM tbperson per, tbpermission p, tbprogram  pr, tbmodule  m, tbprogramcategory  cat, tbaccesstype  acc
                  WHERE m.idmodule = cat.idmodule
                    AND pr.idprogramcategory = cat.idprogramcategory
                    AND per.idperson = p.idperson
                    AND pr.idprogram = p.idprogram
                    AND m.status = 'A'
                    AND pr.status = 'A'
                    AND p.idperson = :userID
                    AND p.idaccesstype = acc.idaccesstype
                    AND p.idaccesstype = '1'
                    AND $andModule
            )";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':userID', $userID);
            $stmt->execute();
        }catch(\PDOException $ex){
            $this->loggerDB->error("Error getting module's active categories", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $ex->getMessage()]);
            return null;
        }
        
        $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        if(!$aRet){
            return null;
        }
        
        return $aRet;
    }

    public function getModuleInfoByName(string $moduleName): ?moduleModel
    {        
        $sql = "SELECT idmodule, `name`, IFNULL(`index`,0) `index`, `status`, path, smarty, 
                        IFNULL(class,'') class,IFNULL(headerlogo,'') headerlogo, IFNULL(reportslogo,'') reportslogo, 
                        IFNULL(tableprefix,'') tableprefix,IFNULL(defaultmodule,'NO') defaultmodule
                  FROM tbmodule
                 WHERE `name` = :moduleName";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':moduleName', $moduleName);
            $stmt->execute();
        }catch(\PDOException $ex){
            $this->loggerDB->error('Error getting module info ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $ex->getMessage()]);
            return null;
        }
        
        $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);

        if(!$aRet){
            return null;
        }

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

    public function fetchExtraModulesPerson(int $userID): array
    {        
        $sql = "SELECT DISTINCT temp.idmodule, temp.name, temp.index, temp.path, temp.smarty, temp.class, temp.headerlogo,
                        temp.reportslogo, temp.tableprefix
                  FROM ((SELECT m.idmodule, m.name, m.index, m.path, m.smarty, m.class, m.headerlogo, m.reportslogo,  m.tableprefix
                           FROM tbperson per, tbpermission p, tbprogram pr, tbmodule m, tbprogramcategory cat, tbaccesstype acc
                          WHERE m.idmodule = cat.idmodule
                            AND pr.idprogramcategory = cat.idprogramcategory
                            AND per.idperson = p.idperson
                            AND pr.idprogram = p.idprogram
                            AND m.status = 'A'
                            AND pr.status = 'A'
                            AND p.idperson = :userID
                            AND p.allow = 'Y'
                            AND p.idaccesstype = acc.idaccesstype
                            AND p.idaccesstype = '1'
                            AND m.idmodule > 3
                       GROUP BY m.idmodule)
                          UNION
                        (SELECT d.idmodule, d.name, d.index, d.path, d.smarty, d.class, d.headerlogo, d.reportslogo, d.tableprefix
                           FROM tbtypepersonpermission a, tbprogram b, tbprogramcategory c, tbmodule d
                          WHERE a.idtypeperson IN (SELECT idtypeperson FROM tbpersontypes WHERE idperson = :userID)
                            AND a.allow = 'Y'
                            AND d.status = 'A'
                            AND d.idmodule > 3
                            AND a.idprogram = b.idprogram
                            AND c.idprogramcategory = b.idprogramcategory
                            AND d.idmodule = c.idmodule
                       GROUP BY d.idmodule)) AS temp";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':userID', $userID);
            $stmt->execute();
        }catch(\PDOException $ex){
            $this->loggerDB->error("Error getting extra modules ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $ex->getMessage()]);
            return null;
        }
        
        $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        if(!$aRet){
            return array();
        }
        
        return $aRet;
    }
}