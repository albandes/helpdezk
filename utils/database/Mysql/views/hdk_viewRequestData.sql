
CREATE
    VIEW `hdk_viewrequestData`
    AS
(

SELECT
  req.code_request,
  req.expire_date,
  req.entry_date,
  req.flag_opened,
  req.subject,
  req.idperson_owner,
  req.idperson_creator,
  cre.name AS name_creator,
  cre.phone_number AS phone_number,
  cre.cel_phone AS cel_phone,
  cre.branch_number AS branch_number,
  req.idperson_juridical AS idcompany,
  req.idsource,
  req.extensions_number,
  source.name AS source,
  req.idstatus,
  req.idattendance_way,
  req.os_number,
  req.serial_number,
  req.label,
  req.description,
  comp.name AS company,
  stat.user_view AS `status`,
  rtype.name AS `type`,
  rtype.idtype,
  item.iditem,
  item.name AS item,
  serv.idservice,
  serv.name AS service,
  prio.name AS priority,
  prio.idpriority,
  inch.ind_in_charge,
  inch.id_in_charge,
  resp.name AS in_charge,
  prio.color,
  pers.name AS personname,
  pers.email,
  pers.phone_number AS phone,
  pers.branch_number AS branch,
  inch.type AS typeincharge,
  dep.name AS department,
  dep.iddepartment,
  source.name AS source_name,
  are.idarea,
  are.name as area,
  (select
  name
   from hdk_tbcore_reason
   where idreason = req.idreason) as reason,
  (select
  way
   from hdk_tbattendance_way
   where idattendanceway = req.idattendance_way) as way_name
FROM
  (
    hdk_tbrequest req,
    tbperson pers,
    tbperson comp,
    tbperson resp,
    tbperson cre,
    hdk_tbdepartment AS dep,
    hdk_tbcore_type rtype,
    hdk_tbcore_service serv,
    hdk_tbcore_area are,
    hdk_tbpriority prio,
    hdk_tbcore_item item,
    hdk_tbstatus stat,
    hdk_tbsource AS source,
    hdk_tbdepartment_has_person AS dep_pers,
    hdk_tbrequest_in_charge AS inch
  )
  LEFT JOIN hdk_tbcore_reason AS reason
    ON (req.idreason = reason.idreason)
  LEFT JOIN hdk_tbgroup AS grp
    ON (resp.idperson = grp.idperson)
WHERE req.idperson_owner = pers.idperson
  AND req.idperson_creator = cre.idperson
  AND req.idstatus = stat.idstatus
  AND req.idperson_juridical = comp.idperson
  AND req.idtype = rtype.idtype
  AND req.idservice = serv.idservice
  AND req.idpriority = prio.idpriority
  AND req.idsource = source.idsource
  AND req.code_request = inch.code_request
  AND req.iditem = item.iditem
  AND dep.iddepartment = dep_pers.iddepartment
  AND pers.idperson = dep_pers.idperson
  AND are.idarea = rtype.idarea
  AND inch.id_in_charge = resp.idperson
  AND inch.ind_in_charge = 1

);
