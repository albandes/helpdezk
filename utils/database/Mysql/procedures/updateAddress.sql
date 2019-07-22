DELIMITER $$

DROP PROCEDURE IF EXISTS `updateAddress`$$


CREATE PROCEDURE updateAddress (IN idPerson int,
                                IN idCity int,
                                IN idNeighborhood int, 
                                IN number char(9),
                                IN complement varchar(45),
                                IN zipcode varchar(11),
                                IN idTypeStreet int,
                                IN nameStreet varchar(100))

BEGIN

  CALL updateStreet(idTypeStreet,nameStreet,@output);
  SET @idStreet = (SELECT @output AS idperson);

  UPDATE tbaddress 
    SET idcity = idCity -- idcity - INT(11) NOT NULL
    ,idneighborhood = idNeighborhood -- idneighborhood - INT(11) NOT NULL
    ,idstreet = @idStreet -- idstreet - INT(11) NOT NULL
    ,number = number -- number - VARCHAR(9)
    ,complement = complement -- complement - VARCHAR(45) 
    ,zipcode = zipcode -- zipcode - VARCHAR(11)
    WHERE idperson = idPerson ;


END$$

DELIMITER ;