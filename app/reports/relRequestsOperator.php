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
$title 		= utf8_decode($a_lang['PDF_request_report']);
$page 		= utf8_decode($a_lang['PDF_Page']);
$LineCabec  = array(utf8_decode($a_lang['PDF_code']), $a_lang['Request_owner'],$a_lang['Company'],$a_lang['Subject'], $a_lang['Opening_date'], $a_lang['Priority'], $a_lang['status']);
/* ---------------------------------------------------------------------------------------- */


$leftmargin = 10;


$db = NewADOConnection('mysqlt');
if (!$db->Connect($db_hostname, $db_username, $db_password, $db_name)) {
    die("<br>Erro ao conectar o banco de dados: " . $db->ErrorNo() . " - " . $db->ErrorMsg());
}

$sql_logo = "select name, height, width, file_name from tblogos where name = 'reports'";
$rs_logo = $db->Execute($sql_logo);
if(!$rs_logo) die("Erro : " . $db->ErrorMsg());

$logo       = "app/uploads/logos/".$rs_logo->fields['file_name'];
$height     = $rs_logo->fields['height'];
$width      = $rs_logo->fields['width'];




if($_GET['fromdate'] || $_GET['todate']){
	$pipe = new pipegrep();
	$date_field = "req.entry_date";
	$date_interval = $pipe->mysql_date_condition($date_field, $_GET['fromdate'] , $_GET['todate'], $lang_default) ;
	if ($date_interval) $date = "AND " . $date_interval;
}


if($_GET['operator'])
	$where = "AND ope.idperson = ".$_GET['operator'];

if($_GET['status'])
	$where .= " AND req.idstatus = ".$_GET['status'];

if($_GET['txtowner'])
	$where .= " AND req.idperson_owner = ".$_GET['txtowner'];

if($_GET['cmbCompany'])
	$where .= " AND req.idperson_juridical = ".$_GET['cmbCompany'];

if($_GET['txtPriority'])
	$where .= " AND req.idpriority = ".$_GET['txtPriority'];

if($_GET['cmbType'])
	$where .= " AND req.idtype = ".$_GET['cmbType'];

if($_GET['cmbItem'])
	$where .= " AND req.iditem = ".$_GET['cmbItem'];

if($_GET['cmbService'])
	$where .= " AND req.idservice = ".$_GET['cmbService'];		

if(!$date_interval) $date_interval = null;
if(!$where) $where = null;

$SQL = "select
		  req.code_request,
		  req.description,
		  pers.name,
		  (SELECT
		     tbperson.name
		   FROM hdk_tbdepartment_has_person,
		     hdk_tbdepartment,
		     tbperson
		   WHERE hdk_tbdepartment_has_person.idperson = pers.idperson
		       AND hdk_tbdepartment_has_person.iddepartment = hdk_tbdepartment.iddepartment
		       AND tbperson.idperson = hdk_tbdepartment.idperson) as company,
		  req.subject,
		  DATE_FORMAT(req.entry_date, '$date_format') as date,
		  pr.name          as priority,
		  st.name          as status,
		  st.idstatus_source,
		  st.idstatus,
		  ope.name AS operator,
		  (SELECT count(ind_repass) FROM hdk_tbrequest_in_charge WHERE code_request = req.code_request AND ind_repass = 'Y') as repass
		from hdk_tbrequest as req,
		  hdk_tbpriority as pr,
		  hdk_tbstatus as st,
		  tbperson AS pers,
		  tbperson AS ope,
		  hdk_tbrequest_in_charge AS inc
		where pr.idpriority = req.idpriority
		    AND st.idstatus = req.idstatus
		    $where
		    AND req.idperson_owner = pers.idperson
		    AND inc.code_request = req.code_request
		    AND inc.ind_in_charge = 1
		    AND ope.idperson = inc.id_in_charge
		    AND ope.idtypeperson IN (1,3)
			$date
		ORDER BY operator, req.code_request ASC";



$rs = $db->Execute($SQL);
if (!$rs)
    die("Erro : " . $db->ErrorMsg());

