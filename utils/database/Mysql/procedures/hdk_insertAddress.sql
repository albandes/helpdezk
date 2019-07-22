DELIMITER $$

DROP PROCEDURE IF EXISTS `hdk_insertAddress`$$

CREATE PROCEDURE `hdk_insertAddress`(IN idPerson INT,
                                IN idCity INT,
                                IN idNeighborhood INT, 
                                IN idTypeaddress INT,
                                IN number CHAR(9),
                                IN complement VARCHAR(45),
                                IN zipcode VARCHAR(11),
                                IN idTypeStreet INT,
                                IN nameStreet VARCHAR(100),
                                OUT insertId INT)
BEGIN
  CALL hdk_updateStreet(idTypeStreet,nameStreet,@output);
  SET @idStreet = (SELECT @output AS idperson);
  INSERT INTO tbaddress
  (
    idperson
   ,idcity
   ,idneighborhood
   ,idstreet
   ,idtypeaddress
   ,number
   ,complement
   ,zipcode
  )
  VALUES
  (
    idPerson -- idperson - INT(11) NOT NULL
   ,idCity -- idcity - INT(11) NOT NULL
   ,idNeighborhood -- idneighborhood - INT(11) NOT NULL
   ,@idStreet -- idstreet - INT(11) NOT NULL
   ,idTypeaddress -- idtypeaddress - INT(11) NOT NULL
   ,number -- number - VARCHAR(9)
   ,complement -- complement - VARCHAR(45)
   ,zipcode -- zipcode - VARCHAR(11)
  );
  SET insertId := LAST_INSERT_ID();
END$$

DELIMITER ;