DELIMITER $$

DROP PROCEDURE IF EXISTS  `hdk_deleteAreaTypeItemService`$$


CREATE PROCEDURE `hdk_deleteAreaTypeItemService`(_list VARCHAR(200))
BEGIN
    SET @LIST=_list; -- assume this a parameter from the stored procedure
	DELETE FROM hdk_tbreason WHERE idservice IN (SELECT idservice FROM `hdk_tbcore_service` WHERE iditem IN (SELECT iditem FROM `hdk_tbcore_item` WHERE idtype IN (SELECT idtype FROM `hdk_tbcore_type` WHERE FIND_IN_SET(idarea,@LIST)))) ;
	DELETE FROM hdk_tbgroup_has_service WHERE idservice IN (SELECT idservice FROM `hdk_tbcore_service` WHERE iditem IN (SELECT iditem FROM `hdk_tbcore_item` WHERE idtype IN (SELECT idtype FROM `hdk_tbcore_type` WHERE FIND_IN_SET(idarea,@LIST)))) ;
	DELETE FROM hdk_tbgetemaildepartment WHERE idgetemail IN (SELECT idgetemail FROM  hdk_tbgetemail WHERE idservice IN (SELECT idservice FROM `hdk_tbcore_service` WHERE iditem IN (SELECT iditem FROM `hdk_tbcore_item` WHERE idtype IN (SELECT idtype FROM `hdk_tbcore_type` WHERE FIND_IN_SET(idarea,@LIST))))) ;
	DELETE FROM hdk_tbgetemail WHERE idservice IN (SELECT idservice FROM `hdk_tbcore_service` WHERE iditem IN (SELECT iditem FROM `hdk_tbcore_item` WHERE idtype IN (SELECT idtype FROM `hdk_tbcore_type` WHERE FIND_IN_SET(idarea,@LIST)))) ;
	DELETE FROM hdk_tbcore_service WHERE iditem IN (SELECT iditem FROM `hdk_tbcore_item` WHERE idtype IN (SELECT idtype FROM `hdk_tbcore_type` WHERE FIND_IN_SET(idarea,@LIST)));
	DELETE FROM hdk_tbcore_item WHERE idtype IN (SELECT idtype FROM `hdk_tbcore_type` WHERE FIND_IN_SET(idarea,@LIST));
	DELETE FROM hdk_tbcore_type WHERE FIND_IN_SET(idarea,@LIST);
	DELETE FROM hdk_tbcore_area WHERE FIND_IN_SET(idarea,@LIST);
END$$

DELIMITER ;