<?php

namespace App\modules\helpdezk\dao\mysql;

use App\core\Database;

use App\modules\helpdezk\models\mysql\hdkServiceModel;

class hdkServiceDAO extends Database
{
    public function __construct()
    {
        parent::__construct(); 
    }

    /**
     * en_us Returns an array with active areas list
     * pt_br Retorna um array com lista de áreas ativas
     *
     * @param  hdkServiceModel $hdkServiceModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchArea(hdkServiceModel $hdkServiceModel): array
    {        
        $sql = "SELECT idarea, `name`, `default`
                  FROM hdk_tbcore_area
                 WHERE `status` = 'A'
              ORDER BY `name` ASC";
        //echo "{$sql}\n";
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $hdkServiceModel->setAreaList($aRet);

            $ret = true;
            $result = array("message"=>"","object"=>$hdkServiceModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting areas ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Returns an array with active types list
     * pt_br Retorn um array com a lista de tipos activos
     *
     * @param  hdkServiceModel $hdkServiceModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchTypeByArea(hdkServiceModel $hdkServiceModel): array
    {        
        $sql = "SELECT idtype, `name`, `selected` AS `default`
                  FROM hdk_tbcore_type
                 WHERE idarea = :areaID
                   AND `status` = 'A'
              ORDER BY `name` ASC";
        //echo "{$sql}\n";
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':areaID', $hdkServiceModel->getIdArea());
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $hdkServiceModel->setTypeList($aRet);

            $ret = true;
            $result = array("message"=>"","object"=>$hdkServiceModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting types ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Returns an array with active items list
     * pt_br Retorna um array com a lista de itens ativos
     *
     * @param  hdkServiceModel $hdkServiceModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchItemByType(hdkServiceModel $hdkServiceModel): array
    {        
        $sql = "SELECT iditem, `name`, `selected` AS `default`
                  FROM hdk_tbcore_item
                 WHERE idtype = :typeID
                   AND `status` = 'A'
              ORDER BY `name` ASC";
        //echo "{$sql}\n";
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':typeID', $hdkServiceModel->getIdType());
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $hdkServiceModel->setItemList($aRet);

            $ret = true;
            $result = array("message"=>"","object"=>$hdkServiceModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting items ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Returns an array with active services list
     * pt_br Retorna um array com a lista de serviços ativos
     *
     * @param  hdkServiceModel $hdkServiceModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchServiceByItem(hdkServiceModel $hdkServiceModel): array
    {        
        $sql = "SELECT idservice, `name`, `selected` AS `default`
                  FROM hdk_tbcore_service
                 WHERE iditem = :itemID
                   AND `status` = 'A'
              ORDER BY `name` ASC";
        //echo "{$sql}\n";
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':itemID', $hdkServiceModel->getIdItem());
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $hdkServiceModel->setServiceList($aRet);

            $ret = true;
            $result = array("message"=>"","object"=>$hdkServiceModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting items ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Returns an array with areas list
     * pt_br Retorna um array com a lista de áreas
     *
     * @param  hdkServiceModel $hdkServiceModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchAreas(hdkServiceModel $hdkServiceModel): array
    {        
        $sql = "SELECT idarea, `name`, `default`, `status`
                  FROM hdk_tbcore_area
              ORDER BY `name` ASC";
        //echo "{$sql}\n";
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $hdkServiceModel->setAreaList($aRet);

            $ret = true;
            $result = array("message"=>"","object"=>$hdkServiceModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting areas ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Returns an array with types list
     * pt_br Retorn um array com a lista de tipos
     *
     * @param  hdkServiceModel $hdkServiceModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchAllTypesByArea(hdkServiceModel $hdkServiceModel): array
    {        
        $sql = "SELECT idtype, `name`, `selected` AS `default`, `status`
                  FROM hdk_tbcore_type
                 WHERE idarea = :areaID
              ORDER BY `name` ASC";
        //echo "{$sql}\n";
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':areaID', $hdkServiceModel->getIdArea());
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $hdkServiceModel->setTypeList($aRet);

            $ret = true;
            $result = array("message"=>"","object"=>$hdkServiceModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting types ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Returns an array with items list
     * pt_br Retorna um array com a lista de itens
     *
     * @param  hdkServiceModel $hdkServiceModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchAllItemsByType(hdkServiceModel $hdkServiceModel): array
    {        
        $sql = "SELECT iditem, `name`, `selected` AS `default`, `status`
                  FROM hdk_tbcore_item
                 WHERE idtype = :typeID
              ORDER BY `name` ASC";
        //echo "{$sql}\n";
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':typeID', $hdkServiceModel->getIdType());
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $hdkServiceModel->setItemList($aRet);

            $ret = true;
            $result = array("message"=>"","object"=>$hdkServiceModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting items ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Returns an array with active services list
     * pt_br Retorna um array com a lista de serviços ativos
     *
     * @param  hdkServiceModel $hdkServiceModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchAllServicesByItem(hdkServiceModel $hdkServiceModel): array
    {        
        $sql = "SELECT idservice, `name`, `selected` AS `default`, `status`
                  FROM hdk_tbcore_service
                 WHERE iditem = :itemID
              ORDER BY `name` ASC";
        //echo "{$sql}\n";
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':itemID', $hdkServiceModel->getIdItem());
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $hdkServiceModel->setServiceList($aRet);

            $ret = true;
            $result = array("message"=>"","object"=>$hdkServiceModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting items ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Returns area's data
     * pt_br Retorna os dados da área
     *
     * @param  hdkServiceModel $hdkServiceModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function getArea(hdkServiceModel $hdkServiceModel): array
    {        
        $sql = "SELECT `name`, `status`, `default` FROM hdk_tbcore_area WHERE idarea = :areaId";
        //echo "{$sql}\n";
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':areaId', $hdkServiceModel->getIdArea());
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            $hdkServiceModel->setAreaName((!is_null($aRet['name'])) ? $aRet['name'] : "")
                            ->setStatus((!is_null($aRet['status'])) ? $aRet['status'] : "I")
                            ->setFlagDefault((!is_null($aRet['default'])) ? $aRet['default'] : 0);

            $ret = true;
            $result = array("message"=>"","object"=>$hdkServiceModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting area's info.", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Returns type's data
     * pt_br Retorna os dados do tipo
     *
     * @param  hdkServiceModel $hdkServiceModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function getType(hdkServiceModel $hdkServiceModel): array
    {        
        $sql = "SELECT `name`, `status`, `selected`, classify, idarea FROM hdk_tbcore_type WHERE idtype = :typeId";
        //echo "{$sql}\n";
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':typeId', $hdkServiceModel->getIdType());
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            $hdkServiceModel->setTypeName((!is_null($aRet['name'])) ? $aRet['name'] : "")
                            ->setStatus((!is_null($aRet['status'])) ? $aRet['status'] : "I")
                            ->setFlagDefault((!is_null($aRet['selected'])) ? $aRet['selected'] : 0)
                            ->setFlagClassify((!is_null($aRet['classify'])) ? $aRet['classify'] : 0)
                            ->setIdArea((!is_null($aRet['idarea'])) ? $aRet['idarea'] : 0);

            $ret = true;
            $result = array("message"=>"","object"=>$hdkServiceModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting type's info.", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Returns item's data
     * pt_br Retorna os dados do item
     *
     * @param  hdkServiceModel $hdkServiceModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function getItem(hdkServiceModel $hdkServiceModel): array
    {        
        $sql = "SELECT `name`, `status`, `selected`, classify, idtype FROM hdk_tbcore_item WHERE iditem = :itemId";
        //echo "{$sql}\n";
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':itemId', $hdkServiceModel->getIdItem());
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            $hdkServiceModel->setItemName((!is_null($aRet['name'])) ? $aRet['name'] : "")
                            ->setStatus((!is_null($aRet['status'])) ? $aRet['status'] : "I")
                            ->setFlagDefault((!is_null($aRet['selected'])) ? $aRet['selected'] : 0)
                            ->setFlagClassify((!is_null($aRet['classify'])) ? $aRet['classify'] : 0)
                            ->setIdType((!is_null($aRet['idtype'])) ? $aRet['idtype'] : 0);

            $ret = true;
            $result = array("message"=>"","object"=>$hdkServiceModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting item's info.", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Returns service's data
     * pt_br Retorna os dados do item
     *
     * @param  hdkServiceModel $hdkServiceModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function getService(hdkServiceModel $hdkServiceModel): array
    {        
        $sql = "SELECT a.idservice, iditem, idpriority,`name`,a.status,selected,classify,
                       time_attendance,hours_attendance,days_attendance,ind_hours_minutes, b.idgroup
                  FROM hdk_tbcore_service a, hdk_tbgroup_has_service b
                 WHERE a.idservice = b.idservice
                   AND a.idservice = :serviceId";
        //echo "{$sql}\n";
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':serviceId', $hdkServiceModel->getIdService());
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            $hdkServiceModel->setIdItem((!is_null($aRet['iditem'])) ? $aRet['iditem'] : 0)
                            ->setIdPriority((!is_null($aRet['idpriority'])) ? $aRet['idpriority'] : 0)
                            ->setServiceName((!is_null($aRet['name'])) ? $aRet['name'] : "")
                            ->setStatus((!is_null($aRet['status'])) ? $aRet['status'] : "I")
                            ->setFlagDefault((!is_null($aRet['selected'])) ? $aRet['selected'] : 0)
                            ->setFlagClassify((!is_null($aRet['classify'])) ? $aRet['classify'] : 0)
                            ->setAttendanceTime((!is_null($aRet['time_attendance'])) ? $aRet['time_attendance'] : 0)
                            ->setLimitTime((!is_null($aRet['hours_attendance'])) ? $aRet['hours_attendance'] : 0)
                            ->setLimitDays((!is_null($aRet['days_attendance'])) ? $aRet['days_attendance'] : 0)
                            ->setTimeType((!is_null($aRet['ind_hours_minutes'])) ? $aRet['ind_hours_minutes'] : "H")
                            ->setIdGroup((!is_null($aRet['idgroup'])) ? $aRet['idgroup'] : 0);

            $ret = true;
            $result = array("message"=>"","object"=>$hdkServiceModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting service's info.", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Updates area's status in DB
     * pt_br Atualiza o status da área no BD
     *
     * @param  hdkServiceModel $hdkServiceModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function updateAreaStatus(hdkServiceModel $hdkServiceModel): array
    {        
        $sql = "UPDATE hdk_tbcore_area SET status = :status WHERE idarea = :areaId";
        //echo "{$sql}\n";
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':status', $hdkServiceModel->getStatus());
            $stmt->bindParam(':areaId', $hdkServiceModel->getIdArea());
            $stmt->execute();

            $ret = true;
            $result = array("message"=>"","object"=>$hdkServiceModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error updating area's status ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Updates type's status in DB
     * pt_br Atualiza o status do tipo no BD
     *
     * @param  hdkServiceModel $hdkServiceModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function updateTypeStatus(hdkServiceModel $hdkServiceModel): array
    {        
        $sql = "UPDATE hdk_tbcore_type SET status = :status WHERE idtype = :typeId";
        //echo "{$sql}\n";
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':status', $hdkServiceModel->getStatus());
            $stmt->bindParam(':typeId', $hdkServiceModel->getIdType());
            $stmt->execute();

            $ret = true;
            $result = array("message"=>"","object"=>$hdkServiceModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error updating type's status ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Updates item's status in DB
     * pt_br Atualiza o status do item no BD
     *
     * @param  hdkServiceModel $hdkServiceModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function updateItemStatus(hdkServiceModel $hdkServiceModel): array
    {        
        $sql = "UPDATE hdk_tbcore_item SET status = :status WHERE iditem = :itemId";
        //echo "{$sql}\n";
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':status', $hdkServiceModel->getStatus());
            $stmt->bindParam(':itemId', $hdkServiceModel->getIdItem());
            $stmt->execute();

            $ret = true;
            $result = array("message"=>"","object"=>$hdkServiceModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error updating item's status ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Updates service's status in DB
     * pt_br Atualiza o status do serviço no BD
     *
     * @param  hdkServiceModel $hdkServiceModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function updateServiceStatus(hdkServiceModel $hdkServiceModel): array
    {        
        $sql = "UPDATE hdk_tbcore_service SET status = :status WHERE idservice = :serviceId";
        //echo "{$sql}\n";
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':status', $hdkServiceModel->getStatus());
            $stmt->bindParam(':serviceId', $hdkServiceModel->getIdService());
            $stmt->execute();

            $ret = true;
            $result = array("message"=>"","object"=>$hdkServiceModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error updating service's status ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Returns an array with areas to display in grid
     * pt_br Retorna um array com áreas a serem exibidas no grid
     *
     * @param  string $where
     * @param  string $group
     * @param  string $order
     * @param  string $limit
     * @return array  Parameters returned in array: 
     *                [status = true/false
     *                 push =  [message = PDO Exception message 
     *                          object = model's object]]
     */
    public function queryAreas($where=null,$group=null,$order=null,$limit=null): array
    {
        $sql = "SELECT idarea, `name`, `default`, `status`
                  FROM hdk_tbcore_area 
                $where $group $order $limit";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $serviceModel = new hdkServiceModel();
            $serviceModel->setAreaList($aRet);

            $ret = true;
            $result = array("message"=>"","object"=>$serviceModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting areas ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Returns an array with types to display in grid
     * pt_br Retorna um array com tipos a serem exibidos no grid
     *
     * @param  string $where
     * @param  string $group
     * @param  string $order
     * @param  string $limit
     * @return array  Parameters returned in array: 
     *                [status = true/false
     *                 push =  [message = PDO Exception message 
     *                          object = model's object]]
     */
    public function queryTypes($where=null,$group=null,$order=null,$limit=null): array
    {
        $sql = "SELECT idtype, `name`, `status`, `selected`, `classify`, idarea
                  FROM hdk_tbcore_type 
                $where $group $order $limit";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $serviceModel = new hdkServiceModel();
            $serviceModel->setTypeList($aRet);

            $ret = true;
            $result = array("message"=>"","object"=>$serviceModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting types ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Returns an array with items to display in grid
     * pt_br Retorna um array com itens a serem exibidos no grid
     *
     * @param  string $where
     * @param  string $group
     * @param  string $order
     * @param  string $limit
     * @return array  Parameters returned in array: 
     *                [status = true/false
     *                 push =  [message = PDO Exception message 
     *                          object = model's object]]
     */
    public function queryItems($where=null,$group=null,$order=null,$limit=null): array
    {
        $sql = "SELECT iditem, `name`, `status`, `selected`, `classify`, idtype
                  FROM hdk_tbcore_item 
                $where $group $order $limit";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $serviceModel = new hdkServiceModel();
            $serviceModel->setItemList($aRet);

            $ret = true;
            $result = array("message"=>"","object"=>$serviceModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting items ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Returns an array with services to display in grid
     * pt_br Retorna um array com serviços a serem exibidos no grid
     *
     * @param  string $where
     * @param  string $group
     * @param  string $order
     * @param  string $limit
     * @return array  Parameters returned in array: 
     *                [status = true/false
     *                 push =  [message = PDO Exception message 
     *                          object = model's object]]
     */
    public function queryServices($where=null,$group=null,$order=null,$limit=null): array
    {
        $sql = "SELECT a.idservice, iditem, idpriority,`name`,a.status,selected,classify,
                        time_attendance,hours_attendance,days_attendance,ind_hours_minutes, b.idgroup
                  FROM hdk_tbcore_service a, hdk_tbgroup_has_service b
                 WHERE a.idservice = b.idservice
                $where $group $order $limit";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $serviceModel = new hdkServiceModel();
            $serviceModel->setServiceList($aRet);

            $ret = true;
            $result = array("message"=>"","object"=>$serviceModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting services ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Returns an array of services linked with tickets
     * pt_br Retorna uma matriz de serviços vinculados a tickets
     *
     * @param  string $where
     * @param  string $group
     * @param  string $order
     * @param  string $limit
     * @return array  Parameters returned in array: 
     *                [status = true/false
     *                 push =  [message = PDO Exception message 
     *                          object = model's object]]
     */
    public function queryTicketService($where=null,$group=null,$order=null,$limit=null): array
    {
        $sql = "SELECT code_request FROM hdk_viewRequestData $where $group $order $limit";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $serviceModel = new hdkServiceModel();
            $serviceModel->setTicketList($aRet);

            $ret = true;
            $result = array("message"=>"","object"=>$serviceModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting tickets linked with services ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Removes the default flag in hdk_tbcore_area table
     * pt_br Elimina o sinalizador da área default na tabela hdk_tbcore_area
     *
     * @param  hdkServiceModel $hdkServiceModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function removeAreaDefault(hdkServiceModel $hdkServiceModel): array
    {        
        $sql = "UPDATE hdk_tbcore_area SET `default` = 0 WHERE `default` = 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        $ret = true;
        $result = array("message"=>"","object"=>$hdkServiceModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Inserts area data into hdk_tbcore_area table
     * pt_br Insere os dados da área na tabela hdk_tbcore_area
     *
     * @param  hdkServiceModel $hdkServiceModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function insertArea(hdkServiceModel $hdkServiceModel): array
    {        
        $sql = "INSERT INTO hdk_tbcore_area (`name`,`default`) 
                     VALUES (:name,:default)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":name",$hdkServiceModel->getAreaName());
        $stmt->bindValue(":default",$hdkServiceModel->getFlagDefault());
        $stmt->execute();

        $hdkServiceModel->setIdArea($this->db->lastInsertId());

        $ret = true;
        $result = array("message"=>"","object"=>$hdkServiceModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Saves the new area into DB
     * pt_br Grava a nova área no banco de dados
     *
     * @param  hdkServiceModel $hdkServiceModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function saveArea(hdkServiceModel $hdkServiceModel): array
    {   
        try{
            $this->db->beginTransaction();

            if($hdkServiceModel->getFlagDefault() == 1){
                $remDefault = $this->removeAreaDefault($hdkServiceModel);
            }

            $ins = $this->insertArea($hdkServiceModel);
            
            $ret = true;
            $result = array("message"=>"","object"=>$ins['push']['object']);
            $this->db->commit();
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error trying save area data', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
            $this->db->rollBack();
        }         
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Updates area data into hdk_tbcore_area table
     * pt_br Atualiza os dados da área na tabela hdk_tbcore_area
     *
     * @param  hdkServiceModel $hdkServiceModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function updateArea(hdkServiceModel $hdkServiceModel): array
    {        
        $sql = "UPDATE hdk_tbcore_area 
                   SET `name` = :name,
                       `default` = :default
                 WHERE idarea = :areaId";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":name",$hdkServiceModel->getAreaName());
        $stmt->bindValue(":default",$hdkServiceModel->getFlagDefault());
        $stmt->bindValue(":areaId",$hdkServiceModel->getIdArea());
        $stmt->execute();

        $ret = true;
        $result = array("message"=>"","object"=>$hdkServiceModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Updates area's data into DB
     * pt_br Atualiza os dados da área no banco de dados
     *
     * @param  hdkServiceModel $hdkServiceModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function saveUpdateArea(hdkServiceModel $hdkServiceModel): array
    {   
        try{
            $this->db->beginTransaction();

            if($hdkServiceModel->getFlagDefault() == 1){
                $remDefault = $this->removeAreaDefault($hdkServiceModel);
            }

            $upd = $this->updateArea($hdkServiceModel);
            
            $ret = true;
            $result = array("message"=>"","object"=>$upd['push']['object']);
            $this->db->commit();
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error trying update area data', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
            $this->db->rollBack();
        }         
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Removes the default flag in hdk_tbcore_type table
     * pt_br Elimina o sinalizador do tipo default na tabela hdk_tbcore_type
     *
     * @param  hdkServiceModel $hdkServiceModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function removeTypeDefault(hdkServiceModel $hdkServiceModel): array
    {        
        $sql = "UPDATE hdk_tbcore_type SET `selected` = 0 WHERE `selected` = 1 AND idarea = :areaId";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":areaId",$hdkServiceModel->getIdArea());
        $stmt->execute();

        $ret = true;
        $result = array("message"=>"","object"=>$hdkServiceModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Inserts type data into hdk_tbcore_type table
     * pt_br Insere os dados do tipo na tabela hdk_tbcore_type
     *
     * @param  hdkServiceModel $hdkServiceModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function insertType(hdkServiceModel $hdkServiceModel): array
    {        
        $sql = "INSERT INTO hdk_tbcore_type (`name`,`status`,`selected`,`classify`,`idarea`) 
                     VALUES (:name,:status,:default,:classify,:areaId)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":name",$hdkServiceModel->getTypeName());
        $stmt->bindValue(":status",$hdkServiceModel->getStatus());
        $stmt->bindValue(":default",$hdkServiceModel->getFlagDefault());
        $stmt->bindValue(":classify",$hdkServiceModel->getFlagClassify());
        $stmt->bindValue(":areaId",$hdkServiceModel->getIdArea());
        $stmt->execute();

        $hdkServiceModel->setIdType($this->db->lastInsertId());

        $ret = true;
        $result = array("message"=>"","object"=>$hdkServiceModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Saves the new type into DB
     * pt_br Grava o novo tipo no banco de dados
     *
     * @param  hdkServiceModel $hdkServiceModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function saveType(hdkServiceModel $hdkServiceModel): array
    {   
        try{
            $this->db->beginTransaction();

            if($hdkServiceModel->getFlagDefault() == 1){
                $remDefault = $this->removeTypeDefault($hdkServiceModel);
            }

            $ins = $this->insertType($hdkServiceModel);
            
            $ret = true;
            $result = array("message"=>"","object"=>$ins['push']['object']);
            $this->db->commit();
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error trying save type data', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
            $this->db->rollBack();
        }         
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Updates type data into hdk_tbcore_type table
     * pt_br Atualiza os dados do tipo na tabela hdk_tbcore_type
     *
     * @param  hdkServiceModel $hdkServiceModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function updateType(hdkServiceModel $hdkServiceModel): array
    {        
        $sql = "UPDATE hdk_tbcore_type 
                   SET `name` = :name,
                       `status` = :status,
                       `selected` = :default,
                       `classify` = :classify,
                       `idarea` = :areaId
                 WHERE idtype = :typeId";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":name",$hdkServiceModel->getTypeName());
        $stmt->bindValue(":status",$hdkServiceModel->getStatus());
        $stmt->bindValue(":default",$hdkServiceModel->getFlagDefault());
        $stmt->bindValue(":classify",$hdkServiceModel->getFlagClassify());
        $stmt->bindValue(":areaId",$hdkServiceModel->getIdArea());
        $stmt->bindValue(":typeId",$hdkServiceModel->getIdType());
        $stmt->execute();

        $ret = true;
        $result = array("message"=>"","object"=>$hdkServiceModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Updates type's data into DB
     * pt_br Atualiza os dados do tipo no banco de dados
     *
     * @param  hdkServiceModel $hdkServiceModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function saveUpdateType(hdkServiceModel $hdkServiceModel): array
    {   
        try{
            $this->db->beginTransaction();

            if($hdkServiceModel->getFlagDefault() == 1){
                $remDefault = $this->removeTypeDefault($hdkServiceModel);
            }

            $upd = $this->updateType($hdkServiceModel);
            
            $ret = true;
            $result = array("message"=>"","object"=>$upd['push']['object']);
            $this->db->commit();
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error trying update Type data', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
            $this->db->rollBack();
        }         
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Removes the default flag in hdk_tbcore_item table
     * pt_br Elimina o sinalizador do item default na tabela hdk_tbcore_item
     *
     * @param  hdkServiceModel $hdkServiceModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function removeItemDefault(hdkServiceModel $hdkServiceModel): array
    {        
        $sql = "UPDATE hdk_tbcore_item SET `selected` = 0 WHERE `selected` = 1 AND idtype = :typeId";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":typeId",$hdkServiceModel->getIdType());
        $stmt->execute();

        $ret = true;
        $result = array("message"=>"","object"=>$hdkServiceModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Inserts item data into hdk_tbcore_item table
     * pt_br Insere os dados do item na tabela hdk_tbcore_item
     *
     * @param  hdkServiceModel $hdkServiceModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function insertItem(hdkServiceModel $hdkServiceModel): array
    {        
        $sql = "INSERT INTO hdk_tbcore_item (`name`,`status`,`selected`,`classify`,`idtype`) 
                     VALUES (:name,:status,:default,:classify,:typeId)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":name",$hdkServiceModel->getItemName());
        $stmt->bindValue(":status",$hdkServiceModel->getStatus());
        $stmt->bindValue(":default",$hdkServiceModel->getFlagDefault());
        $stmt->bindValue(":classify",$hdkServiceModel->getFlagClassify());
        $stmt->bindValue(":typeId",$hdkServiceModel->getIdType());
        $stmt->execute();

        $hdkServiceModel->setIdItem($this->db->lastInsertId());

        $ret = true;
        $result = array("message"=>"","object"=>$hdkServiceModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Saves the new item into DB
     * pt_br Grava o novo item no banco de dados
     *
     * @param  hdkServiceModel $hdkServiceModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function saveItem(hdkServiceModel $hdkServiceModel): array
    {   
        try{
            $this->db->beginTransaction();

            if($hdkServiceModel->getFlagDefault() == 1){
                $remDefault = $this->removeItemDefault($hdkServiceModel);
            }

            $ins = $this->insertItem($hdkServiceModel);
            
            $ret = true;
            $result = array("message"=>"","object"=>$ins['push']['object']);
            $this->db->commit();
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error trying save item data', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
            $this->db->rollBack();
        }         
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Updates item data into hdk_tbcore_item table
     * pt_br Atualiza os dados do item na tabela hdk_tbcore_item
     *
     * @param  hdkServiceModel $hdkServiceModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function updateItem(hdkServiceModel $hdkServiceModel): array
    {        
        $sql = "UPDATE hdk_tbcore_item 
                   SET `name` = :name,
                       `status` = :status,
                       `selected` = :default,
                       `classify` = :classify
                 WHERE iditem = :itemId";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":name",$hdkServiceModel->getItemName());
        $stmt->bindValue(":status",$hdkServiceModel->getStatus());
        $stmt->bindValue(":default",$hdkServiceModel->getFlagDefault());
        $stmt->bindValue(":classify",$hdkServiceModel->getFlagClassify());
        $stmt->bindValue(":itemId",$hdkServiceModel->getIdItem());
        $stmt->execute();

        $ret = true;
        $result = array("message"=>"","object"=>$hdkServiceModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Updates item's data into DB
     * pt_br Atualiza os dados do item no banco de dados
     *
     * @param  hdkServiceModel $hdkServiceModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function saveUpdateItem(hdkServiceModel $hdkServiceModel): array
    {   
        try{
            $this->db->beginTransaction();

            if($hdkServiceModel->getFlagDefault() == 1){
                $remDefault = $this->removeItemDefault($hdkServiceModel);
            }

            $upd = $this->updateItem($hdkServiceModel);
            
            $ret = true;
            $result = array("message"=>"","object"=>$upd['push']['object']);
            $this->db->commit();
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error trying update item data', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
            $this->db->rollBack();
        }         
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Removes the default flag in hdk_tbcore_service table
     * pt_br Elimina o sinalizador do serviço default na tabela hdk_tbcore_service
     *
     * @param  hdkServiceModel $hdkServiceModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function removeServiceDefault(hdkServiceModel $hdkServiceModel): array
    {        
        $sql = "UPDATE hdk_tbcore_service SET `selected` = 0 WHERE `selected` = 1 AND iditem = :itemId";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":itemId",$hdkServiceModel->getIdItem());
        $stmt->execute();

        $ret = true;
        $result = array("message"=>"","object"=>$hdkServiceModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Inserts service data into hdk_tbcore_service table
     * pt_br Insere os dados do serviço na tabela hdk_tbcore_service
     *
     * @param  hdkServiceModel $hdkServiceModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function insertService(hdkServiceModel $hdkServiceModel): array
    {        
        $sql = "INSERT INTO hdk_tbcore_service (`iditem`,`idpriority`,`name`,`status`,`selected`,`classify`,`time_attendance`,
                                                `hours_attendance`,`days_attendance`,`ind_hours_minutes`) 
                     VALUES (:itemId,:priorityId,:name,:status,:default,:classify,:attendanceTime,:limitTime,:limitDays,:timeType)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":itemId",$hdkServiceModel->getIdItem());
        $stmt->bindValue(":priorityId",$hdkServiceModel->getIdPriority());
        $stmt->bindValue(":name",$hdkServiceModel->getServiceName());
        $stmt->bindValue(":status",$hdkServiceModel->getStatus());
        $stmt->bindValue(":default",$hdkServiceModel->getFlagDefault());
        $stmt->bindValue(":classify",$hdkServiceModel->getFlagClassify());
        $stmt->bindValue(":attendanceTime",$hdkServiceModel->getAttendanceTime());
        $stmt->bindValue(":limitTime",$hdkServiceModel->getLimitTime());
        $stmt->bindValue(":limitDays",$hdkServiceModel->getLimitDays());
        $stmt->bindValue(":timeType",$hdkServiceModel->getTimeType());
        $stmt->execute();

        $hdkServiceModel->setIdService($this->db->lastInsertId());

        $ret = true;
        $result = array("message"=>"","object"=>$hdkServiceModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Inserts link between service and attendance group into hdk_tbgroup_has_service table
     * pt_br Insere o vínculo entre o serviço e o grupo de atendimento na tabela hdk_tbgroup_has_service
     *
     * @param  hdkServiceModel $hdkServiceModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function insertServiceGroup(hdkServiceModel $hdkServiceModel): array
    {        
        $sql = "INSERT INTO hdk_tbgroup_has_service (`idgroup`,`idservice`) 
                     VALUES (:groupId,:serviceId)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":groupId",$hdkServiceModel->getIdGroup());
        $stmt->bindValue(":serviceId",$hdkServiceModel->getIdService());
        $stmt->execute();

        $ret = true;
        $result = array("message"=>"","object"=>$hdkServiceModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Saves the new service into DB
     * pt_br Grava o novo serviço no banco de dados
     *
     * @param  hdkServiceModel $hdkServiceModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function saveService(hdkServiceModel $hdkServiceModel): array
    {   
        try{
            $this->db->beginTransaction();

            if($hdkServiceModel->getFlagDefault() == 1){
                $remDefault = $this->removeServiceDefault($hdkServiceModel);
            }

            $ins = $this->insertService($hdkServiceModel);

            if($ins['status']){
                $insGroup = $this->insertServiceGroup($ins['push']['object']);
            }
            
            $ret = true;
            $result = array("message"=>"","object"=>$ins['push']['object']);
            $this->db->commit();
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error trying save service data', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
            $this->db->rollBack();
        }         
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Updates service data into hdk_tbcore_service table
     * pt_br Atualiza os dados do item na tabela hdk_tbcore_service
     *
     * @param  hdkServiceModel $hdkServiceModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function updateService(hdkServiceModel $hdkServiceModel): array
    {        
        $sql = "UPDATE hdk_tbcore_service 
                   SET `idpriority` = :priorityId,
                       `name` = :name,
                       `status` = :status,
                       `selected` = :default,
                       `classify` = :classify,
                       `time_attendance` = :attendanceTime,
                       `hours_attendance` = :limitTime,
                       `days_attendance` = :limitDays,
                       `ind_hours_minutes` = :timeType
                 WHERE idservice = :serviceId";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":priorityId",$hdkServiceModel->getIdPriority());
        $stmt->bindValue(":name",$hdkServiceModel->getServiceName());
        $stmt->bindValue(":status",$hdkServiceModel->getStatus());
        $stmt->bindValue(":default",$hdkServiceModel->getFlagDefault());
        $stmt->bindValue(":classify",$hdkServiceModel->getFlagClassify());
        $stmt->bindValue(":attendanceTime",$hdkServiceModel->getAttendanceTime());
        $stmt->bindValue(":limitTime",$hdkServiceModel->getLimitTime());
        $stmt->bindValue(":limitDays",$hdkServiceModel->getLimitDays());
        $stmt->bindValue(":timeType",$hdkServiceModel->getTimeType());
        $stmt->bindValue(":serviceId",$hdkServiceModel->getIdService());
        $stmt->execute();

        $ret = true;
        $result = array("message"=>"","object"=>$hdkServiceModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Updates link between service and attendance group into hdk_tbgroup_has_service table
     * pt_br Atualiza o vínculo entre o serviço e o grupo de atendimento na tabela hdk_tbgroup_has_service
     *
     * @param  hdkServiceModel $hdkServiceModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function updateServiceGroup(hdkServiceModel $hdkServiceModel): array
    {        
        $sql = "UPDATE hdk_tbgroup_has_service 
                   SET `idgroup` = :groupId
                 WHERE `idservice` = :serviceId";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":groupId",$hdkServiceModel->getIdGroup());
        $stmt->bindValue(":serviceId",$hdkServiceModel->getIdService());
        $stmt->execute();

        $ret = true;
        $result = array("message"=>"","object"=>$hdkServiceModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Updates service's data into DB
     * pt_br Atualiza os dados do serviço no banco de dados
     *
     * @param  hdkServiceModel $hdkServiceModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function saveUpdateService(hdkServiceModel $hdkServiceModel): array
    {   
        try{
            $this->db->beginTransaction();

            if($hdkServiceModel->getFlagDefault() == 1){
                $remDefault = $this->removeServiceDefault($hdkServiceModel);
            }

            $upd = $this->updateService($hdkServiceModel);
            if($upd['status']){
                $updGroup = $this->updateServiceGroup($upd['push']['object']);
            }
            
            $ret = true;
            $result = array("message"=>"","object"=>$upd['push']['object']);
            $this->db->commit();
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error trying update service data', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
            $this->db->rollBack();
        }         
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Gets a list with ids of area, type, item, or service
     * pt_br Obtém uma lista com ids de área, tipo, item ou serviço
     *
     * @param  hdkServiceModel $hdkServiceModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchTargetIds(hdkServiceModel $hdkServiceModel): array
    {        
        $field = $hdkServiceModel->getTargetField();
        $where = $hdkServiceModel->getTargetCondition();

        $sql = "SELECT DISTINCT $field targetId
                  FROM hdk_tbcore_service a, hdk_tbcore_item b, hdk_tbcore_type c
                 WHERE a.iditem = b.iditem
                   AND b.idtype = c.idtype
                 $where";
       
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $hdkServiceModel->setTargetIdList((!is_null($aRet) && !empty($aRet)) ? implode(',',array_column($aRet,'targetId')) : "");

        $ret = true;
        $result = array("message"=>"","object"=>$hdkServiceModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Remove link between service and attendance group into hdk_tbgroup_has_service table
     * pt_br Deleta o vínculo entre o serviço e o grupo de atendimento na tabela hdk_tbgroup_has_service
     *
     * @param  hdkServiceModel $hdkServiceModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function deleteServiceGroup(hdkServiceModel $hdkServiceModel): array
    {        
        $serviceList = $hdkServiceModel->getTargetIdList();
        $sql = "DELETE FROM hdk_tbgroup_has_service WHERE idservice IN ({$serviceList})";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        $ret = true;
        $result = array("message"=>"","object"=>$hdkServiceModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Remove service data from hdk_tbcore_service table
     * pt_br Deleta os dados do serviço da tabela hdk_tbcore_service
     *
     * @param  hdkServiceModel $hdkServiceModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function deleteService(hdkServiceModel $hdkServiceModel): array
    {        
        $serviceList = $hdkServiceModel->getTargetIdList();
        $sql = "DELETE FROM hdk_tbcore_service WHERE idservice IN ({$serviceList})";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        $ret = true;
        $result = array("message"=>"","object"=>$hdkServiceModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Remove item data from hdk_tbcore_item table
     * pt_br Deleta os dados do item da tabela hdk_tbcore_item
     *
     * @param  hdkServiceModel $hdkServiceModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function deleteItem(hdkServiceModel $hdkServiceModel): array
    {        
        $itemList = $hdkServiceModel->getTargetIdList();
        $sql = "DELETE FROM hdk_tbcore_item WHERE iditem IN ({$itemList})";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        $ret = true;
        $result = array("message"=>"","object"=>$hdkServiceModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Remove type data from hdk_tbcore_type table
     * pt_br Deleta os dados do tipo da tabela hdk_tbcore_type
     *
     * @param  hdkServiceModel $hdkServiceModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function deleteType(hdkServiceModel $hdkServiceModel): array
    {        
        $typeList = $hdkServiceModel->getTargetIdList();
        $sql = "DELETE FROM hdk_tbcore_type WHERE idtype IN ({$typeList})";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        $ret = true;
        $result = array("message"=>"","object"=>$hdkServiceModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Remove area data from hdk_tbcore_area table
     * pt_br Deleta os dados do tipo da tabela hdk_tbcore_area
     *
     * @param  hdkServiceModel $hdkServiceModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function deleteArea(hdkServiceModel $hdkServiceModel): array
    {        
        $areaList = $hdkServiceModel->getTargetIdList();
        $sql = "DELETE FROM hdk_tbcore_area WHERE idarea IN ({$areaList})";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        $ret = true;
        $result = array("message"=>"","object"=>$hdkServiceModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Remove area from DB
     * pt_br Exclui area do banco de dados
     *
     * @param  hdkServiceModel $hdkServiceModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function removeArea(hdkServiceModel $hdkServiceModel): array
    {   
        try{
            $this->db->beginTransaction();

            $hdkServiceModel->setTargetCondition("AND c.idarea = {$hdkServiceModel->getIdArea()}");
            $areaId = $hdkServiceModel->getIdArea();

            $hdkServiceModel->setTargetField("a.idservice");
            $retServices =  $this->fetchTargetIds($hdkServiceModel);
            if($retServices['status'] && !empty($retServices['push']['object']->getTargetIdList())){
                $servicesIds = $retServices['push']['object']->getTargetIdList();
            }
            
            $hdkServiceModel->setTargetField("a.iditem");
            $retItems =  $this->fetchTargetIds($hdkServiceModel);
            if($retItems['status'] && !empty($retItems['push']['object']->getTargetIdList())){
                $itemsIds = $retItems['push']['object']->getTargetIdList();
            }

            $hdkServiceModel->setTargetField("b.idtype");
            $retTypes =  $this->fetchTargetIds($hdkServiceModel);
            if($retTypes['status'] && !empty($retTypes['push']['object']->getTargetIdList())){
                $typesIds = $retTypes['push']['object']->getTargetIdList();
            }

            if(!empty($servicesIds)){
                $hdkServiceModel->setTargetIdList($servicesIds);
                $delServiceGroup = $this->deleteServiceGroup($hdkServiceModel);
                $delService = $this->deleteService($hdkServiceModel);
            }

            if(!empty($itemsIds)){
                $hdkServiceModel->setTargetIdList($itemsIds);
                $delItem = $this->deleteItem($hdkServiceModel);
            }

            if(!empty($typesIds)){
                $hdkServiceModel->setTargetIdList($typesIds);
                $delType = $this->deleteType($hdkServiceModel);
            }

            $hdkServiceModel->setTargetIdList($areaId);
            $delArea = $this->deleteArea($hdkServiceModel);
 
            $ret = true;
            $result = array("message"=>"","object"=>$hdkServiceModel);
            $this->db->commit();
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error trying delete area data', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
            $this->db->rollBack();
        }         
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Remove type from DB
     * pt_br Exclui o tipo do banco de dados
     *
     * @param  hdkServiceModel $hdkServiceModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function removeType(hdkServiceModel $hdkServiceModel): array
    {   
        try{
            $this->db->beginTransaction();

            $hdkServiceModel->setTargetCondition("AND b.idtype = {$hdkServiceModel->getIdType()}");
            $typeId = $hdkServiceModel->getIdType();

            $hdkServiceModel->setTargetField("a.idservice");
            $retServices =  $this->fetchTargetIds($hdkServiceModel);
            if($retServices['status'] && !empty($retServices['push']['object']->getTargetIdList())){
                $servicesIds = $retServices['push']['object']->getTargetIdList();
            }
            
            $hdkServiceModel->setTargetField("a.iditem");
            $retItems =  $this->fetchTargetIds($hdkServiceModel);
            if($retItems['status'] && !empty($retItems['push']['object']->getTargetIdList())){
                $itemsIds = $retItems['push']['object']->getTargetIdList();
            }

            if(!empty($servicesIds)){
                $hdkServiceModel->setTargetIdList($servicesIds);
                $delServiceGroup = $this->deleteServiceGroup($hdkServiceModel);
                $delService = $this->deleteService($hdkServiceModel);
            }

            if(!empty($itemsIds)){
                $hdkServiceModel->setTargetIdList($itemsIds);
                $delItem = $this->deleteItem($hdkServiceModel);
            }

            $hdkServiceModel->setTargetIdList($typeId);
            $delType = $this->deleteType($hdkServiceModel);
 
            $ret = true;
            $result = array("message"=>"","object"=>$hdkServiceModel);
            $this->db->commit();
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error trying delete type data', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
            $this->db->rollBack();
        }         
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Remove item from DB
     * pt_br Exclui o item do banco de dados
     *
     * @param  hdkServiceModel $hdkServiceModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function removeItem(hdkServiceModel $hdkServiceModel): array
    {   
        try{
            $this->db->beginTransaction();

            $hdkServiceModel->setTargetCondition("AND a.iditem = {$hdkServiceModel->getIdItem()}");
            $itemId = $hdkServiceModel->getIdItem();

            $hdkServiceModel->setTargetField("a.idservice");
            $retServices =  $this->fetchTargetIds($hdkServiceModel);
            if($retServices['status'] && !empty($retServices['push']['object']->getTargetIdList())){
                $servicesIds = $retServices['push']['object']->getTargetIdList();
            }
            
            if(!empty($servicesIds)){
                $hdkServiceModel->setTargetIdList($servicesIds);
                $delServiceGroup = $this->deleteServiceGroup($hdkServiceModel);
                $delService = $this->deleteService($hdkServiceModel);
            }

            $hdkServiceModel->setTargetIdList($itemId);
            $delItem = $this->deleteItem($hdkServiceModel);
 
            $ret = true;
            $result = array("message"=>"","object"=>$hdkServiceModel);
            $this->db->commit();
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error trying delete item data', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
            $this->db->rollBack();
        }         
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Remove service from DB
     * pt_br Exclui o service do banco de dados
     *
     * @param  hdkServiceModel $hdkServiceModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function removeService(hdkServiceModel $hdkServiceModel): array
    {   
        try{
            $this->db->beginTransaction();

            $serviceId = $hdkServiceModel->getIdService();

            $hdkServiceModel->setTargetIdList($serviceId);
            $delServiceGroup = $this->deleteServiceGroup($hdkServiceModel);
            $delService = $this->deleteService($hdkServiceModel);
 
            $ret = true;
            $result = array("message"=>"","object"=>$hdkServiceModel);
            $this->db->commit();
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error trying delete service data', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
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
     * @param  hdkServiceModel $hdkServiceModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchAllServices(hdkServiceModel $hdkServiceModel): array
    {        
        $sql = "SELECT a.idservice, a.iditem, d.name area_name, c.name type_name, b.name item_name, a.name service_name
                  FROM hdk_tbcore_service a, hdk_tbcore_item b, hdk_tbcore_type c, hdk_tbcore_area d
                 WHERE a.iditem = b.iditem
                   AND b.idtype = c.idtype
                   AND c.idarea = d.idarea
              ORDER BY area_name,type_name,item_name,service_name";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $hdkServiceModel->setServiceList($aRet);

            $ret = true;
            $result = array("message"=>"","object"=>$hdkServiceModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting service list. ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * fetchServiceGroup
     * 
     * en_us Returns a list of all service's groups
     * pt_br Retorna uma lista de todos os grupos do servços
     *
     * @param  hdkServiceModel $hdkServiceModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchServiceGroup(hdkServiceModel $hdkServiceModel): array
    {        
        $sql = "SELECT b.idgroup, d.name group_name, c.level
                  FROM hdk_tbcore_service a, hdk_tbgroup_has_service b, hdk_tbgroup c, tbperson d
                 WHERE a.idservice = b.idservice
                   AND b.idgroup = c.idgroup
                   AND c.idperson = d.idperson
                   AND a.idservice = :serviceId
              ORDER BY group_name";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':serviceId', $hdkServiceModel->getIdService());
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $hdkServiceModel->setGroupList(($aRet && !is_null($aRet)) ? $aRet : array());

            $ret = true;
            $result = array("message"=>"","object"=>$hdkServiceModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting service's groups ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * getServiceCore
     * 
     * en_us Returns service's core data
     * pt_br Retorna os dados principais do serviço
     *
     * @param  mixed $hdkServiceModel
     * @return array
     */
    public function getServiceCore(hdkServiceModel $hdkServiceModel): array
    {        
        $sql = "SELECT a.idservice, a.iditem, a.idpriority,a.name,a.status,a.selected,a.classify,
                       time_attendance,hours_attendance,days_attendance,ind_hours_minutes, b.idgroup, c.name item_name,
                       c.idtype, d.name type_name, d.idarea, e.name area_name
                  FROM hdk_tbcore_service a, hdk_tbgroup_has_service b, hdk_tbcore_item c, hdk_tbcore_type d, hdk_tbcore_area e
                 WHERE a.idservice = b.idservice
                   AND a.iditem = c.iditem
                   AND c.idtype = d.idtype
                   AND d.idarea = e.idarea
                   AND a.idservice = :serviceId";
        //echo "{$sql}\n";
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':serviceId', $hdkServiceModel->getIdService());
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            $hdkServiceModel->setIdItem((!is_null($aRet['iditem'])) ? $aRet['iditem'] : 0)
                            ->setIdPriority((!is_null($aRet['idpriority'])) ? $aRet['idpriority'] : 0)
                            ->setServiceName((!is_null($aRet['name'])) ? $aRet['name'] : "")
                            ->setStatus((!is_null($aRet['status'])) ? $aRet['status'] : "I")
                            ->setFlagDefault((!is_null($aRet['selected'])) ? $aRet['selected'] : 0)
                            ->setFlagClassify((!is_null($aRet['classify'])) ? $aRet['classify'] : 0)
                            ->setAttendanceTime((!is_null($aRet['time_attendance'])) ? $aRet['time_attendance'] : 0)
                            ->setLimitTime((!is_null($aRet['hours_attendance'])) ? $aRet['hours_attendance'] : 0)
                            ->setLimitDays((!is_null($aRet['days_attendance'])) ? $aRet['days_attendance'] : 0)
                            ->setTimeType((!is_null($aRet['ind_hours_minutes'])) ? $aRet['ind_hours_minutes'] : "H")
                            ->setIdGroup((!is_null($aRet['idgroup'])) ? $aRet['idgroup'] : 0)
                            ->setItemName((!is_null($aRet['item_name'])) ? $aRet['item_name'] : "")
                            ->setIdType((!is_null($aRet['idtype'])) ? $aRet['idtype'] : 0)
                            ->setTypeName((!is_null($aRet['type_name'])) ? $aRet['type_name'] : "")
                            ->setIdArea((!is_null($aRet['idarea'])) ? $aRet['idarea'] : 0)
                            ->setAreaName((!is_null($aRet['area_name'])) ? $aRet['area_name'] : "");

            $ret = true;
            $result = array("message"=>"","object"=>$hdkServiceModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting service's core info.", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
}