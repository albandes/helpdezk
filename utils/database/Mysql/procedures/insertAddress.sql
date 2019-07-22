DELIMITER $$

DROP PROCEDURE IF EXISTS `insertAddress`$$


CREATE PROCEDURE insertAddress (IN idPerson int,
                                IN idCity int,
                                IN idNeighborhood int, 
                                IN idTypeaddress int,
                                IN number char(9),
                                IN complement varchar(45),
                                IN zipcode varchar(11),
                                IN idTypeStreet int,
                                IN nameStreet varchar(100),
                                OUT insertId int)

BEGIN

  CALL updateStreet(idTypeStreet,nameStreet,@output);
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