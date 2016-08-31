<?php

class erpFileReturnBank extends Controllers {
    /**
     * Create an instance, check session time
     *
     * @access public
     */
    public function __construct(){
        parent::__construct();
        session_start();
        $this->validasessao();

        if (path == "/..") {
            $erpclasspath  = DOCUMENT_ROOT . '/includes/classes/pipegrep/ErpClass.php' ;
            $classesPath = DOCUMENT_ROOT . '/includes/classes/';
        } else {
            $erpclasspath = DOCUMENT_ROOT . path . '/includes/classes/pipegrep/ErpClass.php';
            $classesPath  = DOCUMENT_ROOT . path . '/includes/classes/';
        }

        /*
         * https://github.com/manoelcampos/Retorno-BoletoPHP
         */
        require_once($erpclasspath);
        require_once($classesPath . 'febraban/BankReturn.php');
        require_once($classesPath . 'febraban/ReturnCnab400Sicredi.php');

    }

    /**
     * Create a Smarty instance and show the view.
     *
     * @access public
     */
    public function index() {
        $user = $_SESSION['SES_COD_USUARIO'];
        $bd = new home_model();
        $typeperson = $bd->selectTypePerson($user);
        $program = $bd->selectProgramIDByController("erpFileReturnBank/");
        $access = $this->access($user,$program,$typeperson);
        $smarty = $this->retornaSmarty();
        $smarty->display('erpFileReturnBank.tpl.html');
    }

    /**
     * Show the insert modal.
     *
     * @access public
     */
    public function modalStart(){

        $smarty = $this->retornaSmarty();

        /*
         * Companys
         */
        $dbperson = new person_model();
        $select = $dbperson->getErpCompanies("WHERE idtypeperson = 7","ORDER BY name ASC");
        while (!$select->EOF) {
            $fieldsID[] = $select->fields['idcompany'];
            $values[]   = $select->fields['name'];
            $select->MoveNext();
        }
        $smarty->assign('corpId',  $fieldsID);
        $smarty->assign('corpVal', $values);

        $smarty->display('modais/erpfilereturnbank/start.tpl.html');
    }



    public function upload()
    {
        //print_r($_POST);
        //print_r($_FILES);
        //print_r($_GET);
        $data = array();

        // First
        if(isset($_GET['url']))
        {
            $error = false;
            $files = array();

            $uploaddir = 'app/uploads/tmp/';
            foreach($_FILES as $file)
            {
                if(move_uploaded_file($file['tmp_name'], $uploaddir .basename($file['name'])))
                {
                    // $files[] = $uploaddir .$file['name'];
                    $files[] = $file['name'];
                }
                else
                {
                    $error = true;
                }
            }
            $data = ($error) ? array('error' => 'There was an error uploading your files') : array('files' => $files);
        }
        // Second
        else
        {
            $data = array('success' => 'Form was submitted', 'formData' => $_POST);
        }
        if(isset($_POST['idbankaccount'])) {
            $data = array('success' => 'Form was submitted', 'formData' => $_POST);
        }

        echo json_encode($data);

    }


    /**
     * Show the Grid modal.
     *
     * @access public
     */
    public function modalGrid()
    {

        $smarty = $this->retornaSmarty();

        $dbBilling = new erpbilling_model();
        $dbBankAccount = new erpbankaccount_model();

        $rsBankAccount = $dbBankAccount->getErpBankAccount('AND a.idbankaccount = '. $this->getParam('idbankaccount'),'','') ;


        if ($rsBankAccount->fields['code'] == '33') {                       // -- SANTANDER parse procedures --

        } else if ($rsBankAccount->fields['code'] == '748') {               // -- SICRED parse procedures --

            $codBank = $rsBankAccount->fields['code'];
            $fileName = 'app/uploads/tmp/' . $this->getParam('filename');

            $BankReturn = new BankReturn();
            $cnabFormat = $BankReturn->FileNumberOfLines($fileName) ;

            if ($cnabFormat == '240') {

            } else if ($cnabFormat = '400') {
                if ($codBank == "748") {
                    $CNAB400 = new ReturnCnab400Sicredi();
                }
            }

            $aLines = file($fileName);

            $i = 1;
            $list = array();

            foreach($aLines as $numLn => $line) {

                $observation = '&nbsp;';
                $disable = false ;

                $arrLine = $CNAB400->runLine($numLn, $line );

                if ($arrLine['registro'] == $CNAB400::DETAIL and $arrLine['ocorrencia'] == "06") {
                    $yyyy = substr($arrLine['data_credito'],0,4);
                    $mm   = substr($arrLine['data_credito'],4,2);
                    $dd   = substr($arrLine['data_credito'],6,2);
                    $dt_formatted = $dd."/".$mm."/".$yyyy;

                    if ($this->getConfig('lang') == 'pt_BR') {
                        $vlrLancamento = substr_replace($arrLine['valor_recebido'], '.', -2, 0);
                        $vlrLancamento = ltrim($vlrLancamento,0) ;
                        $am_formatted = number_format($vlrLancamento,2,",",".");
                    }

                    $idinvoice = substr($arrLine['nosso_numero'], 3,5);

                    $rsInvoice = $dbBilling->getInvoice("WHERE a.idinvoice = $idinvoice") ;
                    if($rsInvoice->RecordCount() == 0 ) {
                        $disable = true ;
                        $memo = 'Título Inexistente' ;
                        $observation = 'Número: ' . $idinvoice ;
                    } else {
                        $custname = $dbBilling->getCustomerByInvoice($idinvoice);
                        $rsBilling = $dbBilling->getInvoicePaymentDetails('WHERE idinvoice='.$idinvoice);
                        if ($rsBilling->RecordCount() > 0) {
                            $disable = true ;
                            $memo = $custname ;
                            $observation = 'Título Pago' ;
                        } else {
                            $memo = $custname ;
                            $observation = 'Número: ' . $idinvoice ;
                        }
                    }

                    array_push($list, array	(
                        'id'		        => $i,
                        'memo' 	 	        => $memo,
                        'date'		        => $dt_formatted,
                        'desabilita'	    => $disable,
                        'amount'            => $am_formatted,
                        'amount_noformat'   => $vlrLancamento,
                        'occurrence'        => $arrLine['ocorrencia'],
                        'reason'            => $arrLine['motivo_ocorrencia'],
                        'operation'         => 'C',
                        'observation'       => $observation,
                        'invoice'           => $idinvoice,
                        'rebate'            => $arrLine["valor_abatimento"],
                        'discount'          => $arrLine["desconto_concedido"],
                        'interest'          => $arrLine["juros_mora"] ,
                        'finebank'          => $arrLine["multa"]
                    ));

                    $i++;
               }
            }

        }

        $smarty->assign('lista', $list);
        $smarty->assign('idcompany', $this->getParam('idcompany'));
        $smarty->assign('idbankaccount', $this->getParam('idbankaccount'));

        $smarty->display('modais/erpfilereturnbank/grid.tpl.html');

    }

