<?php

namespace App\modules\helpdezk\dao\mysql;

use App\core\Database;
use App\modules\helpdezk\models\mysql\warningModel;

class warningDAO extends Database
{
    
    public function __construct()
    {
        parent::__construct(); 
    }
    
    /**
     * queryWarnings
     * 
     * en_us Returns an array with the warnings list to display in the grid     
     * pt_br Retorna um array com a lista de avisos para exibir na grade
     *
     * @param  string $where WHERE statement
     * @param  string $group GROUP statement
     * @param  string $order ORDER statement
     * @param  string $limit LIMIT statement
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function queryWarnings($where=null,$group=null,$order=null,$limit=null): array
    {
        
        $sql = "SELECT a.idmessage, b.idtopic,  b.title as title_topic, a.title as title_warning, a.description, a.dtcreate, a.dtstart, a.dtend, a.showin, a.sendemail,
                        (select count(*) from bbd_topic_company WHERE idtopic = a.idtopic) + (select count(*) from bbd_topic_group WHERE idtopic = a.idtopic) as total
                  FROM bbd_tbmessage a, bbd_topic b
                 WHERE a.idtopic = b.idtopic
                $where $group $order $limit";
                //echo "{$sql}\n";
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $warningDTO = new warningModel(); 
            $warningDTO->setGridList($aRet);

            $ret = true;
            $result = array("message"=>"","object"=>$warningDTO);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting warnings list", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * countWarnings
     * 
     * en_us Counts the rows returned in the query
     * pt_br Conta as linhas retornadas na consulta
     *
     * @param  string $where WHERE statement
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function countWarnings($where=null): array
    {        
        $sql = "SELECT COUNT(a.idmessage) total
                  FROM bbd_tbmessage a, bbd_topic b
                 WHERE a.idtopic = b.idtopic
                $where";
    
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            $warningDTO = new warningModel();
            $warningDTO->setTotalRows($aRet['total']);

            $ret = true;
            $result = array("message"=>"","object"=>$warningDTO);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error counting warnings", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
        
    /**
     * queryTopics
     * 
     * en_us Returns an array with the topics list to display in the grid     
     * pt_br Retorna um array com a lista dos tópicos para exibir na grade
     *
     * @param  string $where WHERE statement
     * @param  string $group GROUP statement
     * @param  string $order ORDER statement
     * @param  string $limit LIMIT statement
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function queryTopics($where=null,$group=null,$order=null,$limit=null): array
    {
        
        $sql = "SELECT idtopic, title, default_display, fl_emailsent
                  FROM bbd_topic
                $where $group $order $limit";
                //echo "{$sql}\n";
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $warningDTO = new warningModel(); 
            $warningDTO->setGridList($aRet);

            $ret = true;
            $result = array("message"=>"","object"=>$warningDTO);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting topics data", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * countTopics
     * 
     * en_us Counts the rows returned in the query
     * pt_br Conta as linhas retornadas na consulta
     *
     * @param  string $where WHERE statement
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function countTopics($where=null): array
    {        
        $sql = "SELECT COUNT(idtopic) total
                  FROM bbd_topic
                $where";
    
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            $warningDTO = new warningModel();
            $warningDTO->setTotalRows($aRet['total']);

            $ret = true;
            $result = array("message"=>"","object"=>$warningDTO);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error counting topics", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * insertWarning
     * 
     * en_us Saves warning's data in bbd_tbmessage table
     * pt_br Grava os dados do aviso na tabela bbd_tbmessage
     *
     * @param  warningModel $warningDTO
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function insertWarning(warningModel $warningDTO): array
    {        
        $sql = "INSERT INTO bbd_tbmessage (idtopic, idperson, title, `description`, dtcreate, dtstart, dtend, sendemail, showin, emailsent)
                     VALUES (:topicId,:userId,:title,:description,NOW(),:startDate,:endDate,:flgSendEmail,:showIn,:flgEmailSent)";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":topicId",$warningDTO->getTopicId());
            $stmt->bindValue(":userId",$warningDTO->getUserId());
            $stmt->bindValue(":title",$warningDTO->getWarningTitle());
            $stmt->bindValue(":description",$warningDTO->getWarningDescription());
            $stmt->bindValue(":startDate",$warningDTO->getStartDate());
            $stmt->bindValue(":endDate",$warningDTO->getEndDate());
            $stmt->bindValue(":flgSendEmail",$warningDTO->getFlagSendEmail());
            $stmt->bindValue(":showIn",$warningDTO->getShowIn());
            $stmt->bindValue(":flgEmailSent",$warningDTO->getFlagEmailSent());
            $stmt->execute();

            $warningDTO->setWarningId($this->db->lastInsertId());

            $ret = true;
            $result = array("message"=>"","object"=>$warningDTO);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error saving warning's data.", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * getWarning
     * 
     * en_us Returns warning's data
     * pt_br Retorna os dados do aviso
     *
     * @param  warningModel $warningDTO
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function getWarning(warningModel $warningDTO): array
    {  

        $sql = "SELECT a.idmessage, b.idtopic,  b.title as title_topic, a.title as title_warning, a.description, a.dtcreate, a.dtstart, a.dtend, a.showin, a.sendemail, a.emailsent
                  FROM bbd_tbmessage a, bbd_topic b
                 WHERE a.idtopic = b.idtopic
                   AND a.idmessage = :warningId";
                 
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':warningId', $warningDTO->getWarningId());
            $stmt->execute();
            
            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            $warningDTO->setTopicId((!is_null($aRet['idtopic']) && !empty($aRet['idtopic'])) ? $aRet['idtopic'] : 0)
                       ->setTopicTitle((!is_null($aRet['title_topic']) && !empty($aRet['title_topic'])) ? $aRet['title_topic'] : "")
                       ->setWarningTitle((!is_null($aRet['title_warning']) && !empty($aRet['title_warning'])) ? $aRet['title_warning'] : "")
                       ->setWarningDescription((!is_null($aRet['description']) && !empty($aRet['description'])) ? $aRet['description'] : "")
                       ->setStartDate((!is_null($aRet['dtstart']) && !empty($aRet['dtstart'])) ? $aRet['dtstart'] : "")
                       ->setEndDate((!is_null($aRet['dtend']) && !empty($aRet['dtend'])) ? $aRet['dtend'] : "")
                       ->setFlagSendEmail((!is_null($aRet['sendemail']) && !empty($aRet['sendemail'])) ? $aRet['sendemail'] : "")
                       ->setShowIn((!is_null($aRet['showin']) && !empty($aRet['showin'])) ? $aRet['showin'] : "")
                       ->setFlagEmailSent((!is_null($aRet['emailsent']) && !empty($aRet['emailsent'])) ? $aRet['emailsent'] : 0);
            
            $ret = true;
            $result = array("message"=>"","object"=>$warningDTO);
        }catch(\PDOException $ex){ $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting warning's data.", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
        
    }
    
    /**
     * updateWarning
     * 
     * en_us Saves warning's data in bbd_tbmessage table
     * pt_br Grava os dados do aviso na tabela bbd_tbmessage
     *
     * @param  warningModel $warningDTO
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function updateWarning(warningModel $warningDTO): array
    {        
        $sql = "UPDATE bbd_tbmessage 
                   SET idtopic = :topicId, 
                       title = :title, 
                       `description` = :description, 
                       dtstart = :startDate, 
                       dtend = :endDate, 
                       sendemail = :flgSendEmail, 
                       showin = :showIn
                 WHERE idmessage = :messageId";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":topicId",$warningDTO->getTopicId());
            $stmt->bindValue(":title",$warningDTO->getWarningTitle());
            $stmt->bindValue(":description",$warningDTO->getWarningDescription());
            $stmt->bindValue(":startDate",$warningDTO->getStartDate());
            $stmt->bindValue(":endDate",$warningDTO->getEndDate());
            $stmt->bindValue(":flgSendEmail",$warningDTO->getFlagSendEmail());
            $stmt->bindValue(":showIn",$warningDTO->getShowIn());
            $stmt->bindValue(":messageId",$warningDTO->getWarningId());
            $stmt->execute();

            $ret = true;
            $result = array("message"=>"","object"=>$warningDTO);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error updating warning's data.", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * insertTopic
     * 
     * en_us Saves topic's data into bbd_topic table
     * pt_br Grava os dados do tópico na tabela bbd_topic
     *
     * @param  mixed $warningDTO
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function insertTopic(warningModel $warningDTO): array
    {
        $sql = "INSERT INTO bbd_topic (title, default_display, fl_emailsent) VALUES (:title,:defaultDisplay,:flgSendEmail)";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":title",$warningDTO->getTopicTitle());
        $stmt->bindValue(":defaultDisplay",$warningDTO->getTopicValidity());
        $stmt->bindValue(":flgSendEmail",$warningDTO->getTopicFlagSendEmail());
        $stmt->execute();

        $warningDTO->setTopicId($this->db->lastInsertId());

        $ret = true;
        $result = array("message"=>"","object"=>$warningDTO);

        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * insertTopicGroup
     * 
     * en_us Saves link between topic and group
     * pt_br Grava o vínculo entre o tópico e o grupo
     *
     * @param  mixed $warningDTO
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function insertTopicGroup(warningModel $warningDTO): array
    {
        $sql = "INSERT INTO bbd_topic_group (idtopic,idgroup) VALUES (:topicId,:groupId)";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":topicId",$warningDTO->getTopicId());
        $stmt->bindValue(":groupId",$warningDTO->getGroupId());
        $stmt->execute();
        

        $ret = true;
        $result = array("message"=>"","object"=>$warningDTO);

        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * insertTopicCompany
     * 
     * en_us Saves link between topic and company
     * pt_br Grava o vínculo entre o tópico e a empresa
     *
     * @param  mixed $warningDTO
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function insertTopicCompany(warningModel $warningDTO): array
    {
        $sql = "INSERT INTO bbd_topic_company (idtopic,idcompany) VALUES (:topicId,:companyId)";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":topicId",$warningDTO->getTopicId());
        $stmt->bindValue(":companyId",$warningDTO->getCompanyId());
        $stmt->execute();
        

        $ret = true;
        $result = array("message"=>"","object"=>$warningDTO);

        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * saveTopic
     * 
     * en_us Saves topic's data into DB
     * pt_br Grava os dados do tópico no BD
     *
     * @param  warningModel $warningDTO
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function saveTopic(warningModel $warningDTO): array
    {
        $aGroups = $warningDTO->getTopicGroups();
        $aCompanies = $warningDTO->getTopicCompanies();
                 
        try{
            $this->db->beginTransaction();
            
            $ins = $this->insertTopic($warningDTO);
            if($ins['status']){
                // -- saves groups linked to topic
                if(count($aGroups) > 0){
                    foreach($aGroups as $key=>$val){
                        $ins['push']['object']->setGroupId($val);
                        $this->insertTopicGroup($ins['push']['object']);
                    }
                }

                // -- saves companies linked to topic
                if(count($aCompanies) > 0){
                    foreach($aCompanies as $key=>$val){
                        $ins['push']['object']->setCompanyId($val);
                        $this->insertTopicCompany($ins['push']['object']);
                    }
                }
            }

            $ret = true;
            $result = array("message"=>"","object"=>$ins['push']['object']);
            $this->db->commit();
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error saving topic's data", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
            $this->db->rollBack();
        }

        return array("status"=>$ret,"push"=>$result);        
    }

    /**
     * getTopic
     * 
     * en_us Returns warning's data
     * pt_br Retorna os dados do aviso
     *
     * @param  warningModel $warningDTO
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function getTopic(warningModel $warningDTO): array
    {
        $sql = "SELECT title, default_display, fl_emailsent,
                       GROUP_CONCAT(idcompany) companies_id, GROUP_CONCAT(idgroup) groups_id
                  FROM bbd_topic a
       LEFT OUTER JOIN bbd_topic_company b
                    ON b.idtopic = a.idtopic
       LEFT OUTER JOIN bbd_topic_group c
                    ON c.idtopic = a.idtopic
                 WHERE a.idtopic = :topicId
              GROUP BY a.idtopic";
                 
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':topicId', $warningDTO->getTopicId());
            $stmt->execute();
            
            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            $warningDTO->setTopicTitle((!is_null($aRet['title']) && !empty($aRet['title'])) ? $aRet['title'] : "")
                       ->setTopicValidity((!is_null($aRet['default_display']) && !empty($aRet['default_display'])) ? $aRet['default_display'] : "")
                       ->setTopicFlagSendEmail((!is_null($aRet['fl_emailsent']) && !empty($aRet['fl_emailsent'])) ? $aRet['fl_emailsent'] : "")
                       ->setTopicCompanies((!is_null($aRet['companies_id']) && !empty($aRet['companies_id'])) ? explode(",",$aRet['companies_id']) : array())
                       ->setTopicGroups((!is_null($aRet['groups_id']) && !empty($aRet['groups_id'])) ? explode(",",$aRet['groups_id']) : array());
            
            $ret = true;
            $result = array("message"=>"","object"=>$warningDTO);
        }catch(\PDOException $ex){ $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting topic's data.", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
        
    }

    /**
     * updateTopic
     * 
     * en_us Updates topic's data into bbd_topic table
     * pt_br Atualiza os dados do tópico na tabela bbd_topic
     *
     * @param  mixed $warningDTO
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function updateTopic(warningModel $warningDTO): array
    {
        $sql = "UPDATE bbd_topic 
                   SET title = :title, default_display = :defaultDisplay, fl_emailsent = :flgSendEmail
                 WHERE idtopic = :topicId";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":title",$warningDTO->getTopicTitle());
        $stmt->bindValue(":defaultDisplay",$warningDTO->getTopicValidity());
        $stmt->bindValue(":flgSendEmail",$warningDTO->getTopicFlagSendEmail());
        $stmt->bindValue(":topicId",$warningDTO->getTopicId());
        $stmt->execute();

        $ret = true;
        $result = array("message"=>"","object"=>$warningDTO);

        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * deleteTopicGroups
     * 
     * en_us Updates topic's data into bbd_topic table
     * pt_br Atualiza os dados do tópico na tabela bbd_topic
     *
     * @param  mixed $warningDTO
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function deleteTopicGroups(warningModel $warningDTO): array
    {
        $sql = "DELETE FROM bbd_topic_group WHERE idtopic = :topicId";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":topicId",$warningDTO->getTopicId());
        $stmt->execute();

        $ret = true;
        $result = array("message"=>"","object"=>$warningDTO);

        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * deleteTopicCompanies
     * 
     * en_us Updates topic's data into bbd_topic table
     * pt_br Atualiza os dados do tópico na tabela bbd_topic
     *
     * @param  mixed $warningDTO
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function deleteTopicCompanies(warningModel $warningDTO): array
    {
        $sql = "DELETE FROM bbd_topic_company WHERE idtopic = :topicId";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":topicId",$warningDTO->getTopicId());
        $stmt->execute();

        $ret = true;
        $result = array("message"=>"","object"=>$warningDTO);

        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * saveUpdateTopic
     * 
     * en_us Updates topic's data into DB
     * pt_br Atualiza os dados do tópico no BD
     *
     * @param  warningModel $warningDTO
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function saveUpdateTopic(warningModel $warningDTO): array
    {
        $aGroups = $warningDTO->getTopicGroups();
        $aCompanies = $warningDTO->getTopicCompanies();
                 
        try{
            $this->db->beginTransaction();
            
            $ins = $this->updateTopic($warningDTO);
            if($ins['status']){
                // -- deletes groups linked to topic
                $this->deleteTopicGroups($ins['push']['object']);

                // -- deletes companies linked to topic
                $this->deleteTopicCompanies($ins['push']['object']);

                // -- saves groups linked to topic
                if(count($aGroups) > 0){
                    foreach($aGroups as $key=>$val){
                        $ins['push']['object']->setGroupId($val);
                        $this->insertTopicGroup($ins['push']['object']);
                    }
                }

                // -- saves companies linked to topic
                if(count($aCompanies) > 0){
                    foreach($aCompanies as $key=>$val){
                        $ins['push']['object']->setCompanyId($val);
                        $this->insertTopicCompany($ins['push']['object']);
                    }
                }
            }

            $ret = true;
            $result = array("message"=>"","object"=>$ins['push']['object']);
            $this->db->commit();
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error updating topic's data", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
            $this->db->rollBack();
        }

        return array("status"=>$ret,"push"=>$result);        
    }
}