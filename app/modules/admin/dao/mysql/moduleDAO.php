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

            $moduleModel->setIdModule($aRet['idmodule'])
                        ->setName($aRet['name'])
                        ->setIndex($aRet['index'])
                        ->setStatus($aRet['status'])
                        ->setPath($aRet['path'])
                        ->setLanguageKeyName($aRet['smarty'])
                        ->setClass($aRet['class'])
                        ->setHeaderLogo($aRet['headerlogo'])
                        ->setReportsLogo($aRet['reportslogo'])
                        ->setTablePrefix($aRet['tableprefix'])
                        ->setIsDefault($aRet['defaultmodule']);
            
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
        $prefix = $moduleModel->getTablePrefix() . '_tbconfig';
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
            $cond = " AND (tp.idtypeperson IN
                        (SELECT idtypeperson
                           FROM tbpersontypes
                          WHERE idperson = '{$moduleModel->getUserID()}')
                          OR tp.idtypeperson = p.idtypeperson)";
        }
        
        $sql = "SELECT category_id, category, cat_smarty, cat_printable FROM 
                ((SELECT DISTINCT cat.name AS category, cat.idprogramcategory AS category_id, cat.smarty AS cat_smarty, v.key_value cat_printable
                   FROM tbperson p, tbtypepersonpermission g, tbaccesstype a, tbprogram pr, tbmodule m,
                        tbprogramcategory cat, tbtypeperson tp, tbvocabulary v, tblocale l
                  WHERE g.idaccesstype = a.idaccesstype
                    AND g.idprogram = pr.idprogram
                    AND m.idmodule = cat.idmodule
                    AND cat.idprogramcategory = pr.idprogramcategory
                    AND tp.idtypeperson = g.idtypeperson
                    AND cat.smarty = v.key_name
                    AND v.idlocale = l.idlocale
                    AND LOWER(l.name) = LOWER('{$_ENV['DEFAULT_LANG']}')
                    AND m.status = 'A'
                    AND pr.status = 'A'
                    AND p.idperson = :userID
                    $cond
                    AND g.idaccesstype = '1'
                    AND g.allow = 'Y'
                    AND m.idmodule = :moduleID)
                  UNION
                (SELECT DISTINCT cat.name AS category, cat.idprogramcategory AS category_id, cat.smarty AS cat_smarty, v.key_value cat_printable
                   FROM tbperson per, tbpermission p, tbprogram pr, tbmodule m, tbprogramcategory cat, tbaccesstype acc, tbvocabulary v, tblocale l
                  WHERE m.idmodule = cat.idmodule
                    AND pr.idprogramcategory = cat.idprogramcategory
                    AND per.idperson = p.idperson
                    AND pr.idprogram = p.idprogram
                    AND cat.smarty = v.key_name
                    AND v.idlocale = l.idlocale
                    AND LOWER(l.name) = LOWER('{$_ENV['DEFAULT_LANG']}')
                    AND m.status = 'A'
                    AND pr.status = 'A'
                    AND p.idperson = :userID
                    AND p.idaccesstype = acc.idaccesstype
                    AND p.idaccesstype = '1'
                    AND p.allow = 'Y'
                    AND m.idmodule = :moduleID)) AS tmp
                    ORDER BY cat_printable";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':userID', $moduleModel->getUserID());
            $stmt->bindParam(':moduleID', $moduleModel->getIdModule());
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
            $cond = " AND (tp.idtypeperson IN
                            (SELECT idtypeperson
                               FROM tbpersontypes
                              WHERE idperson = '{$moduleModel->getUserID()}')
                           OR tp.idtypeperson = p.idtypeperson)";
        }

        $andModule = " m.idmodule = {$moduleModel->getIdModule()} AND cat.idprogramcategory = {$moduleModel->getCategoryID()}";
        
        $sql = "SELECT idmodule_pai, module, path, idmodule_origem, category, category_pai, cat_smarty, idcategory_origem, program, controller,
                        pr_smarty, idprogram, allow,pr_printable
                  FROM 
                ((SELECT m.idmodule as idmodule_pai, m.name as module, m.path as path, cat.idmodule as idmodule_origem,
                        cat.name as category, cat.idprogramcategory as category_pai, cat.smarty as cat_smarty,
                        pr.idprogramcategory as idcategory_origem, pr.name as program, pr.controller as controller,
                        pr.smarty   as pr_smarty, pr.idprogram as idprogram, g.allow, v.key_value pr_printable
                   FROM tbperson p, tbtypepersonpermission g, tbaccesstype a, tbprogram pr, tbmodule m,
                        tbprogramcategory cat, tbtypeperson tp, tbvocabulary v, tblocale l
                  WHERE g.idaccesstype = a.idaccesstype
                    AND g.idprogram = pr.idprogram
                    AND m.idmodule = cat.idmodule
                    AND cat.idprogramcategory = pr.idprogramcategory
                    AND tp.idtypeperson = g.idtypeperson
                    AND pr.smarty = v.key_name
                    AND v.idlocale = l.idlocale
                    AND LOWER(l.name) = LOWER('{$_ENV['DEFAULT_LANG']}')
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
                        pr.smarty as pr_smarty, pr.idprogram as idprogram, p.allow, v.key_value pgr_printable
                   FROM tbperson per, tbpermission p, tbprogram  pr, tbmodule  m, tbprogramcategory  cat, tbaccesstype  acc,
                        tbvocabulary v, tblocale l
                  WHERE m.idmodule = cat.idmodule
                    AND pr.idprogramcategory = cat.idprogramcategory
                    AND per.idperson = p.idperson
                    AND pr.idprogram = p.idprogram
                    AND pr.smarty = v.key_name
                    AND v.idlocale = l.idlocale
                    AND LOWER(l.name) = LOWER('{$_ENV['DEFAULT_LANG']}')
                    AND m.status = 'A'
                    AND pr.status = 'A'
                    AND p.idperson = :userID
                    AND p.idaccesstype = acc.idaccesstype
                    AND p.idaccesstype = '1'
                    AND $andModule)) AS tmp
                    ORDER BY pr_printable";
        
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
            $stmt->bindValue(':moduleName', $moduleModel->getName());
            $stmt->execute();
            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            $moduleModel->setIdModule($aRet['idmodule'])
                        ->setName($aRet['name'])
                        ->setIndex($aRet['index'])
                        ->setStatus($aRet['status'])
                        ->setPath($aRet['path'])
                        ->setLanguageKeyName($aRet['smarty'])
                        ->setClass($aRet['class'])
                        ->setHeaderLogo($aRet['headerlogo'])
                        ->setReportsLogo($aRet['reportslogo'])
                        ->setTablePrefix($aRet['tableprefix'])
                        ->setIsDefault($aRet['defaultmodule']);
            
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
                  FROM ((SELECT m.idmodule, m.name, m.index, m.path, m.smarty, m.class, m.headerlogo, m.reportslogo,  m.tableprefix, v.key_value module_printable
                           FROM tbperson per, tbpermission p, tbprogram pr, tbmodule m, tbprogramcategory cat, tbaccesstype acc,
                                tbvocabulary v, tblocale l
                          WHERE m.idmodule = cat.idmodule
                            AND pr.idprogramcategory = cat.idprogramcategory
                            AND per.idperson = p.idperson
                            AND pr.idprogram = p.idprogram
                            AND m.smarty = v.key_name
                            AND v.idlocale = l.idlocale
                            AND LOWER(l.name) = LOWER('{$_ENV['DEFAULT_LANG']}')
                            AND m.status = 'A'
                            AND pr.status = 'A'
                            AND p.idperson = :userID
                            AND p.allow = 'Y'
                            AND p.idaccesstype = acc.idaccesstype
                            AND p.idaccesstype = '1'
                            AND m.idmodule > 3
                       GROUP BY m.idmodule)
                          UNION
                        (SELECT d.idmodule, d.name, d.index, d.path, d.smarty, d.class, d.headerlogo, d.reportslogo, d.tableprefix, v.key_value module_printable
                           FROM tbtypepersonpermission a, tbprogram b, tbprogramcategory c, tbmodule d, tbvocabulary v, tblocale l
                          WHERE (a.idtypeperson IN (SELECT idtypeperson FROM tbpersontypes WHERE idperson = :userID)
                                 OR a.idtypeperson = (SELECT idtypeperson FROM tbperson WHERE idperson = :userID))
                            AND a.allow = 'Y'
                            AND d.status = 'A'
                            AND d.idmodule > 3
                            AND a.idprogram = b.idprogram
                            AND c.idprogramcategory = b.idprogramcategory
                            AND d.idmodule = c.idmodule
                            AND d.smarty = v.key_name
                            AND v.idlocale = l.idlocale
                            AND LOWER(l.name) = LOWER('{$_ENV['DEFAULT_LANG']}')
                       GROUP BY d.idmodule)) AS temp
                       ORDER BY module_printable";
        
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

    /**
     * Return an array with modules to display in grid
     *
     * @param  string $where
     * @param  string $group
     * @param  string $order
     * @param  string $limit
     * @return array
     */
    public function queryModules($where=null,$group=null,$order=null,$limit=null): array
    {
        
        $sql = "SELECT idmodule, name, status, defaultmodule, path, smarty
                  FROM tbmodule
                $where $group $order $limit";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $moduleModel = new moduleModel(); 
            $moduleModel->setGridList($aRet);

            $ret = true;
            $result = array("message"=>"","object"=>$moduleModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting modules ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * Return an array with rows total for grid pagination 
     *
     * @param  string $where
     * @param  string $group
     * @param  string $order
     * @param  string $limit
     * @return array
     */
    public function countModules($where=null): array
    {
        
        $sql = "SELECT COUNT(idmodule) total
                  FROM tbmodule 
                $where";
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            $moduleModel = new moduleModel(); 
            $moduleModel->setTotalRows($aRet['total']);

            $ret = true;
            $result = array("message"=>"","object"=>$moduleModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error counting modules ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Removes the default module flag
     * pt_br Remove o sinalizador de módulo padrão
     *
     * @param  moduleModel $moduleModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function removeDefault(moduleModel $moduleModel): array
    {        
        $sql = "UPDATE tbmodule SET defaultmodule = NULL";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        $ret = true;
        $result = array("message"=>"","object"=>$moduleModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * en_us Inserts module data into tbmodule
     * pt_br Insere os dados do módulo no tbmodule
     *
     * @param  moduleModel $moduleModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function insertModule(moduleModel $moduleModel): array
    {        
        $sql = "INSERT INTO tbmodule (name, path, smarty, headerlogo, tableprefix, defaultmodule) 
                     VALUES (:name, :path, :langKey, NULLIF(:logo,'NULL'), :prefix, NULLIF(:isDefault,'NULL'))";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":name",$moduleModel->getName());
        $stmt->bindValue(":path",$moduleModel->getPath());
        $stmt->bindValue(":langKey",$moduleModel->getLanguageKeyName());
        $stmt->bindValue(":logo",(empty($moduleModel->getHeaderLogo()) ? 'NULL': $moduleModel->getHeaderLogo()));
        $stmt->bindValue(":prefix",$moduleModel->getTablePrefix());
        $stmt->bindValue(":isDefault",(empty($moduleModel->getIsDefault()) ? 'NULL': $moduleModel->getIsDefault()));
        $stmt->execute();

        $moduleModel->setIdModule($this->db->lastInsertId());

        $ret = true;
        $result = array("message"=>"","object"=>$moduleModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Inserts module restrictions
     * pt_br Insere as restrições do módulo
     *
     * @param  moduleModel $moduleModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function insertModuleRestriction(moduleModel $moduleModel): array
    {        
        $sql = "INSERT INTO tbmodule_has_restriction (idmodule,ip,ip_aton) 
                     VALUES (:moduleId, :ip, :ipAtoN)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":moduleId",$moduleModel->getIdModule());
        $stmt->bindValue(":ip",$moduleModel->getRestriction());
        $stmt->bindValue(":ipAtoN",$moduleModel->getRestrictionAtoN());
        $stmt->execute();

        $ret = true;
        $result = array("message"=>"","object"=>$moduleModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Creates config tables
     * pt_br Cria tabelas de configuração
     *
     * @param  moduleModel $moduleModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function createConfigTables(moduleModel $moduleModel): array
    {        
        $sql = "CALL adm_createconfigtables(:prefix)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":prefix",$moduleModel->getTablePrefix());
        $stmt->execute();

        $ret = true;
        $result = array("message"=>"","object"=>$moduleModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * en_us Saves the new module into DB
     * pt_br Grava o novo módulo no banco de dados
     *
     * @param  moduleModel $moduleModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function saveModule(moduleModel $moduleModel): array
    {   
        $aRestriction = $moduleModel->getRestrictionList();
        
        try{
            $this->db->beginTransaction();

            if(!empty($moduleModel->getIsDefault())){
                $this->removeDefault($moduleModel);
            }

            $insModule = $this->insertModule($moduleModel);

            if($insModule['status']){
                // insert modules's restrictions
                if(count($aRestriction) > 0){
                    foreach($aRestriction as $k=>$v){
                        $retrictionAtoN = $this->appSrcDB->_formatIpToLong($v);

                        $insModule['push']['object']->setRestriction($v)
                                                    ->setRestrictionAtoN($retrictionAtoN);

                        $this->insertModuleRestriction($insModule['push']['object']);
                    }
                }

                // makes config tables
                $this->createConfigTables($insModule['push']['object']);
            }
            
            $ret = true;
            $result = array("message"=>"","object"=>$insModule['push']['object']);
            $this->db->commit();
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error trying save module info ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
            $this->db->rollBack();
        }         
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Returns module's data
     * pt_br Retorna os dados do módulo
     *
     * @param  moduleModel $moduleModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function getModule(moduleModel $moduleModel): array
    {        
        $sql = "SELECT name, path, smarty, headerlogo, tableprefix, defaultmodule FROM tbmodule WHERE idmodule = :moduleId";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":moduleId",$moduleModel->getIdModule());
            $stmt->execute();
            
            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);

            $moduleModel->setName($aRet['name'])
                        ->setPath($aRet['path'])
                        ->setLanguageKeyName($aRet['smarty'])
                        ->setHeaderLogo((!empty($aRet['headerlogo']) && !is_null($aRet['headerlogo'])) ? $aRet['headerlogo'] : "")
                        ->setTablePrefix((!empty($aRet['tableprefix']) && !is_null($aRet['tableprefix'])) ? $aRet['tableprefix'] : "")
                        ->setIsDefault((!empty($aRet['defaultmodule']) && !is_null($aRet['defaultmodule'])) ? $aRet['defaultmodule'] : "");
            
            $ret = true;
            $result = array("message"=>"","object"=>$moduleModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting module data ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Returns module's restrictions
     * pt_br Retorna as restrições do módulo
     *
     * @param  moduleModel $moduleModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchModuleRestrictions(moduleModel $moduleModel): array
    {        
        $sql = "SELECT idrestriction, idmodule, ip, ip_aton FROM tbmodule_has_restriction WHERE idmodule = :moduleId";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":moduleId",$moduleModel->getIdModule());
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $moduleModel->setRestrictionList((!empty($aRet) && count($aRet) > 0) ? $aRet : array());

            $ret = true;
            $result = array("message"=>"","object"=>$moduleModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting module's restrictions ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Setup helpdezk's module as default
     * pt_br Remove as restrições do módulo da tabela tbmodule_has_restriction
     *
     * @param  moduleModel $moduleModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function setHdkAsDefault(moduleModel $moduleModel): array
    {        
        $sql = "UPDATE tbmodule SET defaultmodule = 'YES' WHERE idmodule = 2";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        $ret = true;
        $result = array("message"=>"","object"=>$moduleModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Updates module's data
     * pt_br Atualiza os dados do módulo
     *
     * @param  moduleModel $moduleModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function updateModule(moduleModel $moduleModel): array
    {        
        $sql = "UPDATE tbmodule 
                   SET name = :name,
                       smarty = :langKey";
        
        $sql .= (empty($moduleModel->getHeaderLogo()))  ? "" : ", headerlogo = '{$moduleModel->getHeaderLogo()}'";
        $sql .= (empty($moduleModel->getIsDefault()))  ? "" : ", defaultmodule = 'YES'";

        $sql .= " WHERE idmodule = :moduleId";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":name",$moduleModel->getName());
        $stmt->bindValue(":langKey",$moduleModel->getLanguageKeyName());
        $stmt->bindValue(":moduleId",$moduleModel->getIdModule());
        $stmt->execute();

        $ret = true;
        $result = array("message"=>"","object"=>$moduleModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Removes module's restrictions from table tbmodule_has_restriction
     * pt_br Remove as restrições do módulo da tabela tbmodule_has_restriction
     *
     * @param  moduleModel $moduleModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function deleteModuleRestriction(moduleModel $moduleModel): array
    {        
        $sql = "DELETE FROM tbmodule_has_restriction WHERE idmodule = :moduleId";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":moduleId",$moduleModel->getIdModule());
        $stmt->execute();

        $ret = true;
        $result = array("message"=>"","object"=>$moduleModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Saves module's update data into DB
     * pt_br Grava os dados de atualização do módulo no banco de dados
     *
     * @param  moduleModel $moduleModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function saveUpdateModule(moduleModel $moduleModel): array
    {   
        $aRestriction = $moduleModel->getRestrictionList();
        
        try{
            $this->db->beginTransaction();

            if(!empty($moduleModel->getIsDefault())){
                $this->removeDefault($moduleModel);
            }else{
                $this->removeDefault($moduleModel);
                $this->setHdkAsDefault($moduleModel);
            }
            
            $updModule = $this->updateModule($moduleModel);
            
            if($updModule['status']){
                if(count($aRestriction) > 0){
                    // remove modules's restrictions
                    $delRestriction = $this->deleteModuleRestriction($updModule['push']['object']);

                    // insert modules's restrictions
                    foreach($aRestriction as $k=>$v){
                        $retrictionAtoN = $this->appSrcDB->_formatIpToLong($v);

                        $updModule['push']['object']->setRestriction($v)
                                                    ->setRestrictionAtoN($retrictionAtoN);

                        $this->insertModuleRestriction($updModule['push']['object']);
                    }
                }else{
                    // remove modules's restrictions
                    $delRestriction = $this->deleteModuleRestriction($updModule['push']['object']);
                }
            }
            
            $ret = true;
            $result = array("message"=>"","object"=>$updModule['push']['object']);
            $this->db->commit();
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error trying update module data ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
            //$this->db->rollBack();
        }         
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Setup the first active module as default
     * pt_br Configura o primeiro módulo ativo como padrão
     *
     * @param  moduleModel $moduleModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function setModuleDefault(moduleModel $moduleModel): array
    {        
        $sql = "UPDATE tbmodule 
                   SET defaultmodule = 'YES' 
                 WHERE idmodule = :newDefaultId";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":newDefaultId",$moduleModel->getNewDefaultId());
        $stmt->execute();

        $ret = true;
        $result = array("message"=>"","object"=>$moduleModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Updates module's data
     * pt_br Atualiza os dados do módulo
     *
     * @param  moduleModel $moduleModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function updateModuleStatus(moduleModel $moduleModel): array
    {        
        $sql = "UPDATE tbmodule SET `status` = :newStatus WHERE idmodule = :moduleId";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":newStatus",$moduleModel->getStatus());
        $stmt->bindValue(":moduleId",$moduleModel->getIdModule());
        $stmt->execute();

        $ret = true;
        $result = array("message"=>"","object"=>$moduleModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Saves module's update data into DB
     * pt_br Grava os dados de atualização do módulo no banco de dados
     *
     * @param  moduleModel $moduleModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function changeModuleStatus(moduleModel $moduleModel): array
    {   
        try{
            $this->db->beginTransaction();

            if($moduleModel->getIsDefault() == "YES" && $moduleModel->getIdModule() == 2){ // if trying to inactivate helpdezk's module set an active module as the default
                $this->removeDefault($moduleModel);
                $retNewDefault = $this->queryModules("WHERE `status` = 'A' AND idmodule NOT IN (1,2)",null,"ORDER BY `name` ASC","LIMIT 1");
                if($retNewDefault['status']){
                    $aNewDefault = $retNewDefault['push']['object']->getGridList();
                    $newDefaultId = $aNewDefault[0]['idmodule'];
                    $moduleModel->setNewDefaultId($newDefaultId);
                    $this->setModuleDefault($moduleModel);
                }
            }elseif($moduleModel->getIsDefault() == "YES" && $moduleModel->getIdModule() != 2){ // if trying to inactivate other module set helpdezk as the default
                $this->removeDefault($moduleModel);
                $this->setHdkAsDefault($moduleModel);
            }
            
            $updModule = $this->updateModuleStatus($moduleModel);
            
            $ret = true;
            $result = array("message"=>"","object"=>$updModule['push']['object']);
            $this->db->commit();
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error trying change module data ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
            //$this->db->rollBack();
        }         
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Removes module's config tables
     * pt_br Remove as tabelas de configuração do módulo
     *
     * @param  moduleModel $moduleModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function deleteConfigTables(moduleModel $moduleModel): array
    {        
        $sql = "CALL adm_deleteTables(:dbName,:prefix,@msg)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":dbName",$_ENV['DB_NAME']);
        $stmt->bindValue(":prefix",$moduleModel->getTablePrefix());
        $stmt->execute();

        $ret = true;
        $result = array("message"=>"","object"=>$moduleModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Removes module's data from tbmodule table
     * pt_br Remove os dados do módulo da tabela tbmodule
     *
     * @param  moduleModel $moduleModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function deleteModule(moduleModel $moduleModel): array
    {        
        $sql = "DELETE FROM tbmodule WHERE idmodule = :moduleId";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":moduleId",$moduleModel->getIdModule());
        $stmt->execute();

        $ret = true;
        $result = array("message"=>"","object"=>$moduleModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Removes module's data from DB
     * pt_br Remove os dados do módulo do banco de dados
     *
     * @param  moduleModel $moduleModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function saveDeleteModule(moduleModel $moduleModel): array
    {   
        try{
            $this->db->beginTransaction();

            // remove module's config tables
            $remConfig = $this->deleteConfigTables($moduleModel);
            if($remConfig['status']){
                // remove module's restrictions
                $remRestrictions = $this->deleteModuleRestriction($remConfig['push']['object']);

                // remove module's data
                $rem = $this->deleteModule($remConfig['push']['object']);
            }
            
            $ret = true;
            $result = array("message"=>"","object"=>$remConfig['push']['object']);
            $this->db->commit();
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error trying remove module data ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
            //$this->db->rollBack();
        }         
        
        return array("status"=>$ret,"push"=>$result);
    }
}