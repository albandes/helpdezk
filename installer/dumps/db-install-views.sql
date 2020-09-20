CREATE OR REPLACE VIEW hdk_viewnotesdata AS (select `n`.`idnote` AS `idnote`,`n`.`code_request` AS `code_request`,`n`.`idperson` AS `idperson`,`p`.`name` AS `name`,`n`.`entry_date` AS `entry_date`,`n`.`execution_date` AS `execution_date`,`n`.`start_hour` AS `start_hour`,`n`.`finish_hour` AS `finish_hour`,`n`.`minutes` AS `minutes`,`n`.`idtype` AS `idtype`,`n`.`IND_CHAMADO` AS `IND_CHAMADO`,`n`.`ip_adress` AS `ip_adress`,`n`.`public` AS `public`,`n`.`hour_type` AS `hour_type`,`n`.`service_value` AS `service_value`,`n`.`callback` AS `callback`,`n`.`description` AS `description` from (`hdk_tbnote` `n` join `tbperson` `p`) where (`p`.`idperson` = `n`.`idperson`)); 


CREATE OR REPLACE VIEW hdk_viewrequestdata AS (select `req`.`code_request` AS `code_request`,`req`.`expire_date` AS `expire_date`,`req`.`entry_date` AS `entry_date`,`req`.`flag_opened` AS `flag_opened`,`req`.`subject` AS `subject`,`req`.`idperson_owner` AS `idperson_owner`,`req`.`idperson_creator` AS `idperson_creator`,`cre`.`name` AS `name_creator`,`cre`.`phone_number` AS `phone_number`,`cre`.`cel_phone` AS `cel_phone`,`cre`.`branch_number` AS `branch_number`,`req`.`idperson_juridical` AS `idcompany`,`req`.`idsource` AS `idsource`,`req`.`extensions_number` AS `extensions_number`,`source`.`name` AS `source`,`req`.`idstatus` AS `idstatus`,`req`.`idattendance_way` AS `idattendance_way`,`req`.`os_number` AS `os_number`,`req`.`serial_number` AS `serial_number`,`req`.`label` AS `label`,`req`.`description` AS `description`,`comp`.`name` AS `company`,`stat`.`user_view` AS `status`,`rtype`.`name` AS `type`,`rtype`.`idtype` AS `idtype`,`item`.`iditem` AS `iditem`,`item`.`name` AS `item`,`serv`.`idservice` AS `idservice`,`serv`.`name` AS `service`,`prio`.`name` AS `priority`,`prio`.`idpriority` AS `idpriority`,`inch`.`ind_in_charge` AS `ind_in_charge`,`inch`.`id_in_charge` AS `id_in_charge`,`resp`.`name` AS `in_charge`,`prio`.`color` AS `color`,`pers`.`name` AS `personname`,`pers`.`email` AS `email`,`pers`.`phone_number` AS `phone`,`pers`.`branch_number` AS `branch`,`inch`.`type` AS `typeincharge`,`dep`.`name` AS `department`,`dep`.`iddepartment` AS `iddepartment`,`source`.`name` AS `source_name`,`are`.`idarea` AS `idarea`,`are`.`name` AS `AREA`,(select `reason`.`reason` from `hdk_tbcore_reason` where (`hdk_tbcore_reason`.`idreason` = `req`.`idreason`)) AS `reason`,`req`.`idreason` AS `idreason`,(select `hdk_tbattendance_way`.`way` from `hdk_tbattendance_way` where (`hdk_tbattendance_way`.`idattendanceway` = `req`.`idattendance_way`)) AS `way_name` from ((((((((((((((((`hdk_tbrequest` `req` join `tbperson` `pers`) join `tbperson` `comp`) join `tbperson` `resp`) join `tbperson` `cre`) join `hdk_tbdepartment` `dep`) join `hdk_tbcore_type` `rtype`) join `hdk_tbcore_service` `serv`) join `hdk_tbcore_area` `are`) join `hdk_tbpriority` `prio`) join `hdk_tbcore_item` `item`) join `hdk_tbstatus` `stat`) join `hdk_tbsource` `source`) join `hdk_tbdepartment_has_person` `dep_pers`) join `hdk_tbrequest_in_charge` `inch`) left join `hdk_tbreason` `reason` on((`req`.`idreason` = `reason`.`idreason`))) left join `hdk_tbgroup` `grp` on((`resp`.`idperson` = `grp`.`idperson`))) where ((`req`.`idperson_owner` = `pers`.`idperson`) and (`req`.`idperson_creator` = `cre`.`idperson`) and (`req`.`idstatus` = `stat`.`idstatus`) and (`req`.`idperson_juridical` = `comp`.`idperson`) and (`req`.`idtype` = `rtype`.`idtype`) and (`req`.`idservice` = `serv`.`idservice`) and (`req`.`idpriority` = `prio`.`idpriority`) and (`req`.`idsource` = `source`.`idsource`) and (`req`.`code_request` = `inch`.`code_request`) and (`req`.`iditem` = `item`.`iditem`) and (`dep`.`iddepartment` = `dep_pers`.`iddepartment`) and (`pers`.`idperson` = `dep_pers`.`idperson`) and (`are`.`idarea` = `rtype`.`idarea`) and (`inch`.`id_in_charge` = `resp`.`idperson`) and (`inch`.`ind_in_charge` = 1))); 


