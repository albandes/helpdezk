<?php

namespace App\modules\admin\dao\mysql;

use App\core\Database;
use App\modules\admin\models\mysql\holidayModel;

class holidayDAO extends Database
{
    public function __construct()
    {
        parent::__construct(); 
    }

    public function fetchHolidays(): array
    {

        $sql = "SELECT tbh.idholiday, tbh.holiday_date, tbh.holiday_description, tbp.idperson, tbp.name
                  FROM tbholiday tbh
             LEFT JOIN tbholiday_has_company tbhc
                    ON tbhc.idholiday = tbh.idholiday
             LEFT JOIN tbperson tbp
                    ON tbp.idperson = tbhc.idperson";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
        }catch(\PDOException $ex){
            $this->loggerDB->error("Error getting holidays ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $ex->getMessage()]);
            return null;
        }
        
        $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        if(!$aRet){
            return array();
        }
        
        return $aRet;
    }
    
    /**
     * queryHolidays
     *
     * @param  string $where
     * @param  string $group
     * @param  string $order
     * @param  string $limit
     * @return array
     */
    public function queryHolidays($where=null,$group=null,$order=null,$limit=null): array
    {

        $sql = "SELECT tbh.idholiday, tbh.holiday_date, tbh.holiday_description, tbp.idperson, tbp.name
                  FROM tbholiday tbh
             LEFT JOIN tbholiday_has_company tbhc
                    ON tbhc.idholiday = tbh.idholiday
             LEFT JOIN tbperson tbp
                    ON tbp.idperson = tbhc.idperson
                $where $group $order $limit";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
        }catch(\PDOException $ex){
            $this->loggerDB->error("Error getting holidays ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $ex->getMessage()]);
            return array();
        }
        
        $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        if(!$aRet){
            return array();
        }
        
        return $aRet;
    }
}