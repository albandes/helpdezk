<?php
class relRequestMonth_model extends Model{
    public function getYears(){
        $sel = $this->select("SELECT DATE_FORMAT(entry_date, '%Y') as year FROM hdk_tbrequest GROUP BY year ORDER BY year DESC");
        return $sel;
    }
	
	public function getSource(){
        $sel = $this->select("SELECT idsource, name FROM hdk_tbsource ORDER BY idsource ASC");
        return $sel;
    }
	
	public function getArea(){
        $sel = $this->select("SELECT idarea, name FROM hdk_tbcore_area ORDER BY name ASC");
        return $sel;
    }
	
	public function getReport($year,$source = null,$company = null,$operator = null){
        $sel = $this->select("SELECT
							  count(a.code_request) as total,
							  DATE_FORMAT(a.entry_date, '%m') as month,
							  DATE_FORMAT(entry_date, '%Y') as year
							FROM hdk_tbrequest a,
							  hdk_tbrequest_in_charge b
							WHERE DATE_FORMAT(a.entry_date, '%Y') = $year
							    AND b.code_request = a.code_request
							    AND b.ind_in_charge = 1
							    $source
							    $company
							    $operator
							GROUP BY month, year
							ORDER BY year DESC, month ASC");
        return $sel;
    }
	
	
	
}