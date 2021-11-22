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

    public function insertHoliday(string $holidayDate, string $holidayDescription): ?holidayModel
    {        
        $sql = "INSERT INTO tbholiday (holiday_date,holiday_description)
                     VALUES(:holidayDate,,:description)";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':holidayDate,', $holidayDate,);
            $stmt->bindParam(':holidayDescription', $holidayDescription);
            $stmt->execute();
        }catch(\PDOException $ex){
            $this->loggerDB->error('Error trying insert holiday ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $ex->getMessage()]);
            return null;
        }

        $holiday = new holidayModel(); 
        $holiday->setIdholiday($this->db->lastInsertId()); 
        
        return $holiday;
    }

    public function insertHolidayHasCompany(int $holidayID, int $company)
    {        
        $sql = "INSERT INTO tbholiday (holiday_date,holiday_description)
                     VALUES(:holidayDate,,:description)";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':holidayDate,', $holidayDate,);
            $stmt->bindParam(':holidayDescription', $holidayDescription);
            $stmt->execute();
        }catch(\PDOException $ex){
            $this->loggerDB->error('Error trying insert holiday ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $ex->getMessage()]);
            return null;
        }

        $holiday = new holidayModel(); 
        $holiday->setIdholiday($this->db->lastInsertId()); 
        
        return $holiday;
    }

    /**
     * Returns a query date format so use in a database query
     * Use in method formatSaveDate
     *
     * @param  string   $date       Date
     * @param  string   $format     Date format
     * @return string   
     *
     */
    public function getSaveDate(string $date, string $format): string
    {
        $sql = "SELECT STR_TO_DATE(:date,:format) AS date";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':date,', $date,);
            $stmt->bindParam(':format', $format);
            $stmt->execute();
        }catch(\PDOException $ex){
            $this->loggerDB->error('Error trying format date to save in DB ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $ex->getMessage()]);
            return null;
        }

        $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        if(!$aRet){
            return null;
        }
        
        return $aRet['date'];
    }
}