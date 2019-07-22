delimiter $$

CREATE FUNCTION pipeTableExists(_table_name VARCHAR(45))
RETURNS BOOLEAN
DETERMINISTIC READS SQL DATA
BEGIN
    DECLARE _exists  TINYINT(1) DEFAULT 0;

    SELECT COUNT(*) INTO _exists
    FROM information_schema.tables 
    WHERE table_schema =  DATABASE()
    AND table_name =  _table_name;

    RETURN _exists;

END$$
