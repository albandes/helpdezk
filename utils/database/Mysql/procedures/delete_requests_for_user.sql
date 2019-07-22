/*
 *      Procedure Name  :  delete_requests_for_user
 *      Database/Schema :  helpdesk
 *
 *      Description:
 *          Delete all requests for the especific user
 *
 *      Tables Impacted :
 *          hdk_tbrequest_dates
 *          hdk_tbevaluation_token
 *          hdk_tbrequest_times
 *          hdk_tbrequest_log
 *          hdk_tbrequest_repassed
 *          hdk_tbrequest_attachment
 *          hdk_tbrequest
 *
 *      Params:
 *          IN:  person_ID - user ID
 *
 *      Revision History:
 *
 *         Date:          Id:         Comment:
 *         2016/02/18     albandes    Original
 *
 */

DELIMITER $$


DROP PROCEDURE IF EXISTS  `delete_requests_for_user`$$

CREATE PROCEDURE `delete_requests_for_user`(IN person_ID INT)

delete_request:BEGIN

	  -- Declare variables to read in each record from the cursor
	  DECLARE code_ID VARCHAR(20);

	  -- Declare variables used just for cursor and loop control
	  DECLARE no_more_rows BOOLEAN;
	  DECLARE loop_cntr INT DEFAULT 0;
	  DECLARE num_rows INT DEFAULT 0;

	  -- Declare the cursor
	  DECLARE requests_cur CURSOR FOR
	  SELECT
	     code_request
	  FROM
	     hdk_tbrequest
	  WHERE idperson_creator = person_ID ;

	  -- Declare 'handlers' for exceptions
	  DECLARE CONTINUE HANDLER FOR NOT FOUND
	  SET no_more_rows = TRUE;

	  -- 'open' the cursor and capture the number of rows returned
	  -- (the 'select' gets invoked when the cursor is 'opened')
	  OPEN requests_cur;
	  SELECT FOUND_ROWS() INTO num_rows;

	  the_loop: LOOP
		FETCH requests_cur INTO code_ID;
		IF no_more_rows THEN
			CLOSE requests_cur;
			LEAVE the_loop;
		END IF;

		-- the equivalent of a 'print statement' in a stored procedure
		-- it simply displays output for each loop
		-- select code_ID;
		CALL delete_request(code_ID,@msg);
		-- count the number of times looped
		SET loop_cntr = loop_cntr + 1;
	  END LOOP the_loop;
	  -- 'print' the output so we can see they are the same
	  SELECT loop_cntr AS messsage;

END$$
DELIMITER ;



