<?php
error_reporting(0);
// "D:\Program Files\xampp\php\php.exe" D:\home\rogerio\htdocs\erp\cron\alt_actions.php
// C:\xampp\php\php.exe C:\xampp\htdocs\erp\cron\alt_actions.php

if (substr(php_sapi_name(), 0, 3) != 'cli') { die("This program runs only in the command line"); }

$debug = true ;
$loga  = true ;

set_time_limit(0);
$path_parts = pathinfo(dirname(__FILE__));
$cron_path = $path_parts['dirname'] ;
$lb = "\n";
include($cron_path . "/includes/config/config.php") ;
include($cron_path . "/includes/adodb/adodb.inc.php");

$date_format = $config['date_format'];
$hour_format = $config['hour_format'];
$logfile = $cron_path . "/logs/alt_action.log" ;

$langVars = GetLangVars($cron_path,$lang_default) ;
$print_date = str_replace("%","",$date_format) . " " . str_replace("%","",$hour_format);

if($loga) logit("[".date($print_date)."]" . " - Run cron/alt_actions.php - Start" , $logfile);

$db_connect     = $config["db_connect"];
$db_hostname    = $config["db_hostname"];
$db_username    = $config["db_username"] ;
$db_password    = $config["db_password"];
$db_name        = $config["db_name"];
$db_sn          = $config["db_sn"]    ;
$db_port        = $config["db_port"]	;

$db = NewADOConnection($db_connect);

if($db_connect == 'mysqlt'){
    if (!$db->Connect($db_hostname, $db_username, $db_password, $db_name)) {
        die("<br>Error connecting to database: " . $db->ErrorNo() . " - " . $db->ErrorMsg());
    }
}

$q =    "
        SELECT
          a.idmodule,
          a.idaction,
          b.idcondiction,
          b.table,
          b.column,
          b.value,
          b.variable,
          b.operator
        FROM
          alt_tbaction a,
          alt_tbcondiction b
        WHERE a.idaction = b.idaction
        AND
          a.status = 'A'
        ORDER BY a.idmodule,a.idaction,b.idaction,b.idcondiction ASC
        ";

if($debug) logit("[".date($print_date)."]" . "DEBUG: line " . __LINE__  . ", query \n\n " . $q, $logfile);

$rs = $db->Execute($q);
if(!$rs) {
    if($loga) logit("[".date($print_date)."]" . " - Erro: " . $db->ErrorMsg(). " SQL: " . $q, $logfile);
    die("$lb Erro : " . $db->ErrorMsg());
}

if ($rs->RecordCount() == 0 ) {
    if($loga) logit("[".date($print_date)."]" . " - No alerts configurated !!!" , $logfile);
    if($loga) logit("[".date($print_date)."]" . " - Run cron/alt_actions.php - Finish" , $logfile);
    die('No alerts configurated !!!');
}

require_once($cron_path . "/includes/classes/phpMailer/class.phpmailer.php");
$mail = new phpmailer();

$first      = true;
$firstClause= true ;
$aClause    = array();
$i          = 0 ;

