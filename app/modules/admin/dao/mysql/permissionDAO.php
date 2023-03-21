<?php

namespace App\modules\admin\dao\mysql;

use App\core\Database;
use App\modules\admin\models\mysql\permissionModel;

class permissionDAO extends Database
{
    public function __construct()
    {
        parent::__construct(); 
    }

    /**
     * en_us Returns an array with default permissions
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
    /* public function querypermissions($where=null,$group=null,$order=null,$limit=null): array
    {
        
        $sql = "SELECT tbp.idpermission, tbp.name, pvoc.key_value name_fmt,
                       tbm.name module, mvoc.key_value module_fmt, 
                       tbtp.name category, pcvoc.key_value category_fmt,  
                       tbp.controller, tbp.status
                  FROM tbpermission tbp
                  JOIN tbpermissioncategory tbtp 
                    ON tbtp.idpermissioncategory = tbp.idpermissioncategory
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
            $permissionModel = new permissionModel();
            $permissionModel->setGridList($aRet);

            $ret = true;
            $result = array("message"=>"","object"=>$permissionModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting permissions ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    } */

    /**
     * Return an array with total of permissions to display in grid
     *
     * @param  string $where
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    /* public function countpermissions($where=null): array
    {        
        $sql = "SELECT COUNT(tbp.idpermission) total
                  FROM tbpermission tbp
                  JOIN tbpermissioncategory tbtp 
                    ON tbtp.idpermissioncategory = tbp.idpermissioncategory
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
            $permissionModel = new permissionModel();
            $permissionModel->setTotalRows($aRet['total']);

            $ret = true;
            $result = array("message"=>"","object"=>$permissionModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error counting permissions ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    } */

