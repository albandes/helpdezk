<?php

namespace App\modules\helpdezk\dao\mysql;

use App\core\Database;
use App\modules\helpdezk\models\mysql\hdkRequestEmailModel;

use App\modules\admin\dao\mysql\personDAO;
use App\modules\admin\models\mysql\personModel;

class hdkRequestEmailDAO extends Database
{
    
    public function __construct()
    {
        parent::__construct(); 
    }
    
    /**
     * en_us Returns an array with request by email data to display in grid
     * pt_br Retorna um array com os dados das configurações de solicitações por e-mail para visualização no grid
     *
     * @param  string $where
     * @param  string $group
     * @param  string $order
     * @param  string $limit
     * @return array            Parameters returned in array: 
     *                          [status = true/false
     *                           push =  [message = PDO Exception message 
     *                                    object = model's object]]
     */
    public function queryHdkRequestEmails($where=null,$group=null,$order=null,$limit=null): array
    {
        
        $sql = "SELECT idgetemail, serverurl, servertype, serverport, user, `password`, ind_create_user, ind_delete_server, idservice, filter_from, filter_subject, login_layout, 
                        email_response_as_note  
                  FROM hdk_tbgetemail
                $where $group $order $limit";

        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $requestEmailDTO = new hdkRequestEmailModel(); 
            $requestEmailDTO->setGridList((!empty($aRet) && count($aRet) > 0) ? $aRet : array());

            $ret = true;
            $result = array("message"=>"","object"=>$requestEmailDTO);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting request by email data ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * en_us Returns an array with rows total for grid pagination
     * pt_br Retorna um array com o total de registros para paginação do grid
     *
     * @param  mixed $where
     * @return array            Parameters returned in array: 
     *                          [status = true/false
     *                           push =  [message = PDO Exception message 
     *                                    object = model's object]]
     */
    public function countHdkRequestEmails($where=null): array
    {        
        $sql = "SELECT COUNT(idgetemail) total 
                  FROM hdk_tbgetemail
                $where";
    
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            $requestEmailDTO = new hdkRequestEmailModel(); 
            $requestEmailDTO->setTotalRows((!is_null($aRet['total']) && !empty($aRet['total'])) ? $aRet['total'] : 0);

            $ret = true;
            $result = array("message"=>"","object"=>$requestEmailDTO);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error counting request by email data ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * insertRequestEmail
     * 
     * en_us Saves requests by email settings in hdk_tbgetemail table
     * pr_br Grava as configurações de solicitações por e-mail na tabela hdk_tbgetemail
     *
     * @param  hdkRequestEmailModel $hdkRequestEmailModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function insertRequestEmail(hdkRequestEmailModel $hdkRequestEmailModel): array
    {        
        $sql = "INSERT INTO hdk_tbgetemail (serverurl,servertype,serverport,user,password,ind_create_user,ind_delete_server,idservice,filter_from,filter_subject,login_layout,email_response_as_note)
                     VALUES (:serverUrl,:serverType,:serverPort,:user,:password,:createUserFlg,:deleteServerflg,:serviceId,:filterFrom,:filterSubject,NULLIF(:loginLayout,'NULL'),:responseNoteflg)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":serverUrl",$hdkRequestEmailModel->getServerUrl());
        $stmt->bindValue(":serverType",$hdkRequestEmailModel->getServerType());
        $stmt->bindValue(":serverPort",$hdkRequestEmailModel->getServerPort());
        $stmt->bindValue(":user",$hdkRequestEmailModel->getUser());
        $stmt->bindValue(":password",$hdkRequestEmailModel->getPassword());
        $stmt->bindValue(":createUserFlg",$hdkRequestEmailModel->getAddUserFlag());
        $stmt->bindValue(":deleteServerflg",$hdkRequestEmailModel->getDelFromServerFlag());
        $stmt->bindValue(":serviceId",$hdkRequestEmailModel->getServiceId());
        $stmt->bindValue(":filterFrom",$hdkRequestEmailModel->getFilterFrom());
        $stmt->bindValue(":filterSubject",$hdkRequestEmailModel->getFilterSubject());
        $stmt->bindValue(":loginLayout",(!empty($hdkRequestEmailModel->getLoginLayout())) ? $hdkRequestEmailModel->getLoginLayout() : 'NULL');
        $stmt->bindValue(":responseNoteflg",$hdkRequestEmailModel->getResponseNoteFlag());
        $stmt->execute();

        $hdkRequestEmailModel->setRequestEmailId($this->db->lastInsertId());

        $ret = true;
        $result = array("message"=>"","object"=>$hdkRequestEmailModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * insertRequestEmailDepartment
     * 
     * en_us Saves link between requests by email settings and department
     * pr_br Grava o vínculo entre as configurações de solicitações por e-mail e o departamento
     *
     * @param  hdkRequestEmailModel $hdkRequestEmailModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function insertRequestEmailDepartment(hdkRequestEmailModel $hdkRequestEmailModel): array
    {        
        $sql = "INSERT INTO hdk_tbgetemaildepartment (idgetemail,iddepartment) VALUES (:requestEmailId,:departmentId)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":requestEmailId",$hdkRequestEmailModel->getRequestEmailId());
        $stmt->bindValue(":departmentId",$hdkRequestEmailModel->getDepartmentId());
        $stmt->execute();

        $ret = true;
        $result = array("message"=>"","object"=>$hdkRequestEmailModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * saveRequestEmail
     * 
     * en_us Saves requests by email settings in DB
     * pr_br Grava as configurações de solicitações por e-mail no BD
     *
     * @param  hdkRequestEmailModel $hdkRequestEmailModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function saveRequestEmail(hdkRequestEmailModel $hdkRequestEmailModel): array
    {   
        try{
            $this->db->beginTransaction();

            $ins = $this->insertRequestEmail($hdkRequestEmailModel);

            if($ins['status'] && $hdkRequestEmailModel->getDepartmentId() > 0){
                //insert link between request email and department
                $insDepartment = $this->insertRequestEmailDepartment($hdkRequestEmailModel);
            }
            
            $ret = true;
            $result = array("message"=>"","object"=>$ins['push']['object']);
            $this->db->commit();
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error trying save requests by email settings', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
            $this->db->rollBack();
        }         
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * getRequestEmail
     * 
     * en_us Returns requests by email settings data
     * pr_br Retorna os dados das configurações de solicitações por e-mail
     *
     * @param  hdkRequestEmailModel $hdkRequestEmailModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function getRequestEmail(hdkRequestEmailModel $hdkRequestEmailModel): array
    {
        
        $sql = "SELECT a.idgetemail, serverurl, servertype, serverport, `user`, `password`, ind_create_user, ind_delete_server, a.idservice, filter_from, filter_subject, login_layout, 
                       email_response_as_note, e.idarea,d.idtype,c.iditem, f.idperson, b.iddepartment  
                  FROM hdk_tbgetemail a
       LEFT OUTER JOIN hdk_tbgetemaildepartment b
                    ON b.idgetemail = a.idgetemail
                  JOIN hdk_tbcore_service c
                    ON c.idservice = a.idservice
                  JOIN hdk_tbcore_item d
                    ON d.iditem = c.iditem
                  JOIN hdk_tbcore_type e
                    ON e.idtype = d.idtype
       LEFT OUTER JOIN hdk_tbdepartment f
                    ON f.iddepartment = b.iddepartment
                 WHERE a.idgetemail = :requestEmailId";
               // echo "{$sql}\n";
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':requestEmailId', $hdkRequestEmailModel->getRequestEmailId());
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);

            $hdkRequestEmailModel->setServerUrl((!is_null($aRet['serverurl']) && !empty($aRet['serverurl'])) ? $aRet['serverurl'] : "")
                                 ->setServerType((!is_null($aRet['servertype']) && !empty($aRet['servertype'])) ? $aRet['servertype'] : "")
                                 ->setServerPort((!is_null($aRet['serverport']) && !empty($aRet['serverport'])) ? $aRet['serverport'] : "")
                                 ->setUser((!is_null($aRet['user']) && !empty($aRet['user'])) ? $aRet['user'] : "")
                                 ->setPassword((!is_null($aRet['password']) && !empty($aRet['password'])) ? $aRet['password'] : "")
                                 ->setAddUserFlag((!is_null($aRet['ind_create_user']) && !empty($aRet['ind_create_user'])) ? $aRet['ind_create_user'] : 0)
                                 ->setDelFromServerFlag((!is_null($aRet['ind_delete_server']) && !empty($aRet['ind_delete_server'])) ? $aRet['ind_delete_server'] : 0)
                                 ->setServiceId((!is_null($aRet['idservice']) && !empty($aRet['idservice'])) ? $aRet['idservice'] : 0)
                                 ->setFilterFrom((!is_null($aRet['filter_from']) && !empty($aRet['filter_from'])) ? $aRet['filter_from'] : "")
                                 ->setFilterSubject((!is_null($aRet['filter_subject']) && !empty($aRet['filter_subject'])) ? $aRet['filter_subject'] : "")
                                 ->setLoginLayout((!is_null($aRet['login_layout']) && !empty($aRet['login_layout'])) ? $aRet['login_layout'] : "")
                                 ->setResponseNoteFlag((!is_null($aRet['email_response_as_note']) && !empty($aRet['email_response_as_note'])) ? $aRet['email_response_as_note'] : "")
                                 ->setAreaId((!is_null($aRet['idarea']) && !empty($aRet['idarea'])) ? $aRet['idarea'] : 0)
                                 ->setTypeId((!is_null($aRet['idtype']) && !empty($aRet['idtype'])) ? $aRet['idtype'] : 0)
                                 ->setItemId((!is_null($aRet['iditem']) && !empty($aRet['iditem'])) ? $aRet['iditem'] : 0)
                                 ->setCompanyId((!is_null($aRet['idperson']) && !empty($aRet['idperson'])) ? $aRet['idperson'] : 0)
                                 ->setDepartmentId((!is_null($aRet['iddepartment']) && !empty($aRet['iddepartment'])) ? $aRet['iddepartment'] : 0);

            $ret = true;
            $result = array("message"=>"","object"=>$hdkRequestEmailModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting request by email settings data", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * updateRequestEmail
     * 
     * en_us Updates requests by email settings in hdk_tbgetemail table
     * pr_br Atualiza as configurações de solicitações por e-mail na tabela hdk_tbgetemail
     *
     * @param  hdkRequestEmailModel $hdkRequestEmailModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function updateRequestEmail(hdkRequestEmailModel $hdkRequestEmailModel): array
    {
        $sql = "UPDATE hdk_tbgetemail
                   SET serverurl = :serverUrl,
                       servertype = :serverType,
                       serverport = :serverPort,
                       user = :user,
                       `password` = :password,
                       ind_create_user = :createUserFlg,
                       ind_delete_server = :deleteServerflg,
                       idservice = :serviceId,
                       filter_from = :filterFrom,
                       filter_subject = :filterSubject,
                       login_layout = NULLIF(:loginLayout,'NULL'),
                       email_response_as_note =:responseNoteflg
                 WHERE idgetemail = :requestEmailId";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":serverUrl",$hdkRequestEmailModel->getServerUrl());
        $stmt->bindValue(":serverType",$hdkRequestEmailModel->getServerType());
        $stmt->bindValue(":serverPort",$hdkRequestEmailModel->getServerPort());
        $stmt->bindValue(":user",$hdkRequestEmailModel->getUser());
        $stmt->bindValue(":password",$hdkRequestEmailModel->getPassword());
        $stmt->bindValue(":createUserFlg",$hdkRequestEmailModel->getAddUserFlag());
        $stmt->bindValue(":deleteServerflg",$hdkRequestEmailModel->getDelFromServerFlag());
        $stmt->bindValue(":serviceId",$hdkRequestEmailModel->getServiceId());
        $stmt->bindValue(":filterFrom",$hdkRequestEmailModel->getFilterFrom());
        $stmt->bindValue(":filterSubject",$hdkRequestEmailModel->getFilterSubject());
        $stmt->bindValue(":loginLayout",(!empty($hdkRequestEmailModel->getLoginLayout())) ? $hdkRequestEmailModel->getLoginLayout() : 'NULL');
        $stmt->bindValue(":responseNoteflg",$hdkRequestEmailModel->getResponseNoteFlag());
        $stmt->bindValue(":requestEmailId",$hdkRequestEmailModel->getRequestEmailId());
        $stmt->execute();

        $ret = true;
        $result = array("message"=>"","object"=>$hdkRequestEmailModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * deleteRequestEmailDepartment
     * 
     * en_us Removes link between requests by email settings and department
     * pr_br Deleta o vínculo entre as configurações de solicitações por e-mail e o departamento
     *
     * @param  hdkRequestEmailModel $hdkRequestEmailModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function deleteRequestEmailDepartment(hdkRequestEmailModel $hdkRequestEmailModel): array
    {
        $sql = "DELETE FROM hdk_tbgetemaildepartment WHERE idgetemail = :requestEmailId";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":requestEmailId",$hdkRequestEmailModel->getRequestEmailId());
        $stmt->execute();

        $ret = true;
        $result = array("message"=>"","object"=>$hdkRequestEmailModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * saveUpdatehdkRequestEmail
     * 
     * en_us Updates requests by email setting data in DB
     * pr_br Atualiza os dados da configução de solicitações por e-mail no BD
     *
     * @param  hdkRequestEmailModel $hdkRequestEmailModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function saveUpdateRequestEmail(hdkRequestEmailModel $hdkRequestEmailModel): array
    {   
        try{
            $this->db->beginTransaction();

            $upd = $this->updateRequestEmail($hdkRequestEmailModel);

            if($upd['status']){
                //remove department
                $this->deleteRequestEmailDepartment($hdkRequestEmailModel);
                
                //insert link between request email and department
                if($hdkRequestEmailModel->getDepartmentId() > 0){
                    $this->insertRequestEmailDepartment($hdkRequestEmailModel);
                }
            }
            
            $ret = true;
            $result = array("message"=>"","object"=>$upd['push']['object']);
            $this->db->commit();
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error updating requests by email settings', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
            $this->db->rollBack();
        }         
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * deleteRequestEmail
     * 
     * en_us Removes requests by email setting data from hdk_tbgetemail table
     * pr_br Deleta os dados da configuração de solicitações por e-mail da tabela hdk_tbgetemail
     *
     * @param  hdkRequestEmailModel $hdkRequestEmailModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function deleteRequestEmail(hdkRequestEmailModel $hdkRequestEmailModel): array
    {
        $sql = "DELETE FROM hdk_tbgetemail WHERE idgetemail = :requestEmailId";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":requestEmailId",$hdkRequestEmailModel->getRequestEmailId());
        $stmt->execute();

        $ret = true;
        $result = array("message"=>"","object"=>$hdkRequestEmailModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * saveDeleteRequestEmail
     * 
     * en_us Removes requests by email setting data from DB
     * pr_br Deleta os dados da configução de solicitações por e-mail do BD
     *
     * @param  hdkRequestEmailModel $hdkRequestEmailModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function saveDeleteRequestEmail(hdkRequestEmailModel $hdkRequestEmailModel): array
    {   
        try{
            $this->db->beginTransaction();

            //remove department
            $delDepartment = $this->deleteRequestEmailDepartment($hdkRequestEmailModel);

            if($delDepartment['status']){
                $this->deleteRequestEmail($hdkRequestEmailModel);
            }
            
            $ret = true;
            $result = array("message"=>"","object"=>$hdkRequestEmailModel);
            $this->db->commit();
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error delete requests by email settings', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
            $this->db->rollBack();
        }         
        
        return array("status"=>$ret,"push"=>$result);
    }
}