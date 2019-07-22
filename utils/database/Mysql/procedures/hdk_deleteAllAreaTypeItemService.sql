

DELIMITER $$

USE `demo`$$

DROP PROCEDURE IF EXISTS `hdk_deleteAllAreaTypeItemService`$$

CREATE DEFINER=`pipeadm`@`%` PROCEDURE `hdk_deleteAllAreaTypeItemService`()
BEGIN
		DELETE FROM hdk_tbrequest_dates  ;
		DELETE FROM hdk_tbevaluation_token;
		DELETE FROM hdk_tbrequest_times  ;
		DELETE FROM hdk_tbrequest_log  ;
		DELETE FROM hdk_tbrequest_repassed  ;
		DELETE FROM hdk_tbrequest_in_charge;
		DELETE FROM hdk_tbrequest_attachment  ;
		DELETE FROM hdk_tbrequest_change_expire ;
		DELETE FROM hdk_tbrequest_emailcron;
		DELETE FROM hdk_tbrequest  ;
    END$$

DELIMITER ;
