<?php

require_once(HELPDEZK_PATH . '/app/modules/fin/controllers/finCommonController.php');

class finBankRemittanceRep extends finCommon
{
    /**
     * Create an instance, check session time
     *
     * @access public
     */
    public function __construct()
    {

        parent::__construct();
        session_start();
        $this->sessionValidate();

        $this->idPerson = $_SESSION['SES_COD_USUARIO'];

        $this->modulename = 'Financeiro' ;
        $this->idmodule =  $this->getIdModule($this->modulename);

        // Log settings
        $this->log = parent::$_logStatus;
        $this->program = basename(__FILE__);
        $this->idprogram =  $this->getIdProgramByController('finBankRemittanceRep');

        $this->loadModel('company_model');
        $dbCompany = new company_model();
        $this->dbCompany = $dbCompany;

        $this->loadModel('bank_model');
        $dbBank = new bank_model();
        $this->dbBank = $dbBank;

    }

    public function index()
    {
        $smarty = $this->retornaSmarty();
        $permissions = array_values($this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']));

        $this->makeNavVariables($smarty,$this->idmodule);
        $this->makeFooterVariables($smarty);
        $this->_makeNavFin($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');
        $smarty->assign('curyear', date("Y"));

        // --- Company ---
        $arrCompany = $this->_comboCompanies();
        $smarty->assign('companyids',  $arrCompany['ids']);
        $smarty->assign('companyvals', $arrCompany['values']);
        $smarty->assign('idcompany', $arrCompany['ids'][0] );

        // --- Bank ---
        if($arrCompany['ids'][0]){
            $arrBank = $this->_comboBank($arrCompany['ids'][0]);
            $smarty->assign('bankids',  $arrBank['ids']);
            $smarty->assign('bankvals', $arrBank['values']);
            $smarty->assign('idbank', $arrBank['ids'][0] );
        }

        $smarty->assign('token', $this->_makeToken()) ;

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);

        $this->_displayButtons($smarty,$permissions);
        if($permissions[0] == "Y"){
            $smarty->display('fin-remittance-report.tpl');
        }else{
            $smarty->assign('href', $this->helpdezkUrl.'/fin/home');
            $smarty->display($this->helpdezkPath.'/app/modules/main/views/access_denied.tpl');
        }

    }

    function ajaxBank()
    {
        echo $this->comboBanksHtml($_POST['companyId']);
    }

    public function comboBanksHtml($idCompany)
    {

        $arrType = $this->_comboBank($idCompany);
        $select = '';

        foreach ( $arrType['ids'] as $indexKey => $indexValue ) {
            if ($arrType['default'][$indexKey] == 1) {
                $default = 'selected="selected"';
            } else {
                $default = '';
            }
            $select .= "<option value='$indexValue' $default>".$arrType['values'][$indexKey]."</option>";
        }
        if($idCompany == 0){$select = "<option value='' selected='selected'></option>";}

        return $select;
    }

    function checkFile()
    {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $idcompany = $_POST['idcompany'];
        $idbank = $_POST['idbank'];
        $flgProtest = $_POST[''];

        $rsbank = $this->dbBank->getCompanyBanks("AND a.idbank = $idbank AND a.idperson = $idcompany");
        $rscompany = $this->dbCompany->getErpCompanies("WHERE idperson = $idcompany");

        //Create directory if not exists
        $dirModule = $this->_setFolder($this->helpdezkPath . "/app/uploads/fin/");
        $dirYear = $this->_setFolder($dirModule . date("Y") . "/");
        $uploadDir = $this->_setFolder( $dirYear. "remittance/");

        $destination = $uploadDir.$_FILES['file']['name'];
        if(!@copy($_FILES['file']['tmp_name'], $destination)){
            $this->logIt("Make remittance report " . ' - File ' . $_FILES['file']['tmp_name'] . ' can\'t copy  into ' .  $destination,3,'general',__LINE__);
            return false;
        }

        $fd = fopen($destination, "r");

        while(!feof($fd)) {
            $row = fgets($fd, 4096);

            $typeRow = $this->identifyRow($row,$idcompany,$idbank);

            switch ($typeRow){
                case 1:
                case 2:
                    $thead = $this->getInfo($row,$idcompany,$idbank,$typeRow);

                    if($thead['company_bankcode'] != $rsbank->fields['insurance_number']){
                        $msg = "Arquivo não corresponde à empresa selecionada.";
                    }

                    if($thead['bank_code'] != $rsbank->fields['ticketgroup']){
                        $msg = "Arquivo não corresponde ao banco selecionado.";
                    }

                    break;
                case 3:
                    $tbody[] = $this->getInfo($row,$idcompany,$idbank,$typeRow);
                    break;
            }
        }

        if(sizeof($tbody) == 0 ){$msg = "Arquivos sem dados.";}

        $arrParams = array( "idcompany" => $idcompany,
                            "idbank"    => $idbank,
                            "file_name" => $_FILES['file']['name'],
                            "company_name" => $rscompany->fields['name'],
                            "bank_name" => $rsbank->fields['name'],
                            "header"    => $thead,
                            "body"      => $tbody);

        if($msg){
            echo "msg|".$msg;
        }else{
            $ret = $this->makeReport($arrParams);
            $txt  = "filename=".$ret['fileUrl']."|numrows=".$ret['rows']."|numprotest=".$ret['rowsprotest'];
            $txt .= "|numnoprotest=".$ret['rowsnoprotest']."|total=".$ret['total'];
            $txt .= "|filerem=".$_FILES['file']['name']."|sequence=".$thead['remittance_sequence'];
            $txt .= "|numsendbank=".$ret['rowssendbank']."|numnosendbank=".$ret['rowsnosendbank'];
            $txt .= "|numprintbank=".$ret['rowsprintbank']."|numnoprintbank=".$ret['rowsnoprintbank'];
            $txt .= "|numdiscount=".$ret['rowsdiscount']."|numnodiscount=".$ret['rowsnodiscount'];
            $txt .= "|lbltypediscount=".$ret['lbltypediscount']."|lbldiscountval=".$ret['lbldiscountval'];

            echo $txt;
        }


    }

    function makeReport($params){
        $smarty = $this->retornaSmarty();
        $langVars = $smarty->getConfigVars();
        // class FPDF with extension to parsehtml
        // Cria o objeto da biblioteca FPDF
        $pdf = $this->_returnfpdfhdk();

        //Parâmetros para a Fonte a ser utilizado no relatório
        $FontFamily = 'Arial';
        $FontStyle  = '';
        $FontSize   = 8;
        $CelHeight = 4;

        $title =  html_entity_decode(utf8_decode('Relatório de Arquivo de Remessa'),ENT_QUOTES, "ISO8859-1");//Titulo //Titulo
        $PdfPage = (utf8_decode($langVars['PDF_Page'])) ; //numeração página
        $leftMargin = 10;

        $logo = array("file" => $this->helpdezkPath . '/app/uploads/logos/' .  $this->getReportsLogoImage(),
            "posx" => $leftMargin + 10,
            "posy" => 8
        );
        $h2 = array(
            array("txt"=>html_entity_decode(utf8_decode('Nome Arquivo: '),ENT_QUOTES, "ISO8859-1"),
                "width"=>20,
                "height" => $CelHeight,
                "border" => 0,
                "ln"=>0,
                "fill"=>0,
                "align" => 'R'),
            array("txt"=>html_entity_decode(utf8_decode($params['file_name']),ENT_QUOTES, "ISO8859-1"),
                "width"=>40,
                "height" => $CelHeight,
                "border" => 0,
                "ln"=>0,
                "fill"=>0,
                "align" => 'L'),
            array("txt"=>'',
                "width"=>60,
                "height" => $CelHeight,
                "border" => 0,
                "ln"=>0,
                "fill"=>0,
                "align" => 'L'),
            array("txt"=>html_entity_decode(utf8_decode('Número Sequencial: '),ENT_QUOTES, "ISO8859-1"),
                "width"=>20,
                "height" => $CelHeight,
                "border" => 0,
                "ln"=>0,
                "fill"=>0,
                "align" => 'R'),
            array("txt"=>html_entity_decode(utf8_decode($params['header']['remittance_sequence']),ENT_QUOTES, "ISO8859-1"),
                "width"=>10,
                "height" => $CelHeight,
                "border" => 0,
                "ln"=>1,
                "fill"=>0,
                "align" => 'L'),
            array("txt"=>html_entity_decode(utf8_decode('Empresa: '),ENT_QUOTES, "ISO8859-1"),
                "width"=>20,
                "height" => $CelHeight,
                "border" => 0,
                "ln"=>0,
                "fill"=>0,
                "align" => 'R'),
            array("txt"=>html_entity_decode(utf8_decode($params['company_name']),ENT_QUOTES, "ISO8859-1"),
                "width"=>60,
                "height" => $CelHeight,
                "border" => 0,
                "ln"=>1,
                "fill"=>0,
                "align" => 'L'),
            array("txt"=>html_entity_decode(utf8_decode('CNPJ: '),ENT_QUOTES, "ISO8859-1"),
                "width"=>20,
                "height" => $CelHeight,
                "border" => 0,
                "ln"=>0,
                "fill"=>0,
                "align" => 'R'),
            array("txt"=>html_entity_decode(utf8_decode($this->formatMask($params['header']['company_cnpj'],$this->getConfig('ein_mask'))),ENT_QUOTES, "ISO8859-1"),
                "width"=>10,
                "height" => $CelHeight,
                "border" => 0,
                "ln"=>0,
                "fill"=>0,
                "align" => 'L'),
            array("txt"=>'',
                "width"=>90,
                "height" => $CelHeight,
                "border" => 0,
                "ln"=>0,
                "fill"=>0,
                "align" => 'L'),
            array("txt"=>html_entity_decode(utf8_decode('Código Cedente: '),ENT_QUOTES, "ISO8859-1"),
                "width"=>20,
                "height" => $CelHeight,
                "border" => 0,
                "ln"=>0,
                "fill"=>0,
                "align" => 'R'),
            array("txt"=>html_entity_decode(utf8_decode($params['header']['company_bankcode']),ENT_QUOTES, "ISO8859-1"),
                "width"=>10,
                "height" => $CelHeight,
                "border" => 0,
                "ln"=>1,
                "fill"=>0,
                "align" => 'L'),
            array("txt"=>html_entity_decode(utf8_decode('Banco: '),ENT_QUOTES, "ISO8859-1"),
                "width"=>20,
                "height" => $CelHeight,
                "border" => 0,
                "ln"=>0,
                "fill"=>0,
                "align" => 'R'),
            array("txt"=>html_entity_decode(utf8_decode($params['bank_name']),ENT_QUOTES, "ISO8859-1"),
                "width"=>10,
                "height" => $CelHeight,
                "border" => 0,
                "ln"=>0,
                "fill"=>0,
                "align" => 'L'),
            array("txt"=>'',
                "width"=>90,
                "height" => $CelHeight,
                "border" => 0,
                "ln"=>0,
                "fill"=>0,
                "align" => 'L'),
            array("txt"=>html_entity_decode(utf8_decode('Código: '),ENT_QUOTES, "ISO8859-1"),
                "width"=>20,
                "height" => $CelHeight,
                "border" => 0,
                "ln"=>0,
                "fill"=>0,
                "align" => 'R'),
            array("txt"=>html_entity_decode(utf8_decode($params['header']['bank_code']),ENT_QUOTES, "ISO8859-1"),
                "width"=>10,
                "height" => $CelHeight,
                "border" => 0,
                "ln"=>0,
                "fill"=>0,
                "align" => 'L')
        );
        $th = array(
            array("txt"=>html_entity_decode(utf8_decode('Nosso Número'),ENT_QUOTES, "ISO8859-1"),
                "width"=>17,
                "height" => $CelHeight,
                "border" => 1,
                "ln"=>0,
                "fill"=>1,
                "align" => 'C'),
            array("txt"=>html_entity_decode(utf8_decode('Sacado'),ENT_QUOTES, "ISO8859-1"),
                "width"=>70,
                "height" => $CelHeight,
                "border" => 1,
                "ln"=>0,
                "fill"=>1,
                "align" => 'C'),
            array("txt"=>html_entity_decode(utf8_decode('CPF'),ENT_QUOTES, "ISO8859-1"),
                "width"=>20,
                "height" => $CelHeight,
                "border" => 1,
                "ln"=>0,
                "fill"=>1,
                "align" => 'C'),
            array("txt"=>html_entity_decode(utf8_decode('Vencimento'),ENT_QUOTES, "ISO8859-1"),
                "width"=>20,
                "height" => $CelHeight,
                "border" => 1,
                "ln"=>0,
                "fill"=>1,
                "align" => 'C'),
            array("txt"=>html_entity_decode(utf8_decode('Valor'),ENT_QUOTES, "ISO8859-1"),
                "width"=>20,
                "height" => $CelHeight,
                "border" => 1,
                "ln"=>0,
                "fill"=>1,
                "align" => 'C'),
            array("txt"=>html_entity_decode(utf8_decode('Desconto'),ENT_QUOTES, "ISO8859-1"),
                "width"=>20,
                "height" => $CelHeight,
                "border" => 1,
                "ln"=>0,
                "fill"=>1,
                "align" => 'C'),
            array("txt"=>html_entity_decode(utf8_decode('Tipo Desconto'),ENT_QUOTES, "ISO8859-1"),
                "width"=>20,
                "height" => $CelHeight,
                "border" => 1,
                "ln"=>0,
                "fill"=>1,
                "align" => 'C'),
            array("txt"=>html_entity_decode(utf8_decode('V. Desconto'),ENT_QUOTES, "ISO8859-1"),
                "width"=>15,
                "height" => $CelHeight,
                "border" => 1,
                "ln"=>0,
                "fill"=>1,
                "align" => 'C'),
            array("txt"=>html_entity_decode(utf8_decode('Protesto'),ENT_QUOTES, "ISO8859-1"),
                "width"=>15,
                "height" => $CelHeight,
                "border" => 1,
                "ln"=>0,
                "fill"=>1,
                "align" => 'C'),
            array("txt"=>html_entity_decode(utf8_decode('Dias'),ENT_QUOTES, "ISO8859-1"),
                "width"=>15,
                "height" => $CelHeight,
                "border" => 1,
                "ln"=>0,
                "fill"=>1,
                "align" => 'C'),
            array("txt"=>html_entity_decode(utf8_decode('Envia Correio'),ENT_QUOTES, "ISO8859-1"),
                "width"=>15,
                "height" => $CelHeight,
                "border" => 1,
                "ln"=>0,
                "fill"=>1,
                "align" => 'C'),
            array("txt"=>html_entity_decode(utf8_decode('Impressão'),ENT_QUOTES, "ISO8859-1"),
                "width"=>15,
                "height" => $CelHeight,
                "border" => 1,
                "ln"=>1,
                "fill"=>1,
                "align" => 'C')
        );

        $headerparams = array(
            "leftMargin" => $leftMargin,
            "pdfpage" => $PdfPage,
            "FontFamily" => $FontFamily,
            "FontStyle"  => $FontStyle,
            "FontSize"  => $FontSize,
            "logo" => $logo,
            "title" => $title,
            "tableHeader" => $th,
            "h2" => $h2,
            "lineWidth" => "285"
        );

        $pdf->AliasNbPages();
        $pdf->AddPage('L','A4',$headerparams); //Cria a página no arquivo pdf

        $a = $params['body'];
        $totalFile = 0;
        $numPay = 0;
        $totalProtest = 0;
        $totalNoProtest = 0;
        $totalSendBank = 0;
        $totalNoSendBank = 0;
        $totalPrintBank = 0;
        $totalNoPrintBank = 0;
        $totalDiscount = 0;
        $totalNoDiscount = 0;
        $arrDiscountVals = array();
        $arrDiscountTypes = array();
        $lblTypeDiscount = "";
        $lblDiscountValue = "";

        foreach ($a as $k=>$v){
            $bankConf = $this->dbBank->getBankConfig("AND a.idbank = ".$params['idbank']." AND a.idperson = ".$params['idcompany']." AND a.protest_code = '".$v['protest_code']."'");
            if($bankConf->fields['flag_protest'] == 'S'){$totalProtest++;}
            else{$totalNoProtest++;}

            $v['send_bank_ticket'] == 'S' ? $totalSendBank++ : $totalNoSendBank++;
            $v['bank_print'] == 'A' ? $totalPrintBank++ : $totalNoPrintBank++;
            $v['discount_value'] == '0000000000000' ? $totalNoDiscount++ : $totalDiscount++;
            $flagDiscount = $v['discount_value'] == '0000000000000' ? 'N' : 'S';
            $typeDiscount = $v['discount_type'] == 'A' ? 'Valor' : 'Percentual';
            $discountLen = strlen($v['discount_value']) - 2;
            $valueDiscount = substr($v['discount_value'],0,$discountLen).".".substr($v['discount_value'],-2);
            $txtValueDiscount = $v['discount_type'] == 'A' ? 'R$'.number_format($valueDiscount,2,',','.') : number_format($valueDiscount,2,',','.').'%';

            if($flagDiscount == 'S'){
                if(!in_array($v['discount_type'],$arrDiscountTypes)){
                    $lblTypeDiscount =  $typeDiscount;
                    array_push($arrDiscountTypes,$v['discount_type']);
                }

                if(!in_array($v['discount_value'],$arrDiscountVals)){
                    $lblDiscountValue =  $txtValueDiscount;
                    array_push($arrDiscountVals,$v['discount_value']);
                }
            }

            $cpf = $this->formatMask(substr($v['cpfcnpj'],strlen($v['cpfcnpj'])-11),$this->getConfig('id_mask'));
            $nossonro = $this->formatMask($v['nossonro'],$bankConf->fields['nossonro_mask']);
            $duedate = strlen($v['duedate']) == 8 ? $this->formatMask($v['duedate'],'99/99/9999') : $this->formatMask($v['duedate'],'99/99/99');
            $payvalue = substr(ltrim($v['payvalue'],'0'),0,-2).'.'.substr(ltrim($v['payvalue'],'0'),-2,2);

            $pdf->Cell($leftMargin);
            $pdf->Cell(17,5,$nossonro,1,0,'L');
            $pdf->Cell(70,5,$v['payer'],1,0,'L');
            $pdf->Cell(20,5,$cpf,1,0,'C');
            $pdf->Cell(20,5,$duedate,1,0,'C');
            $pdf->Cell(20,5,number_format($payvalue,2,',','.'),1,0,'R');
            $pdf->Cell(20,5,$flagDiscount,1,0,'C');
            $pdf->Cell(20,5,$typeDiscount,1,0,'C');
            $pdf->Cell(15,5,$txtValueDiscount,1,0,'C');
            $pdf->Cell(15,5,$bankConf->fields['flag_protest'],1,0,'C');
            $pdf->Cell(15,5,$v['days'],1,0,'C');
            $pdf->Cell(15,5,$v['send_bank_ticket'],1,0,'C');
            $pdf->Cell(15,5,$v['bank_print'],1,1,'C');

            $totalFile += $payvalue;
            $numPay++;
        }

        $pdf->Ln(8);
        $pdf->Cell($leftMargin + 30);
        $pdf->Cell(15,5,'Total Boletos: ',0,0,'R');
        $pdf->Cell(10,5,$numPay,0,0,'L');
        $pdf->Cell(97,5,'',0,0,'L');
        $pdf->Cell(15,5,'Total: ',0,0,'R');
        $pdf->Cell(30,5,'R$ '.number_format($totalFile,2,',','.'),0,0,'L');

        $pdf->Ln();
        $pdf->Cell($leftMargin + 30);
        $pdf->Cell(15,5,'Total Boletos Com Protesto: ',0,0,'R');
        $pdf->Cell(10,5,$totalProtest,0,0,'L');
        $pdf->Cell(97,5,'',0,0,'L');
        $pdf->Cell(15,5,'Total Boletos Sem Protesto: ',0,0,'R');
        $pdf->Cell(30,5,$totalNoProtest,0,0,'L');

        $pdf->Ln();
        $pdf->Cell($leftMargin + 30);
        $pdf->Cell(15,5,'Total Boletos Com Desconto: ',0,0,'R');
        $pdf->Cell(10,5,$totalDiscount,0,0,'L');
        $pdf->Cell(97,5,'',0,0,'L');
        $pdf->Cell(15,5,'Total Boletos Sem Desconto: ',0,0,'R');
        $pdf->Cell(30,5,$totalNoDiscount,0,0,'L');

        $pdf->Ln();
        $pdf->Cell($leftMargin + 30);
        $pdf->Cell(15,5,'Total Boletos Envio pelo Banco: ',0,0,'R');
        $pdf->Cell(10,5,$totalSendBank,0,0,'L');
        $pdf->Cell(97,5,'',0,0,'L');
        $pdf->Cell(15,5,'Total Boletos Envio pela Escola: ',0,0,'R');
        $pdf->Cell(30,5,$totalNoSendBank,0,0,'L');

        $pdf->Ln();
        $pdf->Cell($leftMargin + 30);
        $pdf->Cell(15,5,html_entity_decode(utf8_decode('Total Boletos Impressão pelo Banco: ')),0,0,'R');
        $pdf->Cell(10,5,$totalPrintBank,0,0,'L');
        $pdf->Cell(97,5,'',0,0,'L');
        $pdf->Cell(15,5,html_entity_decode(utf8_decode('Total Boletos Impressão pela Escola: ')),0,0,'R');
        $pdf->Cell(30,5,$totalNoPrintBank,0,0,'L');

        $pdf->Ln(8);
        $pdf->Cell($leftMargin + 30);
        $legendaLbl = array(array('title'=>html_entity_decode(utf8_decode("Legendas")),'cellWidth'=>55,'cellHeight'=>4,'titleAlign'=>'C'));
        $this->makePdfLineBlur($pdf, $legendaLbl);
        $pdf->Cell($leftMargin + 10);
        $pdf->Cell(15,5,'Envia Correio: ',0,0,'R');
        $pdf->Cell(15,5,'S - Banco posta diretamente ao pagador',0,1,'L');
        $pdf->Cell($leftMargin + 25);
        $pdf->Cell(15,5,html_entity_decode(utf8_decode('N - Beneficiário posta diretamente ao pagador')),0,1,'L');
        $pdf->Cell($leftMargin + 10);
        $pdf->Cell(15,5,html_entity_decode(utf8_decode('Impressão: ')),0,0,'R');
        $pdf->Cell(15,5,html_entity_decode(utf8_decode('A - Banco imprime Boleto')),0,1,'L');
        $pdf->Cell($leftMargin + 25);
        $pdf->Cell(15,5,html_entity_decode(utf8_decode('B - Beneficiário imprime Boleto')),0,1,'L');

        //Create directory if not exists
        $dirModule = $this->_setFolder($this->helpdezkPath . "/app/downloads/tmp/");

        //Parâmetros para salvar o arquivo
        $filename = "check_remessa_report_".time().".pdf"; //nome do arquivo
        $fileNameWrite = $this->helpdezkPath . '/app/downloads/tmp/'. $filename ; //caminho onde é salvo o arquivo
        $fileNameUrl   = $this->helpdezkUrl . '/app/downloads/tmp/'. $filename ; //link para visualização em nova aba/janela


        $pdf->Output($fileNameWrite,'F'); //a biblioteca cria o arquivo
        
        return array(
            "fileUrl" => $fileNameUrl,
            "rows" => $numPay,
            "rowsprotest" => $totalProtest,
            "rowsnoprotest" => $totalNoProtest,
            "total" => number_format($totalFile,2,',','.'),
            "rowssendbank" => $totalSendBank,
            "rowsnosendbank" => $totalNoSendBank,
            "rowsprintbank" => $totalPrintBank,
            "rowsnoprintbank" => $totalNoPrintBank,
            "rowsdiscount" => $totalDiscount,
            "rowsnodiscount" => $totalNoDiscount,
            "lbltypediscount" => $lblTypeDiscount,
            "lbldiscountval" => $lblDiscountValue
        );


    }

    function identifyRow($row,$idcompany,$idbank){
        $where = "WHERE a.flag_checksend = 'Y' AND a.line_identifier = 'Y' AND d.idbank = $idbank AND d.idperson = $idcompany";
        $ret = $this->dbBank->getCheckSendItem($where);

        while(!$ret->EOF){
            $rowVal = substr($row, $ret->fields['start_position'],$ret->fields['fieldlength']);
            $checkVal = $ret->fields['idtype'] != '' ? $this->getBankData($ret->fields['idtype']) : $ret->fields['text'];
            if($rowVal == $checkVal){
                $type = $ret->fields['idtypesection'];
                break;
            }
            $ret->MoveNext();
        }
        
        return $type;
    }

    function getInfo($row,$idcompany,$idbank,$typeRow){
        $where = "WHERE a.flag_checksend = 'Y' AND a.line_identifier != 'Y' AND d.idbank = $idbank AND d.idperson = $idcompany AND c.status = 'A' AND a.idtypesection = $typeRow";
        $order = "ORDER BY e.idlayoutcheck";

        $ret = $this->dbBank->getCheckSendItem($where,$order);

        while(!$ret->EOF){
            $rowVal = substr($row, $ret->fields['start_position'],$ret->fields['fieldlength']);
            $arrRet[$ret->fields['title']] = $rowVal;
            $ret->MoveNext();
        }

        return $arrRet;


    }

}