    public function writeDB(){

        if(!$_POST) {
            return false;
        }

        $dbBankAccount  = new erpbankaccount_model();
        $dbBilling      = new erpbilling_model();
        $dbCashBank     = new erpcashbank_model();

        $rsBankAccount = $dbBankAccount->getErpBankAccount('AND a.idbankaccount = '. $_POST['idbankaccount']) ;

        $i      = 1;
        $paid   = 0;
        $err    = false;
        $count  = count($_POST['hid_operation']) ;
        $dbCashBank->BeginTrans();
        $dbBilling->BeginTrans();
        while ($i <= $count) {
            if ($_POST['ID'][$i] == $i) {
                $idinvoice = $_POST['hid_invoice'][$i] ;
                $dtentry = $this->formatSaveDate($_POST['hid_date'][$i]);
                if ($rsBankAccount->fields['code'] == '33') {                                     // -- SANTANDER --

                } else if ($rsBankAccount->fields['code'] == '748') {                             // -- SICREDI --
                    $amount = $this->SicrediCurrency($_POST['hid_amount'][$i]) ;
                    $rebate = $this->SicrediCurrency($_POST['hid_rebate'][$i]) ;
                    $discount = $this->SicrediCurrency($_POST['hid_discount'][$i]) ;
                    $finebank = $this->SicrediCurrency($_POST['hid_finebank'][$i]) ;
                    $interest = $this->SicrediCurrency($_POST['hid_interest'][$i]) ;
                }
                if ($amount < 0) $amount = ($amount * -1) ;

                // Make description
                $custname = $dbBilling->getCustomerByInvoice($idinvoice);
                $description = 'Pagto. Ciente ' . $custname ;
                // Get Invoice Itens
                $and = "AND b.idperson=".$_POST['idcompany']." AND a.idinvoice=".$idinvoice  ;
                $rsInvoiceItens = $dbBilling->getInvoiceItens($and) ;
                // Get Invoice Status
                $rsInvoice = $dbBilling->getInvoice("AND a.idinvoice=$idinvoice") ;
                $invoiceStatus = $rsInvoice->fields['status'] ;
                // Write into erp_tbBankEntry
                while (!$rsInvoiceItens->EOF) {
                    $ret = $dbCashBank->insertErpBankEntry($_POST['idcompany'],$_POST['idbankaccount'],$rsInvoiceItens->fields['idaccount'], $description,$dtentry,$amount, $_POST['hid_operation'][$i]);
                    if(!$ret){
                        $dbCashBank->RollbackTrans();
                        $err = true;
                    }
                    $rsInvoiceItens->MoveNext();
                }
                // Update Invoice Status
                $ret = $dbBilling->updateInvoiceStatus($idinvoice,'paid');
                if(!$ret){
                    $dbCashBank->RollbackTrans();
                    $dbBilling->RollbackTrans();
                    $err = true;
                }
                // Insert Invoice Log
                $ret = $dbBilling->insertErpBillingLog($idinvoice,$invoiceStatus,'paid','Invoice paid by bank.');
                if(!$ret){
                    $dbCashBank->RollbackTrans();
                    $dbBilling->RollbackTrans();
                    $err = true;
                }
                // Insert Payment Details
                $ret = $dbBilling->insertInvoicePayment($idinvoice,$_POST['hid_occurrence'][$i],$_POST['hid_reason'][$i],$dtentry,$rebate,$discount,$finebank,$interest,$amount);
                if($err){
                    echo "erro" ;
                    exit;
                } else {
                    $dbCashBank->CommitTrans();
                    $dbBilling->CommitTrans();
                    $paid++;
                }
            }

            $i++;

        }

        // delete file
        $uploaddir = 'app/uploads/tmp/';
        unlink($uploaddir.$_POST['filename']) ;

        echo $paid;

    }

    public function SicrediCurrency($sicrediAmount)
    {
        $vlr = substr_replace($sicrediAmount, ',', -2, 0);
        $vlr = ltrim($vlr,0) ;
        return $this->BrasilianCurrencyToMysql($vlr) ;
    }
    public function testCompanyDef()
    {
        echo json_encode(array("idcompany" => $this->getModuleParam('erp','erp_company_default') ));
    }
}

?>
