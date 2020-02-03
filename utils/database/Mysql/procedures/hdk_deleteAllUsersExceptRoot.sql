DELIMITER $$

DROP PROCEDURE IF EXISTS `hdk_deleteAllUsersExceptRoot`$$

CREATE PROCEDURE `hdk_deleteAllUsersExceptRoot`()

    BEGIN
	DELETE FROM hdk_tbgroup_has_service WHERE idgroup IN ( SELECT idgroup FROM hdk_tbgroup WHERE idperson > 1) ;
	-- DELETE FROM prj_tbprojects WHERE group_project IN ( SELECT idgroup FROM hdk_tbgroup WHERE idperson > 1);
	DELETE FROM hdk_tbgroup WHERE idperson > 1 ;
	DELETE FROM tbaddress WHERE idperson > 1 ;
	DELETE FROM tbjuridicalperson WHERE idperson > 1 ;
	DELETE FROM tbnaturalperson WHERE idperson > 1 ;
	DELETE FROM hdk_tbdepartment_has_person WHERE idperson > 1 ;
	DELETE FROM tbpermission WHERE idperson > 1 ;
	DELETE FROM hdk_tbrequest_log WHERE cod_request IN ( SELECT code_request FROM hdk_tbrequest WHERE idperson_creator > 1 ) ;
	DELETE FROM hdk_tbrequest_log  WHERE idperson > 1 ;
	DELETE FROM hdk_tbrequest_times WHERE code_request IN ( SELECT code_request FROM hdk_tbrequest WHERE idperson_creator > 1 ) ;
	DELETE FROM hdk_tbrequest_attachment WHERE code_request IN ( SELECT code_request FROM hdk_tbrequest WHERE idperson_creator > 1 ) ;
	DELETE FROM hdk_tbrequest_repassed WHERE code_request IN ( SELECT code_request FROM hdk_tbrequest WHERE idperson_creator > 1 ) ;
	DELETE FROM hdk_tbrequest_evaluation WHERE code_request IN ( SELECT code_request FROM hdk_tbrequest WHERE idperson_creator > 1 ) ;
	DELETE FROM hdk_tbrequest WHERE idperson_creator > 1 ;
	DELETE FROM hdk_tbcostcenter WHERE idperson > 1 ;
	DELETE FROM hdk_tbconfig_user WHERE idperson > 1 ;
	DELETE FROM hdk_tbexecutionorder_person WHERE idperson > 1 ;
	-- DELETE FROM hdk_base_knowledge WHERE idperson > 1 ;
	-- DELETE FROM hdk_base_knowledge WHERE idperson_edit > 1;
	DELETE FROM bbd_tbread WHERE idperson > 1;
	DELETE FROM tbpersontypes WHERE idperson > 1;
	DELETE FROM bbd_tbread WHERE idperson > 1;
	DELETE FROM dsh_tbstatoperator WHERE idperson > 1;
	DELETE FROM tbperson WHERE idperson > 1 ;
    END$$

DELIMITER ;


