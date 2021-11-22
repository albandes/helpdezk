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
        //echo "{$sql}\n";
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
    
    /**
     * Insert the holiday into the database
     *
     * @param  string $date
     * @param  string $description
     * @return holidayModel
     */
    public function insertHoliday(string $date, string $description): ?holidayModel
    {        
        $sql = "INSERT INTO tbholiday (holiday_date,holiday_description)
                     VALUES(:date,:description)";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':date', $date);
            $stmt->bindParam(':description', $description);
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
     * Link the holiday with a company
     *
     * @param  mixed $holidayID
     * @param  mixed $companyID
     * @return holidayModel
     */
    public function insertHolidayHasCompany(int $holidayID, int $companyID): ?holidayModel
    {        
        $sql = "INSERT INTO tbholiday_has_company (idholiday,idperson)
                     VALUES(:holidayID,:companyID)";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':holidayID', $holidayID);
            $stmt->bindParam(':companyID', $companyID);
            $stmt->execute();
        }catch(\PDOException $ex){
            $this->loggerDB->error('Error trying link holiday with company ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $ex->getMessage()]);
            return null;
        }

        $holiday = new holidayModel(); 
        $holiday->setIdcompany($this->db->lastInsertId()); 
        
        return $holiday;
    }

    /**
     * Returns a query date format so use in a database query
     * Use in method appServices::_formatSaveDate
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
            $stmt->bindParam(':date', $date);
            $stmt->bindParam(':format', $format);
            $stmt->execute();
        }catch(\PDOException $ex){
            $this->loggerDB->error('Error trying format date to save in DB ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $ex->getMessage()]);
            return null;
        }
        
        $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if(!$aRet || empty($aRet)){
            return null;
        }
        
        return $aRet['date'];
    }
}