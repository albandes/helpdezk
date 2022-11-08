<?php
session_start();
error_reporting(0);
require_once('../../includes/config/config.php');
require_once('../../includes/adodb/adodb.inc.php');
require_once('../../includes/Smarty/Smarty.class.php');
require_once('../../system/system.php');
include_once('classes/ProtectSql/ProtectSql.php');


$ProtectSql = new sqlinj;
$ProtectSql->start("aio","all");

$hdk_url = $config['hdk_url'] ;
$path_default = $config['path_default'];
$lang_default = $config['lang'] ;

$document_root = $_SERVER['DOCUMENT_ROOT'];
if (substr($document_root, -1) != '/') {
    $document_root = $document_root . '/';
}

$smarty = new Smarty;
$smarty->debugging = true;

if(substr($hdk_url ,-1)!='/'){
    $hdk_url = $hdk_url .'/';
}



if(substr($path_default, 0,1)!='/'){
    $path_default='/'.$path_default;
}

if ($path_default == "/..") {   
	$path_root = "";
} else {
    $path_root = $path_default;
}


if (!isset($_SESSION['SES_COD_USUARIO'])) {
	echo '<META HTTP-EQUIV="Refresh" Content="0; URL='.$hdk_url . 'admin/login">';	
}

$smarty->compile_dir = $document_root . $path_root . "/system/templates_c/";
$smarty->config_load($document_root . $path_root . '/app/lang/' . $lang_default . '.txt', $lang_default);

$a_lang = $smarty->get_config_vars();


/*
 *   Variables 
 */
$title 		= utf8_decode($a_lang['PDF_request_report']);
$page 		= utf8_decode($a_lang['PDF_Page']);
$CelHeight	= 5;
/* ---------------------------------------------------------------------------------------- */

$leftmargin = 10;


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


