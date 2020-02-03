
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS bbd_tbmessage ;

##
## TABELA: bbd_tbmessage
##

-- Table: TESTE
CREATE TABLE IF NOT EXISTS `bbd_tbmessage` (
  `idmessage` int(11) NOT NULL AUTO_INCREMENT,
  `idtopic` int(11) NOT NULL,
  `idperson` int(11) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `description` text,
  `dtcreate` datetime DEFAULT NULL,
  `dtstart` datetime DEFAULT NULL,
  `dtend` datetime DEFAULT NULL,
  `sendemail` varchar(1) NOT NULL,
  `showin` varchar(1) NOT NULL,
  `emailsent` int(1) DEFAULT '0',
  PRIMARY KEY (`idmessage`),
  KEY `fk_bbd_tbmessage_bbd_topic1` (`idtopic`),
  KEY `fk_bbd_tbmessage_tbperson1` (`idperson`),
  CONSTRAINT `fk_bbd_tbmessage_bbd_topic1` FOREIGN KEY (`idtopic`) REFERENCES `bbd_topic` (`idtopic`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_bbd_tbmessage_tbperson1` FOREIGN KEY (`idperson`) REFERENCES `tbperson` (`idperson`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ;

# Dados para a tabela: bbd_tbmessage

DROP TABLE IF EXISTS bbd_tbread ;

CREATE TABLE IF NOT EXISTS `bbd_tbread` (
  `idread` int(11) NOT NULL AUTO_INCREMENT,
  `dtread` datetime NOT NULL,
  `idperson` int(11) NOT NULL,
  `idmessage` int(11) NOT NULL,
  PRIMARY KEY (`idread`),
  KEY `fk_bbd_tbread_tbperson1` (`idperson`),
  CONSTRAINT `fk_bbd_tbread_tbperson1` FOREIGN KEY (`idperson`) REFERENCES `tbperson` (`idperson`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ;

DROP TABLE IF EXISTS bbd_topic ;

CREATE TABLE IF NOT EXISTS `bbd_topic` (
  `idtopic` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `default_display` varchar(10) DEFAULT NULL,
  `fl_emailsent` varchar(1) DEFAULT 'N',
  PRIMARY KEY (`idtopic`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ;

DROP TABLE IF EXISTS tmp_blobs ;

CREATE TABLE IF NOT EXISTS `tmp_blobs` (
  `idblobs` int(11) NOT NULL AUTO_INCREMENT,
  `tabela` varchar(80) DEFAULT NULL,
  `campo` varchar(80) DEFAULT NULL,
  PRIMARY KEY (`idblobs`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1 ;


INSERT IGNORE INTO tmp_blobs VALUES ('1','dsh_tbwidgetusuario','widgets');

INSERT IGNORE INTO tmp_blobs VALUES ('2','hdk_tbconfig','description');

INSERT IGNORE INTO tmp_blobs VALUES ('3','hdk_tbdownload','description');

INSERT IGNORE INTO tmp_blobs VALUES ('4','hdk_tbdownload','instruction');

INSERT IGNORE INTO tmp_blobs VALUES ('5','hdk_tbnote','description');

INSERT IGNORE INTO tmp_blobs VALUES ('6','hdk_tbproperty','observations');

INSERT IGNORE INTO tmp_blobs VALUES ('7','hdk_tbrequest','description');

INSERT IGNORE INTO tmp_blobs VALUES ('8','hdk_tbrequest_change_expire','reason');

INSERT IGNORE INTO tmp_blobs VALUES ('9','hdk_tbtemplate_email','description');

INSERT IGNORE INTO tmp_blobs VALUES ('10','tbjuridicalperson','observation');