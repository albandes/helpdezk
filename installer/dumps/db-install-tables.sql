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
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=latin1 AVG_ROW_LENGTH=16384 ROW_FORMAT=DYNAMIC;

INSERT INTO hdk_tbexternallapp VALUES("50","Trello","https://api.trello.com");



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
) ENGINE=InnoDB AUTO_INCREMENT=254 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

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
  FULLTEXT KEY `defaultmodule` (`defaultmodule`)
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
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

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
) ENGINE=InnoDB AUTO_INCREMENT=995 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

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



