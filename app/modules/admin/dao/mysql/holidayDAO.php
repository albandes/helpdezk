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
    
    /**
     * Returns a list of holidays by selected company and year
     *
     * @param  int $companyID
     * @param  int $year
     * @return array
     */
    public function fetchHolidays(int $companyID, int $year): array
    {

        $sql = "SELECT a.idholiday, a.holiday_date, a.holiday_description, 
                        IFNULL(c.idperson,0) idperson, IFNULL(c.name,'') name
                  FROM tbholiday a
       LEFT OUTER JOIN tbholiday_has_company b
                    ON b.idholiday = a.idholiday
             LEFT JOIN tbperson c
                    ON c.idperson = b.idperson
                 WHERE YEAR(a.holiday_date) = $year ";
        $sql .= ($companyID != "" || $companyID != 0) ? "AND c.idperson = {$companyID} " : "AND b.idperson IS NULL ";
        $sql .= "ORDER BY holiday_date";

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
     * Return an array with holidays to display in grid
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

            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $holiday = new holidayModel(); 
            $holiday->setGridList($aRet);

            $ret = true;
            $result = array("message"=>"","object"=>$holiday);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting holidays ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * Insert the holiday into the database
     *
     * @param  holidayModel $holidayModel
     * @return array
     */
    public function insertHoliday(holidayModel $holidayModel): array
    {        
        $sql = "INSERT INTO tbholiday (holiday_date,holiday_description)
                     VALUES(:date,:description)";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':date', $holidayModel->getDate());
            $stmt->bindParam(':description', $holidayModel->getDescription());
            $stmt->execute();

            $holidayModel->setIdholiday($this->db->lastInsertId());
            $ret = true;
            $result = array("message"=>"","object"=>$holidayModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error trying insert holiday ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * Link the holiday with a company
     *
     * @param  holidayModel $holidayModel
     * @return array
     */
    public function insertHolidayHasCompany(holidayModel $holidayModel): array
    {        
        $sql = "INSERT INTO tbholiday_has_company (idholiday,idperson)
                     VALUES(:holidayID,:companyID)";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':holidayID', $holidayModel->getIdholiday());
            $stmt->bindParam(':companyID', $holidayModel->getIdcompany());
            $stmt->execute();

            $ret = true;
            $result = array("message"=>"","object"=>$holidayModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error trying link holiday with company ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        $holiday = new holidayModel(); 
        $holiday->setIdcompany($this->db->lastInsertId()); 
        
        return array("status"=>$ret,"push"=>$result);
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

    /**
     * Returns a query date format so use in a database query
     * Use in method appServices::_formatDate
     *
     * @param  string   $date       Date
     * @param  string   $format     Date format
     * @return string   
     *
     */
    public function getDate(string $date, string $format): string
    {
        $sql = "SELECT DATE_FORMAT(:date,:format) AS date";
        
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
    
    /**
     * Returns a ibject with holiday data
     *
     * @param  holidayModel $holidayModel
     * @return array
     */
    public function getHoliday(holidayModel $holidayModel): array
    {        
        $sql = "SELECT tbh.idholiday, tbh.holiday_date, tbh.holiday_description, tbp.idperson, tbp.name
                  FROM tbholiday tbh
             LEFT JOIN tbholiday_has_company tbhc
                    ON tbhc.idholiday = tbh.idholiday
             LEFT JOIN tbperson tbp
                    ON tbp.idperson = tbhc.idperson
                 WHERE tbh.idholiday = :holidayID";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':holidayID', $holidayModel->getIdholiday());
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
        
            if($aRet && count($aRet) > 0){
                $holidayModel->setDate($aRet['holiday_date'])
                             ->setDescription($aRet['holiday_description'])
                             ->setIdcompany(!empty($aRet['idperson']) ? $aRet['idperson'] : 0)
                             ->setCompany(!empty($aRet['name']) ? $aRet['name'] : "");
            }

            $ret = true;
            $result = array("message"=>"","object"=>$holidayModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error getting holiday data ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * Update the holiday into the database
     *
     * @param  holidayModel $holidayModel
     * @return array
     */
    public function updateHoliday(holidayModel $holidayModel): array
    {        
        $sql = "UPDATE tbholiday 
                   SET holiday_date = :date,
                       holiday_description = :description
                 WHERE idholiday = :holidayID";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':date', $holidayModel->getDate());
            $stmt->bindParam(':description', $holidayModel->getDescription());
            $stmt->bindParam(':holidayID', $holidayModel->getIdholiday());
            $stmt->execute();

            $ret = true;
            $result = array("message"=>"","object"=>$holidayModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error trying insert holiday ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * Returns an array with years recorded on DB
     *
     * @return array
     */
    public function fetchHolidayYears(): array
    {        
        $sql = "SELECT DISTINCT YEAR(holiday_date) AS holiday_year
                  FROM tbholiday
                 WHERE (YEAR(holiday_date) <> YEAR(NOW()) AND YEAR(holiday_date) > 0)
              ORDER BY holiday_year DESC";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
        }catch(\PDOException $ex){
            $this->loggerDB->error("Error getting holiday years ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $ex->getMessage()]);
            return null;
        }
        
        $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        if(!$aRet){
            return array();
        }
        
        return $aRet;
    }

    /**
     * Returns an array with years recorded on DB
     *
     * @param  int $companyID
     * @return array
     */
    public function fetchHolidayYearsByCompany(int $companyID): array
    {        
        $sql = "SELECT DISTINCT YEAR(a.holiday_date) AS holiday_year, b.idperson idcompany
                  FROM tbholiday a
       LEFT OUTER JOIN tbholiday_has_company b
                    ON a.idholiday = b.idholiday
                 WHERE (YEAR(a.holiday_date) <> YEAR(NOW()) AND YEAR(a.holiday_date) > 0)";
        $sql .= ($companyID != "" || $companyID != 0) ? " AND b.idperson = :companyID" : "";
        $sql .= " GROUP BY holiday_year
                 ORDER BY holiday_year DESC";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':companyID', $companyID);
            $stmt->execute();
        }catch(\PDOException $ex){
            $this->loggerDB->error("Error getting holiday years by company ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $ex->getMessage()]);
            return null;
        }
        
        $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        if(!$aRet){
            return array();
        }
        
        return $aRet;
    }

    /**
     * Delete the holiday from the database
     *
     * @param  int $holidayID
     * @return holidayModel
     */
    public function deleteHoliday(int $holidayID): ?holidayModel
    {        
        $sql = "DELETE FROM tbholiday WHERE idholiday = :holidayID";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':holidayID', $holidayID);
            $stmt->execute();
        }catch(\PDOException $ex){
            $this->loggerDB->error('Error trying delete holiday ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $ex->getMessage()]);
            return null;
        }

        $holiday = new holidayModel(); 
        $holiday->setIdholiday($holidayID); 
        
        return $holiday;
    }

    /**
     * Delete the link between holiday and company from the database
     *
     * @param  int $holidayID
     * @return holidayModel
     */
    public function deleteHolidayCompany(int $holidayID): ?holidayModel
    {        
        $sql = "DELETE FROM tbholiday_has_company WHERE idholiday = :holidayID";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':holidayID', $holidayID);
            $stmt->execute();
        }catch(\PDOException $ex){
            $this->loggerDB->error('Error trying delete holiday ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $ex->getMessage()]);
            return null;
        }

        $holiday = new holidayModel(); 
        $holiday->setIdholiday($holidayID); 
        
        return $holiday;
    }

}