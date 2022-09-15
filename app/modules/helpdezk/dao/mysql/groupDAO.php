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
     * Return an array with department to display in grid
     *
     * @param  string $where
     * @param  string $group
     * @param  string $order
     * @param  string $limit
     * @return array
     */  
    
    /*public function queryGroup($where=null,$group=null,$order=null,$limit=null): array
    {
        
        $sql = "SELECT a.iddepartment, b.name company, a.status, a.name AS department, 
                    a.idperson AS idcompany
                FROM hdk_tbdepartment a, tbperson b 
                WHERE a.idperson = b.idperson 
                $where $group $order $limit";
               // echo "{$sql}\n";
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $department = new departmentModel(); 
            $department->setgridList($aRet);

            $ret = true;
            $result = array("message"=>"","object"=>$department);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error query department ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    public function countDepartment($where=null): array
    {        
        $sql = "SELECT COUNT(iddepartment) total
                FROM hdk_tbdepartment a, tbperson b 
                WHERE a.idperson = b.idperson  
                $where";
    
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            $department = new departmentModel();
            $department->setTotalRows($aRet['total']);

            $ret = true;
            $result = array("message"=>"","object"=>$department);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error counting department ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }*/
    
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
        
        $sql = "SELECT pers.idperson, pers.email, pers.name operator_name, grpname.name
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
            $this->loggerDB->error("Error query department ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

}