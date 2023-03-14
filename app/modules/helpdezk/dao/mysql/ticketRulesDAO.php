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

    /**
     * en_us Returns total ticket approvals
     * pt_br Retorna o total de aprovações de ticket
     *
     * @param  ticketRulesModel $ticketRulesModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function getTotalApprovals(ticketRulesModel $ticketRulesModel): array
    {        
        $sql = "SELECT COUNT(idrequestapproval) total
                  FROM hdk_tbrequest_approval
                 WHERE request_code = :ticketCode  
                   AND idnote IS NULL 
                   AND fl_rejected = 0";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':ticketCode', $ticketRulesModel->getTicketCode());
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);

            $ticketRulesModel->setTotalRows((!empty($aRet['total']) && !is_null($aRet['total'])) ? $aRet['total'] : 0);

            $ret = true;
            $result = array("message"=>"","object"=>$ticketRulesModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting ticket's total approvals. ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Returns next approver data
     * pt_br Retorna o seguinte aprovador
     *
     * @param  ticketRulesModel $ticketRulesModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function getNextApprover(ticketRulesModel $ticketRulesModel): array
    {
        $sql = "SELECT idperson, `order`
                  FROM hdk_tbrequest_approval
                 WHERE request_code = :ticketCode  
                   AND idnote IS NULL 
                   AND fl_rejected = 0
              ORDER BY `order` LIMIT 1";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':ticketCode', $ticketRulesModel->getTicketCode());
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);

            $ticketRulesModel->setIdPerson((!empty($aRet['idperson']) && !is_null($aRet['idperson'])) ? $aRet['idperson'] : 0)
                             ->setOrder((!empty($aRet['order']) && !is_null($aRet['order'])) ? $aRet['order'] : 0);

            $ret = true;
            $result = array("message"=>"","object"=>$ticketRulesModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting ticket's next approver. ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Returns original in charge data
     * pt_br Retorna os dados do responsável original
     *
     * @param  ticketRulesModel $ticketRulesModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function getOriginalInCharge(ticketRulesModel $ticketRulesModel): array
    {
        $sql = "SELECT id_in_charge, type 
                  FROM hdk_tbrequest_in_charge 
                 WHERE code_request = :ticketCode
              ORDER BY idrequest_in_charge LIMIT 1";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':ticketCode', $ticketRulesModel->getTicketCode());
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);

            $ticketRulesModel->setIdPerson((!empty($aRet['id_in_charge']) && !is_null($aRet['id_in_charge'])) ? $aRet['id_in_charge'] : 0)
                             ->setInChargeType((!empty($aRet['type']) && !is_null($aRet['type'])) ? $aRet['type'] : "");

            $ret = true;
            $result = array("message"=>"","object"=>$ticketRulesModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting ticket's next approver. ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

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
    public function updateApprovalNote(ticketRulesModel $ticketRulesModel): array
    {        
        $sql = "UPDATE hdk_tbrequest_approval SET idnote = :noteId WHERE request_code = :ticketCode AND idperson = :personId";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":noteId",$ticketRulesModel->getNoteId());
        $stmt->bindValue(":ticketCode",($ticketRulesModel->getTicketCode()));
        $stmt->bindValue(":personId",$ticketRulesModel->getIdPerson());
        $stmt->execute();

        $ticketRulesModel->setIdApproval($this->db->lastInsertId());

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
    public function getRecalculate(ticketRulesModel $ticketRulesModel): array
    {        
        $sql = "SELECT fl_recalculate as recalculate, a.iditem, a.idservice, a.idpriority 
                  FROM hdk_tbrequest a, hdk_tbapproval_rule b 
                 WHERE a.iditem = b.iditem 
                   AND a.idservice = b.idservice 
                   AND a.code_request = :ticketCode
                   LIMIT 0,1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":ticketCode",$ticketRulesModel->getTicketCode());
        $stmt->execute();

        $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);

        $ticketRulesModel->setIsRecalculate((!empty($aRet['recalculate']) && !is_null($aRet['recalculate'])) ? $aRet['recalculate'] : 0)
                         ->setItemId((!empty($aRet['idtem']) && !is_null($aRet['idtem'])) ? $aRet['idtem'] : 0)
                         ->setServiceId((!empty($aRet['idservice']) && !is_null($aRet['idservice'])) ? $aRet['idservice'] : 0)
                         ->setPriorityId((!empty($aRet['idpriority']) && !is_null($aRet['idpriority'])) ? $aRet['idpriority'] : 0);

        $ret = true;
        $result = array("message"=>"","object"=>$ticketRulesModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Returns ticket's previous approver
     * pt_br Retorna o aprovador anterior do ticket
     *
     * @param  ticketRulesModel $ticketRulesModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function getLastApprover(ticketRulesModel $ticketRulesModel): array
    {        
        $sql = "SELECT idperson, `order`
                  FROM hdk_tbrequest_approval
                 WHERE request_code = :ticketCode  
                   AND idnote IS NOT NULL 
              ORDER BY `order` DESC LIMIT 1";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':ticketCode', $ticketRulesModel->getTicketCode());
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);

            $ticketRulesModel->setIdPerson((!empty($aRet['idperson']) && !is_null($aRet['idperson'])) ? $aRet['idperson'] : 0)
                             ->setOrder((!empty($aRet['order']) && !is_null($aRet['order'])) ? $aRet['order'] : 0);

            $ret = true;
            $result = array("message"=>"","object"=>$ticketRulesModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting ticket's total approvals. ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Records the return to the previous phase in hdk_tbrequest_approval table
     * pt_br Grava o retorno à fase anterior na tabela hdk_tbrequest_approval
     *
     * @param  ticketRulesModel $ticketRulesModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function updateApprovalReturn(ticketRulesModel $ticketRulesModel): array
    {        
        $sql = "UPDATE hdk_tbrequest_approval SET idnote = NULL WHERE request_code = :ticketCode AND idperson = :personId AND `order` = :order";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":ticketCode",($ticketRulesModel->getTicketCode()));
        $stmt->bindValue(":personId",$ticketRulesModel->getIdPerson());
        $stmt->bindValue(":order",$ticketRulesModel->getOrder());
        $stmt->execute();

        $ticketRulesModel->setIdApproval($this->db->lastInsertId());

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
    public function updateDisapprovalNote(ticketRulesModel $ticketRulesModel): array
    {        
        $sql = "UPDATE hdk_tbrequest_approval SET idnote = :noteId, fl_rejected = 1 WHERE request_code = :ticketCode AND idperson = :personId";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":noteId",$ticketRulesModel->getNoteId());
        $stmt->bindValue(":ticketCode",($ticketRulesModel->getTicketCode()));
        $stmt->bindValue(":personId",$ticketRulesModel->getIdPerson());
        $stmt->execute();

        $ticketRulesModel->setIdApproval($this->db->lastInsertId());

        $ret = true;
        $result = array("message"=>"","object"=>$ticketRulesModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }
        
}