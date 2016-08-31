<?php
if (substr(php_sapi_name(), 0, 3) != 'cli') { die("This program runs only in the command line"); }

/* Configs */
$lb = "\n";
$loga = true ;
$debug_screen = true ;
error_reporting(1);
set_time_limit(0);
$path_parts = pathinfo(dirname(__FILE__));
$cron_path = $path_parts['dirname'] ;
$logfile = $cron_path . "/logs/dash_stats.log" ;
/* ------- */

/* Includes */
include($cron_path . "/includes/config/config.php") ;
include($cron_path . "/includes/adodb/adodb.inc.php");
/* -------- */
$lang_default = $config['lang'];
$date_format = $config['date_format'];
$hour_format = $config['hour_format'];
$db_hostname =  $config["db_hostname"];
$db_port = $config["db_port"];
$db_username = $config["db_username"];
$db_password = $config["db_password"];
$db_name = $config["db_name"];

$langVars = GetLangVars($cron_path,$lang_default) ;
$print_date = str_replace("%","",$date_format) . " " . str_replace("%","",$hour_format);

$db = NewADOConnection('mysqli');
if ($db_port) {
	$db_server = $db_hostname.":" . $db_port ;
} else {
	$db_server = $db_hostname;
}
if (!$db->Connect( $db_server, $db_username , $db_password, $db_name)) {  
	die("$lb Database Error : " . $db->ErrorNo() . " - " . $db->ErrorMsg()); 
}

if($loga) logit("[".date($print_date)."]" . " - Run cron/dashboard_stats.php" , $logfile); 	

$sql =	"
		select
		   a.code_request as Codigo,
		   a.idstatus,
		   a.expire_date,
		   (select
			   if(s_log.date < s_req.expire_date,'SLA_IN','SLA_OUT')
			from hdk_tbrequest_log s_log,
			   hdk_tbrequest s_req
			where s_log.idstatus = 5
				 and s_req.code_request = a.code_request
				 and s_log.cod_request = s_req.code_request
			group by s_log.cod_request) as Sla,
		   (SELECT
			   date
			FROM hdk_tbrequest_log logreq
			WHERE idstatus = 4
				 AND logreq.cod_request = a.code_request
			ORDER BY date DESC
			LIMIT 1) as UltimaAguardAprov
		from hdk_tbrequest a
		";
		
$rs = $db->Execute($sql) or die($db->ErrorMsg());

$in  = 0 ;
$out = 0 ;
$tot = 0;

while (!$rs->EOF)		
{
		
		if($rs->fields['idstatus'] == 4 || $rs->fields['idstatus'] == 5){
			if(strtotime($rs->fields['UltimaAguardAprov']) > strtotime($rs->fields['expire_date'])){
				$sla = "SLA_OUT";
			}else{
				$sla = "SLA_IN";
			}
		}else{
			$sla = $rs->fields['Sla'];
		}	
		
		$sla = $rs->fields['Sla'];
		if ($sla == "SLA_IN") {
			$in++;
		} elseif($sla == "SLA_OUT") {
			$out++;
		}		
		$tot++;	
	$rs->MoveNext();
}


$sql = 	"DELETE FROM dsh_tbsla";	
$rs = $db->Execute($sql) or die($db->ErrorMsg());

$sql = 	"
		insert into dsh_tbsla
					(
					 intime,
					 outoftime,
					 datetimeupdate)
		values (
				'$in',
				'$out',
				NOW());
		";	

$rs = $db->Execute($sql) or die($db->ErrorMsg());
exit;

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
