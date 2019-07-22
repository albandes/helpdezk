DELIMITER ;;
CREATE FUNCTION `pipeFormatCurrency`(timezone VARCHAR(255), val VARCHAR(255)) RETURNS VARCHAR(100) CHARSET latin1
BEGIN
	    DECLARE RET VARCHAR(100);
	    IF (timezone = 'pt_BR' ) THEN
		SET RET = ( SELECT REPLACE (REPLACE (REPLACE (FORMAT(val, 2), '.', '|'),',','.' ),'|',',' ) );
	    ELSEIF (timezone = 'en_US' ) THEN
		SET RET = ( SELECT FORMAT (val,2) );
	    END IF;
	    RETURN RET;
	END ;;