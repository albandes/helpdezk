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

    /**
     * en_us Returns an object with approvers for item and service
     * pt_br Retorna um objeto com aprovadores para o item e serviço
     *
     * @param  ticketRulesModel $ticketRulesModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchApprovers(ticketRulesModel $ticketRulesModel): array
    {        
        $sql = "SELECT a.idperson, b.name, `order`, fl_recalculate 
                  FROM hdk_tbapproval_rule a, tbperson b
                 WHERE a.idperson = b.idperson 
                   AND iditem = :itemID 
                   AND idservice = :serviceID
              ORDER BY `order`";
        
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
     * en_us Deletes approval rules from hdk_tbapproval_rule table
     * pt_br Exclui regras de aprovação da tabela hdk_tbapproval_rule
     *
     * @param  ticketRulesModel $ticketRulesModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function deleteRules(ticketRulesModel $ticketRulesModel): array
    {        
        $sql = "DELETE FROM hdk_tbapproval_rule WHERE idservice = :serviceId AND iditem = :itemId";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":serviceId",$ticketRulesModel->getServiceId());
        $stmt->bindValue(":itemId",$ticketRulesModel->getItemId());
        $stmt->execute();

        $ret = true;
        $result = array("message"=>"","object"=>$ticketRulesModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Inserts approval rule data into hdk_tbapproval_rule table
     * pt_br Insere os dados do módulo na tabela hdk_tbapproval_rule
     *
     * @param  ticketRulesModel $ticketRulesModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function insertRule(ticketRulesModel $ticketRulesModel): array
    {        
        $sql = "INSERT INTO hdk_tbapproval_rule (iditem,idservice,idperson,`order`,fl_recalculate) 
                     VALUES (:itemId,:serviceId,:personId,:order,:flgRecalculate)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":itemId",$ticketRulesModel->getItemId());
        $stmt->bindValue(":serviceId",$ticketRulesModel->getServiceId());
        $stmt->bindValue(":personId",$ticketRulesModel->getIdPerson());
        $stmt->bindValue(":order",$ticketRulesModel->getOrder());
        $stmt->bindValue(":flgRecalculate",$ticketRulesModel->getIsRecalculate());
        $stmt->execute();

        $ticketRulesModel->setIdApproval($this->db->lastInsertId());

        $ret = true;
        $result = array("message"=>"","object"=>$ticketRulesModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Saves the new approval rule into DB
     * pt_br Grava a nova regra de aprovação no banco de dados
     *
     * @param  ticketRulesModel $ticketRulesModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function saveApprovalRule(ticketRulesModel $ticketRulesModel): array
    {   
        $aApprover = $ticketRulesModel->getApproverList();
        
        try{
            $this->db->beginTransaction();

            $delRules = $this->deleteRules($ticketRulesModel);

            if($delRules['status'] && sizeof($aApprover) > 0){
                $i = 1;
                foreach($aApprover as $k=>$v){
                    $delRules['push']['object']->setIdPerson($v)
                                               ->setOrder($i);

                    $this->insertRule($delRules['push']['object']);
                    $i++;
                }
            }
            
            $ret = true;
            $result = array("message"=>"","object"=>$delRules['push']['object']);
            $this->db->commit();
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error trying save approval rules ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
            $this->db->rollBack();
        }         
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Returns a list of all approval rules
     * pt_br Retorna uma lista de todas as regras de aprovação
     *
     * @param  ticketRulesModel $ticketRulesModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchAllRules(ticketRulesModel $ticketRulesModel): array
    {        
        $sql = "SELECT idapproval, e.name area_name, d.name type_name, c.name item_name, b.name service_name, 
                        a.idperson, f.name approver, `order`, fl_recalculate 
                  FROM hdk_tbapproval_rule a, hdk_tbcore_service b, hdk_tbcore_item c, hdk_tbcore_type d, hdk_tbcore_area e, tbperson f
                 WHERE (a.iditem = b.iditem AND a.idservice = b.idservice)
                   AND b.iditem = c.iditem
                   AND c.idtype = d.idtype
                   AND d.idarea = e.idarea
                   AND a.idperson = f.idperson
              ORDER BY area_name,type_name,item_name,service_name,`order`";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $ticketRulesModel->setGridList($aRet);

            $ret = true;
            $result = array("message"=>"","object"=>$ticketRulesModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting approval rules. ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
    }
        
}