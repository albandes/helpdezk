DELIMITER $$

DROP VIEW IF EXISTS `vw_NotesData`

$$

CREATE

VIEW vw_NotesData
AS
(
SELECT 
  n.idnote,
  n.code_request,
  n.idperson,
  p.name,
  n.entry_date,
  n.execution_date,
  n.start_hour,
  n.finish_hour,
  n.minutes,
  n.idtype,
  n.IND_CHAMADO,
  n.ip_adress,
  n.public,
  n.hour_type,
  n.service_value,
  n.callback,
  n.description 
FROM
  hdk_tbnote n,
  tbperson p 
WHERE p.idperson = n.idperson 
)
