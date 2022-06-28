<?php

namespace App\modules\helpdezk\dao\mysql;

use App\core\Database;

use App\modules\helpdezk\models\mysql\expireDateModel;

class expireDateDAO extends Database
{
    public function __construct()
    {
        parent::__construct(); 
    }

    /**
     * Returns a object with bussiness days
     *
     * @param  expireDateModel $expireDateModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchBusinessDays(expireDateModel $expireDateModel): array
    {        
        $sql = "SELECT num_day_week, begin_morning, end_morning, begin_afternoon, end_afternoon 
                  FROM hdk_tbwork_calendar_new 
                 WHERE business_day = 1";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $expireDateModel->setBusinessDays($aRet);

            $ret = true;
            $result = array("message"=>"","object"=>$expireDateModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting business days. ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * Returns an object with the service's attendance data
     *
     * @param  expireDateModel $expireDateModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function getExpireDateService(expireDateModel $expireDateModel): array
    {        
        $sql = "SELECT grp.idcustomer, hours_attendance, days_attendance, ind_hours_minutes
                  FROM hdk_tbgroup grp, hdk_tbcore_service serv, hdk_tbgroup_has_service grp_serv
                 WHERE grp.idgroup = grp_serv.idgroup
                   AND serv.idservice = grp_serv.idservice
                   AND serv.idservice = :serviceID";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':serviceID', $expireDateModel->getIdService());
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            $expireDateModel->setIdCustomer($aRet['idcustomer'])
                            ->setAttendanceDays($aRet['hours_attendance'])
                            ->setAttendanceHours($aRet['days_attendance'])
                            ->setTimeType($aRet['ind_hours_minutes']);

            $ret = true;
            $result = array("message"=>"","object"=>$expireDateModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting service's expiry data. ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * Returns an object with the priority's attendance data
     *
     * @param  expireDateModel $expireDateModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function getExpireDatePriority(expireDateModel $expireDateModel): array
    {        
        $sql = "SELECT limit_hours, limit_days FROM hdk_tbpriority WHERE idpriority = :priorityID";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':priorityID', $expireDateModel->getIdPriority());
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            $expireDateModel->setAttendanceDays($aRet['limit_days'])
                            ->setAttendanceHours($aRet['limit_hours']);

            $ret = true;
            $result = array("message"=>"","object"=>$expireDateModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting priority's expiry data. ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
    }

    
}