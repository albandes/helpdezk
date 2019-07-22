DELIMITER $$

DROP FUNCTION IF EXISTS `pipeIsAddressOk`$$

CREATE FUNCTION pipeIsAddressOk (id_person int ) RETURNS binary

BEGIN 

  IF (SELECT count(idaddress) FROM tbaddress WHERE  idperson = id_person AND (idcity=1 OR idneighborhood=1 OR idstreet=1)) > 0  THEN
    return FALSE;
  else
    return TRUE;
  end if;

END$$

DELIMITER ;

