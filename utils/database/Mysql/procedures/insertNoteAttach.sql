/*
 *      Procedure Name  :  insertNoteAttachments
 *      Database/Schema :  helpdesk
 *
 *      Description:
 *          insert a note attachment and a relation in hdk_tbnote_has_attach
 *
 *      Tables Impacted :
 *          hdk_tbnote_attach
 *          hdk_tbnote_has_attach

 *      Params:
 *          IN:  note_ID      - Note´s ID
 *          IN:  filename     - Attachment file name
 *          OUT: noteatt_ID   - Attachment ID
 *
 *      Revision History:
 *
 *         Date:          Id:         Comment:
 *         2017/12/05     albandes    Original
 *
 */
DELIMITER $$

DROP PROCEDURE IF EXISTS `insertNoteAttachments`$$

CREATE PROCEDURE `insertNoteAttachments`(
  IN note_ID INT,
  IN file_NAME VARCHAR(255),
  OUT noteatt_ID INT
)
BEGIN
 START TRANSACTION;
   INSERT INTO hdk_tbnote_attachments (filename) VALUES  (file_NAME) ;
   set noteatt_ID := LAST_INSERT_ID();
   INSERT INTO hdk_tbnote_has_attachments (idnote,idnote_attachments)	VALUES (note_ID,noteatt_ID);
 COMMIT;
END$$

DELIMITER ;