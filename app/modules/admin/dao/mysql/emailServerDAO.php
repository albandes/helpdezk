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
     * @return array
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
     * @return array
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
     * @return array
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
     * @return array
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
     * @return array
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

}