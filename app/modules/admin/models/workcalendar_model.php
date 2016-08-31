<?php
class workcalendar_model extends Model{    	
	public function getWorkCalendar(){
        $ret = $this->select("SELECT
								  num_day_week,
								  business_day,
								  begin_morning,
								  end_morning,
								  begin_afternoon,
								  end_afternoon
								FROM hdk_tbwork_calendar_new
								ORDER BY num_day_week ASC");
		if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
        }
        return $ret;
    }
  	public function updateWorkCalendar($data, $id){
        $ret = $this->update("hdk_tbwork_calendar_new", $data, "num_day_week = $id");
										
		if (!$ret) {
            $sError = "Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $this->db->ErrorMsg();
            $this->error($sError);
        }
        return $ret;
    }
}