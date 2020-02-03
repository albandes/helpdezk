DELIMITER $$


DROP PROCEDURE IF EXISTS `hdk_insertPersonPermission`$$

CREATE PROCEDURE `hdk_insertPersonPermission`(IN program_ID INT, IN person_ID INT, IN accesstype_ID INT, perm CHAR)
BEGIN
IF EXISTS (SELECT idpermission FROM tbpermission WHERE idprogram = program_ID AND idperson = person_ID AND idaccesstype = accesstype_ID) THEN
        UPDATE
          tbpermission
        SET
          allow = perm
        WHERE idprogram    = program_ID
          AND idperson = person_ID
          AND idaccesstype = accesstype_ID ;
ELSE
        INSERT INTO tbpermission (idaccesstype, idprogram, idperson, allow)
        VALUES
          (
            accesstype_ID,
            program_ID,
            person_ID,
            perm
          ) ;
END IF;
END$$

DELIMITER ;