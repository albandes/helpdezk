/*
 *      Procedure Name  :  delete_user_without_requests
 *      Database/Schema :  helpdesk
 *
 *      Description:
 *          delete a user if he doesn´t have an requests
 *
 *      Tables Impacted :
 *          tbaddress
 *          tbnaturalperson
 *          tbjuridicalperson
 *          hdk_tbdepartment_has_person
 *          hdk_tbconfig_user
 *          tbperson
 *
 *      Params:
 *          IN:  person_ID - user ID
 *          OUT: message
 *
 *      Revision History:
 *
 *         Date:          Id:         Comment:
 *         2016/02/18     albandes    Original
 *
 */
DELIMITER $$

DROP PROCEDURE IF EXISTS `delete_user_without_requests`$$

CREATE PROCEDURE `delete_user_without_requests`(IN person_ID INT, OUT msg VARCHAR(100))
delete_user:BEGIN

	DECLARE num_requests INT;
	DECLARE num_user INT;

	SELECT COUNT(*) INTO num_user FROM tbperson WHERE idperson = person_ID ;
	IF ( num_user = 0 ) THEN
		SET msg = "User not exists !!!" ;
		LEAVE delete_user ;
	END IF;
	IF NOT EXISTS (SELECT * FROM tbperson WHERE idperson = person_ID AND idtypeperson = 2) THEN
		SET msg = "Person exists, but is not a user  !!!" ;
		LEAVE delete_user ;
	END IF;

	SELECT COUNT(idrequest) INTO num_requests FROM  hdk_tbrequest WHERE idperson_creator = person_ID;

	IF ( num_requests = 0 ) THEN
		DELETE FROM tbaddress  WHERE idperson = person_ID ;
		DELETE FROM tbnaturalperson  WHERE idperson = person_ID ;
		DELETE FROM tbjuridicalperson  WHERE idperson = person_ID ;
		DELETE FROM hdk_tbdepartment_has_person  WHERE idperson = person_ID ;
		DELETE FROM hdk_tbconfig_user  WHERE idperson = person_ID ;
		DELETE FROM tbperson  WHERE idperson = person_ID ;
		SET msg = "User deleted !!!" ;
	ELSE
		SET msg = "User have requests - Not deleted !!!" ;
	END IF;
    END$$

DELIMITER ;
