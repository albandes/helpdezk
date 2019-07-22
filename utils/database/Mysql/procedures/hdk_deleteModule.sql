/*
 *      Procedure Name  :  hdk_deleteModule
 *      Database/Schema :  helpdezk
 *
 *      Description:
 *          Delete a specific module
 *
 *      Tables Impacted :
 *        tbdefaultpermission
 *        tbpermission 
 *        tbtypepersonpermission
 *        tbtypepersonpermission
 *        tbprogram
 *        tbprogramcategory
 *        tbemail
 *		  tbmodule	
 *
 *      Params:
 *         IN:  moduleID - The module OD
 *         OUT: message

 *      Revision History:
 *
 *         Date:          Id:         Comment:
 *         2019/07/22     albandes    Original
 *
 *
 */


DELIMITER $$

DROP PROCEDURE IF EXISTS `hdk_deleteModule`$$

CREATE PROCEDURE `hdk_deleteModule`(IN moduleID INT)
hdk_deleteModule:BEGIN

	DECLARE num_modules INT;

	SELECT COUNT(idmodule) INTO num_modules FROM  tbmodule WHERE idmodule = moduleID;

	IF ( num_modules = 0 ) THEN
		LEAVE hdk_deleteModule ;
	ELSE
		DELETE FROM `tbdefaultpermission` WHERE idprogram IN (SELECT idprogram FROM tbprogram WHERE idprogramcategory IN (SELECT idprogramcategory FROM  tbprogramcategory WHERE idmodule = moduleID));
		DELETE FROM `tbpermission` WHERE idprogram IN (SELECT idprogram FROM tbprogram WHERE idprogramcategory IN (SELECT idprogramcategory FROM  tbprogramcategory WHERE idmodule = moduleID));
		DELETE FROM `tbtypepersonpermission` WHERE idprogram IN (SELECT idprogram FROM tbprogram WHERE idprogramcategory IN (SELECT idprogramcategory FROM  tbprogramcategory WHERE idmodule = moduleID));
		DELETE FROM tbprogram WHERE idprogramcategory IN (SELECT idprogramcategory FROM  tbprogramcategory WHERE idmodule = moduleID);
		DELETE FROM `tbprogramcategory` WHERE idmodule = moduleID;
		DELETE FROM `tbemail` WHERE `idmodule` = moduleID;
		DELETE FROM `tbmodule` WHERE `idmodule` = moduleID;
	END IF;		
END$$

DELIMITER ;
