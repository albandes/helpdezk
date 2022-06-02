<?php

require_once(HELPDEZK_PATH . '/includes/classes/pipegrep/syslog.php');

class finCommon extends Controllers
{
    public static $_logStatus;

    public function __construct()
    {
        parent::__construct();

        $this->program  = basename( __FILE__ );

        // Log settings
        $objSyslog = new Syslog();
        $this->log  = $objSyslog->setLogStatus() ;
        self::$_logStatus = $objSyslog->setLogStatus() ;
        if ($this->log) {
            $this->_logLevel = $objSyslog->setLogLevel();
            $this->_logHost = $objSyslog->setLogHost();
            if($this->_logHost == 'remote')
                $this->_logRemoteServer = $objSyslog->setLogRemoteServer();
        }

        $this->_serverApi = $this->_getServerApi();

        $this->loadModel('company_model');
        $dbCompany = new company_model();
        $this->dbCompany = $dbCompany;

        $this->loadModel('bank_model');
        $dbBank = new bank_model();
        $this->dbBank = $dbBank;


    }

    public function _makeNavFin($smarty)
    {
        $idPerson = $_SESSION['SES_COD_USUARIO'];
        $listRecords = $this->makeMenuByModule($idPerson,$this->idmodule);
        $moduleinfo = $this->getModuleInfo($this->idmodule);

        //$smarty->assign('displayMenu_1',1);
        $smarty->assign('listMenu_1',$listRecords);
        $smarty->assign('moduleLogo',$moduleinfo->fields['headerlogo']);
        $smarty->assign('modulePath',$moduleinfo->fields['path']);

    }

    public function _comboCompanies()
    {
        $rs = $this->dbCompany->getErpCompanies('WHERE idtypeperson = 7','ORDER BY `name`');
        while (!$rs->EOF) {
            $fieldsID[] = $rs->fields['idcompany'];
            $values[]   = $rs->fields['name'];
            $rs->MoveNext();
        }
        //echo"<pre>"; print_r($rs); echo"</pre>";
        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;

    }

