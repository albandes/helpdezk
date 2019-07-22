<?php
error_reporting(E_ERROR |  E_PARSE);
set_time_limit(3600);
$url = 'http://reverse.marioquintana.com.br/reverse/mq/public/api';
$key = 'vyNYXAz9fPZ8OR6bOzL1IL7BiU8RgxQ5';
$loga  = true ;
$debug = false;
$logfile = 'app_postdata.log';
$year = 2019;

//include $cron_path . 'db.php';
include 'db.php';
date_default_timezone_set('Brazil/East');

$db = getDataBase();

//delReversePerson('36161993015');
//exit;


if($loga) logit("[".date('d/M/Y H:i:s')."]" . " - Run cron/api_postdata.php " , $logfile);

//$type = 'list_all';
//$type = 'delete_all';
$type = 'write_all';


switch ($type) {
    case 'list_all':
			$aJsonData = getReversePerson();
			if(empty ($aJsonData)){
				echo "Sem usuarios para listar !!!" ;
				if($loga) logit("[".date('d/M/Y H:i:s')."]" . " - api_postdata.php - Sem usuarios parar listar !!" , $logfile);
			} else {
				foreach ($aJsonData as $keyArr => $value) {
					echo $value->login . ' - ' . $value->name . '<br>';
				}  
			}
			echo 'Total: ' . count($aJsonData);
        break;
	case 'write_all':
			// Ensino Medio
			//$andTurma1 = 'AND e.CoCurso = 2';
			//$andTurma2 = 'AND t.CoCurso = 2'; 
			
			//Ensino fundamental a partir do 4 ano
			//$andTurma1 = 'AND (e.CoCurso = 1 AND e.Serie > 3)';
			//$andTurma2 = 'AND (t.CoCurso = 1 AND t.Serie > 3)'; 

			//$andTurma1 = 'AND ((e.CoCurso = 1 AND e.Serie <= 3) OR e.CoCurso = 3)';
			//$andTurma2 = 'AND ((t.CoCurso = 1 AND t.Serie <= 3) OR t.CoCurso = 3)' ;
			
			//Geral
			$andTurma1 = 'AND (e.CoCurso IN (1,2,3))';
			$andTurma2 = 'AND (t.CoCurso IN (1,2,3))'; 
			
			$rsPersonPost = getPersonToPost('A',$year,'ALL', $andTurma1,$andTurma2);
			while (!$rsPersonPost->EOF) {
				print 'Incluindo: '. $rsPersonPost->fields['login'] . ' - ' . $rsPersonPost->fields['login'] . '<br>' ;
				$ret = putReversePerson($rsPersonPost);
				$arrMess = getReverseReturn($ret);
				if($loga) logit("[".date('d/M/Y H:i:s')."]" . " - Add user: " . $rsPersonPost->fields['login'] . " - " . $arrMess['message'] , $logfile);
				print_r($arrMess);
				echo "<br> -- <br>";
				$rsPersonPost->MoveNext();
			}

			echo 'total: '. $rsPersonPost->RecordCount();
		break;
	case 'delete_all':
			$aJsonData = getReversePerson();
			if(empty ($aJsonData)){
				echo "Sem usuarios para listar !!!" ;
				if($loga) logit("[".date('d/M/Y H:i:s')."]" . " - api_postdata.php - Sem usuarios parar listar !!" , $logfile);
			} else {
				foreach ($aJsonData as $keyArr => $value) {
						$ret = delReversePerson($value->login);
						$arrMess = getReverseReturn($ret);
						if($loga) logit("[".date('d/M/Y H:i:s')."]" . " - Delete user: " . $value->login . " - " . $arrMess['message'] , $logfile);
						echo 'Deletado: '. $value->login . ' - ' . $value->name . '<br>';
				}  
			}

		break;
}


echo '<br>';
die();



/*
 *  Por curso   
 *  $andTurma1 = 'AND e.CoCurso = 1';
 *  $andTurma2 = 'AND t.CoCurso = 1'; 
 *  $personID = ''; 
 *
 *  Por Serie
 *  $andTurma1 = 'AND (e.CoCurso = 2 AND e.Serie = 2)';
 *  $andTurma2 = 'AND (t.CoCurso = 2 AND t.Serie = 2)'; 
 *  $personID = '';
 *  
 *  Por Aluno
 *  $andTurma1 = '';
 *  $andTurma2 = ''; 
 *  $personID = '';
 * 
 */



/*
 * FUNCTIONS
 */
