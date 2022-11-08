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
$title 		= utf8_decode($a_lang['Request_Operator_Average_Response_Time']);
$page 		= utf8_decode($a_lang['PDF_Page']);
$LineCabec  = array( html_entity_decode($a_lang['Operator']), html_entity_decode($a_lang['Company']),  html_entity_decode($a_lang['Minimum']),  html_entity_decode($a_lang['Maximum']),  html_entity_decode($a_lang['Middle']));


$leftmargin = 1;

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
$date_field = "a.entry_date";
$date_interval = $pipe->mysql_date_condition($date_field, $_GET['fromdate'] , $_GET['todate'], $lang_default) ;
if ($date_interval) {
	$date_interval = "AND " . $date_interval ;
}
if($_GET['operator'] != "ALL")
	$condition = " AND c.id_in_charge = ".$_GET['operator'];

$sql = 	"
		select
		  d.name,
		  (SELECT
				     tbperson.name
				   FROM hdk_tbdepartment_has_person,
				     hdk_tbdepartment,
				     tbperson
				   WHERE hdk_tbdepartment_has_person.idperson = d.idperson
				       AND hdk_tbdepartment_has_person.iddepartment = hdk_tbdepartment.iddepartment
				       AND tbperson.idperson = hdk_tbdepartment.idperson) as company,
		  min(b.MIN_OPENING_TIME) as min_time,
		  round(avg(b.MIN_OPENING_TIME),2) as avg_time,
		  max(b.MIN_OPENING_TIME) as max_time
		from hdk_tbrequest a,
		  hdk_tbrequest_times b,
		  hdk_tbrequest_in_charge c,
		  tbperson d
		where c.type = 'P'
			$date_interval
			and b.CODE_REQUEST = a.code_request
			and c.code_request = b.CODE_REQUEST
			and d.idperson = c.id_in_charge
			and c.ind_in_charge = 1
			and b.MIN_OPENING_TIME > 0
			$condition
		group by c.id_in_charge		
		order by d.name asc
		";

$rs = $db->Execute($sql);
if (!$rs)
    die("Erro : " . $db->ErrorMsg());

switch ($_GET['outputtype']) {

    case "CSV":
       	$csv = array();
        while (!$rs->EOF) {
            array_push($csv, $rs->fields['name'] . ',' . $rs->fields['company'] . ',' . $rs->fields['min_time'] . ',' . $rs->fields['max_time'] . ',' . $rs->fields['avg_time']) ;
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

        $i = 0;
        while (!$rs->EOF) {
        	
            $excel[$i] = array  (
                                0 => utf8_decode($rs->fields['name']),
                                1 => utf8_decode($rs->fields['company']),
                                2 => $rs->fields['min_time'], 
                                3 => $rs->fields['max_time'],
                                4 => $rs->fields['avg_time']
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
            
            function SetWidths($w){
                //Configura o array da largura das colunas
                $this->widths=$w;
            }
            
            function SetAligns($a){
                //Configura o array dos alinhamentos de coluna
                $this->aligns=$a;
            }
            
            function Row($data){
                //Calcula a altura da fila
                $nb=0;
                for($i=0;$i<count($data);$i++)
                $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
                $h=5*$nb;
                //Insere um salto de página primeiramente se for necessario
                $this->CheckPageBreak($h);
                //Desenha as células da linha
                for($i=0;$i<count($data);$i++){
                    $w=$this->widths[$i];
                    $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'C';
                    //Salva a posição atual
                    $x=$this->GetX();
                    $y=$this->GetY();
                    //Draw the border
                    $this->Rect($x,$y,$w,$h,'F');
                    //Imprime o texto
                    $this->MultiCell($w,5,$data[$i],0,$a);
                    //Coloca a posição para a direita da célula
                    $this->SetXY($x+$w,$y);
                }
                //Va para a próxima linha
                $this->Ln($h);
            }
            
            function CheckPageBreak($h){
                //Se altura h causar desbordamento, agrega uma nova pagina
                if($this->GetY()+$h>$this->PageBreakTrigger)
                $this->AddPage($this->CurOrientation);
            }
            
            function NbLines($w,$txt){
                //Calcula o número de linhas de uma MultiCell de largura w
                $cw=&$this->CurrentFont['cw'];
                if($w==0)
                    $w=$this->w-$this->rMargin-$this->x;
                $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
                $s=str_replace("\r",'',$txt);
                $nb=strlen($s);
                if($nb>0 and $s[$nb-1]=="\n")
                    $nb--;
                $sep=-1;
                $i=0;
                $j=0;
                $l=0;
                $nl=1;
                while($i<$nb){
                    $c=$s[$i];
                    if($c=="\n"){
                        $i++;
                        $sep=-1;
                        $j=$i;
                        $l=0;
                        $nl++;
                        continue;
                    }
                    if($c==' ')
                        $sep=$i;
                    $l+=$cw[$c];
                    if($l>$wmax){
                        if($sep==-1){
                            if($i==$j)
                                $i++;
                        }
                        else
                            $i=$sep+1;
                        $sep=-1;
                        $j=$i;
                        $l=0;
                        $nl++;
                    }
                    else
                        $i++;
                }
                return $nl;
            }

            function Header() {
                global $title, $leftmargin, $hdk_url, $logo, $height, $width;
				if(file_exists($hdk_url . $logo)) {
					$this->Image($hdk_url . $logo, 10 + $leftmargin, 8);
				}	
                $this->Ln(2);
                $this->SetFont('Arial', 'B', 10);
                //Move to the right
                $this->Cell($leftmargin);
                //Title
                $this->Cell(0, 5, $title, 0, 0, 'C');
                $this->Ln(7);
                $this->SetFont('Arial','B',8);
				$this->Cell($leftmargin);
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
                $this->Cell(53, 4, $LineCabec[0], 0, 0, 'L', 1);
				$this->Cell(53, 4, $LineCabec[1], 0, 0, 'L', 1);
                $this->Cell(27, 4, $LineCabec[2], 0, 0, 'L', 1);
                $this->Cell(27, 4, $LineCabec[3], 0, 0, 'L', 1);
                $this->Cell(27, 4, $LineCabec[4], 0, 0, 'L', 1);
                
                $this->Ln(5);
            }

        }

        $pdf = new PDF();
        $pdf->AliasNbPages();
        $pdf->AddPage();
		$pdf->SetFont('Arial','',8);
		$pdf->SetWidths(array(53,53,27,27,27));
		$linha = 0;
        while (!$rs->EOF) {
			$pdf->Cell($leftmargin);
			if(($linha % 2) == 0) $pdf->SetFillColor(255,255,255);
			else $pdf->SetFillColor(230,230,250);
			$pdf->SetAligns(array("L","L","L","L","L"));
        	$pdf->Row(array(utf8_decode($rs->fields['name']),utf8_decode($rs->fields['company']), $pipe->conv_minute_hour($rs->fields['min_time']), $pipe->conv_minute_hour($rs->fields['max_time']), $pipe->conv_minute_hour($rs->fields['avg_time'])));
            $linha++;
            $rs->MoveNext();
        }

        $pdf->Output("report_".date("Ymd").".pdf",'D');

        break;

    
}

exit;
?>
