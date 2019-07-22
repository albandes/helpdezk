DELIMITER $$

DROP PROCEDURE IF EXISTS `hdk_updateAddress`$$

CREATE PROCEDURE `hdk_updateAddress`(IN id_person INT,
                                IN id_city INT,
                                IN id_neighborhood INT, 
                                IN number CHAR(9),
                                IN complement VARCHAR(45),
                                IN zipcode VARCHAR(11),
                                IN id_typeStreet INT,
                                IN name_street VARCHAR(100))
BEGIN
  CALL hdk_updateStreet(id_typeStreet,name_street,@output);
  SET @id_street = (SELECT @output AS idperson);
  UPDATE tbaddress 
    SET idcity = id_city               -- idcity - INT(11) NOT NULL
    ,idneighborhood = id_neighborhood  -- idneighborhood - INT(11) NOT NULL
    ,idstreet = @id_street             -- idstreet - INT(11) NOT NULL
    ,number = number                  -- number - VARCHAR(9)
    ,complement = complement          -- complement - VARCHAR(45) 
    ,zipcode = zipcode                -- zipcode - VARCHAR(11)
    WHERE idperson = id_person ;
END$$

DELIMITER ;