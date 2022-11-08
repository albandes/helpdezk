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

$title 		= html_entity_decode($a_lang['pgr_summarized_operator'])." ".$period;
$page 		= utf8_decode($a_lang['PDF_Page']);
$LineCabec  = array( 
					html_entity_decode($a_lang['Operator'], ENT_COMPAT, 'ISO-8859-1'),
					html_entity_decode($a_lang['Requests'], ENT_COMPAT, 'ISO-8859-1'),
					html_entity_decode($a_lang['Hours'], ENT_COMPAT, 'ISO-8859-1'),
					html_entity_decode($a_lang['New'], ENT_COMPAT, 'ISO-8859-1'),
					html_entity_decode($a_lang['Repassed'], ENT_COMPAT, 'ISO-8859-1'),
					html_entity_decode($a_lang['Request_in_progress'], ENT_COMPAT, 'ISO-8859-1'),
					html_entity_decode($a_lang['Finished'], ENT_COMPAT, 'ISO-8859-1'),
					html_entity_decode($a_lang['Total'], ENT_COMPAT, 'ISO-8859-1'), 
					html_entity_decode($a_lang['Normal'], ENT_COMPAT, 'ISO-8859-1'),
					html_entity_decode($a_lang['Extra'], ENT_COMPAT, 'ISO-8859-1'),
					html_entity_decode($a_lang['Phone'], ENT_COMPAT, 'ISO-8859-1')
				);

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
$date_field_hour = "apont.entry_date";
$date_interval_hour = $pipe->mysql_date_condition($date_field_hour, $_GET['fromdate'] , $_GET['todate'], $lang_default) ;
if ($date_interval_hour) $date_interval_hour = "AND " . $date_interval_hour;

$date_field_request = "solicitacao.entry_date";
$date_interval_request = $pipe->mysql_date_condition($date_field_request, $_GET['fromdate'] , $_GET['todate'], $lang_default) ;		
if ($date_interval_request) $date_interval_request = "AND " . $date_interval_request;


$sql_person = " select tbp.idperson, tbp.name
				from tbperson as tbp
				where  tbp.idtypeperson IN(1,3) AND tbp.idperson != 1 ORDER BY name ASC 
				";
				

$select = $db->Execute($sql_person);
if (!$select)
    die("Erro : " . $db->ErrorMsg());


$output = array();
		