    /**
     * en_us Returns default permissions
     * pt_br Retorna permissões padrões
     *
     * @param  permissionModel $permissionModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchDefaultPermissionsByProgram(permissionModel $permissionModel): array
    {        
        $sql = "SELECT idaccesstype FROM tbdefaultpermission WHERE idprogram = :programId";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':programId', $permissionModel->getProgramId());
            $stmt->execute();
            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $permissionModel->setDefaultPermissionList($aRet);

            $ret = true;
            $result = array("message"=>"","object"=>$permissionModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting default permissions by program ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Returns default permissions
     * pt_br Retorna permissões padrões
     *
     * @param  permissionModel $permissionModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function getUserPermission(permissionModel $permissionModel): array
    {        
        $sql = "SELECT tbp.name, act.type, perm.allow, per.name AS person
                  FROM tbprogram tbp, tbmodule tbm, tbprogramcategory tbtp,
                       tbaccesstype act, tbpermission perm, tbperson per
                 WHERE tbtp.idmodule = tbm.idmodule
                   AND tbtp.idprogramcategory = tbp.idprogramcategory
                   AND perm.idaccesstype = act.idaccesstype
                   AND perm.idperson = per.idperson
                   AND tbp.idprogram = perm.idprogram
                   AND tbp.idprogram = :programId
                   AND per.idperson = :personId
                   AND act.idaccesstype = :accessTypeId";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':programId', $permissionModel->getProgramId());
            $stmt->bindParam(':personId', $permissionModel->getPersonId());
            $stmt->bindParam(':accessTypeId', $permissionModel->getAccessTypeId());
            $stmt->execute();
            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);

            $permissionModel->setProgramName((!empty($aRet['name']) && !is_null($aRet['name'])) ? $aRet['name'] : 'N')
                            ->setAccessType((!empty($aRet['type']) && !is_null($aRet['type'])) ? $aRet['type'] : 'N')
                            ->setAllow((!empty($aRet['allow']) && !is_null($aRet['allow'])) ? $aRet['allow'] : 'N')
                            ->setPersonName((!empty($aRet['person']) && !is_null($aRet['person'])) ? $aRet['person'] : 'N');

            $ret = true;
            $result = array("message"=>"","object"=>$permissionModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting default permissions by program ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Returns default permissions
     * pt_br Retorna permissões padrões
     *
     * @param  permissionModel $permissionModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function grantUserPermission(permissionModel $permissionModel): array
    {        
        $sql = "CALL hdk_insertpersonpermission(:programId,:personId,:accessTypeId,:allow)";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':programId', $permissionModel->getProgramId());
            $stmt->bindValue(':personId', $permissionModel->getPersonId());
            $stmt->bindValue(':accessTypeId', $permissionModel->getAccessTypeId());
            $stmt->bindValue(':allow', $permissionModel->getAllow());
            $stmt->execute();

            $ret = true;
            $result = array("message"=>"","object"=>$permissionModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting default permissions by program ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Returns default permissions
     * pt_br Retorna permissões padrões
     *
     * @param  permissionModel $permissionModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchUserPermissionsByProgram(permissionModel $permissionModel): array
    {        
        $sql = "SELECT idaccesstype,allow FROM tbpermission WHERE idperson = :userId AND idprogram = :programId
                ORDER BY idaccesstype";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':programId', $permissionModel->getProgramId());
            $stmt->bindParam(':userId', $permissionModel->getPersonId());
            $stmt->execute();
            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $permissionModel->setUserPermissionList((!empty($aRet) && !is_null($aRet)) ? $aRet : array());

            $ret = true;
            $result = array("message"=>"","object"=>$permissionModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting user's permissions by program ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Inserts default permission by program
     * pt_br Insere permissão padrão por programa
     *
     * @param  permissionModel $permissionModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function insertDefaultPermission(permissionModel $permissionModel): array
    {        
        $sql = "INSERT INTO tbdefaultpermission (idprogram, idaccesstype, allow) 
                     VALUES (:programId, :accessTypeId, :allow)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":programId",$permissionModel->getProgramId());
        $stmt->bindValue(":accessTypeId",$permissionModel->getAccessTypeId());
        $stmt->bindValue(":allow",$permissionModel->getAllow());
        $stmt->execute(); echo "",print_r($permissionModel,true),"\n";

        $permissionModel->setPermissionId($this->db->lastInsertId());
		
        $ret = true;
        $result = array("message"=>"","object"=>$permissionModel);
        
        return array("status"=>$ret,"push"=>$result);
    }

	/**
     * en_us Inserts program's permission by person type 
     * pt_br Insere permissão do programa por tipo de pessoa
     *
     * @param  permissionModel $permissionModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function insertGroupPermission(permissionModel $permissionModel): array
    {        
        $sql = "INSERT INTO tbtypepersonpermission (idprogram,idtypeperson,idaccesstype,allow) 
                     VALUES (:programId, :personTypeId, :accessTypeId, :allow)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":programId",$permissionModel->getProgramId());
		$stmt->bindValue(":personTypeId",$permissionModel->getPersonTypeId());
        $stmt->bindValue(":accessTypeId",$permissionModel->getAccessTypeId());
        $stmt->bindValue(":allow",$permissionModel->getAllow());
        $stmt->execute();

        $permissionModel->setPermissionId($this->db->lastInsertId());

        $ret = true;
        $result = array("message"=>"","object"=>$permissionModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Returns default permissions
     * pt_br Retorna permissões padrões
     *
     * @param  permissionModel $permissionModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchUserTypePermissionsByProgram(permissionModel $permissionModel): array
    {        
        $sql = "SELECT idaccesstype,allow 
                  FROM tbtypepersonpermission 
                 WHERE idprogram = :programId
                   AND (idtypeperson IN (SELECT idtypeperson FROM tbpersontypes WHERE idperson = :userId) 
                        OR idtypeperson = (SELECT idtypeperson FROM tbperson WHERE idperson = :userId))
              ORDER BY idaccesstype";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':programId', $permissionModel->getProgramId());
            $stmt->bindParam(':userId', $permissionModel->getPersonId());
            $stmt->execute();
            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $permissionModel->setUserTypePermissionList((!empty($aRet) && !is_null($aRet)) ? $aRet : array());

            $ret = true;
            $result = array("message"=>"","object"=>$permissionModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting user type permissions by program ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    
}