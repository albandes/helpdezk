DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `pipeFormatCpf`(cpf VARCHAR(20)) RETURNS VARCHAR(14) CHARSET utf8
BEGIN
 DECLARE l_ret VARCHAR(14);

 SET cpf = REPLACE(cpf,'.','');
 SET cpf = REPLACE(cpf,'-','');
 SET cpf = REPLACE(cpf,' ','');
 SET cpf = TRIM(cpf);

 SET l_ret = cpf;

 SET l_ret = CONCAT(MID(l_ret,1,3),'.',MID(l_ret,4,3),'.',MID(l_ret,7,3),'-',MID(l_ret,10,2));

 RETURN l_ret;
 END ;;