$sql_logo = "select name, height, width, file_name from tblogos where name = 'reports'";
$rs_logo = $db->Execute($sql_logo);
if(!$rs_logo) die(" Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $db->ErrorMsg());


$logo       = "/app/uploads/logos/".$rs_logo->fields['file_name'];
$height     = $rs_logo->fields['height'];
$width      = $rs_logo->fields['width'];

$logo = str_replace('//','/',$document_root . $path_root . $logo );

if ($db_connect == 'oci8po'){
    $SQL = "
SELECT req.code_request,
       to_char (req.expire_date, 'DD/MM/YYYY HH24:MI') AS expire_date,
       TO_CHAR (req.entry_date, 'DD/MM/YYYY HH24:MI') AS entry_date,
       req.flag_opened,
       req.subject,
       req.idperson_owner,
       req.idperson_creator,
       req.idperson_juridical AS idcompany,
       req.idsource,
       req.extensions_number,
       source.name AS source,
       req.idstatus,
       (SELECT reason
          FROM hdk_tbreason
         WHERE idreason = req.idreason)
          AS reason,
       (SELECT way
          FROM hdk_tbattendance_way
         WHERE idattendanceway = req.idattendance_way)
          AS way_name,
       req.os_number,
       req.serial_number,
       req.description,
       comp.name AS company,
       stat.user_view AS status,
       rtype.name AS TYPE,
       rtype.idtype,
       item.iditem,
       item.name AS item_name,
       serv.idservice,
       serv.name AS service,
       prio.name AS priority,
       prio.idpriority,
       inch.ind_in_charge,
       inch.id_in_charge,
       resp.name AS in_charge,
       prio.color,
       pers.name AS personname,
       pers.phone_number AS phone_number,
       pers.cel_phone AS cel_phone,
       pers.branch_number AS branch_number,
       pers.email,
       pers.phone_number AS phone,
       pers.branch_number AS branch,
       inch.TYPE AS typeincharge,
       dep.name AS department,
       dep.iddepartment,
       source.name,
       are.idarea,
       are.name AS area_name
  FROM hdk_tbrequest req,
       tbperson pers,
       tbperson comp,
       tbperson resp,
       hdk_tbdepartment dep,
       hdk_tbcore_type rtype,
       hdk_tbcore_service serv,
       hdk_tbcore_area are,
       hdk_tbpriority prio,
       hdk_tbcore_item item,
       hdk_tbstatus stat,
       hdk_tbsource source,
       hdk_tbdepartment_has_person dep_pers,
       hdk_tbrequest_in_charge inch,
       hdk_tbreason reason,
       hdk_tbgroup grp
 WHERE     req.idperson_owner = pers.idperson
       AND req.idreason = reason.idreason(+)
       AND resp.idperson = grp.idperson(+)
       AND req.idstatus = stat.idstatus
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
       AND req.code_request =  '".$_GET['code_request']."'
		";
}else{

    $SQL = "
		select
		   req.code_request,
		   DATE_FORMAT(req.expire_date, '%d/%m/%Y %H:%i') AS expire_date,
		   DATE_FORMAT(req.entry_date, '%d/%m/%Y %H:%i') AS entry_date,
		   req.flag_opened,
		   req.subject,
		   req.idperson_owner,
		   req.idperson_creator,
		   req.idperson_juridical as idcompany,
		   req.idsource,
		   req.extensions_number,
		   source.name            as source,
		   req.idstatus,
		   (select
			   reason
			from hdk_tbreason
			where idreason = req.idreason) as reason,
		   (select
			   way
			from hdk_tbattendance_way
			where idattendanceway = req.idattendance_way) as way_name,
		   req.os_number,
		   req.serial_number,
		   req.description,
		   comp.name              as company,
		   stat.user_view         as `status`,
		   rtype.name             as `type`,
		   rtype.idtype,
		   item.iditem,
		   item.name		  	  AS item_name,
		   serv.idservice,
		   serv.name              as service,
		   prio.name              as priority,
		   prio.idpriority,
		   inch.ind_in_charge,
		   inch.id_in_charge,
		   resp.name              as in_charge,
		   prio.color,
		   pers.name              as personname,
		   pers.phone_number       AS phone_number,
		   pers.cel_phone          AS cel_phone,
		   pers.branch_number      AS branch_number,
		   pers.email,
		   pers.phone_number      as phone,
		   pers.branch_number     as branch,
		   inch.type              as typeincharge,
		   dep.name               as department,
		   dep.iddepartment,
		   source.name,
		   are.idarea,
		   are.name 		 as area_name
		FROM (hdk_tbrequest req,
			tbperson pers,
			tbperson comp,
			tbperson resp,
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
			 AND req.code_request = '".$_GET['code_request']."'
		";
}
$rs = $db->Execute($SQL);
if (!$rs)
    die($SQL. " Arq: " . __FILE__ . " Line: " . __LINE__ . "<br>DB ERROR: " . $db->ErrorMsg());

require_once('../../includes/classes/fpdf/' . 'fpdf.php');

class PDF extends FPDF {
	var $widths;
	var $aligns;

	/** 
	 ** Functions to decode HTML
	 **	
	 **/	
	function WriteHTML($html)
	{
		$html = preg_replace("/<br\W*?\/>/", "<br><br>", $html);
		//HTML parser
		$html=strip_tags($html,"<b><u><i><a><img><p><br><strong><em><font><tr><blockquote>"); //supprime tous les tags sauf ceux reconnus
		$html=str_replace("\n",' ',$html); //remplace retour à la ligne par un espace
		$a=preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE); //éclate la chaîne avec les balises
		foreach($a as $i=>$e)
		{
			if($i%2==0)
			{
				//Text
				if($this->HREF)
					$this->PutLink($this->HREF,$e);
				else
					$this->Write(5,stripslashes(txtentities($e)));
			}
			else
			{
				//Tag
				if($e[0]=='/')
					$this->CloseTag(strtoupper(substr($e,1)));
				else
				{
					//Extract attributes
					$a2=explode(' ',$e);
					$tag=strtoupper(array_shift($a2));
					$attr=array();
					foreach($a2 as $v)
					{
						if(preg_match('/([^=]*)=["\']?([^"\']*)/',$v,$a3))
							$attr[strtoupper($a3[1])]=$a3[2];
					}
					$this->OpenTag($tag,$attr);
				}
			}
		}
	}
	function OpenTag($tag, $attr)
	{
		//Opening tag
		switch($tag){
			case 'STRONG':
				$this->SetStyle('B',true);
				break;
			case 'EM':
				$this->SetStyle('I',true);
				break;
			case 'B':
			case 'I':
			case 'U':
				$this->SetStyle($tag,true);
				break;
			case 'A':
				$this->HREF=$attr['HREF'];
				break;
			case 'IMG':
				if(isset($attr['SRC']) && (isset($attr['WIDTH']) || isset($attr['HEIGHT']))) {
					if(!isset($attr['WIDTH']))
						$attr['WIDTH'] = 0;
					if(!isset($attr['HEIGHT']))
						$attr['HEIGHT'] = 0;
					$this->Image($attr['SRC'], $this->GetX(), $this->GetY(), px2mm($attr['WIDTH']), px2mm($attr['HEIGHT']));
				}
				break;
			case 'TR':
			case 'BLOCKQUOTE':
			case 'BR':
				$this->Ln(2);
				break;
			case 'P':
				$this->Ln(4);
				break;
			case 'FONT':
				if (isset($attr['COLOR']) && $attr['COLOR']!='') {
					$coul=hex2dec($attr['COLOR']);
					$this->SetTextColor($coul['R'],$coul['V'],$coul['B']);
					$this->issetcolor=true;
				}
				if (isset($attr['FACE']) && in_array(strtolower($attr['FACE']), $this->fontlist)) {
					$this->SetFont(strtolower($attr['FACE']));
					$this->issetfont=true;
				}
				break;
		}
	}
	function CloseTag($tag)
	{
		//Closing tag
		if($tag=='STRONG')
			$tag='B';
		if($tag=='EM')
			$tag='I';
		if($tag=='B' || $tag=='I' || $tag=='U')
			$this->SetStyle($tag,false);
		if($tag=='A')
			$this->HREF='';
		if($tag=='FONT'){
			if ($this->issetcolor==true) {
				$this->SetTextColor(0);
			}
			if ($this->issetfont) {
				$this->SetFont('arial');
				$this->issetfont=false;
			}
		}
	}
	function SetStyle($tag, $enable)
	{
		//Modify style and select corresponding font
		$this->$tag+=($enable ? 1 : -1);
		$style='';
		foreach(array('B','I','U') as $s)
		{
			if($this->$s>0)
				$style.=$s;
		}
		$this->SetFont('',$style);
	}
	function PutLink($URL, $txt)
	{
		//Put a hyperlink
		$this->SetTextColor(0,0,255);
		$this->SetStyle('U',true);
		$this->Write(5,$txt,$URL);
		$this->SetStyle('U',false);
		$this->SetTextColor(0);
	}
	function Header() {
		global $title, $leftmargin, $hdk_url, $logo, $height, $width;
		if(file_exists($logo)) {
			$this->Image($logo, 10 + $leftmargin, 8);
		}	
		$this->Ln(2);
		$this->SetFont('Arial', 'B', 10);
		//Move to the right
		$this->Cell($leftmargin);
		//Title
		//$this->Cell(0, 5, $title, 0, 0, 'L');
		//Line break
		$this->Ln(8);
		$this->cabec();
	}
	function Footer() {
		global $page;
		//Position at 1.5 cm from bottom
		$this->SetY(-15);
		$this->SetFont('Arial', 'I', 6);
		//Page number
		$this->Cell(0, 10, $page . ' ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
	}
	function cabec() {
		global $LineCabec, $leftmargin;
		$this->SetFont('Arial', '', 8);
		$this->SetFillColor(211,211,211);
		$this->Cell($leftmargin);
		$this->Ln(5);
	}
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial','',9);

$pdf->Cell($leftmargin);
$pdf->SetFillColor(200,220,255);
$pdf->Cell(175,$CelHeight,html_entity_decode($a_lang['Request'],ENT_QUOTES, "ISO8859-1"),0,1,'C',1);
$pdf->Ln(4);



$pdf->Cell($leftmargin);
$pdf->Cell(22,$CelHeight,html_entity_decode($a_lang['Number'],ENT_QUOTES, "ISO8859-1") . ":",0,0,'R',0);
$pdf->Cell(33,$CelHeight,substr($rs->fields['code_request'],0,4) . "/" . substr($rs->fields['code_request'],4,2) . "-" . substr($rs->fields['code_request'],6),0,0,'L',0);
$pdf->Cell(40);
$pdf->Cell(30,$CelHeight,html_entity_decode($a_lang['Opened_by'],ENT_QUOTES, "ISO8859-1") .':',0,0,'R',0);
$pdf->Cell(60,$CelHeight,utf8_decode($rs->fields['personname']),0,1,'L',0);
//--
$pdf->Cell($leftmargin);
$pdf->Cell(22,$CelHeight,html_entity_decode($a_lang['Request_owner'],ENT_QUOTES, "ISO8859-1") .':',0,0,'R',0);
$pdf->Cell(25,$CelHeight,utf8_decode($rs->fields['personname']),0,0,'L',0);
$pdf->Cell(48);
$pdf->Cell(30,$CelHeight,html_entity_decode($a_lang['Source'],ENT_QUOTES, "ISO8859-1").':',0,0,'R',0);
$pdf->Cell(60,$CelHeight,utf8_decode($rs->fields['source']),0,1,'L',0);
// --

if($_SESSION['SES_REQUEST_SHOW_PHONE'] == 1) {
	$pdf->Cell($leftmargin);
	$pdf->Cell(22,$CelHeight,html_entity_decode($a_lang['Company'],ENT_QUOTES, "ISO8859-1") .':',0,0,'R',0);
	$pdf->Cell(25,$CelHeight,utf8_decode($rs->fields['company']),0,0,'L',0);
	$pdf->Cell(48);
	$pdf->Cell(30,$CelHeight,html_entity_decode($a_lang['Phone'],ENT_QUOTES, "ISO8859-1").':',0,0,'R',0);
	$pdf->Cell(60,$CelHeight,preg_replace('/([0-9]{2})([0-9]{4})([0-9]{4})/', '($1) $2-$3', $rs->fields['phone_number']),0,1,'L',0);
	$pdf->Cell($leftmargin);
	$pdf->Cell(95);
	$pdf->Cell(30,$CelHeight,html_entity_decode($a_lang['Branch'],ENT_QUOTES, "ISO8859-1").':',0,0,'R',0);
	$pdf->Cell(60,$CelHeight,$rs->fields['branch_number'],0,1,'L',0);
	$pdf->Cell($leftmargin);
	$pdf->Cell(95);
	$pdf->Cell(30,$CelHeight,html_entity_decode($a_lang['Mobile_phone'],ENT_QUOTES, "ISO8859-1").':',0,0,'R',0);
	$pdf->Cell(60,$CelHeight,preg_replace('/([0-9]{2})([0-9]{4})([0-9]{4})/', '($1) $2-$3', $rs->fields['cel_phone']),0,1,'L',0);
}else{
	$pdf->Cell($leftmargin);
	$pdf->Cell(22,$CelHeight,html_entity_decode($a_lang['Company'],ENT_QUOTES, "ISO8859-1") .':',0,0,'R',0);
	$pdf->Cell(15,$CelHeight,utf8_decode($rs->fields['company']),0,1,'L',0);
	$pdf->Cell(48);
}

/*
 * 
pers.phone_number       AS phone_number,
		   pers.cel_phone          AS cel_phone,
		   pers.branch_number      AS branch_number,
 */
// -- Department and Status --
$pdf->Cell($leftmargin);
$pdf->Ln(2);
$pdf->Cell($leftmargin);
$pdf->Line($pdf->GetX(),$pdf->GetY(), 195, $pdf->GetY());
$pdf->Ln(2);	
$pdf->Cell($leftmargin-1);
$pdf->Cell(22,$CelHeight,html_entity_decode($a_lang['Department'],ENT_QUOTES, "ISO8859-1") .':',0,'L');
$pdf->Cell(15,$CelHeight,utf8_decode($rs->fields['department']),0,0,'L',0);
$pdf->Cell(51);
$pdf->Cell(30,$CelHeight,html_entity_decode($a_lang['status'],ENT_QUOTES, "ISO8859-1").':',0,0,'R');
$pdf->Cell(60,$CelHeight,utf8_decode($rs->fields['status']),0,1,'L',0);
// -- Area and Opening Date
$pdf->Cell($leftmargin);
$pdf->Cell(22,$CelHeight,html_entity_decode($a_lang['Area'],ENT_QUOTES, "ISO8859-1") .':',0,0,'R',0);
$pdf->Cell(15,$CelHeight,utf8_decode($rs->fields['area_name']),0,0,'L',0);
$pdf->Cell(50);
$pdf->Cell(30,$CelHeight,html_entity_decode($a_lang['Opening_date'],ENT_QUOTES, "ISO8859-1").':',0,0,'R');
$pdf->Cell(60,$CelHeight,utf8_decode($rs->fields['entry_date']),0,1,'L',0);
// -- Type and Priority
$pdf->Cell($leftmargin);
$pdf->Cell(22,$CelHeight,html_entity_decode($a_lang['type'],ENT_QUOTES, "ISO8859-1") .':',0,0,'R',0);
$pdf->Cell(15,$CelHeight,utf8_decode($rs->fields['type']),0,0,'L',0);
$pdf->Cell(50);
$pdf->Cell(30,$CelHeight,html_entity_decode($a_lang['Priority'],ENT_QUOTES, "ISO8859-1").':',0,0,'R');
$pdf->Cell(60,$CelHeight,utf8_decode($rs->fields['priority']),0,1,'L',0);
// -- Item and Attendance Way
$pdf->Cell($leftmargin);
$pdf->Cell(22,$CelHeight,html_entity_decode($a_lang['Item'],ENT_QUOTES, "ISO8859-1") .':',0,0,'R',0);
$pdf->Cell(15,$CelHeight,utf8_decode($rs->fields['item_name']),0,0,'L',0);
$pdf->Cell(50);
$pdf->Cell(30,$CelHeight,html_entity_decode($a_lang['Att_way'],ENT_QUOTES, "ISO8859-1").':',0,0,'R');
$pdf->Cell(60,$CelHeight,utf8_decode($rs->fields['way_name']),0,1,'L',0);
// -- Service and In Charge
$pdf->Cell($leftmargin);
$pdf->Cell(22,$CelHeight,html_entity_decode($a_lang['Service'],ENT_QUOTES, "ISO8859-1") .':',0,0,'R',0);
$pdf->Cell(15,$CelHeight,utf8_decode($rs->fields['service']),0,0,'L',0);
$pdf->Cell(50);
$pdf->Cell(30,$CelHeight,html_entity_decode($a_lang['Var_incharge'],ENT_QUOTES, "ISO8859-1").':',0,0,'R');
$pdf->Cell(60,$CelHeight,utf8_decode($rs->fields['in_charge']),0,1,'L',0);
// -- Reason and Expire date
$pdf->Cell($leftmargin);
$pdf->Cell(22,$CelHeight,html_entity_decode($a_lang['Reason'],ENT_QUOTES, "ISO8859-1") .':',0,0,'R',0);
$pdf->Cell(15,$CelHeight,utf8_decode($rs->fields['reason']),0,0,'L',0);
$pdf->Cell(50);
$pdf->Cell(30,$CelHeight,html_entity_decode($a_lang['Expire_date'],ENT_QUOTES, "ISO8859-1").':',0,0,'R');
$pdf->Cell(60,$CelHeight,utf8_decode($rs->fields['expire_date']),0,1,'L',0);
// -- Subject and description 
$pdf->Ln(2);
$pdf->Cell($leftmargin);
$pdf->Line($pdf->GetX(),$pdf->GetY(), 195, $pdf->GetY());
$pdf->Ln(2);	
$pdf->Cell($leftmargin);

$pdf->Cell(22,$CelHeight,html_entity_decode($a_lang['Subject'],ENT_QUOTES, "ISO8859-1") .':',0,0,'R',0);
$pdf->Cell(0,$CelHeight,utf8_decode($rs->fields['subject']),0,1,'L',0);

$pdf->Cell($leftmargin);
$pdf->Cell(22,$CelHeight,html_entity_decode($a_lang['Description'],ENT_QUOTES, "ISO8859-1") .':',0,0,'R',0);
$pdf->SetLeftMargin($leftmargin + 30);
$description = ltrim(html_entity_decode(utf8_decode($rs->fields['description']),ENT_QUOTES, "ISO8859-1"));
//die($description);
$pdf->Cell(0,$CelHeight,$pdf->WriteHTML($description),0,1,'L',0);
$pdf->SetLeftMargin($leftmargin);
/* --  Line -- */ 
$pdf->Ln(2);
$pdf->Cell($leftmargin);
$pdf->Line($pdf->GetX(),$pdf->GetY(), 195, $pdf->GetY());
$pdf->Ln(2);	
/* ---------- */

if ($db_connect == 'oci8po'){
$sql = "select
           nt.idnote,
           pers.idperson,
           pers.name as person_name,
           nt.description,
           to_char(nt.entry_date, 'DD/MM/YYYY HH24:MI') AS entry_date,
           nt.minutes,
           nt.start_hour,
           nt.finish_hour,
           nt.execution_date,
           nt.public_,
           nt.idtype,
           nt.idnote_attachment,
           nt.ip_adress,
           nt.callback,
           nta.file_name,
           TO_CHAR(TRUNC(SYSDATE) + ( NUMTODSINTERVAL(TO_DATE(nt.finish_hour,'HH24:MI:SS') - to_date(nt.start_hour, 'HH24:MI:SS'), 'DAY')),'HH24:MI:SS')
           AS diferenca
        from hdk_tbnote  nt,
            tbperson pers,
            hdk_tbnote_attachment  nta
        where code_request = '".$_GET['code_request']."'
            and nt.idnote_attachment = nta.idnote_attachment (+)
             and pers.idperson = nt.idperson
        order by nt.entry_date desc";
}else{

$sql =	"
		select
		   nt.idnote,
		   pers.idperson,
		   pers.name as person_name,
		   nt.description,
		   DATE_FORMAT(nt.entry_date, '%d/%m/%Y %H:%i') AS entry_date,
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
		where code_request = '".$_GET['code_request']."'
			 and pers.idperson = nt.idperson
		order by nt.entry_date desc
		";
}
$rs = $db->Execute($sql);
if (!$rs)
    die("Erro : " . $db->ErrorMsg());	


//Background color                  //$this->; Added_notes

if ($rs->RecordCount() != 0) {
	$pdf->Ln(6);
	$pdf->Cell($leftmargin);
	$pdf->SetFillColor(200,220,255);
	$pdf->Cell(175,$CelHeight,html_entity_decode($a_lang['Added_notes'],ENT_QUOTES, "ISO8859-1"),0,1,'C',1);
	
	while (!$rs->EOF) {
		$pdf->Ln(3);
		$pdf->Cell($leftmargin);
		$pdf->Cell(30,$CelHeight,$rs->fields['entry_date'] . " [ " . utf8_decode($rs->fields['person_name']) . " ] " ,0,1,'L');
		//$pdf->Cell($leftmargin);
		$pdf->SetLeftMargin($leftmargin + 28);
		//$pdf->SetRightMargin(194);
		$description = ltrim(html_entity_decode(utf8_decode($rs->fields['description']),ENT_QUOTES, "ISO8859-1"));
		$description = preg_replace("/<br\W*?\/>/", "<br><br>", $description);
		$pdf->Cell(0,$CelHeight,$pdf->WriteHTML($description),0,1,'C');
		$pdf->Ln(1);
		$pdf->SetLeftMargin($leftmargin);	
		/* --  Line -- */ 
		$pdf->Ln(1);
		$pdf->Cell($leftmargin);
		$pdf->Line($pdf->GetX(),$pdf->GetY(), 195, $pdf->GetY());
		$pdf->Ln(1);	
		/* ---------- */	

		$rs->MoveNext();
	}

	
}
					
/*


*/	

$pdf->Output("report_".date("Ymd").".pdf",'I');



exit;

//function hex2dec
//returns an associative array (keys: R,G,B) from
//a hex html code (e.g. #3FE5AA)
function hex2dec($couleur = "#000000"){
	$R = substr($couleur, 1, 2);
	$rouge = hexdec($R);
	$V = substr($couleur, 3, 2);
	$vert = hexdec($V);
	$B = substr($couleur, 5, 2);
	$bleu = hexdec($B);
	$tbl_couleur = array();
	$tbl_couleur['R']=$rouge;
	$tbl_couleur['V']=$vert;
	$tbl_couleur['B']=$bleu;
	return $tbl_couleur;
}

//conversion pixel -> millimeter at 72 dpi
function px2mm($px){
	return $px*25.4/72;
}

function txtentities($html){
	$trans = get_html_translation_table(HTML_ENTITIES);
	$trans = array_flip($trans);
	return strtr($html, $trans);
}
////////////////////////////////////

?>