    public function _comboBank($idcompany=null)
    {
        $where = "";
        if($idcompany) $where = "AND idperson = $idcompany";

        $rs = $this->dbBank->getCompanyBanks($where,"ORDER BY name");
        while (!$rs->EOF) {
            $fieldsID[] = $rs->fields['idbank'];
            $values[]   = $rs->fields['name'];
            $rs->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;

    }

    // class FPDF with extention to parsehtml
    public function _returnfpdfhdk() {
        require_once($this->helpdezkPath. "/app/modules/fin/lib/classes/fpdf/fpdfhdk.php");
        $pdf = new fpdfhdk();
        return $pdf;
    }

    function _getServerApi()
    {

        $sessionVal = $_SESSION['fin']['server_api'] ;
        if (isset($sessionVal) && !empty($sessionVal)) {
            return $sessionVal;
        } else {
            if ($this->log)
                $this->logIt('Url da API sem valor - Variavel de sessao: $_SESSION[\'fin\'][\'server_api\']' . $_SESSION['SES_LOGIN_PERSON'] . ' - program: ' . $this->program . ' - method: ' . __METHOD__, 3, 'general', __LINE__);
            return false ;
        }

    }

    public function _returnApiData($link,$srvname='Perseus')
    {
        $ctx = stream_context_create(array(
                'http' => array(
                    'timeout' => 30
                )
            )
        );

        $response = file_get_contents($this->_serverApi . $link,false,$ctx);

        if($response) {
            $response = json_decode($response, true);

            if (!$response['status']){
                if ($this->log)
                    $this->logIt("Didn't return data from {$srvname}'s server. link: {$this->_serverApi}{$link} - User: " . $_SESSION['SES_LOGIN_PERSON'] . ' - program: ' . $this->program . ' - method: ' . __METHOD__, 3, 'general', __LINE__);
                return false;
            }

            return $response['result'];

        }else{
            return false;
        }

    }

    public function _makeBankSlip($data)
    {
        switch ($data['banco']){
            case "033":
                require_once($this->helpdezkPath . "/app/modules/fin/lib/classes/makeBoleto/Santander.php");
                $bankSlip = new Santander($data);
                break;
            default:
                require_once($this->helpdezkPath . "/app/modules/fin/lib/classes/makeBoleto/Sicredi.php");
                $bankSlip = new Sicredi($data);
                break;

        }
        
        $ret = $bankSlip->setParams();

        $file = $this->makefile($ret,$data['month']);
        if(!$file){
            $this->logIt('Could not return the bank slip file - program: '.$this->program ,3,'general',__LINE__);
            return false;
        }
        return $file;

    }

    public function makefile($data,$competence)
    {
        require_once ($this->helpdezkPath . "/" . FPDF ."fpdf.php");
        require_once ($this->helpdezkPath . "/" . FPDF ."i25.php");

        $pdf=new PDF_i25('P', 'mm', 'A4');
        $y= 30;

        $pdf->AddPage();

        /**
         **
         ** Recibo do Sacado
         **
         **/

        // Pontilhado
        $pdf->SetDrawColor(200);
        $pdf->SetFont('Arial','B',6);
        $pdf->DashLine(20,$y,204,0.2,50);
        $pdf->SetXY(185,$y+1.5);
        $pdf->Image($data["company_logo"],20,$y+2,13,12);
        $pdf->Cell(20,0,html_entity_decode(utf8_decode("Recibo do Sacado"),ENT_QUOTES, "ISO8859-1"),0,0,'R');
        $y=$y+20;
        $pdf->Image($data["bank_logo"],20,$y,41.66,11.11);
        $pdf->Line(62,$y+5, 62, $y+11) ;
        $pdf->SetFont('Arial','B',20);
        $pdf->Text(63,$y+10, $data["codigo_banco_com_dv"]) ;
        $pdf->Line(82,$y+5, 82, $y+11) ;
        $pdf->SetFont('Arial','B',11);
        $pdf->SetXY(185,$y+9);
        $pdf->Cell(20,0,$data["linha_digitavel"],0,0,'R');
        $y=$y+11;

        // Primeira Linha
        $pdf->SetFont('Arial','',7);
        $pdf->Line(20,$y, 204, $y) ;

        // -- Cedente --
        $pdf->Text(22,$y+2.5, html_entity_decode(utf8_decode('Cedente'),ENT_QUOTES, "ISO8859-1")) ;
        $pdf->SetFont('Arial','B',7);
        $pdf->Text(22,$y+6, html_entity_decode($data["cedente"],ENT_QUOTES, "ISO8859-1")) ;

        // --- Agência/Código do Cedente ---
        $pdf->Line(108,$y, 108, $y+7) ;
        $pdf->SetFont('Arial','',7);
        $pdf->Text(110,$y+2.5, html_entity_decode(utf8_decode('Agência/Código do Cedente'),ENT_QUOTES, "ISO8859-1")) ;
        $pdf->SetFont('Arial','B',7);
        $tmp2 = $this->makeAgencyData($data);
        $pdf->Text(110,$y+6,$tmp2) ;

        // -- Espécie --
        $pdf->Line(142,$y, 142, $y+7) ;
        $pdf->SetFont('Arial','',7);
        $pdf->Text(144,$y+2.5, html_entity_decode(utf8_decode('Espécie'),ENT_QUOTES, "ISO8859-1")) ;
        $pdf->SetFont('Arial','B',7);
        $pdf->Text(144,$y+6, 'R$') ;

        // -- Quantidade --
        $pdf->Line(155,$y, 155, $y+7) ;
        $pdf->SetFont('Arial','',7);
        $pdf->Text(157,$y+2.5, html_entity_decode(utf8_decode('Quantidade'),ENT_QUOTES, "ISO8859-1")) ;
        $pdf->SetFont('Arial','B',7);
        $pdf->Text(157,$y+6, $data["quantidade"]) ;

        // -- Nosso número
        $pdf->Line(172,$y, 172, $y+7) ;
        $pdf->SetFont('Arial','',7);
        $pdf->Text(174,$y+2.5, html_entity_decode(utf8_decode('Nosso número'),ENT_QUOTES, "ISO8859-1")) ;
        $pdf->SetFont('Arial','B',7);
        $pdf->SetXY(185,$y+5);
        $tmp = $data["nosso_numero"];
        $pdf->Cell(20,0,$tmp,0,0,'R');

        // Segunda Linha
        $y = $y+7;
        $pdf->SetFont('Arial','',7);
        $pdf->Line(20,$y, 204, $y) ;
        $pdf->Text(22,$y+2.5, html_entity_decode(utf8_decode('Número do documento'),ENT_QUOTES, "ISO8859-1")) ;
        $pdf->SetFont('Arial','B',7);
        $pdf->Text(22,$y+6, $data["numero_documento"]) ;

        // --------------------------------------
        $pdf->Line(57,$y, 57, $y+7) ;
        $pdf->SetFont('Arial','',7);
        $pdf->Text(59,$y+2.5, html_entity_decode(utf8_decode('CPF/CNPJ'),ENT_QUOTES, "ISO8859-1")) ;
        $pdf->SetFont('Arial','B',7);
        $pdf->Text(59,$y+6, $data["cpf_cnpj"]) ;

        // --------------------------------------
        $pdf->Line(98,$y, 98, $y+7) ;
        $pdf->SetFont('Arial','',7);
        $pdf->Text(100,$y+2.5, html_entity_decode(utf8_decode('Vencimento'),ENT_QUOTES, "ISO8859-1")) ;
        $pdf->SetFont('Arial','B',7);
        $pdf->Text(100,$y+6, $data["data_vencimento"]) ;

        // --------------------------------------
        $pdf->Line(153,$y, 153, $y+7) ;
        $pdf->SetFont('Arial','',7);
        $pdf->Text(155,$y+2.5, html_entity_decode(utf8_decode('Valor documento'),ENT_QUOTES, "ISO8859-1")) ;
        $pdf->SetFont('Arial','B',7);
        $pdf->SetXY(185,$y+5);
        $pdf->Cell(20,0,$data["valor_boleto"],0,0,'R');

        // Terceira Linha
        $y = $y+7;
        $pdf->SetFont('Arial','',7);
        $pdf->Line(20,$y, 204, $y) ;
        $pdf->Text(22,$y+2.5, html_entity_decode(utf8_decode('Sacado'),ENT_QUOTES, "ISO8859-1")) ;
        $pdf->SetFont('Arial','B',7);
        $pdf->Text(22,$y+6, html_entity_decode($data["sacado"],ENT_QUOTES, "ISO8859-1")) ;

        // Quarta Linha
        $y = $y+7;
        $pdf->SetFont('Arial','',7);
        $pdf->Line(20,$y, 204, $y) ;
        $pdf->Text(22,$y+2.5, html_entity_decode(utf8_decode('Demonstrativo'),ENT_QUOTES, "ISO8859-1")) ;
        $pdf->SetXY(185,$y+1.8);
        $pdf->Cell(20,0,html_entity_decode(utf8_decode('Autenticação mecânica'),ENT_QUOTES, "ISO8859-1"),0,0,'R');

        // Pontilhado
        $y=$y+15.5;
        $pdf->SetDrawColor(200);
        $pdf->SetFont('Arial','',6);
        $pdf->SetXY(185,$y);
        $pdf->Cell(20,0,html_entity_decode(utf8_decode("Corte na linha pontilhada"),ENT_QUOTES, "ISO8859-1"),0,0,'R');
        $pdf->DashLine(20,$y+1.5,204,0.2,50);

        /**
         **
         ** Boleto
         **
         **/

        $y=$y+5;
        // 1 px = 5mm
        $pdf->Image($data["bank_logo"],20,$y,41.66,11.11);
        $pdf->Line(62,$y+5, 62, $y+11) ;
        $pdf->SetFont('Arial','B',20);
        $pdf->Text(63,$y+10, $data["codigo_banco_com_dv"]) ;
        $pdf->Line(82,$y+5, 82, $y+11) ;
        $pdf->SetFont('Arial','B',11);
        $pdf->SetXY(185,$y+9);
        $pdf->Cell(20,0,$data["linha_digitavel"],0,0,'R');

        // Primeira Linha
        $y=$y+11;
        $pdf->SetFont('Arial','',7);
        $pdf->Line(20,$y, 204, $y) ;
        $pdf->Text(22,$y+2.5, html_entity_decode(utf8_decode('Local de Pagamento'),ENT_QUOTES, "ISO8859-1")) ;
        $pdf->SetFont('Arial','B',7);
        $pdf->Text(22,$y+6, html_entity_decode(utf8_decode($data['payment_local']),ENT_QUOTES, "ISO8859-1"));
        $pdf->Line(153,$y, 153, $y+7) ;
        $pdf->SetFont('Arial','',7);
        $pdf->Text(155,$y+2.5, html_entity_decode(utf8_decode('Vencimento'),ENT_QUOTES, "ISO8859-1")) ;
        $pdf->SetFont('Arial','B',7);
        $pdf->SetXY(185,$y+5);
        $pdf->Cell(20,0,$data["data_vencimento"],0,0,'R');

        // Segunda Linha
        $y = $y+7;
        $pdf->SetFont('Arial','',7);
        $pdf->Line(20,$y, 204, $y) ;
        $pdf->Text(22,$y+2.5, html_entity_decode(utf8_decode('Cedente'),ENT_QUOTES, "ISO8859-1")) ;
        $pdf->SetFont('Arial','B',7);
        $pdf->Text(22,$y+6, html_entity_decode($data["cedente"].' - '.$data["cpf_cnpj"],ENT_QUOTES, "ISO8859-1")) ;
        $pdf->Line(153,$y, 153, $y+7) ;
        $pdf->SetFont('Arial','',7);
        $pdf->Text(155,$y+2.5, html_entity_decode(utf8_decode('Agência/Código Cedente'),ENT_QUOTES, "ISO8859-1")) ;
        $pdf->SetFont('Arial','B',7);
        $pdf->SetXY(185,$y+5);
        $tmp2 = $this->makeAgencyData($data);
        $pdf->Cell(20,0,$tmp2,0,0,'R');

        // Terceira Linha
        $y = $y+7;
        $pdf->SetFont('Arial','',7);
        $pdf->Line(20,$y, 204, $y) ;
        $pdf->Text(22,$y+2.5, html_entity_decode(utf8_decode('Data do documento'),ENT_QUOTES, "ISO8859-1")) ;
        $pdf->SetFont('Arial','B',7);
        $pdf->Text(22,$y+6, $data["data_documento"]) ;

        // --------------------------------------
        $pdf->Line(57,$y, 57, $y+7) ;
        $pdf->SetFont('Arial','',7);
        $pdf->Text(59,$y+2.5, html_entity_decode(utf8_decode('N. documento'),ENT_QUOTES, "ISO8859-1")) ;
        $pdf->SetFont('Arial','B',7);
        $pdf->Text(59,$y+6, $data["numero_documento"]) ;

        // --------------------------------------
        $pdf->Line(98,$y, 98, $y+7) ;
        $pdf->SetFont('Arial','',7);
        $pdf->Text(100,$y+2.5, html_entity_decode(utf8_decode('Espécie doc.'),ENT_QUOTES, "ISO8859-1")) ;
        $pdf->SetFont('Arial','B',7);
        $pdf->Text(100,$y+6, 'DMI') ;

        // --------------------------------------
        $pdf->Line(118,$y, 118, $y+7) ;
        $pdf->SetFont('Arial','',7);
        $pdf->Text(120,$y+2.5, html_entity_decode(utf8_decode('Aceite'),ENT_QUOTES, "ISO8859-1")) ;
        $pdf->SetFont('Arial','B',7);
        $pdf->Text(120,$y+6, $data["aceite"]) ;

        // --------------------------------------
        $pdf->Line(128,$y, 128, $y+7) ;
        $pdf->SetFont('Arial','',7);
        $pdf->Text(130,$y+2.5, html_entity_decode(utf8_decode('Data processamento'),ENT_QUOTES, "ISO8859-1")) ;
        $pdf->SetFont('Arial','B',7);
        $pdf->Text(130,$y+6, $data["data_processamento"]) ;

        // --------------------------------------
        $pdf->Line(153,$y, 153, $y+7) ;
        $pdf->SetFont('Arial','',7);
        $pdf->Text(155,$y+2.5, html_entity_decode(utf8_decode('Nosso número'),ENT_QUOTES, "ISO8859-1")) ;
        $pdf->SetFont('Arial','B',7);
        $pdf->SetXY(185,$y+5);
        $tmp = $data["nosso_numero"];
        $pdf->Cell(20,0,$tmp,0,0,'R');

        // Quarta Linha
        $y = $y+7; //31
        $pdf->SetFont('Arial','',7);
        $pdf->Line(20,$y, 204, $y) ;
        $pdf->Text(22,$y+2.5, html_entity_decode(utf8_decode('Uso do Banco'),ENT_QUOTES, "ISO8859-1")) ;
        $pdf->SetFont('Arial','B',7);
        $pdf->Text(22,$y+6, '') ;

        // --------------------------------------
        $pdf->Line(57,$y, 57, $y+7) ;
        $pdf->SetFont('Arial','',7);
        $pdf->Text(59,$y+2.5, html_entity_decode(utf8_decode('Carteira'),ENT_QUOTES, "ISO8859-1")) ;
        $pdf->SetFont('Arial','B',7);
        $pdf->Text(59,$y+6, $data["carteira"]) ;

        // --------------------------------------
        $pdf->Line(80,$y, 80, $y+7) ;
        $pdf->SetFont('Arial','',7);
        $pdf->Text(82,$y+2.5, html_entity_decode(utf8_decode('Moeda'),ENT_QUOTES, "ISO8859-1")) ;
        $pdf->SetFont('Arial','B',7);
        $pdf->Text(82,$y+6, 'REAL') ;

        // --------------------------------------
        $pdf->Line(98,$y, 98, $y+7) ;
        $pdf->SetFont('Arial','',7);
        $pdf->Text(100,$y+2.5, html_entity_decode(utf8_decode('Quantidade'),ENT_QUOTES, "ISO8859-1")) ;
        $pdf->SetFont('Arial','B',7);
        $pdf->Text(100,$y+6, $data["quantidade"]) ;

        // --------------------------------------
        $pdf->Line(128,$y, 128, $y+7) ;
        $pdf->SetFont('Arial','',7);
        $pdf->Text(130,$y+2.5, html_entity_decode(utf8_decode('(x)Valor'),ENT_QUOTES, "ISO8859-1")) ;
        $pdf->SetFont('Arial','B',7);
        $pdf->Text(130,$y+6, $data["valor_unitario"]) ;

        // --------------------------------------
        $pdf->Line(153,$y, 153, $y+7) ;
        $pdf->SetFont('Arial','',7);
        $pdf->Text(155,$y+2.5, html_entity_decode(utf8_decode('(=)Valor do Documento'),ENT_QUOTES, "ISO8859-1")) ;
        $pdf->SetFont('Arial','B',7);
        $pdf->SetXY(185,$y+5);
        $pdf->Cell(20,0,$data["valor_boleto"],0,0,'R');

        // Quinta Linha
        $y = $y+7; //38
        $pdf->SetFont('Arial','',7);
        $pdf->Line(20,$y, 204, $y) ;
        $pdf->Text(22,$y+2.5, html_entity_decode(utf8_decode('Instruções'),ENT_QUOTES, "ISO8859-1")) ;
        $pdf->SetFont('Arial','B',7);
        $pdf->Text(22,$y+6, '') ;
        $pdf->Text(22,$y+10, html_entity_decode(utf8_decode($data["instrucoes1"]),ENT_QUOTES, "ISO8859-1")) ;
        $pdf->Text(22,$y+13, html_entity_decode(utf8_decode($data["instrucoes2"]),ENT_QUOTES, "ISO8859-1")) ;
        $pdf->Text(22,$y+16, html_entity_decode(utf8_decode($data["instrucoes3"]),ENT_QUOTES, "ISO8859-1")) ;
        $pdf->Text(22,$y+19, html_entity_decode(utf8_decode($data["instrucoes4"]),ENT_QUOTES, "ISO8859-1")) ;
        $pdf->Text(22,$y+22, html_entity_decode(utf8_decode($data["instrucoes5"]),ENT_QUOTES, "ISO8859-1")) ;

        // --------------------------------------
        $pdf->Line(153,$y, 153, $y+7) ;
        $pdf->SetFont('Arial','',7);
        $pdf->Text(155,$y+2.5, html_entity_decode(utf8_decode('(-) Desconto'),ENT_QUOTES, "ISO8859-1")) ;
        $pdf->SetFont('Arial','B',7);
        $pdf->SetXY(185,$y+5);
        $pdf->Cell(20,0,"",0,0,'R');

        // Sexta Linha
        $y = $y+7; //45
        $pdf->SetFont('Arial','',7);
        $pdf->Line(153,$y, 204, $y) ;
        $pdf->Line(153,$y, 153, $y+7) ;
        $pdf->SetFont('Arial','',7);
        $pdf->Text(155,$y+2.5, html_entity_decode(utf8_decode('(-) Outras Deduções'),ENT_QUOTES, "ISO8859-1")) ;
        $pdf->SetFont('Arial','B',7);
        $pdf->SetXY(185,$y+5);
        $pdf->Cell(20,0,"",0,0,'R');

        // Setima Linha
        $y = $y+7; //52
        $pdf->SetFont('Arial','',7);
        $pdf->Line(153,$y, 204, $y) ;
        $pdf->Line(153,$y, 153, $y+7) ;
        $pdf->SetFont('Arial','',7);
        $pdf->Text(155,$y+2.5, html_entity_decode(utf8_decode('(+) Mora/Multa'),ENT_QUOTES, "ISO8859-1")) ;
        $pdf->SetFont('Arial','B',7);
        $pdf->SetXY(185,$y+5);
        $pdf->Cell(20,0,"",0,0,'R');

        // Oitava Linha
        $y = $y+7; //59
        $pdf->SetFont('Arial','',7);
        $pdf->Line(153,$y, 204, $y) ;
        $pdf->Line(153,$y, 153, $y+7) ;
        $pdf->SetFont('Arial','',7);
        $pdf->Text(155,$y+2.5, html_entity_decode(utf8_decode('(+) Outros Acréscimos'),ENT_QUOTES, "ISO8859-1")) ;
        $pdf->SetFont('Arial','B',7);
        $pdf->SetXY(185,$y+5);
        $pdf->Cell(20,0,"",0,0,'R');

        // Nona Linha
        $y = $y+7; //66
        $pdf->SetFont('Arial','',7);
        $pdf->Line(153,$y, 204, $y) ;
        $pdf->Line(153,$y, 153, $y+7) ;
        $pdf->SetFont('Arial','',7);
        $pdf->Text(155,$y+2.5, html_entity_decode(utf8_decode('(=) Valor Cobrado'),ENT_QUOTES, "ISO8859-1")) ;
        $pdf->SetFont('Arial','B',7);
        $pdf->SetXY(185,$y+5);
        $pdf->Cell(20,0,"",0,0,'R');

        // Decima Linha
        $y = $y+7; //73
        $pdf->SetFont('Arial','',7);
        $pdf->Line(20,$y, 204, $y) ;
        $pdf->Text(22,$y+2.5, html_entity_decode(utf8_decode('Sacado'),ENT_QUOTES, "ISO8859-1")) ;

        // Decima Primeira Linha
        $pdf->SetFont('Arial','',7);
        $pdf->SetFont('Arial','B',7);
        $pdf->Text(22,$y+5.5, html_entity_decode($data["sacado"],ENT_QUOTES, "ISO8859-1")) ;
        $pdf->Text(22,$y+8, html_entity_decode($data["endereco1"],ENT_QUOTES, "ISO8859-1")) ;
        $pdf->Text(22,$y+11, html_entity_decode($data["endereco2"],ENT_QUOTES, "ISO8859-1")) ;

        // Decima Segunda Linha
        $y=$y+11; // 83
        $pdf->Line(153,$y, 153, $y+4) ;
        $pdf->SetFont('Arial','',7);
        //$pdf->Text(22,$y+3, 'Sacador/Avalista') ;
        $pdf->Text(155,$y+3, html_entity_decode(utf8_decode('Cód baixa'),ENT_QUOTES, "ISO8859-1")) ;
        $pdf->Line(20,$y+4, 204, $y+4) ;

        // Decima Terceira Linha
        $y=$y+6; //89

        $pdf->Text(22,$y+0.5, html_entity_decode(utf8_decode('Sacador/Avalista'),ENT_QUOTES, "ISO8859-1")) ;
        $pdf->SetXY(185,$y);
        $pdf->Cell(20,0,html_entity_decode(utf8_decode("Autenticação mecânica - Ficha de Compensação"),ENT_QUOTES, "ISO8859-1"),0,0,'R');

        // Código de Barras
        $pdf->i25(20,$y+1,$data["codigo_barras"],0.84,12.95);

        // Pontilhado
        $y=$y+15.5; //104.5
        $pdf->SetDrawColor(200);
        $pdf->SetFont('Arial','',6);
        $pdf->SetXY(185,$y);
        $pdf->Cell(20,0,html_entity_decode(utf8_decode("Corte na linha pontilhada"),ENT_QUOTES, "ISO8859-1"),0,0,'R');
        $pdf->DashLine(20,$y+1.5,204,0.2,50);

        $dirModule = $this->_setFolder($this->helpdezkPath . "/app/downloads/fin/");
        $dirType = $this->_setFolder($dirModule . "bankslip/");
        $dirYear = $this->_setFolder($dirType . date("Y") . "/");
        $dirPath = $this->_setFolder($dirYear . $competence . "/");

        $dirUrl = $this->helpdezkUrl . "/app/downloads/fin/bankslip/" . date("Y") ."/".$competence;
        $fileDir = "/app/downloads/fin/bankslip/" . date("Y") ."/".$competence;

        $fileName = $data["numero_documento"].'.pdf';
        $filePath = $dirPath.$fileName;
        $fileUrl = $dirUrl.$fileName;

        $pdf->Output($filePath ,"F");

        if(!file_exists($filePath )){
            $this->logIt('Could not create the file: '.$fileName.' - program: '.$this->program ,3,'general',__LINE__);
            return false;
        }

        $aFileRet = array(
            "filename" => $fileName,
            "fileurl" => $fileUrl,
            "filedir" => $fileDir
        );

        return $aFileRet;
    }

    private function makeAgencyData($data)
    {
        switch ($data['cod_febraban']){
            case "033": //Santander
                $tmp2 = $data["codigo_cliente"];
                $tmp2 = substr($tmp2,0,strlen($tmp2)-1).'-'.substr($tmp2,strlen($tmp2)-1,1);
                break;
            default: //Sicredi
                $tmp2 = $data["agencia_codigo"];
                break;
        }

        return $tmp2;
    }

    public function _setFolder($path)
    {
        if(!is_dir($path)) {
            $this->logIt('Directory: '. $path.' does not exists, I will try to create it. - program: '.$this->program ,6,'general',__LINE__);
            if (!mkdir ($path, 0777 )) {
                $this->logIt('I could not create the directory: '.$path.' - program: '.$this->program ,3,'general',__LINE__);
                return false;
            }

        }

        if (!is_writable($path)) {
            $this->logIt('Directory: '. $path.' Is not writable, I will try to make it writable - program: '.$this->program ,6,'general',__LINE__);
            if (!chmod($path,0777)){
                $this->logIt('Directory: '.$path.'Is not writable !! - program: '.$this->program ,3,'general',__LINE__);
                return false;
            }
        }

        return $path;
    }

    public function _comboBankLegacyID($idcompany=null)
    {
        $where = "";
        if($idcompany) $where = "AND idperson = $idcompany";

        $rs = $this->dbBank->getCompanyBanksExport($where,"ORDER BY name");
        while (!$rs->EOF) {
            $fieldsID[] = $rs->fields['idperseus'];
            $values[]   = $rs->fields['name'];
            $rs->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;

    }

    public function _formatFileField($value, $size, $type=null){
        if(strlen($value) < $size){
            if($type == 'N'){$value = '0'.$value;}
            else {$value = $value.' ';}
            return $this->_formatFileField($value, $size, $type);
        }else{
            return $value;
        }
    }

    public function _displayButtons($smarty,$permissions)
    {
        (isset($permissions[1]) && $permissions[1] == "Y") ? $smarty->assign('display_btn_add', '') : $smarty->assign('display_btn_add', 'hide');
        (isset($permissions[2]) && $permissions[2] == "Y") ? $smarty->assign('display_btn_edit', '') : $smarty->assign('display_btn_edit', 'hide');
        (isset($permissions[2]) && $permissions[2] == "Y") ? $smarty->assign('display_btn_enable', '') : $smarty->assign('display_btn_enable', 'hide');
        (isset($permissions[2]) && $permissions[2] == "Y") ? $smarty->assign('display_btn_disable', '') : $smarty->assign('display_btn_disable', 'hide');
        (isset($permissions[3]) && $permissions[3] == "Y") ? $smarty->assign('display_btn_delete', '') : $smarty->assign('display_btn_delete', 'hide');
        (isset($permissions[4]) && $permissions[4] == "Y") ? $smarty->assign('display_btn_export', '') : $smarty->assign('display_btn_export', 'hide');
        (isset($permissions[5]) && $permissions[5] == "Y") ? $smarty->assign('display_btn_email', '') : $smarty->assign('display_btn_email', 'hide');
        (isset($permissions[6]) && $permissions[6] == "Y") ? $smarty->assign('display_btn_sms', '') : $smarty->assign('display_btn_sms', 'hide');
    }

    /**
     * Method to send e-mails
     *
     * @param array $params E-mail params
     *
     * @return string true|false
     */
    public function _sendEmailDefault($params,$typesender=null,$token=null)
    {
        $dbCommon = new common();
        $emconfigs = $dbCommon->getEmailConfigs();
        $tempconfs = $dbCommon->getTempEmail();

        $mail_title     = '=?UTF-8?B?'.base64_encode($emconfigs['EM_TITLE']).'?=';
        $mail_method    = 'smtp';
        $mail_host      = $emconfigs['EM_HOSTNAME'];
        $mail_domain    = $emconfigs['EM_DOMAIN'];
        $mail_auth      = $emconfigs['EM_AUTH'];
        $mail_username  = $emconfigs['EM_USER'];
        $mail_password  = $emconfigs['EM_PASSWORD'];
        $mail_sender    = $emconfigs['EM_SENDER'];
        $mail_header    = $tempconfs['EM_HEADER'];
        $mail_footer    = $tempconfs['EM_FOOTER'];
        $mail_port      = $emconfigs['EM_PORT'];

        if(!$typesender){
            $typesender = strpos($mail_host,'mandrill') !== false ? 'mandrill' : 'SMTP';
        }

        if(!$token){
            if(strpos($typesender,'mandrill') !== false){
                if(!$this->getConfig('mandrill_token')){
                    $this->logIt("Not found this param: 'mandrill_token', in config file" .' - program: ' . $this->program, 3, 'general', __LINE__);
                    return false;
                }

                $token =  $this->getConfig('mandrill_token');
            }
        }


        $mail = $this->_returnMailer($typesender,$token);

        if($params['customHeader'] && $params['customHeader'] != ''){
            $arrCustomHead = explode(': ',$params['customHeader']);
            $customHead[$arrCustomHead[0]] = $arrCustomHead[1];
        }

        if ($this->getConfig('demo') == true) {
            $customHead['X-hdkLicence'] = 'demo';
        } else {
            $customHead['X-hdkLicence'] = $this->getConfig('license');
        }

        if($params['sender'] && $params['sender'] != ''){
            $mail_sender = $params['sender'];
        }

        if($params['sender_name'] && $params['sender_name'] != ''){
            $mail_title = '=?UTF-8?B?'.base64_encode($params['sender_name']).'?=';
        }

        $server = array(
            "host" => $mail_host,
            "port" => $mail_port,
            "method" => $mail_method,
            "domain" => $mail_domain,
            "auth" => $mail_auth,
            "username" => $mail_username,
            "password" => $mail_password

        );

        $paramsDone = array("msg" => $params['msg'],
            "msg2" => $params['msg2'],
            "mail_host" => $mail_host,
            "mail_domain" => $mail_domain,
            "mail_auth" => $mail_auth,
            "mail_port" => $mail_port,
            "mail_username" => $mail_username,
            "mail_password" => $mail_password,
            "mail_sender" => $mail_sender,
            "type_sender" => $typesender
        );

        if($params['idspool_recipient'] && $params['idspool_recipient'] != ''){
            $paramsDone['idspool_recip'] = $params['idspool_recipient'];
        }

        $arrMessage = array(
            "subject" => $params['subject'],
            "senderName" => $mail_title,
            "sender" => $mail_sender,
            "extra_header" => $customHead,
            "global_merge_vars" => array(),
            "merge_vars" => array(),
            "tags" => array(),
            "analytics_domains" => array(),
            "metadata" => array(),
            "recipient_metadata" => array(),
            "attachments" => $params['attachment'],
            "images" => array(),
            "server" => $server
        );



        if ($typesender == 'mandrill') {
            $aEmail = !is_array($params['address']) ? $this->_makeArraySendTo($params['address']) : $params['address'];
            foreach ($aEmail as $key => $sendEmailTo) {
                $idEmail = ($params['idemail'] && $params['idemail'] != '')
                            ? $params['idemail']
                            : $this->saveTracker($params['idmodule'],$mail_sender,$sendEmailTo['to_address'],addslashes($params['subject']),addslashes($params['contents']));
                if(!$idEmail) {
                    $this->logIt("Error insert in tbtracker, " . $params['msg'] .' - program: ' . $this->program, 3, 'email', __LINE__);
                } else {
                    $paramsDone['idemail'] = $idEmail;
                    $arrMessage['to'] = array(array('email' => $sendEmailTo['to_address'],
                        'name' => $sendEmailTo['to_name'],
                        'type' => 'to'));
                    $arrMessage['body'] = $mail_header . $params['contents'] . $mail_footer;

                    $error_send = $this->_isSendDone($mail,$arrMessage,$paramsDone);
                }
            }
        }else{

            if($params['tracker']) {

                $body = $mail_header . $params['contents'] . $mail_footer;
                $aEmail = !is_array($params['address']) ? $this->_makeArraySendTo($params['address']) : $params['address'];

                foreach ($aEmail as $key => $sendEmailTo) {
                    $idEmail = ($params['idemail'] && $params['idemail'] != '')
                        ? $params['idemail']
                        : $this->saveTracker($params['idmodule'],$mail_sender,$sendEmailTo['to_address'],addslashes($params['subject']),addslashes($params['contents']));
                    if(!$idEmail) {
                        $this->logIt("Error insert in tbtracker, " . $params['msg'] .' - program: ' . $this->program, 3, 'email', __LINE__);
                    } else {
                        $paramsDone['idemail'] = $idEmail;
                        $arrMessage['to'] = $sendEmailTo['to_address'];
                        $trackerID = '<img src="'.$this->helpdezkUrl.'/tracker/'.$params['modulename'].'/'.$idEmail.'.png" height="1" width="1" />' ;
                        $arrMessage['body'] = $mail_header . $params['contents'] . $mail_footer . $trackerID;
                        echo PHP_EOL . 'send ' . $params['tracker'] . PHP_EOL ;
                        $error_send = $this->_isSendDone($mail,$arrMessage,$paramsDone);
                    }
                }
            } else {
                $aEmail = ($params['idemail'] && $params['idemail'] != '')
                    ? $params['idemail']
                    : !is_array($params['address']) ? $this->_makeArraySendTo($params['address']) : $params['address'];

                foreach ($aEmail as $key => $sendEmailTo) {
                    $idEmail = $this->saveTracker($params['idmodule'],$mail_sender,$sendEmailTo['to_address'],addslashes($params['subject']),addslashes($params['contents']));
                    if(!$idEmail) {
                        $this->logIt("Error insert in tbtracker, " . $params['msg'] .' - program: ' . $this->program, 3, 'email', __LINE__);
                    } else {
                        $paramsDone['idemail'] = $idEmail;
                        $arrMessage['to'] = $sendEmailTo['to_address'];
                        $arrMessage['body'] = $mail_header . $params['contents'] . $mail_footer;

                        $error_send = $this->_isSendDone($mail,$arrMessage,$paramsDone);
                    }
                }
            }
        }


        if ($error_send)
            return false;
        else
            return true;

    }

    public function _returnMailer($sender,$token=null)
    {


        $mailerDir = $this->helpdezkPath . '/includes/classes/pipegrep/sendMail.php';


        if (!file_exists($mailerDir)) {
            die ('ERROR: ' .$mailerDir . ' , does not exist  !!!!') ;
        }

        require_once($mailerDir);

        $mail = new sendMail($sender,$token);

        return $mail;
    }

    public function _isSendDone($objmail,$message,$params){
        $done = $objmail->sendEmail($message);

        if ($done['status'] == 'error') {
            if($this->log AND $_SESSION['EM_FAILURE_LOG'] == '1') {
                $this->logIt("Error send email, " . $params['msg'] . ' - program: ' . $this->program, 3, 'email', __LINE__);
                $this->logIt("Error send email, " . $params['msg2'] . ' - Error Info:: ' . $done['result']['message'] . ' - program: ' . $this->program, 3, 'email', __LINE__);
                $this->logIt("Error send email, " . $params['msg'] . ' - Variables: HOST: '.$params['mail_host'].'  DOMAIN: '.$params['mail_domain'].'  AUTH: '.$params['mail_auth'].' PORT: '.$params['mail_port'].' USER: '.$params['mail_username'].' PASS: '.$params['mail_password'].'  SENDER: '.$params['mail_sender'].' - program: ' . $this->program, 7, 'email', __LINE__);
            }
            $error_send = true ;
        } else {
            if($this->log AND $_SESSION['EM_SUCCESS_LOG'] == '1') {
                $toMsg = $params['type_sender'] == 'mandrill' ? "to ". $message['to'][0]['email'] : "to ". $message['to'];
                $senderMsg = " with ".$params['type_sender'];

                $logMsg = ($params['msg'] && $params['msg'] !='') ? $params['msg'] . ' ' .$toMsg . $senderMsg : $toMsg . $senderMsg;

                $this->logIt("Email Succesfully Sent, ".$logMsg  ,6,'email');
            }

            if ($params['type_sender'] == 'mandrill') {
                $this->_saveMandrillID($params['idemail'],$done['result'][0]['_id']);
            }

            $this->_updateEmailSendTime($params['idemail']);



            $error_send = false;
        }

        return $error_send;

    }

    function _saveMandrillID($idemail,$idmandrill)
    {
        $this->loadModel('admin/tracker_model');
        $dbTracker = new tracker_model();

        $ret = $dbTracker->insertMadrillID($idemail,$idmandrill);
        if(!$ret) {
            return false;
        } else {
            return 'ok';
        }

    }

    function _updateEmailSendTime($idemail)
    {
        $this->loadModel('admin/tracker_model');
        $dbTracker = new tracker_model();

        $ret = $dbTracker->updateEmailSendTime($idemail);
        if(!$ret) {
            return false;
        } else {
            return 'ok';
        }

    }

    public function _makeArraySendTo($sentTo)
    {
        $jaExiste = array();
        $aRet = array();
        if (preg_match("/;/", $sentTo)) {
            $email_destino = explode(";", $sentTo);
            if (is_array($email_destino)) {
                for ($i = 0; $i < count($email_destino); $i++) {
                    if (empty($email_destino[$i]))
                        continue;
                    if (!in_array($email_destino[$i], $jaExiste)) {
                        $jaExiste[] = $email_destino[$i];
                        $bus = array(
                            'to_name'=> '',
                            'to_address' => $email_destino[$i]
                        );
                        array_push($aRet,$bus);
                    }
                }
            } else {
                $bus = array(
                    'to_name'=> '',
                    'to_address' => $email_destino
                );
                array_push($aRet,$bus);
            }
        } else {
            $bus = array(
                'to_name'=> '',
                'to_address' => $sentTo
            );
            array_push($aRet,$bus);
        }
        return $aRet;
    }

}