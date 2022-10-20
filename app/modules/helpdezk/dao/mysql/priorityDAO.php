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
     * Return an array with department to display in grid
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
        
        $sql = "SELECT idpriority, `name`, `order`, `default`, color, limit_days, limit_hours, `status`
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
     * Return an array with rows total for grid pagination
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
    

}