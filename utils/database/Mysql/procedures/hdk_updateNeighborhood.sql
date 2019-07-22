DELIMITER $$

DROP PROCEDURE IF EXISTS `hdk_updateNeighborhood`$$

CREATE PROCEDURE `hdk_updateNeighborhood`(IN cityId INT,
                                     IN nameNeighborhood VARCHAR(100),
                                     OUT insertId INT)
BEGIN
  SET @id = (SELECT tbneighborhood.idneighborhood FROM tbneighborhood WHERE tbneighborhood.name = nameNeighborhood AND tbneighborhood.idcity = cityId);
  IF @id IS NULL THEN
    
    INSERT INTO tbneighborhood (idcity,NAME) VALUES (cityId, nameNeighborhood);
    SET insertId := LAST_INSERT_ID();
    
  ELSE
    
    SET insertId := @id;
  END IF;
END$$

DELIMITER ;