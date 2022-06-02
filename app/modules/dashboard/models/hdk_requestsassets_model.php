<?php

class hdk_requestsassets_model extends Model 
{

	public function getStatusByEntryDate($idstatus,$entrydate) 
	{
		$sql = "
			select
			  COUNT(req.idrequest) as total,
			  stat.name            as status_name,
			  stat.idstatus,
			  stat.idstatus_source,
			  req.entry_date
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
			   hdk_tbrequest_in_charge inch)
			  left join hdk_tbgroup grp
				on (inch.id_in_charge = grp.idperson
					and resp.idperson = grp.idperson)
			WHERE req.entry_date <= '$entrydate'
			and req.idperson_owner = pers.idperson
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
				and req.idservice <> 251
			    and stat.idstatus = '$idstatus'
			";
			//print $sql . "<br>";
			return $this->select($sql);
	}

}

?>