while (!$select->EOF) {
    $idperson = $select->fields['idperson'];
    $personname = $select->fields['name'];
	
	$sql_vals = "
			SELECT
        	(SELECT `name` FROM hdk_tbdepartment_has_person a, hdk_tbdepartment b WHERE a.idperson = $idperson AND a.iddepartment = b.iddepartment) as department,
        	(SELECT tbperson.name FROM hdk_tbdepartment_has_person, hdk_tbdepartment, tbperson WHERE hdk_tbdepartment_has_person.idperson = $idperson AND hdk_tbdepartment_has_person.iddepartment = hdk_tbdepartment.iddepartment AND tbperson.idperson = hdk_tbdepartment.idperson) as company,
			(SELECT SUM(minutes) FROM tbperson usu, hdk_tbnote apont, hdk_tbrequest sol WHERE usu.idperson = apont.idperson and apont.code_request = sol.code_request AND sol.idsource = 2 AND usu.idperson = $idperson $date_interval_hour AND apont.minutes > 0) AS TOTAL_TELEPHONE,
			(SELECT SUM(minutes) FROM tbperson usu, hdk_tbnote apont WHERE usu.idperson = apont.idperson AND usu.idperson = $idperson $date_interval_hour AND apont.hour_type = 1 AND apont.minutes > 0) AS TOTAL_NORMAL,
			(SELECT SUM(minutes) FROM tbperson usu, hdk_tbnote apont WHERE usu.idperson = apont.idperson AND usu.idperson = $idperson $date_interval_hour AND apont.hour_type = 2 AND apont.minutes > 0) AS TOTAL_EXTRA, 
			(SELECT COUNT(distinct solicitacao.code_request) FROM hdk_tbrequest solicitacao, hdk_tbrequest_in_charge solgrupo WHERE solicitacao.code_request = solgrupo.code_request AND solicitacao.idstatus = 1 AND solgrupo.id_in_charge = $idperson $date_interval_request AND solgrupo.ind_in_charge = 1) AS NEW,
			(SELECT COUNT(distinct solicitacao.code_request) FROM hdk_tbrequest solicitacao, hdk_tbrequest_in_charge solgrupo WHERE solicitacao.code_request = solgrupo.code_request AND solicitacao.idstatus = 2 AND solgrupo.id_in_charge = $idperson $date_interval_request AND solgrupo.ind_in_charge = 1) AS REPASSED,
			(SELECT COUNT(distinct solicitacao.code_request) FROM hdk_tbrequest solicitacao, hdk_tbrequest_in_charge solgrupo WHERE solicitacao.code_request = solgrupo.code_request AND solicitacao.idstatus = 3 AND solgrupo.id_in_charge = $idperson $date_interval_request AND solgrupo.ind_in_charge = 1) AS ON_ATTENDANCE,
			(SELECT COUNT(distinct solicitacao.code_request) FROM hdk_tbrequest solicitacao, hdk_tbrequest_in_charge solgrupo WHERE solicitacao.code_request = solgrupo.code_request AND solicitacao.idstatus in ( 4, 5 ) AND solgrupo.id_in_charge = $idperson $date_interval_request AND solgrupo.ind_in_charge = 1) AS FINISH";
    
    $rs = $db->Execute($sql_vals);
	if (!$rs)
    	die("Erro : " . $db->ErrorMsg());
	
	$company = $rs->fields['company'];
	$new = $rs->fields["NEW"];
	$repassed = $rs->fields["REPASSED"];
	$on_attendance = $rs->fields["ON_ATTENDANCE"];
	$finish = $rs->fields["FINISH"];
	$total_req = $new + $repassed + $on_attendance + $finish;
	$normal = $pipe->conv_minute_hour($rs->fields["TOTAL_NORMAL"]);
	$extra = $pipe->conv_minute_hour($rs->fields["TOTAL_EXTRA"]);
	$telephone = $pipe->conv_minute_hour($rs->fields["TOTAL_TELEPHONE"]);
	$total_hour = $pipe->conv_minute_hour(($rs->fields["TOTAL_NORMAL"] + $rs->fields["TOTAL_EXTRA"] + $rs->fields["TOTAL_TELEPHONE"]));
	$total_hour_no_convert = number_format(($rs->fields["TOTAL_NORMAL"] + $rs->fields["TOTAL_EXTRA"] + $rs->fields["TOTAL_TELEPHONE"])/60, 2, ",",".");			
	$output['result'][$rs->fields['department']]['company'] = $company; 
	$output['result'][$rs->fields['department']]['user'][] = array(
    					"name"  		=> $personname,
    					"company"		=> $company,
    					"new"			=> $new,
    					"repassed"		=> $repassed,
    					"on_attendance" => $on_attendance,
    					"finish"		=> $finish,
    					"total_req"		=> $total_req,
    					"normal"		=> $normal,
    					"extra"			=> $extra,
    					"tel"			=> $telephone,
    					"total_hour"	=> $total_hour
                    ) ;
				
    $total_all_req += $total_req;
	$total_all_hour += $rs->fields["TOTAL_NORMAL"] + $rs->fields["TOTAL_EXTRA"] + $rs->fields["TOTAL_TELEPHONE"];
	
	$output['result'][$rs->fields['department']]['total']['total_all_req'] += $total_req;
	$output['result'][$rs->fields['department']]['total']['total_all_hour'] += $rs->fields["TOTAL_NORMAL"] + $rs->fields["TOTAL_EXTRA"] + $rs->fields["TOTAL_TELEPHONE"];
	
    $select->MoveNext();
}		

//Get value of "total_all_hour" for each department and convert the hour
foreach ($output['result'] as $key => $val) {
	$output['result'][$key]['total']['total_all_hour'] = $pipe->conv_minute_hour($output['result'][$key]['total']['total_all_hour']);
}		

