<?php

namespace App\modules\helpdezk\dao\mysql;

use App\core\Database;

use App\modules\helpdezk\dao\mysql\ticketRulesDAO;
use App\modules\helpdezk\dao\mysql\evaluationDAO;

use App\modules\helpdezk\models\mysql\ticketModel;
use App\modules\helpdezk\models\mysql\ticketRulesModel;
use App\modules\helpdezk\models\mysql\evaluationModel;

class ticketDAO extends Database
{
    public function __construct()
    {
        parent::__construct(); 
    }

    /**
     * en_us Returns an array with ticket to display in grid
     *
     * @param  string $where
     * @param  string $group
     * @param  string $order
     * @param  string $limit
     * @return array    Parameters returned in array: 
     *                  [status = true/false
     *                   push =  [message = PDO Exception message 
     *                            object = model's object]]
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
     * en_us Return an array with rows total for grid pagination 
     *
     * @param  string $where
     * @return array    Parameters returned in array: 
     *                  [status = true/false
     *                   push =  [message = PDO Exception message 
     *                            object = model's object]]
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
     * en_us Returns an array with ticket to display in grid
     *
     * @param  string $where
     * @param  string $group
     * @param  string $order
     * @param  string $limit
     * @return array    Parameters returned in array: 
     *                  [status = true/false
     *                   push =  [message = PDO Exception message 
     *                            object = model's object]]
     */
    public function queryAttendantTickets($where=null,$group=null,$order=null,$limit=null): array
    {        
        $sql = "SELECT req.code_request AS code_request, req.expire_date  AS expire_date, req.entry_date AS entry_date,
                        req.flag_opened AS flag_opened, req.subject  AS `subject`, req.idperson_owner AS idperson_owner,
                        req.idperson_creator AS idperson_creator, cre.name AS name_creator, cre.phone_number AS phone_number,
                        cre.cel_phone AS cel_phone, cre.branch_number  AS branch_number, req.idperson_juridical AS idcompany,
                        req.idsource AS idsource, req.extensions_number AS extensions_number, source.name AS `source`,
                        req.idstatus AS idstatus, req.idattendance_way AS idattendance_way, req.os_number AS os_number,
                        req.serial_number AS serial_number, req.label AS label, req.description AS `description`,
                        comp.name AS company, stat.name AS `status`, rtype.name AS `type`, rtype.idtype AS idtype,
                        item.iditem AS iditem, item.name AS item, serv.idservice AS idservice, serv.name AS service,
                        prio.name AS priority, prio.idpriority AS idpriority, tmp.ind_in_charge AS ind_in_charge,
                        tmp.id_in_charge  AS id_in_charge, resp.name AS in_charge, prio.color AS priority_color,
                        pers.name AS personname, pers.email AS email, pers.phone_number AS phone, pers.branch_number AS branch,
                        tmp.type AS typeincharge, dep.name AS department, dep.iddepartment AS iddepartment, 
                        source.name AS source_name, are.idarea AS idarea, are.name AS `area`,
                        req.idreason AS idreason, attway.way AS way_name, stat.color AS status_color, stat.idstatus_source,
                        (SELECT COUNT(idrequest_attachment) FROM hdk_tbrequest_attachment WHERE code_request = req.code_request) total_attachs, inch.ind_track
                  FROM hdk_tbrequest req
                  JOIN tbperson pers
                    ON req.idperson_owner = pers.idperson
                  JOIN tbperson comp
                    ON req.idperson_juridical = comp.idperson
                  JOIN hdk_tbrequest_in_charge inch
                    ON req.code_request = inch.code_request
                  JOIN (SELECT code_request, id_in_charge, `type`, ind_in_charge FROM hdk_tbrequest_in_charge WHERE ind_in_charge = 1) tmp
                    ON req.code_request = tmp.code_request
                  JOIN tbperson resp
                    ON tmp.id_in_charge = resp.idperson
                  JOIN tbperson cre
                    ON req.idperson_creator = cre.idperson
                  JOIN hdk_tbdepartment_has_person dep_pers
                    ON pers.idperson = dep_pers.idperson
                  JOIN hdk_tbdepartment dep
                    ON dep.iddepartment = dep_pers.iddepartment
                  JOIN hdk_tbcore_type rtype
                    ON req.idtype = rtype.idtype
                  JOIN hdk_tbcore_service serv
                    ON req.idservice = serv.idservice
                  JOIN hdk_tbcore_area are
                    ON are.idarea = rtype.idarea
                  JOIN hdk_tbpriority prio
                    ON req.idpriority = prio.idpriority
                  JOIN hdk_tbcore_item item
                    ON req.iditem = item.iditem
                  JOIN hdk_tbstatus stat
                    ON req.idstatus = stat.idstatus
                  JOIN hdk_tbsource `source`
                    ON req.idsource = source.idsource
                  JOIN hdk_tbattendance_way attway
                    ON attway.idattendanceway = req.idattendance_way
                $where
              GROUP BY  req.code_request $group
                $order $limit";
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
     * en_us Return an array with rows total for grid pagination 
     *
     * @param  string $where
     * @return array    Parameters returned in array: 
     *                  [status = true/false
     *                   push =  [message = PDO Exception message 
     *                            object = model's object]]
     */
    public function countAttendantTickets($where=null): array
    {
        
        $sql = "SELECT COUNT(DISTINCT req.code_request) total
                  FROM hdk_tbrequest req
                  JOIN tbperson pers
                    ON req.idperson_owner = pers.idperson
                  JOIN tbperson comp
                    ON req.idperson_juridical = comp.idperson
                  JOIN hdk_tbrequest_in_charge inch
                    ON req.code_request = inch.code_request
                  JOIN (SELECT code_request, id_in_charge, `type`, ind_in_charge FROM hdk_tbrequest_in_charge WHERE ind_in_charge = 1) tmp
                    ON req.code_request = tmp.code_request
                  JOIN tbperson resp
                    ON tmp.id_in_charge = resp.idperson
                  JOIN tbperson cre
                    ON req.idperson_creator = cre.idperson
                  JOIN hdk_tbdepartment_has_person dep_pers
                    ON pers.idperson = dep_pers.idperson
                  JOIN hdk_tbdepartment dep
                    ON dep.iddepartment = dep_pers.iddepartment
                  JOIN hdk_tbcore_type rtype
                    ON req.idtype = rtype.idtype
                  JOIN hdk_tbcore_service serv
                    ON req.idservice = serv.idservice
                  JOIN hdk_tbcore_area are
                    ON are.idarea = rtype.idarea
                  JOIN hdk_tbpriority prio
                    ON req.idpriority = prio.idpriority
                  JOIN hdk_tbcore_item item
                    ON req.iditem = item.iditem
                  JOIN hdk_tbstatus stat
                    ON req.idstatus = stat.idstatus
                  JOIN hdk_tbsource source
                    ON req.idsource = source.idsource
                  JOIN hdk_tbattendance_way attway
                    ON attway.idattendanceway = req.idattendance_way
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
                             :executionDate,:hourType,NULLIF(:serviceVal,'NULL'),:public,:typeID,:ipAddress, 
                             :isCallback,:isOpen,NULLIF(:emailCode,'NULL'))";
        
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

        $ticketModel->setIdNote($this->db->lastInsertId());

        $ret = true;
        $result = array("message"=>"","object"=>$ticketModel);         
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Saves the new ticket into DB
     * pt_br Grava o novo ticket no banco de dados
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
        
        $aApproval = $ticketModel->getApprovalList();
        $aInCharge = $ticketModel->getInChargeList();
        $aNotes = $ticketModel->getNoteList();
        $aExtraFields = $ticketModel->getExtraFieldList();
        
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
                                ->setNoteTotalMinutes($v['totalMinutes'])
                                ->setNoteStartHour($v['startHour'])
                                ->setNoteFinishHour($v['finishHour'])
                                ->setNoteExecutionDate($v['executionDate'])
                                ->setNoteHourType($v['hourType'])
                                ->setNoteIpAddress($v['ipAddress'])
                                ->setNoteIsCallback($v['callback'])
                                ->setNoteIsOpen(0);

                    $insNote = $this->insertTicketNote($ticketModel);
                }

                // -- save extra fields
                if(!empty($aExtraFields) && count($aExtraFields) > 0){
                    foreach($aExtraFields as $k=>$v){
                        if(!empty($v)){
                            $ticketModel->setExtraFieldId($k)
                                        ->setExtraFieldValue($v);
                            
                            $insExtraField = $this->insertTicketExtraField($ticketModel);
                        }
                    }
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
        $sql = "SELECT `req`.`code_request` AS `code_request`, `req`.`expire_date` AS `expire_date`, `req`.`entry_date` AS `entry_date`,
                        `req`.`flag_opened` AS `flag_opened`, `req`.`subject` AS `subject`, `req`.`idperson_owner` AS `idperson_owner`,
                        `req`.`idperson_creator` AS `idperson_creator`, `cre`.`name` AS `name_creator`, `cre`.`phone_number` AS `phone_number`,
                        `cre`.`cel_phone` AS `cel_phone`, `cre`.`branch_number` AS `branch_number`, `req`.`idperson_juridical` AS `idcompany`,
                        `req`.`idsource` AS `idsource`, `req`.`extensions_number` AS `extensions_number`, `source`.`name` AS `source`,
                        `req`.`idstatus` AS `idstatus`, `req`.`idattendance_way` AS `idattendance_way`, `req`.`os_number` AS `os_number`,
                        `req`.`serial_number` AS `serial_number`, `req`.`label` AS `label`, `req`.`description` AS `description`,
                        `comp`.`name`  AS `company`, `stat`.`user_view` AS `status`, `rtype`.`name` AS `type`, `rtype`.`idtype` AS `idtype`,
                        `item`.`iditem` AS `iditem`, `item`.`name` AS `item`, `serv`.`idservice` AS `idservice`, `serv`.`name` AS `service`,
                        `prio`.`name` AS `priority`, `prio`.`idpriority` AS `idpriority`, `inch`.`ind_in_charge` AS `ind_in_charge`,
                        `inch`.`id_in_charge` AS `id_in_charge`, `resp`.`name` AS `in_charge`, `prio`.`color` AS `priority_color`,
                        `pers`.`name` AS `personname`, `pers`.`email` AS `email`, `pers`.`phone_number` AS `phone`,
                        `pers`.`branch_number` AS `branch`, `inch`.`type` AS `typeincharge`, `dep`.`name` AS `department`, 
                        `dep`.`iddepartment` AS `iddepartment`, `source`.`name` AS `source_name`, `are`.`idarea` AS `idarea`,
                        `are`.`name` AS `area`, `reason`.`name`  AS `reason`, `req`.`idreason` AS `idreason`, `attway`.`way` AS `way_name`,
                        `stat`.`color` AS `status_color`, `pers`.`idtypeperson` AS `owner_type`, `rse`.`sender_email` AS `sender_email`
                  FROM hdk_tbrequest req
                  JOIN `tbperson` `pers`
                    ON `req`.`idperson_owner` = `pers`.`idperson`
                  JOIN `tbperson` `comp`
                    ON `req`.`idperson_juridical` = `comp`.`idperson`
                  JOIN `hdk_tbrequest_in_charge` `inch`
                    ON `req`.`code_request` = `inch`.`code_request`
                  JOIN `tbperson` `resp`
                    ON `inch`.`id_in_charge` = `resp`.`idperson` AND 
                       `inch`.`ind_in_charge` = 1
                  JOIN `tbperson` `cre`
                    ON `req`.`idperson_creator` = `cre`.`idperson`
                  JOIN `hdk_tbdepartment_has_person` `dep_pers`
                    ON `pers`.`idperson` = `dep_pers`.`idperson`
                  JOIN `hdk_tbdepartment` `dep`
                    ON `dep`.`iddepartment` = `dep_pers`.`iddepartment`
                  JOIN `hdk_tbcore_type` `rtype`
                    ON `req`.`idtype` = `rtype`.`idtype`
                  JOIN `hdk_tbcore_service` `serv`
                    ON`req`.`idservice` = `serv`.`idservice`
                  JOIN `hdk_tbcore_area` `are`
                    ON `are`.`idarea` = `rtype`.`idarea`
                  JOIN `hdk_tbpriority` `prio`
                    ON `req`.`idpriority` = `prio`.`idpriority`
                  JOIN `hdk_tbcore_item` `item`
                    ON `req`.`iditem` = `item`.`iditem`
                  JOIN `hdk_tbstatus` `stat`
                    ON `req`.`idstatus` = `stat`.`idstatus`
                  JOIN `hdk_tbsource` `source`
                    ON `req`.`idsource` = `source`.`idsource`
       LEFT OUTER JOIN `hdk_tbcore_reason` `reason`
                    ON `req`.`idreason` = `reason`.`idreason`
       LEFT OUTER JOIN `hdk_tbgroup` `grp`
                    ON `resp`.`idperson` = `grp`.`idperson`
                  JOIN `hdk_tbattendance_way` `attway`
                    ON `attway`.`idattendanceway` = `req`.`idattendance_way` 
       LEFT OUTER JOIN `hdk_tbrequest_response_sender` `rse`
                    ON `req`.`code_request` = `rse`.`code_request` 
                 WHERE `req`.`code_request` = :ticketCode";
        
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
                        ->setOwnerTypeId($aRet['owner_type'])
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
                        ->setColor($aRet['status_color'])
                        ->setIdAttendanceWay($aRet['idattendance_way'])
                        ->setAttendanceWay($aRet['way_name'])
                        ->setOsNumber($aRet['os_number'])
                        ->setSerialNumber($aRet['serial_number'])
                        ->setLabel($aRet['label'])
                        ->setIdArea($aRet['idarea'])
                        ->setArea($aRet['area'])
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
                        ->setReason((isset($aRet['reason']) && !is_null($aRet['reason'])) ? $aRet['reason'] : "")
                        ->setSenderEmail((!is_null($aRet['sender_email']) && !empty($aRet['sender_email'])) ? $aRet['sender_email'] : "");

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

    /**
     * Returns a object with ticket's attachments
     *
     * @param  ticketModel $ticketModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchTicketAttachments(ticketModel $ticketModel): array
    {        
        $sql = "SELECT file_name,idrequest_attachment 
                  FROM hdk_tbrequest_attachment 
                  WHERE code_request = :ticketCode";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':ticketCode', $ticketModel->getTicketCode());
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $ticketModel->setAttachments((!empty($aRet) && count($aRet) > 0) ? $aRet : array());

            $ret = true;
            $result = array("message"=>"","object"=>$ticketModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting ticket's attachments. ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * Get status source id
     *
     * @param  ticketModel $ticketModel
     * @return array            Parameters returned in array: 
     *                          [status = true/false
     *                           push = [message = PDO Exception message 
     *                                   object = model's object]]
     */
    public function getIdStatusSource(ticketModel $ticketModel): array
    {        
        $sql = "SELECT idstatus_source FROM hdk_tbstatus WHERE idstatus = :statusID";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':statusID', $ticketModel->getIdStatus());
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            $ticketModel->setIdStatusSource($aRet['idstatus_source']);

            $ret = true;
            $result = array("message"=>"","object"=>$ticketModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting ticket's status ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * Returns a object with notes attachments
     *
     * @param  ticketModel $ticketModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchNoteAttachments(ticketModel $ticketModel): array
    {        
        $sql = " SELECT b.idnote_attachments,b.filename
                   FROM hdk_tbnote_has_attachments a
             INNER JOIN hdk_tbnote_attachments b
                     ON a.idnote_attachments = b.idnote_attachments
                  WHERE a.idnote = :noteID";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':noteID', $ticketModel->getIdNote());
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $ticketModel->setNoteAttachmentsList((!empty($aRet) && count($aRet) > 0) ? $aRet : array());

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

    /**
     * Returns a object with notes attachments
     *
     * @param  ticketModel $ticketModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchTicketApprover(ticketModel $ticketModel): array
    {        
        $sql = "SELECT a.idperson, a.`order` 
                  FROM hdk_tbrequest_approval a, hdk_tbrequest b 
                 WHERE a.request_code = b.code_request 
                   AND idnote IS NULL 
                   AND fl_rejected = 0
                   AND b.idstatus != 6 
                   AND a.request_code = :ticketCode";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':ticketCode', $ticketModel->getTicketCode());
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $ticketModel->setTicketApproversList((!empty($aRet) && count($aRet) > 0) ? $aRet : array());

            $ret = true;
            $result = array("message"=>"","object"=>$ticketModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting ticket's approvers. ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Saves cancellation of ticket into DB
     * pt_br Grava o cancelamento do ticket no banco de dados
     *
     * @param  ticketModel $ticketModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function saveCancelTicket(ticketModel $ticketModel): array
    {   
        $aNotes = $ticketModel->getNoteList();
        
        try{
            $this->db->beginTransaction();

            $retTicketLog = $this->insertTicketLog($ticketModel);

            if($retTicketLog['status']){
                // -- changes status to cancelled
                $retCancel = $this->updateTicketStatus($ticketModel);
                
                // -- save notes
                foreach($aNotes as $k=>$v){
                    $ticketModel->setNotePublic($v['public'])
                                ->setNoteTypeID($v['type'])
                                ->setNote($v['note'])
                                ->setNoteDateTime($v['date'])
                                ->setNoteTotalMinutes($v['totalMinutes'])
                                ->setNoteStartHour($v['startHour'])
                                ->setNoteFinishHour($v['finishHour'])
                                ->setNoteExecutionDate($v['executionDate'])
                                ->setNoteHourType($v['hourType'])
                                ->setNoteIpAddress($v['ipAddress'])
                                ->setNoteIsCallback($v['callback'])
                                ->setNoteIsOpen(0);

                    $insNote = $this->insertTicketNote($ticketModel);
                }
            }
            
            $ret = true;
            $result = array("message"=>"","object"=>$retTicketLog['push']['object']);
            $this->db->commit();
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error trying save ticket's cancellation ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
            $this->db->rollBack();
        }         
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Changes ticket's status
     * 
     * pt_br Altera o status do ticket
     *
     * @param  ticketModel $ticketModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function updateTicketStatus(ticketModel $ticketModel): array
    {        
        $sql = "UPDATE hdk_tbrequest SET idstatus = :statusID WHERE code_request = :ticketCode";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":ticketCode",$ticketModel->getTicketCode());
        $stmt->bindValue(":statusID",$ticketModel->getIdStatus());
        $stmt->execute();

        $ret = true;
        $result = array("message"=>"","object"=>$ticketModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Updates ticket's dates
     * 
     * pt_br Atualiza as datas da solicitação
     *
     * @param  ticketModel $ticketModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function updateTicketDate(ticketModel $ticketModel): array
    {        
        $sql = "UPDATE hdk_tbrequest_dates SET ". $ticketModel->getTicketDateField() ." = NOW() WHERE code_request = :ticketCode";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":ticketCode",$ticketModel->getTicketCode());
        $stmt->execute();

        $ret = true;
        $result = array("message"=>"","object"=>$ticketModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Saves reopen of the ticket into DB
     * pt_br Grava a reabertura do ticket no banco de dados
     *
     * @param  ticketModel $ticketModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function saveReopenTicket(ticketModel $ticketModel): array
    {   
        $aNotes = $ticketModel->getNoteList();
        
        try{
            $this->db->beginTransaction();

            $retTicketLog = $this->insertTicketLog($ticketModel);

            if($retTicketLog['status']){
                // -- changes status to cancelled
                $retCancel = $this->updateTicketStatus($ticketModel);
                
                // -- save notes
                foreach($aNotes as $k=>$v){
                    $ticketModel->setNotePublic($v['public'])
                                ->setNoteTypeID($v['type'])
                                ->setNote($v['note'])
                                ->setNoteDateTime($v['date'])
                                ->setNoteTotalMinutes($v['totalMinutes'])
                                ->setNoteStartHour($v['startHour'])
                                ->setNoteFinishHour($v['finishHour'])
                                ->setNoteExecutionDate($v['executionDate'])
                                ->setNoteHourType($v['hourType'])
                                ->setNoteIpAddress($v['ipAddress'])
                                ->setNoteIsCallback($v['callback'])
                                ->setNoteIsOpen(0);

                    $insNote = $this->insertTicketNote($ticketModel);
                }
            }
            
            $ret = true;
            $result = array("message"=>"","object"=>$retTicketLog['push']['object']);
            $this->db->commit();
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error trying save ticket's cancellation ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
            $this->db->rollBack();
        }         
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Returns array with auxiliary attendant list
     * pt_br Retorna array com a lista de atendentes auxiliares
     *
     * @param  ticketModel $ticketModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchAuxiliaryAttendant(ticketModel $ticketModel): array
    {        
        $sql = "SELECT idperson, TRIM(name) AS `name`
                  FROM tbperson
                 WHERE idperson ";
        $sql .= ($ticketModel->getInCond()) ? "IN " : "NOT IN "; 
        $sql .= "(SELECT id_in_charge
                    FROM hdk_tbrequest_in_charge 
                   WHERE code_request = :ticketCode
                     AND ind_operator_aux = 1
                     AND type = 'P')
                   AND idtypeperson IN (1,3)
                   AND status = 'A'
              ORDER BY name ASC";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':ticketCode', $ticketModel->getTicketCode());
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $ticketModel->setAuxiliaryAttendantList((!empty($aRet) && count($aRet) > 0) ? $aRet : array());

            $ret = true;
            $result = array("message"=>"","object"=>$ticketModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting ticket's auxiliary attendants. ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Returns array with auxiliary attendant list
     * pt_br Retorna array com a lista de atendentes auxiliares
     *
     * @param  ticketModel $ticketModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchNoteType(ticketModel $ticketModel): array
    {        
        $sql = "SELECT idtypenote, b.key_value note_type_label
                  FROM hdk_tbnote_type a, tbvocabulary b, tblocale c
                 WHERE a.lang_key_name = b.key_name
                   AND b.idlocale = c.idlocale
                   AND LOWER(c.name) = LOWER('{$_ENV['DEFAULT_LANG']}')
                   AND a.available = 1
              ORDER BY note_type_label";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $ticketModel->setNoteTypeList((!empty($aRet) && count($aRet) > 0) ? $aRet : array());

            $ret = true;
            $result = array("message"=>"","object"=>$ticketModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting note's type. ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Returns array with attendant's groups id list
     * pt_br Retorna array com a lista de id real de grupos do atendente
     *
     * @param  ticketModel $ticketModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchAttendantGroupRealID(ticketModel $ticketModel): array
    {        
        $groupsID = $ticketModel->getIdGroupList();
        
        $sql = "SELECT idperson FROM hdk_tbgroup WHERE idgroup IN ({$groupsID})";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $ticketModel->setGroupRealIDList((!empty($aRet) && count($aRet) > 0) ? $aRet : array());

            $ret = true;
            $result = array("message"=>"","object"=>$ticketModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error fetching attendant's group real id. ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Update ticket deadline date
     * pt_br Atualiza o prazo de atendimento da solicitação
     *
     * @param  ticketModel $ticketModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function updateTicketDeadline(ticketModel $ticketModel): array
    {        
        $sql = "UPDATE hdk_tbrequest SET extensions_number = :extensionNumber, expire_date = :newDeadline WHERE code_request = :ticketCode";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":ticketCode",$ticketModel->getTicketCode());
        $stmt->bindValue(":extensionNumber",$ticketModel->getExtensionsNumber());
        $stmt->bindValue(":newDeadline",$ticketModel->getExpireDate());
        $stmt->execute();

        $ret = true;
        $result = array("message"=>"","object"=>$ticketModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Save ticket deadline change in DB
     * pt_br Grava a alteração da data do prazo de atendimento no BD
     *
     * @param  ticketModel $ticketModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function insertTicketDeadlineChange(ticketModel $ticketModel): array
    {        
        $sql = "INSERT INTO hdk_tbrequest_change_expire (code_request,reason,idperson,changedate) 
                     VALUES (:ticketCode,:reason,:personID,NOW())";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":ticketCode",$ticketModel->getTicketCode());
        $stmt->bindValue(":reason",$ticketModel->getReason());
        $stmt->bindValue(":personID",$ticketModel->getIdUserLog());
        $stmt->execute();

        $ret = true;
        $result = array("message"=>"","object"=>$ticketModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Saves deadline date change
     * pt_br Grava a alteração da data do prazo de atendimento
     *
     * @param  ticketModel $ticketModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function saveChangeTicketDeadline(ticketModel $ticketModel): array
    {   
        try{
            $this->db->beginTransaction();

            // -- update ticket deadline in [hdk_tbrequest]
            $updDeadline = $this->updateTicketDeadline($ticketModel);

            if($updDeadline['status']){
                // -- insert deadline change in [hdk_tbrequest_change_expire]
                $retCancel = $this->insertTicketDeadlineChange($updDeadline['push']['object']);
            }
            
            $ret = true;
            $result = array("message"=>"","object"=>$updDeadline['push']['object']);
            $this->db->commit();
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error trying save ticket's deadline change ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
            $this->db->rollBack();
        }         
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Updates ticket's data
     * pt_br Atualiza os dados da solicitação
     *
     * @param  ticketModel $ticketModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function updateTicket(ticketModel $ticketModel): array
    {        
        $sql = "UPDATE hdk_tbrequest 
                   SET idtype = :typeID, 
                       iditem = :itemID,
                       idservice = :serviceID,
                       idreason = NULLIF(:reasonID,'NULL'),
                       idattendance_way = NULLIF(:attendanceTypeID,'NULL'),
                       idpriority = :priorityID
                WHERE code_request = :ticketCode";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":ticketCode",$ticketModel->getTicketCode());
            $stmt->bindValue(":typeID",$ticketModel->getIdType());
            $stmt->bindValue(":itemID",$ticketModel->getIdItem());
            $stmt->bindValue(":serviceID",$ticketModel->getIdService());
            $stmt->bindValue(":reasonID",(($ticketModel->getIdReason() <= 0) ? 'NULL' : $ticketModel->getIdReason()));
            $stmt->bindValue(":attendanceTypeID",(($ticketModel->getIdAttendanceWay() <= 0) ? 'NULL' : $ticketModel->getIdAttendanceWay()));
            $stmt->bindValue(":priorityID",$ticketModel->getIdPriority());
            $stmt->execute();

            $ret = true;
            $result = array("message"=>"","object"=>$ticketModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error trying update ticket's data ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Removes in charge by the ticket
     * 
     * pt_br Remove o responsável pela solicitação
     *
     * @param  ticketModel $ticketModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function removeTicketInCharge(ticketModel $ticketModel): array
    {        
        $sql = "UPDATE hdk_tbrequest_in_charge SET ind_in_charge = '0' WHERE code_request = :ticketCode";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":ticketCode",$ticketModel->getTicketCode());
        $stmt->execute();

        $ret = true;
        $result = array("message"=>"","object"=>$ticketModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Updates ticket's times
     * 
     * pt_br Atualiza as horas da solicitação
     *
     * @param  ticketModel $ticketModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function updateTicketTime(ticketModel $ticketModel): array
    {        
        $sql = "UPDATE hdk_tbrequest_times SET ". $ticketModel->getTicketTimeField() ." = :timeValue WHERE CODE_REQUEST = :ticketCode";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":ticketCode",$ticketModel->getTicketCode());
        $stmt->bindValue(":timeValue",$ticketModel->getTicketTimeValue());
        $stmt->execute();

        $ret = true;
        $result = array("message"=>"","object"=>$ticketModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Updates in charge of the ticket into DB
     * pt_br Atualiza o responsável pela solicitação no banco de dados
     *
     * @param  ticketModel $ticketModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function saveAssumeTicket(ticketModel $ticketModel): array
    {   
        $aInCharge = $ticketModel->getInChargeList();
        $aNotes = $ticketModel->getNoteList();
        $aTimes = $ticketModel->getTimesList();
        
        try{
            $this->db->beginTransaction();

            $retTicketLog = $this->insertTicketLog($ticketModel);

            if($retTicketLog['status']){
                $remInCharge = $this->removeTicketInCharge($retTicketLog['push']['object']);
                
                // -- save in charge
                foreach($aInCharge as $k=>$v){
                    $ticketModel->setTicketCode($ticketModel->getTicketCode())
                                ->setIdInCharge($v['id'])
                                ->setInChargeType($v['type'])
                                ->setIsInCharge($v['isInCharge'])
                                ->setIsRepass($v['isRepassed'])
                                ->setIsTrack($v['isTrack']);

                    $insInCharge = $this->insertTicketInCharge($ticketModel);
                }
                
                // -- save notes
                foreach($aNotes as $k=>$v){
                    $ticketModel->setNotePublic($v['public'])
                                ->setNoteTypeID($v['type'])
                                ->setNote($v['note'])
                                ->setNoteDateTime($v['date'])
                                ->setNoteTotalMinutes($v['totalMinutes'])
                                ->setNoteStartHour($v['startHour'])
                                ->setNoteFinishHour($v['finishHour'])
                                ->setNoteExecutionDate($v['executionDate'])
                                ->setNoteHourType($v['hourType'])
                                ->setNoteIpAddress($v['ipAddress'])
                                ->setNoteIsCallback($v['callback'])
                                ->setNoteIsOpen(0);

                    $insNote = $this->insertTicketNote($ticketModel);
                }

                // -- changes status to in attendance
                $upStatus = $this->updateTicketStatus($ticketModel);

                // -- changes ticket's time
                foreach($aTimes as $k=>$v){
                    $ticketModel->setTicketTimeField($v['field'])
                                ->setTicketTimeValue($v['value']);

                    $upStatus = $this->updateTicketTime($ticketModel);
                }
                
            }
            
            $ret = true;
            $result = array("message"=>"","object"=>$retTicketLog['push']['object']);
            $this->db->commit();
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error trying update ticket's in charge (assume) ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
            $this->db->rollBack();
        }         
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Returns array with attendants list
     * pt_br Retorna array com a lista de atendentes
     *
     * @param  ticketModel $ticketModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchAttendants(ticketModel $ticketModel): array
    {        
        $sql = "SELECT idperson, TRIM(name) AS `name`
                  FROM tbperson
                 WHERE idtypeperson IN (1,3)
                   AND `status` = 'A'
              ORDER BY `name` ASC";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':ticketCode', $ticketModel->getTicketCode());
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $ticketModel->setAttendantList((!empty($aRet) && count($aRet) > 0) ? $aRet : array());

            $ret = true;
            $result = array("message"=>"","object"=>$ticketModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting attendants. ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Returns array with partners list
     * pt_br Retorna array com a lista de parcerias
     *
     * @param  ticketModel $ticketModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchPartners(ticketModel $ticketModel): array
    {        
        $sql = "SELECT idperson, TRIM(name) AS `name`
                  FROM tbperson
                 WHERE idtypeperson IN (5)
                   AND `status` = 'A'
              ORDER BY `name` ASC";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':ticketCode', $ticketModel->getTicketCode());
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $ticketModel->setPartnerList((!empty($aRet) && count($aRet) > 0) ? $aRet : array());

            $ret = true;
            $result = array("message"=>"","object"=>$ticketModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting partners. ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Returns array with group's abilities list
     * pt_br Retorna array com a lista de habilidades do grupo
     *
     * @param  ticketModel $ticketModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchGroupAbilities(ticketModel $ticketModel): array
    {        
        $sql = "SELECT grpp.name, serv.name as service, grp.idgroup, serv.idservice
                  FROM hdk_tbcore_service  serv,
                       hdk_tbgroup  grp,
                       tbperson  grpp,
                       hdk_tbgroup_has_service  relat
                 WHERE grp.idgroup = relat.idgroup
                   AND grpp.idperson = grp.idperson
                   AND serv.idservice = relat.idservice
                   AND grpp.idperson = :groupID";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':groupID', $ticketModel->getIdOperator());
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $ticketModel->setGridList((!empty($aRet) && count($aRet) > 0) ? $aRet : array());

            $ret = true;
            $result = array("message"=>"","object"=>$ticketModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error fetching group's abilities. ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Returns array with attendant's abilities list
     * pt_br Retorna array com a lista de habilidades do atendente
     *
     * @param  ticketModel $ticketModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchAttendantAbilities(ticketModel $ticketModel): array
    {        
        $sql = "SELECT per.name, serv.name as service, per.idperson, serv.idservice, grpper.name
                  FROM hdk_tbcore_service serv,
                       hdk_tbgroup grp,
                       hdk_tbgroup_has_service relat,
                       tbperson per,
                       tbperson grpper,
                       hdk_tbgroup_has_person relatp
                 WHERE grp.idgroup = relat.idgroup
                   AND grpper.idperson = grp.idperson
                   AND serv.idservice = relat.idservice
                   AND relatp.idperson = per.idperson
                   AND relatp.idgroup = grp.idgroup
                   AND per.idperson = :attendantID";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':attendantID', $ticketModel->getIdOperator());
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $ticketModel->setGridList((!empty($aRet) && count($aRet) > 0) ? $aRet : array());

            $ret = true;
            $result = array("message"=>"","object"=>$ticketModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error fetching attendant's abilities. ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Inserts repass date in 
     *
     * @param  ticketModel $ticketModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function insertRepassTicket(ticketModel $ticketModel): array
    {        
        $sql = "INSERT INTO hdk_tbrequest_repassed (date,idnote,code_request) 
                     VALUES (:logDate,:noteID,:ticketCode)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":ticketCode",$ticketModel->getTicketCode());
        $stmt->bindValue(":logDate",$ticketModel->getLogDate());
        $stmt->bindValue(":noteID",$ticketModel->getIdNote());
        $stmt->execute();

        $ret = true;
        $result = array("message"=>"","object"=>$ticketModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Save ticket's repass data into DB
     * pt_br Grava os dados da solicitação repassada
     *
     * @param  ticketModel $ticketModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function saveRepassTicket(ticketModel $ticketModel): array
    {   
        $aInCharge = $ticketModel->getInChargeList();
        $aNotes = $ticketModel->getNoteList();
        $lastKey = array_pop(array_keys($aNotes));
        
        try{
            $this->db->beginTransaction();

            $retTicketLog = $this->insertTicketLog($ticketModel);
            
            if($retTicketLog['status']){
                // -- save notes
                foreach($aNotes as $k=>$v){
                    $ticketModel->setNotePublic($v['public'])
                                ->setNoteTypeID($v['type'])
                                ->setNote($v['note'])
                                ->setNoteDateTime($v['date'])
                                ->setNoteTotalMinutes($v['totalMinutes'])
                                ->setNoteStartHour($v['startHour'])
                                ->setNoteFinishHour($v['finishHour'])
                                ->setNoteExecutionDate($v['executionDate'])
                                ->setNoteHourType($v['hourType'])
                                ->setNoteIpAddress($v['ipAddress'])
                                ->setNoteIsCallback($v['callback'])
                                ->setNoteIsOpen(0);

                    $insNote = $this->insertTicketNote($ticketModel);
                    if($insNote['status']){
                        $noteID = ($lastKey == $key) ? $insNote['push']['object']->getIdNote(): 0;
                    }
                }
                
                if(isset($noteID) && $noteID > 0){
                    $insRepass = $this->insertRepassTicket($ticketModel);
                }
                
                $remInCharge = $this->removeTicketInCharge($retTicketLog['push']['object']);
                
                // -- save in charge
                foreach($aInCharge as $k=>$v){
                    if(count($v) > 0) {
                        $ticketModel->setTicketCode($ticketModel->getTicketCode())
                                    ->setIdInCharge($v['id'])
                                    ->setInChargeType($v['type'])
                                    ->setIsInCharge($v['isInCharge'])
                                    ->setIsRepass($v['isRepassed'])
                                    ->setIsTrack($v['isTrack']);
                                    
                        $insInCharge = $this->insertTicketInCharge($ticketModel);
                    }
                }                
                
                // -- changes status to in attendance
                $upStatus = $this->updateTicketStatus($ticketModel);
                
                // -- update ticket action date
                $ticketModel->setTicketDateField("forwarded_date");                    
                $updTicketDate = $this->updateTicketDate($ticketModel);
                
            }
            
            $ret = true;
            $result = array("message"=>"","object"=>$retTicketLog['push']['object']);
            $this->db->commit();
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error trying save ticket's repass ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
            $this->db->rollBack();
        }         
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Inserts ticket's auxiliary attendant in DB
     * pt_br Adiciona attendente auxiliar da solicitação no BD
     *
     * @param  ticketModel $ticketModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function insertAuxiliaryAttendant(ticketModel $ticketModel): array
    {        
        $aInCharge = $ticketModel->getInChargeList();
        $aNotes = $ticketModel->getNoteList();
        
        try{
            $this->db->beginTransaction();
            // -- save in charge
            foreach($aInCharge as $k=>$v){
                $ticketModel->setTicketCode($ticketModel->getTicketCode())
                            ->setIdInCharge($v['id'])
                            ->setInChargeType($v['type'])
                            ->setIsInCharge($v['isInCharge'])
                            ->setIsRepass($v['isRepassed'])
                            ->setIsTrack($v['isTrack'])
                            ->setIsOperatorAux($v['isOperatorAux']);

                $insInCharge = $this->insertTicketInCharge($ticketModel);
            }

            // -- save notes
            foreach($aNotes as $k=>$v){
                $ticketModel->setNotePublic($v['public'])
                            ->setNoteTypeID($v['type'])
                            ->setNote($v['note'])
                            ->setNoteDateTime($v['date'])
                            ->setNoteTotalMinutes($v['totalMinutes'])
                            ->setNoteStartHour($v['startHour'])
                            ->setNoteFinishHour($v['finishHour'])
                            ->setNoteExecutionDate($v['executionDate'])
                            ->setNoteHourType($v['hourType'])
                            ->setNoteIpAddress($v['ipAddress'])
                            ->setNoteIsCallback($v['callback'])
                            ->setNoteIsOpen(0);

                $insNote = $this->insertTicketNote($ticketModel);
            }

            $ret = true;
            $result = array("message"=>"","object"=>$ticketModel);
            $this->db->commit();
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error trying insert ticket's auxiliary attendant ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
            $this->db->rollBack();
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * en_us Updates ticket's data
     * pt_br Atualiza os dados da solicitação
     *
     * @param  ticketModel $ticketModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function deleteAuxiliaryAttendant(ticketModel $ticketModel): array
    {        
        $sql = "DELETE FROM hdk_tbrequest_in_charge WHERE id_in_charge = :attendantID AND code_request = :ticketCode AND ind_operator_aux = 1";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":ticketCode",$ticketModel->getTicketCode());
            $stmt->bindValue(":attendantID",$ticketModel->getIdInCharge());
            $stmt->execute();

            $ret = true;
            $result = array("message"=>"","object"=>$ticketModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error trying remove ticket's auxiliary attendant ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Save ticket's note into DB
     * pt_br Grava o apontamento da solicitação no banco de dados
     *
     * @param  ticketModel $ticketModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function saveTicketNote(ticketModel $ticketModel): array
    {   
        $aNotes = $ticketModel->getNoteList();
        
        try{
            $this->db->beginTransaction();

            // -- save notes
            foreach($aNotes as $k=>$v){
                $ticketModel->setNotePublic($v['public'])
                            ->setNoteTypeID($v['type'])
                            ->setNote($v['note'])
                            ->setNoteDateTime($v['date'])
                            ->setNoteTotalMinutes($v['totalMinutes'])
                            ->setNoteStartHour($v['startHour'])
                            ->setNoteFinishHour($v['finishHour'])
                            ->setNoteExecutionDate($v['executionDate'])
                            ->setNoteHourType($v['hourType'])
                            ->setNoteIpAddress($v['ipAddress'])
                            ->setNoteIsCallback($v['callback'])
                            ->setNoteIsOpen(1);

                $insNote = $this->insertTicketNote($ticketModel);
            }
            
            $ret = true;
            $result = array("message"=>"","object"=>$ticketModel);
            $this->db->commit();
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error trying update ticket's in charge (assume) ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
            $this->db->rollBack();
        }         
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Saves in DB the link between note and attachment 
     * pt_br Grava no DB o vínculo entre apontamento e o anexo
     *
     * @param  ticketModel $ticketModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function insertNoteAttachment(ticketModel $ticketModel): array
    {        
        $sql = "CALL hdk_insertnoteattachments(:noteID,:fileName,@id)";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":noteID",$ticketModel->getIdNote(),\PDO::PARAM_INT|\PDO::PARAM_INPUT_OUTPUT);
            $stmt->bindParam(":fileName",$ticketModel->getFileName(),\PDO::PARAM_STR|\PDO::PARAM_INPUT_OUTPUT);
            $stmt->execute();

            $stmt = null;
            $sql2 = "SELECT @id noteatt_ID";
            $stmt2 = $this->db->prepare($sql2);
            $stmt2->execute();
            $aRet = $stmt2->fetch(\PDO::FETCH_ASSOC);
            
            $ticketModel->setIdAttachment((!empty($aRet) && !is_null($aRet['noteatt_ID'])) ? $aRet['noteatt_ID'] : 0);
            $ret = true;
            $result = array("message"=>"","object"=>$ticketModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error trying save link between note and attachment ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Deletes note's attachment from hdk_tbnote_has_attachments table
     * pt_br Exclui o anexo do apontamento da tabela hdk_tbnote_has_attachments
     *
     * @param  ticketModel $ticketModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function deleteNoteHasAttachment(ticketModel $ticketModel): array
    {        
        $sql = "DELETE FROM hdk_tbnote_has_attachments WHERE idnote_attachments = :attachmentID";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":attachmentID",$ticketModel->getIdAttachment());
        $stmt->execute();

        $ret = true;
        $result = array("message"=>"","object"=>$ticketModel);
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Deletes note's attachment from hdk_tbnote_attachments table
     * pt_br Exclui o anexo do apontamento da tabela hdk_tbnote_attachments
     *
     * @param  ticketModel $ticketModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function deleteNoteAttachmentData(ticketModel $ticketModel): array
    {        
        $sql = "DELETE FROM hdk_tbnote_attachments WHERE idnote_attachments = :attachmentID";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":attachmentID",$ticketModel->getIdAttachment());
        $stmt->execute();

        $ret = true;
        $result = array("message"=>"","object"=>$ticketModel);
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Deletes in DB the link between note and attachment 
     * pt_br Exclui no DB o vínculo entre apontamento e o anexo
     *
     * @param  ticketModel $ticketModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function removeNoteAttachment(ticketModel $ticketModel): array
    {        
        try{
            $rem = $this->deleteNoteHasAttachment($ticketModel);
            if($rem['status']){
                $delAttachment = $this->deleteNoteAttachmentData($rem['push']['object']);
            }

            $ret = true;
            $result = array("message"=>"","object"=>$rem['push']['object']);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error trying delete link note to uploaded file ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Returns date when attendant assumes the ticket
     * pt_br Retorna a data em que o atendente assume a solicitação
     *
     * @param  ticketModel $ticketModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function getAssumedDate(ticketModel $ticketModel): array
    {        
        $sql = "SELECT `date` FROM hdk_tbrequest_log WHERE cod_request = :ticketCode AND idstatus = 3 ORDER BY id ASC LIMIT 1";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':ticketCode', $ticketModel->getTicketCode());
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            $ticketModel->setAssumeDate((isset($aRet['date']) && !is_null($aRet['date'])) ? $aRet['date'] : "");

            $ret = true;
            $result = array("message"=>"","object"=>$ticketModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting assuming date ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Returns total minutes expended to attendance
     * pt_br Retorna o total de minutos gastos para atendimento
     *
     * @param  ticketModel $ticketModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function getExpendedDate(ticketModel $ticketModel): array
    {        
        $sql = "SELECT SUM(minutes) as minutes FROM hdk_tbnote WHERE code_request = :ticketCode";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':ticketCode', $ticketModel->getTicketCode());
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            $ticketModel->setMinExpendedTime((isset($aRet['minutes']) && !is_null($aRet['minutes'])) ? $aRet['minutes'] : 0);

            $ret = true;
            $result = array("message"=>"","object"=>$ticketModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting attendance expended time. ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Updates in charge of the ticket into DB
     * pt_br Atualiza o responsável pela solicitação no banco de dados
     *
     * @param  ticketModel $ticketModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function saveCloseTicket(ticketModel $ticketModel): array
    {   
        $evaluationDAO = new evaluationDAO();
        $evaluationModel = new evaluationModel();

        $aNotes = $ticketModel->getNoteList();
        $aTimes = $ticketModel->getTimesList();
        $statusID = $ticketModel->getIdStatus();
        
        try{
            $this->db->beginTransaction();

            // -- update ticket finish date
            $ticketModel->setTicketDateField("finish_date");                    
            $updTicketDate = $this->updateTicketDate($ticketModel);

            if($updTicketDate['status']){
                if($statusID == 4){
                    $evaluationModel->setTicketCode($updTicketDate['push']['object']->getTicketCode());
                    $retEvalToken = $evaluationDAO->insertEvaluationToken($evaluationModel);
                }elseif($statusID == 5){
                    // -- update ticket approval date
                    $ticketModel->setTicketDateField("approval_date");                    
                    $updApprovalDate = $this->updateTicketDate($ticketModel);
                }

                // -- update ticket log
                $retTicketLog = $this->insertTicketLog($ticketModel);
                
                // -- save notes
                foreach($aNotes as $k=>$v){
                    $ticketModel->setNotePublic($v['public'])
                                ->setNoteTypeID($v['type'])
                                ->setNote($v['note'])
                                ->setNoteDateTime($v['date'])
                                ->setNoteTotalMinutes($v['totalMinutes'])
                                ->setNoteStartHour($v['startHour'])
                                ->setNoteFinishHour($v['finishHour'])
                                ->setNoteExecutionDate($v['executionDate'])
                                ->setNoteHourType($v['hourType'])
                                ->setNoteIpAddress($v['ipAddress'])
                                ->setNoteIsCallback($v['callback'])
                                ->setNoteIsOpen(0);

                    $insNote = $this->insertTicketNote($ticketModel);
                }

                // -- changes ticket's status
                $upStatus = $this->updateTicketStatus($ticketModel);

                // -- changes ticket's time
                foreach($aTimes as $k=>$v){
                    $ticketModel->setTicketTimeField($v['field'])
                                ->setTicketTimeValue($v['value']);

                    $upStatus = $this->updateTicketTime($ticketModel);
                }
                
            }
            
            $ret = true;
            $result = array("message"=>"","object"=>$updTicketDate['push']['object']);
            $this->db->commit();
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error trying close ticket ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
            $this->db->rollBack();
        }         
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Saves ticket's reject into DB
     * pt_br Atualiza o responsável pela solicitação no banco de dados
     *
     * @param  ticketModel $ticketModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function saveRejectTicket(ticketModel $ticketModel): array
    {   
        $aInCharge = $ticketModel->getInChargeList();
        $aNotes = $ticketModel->getNoteList();
        
        try{
            $this->db->beginTransaction();

            $retTicketLog = $this->insertTicketLog($ticketModel);

            if($retTicketLog['status']){
                // -- save notes
                foreach($aNotes as $k=>$v){
                    $ticketModel->setNotePublic($v['public'])
                                ->setNoteTypeID($v['type'])
                                ->setNote($v['note'])
                                ->setNoteDateTime($v['date'])
                                ->setNoteTotalMinutes($v['totalMinutes'])
                                ->setNoteStartHour($v['startHour'])
                                ->setNoteFinishHour($v['finishHour'])
                                ->setNoteExecutionDate($v['executionDate'])
                                ->setNoteHourType($v['hourType'])
                                ->setNoteIpAddress($v['ipAddress'])
                                ->setNoteIsCallback($v['callback'])
                                ->setNoteIsOpen(0);

                    $insNote = $this->insertTicketNote($ticketModel);
                }

                // -- changes status to rejected
                $upStatus = $this->updateTicketStatus($ticketModel);

                $remInCharge = $this->removeTicketInCharge($retTicketLog['push']['object']);
                
                // -- save in charge
                foreach($aInCharge as $k=>$v){
                    $ticketModel->setTicketCode($ticketModel->getTicketCode())
                                ->setIdInCharge($v['id'])
                                ->setInChargeType($v['type'])
                                ->setIsInCharge($v['isInCharge'])
                                ->setIsRepass($v['isRepassed'])
                                ->setIsTrack($v['isTrack']);

                    $insInCharge = $this->insertTicketInCharge($ticketModel);
                }

                // -- update ticket rejection date
                $ticketModel->setTicketDateField("rejection_date");                    
                $updTicketDate = $this->updateTicketDate($ticketModel);
                
            }
            
            $ret = true;
            $result = array("message"=>"","object"=>$retTicketLog['push']['object']);
            $this->db->commit();
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error trying reject ticket ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
            $this->db->rollBack();
        }         
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Returns array with attendance source list
     * pt_br Retorna array com a lista de origens da solcitação
     *
     * @param  ticketModel $ticketModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchSources(ticketModel $ticketModel): array
    {        
        $sql = "SELECT idsource, name, icon FROM hdk_tbsource ORDER BY name ASC";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $ticketModel->setSourceList((!empty($aRet) && count($aRet) > 0) ? $aRet : array());

            $ret = true;
            $result = array("message"=>"","object"=>$ticketModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting sources. ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Saves the ticket opening and repass
     * pt_br Grava abertura e repasse da solicitação
     *
     * @param  ticketModel $ticketModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function saveOpenRepassTicket(ticketModel $ticketModel): array
    {   
        $aInCharge = $ticketModel->getInChargeList();
        $aNotes = $ticketModel->getNoteList();
        $aExtraFields = $ticketModel->getExtraFieldList();
        
        try{
            $this->db->beginTransaction();

            $insTicket = $this->insertTicket($ticketModel);

            if($insTicket['status']){
                // -- save in charge
                foreach($aInCharge as $k=>$v){
                    $ticketModel->setTicketCode($ticketModel->getTicketCode())
                                ->setIdInCharge($v['id'])
                                ->setInChargeType($v['type'])
                                ->setIsInCharge($v['isInCharge'])
                                ->setIsRepass($v['isRepassed'])
                                ->setIsTrack($v['isTrack']);

                    $insInCharge = $this->insertTicketInCharge($ticketModel);
                }

                $insTicketTimes = $this->insertTicketTimesNew($ticketModel);
                $insTicketDate = $this->insertTicketDate($ticketModel);
                
                // -- update ticket repass date
                $ticketModel->setTicketDateField("forwarded_date");                    
                $updTicketDate = $this->updateTicketDate($ticketModel);
                
                $insTicketLog = $this->insertTicketLog($ticketModel);
                
                // -- save notes
                foreach($aNotes as $k=>$v){
                    $ticketModel->setNotePublic($v['public'])
                                ->setNoteTypeID($v['type'])
                                ->setNote($v['note'])
                                ->setNoteDateTime($v['date'])
                                ->setNoteTotalMinutes($v['totalMinutes'])
                                ->setNoteStartHour($v['startHour'])
                                ->setNoteFinishHour($v['finishHour'])
                                ->setNoteExecutionDate($v['executionDate'])
                                ->setNoteHourType($v['hourType'])
                                ->setNoteIpAddress($v['ipAddress'])
                                ->setNoteIsCallback($v['callback'])
                                ->setNoteIsOpen(0);

                    $insNote = $this->insertTicketNote($ticketModel);
                }

                // -- save extra fields
                if(!empty($aExtraFields) && count($aExtraFields) > 0){
                    foreach($aExtraFields as $k=>$v){
                        if(!empty($v)){
                            $ticketModel->setExtraFieldId($k)
                                        ->setExtraFieldValue($v);
                            
                            $insExtraField = $this->insertTicketExtraField($ticketModel);
                        }
                    }
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
     * en_us Saves the ticket opening and repass
     * pt_br Grava abertura e repasse da solicitação
     *
     * @param  ticketModel $ticketModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function saveOpenFinishTicket(ticketModel $ticketModel): array
    {   
        $aInCharge = $ticketModel->getInChargeList();
        $aNotes = $ticketModel->getNoteList();
        $aExtraFields = $ticketModel->getExtraFieldList();
        
        try{
            $this->db->beginTransaction();

            $insTicket = $this->insertTicket($ticketModel);

            if($insTicket['status']){
                // -- save in charge
                foreach($aInCharge as $k=>$v){
                    $ticketModel->setTicketCode($ticketModel->getTicketCode())
                                ->setIdInCharge($v['id'])
                                ->setInChargeType($v['type'])
                                ->setIsInCharge($v['isInCharge'])
                                ->setIsRepass($v['isRepassed'])
                                ->setIsTrack((isset($v['isTrack']) && !empty($v['isTrack'])) ? $v['isTrack'] : 0);

                    $insInCharge = $this->insertTicketInCharge($ticketModel);
                }

                $insTicketTimes = $this->insertTicketTimesNew($ticketModel);
                $insTicketDate = $this->insertTicketDate($ticketModel);
                
                // -- update ticket repass date
                $ticketModel->setTicketDateField("finish_date");                    
                $updTicketDate = $this->updateTicketDate($ticketModel);
                
                $insTicketLog = $this->insertTicketLog($ticketModel);
                
                // -- save notes
                foreach($aNotes as $k=>$v){
                    $ticketModel->setNotePublic($v['public'])
                                ->setNoteTypeID($v['type'])
                                ->setNote($v['note'])
                                ->setNoteDateTime($v['date'])
                                ->setNoteTotalMinutes($v['totalMinutes'])
                                ->setNoteStartHour($v['startHour'])
                                ->setNoteFinishHour($v['finishHour'])
                                ->setNoteExecutionDate($v['executionDate'])
                                ->setNoteHourType($v['hourType'])
                                ->setNoteIpAddress($v['ipAddress'])
                                ->setNoteIsCallback($v['callback'])
                                ->setNoteIsOpen(0);

                    $insNote = $this->insertTicketNote($ticketModel);
                }

                // -- save extra fields
                if(!empty($aExtraFields) && count($aExtraFields) > 0){
                    foreach($aExtraFields as $k=>$v){
                        if(!empty($v)){
                            $ticketModel->setExtraFieldId($k)
                                        ->setExtraFieldValue($v);
                            
                            $insExtraField = $this->insertTicketExtraField($ticketModel);
                        }
                    }
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
     * Link the ticket with uploaded file 
     *
     * @param  ticketModel $ticketModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function insertTrelloCard(ticketModel $ticketModel): array
    {
        $sql = "INSERT INTO hdk_tbrequest_has_trello_card (code_request,idtrellocard,idperson)
                     VALUES(:ticketCode,:cardId,:personId)";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":ticketCode",$ticketModel->getTicketCode());
            $stmt->bindValue(":cardId",$ticketModel->getTrelloCardId());
            $stmt->bindValue(":personId",$ticketModel->getTrelloUserId());
            $stmt->execute();
            
            $ret = true;
            $result = array("message"=>"","object"=>$ticketModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error trying link ticket to trello card ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Returns note attachment data
     * pt_br Retorna os dados do anexo do apontamento
     *
     * @param  ticketModel $ticketModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function getNoteAttachment(ticketModel $ticketModel): array
    {        
        $sql = "SELECT filename FROM hdk_tbnote_attachments WHERE idnote_attachments = :attachmentID";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":attachmentID",$ticketModel->getIdAttachment());
        $stmt->execute();

        $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
        $ticketModel->setFileName((!empty($aRet['filename']) && !is_null($aRet['filename'])) ? $aRet['filename'] : "");

        $ret = true;
        $result = array("message"=>"","object"=>$ticketModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Returns ticket's attachment data
     * pt_br Retorna os dados do anexo da solicitação
     *
     * @param  ticketModel $ticketModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function getTicketAttachment(ticketModel $ticketModel): array
    {        
        $sql = "SELECT file_name FROM hdk_tbrequest_attachment WHERE idrequest_attachment = :attachmentID";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":attachmentID",$ticketModel->getIdAttachment());
        $stmt->execute();

        $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
        $ticketModel->setFileName((!empty($aRet['file_name']) && !is_null($aRet['file_name'])) ? $aRet['file_name'] : "");

        $ret = true;
        $result = array("message"=>"","object"=>$ticketModel);      
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Returns ticket's attachment or note's attachment
     * pt_br Retorna o anexo do ticket ou do apontamento
     *
     * @param  ticketModel $ticketModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function getAttachment(ticketModel $ticketModel): array
    {   
        try{
            switch($ticketModel->getAttachmentType()){
                case 'note':
                    $retAttachment = $this->getNoteAttachment($ticketModel);
                    break;
                case 'request':
                    $retAttachment = $this->getTicketAttachment($ticketModel);
                    break;
            }
            
            $ret = true;
            $result = array("message"=>"","object"=>$retAttachment['push']['object']);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error getting attachment data ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }         
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Deletes ticket's note from hdk_tbnote table
     * pt_br Exclui o apontamento da tabela hdk_tbnote
     *
     * @param  ticketModel $ticketModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function deleteTicketNote(ticketModel $ticketModel): array
    {        
        $sql = "CALL hdk_deletenote(:noteID)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":noteID",$ticketModel->getIdNote());
        $stmt->execute();

        $ret = true;
        $result = array("message"=>"","object"=>$ticketModel);
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Saves the ticket opening and repass
     * pt_br Grava abertura e repasse da solicitação
     *
     * @param  ticketModel $ticketModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function deleteNote(ticketModel $ticketModel): array
    {   
        try{
            $this->db->beginTransaction();

            $retNoteAttach = $this->fetchNoteAttachments($ticketModel);

            if($retNoteAttach['status']){
                
                $delNote = $this->deleteTicketNote($retNoteAttach['push']['object']);
            }
            
            $ret = true;
            $result = array("message"=>"","object"=>$retNoteAttach['push']['object']);
            $this->db->commit();
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error trying delete ticket's note", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
            $this->db->rollBack();
        }         
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Returns a object with user id
     *
     * @param  ticketModel $ticketModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function getUserDataByToken(ticketModel $ticketModel): array
    {        
        $sql = "SELECT b.idperson, a.idtypeperson
                  FROM tbperson a, hdk_tbviewbyurl b 
                 WHERE a.idperson = b.idperson
                   AND b.code_request = :ticketCode
                   AND b.token = :token";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':token', $ticketModel->getTicketToken());
            $stmt->bindParam(':ticketCode', $ticketModel->getTicketCode());
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            $ticketModel->setIdUser((isset($aRet['idperson']) && !is_null($aRet['idperson'])) ? $aRet['idperson'] : 0)
                        ->setUserTypeId((isset($aRet['idtypeperson']) && !is_null($aRet['idtypeperson'])) ? $aRet['idtypeperson'] : 0);

            $ret = true;
            $result = array("message"=>"","object"=>$ticketModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting user's data by url token. ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Updates ticket's open flag
     * pt_br Atualiza a flag de abertura de solicitação
     *
     * @param  ticketModel $ticketModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function updateTicketFlag(ticketModel $ticketModel): array
    {        
        $sql = "UPDATE hdk_tbrequest SET flag_opened = :ticketFlag WHERE code_request = :ticketCode";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":ticketCode",$ticketModel->getTicketCode());
            $stmt->bindValue(":ticketFlag",$ticketModel->getNoteIsOpen());
            $stmt->execute();

            $ret = true;
            $result = array("message"=>"","object"=>$ticketModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error trying update ticket's opened flag ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Updates note's view flag
     * pt_br Atualiza a flag de visualização do apontamento
     *
     * @param  ticketModel $ticketModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function updateNoteFlag(ticketModel $ticketModel): array
    {        
        $sql = "UPDATE hdk_tbnote 
                   SET flag_opened = :noteFlag
                 WHERE code_request = :ticketCode 
                   AND idperson !=  :userID";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":ticketCode",$ticketModel->getTicketCode());
            $stmt->bindValue(":noteFlag",$ticketModel->getNoteIsOpen());
            $stmt->bindValue(":userID",$ticketModel->getIdUser());
            $stmt->execute();

            $ret = true;
            $result = array("message"=>"","object"=>$ticketModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error trying update note's view flag ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Returns in charge, user/group who track and auxiliary attendant data
     * pt_br Retorna os dados do responsável pelo solicitação, usuário/grupo que acompanha e atendente auxiliar
     *
     * @param  ticketModel $ticketModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchInChargeEmail(ticketModel $ticketModel): array
    {        
        $sql = "SELECT id_in_charge, `name`, `type`, b.email
                  FROM hdk_tbrequest_in_charge a, tbperson b
                 WHERE b.idperson =  a.id_in_charge
                   AND (ind_in_charge = 1 OR ind_track = 1 OR ind_operator_aux = 1)
                   AND code_request = :ticketCode";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":ticketCode",$ticketModel->getTicketCode());
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $ticketModel->setGridList((!empty($aRet) && !is_null($aRet)) ? $aRet : array());

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
     * en_us Returns an array with extra fields
     * pt_br Retorna um array com campos adicionais
     *
     * @param  ticketModel $ticketModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchExtraFieldsByService(ticketModel $ticketModel): array
    {
        $sql = "SELECT a.idextra_field, b.name, b.type, b.lang_key_name, b.combo_options
                  FROM `hdk_tbcore_service_has_extra_field` a, `hdk_tbextra_field` b
                 WHERE b.idextra_field = a.idextra_field
                   AND a.idservice = :serviceId
              ORDER BY a.num_order";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':serviceId', $ticketModel->getIdService());
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $ticketModel->setExtraFieldList((!empty($aRet) && count($aRet) > 0) ? $aRet : array());

            $ret = true;
            $result = array("message"=>"","object"=>$ticketModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting service's extra fields. ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Saves ticket's extra fields
     * pt_br Grava os campos adicionais da solicitação
     *
     * @param  ticketModel $ticketModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function insertTicketExtraField(ticketModel $ticketModel): array
    {        
        $sql = "INSERT INTO `hdk_tbrequest_has_extra_field` (code_request,idextra_field,field_value)
                     VALUES (:ticketCode,:extraFieldId,:extraFieldName)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":ticketCode",$ticketModel->getTicketCode());
        $stmt->bindValue(":extraFieldId",$ticketModel->getExtraFieldId());
        $stmt->bindValue(":extraFieldName",$ticketModel->getExtraFieldValue());
        $stmt->execute();

        $ticketModel->setIdNote($this->db->lastInsertId());

        $ret = true;
        $result = array("message"=>"","object"=>$ticketModel);         
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Returns an array with ticket's extra fields
     * pt_br Retorna um array com campos adicionais da solicitação
     *
     * @param  ticketModel $ticketModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchTicketExtraFields(ticketModel $ticketModel): array
    {
        $sql = "SELECT DISTINCT a.idextra_field, c.name, c.type, c.lang_key_name, a.field_value
                  FROM `hdk_tbrequest_has_extra_field` a, `hdk_tbcore_service_has_extra_field` b, `hdk_tbextra_field` c
                 WHERE b.idextra_field = a.idextra_field
                   AND b.idextra_field = c.idextra_field
                   AND a.code_request = :ticketCode
              ORDER BY b.num_order";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':ticketCode', $ticketModel->getTicketCode());
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $ticketModel->setExtraFieldList((!empty($aRet) && count($aRet) > 0) ? $aRet : array());

            $ret = true;
            $result = array("message"=>"","object"=>$ticketModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting ticket's extra fields. ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Saves the ticket approval
     * pt_br Grava a aprovação do ticket
     *
     * @param  ticketModel $ticketModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function saveTicketApproval(ticketModel $ticketModel): array
    {   
        $ticketRulesDAO = new ticketRulesDAO();
        $ticketRulesModel = new ticketRulesModel();

        $aNotes = $ticketModel->getNoteList();
        
        try{
            $this->db->beginTransaction();

            // -- save notes
            foreach($aNotes as $k=>$v){
                $ticketModel->setNotePublic($v['public'])
                            ->setNoteTypeID($v['type'])
                            ->setNote($v['note'])
                            ->setNoteDateTime($v['date'])
                            ->setNoteTotalMinutes($v['totalMinutes'])
                            ->setNoteStartHour($v['startHour'])
                            ->setNoteFinishHour($v['finishHour'])
                            ->setNoteExecutionDate($v['executionDate'])
                            ->setNoteHourType($v['hourType'])
                            ->setNoteIpAddress($v['ipAddress'])
                            ->setNoteIsCallback($v['callback'])
                            ->setNoteIsOpen(0);

                $insNote = $this->insertTicketNote($ticketModel);
            }

            $ticketRulesModel->setTicketCode($insNote['push']['object']->getTicketCode())
                             ->setIdPerson($insNote['push']['object']->getIdCreator())
                             ->setNoteId($insNote['push']['object']->getIdNote());
            
            $updAppNote = $ticketRulesDAO->updateApprovalNote($ticketRulesModel);// update approval status
            $remInCharge = $this->removeTicketInCharge($insNote['push']['object']);

            if($updAppNote['status']){
                $retNext = $ticketRulesDAO->getNextApprover($ticketRulesModel);
                if($retNext['push']['object']->getIdPerson() > 0 ){
                    $insNote['push']['object']->setIdInCharge($retNext['push']['object']->getIdPerson())
                                              ->setInChargeType("P")
                                              ->setIsInCharge(1)
                                              ->setIsRepass("N")
                                              ->setIsTrack(0)
                                              ->setEmailTransaction("approve");

                    $insInCharge = $this->insertTicketInCharge($ticketModel);
                }else{
                    $retOriginal = $ticketRulesDAO->getOriginalInCharge($ticketRulesModel);
                    $insNote['push']['object']->setIdInCharge($retOriginal['push']['object']->getIdPerson())
                                              ->setInChargeType($retOriginal['push']['object']->getInChargeType())
                                              ->setIsInCharge(1)
                                              ->setIsRepass("N")
                                              ->setIsTrack(0)
                                              ->setEmailTransaction("new-ticket-user");

                    $insInCharge = $this->insertTicketInCharge($ticketModel);
                    // -- changes status to approved
                    $upStatus = $this->updateTicketStatus($ticketModel);

                    if(!empty($ticketModel->getExpireDate())){
                        // -- update ticket deadline in [hdk_tbrequest]
                        $updDeadline = $this->updateTicketDeadline($ticketModel);
                    }
                }              
            }
            
            $ret = true;
            $result = array("message"=>"","object"=>$insNote['push']['object']);
            $this->db->commit();
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error trying save ticket approval ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
            $this->db->rollBack();
        }         
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Saves the ticket approval
     * pt_br Grava a aprovação do ticket
     *
     * @param  ticketModel $ticketModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function saveApprovalReturn(ticketModel $ticketModel): array
    {   
        $ticketRulesDAO = new ticketRulesDAO();
        $ticketRulesModel = new ticketRulesModel();

        $aNotes = $ticketModel->getNoteList();
        
        try{
            $this->db->beginTransaction();

            // -- save notes
            foreach($aNotes as $k=>$v){
                $ticketModel->setNotePublic($v['public'])
                            ->setNoteTypeID($v['type'])
                            ->setNote($v['note'])
                            ->setNoteDateTime($v['date'])
                            ->setNoteTotalMinutes($v['totalMinutes'])
                            ->setNoteStartHour($v['startHour'])
                            ->setNoteFinishHour($v['finishHour'])
                            ->setNoteExecutionDate($v['executionDate'])
                            ->setNoteHourType($v['hourType'])
                            ->setNoteIpAddress($v['ipAddress'])
                            ->setNoteIsCallback($v['callback'])
                            ->setNoteIsOpen(0);

                $insNote = $this->insertTicketNote($ticketModel);
            }

            $ticketRulesModel->setTicketCode($insNote['push']['object']->getTicketCode())
                             ->setIdPerson($insNote['push']['object']->getApproverId())
                             ->setOrder($insNote['push']['object']->getApproverOrder());
            
            $updAppNote = $ticketRulesDAO->updateApprovalReturn($ticketRulesModel);// update approval status
            $remInCharge = $this->removeTicketInCharge($insNote['push']['object']);

            if($updAppNote['status']){
                $insNote['push']['object']->setIdInCharge($insNote['push']['object']->getApproverId())
                                          ->setInChargeType("P")
                                          ->setIsInCharge(1)
                                          ->setIsRepass("N")
                                          ->setIsTrack(0);

                $insInCharge = $this->insertTicketInCharge($insNote['push']['object']);
            }
            
            $ret = true;
            $result = array("message"=>"","object"=>$insNote['push']['object']);
            $this->db->commit();
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error trying save ticket approval return', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
            $this->db->rollBack();
        }         
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * en_us Saves the ticket approval
     * pt_br Grava a aprovação do ticket
     *
     * @param  ticketModel $ticketModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function saveTicketDisapproval(ticketModel $ticketModel): array
    {   
        $ticketRulesDAO = new ticketRulesDAO();
        $ticketRulesModel = new ticketRulesModel();

        $aNotes = $ticketModel->getNoteList();
        
        try{
            $this->db->beginTransaction();

            // -- save notes
            foreach($aNotes as $k=>$v){
                $ticketModel->setNotePublic($v['public'])
                            ->setNoteTypeID($v['type'])
                            ->setNote($v['note'])
                            ->setNoteDateTime($v['date'])
                            ->setNoteTotalMinutes($v['totalMinutes'])
                            ->setNoteStartHour($v['startHour'])
                            ->setNoteFinishHour($v['finishHour'])
                            ->setNoteExecutionDate($v['executionDate'])
                            ->setNoteHourType($v['hourType'])
                            ->setNoteIpAddress($v['ipAddress'])
                            ->setNoteIsCallback($v['callback'])
                            ->setNoteIsOpen(0);

                $insNote = $this->insertTicketNote($ticketModel);
            }

            $ticketRulesModel->setTicketCode($insNote['push']['object']->getTicketCode())
                             ->setIdPerson($insNote['push']['object']->getIdCreator())
                             ->setNoteId($insNote['push']['object']->getIdNote());
            
            $updAppNote = $ticketRulesDAO->updateDisapprovalNote($ticketRulesModel);// update approval status
            // -- changes status to approved
            $upStatus = $this->updateTicketStatus($ticketModel);
            
            $ret = true;
            $result = array("message"=>"","object"=>$insNote['push']['object']);
            $this->db->commit();
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error trying save ticket approval ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
            $this->db->rollBack();
        }         
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * getUrlTokenByUserId
     * 
     * en_us Returns url's token by user's id
     * pt_br Retorna o token da url pelo id do usuário
     *
     * @param  ticketModel $ticketModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function getUrlTokenByUserId(ticketModel $ticketModel): array
    {        
        $sql = "SELECT idviewbyurl,idperson,code_request,token,lifecycle
                  FROM hdk_tbviewbyurl
                 WHERE idperson = :personId
                   AND code_request = :ticketCode";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':personId', $ticketModel->getIdOperator());
            $stmt->bindParam(':ticketCode', $ticketModel->getTicketCode());
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            $ticketModel->setTicketToken((isset($aRet['token']) && !is_null($aRet['token']) && !empty($aRet['token'])) ? $aRet['token'] : "");

            $ret = true;
            $result = array("message"=>"","object"=>$ticketModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting url token by user id. ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
    }

    public function insertResponseSender(ticketModel $ticketModel): array
    {        
        $sql = "INSERT INTO hdk_tbrequest_response_sender (code_request,sender_email) VALUES (:ticketCode,:senderEmail)";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':ticketCode', $ticketModel->getTicketCode());
            $stmt->bindParam(':senderEmail', $ticketModel->getSenderEmail());
            $stmt->execute();

            $ret = true;
            $result = array("message"=>"","object"=>$ticketModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error saving ticket's response sender.", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
    }

    
}