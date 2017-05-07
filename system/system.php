<?php

class System {

    private $_url, $_explode;
    public $_controller, $_action, $_params, $_module, $_config;
    var $pdfAligns, $pdfWidths, $pdfLeftMargin, $pdfLogo, $pdfTitle, $a_pdfHeaderData, $pdfPage;
    // Use only in TMS module
    var $a_pdfHeaderTestData;

    public function __construct() {		
        $this->setConfig();
        $this->setUrl();
        $this->setExplode();
        $this->setModule();
        $this->setController();
        $this->setAction();
        $this->setParams();

        $this->database     = $this->getConfig('db_connect');
        $this->pathDefault  = $this->getConfig('path_default');
        $this->dateFormat 	= $this->getConfig('date_format');
        $this->hourFormat 	= $this->getConfig('hour_format');

        $this->logEmail = true ;
        $this->logFileEmail  = $this->getHelpdezkPath().'/logs/email.log';
    }

    // Since Appil 28, 2017
    public function getHelpdezkPath()
    {
        $path_default = $this->pathDefault;
        if(substr($path_default, 0,1)!='/'){
            $path_default='/'.$path_default;
        }
        if ($path_default == "/..") {
            $path = "";
        } else {
            $path =$path_default;
        }
        // if in localhost document root is D:/xampp/htdocs
        $document_root=$_SERVER['DOCUMENT_ROOT'];
        if(substr($document_root, -1)!='/'){
            $document_root=$document_root.'/';
        }
        return  realpath($document_root.$path) ;
    }

