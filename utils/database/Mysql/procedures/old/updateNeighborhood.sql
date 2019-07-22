DELIMITER $$

DROP PROCEDURE IF EXISTS `updateNeighborhood`$$


CREATE PROCEDURE updateNeighborhood (IN cityId int,
                                     IN nameNeighborhood varchar(100),
                                     OUT insertId int)
BEGIN

  set @id = (select tbneighborhood.idneighborhood from tbneighborhood where tbneighborhood.name = nameNeighborhood AND tbneighborhood.idcity = cityId);

  IF @id IS NULL THEN
    
    INSERT INTO tbneighborhood (idcity,name) VALUES (cityId, nameNeighborhood);
    SET insertId := LAST_INSERT_ID();
    
  ELSE
    
    SET insertId := @id;

  END IF;

END$$

DELIMITER ;