DELIMITER $$

DROP PROCEDURE IF EXISTS `hdk_insUpdNaturalPerson`$$

CREATE PROCEDURE `hdk_insUpdNaturalPerson`(IN personID INT,
				      IN nameIN VARCHAR(80),
				      IN ssn_cpfIN VARCHAR(15),
				      IN rgIN VARCHAR(20),
				      IN rgoexpIN VARCHAR(15),
				      IN dtbirthIN DATE,
				      IN motherIN VARCHAR(80),
				      IN fatherIN VARCHAR(80),
				      IN genderIN VARCHAR(1))
BEGIN
		
		DECLARE naturalpersonIDTmp INT;
		
		IF EXISTS (SELECT idnaturalperson FROM tbnaturalperson WHERE idperson = personID) THEN
			SELECT idnaturalperson INTO naturalpersonIDTmp FROM tbnaturalperson WHERE idperson = personID;
			
			UPDATE tbnaturalperson 
			   SET `name` = nameIN,
			       ssn_cpf = ssn_cpfIN,
			       rg = rgIN,
			       rgoexp = rgoexpIN,
			       dtbirth = dtbirthIN,
			       mother = motherIN,
			       father = fatherIN,
			       gender = genderIN
			 WHERE idnaturalperson = naturalpersonIDTmp;
		ELSE
			INSERT INTO tbnaturalperson (idperson,
						     `name`,
						     ssn_cpf,
						     rg,
						     rgoexp,
						     dtbirth,
						     mother,
						     father,
						     gender)
			VALUES(personID,
			       nameIN,
			       ssn_cpfIN,
			       rgIN,
			       rgoexpIN,
			       dtbirthIN,
			       motherIN,
			       fatherIN,
			       genderIN);
		END IF;
	END$$

DELIMITER ;