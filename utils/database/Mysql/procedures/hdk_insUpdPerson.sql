DELIMITER $$

DROP PROCEDURE IF EXISTS `hdk_insUpdPerson`$$

CREATE PROCEDURE `hdk_insUpdPerson`(
					IN idtypeloginIN INT,
					IN idtypepersonIN INT,
					IN idnaturepersonIN INT,
					IN idthemeIN INT, 
					IN person_name VARCHAR(60),
					IN loginIN VARCHAR(45),
					IN passwordIN VARCHAR(45),				
					IN emailIN VARCHAR(45),
					IN dtcreateIN DATETIME,				
					IN statusIN CHAR(1),
					IN user_vipIN CHAR(1),
					IN telephoneIN VARCHAR(30),
					IN cellphoneIN VARCHAR(30),
					IN branch_numberIN VARCHAR(10),				
					IN time_valueIN FLOAT,
					IN overtimeIN FLOAT,
					IN idcityIN INT,			
					IN idneighborhoodIN INT,
					IN idstreetIN INT,
					IN idtypestreetIN INT,              
					IN numberIN VARCHAR(9),
					IN complementIN VARCHAR(45),
					IN zipcodeIN VARCHAR(11),
					IN ssn_cpfIN VARCHAR(15),
					IN dtbirthIN DATE,
					IN genderIN VARCHAR(1),
					IN iddepartmentIN INT
				     )
BEGIN
		DECLARE person_IDTmp INT;
		DECLARE naturalperson_IDTmp INT;
		DECLARE address_IDTmp INT;
		DECLARE idtmp INT;
		
		IF EXISTS (SELECT idperson FROM tbperson WHERE login = login) THEN
			SELECT idperson INTO person_IDTmp FROM tbperson WHERE login = loginIN;
			
			UPDATE 	tbperson
			   SET 	idtypelogin = idtypeloginIN,
				idtypeperson = idtypepersonIN,
				idnatureperson = idnatureIN,
				idtheme = idthemeIN,
				`name` = person_name,
				login = loginIN,
				`password` = MD5(passwordIN),
				email = emailIN,
				dtcreate = dtcreateIN,
				`status` = statusIN,
				user_vip = user_vipIN,
				phone_number = telephoneIN,
				cel_phone = cellphoneIN,
				branch_number = branch_numberIN,
				time_value = time_valueIN,
				overtime = overtimeIN
			 WHERE	idperson = person_IDTmp;
			 
			 IF EXISTS (SELECT idnaturalperson FROM tbnaturalperson WHERE idperson = person_IDTmp) THEN
				SELECT idnaturalperson INTO naturalperson_IDTmp FROM tbnaturalperson WHERE idperson = person_IDTmp;
				
				UPDATE 	tbnaturalperson
				   SET 	ssn_cpf = ssn_cpfIN,
					dtbirth = dtbirthIN,
					gender = genderIN
				 WHERE	idnaturalperson = naturalperson_IDTmp;
			 ELSE
				INSERT INTO tbnaturalperson (idperson,ssn_cpf,dtbirth,gender)
				     VALUES (person_IDTmp,ssn_cpfIN,dtbirthIN,genderIN);
			 END IF;
			 
			 IF EXISTS (SELECT idaddress FROM tbaddress WHERE idperson = person_IDTmp) THEN
				SELECT idaddress INTO address_IDTmp FROM tbaddress WHERE idperson = person_IDTmp;
				
				UPDATE 	tbaddress
				   SET 	idcity = idcityIN,
					idneighborhood = idneighborhoodIN,
					idstreet = idstreetIN,
					idtypeaddress = idtypestreetIN,
					number = numberIN,
					complement = complementIN,
					zipcode = zipcodeIN
				 WHERE	idaddress = address_IDTmp;
			 ELSE
				INSERT INTO tbaddress (idperson,idcity,idneighborhood,idstreet,idtypeaddress,number,complement,zipcode)
				     VALUES (person_IDTmp,idcityIN,idneighborhoodIN,idstreetIN,idtypestreetIN,numberIN,complementIN,zipcodeIN);
			 END IF;
			 
			 IF EXISTS (SELECT idperson FROM hdk_tbdepartment_has_person WHERE idperson = person_IDTmp) THEN
				
				UPDATE 	hdk_tbdepartment_has_person
				   SET 	iddepartment = iddepartmentIN
				 WHERE	idperson = person_IDTmp;
			 ELSE
				INSERT INTO hdk_tbdepartment_has_person (idperson,iddepartment)
				     VALUES (person_IDTmp,iddepartmentIN);
			 END IF;
		
		ELSE
			INSERT INTO tbperson (idtypelogin,idtypeperson,idnatureperson,idtheme,`name`,login,
					      `password`,email,dtcreate,`status`,user_vip,phone_number,cel_phone,
					      branch_number,time_value,overtime)
			     VALUES (idtypeloginIN,idtypepersonIN,idnatureIN,idthemeIN,person_name,loginIN,
				     MD5(passwordIN),emailIN,dtcreateIN,statusIN,user_vipIN,telephoneIN,cellphoneIN,
				     branch_numberIN,time_valueIN,overtimeIN);
			
			SET idtmp = LAST_INSERT_ID();
			
			INSERT INTO tbnaturalperson (idperson,ssn_cpf,dtbirth,gender)
				     VALUES (idtmp,ssn_cpfIN,dtbirthIN,genderIN);	     
			
			INSERT INTO tbaddress (idperson,idcity,idneighborhood,idstreet,idtypeaddress,number,complement,zipcode)
			     VALUES (idtmp,idcityIN,idneighborhoodIN,idstreetIN,idtypestreetIN,numberIN,complementIN,zipcodeIN);
			     
			INSERT INTO hdk_tbdepartment_has_person (idperson,iddepartment)
				     VALUES (idtmp,iddepartmentIN);
			
		END IF;
	END$$

DELIMITER ;