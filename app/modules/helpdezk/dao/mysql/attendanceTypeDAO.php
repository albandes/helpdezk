<?php

namespace App\modules\helpdezk\dao\mysql;

use App\core\Database;
use App\modules\helpdezk\models\mysql\attendanceTypeModel;

class attendanceTypeDAO extends Database
{
    
    public function __construct()
    {
        parent::__construct(); 
    }
    
    /**
     * Return an array with attendance types to display in grid
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
    public function queryAttendanceTypes($where=null,$group=null,$order=null,$limit=null): array
    {
        
        $sql = "SELECT idattendanceway, way, `status`, `default` 
                  FROM hdk_tbattendance_way
                  $where $group $order $limit";
               // echo "{$sql}\n";
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $attendanceType = new attendanceTypeModel(); 
            $attendanceType->setGridList($aRet);

            $ret = true;
            $result = array("message"=>"","object"=>$attendanceType);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting attendance types ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
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
    public function countAttendanceTypes($where=null): array
    {        
        $sql = "SELECT COUNT(idattendanceway) total
                  FROM hdk_tbattendance_way
                $where";
    
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            $attendanceType = new attendanceTypeModel();
            $attendanceType->setTotalRows($aRet['total']);

            $ret = true;
            $result = array("message"=>"","object"=>$attendanceType);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error counting attendance types ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
    

}