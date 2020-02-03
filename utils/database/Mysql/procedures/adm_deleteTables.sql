/*
 *      Procedure Name  :  adm_deleTables
 *      Database/Schema :  helpdezk
 *
 *      Description:
 *          Delete tables for specific module
 *
 *      Tables Impacted :
 *        prefix_%
 *
 *      Params:
 *         IN:  dbDelete - Database name
 *	       IN:  prefix   - Module table´s prefix
 *         OUT: message

 *      Revision History:
 *
 *         Date:          Id:         Comment:
 *         2019/08/02     albandes    Original
 *
 *
 */


DELIMITER $$

DROP PROCEDURE IF EXISTS `adm_deleteTables`$$

CREATE PROCEDURE `adm_deleteTables`( IN dbDelete CHAR(200),IN prefix CHAR(3), OUT message CHAR(100))

deleteTables:BEGIN

IF (prefix  = "hdk" OR prefix = "tb") THEN
	SET message := CONCAT("Unable to delete tables with ", prefix, " prefix.");
	LEAVE deleteTables;
END IF ;

SET GROUP_CONCAT_MAX_LEN=10000;
SET @tbls = (
		SELECT GROUP_CONCAT(`TABLE_NAME`) 
		FROM information_schema.TABLES
		WHERE TABLE_SCHEMA = dbDelete
		AND TABLE_NAME LIKE CONCAT(prefix, "_%")
	    );
		    
IF ( @tbls IS NOT NULL ) THEN
	SET FOREIGN_KEY_CHECKS = 0;
	
	SET @delStmt = CONCAT('DROP TABLE ',  @tbls);
	-- SELECT @delStmt;
	PREPARE stmt FROM @delStmt;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	SET FOREIGN_KEY_CHECKS = 1;
	SET message := "Tables  deleted sucessfull.";
ELSE 
	SET message := "Don´t exist tables with this prefix. ";	
END IF;

END$$


