/*
 *      Procedure Name  :  hdk_deleteAllRequests
 *      Database/Schema :  helpdesk
 *
 *      Description:
 *          Delete all requests from database
 *
 *      Tables Impacted :
 *        hdk_tbrequest_dates
 *        hdk_tbevaluation_token
 *        hdk_tbrequest_times
 *        hdk_tbrequest_log
 *        hdk_tbrequest_repassed
 *        hdk_tbrequest_attachment
 *        hdk_tbrequest
 *
 *      Params:

 *      Revision History:
 *
 *         Date:          Id:         Comment:
 *         2016/02/19     albandes    Original
 *         2019/07/22     albandes    Change name
 *
 *
 */


DELIMITER $$

DROP PROCEDURE IF EXISTS `hdk_deleteAllRequests`$$

CREATE PROCEDURE `hdk_deleteAllRequests`()

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
