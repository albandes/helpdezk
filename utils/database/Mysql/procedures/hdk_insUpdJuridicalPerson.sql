DELIMITER $$



DROP PROCEDURE IF EXISTS `hdk_insUpdJuridicalPerson`$$

CREATE PROCEDURE `hdk_insUpdJuridicalPerson`(IN personID INT,
						 IN nameIN VARCHAR(80),
						 IN ein_cnpjIN VARCHAR(18),
						 IN iestadualIN VARCHAR(20),
						 IN contactIN VARCHAR(80),
						 IN observationIN BLOB)
BEGIN
		
		DECLARE juridicalpersonIDTmp INT;
		
		IF EXISTS (SELECT idjuridicalperson FROM tbjuridicalperson WHERE idperson = personID) THEN
			SELECT idjuridicalperson INTO juridicalpersonIDTmp FROM tbjuridicalperson WHERE idperson = personID;
			
			UPDATE tbjuridicalperson 
			   SET `name` = nameIN,
			       ein_cnpj = ein_cnpjIN,
			       iestadual = iestadualIN,
			       contact_person = contactIN,
			       observation = observationIN
			 WHERE idjuridicalperson = juridicalpersonIDTmp;
		ELSE
			INSERT INTO tbjuridicalperson (idperson,
						       `name`,
						       ein_cnpj,
						       iestadual,
						       contact_person,
						       observation)
			VALUES(personID,
			       nameIN,
			       ein_cnpjIN,
			       iestadualIN,
			       contactIN,
			       observationIN);
		END IF;
	END$$

DELIMITER ;