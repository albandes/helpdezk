<?php
class helpers_model extends Model{
       public function getDiasUteisM(){
           $sel=$this->select("select num_day_week, begin_morning, end_morning, begin_afternoon, end_afternoon 
			from hdk_tbwork_calendar
			where business_day = 1 order by num_day_week");
           return $sel;
       }
}
?>
