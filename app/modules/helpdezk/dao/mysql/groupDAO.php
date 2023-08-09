<?php

namespace App\modules\helpdezk\dao\mysql;

use App\core\Database;
use App\modules\helpdezk\models\mysql\groupModel;

use App\modules\admin\dao\mysql\personDAO;
use App\modules\admin\models\mysql\personModel;

class groupDAO extends Database
{
    
    public function __construct()
    {
        parent::__construct(); 
    }
    
    /**
     * en_us Returns an array with groups data to display in grid
     * pt_br Retorna um array com os dados dos grupos para visualização no grid
     *
     * @param  string $where
     * @param  string $group
     * @param  string $order
     * @param  string $limit
     * @return array            Parameters returned in array: 
     *                          [status = true/false
     *                           push =  [message = PDO Exception message 
     *                                    object = model's object]]
     */
    public function queryGroups($where=null,$group=null,$order=null,$limit=null): array
    {
        
        $sql = "SELECT tbg.idgroup, tbg.idperson, tbp.name, tbg.level, tbg.status, tbg.idcustomer AS idcompany, tbp2.name AS company
                  FROM hdk_tbgroup tbg, tbperson tbp, tbperson tbp2
                 WHERE tbg.idperson = tbp.idperson
                   AND tbp2.idperson = tbg.idcustomer
                $where $group $order $limit";

        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $groupModel = new groupModel(); 
            $groupModel->setGridList((!empty($aRet) && count($aRet) > 0) ? $aRet : array());

            $ret = true;
            $result = array("message"=>"","object"=>$groupModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error query groups ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * en_us Returns an array with rows total for grid pagination
     * pt_br Retorna um array com o total de registros para paginação do grid
     *
     * @param  mixed $where
     * @return array            Parameters returned in array: 
     *                          [status = true/false
     *                           push =  [message = PDO Exception message 
     *                                    object = model's object]]
     */
    public function countGroups($where=null): array
    {        
        $sql = "SELECT COUNT(tbg.idgroup) total
                  FROM hdk_tbgroup tbg, tbperson tbp, tbperson tbp2
                 WHERE tbg.idperson = tbp.idperson
                   AND tbp2.idperson = tbg.idcustomer  
                $where";
    
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            $groupModel = new groupModel();
            $groupModel->setTotalRows((!is_null($aRet['total']) && !empty($aRet['total'])) ? $aRet['total'] : 0);

            $ret = true;
            $result = array("message"=>"","object"=>$groupModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error counting groups ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * Checks if the group is for repassing only
     *
     * @param  groupModel $groupModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function checkGroupOnlyRepass(groupModel $groupModel): array
    {  

        $sql = "SELECT repass_only FROM hdk_tbgroup WHERE idperson = :groupID";
                 
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':groupID', $groupModel->getIdGroup());
            $stmt->execute();
           
            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            $groupModel->setIsRepassOnly($aRet['repass_only']);

            $ret = true;
            $result = array("message"=>"","object"=>$groupModel);
        }catch(\PDOException $ex){ $msg = $ex->getMessage();
            $this->loggerDB->error('Error get group ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
        
    } 

    /**
     * Checks if the group is for repassing only
     *
     * @param  groupModel $groupModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function getNewGroupOnlyRepass(groupModel $groupModel): array
    {
        $sql = "SELECT a.idperson 
                  FROM hdk_tbgroup a, hdk_tbgroup_alias b 
                 WHERE b.idalias = :groupID 
                   AND a.idperson = b.idgroup 
                   AND a.idcustomer = :companyID";
                 
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':groupID', $groupModel->getIdGroup());
            $stmt->bindValue(':groupID', $groupModel->getIdCompany());
            $stmt->execute();
           
            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            $groupModel->setNewIdGroup((($aRet['idperson']) ? $aRet['idperson'] : 0));

            $ret = true;
            $result = array("message"=>"","object"=>$groupModel);
        }catch(\PDOException $ex){ $msg = $ex->getMessage();
            $this->loggerDB->error('Error get group ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
        
    }

    /**
     * Return an array with group's operators
     *
     * @param  groupModel $groupModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchGroupOperators(groupModel $groupModel): array
    {
        
        $sql = "SELECT pers.idperson, pers.email, pers.name operator_name, grpname.name, pers.login
                  FROM tbperson pers, tbperson grpname, hdk_tbgroup grp, hdk_tbgroup_has_person pergrp
                 WHERE pers.idperson = pergrp.idperson
                   AND pers.status = 'A'
                   AND grp.idgroup = pergrp.idgroup
                   AND grpname.idperson = grp.idperson
                   AND grpname.idperson = :groupID";
               // echo "{$sql}\n";
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':groupID', $groupModel->getIdGroup());
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $groupModel->setGridList($aRet);

            $ret = true;
            $result = array("message"=>"","object"=>$groupModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error fetching group's operators ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Returns an array with user's groups
     * pt_br Retorna um array com os grupos do usuário
     *
     * @param  groupModel $groupModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchUserGroups(groupModel $groupModel): array
    {
        
        $sql = "SELECT grppr.name as pername, per.idperson, grp.idperson as idpergroup, grp.idgroup
                  FROM hdk_tbgroup grp, tbperson per, tbperson grppr, hdk_tbgroup_has_person rel
                 WHERE per.idperson = rel.idperson
                   AND grppr.idperson = grp.idperson
                   AND grp.idgroup = rel.idgroup
                   AND per.idperson = :userID";
               // echo "{$sql}\n";
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':userID', $groupModel->getIdUser());
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $groupModel->setGridList($aRet);

            $ret = true;
            $result = array("message"=>"","object"=>$groupModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error fetching operator's groups ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * Return an array with group's operators
     *
     * @param  groupModel $groupModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchGroupUsers(groupModel $groupModel): array
    {
        
        $sql = "SELECT pers.idperson, pers.email, pers.name operator_name, grpname.name, pers.login
                  FROM tbperson pers, tbperson grpname, hdk_tbgroup grp, hdk_tbgroup_has_person pergrp
                 WHERE pers.idperson = pergrp.idperson
                   AND pers.status = 'A'
                   AND grp.idgroup = pergrp.idgroup
                   AND grpname.idperson = grp.idperson
                   AND grp.idgroup = :groupID";
               // echo "{$sql}\n";
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':groupID', $groupModel->getIdGroup());
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $groupModel->setGridList($aRet);

            $ret = true;
            $result = array("message"=>"","object"=>$groupModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error fetching group's operators ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * insertGroup
     * 
     * en_us Saves group's data in hdk_tbgroup table
     * pr_br Grava os dados do grupo na tabela hdk_tbgroup
     *
     * @param  groupModel $groupModel
     * @return array
     */
    public function insertGroup(groupModel $groupModel): array
    {        
        $sql = "INSERT INTO hdk_tbgroup (idperson,level,idcustomer,repass_only) 
                     VALUES (:personId,:level,:customerId,:isRepassOnly)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":personId",$groupModel->getPersonId());
        $stmt->bindValue(":level",$groupModel->getGroupLevel());
        $stmt->bindValue(":customerId",$groupModel->getIdCompany());
        $stmt->bindValue(":isRepassOnly",$groupModel->getIsRepassOnly());
        $stmt->execute();

        $groupModel->setIdGroup($this->db->lastInsertId());

        $ret = true;
        $result = array("message"=>"","object"=>$groupModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * saveGroup
     * 
     * en_us Saves group's data in DB
     * pr_br Grava os dados do grupo no BD
     *
     * @param  groupModel $groupModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function saveGroup(groupModel $groupModel): array
    {   
        $personDAO = new personDAO();
        $personDTO = new personModel();
        
        try{
            $personDTO->setIdTypeLogin(3)
                      ->setIdTypePerson(6)
                      ->setPersonNatureId(1)
                      ->setThemeId(1)
                      ->setName($groupModel->getGroupName())
                      ->setLogin('')
                      ->setPassword('')
                      ->setEmail('')
                      ->setUserVip('N')
                      ->setTelephone('')
                      ->setBranchNumber('')
                      ->setCellphone('')
                      ->setFax('')
                      ->setLocationId(0)
                      ->setTimeValue(0)
                      ->setOvertimeWork(0)
                      ->setChangePasswordFlag(0);

            $this->db->beginTransaction();

            $ins = $personDAO->insertPerson($personDTO);

            if($ins['status']){
                //insert group in hdk_tbgroup
                $groupModel->setPersonId($ins['push']['object']->getIdPerson());
                $insGroup = $this->insertGroup($groupModel);
            }
            
            $ret = true;
            $result = array("message"=>"","object"=>$insGroup['push']['object']);
            $this->db->commit();
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error trying save group info', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
            $this->db->rollBack();
        }         
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * getGroup
     * 
     * en_us Returns group's data
     * pr_br Retorna os dados do grupo
     *
     * @param  groupModel $groupModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function getGroup(groupModel $groupModel): array
    {
        
        $sql = "SELECT tbg.idgroup, tbg.idperson, tbp.name, tbg.level, tbg.status, tbg.idcustomer AS idcompany, tbp2.name AS company, tbg.repass_only
                  FROM hdk_tbgroup tbg, tbperson tbp, tbperson tbp2
                 WHERE tbg.idperson = tbp.idperson
                   AND tbp2.idperson = tbg.idcustomer
                   AND tbg.idgroup = :groupID";
               // echo "{$sql}\n";
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':groupID', $groupModel->getIdGroup());
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);

            $groupModel->setPersonId((!is_null($aRet['idperson']) && !empty($aRet['idperson'])) ? $aRet['idperson'] : 0)
                       ->setIdCompany((!is_null($aRet['idcompany']) && !empty($aRet['idcompany'])) ? $aRet['idcompany'] : 0)
                       ->setCompanyName((!is_null($aRet['company']) && !empty($aRet['company'])) ? $aRet['company'] : "")
                       ->setGroupName((!is_null($aRet['name']) && !empty($aRet['name'])) ? $aRet['name'] : "")
                       ->setGroupLevel((!is_null($aRet['level']) && !empty($aRet['level'])) ? $aRet['level'] : "")
                       ->setIsRepassOnly((!is_null($aRet['repass_only']) && !empty($aRet['repass_only'])) ? $aRet['repass_only'] : "")
                       ->setStatus((!is_null($aRet['status']) && !empty($aRet['status'])) ? $aRet['status'] : "");

            $ret = true;
            $result = array("message"=>"","object"=>$groupModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting group's data", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * updateGroup
     * 
     * en_us Updates group's data in hdk_tbgroup table
     * pr_br Atualiza os dados do grupo na tabela hdk_tbgroup
     *
     * @param  groupModel $groupModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function updateGroup(groupModel $groupModel): array
    {
        $sql = "UPDATE hdk_tbgroup 
                   SET level = :level,
                       idcustomer = :customerId,
                       repass_only = :isRepassOnly
                 WHERE idgroup = :groupId";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":level",$groupModel->getGroupLevel());
        $stmt->bindValue(":customerId",$groupModel->getIdCompany());
        $stmt->bindValue(":isRepassOnly",$groupModel->getIsRepassOnly());
        $stmt->bindValue(":groupId",$groupModel->getIdGroup());
        $stmt->execute();

        $ret = true;
        $result = array("message"=>"","object"=>$groupModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * saveUpdateGroup
     * 
     * en_us Updates group's data in DB
     * pr_br Atualiza os dados do grupo no BD
     *
     * @param  groupModel $groupModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function saveUpdateGroup(groupModel $groupModel): array
    {   
        $personDAO = new personDAO();
        $personDTO = new personModel();
        
        try{
            $personDTO->setIdTypeLogin(3)
                      ->setIdTypePerson(6)
                      ->setName($groupModel->getGroupName())
                      ->setEmail('')
                      ->setUserVip('N')
                      ->setTelephone('')
                      ->setBranchNumber('')
                      ->setCellphone('')
                      ->setFax('')
                      ->setLocationId(0)
                      ->setTimeValue(0)
                      ->setOvertimeWork(0)
                      ->setIdPerson($groupModel->getPersonId());

            $this->db->beginTransaction();

            $upd = $personDAO->updatePerson($personDTO);

            if($upd['status']){
                //insert group in hdk_tbgroup
                $updGroup = $this->updateGroup($groupModel);
            }
            
            $ret = true;
            $result = array("message"=>"","object"=>$updGroup['push']['object']);
            $this->db->commit();
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error updating group info', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
            $this->db->rollBack();
        }         
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * updateGroupStatus
     * 
     * en_us Updates group's status
     * pr_br Atualiza o status do grupo
     *
     * @param  groupModel $groupModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function updateGroupStatus(groupModel $groupModel): array
    {   
        $sql = "UPDATE hdk_tbgroup SET `status` = :newStatus WHERE idgroup = :groupId";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":newStatus",$groupModel->getStatus());
            $stmt->bindValue(":groupId",$groupModel->getIdGroup());
            $stmt->execute();
            
            $ret = true;
            $result = array("message"=>"","object"=>$groupModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error updating group's status", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }         
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * checkAttendantInGroup
     * 
     * en_us Checks if the attendant is already in the group
     * pr_br Verifica se o atendente já está no grupo
     *
     * @param  groupModel $groupModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function checkAttendantInGroup(groupModel $groupModel): array
    {   
        $sql = "SELECT idgroup FROM hdk_tbgroup_has_person WHERE idperson = :attendantId AND idgroup = :groupId";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":attendantId",$groupModel->getIdUser());
            $stmt->bindValue(":groupId",$groupModel->getIdGroup());
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);

            $groupModel->setInGroupFlag((!is_null($aRet['idgroup']) && !empty($aRet['idgroup'])) ? 1 : 0);
            
            $ret = true;
            $result = array("message"=>"","object"=>$groupModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error updating group's status", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }         
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * insertGroupAttendant
     * 
     * en_us Add link between attendant and group
     * pr_br Insere o vínculo entre o atendente e o grupo
     *
     * @param  groupModel $groupModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function insertGroupAttendant(groupModel $groupModel): array
    {   
        $sql = "INSERT INTO hdk_tbgroup_has_person (idperson,idgroup) VALUES (:attendantId,:groupId)";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":attendantId",$groupModel->getIdUser());
            $stmt->bindValue(":groupId",$groupModel->getIdGroup());
            $stmt->execute();
            
            $ret = true;
            $result = array("message"=>"","object"=>$groupModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error saving group's attendant", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }         
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * deleteGroupAttendant
     * 
     * en_us Remove link between attendant and group
     * pr_br Deleta o vínculo entre o atendente e o grupo
     *
     * @param  groupModel $groupModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function deleteGroupAttendant(groupModel $groupModel): array
    {   
        $sql = "DELETE FROM hdk_tbgroup_has_person WHERE idperson = :attendantId AND idgroup = :groupId";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":attendantId",$groupModel->getIdUser());
            $stmt->bindValue(":groupId",$groupModel->getIdGroup());
            $stmt->execute();
            
            $ret = true;
            $result = array("message"=>"","object"=>$groupModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error deleting link between attendant and group", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }         
        
        return array("status"=>$ret,"push"=>$result);
    }

}