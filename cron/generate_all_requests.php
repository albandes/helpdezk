<?php
/**
 ** C:\xampp\php\php -f D:\home\rogerio\www\hd\cron\generate_all_requests.php 
 ** F:\xampp\php\php -f F:\home\rogerio\htdocs\hd\cron\generate_all_requests.php 
 **/
if (substr(php_sapi_name(), 0, 3) != 'cli') { die("This program runs only in the command line"); }
$path_parts = pathinfo(dirname(__FILE__));
$cron_path = $path_parts['dirname'] ;
$lb = "\n";
include($cron_path . "/includes/config/config.php") ;
include($cron_path . "/includes/adodb/adodb.inc.php");

//$limit = "LIMIT 10";

$db = NewADOConnection('mysqli');
if (!$db->Connect($config["db_hostname"] , $config["db_username"] , $config["db_password"], $config["db_name"])) {  die("$lb Database Error : " . $db->ErrorNo() . " - " . $db->ErrorMsg()); }

$header  = "Codigo,Empresa,Assunto,Data_Abertura,Mes_Abertura,Data_Vencimento,Data_Encerramento,Mes_Data_Encerramento,";
$header .= "Solicitante,Departamento_do_Solicitante,Responsavel,Departamento_do_Responsavel,Auxiliares,Prioridade,Status,";
$header .= "Area,Tipo,Item,Servico,Avaliacao,Sla,Tempo_de_Atendimento,Data_Rejeicao,Tipo_Atd,Data_Repassado,Ultimo_Repassador,Data_Encerrado_Atendente" ;


$meses = array('Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');


$sql = 	"
		select
		   idevaluation,
		   name
		from hdk_tbevaluation
		";
$rs = $db->Execute($sql);
if(!$rs) die("$lb Erro : " . $db->ErrorMsg() . $lb . $sql);
$evaluation = array();
while (!$rs->EOF) {
	$evaluation[$rs->fields['idevaluation']] = $rs->fields['name'] ;
	$rs->MoveNext();
}


