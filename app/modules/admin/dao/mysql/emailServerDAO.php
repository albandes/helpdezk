<?php

namespace App\modules\admin\dao\mysql;

use App\core\Database;
use App\modules\admin\models\mysql\emailServerModel;

class emailServerDAO extends Database
{
    
    public function __construct()
    {
        parent::__construct(); 
    }
    
    /**
     * Return an array with emailServers to display in grid
     *
     * @param  string $where
     * @param  string $group
     * @param  string $order
     * @param  string $limit
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function queryEmailServers($where=null,$group=null,$order=null,$limit=null): array
    {
        
        $sql = "SELECT idemailserver,a.idservertype,b.name servertype,a.name servername,user,`password`,
                        port,apikey,apisecret,apiendpoint,a.status,`default`
                  FROM tbemailserver a
                  JOIN tbservertype b
                    ON b.idservertype = a.idservertype 
                $where $group $order $limit";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $emailServer = new emailServerModel(); 
            $emailServer->setGridList($aRet);

            $ret = true;
            $result = array("message"=>"","object"=>$emailServer);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting emailServers ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * Return an array with rows total for grid pagination 
     *
     * @param  string $where
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function countEmailServers($where=null): array
    {
        
        $sql = "SELECT COUNT(idemailserver) total
                  FROM tbemailserver a
                  JOIN tbservertype b
                    ON b.idservertype = a.idservertype 
                $where";
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            $emailServer = new emailServerModel();
            $emailServer->setTotalRows($aRet['total']);

            $ret = true;
            $result = array("message"=>"","object"=>$emailServer);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error counting email servers ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * Inserts email data to send in DB
     *
     * @param  mixed $emailSrvModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function insertEmailCron(emailServerModel $emailServerModel): array
    {        
        $sql = "INSERT INTO tbemailcron(idemailserver,idmodule,code,date_in,send,tag)
                     VALUES (:emailSrvID,:moduleID,:code,NOW(),0,:tag)";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':emailSrvID', $emailServerModel->getIdEmailServer());
            $stmt->bindValue(':moduleID', $emailServerModel->getIdModule());
            $stmt->bindValue(':code', $emailServerModel->getCode());
            $stmt->bindValue(':tag', $emailServerModel->getTag());
            $stmt->execute();

            $emailServerModel->setIdEmailCron($this->db->lastInsertId());
            $ret = true;
            $result = array("message"=>"","object"=>$emailServerModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error insert email cron", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * Returns email template data
     *
     * @param  mixed $emailSrvModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function getEmailTemplate(emailServerModel $emailServerModel): array
    {        
        $prefix = $emailServerModel->getTablePrefix();

        $sql = "SELECT c.idtemplate, c.name template_name, c.description template_body
                  FROM {$prefix}_tbconfig a, {$prefix}_tbconfig_has_template b, {$prefix}_tbtemplate_email c,
                        tblocale d
                 WHERE a.idconfig = b.idconfig
                   AND b.idtemplate = c.idtemplate
                   AND c.idlocale = d.idlocale
                   AND d.name = ''
                   AND session_name = :sessionName";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':sessionName', $emailServerModel->getSessionName());
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            $emailServerModel->setIdEmailTemplate($aRet['idtemplate'])
                             ->setEmailTemplateSubject($aRet['template_name'])
                             ->setEmailTemplateBody($aRet['template_body']);

            $ret = true;
            $result = array("message"=>"","object"=>$emailServerModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting email template", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * en_us Returns a list of emails to send
     * pt_br Retorna uma lista de e-mails para enviar
     *
     * @param  emailServerModel $emailServerModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchEmailToSendByModule(emailServerModel $emailServerModel): array
    {        
        $sql = "SELECT idemailcron, code, date_in, date_out, send, tag
                  FROM tbemailcron
                 WHERE idmodule = :moduleID
                   AND send = 0";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':moduleID', $emailServerModel->getIdModule());
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $emailServerModel->setGridList((!is_null($aRet) && !empty($aRet)) ? $aRet :  array());

            $ret = true;
            $result = array("message"=>"","object"=>$emailServerModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting emails to send", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * updateEmailCronStatus
     *
     * @param  emailServerModel $emailServerModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function updateEmailCronStatus(emailServerModel $emailServerModel): array
    {        
        $sql = "UPDATE tbemailcron
                   SET date_out = NOW(), 
                       send = 1
                 WHERE idemailcron = :emailCronId";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':emailCronId', $emailServerModel->getIdEmailCron());
            $stmt->execute();

            $ret = true;
            $result = array("message"=>"","object"=>$emailServerModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error updating emails to send", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * getEmailServer
     *
     * @param  emailServerModel $emailServerModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function getEmailServer(emailServerModel $emailServerModel): array
    {        
        $sql = "SELECT a.idservertype,b.name server_type,a.name,`user`,`password`,`port`,apikey,apisecret,apiendpoint,a.status,`default`
                  FROM tbemailserver a, tbservertype b
                 WHERE a.idservertype = b.idservertype
                   AND idemailserver = :emailServerId";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':emailServerId', $emailServerModel->getIdEmailServer());
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            $emailServerModel->setIdServerType((!is_null($aRet['idservertype']) && !empty($aRet['idservertype'])) ? $aRet['idservertype'] : 0)
                             ->setServerType((!is_null($aRet['server_type']) && !empty($aRet['server_type'])) ? $aRet['server_type'] : "")
                             ->setName((!is_null($aRet['name']) && !empty($aRet['name'])) ? $aRet['name'] : "")
                             ->setUser((!is_null($aRet['user']) && !empty($aRet['user'])) ? $aRet['user'] : "")
                             ->setPassword((!is_null($aRet['password']) && !empty($aRet['password'])) ? $aRet['password'] : "")
                             ->setPort((!is_null($aRet['port']) && !empty($aRet['port'])) ? $aRet['port'] : "")
                             ->setApiKey((!is_null($aRet['apikey']) && !empty($aRet['apikey'])) ? $aRet['apikey'] : "")
                             ->setApiSecret((!is_null($aRet['apisecret']) && !empty($aRet['apisecret'])) ? $aRet['apisecret'] : "")
                             ->setApiEndpoint((!is_null($aRet['apiendpoint']) && !empty($aRet['apiendpoint'])) ? $aRet['apiendpoint'] : "")
                             ->setStatus((!is_null($aRet['status']) && !empty($aRet['status'])) ? $aRet['status'] : "")
                             ->setDefault((!is_null($aRet['default']) && !empty($aRet['default'])) ? $aRet['default'] : "");

            $ret = true;
            $result = array("message"=>"","object"=>$emailServerModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting email server data", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * fetchEmailToSend
     * 
     * en_us Returns a list of emails to send
     * pt_br Retorna uma lista de e-mails para enviar
     *
     * @param  emailServerModel $emailServerModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchEmailToSend(emailServerModel $emailServerModel): array
    {        
        $sql = "SELECT idemailcron, a.idmodule, code, date_in, date_out, send, tag, tableprefix
                  FROM tbemailcron a, tbmodule b
                 WHERE a.idmodule = b.idmodule
                   AND send = 0";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':moduleID', $emailServerModel->getIdModule());
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $emailServerModel->setGridList((!is_null($aRet) && !empty($aRet)) ? $aRet :  array());

            $ret = true;
            $result = array("message"=>"","object"=>$emailServerModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting emails to send", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

}