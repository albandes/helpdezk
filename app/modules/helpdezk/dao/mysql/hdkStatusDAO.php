<?php

namespace App\modules\helpdezk\dao\mysql;

use App\core\Database;
use App\modules\helpdezk\models\mysql\hdkStatusModel;

class hdkStatusDAO extends Database
{
    
    public function __construct()
    {
        parent::__construct(); 
    }
    
    /**
     * queryHdkStatus
     * 
     * en_us Returns an array with a status list to display in the grid
     * pt_br Retorna um array com uma lista de status para exibir no grid
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
    public function queryHdkStatus($where=null,$group=null,$order=null,$limit=null): array
    {
        
        $sql = "SELECT idstatus, `name`, user_view, color, `status`, idstatus_source
                  FROM hdk_tbstatus
                  $where $group $order $limit";
               // echo "{$sql}\n";
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $hdkStatus = new hdkStatusModel(); 
            $hdkStatus->setGridList(($aRet && is_array($aRet)) ? $aRet : array());

            $ret = true;
            $result = array("message"=>"","object"=>$hdkStatus);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting status ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * countHdkStatus
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
    public function countHdkStatus($where=null): array
    {        
        $sql = "SELECT COUNT(idstatus) total
                  FROM hdk_tbstatus
                $where";
    
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            $hdkStatus = new hdkStatusModel();
            $hdkStatus->setTotalRows((!is_null($aRet['total']) && !empty($aRet['total'])) ? $aRet['total'] : 0);

            $ret = true;
            $result = array("message"=>"","object"=>$hdkStatus);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error counting status ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * insertHdkStatus
     * 
     * en_us Inserts status data into hdk_tbstatus
     * pt_br Insere os dados do status em hdk_tbstatus
     *
     * @param  hdkStatusModel $hdkStatusModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function insertHdkStatus(hdkStatusModel $hdkStatusModel): array
    {        
        $sql = "INSERT INTO hdk_tbstatus (`name`,user_view,color,idstatus_source,stop_time_flag) 
                     VALUES (:name,:userView,:color,:statusGroup,:stopTimeFlag)";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":name",$hdkStatusModel->getName());
            $stmt->bindValue(":userView",$hdkStatusModel->getRequesterView());
            $stmt->bindValue(":color",$hdkStatusModel->getColor());
            $stmt->bindValue(":statusGroup",$hdkStatusModel->getStatusSourceId());
            $stmt->bindValue(":stopTimeFlag",$hdkStatusModel->getStopSlaFlag());
            $stmt->execute();

            $hdkStatusModel->setStatusId($this->db->lastInsertId());

            $ret = true;
            $result = array("message"=>"","object"=>$hdkStatusModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error trying save status info ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }      
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * getHdkStatus
     * 
     * en_us Returns status data from DB
     * pt_br Retorna os dados do status do BD
     *
     * @param  mixed $hdkStatusModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function getHdkStatus(hdkStatusModel $hdkStatusModel): array
    {        
        $sql = "SELECT idstatus, `name`, user_view, color, `status`, idstatus_source, stop_time_flag
                  FROM hdk_tbstatus
                 WHERE idstatus = :hdkStatusId";
    
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":hdkStatusId",$hdkStatusModel->getStatusId());
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            $hdkStatusModel->setName((!is_null($aRet['name']) && !empty($aRet['name'])) ? $aRet['name'] : "")
                           ->setRequesterView((!is_null($aRet['user_view']) && !empty($aRet['user_view'])) ? $aRet['user_view'] : "")
                           ->setColor((!is_null($aRet['color']) && !empty($aRet['color'])) ? $aRet['color'] : "")
                           ->setStatus((!is_null($aRet['status']) && !empty($aRet['status'])) ? $aRet['status'] : "")
                           ->setStatusSourceId((!is_null($aRet['idstatus_source']) && !empty($aRet['idstatus_source'])) ? $aRet['idstatus_source'] : 0)
                           ->setStopSlaFlag((!is_null($aRet['stop_time_flag']) && !empty($aRet['stop_time_flag'])) ? $aRet['stop_time_flag'] : 0);

            $ret = true;
            $result = array("message"=>"","object"=>$hdkStatusModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting status data ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * updateHdkStatus
     * 
     * en_us Updates hdkStatus data into hdk_tbhdkStatus
     * pt_br Atualiza os dados da prioridade em hdk_tbhdkStatus
     *
     * @param  mixed $hdkStatusModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function updateHdkStatus(hdkStatusModel $hdkStatusModel): array
    {        
        $sql = "UPDATE hdk_tbstatus 
                   SET `name` = :name,
                        user_view = :userView,
                        color = :color,
                        idstatus_source = :statusGroup,
                        stop_time_flag = :stopTimeFlag
                 WHERE idstatus = :hdkStatusId";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":name",$hdkStatusModel->getName());
            $stmt->bindValue(":userView",$hdkStatusModel->getRequesterView());
            $stmt->bindValue(":color",$hdkStatusModel->getColor());
            $stmt->bindValue(":statusGroup",$hdkStatusModel->getStatusSourceId());
            $stmt->bindValue(":stopTimeFlag",$hdkStatusModel->getStopSlaFlag());
            $stmt->bindValue(":hdkStatusId",$hdkStatusModel->getStatusId());
            $stmt->execute();

            $ret = true;
            $result = array("message"=>"","object"=>$hdkStatusModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error trying update status info ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * updateStatusState
     * 
     * en_us Updates status state in hdk_tbstatus table
     * pt_br Atualiza o estado do status na tabela hdk_tbstatus
     *
     * @param  mixed $hdkStatusModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function updateStatusState(hdkStatusModel $hdkStatusModel): array
    {        
        $sql = "UPDATE hdk_tbstatus SET `status` = :status WHERE idstatus = :hdkStatusId";
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":status",$hdkStatusModel->getStatus());
            $stmt->bindValue(":hdkStatusId",$hdkStatusModel->getStatusId());
            $stmt->execute();

            $ret = true;
            $result = array("message"=>"","object"=>$hdkStatusModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error trying update status state ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * fetchHdkStatusLink
     * 
     * en_us Returns status's links with tickets
     * pt_br Retorna os vínculos dos status com os tickets
     *
     * @param  mixed $hdkStatusModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchHdkStatusLink(hdkStatusModel $hdkStatusModel): array
    {        
        $sql = "SELECT code_request FROM hdk_tbrequest WHERE idstatus = :hdkStatusId";
    
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":hdkStatusId",$hdkStatusModel->getStatusId());
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            $hdkStatusModel->setLinkList((!is_null($aRet) && !empty($aRet)) ? $aRet : array());

            $ret = true;
            $result = array("message"=>"","object"=>$hdkStatusModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting status link with tickets ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * deleteHdkStatus
     * 
     * en_us Deletes hdkStatus's data from hdk_tbhdkStatus table
     * pt_br Deleta os dados da prioridade da tabela hdk_tbhdkStatus
     *
     * @param  mixed $hdkStatusModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function deleteHdkStatus(hdkStatusModel $hdkStatusModel): array
    {        
        $sql = "DELETE FROM hdk_tbstatus WHERE idstatus = :hdkStatusId";

        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":hdkStatusId",$hdkStatusModel->getStatusId());
            $stmt->execute();

            $ret = true;
            $result = array("message"=>"","object"=>$hdkStatusModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error trying remove status', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }      
        
        return array("status"=>$ret,"push"=>$result);
    }

}