for ($i = 2011; $i <= date("Y"); $i++) {

	//$file = $cron_path . "/cron/".date("Ymd") . "_" . $i . ".txt" ;
	$file = "/tmp/".date("Ymd")  . "_" . $i . ".txt";
	file_put_contents($file, $header.$lb);

	$query=	"
			select
			   a.code_request as Codigo,
			   b.name         as Empresa,
			   a.subject      as Assunto,
			   DATE_FORMAT(a.entry_date, '%d/%m/%Y') as Data_Abertura,
			   DATE_FORMAT(a.entry_date, '%m') as Mes_Abertura,
			   DATE_FORMAT(a.expire_date, '%d/%m/%Y') as Data_Vencimento,
			   a.expire_date as Data_Tempo_Vencimento,
			   (select
				   DATE_FORMAT(date, '%d/%m/%Y')
				from hdk_tbrequest_log
				where cod_request = a.code_request
					 and idstatus = 5
				group by a.code_request) as Data_Encerramento,
			   (select
				   DATE_FORMAT(date, '%m/%Y')
				from hdk_tbrequest_log
				where cod_request = a.code_request
					 and idstatus = 5
				group by a.code_request) as Mes_Data_Encerramento,
			   percre.name    as Solicitante,
			   m.name         as Departamento_do_Solicitante,
			   res.name       as Responsavel,
			   (SELECT a.name FROM hdk_tbdepartment a, hdk_tbdepartment_has_person b WHERE a.iddepartment = b.iddepartment AND b.idperson = res.idperson) as Departamento_do_Responsavel,
			   e.name         as Prioridade,
			   f.idstatus     as IdStatus,
			   f.name         as Status,
			   g.name         as Area,
			   h.name         as Tipo,
			   i.name         as Item,
			   j.name         as Servico,
			   l.way          as Tipo_Atd,
			   (select
				   GROUP_CONCAT(aux_pes.name)
				from hdk_tbrequest_in_charge aux_cha,
				   tbperson aux_pes
				where aux_cha.ind_operator_aux = 1
					 and aux_cha.code_request = a.code_request
					 and aux_cha.id_in_charge = aux_pes.idperson) as Auxiliares,
			   (select
				   if(s_log.date < s_req.expire_date,'Cumprido','Nao cumprido')
				from hdk_tbrequest_log s_log,
				   hdk_tbrequest s_req
				where s_log.idstatus = 5
					 and s_req.code_request = a.code_request
					 and s_log.cod_request = s_req.code_request
				group by s_log.cod_request) as Sla,				
				(SELECT date 
					FROM hdk_tbrequest_log logreq 
					WHERE idstatus = 4
					AND logreq.cod_request = a.code_request
					ORDER BY date DESC LIMIT 1) as UltimaAguardAprov,
			   (select
				   DATE_FORMAT(date, '%d/%m/%Y')
				from hdk_tbrequest_log
				where cod_request = a.code_request
					 and idstatus = 6
				group by a.code_request) as Data_Rejeicao,
				(select
					   sec_to_time(ceiling((sum(t.minutes) * 60)))
					from hdk_tbnote t
					where t.code_request = a.code_request) as Tempo_de_Atendimento,
			    (
				SELECT GROUP_CONCAT(eva.idevaluation) 
					FROM hdk_tbrequest_evaluation eva
					where eva.code_request = a.code_request
				) as idevaluation,
				(SELECT DATE_FORMAT(date, '%d/%m/%Y') 
				FROM hdk_tbrequest_log 
				where cod_request = a.code_request
				and idstatus = 2
				ORDER BY date DESC
				LIMIT 1) as Data_Repassado,
				(SELECT tbperson.name
				FROM hdk_tbrequest_log, tbperson
				WHERE hdk_tbrequest_log.cod_request = a.code_request   
				AND hdk_tbrequest_log.idperson = tbperson.idperson
				AND hdk_tbrequest_log.idstatus = 2
				ORDER BY hdk_tbrequest_log.id DESC LIMIT 1) as Ultimo_Repassador					
			from hdk_tbrequest a
			   left join hdk_tbattendance_way l
				 on a.idattendance_way = l.idattendanceway,
			   tbperson b,
			   tbperson percre,
			   hdk_tbdepartment m,
			   hdk_tbdepartment_has_person n,
			   hdk_tbrequest_in_charge d,
			   tbperson res,
			   hdk_tbpriority e,
			   hdk_tbstatus f,
			   hdk_tbcore_area g,
			   hdk_tbcore_type h,
			   hdk_tbcore_item i,
			   hdk_tbcore_service j
			where a.idperson_juridical = b.idperson
				 and a.idperson_owner = percre.idperson
				 and n.idperson = a.idperson_owner
				 and m.iddepartment = n.iddepartment
				 and a.code_request = d.code_request
				 and d.ind_in_charge = 1
				 and d.id_in_charge = res.idperson
				 and a.idpriority = e.idpriority
				 and a.idstatus = f.idstatus
				 and a.idtype = h.idtype
				 and g.idarea = h.idarea
				 and a.iditem = i.iditem
				 and a.idservice = j.idservice
				 and year(a.entry_date) = '$i'
				$limit				
			";

	$rs = $db->Execute($query);
	if(!$rs) die("$lb Erro : " . $db->ErrorMsg() . $lb . $query);
	
	while (!$rs->EOF) {
		
		if($rs->fields['IdStatus'] == 4 || $rs->fields['IdStatus'] == 5){
			if(strtotime($rs->fields['UltimaAguardAprov']) > strtotime($rs->fields['Data_Tempo_Vencimento'])){
				$sla = "Nao cumprido";
			}else{
				$sla = "Cumprido";
			}
		}else{
			$sla = $rs->fields['Sla'];
		}	
		
		
		
		if($rs->fields['idevaluation']){
			$tpEval = $evaluation[$rs->fields['idevaluation']]; 
		}
		else{
			$tpEval = "";
		}
		
		if($rs->fields['UltimaAguardAprov'])
			$ultima_data_aprovacao = date("d/m/Y", strtotime($rs->fields['UltimaAguardAprov']));
		else
			$ultima_data_aprovacao = ""; 
		
		$linha  = 	"\"" . $rs->fields['Codigo'] . "\","  ;
		$linha .=   "\"" . utf8_decode($rs->fields['Empresa']) . "\"," ;
		$linha .= 	"\"" . str_replace(",", "", utf8_decode($rs->fields['Assunto'])) . "\"," ;
		$linha .= 	$rs->fields['Data_Abertura'] . "," ;
		$linha .=  	"\"" . utf8_decode($meses[$rs->fields['Mes_Abertura']-1]) . "\"," ;
		$linha .= 	$rs->fields['Data_Vencimento'] . "," ;
		$linha .=  	$rs->fields['Data_Encerramento'] . "," ;
		$linha .=  	$rs->fields['Mes_Data_Encerramento'] . "," ;
		$linha .=  	"\"" . utf8_decode($rs->fields['Solicitante']) . "\"," ;
		$linha .=  	"\"" . utf8_decode($rs->fields['Departamento_do_Solicitante']) . "\"," ;
		$linha .=  	"\"" . utf8_decode($rs->fields['Responsavel']) . "\"," ;
		$linha .=  	"\"" . utf8_decode($rs->fields['Departamento_do_Responsavel']) . "\"," ;
		$linha .=  	"\"" . utf8_decode($rs->fields['Auxiliares']) . "\"," ;
		$linha .=  	"\"" . utf8_decode($rs->fields['Prioridade']) . "\"," ;
		$linha .=  	"\"" . utf8_decode($rs->fields['Status']) . "\"," ;
		$linha .=  	"\"" . utf8_decode($rs->fields['Area']) . "\"," ;
		$linha .=  	"\"" . utf8_decode($rs->fields['Tipo']) . "\"," ;
		$linha .=  	"\"" . utf8_decode($rs->fields['Item']) . "\"," ;
		$linha .=  	"\"" . utf8_decode($rs->fields['Servico']) . "\"," ;
		//$linha .=  	"\"" . GetEvaluation($rs->fields['Codigo']) . "\"," ;
		$linha .=   "\"" .  $tpEval . "\"," ;
		$linha .=  	"\"".$sla . "\"," ;
		$linha .=  	$rs->fields['Tempo_de_Atendimento'] . "," ;
		$linha .=  	$rs->fields['Data_Rejeicao'] . "," ;
		$linha .=  	"\"" . utf8_decode($rs->fields['Tipo_Atd']) . "\",";
		$linha .= 	$rs->fields['Data_Repassado']. "," ;
		$linha .=  	"\"" . utf8_decode($rs->fields['Ultimo_Repassador']) . "\",";
		$linha .=  	"\"" . $ultima_data_aprovacao . "\"";		
		$linha .= 	$lb;
		
		
		
		file_put_contents($file, $linha, FILE_APPEND);
		
		$rs->MoveNext();
	}
}

function GetEvaluation ($id) 
{
	global $lb;
	global $db;
	$sql = 	"
			SELECT distinct
			   eva.name
			FROM hdk_tbrequest req
			   left JOIN hdk_tbrequest_evaluation reqeval
				 ON req.code_request = reqeval.code_request,
			   hdk_tbevaluation eva
			where req.code_request = $id
				 and eva.idevaluation = reqeval.idevaluation
			";
	$rs_eval = $db->Execute($sql);
	if(!$rs_eval) die("$lb Erro : " . $db->ErrorMsg() . $lb . $sql);
	if ($rs_eval->RecordCount() == 0) {
		return " ";
	} else {	
		return utf8_decode($rs_eval->fields['name']);
	}
	/*
   (SELECT distinct
	   eva.name
	FROM hdk_tbrequest req
	   left JOIN hdk_tbrequest_evaluation reqeval
		 ON req.code_request = reqeval.code_request,
	   hdk_tbevaluation eva
	where req.code_request = a.code_request
		 and eva.idevaluation = reqeval.idevaluation) as Avaliacao,	
	*/
		
}
?>
