

DELIMITER $$

USE `demo`$$

DROP PROCEDURE IF EXISTS `hdk_deleteAllAreaTypeItemService`$$

CREATE PROCEDURE `hdk_deleteAllAreaTypeItemService`()
BEGIN

  DELETE FROM hdk_tbcore_reason;
  DELETE FROM hdk_tbgroup_has_service;
  DELETE FROM hdk_tbgetemaildepartment;
  DELETE FROM hdk_tbgetemail;
  DELETE FROM hdk_tbcore_service;
  DELETE FROM hdk_tbcore_item;
  DELETE FROM hdk_tbcore_type;
  DELETE FROM hdk_tbcore_area;
END$$

DELIMITER ;
