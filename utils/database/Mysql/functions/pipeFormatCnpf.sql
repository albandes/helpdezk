DELIMITER ;;
CREATE FUNCTION `pipeFormatCnpj`(cnpj VARCHAR(20)) RETURNS VARCHAR(18) CHARSET utf8
  BEGIN
    DECLARE l_ret VARCHAR(18);

    SET cnpj = REPLACE(cnpj,'.','');
    SET cnpj = REPLACE(cnpj,'-','');
    SET cnpj = REPLACE(cnpj,' ','');
    SET cnpj = TRIM(cnpj);

    SET l_ret = cnpj;

    SET l_ret = CONCAT(MID(l_ret,1,2),'.',MID(l_ret,3,3),'.',MID(l_ret,6,3),'/',MID(l_ret,9,4),'-',MID(l_ret,13,2));

    RETURN l_ret;
  END ;;