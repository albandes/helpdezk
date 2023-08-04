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
     * queryReason
     * 
     * en_us Returns an array with a reason list to display in the grid
     * pt_br Retorna um array com uma lista de motivos para exibir no grid
     *
     * @param  mixed $where
     * @param  mixed $group
     * @param  mixed $order
     * @param  mixed $limit
     * @return array
     */
    public function queryReason($where=null,$group=null,$order=null,$limit=null): array
    {        
        $sql = "SELECT a.idreason, a.name reason, a.status, b.idservice, b.name  service, c.iditem, c.name  item, d.idtype, d.name `type`, 
                        e.idarea, e.name `area`  
                  FROM hdk_tbcore_reason a, hdk_tbcore_service b, hdk_tbcore_item c, hdk_tbcore_type d, hdk_tbcore_area e 
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
    
    /**
     * countReason
     * 
     * en_us Returns an array with rows total for grid pagination
     * pt_br Retorna um array com o total de linhas para paginação do grid
     *
     * @param  mixed $where
     * @return array
     */
    public function countReason($where=null): array
    {        
        $sql = "SELECT COUNT(idreason) total
                  FROM hdk_tbcore_reason a, hdk_tbcore_service b, hdk_tbcore_item c, hdk_tbcore_type d, hdk_tbcore_area e 
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
    
    /**
     * getReason
     * 
     * en_us Returns reason's data
     * pt_br Retorna os dados do motivo
     *
     * @param  reasonModel $reasonModel
     * @return array
     */
    public function getReason(reasonModel $reasonModel): array
    {
        $sql = "SELECT a.idreason, a.name reason, a.status, b.idservice, b.name  service, c.iditem, c.name  item, d.idtype, d.name `type`, 
                       e.idarea, e.name `area`  
                  FROM hdk_tbcore_reason a, hdk_tbcore_service b, hdk_tbcore_item c, hdk_tbcore_type d, hdk_tbcore_area e 
                 WHERE b.idservice = a.idservice 
                   AND b.iditem = c.iditem
                   AND c.idtype = d.idtype
                   AND d.idarea = e.idarea
                   AND idreason = :reasonID";
                 
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':reasonID', $reasonModel->getIdReason());
            $stmt->execute();
           
            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            $reasonModel->setIdReason((!is_null($aRet['idreason']) && !empty($aRet['idreason'])) ? $aRet['idreason'] : 0)
                        ->setReason((!is_null($aRet['reason']) && !empty($aRet['reason'])) ? $aRet['reason'] : "")
                        ->setIdArea((!is_null($aRet['idarea']) && !empty($aRet['idarea'])) ? $aRet['idarea'] : 0)
                        ->setIdType((!is_null($aRet['idtype']) && !empty($aRet['idtype'])) ? $aRet['idtype'] : 0)
                        ->setIdItem((!is_null($aRet['iditem']) && !empty($aRet['iditem'])) ? $aRet['iditem'] : 0)
                        ->setIdService((!is_null($aRet['idservice']) && !empty($aRet['idservice'])) ? $aRet['idservice'] : 0);

            $ret = true;
            $result = array("message"=>"","object"=>$reasonModel);
        }catch(\PDOException $ex){ $msg = $ex->getMessage();
            $this->loggerDB->error('Error get reason ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * insertReason
     * 
     * en_us Saves reason's data into DB
     * pt_br Grava os dados do motivo no BD
     *
     * @param  reasonModel $reasonModel
     * @return array
     */
    public function insertReason(reasonModel $reasonModel): array
    {        
        $sql = "INSERT INTO hdk_tbcore_reason(`idservice`,`name`)
                VALUES(:service,:reason)";
                
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
            $this->loggerDB->error("Error saving new reason's data", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * updateReason
     * 
     * en_us Updates reason's data into DB
     * pt_br Atualiza os dados do motivo no BD
     *
     * @param  reasonModel $reasonModel
     * @return array
     */
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
            $this->loggerDB->error("Error updating reason's data", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * updateStatus
     * 
     * en_us Updates reason's status into DB
     * pt_br Atualiza o status do motivo no BD
     *
     * @param  reasonModel $reasonModel
     * @return array
     */
    public function updateStatus(reasonModel $reasonModel): array
    {        
        $sql = "UPDATE hdk_tbcore_reason
                   SET `status` = :newStatus
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

    /**
     * Return an array with a item's list
     *
     * @param  reasonModel $reasonModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchReasonByService(reasonModel $reasonModel): array
    {        
        $sql = "SELECT idreason, `name`, `default`
                  FROM hdk_tbcore_reason
                 WHERE idservice = :serviceID
                   AND `status` = 'A'
              ORDER BY `name` ASC";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':serviceID', $reasonModel->getIdService());
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $reasonModel->setGridList($aRet);

            $ret = true;
            $result = array("message"=>"","object"=>$reasonModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting reasons by service ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
}