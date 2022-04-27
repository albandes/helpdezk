<?php

namespace App\modules\helpdezk\dao\mysql;

use App\core\Database;

use App\modules\helpdezk\models\mysql\ticketModel;

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
        $sql = "SELECT DISTINCT a.idfingerprint, a.idpersontype, `description`, accessid, a.`status`, a.idperson, 
                        COALESCE(a.`name`, pst.name, pp.name, p.name) `fpname`
                  FROM bmm_tbfingerprint a
                  JOIN bmm_tbpersontype b
                    ON a.idpersontype = b.idpersontype
       LEFT OUTER JOIN bmm_tbturnstile_fingerprint tfp
                    ON tfp.idfingerprint = a.idfingerprint
       LEFT OUTER JOIN acd_tbstudent ast
                    ON ast.idstudent = a.idperson AND
                        a.idpersontype = 1
       LEFT OUTER JOIN tbperson_profile pst
                    ON pst.idperson_profile = ast.idperson_profile
       LEFT OUTER JOIN acd_tbparent ap
                    ON ap.idparent = a.idperson
       LEFT OUTER JOIN tbperson_profile pp
                    ON pp.idperson_profile = ap.idperson_profile AND
                        a.idpersontype = 7
       LEFT OUTER JOIN tbperson p
                    ON p.idperson = a.idperson AND
                        a.idpersontype = 6
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
        
        $sql = "SELECT COUNT(DISTINCT a.idfingerprint)
                  FROM bmm_tbfingerprint a
                  JOIN bmm_tbpersontype b
                    ON a.idpersontype = b.idpersontype
       LEFT OUTER JOIN bmm_tbturnstile_fingerprint tfp
                    ON tfp.idfingerprint = a.idfingerprint
       LEFT OUTER JOIN acd_tbstudent ast
                    ON ast.idstudent = a.idperson AND
                        a.idpersontype = 1
       LEFT OUTER JOIN tbperson_profile pst
                    ON pst.idperson_profile = ast.idperson_profile
       LEFT OUTER JOIN acd_tbparent ap
                    ON ap.idparent = a.idperson
       LEFT OUTER JOIN tbperson_profile pp
                    ON pp.idperson_profile = ap.idperson_profile AND
                        a.idpersontype = 7
       LEFT OUTER JOIN tbperson p
                    ON p.idperson = a.idperson AND
                        a.idpersontype = 6 
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
     * Returns a object with fingerprint's template
     *
     * @param  ticketModel $fingerprintModel
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

    
}