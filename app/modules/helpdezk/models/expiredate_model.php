<?php
if(class_exists('Model')) {
    class DynamicRequestExpireDate_model extends Model {}
} elseif(class_exists('cronModel')) {
    class DynamicRequestExpireDate_model extends cronModel {}
} elseif(class_exists('apiModel')) {
    class DynamicRequestExpireDate_model extends apiModel {}
}

class expiredate_model extends DynamicRequestExpireDate_model {

	public function getExpireDateService($id){
		return $this->select("SELECT hours_attendance, days_attendance, ind_hours_minutes FROM hdk_tbcore_service WHERE idservice = $id");
	}
	
	public function getExpireDatePriority($id){
		return $this->select("SELECT limit_hours, limit_days FROM hdk_tbpriority WHERE idpriority = $id");
	}
	
	public function getDaysHoliday($start, $end){
		return $this->select("SELECT COUNT(*) AS num_holiday FROM tbholiday WHERE holiday_date >= '$start' AND holiday_date <= '$end'");
	}
	
	public function getNationalDaysHoliday($start, $end){
		return $this->select("
			SELECT
			  COUNT(*) AS num_holiday
			FROM tbholiday a
			LEFT JOIN tbholiday_has_company b
			ON a.idholiday = b.idholiday
			WHERE holiday_date >= '$start'
			    AND holiday_date <= '$end'
			AND b.idholiday IS NULL
		");
	}
	
	public function getCompanyDaysHoliday($start, $end, $idperson){
		return $this->select("
			SELECT
			  COUNT(*) AS num_holiday
			FROM tbholiday a
			LEFT JOIN tbholiday_has_company b
			ON a.idholiday = b.idholiday
			WHERE holiday_date >= '$start'
			    AND holiday_date <= '$end'
			AND b.idperson = $idperson
		");
	}
	
	public function getIdCustumerByService($idservice){		
		$ret = $this->db->Execute("	select grp.idcustomer
									from hdk_tbgroup grp,
									  hdk_tbcore_service serv,
									  hdk_tbgroup_has_service grp_serv
									where grp.idgroup = grp_serv.idgroup
									    and serv.idservice = grp_serv.idservice
									    and serv.idservice = $idservice");
        if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret->fields['idcustomer'];		
	}
	
	public function getBusinessDays(){
		$ret = $this->select("SELECT num_day_week, begin_morning, end_morning, begin_afternoon, end_afternoon FROM hdk_tbwork_calendar_new WHERE business_day = 1");
		if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
            return false;
        }
        return $ret;
	}

}
?>
