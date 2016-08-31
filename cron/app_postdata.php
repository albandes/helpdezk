<?php
error_reporting(E_ERROR |  E_PARSE);
// "D:\Program Files\xampp\php\php.exe" D:\home\rogerio\htdocs\erp\cron\alt_actions.php
// C:\xampp\php\php.exe C:\xampp\htdocs\erp\cron\alt_actions.php

//if (substr(php_sapi_name(), 0, 3) != 'cli') { die("This program runs only in the command line"); }

$debug = false ;
$loga  = true ;
$year  = date('Y');

set_time_limit(0);
$path_parts = pathinfo(dirname(__FILE__));
$cron_path = $path_parts['dirname'] ;
$lb = "\n";

include $cron_path . '/api/db.php';
include($cron_path . "/includes/config/config.php") ;

$date_format = $config['date_format'];
$hour_format = $config['hour_format'];
$logfile = $cron_path . "/logs/api_postdata.log" ;


$print_date = str_replace("%","",$date_format) . " " . str_replace("%","",$hour_format);

if($loga) logit("[".date($print_date)."]" . " - Run cron/api_postdata.php - Start" , $logfile);

try {
    $db = getDB();
    $db->setFetchMode(ADODB_FETCH_ASSOC);
    $db->execute("SET CHARACTER SET utf8");
} catch(exception $e) {
    if($loga) logit("[".date($print_date)."]" . " - Error connecting to database: " . $db->ErrorMsg(), $logfile);
    die("$lb Erro : " . $db->ErrorMsg());
}


//
$content = http_build_query(array('key' => 'VYrx2VbhOglyentZSL5Z4KZl79MUOkAs'));
$context = stream_context_create(array(
    'http' => array(
        'method'  => 'POST',
        'content' => $content,
    )
));
$result = file_get_contents('http://intera.studio/reverse_api/emq/user/all', null, $context);
print $result . '<br>';
die('end');
//

$rsPerson = getPersonToPost('A','2016');

echo '<br><pre>';


while (!$rsPerson->EOF) {
    print $rsPerson->fields['login'] . '<br>' ;
    $rsPost = getPerson('A',$year,$rsPerson->fields['login'])   ;

    print_r($rsPost->fields) . "<br>";

    echo "<br>";

    $content = http_build_query(array(
        'key' => 'VYrx2VbhOglyentZSL5Z4KZl79MUOkAs',
        'name' => $rsPost->fields['name'],
        'email' => $rsPost->fields['email'],
        'password' => $rsPost->fields['password'],
        'gender' => $rsPost->fields['sexo'],
        'role' => 'A',
        'birthday' => $rsPost->fields['data_nascimento'],
        'matricula' => $rsPost->fields['matricula'],
        'avatar' => ''
    ));

    $context = stream_context_create(array(
        'http' => array(
            'method'  => 'POST',
            'content' => $content,
        )
    ));

    $result = file_get_contents('http://intera.studio/reverse_api/emq/user/new', null, $context);

    if($result) {

        print $result . '<br>';
        //$obj = json_decode($result);
        //print $obj->{'status'} . '<br>';
        //print $obj->{'mensagem'} . '<br>';

    } else {
        if($loga) logit("[".date($print_date)."]" . " - Erro, nao acessou a API, na linha: " . __LINE__ . ". ID: " . $rsPerson->fields['login'] . " - " . $rsPost->fields['name'] , $logfile);
    }

    $rsPerson->MoveNext();
}



if($loga) logit("[".date($print_date)."]" . " - Run cron/api_postdata.php - Stop" , $logfile);
exit ;



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
function getPersonToPost($flag,$year,$personID='ALL')
{
    global $db, $loga, $print_date, $logfile, $debug;

    $queryPerson = "
                    SELECT
                      Login login,
                      NoPessoa `name`,
                      Email email,
                      Senha `password`,
                      Sexo sexo,
                      (
                        CASE
                          CoTipoPessoa
                          WHEN 3
                          THEN 'P'
                          WHEN 2
                          THEN 'A'
                          WHEN 14
                          THEN 'A'
                          ELSE 'R'
                        END
                      ) role,
                      DATE_FORMAT(DtNascimento, '%d/%m/%Y') data_nascimento,
                      Login matricula
                    FROM
                      pessoa p,
                      turma_has_pessoa thp
                    WHERE thp.CoPessoa = p.CoPessoa
                      AND p.flag = '$flag'
                      AND thp.Ano = $year
                    ORDER BY p.NoPessoa limit 20
                " ;
    if($debug) logit("[".date($print_date)."]" . " - DEBUG: function " . __FUNCTION__ . ", line " . __LINE__  . ", query \n\n " . $queryPerson, $logfile);
    $rs = $db->Execute($queryPerson);
    if(!$rs) if($loga) logit("[".date($print_date)."]" . " - Erro: " . $db->ErrorMsg(). " SQL: " . $queryPerson, $logfile);
    return $rs ;
}


function getPerson($flag,$year,$personID='ALL')
{
    global $db, $loga, $print_date, $logfile, $debug;

    if ($personID == 'ALL') {
        $tmpQuery = '';
    } else {
        $tmpQuery = "AND Login = $personID" ;
    }

    $queryPerson = "
                    SELECT
                      Login login,
                      NoPessoa `name`,
                      Email email,
                      Senha `password`,
                      Sexo sexo,
                      (
                        CASE
                          CoTipoPessoa
                          WHEN 3
                          THEN 'P'
                          WHEN 2
                          THEN 'A'
                          WHEN 14
                          THEN 'A'
                          ELSE 'R'
                        END
                      ) role,
                      DATE_FORMAT(DtNascimento, '%d/%m/%Y') data_nascimento,
                      Login matricula,
                      'KblHJ2Be5acZphYK5nqEVPPkI3QFFg7O' AS `key`
                    FROM
                      pessoa p,
                      turma_has_pessoa thp
                    WHERE thp.CoPessoa = p.CoPessoa
                      AND p.flag = '$flag'
                      AND thp.Ano = $year
                      $tmpQuery
                " ;
    if($debug) logit("[".date($print_date)."]" . " - DEBUG: function " . __FUNCTION__ . ", line " . __LINE__  . ", query \n\n " . $queryPerson, $logfile);
    $rs=$db->Execute($queryPerson);
    if(!$rs) if($loga) logit("[".date($print_date)."]" . " - Erro: " . $db->ErrorMsg(). " SQL: " . $queryPerson, $logfile);
    return $rs ;
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
