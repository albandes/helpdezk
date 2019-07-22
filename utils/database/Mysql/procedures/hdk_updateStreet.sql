DELIMITER $$


DROP PROCEDURE IF EXISTS `hdk_updateStreet`$$

CREATE PROCEDURE `hdk_updateStreet`(IN idTypeStreet INT,
                               IN nameStreet VARCHAR(100),
                               OUT insertId INT)
BEGIN
  SET @id = (SELECT tbstreet.idstreet FROM tbstreet WHERE tbstreet.idtypestreet = idTypeStreet AND tbstreet.name = nameStreet );
  IF @id IS NULL  THEN
    INSERT INTO tbstreet (`name`, idtypestreet) VALUES (nameStreet, idTypeStreet);
    SET insertId := LAST_INSERT_ID();
  ELSE
    SET insertId := @id;
  END IF;
END$$

DELIMITER ;