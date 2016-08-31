/*
SQLyog Enterprise - MySQL GUI v8.02 RC
MySQL - 5.1.61 : Database - quintana
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

/*Table structure for table `dsh_tbcategory` */

DROP TABLE IF EXISTS `dsh_tbcategory`;

CREATE TABLE `dsh_tbcategory` (
  `idcategory` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT NULL,
  `url` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`idcategory`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

/*Data for the table `dsh_tbcategory` */

insert  into `dsh_tbcategory`(`idcategory`,`title`,`url`) values (1,'Helpdezk','___/dashboard/index/getDashBoardWidgets/id/helpdezk');
insert  into `dsh_tbcategory`(`idcategory`,`title`,`url`) values (2,'General','__/dashboard/index/getDashBoardWidgets/id/general');
insert  into `dsh_tbcategory`(`idcategory`,`title`,`url`) values (3,'Cms','___/dashboard/index/getDashBoardWidgets/id/cms');
insert  into `dsh_tbcategory`(`idcategory`,`title`,`url`) values (4,'Infraestrutura',NULL);
insert  into `dsh_tbcategory`(`idcategory`,`title`,`url`) values (5,'Mario Quintana',NULL);

/*Table structure for table `dsh_tbcategory_has_widget` */

DROP TABLE IF EXISTS `dsh_tbcategory_has_widget`;

CREATE TABLE `dsh_tbcategory_has_widget` (
  `idcategory` int(11) DEFAULT NULL,
  `idwidget` int(11) DEFAULT NULL,
  KEY `FK_dsh_tbcategory_has_widget` (`idwidget`),
  KEY `FK1_dsh_tbcategory_has_widget` (`idcategory`),
  CONSTRAINT `FK1_dsh_tbcategory_has_widget` FOREIGN KEY (`idcategory`) REFERENCES `dsh_tbcategory` (`idcategory`),
  CONSTRAINT `FK_dsh_tbcategory_has_widget` FOREIGN KEY (`idwidget`) REFERENCES `dsh_tbwidget` (`idwidget`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `dsh_tbcategory_has_widget` */

insert  into `dsh_tbcategory_has_widget`(`idcategory`,`idwidget`) values (1,2);
insert  into `dsh_tbcategory_has_widget`(`idcategory`,`idwidget`) values (1,1);
insert  into `dsh_tbcategory_has_widget`(`idcategory`,`idwidget`) values (2,1);
insert  into `dsh_tbcategory_has_widget`(`idcategory`,`idwidget`) values (3,3);
insert  into `dsh_tbcategory_has_widget`(`idcategory`,`idwidget`) values (4,4);
insert  into `dsh_tbcategory_has_widget`(`idcategory`,`idwidget`) values (3,5);
insert  into `dsh_tbcategory_has_widget`(`idcategory`,`idwidget`) values (4,6);
insert  into `dsh_tbcategory_has_widget`(`idcategory`,`idwidget`) values (5,7);
insert  into `dsh_tbcategory_has_widget`(`idcategory`,`idwidget`) values (5,8);
insert  into `dsh_tbcategory_has_widget`(`idcategory`,`idwidget`) values (1,9);
insert  into `dsh_tbcategory_has_widget`(`idcategory`,`idwidget`) values (1,10);
insert  into `dsh_tbcategory_has_widget`(`idcategory`,`idwidget`) values (1,11);

/*Table structure for table `dsh_tbwidget` */

DROP TABLE IF EXISTS `dsh_tbwidget`;

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
  `description` varchar(100) DEFAULT NULL,
  `creator` varchar(100) DEFAULT NULL,
  `url` varchar(150) DEFAULT NULL,
  `image` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`idwidget`),
  KEY `idx_name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;

/*Data for the table `dsh_tbwidget` */

insert  into `dsh_tbwidget`(`idwidget`,`name`,`dbhost`,`dbuser`,`dbpass`,`dbname`,`field1`,`field2`,`field3`,`field4`,`field5`,`description`,`creator`,`url`,`image`) values (1,'Estoque de solicitações',NULL,NULL,NULL,NULL,'30',NULL,NULL,NULL,NULL,'Estoque de solicitaÃ§Ãµes dos Ãºltimos 30 dias, de todos os atenddentes','Rogério Albandes','/dashboard/hdk_requestassets/home/idwidget/1','hdk_request_assets.jpg');
insert  into `dsh_tbwidget`(`idwidget`,`name`,`dbhost`,`dbuser`,`dbpass`,`dbname`,`field1`,`field2`,`field3`,`field4`,`field5`,`description`,`creator`,`url`,`image`) values (2,'Solicitações',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Tabela com as solicitações ativas do atendente','Rogério Albandes','/dashboard/hdk_requests/home/idwidget/2','image2.png');
insert  into `dsh_tbwidget`(`idwidget`,`name`,`dbhost`,`dbuser`,`dbpass`,`dbname`,`field1`,`field2`,`field3`,`field4`,`field5`,`description`,`creator`,`url`,`image`) values (3,'Acessos ao Portal',NULL,NULL,NULL,NULL,'ralbandes@gmail.com','Bucet@.2013','46811633',NULL,NULL,'Acessos ao portal - Google Analytics','Rogério Albandes','/dashboard/cms_analyticsacessos/home/idwidget/3',NULL);
insert  into `dsh_tbwidget`(`idwidget`,`name`,`dbhost`,`dbuser`,`dbpass`,`dbname`,`field1`,`field2`,`field3`,`field4`,`field5`,`description`,`creator`,`url`,`image`) values (4,'Tráfego Link Embratel',NULL,NULL,NULL,NULL,'http://monitora.marioquintana.com.br:8888/graph_image_pipe.php?action=edit&local_graph_id=5&rra_id=1&graph_height=100&graph_width=320',NULL,NULL,NULL,NULL,'Tráfego no link da rede administrativa','Rogério Albandes','/dashboard/inf_embratel/home/idwidget/4',NULL);
insert  into `dsh_tbwidget`(`idwidget`,`name`,`dbhost`,`dbuser`,`dbpass`,`dbname`,`field1`,`field2`,`field3`,`field4`,`field5`,`description`,`creator`,`url`,`image`) values (5,'Downloads versão demo','helpdezk.org','albandes_rogerio','361/619*','albandes_site','180','6',NULL,NULL,NULL,'Downloads da versão demo por país ','Rogério Albandes','/dashboard/cms_sourceforgecountrys/home/idwidget/5',NULL);
insert  into `dsh_tbwidget`(`idwidget`,`name`,`dbhost`,`dbuser`,`dbpass`,`dbname`,`field1`,`field2`,`field3`,`field4`,`field5`,`description`,`creator`,`url`,`image`) values (6,'Squid - HTTP Requests',NULL,NULL,NULL,NULL,'http://monitora.marioquintana.com.br:8888/graph_image_pipe.php?action=edit&local_graph_id=70&rra_id=1&graph_height=100&graph_width=320',NULL,NULL,NULL,NULL,'Requisições no proxy da rede administrativa','Rogério Albandes','/dashboard/inf_squid/home/idwidget/6',NULL);
insert  into `dsh_tbwidget`(`idwidget`,`name`,`dbhost`,`dbuser`,`dbpass`,`dbname`,`field1`,`field2`,`field3`,`field4`,`field5`,`description`,`creator`,`url`,`image`) values (7,'Impressões por usuário','10.42.43.10','helpdezk','qpal10','extramq','30','6',NULL,NULL,NULL,'Número de impressões por usuários (maiores)','Rogério Albandes','/dashboard/maq_impusuario/home/idwidget/7',NULL);
insert  into `dsh_tbwidget`(`idwidget`,`name`,`dbhost`,`dbuser`,`dbpass`,`dbname`,`field1`,`field2`,`field3`,`field4`,`field5`,`description`,`creator`,`url`,`image`) values (8,'Coletoras','10.42.43.10','helpdezk','qpal10','extramq','20',NULL,NULL,NULL,NULL,'Bilhetes coletados','Rogério Albandes','/dashboard/maq_coletora/home/idwidget/8','coletoras.jpg');
insert  into `dsh_tbwidget`(`idwidget`,`name`,`dbhost`,`dbuser`,`dbpass`,`dbname`,`field1`,`field2`,`field3`,`field4`,`field5`,`description`,`creator`,`url`,`image`) values (9,'SLA - Vencimento semanal',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Solicitações com vencimento na semana atual ','Rogério Albandes','/dashboard/hdk_vctosemana/home/idwidget/9',NULL);
insert  into `dsh_tbwidget`(`idwidget`,`name`,`dbhost`,`dbuser`,`dbpass`,`dbname`,`field1`,`field2`,`field3`,`field4`,`field5`,`description`,`creator`,`url`,`image`) values (10,'SLA - Solicitação fora do prazo',NULL,NULL,NULL,NULL,'2013',NULL,NULL,NULL,NULL,'Solicitações fora do prazo ','Rogério Albandes','/dashboard/hdk_outoftime/home/idwidget/10',NULL);
insert  into `dsh_tbwidget`(`idwidget`,`name`,`dbhost`,`dbuser`,`dbpass`,`dbname`,`field1`,`field2`,`field3`,`field4`,`field5`,`description`,`creator`,`url`,`image`) values (11,'Hard User ',NULL,NULL,NULL,NULL,'2013','6',NULL,NULL,NULL,'Usuário mais ativo ','Rogério Albandes','/dashboard/hdk_harduser/home/idwidget/11',NULL);

/*Table structure for table `dsh_tbwidgetusuario` */

DROP TABLE IF EXISTS `dsh_tbwidgetusuario`;

CREATE TABLE `dsh_tbwidgetusuario` (
  `idwidgetusuario` int(11) NOT NULL AUTO_INCREMENT,
  `idusuario` int(10) unsigned NOT NULL,
  `widgets` blob,
  PRIMARY KEY (`idwidgetusuario`)
) ENGINE=InnoDB AUTO_INCREMENT=72 DEFAULT CHARSET=latin1;

/*Data for the table `dsh_tbwidgetusuario` */

insert  into `dsh_tbwidgetusuario`(`idwidgetusuario`,`idusuario`,`widgets`) values (70,14,'{\"result\" :{\"layout\": \"layout5\", \"data\" : []}}');
insert  into `dsh_tbwidgetusuario`(`idwidgetusuario`,`idusuario`,`widgets`) values (71,18,'{\"result\" :{\"layout\": \"layout5\", \"data\" : [{\"title\" : \"Tráfego Link Embratel\", \"id\" : \"5328669.93767219\", \"column\" : \"first\",\"editurl\" : \"undefined\",\"open\" : true,\"url\" : \"/branch/dashboard/inf_embratel/home/idwidget/4\",\"metadata\":{}},{\"title\" : \"Squid - HTTP Requests\", \"id\" : \"5027857.834810456\", \"column\" : \"undefined\",\"editurl\" : \"undefined\",\"open\" : true,\"url\" : \"/branch/dashboard/inf_squid/home/idwidget/6\",\"metadata\":{}},{\"title\" : \"Hard User \", \"id\" : \"5027859.834810456\", \"column\" : \"undefined\",\"editurl\" : \"undefined\",\"open\" : true,\"url\" : \"/branch/dashboard/hdk_harduser/home/idwidget/11\",\"metadata\":{}},{\"title\" : \"Acessos ao Portal\", \"id\" : \"5745954.823874734\", \"column\" : \"second\",\"editurl\" : \"undefined\",\"open\" : true,\"url\" : \"/branch/dashboard/cms_analyticsacessos/home/idwidget/3\",\"metadata\":{}},{\"title\" : \"Downloads versão demo\", \"id\" : \"5745955.823874734\", \"column\" : \"second\",\"editurl\" : \"undefined\",\"open\" : true,\"url\" : \"/branch/dashboard/cms_sourceforgecountrys/home/idwidget/5\",\"metadata\":{}},{\"title\" : \"SLA - Solicitação fora do prazo\", \"id\" : \"5328667.93767219\", \"column\" : \"second\",\"editurl\" : \"undefined\",\"open\" : true,\"url\" : \"/branch/dashboard/hdk_outoftime/home/idwidget/10\",\"metadata\":{}},{\"title\" : \"Estoque de solicitações\", \"id\" : \"5027858.834810456\", \"column\" : \"third\",\"editurl\" : \"undefined\",\"open\" : true,\"url\" : \"/branch/dashboard/hdk_requestassets/home/idwidget/1\",\"metadata\":{}},{\"title\" : \"Impressões por usuário\", \"id\" : \"5027860.834810456\", \"column\" : \"third\",\"editurl\" : \"undefined\",\"open\" : true,\"url\" : \"/branch/dashboard/maq_impusuario/home/idwidget/7\",\"metadata\":{}},{\"title\" : \"SLA - Vencimento semanal\", \"id\" : \"5328665.93767219\", \"column\" : \"third\",\"editurl\" : \"undefined\",\"open\" : true,\"url\" : \"/branch/dashboard/hdk_vctosemana/home/idwidget/9\",\"metadata\":{}}]}}');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
