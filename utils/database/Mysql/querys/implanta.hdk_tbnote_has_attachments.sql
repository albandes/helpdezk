DROP TABLE IF EXISTS hdk_tbnote_attachments;
CREATE TABLE hdk_tbnote_attachments (
	idnote_attachments INT(10) NOT NULL AUTO_INCREMENT ,
	filename VARCHAR(255) DEFAULT ' ',
	PRIMARY KEY (idnote_attachments)
) ENGINE=INNODB;

-- para resolver o problema dos campos unsigned --
ALTER TABLE hdk_tbrequest_repassed
  DROP FOREIGN KEY FK_hdk_tbrequest_repassed2

ALTER TABLE hdk_tbnote MODIFY idnote INT(10) NOT NULL AUTO_INCREMENT;

ALTER TABLE hdk_tbrequest_repassed MODIFY idnote INT(10) NOT NULL ;

ALTER TABLE hdk_tbrequest_repassed
ADD CONSTRAINT idnote_fk_idnote
FOREIGN KEY(idnote)
REFERENCES hdk_tbnote(idnote)
ON DELETE CASCADE;
-- ----------------------------------------------

DROP TABLE IF EXISTS hdk_tbnote_has_attachments;
CREATE TABLE hdk_tbnote_has_attachments(
  idnote INT(10) NOT NULL,
  idnote_attachments INT(10) NOT NULL,
  KEY (idnote),
  KEY (idnote_attachments),
  FOREIGN KEY (idnote) REFERENCES hdk_tbnote(idnote),
  FOREIGN KEY (idnote_attachments) REFERENCES hdk_tbnote_attachments(idnote_attachments)
) ENGINE=INNODB;

INSERT INTO hdk_tbnote_attachments (filename) SELECT file_name FROM hdk_tbnote_attachment;

INSERT INTO hdk_tbnote_has_attachments (idnote,idnote_attachments) SELECT idnote,idnote_attachment  FROM hdk_tbnote WHERE idnote_attachment IS NOT NULL

-- End --

-- After testing --
ALTER TABLE hdk_tbnote
  DROP FOREIGN KEY fk_idnote_attachment

ALTER TABLE hdk_tbnote DROP COLUMN idnote_attachment;

DROP TABLE hdk_tbnote_attachment




