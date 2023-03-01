<?php

namespace App\modules\admin\dao\mysql;

use App\core\Database;

use App\modules\admin\models\mysql\programModel;

class programDAO extends Database
{
    public function __construct()
    {
        parent::__construct(); 
    }

    /**
     * Return an array with Program to display in grid
     *
     * @param  string $where
     * @param  string $group
     * @param  string $order
     * @param  string $limit
     * @return array  Parameters returned in array: 
     *                [status = true/false
     *                 push =  [message = PDO Exception message 
     *                          object = model's object]]
     */
    public function queryPrograms($where=null,$group=null,$order=null,$limit=null): array
    {
        
        $sql = "SELECT tbp.idprogram, tbp.name, pvoc.key_value name_fmt,
                       tbm.name module, mvoc.key_value module_fmt, 
                       tbtp.name category, pcvoc.key_value category_fmt,  
                       tbp.controller, tbp.status
                  FROM tbprogram tbp
                  JOIN tbprogramcategory tbtp 
                    ON tbtp.idprogramcategory = tbp.idprogramcategory
                  JOIN tbmodule tbm
                    ON tbtp.idmodule = tbm.idmodule
                  JOIN tbvocabulary pvoc
                    ON pvoc.key_name = tbp.smarty
                  JOIN tblocale ploc
                    ON (ploc.idlocale = pvoc.idlocale AND
                        LOWER(ploc.name) = LOWER('{$_ENV['DEFAULT_LANG']}'))
                  JOIN tbvocabulary mvoc
                    ON mvoc.key_name = tbm.smarty
                  JOIN tblocale mloc
                    ON (mloc.idlocale = mvoc.idlocale AND
                        LOWER(mloc.name) = LOWER('{$_ENV['DEFAULT_LANG']}'))
                  JOIN tbvocabulary pcvoc
                    ON pcvoc.key_name = tbtp.smarty
                  JOIN tblocale pcloc
                    ON (pcloc.idlocale = pcvoc.idlocale AND
                        LOWER(pcloc.name) = LOWER('{$_ENV['DEFAULT_LANG']}')) 
                $where $group $order $limit";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $programModel = new programModel();
            $programModel->setGridList($aRet);

            $ret = true;
            $result = array("message"=>"","object"=>$programModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting programs ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * Return an array with total of Programs to display in grid
     *
     * @param  string $where
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function countPrograms($where=null): array
    {        
        $sql = "SELECT COUNT(tbp.idprogram) total
                  FROM tbprogram tbp
                  JOIN tbprogramcategory tbtp 
                    ON tbtp.idprogramcategory = tbp.idprogramcategory
                  JOIN tbmodule tbm
                    ON tbtp.idmodule = tbm.idmodule
                  JOIN tbvocabulary pvoc
                    ON pvoc.key_name = tbp.smarty
                  JOIN tblocale ploc
                    ON (ploc.idlocale = pvoc.idlocale AND
                        LOWER(ploc.name) = LOWER('{$_ENV['DEFAULT_LANG']}'))
                  JOIN tbvocabulary mvoc
                    ON mvoc.key_name = tbm.smarty
                  JOIN tblocale mloc
                    ON (mloc.idlocale = mvoc.idlocale AND
                        LOWER(mloc.name) = LOWER('{$_ENV['DEFAULT_LANG']}'))
                  JOIN tbvocabulary pcvoc
                    ON pcvoc.key_name = tbtp.smarty
                  JOIN tblocale pcloc
                    ON (pcloc.idlocale = pcvoc.idlocale AND
                        LOWER(pcloc.name) = LOWER('{$_ENV['DEFAULT_LANG']}')) 
                $where";

        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            $programModel = new programModel();
            $programModel->setTotalRows($aRet['total']);

            $ret = true;
            $result = array("message"=>"","object"=>$programModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error counting programs ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Returns a list with registered cities
     * pt_br Retorna uma lista com cidades cadastradas
     *
     * @param  programModel $programModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchCategoriesByModuleId(programModel $programModel): array
    {        
        $sql = "SELECT idprogramcategory, a.name, b.key_value name_fmt 
                  FROM tbprogramcategory a, tbvocabulary b, tblocale c 
                 WHERE b.key_name = a.smarty
                   AND (c.idlocale = b.idlocale AND
                        LOWER(c.name) = LOWER('{$_ENV['DEFAULT_LANG']}'))
                   AND a.idmodule = :moduleID 
              ORDER BY name_fmt";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":moduleID",$programModel->getModuleId());
            $stmt->execute();
            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $programModel->setGridList((!empty($aRet) && count($aRet) > 0) ? $aRet : array());

            $ret = true;
            $result = array("message"=>"","object"=>$programModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting program's categories ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Returns a list with person types for permissions
     * pt_br Retorna uma lista com os tipos de pessoa para as permissões
     *
     * @param  programModel $programModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchPersonTypes(programModel $programModel): array
    {        
        $sql = "SELECT idtypeperson, `name`
				  FROM tbtypeperson 
				 WHERE (idtypeperson <= 5 OR permissiongroup = 'Y')
              	ORDER BY idtypeperson";
        
        $stmt = $this->db->prepare($sql);
		$stmt->execute();

		$aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);

		$programModel->setGridList((!empty($aRet) && count($aRet) > 0) ? $aRet : array());

		$ret = true;
		$result = array("message"=>"","object"=>$programModel);
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Inserts program data into tbprogram
     * pt_br Insere os dados do programa no tbprogram
     *
     * @param  programModel $programModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function insertProgram(programModel $programModel): array
    {        
        $sql = "INSERT INTO tbprogram (name, controller, smarty, idprogramcategory, status) 
                     VALUES (:name, :controller, :langKey, :categoryId, :status)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":name",$programModel->getName());
        $stmt->bindValue(":controller",$programModel->getController());
        $stmt->bindValue(":langKey",$programModel->getLanguageKeyName());
        $stmt->bindValue(":categoryId",$programModel->getProgramCategoryId());
        $stmt->bindValue(":status",(empty($programModel->getStatus()) ? 'A': $programModel->getStatus()));
        $stmt->execute();

        $programModel->setProgramId($this->db->lastInsertId());

        $ret = true;
        $result = array("message"=>"","object"=>$programModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Inserts default permission by program
     * pt_br Insere permissão padrão por programa
     *
     * @param  programModel $programModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function insertDefaultPermission(programModel $programModel): array
    {        
        $sql = "INSERT INTO tbdefaultpermission (idprogram, idaccesstype, allow) 
                     VALUES (:programId, :accessTypeId, :allow)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":programId",$programModel->getProgramId());
        $stmt->bindValue(":accessTypeId",$programModel->getAccessTypeId());
        $stmt->bindValue(":allow",$programModel->getAllow());
        $stmt->execute();

        //$programModel->setPermissionId($this->db->lastInsertId());
		
        $ret = true;
        $result = array("message"=>"","object"=>$programModel);
        
        return array("status"=>$ret,"push"=>$result);
    }

	/**
     * en_us Inserts program's permission by person type 
     * pt_br Insere permissão do programa por tipo de pessoa
     *
     * @param  programModel $programModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function insertGroupPermission(programModel $programModel): array
    {        
        $sql = "INSERT INTO tbtypepersonpermission (idprogram,idtypeperson,idaccesstype,allow) 
                     VALUES (:programId, :personTypeId, :accessTypeId, :allow)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":programId",$programModel->getProgramId());
		    $stmt->bindValue(":personTypeId",$programModel->getPersonTypeId());
        $stmt->bindValue(":accessTypeId",$programModel->getAccessTypeId());
        $stmt->bindValue(":allow",$programModel->getAllow());
        $stmt->execute();

        //$programModel->setPermissionId($this->db->lastInsertId());

        $ret = true;
        $result = array("message"=>"","object"=>$programModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Saves the new program into DB
     * pt_br Grava o novo programa no banco de dados
     *
     * @param  programModel $programModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function saveProgram(programModel $programModel): array
    {   
        //$permDAO = new permissionDAO();
        //$permModel = new permissionModel();
        $aOperations = $programModel->getOperationList();
        
        try{
            $this->db->beginTransaction();

            $ins = $this->insertProgram($programModel);

            if($ins['status']){
                // insert program's permissions
                if(count($aOperations) > 0){
                    $retTypes = $this->fetchPersonTypes($ins['push']['object']);
                    $aPersonTypes = $retTypes['status'] ? $retTypes['push']['object']->getGridList() : array();
                    
                    foreach($aOperations as $k=>$v){
                        $ins['push']['object']->setAccessTypeId($v)
                                              ->setAllow("Y");
                        
                        $insDefault = $this->insertDefaultPermission($ins['push']['object']);// inserts default permission
                        
                        if(count($aPersonTypes) > 0){
                            foreach($aPersonTypes as $key=>$val){
                                $ins['push']['object']->setPersonTypeId($val['idtypeperson'])
                                                      ->setAllow("N");
                                
                                $insGroup = $this->insertGroupPermission($ins['push']['object']);// inserts permission by person type
                            }
                        }
                    }
                }
            }
            
            $ret = true;
            $result = array("message"=>"","object"=>$ins['push']['object']);
            $this->db->commit();
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error trying save program info ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
            $this->db->rollBack();
        }         
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Returns a list with registered cities
     * pt_br Retorna uma lista com cidades cadastradas
     *
     * @param  programModel $programModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function getProgram(programModel $programModel): array
    {        
        $sql = "SELECT tbp.idprogram, tbp.name, pvoc.key_value name_fmt,
                        tbtp.idmodule, tbm.name module, mvoc.key_value module_fmt, 
                        tbp.idprogramcategory, tbtp.name category, pcvoc.key_value category_fmt,  
                        tbp.controller, tbp.status, tbp.smarty,
                        GROUP_CONCAT(idaccesstype) operations
                  FROM tbprogram tbp
                  JOIN tbprogramcategory tbtp 
                    ON tbtp.idprogramcategory = tbp.idprogramcategory
                  JOIN tbmodule tbm
                    ON tbtp.idmodule = tbm.idmodule
                  JOIN tbvocabulary pvoc
                    ON pvoc.key_name = tbp.smarty
                  JOIN tblocale ploc
                    ON (ploc.idlocale = pvoc.idlocale AND
                        LOWER(ploc.name) = LOWER('{$_ENV['DEFAULT_LANG']}'))
                  JOIN tbvocabulary mvoc
                    ON mvoc.key_name = tbm.smarty
                  JOIN tblocale mloc
                    ON (mloc.idlocale = mvoc.idlocale AND
                        LOWER(mloc.name) = LOWER('{$_ENV['DEFAULT_LANG']}'))
                  JOIN tbvocabulary pcvoc
                    ON pcvoc.key_name = tbtp.smarty
                  JOIN tblocale pcloc
                    ON (pcloc.idlocale = pcvoc.idlocale AND
                        LOWER(pcloc.name) = LOWER('{$_ENV['DEFAULT_LANG']}'))
       LEFT OUTER JOIN tbdefaultpermission dp
                    ON dp.idprogram = tbp.idprogram
                 WHERE tbp.idprogram = :programId";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":programId",$programModel->getProgramId());
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);

            $programModel->setName((!is_null($aRet['name']) && !empty($aRet['name'])) ? $aRet['name'] : "")
                         ->setFmtName((!is_null($aRet['name_fmt']) && !empty($aRet['name_fmt'])) ? $aRet['name_fmt'] : "")
                         ->setModuleId((!is_null($aRet['idmodule']) && !empty($aRet['idmodule'])) ? $aRet['idmodule'] : 0)
                         ->setModule((!is_null($aRet['module_fmt']) && !empty($aRet['module_fmt'])) ? $aRet['module_fmt'] : "")
                         ->setProgramCategoryId((!is_null($aRet['idprogramcategory']) && !empty($aRet['idprogramcategory'])) ? $aRet['idprogramcategory'] : 0)
                         ->setProgramCategory((!is_null($aRet['module_fmt']) && !empty($aRet['module_fmt'])) ? $aRet['module_fmt'] : "")
                         ->setController((!is_null($aRet['controller']) && !empty($aRet['controller'])) ? $aRet['controller'] : "")
                         ->setStatus((!is_null($aRet['status']) && !empty($aRet['status'])) ? $aRet['status'] : "")
                         ->setLanguageKeyName((!is_null($aRet['smarty']) && !empty($aRet['smarty'])) ? $aRet['smarty'] : "")
                         ->setOperationList((!is_null($aRet['operations']) && !empty($aRet['operations'])) ? explode(",",$aRet['operations']) : array());

            $ret = true;
            $result = array("message"=>"","object"=>$programModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting program's data ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Updates program data into tbprogram
     * pt_br Atualiza os dados do programa no tbprogram
     *
     * @param  programModel $programModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function updateProgram(programModel $programModel): array
    {        
        $sql = "UPDATE tbprogram 
                   SET name = :name, 
                       controller = :controller, 
                       smarty = :langKey, 
                       idprogramcategory = :categoryId
                 WHERE idprogram = :programId";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":name",$programModel->getName());
        $stmt->bindValue(":controller",$programModel->getController());
        $stmt->bindValue(":langKey",$programModel->getLanguageKeyName());
        $stmt->bindValue(":categoryId",$programModel->getProgramCategoryId());
        $stmt->bindValue(":programId",$programModel->getProgramId());
        $stmt->execute();

        $ret = true;
        $result = array("message"=>"","object"=>$programModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Removes program's default permission
     * pt_br Deleta as permissões default do programa
     *
     * @param  programModel $programModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function deleteDefaultPermission(programModel $programModel): array
    {        
        $sql = "DELETE FROM tbdefaultpermission WHERE idprogram = :programId";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":programId",$programModel->getProgramId());
        $stmt->execute();

        $ret = true;
        $result = array("message"=>"","object"=>$programModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Removes program's permission by person type
     * pt_br Deleta as permissões por tipo de pessoa do programa
     *
     * @param  programModel $programModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function deleteGroupPermission(programModel $programModel): array
    {        
        $sql = "DELETE FROM tbtypepersonpermission WHERE idprogram = :programId";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":programId",$programModel->getProgramId());
        $stmt->execute();

        $ret = true;
        $result = array("message"=>"","object"=>$programModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Updates program's data into DB
     * pt_br Atualiza os dados do programa no banco de dados
     *
     * @param  programModel $programModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function saveUpdateProgram(programModel $programModel): array
    {   
        $aOperations = $programModel->getOperationList();
        
        try{
            $this->db->beginTransaction();

            $upd = $this->updateProgram($programModel);

            if($upd['status']){
                
                if($programModel->getFlgChangeOperations()){
                    $delDefault = $this->deleteDefaultPermission($upd['push']['object']); // delete program's default permission
                    $delGroup = $this->deleteGroupPermission($upd['push']['object']); // delete program's permission by person type

                    // insert program's permissions
                    if(count($aOperations) > 0){
                        $retTypes = $this->fetchPersonTypes($upd['push']['object']);
                        $aPersonTypes = $retTypes['status'] ? $retTypes['push']['object']->getGridList() : array();
                    
                        foreach($aOperations as $k=>$v){
                            $upd['push']['object']->setAccessTypeId($v)
                                                  ->setAllow("Y");
                        
                            $updDefault = $this->insertDefaultPermission($upd['push']['object']);// inserts default permission
                            
                            if(count($aPersonTypes) > 0){
                                foreach($aPersonTypes as $key=>$val){
                                    $upd['push']['object']->setPersonTypeId($val['idtypeperson'])
                                                          ->setAllow("N");
                                                          
                                    $updGroup = $this->insertGroupPermission($upd['push']['object']);// inserts permission by person type
                                }
                            }
                        }
                    }

                }
            }
            
            $ret = true;
            $result = array("message"=>"","object"=>$upd['push']['object']);
            $this->db->commit();
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error trying update program info ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
            $this->db->rollBack();
        }         
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Updates program's status 
     * pt_br Atualiza o status do programa
     *
     * @param  programModel $programModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function changeProgramStatus(programModel $programModel): array
    {   
        $sql = "UPDATE tbprogram SET status = :newStatus WHERE idprogram = :programId";

        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":programId",$programModel->getProgramId());
            $stmt->bindParam(':newStatus', $programModel->getStatus());
            $stmt->execute();

            $ret = true;
            $result = array("message"=>"","object"=>$programModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error update program's status", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }         
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Return an array with program's categories to display in grid
     * pt_br Retorna um array com as categorias de programa para mostrar no grid
     *
     * @param  string $where
     * @param  string $group
     * @param  string $order
     * @param  string $limit
     * @return array  Parameters returned in array: 
     *                [status = true/false
     *                 push =  [message = PDO Exception message 
     *                          object = model's object]]
     */
    public function queryCategories($where=null,$group=null,$order=null,$limit=null): array
    {
        
        $sql = "SELECT idprogramcategory, `name`, idmodule, smarty
                  FROM tbprogramcategory
                $where $group $order $limit";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $programModel = new programModel();
            $programModel->setGridList($aRet);

            $ret = true;
            $result = array("message"=>"","object"=>$programModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting programs ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * en_us Inserts program's category data into tbprogramcategory
     * pt_br Insere os dados da categoria de programa no tbprogramcategory
     *
     * @param  mixed $programModel
     * @return array
     */
    public function insertCategory(programModel $programModel): array
    {
        
        $sql = "INSERT INTO tbprogramcategory (`name`,idmodule,smarty) VALUES (:name,:moduleId,:langKey)";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":name",$programModel->getProgramCategory());
            $stmt->bindValue(":moduleId",$programModel->getModuleId());
            $stmt->bindValue(":langKey",$programModel->getLanguageKeyName());
            $stmt->execute();

            $programModel->setProgramCategoryId($this->db->lastInsertId());

            $ret = true;
            $result = array("message"=>"","object"=>$programModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error saving program's category", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    
}