delimiter $$

CREATE PROCEDURE `insertPersonPermission`(IN program_ID INT, IN typeperson_ID INT, IN accesstype_ID INT, perm CHAR)
BEGIN
IF EXISTS (SELECT idpermissiongroup FROM tbtypepersonpermission WHERE idprogram = program_ID AND idtypeperson = typeperson_ID and idaccesstype = accesstype_ID) THEN
        UPDATE
          tbtypepersonpermission
        SET
          allow = perm
        WHERE idprogram    = program_ID
          AND idtypeperson = typeperson_ID
          and idaccesstype = accesstype_ID ;
ELSE
        INSERT INTO tbtypepersonpermission (idprogram, idtypeperson, idaccesstype,allow)
        VALUES
          (
            program_ID,
            typeperson_ID,
            accesstype_ID,
            perm
          ) ;
END IF;
END$$