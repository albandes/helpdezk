<?php

namespace App\modules\helpdezk\dao\mysql;

use App\core\Database;
use App\modules\helpdezk\models\mysql\groupModel;

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
            $groupModel = new departmentModel();
            $groupModel->setTotalRows($aRet['total']);

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

}