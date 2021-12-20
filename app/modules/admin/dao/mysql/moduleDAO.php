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
    
    /**
     * Returns default module
     *
     * @param  moduleModel $moduleModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function getModuleDefault(moduleModel $moduleModel): array
    {        
        $sql = "SELECT idmodule, `name`, IFNULL(`index`,0) `index`, `status`, path, smarty, 
                        IFNULL(class,'') class,IFNULL(headerlogo,'') headerlogo, IFNULL(reportslogo,'') reportslogo, 
                        IFNULL(tableprefix,'') tableprefix,IFNULL(defaultmodule,'NO') defaultmodule
                  FROM tbmodule
                 WHERE defaultmodule='YES'";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);

            $moduleModel->setIdmodule($aRet['idmodule'])
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
            
            $ret = true;
            $result = array("message"=>"","object"=>$moduleModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error getting default module ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * Returns active modules data
     *
     * @param  moduleModel $moduleModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchActiveModules(moduleModel $moduleModel): array
    {        
        $sql = "SELECT idmodule,`name`,`index`,path,smarty,headerlogo,reportslogo,tableprefix 
                  FROM tbmodule
                 WHERE `status` = 'A'";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $moduleModel->setActiveList($aRet);
            
            $ret = true;
            $result = array("message"=>"","object"=>$moduleModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error getting active modules ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * Returns module's settings
     *
     * @param  moduleModel $moduleModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchConfigDataByModule(moduleModel $moduleModel): array
    {        
        $prefix = $moduleModel->getTableprefix() . '_tbconfig';
        $sql = "SELECT session_name, value FROM $prefix";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $moduleModel->setSettingsList($aRet);
            
            $ret = true;
            $result = array("message"=>"","object"=>$moduleModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error getting module settings ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * Returns module's categories data
     *
     * @param  moduleModel $moduleModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchModuleActiveCategories(moduleModel $moduleModel): array
    {        
        if($moduleModel->getUserID() == 1 || $moduleModel->getUserType() == 1){
            $cond = " AND tp.idtypeperson = 1";
        }else{
            $cond = " AND tp.idtypeperson IN
                        (SELECT idtypeperson
                           FROM tbpersontypes
                          WHERE idperson = '{$moduleModel->getUserID()}')";
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
            $stmt->bindParam(':userID', $moduleModel->getUserID());
            $stmt->bindParam(':moduleID', $moduleModel->getIdmodule());
            $stmt->execute();
            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            $moduleModel->setCategoriesList($aRet);

            $ret = true;
            $result = array("message"=>"","object"=>$moduleModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting module's active categories", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * Returns program's permissions by module
     *
     * @param  moduleModel $moduleModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchPermissionMenu(moduleModel $moduleModel): array
    {        
        if($moduleModel->getUserID() == 1 || $moduleModel->getUserType() == 1){
            $cond = " AND tp.idtypeperson = 1";
        }else{
            $cond = " AND tp.idtypeperson IN
                        (SELECT idtypeperson
                           FROM tbpersontypes
                          WHERE idperson = '{$moduleModel->getUserID()}')";
        }

        $andModule = " m.idmodule = {$moduleModel->getIdmodule()} AND cat.idprogramcategory = {$moduleModel->getCategoryID()}";
        
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
            $stmt->bindParam(':userID', $moduleModel->getUserID());
            $stmt->execute();
            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            $moduleModel->setPermissionsList($aRet);

            $ret = true;
            $result = array("message"=>"","object"=>$moduleModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting module's active categories", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * Returns module's data
     *
     * @param  moduleModel $moduleModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function getModuleInfoByName(moduleModel $moduleModel): array
    {        
        $sql = "SELECT idmodule, `name`, IFNULL(`index`,0) `index`, `status`, path, smarty, 
                        IFNULL(class,'') class,IFNULL(headerlogo,'') headerlogo, IFNULL(reportslogo,'') reportslogo, 
                        IFNULL(tableprefix,'') tableprefix,IFNULL(defaultmodule,'NO') defaultmodule
                  FROM tbmodule
                 WHERE `name` = :moduleName";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':moduleName', $moduleModel->getName());
            $stmt->execute();
            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            $moduleModel->setIdmodule($aRet['idmodule'])
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
            
            $ret = true;
            $result = array("message"=>"","object"=>$moduleModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error getting module info ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * Returns user's extra modules
     *
     * @param  moduleModel $moduleModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchExtraModulesPerson(moduleModel $moduleModel): array
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
            $stmt->bindParam(':userID', $moduleModel->getUserID());
            $stmt->execute();
            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            $moduleModel->setActiveList($aRet);

            $ret = true;
            $result = array("message"=>"","object"=>$moduleModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting extra modules ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
}