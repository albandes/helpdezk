DROP TABLE IF EXISTS bbd_tbmessage;

CREATE TABLE `bbd_tbmessage` (
  `idmessage` int(11) NOT NULL AUTO_INCREMENT,
  `idtopic` int(11) NOT NULL,
  `idperson` int(11) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `dtcreate` datetime DEFAULT NULL,
  `dtstart` datetime DEFAULT NULL,
  `dtend` datetime DEFAULT NULL,
  `sendemail` varchar(1) NOT NULL,
  `showin` varchar(1) NOT NULL,
  `emailsent` int(1) DEFAULT 0,
  PRIMARY KEY (`idmessage`),
  KEY `fk_bbd_tbmessage_bbd_topic1` (`idtopic`),
  KEY `fk_bbd_tbmessage_tbperson1` (`idperson`),
  CONSTRAINT `fk_bbd_tbmessage_bbd_topic1` FOREIGN KEY (`idtopic`) REFERENCES `bbd_topic` (`idtopic`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_bbd_tbmessage_tbperson1` FOREIGN KEY (`idperson`) REFERENCES `tbperson` (`idperson`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

INSERT INTO bbd_tbmessage VALUES("1","1","1","Email sending issue .","The email service (SMTP) has problems when sending emails. But receiving emails (POP / IMAP) is working normally.Our infrastructure team is working to resolve the issue as quickly as possible.","2016-02-25 19:48:56","2019-12-26 19:48:00","0000-00-00 00:00:00","N","3","0");



DROP TABLE IF EXISTS bbd_tbread;

CREATE TABLE `bbd_tbread` (
  `idread` int(11) NOT NULL AUTO_INCREMENT,
  `dtread` datetime NOT NULL,
  `idperson` int(11) NOT NULL,
  `idmessage` int(11) NOT NULL,
  PRIMARY KEY (`idread`),
  KEY `fk_bbd_tbread_tbperson1` (`idperson`),
  CONSTRAINT `fk_bbd_tbread_tbperson1` FOREIGN KEY (`idperson`) REFERENCES `tbperson` (`idperson`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;




DROP TABLE IF EXISTS bbd_topic;

CREATE TABLE `bbd_topic` (
  `idtopic` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `default_display` varchar(10) DEFAULT NULL,
  `fl_emailsent` varchar(1) DEFAULT 'N',
  PRIMARY KEY (`idtopic`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

INSERT INTO bbd_topic VALUES("1","Servers","","N");



DROP TABLE IF EXISTS bbd_topiccustomer;

CREATE TABLE `bbd_topiccustomer` (
  `idtopiccustomer` int(11) NOT NULL,
  `idperson` int(11) NOT NULL,
  `idtopic` int(11) NOT NULL,
  PRIMARY KEY (`idtopiccustomer`),
  KEY `fk_bbd_topiccustomer_tbperson1` (`idperson`),
  KEY `fk_bbd_topiccustomer_bbd_topic1` (`idtopic`),
  CONSTRAINT `fk_bbd_topiccustomer_bbd_topic1` FOREIGN KEY (`idtopic`) REFERENCES `bbd_topic` (`idtopic`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_bbd_topiccustomer_tbperson1` FOREIGN KEY (`idperson`) REFERENCES `tbperson` (`idperson`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;




DROP TABLE IF EXISTS bbd_topic_company;

CREATE TABLE `bbd_topic_company` (
  `idtopiccompany` int(11) NOT NULL AUTO_INCREMENT,
  `idtopic` int(11) NOT NULL,
  `idcompany` int(11) NOT NULL,
  PRIMARY KEY (`idtopiccompany`),
  KEY `idtopic` (`idtopic`),
  KEY `idcompany` (`idcompany`),
  CONSTRAINT `bbd_topic_company_ibfk_1` FOREIGN KEY (`idtopic`) REFERENCES `bbd_topic` (`idtopic`),
  CONSTRAINT `bbd_topic_company_ibfk_2` FOREIGN KEY (`idcompany`) REFERENCES `tbperson` (`idperson`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;




DROP TABLE IF EXISTS bbd_topic_group;

CREATE TABLE `bbd_topic_group` (
  `idtopicgroup` int(11) NOT NULL AUTO_INCREMENT,
  `idtopic` int(11) NOT NULL,
  `idgroup` int(11) NOT NULL,
  PRIMARY KEY (`idtopicgroup`),
  KEY `idtopic` (`idtopic`),
  KEY `idgroup` (`idgroup`),
  CONSTRAINT `bbd_topic_group_ibfk_1` FOREIGN KEY (`idtopic`) REFERENCES `bbd_topic` (`idtopic`),
  CONSTRAINT `bbd_topic_group_ibfk_2` FOREIGN KEY (`idgroup`) REFERENCES `hdk_tbgroup` (`idgroup`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;




DROP TABLE IF EXISTS dsh_tbcategory;

CREATE TABLE `dsh_tbcategory` (
  `idcategory` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT NULL,
  `index` int(11) DEFAULT NULL,
  `status` char(1) DEFAULT 'A',
  PRIMARY KEY (`idcategory`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

INSERT INTO dsh_tbcategory VALUES("1","Helpdezk","0","A");



DROP TABLE IF EXISTS dsh_tbcategory_has_widget;

CREATE TABLE `dsh_tbcategory_has_widget` (
  `idcategory` int(11) DEFAULT NULL,
  `idwidget` int(11) DEFAULT NULL,
  KEY `FK_dsh_tbcategory_has_widget` (`idwidget`),
  KEY `FK1_dsh_tbcategory_has_widget` (`idcategory`),
  CONSTRAINT `FK1_dsh_tbcategory_has_widget` FOREIGN KEY (`idcategory`) REFERENCES `dsh_tbcategory` (`idcategory`) ON DELETE CASCADE,
  CONSTRAINT `FK_dsh_tbcategory_has_widget` FOREIGN KEY (`idwidget`) REFERENCES `dsh_tbwidget` (`idwidget`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO dsh_tbcategory_has_widget VALUES("1","1");
INSERT INTO dsh_tbcategory_has_widget VALUES("1","3");
INSERT INTO dsh_tbcategory_has_widget VALUES("1","3");



DROP TABLE IF EXISTS dsh_tbwidget;

CREATE TABLE `dsh_tbwidget` (
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

INSERT INTO dsh_tbwidget VALUES("1","Hard Users","","","","","2013","6","","","","Most active users","Rogerio Albandes","hdk_harduser","hard user.jpg","0","A");
INSERT INTO dsh_tbwidget VALUES("2","Requests in Stock","","","","","1800000","12","","","","Requests  in stock , in the last 30 days","Rogerio Albandes","hdk_requestassets","Estoque de Solicitacoes.jpg","0","A");
INSERT INTO dsh_tbwidget VALUES("3","Service Level Agreement","","","","","","","","","","Service Level Agreement","Rogerio Albandes","hdk_sla","sla.jpg","0","A");



DROP TABLE IF EXISTS dsh_tbwidgetusuario;

CREATE TABLE `dsh_tbwidgetusuario` (
  `idwidgetusuario` int(11) NOT NULL AUTO_INCREMENT,
  `idusuario` int(10) unsigned NOT NULL,
  `widgets` blob DEFAULT NULL,
  PRIMARY KEY (`idwidgetusuario`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;




DROP TABLE IF EXISTS hdk_base_attachment;

CREATE TABLE `hdk_base_attachment` (
  `idattachment` int(11) NOT NULL AUTO_INCREMENT,
  `filename` varchar(200) NOT NULL,
  `idbase` int(11) NOT NULL,
  `real_filename` varchar(200) NOT NULL,
  PRIMARY KEY (`idattachment`),
  KEY `fk_idbase_tbperson` (`idbase`),
  CONSTRAINT `fk_idbase_tbperson` FOREIGN KEY (`idbase`) REFERENCES `hdk_base_knowledge` (`idbase`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;




DROP TABLE IF EXISTS hdk_base_category;

CREATE TABLE `hdk_base_category` (
  `idcategory` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `idcategory_reference` int(11) DEFAULT NULL,
  PRIMARY KEY (`idcategory`)
) ENGINE=InnoDB AUTO_INCREMENT=113 DEFAULT CHARSET=latin1;

INSERT INTO hdk_base_category VALUES("1","Definir Categoria","0");
INSERT INTO hdk_base_category VALUES("17","ccc","0");
INSERT INTO hdk_base_category VALUES("94","Siare","0");
INSERT INTO hdk_base_category VALUES("95","CRM - Syonet","0");
INSERT INTO hdk_base_category VALUES("96","WEON","0");
INSERT INTO hdk_base_category VALUES("97","Hardware","0");
INSERT INTO hdk_base_category VALUES("98","Windows","111");
INSERT INTO hdk_base_category VALUES("99","QlikView","0");
INSERT INTO hdk_base_category VALUES("100","Windows XP","98");
INSERT INTO hdk_base_category VALUES("101","Windows Vista","98");
INSERT INTO hdk_base_category VALUES("102","Windows 7","98");
INSERT INTO hdk_base_category VALUES("103","AntiVirus","0");
INSERT INTO hdk_base_category VALUES("106","SENHAS","0");
INSERT INTO hdk_base_category VALUES("107","Internet","0");
INSERT INTO hdk_base_category VALUES("110","PROGRAMAS","0");
INSERT INTO hdk_base_category VALUES("111","&acirc;&euro;&oelig;Esta opera&Atilde;&sect;&Atilde;&pound;o foi cancelada devido &Atilde;&nbsp;s re","98");
INSERT INTO hdk_base_category VALUES("112","Senha Desp Adriano","106");



DROP TABLE IF EXISTS hdk_base_knowledge;

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
  `faq` int(1) DEFAULT 0,
  PRIMARY KEY (`idbase`),
  KEY `fk_idcategory_hdk_base_category` (`idcategory`),
  KEY `fk_idperson_edit_tbperson` (`idperson_edit`),
  KEY `fk_idperson_tbperson` (`idperson`),
  KEY `idx_1` (`idbase`,`idcategory`,`idperson`,`idperson_edit`,`name`),
  CONSTRAINT `fk_idcategory_hdk_base_category` FOREIGN KEY (`idcategory`) REFERENCES `hdk_base_category` (`idcategory`) ON DELETE CASCADE,
  CONSTRAINT `fk_idperson_edit_tbperson` FOREIGN KEY (`idperson_edit`) REFERENCES `tbperson` (`idperson`),
  CONSTRAINT `fk_idperson_tbperson` FOREIGN KEY (`idperson`) REFERENCES `tbperson` (`idperson`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;




DROP TABLE IF EXISTS hdk_tbaddinfo;

CREATE TABLE `hdk_tbaddinfo` (
  `idaddinfo` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  PRIMARY KEY (`idaddinfo`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

INSERT INTO hdk_tbaddinfo VALUES("1","Erro no Sistema");
INSERT INTO hdk_tbaddinfo VALUES("2","Erro do Usu&Atilde;&iexcl;rio");



DROP TABLE IF EXISTS hdk_tbapproval_rule;

CREATE TABLE `hdk_tbapproval_rule` (
  `idapproval` int(10) NOT NULL AUTO_INCREMENT,
  `iditem` int(3) DEFAULT NULL,
  `idservice` int(3) DEFAULT NULL,
  `idperson` int(10) DEFAULT NULL,
  `order` int(10) DEFAULT 1,
  `fl_recalculate` int(1) DEFAULT 0,
  PRIMARY KEY (`idapproval`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;




DROP TABLE IF EXISTS hdk_tbattendance_way;

CREATE TABLE `hdk_tbattendance_way` (
  `idattendanceway` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `way` varchar(100) NOT NULL,
  PRIMARY KEY (`idattendanceway`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

INSERT INTO hdk_tbattendance_way VALUES("1","Internal");
INSERT INTO hdk_tbattendance_way VALUES("2","External");
INSERT INTO hdk_tbattendance_way VALUES("3","Remote Controll");



DROP TABLE IF EXISTS hdk_tbconfig;

CREATE TABLE `hdk_tbconfig` (
  `idconfig` int(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) DEFAULT NULL,
  `description` blob DEFAULT NULL,
  `idconfigcategory` int(10) unsigned DEFAULT NULL,
  `session_name` varchar(50) DEFAULT NULL,
  `field_type` varchar(200) DEFAULT NULL,
  `status` char(1) DEFAULT 'A',
  `smarty` varchar(120) NOT NULL,
  `value` varchar(200) DEFAULT NULL,
  `allowremove` enum('Y','N') NOT NULL DEFAULT 'N',
  PRIMARY KEY (`idconfig`),
  KEY `FK_hdk_tbconfig` (`idconfigcategory`),
  CONSTRAINT `FK_hdk_tbconfig` FOREIGN KEY (`idconfigcategory`) REFERENCES `hdk_tbconfig_category` (`idconfigcategory`)
) ENGINE=InnoDB AUTO_INCREMENT=109 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

INSERT INTO hdk_tbconfig VALUES("1","USUARIO: Notificar usu&aacuterio quando uma solicita&ccedil&atildeo for assumida.","","3","NEW_ASSUMED_MAIL","checkbox","A","Email_request_assumed","1","N");
INSERT INTO hdk_tbconfig VALUES("2","USUARIO: Notificar usu&aacuterio ao encerrar a solicita&ccedil&atildeo","","3","FINISH_MAIL","checkbox","A","Email_request_finished","1","N");
INSERT INTO hdk_tbconfig VALUES("3","USUARIO: Notificar usu&aacuterio ao rejeitar a solicita&ccedil&atildeo","","3","REJECTED_MAIL","checkbox","A","Email_request_rejected","1","N");
INSERT INTO hdk_tbconfig VALUES("4","Enviar email ao avaliar","","3","EM_EVALUATED","checkbox","A","Email_request_evaluated","1","N");
INSERT INTO hdk_tbconfig VALUES("13","USUARIO: Enviar notifica&ccedil&atildeo ao incluir novo apontamento","","3","USER_NEW_NOTE_MAIL","checkbox","A","Email_request_apont_user","1","N");
INSERT INTO hdk_tbconfig VALUES("16","ATENDENTE: Notificar atendentes respons&aacuteveis ao cadastrar uma nova solicita&ccedil&atildeo","","3","NEW_REQUEST_OPERATOR_MAIL","checkbox","A","Email_request_record","1","N");
INSERT INTO hdk_tbconfig VALUES("18","Permitir que os atendentes excluam apontamentos","","1","SES_IND_DELETE_NOTE","checkbox","A","sys_allow_delete_note","1","N");
INSERT INTO hdk_tbconfig VALUES("20","Qtde de prorroga&ccedil&otildees na data de vencimento (0 = Nunca, [Em branco] = Sem limites, [Acima de 0] = Qtd. de prorroga&ccedil&otildees)","","1","SES_QT_PRORROGATION","input","A","sys_prorogation_qt","2","N");
INSERT INTO hdk_tbconfig VALUES("23","Permitir reabertura de solicita&ccedil&otildees","","1","SES_IND_REOPEN","checkbox","A","sys_allow_reopen","1","N");
INSERT INTO hdk_tbconfig VALUES("33","Obrigar o atendente a informar o tempo gasto na tarefas.","","1","SES_IND_ENTER_TIME","checkbox","A","sys_enter_time","0","N");
INSERT INTO hdk_tbconfig VALUES("34","Permitir que os atendentes assumam solicita&ccedil&otildees repassadas ou assumidas por outro atendente.","","1","SES_IND_ASSUME_OTHER","checkbox","A","sys_allow_assume_others","1","N");
INSERT INTO hdk_tbconfig VALUES("43","ATENDENTE: Enviar notifica&ccedil&atildeo ao incluir novo apontamento","","3","OPERATOR_NEW_NOTE","checkbox","A","Email_request_apont_operator","1","N");
INSERT INTO hdk_tbconfig VALUES("44","Ao abrir uma nova solicitaï¿½ï¿½o iniciar o timer","","1","SES_IND_TIMER_OPENING","checkbox","A","sys_start_timer","1","N");
INSERT INTO hdk_tbconfig VALUES("49","NÃ£o mostrar prazo de entrega no grid do solicitante atÃ© que um atendente tenha assumido a solicitaÃ§Ã£o","","10","SES_HIDE_GRID_PERIOD","checkbox","A","sys_expire_date_user_grid","1","N");
INSERT INTO hdk_tbconfig VALUES("50","Ao assumir ou repassar solicita&ccedil&otildees, trazer marcada a op&ccedil&atildeo: \"Desejo que meu grupo continue visualizando a solicita&ccedil&atildeo\"","","10","SES_SHARE_VIEW","checkbox","A","sys_show_group_view_checkbox","1","N");
INSERT INTO hdk_tbconfig VALUES("62","ATENDENTE: Enviar notifica&ccedil&atildeo quando uma solita&ccedil&atildeo for reaberta","","3","REQUEST_REOPENED","checkbox","A","Email_request_reopened","1","N");
INSERT INTO hdk_tbconfig VALUES("63","Send email notification for the requests status","","1","SEND_EMAILS","checkbox","A","sys_email_notification","1","N");
INSERT INTO hdk_tbconfig VALUES("64","Order requests ASC","","10","SES_ORDER_ASC","checkbox","A","sys_sort_asc","1","N");
INSERT INTO hdk_tbconfig VALUES("65","Email Host","","5","EM_HOSTNAME","","A","em_hostname","mail.helpdezk.org","N");
INSERT INTO hdk_tbconfig VALUES("66","Domain","","5","EM_DOMAIN","","A","em_domain","helpdezk.org","N");
INSERT INTO hdk_tbconfig VALUES("67","Email user","","5","EM_USER","","A","em_user","no-reply@helpdezk.org","N");
INSERT INTO hdk_tbconfig VALUES("68","User password","","5","EM_PASSWORD","","A","em_password","LambruscO","N");
INSERT INTO hdk_tbconfig VALUES("69","Sender email","","5","EM_SENDER","","A","em_sender","no-reply@helpdezk.org","N");
INSERT INTO hdk_tbconfig VALUES("70","Requires authentication","","5","EM_AUTH","","A","em_auth","1","N");
INSERT INTO hdk_tbconfig VALUES("71","Email Header","","11","EM_HEADER","","A","em_header","","N");
INSERT INTO hdk_tbconfig VALUES("72","Email Footer","<p>
	&nbsp;</p>
<div>
	___________________________________________</div>
<div>
	Helpdezk Demo Version - Pipegrep Software&nbsp;</div>
<p>
	&nbsp;</p>
","11","EM_FOOTER","","A","em_footer","","N");
INSERT INTO hdk_tbconfig VALUES("73","POP Host","","12","POP_HOST","","A","pop_host","pop.gmail.com","N");
INSERT INTO hdk_tbconfig VALUES("74","POP Port","","12","POP_PORT","","A","pop_port","993","N");
INSERT INTO hdk_tbconfig VALUES("75","POP Type","","12","POP_TYPE","","A","pop_type","GMAIL","N");
INSERT INTO hdk_tbconfig VALUES("76","Success Log","","5","EM_SUCCESS_LOG","","A","em_success_log","1","N");
INSERT INTO hdk_tbconfig VALUES("77","Failure Log","","5","EM_FAILURE_LOG","","A","em_failure_log","1","N");
INSERT INTO hdk_tbconfig VALUES("78","Solicitar a aprovação do usuário após o encerramento da solicitação","","10","SES_APROVE","checkbox","A","ses_aprove","0","N");
INSERT INTO hdk_tbconfig VALUES("79","Ao Aprovar, solicitar que o usuÃ¡rio responda o questionário de avaliação do atendimento","","10","SES_EVALUATE","checkbox","A","ses_evaluate","0","N");
INSERT INTO hdk_tbconfig VALUES("80","Enable maintenance mode","","5","SES_MAINTENANCE","checkbox","A","ses_maintenance","0","N");
INSERT INTO hdk_tbconfig VALUES("81","Maintenance message","","5","SES_MAINTENANCE_MSG","","A","ses_maintenance_msg","
	We&#39;re doing maintenance, be back soon !!!!
","N");
INSERT INTO hdk_tbconfig VALUES("82","ATENDENTE: Notificar atendentes respons&aacuteveis ao repassar uma solicita&ccedil&atildeo","","3","REPASS_REQUEST_OPERATOR_MAIL","checkbox","A","Email_request_repass","1","N");
INSERT INTO hdk_tbconfig VALUES("83","Usuário administrador visualizar todas solicitações","","10","SES_ADM_VIEW_REQUEST","checkbox","A","sys_adm_view_request","1","N");
INSERT INTO hdk_tbconfig VALUES("84","Para abrir nova solicitação o usuário não poderá ter solicitações para aprovação","","1","SES_OPEN_NEW_REQUEST","checkbox","A","sys_open_new_request","1","N");
INSERT INTO hdk_tbconfig VALUES("85","Permitir atendente inserir apontamento sem descrição","","1","SES_EMPTY_NOTE","checkbox","A","sys_empty_note","1","N");
INSERT INTO hdk_tbconfig VALUES("86","ATENDENTE: Notificar atendente de solita&ccedil&atildeo para aprova&ccedil&atildeo.","","3","SES_REQUEST_APPROVE","checkbox","A","Email_request_approve","1","N");
INSERT INTO hdk_tbconfig VALUES("87","Email Title","","5","EM_TITLE","","A","em_title","[HELPDEZK - Demo Version]","N");
INSERT INTO hdk_tbconfig VALUES("88","Permitir reabertura de solicita&ccedil&otildees por usu&aacuterio","","1","SES_IND_REOPEN_USER","checkbox","A","sys_allow_reopen_user","1","N");
INSERT INTO hdk_tbconfig VALUES("90","Utilizar controle de equipamento (os, etiqueta e serial)","","1","SES_IND_EQUIPMENT","checkbox","A","sys_use_equipment","0","N");
INSERT INTO hdk_tbconfig VALUES("91","Define session time","","1","SES_TIME_SESSION","input","A","sys_time_session","7200","N");
INSERT INTO hdk_tbconfig VALUES("92","LDAP/AD Server","","13","SES_LDAP_SERVER","input","A","ldap_server"," ","N");
INSERT INTO hdk_tbconfig VALUES("93","LDAP Distinguished Names","","13","SES_LDAP_DN","input","A","ldap_dn"," ","N");
INSERT INTO hdk_tbconfig VALUES("94","LDAP/AD Server","","13","SES_LDAP_SERVER","input","A","ldap_server"," ","N");
INSERT INTO hdk_tbconfig VALUES("95","LDAP Distinguished Names","","13","SES_LDAP_DN","input","A","ldap_dn"," ","N");
INSERT INTO hdk_tbconfig VALUES("96","POP Domain","","12","POP_DOMAIN","","A","pop_domain","demo.com.br","N");
INSERT INTO hdk_tbconfig VALUES("97","Mostrar número de telefone, ramal e celular na solicitação","","10","SES_REQUEST_SHOW_PHONE","checkbox","A","ses_request_show_phone","1","N");
INSERT INTO hdk_tbconfig VALUES("98","Definir tempo de auto atualizar grid dos atendents [0 = n?o atualizar]","","10","SES_REFRESH_OPERATOR_GRID","input","A","ses_refresh_opertor_grid","0","N");
INSERT INTO hdk_tbconfig VALUES("99","LDAP Domain","","13","SES_LDAP_DOMAIN","","A","ldap_domain","","N");
INSERT INTO hdk_tbconfig VALUES("100","LDAP Field ID","","13","SES_LDAP_FIELD","","A","ldap_field","uid","N");
INSERT INTO hdk_tbconfig VALUES("101","LDAP or AD","","13","SES_LDAP_AD","","A","Type","1","N");
INSERT INTO hdk_tbconfig VALUES("104","Email Port","","5","EM_PORT","","A","em_port","","N");
INSERT INTO hdk_tbconfig VALUES("105","State default","","5","STATE_DEFAULT","input","A","state_default","1","N");
INSERT INTO hdk_tbconfig VALUES("107","NÃ£o mostrar prazo de entrega e status no dashboard do solicitante atÃ© que um atendente tenha assumido a solicitaÃ§Ã£o","","10","SES_HIDE_DASH_PERIOD","checkbox","A","sys_expire_date_user_dash","1","N");
INSERT INTO hdk_tbconfig VALUES("108","When open request, do not show area, type, item service default. User chooses regardless of default value.","","1","TKT_DONT_SHOW_DEFAULT","checkbox","A","sys_dont_show_default","0","N");



DROP TABLE IF EXISTS hdk_tbconfig_category;

CREATE TABLE `hdk_tbconfig_category` (
  `idconfigcategory` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) DEFAULT NULL,
  `smarty` varchar(250) DEFAULT NULL,
  `flgsetup` enum('Y','N') NOT NULL DEFAULT 'N',
  PRIMARY KEY (`idconfigcategory`),
  KEY `COD_CATEGORIA` (`idconfigcategory`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;

INSERT INTO hdk_tbconfig_category VALUES("1","Funcionalidades do Sistema","System_features","Y");
INSERT INTO hdk_tbconfig_category VALUES("2","Permissï¿½es para Analista","","N");
INSERT INTO hdk_tbconfig_category VALUES("3","Notificaï¿½ï¿½es de E-mail","","N");
INSERT INTO hdk_tbconfig_category VALUES("4","Usuï¿½rios","","N");
INSERT INTO hdk_tbconfig_category VALUES("5","Geral","","N");
INSERT INTO hdk_tbconfig_category VALUES("6","Patrimï¿½nio","","N");
INSERT INTO hdk_tbconfig_category VALUES("7","Integraï¿½ï¿½o com SMTP/LDAP/MS Active Directory","","Y");
INSERT INTO hdk_tbconfig_category VALUES("8","Inventï¿½rio","","N");
INSERT INTO hdk_tbconfig_category VALUES("9","Inventario","","N");
INSERT INTO hdk_tbconfig_category VALUES("10","Outros itens","Other_items","Y");
INSERT INTO hdk_tbconfig_category VALUES("11","Email Templates","","N");
INSERT INTO hdk_tbconfig_category VALUES("12","POP Server","","N");
INSERT INTO hdk_tbconfig_category VALUES("13","Integration with LDAP/MS Active Directory","Integration_ldap","N");
INSERT INTO hdk_tbconfig_category VALUES("14","Integration with LDAP/MS Active Directory","Integration_ldap","N");



DROP TABLE IF EXISTS hdk_tbconfig_has_template;

CREATE TABLE `hdk_tbconfig_has_template` (
  `idconfig` int(4) NOT NULL,
  `idtemplate` int(4) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO hdk_tbconfig_has_template VALUES("1","1");
INSERT INTO hdk_tbconfig_has_template VALUES("2","2");
INSERT INTO hdk_tbconfig_has_template VALUES("3","3");
INSERT INTO hdk_tbconfig_has_template VALUES("4","4");
INSERT INTO hdk_tbconfig_has_template VALUES("13","13");
INSERT INTO hdk_tbconfig_has_template VALUES("16","16");
INSERT INTO hdk_tbconfig_has_template VALUES("43","43");
INSERT INTO hdk_tbconfig_has_template VALUES("86","7");
INSERT INTO hdk_tbconfig_has_template VALUES("82","21");
INSERT INTO hdk_tbconfig_has_template VALUES("62","8");



DROP TABLE IF EXISTS hdk_tbconfig_user;

CREATE TABLE `hdk_tbconfig_user` (
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
) ENGINE=InnoDB AUTO_INCREMENT=274 DEFAULT CHARSET=latin1;

INSERT INTO hdk_tbconfig_user VALUES("260","1","","","[0,0,0,0,0,0,0,0,0,0,0,0,0,0]","[22,8,85,105,70,115,90,90,80,130,80,80,55,100]","[0,0,0,0,0,0,0,0]","[26,75,100,240,200,100,130,145]");
INSERT INTO hdk_tbconfig_user VALUES("261","1061","","","[0,0,0,0,0,0,0,0,0,0,0,0,0,0]","[22,8,85,105,70,115,90,90,80,130,80,80,55,100]","[0,0,0,0,0,0,0,0]","[26,75,100,240,200,100,130,145]");
INSERT INTO hdk_tbconfig_user VALUES("273","1078","","","[0,0,0,0,0,0,0,0,0,0,0,0,0,0]","[22,8,85,105,70,115,90,90,80,130,80,80,55,100]","[0,0,0,0,0,0,0,0]","[26,75,100,240,200,100,130,145]");



DROP TABLE IF EXISTS hdk_tbcore_area;

CREATE TABLE `hdk_tbcore_area` (
  `idarea` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `status` char(1) NOT NULL DEFAULT 'A',
  `default` int(1) DEFAULT 0,
  PRIMARY KEY (`idarea`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

INSERT INTO hdk_tbcore_area VALUES("10","IT","A","1");
INSERT INTO hdk_tbcore_area VALUES("11","Internal Communication","A","0");



DROP TABLE IF EXISTS hdk_tbcore_default;

CREATE TABLE `hdk_tbcore_default` (
  `idcoredefault` int(11) NOT NULL AUTO_INCREMENT,
  `table` char(50) NOT NULL DEFAULT '',
  `default` int(11) DEFAULT 0,
  PRIMARY KEY (`idcoredefault`),
  KEY `table` (`table`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1 AVG_ROW_LENGTH=2730;

INSERT INTO hdk_tbcore_default VALUES("1","area","1");
INSERT INTO hdk_tbcore_default VALUES("2","type","14");
INSERT INTO hdk_tbcore_default VALUES("3","item","132");
INSERT INTO hdk_tbcore_default VALUES("4","service","269");
INSERT INTO hdk_tbcore_default VALUES("5","type","6");
INSERT INTO hdk_tbcore_default VALUES("6","type","33");



DROP TABLE IF EXISTS hdk_tbcore_item;

CREATE TABLE `hdk_tbcore_item` (
  `iditem` int(11) NOT NULL AUTO_INCREMENT,
  `idtype` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `status` char(1) NOT NULL DEFAULT 'A',
  `selected` int(11) NOT NULL DEFAULT 0,
  `classify` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`iditem`),
  KEY `FK_hdk_tbcore_item` (`idtype`),
  CONSTRAINT `FK_hdk_tbcore_item` FOREIGN KEY (`idtype`) REFERENCES `hdk_tbcore_type` (`idtype`)
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

INSERT INTO hdk_tbcore_item VALUES("50","28","Office Domain","A","0","0");
INSERT INTO hdk_tbcore_item VALUES("55","29","Advertising Poster","A","0","0");
INSERT INTO hdk_tbcore_item VALUES("56","29","Company Signage","A","0","0");
INSERT INTO hdk_tbcore_item VALUES("57","32","Pictures","A","0","0");
INSERT INTO hdk_tbcore_item VALUES("58","33","SCM - Supply Chain Management","A","0","0");
INSERT INTO hdk_tbcore_item VALUES("59","33","CRM - Customer Relationship Management","A","0","0");
INSERT INTO hdk_tbcore_item VALUES("60","34","IoT Dashboard","A","0","0");
INSERT INTO hdk_tbcore_item VALUES("61","35","System Access","A","0","0");
INSERT INTO hdk_tbcore_item VALUES("62","36","Laser printer","A","0","0");
INSERT INTO hdk_tbcore_item VALUES("63","37","There is no Internet Access","A","0","0");
INSERT INTO hdk_tbcore_item VALUES("64","37","DNS Server Problems","A","0","0");
INSERT INTO hdk_tbcore_item VALUES("65","37","Modem & Router Issues","A","0","0");
INSERT INTO hdk_tbcore_item VALUES("66","37","IP Address Exhaustion","A","0","0");
INSERT INTO hdk_tbcore_item VALUES("67","37","Other connectivity issues","A","0","0");



DROP TABLE IF EXISTS hdk_tbcore_reason;

CREATE TABLE `hdk_tbcore_reason` (
  `idreason` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `idservice` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `status` char(1) NOT NULL DEFAULT 'A',
  PRIMARY KEY (`idreason`),
  KEY `idservice` (`idservice`),
  CONSTRAINT `hdk_tbcore_reason_ibfk_1` FOREIGN KEY (`idservice`) REFERENCES `hdk_tbcore_service` (`idservice`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AVG_ROW_LENGTH=16384 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;




DROP TABLE IF EXISTS hdk_tbcore_service;

CREATE TABLE `hdk_tbcore_service` (
  `idservice` int(11) NOT NULL AUTO_INCREMENT,
  `iditem` int(11) NOT NULL,
  `idpriority` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `status` char(1) NOT NULL DEFAULT 'A',
  `selected` int(11) NOT NULL DEFAULT 0,
  `classify` int(11) NOT NULL DEFAULT 1,
  `time_attendance` int(11) NOT NULL DEFAULT 0,
  `hours_attendance` int(11) NOT NULL DEFAULT 0,
  `days_attendance` int(11) NOT NULL DEFAULT 0,
  `ind_hours_minutes` char(1) NOT NULL DEFAULT 'H',
  PRIMARY KEY (`idservice`),
  KEY `FK_hdk_tbcore_service` (`iditem`),
  CONSTRAINT `FK_hdk_tbcore_service` FOREIGN KEY (`iditem`) REFERENCES `hdk_tbcore_item` (`iditem`)
) ENGINE=InnoDB AUTO_INCREMENT=118 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

INSERT INTO hdk_tbcore_service VALUES("85","50","3","Add client computers to domain","A","0","0","0","0","1","H");
INSERT INTO hdk_tbcore_service VALUES("86","50","2","Add user to domain","A","0","0","0","2","0","H");
INSERT INTO hdk_tbcore_service VALUES("87","50","3","Map Drive on Server","A","0","0","0","0","1","H");
INSERT INTO hdk_tbcore_service VALUES("88","50","1","Change user password","A","0","0","0","30","0","M");
INSERT INTO hdk_tbcore_service VALUES("89","55","5","Create a new","A","0","0","0","0","12","H");
INSERT INTO hdk_tbcore_service VALUES("90","55","3","Print Out","A","0","0","0","3","0","H");
INSERT INTO hdk_tbcore_service VALUES("91","56","3","Print Out","A","0","0","0","6","0","H");
INSERT INTO hdk_tbcore_service VALUES("92","57","3","Take pictures","A","0","0","0","2","0","H");
INSERT INTO hdk_tbcore_service VALUES("93","57","2","Print pictures","A","0","0","0","1","0","H");
INSERT INTO hdk_tbcore_service VALUES("94","57","5","Store pictures in the cloud","A","0","0","0","0","20","H");
INSERT INTO hdk_tbcore_service VALUES("95","58","2","Go live ","A","0","0","0","1","0","H");
INSERT INTO hdk_tbcore_service VALUES("96","58","2","Restore ","A","0","0","0","1","0","H");
INSERT INTO hdk_tbcore_service VALUES("97","59","2","Go live","A","0","0","0","1","0","H");
INSERT INTO hdk_tbcore_service VALUES("98","59","2","Restore","A","0","0","0","1","0","H");
INSERT INTO hdk_tbcore_service VALUES("99","60","3","Create widget","A","0","0","0","0","2","H");
INSERT INTO hdk_tbcore_service VALUES("100","60","3","Change widget","A","0","0","0","0","1","H");
INSERT INTO hdk_tbcore_service VALUES("101","60","1","Delete widget","A","0","0","0","1","0","H");
INSERT INTO hdk_tbcore_service VALUES("102","61","2","Change password","A","0","0","0","1","0","H");
INSERT INTO hdk_tbcore_service VALUES("103","61","2","Change access profile","A","0","0","0","1","0","H");
INSERT INTO hdk_tbcore_service VALUES("104","61","2","Create login","A","0","0","0","1","0","H");
INSERT INTO hdk_tbcore_service VALUES("105","61","2","Password unlock ","A","0","0","0","1","0","H");
INSERT INTO hdk_tbcore_service VALUES("106","61","2","Error accessing system","A","0","0","0","1","0","H");
INSERT INTO hdk_tbcore_service VALUES("107","61","2","Connection failed / Time out","A","0","0","0","1","0","H");
INSERT INTO hdk_tbcore_service VALUES("108","62","1","Knead paper","A","0","0","0","1","0","H");
INSERT INTO hdk_tbcore_service VALUES("109","62","2","To set up","A","0","0","0","1","0","H");
INSERT INTO hdk_tbcore_service VALUES("110","62","2","Uninstall","A","0","0","0","1","0","H");
INSERT INTO hdk_tbcore_service VALUES("111","62","2","Poor/smudged/flawed printing","A","0","0","0","1","0","H");
INSERT INTO hdk_tbcore_service VALUES("112","62","2","Install","A","0","0","0","1","0","H");
INSERT INTO hdk_tbcore_service VALUES("113","63","1","Fix the problem","A","0","0","0","1","0","H");
INSERT INTO hdk_tbcore_service VALUES("114","64","1","Fix the problem","A","0","0","0","1","0","H");
INSERT INTO hdk_tbcore_service VALUES("115","65","1","Fix the problem","A","0","0","0","30","0","M");
INSERT INTO hdk_tbcore_service VALUES("116","66","1","Fix the problem","A","0","0","0","30","0","M");
INSERT INTO hdk_tbcore_service VALUES("117","67","1","Fix the problem","A","0","0","0","30","0","M");



DROP TABLE IF EXISTS hdk_tbcore_type;

CREATE TABLE `hdk_tbcore_type` (
  `idtype` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `status` char(1) NOT NULL DEFAULT 'A',
  `selected` char(1) NOT NULL DEFAULT '0',
  `classify` char(1) NOT NULL DEFAULT '1',
  `idarea` int(11) NOT NULL,
  PRIMARY KEY (`idtype`),
  KEY `FK_hdk_tbcore_type` (`idarea`),
  CONSTRAINT `FK_hdk_tbcore_type` FOREIGN KEY (`idarea`) REFERENCES `hdk_tbcore_area` (`idarea`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

INSERT INTO hdk_tbcore_type VALUES("28","Network","A","0","0","10");
INSERT INTO hdk_tbcore_type VALUES("29","Visual Programming","A","0","0","11");
INSERT INTO hdk_tbcore_type VALUES("32","Photographic Collection","A","0","0","11");
INSERT INTO hdk_tbcore_type VALUES("33","Change","A","0","0","10");
INSERT INTO hdk_tbcore_type VALUES("34","Dashboards","A","0","0","10");
INSERT INTO hdk_tbcore_type VALUES("35","BI - QlikView","A","0","0","10");
INSERT INTO hdk_tbcore_type VALUES("36","Hardware","A","0","0","10");
INSERT INTO hdk_tbcore_type VALUES("37","Internet Access","A","0","0","10");



DROP TABLE IF EXISTS hdk_tbcostcenter;

CREATE TABLE `hdk_tbcostcenter` (
  `idcostcenter` int(11) NOT NULL AUTO_INCREMENT,
  `idperson` int(11) NOT NULL,
  `cod_costcenter` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `status` char(1) NOT NULL DEFAULT 'A',
  PRIMARY KEY (`idcostcenter`),
  KEY `FK_hdk_tbcostcenter` (`idperson`),
  CONSTRAINT `FK_hdk_tbcostcenter` FOREIGN KEY (`idperson`) REFERENCES `tbperson` (`idperson`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;




DROP TABLE IF EXISTS hdk_tbdepartment;

CREATE TABLE `hdk_tbdepartment` (
  `iddepartment` int(11) NOT NULL AUTO_INCREMENT,
  `idperson` int(11) NOT NULL,
  `cod_area` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `status` char(1) NOT NULL DEFAULT 'A',
  PRIMARY KEY (`iddepartment`),
  KEY `FK_hdk_tbdepartment` (`idperson`)
) ENGINE=InnoDB AUTO_INCREMENT=79 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

INSERT INTO hdk_tbdepartment VALUES("76","1038","0","Infrastructure","A");
INSERT INTO hdk_tbdepartment VALUES("77","1038","","Internal Communication","A");
INSERT INTO hdk_tbdepartment VALUES("78","1038","","Advertising and Marketing","A");



DROP TABLE IF EXISTS hdk_tbdepartment_has_person;

CREATE TABLE `hdk_tbdepartment_has_person` (
  `idperson` int(4) NOT NULL,
  `iddepartment` int(4) NOT NULL,
  KEY `FK_tbperson_has_juridical` (`idperson`),
  KEY `FK_tbdepartmet_has_person` (`iddepartment`),
  CONSTRAINT `FK_tbdepartmet_has_person` FOREIGN KEY (`iddepartment`) REFERENCES `hdk_tbdepartment` (`iddepartment`),
  CONSTRAINT `FK_tbperson_has_juridical` FOREIGN KEY (`idperson`) REFERENCES `tbperson` (`idperson`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

INSERT INTO hdk_tbdepartment_has_person VALUES("1061","76");
INSERT INTO hdk_tbdepartment_has_person VALUES("1078","78");



DROP TABLE IF EXISTS hdk_tbdownload;

CREATE TABLE `hdk_tbdownload` (
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
  `instruction` blob DEFAULT NULL,
  `status` char(1) NOT NULL DEFAULT 'A',
  PRIMARY KEY (`iddownload`),
  KEY `FK_hdk_tbdownload` (`iddownloadcategory`),
  CONSTRAINT `FK_hdk_tbdownload` FOREIGN KEY (`iddownloadcategory`) REFERENCES `hdk_tbdownload_category` (`iddownloadcategory`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;




DROP TABLE IF EXISTS hdk_tbdownload_category;

CREATE TABLE `hdk_tbdownload_category` (
  `iddownloadcategory` int(4) NOT NULL AUTO_INCREMENT,
  `category` varchar(200) NOT NULL,
  PRIMARY KEY (`iddownloadcategory`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

INSERT INTO hdk_tbdownload_category VALUES("1","General Files");
INSERT INTO hdk_tbdownload_category VALUES("2","Text Files");
INSERT INTO hdk_tbdownload_category VALUES("3","Image Files");
INSERT INTO hdk_tbdownload_category VALUES("4","Music");
INSERT INTO hdk_tbdownload_category VALUES("5","PDF");
INSERT INTO hdk_tbdownload_category VALUES("6","PHP");
INSERT INTO hdk_tbdownload_category VALUES("7","HTML");
INSERT INTO hdk_tbdownload_category VALUES("8","Overall Instructions");
INSERT INTO hdk_tbdownload_category VALUES("9","Rules");



DROP TABLE IF EXISTS hdk_tbevaluation;

CREATE TABLE `hdk_tbevaluation` (
  `idevaluation` int(11) NOT NULL AUTO_INCREMENT,
  `idquestion` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `status` char(1) NOT NULL DEFAULT 'A',
  `icon_name` varchar(25) NOT NULL,
  `checked` int(1) DEFAULT 0,
  PRIMARY KEY (`idevaluation`),
  KEY `FK_hdk_tbevaluation` (`idquestion`),
  CONSTRAINT `FK_hdk_tbevaluation` FOREIGN KEY (`idquestion`) REFERENCES `hdk_tbevaluationquestion` (`idquestion`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

INSERT INTO hdk_tbevaluation VALUES("1","2","Ruim","A","ico_ruim.gif","0");
INSERT INTO hdk_tbevaluation VALUES("2","2","MÃ©dio","A","ico_regular.gif","0");
INSERT INTO hdk_tbevaluation VALUES("3","2","Bom","A","ico_bom.gif","0");
INSERT INTO hdk_tbevaluation VALUES("4","2","Muito Bom","A","ico_otimo.gif","1");



DROP TABLE IF EXISTS hdk_tbevaluationquestion;

CREATE TABLE `hdk_tbevaluationquestion` (
  `idquestion` int(11) NOT NULL AUTO_INCREMENT,
  `question` varchar(200) NOT NULL,
  `status` char(1) NOT NULL DEFAULT 'A',
  PRIMARY KEY (`idquestion`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

INSERT INTO hdk_tbevaluationquestion VALUES("2","Como voce avalia o atendimento feito pelo atendente?","A");



DROP TABLE IF EXISTS hdk_tbevaluation_icon;

CREATE TABLE `hdk_tbevaluation_icon` (
  `idevaluation_icon` int(4) NOT NULL AUTO_INCREMENT,
  `file_name` varchar(50) NOT NULL,
  PRIMARY KEY (`idevaluation_icon`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

INSERT INTO hdk_tbevaluation_icon VALUES("1","dinheiro.jpg");
INSERT INTO hdk_tbevaluation_icon VALUES("2","tattoo_desenho.jpg");



DROP TABLE IF EXISTS hdk_tbevaluation_token;

CREATE TABLE `hdk_tbevaluation_token` (
  `idtoken` int(11) NOT NULL AUTO_INCREMENT,
  `code_request` varchar(20) NOT NULL,
  `token` varchar(40) NOT NULL,
  PRIMARY KEY (`idtoken`),
  KEY `FK_code_request` (`code_request`),
  CONSTRAINT `FK_code_request` FOREIGN KEY (`code_request`) REFERENCES `hdk_tbrequest` (`code_request`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=latin1;




DROP TABLE IF EXISTS hdk_tbexecutionorder_person;

CREATE TABLE `hdk_tbexecutionorder_person` (
  `idexecutionorder` int(4) NOT NULL AUTO_INCREMENT,
  `code_request` varchar(20) NOT NULL,
  `idperson` int(4) NOT NULL,
  `exorder` int(3) NOT NULL,
  PRIMARY KEY (`idexecutionorder`),
  KEY `FK_hdk_tbexecutionorder_person` (`idperson`),
  KEY `FK_hdk_tbexecutionorder_person2` (`code_request`),
  CONSTRAINT `FK_hdk_tbexecutionorder_person` FOREIGN KEY (`idperson`) REFERENCES `tbperson` (`idperson`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;




DROP TABLE IF EXISTS hdk_tbexternalfield;

CREATE TABLE `hdk_tbexternalfield` (
  `idexternalfield` int(11) NOT NULL AUTO_INCREMENT,
  `idexternalsettings` int(11) NOT NULL,
  `fieldname` varchar(255) DEFAULT 'NULL',
  `VALUE` varchar(255) DEFAULT 'NULL',
  PRIMARY KEY (`idexternalfield`,`idexternalsettings`),
  KEY `IDX_hdk_tbexternalfield_fieldname` (`fieldname`),
  KEY `FK_hdk_tbexternalfield_hdk_tbexternalsettings_idexternallsetting` (`idexternalsettings`),
  CONSTRAINT `FK_hdk_tbexternalfield_hdk_tbexternalsettings_idexternallsetting` FOREIGN KEY (`idexternalsettings`) REFERENCES `hdk_tbexternalsettings` (`idexternalsetting`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1 AVG_ROW_LENGTH=8192 ROW_FORMAT=DYNAMIC;




DROP TABLE IF EXISTS hdk_tbexternallapp;

CREATE TABLE `hdk_tbexternallapp` (
  `idexternalapp` int(11) NOT NULL AUTO_INCREMENT,
  `appname` varchar(255) DEFAULT 'NULL',
  `url` varchar(255) DEFAULT 'NULL',
  PRIMARY KEY (`idexternalapp`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=latin1 AVG_ROW_LENGTH=16384 ROW_FORMAT=DYNAMIC;

INSERT INTO hdk_tbexternallapp VALUES("50","Trello","https://api.trello.com");
INSERT INTO hdk_tbexternallapp VALUES("51","Pushover","https://api.pushover.net/1/messages.json");



DROP TABLE IF EXISTS hdk_tbexternalsettings;

CREATE TABLE `hdk_tbexternalsettings` (
  `idexternalsetting` int(11) NOT NULL AUTO_INCREMENT,
  `idexternalapp` int(11) DEFAULT NULL,
  `idperson` int(11) DEFAULT NULL,
  PRIMARY KEY (`idexternalsetting`),
  UNIQUE KEY `UK_hdk_tbexternalsettings` (`idexternalapp`,`idperson`),
  KEY `FK_hdk_tbexternalsettings_tbperson_idperson` (`idperson`),
  CONSTRAINT `FK_hdk_tbexternalsettings_hdk_tbexternallapp_idexternalapp` FOREIGN KEY (`idexternalapp`) REFERENCES `hdk_tbexternallapp` (`idexternalapp`),
  CONSTRAINT `FK_hdk_tbexternalsettings_tbperson_idperson` FOREIGN KEY (`idperson`) REFERENCES `tbperson` (`idperson`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1 AVG_ROW_LENGTH=16384 ROW_FORMAT=DYNAMIC;




DROP TABLE IF EXISTS hdk_tbgetemail;

CREATE TABLE `hdk_tbgetemail` (
  `idgetemail` int(11) NOT NULL AUTO_INCREMENT,
  `serverurl` varchar(60) DEFAULT NULL,
  `servertype` varchar(10) DEFAULT NULL,
  `serverport` char(5) DEFAULT NULL,
  `emailacct` varchar(100) DEFAULT NULL,
  `user` varchar(80) DEFAULT NULL,
  `password` char(30) DEFAULT NULL,
  `ind_create_user` int(1) DEFAULT 0,
  `ind_delete_server` int(1) DEFAULT 1,
  `idservice` int(11) DEFAULT NULL,
  `filter_from` varchar(200) DEFAULT NULL,
  `filter_subject` varchar(200) DEFAULT NULL,
  `login_layout` varchar(1) DEFAULT NULL,
  `email_response_as_note` int(1) DEFAULT 0,
  PRIMARY KEY (`idgetemail`),
  KEY `FK_hdk_tbgetemail` (`idservice`),
  CONSTRAINT `FK_hdk_tbgetemail` FOREIGN KEY (`idservice`) REFERENCES `hdk_tbcore_service` (`idservice`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;




DROP TABLE IF EXISTS hdk_tbgetemaildepartment;

CREATE TABLE `hdk_tbgetemaildepartment` (
  `idgetemaildepartment` int(11) NOT NULL AUTO_INCREMENT,
  `idgetemail` int(11) DEFAULT NULL,
  `iddepartment` int(11) DEFAULT NULL,
  PRIMARY KEY (`idgetemaildepartment`),
  KEY `FK_hdk_tbgetemaildepartment` (`iddepartment`),
  KEY `FK_hdk_tbgetemaildepartment_IDGETEMAIL` (`idgetemail`),
  CONSTRAINT `FK_hdk_tbgetemaildepartment` FOREIGN KEY (`iddepartment`) REFERENCES `hdk_tbdepartment` (`iddepartment`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_hdk_tbgetemaildepartment_IDGETEMAIL` FOREIGN KEY (`idgetemail`) REFERENCES `hdk_tbgetemail` (`idgetemail`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;




DROP TABLE IF EXISTS hdk_tbgroup;

CREATE TABLE `hdk_tbgroup` (
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
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

INSERT INTO hdk_tbgroup VALUES("50","1054","1","1038","N","A");
INSERT INTO hdk_tbgroup VALUES("51","1055","1","1038","N","A");
INSERT INTO hdk_tbgroup VALUES("54","1058","1","1038","N","A");
INSERT INTO hdk_tbgroup VALUES("55","1059","1","1038","N","A");
INSERT INTO hdk_tbgroup VALUES("56","1060","1","1038","N","A");
INSERT INTO hdk_tbgroup VALUES("57","1073","1","1038","N","A");
INSERT INTO hdk_tbgroup VALUES("58","1074","1","1038","N","A");



DROP TABLE IF EXISTS hdk_tbgroup_has_person;

CREATE TABLE `hdk_tbgroup_has_person` (
  `idperson` int(4) NOT NULL,
  `idgroup` int(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

INSERT INTO hdk_tbgroup_has_person VALUES("272","3");
INSERT INTO hdk_tbgroup_has_person VALUES("275","3");
INSERT INTO hdk_tbgroup_has_person VALUES("279","3");
INSERT INTO hdk_tbgroup_has_person VALUES("278","3");
INSERT INTO hdk_tbgroup_has_person VALUES("611","3");
INSERT INTO hdk_tbgroup_has_person VALUES("276","3");
INSERT INTO hdk_tbgroup_has_person VALUES("273","3");
INSERT INTO hdk_tbgroup_has_person VALUES("607","4");
INSERT INTO hdk_tbgroup_has_person VALUES("613","4");
INSERT INTO hdk_tbgroup_has_person VALUES("117","4");
INSERT INTO hdk_tbgroup_has_person VALUES("612","4");
INSERT INTO hdk_tbgroup_has_person VALUES("608","4");
INSERT INTO hdk_tbgroup_has_person VALUES("611","4");
INSERT INTO hdk_tbgroup_has_person VALUES("609","4");
INSERT INTO hdk_tbgroup_has_person VALUES("652","4");
INSERT INTO hdk_tbgroup_has_person VALUES("277","3");
INSERT INTO hdk_tbgroup_has_person VALUES("881","4");
INSERT INTO hdk_tbgroup_has_person VALUES("881","3");
INSERT INTO hdk_tbgroup_has_person VALUES("613","3");
INSERT INTO hdk_tbgroup_has_person VALUES("117","3");
INSERT INTO hdk_tbgroup_has_person VALUES("609","3");
INSERT INTO hdk_tbgroup_has_person VALUES("932","4");
INSERT INTO hdk_tbgroup_has_person VALUES("932","3");
INSERT INTO hdk_tbgroup_has_person VALUES("949","4");
INSERT INTO hdk_tbgroup_has_person VALUES("949","3");
INSERT INTO hdk_tbgroup_has_person VALUES("1025","6");
INSERT INTO hdk_tbgroup_has_person VALUES("1024","6");
INSERT INTO hdk_tbgroup_has_person VALUES("1025","28");
INSERT INTO hdk_tbgroup_has_person VALUES("1024","28");
INSERT INTO hdk_tbgroup_has_person VALUES("1025","5");
INSERT INTO hdk_tbgroup_has_person VALUES("1024","5");
INSERT INTO hdk_tbgroup_has_person VALUES("1024","30");
INSERT INTO hdk_tbgroup_has_person VALUES("1024","31");
INSERT INTO hdk_tbgroup_has_person VALUES("1025","29");
INSERT INTO hdk_tbgroup_has_person VALUES("1024","29");
INSERT INTO hdk_tbgroup_has_person VALUES("992","6");
INSERT INTO hdk_tbgroup_has_person VALUES("1025","35");
INSERT INTO hdk_tbgroup_has_person VALUES("1025","34");
INSERT INTO hdk_tbgroup_has_person VALUES("1025","37");
INSERT INTO hdk_tbgroup_has_person VALUES("1052","43");
INSERT INTO hdk_tbgroup_has_person VALUES("1051","42");
INSERT INTO hdk_tbgroup_has_person VALUES("1061","56");
INSERT INTO hdk_tbgroup_has_person VALUES("1061","50");
INSERT INTO hdk_tbgroup_has_person VALUES("1061","51");
INSERT INTO hdk_tbgroup_has_person VALUES("1061","58");



DROP TABLE IF EXISTS hdk_tbgroup_has_service;

CREATE TABLE `hdk_tbgroup_has_service` (
  `idgroup` int(4) NOT NULL,
  `idservice` int(4) NOT NULL,
  KEY `COD_GRUPO` (`idgroup`),
  KEY `FK_SERVICO_X_GRUPO_SERVICO` (`idservice`),
  CONSTRAINT `FK_hdk_tbgroup_has_service` FOREIGN KEY (`idgroup`) REFERENCES `hdk_tbgroup` (`idgroup`),
  CONSTRAINT `FK_hdk_tbgroup_has_service2` FOREIGN KEY (`idservice`) REFERENCES `hdk_tbcore_service` (`idservice`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO hdk_tbgroup_has_service VALUES("50","85");
INSERT INTO hdk_tbgroup_has_service VALUES("50","86");
INSERT INTO hdk_tbgroup_has_service VALUES("50","87");
INSERT INTO hdk_tbgroup_has_service VALUES("50","88");
INSERT INTO hdk_tbgroup_has_service VALUES("57","89");
INSERT INTO hdk_tbgroup_has_service VALUES("57","90");
INSERT INTO hdk_tbgroup_has_service VALUES("57","91");
INSERT INTO hdk_tbgroup_has_service VALUES("57","92");
INSERT INTO hdk_tbgroup_has_service VALUES("57","93");
INSERT INTO hdk_tbgroup_has_service VALUES("57","94");
INSERT INTO hdk_tbgroup_has_service VALUES("58","95");
INSERT INTO hdk_tbgroup_has_service VALUES("58","96");
INSERT INTO hdk_tbgroup_has_service VALUES("58","97");
INSERT INTO hdk_tbgroup_has_service VALUES("58","98");
INSERT INTO hdk_tbgroup_has_service VALUES("58","99");
INSERT INTO hdk_tbgroup_has_service VALUES("58","100");
INSERT INTO hdk_tbgroup_has_service VALUES("58","101");
INSERT INTO hdk_tbgroup_has_service VALUES("58","102");
INSERT INTO hdk_tbgroup_has_service VALUES("58","103");
INSERT INTO hdk_tbgroup_has_service VALUES("58","104");
INSERT INTO hdk_tbgroup_has_service VALUES("58","105");
INSERT INTO hdk_tbgroup_has_service VALUES("58","106");
INSERT INTO hdk_tbgroup_has_service VALUES("58","107");
INSERT INTO hdk_tbgroup_has_service VALUES("56","108");
INSERT INTO hdk_tbgroup_has_service VALUES("56","109");
INSERT INTO hdk_tbgroup_has_service VALUES("56","110");
INSERT INTO hdk_tbgroup_has_service VALUES("56","111");
INSERT INTO hdk_tbgroup_has_service VALUES("56","112");
INSERT INTO hdk_tbgroup_has_service VALUES("50","113");
INSERT INTO hdk_tbgroup_has_service VALUES("50","114");
INSERT INTO hdk_tbgroup_has_service VALUES("50","115");
INSERT INTO hdk_tbgroup_has_service VALUES("50","116");
INSERT INTO hdk_tbgroup_has_service VALUES("50","117");



DROP TABLE IF EXISTS hdk_tbnote;

CREATE TABLE `hdk_tbnote` (
  `idnote` int(10) NOT NULL AUTO_INCREMENT,
  `code_request` bigint(16) unsigned DEFAULT NULL,
  `idperson` int(10) unsigned DEFAULT NULL,
  `description` blob DEFAULT NULL,
  `entry_date` datetime DEFAULT NULL,
  `minutes` float DEFAULT NULL,
  `start_hour` varchar(8) DEFAULT NULL,
  `finish_hour` varchar(8) DEFAULT NULL,
  `IND_CHAMADO` int(10) unsigned DEFAULT NULL,
  `execution_date` datetime DEFAULT NULL,
  `hour_type` int(3) unsigned DEFAULT NULL,
  `service_value` float(10,4) DEFAULT NULL,
  `public` int(1) unsigned DEFAULT 1,
  `idtype` int(3) unsigned DEFAULT NULL,
  `ip_adress` varchar(30) DEFAULT NULL,
  `callback` int(1) DEFAULT 0,
  `flag_opened` tinyint(1) DEFAULT NULL,
  `code_email` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`idnote`),
  KEY `COD_SOLICITACAO` (`code_request`),
  KEY `COD_TIPO` (`idtype`),
  KEY `COD_USUARIO` (`idperson`),
  KEY `DAT_CADASTRO` (`entry_date`),
  KEY `idx_entry_date` (`entry_date`)
) ENGINE=InnoDB AUTO_INCREMENT=18430 DEFAULT CHARSET=latin1;




DROP TABLE IF EXISTS hdk_tbnote_attachments;

CREATE TABLE `hdk_tbnote_attachments` (
  `idnote_attachments` int(10) NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) DEFAULT ' ',
  PRIMARY KEY (`idnote_attachments`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;




DROP TABLE IF EXISTS hdk_tbnote_has_attachments;

CREATE TABLE `hdk_tbnote_has_attachments` (
  `idnote` int(10) NOT NULL,
  `idnote_attachments` int(10) NOT NULL,
  KEY `idnote` (`idnote`),
  KEY `idnote_attachments` (`idnote_attachments`),
  CONSTRAINT `hdk_tbnote_has_attachments_ibfk_1` FOREIGN KEY (`idnote`) REFERENCES `hdk_tbnote` (`idnote`),
  CONSTRAINT `hdk_tbnote_has_attachments_ibfk_2` FOREIGN KEY (`idnote_attachments`) REFERENCES `hdk_tbnote_attachments` (`idnote_attachments`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;




DROP TABLE IF EXISTS hdk_tbnote_type;

CREATE TABLE `hdk_tbnote_type` (
  `idtypenote` int(4) unsigned NOT NULL,
  `description` varchar(60) DEFAULT NULL,
  `available` int(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`idtypenote`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO hdk_tbnote_type VALUES("1","{$smarty.config.apont_tipo_visivel_user}","1");
INSERT INTO hdk_tbnote_type VALUES("2","{$smarty.config.apont_tipo_visivel_only_attendance}","1");
INSERT INTO hdk_tbnote_type VALUES("3","{$smarty.config.apont_tipo_criado__pelo_sistema}","0");
INSERT INTO hdk_tbnote_type VALUES("4","{$smarty.config.apont_tipo_aprovacao_solicitacao}","0");



DROP TABLE IF EXISTS hdk_tbpriority;

CREATE TABLE `hdk_tbpriority` (
  `idpriority` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `order` int(11) NOT NULL,
  `color` varchar(8) NOT NULL,
  `default` int(11) NOT NULL DEFAULT 0,
  `vip` int(11) NOT NULL DEFAULT 0,
  `limit_hours` int(11) NOT NULL DEFAULT 0,
  `limit_days` int(11) NOT NULL DEFAULT 0,
  `status` char(1) NOT NULL DEFAULT 'A',
  PRIMARY KEY (`idpriority`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

INSERT INTO hdk_tbpriority VALUES("1","Critical","8","#660000","0","0","1","0","A");
INSERT INTO hdk_tbpriority VALUES("2","High","16","#ff0000","0","0","4","0","A");
INSERT INTO hdk_tbpriority VALUES("3","Average","32","#ffcc33","0","0","8","0","A");
INSERT INTO hdk_tbpriority VALUES("4","Low","32","#66ff66","1","0","24","0","A");
INSERT INTO hdk_tbpriority VALUES("5","Planned","5","#3399ff","0","0","0","15","N");



DROP TABLE IF EXISTS hdk_tbproperty;

CREATE TABLE `hdk_tbproperty` (
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
  `observations` blob DEFAULT NULL,
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;




DROP TABLE IF EXISTS hdk_tbproperty_category;

CREATE TABLE `hdk_tbproperty_category` (
  `idcategory` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`idcategory`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;




DROP TABLE IF EXISTS hdk_tbproperty_manufacturer;

CREATE TABLE `hdk_tbproperty_manufacturer` (
  `idmanufacturer` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`idmanufacturer`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;




DROP TABLE IF EXISTS hdk_tbproperty_provider;

CREATE TABLE `hdk_tbproperty_provider` (
  `idprovider` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`idprovider`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;




DROP TABLE IF EXISTS hdk_tbreason;

CREATE TABLE `hdk_tbreason` (
  `idreason` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `idservice` int(11) NOT NULL,
  `reason` varchar(100) NOT NULL,
  `status` char(1) NOT NULL DEFAULT 'A',
  PRIMARY KEY (`idreason`),
  KEY `FK_hdk_tbreason` (`idservice`),
  CONSTRAINT `FK_hdk_tbreason` FOREIGN KEY (`idservice`) REFERENCES `hdk_tbcore_service` (`idservice`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;




DROP TABLE IF EXISTS hdk_tbrequest;

CREATE TABLE `hdk_tbrequest` (
  `idrequest` int(11) NOT NULL AUTO_INCREMENT,
  `code_request` varchar(20) NOT NULL,
  `subject` blob NOT NULL,
  `description` blob NOT NULL,
  `idtype` int(11) NOT NULL,
  `iditem` int(11) NOT NULL,
  `idservice` int(11) NOT NULL,
  `idreason` int(11) unsigned DEFAULT NULL,
  `idpriority` int(11) NOT NULL,
  `idsource` int(11) NOT NULL,
  `idperson_creator` int(11) DEFAULT NULL COMMENT 'id da pessoa que abriu a solicitacao em nome de outra, sï¿½ ï¿½ preenchido nesse tipo de situaï¿½ï¿½o',
  `entry_date` datetime NOT NULL COMMENT 'data em que foi registrada no sistema',
  `service_value` float DEFAULT NULL,
  `os_number` varchar(20) DEFAULT NULL,
  `label` varchar(20) DEFAULT NULL,
  `extensions_number` varchar(2) DEFAULT '0' COMMENT 'numero de vezes que foi prorrogada',
  `idperson_juridical` mediumint(5) DEFAULT NULL COMMENT 'id da empresa do usuario',
  `serial_number` varchar(20) DEFAULT NULL,
  `idattendance_way` smallint(6) unsigned DEFAULT NULL COMMENT 'forma de atendimento',
  `expire_date` datetime DEFAULT NULL,
  `code_group` mediumint(9) DEFAULT NULL COMMENT 'esta coluna ï¿½ para dizer para qual grupo que sera designado o atendimento',
  `code_email` varchar(240) DEFAULT NULL,
  `idperson_owner` mediumint(9) NOT NULL COMMENT 'esta coluna o id de quem necessita do serviï¿½o, que pode nao ser o mesmo que abriu',
  `idstatus` tinyint(2) DEFAULT NULL COMMENT 'aqui ficara o id do status atual da solicitacao',
  `flag_opened` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'essa coluna mostra se o atendente ja viu a solicitacao nova sem assumir',
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
  KEY `idx_1` (`code_request`,`idperson_owner`,`idstatus`),
  KEY `idx_2` (`code_request`,`idstatus`),
  CONSTRAINT `FK_iditem` FOREIGN KEY (`iditem`) REFERENCES `hdk_tbcore_item` (`iditem`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_idperson_creator` FOREIGN KEY (`idperson_creator`) REFERENCES `tbperson` (`idperson`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_idpriority` FOREIGN KEY (`idpriority`) REFERENCES `hdk_tbpriority` (`idpriority`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_idreason` FOREIGN KEY (`idreason`) REFERENCES `hdk_tbcore_reason` (`idreason`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_idservice` FOREIGN KEY (`idservice`) REFERENCES `hdk_tbcore_service` (`idservice`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_idsource` FOREIGN KEY (`idsource`) REFERENCES `hdk_tbsource` (`idsource`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_idtype` FOREIGN KEY (`idtype`) REFERENCES `hdk_tbcore_type` (`idtype`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=latin1;




DROP TABLE IF EXISTS hdk_tbrequest_addinfo;

CREATE TABLE `hdk_tbrequest_addinfo` (
  `idrequest_addinfo` int(11) NOT NULL AUTO_INCREMENT,
  `idaddinfo` int(11) NOT NULL,
  `code_request` varchar(20) NOT NULL,
  PRIMARY KEY (`idrequest_addinfo`),
  KEY `fk_addinfo_idaddinfo_hdk_tbaddinfo` (`idaddinfo`),
  KEY `fk_addinfo_code_request_hdk_tbrequest` (`code_request`),
  CONSTRAINT `fk_addinfo_code_request_hdk_tbrequest` FOREIGN KEY (`code_request`) REFERENCES `hdk_tbrequest` (`code_request`),
  CONSTRAINT `fk_addinfo_idaddinfo_hdk_tbaddinfo` FOREIGN KEY (`idaddinfo`) REFERENCES `hdk_tbaddinfo` (`idaddinfo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;




DROP TABLE IF EXISTS hdk_tbrequest_approval;

CREATE TABLE `hdk_tbrequest_approval` (
  `request_code` bigint(16) NOT NULL,
  `idapproval` int(10) DEFAULT NULL,
  `idnote` int(10) DEFAULT NULL,
  `idperson` int(10) NOT NULL DEFAULT 0,
  `order` int(2) NOT NULL DEFAULT 0,
  `fl_rejected` int(1) NOT NULL DEFAULT 0,
  `fl_recalculate` int(1) NOT NULL DEFAULT 0,
  `idrequestapproval` int(4) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`idrequestapproval`),
  KEY `FK_hdk_tbrequest_approval` (`idapproval`),
  CONSTRAINT `FK_hdk_tbrequest_approval` FOREIGN KEY (`idapproval`) REFERENCES `hdk_tbapproval_rule` (`idapproval`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;




DROP TABLE IF EXISTS hdk_tbrequest_attachment;

CREATE TABLE `hdk_tbrequest_attachment` (
  `idrequest_attachment` int(11) NOT NULL AUTO_INCREMENT,
  `code_request` varchar(20) DEFAULT NULL,
  `file_name` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`idrequest_attachment`),
  KEY `FK_id_request_attachment` (`code_request`),
  CONSTRAINT `FK_hdk_tbrequest_attachment` FOREIGN KEY (`code_request`) REFERENCES `hdk_tbrequest` (`code_request`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;




DROP TABLE IF EXISTS hdk_tbrequest_change_expire;

CREATE TABLE `hdk_tbrequest_change_expire` (
  `idchangeexpire` int(11) NOT NULL AUTO_INCREMENT,
  `code_request` varchar(20) NOT NULL,
  `reason` blob DEFAULT NULL,
  `idperson` int(11) NOT NULL,
  `changedate` date NOT NULL,
  PRIMARY KEY (`idchangeexpire`),
  KEY `FK_chexpire_code_request` (`code_request`),
  KEY `FK_chexpire_idperson` (`idperson`),
  CONSTRAINT `FK_chexpire_code_request` FOREIGN KEY (`code_request`) REFERENCES `hdk_tbrequest` (`code_request`),
  CONSTRAINT `FK_chexpire_idperson` FOREIGN KEY (`idperson`) REFERENCES `tbperson` (`idperson`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;




DROP TABLE IF EXISTS hdk_tbrequest_code;

CREATE TABLE `hdk_tbrequest_code` (
  `cod_request` varchar(20) NOT NULL,
  `cod_month` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

INSERT INTO hdk_tbrequest_code VALUES("7","201208");
INSERT INTO hdk_tbrequest_code VALUES("109","201209");
INSERT INTO hdk_tbrequest_code VALUES("72","201210");
INSERT INTO hdk_tbrequest_code VALUES("27","201211");
INSERT INTO hdk_tbrequest_code VALUES("4","201301");
INSERT INTO hdk_tbrequest_code VALUES("2","201405");
INSERT INTO hdk_tbrequest_code VALUES("265","201406");
INSERT INTO hdk_tbrequest_code VALUES("412","201407");
INSERT INTO hdk_tbrequest_code VALUES("367","201408");
INSERT INTO hdk_tbrequest_code VALUES("360","201409");
INSERT INTO hdk_tbrequest_code VALUES("358","201410");
INSERT INTO hdk_tbrequest_code VALUES("315","201411");
INSERT INTO hdk_tbrequest_code VALUES("269","201412");
INSERT INTO hdk_tbrequest_code VALUES("326","201501");
INSERT INTO hdk_tbrequest_code VALUES("326","201502");
INSERT INTO hdk_tbrequest_code VALUES("352","201503");
INSERT INTO hdk_tbrequest_code VALUES("157","201504");
INSERT INTO hdk_tbrequest_code VALUES("20","201602");
INSERT INTO hdk_tbrequest_code VALUES("3","201905");
INSERT INTO hdk_tbrequest_code VALUES("4","201906");
INSERT INTO hdk_tbrequest_code VALUES("2","201907");
INSERT INTO hdk_tbrequest_code VALUES("2","201908");
INSERT INTO hdk_tbrequest_code VALUES("12","201909");
INSERT INTO hdk_tbrequest_code VALUES("9","201911");
INSERT INTO hdk_tbrequest_code VALUES("62","201912");



DROP TABLE IF EXISTS hdk_tbrequest_dates;

CREATE TABLE `hdk_tbrequest_dates` (
  `idrequestdate` int(11) NOT NULL AUTO_INCREMENT,
  `code_request` varchar(20) NOT NULL,
  `forwarded_date` datetime DEFAULT NULL,
  `approval_date` datetime DEFAULT NULL,
  `finish_date` datetime DEFAULT NULL,
  `rejection_date` datetime DEFAULT NULL,
  `assume_date` datetime DEFAULT NULL,
  PRIMARY KEY (`idrequestdate`),
  KEY `FK_hdk_tbrequest_dates` (`code_request`),
  CONSTRAINT `FK_hdk_tbrequest_dates` FOREIGN KEY (`code_request`) REFERENCES `hdk_tbrequest` (`code_request`)
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=latin1;




DROP TABLE IF EXISTS hdk_tbrequest_emailcron;

CREATE TABLE `hdk_tbrequest_emailcron` (
  `idrequest_emailcron` int(11) NOT NULL AUTO_INCREMENT,
  `code_request` varchar(20) DEFAULT NULL,
  `date_in` datetime DEFAULT NULL,
  `date_out` datetime DEFAULT NULL,
  `send` tinyint(1) DEFAULT NULL,
  `operation` varchar(20) DEFAULT NULL,
  `reason` varchar(250) DEFAULT 'NULL',
  PRIMARY KEY (`idrequest_emailcron`),
  KEY `code_request` (`code_request`),
  KEY `date_in` (`date_in`),
  KEY `date_out` (`date_out`),
  KEY `send` (`send`),
  CONSTRAINT `hdk_tbrequest_emailcron_ibfk_2` FOREIGN KEY (`code_request`) REFERENCES `hdk_tbrequest` (`code_request`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AVG_ROW_LENGTH=564;




DROP TABLE IF EXISTS hdk_tbrequest_evaluation;

CREATE TABLE `hdk_tbrequest_evaluation` (
  `idrequestevaluation` int(11) NOT NULL AUTO_INCREMENT,
  `idevaluation` int(11) NOT NULL,
  `code_request` varchar(20) NOT NULL,
  `date` datetime DEFAULT NULL,
  PRIMARY KEY (`idrequestevaluation`),
  KEY `FK_hdk_tbrequest_evaluation` (`idevaluation`),
  KEY `CODE_REQUEST_IDX` (`code_request`),
  CONSTRAINT `FK_hdk_tbrequest_evaluation` FOREIGN KEY (`idevaluation`) REFERENCES `hdk_tbevaluation` (`idevaluation`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=3124 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

INSERT INTO hdk_tbrequest_evaluation VALUES("3123","4","201909000001","2019-09-18 13:31:44");



DROP TABLE IF EXISTS hdk_tbrequest_has_tbproperty;

CREATE TABLE `hdk_tbrequest_has_tbproperty` (
  `idrequest` int(11) DEFAULT NULL,
  `idproperty` int(11) DEFAULT NULL,
  `label` varchar(20) DEFAULT NULL,
  `serial_number` varchar(20) DEFAULT NULL,
  KEY `FK_idrequest_property` (`idrequest`),
  KEY `FK_idproperty_request` (`idproperty`),
  CONSTRAINT `FK_idproperty_request` FOREIGN KEY (`idproperty`) REFERENCES `hdk_tbproperty` (`idproperty`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_idrequest_property` FOREIGN KEY (`idrequest`) REFERENCES `hdk_tbrequest` (`idrequest`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;




DROP TABLE IF EXISTS hdk_tbrequest_in_charge;

CREATE TABLE `hdk_tbrequest_in_charge` (
  `idrequest_in_charge` int(11) NOT NULL AUTO_INCREMENT,
  `code_request` varchar(20) NOT NULL,
  `id_in_charge` int(11) NOT NULL,
  `type` varchar(1) NOT NULL COMMENT 'aqui sera o tipo de responsï¿½vel, analista ou grupo de atendimento, definido por O(operator) ou G(group)',
  `ind_in_charge` varchar(1) DEFAULT NULL,
  `ind_repass` varchar(1) NOT NULL DEFAULT 'N',
  `ind_track` smallint(1) DEFAULT 0 COMMENT 'Aqui vai ficar marcado se o grupo continua vizualizando apï¿½s alguï¿½m assumir',
  `ind_operator_aux` smallint(1) DEFAULT 0,
  PRIMARY KEY (`idrequest_in_charge`),
  KEY `FK_idrequest` (`code_request`),
  KEY `FK_id_person_in_charge` (`id_in_charge`),
  KEY `idx_1` (`code_request`,`id_in_charge`,`ind_in_charge`),
  KEY `idx_2` (`id_in_charge`,`ind_in_charge`),
  KEY `idx_3` (`ind_in_charge`)
) ENGINE=InnoDB AUTO_INCREMENT=183 DEFAULT CHARSET=latin1;




DROP TABLE IF EXISTS hdk_tbrequest_log;

CREATE TABLE `hdk_tbrequest_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cod_request` varchar(20) NOT NULL,
  `date` datetime NOT NULL,
  `idstatus` mediumint(9) NOT NULL,
  `idperson` mediumint(9) NOT NULL,
  `reopened` char(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `NewIndex1` (`cod_request`),
  KEY `idx_1` (`cod_request`,`idperson`),
  CONSTRAINT `FK_hdk_tbrequest_log` FOREIGN KEY (`cod_request`) REFERENCES `hdk_tbrequest` (`code_request`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=189 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;




DROP TABLE IF EXISTS hdk_tbrequest_repassed;

CREATE TABLE `hdk_tbrequest_repassed` (
  `date` datetime NOT NULL,
  `idnote` int(10) NOT NULL,
  `code_request` varchar(20) NOT NULL,
  KEY `FK_hdk_tbrequest_repassed` (`code_request`),
  KEY `FK_hdk_tbrequest_repassed2` (`idnote`),
  CONSTRAINT `FK_hdk_tbrequest_repassed` FOREIGN KEY (`code_request`) REFERENCES `hdk_tbrequest` (`code_request`),
  CONSTRAINT `idnote_fk_idnote` FOREIGN KEY (`idnote`) REFERENCES `hdk_tbnote` (`idnote`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;




DROP TABLE IF EXISTS hdk_tbrequest_times;

CREATE TABLE `hdk_tbrequest_times` (
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
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;




DROP TABLE IF EXISTS hdk_tbsource;

CREATE TABLE `hdk_tbsource` (
  `idsource` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `icon` varchar(100) NOT NULL,
  PRIMARY KEY (`idsource`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

INSERT INTO hdk_tbsource VALUES("1","Helpdezk","hpdx.ico");
INSERT INTO hdk_tbsource VALUES("2","Telephone","fone.ico");
INSERT INTO hdk_tbsource VALUES("3","Email","mail.ico");
INSERT INTO hdk_tbsource VALUES("8","Webservice","web.ico");



DROP TABLE IF EXISTS hdk_tbstatus;

CREATE TABLE `hdk_tbstatus` (
  `idstatus` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(40) NOT NULL,
  `user_view` varchar(40) NOT NULL,
  `color` varchar(8) NOT NULL,
  `status` char(1) NOT NULL DEFAULT 'A',
  `idstatus_source` int(2) NOT NULL DEFAULT 3,
  PRIMARY KEY (`idstatus`),
  KEY `idx_1` (`idstatus_source`)
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

INSERT INTO hdk_tbstatus VALUES("1","1 - New","1 - Awaiting Service","#ff0033","A","1");
INSERT INTO hdk_tbstatus VALUES("2","2 - Repassed","2 - Passed on to another Attendant","#0033ff","A","1");
INSERT INTO hdk_tbstatus VALUES("3","3 - On Attendance","3 - In attendance","#0033ff","A","3");
INSERT INTO hdk_tbstatus VALUES("4","6 - Waiting for Approval","6 - Waiting my approval","#6600cc","A","4");
INSERT INTO hdk_tbstatus VALUES("5","7 - Finished","7 - Ticket completed","#339900","A","5");
INSERT INTO hdk_tbstatus VALUES("6","8 - Rejected","8 - Unable to fulfill ","#339900","A","6");
INSERT INTO hdk_tbstatus VALUES("11","9 - Canceled by user","9 - Canceled","#339900","A","3");
INSERT INTO hdk_tbstatus VALUES("50","4 - Waiting for customer","4 - Awaiting Your Opinion","#ffcc66","A","3");
INSERT INTO hdk_tbstatus VALUES("51","5 - Waiting for supplier","5 - In attendance","#ffcc66","A","3");



DROP TABLE IF EXISTS hdk_tbtemplate_email;

CREATE TABLE `hdk_tbtemplate_email` (
  `idtemplate` int(3) unsigned NOT NULL DEFAULT 0,
  `name` varchar(100) DEFAULT NULL,
  `description` blob DEFAULT NULL,
  PRIMARY KEY (`idtemplate`),
  KEY `IX_TEMPLATE_EMAIL_CD_TEMPL` (`idtemplate`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO hdk_tbtemplate_email VALUES("1","Request assumed by operator # $REQUEST","<p><font face=\"Arial, Helvetica, sans-serif\" size=\"2\">The request number <strong><u>$REQUEST</u></strong> was assumed in $ASSUME<br/>
<strong>Requester</strong>: $REQUESTER<br/>
<font face=\"Arial, Helvetica, sans-serif\" size=\"2\"><strong>Subject:</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; $SUBJECT<br/>
<strong>Entry Date:</strong> $RECORD<br/>
</font><strong>Operator In Charge:</strong> $INCHARGE<br/>
</font></p>
<p><font face=\"Arial, Helvetica, sans-serif\" size=\"2\"><strong>Description:</strong> $DESCRIPTION </font></p>
<p><font face=\"Arial, Helvetica, sans-serif\" size=\"2\">$LINK_USER</font></p>
<p><font face=\"Arial\" size=\"2\"><strong>Notes:</strong><br/>
$NT_USER</font></p>");
INSERT INTO hdk_tbtemplate_email VALUES("2","Request closed # $REQUEST","<p>
	<font face=\"Arial, Helvetica, sans-serif\" size=\"2\">Dear Sr.(Ms.)<strong> </strong>$REQUESTER<strong>, </strong>the request number $REQUEST&nbsp; was closed in $FINISH_DATE<strong>.</strong></font></p>
<p>
	<font face=\"Arial, Helvetica, sans-serif\" size=\"2\"><strong>Requester</strong>: $REQUESTER<br />
	<font face=\"Arial, Helvetica, sans-serif\" size=\"2\"><strong>Subject:</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; $SUBJECT<br />
	<strong>Entry Date:</strong> $RECORD</font><br />
	<strong>Operator In Charge:</strong> $INCHARGE</font></p>
<p>
	<font face=\"Arial, Helvetica, sans-serif\" size=\"2\">$LINK_USER</font></p>
<p>
	<font face=\"Arial, Helvetica, sans-serif\" size=\"2\"><strong>Description:</strong> $DESCRIPTION </font></p>
<p>
	<font face=\"Arial\" size=\"2\"><strong>Notes:</strong><br />
	$NT_USER</font></p>
");
INSERT INTO hdk_tbtemplate_email VALUES("3","Request rejected # $REQUEST","<p><font size=\"2\"><font face=\"Arial\">The request number <strong><u>$REQUEST</u></strong> was rejected in $REJECTION.<br>
</font></font></p><p><font size=\"2\"><font face=\"Arial\"><strong>Requester</strong>: $REQUESTER<br>
<font face=\"Arial, Helvetica, sans-serif\" size=\"2\"><strong>Subject:</strong>");
INSERT INTO hdk_tbtemplate_email VALUES("4","Request evaluated # $REQUEST","<p>
	<font size=\"2\"><font face=\"Arial\">N. <strong><u>$REQUEST</u></strong> .<br />
	<strong>Subject:</strong> $SUBJECT<br />
	<strong>User:</strong> $REQUESTER<br />
	<strong>Entry Date:</strong> $RECORD<br />
	<strong>Description:</strong> $DESCRIPTION<br />
	<strong>In Charge:</strong> $INCHARGE</font></font></p>
<p>
	<strong>Evaluation: </strong>$EVALUATION</p>
<p>
	<font size=\"2\"><strong>Phone:</strong> $PHONE</font><font size=\"2\"><strong> - Branch:</strong> $BRANCH</font></p>
<p>
	<font face=\"Arial, Helvetica, sans-serif\" size=\"2\">$LINK_OPERATOR</font></p>
<p>
	<font size=\"2\"><font face=\"Arial\"><strong>Notes:</strong><br />
	$NT_OPERATOR</font></font></p>
");
INSERT INTO hdk_tbtemplate_email VALUES("7","New request to approve # $REQUEST","<p>
	<font size=\"2\"><font face=\"Arial\">N. <strong><u>$LINK_OPERATOR</u></strong> .<br />
	<strong>Subject:</strong> $SUBJECT<br />
	<strong>User:</strong> $REQUESTER<br />
	<strong>Entry Date:</strong> $RECORD<br />
	<strong>Description:</strong> $DESCRIPTION<br />
	<strong>In Charge:</strong> $INCHARGE</font></font></p>
<p>
	<font size=\"2\"><strong>Phone:</strong> $PHONE</font><font size=\"2\"><strong> - Branch:</strong> $BRANCH</font></p>
");
INSERT INTO hdk_tbtemplate_email VALUES("8","Request reopened # $REQUEST","<p>
	<font face=\"Arial, Helvetica, sans-serif\" size=\"2\">Dear Sr.(Ms.)<strong> </strong>$INCHARGE<strong>, </strong>the request number $REQUEST&nbsp; was reopened in $DATE<strong>.</strong></font></p>
<p>
	<font face=\"Arial, Helvetica, sans-serif\" size=\"2\"><strong>Requester</strong>: $REQUESTER<br />
	<font face=\"Arial, Helvetica, sans-serif\" size=\"2\"><strong>Subject:</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; $SUBJECT<br />
	<strong>Entry Date:</strong> $RECORD</font><br />
	<strong>Operator In Charge:</strong> $INCHARGE</font></p>
<p>
	<font face=\"Arial, Helvetica, sans-serif\" size=\"2\">$LINK_USER</font></p>
<p>
	<font face=\"Arial, Helvetica, sans-serif\" size=\"2\"><strong>Description:</strong> $DESCRIPTION </font></p>
<p>
	<font face=\"Arial\" size=\"2\"><strong>Notes:</strong><br />
	$NT_USER</font></p>
");
INSERT INTO hdk_tbtemplate_email VALUES("13","New note added to the request # $REQUEST","<p><font size=\"2\"><font face=\"Arial\">The request number <strong><u>$REQUEST</u></strong>&nbsp;received another note.<br/>
<strong>Requester</strong>: $REQUESTER<br/>
<font face=\"Arial, Helvetica, sans-serif\" size=\"2\"><strong>Subject:</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; $SUBJECT<br/>
<strong>Entry Date:</strong> $RECORD<br/>
</font></font></font><font size=\"2\"><font face=\"Arial\"><strong>Operator In Charge:</strong> $INCHARGE<br/>
<strong>Description:</strong> $DESCRIPTION&nbsp;</font></font>&nbsp;</p>
<p><font face=\"Arial, Helvetica, sans-serif\" size=\"2\">$LINK_USER<a href=\"$LINK_USER\" target=\"helpdesk\"></a></font></p>
<p><font face=\"Arial\" size=\"2\"><strong>Notes:</strong><br/>
$NT_USER</font></p>");
INSERT INTO hdk_tbtemplate_email VALUES("16","New request recorded # $REQUEST","<p>
	<font size=\"2\"><font face=\"Arial\">N. <strong><u>$REQUEST</u></strong> .<br />
	<strong>Subject:</strong> $SUBJECT<br />
	<strong>User:</strong> $REQUESTER<br />
	<strong>Entry Date:</strong> $RECORD<br />
	<strong>Description:</strong> $DESCRIPTION<br />
	<strong>In Charge:</strong> $INCHARGE</font></font></p>
<p>
	<font size=\"2\"><strong>Phone:</strong> $PHONE</font><font size=\"2\"><strong> - Branch:</strong> $BRANCH</font></p>
<p>
	<font face=\"Arial, Helvetica, sans-serif\" size=\"2\">$LINK_OPERATOR</font></p>
<p>
	<font size=\"2\"><font face=\"Arial\"><strong>Notes:</strong><br />
	$NT_OPERATOR</font></font></p>
");
INSERT INTO hdk_tbtemplate_email VALUES("21","Request repassed # $REQUEST","<p>
	<font size=\"2\"><font face=\"Arial\">N. <strong><u>$LINK_OPERATOR</u></strong>.<br />
	<strong>Subject:</strong> $SUBJECT<br />
	<strong>User:</strong> $REQUESTER<br />
	<strong>Entry Date:</strong> $RECORD<br />
	<strong>Description:</strong> $DESCRIPTION<br />
	<strong>In Charge:</strong> $INCHARGE</font></font></p>
<p>
	<font size=\"2\"><strong>Phone:</strong> $PHONE</font><font size=\"2\"><strong> - Branch:</strong> $BRANCH</font></p>
<p>
	<font size=\"2\"><font face=\"Arial\"><strong>Notes:</strong><br />
	$NT_OPERATOR</font></font></p>
");
INSERT INTO hdk_tbtemplate_email VALUES("43","A new note was recorded by an user # $REQUEST","<p>
	<font size=\"2\"><font face=\"Arial\">The request number: <strong><u>$REQUEST</u></strong>&nbsp;received another note.</font></font><br />
	&nbsp;</p>
<p>
	<font size=\"2\"><font face=\"Arial\"><span style=\"color:#000000;\"><strong>Requester</strong>:</span> $REQUESTER<br />
	<font face=\"Arial, Helvetica, sans-serif\" size=\"2\"><strong>Subject:</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; $SUBJECT<br />
	<strong>Entry Date:</strong> $RECORD</font></font></font><br />
	<font size=\"2\"><font face=\"Arial\"><strong>Operator In Charge:</strong> $INCHARGE<br />
	<strong>Descri&ccedil;&atilde;o:</strong> $DESCRIPTION </font> </font></p>
<p>
	<font size=\"2\"><font face=\"Arial, Helvetica, sans-serif\" size=\"2\">$LINK_OPERATOR</font></font></p>
<p>
	&nbsp;</p>
<p>
	<font face=\"Arial\" size=\"2\"><strong>Notes:</strong><br />
	$NT_USER</font></p>

");



DROP TABLE IF EXISTS hdk_tbtrigger;

CREATE TABLE `hdk_tbtrigger` (
  `idtrigger` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`idtrigger`),
  KEY `FK_hdk_tbtrigger_hdk_tbevaluation_idevaluation` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;




DROP TABLE IF EXISTS hdk_tbtrigger_group;

CREATE TABLE `hdk_tbtrigger_group` (
  `idtriggergroup` int(11) NOT NULL AUTO_INCREMENT,
  `idtrigger` int(11) DEFAULT NULL,
  `idgroup` int(11) DEFAULT NULL,
  `condgroup` char(3) DEFAULT NULL,
  PRIMARY KEY (`idtriggergroup`),
  KEY `FK_hdk_trigger_group_hdk_tbtrigger_idtrigger` (`idtrigger`),
  KEY `FK_hdk_trigger_group_hdk_tbgroup_idgroup` (`idgroup`),
  CONSTRAINT `FK_hdk_trigger_group_hdk_tbgroup_idgroup` FOREIGN KEY (`idgroup`) REFERENCES `hdk_tbgroup` (`idgroup`),
  CONSTRAINT `FK_hdk_trigger_group_hdk_tbtrigger_idtrigger` FOREIGN KEY (`idtrigger`) REFERENCES `hdk_tbtrigger` (`idtrigger`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;




DROP TABLE IF EXISTS hdk_tbtrigger_priority;

CREATE TABLE `hdk_tbtrigger_priority` (
  `idtriggerpriority` int(11) DEFAULT NULL,
  `idtrigger` int(11) DEFAULT NULL,
  `idpriority` int(11) DEFAULT NULL,
  `condpriority` char(3) DEFAULT NULL,
  KEY `FK_hdk_tbtrigger_priority_hdk_tbtrigger_idtrigger` (`idtrigger`),
  KEY `FK_hdk_tbtrigger_priority_hdk_tbpriority_idpriority` (`idpriority`),
  CONSTRAINT `FK_hdk_tbtrigger_priority_hdk_tbpriority_idpriority` FOREIGN KEY (`idpriority`) REFERENCES `hdk_tbpriority` (`idpriority`),
  CONSTRAINT `FK_hdk_tbtrigger_priority_hdk_tbtrigger_idtrigger` FOREIGN KEY (`idtrigger`) REFERENCES `hdk_tbtrigger` (`idtrigger`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;




DROP TABLE IF EXISTS hdk_tbtrigger_status;

CREATE TABLE `hdk_tbtrigger_status` (
  `idtriggerstatus` int(11) DEFAULT NULL,
  `idtrigger` int(11) DEFAULT NULL,
  `idstatus` int(11) DEFAULT NULL,
  `condstatus` char(3) DEFAULT NULL,
  KEY `FK_hdk_trigger_status_hdk_tbtrigger_idtrigger` (`idtrigger`),
  KEY `FK_hdk_trigger_status_hdk_tbstatus_idstatus` (`idstatus`),
  CONSTRAINT `FK_hdk_trigger_status_hdk_tbstatus_idstatus` FOREIGN KEY (`idstatus`) REFERENCES `hdk_tbstatus` (`idstatus`),
  CONSTRAINT `FK_hdk_trigger_status_hdk_tbtrigger_idtrigger` FOREIGN KEY (`idtrigger`) REFERENCES `hdk_tbtrigger` (`idtrigger`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;




DROP TABLE IF EXISTS hdk_tbwork_calendar;

CREATE TABLE `hdk_tbwork_calendar` (
  `num_day_week` int(1) unsigned NOT NULL,
  `business_day` int(1) unsigned DEFAULT 0,
  `begin_morning` varchar(4) DEFAULT '0800',
  `end_morning` varchar(4) DEFAULT '1200',
  `begin_afternoon` varchar(4) DEFAULT '1300',
  `end_afternoon` varchar(4) DEFAULT '1800',
  PRIMARY KEY (`num_day_week`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO hdk_tbwork_calendar VALUES("0","0","0800","1200","1300","1800");
INSERT INTO hdk_tbwork_calendar VALUES("1","1","0800","1200","1300","1800");
INSERT INTO hdk_tbwork_calendar VALUES("2","1","0800","1200","1300","1800");
INSERT INTO hdk_tbwork_calendar VALUES("3","1","0800","1200","1300","1800");
INSERT INTO hdk_tbwork_calendar VALUES("4","1","0800","1200","1300","1800");
INSERT INTO hdk_tbwork_calendar VALUES("5","1","0800","1200","1300","1700");
INSERT INTO hdk_tbwork_calendar VALUES("6","0","0800","1200","1300","1800");



DROP TABLE IF EXISTS hdk_tbwork_calendar_new;

CREATE TABLE `hdk_tbwork_calendar_new` (
  `num_day_week` int(1) unsigned NOT NULL,
  `business_day` int(1) unsigned DEFAULT 0,
  `begin_morning` time NOT NULL,
  `end_morning` time NOT NULL,
  `begin_afternoon` time NOT NULL,
  `end_afternoon` time NOT NULL,
  PRIMARY KEY (`num_day_week`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO hdk_tbwork_calendar_new VALUES("0","0","07:30:00","12:00:00","13:00:00","17:30:00");
INSERT INTO hdk_tbwork_calendar_new VALUES("1","1","07:30:00","12:00:00","13:00:00","17:30:00");
INSERT INTO hdk_tbwork_calendar_new VALUES("2","1","07:30:00","12:00:00","13:00:00","17:30:00");
INSERT INTO hdk_tbwork_calendar_new VALUES("3","1","07:30:00","12:00:00","13:00:00","17:30:00");
INSERT INTO hdk_tbwork_calendar_new VALUES("4","1","07:30:00","12:00:00","13:00:00","17:30:00");
INSERT INTO hdk_tbwork_calendar_new VALUES("5","1","07:30:00","12:00:00","13:00:00","16:30:00");
INSERT INTO hdk_tbwork_calendar_new VALUES("6","0","07:30:00","12:00:00","13:00:00","17:30:00");



DROP TABLE IF EXISTS tbaccesstype;

CREATE TABLE `tbaccesstype` (
  `idaccesstype` int(4) NOT NULL AUTO_INCREMENT,
  `type` varchar(20) NOT NULL,
  PRIMARY KEY (`idaccesstype`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

INSERT INTO tbaccesstype VALUES("1","access");
INSERT INTO tbaccesstype VALUES("2","new");
INSERT INTO tbaccesstype VALUES("3","edit");
INSERT INTO tbaccesstype VALUES("4","delete");
INSERT INTO tbaccesstype VALUES("5","export");
INSERT INTO tbaccesstype VALUES("6","email");
INSERT INTO tbaccesstype VALUES("7","sms");



DROP TABLE IF EXISTS tbaddress;

CREATE TABLE `tbaddress` (
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
) ENGINE=InnoDB AUTO_INCREMENT=960 DEFAULT CHARSET=latin1;

INSERT INTO tbaddress VALUES("937","1038","1","1","1","3","","","");
INSERT INTO tbaddress VALUES("945","1061","1","1","1","2","","","");
INSERT INTO tbaddress VALUES("959","1078","1","1","1","2","","","");



DROP TABLE IF EXISTS tbcity;

CREATE TABLE `tbcity` (
  `idcity` int(11) NOT NULL AUTO_INCREMENT,
  `idstate` int(11) NOT NULL,
  `name` varchar(60) DEFAULT NULL,
  PRIMARY KEY (`idcity`),
  UNIQUE KEY `idstate` (`idstate`,`name`),
  KEY `idx_tbcity_name` (`name`),
  KEY `fk_tbcity_tbstate1` (`idstate`),
  CONSTRAINT `fk_tbcity_tbstate1` FOREIGN KEY (`idstate`) REFERENCES `tbstate` (`idstate`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=21556 DEFAULT CHARSET=latin1;

INSERT INTO tbcity VALUES("1","1","Choose");
INSERT INTO tbcity VALUES("21555","1098","Alabaster");



DROP TABLE IF EXISTS tbcompany;

CREATE TABLE `tbcompany` (
  `idcompany` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`idcompany`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

INSERT INTO tbcompany VALUES("1","Demo Company");



DROP TABLE IF EXISTS tbconfig;

CREATE TABLE `tbconfig` (
  `idconfig` int(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) DEFAULT NULL,
  `description` blob DEFAULT NULL,
  `idconfigcategory` int(10) unsigned DEFAULT NULL,
  `session_name` varchar(50) DEFAULT NULL,
  `field_type` varchar(200) DEFAULT NULL,
  `status` char(1) DEFAULT 'A',
  `smarty` varchar(120) NOT NULL,
  `value` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`idconfig`),
  UNIQUE KEY `hdk_tbconfig_session_idx` (`session_name`),
  KEY `idconfigcategory` (`idconfigcategory`),
  CONSTRAINT `tbconfig_ibfk_1` FOREIGN KEY (`idconfigcategory`) REFERENCES `tbconfig_category` (`idconfigcategory`)
) ENGINE=InnoDB AUTO_INCREMENT=115 DEFAULT CHARSET=latin1 AVG_ROW_LENGTH=321 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

INSERT INTO tbconfig VALUES("5","Email Title","","5","EM_TITLE","","A","em_title","[DEMO SERVICEDESK]");
INSERT INTO tbconfig VALUES("65","Email Host","","5","EM_HOSTNAME","","A","em_hostname","smtp.gmail.com");
INSERT INTO tbconfig VALUES("66","Domain","","5","EM_DOMAIN","","A","em_domain","foo.in");
INSERT INTO tbconfig VALUES("67","Email user","","5","EM_USER","","A","em_user","noreply@foo.in");
INSERT INTO tbconfig VALUES("68","User password","","5","EM_PASSWORD","","A","em_password","PassworDemo");
INSERT INTO tbconfig VALUES("69","Sender email","","5","EM_SENDER","","A","em_sender","noreply@foo.in");
INSERT INTO tbconfig VALUES("70","Requires authentication","","5","EM_AUTH","","A","em_auth","1");
INSERT INTO tbconfig VALUES("71","Email Header","<p><br></p>","11","EM_HEADER","","A","em_header","");
INSERT INTO tbconfig VALUES("72","Email Footer","<p><br></p>","11","EM_FOOTER","","A","em_footer","");
INSERT INTO tbconfig VALUES("73","POP Host","","12","POP_HOST","","A","pop_host","imap.google.com");
INSERT INTO tbconfig VALUES("74","POP Port","","12","POP_PORT","","A","pop_port","993");
INSERT INTO tbconfig VALUES("75","POP Type","","12","POP_TYPE","","A","pop_type","GMAIL");
INSERT INTO tbconfig VALUES("76","Success Log","","5","EM_SUCCESS_LOG","","A","em_success_log","1");
INSERT INTO tbconfig VALUES("77","Failure Log","","5","EM_FAILURE_LOG","","A","em_failure_log","1");
INSERT INTO tbconfig VALUES("80","Enable maintenance mode","","5","SES_MAINTENANCE","checkbox","A","ses_maintenance","0");
INSERT INTO tbconfig VALUES("81","Maintenance message","","5","SES_MAINTENANCE_MSG","","A","ses_maintenance_msg","
	We are undergoing maintenance, we will be back soon !
");
INSERT INTO tbconfig VALUES("85","Define session time","","1","SES_TIME_SESSION","input","A","sys_time_session","3720");
INSERT INTO tbconfig VALUES("86","LDAP/AD Server","","13","SES_LDAP_SERVER","input","A","ldap_server","ldap.testathon.net");
INSERT INTO tbconfig VALUES("87","LDAP Distinguished Names","","13","SES_LDAP_DN","input","A","ldap_dn","OU=users,DC=testathon,DC=net");
INSERT INTO tbconfig VALUES("90","LDAP Domain","","13","SES_LDAP_DOMAIN","","A","ldap_domain","");
INSERT INTO tbconfig VALUES("91","LDAP Field ID","","13","SES_LDAP_FIELD","","A","ldap_field","uid");
INSERT INTO tbconfig VALUES("92","LDAP or AD","","13","SES_LDAP_AD","","A","Type","1");
INSERT INTO tbconfig VALUES("98","Google two factor authentication","","1","SES_GOOGLE_2FA","checkbox","A","sys_2FAuthentication","0");
INSERT INTO tbconfig VALUES("99","POP Domain","","12","POP_DOMAIN","","A","pop_domain","foo.in");
INSERT INTO tbconfig VALUES("102","Email By Cron","","5","EM_BY_CRON","checkbox","A","em_email_by_cron","0");
INSERT INTO tbconfig VALUES("103","Email PORT","","5","EM_PORT","NULL","A","em_port","587");
INSERT INTO tbconfig VALUES("104","General Log","","5","LOG_GENERAL","","A","log_general","1");
INSERT INTO tbconfig VALUES("105","Email Log","","5","LOG_EMAIL","","A","log_email","1");
INSERT INTO tbconfig VALUES("106","Host Log","","5","LOG_HOST","","A","log_host","local");
INSERT INTO tbconfig VALUES("107","Remote Server Log","","5","LOG_REMOTE_SERVER","input","A","log_remote_server","10.42.44.203");
INSERT INTO tbconfig VALUES("108","Log Level","","5","LOG_LEVEL","input","A","log_level","7");
INSERT INTO tbconfig VALUES("112","Tracker Status","","5","TRACKER_STATUS","checkbox","A","tracker_status","1");
INSERT INTO tbconfig VALUES("113","Country default","","5","COUNTRY_DEFAULT","input","A","country_default","227");
INSERT INTO tbconfig VALUES("114","Email Secure","Use TLS","5","EM_TLS","checkbox","A","email_tls","1");



DROP TABLE IF EXISTS tbconfig_category;

CREATE TABLE `tbconfig_category` (
  `idconfigcategory` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) DEFAULT NULL,
  `smarty` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`idconfigcategory`),
  KEY `idx_category` (`idconfigcategory`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1 AVG_ROW_LENGTH=1260;

INSERT INTO tbconfig_category VALUES("1","Funcionalidades do Sistema","System_features");
INSERT INTO tbconfig_category VALUES("2","Permissï¿½es para Analista","");
INSERT INTO tbconfig_category VALUES("3","Notificaçôes de E-mail","");
INSERT INTO tbconfig_category VALUES("4","Usuários","");
INSERT INTO tbconfig_category VALUES("5","Geral","");
INSERT INTO tbconfig_category VALUES("6","Patrimônioio","");
INSERT INTO tbconfig_category VALUES("7","Integração com SMTP/LDAP/MS Active Directory","");
INSERT INTO tbconfig_category VALUES("8","Inventário","");
INSERT INTO tbconfig_category VALUES("9","Inventario","");
INSERT INTO tbconfig_category VALUES("10","Outros itens","Other_items");
INSERT INTO tbconfig_category VALUES("11","Email Templates","");
INSERT INTO tbconfig_category VALUES("12","POP Server","");
INSERT INTO tbconfig_category VALUES("13","Integration with LDAP/MS Active Directory","Integration_ldap");



DROP TABLE IF EXISTS tbcountry;

CREATE TABLE `tbcountry` (
  `idcountry` int(11) NOT NULL AUTO_INCREMENT,
  `iso` varchar(2) DEFAULT NULL,
  `name` varchar(80) DEFAULT NULL,
  `iso3` varchar(3) DEFAULT NULL,
  `printablename` varchar(80) DEFAULT NULL,
  PRIMARY KEY (`idcountry`),
  UNIQUE KEY `code_UNIQUE` (`iso`)
) ENGINE=InnoDB AUTO_INCREMENT=241 DEFAULT CHARSET=latin1;

INSERT INTO tbcountry VALUES("1","AA","Choose","AAA","Choose");
INSERT INTO tbcountry VALUES("2","AF","AFGHANISTAN","AFG","Afghanistan");
INSERT INTO tbcountry VALUES("3","AL","ALBANIA","ALB","Albania");
INSERT INTO tbcountry VALUES("4","DZ","ALGERIA","DZA","Algeria");
INSERT INTO tbcountry VALUES("5","AS","AMERICAN SAMOA","ASM","American Samoa");
INSERT INTO tbcountry VALUES("6","AD","ANDORRA","AND","Andorra");
INSERT INTO tbcountry VALUES("7","AO","ANGOLA","AGO","Angola");
INSERT INTO tbcountry VALUES("8","AI","ANGUILLA","AIA","Anguilla");
INSERT INTO tbcountry VALUES("9","AQ","ANTARCTICA","","Antarctica");
INSERT INTO tbcountry VALUES("10","AG","ANTIGUA AND BARBUDA","ATG","Antigua and Barbuda");
INSERT INTO tbcountry VALUES("11","AR","ARGENTINA","ARG","Argentina");
INSERT INTO tbcountry VALUES("12","AM","ARMENIA","ARM","Armenia");
INSERT INTO tbcountry VALUES("13","AW","ARUBA","ABW","Aruba");
INSERT INTO tbcountry VALUES("14","AU","AUSTRALIA","AUS","Australia");
INSERT INTO tbcountry VALUES("15","AT","AUSTRIA","AUT","Austria");
INSERT INTO tbcountry VALUES("16","AZ","AZERBAIJAN","AZE","Azerbaijan");
INSERT INTO tbcountry VALUES("17","BS","BAHAMAS","BHS","Bahamas");
INSERT INTO tbcountry VALUES("18","BH","BAHRAIN","BHR","Bahrain");
INSERT INTO tbcountry VALUES("19","BD","BANGLADESH","BGD","Bangladesh");
INSERT INTO tbcountry VALUES("20","BB","BARBADOS","BRB","Barbados");
INSERT INTO tbcountry VALUES("21","BY","BELARUS","BLR","Belarus");
INSERT INTO tbcountry VALUES("22","BE","BELGIUM","BEL","Belgium");
INSERT INTO tbcountry VALUES("23","BZ","BELIZE","BLZ","Belize");
INSERT INTO tbcountry VALUES("24","BJ","BENIN","BEN","Benin");
INSERT INTO tbcountry VALUES("25","BM","BERMUDA","BMU","Bermuda");
INSERT INTO tbcountry VALUES("26","BT","BHUTAN","BTN","Bhutan");
INSERT INTO tbcountry VALUES("27","BO","BOLIVIA","BOL","Bolivia");
INSERT INTO tbcountry VALUES("28","BA","BOSNIA AND HERZEGOVINA","BIH","Bosnia and Herzegovina");
INSERT INTO tbcountry VALUES("29","BW","BOTSWANA","BWA","Botswana");
INSERT INTO tbcountry VALUES("30","BV","BOUVET ISLAND","","Bouvet Island");
INSERT INTO tbcountry VALUES("31","BR","BRAZIL","BRA","Brazil");
INSERT INTO tbcountry VALUES("32","IO","BRITISH INDIAN OCEAN TERRITORY","","British Indian Ocean Territory");
INSERT INTO tbcountry VALUES("33","BN","BRUNEI DARUSSALAM","BRN","Brunei Darussalam");
INSERT INTO tbcountry VALUES("34","BG","BULGARIA","BGR","Bulgaria");
INSERT INTO tbcountry VALUES("35","BF","BURKINA FASO","BFA","Burkina Faso");
INSERT INTO tbcountry VALUES("36","BI","BURUNDI","BDI","Burundi");
INSERT INTO tbcountry VALUES("37","KH","CAMBODIA","KHM","Cambodia");
INSERT INTO tbcountry VALUES("38","CM","CAMEROON","CMR","Cameroon");
INSERT INTO tbcountry VALUES("39","CA","CANADA","CAN","Canada");
INSERT INTO tbcountry VALUES("40","CV","CAPE VERDE","CPV","Cape Verde");
INSERT INTO tbcountry VALUES("41","KY","CAYMAN ISLANDS","CYM","Cayman Islands");
INSERT INTO tbcountry VALUES("42","CF","CENTRAL AFRICAN REPUBLIC","CAF","Central African Republic");
INSERT INTO tbcountry VALUES("43","TD","CHAD","TCD","Chad");
INSERT INTO tbcountry VALUES("44","CL","CHILE","CHL","Chile");
INSERT INTO tbcountry VALUES("45","CN","CHINA","CHN","China");
INSERT INTO tbcountry VALUES("46","CX","CHRISTMAS ISLAND","","Christmas Island");
INSERT INTO tbcountry VALUES("47","CC","COCOS (KEELING) ISLANDS","","Cocos (Keeling) Islands");
INSERT INTO tbcountry VALUES("48","CO","COLOMBIA","COL","Colombia");
INSERT INTO tbcountry VALUES("49","KM","COMOROS","COM","Comoros");
INSERT INTO tbcountry VALUES("50","CG","CONGO","COG","Congo");
INSERT INTO tbcountry VALUES("51","CD","CONGO, THE DEMOCRATIC REPUBLIC OF THE","COD","Congo, the Democratic Republic of the");
INSERT INTO tbcountry VALUES("52","CK","COOK ISLANDS","COK","Cook Islands");
INSERT INTO tbcountry VALUES("53","CR","COSTA RICA","CRI","Costa Rica");
INSERT INTO tbcountry VALUES("54","CI","COTE D\'IVOIRE","CIV","Cote D\'Ivoire");
INSERT INTO tbcountry VALUES("55","HR","CROATIA","HRV","Croatia");
INSERT INTO tbcountry VALUES("56","CU","CUBA","CUB","Cuba");
INSERT INTO tbcountry VALUES("57","CY","CYPRUS","CYP","Cyprus");
INSERT INTO tbcountry VALUES("58","CZ","CZECH REPUBLIC","CZE","Czech Republic");
INSERT INTO tbcountry VALUES("59","DK","DENMARK","DNK","Denmark");
INSERT INTO tbcountry VALUES("60","DJ","DJIBOUTI","DJI","Djibouti");
INSERT INTO tbcountry VALUES("61","DM","DOMINICA","DMA","Dominica");
INSERT INTO tbcountry VALUES("62","DO","DOMINICAN REPUBLIC","DOM","Dominican Republic");
INSERT INTO tbcountry VALUES("63","EC","ECUADOR","ECU","Ecuador");
INSERT INTO tbcountry VALUES("64","EG","EGYPT","EGY","Egypt");
INSERT INTO tbcountry VALUES("65","SV","EL SALVADOR","SLV","El Salvador");
INSERT INTO tbcountry VALUES("66","GQ","EQUATORIAL GUINEA","GNQ","Equatorial Guinea");
INSERT INTO tbcountry VALUES("67","ER","ERITREA","ERI","Eritrea");
INSERT INTO tbcountry VALUES("68","EE","ESTONIA","EST","Estonia");
INSERT INTO tbcountry VALUES("69","ET","ETHIOPIA","ETH","Ethiopia");
INSERT INTO tbcountry VALUES("70","FK","FALKLAND ISLANDS (MALVINAS)","FLK","Falkland Islands (Malvinas)");
INSERT INTO tbcountry VALUES("71","FO","FAROE ISLANDS","FRO","Faroe Islands");
INSERT INTO tbcountry VALUES("72","FJ","FIJI","FJI","Fiji");
INSERT INTO tbcountry VALUES("73","FI","FINLAND","FIN","Finland");
INSERT INTO tbcountry VALUES("74","FR","FRANCE","FRA","France");
INSERT INTO tbcountry VALUES("75","GF","FRENCH GUIANA","GUF","French Guiana");
INSERT INTO tbcountry VALUES("76","PF","FRENCH POLYNESIA","PYF","French Polynesia");
INSERT INTO tbcountry VALUES("77","TF","FRENCH SOUTHERN TERRITORIES","","French Southern Territories");
INSERT INTO tbcountry VALUES("78","GA","GABON","GAB","Gabon");
INSERT INTO tbcountry VALUES("79","GM","GAMBIA","GMB","Gambia");
INSERT INTO tbcountry VALUES("80","GE","GEORGIA","GEO","Georgia");
INSERT INTO tbcountry VALUES("81","DE","GERMANY","DEU","Germany");
INSERT INTO tbcountry VALUES("82","GH","GHANA","GHA","Ghana");
INSERT INTO tbcountry VALUES("83","GI","GIBRALTAR","GIB","Gibraltar");
INSERT INTO tbcountry VALUES("84","GR","GREECE","GRC","Greece");
INSERT INTO tbcountry VALUES("85","GL","GREENLAND","GRL","Greenland");
INSERT INTO tbcountry VALUES("86","GD","GRENADA","GRD","Grenada");
INSERT INTO tbcountry VALUES("87","GP","GUADELOUPE","GLP","Guadeloupe");
INSERT INTO tbcountry VALUES("88","GU","GUAM","GUM","Guam");
INSERT INTO tbcountry VALUES("89","GT","GUATEMALA","GTM","Guatemala");
INSERT INTO tbcountry VALUES("90","GN","GUINEA","GIN","Guinea");
INSERT INTO tbcountry VALUES("91","GW","GUINEA-BISSAU","GNB","Guinea-Bissau");
INSERT INTO tbcountry VALUES("92","GY","GUYANA","GUY","Guyana");
INSERT INTO tbcountry VALUES("93","HT","HAITI","HTI","Haiti");
INSERT INTO tbcountry VALUES("94","HM","HEARD ISLAND AND MCDONALD ISLANDS","","Heard Island and Mcdonald Islands");
INSERT INTO tbcountry VALUES("95","VA","HOLY SEE (VATICAN CITY STATE)","VAT","Holy See (Vatican City State)");
INSERT INTO tbcountry VALUES("96","HN","HONDURAS","HND","Honduras");
INSERT INTO tbcountry VALUES("97","HK","HONG KONG","HKG","Hong Kong");
INSERT INTO tbcountry VALUES("98","HU","HUNGARY","HUN","Hungary");
INSERT INTO tbcountry VALUES("99","IS","ICELAND","ISL","Iceland");
INSERT INTO tbcountry VALUES("100","IN","INDIA","IND","India");
INSERT INTO tbcountry VALUES("101","ID","INDONESIA","IDN","Indonesia");
INSERT INTO tbcountry VALUES("102","IR","IRAN, ISLAMIC REPUBLIC OF","IRN","Iran, Islamic Republic of");
INSERT INTO tbcountry VALUES("103","IQ","IRAQ","IRQ","Iraq");
INSERT INTO tbcountry VALUES("104","IE","IRELAND","IRL","Ireland");
INSERT INTO tbcountry VALUES("105","IL","ISRAEL","ISR","Israel");
INSERT INTO tbcountry VALUES("106","IT","ITALY","ITA","Italy");
INSERT INTO tbcountry VALUES("107","JM","JAMAICA","JAM","Jamaica");
INSERT INTO tbcountry VALUES("108","JP","JAPAN","JPN","Japan");
INSERT INTO tbcountry VALUES("109","JO","JORDAN","JOR","Jordan");
INSERT INTO tbcountry VALUES("110","KZ","KAZAKHSTAN","KAZ","Kazakhstan");
INSERT INTO tbcountry VALUES("111","KE","KENYA","KEN","Kenya");
INSERT INTO tbcountry VALUES("112","KI","KIRIBATI","KIR","Kiribati");
INSERT INTO tbcountry VALUES("113","KP","KOREA, DEMOCRATIC PEOPLE\'S REPUBLIC OF","PRK","Korea, Democratic People\'s Republic of");
INSERT INTO tbcountry VALUES("114","KR","KOREA, REPUBLIC OF","KOR","Korea, Republic of");
INSERT INTO tbcountry VALUES("115","KW","KUWAIT","KWT","Kuwait");
INSERT INTO tbcountry VALUES("116","KG","KYRGYZSTAN","KGZ","Kyrgyzstan");
INSERT INTO tbcountry VALUES("117","LA","LAO PEOPLE\'S DEMOCRATIC REPUBLIC","LAO","Lao People\'s Democratic Republic");
INSERT INTO tbcountry VALUES("118","LV","LATVIA","LVA","Latvia");
INSERT INTO tbcountry VALUES("119","LB","LEBANON","LBN","Lebanon");
INSERT INTO tbcountry VALUES("120","LS","LESOTHO","LSO","Lesotho");
INSERT INTO tbcountry VALUES("121","LR","LIBERIA","LBR","Liberia");
INSERT INTO tbcountry VALUES("122","LY","LIBYAN ARAB JAMAHIRIYA","LBY","Libyan Arab Jamahiriya");
INSERT INTO tbcountry VALUES("123","LI","LIECHTENSTEIN","LIE","Liechtenstein");
INSERT INTO tbcountry VALUES("124","LT","LITHUANIA","LTU","Lithuania");
INSERT INTO tbcountry VALUES("125","LU","LUXEMBOURG","LUX","Luxembourg");
INSERT INTO tbcountry VALUES("126","MO","MACAO","MAC","Macao");
INSERT INTO tbcountry VALUES("127","MK","MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF","MKD","Macedonia, the Former Yugoslav Republic of");
INSERT INTO tbcountry VALUES("128","MG","MADAGASCAR","MDG","Madagascar");
INSERT INTO tbcountry VALUES("129","MW","MALAWI","MWI","Malawi");
INSERT INTO tbcountry VALUES("130","MY","MALAYSIA","MYS","Malaysia");
INSERT INTO tbcountry VALUES("131","MV","MALDIVES","MDV","Maldives");
INSERT INTO tbcountry VALUES("132","ML","MALI","MLI","Mali");
INSERT INTO tbcountry VALUES("133","MT","MALTA","MLT","Malta");
INSERT INTO tbcountry VALUES("134","MH","MARSHALL ISLANDS","MHL","Marshall Islands");
INSERT INTO tbcountry VALUES("135","MQ","MARTINIQUE","MTQ","Martinique");
INSERT INTO tbcountry VALUES("136","MR","MAURITANIA","MRT","Mauritania");
INSERT INTO tbcountry VALUES("137","MU","MAURITIUS","MUS","Mauritius");
INSERT INTO tbcountry VALUES("138","YT","MAYOTTE","","Mayotte");
INSERT INTO tbcountry VALUES("139","MX","MEXICO","MEX","Mexico");
INSERT INTO tbcountry VALUES("140","FM","MICRONESIA, FEDERATED STATES OF","FSM","Micronesia, Federated States of");
INSERT INTO tbcountry VALUES("141","MD","MOLDOVA, REPUBLIC OF","MDA","Moldova, Republic of");
INSERT INTO tbcountry VALUES("142","MC","MONACO","MCO","Monaco");
INSERT INTO tbcountry VALUES("143","MN","MONGOLIA","MNG","Mongolia");
INSERT INTO tbcountry VALUES("144","MS","MONTSERRAT","MSR","Montserrat");
INSERT INTO tbcountry VALUES("145","MA","MOROCCO","MAR","Morocco");
INSERT INTO tbcountry VALUES("146","MZ","MOZAMBIQUE","MOZ","Mozambique");
INSERT INTO tbcountry VALUES("147","MM","MYANMAR","MMR","Myanmar");
INSERT INTO tbcountry VALUES("148","NA","NAMIBIA","NAM","Namibia");
INSERT INTO tbcountry VALUES("149","NR","NAURU","NRU","Nauru");
INSERT INTO tbcountry VALUES("150","NP","NEPAL","NPL","Nepal");
INSERT INTO tbcountry VALUES("151","NL","NETHERLANDS","NLD","Netherlands");
INSERT INTO tbcountry VALUES("152","AN","NETHERLANDS ANTILLES","ANT","Netherlands Antilles");
INSERT INTO tbcountry VALUES("153","NC","NEW CALEDONIA","NCL","New Caledonia");
INSERT INTO tbcountry VALUES("154","NZ","NEW ZEALAND","NZL","New Zealand");
INSERT INTO tbcountry VALUES("155","NI","NICARAGUA","NIC","Nicaragua");
INSERT INTO tbcountry VALUES("156","NE","NIGER","NER","Niger");
INSERT INTO tbcountry VALUES("157","NG","NIGERIA","NGA","Nigeria");
INSERT INTO tbcountry VALUES("158","NU","NIUE","NIU","Niue");
INSERT INTO tbcountry VALUES("159","NF","NORFOLK ISLAND","NFK","Norfolk Island");
INSERT INTO tbcountry VALUES("160","MP","NORTHERN MARIANA ISLANDS","MNP","Northern Mariana Islands");
INSERT INTO tbcountry VALUES("161","NO","NORWAY","NOR","Norway");
INSERT INTO tbcountry VALUES("162","OM","OMAN","OMN","Oman");
INSERT INTO tbcountry VALUES("163","PK","PAKISTAN","PAK","Pakistan");
INSERT INTO tbcountry VALUES("164","PW","PALAU","PLW","Palau");
INSERT INTO tbcountry VALUES("165","PS","PALESTINIAN TERRITORY, OCCUPIED","","Palestinian Territory, Occupied");
INSERT INTO tbcountry VALUES("166","PA","PANAMA","PAN","Panama");
INSERT INTO tbcountry VALUES("167","PG","PAPUA NEW GUINEA","PNG","Papua New Guinea");
INSERT INTO tbcountry VALUES("168","PY","PARAGUAY","PRY","Paraguay");
INSERT INTO tbcountry VALUES("169","PE","PERU","PER","Peru");
INSERT INTO tbcountry VALUES("170","PH","PHILIPPINES","PHL","Philippines");
INSERT INTO tbcountry VALUES("171","PN","PITCAIRN","PCN","Pitcairn");
INSERT INTO tbcountry VALUES("172","PL","POLAND","POL","Poland");
INSERT INTO tbcountry VALUES("173","PT","PORTUGAL","PRT","Portugal");
INSERT INTO tbcountry VALUES("174","PR","PUERTO RICO","PRI","Puerto Rico");
INSERT INTO tbcountry VALUES("175","QA","QATAR","QAT","Qatar");
INSERT INTO tbcountry VALUES("176","RE","REUNION","REU","Reunion");
INSERT INTO tbcountry VALUES("177","RO","ROMANIA","ROM","Romania");
INSERT INTO tbcountry VALUES("178","RU","RUSSIAN FEDERATION","RUS","Russian Federation");
INSERT INTO tbcountry VALUES("179","RW","RWANDA","RWA","Rwanda");
INSERT INTO tbcountry VALUES("180","SH","SAINT HELENA","SHN","Saint Helena");
INSERT INTO tbcountry VALUES("181","KN","SAINT KITTS AND NEVIS","KNA","Saint Kitts and Nevis");
INSERT INTO tbcountry VALUES("182","LC","SAINT LUCIA","LCA","Saint Lucia");
INSERT INTO tbcountry VALUES("183","PM","SAINT PIERRE AND MIQUELON","SPM","Saint Pierre and Miquelon");
INSERT INTO tbcountry VALUES("184","VC","SAINT VINCENT AND THE GRENADINES","VCT","Saint Vincent and the Grenadines");
INSERT INTO tbcountry VALUES("185","WS","SAMOA","WSM","Samoa");
INSERT INTO tbcountry VALUES("186","SM","SAN MARINO","SMR","San Marino");
INSERT INTO tbcountry VALUES("187","ST","SAO TOME AND PRINCIPE","STP","Sao Tome and Principe");
INSERT INTO tbcountry VALUES("188","SA","SAUDI ARABIA","SAU","Saudi Arabia");
INSERT INTO tbcountry VALUES("189","SN","SENEGAL","SEN","Senegal");
INSERT INTO tbcountry VALUES("190","CS","SERBIA AND MONTENEGRO","","Serbia and Montenegro");
INSERT INTO tbcountry VALUES("191","SC","SEYCHELLES","SYC","Seychelles");
INSERT INTO tbcountry VALUES("192","SL","SIERRA LEONE","SLE","Sierra Leone");
INSERT INTO tbcountry VALUES("193","SG","SINGAPORE","SGP","Singapore");
INSERT INTO tbcountry VALUES("194","SK","SLOVAKIA","SVK","Slovakia");
INSERT INTO tbcountry VALUES("195","SI","SLOVENIA","SVN","Slovenia");
INSERT INTO tbcountry VALUES("196","SB","SOLOMON ISLANDS","SLB","Solomon Islands");
INSERT INTO tbcountry VALUES("197","SO","SOMALIA","SOM","Somalia");
INSERT INTO tbcountry VALUES("198","ZA","SOUTH AFRICA","ZAF","South Africa");
INSERT INTO tbcountry VALUES("199","GS","SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS","","South Georgia and the South Sandwich Islands");
INSERT INTO tbcountry VALUES("200","ES","SPAIN","ESP","Spain");
INSERT INTO tbcountry VALUES("201","LK","SRI LANKA","LKA","Sri Lanka");
INSERT INTO tbcountry VALUES("202","SD","SUDAN","SDN","Sudan");
INSERT INTO tbcountry VALUES("203","SR","SURINAME","SUR","Suriname");
INSERT INTO tbcountry VALUES("204","SJ","SVALBARD AND JAN MAYEN","SJM","Svalbard and Jan Mayen");
INSERT INTO tbcountry VALUES("205","SZ","SWAZILAND","SWZ","Swaziland");
INSERT INTO tbcountry VALUES("206","SE","SWEDEN","SWE","Sweden");
INSERT INTO tbcountry VALUES("207","CH","SWITZERLAND","CHE","Switzerland");
INSERT INTO tbcountry VALUES("208","SY","SYRIAN ARAB REPUBLIC","SYR","Syrian Arab Republic");
INSERT INTO tbcountry VALUES("209","TW","TAIWAN, PROVINCE OF CHINA","TWN","Taiwan, Province of China");
INSERT INTO tbcountry VALUES("210","TJ","TAJIKISTAN","TJK","Tajikistan");
INSERT INTO tbcountry VALUES("211","TZ","TANZANIA, UNITED REPUBLIC OF","TZA","Tanzania, United Republic of");
INSERT INTO tbcountry VALUES("212","TH","THAILAND","THA","Thailand");
INSERT INTO tbcountry VALUES("213","TL","TIMOR-LESTE","","Timor-Leste");
INSERT INTO tbcountry VALUES("214","TG","TOGO","TGO","Togo");
INSERT INTO tbcountry VALUES("215","TK","TOKELAU","TKL","Tokelau");
INSERT INTO tbcountry VALUES("216","TO","TONGA","TON","Tonga");
INSERT INTO tbcountry VALUES("217","TT","TRINIDAD AND TOBAGO","TTO","Trinidad and Tobago");
INSERT INTO tbcountry VALUES("218","TN","TUNISIA","TUN","Tunisia");
INSERT INTO tbcountry VALUES("219","TR","TURKEY","TUR","Turkey");
INSERT INTO tbcountry VALUES("220","TM","TURKMENISTAN","TKM","Turkmenistan");
INSERT INTO tbcountry VALUES("221","TC","TURKS AND CAICOS ISLANDS","TCA","Turks and Caicos Islands");
INSERT INTO tbcountry VALUES("222","TV","TUVALU","TUV","Tuvalu");
INSERT INTO tbcountry VALUES("223","UG","UGANDA","UGA","Uganda");
INSERT INTO tbcountry VALUES("224","UA","UKRAINE","UKR","Ukraine");
INSERT INTO tbcountry VALUES("225","AE","UNITED ARAB EMIRATES","ARE","United Arab Emirates");
INSERT INTO tbcountry VALUES("226","GB","UNITED KINGDOM","GBR","United Kingdom");
INSERT INTO tbcountry VALUES("227","US","UNITED STATES","USA","United States");
INSERT INTO tbcountry VALUES("228","UM","UNITED STATES MINOR OUTLYING ISLANDS","","United States Minor Outlying Islands");
INSERT INTO tbcountry VALUES("229","UY","URUGUAY","URY","Uruguay");
INSERT INTO tbcountry VALUES("230","UZ","UZBEKISTAN","UZB","Uzbekistan");
INSERT INTO tbcountry VALUES("231","VU","VANUATU","VUT","Vanuatu");
INSERT INTO tbcountry VALUES("232","VE","VENEZUELA","VEN","Venezuela");
INSERT INTO tbcountry VALUES("233","VN","VIET NAM","VNM","Viet Nam");
INSERT INTO tbcountry VALUES("234","VG","VIRGIN ISLANDS, BRITISH","VGB","Virgin Islands, British");
INSERT INTO tbcountry VALUES("235","VI","VIRGIN ISLANDS, U.S.","VIR","Virgin Islands, U.s.");
INSERT INTO tbcountry VALUES("236","WF","WALLIS AND FUTUNA","WLF","Wallis and Futuna");
INSERT INTO tbcountry VALUES("237","EH","WESTERN SAHARA","ESH","Western Sahara");
INSERT INTO tbcountry VALUES("238","YE","YEMEN","YEM","Yemen");
INSERT INTO tbcountry VALUES("239","ZM","ZAMBIA","ZMB","Zambia");
INSERT INTO tbcountry VALUES("240","ZW","ZIMBABWE","ZWE","Zimbabwe");



DROP TABLE IF EXISTS tbdefaultpermission;

CREATE TABLE `tbdefaultpermission` (
  `iddefaultpermission` int(4) NOT NULL AUTO_INCREMENT,
  `idaccesstype` int(4) NOT NULL,
  `idprogram` int(4) NOT NULL,
  `allow` char(4) NOT NULL DEFAULT 'Y',
  PRIMARY KEY (`iddefaultpermission`),
  KEY `FK_tbdefaultpermission` (`idaccesstype`),
  KEY `FK_tbdefaultpermission2` (`idprogram`),
  CONSTRAINT `FK_tbdefaultpermission` FOREIGN KEY (`idaccesstype`) REFERENCES `tbaccesstype` (`idaccesstype`),
  CONSTRAINT `FK_tbdefaultpermission2` FOREIGN KEY (`idprogram`) REFERENCES `tbprogram` (`idprogram`)
) ENGINE=InnoDB AUTO_INCREMENT=260 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

INSERT INTO tbdefaultpermission VALUES("1","1","1","Y");
INSERT INTO tbdefaultpermission VALUES("2","2","1","Y");
INSERT INTO tbdefaultpermission VALUES("3","3","1","Y");
INSERT INTO tbdefaultpermission VALUES("4","4","1","Y");
INSERT INTO tbdefaultpermission VALUES("5","1","2","Y");
INSERT INTO tbdefaultpermission VALUES("6","2","2","Y");
INSERT INTO tbdefaultpermission VALUES("7","3","2","Y");
INSERT INTO tbdefaultpermission VALUES("8","4","2","Y");
INSERT INTO tbdefaultpermission VALUES("9","1","3","Y");
INSERT INTO tbdefaultpermission VALUES("10","2","3","Y");
INSERT INTO tbdefaultpermission VALUES("11","3","3","Y");
INSERT INTO tbdefaultpermission VALUES("12","4","3","Y");
INSERT INTO tbdefaultpermission VALUES("13","1","4","Y");
INSERT INTO tbdefaultpermission VALUES("14","2","4","Y");
INSERT INTO tbdefaultpermission VALUES("15","3","4","Y");
INSERT INTO tbdefaultpermission VALUES("16","4","4","Y");
INSERT INTO tbdefaultpermission VALUES("17","1","5","Y");
INSERT INTO tbdefaultpermission VALUES("18","2","5","Y");
INSERT INTO tbdefaultpermission VALUES("19","3","5","Y");
INSERT INTO tbdefaultpermission VALUES("20","4","5","Y");
INSERT INTO tbdefaultpermission VALUES("21","1","6","Y");
INSERT INTO tbdefaultpermission VALUES("22","2","6","Y");
INSERT INTO tbdefaultpermission VALUES("23","3","6","Y");
INSERT INTO tbdefaultpermission VALUES("24","4","6","Y");
INSERT INTO tbdefaultpermission VALUES("25","1","7","Y");
INSERT INTO tbdefaultpermission VALUES("26","2","7","Y");
INSERT INTO tbdefaultpermission VALUES("27","3","7","Y");
INSERT INTO tbdefaultpermission VALUES("28","4","7","Y");
INSERT INTO tbdefaultpermission VALUES("29","1","8","Y");
INSERT INTO tbdefaultpermission VALUES("30","2","8","Y");
INSERT INTO tbdefaultpermission VALUES("31","3","8","Y");
INSERT INTO tbdefaultpermission VALUES("32","4","8","Y");
INSERT INTO tbdefaultpermission VALUES("33","1","9","Y");
INSERT INTO tbdefaultpermission VALUES("34","2","9","Y");
INSERT INTO tbdefaultpermission VALUES("35","3","9","Y");
INSERT INTO tbdefaultpermission VALUES("36","4","9","Y");
INSERT INTO tbdefaultpermission VALUES("37","1","10","Y");
INSERT INTO tbdefaultpermission VALUES("38","2","10","Y");
INSERT INTO tbdefaultpermission VALUES("39","3","10","Y");
INSERT INTO tbdefaultpermission VALUES("40","4","10","Y");
INSERT INTO tbdefaultpermission VALUES("45","1","12","Y");
INSERT INTO tbdefaultpermission VALUES("46","2","12","Y");
INSERT INTO tbdefaultpermission VALUES("47","3","12","Y");
INSERT INTO tbdefaultpermission VALUES("48","4","12","Y");
INSERT INTO tbdefaultpermission VALUES("57","1","16","Y");
INSERT INTO tbdefaultpermission VALUES("58","2","16","Y");
INSERT INTO tbdefaultpermission VALUES("59","3","16","Y");
INSERT INTO tbdefaultpermission VALUES("60","4","16","Y");
INSERT INTO tbdefaultpermission VALUES("69","1","23","Y");
INSERT INTO tbdefaultpermission VALUES("70","2","23","Y");
INSERT INTO tbdefaultpermission VALUES("71","3","23","Y");
INSERT INTO tbdefaultpermission VALUES("72","4","23","Y");
INSERT INTO tbdefaultpermission VALUES("73","1","25","Y");
INSERT INTO tbdefaultpermission VALUES("74","2","25","Y");
INSERT INTO tbdefaultpermission VALUES("75","3","25","Y");
INSERT INTO tbdefaultpermission VALUES("76","4","25","Y");
INSERT INTO tbdefaultpermission VALUES("77","5","2","Y");
INSERT INTO tbdefaultpermission VALUES("85","1","32","Y");
INSERT INTO tbdefaultpermission VALUES("86","2","32","Y");
INSERT INTO tbdefaultpermission VALUES("87","3","32","Y");
INSERT INTO tbdefaultpermission VALUES("161","1","51","Y");
INSERT INTO tbdefaultpermission VALUES("162","5","51","Y");
INSERT INTO tbdefaultpermission VALUES("163","6","51","Y");
INSERT INTO tbdefaultpermission VALUES("174","1","59","Y");
INSERT INTO tbdefaultpermission VALUES("175","2","59","Y");
INSERT INTO tbdefaultpermission VALUES("176","3","59","Y");
INSERT INTO tbdefaultpermission VALUES("177","4","59","Y");
INSERT INTO tbdefaultpermission VALUES("182","1","61","Y");
INSERT INTO tbdefaultpermission VALUES("183","2","61","Y");
INSERT INTO tbdefaultpermission VALUES("184","3","61","Y");
INSERT INTO tbdefaultpermission VALUES("185","4","61","Y");
INSERT INTO tbdefaultpermission VALUES("194","1","64","Y");
INSERT INTO tbdefaultpermission VALUES("195","2","64","Y");
INSERT INTO tbdefaultpermission VALUES("196","3","64","Y");
INSERT INTO tbdefaultpermission VALUES("197","5","64","Y");
INSERT INTO tbdefaultpermission VALUES("198","1","65","Y");
INSERT INTO tbdefaultpermission VALUES("199","2","65","Y");
INSERT INTO tbdefaultpermission VALUES("200","3","65","Y");
INSERT INTO tbdefaultpermission VALUES("201","4","65","Y");
INSERT INTO tbdefaultpermission VALUES("202","5","65","Y");
INSERT INTO tbdefaultpermission VALUES("203","1","66","Y");
INSERT INTO tbdefaultpermission VALUES("204","2","66","Y");
INSERT INTO tbdefaultpermission VALUES("205","5","66","Y");
INSERT INTO tbdefaultpermission VALUES("206","1","67","Y");
INSERT INTO tbdefaultpermission VALUES("207","2","67","Y");
INSERT INTO tbdefaultpermission VALUES("208","5","67","Y");
INSERT INTO tbdefaultpermission VALUES("209","1","68","Y");
INSERT INTO tbdefaultpermission VALUES("210","2","68","Y");
INSERT INTO tbdefaultpermission VALUES("211","5","68","Y");
INSERT INTO tbdefaultpermission VALUES("212","1","69","Y");
INSERT INTO tbdefaultpermission VALUES("213","2","69","Y");
INSERT INTO tbdefaultpermission VALUES("214","5","69","Y");
INSERT INTO tbdefaultpermission VALUES("215","1","70","Y");
INSERT INTO tbdefaultpermission VALUES("216","2","70","Y");
INSERT INTO tbdefaultpermission VALUES("217","5","70","Y");
INSERT INTO tbdefaultpermission VALUES("218","1","71","Y");
INSERT INTO tbdefaultpermission VALUES("219","2","71","Y");
INSERT INTO tbdefaultpermission VALUES("220","5","71","Y");
INSERT INTO tbdefaultpermission VALUES("221","1","72","Y");
INSERT INTO tbdefaultpermission VALUES("222","2","72","Y");
INSERT INTO tbdefaultpermission VALUES("223","5","72","Y");
INSERT INTO tbdefaultpermission VALUES("224","1","73","Y");
INSERT INTO tbdefaultpermission VALUES("225","2","73","Y");
INSERT INTO tbdefaultpermission VALUES("226","5","73","Y");
INSERT INTO tbdefaultpermission VALUES("227","1","74","Y");
INSERT INTO tbdefaultpermission VALUES("228","2","74","Y");
INSERT INTO tbdefaultpermission VALUES("229","3","74","Y");
INSERT INTO tbdefaultpermission VALUES("230","4","74","Y");
INSERT INTO tbdefaultpermission VALUES("231","5","74","Y");
INSERT INTO tbdefaultpermission VALUES("232","1","75","Y");
INSERT INTO tbdefaultpermission VALUES("233","2","75","Y");
INSERT INTO tbdefaultpermission VALUES("234","3","75","Y");
INSERT INTO tbdefaultpermission VALUES("235","1","76","Y");
INSERT INTO tbdefaultpermission VALUES("236","2","76","Y");
INSERT INTO tbdefaultpermission VALUES("237","1","77","Y");
INSERT INTO tbdefaultpermission VALUES("238","2","77","Y");
INSERT INTO tbdefaultpermission VALUES("239","3","77","Y");
INSERT INTO tbdefaultpermission VALUES("240","4","77","Y");
INSERT INTO tbdefaultpermission VALUES("241","5","77","Y");
INSERT INTO tbdefaultpermission VALUES("242","1","78","Y");
INSERT INTO tbdefaultpermission VALUES("243","2","78","Y");
INSERT INTO tbdefaultpermission VALUES("244","5","78","Y");
INSERT INTO tbdefaultpermission VALUES("245","1","79","Y");
INSERT INTO tbdefaultpermission VALUES("246","2","79","Y");
INSERT INTO tbdefaultpermission VALUES("247","3","79","Y");
INSERT INTO tbdefaultpermission VALUES("248","4","79","Y");
INSERT INTO tbdefaultpermission VALUES("249","1","80","Y");
INSERT INTO tbdefaultpermission VALUES("250","2","80","Y");
INSERT INTO tbdefaultpermission VALUES("251","3","80","Y");
INSERT INTO tbdefaultpermission VALUES("252","4","80","Y");
INSERT INTO tbdefaultpermission VALUES("253","5","80","Y");
INSERT INTO tbdefaultpermission VALUES("254","1","81","Y");
INSERT INTO tbdefaultpermission VALUES("255","2","81","Y");
INSERT INTO tbdefaultpermission VALUES("256","3","81","Y");
INSERT INTO tbdefaultpermission VALUES("257","4","81","Y");
INSERT INTO tbdefaultpermission VALUES("258","5","81","Y");
INSERT INTO tbdefaultpermission VALUES("259","6","81","Y");



DROP TABLE IF EXISTS tbdepartment;

CREATE TABLE `tbdepartment` (
  `iddepartment` int(11) NOT NULL AUTO_INCREMENT,
  `idcompany` int(11) NOT NULL,
  `name` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`iddepartment`),
  KEY `fk_tbdepartment_tbcompany1` (`idcompany`),
  CONSTRAINT `fk_tbdepartment_tbcompany1` FOREIGN KEY (`idcompany`) REFERENCES `tbcompany` (`idcompany`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

INSERT INTO tbdepartment VALUES("1","1","IT");



DROP TABLE IF EXISTS tbdeploy;

CREATE TABLE `tbdeploy` (
  `iddeploy` int(11) NOT NULL AUTO_INCREMENT,
  `gitserver` char(100) DEFAULT NULL,
  `dttrigger` datetime DEFAULT NULL,
  `dtdone` datetime DEFAULT NULL,
  PRIMARY KEY (`iddeploy`),
  KEY `dtdone` (`dtdone`),
  KEY `dttrigger` (`dttrigger`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;




DROP TABLE IF EXISTS tbemail;

CREATE TABLE `tbemail` (
  `idemail` int(11) NOT NULL AUTO_INCREMENT,
  `idmodule` int(11) NOT NULL,
  `from` varchar(255) DEFAULT NULL COMMENT 'According to RFC 5321',
  `to` varchar(255) DEFAULT NULL COMMENT 'According to RFC 5321',
  `subject` varchar(255) DEFAULT NULL,
  `body` blob DEFAULT NULL,
  PRIMARY KEY (`idemail`),
  KEY `IDX_tbemail_from` (`from`),
  KEY `IDX_tbemail_idmodule` (`idmodule`),
  KEY `IDX_tbemail_subject` (`subject`),
  KEY `IDX_tbemail_to` (`to`),
  CONSTRAINT `FK_tbemail_tbmodule_idmodule` FOREIGN KEY (`idmodule`) REFERENCES `tbmodule` (`idmodule`)
) ENGINE=InnoDB AUTO_INCREMENT=1913 DEFAULT CHARSET=latin1;




DROP TABLE IF EXISTS tbholiday;

CREATE TABLE `tbholiday` (
  `idholiday` int(4) NOT NULL AUTO_INCREMENT,
  `holiday_date` date NOT NULL,
  `holiday_description` varchar(50) NOT NULL,
  PRIMARY KEY (`idholiday`)
) ENGINE=InnoDB AUTO_INCREMENT=160 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

INSERT INTO tbholiday VALUES("16","2020-01-01","First day of the year");
INSERT INTO tbholiday VALUES("20","2020-04-07","Independence Day");
INSERT INTO tbholiday VALUES("32","2020-12-25","Christmas");
INSERT INTO tbholiday VALUES("152","2020-01-20","Martin Luther King\'s birthday");
INSERT INTO tbholiday VALUES("153","2020-01-16","Washington\'s Birthday");
INSERT INTO tbholiday VALUES("154","2020-05-25","Memorial Day");
INSERT INTO tbholiday VALUES("155","2020-09-07","Labor Day");
INSERT INTO tbholiday VALUES("156","2020-10-12","Columbu\'s Day");
INSERT INTO tbholiday VALUES("157","2020-11-11","Veterans Day");
INSERT INTO tbholiday VALUES("158","2020-11-26","Thanksgiving Day");
INSERT INTO tbholiday VALUES("159","2020-02-17","President\'s Day");



DROP TABLE IF EXISTS tbholiday_has_company;

CREATE TABLE `tbholiday_has_company` (
  `idholiday` int(4) NOT NULL,
  `idperson` int(11) NOT NULL,
  KEY `fk_idholiday_tbholiday_has_company` (`idholiday`),
  KEY `fk_idperson_tbholiday_has_company` (`idperson`),
  CONSTRAINT `fk_idholiday_tbholiday_has_company` FOREIGN KEY (`idholiday`) REFERENCES `tbholiday` (`idholiday`),
  CONSTRAINT `fk_idperson_tbholiday_has_company` FOREIGN KEY (`idperson`) REFERENCES `tbperson` (`idperson`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO tbholiday_has_company VALUES("159","1038");



DROP TABLE IF EXISTS tbjuridicalperson;

CREATE TABLE `tbjuridicalperson` (
  `idjuridicalperson` int(11) NOT NULL AUTO_INCREMENT,
  `idperson` int(11) NOT NULL,
  `ein_cnpj` varchar(18) DEFAULT NULL,
  `iestadual` varchar(20) DEFAULT NULL,
  `contact_person` varchar(80) DEFAULT NULL,
  `observation` blob DEFAULT NULL,
  PRIMARY KEY (`idjuridicalperson`),
  KEY `fk_tbjuridicalperson_tbperson1` (`idperson`),
  CONSTRAINT `fk_tbjuridicalperson_tbperson1` FOREIGN KEY (`idperson`) REFERENCES `tbperson` (`idperson`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

INSERT INTO tbjuridicalperson VALUES("6","1038","07452272/000171","","Gustavo Silveira","");



DROP TABLE IF EXISTS tblocale;

CREATE TABLE `tblocale` (
  `idlocale` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`idlocale`),
  UNIQUE KEY `UK_tblocale_name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=611 DEFAULT CHARSET=latin1;

INSERT INTO tblocale VALUES("1","ab","Abkasies");
INSERT INTO tblocale VALUES("3","ace","Achinese");
INSERT INTO tblocale VALUES("4","ada","Adangme");
INSERT INTO tblocale VALUES("5","ady","Adyghe");
INSERT INTO tblocale VALUES("6","aa","Afar");
INSERT INTO tblocale VALUES("7","afh","Afrihili");
INSERT INTO tblocale VALUES("8","af","Afrikaans");
INSERT INTO tblocale VALUES("9","agq","Aghem");
INSERT INTO tblocale VALUES("10","ain","Ainu");
INSERT INTO tblocale VALUES("11","ak","Akan");
INSERT INTO tblocale VALUES("12","akk","Akkadian");
INSERT INTO tblocale VALUES("13","ach","Akoli");
INSERT INTO tblocale VALUES("14","bss","Akoose");
INSERT INTO tblocale VALUES("15","akz","Alabama");
INSERT INTO tblocale VALUES("16","sq","Albanees");
INSERT INTO tblocale VALUES("17","ale","Aleut");
INSERT INTO tblocale VALUES("18","arq","Algerian Arabic");
INSERT INTO tblocale VALUES("19","en_US","American English");
INSERT INTO tblocale VALUES("20","ase","American Sign Language");
INSERT INTO tblocale VALUES("21","am","Amharies");
INSERT INTO tblocale VALUES("22","anp","Angika");
INSERT INTO tblocale VALUES("23","egy","Antieke Egipties");
INSERT INTO tblocale VALUES("24","grc","Antieke Grieks");
INSERT INTO tblocale VALUES("25","njo","Ao Naga");
INSERT INTO tblocale VALUES("26","ar","Arabies");
INSERT INTO tblocale VALUES("27","an","Aragonese");
INSERT INTO tblocale VALUES("28","arc","Aramees");
INSERT INTO tblocale VALUES("29","aro","Araona");
INSERT INTO tblocale VALUES("30","arp","Arapaho");
INSERT INTO tblocale VALUES("31","arw","Arawak");
INSERT INTO tblocale VALUES("32","hy","Armeens");
INSERT INTO tblocale VALUES("33","rup","Aromanian");
INSERT INTO tblocale VALUES("34","frp","Arpitan");
INSERT INTO tblocale VALUES("35","as","Assamees");
INSERT INTO tblocale VALUES("36","ast","Asturian");
INSERT INTO tblocale VALUES("37","asa","Asu");
INSERT INTO tblocale VALUES("38","cch","Atsam");
INSERT INTO tblocale VALUES("39","en_AU","Australian English");
INSERT INTO tblocale VALUES("40","de_AT","Austrian German");
INSERT INTO tblocale VALUES("41","av","Avaric");
INSERT INTO tblocale VALUES("42","ae","Avestan");
INSERT INTO tblocale VALUES("43","awa","Awadhi");
INSERT INTO tblocale VALUES("44","ay","Aymara");
INSERT INTO tblocale VALUES("45","az","Azerbeidjans");
INSERT INTO tblocale VALUES("46","bfq","Badaga");
INSERT INTO tblocale VALUES("47","ksf","Bafia");
INSERT INTO tblocale VALUES("48","bfd","Bafut");
INSERT INTO tblocale VALUES("49","bqi","Bakhtiari");
INSERT INTO tblocale VALUES("50","ban","Balinese");
INSERT INTO tblocale VALUES("51","bal","Baluchi");
INSERT INTO tblocale VALUES("52","bm","Bambara");
INSERT INTO tblocale VALUES("53","bax","Bamun");
INSERT INTO tblocale VALUES("54","bjn","Banjar");
INSERT INTO tblocale VALUES("55","bas","Basaa");
INSERT INTO tblocale VALUES("56","eu","Baskies");
INSERT INTO tblocale VALUES("57","ba","Baskir");
INSERT INTO tblocale VALUES("58","bbc","Batak Toba");
INSERT INTO tblocale VALUES("59","bar","Bavarian");
INSERT INTO tblocale VALUES("60","bej","Beja");
INSERT INTO tblocale VALUES("61","bem","Bemba");
INSERT INTO tblocale VALUES("62","bez","Bena");
INSERT INTO tblocale VALUES("63","bn","Bengaals");
INSERT INTO tblocale VALUES("64","bew","Betawi");
INSERT INTO tblocale VALUES("65","bho","Bhojpuri");
INSERT INTO tblocale VALUES("66","bik","Bikol");
INSERT INTO tblocale VALUES("67","bin","Bini");
INSERT INTO tblocale VALUES("68","my","Birmaans");
INSERT INTO tblocale VALUES("69","bpy","Bishnupriya");
INSERT INTO tblocale VALUES("70","bi","Bislama");
INSERT INTO tblocale VALUES("71","byn","Blin");
INSERT INTO tblocale VALUES("72","zbl","Blissymbols");
INSERT INTO tblocale VALUES("73","brx","Bodo");
INSERT INTO tblocale VALUES("74","bs","Bosnies");
INSERT INTO tblocale VALUES("75","brh","Brahui");
INSERT INTO tblocale VALUES("76","bra","Braj");
INSERT INTO tblocale VALUES("77","pt_BR","Brazilian Portuguese");
INSERT INTO tblocale VALUES("78","br","Bretons");
INSERT INTO tblocale VALUES("79","en_GB","British English");
INSERT INTO tblocale VALUES("80","bug","Buginese");
INSERT INTO tblocale VALUES("81","bg","Bulgaars");
INSERT INTO tblocale VALUES("82","bum","Bulu");
INSERT INTO tblocale VALUES("83","bua","Buriat");
INSERT INTO tblocale VALUES("84","cad","Caddo");
INSERT INTO tblocale VALUES("85","frc","Cajun French");
INSERT INTO tblocale VALUES("86","en_CA","Canadian English");
INSERT INTO tblocale VALUES("87","fr_CA","Canadian French");
INSERT INTO tblocale VALUES("88","yue","Cantonese");
INSERT INTO tblocale VALUES("89","cps","Capiznon");
INSERT INTO tblocale VALUES("90","car","Carib");
INSERT INTO tblocale VALUES("91","cay","Cayuga");
INSERT INTO tblocale VALUES("92","ceb","Cebuano");
INSERT INTO tblocale VALUES("93","dtp","Central Dusun");
INSERT INTO tblocale VALUES("94","esu","Central Yupik");
INSERT INTO tblocale VALUES("95","shu","Chadian Arabic");
INSERT INTO tblocale VALUES("96","chg","Chagatai");
INSERT INTO tblocale VALUES("97","ch","Chamorro");
INSERT INTO tblocale VALUES("98","ce","Chechen");
INSERT INTO tblocale VALUES("99","chr","Cherokees");
INSERT INTO tblocale VALUES("100","chy","Cheyenne");
INSERT INTO tblocale VALUES("101","chb","Chibcha");
INSERT INTO tblocale VALUES("102","qug","Chimborazo Highland Quichua");
INSERT INTO tblocale VALUES("103","chn","Chinook Jargon");
INSERT INTO tblocale VALUES("104","chp","Chipewyan");
INSERT INTO tblocale VALUES("105","cho","Choctaw");
INSERT INTO tblocale VALUES("106","cu","Church Slavic");
INSERT INTO tblocale VALUES("107","chk","Chuukese");
INSERT INTO tblocale VALUES("108","cv","Chuvash");
INSERT INTO tblocale VALUES("109","nwc","Classical Newari");
INSERT INTO tblocale VALUES("110","syc","Classical Syriac");
INSERT INTO tblocale VALUES("111","ksh","Colognian");
INSERT INTO tblocale VALUES("112","swb","Comorian");
INSERT INTO tblocale VALUES("113","cop","Coptic");
INSERT INTO tblocale VALUES("114","cr","Cree");
INSERT INTO tblocale VALUES("115","mus","Creek");
INSERT INTO tblocale VALUES("116","crh","Crimean Turkish");
INSERT INTO tblocale VALUES("117","dak","Dakota");
INSERT INTO tblocale VALUES("118","dar","Dargwa");
INSERT INTO tblocale VALUES("119","dzg","Dazaga");
INSERT INTO tblocale VALUES("120","da","Deens");
INSERT INTO tblocale VALUES("121","del","Delaware");
INSERT INTO tblocale VALUES("122","din","Dinka");
INSERT INTO tblocale VALUES("123","dv","Divehi");
INSERT INTO tblocale VALUES("124","doi","Dogri");
INSERT INTO tblocale VALUES("125","dgr","Dogrib");
INSERT INTO tblocale VALUES("126","dua","Duala");
INSERT INTO tblocale VALUES("127","de","Duits");
INSERT INTO tblocale VALUES("128","dyu","Dyula");
INSERT INTO tblocale VALUES("129","dz","Dzongkha");
INSERT INTO tblocale VALUES("130","frs","Eastern Frisian");
INSERT INTO tblocale VALUES("131","efi","Efik");
INSERT INTO tblocale VALUES("132","arz","Egyptian Arabic");
INSERT INTO tblocale VALUES("133","eka","Ekajuk");
INSERT INTO tblocale VALUES("134","elx","Elamite");
INSERT INTO tblocale VALUES("135","ebu","Embu");
INSERT INTO tblocale VALUES("136","egl","Emilian");
INSERT INTO tblocale VALUES("137","en","Engels");
INSERT INTO tblocale VALUES("138","myv","Erzya");
INSERT INTO tblocale VALUES("139","eo","Esperanto");
INSERT INTO tblocale VALUES("140","et","Estnies");
INSERT INTO tblocale VALUES("141","pt_PT","European Portuguese");
INSERT INTO tblocale VALUES("142","es_ES","European Spanish");
INSERT INTO tblocale VALUES("143","ee","Ewe");
INSERT INTO tblocale VALUES("144","ewo","Ewondo");
INSERT INTO tblocale VALUES("145","ext","Extremaduran");
INSERT INTO tblocale VALUES("146","fan","Fang");
INSERT INTO tblocale VALUES("147","fat","Fanti");
INSERT INTO tblocale VALUES("148","fo","Faroees");
INSERT INTO tblocale VALUES("149","fj","Fidjiaans");
INSERT INTO tblocale VALUES("150","hif","Fiji Hindi");
INSERT INTO tblocale VALUES("151","fil","Filippyns");
INSERT INTO tblocale VALUES("152","fi","Fins");
INSERT INTO tblocale VALUES("153","fon","Fon");
INSERT INTO tblocale VALUES("154","gur","Frafra");
INSERT INTO tblocale VALUES("155","fr","Frans");
INSERT INTO tblocale VALUES("156","fur","Friulian");
INSERT INTO tblocale VALUES("157","ff","Fulah");
INSERT INTO tblocale VALUES("158","gaa","Gaa");
INSERT INTO tblocale VALUES("159","gag","Gagauz");
INSERT INTO tblocale VALUES("160","gl","Galisies");
INSERT INTO tblocale VALUES("161","gan","Gan Chinese");
INSERT INTO tblocale VALUES("162","lg","Ganda");
INSERT INTO tblocale VALUES("163","gay","Gayo");
INSERT INTO tblocale VALUES("164","gba","Gbaya");
INSERT INTO tblocale VALUES("165","zxx","Geen linguistiese inhoud");
INSERT INTO tblocale VALUES("166","gez","Geez");
INSERT INTO tblocale VALUES("167","ka","Georgies");
INSERT INTO tblocale VALUES("168","aln","Gheg Albanian");
INSERT INTO tblocale VALUES("169","bbj","Ghomala");
INSERT INTO tblocale VALUES("170","glk","Gilaki");
INSERT INTO tblocale VALUES("171","gil","Gilbertese");
INSERT INTO tblocale VALUES("172","gom","Goan Konkani");
INSERT INTO tblocale VALUES("173","gu","Goedjarati");
INSERT INTO tblocale VALUES("174","gon","Gondi");
INSERT INTO tblocale VALUES("175","gor","Gorontalo");
INSERT INTO tblocale VALUES("176","got","Goties");
INSERT INTO tblocale VALUES("177","grb","Grebo");
INSERT INTO tblocale VALUES("178","el","Grieks");
INSERT INTO tblocale VALUES("179","gn","Guarani");
INSERT INTO tblocale VALUES("180","guz","Gusii");
INSERT INTO tblocale VALUES("181","gwi","Gwich?in");
INSERT INTO tblocale VALUES("182","hai","Haida");
INSERT INTO tblocale VALUES("183","ht","Haïtiaans");
INSERT INTO tblocale VALUES("184","hak","Hakka Chinese");
INSERT INTO tblocale VALUES("185","ha","Hausa");
INSERT INTO tblocale VALUES("186","haw","Hawaiies");
INSERT INTO tblocale VALUES("187","he","Hebreeus");
INSERT INTO tblocale VALUES("188","hz","Herero");
INSERT INTO tblocale VALUES("189","hil","Hiligaynon");
INSERT INTO tblocale VALUES("190","hi","Hindi");
INSERT INTO tblocale VALUES("191","ho","Hiri Motu");
INSERT INTO tblocale VALUES("192","hit","Hittite");
INSERT INTO tblocale VALUES("193","hmn","Hmong");
INSERT INTO tblocale VALUES("194","hu","Hongaars");
INSERT INTO tblocale VALUES("195","hsb","Hoog-Sorbies");
INSERT INTO tblocale VALUES("196","hup","Hupa");
INSERT INTO tblocale VALUES("197","iba","Iban");
INSERT INTO tblocale VALUES("198","ibb","Ibibio");
INSERT INTO tblocale VALUES("199","io","Ido");
INSERT INTO tblocale VALUES("200","ga","Iers");
INSERT INTO tblocale VALUES("201","ig","Igbo");
INSERT INTO tblocale VALUES("202","ilo","Iloko");
INSERT INTO tblocale VALUES("203","smn","Inari Sami");
INSERT INTO tblocale VALUES("204","id","Indonesies");
INSERT INTO tblocale VALUES("205","izh","Ingrian");
INSERT INTO tblocale VALUES("206","inh","Ingush");
INSERT INTO tblocale VALUES("207","iu","Innuïties");
INSERT INTO tblocale VALUES("208","ia","Interlingua");
INSERT INTO tblocale VALUES("209","ie","Interlingue");
INSERT INTO tblocale VALUES("210","ik","Inupiaq");
INSERT INTO tblocale VALUES("211","it","Italiaans");
INSERT INTO tblocale VALUES("212","jam","Jamaican Creole English");
INSERT INTO tblocale VALUES("213","ja","Japannees");
INSERT INTO tblocale VALUES("214","jv","Javaans");
INSERT INTO tblocale VALUES("215","yi","Jiddisj");
INSERT INTO tblocale VALUES("216","kaj","Jju");
INSERT INTO tblocale VALUES("217","dyo","Jola-Fonyi");
INSERT INTO tblocale VALUES("218","jrb","Judeo-Arabic");
INSERT INTO tblocale VALUES("219","jpr","Judeo-Persian");
INSERT INTO tblocale VALUES("220","jut","Jutish");
INSERT INTO tblocale VALUES("221","quc","K’iche’");
INSERT INTO tblocale VALUES("222","kbd","Kabardian");
INSERT INTO tblocale VALUES("223","kea","Kabuverdianu");
INSERT INTO tblocale VALUES("224","kab","Kabyle");
INSERT INTO tblocale VALUES("225","kac","Kachin");
INSERT INTO tblocale VALUES("226","kgp","Kaingang");
INSERT INTO tblocale VALUES("227","kkj","Kako");
INSERT INTO tblocale VALUES("228","kl","Kalaallisut");
INSERT INTO tblocale VALUES("229","kln","Kalenjin");
INSERT INTO tblocale VALUES("230","xal","Kalmyk");
INSERT INTO tblocale VALUES("231","kam","Kamba");
INSERT INTO tblocale VALUES("232","kbl","Kanembu");
INSERT INTO tblocale VALUES("233","kn","Kannada");
INSERT INTO tblocale VALUES("234","kr","Kanuri");
INSERT INTO tblocale VALUES("235","kaa","Kara-Kalpak");
INSERT INTO tblocale VALUES("236","krc","Karachay-Balkar");
INSERT INTO tblocale VALUES("237","krl","Karelian");
INSERT INTO tblocale VALUES("238","csb","Kashubian");
INSERT INTO tblocale VALUES("239","ks","Kasjmirs");
INSERT INTO tblocale VALUES("240","ca","Katalaans");
INSERT INTO tblocale VALUES("241","kaw","Kawi");
INSERT INTO tblocale VALUES("242","kk","Kazaks");
INSERT INTO tblocale VALUES("243","ken","Kenyang");
INSERT INTO tblocale VALUES("244","kha","Khasi");
INSERT INTO tblocale VALUES("245","km","Khmer");
INSERT INTO tblocale VALUES("246","kho","Khotanese");
INSERT INTO tblocale VALUES("247","khw","Khowar");
INSERT INTO tblocale VALUES("248","ki","Kikuyu");
INSERT INTO tblocale VALUES("249","kmb","Kimbundu");
INSERT INTO tblocale VALUES("250","krj","Kinaray-a");
INSERT INTO tblocale VALUES("251","ky","Kirgisies");
INSERT INTO tblocale VALUES("252","kiu","Kirmanjki");
INSERT INTO tblocale VALUES("253","tlh","Klingon");
INSERT INTO tblocale VALUES("254","ku","Koerdies");
INSERT INTO tblocale VALUES("255","bkm","Kom");
INSERT INTO tblocale VALUES("256","kv","Komi");
INSERT INTO tblocale VALUES("257","koi","Komi-Permyaks");
INSERT INTO tblocale VALUES("258","kg","Kongolees");
INSERT INTO tblocale VALUES("259","kok","Konkani");
INSERT INTO tblocale VALUES("260","ko","Koreaans");
INSERT INTO tblocale VALUES("261","kw","Kornies");
INSERT INTO tblocale VALUES("262","kfo","Koro");
INSERT INTO tblocale VALUES("263","co","Korsikaans");
INSERT INTO tblocale VALUES("264","kos","Kosraean");
INSERT INTO tblocale VALUES("265","avk","Kotava");
INSERT INTO tblocale VALUES("266","khq","Koyra Chiini");
INSERT INTO tblocale VALUES("267","ses","Koyraboro Senni");
INSERT INTO tblocale VALUES("268","kpe","Kpelle");
INSERT INTO tblocale VALUES("269","kri","Krio");
INSERT INTO tblocale VALUES("270","hr","Kroaties");
INSERT INTO tblocale VALUES("271","kj","Kuanyama");
INSERT INTO tblocale VALUES("272","kum","Kumyk");
INSERT INTO tblocale VALUES("273","kru","Kurukh");
INSERT INTO tblocale VALUES("274","kut","Kutenai");
INSERT INTO tblocale VALUES("275","nmg","Kwasio");
INSERT INTO tblocale VALUES("276","lad","Ladino");
INSERT INTO tblocale VALUES("277","dsb","Lae Sorbies");
INSERT INTO tblocale VALUES("278","lah","Lahnda");
INSERT INTO tblocale VALUES("279","lkt","Lakota");
INSERT INTO tblocale VALUES("280","lam","Lamba");
INSERT INTO tblocale VALUES("281","lag","Langi");
INSERT INTO tblocale VALUES("282","lo","Lao");
INSERT INTO tblocale VALUES("283","ltg","Latgalian");
INSERT INTO tblocale VALUES("284","es_419","Latin American Spanish");
INSERT INTO tblocale VALUES("285","la","Latyn");
INSERT INTO tblocale VALUES("286","lzz","Laz");
INSERT INTO tblocale VALUES("287","lv","Letties");
INSERT INTO tblocale VALUES("288","lez","Lezghian");
INSERT INTO tblocale VALUES("289","lij","Ligurian");
INSERT INTO tblocale VALUES("290","li","Limburgish");
INSERT INTO tblocale VALUES("291","ln","Lingaals");
INSERT INTO tblocale VALUES("292","lfn","Lingua Franca Nova");
INSERT INTO tblocale VALUES("293","lt","Litaus");
INSERT INTO tblocale VALUES("294","lzh","Literary Chinese");
INSERT INTO tblocale VALUES("295","liv","Livonian");
INSERT INTO tblocale VALUES("296","jbo","Lojban");
INSERT INTO tblocale VALUES("297","lmo","Lombard");
INSERT INTO tblocale VALUES("298","nds","Low German");
INSERT INTO tblocale VALUES("299","sli","Lower Silesian");
INSERT INTO tblocale VALUES("300","loz","Lozi");
INSERT INTO tblocale VALUES("301","lu","Luba-Katanga");
INSERT INTO tblocale VALUES("302","lua","Luba-Lulua");
INSERT INTO tblocale VALUES("303","lui","Luiseno");
INSERT INTO tblocale VALUES("304","smj","Lule Sami");
INSERT INTO tblocale VALUES("305","lun","Lunda");
INSERT INTO tblocale VALUES("306","luo","Luo");
INSERT INTO tblocale VALUES("307","lb","Luxemburgs");
INSERT INTO tblocale VALUES("308","luy","Luyia");
INSERT INTO tblocale VALUES("309","mde","Maba");
INSERT INTO tblocale VALUES("310","jmc","Machame");
INSERT INTO tblocale VALUES("311","mad","Madurese");
INSERT INTO tblocale VALUES("312","maf","Mafa");
INSERT INTO tblocale VALUES("313","mag","Magahi");
INSERT INTO tblocale VALUES("314","vmf","Main-Franconian");
INSERT INTO tblocale VALUES("315","mai","Maithili");
INSERT INTO tblocale VALUES("316","mak","Makasar");
INSERT INTO tblocale VALUES("317","mgh","Makhuwa-Meetto");
INSERT INTO tblocale VALUES("318","kde","Makonde");
INSERT INTO tblocale VALUES("319","ml","Malabaars");
INSERT INTO tblocale VALUES("320","ms","Maleis");
INSERT INTO tblocale VALUES("321","mg","Malgassies");
INSERT INTO tblocale VALUES("322","mt","Maltees");
INSERT INTO tblocale VALUES("323","mnc","Manchu");
INSERT INTO tblocale VALUES("324","mdr","Mandar");
INSERT INTO tblocale VALUES("325","man","Mandingo");
INSERT INTO tblocale VALUES("326","mni","Manipuri");
INSERT INTO tblocale VALUES("327","gv","Manx");
INSERT INTO tblocale VALUES("328","mi","Maori");
INSERT INTO tblocale VALUES("329","arn","Mapuche");
INSERT INTO tblocale VALUES("330","mr","Marathi");
INSERT INTO tblocale VALUES("331","chm","Mari");
INSERT INTO tblocale VALUES("332","mh","Marshallese");
INSERT INTO tblocale VALUES("333","mwr","Marwari");
INSERT INTO tblocale VALUES("334","mas","Masai");
INSERT INTO tblocale VALUES("335","mk","Masedonies");
INSERT INTO tblocale VALUES("336","mzn","Mazanderani");
INSERT INTO tblocale VALUES("337","byv","Medumba");
INSERT INTO tblocale VALUES("338","men","Mende");
INSERT INTO tblocale VALUES("339","mwv","Mentawai");
INSERT INTO tblocale VALUES("340","mer","Meru");
INSERT INTO tblocale VALUES("341","mgo","Meta’");
INSERT INTO tblocale VALUES("342","es_MX","Mexican Spanish");
INSERT INTO tblocale VALUES("343","mic","Micmac");
INSERT INTO tblocale VALUES("344","dum","Middle Dutch");
INSERT INTO tblocale VALUES("345","enm","Middle English");
INSERT INTO tblocale VALUES("346","frm","Middle French");
INSERT INTO tblocale VALUES("347","gmh","Middle High German");
INSERT INTO tblocale VALUES("348","mga","Middle Irish");
INSERT INTO tblocale VALUES("349","nan","Min Nan Chinese");
INSERT INTO tblocale VALUES("350","min","Minangkabau");
INSERT INTO tblocale VALUES("351","xmf","Mingrelian");
INSERT INTO tblocale VALUES("352","mwl","Mirandese");
INSERT INTO tblocale VALUES("353","lus","Mizo");
INSERT INTO tblocale VALUES("354","ar_001","Moderne Standaard Arabies");
INSERT INTO tblocale VALUES("355","moh","Mohawk");
INSERT INTO tblocale VALUES("356","mdf","Moksha");
INSERT INTO tblocale VALUES("357","ro_MD","Moldawies");
INSERT INTO tblocale VALUES("358","lol","Mongo");
INSERT INTO tblocale VALUES("359","mn","Mongools");
INSERT INTO tblocale VALUES("360","mfe","Morisjen");
INSERT INTO tblocale VALUES("361","ary","Moroccan Arabic");
INSERT INTO tblocale VALUES("362","mos","Mossi");
INSERT INTO tblocale VALUES("363","mua","Mundang");
INSERT INTO tblocale VALUES("364","ttt","Muslim Tat");
INSERT INTO tblocale VALUES("365","mye","Myene");
INSERT INTO tblocale VALUES("366","nqo","N’Ko");
INSERT INTO tblocale VALUES("367","naq","Nama");
INSERT INTO tblocale VALUES("368","na","Nauru");
INSERT INTO tblocale VALUES("369","nv","Navajo");
INSERT INTO tblocale VALUES("370","ng","Ndonga");
INSERT INTO tblocale VALUES("371","nap","Neapolitan");
INSERT INTO tblocale VALUES("372","nl","Nederlands");
INSERT INTO tblocale VALUES("373","ne","Nepalees");
INSERT INTO tblocale VALUES("374","new","Newari");
INSERT INTO tblocale VALUES("375","sba","Ngambay");
INSERT INTO tblocale VALUES("376","nnh","Ngiemboon");
INSERT INTO tblocale VALUES("377","jgo","Ngomba");
INSERT INTO tblocale VALUES("378","yrl","Nheengatu");
INSERT INTO tblocale VALUES("379","nia","Nias");
INSERT INTO tblocale VALUES("380","niu","Niuean");
INSERT INTO tblocale VALUES("381","nog","Nogai");
INSERT INTO tblocale VALUES("382","nd","Noord-Ndebele");
INSERT INTO tblocale VALUES("383","nso","Noord-Sotho");
INSERT INTO tblocale VALUES("384","se","Noordelike Sami");
INSERT INTO tblocale VALUES("385","nb","Noorse Bokmål");
INSERT INTO tblocale VALUES("386","nn","Noorweegse Nynorsk");
INSERT INTO tblocale VALUES("387","frr","Northern Frisian");
INSERT INTO tblocale VALUES("388","no","Norwegian");
INSERT INTO tblocale VALUES("389","nov","Novial");
INSERT INTO tblocale VALUES("390","nus","Nuer");
INSERT INTO tblocale VALUES("391","nym","Nyamwezi");
INSERT INTO tblocale VALUES("392","ny","Nyanja");
INSERT INTO tblocale VALUES("393","nyn","Nyankole");
INSERT INTO tblocale VALUES("394","tog","Nyasa Tonga");
INSERT INTO tblocale VALUES("395","nyo","Nyoro");
INSERT INTO tblocale VALUES("396","nzi","Nzima");
INSERT INTO tblocale VALUES("397","uk","Oekraïens");
INSERT INTO tblocale VALUES("398","ur","Oerdoe");
INSERT INTO tblocale VALUES("399","uz","Oezbeeks");
INSERT INTO tblocale VALUES("400","oj","Ojibwa");
INSERT INTO tblocale VALUES("401","oc","Oksitaans");
INSERT INTO tblocale VALUES("402","ang","Old English");
INSERT INTO tblocale VALUES("403","fro","Old French");
INSERT INTO tblocale VALUES("404","goh","Old High German");
INSERT INTO tblocale VALUES("405","sga","Old Irish");
INSERT INTO tblocale VALUES("406","non","Old Norse");
INSERT INTO tblocale VALUES("407","peo","Old Persian");
INSERT INTO tblocale VALUES("408","pro","Old Provençal");
INSERT INTO tblocale VALUES("409","und","Onbekende of ongeldige taal");
INSERT INTO tblocale VALUES("410","or","Oriya");
INSERT INTO tblocale VALUES("411","om","Oromo");
INSERT INTO tblocale VALUES("412","osa","Osage");
INSERT INTO tblocale VALUES("413","os","Osseties");
INSERT INTO tblocale VALUES("414","ota","Ottoman Turkish");
INSERT INTO tblocale VALUES("415","pal","Pahlavi");
INSERT INTO tblocale VALUES("416","pfl","Palatine German");
INSERT INTO tblocale VALUES("417","pau","Palauan");
INSERT INTO tblocale VALUES("418","pi","Pali");
INSERT INTO tblocale VALUES("419","pam","Pampanga");
INSERT INTO tblocale VALUES("420","pa","Pandjabi");
INSERT INTO tblocale VALUES("421","pag","Pangasinan");
INSERT INTO tblocale VALUES("422","pap","Papiamento");
INSERT INTO tblocale VALUES("423","ps","Pasjto");
INSERT INTO tblocale VALUES("424","pdc","Pennsylvania German");
INSERT INTO tblocale VALUES("425","fa","Persies");
INSERT INTO tblocale VALUES("426","phn","Phoenician");
INSERT INTO tblocale VALUES("427","pcd","Picard");
INSERT INTO tblocale VALUES("428","pms","Piedmontese");
INSERT INTO tblocale VALUES("429","pdt","Plautdietsch");
INSERT INTO tblocale VALUES("430","pon","Pohnpeian");
INSERT INTO tblocale VALUES("431","pnt","Pontic");
INSERT INTO tblocale VALUES("432","pl","Pools");
INSERT INTO tblocale VALUES("433","pt","Portugees");
INSERT INTO tblocale VALUES("434","prg","Prussian");
INSERT INTO tblocale VALUES("435","qu","Quechua");
INSERT INTO tblocale VALUES("436","raj","Rajasthani");
INSERT INTO tblocale VALUES("437","rap","Rapanui");
INSERT INTO tblocale VALUES("438","rar","Rarotongan");
INSERT INTO tblocale VALUES("439","rm","Reto-Romaans");
INSERT INTO tblocale VALUES("440","rif","Riffian");
INSERT INTO tblocale VALUES("441","ro","Roemeens");
INSERT INTO tblocale VALUES("442","rgn","Romagnol");
INSERT INTO tblocale VALUES("443","rom","Romany");
INSERT INTO tblocale VALUES("444","rof","Rombo");
INSERT INTO tblocale VALUES("445","root","Root");
INSERT INTO tblocale VALUES("446","rtm","Rotuman");
INSERT INTO tblocale VALUES("447","rug","Roviana");
INSERT INTO tblocale VALUES("448","rn","Rundi");
INSERT INTO tblocale VALUES("449","ru","Russies");
INSERT INTO tblocale VALUES("450","rue","Rusyn");
INSERT INTO tblocale VALUES("451","rwk","Rwa");
INSERT INTO tblocale VALUES("452","rw","Rwandees");
INSERT INTO tblocale VALUES("453","ssy","Saho");
INSERT INTO tblocale VALUES("454","sah","Sakha");
INSERT INTO tblocale VALUES("455","sam","Samaritan Aramaic");
INSERT INTO tblocale VALUES("456","saq","Samburu");
INSERT INTO tblocale VALUES("457","sm","Samoaans");
INSERT INTO tblocale VALUES("458","sgs","Samogitian");
INSERT INTO tblocale VALUES("459","sad","Sandawe");
INSERT INTO tblocale VALUES("460","sg","Sango");
INSERT INTO tblocale VALUES("461","sbp","Sangu");
INSERT INTO tblocale VALUES("462","sa","Sanskrit");
INSERT INTO tblocale VALUES("463","sat","Santali");
INSERT INTO tblocale VALUES("464","sc","Sardinian");
INSERT INTO tblocale VALUES("465","sas","Sasak");
INSERT INTO tblocale VALUES("466","sdc","Sassarese Sardinian");
INSERT INTO tblocale VALUES("467","stq","Saterland Frisian");
INSERT INTO tblocale VALUES("468","saz","Saurashtra");
INSERT INTO tblocale VALUES("469","sco","Scots");
INSERT INTO tblocale VALUES("470","sly","Selayar");
INSERT INTO tblocale VALUES("471","sel","Selkup");
INSERT INTO tblocale VALUES("472","seh","Sena");
INSERT INTO tblocale VALUES("473","see","Seneca");
INSERT INTO tblocale VALUES("474","tzm","Sentraal Atlas Tamazight");
INSERT INTO tblocale VALUES("475","sh","Serbo-Croatian");
INSERT INTO tblocale VALUES("476","srr","Serer");
INSERT INTO tblocale VALUES("477","sei","Seri");
INSERT INTO tblocale VALUES("478","sr","Serwies");
INSERT INTO tblocale VALUES("479","ksb","Shambala");
INSERT INTO tblocale VALUES("480","shn","Shan");
INSERT INTO tblocale VALUES("481","sn","Shona");
INSERT INTO tblocale VALUES("482","ii","Sichuan Yi");
INSERT INTO tblocale VALUES("483","scn","Sicilian");
INSERT INTO tblocale VALUES("484","sid","Sidamo");
INSERT INTO tblocale VALUES("485","bla","Siksika");
INSERT INTO tblocale VALUES("486","szl","Silesian");
INSERT INTO tblocale VALUES("487","zh_Hans","Simplified Chinese");
INSERT INTO tblocale VALUES("488","sd","Sindhi");
INSERT INTO tblocale VALUES("489","si","Sinhala");
INSERT INTO tblocale VALUES("490","cgg","Sjiga");
INSERT INTO tblocale VALUES("491","zh","Sjinees");
INSERT INTO tblocale VALUES("492","sms","Skolt Sami");
INSERT INTO tblocale VALUES("493","gd","Skotse Gallies");
INSERT INTO tblocale VALUES("494","den","Slave");
INSERT INTO tblocale VALUES("495","sk","Slowaaks");
INSERT INTO tblocale VALUES("496","sl","Sloweens");
INSERT INTO tblocale VALUES("497","xog","Soga");
INSERT INTO tblocale VALUES("498","sog","Sogdien");
INSERT INTO tblocale VALUES("499","so","Somalies");
INSERT INTO tblocale VALUES("500","snk","Soninke");
INSERT INTO tblocale VALUES("501","ckb","Sorani Koerdies");
INSERT INTO tblocale VALUES("502","azb","South Azerbaijani");
INSERT INTO tblocale VALUES("503","alt","Southern Altai");
INSERT INTO tblocale VALUES("504","es","Spaans");
INSERT INTO tblocale VALUES("505","srn","Sranan Tongo");
INSERT INTO tblocale VALUES("506","zgh","Standaard Marokkaanse Tamazight");
INSERT INTO tblocale VALUES("507","nr","Suid-Ndebele");
INSERT INTO tblocale VALUES("508","sma","Suid-Sami");
INSERT INTO tblocale VALUES("509","st","Suid-Sotho");
INSERT INTO tblocale VALUES("510","suk","Sukuma");
INSERT INTO tblocale VALUES("511","sux","Sumerian");
INSERT INTO tblocale VALUES("512","su","Sundanees");
INSERT INTO tblocale VALUES("513","sus","Susu");
INSERT INTO tblocale VALUES("514","sw","Swahili");
INSERT INTO tblocale VALUES("515","swc","Swahili (Kongo)");
INSERT INTO tblocale VALUES("516","ss","Swazi");
INSERT INTO tblocale VALUES("517","sv","Sweeds");
INSERT INTO tblocale VALUES("518","fr_CH","Swiss French");
INSERT INTO tblocale VALUES("519","gsw","Switserse Duits");
INSERT INTO tblocale VALUES("520","de_CH","Switserse hoog-Duits");
INSERT INTO tblocale VALUES("521","syr","Syriac");
INSERT INTO tblocale VALUES("522","shi","Tachelhit");
INSERT INTO tblocale VALUES("523","tg","Tadzjieks");
INSERT INTO tblocale VALUES("524","tl","Tagalog");
INSERT INTO tblocale VALUES("525","ty","Tahities");
INSERT INTO tblocale VALUES("526","dav","Taita");
INSERT INTO tblocale VALUES("527","tly","Talysh");
INSERT INTO tblocale VALUES("528","tmh","Tamashek");
INSERT INTO tblocale VALUES("529","ta","Tamil");
INSERT INTO tblocale VALUES("530","trv","Taroko");
INSERT INTO tblocale VALUES("531","twq","Tasawaq");
INSERT INTO tblocale VALUES("532","tt","Tataars");
INSERT INTO tblocale VALUES("533","te","Telugu");
INSERT INTO tblocale VALUES("534","ter","Tereno");
INSERT INTO tblocale VALUES("535","teo","Teso");
INSERT INTO tblocale VALUES("536","tet","Tetum");
INSERT INTO tblocale VALUES("537","th","Thai");
INSERT INTO tblocale VALUES("538","bo","Tibettaans");
INSERT INTO tblocale VALUES("539","tig","Tigre");
INSERT INTO tblocale VALUES("540","ti","Tigrinya");
INSERT INTO tblocale VALUES("541","tem","Timne");
INSERT INTO tblocale VALUES("542","tiv","Tiv");
INSERT INTO tblocale VALUES("543","tli","Tlingit");
INSERT INTO tblocale VALUES("544","tum","Toemboeka");
INSERT INTO tblocale VALUES("545","tpi","Tok Pisin");
INSERT INTO tblocale VALUES("546","tkl","Tokelau");
INSERT INTO tblocale VALUES("547","to","Tongaans");
INSERT INTO tblocale VALUES("548","fit","Tornedalen Finnish");
INSERT INTO tblocale VALUES("549","zh_Hant","Traditional Chinese");
INSERT INTO tblocale VALUES("550","tkr","Tsakhur");
INSERT INTO tblocale VALUES("551","tsd","Tsakonian");
INSERT INTO tblocale VALUES("552","tsi","Tsimshian");
INSERT INTO tblocale VALUES("553","cs","Tsjeggies");
INSERT INTO tblocale VALUES("554","ts","Tsonga");
INSERT INTO tblocale VALUES("555","tn","Tswana");
INSERT INTO tblocale VALUES("556","tcy","Tulu");
INSERT INTO tblocale VALUES("557","aeb","Tunisian Arabic");
INSERT INTO tblocale VALUES("558","tk","Turkmeens");
INSERT INTO tblocale VALUES("559","tr","Turks");
INSERT INTO tblocale VALUES("560","tru","Turoyo");
INSERT INTO tblocale VALUES("561","tvl","Tuvalu");
INSERT INTO tblocale VALUES("562","tyv","Tuvinian");
INSERT INTO tblocale VALUES("563","tw","Twi");
INSERT INTO tblocale VALUES("564","kcg","Tyap");
INSERT INTO tblocale VALUES("565","udm","Udmurt");
INSERT INTO tblocale VALUES("566","uga","Ugaritic");
INSERT INTO tblocale VALUES("567","ug","Uighur");
INSERT INTO tblocale VALUES("568","umb","Umbundu");
INSERT INTO tblocale VALUES("569","vai","Vai");
INSERT INTO tblocale VALUES("570","mul","Veelvuldige tale");
INSERT INTO tblocale VALUES("571","ve","Venda");
INSERT INTO tblocale VALUES("572","vec","Venetian");
INSERT INTO tblocale VALUES("573","vep","Veps");
INSERT INTO tblocale VALUES("574","vi","Viët`name`,es");
INSERT INTO tblocale VALUES("575","nl_BE","Vlaams");
INSERT INTO tblocale VALUES("576","vo","Volapük");
INSERT INTO tblocale VALUES("577","vro","Võro");
INSERT INTO tblocale VALUES("578","vot","Votic");
INSERT INTO tblocale VALUES("579","vun","Vunjo");
INSERT INTO tblocale VALUES("580","cy","Wallies");
INSERT INTO tblocale VALUES("581","wa","Walloon");
INSERT INTO tblocale VALUES("582","wae","Walser");
INSERT INTO tblocale VALUES("583","war","Waray");
INSERT INTO tblocale VALUES("584","wbp","Warlpiri");
INSERT INTO tblocale VALUES("585","was","Washo");
INSERT INTO tblocale VALUES("586","guc","Wayuu");
INSERT INTO tblocale VALUES("587","fy","Wes-Fries");
INSERT INTO tblocale VALUES("588","vls","West Flemish");
INSERT INTO tblocale VALUES("589","mrj","Western Mari");
INSERT INTO tblocale VALUES("590","be","Wit-Russies");
INSERT INTO tblocale VALUES("591","wal","Wolaytta");
INSERT INTO tblocale VALUES("592","wo","Wolof");
INSERT INTO tblocale VALUES("593","wuu","Wu Chinese");
INSERT INTO tblocale VALUES("594","xh","Xhosa");
INSERT INTO tblocale VALUES("595","hsn","Xiang Chinese");
INSERT INTO tblocale VALUES("596","yav","Yangben");
INSERT INTO tblocale VALUES("597","yao","Yao");
INSERT INTO tblocale VALUES("598","yap","Yapese");
INSERT INTO tblocale VALUES("599","ybb","Yemba");
INSERT INTO tblocale VALUES("600","yo","Yoruba");
INSERT INTO tblocale VALUES("601","is","Yslands");
INSERT INTO tblocale VALUES("602","zap","Zapotec");
INSERT INTO tblocale VALUES("603","dje","Zarma");
INSERT INTO tblocale VALUES("604","zza","Zaza");
INSERT INTO tblocale VALUES("605","zea","Zeelandic");
INSERT INTO tblocale VALUES("606","zen","Zenaga");
INSERT INTO tblocale VALUES("607","za","Zhuang");
INSERT INTO tblocale VALUES("608","zu","Zoeloe");
INSERT INTO tblocale VALUES("609","gbz","Zoroastrian Dari");
INSERT INTO tblocale VALUES("610","zun","Zuni");



DROP TABLE IF EXISTS tblocation;

CREATE TABLE `tblocation` (
  `idLocation` int(4) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  PRIMARY KEY (`idLocation`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

INSERT INTO tblocation VALUES("1","Head Office");
INSERT INTO tblocation VALUES("2","Branch Office");
INSERT INTO tblocation VALUES("3","Warehouse");



DROP TABLE IF EXISTS tblogos;

CREATE TABLE `tblogos` (
  `idlogo` int(4) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `height` int(4) NOT NULL,
  `width` int(4) NOT NULL,
  `file_name` varchar(50) NOT NULL,
  PRIMARY KEY (`idlogo`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;




DROP TABLE IF EXISTS tbmodule;

CREATE TABLE `tbmodule` (
  `idmodule` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `index` int(11) DEFAULT NULL,
  `status` char(1) NOT NULL DEFAULT 'A',
  `path` varchar(20) DEFAULT NULL,
  `smarty` varchar(50) DEFAULT NULL,
  `class` varchar(50) DEFAULT NULL,
  `headerlogo` varchar(255) DEFAULT NULL,
  `reportslogo` varchar(255) DEFAULT NULL,
  `tableprefix` char(3) DEFAULT NULL,
  `defaultmodule` char(4) DEFAULT NULL,
  PRIMARY KEY (`idmodule`),
  KEY `defaultmodule` (`defaultmodule`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

INSERT INTO tbmodule VALUES("1","Admin","0","A","admin","adm_Navbar_name","","adm_header.png","","","");
INSERT INTO tbmodule VALUES("2","Helpdezk","0","A","helpdezk","hdk_Navbar_name","","","","hdk","YES");
INSERT INTO tbmodule VALUES("3","Dashboard","0","I","dsn","DSN_Navbar_name","","","","","");



DROP TABLE IF EXISTS tbnaturalperson;

CREATE TABLE `tbnaturalperson` (
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
) ENGINE=InnoDB AUTO_INCREMENT=955 DEFAULT CHARSET=latin1;

INSERT INTO tbnaturalperson VALUES("940","1061","03507219042","","","1997-09-25","","","M");
INSERT INTO tbnaturalperson VALUES("954","1078","","","","0000-00-00","","","");



DROP TABLE IF EXISTS tbnatureperson;

CREATE TABLE `tbnatureperson` (
  `idnatureperson` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL COMMENT 'fisica ou juridica\n',
  PRIMARY KEY (`idnatureperson`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

INSERT INTO tbnatureperson VALUES("1","natural");
INSERT INTO tbnatureperson VALUES("2","juridical");



DROP TABLE IF EXISTS tbneighborhood;

CREATE TABLE `tbneighborhood` (
  `idneighborhood` int(11) NOT NULL AUTO_INCREMENT,
  `idcity` int(11) NOT NULL,
  `name` varchar(60) DEFAULT NULL,
  PRIMARY KEY (`idneighborhood`),
  KEY `fk_tbneighborhood_tbcity1` (`idcity`),
  KEY `idx_1` (`name`),
  CONSTRAINT `fk_tbneighborhood_tbcity1` FOREIGN KEY (`idcity`) REFERENCES `tbcity` (`idcity`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=93 DEFAULT CHARSET=latin1;

INSERT INTO tbneighborhood VALUES("1","1","Choose");



DROP TABLE IF EXISTS tbpermission;

CREATE TABLE `tbpermission` (
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;




DROP TABLE IF EXISTS tbperson;

CREATE TABLE `tbperson` (
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
  `change_pass` int(1) DEFAULT 0,
  `token` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`idperson`),
  KEY `fk_tbperson_tblogintype` (`idtypelogin`),
  KEY `fk_tbperson_tbtypeperson1` (`idtypeperson`),
  KEY `fk_tbperson_tbtheme1` (`idtheme`),
  KEY `idx_tbperson_name` (`name`),
  KEY `fk_tbperson_tbnatureperson1` (`idnatureperson`),
  KEY `idx_1` (`login`),
  KEY `fk_person_location` (`cod_location`),
  CONSTRAINT `fk_person_location` FOREIGN KEY (`cod_location`) REFERENCES `tblocation` (`idLocation`),
  CONSTRAINT `fk_tbperson_tblogintype` FOREIGN KEY (`idtypelogin`) REFERENCES `tbtypelogin` (`idtypelogin`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_tbperson_tbnatureperson1` FOREIGN KEY (`idnatureperson`) REFERENCES `tbnatureperson` (`idnatureperson`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_tbperson_tbtheme1` FOREIGN KEY (`idtheme`) REFERENCES `tbtheme` (`idtheme`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_tbperson_tbtypeperson1` FOREIGN KEY (`idtypeperson`) REFERENCES `tbtypeperson` (`idtypeperson`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1079 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

INSERT INTO tbperson VALUES("1","3","1","1","1","HD Admin","admin","81dc9bdb52d04dc20036dbd8313ed055","demo@helpdezk.cc","2011-08-03 18:52:31","A","N","","","","","","0","0","0","");
INSERT INTO tbperson VALUES("1038","3","4","2","1","Demo Company","","81dc9bdb52d04dc20036dbd8313ed055","demo@helpdezk.cc","2019-10-18 13:44:37","A","N","5331994000","","","","","","","0","");
INSERT INTO tbperson VALUES("1054","3","6","1","1","IT - Infrastructure","","81dc9bdb52d04dc20036dbd8313ed055","demo@helpdezk.cc","0000-00-00 00:00:00","A","N","","","","","","0","0","0","");
INSERT INTO tbperson VALUES("1055","3","6","1","1","NOC - Network Operations Center","","81dc9bdb52d04dc20036dbd8313ed055","demo@helpdezk.cc","0000-00-00 00:00:00","A","N","","","","","","0","0","0","");
INSERT INTO tbperson VALUES("1058","3","6","1","1","HR - Human Resources","","81dc9bdb52d04dc20036dbd8313ed055","demo@helpdezk.cc","0000-00-00 00:00:00","A","N","","","","","","0","0","0","");
INSERT INTO tbperson VALUES("1059","3","6","1","1","Marketing","","81dc9bdb52d04dc20036dbd8313ed055","demo@helpdezk.cc","0000-00-00 00:00:00","A","N","","","","","","0","0","0","");
INSERT INTO tbperson VALUES("1060","3","6","1","1","Hardware Handling","","81dc9bdb52d04dc20036dbd8313ed055","demo@helpdezk.cc","0000-00-00 00:00:00","A","N","","","","","","0","0","0","");
INSERT INTO tbperson VALUES("1061","3","3","1","1","Demo Operator","operator","81dc9bdb52d04dc20036dbd8313ed055","demo@helpdezk.cc","2019-12-05 17:24:25","A","N","","984014872","","","","0","0","0","");
INSERT INTO tbperson VALUES("1073","3","6","1","1","Merchandising Operations","","81dc9bdb52d04dc20036dbd8313ed055","demo@helpdezk.cc","0000-00-00 00:00:00","A","N","","","","","","0","0","0","");
INSERT INTO tbperson VALUES("1074","3","6","1","1","Software Handling","","81dc9bdb52d04dc20036dbd8313ed055","demo@helpdezk.cc","0000-00-00 00:00:00","A","N","","","","","","0","0","0","");
INSERT INTO tbperson VALUES("1078","3","2","1","1","Demo User","user","81dc9bdb52d04dc20036dbd8313ed055","demo@helpdezk.cc","2019-12-25 21:41:04","A","N","","","","","1","0","0","0","");



DROP TABLE IF EXISTS tbpersontypes;

CREATE TABLE `tbpersontypes` (
  `idperson` int(11) NOT NULL,
  `idtypeperson` int(11) NOT NULL,
  PRIMARY KEY (`idperson`,`idtypeperson`),
  KEY `idtypeperson` (`idtypeperson`),
  CONSTRAINT `tbpersontypes_ibfk_1` FOREIGN KEY (`idperson`) REFERENCES `tbperson` (`idperson`),
  CONSTRAINT `tbpersontypes_ibfk_2` FOREIGN KEY (`idtypeperson`) REFERENCES `tbtypeperson` (`idtypeperson`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;




DROP TABLE IF EXISTS tbperson_plus;

CREATE TABLE `tbperson_plus` (
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;




DROP TABLE IF EXISTS tbprogram;

CREATE TABLE `tbprogram` (
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
) ENGINE=InnoDB AUTO_INCREMENT=82 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

INSERT INTO tbprogram VALUES("1","People & Companies","person/","1","0","A","pgr_people");
INSERT INTO tbprogram VALUES("2","Holidays","holidays/","1","0","A","pgr_holidays");
INSERT INTO tbprogram VALUES("3","Programs","program/","1","0","A","pgr_programs");
INSERT INTO tbprogram VALUES("4","Modules","modules/","1","0","A","pgr_modules");
INSERT INTO tbprogram VALUES("5","Status","status/","2","0","A","pgr_status");
INSERT INTO tbprogram VALUES("6","Priority","priority/","2","0","A","pgr_priority");
INSERT INTO tbprogram VALUES("7","Groups","hdkGroup","2","0","A","pgr_groups");
INSERT INTO tbprogram VALUES("8","Evaluation","evaluation/","2","0","A","pgr_evaluation");
INSERT INTO tbprogram VALUES("9","Departments","hdkDepartment","2","0","A","pgr_departments");
INSERT INTO tbprogram VALUES("10","Cost Center","costcenter/","2","0","A","pgr_cost_center");
INSERT INTO tbprogram VALUES("12","Services","hdkService","2","0","A","pgr_services");
INSERT INTO tbprogram VALUES("16","Request Reason","hdkReason","2","0","A","pgr_req_reason");
INSERT INTO tbprogram VALUES("23","Email Configuration","hdkEmailConfig","2","0","A","pgr_email_config");
INSERT INTO tbprogram VALUES("25","System Features","features/","2","0","A","pgr_sys_features");
INSERT INTO tbprogram VALUES("32","Type Person Permission","typepersonpermission","1","0","A","pgr_type_permission");
INSERT INTO tbprogram VALUES("51","Person Report","relPessoa/","13","0","A","pgr_person_report");
INSERT INTO tbprogram VALUES("59","Downloads","downloads/","1","0","A","pgr_downloads");
INSERT INTO tbprogram VALUES("61","Logos","logos/","17","0","A","pgr_logos");
INSERT INTO tbprogram VALUES("64","Requests Report","relRequests","13","0","A","pgr_req_reports");
INSERT INTO tbprogram VALUES("65","Importar catalogo de serviÃ§os","importservices/","2","0","A","pgr_import_services");
INSERT INTO tbprogram VALUES("66","Operator Average ResponseTime","relOpeAverRespTime","13","0","A","pgr_ope_aver_resptime");
INSERT INTO tbprogram VALUES("67","Reject Requests","relReject","13","0","A","pgr_rejects_request");
INSERT INTO tbprogram VALUES("68","Requests by Department","relDepartments","13","0","A","pgr_request_department");
INSERT INTO tbprogram VALUES("69","Requests by Status","relStatus","13","0","A","pgr_request_status");
INSERT INTO tbprogram VALUES("70","Summarized by Department","relSumDepartment","13","0","A","pgr_summarized_department");
INSERT INTO tbprogram VALUES("71","Summarized by Operator","relOperator","13","0","A","pgr_summarized_operator");
INSERT INTO tbprogram VALUES("72","User Satisfaction","relUserSatisfaction","13","0","A","pgr_user_satisfaction");
INSERT INTO tbprogram VALUES("73","Warnings","hdkWarning/","2","0","A","pgr_warnings");
INSERT INTO tbprogram VALUES("74","Widgets","widget/","19","0","A","pgr_dash_widgets");
INSERT INTO tbprogram VALUES("75","CalendÃ¡rio de Trabalho","workcalendar/","1","","A","pgr_work_calendar");
INSERT INTO tbprogram VALUES("76","Improt People","importpeople/","1","","A","pgr_import_people");
INSERT INTO tbprogram VALUES("77","SolicitaÃ§Ãµes por Atendentes","relRequestsOperator/","13","","A","pgr_request_operator");
INSERT INTO tbprogram VALUES("78","SolicitaÃ§Ãµes Trabalhadas","relWorkedRequests","13","","A","pgr_worked_requests");
INSERT INTO tbprogram VALUES("79","Solicitacoes por Email","hdkRequestEmail/","2","","A","pgr_email_request");
INSERT INTO tbprogram VALUES("80","System Features","features","17","","A","pgr_sys_features");
INSERT INTO tbprogram VALUES("81","Vocabulary ","vocabulary","1","","A","pgr_vocabulary");



DROP TABLE IF EXISTS tbprogramcategory;

CREATE TABLE `tbprogramcategory` (
  `idprogramcategory` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `idmodule` int(11) NOT NULL,
  `smarty` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`idprogramcategory`),
  KEY `FK_tbprogramcategory` (`idmodule`),
  CONSTRAINT `FK_tbprogramcategory` FOREIGN KEY (`idmodule`) REFERENCES `tbmodule` (`idmodule`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

INSERT INTO tbprogramcategory VALUES("1","Records","1","cat_records");
INSERT INTO tbprogramcategory VALUES("2","Records","2","cat_records");
INSERT INTO tbprogramcategory VALUES("13","Reports","2","cat_reports");
INSERT INTO tbprogramcategory VALUES("17","Config","1","cat_config");
INSERT INTO tbprogramcategory VALUES("18","Database","2","cat_database");
INSERT INTO tbprogramcategory VALUES("19","Records","3","cat_records");



DROP TABLE IF EXISTS tbscreen;

CREATE TABLE `tbscreen` (
  `idscreen` int(11) NOT NULL AUTO_INCREMENT,
  `idmodule` int(11) DEFAULT NULL,
  `formid` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`idscreen`),
  UNIQUE KEY `UK_tbscreen` (`idmodule`,`formid`),
  CONSTRAINT `FK_tbscreen_tbmodule_idmodule` FOREIGN KEY (`idmodule`) REFERENCES `tbmodule` (`idmodule`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 AVG_ROW_LENGTH=16384;

INSERT INTO tbscreen VALUES("1","2","persondata_form","Scrren used to update person data by user");



DROP TABLE IF EXISTS tbscreen_permission;

CREATE TABLE `tbscreen_permission` (
  `idscreenpermission` int(11) NOT NULL AUTO_INCREMENT,
  `idscreen` int(11) DEFAULT NULL,
  `idtypeperson` int(11) DEFAULT NULL,
  `fieldid` varchar(50) NOT NULL,
  `enable` char(1) NOT NULL DEFAULT 'Y',
  PRIMARY KEY (`idscreenpermission`),
  KEY `FK_tbscreen_permission_tbscreen_idscreen` (`idscreen`),
  KEY `FK_tbscreen_permission_tbtypeperson_idtypeperson` (`idtypeperson`),
  CONSTRAINT `FK_tbscreen_permission_tbscreen_idscreen` FOREIGN KEY (`idscreen`) REFERENCES `tbscreen` (`idscreen`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_tbscreen_permission_tbtypeperson_idtypeperson` FOREIGN KEY (`idtypeperson`) REFERENCES `tbtypeperson` (`idtypeperson`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 AVG_ROW_LENGTH=5461;

INSERT INTO tbscreen_permission VALUES("1","1","2","person_name","N");
INSERT INTO tbscreen_permission VALUES("2","1","2","ssn_cpf","Y");
INSERT INTO tbscreen_permission VALUES("3","1","2","person_cellphone","Y");



DROP TABLE IF EXISTS tbstate;

CREATE TABLE `tbstate` (
  `idstate` int(11) NOT NULL AUTO_INCREMENT,
  `idcountry` int(11) NOT NULL,
  `abbr` varchar(15) DEFAULT NULL,
  `name` varchar(60) DEFAULT NULL,
  PRIMARY KEY (`idstate`),
  KEY `fk_tbstate_tbcountry1` (`idcountry`),
  CONSTRAINT `fk_tbstate_tbcountry1` FOREIGN KEY (`idcountry`) REFERENCES `tbcountry` (`idcountry`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1099 DEFAULT CHARSET=latin1;

INSERT INTO tbstate VALUES("1","1","AA","Choose");
INSERT INTO tbstate VALUES("1098","227","AL","Alabama");



DROP TABLE IF EXISTS tbstreet;

CREATE TABLE `tbstreet` (
  `idstreet` int(11) NOT NULL AUTO_INCREMENT,
  `idtypestreet` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`idstreet`),
  KEY `fk_tbstreet_tbtypestreet1` (`idtypestreet`),
  CONSTRAINT `fk_tbstreet_tbtypestreet1` FOREIGN KEY (`idtypestreet`) REFERENCES `tbtypestreet` (`idtypestreet`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=latin1;

INSERT INTO tbstreet VALUES("1","1","Choose");
INSERT INTO tbstreet VALUES("60","309","Bennett Dr");



DROP TABLE IF EXISTS tbteste;

CREATE TABLE `tbteste` (
  `idex` int(4) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `age` int(3) NOT NULL,
  PRIMARY KEY (`idex`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;

INSERT INTO tbteste VALUES("1","teste 2","123");
INSERT INTO tbteste VALUES("2","Alejandro","20");
INSERT INTO tbteste VALUES("3","Deivisson","26");
INSERT INTO tbteste VALUES("5","Fulano 5","54");



DROP TABLE IF EXISTS tbtheme;

CREATE TABLE `tbtheme` (
  `idtheme` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`idtheme`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

INSERT INTO tbtheme VALUES("1","Mq Theme");
INSERT INTO tbtheme VALUES("2","Usbs Theme");



DROP TABLE IF EXISTS tbtracker;

CREATE TABLE `tbtracker` (
  `idtracker` int(11) NOT NULL AUTO_INCREMENT,
  `idemail` int(11) NOT NULL,
  `campaign` varchar(255) DEFAULT NULL,
  `ipaddress` varchar(39) DEFAULT NULL,
  `php_self` varchar(255) DEFAULT NULL COMMENT 'The filename of the currently executing script, relative to the document root',
  `http_user_agent` varchar(255) DEFAULT NULL COMMENT 'This is a string denoting the user agent being which is accessing the page',
  `http_referer` varchar(255) DEFAULT NULL COMMENT 'The address of the page (if any) which referred the user agent to the current page.',
  `request_uri` varchar(255) DEFAULT NULL COMMENT 'The URI which was given in order to access this page',
  `request_time` int(11) DEFAULT NULL COMMENT 'The timestamp of the start of the request',
  `date` datetime DEFAULT NULL,
  PRIMARY KEY (`idtracker`),
  KEY `IDX_tbtracker_date` (`date`),
  KEY `IDX_tbtracker_campaign` (`campaign`),
  KEY `FK_tbtracker` (`idemail`),
  CONSTRAINT `FK_tbtracker` FOREIGN KEY (`idemail`) REFERENCES `tbemail` (`idemail`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;




DROP TABLE IF EXISTS tbtypeaddress;

CREATE TABLE `tbtypeaddress` (
  `idtypeaddress` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`idtypeaddress`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

INSERT INTO tbtypeaddress VALUES("1","Choose");
INSERT INTO tbtypeaddress VALUES("2","residential");
INSERT INTO tbtypeaddress VALUES("3","commercial");



DROP TABLE IF EXISTS tbtypelogin;

CREATE TABLE `tbtypelogin` (
  `idtypelogin` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`idtypelogin`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

INSERT INTO tbtypelogin VALUES("1","POP");
INSERT INTO tbtypelogin VALUES("2","AD");
INSERT INTO tbtypelogin VALUES("3","HD");
INSERT INTO tbtypelogin VALUES("4","REQUEST");



DROP TABLE IF EXISTS tbtypeperson;

CREATE TABLE `tbtypeperson` (
  `idtypeperson` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  `permissiongroup` char(1) DEFAULT 'N',
  PRIMARY KEY (`idtypeperson`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

INSERT INTO tbtypeperson VALUES("1","admin","N");
INSERT INTO tbtypeperson VALUES("2","user","N");
INSERT INTO tbtypeperson VALUES("3","operator","N");
INSERT INTO tbtypeperson VALUES("4","costumer","N");
INSERT INTO tbtypeperson VALUES("5","partner","N");
INSERT INTO tbtypeperson VALUES("6","group","N");



DROP TABLE IF EXISTS tbtypepersonpermission;

CREATE TABLE `tbtypepersonpermission` (
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
) ENGINE=InnoDB AUTO_INCREMENT=1031 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

INSERT INTO tbtypepersonpermission VALUES("1","1","1","1","Y");
INSERT INTO tbtypepersonpermission VALUES("2","1","2","1","N");
INSERT INTO tbtypepersonpermission VALUES("3","1","3","1","Y");
INSERT INTO tbtypepersonpermission VALUES("4","1","4","1","N");
INSERT INTO tbtypepersonpermission VALUES("5","1","5","1","N");
INSERT INTO tbtypepersonpermission VALUES("6","1","1","2","Y");
INSERT INTO tbtypepersonpermission VALUES("7","1","2","2","N");
INSERT INTO tbtypepersonpermission VALUES("8","1","3","2","Y");
INSERT INTO tbtypepersonpermission VALUES("9","1","4","2","N");
INSERT INTO tbtypepersonpermission VALUES("10","1","5","2","N");
INSERT INTO tbtypepersonpermission VALUES("11","1","1","3","Y");
INSERT INTO tbtypepersonpermission VALUES("12","1","2","3","N");
INSERT INTO tbtypepersonpermission VALUES("13","1","3","3","Y");
INSERT INTO tbtypepersonpermission VALUES("14","1","4","3","N");
INSERT INTO tbtypepersonpermission VALUES("15","1","5","3","N");
INSERT INTO tbtypepersonpermission VALUES("16","1","1","4","Y");
INSERT INTO tbtypepersonpermission VALUES("17","1","2","4","N");
INSERT INTO tbtypepersonpermission VALUES("18","1","3","4","N");
INSERT INTO tbtypepersonpermission VALUES("19","1","4","4","N");
INSERT INTO tbtypepersonpermission VALUES("20","1","5","4","N");
INSERT INTO tbtypepersonpermission VALUES("21","2","1","1","Y");
INSERT INTO tbtypepersonpermission VALUES("22","2","2","1","N");
INSERT INTO tbtypepersonpermission VALUES("23","2","3","1","N");
INSERT INTO tbtypepersonpermission VALUES("24","2","4","1","N");
INSERT INTO tbtypepersonpermission VALUES("25","2","5","1","N");
INSERT INTO tbtypepersonpermission VALUES("26","2","1","2","Y");
INSERT INTO tbtypepersonpermission VALUES("27","2","2","2","N");
INSERT INTO tbtypepersonpermission VALUES("28","2","3","2","N");
INSERT INTO tbtypepersonpermission VALUES("29","2","4","2","N");
INSERT INTO tbtypepersonpermission VALUES("30","2","5","2","N");
INSERT INTO tbtypepersonpermission VALUES("31","2","1","3","Y");
INSERT INTO tbtypepersonpermission VALUES("32","2","2","3","N");
INSERT INTO tbtypepersonpermission VALUES("33","2","3","3","N");
INSERT INTO tbtypepersonpermission VALUES("34","2","4","3","N");
INSERT INTO tbtypepersonpermission VALUES("35","2","5","3","N");
INSERT INTO tbtypepersonpermission VALUES("36","2","1","4","Y");
INSERT INTO tbtypepersonpermission VALUES("37","2","2","4","N");
INSERT INTO tbtypepersonpermission VALUES("38","2","3","4","N");
INSERT INTO tbtypepersonpermission VALUES("39","2","4","4","N");
INSERT INTO tbtypepersonpermission VALUES("40","2","5","4","N");
INSERT INTO tbtypepersonpermission VALUES("41","3","1","1","Y");
INSERT INTO tbtypepersonpermission VALUES("42","3","2","1","N");
INSERT INTO tbtypepersonpermission VALUES("43","3","3","1","N");
INSERT INTO tbtypepersonpermission VALUES("44","3","4","1","N");
INSERT INTO tbtypepersonpermission VALUES("45","3","5","1","N");
INSERT INTO tbtypepersonpermission VALUES("46","3","1","2","Y");
INSERT INTO tbtypepersonpermission VALUES("47","3","2","2","N");
INSERT INTO tbtypepersonpermission VALUES("48","3","3","2","N");
INSERT INTO tbtypepersonpermission VALUES("49","3","4","2","N");
INSERT INTO tbtypepersonpermission VALUES("50","3","5","2","N");
INSERT INTO tbtypepersonpermission VALUES("51","3","1","3","Y");
INSERT INTO tbtypepersonpermission VALUES("52","3","2","3","N");
INSERT INTO tbtypepersonpermission VALUES("53","3","3","3","N");
INSERT INTO tbtypepersonpermission VALUES("54","3","4","3","N");
INSERT INTO tbtypepersonpermission VALUES("55","3","5","3","N");
INSERT INTO tbtypepersonpermission VALUES("56","3","1","4","Y");
INSERT INTO tbtypepersonpermission VALUES("57","3","2","4","N");
INSERT INTO tbtypepersonpermission VALUES("58","3","3","4","N");
INSERT INTO tbtypepersonpermission VALUES("59","3","4","4","N");
INSERT INTO tbtypepersonpermission VALUES("60","3","5","4","N");
INSERT INTO tbtypepersonpermission VALUES("61","4","1","1","Y");
INSERT INTO tbtypepersonpermission VALUES("62","4","2","1","N");
INSERT INTO tbtypepersonpermission VALUES("63","4","3","1","N");
INSERT INTO tbtypepersonpermission VALUES("64","4","4","1","N");
INSERT INTO tbtypepersonpermission VALUES("65","4","5","1","N");
INSERT INTO tbtypepersonpermission VALUES("66","4","1","2","Y");
INSERT INTO tbtypepersonpermission VALUES("67","4","2","2","N");
INSERT INTO tbtypepersonpermission VALUES("68","4","3","2","N");
INSERT INTO tbtypepersonpermission VALUES("69","4","4","2","N");
INSERT INTO tbtypepersonpermission VALUES("70","4","5","2","N");
INSERT INTO tbtypepersonpermission VALUES("71","4","1","3","Y");
INSERT INTO tbtypepersonpermission VALUES("72","4","2","3","N");
INSERT INTO tbtypepersonpermission VALUES("73","4","3","3","N");
INSERT INTO tbtypepersonpermission VALUES("74","4","4","3","N");
INSERT INTO tbtypepersonpermission VALUES("75","4","5","3","N");
INSERT INTO tbtypepersonpermission VALUES("76","4","1","4","Y");
INSERT INTO tbtypepersonpermission VALUES("77","4","2","4","N");
INSERT INTO tbtypepersonpermission VALUES("78","4","3","4","N");
INSERT INTO tbtypepersonpermission VALUES("79","4","4","4","N");
INSERT INTO tbtypepersonpermission VALUES("80","4","5","4","N");
INSERT INTO tbtypepersonpermission VALUES("81","5","1","1","Y");
INSERT INTO tbtypepersonpermission VALUES("82","5","2","1","N");
INSERT INTO tbtypepersonpermission VALUES("83","5","3","1","N");
INSERT INTO tbtypepersonpermission VALUES("84","5","4","1","N");
INSERT INTO tbtypepersonpermission VALUES("85","5","5","1","N");
INSERT INTO tbtypepersonpermission VALUES("86","5","1","2","Y");
INSERT INTO tbtypepersonpermission VALUES("87","5","2","2","N");
INSERT INTO tbtypepersonpermission VALUES("88","5","3","2","N");
INSERT INTO tbtypepersonpermission VALUES("89","5","4","2","N");
INSERT INTO tbtypepersonpermission VALUES("90","5","5","2","N");
INSERT INTO tbtypepersonpermission VALUES("91","5","1","3","Y");
INSERT INTO tbtypepersonpermission VALUES("92","5","2","3","N");
INSERT INTO tbtypepersonpermission VALUES("93","5","3","3","N");
INSERT INTO tbtypepersonpermission VALUES("94","5","4","3","N");
INSERT INTO tbtypepersonpermission VALUES("95","5","5","3","N");
INSERT INTO tbtypepersonpermission VALUES("96","5","1","4","Y");
INSERT INTO tbtypepersonpermission VALUES("97","5","2","4","N");
INSERT INTO tbtypepersonpermission VALUES("98","5","3","4","N");
INSERT INTO tbtypepersonpermission VALUES("99","5","4","4","N");
INSERT INTO tbtypepersonpermission VALUES("100","5","5","4","N");
INSERT INTO tbtypepersonpermission VALUES("101","6","1","1","Y");
INSERT INTO tbtypepersonpermission VALUES("102","6","2","1","N");
INSERT INTO tbtypepersonpermission VALUES("103","6","3","1","N");
INSERT INTO tbtypepersonpermission VALUES("104","6","4","1","N");
INSERT INTO tbtypepersonpermission VALUES("105","6","5","1","N");
INSERT INTO tbtypepersonpermission VALUES("106","6","1","2","Y");
INSERT INTO tbtypepersonpermission VALUES("107","6","2","2","N");
INSERT INTO tbtypepersonpermission VALUES("108","6","3","2","N");
INSERT INTO tbtypepersonpermission VALUES("109","6","4","2","N");
INSERT INTO tbtypepersonpermission VALUES("110","6","5","2","N");
INSERT INTO tbtypepersonpermission VALUES("111","6","1","3","Y");
INSERT INTO tbtypepersonpermission VALUES("112","6","2","3","N");
INSERT INTO tbtypepersonpermission VALUES("113","6","3","3","N");
INSERT INTO tbtypepersonpermission VALUES("114","6","4","3","N");
INSERT INTO tbtypepersonpermission VALUES("115","6","5","3","N");
INSERT INTO tbtypepersonpermission VALUES("116","6","1","4","Y");
INSERT INTO tbtypepersonpermission VALUES("117","6","2","4","N");
INSERT INTO tbtypepersonpermission VALUES("118","6","3","4","N");
INSERT INTO tbtypepersonpermission VALUES("119","6","4","4","N");
INSERT INTO tbtypepersonpermission VALUES("120","6","5","4","N");
INSERT INTO tbtypepersonpermission VALUES("121","7","1","1","Y");
INSERT INTO tbtypepersonpermission VALUES("122","7","2","1","N");
INSERT INTO tbtypepersonpermission VALUES("123","7","3","1","N");
INSERT INTO tbtypepersonpermission VALUES("124","7","4","1","N");
INSERT INTO tbtypepersonpermission VALUES("125","7","5","1","N");
INSERT INTO tbtypepersonpermission VALUES("126","7","1","2","Y");
INSERT INTO tbtypepersonpermission VALUES("127","7","2","2","N");
INSERT INTO tbtypepersonpermission VALUES("128","7","3","2","N");
INSERT INTO tbtypepersonpermission VALUES("129","7","4","2","N");
INSERT INTO tbtypepersonpermission VALUES("130","7","5","2","N");
INSERT INTO tbtypepersonpermission VALUES("131","7","1","3","Y");
INSERT INTO tbtypepersonpermission VALUES("132","7","2","3","N");
INSERT INTO tbtypepersonpermission VALUES("133","7","3","3","N");
INSERT INTO tbtypepersonpermission VALUES("134","7","4","3","N");
INSERT INTO tbtypepersonpermission VALUES("135","7","5","3","N");
INSERT INTO tbtypepersonpermission VALUES("136","7","1","4","Y");
INSERT INTO tbtypepersonpermission VALUES("137","7","2","4","N");
INSERT INTO tbtypepersonpermission VALUES("138","7","3","4","N");
INSERT INTO tbtypepersonpermission VALUES("139","7","4","4","N");
INSERT INTO tbtypepersonpermission VALUES("140","7","5","4","N");
INSERT INTO tbtypepersonpermission VALUES("141","8","1","1","Y");
INSERT INTO tbtypepersonpermission VALUES("142","8","2","1","N");
INSERT INTO tbtypepersonpermission VALUES("143","8","3","1","N");
INSERT INTO tbtypepersonpermission VALUES("144","8","4","1","N");
INSERT INTO tbtypepersonpermission VALUES("145","8","5","1","N");
INSERT INTO tbtypepersonpermission VALUES("146","8","1","2","Y");
INSERT INTO tbtypepersonpermission VALUES("147","8","2","2","N");
INSERT INTO tbtypepersonpermission VALUES("148","8","3","2","N");
INSERT INTO tbtypepersonpermission VALUES("149","8","4","2","N");
INSERT INTO tbtypepersonpermission VALUES("150","8","5","2","N");
INSERT INTO tbtypepersonpermission VALUES("151","8","1","3","Y");
INSERT INTO tbtypepersonpermission VALUES("152","8","2","3","N");
INSERT INTO tbtypepersonpermission VALUES("153","8","3","3","N");
INSERT INTO tbtypepersonpermission VALUES("154","8","4","3","N");
INSERT INTO tbtypepersonpermission VALUES("155","8","5","3","N");
INSERT INTO tbtypepersonpermission VALUES("156","8","1","4","Y");
INSERT INTO tbtypepersonpermission VALUES("157","8","2","4","N");
INSERT INTO tbtypepersonpermission VALUES("158","8","3","4","N");
INSERT INTO tbtypepersonpermission VALUES("159","8","4","4","N");
INSERT INTO tbtypepersonpermission VALUES("160","8","5","4","N");
INSERT INTO tbtypepersonpermission VALUES("161","9","1","1","Y");
INSERT INTO tbtypepersonpermission VALUES("162","9","2","1","N");
INSERT INTO tbtypepersonpermission VALUES("163","9","3","1","N");
INSERT INTO tbtypepersonpermission VALUES("164","9","4","1","N");
INSERT INTO tbtypepersonpermission VALUES("165","9","5","1","N");
INSERT INTO tbtypepersonpermission VALUES("166","9","1","2","Y");
INSERT INTO tbtypepersonpermission VALUES("167","9","2","2","N");
INSERT INTO tbtypepersonpermission VALUES("168","9","3","2","N");
INSERT INTO tbtypepersonpermission VALUES("169","9","4","2","N");
INSERT INTO tbtypepersonpermission VALUES("170","9","5","2","N");
INSERT INTO tbtypepersonpermission VALUES("171","9","1","3","Y");
INSERT INTO tbtypepersonpermission VALUES("172","9","2","3","N");
INSERT INTO tbtypepersonpermission VALUES("173","9","3","3","N");
INSERT INTO tbtypepersonpermission VALUES("174","9","4","3","N");
INSERT INTO tbtypepersonpermission VALUES("175","9","5","3","N");
INSERT INTO tbtypepersonpermission VALUES("176","9","1","4","Y");
INSERT INTO tbtypepersonpermission VALUES("177","9","2","4","N");
INSERT INTO tbtypepersonpermission VALUES("178","9","3","4","N");
INSERT INTO tbtypepersonpermission VALUES("179","9","4","4","N");
INSERT INTO tbtypepersonpermission VALUES("180","9","5","4","N");
INSERT INTO tbtypepersonpermission VALUES("181","10","1","1","Y");
INSERT INTO tbtypepersonpermission VALUES("182","10","2","1","N");
INSERT INTO tbtypepersonpermission VALUES("183","10","3","1","N");
INSERT INTO tbtypepersonpermission VALUES("184","10","4","1","N");
INSERT INTO tbtypepersonpermission VALUES("185","10","5","1","N");
INSERT INTO tbtypepersonpermission VALUES("186","10","1","2","Y");
INSERT INTO tbtypepersonpermission VALUES("187","10","2","2","N");
INSERT INTO tbtypepersonpermission VALUES("188","10","3","2","N");
INSERT INTO tbtypepersonpermission VALUES("189","10","4","2","N");
INSERT INTO tbtypepersonpermission VALUES("190","10","5","2","N");
INSERT INTO tbtypepersonpermission VALUES("191","10","1","3","Y");
INSERT INTO tbtypepersonpermission VALUES("192","10","2","3","N");
INSERT INTO tbtypepersonpermission VALUES("193","10","3","3","N");
INSERT INTO tbtypepersonpermission VALUES("194","10","4","3","N");
INSERT INTO tbtypepersonpermission VALUES("195","10","5","3","N");
INSERT INTO tbtypepersonpermission VALUES("196","10","1","4","Y");
INSERT INTO tbtypepersonpermission VALUES("197","10","2","4","N");
INSERT INTO tbtypepersonpermission VALUES("198","10","3","4","N");
INSERT INTO tbtypepersonpermission VALUES("199","10","4","4","N");
INSERT INTO tbtypepersonpermission VALUES("200","10","5","4","N");
INSERT INTO tbtypepersonpermission VALUES("221","12","1","1","Y");
INSERT INTO tbtypepersonpermission VALUES("222","12","2","1","N");
INSERT INTO tbtypepersonpermission VALUES("223","12","3","1","N");
INSERT INTO tbtypepersonpermission VALUES("224","12","4","1","N");
INSERT INTO tbtypepersonpermission VALUES("225","12","5","1","N");
INSERT INTO tbtypepersonpermission VALUES("226","12","1","2","Y");
INSERT INTO tbtypepersonpermission VALUES("227","12","2","2","N");
INSERT INTO tbtypepersonpermission VALUES("228","12","3","2","N");
INSERT INTO tbtypepersonpermission VALUES("229","12","4","2","N");
INSERT INTO tbtypepersonpermission VALUES("230","12","5","2","N");
INSERT INTO tbtypepersonpermission VALUES("231","12","1","3","Y");
INSERT INTO tbtypepersonpermission VALUES("232","12","2","3","N");
INSERT INTO tbtypepersonpermission VALUES("233","12","3","3","N");
INSERT INTO tbtypepersonpermission VALUES("234","12","4","3","N");
INSERT INTO tbtypepersonpermission VALUES("235","12","5","3","N");
INSERT INTO tbtypepersonpermission VALUES("236","12","1","4","Y");
INSERT INTO tbtypepersonpermission VALUES("237","12","2","4","N");
INSERT INTO tbtypepersonpermission VALUES("238","12","3","4","N");
INSERT INTO tbtypepersonpermission VALUES("239","12","4","4","N");
INSERT INTO tbtypepersonpermission VALUES("240","12","5","4","N");
INSERT INTO tbtypepersonpermission VALUES("281","16","1","1","Y");
INSERT INTO tbtypepersonpermission VALUES("282","16","2","1","N");
INSERT INTO tbtypepersonpermission VALUES("283","16","3","1","N");
INSERT INTO tbtypepersonpermission VALUES("284","16","4","1","N");
INSERT INTO tbtypepersonpermission VALUES("285","16","5","1","N");
INSERT INTO tbtypepersonpermission VALUES("286","16","1","2","Y");
INSERT INTO tbtypepersonpermission VALUES("287","16","2","2","N");
INSERT INTO tbtypepersonpermission VALUES("288","16","3","2","N");
INSERT INTO tbtypepersonpermission VALUES("289","16","4","2","N");
INSERT INTO tbtypepersonpermission VALUES("290","16","5","2","N");
INSERT INTO tbtypepersonpermission VALUES("291","16","1","3","Y");
INSERT INTO tbtypepersonpermission VALUES("292","16","2","3","N");
INSERT INTO tbtypepersonpermission VALUES("293","16","3","3","N");
INSERT INTO tbtypepersonpermission VALUES("294","16","4","3","N");
INSERT INTO tbtypepersonpermission VALUES("295","16","5","3","N");
INSERT INTO tbtypepersonpermission VALUES("296","16","1","4","Y");
INSERT INTO tbtypepersonpermission VALUES("297","16","2","4","N");
INSERT INTO tbtypepersonpermission VALUES("298","16","3","4","N");
INSERT INTO tbtypepersonpermission VALUES("299","16","4","4","N");
INSERT INTO tbtypepersonpermission VALUES("300","16","5","4","N");
INSERT INTO tbtypepersonpermission VALUES("341","23","1","1","Y");
INSERT INTO tbtypepersonpermission VALUES("342","23","2","1","N");
INSERT INTO tbtypepersonpermission VALUES("343","23","3","1","N");
INSERT INTO tbtypepersonpermission VALUES("344","23","4","1","N");
INSERT INTO tbtypepersonpermission VALUES("345","23","5","1","N");
INSERT INTO tbtypepersonpermission VALUES("346","23","1","2","Y");
INSERT INTO tbtypepersonpermission VALUES("347","23","2","2","N");
INSERT INTO tbtypepersonpermission VALUES("348","23","3","2","N");
INSERT INTO tbtypepersonpermission VALUES("349","23","4","2","N");
INSERT INTO tbtypepersonpermission VALUES("350","23","5","2","N");
INSERT INTO tbtypepersonpermission VALUES("351","23","1","3","Y");
INSERT INTO tbtypepersonpermission VALUES("352","23","2","3","N");
INSERT INTO tbtypepersonpermission VALUES("353","23","3","3","N");
INSERT INTO tbtypepersonpermission VALUES("354","23","4","3","N");
INSERT INTO tbtypepersonpermission VALUES("355","23","5","3","N");
INSERT INTO tbtypepersonpermission VALUES("356","23","1","4","Y");
INSERT INTO tbtypepersonpermission VALUES("357","23","2","4","N");
INSERT INTO tbtypepersonpermission VALUES("358","23","3","4","N");
INSERT INTO tbtypepersonpermission VALUES("359","23","4","4","N");
INSERT INTO tbtypepersonpermission VALUES("360","23","5","4","N");
INSERT INTO tbtypepersonpermission VALUES("361","25","1","1","Y");
INSERT INTO tbtypepersonpermission VALUES("362","25","2","1","N");
INSERT INTO tbtypepersonpermission VALUES("363","25","3","1","N");
INSERT INTO tbtypepersonpermission VALUES("364","25","4","1","N");
INSERT INTO tbtypepersonpermission VALUES("365","25","5","1","N");
INSERT INTO tbtypepersonpermission VALUES("366","25","1","2","Y");
INSERT INTO tbtypepersonpermission VALUES("367","25","2","2","N");
INSERT INTO tbtypepersonpermission VALUES("368","25","3","2","N");
INSERT INTO tbtypepersonpermission VALUES("369","25","4","2","N");
INSERT INTO tbtypepersonpermission VALUES("370","25","5","2","N");
INSERT INTO tbtypepersonpermission VALUES("371","25","1","3","Y");
INSERT INTO tbtypepersonpermission VALUES("372","25","2","3","N");
INSERT INTO tbtypepersonpermission VALUES("373","25","3","3","N");
INSERT INTO tbtypepersonpermission VALUES("374","25","4","3","N");
INSERT INTO tbtypepersonpermission VALUES("375","25","5","3","N");
INSERT INTO tbtypepersonpermission VALUES("376","25","1","4","Y");
INSERT INTO tbtypepersonpermission VALUES("377","25","2","4","N");
INSERT INTO tbtypepersonpermission VALUES("378","25","3","4","N");
INSERT INTO tbtypepersonpermission VALUES("379","25","4","4","N");
INSERT INTO tbtypepersonpermission VALUES("380","25","5","4","N");
INSERT INTO tbtypepersonpermission VALUES("381","2","1","5","Y");
INSERT INTO tbtypepersonpermission VALUES("382","2","2","5","N");
INSERT INTO tbtypepersonpermission VALUES("383","2","3","5","N");
INSERT INTO tbtypepersonpermission VALUES("384","2","4","5","N");
INSERT INTO tbtypepersonpermission VALUES("385","2","5","5","N");
INSERT INTO tbtypepersonpermission VALUES("386","32","1","1","Y");
INSERT INTO tbtypepersonpermission VALUES("387","32","1","2","Y");
INSERT INTO tbtypepersonpermission VALUES("388","32","1","3","Y");
INSERT INTO tbtypepersonpermission VALUES("487","51","1","1","Y");
INSERT INTO tbtypepersonpermission VALUES("488","51","2","1","N");
INSERT INTO tbtypepersonpermission VALUES("489","51","3","1","N");
INSERT INTO tbtypepersonpermission VALUES("490","51","4","1","N");
INSERT INTO tbtypepersonpermission VALUES("491","51","5","1","N");
INSERT INTO tbtypepersonpermission VALUES("492","51","1","5","Y");
INSERT INTO tbtypepersonpermission VALUES("493","51","2","5","N");
INSERT INTO tbtypepersonpermission VALUES("494","51","3","5","N");
INSERT INTO tbtypepersonpermission VALUES("495","51","4","5","N");
INSERT INTO tbtypepersonpermission VALUES("496","51","5","5","N");
INSERT INTO tbtypepersonpermission VALUES("497","51","1","6","Y");
INSERT INTO tbtypepersonpermission VALUES("498","51","2","6","N");
INSERT INTO tbtypepersonpermission VALUES("499","51","3","6","N");
INSERT INTO tbtypepersonpermission VALUES("500","51","4","6","N");
INSERT INTO tbtypepersonpermission VALUES("501","51","5","6","N");
INSERT INTO tbtypepersonpermission VALUES("552","59","1","1","Y");
INSERT INTO tbtypepersonpermission VALUES("553","59","2","1","N");
INSERT INTO tbtypepersonpermission VALUES("554","59","3","1","N");
INSERT INTO tbtypepersonpermission VALUES("555","59","4","1","N");
INSERT INTO tbtypepersonpermission VALUES("556","59","5","1","N");
INSERT INTO tbtypepersonpermission VALUES("557","59","1","2","Y");
INSERT INTO tbtypepersonpermission VALUES("558","59","2","2","N");
INSERT INTO tbtypepersonpermission VALUES("559","59","3","2","N");
INSERT INTO tbtypepersonpermission VALUES("560","59","4","2","N");
INSERT INTO tbtypepersonpermission VALUES("561","59","5","2","N");
INSERT INTO tbtypepersonpermission VALUES("562","59","1","3","Y");
INSERT INTO tbtypepersonpermission VALUES("563","59","2","3","N");
INSERT INTO tbtypepersonpermission VALUES("564","59","3","3","N");
INSERT INTO tbtypepersonpermission VALUES("565","59","4","3","N");
INSERT INTO tbtypepersonpermission VALUES("566","59","5","3","N");
INSERT INTO tbtypepersonpermission VALUES("567","59","1","4","Y");
INSERT INTO tbtypepersonpermission VALUES("568","59","2","4","N");
INSERT INTO tbtypepersonpermission VALUES("569","59","3","4","N");
INSERT INTO tbtypepersonpermission VALUES("570","59","4","4","N");
INSERT INTO tbtypepersonpermission VALUES("571","59","5","4","N");
INSERT INTO tbtypepersonpermission VALUES("592","61","1","1","Y");
INSERT INTO tbtypepersonpermission VALUES("593","61","2","1","N");
INSERT INTO tbtypepersonpermission VALUES("594","61","3","1","N");
INSERT INTO tbtypepersonpermission VALUES("595","61","4","1","N");
INSERT INTO tbtypepersonpermission VALUES("596","61","5","1","N");
INSERT INTO tbtypepersonpermission VALUES("597","61","1","2","Y");
INSERT INTO tbtypepersonpermission VALUES("598","61","2","2","N");
INSERT INTO tbtypepersonpermission VALUES("599","61","3","2","N");
INSERT INTO tbtypepersonpermission VALUES("600","61","4","2","N");
INSERT INTO tbtypepersonpermission VALUES("601","61","5","2","N");
INSERT INTO tbtypepersonpermission VALUES("602","61","1","3","Y");
INSERT INTO tbtypepersonpermission VALUES("603","61","2","3","N");
INSERT INTO tbtypepersonpermission VALUES("604","61","3","3","N");
INSERT INTO tbtypepersonpermission VALUES("605","61","4","3","N");
INSERT INTO tbtypepersonpermission VALUES("606","61","5","3","N");
INSERT INTO tbtypepersonpermission VALUES("607","61","1","4","N");
INSERT INTO tbtypepersonpermission VALUES("608","61","2","4","N");
INSERT INTO tbtypepersonpermission VALUES("609","61","3","4","N");
INSERT INTO tbtypepersonpermission VALUES("610","61","4","4","N");
INSERT INTO tbtypepersonpermission VALUES("611","61","5","4","N");
INSERT INTO tbtypepersonpermission VALUES("660","64","1","1","Y");
INSERT INTO tbtypepersonpermission VALUES("661","64","2","1","N");
INSERT INTO tbtypepersonpermission VALUES("662","64","3","1","N");
INSERT INTO tbtypepersonpermission VALUES("663","64","4","1","N");
INSERT INTO tbtypepersonpermission VALUES("664","64","5","1","N");
INSERT INTO tbtypepersonpermission VALUES("665","64","6","1","N");
INSERT INTO tbtypepersonpermission VALUES("666","64","1","2","Y");
INSERT INTO tbtypepersonpermission VALUES("667","64","2","2","N");
INSERT INTO tbtypepersonpermission VALUES("668","64","3","2","N");
INSERT INTO tbtypepersonpermission VALUES("669","64","4","2","N");
INSERT INTO tbtypepersonpermission VALUES("670","64","5","2","N");
INSERT INTO tbtypepersonpermission VALUES("671","64","6","2","N");
INSERT INTO tbtypepersonpermission VALUES("672","64","1","3","N");
INSERT INTO tbtypepersonpermission VALUES("673","64","2","3","N");
INSERT INTO tbtypepersonpermission VALUES("674","64","3","3","N");
INSERT INTO tbtypepersonpermission VALUES("675","64","4","3","N");
INSERT INTO tbtypepersonpermission VALUES("676","64","5","3","N");
INSERT INTO tbtypepersonpermission VALUES("677","64","6","3","N");
INSERT INTO tbtypepersonpermission VALUES("678","64","1","5","Y");
INSERT INTO tbtypepersonpermission VALUES("679","64","2","5","N");
INSERT INTO tbtypepersonpermission VALUES("680","64","3","5","N");
INSERT INTO tbtypepersonpermission VALUES("681","64","4","5","N");
INSERT INTO tbtypepersonpermission VALUES("682","64","5","5","N");
INSERT INTO tbtypepersonpermission VALUES("683","64","6","5","N");
INSERT INTO tbtypepersonpermission VALUES("684","65","1","1","Y");
INSERT INTO tbtypepersonpermission VALUES("685","65","2","1","N");
INSERT INTO tbtypepersonpermission VALUES("686","65","3","1","N");
INSERT INTO tbtypepersonpermission VALUES("687","65","4","1","N");
INSERT INTO tbtypepersonpermission VALUES("688","65","5","1","N");
INSERT INTO tbtypepersonpermission VALUES("689","65","6","1","N");
INSERT INTO tbtypepersonpermission VALUES("690","65","1","2","Y");
INSERT INTO tbtypepersonpermission VALUES("691","65","2","2","N");
INSERT INTO tbtypepersonpermission VALUES("692","65","3","2","N");
INSERT INTO tbtypepersonpermission VALUES("693","65","4","2","N");
INSERT INTO tbtypepersonpermission VALUES("694","65","5","2","N");
INSERT INTO tbtypepersonpermission VALUES("695","65","6","2","N");
INSERT INTO tbtypepersonpermission VALUES("696","65","1","3","Y");
INSERT INTO tbtypepersonpermission VALUES("697","65","2","3","N");
INSERT INTO tbtypepersonpermission VALUES("698","65","3","3","N");
INSERT INTO tbtypepersonpermission VALUES("699","65","4","3","N");
INSERT INTO tbtypepersonpermission VALUES("700","65","5","3","N");
INSERT INTO tbtypepersonpermission VALUES("701","65","6","3","N");
INSERT INTO tbtypepersonpermission VALUES("702","65","1","4","Y");
INSERT INTO tbtypepersonpermission VALUES("703","65","2","4","N");
INSERT INTO tbtypepersonpermission VALUES("704","65","3","4","N");
INSERT INTO tbtypepersonpermission VALUES("705","65","4","4","N");
INSERT INTO tbtypepersonpermission VALUES("706","65","5","4","N");
INSERT INTO tbtypepersonpermission VALUES("707","65","6","4","N");
INSERT INTO tbtypepersonpermission VALUES("708","65","1","5","Y");
INSERT INTO tbtypepersonpermission VALUES("709","65","2","5","N");
INSERT INTO tbtypepersonpermission VALUES("710","65","3","5","N");
INSERT INTO tbtypepersonpermission VALUES("711","65","4","5","N");
INSERT INTO tbtypepersonpermission VALUES("712","65","5","5","N");
INSERT INTO tbtypepersonpermission VALUES("713","65","6","5","N");
INSERT INTO tbtypepersonpermission VALUES("714","66","1","1","Y");
INSERT INTO tbtypepersonpermission VALUES("715","66","2","1","N");
INSERT INTO tbtypepersonpermission VALUES("716","66","3","1","N");
INSERT INTO tbtypepersonpermission VALUES("717","66","4","1","N");
INSERT INTO tbtypepersonpermission VALUES("718","66","5","1","N");
INSERT INTO tbtypepersonpermission VALUES("719","66","6","1","N");
INSERT INTO tbtypepersonpermission VALUES("720","66","1","2","N");
INSERT INTO tbtypepersonpermission VALUES("721","66","2","2","N");
INSERT INTO tbtypepersonpermission VALUES("722","66","3","2","N");
INSERT INTO tbtypepersonpermission VALUES("723","66","4","2","N");
INSERT INTO tbtypepersonpermission VALUES("724","66","5","2","N");
INSERT INTO tbtypepersonpermission VALUES("725","66","6","2","N");
INSERT INTO tbtypepersonpermission VALUES("726","66","1","5","Y");
INSERT INTO tbtypepersonpermission VALUES("727","66","2","5","N");
INSERT INTO tbtypepersonpermission VALUES("728","66","3","5","N");
INSERT INTO tbtypepersonpermission VALUES("729","66","4","5","N");
INSERT INTO tbtypepersonpermission VALUES("730","66","5","5","N");
INSERT INTO tbtypepersonpermission VALUES("731","66","6","5","N");
INSERT INTO tbtypepersonpermission VALUES("732","67","1","1","Y");
INSERT INTO tbtypepersonpermission VALUES("733","67","2","1","N");
INSERT INTO tbtypepersonpermission VALUES("734","67","3","1","N");
INSERT INTO tbtypepersonpermission VALUES("735","67","4","1","N");
INSERT INTO tbtypepersonpermission VALUES("736","67","5","1","N");
INSERT INTO tbtypepersonpermission VALUES("737","67","6","1","N");
INSERT INTO tbtypepersonpermission VALUES("738","67","1","2","N");
INSERT INTO tbtypepersonpermission VALUES("739","67","2","2","N");
INSERT INTO tbtypepersonpermission VALUES("740","67","3","2","N");
INSERT INTO tbtypepersonpermission VALUES("741","67","4","2","N");
INSERT INTO tbtypepersonpermission VALUES("742","67","5","2","N");
INSERT INTO tbtypepersonpermission VALUES("743","67","6","2","N");
INSERT INTO tbtypepersonpermission VALUES("744","67","1","5","Y");
INSERT INTO tbtypepersonpermission VALUES("745","67","2","5","N");
INSERT INTO tbtypepersonpermission VALUES("746","67","3","5","N");
INSERT INTO tbtypepersonpermission VALUES("747","67","4","5","N");
INSERT INTO tbtypepersonpermission VALUES("748","67","5","5","N");
INSERT INTO tbtypepersonpermission VALUES("749","67","6","5","N");
INSERT INTO tbtypepersonpermission VALUES("750","68","1","1","Y");
INSERT INTO tbtypepersonpermission VALUES("751","68","2","1","N");
INSERT INTO tbtypepersonpermission VALUES("752","68","3","1","N");
INSERT INTO tbtypepersonpermission VALUES("753","68","4","1","N");
INSERT INTO tbtypepersonpermission VALUES("754","68","5","1","N");
INSERT INTO tbtypepersonpermission VALUES("755","68","6","1","N");
INSERT INTO tbtypepersonpermission VALUES("756","68","1","2","N");
INSERT INTO tbtypepersonpermission VALUES("757","68","2","2","N");
INSERT INTO tbtypepersonpermission VALUES("758","68","3","2","N");
INSERT INTO tbtypepersonpermission VALUES("759","68","4","2","N");
INSERT INTO tbtypepersonpermission VALUES("760","68","5","2","N");
INSERT INTO tbtypepersonpermission VALUES("761","68","6","2","N");
INSERT INTO tbtypepersonpermission VALUES("762","68","1","5","Y");
INSERT INTO tbtypepersonpermission VALUES("763","68","2","5","N");
INSERT INTO tbtypepersonpermission VALUES("764","68","3","5","N");
INSERT INTO tbtypepersonpermission VALUES("765","68","4","5","N");
INSERT INTO tbtypepersonpermission VALUES("766","68","5","5","N");
INSERT INTO tbtypepersonpermission VALUES("767","68","6","5","N");
INSERT INTO tbtypepersonpermission VALUES("768","69","1","1","Y");
INSERT INTO tbtypepersonpermission VALUES("769","69","2","1","N");
INSERT INTO tbtypepersonpermission VALUES("770","69","3","1","N");
INSERT INTO tbtypepersonpermission VALUES("771","69","4","1","N");
INSERT INTO tbtypepersonpermission VALUES("772","69","5","1","N");
INSERT INTO tbtypepersonpermission VALUES("773","69","6","1","N");
INSERT INTO tbtypepersonpermission VALUES("774","69","1","2","N");
INSERT INTO tbtypepersonpermission VALUES("775","69","2","2","N");
INSERT INTO tbtypepersonpermission VALUES("776","69","3","2","N");
INSERT INTO tbtypepersonpermission VALUES("777","69","4","2","N");
INSERT INTO tbtypepersonpermission VALUES("778","69","5","2","N");
INSERT INTO tbtypepersonpermission VALUES("779","69","6","2","N");
INSERT INTO tbtypepersonpermission VALUES("780","69","1","5","Y");
INSERT INTO tbtypepersonpermission VALUES("781","69","2","5","N");
INSERT INTO tbtypepersonpermission VALUES("782","69","3","5","N");
INSERT INTO tbtypepersonpermission VALUES("783","69","4","5","N");
INSERT INTO tbtypepersonpermission VALUES("784","69","5","5","N");
INSERT INTO tbtypepersonpermission VALUES("785","69","6","5","N");
INSERT INTO tbtypepersonpermission VALUES("786","70","1","1","Y");
INSERT INTO tbtypepersonpermission VALUES("787","70","2","1","N");
INSERT INTO tbtypepersonpermission VALUES("788","70","3","1","N");
INSERT INTO tbtypepersonpermission VALUES("789","70","4","1","N");
INSERT INTO tbtypepersonpermission VALUES("790","70","5","1","N");
INSERT INTO tbtypepersonpermission VALUES("791","70","6","1","N");
INSERT INTO tbtypepersonpermission VALUES("792","70","1","2","N");
INSERT INTO tbtypepersonpermission VALUES("793","70","2","2","N");
INSERT INTO tbtypepersonpermission VALUES("794","70","3","2","N");
INSERT INTO tbtypepersonpermission VALUES("795","70","4","2","N");
INSERT INTO tbtypepersonpermission VALUES("796","70","5","2","N");
INSERT INTO tbtypepersonpermission VALUES("797","70","6","2","N");
INSERT INTO tbtypepersonpermission VALUES("798","70","1","5","Y");
INSERT INTO tbtypepersonpermission VALUES("799","70","2","5","N");
INSERT INTO tbtypepersonpermission VALUES("800","70","3","5","N");
INSERT INTO tbtypepersonpermission VALUES("801","70","4","5","N");
INSERT INTO tbtypepersonpermission VALUES("802","70","5","5","N");
INSERT INTO tbtypepersonpermission VALUES("803","70","6","5","N");
INSERT INTO tbtypepersonpermission VALUES("804","71","1","1","Y");
INSERT INTO tbtypepersonpermission VALUES("805","71","2","1","N");
INSERT INTO tbtypepersonpermission VALUES("806","71","3","1","N");
INSERT INTO tbtypepersonpermission VALUES("807","71","4","1","N");
INSERT INTO tbtypepersonpermission VALUES("808","71","5","1","N");
INSERT INTO tbtypepersonpermission VALUES("809","71","6","1","N");
INSERT INTO tbtypepersonpermission VALUES("810","71","1","2","N");
INSERT INTO tbtypepersonpermission VALUES("811","71","2","2","N");
INSERT INTO tbtypepersonpermission VALUES("812","71","3","2","N");
INSERT INTO tbtypepersonpermission VALUES("813","71","4","2","N");
INSERT INTO tbtypepersonpermission VALUES("814","71","5","2","N");
INSERT INTO tbtypepersonpermission VALUES("815","71","6","2","N");
INSERT INTO tbtypepersonpermission VALUES("816","71","1","5","Y");
INSERT INTO tbtypepersonpermission VALUES("817","71","2","5","N");
INSERT INTO tbtypepersonpermission VALUES("818","71","3","5","N");
INSERT INTO tbtypepersonpermission VALUES("819","71","4","5","N");
INSERT INTO tbtypepersonpermission VALUES("820","71","5","5","N");
INSERT INTO tbtypepersonpermission VALUES("821","71","6","5","N");
INSERT INTO tbtypepersonpermission VALUES("822","72","1","1","Y");
INSERT INTO tbtypepersonpermission VALUES("823","72","2","1","N");
INSERT INTO tbtypepersonpermission VALUES("824","72","3","1","N");
INSERT INTO tbtypepersonpermission VALUES("825","72","4","1","N");
INSERT INTO tbtypepersonpermission VALUES("826","72","5","1","N");
INSERT INTO tbtypepersonpermission VALUES("827","72","6","1","N");
INSERT INTO tbtypepersonpermission VALUES("828","72","1","2","N");
INSERT INTO tbtypepersonpermission VALUES("829","72","2","2","N");
INSERT INTO tbtypepersonpermission VALUES("830","72","3","2","N");
INSERT INTO tbtypepersonpermission VALUES("831","72","4","2","N");
INSERT INTO tbtypepersonpermission VALUES("832","72","5","2","N");
INSERT INTO tbtypepersonpermission VALUES("833","72","6","2","N");
INSERT INTO tbtypepersonpermission VALUES("834","72","1","5","Y");
INSERT INTO tbtypepersonpermission VALUES("835","72","2","5","N");
INSERT INTO tbtypepersonpermission VALUES("836","72","3","5","N");
INSERT INTO tbtypepersonpermission VALUES("837","72","4","5","N");
INSERT INTO tbtypepersonpermission VALUES("838","72","5","5","N");
INSERT INTO tbtypepersonpermission VALUES("839","72","6","5","N");
INSERT INTO tbtypepersonpermission VALUES("840","73","1","1","Y");
INSERT INTO tbtypepersonpermission VALUES("841","73","2","1","N");
INSERT INTO tbtypepersonpermission VALUES("842","73","3","1","N");
INSERT INTO tbtypepersonpermission VALUES("843","73","4","1","N");
INSERT INTO tbtypepersonpermission VALUES("844","73","5","1","N");
INSERT INTO tbtypepersonpermission VALUES("845","73","6","1","N");
INSERT INTO tbtypepersonpermission VALUES("846","73","1","2","Y");
INSERT INTO tbtypepersonpermission VALUES("847","73","2","2","N");
INSERT INTO tbtypepersonpermission VALUES("848","73","3","2","N");
INSERT INTO tbtypepersonpermission VALUES("849","73","4","2","N");
INSERT INTO tbtypepersonpermission VALUES("850","73","5","2","N");
INSERT INTO tbtypepersonpermission VALUES("851","73","6","2","N");
INSERT INTO tbtypepersonpermission VALUES("852","73","1","5","Y");
INSERT INTO tbtypepersonpermission VALUES("853","73","2","5","N");
INSERT INTO tbtypepersonpermission VALUES("854","73","3","5","N");
INSERT INTO tbtypepersonpermission VALUES("855","73","4","5","N");
INSERT INTO tbtypepersonpermission VALUES("856","73","5","5","N");
INSERT INTO tbtypepersonpermission VALUES("857","73","6","5","N");
INSERT INTO tbtypepersonpermission VALUES("858","74","1","1","Y");
INSERT INTO tbtypepersonpermission VALUES("859","74","1","2","Y");
INSERT INTO tbtypepersonpermission VALUES("860","74","1","3","Y");
INSERT INTO tbtypepersonpermission VALUES("861","74","1","4","Y");
INSERT INTO tbtypepersonpermission VALUES("862","74","1","5","N");
INSERT INTO tbtypepersonpermission VALUES("863","75","1","1","Y");
INSERT INTO tbtypepersonpermission VALUES("864","75","2","1","N");
INSERT INTO tbtypepersonpermission VALUES("865","75","3","1","N");
INSERT INTO tbtypepersonpermission VALUES("866","75","4","1","N");
INSERT INTO tbtypepersonpermission VALUES("867","75","5","1","N");
INSERT INTO tbtypepersonpermission VALUES("868","75","6","1","N");
INSERT INTO tbtypepersonpermission VALUES("869","75","1","2","Y");
INSERT INTO tbtypepersonpermission VALUES("870","75","2","2","N");
INSERT INTO tbtypepersonpermission VALUES("871","75","3","2","N");
INSERT INTO tbtypepersonpermission VALUES("872","75","4","2","N");
INSERT INTO tbtypepersonpermission VALUES("873","75","5","2","N");
INSERT INTO tbtypepersonpermission VALUES("874","75","6","2","N");
INSERT INTO tbtypepersonpermission VALUES("875","75","1","3","N");
INSERT INTO tbtypepersonpermission VALUES("876","75","2","3","N");
INSERT INTO tbtypepersonpermission VALUES("877","75","3","3","N");
INSERT INTO tbtypepersonpermission VALUES("878","75","4","3","N");
INSERT INTO tbtypepersonpermission VALUES("879","75","5","3","N");
INSERT INTO tbtypepersonpermission VALUES("880","75","6","3","N");
INSERT INTO tbtypepersonpermission VALUES("881","76","1","1","Y");
INSERT INTO tbtypepersonpermission VALUES("882","76","2","1","N");
INSERT INTO tbtypepersonpermission VALUES("883","76","3","1","N");
INSERT INTO tbtypepersonpermission VALUES("884","76","4","1","N");
INSERT INTO tbtypepersonpermission VALUES("885","76","5","1","N");
INSERT INTO tbtypepersonpermission VALUES("886","76","6","1","N");
INSERT INTO tbtypepersonpermission VALUES("887","76","1","2","Y");
INSERT INTO tbtypepersonpermission VALUES("888","76","2","2","N");
INSERT INTO tbtypepersonpermission VALUES("889","76","3","2","N");
INSERT INTO tbtypepersonpermission VALUES("890","76","4","2","N");
INSERT INTO tbtypepersonpermission VALUES("891","76","5","2","N");
INSERT INTO tbtypepersonpermission VALUES("892","76","6","2","N");
INSERT INTO tbtypepersonpermission VALUES("893","77","1","1","Y");
INSERT INTO tbtypepersonpermission VALUES("894","77","2","1","N");
INSERT INTO tbtypepersonpermission VALUES("895","77","3","1","N");
INSERT INTO tbtypepersonpermission VALUES("896","77","4","1","N");
INSERT INTO tbtypepersonpermission VALUES("897","77","5","1","N");
INSERT INTO tbtypepersonpermission VALUES("898","77","6","1","N");
INSERT INTO tbtypepersonpermission VALUES("899","77","1","2","Y");
INSERT INTO tbtypepersonpermission VALUES("900","77","2","2","N");
INSERT INTO tbtypepersonpermission VALUES("901","77","3","2","N");
INSERT INTO tbtypepersonpermission VALUES("902","77","4","2","N");
INSERT INTO tbtypepersonpermission VALUES("903","77","5","2","N");
INSERT INTO tbtypepersonpermission VALUES("904","77","6","2","N");
INSERT INTO tbtypepersonpermission VALUES("905","77","1","3","Y");
INSERT INTO tbtypepersonpermission VALUES("906","77","2","3","N");
INSERT INTO tbtypepersonpermission VALUES("907","77","3","3","N");
INSERT INTO tbtypepersonpermission VALUES("908","77","4","3","N");
INSERT INTO tbtypepersonpermission VALUES("909","77","5","3","N");
INSERT INTO tbtypepersonpermission VALUES("910","77","6","3","N");
INSERT INTO tbtypepersonpermission VALUES("911","77","1","4","Y");
INSERT INTO tbtypepersonpermission VALUES("912","77","2","4","N");
INSERT INTO tbtypepersonpermission VALUES("913","77","3","4","N");
INSERT INTO tbtypepersonpermission VALUES("914","77","4","4","N");
INSERT INTO tbtypepersonpermission VALUES("915","77","5","4","N");
INSERT INTO tbtypepersonpermission VALUES("916","77","6","4","N");
INSERT INTO tbtypepersonpermission VALUES("917","77","1","5","Y");
INSERT INTO tbtypepersonpermission VALUES("918","77","2","5","N");
INSERT INTO tbtypepersonpermission VALUES("919","77","3","5","N");
INSERT INTO tbtypepersonpermission VALUES("920","77","4","5","N");
INSERT INTO tbtypepersonpermission VALUES("921","77","5","5","N");
INSERT INTO tbtypepersonpermission VALUES("922","77","6","5","N");
INSERT INTO tbtypepersonpermission VALUES("923","78","1","1","Y");
INSERT INTO tbtypepersonpermission VALUES("924","78","2","1","N");
INSERT INTO tbtypepersonpermission VALUES("925","78","3","1","N");
INSERT INTO tbtypepersonpermission VALUES("926","78","4","1","N");
INSERT INTO tbtypepersonpermission VALUES("927","78","5","1","N");
INSERT INTO tbtypepersonpermission VALUES("928","78","6","1","N");
INSERT INTO tbtypepersonpermission VALUES("929","78","1","2","Y");
INSERT INTO tbtypepersonpermission VALUES("930","78","2","2","N");
INSERT INTO tbtypepersonpermission VALUES("931","78","3","2","N");
INSERT INTO tbtypepersonpermission VALUES("932","78","4","2","N");
INSERT INTO tbtypepersonpermission VALUES("933","78","5","2","N");
INSERT INTO tbtypepersonpermission VALUES("934","78","6","2","N");
INSERT INTO tbtypepersonpermission VALUES("935","78","1","5","Y");
INSERT INTO tbtypepersonpermission VALUES("936","78","2","5","N");
INSERT INTO tbtypepersonpermission VALUES("937","78","3","5","N");
INSERT INTO tbtypepersonpermission VALUES("938","78","4","5","N");
INSERT INTO tbtypepersonpermission VALUES("939","78","5","5","N");
INSERT INTO tbtypepersonpermission VALUES("940","78","6","5","N");
INSERT INTO tbtypepersonpermission VALUES("941","79","1","1","Y");
INSERT INTO tbtypepersonpermission VALUES("942","79","2","1","N");
INSERT INTO tbtypepersonpermission VALUES("943","79","3","1","N");
INSERT INTO tbtypepersonpermission VALUES("944","79","4","1","N");
INSERT INTO tbtypepersonpermission VALUES("945","79","5","1","N");
INSERT INTO tbtypepersonpermission VALUES("946","79","6","1","N");
INSERT INTO tbtypepersonpermission VALUES("947","79","1","2","Y");
INSERT INTO tbtypepersonpermission VALUES("948","79","2","2","N");
INSERT INTO tbtypepersonpermission VALUES("949","79","3","2","N");
INSERT INTO tbtypepersonpermission VALUES("950","79","4","2","N");
INSERT INTO tbtypepersonpermission VALUES("951","79","5","2","N");
INSERT INTO tbtypepersonpermission VALUES("952","79","6","2","N");
INSERT INTO tbtypepersonpermission VALUES("953","79","1","3","Y");
INSERT INTO tbtypepersonpermission VALUES("954","79","2","3","N");
INSERT INTO tbtypepersonpermission VALUES("955","79","3","3","N");
INSERT INTO tbtypepersonpermission VALUES("956","79","4","3","N");
INSERT INTO tbtypepersonpermission VALUES("957","79","5","3","N");
INSERT INTO tbtypepersonpermission VALUES("958","79","6","3","N");
INSERT INTO tbtypepersonpermission VALUES("959","79","1","4","Y");
INSERT INTO tbtypepersonpermission VALUES("960","79","2","4","N");
INSERT INTO tbtypepersonpermission VALUES("961","79","3","4","N");
INSERT INTO tbtypepersonpermission VALUES("962","79","4","4","N");
INSERT INTO tbtypepersonpermission VALUES("963","79","5","4","N");
INSERT INTO tbtypepersonpermission VALUES("964","79","6","4","N");
INSERT INTO tbtypepersonpermission VALUES("965","80","1","1","Y");
INSERT INTO tbtypepersonpermission VALUES("966","80","2","1","N");
INSERT INTO tbtypepersonpermission VALUES("967","80","3","1","N");
INSERT INTO tbtypepersonpermission VALUES("968","80","4","1","N");
INSERT INTO tbtypepersonpermission VALUES("969","80","5","1","N");
INSERT INTO tbtypepersonpermission VALUES("970","80","6","1","N");
INSERT INTO tbtypepersonpermission VALUES("971","80","1","2","Y");
INSERT INTO tbtypepersonpermission VALUES("972","80","2","2","N");
INSERT INTO tbtypepersonpermission VALUES("973","80","3","2","N");
INSERT INTO tbtypepersonpermission VALUES("974","80","4","2","N");
INSERT INTO tbtypepersonpermission VALUES("975","80","5","2","N");
INSERT INTO tbtypepersonpermission VALUES("976","80","6","2","N");
INSERT INTO tbtypepersonpermission VALUES("977","80","1","3","Y");
INSERT INTO tbtypepersonpermission VALUES("978","80","2","3","N");
INSERT INTO tbtypepersonpermission VALUES("979","80","3","3","N");
INSERT INTO tbtypepersonpermission VALUES("980","80","4","3","N");
INSERT INTO tbtypepersonpermission VALUES("981","80","5","3","N");
INSERT INTO tbtypepersonpermission VALUES("982","80","6","3","N");
INSERT INTO tbtypepersonpermission VALUES("983","80","1","4","Y");
INSERT INTO tbtypepersonpermission VALUES("984","80","2","4","N");
INSERT INTO tbtypepersonpermission VALUES("985","80","3","4","N");
INSERT INTO tbtypepersonpermission VALUES("986","80","4","4","N");
INSERT INTO tbtypepersonpermission VALUES("987","80","5","4","N");
INSERT INTO tbtypepersonpermission VALUES("988","80","6","4","N");
INSERT INTO tbtypepersonpermission VALUES("989","80","1","5","Y");
INSERT INTO tbtypepersonpermission VALUES("990","80","2","5","N");
INSERT INTO tbtypepersonpermission VALUES("991","80","3","5","N");
INSERT INTO tbtypepersonpermission VALUES("992","80","4","5","N");
INSERT INTO tbtypepersonpermission VALUES("993","80","5","5","N");
INSERT INTO tbtypepersonpermission VALUES("994","80","6","5","N");
INSERT INTO tbtypepersonpermission VALUES("995","81","1","1","Y");
INSERT INTO tbtypepersonpermission VALUES("996","81","2","1","N");
INSERT INTO tbtypepersonpermission VALUES("997","81","3","1","N");
INSERT INTO tbtypepersonpermission VALUES("998","81","4","1","N");
INSERT INTO tbtypepersonpermission VALUES("999","81","5","1","N");
INSERT INTO tbtypepersonpermission VALUES("1000","81","6","1","N");
INSERT INTO tbtypepersonpermission VALUES("1001","81","1","2","Y");
INSERT INTO tbtypepersonpermission VALUES("1002","81","2","2","N");
INSERT INTO tbtypepersonpermission VALUES("1003","81","3","2","N");
INSERT INTO tbtypepersonpermission VALUES("1004","81","4","2","N");
INSERT INTO tbtypepersonpermission VALUES("1005","81","5","2","N");
INSERT INTO tbtypepersonpermission VALUES("1006","81","6","2","N");
INSERT INTO tbtypepersonpermission VALUES("1007","81","1","3","Y");
INSERT INTO tbtypepersonpermission VALUES("1008","81","2","3","N");
INSERT INTO tbtypepersonpermission VALUES("1009","81","3","3","N");
INSERT INTO tbtypepersonpermission VALUES("1010","81","4","3","N");
INSERT INTO tbtypepersonpermission VALUES("1011","81","5","3","N");
INSERT INTO tbtypepersonpermission VALUES("1012","81","6","3","N");
INSERT INTO tbtypepersonpermission VALUES("1013","81","1","4","Y");
INSERT INTO tbtypepersonpermission VALUES("1014","81","2","4","N");
INSERT INTO tbtypepersonpermission VALUES("1015","81","3","4","N");
INSERT INTO tbtypepersonpermission VALUES("1016","81","4","4","N");
INSERT INTO tbtypepersonpermission VALUES("1017","81","5","4","N");
INSERT INTO tbtypepersonpermission VALUES("1018","81","6","4","N");
INSERT INTO tbtypepersonpermission VALUES("1019","81","1","5","N");
INSERT INTO tbtypepersonpermission VALUES("1020","81","2","5","N");
INSERT INTO tbtypepersonpermission VALUES("1021","81","3","5","N");
INSERT INTO tbtypepersonpermission VALUES("1022","81","4","5","N");
INSERT INTO tbtypepersonpermission VALUES("1023","81","5","5","N");
INSERT INTO tbtypepersonpermission VALUES("1024","81","6","5","N");
INSERT INTO tbtypepersonpermission VALUES("1025","81","1","6","N");
INSERT INTO tbtypepersonpermission VALUES("1026","81","2","6","N");
INSERT INTO tbtypepersonpermission VALUES("1027","81","3","6","N");
INSERT INTO tbtypepersonpermission VALUES("1028","81","4","6","N");
INSERT INTO tbtypepersonpermission VALUES("1029","81","5","6","N");
INSERT INTO tbtypepersonpermission VALUES("1030","81","6","6","N");



DROP TABLE IF EXISTS tbtypeperson_plus;

CREATE TABLE `tbtypeperson_plus` (
  `idtypepersonplus` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`idtypepersonplus`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

INSERT INTO tbtypeperson_plus VALUES("1","Google");
INSERT INTO tbtypeperson_plus VALUES("2","Facebook");



DROP TABLE IF EXISTS tbtypestreet;

CREATE TABLE `tbtypestreet` (
  `idtypestreet` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  `abbr` varchar(15) DEFAULT NULL,
  `location` char(20) DEFAULT NULL,
  PRIMARY KEY (`idtypestreet`)
) ENGINE=InnoDB AUTO_INCREMENT=340 DEFAULT CHARSET=latin1;

INSERT INTO tbtypestreet VALUES("1","Choose","AAA","pt_BR");
INSERT INTO tbtypestreet VALUES("2","Avenue","AVE","pt_BR");
INSERT INTO tbtypestreet VALUES("4","Street","ST","pt_BR");
INSERT INTO tbtypestreet VALUES("5","Alameda","AL","pt_BR");
INSERT INTO tbtypestreet VALUES("6","Acesso","AC","pt_BR");
INSERT INTO tbtypestreet VALUES("7","Adro","AD","pt_BR");
INSERT INTO tbtypestreet VALUES("8","Aeroporto","ERA","pt_BR");
INSERT INTO tbtypestreet VALUES("9","Alameda","AL","pt_BR");
INSERT INTO tbtypestreet VALUES("10","Alto","AT","pt_BR");
INSERT INTO tbtypestreet VALUES("11","ï¿½rea","A","pt_BR");
INSERT INTO tbtypestreet VALUES("12","ï¿½rea Especial","AE","pt_BR");
INSERT INTO tbtypestreet VALUES("13","Artï¿½ria","ART","pt_BR");
INSERT INTO tbtypestreet VALUES("14","Atalho","ATL","pt_BR");
INSERT INTO tbtypestreet VALUES("15","Avenida","AV","pt_BR");
INSERT INTO tbtypestreet VALUES("16","Avenida Contorno","AV-CONT","pt_BR");
INSERT INTO tbtypestreet VALUES("17","Baixa","BX","pt_BR");
INSERT INTO tbtypestreet VALUES("18","Balï¿½o","BLO","pt_BR");
INSERT INTO tbtypestreet VALUES("19","Balneï¿½rio","BAL","pt_BR");
INSERT INTO tbtypestreet VALUES("20","Beco","BC","pt_BR");
INSERT INTO tbtypestreet VALUES("21","Belvedere","BELV","pt_BR");
INSERT INTO tbtypestreet VALUES("22","Bloco","BL","pt_BR");
INSERT INTO tbtypestreet VALUES("23","Bosque","BSQ","pt_BR");
INSERT INTO tbtypestreet VALUES("24","Boulevard","BVD","pt_BR");
INSERT INTO tbtypestreet VALUES("25","Buraco","BCO","pt_BR");
INSERT INTO tbtypestreet VALUES("26","Cais","C","pt_BR");
INSERT INTO tbtypestreet VALUES("27","Calï¿½ada","CALC","pt_BR");
INSERT INTO tbtypestreet VALUES("28","Caminho","CAM","pt_BR");
INSERT INTO tbtypestreet VALUES("29","Campo","CPO","pt_BR");
INSERT INTO tbtypestreet VALUES("30","Canal","CAN","pt_BR");
INSERT INTO tbtypestreet VALUES("31","Chï¿½cara","CH","pt_BR");
INSERT INTO tbtypestreet VALUES("32","Chapadï¿½o","CHAP","pt_BR");
INSERT INTO tbtypestreet VALUES("33","Circular","CIRC","pt_BR");
INSERT INTO tbtypestreet VALUES("34","Colï¿½nia","COL","pt_BR");
INSERT INTO tbtypestreet VALUES("35","Complexo Viï¿½rio","CMP-VR","pt_BR");
INSERT INTO tbtypestreet VALUES("36","Condomï¿½nio","COND","pt_BR");
INSERT INTO tbtypestreet VALUES("37","Conjunto","CJ","pt_BR");
INSERT INTO tbtypestreet VALUES("38","Corredor","COR","pt_BR");
INSERT INTO tbtypestreet VALUES("39","Cï¿½rrego","CRG","pt_BR");
INSERT INTO tbtypestreet VALUES("40","Descida","DSC","pt_BR");
INSERT INTO tbtypestreet VALUES("41","Desvio","DSV","pt_BR");
INSERT INTO tbtypestreet VALUES("42","Distrito","DT","pt_BR");
INSERT INTO tbtypestreet VALUES("43","Elevada","EVD","pt_BR");
INSERT INTO tbtypestreet VALUES("44","Entrada Particular","ENT-PART","pt_BR");
INSERT INTO tbtypestreet VALUES("45","Entre Quadra","EQ","pt_BR");
INSERT INTO tbtypestreet VALUES("46","Escada","ESC","pt_BR");
INSERT INTO tbtypestreet VALUES("47","Esplanada","ESP","pt_BR");
INSERT INTO tbtypestreet VALUES("48","Estaï¿½ï¿½o","ETC","pt_BR");
INSERT INTO tbtypestreet VALUES("49","Estacionamento","ESTC","pt_BR");
INSERT INTO tbtypestreet VALUES("50","Estï¿½dio","ETD","pt_BR");
INSERT INTO tbtypestreet VALUES("51","Estï¿½ncia","ETN","pt_BR");
INSERT INTO tbtypestreet VALUES("52","Estrada","EST","pt_BR");
INSERT INTO tbtypestreet VALUES("53","Estrada Municipal","EST-MUN","pt_BR");
INSERT INTO tbtypestreet VALUES("54","Favela","FAV","pt_BR");
INSERT INTO tbtypestreet VALUES("55","Fazenda","FAZ","pt_BR");
INSERT INTO tbtypestreet VALUES("56","Feira","FRA","pt_BR");
INSERT INTO tbtypestreet VALUES("57","Ferrovia","FER","pt_BR");
INSERT INTO tbtypestreet VALUES("58","Fonte","FNT","pt_BR");
INSERT INTO tbtypestreet VALUES("59","Forte","FTE","pt_BR");
INSERT INTO tbtypestreet VALUES("60","Galeria","GAL","pt_BR");
INSERT INTO tbtypestreet VALUES("61","Granja","GJA","pt_BR");
INSERT INTO tbtypestreet VALUES("62","Habitacional","HAB","pt_BR");
INSERT INTO tbtypestreet VALUES("63","Ilha","IA","pt_BR");
INSERT INTO tbtypestreet VALUES("64","Jardim","JD","pt_BR");
INSERT INTO tbtypestreet VALUES("65","Jardinete","JDE","pt_BR");
INSERT INTO tbtypestreet VALUES("66","Ladeira","LD","pt_BR");
INSERT INTO tbtypestreet VALUES("67","Lago","LG","pt_BR");
INSERT INTO tbtypestreet VALUES("68","Lagoa","LGA","pt_BR");
INSERT INTO tbtypestreet VALUES("69","Largo","LRG","pt_BR");
INSERT INTO tbtypestreet VALUES("70","Loteamento","LOT","pt_BR");
INSERT INTO tbtypestreet VALUES("71","Marina","MNA","pt_BR");
INSERT INTO tbtypestreet VALUES("72","Mï¿½dulo","MOD","pt_BR");
INSERT INTO tbtypestreet VALUES("73","Monte","TEM","pt_BR");
INSERT INTO tbtypestreet VALUES("74","Morro","MRO","pt_BR");
INSERT INTO tbtypestreet VALUES("75","Nï¿½cleo","NUC","pt_BR");
INSERT INTO tbtypestreet VALUES("76","Parada","PDA","pt_BR");
INSERT INTO tbtypestreet VALUES("77","Paradouro","PDO","pt_BR");
INSERT INTO tbtypestreet VALUES("78","Paralela","PAR","pt_BR");
INSERT INTO tbtypestreet VALUES("79","Parque","PRQ","pt_BR");
INSERT INTO tbtypestreet VALUES("80","Passagem","PSG","pt_BR");
INSERT INTO tbtypestreet VALUES("81","Passagem Subterrï¿½nea","PSC-SUB","pt_BR");
INSERT INTO tbtypestreet VALUES("82","Passarela","PSA","pt_BR");
INSERT INTO tbtypestreet VALUES("83","Passeio","PAS","pt_BR");
INSERT INTO tbtypestreet VALUES("84","Pï¿½tio","PAT","pt_BR");
INSERT INTO tbtypestreet VALUES("85","Ponta","PNT","pt_BR");
INSERT INTO tbtypestreet VALUES("86","Ponte","PTE","pt_BR");
INSERT INTO tbtypestreet VALUES("87","Porto","PTO","pt_BR");
INSERT INTO tbtypestreet VALUES("88","Praï¿½a","PC","pt_BR");
INSERT INTO tbtypestreet VALUES("89","Praï¿½a de Esportes","PC-ESP","pt_BR");
INSERT INTO tbtypestreet VALUES("90","Praia","PR","pt_BR");
INSERT INTO tbtypestreet VALUES("91","Prolongamento","PRL","pt_BR");
INSERT INTO tbtypestreet VALUES("92","Quadra","Q","pt_BR");
INSERT INTO tbtypestreet VALUES("93","Quinta","QTA","pt_BR");
INSERT INTO tbtypestreet VALUES("94","Ane Quintas","QTASRodo","pt_BR");
INSERT INTO tbtypestreet VALUES("95","Ramal","RAM","pt_BR");
INSERT INTO tbtypestreet VALUES("96","Rampa","RMP","pt_BR");
INSERT INTO tbtypestreet VALUES("97","Recanto","REC","pt_BR");
INSERT INTO tbtypestreet VALUES("98","Residencial","RES","pt_BR");
INSERT INTO tbtypestreet VALUES("99","Reta","RET","pt_BR");
INSERT INTO tbtypestreet VALUES("100","Retiro","RER","pt_BR");
INSERT INTO tbtypestreet VALUES("101","Retorno","RTN","pt_BR");
INSERT INTO tbtypestreet VALUES("102","Rodo Anel","ROD-AN","pt_BR");
INSERT INTO tbtypestreet VALUES("103","Rodovia","ROD","pt_BR");
INSERT INTO tbtypestreet VALUES("104","Rotatï¿½ria","RTT","pt_BR");
INSERT INTO tbtypestreet VALUES("105","Rï¿½tula","ROT","pt_BR");
INSERT INTO tbtypestreet VALUES("106","Rua","R","pt_BR");
INSERT INTO tbtypestreet VALUES("107","Rua de Ligaï¿½ï¿½o","R-LIG","pt_BR");
INSERT INTO tbtypestreet VALUES("108","Rua de Pedestre","R-PED","pt_BR");
INSERT INTO tbtypestreet VALUES("109","Servidï¿½o","SRV","pt_BR");
INSERT INTO tbtypestreet VALUES("110","Setor","ST","pt_BR");
INSERT INTO tbtypestreet VALUES("111","Sï¿½tio","SIT","pt_BR");
INSERT INTO tbtypestreet VALUES("112","Subida","SUB","pt_BR");
INSERT INTO tbtypestreet VALUES("113","Terminal","TER","pt_BR");
INSERT INTO tbtypestreet VALUES("114","Travessa","TV","pt_BR");
INSERT INTO tbtypestreet VALUES("115","Travessa Particular","TV-PART","pt_BR");
INSERT INTO tbtypestreet VALUES("116","Trecho","TRC","pt_BR");
INSERT INTO tbtypestreet VALUES("117","Trevo","TRV","pt_BR");
INSERT INTO tbtypestreet VALUES("118","Trincheira","TCH","pt_BR");
INSERT INTO tbtypestreet VALUES("119","Tï¿½nel","TUN","pt_BR");
INSERT INTO tbtypestreet VALUES("120","Unidade","UNID","pt_BR");
INSERT INTO tbtypestreet VALUES("121","Vala","VAL","pt_BR");
INSERT INTO tbtypestreet VALUES("122","Vale","VLE","pt_BR");
INSERT INTO tbtypestreet VALUES("123","Variante","VRTE","pt_BR");
INSERT INTO tbtypestreet VALUES("124","Vereda","VER","pt_BR");
INSERT INTO tbtypestreet VALUES("125","Via","V","pt_BR");
INSERT INTO tbtypestreet VALUES("126","Via de Acesso","V-AC","pt_BR");
INSERT INTO tbtypestreet VALUES("127","Via de Pedestre","V-PED","pt_BR");
INSERT INTO tbtypestreet VALUES("128","Via Elevado","V-EVD","pt_BR");
INSERT INTO tbtypestreet VALUES("129","Via Expressa","V-EXP","pt_BR");
INSERT INTO tbtypestreet VALUES("130","Viaduto","VD","pt_BR");
INSERT INTO tbtypestreet VALUES("131","Viela","VLA","pt_BR");
INSERT INTO tbtypestreet VALUES("132","Vila","VL","pt_BR");
INSERT INTO tbtypestreet VALUES("133","Zigue-Zague","ZIG-ZAG","pt_BR");
INSERT INTO tbtypestreet VALUES("134","ALLEY","ALY","en_US");
INSERT INTO tbtypestreet VALUES("135","ANEX","ANX","en_US");
INSERT INTO tbtypestreet VALUES("136","ARCADE","ARC","en_US");
INSERT INTO tbtypestreet VALUES("137","AVENUE","AVE","en_US");
INSERT INTO tbtypestreet VALUES("138","BAYOU","BYU","en_US");
INSERT INTO tbtypestreet VALUES("139","BEACH","BCH","en_US");
INSERT INTO tbtypestreet VALUES("140","BEND","BND","en_US");
INSERT INTO tbtypestreet VALUES("141","BLUFF","BLF","en_US");
INSERT INTO tbtypestreet VALUES("142","BLUFFS","BLFS","en_US");
INSERT INTO tbtypestreet VALUES("143","BOTTOM","BTM","en_US");
INSERT INTO tbtypestreet VALUES("144","BOULEVARD","BLVD","en_US");
INSERT INTO tbtypestreet VALUES("145","BRANCH","BR","en_US");
INSERT INTO tbtypestreet VALUES("146","BRIDGE","BRG","en_US");
INSERT INTO tbtypestreet VALUES("147","BROOK","BRK","en_US");
INSERT INTO tbtypestreet VALUES("148","BROOKS","BRKS","en_US");
INSERT INTO tbtypestreet VALUES("149","BURG","BG","en_US");
INSERT INTO tbtypestreet VALUES("150","BURGS","BGS","en_US");
INSERT INTO tbtypestreet VALUES("151","BYPASS","BYP","en_US");
INSERT INTO tbtypestreet VALUES("152","CAMP","CP","en_US");
INSERT INTO tbtypestreet VALUES("153","CANYON","CYN","en_US");
INSERT INTO tbtypestreet VALUES("154","CAPE","CPE","en_US");
INSERT INTO tbtypestreet VALUES("155","CAUSEWAY","CSWY","en_US");
INSERT INTO tbtypestreet VALUES("156","CENTER","CTR","en_US");
INSERT INTO tbtypestreet VALUES("157","CENTERS","CTRS","en_US");
INSERT INTO tbtypestreet VALUES("158","CIRCLE","CIR","en_US");
INSERT INTO tbtypestreet VALUES("159","CIRCLES","CIRS","en_US");
INSERT INTO tbtypestreet VALUES("160","CLIFF","CLF","en_US");
INSERT INTO tbtypestreet VALUES("161","CLIFFS","CLFS","en_US");
INSERT INTO tbtypestreet VALUES("162","CLUB","CLB","en_US");
INSERT INTO tbtypestreet VALUES("163","COMMON","CMN","en_US");
INSERT INTO tbtypestreet VALUES("164","COMMONS","CMNS","en_US");
INSERT INTO tbtypestreet VALUES("165","CORNER","COR","en_US");
INSERT INTO tbtypestreet VALUES("166","CORNERS","CORS","en_US");
INSERT INTO tbtypestreet VALUES("167","COURSE","CRSE","en_US");
INSERT INTO tbtypestreet VALUES("168","COURT","CT","en_US");
INSERT INTO tbtypestreet VALUES("169","COURTS","CTS","en_US");
INSERT INTO tbtypestreet VALUES("170","COVE","CV","en_US");
INSERT INTO tbtypestreet VALUES("171","COVES","CVS","en_US");
INSERT INTO tbtypestreet VALUES("172","CREEK","CRK","en_US");
INSERT INTO tbtypestreet VALUES("173","CRESCENT","CRES","en_US");
INSERT INTO tbtypestreet VALUES("174","CREST","CRST","en_US");
INSERT INTO tbtypestreet VALUES("175","CROSSING","XING","en_US");
INSERT INTO tbtypestreet VALUES("176","CROSSROAD","XRD","en_US");
INSERT INTO tbtypestreet VALUES("177","CROSSROADS","XRDS","en_US");
INSERT INTO tbtypestreet VALUES("178","CURVE ","CURV","en_US");
INSERT INTO tbtypestreet VALUES("179","DALE","DL","en_US");
INSERT INTO tbtypestreet VALUES("180","DAM","DM","en_US");
INSERT INTO tbtypestreet VALUES("181","DIVIDE","DV","en_US");
INSERT INTO tbtypestreet VALUES("182","DRIVE","DR","en_US");
INSERT INTO tbtypestreet VALUES("183","DRIVES","DRS","en_US");
INSERT INTO tbtypestreet VALUES("184","ESTATE","EST","en_US");
INSERT INTO tbtypestreet VALUES("185","ESTATES","ESTS","en_US");
INSERT INTO tbtypestreet VALUES("186","EXPRESSWAY","EXPY","en_US");
INSERT INTO tbtypestreet VALUES("187","EXTENSION","EXT","en_US");
INSERT INTO tbtypestreet VALUES("188","EXTENSIONS","EXTS","en_US");
INSERT INTO tbtypestreet VALUES("189","FALL","FALL","en_US");
INSERT INTO tbtypestreet VALUES("190","FALLS","FLS","en_US");
INSERT INTO tbtypestreet VALUES("191","FERRY","FRY","en_US");
INSERT INTO tbtypestreet VALUES("192","FIELD","FLD","en_US");
INSERT INTO tbtypestreet VALUES("193","FIELDS","FLDS","en_US");
INSERT INTO tbtypestreet VALUES("194","FLAT","FLT","en_US");
INSERT INTO tbtypestreet VALUES("195","FLATS","FLTS","en_US");
INSERT INTO tbtypestreet VALUES("196","FORD","FRD","en_US");
INSERT INTO tbtypestreet VALUES("197","FORDS","FRDS","en_US");
INSERT INTO tbtypestreet VALUES("198","FOREST","FRST","en_US");
INSERT INTO tbtypestreet VALUES("199","FORGE","FRG","en_US");
INSERT INTO tbtypestreet VALUES("200","FORGES","FRGS","en_US");
INSERT INTO tbtypestreet VALUES("201","FORK","FRK","en_US");
INSERT INTO tbtypestreet VALUES("202","FORKS","FRKS","en_US");
INSERT INTO tbtypestreet VALUES("203","FORT","FT","en_US");
INSERT INTO tbtypestreet VALUES("204","FREEWAY","FWY","en_US");
INSERT INTO tbtypestreet VALUES("205","GARDEN","GDN","en_US");
INSERT INTO tbtypestreet VALUES("206","GARDENS","GDNS","en_US");
INSERT INTO tbtypestreet VALUES("207","GATEWAY","GTWY","en_US");
INSERT INTO tbtypestreet VALUES("208","GLEN","GLN","en_US");
INSERT INTO tbtypestreet VALUES("209","GLENS","GLNS","en_US");
INSERT INTO tbtypestreet VALUES("210","GREEN","GRN","en_US");
INSERT INTO tbtypestreet VALUES("211","GREENS","GRNS","en_US");
INSERT INTO tbtypestreet VALUES("212","GROVE","GRV","en_US");
INSERT INTO tbtypestreet VALUES("213","GROVES","GRVS","en_US");
INSERT INTO tbtypestreet VALUES("214","HARBOR","HBR","en_US");
INSERT INTO tbtypestreet VALUES("215","HARBORS","HBRS","en_US");
INSERT INTO tbtypestreet VALUES("216","HAVEN","HVN","en_US");
INSERT INTO tbtypestreet VALUES("217","HEIGHTS","HTS","en_US");
INSERT INTO tbtypestreet VALUES("218","HIGHWAY","HWY","en_US");
INSERT INTO tbtypestreet VALUES("219","HILL","HL","en_US");
INSERT INTO tbtypestreet VALUES("220","HILLS","HLS","en_US");
INSERT INTO tbtypestreet VALUES("221","HOLLOW","HOLW","en_US");
INSERT INTO tbtypestreet VALUES("222","INLET","INLT","en_US");
INSERT INTO tbtypestreet VALUES("223","ISLAND","IS","en_US");
INSERT INTO tbtypestreet VALUES("224","ISLANDS","ISS","en_US");
INSERT INTO tbtypestreet VALUES("225","ISLE","ISLE","en_US");
INSERT INTO tbtypestreet VALUES("226","JUNCTION","JCT","en_US");
INSERT INTO tbtypestreet VALUES("227","JUNCTIONS","JCTS","en_US");
INSERT INTO tbtypestreet VALUES("228","KEY","KY","en_US");
INSERT INTO tbtypestreet VALUES("229","KEYS","KYS","en_US");
INSERT INTO tbtypestreet VALUES("230","KNOLL","KNL ","en_US");
INSERT INTO tbtypestreet VALUES("231","KNOLLS","KNLS","en_US");
INSERT INTO tbtypestreet VALUES("232","LAKE","LK","en_US");
INSERT INTO tbtypestreet VALUES("233","LAKES","LKS","en_US");
INSERT INTO tbtypestreet VALUES("234","LAND","LAND","en_US");
INSERT INTO tbtypestreet VALUES("235","LANDING","LNDG","en_US");
INSERT INTO tbtypestreet VALUES("236","LANE","LN","en_US");
INSERT INTO tbtypestreet VALUES("237","LIGHT","LGT","en_US");
INSERT INTO tbtypestreet VALUES("238","LIGHTS","LGTS","en_US");
INSERT INTO tbtypestreet VALUES("239","LOAF","LF","en_US");
INSERT INTO tbtypestreet VALUES("240","LOCK","LCK","en_US");
INSERT INTO tbtypestreet VALUES("241","LOCKS","LCKS","en_US");
INSERT INTO tbtypestreet VALUES("242","LODGE","LDG","en_US");
INSERT INTO tbtypestreet VALUES("243","LOOP","LOOP","en_US");
INSERT INTO tbtypestreet VALUES("244","MALL","MALL","en_US");
INSERT INTO tbtypestreet VALUES("245","MANOR","MNR","en_US");
INSERT INTO tbtypestreet VALUES("246","MANORS","MNRS","en_US");
INSERT INTO tbtypestreet VALUES("247","MEADOW","MDW","en_US");
INSERT INTO tbtypestreet VALUES("248","MEADOWS","MDWS","en_US");
INSERT INTO tbtypestreet VALUES("249","MEWS","MEWS","en_US");
INSERT INTO tbtypestreet VALUES("250","MILL","ML","en_US");
INSERT INTO tbtypestreet VALUES("251","MILLS","MLS","en_US");
INSERT INTO tbtypestreet VALUES("252","MISSION","MSN","en_US");
INSERT INTO tbtypestreet VALUES("253","MOTORWAY","MTWY","en_US");
INSERT INTO tbtypestreet VALUES("254","MOUNT","MT","en_US");
INSERT INTO tbtypestreet VALUES("255","MOUNTAIN","MTN","en_US");
INSERT INTO tbtypestreet VALUES("256","MOUNTAINS","MTNS","en_US");
INSERT INTO tbtypestreet VALUES("257","NECK","NCK","en_US");
INSERT INTO tbtypestreet VALUES("258","ORCHARD","ORCH","en_US");
INSERT INTO tbtypestreet VALUES("259","OVAL","OVAL","en_US");
INSERT INTO tbtypestreet VALUES("260","OVERPASS","OPAS","en_US");
INSERT INTO tbtypestreet VALUES("261","PARK","PARK","en_US");
INSERT INTO tbtypestreet VALUES("262","PARKS","PARK","en_US");
INSERT INTO tbtypestreet VALUES("263","PARKWAY","PKWY","en_US");
INSERT INTO tbtypestreet VALUES("264","PARKWAYS","PKWY","en_US");
INSERT INTO tbtypestreet VALUES("265","PASS","PASS","en_US");
INSERT INTO tbtypestreet VALUES("266","PASSAGE","PSGE","en_US");
INSERT INTO tbtypestreet VALUES("267","PATH","PATH","en_US");
INSERT INTO tbtypestreet VALUES("268","PIKE","PIKE","en_US");
INSERT INTO tbtypestreet VALUES("269","PINE","PNE ","en_US");
INSERT INTO tbtypestreet VALUES("270","PINES","PNES","en_US");
INSERT INTO tbtypestreet VALUES("271","PLACE","PL","en_US");
INSERT INTO tbtypestreet VALUES("272","PLAIN","PLN","en_US");
INSERT INTO tbtypestreet VALUES("273","PLAINS","PLNS","en_US");
INSERT INTO tbtypestreet VALUES("274","PLAZA","PLZ","en_US");
INSERT INTO tbtypestreet VALUES("275","POINT","PT","en_US");
INSERT INTO tbtypestreet VALUES("276","POINTS","PTS","en_US");
INSERT INTO tbtypestreet VALUES("277","PORT","PRT","en_US");
INSERT INTO tbtypestreet VALUES("278","PORTS","PRTS","en_US");
INSERT INTO tbtypestreet VALUES("279","PRAIRIE","PR","en_US");
INSERT INTO tbtypestreet VALUES("280","RADIAL","RADL","en_US");
INSERT INTO tbtypestreet VALUES("281","RAMP","RAMP","en_US");
INSERT INTO tbtypestreet VALUES("282","RANCH","RNCH","en_US");
INSERT INTO tbtypestreet VALUES("283","RAPID","RPD","en_US");
INSERT INTO tbtypestreet VALUES("284","RAPIDS","RPDS","en_US");
INSERT INTO tbtypestreet VALUES("285","REST","RST","en_US");
INSERT INTO tbtypestreet VALUES("286","RIDGE","RDG","en_US");
INSERT INTO tbtypestreet VALUES("287","RIDGES","RDGS","en_US");
INSERT INTO tbtypestreet VALUES("288","RIVER","RIV","en_US");
INSERT INTO tbtypestreet VALUES("289","ROAD","RD","en_US");
INSERT INTO tbtypestreet VALUES("290","ROADS","RDS","en_US");
INSERT INTO tbtypestreet VALUES("291","ROUTE","RTE","en_US");
INSERT INTO tbtypestreet VALUES("292","ROW","ROW","en_US");
INSERT INTO tbtypestreet VALUES("293","RUE","RUE","en_US");
INSERT INTO tbtypestreet VALUES("294","RUN","RUN","en_US");
INSERT INTO tbtypestreet VALUES("295","SHOAL","SHL","en_US");
INSERT INTO tbtypestreet VALUES("296","SHOALS","SHLS","en_US");
INSERT INTO tbtypestreet VALUES("297","SHORE","SHR","en_US");
INSERT INTO tbtypestreet VALUES("298","SHORES","SHRS","en_US");
INSERT INTO tbtypestreet VALUES("299","SKYWAY","SKWY","en_US");
INSERT INTO tbtypestreet VALUES("300","SPRING","SPG","en_US");
INSERT INTO tbtypestreet VALUES("301","SPRINGS","SPGS","en_US");
INSERT INTO tbtypestreet VALUES("302","SPUR","SPUR","en_US");
INSERT INTO tbtypestreet VALUES("303","SPURS","SPUR","en_US");
INSERT INTO tbtypestreet VALUES("304","SQUARE","SQ","en_US");
INSERT INTO tbtypestreet VALUES("305","SQUARES","SQS","en_US");
INSERT INTO tbtypestreet VALUES("306","STATION","STA","en_US");
INSERT INTO tbtypestreet VALUES("307","STRAVENUE","STRA","en_US");
INSERT INTO tbtypestreet VALUES("308","STREAM","STRM","en_US");
INSERT INTO tbtypestreet VALUES("309","STREET","ST","en_US");
INSERT INTO tbtypestreet VALUES("310","STREETS","STS","en_US");
INSERT INTO tbtypestreet VALUES("311","SUMMIT","SMT","en_US");
INSERT INTO tbtypestreet VALUES("312","TERRACE","TER","en_US");
INSERT INTO tbtypestreet VALUES("313","THROUGHWAY","TRWY","en_US");
INSERT INTO tbtypestreet VALUES("314","TRACE","TRCE","en_US");
INSERT INTO tbtypestreet VALUES("315","TRACK","TRAK","en_US");
INSERT INTO tbtypestreet VALUES("316","TRAFFICWAY","TRFY","en_US");
INSERT INTO tbtypestreet VALUES("317","TRAIL","TRL","en_US");
INSERT INTO tbtypestreet VALUES("318","TRAILER","TRLR","en_US");
INSERT INTO tbtypestreet VALUES("319","TUNNEL","TUNL","en_US");
INSERT INTO tbtypestreet VALUES("320","TURNPIKE","TPKE","en_US");
INSERT INTO tbtypestreet VALUES("321","UNDERPASS","UPAS","en_US");
INSERT INTO tbtypestreet VALUES("322","UNION","UN","en_US");
INSERT INTO tbtypestreet VALUES("323","UNIONS","UNS","en_US");
INSERT INTO tbtypestreet VALUES("324","VALLEY","VLY","en_US");
INSERT INTO tbtypestreet VALUES("325","VALLEYS","VLYS","en_US");
INSERT INTO tbtypestreet VALUES("326","VIADUCT","VIA","en_US");
INSERT INTO tbtypestreet VALUES("327","VIEW","VW","en_US");
INSERT INTO tbtypestreet VALUES("328","VIEWS","VWS","en_US");
INSERT INTO tbtypestreet VALUES("329","VILLAGE","VLG","en_US");
INSERT INTO tbtypestreet VALUES("330","VILLAGES","VLGS","en_US");
INSERT INTO tbtypestreet VALUES("331","VILLE","VL","en_US");
INSERT INTO tbtypestreet VALUES("332","VISTA","VIS","en_US");
INSERT INTO tbtypestreet VALUES("333","WALK","WALK","en_US");
INSERT INTO tbtypestreet VALUES("334","WALKS","WALK","en_US");
INSERT INTO tbtypestreet VALUES("335","WALL","WALL","en_US");
INSERT INTO tbtypestreet VALUES("336","WAY","WAY","en_US");
INSERT INTO tbtypestreet VALUES("337","WAYS","WAYS","en_US");
INSERT INTO tbtypestreet VALUES("338","WELL","WL","en_US");
INSERT INTO tbtypestreet VALUES("339","WELLS","WLS","en_US");



DROP TABLE IF EXISTS tbvocabulary;

CREATE TABLE `tbvocabulary` (
  `idvocabulary` int(11) NOT NULL AUTO_INCREMENT,
  `idlocale` int(11) DEFAULT NULL,
  `idmodule` int(11) DEFAULT NULL,
  `key_name` varchar(50) DEFAULT 'NULL',
  `key_value` varchar(255) DEFAULT NULL,
  `status` char(1) DEFAULT 'A',
  PRIMARY KEY (`idvocabulary`),
  UNIQUE KEY `UK_tbvocabulary_key` (`idlocale`,`key_name`),
  KEY `FK_tbvocabulary_tbmodule_idmodule` (`idmodule`),
  CONSTRAINT `FK_tbvocabulary_tblocale_idlocale` FOREIGN KEY (`idlocale`) REFERENCES `tblocale` (`idlocale`),
  CONSTRAINT `FK_tbvocabulary_tbmodule_idmodule` FOREIGN KEY (`idmodule`) REFERENCES `tbmodule` (`idmodule`)
) ENGINE=InnoDB AUTO_INCREMENT=2079 DEFAULT CHARSET=latin1;

INSERT INTO tbvocabulary VALUES("1","77","2","adm_Navbar_name","Admin","A");
INSERT INTO tbvocabulary VALUES("2","77","2","APP_apiUrlLabel","Url da API","A");
INSERT INTO tbvocabulary VALUES("3","77","2","APP_areaLabel","Área","A");
INSERT INTO tbvocabulary VALUES("4","77","2","APP_attachLabel","Anexo","A");
INSERT INTO tbvocabulary VALUES("5","77","2","APP_btnConfirm","Confirmar","A");
INSERT INTO tbvocabulary VALUES("6","77","2","APP_btnLogin","ENTRE","A");
INSERT INTO tbvocabulary VALUES("7","77","2","APP_cancelButton","Cancelar","A");
INSERT INTO tbvocabulary VALUES("8","77","2","APP_changePassButton","Alterar Senha","A");
INSERT INTO tbvocabulary VALUES("9","77","2","APP_ChangePassword_title","Nova Senha","A");
INSERT INTO tbvocabulary VALUES("10","77","2","APP_cityLabel","Cidade","A");
INSERT INTO tbvocabulary VALUES("11","77","2","APP_companyLabel","Empresa","A");
INSERT INTO tbvocabulary VALUES("12","77","2","APP_configPage","Configurações","A");
INSERT INTO tbvocabulary VALUES("13","77","2","APP_Configuration_title","Configurações","A");
INSERT INTO tbvocabulary VALUES("14","77","2","APP_confirmButton","Confirmar","A");
INSERT INTO tbvocabulary VALUES("15","77","2","APP_descriptionLabel","Descrição","A");
INSERT INTO tbvocabulary VALUES("16","77","2","APP_exitLink","Sair","A");
INSERT INTO tbvocabulary VALUES("17","77","2","APP_GetUrl_title","Seja bem-vindo!","A");
INSERT INTO tbvocabulary VALUES("18","77","2","APP_homePage","Início","A");
INSERT INTO tbvocabulary VALUES("19","77","2","APP_Home_title","Tickets Abertos","A");
INSERT INTO tbvocabulary VALUES("20","77","2","APP_inChargeLabel","Departamento","A");
INSERT INTO tbvocabulary VALUES("21","77","2","APP_itemLabel","Item","A");
INSERT INTO tbvocabulary VALUES("22","77","2","APP_keep","Manter-se conectado","A");
INSERT INTO tbvocabulary VALUES("23","77","2","APP_Login_title","Login","A");
INSERT INTO tbvocabulary VALUES("24","77","2","APP_nameLabel","Nome","A");
INSERT INTO tbvocabulary VALUES("25","77","2","APP_newAttachLabel","Anexo","A");
INSERT INTO tbvocabulary VALUES("26","77","2","APP_newNoteTitle","Inserir Apontamento","A");
INSERT INTO tbvocabulary VALUES("27","77","2","APP_newRequestButton","Novo Ticket","A");
INSERT INTO tbvocabulary VALUES("28","77","2","APP_NewTicket_title","Novo Ticket","A");
INSERT INTO tbvocabulary VALUES("29","77","2","APP_notesTitle","Apontamentos","A");
INSERT INTO tbvocabulary VALUES("30","77","2","APP_originLabel","Origem","A");
INSERT INTO tbvocabulary VALUES("31","77","2","APP_passLabel","Digite sua nova senha","A");
INSERT INTO tbvocabulary VALUES("32","77","2","APP_passPlaceholder","Senha","A");
INSERT INTO tbvocabulary VALUES("33","77","2","APP_passRequired","É obrigatório inserir a senha.","A");
INSERT INTO tbvocabulary VALUES("34","77","2","APP_phoneLabel","Telefone","A");
INSERT INTO tbvocabulary VALUES("35","77","2","APP_reasonLabel","Razão","A");
INSERT INTO tbvocabulary VALUES("36","77","2","APP_rememberPass","Esqueceu a senha?","A");
INSERT INTO tbvocabulary VALUES("37","77","2","APP_RememberPassword_title","Esqueceu a Senha","A");
INSERT INTO tbvocabulary VALUES("38","77","2","APP_requestDate","Prazo Final","A");
INSERT INTO tbvocabulary VALUES("39","77","2","APP_requireArea","É obrigatório selecionar uma área.","A");
INSERT INTO tbvocabulary VALUES("40","77","2","APP_requireDescription","É obrigatório inserir uma descrição.","A");
INSERT INTO tbvocabulary VALUES("41","77","2","APP_requireEqualPass","As senhas devem ser iguais.","A");
INSERT INTO tbvocabulary VALUES("42","77","2","APP_requireItem","É obrigatório selecionar um item.","A");
INSERT INTO tbvocabulary VALUES("43","77","2","APP_requirePass","É obrigatório inserir a senha.","A");
INSERT INTO tbvocabulary VALUES("44","77","2","APP_requireReason","É obrigatório selecionar uma razão.","A");
INSERT INTO tbvocabulary VALUES("45","77","2","APP_requireService","É obrigatório selecionar um serviço.","A");
INSERT INTO tbvocabulary VALUES("46","77","2","APP_requireTitle","É obrigatorio inserir um título.","A");
INSERT INTO tbvocabulary VALUES("47","77","2","APP_requireType","É obrigatório selecionar um tipo.","A");
INSERT INTO tbvocabulary VALUES("48","77","2","APP_requireUser","É obrigatório inserir o usuário.","A");
INSERT INTO tbvocabulary VALUES("49","77","2","APP_sendButton","Enviar","A");
INSERT INTO tbvocabulary VALUES("50","77","2","APP_serviceLabel","Serviço","A");
INSERT INTO tbvocabulary VALUES("51","77","2","APP_ShowTicket_title","Solicitação","A");
INSERT INTO tbvocabulary VALUES("52","77","2","APP_stateLabel","Estado","A");
INSERT INTO tbvocabulary VALUES("53","77","2","APP_statusLabel","Status","A");
INSERT INTO tbvocabulary VALUES("54","77","2","APP_titleLabel","Título","A");
INSERT INTO tbvocabulary VALUES("55","77","2","APP_typeLabel","Tipo","A");
INSERT INTO tbvocabulary VALUES("56","77","2","APP_urlLabel","Url da API","A");
INSERT INTO tbvocabulary VALUES("57","77","2","APP_urlPlaceholder","https://...","A");
INSERT INTO tbvocabulary VALUES("58","77","2","APP_urlRequired","É Obrigatório inserir a URL.","A");
INSERT INTO tbvocabulary VALUES("59","77","2","APP_userLabel","Digite seu nome de usuário","A");
INSERT INTO tbvocabulary VALUES("60","77","2","APP_userPlaceholder","Usuário","A");
INSERT INTO tbvocabulary VALUES("61","77","2","APP_userRequired","É obrigatório inserir o usuário.","A");
INSERT INTO tbvocabulary VALUES("62","77","2","hdk_Navbar_name","HelpDEZK","A");
INSERT INTO tbvocabulary VALUES("63","77","2","pgr_cost_center","Centro de Custos","A");
INSERT INTO tbvocabulary VALUES("64","77","2","pgr_dash_widgets","Widgets","A");
INSERT INTO tbvocabulary VALUES("65","77","2","pgr_downloads","Downloads","A");
INSERT INTO tbvocabulary VALUES("66","77","2","pgr_email_config","Configura&ccedil;&atilde;o de Emails","A");
INSERT INTO tbvocabulary VALUES("67","77","2","pgr_email_request","Solicitações por Email","A");
INSERT INTO tbvocabulary VALUES("68","77","2","pgr_evaluation","Avalia&ccedil;&atilde;o de Atendimento","A");
INSERT INTO tbvocabulary VALUES("69","77","2","pgr_groups","Grupos","A");
INSERT INTO tbvocabulary VALUES("70","77","2","pgr_holidays","Feriados","A");
INSERT INTO tbvocabulary VALUES("71","77","2","pgr_import_people","Importa&ccedil;&atilde;o de Usu&aacute;rios","A");
INSERT INTO tbvocabulary VALUES("72","77","2","pgr_import_services","Importar Cat&aacute;logo de Servi&ccedil;os","A");
INSERT INTO tbvocabulary VALUES("73","77","2","pgr_logos","Logos","A");
INSERT INTO tbvocabulary VALUES("74","77","2","pgr_modules","Módulos","A");
INSERT INTO tbvocabulary VALUES("75","77","2","pgr_ope_aver_resptime","Tempo de resposta por atendente","A");
INSERT INTO tbvocabulary VALUES("76","77","2","pgr_people","Pessoas & Empresas","A");
INSERT INTO tbvocabulary VALUES("77","77","2","pgr_person_report","Relat&oacute;rio de Pessoas","A");
INSERT INTO tbvocabulary VALUES("78","77","2","pgr_priority","Prioridade","A");
INSERT INTO tbvocabulary VALUES("79","77","2","pgr_programs","Programas","A");
INSERT INTO tbvocabulary VALUES("80","77","2","pgr_rejects_request","Solicita&ccedil;&otilde;es Rejeitadas","A");
INSERT INTO tbvocabulary VALUES("81","77","2","pgr_request_department","Solicitações por Departamentos","A");
INSERT INTO tbvocabulary VALUES("82","77","2","pgr_request_operator","Solicita&ccedil;&otilde;es por Atendente","A");
INSERT INTO tbvocabulary VALUES("83","77","2","pgr_request_status","Solicita&ccedil;&otilde;es por Status","A");
INSERT INTO tbvocabulary VALUES("84","77","2","pgr_req_reason","Motivo de Abertura","A");
INSERT INTO tbvocabulary VALUES("85","77","2","pgr_req_reports","Relat&oacute;rio de Solicita&ccedil;&otilde;es","A");
INSERT INTO tbvocabulary VALUES("86","77","2","pgr_services","Servi&ccedil;os","A");
INSERT INTO tbvocabulary VALUES("87","77","2","pgr_status","Status","A");
INSERT INTO tbvocabulary VALUES("88","77","2","pgr_summarized_department","Resumido por Departamento","A");
INSERT INTO tbvocabulary VALUES("89","77","2","pgr_summarized_operator","Resumido por Atendente","A");
INSERT INTO tbvocabulary VALUES("90","77","2","pgr_sys_features","Funcionalidades do Sistema","A");
INSERT INTO tbvocabulary VALUES("91","77","2","pgr_type_permission","Permiss&atilde;o por Tipo de Pessoa","A");
INSERT INTO tbvocabulary VALUES("92","77","2","pgr_user_satisfaction","Satisfa&ccedil;&atilde;o do Usu&aacute;rio","A");
INSERT INTO tbvocabulary VALUES("93","77","2","pgr_vocabulary","Vocabulário","A");
INSERT INTO tbvocabulary VALUES("94","77","2","pgr_warnings","Avisos","A");
INSERT INTO tbvocabulary VALUES("95","77","2","pgr_worked_requests","Solicita&ccedil;&otilde;es Trabalhadas","A");
INSERT INTO tbvocabulary VALUES("96","77","2","pgr_work_calendar","Calend&aacute;rio de Trabalho","A");
INSERT INTO tbvocabulary VALUES("97","77","2","Abbreviation","Sigla","A");
INSERT INTO tbvocabulary VALUES("98","77","2","Abilities","Habilidades","A");
INSERT INTO tbvocabulary VALUES("99","77","2","Access","Accessar","A");
INSERT INTO tbvocabulary VALUES("100","77","2","Access_denied","Acesso negado.","A");
INSERT INTO tbvocabulary VALUES("101","77","2","Acess_level","N&iacute;vel de Acesso","A");
INSERT INTO tbvocabulary VALUES("102","77","2","Activate","Ativar","A");
INSERT INTO tbvocabulary VALUES("103","77","2","Add","Adicionar","A");
INSERT INTO tbvocabulary VALUES("104","77","2","Added_notes","Apontamentos Inclusos","A");
INSERT INTO tbvocabulary VALUES("105","77","2","address","Endereço","A");
INSERT INTO tbvocabulary VALUES("106","77","2","Add_category","Adicionar Categoria","A");
INSERT INTO tbvocabulary VALUES("107","77","2","Add_item","Adicionar Item","A");
INSERT INTO tbvocabulary VALUES("108","77","2","add_new_feature","Adicionar Nova Funcionalidade","A");
INSERT INTO tbvocabulary VALUES("109","77","2","Add_service","Adicionar Servi&ccedil;o","A");
INSERT INTO tbvocabulary VALUES("110","77","2","Add_widget","Adicionar Widget","A");
INSERT INTO tbvocabulary VALUES("111","77","2","admin_dashboard","Dashboard Administrativo","A");
INSERT INTO tbvocabulary VALUES("112","77","2","adodb_version","Versão do Adodb:","A");
INSERT INTO tbvocabulary VALUES("113","77","2","Adress","Endereço","A");
INSERT INTO tbvocabulary VALUES("114","77","2","Alert_activated","Ativado com sucesso.","A");
INSERT INTO tbvocabulary VALUES("115","77","2","Alert_activated_error","Falha ao ativar.","A");
INSERT INTO tbvocabulary VALUES("116","77","2","Alert_add_config_categ_title","Não esqueça de acrescentar a entrada da categoria em \'app/lang/[idioma].txt\'","A");
INSERT INTO tbvocabulary VALUES("117","77","2","Alert_add_feature_title","Não esqueça de acrescentar a entrada da funcionalidade em \'app/lang/[idioma].txt\'","A");
INSERT INTO tbvocabulary VALUES("118","77","2","Alert_add_module_title","Não esqueça de acrescentar a entrada do módulo em \'app/lang/[idioma].txt\'","A");
INSERT INTO tbvocabulary VALUES("119","77","2","Alert_add_program_title","Não esqueça de acrescentar a entrada do programa em \'app/lang/[idioma].txt\'","A");
INSERT INTO tbvocabulary VALUES("120","77","2","Alert_approve","Você tem solicitações aguardando aprovação, quer aprova-las agora?","A");
INSERT INTO tbvocabulary VALUES("121","77","2","Alert_Cancel_sucess","Solicitação cancelada com sucesso","A");
INSERT INTO tbvocabulary VALUES("122","77","2","Alert_change_password","Senha alterada com sucesso","A");
INSERT INTO tbvocabulary VALUES("123","77","2","Alert_choose_area","Escolha a Área","A");
INSERT INTO tbvocabulary VALUES("124","77","2","Alert_choose_item","Escolha o Item","A");
INSERT INTO tbvocabulary VALUES("125","77","2","Alert_choose_service","Escolha o Serviço","A");
INSERT INTO tbvocabulary VALUES("126","77","2","Alert_choose_type","Escolha o Tipo","A");
INSERT INTO tbvocabulary VALUES("127","77","2","Alert_close_request","Solicitação encerrada com sucesso","A");
INSERT INTO tbvocabulary VALUES("128","77","2","Alert_deactivated","Desativado com sucesso.","A");
INSERT INTO tbvocabulary VALUES("129","77","2","Alert_deactivated_error","Falha ao desativar.","A");
INSERT INTO tbvocabulary VALUES("130","77","2","Alert_deleted","Deletado com sucesso.","A");
INSERT INTO tbvocabulary VALUES("131","77","2","Alert_deleted_error","Falha ao deletar.","A");
INSERT INTO tbvocabulary VALUES("132","77","2","Alert_deleted_note","Apontamento excluido","A");
INSERT INTO tbvocabulary VALUES("133","77","2","Alert_department_person","Este Departamento possui usuários vinculados.<br> Para deletá-lo, é necessário mover os usuários para um novo departamento.  <br>Selecione o Departamento destino.","A");
INSERT INTO tbvocabulary VALUES("134","77","2","Alert_different_passwords","Senhas diferentes","A");
INSERT INTO tbvocabulary VALUES("135","77","2","Alert_dont_delete_area","Esta área está vinculada a uma ou a várias solicitações.<br> Não é possível realizar esta operação.","A");
INSERT INTO tbvocabulary VALUES("136","77","2","Alert_dont_delete_item","Este item está vinculado a uma ou a várias solicitações.<br> Não é possível realizar esta operação.","A");
INSERT INTO tbvocabulary VALUES("137","77","2","Alert_dont_delete_service","Este serviço está vinculado a uma ou a várias solicitações.<br> Não é possível realizar esta operação.","A");
INSERT INTO tbvocabulary VALUES("138","77","2","Alert_dont_delete_type","Este tipo está vinculado a uma ou a várias solicitações.<br> Não é possível realizar esta operação.","A");
INSERT INTO tbvocabulary VALUES("139","77","2","Alert_empty_note","Preencha o corpo do apontamento","A");
INSERT INTO tbvocabulary VALUES("140","77","2","Alert_empty_reason","Preencha o motivo","A");
INSERT INTO tbvocabulary VALUES("141","77","2","Alert_empty_subject","Preencha o assunto da solicita&ccedil;&atilde;o","A");
INSERT INTO tbvocabulary VALUES("142","77","2","Alert_external_settings_OK","Configurações Externas Salvas com Sucesso !!!","A");
INSERT INTO tbvocabulary VALUES("143","77","2","Alert_failure","Não foi possivel inserir","A");
INSERT INTO tbvocabulary VALUES("144","77","2","Alert_field_required","Este campo é obrigatório","A");
INSERT INTO tbvocabulary VALUES("145","77","2","Alert_follow_repass","Escolha uma das opções sobre o acompanhamento.","A");
INSERT INTO tbvocabulary VALUES("146","77","2","Alert_get_data","Não foi possível obter os dados","A");
INSERT INTO tbvocabulary VALUES("147","77","2","Alert_import_services_nofile_failure","Não foi possível importar !! Anexe o arquivo .","A");
INSERT INTO tbvocabulary VALUES("148","77","2","Alert_inserted","Inserido com sucesso","A");
INSERT INTO tbvocabulary VALUES("149","77","2","Alert_invalid_email","Formato de E-mail inválido","A");
INSERT INTO tbvocabulary VALUES("150","77","2","Alert_note_sucess","Apontamento inserido com sucesso","A");
INSERT INTO tbvocabulary VALUES("151","77","2","Alert_not_match_new_pass","A nova senha não pode ser igual à senha atual!","A");
INSERT INTO tbvocabulary VALUES("152","77","2","Alert_reopen_sucess","Solicitação Reaberta com sucesso","A");
INSERT INTO tbvocabulary VALUES("153","77","2","Alert_select_one","Por favor, selecione 1 item","A");
INSERT INTO tbvocabulary VALUES("154","77","2","Alert_success_update","Dados atualizados com sucesso","A");
INSERT INTO tbvocabulary VALUES("155","77","2","Alert_sucess_category","Categoria cadastrada com sucesso","A");
INSERT INTO tbvocabulary VALUES("156","77","2","Alert_sucess_module","Módulo cadastrado com sucesso","A");
INSERT INTO tbvocabulary VALUES("157","77","2","Alert_sucess_repass","Repassada com sucesso!","A");
INSERT INTO tbvocabulary VALUES("158","77","2","Alert_wrong_extension_csv","Extensão de arquivo inválido. É permitido apenas arquivos com extensão CSV.","A");
INSERT INTO tbvocabulary VALUES("159","77","2","all","Todos","A");
INSERT INTO tbvocabulary VALUES("160","77","2","and","e","A");
INSERT INTO tbvocabulary VALUES("161","77","2","Approve_no","Não.","A");
INSERT INTO tbvocabulary VALUES("162","77","2","Approve_obs","Sim, com observações.","A");
INSERT INTO tbvocabulary VALUES("163","77","2","Approve_text","Você aprova o atendimento do atendente?","A");
INSERT INTO tbvocabulary VALUES("164","77","2","Approve_yes","Sim.","A");
INSERT INTO tbvocabulary VALUES("165","77","2","April","Abril","A");
INSERT INTO tbvocabulary VALUES("166","77","2","Area","&Aacute;rea","A");
INSERT INTO tbvocabulary VALUES("167","77","2","Area_edit","Editar &Aacute;rea","A");
INSERT INTO tbvocabulary VALUES("168","77","2","Area_insert","Cadastrar &Aacute;rea","A");
INSERT INTO tbvocabulary VALUES("169","77","2","Area_name","Nome da &Aacute;rea","A");
INSERT INTO tbvocabulary VALUES("170","77","2","Assumed_successfully","Assumida com sucesso!","A");
INSERT INTO tbvocabulary VALUES("171","77","2","Assume_request","Assumir Solicita&ccedil;&atilde;o","A");
INSERT INTO tbvocabulary VALUES("172","77","2","Attach","Anexar","A");
INSERT INTO tbvocabulary VALUES("173","77","2","Attachments","Anexos","A");
INSERT INTO tbvocabulary VALUES("174","77","2","Attendance","Atendimento","A");
INSERT INTO tbvocabulary VALUES("175","77","2","Attendance_time","Tempo para Atendimento","A");
INSERT INTO tbvocabulary VALUES("176","77","2","Attendants_by_group","Visualizar atendentes por grupo","A");
INSERT INTO tbvocabulary VALUES("177","77","2","Attend_level","N&iacute;vel de Atendimento","A");
INSERT INTO tbvocabulary VALUES("178","77","2","Attend_time","Tempo de Atendimento","A");
INSERT INTO tbvocabulary VALUES("179","77","2","Att_way","Fornecedor","A");
INSERT INTO tbvocabulary VALUES("180","77","2","Att_way_min","Fornecedor","A");
INSERT INTO tbvocabulary VALUES("181","77","2","Att_way_new","Novo Tipo de Atendimento","A");
INSERT INTO tbvocabulary VALUES("182","77","2","August","Agosto","A");
INSERT INTO tbvocabulary VALUES("183","77","2","auxiliary_operator_include","Incluir Atendente Auxiliar","A");
INSERT INTO tbvocabulary VALUES("184","77","2","Available","Dispon&iacute;vel","A");
INSERT INTO tbvocabulary VALUES("185","77","2","Available_for","Dispon&iacute;vel para","A");
INSERT INTO tbvocabulary VALUES("186","77","2","Available_text","D&iacute;sponivel para cadastro na solicita&ccedil;&atilde;o.","A");
INSERT INTO tbvocabulary VALUES("187","77","2","Average","Moderada","A");
INSERT INTO tbvocabulary VALUES("188","77","2","Back_btn","Voltar","A");
INSERT INTO tbvocabulary VALUES("189","77","2","Bar_hide","Esconder Barra","A");
INSERT INTO tbvocabulary VALUES("190","77","2","Bar_show","Mostrar Barra","A");
INSERT INTO tbvocabulary VALUES("191","77","2","Birth_date","Nascimento","A");
INSERT INTO tbvocabulary VALUES("192","77","2","Branch","Ramal","A");
INSERT INTO tbvocabulary VALUES("193","77","2","btn_assume","Assumir","A");
INSERT INTO tbvocabulary VALUES("194","77","2","btn_cancel","Cancela","A");
INSERT INTO tbvocabulary VALUES("195","77","2","btn_close","Encerrar","A");
INSERT INTO tbvocabulary VALUES("196","77","2","Btn_evaluate","Avaliar","A");
INSERT INTO tbvocabulary VALUES("197","77","2","btn_ope_aux","Atendente Auxiliar","A");
INSERT INTO tbvocabulary VALUES("198","77","2","btn_reject","Rejeitar","A");
INSERT INTO tbvocabulary VALUES("199","77","2","btn_reopen","Reabrir","A");
INSERT INTO tbvocabulary VALUES("200","77","2","btn_save_changes","Salvar Altera&ccedil;&otilde;es","A");
INSERT INTO tbvocabulary VALUES("201","77","2","btn_submit","Enviar","A");
INSERT INTO tbvocabulary VALUES("202","77","2","btn_update_userdata","Atualize seus dados","A");
INSERT INTO tbvocabulary VALUES("203","77","2","By_company","por empresa","A");
INSERT INTO tbvocabulary VALUES("204","77","2","By_group","por grupo","A");
INSERT INTO tbvocabulary VALUES("205","77","2","Cancel_btn","Cancelar","A");
INSERT INTO tbvocabulary VALUES("206","77","2","Categories","Categorias","A");
INSERT INTO tbvocabulary VALUES("207","77","2","Category","Categoria","A");
INSERT INTO tbvocabulary VALUES("208","77","2","Category_insert","Inserir Categoria","A");
INSERT INTO tbvocabulary VALUES("209","77","2","cat_config","Config","A");
INSERT INTO tbvocabulary VALUES("210","77","2","cat_records","Cadastros","A");
INSERT INTO tbvocabulary VALUES("211","77","2","cat_reports","Relat&oacute;rios","A");
INSERT INTO tbvocabulary VALUES("212","77","2","Change","Alterar","A");
INSERT INTO tbvocabulary VALUES("213","77","2","Change_date","Alterar Data","A");
INSERT INTO tbvocabulary VALUES("214","77","2","Change_password","Alterar Senha","A");
INSERT INTO tbvocabulary VALUES("215","77","2","Change_password_required","Obrigar usuário alterar senha.","A");
INSERT INTO tbvocabulary VALUES("216","77","2","Change_permissions","Alterar Permiss&otilde;es","A");
INSERT INTO tbvocabulary VALUES("217","77","2","Choose_format","Escolha o formato que você deseja exportar o relatório.","A");
INSERT INTO tbvocabulary VALUES("218","77","2","City","Cidade","A");
INSERT INTO tbvocabulary VALUES("219","77","2","Classification","Classifica&ccedil;&atilde;o","A");
INSERT INTO tbvocabulary VALUES("220","77","2","Classification_text","Configurar este item como \'sem classifica&ccedil;&atilde;o\'.","A");
INSERT INTO tbvocabulary VALUES("221","77","2","Client","Cliente","A");
INSERT INTO tbvocabulary VALUES("222","77","2","Close","Fechar","A");
INSERT INTO tbvocabulary VALUES("223","77","2","Closed","Encerrados","A");
INSERT INTO tbvocabulary VALUES("224","77","2","Code","C&oacute;digo","A");
INSERT INTO tbvocabulary VALUES("225","77","2","Color","Cor","A");
INSERT INTO tbvocabulary VALUES("226","77","2","Company","Empresa","A");
INSERT INTO tbvocabulary VALUES("227","77","2","Complement","Complemento","A");
INSERT INTO tbvocabulary VALUES("228","77","2","Confirm_close","Encerrar solicitação?","A");
INSERT INTO tbvocabulary VALUES("229","77","2","Confirm_password","Repita a Senha","A");
INSERT INTO tbvocabulary VALUES("230","77","2","conf_approvals","Configurar Aprovações","A");
INSERT INTO tbvocabulary VALUES("231","77","2","Contact_person","Pessoa para contato","A");
INSERT INTO tbvocabulary VALUES("232","77","2","Controller","Controller","A");
INSERT INTO tbvocabulary VALUES("233","77","2","copy","Copiar","A");
INSERT INTO tbvocabulary VALUES("234","77","2","Country","Pa&iacute;s","A");
INSERT INTO tbvocabulary VALUES("235","77","2","country_default","País Padrão","A");
INSERT INTO tbvocabulary VALUES("236","77","2","cpf","CPF","A");
INSERT INTO tbvocabulary VALUES("237","77","2","Create_user","Criar usu&aacute;rio","A");
INSERT INTO tbvocabulary VALUES("238","77","2","Create_user_msg","Criar novo usuário, caso não exista no Helpdezk.","A");
INSERT INTO tbvocabulary VALUES("239","77","2","Current_date","Data Atual","A");
INSERT INTO tbvocabulary VALUES("240","77","2","Current_time","Hora Atual","A");
INSERT INTO tbvocabulary VALUES("241","77","2","Dashboard","Dashboard","A");
INSERT INTO tbvocabulary VALUES("242","77","2","Dashboard_SLAFulfillment","Cumprido","A");
INSERT INTO tbvocabulary VALUES("243","77","2","Dashboard_SLANotFulfillment","N&atilde;o cumprido","A");
INSERT INTO tbvocabulary VALUES("244","77","2","Dashboard_UpdatedDaily","Atualizado diariamente","A");
INSERT INTO tbvocabulary VALUES("245","77","2","Date","Data","A");
INSERT INTO tbvocabulary VALUES("246","77","2","Day","dia","A");
INSERT INTO tbvocabulary VALUES("247","77","2","Days","Dias","A");
INSERT INTO tbvocabulary VALUES("248","77","2","Deactivate","Desativar","A");
INSERT INTO tbvocabulary VALUES("249","77","2","December","Dezembro","A");
INSERT INTO tbvocabulary VALUES("250","77","2","Default","Padr&atilde;o","A");
INSERT INTO tbvocabulary VALUES("251","77","2","Default_department","Depart. padrão","A");
INSERT INTO tbvocabulary VALUES("252","77","2","Default_department_msg","você pode adicionar mais departamentos no programa Inserir Departamento","A");
INSERT INTO tbvocabulary VALUES("253","77","2","Default_text","Ao abrir uma nova solicita&ccedil;&atilde;o usar este Item como padr&atilde;o.","A");
INSERT INTO tbvocabulary VALUES("254","77","2","Delete","Deletar","A");
INSERT INTO tbvocabulary VALUES("255","77","2","Delete_emails","Deletar emails","A");
INSERT INTO tbvocabulary VALUES("256","77","2","Delete_emails_msg","Deletar emails quando baixar do servidor.","A");
INSERT INTO tbvocabulary VALUES("257","77","2","Delete_module","Tem certeza de que deseja excluir este módulo?","A");
INSERT INTO tbvocabulary VALUES("258","77","2","Delete_record","Tem certeza de que deseja excluir este cadastro?","A");
INSERT INTO tbvocabulary VALUES("259","77","2","Delete_widget","Tem certeza de que deseja excluir este widget?","A");
INSERT INTO tbvocabulary VALUES("260","77","2","Delimiter","Separador","A");
INSERT INTO tbvocabulary VALUES("261","77","2","Department","Departamento","A");
INSERT INTO tbvocabulary VALUES("262","77","2","Departments","Departamentos","A");
INSERT INTO tbvocabulary VALUES("263","77","2","Department_exists","Departamento j&aacute; cadastrado!","A");
INSERT INTO tbvocabulary VALUES("264","77","2","Department_name","Nome do Departamento","A");
INSERT INTO tbvocabulary VALUES("265","77","2","Description","Descri&ccedil;&atilde;o","A");
INSERT INTO tbvocabulary VALUES("266","77","2","Domain","Dom&iacute;nio","A");
INSERT INTO tbvocabulary VALUES("267","77","2","Downloads","Downloads","A");
INSERT INTO tbvocabulary VALUES("268","77","2","Drag_image_msg","Arraste<br> o arquivo<br> com a imagem<br> ou<br> clique aqui.","A");
INSERT INTO tbvocabulary VALUES("269","77","2","Drag_import_file_msg","<br>Arraste o arquivo com os dados <br> ou <br> clique aqui.","A");
INSERT INTO tbvocabulary VALUES("270","77","2","Drag_widget","Arraste seus widgets aqui","A");
INSERT INTO tbvocabulary VALUES("271","77","2","dropzone_File_Too_Big","Excedeu o tamanho máximo {{filesize}}","A");
INSERT INTO tbvocabulary VALUES("272","77","2","dropzone_invalid_dimension","A imagem tem que ser quadrada","A");
INSERT INTO tbvocabulary VALUES("273","77","2","dropzone_remove_file","Remover arquivo","A");
INSERT INTO tbvocabulary VALUES("274","77","2","dropzone_user_photot_message","<br>Para atualizar sua foto, <br> arraste o arquivo com a imagem<br> ou<br> clique aqui.<br><br>A imagem tem que ser quadrada !!! ","A");
INSERT INTO tbvocabulary VALUES("275","77","2","dsh_installer_dir","Diretório installer","A");
INSERT INTO tbvocabulary VALUES("276","77","2","dsh_msg_installer","Por questões de segurança, por favor, remova o diretório  <b>installer/</b> do seu servidor!!!","A");
INSERT INTO tbvocabulary VALUES("277","77","2","dsh_warning","Alerta","A");
INSERT INTO tbvocabulary VALUES("278","77","2","edit","Editar","A");
INSERT INTO tbvocabulary VALUES("279","77","2","Editor_Placeholder_description","Insira aqui a descrição da solicitação ...","A");
INSERT INTO tbvocabulary VALUES("280","77","2","Editor_Placeholder_insert","Insira aqui seu apontamento ...","A");
INSERT INTO tbvocabulary VALUES("281","77","2","Editor_Placeholder_reason","Insira aqui o motivo...","A");
INSERT INTO tbvocabulary VALUES("282","77","2","Editor_Placeholder_solution","Insira aqui a solução da solicitação...","A");
INSERT INTO tbvocabulary VALUES("283","77","2","Edit_btn","Editar","A");
INSERT INTO tbvocabulary VALUES("284","77","2","Edit_failure","N&atilde;o foi poss&iacute;vel editar","A");
INSERT INTO tbvocabulary VALUES("285","77","2","Edit_layout","Editar Layout","A");
INSERT INTO tbvocabulary VALUES("286","77","2","Edit_sucess","Editado com sucesso!","A");
INSERT INTO tbvocabulary VALUES("287","77","2","EIN_CNPJ","CNPJ","A");
INSERT INTO tbvocabulary VALUES("288","77","2","email","E-mail","A");
INSERT INTO tbvocabulary VALUES("289","77","2","Email_byCron","Envia os e-mails das solicitações pela cron","A");
INSERT INTO tbvocabulary VALUES("290","77","2","Email_config","Configurar Email","A");
INSERT INTO tbvocabulary VALUES("291","77","2","Email_host","Host de email","A");
INSERT INTO tbvocabulary VALUES("292","77","2","Email_sender","Remetente do email","A");
INSERT INTO tbvocabulary VALUES("293","77","2","Empty","Sem Itens","A");
INSERT INTO tbvocabulary VALUES("294","77","2","Environment_settings","Configurações do Ambiente","A");
INSERT INTO tbvocabulary VALUES("295","77","2","Equipment","Equipamento","A");
INSERT INTO tbvocabulary VALUES("296","77","2","ERP_Code","Código","A");
INSERT INTO tbvocabulary VALUES("297","77","2","ERP_Log","Log","A");
INSERT INTO tbvocabulary VALUES("298","77","2","Error","Erro!","A");
INSERT INTO tbvocabulary VALUES("299","77","2","Error_insert_note","Erro ao inserir apontamento","A");
INSERT INTO tbvocabulary VALUES("300","77","2","Error_Number_columns","N&uacute;mero de colunas na linha % do arquivo inv&aacute;lida. Importa&ccedil;&atilde;o cancelada !!!","A");
INSERT INTO tbvocabulary VALUES("301","77","2","Execution_date","Data de Execu&ccedil;&atilde;o","A");
INSERT INTO tbvocabulary VALUES("302","77","2","Expire_date","Prazo para Atendimento","A");
INSERT INTO tbvocabulary VALUES("303","77","2","Expire_date_sucess","Prazo alterado com sucesso","A");
INSERT INTO tbvocabulary VALUES("304","77","2","Export","Exportar","A");
INSERT INTO tbvocabulary VALUES("305","77","2","external_hostname","Nome do host externo:","A");
INSERT INTO tbvocabulary VALUES("306","77","2","external_ip","Endereço Ip Externo:","A");
INSERT INTO tbvocabulary VALUES("307","77","2","Extra","Extra","A");
INSERT INTO tbvocabulary VALUES("308","77","2","Failure_logs","Logs de erro","A");
INSERT INTO tbvocabulary VALUES("309","77","2","Feature_remove","Remover funcionalidade","A");
INSERT INTO tbvocabulary VALUES("310","77","2","February","Fevereiro","A");
INSERT INTO tbvocabulary VALUES("311","77","2","Female","Feminino","A");
INSERT INTO tbvocabulary VALUES("312","77","2","File","Arquivo","A");
INSERT INTO tbvocabulary VALUES("313","77","2","File_CSV","Arquivo CSV.","A");
INSERT INTO tbvocabulary VALUES("314","77","2","File_PDF","Arquivo PDF.","A");
INSERT INTO tbvocabulary VALUES("315","77","2","File_XLS","Arquivo XLS.","A");
INSERT INTO tbvocabulary VALUES("316","77","2","Fill","Preencher","A");
INSERT INTO tbvocabulary VALUES("317","77","2","Fill_adress","Endere&ccedil;o?","A");
INSERT INTO tbvocabulary VALUES("318","77","2","Filter_by_sender","Filtro por Remetente","A");
INSERT INTO tbvocabulary VALUES("319","77","2","Filter_by_subject","Filtro por Assunto","A");
INSERT INTO tbvocabulary VALUES("320","77","2","Finished_alt","Encerrou","A");
INSERT INTO tbvocabulary VALUES("321","77","2","Finish_btn","Encerrar","A");
INSERT INTO tbvocabulary VALUES("322","77","2","Finish_date","Data Final","A");
INSERT INTO tbvocabulary VALUES("323","77","2","Footer","Rodap&eacute;","A");
INSERT INTO tbvocabulary VALUES("324","77","2","Friday","Sexta-Feira","A");
INSERT INTO tbvocabulary VALUES("325","77","2","From","De","A");
INSERT INTO tbvocabulary VALUES("326","77","2","Gender","Sexo","A");
INSERT INTO tbvocabulary VALUES("327","77","2","Generated","Gerado","A");
INSERT INTO tbvocabulary VALUES("328","77","2","grd_expired","Vencidas","A");
INSERT INTO tbvocabulary VALUES("329","77","2","grd_expired_n_assumed","Vencidas n&atilde;o assumidas","A");
INSERT INTO tbvocabulary VALUES("330","77","2","grd_expiring","Vencendo","A");
INSERT INTO tbvocabulary VALUES("331","77","2","grd_expiring_today","Vencendo Hoje","A");
INSERT INTO tbvocabulary VALUES("332","77","2","grd_show_all","Mostrar todas","A");
INSERT INTO tbvocabulary VALUES("333","77","2","grd_show_group","Mostrar as do meu grupo","A");
INSERT INTO tbvocabulary VALUES("334","77","2","grd_show_only_mine","Mostrar as minhas","A");
INSERT INTO tbvocabulary VALUES("335","77","2","Grid_all","Todas","A");
INSERT INTO tbvocabulary VALUES("336","77","2","Grid_all_tickets","Todas as solicitações","A");
INSERT INTO tbvocabulary VALUES("337","77","2","Grid_being_attended","Em atendimento","A");
INSERT INTO tbvocabulary VALUES("338","77","2","Grid_being_attended_tickets","Solicitações em atendimento","A");
INSERT INTO tbvocabulary VALUES("339","77","2","Grid_expire_date","Prazo Final","A");
INSERT INTO tbvocabulary VALUES("340","77","2","Grid_finished","Encerradas","A");
INSERT INTO tbvocabulary VALUES("341","77","2","Grid_finished_tickets","Solicitações Atendidas e Encerradas","A");
INSERT INTO tbvocabulary VALUES("342","77","2","Grid_incharge","Respons&aacute;vel","A");
INSERT INTO tbvocabulary VALUES("343","77","2","Grid_new","Novas","A");
INSERT INTO tbvocabulary VALUES("344","77","2","Grid_new_tickets","Solicitações novas","A");
INSERT INTO tbvocabulary VALUES("345","77","2","Grid_opening_date","Data de Abertura","A");
INSERT INTO tbvocabulary VALUES("346","77","2","Grid_rejected","Rejeitadas","A");
INSERT INTO tbvocabulary VALUES("347","77","2","Grid_rejected_tickets","Solicitações Rejeitadas","A");
INSERT INTO tbvocabulary VALUES("348","77","2","Grid_reload","Recarregar","A");
INSERT INTO tbvocabulary VALUES("349","77","2","Grid_status","Status","A");
INSERT INTO tbvocabulary VALUES("350","77","2","Grid_subject","Assunto","A");
INSERT INTO tbvocabulary VALUES("351","77","2","Grid_view","Ver","A");
INSERT INTO tbvocabulary VALUES("352","77","2","Grid_waiting_approve_msg","Existem solicitações aguardando sua aprovação. <br/> Você não poderá abrir novos chamados antes de aprová-las.","A");
INSERT INTO tbvocabulary VALUES("353","77","2","Grid_waiting_my_approval","Aguardando minha aprova&ccedil;&atilde;o","A");
INSERT INTO tbvocabulary VALUES("354","77","2","Grid_waiting_my_approval_tickets","Solicitações aguardando aprova&ccedil;&atilde;o","A");
INSERT INTO tbvocabulary VALUES("355","77","2","Group","Grupo","A");
INSERT INTO tbvocabulary VALUES("356","77","2","Groups","Grupos","A");
INSERT INTO tbvocabulary VALUES("357","77","2","Groups_by_service","Visualizar grupos por servi&ccedil;o","A");
INSERT INTO tbvocabulary VALUES("358","77","2","Group_name","Nome do Grupo","A");
INSERT INTO tbvocabulary VALUES("359","77","2","Group_operators","Atendentes do Grupo","A");
INSERT INTO tbvocabulary VALUES("360","77","2","Group_still_viewing","Desejo que meu grupo continue visualizando a solicita&ccedil;&atilde;o","A");
INSERT INTO tbvocabulary VALUES("361","77","2","hdk_exceed_max_file_size","Este arquivo não será processado. Excede o tamanho máximo de upload: {{maxFilesize}}MB","A");
INSERT INTO tbvocabulary VALUES("362","77","2","hdk_remove_file","Remover Arquivo","A");
INSERT INTO tbvocabulary VALUES("363","77","2","Header","Cabe&ccedil;alho","A");
INSERT INTO tbvocabulary VALUES("364","77","2","helpdezk_path","Caminho do Helpdezk:","A");
INSERT INTO tbvocabulary VALUES("365","77","2","helpdezk_version","Versão do Helpdezk:","A");
INSERT INTO tbvocabulary VALUES("366","77","2","Holiday","Feriado","A");
INSERT INTO tbvocabulary VALUES("367","77","2","Holidays","Feriados","A");
INSERT INTO tbvocabulary VALUES("368","77","2","Holiday_des","Nome do Feriado","A");
INSERT INTO tbvocabulary VALUES("369","77","2","Holiday_import","Importar Feriados","A");
INSERT INTO tbvocabulary VALUES("370","77","2","Home","In&iacute;cio","A");
INSERT INTO tbvocabulary VALUES("371","77","2","Hour","Hora","A");
INSERT INTO tbvocabulary VALUES("372","77","2","Hours","Horas","A");
INSERT INTO tbvocabulary VALUES("373","77","2","Import","Importar","A");
INSERT INTO tbvocabulary VALUES("374","77","2","Important_notices","Avisos Importantes","A");
INSERT INTO tbvocabulary VALUES("375","77","2","Import_error_file","Não foi possível gravar o arquivo para importação.","A");
INSERT INTO tbvocabulary VALUES("376","77","2","Import_failure","N&aatilde;o foi poss&iacute;vel importar.","A");
INSERT INTO tbvocabulary VALUES("377","77","2","Import_layout_error","Erro no layout do arquivo de importação - número de colunas errado.","A");
INSERT INTO tbvocabulary VALUES("378","77","2","Import_not_writable","Não tem permissão para salvar o arquivo de importação","A");
INSERT INTO tbvocabulary VALUES("379","77","2","Import_successfull","Importado com sucesso","A");
INSERT INTO tbvocabulary VALUES("380","77","2","Import_to","Importar para","A");
INSERT INTO tbvocabulary VALUES("381","77","2","Info_header_logo","O logo do topo dever&aacute; ter 35px de altura, imagens maiores ser&atilde;o redimencionadas.","A");
INSERT INTO tbvocabulary VALUES("382","77","2","Info_login_logo","O logo da página de login deve ter 70px de altura, imagens maiores ser&atilde;o redimencionadas.","A");
INSERT INTO tbvocabulary VALUES("383","77","2","Info_reports_logo","O logo de relat&oacute;rios deve ter 40px de altura, imagens maiores ser&atilde;o redimencionadas.","A");
INSERT INTO tbvocabulary VALUES("384","77","2","Initial_date","Data Inicial","A");
INSERT INTO tbvocabulary VALUES("385","77","2","Insert_note","Inserir Apontamento","A");
INSERT INTO tbvocabulary VALUES("386","77","2","Integration_ldap","Integração com LDAP/AD","A");
INSERT INTO tbvocabulary VALUES("387","77","2","Item","Item","A");
INSERT INTO tbvocabulary VALUES("388","77","2","Item_edit","Editar Item","A");
INSERT INTO tbvocabulary VALUES("389","77","2","Item_insert","Cadastrar Item","A");
INSERT INTO tbvocabulary VALUES("390","77","2","Item_name","Nome do Item","A");
INSERT INTO tbvocabulary VALUES("391","77","2","itens","itens","A");
INSERT INTO tbvocabulary VALUES("392","77","2","January","Janeiro","A");
INSERT INTO tbvocabulary VALUES("393","77","2","jquery_version","Versão do Jquery:","A");
INSERT INTO tbvocabulary VALUES("394","77","2","July","Julho","A");
INSERT INTO tbvocabulary VALUES("395","77","2","June","Junho","A");
INSERT INTO tbvocabulary VALUES("396","77","2","juridical","Jur&iacute;dica","A");
INSERT INTO tbvocabulary VALUES("397","77","2","key_no_accents_no_whitespace","Key name não pode conter acentos ou espaços em branco","A");
INSERT INTO tbvocabulary VALUES("398","77","2","lbl_auxiliary_operator","Atendente(s) Auxiliar(es)","A");
INSERT INTO tbvocabulary VALUES("399","77","2","Lbl_photo","Foto","A");
INSERT INTO tbvocabulary VALUES("400","77","2","lbl_session_name","Nome Variável de Sessão","A");
INSERT INTO tbvocabulary VALUES("401","77","2","lbl_value","Valor","A");
INSERT INTO tbvocabulary VALUES("402","77","2","ldap_dn","Distinguished Names","A");
INSERT INTO tbvocabulary VALUES("403","77","2","ldap_domain","Dom&iacute;nio","A");
INSERT INTO tbvocabulary VALUES("404","77","2","ldap_field","Objeto AD/LDAP","A");
INSERT INTO tbvocabulary VALUES("405","77","2","ldap_field_obs","Campo onde &eacute; armazenado o usu&aacute;rio.","A");
INSERT INTO tbvocabulary VALUES("406","77","2","ldap_server","Servidor","A");
INSERT INTO tbvocabulary VALUES("407","77","2","List_comp_groups","Lista de empresas e seus grupos:","A");
INSERT INTO tbvocabulary VALUES("408","77","2","Loading","Carregando...","A");
INSERT INTO tbvocabulary VALUES("409","77","2","Location","Localiza&ccedil;&atilde;o","A");
INSERT INTO tbvocabulary VALUES("410","77","2","Location_insert","Cadastrar Localiza&ccedil;&atilde;o","A");
INSERT INTO tbvocabulary VALUES("411","77","2","Lock_text","Você está em tela de bloqueio. O aplicativo principal foi desligado e você precisa acessar a tela de login para voltar ao aplicativo.","A");
INSERT INTO tbvocabulary VALUES("412","77","2","Lock_unlock","Desbloquear","A");
INSERT INTO tbvocabulary VALUES("413","77","2","Login","Login","A");
INSERT INTO tbvocabulary VALUES("414","77","2","Login_cant_create_user","Cant create User !!!!","A");
INSERT INTO tbvocabulary VALUES("415","77","2","Login_error_error","Senha incorreta, digite novamente.","A");
INSERT INTO tbvocabulary VALUES("416","77","2","Login_error_secret","Token incorreto, tente novamente !!!","A");
INSERT INTO tbvocabulary VALUES("417","77","2","Login_exists","Login j&aacute; cadastrado!","A");
INSERT INTO tbvocabulary VALUES("418","77","2","Login_layout","Layout de Login","A");
INSERT INTO tbvocabulary VALUES("419","77","2","Login_page_logo","Logo da p&aacute;gina de Login","A");
INSERT INTO tbvocabulary VALUES("420","77","2","Login_type","Tipo de Login","A");
INSERT INTO tbvocabulary VALUES("421","77","2","Login_user_inactive","Usuário inativo, entre em contato com o administrador.","A");
INSERT INTO tbvocabulary VALUES("422","77","2","Login_user_not_exist","Usu&aacute;rio n&atilde;o existe, verifique a digita&ccedil;&atilde;o!","A");
INSERT INTO tbvocabulary VALUES("423","77","2","Logos_Title","Logos","A");
INSERT INTO tbvocabulary VALUES("424","77","2","logout","Sair","A");
INSERT INTO tbvocabulary VALUES("425","77","2","log_email","Logs de E-mail","A");
INSERT INTO tbvocabulary VALUES("426","77","2","log_general","Logs Gerais","A");
INSERT INTO tbvocabulary VALUES("427","77","2","log_host","Tipo de Host","A");
INSERT INTO tbvocabulary VALUES("428","77","2","log_level","Nivel de Log","A");
INSERT INTO tbvocabulary VALUES("429","77","2","log_remote_server","Servidor Remoto","A");
INSERT INTO tbvocabulary VALUES("430","77","2","Lost_password","Esqueci minha senha","A");
INSERT INTO tbvocabulary VALUES("431","77","2","Lost_password_ad","N&atilde;o é poss&iacute;vel recuperar senha no Active Directory, contate o administrador do sistema.","A");
INSERT INTO tbvocabulary VALUES("432","77","2","Lost_password_body","<br><br><br><p>Informamos que sua nova senha de acesso é: <b>$pass</b> <br>Esta &eacute; uma mensagem autom&aacute;tica, por favor, n&atilde;o responda.</p><br><br><br><br>","A");
INSERT INTO tbvocabulary VALUES("433","77","2","Lost_password_err","Falha ao enviar nova senha.","A");
INSERT INTO tbvocabulary VALUES("434","77","2","Lost_password_log","Enviado e-mail com nova senha - ","A");
INSERT INTO tbvocabulary VALUES("435","77","2","Lost_password_master","Senha do usuario master não pode ser trocada","A");
INSERT INTO tbvocabulary VALUES("436","77","2","Lost_password_not","Usu&aacute;rio inexistente ","A");
INSERT INTO tbvocabulary VALUES("437","77","2","Lost_password_pop","N&atilde;o é poss&iacute;vel recuperar senha com autentica&ccedil;&atilde;o POP, contate o administrador do sistema.","A");
INSERT INTO tbvocabulary VALUES("438","77","2","Lost_password_subject","Lembrete de Senha","A");
INSERT INTO tbvocabulary VALUES("439","77","2","Lost_password_suc","Nova senha enviada com sucesso.","A");
INSERT INTO tbvocabulary VALUES("440","77","2","Maintenance","Manutenção","A");
INSERT INTO tbvocabulary VALUES("441","77","2","Male","Masculino","A");
INSERT INTO tbvocabulary VALUES("442","77","2","Manage_fail_import_file","Falha ao importar o arquivo csv com os dados.","A");
INSERT INTO tbvocabulary VALUES("443","77","2","Manage_fail_move_file","Falha ao mover o arquivo de dados.\\nVerifique as permissões no diretório de anexos e tente novamente","A");
INSERT INTO tbvocabulary VALUES("444","77","2","Manage_fail_open_file_in","Falha ao abrir o arquivo de dados em ","A");
INSERT INTO tbvocabulary VALUES("445","77","2","Manage_fail_open_file_per","\\nVerifique as permissões e tente novamente.","A");
INSERT INTO tbvocabulary VALUES("446","77","2","Manage_instructions","Faça download das instru&ccedil;&otilde;es para importa&ccedil;&atilde;o: ","A");
INSERT INTO tbvocabulary VALUES("447","77","2","Manage_layout_service","Layout de Importa&ccedil;&atilde;o de Servi&ccedil;o","A");
INSERT INTO tbvocabulary VALUES("448","77","2","Manage_layout_service_file","Layout-Importacao-Servicos.pdf","A");
INSERT INTO tbvocabulary VALUES("449","77","2","Manage_service_already_registered","já está cadastrado.","A");
INSERT INTO tbvocabulary VALUES("450","77","2","Manage_service_and_group","e o grupo ","A");
INSERT INTO tbvocabulary VALUES("451","77","2","Manage_service_area","Cadastrada nova área -> Código: ","A");
INSERT INTO tbvocabulary VALUES("452","77","2","Manage_service_area_fail","Falha ao cadastrar a área de atendimento ","A");
INSERT INTO tbvocabulary VALUES("453","77","2","Manage_service_column_6","A coluna 6 deve conter apenas valor numérico. Foi informado o valor ","A");
INSERT INTO tbvocabulary VALUES("454","77","2","Manage_service_company_fail","Na coluna 8 é obrigatório um nome de empresa válido. A empresa % não está cadastrada !","A");
INSERT INTO tbvocabulary VALUES("455","77","2","Manage_service_completed","Processo finalizado com sucesso","A");
INSERT INTO tbvocabulary VALUES("456","77","2","Manage_service_default_pri","foi relacionado com prioridade padrão, pois a prioridade informada ","A");
INSERT INTO tbvocabulary VALUES("457","77","2","Manage_service_fail","Falha ao cadastrar o serviço ","A");
INSERT INTO tbvocabulary VALUES("458","77","2","Manage_service_fail_code","Falha ao determinar o código do serviço ","A");
INSERT INTO tbvocabulary VALUES("459","77","2","Manage_service_fail_rel","Falha ao cadastrar a relação entre o serviço ","A");
INSERT INTO tbvocabulary VALUES("460","77","2","Manage_service_finalized_line","Finalizada a linha ","A");
INSERT INTO tbvocabulary VALUES("461","77","2","Manage_service_group_fail","Falha ao cadastrar o grupo na tabela pessoa: ","A");
INSERT INTO tbvocabulary VALUES("462","77","2","Manage_service_group_fail2","Falha ao cadastrar o grupo de atendimento","A");
INSERT INTO tbvocabulary VALUES("463","77","2","Manage_service_group_register","! Cadastrado novo grupo de atendimento -> C?digo:","A");
INSERT INTO tbvocabulary VALUES("464","77","2","Manage_service_group_using","* Utilizando grupo existente -> Codigo ","A");
INSERT INTO tbvocabulary VALUES("465","77","2","Manage_service_imp_canceled",". Importação cancelada!","A");
INSERT INTO tbvocabulary VALUES("466","77","2","Manage_service_inf_line","informada na linha ","A");
INSERT INTO tbvocabulary VALUES("467","77","2","Manage_service_inf_on_line","informado na linha ","A");
INSERT INTO tbvocabulary VALUES("468","77","2","Manage_service_in_service","no serviço ","A");
INSERT INTO tbvocabulary VALUES("469","77","2","Manage_service_item","Cadastrado novo item -> ","A");
INSERT INTO tbvocabulary VALUES("470","77","2","Manage_service_item_fail","Falha ao cadastrar o item de atendimento ","A");
INSERT INTO tbvocabulary VALUES("471","77","2","Manage_service_line","linha ","A");
INSERT INTO tbvocabulary VALUES("472","77","2","Manage_service_not_identify_priority","Não foi possivel identificar tempo de atendimento da prioridade na linha ","A");
INSERT INTO tbvocabulary VALUES("473","77","2","Manage_service_not_registered","Não está cadastrado ou não é atendente. Linha ","A");
INSERT INTO tbvocabulary VALUES("474","77","2","Manage_service_on_line",", na linha ","A");
INSERT INTO tbvocabulary VALUES("475","77","2","Manage_service_pri_no_exist","não existe no sistema.","A");
INSERT INTO tbvocabulary VALUES("476","77","2","Manage_service_register_service","Cadastrado novo serviço ","A");
INSERT INTO tbvocabulary VALUES("477","77","2","Manage_service_type","Cadastrado novo TIPO -> Código: ","A");
INSERT INTO tbvocabulary VALUES("478","77","2","Manage_service_type_fail","Falha ao cadastrar o tipo de atendimento ","A");
INSERT INTO tbvocabulary VALUES("479","77","2","Manage_service_using_area","Utilizando área existente -> Código: ","A");
INSERT INTO tbvocabulary VALUES("480","77","2","Manage_service_using_item","Utilizando item existente -> Código: ","A");
INSERT INTO tbvocabulary VALUES("481","77","2","Manage_service_using_type","Utilizando tipo existente -> Código: ","A");
INSERT INTO tbvocabulary VALUES("482","77","2","Mange_service_order",", ordem","A");
INSERT INTO tbvocabulary VALUES("483","77","2","March","Mar&ccedil;o","A");
INSERT INTO tbvocabulary VALUES("484","77","2","May","Maio","A");
INSERT INTO tbvocabulary VALUES("485","77","2","Messages","Mensagens","A");
INSERT INTO tbvocabulary VALUES("486","77","2","Message_title","Você tem $$ novas mensagens.","A");
INSERT INTO tbvocabulary VALUES("487","77","2","Minutes","Minutos","A");
INSERT INTO tbvocabulary VALUES("488","77","2","Mobile_phone","Celular","A");
INSERT INTO tbvocabulary VALUES("489","77","2","Module","M&oacute;dulo","A");
INSERT INTO tbvocabulary VALUES("490","77","2","Modules","M&oacute;dulos","A");
INSERT INTO tbvocabulary VALUES("491","77","2","Module_default","M&oacute;dulo Padr&atilde;o","A");
INSERT INTO tbvocabulary VALUES("492","77","2","Module_insert","Cadastrar M&oacute;dulo","A");
INSERT INTO tbvocabulary VALUES("493","77","2","Module_name","Nome do M&oacute;dulo","A");
INSERT INTO tbvocabulary VALUES("494","77","2","module_not_delete","Este módulo não pode ser excluído","A");
INSERT INTO tbvocabulary VALUES("495","77","2","module_not_disable","Este módulo não pode ser desativado","A");
INSERT INTO tbvocabulary VALUES("496","77","2","module_not_edit","Este módulo não pode ser editado","A");
INSERT INTO tbvocabulary VALUES("497","77","2","Module_path","Caminho do M&oacute;dulo","A");
INSERT INTO tbvocabulary VALUES("498","77","2","Monday","Segunda-Feira","A");
INSERT INTO tbvocabulary VALUES("499","77","2","Month","M&ecirc;s","A");
INSERT INTO tbvocabulary VALUES("500","77","2","mother","Nome da Mãe","A");
INSERT INTO tbvocabulary VALUES("501","77","2","Msg_change_operation","Aten&ccedil;&atilde;o, ao editar os tipos de opera&ccedil;&atilde;o &eacute; necess&aacute;rio setar novamente as permiss&otilde;es do programa \'Permiss&atilde;o por Tipo de Pessoa\'.","A");
INSERT INTO tbvocabulary VALUES("502","77","2","mysql_version","Versão do mysql:","A");
INSERT INTO tbvocabulary VALUES("503","77","2","My_Tickets","Minhas Solicitações","A");
INSERT INTO tbvocabulary VALUES("504","77","2","Name","Nome","A");
INSERT INTO tbvocabulary VALUES("505","77","2","National_holiday","Feriado Nacional","A");
INSERT INTO tbvocabulary VALUES("506","77","2","natural","F&iacute;sica","A");
INSERT INTO tbvocabulary VALUES("507","77","2","Neighborhood","Bairro","A");
INSERT INTO tbvocabulary VALUES("508","77","2","New","Novo","A");
INSERT INTO tbvocabulary VALUES("509","77","2","New_category","Nova Categoria","A");
INSERT INTO tbvocabulary VALUES("510","77","2","New_date","Nova Data","A");
INSERT INTO tbvocabulary VALUES("511","77","2","new_feature","Nova Funcionalidade","A");
INSERT INTO tbvocabulary VALUES("512","77","2","New_messages","Novas mensagens","A");
INSERT INTO tbvocabulary VALUES("513","77","2","New_password","Nova Senha","A");
INSERT INTO tbvocabulary VALUES("514","77","2","New_request","Nova Solicita&ccedil;&atilde;o","A");
INSERT INTO tbvocabulary VALUES("515","77","2","New_time","Nova Hora","A");
INSERT INTO tbvocabulary VALUES("516","77","2","No","N&atilde;o","A");
INSERT INTO tbvocabulary VALUES("517","77","2","Normal","Normal","A");
INSERT INTO tbvocabulary VALUES("518","77","2","Note","Apontamento","A");
INSERT INTO tbvocabulary VALUES("519","77","2","Note_msg","Inserir apontamentos ao responder emails.","A");
INSERT INTO tbvocabulary VALUES("520","77","2","Notification","Notifica&ccedil;&atilde;o","A");
INSERT INTO tbvocabulary VALUES("521","77","2","Not_available_yet","Ainda n&atilde;o dipon&iacute;vel","A");
INSERT INTO tbvocabulary VALUES("522","77","2","November","Novembro","A");
INSERT INTO tbvocabulary VALUES("523","77","2","No_abilities","N&atilde;o h&aacute; habilidades relacionadas","A");
INSERT INTO tbvocabulary VALUES("524","77","2","No_data","Sem dados.","A");
INSERT INTO tbvocabulary VALUES("525","77","2","No_notices","Nenhum aviso importante no momento.","A");
INSERT INTO tbvocabulary VALUES("526","77","2","no_permission_edit","Você não possui permissão para edição","A");
INSERT INTO tbvocabulary VALUES("527","77","2","No_result","Nenhum registro encontrado.","A");
INSERT INTO tbvocabulary VALUES("528","77","2","Number","N&uacute;mero","A");
INSERT INTO tbvocabulary VALUES("529","77","2","Obrigatory_time","Preenchimento de tempo gasto com a tarefa obrigatório","A");
INSERT INTO tbvocabulary VALUES("530","77","2","Observation","Observa&ccedil;&atilde;o","A");
INSERT INTO tbvocabulary VALUES("531","77","2","October","Outubro","A");
INSERT INTO tbvocabulary VALUES("532","77","2","of","de","A");
INSERT INTO tbvocabulary VALUES("533","77","2","Ok_btn","Ok","A");
INSERT INTO tbvocabulary VALUES("534","77","2","Only_operator","Somente atendente","A");
INSERT INTO tbvocabulary VALUES("535","77","2","on_time","No prazo","A");
INSERT INTO tbvocabulary VALUES("536","77","2","Opened_by","Aberta por","A");
INSERT INTO tbvocabulary VALUES("537","77","2","Opening_date","Data de Abertura","A");
INSERT INTO tbvocabulary VALUES("538","77","2","Operation","Opera&ccedil;&atilde;o","A");
INSERT INTO tbvocabulary VALUES("539","77","2","Operations","Opera&ccedil;&otilde;es","A");
INSERT INTO tbvocabulary VALUES("540","77","2","Operator","Atendente","A");
INSERT INTO tbvocabulary VALUES("541","77","2","Operator_groups","Grupos do Atendente","A");
INSERT INTO tbvocabulary VALUES("542","77","2","Option_only_attendant_active","Opção somente para atendentes ativos","A");
INSERT INTO tbvocabulary VALUES("543","77","2","Option_only_operator","Opção somente para atendentes","A");
INSERT INTO tbvocabulary VALUES("544","77","2","Other_items","Outros Itens","A");
INSERT INTO tbvocabulary VALUES("545","77","2","overdue","Atrasado","A");
INSERT INTO tbvocabulary VALUES("546","77","2","Overtime","Hora Extra","A");
INSERT INTO tbvocabulary VALUES("547","77","2","Page","P&aacute;ginas","A");
INSERT INTO tbvocabulary VALUES("548","77","2","Page_header_logo","Logo do cabe&ccedil;alho","A");
INSERT INTO tbvocabulary VALUES("549","77","2","Password","Senha","A");
INSERT INTO tbvocabulary VALUES("550","77","2","PDF_code","Código","A");
INSERT INTO tbvocabulary VALUES("551","77","2","PDF_Page","Página","A");
INSERT INTO tbvocabulary VALUES("552","77","2","PDF_person_report","Relatório de Pessoa","A");
INSERT INTO tbvocabulary VALUES("553","77","2","people","Pessoas & Empresas","A");
INSERT INTO tbvocabulary VALUES("554","77","2","Permissions","Permiss&otilde;es","A");
INSERT INTO tbvocabulary VALUES("555","77","2","Permission_error","Erro durante a opera&ccedil;&atilde;o","A");
INSERT INTO tbvocabulary VALUES("556","77","2","Permission_Groups","Permissão","A");
INSERT INTO tbvocabulary VALUES("557","77","2","Permission_Groups_Select","Selecione o Grupo de Permissão","A");
INSERT INTO tbvocabulary VALUES("558","77","2","pgr_departments","Departamentos","A");
INSERT INTO tbvocabulary VALUES("559","77","2","pgr_erp_emailtemplate","E-mail Templates","A");
INSERT INTO tbvocabulary VALUES("560","77","2","Phone","Telefone","A");
INSERT INTO tbvocabulary VALUES("561","77","2","php_version","Versão do php:","A");
INSERT INTO tbvocabulary VALUES("562","77","2","Placeholder_subject","Insira o assunto","A");
INSERT INTO tbvocabulary VALUES("563","77","2","Placeholder_zipcode","Insira o Cep","A");
INSERT INTO tbvocabulary VALUES("564","77","2","plh_category_description","Informe o nome da Categoria","A");
INSERT INTO tbvocabulary VALUES("565","77","2","plh_controller_description","Informe o Controller do Programa","A");
INSERT INTO tbvocabulary VALUES("566","77","2","plh_holiday_date","Informe a data do Feriado","A");
INSERT INTO tbvocabulary VALUES("567","77","2","plh_holiday_description","Informe a descrição do Feriado","A");
INSERT INTO tbvocabulary VALUES("568","77","2","plh_module_description","Informe o nome do Módulo","A");
INSERT INTO tbvocabulary VALUES("569","77","2","plh_module_path","Informe o caminho do Módulo","A");
INSERT INTO tbvocabulary VALUES("570","77","2","plh_module_prefix","Informe o prefixo de tabela","A");
INSERT INTO tbvocabulary VALUES("571","77","2","plh_program_description","Informe o nome do Programa","A");
INSERT INTO tbvocabulary VALUES("572","77","2","plh_smarty_variable","Informe a vari&aacute;vel smarty","A");
INSERT INTO tbvocabulary VALUES("573","77","2","Pop_server","Servidor POP","A");
INSERT INTO tbvocabulary VALUES("574","77","2","Port","Porta","A");
INSERT INTO tbvocabulary VALUES("575","77","2","Previous_year","Ano Anterior","A");
INSERT INTO tbvocabulary VALUES("576","77","2","Print","Imprimir","A");
INSERT INTO tbvocabulary VALUES("577","77","2","Priority","Prioridade","A");
INSERT INTO tbvocabulary VALUES("578","77","2","Processing","Processando","A");
INSERT INTO tbvocabulary VALUES("579","77","2","Program","Programa","A");
INSERT INTO tbvocabulary VALUES("580","77","2","Programs","Programas","A");
INSERT INTO tbvocabulary VALUES("581","77","2","pushover","Configurações Pushover API","A");
INSERT INTO tbvocabulary VALUES("582","77","2","Reason","Motivo","A");
INSERT INTO tbvocabulary VALUES("583","77","2","Reason_no_registered","Sem motivo cadastrado","A");
INSERT INTO tbvocabulary VALUES("584","77","2","Recalculate","Recalcular","A");
INSERT INTO tbvocabulary VALUES("585","77","2","Recalculate_msg_chk","Recalcular tempo de atendimento após o fim das aprovações","A");
INSERT INTO tbvocabulary VALUES("586","77","2","records","Cadastros","A");
INSERT INTO tbvocabulary VALUES("587","77","2","Register_btn","Registrar","A");
INSERT INTO tbvocabulary VALUES("588","77","2","Rejected","Rejeitadas","A");
INSERT INTO tbvocabulary VALUES("589","77","2","Reject_btn","Rejeitar","A");
INSERT INTO tbvocabulary VALUES("590","77","2","Reject_sucess","Solicitação Rejeitada","A");
INSERT INTO tbvocabulary VALUES("591","77","2","Related_abilities","Habilidades Relacionadas","A");
INSERT INTO tbvocabulary VALUES("592","77","2","reload_request","Recarregar dados ","A");
INSERT INTO tbvocabulary VALUES("593","77","2","Remove","Remover","A");
INSERT INTO tbvocabulary VALUES("594","77","2","Repassed","Repassada","A");
INSERT INTO tbvocabulary VALUES("595","77","2","Repass_btn","Repassar","A");
INSERT INTO tbvocabulary VALUES("596","77","2","Repass_request_only","Apenas Repassar Chamados","A");
INSERT INTO tbvocabulary VALUES("597","77","2","Repass_request_to","Repassar solicita&ccedil;&atilde;o para","A");
INSERT INTO tbvocabulary VALUES("598","77","2","Reports_logo","Logo dos Relat&oacute;rios","A");
INSERT INTO tbvocabulary VALUES("599","77","2","Request","Solicita&ccedil;&atilde;o","A");
INSERT INTO tbvocabulary VALUES("600","77","2","Requests","Solicita&ccedil;&otilde;es","A");
INSERT INTO tbvocabulary VALUES("601","77","2","Request_approve","Existem solicitações aguardando sua aprovação. <br/> Você não poderá abrir novos chamados antes de aprová-las.<br/> Deseja aprova-las agora?","A");
INSERT INTO tbvocabulary VALUES("602","77","2","Request_approve_app","Aprovar Solicita&ccedil;&atilde;o","A");
INSERT INTO tbvocabulary VALUES("603","77","2","Request_assumed","Solicita&ccedil;&atilde;o Assumida","A");
INSERT INTO tbvocabulary VALUES("604","77","2","Request_canceled","Solicita&ccedil;&atilde;o Cancelada","A");
INSERT INTO tbvocabulary VALUES("605","77","2","Request_closed","Solicita&ccedil;&atilde;o Encerrada","A");
INSERT INTO tbvocabulary VALUES("606","77","2","Request_code","C&oacute;digo da Solicita&ccedil;&atilde;o","A");
INSERT INTO tbvocabulary VALUES("607","77","2","Request_not_approve","A solicita&ccedil;&atilde;o n&atilde;o foi aprovada.","A");
INSERT INTO tbvocabulary VALUES("608","77","2","Request_opened","Solicita&ccedil;&atilde;o Cadastrada","A");
INSERT INTO tbvocabulary VALUES("609","77","2","Request_owner","Solicitante","A");
INSERT INTO tbvocabulary VALUES("610","77","2","Request_rejected","Solicita&ccedil;&atilde;o n&atilde;o pode ser atendida: ","A");
INSERT INTO tbvocabulary VALUES("611","77","2","Request_reopened","Solicita&ccedil;&atilde;o Reaberta","A");
INSERT INTO tbvocabulary VALUES("612","77","2","Request_repassed","Solicita&ccedil;&atilde;o Repassada ","A");
INSERT INTO tbvocabulary VALUES("613","77","2","Request_reprove_app","Reprovar Solicita&ccedil;&atilde;o","A");
INSERT INTO tbvocabulary VALUES("614","77","2","Request_return_app","Retornar &agrave; fase anterior","A");
INSERT INTO tbvocabulary VALUES("615","77","2","Request_waiting_approval","Aguardando aprovação do usu&aacute;rio","A");
INSERT INTO tbvocabulary VALUES("616","77","2","Requires_Autentication","Autentica&ccedil;&atilde;o obrigat&oacute;ria","A");
INSERT INTO tbvocabulary VALUES("617","77","2","rg","RG","A");
INSERT INTO tbvocabulary VALUES("618","77","2","role","Cargo","A");
INSERT INTO tbvocabulary VALUES("619","77","2","Saturday","S&eacute;bado","A");
INSERT INTO tbvocabulary VALUES("620","77","2","Save","Salvar","A");
INSERT INTO tbvocabulary VALUES("621","77","2","Save_changes_sucess","Alterações salvas","A");
INSERT INTO tbvocabulary VALUES("622","77","2","Search","Buscar","A");
INSERT INTO tbvocabulary VALUES("623","77","2","Second","segundo","A");
INSERT INTO tbvocabulary VALUES("624","77","2","seconds","segundos","A");
INSERT INTO tbvocabulary VALUES("625","77","2","Select","Selecione","A");
INSERT INTO tbvocabulary VALUES("626","77","2","Select_acess_level","Selecione N&iacute;vel de Acesso","A");
INSERT INTO tbvocabulary VALUES("627","77","2","Select_area","Selecione uma &Aacute;rea","A");
INSERT INTO tbvocabulary VALUES("628","77","2","Select_category","Selecione uma categoria","A");
INSERT INTO tbvocabulary VALUES("629","77","2","Select_company","Selecione a empresa","A");
INSERT INTO tbvocabulary VALUES("630","77","2","Select_country","Selecione o Pa&iacute;s","A");
INSERT INTO tbvocabulary VALUES("631","77","2","Select_department","Selecione o departamento","A");
INSERT INTO tbvocabulary VALUES("632","77","2","Select_group","Selecione um grupo","A");
INSERT INTO tbvocabulary VALUES("633","77","2","Select_group_operator","Selecione um grupo ou atendente.","A");
INSERT INTO tbvocabulary VALUES("634","77","2","Select_location","Selecione a Localização","A");
INSERT INTO tbvocabulary VALUES("635","77","2","Select_module","Selecione o módulo","A");
INSERT INTO tbvocabulary VALUES("636","77","2","Select_priority","Selecione uma Prioridade","A");
INSERT INTO tbvocabulary VALUES("637","77","2","Select_street","Selecione o Endereço","A");
INSERT INTO tbvocabulary VALUES("638","77","2","Send","ENVIAR ","A");
INSERT INTO tbvocabulary VALUES("639","77","2","Send_alerts_email","Sim, envie este alerta por e-mail.","A");
INSERT INTO tbvocabulary VALUES("640","77","2","Send_alerts_topic_email","Sim, envie alertas deste tópico por email.","A");
INSERT INTO tbvocabulary VALUES("641","77","2","Send_email","Enviar email","A");
INSERT INTO tbvocabulary VALUES("642","77","2","September","Setembro","A");
INSERT INTO tbvocabulary VALUES("643","77","2","Serial_number","N&ordm; s&eacute;rie","A");
INSERT INTO tbvocabulary VALUES("644","77","2","Server","Servidor","A");
INSERT INTO tbvocabulary VALUES("645","77","2","Service","Servi&ccedil;o","A");
INSERT INTO tbvocabulary VALUES("646","77","2","Service_edit","Editar Servi&ccedil;o","A");
INSERT INTO tbvocabulary VALUES("647","77","2","Service_insert","Cadastrar Servi&ccedil;o","A");
INSERT INTO tbvocabulary VALUES("648","77","2","Service_name","Nome do Servi&ccedil;o","A");
INSERT INTO tbvocabulary VALUES("649","77","2","Service_order_number","N&ordm; do documento","A");
INSERT INTO tbvocabulary VALUES("650","77","2","Service_order_number_min","N&ordm; do doc.","A");
INSERT INTO tbvocabulary VALUES("651","77","2","Settings","Configurações","A");
INSERT INTO tbvocabulary VALUES("652","77","2","Set_repass_groups","Configurar grupos de repasse","A");
INSERT INTO tbvocabulary VALUES("653","77","2","show","Mostrar","A");
INSERT INTO tbvocabulary VALUES("654","77","2","Show_attendants_title","Exibir atendentes por grupo","A");
INSERT INTO tbvocabulary VALUES("655","77","2","Show_groups_services_title","Exibir grupos por servi&ccedil;os","A");
INSERT INTO tbvocabulary VALUES("656","77","2","Show_in","Mostrar em","A");
INSERT INTO tbvocabulary VALUES("657","77","2","Smarty","Vari&aacute;vel Smarty","A");
INSERT INTO tbvocabulary VALUES("658","77","2","smarty_version","Versão do Smarty: ","A");
INSERT INTO tbvocabulary VALUES("659","77","2","SMS","SMS","A");
INSERT INTO tbvocabulary VALUES("660","77","2","Solution","Solu&ccedil;&atilde;o","A");
INSERT INTO tbvocabulary VALUES("661","77","2","Source","Origem","A");
INSERT INTO tbvocabulary VALUES("662","77","2","Started","Iniciou","A");
INSERT INTO tbvocabulary VALUES("663","77","2","State","Estado","A");
INSERT INTO tbvocabulary VALUES("664","77","2","status","Status","A");
INSERT INTO tbvocabulary VALUES("665","77","2","Still_viewing","Desejo continuar visualizando a solicita&ccedil;&atilde;o.","A");
INSERT INTO tbvocabulary VALUES("666","77","2","Stop_viewing","N&atilde;o acompanhar.","A");
INSERT INTO tbvocabulary VALUES("667","77","2","Subject","Assunto","A");
INSERT INTO tbvocabulary VALUES("668","77","2","Success_logs","Logs de sucesso","A");
INSERT INTO tbvocabulary VALUES("669","77","2","summary","Resumo do Currículo","A");
INSERT INTO tbvocabulary VALUES("670","77","2","Sunday","Domingo","A");
INSERT INTO tbvocabulary VALUES("671","77","2","sys_2FAuthentication","Usar autenticação em duas etapas","A");
INSERT INTO tbvocabulary VALUES("672","77","2","sys_session_time_lbl","Tempo duração da Sessão do Sistema","A");
INSERT INTO tbvocabulary VALUES("673","77","2","sys_time_session","Tempo que ir&aacute; durar a sess&atilde;o do sistema. Valor em segundos. Se n&atilde;o definido ser&aacute; de 10 minutos.","A");
INSERT INTO tbvocabulary VALUES("674","77","2","Table_prefix","Prefixo de Tabela","A");
INSERT INTO tbvocabulary VALUES("675","77","2","Tag_min","N&ordm; de patrim.","A");
INSERT INTO tbvocabulary VALUES("676","77","2","Tckt_cancel_request","Confirma cancelamento da solicitação ?","A");
INSERT INTO tbvocabulary VALUES("677","77","2","Tckt_delete_note","Confirma exclusão do apontamento ?","A");
INSERT INTO tbvocabulary VALUES("678","77","2","Tckt_del_note_failure","Não possível excluir o apontamento !","A");
INSERT INTO tbvocabulary VALUES("679","77","2","Tckt_drop_file","Arraste os arquivos para upload ou clique aqui !","A");
INSERT INTO tbvocabulary VALUES("680","77","2","Tckt_evaluated_success","Solicitação avaliada com sucesso !","A");
INSERT INTO tbvocabulary VALUES("681","77","2","Tckt_finish_request","Encerrar","A");
INSERT INTO tbvocabulary VALUES("682","77","2","Tckt_incharge","Respons&aacute;vel","A");
INSERT INTO tbvocabulary VALUES("683","77","2","Tckt_opened","Solicitação aberta","A");
INSERT INTO tbvocabulary VALUES("684","77","2","Tckt_reopen_request","Confirma reabertura da solicitação ?","A");
INSERT INTO tbvocabulary VALUES("685","77","2","Tckt_Request","Solicita&ccedil;&atilde;o","A");
INSERT INTO tbvocabulary VALUES("686","77","2","Tck_new_ticket","Nova Solicitação","A");
INSERT INTO tbvocabulary VALUES("687","77","2","Tck_Open","Solicitações em aberto","A");
INSERT INTO tbvocabulary VALUES("688","77","2","Tck_title","Solicitações","A");
INSERT INTO tbvocabulary VALUES("689","77","2","Template_edit","Editar Template","A");
INSERT INTO tbvocabulary VALUES("690","77","2","Thursday","Quinta-Feira","A");
INSERT INTO tbvocabulary VALUES("691","77","2","timeouttext","Sua sess&atilde;o vai expirar em","A");
INSERT INTO tbvocabulary VALUES("692","77","2","Time_expended","Tempo gasto na tarefa","A");
INSERT INTO tbvocabulary VALUES("693","77","2","Time_return","Tempo liga&ccedil;&atilde;o de retorno (callback)","A");
INSERT INTO tbvocabulary VALUES("694","77","2","Time_value","Valor da Hora","A");
INSERT INTO tbvocabulary VALUES("695","77","2","Title","T&iacute;tulo","A");
INSERT INTO tbvocabulary VALUES("696","77","2","tlt_span_group","Solicita&ccedil;&otilde;es do Grupo","A");
INSERT INTO tbvocabulary VALUES("697","77","2","tlt_span_my","Minhas Solicita&ccedil;&otilde;es","A");
INSERT INTO tbvocabulary VALUES("698","77","2","tlt_span_track_group","Acompanhada pelo grupo","A");
INSERT INTO tbvocabulary VALUES("699","77","2","tlt_span_track_me","Acompanhada por mim","A");
INSERT INTO tbvocabulary VALUES("700","77","2","to","para","A");
INSERT INTO tbvocabulary VALUES("701","77","2","tooltip_city","Adicionar nova cidade","A");
INSERT INTO tbvocabulary VALUES("702","77","2","tooltip_delete_area","Deletar área","A");
INSERT INTO tbvocabulary VALUES("703","77","2","tooltip_delete_item","Deletar item","A");
INSERT INTO tbvocabulary VALUES("704","77","2","tooltip_delete_service","Deletar serviço","A");
INSERT INTO tbvocabulary VALUES("705","77","2","tooltip_delete_type","Deletar tipo","A");
INSERT INTO tbvocabulary VALUES("706","77","2","tooltip_list_items","Listar Itens","A");
INSERT INTO tbvocabulary VALUES("707","77","2","tooltip_list_services","Listar Serviços","A");
INSERT INTO tbvocabulary VALUES("708","77","2","tooltip_neighborhood","Adicionar novo bairro","A");
INSERT INTO tbvocabulary VALUES("709","77","2","tooltip_state","Adicionar novo estado","A");
INSERT INTO tbvocabulary VALUES("710","77","2","tooltip_street","Adicionar novo endereço","A");
INSERT INTO tbvocabulary VALUES("711","77","2","Topic","T&oacute;pico","A");
INSERT INTO tbvocabulary VALUES("712","77","2","Topic_edit","Editar T&oacute;pico","A");
INSERT INTO tbvocabulary VALUES("713","77","2","Total","Total","A");
INSERT INTO tbvocabulary VALUES("714","77","2","Total_holidays","Total de feriados de","A");
INSERT INTO tbvocabulary VALUES("715","77","2","Total_minutes","Total minutos","A");
INSERT INTO tbvocabulary VALUES("716","77","2","tracker_status","Rastrear E-mails","A");
INSERT INTO tbvocabulary VALUES("717","77","2","trello","Configurações Trello API","A");
INSERT INTO tbvocabulary VALUES("718","77","2","trello_boards","Quadros","A");
INSERT INTO tbvocabulary VALUES("719","77","2","trello_cards","Cartões","A");
INSERT INTO tbvocabulary VALUES("720","77","2","trello_description","Descrição do Cartão","A");
INSERT INTO tbvocabulary VALUES("721","77","2","trello_integration","Integração com o Trello","A");
INSERT INTO tbvocabulary VALUES("722","77","2","trello_key","Chave","A");
INSERT INTO tbvocabulary VALUES("723","77","2","trello_lists","Listas","A");
INSERT INTO tbvocabulary VALUES("724","77","2","trello_title","Título do Cartão","A");
INSERT INTO tbvocabulary VALUES("725","77","2","trello_token","Token","A");
INSERT INTO tbvocabulary VALUES("726","77","2","trello_tooltip_card","Adicionar novo cartão","A");
INSERT INTO tbvocabulary VALUES("727","77","2","Tuesday","Ter&ccedil;a-Feira","A");
INSERT INTO tbvocabulary VALUES("728","77","2","twitter","Twitter","A");
INSERT INTO tbvocabulary VALUES("729","77","2","Type","Tipo","A");
INSERT INTO tbvocabulary VALUES("730","77","2","Type_adress","Logradouro","A");
INSERT INTO tbvocabulary VALUES("731","77","2","Type_edit","Editar Tipo","A");
INSERT INTO tbvocabulary VALUES("732","77","2","Type_insert","Cadastrar Tipo","A");
INSERT INTO tbvocabulary VALUES("733","77","2","Type_name","Nome do Tipo","A");
INSERT INTO tbvocabulary VALUES("734","77","2","type_user_operator","Atendente","A");
INSERT INTO tbvocabulary VALUES("735","77","2","type_user_user","Usu&aacute;rio","A");
INSERT INTO tbvocabulary VALUES("736","77","2","until","at&eacute;","A");
INSERT INTO tbvocabulary VALUES("737","77","2","Until_closed","At&eacute; ser encerrado","A");
INSERT INTO tbvocabulary VALUES("738","77","2","Update","Atualizar","A");
INSERT INTO tbvocabulary VALUES("739","77","2","User","Usu&aacute;rio","A");
INSERT INTO tbvocabulary VALUES("740","77","2","UserData","Dados do usuário","A");
INSERT INTO tbvocabulary VALUES("741","77","2","user_external_settings","Configurações Externas","A");
INSERT INTO tbvocabulary VALUES("742","77","2","User_login","Login do usu&aacute;rio","A");
INSERT INTO tbvocabulary VALUES("743","77","2","user_profile","Perfil","A");
INSERT INTO tbvocabulary VALUES("744","77","2","Valid","V&aacute;lido de","A");
INSERT INTO tbvocabulary VALUES("745","77","2","Validity_Standard","Validade Padr&atilde;o","A");
INSERT INTO tbvocabulary VALUES("746","77","2","Valid_until","V&aacute;lido at&eacute;","A");
INSERT INTO tbvocabulary VALUES("747","77","2","Value_exists","Valor inserido já está cadastrado","A");
INSERT INTO tbvocabulary VALUES("748","77","2","Var_assume","Data que foi assumida","A");
INSERT INTO tbvocabulary VALUES("749","77","2","Var_branch","Ramal do usu&aacute;rio","A");
INSERT INTO tbvocabulary VALUES("750","77","2","Var_date","Data do email","A");
INSERT INTO tbvocabulary VALUES("751","77","2","Var_description","Descri&ccedil;&atilde;o da solicita&ccedil;&atilde;o","A");
INSERT INTO tbvocabulary VALUES("752","77","2","Var_evaluation","Avaliação dada pelo usu&aacute;rio!","A");
INSERT INTO tbvocabulary VALUES("753","77","2","Var_expire","Data de validade","A");
INSERT INTO tbvocabulary VALUES("754","77","2","Var_finish","Data que foi finalizada","A");
INSERT INTO tbvocabulary VALUES("755","77","2","Var_incharge","Nome do respons&aacute;vel","A");
INSERT INTO tbvocabulary VALUES("756","77","2","Var_link_evaluation","Link para avaliação sem login.","A");
INSERT INTO tbvocabulary VALUES("757","77","2","Var_link_operator","Link para a p&aacute;gina da solicita&ccedil;&atilde;o (atendente)","A");
INSERT INTO tbvocabulary VALUES("758","77","2","Var_link_user","Link para a p&aacute;gina da solicita&ccedil;&atilde;o (usu&aacute;rio)","A");
INSERT INTO tbvocabulary VALUES("759","77","2","Var_nt_operator","Apontamentos que somente os atendentes podem ver","A");
INSERT INTO tbvocabulary VALUES("760","77","2","Var_nt_user","Apontamentos que usu&aacute;rios podem ver","A");
INSERT INTO tbvocabulary VALUES("761","77","2","Var_phone","Telefone do usu&aacute;rio","A");
INSERT INTO tbvocabulary VALUES("762","77","2","Var_record","Data de cadastro","A");
INSERT INTO tbvocabulary VALUES("763","77","2","Var_rejection","Data que foi rejeitada","A");
INSERT INTO tbvocabulary VALUES("764","77","2","Var_request","Data da solicita&ccedil;&atilde;o","A");
INSERT INTO tbvocabulary VALUES("765","77","2","Var_requester","Nome do solicitante","A");
INSERT INTO tbvocabulary VALUES("766","77","2","Var_status","Status da solicita&ccedil;&atilde;o","A");
INSERT INTO tbvocabulary VALUES("767","77","2","Var_subject","Assunto da solicita&ccedil;&atilde;o","A");
INSERT INTO tbvocabulary VALUES("768","77","2","Var_user","Usu&aacute;rio logado no sistema","A");
INSERT INTO tbvocabulary VALUES("769","77","2","Version","Vers&atilde;o","A");
INSERT INTO tbvocabulary VALUES("770","77","2","View_groups","Visualizar grupos","A");
INSERT INTO tbvocabulary VALUES("771","77","2","VIP_user","Usu&aacute;rio VIP","A");
INSERT INTO tbvocabulary VALUES("772","77","2","Visible","Vis&iacute;vel","A");
INSERT INTO tbvocabulary VALUES("773","77","2","vocabulary_key_exists","Esta key já existe","A");
INSERT INTO tbvocabulary VALUES("774","77","2","vocabulary_key_name","Key Name","A");
INSERT INTO tbvocabulary VALUES("775","77","2","vocabulary_key_value","Key Value","A");
INSERT INTO tbvocabulary VALUES("776","77","2","vocabulary_locale","Locale","A");
INSERT INTO tbvocabulary VALUES("777","77","2","Waiting_for_approval","Aguardando aprova&ccedil;&atilde;o","A");
INSERT INTO tbvocabulary VALUES("778","77","2","Warning_new_topic","Novo T&oacute;pico","A");
INSERT INTO tbvocabulary VALUES("779","77","2","Wednesday","Quarta-Feira","A");
INSERT INTO tbvocabulary VALUES("780","77","2","Widget","Widget","A");
INSERT INTO tbvocabulary VALUES("781","77","2","Yes","Sim","A");
INSERT INTO tbvocabulary VALUES("782","77","2","Zipcode","Cep","A");
INSERT INTO tbvocabulary VALUES("783","19","2","adm_Navbar_name","Admin","A");
INSERT INTO tbvocabulary VALUES("784","19","2","APP_apiUrlLabel","Url da API","A");
INSERT INTO tbvocabulary VALUES("785","19","2","APP_areaLabel","Área","A");
INSERT INTO tbvocabulary VALUES("786","19","2","APP_attachLabel","Anexo","A");
INSERT INTO tbvocabulary VALUES("787","19","2","APP_btnConfirm","Confirmar","A");
INSERT INTO tbvocabulary VALUES("788","19","2","APP_btnLogin","ENTRE","A");
INSERT INTO tbvocabulary VALUES("789","19","2","APP_cancelButton","Cancelar","A");
INSERT INTO tbvocabulary VALUES("790","19","2","APP_changePassButton","Alterar Senha","A");
INSERT INTO tbvocabulary VALUES("791","19","2","APP_ChangePassword_title","Nova Senha","A");
INSERT INTO tbvocabulary VALUES("792","19","2","APP_cityLabel","Cidade","A");
INSERT INTO tbvocabulary VALUES("793","19","2","APP_companyLabel","Empresa","A");
INSERT INTO tbvocabulary VALUES("794","19","2","APP_configPage","Configurações","A");
INSERT INTO tbvocabulary VALUES("795","19","2","APP_Configuration_title","Configurações","A");
INSERT INTO tbvocabulary VALUES("796","19","2","APP_confirmButton","Confirmar","A");
INSERT INTO tbvocabulary VALUES("797","19","2","APP_descriptionLabel","Descrição","A");
INSERT INTO tbvocabulary VALUES("798","19","2","APP_exitLink","Sair","A");
INSERT INTO tbvocabulary VALUES("799","19","2","APP_GetUrl_title","Seja bem-vindo!","A");
INSERT INTO tbvocabulary VALUES("800","19","2","APP_homePage","Início","A");
INSERT INTO tbvocabulary VALUES("801","19","2","APP_Home_title","Tickets Abertos","A");
INSERT INTO tbvocabulary VALUES("802","19","2","APP_inChargeLabel","Departamento","A");
INSERT INTO tbvocabulary VALUES("803","19","2","APP_itemLabel","Item","A");
INSERT INTO tbvocabulary VALUES("804","19","2","APP_keep","Manter-se conectado","A");
INSERT INTO tbvocabulary VALUES("805","19","2","APP_Login_title","Login","A");
INSERT INTO tbvocabulary VALUES("806","19","2","APP_nameLabel","Nome","A");
INSERT INTO tbvocabulary VALUES("807","19","2","APP_newAttachLabel","Anexo","A");
INSERT INTO tbvocabulary VALUES("808","19","2","APP_newNoteTitle","Inserir Apontamento","A");
INSERT INTO tbvocabulary VALUES("809","19","2","APP_newRequestButton","Novo Ticket","A");
INSERT INTO tbvocabulary VALUES("810","19","2","APP_NewTicket_title","Novo Ticket","A");
INSERT INTO tbvocabulary VALUES("811","19","2","APP_notesTitle","Apontamentos","A");
INSERT INTO tbvocabulary VALUES("812","19","2","APP_originLabel","Origem","A");
INSERT INTO tbvocabulary VALUES("813","19","2","APP_passLabel","Digite sua nova senha","A");
INSERT INTO tbvocabulary VALUES("814","19","2","APP_passPlaceholder","Senha","A");
INSERT INTO tbvocabulary VALUES("815","19","2","APP_passRequired","É obrigatório inserir a senha.","A");
INSERT INTO tbvocabulary VALUES("816","19","2","APP_phoneLabel","Telefone","A");
INSERT INTO tbvocabulary VALUES("817","19","2","APP_reasonLabel","Razão","A");
INSERT INTO tbvocabulary VALUES("818","19","2","APP_rememberPass","Esqueceu a senha?","A");
INSERT INTO tbvocabulary VALUES("819","19","2","APP_RememberPassword_title","Esqueceu a Senha","A");
INSERT INTO tbvocabulary VALUES("820","19","2","APP_requestDate","Prazo Final","A");
INSERT INTO tbvocabulary VALUES("821","19","2","APP_requireArea","É obrigatório selecionar uma área.","A");
INSERT INTO tbvocabulary VALUES("822","19","2","APP_requireDescription","É obrigatório inserir uma descrição.","A");
INSERT INTO tbvocabulary VALUES("823","19","2","APP_requireEqualPass","As senhas devem ser iguais.","A");
INSERT INTO tbvocabulary VALUES("824","19","2","APP_requireItem","É obrigatório selecionar um item.","A");
INSERT INTO tbvocabulary VALUES("825","19","2","APP_requirePass","É obrigatório inserir a senha.","A");
INSERT INTO tbvocabulary VALUES("826","19","2","APP_requireReason","É obrigatório selecionar uma razão.","A");
INSERT INTO tbvocabulary VALUES("827","19","2","APP_requireService","É obrigatório selecionar um serviço.","A");
INSERT INTO tbvocabulary VALUES("828","19","2","APP_requireTitle","É obrigatorio inserir um título.","A");
INSERT INTO tbvocabulary VALUES("829","19","2","APP_requireType","É obrigatório selecionar um tipo.","A");
INSERT INTO tbvocabulary VALUES("830","19","2","APP_requireUser","É obrigatório inserir o usuário.","A");
INSERT INTO tbvocabulary VALUES("831","19","2","APP_sendButton","Enviar","A");
INSERT INTO tbvocabulary VALUES("832","19","2","APP_serviceLabel","Serviço","A");
INSERT INTO tbvocabulary VALUES("833","19","2","APP_ShowTicket_title","Solicitação","A");
INSERT INTO tbvocabulary VALUES("834","19","2","APP_stateLabel","Estado","A");
INSERT INTO tbvocabulary VALUES("835","19","2","APP_statusLabel","Status","A");
INSERT INTO tbvocabulary VALUES("836","19","2","APP_titleLabel","Título","A");
INSERT INTO tbvocabulary VALUES("837","19","2","APP_typeLabel","Tipo","A");
INSERT INTO tbvocabulary VALUES("838","19","2","APP_urlLabel","Url da API","A");
INSERT INTO tbvocabulary VALUES("839","19","2","APP_urlPlaceholder","https://...","A");
INSERT INTO tbvocabulary VALUES("840","19","2","APP_urlRequired","É Obrigatório inserir a URL.","A");
INSERT INTO tbvocabulary VALUES("841","19","2","APP_userLabel","Digite seu nome de usuário","A");
INSERT INTO tbvocabulary VALUES("842","19","2","APP_userPlaceholder","Usuário","A");
INSERT INTO tbvocabulary VALUES("843","19","2","APP_userRequired","É obrigatório inserir o usuário.","A");
INSERT INTO tbvocabulary VALUES("844","19","2","hdk_Navbar_name","HelpDEZK","A");
INSERT INTO tbvocabulary VALUES("845","19","2","pgr_cost_center","Cost Center","A");
INSERT INTO tbvocabulary VALUES("846","19","2","pgr_dash_widgets","Widgets","A");
INSERT INTO tbvocabulary VALUES("847","19","2","pgr_downloads","Downloads","A");
INSERT INTO tbvocabulary VALUES("848","19","2","pgr_email_config","Email Configuration","A");
INSERT INTO tbvocabulary VALUES("849","19","2","pgr_email_request","Requests by Email","A");
INSERT INTO tbvocabulary VALUES("850","19","2","pgr_evaluation","Evaluation","A");
INSERT INTO tbvocabulary VALUES("851","19","2","pgr_groups","Groups","A");
INSERT INTO tbvocabulary VALUES("852","19","2","pgr_holidays","Holidays","A");
INSERT INTO tbvocabulary VALUES("853","19","2","pgr_import_people","Users Import","A");
INSERT INTO tbvocabulary VALUES("854","19","2","pgr_import_services","Import Services Catalog","A");
INSERT INTO tbvocabulary VALUES("855","19","2","pgr_logos","Logos","A");
INSERT INTO tbvocabulary VALUES("856","19","2","pgr_modules","Modules","A");
INSERT INTO tbvocabulary VALUES("857","19","2","pgr_ope_aver_resptime","Operator Average Response Time","A");
INSERT INTO tbvocabulary VALUES("858","19","2","pgr_people","People & Companies","A");
INSERT INTO tbvocabulary VALUES("859","19","2","pgr_person_report","Person Report","A");
INSERT INTO tbvocabulary VALUES("860","19","2","pgr_priority","Priority","A");
INSERT INTO tbvocabulary VALUES("861","19","2","pgr_programs","Programs","A");
INSERT INTO tbvocabulary VALUES("862","19","2","pgr_rejects_request","Reject Requests","A");
INSERT INTO tbvocabulary VALUES("863","19","2","pgr_request_department","Request by Department","A");
INSERT INTO tbvocabulary VALUES("864","19","2","pgr_request_operator","Requests by Operator","A");
INSERT INTO tbvocabulary VALUES("865","19","2","pgr_request_status","Requests by Status","A");
INSERT INTO tbvocabulary VALUES("866","19","2","pgr_req_reason","Request Reason","A");
INSERT INTO tbvocabulary VALUES("867","19","2","pgr_req_reports","Total Requests Report","A");
INSERT INTO tbvocabulary VALUES("868","19","2","pgr_services","Services","A");
INSERT INTO tbvocabulary VALUES("869","19","2","pgr_status","Status","A");
INSERT INTO tbvocabulary VALUES("870","19","2","pgr_summarized_department","Summarized by Department","A");
INSERT INTO tbvocabulary VALUES("871","19","2","pgr_summarized_operator","Summarized by Operator","A");
INSERT INTO tbvocabulary VALUES("872","19","2","pgr_sys_features","System Features","A");
INSERT INTO tbvocabulary VALUES("873","19","2","pgr_type_permission","Type Person Permission","A");
INSERT INTO tbvocabulary VALUES("874","19","2","pgr_user_satisfaction","User Satisfaction","A");
INSERT INTO tbvocabulary VALUES("875","19","2","pgr_vocabulary","Vocabulary","A");
INSERT INTO tbvocabulary VALUES("876","19","2","pgr_warnings","Warnings","A");
INSERT INTO tbvocabulary VALUES("877","19","2","pgr_worked_requests","Worked Requests","A");
INSERT INTO tbvocabulary VALUES("878","19","2","pgr_work_calendar","Work Calendar","A");
INSERT INTO tbvocabulary VALUES("879","19","2","Abbreviation","Abbreviation","A");
INSERT INTO tbvocabulary VALUES("880","19","2","Abilities","Abilities","A");
INSERT INTO tbvocabulary VALUES("881","19","2","Access","Access","A");
INSERT INTO tbvocabulary VALUES("882","19","2","Access_denied","Access denied.","A");
INSERT INTO tbvocabulary VALUES("883","19","2","Acess_level","Access Level","A");
INSERT INTO tbvocabulary VALUES("884","19","2","Activate","Activate","A");
INSERT INTO tbvocabulary VALUES("885","19","2","Add","Add","A");
INSERT INTO tbvocabulary VALUES("886","19","2","Added_notes","Added Notes","A");
INSERT INTO tbvocabulary VALUES("887","19","2","Add_category","Add Category","A");
INSERT INTO tbvocabulary VALUES("888","19","2","Add_item","Add Item","A");
INSERT INTO tbvocabulary VALUES("889","19","2","add_new_feature","Add New Feature","A");
INSERT INTO tbvocabulary VALUES("890","19","2","Add_service","Add Service","A");
INSERT INTO tbvocabulary VALUES("891","19","2","Add_widget","Add Widget","A");
INSERT INTO tbvocabulary VALUES("892","19","2","admin_dashboard","Administration Dashboard","A");
INSERT INTO tbvocabulary VALUES("893","19","2","adodb_version","Adodb Version:","A");
INSERT INTO tbvocabulary VALUES("894","19","2","Adress","Address","A");
INSERT INTO tbvocabulary VALUES("895","19","2","Alert_activated","Succesfully activated.","A");
INSERT INTO tbvocabulary VALUES("896","19","2","Alert_activated_error","Failure to activate.","A");
INSERT INTO tbvocabulary VALUES("897","19","2","Alert_add_config_categ_title","Don\'t forget to add the category entry in \'app/lang/[idioma].txt\'","A");
INSERT INTO tbvocabulary VALUES("898","19","2","Alert_add_feature_title","Don\'t forget to add the feature entry in \'app/lang/[idioma].txt\'","A");
INSERT INTO tbvocabulary VALUES("899","19","2","Alert_add_module_title","Don\'t forget to add the module entry to \'app/lang/[idioma].txt\'","A");
INSERT INTO tbvocabulary VALUES("900","19","2","Alert_add_program_title","Do not forget to add the program entry in \'app/lang/[idioma].txt\'","A");
INSERT INTO tbvocabulary VALUES("901","19","2","Alert_approve","You have requests waiting for approval, do you want to approve them now?","A");
INSERT INTO tbvocabulary VALUES("902","19","2","Alert_Cancel_sucess","Request succesfully canceled","A");
INSERT INTO tbvocabulary VALUES("903","19","2","Alert_change_password","Password changed successfully","A");
INSERT INTO tbvocabulary VALUES("904","19","2","Alert_choose_area","Please choose an area","A");
INSERT INTO tbvocabulary VALUES("905","19","2","Alert_choose_item","Please choose an item","A");
INSERT INTO tbvocabulary VALUES("906","19","2","Alert_choose_service","Please choose a service","A");
INSERT INTO tbvocabulary VALUES("907","19","2","Alert_choose_type","Please choose a type","A");
INSERT INTO tbvocabulary VALUES("908","19","2","Alert_close_request","Request finished","A");
INSERT INTO tbvocabulary VALUES("909","19","2","Alert_deactivated","Succesfully deactivated.","A");
INSERT INTO tbvocabulary VALUES("910","19","2","Alert_deactivated_error","Failure to desactivate.","A");
INSERT INTO tbvocabulary VALUES("911","19","2","Alert_deleted","Succesfully Deleted.","A");
INSERT INTO tbvocabulary VALUES("912","19","2","Alert_deleted_error","Failure to delete.","A");
INSERT INTO tbvocabulary VALUES("913","19","2","Alert_deleted_note","Note deleted","A");
INSERT INTO tbvocabulary VALUES("914","19","2","Alert_department_person","This Department has linked users. <br> To delete it, you need to move users to a new department. <br> Select the destination Department.","A");
INSERT INTO tbvocabulary VALUES("915","19","2","Alert_different_passwords","Passwords do not match","A");
INSERT INTO tbvocabulary VALUES("916","19","2","Alert_dont_delete_area","This area is linked to one or several requests. <br> This operation cannot be performed.","A");
INSERT INTO tbvocabulary VALUES("917","19","2","Alert_dont_delete_item","This item is linked to one or several requests. <br> This operation cannot be performed.","A");
INSERT INTO tbvocabulary VALUES("918","19","2","Alert_dont_delete_service","This service is bound to one or several requests. <br> This operation cannot be performed.","A");
INSERT INTO tbvocabulary VALUES("919","19","2","Alert_dont_delete_type","This type is linked to one or several requests. <br> This operation cannot be performed.","A");
INSERT INTO tbvocabulary VALUES("920","19","2","Alert_empty_note","Please fill the note\'s body","A");
INSERT INTO tbvocabulary VALUES("921","19","2","Alert_empty_reason","Fill in the reason","A");
INSERT INTO tbvocabulary VALUES("922","19","2","Alert_empty_subject","Please fill the request\'s subject","A");
INSERT INTO tbvocabulary VALUES("923","19","2","Alert_external_settings_OK","Successfully Saved External Settings !!!","A");
INSERT INTO tbvocabulary VALUES("924","19","2","Alert_failure","Couldn\'t insert","A");
INSERT INTO tbvocabulary VALUES("925","19","2","Alert_field_required","This field is required.","A");
INSERT INTO tbvocabulary VALUES("926","19","2","Alert_follow_repass","Choose one of the options on the follow.","A");
INSERT INTO tbvocabulary VALUES("927","19","2","Alert_get_data","Unable to get data","A");
INSERT INTO tbvocabulary VALUES("928","19","2","Alert_import_services_nofile_failure","Could not import !! Attach the file .","A");
INSERT INTO tbvocabulary VALUES("929","19","2","Alert_inserted","Succesfully Inserted","A");
INSERT INTO tbvocabulary VALUES("930","19","2","Alert_invalid_email","Invalid Email Format","A");
INSERT INTO tbvocabulary VALUES("931","19","2","Alert_note_sucess","Note succesfully inserted","A");
INSERT INTO tbvocabulary VALUES("932","19","2","Alert_not_match_new_pass","The new password cannot be the same as the current password!","A");
INSERT INTO tbvocabulary VALUES("933","19","2","Alert_reopen_sucess","Request reopened","A");
INSERT INTO tbvocabulary VALUES("934","19","2","Alert_select_one","Please select 1 item","A");
INSERT INTO tbvocabulary VALUES("935","19","2","Alert_success_update","Data updated successfully","A");
INSERT INTO tbvocabulary VALUES("936","19","2","Alert_sucess_category","Category successfully registered","A");
INSERT INTO tbvocabulary VALUES("937","19","2","Alert_sucess_module","Module succesfully inserted.","A");
INSERT INTO tbvocabulary VALUES("938","19","2","Alert_sucess_repass","Succesfully repassed!","A");
INSERT INTO tbvocabulary VALUES("939","19","2","Alert_wrong_extension_csv","Invalid file extension. Are permitted only files with the extension CSV","A");
INSERT INTO tbvocabulary VALUES("940","19","2","all","All","A");
INSERT INTO tbvocabulary VALUES("941","19","2","and","and","A");
INSERT INTO tbvocabulary VALUES("942","19","2","Approve_no","No.","A");
INSERT INTO tbvocabulary VALUES("943","19","2","Approve_obs","Yes, with observation.","A");
INSERT INTO tbvocabulary VALUES("944","19","2","Approve_text","Do you approve the operators attendance?","A");
INSERT INTO tbvocabulary VALUES("945","19","2","Approve_yes","Yes.","A");
INSERT INTO tbvocabulary VALUES("946","19","2","April","April","A");
INSERT INTO tbvocabulary VALUES("947","19","2","Area","Area","A");
INSERT INTO tbvocabulary VALUES("948","19","2","Area_edit","Edit Area","A");
INSERT INTO tbvocabulary VALUES("949","19","2","Area_insert","Insert Area","A");
INSERT INTO tbvocabulary VALUES("950","19","2","Area_name","Area\'s name","A");
INSERT INTO tbvocabulary VALUES("951","19","2","Assumed_successfully","Assumed successfully!","A");
INSERT INTO tbvocabulary VALUES("952","19","2","Assume_request","Assume Request","A");
INSERT INTO tbvocabulary VALUES("953","19","2","Attach","Attach","A");
INSERT INTO tbvocabulary VALUES("954","19","2","Attachments","Attachments","A");
INSERT INTO tbvocabulary VALUES("955","19","2","Attendance","Attendance","A");
INSERT INTO tbvocabulary VALUES("956","19","2","Attendance_time","Attendance time","A");
INSERT INTO tbvocabulary VALUES("957","19","2","Attendants_by_group","View operators by group","A");
INSERT INTO tbvocabulary VALUES("958","19","2","Attend_level","Attendance level","A");
INSERT INTO tbvocabulary VALUES("959","19","2","Attend_time","Attendance Time","A");
INSERT INTO tbvocabulary VALUES("960","19","2","Att_way","Attendance Way","A");
INSERT INTO tbvocabulary VALUES("961","19","2","Att_way_min","Atend. Way","A");
INSERT INTO tbvocabulary VALUES("962","19","2","Att_way_new","New Atend. Way","A");
INSERT INTO tbvocabulary VALUES("963","19","2","August","August","A");
INSERT INTO tbvocabulary VALUES("964","19","2","auxiliary_operator_include","Include Auxiliary Operator","A");
INSERT INTO tbvocabulary VALUES("965","19","2","Available","Available","A");
INSERT INTO tbvocabulary VALUES("966","19","2","Available_for","Available for","A");
INSERT INTO tbvocabulary VALUES("967","19","2","Available_text","Available for record on request.","A");
INSERT INTO tbvocabulary VALUES("968","19","2","Average","Average","A");
INSERT INTO tbvocabulary VALUES("969","19","2","Back_btn","Back","A");
INSERT INTO tbvocabulary VALUES("970","19","2","Bar_hide","Hide Bar","A");
INSERT INTO tbvocabulary VALUES("971","19","2","Bar_show","Show Bar","A");
INSERT INTO tbvocabulary VALUES("972","19","2","Birth_date","Birth Date","A");
INSERT INTO tbvocabulary VALUES("973","19","2","Branch","Branch","A");
INSERT INTO tbvocabulary VALUES("974","19","2","btn_assume","Assume","A");
INSERT INTO tbvocabulary VALUES("975","19","2","btn_cancel","Cancel","A");
INSERT INTO tbvocabulary VALUES("976","19","2","btn_close","Close","A");
INSERT INTO tbvocabulary VALUES("977","19","2","Btn_evaluate","Evaluate","A");
INSERT INTO tbvocabulary VALUES("978","19","2","btn_ope_aux","Operator Auxiliary","A");
INSERT INTO tbvocabulary VALUES("979","19","2","btn_reject","Reject","A");
INSERT INTO tbvocabulary VALUES("980","19","2","btn_reopen","Reopen","A");
INSERT INTO tbvocabulary VALUES("981","19","2","btn_save_changes","Save Changes","A");
INSERT INTO tbvocabulary VALUES("982","19","2","btn_submit","Submit","A");
INSERT INTO tbvocabulary VALUES("983","19","2","btn_update_userdata","Update your data ","A");
INSERT INTO tbvocabulary VALUES("984","19","2","By_company","by company","A");
INSERT INTO tbvocabulary VALUES("985","19","2","By_group","by group","A");
INSERT INTO tbvocabulary VALUES("986","19","2","Cancel_btn","Cancel","A");
INSERT INTO tbvocabulary VALUES("987","19","2","Categories","Categories","A");
INSERT INTO tbvocabulary VALUES("988","19","2","Category","Category","A");
INSERT INTO tbvocabulary VALUES("989","19","2","Category_insert","Category Insert","A");
INSERT INTO tbvocabulary VALUES("990","19","2","cat_config","Config","A");
INSERT INTO tbvocabulary VALUES("991","19","2","cat_records","Records","A");
INSERT INTO tbvocabulary VALUES("992","19","2","cat_reports","Reports","A");
INSERT INTO tbvocabulary VALUES("993","19","2","Change","Change","A");
INSERT INTO tbvocabulary VALUES("994","19","2","Change_date","Change Date","A");
INSERT INTO tbvocabulary VALUES("995","19","2","Change_password","Change Password","A");
INSERT INTO tbvocabulary VALUES("996","19","2","Change_password_required","Forcing user to change password.","A");
INSERT INTO tbvocabulary VALUES("997","19","2","Change_permissions","Change Permissions","A");
INSERT INTO tbvocabulary VALUES("998","19","2","Choose_format","Choose the format you want to export your report.","A");
INSERT INTO tbvocabulary VALUES("999","19","2","City","City","A");
INSERT INTO tbvocabulary VALUES("1000","19","2","Classification","Classification","A");
INSERT INTO tbvocabulary VALUES("1001","19","2","Classification_text","Set this item as \'Classification Free\'.","A");
INSERT INTO tbvocabulary VALUES("1002","19","2","Client","Costumer","A");
INSERT INTO tbvocabulary VALUES("1003","19","2","Close","Close","A");
INSERT INTO tbvocabulary VALUES("1004","19","2","Closed","Closed","A");
INSERT INTO tbvocabulary VALUES("1005","19","2","Code","Code","A");
INSERT INTO tbvocabulary VALUES("1006","19","2","Color","Color","A");
INSERT INTO tbvocabulary VALUES("1007","19","2","Company","Company","A");
INSERT INTO tbvocabulary VALUES("1008","19","2","Complement","Complement","A");
INSERT INTO tbvocabulary VALUES("1009","19","2","Confirm_close","Close request?","A");
INSERT INTO tbvocabulary VALUES("1010","19","2","Confirm_password","Confirm Password","A");
INSERT INTO tbvocabulary VALUES("1011","19","2","conf_approvals","Configure Approvals","A");
INSERT INTO tbvocabulary VALUES("1012","19","2","Contact_person","Contact Person","A");
INSERT INTO tbvocabulary VALUES("1013","19","2","Controller","Controller","A");
INSERT INTO tbvocabulary VALUES("1014","19","2","Country","Country","A");
INSERT INTO tbvocabulary VALUES("1015","19","2","country_default","Default Country","A");
INSERT INTO tbvocabulary VALUES("1016","19","2","cpf","SSN","A");
INSERT INTO tbvocabulary VALUES("1017","19","2","Create_user","Create user","A");
INSERT INTO tbvocabulary VALUES("1018","19","2","Create_user_msg","Create new user if this not exist in the Helpdezk.","A");
INSERT INTO tbvocabulary VALUES("1019","19","2","Current_date","Current Date","A");
INSERT INTO tbvocabulary VALUES("1020","19","2","Current_time","Current Time","A");
INSERT INTO tbvocabulary VALUES("1021","19","2","Dashboard","Dashboard","A");
INSERT INTO tbvocabulary VALUES("1022","19","2","Dashboard_SLAFulfillment","Fulfillment","A");
INSERT INTO tbvocabulary VALUES("1023","19","2","Dashboard_SLANotFulfillment","Not Fulfillment","A");
INSERT INTO tbvocabulary VALUES("1024","19","2","Dashboard_UpdatedDaily","Updated daily","A");
INSERT INTO tbvocabulary VALUES("1025","19","2","Date","Date","A");
INSERT INTO tbvocabulary VALUES("1026","19","2","Day","day","A");
INSERT INTO tbvocabulary VALUES("1027","19","2","Days","Days","A");
INSERT INTO tbvocabulary VALUES("1028","19","2","Deactivate","Deactivate","A");
INSERT INTO tbvocabulary VALUES("1029","19","2","December","December","A");
INSERT INTO tbvocabulary VALUES("1030","19","2","Default","Default","A");
INSERT INTO tbvocabulary VALUES("1031","19","2","Default_department","Default Department","A");
INSERT INTO tbvocabulary VALUES("1032","19","2","Default_department_msg","you can add more departments in the department\'s insert program","A");
INSERT INTO tbvocabulary VALUES("1033","19","2","Default_text","Use this priority as default when a request is opened.","A");
INSERT INTO tbvocabulary VALUES("1034","19","2","Delete","Delete","A");
INSERT INTO tbvocabulary VALUES("1035","19","2","Delete_emails","Delete emails","A");
INSERT INTO tbvocabulary VALUES("1036","19","2","Delete_emails_msg","Delete emails from the server when downloading.","A");
INSERT INTO tbvocabulary VALUES("1037","19","2","Delete_module","Are you sure you want to delete this module?","A");
INSERT INTO tbvocabulary VALUES("1038","19","2","Delete_record","Are you sure you want to delete this entry?","A");
INSERT INTO tbvocabulary VALUES("1039","19","2","Delete_widget","Are you sure you want to delete this widget?","A");
INSERT INTO tbvocabulary VALUES("1040","19","2","Delimiter","Delimiter","A");
INSERT INTO tbvocabulary VALUES("1041","19","2","Department","Departament","A");
INSERT INTO tbvocabulary VALUES("1042","19","2","Departments","Departaments","A");
INSERT INTO tbvocabulary VALUES("1043","19","2","Department_exists","Department already recorded!","A");
INSERT INTO tbvocabulary VALUES("1044","19","2","Department_name","Departament\'s name","A");
INSERT INTO tbvocabulary VALUES("1045","19","2","Description","Description","A");
INSERT INTO tbvocabulary VALUES("1046","19","2","Domain","Domain","A");
INSERT INTO tbvocabulary VALUES("1047","19","2","Downloads","Downloads","A");
INSERT INTO tbvocabulary VALUES("1048","19","2","Drag_image_msg","Drag the file with the image<br> or<br> click here.","A");
INSERT INTO tbvocabulary VALUES("1049","19","2","Drag_import_file_msg","<br>Drag the file with the data<br> or<br> click here.","A");
INSERT INTO tbvocabulary VALUES("1050","19","2","Drag_widget","Drag your widgets here","A");
INSERT INTO tbvocabulary VALUES("1051","19","2","dropzone_File_Too_Big","Excedeu o tamanho máximo {{filesize}}","A");
INSERT INTO tbvocabulary VALUES("1052","19","2","dropzone_invalid_dimension","A imagem tem que ser quadrada","A");
INSERT INTO tbvocabulary VALUES("1053","19","2","dropzone_remove_file","Remover arquivo","A");
INSERT INTO tbvocabulary VALUES("1054","19","2","dropzone_user_photot_message","<br>Para atualizar sua foto, <br> arraste o arquivo com a imagem<br> ou<br> clique aqui.<br><br>A imagem tem que ser quadrada !!! ","A");
INSERT INTO tbvocabulary VALUES("1055","19","2","dsh_installer_dir","Installer directory","A");
INSERT INTO tbvocabulary VALUES("1056","19","2","dsh_msg_installer","For security reasons, please remove <b>installer/</b> directory from your server !!!","A");
INSERT INTO tbvocabulary VALUES("1057","19","2","dsh_warning","Warning","A");
INSERT INTO tbvocabulary VALUES("1058","19","2","edit","Edit","A");
INSERT INTO tbvocabulary VALUES("1059","19","2","Editor_Placeholder_description","Insert your descrition here ...","A");
INSERT INTO tbvocabulary VALUES("1060","19","2","Editor_Placeholder_insert","Insert your note here ...","A");
INSERT INTO tbvocabulary VALUES("1061","19","2","Editor_Placeholder_reason","Enter reason here...","A");
INSERT INTO tbvocabulary VALUES("1062","19","2","Editor_Placeholder_solution","Enter request solution here...","A");
INSERT INTO tbvocabulary VALUES("1063","19","2","Edit_btn","Edit","A");
INSERT INTO tbvocabulary VALUES("1064","19","2","Edit_failure","Can\'t Edit!","A");
INSERT INTO tbvocabulary VALUES("1065","19","2","Edit_layout","Edit Layout","A");
INSERT INTO tbvocabulary VALUES("1066","19","2","Edit_sucess","Succesfully edited!","A");
INSERT INTO tbvocabulary VALUES("1067","19","2","EIN_CNPJ","EIN","A");
INSERT INTO tbvocabulary VALUES("1068","19","2","email","E-mail","A");
INSERT INTO tbvocabulary VALUES("1069","19","2","Email_byCron","Send the emails of the requests by cron","A");
INSERT INTO tbvocabulary VALUES("1070","19","2","Email_config","Email Configuration","A");
INSERT INTO tbvocabulary VALUES("1071","19","2","Email_host","Email host","A");
INSERT INTO tbvocabulary VALUES("1072","19","2","Email_sender","Email sender","A");
INSERT INTO tbvocabulary VALUES("1073","19","2","Empty","No Items","A");
INSERT INTO tbvocabulary VALUES("1074","19","2","Environment_settings","Environment Settings","A");
INSERT INTO tbvocabulary VALUES("1075","19","2","Equipment","Equipment","A");
INSERT INTO tbvocabulary VALUES("1076","19","2","ERP_Code","Code","A");
INSERT INTO tbvocabulary VALUES("1077","19","2","ERP_Log","Log","A");
INSERT INTO tbvocabulary VALUES("1078","19","2","Error","Error!","A");
INSERT INTO tbvocabulary VALUES("1079","19","2","Error_insert_note","Error when inserting note","A");
INSERT INTO tbvocabulary VALUES("1080","19","2","Error_Number_columns","Number of columns on line % of the file is invalid. Import canceled !!!","A");
INSERT INTO tbvocabulary VALUES("1081","19","2","Execution_date","Execution Date","A");
INSERT INTO tbvocabulary VALUES("1082","19","2","Expire_date","Expiry Date","A");
INSERT INTO tbvocabulary VALUES("1083","19","2","Expire_date_sucess","Expire date changed successfully!","A");
INSERT INTO tbvocabulary VALUES("1084","19","2","Export","Export","A");
INSERT INTO tbvocabulary VALUES("1085","19","2","external_hostname","External Host Name:","A");
INSERT INTO tbvocabulary VALUES("1086","19","2","external_ip","External Ip Address:","A");
INSERT INTO tbvocabulary VALUES("1087","19","2","Extra","Extra","A");
INSERT INTO tbvocabulary VALUES("1088","19","2","Failure_logs","Failure logs","A");
INSERT INTO tbvocabulary VALUES("1089","19","2","Feature_remove","Remove Feature","A");
INSERT INTO tbvocabulary VALUES("1090","19","2","February","February","A");
INSERT INTO tbvocabulary VALUES("1091","19","2","Female","Female","A");
INSERT INTO tbvocabulary VALUES("1092","19","2","File","File","A");
INSERT INTO tbvocabulary VALUES("1093","19","2","File_CSV","CSV file.","A");
INSERT INTO tbvocabulary VALUES("1094","19","2","File_PDF","PDF file.","A");
INSERT INTO tbvocabulary VALUES("1095","19","2","File_XLS","XLS dile.","A");
INSERT INTO tbvocabulary VALUES("1096","19","2","Fill","Fill","A");
INSERT INTO tbvocabulary VALUES("1097","19","2","Fill_adress","Fill Adress?","A");
INSERT INTO tbvocabulary VALUES("1098","19","2","Filter_by_sender","Filter by Sender","A");
INSERT INTO tbvocabulary VALUES("1099","19","2","Filter_by_subject","Filter by Subject","A");
INSERT INTO tbvocabulary VALUES("1100","19","2","Finished_alt","Finished","A");
INSERT INTO tbvocabulary VALUES("1101","19","2","Finish_btn","Finish","A");
INSERT INTO tbvocabulary VALUES("1102","19","2","Finish_date","Finish Date","A");
INSERT INTO tbvocabulary VALUES("1103","19","2","Footer","Footer","A");
INSERT INTO tbvocabulary VALUES("1104","19","2","Friday","Friday","A");
INSERT INTO tbvocabulary VALUES("1105","19","2","From","From","A");
INSERT INTO tbvocabulary VALUES("1106","19","2","Gender","Gender","A");
INSERT INTO tbvocabulary VALUES("1107","19","2","Generated","Generated","A");
INSERT INTO tbvocabulary VALUES("1108","19","2","grd_expired","Expired","A");
INSERT INTO tbvocabulary VALUES("1109","19","2","grd_expired_n_assumed","Expired not assumed","A");
INSERT INTO tbvocabulary VALUES("1110","19","2","grd_expiring","Expiring","A");
INSERT INTO tbvocabulary VALUES("1111","19","2","grd_expiring_today","Expiring today","A");
INSERT INTO tbvocabulary VALUES("1112","19","2","grd_show_all","Show all","A");
INSERT INTO tbvocabulary VALUES("1113","19","2","grd_show_group","Show from my groups","A");
INSERT INTO tbvocabulary VALUES("1114","19","2","grd_show_only_mine","Show only mine","A");
INSERT INTO tbvocabulary VALUES("1115","19","2","Grid_all","All","A");
INSERT INTO tbvocabulary VALUES("1116","19","2","Grid_all_tickets","All tickets","A");
INSERT INTO tbvocabulary VALUES("1117","19","2","Grid_being_attended","Being attended","A");
INSERT INTO tbvocabulary VALUES("1118","19","2","Grid_being_attended_tickets","Being attended Tickets","A");
INSERT INTO tbvocabulary VALUES("1119","19","2","Grid_expire_date","Deadline","A");
INSERT INTO tbvocabulary VALUES("1120","19","2","Grid_finished","Closed","A");
INSERT INTO tbvocabulary VALUES("1121","19","2","Grid_finished_tickets","Closed Tickets","A");
INSERT INTO tbvocabulary VALUES("1122","19","2","Grid_incharge","In charge","A");
INSERT INTO tbvocabulary VALUES("1123","19","2","Grid_new","New","A");
INSERT INTO tbvocabulary VALUES("1124","19","2","Grid_new_tickets","New tickets","A");
INSERT INTO tbvocabulary VALUES("1125","19","2","Grid_opening_date","Entry Date","A");
INSERT INTO tbvocabulary VALUES("1126","19","2","Grid_rejected","Rejected","A");
INSERT INTO tbvocabulary VALUES("1127","19","2","Grid_rejected_tickets","Rejected Tickets","A");
INSERT INTO tbvocabulary VALUES("1128","19","2","Grid_reload","Grid Reload","A");
INSERT INTO tbvocabulary VALUES("1129","19","2","Grid_status","Status","A");
INSERT INTO tbvocabulary VALUES("1130","19","2","Grid_subject","Subject","A");
INSERT INTO tbvocabulary VALUES("1131","19","2","Grid_view","View","A");
INSERT INTO tbvocabulary VALUES("1132","19","2","Grid_waiting_my_approval","Waiting my approval","A");
INSERT INTO tbvocabulary VALUES("1133","19","2","Grid_waiting_my_approval_tickets","Waiting approval Tickets ","A");
INSERT INTO tbvocabulary VALUES("1134","19","2","Group","Group","A");
INSERT INTO tbvocabulary VALUES("1135","19","2","Groups","Groups","A");
INSERT INTO tbvocabulary VALUES("1136","19","2","Groups_by_service","View groups by services","A");
INSERT INTO tbvocabulary VALUES("1137","19","2","Group_name","Group\'s name","A");
INSERT INTO tbvocabulary VALUES("1138","19","2","Group_operators","Group Operators","A");
INSERT INTO tbvocabulary VALUES("1139","19","2","Group_still_viewing","I want my group to keep viewing this request","A");
INSERT INTO tbvocabulary VALUES("1140","19","2","hdk_exceed_max_file_size","This file will not be processed. Exceeds maximum upload size: {{maxFilesize}}MB","A");
INSERT INTO tbvocabulary VALUES("1141","19","2","hdk_remove_file","Remove File","A");
INSERT INTO tbvocabulary VALUES("1142","19","2","Header","Header","A");
INSERT INTO tbvocabulary VALUES("1143","19","2","helpdezk_path","Helpdezk Path:","A");
INSERT INTO tbvocabulary VALUES("1144","19","2","helpdezk_version","Helpdezk Version:","A");
INSERT INTO tbvocabulary VALUES("1145","19","2","Holiday","Holiday","A");
INSERT INTO tbvocabulary VALUES("1146","19","2","Holidays","Holidays","A");
INSERT INTO tbvocabulary VALUES("1147","19","2","Holiday_des","Holiday Description","A");
INSERT INTO tbvocabulary VALUES("1148","19","2","Holiday_import","Holiday\'s Import","A");
INSERT INTO tbvocabulary VALUES("1149","19","2","Home","Home","A");
INSERT INTO tbvocabulary VALUES("1150","19","2","Hour","Hour","A");
INSERT INTO tbvocabulary VALUES("1151","19","2","Hours","Hours","A");
INSERT INTO tbvocabulary VALUES("1152","19","2","Import","Import","A");
INSERT INTO tbvocabulary VALUES("1153","19","2","Important_notices","Important Notices","A");
INSERT INTO tbvocabulary VALUES("1154","19","2","Import_error_file","Unable to write file for import.","A");
INSERT INTO tbvocabulary VALUES("1155","19","2","Import_failure","Couldn\'t Import","A");
INSERT INTO tbvocabulary VALUES("1156","19","2","Import_layout_error","Import file layout error - wrong number of columns.","A");
INSERT INTO tbvocabulary VALUES("1157","19","2","Import_not_writable","Not allowed to save import file","A");
INSERT INTO tbvocabulary VALUES("1158","19","2","Import_successfull","Successfully Imported","A");
INSERT INTO tbvocabulary VALUES("1159","19","2","Import_to","Import to","A");
INSERT INTO tbvocabulary VALUES("1160","19","2","Info_header_logo","Header logo must have 35pixels in height, all images with different proportions will be resized.","A");
INSERT INTO tbvocabulary VALUES("1161","19","2","Info_login_logo","Login page logo must have 70pixels in height, all images with different proportions will be resized.","A");
INSERT INTO tbvocabulary VALUES("1162","19","2","Info_reports_logo","Reports logo must have 40pixels in height, all images with different proportions will be resized.","A");
INSERT INTO tbvocabulary VALUES("1163","19","2","Initial_date","Initial Date","A");
INSERT INTO tbvocabulary VALUES("1164","19","2","Insert_note","Insert Note","A");
INSERT INTO tbvocabulary VALUES("1165","19","2","Integration_ldap","LDAP/AD Integration","A");
INSERT INTO tbvocabulary VALUES("1166","19","2","Item","Item","A");
INSERT INTO tbvocabulary VALUES("1167","19","2","Item_edit","Item Edit","A");
INSERT INTO tbvocabulary VALUES("1168","19","2","Item_insert","Item Insert","A");
INSERT INTO tbvocabulary VALUES("1169","19","2","Item_name","Item\'s name","A");
INSERT INTO tbvocabulary VALUES("1170","19","2","itens","items","A");
INSERT INTO tbvocabulary VALUES("1171","19","2","January","January","A");
INSERT INTO tbvocabulary VALUES("1172","19","2","jquery_version","Jquery Version:","A");
INSERT INTO tbvocabulary VALUES("1173","19","2","July","July","A");
INSERT INTO tbvocabulary VALUES("1174","19","2","June","June","A");
INSERT INTO tbvocabulary VALUES("1175","19","2","juridical","Juridical","A");
INSERT INTO tbvocabulary VALUES("1176","19","2","key_no_accents_no_whitespace","Key name cannot contain accents or blanks","A");
INSERT INTO tbvocabulary VALUES("1177","19","2","Lbl_photo","Photo","A");
INSERT INTO tbvocabulary VALUES("1178","19","2","lbl_session_name","Session Variable Name","A");
INSERT INTO tbvocabulary VALUES("1179","19","2","lbl_value","Value","A");
INSERT INTO tbvocabulary VALUES("1180","19","2","ldap_dn","Distinguished Names","A");
INSERT INTO tbvocabulary VALUES("1181","19","2","ldap_domain","Domain","A");
INSERT INTO tbvocabulary VALUES("1182","19","2","ldap_field","Object AD/LDAP","A");
INSERT INTO tbvocabulary VALUES("1183","19","2","ldap_field_obs","Field where the user is stored.","A");
INSERT INTO tbvocabulary VALUES("1184","19","2","ldap_server","Server ","A");
INSERT INTO tbvocabulary VALUES("1185","19","2","List_comp_groups","List of companies and their groups:","A");
INSERT INTO tbvocabulary VALUES("1186","19","2","Loading","Loading...","A");
INSERT INTO tbvocabulary VALUES("1187","19","2","Location","Location","A");
INSERT INTO tbvocabulary VALUES("1188","19","2","Location_insert","Location Insert","A");
INSERT INTO tbvocabulary VALUES("1189","19","2","Lock_text","Your are in lock screen. Main app was shut down and you need to go the login screen  to go back to app.","A");
INSERT INTO tbvocabulary VALUES("1190","19","2","Lock_unlock","Unlock","A");
INSERT INTO tbvocabulary VALUES("1191","19","2","Login","Login","A");
INSERT INTO tbvocabulary VALUES("1192","19","2","Login_cant_create_user","Cant create User !!!!","A");
INSERT INTO tbvocabulary VALUES("1193","19","2","Login_error_error","Wrong password, please try again.","A");
INSERT INTO tbvocabulary VALUES("1194","19","2","Login_error_secret","Wrong token, please try again !!!","A");
INSERT INTO tbvocabulary VALUES("1195","19","2","Login_exists","Login already recorded!","A");
INSERT INTO tbvocabulary VALUES("1196","19","2","Login_layout","Layout Login","A");
INSERT INTO tbvocabulary VALUES("1197","19","2","Login_page_logo","Login Page Logo","A");
INSERT INTO tbvocabulary VALUES("1198","19","2","Login_type","Login Type","A");
INSERT INTO tbvocabulary VALUES("1199","19","2","Login_user_inactive","Inactive user, please talk with one administrator.","A");
INSERT INTO tbvocabulary VALUES("1200","19","2","Login_user_not_exist","User don\'t exist, please check your typing!","A");
INSERT INTO tbvocabulary VALUES("1201","19","2","Logos_Title","Logos","A");
INSERT INTO tbvocabulary VALUES("1202","19","2","logout","Log out","A");
INSERT INTO tbvocabulary VALUES("1203","19","2","log_email","Email Logs","A");
INSERT INTO tbvocabulary VALUES("1204","19","2","log_general","General Logs","A");
INSERT INTO tbvocabulary VALUES("1205","19","2","log_host","Host Type","A");
INSERT INTO tbvocabulary VALUES("1206","19","2","log_level","Log Level","A");
INSERT INTO tbvocabulary VALUES("1207","19","2","log_remote_server","Remote Server","A");
INSERT INTO tbvocabulary VALUES("1208","19","2","Lost_password","I forgot my password","A");
INSERT INTO tbvocabulary VALUES("1209","19","2","Lost_password_ad","It is not possible to recovery the password in the Active Directory, contact the System Administrator.","A");
INSERT INTO tbvocabulary VALUES("1210","19","2","Lost_password_body","<br><br><br><p>We inform you that your new password is: <b>$pass</b> <br>This is an automatically message, please do not answer.</p><br><br><br><br>","A");
INSERT INTO tbvocabulary VALUES("1211","19","2","Lost_password_err","Falha ao enviar nova senha.","A");
INSERT INTO tbvocabulary VALUES("1212","19","2","Lost_password_log","Sent e-mail lost password - ","A");
INSERT INTO tbvocabulary VALUES("1213","19","2","Lost_password_master","User master password can not be changed","A");
INSERT INTO tbvocabulary VALUES("1214","19","2","Lost_password_not","User don\'t exist, please check your typing","A");
INSERT INTO tbvocabulary VALUES("1215","19","2","Lost_password_pop","It is not possible to recovery the password at POP mode, contact the System Administrator.","A");
INSERT INTO tbvocabulary VALUES("1216","19","2","Lost_password_subject","Password Reminder","A");
INSERT INTO tbvocabulary VALUES("1217","19","2","Lost_password_suc","New password send with success. ","A");
INSERT INTO tbvocabulary VALUES("1218","19","2","Maintenance","Maintenance","A");
INSERT INTO tbvocabulary VALUES("1219","19","2","Male","Male","A");
INSERT INTO tbvocabulary VALUES("1220","19","2","Manage_fail_import_file","Failed to import the csv file with the data.","A");
INSERT INTO tbvocabulary VALUES("1221","19","2","Manage_fail_move_file","Failed to move file data.\\nCheck the permissions on attachments directory and try again.","A");
INSERT INTO tbvocabulary VALUES("1222","19","2","Manage_fail_open_file_in","Failed to open the data file in ","A");
INSERT INTO tbvocabulary VALUES("1223","19","2","Manage_fail_open_file_per","\\nCheck the permissions and try again.","A");
INSERT INTO tbvocabulary VALUES("1224","19","2","Manage_instructions","Download the instructions for importing: ","A");
INSERT INTO tbvocabulary VALUES("1225","19","2","Manage_layout_service","Layout Import Services","A");
INSERT INTO tbvocabulary VALUES("1226","19","2","Manage_layout_service_file","Layout-Import-Services.pdf","A");
INSERT INTO tbvocabulary VALUES("1227","19","2","Manage_service_already_registered","is already registered.","A");
INSERT INTO tbvocabulary VALUES("1228","19","2","Manage_service_and_group","and the group ","A");
INSERT INTO tbvocabulary VALUES("1229","19","2","Manage_service_area","Registered new area -> Code:","A");
INSERT INTO tbvocabulary VALUES("1230","19","2","Manage_service_area_fail","Failed to register the service area ","A");
INSERT INTO tbvocabulary VALUES("1231","19","2","Manage_service_column_6","Column 6 must contain only numeric value. The value was entered ","A");
INSERT INTO tbvocabulary VALUES("1232","19","2","Manage_service_company_fail","Column 8 is required a valid filename company. The company % is not registered!","A");
INSERT INTO tbvocabulary VALUES("1233","19","2","Manage_service_completed","Process completed successfully","A");
INSERT INTO tbvocabulary VALUES("1234","19","2","Manage_service_default_pri","was associated with default priority, because the priority informed ","A");
INSERT INTO tbvocabulary VALUES("1235","19","2","Manage_service_fail","Fails to enroll the service ","A");
INSERT INTO tbvocabulary VALUES("1236","19","2","Manage_service_fail_code","Failed to determine the service code ","A");
INSERT INTO tbvocabulary VALUES("1237","19","2","Manage_service_fail_rel","Failed to register the relationship between service ","A");
INSERT INTO tbvocabulary VALUES("1238","19","2","Manage_service_finalized_line","Finalized the line ","A");
INSERT INTO tbvocabulary VALUES("1239","19","2","Manage_service_group_fail","Failed to register the group in person table: ","A");
INSERT INTO tbvocabulary VALUES("1240","19","2","Manage_service_group_fail2","Failed to register the service group","A");
INSERT INTO tbvocabulary VALUES("1241","19","2","Manage_service_group_register","! Joined new service group -> Code ","A");
INSERT INTO tbvocabulary VALUES("1242","19","2","Manage_service_group_using","* Using existing group -> Code ","A");
INSERT INTO tbvocabulary VALUES("1243","19","2","Manage_service_imp_canceled",". Import canceled!","A");
INSERT INTO tbvocabulary VALUES("1244","19","2","Manage_service_inf_line","given in row ","A");
INSERT INTO tbvocabulary VALUES("1245","19","2","Manage_service_inf_on_line","entered on line ","A");
INSERT INTO tbvocabulary VALUES("1246","19","2","Manage_service_in_service","in service ","A");
INSERT INTO tbvocabulary VALUES("1247","19","2","Manage_service_item","Joined new item -> ","A");
INSERT INTO tbvocabulary VALUES("1248","19","2","Manage_service_item_fail","Failed to register the service item ","A");
INSERT INTO tbvocabulary VALUES("1249","19","2","Manage_service_line","line ","A");
INSERT INTO tbvocabulary VALUES("1250","19","2","Manage_service_not_identify_priority","Unable to identify the priority service time on line ","A");
INSERT INTO tbvocabulary VALUES("1251","19","2","Manage_service_not_registered","Is not registered or is not attending. Line ","A");
INSERT INTO tbvocabulary VALUES("1252","19","2","Manage_service_on_line",", on line ","A");
INSERT INTO tbvocabulary VALUES("1253","19","2","Manage_service_pri_no_exist","does not exist in the system.","A");
INSERT INTO tbvocabulary VALUES("1254","19","2","Manage_service_register_service","Joined new service ","A");
INSERT INTO tbvocabulary VALUES("1255","19","2","Manage_service_type","Joined new TYPE -> Code: ","A");
INSERT INTO tbvocabulary VALUES("1256","19","2","Manage_service_type_fail","Failed to register the type of care ","A");
INSERT INTO tbvocabulary VALUES("1257","19","2","Manage_service_using_area","Using existing area -> Code: ","A");
INSERT INTO tbvocabulary VALUES("1258","19","2","Manage_service_using_item","Using existing item -> Code ","A");
INSERT INTO tbvocabulary VALUES("1259","19","2","Manage_service_using_type","Using existing type -> ","A");
INSERT INTO tbvocabulary VALUES("1260","19","2","Mange_service_order",", order","A");
INSERT INTO tbvocabulary VALUES("1261","19","2","March","March","A");
INSERT INTO tbvocabulary VALUES("1262","19","2","May","May","A");
INSERT INTO tbvocabulary VALUES("1263","19","2","Messages","Messages","A");
INSERT INTO tbvocabulary VALUES("1264","19","2","Message_title","You have $$ new messages .","A");
INSERT INTO tbvocabulary VALUES("1265","19","2","Minutes","Minutes","A");
INSERT INTO tbvocabulary VALUES("1266","19","2","Mobile_phone","Mobile Phone","A");
INSERT INTO tbvocabulary VALUES("1267","19","2","Module","Module","A");
INSERT INTO tbvocabulary VALUES("1268","19","2","Modules","Modules","A");
INSERT INTO tbvocabulary VALUES("1269","19","2","Module_default","Default module","A");
INSERT INTO tbvocabulary VALUES("1270","19","2","Module_insert","Module Insert","A");
INSERT INTO tbvocabulary VALUES("1271","19","2","Module_name","Module\'s name","A");
INSERT INTO tbvocabulary VALUES("1272","19","2","module_not_delete","This module cannot be deleted","A");
INSERT INTO tbvocabulary VALUES("1273","19","2","module_not_disable","This module cannot be disabled","A");
INSERT INTO tbvocabulary VALUES("1274","19","2","module_not_edit","This module cannot be edited","A");
INSERT INTO tbvocabulary VALUES("1275","19","2","Module_path","Module path","A");
INSERT INTO tbvocabulary VALUES("1276","19","2","Monday","Monday","A");
INSERT INTO tbvocabulary VALUES("1277","19","2","Month","Month","A");
INSERT INTO tbvocabulary VALUES("1278","19","2","Msg_change_operation","Warning! If you want change the operations permissions you need set the permissions again at program \'Type Person Permission\'","A");
INSERT INTO tbvocabulary VALUES("1279","19","2","mysql_version","Mysql Version:","A");
INSERT INTO tbvocabulary VALUES("1280","19","2","My_Tickets","My Tickets","A");
INSERT INTO tbvocabulary VALUES("1281","19","2","Name","Name","A");
INSERT INTO tbvocabulary VALUES("1282","19","2","National_holiday","National Holiday","A");
INSERT INTO tbvocabulary VALUES("1283","19","2","natural","Natural","A");
INSERT INTO tbvocabulary VALUES("1284","19","2","Neighborhood","Neighborhood","A");
INSERT INTO tbvocabulary VALUES("1285","19","2","New","New","A");
INSERT INTO tbvocabulary VALUES("1286","19","2","New_category","New Category","A");
INSERT INTO tbvocabulary VALUES("1287","19","2","New_date","New Date","A");
INSERT INTO tbvocabulary VALUES("1288","19","2","new_feature","New Feature","A");
INSERT INTO tbvocabulary VALUES("1289","19","2","New_messages","New messages","A");
INSERT INTO tbvocabulary VALUES("1290","19","2","New_password","New Password","A");
INSERT INTO tbvocabulary VALUES("1291","19","2","New_request","New Request","A");
INSERT INTO tbvocabulary VALUES("1292","19","2","New_time","New Time","A");
INSERT INTO tbvocabulary VALUES("1293","19","2","No","No","A");
INSERT INTO tbvocabulary VALUES("1294","19","2","Normal","Normal","A");
INSERT INTO tbvocabulary VALUES("1295","19","2","Note","Note","A");
INSERT INTO tbvocabulary VALUES("1296","19","2","Note_msg","Insert notes when answer emails.","A");
INSERT INTO tbvocabulary VALUES("1297","19","2","Notification","Notification","A");
INSERT INTO tbvocabulary VALUES("1298","19","2","Not_available_yet","Not available yet","A");
INSERT INTO tbvocabulary VALUES("1299","19","2","November","November","A");
INSERT INTO tbvocabulary VALUES("1300","19","2","No_abilities","There\'s no abilities related","A");
INSERT INTO tbvocabulary VALUES("1301","19","2","No_data","No data.","A");
INSERT INTO tbvocabulary VALUES("1302","19","2","No_notices","No important notice at the moment.","A");
INSERT INTO tbvocabulary VALUES("1303","19","2","no_permission_edit","You don\'t have permission to edit","A");
INSERT INTO tbvocabulary VALUES("1304","19","2","No_result","No results found.","A");
INSERT INTO tbvocabulary VALUES("1305","19","2","Number","Number","A");
INSERT INTO tbvocabulary VALUES("1306","19","2","Obrigatory_time","Please fill the time expended in this activity","A");
INSERT INTO tbvocabulary VALUES("1307","19","2","Observation","Observation","A");
INSERT INTO tbvocabulary VALUES("1308","19","2","October","October","A");
INSERT INTO tbvocabulary VALUES("1309","19","2","of","of","A");
INSERT INTO tbvocabulary VALUES("1310","19","2","Ok_btn","Ok","A");
INSERT INTO tbvocabulary VALUES("1311","19","2","Only_operator","Only operator","A");
INSERT INTO tbvocabulary VALUES("1312","19","2","on_time","On Time","A");
INSERT INTO tbvocabulary VALUES("1313","19","2","Opened_by","Opened By","A");
INSERT INTO tbvocabulary VALUES("1314","19","2","Opening_date","Entry Date","A");
INSERT INTO tbvocabulary VALUES("1315","19","2","Operation","Operation","A");
INSERT INTO tbvocabulary VALUES("1316","19","2","Operations","Operations","A");
INSERT INTO tbvocabulary VALUES("1317","19","2","Operator","Operator","A");
INSERT INTO tbvocabulary VALUES("1318","19","2","Operator_groups","Operator Groups","A");
INSERT INTO tbvocabulary VALUES("1319","19","2","Option_only_attendant_active","Active Attendants Only Option","A");
INSERT INTO tbvocabulary VALUES("1320","19","2","Option_only_operator","Option only for operators","A");
INSERT INTO tbvocabulary VALUES("1321","19","2","Other_items","Other Options","A");
INSERT INTO tbvocabulary VALUES("1322","19","2","overdue","Overdue","A");
INSERT INTO tbvocabulary VALUES("1323","19","2","Overtime","Overtime","A");
INSERT INTO tbvocabulary VALUES("1324","19","2","Page","Page","A");
INSERT INTO tbvocabulary VALUES("1325","19","2","Page_header_logo","Page Header Logo","A");
INSERT INTO tbvocabulary VALUES("1326","19","2","Password","Password","A");
INSERT INTO tbvocabulary VALUES("1327","19","2","PDF_code","Code","A");
INSERT INTO tbvocabulary VALUES("1328","19","2","PDF_Page","Page","A");
INSERT INTO tbvocabulary VALUES("1329","19","2","PDF_person_report","Person Report","A");
INSERT INTO tbvocabulary VALUES("1330","19","2","people","People & Companies","A");
INSERT INTO tbvocabulary VALUES("1331","19","2","Permissions","Permissions","A");
INSERT INTO tbvocabulary VALUES("1332","19","2","Permission_error","Error during operation","A");
INSERT INTO tbvocabulary VALUES("1333","19","2","Permission_Groups","Permission","A");
INSERT INTO tbvocabulary VALUES("1334","19","2","Permission_Groups_Select","Select the Permission Group","A");
INSERT INTO tbvocabulary VALUES("1335","19","2","pgr_departments","Departments","A");
INSERT INTO tbvocabulary VALUES("1336","19","2","pgr_erp_emailtemplate","E-mail Templates","A");
INSERT INTO tbvocabulary VALUES("1337","19","2","Phone","Phone","A");
INSERT INTO tbvocabulary VALUES("1338","19","2","php_version","Php Version:","A");
INSERT INTO tbvocabulary VALUES("1339","19","2","Placeholder_subject","Enter the subject","A");
INSERT INTO tbvocabulary VALUES("1340","19","2","Placeholder_zipcode","Enter de Zipcode","A");
INSERT INTO tbvocabulary VALUES("1341","19","2","plh_category_description","Enter Category Name","A");
INSERT INTO tbvocabulary VALUES("1342","19","2","plh_controller_description","Inform the Program Controller","A");
INSERT INTO tbvocabulary VALUES("1343","19","2","plh_holiday_date","Enter Holiday Date","A");
INSERT INTO tbvocabulary VALUES("1344","19","2","plh_holiday_description","Enter Holiday Description","A");
INSERT INTO tbvocabulary VALUES("1345","19","2","plh_module_description","Enter Module Name","A");
INSERT INTO tbvocabulary VALUES("1346","19","2","plh_module_path","Enter Module Path","A");
INSERT INTO tbvocabulary VALUES("1347","19","2","plh_module_prefix","Enter the table prefix","A");
INSERT INTO tbvocabulary VALUES("1348","19","2","plh_program_description","Enter Program Name","A");
INSERT INTO tbvocabulary VALUES("1349","19","2","plh_smarty_variable","Enter the smarty variable","A");
INSERT INTO tbvocabulary VALUES("1350","19","2","Pop_server","POP Server","A");
INSERT INTO tbvocabulary VALUES("1351","19","2","Port","Port","A");
INSERT INTO tbvocabulary VALUES("1352","19","2","Previous_year","Previous Year","A");
INSERT INTO tbvocabulary VALUES("1353","19","2","Print","Print","A");
INSERT INTO tbvocabulary VALUES("1354","19","2","Priority","Priority","A");
INSERT INTO tbvocabulary VALUES("1355","19","2","Processing","Processing","A");
INSERT INTO tbvocabulary VALUES("1356","19","2","Program","Program","A");
INSERT INTO tbvocabulary VALUES("1357","19","2","Programs","Programs","A");
INSERT INTO tbvocabulary VALUES("1358","19","2","pushover","PushOver API Configuration","A");
INSERT INTO tbvocabulary VALUES("1359","19","2","Reason","Reason","A");
INSERT INTO tbvocabulary VALUES("1360","19","2","Reason_no_registered","No registered reason","A");
INSERT INTO tbvocabulary VALUES("1361","19","2","Recalculate","Recalculate","A");
INSERT INTO tbvocabulary VALUES("1362","19","2","Recalculate_msg_chk","Recalculate service time after the end of the approvals","A");
INSERT INTO tbvocabulary VALUES("1363","19","2","records","Records","A");
INSERT INTO tbvocabulary VALUES("1364","19","2","Register_btn","Register","A");
INSERT INTO tbvocabulary VALUES("1365","19","2","Rejected","Rejected","A");
INSERT INTO tbvocabulary VALUES("1366","19","2","Reject_btn","Reject","A");
INSERT INTO tbvocabulary VALUES("1367","19","2","Reject_sucess","Request Rejected","A");
INSERT INTO tbvocabulary VALUES("1368","19","2","Related_abilities","Related Abilities","A");
INSERT INTO tbvocabulary VALUES("1369","19","2","reload_request","Reload request data","A");
INSERT INTO tbvocabulary VALUES("1370","19","2","Remove","Remove","A");
INSERT INTO tbvocabulary VALUES("1371","19","2","Repassed","Repassed","A");
INSERT INTO tbvocabulary VALUES("1372","19","2","Repass_btn","Repass","A");
INSERT INTO tbvocabulary VALUES("1373","19","2","Repass_request_only","Only repass requests","A");
INSERT INTO tbvocabulary VALUES("1374","19","2","Repass_request_to","Repass request to","A");
INSERT INTO tbvocabulary VALUES("1375","19","2","Reports_logo","Reports Logo","A");
INSERT INTO tbvocabulary VALUES("1376","19","2","Request","Request","A");
INSERT INTO tbvocabulary VALUES("1377","19","2","Requests","Requests","A");
INSERT INTO tbvocabulary VALUES("1378","19","2","Request_approve","There are requests waiting for approval. <br/> You can not open new requests before approving it.","A");
INSERT INTO tbvocabulary VALUES("1379","19","2","Request_approve_app","Request Approve","A");
INSERT INTO tbvocabulary VALUES("1380","19","2","Request_assumed","Request Assumed","A");
INSERT INTO tbvocabulary VALUES("1381","19","2","Request_canceled","Request Canceled","A");
INSERT INTO tbvocabulary VALUES("1382","19","2","Request_closed","Request Closed","A");
INSERT INTO tbvocabulary VALUES("1383","19","2","Request_code","Request Code","A");
INSERT INTO tbvocabulary VALUES("1384","19","2","Request_not_approve","The request was not approved","A");
INSERT INTO tbvocabulary VALUES("1385","19","2","Request_opened","Request Opened","A");
INSERT INTO tbvocabulary VALUES("1386","19","2","Request_owner","Request Owner","A");
INSERT INTO tbvocabulary VALUES("1387","19","2","Request_rejected","Request Could not be attended: ","A");
INSERT INTO tbvocabulary VALUES("1388","19","2","Request_reopened","Request Reopened ","A");
INSERT INTO tbvocabulary VALUES("1389","19","2","Request_repassed","Request Repassed ","A");
INSERT INTO tbvocabulary VALUES("1390","19","2","Request_reprove_app","Request Reprove","A");
INSERT INTO tbvocabulary VALUES("1391","19","2","Request_return_app","Return to the previous phase","A");
INSERT INTO tbvocabulary VALUES("1392","19","2","Request_waiting_approval","Waiting for user`s approval","A");
INSERT INTO tbvocabulary VALUES("1393","19","2","Requires_Autentication","Requires autentication","A");
INSERT INTO tbvocabulary VALUES("1394","19","2","Saturday","Saturday","A");
INSERT INTO tbvocabulary VALUES("1395","19","2","Save","Save","A");
INSERT INTO tbvocabulary VALUES("1396","19","2","Save_changes_sucess","Changes Saved","A");
INSERT INTO tbvocabulary VALUES("1397","19","2","Search","Search","A");
INSERT INTO tbvocabulary VALUES("1398","19","2","Second","second","A");
INSERT INTO tbvocabulary VALUES("1399","19","2","seconds","seconds","A");
INSERT INTO tbvocabulary VALUES("1400","19","2","Select","Select","A");
INSERT INTO tbvocabulary VALUES("1401","19","2","Select_acess_level","Choose Acess Level","A");
INSERT INTO tbvocabulary VALUES("1402","19","2","Select_area","Choose an area","A");
INSERT INTO tbvocabulary VALUES("1403","19","2","Select_category","Choose a category","A");
INSERT INTO tbvocabulary VALUES("1404","19","2","Select_company","Choose a company","A");
INSERT INTO tbvocabulary VALUES("1405","19","2","Select_country","Choose country","A");
INSERT INTO tbvocabulary VALUES("1406","19","2","Select_department","Chose a department","A");
INSERT INTO tbvocabulary VALUES("1407","19","2","Select_group","Selecione um grupo","A");
INSERT INTO tbvocabulary VALUES("1408","19","2","Select_group_operator","Select a group or operator.","A");
INSERT INTO tbvocabulary VALUES("1409","19","2","Select_location","Choose Location","A");
INSERT INTO tbvocabulary VALUES("1410","19","2","Select_module","Select the module","A");
INSERT INTO tbvocabulary VALUES("1411","19","2","Select_priority","Choose a priority","A");
INSERT INTO tbvocabulary VALUES("1412","19","2","Select_street","Choose Address","A");
INSERT INTO tbvocabulary VALUES("1413","19","2","Send","S E N D ","A");
INSERT INTO tbvocabulary VALUES("1414","19","2","Send_alerts_email","Yes, send this alert by email.","A");
INSERT INTO tbvocabulary VALUES("1415","19","2","Send_alerts_topic_email","Yes, send alerts by email for this topic.","A");
INSERT INTO tbvocabulary VALUES("1416","19","2","Send_email","Send email","A");
INSERT INTO tbvocabulary VALUES("1417","19","2","September","September","A");
INSERT INTO tbvocabulary VALUES("1418","19","2","Serial_number","Serial Number","A");
INSERT INTO tbvocabulary VALUES("1419","19","2","Server","Server","A");
INSERT INTO tbvocabulary VALUES("1420","19","2","Service","Service","A");
INSERT INTO tbvocabulary VALUES("1421","19","2","Service_edit","Service Edit","A");
INSERT INTO tbvocabulary VALUES("1422","19","2","Service_insert","Service Insert","A");
INSERT INTO tbvocabulary VALUES("1423","19","2","Service_name","Service\'s Name","A");
INSERT INTO tbvocabulary VALUES("1424","19","2","Service_order_number","OS Number","A");
INSERT INTO tbvocabulary VALUES("1425","19","2","Service_order_number_min","OS Number","A");
INSERT INTO tbvocabulary VALUES("1426","19","2","Settings","Settings","A");
INSERT INTO tbvocabulary VALUES("1427","19","2","Set_repass_groups","Set repass groups","A");
INSERT INTO tbvocabulary VALUES("1428","19","2","show","Show","A");
INSERT INTO tbvocabulary VALUES("1429","19","2","Show_attendants_title","Show operators by group","A");
INSERT INTO tbvocabulary VALUES("1430","19","2","Show_groups_services_title","View groups by service","A");
INSERT INTO tbvocabulary VALUES("1431","19","2","Show_in","Show in","A");
INSERT INTO tbvocabulary VALUES("1432","19","2","Smarty","Smarty\'s Variable","A");
INSERT INTO tbvocabulary VALUES("1433","19","2","smarty_version","Smarty Version:","A");
INSERT INTO tbvocabulary VALUES("1434","19","2","SMS","SMS","A");
INSERT INTO tbvocabulary VALUES("1435","19","2","Solution","Solution","A");
INSERT INTO tbvocabulary VALUES("1436","19","2","Source","Source","A");
INSERT INTO tbvocabulary VALUES("1437","19","2","Started","Started","A");
INSERT INTO tbvocabulary VALUES("1438","19","2","State","State","A");
INSERT INTO tbvocabulary VALUES("1439","19","2","status","Status","A");
INSERT INTO tbvocabulary VALUES("1440","19","2","Still_viewing","I want to keep viewing this request.","A");
INSERT INTO tbvocabulary VALUES("1441","19","2","Stop_viewing","Stop viewing this request.","A");
INSERT INTO tbvocabulary VALUES("1442","19","2","Subject","Subject","A");
INSERT INTO tbvocabulary VALUES("1443","19","2","Success_logs","Success logs","A");
INSERT INTO tbvocabulary VALUES("1444","19","2","Sunday","Sunday","A");
INSERT INTO tbvocabulary VALUES("1445","19","2","sys_2FAuthentication","Usar autenticação em duas etapas","A");
INSERT INTO tbvocabulary VALUES("1446","19","2","sys_session_time_lbl","Time System Session Duration","A");
INSERT INTO tbvocabulary VALUES("1447","19","2","sys_time_session","Time that will last the session system. Value in seconds. If not set will be 10 minutes.","A");
INSERT INTO tbvocabulary VALUES("1448","19","2","Table_prefix","Table prefix","A");
INSERT INTO tbvocabulary VALUES("1449","19","2","Tag_min","Tag","A");
INSERT INTO tbvocabulary VALUES("1450","19","2","Tckt_cancel_request","Confirm  cancel request ?","A");
INSERT INTO tbvocabulary VALUES("1451","19","2","Tckt_delete_note","Confirm  delete request note?","A");
INSERT INTO tbvocabulary VALUES("1452","19","2","Tckt_del_note_failure","Could not delete the note !","A");
INSERT INTO tbvocabulary VALUES("1453","19","2","Tckt_drop_file","Drop files to upload (or click here)!","A");
INSERT INTO tbvocabulary VALUES("1454","19","2","Tckt_evaluated_success","Request evaluated successfully !","A");
INSERT INTO tbvocabulary VALUES("1455","19","2","Tckt_finish_request","Close","A");
INSERT INTO tbvocabulary VALUES("1456","19","2","Tckt_incharge","In charge","A");
INSERT INTO tbvocabulary VALUES("1457","19","2","Tckt_opened","Ticket opened","A");
INSERT INTO tbvocabulary VALUES("1458","19","2","Tckt_reopen_request","Confirm  reopen request ?","A");
INSERT INTO tbvocabulary VALUES("1459","19","2","Tckt_Request","Ticket;","A");
INSERT INTO tbvocabulary VALUES("1460","19","2","Tck_new_ticket","New Ticket","A");
INSERT INTO tbvocabulary VALUES("1461","19","2","Tck_Open","Open Tickets ","A");
INSERT INTO tbvocabulary VALUES("1462","19","2","Tck_title","Tickets","A");
INSERT INTO tbvocabulary VALUES("1463","19","2","Template_edit","Edit Template","A");
INSERT INTO tbvocabulary VALUES("1464","19","2","Thursday","Thursday","A");
INSERT INTO tbvocabulary VALUES("1465","19","2","timeouttext","Your session will expire in","A");
INSERT INTO tbvocabulary VALUES("1466","19","2","Time_expended","Time Expended in task","A");
INSERT INTO tbvocabulary VALUES("1467","19","2","Time_return","Time return link (callback)","A");
INSERT INTO tbvocabulary VALUES("1468","19","2","Time_value","Time Value","A");
INSERT INTO tbvocabulary VALUES("1469","19","2","Title","Title","A");
INSERT INTO tbvocabulary VALUES("1470","19","2","tlt_span_group","Requests from my group","A");
INSERT INTO tbvocabulary VALUES("1471","19","2","tlt_span_my","My Requests","A");
INSERT INTO tbvocabulary VALUES("1472","19","2","tlt_span_track_group","Track by group","A");
INSERT INTO tbvocabulary VALUES("1473","19","2","tlt_span_track_me","Track by me","A");
INSERT INTO tbvocabulary VALUES("1474","19","2","to","to","A");
INSERT INTO tbvocabulary VALUES("1475","19","2","tooltip_city","Add new city","A");
INSERT INTO tbvocabulary VALUES("1476","19","2","tooltip_delete_area","Delete Area","A");
INSERT INTO tbvocabulary VALUES("1477","19","2","tooltip_delete_item","Delete Item","A");
INSERT INTO tbvocabulary VALUES("1478","19","2","tooltip_delete_service","Delete Service","A");
INSERT INTO tbvocabulary VALUES("1479","19","2","tooltip_delete_type","Delete Type","A");
INSERT INTO tbvocabulary VALUES("1480","19","2","tooltip_list_items","List Items","A");
INSERT INTO tbvocabulary VALUES("1481","19","2","tooltip_list_services","List Services","A");
INSERT INTO tbvocabulary VALUES("1482","19","2","tooltip_neighborhood","Add new neighborhood","A");
INSERT INTO tbvocabulary VALUES("1483","19","2","tooltip_state","Add new state","A");
INSERT INTO tbvocabulary VALUES("1484","19","2","tooltip_street","Add new address","A");
INSERT INTO tbvocabulary VALUES("1485","19","2","Topic","Topic","A");
INSERT INTO tbvocabulary VALUES("1486","19","2","Topic_edit","Topic Edit","A");
INSERT INTO tbvocabulary VALUES("1487","19","2","Total","Total","A");
INSERT INTO tbvocabulary VALUES("1488","19","2","Total_holidays","Total Holidays","A");
INSERT INTO tbvocabulary VALUES("1489","19","2","Total_minutes","Total minutes","A");
INSERT INTO tbvocabulary VALUES("1490","19","2","tracker_status","Track Emails","A");
INSERT INTO tbvocabulary VALUES("1491","19","2","trello","Trello API Configuration","A");
INSERT INTO tbvocabulary VALUES("1492","19","2","trello_boards","Boards","A");
INSERT INTO tbvocabulary VALUES("1493","19","2","trello_cards","Cards","A");
INSERT INTO tbvocabulary VALUES("1494","19","2","trello_description","Card Description","A");
INSERT INTO tbvocabulary VALUES("1495","19","2","trello_integration","Trello Integration","A");
INSERT INTO tbvocabulary VALUES("1496","19","2","trello_key","Key","A");
INSERT INTO tbvocabulary VALUES("1497","19","2","trello_lists","Lists","A");
INSERT INTO tbvocabulary VALUES("1498","19","2","trello_title","Card Title","A");
INSERT INTO tbvocabulary VALUES("1499","19","2","trello_token","Token","A");
INSERT INTO tbvocabulary VALUES("1500","19","2","trello_tooltip_card","Add new card","A");
INSERT INTO tbvocabulary VALUES("1501","19","2","Tuesday","Tuesday","A");
INSERT INTO tbvocabulary VALUES("1502","19","2","type","Type","A");
INSERT INTO tbvocabulary VALUES("1503","19","2","Type_adress","Type adress","A");
INSERT INTO tbvocabulary VALUES("1504","19","2","Type_edit","Type Edit","A");
INSERT INTO tbvocabulary VALUES("1505","19","2","Type_insert","Insert Type","A");
INSERT INTO tbvocabulary VALUES("1506","19","2","Type_name","Type\'s name","A");
INSERT INTO tbvocabulary VALUES("1507","19","2","type_user_operator","Operator","A");
INSERT INTO tbvocabulary VALUES("1508","19","2","type_user_user","User","A");
INSERT INTO tbvocabulary VALUES("1509","19","2","until","until","A");
INSERT INTO tbvocabulary VALUES("1510","19","2","Until_closed","Until closed","A");
INSERT INTO tbvocabulary VALUES("1511","19","2","Update","Update","A");
INSERT INTO tbvocabulary VALUES("1512","19","2","User","User","A");
INSERT INTO tbvocabulary VALUES("1513","19","2","UserData","User data","A");
INSERT INTO tbvocabulary VALUES("1514","19","2","user_external_settings","External Settings","A");
INSERT INTO tbvocabulary VALUES("1515","19","2","User_login","User login","A");
INSERT INTO tbvocabulary VALUES("1516","19","2","user_profile","Profile","A");
INSERT INTO tbvocabulary VALUES("1517","19","2","Valid","Valid","A");
INSERT INTO tbvocabulary VALUES("1518","19","2","Validity_Standard","Validity Standard","A");
INSERT INTO tbvocabulary VALUES("1519","19","2","Valid_until","Valid until","A");
INSERT INTO tbvocabulary VALUES("1520","19","2","Value_exists","Entered value is already registered","A");
INSERT INTO tbvocabulary VALUES("1521","19","2","Var_assume","Assume Date","A");
INSERT INTO tbvocabulary VALUES("1522","19","2","Var_branch","User\'s branch phone number","A");
INSERT INTO tbvocabulary VALUES("1523","19","2","Var_date","Date when the email is sent","A");
INSERT INTO tbvocabulary VALUES("1524","19","2","Var_description","Request Description","A");
INSERT INTO tbvocabulary VALUES("1525","19","2","Var_evaluation","Evaluation given by the user","A");
INSERT INTO tbvocabulary VALUES("1526","19","2","Var_expire","Expire Date","A");
INSERT INTO tbvocabulary VALUES("1527","19","2","Var_finish","Finish Date","A");
INSERT INTO tbvocabulary VALUES("1528","19","2","Var_incharge","In charge","A");
INSERT INTO tbvocabulary VALUES("1529","19","2","Var_link_evaluation","Link to evaluate without login.","A");
INSERT INTO tbvocabulary VALUES("1530","19","2","Var_link_operator","Link to the request page (operator)","A");
INSERT INTO tbvocabulary VALUES("1531","19","2","Var_link_user","Link to the request page (user)","A");
INSERT INTO tbvocabulary VALUES("1532","19","2","Var_nt_operator","Notes that only operators can see","A");
INSERT INTO tbvocabulary VALUES("1533","19","2","Var_nt_user","Notes that users can see","A");
INSERT INTO tbvocabulary VALUES("1534","19","2","Var_phone","User\'s Phone","A");
INSERT INTO tbvocabulary VALUES("1535","19","2","Var_record","Entry Date","A");
INSERT INTO tbvocabulary VALUES("1536","19","2","Var_rejection","Rejection Date","A");
INSERT INTO tbvocabulary VALUES("1537","19","2","Var_request","Request Code","A");
INSERT INTO tbvocabulary VALUES("1538","19","2","Var_requester","Requester Name","A");
INSERT INTO tbvocabulary VALUES("1539","19","2","Var_status","Request Status","A");
INSERT INTO tbvocabulary VALUES("1540","19","2","Var_subject","Request Subject","A");
INSERT INTO tbvocabulary VALUES("1541","19","2","Var_user","Logged User","A");
INSERT INTO tbvocabulary VALUES("1542","19","2","Version","Version","A");
INSERT INTO tbvocabulary VALUES("1543","19","2","View_groups","View groups","A");
INSERT INTO tbvocabulary VALUES("1544","19","2","VIP_user","VIP user","A");
INSERT INTO tbvocabulary VALUES("1545","19","2","Visible","Visible","A");
INSERT INTO tbvocabulary VALUES("1546","19","2","vocabulary_key_exists","This key already exists","A");
INSERT INTO tbvocabulary VALUES("1547","19","2","vocabulary_key_name","Key Name","A");
INSERT INTO tbvocabulary VALUES("1548","19","2","vocabulary_key_value","Key Value","A");
INSERT INTO tbvocabulary VALUES("1549","19","2","vocabulary_locale","Locale","A");
INSERT INTO tbvocabulary VALUES("1550","19","2","Waiting_for_approval","Waiting for approval","A");
INSERT INTO tbvocabulary VALUES("1551","19","2","Warning_new_topic","New Topic","A");
INSERT INTO tbvocabulary VALUES("1552","19","2","Wednesday","Wednesday","A");
INSERT INTO tbvocabulary VALUES("1553","19","2","Widget","Widget","A");
INSERT INTO tbvocabulary VALUES("1554","19","2","Yes","Yes ","A");
INSERT INTO tbvocabulary VALUES("1555","19","2","Zipcode","Zipcode","A");
INSERT INTO tbvocabulary VALUES("1556","559","2","pgr_people","?nsanlar ve ?irketler","A");
INSERT INTO tbvocabulary VALUES("1557","559","2","pgr_holidays","Bayram","A");
INSERT INTO tbvocabulary VALUES("1558","559","2","pgr_programs","Programlar","A");
INSERT INTO tbvocabulary VALUES("1559","559","2","pgr_modules","Modüller","A");
INSERT INTO tbvocabulary VALUES("1560","559","2","pgr_type_permission","Ki?i ?zni yaz?n","A");
INSERT INTO tbvocabulary VALUES("1561","559","2","pgr_downloads","?ndirme","A");
INSERT INTO tbvocabulary VALUES("1562","559","2","pgr_logos","logolar","A");
INSERT INTO tbvocabulary VALUES("1563","559","2","pgr_status","durum","A");
INSERT INTO tbvocabulary VALUES("1564","559","2","pgr_priority","öncelik","A");
INSERT INTO tbvocabulary VALUES("1565","559","2","pgr_groups","Gruplar","A");
INSERT INTO tbvocabulary VALUES("1566","559","2","pgr_evaluation","De?erlendirme","A");
INSERT INTO tbvocabulary VALUES("1567","559","2","pgr_cost_center","ödeme merkezi","A");
INSERT INTO tbvocabulary VALUES("1568","559","2","pgr_services","Hizmetler","A");
INSERT INTO tbvocabulary VALUES("1569","559","2","pgr_req_reason","Talep Nedeni","A");
INSERT INTO tbvocabulary VALUES("1570","559","2","pgr_email_config","E-posta Yap?land?rma","A");
INSERT INTO tbvocabulary VALUES("1571","559","2","pgr_email_request","E-posta ile ?stekler","A");
INSERT INTO tbvocabulary VALUES("1572","559","2","pgr_sys_features","sistem özellikleri","A");
INSERT INTO tbvocabulary VALUES("1573","559","2","pgr_person_report","ki?i Raporu","A");
INSERT INTO tbvocabulary VALUES("1574","559","2","pgr_req_reports","istekler Raporu","A");
INSERT INTO tbvocabulary VALUES("1575","559","2","pgr_ope_aver_resptime","Operatör Ortalama Tepki Süresi","A");
INSERT INTO tbvocabulary VALUES("1576","559","2","pgr_request_department","bölümü taraf?ndan Talebi","A");
INSERT INTO tbvocabulary VALUES("1577","559","2","pgr_rejects_request","?stekleri reddet","A");
INSERT INTO tbvocabulary VALUES("1578","559","2","pgr_request_status","Duruma göre ?stekler","A");
INSERT INTO tbvocabulary VALUES("1579","559","2","pgr_request_operator","??letmeci taraf?ndan ?stekler","A");
INSERT INTO tbvocabulary VALUES("1580","559","2","pgr_summarized_operator","??letmeci taraf?ndan özetlenen","A");
INSERT INTO tbvocabulary VALUES("1581","559","2","pgr_summarized_department","bölümü taraf?ndan özetlenen","A");
INSERT INTO tbvocabulary VALUES("1582","559","2","pgr_user_satisfaction","kullan?c? Memnuniyeti","A");
INSERT INTO tbvocabulary VALUES("1583","559","2","pgr_dash_widgets","widget\'lar","A");
INSERT INTO tbvocabulary VALUES("1584","559","2","pgr_warnings","Uyar?lar","A");
INSERT INTO tbvocabulary VALUES("1585","559","2","pgr_import_people","Kullan?c?lar ?thalat","A");
INSERT INTO tbvocabulary VALUES("1586","559","2","pgr_work_calendar","çal??ma Takvimi","A");
INSERT INTO tbvocabulary VALUES("1587","559","2","pgr_import_services","?thalat Hizmetleri Katalog","A");
INSERT INTO tbvocabulary VALUES("1588","559","2","Abbreviation","K?saltma","A");
INSERT INTO tbvocabulary VALUES("1589","559","2","Abilities","Yetenekleri","A");
INSERT INTO tbvocabulary VALUES("1590","559","2","Access","Eri?im","A");
INSERT INTO tbvocabulary VALUES("1591","559","2","Access_denied","Eri?im reddedildi.","A");
INSERT INTO tbvocabulary VALUES("1592","559","2","Acess_level","eri?im Seviyesi","A");
INSERT INTO tbvocabulary VALUES("1593","559","2","Activate","etkinle?tirmek","A");
INSERT INTO tbvocabulary VALUES("1594","559","2","Add","Eklemek","A");
INSERT INTO tbvocabulary VALUES("1595","559","2","Add_category","Kategori ekle","A");
INSERT INTO tbvocabulary VALUES("1596","559","2","Add_item","ö?e eklemek","A");
INSERT INTO tbvocabulary VALUES("1597","559","2","Add_service","Hizmet ekle","A");
INSERT INTO tbvocabulary VALUES("1598","559","2","Add_widget","Widget ekleyin","A");
INSERT INTO tbvocabulary VALUES("1599","559","2","Added_notes","Eklenen Notlar","A");
INSERT INTO tbvocabulary VALUES("1600","559","2","Adress","Adres","A");
INSERT INTO tbvocabulary VALUES("1601","559","2","Alert_activated","Ba?ar?yla çal??t?r?ld?.","A");
INSERT INTO tbvocabulary VALUES("1602","559","2","Alert_activated_error","Ba?ar?s?zl?k etkinle?tirmek için.","A");
INSERT INTO tbvocabulary VALUES("1603","559","2","Alert_approve","Sen, onay için bekleyen iste?iniz var ?imdi onlar? onaylamak istiyor musunuz?","A");
INSERT INTO tbvocabulary VALUES("1604","559","2","Alert_choose_area","bir alan seçiniz","A");
INSERT INTO tbvocabulary VALUES("1605","559","2","Alert_choose_item","Bir ö?eyi seçiniz","A");
INSERT INTO tbvocabulary VALUES("1606","559","2","Alert_choose_service","bir hizmet seçiniz","A");
INSERT INTO tbvocabulary VALUES("1607","559","2","Alert_choose_type","Bir türü seçiniz","A");
INSERT INTO tbvocabulary VALUES("1608","559","2","Alert_deactivated","Ba?ar?yla devre d???.","A");
INSERT INTO tbvocabulary VALUES("1609","559","2","Alert_deactivated_error","Ba?ar?s?zl?k desactivate için.","A");
INSERT INTO tbvocabulary VALUES("1610","559","2","Alert_deleted","Ba?ar?yla silinmi?.","A");
INSERT INTO tbvocabulary VALUES("1611","559","2","Alert_deleted_error","silmek için ba?ar?s?zl?k.","A");
INSERT INTO tbvocabulary VALUES("1612","559","2","Alert_different_passwords","?ifreler e?le?medi","A");
INSERT INTO tbvocabulary VALUES("1613","559","2","Alert_empty_note","önotas?n?n vücudunu doldurunuz","A");
INSERT INTO tbvocabulary VALUES("1614","559","2","Alert_empty_subject","iste?in konuyu doldurunuz","A");
INSERT INTO tbvocabulary VALUES("1615","559","2","Alert_failure","eklemek olamazd?","A");
INSERT INTO tbvocabulary VALUES("1616","559","2","Alert_change_password","Parola ba?ar?yla de?i?tirildi","A");
INSERT INTO tbvocabulary VALUES("1617","559","2","Alert_inserted","ba?ar?yla Eklenen","A");
INSERT INTO tbvocabulary VALUES("1618","559","2","Alert_select_one","1 ö?e seçiniz","A");
INSERT INTO tbvocabulary VALUES("1619","559","2","Alert_sucess_module","Modül ba?ar?yla tak?l?.","A");
INSERT INTO tbvocabulary VALUES("1620","559","2","Alert_sucess_repass","Ba?ar?yla repassed!","A");
INSERT INTO tbvocabulary VALUES("1621","559","2","Alert_follow_repass","takip seçeneklerden birini seçin.","A");
INSERT INTO tbvocabulary VALUES("1622","559","2","Alert_wrong_extension_csv","Geçersiz dosya uzant?s?. uzatma CSV ile dosyalar? izin verilir","A");
INSERT INTO tbvocabulary VALUES("1623","559","2","all","Her?ey","A");
INSERT INTO tbvocabulary VALUES("1624","559","2","Approve_text","E?er operatörler kat?l?m? onayl?yor musunuz?","A");
INSERT INTO tbvocabulary VALUES("1625","559","2","Approve_no","Yok hay?r.","A");
INSERT INTO tbvocabulary VALUES("1626","559","2","Approve_obs","Evet, gözlem.","A");
INSERT INTO tbvocabulary VALUES("1627","559","2","Approve_yes","Evet.","A");
INSERT INTO tbvocabulary VALUES("1628","559","2","and","ve","A");
INSERT INTO tbvocabulary VALUES("1629","559","2","Area","alan","A");
INSERT INTO tbvocabulary VALUES("1630","559","2","Area_insert","Ekle Alan?","A");
INSERT INTO tbvocabulary VALUES("1631","559","2","Area_edit","Düzenleme Alan?","A");
INSERT INTO tbvocabulary VALUES("1632","559","2","Area_name","Yrenin ad?","A");
INSERT INTO tbvocabulary VALUES("1633","559","2","Assume_request","?ste?i varsayal?m","A");
INSERT INTO tbvocabulary VALUES("1634","559","2","Assumed_successfully","Ba?ar?yla varsayarsak!","A");
INSERT INTO tbvocabulary VALUES("1635","559","2","Att_way","Seyirci Yolu","A");
INSERT INTO tbvocabulary VALUES("1636","559","2","Att_way_min","Al?yorlar. yol","A");
INSERT INTO tbvocabulary VALUES("1637","559","2","Att_way_new","Yeni al?yorlar. yol","A");
INSERT INTO tbvocabulary VALUES("1638","559","2","Attach","ili?tirmek","A");
INSERT INTO tbvocabulary VALUES("1639","559","2","Attachments","Ekler","A");
INSERT INTO tbvocabulary VALUES("1640","559","2","Attend_level","Seyirci seviyesi","A");
INSERT INTO tbvocabulary VALUES("1641","559","2","Attend_time","Seyirci Zaman","A");
INSERT INTO tbvocabulary VALUES("1642","559","2","Attendance","kat?l?m","A");
INSERT INTO tbvocabulary VALUES("1643","559","2","Attendance_time","Seyirci zaman","A");
INSERT INTO tbvocabulary VALUES("1644","559","2","Attendants_by_group","grup taraf?ndan görünüm operatörleri","A");
INSERT INTO tbvocabulary VALUES("1645","559","2","Available","Mevcut","A");
INSERT INTO tbvocabulary VALUES("1646","559","2","Available_for","?çin uygun","A");
INSERT INTO tbvocabulary VALUES("1647","559","2","Available_text","?stek üzerine kay?t için kullan?labilir.","A");
INSERT INTO tbvocabulary VALUES("1648","559","2","Back_btn","Arka","A");
INSERT INTO tbvocabulary VALUES("1649","559","2","Birth_date","Do?um günü","A");
INSERT INTO tbvocabulary VALUES("1650","559","2","Branch","?ube","A");
INSERT INTO tbvocabulary VALUES("1651","559","2","Bar_show","Show Bar","A");
INSERT INTO tbvocabulary VALUES("1652","559","2","Bar_hide","gizle Bar","A");
INSERT INTO tbvocabulary VALUES("1653","559","2","By_group","grubu ile","A");
INSERT INTO tbvocabulary VALUES("1654","559","2","By_company","?irket taraf?ndan","A");
INSERT INTO tbvocabulary VALUES("1655","559","2","Cancel_btn","?ptal etmek","A");
INSERT INTO tbvocabulary VALUES("1656","559","2","Category","Kategori","A");
INSERT INTO tbvocabulary VALUES("1657","559","2","Category_insert","Kategori Ekle","A");
INSERT INTO tbvocabulary VALUES("1658","559","2","Change","De?i?iklik","A");
INSERT INTO tbvocabulary VALUES("1659","559","2","Change_date","Tarihi de?i?tir","A");
INSERT INTO tbvocabulary VALUES("1660","559","2","Change_password","?ifre de?i?tir","A");
INSERT INTO tbvocabulary VALUES("1661","559","2","Change_password_required","?ifresini de?i?tirmek için kullan?c? zorlamak.","A");
INSERT INTO tbvocabulary VALUES("1662","559","2","Change_permissions","?zinleri de?i?tir","A");
INSERT INTO tbvocabulary VALUES("1663","559","2","City","?ehir","A");
INSERT INTO tbvocabulary VALUES("1664","559","2","Classification","s?n?fland?rma","A");
INSERT INTO tbvocabulary VALUES("1665","559","2","Classification_text","\'S?n?fland?rma Free\' olarak bu ö?eyi ayarlay?n.","A");
INSERT INTO tbvocabulary VALUES("1666","559","2","Client","kostümcü","A");
INSERT INTO tbvocabulary VALUES("1667","559","2","Closed","Kapal?","A");
INSERT INTO tbvocabulary VALUES("1668","559","2","Code","kod","A");
INSERT INTO tbvocabulary VALUES("1669","559","2","Color","Renk","A");
INSERT INTO tbvocabulary VALUES("1670","559","2","Company","?irket","A");
INSERT INTO tbvocabulary VALUES("1671","559","2","Complement","tamamlay?c?","A");
INSERT INTO tbvocabulary VALUES("1672","559","2","Confirm_close","Kapat istek?","A");
INSERT INTO tbvocabulary VALUES("1673","559","2","Confirm_password","?ifreyi Onayla","A");
INSERT INTO tbvocabulary VALUES("1674","559","2","Contact_person","?lgili ki?i","A");
INSERT INTO tbvocabulary VALUES("1675","559","2","Controller","kontrolr","A");
INSERT INTO tbvocabulary VALUES("1676","559","2","conf_approvals","Yap?land?rma Onaylar","A");
INSERT INTO tbvocabulary VALUES("1677","559","2","Country","ülke","A");
INSERT INTO tbvocabulary VALUES("1678","559","2","cpf","SSN","A");
INSERT INTO tbvocabulary VALUES("1679","559","2","Create_user","Kullan?c? olu?tur","A");
INSERT INTO tbvocabulary VALUES("1680","559","2","Create_user_msg","Bu Helpdezk bulunmayan e?er yeni bir kullan?c? olu?turun.","A");
INSERT INTO tbvocabulary VALUES("1681","559","2","Current_date","Geçerli tarih","A");
INSERT INTO tbvocabulary VALUES("1682","559","2","Current_time","?imdiki zaman","A");
INSERT INTO tbvocabulary VALUES("1683","559","2","Choose_format","Raporunuzu vermek istedi?iniz biçimi seçin.","A");
INSERT INTO tbvocabulary VALUES("1684","559","2","Close","Kapat","A");
INSERT INTO tbvocabulary VALUES("1685","559","2","Dashboard","gösterge paneli","A");
INSERT INTO tbvocabulary VALUES("1686","559","2","Date","tarih","A");
INSERT INTO tbvocabulary VALUES("1687","559","2","Days","günler","A");
INSERT INTO tbvocabulary VALUES("1688","559","2","Deactivate","Devre d??? b?rakmak","A");
INSERT INTO tbvocabulary VALUES("1689","559","2","Default","Varsay?lan","A");
INSERT INTO tbvocabulary VALUES("1690","559","2","Default_department","varsay?lan bölümü","A");
INSERT INTO tbvocabulary VALUES("1691","559","2","Default_department_msg","E?er bölümün ekleme program?nda daha fazla bölümler ekleyebilir","A");
INSERT INTO tbvocabulary VALUES("1692","559","2","Default_text","bir istek aç?ld???nda varsay?lan olarak bu önceli?i kullan?n.","A");
INSERT INTO tbvocabulary VALUES("1693","559","2","Delete","silmek","A");
INSERT INTO tbvocabulary VALUES("1694","559","2","Delete_emails","e-postalar? silin","A");
INSERT INTO tbvocabulary VALUES("1695","559","2","Delete_emails_msg","indirirken sunucudan e-postalar? silmek.","A");
INSERT INTO tbvocabulary VALUES("1696","559","2","Delimiter","S?n?rlay?c?","A");
INSERT INTO tbvocabulary VALUES("1697","559","2","Department","Departament","A");
INSERT INTO tbvocabulary VALUES("1698","559","2","Department_exists","bölüm önceden kaydedilmi?!","A");
INSERT INTO tbvocabulary VALUES("1699","559","2","Department_name","Departament ad?","A");
INSERT INTO tbvocabulary VALUES("1700","559","2","Departments","Departaments","A");
INSERT INTO tbvocabulary VALUES("1701","559","2","Description","Aç?klama","A");
INSERT INTO tbvocabulary VALUES("1702","559","2","Domain","domain","A");
INSERT INTO tbvocabulary VALUES("1703","559","2","Downloads","?ndirme","A");
INSERT INTO tbvocabulary VALUES("1704","559","2","Drag_widget","Burada widget sürükleyin","A");
INSERT INTO tbvocabulary VALUES("1705","559","2","Delete_widget","Bu widget\'? silmek istedi?inizden emin misiniz?","A");
INSERT INTO tbvocabulary VALUES("1706","559","2","EIN_CNPJ","EIN","A");
INSERT INTO tbvocabulary VALUES("1707","559","2","edit","Düzenleme","A");
INSERT INTO tbvocabulary VALUES("1708","559","2","Edit_btn","Düzenleme","A");
INSERT INTO tbvocabulary VALUES("1709","559","2","Edit_sucess","Ba?ar?yla düzenlenmi?!","A");
INSERT INTO tbvocabulary VALUES("1710","559","2","Edit_failure","Edit olamaz!","A");
INSERT INTO tbvocabulary VALUES("1711","559","2","Edit_layout","Düzenleme Düzeni","A");
INSERT INTO tbvocabulary VALUES("1712","559","2","email","E-posta","A");
INSERT INTO tbvocabulary VALUES("1713","559","2","Email_host","e-posta sahibi","A");
INSERT INTO tbvocabulary VALUES("1714","559","2","Email_config","E-posta Yap?land?rma","A");
INSERT INTO tbvocabulary VALUES("1715","559","2","Email_sender","E-posta gönderen","A");
INSERT INTO tbvocabulary VALUES("1716","559","2","Empty","hiçbir Ürünleri","A");
INSERT INTO tbvocabulary VALUES("1717","559","2","Equipment","ekipman","A");
INSERT INTO tbvocabulary VALUES("1718","559","2","Error","Hata!","A");
INSERT INTO tbvocabulary VALUES("1719","559","2","Expire_date","Son kullanma tarihi","A");
INSERT INTO tbvocabulary VALUES("1720","559","2","Expire_date_sucess","Yürürlük süresi ba?ar?yla de?i?tirildi!","A");
INSERT INTO tbvocabulary VALUES("1721","559","2","Execution_date","?nfaz tarihi","A");
INSERT INTO tbvocabulary VALUES("1722","559","2","Export","?hracat","A");
INSERT INTO tbvocabulary VALUES("1723","559","2","Extra","Ekstra","A");
INSERT INTO tbvocabulary VALUES("1724","559","2","Female","Kad?n","A");
INSERT INTO tbvocabulary VALUES("1725","559","2","Failure_logs","ba?ar?s?zl?k günlükleri","A");
INSERT INTO tbvocabulary VALUES("1726","559","2","File","Dosya","A");
INSERT INTO tbvocabulary VALUES("1727","559","2","File_PDF","PDF dosyas?.","A");
INSERT INTO tbvocabulary VALUES("1728","559","2","File_XLS","XLS dile.","A");
INSERT INTO tbvocabulary VALUES("1729","559","2","File_CSV","CSV dosyas?.","A");
INSERT INTO tbvocabulary VALUES("1730","559","2","Fill_adress","Adres doldurun?","A");
INSERT INTO tbvocabulary VALUES("1731","559","2","Filter_by_sender","Gönderene göre filtreleme","A");
INSERT INTO tbvocabulary VALUES("1732","559","2","Filter_by_subject","Konu Filtre","A");
INSERT INTO tbvocabulary VALUES("1733","559","2","Finish_btn","Biti?","A");
INSERT INTO tbvocabulary VALUES("1734","559","2","Finished_alt","bitirdi","A");
INSERT INTO tbvocabulary VALUES("1735","559","2","Footer","Altbilgi","A");
INSERT INTO tbvocabulary VALUES("1736","559","2","From","itibaren","A");
INSERT INTO tbvocabulary VALUES("1737","559","2","Generated","olu?turulan","A");
INSERT INTO tbvocabulary VALUES("1738","559","2","Gender","Cinsiyet","A");
INSERT INTO tbvocabulary VALUES("1739","559","2","Group","grup","A");
INSERT INTO tbvocabulary VALUES("1740","559","2","Group_name","Grubun ad?","A");
INSERT INTO tbvocabulary VALUES("1741","559","2","Group_operators","Grup Operatörleri","A");
INSERT INTO tbvocabulary VALUES("1742","559","2","Group_still_viewing","Benim grup bu iste?i inceleyen tutmak istiyorum","A");
INSERT INTO tbvocabulary VALUES("1743","559","2","Groups","Gruplar","A");
INSERT INTO tbvocabulary VALUES("1744","559","2","Header","üstbilgi","A");
INSERT INTO tbvocabulary VALUES("1745","559","2","Holiday","Tatil","A");
INSERT INTO tbvocabulary VALUES("1746","559","2","Holidays","Bayram","A");
INSERT INTO tbvocabulary VALUES("1747","559","2","Holiday_des","tatil Aç?klamas?","A");
INSERT INTO tbvocabulary VALUES("1748","559","2","Holiday_import","Bayram?n ?thalat","A");
INSERT INTO tbvocabulary VALUES("1749","559","2","Home","Ev","A");
INSERT INTO tbvocabulary VALUES("1750","559","2","Hours","Saatler","A");
INSERT INTO tbvocabulary VALUES("1751","559","2","Hour","Saat","A");
INSERT INTO tbvocabulary VALUES("1752","559","2","Info_header_logo","Header logo yüksekli?i 35pixels olmal?d?r, farkl? oranlarda tüm görüntüleri yeniden boyutland?r?l?r.","A");
INSERT INTO tbvocabulary VALUES("1753","559","2","Info_login_logo","yüksekli?i 70pixels olmal?d?r Oturum açma sayfas? logosu, farkl? oranlarda tüm görüntüleri yeniden boyutland?r?l?r.","A");
INSERT INTO tbvocabulary VALUES("1754","559","2","Info_reports_logo","logo yüksekli?i 40pixels olmal?d?r raporlar?n?n, farkl? oranlarda tüm görüntüleri yeniden boyutland?r?l?r.","A");
INSERT INTO tbvocabulary VALUES("1755","559","2","Import","?thalat","A");
INSERT INTO tbvocabulary VALUES("1756","559","2","Import_to","?thalat","A");
INSERT INTO tbvocabulary VALUES("1757","559","2","Import_successfull","ba?ar?yla ?thal","A");
INSERT INTO tbvocabulary VALUES("1758","559","2","Import_failure","?thalat olamazd?","A");
INSERT INTO tbvocabulary VALUES("1759","559","2","Insert_note","Ekle Not","A");
INSERT INTO tbvocabulary VALUES("1760","559","2","Integration_ldap","LDAP / AD Entegrasyonu","A");
INSERT INTO tbvocabulary VALUES("1761","559","2","Item_insert","ö?e Ekle","A");
INSERT INTO tbvocabulary VALUES("1762","559","2","Item_edit","ö?e Düzenleme","A");
INSERT INTO tbvocabulary VALUES("1763","559","2","Item_name","ö?enin ad?","A");
INSERT INTO tbvocabulary VALUES("1764","559","2","itens","ürün","A");
INSERT INTO tbvocabulary VALUES("1765","559","2","juridical","tüzel","A");
INSERT INTO tbvocabulary VALUES("1766","559","2","ldap_server","sunucu","A");
INSERT INTO tbvocabulary VALUES("1767","559","2","ldap_dn","Ay?rt edici ad?","A");
INSERT INTO tbvocabulary VALUES("1768","559","2","ldap_domain","domain","A");
INSERT INTO tbvocabulary VALUES("1769","559","2","ldap_field","önesne AD / LDAP","A");
INSERT INTO tbvocabulary VALUES("1770","559","2","ldap_field_obs","Kullan?c? depoland??? alan?.","A");
INSERT INTO tbvocabulary VALUES("1771","559","2","List_comp_groups","?irketler ve onlar?n gruplar?n?n listesi:","A");
INSERT INTO tbvocabulary VALUES("1772","559","2","Loading","Yükleniyor...","A");
INSERT INTO tbvocabulary VALUES("1773","559","2","Login","Oturum aç","A");
INSERT INTO tbvocabulary VALUES("1774","559","2","Login_exists","Oturum önceden kaydedilmi?!","A");
INSERT INTO tbvocabulary VALUES("1775","559","2","Login_page_logo","Giri? Sayfas? Logosu","A");
INSERT INTO tbvocabulary VALUES("1776","559","2","Login_type","Giri? Türü","A");
INSERT INTO tbvocabulary VALUES("1777","559","2","Login_layout","Düzen Giri?i","A");
INSERT INTO tbvocabulary VALUES("1778","559","2","Login_user_not_exist","Kullan?c? yoktur, yazarak kontrol edin!","A");
INSERT INTO tbvocabulary VALUES("1779","559","2","Login_error_error","Yanl?? ?ifre, lütfen tekrar deneyin.","A");
INSERT INTO tbvocabulary VALUES("1780","559","2","Login_user_inactive","Inaktif kullan?c?, bir yönetici ile konu?mak lütfen.","A");
INSERT INTO tbvocabulary VALUES("1781","559","2","Location","yer","A");
INSERT INTO tbvocabulary VALUES("1782","559","2","Location_insert","Yer Ekle","A");
INSERT INTO tbvocabulary VALUES("1783","559","2","Lost_password","?ifremi Unuttum","A");
INSERT INTO tbvocabulary VALUES("1784","559","2","Lost_password_suc","Yeni ?ifre ba?ar? ile gönderin.","A");
INSERT INTO tbvocabulary VALUES("1785","559","2","Lost_password_err","Yeni ?ifreyi gönderilemedi.","A");
INSERT INTO tbvocabulary VALUES("1786","559","2","Lost_password_ad","Bu kurtarma Active Directory ?ifre mümkün de?ildir, Sistem Yöneticisine ba?vurun.","A");
INSERT INTO tbvocabulary VALUES("1787","559","2","Lost_password_pop","Bu sistem yönetici ile ileti?im, kurtarma POP modunda ?ifre mümkün de?ildir.","A");
INSERT INTO tbvocabulary VALUES("1788","559","2","Lost_password_master","Kullan?c? ana ?ifre de?i?tirilemez","A");
INSERT INTO tbvocabulary VALUES("1789","559","2","Lost_password_not","Kullan?c? yazarak kontrol edin, yoktur","A");
INSERT INTO tbvocabulary VALUES("1790","559","2","Lost_password_subject","?ifre hat?rlat?c?","A");
INSERT INTO tbvocabulary VALUES("1791","559","2","Lost_password_body","<br> <p> Size yeni ?ifrenizi oldu?unu bildirmek. $ <b> geçmesi </ b> <br> Bu otomatik olarak mesaj, cevap yok lütfen </ p> <br > <br>","A");
INSERT INTO tbvocabulary VALUES("1792","559","2","Lost_password_log","gönderilen e-posta ?ifresini kaybetti -","A");
INSERT INTO tbvocabulary VALUES("1793","559","2","Manage_fail_import_file","veri ile csv dosyas?n? al?namad?.","A");
INSERT INTO tbvocabulary VALUES("1794","559","2","Manage_fail_move_file","dosya verileri ta??mak için ba?ar?s?z oldu. \\ nGüvenlik ekleri dizin üzerinde izinleri ve yeniden deneyin.","A");
INSERT INTO tbvocabulary VALUES("1795","559","2","Manage_fail_open_file_in","veri dosyas? aç?lamad?","A");
INSERT INTO tbvocabulary VALUES("1796","559","2","Manage_fail_open_file_per","\\ NGüvenlik izinleri ve yeniden deneyin.","A");
INSERT INTO tbvocabulary VALUES("1797","559","2","Manage_service_area","yeni bir alan Kay?tl? -> Kod:","A");
INSERT INTO tbvocabulary VALUES("1798","559","2","Manage_service_using_area","Mevcut alan? kullanarak -> Kod:","A");
INSERT INTO tbvocabulary VALUES("1799","559","2","Manage_service_area_fail","hizmet alan?n? kaydedilemedi","A");
INSERT INTO tbvocabulary VALUES("1800","559","2","Manage_service_inf_line","sat?rda verilmi?tir","A");
INSERT INTO tbvocabulary VALUES("1801","559","2","Manage_service_imp_canceled",". ?thalat iptal edildi!","A");
INSERT INTO tbvocabulary VALUES("1802","559","2","Manage_service_type","Kat?l?m yeni T?P -> Kod:","A");
INSERT INTO tbvocabulary VALUES("1803","559","2","Manage_service_type_fail","bak?m türünü kaydedemedi","A");
INSERT INTO tbvocabulary VALUES("1804","559","2","Manage_service_using_type","Mevcut türünü kullanarak ->","A");
INSERT INTO tbvocabulary VALUES("1805","559","2","Manage_service_item","yeni ö?e kat?ld? ->","A");
INSERT INTO tbvocabulary VALUES("1806","559","2","Manage_service_item_fail","Servis ö?esini kaydedilemedi","A");
INSERT INTO tbvocabulary VALUES("1807","559","2","Manage_service_using_item","Mevcut ö?esini kullanarak -> Kod","A");
INSERT INTO tbvocabulary VALUES("1808","559","2","Manage_service_company_fail","Sütun 8 geçerli bir dosya ismi ?irketi gereklidir. ?irket% kay?tl? de?il!","A");
INSERT INTO tbvocabulary VALUES("1809","559","2","Manage_service_group_fail","ki?i masada grubu kaydedemedi:","A");
INSERT INTO tbvocabulary VALUES("1810","559","2","Manage_service_group_fail2","hizmet grubu kaydedilemedi","A");
INSERT INTO tbvocabulary VALUES("1811","559","2","Manage_service_group_register","! Yeni hizmet grubuna kat?ld? -> Kod","A");
INSERT INTO tbvocabulary VALUES("1812","559","2","Manage_service_group_using","* Mevcut grup kullanarak -> Kod","A");
INSERT INTO tbvocabulary VALUES("1813","559","2","Manage_service_default_pri","öncelik haberdar çünkü, varsay?lan önceli?i ile ili?kili","A");
INSERT INTO tbvocabulary VALUES("1814","559","2","Manage_service_pri_no_exist","sistemde mevcut de?il.","A");
INSERT INTO tbvocabulary VALUES("1815","559","2","Manage_service_fail_code","Servis kod belirlemek için Ba?ar?s?z","A");
INSERT INTO tbvocabulary VALUES("1816","559","2","Manage_service_on_line",", internet üzerinden","A");
INSERT INTO tbvocabulary VALUES("1817","559","2","Manage_service_line","hat","A");
INSERT INTO tbvocabulary VALUES("1818","559","2","Manage_service_already_registered","zaten kay?tl?.","A");
INSERT INTO tbvocabulary VALUES("1819","559","2","Manage_service_column_6","Sütun 6 yaln?zca say?sal de?er içermelidir. de?eri girildi","A");
INSERT INTO tbvocabulary VALUES("1820","559","2","Manage_service_fail","hizmet kay?t ba?ar?s?z","A");
INSERT INTO tbvocabulary VALUES("1821","559","2","Manage_service_inf_on_line","sat?r?nda girilen","A");
INSERT INTO tbvocabulary VALUES("1822","559","2","Manage_service_register_service","Kat?l?m yeni hizmet","A");
INSERT INTO tbvocabulary VALUES("1823","559","2","Manage_service_fail_rel","hizmet aras?ndaki ili?kiyi kaydedemedi","A");
INSERT INTO tbvocabulary VALUES("1824","559","2","Manage_service_and_group","ve grup","A");
INSERT INTO tbvocabulary VALUES("1825","559","2","Manage_service_not_registered","kay?tl? de?il ya da kat?l?yor de?ildir. Hat","A");
INSERT INTO tbvocabulary VALUES("1826","559","2","Manage_service_in_service","serviste","A");
INSERT INTO tbvocabulary VALUES("1827","559","2","Mange_service_order",", sipari?","A");
INSERT INTO tbvocabulary VALUES("1828","559","2","Manage_service_finalized_line","çizgi kesinle?mi?","A");
INSERT INTO tbvocabulary VALUES("1829","559","2","Manage_service_completed","Süreç ba?ar?yla tamamland?","A");
INSERT INTO tbvocabulary VALUES("1830","559","2","Manage_service_not_identify_priority","on line öncelikli hizmet süresini tan?mlamak için aç?lam?yor","A");
INSERT INTO tbvocabulary VALUES("1831","559","2","Manage_instructions","ithalat? için talimatlar? indirin:","A");
INSERT INTO tbvocabulary VALUES("1832","559","2","Manage_layout_service","Düzen ?thalat Hizmetleri","A");
INSERT INTO tbvocabulary VALUES("1833","559","2","Manage_layout_service_file","Düzen-?thalat Services.pdf","A");
INSERT INTO tbvocabulary VALUES("1834","559","2","Male","Erkek","A");
INSERT INTO tbvocabulary VALUES("1835","559","2","Maintenance","Bak?m","A");
INSERT INTO tbvocabulary VALUES("1836","559","2","Minutes","dakika","A");
INSERT INTO tbvocabulary VALUES("1837","559","2","Mobile_phone","Cep telefonu","A");
INSERT INTO tbvocabulary VALUES("1838","559","2","Module","modül","A");
INSERT INTO tbvocabulary VALUES("1839","559","2","Module_insert","modül Ekle","A");
INSERT INTO tbvocabulary VALUES("1840","559","2","Module_name","Modül ad?","A");
INSERT INTO tbvocabulary VALUES("1841","559","2","Modules","Modüller","A");
INSERT INTO tbvocabulary VALUES("1842","559","2","Month","Ay","A");
INSERT INTO tbvocabulary VALUES("1843","559","2","Msg_change_operation","Uyar?! E?er operasyon izinlerini de?i?tirmek isterseniz program?n \'Tür Ki?i ?zni\' tekrar izinlerini ayarlamak gerekir","A");
INSERT INTO tbvocabulary VALUES("1844","559","2","Categories","Kategoriler","A");
INSERT INTO tbvocabulary VALUES("1845","559","2","Name","isim","A");
INSERT INTO tbvocabulary VALUES("1846","559","2","natural","Do?al","A");
INSERT INTO tbvocabulary VALUES("1847","559","2","National_holiday","Ulusal tatil","A");
INSERT INTO tbvocabulary VALUES("1848","559","2","Neighborhood","Kom?uluk","A");
INSERT INTO tbvocabulary VALUES("1849","559","2","New","yeni","A");
INSERT INTO tbvocabulary VALUES("1850","559","2","New_category","Yeni kategori","A");
INSERT INTO tbvocabulary VALUES("1851","559","2","New_date","yeni Tarih","A");
INSERT INTO tbvocabulary VALUES("1852","559","2","New_time","Yeni zaman","A");
INSERT INTO tbvocabulary VALUES("1853","559","2","New_password","Yeni ?ifre","A");
INSERT INTO tbvocabulary VALUES("1854","559","2","New_request","Yeni istek","A");
INSERT INTO tbvocabulary VALUES("1855","559","2","No_abilities","ilgili herhangi yetenekler var","A");
INSERT INTO tbvocabulary VALUES("1856","559","2","No_data","Veri yok.","A");
INSERT INTO tbvocabulary VALUES("1857","559","2","Note","önot","A");
INSERT INTO tbvocabulary VALUES("1858","559","2","Note_msg","öne zaman cevap e-postalar notlar? yerle?tirin.","A");
INSERT INTO tbvocabulary VALUES("1859","559","2","Not_available_yet","Henüz mevcut de?il","A");
INSERT INTO tbvocabulary VALUES("1860","559","2","No_result","Sonuç bulunamad?.","A");
INSERT INTO tbvocabulary VALUES("1861","559","2","No","Yok hay?r","A");
INSERT INTO tbvocabulary VALUES("1862","559","2","Notification","Bildirim","A");
INSERT INTO tbvocabulary VALUES("1863","559","2","Number","önumara","A");
INSERT INTO tbvocabulary VALUES("1864","559","2","Obrigatory_time","Bu etkinlikte harcanan zaman? doldurunuz","A");
INSERT INTO tbvocabulary VALUES("1865","559","2","Observation","Gözlem","A");
INSERT INTO tbvocabulary VALUES("1866","559","2","of","aras?nda","A");
INSERT INTO tbvocabulary VALUES("1867","559","2","Ok_btn","Tamam","A");
INSERT INTO tbvocabulary VALUES("1868","559","2","Only_operator","sadece operatör","A");
INSERT INTO tbvocabulary VALUES("1869","559","2","Option_only_operator","sadece operatörler için Seçenek","A");
INSERT INTO tbvocabulary VALUES("1870","559","2","Opened_by","ile aç?ld?","A");
INSERT INTO tbvocabulary VALUES("1871","559","2","Operations","Operasyonlar","A");
INSERT INTO tbvocabulary VALUES("1872","559","2","Operator","?ebeke","A");
INSERT INTO tbvocabulary VALUES("1873","559","2","Operator_groups","operatör Gruplar?","A");
INSERT INTO tbvocabulary VALUES("1874","559","2","Opening_date","Giri? tarihi","A");
INSERT INTO tbvocabulary VALUES("1875","559","2","Overtime","Mesai","A");
INSERT INTO tbvocabulary VALUES("1876","559","2","Print","bask?","A");
INSERT INTO tbvocabulary VALUES("1877","559","2","Page","Sayfa","A");
INSERT INTO tbvocabulary VALUES("1878","559","2","Page_header_logo","Sayfa Header Logo","A");
INSERT INTO tbvocabulary VALUES("1879","559","2","Password","Parola","A");
INSERT INTO tbvocabulary VALUES("1880","559","2","people","?nsanlar ve ?irketler","A");
INSERT INTO tbvocabulary VALUES("1881","559","2","Permissions","?zinler","A");
INSERT INTO tbvocabulary VALUES("1882","559","2","Permission_error","operasyon s?ras?nda hata","A");
INSERT INTO tbvocabulary VALUES("1883","559","2","Port","Liman","A");
INSERT INTO tbvocabulary VALUES("1884","559","2","Phone","Telefon","A");
INSERT INTO tbvocabulary VALUES("1885","559","2","Pop_server","POP Sunucusu","A");
INSERT INTO tbvocabulary VALUES("1886","559","2","Previous_year","Geçen y?l","A");
INSERT INTO tbvocabulary VALUES("1887","559","2","Priority","öncelik","A");
INSERT INTO tbvocabulary VALUES("1888","559","2","Programs","Programlar","A");
INSERT INTO tbvocabulary VALUES("1889","559","2","PDF_person_report","ki?i Raporu","A");
INSERT INTO tbvocabulary VALUES("1890","559","2","PDF_code","kod","A");
INSERT INTO tbvocabulary VALUES("1891","559","2","PDF_Page","Sayfa","A");
INSERT INTO tbvocabulary VALUES("1892","559","2","Widget","Widget","A");
INSERT INTO tbvocabulary VALUES("1893","559","2","Reason","öneden","A");
INSERT INTO tbvocabulary VALUES("1894","559","2","Recalculate_msg_chk","onaylar?n bitiminden sonra servis süresini yeniden hesapla","A");
INSERT INTO tbvocabulary VALUES("1895","559","2","Recalculate","Yeniden Hesapla","A");
INSERT INTO tbvocabulary VALUES("1896","559","2","Remove","Kald?r","A");
INSERT INTO tbvocabulary VALUES("1897","559","2","Register_btn","Kay?t olmak","A");
INSERT INTO tbvocabulary VALUES("1898","559","2","Related_abilities","?lgili Yetenekler","A");
INSERT INTO tbvocabulary VALUES("1899","559","2","Repass_btn","geri gitmek","A");
INSERT INTO tbvocabulary VALUES("1900","559","2","Repassed","Geri gönderildi","A");
INSERT INTO tbvocabulary VALUES("1901","559","2","Repass_request_only","Sadece istekleri geri gitmek","A");
INSERT INTO tbvocabulary VALUES("1902","559","2","Repass_request_to","için geri gitmek iste?i","A");
INSERT INTO tbvocabulary VALUES("1903","559","2","Rejected","reddedilen","A");
INSERT INTO tbvocabulary VALUES("1904","559","2","Reports_logo","Raporlar Logo","A");
INSERT INTO tbvocabulary VALUES("1905","559","2","Request","?stek","A");
INSERT INTO tbvocabulary VALUES("1906","559","2","Requests","istekler","A");
INSERT INTO tbvocabulary VALUES("1907","559","2","Request_assumed","Talep Tahmini","A");
INSERT INTO tbvocabulary VALUES("1908","559","2","Request_approve_app","Onayla istek formu","A");
INSERT INTO tbvocabulary VALUES("1909","559","2","Request_reprove_app","Talep reprove","A");
INSERT INTO tbvocabulary VALUES("1910","559","2","Request_return_app","Bir önceki faza dönü?","A");
INSERT INTO tbvocabulary VALUES("1911","559","2","Request_rejected","Talep kat?ld? edilemedi:","A");
INSERT INTO tbvocabulary VALUES("1912","559","2","Request_repassed","Talep Repassed","A");
INSERT INTO tbvocabulary VALUES("1913","559","2","Request_reopened","Talep Reopened","A");
INSERT INTO tbvocabulary VALUES("1914","559","2","Request_closed","Talep Kapal?","A");
INSERT INTO tbvocabulary VALUES("1915","559","2","Request_canceled","Talep ?ptal Edildi","A");
INSERT INTO tbvocabulary VALUES("1916","559","2","Request_opened","Talep Aç?ld?","A");
INSERT INTO tbvocabulary VALUES("1917","559","2","Request_code","?stek kodu","A");
INSERT INTO tbvocabulary VALUES("1918","559","2","Request_owner","Talep Sahibi","A");
INSERT INTO tbvocabulary VALUES("1919","559","2","Request_not_approve","istek kabul edilmedi","A");
INSERT INTO tbvocabulary VALUES("1920","559","2","Request_approve","Onay bekleyen istekleri vard?r. <br/> Bunu onaylamadan önce yeni istekleri aç?lam?yor.","A");
INSERT INTO tbvocabulary VALUES("1921","559","2","Request_waiting_approval","`?n onay bekliyor","A");
INSERT INTO tbvocabulary VALUES("1922","559","2","Requires_Autentication","Do?rulamas? gerektirir","A");
INSERT INTO tbvocabulary VALUES("1923","559","2","Smarty","Smarty De?i?ken","A");
INSERT INTO tbvocabulary VALUES("1924","559","2","Save","Kay?t etmek","A");
INSERT INTO tbvocabulary VALUES("1925","559","2","Search","Arama","A");
INSERT INTO tbvocabulary VALUES("1926","559","2","Set_repass_groups","Set geri gitmek gruplar?","A");
INSERT INTO tbvocabulary VALUES("1927","559","2","Select","seçmek","A");
INSERT INTO tbvocabulary VALUES("1928","559","2","Select_acess_level","Eri?im Seviye Seç","A");
INSERT INTO tbvocabulary VALUES("1929","559","2","Select_area","bir alan seçin","A");
INSERT INTO tbvocabulary VALUES("1930","559","2","Select_category","Bir kategori seçin","A");
INSERT INTO tbvocabulary VALUES("1931","559","2","Select_company","Bir ?irket","A");
INSERT INTO tbvocabulary VALUES("1932","559","2","Select_department","Bir bölümü seçti","A");
INSERT INTO tbvocabulary VALUES("1933","559","2","Select_group","SBir grup seçin","A");
INSERT INTO tbvocabulary VALUES("1934","559","2","Select_priority","Bir öncelik seçin","A");
INSERT INTO tbvocabulary VALUES("1935","559","2","Select_module","modülünü seçin","A");
INSERT INTO tbvocabulary VALUES("1936","559","2","Select_group_operator","Bir grup veya operatörü seçin.","A");
INSERT INTO tbvocabulary VALUES("1937","559","2","Send","GÖNDER","A");
INSERT INTO tbvocabulary VALUES("1938","559","2","Send_email","Eposta gönder","A");
INSERT INTO tbvocabulary VALUES("1939","559","2","Serial_number","Seri numaras?","A");
INSERT INTO tbvocabulary VALUES("1940","559","2","Service","Hizmet","A");
INSERT INTO tbvocabulary VALUES("1941","559","2","Server","sunucu","A");
INSERT INTO tbvocabulary VALUES("1942","559","2","Service_insert","servis Ekle","A");
INSERT INTO tbvocabulary VALUES("1943","559","2","Service_edit","hizmet Düzenleme","A");
INSERT INTO tbvocabulary VALUES("1944","559","2","Service_name","Hizmet Ad?","A");
INSERT INTO tbvocabulary VALUES("1945","559","2","Groups_by_service","Hizmetlere göre görüntüle gruplar?","A");
INSERT INTO tbvocabulary VALUES("1946","559","2","Send_alerts_topic_email","Evet, bu konu için e-posta ile uyar? göndermek.","A");
INSERT INTO tbvocabulary VALUES("1947","559","2","Send_alerts_email","Evet, e-posta yoluyla bu uyar? gönderir.","A");
INSERT INTO tbvocabulary VALUES("1948","559","2","Service_order_number","??letim Numaras?","A");
INSERT INTO tbvocabulary VALUES("1949","559","2","Service_order_number_min","??letim Numaras?","A");
INSERT INTO tbvocabulary VALUES("1950","559","2","Show_attendants_title","grup taraf?ndan göster operatörleri","A");
INSERT INTO tbvocabulary VALUES("1951","559","2","show","Göster","A");
INSERT INTO tbvocabulary VALUES("1952","559","2","Show_in","göster","A");
INSERT INTO tbvocabulary VALUES("1953","559","2","SMS","SMS","A");
INSERT INTO tbvocabulary VALUES("1954","559","2","Solution","Çözüm","A");
INSERT INTO tbvocabulary VALUES("1955","559","2","Source","Kaynak","A");
INSERT INTO tbvocabulary VALUES("1956","559","2","Started","ba?lad?","A");
INSERT INTO tbvocabulary VALUES("1957","559","2","State","Belirtmek, bildirmek","A");
INSERT INTO tbvocabulary VALUES("1958","559","2","status","durum","A");
INSERT INTO tbvocabulary VALUES("1959","559","2","Still_viewing","Ben bu iste?i incelemeye devam etmek istiyorum.","A");
INSERT INTO tbvocabulary VALUES("1960","559","2","Stop_viewing","Bu iste?i inceleyen durdurun.","A");
INSERT INTO tbvocabulary VALUES("1961","559","2","Subject","konu","A");
INSERT INTO tbvocabulary VALUES("1962","559","2","Success_logs","ba?ar? günlükleri","A");
INSERT INTO tbvocabulary VALUES("1963","559","2","Tag_min","Etiket","A");
INSERT INTO tbvocabulary VALUES("1964","559","2","Template_edit","?ablon Düzenle","A");
INSERT INTO tbvocabulary VALUES("1965","559","2","Time_expended","özaman görev Harcanan","A");
INSERT INTO tbvocabulary VALUES("1966","559","2","Time_value","özaman De?eri","A");
INSERT INTO tbvocabulary VALUES("1967","559","2","Title","Ba?l?k","A");
INSERT INTO tbvocabulary VALUES("1968","559","2","tlt_span_group","Grubuma gelen talepler","A");
INSERT INTO tbvocabulary VALUES("1969","559","2","tlt_span_my","benim ?stekler","A");
INSERT INTO tbvocabulary VALUES("1970","559","2","tlt_span_track_group","grup taraf?ndan Takip","A");
INSERT INTO tbvocabulary VALUES("1971","559","2","tlt_span_track_me","Bana göre Parça","A");
INSERT INTO tbvocabulary VALUES("1972","559","2","to","için","A");
INSERT INTO tbvocabulary VALUES("1973","559","2","Topic","konu","A");
INSERT INTO tbvocabulary VALUES("1974","559","2","Topic_edit","Konu Düzenleme","A");
INSERT INTO tbvocabulary VALUES("1975","559","2","Total_minutes","Toplam dakikalar","A");
INSERT INTO tbvocabulary VALUES("1976","559","2","Total","Genel Toplam","A");
INSERT INTO tbvocabulary VALUES("1977","559","2","Total_holidays","Toplam Tatiller","A");
INSERT INTO tbvocabulary VALUES("1978","559","2","type","tip","A");
INSERT INTO tbvocabulary VALUES("1979","559","2","Type_adress","Tip adresi","A");
INSERT INTO tbvocabulary VALUES("1980","559","2","Type_edit","Düzenle Tür","A");
INSERT INTO tbvocabulary VALUES("1981","559","2","Type_insert","Ekle Tipi","A");
INSERT INTO tbvocabulary VALUES("1982","559","2","Type_name","Türünün ad?","A");
INSERT INTO tbvocabulary VALUES("1983","559","2","Update","Güncelle?tirme","A");
INSERT INTO tbvocabulary VALUES("1984","559","2","User","kullan?c?","A");
INSERT INTO tbvocabulary VALUES("1985","559","2","User_login","Kullan?c? Giri?i","A");
INSERT INTO tbvocabulary VALUES("1986","559","2","Until_closed","kapanana kadar","A");
INSERT INTO tbvocabulary VALUES("1987","559","2","Validity_Standard","Geçerlilik Standart","A");
INSERT INTO tbvocabulary VALUES("1988","559","2","Valid","Geçerli","A");
INSERT INTO tbvocabulary VALUES("1989","559","2","Valid_until","geçerli kadar","A");
INSERT INTO tbvocabulary VALUES("1990","559","2","Version","versiyon","A");
INSERT INTO tbvocabulary VALUES("1991","559","2","Visible","Gözle görülür","A");
INSERT INTO tbvocabulary VALUES("1992","559","2","VIP_user","VIP kullan?c?","A");
INSERT INTO tbvocabulary VALUES("1993","559","2","View_groups","görünüm gruplar?","A");
INSERT INTO tbvocabulary VALUES("1994","559","2","Yes","Evet","A");
INSERT INTO tbvocabulary VALUES("1995","559","2","Waiting_for_approval","onay bekleniyor","A");
INSERT INTO tbvocabulary VALUES("1996","559","2","Warning_new_topic","Yeni Konu","A");
INSERT INTO tbvocabulary VALUES("1997","559","2","Zipcode","Posta kodu","A");
INSERT INTO tbvocabulary VALUES("1998","559","2","Normal","normal","A");
INSERT INTO tbvocabulary VALUES("1999","559","2","Monday","Pazartesi","A");
INSERT INTO tbvocabulary VALUES("2000","559","2","Tuesday","Sal?","A");
INSERT INTO tbvocabulary VALUES("2001","559","2","Wednesday","Çar?amba","A");
INSERT INTO tbvocabulary VALUES("2002","559","2","Thursday","Per?embe","A");
INSERT INTO tbvocabulary VALUES("2003","559","2","Friday","Cuma","A");
INSERT INTO tbvocabulary VALUES("2004","559","2","Saturday","Cumartesi","A");
INSERT INTO tbvocabulary VALUES("2005","559","2","Sunday","Pazar","A");
INSERT INTO tbvocabulary VALUES("2006","559","2","January","Ocak ay?","A");
INSERT INTO tbvocabulary VALUES("2007","559","2","February","?ubat ay?","A");
INSERT INTO tbvocabulary VALUES("2008","559","2","March","Mart","A");
INSERT INTO tbvocabulary VALUES("2009","559","2","April","önisan","A");
INSERT INTO tbvocabulary VALUES("2010","559","2","May","May?s ay?","A");
INSERT INTO tbvocabulary VALUES("2011","559","2","June","Haziran","A");
INSERT INTO tbvocabulary VALUES("2012","559","2","July","Temmuz","A");
INSERT INTO tbvocabulary VALUES("2013","559","2","August","A?ustos","A");
INSERT INTO tbvocabulary VALUES("2014","559","2","September","Eylül","A");
INSERT INTO tbvocabulary VALUES("2015","559","2","October","Ekim","A");
INSERT INTO tbvocabulary VALUES("2016","559","2","November","Kas?m","A");
INSERT INTO tbvocabulary VALUES("2017","559","2","December","Aral?k","A");
INSERT INTO tbvocabulary VALUES("2018","559","2","type_user_user","kullan?c?","A");
INSERT INTO tbvocabulary VALUES("2019","559","2","type_user_operator","?ebeke","A");
INSERT INTO tbvocabulary VALUES("2020","559","2","Var_user","Kay?tl? Kullan?c?","A");
INSERT INTO tbvocabulary VALUES("2021","559","2","Var_phone","Kullan?c?n?n Telefon","A");
INSERT INTO tbvocabulary VALUES("2022","559","2","Var_branch","Kullan?c? ?ube telefon numaras?","A");
INSERT INTO tbvocabulary VALUES("2023","559","2","Var_date","E-posta gönderilir tarihi","A");
INSERT INTO tbvocabulary VALUES("2024","559","2","Var_request","?stek kodu","A");
INSERT INTO tbvocabulary VALUES("2025","559","2","Var_subject","Talep Konusu","A");
INSERT INTO tbvocabulary VALUES("2026","559","2","Var_description","Talep Aç?klama","A");
INSERT INTO tbvocabulary VALUES("2027","559","2","Var_record","Giri? tarihi","A");
INSERT INTO tbvocabulary VALUES("2028","559","2","Var_expire","Son kullanma tarihi","A");
INSERT INTO tbvocabulary VALUES("2029","559","2","Var_assume","Tarihi varsayal?m","A");
INSERT INTO tbvocabulary VALUES("2030","559","2","Var_finish","Biti? tarihi","A");
INSERT INTO tbvocabulary VALUES("2031","559","2","Var_rejection","Reddetme tarihi","A");
INSERT INTO tbvocabulary VALUES("2032","559","2","Var_status","Talep Durumu","A");
INSERT INTO tbvocabulary VALUES("2033","559","2","Var_requester","?stekte Ad","A");
INSERT INTO tbvocabulary VALUES("2034","559","2","Var_incharge","Sorumlu","A");
INSERT INTO tbvocabulary VALUES("2035","559","2","Var_nt_operator","Sadece operatörler görebilir belirtiyor","A");
INSERT INTO tbvocabulary VALUES("2036","559","2","Var_nt_user","kullan?c?lar görebilir belirtiyor","A");
INSERT INTO tbvocabulary VALUES("2037","559","2","Var_link_operator","istek sayfaya link (operatör)","A");
INSERT INTO tbvocabulary VALUES("2038","559","2","Var_link_user","istek sayfaya link (kullan?c?)","A");
INSERT INTO tbvocabulary VALUES("2039","559","2","Var_evaluation","Kullan?c? taraf?ndan verilen de?erlendirme","A");
INSERT INTO tbvocabulary VALUES("2040","559","2","Var_link_evaluation","Ba?lant? giri? yapmadan de?erlendirmek.","A");
INSERT INTO tbvocabulary VALUES("2041","559","2","auxiliary_operator_include","Yard?mc? Operator ekleyin","A");
INSERT INTO tbvocabulary VALUES("2042","559","2","btn_save_changes","De?i?iklikleri Kaydet","A");
INSERT INTO tbvocabulary VALUES("2043","559","2","btn_assume","üstlenmek","A");
INSERT INTO tbvocabulary VALUES("2044","559","2","btn_reject","reddetmek","A");
INSERT INTO tbvocabulary VALUES("2045","559","2","btn_close","Kapat","A");
INSERT INTO tbvocabulary VALUES("2046","559","2","btn_reopen","yeniden açmak","A");
INSERT INTO tbvocabulary VALUES("2047","559","2","btn_ope_aux","operatör Yard?mc?","A");
INSERT INTO tbvocabulary VALUES("2048","559","2","Other_items","Di?er seçenekler","A");
INSERT INTO tbvocabulary VALUES("2049","559","2","sys_time_session","oturum sistemini sürecek zaman?. saniyeler içinde de?er. E?er 10 dakika olacak ayarlanmam??.","A");
INSERT INTO tbvocabulary VALUES("2050","559","2","grd_show_only_mine","Sadece beni göster","A");
INSERT INTO tbvocabulary VALUES("2051","559","2","grd_show_group","Benim gruplar? göster","A");
INSERT INTO tbvocabulary VALUES("2052","559","2","grd_show_all","Tümünü göster","A");
INSERT INTO tbvocabulary VALUES("2053","559","2","grd_expiring","Bitmek","A");
INSERT INTO tbvocabulary VALUES("2054","559","2","grd_expiring_today","bugün süresi dolan","A");
INSERT INTO tbvocabulary VALUES("2055","559","2","grd_expired","Süresi doldu","A");
INSERT INTO tbvocabulary VALUES("2056","559","2","grd_expired_n_assumed","kazanmam?? süresi dolmu?","A");
INSERT INTO tbvocabulary VALUES("2057","559","2","cat_records","kay?tlar","A");
INSERT INTO tbvocabulary VALUES("2058","559","2","cat_config","Yap?land?rma","A");
INSERT INTO tbvocabulary VALUES("2059","559","2","cat_reports","Raporlar","A");
INSERT INTO tbvocabulary VALUES("2060","559","2","pgr_departments","bölümler","A");
INSERT INTO tbvocabulary VALUES("2061","559","2","seconds","saniye","A");
INSERT INTO tbvocabulary VALUES("2062","559","2","reload_request","istek verileri güncelle","A");
INSERT INTO tbvocabulary VALUES("2063","559","2","Error_insert_note","Hata notu ekleyerek","A");
INSERT INTO tbvocabulary VALUES("2064","559","2","Alert_Cancel_sucess","Talep ba?ar?yla iptal edildi","A");
INSERT INTO tbvocabulary VALUES("2065","559","2","Alert_note_sucess","ba?ar?yla tak?l? Not","A");
INSERT INTO tbvocabulary VALUES("2066","559","2","Alert_close_request","bitmi? talep","A");
INSERT INTO tbvocabulary VALUES("2067","559","2","Alert_reopen_sucess","Talep aç?ld?","A");
INSERT INTO tbvocabulary VALUES("2068","559","2","Alert_deleted_note","önot silindi","A");
INSERT INTO tbvocabulary VALUES("2069","559","2","Fill","doldurmak","A");
INSERT INTO tbvocabulary VALUES("2070","559","2","Reject_sucess","Talep Reddedildi","A");
INSERT INTO tbvocabulary VALUES("2071","559","2","Reject_btn","reddetmek","A");
INSERT INTO tbvocabulary VALUES("2072","559","2","Save_changes_sucess","De?i?iklikler kaydedildi","A");
INSERT INTO tbvocabulary VALUES("2073","559","2","Initial_date","ilk Tarih","A");
INSERT INTO tbvocabulary VALUES("2074","559","2","Finish_date","Biti? tarihi","A");
INSERT INTO tbvocabulary VALUES("2075","559","2","Average","Ortalama","A");
INSERT INTO tbvocabulary VALUES("2076","559","2","Dashboard_SLAFulfillment","yerine getirme","A");
INSERT INTO tbvocabulary VALUES("2077","559","2","Dashboard_SLANotFulfillment","de?il yerine getirilmesi","A");
INSERT INTO tbvocabulary VALUES("2078","559","2","Dashboard_UpdatedDaily","günlük güncellendi","A");



