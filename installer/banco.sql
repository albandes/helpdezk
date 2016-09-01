SET FOREIGN_KEY_CHECKS = 0; 

##
## TABELA: bbd_tbmessage
##
DROP TABLE IF EXISTS bbd_tbmessage ; 

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



##
## TABELA: bbd_tbread
##
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

# Dados para a tabela: bbd_tbread



##
## TABELA: bbd_topic
##
DROP TABLE IF EXISTS bbd_topic ; 

CREATE TABLE IF NOT EXISTS `bbd_topic` (
  `idtopic` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `default_display` varchar(10) DEFAULT NULL,
  `fl_emailsent` varchar(1) DEFAULT 'N',
  PRIMARY KEY (`idtopic`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ; 

# Dados para a tabela: bbd_topic



##
## TABELA: bbd_topic_company
##
DROP TABLE IF EXISTS bbd_topic_company ; 

CREATE TABLE IF NOT EXISTS `bbd_topic_company` (
  `idtopiccompany` int(11) NOT NULL AUTO_INCREMENT,
  `idtopic` int(11) NOT NULL,
  `idcompany` int(11) NOT NULL,
  PRIMARY KEY (`idtopiccompany`),
  KEY `idtopic` (`idtopic`),
  KEY `idcompany` (`idcompany`),
  CONSTRAINT `bbd_topic_company_ibfk_1` FOREIGN KEY (`idtopic`) REFERENCES `bbd_topic` (`idtopic`),
  CONSTRAINT `bbd_topic_company_ibfk_2` FOREIGN KEY (`idcompany`) REFERENCES `tbperson` (`idperson`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ; 

# Dados para a tabela: bbd_topic_company



##
## TABELA: bbd_topic_group
##
DROP TABLE IF EXISTS bbd_topic_group ; 

CREATE TABLE IF NOT EXISTS `bbd_topic_group` (
  `idtopicgroup` int(11) NOT NULL AUTO_INCREMENT,
  `idtopic` int(11) NOT NULL,
  `idgroup` int(11) NOT NULL,
  PRIMARY KEY (`idtopicgroup`),
  KEY `idtopic` (`idtopic`),
  KEY `idgroup` (`idgroup`),
  CONSTRAINT `bbd_topic_group_ibfk_1` FOREIGN KEY (`idtopic`) REFERENCES `bbd_topic` (`idtopic`),
  CONSTRAINT `bbd_topic_group_ibfk_2` FOREIGN KEY (`idgroup`) REFERENCES `hdk_tbgroup` (`idgroup`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ; 

# Dados para a tabela: bbd_topic_group



##
## TABELA: bbd_topiccustomer
##
DROP TABLE IF EXISTS bbd_topiccustomer ; 

CREATE TABLE IF NOT EXISTS `bbd_topiccustomer` (
  `idtopiccustomer` int(11) NOT NULL,
  `idperson` int(11) NOT NULL,
  `idtopic` int(11) NOT NULL,
  PRIMARY KEY (`idtopiccustomer`),
  KEY `fk_bbd_topiccustomer_tbperson1` (`idperson`),
  KEY `fk_bbd_topiccustomer_bbd_topic1` (`idtopic`),
  CONSTRAINT `fk_bbd_topiccustomer_bbd_topic1` FOREIGN KEY (`idtopic`) REFERENCES `bbd_topic` (`idtopic`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_bbd_topiccustomer_tbperson1` FOREIGN KEY (`idperson`) REFERENCES `tbperson` (`idperson`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ; 

# Dados para a tabela: bbd_topiccustomer



##
## TABELA: dsh_tbcategory
##
DROP TABLE IF EXISTS dsh_tbcategory ; 

CREATE TABLE IF NOT EXISTS `dsh_tbcategory` (
  `idcategory` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT NULL,
  `index` int(11) DEFAULT NULL,
  `status` char(1) DEFAULT 'A',
  PRIMARY KEY (`idcategory`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 ; 

# Dados para a tabela: dsh_tbcategory

INSERT IGNORE INTO dsh_tbcategory VALUES ('1','Helpdezk','0','A');



##
## TABELA: dsh_tbcategory_has_widget
##
DROP TABLE IF EXISTS dsh_tbcategory_has_widget ; 

CREATE TABLE IF NOT EXISTS `dsh_tbcategory_has_widget` (
  `idcategory` int(11) DEFAULT NULL,
  `idwidget` int(11) DEFAULT NULL,
  KEY `FK_dsh_tbcategory_has_widget` (`idwidget`),
  KEY `FK1_dsh_tbcategory_has_widget` (`idcategory`),
  CONSTRAINT `FK1_dsh_tbcategory_has_widget` FOREIGN KEY (`idcategory`) REFERENCES `dsh_tbcategory` (`idcategory`) ON DELETE CASCADE,
  CONSTRAINT `FK_dsh_tbcategory_has_widget` FOREIGN KEY (`idwidget`) REFERENCES `dsh_tbwidget` (`idwidget`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ; 

# Dados para a tabela: dsh_tbcategory_has_widget

INSERT IGNORE INTO dsh_tbcategory_has_widget VALUES ('1','1');

INSERT IGNORE INTO dsh_tbcategory_has_widget VALUES ('1','3');

INSERT IGNORE INTO dsh_tbcategory_has_widget VALUES ('1','3');



##
## TABELA: dsh_tbwidget
##
DROP TABLE IF EXISTS dsh_tbwidget ; 

CREATE TABLE IF NOT EXISTS `dsh_tbwidget` (
  `idwidget` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  `dbhost` varchar(45) DEFAULT NULL,
  `dbuser` varchar(45) DEFAULT NULL,
  `dbpass` varchar(45) DEFAULT NULL,
  `dbname` varchar(45) DEFAULT NULL,
  `field1` varchar(200) DEFAULT NULL,
  `field2` varchar(200) DEFAULT NULL,
  `field3` varchar(200) DEFAULT NULL,
  `field4` varchar(200) DEFAULT NULL,
  `field5` varchar(200) DEFAULT NULL,
  `description` varchar(200) DEFAULT NULL,
  `creator` varchar(100) DEFAULT NULL,
  `controller` varchar(100) DEFAULT NULL,
  `image` varchar(100) DEFAULT NULL,
  `index` int(11) DEFAULT NULL,
  `status` char(1) DEFAULT 'A',
  PRIMARY KEY (`idwidget`),
  KEY `idx_name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 ; 

# Dados para a tabela: dsh_tbwidget

INSERT IGNORE INTO dsh_tbwidget VALUES ('1','Hard Users','','','','','2013','6','','','','Most active users','Rogerio Albandes','hdk_harduser','hard user.jpg','0','A');

INSERT IGNORE INTO dsh_tbwidget VALUES ('2','Requests in Stock','','','','','1800000','12','','','','Requests  in stock , in the last 30 days','Rogerio Albandes','hdk_requestassets','Estoque de Solicitacoes.jpg','0','A');

INSERT IGNORE INTO dsh_tbwidget VALUES ('3','Service Level Agreement','','','','','','','','','','Service Level Agreement','Rogerio Albandes','hdk_sla','sla.jpg','0','A');



##
## TABELA: dsh_tbwidgetusuario
##
DROP TABLE IF EXISTS dsh_tbwidgetusuario ; 

CREATE TABLE IF NOT EXISTS `dsh_tbwidgetusuario` (
  `idwidgetusuario` int(11) NOT NULL AUTO_INCREMENT,
  `idusuario` int(10) unsigned NOT NULL,
  `widgets` blob,
  PRIMARY KEY (`idwidgetusuario`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 ; 

# Dados para a tabela: dsh_tbwidgetusuario

INSERT IGNORE INTO dsh_tbwidgetusuario VALUES ('1','1','ZXlKeVpYTjFiSFFpSURwN0lteGhlVzkxZENJNklDSnNZWGx2ZFhReUlpd2dJbVJoZEdFaUlEb2dXM3NpZEdsMGJHVWlJRG9nSWtoaGNtUWdWWE5sY25NaUxDQWlhV1FpSURvZ0lqWTBPVEEwTVRVdU9UZ3pNekkwTURJNUlpd2dJbU52YkhWdGJpSWdPaUFpZFc1a1pXWnBibVZrSWl3aVpXUnBkSFZ5YkNJZ09pQWlkVzVrWldacGJtVmtJaXdpYjNCbGJpSWdPaUIwY25WbExDSjFjbXdpSURvZ0lpOW9aQzlrWVhOb1ltOWhjbVF2YUdSclgyaGhjbVIxYzJWeUwyaHZiV1V2YVdSM2FXUm5aWFF2TVNJc0ltMWxkR0ZrWVhSaElqcDdmWDBzZXlKMGFYUnNaU0lnT2lBaVUyVnlkbWxqWlNCTVpYWmxiQ0JCWjNKbFpXMWxiblFpTENBaWFXUWlJRG9nSWpZME9UQTBNVFl1T1Rnek16STBNREk1SWl3Z0ltTnZiSFZ0YmlJZ09pQWljMlZqYjI1a0lpd2laV1JwZEhWeWJDSWdPaUFpZFc1a1pXWnBibVZrSWl3aWIzQmxiaUlnT2lCMGNuVmxMQ0oxY213aUlEb2dJaTlvWkM5a1lYTm9ZbTloY21RdmFHUnJYM05zWVM5b2IyMWxMMmxrZDJsa1oyVjBMek1pTENKdFpYUmhaR0YwWVNJNmUzMTlYWDE5');



##
## TABELA: hdk_tbaddinfo
##
DROP TABLE IF EXISTS hdk_tbaddinfo ; 

CREATE TABLE IF NOT EXISTS `hdk_tbaddinfo` (
  `idaddinfo` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  PRIMARY KEY (`idaddinfo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ; 

# Dados para a tabela: hdk_tbaddinfo



##
## TABELA: hdk_tbapproval_rule
##
DROP TABLE IF EXISTS hdk_tbapproval_rule ; 

CREATE TABLE IF NOT EXISTS `hdk_tbapproval_rule` (
  `idapproval` int(10) NOT NULL AUTO_INCREMENT,
  `iditem` int(3) DEFAULT NULL,
  `idservice` int(3) DEFAULT NULL,
  `idperson` int(10) DEFAULT NULL,
  `order` int(10) DEFAULT '1',
  `fl_recalculate` int(1) DEFAULT '0',
  PRIMARY KEY (`idapproval`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ; 

# Dados para a tabela: hdk_tbapproval_rule



##
## TABELA: hdk_tbattendance_way
##
DROP TABLE IF EXISTS hdk_tbattendance_way ; 

CREATE TABLE IF NOT EXISTS `hdk_tbattendance_way` (
  `idattendanceway` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `way` varchar(100) NOT NULL,
  PRIMARY KEY (`idattendanceway`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 ; 

# Dados para a tabela: hdk_tbattendance_way

INSERT IGNORE INTO hdk_tbattendance_way VALUES ('1','Internal');

INSERT IGNORE INTO hdk_tbattendance_way VALUES ('2','External');

INSERT IGNORE INTO hdk_tbattendance_way VALUES ('3','Remote Controll');



##
## TABELA: hdk_tbconfig
##
DROP TABLE IF EXISTS hdk_tbconfig ; 

CREATE TABLE IF NOT EXISTS `hdk_tbconfig` (
  `idconfig` int(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) DEFAULT NULL,
  `description` blob,
  `idconfigcategory` int(10) unsigned DEFAULT NULL,
  `session_name` varchar(50) DEFAULT NULL,
  `field_type` varchar(200) DEFAULT NULL,
  `status` char(1) DEFAULT 'A',
  `smarty` varchar(120) NOT NULL,
  `value` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`idconfig`),
  KEY `FK_hdk_tbconfig` (`idconfigcategory`),
  CONSTRAINT `FK_hdk_tbconfig` FOREIGN KEY (`idconfigcategory`) REFERENCES `hdk_tbconfig_category` (`idconfigcategory`)
) ENGINE=InnoDB AUTO_INCREMENT=90 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC ; 

# Dados para a tabela: hdk_tbconfig

INSERT IGNORE INTO hdk_tbconfig VALUES ('1','USER: Notify user when a request is opened','','3','NEW_ASSUMED_MAIL','checkbox','A','Email_request_assumed','1');

INSERT IGNORE INTO hdk_tbconfig VALUES ('2','USER: Notify user when a request is closed','','3','FINISH_MAIL','checkbox','A','Email_request_finished','1');

INSERT IGNORE INTO hdk_tbconfig VALUES ('3','USER: Notiffy user when a request is rejected ','','3','REJECTED_MAIL','checkbox','A','Email_request_rejected','1');

INSERT IGNORE INTO hdk_tbconfig VALUES ('4','OPERATOR: Notify operator when a request is evaluated','','3','EM_EVALUATED','checkbox','A','Email_request_evaluated','1');

INSERT IGNORE INTO hdk_tbconfig VALUES ('13','USER: Send notifications when a new appointment is included','','3','USER_NEW_NOTE_MAIL','checkbox','A','Email_request_apont_user','1');

INSERT IGNORE INTO hdk_tbconfig VALUES ('16','OPERATOR: Notify the attendant responsible when a new request is registered','','3','NEW_REQUEST_OPERATOR_MAIL','checkbox','A','Email_request_record','1');

INSERT IGNORE INTO hdk_tbconfig VALUES ('18','Allow attendants may exclude notes','','1','SES_IND_DELETE_NOTE','checkbox','A','sys_allow_delete_note','1');

INSERT IGNORE INTO hdk_tbconfig VALUES ('20','Qty extensions of the due date(0 = Never, [Blank] = No limits, [Above 0] = Qty of extensions)','','1','SES_QT_PRORROGATION','input','A','sys_prorogation_qt','2');

INSERT IGNORE INTO hdk_tbconfig VALUES ('23','Allow reopening requests','','1','SES_IND_REOPEN','checkbox','A','sys_allow_reopen','1');

INSERT IGNORE INTO hdk_tbconfig VALUES ('33','The operator will need to inform the time spent on tasks','','1','SES_IND_ENTER_TIME','checkbox','A','sys_enter_time','1');

INSERT IGNORE INTO hdk_tbconfig VALUES ('34','Allow operators to take requests forwarded or assumed by another operator.','','1','SES_IND_ASSUME_OTHER','checkbox','A','sys_allow_assume_others','1');

INSERT IGNORE INTO hdk_tbconfig VALUES ('43','OPERATOR: Send Notifications when add a new note','','3','OPERATOR_NEW_NOTE','checkbox','A','Email_request_apont_operator','1');

INSERT IGNORE INTO hdk_tbconfig VALUES ('44','When you open a new request, start the timer','','1','SES_IND_TIMER_OPENING','checkbox','A','sys_start_timer','1');

INSERT IGNORE INTO hdk_tbconfig VALUES ('45','Do not show delivery time to the requester until an attendant has assumed the request','','10','SES_HIDE_PERIOD','checkbox','A','sys_expire_date_user','1');

INSERT IGNORE INTO hdk_tbconfig VALUES ('46','When forward or take requests, bring marked the option:  \"I want my group to continue viewing the request \"','','10','SES_SHARE_VIEW','checkbox','A','sys_show_group_view_checkbox','1');

INSERT IGNORE INTO hdk_tbconfig VALUES ('47','OPERATOR: Send notification when a request is reopened','','3','REQUEST_REOPENED','checkbox','A','Email_request_reopened','1');

INSERT IGNORE INTO hdk_tbconfig VALUES ('48','Enable/disable send emails.','','1','SEND_EMAILS','checkbox','A','sys_email_notification','1');

INSERT IGNORE INTO hdk_tbconfig VALUES ('49','Order requests ASC','','10','SES_ORDER_ASC','checkbox','A','sys_sort_asc','1');

INSERT IGNORE INTO hdk_tbconfig VALUES ('50','Email Host','','5','EM_HOSTNAME','','A','em_hostname','mail.helpdezk.org');

INSERT IGNORE INTO hdk_tbconfig VALUES ('51','Domain','','5','EM_DOMAIN','','A','em_domain','helpdezk.org');

INSERT IGNORE INTO hdk_tbconfig VALUES ('52','Email user','','5','EM_USER','','A','em_user','no-reply@helpdezk.org');

INSERT IGNORE INTO hdk_tbconfig VALUES ('53','User password','','5','EM_PASSWORD','','A','em_password','LambruscO');

INSERT IGNORE INTO hdk_tbconfig VALUES ('54','Sender email','','5','EM_SENDER','','A','em_sender','no-reply@helpdezk.org');

INSERT IGNORE INTO hdk_tbconfig VALUES ('55','Requires authentication','','5','EM_AUTH','','A','em_auth','1');

INSERT IGNORE INTO hdk_tbconfig VALUES ('56','Email Header','UEhBK0NnazhhVzFuSUdGc2REMGlJaUJ6Y21NOUltaDBkSEE2THk5M2QzY3VhR1ZzY0dSbGVtc3ViM0puTDNCMVlteHBZeTlwYldGblpYTXZiRzluYnk1d2JtY2lJQzgrUEM5d1Bnbz0=','11','EM_HEADER','','A','em_header','');

INSERT IGNORE INTO hdk_tbconfig VALUES ('57','Email Footer','','11','EM_FOOTER','','A','em_footer','');

INSERT IGNORE INTO hdk_tbconfig VALUES ('58','POP Host','','12','POP_HOST','','A','pop_host','Teste');

INSERT IGNORE INTO hdk_tbconfig VALUES ('59','POP Port','','12','POP_PORT','','A','pop_port','993');

INSERT IGNORE INTO hdk_tbconfig VALUES ('60','POP Type','','12','POP_TYPE','','A','pop_type','GMAIL');

INSERT IGNORE INTO hdk_tbconfig VALUES ('61','Success Log','','5','EM_SUCCESS_LOG','','A','em_success_log','1');

INSERT IGNORE INTO hdk_tbconfig VALUES ('62','Failure Log','','5','EM_FAILURE_LOG','','A','em_failure_log','1');

INSERT IGNORE INTO hdk_tbconfig VALUES ('63','Need user approval after the closing of the request','','10','SES_APROVE','checkbox','A','ses_aprove','1');

INSERT IGNORE INTO hdk_tbconfig VALUES ('64','After you approve the request, the user must answer the questionnaire on assessment of care','','10','SES_EVALUATE','checkbox','A','ses_evaluate','1');

INSERT IGNORE INTO hdk_tbconfig VALUES ('65','Enable maintenance mode','','5','SES_MAINTENANCE','checkbox','A','ses_maintenance','0');

INSERT IGNORE INTO hdk_tbconfig VALUES ('66','Maintenance message','','5','SES_MAINTENANCE_MSG','','A','ses_maintenance_msg','We are doing maintenance, back shortly!!!');

INSERT IGNORE INTO hdk_tbconfig VALUES ('67','OPERATOR: Notify operators responsible when forward a request','','3','REPASS_REQUEST_OPERATOR_MAIL','checkbox','A','Email_request_repass','1');

INSERT IGNORE INTO hdk_tbconfig VALUES ('68','Para abrir nova solicita??o o usu?rio n?o poder? ter solicita??es para aprova??o','','1','SES_OPEN_NEW_REQUEST','checkbox','A','sys_open_new_request','1');

INSERT IGNORE INTO hdk_tbconfig VALUES ('69','Allow operator to enter appointment without description','','1','SES_EMPTY_NOTE','checkbox','A','sys_empty_note','1');

INSERT IGNORE INTO hdk_tbconfig VALUES ('70','OPERATOR: Notify the operator that he has a request for approval.','','3','SES_REQUEST_APPROVE','checkbox','A','Email_request_approve','1');

INSERT IGNORE INTO hdk_tbconfig VALUES ('71','Email Title','','5','EM_TITLE','','A','em_title','[HELPDEZK - Parracho]');

INSERT IGNORE INTO hdk_tbconfig VALUES ('72','Allow user reopening requests ','','1','SES_IND_REOPEN_USER','checkbox','A','sys_allow_reopen_user','1');

INSERT IGNORE INTO hdk_tbconfig VALUES ('73','Use hardware control (os, etiqueta e serial)','','1','SES_IND_EQUIPMENT','checkbox','A','sys_use_equipment','1');

INSERT IGNORE INTO hdk_tbconfig VALUES ('74','Set session time','','1','SES_TIME_SESSION','input','A','sys_time_session','12000');

INSERT IGNORE INTO hdk_tbconfig VALUES ('75','Number of working days for the system to automatically approve requests closed [0 = never approve]','','1','SES_QT_WORK_DAYS_REQUEST_APPROVAL','input','A','sys_workdays_request_approval','0');

INSERT IGNORE INTO hdk_tbconfig VALUES ('76','Show phone number, extension and cell phone on request','','10','SES_REQUEST_SHOW_PHONE','checkbox','A','ses_request_show_phone','1');

INSERT IGNORE INTO hdk_tbconfig VALUES ('77','Set refresh time on the grid of atendents [0 = no update]','','10','SES_REFRESH_OPERATOR_GRID','input','A','ses_refresh_opertor_grid','0');

INSERT IGNORE INTO hdk_tbconfig VALUES ('78','Send the attachment note inserted by attendant to the users email.','','1','SES_ATTACHMENT_OPERATOR_NOTE','checkbox','A','ses_attachment_operator_note','1');

INSERT IGNORE INTO hdk_tbconfig VALUES ('79','Enable additional information at the end of request.','','1','SES_REQUEST_ADDINFO','checkbox','A','ses_request_addinfo','1');

INSERT IGNORE INTO hdk_tbconfig VALUES ('80','OPERATOR: Send email to all attendantos of the group, when reject a request.','','3','SES_MAIL_OPERATOR_REJECT','checkbox','A','Email_operator_reject','1');

INSERT IGNORE INTO hdk_tbconfig VALUES ('81','LDAP/AD Server','','13','SES_LDAP_SERVER','','A','ldap_server','172.16.2.3');

INSERT IGNORE INTO hdk_tbconfig VALUES ('82','LDAP Distinguished Names','','13','SES_LDAP_DN','','A','ldap_dn','OU=usuarios,DC=helpdezk,DC=org');

INSERT IGNORE INTO hdk_tbconfig VALUES ('83','LDAP Domain','','13','SES_LDAP_DOMAIN','','A','ldap_domain','');

INSERT IGNORE INTO hdk_tbconfig VALUES ('84','LDAP Field ID','','13','SES_LDAP_FIELD','','A','ldap_field','uid');

INSERT IGNORE INTO hdk_tbconfig VALUES ('85','LDAP or AD','','13','SES_LDAP_AD','','A','Type','1');

INSERT IGNORE INTO hdk_tbconfig VALUES ('86','POP Domain','','12','POP_DOMAIN','','A','pop_domain','');

INSERT IGNORE INTO hdk_tbconfig VALUES ('89','Google two factor authentication','','1','SES_GOOGLE_2FA','checkbox','A','sys_2FAuthentication','0');



##
## TABELA: hdk_tbconfig_category
##
DROP TABLE IF EXISTS hdk_tbconfig_category ; 

CREATE TABLE IF NOT EXISTS `hdk_tbconfig_category` (
  `idconfigcategory` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) DEFAULT NULL,
  `smarty` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`idconfigcategory`),
  KEY `COD_CATEGORIA` (`idconfigcategory`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1 ; 

# Dados para a tabela: hdk_tbconfig_category

INSERT IGNORE INTO hdk_tbconfig_category VALUES ('1','Funcionalidades do Sistema','System_features');

INSERT IGNORE INTO hdk_tbconfig_category VALUES ('2','Permiss?es para Analista','');

INSERT IGNORE INTO hdk_tbconfig_category VALUES ('3','Notifica??es de E-mail','');

INSERT IGNORE INTO hdk_tbconfig_category VALUES ('4','Usu?rios','');

INSERT IGNORE INTO hdk_tbconfig_category VALUES ('5','Geral','');

INSERT IGNORE INTO hdk_tbconfig_category VALUES ('6','Patrim?nio','');

INSERT IGNORE INTO hdk_tbconfig_category VALUES ('7','Integra??o com SMTP/LDAP/MS Active Directory','');

INSERT IGNORE INTO hdk_tbconfig_category VALUES ('8','Invent?rio','');

INSERT IGNORE INTO hdk_tbconfig_category VALUES ('9','Inventario','');

INSERT IGNORE INTO hdk_tbconfig_category VALUES ('10','Outros itens','Other_items');

INSERT IGNORE INTO hdk_tbconfig_category VALUES ('11','Email Templates','');

INSERT IGNORE INTO hdk_tbconfig_category VALUES ('12','POP Server','');

INSERT IGNORE INTO hdk_tbconfig_category VALUES ('13','Integration with LDAP/MS Active Directory','Integration_ldap');



##
## TABELA: hdk_tbconfig_has_template
##
DROP TABLE IF EXISTS hdk_tbconfig_has_template ; 

CREATE TABLE IF NOT EXISTS `hdk_tbconfig_has_template` (
  `idconfig` int(4) NOT NULL,
  `idtemplate` int(4) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ; 

# Dados para a tabela: hdk_tbconfig_has_template

INSERT IGNORE INTO hdk_tbconfig_has_template VALUES ('1','1');

INSERT IGNORE INTO hdk_tbconfig_has_template VALUES ('2','2');

INSERT IGNORE INTO hdk_tbconfig_has_template VALUES ('3','3');

INSERT IGNORE INTO hdk_tbconfig_has_template VALUES ('4','4');

INSERT IGNORE INTO hdk_tbconfig_has_template VALUES ('80','80');

INSERT IGNORE INTO hdk_tbconfig_has_template VALUES ('70','70');

INSERT IGNORE INTO hdk_tbconfig_has_template VALUES ('67','67');

INSERT IGNORE INTO hdk_tbconfig_has_template VALUES ('47','47');

INSERT IGNORE INTO hdk_tbconfig_has_template VALUES ('43','43');

INSERT IGNORE INTO hdk_tbconfig_has_template VALUES ('16','16');

INSERT IGNORE INTO hdk_tbconfig_has_template VALUES ('13','13');



##
## TABELA: hdk_tbconfig_user
##
DROP TABLE IF EXISTS hdk_tbconfig_user ; 

CREATE TABLE IF NOT EXISTS `hdk_tbconfig_user` (
  `idconfiguser` int(11) NOT NULL AUTO_INCREMENT,
  `idperson` int(11) DEFAULT NULL,
  `lang` varchar(10) DEFAULT NULL,
  `theme` varchar(100) DEFAULT NULL,
  `grid_operator` varchar(255) DEFAULT '[0,0,0,0,0,0,0,0,0,0,0,0,0,0]',
  `grid_operator_width` varchar(255) DEFAULT '[22,8,85,105,70,115,90,90,80,130,80,80,55,100]',
  `grid_user` varchar(255) DEFAULT '[0,0,0,0,0,0,0,0]',
  `grid_user_width` varchar(255) DEFAULT '[26,75,100,240,200,100,130,145]',
  PRIMARY KEY (`idconfiguser`),
  KEY `fk_PerConf` (`idperson`),
  CONSTRAINT `fk_PerConf` FOREIGN KEY (`idperson`) REFERENCES `tbperson` (`idperson`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1 ; 

# Dados para a tabela: hdk_tbconfig_user

INSERT IGNORE INTO hdk_tbconfig_user VALUES ('7','1','','','[0,0,0,0,0,0,0,0,0,0,0,0,0,0]','[22,8,85,105,70,115,90,90,80,130,80,80,55,100]','[0,0,0,0,0,0,0,0]','[26,75,100,240,200,100,130,145]');



##
## TABELA: hdk_tbcore_area
##
DROP TABLE IF EXISTS hdk_tbcore_area ; 

CREATE TABLE IF NOT EXISTS `hdk_tbcore_area` (
  `idarea` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `status` char(1) NOT NULL DEFAULT 'A',
  `default` int(1) DEFAULT '0',
  PRIMARY KEY (`idarea`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC ; 

# Dados para a tabela: hdk_tbcore_area

INSERT IGNORE INTO hdk_tbcore_area VALUES ('1','Infrastructure','A','1');



##
## TABELA: hdk_tbcore_item
##
DROP TABLE IF EXISTS hdk_tbcore_item ; 

CREATE TABLE IF NOT EXISTS `hdk_tbcore_item` (
  `iditem` int(11) NOT NULL AUTO_INCREMENT,
  `idtype` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `status` char(1) NOT NULL DEFAULT 'A',
  `selected` int(11) NOT NULL DEFAULT '0',
  `classify` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`iditem`),
  KEY `FK_hdk_tbcore_item` (`idtype`),
  CONSTRAINT `FK_hdk_tbcore_item` FOREIGN KEY (`idtype`) REFERENCES `hdk_tbcore_type` (`idtype`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC ; 

# Dados para a tabela: hdk_tbcore_item

INSERT IGNORE INTO hdk_tbcore_item VALUES ('1','1','Wirelles','A','0','0');

INSERT IGNORE INTO hdk_tbcore_item VALUES ('2','1','Lan','A','0','0');



##
## TABELA: hdk_tbcore_service
##
DROP TABLE IF EXISTS hdk_tbcore_service ; 

CREATE TABLE IF NOT EXISTS `hdk_tbcore_service` (
  `idservice` int(11) NOT NULL AUTO_INCREMENT,
  `iditem` int(11) NOT NULL,
  `idpriority` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `status` char(1) NOT NULL DEFAULT 'A',
  `selected` int(11) NOT NULL DEFAULT '0',
  `classify` int(11) NOT NULL DEFAULT '1',
  `time_attendance` int(11) NOT NULL DEFAULT '0',
  `hours_attendance` int(11) NOT NULL DEFAULT '0',
  `days_attendance` int(11) NOT NULL DEFAULT '0',
  `ind_hours_minutes` char(1) NOT NULL DEFAULT 'H',
  PRIMARY KEY (`idservice`),
  KEY `FK_hdk_tbcore_service` (`iditem`),
  CONSTRAINT `FK_hdk_tbcore_service` FOREIGN KEY (`iditem`) REFERENCES `hdk_tbcore_item` (`iditem`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC ; 

# Dados para a tabela: hdk_tbcore_service

INSERT IGNORE INTO hdk_tbcore_service VALUES ('1','1','3','Add a MAc Address','A','0','0','0','0','1','H');

INSERT IGNORE INTO hdk_tbcore_service VALUES ('2','1','2','Setup a wirelles router','A','0','0','0','5','0','H');

INSERT IGNORE INTO hdk_tbcore_service VALUES ('3','2','1',' Do Not Obtain an IP Address ','A','0','0','0','30','0','M');

INSERT IGNORE INTO hdk_tbcore_service VALUES ('4','2','2',' Connectivity Problem ','A','0','0','0','2','0','H');

INSERT IGNORE INTO hdk_tbcore_service VALUES ('5','2','2',' Ethernet Cord Not Working','A','0','0','0','2','0','H');

INSERT IGNORE INTO hdk_tbcore_service VALUES ('6','2','3',' Internet connection so slow','A','0','0','0','0','1','H');

INSERT IGNORE INTO hdk_tbcore_service VALUES ('7','1','2',' Do Not Obtain an IP Address ','A','0','0','0','2','0','H');

INSERT IGNORE INTO hdk_tbcore_service VALUES ('8','1','2','Do Not Conect: IIncorrect Wifi Password','A','0','0','0','2','0','H');



##
## TABELA: hdk_tbcore_type
##
DROP TABLE IF EXISTS hdk_tbcore_type ; 

CREATE TABLE IF NOT EXISTS `hdk_tbcore_type` (
  `idtype` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `status` char(1) NOT NULL DEFAULT 'A',
  `selected` char(1) NOT NULL DEFAULT '0',
  `classify` char(1) NOT NULL DEFAULT '1',
  `idarea` int(11) NOT NULL,
  PRIMARY KEY (`idtype`),
  KEY `FK_hdk_tbcore_type` (`idarea`),
  CONSTRAINT `FK_hdk_tbcore_type` FOREIGN KEY (`idarea`) REFERENCES `hdk_tbcore_area` (`idarea`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC ; 

# Dados para a tabela: hdk_tbcore_type

INSERT IGNORE INTO hdk_tbcore_type VALUES ('1','Network','A','0','0','1');



##
## TABELA: hdk_tbcostcenter
##
DROP TABLE IF EXISTS hdk_tbcostcenter ; 

CREATE TABLE IF NOT EXISTS `hdk_tbcostcenter` (
  `idcostcenter` int(11) NOT NULL AUTO_INCREMENT,
  `idperson` int(11) NOT NULL,
  `cod_costcenter` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `status` char(1) NOT NULL DEFAULT 'A',
  PRIMARY KEY (`idcostcenter`),
  KEY `FK_hdk_tbcostcenter` (`idperson`),
  CONSTRAINT `FK_hdk_tbcostcenter` FOREIGN KEY (`idperson`) REFERENCES `tbperson` (`idperson`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC ; 

# Dados para a tabela: hdk_tbcostcenter

INSERT IGNORE INTO hdk_tbcostcenter VALUES ('1','60','11223344','Internal Needs','A');



##
## TABELA: hdk_tbdepartment
##
DROP TABLE IF EXISTS hdk_tbdepartment ; 

CREATE TABLE IF NOT EXISTS `hdk_tbdepartment` (
  `iddepartment` int(11) NOT NULL AUTO_INCREMENT,
  `idperson` int(11) NOT NULL,
  `cod_area` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `status` char(1) NOT NULL DEFAULT 'A',
  PRIMARY KEY (`iddepartment`),
  KEY `FK_hdk_tbdepartment` (`idperson`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC ; 

# Dados para a tabela: hdk_tbdepartment

INSERT IGNORE INTO hdk_tbdepartment VALUES ('1','60','0','PHP Development','A');

INSERT IGNORE INTO hdk_tbdepartment VALUES ('4','5','0','Project Engineering','A');

INSERT IGNORE INTO hdk_tbdepartment VALUES ('7','3','0','Teste','A');

INSERT IGNORE INTO hdk_tbdepartment VALUES ('9','3','0','HAHA','N');

INSERT IGNORE INTO hdk_tbdepartment VALUES ('10','5','0','internal 1','A');

INSERT IGNORE INTO hdk_tbdepartment VALUES ('11','5','0','External ','A');

INSERT IGNORE INTO hdk_tbdepartment VALUES ('12','3','0','External 2','A');

INSERT IGNORE INTO hdk_tbdepartment VALUES ('24','60','0','Technical Support','A');



##
## TABELA: hdk_tbdepartment_has_person
##
DROP TABLE IF EXISTS hdk_tbdepartment_has_person ; 

CREATE TABLE IF NOT EXISTS `hdk_tbdepartment_has_person` (
  `idperson` int(4) NOT NULL,
  `iddepartment` int(4) NOT NULL,
  KEY `FK_tbperson_has_juridical` (`idperson`),
  KEY `FK_tbdepartmet_has_person` (`iddepartment`),
  CONSTRAINT `FK_tbdepartmet_has_person` FOREIGN KEY (`iddepartment`) REFERENCES `hdk_tbdepartment` (`iddepartment`),
  CONSTRAINT `FK_tbperson_has_juridical` FOREIGN KEY (`idperson`) REFERENCES `tbperson` (`idperson`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC ; 

# Dados para a tabela: hdk_tbdepartment_has_person

INSERT IGNORE INTO hdk_tbdepartment_has_person VALUES ('1','1');



##
## TABELA: hdk_tbdownload
##
DROP TABLE IF EXISTS hdk_tbdownload ; 

CREATE TABLE IF NOT EXISTS `hdk_tbdownload` (
  `iddownload` int(4) NOT NULL AUTO_INCREMENT,
  `iddownloadcategory` int(4) NOT NULL,
  `name` varchar(25) NOT NULL,
  `description` blob NOT NULL,
  `file_name` varchar(250) NOT NULL,
  `date` date NOT NULL,
  `download_file_name` varchar(250) NOT NULL,
  `file_url` varchar(250) DEFAULT NULL,
  `version_description` varchar(100) NOT NULL,
  `restricted` char(1) NOT NULL DEFAULT 'N',
  `instruction` blob,
  `status` char(1) NOT NULL DEFAULT 'A',
  PRIMARY KEY (`iddownload`),
  KEY `FK_hdk_tbdownload` (`iddownloadcategory`),
  CONSTRAINT `FK_hdk_tbdownload` FOREIGN KEY (`iddownloadcategory`) REFERENCES `hdk_tbdownload_category` (`iddownloadcategory`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC ; 

# Dados para a tabela: hdk_tbdownload



##
## TABELA: hdk_tbdownload_category
##
DROP TABLE IF EXISTS hdk_tbdownload_category ; 

CREATE TABLE IF NOT EXISTS `hdk_tbdownload_category` (
  `iddownloadcategory` int(4) NOT NULL AUTO_INCREMENT,
  `category` varchar(200) NOT NULL,
  PRIMARY KEY (`iddownloadcategory`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC ; 

# Dados para a tabela: hdk_tbdownload_category

INSERT IGNORE INTO hdk_tbdownload_category VALUES ('1','General Files');

INSERT IGNORE INTO hdk_tbdownload_category VALUES ('2','Text Files');

INSERT IGNORE INTO hdk_tbdownload_category VALUES ('3','Image Files');

INSERT IGNORE INTO hdk_tbdownload_category VALUES ('4','Music');

INSERT IGNORE INTO hdk_tbdownload_category VALUES ('5','PDF');

INSERT IGNORE INTO hdk_tbdownload_category VALUES ('6','PHP');

INSERT IGNORE INTO hdk_tbdownload_category VALUES ('7','HTML');

INSERT IGNORE INTO hdk_tbdownload_category VALUES ('8','Overall Instructions');

INSERT IGNORE INTO hdk_tbdownload_category VALUES ('9','Rules');



##
## TABELA: hdk_tbevaluation
##
DROP TABLE IF EXISTS hdk_tbevaluation ; 

CREATE TABLE IF NOT EXISTS `hdk_tbevaluation` (
  `idevaluation` int(11) NOT NULL AUTO_INCREMENT,
  `idquestion` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `status` char(1) NOT NULL DEFAULT 'A',
  `icon_name` varchar(25) NOT NULL,
  `checked` int(1) DEFAULT '0',
  PRIMARY KEY (`idevaluation`),
  KEY `FK_hdk_tbevaluation` (`idquestion`),
  CONSTRAINT `FK_hdk_tbevaluation` FOREIGN KEY (`idquestion`) REFERENCES `hdk_tbevaluationquestion` (`idquestion`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC ; 

# Dados para a tabela: hdk_tbevaluation

INSERT IGNORE INTO hdk_tbevaluation VALUES ('1','1','Bad','N','ico_ruim.gif','0');

INSERT IGNORE INTO hdk_tbevaluation VALUES ('2','1','Regular','A','ico_regular.gif','0');

INSERT IGNORE INTO hdk_tbevaluation VALUES ('3','1','Good','A','ico_bom.gif','0');

INSERT IGNORE INTO hdk_tbevaluation VALUES ('4','1','Great','A','ico_otimo.gif','0');

INSERT IGNORE INTO hdk_tbevaluation VALUES ('5','1','Superb!','N','ico_bom.gif','0');

INSERT IGNORE INTO hdk_tbevaluation VALUES ('6','1','Perfect!','N','ico_otimo.gif','0');



##
## TABELA: hdk_tbevaluation_icon
##
DROP TABLE IF EXISTS hdk_tbevaluation_icon ; 

CREATE TABLE IF NOT EXISTS `hdk_tbevaluation_icon` (
  `idevaluation_icon` int(4) NOT NULL AUTO_INCREMENT,
  `file_name` varchar(50) NOT NULL,
  PRIMARY KEY (`idevaluation_icon`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC ; 

# Dados para a tabela: hdk_tbevaluation_icon

INSERT IGNORE INTO hdk_tbevaluation_icon VALUES ('1','dinheiro.jpg');

INSERT IGNORE INTO hdk_tbevaluation_icon VALUES ('2','tattoo_desenho.jpg');



##
## TABELA: hdk_tbevaluation_token
##
DROP TABLE IF EXISTS hdk_tbevaluation_token ; 

CREATE TABLE IF NOT EXISTS `hdk_tbevaluation_token` (
  `idtoken` int(11) NOT NULL AUTO_INCREMENT,
  `code_request` varchar(20) NOT NULL,
  `token` varchar(40) NOT NULL,
  PRIMARY KEY (`idtoken`),
  KEY `FK_code_request` (`code_request`),
  CONSTRAINT `FK_code_request` FOREIGN KEY (`code_request`) REFERENCES `hdk_tbrequest` (`code_request`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ; 

# Dados para a tabela: hdk_tbevaluation_token



##
## TABELA: hdk_tbevaluationquestion
##
DROP TABLE IF EXISTS hdk_tbevaluationquestion ; 

CREATE TABLE IF NOT EXISTS `hdk_tbevaluationquestion` (
  `idquestion` int(11) NOT NULL AUTO_INCREMENT,
  `question` varchar(200) NOT NULL,
  `status` char(1) NOT NULL DEFAULT 'A',
  PRIMARY KEY (`idquestion`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC ; 

# Dados para a tabela: hdk_tbevaluationquestion

INSERT IGNORE INTO hdk_tbevaluationquestion VALUES ('1','How would you rate the service(s) of operator(s)?','N');

INSERT IGNORE INTO hdk_tbevaluationquestion VALUES ('2','Como voce avalia o atendimento feito pelo atendente?','A');

INSERT IGNORE INTO hdk_tbevaluationquestion VALUES ('3','Test Question','N');



##
## TABELA: hdk_tbexecutionorder_person
##
DROP TABLE IF EXISTS hdk_tbexecutionorder_person ; 

CREATE TABLE IF NOT EXISTS `hdk_tbexecutionorder_person` (
  `idexecutionorder` int(4) NOT NULL AUTO_INCREMENT,
  `code_request` varchar(20) NOT NULL,
  `idperson` int(4) NOT NULL,
  `exorder` int(3) NOT NULL,
  PRIMARY KEY (`idexecutionorder`),
  KEY `FK_hdk_tbexecutionorder_person` (`idperson`),
  KEY `FK_hdk_tbexecutionorder_person2` (`code_request`),
  CONSTRAINT `FK_hdk_tbexecutionorder_person` FOREIGN KEY (`idperson`) REFERENCES `tbperson` (`idperson`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC ; 

# Dados para a tabela: hdk_tbexecutionorder_person



##
## TABELA: hdk_tbgetemail
##
DROP TABLE IF EXISTS hdk_tbgetemail ; 

CREATE TABLE IF NOT EXISTS `hdk_tbgetemail` (
  `idgetemail` int(11) NOT NULL AUTO_INCREMENT,
  `serverurl` varchar(60) DEFAULT NULL,
  `servertype` varchar(10) DEFAULT NULL,
  `serverport` char(5) DEFAULT NULL,
  `emailacct` varchar(100) DEFAULT NULL,
  `user` varchar(80) DEFAULT NULL,
  `password` char(30) DEFAULT NULL,
  `ind_create_user` int(1) DEFAULT '0',
  `ind_delete_server` int(1) DEFAULT '1',
  `idservice` int(11) DEFAULT NULL,
  PRIMARY KEY (`idgetemail`),
  KEY `FK_hdk_tbgetemail` (`idservice`),
  CONSTRAINT `FK_hdk_tbgetemail` FOREIGN KEY (`idservice`) REFERENCES `hdk_tbcore_service` (`idservice`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 ; 

# Dados para a tabela: hdk_tbgetemail

INSERT IGNORE INTO hdk_tbgetemail VALUES ('3','200.248.178.82','pop','110','','teste','1234','0','1','0');



##
## TABELA: hdk_tbgetemaildepartment
##
DROP TABLE IF EXISTS hdk_tbgetemaildepartment ; 

CREATE TABLE IF NOT EXISTS `hdk_tbgetemaildepartment` (
  `idgetemaildepartment` int(11) NOT NULL AUTO_INCREMENT,
  `idgetemail` int(11) DEFAULT NULL,
  `iddepartment` int(11) DEFAULT NULL,
  PRIMARY KEY (`idgetemaildepartment`),
  KEY `FK_hdk_tbgetemaildepartment` (`iddepartment`),
  KEY `FK_hdk_tbgetemaildepartment_IDGETEMAIL` (`idgetemail`),
  CONSTRAINT `FK_hdk_tbgetemaildepartment` FOREIGN KEY (`iddepartment`) REFERENCES `hdk_tbdepartment` (`iddepartment`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_hdk_tbgetemaildepartment_IDGETEMAIL` FOREIGN KEY (`idgetemail`) REFERENCES `hdk_tbgetemail` (`idgetemail`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC ; 

# Dados para a tabela: hdk_tbgetemaildepartment



##
## TABELA: hdk_tbgroup
##
DROP TABLE IF EXISTS hdk_tbgroup ; 

CREATE TABLE IF NOT EXISTS `hdk_tbgroup` (
  `idgroup` int(11) NOT NULL AUTO_INCREMENT,
  `idperson` int(11) NOT NULL,
  `level` char(3) NOT NULL,
  `idcustomer` int(11) NOT NULL,
  `repass_only` char(1) NOT NULL DEFAULT 'N',
  `status` char(1) NOT NULL DEFAULT 'A',
  PRIMARY KEY (`idgroup`),
  KEY `FK_hdk_tbgroup` (`idcustomer`),
  KEY `FK_hdk_tbgroup_IDPERSON` (`idperson`),
  CONSTRAINT `FK_hdk_tbgroup` FOREIGN KEY (`idcustomer`) REFERENCES `tbperson` (`idperson`),
  CONSTRAINT `FK_hdk_tbgroup_IDPERSON` FOREIGN KEY (`idperson`) REFERENCES `tbperson` (`idperson`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC ; 

# Dados para a tabela: hdk_tbgroup

INSERT IGNORE INTO hdk_tbgroup VALUES ('1','61','1','60','N','A');



##
## TABELA: hdk_tbgroup_has_person
##
DROP TABLE IF EXISTS hdk_tbgroup_has_person ; 

CREATE TABLE IF NOT EXISTS `hdk_tbgroup_has_person` (
  `idperson` int(4) NOT NULL,
  `idgroup` int(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC ; 

# Dados para a tabela: hdk_tbgroup_has_person

INSERT IGNORE INTO hdk_tbgroup_has_person VALUES ('62','2');

INSERT IGNORE INTO hdk_tbgroup_has_person VALUES ('65','3');

INSERT IGNORE INTO hdk_tbgroup_has_person VALUES ('69','4');

INSERT IGNORE INTO hdk_tbgroup_has_person VALUES ('62','1');

INSERT IGNORE INTO hdk_tbgroup_has_person VALUES ('71','1');

INSERT IGNORE INTO hdk_tbgroup_has_person VALUES ('71','2');

INSERT IGNORE INTO hdk_tbgroup_has_person VALUES ('71','3');

INSERT IGNORE INTO hdk_tbgroup_has_person VALUES ('71','4');

INSERT IGNORE INTO hdk_tbgroup_has_person VALUES ('72','2');

INSERT IGNORE INTO hdk_tbgroup_has_person VALUES ('73','2');

INSERT IGNORE INTO hdk_tbgroup_has_person VALUES ('74','1');

INSERT IGNORE INTO hdk_tbgroup_has_person VALUES ('62','3');

INSERT IGNORE INTO hdk_tbgroup_has_person VALUES ('62','4');



##
## TABELA: hdk_tbgroup_has_service
##
DROP TABLE IF EXISTS hdk_tbgroup_has_service ; 

CREATE TABLE IF NOT EXISTS `hdk_tbgroup_has_service` (
  `idgroup` int(4) NOT NULL,
  `idservice` int(4) NOT NULL,
  KEY `COD_GRUPO` (`idgroup`),
  KEY `FK_SERVICO_X_GRUPO_SERVICO` (`idservice`),
  CONSTRAINT `FK_hdk_tbgroup_has_service` FOREIGN KEY (`idgroup`) REFERENCES `hdk_tbgroup` (`idgroup`),
  CONSTRAINT `FK_hdk_tbgroup_has_service2` FOREIGN KEY (`idservice`) REFERENCES `hdk_tbcore_service` (`idservice`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ; 

# Dados para a tabela: hdk_tbgroup_has_service

INSERT IGNORE INTO hdk_tbgroup_has_service VALUES ('1','1');

INSERT IGNORE INTO hdk_tbgroup_has_service VALUES ('1','2');

INSERT IGNORE INTO hdk_tbgroup_has_service VALUES ('1','3');

INSERT IGNORE INTO hdk_tbgroup_has_service VALUES ('1','4');

INSERT IGNORE INTO hdk_tbgroup_has_service VALUES ('1','5');

INSERT IGNORE INTO hdk_tbgroup_has_service VALUES ('1','6');

INSERT IGNORE INTO hdk_tbgroup_has_service VALUES ('1','7');

INSERT IGNORE INTO hdk_tbgroup_has_service VALUES ('1','8');



##
## TABELA: hdk_tbnote
##
DROP TABLE IF EXISTS hdk_tbnote ; 

CREATE TABLE IF NOT EXISTS `hdk_tbnote` (
  `idnote` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code_request` bigint(16) unsigned DEFAULT NULL,
  `idperson` int(10) unsigned DEFAULT NULL,
  `description` blob,
  `entry_date` datetime DEFAULT NULL,
  `minutes` float DEFAULT NULL,
  `start_hour` varchar(8) DEFAULT NULL,
  `finish_hour` varchar(8) DEFAULT NULL,
  `IND_CHAMADO` int(10) unsigned DEFAULT NULL,
  `execution_date` datetime DEFAULT NULL,
  `hour_type` int(3) unsigned DEFAULT NULL,
  `service_value` float(10,4) DEFAULT NULL,
  `public` int(1) unsigned DEFAULT '1',
  `idtype` int(3) unsigned DEFAULT NULL,
  `idnote_attachment` bigint(10) DEFAULT NULL,
  `ip_adress` varchar(30) DEFAULT NULL,
  `callback` int(1) DEFAULT '0',
  PRIMARY KEY (`idnote`),
  KEY `COD_SOLICITACAO` (`code_request`),
  KEY `COD_TIPO` (`idtype`),
  KEY `COD_USUARIO` (`idperson`),
  KEY `DAT_CADASTRO` (`entry_date`),
  KEY `fk_idnote_attachment` (`idnote_attachment`),
  CONSTRAINT `fk_idnote_attachment` FOREIGN KEY (`idnote_attachment`) REFERENCES `hdk_tbnote_attachment` (`idnote_attachment`)
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=latin1 ; 

# Dados para a tabela: hdk_tbnote

INSERT IGNORE INTO hdk_tbnote VALUES ('1','201211000014','85','UEhBK1BHSStQQzlpUGp3dmNEND0=','2012-11-13 17:36:13','0','0','0','0','2012-11-13 17:36:13','0','0.0000','1','3','0','127.0.0.1','0');

INSERT IGNORE INTO hdk_tbnote VALUES ('2','201211000014','1','UEhBK1BHSStVMjlzYVdOcGRHRW1ZMk5sWkdsc095WmhkR2xzWkdVN2J5QkJjM04xYldsa1lUd3ZZajQ4TDNBKw==','2012-11-13 17:36:36','0','','','0','0000-00-00 00:00:00','0','0.0000','1','3','0','127.0.0.1','0');

INSERT IGNORE INTO hdk_tbnote VALUES ('3','201211000014','1','UEhBK1BHSStRV2QxWVhKa1lXNWtieUJoY0hKdmRtSERwOE9qYnlCa2J5QjFjM1VtWVdGamRYUmxPM0pwYnp3dllqNDhMM0Er','2012-11-13 17:36:43','0','','','0','0000-00-00 00:00:00','0','0.0000','1','3','0','127.0.0.1','0');

INSERT IGNORE INTO hdk_tbnote VALUES ('4','201211000014','85','UEhBK1BHSStVMjlzYVdOcGRHRW1ZMk5sWkdsc095WmhkR2xzWkdVN2J5QkZibU5sY25KaFpHRThMMkkrUEM5d1BnPT0=','2012-11-13 17:37:57','0','','','0','0000-00-00 00:00:00','0','0.0000','1','3','0','127.0.0.1','0');

INSERT IGNORE INTO hdk_tbnote VALUES ('5','201211000014','85','UEhBK1BHSStVMjlzYVdOcGRHRW1ZMk5sWkdsc095WmhkR2xzWkdVN2J5QkZibU5sY25KaFpHRThMMkkrUEM5d1BnPT0=','2012-11-13 17:38:02','0','','','0','0000-00-00 00:00:00','0','0.0000','1','3','0','127.0.0.1','0');

INSERT IGNORE INTO hdk_tbnote VALUES ('6','201211000015','1','UEhBK1BHSStQQzlpUGp3dmNEND0=','2012-11-13 17:55:19','0','0','0','0','2012-11-13 17:55:19','0','0.0000','1','3','0','127.0.0.1','0');

INSERT IGNORE INTO hdk_tbnote VALUES ('7','201211000015','1','UEhBK1BHSStVMjlzYVdOcGRHRW1ZMk5sWkdsc095WmhkR2xzWkdVN2J5QlNaWEJoYzNOaFpHRWdjR0Z5WVNCaGRHVnVaR1Z1ZEdVZ1lYUmxibVJsYm5SbFBDOWlQand2Y0Q0PQ==','2012-11-13 17:56:19','0','','','0','0000-00-00 00:00:00','0','0.0000','1','3','0','127.0.0.1','0');

INSERT IGNORE INTO hdk_tbnote VALUES ('8','201211000016','1','UEhBK1BHSStVMjlzYVdOcGRHRW1ZMk5sWkdsc095WmhkR2xzWkdVN2J5QkRZV1JoYzNSeVlXUmhQQzlpUGp3dmNEND0=','2012-11-14 15:03:12','0','0','0','0','2012-11-14 15:03:12','0','0.0000','1','3','0','127.0.0.1','0');

INSERT IGNORE INTO hdk_tbnote VALUES ('9','201211000017','1','UEhBK1BHSStQQzlpUGp3dmNEND0=','2012-11-14 15:04:07','0','0','0','0','2012-11-14 15:04:07','0','0.0000','1','3','0','127.0.0.1','0');

INSERT IGNORE INTO hdk_tbnote VALUES ('10','201211000016','1','UEhBK1BHSStQSE53WVc0Z2MzUjViR1U5SW1OdmJHOXlPaUFqUmtZd01EQXdPeUkrVTI5c2FXTnBkR0VtWTJObFpHbHNPeVpoZEdsc1pHVTdieUJTWldGaVpYSjBZVHd2YzNCaGJqNDhMMkkrUEM5d1BnPT0=','2012-11-14 15:04:37','0','','','0','0000-00-00 00:00:00','0','0.0000','1','3','0','127.0.0.1','0');

INSERT IGNORE INTO hdk_tbnote VALUES ('11','201211000016','1','UEhBK0NnbDZZV05rYzJaamRuaDJZM2gyUEM5d1Bnbz0=','2012-11-14 15:04:56','0','','','0','0000-00-00 00:00:00','0','0.0000','1','2','0','127.0.0.1','0');

INSERT IGNORE INTO hdk_tbnote VALUES ('12','201211000016','74','UEhBK0NnbGhjMkZ6WkhOaFpHRnpaSE04TDNBK0NnPT0=','2012-11-14 15:05:59','0.17','15:05:50','15:06:00','0','2012-11-14 00:00:00','0','0.0000','1','2','0','127.0.0.1','0');

INSERT IGNORE INTO hdk_tbnote VALUES ('13','201211000016','74','UEhBK0NnbGhjMkZ6WkhOaFpHRnpaSE04TDNBK0NnPT0=','2012-11-14 15:06:02','0.17','15:05:50','15:06:02','0','2012-11-14 00:00:00','0','0.0000','1','2','0','127.0.0.1','0');

INSERT IGNORE INTO hdk_tbnote VALUES ('14','201211000016','74','UEhBK1BHSStVMjlzYVdOcGRHRW1ZMk5sWkdsc095WmhkR2xzWkdVN2J5QlNaWEJoYzNOaFpHRWdjR0Z5WVNCaGRHVnVaR1Z1ZEdVZ1FXeGxhbUZ1WkhKdlBDOWlQand2Y0Q0PQ==','2012-11-14 15:06:19','0','','','0','0000-00-00 00:00:00','0','0.0000','1','3','0','127.0.0.1','0');

INSERT IGNORE INTO hdk_tbnote VALUES ('15','201211000015','74','UEhBK1BHSStVMjlzYVdOcGRHRW1ZMk5sWkdsc095WmhkR2xzWkdVN2J5QlNaWEJoYzNOaFpHRWdjR0Z5WVNCaGRHVnVaR1Z1ZEdVZ1FXeGxhbUZ1WkhKdlBDOWlQand2Y0Q0PQ==','2012-11-14 15:06:47','0','','','0','0000-00-00 00:00:00','0','0.0000','1','3','0','127.0.0.1','0');

INSERT IGNORE INTO hdk_tbnote VALUES ('16','201211000018','85','UEhBK1BHSStQQzlpUGp3dmNEND0=','2012-11-19 11:33:24','0','0','0','0','2012-11-19 11:33:24','0','0.0000','1','3','0','127.0.0.1','0');

INSERT IGNORE INTO hdk_tbnote VALUES ('17','201211000018','1','UEhBK1BHSStVMjlzYVdOcGRHRW1ZMk5sWkdsc095WmhkR2xzWkdVN2J5QkJjM04xYldsa1lUd3ZZajQ4TDNBKw==','2012-11-19 11:34:07','0','','','0','0000-00-00 00:00:00','0','0.0000','1','3','0','127.0.0.1','0');

INSERT IGNORE INTO hdk_tbnote VALUES ('18','201211000018','1','UEhBK0NnbHRhVzFwYldsdGFUd3ZjRDRL','2012-11-19 11:34:24','0.2','11:34:23','11:34:35','0','2012-11-19 00:00:00','0','0.0000','1','2','0','127.0.0.1','0');

INSERT IGNORE INTO hdk_tbnote VALUES ('19','201211000018','85','UEhBK0NnbGhjMkZ6WVhOaGN6d3ZjRDRL','2012-11-19 11:39:33','0','','','0','0000-00-00 00:00:00','0','0.0000','1','2','0','127.0.0.1','0');

INSERT IGNORE INTO hdk_tbnote VALUES ('20','201211000018','1','UEhBK0NnbHpaR1prYzJZZ2MyUm1JSE5rWmlCelpHWWdjMlJtSUhOa0lHWnpaR1lnYzJRZ1ptUnpJSE04TDNBK0NnPT0=','2012-11-19 11:40:47','0.13','11:40:50','11:40:58','0','2012-11-19 00:00:00','0','0.0000','1','2','0','127.0.0.1','0');

INSERT IGNORE INTO hdk_tbnote VALUES ('21','201211000019','85','UEhBK1BHSStQQzlpUGp3dmNEND0=','2012-11-19 13:02:54','0','0','0','0','2012-11-19 13:02:54','0','0.0000','1','3','0','127.0.0.1','0');

INSERT IGNORE INTO hdk_tbnote VALUES ('22','201211000019','1','UEhBK1BHSStVMjlzYVdOcGRHRW1ZMk5sWkdsc095WmhkR2xzWkdVN2J5QkJjM04xYldsa1lUd3ZZajQ4TDNBKw==','2012-11-19 13:03:58','0','','','0','0000-00-00 00:00:00','0','0.0000','1','3','0','127.0.0.1','0');

INSERT IGNORE INTO hdk_tbnote VALUES ('23','201211000019','85','UEhBK0NnbFVaWE4wWlNCcGJtTnNkWE1tWVhScGJHUmxPMjhnWVhCdmJuUmhiV1Z1ZEc4dVBDOXdQZ289','2012-11-19 13:12:31','0','','','0','0000-00-00 00:00:00','0','0.0000','1','2','0','127.0.0.1','0');

INSERT IGNORE INTO hdk_tbnote VALUES ('24','201211000019','1','UEhBK0NnbFVaWE4wWlNCaGNHOXVkR0Z0Wlc1MGJ5QmhkR1Z1WkdWdWRHVXVQQzl3UGdvPQ==','2012-11-19 13:13:46','0.52','13:13:26','13:13:57','0','2012-11-19 00:00:00','0','0.0000','1','2','0','127.0.0.1','0');

INSERT IGNORE INTO hdk_tbnote VALUES ('25','201211000020','85','UEhBK1BHSStQQzlpUGp3dmNEND0=','2012-11-19 13:27:40','0','0','0','0','2012-11-19 13:27:40','0','0.0000','1','3','0','127.0.0.1','0');

INSERT IGNORE INTO hdk_tbnote VALUES ('26','201211000019','1','UEhBK1BHSStRV2QxWVhKa1lXNWtieUJoY0hKdmRtSERwOE9qYnlCa2J5QjFjM1VtWVdGamRYUmxPM0pwYnp3dllqNDhMM0Er','2012-11-19 13:32:16','0','','','0','0000-00-00 00:00:00','0','0.0000','1','3','0','127.0.0.1','0');

INSERT IGNORE INTO hdk_tbnote VALUES ('27','201211000020','1','UEhBK1BHSStVMjlzYVdOcGRHRW1ZMk5sWkdsc095WmhkR2xzWkdVN2J5QkJjM04xYldsa1lUd3ZZajQ4TDNBKw==','2012-11-19 15:40:06','0','','','0','0000-00-00 00:00:00','0','0.0000','1','3','0','127.0.0.1','0');

INSERT IGNORE INTO hdk_tbnote VALUES ('28','201211000020','1','UEhBK1BHSStVMjlzYVdOcGRHRW1ZMk5sWkdsc095WmhkR2xzWkdVN2J5QkJjM04xYldsa1lUd3ZZajQ4TDNBKw==','2012-11-19 15:40:10','0','','','0','0000-00-00 00:00:00','0','0.0000','1','3','0','127.0.0.1','0');

INSERT IGNORE INTO hdk_tbnote VALUES ('29','201211000020','1','UEhBK0NnbDBaWE56YzNOMFpUd3ZjRDRL','2012-11-19 15:40:33','0.32','15:40:25','15:40:44','0','2012-11-19 00:00:00','0','0.0000','1','2','0','127.0.0.1','0');

INSERT IGNORE INTO hdk_tbnote VALUES ('30','201211000020','1','UEhBK0NnbGhZV0ZoWVR3dmNENEs=','2012-11-19 17:16:39','0.32','17:16:31','17:16:50','0','2012-11-19 00:00:00','0','0.0000','1','2','0','127.0.0.1','0');

INSERT IGNORE INTO hdk_tbnote VALUES ('31','201211000020','1','UEhBK0NnbGhjMlJoYzJROEwzQStDZz09','2012-11-19 17:27:14','10.47','17:16:57','17:27:25','0','2012-11-19 00:00:00','0','0.0000','1','2','0','127.0.0.1','0');

INSERT IGNORE INTO hdk_tbnote VALUES ('32','201211000020','85','UEhBK0NnbGhjMlJoYzJSaGMyUThMM0ErQ2c9PQ==','2012-11-19 17:45:13','0','','','0','0000-00-00 00:00:00','0','0.0000','1','2','0','127.0.0.1','0');

INSERT IGNORE INTO hdk_tbnote VALUES ('33','201211000018','1','UEhBK0NnbGhZV0ZoUEM5d1Bnbz0=','2012-11-19 17:45:39','0.12','17:45:43','17:45:50','0','2012-11-19 00:00:00','0','0.0000','1','2','0','127.0.0.1','0');

INSERT IGNORE INTO hdk_tbnote VALUES ('34','201211000020','1','UEhBK0NnbGhZV0ZoWVdFOEwzQStDZz09','2012-11-19 17:45:56','0.07','17:46:03','17:46:07','0','2012-11-19 00:00:00','0','0.0000','1','2','0','127.0.0.1','0');

INSERT IGNORE INTO hdk_tbnote VALUES ('35','201211000018','1','UEhBK0NnbGhZU0JoSUdFZ1lTQW1ibUp6Y0R0aFBDOXdQZ289','2012-11-19 17:57:21','0.25','17:57:18','17:57:33','0','2012-11-19 00:00:00','0','0.0000','1','2','1','127.0.0.1','0');

INSERT IGNORE INTO hdk_tbnote VALUES ('36','201211000021','85','UEhBK1BHSStQQzlpUGp3dmNEND0=','2012-11-19 18:00:05','0','0','0','0','2012-11-19 18:00:05','0','0.0000','1','3','0','127.0.0.1','0');

INSERT IGNORE INTO hdk_tbnote VALUES ('37','201211000021','1','UEhBK1BHSStVMjlzYVdOcGRHRW1ZMk5sWkdsc095WmhkR2xzWkdVN2J5QkJjM04xYldsa1lUd3ZZajQ4TDNBKw==','2012-11-19 18:07:46','0','','','0','0000-00-00 00:00:00','0','0.0000','1','3','0','127.0.0.1','0');

INSERT IGNORE INTO hdk_tbnote VALUES ('38','201211000021','1','UEhBK0NnbGhjMlJoYzJSaGMyUmhjMlJoY3lCaGMyUmhjMlFnWVhOa1lYTmtQQzl3UGdvPQ==','2012-11-19 18:08:00','0.13','18:08:03','18:08:11','0','2012-11-19 00:00:00','0','0.0000','1','2','0','127.0.0.1','0');

INSERT IGNORE INTO hdk_tbnote VALUES ('39','201211000017','1','UEhBK1BHSStVMjlzYVdOcGRHRW1ZMk5sWkdsc095WmhkR2xzWkdVN2J5QlNaWEJoYzNOaFpHRWdjR0Z5WVNCaGRHVnVaR1Z1ZEdVZ1lYUmxibVJsYm5SbFBDOWlQand2Y0Q0PQ==','2012-11-21 09:38:32','0','','','0','0000-00-00 00:00:00','0','0.0000','1','3','0','127.0.0.1','0');

INSERT IGNORE INTO hdk_tbnote VALUES ('40','201211000022','85','UEhBK1BHSStQQzlpUGp3dmNEND0=','2012-11-21 10:10:17','0','0','0','0','2012-11-21 10:10:17','0','0.0000','1','3','0','127.0.0.1','0');

INSERT IGNORE INTO hdk_tbnote VALUES ('41','201211000023','85','UEhBK1BHSStQQzlpUGp3dmNEND0=','2012-11-21 10:51:18','0','0','0','0','2012-11-21 10:51:18','0','0.0000','1','3','0','127.0.0.1','0');

INSERT IGNORE INTO hdk_tbnote VALUES ('42','201211000023','1','UEhBK1BHSStVMjlzYVdOcGRHRW1ZMk5sWkdsc095WmhkR2xzWkdVN2J5QkJjM04xYldsa1lUd3ZZajQ4TDNBKw==','2012-11-21 10:52:38','0','','','0','0000-00-00 00:00:00','0','0.0000','1','3','0','127.0.0.1','0');

INSERT IGNORE INTO hdk_tbnote VALUES ('43','201211000023','1','UEhBK1BHSStVMjlzYVdOcGRHRW1ZMk5sWkdsc095WmhkR2xzWkdVN2J5QlNaWEJoYzNOaFpHRWdjR0Z5WVNCbmNuVndieUE4TDJJK1BDOXdQZz09','2012-11-21 10:53:02','0','','','0','0000-00-00 00:00:00','0','0.0000','1','3','0','127.0.0.1','0');

INSERT IGNORE INTO hdk_tbnote VALUES ('44','201211000023','1','UEhBK1BHSStVMjlzYVdOcGRHRW1ZMk5sWkdsc095WmhkR2xzWkdVN2J5QlNaWEJoYzNOaFpHRWdjR0Z5WVNCbmNuVndieUE4TDJJK1BDOXdQZz09','2012-11-21 11:40:04','0','','','0','0000-00-00 00:00:00','0','0.0000','1','3','0','127.0.0.1','0');

INSERT IGNORE INTO hdk_tbnote VALUES ('45','201211000023','1','UEhBK1BHSStVMjlzYVdOcGRHRW1ZMk5sWkdsc095WmhkR2xzWkdVN2J5QlNaWEJoYzNOaFpHRWdjR0Z5WVNCbmNuVndieUJJWVhKa2QyRnlaU0JJWVc1a2JHbHVaend2WWo0OEwzQSs=','2012-11-21 11:46:08','0','','','0','0000-00-00 00:00:00','0','0.0000','1','3','0','127.0.0.1','0');

INSERT IGNORE INTO hdk_tbnote VALUES ('46','201211000023','1','UEhBK1BHSStVMjlzYVdOcGRHRW1ZMk5sWkdsc095WmhkR2xzWkdVN2J5QlNaWEJoYzNOaFpHRWdjR0Z5WVNCbmNuVndieUJJWVhKa2QyRnlaU0JJWVc1a2JHbHVaend2WWo0OEwzQSs=','2012-11-21 11:46:40','0','','','0','0000-00-00 00:00:00','0','0.0000','1','3','0','127.0.0.1','0');

INSERT IGNORE INTO hdk_tbnote VALUES ('47','201211000023','1','UEhBK1BHSStVMjlzYVdOcGRHRW1ZMk5sWkdsc095WmhkR2xzWkdVN2J5QlNaWEJoYzNOaFpHRWdjR0Z5WVNCbmNuVndieUJJWVhKa2QyRnlaU0JJWVc1a2JHbHVaend2WWo0OEwzQSs=','2012-11-21 11:47:12','0','','','0','0000-00-00 00:00:00','0','0.0000','1','3','0','127.0.0.1','0');

INSERT IGNORE INTO hdk_tbnote VALUES ('48','201211000024','85','UEhBK1BHSStQQzlpUGp3dmNEND0=','2012-11-21 11:47:44','0','0','0','0','2012-11-21 11:47:44','0','0.0000','1','3','0','127.0.0.1','0');

INSERT IGNORE INTO hdk_tbnote VALUES ('49','201211000024','1','UEhBK1BHSStVMjlzYVdOcGRHRW1ZMk5sWkdsc095WmhkR2xzWkdVN2J5QkJjM04xYldsa1lUd3ZZajQ4TDNBKw==','2012-11-21 11:48:05','0','','','0','0000-00-00 00:00:00','0','0.0000','1','3','0','127.0.0.1','0');

INSERT IGNORE INTO hdk_tbnote VALUES ('50','201211000024','1','UEhBK1BHSStVMjlzYVdOcGRHRW1ZMk5sWkdsc095WmhkR2xzWkdVN2J5QlNaWEJoYzNOaFpHRWdjR0Z5WVNCbmNuVndieUJJWVhKa2QyRnlaU0JJWVc1a2JHbHVaend2WWo0OEwzQSs=','2012-11-21 11:48:16','0','','','0','0000-00-00 00:00:00','0','0.0000','1','3','0','127.0.0.1','0');

INSERT IGNORE INTO hdk_tbnote VALUES ('51','201211000025','85','UEhBK1BHSStQQzlpUGp3dmNEND0=','2012-11-21 11:50:56','0','0','0','0','2012-11-21 11:50:56','0','0.0000','1','3','0','127.0.0.1','0');

INSERT IGNORE INTO hdk_tbnote VALUES ('52','201211000025','1','UEhBK1BHSStVMjlzYVdOcGRHRW1ZMk5sWkdsc095WmhkR2xzWkdVN2J5QkJjM04xYldsa1lUd3ZZajQ4TDNBKw==','2012-11-21 11:51:15','0','','','0','0000-00-00 00:00:00','0','0.0000','1','3','0','127.0.0.1','0');

INSERT IGNORE INTO hdk_tbnote VALUES ('53','201211000025','1','UEhBK1BHSStVMjlzYVdOcGRHRW1ZMk5sWkdsc095WmhkR2xzWkdVN2J5QlNaWEJoYzNOaFpHRWdjR0Z5WVNCbmNuVndieUJJWVhKa2QyRnlaU0JJWVc1a2JHbHVaend2WWo0OEwzQSs=','2012-11-21 11:51:28','0','','','0','0000-00-00 00:00:00','0','0.0000','1','3','0','127.0.0.1','0');

INSERT IGNORE INTO hdk_tbnote VALUES ('54','201211000025','1','UEhBK1BHSStVMjlzYVdOcGRHRW1ZMk5sWkdsc095WmhkR2xzWkdVN2J5QlNaWEJoYzNOaFpHRWdjR0Z5WVNCbmNuVndieUE4TDJJK1BDOXdQZz09','2012-11-21 12:51:39','0','','','0','0000-00-00 00:00:00','0','0.0000','1','3','0','127.0.0.1','0');

INSERT IGNORE INTO hdk_tbnote VALUES ('55','201211000025','1','UEhBK1BHSStVMjlzYVdOcGRHRW1ZMk5sWkdsc095WmhkR2xzWkdVN2J5QlNaWEJoYzNOaFpHRWdjR0Z5WVNCbmNuVndieUJJWVhKa2QyRnlaU0JJWVc1a2JHbHVaend2WWo0OEwzQSs=','2012-11-21 12:52:17','0','','','0','0000-00-00 00:00:00','0','0.0000','1','3','0','127.0.0.1','0');

INSERT IGNORE INTO hdk_tbnote VALUES ('56','201211000025','1','UEhBK1BHSStVMjlzYVdOcGRHRW1ZMk5sWkdsc095WmhkR2xzWkdVN2J5QlNaWEJoYzNOaFpHRWdjR0Z5WVNCbmNuVndieUJJWVhKa2QyRnlaU0JJWVc1a2JHbHVaend2WWo0OEwzQSs=','2012-11-21 13:39:16','0','','','0','0000-00-00 00:00:00','0','0.0000','1','3','0','127.0.0.1','0');

INSERT IGNORE INTO hdk_tbnote VALUES ('57','201211000025','1','UEhBK1BHSStVMjlzYVdOcGRHRW1ZMk5sWkdsc095WmhkR2xzWkdVN2J5QlNaWEJoYzNOaFpHRWdjR0Z5WVNCbmNuVndieUJUYjJaMGQyRnlaU0JJWVc1a2JHbHVaend2WWo0OEwzQSs=','2012-11-21 13:39:53','0','','','0','0000-00-00 00:00:00','0','0.0000','1','3','0','127.0.0.1','0');

INSERT IGNORE INTO hdk_tbnote VALUES ('58','201211000026','64','UEhBK1BHSStQQzlpUGp3dmNEND0=','2012-11-26 17:54:54','0','0','0','0','2012-11-26 17:54:54','0','0.0000','1','3','0','127.0.0.1','0');

INSERT IGNORE INTO hdk_tbnote VALUES ('59','201211000026','62','UEhBK1BHSStVbVZ4ZFdWemRDQkJjM04xYldWa1BDOWlQand2Y0Q0PQ==','2012-11-26 17:56:35','0','','','0','0000-00-00 00:00:00','0','0.0000','1','3','0','127.0.0.1','0');

INSERT IGNORE INTO hdk_tbnote VALUES ('60','201211000026','62','UEhBK0NnazhjM0JoYmlCemRIbHNaVDBpWm05dWRDMW1ZVzFwYkhrNklHRnlhV0ZzTENCellXNXpMWE5sY21sbU95Qm1iMjUwTFhOcGVtVTZJREV6Y0hnN0lqNVdaWEpwWm5rZ2VXOTFjaUJCYm5ScExWWnBjblZ6SUhabGNuTnBiMjRzSUdsbUlHbDBKaU16T1R0eklHeHZkMlZ5SUhSb1lXNGdNVEF1TUM0eElHeGxkQ0IxY3lCcmJtOTNJSFJvWVhRZ2QyVWdkMmxzYkNCb1lYWmxJSFJ2SUdSdklHRWdjbVZ0YjNSbElHRmpZMlZ6Y3lCMGJ5QjFjR1JoZEdVZ2FYUXVKbTVpYzNBN1BDOXpjR0Z1UGp3dmNENEs=','2012-11-26 18:00:35','3.92','17:56:37','18:00:32','0','0000-00-00 00:00:00','0','0.0000','1','2','0','127.0.0.1','0');

INSERT IGNORE INTO hdk_tbnote VALUES ('61','201301000001','64','UEhBK1BHSStQQzlpUGp3dmNEND0=','2013-01-11 15:57:40','0','0','0','0','2013-01-11 15:57:40','0','0.0000','1','3','0','127.0.0.1','0');

INSERT IGNORE INTO hdk_tbnote VALUES ('62','201301000002','64','UEhBK1BHSStQQzlpUGp3dmNEND0=','2013-01-11 15:59:08','0','0','0','0','2013-01-11 15:59:08','0','0.0000','1','3','0','127.0.0.1','0');

INSERT IGNORE INTO hdk_tbnote VALUES ('63','201301000001','62','UEhBK1BHSStVbVZ4ZFdWemRDQkJjM04xYldWa1BDOWlQand2Y0Q0PQ==','2013-01-11 16:02:11','0','','','0','0000-00-00 00:00:00','0','0.0000','1','3','0','127.0.0.1','0');

INSERT IGNORE INTO hdk_tbnote VALUES ('64','201301000001','62','UEhBK0NnbFBheTRnUVZOQlVDQnpkWEJ3YjNKMElITjBZV1lnZDJsc2JDQmhibmR6WlhJZ2VXOTFjaUJ5WlhGMVpYTjBMaVp1WW5Od096d3ZjRDRL','2013-01-11 16:03:08','0.87','16:02:10','16:03:02','0','2013-11-01 00:00:00','0','0.0000','1','2','0','127.0.0.1','0');

INSERT IGNORE INTO hdk_tbnote VALUES ('65','201211000025','62','UEhBK1BHSStWMkZwZEdsdVp5Qm1iM0lnZFhObGNtQnpJR0Z3Y0hKdmRtRnNQQzlpUGp3dmNEND0=','2013-01-11 16:03:42','0','','','0','0000-00-00 00:00:00','0','0.0000','1','3','0','127.0.0.1','0');

INSERT IGNORE INTO hdk_tbnote VALUES ('66','201301000003','64','UEhBK1BHSStQQzlpUGp3dmNEND0=','2013-01-11 17:46:16','0','0','0','0','2013-01-11 17:46:16','0','0.0000','1','3','0','127.0.0.1','0');



##
## TABELA: hdk_tbnote_attachment
##
DROP TABLE IF EXISTS hdk_tbnote_attachment ; 

CREATE TABLE IF NOT EXISTS `hdk_tbnote_attachment` (
  `idnote_attachment` bigint(10) NOT NULL AUTO_INCREMENT,
  `file_name` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`idnote_attachment`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ; 

# Dados para a tabela: hdk_tbnote_attachment

INSERT IGNORE INTO hdk_tbnote_attachment VALUES ('1','HelpDEZk - Manual - Usuario (1).pdf');



##
## TABELA: hdk_tbnote_type
##
DROP TABLE IF EXISTS hdk_tbnote_type ; 

CREATE TABLE IF NOT EXISTS `hdk_tbnote_type` (
  `idtypenote` int(4) unsigned NOT NULL,
  `description` varchar(60) DEFAULT NULL,
  `available` int(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`idtypenote`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ; 

# Dados para a tabela: hdk_tbnote_type

INSERT IGNORE INTO hdk_tbnote_type VALUES ('1','{$smarty.config.apont_tipo_visivel_user}','1');

INSERT IGNORE INTO hdk_tbnote_type VALUES ('2','{$smarty.config.apont_tipo_visivel_only_attendance}','1');

INSERT IGNORE INTO hdk_tbnote_type VALUES ('3','{$smarty.config.apont_tipo_criado__pelo_sistema}','0');

INSERT IGNORE INTO hdk_tbnote_type VALUES ('4','{$smarty.config.apont_tipo_aprovacao_solicitacao}','0');



##
## TABELA: hdk_tbpriority
##
DROP TABLE IF EXISTS hdk_tbpriority ; 

CREATE TABLE IF NOT EXISTS `hdk_tbpriority` (
  `idpriority` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `order` int(11) NOT NULL,
  `color` varchar(8) NOT NULL,
  `default` int(11) NOT NULL DEFAULT '0',
  `vip` int(11) NOT NULL DEFAULT '0',
  `limit_hours` int(11) NOT NULL DEFAULT '0',
  `limit_days` int(11) NOT NULL DEFAULT '0',
  `status` char(1) NOT NULL DEFAULT 'A',
  PRIMARY KEY (`idpriority`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC ; 

# Dados para a tabela: hdk_tbpriority

INSERT IGNORE INTO hdk_tbpriority VALUES ('1','Critical','1','#660000','0','1','1','0','A');

INSERT IGNORE INTO hdk_tbpriority VALUES ('2','High','2','#ff0000','0','0','4','0','A');

INSERT IGNORE INTO hdk_tbpriority VALUES ('3','Average','3','#ffcc33','1','0','0','1','A');

INSERT IGNORE INTO hdk_tbpriority VALUES ('4','Low','4','#66ff66','0','0','0','2','A');

INSERT IGNORE INTO hdk_tbpriority VALUES ('5','Planned','5','#3399ff','0','0','0','0','A');



##
## TABELA: hdk_tbproperty
##
DROP TABLE IF EXISTS hdk_tbproperty ; 

CREATE TABLE IF NOT EXISTS `hdk_tbproperty` (
  `idproperty` int(11) NOT NULL AUTO_INCREMENT,
  `idcategory` int(11) DEFAULT NULL,
  `idperson` int(11) DEFAULT NULL,
  `idcompany` int(11) DEFAULT NULL,
  `iddepartment` int(11) DEFAULT NULL,
  `situation` int(11) DEFAULT NULL,
  `idprovider` int(11) DEFAULT NULL,
  `idmanufacturer` int(11) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `tag_number` varchar(50) DEFAULT NULL,
  `model` varchar(100) DEFAULT NULL,
  `serial_number` varchar(50) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `purchasing_date` date DEFAULT NULL,
  `receipt_number` varchar(50) DEFAULT NULL,
  `warranty_date` date DEFAULT NULL,
  `observations` blob,
  `entry_date` date DEFAULT NULL,
  `idcostcenter` int(11) DEFAULT NULL,
  `days_attendance` int(11) DEFAULT NULL,
  `hours_attendance` int(11) DEFAULT NULL,
  `maintenance_date` date DEFAULT NULL,
  `ip_number` varchar(25) DEFAULT NULL,
  `mac_adress` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`idproperty`),
  KEY `FK2_hdk_tbproperty` (`idperson`),
  KEY `FK3_hdk_tbproperty` (`iddepartment`),
  KEY `FK1_hdk_tbproperty` (`idprovider`),
  KEY `FK5_hdk_tbproperty` (`idmanufacturer`),
  KEY `FK6_hdk_tbproperty` (`idcompany`),
  KEY `FK_hdk_tbproperty` (`idcategory`),
  KEY `FK4_hdk_tbproperty` (`idcostcenter`),
  CONSTRAINT `FK1_hdk_tbproperty` FOREIGN KEY (`idprovider`) REFERENCES `hdk_tbproperty_provider` (`idprovider`),
  CONSTRAINT `FK2_hdk_tbproperty` FOREIGN KEY (`idperson`) REFERENCES `tbperson` (`idperson`),
  CONSTRAINT `FK3_hdk_tbproperty` FOREIGN KEY (`iddepartment`) REFERENCES `hdk_tbdepartment` (`iddepartment`),
  CONSTRAINT `FK4_hdk_tbproperty` FOREIGN KEY (`idcostcenter`) REFERENCES `hdk_tbcostcenter` (`idcostcenter`),
  CONSTRAINT `FK5_hdk_tbproperty` FOREIGN KEY (`idmanufacturer`) REFERENCES `hdk_tbproperty_manufacturer` (`idmanufacturer`),
  CONSTRAINT `FK6_hdk_tbproperty` FOREIGN KEY (`idcompany`) REFERENCES `tbperson` (`idperson`),
  CONSTRAINT `FK_hdk_tbproperty` FOREIGN KEY (`idcategory`) REFERENCES `hdk_tbproperty_category` (`idcategory`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC ; 

# Dados para a tabela: hdk_tbproperty



##
## TABELA: hdk_tbproperty_category
##
DROP TABLE IF EXISTS hdk_tbproperty_category ; 

CREATE TABLE IF NOT EXISTS `hdk_tbproperty_category` (
  `idcategory` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`idcategory`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC ; 

# Dados para a tabela: hdk_tbproperty_category



##
## TABELA: hdk_tbproperty_manufacturer
##
DROP TABLE IF EXISTS hdk_tbproperty_manufacturer ; 

CREATE TABLE IF NOT EXISTS `hdk_tbproperty_manufacturer` (
  `idmanufacturer` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`idmanufacturer`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC ; 

# Dados para a tabela: hdk_tbproperty_manufacturer



##
## TABELA: hdk_tbproperty_provider
##
DROP TABLE IF EXISTS hdk_tbproperty_provider ; 

CREATE TABLE IF NOT EXISTS `hdk_tbproperty_provider` (
  `idprovider` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`idprovider`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC ; 

# Dados para a tabela: hdk_tbproperty_provider



##
## TABELA: hdk_tbreason
##
DROP TABLE IF EXISTS hdk_tbreason ; 

CREATE TABLE IF NOT EXISTS `hdk_tbreason` (
  `idreason` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `idservice` int(11) NOT NULL,
  `reason` varchar(100) NOT NULL,
  `status` char(1) NOT NULL DEFAULT 'A',
  PRIMARY KEY (`idreason`),
  KEY `FK_hdk_tbreason` (`idservice`),
  CONSTRAINT `FK_hdk_tbreason` FOREIGN KEY (`idservice`) REFERENCES `hdk_tbcore_service` (`idservice`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC ; 

# Dados para a tabela: hdk_tbreason



##
## TABELA: hdk_tbrequest
##
DROP TABLE IF EXISTS hdk_tbrequest ; 

CREATE TABLE IF NOT EXISTS `hdk_tbrequest` (
  `idrequest` int(11) NOT NULL AUTO_INCREMENT,
  `code_request` varchar(20) NOT NULL,
  `subject` varchar(100) NOT NULL,
  `description` blob NOT NULL,
  `idtype` int(11) NOT NULL,
  `iditem` int(11) NOT NULL,
  `idservice` int(11) NOT NULL,
  `idreason` int(11) unsigned DEFAULT NULL,
  `idpriority` int(11) NOT NULL,
  `idsource` int(11) NOT NULL,
  `idperson_creator` int(11) DEFAULT NULL COMMENT 'id da pessoa que abriu a solicitacao em nome de outra, s? ? preenchido nesse tipo de situa??o',
  `entry_date` datetime NOT NULL COMMENT 'data em que foi registrada no sistema',
  `service_value` float DEFAULT NULL,
  `os_number` varchar(20) DEFAULT NULL,
  `label` varchar(20) DEFAULT NULL,
  `extensions_number` varchar(2) DEFAULT '0' COMMENT 'numero de vezes que foi prorrogada',
  `idperson_juridical` mediumint(5) DEFAULT NULL COMMENT 'id da empresa do usuario',
  `serial_number` varchar(20) DEFAULT NULL,
  `idattendance_way` smallint(6) unsigned DEFAULT NULL COMMENT 'forma de atendimento',
  `expire_date` datetime DEFAULT NULL,
  `code_group` mediumint(9) DEFAULT NULL COMMENT 'esta coluna ? para dizer para qual grupo que sera designado o atendimento',
  `code_email` varchar(240) DEFAULT NULL,
  `idperson_owner` mediumint(9) NOT NULL COMMENT 'esta coluna o id de quem necessita do servi?o, que pode nao ser o mesmo que abriu',
  `idstatus` tinyint(2) DEFAULT NULL COMMENT 'aqui ficara o id do status atual da solicitacao',
  `flag_opened` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'essa coluna mostra se o atendente ja viu a solicitacao nova sem assumir',
  PRIMARY KEY (`idrequest`,`code_request`),
  UNIQUE KEY `request_code` (`code_request`),
  UNIQUE KEY `IDX_CODE_EMAIL` (`code_email`),
  KEY `FK_idtype` (`idtype`),
  KEY `FK_iditem` (`iditem`),
  KEY `FK_idservice` (`idservice`),
  KEY `FK_idreason` (`idreason`),
  KEY `FK_idpriority` (`idpriority`),
  KEY `FK_idsource` (`idsource`),
  KEY `FK_idperson_creator` (`idperson_creator`),
  CONSTRAINT `FK_iditem` FOREIGN KEY (`iditem`) REFERENCES `hdk_tbcore_item` (`iditem`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_idperson_creator` FOREIGN KEY (`idperson_creator`) REFERENCES `tbperson` (`idperson`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_idpriority` FOREIGN KEY (`idpriority`) REFERENCES `hdk_tbpriority` (`idpriority`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_idreason` FOREIGN KEY (`idreason`) REFERENCES `hdk_tbreason` (`idreason`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_idservice` FOREIGN KEY (`idservice`) REFERENCES `hdk_tbcore_service` (`idservice`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_idsource` FOREIGN KEY (`idsource`) REFERENCES `hdk_tbsource` (`idsource`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_idtype` FOREIGN KEY (`idtype`) REFERENCES `hdk_tbcore_type` (`idtype`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ; 

# Dados para a tabela: hdk_tbrequest



##
## TABELA: hdk_tbrequest_addinfo
##
DROP TABLE IF EXISTS hdk_tbrequest_addinfo ; 

CREATE TABLE IF NOT EXISTS `hdk_tbrequest_addinfo` (
  `idrequest_addinfo` int(11) NOT NULL AUTO_INCREMENT,
  `idaddinfo` int(11) NOT NULL,
  `code_request` varchar(20) NOT NULL,
  PRIMARY KEY (`idrequest_addinfo`),
  KEY `fk_addinfo_idaddinfo_hdk_tbaddinfo` (`idaddinfo`),
  KEY `fk_addinfo_code_request_hdk_tbrequest` (`code_request`),
  CONSTRAINT `fk_addinfo_code_request_hdk_tbrequest` FOREIGN KEY (`code_request`) REFERENCES `hdk_tbrequest` (`code_request`),
  CONSTRAINT `fk_addinfo_idaddinfo_hdk_tbaddinfo` FOREIGN KEY (`idaddinfo`) REFERENCES `hdk_tbaddinfo` (`idaddinfo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ; 

# Dados para a tabela: hdk_tbrequest_addinfo



##
## TABELA: hdk_tbrequest_approval
##
DROP TABLE IF EXISTS hdk_tbrequest_approval ; 

CREATE TABLE IF NOT EXISTS `hdk_tbrequest_approval` (
  `request_code` bigint(16) NOT NULL,
  `idapproval` int(10) DEFAULT NULL,
  `idnote` int(10) DEFAULT NULL,
  `idperson` int(10) NOT NULL DEFAULT '0',
  `order` int(2) NOT NULL DEFAULT '0',
  `fl_rejected` int(1) NOT NULL DEFAULT '0',
  `fl_recalculate` int(1) NOT NULL DEFAULT '0',
  `idrequestapproval` int(4) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`idrequestapproval`),
  KEY `FK_hdk_tbrequest_approval` (`idapproval`),
  CONSTRAINT `FK_hdk_tbrequest_approval` FOREIGN KEY (`idapproval`) REFERENCES `hdk_tbapproval_rule` (`idapproval`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ; 

# Dados para a tabela: hdk_tbrequest_approval



##
## TABELA: hdk_tbrequest_attachment
##
DROP TABLE IF EXISTS hdk_tbrequest_attachment ; 

CREATE TABLE IF NOT EXISTS `hdk_tbrequest_attachment` (
  `idrequest_attachment` int(11) NOT NULL AUTO_INCREMENT,
  `code_request` varchar(20) DEFAULT NULL,
  `file_name` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`idrequest_attachment`),
  KEY `FK_id_request_attachment` (`code_request`),
  CONSTRAINT `FK_hdk_tbrequest_attachment` FOREIGN KEY (`code_request`) REFERENCES `hdk_tbrequest` (`code_request`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC ; 

# Dados para a tabela: hdk_tbrequest_attachment



##
## TABELA: hdk_tbrequest_change_expire
##
DROP TABLE IF EXISTS hdk_tbrequest_change_expire ; 

CREATE TABLE IF NOT EXISTS `hdk_tbrequest_change_expire` (
  `idchangeexpire` int(11) NOT NULL AUTO_INCREMENT,
  `code_request` varchar(20) NOT NULL,
  `reason` blob,
  `idperson` int(11) NOT NULL,
  `changedate` date NOT NULL,
  PRIMARY KEY (`idchangeexpire`),
  KEY `FK_chexpire_code_request` (`code_request`),
  KEY `FK_chexpire_idperson` (`idperson`),
  CONSTRAINT `FK_chexpire_code_request` FOREIGN KEY (`code_request`) REFERENCES `hdk_tbrequest` (`code_request`),
  CONSTRAINT `FK_chexpire_idperson` FOREIGN KEY (`idperson`) REFERENCES `tbperson` (`idperson`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ; 

# Dados para a tabela: hdk_tbrequest_change_expire



##
## TABELA: hdk_tbrequest_code
##
DROP TABLE IF EXISTS hdk_tbrequest_code ; 

CREATE TABLE IF NOT EXISTS `hdk_tbrequest_code` (
  `cod_request` varchar(20) NOT NULL,
  `cod_month` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC ; 

# Dados para a tabela: hdk_tbrequest_code

INSERT IGNORE INTO hdk_tbrequest_code VALUES ('7','201208');

INSERT IGNORE INTO hdk_tbrequest_code VALUES ('109','201209');

INSERT IGNORE INTO hdk_tbrequest_code VALUES ('72','201210');

INSERT IGNORE INTO hdk_tbrequest_code VALUES ('27','201211');

INSERT IGNORE INTO hdk_tbrequest_code VALUES ('4','201301');



##
## TABELA: hdk_tbrequest_dates
##
DROP TABLE IF EXISTS hdk_tbrequest_dates ; 

CREATE TABLE IF NOT EXISTS `hdk_tbrequest_dates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code_request` varchar(20) NOT NULL,
  `forwarded_date` datetime DEFAULT NULL,
  `approval_date` datetime DEFAULT NULL,
  `finish_date` datetime DEFAULT NULL,
  `rejection_date` datetime DEFAULT NULL,
  `date_period_attendant` datetime DEFAULT NULL,
  `date_charging_period` datetime DEFAULT NULL,
  `opening_date` datetime DEFAULT NULL COMMENT 'est? ? a data de abertura, e nao a de cadastro',
  PRIMARY KEY (`id`),
  KEY `FK_hdk_tbrequest_dates` (`code_request`),
  CONSTRAINT `FK_hdk_tbrequest_dates` FOREIGN KEY (`code_request`) REFERENCES `hdk_tbrequest` (`code_request`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ; 

# Dados para a tabela: hdk_tbrequest_dates



##
## TABELA: hdk_tbrequest_evaluation
##
DROP TABLE IF EXISTS hdk_tbrequest_evaluation ; 

CREATE TABLE IF NOT EXISTS `hdk_tbrequest_evaluation` (
  `idrequestevaluation` int(11) NOT NULL AUTO_INCREMENT,
  `idevaluation` int(11) NOT NULL,
  `code_request` varchar(20) NOT NULL,
  `date` datetime DEFAULT NULL,
  PRIMARY KEY (`idrequestevaluation`),
  KEY `FK_hdk_tbrequest_evaluation` (`idevaluation`),
  KEY `CODE_REQUEST_IDX` (`code_request`),
  CONSTRAINT `FK_hdk_tbrequest_evaluation` FOREIGN KEY (`idevaluation`) REFERENCES `hdk_tbevaluation` (`idevaluation`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC ; 

# Dados para a tabela: hdk_tbrequest_evaluation



##
## TABELA: hdk_tbrequest_has_tbproperty
##
DROP TABLE IF EXISTS hdk_tbrequest_has_tbproperty ; 

CREATE TABLE IF NOT EXISTS `hdk_tbrequest_has_tbproperty` (
  `idrequest` int(11) DEFAULT NULL,
  `idproperty` int(11) DEFAULT NULL,
  `label` varchar(20) DEFAULT NULL,
  `serial_number` varchar(20) DEFAULT NULL,
  KEY `FK_idrequest_property` (`idrequest`),
  KEY `FK_idproperty_request` (`idproperty`),
  CONSTRAINT `FK_idproperty_request` FOREIGN KEY (`idproperty`) REFERENCES `hdk_tbproperty` (`idproperty`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_idrequest_property` FOREIGN KEY (`idrequest`) REFERENCES `hdk_tbrequest` (`idrequest`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ; 

# Dados para a tabela: hdk_tbrequest_has_tbproperty



##
## TABELA: hdk_tbrequest_in_charge
##
DROP TABLE IF EXISTS hdk_tbrequest_in_charge ; 

CREATE TABLE IF NOT EXISTS `hdk_tbrequest_in_charge` (
  `idrequest_in_charge` int(11) NOT NULL AUTO_INCREMENT,
  `code_request` varchar(20) NOT NULL,
  `id_in_charge` int(11) NOT NULL,
  `type` varchar(1) NOT NULL COMMENT 'aqui sera o tipo de respons?vel, analista ou grupo de atendimento, definido por O(operator) ou G(group)',
  `ind_in_charge` varchar(1) DEFAULT NULL,
  `ind_repass` varchar(1) NOT NULL DEFAULT 'N',
  `ind_track` smallint(1) DEFAULT '0' COMMENT 'Aqui vai ficar marcado se o grupo continua vizualizando ap?s algu?m assumir',
  `ind_operator_aux` smallint(1) DEFAULT '0',
  PRIMARY KEY (`idrequest_in_charge`),
  KEY `FK_idrequest` (`code_request`),
  KEY `FK_id_person_in_charge` (`id_in_charge`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ; 

# Dados para a tabela: hdk_tbrequest_in_charge



##
## TABELA: hdk_tbrequest_log
##
DROP TABLE IF EXISTS hdk_tbrequest_log ; 

CREATE TABLE IF NOT EXISTS `hdk_tbrequest_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cod_request` varchar(20) NOT NULL,
  `date` datetime NOT NULL,
  `idstatus` mediumint(9) NOT NULL,
  `idperson` mediumint(9) NOT NULL,
  `reopened` char(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `NewIndex1` (`cod_request`),
  CONSTRAINT `FK_hdk_tbrequest_log` FOREIGN KEY (`cod_request`) REFERENCES `hdk_tbrequest` (`code_request`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC ; 

# Dados para a tabela: hdk_tbrequest_log



##
## TABELA: hdk_tbrequest_repassed
##
DROP TABLE IF EXISTS hdk_tbrequest_repassed ; 

CREATE TABLE IF NOT EXISTS `hdk_tbrequest_repassed` (
  `date` datetime NOT NULL,
  `idnote` int(10) unsigned NOT NULL,
  `code_request` varchar(20) NOT NULL,
  KEY `FK_hdk_tbrequest_repassed` (`code_request`),
  KEY `FK_hdk_tbrequest_repassed2` (`idnote`),
  CONSTRAINT `FK_hdk_tbrequest_repassed` FOREIGN KEY (`code_request`) REFERENCES `hdk_tbrequest` (`code_request`),
  CONSTRAINT `FK_hdk_tbrequest_repassed2` FOREIGN KEY (`idnote`) REFERENCES `hdk_tbnote` (`idnote`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC ; 

# Dados para a tabela: hdk_tbrequest_repassed



##
## TABELA: hdk_tbrequest_times
##
DROP TABLE IF EXISTS hdk_tbrequest_times ; 

CREATE TABLE IF NOT EXISTS `hdk_tbrequest_times` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `CODE_REQUEST` varchar(20) NOT NULL,
  `MIN_OPENING_TIME` float NOT NULL,
  `MIN_ATTENDANCE_TIME` float NOT NULL,
  `MIN_EXPENDED_TIME` float NOT NULL,
  `MIN_TELEPHONE_TIME` float NOT NULL,
  `MIN_CLOSURE_TIME` float NOT NULL,
  PRIMARY KEY (`ID`,`CODE_REQUEST`),
  KEY `FK_hdk_tbrequest_times` (`CODE_REQUEST`),
  CONSTRAINT `FK_hdk_tbrequest_times` FOREIGN KEY (`CODE_REQUEST`) REFERENCES `hdk_tbrequest` (`code_request`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC ; 

# Dados para a tabela: hdk_tbrequest_times



##
## TABELA: hdk_tbsource
##
DROP TABLE IF EXISTS hdk_tbsource ; 

CREATE TABLE IF NOT EXISTS `hdk_tbsource` (
  `idsource` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `icon` varchar(100) NOT NULL,
  PRIMARY KEY (`idsource`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC ; 

# Dados para a tabela: hdk_tbsource

INSERT IGNORE INTO hdk_tbsource VALUES ('1','Helpdezk','hpdx.ico');

INSERT IGNORE INTO hdk_tbsource VALUES ('2','Telephone','fone.ico');

INSERT IGNORE INTO hdk_tbsource VALUES ('3','Email','mail.ico');

INSERT IGNORE INTO hdk_tbsource VALUES ('8','Webservice','web.ico');



##
## TABELA: hdk_tbstatus
##
DROP TABLE IF EXISTS hdk_tbstatus ; 

CREATE TABLE IF NOT EXISTS `hdk_tbstatus` (
  `idstatus` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(40) NOT NULL,
  `user_view` varchar(40) NOT NULL,
  `color` varchar(8) NOT NULL,
  `status` char(1) NOT NULL DEFAULT 'A',
  `idstatus_source` int(2) NOT NULL DEFAULT '3',
  PRIMARY KEY (`idstatus`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC ; 

# Dados para a tabela: hdk_tbstatus

INSERT IGNORE INTO hdk_tbstatus VALUES ('1','New','1 - Waiting for attendance','#FF0000','A','1');

INSERT IGNORE INTO hdk_tbstatus VALUES ('2','Repassed','2 - Transfered to another operator/group','#6600cc','A','1');

INSERT IGNORE INTO hdk_tbstatus VALUES ('3','On Attendance','3 - On Attendance','#CC9900','A','3');

INSERT IGNORE INTO hdk_tbstatus VALUES ('4','Waiting for Approval','4 - Waiting my approval','#FF0000','A','4');

INSERT IGNORE INTO hdk_tbstatus VALUES ('5','Finished','5 - Finished','#990099','A','5');

INSERT IGNORE INTO hdk_tbstatus VALUES ('6','Rejected','6 - Rejected','#00ff66','A','6');

INSERT IGNORE INTO hdk_tbstatus VALUES ('11','Canceled by user','7 - Canceled by me','#00ff66','A','3');



##
## TABELA: hdk_tbtemplate_email
##
DROP TABLE IF EXISTS hdk_tbtemplate_email ; 

CREATE TABLE IF NOT EXISTS `hdk_tbtemplate_email` (
  `idtemplate` int(3) unsigned NOT NULL DEFAULT '0',
  `name` varchar(100) DEFAULT NULL,
  `description` blob,
  PRIMARY KEY (`idtemplate`),
  KEY `IX_TEMPLATE_EMAIL_CD_TEMPL` (`idtemplate`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ; 

# Dados para a tabela: hdk_tbtemplate_email

INSERT IGNORE INTO hdk_tbtemplate_email VALUES ('1','Request assumed by operator # $REQUEST','UEhBK1BHWnZiblFnWm1GalpUMGlRWEpwWVd3c0lFaGxiSFpsZEdsallTd2djMkZ1Y3kxelpYSnBaaUlnYzJsNlpUMGlNaUkrVkdobElISmxjWFZsYzNRZ2JuVnRZbVZ5SUR4emRISnZibWMrUEhVK0pGSkZVVlZGVTFROEwzVStQQzl6ZEhKdmJtYytJSGRoY3lCaGMzTjFiV1ZrSUdsdUlDUkJVMU5WVFVVOFluSXZQZzBLUEhOMGNtOXVaejVTWlhGMVpYTjBaWEk4TDNOMGNtOXVaejQ2SUNSU1JWRlZSVk5VUlZJOFluSXZQZzBLUEdadmJuUWdabUZqWlQwaVFYSnBZV3dzSUVobGJIWmxkR2xqWVN3Z2MyRnVjeTF6WlhKcFppSWdjMmw2WlQwaU1pSStQSE4wY205dVp6NVRkV0pxWldOME9qd3ZjM1J5YjI1blBpWnVZbk53T3ladVluTndPeVp1WW5Od095WnVZbk53T3ladVluTndPeUFrVTFWQ1NrVkRWRHhpY2k4K0RRbzhjM1J5YjI1blBrVnVkSEo1SUVSaGRHVTZQQzl6ZEhKdmJtYytJQ1JTUlVOUFVrUThZbkl2UGcwS1BDOW1iMjUwUGp4emRISnZibWMrVDNCbGNtRjBiM0lnU1c0Z1EyaGhjbWRsT2p3dmMzUnliMjVuUGlBa1NVNURTRUZTUjBVOFluSXZQZzBLUEM5bWIyNTBQand2Y0Q0TkNqeHdQanhtYjI1MElHWmhZMlU5SWtGeWFXRnNMQ0JJWld4MlpYUnBZMkVzSUhOaGJuTXRjMlZ5YVdZaUlITnBlbVU5SWpJaVBqeHpkSEp2Ym1jK1JHVnpZM0pwY0hScGIyNDZQQzl6ZEhKdmJtYytJQ1JFUlZORFVrbFFWRWxQVGlBOEwyWnZiblErUEM5d1BnMEtQSEErUEdadmJuUWdabUZqWlQwaVFYSnBZV3dzSUVobGJIWmxkR2xqWVN3Z2MyRnVjeTF6WlhKcFppSWdjMmw2WlQwaU1pSStKRXhKVGt0ZlZWTkZVand2Wm05dWRENDhMM0ErRFFvOGNENDhabTl1ZENCbVlXTmxQU0pCY21saGJDSWdjMmw2WlQwaU1pSStQSE4wY205dVp6NU9iM1JsY3pvOEwzTjBjbTl1Wno0OFluSXZQZzBLSkU1VVgxVlRSVkk4TDJadmJuUStQQzl3UGc9PQ==');

INSERT IGNORE INTO hdk_tbtemplate_email VALUES ('2','Request closed # $REQUEST','UEhBK0NnazhabTl1ZENCbVlXTmxQU0pCY21saGJDd2dTR1ZzZG1WMGFXTmhMQ0J6WVc1ekxYTmxjbWxtSWlCemFYcGxQU0l5SWo1RVpXRnlJRk55TGloTmN5NHBQSE4wY205dVp6NGdQQzl6ZEhKdmJtYytKRkpGVVZWRlUxUkZVanh6ZEhKdmJtYytMQ0E4TDNOMGNtOXVaejUwYUdVZ2NtVnhkV1Z6ZENCdWRXMWlaWElnSkZKRlVWVkZVMVFtYm1KemNEc2dkMkZ6SUdOc2IzTmxaQ0JwYmlBa1JrbE9TVk5JWDBSQlZFVThjM1J5YjI1blBpNDhMM04wY205dVp6NDhMMlp2Ym5RK1BDOXdQZ284Y0Q0S0NUeG1iMjUwSUdaaFkyVTlJa0Z5YVdGc0xDQklaV3gyWlhScFkyRXNJSE5oYm5NdGMyVnlhV1lpSUhOcGVtVTlJaklpUGp4emRISnZibWMrVW1WeGRXVnpkR1Z5UEM5emRISnZibWMrT2lBa1VrVlJWVVZUVkVWU1BHSnlJQzgrQ2drOFptOXVkQ0JtWVdObFBTSkJjbWxoYkN3Z1NHVnNkbVYwYVdOaExDQnpZVzV6TFhObGNtbG1JaUJ6YVhwbFBTSXlJajQ4YzNSeWIyNW5QbE4xWW1wbFkzUTZQQzl6ZEhKdmJtYytKbTVpYzNBN0ptNWljM0E3Sm01aWMzQTdKbTVpYzNBN0ptNWljM0E3SUNSVFZVSktSVU5VUEdKeUlDOCtDZ2s4YzNSeWIyNW5Qa1Z1ZEhKNUlFUmhkR1U2UEM5emRISnZibWMrSUNSU1JVTlBVa1E4TDJadmJuUStQR0p5SUM4K0NnazhjM1J5YjI1blBrOXdaWEpoZEc5eUlFbHVJRU5vWVhKblpUbzhMM04wY205dVp6NGdKRWxPUTBoQlVrZEZQQzltYjI1MFBqd3ZjRDRLUEhBK0NnazhabTl1ZENCbVlXTmxQU0pCY21saGJDd2dTR1ZzZG1WMGFXTmhMQ0J6WVc1ekxYTmxjbWxtSWlCemFYcGxQU0l5SWo0a1RFbE9TMTlWVTBWU1BDOW1iMjUwUGp3dmNENEtQSEErQ2drOFptOXVkQ0JtWVdObFBTSkJjbWxoYkN3Z1NHVnNkbVYwYVdOaExDQnpZVzV6TFhObGNtbG1JaUJ6YVhwbFBTSXlJajQ4YzNSeWIyNW5Qa1JsYzJOeWFYQjBhVzl1T2p3dmMzUnliMjVuUGlBa1JFVlRRMUpKVUZSSlQwNGdQQzltYjI1MFBqd3ZjRDRLUEhBK0NnazhabTl1ZENCbVlXTmxQU0pCY21saGJDSWdjMmw2WlQwaU1pSStQSE4wY205dVp6NU9iM1JsY3pvOEwzTjBjbTl1Wno0OFluSWdMejRLQ1NST1ZGOVZVMFZTUEM5bWIyNTBQand2Y0Q0Sw==');

INSERT IGNORE INTO hdk_tbtemplate_email VALUES ('3','Request rejected # $REQUEST','UEdadmJuUWdjMmw2WlQwaU1pSStQR1p2Ym5RZ1ptRmpaVDBpUVhKcFlXd2lQbFJvWlNCeVpYRjFaWE4wSUc1MWJXSmxjaUE4YzNSeWIyNW5QangxUGlSU1JWRlZSVk5VUEM5MVBqd3ZjM1J5YjI1blBpQjNZWE1nY21WcVpXTjBaV1FnYVc0Z0pGSkZTa1ZEVkVsUFRpNDhZbkl2UGdvOGMzUnliMjVuUGxKbGNYVmxjM1JsY2p3dmMzUnliMjVuUGpvZ0pGSkZVVlZGVTFSRlVqeGljaTgrQ2p4bWIyNTBJR1poWTJVOUlrRnlhV0ZzTENCSVpXeDJaWFJwWTJFc0lITmhibk10YzJWeWFXWWlJSE5wZW1VOUlqSWlQanh6ZEhKdmJtYytVM1ZpYW1WamREbzhMM04wY205dVp6NG1ibUp6Y0RzbWJtSnpjRHNtYm1KemNEc21ibUp6Y0RzbWJtSnpjRHNnSkZOVlFrcEZRMVE4WW5JdlBnbzhjM1J5YjI1blBrVnVkSEo1SUVSaGRHVTZQQzl6ZEhKdmJtYytJQ1JTUlVOUFVrUThZbkl2UGdvOEwyWnZiblErUEhOMGNtOXVaejVTWldwbFkzUmxaQ0JpZVRvOEwzTjBjbTl1Wno0Z0pGVlRSVkk4WW5JdlBnbzhjM1J5YjI1blBrUmxjMk55YVhCMGFXOXVPand2YzNSeWIyNW5QaUFrUkVWVFExSkpVRlJKVDA0Z1BDOW1iMjUwUGp3dlptOXVkRDRLUEhBK1BHWnZiblFnWm1GalpUMGlRWEpwWVd3c0lFaGxiSFpsZEdsallTd2djMkZ1Y3kxelpYSnBaaUlnYzJsNlpUMGlNaUkrSkV4SlRrdGZWVk5GVWp4aElHaHlaV1k5SWlSTVNVNUxYMVZUUlZJaUlIUmhjbWRsZEQwaWFHVnNjR1JsYzJzaVBqd3ZZVDQ4TDJadmJuUStQQzl3UGdvOGNENDhabTl1ZENCbVlXTmxQU0pCY21saGJDSWdjMmw2WlQwaU1pSStQSE4wY205dVp6NU9iM1JsY3p3dmMzUnliMjVuUGp4aWNpOCtDaVJPVkY5VlUwVlNQQzltYjI1MFBqd3ZjRDQ9');

INSERT IGNORE INTO hdk_tbtemplate_email VALUES ('4','Request evaluated # $REQUEST','UEhBK0NnazhabTl1ZENCemFYcGxQU0l5SWo0OFptOXVkQ0JtWVdObFBTSkJjbWxoYkNJK1RpNGdQSE4wY205dVp6NDhkVDRrVWtWUlZVVlRWRHd2ZFQ0OEwzTjBjbTl1Wno0Z0xqeGljaUF2UGdvSlBITjBjbTl1Wno1VGRXSnFaV04wT2p3dmMzUnliMjVuUGlBa1UxVkNTa1ZEVkR4aWNpQXZQZ29KUEhOMGNtOXVaejVWYzJWeU9qd3ZjM1J5YjI1blBpQWtVa1ZSVlVWVFZFVlNQR0p5SUM4K0NnazhjM1J5YjI1blBrVnVkSEo1SUVSaGRHVTZQQzl6ZEhKdmJtYytJQ1JTUlVOUFVrUThZbklnTHo0S0NUeHpkSEp2Ym1jK1JHVnpZM0pwY0hScGIyNDZQQzl6ZEhKdmJtYytJQ1JFUlZORFVrbFFWRWxQVGp4aWNpQXZQZ29KUEhOMGNtOXVaejVKYmlCRGFHRnlaMlU2UEM5emRISnZibWMrSUNSSlRrTklRVkpIUlR3dlptOXVkRDQ4TDJadmJuUStQQzl3UGdvOGNENEtDVHh6ZEhKdmJtYytSWFpoYkhWaGRHbHZiam9nUEM5emRISnZibWMrSkVWV1FVeFZRVlJKVDA0OEwzQStDanh3UGdvSlBHWnZiblFnYzJsNlpUMGlNaUkrUEhOMGNtOXVaejVRYUc5dVpUbzhMM04wY205dVp6NGdKRkJJVDA1RlBDOW1iMjUwUGp4bWIyNTBJSE5wZW1VOUlqSWlQanh6ZEhKdmJtYytJQzBnUW5KaGJtTm9Pand2YzNSeWIyNW5QaUFrUWxKQlRrTklQQzltYjI1MFBqd3ZjRDRLUEhBK0NnazhabTl1ZENCbVlXTmxQU0pCY21saGJDd2dTR1ZzZG1WMGFXTmhMQ0J6WVc1ekxYTmxjbWxtSWlCemFYcGxQU0l5SWo0a1RFbE9TMTlQVUVWU1FWUlBVand2Wm05dWRENDhMM0ErQ2p4d1Bnb0pQR1p2Ym5RZ2MybDZaVDBpTWlJK1BHWnZiblFnWm1GalpUMGlRWEpwWVd3aVBqeHpkSEp2Ym1jK1RtOTBaWE02UEM5emRISnZibWMrUEdKeUlDOCtDZ2trVGxSZlQxQkZVa0ZVVDFJOEwyWnZiblErUEM5bWIyNTBQand2Y0Q0Sw==');

INSERT IGNORE INTO hdk_tbtemplate_email VALUES ('13','New note added to the request # $REQUEST','UEhBK1BHWnZiblFnYzJsNlpUMGlNaUkrUEdadmJuUWdabUZqWlQwaVFYSnBZV3dpUGxSb1pTQnlaWEYxWlhOMElHNTFiV0psY2lBOGMzUnliMjVuUGp4MVBpUlNSVkZWUlZOVVBDOTFQand2YzNSeWIyNW5QaVp1WW5Od08zSmxZMlZwZG1Wa0lHRnViM1JvWlhJZ2JtOTBaUzQ4WW5JdlBnMEtQSE4wY205dVp6NVNaWEYxWlhOMFpYSThMM04wY205dVp6NDZJQ1JTUlZGVlJWTlVSVkk4WW5JdlBnMEtQR1p2Ym5RZ1ptRmpaVDBpUVhKcFlXd3NJRWhsYkhabGRHbGpZU3dnYzJGdWN5MXpaWEpwWmlJZ2MybDZaVDBpTWlJK1BITjBjbTl1Wno1VGRXSnFaV04wT2p3dmMzUnliMjVuUGladVluTndPeVp1WW5Od095WnVZbk53T3ladVluTndPeVp1WW5Od095QWtVMVZDU2tWRFZEeGljaTgrRFFvOGMzUnliMjVuUGtWdWRISjVJRVJoZEdVNlBDOXpkSEp2Ym1jK0lDUlNSVU5QVWtROFluSXZQZzBLUEM5bWIyNTBQand2Wm05dWRENDhMMlp2Ym5RK1BHWnZiblFnYzJsNlpUMGlNaUkrUEdadmJuUWdabUZqWlQwaVFYSnBZV3dpUGp4emRISnZibWMrVDNCbGNtRjBiM0lnU1c0Z1EyaGhjbWRsT2p3dmMzUnliMjVuUGlBa1NVNURTRUZTUjBVOFluSXZQZzBLUEhOMGNtOXVaejVFWlhOamNtbHdkR2x2YmpvOEwzTjBjbTl1Wno0Z0pFUkZVME5TU1ZCVVNVOU9KbTVpYzNBN1BDOW1iMjUwUGp3dlptOXVkRDRtYm1KemNEczhMM0ErRFFvOGNENDhabTl1ZENCbVlXTmxQU0pCY21saGJDd2dTR1ZzZG1WMGFXTmhMQ0J6WVc1ekxYTmxjbWxtSWlCemFYcGxQU0l5SWo0a1RFbE9TMTlWVTBWU1BHRWdhSEpsWmowaUpFeEpUa3RmVlZORlVpSWdkR0Z5WjJWMFBTSm9aV3h3WkdWemF5SStQQzloUGp3dlptOXVkRDQ4TDNBK0RRbzhjRDQ4Wm05dWRDQm1ZV05sUFNKQmNtbGhiQ0lnYzJsNlpUMGlNaUkrUEhOMGNtOXVaejVPYjNSbGN6bzhMM04wY205dVp6NDhZbkl2UGcwS0pFNVVYMVZUUlZJOEwyWnZiblErUEM5d1BnPT0=');

INSERT IGNORE INTO hdk_tbtemplate_email VALUES ('16','New request recorded # $REQUEST','UEhBK0NnazhabTl1ZENCemFYcGxQU0l5SWo0OFptOXVkQ0JtWVdObFBTSkJjbWxoYkNJK1RpNGdQSE4wY205dVp6NDhkVDRrVWtWUlZVVlRWRHd2ZFQ0OEwzTjBjbTl1Wno0Z0xqeGljaUF2UGdvSlBITjBjbTl1Wno1VGRXSnFaV04wT2p3dmMzUnliMjVuUGlBa1UxVkNTa1ZEVkR4aWNpQXZQZ29KUEhOMGNtOXVaejVWYzJWeU9qd3ZjM1J5YjI1blBpQWtVa1ZSVlVWVFZFVlNQR0p5SUM4K0NnazhjM1J5YjI1blBrVnVkSEo1SUVSaGRHVTZQQzl6ZEhKdmJtYytJQ1JTUlVOUFVrUThZbklnTHo0S0NUeHpkSEp2Ym1jK1JHVnpZM0pwY0hScGIyNDZQQzl6ZEhKdmJtYytJQ1JFUlZORFVrbFFWRWxQVGp4aWNpQXZQZ29KUEhOMGNtOXVaejVKYmlCRGFHRnlaMlU2UEM5emRISnZibWMrSUNSSlRrTklRVkpIUlR3dlptOXVkRDQ4TDJadmJuUStQQzl3UGdvOGNENEtDVHhtYjI1MElITnBlbVU5SWpJaVBqeHpkSEp2Ym1jK1VHaHZibVU2UEM5emRISnZibWMrSUNSUVNFOU9SVHd2Wm05dWRENDhabTl1ZENCemFYcGxQU0l5SWo0OGMzUnliMjVuUGlBdElFSnlZVzVqYURvOEwzTjBjbTl1Wno0Z0pFSlNRVTVEU0R3dlptOXVkRDQ4TDNBK0NqeHdQZ29KUEdadmJuUWdabUZqWlQwaVFYSnBZV3dzSUVobGJIWmxkR2xqWVN3Z2MyRnVjeTF6WlhKcFppSWdjMmw2WlQwaU1pSStKRXhKVGt0ZlQxQkZVa0ZVVDFJOEwyWnZiblErUEM5d1BnbzhjRDRLQ1R4bWIyNTBJSE5wZW1VOUlqSWlQanhtYjI1MElHWmhZMlU5SWtGeWFXRnNJajQ4YzNSeWIyNW5QazV2ZEdWek9qd3ZjM1J5YjI1blBqeGljaUF2UGdvSkpFNVVYMDlRUlZKQlZFOVNQQzltYjI1MFBqd3ZabTl1ZEQ0OEwzQStDZz09');

INSERT IGNORE INTO hdk_tbtemplate_email VALUES ('43','A new note was recorded by an user # $REQUEST','UEhBK0NnazhabTl1ZENCemFYcGxQU0l5SWo0OFptOXVkQ0JtWVdObFBTSkJjbWxoYkNJK1ZHaGxJSEpsY1hWbGMzUWdiblZ0WW1WeU9pQThjM1J5YjI1blBqeDFQaVJTUlZGVlJWTlVQQzkxUGp3dmMzUnliMjVuUGladVluTndPM0psWTJWcGRtVmtJR0Z1YjNSb1pYSWdibTkwWlM0OEwyWnZiblErUEM5bWIyNTBQanhpY2lBdlBnb0pKbTVpYzNBN1BDOXdQZ284Y0Q0S0NUeG1iMjUwSUhOcGVtVTlJaklpUGp4bWIyNTBJR1poWTJVOUlrRnlhV0ZzSWo0OGMzQmhiaUJ6ZEhsc1pUMGlZMjlzYjNJNkl6QXdNREF3TURzaVBqeHpkSEp2Ym1jK1VtVnhkV1Z6ZEdWeVBDOXpkSEp2Ym1jK09qd3ZjM0JoYmo0Z0pGSkZVVlZGVTFSRlVqeGljaUF2UGdvSlBHWnZiblFnWm1GalpUMGlRWEpwWVd3c0lFaGxiSFpsZEdsallTd2djMkZ1Y3kxelpYSnBaaUlnYzJsNlpUMGlNaUkrUEhOMGNtOXVaejVUZFdKcVpXTjBPand2YzNSeWIyNW5QaVp1WW5Od095WnVZbk53T3ladVluTndPeVp1WW5Od095WnVZbk53T3lBa1UxVkNTa1ZEVkR4aWNpQXZQZ29KUEhOMGNtOXVaejVGYm5SeWVTQkVZWFJsT2p3dmMzUnliMjVuUGlBa1VrVkRUMUpFUEM5bWIyNTBQand2Wm05dWRENDhMMlp2Ym5RK1BHSnlJQzgrQ2drOFptOXVkQ0J6YVhwbFBTSXlJajQ4Wm05dWRDQm1ZV05sUFNKQmNtbGhiQ0krUEhOMGNtOXVaejVQY0dWeVlYUnZjaUJKYmlCRGFHRnlaMlU2UEM5emRISnZibWMrSUNSSlRrTklRVkpIUlR4aWNpQXZQZ29KUEhOMGNtOXVaejVFWlhOamNta21ZMk5sWkdsc095WmhkR2xzWkdVN2J6bzhMM04wY205dVp6NGdKRVJGVTBOU1NWQlVTVTlPSUR3dlptOXVkRDRnUEM5bWIyNTBQand2Y0Q0S1BIQStDZ2s4Wm05dWRDQnphWHBsUFNJeUlqNDhabTl1ZENCbVlXTmxQU0pCY21saGJDd2dTR1ZzZG1WMGFXTmhMQ0J6WVc1ekxYTmxjbWxtSWlCemFYcGxQU0l5SWo0a1RFbE9TMTlQVUVWU1FWUlBVand2Wm05dWRENDhMMlp2Ym5RK1BDOXdQZ284Y0Q0S0NTWnVZbk53T3p3dmNENEtQSEErQ2drOFptOXVkQ0JtWVdObFBTSkJjbWxoYkNJZ2MybDZaVDBpTWlJK1BITjBjbTl1Wno1T2IzUmxjem84TDNOMGNtOXVaejQ4WW5JZ0x6NEtDU1JPVkY5VlUwVlNQQzltYjI1MFBqd3ZjRDRL');

INSERT IGNORE INTO hdk_tbtemplate_email VALUES ('47','Request reopened # $REQUEST','UEhBK0RRb0pQR1p2Ym5RZ1ptRmpaVDBpUVhKcFlXd3NJRWhsYkhabGRHbGpZU3dnYzJGdWN5MXpaWEpwWmlJZ2MybDZaVDBpTWlJK1JHVmhjaUJUY2k0b1RYTXVLVHh6ZEhKdmJtYytJRHd2YzNSeWIyNW5QaVJKVGtOSVFWSkhSVHh6ZEhKdmJtYytMQ0E4TDNOMGNtOXVaejUwYUdVZ2NtVnhkV1Z6ZENCdWRXMWlaWElnSkZKRlVWVkZVMVFtYm1KemNEc2dkMkZ6SUhKbGIzQmxibVZrSUdsdUlDUkVRVlJGUEhOMGNtOXVaejR1UEM5emRISnZibWMrUEM5bWIyNTBQand2Y0Q0TkNqeHdQZzBLQ1R4bWIyNTBJR1poWTJVOUlrRnlhV0ZzTENCSVpXeDJaWFJwWTJFc0lITmhibk10YzJWeWFXWWlJSE5wZW1VOUlqSWlQanh6ZEhKdmJtYytVbVZ4ZFdWemRHVnlQQzl6ZEhKdmJtYytPaUFrVWtWUlZVVlRWRVZTUEdKeUlDOCtEUW9KUEdadmJuUWdabUZqWlQwaVFYSnBZV3dzSUVobGJIWmxkR2xqWVN3Z2MyRnVjeTF6WlhKcFppSWdjMmw2WlQwaU1pSStQSE4wY205dVp6NVRkV0pxWldOME9qd3ZjM1J5YjI1blBpWnVZbk53T3ladVluTndPeVp1WW5Od095WnVZbk53T3ladVluTndPeUFrVTFWQ1NrVkRWRHhpY2lBdlBnMEtDVHh6ZEhKdmJtYytSVzUwY25rZ1JHRjBaVG84TDNOMGNtOXVaejRnSkZKRlEwOVNSRHd2Wm05dWRENDhZbklnTHo0TkNnazhjM1J5YjI1blBrOXdaWEpoZEc5eUlFbHVJRU5vWVhKblpUbzhMM04wY205dVp6NGdKRWxPUTBoQlVrZEZQQzltYjI1MFBqd3ZjRDROQ2p4d1BnMEtDVHhtYjI1MElHWmhZMlU5SWtGeWFXRnNMQ0JJWld4MlpYUnBZMkVzSUhOaGJuTXRjMlZ5YVdZaUlITnBlbVU5SWpJaVBpUk1TVTVMWDFWVFJWSThMMlp2Ym5RK1BDOXdQZzBLUEhBK0RRb0pQR1p2Ym5RZ1ptRmpaVDBpUVhKcFlXd3NJRWhsYkhabGRHbGpZU3dnYzJGdWN5MXpaWEpwWmlJZ2MybDZaVDBpTWlJK1BITjBjbTl1Wno1RVpYTmpjbWx3ZEdsdmJqbzhMM04wY205dVp6NGdKRVJGVTBOU1NWQlVTVTlPSUR3dlptOXVkRDQ4TDNBK0RRbzhjRDROQ2drOFptOXVkQ0JtWVdObFBTSkJjbWxoYkNJZ2MybDZaVDBpTWlJK1BITjBjbTl1Wno1T2IzUmxjem84TDNOMGNtOXVaejQ4WW5JZ0x6NE5DZ2trVGxSZlZWTkZVand2Wm05dWRENDhMM0ErRFFvPQ==');

INSERT IGNORE INTO hdk_tbtemplate_email VALUES ('67','Request repassed # $REQUEST','UEhBK0RRb0pQR1p2Ym5RZ2MybDZaVDBpTWlJK1BHWnZiblFnWm1GalpUMGlRWEpwWVd3aVBrNHVJRHh6ZEhKdmJtYytQSFUrSkV4SlRrdGZUMUJGVWtGVVQxSThMM1UrUEM5emRISnZibWMrTGp4aWNpQXZQZzBLQ1R4emRISnZibWMrVTNWaWFtVmpkRG84TDNOMGNtOXVaejRnSkZOVlFrcEZRMVE4WW5JZ0x6NE5DZ2s4YzNSeWIyNW5QbFZ6WlhJNlBDOXpkSEp2Ym1jK0lDUlNSVkZWUlZOVVJWSThZbklnTHo0TkNnazhjM1J5YjI1blBrVnVkSEo1SUVSaGRHVTZQQzl6ZEhKdmJtYytJQ1JTUlVOUFVrUThZbklnTHo0TkNnazhjM1J5YjI1blBrUmxjMk55YVhCMGFXOXVPand2YzNSeWIyNW5QaUFrUkVWVFExSkpVRlJKVDA0OFluSWdMejROQ2drOGMzUnliMjVuUGtsdUlFTm9ZWEpuWlRvOEwzTjBjbTl1Wno0Z0pFbE9RMGhCVWtkRlBDOW1iMjUwUGp3dlptOXVkRDQ4TDNBK0RRbzhjRDROQ2drOFptOXVkQ0J6YVhwbFBTSXlJajQ4YzNSeWIyNW5QbEJvYjI1bE9qd3ZjM1J5YjI1blBpQWtVRWhQVGtVOEwyWnZiblErUEdadmJuUWdjMmw2WlQwaU1pSStQSE4wY205dVp6NGdMU0JDY21GdVkyZzZQQzl6ZEhKdmJtYytJQ1JDVWtGT1EwZzhMMlp2Ym5RK1BDOXdQZzBLUEhBK0RRb0pQR1p2Ym5RZ2MybDZaVDBpTWlJK1BHWnZiblFnWm1GalpUMGlRWEpwWVd3aVBqeHpkSEp2Ym1jK1RtOTBaWE02UEM5emRISnZibWMrUEdKeUlDOCtEUW9KSkU1VVgwOVFSVkpCVkU5U1BDOW1iMjUwUGp3dlptOXVkRDQ4TDNBK0RRbz0=');

INSERT IGNORE INTO hdk_tbtemplate_email VALUES ('70','New request to approve # $REQUEST','UEhBK0RRb0pQR1p2Ym5RZ2MybDZaVDBpTWlJK1BHWnZiblFnWm1GalpUMGlRWEpwWVd3aVBrNHVJRHh6ZEhKdmJtYytQSFUrSkV4SlRrdGZUMUJGVWtGVVQxSThMM1UrUEM5emRISnZibWMrSUM0OFluSWdMejROQ2drOGMzUnliMjVuUGxOMVltcGxZM1E2UEM5emRISnZibWMrSUNSVFZVSktSVU5VUEdKeUlDOCtEUW9KUEhOMGNtOXVaejVWYzJWeU9qd3ZjM1J5YjI1blBpQWtVa1ZSVlVWVFZFVlNQR0p5SUM4K0RRb0pQSE4wY205dVp6NUZiblJ5ZVNCRVlYUmxPand2YzNSeWIyNW5QaUFrVWtWRFQxSkVQR0p5SUM4K0RRb0pQSE4wY205dVp6NUVaWE5qY21sd2RHbHZiam84TDNOMGNtOXVaejRnSkVSRlUwTlNTVkJVU1U5T1BHSnlJQzgrRFFvSlBITjBjbTl1Wno1SmJpQkRhR0Z5WjJVNlBDOXpkSEp2Ym1jK0lDUkpUa05JUVZKSFJUd3ZabTl1ZEQ0OEwyWnZiblErUEM5d1BnMEtQSEErRFFvSlBHWnZiblFnYzJsNlpUMGlNaUkrUEhOMGNtOXVaejVRYUc5dVpUbzhMM04wY205dVp6NGdKRkJJVDA1RlBDOW1iMjUwUGp4bWIyNTBJSE5wZW1VOUlqSWlQanh6ZEhKdmJtYytJQzBnUW5KaGJtTm9Pand2YzNSeWIyNW5QaUFrUWxKQlRrTklQQzltYjI1MFBqd3ZjRDROQ2c9PQ==');

INSERT IGNORE INTO hdk_tbtemplate_email VALUES ('80','Request rejected # $REQUEST','UEhBK0RRb0pQR1p2Ym5RZ2MybDZaVDBpTWlJK1BHWnZiblFnWm1GalpUMGlRWEpwWVd3aVBsUm9aU0J5WlhGMVpYTjBJRzUxYldKbGNpWnVZbk53T3p4emRISnZibWMrUEhVK0pFeEpUa3RmVDFCRlVrRlVTVkk4TDNVK1BDOXpkSEp2Ym1jK0ptNWljM0E3ZDJGeklISmxhbVZqZEdWa0lHbHVJQ1JTUlVwRlExUkpUMDR1UEdKeUlDOCtEUW9KUEhOMGNtOXVaejVTWlhGMVpYTjBaWEk4TDNOMGNtOXVaejQ2SUNSU1JWRlZSVk5VUlZJOFluSWdMejROQ2drOFptOXVkQ0JtWVdObFBTSkJjbWxoYkN3Z1NHVnNkbVYwYVdOaExDQnpZVzV6TFhObGNtbG1JaUJ6YVhwbFBTSXlJajQ4YzNSeWIyNW5QbE4xWW1wbFkzUTZQQzl6ZEhKdmJtYytKbTVpYzNBN0ptNWljM0E3Sm01aWMzQTdKbTVpYzNBN0ptNWljM0E3SUNSVFZVSktSVU5VUEdKeUlDOCtEUW9KUEhOMGNtOXVaejVGYm5SeWVTQkVZWFJsT2p3dmMzUnliMjVuUGladVluTndPeVJTUlVOUFVrUThMMlp2Ym5RK1BHSnlJQzgrRFFvSlBITjBjbTl1Wno1U1pXcGxZM1JsWkNCaWVUbzhMM04wY205dVp6NG1ibUp6Y0Rza1ZWTkZVanhpY2lBdlBnMEtDVHh6ZEhKdmJtYytSR1Z6WTNKcGNIUnBiMjQ2UEM5emRISnZibWMrSm01aWMzQTdKRVJGVTBOU1NWQlVTVTlPUEM5bWIyNTBQand2Wm05dWRENDhMM0ErRFFvOGNENE5DZ2s4Wm05dWRDQm1ZV05sUFNKQmNtbGhiQ0lnYzJsNlpUMGlNaUkrUEhOMGNtOXVaejVPYjNSbGN6d3ZjM1J5YjI1blBqeGljaUF2UGcwS0NTUk9WRjlQVUVWU1FWUlBVand2Wm05dWRENDhMM0ErRFFvPQ==');



##
## TABELA: hdk_tbwork_calendar
##
DROP TABLE IF EXISTS hdk_tbwork_calendar ; 

CREATE TABLE IF NOT EXISTS `hdk_tbwork_calendar` (
  `num_day_week` int(1) unsigned NOT NULL,
  `business_day` int(1) unsigned DEFAULT '0',
  `begin_morning` varchar(4) DEFAULT '0800',
  `end_morning` varchar(4) DEFAULT '1200',
  `begin_afternoon` varchar(4) DEFAULT '1300',
  `end_afternoon` varchar(4) DEFAULT '1800',
  PRIMARY KEY (`num_day_week`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ; 

# Dados para a tabela: hdk_tbwork_calendar

INSERT IGNORE INTO hdk_tbwork_calendar VALUES ('0','0','0800','1200','1300','1800');

INSERT IGNORE INTO hdk_tbwork_calendar VALUES ('1','1','0800','1200','1300','1800');

INSERT IGNORE INTO hdk_tbwork_calendar VALUES ('2','1','0800','1200','1300','1800');

INSERT IGNORE INTO hdk_tbwork_calendar VALUES ('3','1','0800','1200','1300','1800');

INSERT IGNORE INTO hdk_tbwork_calendar VALUES ('4','1','0800','1200','1300','1800');

INSERT IGNORE INTO hdk_tbwork_calendar VALUES ('5','1','0800','1200','1300','1700');

INSERT IGNORE INTO hdk_tbwork_calendar VALUES ('6','0','0800','1200','1300','1800');



##
## TABELA: hdk_tbwork_calendar_new
##
DROP TABLE IF EXISTS hdk_tbwork_calendar_new ; 

CREATE TABLE IF NOT EXISTS `hdk_tbwork_calendar_new` (
  `num_day_week` int(1) unsigned NOT NULL,
  `business_day` int(1) unsigned DEFAULT '0',
  `begin_morning` time NOT NULL,
  `end_morning` time NOT NULL,
  `begin_afternoon` time NOT NULL,
  `end_afternoon` time NOT NULL,
  PRIMARY KEY (`num_day_week`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ; 

# Dados para a tabela: hdk_tbwork_calendar_new

INSERT IGNORE INTO hdk_tbwork_calendar_new VALUES ('0','0','08:00:00','12:00:00','13:00:00','18:00:00');

INSERT IGNORE INTO hdk_tbwork_calendar_new VALUES ('1','1','08:00:00','12:00:00','13:00:00','18:00:00');

INSERT IGNORE INTO hdk_tbwork_calendar_new VALUES ('2','1','08:00:00','12:00:00','13:00:00','18:00:00');

INSERT IGNORE INTO hdk_tbwork_calendar_new VALUES ('3','1','08:00:00','12:00:00','13:00:00','18:00:00');

INSERT IGNORE INTO hdk_tbwork_calendar_new VALUES ('4','1','08:00:00','12:00:00','13:00:00','18:00:00');

INSERT IGNORE INTO hdk_tbwork_calendar_new VALUES ('5','1','08:00:00','12:00:00','13:00:00','17:00:00');

INSERT IGNORE INTO hdk_tbwork_calendar_new VALUES ('6','0','08:00:00','12:00:00','13:00:00','18:00:00');



##
## TABELA: prj_task_dep
##
DROP TABLE IF EXISTS prj_task_dep ; 

CREATE TABLE IF NOT EXISTS `prj_task_dep` (
  `idtask_dep` int(11) NOT NULL AUTO_INCREMENT,
  `idtask` int(11) NOT NULL,
  `idtask_pai` int(11) NOT NULL,
  PRIMARY KEY (`idtask_dep`),
  KEY `idtask_pai` (`idtask_pai`),
  KEY `idtask` (`idtask`),
  CONSTRAINT `prj_tbtask_dep_ibfk_1` FOREIGN KEY (`idtask_pai`) REFERENCES `prj_tbtask` (`idtask`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `prj_tbtask_dep_ibfk_2` FOREIGN KEY (`idtask`) REFERENCES `prj_tbtask` (`idtask`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ; 

# Dados para a tabela: prj_task_dep



##
## TABELA: prj_tbday_week
##
DROP TABLE IF EXISTS prj_tbday_week ; 

CREATE TABLE IF NOT EXISTS `prj_tbday_week` (
  `idday_week` int(11) NOT NULL,
  `description_day_week` varchar(25) NOT NULL DEFAULT '',
  PRIMARY KEY (`idday_week`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ; 

# Dados para a tabela: prj_tbday_week



##
## TABELA: prj_tbdep
##
DROP TABLE IF EXISTS prj_tbdep ; 

CREATE TABLE IF NOT EXISTS `prj_tbdep` (
  `idprj_dep` int(11) NOT NULL AUTO_INCREMENT,
  `idprj` int(11) NOT NULL,
  `idprj_pai` int(11) NOT NULL,
  PRIMARY KEY (`idprj_dep`),
  KEY `idprj_pai` (`idprj_pai`),
  KEY `idprj` (`idprj`),
  CONSTRAINT `prj_dep_ibfk_1` FOREIGN KEY (`idprj_pai`) REFERENCES `prj_tbprojects` (`idproject`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `prj_dep_ibfk_2` FOREIGN KEY (`idprj`) REFERENCES `prj_tbprojects` (`idproject`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ; 

# Dados para a tabela: prj_tbdep



##
## TABELA: prj_tblog_task
##
DROP TABLE IF EXISTS prj_tblog_task ; 

CREATE TABLE IF NOT EXISTS `prj_tblog_task` (
  `idlog_task` int(11) NOT NULL AUTO_INCREMENT,
  `idtask` int(11) NOT NULL DEFAULT '0',
  `date_log` varchar(10) NOT NULL DEFAULT '',
  `hour_log` varchar(5) NOT NULL DEFAULT '',
  `num_hours_worked` varchar(9) NOT NULL DEFAULT '',
  `summary_log` varchar(255) NOT NULL DEFAULT '',
  `description_log` text NOT NULL,
  PRIMARY KEY (`idlog_task`),
  KEY `idtask` (`idtask`),
  CONSTRAINT `prj_log_task_ibfk_1` FOREIGN KEY (`idtask`) REFERENCES `prj_tbtask` (`idtask`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ; 

# Dados para a tabela: prj_tblog_task



##
## TABELA: prj_tbpercentual
##
DROP TABLE IF EXISTS prj_tbpercentual ; 

CREATE TABLE IF NOT EXISTS `prj_tbpercentual` (
  `idpercentual` int(11) NOT NULL AUTO_INCREMENT,
  `percentual` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`idpercentual`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 ; 

# Dados para a tabela: prj_tbpercentual

INSERT IGNORE INTO prj_tbpercentual VALUES ('1','0');

INSERT IGNORE INTO prj_tbpercentual VALUES ('2','5');

INSERT IGNORE INTO prj_tbpercentual VALUES ('3','10');

INSERT IGNORE INTO prj_tbpercentual VALUES ('4','15');

INSERT IGNORE INTO prj_tbpercentual VALUES ('5','20');

INSERT IGNORE INTO prj_tbpercentual VALUES ('6','25');

INSERT IGNORE INTO prj_tbpercentual VALUES ('7','30');

INSERT IGNORE INTO prj_tbpercentual VALUES ('8','35');

INSERT IGNORE INTO prj_tbpercentual VALUES ('9','40');

INSERT IGNORE INTO prj_tbpercentual VALUES ('10','45');

INSERT IGNORE INTO prj_tbpercentual VALUES ('11','50');

INSERT IGNORE INTO prj_tbpercentual VALUES ('12','55');

INSERT IGNORE INTO prj_tbpercentual VALUES ('13','60');

INSERT IGNORE INTO prj_tbpercentual VALUES ('14','65');

INSERT IGNORE INTO prj_tbpercentual VALUES ('15','70');

INSERT IGNORE INTO prj_tbpercentual VALUES ('16','75');

INSERT IGNORE INTO prj_tbpercentual VALUES ('17','80');

INSERT IGNORE INTO prj_tbpercentual VALUES ('18','85');

INSERT IGNORE INTO prj_tbpercentual VALUES ('19','90');

INSERT IGNORE INTO prj_tbpercentual VALUES ('20','95');

INSERT IGNORE INTO prj_tbpercentual VALUES ('21','100');



##
## TABELA: prj_tbprojects
##
DROP TABLE IF EXISTS prj_tbprojects ; 

CREATE TABLE IF NOT EXISTS `prj_tbprojects` (
  `idproject` int(11) NOT NULL AUTO_INCREMENT,
  `company_project` int(11) DEFAULT NULL,
  `group_project` int(11) DEFAULT NULL,
  `name_project` varchar(255) NOT NULL,
  `name_reduzido_project` varchar(10) NOT NULL,
  `creator_project` int(11) DEFAULT NULL,
  `person_project` int(11) DEFAULT NULL,
  `url_project` varchar(255) DEFAULT '',
  `date_begin_project` varchar(10) NOT NULL,
  `date_finish_project` varchar(10) NOT NULL,
  `hour_begin_project` varchar(5) NOT NULL,
  `hour_finish_project` varchar(5) NOT NULL,
  `status_project` int(11) DEFAULT NULL,
  `percentual_complete_project` tinyint(4) DEFAULT NULL,
  `description_project` text NOT NULL,
  `active_project` tinyint(4) DEFAULT NULL,
  `priority_project` tinyint(4) DEFAULT NULL,
  `type_project` int(11) DEFAULT NULL,
  `code_request` int(11) DEFAULT NULL,
  PRIMARY KEY (`idproject`),
  KEY `company_project` (`company_project`),
  KEY `grouo_project` (`group_project`),
  KEY `creator_project` (`creator_project`),
  KEY `person_project` (`person_project`),
  KEY `status_project` (`status_project`),
  KEY `type_project` (`type_project`),
  KEY `code_request` (`code_request`),
  CONSTRAINT `prj_projects_ibfk_1` FOREIGN KEY (`company_project`) REFERENCES `tbperson` (`idperson`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `prj_projects_ibfk_2` FOREIGN KEY (`group_project`) REFERENCES `hdk_tbgroup` (`idgroup`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `prj_projects_ibfk_3` FOREIGN KEY (`creator_project`) REFERENCES `tbperson` (`idperson`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `prj_projects_ibfk_4` FOREIGN KEY (`person_project`) REFERENCES `tbperson` (`idperson`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `prj_projects_ibfk_5` FOREIGN KEY (`status_project`) REFERENCES `prj_tbstatus_project` (`idstatus_project`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `prj_projects_ibfk_6` FOREIGN KEY (`type_project`) REFERENCES `prj_tbtype_project` (`idtype_project`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ; 

# Dados para a tabela: prj_tbprojects



##
## TABELA: prj_tbstatus_project
##
DROP TABLE IF EXISTS prj_tbstatus_project ; 

CREATE TABLE IF NOT EXISTS `prj_tbstatus_project` (
  `idstatus_project` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`idstatus_project`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 ; 

# Dados para a tabela: prj_tbstatus_project

INSERT IGNORE INTO prj_tbstatus_project VALUES ('1','Pendente');

INSERT IGNORE INTO prj_tbstatus_project VALUES ('2','Concluido');

INSERT IGNORE INTO prj_tbstatus_project VALUES ('3','Aberto');



##
## TABELA: prj_tbtar_rh
##
DROP TABLE IF EXISTS prj_tbtar_rh ; 

CREATE TABLE IF NOT EXISTS `prj_tbtar_rh` (
  `idtar_rh` int(11) NOT NULL AUTO_INCREMENT,
  `idoperator` int(10) NOT NULL,
  `idtask` int(11) NOT NULL,
  `percentual` int(11) NOT NULL,
  PRIMARY KEY (`idtar_rh`),
  KEY `idoperator` (`idoperator`),
  KEY `idtask` (`idtask`),
  CONSTRAINT `prj_tbtar_rh_ibfk_1` FOREIGN KEY (`idoperator`) REFERENCES `tbperson` (`idperson`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `prj_tbtar_rh_ibfk_2` FOREIGN KEY (`idtask`) REFERENCES `prj_tbtask` (`idtask`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ; 

# Dados para a tabela: prj_tbtar_rh



##
## TABELA: prj_tbtask
##
DROP TABLE IF EXISTS prj_tbtask ; 

CREATE TABLE IF NOT EXISTS `prj_tbtask` (
  `idtask` int(11) NOT NULL AUTO_INCREMENT,
  `idproject` int(11) NOT NULL,
  `name_task` varchar(255) NOT NULL DEFAULT '',
  `creator_task` int(10) NOT NULL,
  `priority_task` int(11) NOT NULL,
  `url_task` varchar(255) NOT NULL DEFAULT '',
  `percentual_complete_task` varchar(4) NOT NULL DEFAULT '',
  `date_begin_task` varchar(10) NOT NULL DEFAULT '',
  `date_end_task` varchar(10) NOT NULL DEFAULT '',
  `hour_begin_task` varchar(5) NOT NULL DEFAULT '',
  `hour_end_task` varchar(5) NOT NULL DEFAULT '',
  `duration_estimated_task` varchar(9) NOT NULL DEFAULT '',
  `type_task` int(11) NOT NULL,
  `person_task` int(10) NOT NULL,
  `description_task` text NOT NULL,
  `aticve_task` tinyint(4) NOT NULL,
  PRIMARY KEY (`idtask`),
  KEY `idtask` (`idtask`),
  KEY `idproject` (`idproject`),
  KEY `creator_task` (`creator_task`),
  KEY `creator_task_2` (`creator_task`),
  KEY `person_task` (`person_task`),
  CONSTRAINT `prj_tbtask_ibfk_1` FOREIGN KEY (`idproject`) REFERENCES `prj_tbprojects` (`idproject`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `prj_tbtask_ibfk_2` FOREIGN KEY (`creator_task`) REFERENCES `tbperson` (`idperson`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `prj_tbtask_ibfk_3` FOREIGN KEY (`person_task`) REFERENCES `tbperson` (`idperson`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ; 

# Dados para a tabela: prj_tbtask



##
## TABELA: prj_tbtype_project
##
DROP TABLE IF EXISTS prj_tbtype_project ; 

CREATE TABLE IF NOT EXISTS `prj_tbtype_project` (
  `idtype_project` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`idtype_project`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 ; 

# Dados para a tabela: prj_tbtype_project

INSERT IGNORE INTO prj_tbtype_project VALUES ('1','Not rated');

INSERT IGNORE INTO prj_tbtype_project VALUES ('2','Develop Improved');

INSERT IGNORE INTO prj_tbtype_project VALUES ('3','Process Improvement');

INSERT IGNORE INTO prj_tbtype_project VALUES ('4','Integration Infrastructure');



##
## TABELA: prj_tbworkday
##
DROP TABLE IF EXISTS prj_tbworkday ; 

CREATE TABLE IF NOT EXISTS `prj_tbworkday` (
  `idworkday` int(11) NOT NULL AUTO_INCREMENT,
  `idperson` int(10) NOT NULL,
  `idday_week` int(11) NOT NULL,
  `hour_begin_morming` time NOT NULL DEFAULT '00:00:00',
  `hour_end_morning` time NOT NULL DEFAULT '00:00:00',
  `hour_begin_afternoon` time NOT NULL DEFAULT '00:00:00',
  `hour_end_afternoon` time NOT NULL DEFAULT '00:00:00',
  `business_day` char(1) NOT NULL DEFAULT '',
  PRIMARY KEY (`idworkday`),
  KEY `idperson` (`idperson`),
  KEY `idday_week` (`idday_week`),
  CONSTRAINT `prj_workday_ibfk_1` FOREIGN KEY (`idperson`) REFERENCES `tbperson` (`idperson`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `prj_workday_ibfk_2` FOREIGN KEY (`idday_week`) REFERENCES `prj_tbday_week` (`idday_week`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ; 

# Dados para a tabela: prj_tbworkday



##
## TABELA: tbaccesstype
##
DROP TABLE IF EXISTS tbaccesstype ; 

CREATE TABLE IF NOT EXISTS `tbaccesstype` (
  `idaccesstype` int(4) NOT NULL AUTO_INCREMENT,
  `type` varchar(20) NOT NULL,
  PRIMARY KEY (`idaccesstype`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC ; 

# Dados para a tabela: tbaccesstype

INSERT IGNORE INTO tbaccesstype VALUES ('1','access');

INSERT IGNORE INTO tbaccesstype VALUES ('2','new');

INSERT IGNORE INTO tbaccesstype VALUES ('3','edit');

INSERT IGNORE INTO tbaccesstype VALUES ('4','delete');

INSERT IGNORE INTO tbaccesstype VALUES ('5','export');

INSERT IGNORE INTO tbaccesstype VALUES ('6','email');

INSERT IGNORE INTO tbaccesstype VALUES ('7','sms');



##
## TABELA: tbaddress
##
DROP TABLE IF EXISTS tbaddress ; 

CREATE TABLE IF NOT EXISTS `tbaddress` (
  `idaddress` int(11) NOT NULL AUTO_INCREMENT,
  `idperson` int(11) NOT NULL,
  `idcity` int(11) NOT NULL,
  `idneighborhood` int(11) NOT NULL,
  `idstreet` int(11) NOT NULL,
  `idtypeaddress` int(11) NOT NULL,
  `number` varchar(9) DEFAULT NULL,
  `complement` varchar(45) DEFAULT NULL,
  `zipcode` varchar(11) DEFAULT NULL,
  PRIMARY KEY (`idaddress`),
  KEY `fk_tbaddress_tbperson1` (`idperson`),
  KEY `fk_tbaddress_tbcity1` (`idcity`),
  KEY `fk_tbaddress_tbneighborhood1` (`idneighborhood`),
  KEY `fk_tbaddress_tbstreet1` (`idstreet`),
  KEY `fk_tbaddress_tbtypeaddress1` (`idtypeaddress`),
  CONSTRAINT `fk_tbaddress_tbcity1` FOREIGN KEY (`idcity`) REFERENCES `tbcity` (`idcity`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_tbaddress_tbneighborhood1` FOREIGN KEY (`idneighborhood`) REFERENCES `tbneighborhood` (`idneighborhood`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_tbaddress_tbperson1` FOREIGN KEY (`idperson`) REFERENCES `tbperson` (`idperson`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_tbaddress_tbstreet1` FOREIGN KEY (`idstreet`) REFERENCES `tbstreet` (`idstreet`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_tbaddress_tbtypeaddress1` FOREIGN KEY (`idtypeaddress`) REFERENCES `tbtypeaddress` (`idtypeaddress`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 ; 

# Dados para a tabela: tbaddress

INSERT IGNORE INTO tbaddress VALUES ('1','60','1','33','37','3','1','','99999999');



##
## TABELA: tbcity
##
DROP TABLE IF EXISTS tbcity ; 

CREATE TABLE IF NOT EXISTS `tbcity` (
  `idcity` int(11) NOT NULL AUTO_INCREMENT,
  `idstate` int(11) NOT NULL,
  `name` varchar(60) DEFAULT NULL,
  PRIMARY KEY (`idcity`),
  KEY `idx_tbcity_name` (`name`),
  KEY `fk_tbcity_tbstate1` (`idstate`),
  CONSTRAINT `fk_tbcity_tbstate1` FOREIGN KEY (`idstate`) REFERENCES `tbstate` (`idstate`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1 ; 

# Dados para a tabela: tbcity

INSERT IGNORE INTO tbcity VALUES ('1','1','Choose');

INSERT IGNORE INTO tbcity VALUES ('2','12','Miami');

INSERT IGNORE INTO tbcity VALUES ('3','89','Pelotas');

INSERT IGNORE INTO tbcity VALUES ('4','89','Porto Alegre');

INSERT IGNORE INTO tbcity VALUES ('5','89','Rio Grande');

INSERT IGNORE INTO tbcity VALUES ('6','89','Camaquã');

INSERT IGNORE INTO tbcity VALUES ('7','89','Jaguarão');

INSERT IGNORE INTO tbcity VALUES ('8','92','Santos');

INSERT IGNORE INTO tbcity VALUES ('9','7','Oakland');

INSERT IGNORE INTO tbcity VALUES ('10','96','New City');

INSERT IGNORE INTO tbcity VALUES ('11','97','Vale da Honra');



##
## TABELA: tbcompany
##
DROP TABLE IF EXISTS tbcompany ; 

CREATE TABLE IF NOT EXISTS `tbcompany` (
  `idcompany` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`idcompany`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 ; 

# Dados para a tabela: tbcompany

INSERT IGNORE INTO tbcompany VALUES ('1','Demo Company');



##
## TABELA: tbcountry
##
DROP TABLE IF EXISTS tbcountry ; 

CREATE TABLE IF NOT EXISTS `tbcountry` (
  `idcountry` int(11) NOT NULL AUTO_INCREMENT,
  `iso` varchar(2) DEFAULT NULL,
  `name` varchar(80) DEFAULT NULL,
  `iso3` varchar(3) DEFAULT NULL,
  `printablename` varchar(80) DEFAULT NULL,
  PRIMARY KEY (`idcountry`),
  UNIQUE KEY `code_UNIQUE` (`iso`)
) ENGINE=InnoDB AUTO_INCREMENT=241 DEFAULT CHARSET=latin1 ; 

# Dados para a tabela: tbcountry

INSERT IGNORE INTO tbcountry VALUES ('1','AA','Choose','AAA','Choose');

INSERT IGNORE INTO tbcountry VALUES ('2','AF','AFGHANISTAN','AFG','Afghanistan');

INSERT IGNORE INTO tbcountry VALUES ('3','AL','ALBANIA','ALB','Albania');

INSERT IGNORE INTO tbcountry VALUES ('4','DZ','ALGERIA','DZA','Algeria');

INSERT IGNORE INTO tbcountry VALUES ('5','AS','AMERICAN SAMOA','ASM','American Samoa');

INSERT IGNORE INTO tbcountry VALUES ('6','AD','ANDORRA','AND','Andorra');

INSERT IGNORE INTO tbcountry VALUES ('7','AO','ANGOLA','AGO','Angola');

INSERT IGNORE INTO tbcountry VALUES ('8','AI','ANGUILLA','AIA','Anguilla');

INSERT IGNORE INTO tbcountry VALUES ('9','AQ','ANTARCTICA','','Antarctica');

INSERT IGNORE INTO tbcountry VALUES ('10','AG','ANTIGUA AND BARBUDA','ATG','Antigua and Barbuda');

INSERT IGNORE INTO tbcountry VALUES ('11','AR','ARGENTINA','ARG','Argentina');

INSERT IGNORE INTO tbcountry VALUES ('12','AM','ARMENIA','ARM','Armenia');

INSERT IGNORE INTO tbcountry VALUES ('13','AW','ARUBA','ABW','Aruba');

INSERT IGNORE INTO tbcountry VALUES ('14','AU','AUSTRALIA','AUS','Australia');

INSERT IGNORE INTO tbcountry VALUES ('15','AT','AUSTRIA','AUT','Austria');

INSERT IGNORE INTO tbcountry VALUES ('16','AZ','AZERBAIJAN','AZE','Azerbaijan');

INSERT IGNORE INTO tbcountry VALUES ('17','BS','BAHAMAS','BHS','Bahamas');

INSERT IGNORE INTO tbcountry VALUES ('18','BH','BAHRAIN','BHR','Bahrain');

INSERT IGNORE INTO tbcountry VALUES ('19','BD','BANGLADESH','BGD','Bangladesh');

INSERT IGNORE INTO tbcountry VALUES ('20','BB','BARBADOS','BRB','Barbados');

INSERT IGNORE INTO tbcountry VALUES ('21','BY','BELARUS','BLR','Belarus');

INSERT IGNORE INTO tbcountry VALUES ('22','BE','BELGIUM','BEL','Belgium');

INSERT IGNORE INTO tbcountry VALUES ('23','BZ','BELIZE','BLZ','Belize');

INSERT IGNORE INTO tbcountry VALUES ('24','BJ','BENIN','BEN','Benin');

INSERT IGNORE INTO tbcountry VALUES ('25','BM','BERMUDA','BMU','Bermuda');

INSERT IGNORE INTO tbcountry VALUES ('26','BT','BHUTAN','BTN','Bhutan');

INSERT IGNORE INTO tbcountry VALUES ('27','BO','BOLIVIA','BOL','Bolivia');

INSERT IGNORE INTO tbcountry VALUES ('28','BA','BOSNIA AND HERZEGOVINA','BIH','Bosnia and Herzegovina');

INSERT IGNORE INTO tbcountry VALUES ('29','BW','BOTSWANA','BWA','Botswana');

INSERT IGNORE INTO tbcountry VALUES ('30','BV','BOUVET ISLAND','','Bouvet Island');

INSERT IGNORE INTO tbcountry VALUES ('31','BR','BRAZIL','BRA','Brazil');

INSERT IGNORE INTO tbcountry VALUES ('32','IO','BRITISH INDIAN OCEAN TERRITORY','','British Indian Ocean Territory');

INSERT IGNORE INTO tbcountry VALUES ('33','BN','BRUNEI DARUSSALAM','BRN','Brunei Darussalam');

INSERT IGNORE INTO tbcountry VALUES ('34','BG','BULGARIA','BGR','Bulgaria');

INSERT IGNORE INTO tbcountry VALUES ('35','BF','BURKINA FASO','BFA','Burkina Faso');

INSERT IGNORE INTO tbcountry VALUES ('36','BI','BURUNDI','BDI','Burundi');

INSERT IGNORE INTO tbcountry VALUES ('37','KH','CAMBODIA','KHM','Cambodia');

INSERT IGNORE INTO tbcountry VALUES ('38','CM','CAMEROON','CMR','Cameroon');

INSERT IGNORE INTO tbcountry VALUES ('39','CA','CANADA','CAN','Canada');

INSERT IGNORE INTO tbcountry VALUES ('40','CV','CAPE VERDE','CPV','Cape Verde');

INSERT IGNORE INTO tbcountry VALUES ('41','KY','CAYMAN ISLANDS','CYM','Cayman Islands');

INSERT IGNORE INTO tbcountry VALUES ('42','CF','CENTRAL AFRICAN REPUBLIC','CAF','Central African Republic');

INSERT IGNORE INTO tbcountry VALUES ('43','TD','CHAD','TCD','Chad');

INSERT IGNORE INTO tbcountry VALUES ('44','CL','CHILE','CHL','Chile');

INSERT IGNORE INTO tbcountry VALUES ('45','CN','CHINA','CHN','China');

INSERT IGNORE INTO tbcountry VALUES ('46','CX','CHRISTMAS ISLAND','','Christmas Island');

INSERT IGNORE INTO tbcountry VALUES ('47','CC','COCOS (KEELING) ISLANDS','','Cocos (Keeling) Islands');

INSERT IGNORE INTO tbcountry VALUES ('48','CO','COLOMBIA','COL','Colombia');

INSERT IGNORE INTO tbcountry VALUES ('49','KM','COMOROS','COM','Comoros');

INSERT IGNORE INTO tbcountry VALUES ('50','CG','CONGO','COG','Congo');

INSERT IGNORE INTO tbcountry VALUES ('51','CD','CONGO, THE DEMOCRATIC REPUBLIC OF THE','COD','Congo, the Democratic Republic of the');

INSERT IGNORE INTO tbcountry VALUES ('52','CK','COOK ISLANDS','COK','Cook Islands');

INSERT IGNORE INTO tbcountry VALUES ('53','CR','COSTA RICA','CRI','Costa Rica');

INSERT IGNORE INTO tbcountry VALUES ('54','CI','COTE D\'IVOIRE','CIV','Cote D\'Ivoire');

INSERT IGNORE INTO tbcountry VALUES ('55','HR','CROATIA','HRV','Croatia');

INSERT IGNORE INTO tbcountry VALUES ('56','CU','CUBA','CUB','Cuba');

INSERT IGNORE INTO tbcountry VALUES ('57','CY','CYPRUS','CYP','Cyprus');

INSERT IGNORE INTO tbcountry VALUES ('58','CZ','CZECH REPUBLIC','CZE','Czech Republic');

INSERT IGNORE INTO tbcountry VALUES ('59','DK','DENMARK','DNK','Denmark');

INSERT IGNORE INTO tbcountry VALUES ('60','DJ','DJIBOUTI','DJI','Djibouti');

INSERT IGNORE INTO tbcountry VALUES ('61','DM','DOMINICA','DMA','Dominica');

INSERT IGNORE INTO tbcountry VALUES ('62','DO','DOMINICAN REPUBLIC','DOM','Dominican Republic');

INSERT IGNORE INTO tbcountry VALUES ('63','EC','ECUADOR','ECU','Ecuador');

INSERT IGNORE INTO tbcountry VALUES ('64','EG','EGYPT','EGY','Egypt');

INSERT IGNORE INTO tbcountry VALUES ('65','SV','EL SALVADOR','SLV','El Salvador');

INSERT IGNORE INTO tbcountry VALUES ('66','GQ','EQUATORIAL GUINEA','GNQ','Equatorial Guinea');

INSERT IGNORE INTO tbcountry VALUES ('67','ER','ERITREA','ERI','Eritrea');

INSERT IGNORE INTO tbcountry VALUES ('68','EE','ESTONIA','EST','Estonia');

INSERT IGNORE INTO tbcountry VALUES ('69','ET','ETHIOPIA','ETH','Ethiopia');

INSERT IGNORE INTO tbcountry VALUES ('70','FK','FALKLAND ISLANDS (MALVINAS)','FLK','Falkland Islands (Malvinas)');

INSERT IGNORE INTO tbcountry VALUES ('71','FO','FAROE ISLANDS','FRO','Faroe Islands');

INSERT IGNORE INTO tbcountry VALUES ('72','FJ','FIJI','FJI','Fiji');

INSERT IGNORE INTO tbcountry VALUES ('73','FI','FINLAND','FIN','Finland');

INSERT IGNORE INTO tbcountry VALUES ('74','FR','FRANCE','FRA','France');

INSERT IGNORE INTO tbcountry VALUES ('75','GF','FRENCH GUIANA','GUF','French Guiana');

INSERT IGNORE INTO tbcountry VALUES ('76','PF','FRENCH POLYNESIA','PYF','French Polynesia');

INSERT IGNORE INTO tbcountry VALUES ('77','TF','FRENCH SOUTHERN TERRITORIES','','French Southern Territories');

INSERT IGNORE INTO tbcountry VALUES ('78','GA','GABON','GAB','Gabon');

INSERT IGNORE INTO tbcountry VALUES ('79','GM','GAMBIA','GMB','Gambia');

INSERT IGNORE INTO tbcountry VALUES ('80','GE','GEORGIA','GEO','Georgia');

INSERT IGNORE INTO tbcountry VALUES ('81','DE','GERMANY','DEU','Germany');

INSERT IGNORE INTO tbcountry VALUES ('82','GH','GHANA','GHA','Ghana');

INSERT IGNORE INTO tbcountry VALUES ('83','GI','GIBRALTAR','GIB','Gibraltar');

INSERT IGNORE INTO tbcountry VALUES ('84','GR','GREECE','GRC','Greece');

INSERT IGNORE INTO tbcountry VALUES ('85','GL','GREENLAND','GRL','Greenland');

INSERT IGNORE INTO tbcountry VALUES ('86','GD','GRENADA','GRD','Grenada');

INSERT IGNORE INTO tbcountry VALUES ('87','GP','GUADELOUPE','GLP','Guadeloupe');

INSERT IGNORE INTO tbcountry VALUES ('88','GU','GUAM','GUM','Guam');

INSERT IGNORE INTO tbcountry VALUES ('89','GT','GUATEMALA','GTM','Guatemala');

INSERT IGNORE INTO tbcountry VALUES ('90','GN','GUINEA','GIN','Guinea');

INSERT IGNORE INTO tbcountry VALUES ('91','GW','GUINEA-BISSAU','GNB','Guinea-Bissau');

INSERT IGNORE INTO tbcountry VALUES ('92','GY','GUYANA','GUY','Guyana');

INSERT IGNORE INTO tbcountry VALUES ('93','HT','HAITI','HTI','Haiti');

INSERT IGNORE INTO tbcountry VALUES ('94','HM','HEARD ISLAND AND MCDONALD ISLANDS','','Heard Island and Mcdonald Islands');

INSERT IGNORE INTO tbcountry VALUES ('95','VA','HOLY SEE (VATICAN CITY STATE)','VAT','Holy See (Vatican City State)');

INSERT IGNORE INTO tbcountry VALUES ('96','HN','HONDURAS','HND','Honduras');

INSERT IGNORE INTO tbcountry VALUES ('97','HK','HONG KONG','HKG','Hong Kong');

INSERT IGNORE INTO tbcountry VALUES ('98','HU','HUNGARY','HUN','Hungary');

INSERT IGNORE INTO tbcountry VALUES ('99','IS','ICELAND','ISL','Iceland');

INSERT IGNORE INTO tbcountry VALUES ('100','IN','INDIA','IND','India');

INSERT IGNORE INTO tbcountry VALUES ('101','ID','INDONESIA','IDN','Indonesia');

INSERT IGNORE INTO tbcountry VALUES ('102','IR','IRAN, ISLAMIC REPUBLIC OF','IRN','Iran, Islamic Republic of');

INSERT IGNORE INTO tbcountry VALUES ('103','IQ','IRAQ','IRQ','Iraq');

INSERT IGNORE INTO tbcountry VALUES ('104','IE','IRELAND','IRL','Ireland');

INSERT IGNORE INTO tbcountry VALUES ('105','IL','ISRAEL','ISR','Israel');

INSERT IGNORE INTO tbcountry VALUES ('106','IT','ITALY','ITA','Italy');

INSERT IGNORE INTO tbcountry VALUES ('107','JM','JAMAICA','JAM','Jamaica');

INSERT IGNORE INTO tbcountry VALUES ('108','JP','JAPAN','JPN','Japan');

INSERT IGNORE INTO tbcountry VALUES ('109','JO','JORDAN','JOR','Jordan');

INSERT IGNORE INTO tbcountry VALUES ('110','KZ','KAZAKHSTAN','KAZ','Kazakhstan');

INSERT IGNORE INTO tbcountry VALUES ('111','KE','KENYA','KEN','Kenya');

INSERT IGNORE INTO tbcountry VALUES ('112','KI','KIRIBATI','KIR','Kiribati');

INSERT IGNORE INTO tbcountry VALUES ('113','KP','KOREA, DEMOCRATIC PEOPLE\'S REPUBLIC OF','PRK','Korea, Democratic People\'s Republic of');

INSERT IGNORE INTO tbcountry VALUES ('114','KR','KOREA, REPUBLIC OF','KOR','Korea, Republic of');

INSERT IGNORE INTO tbcountry VALUES ('115','KW','KUWAIT','KWT','Kuwait');

INSERT IGNORE INTO tbcountry VALUES ('116','KG','KYRGYZSTAN','KGZ','Kyrgyzstan');

INSERT IGNORE INTO tbcountry VALUES ('117','LA','LAO PEOPLE\'S DEMOCRATIC REPUBLIC','LAO','Lao People\'s Democratic Republic');

INSERT IGNORE INTO tbcountry VALUES ('118','LV','LATVIA','LVA','Latvia');

INSERT IGNORE INTO tbcountry VALUES ('119','LB','LEBANON','LBN','Lebanon');

INSERT IGNORE INTO tbcountry VALUES ('120','LS','LESOTHO','LSO','Lesotho');

INSERT IGNORE INTO tbcountry VALUES ('121','LR','LIBERIA','LBR','Liberia');

INSERT IGNORE INTO tbcountry VALUES ('122','LY','LIBYAN ARAB JAMAHIRIYA','LBY','Libyan Arab Jamahiriya');

INSERT IGNORE INTO tbcountry VALUES ('123','LI','LIECHTENSTEIN','LIE','Liechtenstein');

INSERT IGNORE INTO tbcountry VALUES ('124','LT','LITHUANIA','LTU','Lithuania');

INSERT IGNORE INTO tbcountry VALUES ('125','LU','LUXEMBOURG','LUX','Luxembourg');

INSERT IGNORE INTO tbcountry VALUES ('126','MO','MACAO','MAC','Macao');

INSERT IGNORE INTO tbcountry VALUES ('127','MK','MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF','MKD','Macedonia, the Former Yugoslav Republic of');

INSERT IGNORE INTO tbcountry VALUES ('128','MG','MADAGASCAR','MDG','Madagascar');

INSERT IGNORE INTO tbcountry VALUES ('129','MW','MALAWI','MWI','Malawi');

INSERT IGNORE INTO tbcountry VALUES ('130','MY','MALAYSIA','MYS','Malaysia');

INSERT IGNORE INTO tbcountry VALUES ('131','MV','MALDIVES','MDV','Maldives');

INSERT IGNORE INTO tbcountry VALUES ('132','ML','MALI','MLI','Mali');

INSERT IGNORE INTO tbcountry VALUES ('133','MT','MALTA','MLT','Malta');

INSERT IGNORE INTO tbcountry VALUES ('134','MH','MARSHALL ISLANDS','MHL','Marshall Islands');

INSERT IGNORE INTO tbcountry VALUES ('135','MQ','MARTINIQUE','MTQ','Martinique');

INSERT IGNORE INTO tbcountry VALUES ('136','MR','MAURITANIA','MRT','Mauritania');

INSERT IGNORE INTO tbcountry VALUES ('137','MU','MAURITIUS','MUS','Mauritius');

INSERT IGNORE INTO tbcountry VALUES ('138','YT','MAYOTTE','','Mayotte');

INSERT IGNORE INTO tbcountry VALUES ('139','MX','MEXICO','MEX','Mexico');

INSERT IGNORE INTO tbcountry VALUES ('140','FM','MICRONESIA, FEDERATED STATES OF','FSM','Micronesia, Federated States of');

INSERT IGNORE INTO tbcountry VALUES ('141','MD','MOLDOVA, REPUBLIC OF','MDA','Moldova, Republic of');

INSERT IGNORE INTO tbcountry VALUES ('142','MC','MONACO','MCO','Monaco');

INSERT IGNORE INTO tbcountry VALUES ('143','MN','MONGOLIA','MNG','Mongolia');

INSERT IGNORE INTO tbcountry VALUES ('144','MS','MONTSERRAT','MSR','Montserrat');

INSERT IGNORE INTO tbcountry VALUES ('145','MA','MOROCCO','MAR','Morocco');

INSERT IGNORE INTO tbcountry VALUES ('146','MZ','MOZAMBIQUE','MOZ','Mozambique');

INSERT IGNORE INTO tbcountry VALUES ('147','MM','MYANMAR','MMR','Myanmar');

INSERT IGNORE INTO tbcountry VALUES ('148','NA','NAMIBIA','NAM','Namibia');

INSERT IGNORE INTO tbcountry VALUES ('149','NR','NAURU','NRU','Nauru');

INSERT IGNORE INTO tbcountry VALUES ('150','NP','NEPAL','NPL','Nepal');

INSERT IGNORE INTO tbcountry VALUES ('151','NL','NETHERLANDS','NLD','Netherlands');

INSERT IGNORE INTO tbcountry VALUES ('152','AN','NETHERLANDS ANTILLES','ANT','Netherlands Antilles');

INSERT IGNORE INTO tbcountry VALUES ('153','NC','NEW CALEDONIA','NCL','New Caledonia');

INSERT IGNORE INTO tbcountry VALUES ('154','NZ','NEW ZEALAND','NZL','New Zealand');

INSERT IGNORE INTO tbcountry VALUES ('155','NI','NICARAGUA','NIC','Nicaragua');

INSERT IGNORE INTO tbcountry VALUES ('156','NE','NIGER','NER','Niger');

INSERT IGNORE INTO tbcountry VALUES ('157','NG','NIGERIA','NGA','Nigeria');

INSERT IGNORE INTO tbcountry VALUES ('158','NU','NIUE','NIU','Niue');

INSERT IGNORE INTO tbcountry VALUES ('159','NF','NORFOLK ISLAND','NFK','Norfolk Island');

INSERT IGNORE INTO tbcountry VALUES ('160','MP','NORTHERN MARIANA ISLANDS','MNP','Northern Mariana Islands');

INSERT IGNORE INTO tbcountry VALUES ('161','NO','NORWAY','NOR','Norway');

INSERT IGNORE INTO tbcountry VALUES ('162','OM','OMAN','OMN','Oman');

INSERT IGNORE INTO tbcountry VALUES ('163','PK','PAKISTAN','PAK','Pakistan');

INSERT IGNORE INTO tbcountry VALUES ('164','PW','PALAU','PLW','Palau');

INSERT IGNORE INTO tbcountry VALUES ('165','PS','PALESTINIAN TERRITORY, OCCUPIED','','Palestinian Territory, Occupied');

INSERT IGNORE INTO tbcountry VALUES ('166','PA','PANAMA','PAN','Panama');

INSERT IGNORE INTO tbcountry VALUES ('167','PG','PAPUA NEW GUINEA','PNG','Papua New Guinea');

INSERT IGNORE INTO tbcountry VALUES ('168','PY','PARAGUAY','PRY','Paraguay');

INSERT IGNORE INTO tbcountry VALUES ('169','PE','PERU','PER','Peru');

INSERT IGNORE INTO tbcountry VALUES ('170','PH','PHILIPPINES','PHL','Philippines');

INSERT IGNORE INTO tbcountry VALUES ('171','PN','PITCAIRN','PCN','Pitcairn');

INSERT IGNORE INTO tbcountry VALUES ('172','PL','POLAND','POL','Poland');

INSERT IGNORE INTO tbcountry VALUES ('173','PT','PORTUGAL','PRT','Portugal');

INSERT IGNORE INTO tbcountry VALUES ('174','PR','PUERTO RICO','PRI','Puerto Rico');

INSERT IGNORE INTO tbcountry VALUES ('175','QA','QATAR','QAT','Qatar');

INSERT IGNORE INTO tbcountry VALUES ('176','RE','REUNION','REU','Reunion');

INSERT IGNORE INTO tbcountry VALUES ('177','RO','ROMANIA','ROM','Romania');

INSERT IGNORE INTO tbcountry VALUES ('178','RU','RUSSIAN FEDERATION','RUS','Russian Federation');

INSERT IGNORE INTO tbcountry VALUES ('179','RW','RWANDA','RWA','Rwanda');

INSERT IGNORE INTO tbcountry VALUES ('180','SH','SAINT HELENA','SHN','Saint Helena');

INSERT IGNORE INTO tbcountry VALUES ('181','KN','SAINT KITTS AND NEVIS','KNA','Saint Kitts and Nevis');

INSERT IGNORE INTO tbcountry VALUES ('182','LC','SAINT LUCIA','LCA','Saint Lucia');

INSERT IGNORE INTO tbcountry VALUES ('183','PM','SAINT PIERRE AND MIQUELON','SPM','Saint Pierre and Miquelon');

INSERT IGNORE INTO tbcountry VALUES ('184','VC','SAINT VINCENT AND THE GRENADINES','VCT','Saint Vincent and the Grenadines');

INSERT IGNORE INTO tbcountry VALUES ('185','WS','SAMOA','WSM','Samoa');

INSERT IGNORE INTO tbcountry VALUES ('186','SM','SAN MARINO','SMR','San Marino');

INSERT IGNORE INTO tbcountry VALUES ('187','ST','SAO TOME AND PRINCIPE','STP','Sao Tome and Principe');

INSERT IGNORE INTO tbcountry VALUES ('188','SA','SAUDI ARABIA','SAU','Saudi Arabia');

INSERT IGNORE INTO tbcountry VALUES ('189','SN','SENEGAL','SEN','Senegal');

INSERT IGNORE INTO tbcountry VALUES ('190','CS','SERBIA AND MONTENEGRO','','Serbia and Montenegro');

INSERT IGNORE INTO tbcountry VALUES ('191','SC','SEYCHELLES','SYC','Seychelles');

INSERT IGNORE INTO tbcountry VALUES ('192','SL','SIERRA LEONE','SLE','Sierra Leone');

INSERT IGNORE INTO tbcountry VALUES ('193','SG','SINGAPORE','SGP','Singapore');

INSERT IGNORE INTO tbcountry VALUES ('194','SK','SLOVAKIA','SVK','Slovakia');

INSERT IGNORE INTO tbcountry VALUES ('195','SI','SLOVENIA','SVN','Slovenia');

INSERT IGNORE INTO tbcountry VALUES ('196','SB','SOLOMON ISLANDS','SLB','Solomon Islands');

INSERT IGNORE INTO tbcountry VALUES ('197','SO','SOMALIA','SOM','Somalia');

INSERT IGNORE INTO tbcountry VALUES ('198','ZA','SOUTH AFRICA','ZAF','South Africa');

INSERT IGNORE INTO tbcountry VALUES ('199','GS','SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS','','South Georgia and the South Sandwich Islands');

INSERT IGNORE INTO tbcountry VALUES ('200','ES','SPAIN','ESP','Spain');

INSERT IGNORE INTO tbcountry VALUES ('201','LK','SRI LANKA','LKA','Sri Lanka');

INSERT IGNORE INTO tbcountry VALUES ('202','SD','SUDAN','SDN','Sudan');

INSERT IGNORE INTO tbcountry VALUES ('203','SR','SURINAME','SUR','Suriname');

INSERT IGNORE INTO tbcountry VALUES ('204','SJ','SVALBARD AND JAN MAYEN','SJM','Svalbard and Jan Mayen');

INSERT IGNORE INTO tbcountry VALUES ('205','SZ','SWAZILAND','SWZ','Swaziland');

INSERT IGNORE INTO tbcountry VALUES ('206','SE','SWEDEN','SWE','Sweden');

INSERT IGNORE INTO tbcountry VALUES ('207','CH','SWITZERLAND','CHE','Switzerland');

INSERT IGNORE INTO tbcountry VALUES ('208','SY','SYRIAN ARAB REPUBLIC','SYR','Syrian Arab Republic');

INSERT IGNORE INTO tbcountry VALUES ('209','TW','TAIWAN, PROVINCE OF CHINA','TWN','Taiwan, Province of China');

INSERT IGNORE INTO tbcountry VALUES ('210','TJ','TAJIKISTAN','TJK','Tajikistan');

INSERT IGNORE INTO tbcountry VALUES ('211','TZ','TANZANIA, UNITED REPUBLIC OF','TZA','Tanzania, United Republic of');

INSERT IGNORE INTO tbcountry VALUES ('212','TH','THAILAND','THA','Thailand');

INSERT IGNORE INTO tbcountry VALUES ('213','TL','TIMOR-LESTE','','Timor-Leste');

INSERT IGNORE INTO tbcountry VALUES ('214','TG','TOGO','TGO','Togo');

INSERT IGNORE INTO tbcountry VALUES ('215','TK','TOKELAU','TKL','Tokelau');

INSERT IGNORE INTO tbcountry VALUES ('216','TO','TONGA','TON','Tonga');

INSERT IGNORE INTO tbcountry VALUES ('217','TT','TRINIDAD AND TOBAGO','TTO','Trinidad and Tobago');

INSERT IGNORE INTO tbcountry VALUES ('218','TN','TUNISIA','TUN','Tunisia');

INSERT IGNORE INTO tbcountry VALUES ('219','TR','TURKEY','TUR','Turkey');

INSERT IGNORE INTO tbcountry VALUES ('220','TM','TURKMENISTAN','TKM','Turkmenistan');

INSERT IGNORE INTO tbcountry VALUES ('221','TC','TURKS AND CAICOS ISLANDS','TCA','Turks and Caicos Islands');

INSERT IGNORE INTO tbcountry VALUES ('222','TV','TUVALU','TUV','Tuvalu');

INSERT IGNORE INTO tbcountry VALUES ('223','UG','UGANDA','UGA','Uganda');

INSERT IGNORE INTO tbcountry VALUES ('224','UA','UKRAINE','UKR','Ukraine');

INSERT IGNORE INTO tbcountry VALUES ('225','AE','UNITED ARAB EMIRATES','ARE','United Arab Emirates');

INSERT IGNORE INTO tbcountry VALUES ('226','GB','UNITED KINGDOM','GBR','United Kingdom');

INSERT IGNORE INTO tbcountry VALUES ('227','US','UNITED STATES','USA','United States');

INSERT IGNORE INTO tbcountry VALUES ('228','UM','UNITED STATES MINOR OUTLYING ISLANDS','','United States Minor Outlying Islands');

INSERT IGNORE INTO tbcountry VALUES ('229','UY','URUGUAY','URY','Uruguay');

INSERT IGNORE INTO tbcountry VALUES ('230','UZ','UZBEKISTAN','UZB','Uzbekistan');

INSERT IGNORE INTO tbcountry VALUES ('231','VU','VANUATU','VUT','Vanuatu');

INSERT IGNORE INTO tbcountry VALUES ('232','VE','VENEZUELA','VEN','Venezuela');

INSERT IGNORE INTO tbcountry VALUES ('233','VN','VIET NAM','VNM','Viet Nam');

INSERT IGNORE INTO tbcountry VALUES ('234','VG','VIRGIN ISLANDS, BRITISH','VGB','Virgin Islands, British');

INSERT IGNORE INTO tbcountry VALUES ('235','VI','VIRGIN ISLANDS, U.S.','VIR','Virgin Islands, U.s.');

INSERT IGNORE INTO tbcountry VALUES ('236','WF','WALLIS AND FUTUNA','WLF','Wallis and Futuna');

INSERT IGNORE INTO tbcountry VALUES ('237','EH','WESTERN SAHARA','ESH','Western Sahara');

INSERT IGNORE INTO tbcountry VALUES ('238','YE','YEMEN','YEM','Yemen');

INSERT IGNORE INTO tbcountry VALUES ('239','ZM','ZAMBIA','ZMB','Zambia');

INSERT IGNORE INTO tbcountry VALUES ('240','ZW','ZIMBABWE','ZWE','Zimbabwe');



##
## TABELA: tbdefaultpermission
##
DROP TABLE IF EXISTS tbdefaultpermission ; 

CREATE TABLE IF NOT EXISTS `tbdefaultpermission` (
  `iddefaultpermission` int(4) NOT NULL AUTO_INCREMENT,
  `idaccesstype` int(4) NOT NULL,
  `idprogram` int(4) NOT NULL,
  `allow` char(4) NOT NULL DEFAULT 'Y',
  PRIMARY KEY (`iddefaultpermission`),
  KEY `FK_tbdefaultpermission` (`idaccesstype`),
  KEY `FK_tbdefaultpermission2` (`idprogram`),
  CONSTRAINT `FK_tbdefaultpermission` FOREIGN KEY (`idaccesstype`) REFERENCES `tbaccesstype` (`idaccesstype`),
  CONSTRAINT `FK_tbdefaultpermission2` FOREIGN KEY (`idprogram`) REFERENCES `tbprogram` (`idprogram`)
) ENGINE=InnoDB AUTO_INCREMENT=232 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC ; 

# Dados para a tabela: tbdefaultpermission

INSERT IGNORE INTO tbdefaultpermission VALUES ('1','1','1','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('2','2','1','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('3','3','1','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('4','4','1','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('5','1','2','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('6','2','2','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('7','3','2','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('8','4','2','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('9','1','3','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('10','2','3','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('11','3','3','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('12','4','3','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('13','1','4','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('14','2','4','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('15','3','4','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('16','4','4','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('17','1','5','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('18','2','5','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('19','3','5','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('20','4','5','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('21','1','6','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('22','2','6','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('23','3','6','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('24','4','6','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('25','1','7','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('26','2','7','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('27','3','7','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('28','4','7','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('29','1','8','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('30','2','8','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('31','3','8','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('32','4','8','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('33','1','9','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('34','2','9','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('35','3','9','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('36','4','9','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('37','1','10','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('38','2','10','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('39','3','10','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('40','4','10','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('45','1','12','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('46','2','12','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('47','3','12','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('48','4','12','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('57','1','16','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('58','2','16','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('59','3','16','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('60','4','16','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('69','1','23','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('70','2','23','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('71','3','23','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('72','4','23','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('73','1','25','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('74','2','25','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('75','3','25','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('76','4','25','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('77','5','2','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('85','1','32','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('86','2','32','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('87','3','32','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('161','1','51','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('162','5','51','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('163','6','51','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('174','1','59','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('175','2','59','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('176','3','59','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('177','4','59','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('182','1','61','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('183','2','61','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('184','3','61','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('185','4','61','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('194','1','64','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('195','2','64','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('196','3','64','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('197','5','64','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('198','1','65','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('199','2','65','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('200','3','65','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('201','4','65','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('202','5','65','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('203','1','66','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('204','2','66','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('205','5','66','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('206','1','67','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('207','2','67','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('208','5','67','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('209','1','68','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('210','2','68','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('211','5','68','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('212','1','69','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('213','2','69','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('214','5','69','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('215','1','70','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('216','2','70','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('217','5','70','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('218','1','71','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('219','2','71','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('220','5','71','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('221','1','72','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('222','2','72','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('223','5','72','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('224','1','73','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('225','2','73','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('226','5','73','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('227','1','74','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('228','2','74','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('229','3','74','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('230','4','74','Y');

INSERT IGNORE INTO tbdefaultpermission VALUES ('231','5','74','Y');



##
## TABELA: tbdepartment
##
DROP TABLE IF EXISTS tbdepartment ; 

CREATE TABLE IF NOT EXISTS `tbdepartment` (
  `iddepartment` int(11) NOT NULL AUTO_INCREMENT,
  `idcompany` int(11) NOT NULL,
  `name` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`iddepartment`),
  KEY `fk_tbdepartment_tbcompany1` (`idcompany`),
  CONSTRAINT `fk_tbdepartment_tbcompany1` FOREIGN KEY (`idcompany`) REFERENCES `tbcompany` (`idcompany`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 ; 

# Dados para a tabela: tbdepartment

INSERT IGNORE INTO tbdepartment VALUES ('1','1','IT');



##
## TABELA: tbholiday
##
DROP TABLE IF EXISTS tbholiday ; 

CREATE TABLE IF NOT EXISTS `tbholiday` (
  `idholiday` int(4) NOT NULL AUTO_INCREMENT,
  `holiday_date` date NOT NULL,
  `holiday_description` varchar(50) NOT NULL,
  PRIMARY KEY (`idholiday`)
) ENGINE=InnoDB AUTO_INCREMENT=159 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC ; 

# Dados para a tabela: tbholiday

INSERT IGNORE INTO tbholiday VALUES ('16','2012-01-01','First day of the year');

INSERT IGNORE INTO tbholiday VALUES ('20','2012-04-07','Independence Day');

INSERT IGNORE INTO tbholiday VALUES ('31','2012-12-31','New years eve.');

INSERT IGNORE INTO tbholiday VALUES ('32','2012-12-25','Christmas');

INSERT IGNORE INTO tbholiday VALUES ('152','2012-01-16','Martin Luther King\'s birthday');

INSERT IGNORE INTO tbholiday VALUES ('153','2012-01-16','Washington\'s Birthday');

INSERT IGNORE INTO tbholiday VALUES ('154','2012-05-28','Memorial Day');

INSERT IGNORE INTO tbholiday VALUES ('155','2012-09-03','Labor Day');

INSERT IGNORE INTO tbholiday VALUES ('156','2012-10-08','Columbu\'s Day');

INSERT IGNORE INTO tbholiday VALUES ('157','2012-11-11','Veterans Day');

INSERT IGNORE INTO tbholiday VALUES ('158','2012-11-22','Thanksgiving Day');



##
## TABELA: tbjuridicalperson
##
DROP TABLE IF EXISTS tbjuridicalperson ; 

CREATE TABLE IF NOT EXISTS `tbjuridicalperson` (
  `idjuridicalperson` int(11) NOT NULL AUTO_INCREMENT,
  `idperson` int(11) NOT NULL,
  `ein_cnpj` varchar(18) DEFAULT NULL,
  `iestadual` varchar(20) DEFAULT NULL,
  `contact_person` varchar(80) DEFAULT NULL,
  `observation` blob,
  PRIMARY KEY (`idjuridicalperson`),
  KEY `fk_tbjuridicalperson_tbperson1` (`idperson`),
  CONSTRAINT `fk_tbjuridicalperson_tbperson1` FOREIGN KEY (`idperson`) REFERENCES `tbperson` (`idperson`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 ; 

# Dados para a tabela: tbjuridicalperson

INSERT IGNORE INTO tbjuridicalperson VALUES ('1','60','111111111','','User','');



##
## TABELA: tblocation
##
DROP TABLE IF EXISTS tblocation ; 

CREATE TABLE IF NOT EXISTS `tblocation` (
  `idLocation` int(4) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  PRIMARY KEY (`idLocation`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ; 

# Dados para a tabela: tblocation



##
## TABELA: tblogos
##
DROP TABLE IF EXISTS tblogos ; 

CREATE TABLE IF NOT EXISTS `tblogos` (
  `idlogo` int(4) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `height` int(4) NOT NULL,
  `width` int(4) NOT NULL,
  `file_name` varchar(50) NOT NULL,
  PRIMARY KEY (`idlogo`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 ; 

# Dados para a tabela: tblogos

INSERT IGNORE INTO tblogos VALUES ('1','header','35','142','top_helpdezk.png');

INSERT IGNORE INTO tblogos VALUES ('2','login','70','285','login_helpdezk.png');

INSERT IGNORE INTO tblogos VALUES ('3','reports','40','162','reports_reports_helpdezk.jpg');



##
## TABELA: tbmodule
##
DROP TABLE IF EXISTS tbmodule ; 

CREATE TABLE IF NOT EXISTS `tbmodule` (
  `idmodule` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `index` int(11) DEFAULT NULL,
  `status` char(1) NOT NULL DEFAULT 'A',
  `path` varchar(20) DEFAULT NULL,
  `smarty` varchar(50) DEFAULT NULL,
  `class` varchar(50) DEFAULT NULL COMMENT 'Class use in css',
  PRIMARY KEY (`idmodule`),
  KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC ; 

# Dados para a tabela: tbmodule

INSERT IGNORE INTO tbmodule VALUES ('1','Admin','0','A','','','');

INSERT IGNORE INTO tbmodule VALUES ('2','Helpdezk','0','A','','','');

INSERT IGNORE INTO tbmodule VALUES ('3','Dashboard','0','A','','','');



##
## TABELA: tbnaturalperson
##
DROP TABLE IF EXISTS tbnaturalperson ; 

CREATE TABLE IF NOT EXISTS `tbnaturalperson` (
  `idnaturalperson` int(11) NOT NULL AUTO_INCREMENT,
  `idperson` int(11) NOT NULL,
  `ssn_cpf` varchar(15) DEFAULT NULL,
  `rg` varchar(20) DEFAULT NULL,
  `rgoexp` varchar(15) DEFAULT NULL,
  `dtbirth` date DEFAULT NULL,
  `mother` varchar(80) DEFAULT NULL,
  `father` varchar(80) DEFAULT NULL,
  `gender` varchar(1) DEFAULT NULL,
  PRIMARY KEY (`idnaturalperson`),
  KEY `fk_tbnaturalperson_tbperson1` (`idperson`),
  CONSTRAINT `fk_tbnaturalperson_tbperson1` FOREIGN KEY (`idperson`) REFERENCES `tbperson` (`idperson`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ; 

# Dados para a tabela: tbnaturalperson



##
## TABELA: tbnatureperson
##
DROP TABLE IF EXISTS tbnatureperson ; 

CREATE TABLE IF NOT EXISTS `tbnatureperson` (
  `idnatureperson` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL COMMENT 'fisica ou juridica\n',
  PRIMARY KEY (`idnatureperson`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 ; 

# Dados para a tabela: tbnatureperson

INSERT IGNORE INTO tbnatureperson VALUES ('1','natural');

INSERT IGNORE INTO tbnatureperson VALUES ('2','juridical');



##
## TABELA: tbneighborhood
##
DROP TABLE IF EXISTS tbneighborhood ; 

CREATE TABLE IF NOT EXISTS `tbneighborhood` (
  `idneighborhood` int(11) NOT NULL AUTO_INCREMENT,
  `idcity` int(11) NOT NULL,
  `name` varchar(60) DEFAULT NULL,
  PRIMARY KEY (`idneighborhood`),
  KEY `fk_tbneighborhood_tbcity1` (`idcity`),
  CONSTRAINT `fk_tbneighborhood_tbcity1` FOREIGN KEY (`idcity`) REFERENCES `tbcity` (`idcity`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=latin1 ; 

# Dados para a tabela: tbneighborhood

INSERT IGNORE INTO tbneighborhood VALUES ('1','1','Choose');

INSERT IGNORE INTO tbneighborhood VALUES ('26','2','Allapattah');

INSERT IGNORE INTO tbneighborhood VALUES ('27','2','Brickell');

INSERT IGNORE INTO tbneighborhood VALUES ('28','2','Buena Vista');

INSERT IGNORE INTO tbneighborhood VALUES ('29','2','Civic Center');

INSERT IGNORE INTO tbneighborhood VALUES ('30','2','Coconut Grove');

INSERT IGNORE INTO tbneighborhood VALUES ('31','2','Coral Way');

INSERT IGNORE INTO tbneighborhood VALUES ('32','2','Design District');

INSERT IGNORE INTO tbneighborhood VALUES ('33','2','Downtown ');

INSERT IGNORE INTO tbneighborhood VALUES ('34','2','Edgewater');

INSERT IGNORE INTO tbneighborhood VALUES ('35','2','Flagami  ');

INSERT IGNORE INTO tbneighborhood VALUES ('36','2','Grapeland Heights  ');

INSERT IGNORE INTO tbneighborhood VALUES ('37','2','Liberty City ');

INSERT IGNORE INTO tbneighborhood VALUES ('38','2','Little Haiti ');

INSERT IGNORE INTO tbneighborhood VALUES ('39','2','Little Havana');

INSERT IGNORE INTO tbneighborhood VALUES ('40','2','Lummus Park  ');

INSERT IGNORE INTO tbneighborhood VALUES ('41','2','Midtown');

INSERT IGNORE INTO tbneighborhood VALUES ('42','2','Omni  ');

INSERT IGNORE INTO tbneighborhood VALUES ('43','2','Overtown');

INSERT IGNORE INTO tbneighborhood VALUES ('44','2','Park West');

INSERT IGNORE INTO tbneighborhood VALUES ('45','2','The Roads');

INSERT IGNORE INTO tbneighborhood VALUES ('46','2','Upper East Side');

INSERT IGNORE INTO tbneighborhood VALUES ('47','2','Venetian Islands');

INSERT IGNORE INTO tbneighborhood VALUES ('48','2','Virginia Key  ');

INSERT IGNORE INTO tbneighborhood VALUES ('49','2','West Flagler  ');

INSERT IGNORE INTO tbneighborhood VALUES ('50','2','Wynwood ');

INSERT IGNORE INTO tbneighborhood VALUES ('51','3','Porto');

INSERT IGNORE INTO tbneighborhood VALUES ('52','3','Centro');

INSERT IGNORE INTO tbneighborhood VALUES ('57','3','Guabiroba');

INSERT IGNORE INTO tbneighborhood VALUES ('58','3','Fragata');

INSERT IGNORE INTO tbneighborhood VALUES ('59','3','Castilho');

INSERT IGNORE INTO tbneighborhood VALUES ('60','3','Simões Lopes');

INSERT IGNORE INTO tbneighborhood VALUES ('61','3','Cascata');

INSERT IGNORE INTO tbneighborhood VALUES ('62','3','Dunas');



##
## TABELA: tbpermission
##
DROP TABLE IF EXISTS tbpermission ; 

CREATE TABLE IF NOT EXISTS `tbpermission` (
  `idpermission` int(4) NOT NULL AUTO_INCREMENT,
  `idaccesstype` int(4) NOT NULL,
  `idprogram` int(4) NOT NULL,
  `idperson` int(4) NOT NULL,
  `allow` char(1) NOT NULL DEFAULT 'Y',
  PRIMARY KEY (`idpermission`),
  KEY `FK_tbpermission` (`idperson`),
  KEY `FK_tbpermission2` (`idaccesstype`),
  KEY `FK_tbpermission3` (`idprogram`),
  CONSTRAINT `FK_tbpermission` FOREIGN KEY (`idperson`) REFERENCES `tbperson` (`idperson`),
  CONSTRAINT `FK_tbpermission2` FOREIGN KEY (`idaccesstype`) REFERENCES `tbaccesstype` (`idaccesstype`),
  CONSTRAINT `FK_tbpermission3` FOREIGN KEY (`idprogram`) REFERENCES `tbprogram` (`idprogram`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC ; 

# Dados para a tabela: tbpermission



##
## TABELA: tbperson
##
DROP TABLE IF EXISTS tbperson ; 

CREATE TABLE IF NOT EXISTS `tbperson` (
  `idperson` int(11) NOT NULL AUTO_INCREMENT,
  `idtypelogin` int(11) NOT NULL,
  `idtypeperson` int(11) NOT NULL,
  `idnatureperson` int(11) NOT NULL,
  `idtheme` int(11) NOT NULL,
  `name` varchar(60) NOT NULL,
  `login` varchar(45) DEFAULT NULL,
  `password` varchar(45) DEFAULT NULL,
  `email` varchar(45) DEFAULT NULL,
  `dtcreate` datetime DEFAULT NULL,
  `status` char(1) NOT NULL DEFAULT 'A',
  `user_vip` char(1) NOT NULL DEFAULT 'N',
  `phone_number` varchar(30) DEFAULT NULL,
  `cel_phone` varchar(30) DEFAULT NULL,
  `branch_number` varchar(10) DEFAULT NULL,
  `fax` varchar(30) DEFAULT NULL,
  `cod_location` int(4) DEFAULT NULL,
  `time_value` float DEFAULT NULL,
  `overtime` float DEFAULT NULL,
  `change_pass` int(1) DEFAULT '0',
  `token` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`idperson`),
  KEY `fk_tbperson_tblogintype` (`idtypelogin`),
  KEY `fk_tbperson_tbtypeperson1` (`idtypeperson`),
  KEY `fk_tbperson_tbtheme1` (`idtheme`),
  KEY `idx_tbperson_name` (`name`),
  KEY `fk_tbperson_tbnatureperson1` (`idnatureperson`),
  CONSTRAINT `fk_tbperson_tblogintype` FOREIGN KEY (`idtypelogin`) REFERENCES `tbtypelogin` (`idtypelogin`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_tbperson_tbnatureperson1` FOREIGN KEY (`idnatureperson`) REFERENCES `tbnatureperson` (`idnatureperson`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_tbperson_tbtheme1` FOREIGN KEY (`idtheme`) REFERENCES `tbtheme` (`idtheme`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_tbperson_tbtypeperson1` FOREIGN KEY (`idtypeperson`) REFERENCES `tbtypeperson` (`idtypeperson`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC ; 

# Dados para a tabela: tbperson

INSERT IGNORE INTO tbperson VALUES ('1','3','1','1','1','root','root','81dc9bdb52d04dc20036dbd8313ed055','rogerio@pipegrep.com.br','2011-08-03 18:52:31','A','N','','','','','0','0','0','0','');

INSERT IGNORE INTO tbperson VALUES ('60','3','4','2','1','T.I Masters','','81dc9bdb52d04dc20036dbd8313ed055','timasters@teste.com','2012-08-20 15:29:19','A','N','1154587451','','','1154245784','0','0','0','0','');

INSERT IGNORE INTO tbperson VALUES ('61','3','6','1','1','Information Technology Support','','','','0000-00-00 00:00:00','A','N','','','','','0','0','0','0','');



##
## TABELA: tbperson_plus
##
DROP TABLE IF EXISTS tbperson_plus ; 

CREATE TABLE IF NOT EXISTS `tbperson_plus` (
  `idpersonplus` int(11) NOT NULL AUTO_INCREMENT,
  `idtypepersonplus` int(11) DEFAULT NULL,
  `idperson` int(11) DEFAULT NULL,
  `login` varchar(100) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`idpersonplus`),
  UNIQUE KEY `NewIndex1` (`idtypepersonplus`,`idperson`),
  KEY `FK_tbperson_plus_person_FK` (`idperson`),
  CONSTRAINT `FK_tbperson_plus` FOREIGN KEY (`idtypepersonplus`) REFERENCES `tbtypeperson_plus` (`idtypepersonplus`) ON DELETE CASCADE,
  CONSTRAINT `FK_tbperson_plus_person_FK` FOREIGN KEY (`idperson`) REFERENCES `tbperson` (`idperson`) ON DELETE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC ; 

# Dados para a tabela: tbperson_plus



##
## TABELA: tbpersontypes
##
DROP TABLE IF EXISTS tbpersontypes ; 

CREATE TABLE IF NOT EXISTS `tbpersontypes` (
  `idperson` int(11) NOT NULL,
  `idtypeperson` int(11) NOT NULL,
  PRIMARY KEY (`idperson`,`idtypeperson`),
  KEY `idtypeperson` (`idtypeperson`),
  CONSTRAINT `tbpersontypes_ibfk_1` FOREIGN KEY (`idperson`) REFERENCES `tbperson` (`idperson`),
  CONSTRAINT `tbpersontypes_ibfk_2` FOREIGN KEY (`idtypeperson`) REFERENCES `tbtypeperson` (`idtypeperson`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ; 

# Dados para a tabela: tbpersontypes



##
## TABELA: tbprogram
##
DROP TABLE IF EXISTS tbprogram ; 

CREATE TABLE IF NOT EXISTS `tbprogram` (
  `idprogram` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `controller` varchar(20) NOT NULL,
  `idprogramcategory` int(11) NOT NULL,
  `index` int(11) DEFAULT NULL,
  `status` char(1) NOT NULL,
  `smarty` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`idprogram`),
  KEY `fk_tbprogramcategory` (`idprogramcategory`),
  CONSTRAINT `FK_tbprogramct` FOREIGN KEY (`idprogramcategory`) REFERENCES `tbprogramcategory` (`idprogramcategory`)
) ENGINE=InnoDB AUTO_INCREMENT=75 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC ; 

# Dados para a tabela: tbprogram

INSERT IGNORE INTO tbprogram VALUES ('1','People & Companies','person/','1','0','A','pgr_people');

INSERT IGNORE INTO tbprogram VALUES ('2','Holidays','holidays/','1','0','A','pgr_holidays');

INSERT IGNORE INTO tbprogram VALUES ('3','Programs','program/','1','0','A','pgr_programs');

INSERT IGNORE INTO tbprogram VALUES ('4','Modules','modules/','1','0','A','pgr_modules');

INSERT IGNORE INTO tbprogram VALUES ('5','Status','status/','2','0','A','pgr_status');

INSERT IGNORE INTO tbprogram VALUES ('6','Priority','priority/','2','0','A','pgr_priority');

INSERT IGNORE INTO tbprogram VALUES ('7','Groups','groups/','2','0','A','pgr_groups');

INSERT IGNORE INTO tbprogram VALUES ('8','Evaluation','evaluation/','2','0','A','pgr_evaluation');

INSERT IGNORE INTO tbprogram VALUES ('9','Departments','department/','2','0','A','pgr_departments');

INSERT IGNORE INTO tbprogram VALUES ('10','Cost Center','costcenter/','2','0','A','pgr_cost_center');

INSERT IGNORE INTO tbprogram VALUES ('12','Services','services/','2','0','A','pgr_services');

INSERT IGNORE INTO tbprogram VALUES ('16','Request Reason','reason/','2','0','A','pgr_req_reason');

INSERT IGNORE INTO tbprogram VALUES ('23','Email Configuration','emailconfig/','2','0','A','pgr_email_config');

INSERT IGNORE INTO tbprogram VALUES ('25','System Features','features/','2','0','A','pgr_sys_features');

INSERT IGNORE INTO tbprogram VALUES ('32','Type Person Permission','typepersonpermission','1','0','A','pgr_type_permission');

INSERT IGNORE INTO tbprogram VALUES ('51','Person Report','relPessoa/','13','0','A','pgr_person_report');

INSERT IGNORE INTO tbprogram VALUES ('59','Downloads','downloads/','1','0','A','pgr_downloads');

INSERT IGNORE INTO tbprogram VALUES ('61','Logos','logos/','17','0','A','pgr_logos');

INSERT IGNORE INTO tbprogram VALUES ('64','Requests Report','relRequests','13','0','A','pgr_req_reports');

INSERT IGNORE INTO tbprogram VALUES ('65','Importar catalogo de servi�os','importservices/','18','0','A','pgr_import_services');

INSERT IGNORE INTO tbprogram VALUES ('66','Operator Average ResponseTime','relOpeAverRespTime','13','0','A','pgr_ope_aver_resptime');

INSERT IGNORE INTO tbprogram VALUES ('67','Reject Requests','relReject','13','0','A','pgr_rejects_request');

INSERT IGNORE INTO tbprogram VALUES ('68','Requests by Department','relDepartments','13','0','A','pgr_request_department');

INSERT IGNORE INTO tbprogram VALUES ('69','Requests by Status','relStatus','13','0','A','pgr_request_status');

INSERT IGNORE INTO tbprogram VALUES ('70','Summarized by Department','relSumDepartment','13','0','A','pgr_summarized_department');

INSERT IGNORE INTO tbprogram VALUES ('71','Summarized by Operator','relOperator','13','0','A','pgr_summarized_operator');

INSERT IGNORE INTO tbprogram VALUES ('72','User Satisfaction','relUserSatisfaction','13','0','A','pgr_user_satisfaction');

INSERT IGNORE INTO tbprogram VALUES ('73','Warnings','warnings/','2','0','A','pgr_warnings');

INSERT IGNORE INTO tbprogram VALUES ('74','Widgets','widget/','19','0','A','pgr_dash_widgets');



##
## TABELA: tbprogramcategory
##
DROP TABLE IF EXISTS tbprogramcategory ; 

CREATE TABLE IF NOT EXISTS `tbprogramcategory` (
  `idprogramcategory` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `idmodule` int(11) NOT NULL,
  `smarty` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`idprogramcategory`),
  KEY `FK_tbprogramcategory` (`idmodule`),
  CONSTRAINT `FK_tbprogramcategory` FOREIGN KEY (`idmodule`) REFERENCES `tbmodule` (`idmodule`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC ; 

# Dados para a tabela: tbprogramcategory

INSERT IGNORE INTO tbprogramcategory VALUES ('1','Records','1','cat_records');

INSERT IGNORE INTO tbprogramcategory VALUES ('2','Records','2','cat_records');

INSERT IGNORE INTO tbprogramcategory VALUES ('13','Reports','2','cat_reports');

INSERT IGNORE INTO tbprogramcategory VALUES ('17','Config','1','cat_config');

INSERT IGNORE INTO tbprogramcategory VALUES ('18','Database','2','cat_database');

INSERT IGNORE INTO tbprogramcategory VALUES ('19','Records','3','cat_records');



##
## TABELA: tbstate
##
DROP TABLE IF EXISTS tbstate ; 

CREATE TABLE IF NOT EXISTS `tbstate` (
  `idstate` int(11) NOT NULL AUTO_INCREMENT,
  `idcountry` int(11) NOT NULL,
  `abbr` varchar(15) DEFAULT NULL,
  `name` varchar(60) DEFAULT NULL,
  PRIMARY KEY (`idstate`),
  KEY `fk_tbstate_tbcountry1` (`idcountry`),
  CONSTRAINT `fk_tbstate_tbcountry1` FOREIGN KEY (`idcountry`) REFERENCES `tbcountry` (`idcountry`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=98 DEFAULT CHARSET=latin1 ; 

# Dados para a tabela: tbstate

INSERT IGNORE INTO tbstate VALUES ('1','1','AA','Choose');

INSERT IGNORE INTO tbstate VALUES ('2','227','AK','Alaska');

INSERT IGNORE INTO tbstate VALUES ('3','227','AL','Alabama');

INSERT IGNORE INTO tbstate VALUES ('4','227','AS','American Samoa');

INSERT IGNORE INTO tbstate VALUES ('5','227','AZ','Arizona');

INSERT IGNORE INTO tbstate VALUES ('6','227','AR','Arkansas');

INSERT IGNORE INTO tbstate VALUES ('7','227','CA','California');

INSERT IGNORE INTO tbstate VALUES ('8','227','CO','Colorado');

INSERT IGNORE INTO tbstate VALUES ('9','227','CT','Connecticut');

INSERT IGNORE INTO tbstate VALUES ('10','227','DE','Delaware');

INSERT IGNORE INTO tbstate VALUES ('11','227','DC','District of Columbia');

INSERT IGNORE INTO tbstate VALUES ('12','227','FM','Federated States of Micronesia');

INSERT IGNORE INTO tbstate VALUES ('13','227','FL','Florida');

INSERT IGNORE INTO tbstate VALUES ('14','227','GA','Georgia');

INSERT IGNORE INTO tbstate VALUES ('15','227','GU','Guam');

INSERT IGNORE INTO tbstate VALUES ('16','227','HI','Hawaii');

INSERT IGNORE INTO tbstate VALUES ('17','227','ID','Idaho');

INSERT IGNORE INTO tbstate VALUES ('18','227','IL','Illinois');

INSERT IGNORE INTO tbstate VALUES ('19','227','IN','Indiana');

INSERT IGNORE INTO tbstate VALUES ('20','227','IA','Iowa');

INSERT IGNORE INTO tbstate VALUES ('21','227','KS','Kansas');

INSERT IGNORE INTO tbstate VALUES ('22','227','KY','Kentucky');

INSERT IGNORE INTO tbstate VALUES ('23','227','LA','Louisiana');

INSERT IGNORE INTO tbstate VALUES ('24','227','ME','Maine');

INSERT IGNORE INTO tbstate VALUES ('25','227','MH','Marshall Islands');

INSERT IGNORE INTO tbstate VALUES ('26','227','MD','Maryland');

INSERT IGNORE INTO tbstate VALUES ('27','227','MA','Massachusetts');

INSERT IGNORE INTO tbstate VALUES ('28','227','MI','Michigan');

INSERT IGNORE INTO tbstate VALUES ('29','227','MN','Minnesota');

INSERT IGNORE INTO tbstate VALUES ('30','227','MS','Mississippi');

INSERT IGNORE INTO tbstate VALUES ('31','227','MO','Missouri');

INSERT IGNORE INTO tbstate VALUES ('32','227','MT','Montana');

INSERT IGNORE INTO tbstate VALUES ('33','227','NE','Nebraska');

INSERT IGNORE INTO tbstate VALUES ('34','227','NV','Nevada');

INSERT IGNORE INTO tbstate VALUES ('35','227','NH','New Hampshire');

INSERT IGNORE INTO tbstate VALUES ('36','227','NJ','New Jersey');

INSERT IGNORE INTO tbstate VALUES ('37','227','NM','New Mexico');

INSERT IGNORE INTO tbstate VALUES ('38','227','NY','New York');

INSERT IGNORE INTO tbstate VALUES ('39','227','NC','North Carolina');

INSERT IGNORE INTO tbstate VALUES ('40','227','ND','North Dakota');

INSERT IGNORE INTO tbstate VALUES ('41','227','MP','Northern Mariana Islands');

INSERT IGNORE INTO tbstate VALUES ('42','227','OH','Ohio');

INSERT IGNORE INTO tbstate VALUES ('43','227','OK','Oklahoma');

INSERT IGNORE INTO tbstate VALUES ('44','227','OR','Oregon');

INSERT IGNORE INTO tbstate VALUES ('45','227','PW','Palau');

INSERT IGNORE INTO tbstate VALUES ('46','227','PA','Pennsylvania');

INSERT IGNORE INTO tbstate VALUES ('47','227','PR','Puerto Rico');

INSERT IGNORE INTO tbstate VALUES ('48','227','RI','Rhode Island');

INSERT IGNORE INTO tbstate VALUES ('49','227','SC','South Carolina');

INSERT IGNORE INTO tbstate VALUES ('50','227','SD','South Dakota');

INSERT IGNORE INTO tbstate VALUES ('51','227','TN','Tennessee');

INSERT IGNORE INTO tbstate VALUES ('52','227','TX','Texas');

INSERT IGNORE INTO tbstate VALUES ('53','227','UT','Utah');

INSERT IGNORE INTO tbstate VALUES ('54','227','VT','Vermont');

INSERT IGNORE INTO tbstate VALUES ('55','227','VI','Virgin Islands');

INSERT IGNORE INTO tbstate VALUES ('56','227','VA','Virginia');

INSERT IGNORE INTO tbstate VALUES ('57','227','WA','Washington');

INSERT IGNORE INTO tbstate VALUES ('58','227','WV','West Virginia');

INSERT IGNORE INTO tbstate VALUES ('59','227','WI','Wisconsin');

INSERT IGNORE INTO tbstate VALUES ('60','227','WY','Wyoming');

INSERT IGNORE INTO tbstate VALUES ('61','227','AE','Armed Forces Africa');

INSERT IGNORE INTO tbstate VALUES ('62','227','AA','Armed Forces Americas (except Canada)');

INSERT IGNORE INTO tbstate VALUES ('63','227','AE','Armed Forces Canada');

INSERT IGNORE INTO tbstate VALUES ('64','227','AE','Armed Forces Europe');

INSERT IGNORE INTO tbstate VALUES ('65','227','AE','Armed Forces Middle East');

INSERT IGNORE INTO tbstate VALUES ('66','227','AP','Armed Forces Pacific');

INSERT IGNORE INTO tbstate VALUES ('67','31','AC','Acre');

INSERT IGNORE INTO tbstate VALUES ('68','31','AL','Alagoas');

INSERT IGNORE INTO tbstate VALUES ('69','31','AM','Amazonas');

INSERT IGNORE INTO tbstate VALUES ('70','31','AP','Amapa');

INSERT IGNORE INTO tbstate VALUES ('71','31','BA','Bahia');

INSERT IGNORE INTO tbstate VALUES ('72','31','CE','Ceara');

INSERT IGNORE INTO tbstate VALUES ('73','31','DF','Distrito Federal');

INSERT IGNORE INTO tbstate VALUES ('74','31','ES','Espirito Santo');

INSERT IGNORE INTO tbstate VALUES ('75','31','GO','Goias');

INSERT IGNORE INTO tbstate VALUES ('76','31','MA','Maranhao');

INSERT IGNORE INTO tbstate VALUES ('77','31','MG','Minas Gerais');

INSERT IGNORE INTO tbstate VALUES ('78','31','MS','Mato Grosso do Sul');

INSERT IGNORE INTO tbstate VALUES ('79','31','MT','Mato Grosso');

INSERT IGNORE INTO tbstate VALUES ('80','31','PA','Para');

INSERT IGNORE INTO tbstate VALUES ('81','31','PB','Paraiba');

INSERT IGNORE INTO tbstate VALUES ('82','31','PE','Pernambuco');

INSERT IGNORE INTO tbstate VALUES ('83','31','PI','Piaui');

INSERT IGNORE INTO tbstate VALUES ('84','31','PR','Parana');

INSERT IGNORE INTO tbstate VALUES ('85','31','RJ','Rio de Janeiro');

INSERT IGNORE INTO tbstate VALUES ('86','31','RN','Rio Grande do Norte');

INSERT IGNORE INTO tbstate VALUES ('87','31','RO','Rondonia');

INSERT IGNORE INTO tbstate VALUES ('88','31','RR','Roraima');

INSERT IGNORE INTO tbstate VALUES ('89','31','RS','Rio Grande do Sul');

INSERT IGNORE INTO tbstate VALUES ('90','31','SC','Santa Catarina');

INSERT IGNORE INTO tbstate VALUES ('91','31','SE','Sergipe');

INSERT IGNORE INTO tbstate VALUES ('92','31','SP','Sao Paulo');

INSERT IGNORE INTO tbstate VALUES ('93','31','TO','Tocantins');

INSERT IGNORE INTO tbstate VALUES ('94','11','BA','Buenos Aires');

INSERT IGNORE INTO tbstate VALUES ('95','74','PR','Paris');

INSERT IGNORE INTO tbstate VALUES ('96','211','NS','New State');

INSERT IGNORE INTO tbstate VALUES ('97','23','OG','Orgrimmar');



##
## TABELA: tbstreet
##
DROP TABLE IF EXISTS tbstreet ; 

CREATE TABLE IF NOT EXISTS `tbstreet` (
  `idstreet` int(11) NOT NULL AUTO_INCREMENT,
  `idtypestreet` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`idstreet`),
  KEY `fk_tbstreet_tbtypestreet1` (`idtypestreet`),
  CONSTRAINT `fk_tbstreet_tbtypestreet1` FOREIGN KEY (`idtypestreet`) REFERENCES `tbtypestreet` (`idtypestreet`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=latin1 ; 

# Dados para a tabela: tbstreet

INSERT IGNORE INTO tbstreet VALUES ('1','2','Choose');

INSERT IGNORE INTO tbstreet VALUES ('2','2','Brickell');

INSERT IGNORE INTO tbstreet VALUES ('3','2','Collins');

INSERT IGNORE INTO tbstreet VALUES ('4','106','Felix da Cunha');

INSERT IGNORE INTO tbstreet VALUES ('6','106','Bar�o de Santa Tecla');

INSERT IGNORE INTO tbstreet VALUES ('7','106','Gon�alves Chaves');

INSERT IGNORE INTO tbstreet VALUES ('9','1','Teste');

INSERT IGNORE INTO tbstreet VALUES ('10','106','Andrade Neves');

INSERT IGNORE INTO tbstreet VALUES ('11','106','Chuck Norris');

INSERT IGNORE INTO tbstreet VALUES ('12','106','Keep calm we have a hulk');

INSERT IGNORE INTO tbstreet VALUES ('13','106','Maestro Mendanha');

INSERT IGNORE INTO tbstreet VALUES ('14','1','Félix da Cunha');

INSERT IGNORE INTO tbstreet VALUES ('15','106','Coronel Onofre Pires');

INSERT IGNORE INTO tbstreet VALUES ('16','106','Marechal Deodoro');

INSERT IGNORE INTO tbstreet VALUES ('17','106','General Teles');

INSERT IGNORE INTO tbstreet VALUES ('26','106','Major Cícero de Goes Monteiro');

INSERT IGNORE INTO tbstreet VALUES ('27','106','Gomes Carneiro');

INSERT IGNORE INTO tbstreet VALUES ('28','106','Três de Maio');

INSERT IGNORE INTO tbstreet VALUES ('29','106','Padre Anchieta');

INSERT IGNORE INTO tbstreet VALUES ('30','106','Santa Cruz');

INSERT IGNORE INTO tbstreet VALUES ('36','106','Padre Felício');

INSERT IGNORE INTO tbstreet VALUES ('37','4','Example Street');



##
## TABELA: tbteste
##
DROP TABLE IF EXISTS tbteste ; 

CREATE TABLE IF NOT EXISTS `tbteste` (
  `idex` int(4) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `age` int(3) NOT NULL,
  PRIMARY KEY (`idex`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=latin1 ; 

# Dados para a tabela: tbteste

INSERT IGNORE INTO tbteste VALUES ('1','teste 2','123');

INSERT IGNORE INTO tbteste VALUES ('2','Alejandro','20');

INSERT IGNORE INTO tbteste VALUES ('3','Deivisson','26');

INSERT IGNORE INTO tbteste VALUES ('5','Fulano 5','54');



##
## TABELA: tbtheme
##
DROP TABLE IF EXISTS tbtheme ; 

CREATE TABLE IF NOT EXISTS `tbtheme` (
  `idtheme` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`idtheme`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 ; 

# Dados para a tabela: tbtheme

INSERT IGNORE INTO tbtheme VALUES ('1','Mq Theme');

INSERT IGNORE INTO tbtheme VALUES ('2','Usbs Theme');



##
## TABELA: tbtypeaddress
##
DROP TABLE IF EXISTS tbtypeaddress ; 

CREATE TABLE IF NOT EXISTS `tbtypeaddress` (
  `idtypeaddress` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`idtypeaddress`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 ; 

# Dados para a tabela: tbtypeaddress

INSERT IGNORE INTO tbtypeaddress VALUES ('1','Choose');

INSERT IGNORE INTO tbtypeaddress VALUES ('2','residential');

INSERT IGNORE INTO tbtypeaddress VALUES ('3','commercial');



##
## TABELA: tbtypelogin
##
DROP TABLE IF EXISTS tbtypelogin ; 

CREATE TABLE IF NOT EXISTS `tbtypelogin` (
  `idtypelogin` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`idtypelogin`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 ; 

# Dados para a tabela: tbtypelogin

INSERT IGNORE INTO tbtypelogin VALUES ('1','POP');

INSERT IGNORE INTO tbtypelogin VALUES ('2','AD');

INSERT IGNORE INTO tbtypelogin VALUES ('3','HD');



##
## TABELA: tbtypeperson
##
DROP TABLE IF EXISTS tbtypeperson ; 

CREATE TABLE IF NOT EXISTS `tbtypeperson` (
  `idtypeperson` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`idtypeperson`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1 ; 

# Dados para a tabela: tbtypeperson

INSERT IGNORE INTO tbtypeperson VALUES ('1','admin');

INSERT IGNORE INTO tbtypeperson VALUES ('2','user');

INSERT IGNORE INTO tbtypeperson VALUES ('3','operator');

INSERT IGNORE INTO tbtypeperson VALUES ('4','costumer');

INSERT IGNORE INTO tbtypeperson VALUES ('5','partner');

INSERT IGNORE INTO tbtypeperson VALUES ('6','group');



##
## TABELA: tbtypeperson_plus
##
DROP TABLE IF EXISTS tbtypeperson_plus ; 

CREATE TABLE IF NOT EXISTS `tbtypeperson_plus` (
  `idtypepersonplus` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`idtypepersonplus`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 ; 

# Dados para a tabela: tbtypeperson_plus

INSERT IGNORE INTO tbtypeperson_plus VALUES ('1','Google');

INSERT IGNORE INTO tbtypeperson_plus VALUES ('2','Facebook');



##
## TABELA: tbtypepersonpermission
##
DROP TABLE IF EXISTS tbtypepersonpermission ; 

CREATE TABLE IF NOT EXISTS `tbtypepersonpermission` (
  `idpermissiongroup` int(4) NOT NULL AUTO_INCREMENT,
  `idprogram` int(4) NOT NULL,
  `idtypeperson` int(4) NOT NULL,
  `idaccesstype` int(4) NOT NULL,
  `allow` char(1) NOT NULL DEFAULT 'Y',
  PRIMARY KEY (`idpermissiongroup`),
  KEY `FK_tbpermissiongroup` (`idprogram`),
  KEY `FK_tbpermissiongroup2` (`idtypeperson`),
  KEY `FK_tbpermissiongroup3` (`idaccesstype`),
  CONSTRAINT `FK_tbpermissiongroup` FOREIGN KEY (`idprogram`) REFERENCES `tbprogram` (`idprogram`),
  CONSTRAINT `FK_tbpermissiongroup2` FOREIGN KEY (`idtypeperson`) REFERENCES `tbtypeperson` (`idtypeperson`),
  CONSTRAINT `FK_tbpermissiongroup3` FOREIGN KEY (`idaccesstype`) REFERENCES `tbaccesstype` (`idaccesstype`)
) ENGINE=InnoDB AUTO_INCREMENT=863 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC ; 

# Dados para a tabela: tbtypepersonpermission

INSERT IGNORE INTO tbtypepersonpermission VALUES ('1','1','1','1','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('2','1','2','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('3','1','3','1','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('4','1','4','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('5','1','5','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('6','1','1','2','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('7','1','2','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('8','1','3','2','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('9','1','4','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('10','1','5','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('11','1','1','3','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('12','1','2','3','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('13','1','3','3','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('14','1','4','3','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('15','1','5','3','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('16','1','1','4','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('17','1','2','4','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('18','1','3','4','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('19','1','4','4','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('20','1','5','4','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('21','2','1','1','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('22','2','2','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('23','2','3','1','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('24','2','4','1','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('25','2','5','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('26','2','1','2','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('27','2','2','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('28','2','3','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('29','2','4','2','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('30','2','5','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('31','2','1','3','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('32','2','2','3','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('33','2','3','3','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('34','2','4','3','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('35','2','5','3','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('36','2','1','4','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('37','2','2','4','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('38','2','3','4','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('39','2','4','4','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('40','2','5','4','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('41','3','1','1','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('42','3','2','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('43','3','3','1','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('44','3','4','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('45','3','5','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('46','3','1','2','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('47','3','2','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('48','3','3','2','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('49','3','4','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('50','3','5','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('51','3','1','3','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('52','3','2','3','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('53','3','3','3','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('54','3','4','3','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('55','3','5','3','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('56','3','1','4','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('57','3','2','4','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('58','3','3','4','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('59','3','4','4','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('60','3','5','4','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('61','4','1','1','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('62','4','2','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('63','4','3','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('64','4','4','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('65','4','5','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('66','4','1','2','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('67','4','2','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('68','4','3','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('69','4','4','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('70','4','5','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('71','4','1','3','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('72','4','2','3','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('73','4','3','3','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('74','4','4','3','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('75','4','5','3','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('76','4','1','4','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('77','4','2','4','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('78','4','3','4','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('79','4','4','4','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('80','4','5','4','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('81','5','1','1','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('82','5','2','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('83','5','3','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('84','5','4','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('85','5','5','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('86','5','1','2','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('87','5','2','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('88','5','3','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('89','5','4','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('90','5','5','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('91','5','1','3','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('92','5','2','3','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('93','5','3','3','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('94','5','4','3','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('95','5','5','3','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('96','5','1','4','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('97','5','2','4','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('98','5','3','4','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('99','5','4','4','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('100','5','5','4','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('101','6','1','1','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('102','6','2','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('103','6','3','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('104','6','4','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('105','6','5','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('106','6','1','2','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('107','6','2','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('108','6','3','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('109','6','4','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('110','6','5','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('111','6','1','3','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('112','6','2','3','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('113','6','3','3','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('114','6','4','3','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('115','6','5','3','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('116','6','1','4','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('117','6','2','4','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('118','6','3','4','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('119','6','4','4','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('120','6','5','4','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('121','7','1','1','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('122','7','2','1','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('123','7','3','1','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('124','7','4','1','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('125','7','5','1','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('126','7','1','2','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('127','7','2','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('128','7','3','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('129','7','4','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('130','7','5','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('131','7','1','3','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('132','7','2','3','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('133','7','3','3','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('134','7','4','3','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('135','7','5','3','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('136','7','1','4','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('137','7','2','4','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('138','7','3','4','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('139','7','4','4','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('140','7','5','4','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('141','8','1','1','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('142','8','2','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('143','8','3','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('144','8','4','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('145','8','5','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('146','8','1','2','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('147','8','2','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('148','8','3','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('149','8','4','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('150','8','5','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('151','8','1','3','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('152','8','2','3','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('153','8','3','3','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('154','8','4','3','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('155','8','5','3','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('156','8','1','4','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('157','8','2','4','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('158','8','3','4','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('159','8','4','4','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('160','8','5','4','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('161','9','1','1','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('162','9','2','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('163','9','3','1','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('164','9','4','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('165','9','5','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('166','9','1','2','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('167','9','2','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('168','9','3','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('169','9','4','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('170','9','5','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('171','9','1','3','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('172','9','2','3','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('173','9','3','3','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('174','9','4','3','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('175','9','5','3','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('176','9','1','4','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('177','9','2','4','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('178','9','3','4','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('179','9','4','4','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('180','9','5','4','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('181','10','1','1','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('182','10','2','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('183','10','3','1','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('184','10','4','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('185','10','5','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('186','10','1','2','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('187','10','2','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('188','10','3','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('189','10','4','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('190','10','5','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('191','10','1','3','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('192','10','2','3','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('193','10','3','3','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('194','10','4','3','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('195','10','5','3','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('196','10','1','4','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('197','10','2','4','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('198','10','3','4','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('199','10','4','4','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('200','10','5','4','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('221','12','1','1','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('222','12','2','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('223','12','3','1','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('224','12','4','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('225','12','5','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('226','12','1','2','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('227','12','2','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('228','12','3','2','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('229','12','4','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('230','12','5','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('231','12','1','3','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('232','12','2','3','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('233','12','3','3','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('234','12','4','3','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('235','12','5','3','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('236','12','1','4','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('237','12','2','4','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('238','12','3','4','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('239','12','4','4','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('240','12','5','4','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('281','16','1','1','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('282','16','2','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('283','16','3','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('284','16','4','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('285','16','5','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('286','16','1','2','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('287','16','2','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('288','16','3','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('289','16','4','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('290','16','5','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('291','16','1','3','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('292','16','2','3','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('293','16','3','3','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('294','16','4','3','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('295','16','5','3','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('296','16','1','4','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('297','16','2','4','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('298','16','3','4','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('299','16','4','4','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('300','16','5','4','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('341','23','1','1','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('342','23','2','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('343','23','3','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('344','23','4','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('345','23','5','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('346','23','1','2','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('347','23','2','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('348','23','3','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('349','23','4','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('350','23','5','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('351','23','1','3','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('352','23','2','3','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('353','23','3','3','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('354','23','4','3','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('355','23','5','3','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('356','23','1','4','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('357','23','2','4','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('358','23','3','4','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('359','23','4','4','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('360','23','5','4','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('361','25','1','1','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('362','25','2','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('363','25','3','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('364','25','4','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('365','25','5','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('366','25','1','2','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('367','25','2','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('368','25','3','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('369','25','4','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('370','25','5','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('371','25','1','3','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('372','25','2','3','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('373','25','3','3','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('374','25','4','3','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('375','25','5','3','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('376','25','1','4','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('377','25','2','4','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('378','25','3','4','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('379','25','4','4','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('380','25','5','4','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('381','2','1','5','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('382','2','2','5','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('383','2','3','5','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('384','2','4','5','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('385','2','5','5','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('386','32','1','1','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('387','32','1','2','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('388','32','1','3','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('487','51','1','1','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('488','51','2','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('489','51','3','1','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('490','51','4','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('491','51','5','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('492','51','1','5','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('493','51','2','5','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('494','51','3','5','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('495','51','4','5','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('496','51','5','5','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('497','51','1','6','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('498','51','2','6','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('499','51','3','6','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('500','51','4','6','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('501','51','5','6','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('552','59','1','1','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('553','59','2','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('554','59','3','1','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('555','59','4','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('556','59','5','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('557','59','1','2','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('558','59','2','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('559','59','3','2','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('560','59','4','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('561','59','5','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('562','59','1','3','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('563','59','2','3','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('564','59','3','3','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('565','59','4','3','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('566','59','5','3','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('567','59','1','4','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('568','59','2','4','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('569','59','3','4','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('570','59','4','4','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('571','59','5','4','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('592','61','1','1','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('593','61','2','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('594','61','3','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('595','61','4','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('596','61','5','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('597','61','1','2','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('598','61','2','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('599','61','3','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('600','61','4','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('601','61','5','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('602','61','1','3','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('603','61','2','3','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('604','61','3','3','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('605','61','4','3','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('606','61','5','3','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('607','61','1','4','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('608','61','2','4','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('609','61','3','4','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('610','61','4','4','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('611','61','5','4','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('660','64','1','1','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('661','64','2','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('662','64','3','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('663','64','4','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('664','64','5','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('665','64','6','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('666','64','1','2','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('667','64','2','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('668','64','3','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('669','64','4','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('670','64','5','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('671','64','6','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('672','64','1','3','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('673','64','2','3','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('674','64','3','3','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('675','64','4','3','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('676','64','5','3','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('677','64','6','3','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('678','64','1','5','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('679','64','2','5','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('680','64','3','5','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('681','64','4','5','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('682','64','5','5','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('683','64','6','5','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('684','65','1','1','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('685','65','2','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('686','65','3','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('687','65','4','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('688','65','5','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('689','65','6','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('690','65','1','2','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('691','65','2','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('692','65','3','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('693','65','4','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('694','65','5','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('695','65','6','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('696','65','1','3','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('697','65','2','3','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('698','65','3','3','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('699','65','4','3','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('700','65','5','3','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('701','65','6','3','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('702','65','1','4','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('703','65','2','4','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('704','65','3','4','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('705','65','4','4','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('706','65','5','4','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('707','65','6','4','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('708','65','1','5','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('709','65','2','5','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('710','65','3','5','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('711','65','4','5','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('712','65','5','5','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('713','65','6','5','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('714','66','1','1','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('715','66','2','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('716','66','3','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('717','66','4','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('718','66','5','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('719','66','6','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('720','66','1','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('721','66','2','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('722','66','3','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('723','66','4','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('724','66','5','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('725','66','6','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('726','66','1','5','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('727','66','2','5','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('728','66','3','5','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('729','66','4','5','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('730','66','5','5','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('731','66','6','5','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('732','67','1','1','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('733','67','2','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('734','67','3','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('735','67','4','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('736','67','5','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('737','67','6','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('738','67','1','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('739','67','2','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('740','67','3','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('741','67','4','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('742','67','5','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('743','67','6','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('744','67','1','5','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('745','67','2','5','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('746','67','3','5','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('747','67','4','5','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('748','67','5','5','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('749','67','6','5','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('750','68','1','1','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('751','68','2','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('752','68','3','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('753','68','4','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('754','68','5','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('755','68','6','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('756','68','1','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('757','68','2','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('758','68','3','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('759','68','4','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('760','68','5','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('761','68','6','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('762','68','1','5','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('763','68','2','5','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('764','68','3','5','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('765','68','4','5','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('766','68','5','5','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('767','68','6','5','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('768','69','1','1','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('769','69','2','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('770','69','3','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('771','69','4','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('772','69','5','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('773','69','6','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('774','69','1','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('775','69','2','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('776','69','3','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('777','69','4','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('778','69','5','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('779','69','6','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('780','69','1','5','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('781','69','2','5','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('782','69','3','5','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('783','69','4','5','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('784','69','5','5','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('785','69','6','5','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('786','70','1','1','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('787','70','2','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('788','70','3','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('789','70','4','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('790','70','5','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('791','70','6','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('792','70','1','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('793','70','2','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('794','70','3','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('795','70','4','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('796','70','5','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('797','70','6','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('798','70','1','5','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('799','70','2','5','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('800','70','3','5','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('801','70','4','5','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('802','70','5','5','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('803','70','6','5','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('804','71','1','1','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('805','71','2','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('806','71','3','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('807','71','4','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('808','71','5','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('809','71','6','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('810','71','1','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('811','71','2','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('812','71','3','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('813','71','4','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('814','71','5','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('815','71','6','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('816','71','1','5','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('817','71','2','5','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('818','71','3','5','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('819','71','4','5','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('820','71','5','5','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('821','71','6','5','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('822','72','1','1','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('823','72','2','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('824','72','3','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('825','72','4','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('826','72','5','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('827','72','6','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('828','72','1','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('829','72','2','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('830','72','3','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('831','72','4','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('832','72','5','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('833','72','6','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('834','72','1','5','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('835','72','2','5','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('836','72','3','5','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('837','72','4','5','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('838','72','5','5','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('839','72','6','5','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('840','73','1','1','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('841','73','2','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('842','73','3','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('843','73','4','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('844','73','5','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('845','73','6','1','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('846','73','1','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('847','73','2','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('848','73','3','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('849','73','4','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('850','73','5','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('851','73','6','2','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('852','73','1','5','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('853','73','2','5','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('854','73','3','5','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('855','73','4','5','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('856','73','5','5','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('857','73','6','5','N');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('858','74','1','1','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('859','74','1','2','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('860','74','1','3','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('861','74','1','4','Y');

INSERT IGNORE INTO tbtypepersonpermission VALUES ('862','74','1','5','N');



##
## TABELA: tbtypestreet
##
DROP TABLE IF EXISTS tbtypestreet ; 

CREATE TABLE IF NOT EXISTS `tbtypestreet` (
  `idtypestreet` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  `abbr` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`idtypestreet`)
) ENGINE=InnoDB AUTO_INCREMENT=134 DEFAULT CHARSET=latin1 ; 

# Dados para a tabela: tbtypestreet

INSERT IGNORE INTO tbtypestreet VALUES ('1','Choose','AAA');

INSERT IGNORE INTO tbtypestreet VALUES ('2','Avenue','AVE');

INSERT IGNORE INTO tbtypestreet VALUES ('3','Place','PL');

INSERT IGNORE INTO tbtypestreet VALUES ('4','Street','ST');

INSERT IGNORE INTO tbtypestreet VALUES ('5','Alameda','AL');

INSERT IGNORE INTO tbtypestreet VALUES ('6','Acesso','AC');

INSERT IGNORE INTO tbtypestreet VALUES ('7','Adro','AD');

INSERT IGNORE INTO tbtypestreet VALUES ('8','Aeroporto','ERA');

INSERT IGNORE INTO tbtypestreet VALUES ('9','Alameda','AL');

INSERT IGNORE INTO tbtypestreet VALUES ('10','Alto','AT');

INSERT IGNORE INTO tbtypestreet VALUES ('11','?rea','A');

INSERT IGNORE INTO tbtypestreet VALUES ('12','?rea Especial','AE');

INSERT IGNORE INTO tbtypestreet VALUES ('13','Art?ria','ART');

INSERT IGNORE INTO tbtypestreet VALUES ('14','Atalho','ATL');

INSERT IGNORE INTO tbtypestreet VALUES ('15','Avenida','AV');

INSERT IGNORE INTO tbtypestreet VALUES ('16','Avenida Contorno','AV-CONT');

INSERT IGNORE INTO tbtypestreet VALUES ('17','Baixa','BX');

INSERT IGNORE INTO tbtypestreet VALUES ('18','Bal?o','BLO');

INSERT IGNORE INTO tbtypestreet VALUES ('19','Balne?rio','BAL');

INSERT IGNORE INTO tbtypestreet VALUES ('20','Beco','BC');

INSERT IGNORE INTO tbtypestreet VALUES ('21','Belvedere','BELV');

INSERT IGNORE INTO tbtypestreet VALUES ('22','Bloco','BL');

INSERT IGNORE INTO tbtypestreet VALUES ('23','Bosque','BSQ');

INSERT IGNORE INTO tbtypestreet VALUES ('24','Boulevard','BVD');

INSERT IGNORE INTO tbtypestreet VALUES ('25','Buraco','BCO');

INSERT IGNORE INTO tbtypestreet VALUES ('26','Cais','C');

INSERT IGNORE INTO tbtypestreet VALUES ('27','Cal?ada','CALC');

INSERT IGNORE INTO tbtypestreet VALUES ('28','Caminho','CAM');

INSERT IGNORE INTO tbtypestreet VALUES ('29','Campo','CPO');

INSERT IGNORE INTO tbtypestreet VALUES ('30','Canal','CAN');

INSERT IGNORE INTO tbtypestreet VALUES ('31','Ch?cara','CH');

INSERT IGNORE INTO tbtypestreet VALUES ('32','Chapad?o','CHAP');

INSERT IGNORE INTO tbtypestreet VALUES ('33','Circular','CIRC');

INSERT IGNORE INTO tbtypestreet VALUES ('34','Col?nia','COL');

INSERT IGNORE INTO tbtypestreet VALUES ('35','Complexo Vi?rio','CMP-VR');

INSERT IGNORE INTO tbtypestreet VALUES ('36','Condom?nio','COND');

INSERT IGNORE INTO tbtypestreet VALUES ('37','Conjunto','CJ');

INSERT IGNORE INTO tbtypestreet VALUES ('38','Corredor','COR');

INSERT IGNORE INTO tbtypestreet VALUES ('39','C?rrego','CRG');

INSERT IGNORE INTO tbtypestreet VALUES ('40','Descida','DSC');

INSERT IGNORE INTO tbtypestreet VALUES ('41','Desvio','DSV');

INSERT IGNORE INTO tbtypestreet VALUES ('42','Distrito','DT');

INSERT IGNORE INTO tbtypestreet VALUES ('43','Elevada','EVD');

INSERT IGNORE INTO tbtypestreet VALUES ('44','Entrada Particular','ENT-PART');

INSERT IGNORE INTO tbtypestreet VALUES ('45','Entre Quadra','EQ');

INSERT IGNORE INTO tbtypestreet VALUES ('46','Escada','ESC');

INSERT IGNORE INTO tbtypestreet VALUES ('47','Esplanada','ESP');

INSERT IGNORE INTO tbtypestreet VALUES ('48','Esta??o','ETC');

INSERT IGNORE INTO tbtypestreet VALUES ('49','Estacionamento','ESTC');

INSERT IGNORE INTO tbtypestreet VALUES ('50','Est?dio','ETD');

INSERT IGNORE INTO tbtypestreet VALUES ('51','Est?ncia','ETN');

INSERT IGNORE INTO tbtypestreet VALUES ('52','Estrada','EST');

INSERT IGNORE INTO tbtypestreet VALUES ('53','Estrada Municipal','EST-MUN');

INSERT IGNORE INTO tbtypestreet VALUES ('54','Favela','FAV');

INSERT IGNORE INTO tbtypestreet VALUES ('55','Fazenda','FAZ');

INSERT IGNORE INTO tbtypestreet VALUES ('56','Feira','FRA');

INSERT IGNORE INTO tbtypestreet VALUES ('57','Ferrovia','FER');

INSERT IGNORE INTO tbtypestreet VALUES ('58','Fonte','FNT');

INSERT IGNORE INTO tbtypestreet VALUES ('59','Forte','FTE');

INSERT IGNORE INTO tbtypestreet VALUES ('60','Galeria','GAL');

INSERT IGNORE INTO tbtypestreet VALUES ('61','Granja','GJA');

INSERT IGNORE INTO tbtypestreet VALUES ('62','Habitacional','HAB');

INSERT IGNORE INTO tbtypestreet VALUES ('63','Ilha','IA');

INSERT IGNORE INTO tbtypestreet VALUES ('64','Jardim','JD');

INSERT IGNORE INTO tbtypestreet VALUES ('65','Jardinete','JDE');

INSERT IGNORE INTO tbtypestreet VALUES ('66','Ladeira','LD');

INSERT IGNORE INTO tbtypestreet VALUES ('67','Lago','LG');

INSERT IGNORE INTO tbtypestreet VALUES ('68','Lagoa','LGA');

INSERT IGNORE INTO tbtypestreet VALUES ('69','Largo','LRG');

INSERT IGNORE INTO tbtypestreet VALUES ('70','Loteamento','LOT');

INSERT IGNORE INTO tbtypestreet VALUES ('71','Marina','MNA');

INSERT IGNORE INTO tbtypestreet VALUES ('72','M?dulo','MOD');

INSERT IGNORE INTO tbtypestreet VALUES ('73','Monte','TEM');

INSERT IGNORE INTO tbtypestreet VALUES ('74','Morro','MRO');

INSERT IGNORE INTO tbtypestreet VALUES ('75','N?cleo','NUC');

INSERT IGNORE INTO tbtypestreet VALUES ('76','Parada','PDA');

INSERT IGNORE INTO tbtypestreet VALUES ('77','Paradouro','PDO');

INSERT IGNORE INTO tbtypestreet VALUES ('78','Paralela','PAR');

INSERT IGNORE INTO tbtypestreet VALUES ('79','Parque','PRQ');

INSERT IGNORE INTO tbtypestreet VALUES ('80','Passagem','PSG');

INSERT IGNORE INTO tbtypestreet VALUES ('81','Passagem Subterr?nea','PSC-SUB');

INSERT IGNORE INTO tbtypestreet VALUES ('82','Passarela','PSA');

INSERT IGNORE INTO tbtypestreet VALUES ('83','Passeio','PAS');

INSERT IGNORE INTO tbtypestreet VALUES ('84','P?tio','PAT');

INSERT IGNORE INTO tbtypestreet VALUES ('85','Ponta','PNT');

INSERT IGNORE INTO tbtypestreet VALUES ('86','Ponte','PTE');

INSERT IGNORE INTO tbtypestreet VALUES ('87','Porto','PTO');

INSERT IGNORE INTO tbtypestreet VALUES ('88','Pra?a','PC');

INSERT IGNORE INTO tbtypestreet VALUES ('89','Pra?a de Esportes','PC-ESP');

INSERT IGNORE INTO tbtypestreet VALUES ('90','Praia','PR');

INSERT IGNORE INTO tbtypestreet VALUES ('91','Prolongamento','PRL');

INSERT IGNORE INTO tbtypestreet VALUES ('92','Quadra','Q');

INSERT IGNORE INTO tbtypestreet VALUES ('93','Quinta','QTA');

INSERT IGNORE INTO tbtypestreet VALUES ('94','Ane Quintas','QTASRodo');

INSERT IGNORE INTO tbtypestreet VALUES ('95','Ramal','RAM');

INSERT IGNORE INTO tbtypestreet VALUES ('96','Rampa','RMP');

INSERT IGNORE INTO tbtypestreet VALUES ('97','Recanto','REC');

INSERT IGNORE INTO tbtypestreet VALUES ('98','Residencial','RES');

INSERT IGNORE INTO tbtypestreet VALUES ('99','Reta','RET');

INSERT IGNORE INTO tbtypestreet VALUES ('100','Retiro','RER');

INSERT IGNORE INTO tbtypestreet VALUES ('101','Retorno','RTN');

INSERT IGNORE INTO tbtypestreet VALUES ('102','Rodo Anel','ROD-AN');

INSERT IGNORE INTO tbtypestreet VALUES ('103','Rodovia','ROD');

INSERT IGNORE INTO tbtypestreet VALUES ('104','Rotat?ria','RTT');

INSERT IGNORE INTO tbtypestreet VALUES ('105','R?tula','ROT');

INSERT IGNORE INTO tbtypestreet VALUES ('106','Rua','R');

INSERT IGNORE INTO tbtypestreet VALUES ('107','Rua de Liga??o','R-LIG');

INSERT IGNORE INTO tbtypestreet VALUES ('108','Rua de Pedestre','R-PED');

INSERT IGNORE INTO tbtypestreet VALUES ('109','Servid?o','SRV');

INSERT IGNORE INTO tbtypestreet VALUES ('110','Setor','ST');

INSERT IGNORE INTO tbtypestreet VALUES ('111','S?tio','SIT');

INSERT IGNORE INTO tbtypestreet VALUES ('112','Subida','SUB');

INSERT IGNORE INTO tbtypestreet VALUES ('113','Terminal','TER');

INSERT IGNORE INTO tbtypestreet VALUES ('114','Travessa','TV');

INSERT IGNORE INTO tbtypestreet VALUES ('115','Travessa Particular','TV-PART');

INSERT IGNORE INTO tbtypestreet VALUES ('116','Trecho','TRC');

INSERT IGNORE INTO tbtypestreet VALUES ('117','Trevo','TRV');

INSERT IGNORE INTO tbtypestreet VALUES ('118','Trincheira','TCH');

INSERT IGNORE INTO tbtypestreet VALUES ('119','T?nel','TUN');

INSERT IGNORE INTO tbtypestreet VALUES ('120','Unidade','UNID');

INSERT IGNORE INTO tbtypestreet VALUES ('121','Vala','VAL');

INSERT IGNORE INTO tbtypestreet VALUES ('122','Vale','VLE');

INSERT IGNORE INTO tbtypestreet VALUES ('123','Variante','VRTE');

INSERT IGNORE INTO tbtypestreet VALUES ('124','Vereda','VER');

INSERT IGNORE INTO tbtypestreet VALUES ('125','Via','V');

INSERT IGNORE INTO tbtypestreet VALUES ('126','Via de Acesso','V-AC');

INSERT IGNORE INTO tbtypestreet VALUES ('127','Via de Pedestre','V-PED');

INSERT IGNORE INTO tbtypestreet VALUES ('128','Via Elevado','V-EVD');

INSERT IGNORE INTO tbtypestreet VALUES ('129','Via Expressa','V-EXP');

INSERT IGNORE INTO tbtypestreet VALUES ('130','Viaduto','VD');

INSERT IGNORE INTO tbtypestreet VALUES ('131','Viela','VLA');

INSERT IGNORE INTO tbtypestreet VALUES ('132','Vila','VL');

INSERT IGNORE INTO tbtypestreet VALUES ('133','Zigue-Zague','ZIG-ZAG');



##
## TABELA: temp_1
##
DROP TABLE IF EXISTS temp_1 ; 

CREATE TABLE IF NOT EXISTS `temp_1` (
  `idtemp1` int(4) NOT NULL AUTO_INCREMENT,
  `iddepartment` int(4) NOT NULL,
  PRIMARY KEY (`idtemp1`),
  KEY `FK_temp_1` (`iddepartment`),
  CONSTRAINT `FK_temp_1` FOREIGN KEY (`iddepartment`) REFERENCES `hdk_tbdepartment` (`iddepartment`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC ; 

# Dados para a tabela: temp_1



##
## TABELA: tmp_blobs
##
DROP TABLE IF EXISTS tmp_blobs ; 

CREATE TABLE IF NOT EXISTS `tmp_blobs` (
  `idblobs` int(11) NOT NULL AUTO_INCREMENT,
  `tabela` varchar(80) DEFAULT NULL,
  `campo` varchar(80) DEFAULT NULL,
  PRIMARY KEY (`idblobs`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1 ; 

# Dados para a tabela: tmp_blobs

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



SET FOREIGN_KEY_CHECKS = 1; 
