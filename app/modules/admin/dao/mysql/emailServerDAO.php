<?php

namespace App\modules\admin\dao\mysql;

use App\core\Database;
use App\modules\admin\models\mysql\emailServerModel;

class emailServerDAO extends Database
{
    
    public function __construct()
    {
        parent::__construct(); 
    }
    
    /**
     * Return an array with emailServers to display in grid
     *
     * @param  string $where
     * @param  string $group
     * @param  string $order
     * @param  string $limit
     * @return array
     */
    public function queryEmailServers($where=null,$group=null,$order=null,$limit=null): array
    {
        
        $sql = "SELECT idemailserver,a.idservertype,b.name servertype,a.name servername,user,`password`,
                        port,apikey,apisecret,apiendpoint,a.status,`default`
                  FROM tbemailserver a
                  JOIN tbservertype b
                    ON b.idservertype = a.idservertype 
                $where $group $order $limit";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $emailServer = new emailServerModel(); 
            $emailServer->setGridList($aRet);

            $ret = true;
            $result = array("message"=>"","object"=>$emailServer);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting emailServers ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * Return an array with rows total for grid pagination 
     *
     * @param  string $where
     * @return array
     */
    public function countEmailServers($where=null): array
    {
        
        $sql = "SELECT COUNT(idemailserver) total
                  FROM tbemailserver a
                  JOIN tbservertype b
                    ON b.idservertype = a.idservertype 
                $where";
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            $emailServer = new emailServerModel();
            $emailServer->setTotalRows($aRet['total']);

            $ret = true;
            $result = array("message"=>"","object"=>$emailServer);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error counting email servers ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * Inserts email data to send in DB
     *
     * @param  mixed $emailSrvModel
     * @return array
     */
    public function insertEmailCron(emailServerModel $emailServerModel): array
    {        
        $sql = "INSERT INTO tbemailcron(idemailserver,idmodule,code,date_in,send,tag)
                     VALUES (:emailSrvID,:moduleID,:code,NOW(),0,:tag)";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':emailSrvID', $emailServerModel->getIdEmailServer());
            $stmt->bindValue(':moduleID', $emailServerModel->getIdModule());
            $stmt->bindValue(':code', $emailServerModel->getCode());
            $stmt->bindValue(':tag', $emailServerModel->getTag());
            $stmt->execute();

            $emailServerModel->setIdEmailCron($this->db->lastInsertId());
            $ret = true;
            $result = array("message"=>"","object"=>$emailServerModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error insert email cron", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

}