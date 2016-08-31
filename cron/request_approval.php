<?php
error_reporting(0);
if (substr(php_sapi_name(), 0, 3) != 'cli') { die("This program runs only in the command line"); }
set_time_limit(0);
$path_parts = pathinfo(dirname(__FILE__));
$cron_path = $path_parts['dirname'] ;
$lb = "\n";
include($cron_path . "/includes/config/config.php") ;
include($cron_path . "/includes/adodb/adodb.inc.php");



$db_connect = $config["db_connect"];
$db_hostname = $config["db_hostname"];
$db_username  =   $config["db_username"] ;
$db_password    =  $config["db_password"];
$db_name = $config["db_name"];
$db_sn = $config["db_sn"]    ;
$db_port   = $config["db_port"]	;




$db = NewADOConnection($db_connect);
/*
if (!$db->Connect($db_hostname, $db_username, $db_password, $db_name)) {
    die("<br>Erro ao conectar o banco de dados: " . $db->ErrorNo() . " - " . $db->ErrorMsg());
}
*/

if($db_connect == 'mysqlt'){
    if (!$db->Connect($db_hostname, $db_username, $db_password, $db_name)) {
        die("<br>Error connecting to database: " . $db->ErrorNo() . " - " . $db->ErrorMsg());
    }
}
elseif ($db_connect == 'oci8po'){
    $ora_db = "
						(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP) (HOST=".$db_hostname.")(PORT=".$db_port.")))
						(CONNECT_DATA=(SERVICE_NAME=".$db_sn.")))
					";
    if (!$db->Connect($ora_db, $db_username, $db_password)){
        die("<br>Error connecting to database: " . $$db->ErrorNo() . " - " . $$db->ErrorMsg() );
    }
}



$query=	"
		select
		   value
		from hdk_tbconfig
		where session_name = 'SES_QT_WORK_DAYS_REQUEST_APPROVAL'
		";
$rs = $db->Execute($query);
if(!$rs) die("$lb Erro : " . $db->ErrorMsg());

if ($rs->fields['value'] == 0 ) {
	exit;
} else {
	$days = $rs->fields['value'] ;
}

if($db_connect == 'mysqlt'){
	$sql =  "
		select
		   holiday_date
		from tbholiday
		where holiday_date >= date(now()) - INTERVAL $days DAY
				 and holiday_date < date(now())
        ";
}
elseif ($db_connect == 'oci8po'){
	$sql =  "
		select
		   holiday_date
		from tbholiday
		where holiday_date >= sysdate - (INTERVAL '$days' DAY)
				 and holiday_date < sysdate
        ";

}
$rs = $db->Execute($sql);
if(!$rs) die("$lb Erro : " . $db->ErrorMsg());

$holidays = array();
while (!$rs->EOF) { 
        array_push($holidays,$rs->fields['holiday_date']) ;
        $rs->MoveNext(); 
}

$date_test = get_before_work_date(date('Y-m-d'),$days,$holidays) ; 





if($db_connect == 'mysqlt'){
	$sql = "SELECT a.code_request, a.idstatus
			FROM hdk_tbrequest a
			WHERE a.idstatus = 4
			AND (SELECT date FROM hdk_tbrequest_log WHERE cod_request = a.code_request ORDER BY date DESC LIMIT 1) < '$date_test'";		
} elseif ($db_connect == 'oci8po'){
		$sql = "SELECT a.code_request, a.idstatus
				FROM hdk_tbrequest a, hdk_tbrequest_log b
				WHERE a.idstatus = 4
				  AND b.cod_request = a.code_request
				  AND B.date_ < to_date('$date_test','yyyy-mm-dd')
			group by  a.code_request, a.idstatus";		
}
$rs = $db->Execute($sql);
if(!$rs) die("$lb Erro : " . $db->ErrorMsg());

while (!$rs->EOF) { 

	$db->BeginTrans();
	$queryUp=	"
				UPDATE hdk_tbrequest
				SET idstatus = 5
				WHERE code_request = '".$rs->fields['code_request']."' 
				";	
				
	$rsUp = $db->Execute($queryUp);
	if(!$rsUp) {
		$db->RollbackTrans(); 
		die("$lb Erro : " . $db->ErrorMsg());
	}	
	if($db_connect == 'mysqlt'){
		$q=	"
			insert into hdk_tbrequest_log 
				( 
				cod_request, 
				date, 
				idstatus, 
				idperson 
				)
				values
				( 
				'".$rs->fields['code_request']."', 
				NOW(), 
				'5', 
				'1' 
				)	
			";	
	} elseif ($db_connect == 'oci8po'){
		$q=	"
			insert into hdk_tbrequest_log 
				( 
				cod_request, 
				date_, 
				idstatus, 
				idperson 
				)
				values
				( 
				'".$rs->fields['code_request']."', 
				sysdate, 
				'5', 
				'1' 
				)	
			";	
	}
	$rsIns = $db->Execute($q);
	if(!$rsIns) {
		$db->RollbackTrans(); 
		die("$lb Erro : " . $db->ErrorMsg());
	}	
	if($db_connect == 'mysqlt'){	
		$query=	"
			insert into hdk_tbnote 
				(
				code_request, 
				idperson, 
				description, 
				entry_date, 
				idtype 
				)
				values
				(
				'".$rs->fields['code_request']."', 
				'1', 
				'Aprovada automaticamente pelo sistema ap&oacute;s ".$days." dias !', 
				NOW(), 
				'4' 
				)
			";
	} elseif ($db_connect == 'oci8po'){
		$query=	"
			insert into hdk_tbnote 
				(
				code_request, 
				idperson, 
				description, 
				entry_date, 
				idtype 
				)
				values
				(
				'".$rs->fields['code_request']."', 
				'1', 
				'Aprovada automaticamente pelo sistema ap&oacute;s ".$days." dias !', 
				sysdate, 
				'4' 
				)
			";
	}
		$rsNote = $db->Execute($query);
		if(!$rsNote) {
			$db->RollbackTrans(); 
			die("$lb Erro : " . $db->ErrorMsg());
		}			
		$db->CommitTrans();	
        $rs->MoveNext(); 
}
		
function get_before_work_date($startDate,$days,$holidays)
{
	global $lb;
	$i = 1 ;
	$j = 1 ;
	while (true) {
		$date_test = date('Y-m-d', strtotime("-$i days",strtotime($startDate)));
		$i++; 
		if ( in_array($date_test,$holidays) 
			 or date("N",strtotime($date_test)) == 6 
			 or date("N",strtotime($date_test)) == 7
			) 
		{
			// print "feriado ou final de semana: " . $date_test . $lb ; 
		} 
		else 
		{
			$j++;
		}	
		if ($j > $days) 
		{
			return $date_test;		
		}
	}


}
?>
