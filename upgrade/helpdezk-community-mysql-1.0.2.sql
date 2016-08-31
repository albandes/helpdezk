DROP TABLE IF EXISTS `hdk_tbrequest_dates`;
-- [PIPE]
CREATE TABLE `hdk_tbrequest_dates` (                                                                                                                    
                       `idrequestdate` int(11) NOT NULL AUTO_INCREMENT,
                       `code_request` varchar(20) NOT NULL,                                                                                                                  
                       `forwarded_date` datetime DEFAULT NULL,                                                                                                               
                       `approval_date` datetime DEFAULT NULL,                                                                                                                
                       `finish_date` datetime DEFAULT NULL,                                                                                                                  
                       `rejection_date` datetime DEFAULT NULL,                                                                                                               
                       `assume_date` datetime DEFAULT NULL,  
                       PRIMARY KEY (`idrequestdate`),                       
                       CONSTRAINT `FK_hdk_tbrequest_dates` FOREIGN KEY (`code_request`) REFERENCES `hdk_tbrequest` (`code_request`)
                     ) ENGINE=InnoDB;
-- [PIPE]
insert into `hdk_tbconfig` (`idconfig`, `name`, `description`, `idconfigcategory`, `session_name`, `field_type`, `status`, `smarty`, `value`) values ('104', 'Email Port', NULL, '5', 'EM_PORT', NULL, 'A', 'em_port', '');
-- [PIPE]
 CREATE TABLE `hdk_base_category` (                         
			     `idcategory` int(11) NOT NULL AUTO_INCREMENT,            
			     `name` varchar(100) NOT NULL,                            
			     `idcategory_reference` int(11) DEFAULT NULL,             
			     PRIMARY KEY (`idcategory`)                               
			   ) ENGINE=InnoDB;
-- [PIPE]
CREATE TABLE `hdk_base_knowledge` (                                                                                                         
                  `idbase` int(11) NOT NULL AUTO_INCREMENT,                                                                                                 
                  `idcategory` int(11) NOT NULL,                                                                                                            
                  `name` varchar(150) NOT NULL,                                                                                                             
                  `problem` blob NOT NULL,                                                                                                                  
                  `solution` blob NOT NULL,                                                                                                                 
                  `date_register` datetime NOT NULL,                                                                                                                 
                  `idperson` int(11) NOT NULL,                                                                                                              
                  `date_edit` datetime DEFAULT NULL,                                                                                                        
                  `idperson_edit` int(11) DEFAULT NULL,                                                                                                     
                  `faq` int(1) DEFAULT '0',                                                                                                                 
                  PRIMARY KEY (`idbase`),                                                                                                                   
                  CONSTRAINT `fk_idcategory_hdk_base_category` FOREIGN KEY (`idcategory`) REFERENCES `hdk_base_category` (`idcategory`) ON DELETE CASCADE,
                  CONSTRAINT `fk_idperson_edit_tbperson` FOREIGN KEY (`idperson_edit`) REFERENCES `tbperson` (`idperson`),                                  
                  CONSTRAINT `fk_idperson_tbperson` FOREIGN KEY (`idperson`) REFERENCES `tbperson` (`idperson`)                                             
                ) ENGINE=InnoDB;
-- [PIPE]
CREATE TABLE `hdk_base_attachment` (                                                                                   
	               `idattachment` int(11) NOT NULL AUTO_INCREMENT,                                                                      
	               `filename` varchar(200) NOT NULL,                                                                                    
	               `idbase` int(11) NOT NULL,                                                                                           
	               `real_filename` varchar(200) NOT NULL,                                                                               
	               PRIMARY KEY (`idattachment`),                                                                                        
	               KEY `fk_idbase_tbperson` (`idbase`),                                                                                 
	               CONSTRAINT `fk_idbase_tbperson` FOREIGN KEY (`idbase`) REFERENCES `hdk_base_knowledge` (`idbase`) ON DELETE CASCADE  
	             ) ENGINE=InnoDB;
-- [PIPE]
ALTER TABLE tblocation ENGINE = InnoDB;
-- [PIPE]
UPDATE tbperson SET cod_location = NULL WHERE cod_location = 0;
-- [PIPE]
ALTER TABLE tbperson ADD CONSTRAINT fk_person_location FOREIGN KEY (cod_location) REFERENCES tblocation(idLocation);
-- [PIPE]
insert into `hdk_tbconfig` (`name`, `description`, `idconfigcategory`, `session_name`, `field_type`, `status`, `smarty`, `value`) values('POP Domain',NULL,'12','POP_DOMAIN',NULL,'A','pop_domain','');