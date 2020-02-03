DELIMITER $$


DROP PROCEDURE IF EXISTS `hdk_updateCity`$$

CREATE PROCEDURE `hdk_updateCity`(IN stateId INT,
                                     IN nameCity VARCHAR(60),
                                     OUT insertId INT)
BEGIN
		SET @id = (SELECT tbcity.idcity FROM tbcity WHERE tbcity.name = nameCity AND tbcity.idstate = stateId);
		  IF @id IS NULL THEN
		    
		    INSERT INTO tbcity (idstate,`name`) VALUES (stateId, nameCity);
		    SET insertId := LAST_INSERT_ID();
		    
		  ELSE
		    
		    SET insertId := @id;
		 END IF;
	END$$

DELIMITER ;