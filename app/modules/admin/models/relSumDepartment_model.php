<?php
class relSumDepartment_model extends Model{
    public function getSummarizedReq($where = null){
        $sel = $this->select("
        	select emp.name as company, depto.iddepartment, depto.name as department,attway.idattendanceway, attway.way as att_way, count(sol.code_request) as total
			from hdk_tbrequest sol, tbperson emp, tbperson usu, hdk_tbdepartment depto, hdk_tbdepartment_has_person usudepto, HDK_TBATTENDANCE_WAY attway
			where sol.idperson_juridical = emp.idperson
			and depto.idperson = emp.idperson
			and usu.idperson = sol.idperson_owner
			and usu.idperson = usudepto.idperson
			and depto.iddepartment = usudepto.iddepartment
            and attway.idattendanceway = sol.idattendance_way
			$where
			group by emp.name, depto.iddepartment, depto.name,attway.idattendanceway,attway.way
			order by emp.name desc , depto.name
        ");
        return $sel;
    }
	
	public function getSummarizedTime($where = null){
		// changed 30/09/2014 09:27 Rafael Stoever
        $sel = $this->select("
				with apontamentos_normal as (
				select sol.idattendance_way,sum(apont.minutes) as minutes,count(1) total from
									 hdk_tbnote                  apont,
									 hdk_tbrequest               sol,
									 hdk_tbdepartment            depto
							   where apont.code_request = sol.code_request
								 and apont.hour_type = 1
								 $where
								 and nvl(apont.minutes,0) > 0
								 and sol.idperson_juridical = depto.idperson
								 group by sol.idattendance_way),
				apontamentos_extras as (
				select sol.idattendance_way,sum(apont.minutes) as minutes,count(1) total from
									 hdk_tbnote                  apont,
									 hdk_tbrequest               sol,
									 hdk_tbdepartment            depto
							   where apont.code_request = sol.code_request
								 and apont.hour_type = 2
								 $where
								 and nvl(apont.minutes,0) > 0
								 and sol.idperson_juridical = depto.idperson
								 group by sol.idattendance_way),
				apontamentos_total as (
				select sol.idattendance_way,sum(apont.minutes) as minutes,count(1) total from
									 hdk_tbnote                  apont,
									 hdk_tbrequest               sol,
									 hdk_tbdepartment            depto
							   where apont.code_request = sol.code_request
								 $where
								 and nvl(apont.minutes,0) > 0
								 and sol.idperson_juridical = depto.idperson
								 group by sol.idattendance_way)                 
				select attway.way as att_way,
					   nvl(apont_extras.minutes,0) as min_extras,
					   nvl(apont_normal.minutes,0) as min_normal,
					   nvl(apont_total.minutes,0) as min
					   from apontamentos_normal           apont_normal,
							apontamentos_extras           apont_extras,
							apontamentos_total            apont_total,            
							HDK_TBATTENDANCE_WAY          attway
				where attway.idattendanceway = apont_normal.idattendance_way(+)
				  and attway.idattendanceway = apont_extras.idattendance_way(+)
				  and attway.idattendanceway = apont_total.idattendance_way(+)
				  and (nvl(apont_normal.total,0)+nvl(apont_extras.total,0)) > 0
        ");
        return $sel;
    }
}
?>
