DELIMITER $$

DROP PROCEDURE IF EXISTS `hdk_deleteNote`$$

CREATE PROCEDURE `hdk_deleteNote`(IN note_ID INT)

hdk_deleteNote:BEGIN
	  
	  DECLARE attach_ID VARCHAR(20);
	  
	  DECLARE no_more_rows BOOLEAN;
	  DECLARE loop_cntr INT DEFAULT 0;
	  DECLARE num_rows INT DEFAULT 0;
	  
	  DECLARE note_cur CURSOR FOR
    SELECT 
      idnote_attachments 
    FROM
      hdk_tbnote_has_attachments 
    WHERE idnote = note_ID ;
	  
	  DECLARE CONTINUE HANDLER FOR NOT FOUND
	  SET no_more_rows = TRUE;
	  
	  
	  OPEN note_cur;
	  SELECT FOUND_ROWS() INTO num_rows;
	  the_loop: LOOP
		FETCH note_cur INTO attach_ID;
		IF no_more_rows THEN
			CLOSE note_cur;
			LEAVE the_loop;
		END IF;
		
		
		
    DELETE FROM hdk_tbnote_has_attachments WHERE idnote_attachments = attach_ID;
    DELETE FROM hdk_tbnote_attachments WHERE idnote_attachments = attach_ID;
    
		
		
		SET loop_cntr = loop_cntr + 1;
	  END LOOP the_loop;
	  
	  #SELECT loop_cntr AS messsage;
    
    DELETE FROM hdk_tbnote WHERE idnote =  note_ID ;
END$$

DELIMITER ;