while (!$rs->EOF) {
				
	if($rs->fields['repass'] == 0){
		$repass = html_entity_decode($a_lang['No'],ENT_COMPAT, 'ISO-8859-1');		
	}else{
		$repass = $a_lang['Yes'];
	}
	
	$output[$rs->fields['operator']]['sol'][] = array(
						            					"code"    		=> $rs->fields['code_request'],
						            					"name"  		=> $rs->fields['name'],
						            					"company"  		=> $rs->fields['company'],
						            					"subject"  		=> $rs->fields['subject'],
						            					"entry_date"  	=> $rs->fields['date'],
						            					"priority"  	=> $rs->fields['priority'],
						            					"status"  		=> $rs->fields['status'],
						            					"repass"  		=> $repass,
						            					"description"  	=> $rs->fields['description']
						                            ) ;
			
	switch ($rs->fields['idstatus_source']) {
		case '1':
			if($rs->fields['idstatus'] == 2){
				$output[$rs->fields['operator']]['total']['repass']['sum']++;
				$output[$rs->fields['operator']]['total']['repass']['name'] = $rs->fields['status'];
			}
			else{
				$output[$rs->fields['operator']]['total']['new']['sum']++;
				$output[$rs->fields['operator']]['total']['new']['name'] = $rs->fields['status'];
			}
			break;
		
		case '3':
			$output[$rs->fields['operator']]['total']['on_att']['sum']++;
			$output[$rs->fields['operator']]['total']['on_att']['name'] = $rs->fields['status'];
			break;
		
		case '4':
			$output[$rs->fields['operator']]['total']['w_app']['sum']++;
			$output[$rs->fields['operator']]['total']['w_app']['name'] = $rs->fields['status'];
			break;
		
		case '5':
			$output[$rs->fields['operator']]['total']['fins']['sum']++;
			$output[$rs->fields['operator']]['total']['fins']['name'] = $rs->fields['status'];
			break;
		
		case '6':
			$output[$rs->fields['operator']]['total']['rej']['sum']++;
			$output[$rs->fields['operator']]['total']['rej']['name'] = $rs->fields['status'];
			break;
		
	}	
    $rs->MoveNext();
}
    

