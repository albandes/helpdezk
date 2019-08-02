/*
 *      Procedure Name  :  hkd_deleteRequest
 *      Database/Schema :  helpdesk
 *
 *      Description:
 *          Delete a specific request
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
 *         IN:  person_ID - the name of the friend to search for
 *         OUT: message

 *      Revision History:
 *
 *         Date:          Id:         Comment:
 *         2016/02/18     albandes    Original
 *		   2019/07/22     albandes    Change name
 *
 *
 */

DELIMITER $$

DROP PROCEDURE IF EXISTS `hdk_deleteRequest`$$

CREATE PROCEDURE `hdk_deleteRequest`(
	IN code_ID VARCHAR(20),
	OUT msg VARCHAR(100)
)
hkd_deleteRequest:BEGIN

	DECLARE num_requests INT;

	SELECT COUNT(idrequest) INTO num_requests FROM  hdk_tbrequest WHERE code_request = code_ID;

	IF ( num_requests = 0 ) THEN
		SET msg = "Request doesn´t exist !!!" ;
		LEAVE hkd_deleteRequest ;
	ELSE
		DELETE FROM hdk_tbrequest_dates WHERE code_request = code_ID ;
		DELETE FROM hdk_tbevaluation_token WHERE code_request = code_ID ;
		DELETE FROM hdk_tbrequest_times WHERE code_request = code_ID ;
		DELETE FROM hdk_tbrequest_log WHERE cod_request = code_ID ;
		DELETE FROM hdk_tbrequest_repassed WHERE code_request = code_ID ;
		DELETE FROM hdk_tbrequest_attachment WHERE code_request = code_ID ;
		DELETE FROM hdk_tbrequest_change_expire WHERE code_request = code_ID ;
		DELETE FROM hdk_tbrequest WHERE code_request = code_ID ;
		SET msg = "Request deleted !!!" ;
	END IF;

    END$$

DELIMITER ;