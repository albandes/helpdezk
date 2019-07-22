DELIMITER $$

DROP PROCEDURE IF EXISTS `spm_deleteAtleta`$$

CREATE PROCEDURE `spm_deleteAtleta`(IN person_ID INT, OUT msg VARCHAR(100))

delete_user:BEGIN

	
	DECLARE num_user INT;

	SELECT COUNT(*) INTO num_user FROM tbperson WHERE idperson = person_ID ;

	IF ( num_user = 0 ) THEN
		SET msg = "User not exists !!!" ;
		LEAVE delete_user ;
  ELSE
    DELETE FROM spm_person_condicao WHERE idperson = person_ID ;
    DELETE FROM spm_person_departamento WHERE idperson = person_ID ;
    DELETE FROM spm_person_posicao WHERE idperson = person_ID ;
    DELETE FROM spm_tbapelido WHERE idperson = person_ID ;
    DELETE FROM tbaddress WHERE idperson = person_ID ;
    DELETE FROM tbnaturalperson WHERE idperson = person_ID ;
    DELETE FROM tbperson WHERE idperson = person_ID ;
    SET msg = "Atleta deletado !!!" ;
	END IF;


END$$

DELIMITER ;
