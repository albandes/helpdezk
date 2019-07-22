
DELIMITER $$

DROP PROCEDURE IF EXISTS `updateStreet`$$


CREATE PROCEDURE updateStreet (IN idTypeStreet int,
                               IN nameStreet varchar(100),
                               OUT insertId int)
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