$output['total_all'] = array(
    					"total_all_req"  	=> $total_all_req,
    					"total_all_hour"	=> $pipe->conv_minute_hour($total_all_hour)
                      ) ;


switch ($_GET['outputtype']) {

    case "CSV":
       	$csv = array();
		
		
		foreach ($output['result'] as $key => $val) {
			
			array_push($csv,             
	            utf8_decode($key." (". $val['company'] .")")
			);
			
				
			foreach ($val['user'] as $key => $sol) {				
				array_push($csv,             
		            utf8_decode($sol['name']) . ',' .
		            $sol['new'] . ',' .  
		            $sol['repassed'] . ',' .
		            $sol['on_attendance'] . ',' .
		            $sol['finish'] . ',' .
		            $sol['total_req'] . ',' .
		            $sol['normal'] . ',' .
		            $sol['extra'] . ',' .
		            $sol['tel'] . ',' .
		            $sol['total_hour']
				);
			}
			
			array_push($csv,             
		            $val['total']['total_all_req'] . ',' .
		            $val['total']['total_all_hour']
				);
			
		}
        
        array_push($csv,             
		            'Total,' .
		            $output['total_all']['total_all_req'] . "," .
		            $output['total_all']['total_all_hour']
				);

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
        $header_prev = array();		
        foreach($headerEx as $nome){
        	if($nome)
        		array_push($header_prev,utf8_decode($nome));
		}
		
		$header = array(
						0 => $header_prev[0],
						1 => $header_prev[1],
						2 => "",
						3 => "",
						4 => "",
						5 => "",
						6 => $header_prev[2],
						7 => "",
						8 => "",
						9 => ""
						);
		
		$excel[0] = array  (
            					0 => " ",
                                1 => $header_prev[3],
                                2 => $header_prev[4],
                                3 => $header_prev[5],
                                4 => $header_prev[6],
                                5 => $header_prev[7],
                                6 => $header_prev[8],
                                7 => $header_prev[9],
                                8 => $header_prev[10],
                                9 => $header_prev[11]
                                );
		
		$i = 1;
		foreach ($output['result'] as $key => $val) {
			
			$excel[$i] = array  (
            					0 => utf8_decode($key." (". $val['company'] .")")
                                );
			$i++;			
			foreach ($val['user'] as $key => $sol) {				
				
				$excel[$i] = array  (
            					0 => utf8_decode($sol['name']),
            					1 => $sol['new'],
            					2 => $sol['repassed'],
                                3 => $sol['on_attendance'],
                                4 => $sol['finish'],
                                5 => $sol['total_req'],
                                6 => $sol['normal'],
                                7 => $sol['extra'],
                                8 => $sol['tel'],
                                9 => $sol['total_hour']
                                );
				$i++;
			}
			$excel[$i] = array  (
            					0 => "-",
            					1 => "",
            					2 => "",
                                3 => "",
                                4 => "",
                                5 => $val['total']['total_all_req'],
                                6 => "",
                                7 => "",
                                8 => "",
                                9 => $val['total']['total_all_hour']
                                );
			$i++;
		}
        	
		$excel[$i] = array  (
        					0 => "Total",
        					1 => "",
        					2 => "",
                            3 => "",
                            4 => "",
                            5 => $output['total_all']['total_all_req'],
                            6 => "",
                            7 => "",
                            8 => "",
                            9 => $output['total_all']['total_all_hour']
                            );        
		
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
                for($i=0; $i < count($data); $i++){
                    $w = $this->widths[$i];
                    $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'C';
                    //Salva a posição atual
                    $x = $this->GetX();
                    $y = $this->GetY();					
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
                if($this->GetY()+$h>$this->PageBreakTrigger){
                	$this->AddPage($this->CurOrientation);
					$this->Cell(1);
				}
				
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
                $this->Ln(8);
                $this->cabec();
            }

            function Footer() {
            	global $page,$a_lang;
                //Position at 1.5 cm from bottom
                $this->SetY(-15);
                $this->SetFont('Arial', 'I', 8);
                //Page number                
                $this->Cell(20, 10, $a_lang['Generated'].": ".date("d/m/Y H:i"), 0, 0, 'L');
				$this->Cell(0, 10, $page . ' ' . $this->PageNo() . '/{nb}', 0, 0, 'R');
            }

            function cabec() {
                global $LineCabec, $leftmargin;
               	$this->SetFont('Arial', 'B', 8);
                $this->SetFillColor(211,211,211);
                $this->Cell($leftmargin);
                $this->Cell(58, 4, $LineCabec[0], 0, 0, 'L', 1);
				$this->Cell(108, 4, $LineCabec[1], 0, 0, 'C', 1);
				$this->Cell(108, 4, $LineCabec[2], 0, 0, 'C', 1);
                $this->Ln(4);
				$this->Cell($leftmargin);
				$this->Cell(58, 4, "", 0, 0, 'L', 1);
                $this->Cell(24, 4, $LineCabec[3], 0, 0, 'C', 1);
                $this->Cell(24, 4, $LineCabec[4], 0, 0, 'C', 1);
				$this->Cell(24, 4, $LineCabec[5], 0, 0, 'C', 1);
				$this->Cell(24, 4, $LineCabec[6], 0, 0, 'C', 1);
				$this->Cell(24, 4, $LineCabec[7], 0, 0, 'C', 1);
				$this->Cell(24, 4, $LineCabec[8], 0, 0, 'C', 1);
				$this->Cell(24, 4, $LineCabec[9], 0, 0, 'C', 1);
				$this->Cell(24, 4, $LineCabec[8], 0, 0, 'C', 1);
				$this->Cell(24, 4, $LineCabec[5], 0, 0, 'C', 1);
				$this->Ln(4);
            }
	
        }
        
		$pdf = new PDF("L");
        $pdf->AliasNbPages();
        $pdf->AddPage();
		$linha = 0;
		
		
		foreach ($output['result'] as $key => $val) {
			
        	$pdf->Cell($leftmargin);
			$pdf->SetFont('Arial','B',8);
			$pdf->SetFillColor(211,211,211);
			$pdf->SetAligns(array("L"));
			$pdf->SetWidths(array(274));	
			$pdf->Row(array(utf8_decode($key." (". $val['company'] .")")));
        	$pdf->SetFont('Arial','',8);
			foreach ($val['user'] as $key => $sol) {				
				$pdf->Cell($leftmargin);
				if(($linha % 2) == 0) $pdf->SetFillColor(255,255,255);
				else $pdf->SetFillColor(230,230,250);
				$pdf->SetWidths(array(58,24,24,24,24,24,24,24,24,24));
				$pdf->SetAligns(array("L","C","C","C","C","C","C","C","C"));
				$pdf->Row(array(utf8_decode($sol['name']),$sol['new'],$sol['repassed'], $sol['on_attendance'], $sol['finish'], $sol['total_req'], $sol['normal'],$sol['extra'],$sol['tel'],$sol['total_hour']));	
				$linha++;
			}
			
			$pdf->SetFont('Arial','B',8);
			$pdf->Cell($leftmargin);
			$pdf->SetWidths(array(154,24,72,24));
			$pdf->SetAligns(array("C","C","C","C"));
			$pdf->SetFillColor(211,211,211);
			$pdf->Row(array("",$val['total']['total_all_req'],"",$val['total']['total_all_hour']));
			
		}
        
        $pdf->SetFont('Arial','B',8);
		$pdf->Cell($leftmargin);
		$pdf->SetWidths(array(154,24,72,24));
		$pdf->SetAligns(array("L","C","C","C"));
		$pdf->SetFillColor(211,211,211);
		$pdf->Row(array("Total",$output['total_all']['total_all_req'],"",$output['total_all']['total_all_hour']));
		
        
        $pdf->Output("report_".date("Ymd").".pdf",'D');
		break;    
}

exit;
?>
