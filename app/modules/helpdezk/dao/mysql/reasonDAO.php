<?php

namespace App\modules\helpdezk\dao\mysql;

use App\core\Database;
use App\modules\helpdezk\models\mysql\reasonModel;

class reasonDAO extends Database
{
    
    public function __construct()
    {
        parent::__construct(); 
    }
    
    /**
     * Return an array with reason to display in grid
     *
     * @param  string $where
     * @param  string $group
     * @param  string $order
     * @param  string $limit
     * @return array
     */  
    
    public function queryReason($where=null,$group=null,$order=null,$limit=null): array
    {        
        $sql = "SELECT a.idreason, a.name reason, a.status, 
                        b.idservice, b.name AS service, 
                        c.iditem, c.name AS item, 
                        d.idtype, d.name AS `type`, 
                        e.idarea, e.name AS `area`  
                FROM hdk_tbcore_reason a, 
                    hdk_tbcore_service b, 
                    hdk_tbcore_item c, 
                    hdk_tbcore_type d, 
                    hdk_tbcore_area e 
                WHERE b.idservice = a.idservice 
                AND b.iditem = c.iditem
                AND c.idtype = d.idtype
                AND d.idarea = e.idarea  
                $where $group $order $limit";
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $reason = new reasonModel(); 
            $reason->setgridList($aRet);

            $ret = true;
            $result = array("message"=>"","object"=>$reason);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error query reason ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    public function countReason($where=null): array
    {        
        $sql = "SELECT COUNT(idreason) total
                FROM hdk_tbcore_reason a, 
                    hdk_tbcore_service b, 
                    hdk_tbcore_item c, 
                    hdk_tbcore_type d, 
                    hdk_tbcore_area e 
                WHERE b.idservice = a.idservice 
                AND b.iditem = c.iditem
                AND c.idtype = d.idtype
                AND d.idarea = e.idarea 
                $where";
    
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            $reason = new reasonModel();
            $reason->setTotalRows($aRet['total']);

            $ret = true;
            $result = array("message"=>"","object"=>$reason);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error counting reason ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    public function getReason(reasonModel $reasonModel): array
    {  

        $sql = "SELECT a.idreason, a.name reason, a.status, 
                        b.idservice, b.name AS service, 
                        c.iditem, c.name AS item, 
                        d.idtype, d.name AS `type`, 
                        e.idarea, e.name AS `area`  
                FROM hdk_tbcore_reason a, 
                    hdk_tbcore_service b, 
                    hdk_tbcore_item c, 
                    hdk_tbcore_type d, 
                    hdk_tbcore_area e 
                WHERE b.idservice = a.idservice 
                AND b.iditem = c.iditem
                AND c.idtype = d.idtype
                AND d.idarea = e.idarea 
                AND idreason=:reasonID";
                 
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':reasonID', $reasonModel->getIdReason());
            $stmt->execute();
           
            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            $reasonModel->setIdReason($aRet['idreason'])
                                ->setReason($aRet['reason'])
                                ->setIdArea($aRet['idarea'])
                                ->setIdType($aRet['idtype'])
                                ->setIdItem($aRet['iditem'])
                                ->setIdService($aRet['idservice']);

            $ret = true;
            $result = array("message"=>"","object"=>$reasonModel);
        }catch(\PDOException $ex){ $msg = $ex->getMessage();
            $this->loggerDB->error('Error get reason ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
        
    }

    public function insertReason(reasonModel $reasonModel): array
    {        
        $sql = "INSERT INTO hdk_tbcore_reason(`idservice`,`name`)
                VALUES(:service,:reason)";
               // echo "{$sql}\n";
        try{
            $stmt = $this->db->prepare($sql);            
            $stmt->bindParam(':service', $reasonModel->getIdService());
            $stmt->bindParam(':reason', $reasonModel->getReason());
            $stmt->execute();

            $reasonModel->setIdReason($this->db->lastInsertId());
            $ret = true;
            $result = array("message"=>"","object"=>$reasonModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error insert reason's", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    public function updateReason(reasonModel $reasonModel): array
    {        
        $sql = "UPDATE hdk_tbcore_reason
                SET `idservice` = :service,
                    `name` = :reason
                WHERE idreason = :reasonID";  
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':reasonID', $reasonModel->getIdReason());
            $stmt->bindParam(':service', $reasonModel->getIdService());
            $stmt->bindParam(':reason', $reasonModel->getReason());
            $stmt->execute();

            $ret = true;
            $result = array("message"=>"","object"=>$reasonModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error update department', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    public function updateStatus(reasonModel $reasonModel): array
    {        
        $sql = "UPDATE hdk_tbcore_reason
                SET status = :newStatus
                WHERE idreason = :reasonID";
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':reasonID', $reasonModel->getIdReason());
            $stmt->bindParam(':newStatus', $reasonModel->getStatus());
            $stmt->execute();

            $ret = true;
            $result = array("message"=>"","object"=>$reasonModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error update reason's status", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
}