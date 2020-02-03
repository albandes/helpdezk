DELIMITER $$

DROP PROCEDURE IF EXISTS `hdk_deleteNatureperson`$$

CREATE PROCEDURE `hdk_deleteNatureperson`(IN personID INT, IN flag INT)
BEGIN
	
	   IF (flag = 1) THEN
		DELETE FROM tbjuridicalperson WHERE idperson = personID;
	   ELSE
		DELETE FROM tbnaturalperson WHERE idperson = personID;
	   END IF;
	END$$

DELIMITER ;