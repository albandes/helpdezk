<?

class cron_mail{ 
	
	
	public function sendEmail($operation, $code_request, $db) {
		include '../includes/config/config.php';
		$path_default = $config['path_default'];
		$hdk_url = $config['hdk_url'];
				
		$document_root=$_SERVER['DOCUMENT_ROOT'];
		if(substr($document_root, -1)!='/'){
		    $document_root=$document_root.'/';
		}        
		define('DOCUMENT_ROOT',$document_root);
		
		if(substr($path_default, 0,1)!='/'){
		    $path_default='/'.$path_default;
		}
		if ($path_default == "/..") {   
			define('path',"");
		} else {
		    define('path',$path_default);
		}
		
        if (!isset($operation)) {
            print("Email code not provided");
            return false;
        }
		
        $destinatario = "";
        //## ENVIA E-MAIL PARA O GRUPO AO REGISTRAR UMA SOLICITACAO ##===
        switch ($operation) {
            case "record":

                $COD_RECORD = "16"; // Esse é o padrão
				$SQL = "select name, description from hdk_tbtemplate_email where idtemplate = '$COD_RECORD'";
				$rsTemplate = $db->Execute($SQL) or die($db->ErrorMsg());
				
                $SQL = "select
						  req.code_request,
						  req.expire_date,
						  req.entry_date,
						  req.flag_opened,
						  req.subject,
						  req.idperson_owner,
						  req.idperson_creator,
						  cre.name               AS name_creator,
						  cre.phone_number       AS phone_number,
						  cre.cel_phone          AS cel_phone,
						  cre.branch_number      AS branch_number,
						  req.idperson_juridical as idcompany,
						  req.idsource,
						  req.extensions_number,
						  source.name            as source,
						  req.idstatus,
						  req.idattendance_way,
						  req.os_number,
						  req.serial_number,
						  req.label,
						  req.description,
						  comp.name              as company,
						  stat.user_view         as `status`,
						  rtype.name             as `type`,
						  rtype.idtype,
						  item.iditem,
						  item.name,
						  serv.idservice,
						  serv.name              as service,
						  prio.name              as priority,
						  prio.idpriority,
						  inch.ind_in_charge,
						  inch.id_in_charge,
						  resp.name              as in_charge,
						  prio.color,
						  pers.name              as personname,
						  pers.email,
						  pers.phone_number      as phone,
						  pers.branch_number     as branch,
						  inch.type              as typeincharge,
						  dep.name               as department,
						  dep.iddepartment,
						  source.name,
						  are.idarea
						FROM (hdk_tbrequest req,
						   tbperson pers,
						   tbperson comp,
						   tbperson resp,
						   tbperson cre,
						   hdk_tbdepartment as dep,
						   hdk_tbcore_type rtype,
						   hdk_tbcore_service serv,
						   hdk_tbcore_area are,
						   hdk_tbpriority prio,
						   hdk_tbcore_item item,
						   hdk_tbstatus stat,
						   hdk_tbsource as source,
						   hdk_tbdepartment_has_person as dep_pers,
						   hdk_tbrequest_in_charge as inch)
						  left join hdk_tbreason as reason
						    on (req.idreason = reason.idreason)
						  left join hdk_tbgroup as grp
						    on (resp.idperson = grp.idperson)
						where req.idperson_owner = pers.idperson
						    AND req.idperson_creator = cre.idperson
						    and req.idstatus = stat.idstatus
						    AND req.idperson_juridical = comp.idperson
						    AND req.idtype = rtype.idtype
						    AND req.idservice = serv.idservice
						    AND req.idpriority = prio.idpriority
						    AND req.idsource = source.idsource
						    AND req.code_request = inch.code_request
						    AND req.iditem = item.iditem
						    AND dep.iddepartment = dep_pers.iddepartment
						    AND pers.idperson = dep_pers.idperson
						    AND are.idarea = rtype.idarea
						    AND inch.id_in_charge = resp.idperson
						    AND inch.ind_in_charge = 1
						    AND req.code_request = '$code_request'";
				
				$reqdata = $db->Execute($SQL) or die($db->ErrorMsg());

                $REQUEST = $code_request;
                $SUBJECT = $reqdata->fields['subject'];
                $REQUESTER = $reqdata->fields['personname'];
                $RECORD = $this->formatDate($reqdata->fields['entry_date'], $db);
                $DESCRIPTION = $reqdata->fields['description'];
                $INCHARGE = $reqdata->fields['in_charge'];
                $PHONE = $reqdata->fields['phone'];
                $BRANCH = $reqdata->fields['branch'];
				$LINK_OPERATOR = "<a href='".$hdk_url."helpdezk/operator#/operator/viewrequest/id/".$code_request."' target='_blank'>".$code_request."</a>";
				$LINK_USER = "<a href='".$hdk_url."helpdezk/user#/user/viewrequest/id/".$code_request."' target='_blank'>".$code_request."</a>";
								
				$SQL = "select
						  nt.idnote,
						  pers.idperson,
						  pers.name,
						  nt.description,
						  nt.entry_date,
						  nt.minutes,
						  nt.start_hour,
						  nt.finish_hour,
						  nt.execution_date,
						  nt.public,
						  nt.idtype,
						  nt.idnote_attachment,
						  nt.ip_adress,
						  nt.callback,
						  nta.file_name,
						  TIME_FORMAT(TIMEDIFF(nt.finish_hour,nt.start_hour), '%Hh %imin %ss') AS diferenca
						from (hdk_tbnote as nt,
						   tbperson as pers)
						  left join hdk_tbnote_attachment as nta
						    on (nta.idnote_attachment = nt.idnote_attachment)
						where code_request = '$code_request'
						    and pers.idperson = nt.idperson
						order by idnote desc";
				
				$notes = $db->Execute($SQL) or die($db->ErrorMsg());
                //$notes = $bdop->getRequestNotes($code_request);

                $table = "<table width='100%'  border='0' cellspacing='3' cellpadding='0'>";
                while (!$notes->EOF) {
                    $table.= "<tr><td height=28><font size=2 face=arial>";
                    $table.= $this->formatDate($notes->fields['entry_date'],$db) . " [" . $notes->fields["name"] . "] " . str_replace(chr(10), "<BR>", strip_tags($notes->fields["description"]));
                    $table.= "</font><br></td></tr>";
                    $notes->MoveNext();
                }
                $table.= "</table>";

                $NT_OPERATOR = $table;

                //           ---------------------------------------------------------------------

                $conteudo = str_replace(chr(10), "<br>", $rsTemplate->fields("description"));
                $conteudo = str_replace('"', "'", $rsTemplate->fields("description")) . "<br/>";
                eval("\$conteudo = \"$conteudo\";");
				
				$SQL = "select id_in_charge, type from hdk_tbrequest_in_charge where ind_in_charge = 1 and code_request = '$code_request'";
				$rsGroup = $db->Execute($SQL) or die($db->ErrorMsg());
                $inchType = $rsGroup->fields['type'];
                $inchid = $rsGroup->fields['id_in_charge'];

                if ($inchType == 'G') {
					$SQL = "select
							  pers.email,
							  grpname.name
							from tbperson as pers,
							  tbperson as grpname,
							  hdk_tbgroup as grp,
							  hdk_tbgroup_has_person as pergrp
							where pers.idperson = pergrp.idperson
							AND pers.status = 'A'
							and grp.idgroup = pergrp.idgroup
							AND grpname.idperson = grp.idperson
							and grpname.idperson = '$inchid'";
					$grpEmails = $db->Execute($SQL) or die($db->ErrorMsg());
                    while (!$grpEmails->EOF) {
                        if (!$destinatario) {
                            $destinatario = $grpEmails->Fields('email');
                        } else {
                            $destinatario .= ";" . $grpEmails->Fields('email');
                        }
                        $grpEmails->MoveNext();
                    }
                } else {
					$SQL = "select
							  pers.email
							from tbperson as pers
							where pers.idperson = '$inchid'";
					$userEmail = $db->Execute($SQL) or die($db->ErrorMsg());
                    $destinatario = $userEmail->Fields('email');
                }

                $assunto = $rsTemplate->fields['name'];
                eval("\$assunto = \"$assunto\";");
                

                break;			
			case 'operator_note' :

                $COD_ASSUME = "43";
				$SQL = "select name, description from hdk_tbtemplate_email where idtemplate = '$COD_ASSUME'";
				$rsTemplate = $db->Execute($SQL) or die($db->ErrorMsg());

                $SQL = "select
						  req.code_request,
						  req.expire_date,
						  req.entry_date,
						  req.flag_opened,
						  req.subject,
						  req.idperson_owner,
						  req.idperson_creator,
						  cre.name               AS name_creator,
						  cre.phone_number       AS phone_number,
						  cre.cel_phone          AS cel_phone,
						  cre.branch_number      AS branch_number,
						  req.idperson_juridical as idcompany,
						  req.idsource,
						  req.extensions_number,
						  source.name            as source,
						  req.idstatus,
						  req.idattendance_way,
						  req.os_number,
						  req.serial_number,
						  req.label,
						  req.description,
						  comp.name              as company,
						  stat.user_view         as `status`,
						  rtype.name             as `type`,
						  rtype.idtype,
						  item.iditem,
						  item.name,
						  serv.idservice,
						  serv.name              as service,
						  prio.name              as priority,
						  prio.idpriority,
						  inch.ind_in_charge,
						  inch.id_in_charge,
						  resp.name              as in_charge,
						  prio.color,
						  pers.name              as personname,
						  pers.email,
						  pers.phone_number      as phone,
						  pers.branch_number     as branch,
						  inch.type              as typeincharge,
						  dep.name               as department,
						  dep.iddepartment,
						  source.name,
						  are.idarea
						FROM (hdk_tbrequest req,
						   tbperson pers,
						   tbperson comp,
						   tbperson resp,
						   tbperson cre,
						   hdk_tbdepartment as dep,
						   hdk_tbcore_type rtype,
						   hdk_tbcore_service serv,
						   hdk_tbcore_area are,
						   hdk_tbpriority prio,
						   hdk_tbcore_item item,
						   hdk_tbstatus stat,
						   hdk_tbsource as source,
						   hdk_tbdepartment_has_person as dep_pers,
						   hdk_tbrequest_in_charge as inch)
						  left join hdk_tbreason as reason
						    on (req.idreason = reason.idreason)
						  left join hdk_tbgroup as grp
						    on (resp.idperson = grp.idperson)
						where req.idperson_owner = pers.idperson
						    AND req.idperson_creator = cre.idperson
						    and req.idstatus = stat.idstatus
						    AND req.idperson_juridical = comp.idperson
						    AND req.idtype = rtype.idtype
						    AND req.idservice = serv.idservice
						    AND req.idpriority = prio.idpriority
						    AND req.idsource = source.idsource
						    AND req.code_request = inch.code_request
						    AND req.iditem = item.iditem
						    AND dep.iddepartment = dep_pers.iddepartment
						    AND pers.idperson = dep_pers.idperson
						    AND are.idarea = rtype.idarea
						    AND inch.id_in_charge = resp.idperson
						    AND inch.ind_in_charge = 1
						    AND req.code_request = '$code_request'";				
				$reqdata = $db->Execute($SQL) or die($db->ErrorMsg());

                $REQUEST = $code_request;
                $SUBJECT = $reqdata->fields['subject'];
                $REQUESTER = $reqdata->fields['personname'];
                $RECORD = $this->formatDate($reqdata->fields['entry_date'],$db);
                $DESCRIPTION = $reqdata->fields['description'];
                $INCHARGE = $reqdata->fields['in_charge'];
                $PHONE = $reqdata->fields['phone'];
                $BRANCH = $reqdata->fields['branch'];
                $date = date('Y-m-d H:i');
                $FINISH_DATE = $this->formatDate($date,$db);
				$LINK_OPERATOR = "<a href='".$hdk_url."helpdezk/operator#/operator/viewrequest/id/".$code_request."' target='_blank'>".$code_request."</a>";
				$LINK_USER = "<a href='".$hdk_url."helpdezk/user#/user/viewrequest/id/".$code_request."' target='_blank'>".$code_request."</a>";
												
				$SQL = "select
						  nt.idnote,
						  pers.idperson,
						  pers.name,
						  nt.description,
						  nt.entry_date,
						  nt.minutes,
						  nt.start_hour,
						  nt.finish_hour,
						  nt.execution_date,
						  nt.public,
						  nt.idtype,
						  nt.idnote_attachment,
						  nt.ip_adress,
						  nt.callback,
						  nta.file_name,
						  TIME_FORMAT(TIMEDIFF(nt.finish_hour,nt.start_hour), '%Hh %imin %ss') AS diferenca
						from (hdk_tbnote as nt,
						   tbperson as pers)
						  left join hdk_tbnote_attachment as nta
						    on (nta.idnote_attachment = nt.idnote_attachment)
						where code_request = '$code_request'
						    and pers.idperson = nt.idperson
						order by idnote desc";
				$notes = $db->Execute($SQL) or die($db->ErrorMsg());
				
                $table = "<table width='100%'  border='0' cellspacing='3' cellpadding='0'>";
                while (!$notes->EOF) {
                    $table.= "<tr><td height=28><font size=2 face=arial>";
                    $table.= $this->formatDate($notes->fields['entry_date'],$db) . " [" . $notes->fields["name"] . "] " . str_replace(chr(10), "<BR>", strip_tags($notes->fields["description"]));
                    $table.= "</font><br></td></tr>";
                    $notes->MoveNext();
                }
                $table.= "</table>";

                $NT_USER = $table;

                $conteudo = str_replace(chr(10), "<br>", $rsTemplate->fields("description"));
                $conteudo = str_replace('"', "'", $rsTemplate->fields("description")) . "<br/>";
                eval("\$conteudo = \"$conteudo\";");


				$SQL = "select id_in_charge, type from hdk_tbrequest_in_charge where ind_in_charge = 1 and code_request = '$code_request'";
                $rsGroup = $db->Execute($SQL) or die($db->ErrorMsg());
                $inchType = $rsGroup->fields['type'];
                $inchid = $rsGroup->fields['id_in_charge'];

                if ($inchType == 'G') {
                	$SQL = "select
								  pers.email,
								  grpname.name
								from tbperson as pers,
								  tbperson as grpname,
								  hdk_tbgroup as grp,
								  hdk_tbgroup_has_person as pergrp
								where pers.idperson = pergrp.idperson
								AND pers.status = 'A'
								and grp.idgroup = pergrp.idgroup
								AND grpname.idperson = grp.idperson
								and grpname.idperson = '$inchid'";
					$grpEmails = $db->Execute($SQL) or die($db->ErrorMsg());
                    while (!$grpEmails->EOF) {
                        if (!$destinatario) {
                            $destinatario = $grpEmails->Fields('email');
                        } else {
                            $destinatario .= ";" . $grpEmails->Fields('email');
                        }
                        $grpEmails->MoveNext();
                    }
                } else {

					$SQL = "select
							  pers.email
							from tbperson as pers
							where pers.idperson = '$inchid'";
					$userEmail = $db->Execute($SQL) or die($db->ErrorMsg());
                    $destinatario = $userEmail->fields['email'];
                }

                $assunto = $rsTemplate->Fields('name');
                eval("\$assunto = \"$assunto\";");
                break;
		
		}
        		
		$SQL = "select session_name,value from hdk_tbconfig where idconfigcategory = 5";
		$conf = $db->Execute($SQL) or die($db->ErrorMsg());
		while (!$conf->EOF) {
            $ses = $conf->fields['session_name'];
            $val = $conf->fields['value'];
            $emailConfs[$ses] = $val;
            $conf->MoveNext();
        }
		$emconfigs = $emailConfs;
		
		$SQL = "select session_name,description as value from hdk_tbconfig where idconfigcategory = 11";
        $conf = $db->Execute($SQL) or die($db->ErrorMsg());
		while (!$conf->EOF) {
            $ses = $conf->fields['session_name'];
            $val = $conf->fields['value'];
            $tempConfs[$ses] = $val;
            $conf->MoveNext();
        }
        $tempconfs = $tempConfs;
		
        $nom_titulo = $emconfigs['EM_TITLE'];
        $mail_metodo = 'smtp';
        $mail_host = $emconfigs['EM_HOSTNAME'];
        $mail_dominio = $emconfigs['EM_DOMAIN'];
        $mail_auth = $emconfigs['EM_AUTH'];
        $mail_username = $emconfigs['EM_USER'];
        $mail_password = $emconfigs['EM_PASSWORD'];
        $mail_remetente = $emconfigs['EM_SENDER'];
        $mail_cabecalho = $tempconfs['EM_HEADER'];
        $mail_rodape = $tempconfs['EM_FOOTER'];		
		

        require_once("../includes/classes/phpMailer/class.phpmailer.php");
        $mail = new phpmailer();
        $mail->From = $mail_remetente;
        $mail->FromName = $nom_titulo;
        if ($mail_host)
            $mail->Host = $mail_host;
        if (isset($mail_porta) AND !empty($mail_porta)) {
            $mail->Port = $mail_porta;
        }
        $mail->Mailer = $mail_metodo;
        $mail->SMTPAuth = $mail_auth;
        $mail->Username = $mail_username;
        $mail->Password = $mail_password;
        $mail->Body = $mail_cabecalho . $conteudo . $mail_rodape;
        $mail->AltBody = "HTML";
        $mail->Subject = utf8_decode($assunto);
        
        //$mail->Send();
        
        //Verifica se há mais de 1 endereço de email no destinatario 
        $jaExiste = array();
        if (preg_match("/;/", $destinatario)) {
            $email_destino = explode(";", $destinatario);
            if (is_array($email_destino)) {
                for ($i = 0; $i < count($email_destino); $i++) {
                    // Se o endereço de e-mail NÃO estiver no array, envia e-mail e coloca no array
                    // Se já tiver no array, não envia novamente, evitando mails duplicados
                    if (!in_array($email_destino[$i], $jaExiste)) {
                        $mail->AddAddress($email_destino[$i]);
                        $jaExiste[] = $email_destino[$i];
                        //echo $email_destino[$i] . "<br>";
                    }
                }
            } else {
                $mail->AddAddress($email_destino);
            }
        } else {
            $mail->AddAddress($destinatario);
        }
        $mail->SetLanguage('br', DOCUMENT_ROOT . "email/language/");

        $done = $mail->Send();
        
        $mail->ClearAllRecipients();
        $mail->ClearAttachments();

        if (!$done) { //algum debug dos erros            
            $mail->SMTPDebug = true;
            $mail->Send();
			
			$file = fopen('../logs/email_failures.txt', 'ab');
            
            if ($file) {
                $msg = date("Y-m-d H:i:s");
                $msg .= " " . $mail->ErrorInfo;
                $msg .= " CODE REQUEST = $REQUEST AND OPERATION = $operation\r\n";
                fwrite($file, $msg);
                fclose($file);
            }            
        }
    } 
	

	
	public function formatDate($date, $db) {
        include '../includes/config/config.php';
		$date_format = $config['date_format'];
		$SQL = "SELECT DATE_FORMAT('$date','$date_format') as date";
		$rsDate = $db->Execute($SQL) or die($db->ErrorMsg());
        return $rsDate->fields['date'];
    }
	
}

