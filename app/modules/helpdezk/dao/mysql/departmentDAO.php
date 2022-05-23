<?php

namespace App\modules\helpdezk\dao\mysql;

use App\core\Database;
use App\modules\helpdezk\models\mysql\departmentModel;

class departmentDAO extends Database
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
    
    public function queryDepartment($where=null,$group=null,$order=null,$limit=null): array
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
    }

    public function getDepartment(departmentModel $departmentModel): array
    {  

        $sql = "SELECT a.iddepartment, b.name company, a.status, a.name AS department, 
                a.idperson AS idcompany
                FROM hdk_tbdepartment a, tbperson b 
                WHERE a.idperson = b.idperson 
                AND iddepartment=:departmentID";
                 
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':departmentID', $departmentModel->getIdDepartment());
            $stmt->execute();
           
            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            $departmentModel->setIdDepartment($aRet['iddepartment'])
                                ->setDepartment($aRet['department'])
                                ->setIdCompany($aRet['idcompany']);

            $ret = true;
            $result = array("message"=>"","object"=>$departmentModel);
        }catch(\PDOException $ex){ $msg = $ex->getMessage();
            $this->loggerDB->error('Error get department ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
        
    } 

    /**
     * Insert department's data into the database
     * @param  string $department
     * @param  string $default
     * @return departmentModel
     */

    public function insertDepartment(departmentModel $departmentModel): array
    {        
        $sql = "INSERT INTO hdk_tbdepartment(`idperson`,`name`)
                VALUES(:company,:department)";
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':department', $departmentModel->getDepartment());
            $stmt->bindParam(':company', $departmentModel->getIdCompany());
            $stmt->execute();

            $departmentModel->setIdDepartment($this->db->lastInsertId());
            $ret = true;
            $result = array("message"=>"","object"=>$departmentModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error insert department's", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    public function updateDepartment(departmentModel $departmentModel): array
    {        
        $sql = "UPDATE hdk_tbdepartment
                SET `idperson` = :company,
                    `name` = :department
                WHERE iddepartment = :departmentID";     
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':departmentID', $departmentModel->getIdDepartment());
            $stmt->bindParam(':department', $departmentModel->getDepartment());
            $stmt->bindParam(':company', $departmentModel->getIdCompany());
            $stmt->execute();

            $ret = true;
            $result = array("message"=>"","object"=>$departmentModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error update department', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    public function updateStatus(departmentModel $departmentModel): array
    {        
        $sql = "UPDATE hdk_tbdepartment
                SET status = :newStatus
                WHERE iddepartment = :departmentID";
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':departmentID', $departmentModel->getIdDepartment());
            $stmt->bindParam(':newStatus', $departmentModel->getStatus());
            $stmt->execute();

            $ret = true;
            $result = array("message"=>"","object"=>$departmentModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error update department's status", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    public function deleteDepartment(departmentModel $departmentModel): array
    {        
        $sql = "DELETE FROM hdk_tbdepartment
                WHERE iddepartment = :departmentID";        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':departmentID', $departmentModel->getIdDepartment());
            $stmt->execute();

            $ret = true;
            $result = array("message"=>"","object"=>$departmentModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error delete department ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
}