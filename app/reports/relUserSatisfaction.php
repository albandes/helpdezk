<?php
error_reporting(0);
require_once('../../includes/config/config.php');
require_once('../../includes/adodb/adodb.inc.php');
require_once('../../includes/Smarty/Smarty.class.php');
require_once('../../system/system.php');
require_once('../../includes/classes/pipegrep/pipegrep.php');
include_once('classes/ProtectSql/ProtectSql.php');

$hdk_url = $config['hdk_url'];
$path_default = $config['path_default'];
$lang_default = $config['lang'];
$date_format = $config['date_format'];
$hour_format = $config['hour_format'];

$db_hostname = $config["db_hostname"];
$db_name = $config["db_name"];
$db_username = $config["db_username"];
$db_password = $config["db_password"];

$ProtectSql = new sqlinj;
$ProtectSql->start("aio","all");

$document_root = $_SERVER['DOCUMENT_ROOT'];
if (substr($document_root, -1) != '/') {
    $document_root = $document_root . '/';
}

$pipe = new pipegrep();
$smarty = new Smarty;
$smarty->debugging = true;

if(substr($hdk_url , 0,1)!='/'){
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

$smarty->compile_dir = $document_root . $path_root . "/system/templates_c/";
$smarty->config_load($document_root . $path_root . '/app/lang/' . $lang_default . '.txt', $lang_default);

$a_lang = $smarty->get_config_vars();

/*
 *   Variables 
 */
$period = "";
if($_GET['fromdate'] && $_GET['todate']){
	$period = "(".$_GET['fromdate'] ." - ". $_GET['todate'].")";
} 
if($_GET['fromdate'] && !$_GET['todate']){
	$period = "(".html_entity_decode($a_lang['From'])." ".$_GET['fromdate'].")";
}
if(!$_GET['fromdate'] && $_GET['todate']){
	$period = "(".html_entity_decode($a_lang['To'])." ".$_GET['todate'].")";
}
$title 		= html_entity_decode($a_lang['pgr_user_satisfaction'])." ".$period;
$page 		= utf8_decode($a_lang['PDF_Page']);
$LineCabec  = array( html_entity_decode($a_lang['Evaluation2']),  html_entity_decode($a_lang['Requests']), "%");

$leftmargin = 40;

$db = NewADOConnection('mysqlt');
if (!$db->Connect($db_hostname, $db_username, $db_password, $db_name)) {
    die("<br>Error connecting to database: " . $db->ErrorNo() . " - " . $db->ErrorMsg());
}

$sql_logo = "select name, height, width, file_name from tblogos where name = 'reports'";
$rs_logo = $db->Execute($sql_logo);
if(!$rs_logo) die("Erro : " . $db->ErrorMsg());

$logo       = "app/uploads/logos/".$rs_logo->fields['file_name'];
$height     = $rs_logo->fields['height'];
$width      = $rs_logo->fields['width'];
//print_r($_GET);

$date_field = "req.entry_date";
$date_interval = $pipe->mysql_date_condition($date_field, $_GET['fromdate'] , $_GET['todate'], $lang_default) ;
if ($date_interval) $where = "AND " . $date_interval;
  
	if($_GET['cmbCompany'])
	$where .= " and req.idperson_juridical = ".$_GET['cmbCompany'];

if($_GET['cmbPerson'])
	$where .= " AND req_charge.id_in_charge = ".$_GET['cmbPerson']." AND req_charge.ind_in_charge = 1";


$sql = 	"
		select eva.idevaluation, eva.name, count(distinct req.code_request) as total
		FROM 	hdk_tbevaluation eva, 
				hdk_tbrequest_evaluation req_eva, 
				hdk_tbrequest req, 
				hdk_tbrequest_in_charge req_charge
		WHERE 	eva.idevaluation = req_eva.idevaluation
		AND 	req.code_request = req_eva.code_request
		AND 	req_charge.code_request = req.code_request
		
		$where			
		
		GROUP BY eva.name
		ORDER BY eva.idquestion ASC, total DESC 
		";

$rs = $db->Execute($sql);
if (!$rs)
    die("Erro : " . $db->ErrorMsg());

switch ($_GET['outputtype']) {

    case "CSV":
       	$csv = array();
		
		
		while (!$rs->EOF) {
			$percent_total += $rs->fields['total'];
            $rs->MoveNext();
        }
		$rs->MoveFirst();
		
        while (!$rs->EOF) {
            array_push($csv, utf8_decode($rs->fields['name']) . ',' . $rs->fields['total'] . ',' . number_format($rs->fields['total'] * 100 / $percent_total, 2, ",", ".")."%");
            $rs->MoveNext();
        } 
        $filename = "report_".date("Ymd").".csv";
        header( 'Content-Type: text/csv' );
        header( 'Content-Disposition: attachment;filename='.$filename);
        $fp = fopen('php://output', 'w');
		
		if(!$_GET['delimiter'])	$_GET['delimiter'] = ",";
        foreach ($csv as $line) {
            fputcsv($fp, explode(',', $line), $_GET['delimiter']);
        }
        fclose($fp);
	break;

    case "XLS":
        include_once("classes/export_excel/class.export_excel.php");
        $headerEx = explode(',',$_GET['headertab']);
        $header = array();
		
        foreach($headerEx as $nome){
        	array_push($header,utf8_decode($nome));
		}
		
		while (!$rs->EOF) {
			$percent_total += $rs->fields['total'];
            $rs->MoveNext();
        }
		$rs->MoveFirst();
		
		$i = 0;
        while (!$rs->EOF) {
        	$excel[$i] = array  (
            					0 => utf8_decode($rs->fields['name']),
                                1 => $rs->fields['total'],
                                2 => number_format($rs->fields['total'] * 100 / $percent_total, 2, ",", ".")."%"
                                );								
            $i++;	
            $rs->MoveNext();
        }
        $excel_obj = new ExportExcel("report_".date("Ymd").".xls");
        /*
         * 
		Setting the values of the headers and data of the excel file 
		and these values comes from the other file which file shows the data
        $header = array("Name","Email","Country"); 
         * 
        */
         
		$excel_obj->setHeadersAndValues($header,$excel); 
		//now generate the excel file with the data and headers set
		$excel_obj->GenerateExcelFile();
        break;

    case "PDF":
        require_once('../../includes/classes/fpdf/' . 'fpdf.php');

        class PDF extends FPDF {			
			var $widths;
            var $aligns;
            
            function Header() {
                global $title, $leftmargin, $hdk_url, $logo, $height, $width;
				if(file_exists($hdk_url . $logo)) {
					$this->Image($hdk_url . $logo, 10 + $leftmargin, 8);
				}	
                $this->Ln(2);
                $this->SetFont('Arial', 'B', 10);
                //Move to the right
                
                //Title
                $this->Cell(0, 5, $title, 0, 0, 'C');
                $this->Ln(7);
                $this->SetFont('Arial','B',8);
				$this->Cell(0,0,date("d/m/Y"),0,0,'C');                
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
                $this->Cell(60, 4, $LineCabec[0], 1, 0, 'L', 1);
                $this->Cell(25, 4, $LineCabec[1], 1, 0, 'R', 1);
				$this->Cell(25, 4, $LineCabec[2], 1, 0, 'R', 1);
                $this->Ln(4);
            }

        }
		
        $pdf = new PDF();
        $pdf->AliasNbPages();
        $pdf->AddPage();
		$pdf->SetFont('Arial','',8);
		$linha = 0;
		
		while (!$rs->EOF) {
			$percent_total += $rs->fields['total'];
            $rs->MoveNext();
        }
		$rs->MoveFirst();
		
        while (!$rs->EOF) {
        	if(($linha % 2) == 0) $pdf->SetFillColor(255,255,255);
			else $pdf->SetFillColor(230,230,250);
			
            $pdf->Cell($leftmargin);
            $pdf->Cell(60, 4, utf8_decode($rs->fields['name']), 1, 0,"L",1);
            $pdf->Cell(25, 4, $rs->fields['total'], 1, 0,"R",1);
            $pdf->Cell(25, 4, number_format($rs->fields['total'] * 100 / $percent_total, 2, ",", ".")."%", 1, 0,"R",1);
            $pdf->Ln(4);
			$linha++;
			
            $rs->MoveNext();
        }
        $pdf->Output("report_".date("Ymd").".pdf",'D');
		break;    
}

exit;
?>
