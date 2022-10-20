<?php

namespace App\modules\helpdezk\dao\mysql;

use App\core\Database;
use App\modules\helpdezk\models\mysql\evaluationModel;

use App\modules\helpdezk\dao\mysql\ticketDAO;
use App\modules\helpdezk\models\mysql\ticketModel;

class evaluationDAO extends Database
{
    
    public function __construct()
    {
        parent::__construct(); 
    }
    
    /**
     * Return an array with evaluation to display in grid
     *
     * @param  string $where
     * @param  string $group
     * @param  string $order
     * @param  string $limit
     * @return array  array Parameters returned in array: 
     *                      [status = true/false
     *                       push =  [message = PDO Exception message 
     *                       object = model's object]]
     */    
    public function queryEvaluations($where=null,$group=null,$order=null,$limit=null): array
    {
        
        $sql = "SELECT idevaluation, name, icon_name, status 
                  FROM hdk_tbevaluation 
                  $where $group $order $limit";
               // echo "{$sql}\n";
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $evaluationModel = new evaluationModel(); 
            $evaluationModel->setGridList($aRet);

            $ret = true;
            $result = array("message"=>"","object"=>$evaluationModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting priorities ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * Return an array with rows total for grid pagination
     *
     * @param  mixed $where
     * @return array array Parameters returned in array: 
     *                      [status = true/false
     *                       push =  [message = PDO Exception message 
     *                       object = model's object]]
     */
    public function countEvaluations($where=null): array
    {        
        $sql = "SELECT COUNT(idevaluation) total
                  FROM hdk_tbevaluation
                $where";
    
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            $evaluationModel = new evaluationModel();
            $evaluationModel->setTotalRows($aRet['total']);

            $ret = true;
            $result = array("message"=>"","object"=>$priority);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error counting priorities ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Returns a object with evaluation's questions
     * 
     * pt_br Retorna um objeto com as perguntas da avaliação
     *
     * @param  evaluationModel $evaluationModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchQuestions(evaluationModel $evaluationModel): array
    {        
        $sql = "SELECT idquestion, question FROM hdk_tbevaluationquestion WHERE status = 'A'";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $evaluationModel->setQuestionList((!empty($aRet) && count($aRet) > 0) ? $aRet : array());

            $ret = true;
            $result = array("message"=>"","object"=>$evaluationModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting evaluation's notes. ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Returns an object with answers to evaluation's questions
     * 
     * pt_br Retorna um objeto com respostas às perguntas da avaliação
     *
     * @param  evaluationModel $evaluationModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchAnswers(evaluationModel $evaluationModel): array
    {        
        $sql = "SELECT name, icon_name, idevaluation, checked 
                  FROM hdk_tbevaluation 
                 WHERE status = 'A' 
                   AND idquestion = :questionID 
              ORDER BY idevaluation ASC";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':questionID', $evaluationModel->getIdQuestion());
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $evaluationModel->setAnswerList((!empty($aRet) && count($aRet) > 0) ? $aRet : array());

            $ret = true;
            $result = array("message"=>"","object"=>$evaluationModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting evaluation's notes. ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Returns an object with answers to evaluation's questions
     * 
     * pt_br Retorna um objeto com respostas às perguntas da avaliação
     *
     * @param  evaluationModel $evaluationModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function getEvaluationToken(evaluationModel $evaluationModel): array
    {        
        $sql = "SELECT token FROM hdk_tbevaluation_token WHERE code_request = :ticketCode";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':ticketCode', $evaluationModel->getTicketCode());
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            $evaluationModel->setToken((!empty($aRet['token']) && !is_null($aRet['token'])) ? $aRet['token'] : "");

            $ret = true;
            $result = array("message"=>"","object"=>$evaluationModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting evaluation's token. ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Removes ticket service rating token from database
     * pt_br Remove o token da avaliação do atendimento da solicitação do banco de dados
     *
     * @param  evaluationModel $evaluationModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function deleteEvaluationToken(evaluationModel $evaluationModel): array
    {        
        $sql = "DELETE FROM hdk_tbevaluation_token WHERE code_request = :ticketCode";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":ticketCode",$evaluationModel->getTicketCode());
        $stmt->execute(); 

        $ret = true;
        $result = array("message"=>"","object"=>$evaluationModel);         
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Removes ticket service rating from database
     * pt_br Remove a avaliação do atendimento da solicitação do banco de dados
     *
     * @param  evaluationModel $evaluationModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function deleteTicketEvaluation(evaluationModel $evaluationModel): array
    {        
        $sql = "DELETE FROM hdk_tbrequest_evaluation WHERE code_request = :ticketCode";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":ticketCode",$evaluationModel->getTicketCode());
        $stmt->execute(); 

        $ret = true;
        $result = array("message"=>"","object"=>$evaluationModel);         
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Insert ticket service rating from database
     * pt_br Remove a avaliação do atendimento da solicitação do banco de dados
     *
     * @param  evaluationModel $evaluationModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function insertTicketEvaluation(evaluationModel $evaluationModel): array
    {        
        $sql = "INSERT INTO hdk_tbrequest_evaluation (idevaluation, code_request, `date`) VALUES (:evaluationID,:ticketCode,:date)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":evaluationID",$evaluationModel->getIdEvaluation());
        $stmt->bindValue(":ticketCode",$evaluationModel->getTicketCode());
        $stmt->bindValue(":date",$evaluationModel->getLogDate());
        $stmt->execute(); 

        $ret = true;
        $result = array("message"=>"","object"=>$evaluationModel);         
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Saves ticket's attendance evaluation into DB
     * pt_br Grava a avaliação do atendimento da solicitação no banco de dados
     *
     * @param  evaluationModel $evaluationModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function saveTicketEvaluation(evaluationModel $evaluationModel): array
    {   
        $ticketDAO = new ticketDAO();
        $ticketModel = new ticketModel();
        $ticketModel->setTicketCode($evaluationModel->getTicketCode())
                    ->setIdCreator($evaluationModel->getIdCreator())
                    ->setIdStatus($evaluationModel->getIdStatus())
                    ->setIdUserLog($evaluationModel->getIdUserLog())
                    ->setLogDate($evaluationModel->getLogDate());
        //echo "",print_r($evaluationModel,true),"\n"; die();
        $aNotes = $evaluationModel->getNoteList();
        $aAnswer = $evaluationModel->getAnswerList();
        $isApproved = $evaluationModel->getIsApproved();
        
        try{
            $this->db->beginTransaction();

            $delEvalToken = $this->deleteEvaluationToken($evaluationModel);

            if($delEvalToken['status']){
                // -- save ticket's status in log
                $updTicketLog = $ticketDAO->insertTicketLog($ticketModel);

                // -- save notes
                foreach($aNotes as $k=>$v){
                    $ticketModel->setNotePublic($v['public'])
                                ->setNoteTypeID($v['type'])
                                ->setNote($v['note'])
                                ->setNoteDateTime($v['date'])
                                ->setNoteIsOpen(0);

                    $insNote = $ticketDAO->insertTicketNote($ticketModel);
                }

                // -- update ticket's status
                $updTicketStatus = $ticketDAO->updateTicketStatus($ticketModel);

                if($isApproved != "N"){
                    // -- clear ticket's evaluation
                    $delEvaluation = $this->deleteTicketEvaluation($evaluationModel);

                    // -- save answer
                    foreach($aAnswer as $k=>$v){
                        $evaluationModel->setIdEvaluation($v);

                        $insAnswer = $this->insertTicketEvaluation($evaluationModel);
                    }

                    // -- update ticket action date
                    $ticketModel->setTicketDateField("approval_date");                    
                    $updTicketDate = $ticketDAO->updateTicketDate($ticketModel);
                }               
                
            }
            
            $ret = true;
            $result = array("message"=>"","object"=>$insTicket['push']['object']);
            $this->db->commit();
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error trying save ticket's evaluation ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
            $this->db->rollBack();
        }         
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Returns an object with answers to evaluation's questions
     * 
     * pt_br Retorna um objeto com respostas às perguntas da avaliação
     *
     * @param  evaluationModel $evaluationModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function insertEvaluationToken(evaluationModel $evaluationModel): array
    {        
        $sql = "INSERT INTO hdk_tbevaluation_token (code_request,token) values (:ticketCode,:token)";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':ticketCode', $evaluationModel->getTicketCode());
            $stmt->bindValue(':token', sha1(time() . $evaluationModel->getTicketCode()));
            $stmt->execute();

            $evaluationModel->setToken((!empty($aRet['token']) && !is_null($aRet['token'])) ? $aRet['token'] : "");

            $ret = true;
            $result = array("message"=>"","object"=>$evaluationModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error insert evaluation's token. ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
    }
    

}