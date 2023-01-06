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
     * en_us Returns an array with the email template to display in the grid
     * 
     * pt_br Retorna uma matriz com o modelo de email a ser exibido na grade
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
        
        $sql = "SELECT a.idhdkEmailFeature, b.name company, a.status, a.name AS hdkEmailFeature, 
                    a.idperson AS idcompany
                FROM hdk_tbhdkEmailFeature a, tbperson b 
                WHERE a.idperson = b.idperson 
                $where $group $order $limit";
               // echo "{$sql}\n";
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $hdkEmailFeature = new hdkEmailFeatureModel(); 
            $hdkEmailFeature->setgridList($aRet);

            $ret = true;
            $result = array("message"=>"","object"=>$hdkEmailFeature);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error query hdkEmailFeature ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * en_us Counts the rows returned in the query
     * 
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
        $sql = "SELECT COUNT(idhdkEmailFeature) total
                FROM hdk_tbhdkEmailFeature a, tbperson b 
                WHERE a.idperson = b.idperson  
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
            $this->loggerDB->error("Error counting hdkEmailFeature ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * en_us Returns email's template by a session variable
     * 
     * pt_br Retorna o modelo de email por uma variável de sessão
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

}