while (!$rs->EOF) {
    // Helpdezk
    //if ($rs->fields['idmodule'] == 2)
    //{
        if($first) {
            $first = false ;
            $action = $rs->fields['idaction'] ;
        }

        if ($action == $rs->fields['idaction'])
        {
            array_push($aClause,$rs->fields['operator']) ;
            if ($firstClause) {
                $clause = "WHERE " ;
                $firstClause = false;
            } else {
                $clause = ' ' . $aClause[$i-1] . ' ';
            }
            $operators .= $clause . $rs->fields['table'] . "." . $rs->fields['column'] . '=' . "'". $rs->fields['value'] . "'";
        }
        else
        {
            $queryHelpdezk = makeQuery($rs->fields['idmodule'], $operators) ;
            if($debug) logit("[".date($print_date)."]" . "DEBUG: function " . __FUNCTION__ . ", line " . __LINE__  . ", query \n\n " . $queryHelpdezk, $logfile);

            $rsAction = checkAction($action, $queryHelpdezk);
            if ($rsAction->RecordCount() > 0) {
                //
                sendAlert($action,$rsAction) ;
                //
                if ($rsAction->RecordCount() > 1) {
                    while (!$rsAction->EOF) {
                        if ($rs->fields['idmodule'] == 2) {
                            $id = $rsAction->fields['idrequest'] ;
                        }
                        writeExecuted($action,$id) ;
                        $rsAction->MoveNext();
                    }
                } else {
                    if ($rs->fields['idmodule'] == 2) {
                        $id = $rsAction->fields['idrequest'] ;
                    }
                    writeExecuted($action,$id) ;
                }
                print "OK -> " ;
            } else {
                if($loga) logit("[".date($print_date)."]" . " No results for action number:  " . $action . ". " , $logfile);
            }
            //

            $action = $rs->fields['idaction'] ;
            $firstClause = true ;
            $aClause = array();
            $i = 0 ;

            $operators = '';
            array_push($aClause,$rs->fields['operator']) ;
            if ($firstClause) {
                $clause = "WHERE " ;
                $firstClause = false;
            } else {
                $clause = ' ' . $aClause[$i-1] . ' ';
            }
            $operators .= $clause . $rs->fields['table'] . "." . $rs->fields['column'] . '=' . "'". $rs->fields['value'] . "'";


        }
        $i++;
    //}
    $module = $rs->fields['idmodule'] ;
    $rs->MoveNext();
    if($rs->EOF) {
        $queryHelpdezk = makeQuery($module, $operators) ;
        if($debug) logit("[".date($print_date)."]" . "DEBUG: line " . __LINE__  . ", query \n\n " . $queryHelpdezk, $logfile);
        $rsAction = checkAction($action, $queryHelpdezk);
        if ($rsAction->RecordCount() > 0) {
            //
            sendAlert($action,$rsAction) ;
            //
            if ($rsAction->RecordCount() > 1) {
                while (!$rsAction->EOF) {
                    if ($module == 2) {
                        $id = $rsAction->fields['idrequest'] ;
                    }
                    writeExecuted($action,$id) ;
                    $rsAction->MoveNext();
                }
            } else {
                if ($module == 2) {
                    $id = $rsAction->fields['idrequest'] ;
                }
                writeExecuted($action,$id) ;
            }

            print "OK [last] " ;
        } else {
            if($loga) logit("[".date($print_date)."]" . " No results for action number:  " . $action . ". " , $logfile);
        }


    }

}

if($loga) logit("[".date($print_date)."]" . " - Run cron/alt_actions.php - Finish" , $logfile);

die('Performed: ' . $rs->RecordCount() . " condictions .") ;

/*
 * FUNCTIONS
 */


function checkAction($id,$query)
{
    global $db,$loga, $print_date, $logfile;
    $rs = $db->Execute($query);
    if(!$rs) if($loga) logit("[".date($print_date)."]" . " - Erro: " . $db->ErrorMsg(). " SQL: " . $q, $logfile);
    return $rs ;
}

