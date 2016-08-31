<?php
class holidays_model extends Model {
    public $database;

    public function __construct(){
        parent::__construct();
        $this->database = $this->getConfig('db_connect');
    }

    public function insertHoliday(array $dados) {
        $ins = $this->insert('tbholiday', $dados);
		if($ins) return true;
		else 	 return false;
    }
	
	public function insertHolidayHasCompany(array $dados) {
        $ins = $this->insert('tbholiday_has_company', $dados);
		if($ins) return true;
		else 	 return false;
    }
	
    public function selectHoliday($where = NULL, $order = NULL, $limit = NULL){
        $database = $this->getConfig('db_connect');
        if ($database == 'mysqlt') {
            $query = "	SELECT
						  tbh.idholiday,
						  tbh.holiday_date,
						  tbh.holiday_description,
						  tbp.idperson,
						  tbp.name
						from tbholiday tbh
						LEFT JOIN tbholiday_has_company tbhc
						ON tbhc.idholiday = tbh.idholiday
						LEFT JOIN tbperson tbp
						ON tbp.idperson = tbhc.idperson
						$where $order $limit" ;
        } elseif ($database == 'oci8po') {
            $limit = str_replace('LIMIT', "", $limit);
            $p     = explode(",", $limit);
            $start = $p[0] + 1; 
            $end   = $p[0] +  $p[1]; 
            $core  = "SELECT
						  tbh.idholiday,
						  tbh.holiday_date,
						  tbh.holiday_description,
						  tbp.idperson,
						  tbp.name
						from tbholiday tbh
						LEFT JOIN tbholiday_has_company tbhc
						ON tbhc.idholiday = tbh.idholiday
						LEFT JOIN tbperson tbp
						ON tbp.idperson = tbhc.idperson
						$where $order";
            $query =    "
                        SELECT   *
                          FROM   (SELECT                                          
                                        a  .*, ROWNUM rnum
                                    FROM   (  
                                              
                                            $core 

                                            ) a
                                   WHERE   ROWNUM <= $end)
                         WHERE   rnum >= $start         
                        ";
        }

        return $this->db->Execute($query);
    }
    public function countHoliday($where = NULL){
		$sel = $this->select("SELECT count(IDHOLIDAY) as total from tbholiday $where");
		return $sel;
    }
    public function deleteHoliday($where){
        return $this->delete('tbholiday', $where);
    }
    public function selectHolidaysData($id){
        return ($this->database == 'oci8po') ? $this->select("select to_char(holiday_date,'DD/MM/YYYY') holiday_date, holiday_description from tbholiday where idholiday=$id") : $this->select("select holiday_date, holiday_description from tbholiday where idholiday=$id") ;

    }
    public function countAllHolidays($year){
        $sel = $this->select("SELECT count(IDHOLIDAY) as total from tbholiday where HOLIDAY_DATE LIKE '%$year%'");
        return $sel->fields['total'];
    }
    public function selectHolidayByYear($year, $order=NULL){
    	$database = $this->getConfig('db_connect');
        if ($database == 'mysqlt') {
        	return $this->select("
        		SELECT
				  tbh.idholiday,
				  tbh.holiday_date,
				  tbh.holiday_description,
				  tbp.idperson,
				  tbp.name
				from tbholiday tbh
				LEFT JOIN tbholiday_has_company tbhc
				ON tbhc.idholiday = tbh.idholiday
				LEFT JOIN tbperson tbp
				ON tbp.idperson = tbhc.idperson
				where tbh.holiday_date LIKE '%$year%'
        		$order") ;
        } elseif ($database == 'oci8po') {    			
        	return $this->select("
        		SELECT
				  tbh.idholiday,						  
				  to_char(tbh.holiday_date,'DD/MM/YYYY') holiday_date.
				  tbh.holiday_description,
				  tbp.idperson,
				  tbp.name
				from tbholiday tbh
				LEFT JOIN tbholiday_has_company tbhc
				ON tbhc.idholiday = tbh.idholiday
				LEFT JOIN tbperson tbp
				ON tbp.idperson = tbhc.idperson
				where tbh.holiday_date LIKE '%$year%' 
				$order");
        	//return $this->select("SELECT IDHOLIDAY, to_char(holiday_date,'DD/MM/YYYY') holiday_date, HOLIDAY_DESCRIPTION  from tbholiday where HOLIDAY_DATE LIKE '%$year%' $order");
		}    	
        
    }
    public function updateHoliday($id,$desc,$date){
        //die("UPDATE tbholiday set holiday_date=$date, holiday_description='$desc' where idholiday=$id");
        return $this->db->Execute("UPDATE tbholiday set holiday_date=$date, holiday_description='$desc' where idholiday=$id");
    }
	public function getYearsHolidays(){
        $database = $this->getConfig('db_connect');
        if ($database == 'mysqlt') {   
		    return $this->select("
                                SELECT
                                   DATE_FORMAT(holiday_date, '%Y') as holiday_year
                                from tbholiday
                                where year(holiday_date) <> year(now())
                                group by holiday_year
                                order by holiday_year
                                ");
        } elseif ($database == 'oci8po') {
            return $this->select("
                                SELECT   X.HOLIDAY_YEAR
                                    FROM   (SELECT   TO_CHAR (HOLIDAY_DATE, 'YYYY') HOLIDAY_YEAR
                                              FROM   TBHOLIDAY
                                             WHERE   TO_CHAR (HOLIDAY_DATE, 'YYYY') !=
                                                        TO_CHAR (SYSDATE, 'YYYY')) X
                                GROUP BY   X.HOLIDAY_YEAR
                                ORDER BY   X.HOLIDAY_YEAR DESC
                                ");
        }
	}
	public function holidayDelete($id){
        return $this->db->Execute("DELETE FROM tbholiday WHERE idholiday=$id");
    }
    public function holidayDeleteHasCompany($id){
        return $this->db->Execute("DELETE FROM tbholiday_has_company WHERE idholiday=$id");
    }
}