switch ($_GET['outputtype']) {

    case "CSV":
       	$csv = array();
        	
		foreach ($output as $key => $val) {			
			array_push($csv, utf8_decode($key));
			$total = 0;			
			foreach ($val['sol'] as $key => $sol) {
				if($_GET['showDesc'] == "Y"){
					$desc = preg_replace('/^\s+|\n|\r|\s+$/m', '', $sol['description']);
					$description = ",".$desc;
				}			
				array_push($csv, utf8_decode($sol['code']).','.utf8_decode($sol['name']).','.utf8_decode($sol['company']).','.utf8_decode($sol['subject']).','.utf8_decode($sol['entry_date']).','.utf8_decode($sol['priority']).','.utf8_decode($sol['status']).','.utf8_decode($sol['repass']).$description);
				$total++;
			}			
			if($val['total']['new']){				
				array_push($csv, utf8_decode($val['total']['new']['name']).','.$val['total']['new']['sum']);
			}
			if($val['total']['repass']){
				array_push($csv, utf8_decode($val['total']['repass']['name']).','.$val['total']['repass']['sum']);
			}
			if($val['total']['on_att']){
				array_push($csv, utf8_decode($val['total']['on_att']['name']).','.$val['total']['on_att']['sum']);
			}
			if($val['total']['w_app']){
				array_push($csv, utf8_decode($val['total']['w_app']['name']).','.$val['total']['w_app']['sum']);
			}
			if($val['total']['fins']){
				array_push($csv, utf8_decode($val['total']['fins']['name']).','.$val['total']['fins']['sum']);
			}
			if($val['total']['rej']){
				array_push($csv, utf8_decode($val['total']['rej']['name']).','.$val['total']['rej']['sum']);
			}
			array_push($csv, 'Total,'.$total);
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
		
		if($_GET['showDesc'] == "Y"){
			array_push($header,html_entity_decode(utf8_decode($a_lang['Description']),ENT_QUOTES, "ISO8859-1"));
		}	

        $i = 0;
		foreach ($output as $key => $val) {
			
			$excel[$i] = array  (
                                	0 => utf8_decode($key)
                                );
            
			$i++;
			
			$total = 0;
			
			foreach ($val['sol'] as $key => $sol) {
				$excel[$i] = array  (
                                0 => utf8_decode($sol['code']),
                                1 => utf8_decode($sol['name']),
                                2 => utf8_decode($sol['company']),
                                3 => utf8_decode($sol['subject']), 
                                4 => utf8_decode($sol['entry_date']),
                                5 => utf8_decode($sol['priority']),
                                6 => utf8_decode($sol['status']),
                                7 => utf8_decode($sol['repass'])
                                );
				if($_GET['showDesc'] == "Y"){
					$description = preg_replace('/^\s+|\n|\r|\s+$/m', '', $sol['description']);
					array_push($excel[$i], $description);
				}	
				
				$i++;
				$total++;
			}
			
			if($val['total']['new']){
				$excel[$i] = array  (
                                0 => "",
                                1 => "",
                                2 => "",
                                3 => "", 
                                4 => "",
                                5 => utf8_decode($val['total']['new']['name']),
                                6 => $val['total']['new']['sum']
                                );
				$i++;
			}
			if($val['total']['repass']){
				$excel[$i] = array  (
                                0 => "",
                                1 => "",
                                2 => "",
                                3 => "", 
                                4 => "",
                                5 => utf8_decode($val['total']['repass']['name']),
                                6 => $val['total']['repass']['sum']
                                );
				$i++;
			}
			if($val['total']['on_att']){
				$excel[$i] = array  (
                                0 => "",
                                1 => "",
                                2 => "",
                                3 => "", 
                                4 => "",
                                5 => utf8_decode($val['total']['on_att']['name']),
                                6 => $val['total']['on_att']['sum']
                                );
				$i++;
			}
			if($val['total']['w_app']){
				$excel[$i] = array  (
                                0 => "",
                                1 => "",
                                2 => "",
                                3 => "", 
                                4 => "",
                                5 => utf8_decode($val['total']['w_app']['name']),
                                6 => $val['total']['w_app']['sum']
                                );
				$i++;
			}
			if($val['total']['fins']){
				$excel[$i] = array  (
                                0 => "",
                                1 => "",
                                2 => "",
                                3 => "", 
                                4 => "",
                                5 => utf8_decode($val['total']['fins']['name']),
                                6 => $val['total']['fins']['sum']
                                );
				$i++;
			}
			if($val['total']['rej']){
				$excel[$i] = array  (
                                0 => "",
                                1 => "",
                                2 => "",
                                3 => "", 
                                4 => "",
                                5 => utf8_decode($val['total']['rej']['name']),
                                6 => $val['total']['rej']['sum']
                                ); 
				$i++;
			}
			$excel[$i] = array  (
                            0 => "",
                            1 => "",
                            2 => "",
                            3 => "", 
                            4 => "",
                            5 => "Total",
                            6 => $total
                            ); 
			 $i++;
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
					$this->Cell(10);
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
                $this->Cell(25, 5, $LineCabec[0], 0, 0, 'L', 1);
                $this->Cell(40, 5, $LineCabec[1], 0, 0, 'L', 1);
                $this->Cell(40, 5, $LineCabec[2], 0, 0, 'L', 1);
                $this->Cell(60, 5, $LineCabec[3], 0, 0, 'L', 1);
                $this->Cell(28, 5, $LineCabec[4], 0, 0, 'C', 1);
				$this->Cell(30, 5, $LineCabec[5], 0, 0, 'L', 1);
				$this->Cell(30, 5, $LineCabec[6], 0, 0, 'L', 1);
                $this->Ln(5);
            }

        }
		
		$pdf = new PDF("L");
        $pdf->AliasNbPages();
        $pdf->AddPage();
		$pdf->SetWidths(array(253));
		$linha = 0;
		foreach ($output as $key => $val) {
			$pdf->Cell($leftmargin);
			$pdf->SetFont('Arial','B',8);
			$pdf->SetFillColor(211,211,211);
			$pdf->SetAligns(array("L"));
			$pdf->SetWidths(array(253));	
			$pdf->Row(array(utf8_decode($key)));
			$total = 0;
			$pdf->SetFont('Arial','',8);
			foreach ($val['sol'] as $key => $sol) {
				$pdf->Cell($leftmargin);
				if(($linha % 2) == 0) $pdf->SetFillColor(255,255,255);
				else $pdf->SetFillColor(230,230,250);
				$pdf->SetWidths(array(25,40,40,60,28,30,30));
				$pdf->SetAligns(array("L","L","L","L","C","L","L"));
				$pdf->Row(array($sol['code'],utf8_decode($sol['name']),utf8_decode($sol['company']), utf8_decode($sol['subject']), utf8_decode($sol['entry_date']), utf8_decode($sol['priority']), utf8_decode($sol['status'])));
				if($_GET['showDesc'] == "Y"){
					$pdf->Cell($leftmargin);
					$pdf->SetWidths(array(253));
					$pdf->SetAligns(array("L"));
					
					$pdf->SetFont('Arial','B',8);
					$pdf->Row(array(html_entity_decode(utf8_decode($a_lang['Description']),ENT_QUOTES, "ISO8859-1").":"));
					
					$pdf->Cell($leftmargin);
					$pdf->SetWidths(array(253));
					$pdf->SetAligns(array("L"));
					$desc = preg_replace('/^\s+|\n|\r|\s+$/m', '', $sol['description']);					
					//$desc = str_replace("</p>", "\n", $desc);
					//$desc = str_replace("<br/>", "\n", $desc);
					//$desc = str_replace("<br>", "\n", $desc);
					//$desc = str_replace("<p>", "", $desc);
					$desc = str_replace("</p>", "\n", $desc);
					$desc = preg_replace("|<style\b[^>]*>(.*?)</style>|s", "", $desc);
					$desc = html_entity_decode(utf8_decode($desc),ENT_QUOTES, "ISO8859-1");
					$pdf->SetFont('Arial','',8);
					$pdf->Row(array(strip_tags($desc)));
				}	
				$linha++;
				$total++;
			}
			
						
			$pdf->SetWidths(array(223,30));
			$pdf->SetAligns(array("R","L"));
			$pdf->SetFillColor(200,200,200);
			$pdf->SetFont('Arial','B',8);
			if($val['total']['new']){
				$pdf->Cell($leftmargin);
				$pdf->Row(array(utf8_decode($val['total']['new']['name']),$val['total']['new']['sum']));
			}
			if($val['total']['repass']){
				$pdf->Cell($leftmargin); 
				$pdf->Row(array(utf8_decode($val['total']['repass']['name']),$val['total']['repass']['sum']));
			}
			if($val['total']['on_att']){
				$pdf->Cell($leftmargin); 
				$pdf->Row(array(utf8_decode($val['total']['on_att']['name']),$val['total']['on_att']['sum']));
			}
			if($val['total']['w_app']){
				$pdf->Cell($leftmargin); 
				$pdf->Row(array(utf8_decode($val['total']['w_app']['name']),$val['total']['w_app']['sum']));
			}
			if($val['total']['fins']){
				$pdf->Cell($leftmargin);
				$pdf->Row(array(utf8_decode($val['total']['fins']['name']),$val['total']['fins']['sum']));
			}
			if($val['total']['rej']){
				$pdf->Cell($leftmargin); 
				$pdf->Row(array(utf8_decode($val['total']['rej']['name']),$val['total']['rej']['sum']));
			}
			
			$pdf->Cell($leftmargin); 
			$pdf->Row(array("Total",$total));
			
			
		}
		
		$pdf->Output("report_".date("Ymd").".pdf",'D');

        break;
}

exit;
?>
