DELIMITER $$

DROP PROCEDURE IF EXISTS `hdk_insertNoteAttachments`$$

CREATE PROCEDURE `hdk_insertNoteAttachments`(
  IN note_ID INT,
  IN file_NAME VARCHAR(255),
  OUT noteatt_ID INT
)
BEGIN
 START TRANSACTION;
   INSERT INTO hdk_tbnote_attachments (filename) VALUES  (file_NAME) ;
   SET noteatt_ID := LAST_INSERT_ID();
   INSERT INTO hdk_tbnote_has_attachments (idnote,idnote_attachments)	VALUES (note_ID,noteatt_ID);
 COMMIT;
END$$

DELIMITER ;