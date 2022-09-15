<?php

namespace App\modules\helpdezk\dao\mysql;

use App\core\Database;

use App\modules\helpdezk\models\mysql\ticketRulesModel;

class ticketRulesDAO extends Database
{
    public function __construct()
    {
        parent::__construct(); 
    }

    /**
     * Returns a object with the total rules for item and service
     *
     * @param  ticketRulesModel $ticketRulesModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function getTotalRules(ticketRulesModel $ticketRulesModel): array
    {        
        $sql = "SELECT COUNT(idapproval) total 
                  FROM hdk_tbapproval_rule 
                 WHERE iditem = :itemID 
                   AND idservice = :serviceID";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':itemID', $ticketRulesModel->getItemId());
            $stmt->bindValue(':serviceID', $ticketRulesModel->getServiceId());
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            $ticketRulesModel->setTotalRows($aRet['total']);

            $ret = true;
            $result = array("message"=>"","object"=>$ticketRulesModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting total rules. ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * Returns a object with rules for item and service
     *
     * @param  ticketRulesModel $ticketRulesModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchRules(ticketRulesModel $ticketRulesModel): array
    {        
        $sql = "SELECT idapproval, idperson, `order`, fl_recalculate 
                  FROM hdk_tbapproval_rule 
                 WHERE iditem = :itemID 
                   AND idservice = :serviceID";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':itemID', $ticketRulesModel->getItemId());
            $stmt->bindValue(':serviceID', $ticketRulesModel->getServiceId());
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $ticketRulesModel->setGridList($aRet);

            $ret = true;
            $result = array("message"=>"","object"=>$ticketRulesModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting rules. ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * Returns a object with rules for item and service
     *
     * @param  ticketRulesModel $ticketRulesModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function insertApproval(ticketRulesModel $ticketRulesModel): array
    {        
        $sql = "INSERT INTO hdk_tbrequest_approval (idapproval,request_code,`order`,idperson,fl_recalculate)
                     VALUES (:approvalID,:ticketCode,:order,:personID,:isRecalculate)";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':approvalID', $ticketRulesModel->getIdApproval());
            $stmt->bindValue(':ticketCode', $ticketRulesModel->getTicketCode());
            $stmt->bindValue(':order', $ticketRulesModel->getOrder());
            $stmt->bindValue(':personID', $ticketRulesModel->getIdPerson());
            $stmt->bindValue(':isRecalculate', $ticketRulesModel->getIsRecalculate());
            $stmt->execute();

            $ticketRulesModel->setIdTicketApproval($this->db->lastInsertId());

            $ret = true;
            $result = array("message"=>"","object"=>$ticketRulesModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error save approval rules. ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
    }
        
}