function sendAlert($action,$rsAction)
{
    global $db, $loga,$print_date, $logfile , $debug;

    $q = "
        SELECT
           a.idmidiatype,
           b.name,
           b.idmodule
        FROM
           alt_tbaction_has_midia a,
           alt_tbaction b
        WHERE a.idaction = '$action'
           AND a.idaction = b.idaction
         ";

    if($debug) logit("[".date($print_date)."]" . "DEBUG: function " . __FUNCTION__ . ", line " . __LINE__  . ", query \n\n " . $q, $logfile);

    $rs = $db->Execute($q);

    if(!$rs) if($loga) logit("[".date($print_date)."]" . " - Erro: " . $db->ErrorMsg(). " SQL: " . $q, $logfile);
    if ($rs->RecordCount() == 0 )  {
        if($loga) logit("[".date($print_date)."]" . " No return values of alt_tbaction_has_midia for action number " . $action . " !!!", $logfile);
        return ;
    }

    while (!$rs->EOF) {

        $aConfig  = getMidiaConfig($rs->fields['idmidiatype']) ;

        if($rs->fields['idmodule'] == 2) { // Helpdezk
            $vars = array (
                '$action_name' 			=> $rs->fields['name'],
                '$request_code' 		=> $rsAction->fields['code_request'],
                '$request_subject'		=> $rsAction->fields['subject'],
                '$request_description'	=> $rsAction->fields['description'],
                '$entry_date'			=> $rsAction->fields['entry_date_fmt'],
                '$expire_date'			=> $rsAction->fields['expire_date_fmt'],
                '$incharge'			    => $rsAction->fields['incharge'],
                '$creator'			    => $rsAction->fields['creator'],
                '$area_name'			=> $rsAction->fields['area_name'],
                '$type_name'			=> $rsAction->fields['type_name'],
                '$item_name'			=> $rsAction->fields['item_name'],
                '$service_name'			=> $rsAction->fields['service_name']
            );
        }

        if ($rs->fields['idmidiatype'] == 3) {
            $rsContent = getMidiaContent($action, 3);
            $data_base[0]['body'] = $rsContent->fields['body'] ;
            $body = strtr($data_base[0]['body'], $vars);

            $rsSendoTo = getSendTo($action,3) ;
            while (!$rsSendoTo->EOF) {
                $ret = sendBulletNote($rsSendoTo->fields[name],$rsContent->fields['subject'],$body ,$aConfig['token']);
                if($loga) logit("[".date($print_date)."]" . " Send alert for action number:  " . $action . ". " , $logfile);
                if($debug) logit("[".date($print_date)."]" . "DEBUG: function " . __FUNCTION__ . ", line " . __LINE__  . ", return PushBullet: \n\n " . $ret, $logfile);
                $rsSendoTo->MoveNext();
            }
        }

        if ($rs->fields['idmidiatype'] == 1) {
            $rsContent = getMidiaContent($action, 1);

            $data_base[0]['subj'] = $rsContent->fields['subject'] ;
            $subject = strtr($data_base[0]['subj'], $vars);

            $data_base[0]['body'] = $rsContent->fields['body'] ;
            $body = strtr($data_base[0]['body'], $vars);

            $rsSendoTo = getSendTo($action,1) ;
            var_dump($rsSendoTo->fields) ;
            while (!$rsSendoTo->EOF) {
                sendEmail($subject, $body,$rsSendoTo->fields['name'],$aConfig);
                $rsSendoTo->MoveNext();
            }
        }
        $rs->MoveNext();

    }

}

function sendEmail($subject, $body, $sendTo, $aConfig)
{
    global $loga, $logfile, $debug, $print_date, $mail ;

    if (!function_exists('imap_open')){
        if($loga) logit("[".date($print_date)."]" . " IMAP functions are not available ."  , $logfile);
        return;
    }

    $mail->From = $aConfig['sender'];
    $mail->FromName = $aConfig['sender'];
    if ($aConfig['hostname']) $mail->Host = $aConfig['hostname'];
    if (isset($aConfig['port']) AND !empty($aConfig['port']) ) {$mail->Port = $aConfig['port'];}

    $mail->IsSMTP();
    $mail->SMTPAuth = $aConfig['auth'];
    $mail->IsHTML(true);

    $mail->Username = $aConfig['user'];
    $mail->Password = $aConfig['password'];
    $mail->Body = $body;

    $mail->Subject = utf8_decode($subject);

    // Check if have more emails
    $emailTemp = trim($sendTo);
    if(strstr($emailTemp,",")) {
        $dest = explode(",", $emailTemp);
        $first = true ;
        foreach ($dest as $destinatario) {
            if ($first) {
                $mail->AddAddress($destinatario);
                $first = false;
            } else {
                $mail->AddCC($destinatario);
            }
        }
    }
    else {
        $mail->AddAddress($emailTemp);
    }
    //

    if(!$mail->send()) {
        if($loga) logit("[".date($print_date)."]" . " Error sending e-mail: " . $mail->ErrorInfo . " ." , $logfile);
    } else {
        if($loga) logit("[".date($print_date)."]" . " E-mail has been sent to: " . $sendTo . " ." , $logfile);
        echo '';
    }

}