function putReversePerson($rs)
{
	global $url, $key ;
	if (empty($rs->fields['email'])) {
		$rs->fields['login'].'@teste.com'; 
	} else {
		$email = $rs->fields['email']; 
	}	

	$arrFilhos = explode(',',$rs->fields['filhos']);
	

    $content = array(
        'key' => $key,
		'login' => $rs->fields['login'],
        'name' => $rs->fields['name'],
        'email' => $email,
        'sexo' => $rs->fields['sexo'],
        'role' => $rs->fields['role'],
        'data_nascimento' => $rs->fields['data_nascimento'],
        'matricula' => $rs->fields['matricula'],
        'avatar' => '',
		'filhos' =>  $arrFilhos,
    );


	
    $ctx = http_build_query($content);
	$sUrl = $url. '/user/new' ;
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $sUrl);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $ctx);

	//$result = curl_exec($ch);
	$content = trim(curl_exec($ch));
	

	curl_close($ch);

    if(!$content) {
        if($loga) logit("[".date('d/M/Y H:i:s')."]" . " - Erro, nao acessou a API, na linha: " . __LINE__ . ". ID: " . $rs->fields['login'] . " - " . $rsPost->fields['name'] , $logfile);
	    $result = '{"status":"erro","message":"Sem acesso a API"} ';	
    }
	return $content ;	
}

function getReverseReturn($ret)
{
	$newRet = substr($ret, 0, -1) . "}]" ;
	$ret = "[{" . substr($newRet, 1, strlen($newRet)) ;
	$ret = json_decode($ret);
	if ($ret[0]->status == 'ok'){
		
		$aReturn = array(
						'status'  => $ret[0]->status,
						'message' => 
							  $ret[0]->message
						);
						
	} elseif ($ret[0]->status == 'erro') {
		if (is_array($ret[0]->message)) {
			foreach ($ret[0]->message as $value) {
				$msg = $msg . ' ' . $value;
			}
		} else {
			$msg = $ret[0]->message;		
		}
		
		$aReturn = array(
						'status'  => $ret[0]->status,
						'message' =>  $msg 
							 
						);
		
	}
	return $aReturn ;
}

function delReversePerson($login)
{
	global $url, $key ;
	return get_req($url. '/user/delete/'.$login.'/'.$key);
}

function getReversePerson() 
{
	global $url, $key ;
	return  json_decode(file_get_contents($url. '/user/all/'.$key));
	 
}

function getDataBase()
{
	try {
		$db = getDB();
		$db->setFetchMode(ADODB_FETCH_ASSOC);
		$db->execute("SET CHARACTER SET utf8");
		return $db;
	} catch(exception $e) {
		if($loga) logit("[".date('d/M/Y H:i:s')."]" . " - Error connecting to database: " . $db->ErrorMsg(), $logfile);
		die("$lb Erro : " . $db->ErrorMsg());
	}
}

function getPersonToPost($flag,$year,$personID='ALL',$andTurmas1='',$andTurmas2='')
{
    global $db, $loga, $logfile, $debug;

	$andCpf   = ($personID == "ALL" ? ''  : "AND d.cpf = '$personID'") ;
	$andLogin = ($personID == "ALL" ? ''  : "AND p.Login IN ($personID)") ;
	
    $queryPerson = "
					SELECT a.idresponsavel,cpf login, nome `name`, d.email, d.sexo, 'R' AS role, 
						DATE_FORMAT(d.data_nascimento,'%d/%m/%Y') data_nascimento, '' AS matricula,
						GROUP_CONCAT(c.Login)  filhos
					FROM pessoa_has_tbresponsavel a, turma_has_pessoa b, pessoa c, tbresponsavel d, turma e
					WHERE a.CoPessoa = c.CoPessoa
					AND a.idresponsavel = d.idresponsavel
					AND b.CoPessoa = c.CoPessoa
					AND b.CoTurma = e.CoTurma
					AND b.Ano = $year
					AND c.flag = 'A'
					AND a.acessa_portalapp = 'S'
					AND d.status = 'A'
					AND (d.cpf IS NOT NULL AND d.cpf != '' 
						AND d.cpf NOT IN('00000000000','11111111111','22222222222','33333333333','44444444444','55555555555',
						'66666666666','77777777777','88888888888','99999999999') AND LENGTH(TRIM(cpf)) = 11)
					$andCpf
					$andTurmas1
					GROUP BY a.idresponsavel

					UNION

					SELECT p.CoPessoa, Login login, NoPessoa `name`, email, Sexo sexo, 'A' AS role,
						DATE_FORMAT(p.DtNascimento,'%d/%m/%Y') data_nascimento, Login AS matricula,
						'' filhos
					FROM turma_has_pessoa thp, pessoa p, turma t
					WHERE thp.CoPessoa = p.CoPessoa
					AND thp.CoTurma = t.CoTurma
					AND thp.Ano = $year
					AND p.flag = '$flag'
					$andLogin
					$andTurmas2
					
					" ;
					
    if($debug) logit("[".date('d/M/Y H:i:s')."]" . " - DEBUG: function " . __FUNCTION__ . ", line " . __LINE__  . ", query \n\n " . $queryPerson, $logfile);
    $rs = $db->Execute($queryPerson);
    if(!$rs) if($loga) logit("[".date('d/M/Y H:i:s')."]" . " - Erro: " . $db->ErrorMsg(). " SQL: " . $queryPerson, $logfile);
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

function get_req($url) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, True);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl,CURLOPT_USERAGENT,'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:7.0.1) Gecko/20100101 Firefox/7.0.1');
    $return = curl_exec($curl);
    curl_close($curl);
    return $return;
}
?>
