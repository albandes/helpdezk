<?php

require_once(HELPDEZK_PATH . '/app/modules/fin/controllers/finCommonController.php');

class finCashReport extends finCommon
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
        $this->idprogram =  $this->getIdProgramByController('finCashReport');

        $this->loadModel('company_model');
        $dbCompany = new company_model();
        $this->dbCompany = $dbCompany;

        $this->loadModel('bank_model');
        $dbBank = new bank_model();
        $this->dbBank = $dbBank;

        $this->loadModel('financial_model');
        $dbFinancial = new financial_model();
        $this->dbFinancial = $dbFinancial;

        $this->loadModel('admin/logos_model');
        $dbLogo = new logos_model();
        $this->dbLogo = $dbLogo;

    }

    public function index()
    {

        $smarty = $this->retornaSmarty();
        $permissions = array_values($this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']));

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavFin($smarty);

        // --- Company ---
        $arrCompany = $this->_comboCompanies();
        $smarty->assign('companyids',  $arrCompany['ids']);
        $smarty->assign('companyvals', $arrCompany['values']);
        $smarty->assign('idcompany', $arrCompany['ids'][0] );

        $reportslogo = $this->dbLogo->getReportsLogo();
        $smarty->assign('reportslogo', $this->helpdezkUrl . '/app/uploads/logos/' .  $this->getReportsLogoImage());
        $smarty->assign('reportsheight', $reportslogo->fields['height']);
        $smarty->assign('reportswidth', $reportslogo->fields['width']);

        $smarty->assign('token', $this->_makeToken()) ;
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);

        $this->_displayButtons($smarty,$permissions);
        if($permissions[0] == "Y"){
            $smarty->display('fin-cash-report.tpl');
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

        $arrType = $this->_comboBankLegacyID($idCompany);
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

    public function getReport()
    {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $idCompanyLegacy = $this->dbCompany->getCompanyLegacyID($_POST['cmbCompany']);
        if (!$idCompanyLegacy) {
            if($this->log)
                $this->logIt("Can't get Company's Legacy ID  - User: ".$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $dtstart = str_replace("'", "", $this->formatSaveDate($_POST['dtstart']));

        $link = "/movimentocaixa/{$idCompanyLegacy->fields['idperseus']}/{$dtstart}";
        $ret = $this->_returnApiData($link);
        //echo "<pre>", print_r($ret,true), "</pre>";
        if(!$ret) {
            $ret = array();
        }

        $aRet = array(
            "data" => $ret
        );

        echo json_encode($aRet);


    }

    public function exportReport()
    {

        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }
        
        $TotalC = 0;
        $TotalD = 0;

        $dtProcess = $_POST['dtstart'] == '' ? date("d/m/Y") : $_POST['dtstart'];
        $companyID = $_POST['companyID'];
        
        $companyData = $this->dbCompany->getErpCompanyData("AND a.idperson = {$companyID}");
        if(is_array($companyData)){
            if($this->log)
                $this->logIt($companyData['message'] . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
            return false;
        }
        $companyAddress = "{$companyData->fields['street']}, {$companyData->fields['number']} - {$companyData->fields['neighborhood']} - {$companyData->fields['city']} / {$companyData->fields['uf']}";


        // class FPDF with extension to parsehtml
        // Create a instance of library
        $pdf = $this->_returnfpdfhdk();

        //Font parameters to be used in the report.
        $FontFamily = 'Arial';
        $FontStyle  = '';
        $FontSize   = 10;
        $CelHeight = 4;

        $title =  html_entity_decode(utf8_decode($this->getLanguageWord('pgr_fin_cash_report')),ENT_QUOTES, "ISO8859-1"); //Title
        $PdfPage = (utf8_decode($this->getLanguageWord('PDF_Page'))) ; //Page numbering
        $leftMargin = 10;

        $logo = array("file" => $this->helpdezkPath . '/app/uploads/logos/' .  $this->getReportsLogoImage(),
            "posx" => $leftMargin + 10,
            "posy" => 8
        );

        $h2 = array(
            array("txt"=>html_entity_decode(utf8_decode($companyData->fields['company_name']),ENT_QUOTES, "ISO8859-1"),
                "width"=>177,
                "height" => $CelHeight,
                "border" => 0,
                "ln"=>1,
                "fill"=>0,
                "align" => 'C'),
            array("txt"=>html_entity_decode(utf8_decode($companyAddress),ENT_QUOTES, "ISO8859-1"),
                "width"=>177,
                "height" => $CelHeight,
                "border" => 0,
                "ln"=>1,
                "fill"=>0,
                "align" => 'C'),
            array("txt"=>html_entity_decode(utf8_decode(""),ENT_QUOTES, "ISO8859-1"),
                "width"=>177,
                "height" => $CelHeight,
                "border" => 0,
                "ln"=>1,
                "fill"=>0,
                "align" => 'C'),
            array("txt"=>html_entity_decode($dtProcess,ENT_QUOTES, "ISO8859-1"),
                "width"=>177,
                "height" => $CelHeight,
                "border" => 0,
                "ln"=>1,
                "fill"=>0,
                "align" => 'C')
        );

        //table header
        $th = array(
            array("txt"=>html_entity_decode(utf8_decode($this->getLanguageWord('FIN_identification')),ENT_QUOTES, "ISO8859-1"),
                "width"=>77,
                "height" => $CelHeight,
                "border" => 1,
                "ln"=>0,
                "fill"=>1,
                "align" => 'C'),
            array("txt"=>html_entity_decode(utf8_decode($this->getLanguageWord('Description')),ENT_QUOTES, "ISO8859-1"),
                "width"=>60,
                "height" => $CelHeight,
                "border" => 1,
                "ln"=>0,
                "fill"=>1,
                "align" => 'C'),
            array("txt"=>html_entity_decode(utf8_decode($this->getLanguageWord('ERP_In')),ENT_QUOTES, "ISO8859-1"),
                "width"=>20,
                "height" => $CelHeight,
                "border" => 1,
                "ln"=>0,
                "fill"=>1,
                "align" => 'C'),
            array("txt"=>html_entity_decode(utf8_decode($this->getLanguageWord('ERP_Out')),ENT_QUOTES, "ISO8859-1"),
                "width"=>20,
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
            "FontSyze"  => $FontSize,
            "logo" => $logo,
            "title" => $title,
            "tableHeader" => $th,
            "h2" => $h2
        );

        $pdf->AliasNbPages();
        $pdf->SetLineWidth(0.2);
        $pdf->AddPage('P','A4',$headerparams); //Add new page in file
        $pdf->SetLineWidth(0.5);
        
        //Table rows settings
        $pdf->SetWidths(array(77,60,20,20)); 
        $pdf->SetAligns(array('L','L','R','R'));
        $pdf->setRowFillColor(205,205,205);

        $pdf->SetFont('Arial','',9);

        foreach($_POST['movCaixaID'] as $idmovcaixa){

            $link = "/movimentocaixadetail/{$idmovcaixa}";
            $ret = $this->_returnApiData($link);
            //echo "<pre>", print_r($ret,true), "</pre>";
            if(!$ret) {
                if($this->log)
                    $this->logIt("Can't get data - User: ".$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                continue;
            }

            $dthour = $ret[0]["DataHora"];
            $type = $ret[0]["CAITipo"];
            $identification = $ret[0]["Identificacao"];
            $description = $ret[0]["CAIDescricao"];
            $inVal = $ret[0]["CAICredito"];
            $outVal = $ret[0]["CAIDebito"];
            $destinationAcc = $ret[0]["CTADestino"];
            
            if($inVal != 0) $TotalC += $inVal;
            if($outVal != 0)  $TotalD += $outVal;
            
            $inVal = ($inVal == 0) ? '' : number_format($inVal,2,',','.');
            $outVal = ($outVal == 0) ? '' : number_format($outVal,2,',','.');
            
            $identificationF =  ($type == 'S' and $identification == '') ? $destinationAcc : $identification;

            $pdf->Cell($leftMargin);
            $pdf->Row(array(
                html_entity_decode(utf8_decode($identificationF),ENT_QUOTES, "ISO8859-1"),
                html_entity_decode(utf8_decode($description),ENT_QUOTES, "ISO8859-1"),
                html_entity_decode(utf8_decode($inVal),ENT_QUOTES, "ISO8859-1"),
                html_entity_decode(utf8_decode($outVal),ENT_QUOTES, "ISO8859-1")
            ));
            
        }

        $pdf->SetFont('Arial','B',9);
        $pdf->Cell($leftMargin);
        $pdf->Cell(137,5,html_entity_decode(utf8_decode($this->getLanguageWord('scm_request_total')),ENT_QUOTES, "ISO8859-1"),0,0,'R');
        $pdf->Cell(20,5,html_entity_decode(number_format($TotalC,2,',','.'),ENT_QUOTES, "ISO8859-1"),1,0,'R');
        $pdf->Cell(20,5,html_entity_decode(number_format($TotalD,2,',','.'),ENT_QUOTES, "ISO8859-1"),1,1,'R');

        $pdf->Ln(20);

        //Opening Balance
        $dtEntry = str_replace("'", "", $this->formatSaveDate($dtProcess));
        $openingBalance = $this->dbFinancial->getOpeningBalance($companyID,$dtEntry);
        if(is_array($openingBalance)){
            if($this->log)
                $this->logIt($openingBalance['message'] . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);

            return false;
        }

        $pdf->Cell($leftMargin);
        $pdf->Cell(25,5,html_entity_decode(utf8_decode(strtoupper($this->getLanguageWord('FIN_opening_balance'))).":",ENT_QUOTES, "ISO8859-1"),0,0,'R');
        $pdf->Cell(20,5,html_entity_decode(number_format($openingBalance,2,',','.'),ENT_QUOTES, "ISO8859-1"),0,1,'R');

        //Final Balance
        $balanceTmp = $openingBalance + $TotalC - $TotalD;
        $saveBalance = $this->saveBalance($companyID,$dtEntry,$balanceTmp);
        if(!$saveBalance){
            if($this->log)
                $this->logIt('Can\'t save balance - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
            return false;
        }

        $pdf->Cell($leftMargin);
        $pdf->Cell(25,5,html_entity_decode(utf8_decode(strtoupper($this->getLanguageWord('FIN_final_balance'))).":",ENT_QUOTES, "ISO8859-1"),0,0,'R');
        $pdf->Cell(20,5,html_entity_decode(number_format($balanceTmp,2,',','.'),ENT_QUOTES, "ISO8859-1"),0,1,'R');

        $filename = $_SESSION['SES_LOGIN_PERSON'] . "_cash_report_".time().".pdf";
        $fileNameWrite = $this->helpdezkPath . '/app/downloads/tmp/'. $filename ;
        $fileNameUrl = $this->helpdezkUrl . '/app/downloads/tmp/'. $filename ;

        if(!is_writable($this->helpdezkPath . '/app/downloads/tmp')) {
            if( !chmod($this->helpdezkPath . '/app/downloads/tmp', 0777) )
                $this->logIt("Cash Report " . ' - Directory ' . $this->helpdezkPath . '/app/downloads/tmp' . ' is not writable ' ,3,'general',__LINE__);

        }

        $pdf->Output($fileNameWrite,'F');

        echo $fileNameUrl;
        
    }

    function saveBalance($companyID,$dtEntry,$balance)
    {
        $retBalance = $this->dbFinancial->getCashBalance("WHERE idperson = {$companyID} AND dtentry = '{$dtEntry}'");
        
        $this->dbFinancial->beginTrans();
        
        if(is_array($retBalance)){
            if($this->log)
                $this->logIt($retBalance['message'] . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
            return false;
        }

        if($retBalance->RecordCount() != 0){
            $id = $retBalance->fields['idcashbalance'];

            if($retBalance->fields['balance_value'] != $balance){
                $upd = $this->dbFinancial->updateBalance($balance,$id);
                if(is_array($upd) && $upd['status'] == "Error"){
                    $this->dbFinancial->rollbackTrans();
                    if($this->log)
                        $this->logIt($upd['message'] . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
                    return false;
                }
            }
        }else{
            $ins = $this->dbFinancial->insertBalance($companyID,$dtEntry,$balance);
            if(is_array($ins) && $ins['status'] == "Error"){
                $this->dbFinancial->rollbackTrans();
                if($this->log)
                    $this->logIt($ins['message'] . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
                return false;
            }
            $id = $ins;
        }

        //log
        $balanceLog = $this->dbFinancial->saveBalanceLog($id,$balance,$_SESSION['SES_COD_USUARIO']);
        //echo "<pre>", print_r($balanceLog,true), "</pre>";
        if(is_array($balanceLog) && $balanceLog['status'] == "Error"){
            $this->dbFinancial->rollbackTrans();
            if($this->log)
                $this->logIt($balanceLog['message'] . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
            return false;
        }
        
        $this->dbFinancial->commitTrans();

        return true;
    }


}