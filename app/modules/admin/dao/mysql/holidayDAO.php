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
     * @param  holidayModel $holidayModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchHolidays(holidayModel $holidayModel): array
    {

        $sql =      "SELECT a.idholiday, a.holiday_date, a.holiday_description, 
                            IFNULL(c.idperson,0) idperson, IFNULL(c.name,'') name
                       FROM tbholiday a
            LEFT OUTER JOIN tbholiday_has_company b
                         ON b.idholiday = a.idholiday
                  LEFT JOIN tbperson c
                         ON c.idperson = b.idperson
                      WHERE YEAR(a.holiday_date) = {$holidayModel->getYear()} ";
        $sql .= ($holidayModel->getIdCompany() != "" || $holidayModel->getIdCompany() != 0) ? "AND c.idperson = {$holidayModel->getIdCompany()} " : "AND b.idperson IS NULL ";
        $sql .= "ORDER BY holiday_date";

        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $holidayModel->setGridList($aRet);
            
            $ret = true;
            $result = array("message"=>"","object"=>$holidayModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting holidays ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
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
     * Return an array with holidays to display in grid
     *
     * @param  string $where
     * @return array
     */
    public function countHolidays($where=null): array
    {
        
        $sql = "SELECT COUNT(tbh.idholiday) total
                  FROM tbholiday tbh
             LEFT JOIN tbholiday_has_company tbhc
                    ON tbhc.idholiday = tbh.idholiday
             LEFT JOIN tbperson tbp
                    ON tbp.idperson = tbhc.idperson
                $where $group $order $limit";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
            $holiday = new holidayModel(); 
            $holiday->setTotalRows($aRet['total']);

            $ret = true;
            $result = array("message"=>"","object"=>$holiday);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error counting holidays ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * Insert the holiday into the database
     *
     * @param  holidayModel $holidayModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
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

            $holidayModel->setIdHoliday($this->db->lastInsertId());
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
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function insertHolidayHasCompany(holidayModel $holidayModel): array
    {        
        $sql = "INSERT INTO tbholiday_has_company (idholiday,idperson)
                     VALUES(:holidayID,:companyID)";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':holidayID', $holidayModel->getIdHoliday());
            $stmt->bindParam(':companyID', $holidayModel->getIdCompany());
            $stmt->execute();

            $ret = true;
            $result = array("message"=>"","object"=>$holidayModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error trying link holiday with company ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }

        return array("status"=>$ret,"push"=>$result);
    }
    
    /**
     * Returns an object with holiday data
     *
     * @param  holidayModel $holidayModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
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
            $stmt->bindParam(':holidayID', $holidayModel->getIdHoliday());
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
        
            if($aRet && count($aRet) > 0){
                $holidayModel->setDate($aRet['holiday_date'])
                             ->setDescription($aRet['holiday_description'])
                             ->setIdCompany(!empty($aRet['idperson']) ? $aRet['idperson'] : 0)
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
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
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
            $stmt->bindParam(':holidayID', $holidayModel->getIdHoliday());
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
     * @param  holidayModel $holidayModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchHolidayYears(holidayModel $holidayModel): array
    {        
        $sql = "SELECT DISTINCT YEAR(holiday_date) AS holiday_year
                  FROM tbholiday
                 WHERE (YEAR(holiday_date) <> YEAR(NOW()) AND YEAR(holiday_date) > 0)
              ORDER BY holiday_year DESC";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $holidayModel->setYearList($aRet);
            
            $ret = true;
            $result = array("message"=>"","object"=>$holidayModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting holiday years ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * Returns an array with years recorded on DB
     *
     * @param  holidayModel $holidayModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function fetchHolidayYearsByCompany(holidayModel $holidayModel): array
    {        
        $sql = "SELECT DISTINCT YEAR(a.holiday_date) AS holiday_year, b.idperson idcompany
                  FROM tbholiday a
       LEFT OUTER JOIN tbholiday_has_company b
                    ON a.idholiday = b.idholiday
                 WHERE (YEAR(a.holiday_date) <> YEAR(NOW()) AND YEAR(a.holiday_date) > 0)";
        $sql .= ($holidayModel->getIdCompany() != "" || $holidayModel->getIdCompany() != 0) ? " AND b.idperson = :companyID" : "";
        $sql .= " GROUP BY holiday_year
                 ORDER BY holiday_year DESC";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':companyID', $holidayModel->getIdCompany());
            $stmt->execute();
            $aRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            $holidayModel->setYearList($aRet);
            
            $ret = true;
            $result = array("message"=>"","object"=>$holidayModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error("Error getting holiday years by company ", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * Delete the holiday from the database
     *
     * @param  holidayModel $holidayModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function deleteHoliday(holidayModel $holidayModel): array
    {        
        $sql = "DELETE FROM tbholiday WHERE idholiday = :holidayID";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':holidayID', $holidayModel->getIdHoliday());
            $stmt->execute();

            $ret = true;
            $result = array("message"=>"","object"=>$holidayModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error trying delete holiday ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * Delete the link between holiday and company from the database
     *
     * @param  holidayModel $holidayModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function deleteHolidayCompany(holidayModel $holidayModel): array
    {        
        $sql = "DELETE FROM tbholiday_has_company WHERE idholiday = :holidayID";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':holidayID', $holidayModel->getIdHoliday());
            $stmt->execute();

            $ret = true;
            $result = array("message"=>"","object"=>$holidayModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error trying delete holiday ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

    /**
     * Returns a object with holiday data
     *
     * @param  holidayModel $holidayModel
     * @return array Parameters returned in array: 
     *               [status = true/false
     *                push =  [message = PDO Exception message 
     *                         object = model's object]]
     */
    public function getNationalHolidaysTotal(holidayModel $holidayModel): array
    {        
        $sql = "SELECT COUNT(*) AS num_holiday
                  FROM tbholiday a
             LEFT JOIN tbholiday_has_company b
                    ON a.idholiday = b.idholiday
                 WHERE holiday_date >= :startDate
                   AND holiday_date <= :endDate
                   AND b.idholiday IS NULL";
        
        try{
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':startDate', $holidayModel->getStartDate());
            $stmt->bindParam(':endDate', $holidayModel->getEndDate());
            $stmt->execute();

            $aRet = $stmt->fetch(\PDO::FETCH_ASSOC);
        
            $holidayModel->setTotalNational(($aRet['num_holiday'] && $aRet['num_holiday'] > 0) ? $aRet['num_holiday'] : 0);

            $ret = true;
            $result = array("message"=>"","object"=>$holidayModel);
        }catch(\PDOException $ex){
            $msg = $ex->getMessage();
            $this->loggerDB->error('Error getting national holidays total ', ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__, 'DB Message' => $msg]);
            
            $ret = false;
            $result = array("message"=>$msg,"object"=>null);
        }
        
        return array("status"=>$ret,"push"=>$result);
    }

}