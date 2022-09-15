<?php

namespace App\modules\helpdezk\dao\mysql;

use App\core\Database;

use App\modules\helpdezk\dao\mysql\ticketRulesDAO;

use App\modules\helpdezk\models\mysql\ticketModel;
use App\modules\helpdezk\models\mysql\ticketRulesModel;

class ticketDAO extends Database
{
    public function __construct()
    {
        parent::__construct(); 
    }

    /**
     * Return an array with ticket to display in grid
     *
     * @param  string $where
     * @param  string $group
     * @param  string $order
     * @param  string $limit
     * @return array
     */
    public function queryTickets($where=null,$group=null,$order=null,$limit=null): array
    {        
        $sql = "SELECT DISTINCT a.code_request, a.subject, resp.name AS in_charge, inch.id_in_charge,
                        a.expire_date, a.entry_date, b.user_view AS statusview, a.idtype, a.idperson_owner AS owner,
                        b.idstatus AS status, b.color AS color_status, a.flag_opened, pers.name AS personname,
                        (SELECT COUNT(idrequest_attachment)
                           FROM hdk_tbrequest_attachment
                          WHERE code_request = a.code_request) AS totatt
                  FROM (hdk_tbrequest a, hdk_tbstatus b, tbperson pers,
                        tbperson resp, hdk_tbrequest_in_charge inch)
                 WHERE a.idstatus = b.idstatus
                   AND a.idperson_owner = pers.idperson
                   AND inch.id_in_charge = resp.idperson
                   AND inch.code_request = a.code_request
                   AND a.code_request = inch.code_request
                   AND inch.ind_in_charge = 1
                $where $group $order $limit";
        //echo "{$sql}\n";
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $ticket = new ticketModel(); 
            $ticket->setGridList($aRet);

            $ret = true;
            $result = array("message"=>"","object"=>$ticket);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting tickets ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * Return an array with rows total for grid pagination 
     *
     * @param  string $where
     * @param  string $group
     * @param  string $order
     * @param  string $limit
     * @return array
     */
    public function countTickets($where=null): array
    {
        
        $sql = "SELECT COUNT(DISTINCT a.code_request) total
                  FROM (hdk_tbrequest a, hdk_tbstatus b, tbperson pers,
                        tbperson resp, hdk_tbrequest_in_charge inch)
                 WHERE a.idstatus = b.idstatus
                   AND a.idperson_owner = pers.idperson
                   AND inch.id_in_charge = resp.idperson
                   AND inch.code_request = a.code_request
                   AND a.code_request = inch.code_request
                   AND inch.ind_in_charge = 1 
                $where";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            $ticket = new ticketModel();
            $ticket->setTotalRows($aRet['total']);

            $ret = true;
            $result = array("message"=>"","object"=>$ticket);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error counting tickets ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * Returns a object with url token 
     *
     * @param  ticketModel $ticketModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function getUrlTokenByEmail(ticketModel $ticketModel): array
    {        
        $sql = "SELECT b.token
                  FROM tbperson a, hdk_tbviewbyurl b 
                 WHERE a.idperson = b.idperson
                 AND a.email = :email 
                 AND b.code_request = :ticketCode";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':email', $ticketModel->getRecipientEmail());
            $stmt->bindParam(':ticketCode', $ticketModel->getTicketCode());
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            $ticketModel->setLinkToken((isset($aRet['token']) && !is_null($aRet['token'])) ? $aRet['token'] : "");

            $ret = true;
            $result = array("message"=>"","object"=>$ticketModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting ticket's template. ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * Returns an object with the total tickets waiting for approval 
     *
     * @param  ticketModel $fingerprintModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function getWaitingApprovalRequestsCount(ticketModel $ticketModel): array
    {        
        $sql = "SELECT COUNT(distinct req.code_request) AS total 
                  FROM (hdk_tbrequest req, hdk_tbstatus stat, tbperson pers,
                            tbperson resp, hdk_tbrequest_in_charge inch)
             LEFT JOIN hdk_tbgroup grp
                    ON (inch.id_in_charge = grp.idperson
                        AND resp.idperson = grp.idperson)
                 WHERE req.idstatus = stat.idstatus
                   AND req.idperson_owner = pers.idperson
                   AND inch.id_in_charge = resp.idperson
                   AND inch.code_request = req.code_request
                   AND req.code_request = inch.code_request
                   AND inch.ind_in_charge = 1     
                   AND req.idperson_owner = :ownerID
                   AND stat.idstatus_source = 4";
        
        if($ticketModel->getItemException())
            $sql .= " AND iditem <> 124";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':ownerID', $ticketModel->getIdOwner());
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            $ticketModel->setTotalRows($aRet['total']);

            $ret = true;
            $result = array("message"=>"","object"=>$ticketModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error counting tickets waiting for approval. ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * Returns an object with the last ticket code
     *
     * @param  ticketModel $fingerprintModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function getLastTicketCode(ticketModel $ticketModel): array
    {        
        $prefix = $ticketModel->getTablePrefix();
        $sql = "SELECT cod_request, cod_month FROM {$prefix}_tbrequest_code WHERE cod_month = DATE_FORMAT(NOW(),'%Y%m')";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            $ticketModel->setLastTicketCode(($aRet['cod_request']) ? $aRet['cod_request'] : 0);

            $ret = true;
            $result = array("message"=>"","object"=>$ticketModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error counting tickets waiting for approval. ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * Returns an object with the last ticket code
     *
     * @param  ticketModel $fingerprintModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function increaseTicketCode(ticketModel $ticketModel): array
    {        
        $prefix = $ticketModel->getTablePrefix();
        $sql = "UPDATE {$prefix}_tbrequest_code 
                   SET cod_request = cod_request + 1  
                 WHERE cod_month = DATE_FORMAT(NOW(),'%Y%m') 
                   AND LAST_INSERT_ID(cod_request)";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $ret = true;
            $result = array("message"=>"","object"=>$ticketModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error counting tickets waiting for approval. ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * Returns an object with the last ticket code
     *
     * @param  ticketModel $fingerprintModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function createTicketCode(ticketModel $ticketModel): array
    {        
        $prefix = $ticketModel->getTablePrefix();
        $sql = "INSERT INTO {$prefix}_tbrequest_code (cod_request,cod_month)
                     VALUES (1,DATE_FORMAT(NOW(),'%Y%m'))";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $ret = true;
            $result = array("message"=>"","object"=>$ticketModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error counting tickets waiting for approval. ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * Checks if the user is a VIP
     *
     * @param  ticketModel $fingerprintModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function checksVipUser(ticketModel $ticketModel): array
    {        
        $sql = "SELECT user_vip FROM tbperson WHERE idperson = :ownerID";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':ownerID', $ticketModel->getIdOwner());
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            $ticketModel->setIsUserVip($aRet['user_vip']);

            $ret = true;
            $result = array("message"=>"","object"=>$ticketModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting user_vip field data. ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * Checks if the user is a VIP
     *
     * @param  ticketModel $fingerprintModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function checksVipPriority(ticketModel $ticketModel): array
    {        
        $sql = "SELECT COUNT(idpriority) as total FROM hdk_tbpriority WHERE vip = 1 GROUP BY idpriority";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            $hasVip = ($aRet['total'] && $aRet['total'] > 0) ? "Y": "N";
            $ticketModel->getVipHasPriority($hasVip);

            $ret = true;
            $result = array("message"=>"","object"=>$ticketModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error checks VIP priority. ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * Checks if the user is a VIP
     *
     * @param  ticketModel $fingerprintModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function getVipPriority(ticketModel $ticketModel): array
    {        
        $sql = "SELECT idpriority FROM hdk_tbpriority WHERE vip = 1";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            $ticketModel->setIdPriority($aRet['idpriority']);

            $ret = true;
            $result = array("message"=>"","object"=>$ticketModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting VIP priority identification. ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * Checks if the user is a VIP
     *
     * @param  ticketModel $fingerprintModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function getIdPriorityByService(ticketModel $ticketModel): array
    {        
        $sql = "SELECT idpriority FROM hdk_tbcore_service WHERE idservice = :serviceID";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':serviceID', $ticketModel->getIdService());
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            $ticketModel->setIdPriority($aRet['idpriority']);

            $ret = true;
            $result = array("message"=>"","object"=>$ticketModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting priority identification by service. ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * Checks if the user is a VIP
     *
     * @param  ticketModel $fingerprintModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function getDefaultPriority(ticketModel $ticketModel): array
    {        
        $sql = "SELECT idpriority FROM hdk_tbpriority WHERE `default` = 1 AND `status` = 'A'";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            $ticketModel->setIdPriority($aRet['idpriority']);

            $ret = true;
            $result = array("message"=>"","object"=>$ticketModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting default priority identification. ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * Checks if the user is a VIP
     *
     * @param  ticketModel $fingerprintModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function getServiceGroup(ticketModel $ticketModel): array
    {        
        $sql = "SELECT grp.idperson
                  FROM hdk_tbgroup grp, hdk_tbcore_service serv, hdk_tbgroup_has_service grp_serv
                 WHERE grp.idgroup = grp_serv.idgroup
                  AND serv.idservice = grp_serv.idservice
                  AND serv.idservice = :serviceID";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':serviceID', $ticketModel->getIdService());
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            $ticketModel->setIdServiceGroup($aRet['idperson']);

            $ret = true;
            $result = array("message"=>"","object"=>$ticketModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting service's group id. ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * Insert ticket info into the database
     *
     * @param  ticketModel $ticketModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function insertTicket(ticketModel $ticketModel): array
    {        
        $sql = "INSERT INTO hdk_tbrequest  (code_request,`subject`,description,idtype,iditem,idservice,idreason,
                                            idpriority,idsource,idperson_creator,entry_date,os_number,label,
                                            serial_number,idperson_juridical,expire_date,idattendance_way,
                                            idperson_owner,idstatus,code_email)
                     VALUES (:ticketCode,:subject,:description,:typeID,:itemID,:serviceID,NULLIF(:reasonID,'NULL'),
                             :priorityID,:sourceID,:creatorID,:openDate,:osNumber,:tag,
                             :serialNumber,:idJuridical,:expirationDate,:wayID,
                             :ownerID,:statusID,NULLIF(:emailCode,'NULL'))";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":ticketCode",$ticketModel->getTicketCode());
        $stmt->bindValue(":subject",$ticketModel->getSubject());
        $stmt->bindValue(":description",$ticketModel->getDescription());
        $stmt->bindValue(":typeID",$ticketModel->getIdType());
        $stmt->bindValue(":itemID",$ticketModel->getIdItem());
        $stmt->bindValue(":serviceID",$ticketModel->getIdService());
        $stmt->bindValue(":reasonID",(($ticketModel->getIdReason() <= 0) ? 'NULL' : $ticketModel->getIdReason()));
        $stmt->bindValue(":priorityID",$ticketModel->getIdPriority());
        $stmt->bindValue(":sourceID",$ticketModel->getIdSource());
        $stmt->bindValue(":creatorID",$ticketModel->getIdCreator());
        $stmt->bindValue(":openDate",$ticketModel->getEntryDate());
        $stmt->bindValue(":osNumber",$ticketModel->getOsNumber());
        $stmt->bindValue(":tag",$ticketModel->getLabel());
        $stmt->bindValue(":serialNumber",$ticketModel->getSerialNumber());
        $stmt->bindValue(":idJuridical",$ticketModel->getIdCompany());
        $stmt->bindValue(":expirationDate",$ticketModel->getExpireDate());        
        $stmt->bindValue(":wayID",$ticketModel->getIdAttendanceWay());
        $stmt->bindValue(":ownerID",$ticketModel->getIdOwner());
        $stmt->bindValue(":statusID",$ticketModel->getIdStatus());
        $stmt->bindValue(":emailCode",(empty($ticketModel->getEmailCode()) ? 'NULL': $ticketModel->getEmailCode()));
        $stmt->execute();

        $ret = true;
        $result = array("message"=>"","object"=>$ticketModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * Inserts in charge of the ticket into the database
     *
     * @param  ticketModel $ticketModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function insertTicketInCharge(ticketModel $ticketModel): array
    {        
        $sql = "INSERT INTO hdk_tbrequest_in_charge (code_request,id_in_charge,type,ind_in_charge,ind_repass,ind_track,ind_operator_aux) 
                     VALUES (:ticketCode,:inChargeID,:type,:isInCharge,:isRepass,:isTrack,:isOperatorAux)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":ticketCode",$ticketModel->getTicketCode());
        $stmt->bindValue(":inChargeID",$ticketModel->getIdInCharge());
        $stmt->bindValue(":type",$ticketModel->getInChargeType());
        $stmt->bindValue(":isInCharge",$ticketModel->getIsInCharge());
        $stmt->bindValue(":isRepass",$ticketModel->getIsRepass());
        $stmt->bindValue(":isTrack",(empty($ticketModel->getIsTrack()) ? 0 : $ticketModel->getIsTrack()));
        $stmt->bindValue(":isOperatorAux",(empty($ticketModel->getIsOperatorAux()) ? 0 : $ticketModel->getIsOperatorAux()));
        $stmt->execute();

        $ret = true;
        $result = array("message"=>"","object"=>$ticketModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * Inserts in charge of the ticket into the database
     *
     * @param  ticketModel $ticketModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function insertTicketTimesNew(ticketModel $ticketModel): array
    {        
        $sql = "INSERT INTO hdk_tbrequest_times (CODE_REQUEST,MIN_OPENING_TIME,MIN_ATTENDANCE_TIME,
                                                 MIN_EXPENDED_TIME,MIN_TELEPHONE_TIME,MIN_CLOSURE_TIME)
                     VALUES (:ticketCode,:minOpeningTime,:minAttendanceTime,:minExpendedTime,
                             :minTelephoneTime,:minClosureTime)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":ticketCode",$ticketModel->getTicketCode());
        $stmt->bindValue(":minOpeningTime",(empty($ticketModel->getMinOpeningTime()) ? 0 : $ticketModel->getMinOpeningTime()));
        $stmt->bindValue(":minAttendanceTime",(empty($ticketModel->getMinAttendanceTime()) ? 0 : $ticketModel->getMinAttendanceTime()));
        $stmt->bindValue(":minExpendedTime",(empty($ticketModel->getMinExpendedTime()) ? 0 : $ticketModel->getMinExpendedTime()));
        $stmt->bindValue(":minTelephoneTime",(empty($ticketModel->getMinTelephoneTime()) ? 0 : $ticketModel->getMinTelephoneTime()));
        $stmt->bindValue(":minClosureTime",(empty($ticketModel->getMinClosureTime()) ? 0 : $ticketModel->getMinClosureTime()));
        $stmt->execute();

        $ret = true;
        $result = array("message"=>"","object"=>$ticketModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * Inserts in charge of the ticket into the database
     *
     * @param  ticketModel $ticketModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function insertTicketDate(ticketModel $ticketModel): array
    {        
        $sql = "INSERT INTO hdk_tbrequest_dates (code_request,forwarded_date,approval_date,finish_date,
                                                 rejection_date,date_period_attendant,date_charging_period,
                                                 opening_date) 
                     VALUES (:ticketCode,NULLIF(:forwardedDate,'NULL'),NULLIF(:approvalDate,'NULL'),NULLIF(:finishDate,'NULL'),
                             NULLIF(:rejectionDate,'NULL'),NULLIF(:attendantPeriod,'NULL'),NULLIF(:chargingPeriod,'NULL'),
                             NULLIF(:openingDate,'NULL'))";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":ticketCode",$ticketModel->getTicketCode());
        $stmt->bindValue(":forwardedDate",(empty($ticketModel->getForwardedDate()) ? 'NULL' : $ticketModel->getForwardedDate()));
        $stmt->bindValue(":approvalDate",(empty($ticketModel->getApprovalDate()) ? 'NULL' : $ticketModel->getApprovalDate()));
        $stmt->bindValue(":finishDate",(empty($ticketModel->getFinishDate()) ? 'NULL' : $ticketModel->getFinishDate()));
        $stmt->bindValue(":rejectionDate",(empty($ticketModel->getRejectionDate()) ? 'NULL' : $ticketModel->getRejectionDate()));
        $stmt->bindValue(":attendantPeriod",(empty($ticketModel->getAttendantPeriod()) ? 'NULL' : $ticketModel->getAttendantPeriod()));
        $stmt->bindValue(":chargingPeriod",(empty($ticketModel->getChargingPeriod()) ? 'NULL' : $ticketModel->getChargingPeriod()));
        $stmt->bindValue(":openingDate",(empty($ticketModel->getOpeningDate()) ? 'NULL' : $ticketModel->getOpeningDate()));
        $stmt->execute();

        $ret = true;
        $result = array("message"=>"","object"=>$ticketModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * Inserts in charge of the ticket into the database
     *
     * @param  ticketModel $ticketModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function insertTicketLog(ticketModel $ticketModel): array
    {        
        $sql = "INSERT INTO hdk_tbrequest_log (cod_request,date,idstatus,idperson,reopened) 
                     VALUES (:ticketCode,:logDate,:statusID,:personID,:isReopened)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":ticketCode",$ticketModel->getTicketCode());
        $stmt->bindValue(":logDate",$ticketModel->getLogDate());
        $stmt->bindValue(":statusID",$ticketModel->getIdStatus());
        $stmt->bindValue(":personID",$ticketModel->getIdUserLog());
        $stmt->bindValue(":isReopened",(empty($ticketModel->getIsReopened()) ? '0' : $ticketModel->getIsReopened()));
        $stmt->execute();

        $ret = true;
        $result = array("message"=>"","object"=>$ticketModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * Insert request's note
     *
     * @param  ticketModel $ticketModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function insertTicketNote(ticketModel $ticketModel): array
    {        
        $sql = "INSERT INTO hdk_tbnote (code_request,idperson,description,entry_date,minutes,start_hour,
                            finish_hour,execution_date,hour_type,service_value,public,idtype,
                            ip_adress,callback,flag_opened,code_email) 
                     VALUES (:ticketCode,:personID,:note,:noteDate,:totalMinutes,:startHour,:finishHour, 
                             :executionDate,:hourType,:serviceVal,:public,:typeID,:ipAddress, 
                             :isCallback,:isOpen,:emailCode)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":ticketCode",$ticketModel->getTicketCode());
        $stmt->bindValue(":personID",$ticketModel->getIdCreator());
        $stmt->bindValue(":note",$ticketModel->getNote());
        $stmt->bindValue(":noteDate",$ticketModel->getNoteDateTime());
        $stmt->bindValue(":totalMinutes",$ticketModel->getNoteTotalMinutes());
        $stmt->bindValue(":startHour",$ticketModel->getNoteStartHour());
        $stmt->bindValue(":finishHour",$ticketModel->getNoteFinishHour());
        $stmt->bindValue(":executionDate",$ticketModel->getNoteExecutionDate());
        $stmt->bindValue(":hourType",$ticketModel->getNoteHourType());
        $stmt->bindValue(":serviceVal",(empty($ticketModel->getNoteServiceVal()) ? 'NULL': $ticketModel->getNoteServiceVal()));
        $stmt->bindValue(":public",$ticketModel->getNotePublic());
        $stmt->bindValue(":typeID",$ticketModel->getNoteTypeID());
        $stmt->bindValue(":ipAddress",$ticketModel->getNoteIpAddress());
        $stmt->bindValue(":isCallback",$ticketModel->getNoteHourType());
        $stmt->bindValue(":isOpen",$ticketModel->getNoteIsOpen());
        $stmt->bindValue(":emailCode",(empty($ticketModel->getEmailCode()) ? 'NULL': $ticketModel->getEmailCode()));
        $stmt->execute(); 

        $ret = true;
        $result = array("message"=>"","object"=>$ticketModel);         
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * Checks if the user is a VIP
     *
     * @param  ticketModel $ticketModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function saveTicket(ticketModel $ticketModel): array
    {   
        $ticketRulesDAO = new ticketRulesDAO();
        $ticketRulesModel = new ticketRulesModel();
        //echo "",print_r($ticketModel,true),"\n"; die();
        $aApproval = $ticketModel->getApprovalList();
        $aInCharge = $ticketModel->getInChargeList();
        $aNotes = $ticketModel->getNoteList();
        
        try{
            $this->db->beginTransaction();

            $insTicket = $this->insertTicket($ticketModel);

            if($insTicket['status']){
                if(count($aApproval) > 0){
                    foreach($aApproval as $k=>$v){
                        $ticketRulesModel->setIdApproval($v['idapproval'])
                                         ->setTicketCode($ticketModel->getTicketCode())
                                         ->setOrder($v['order'])
                                         ->setIdPerson($v['idperson'])
                                         ->setIsRecalculate($v['fl_recalculate']);

                        $ticketRulesDAO->insertApproval($ticketRulesModel);
                    }
                }

                // -- save in charge
                foreach($aInCharge as $k=>$v){
                    $ticketModel->setTicketCode($ticketModel->getTicketCode())
                                ->setIdInCharge($v['id'])
                                ->setInChargeType($v['type'])
                                ->setIsInCharge($v['isInCharge'])
                                ->setIsRepass($v['isRepassed']);

                    $insInCharge = $this->insertTicketInCharge($ticketModel);
                }

                $insTicketTimes = $this->insertTicketTimesNew($ticketModel);
                $insTicketDate = $this->insertTicketDate($ticketModel);
                $insTicketLog = $this->insertTicketLog($ticketModel);
                
                // -- save notes
                foreach($aNotes as $k=>$v){
                    $ticketModel->setNotePublic($v['public'])
                                ->setNoteTypeID($v['type'])
                                ->setNote($v['note'])
                                ->setNoteDateTime($v['date'])
                                ->setNoteIsOpen(0);

                    $insNote = $this->insertTicketNote($ticketModel);
                }
            }
            
            $ret = true;
            $result = array("message"=>"","object"=>$insTicket['push']['object']);
            $this->db->commit();
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error trying save ticket info ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
            $this->db->rollBack();
        }         
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * Get in charge data
     *
     * @param  ticketModel $ticketModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function getInChargeByTicketCode(ticketModel $ticketModel): array
    {        
        $sql = "SELECT id_in_charge, `name`, `type`, b.email
                  FROM hdk_tbrequest_in_charge a, tbperson b
                 WHERE b.idperson =  a.id_in_charge
                   AND ind_in_charge = 1
                   AND code_request = :ticketCode";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":ticketCode",$ticketModel->getTicketCode());
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            $ticketModel->setIdInCharge($aRet['id_in_charge'])
                        ->setInCharge($aRet['name'])
                        ->setInChargeType($aRet['type'])
                        ->setInChargeEmail((isset($aRet['email']) && !is_null($aRet['email'])) ? $aRet['email'] : "");

            $ret = true;
            $result = array("message"=>"","object"=>$ticketModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting in charge data. ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * Link the ticket with uploaded file 
     *
     * @param  ticketModel $ticketModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function insertTicketAttachment(ticketModel $ticketModel): array
    {
        $sql = "INSERT INTO hdk_tbrequest_attachment (code_request,file_name)
                     VALUES(:requestCode,:fileName)";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":requestCode",$ticketModel->getTicketCode());
            $stmt->bindValue(":fileName",$ticketModel->getFileName());
            $stmt->execute();
            
            $ticketModel->setIdAttachment($this->db->lastInsertId());

            $ret = true;
            $result = array("message"=>"","object"=>$ticketModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error trying link ticket to uploaded file ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * Delete link ticket to attachment
     *
     * @param  ticketModel $ticketModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function deleteTicketAttachment(ticketModel $ticketModel): array
    {        
        $sql = "DELETE FROM hdk_tbrequest_attachment WHERE idrequest_attachment = :attachmentID";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":attachmentID",$ticketModel->getIdAttachment());
            $stmt->execute();

            $ret = true;
            $result = array("message"=>"","object"=>$ticketModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error trying delete link ticket to uploaded file ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * Update uploaded file name
     *
     * @param  mixed $ticketModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function updateTicketAttachmentName(ticketModel $ticketModel): array
    {        
        $sql = "UPDATE lgp_tbrequest_attachment
                   SET file_uploaded = :newName 
                 WHERE idrequest_attachment = :attachmentID";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":newName",$ticketModel->getNewFileName());
            $stmt->bindValue(":attachmentID",$ticketModel->getIdAttachment());
            $stmt->execute();

            $ret = true;
            $result = array("message"=>"","object"=>$ticketModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error trying update uploaded file name ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * Link the ticket with uploaded file 
     *
     * @param  ticketModel $ticketModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function saveViewByUrl(ticketModel $ticketModel): array
    {
        $sql = "INSERT INTO hdk_tbviewbyurl (idperson,code_request,token)
                     VALUES (:idPerson,:ticketCode,:token)";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":idPerson",$ticketModel->getIdOperator());
            $stmt->bindValue(":ticketCode",$ticketModel->getTicketCode());
            $stmt->bindValue(":token",$ticketModel->getTicketToken());
            $stmt->execute();
            
            $ret = true;
            $result = array("message"=>"","object"=>$ticketModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error save token for request preview authentication. ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * Returns a object with ticket data
     *
     * @param  ticketModel $ticketModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function getTicket(ticketModel $ticketModel): array
    {        
        $sql = "SELECT * FROM hdk_viewrequestdata WHERE code_request = :ticketCode";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':ticketCode', $ticketModel->getTicketCode());
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            $ticketModel->setExpireDate($aRet['expire_date'])
                        ->setEntryDate($aRet['entry_date'])
                        ->setNoteIsOpen($aRet['flag_opened'])
                        ->setSubject($aRet['subject'])
                        ->setDescription($aRet['description'])
                        ->setIdOwner($aRet['idperson_owner'])
                        ->setOwner($aRet['personname'])
                        ->setOwnerEmail($aRet['email'])
                        ->setOwnerPhone($aRet['phone'])
                        ->setOwnerBranch($aRet['branch'])
                        ->setIdCreator($aRet['idperson_creator'])
                        ->setCreator($aRet['name_creator'])
                        ->setCreatorPhone($aRet['phone_number'])
                        ->setCreatorMobile($aRet['cel_phone'])
                        ->setCreatorBranch($aRet['branch_number'])
                        ->setIdCompany($aRet['idcompany'])
                        ->setCompany($aRet['company'])
                        ->setIdSource($aRet['idsource'])
                        ->setSource($aRet['source'])
                        ->setExtensionsNumber($aRet['extensions_number'])
                        ->setIdStatus($aRet['idstatus'])
                        ->setStatus($aRet['status'])
                        ->setColor($aRet['color'])
                        ->setIdAttendanceWay($aRet['idattendance_way'])
                        ->setAttendanceWay($aRet['way_name'])
                        ->setOsNumber($aRet['os_number'])
                        ->setSerialNumber($aRet['serial_number'])
                        ->setLabel($aRet['label'])
                        ->setIdArea($aRet['idarea'])
                        ->setArea($aRet['AREA'])
                        ->setIdType($aRet['idtype'])
                        ->setType($aRet['type'])
                        ->setIdItem($aRet['iditem'])
                        ->setItem($aRet['item'])
                        ->setIdService($aRet['idservice'])
                        ->setService($aRet['service'])
                        ->setIdPriority($aRet['idpriority'])
                        ->setPriority($aRet['priority'])
                        ->setIdInCharge($aRet['id_in_charge'])
                        ->setInCharge($aRet['in_charge'])
                        ->setInChargeType($aRet['typeincharge'])
                        ->setIsInCharge($aRet['ind_in_charge'])
                        ->setIdDepartment($aRet['iddepartment'])
                        ->setDepartment($aRet['department'])
                        ->setIdReason((isset($aRet['idreason']) && !is_null($aRet['idreason'])) ? $aRet['idreason'] : 0)
                        ->setReason((isset($aRet['reason']) && !is_null($aRet['reason'])) ? $aRet['reason'] : "");

            $ret = true;
            $result = array("message"=>"","object"=>$ticketModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting ticket's data. ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * Returns a object with ticket's notes
     *
     * @param  ticketModel $ticketModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchTicketNotes(ticketModel $ticketModel): array
    {        
        $sql = " SELECT nt.idnote, pers.idperson, pers.name, nt.description, nt.entry_date, nt.minutes, nt.start_hour, nt.finish_hour,
                        nt.execution_date, nt.public, nt.idtype, nt.ip_adress, nt.callback,
                        TIME_FORMAT(TIMEDIFF(nt.finish_hour, nt.start_hour), '%Hh %imin %ss') AS diferenca, nt.hour_type, nt.flag_opened
                   FROM (hdk_tbnote AS nt, tbperson AS pers)
                  WHERE code_request = :ticketCode
                    AND pers.idperson = nt.idperson
               ORDER BY idnote DESC";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':ticketCode', $ticketModel->getTicketCode());
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $ticketModel->setNoteList((!empty($aRet) && count($aRet) > 0) ? $aRet : array());

            $ret = true;
            $result = array("message"=>"","object"=>$ticketModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting ticket's notes. ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
    }
}