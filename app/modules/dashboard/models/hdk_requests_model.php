<?php
class hdk_requests_model extends Model 
{
    public function getRequests($idperson,$idPersonGroups, $date_format, $hour_format) {
		$sql = "
			select
			  req.code_request,
			  req.expire_date,			  
			  DATE_FORMAT(req.expire_date,'$date_format') as date,
			  DATE_FORMAT(req.expire_date,'$hour_format') as hour,			  
			  req.entry_date,
			  req.subject,
			  req.idperson_owner,
			  req.flag_opened,
			  pers.name              as personname,
			  req.idperson_juridical as idcompany,
			  comp.name              as company,
			  dep.name                  department,
			  resp.name              as in_charge,
			  inch.id_in_charge      as id_in_charge,
			  inch.type              as type_in_charge,
			  stat.name              as `status`,
			  stat.idstatus_source,
			  req_type.name          as `type`,
			  item.name              as item,
			  serv.name              as service,
			  prio.name              as priority,
			  prio.color,
			  grp.idgroup            as grp_in_charge
			FROM (hdk_tbrequest req,
			  tbperson pers,
			  tbperson comp,
			  tbperson resp,
			  hdk_tbdepartment dep,
			  hdk_tbdepartment_has_person dep_pers,
			  hdk_tbstatus stat,
			  hdk_tbcore_type req_type,
			  hdk_tbcore_service serv,
			  hdk_tbpriority prio,
			  hdk_tbcore_item item,
			  hdk_tbrequest_in_charge inch
							)left join hdk_tbgroup grp on (inch.id_in_charge = grp.idperson and resp.idperson = grp.idperson)
			where req.idperson_owner = pers.idperson
				AND inch.id_in_charge = resp.idperson
				and inch.code_request = req.code_request
				and pers.idperson = dep_pers.idperson
				and dep_pers.iddepartment = dep.iddepartment
				and dep.idperson = comp.idperson
				and req.idstatus = stat.idstatus
				and req.idtype = req_type.idtype
				and req.iditem = item.iditem
				and req.idservice = serv.idservice
				and req.idpriority = prio.idpriority
				and req.code_request = inch.code_request
				and inch.ind_in_charge = 1
						 AND stat.idstatus_source =1     and  inch.id_in_charge in(".$idperson . $idPersonGroups .") 
									 order by 
					  req.expire_date desc LIMIT 0, 20
		";
		
		return $this->select($sql);
    }

	public function getOperatorGroups($persongroups) {
		$sql = "
				select
				   idperson
				from hdk_tbgroup
				where idgroup in('$persongroups')
				";
			return $this->select($sql);
	}

}

?>