function writeExecuted($idaction, $idtableaffected)
{
    global $db,$loga, $print_date, $logfile, $debug;
    $q =    "
            INSERT INTO alt_tbexecuted (
              idaction,
              idtableaffected
            )
            VALUES
              (
                '$idaction',
                '$idtableaffected'
              )
            ";
    if($debug) logit("[".date($print_date)."]" . "DEBUG: function " . __FUNCTION__ . ", line " . __LINE__  . ", query \n\n " . $q, $logfile);
    $rsExec = $db->Execute($q);
    if(!$rsExec) if($loga) logit("[".date($print_date)."]" . " - Erro: " . $db->ErrorMsg(). " SQL: " . $q, $logfile);
    return ;
}

function getSendTo($action,$midiatype)
{
    global $db, $loga, $print_date, $logfile, $debug;

    $qSendTo = "
                SELECT
                   `name`
                FROM
                   alt_tbsendto
                WHERE idaction = $action
                   AND idmidiatype = $midiatype
                " ;
    if($debug) logit("[".date($print_date)."]" . "DEBUG: function " . __FUNCTION__ . ", line " . __LINE__  . ", query \n\n " . $qSendTo, $logfile);
    $rsSendTo = $db->Execute($qSendTo);
    if(!$rsSendTo) if($loga) logit("[".date($print_date)."]" . " - Erro: " . $db->ErrorMsg(). " SQL: " . $qSendTo, $logfile);
    return $rsSendTo ;
}

function GetMidiaContent($action,$midiatype)
{
    global $db,$loga,$print_date,$logfile,$debug;

    $qContent = "
                SELECT
                   idaction,
                   idmidiatype,
                   `subject`,
                   body
                FROM
                   alt_tbmidiacontent
                WHERE idaction = $action
                   AND idmidiatype = $midiatype
                " ;
    if($debug) logit("[".date($print_date)."]" . "DEBUG: function " . __FUNCTION__ . ", line " . __LINE__  . ", query \n\n " . $qContent, $logfile);
    $rsContent = $db->Execute($qContent);
    if(!$rsContent) if($loga) logit("[".date($print_date)."]" . " - Erro: " . $db->ErrorMsg(). " SQL: " . $qContent, $logfile);
    return $rsContent ;
}

function getMidiaConfig($idmidiatype)
{
    global $db,$loga,$print_date,$logfile,$debug;
    $qConfig =  "
                SELECT
                   name,
                   `value`
                FROM
                   alt_tbmidiaconfig a
                WHERE a.idmidiatype = $idmidiatype
                " ;

    if($debug) logit("[".date($print_date)."]" . "DEBUG: function " . __FUNCTION__ . ", line " . __LINE__  . ", query \n\n " . $qConfig, $logfile);

    $rsConfig = $db->Execute($qConfig);
    if(!$rsConfig) if($loga) logit("[".date($print_date)."]" . " - Erro: " . $db->ErrorMsg(). " SQL: " . $qConfig, $logfile);

    $aConfig = array();
    while (!$rsConfig->EOF){
        $aConfig[$rsConfig->fields['name']] = $rsConfig->fields['value'] ;
        $rsConfig->MoveNext();
    }

    return $aConfig ;
}