    /**
     * Method to write in log file
     *
     * @author Rogerio Albandes <rogerio.albandes@pipegrep.com.br>
     *
     * @param string  $str String to write
     * @param string  $file  Log filename
     *
     * @since April 28, 2017
     *
     * @return string true|false
     */
    function logit($str, $file)
    {
        if (!file_exists($file)) {
            if($fp = fopen($file, 'a')) {
                @fclose($fp);
                return $this->logit($str, $file);
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

    // Since April 28, 2017
    function getPrintDate()
    {
        return str_replace("%","",$this->dateFormat) . " " . str_replace("%","",$this->hourFormat);

    }

	public function getConfig($param){
		return $this->_config[$param];
	}
	
	public function setConfig($type = null, $value = null) {
        include './includes/config/config.php';
        if($type && $value){
        	$this->_config[$type] = $value;
        }else{
        	$this->_config = $config;
        }

    }

    private function setUrl() {
        $_GET['url'] = (isset($_GET['url']) ? $_GET['url'] : '/admin/');
        $this->_url = $_GET['url'];
        //die($this->_url) ;
        if ($_GET['url'] == 'admin/' || $_GET['url'] == '/admin/') {        	
			$path_default = $this->getConfig("path_default");
			if(substr($path_default, 0,1)!='/'){
			    $path_default='/'.$path_default;
			}
			if ($path_default == "/..") {   
				$path_default = "";
			}			
            header('Location:' . $path_default . '/admin/home');
        }
    }

    private function setExplode() {
        $this->_explode = explode('/', $this->_url);
    }

    private function setModule() {
        $this->_module = $this->_explode[0];
    }

    private function setController() {
        $this->_controller = $this->_explode[1];
    }

    private function setAction() {
        $ac = (!isset($this->_explode[2]) || $this->_explode[2] == NULL || $this->_explode[2] == "index" ? "index" : $this->_explode[2]);
        $this->_action = $ac;
    }

    private function setParams() {
        unset($this->_explode[0], $this->_explode[1], $this->_explode[2]);
        if (end($this->_explode) == NULL) {
            array_pop($this->_explode);
        }
        $i = 0;
        if (!empty($this->_explode)) {
            foreach ($this->_explode as $val) {
                if ($i % 2 == 0) {
                    $ind[] = $val;
                } else {
                    $value[] = $val;
                }
                $i++;
            }
        } else {
            $ind = array();
            $value = array();
        }
        if (count($ind) == count($value) && !empty($ind) && !empty($value)) {
            $this->_params = array_combine($ind, $value);
        } else {
            $this->_params = array();
        }
    }

    public function getParam($name = NULL) {
        if ($name != NULL) {
            return $this->_params[$name];
        } else {
            return $this->_params;
        }
    }

    public function run() {
        $controller_path = CONTROLLERS . $this->_controller . 'Controller.php';

        if (!file_exists($controller_path)) {
            die("O controller não existe: " . $controller_path );
        }
        require_once($controller_path);
        $app = new $this->_controller();
        if (!method_exists($app, $this->_action)) {
            die("A action não existe");
        }
        $action = $this->_action;
        $app->$action();
    }

    public function retornaSmarty() {
        require_once(SMARTY . 'Smarty.class.php');
        $smarty = new Smarty;
        $smarty->debugging = false;
        $smarty->template_dir = VIEWS;
        $smarty->compile_dir = "system/templates_c/";
		$lang_default = $this->getConfig("lang");
		$license =  $this->getConfig("license");
        if (path == "/..") {
            $smarty->config_load(DOCUMENT_ROOT . '/app/lang/' . $lang_default . '.txt', $license);
        } else {
            $smarty->config_load(DOCUMENT_ROOT . path . '/app/lang/' . $lang_default . '.txt', $license);
        }
        
        $smarty->assign('lang', $lang_default);
		$smarty->assign('date_format', $this->getConfig("date_format"));
		$smarty->assign('hour_format', $this->getConfig("hour_format"));
        $smarty->assign('demo', $this->getConfig("demo"));
        $smarty->assign('theme', $this->getConfig("theme"));
        $smarty->assign('path', path);        
        $smarty->assign('pagetitle', $this->getConfig("page_title"));
        return $smarty;
    }

    public function retornaFpdf() {
        require_once(FPDF . 'fpdf.php');
        $pdf = new FPDF;
        return $pdf;
    }

    public function returnPhpExcel()
    {
        require_once DOCUMENT_ROOT . path . '/includes/classes/PHPExcel/PHPExcel.php';
        $objPHPExcel = new PHPExcel();
        return $objPHPExcel;
    }
    /*
    function SetAligns($a){
        //Configura o array dos alinhamentos de coluna
        $this->pdfAligns=$a;
    }
    function SetWidths($w){
        //Configura o array da largura das colunas
        $this->pdfWidths=$w;
    }
    */
    function SetpdfLeftMargin($leftMargin){
        $this->pdfLeftMargin=$leftMargin;
    }
    function SetPdfLogo($logo){
        $this->pdfLogo=$logo;
    }
    function SetPdfPage($page){
        $this->pdfPage=$page;
    }
    function SetPdfTitle($title){
        $this->pdfTitle=$title;
    }
    function SetPdfHeaderData($a_headerData){
        $this->a_pdfHeaderData=$a_headerData;
    }

    public function retornaReportFpdf() {
        require_once DOCUMENT_ROOT . path . '/includes/classes/fpdf/fpdf.php';
        $pdf = new FPDF();
        return $pdf;
    }
    public function ReportPdfHeader($pdf){
        if(file_exists($this->pdfLogo)) {
            $pdf->Image($this->pdfLogo, 10 + $this->pdfLeftMargin, 8);
        }
        //$pdf->AddPage();
        $pdf->Ln(2);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell($this->pdfLeftMargin);
        $pdf->Cell(0, 5, $this->pdfTitle, 0, 0, 'C');
        $pdf->SetFont('Arial', 'I', 6);
        $pdf->Cell(0, 5, $this->pdfPage . ' ' . $pdf->PageNo() . '/{nb}', 0, 0, 'R');
        $pdf->Ln(7);
        $pdf->Cell($this->pdfLeftMargin);
        $pdf->Line($pdf->GetX(), $pdf->GetY(), 198, $pdf->GetY());
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell($this->pdfLeftMargin);
        //$pdf->Cell(0,0,date("d/m/Y"),0,0,'C');
        $pdf->Ln(8);
        return $pdf ;
    }
    public function ReportPdfCabec($pdf)
    {
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetFillColor(211,211,211);
        $pdf->Cell($this->pdfLeftMargin);

        for ($row = 0; $row < count($this->a_pdfHeaderData); $row++) {
            $pdf->Cell($this->a_pdfHeaderData[$row]['width'], 4, $this->a_pdfHeaderData[$row]['title'], 0, 0, $this->a_pdfHeaderData[$row]['align'], 1);
        }

        $pdf->Ln(5);
        return $pdf ;
    }
    public function ReportPdfRow($pdf,$data){
        //Calcula a altura da fila
        $nb=0;
        for($i=0;$i<count($data);$i++)
            $nb=max($nb,$this->ReportPdfNbLines($pdf,$this->a_pdfHeaderData[$i]['width'],$data[$i]));
        $h=5*$nb;
        //Insere um salto de página primeiramente se for necessario
        $this->ReportPdfCheckPageBreak($pdf,$h);
        //Desenha as células da linha
        for($i=0;$i<count($data);$i++){
            $w=$this->a_pdfHeaderData[$i]['width'];
            $a=isset($this->a_pdfHeaderData[$i]['align']) ? $this->a_pdfHeaderData[$i]['align'] : 'C';
            //Salva a posição atual
            $x=$pdf->GetX();
            $y=$pdf->GetY();
            //Draw the border
            $pdf->Rect($x,$y,$w,$h,'F');
            //Imprime o texto
            $pdf->MultiCell($w,5,$data[$i],0,$a);
            //Coloca a posição para a direita da célula
            $pdf->SetXY($x+$w,$y);
        }
        //Va para a próxima linha
        $pdf->Ln($h);
        return $pdf;
    }
    function ReportPdfCheckPageBreak($pdf,$h){
        if($pdf->GetY()+$h>$pdf->PageBreakTrigger) {
            $pdf->AddPage($pdf->CurOrientation);
            $this->ReportPdfHeader($pdf);
            $this->ReportPdfCabec($pdf);
            $pdf->SetFillColor(255,255,255);
        }
    }

    public function ReportPdfNbLines($pdf,$w,$txt){
        //Calcula o número de linhas de uma MultiCell de largura w
        $cw=&$pdf->CurrentFont['cw'];
        if($w==0)
            $w=$this->w-$pdf->rMargin-$pdf->x;
        $wmax=($w-2*$pdf->cMargin)*1000/$pdf->FontSize;
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

    public function parse_ajax($arr) {
        $i = 0;
        $line = array();
        foreach ($arr as &$value) {
            $line[$i] = explode("\t", $value);
            $i++;
        }
        return $line;
    }

    public function access($user, $idprogram, $type) {

        $bd = new common();
        $groupperm = $bd->selectGroupPermission($user, $idprogram );

        $perm = array();


        $first = true;
        while (!$groupperm->EOF) {
            if ($first)	{
                $first = false;
                $flag = false ;
                $program = $groupperm->fields['programname'];
            }
            if ($groupperm->fields['allow'] == 'Y') {
                $flag = true;
            }
            if ($program != $groupperm->fields['programname'] ) {
                $first = true;
                if ($flag){
                    $perm[$program] = 'Y';
                } else {

                }

            }



            /*
            if ($perm[$program] != $groupperm->fields['allow'] ) {
                if ($perm[$program] == 'N') {
                    $perm[$program] = $groupperm->fields['allow'];
                }
            }
            */



            $groupperm->MoveNext();
        }


        $personperm = $bd->selectPersonPermission($user, $idprogram);
		if ($personperm->fields['allow']) {
			while (!$personperm->EOF) {
				$program = $personperm->fields['programname'];
                if ($perm[$program] != $groupperm->fields['allow']) {
                    $perm[$program] = $groupperm->fields['allow'];
                }
				$allow = $personperm->fields['allow'];
				$perm[$program] = $allow;
				$personperm->MoveNext();
			}
		}
        //print "<pre>"; print_r($perm) ; die();
        $string_array = implode('|', $perm);
        ?>
        <script type="text/javascript">
            var i, access, string_array;
            string_array = '<?php echo $string_array; ?>';
                                                                                                                       

            access = string_array.split('|');
        </script>
        <?php

        if(count($perm) > 0){
			$permAccess = array_values($perm);
			if($permAccess[0] != "Y"){
				$smarty = $this->retornaSmarty();
                $dir = str_replace("\\","/", __DIR__) ;
                $path_tpl = str_replace("system","",$dir) ;
                $smarty->display('file:'.$path_tpl.'/app/modules/admin/views/nopermission.tpl.html');
				die();
			}
		}else{
			$smarty = $this->retornaSmarty();
            $dir = str_replace("\\","/", __DIR__) ;
            $path_tpl = str_replace("system","",$dir) ;
            $smarty->display('file:'.$path_tpl.'/app/modules/admin/views/nopermission.tpl.html');
            die();
		}
        
        return $perm;
    }

	public function noAccess($access){
		if(count($access) > 0){
			$permAccess = array_values($access);
			if($permAccess[0] != "Y"){
				$smarty = $this->retornaSmarty();
                $dir = str_replace("\\","/", __DIR__) ;
                $path_tpl = str_replace("system","",$dir) ;
                $smarty->display('file:'.$path_tpl.'/app/modules/admin/views/nopermission.tpl.html');
				die();
			}
		}else{
			$smarty = $this->retornaSmarty();
			$smarty->display('nopermission.tpl.html');
			die();
		}
	}

    public function formatDate($date) {
        $dbCommon = new common();
        $dateafter = $dbCommon->getDate($date, $this->getConfig("date_format"));
        return $dateafter;
    }
	
	public function formatHour($date) {
        $bd = new operatorview_model();
        $dateafter = $bd->getDate($date, $this->getConfig("hour_format"));
        return $dateafter;
    }
	
	public function formatDateHour($date) {
        $bd = new operatorview_model();
        $dateafter = $bd->getDateTime($date, $this->getConfig("date_format")." ".$this->getConfig("hour_format"));
        return $dateafter;
    }

    public function formatSaveDate($date) {
        //$bd = new operatorview_model();
        //$dateafter = $bd->getSaveDate($date, $this->getConfig("date_format"));
        $dbCommon = new common();
        $dateafter = $dbCommon->getSaveDate($date, $this->getConfig("date_format"));
		$database = $this->getConfig('db_connect');
        if ($database == 'mysqlt') {
            return "'".$dateafter."'";
        } elseif ($database == 'oci8po') {
			return $dateafter;
        }
    }
	
	public function formatSaveHour($hour) {
        $bd = new operatorview_model();
        $dateafter = $bd->getSaveHour($hour, $this->getConfig("hour_format"));
        return $dateafter;
    }

	public function formatSaveDateHour($date) {
        $bd = new operatorview_model();
        $dateafter = $bd->getSaveDate($date, $this->getConfig("date_format")." ".$this->getConfig("hour_format"));
        $database = $this->getConfig('db_connect');
        if ($database == 'mysqlt') {
            return "'".$dateafter."'";
        } elseif ($database == 'oci8po') {
			return $dateafter;
        }
    }

    /**
     * Format a value to write in database .
     * @access public
     * @param String $valor Value
     * @return String Formated Value
     **/
    function formatSaValue($value)  {
        $value = str_replace(",",".",str_replace(".","",$value)) ;
        return $value;
    }
    /**
     * Method to write in log file
     *
     * @author Rogerio Albandes <rogerio.albandes@pipegrep.com.br>
     *
     * @param string  $str String to write
     * @param string  $file  Log filename
     *
     * @return string true|false
     */
    public function saveLog($str, $file)
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
            $handle = fopen($file, "a+");
            fwrite($handle, $str."\r\n");
            fclose($handle);
            return true;
        } else {
            return false;
        }
    }

	/**
	* Method to send e-mails
	*
	* @author Rogerio Albandes <rogerio.albandes@pipegrep.com.br>
	*
	* @param string  $subject E-mail subject
	* @param string  $body  E-mail body
	* @param array   $address Addreaesse 
	* @param boolean $log If it will log
	* @param string $log_text Log text 
	*
	* @return string true|false 
	*/
    public function sendEmailDefault($subject,$body,$address,$log = false, $log_text) 
	{        
        $data = new features_model();
        $emconfigs = $data->getEmailConfigs();
        $tempconfs = $data->getTempEmail();
        
        $mail_host 		= $emconfigs['EM_HOSTNAME'];
        $mail_dominio 	= $emconfigs['EM_DOMAIN'];
        
        $mail_header 	= $tempconfs['EM_HEADER'];
        $mail_footer 	= $tempconfs['EM_FOOTER'];
		
        require_once("includes/classes/phpMailer/class.phpmailer.php");
        $mail = new phpmailer();
		
        $mail->From = $emconfigs['EM_SENDER'];
        $mail->FromName = 'HelpDEZk';
        if ($mail_host)
            $mail->Host = $mail_host;
        if (isset($mail_porta) AND !empty($mail_porta)) {
            $mail->Port = $mail_porta;
        }
		
        $mail->Mailer 	= 'smtp';
        $mail->SMTPAuth = $emconfigs['EM_AUTH'];
        $mail->Username = $emconfigs['EM_USER'];
        $mail->Password = $emconfigs['EM_PASSWORD'];
        $mail->Body 	= $mail_header . $body . $mail_footer;
        $mail->AltBody 	= "HTML";
        $mail->Subject 	= $subject;

		$exist = array();
		if (is_array($address) )
		{
			foreach ($address as $v) {
				if (!in_array($v, $exist)) {
					$mail->AddAddress($v);
					$exist[] = $v;
				}
			}
		} 
		else
		{
		
		}

		$done = $mail->Send();
 
        if (!$done) 
		{ 
            if ($log) {
                $mail->SMTPDebug = true;
                $mail->Send();
				
				if (path == "/..") {
					$file = fopen(DOCUMENT_ROOT . '/logs/email_failures.txt', 'ab');
				} else {
					$file = fopen(DOCUMENT_ROOT . path .  '/logs/email_failures.txt', 'ab');
				}	
                
				if ($file) {
					$msg = date("Y-m-d H:i:s");
					$msg .= " " . $mail->ErrorInfo;
					$msg .= $log_text . "\r\n";
					fwrite($file, $msg);
					fclose($file);
				}
			}
		} 
		else 
		{
			if ($log) {
				if (path == "/..") {
					$file = fopen(DOCUMENT_ROOT . '/logs/email_success.txt', 'ab');
				} else {
					$file = fopen(DOCUMENT_ROOT . path .  '/logs/email_success.txt', 'ab');
				}			

				if ($file) {
					$msg = date("Y-m-d H:i:s");
					$msg .= " SUCCESFULLY SENT  - ";
					$msg .= $msg .= $log_text . "\r\n"; 
					fwrite($file, $msg);
					fclose($file);
				}
			}
		}
        $mail->ClearAllRecipients();
        $mail->ClearAttachments();
	}	
	
    public function sendEmail($operation, $code_request, $reason = NULL) {

        $hdk_url = $this->getConfig('hdk_url');
        $smarty = $this->retornaSmarty();
        $bd = new emailconfig_model();
        if (!isset($operation)) {
            print("Email code not provided");
            return false;
        }
        $destinatario = "";
        //## ENVIA E-MAIL PARA O GRUPO AO REGISTRAR UMA SOLICITACAO ##===
        switch ($operation) {
            case "record":
				$COD_RECORD = $bd->getEmailIdBySession("NEW_REQUEST_OPERATOR_MAIL");				
                //$COD_RECORD = "16"; // Esse é o padrão

                $rsTemplate = $bd->getTemplateData($COD_RECORD);

                $bdop = new operatorview_model();
                $reqdata = $bdop->getRequestData($code_request);

                $REQUEST = $code_request;
                $SUBJECT = $reqdata->fields['subject'];
                $REQUESTER = $reqdata->fields['personname'];
                $RECORD = $this->formatDate($reqdata->fields['entry_date']);
                $DESCRIPTION = $reqdata->fields['description'];
                $INCHARGE = $reqdata->fields['in_charge'];
                $PHONE = $reqdata->fields['phone'];
                $BRANCH = $reqdata->fields['branch'];
				$LINK_OPERATOR = "<a href='".$hdk_url."helpdezk/operator#/operator/viewrequest/id/".$code_request."' target='_blank'>".$code_request."</a>";
				$LINK_USER = "<a href='".$hdk_url."helpdezk/user#/user/viewrequest/id/".$code_request."' target='_blank'>".$code_request."</a>";
				
                $notes = $bdop->getRequestNotes($code_request);

                $table = "<table width='100%'  border='0' cellspacing='3' cellpadding='0'>";
                while (!$notes->EOF) {
                    $table.= "<tr><td height=28><font size=2 face=arial>";
                    $table.= $this->formatDate($notes->fields['entry_date']) . " [" . $notes->fields["name"] . "] " . str_replace(chr(10), "<BR>", strip_tags($notes->fields["description"]));
                    $table.= "</font><br></td></tr>";
                    $notes->MoveNext();
                }
                $table.= "</table>";

                $NT_OPERATOR = $table;

                //           ---------------------------------------------------------------------

                $conteudo = str_replace(chr(10), "<br>", $rsTemplate->fields("description"));
                $conteudo = str_replace('"', "'", $rsTemplate->fields("description")) . "<br/>";
                eval("\$conteudo = \"$conteudo\";");

                $rsGroup = $bd->getGroupInCharge($code_request);
                $inchType = $rsGroup->fields['type'];
                $inchid = $rsGroup->fields['id_in_charge'];

                if ($inchType == 'G') {
                    $grpEmails = $bd->getEmailsfromGroupOperators($inchid);
                    while (!$grpEmails->EOF) {
                        if (!$destinatario) {
                            $destinatario = $grpEmails->Fields('email');
                        } else {
                            $destinatario .= ";" . $grpEmails->Fields('email');
                        }
                        $grpEmails->MoveNext();
                    }
                } else {
                    $userEmail = $bd->getUserEmail($inchid);
                    $destinatario = $userEmail->Fields('email');
                }
                $assunto = $rsTemplate->fields['name'];
                eval("\$assunto = \"$assunto\";");
                

                break;

            case 'assume':
				$COD_ASSUME = $bd->getEmailIdBySession("NEW_ASSUMED_MAIL");
                //$COD_ASSUME = "1";
                $rsTemplate = $bd->getTemplateData($COD_ASSUME);

                $bdop = new operatorview_model();
                $reqdata = $bdop->getRequestData($code_request);
				
				$reqEmail = $bd->getRequesterEmail($code_request);
                $destinatario = $reqEmail->fields['email'];
				$typeuser = $reqEmail->fields['idtypeperson'];

                $REQUEST = $code_request;
                $SUBJECT = $reqdata->fields['subject'];
                $REQUESTER = $reqdata->fields['personname'];
                $RECORD = $this->formatDate($reqdata->fields['entry_date']);
                $DESCRIPTION = $reqdata->fields['description'];
                $INCHARGE = $reqdata->fields['in_charge'];
                $PHONE = $reqdata->fields['phone'];
                $BRANCH = $reqdata->fields['branch'];
				$LINK_OPERATOR = "<a href='".$hdk_url."helpdezk/operator#/operator/viewrequest/id/".$code_request."' target='_blank'>".$code_request."</a>";
				if($typeuser == 2)
					$LINK_USER = "<a href='".$hdk_url."helpdezk/user#/user/viewrequest/id/".$code_request."' target='_blank'>".$code_request."</a>";
				else
                	$LINK_USER = "<a href='".$hdk_url."helpdezk/operator#/user/viewrequest/id/".$code_request."' target='_blank'>".$code_request."</a>";
				
					
                $date = date('Y-m-d H:i');
                $ASSUME = $this->formatDate($date);

                $notes = $bdop->getRequestNotes($code_request);

                $table = "<table width='100%'  border='0' cellspacing='3' cellpadding='0'>";
                while (!$notes->EOF) {
                    $table.= "<tr><td height=28><font size=2 face=arial>";
                    $table.= $this->formatDate($notes->fields['entry_date']) . " [" . $notes->fields["name"] . "] " . str_replace(chr(10), "<BR>", strip_tags($notes->fields["description"]));
                    $table.= "</font><br></td></tr>";
                    $notes->MoveNext();
                }
                $table.= "</table>";

                $NT_USER = $table;

                $conteudo = str_replace(chr(10), "<br>", $rsTemplate->fields("description"));
                $conteudo = str_replace('"', "'", $rsTemplate->fields("description")) . "<br/>";
                eval("\$conteudo = \"$conteudo\";");

                $assunto = $rsTemplate->Fields("name");
                eval("\$assunto = \"$assunto\";");
                //$destinatario = $rsMail->Fields("DES_EMAIL");

                break;

            case 'close':
				$COD_CLOSE = $bd->getEmailIdBySession("FINISH_MAIL");
                //$COD_CLOSE = "2";
                $rsTemplate = $bd->getTemplateData($COD_CLOSE);

                $bdop = new operatorview_model();
                $reqdata = $bdop->getRequestData($code_request);

				$reqEmail = $bd->getRequesterEmail($code_request);
                $destinatario = $reqEmail->fields['email'];
				$typeuser = $reqEmail->fields['idtypeperson'];
				
                $REQUEST = $code_request;
                $SUBJECT = $reqdata->fields['subject'];
                $REQUESTER = $reqdata->fields['personname'];
                $RECORD = $this->formatDate($reqdata->fields['entry_date']);
                $DESCRIPTION = $reqdata->fields['description'];
                $INCHARGE = $reqdata->fields['in_charge'];
                $PHONE = $reqdata->fields['phone'];
                $BRANCH = $reqdata->fields['branch'];
                $date = date('Y-m-d H:i');
                $FINISH_DATE = $this->formatDate($date);
				
				$ev = new evaluation_model();
				$tk = $ev->getToken($code_request);
				$token = $tk->fields['token'];
				if($token)
					$LINK_EVALUATE =  $hdk_url."helpdezk/evaluate/index/token/".$token;
				
				
				$LINK_OPERATOR = "<a href='".$hdk_url."helpdezk/operator#/operator/viewrequest/id/".$code_request."' target='_blank'>".$code_request."</a>";
				if($typeuser == 2)
					$LINK_USER = "<a href='".$hdk_url."helpdezk/user#/user/viewrequest/id/".$code_request."' target='_blank'>".$code_request."</a>";
				else
                	$LINK_USER = "<a href='".$hdk_url."helpdezk/operator#/user/viewrequest/id/".$code_request."' target='_blank'>".$code_request."</a>";
				
                $notes = $bdop->getRequestNotes($code_request);

                $table = "<table width='100%'  border='0' cellspacing='3' cellpadding='0'>";
                while (!$notes->EOF) {
                    $table.= "<tr><td height=28><font size=2 face=arial>";
                    $table.= $this->formatDate($notes->fields['entry_date']) . " [" . $notes->fields["name"] . "] " . str_replace(chr(10), "<BR>", strip_tags($notes->fields["description"]));
                    $table.= "</font><br></td></tr>";
                    $notes->MoveNext();
                }
                $table.= "</table>";

                $NT_USER = $table;

                $conteudo = str_replace(chr(10), "<br>", $rsTemplate->fields("description"));
                $conteudo = str_replace('"', "'", $rsTemplate->fields("description")) . "<br/>";
                eval("\$conteudo = \"$conteudo\";");

                $assunto = $rsTemplate->Fields('name');
                eval("\$assunto = \"$assunto\";");
                //$destinatario = $rsMail->Fields("DES_EMAIL");

                break;

            case 'reject':
				$COD_REJECT = $bd->getEmailIdBySession("REJECTED_MAIL");
                //$COD_REJECT = "3";
                $rsTemplate = $bd->getTemplateData($COD_REJECT);				
				
                $bdop = new operatorview_model();
                $reqdata = $bdop->getRequestData($code_request);
				
				$reqEmail = $bd->getRequesterEmail($code_request);
				$destinatario = $reqEmail->fields['email'];
                
				$typeuser = $reqEmail->fields['idtypeperson'];
				
                $REQUEST = $code_request;
                $SUBJECT = $reqdata->fields['subject'];
                $REQUESTER = $reqdata->fields['personname'];
                $RECORD = $this->formatDate($reqdata->fields['entry_date']);
                $DESCRIPTION = $reqdata->fields['description'];
                $INCHARGE = $reqdata->fields['in_charge'];
                $PHONE = $reqdata->fields['phone'];
                $BRANCH = $reqdata->fields['branch'];
                $date = date('Y-m-d H:i');
                $REJECTION = $this->formatDate($date);
				$LINK_OPERATOR = "<a href='".$hdk_url."helpdezk/operator#/operator/viewrequest/id/".$code_request."' target='_blank'>".$code_request."</a>";
				if($typeuser == 2)
					$LINK_USER = "<a href='".$hdk_url."helpdezk/user#/user/viewrequest/id/".$code_request."' target='_blank'>".$code_request."</a>";
				else
                	$LINK_USER = "<a href='".$hdk_url."helpdezk/operator#/user/viewrequest/id/".$code_request."' target='_blank'>".$code_request."</a>";
				
                $notes = $bdop->getRequestNotes($code_request);

                $table = "<table width='100%'  border='0' cellspacing='3' cellpadding='0'>";
                $USER = $notes->fields["name"];
                while (!$notes->EOF) {
                    $table.= "<tr><td height=28><font size=2 face=arial>";
                    $table.= $this->formatDate($notes->fields['entry_date']) . " [" . $notes->fields["name"] . "] " . str_replace(chr(10), "<BR>", strip_tags($notes->fields["description"]));
                    $table.= "</font><br></td></tr>";
                    $notes->MoveNext();
                }
                $table.= "</table>";

                $NT_USER = $table;

                //require_once('../includes/solicitacao_detalhe.php');
                $conteudo = str_replace(chr(10), "<br>", $rsTemplate->Fields("description"));
                $conteudo = str_replace('"', "'", $rsTemplate->Fields("description"));
                eval("\$conteudo = \"$conteudo\";");


                $motivo = "<u>" . $l_eml["lb_motivo_rejeicao"] . "</u> " . $reason;
                $goto = ('usuario/solicita_detalhes.php?COD_SOLICITACAO=' . $COD_SOLICITACAO);
                $url = '<a href="' . $url_helpdesk . 'index.php?url=' . urlencode($goto) . '">' . $l_eml["link_solicitacao"] . '</a>';

                $assunto = $rsTemplate->Fields("name");
                eval("\$assunto = \"$assunto\";");
                //$destinatario = $rsMail->Fields("DES_EMAIL");

                break;

            case 'user_note' :
				$COD_ASSUME = $bd->getEmailIdBySession("USER_NEW_NOTE_MAIL");
                //$COD_ASSUME = "13";
                $rsTemplate = $bd->getTemplateData($COD_ASSUME);

                $bdop = new operatorview_model();
                $reqdata = $bdop->getRequestData($code_request);
				
				$reqEmail = $bd->getRequesterEmail($code_request);
                $destinatario = $reqEmail->fields['email'];
				$typeuser = $reqEmail->fields['idtypeperson'];
				
                $REQUEST = $code_request;
                $SUBJECT = $reqdata->fields['subject'];
                $REQUESTER = $reqdata->fields['personname'];
                $RECORD = $this->formatDate($reqdata->fields['entry_date']);
                $DESCRIPTION = $reqdata->fields['description'];
                $INCHARGE = $reqdata->fields['in_charge'];
                $PHONE = $reqdata->fields['phone'];
                $BRANCH = $reqdata->fields['branch'];
                $date = date('Y-m-d H:i');
                $FINISH_DATE = $this->formatDate($date);
				$LINK_OPERATOR = "<a href='".$hdk_url."helpdezk/operator#/operator/viewrequest/id/".$code_request."' target='_blank'>".$code_request."</a>";
				if($typeuser == 2)
					$LINK_USER = "<a href='".$hdk_url."helpdezk/user#/user/viewrequest/id/".$code_request."' target='_blank'>".$code_request."</a>";
				else
                	$LINK_USER = "<a href='".$hdk_url."helpdezk/operator#/user/viewrequest/id/".$code_request."' target='_blank'>".$code_request."</a>";
				
				
                $notes = $bdop->getRequestNotes($code_request);

                $table = "<table width='100%'  border='0' cellspacing='3' cellpadding='0'>";
                while (!$notes->EOF) {
                	if($notes->fields['idtype'] != 2){
	                    $table.= "<tr><td height=28><font size=2 face=arial>";
	                    $table.= $this->formatDate($notes->fields['entry_date']) . " [" . $notes->fields["name"] . "] " . str_replace(chr(10), "<BR>", strip_tags($notes->fields["description"]));
	                    $table.= "</font><br></td></tr>";
					}
                    $notes->MoveNext();
                }
                $table.= "</table>";

                $NT_USER = $table;


                $rsGroup = $bd->getGroupInCharge($code_request);
                $inchType = $rsGroup->fields['type'];
                $inchid = $rsGroup->fields['id_in_charge'];

             
                $conteudo = str_replace(chr(10), "<br>", $rsTemplate->fields("description"));
                $conteudo = str_replace('"', "'", $rsTemplate->fields("description")) . "<br/>";
                eval("\$conteudo = \"$conteudo\";");


                $assunto = $rsTemplate->Fields('name');
                eval("\$assunto = \"$assunto\";");
                //$destinatario = $rsMail->Fields("DES_EMAIL");
                
                if($_SESSION['SES_ATTACHMENT_OPERATOR_NOTE']){
                	if (path == "/..") {
	                    $file = DOCUMENT_ROOT . '/app/uploads/helpdezk/noteattachments/';
					} else {
	                    $file = DOCUMENT_ROOT . path .  '/app/uploads/helpdezk/noteattachments/';
	                }					
                	$attachment = $bdop->getNoteAttachment($code_request);
					if($attachment->fields['idnote_attachment'] && $attachment->fields['file_name']){
						$attachment_name = $attachment->fields['file_name'];
						$ext = strrchr($attachment_name, '.');
						$attachment_dest = $file.$attachment->fields['idnote_attachment'].$ext;						
					}					
                }

                break;

            case 'operator_note' :
				$COD_ASSUME = $bd->getEmailIdBySession("OPERATOR_NEW_NOTE");
                //$COD_ASSUME = "43";
                $rsTemplate = $bd->getTemplateData($COD_ASSUME);

                $bdop = new operatorview_model();
                $reqdata = $bdop->getRequestData($code_request);

                $REQUEST = $code_request;
                $SUBJECT = $reqdata->fields['subject'];
                $REQUESTER = $reqdata->fields['personname'];
                $RECORD = $this->formatDate($reqdata->fields['entry_date']);
                $DESCRIPTION = $reqdata->fields['description'];
                $INCHARGE = $reqdata->fields['in_charge'];
                $PHONE = $reqdata->fields['phone'];
                $BRANCH = $reqdata->fields['branch'];
                $date = date('Y-m-d H:i');
                $FINISH_DATE = $this->formatDate($date);
				$LINK_OPERATOR = "<a href='".$hdk_url."helpdezk/operator#/operator/viewrequest/id/".$code_request."' target='_blank'>".$code_request."</a>";
				$LINK_USER = "<a href='".$hdk_url."helpdezk/user#/user/viewrequest/id/".$code_request."' target='_blank'>".$code_request."</a>";
								
                $notes = $bdop->getRequestNotes($code_request);

                $table = "<table width='100%'  border='0' cellspacing='3' cellpadding='0'>";
                while (!$notes->EOF) {
                    $table.= "<tr><td height=28><font size=2 face=arial>";
                    $table.= $this->formatDate($notes->fields['entry_date']) . " [" . $notes->fields["name"] . "] " . str_replace(chr(10), "<BR>", strip_tags($notes->fields["description"]));
                    $table.= "</font><br></td></tr>";
                    $notes->MoveNext();
                }
                $table.= "</table>";

                $NT_USER = $table;

                $conteudo = str_replace(chr(10), "<br>", $rsTemplate->fields("description"));
                $conteudo = str_replace('"', "'", $rsTemplate->fields("description")) . "<br/>";
                eval("\$conteudo = \"$conteudo\";");


                $rsGroup = $bd->getGroupInCharge($code_request);
                $inchType = $rsGroup->fields['type'];
                $inchid = $rsGroup->fields['id_in_charge'];


                if ($inchType == 'G') {
                    $grpEmails = $bd->getEmailsfromGroupOperators($inchid);
                    while (!$grpEmails->EOF) {
                        if (!$destinatario) {
                            $destinatario = $grpEmails->Fields('email');
                        } else {
                            $destinatario .= ";" . $grpEmails->Fields('email');
                        }
                        $grpEmails->MoveNext();
                    }
                } else {




                    $userEmail = $bd->getUserEmail($inchid);
                    $destinatario = $userEmail->fields['email'];
                }

                $assunto = $rsTemplate->Fields('name');
                eval("\$assunto = \"$assunto\";");
                //$destinatario = $rsMail->Fields("DES_EMAIL");

                break;

            case 'reopen':
				$COD_ASSUME = $bd->getEmailIdBySession("REQUEST_REOPENED");
               // $COD_ASSUME = "8";
                $rsTemplate = $bd->getTemplateData($COD_ASSUME);

                $bdop = new operatorview_model();
                $reqdata = $bdop->getRequestData($code_request);

                $REQUEST = $code_request;
                $SUBJECT = $reqdata->fields['subject'];
                $REQUESTER = $reqdata->fields['personname'];
                $RECORD = $this->formatDate($reqdata->fields['entry_date']);
                $DESCRIPTION = $reqdata->fields['description'];
                $INCHARGE = $reqdata->fields['in_charge'];
                $PHONE = $reqdata->fields['phone'];
                $BRANCH = $reqdata->fields['branch'];
                $date = date('Y-m-d H:i');
                $DATE = $this->formatDate($date);
				$LINK_OPERATOR = "<a href='".$hdk_url."helpdezk/operator#/operator/viewrequest/id/".$code_request."' target='_blank'>".$code_request."</a>";
				$LINK_USER = "<a href='".$hdk_url."helpdezk/user#/user/viewrequest/id/".$code_request."' target='_blank'>".$code_request."</a>";
				
                $notes = $bdop->getRequestNotes($code_request);

                $table = "<table width='100%'  border='0' cellspacing='3' cellpadding='0'>";
                while (!$notes->EOF) {
                    $table.= "<tr><td height=28><font size=2 face=arial>";
                    $table.= $this->formatDate($notes->fields['entry_date']) . " [" . $notes->fields["name"] . "] " . str_replace(chr(10), "<BR>", strip_tags($notes->fields["description"]));
                    $table.= "</font><br></td></tr>";
                    $notes->MoveNext();
                }
                $table.= "</table>";

                $NT_USER = $table;

                $conteudo = str_replace(chr(10), "<br>", $rsTemplate->fields("description"));
                $conteudo = str_replace('"', "'", $rsTemplate->fields("description")) . "<br/>";
                eval("\$conteudo = \"$conteudo\";");

                $rsGroup = $bd->getGroupInCharge($code_request);
                $inchType = $rsGroup->fields['type'];
                $inchid = $rsGroup->fields['id_in_charge'];

                if ($inchType == 'G') {
                    $grpEmails = $bd->getEmailsfromGroupOperators($inchid);
                    while (!$grpEmails->EOF) {
                        if (!$destinatario) {
                            $destinatario = $grpEmails->Fields('email');
                        } else {
                            $destinatario .= ";" . $grpEmails->Fields('email');
                        }
                        $grpEmails->MoveNext();
                    }
                } else {
                    $userEmail = $bd->getUserEmail($inchid);
                    $destinatario = $userEmail->fields['email'];
                }

                $assunto = $rsTemplate->Fields('name');
                eval("\$assunto = \"$assunto\";");
                //$destinatario = $rsMail->Fields("DES_EMAIL");

                break;
                
			case "afterevaluate":
				$COD_RECORD = $bd->getEmailIdBySession("EM_EVALUATED");
                //$COD_RECORD = "4"; // Esse é o padrão

                $rsTemplate = $bd->getTemplateData($COD_RECORD);

                $bdop = new operatorview_model();
                $reqdata = $bdop->getRequestData($code_request);

                $EVALUATION = $bdop->getEvaluationGiven($code_request);
                $REQUEST = $code_request;
                $SUBJECT = $reqdata->fields['subject'];
                $REQUESTER = $reqdata->fields['personname'];
                $RECORD = $this->formatDate($reqdata->fields['entry_date']);
                $DESCRIPTION = $reqdata->fields['description'];
                $INCHARGE = $reqdata->fields['in_charge'];
                $PHONE = $reqdata->fields['phone'];
                $BRANCH = $reqdata->fields['branch'];
				$LINK_OPERATOR = "<a href='".$hdk_url."helpdezk/operator#/operator/viewrequest/id/".$code_request."' target='_blank'>".$code_request."</a>";
				$LINK_USER = "<a href='".$hdk_url."helpdezk/user#/user/viewrequest/id/".$code_request."' target='_blank'>".$code_request."</a>";
				
                $notes = $bdop->getRequestNotes($code_request);

                $table = "<table width='100%'  border='0' cellspacing='3' cellpadding='0'>";
                while (!$notes->EOF) {
                    $table.= "<tr><td height=28><font size=2 face=arial>";
                    $table.= $this->formatDate($notes->fields['entry_date']) . " [" . $notes->fields["name"] . "] " . str_replace(chr(10), "<BR>", strip_tags($notes->fields["description"]));
                    $table.= "</font><br></td></tr>";
                    $notes->MoveNext();
                }
                $table.= "</table>";

                $NT_OPERATOR = $table;

                //           ---------------------------------------------------------------------

                $conteudo = str_replace(chr(10), "<br>", $rsTemplate->fields("description"));
                $conteudo = str_replace('"', "'", $rsTemplate->fields("description")) . "<br/>";
                eval("\$conteudo = \"$conteudo\";");

                $rsGroup = $bd->getGroupInCharge($code_request);
                $inchType = $rsGroup->fields['type'];
                $inchid = $rsGroup->fields['id_in_charge'];

                if ($inchType == 'G') {
                    $grpEmails = $bd->getEmailsfromGroupOperators($inchid);
                    while (!$grpEmails->EOF) {
                        if (!$destinatario) {
                            $destinatario = $grpEmails->Fields('email');
                        } else {
                            $destinatario .= ";" . $grpEmails->Fields('email');
                        }
                        $grpEmails->MoveNext();
                    }
                } else {
                    $userEmail = $bd->getUserEmail($inchid);
                    $destinatario = $userEmail->Fields('email');
                }

                $assunto = $rsTemplate->fields['name'];
                eval("\$assunto = \"$assunto\";");

                break;
        
			case "repass":
				$COD_RECORD = $bd->getEmailIdBySession("REPASS_REQUEST_OPERATOR_MAIL");
                //$COD_RECORD = "82"; // Esse é o padrão

                $rsTemplate = $bd->getTemplateData($COD_RECORD);

                $bdop = new operatorview_model();
                $reqdata = $bdop->getRequestData($code_request);

                $REQUEST = $code_request;
                $SUBJECT = $reqdata->fields['subject'];
                $REQUESTER = $reqdata->fields['personname'];
                $RECORD = $this->formatDate($reqdata->fields['entry_date']);
                $DESCRIPTION = $reqdata->fields['description'];
                $INCHARGE = $reqdata->fields['in_charge'];
                $PHONE = $reqdata->fields['phone'];
                $BRANCH = $reqdata->fields['branch'];
				$LINK_OPERATOR = "<a href='".$hdk_url."helpdezk/operator#/operator/viewrequest/id/".$code_request."' target='_blank'>".$code_request."</a>";
				$LINK_USER = "<a href='".$hdk_url."helpdezk/user#/user/viewrequest/id/".$code_request."' target='_blank'>".$code_request."</a>";
				
                $notes = $bdop->getRequestNotes($code_request);

                $table = "<table width='100%'  border='0' cellspacing='3' cellpadding='0'>";
                while (!$notes->EOF) {
                    $table.= "<tr><td height=28><font size=2 face=arial>";
                    $table.= $this->formatDate($notes->fields['entry_date']) . " [" . $notes->fields["name"] . "] " . str_replace(chr(10), "<BR>", strip_tags($notes->fields["description"]));
                    $table.= "</font><br></td></tr>";
                    $notes->MoveNext();
                }
                $table.= "</table>";

                $NT_OPERATOR = $table;

                //           ---------------------------------------------------------------------

                $conteudo = str_replace(chr(10), "<br>", $rsTemplate->fields("description"));
                $conteudo = str_replace('"', "'", $rsTemplate->fields("description")) . "<br/>";
                eval("\$conteudo = \"$conteudo\";");

                $rsGroup = $bd->getGroupInCharge($code_request);
                $inchType = $rsGroup->fields['type'];
                $inchid = $rsGroup->fields['id_in_charge'];

                if ($inchType == 'G') {
                    $grpEmails = $bd->getEmailsfromGroupOperators($inchid);
                    while (!$grpEmails->EOF) {
                        if (!$destinatario) {
                            $destinatario = $grpEmails->Fields('email');
                        } else {
                            $destinatario .= ";" . $grpEmails->Fields('email');
                        }
                        $grpEmails->MoveNext();
                    }
                } else {
                    $userEmail = $bd->getUserEmail($inchid);
                    $destinatario = $userEmail->Fields('email');
                }

                $assunto = $rsTemplate->fields['name'];
                eval("\$assunto = \"$assunto\";");
                

			break;

            case "approve":
				$COD_RECORD = $bd->getEmailIdBySession("SES_REQUEST_APPROVE");
                //$COD_RECORD = "83"; // Esse é o padrão

                $rsTemplate = $bd->getTemplateData($COD_RECORD);

                $bdop = new operatorview_model();
                $reqdata = $bdop->getRequestData($code_request);

                $REQUEST = $code_request;
                $SUBJECT = $reqdata->fields['subject'];
                $REQUESTER = $reqdata->fields['personname'];
                $RECORD = $this->formatDate($reqdata->fields['entry_date']);
                $DESCRIPTION = $reqdata->fields['description'];
                $INCHARGE = $reqdata->fields['in_charge'];
                $PHONE = $reqdata->fields['phone'];
                $BRANCH = $reqdata->fields['branch'];
				$LINK_OPERATOR = "<a href='".$hdk_url."helpdezk/operator#/operator/viewrequest/id/".$code_request."' target='_blank'>".$code_request."</a>";
				$LINK_USER = "<a href='".$hdk_url."helpdezk/user#/user/viewrequest/id/".$code_request."' target='_blank'>".$code_request."</a>";

                $notes = $bdop->getRequestNotes($code_request);

                $table = "<table width='100%'  border='0' cellspacing='3' cellpadding='0'>";
                while (!$notes->EOF) {
                    $table.= "<tr><td height=28><font size=2 face=arial>";
                    $table.= $this->formatDate($notes->fields['entry_date']) . " [" . $notes->fields["name"] . "] " . str_replace(chr(10), "<BR>", strip_tags($notes->fields["description"]));
                    $table.= "</font><br></td></tr>";
                    $notes->MoveNext();
                }
                $table.= "</table>";

                $NT_OPERATOR = $table;

                //           ---------------------------------------------------------------------

                $conteudo = str_replace(chr(10), "<br>", $rsTemplate->fields("description"));
                $conteudo = str_replace('"', "'", $rsTemplate->fields("description")) . "<br/>";
                eval("\$conteudo = \"$conteudo\";");

                $rsGroup = $bd->getGroupInCharge($code_request);
                $inchType = $rsGroup->fields['type'];
                $inchid = $rsGroup->fields['id_in_charge'];

                if ($inchType == 'G') {
                    $grpEmails = $bd->getEmailsfromGroupOperators($inchid);
                    while (!$grpEmails->EOF) {
                        if (!$destinatario) {
                            $destinatario = $grpEmails->Fields('email');
                        } else {
                            $destinatario .= ";" . $grpEmails->Fields('email');
                        }
                        $grpEmails->MoveNext();
                    }
                } else {
                    $userEmail = $bd->getUserEmail($inchid);
                    $destinatario = $userEmail->Fields('email');
                }

                $assunto = $rsTemplate->fields['name'];
                eval("\$assunto = \"$assunto\";");
                

                break;
		
			case "operator_reject":
				$COD_REJECT = $bd->getEmailIdBySession("SES_MAIL_OPERATOR_REJECT");
				//$COD_REJECT = "84";
                $rsTemplate = $bd->getTemplateData($COD_REJECT);
			
                $bdop = new operatorview_model();
                $reqdata = $bdop->getRequestData($code_request);
				
				$grpEmails = $bd->getEmailsfromGroupOperators($_SESSION['SES_MAIL_OPERATOR_REJECT_ID']);
                while (!$grpEmails->EOF) {
                    if (!$destinatario) {
                        $destinatario = $grpEmails->Fields('email');
                    } else {
                        $destinatario .= ";" . $grpEmails->Fields('email');
                    }
                    $grpEmails->MoveNext();
                }
                
				$typeuser = $reqEmail->fields['idtypeperson'];
				
                $REQUEST = $code_request;
                $SUBJECT = $reqdata->fields['subject'];
                $REQUESTER = $reqdata->fields['personname'];
                $RECORD = $this->formatDate($reqdata->fields['entry_date']);
                $DESCRIPTION = $reqdata->fields['description'];
                $INCHARGE = $reqdata->fields['in_charge'];
                $PHONE = $reqdata->fields['phone'];
                $BRANCH = $reqdata->fields['branch'];
                $date = date('Y-m-d H:i');
                $REJECTION = $this->formatDate($date);
				$LINK_OPERATOR = "<a href='".$hdk_url."helpdezk/operator#/operator/viewrequest/id/".$code_request."' target='_blank'>".$code_request."</a>";
				if($typeuser == 2)
					$LINK_USER = "<a href='".$hdk_url."helpdezk/user#/user/viewrequest/id/".$code_request."' target='_blank'>".$code_request."</a>";
				else
                	$LINK_USER = "<a href='".$hdk_url."helpdezk/operator#/user/viewrequest/id/".$code_request."' target='_blank'>".$code_request."</a>";
				
                $notes = $bdop->getRequestNotes($code_request);

                $table = "<table width='100%'  border='0' cellspacing='3' cellpadding='0'>";
                $USER = $notes->fields["name"];
                while (!$notes->EOF) {
                    $table.= "<tr><td height=28><font size=2 face=arial>";
                    $table.= $this->formatDate($notes->fields['entry_date']) . " [" . $notes->fields["name"] . "] " . str_replace(chr(10), "<BR>", strip_tags($notes->fields["description"]));
                    $table.= "</font><br></td></tr>";
                    $notes->MoveNext();
                }
                $table.= "</table>";

                $NT_OPERATOR = $table;

                $conteudo = str_replace(chr(10), "<br>", $rsTemplate->Fields("description"));
                $conteudo = str_replace('"', "'", $rsTemplate->Fields("description"));
                eval("\$conteudo = \"$conteudo\";");

                $motivo = "<u>" . $l_eml["lb_motivo_rejeicao"] . "</u> " . $reason;

                $assunto = $rsTemplate->Fields("name");
                eval("\$assunto = \"$assunto\";");
				
			break;
	
		}


        $bd = new features_model();
        $emconfigs = $bd->getEmailConfigs();
        $tempconfs = $bd->getTempEmail();

        $nom_titulo = $emconfigs['EM_TITLE'];
        $mail_metodo = 'smtp';
        $mail_host = $emconfigs['EM_HOSTNAME'];
        $mail_dominio = $emconfigs['EM_DOMAIN'];
        $mail_auth = $emconfigs['EM_AUTH'];
        $mail_username = $emconfigs['EM_USER'];
        $mail_password = $emconfigs['EM_PASSWORD'];
        $mail_remetente = $emconfigs['EM_SENDER'];
        $mail_cabecalho = $tempconfs['EM_HEADER'];
        $mail_rodape = $tempconfs['EM_FOOTER'];        
        $mail_port  = $emconfigs['EM_PORT'];

        // print("HOST: $mail_host DOMAIN: $mail_dominio AUTH: $mail_auth USER: $mail_username PASS: $mail_password SENDER: $mail_remetente CABEÇ: $mail_cabecalho RODA: $mail_rodape <BR/>");


        require_once("includes/classes/phpMailer/class.phpmailer.php");
        $mail = new phpmailer();
        $mail->From = $mail_remetente;
        $mail->FromName = $nom_titulo;
        if ($mail_host)
            $mail->Host = $mail_host;
        if (isset($mail_port) AND !empty($mail_port)) {
            $mail->Port = $mail_port;
        }

        $mail->Mailer = $mail_metodo;
        $mail->SMTPAuth = $mail_auth;
        if (strpos($mail_username,'gmail') !== false) {
            $mail->SMTPSecure = "tls";
        }
        $mail->Username = $mail_username;
        $mail->Password = $mail_password;
        $mail->Body = $mail_cabecalho . $conteudo . $mail_rodape;
        $mail->AltBody = "HTML";
        $mail->Subject = utf8_decode($assunto);
        
		if($attachment_dest && $attachment_name){
			$mail->AddAttachment($attachment_dest, $attachment_name);
			$mail->SetFrom($mail_remetente, $nom_titulo);
		}


        //Checks for more than 1 email address at recipient
        $jaExiste = array();
        if (preg_match("/;/", $destinatario)) {
            $email_destino = explode(";", $destinatario);
            if (is_array($email_destino)) {
                for ($i = 0; $i < count($email_destino); $i++) {
                    // Se o endereço de e-mail NÃO estiver no array, envia e-mail e coloca no array
                    // Se já tiver no array, não envia novamente, evitando mails duplicados
                    if (!in_array($email_destino[$i], $jaExiste)) {
                        $mail->AddAddress($email_destino[$i]);
                        $jaExiste[] = $email_destino[$i];
                    }
                }

            } else {

                $mail->AddAddress($email_destino);
            }
        } else {
            $mail->AddAddress($destinatario);
        }

        $mail->SetLanguage('br', DOCUMENT_ROOT . "email/language/");
        $mail->AddAddress('rogerio.albandes@marioquintana.com.br');
        $done = $mail->Send();
        

        if (!$done) {
            if ($_SESSION['EM_FAILURE_LOG'] == '1') {
                $mail->SMTPDebug = true;
                $mail->Send();
                $this->logit("[".date($this->getPrintDate())."]" . " Line: " .  __LINE__ . " - Error send email, request " . $REQUEST .', operation: ' . $operation , $this->logFileEmail);
                $this->logit("[".date($this->getPrintDate())."]" . " Error Info: " . $mail->ErrorInfo , $this->logFileEmail);
            }
        } else {
            if ($_SESSION['EM_SUCCESS_LOG'] == '1') {
                $this->logit("[".date($this->getPrintDate())."]" . " Line: " .  __LINE__ . " - Email Succesfully Sent, request " . $REQUEST .', operation: ' . $operation , $this->logFileEmail);
            }
        }


    }

    public function validasessao($mob = null) {
        if (!isset($_SESSION['SES_COD_USUARIO'])) {
        	if($mob){
        		echo 1;
			}else{
        		echo '<META HTTP-EQUIV="Refresh" Content="0; URL='.path . '/admin/login">';	
        	}
        }
    }

    public function _sanitize()
    {
        if (isset($headers['X-CSRF-TOKEN'])) {
            if ($headers['X-CSRF-TOKEN'] !== $_SESSION['X-CSRF-TOKEN']) {
                return (json_encode(['error' => 'Wrong CSRF token.']));

            }
        } else {
            return (json_encode(['error' => 'No CSRF token.']));


        }

    }

    public function found_rows(){
        $dbCommon = new common();
        $ret = $dbCommon->foundRows();
		return $ret;
    }

    public function BrasilianCurrencyToMysql($get_valor)
    {
        $source = array('.', ',');
        $replace = array('', '.');
        $valor = str_replace($source, $replace, $get_valor);
        return $valor;
    }

    public function getModuleParam($module,$param) {
        $dbCommon = new common() ;
        return $dbCommon->getValueParam($module,$param) ;
    }

    // Encrypt Function
    public function mc_encrypt($encrypt, $key){
        $encrypt = serialize($encrypt);
        $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC), MCRYPT_DEV_URANDOM);
        $key = pack('H*', $key);
        $mac = hash_hmac('sha256', $encrypt, substr(bin2hex($key), -32));
        $passcrypt = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $encrypt.$mac, MCRYPT_MODE_CBC, $iv);
        $encoded = base64_encode($passcrypt).'|'.base64_encode($iv);
        return $encoded;
    }

    // Decrypt Function
    public function mc_decrypt($decrypt, $key){
        $decrypt = explode('|', $decrypt.'|');
        $decoded = base64_decode($decrypt[0]);
        $iv = base64_decode($decrypt[1]);
        if(strlen($iv)!==mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC)){ return false; }
        $key = pack('H*', $key);
        $decrypted = trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $decoded, MCRYPT_MODE_CBC, $iv));
        $mac = substr($decrypted, -64);
        $decrypted = substr($decrypted, 0, -64);
        $calcmac = hash_hmac('sha256', $decrypted, substr(bin2hex($key), -32));
        if($calcmac!==$mac){ return false; }
        $decrypted = unserialize($decrypted);
        return $decrypted;
    }

    public function makeMenu($idPerson, $idmodule='')
    {
        $smarty = $this->retornaSmarty();
        $langVars = $smarty->get_config_vars();

        $dbCommon = new common();
        $typeperson = $dbCommon->getIdTypePerson($idPerson) ;

        $programcount = $dbCommon->countPrograms($idmodule);
        //$groupperm = $dbCommon->selectGroupPermissionMenu($idPerson, $typeperson,$idmodule) ;
        $andModule = 'm.idmodule = ' . $idmodule ;
        $groupperm = $dbCommon->getPermissionMenu($idPerson, $andModule) ;

        if($groupperm){
            while (!$groupperm->EOF) {
                $allow = $groupperm->fields['allow'];
                $program = $groupperm->fields['program'];
                $idmodule_pai = $groupperm->fields['idmodule_pai'];
                $module = $groupperm->fields['module'];
                $idmodule_origem = $groupperm->fields['idmodule_origem'];
                $category = $groupperm->fields['category'];
                $category_pai = $groupperm->fields['category_pai'];
                $idcategory_origem = $groupperm->fields['idcategory_origem'];
                $controller = $groupperm->fields['controller'];
                $idprogram = $groupperm->fields['idprogram'];
                $prsmarty = $groupperm->fields['pr_smarty'];
                $ctsmarty = $groupperm->fields['cat_smarty'];
                $perm[$idprogram] = array('program' => $program, 'smartypr' => $prsmarty, 'smartyct' => $ctsmarty, 'idmodule_pai' => $idmodule_pai, 'module' => $module, 'idmodule_origem' => $idmodule_origem, 'category' => $category, 'category_pai' => $category_pai, 'idcategory_origem' => $idcategory_origem, 'controller' => $controller, 'idprogram' => $idprogram, 'allow' => $allow);
                $groupperm->MoveNext();
            }
        }

        /*
        $personperm = $dbCommon->selectPersonPermissionMenu($idPerson,$idmodule);

        if ($personperm->fields['idmodule_pai']) {
            while (!$personperm->EOF) {
                $allow = $personperm->fields['allow'];
                $program = $personperm->fields['program'];
                $idmodule_pai = $personperm->fields['idmodule_pai'];
                $module = $personperm->fields['module'];
                $idmodule_origem = $personperm->fields['idmodule_origem'];
                $category = $personperm->fields['category'];
                $category_pai = $personperm->fields['category_pai'];
                $idcategory_origem = $personperm->fields['idcategory_origem'];
                $controller = $personperm->fields['controller'];
                $idprogram = $personperm->fields['idprogram'];
                $prsmarty = $personperm->fields['pr_smarty'];
                $ctsmarty = $personperm->fields['cat_smarty'];
                $perm[$idprogram] = array('program' => $program, 'smartypr' => $prsmarty, 'smartyct' => $ctsmarty, 'idmodule_pai' => $idmodule_pai, 'module' => $module, 'idmodule_origem' => $idmodule_origem, 'category' => $category, 'category_pai' => $category_pai, 'idcategory_origem' => $idcategory_origem, 'controller' => $controller, 'idprogram' => $idprogram, 'allow' => $allow);
                $personperm->MoveNext();
            }
        }

        */

        for ($j = 1; $j <= $programcount; $j++) {
            if (in_array($perm[$j]['idmodule_pai'], $modules) || $perm[$j]['allow'] != 'Y') {

            } else {
                $modules[$perm[$j]['idmodule_pai']] = array('idmodule' => $perm[$j]['idmodule_pai'], 'module' => $perm[$j]['module']);
            }

            //agrupa as categorias tirando as duplicadas
            if (in_array($perm[$j]['category_pai'], $categories) || $perm[$j]['allow'] != 'Y') {

            } else {
                $categories[$perm[$j]['category_pai']] = array('idmodule_origem' => $perm[$j]['idmodule_origem'], 'category' => $perm[$j]['category'], 'idcategory' => $perm[$j]['category_pai'], 'smarty' => $perm[$j]['smartyct']);
            }

            //agrupa os programas separando os duplicados
            if (in_array($perm[$j]['idprogram'], $programs) || $perm[$j]['allow'] != 'Y') {

            } else {
                $programs[$perm[$j]['idprogram']] = array('idprogram' => $perm[$j]['idprogram'],'idcategory_origem' => $perm[$j]['idcategory_origem'], 'program' => $perm[$j]['program'], 'controller' => $perm[$j]['controller'], 'smarty' => $perm[$j]['smartypr']);
            }
        }

        $countmodules    = $dbCommon->countModules();
        $countcategories = $dbCommon->countCategories();

        $lista = "<ul id='menu' class='filetree'>";
        for ($i = 0; $i < $countmodules; $i++) {
            if($modules[$i + 1]['module']){
                $lista.="<li><span>" . $modules[$i + 1]['module'] . "</span>";
                $lista.="<ul>";
                for ($j = 0; $j <= $countcategories; $j++) {
                    if ($modules[$i + 1]['idmodule'] == ($categories[$j + 1]['idmodule_origem'])) {
                        $cat = $categories[$j + 1]['smarty'];
                        $lista.="<li><span>" . $langVars[$cat] . "</span>";
                        $lista.="<ul>";
                        for ($k = 0; $k <= $programcount; $k++) {
                            if ($categories[$j + 1]['idcategory'] == ($programs[$k + 1]['idcategory_origem'])) {
                                $pr = $programs[$k + 1]['smarty'];
                                $checkbar = substr($programs[$k + 1]['controller'], -1);
                                if($checkbar != "/") $checkbar = "/";
                                else $checkbar = "";
                                $lista.="<li><span><a href='#/" . $programs[$k + 1]['controller'] . "' class='loadMenu' rel='" . $programs[$k + 1]['controller'] . $checkbar."' >" . $langVars[$pr] . "</a></span></li>";
                            }
                        }
                        $lista.="</ul></li>";
                    }
                }
                $lista.="</ul></li>";
            }
        }
        $lista.="</ul>";

        return $lista;

    }
    // TMS Methods
    function SetPdfHeaderTestData($a_headerTestData)
    {
        $this->a_pdfHeaderTestData=$a_headerTestData;
    }
    public function ReportTestPdfHeader($pdf)
    {
        if(file_exists($this->pdfLogo)) {
            $pdf->Image($this->pdfLogo, 10 + $this->pdfLeftMargin, 8);
        }
        $pdf->Ln(2);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell($this->pdfLeftMargin);
        $pdf->Cell(0, 5, $this->pdfTitle, 0, 0, 'C');
        $pdf->SetFont('Arial', 'I', 6);
        $pdf->Cell(0, 5, $this->pdfPage . ' ' . $pdf->PageNo() . '/{nb}', 0, 0, 'R');
        $pdf->Ln(10);
        $pdf->SetFont('Arial','B',8);
        $i = 1;
        for ($row = 0; $row < count($this->a_pdfHeaderTestData); $row++) {
            $pdf->Cell($this->pdfLeftMargin);
            if(($i%2) != 0){
                $pdf->Cell(20,4,'',0,0,'L',0);
            }
            $pdf->Cell($this->a_pdfHeaderTestData[$row]['width'], 4, utf8_decode($this->a_pdfHeaderTestData[$row]['title'].$this->a_pdfHeaderTestData[$row]['descricao']), 0, 0, $this->a_pdfHeaderTestData[$row]['align'], 0);
            if(($i%2) == 0){
                $pdf->Ln();
            }
            $i++;
        }
        $pdf->Ln(7);
        $pdf->Cell($this->pdfLeftMargin);
        $pdf->Line($pdf->GetX(), $pdf->GetY(), 198, $pdf->GetY());
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell($this->pdfLeftMargin);
        $pdf->Ln(8);
        return $pdf ;
    }
    public function ReportTestPdfRow($pdf,$data)
    {
        //Calcula a altura da fila
        $nb=0;
        for($i=0;$i<count($data);$i++)
            $nb=max($nb,$this->ReportPdfNbLines($pdf,$this->a_pdfHeaderData[$i]['width'],$data[$i]));
        $h=5*$nb;
        //Insere um salto de página primeiramente se for necessario
        $this->ReportTestPdfCheckPageBreak($pdf,$h);
        //Desenha as células da linha
        for($i=0;$i<count($data);$i++){
            $w=$this->a_pdfHeaderData[$i]['width'];
            $a=isset($this->a_pdfHeaderData[$i]['align']) ? $this->a_pdfHeaderData[$i]['align'] : 'C';
            //Salva a posição atual
            $x=$pdf->GetX();
            $y=$pdf->GetY();
            //Draw the border
            $pdf->Rect($x,$y,$w,$h,'F');
            //Imprime o texto
            $pdf->MultiCell($w,5,$data[$i],0,$a);
            //Coloca a posição para a direita da célula
            $pdf->SetXY($x+$w,$y);
        }
        //Va para a próxima linha
        $pdf->Ln($h);
        return $pdf;
    }
    public function retornaReportFpdfProva()
    {
        require_once DOCUMENT_ROOT . path . '/includes/classes/fpdf/fpdf_prova.php';
        $pdf = new PDF_prova();
        return $pdf;
    }

}
?>
