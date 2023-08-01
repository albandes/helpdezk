<?php

namespace App\modules\helpdezk\dao\mysql;

use App\core\Database;
use App\modules\helpdezk\models\mysql\priorityModel;

class priorityDAO extends Database
{
    
    public function __construct()
    {
        parent::__construct(); 
    }
    
    /**
     * queryPriorities
     * 
     * en_us Returns an array with a priorities list to display in the grid
     * pt_br Retorna um array com uma lista de prioridades para exibir no grid
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
    public function queryPriorities($where=null,$group=null,$order=null,$limit=null): array
    {
        
        $sql = "SELECT idpriority, `name`, `order`, `default`, color, limit_days, limit_hours, `status`, vip
                  FROM hdk_tbpriority
                  $where $group $order $limit";
               // echo "{$sql}\n";
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $priority = new priorityModel(); 
            $priority->setGridList($aRet);

            $ret = true;
            $result = array("message"=>"","object"=>$priority);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting priorities ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * countPriorities
     * 
     * en_us Returns an array with rows total for grid pagination
     * pt_br Retorna um array com o total de linhas para paginação do grid
     *
     * @param  mixed $where
     * @return array array Parameters returned in array: 
     *                      [status = true/false
     *                       push =  [message = PDO Exception message 
     *                       object = model's object]]
     */
    public function countPriorities($where=null): array
    {        
        $sql = "SELECT COUNT(idpriority) total
                 FROM hdk_tbpriority
                $where";
    
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            $priority = new priorityModel();
            $priority->setTotalRows($aRet['total']);

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
     * getLastOrder
     * 
     * en_us Returns last order number
     * pt_br Retorna o último número de ordem
     *
     * @param  priorityModel $priorityModel
     * @return array array Parameters returned in array: 
     *                      [status = true/false
     *                       push =  [message = PDO Exception message 
     *                       object = model's object]]
     */
    public function getLastOrder(priorityModel $priorityModel): array
    {        
        $sql = "SELECT MAX(`order`) last_order FROM hdk_tbpriority";
    
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            $priorityModel->setOrder((!is_null($aRet['last_order']) && !empty($aRet['last_order'])) ? $aRet['last_order'] : 0);

            $ret = true;
            $result = array("message"=>"","object"=>$priorityModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting last priority order ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * getDefaultId
     * 
     * en_us Returns default priority id
     * pt_br Retorna o id da prioridade default
     *
     * @param  mixed $priorityModel
     * @return array
     */
    public function getDefaultPriorityId(priorityModel $priorityModel): array
    {        
        $sql = "SELECT idpriority FROM hdk_tbpriority WHERE `default` = 1";
    
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            $priorityModel->setDefaultId((!is_null($aRet['idpriority']) && !empty($aRet['idpriority'])) ? $aRet['idpriority'] : 0);

            $ret = true;
            $result = array("message"=>"","object"=>$priorityModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting default priority id ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * removeDefault
     * 
     * en_us Removes the default priority flag
     * pt_br Remove o sinalizador de prioridade padrão
     *
     * @param  mixed $priorityModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function removeDefault(priorityModel $priorityModel): array
    {        
        $sql = "UPDATE hdk_tbpriority SET `default` = 0 WHERE `default` = 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        $ret = true;
        $result = array("message"=>"","object"=>$priorityModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * insertPriority
     * 
     * en_us Inserts priority data into hdk_tbpriority
     * pt_br Insere os dados da prioridade em hdk_tbpriority
     *
     * @param  priorityModel $priorityModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function insertPriority(priorityModel $priorityModel): array
    {        
        $sql = "INSERT INTO hdk_tbpriority (`name`,`order`,color,`default`,vip,limit_hours,limit_days) 
                     VALUES (:name,:order,:color,:default,:vip,:limit_hours,:limit_days)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":name",$priorityModel->getName());
        $stmt->bindValue(":order",$priorityModel->getOrder());
        $stmt->bindValue(":color",$priorityModel->getColor());
        $stmt->bindValue(":default",$priorityModel->getDefault());
        $stmt->bindValue(":vip",$priorityModel->getVip());
        $stmt->bindValue(":limit_hours",$priorityModel->getLimitHours());
        $stmt->bindValue(":limit_days",$priorityModel->getLimitDays());
        $stmt->execute();

        $priorityModel->setIdPriority($this->db->lastInsertId());

        $ret = true;
        $result = array("message"=>"","object"=>$priorityModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * savePriority
     * 
     * en_us Saves priority data into DB
     * pt_br Grava os dados da prioridade no BD
     *
     * @param  mixed $priorityModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function savePriority(priorityModel $priorityModel): array
    {
        try{
            $this->db->beginTransaction();

            if($priorityModel->getDefault() == 1){
                $this->removeDefault($priorityModel);
            }

            $ins = $this->insertPriority($priorityModel);

            $ret = true;
            $result = array("message"=>"","object"=>$ins['push']['object']);
            $this->db->commit();
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error trying save priority info ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
            $this->db->rollBack();
        }         
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * getPriority
     * 
     * en_us Returns priority data from DB
     * pt_br Retorna os dados da prioridade do BD
     *
     * @param  mixed $priorityModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function getPriority(priorityModel $priorityModel): array
    {        
        $sql = "SELECT idpriority, `name`, `order`, `default`, color, limit_days, limit_hours, `status`, vip
                  FROM hdk_tbpriority
                 WHERE idpriority = :priorityId";
    
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":priorityId",$priorityModel->getIdPriority());
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            $priorityModel->setName((!is_null($aRet['name']) && !empty($aRet['name'])) ? $aRet['name'] : "")
                          ->setOrder((!is_null($aRet['order']) && !empty($aRet['order'])) ? $aRet['order'] : 0)
                          ->setDefault((!is_null($aRet['default']) && !empty($aRet['default'])) ? $aRet['default'] : 0)
                          ->setColor((!is_null($aRet['color']) && !empty($aRet['color'])) ? $aRet['color'] : "")
                          ->setLimitDays((!is_null($aRet['limit_days']) && !empty($aRet['limit_days'])) ? $aRet['limit_days'] : 0)
                          ->setLimitHours((!is_null($aRet['limit_hours']) && !empty($aRet['limit_hours'])) ? $aRet['limit_hours'] : 0)
                          ->setStatus((!is_null($aRet['status']) && !empty($aRet['status'])) ? $aRet['status'] : "")
                          ->setVip((!is_null($aRet['vip']) && !empty($aRet['vip'])) ? $aRet['vip'] : 0);

            $ret = true;
            $result = array("message"=>"","object"=>$priorityModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting priority data ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * updatePriority
     * 
     * en_us Updates priority data into hdk_tbpriority
     * pt_br Atualiza os dados da prioridade em hdk_tbpriority
     *
     * @param  mixed $priorityModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function updatePriority(priorityModel $priorityModel): array
    {        
        $sql = "UPDATE hdk_tbpriority 
                   SET `name` = :name,
                       `order` = :order,
                       color = :color,
                       `default` = :default,
                       vip = :vip,
                       limit_hours = :limit_hours,
                       limit_days = :limit_days
                 WHERE idpriority = :priorityId";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":name",$priorityModel->getName());
        $stmt->bindValue(":order",$priorityModel->getOrder());
        $stmt->bindValue(":color",$priorityModel->getColor());
        $stmt->bindValue(":default",$priorityModel->getDefault());
        $stmt->bindValue(":vip",$priorityModel->getVip());
        $stmt->bindValue(":limit_hours",$priorityModel->getLimitHours());
        $stmt->bindValue(":limit_days",$priorityModel->getLimitDays());
        $stmt->bindValue(":priorityId",$priorityModel->getIdPriority());
        $stmt->execute();

        $ret = true;
        $result = array("message"=>"","object"=>$priorityModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * getNewDefaultPriority
     * 
     * en_us Returns a new priority's id to make as default
     * pt_br Retorna um novo id de prioridade para tornar como default
     *
     * @param  mixed $priorityModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function getNewDefaultPriority(priorityModel $priorityModel): array
    {        
        $sql = "SELECT idpriority FROM hdk_tbpriority WHERE idpriority != :priorityId ORDER BY `order` LIMIT 1";
    
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":priorityId",$priorityModel->getIdPriority());
        $stmt->execute();

        $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        $priorityModel->setDefaultId((!is_null($aRet['idpriority']) && !empty($aRet['idpriority'])) ? $aRet['idpriority'] : 0);

        $ret = true;
        $result = array("message"=>"","object"=>$priorityModel);
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * makeDefault
     * 
     * en_us Makes a new priority as default
     * pt_br Torna uma nova prioridade como default
     *
     * @param  mixed $priorityModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function makeDefault(priorityModel $priorityModel): array
    {        
        $sql = "UPDATE hdk_tbpriority SET `default` = 1 WHERE idpriority = :priorityId";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":priorityId",$priorityModel->getDefaultId());
        $stmt->execute();

        $ret = true;
        $result = array("message"=>"","object"=>$priorityModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * checkExhibitionOrder
     * 
     * en_us Checks if the display order already exists
     * pt_br Verifica se a ordem de exibição já existe
     *
     * @param  mixed $priorityModel
     * @return array
     */
    public function checkExhibitionOrder(priorityModel $priorityModel): array
    {        
        $sql = "SELECT idpriority FROM hdk_tbpriority WHERE `order` = :order AND idpriority != :priorityId";
    
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":order",$priorityModel->getOrder());
        $stmt->bindValue(":priorityId",$priorityModel->getIdPriority());
        $stmt->execute();

        $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        $priorityModel->setPriorityIdTmp((!is_null($aRet['idpriority']) && !empty($aRet['idpriority'])) ? $aRet['idpriority'] : 0);

        $ret = true;
        $result = array("message"=>"","object"=>$priorityModel);
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * changeTmpOrder
     * 
     * en_us Updates display order's number
     * pt_br Verifica se a ordem de exibição já existe
     *
     * @param  mixed $priorityModel
     * @return array
     */
    public function changeTmpOrder(priorityModel $priorityModel): array
    {        
        $sql = "UPDATE hdk_tbpriority SET `order` = :orderTmp WHERE idpriority = :priorityIdTmp";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":orderTmp",$priorityModel->getOrderTmp());
        $stmt->bindValue(":priorityIdTmp",$priorityModel->getPriorityIdTmp());
        $stmt->execute();

        $ret = true;
        $result = array("message"=>"","object"=>$priorityModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * saveUpdatePriority
     * 
     * en_us Updates priority data into DB
     * pt_br Atualiza os dados da prioridade no BD
     *
     * @param  mixed $priorityModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function saveUpdatePriority(priorityModel $priorityModel): array
    {
        try{
            $this->db->beginTransaction();

            if($priorityModel->getDefault() == 1){
                $this->removeDefault($priorityModel);
            }

            //checks if the display number already exists
            $check = $this->checkExhibitionOrder($priorityModel);
            if($check['status'] && $check['push']['object']->getPriorityIdTmp() > 0){
                $this->changeTmpOrder($check['push']['object']);
            }

            $upd = $this->updatePriority($priorityModel);

            //assigns new default priority when the default's flag is 0
            if($priorityModel->getDefault() == 0 && $priorityModel->getDefaultId() == $priorityModel->getIdPriority()){
                $retNew = $this->getNewDefaultPriority($upd['push']['object']);//get the new priority id
                
                $this->makeDefault($retNew['push']['object']);
            }

            $ret = true;
            $result = array("message"=>"","object"=>$upd['push']['object']);
            $this->db->commit();
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error updating priority info ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
            $this->db->rollBack();
        }         
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * updateStatus
     * 
     * en_us Updates priority's status in hdk_tbpriority table
     * pt_br Atualiza o status da prioridade na tabela hdk_tbpriority
     *
     * @param  mixed $priorityModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function updateStatus(priorityModel $priorityModel): array
    {        
        $sql = "UPDATE hdk_tbpriority SET `status` = :status WHERE idpriority = :priorityId";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":status",$priorityModel->getStatus());
        $stmt->bindValue(":priorityId",$priorityModel->getIdPriority());
        $stmt->execute();

        $ret = true;
        $result = array("message"=>"","object"=>$priorityModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * saveNewStatus
     * 
     * en_us Updates priority's status intoDB
     * pt_br Atualiza o status da prioridade no BD
     *
     * @param  mixed $priorityModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function saveNewStatus(priorityModel $priorityModel): array
    {
        try{
            $this->db->beginTransaction();

            if($priorityModel->getStatus() == "I" && $priorityModel->getDefault() == 1){
                $this->removeDefault($priorityModel);//remove default flag

                $retNew = $this->getNewDefaultPriority($priorityModel);//get the new priority id
                
                $this->makeDefault($retNew['push']['object']);
            }

            $upd = $this->updateStatus($priorityModel);

            $ret = true;
            $result = array("message"=>"","object"=>$upd['push']['object']);
            $this->db->commit();
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error updating priority status ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
            $this->db->rollBack();
        }         
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * fetchPriorityLink
     * 
     * en_us Returns priority's links with services
     * pt_br Retorna os vínculos da prioridade com os serviços
     *
     * @param  mixed $priorityModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchPriorityLink(priorityModel $priorityModel): array
    {        
        $sql = "SELECT idservice FROM hdk_tbcore_service WHERE idpriority = :priorityId";
    
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":priorityId",$priorityModel->getIdPriority());
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            $priorityModel->setLinkList((!is_null($aRet) && !empty($aRet)) ? $aRet : array());

            $ret = true;
            $result = array("message"=>"","object"=>$priorityModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting priority link with service ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * deletePriority
     * 
     * en_us Deletes priority's data from hdk_tbpriority table
     * pt_br Deleta os dados da prioridade da tabela hdk_tbpriority
     *
     * @param  mixed $priorityModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function deletePriority(priorityModel $priorityModel): array
    {        
        $sql = "DELETE FROM hdk_tbpriority WHERE idpriority = :priorityId";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":priorityId",$priorityModel->getIdPriority());
        $stmt->execute();

        $ret = true;
        $result = array("message"=>"","object"=>$priorityModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * saveDeletePriority
     * 
     * en_us Deletes priority's data from DB
     * pt_br Deleta os dados da prioridade do BD
     *
     * @param  mixed $priorityModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function saveDeletePriority(priorityModel $priorityModel): array
    {
        try{
            $this->db->beginTransaction();

            if($priorityModel->getDefault() == 1){
                $this->removeDefault($priorityModel);//remove default flag

                $retNew = $this->getNewDefaultPriority($priorityModel);//get the new priority id
                
                $this->makeDefault($retNew['push']['object']);
            }

            $del = $this->deletePriority($priorityModel);

            $ret = true;
            $result = array("message"=>"","object"=>$del['push']['object']);
            $this->db->commit();
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error updating priority status ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
            $this->db->rollBack();
        }         
        
        return array("status"=>$ret,"push"=>$result);
    }
    

}