DELIMITER $$

DROP PROCEDURE IF EXISTS `adm_createConfigTables`$$

CREATE PROCEDURE `adm_createConfigTables`(IN prefix VARCHAR(5))
BEGIN
		
		SET @tableCatName = CONCAT(prefix,'_tbconfig_category');
		SET @tableConfigName = CONCAT(prefix,'_tbconfig');
		
		
		SET @drop1 = CONCAT('DROP TABLE IF EXISTS ', @tableCatName);
		
		PREPARE stmt FROM @drop1;
		EXECUTE stmt;
		DEALLOCATE PREPARE stmt;
		
		SET @qry1 = CONCAT('CREATE TABLE ',
					@tableCatName,
				   '(idconfigcategory INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
					`name` VARCHAR(250) DEFAULT NULL,
					smarty VARCHAR(250) DEFAULT NULL,
					PRIMARY KEY (idconfigcategory),
					INDEX COD_CATEGORIA (idconfigcategory))'
		
		);
		
		PREPARE stmt1 FROM @qry1;
		EXECUTE stmt1;
		DEALLOCATE PREPARE stmt1;
		
		SET @drop2 = CONCAT('DROP TABLE IF EXISTS ', @tableConfigName);
		
		PREPARE stmt2 FROM @drop2;
		EXECUTE stmt2;
		DEALLOCATE PREPARE stmt2;
		
		
		SET @qry2 = CONCAT("CREATE TABLE ",
					@tableConfigName,
				   "(idconfig INT(3) UNSIGNED NOT NULL AUTO_INCREMENT,
				    `name` VARCHAR(250) DEFAULT NULL,
				    description BLOB DEFAULT NULL,
				    idconfigcategory INT(10) UNSIGNED DEFAULT NULL,
				    session_name VARCHAR(50) DEFAULT NULL,
				    field_type VARCHAR(200) DEFAULT NULL,
				    `status` CHAR(1) DEFAULT 'A',
				    smarty VARCHAR(120) NOT NULL,
				    `value` VARCHAR(200) DEFAULT NULL,
				    PRIMARY KEY (idconfig),
				    UNIQUE INDEX ",@tableConfigName,"_session_idx (session_name),
				    CONSTRAINT FK_",@tableConfigName," FOREIGN KEY (idconfigcategory)
				    REFERENCES ",@tableCatName," (idconfigcategory) ON DELETE RESTRICT ON UPDATE RESTRICT
					)"
		
		);
		
		PREPARE stmt3 FROM @qry2;
		EXECUTE stmt3;
		DEALLOCATE PREPARE stmt3;
		
          
	END$$

DELIMITER ;