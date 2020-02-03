
-- Script date 30/12/2019 09:50:54
-- Server version: 5.5.5-10.4.10-MariaDB


CREATE TABLE hdk_tbexternallapp (
  idexternalapp INT(11) NOT NULL AUTO_INCREMENT,
  appname VARCHAR(255) DEFAULT 'NULL',
  url VARCHAR(255) DEFAULT 'NULL',
  PRIMARY KEY (idexternalapp)
)
ENGINE = INNODB
AUTO_INCREMENT = 51
AVG_ROW_LENGTH = 16384
CHARACTER SET latin1
COLLATE latin1_swedish_ci
ROW_FORMAT = DYNAMIC;

CREATE TABLE hdk_tbexternalsettings (
  idexternalsetting INT(11) NOT NULL AUTO_INCREMENT,
  idexternalapp INT(11) DEFAULT NULL,
  idperson INT(11) DEFAULT NULL,
  PRIMARY KEY (idexternalsetting),
  UNIQUE INDEX UK_hdk_tbexternalsettings (idexternalapp, idperson),
  CONSTRAINT FK_hdk_tbexternalsettings_hdk_tbexternallapp_idexternalapp FOREIGN KEY (idexternalapp)
  REFERENCES hdk_tbexternallapp (idexternalapp) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT FK_hdk_tbexternalsettings_tbperson_idperson FOREIGN KEY (idperson)
  REFERENCES tbperson (idperson) ON DELETE RESTRICT ON UPDATE RESTRICT
)
ENGINE = INNODB
AUTO_INCREMENT = 10
AVG_ROW_LENGTH = 16384
CHARACTER SET latin1
COLLATE latin1_swedish_ci
ROW_FORMAT = DYNAMIC;



CREATE TABLE hdk_tbexternalfield (
  idexternalfield INT(11) NOT NULL AUTO_INCREMENT,
  idexternalsettings INT(11) NOT NULL,
  fieldname VARCHAR(255) DEFAULT 'NULL',
  VALUE VARCHAR(255) DEFAULT 'NULL',
  PRIMARY KEY (idexternalfield, idexternalsettings),
  INDEX IDX_hdk_tbexternalfield_fieldname (fieldname),
  CONSTRAINT FK_hdk_tbexternalfield_hdk_tbexternalsettings_idexternallsetting FOREIGN KEY (idexternalsettings)
  REFERENCES hdk_tbexternalsettings (idexternalsetting) ON DELETE RESTRICT ON UPDATE RESTRICT
)
ENGINE = INNODB
AUTO_INCREMENT = 10
AVG_ROW_LENGTH = 8192
CHARACTER SET latin1
COLLATE latin1_swedish_ci
ROW_FORMAT = DYNAMIC;



INSERT INTO
  hdk_tbexternallapp (idexternalapp, appname, url)
VALUES
  (50, 'Trello', 'https://api.trello.com') ;

