<?php

class holidays_model extends Model {
    public $database;

    public function __construct(){
        parent::__construct();
        $this->database = $this->getConfig('db_connect');
    }
    public function insertHoliday(Array $dados) {
        $ins = $this->insert('tbholiday', $dados);
        if ($ins) {
            return "cadastrado";
        } else {
            return "erro";
        }
    }
    public function selectHoliday($where = NULL, $order = NULL, $limit = NULL){
        return ($this->database == 'oci8po') ? $this->select("SELECT IDHOLIDAY, to_char(holiday_date,'DD/MM/YYYY') holiday_date, HOLIDAY_DESCRIPTION  from tbholiday $where $order $limit") : $this->select("SELECT IDHOLIDAY, HOLIDAY_DATE, HOLIDAY_DESCRIPTION  from tbholiday $where $order $limit") ;

    }
    public function countHoliday($where = NULL, $order = NULL, $limit = NULL){
        $sel = $this->select("SELECT count(IDHOLIDAY) as total from tbholiday $where $order $limit");
        return $sel;
    }
    public function deleteHoliday($where){
        return $this->delete('tbholiday', $where);
    }
    public function selectHolidaysData($id){
        return $this->select("");
        return ($this->database == 'oci8po') ? $this->select("select to_char(holiday_date,'DD/MM/YYYY') holiday_date, holiday_description from tbholiday where idholiday='$id'") : $this->select("select holiday_date, holiday_description from tbholiday where idholiday='$id'") ;
    }
    public function countAllHolidays($year){
        $sel = $this->select("SELECT count(IDHOLIDAY) as total from tbholiday where HOLIDAY_DATE LIKE '%$year%'");
        return $sel->fields['total'];
    }
    public function selectHolidayByYear($year, $order=NULL){
        return ($this->database == 'oci8po') ? $this->select("SELECT IDHOLIDAY, to_char(holiday_date,'DD/MM/YYYY') holiday_date, HOLIDAY_DESCRIPTION  from tbholiday where HOLIDAY_DATE LIKE '%$year%' $order") : $this->select("SELECT IDHOLIDAY, HOLIDAY_DATE, HOLIDAY_DESCRIPTION  from tbholiday where HOLIDAY_DATE LIKE '%$year%' $order") ;

    }
    public function updateHoliday($id,$desc,$date){
        return $this->db->Execute("UPDATE tbholiday set holiday_date='$date', holiday_description='$desc' where idholiday='$id'");
    }
}

?>
