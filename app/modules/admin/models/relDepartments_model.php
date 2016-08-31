<?php
class relDepartments_model extends Model{


    public function getDepartments($date_format, $where = NULL){
		// changed 30/09/2014 09:27 Rafael Stoever
        $fator = $this->getconfig('fator_conversao_hs_extra');
        $sel = $this->select("

		with 
        apontamentos_normal as (
        select sol.code_request,sol.idattendance_way,nvl(sum(apont.minutes),0) as minutes from
                   hdk_tbnote                  apont,
                   hdk_tbrequest               sol,
                   hdk_tbdepartment            depto
                 where apont.code_request = sol.code_request
                 and apont.hour_type = 1
                 $where
                 and nvl(apont.minutes,0) > 0
                 and sol.idperson_juridical = depto.idperson
                 group by sol.code_request,sol.idattendance_way
        ),
        apontamentos_extras as (
        select sol.code_request,sol.idattendance_way,(nvl(sum(apont.minutes),0)+nvl(sum(apont.minutes),0)*".$fator.") as minutes from
                   hdk_tbnote                  apont,
                   hdk_tbrequest               sol,
                   hdk_tbdepartment            depto
                 where apont.code_request = sol.code_request
                 and apont.hour_type = 2
                 $where
                 and nvl(apont.minutes,0) > 0
                 and sol.idperson_juridical = depto.idperson
                 group by sol.code_request,sol.idattendance_way
        ),
        a as (
        select sol.code_request,
             to_char(sol.entry_date, 'dd/mm/yyyy') as entry_date,
             usu.name as username,
             depto.name as department,
             solstat.name as status
             ,sol.subject
             ,usu.idperson
             ,sol.idattendance_way
          from 
              hdk_tbrequest               sol
             ,tbperson                    usu
             ,hdk_tbdepartment            depto
             ,hdk_tbdepartment_has_person usudepto
             ,hdk_tbstatus                solstat
             ,hdk_tbnote                  apont
         where usu.idperson = sol.idperson_owner
           and usu.idperson = usudepto.idperson
           and depto.iddepartment = usudepto.iddepartment
           and solstat.idstatus = sol.idstatus 
           and apont.code_request = sol.code_request
		   and nvl(apont.minutes,0) > 0
           $where
           group by sol.code_request,
             to_char(sol.entry_date, 'dd/mm/yyyy') 
             ,usu.name
             ,depto.name 
             ,solstat.name
             ,sol.subject
             ,usu.idperson
             ,sol.idattendance_way
        ),
        b  as (SELECT tbperson.name,hdk_tbdepartment_has_person.idperson
              FROM hdk_tbdepartment_has_person, hdk_tbdepartment, tbperson
             WHERE hdk_tbdepartment_has_person.iddepartment = hdk_tbdepartment.iddepartment
               AND tbperson.idperson = hdk_tbdepartment.idperson
        )
        select a.*,b.name as company,attway.way,(nvl(apont_normal.minutes,0)+nvl(apont_extras.minutes,0)) as minutes
        from a,b
             ,HDK_TBATTENDANCE_WAY attway
             ,apontamentos_normal apont_normal
             ,apontamentos_extras apont_extras
        where b.idperson = a.idperson
        and a.idattendance_way = attway.idattendanceway
        and a.idattendance_way = apont_normal.idattendance_way(+)
        and a.code_request     = apont_normal.code_request(+)
        and a.idattendance_way = apont_extras.idattendance_way(+)
        and a.code_request     = apont_extras.code_request(+)
		order by 1
		 ");
        return $sel;
    }
}
?>