function sendBulletNote($email,$title, $body,$token)
{
    global $debug, $logfile;

    $pushdata = array(
        "email"     => $email,
        "type"      => "note",
        "title"     => $title,
        "body"      => $body

    );

    $options = array(
        'http' => array(
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n"."Authorization: Bearer $token\r\n",
            'method'  => 'POST',
            'content' => http_build_query($pushdata),
        ),
    );
    $context  = stream_context_create($options);
    $result = file_get_contents("https://api.pushbullet.com/v2/pushes", false, $context, -1, 40000);
    if($debug) logit("[".date($print_date)."]" . "DEBUG: function " . __FUNCTION__ . ", line " . __LINE__  . ", retunr \n\n " . $result, $logfile);
    return $result ;

}

function makeQuery ($module, $operators)
{
    global $date_format, $hour_format;
    $mysql_format = $date_format . " " . $hour_format ;

    if ($module == 2) {
        $queryHelpdezk =  "
                    SELECT
                       (SELECT `name` FROM tbperson WHERE tbperson.idperson = hdk_tbrequest.idperson_creator) AS  creator,
                       (SELECT `name` FROM tbperson WHERE tbperson.idperson = hdk_tbrequest_in_charge.id_in_charge) AS  incharge,
                       (SELECT `name` FROM hdk_tbcore_type WHERE hdk_tbrequest.idtype=hdk_tbcore_type.idtype) AS type_name,
                       (SELECT `name` FROM hdk_tbcore_item WHERE hdk_tbrequest.iditem=hdk_tbcore_item.iditem) AS item_name,
                       (SELECT `name` FROM hdk_tbcore_service WHERE hdk_tbrequest.idservice=hdk_tbcore_service.idservice) AS service_name,
                           (SELECT
                               a.name
                            FROM
                               hdk_tbcore_area a,
                               hdk_tbcore_type b
                            WHERE a.idarea = b.idarea
                               AND b.idtype = hdk_tbrequest.idtype ) AS area_name,
                       (SELECT `name` FROM hdk_tbpriority WHERE idpriority = hdk_tbrequest.idpriority) AS priority_name,
                       hdk_tbrequest_in_charge.*,
                       hdk_tbrequest.*,
					   DATE_FORMAT(hdk_tbrequest.entry_date,'$mysql_format') AS entry_date_fmt,
					   DATE_FORMAT(hdk_tbrequest.expire_date,'$mysql_format') AS expire_date_fmt
                    FROM
                      hdk_tbrequest_in_charge,
                      tbperson,
                      hdk_tbrequest
                    ". $operators  ."
                    AND hdk_tbrequest_in_charge.id_in_charge = tbperson.idperson
                    AND hdk_tbrequest_in_charge.code_request = hdk_tbrequest.code_request
                    AND hdk_tbrequest.idrequest NOT IN (SELECT
                                                           idtableaffected
                                                        FROM
                                                           alt_tbexecuted
                                                        WHERE idtableaffected = hdk_tbrequest.idrequest )
                  ";

        return $queryHelpdezk ;
    }
}

function GetLangVars($cron_path,$lang_default)
{
    require_once($cron_path . "/includes/Smarty/Smarty.class.php");
    $smarty = new Smarty;
    $smarty->debugging = true;
    $smarty->compile_dir = $cron_path . "/system/templates_c/";
    $smarty->config_load($cron_path . '/app/lang/' . $lang_default . '.txt', $lang_default);
    $smarty->assign('lang', $lang_default);
    $langVars = $smarty->get_config_vars();
    $smarty = NULL ;
    return $langVars;
}

function logit($str, $file)
{
    if (!file_exists($file)) {
        if($fp = fopen($file, 'a')) {
            @fclose($fp);
            return logit($str, $file);
        } else {
            return false;
        }
    }
    if (is_writable($file)) {
        $str = time().'	'.$str;
        $handle = fopen($file, "a+");
        fwrite($handle, $str."\r\n");
        fclose($handle);
        return true;
    } else {
        return false;
    }
}

?>
