<?php

require_once(HELPDEZK_PATH . '/app/modules/fin/controllers/finCommonController.php');

class finBankReturnExport extends finCommon
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
        $this->idprogram =  $this->getIdProgramByController('finBankReturnExport');

        $this->loadModel('company_model');
        $dbCompany = new company_model();
        $this->dbCompany = $dbCompany;

        $this->loadModel('bank_model');
        $dbBank = new bank_model();
        $this->dbBank = $dbBank;

        $this->loadModel('admin/logos_model');
        $dbLogo = new logos_model();
        $this->dbLogo = $dbLogo;

        $this->breakLine = chr(13);
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

        // --- Bank ---
        if($arrCompany['ids'][0]){
            $arrBank = $this->_comboBankLegacyID($arrCompany['ids'][0]);
            $smarty->assign('bankids',  $arrBank['ids']);
            $smarty->assign('bankvals', $arrBank['values']);
            $smarty->assign('idbank', $arrBank['ids'][0] );
        }

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
            $smarty->display('fin-bank-return-export.tpl');
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

        $idBank = $_POST['cmbBank'];
        $dtstart = str_replace("'", "", $this->formatSaveDate($_POST['dtstart']));
        $dtfinish = str_replace("'", "", $this->formatSaveDate($_POST['dtfinish']));

        $link = "/retornobancario/{$idCompanyLegacy->fields['idperseus']}/{$idBank}/{$dtstart}/{$dtfinish}";
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
        set_time_limit(0);
        
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }
        //echo "<pre>", print_r($_POST,true), "</pre>";
        $arrNotExport = array();

        $idCompanyLegacy = $this->dbCompany->getCompanyLegacyID($_POST['companyID']);
        if (!$idCompanyLegacy) {
            if($this->log)
                $this->logIt("Can't get Company's Legacy ID  - User: ".$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $idUserDominio = $this->dbCompany->getDominioUserID($_SESSION['SES_COD_USUARIO']);
        if (!$idUserDominio) {
            if($this->log)
                $this->logIt("Can't get User's Dominio ID  - User: ".$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }


        //FILE HEADER
        $txt = $this->_formatFileField('01',2,'S');    //Identifier
        $txt .= $this->_formatFileField($idCompanyLegacy->fields['iddominio'],7,'N');   //Company ID
        $txt .= $this->_formatFileField($idCompanyLegacy->fields['ein_cnpj'],14,'S');   //CNPJ
        $txt .= $this->_formatFileField($_POST['dtstart'],10,'S');       //Initial date
        $txt .= $this->_formatFileField($_POST['dtfinish'],10,'S');       //Final date
        $txt .= $this->_formatFileField('N',1,'S');         //Fixed value "N"
        $txt .= $this->_formatFileField('05',2,'N');        //Note Type: 01 = Contabilidade; 02 = Entradas; 03 = Saídas;
                                                                             //           04 = Serviços; 05 = Contabilidade-Lançamentos em lote
        $txt .= $this->_formatFileField('00000',5,'N');     //Constant "00000"
        $txt .= $this->_formatFileField('1',1,'N');         //System: 1 = Contabilidade; 2 = Caixa; 0 = Outro
        $txt .= $this->_formatFileField('17',2,'N');        //Fixed value "17"
        $txt .= $this->breakLine;
        $i = 1;

        foreach($_POST['mensalidadeID'] as $idmensalidade){

            $link = "/retornodetail/{$idmensalidade}";
            $ret = $this->_returnApiData($link);

            if(!$ret) {
                if($this->log)
                    $this->logIt("Can't get data - User: ".$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);

                continue;
            }

            //Search the crebit account code (customer) in system Dominio
            $link2 = "/getclientid/{$idCompanyLegacy->fields['idperseus']}/{$ret[0]["RESCpf"]}";
            $retCredAcc = $this->_returnApiData($link2);
            //echo "<pre>", print_r($retCredAcc,true), "</pre>"; die();
            if(!$retCredAcc) {
                if($this->log)
                    $this->logIt("Can't get dominio's client ids - User: ".$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);

                $bus = array(
                    "RESNome"       => $ret[0]["RESNome"],
                    "MENParcela"    => $ret[0]["MENParcela"],
                    "MENValorPago"  => number_format($ret[0]["MENValorPago"],2,',','.'),
                    "MENVencimento" => $ret[0]["MENVencimento"],
                    "MENPagamento"  => $ret[0]["MENPagamento"]
                );
                array_push($arrNotExport, $bus);
                continue;
            }
            $credAcc = $retCredAcc[0]["codi_cta"];

            if($credAcc == ""){
                $bus = array(
                    "RESNome"       => $ret[0]["RESNome"],
                    "MENParcela"    => $ret[0]["MENParcela"],
                    "MENValorPago"  => number_format($ret[0]["MENValorPago"],2,',','.'),
                    "MENVencimento" => $ret[0]["MENVencimento"],
                    "MENPagamento"  => $ret[0]["MENPagamento"]
                );
                array_push($arrNotExport, $bus);
            }

            if($ret[0]["MENDiferenca"] == 0){
                $entryType = 'X';
            }elseif($ret[0]["MENDiferenca"] < 0){
                $entryType = 'C';
            }else{
                $entryType = 'D';
            }


            //BATCH DATA
            $txt .= $this->_formatFileField('02',2,'S');  //Identifier
            $txt .= $this->_formatFileField($i,7,'N');    //Sequential code
            $txt .= $this->_formatFileField($entryType,1,'S');  //Type: D = Um débito para vários créditos; C = Um crédito para vários débitos;
                                                                           //      X = Um débito para um crédito; V = Vários débitos para vários créditos
            $txt .= $this->_formatFileField($ret[0]["MENDataCredito"],10,'S');   //Entry date
            $txt .= $this->_formatFileField($idUserDominio->fields['dominiouser'],30,'S');  //User
            $txt .= $this->_formatFileField('',100,'S');    //White spaces
            $txt .= $this->breakLine;

            $i++;

            //Search the debit account code (bank) in system Dominio
            $retDebAcc = $this->dbBank->getCompanyBanksExport("AND a.idperseus = '{$ret[0]["BANCodigo"]}'");
            if (!$retDebAcc) {
                if($this->log)
                    $this->logIt("Can't get Debit Account Code - User: ".$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                continue;
            }
            $debAcc = $retDebAcc->fields['iddominio'];

            $dueValue = number_format($ret[0]["MENValorAteVencimento"],2,'','');
            $diffValue = ($entryType == 'C') ? ($ret[0]["MENDiferenca"] * -1) : $ret[0]["MENDiferenca"];
            $diffValue = number_format($diffValue,2,'','');
            $paidValue = number_format($ret[0]["MENValorPago"],2,'','');

            //ACCOUNTING ENTRYS
            switch($entryType) {
                case 'C':

                    $txt .= $this->_formatFileField('03', 2, 'S');    //Identifier
                    $txt .= $this->_formatFileField($i, 7, 'N');            //Sequential code
                    $txt .= $this->_formatFileField('0000000', 7, 'N');       //Debit Account
                    $txt .= $this->_formatFileField($credAcc, 7, 'N');      //Credit Account
                    $txt .= $this->_formatFileField($dueValue, 15, 'N');       //Entry value
                    $txt .= $this->_formatFileField($idCompanyLegacy->fields['history_code'], 7, 'N');    //History code
                    $txt .= $this->_formatFileField('', 512, 'S');   //History complement
                    $txt .= $this->_formatFileField($idCompanyLegacy->fields['iddominio'], 7, 'N');   //Branch ou Head Office Code
                    $txt .= $this->_formatFileField('', 100, 'S');    //White spaces
                    $txt .= $this->breakLine;

                    $i++;

                    $txt .= $this->_formatFileField('03', 2, 'S');    //Identifier
                    $txt .= $this->_formatFileField($i, 7, 'N');            //Sequential code
                    $txt .= $this->_formatFileField($debAcc, 7, 'N');       //Debit Account
                    $txt .= $this->_formatFileField('0000000', 7, 'N');   //Credit Account
                    $txt .= $this->_formatFileField($paidValue, 15, 'N');       //Entry value
                    $txt .= $this->_formatFileField($idCompanyLegacy->fields['history_code'], 7, 'N');    //History code
                    $txt .= $this->_formatFileField('', 512, 'S');   //History complement
                    $txt .= $this->_formatFileField($idCompanyLegacy->fields['iddominio'], 7, 'N');   //Branch ou Head Office Code
                    $txt .= $this->_formatFileField('', 100, 'S');    //White spaces
                    $txt .= $this->breakLine;

                    $i++;

                    $txt .= $this->_formatFileField('03', 2, 'S');    //Identifier
                    $txt .= $this->_formatFileField($i, 7, 'N');            //Sequential code
                    $txt .= $this->_formatFileField('371', 7, 'N');       //Debit Account
                    $txt .= $this->_formatFileField('0000000', 7, 'N');      //Credit Account
                    $txt .= $this->_formatFileField($diffValue, 15, 'N');       //Entry value
                    $txt .= $this->_formatFileField($idCompanyLegacy->fields['history_code'], 7, 'N');     //History code
                    $txt .= $this->_formatFileField('', 512, 'S');   //History complement
                    $txt .= $this->_formatFileField($idCompanyLegacy->fields['iddominio'], 7, 'N');   //Branch ou Head Office Code
                    $txt .= $this->_formatFileField('', 100, 'S');    //White spaces
                    $txt .= $this->breakLine;

                    $i++;
                    break;
                case 'D':
                    $txt .= $this->_formatFileField('03', 2, 'S');    //Identifier
                    $txt .= $this->_formatFileField($i, 7, 'N');            //Sequential code
                    $txt .= $this->_formatFileField($debAcc, 7, 'N');       //Debit Account
                    $txt .= $this->_formatFileField('0000000', 7, 'N');   //Credit Account
                    $txt .= $this->_formatFileField($paidValue, 15, 'N');       //Entry value
                    $txt .= $this->_formatFileField($idCompanyLegacy->fields['history_code'], 7, 'N');    //History code
                    $txt .= $this->_formatFileField('', 512, 'S');   //History complement
                    $txt .= $this->_formatFileField($idCompanyLegacy->fields['iddominio'], 7, 'N');   //Branch ou Head Office Code
                    $txt .= $this->_formatFileField('', 100, 'S');    //White spaces
                    $txt .= $this->breakLine;

                    $i++;

                    $txt .= $this->_formatFileField('03', 2, 'S');    //Identifier
                    $txt .= $this->_formatFileField($i, 7, 'N');            //Sequential code
                    $txt .= $this->_formatFileField('0000000', 7, 'N');       //Debit Account
                    $txt .= $this->_formatFileField($credAcc, 7, 'N');      //Credit Account
                    $txt .= $this->_formatFileField($dueValue, 15, 'N');       //Entry value
                    $txt .= $this->_formatFileField($idCompanyLegacy->fields['history_code'], 7, 'N');    //History code
                    $txt .= $this->_formatFileField('', 512, 'S');   //History complement
                    $txt .= $this->_formatFileField($idCompanyLegacy->fields['iddominio'], 7, 'N');   //Branch ou Head Office Code
                    $txt .= $this->_formatFileField('', 100, 'S');    //White spaces
                    $txt .= $this->breakLine;

                    $i++;

                    $txt .= $this->_formatFileField('03', 2, 'S');    //Identifier
                    $txt .= $this->_formatFileField($i, 7, 'N');            //Sequential code
                    $txt .= $this->_formatFileField('0000000', 7, 'N');       //Debit Account
                    $txt .= $this->_formatFileField('433', 7, 'N');      //Credit Account
                    $txt .= $this->_formatFileField($diffValue, 15, 'N');       //Entry value
                    $txt .= $this->_formatFileField($idCompanyLegacy->fields['history_code'], 7, 'N');     //History code
                    $txt .= $this->_formatFileField('', 512, 'S');   //History complement
                    $txt .= $this->_formatFileField($idCompanyLegacy->fields['iddominio'], 7, 'N');   //Branch ou Head Office Code
                    $txt .= $this->_formatFileField('', 100, 'S');    //White spaces
                    $txt .= $this->breakLine;

                    $i++;

                    break;
                default:
                    $txt .= $this->_formatFileField('03', 2, 'S');    //Identifier
                    $txt .= $this->_formatFileField($i, 7, 'N');            //Sequential code
                    $txt .= $this->_formatFileField($debAcc, 7, 'N');       //Debit Account
                    $txt .= $this->_formatFileField($credAcc, 7, 'N');      //Credit Account
                    $txt .= $this->_formatFileField($paidValue, 15, 'N');   //Entry value
                    $txt .= $this->_formatFileField($idCompanyLegacy->fields['history_code'], 7, 'N');    //History code
                    $txt .= $this->_formatFileField('', 512, 'S');   //History complement
                    $txt .= $this->_formatFileField($idCompanyLegacy->fields['iddominio'], 7, 'N');   //Branch ou Head Office Code
                    $txt .= $this->_formatFileField('', 100, 'S');                      //White spaces
                    $txt .= $this->breakLine;

                    $i++;

                    break;
            }
        }

        //FILE END
        $txt .= $this->_formatFileField('99',2);    //Identifier
        $txt .= $this->_formatFileField('99999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999',98);             //Finisher

        /*$filename = 'exportaRetorno_'.str_replace('/','',$_POST['dtstart']).'.txt';
        $fileNameWrite = $this->helpdezkPath . '/app/downloads/tmp/'. $filename ;

        if(!is_writable($this->helpdezkPath . '/app/downloads/tmp')) {
            if( !chmod($this->helpdezkPath . '/app/downloads/tmp', 0777) )
                $this->logIt("Export Bank Return " . ' - Directory ' . $this->helpdezkPath . '/app/downloads/tmp' . ' is not writable ' ,3,'general',__LINE__);

        }

        $fp = fopen($fileNameWrite, 'w+t');
        fwrite($fp,$txt);
        fclose($fp);

        $fileNameUrl = $this->helpdezkUrl . '/app/downloads/tmp/'. $filename ;
        header('Set-Cookie: fileDownload=true; path=/');
        header('Cache-Control: max-age=60, must-revalidate');
        header("Content-type: application/text");
        header('Content-Disposition: attachment; filename="'.$fileNameUrl.'"');

        echo $fileNameUrl;*/

        $arrRet =  array(
            'notexport' => $arrNotExport,
            'txt' => $txt
        );

        echo json_encode($arrRet);

    }


}