<?php
if (substr(php_sapi_name(), 0, 3) != 'cli') { die("This program runs only in the command line"); }
set_time_limit(0);
$path_parts = pathinfo(dirname(__FILE__));
$cron_path = $path_parts['dirname'] ;
$lb = "\n";
include($cron_path . "/includes/config/config.php") ;
include($cron_path . "/includes/adodb/adodb.inc.php");

$db = NewADOConnection('mysqli');
if (!$db->Connect($config["db_hostname"] , $config["db_username"] , $config["db_password"], $config["db_name"])) {  die("$lb Database Error : " . $db->ErrorNo() . " - " . $db->ErrorMsg()); }

$query_check_msg = "SELECT COUNT(*) AS total_msg FROM bbd_tbmessage a WHERE (a.dtend > NOW() OR a.dtend = '0000-00-00 00:00:00') AND a.emailsent = 0 AND a.sendemail = 'S'";
$rs_check_msg = $db->Execute($query_check_msg);
if(!$rs_check_msg) die("$lb Erro : " . $db->ErrorMsg());

if($rs_check_msg->fields['total_msg'] > 0){
	require_once("../includes/classes/phpMailer/class.phpmailer.php");
	$mail = new phpmailer();
	
	// CONFIG EMAIL DATA 
	$sql_configemail = "select session_name,value,description from hdk_tbconfig where idconfigcategory IN (5,11)";
	$rs_configemail = $db->Execute($sql_configemail);
	if(!$rs_configemail) die("$lb Erro : " . $db->ErrorMsg());
	while (!$rs_configemail->EOF) {
	    $ses = $rs_configemail->fields['session_name'];
	    $val = $rs_configemail->fields['value'];
		if(!$val || is_null($val) ) $val =  $rs_configemail->fields['description'];
	    $emailConfs[$ses] = $val;
	    $rs_configemail->MoveNext();
	}
	$nom_titulo 	= $emailConfs['EM_TITLE'];
	$mail_metodo 	= 'smtp';
	$mail_host 		= $emailConfs['EM_HOSTNAME'];
	$mail_dominio 	= $emailConfs['EM_DOMAIN'];
	$mail_auth 		= $emailConfs['EM_AUTH'];
	$mail_username 	= $emailConfs['EM_USER'];
	$mail_password 	= $emailConfs['EM_PASSWORD'];
	$mail_remetente = $emailConfs['EM_SENDER'];
	$mail_cabecalho = $emailConfs['EM_HEADER'];
	$mail_rodape 	= $emailConfs['EM_FOOTER'];
	//  END CONFIG EMAIL DATA 
	
	//PEGA TODAS MENSAGENS QUE ESTÃO ATIVAS E AINDA NÃO FOI ENVIADO EMAIL
	$sql = "SELECT 
				a.idmessage,
				b.idtopic,
			    b.title as title_topic,
		  	    a.title as title_warning,
		 	    a.description,
			    a.dtcreate,
			    a.dtstart,
			    a.dtend,
				(select count(*) from bbd_topic_company WHERE idtopic = a.idtopic) AS total_company,
				(select count(*) from bbd_topic_group WHERE idtopic = a.idtopic) AS total_group
			FROM bbd_tbmessage a, bbd_topic b
			WHERE a.idtopic = b.idtopic 
			AND (a.dtend > NOW() OR a.dtend = '0000-00-00 00:00:00')
			AND a.emailsent = 0 AND a.sendemail = 'S'";
	$rs = $db->Execute($sql);
	if(!$rs) die("$lb Erro : " . $db->ErrorMsg());
	
	while (!$rs->EOF) {
		$i = 0;
		$destinatario = array();
		$id = $rs->fields['idmessage'];
		$id_topic = $rs->fields['idtopic'];
		$total_company = $rs->fields['total_company'];
		$total_group = $rs->fields['total_group'];		
		
		if(!$total_company and !$total_group){					
			if(count($destinatario_all) > 0) 
				$destinatario = $destinatario_all;
			else{
				$sql_person = "SELECT name, email FROM tbperson WHERE status = 'A' AND idtypeperson IN (1,2,3) AND email != ''";
				$rs_person = $db->Execute($sql_person);
				if(!$rs_person) die("$lb Erro : " . $db->ErrorMsg());
				while (!$rs_person->EOF) {						
					$destinatario_all[$i] = array('name' => $rs_person->fields['name'], 'email' => $rs_person->fields['email']);
					$i++;
					$rs_person->MoveNext();
				}	
				$destinatario = $destinatario_all; 
			}			
		}else{
			//VERIFICA SE TEM RESTRIÇÕES PARA GRUPO DE ATENDENTES
			if($total_group > 0){
				$sql_groups = "select group_concat(idgroup separator ',') as groups from bbd_topic_group WHERE idtopic = $id_topic";
				$rs_groups = $db->Execute($sql_groups);
				if(!$rs_groups) die("$lb Erro : " . $db->ErrorMsg());
				$ids_groups = $rs_groups->fields['groups'];
				
				$sql_email_groups = "SELECT
									  pers.email,
									  pers.name
									FROM tbperson as pers,
									  tbperson as grpname,
									  hdk_tbgroup as grp,
									  hdk_tbgroup_has_person as pergrp
									WHERE pers.idperson = pergrp.idperson
									AND grp.idgroup = pergrp.idgroup
									AND grpname.idperson = grp.idperson
									AND grp.idgroup IN ($ids_groups)
									AND pers.email != ''
									GROUP BY pers.idperson";
				$rs_email_groups = $db->Execute($sql_email_groups);
				if(!$rs_email_groups) die("$lb Erro : " . $db->ErrorMsg());
				while (!$rs_email_groups->EOF) {						
					$destinatario[$i] = array('name' => $rs_email_groups->fields['name'], 'email' => $rs_email_groups->fields['email']);
					$i++;
					$rs_email_groups->MoveNext();
				}
			}else{ //PEGA TODOS ATENDENTES E ADMINS CASO NÃO TENHA RESTRIÇÃO
				$sql_person = "SELECT name, email FROM tbperson WHERE status = 'A' AND idtypeperson IN (1,3) AND email != ''";
				$rs_person = $db->Execute($sql_person);
				if(!$rs_person) die("$lb Erro : " . $db->ErrorMsg());
				while (!$rs_person->EOF) {						
					$destinatario[$i] = array('name' => $rs_person->fields['name'], 'email' => $rs_person->fields['email']);
					$i++;
					$rs_person->MoveNext();
				}
			}
			
			//VERIFICA SE TEM RESTRIÇÕES PARA EMPRESAS DE USUÁRIOS
			if($total_company > 0){
				$sql_company = "select group_concat(idcompany separator ',') as companies from bbd_topic_company WHERE idtopic = $id_topic";
				$rs_company = $db->Execute($sql_company);
				if(!$rs_company) die("$lb Erro : " . $db->ErrorMsg());
				$ids_company = $rs_company->fields['companies'];

				$sql_email_companies = "SELECT
										  pers.email,
										  pers.name
										FROM tbperson pers,
										     hdk_tbdepartment dep,
										     hdk_tbdepartment_has_person dep_p
										WHERE
										    dep.iddepartment = dep_p.iddepartment
										AND dep_p.idperson = pers.idperson
										AND dep.idperson IN ($ids_company)
										AND pers.idtypeperson = 2
										AND pers.email != ''
										GROUP BY pers.idperson";
				$rs_email_companies = $db->Execute($sql_email_companies);
				if(!$rs_email_companies) die("$lb Erro : " . $db->ErrorMsg());
				while (!$rs_email_companies->EOF) {						
					$destinatario[$i] = array('name' => $rs_email_companies->fields['name'], 'email' => $rs_email_companies->fields['email']);
					$i++;
					$rs_email_companies->MoveNext();
				}				
			}else{//PEGA TODOS DO TIPO USUÁRIO SE NÃO TIVER RESTRIÇÃO
				$sql_person = "SELECT name, email FROM tbperson WHERE status = 'A' AND idtypeperson = 2 AND email != ''";
				$rs_person = $db->Execute($sql_person);
				if(!$rs_person) die("$lb Erro : " . $db->ErrorMsg());
				while (!$rs_person->EOF) {						
					$destinatario[$i] = array('name' => $rs_person->fields['name'], 'email' => $rs_person->fields['email']);
					$i++;
					$rs_person->MoveNext();
				}
			}
	
		}
		
		$conteudo = "<h2>".$rs->fields['title_warning']."</h2>";
		$conteudo .= "<p>".$rs->fields['description']."</p>";		
		$mail->From = $mail_remetente; 
		$mail->FromName = $nom_titulo;
		if ($mail_host) $mail->Host = $mail_host;
		if (isset($mail_porta) AND !empty($mail_porta)) {$mail->Port = $mail_porta;}
		$mail->Mailer = $mail_metodo;
		$mail->SMTPAuth = $mail_auth;
		$mail->Username = $mail_username;
		$mail->Password = $mail_password;
		$mail->Body = $mail_cabecalho . $conteudo . $mail_rodape;
		$mail->AltBody = "HTML";
		$mail->Subject = utf8_decode($rs->fields['title_topic'].": ".$rs->fields['title_warning']);
		//$mail->AddBCC("brunocsouzaa@gmail.com","Bruno Souza");

		foreach($destinatario as $pessoa){
			$mail->AddBCC($pessoa['email'], $pessoa['name']); 
		}
		 
		$mail->SetLanguage('br', DOCUMENT_ROOT . "email/language/");

        $done = $mail->Send();        
        $mail->ClearAllRecipients();
        $mail->ClearAttachments();
		
		if($done){
			//COLOCA O AVISO COM A FLAG 1 PARA IDNTIFICAR QUE JÁ FOI ENVIADO OS EMAILS
			$sql_sent = "UPDATE bbd_tbmessage SET emailsent = 1 WHERE idmessage = $id";
			$db->Execute($sql_sent);
		}
		
		$rs->MoveNext();
	}
}


?>
