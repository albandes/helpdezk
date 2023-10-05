<?php

namespace App\modules\helpdezk\dao\mysql;

use App\core\Database;
use App\modules\helpdezk\models\mysql\hdkEmailFeatureModel;

class hdkEmailFeatureDAO extends Database
{
    
    public function __construct()
    {
        parent::__construct(); 
    }
    
    /**
     * queryHdkEmailFeature
     * 
     * en_us Returns an array with the email template to display in the grid     
     * pt_br Retorna uma matriz com o template de email a ser exibido na grade
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
    public function queryHdkEmailFeature($where=null,$group=null,$order=null,$limit=null): array
    {
        
        $sql = "SELECT idconfig,`name`,`description`,idconfigcategory,session_name,field_type,`status`,smarty,`value`,allowremove
                  FROM hdk_tbconfig 
                 WHERE idconfigcategory = 3 
                $where $group $order $limit";
               // echo "{$sql}\n";
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $hdkEmailFeature = new hdkEmailFeatureModel(); 
            $hdkEmailFeature->setGridList($aRet);

            $ret = true;
            $result = array("message"=>"","object"=>$hdkEmailFeature);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting email settings list", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * countHdkEmailFeature
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
    public function countHdkEmailFeature($where=null): array
    {        
        $sql = "SELECT COUNT(idconfig) total
                  FROM hdk_tbconfig 
                 WHERE idconfigcategory = 3   
                $where";
    
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            $hdkEmailFeature = new hdkEmailFeatureModel();
            $hdkEmailFeature->setTotalRows($aRet['total']);

            $ret = true;
            $result = array("message"=>"","object"=>$hdkEmailFeature);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting email settings", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * en_us Returns email's template by a session variable
     * 
     * pt_br Retorna o template de email por uma variável de sessão
     *
     * @param  hdkEmailFeatureModel $hdkEmailFeatureModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function getEmailTemplateBySession(hdkEmailFeatureModel $hdkEmailFeatureModel): array
    {  

        $sql = "SELECT b.idtemplate, c.name, c.description, `status`
                  FROM hdk_tbconfig a, hdk_tbconfig_has_template b, hdk_tbtemplate_email c, tblocale d
                 WHERE a.idconfig = b.idconfig
                   AND b.idtemplate = c.idtemplate
                   AND d.idlocale = c.idlocale
                   AND session_name = :sessionName
                   AND UPPER(d.name) = UPPER(:localeName)";
                 
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':sessionName', $hdkEmailFeatureModel->getSessionName());
            $stmt->bindValue(':localeName', $hdkEmailFeatureModel->getLocaleName());
            $stmt->execute();
           
            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            $hdkEmailFeatureModel->setIdEmailTemplate($aRet['idtemplate'])
                                 ->setSubject($aRet['name'])
                                 ->setBody($aRet['description'])
                                 ->setStatus($aRet['status']);

            $ret = true;
            $result = array("message"=>"","object"=>$hdkEmailFeatureModel);
        }catch(\PDOException $ex){ $msg = $ex->getMessage();
            $this->loggerDB->error('Error getting email template ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
        
    }
        
    /**
     * queryEmailTemplate
     * 
     * en_us Returns an array with the email template to display in the grid     
     * pt_br Retorna uma matriz com o template de email a ser exibido na grade
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
    public function queryEmailTemplate($where=null,$group=null,$order=null,$limit=null): array
    {
        
        $sql = "SELECT b.idtemplate, c.name, c.description, `status`
                  FROM hdk_tbconfig a, hdk_tbconfig_has_template b, hdk_tbtemplate_email c
                 WHERE a.idconfig = b.idconfig
                   AND b.idtemplate = c.idtemplate
                $where $group $order $limit";
                //echo "{$sql}\n";
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $hdkEmailFeature = new hdkEmailFeatureModel(); 
            $hdkEmailFeature->setGridList($aRet);

            $ret = true;
            $result = array("message"=>"","object"=>$hdkEmailFeature);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting email template data", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * getTemplateLastId
     * 
     * en_us Returns last id in table
     * pt_br Retorna o último id na tabela
     *
     * @param  hdkEmailFeatureModel $hdkEmailFeatureModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function getTemplateLastId(hdkEmailFeatureModel $hdkEmailFeatureModel): array
    {  

        $sql = "SELECT MAX(idtemplate) lastid FROM hdk_tbtemplate_email";
                 
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
        $hdkEmailFeatureModel->setLastId((!is_null($aRet['lastid']) && !empty($aRet['lastid'])) ? $aRet['lastid'] : 0);

        $ret = true;
        $result = array("message"=>"","object"=>$hdkEmailFeatureModel);

        return array("status"=>$ret,"push"=>$result);
        
    }
    
    /**
     * insertEmailTemplate
     * 
     * en_us Saves email template in hdk_tbtemplate_email table
     * pt_br Grava o template do email na tabela hdk_tbtemplate_email
     *
     * @param  hdkEmailFeatureModel $hdkEmailFeatureModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function insertEmailTemplate(hdkEmailFeatureModel $hdkEmailFeatureModel): array
    {        
        $sql = "INSERT INTO hdk_tbtemplate_email (idtemplate,idlocale,`name`,`description`)
                     VALUES (:templateId,:localeId,:subject,:body)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":templateId",$hdkEmailFeatureModel->getIdEmailTemplate());
        $stmt->bindValue(":localeId",$hdkEmailFeatureModel->getIdLocale());
        $stmt->bindValue(":subject",$hdkEmailFeatureModel->getSubject());
        $stmt->bindValue(":body",$hdkEmailFeatureModel->getBody());
        $stmt->execute();

        $ret = true;
        $result = array("message"=>"","object"=>$hdkEmailFeatureModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * insertLinkSettingTemplate
     * 
     * en_us Saves email template in hdk_tbtemplate_email table
     * pt_br Grava o template do email na tabela hdk_tbtemplate_email
     *
     * @param  hdkEmailFeatureModel $hdkEmailFeatureModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function insertLinkSettingTemplate(hdkEmailFeatureModel $hdkEmailFeatureModel): array
    {        
        $sql = "INSERT INTO hdk_tbconfig_has_template (idconfig,idtemplate) VALUES (:featureId,:templateId)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":featureId",$hdkEmailFeatureModel->getFeatureId());
        $stmt->bindValue(":templateId",$hdkEmailFeatureModel->getIdEmailTemplate());
        $stmt->execute();

        $ret = true;
        $result = array("message"=>"","object"=>$hdkEmailFeatureModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * saveEmailConfig
     * 
     * en_us Saves email template data into DB
     * pt_br Grava os dados do template do email no BD
     *
     * @param  hdkEmailFeatureModel $hdkEmailFeatureModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function saveEmailConfig(hdkEmailFeatureModel $hdkEmailFeatureModel): array
    {   
        try{
            $retLastId = $this->getTemplateLastId($hdkEmailFeatureModel);
            if($retLastId['status']){
                $hdkEmailFeatureModel->setIdEmailTemplate(($retLastId['push']['object']->getLastId() + 1));
            }

            $this->db->beginTransaction();            

            $ins = $this->insertEmailTemplate($hdkEmailFeatureModel);

            if($ins['status']){
                //insert link between email config and template
                $insLink = $this->insertLinkSettingTemplate($ins['push']['object']);
            }
            
            $ret = true;
            $result = array("message"=>"","object"=>$ins['push']['object']);
            $this->db->commit();
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error trying save email config template', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
            $this->db->rollBack();
        }         
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * getEmailTemplateBySettingId
     * 
     * en_us Returns email template data by setting's id
     * pt_br Retorna dados do template de e-mail pelo ID da notificação
     *
     * @param  hdkEmailFeatureModel $hdkEmailFeatureModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function getEmailTemplateBySettingId(hdkEmailFeatureModel $hdkEmailFeatureModel): array
    {  

        $sql = "SELECT b.idtemplate, a.name setting_name, c.name, c.description, `status`, a.smarty setting_key_lang, c.idlocale
                  FROM hdk_tbconfig a, hdk_tbconfig_has_template b, hdk_tbtemplate_email c, tblocale d
                 WHERE a.idconfig = b.idconfig
                   AND b.idtemplate = c.idtemplate
                   AND d.idlocale = c.idlocale
                   AND a.idconfig = :settingId";
                 
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':settingId', $hdkEmailFeatureModel->getFeatureId());
            $stmt->execute();
           
            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            $hdkEmailFeatureModel->setIdEmailTemplate((!is_null($aRet['idtemplate']) && !empty($aRet['idtemplate'])) ? $aRet['idtemplate'] : 0)
                                 ->setSubject((!is_null($aRet['name']) && !empty($aRet['name'])) ? $aRet['name'] : "")
                                 ->setBody((!is_null($aRet['description']) && !empty($aRet['description'])) ? $aRet['description'] : "")
                                 ->setStatus((!is_null($aRet['status']) && !empty($aRet['status'])) ? $aRet['status'] : "")
                                 ->setSettingName((!is_null($aRet['setting_name']) && !empty($aRet['setting_name'])) ? $aRet['setting_name'] : "")
                                 ->setSettingKeyLang((!is_null($aRet['setting_key_lang']) && !empty($aRet['setting_key_lang'])) ? $aRet['setting_key_lang'] : "")
                                 ->setIdLocale((!is_null($aRet['idlocale']) && !empty($aRet['idlocale'])) ? $aRet['idlocale'] : 0);

            $ret = true;
            $result = array("message"=>"","object"=>$hdkEmailFeatureModel);
        }catch(\PDOException $ex){ $msg = $ex->getMessage();
            $this->loggerDB->error('Error getting email template ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
        
    }
    
    /**
     * updateEmailConfig
     * 
     * en_us Updates email template data
     * pt_br Atualiza os dados do template de e-mail
     *
     * @param  hdkEmailFeatureModel $hdkEmailFeatureModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function updateEmailConfig(hdkEmailFeatureModel $hdkEmailFeatureModel): array
    {
        $sql = "UPDATE hdk_tbtemplate_email SET idlocale = :localeId, `name` = :subject, `description` = :body WHERE idtemplate = :templateId";
                 
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":templateId",$hdkEmailFeatureModel->getIdEmailTemplate());
            $stmt->bindValue(":localeId",$hdkEmailFeatureModel->getIdLocale());
            $stmt->bindValue(":subject",$hdkEmailFeatureModel->getSubject());
            $stmt->bindValue(":body",$hdkEmailFeatureModel->getBody());
            $stmt->execute();

            $ret = true;
            $result = array("message"=>"","object"=>$hdkEmailFeatureModel);
        }catch(\PDOException $ex){ $msg = $ex->getMessage();
            $this->loggerDB->error('Error updating email template data', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);        
    }
    
    /**
     * updateEmailConfigStatus
     * 
     * en_us Updates email template status
     * pt_br Atualiza o ststus do template de e-mail
     *
     * @param  hdkEmailFeatureModel $hdkEmailFeatureModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function updateEmailConfigStatus(hdkEmailFeatureModel $hdkEmailFeatureModel): array
    {
        $sql = "UPDATE hdk_tbconfig SET `status` = :status WHERE idconfig = :settingId";
                 
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":settingId",$hdkEmailFeatureModel->getFeatureId());
            $stmt->bindValue(":status",$hdkEmailFeatureModel->getStatus());
            $stmt->execute();

            $ret = true;
            $result = array("message"=>"","object"=>$hdkEmailFeatureModel);
        }catch(\PDOException $ex){ $msg = $ex->getMessage();
            $this->loggerDB->error('Error updating email config status', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);        
    }

}