DELIMITER ;;
CREATE FUNCTION `pipeFormatDate`(timezone VARCHAR(255), val VARCHAR(255)) RETURNS VARCHAR(100) CHARSET latin1
BEGIN
	    DECLARE RET VARCHAR(100);
	    IF (timezone = 'pt_BR' ) THEN
		SET RET = ( SELECT DATE_FORMAT(val, '%d/%m/%Y') );
	    ELSEIF (timezone = 'en_US' ) THEN
		SET RET = ( SELECT DATE_FORMAT(val, '%m/%d/%Y') );
	    END IF;
	    RETURN RET;
	END ;;