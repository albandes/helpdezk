<?php
/**
 * Created by PhpStorm.
 * User: rogerio
 * Date: 20/01/2018
 * Time: 17:37
 */

require_once(HELPDEZK_PATH . '/app/modules/hur/controllers/hurCommonController.php');

class hurAso extends hurCommon
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

        $this->modulename = 'RecursosHumanos' ;
        $this->idmodule =  $this->getIdModule($this->modulename);
        $this->idprogram =  $this->getIdProgramByController('hurAso');

        // Log settings
        $this->log = parent::$_logStatus;
        $this->program = basename(__FILE__);


    }

    public function index()
    {

        $smarty = $this->retornaSmarty();
        $permissions = array_values($this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']));

        $this->makeNavVariables($smarty,'RecursosHumanos');
        $this->makeFooterVariables($smarty);
        $this->_makeNavHur($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        if($this->_erroServerDominio)
            $status = "Sem conexão com o servidor da Domínio.";
        else {
            $status = "Atualizado em " . $this->_getDataAtualizacao() ;
        }
        $smarty->assign('status_dominio', $status );

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);

        $this->_displayButtons($smarty,$permissions);
        if($permissions[0] == "Y"){
            $smarty->display('hur-aso-grid.tpl');
        }else{
            $smarty->assign('href', $this->helpdezkUrl.'/bmm/home');
            $smarty->display($this->helpdezkPath.'/app/modules/main/views/access_denied.tpl');
        }

    }

    public function jsonGrid()
    {


        $this->validasessao();
        $smarty = $this->retornaSmarty();

        $where = '';

        $idCondicao = $_POST['idcondicao'];
        if ($idCondicao) {
            if ($idCondicao == 'ALL')
                $where = '';
            else
                $where .= " WHERE idcondicao = $idCondicao ";
        }

        // create the query.
        $page  = $_POST['page'];
        $rows  = $_POST['rows'];
        $sidx  = $_POST['sidx'];
        $sord  = $_POST['sord'];

        if(!$sidx)
            $sidx ='nome';
        if(!$sord)
            $sord ='asc';

        if ($_POST['_search'] == 'true'){
            if ( $_POST['searchField'] == 'nome') $searchField = 'nome';
            if ( $_POST['searchField'] == 'setor') $searchField = 'setor';
            if ( $_POST['searchField'] == 'identidade') $searchField = 'identidade';
            if ( $_POST['searchField'] == 'empresa') $searchField = 'empresa';

            if (empty($where))
                $oper = ' WHERE ';
            else
                $oper = ' AND ';
            $where .= $oper . $this->getJqGridOperation($_POST['searchOper'],$searchField ,$_POST['searchString']);

        }

        $count = $this->_getNumFuncionarios($where);
        //echo $count;
        if( $count > 0 && $rows > 0) {
            $total_pages = ceil($count/$rows);
        } else {
            $total_pages = 0;
        }
        if ($page > $total_pages) $page=$total_pages;
        $start = $rows*$page - $rows;
        if($start <0) $start = 0;

        $order = "ORDER BY $sidx $sord";
        $limit = "LIMIT $start , $rows";
        //

        $rsFuncionario = $this->_getFuncionario($where,$order,null,$limit);

        while (!$rsFuncionario->EOF) {
            $aColumns[] = array(
                'id'            => $rsFuncionario->fields['idfuncionario'],
                'nome'          => $rsFuncionario->fields['nome'],
                'cargo'         => $rsFuncionario->fields['cargo'],
                'identidade'    => $rsFuncionario->fields['identidade'],
                'sexo'          => $rsFuncionario->fields['sexo'],
                'dtnasc'        => $rsFuncionario->fields['dtnasc'],
                'setor'         => $rsFuncionario->fields['setor'],
                'empresa'       => $rsFuncionario->fields['empresa'],
                'cpf'           => $rsFuncionario->fields['cpf']

            );
            $rsFuncionario->MoveNext();
        }
        //

        $data = array(
            'page' => $page,
            'total' => $total_pages,
            'records' => $rsFuncionario->RecordCount(),
            'rows' => $aColumns
        );

        echo json_encode($data);

    }

    public function makeReport()
    {
        $smarty = $this->retornaSmarty();
        $langVars = $smarty->getConfigVars();

        $idFuncionario = $_POST['idfuncionario'];
        $where  = 'WHERE idfuncionario = ' . $idFuncionario ;
        $rsFuncionario = $this->_getFuncionario($where,null,null,null);

        $pdf = $this->returnHtml2pdf();
        /*
         *  Variables
         */
        $this->SetPdfLogo($this->helpdezkPath . '/app/uploads/logos/' .  $this->getReportsLogoImage() );
        $leftMargin   = 10;

        $this->SetPdfPage(utf8_decode($langVars['PDF_Page'])) ;
        $this->SetPdfleftMargin($leftMargin);

        $this->pdfFontFamily = 'Arial';
        $this->pdfFontStyle  = '';
        $this->pdfFontSyze   = 8;

        $pdf->SetFont($this->pdfFontFamily,$this->pdfFontStyle,$this->pdfFontSyze);


        $pdf->AliasNbPages();

        $pdf->AddPage();
		$pdf->SetAutoPageBreak(true,.5);

        $CelHeight = 4;

        $pdf->SetFont($this->pdfFontFamily,'B',$this->pdfFontSyze);

		$this->makeBodyAso($pdf,0,$rsFuncionario);	
		$this->makeBodyAso($pdf,150,$rsFuncionario);			

        $filename = $_SESSION['SES_LOGIN_PERSON'] . "_report_".time().".pdf";
        $fileNameWrite = $this->helpdezkPath . '/app/uploads/tmp/'. $filename ;
        $fileNameUrl   = $this->helpdezkUrl . '/app/uploads/tmp/'. $filename ;

        if(!is_writable($this->helpdezkPath . '/app/uploads/tmp/')) {

            if( !chmod($this->helpdezkPath . '/app/uploads/tmp/', 0777) )
                $this->logIt("Print ASO " . ' - Directory ' . $this->helpdezkPath . '/app/uploads/tmp/' . ' is not writable ' ,3,'general',__LINE__);

        }

        $pdf->SetFont('Arial','',8);
        $pdf->Output($fileNameWrite,'F');
        echo $fileNameUrl;

    }
	
	public function makeBodyAso($pdf,$startline,$rsFuncionario)
    {
		//header
		$pdf->SetY($startline + 5);
		$pdf->SetFont($this->pdfFontFamily,'B',12);
		$pdf->Cell(190,5,utf8_decode('ATESTADO DE SAÚDE OCUPACIONAL (ASO)'),0,1,'C');
		
		$pdf->SetFont($this->pdfFontFamily,'B',7);
		$pdf->Cell(190,5,utf8_decode('EM CUMPRIMENTO ÀS PORTARIAS Nº 3214/78, 3164/82, 24/94 E 08/96 DA NR-7 DO MINISTÉRIO DO TRABALHO'),0,1,'C');
		
		$pdf->SetFont($this->pdfFontFamily,'B',12);
		$pdf->SetY($startline + 17);
		$pdf->Cell(90,5,utf8_decode('DR.ª MARIA GORETE L. ZAGO'),0,1,'L');
		$pdf->SetFont($this->pdfFontFamily,'B',9);
		$pdf->Cell(90,5,utf8_decode('MEDICINA DO TRABALHO'),0,1,'L');
		$pdf->Cell(90,5,utf8_decode('CRM 18479 - RQE 30085'),0,1,'L');
		
			
		$pdf->SetFont($this->pdfFontFamily,'B',7);
		$pdf->SetY($startline + 17);
		$pdf->SetX(139);
		$pdf->Cell(32.5,5,utf8_decode('Exames:'),0,0,'L');
		$pdf->Cell(2.5,3,'',1,0,'C');
		$pdf->Cell(30,5,utf8_decode('Demissional'),0,1,'L');
		$pdf->SetX(139);
		$pdf->Cell(2.5,3,'',1,0,'C');
		$pdf->Cell(30,5,utf8_decode('Admissional'),0,0,'L');
		$pdf->Cell(2.5,3,'',1,0,'C');
		$pdf->Cell(30,5,utf8_decode('Periódico'),0,1,'L');
		$pdf->SetX(139);
		$pdf->Cell(2.5,3,'',1,0,'C');
		$pdf->Cell(30,5,utf8_decode('Mudança de função'),0,0,'L');
		$pdf->Cell(2.5,3,'',1,0,'C');
		$pdf->Cell(30,5,utf8_decode('Retorno ao trabalho'),0,1,'L');		
		//header
		
		if($rsFuncionario->fields['sexo'] == 'F'){$sexo_fmt = 'Feminino'; $fmtapt = 'a';}
		else{$sexo_fmt = 'Masculino'; $fmtapt = 'o';}
		
		$pdf->SetFont($this->pdfFontFamily,'',9);
		$pdf->SetY($startline + 34);
		$pdf->Cell(150,5,utf8_decode('Nome: '.$rsFuncionario->fields['nome']),'LT',0,'L');
		$pdf->Cell(40,5,utf8_decode('RG: '.$rsFuncionario->fields['identidade']),'RT',1,'R');
		$pdf->Cell(30,5,utf8_decode('Sexo: '.$sexo_fmt),'L',0,'L');
		$pdf->Cell(30,5,utf8_decode('Nasc.: '.$rsFuncionario->fields['dtnasc_fmt']),0,0,'L');
		$pdf->Cell(70,5,utf8_decode('Função: '.$rsFuncionario->fields['cargo']),0,0,'L');
		$pdf->Cell(60,5,utf8_decode('Setor: '.$rsFuncionario->fields['setor']),'R',1,'R');
		$pdf->Cell(90,5,utf8_decode('Empresa: '.$rsFuncionario->fields['empresa']),'LB',0,'L');
		$pdf->Cell(70,5,utf8_decode('Admissão: '.$rsFuncionario->fields['dtadmissao_fmt']),'B',0,'C');
		$pdf->Cell(30,5,utf8_decode('Grau de Risco: 2'),'RB',1,'R');
		
		$pdf->SetY($startline + 50);
		$pdf->SetFont($this->pdfFontFamily,'B',9);
		$pdf->Cell(190,5,utf8_decode('Procedimentos Médicos Realizados'),0,1,'L');
		
		//$pdf->Ln(20);
		$pdf->SetFont($this->pdfFontFamily,'B',9);
		$pdf->SetY($startline + 70);
		$pdf->Cell(190,5,utf8_decode('Riscos ocupacionais identificados'),0,1,'L');
		$pdf->SetFont($this->pdfFontFamily,'',9);
		$pdf->SetX(12);
		$pdf->Cell(2.5,3,utf8_decode(''),1,0,'L');	
		$pdf->Cell(33,5,utf8_decode('Nenhum risco'),0,0,'L');
		$pdf->Cell(2.5,3,utf8_decode(''),1,0,'L');
		$pdf->Cell(50,5,utf8_decode('Físicos___________________'),0,0,'L');
		$pdf->Cell(2.5,3,utf8_decode(''),1,0,'L');
		$pdf->Cell(50,5,utf8_decode('Biológicos_________________'),0,0,'L');
		$pdf->Cell(2.5,3,utf8_decode(''),1,0,'L');
		$pdf->Cell(50,5,utf8_decode('Químicos________________'),0,1,'L');
		$pdf->SetY($startline + 82);
		$pdf->SetX(12);
		$pdf->Cell(2.5,3,utf8_decode(''),1,0,'L');
		$pdf->Cell(85.5,5,utf8_decode('Ergonômicos___________________________________'),0,0,'L');
		$pdf->Cell(2.5,3,utf8_decode(''),1,0,'L');
		$pdf->Cell(50,5,utf8_decode('Outros________________________________________________'),0,1,'L');
		
		$pdf->SetFont($this->pdfFontFamily,'B',9);
		$pdf->SetY($startline + 89);
		$pdf->Cell(95,5,utf8_decode('Conclusão'),0,1,'L');
		$pdf->SetFont($this->pdfFontFamily,'',9);
		$pdf->SetY($startline + 96);
		$pdf->SetX(12);
		$pdf->Cell(2.5,3,utf8_decode(''),1,0,'L');
		$pdf->Cell(49.5,5,utf8_decode('Apt'.$fmtapt.' físico e mental para função'),0,0,'L');
		$pdf->Cell(2.5,3,utf8_decode(''),1,0,'L');
		$pdf->Cell(27.5,5,utf8_decode('Inapt'.$fmtapt.'____________'),0,1,'L');
		
		$pdf->SetY($startline + 89);
		$pdf->SetX(101);
		$pdf->SetFont($this->pdfFontFamily,'B',9);
		$pdf->Cell(90,5,utf8_decode('Observação:'),0,1,'L');
		
		$pdf->SetY($startline + 103);
		$pdf->SetFont($this->pdfFontFamily,'',7);
		$pdf->Cell(110,5,utf8_decode('Declaro que estou ciente dos resultados acima, e que recebi cópia deste Atestado de Saúde Ocupacional'),0,1,'L');
		$pdf->SetY($startline + 110);
		$pdf->Cell(110,5,utf8_decode('_________ de __________________________________de ____________'),0,1,'R');
		
		$pdf->SetY($startline + 126);
		$pdf->Cell(120,5,utf8_decode('Assinatura do Funcionário'),0,1,'C');
		
		$pdf->SetY($startline + 112);
		$pdf->SetX(131);
		$pdf->Cell(69,5,utf8_decode('Assinatura da Coordenadora'),0,1,'C');
		$pdf->SetY($startline + 115);
		$pdf->SetX(131);
		$pdf->Cell(69,5,utf8_decode('Dra. Maria Gorete Zago / CRM 18479 - RQE 30085'),0,1,'C');
		
		$pdf->SetY($startline + 128);
		$pdf->SetX(131);
		$pdf->Cell(69,5,utf8_decode('Assinatura do(a) Médico(a) Examinador(a)'),0,1,'C');
		
		$pdf->Cell(120,5,utf8_decode('Rua Marechal Deodoro, 800 sala 401 - Ed. Panoramic Center - CEP: 96020-220 - Fones: 53 - 3225 5554 / 3025 2050 / 98114 1000 - Pelotas/RS'),0,1,'L');
		
		//quadros
		$pdf->Rect(138,($startline + 16),62,17);
		
		$pdf->Rect(10,($startline + 50),190,19);
		$pdf->Rect(10,($startline + 70),190,18);
		
		$pdf->Rect(10,($startline + 89),90,13);
		$pdf->Rect(101,($startline + 89),99,13);

		$pdf->Rect(10,($startline + 103),120,30);
		$pdf->Rect(131,($startline + 103),69,30);
		
		//linhas
		$pdf->Line(11,($startline + 60),199,($startline + 60));
		$pdf->Line(11,($startline + 67),199,($startline + 67));
		
		$pdf->Line(102,($startline + 100),199,($startline + 100));
		
		$pdf->Line(40,($startline + 125),100,($startline + 125));
		
		$pdf->Line(132,($startline + 112),199,($startline + 112));
		$pdf->Line(132,($startline + 128),199,($startline + 128));
	}

    public function atualizaFuncionarios()
    {

        $numFuncionarios = $this->_atualizaFuncionarios(0);

        if(!$numFuncionarios) {
            $aRet = array(
                "status" => "ERRO"
            );
            if ($this->log)
                $this->logIt('Atualizacao funcionarios - User: ' . $_SESSION['SES_LOGIN_PERSON'] . ' - program: ' . $this->program . ' - method: ' . __METHOD__, 3, 'general', __LINE__);
        } else {
            $aRet = array(
                "status" => "OK",
                "funcionarios" => $numFuncionarios,
                "dtatual" => $this->_getDataAtualizacao()
            );

        }

        echo json_encode($aRet);

    }

    public function makeDraft()
    {
        $smarty = $this->retornaSmarty();
        $langVars = $smarty->getConfigVars();

        $pdf = $this->returnHtml2pdf();
        /*
         *  Variables
         */
        $this->SetPdfLogo($this->helpdezkPath . '/app/uploads/logos/' .  $this->getReportsLogoImage() );
        $leftMargin   = 10;

        $this->SetPdfPage(utf8_decode($langVars['PDF_Page'])) ;
        $this->SetPdfleftMargin($leftMargin);

        $this->pdfFontFamily = 'Arial';
        $this->pdfFontStyle  = '';
        $this->pdfFontSyze   = 8;

        $pdf->SetFont($this->pdfFontFamily,$this->pdfFontStyle,$this->pdfFontSyze);


        $pdf->AliasNbPages();

        $pdf->AddPage();
        $pdf->SetAutoPageBreak(true,.5);

        $CelHeight = 4;

        $pdf->SetFont($this->pdfFontFamily,'B',$this->pdfFontSyze);

        $this->makeBodyDraft($pdf,0);
        $this->makeBodyDraft($pdf,150);

        $filename = $_SESSION['SES_LOGIN_PERSON'] . "_report_".time().".pdf";
        $fileNameWrite = $this->helpdezkPath . '/app/uploads/tmp/'. $filename ;
        $fileNameUrl   = $this->helpdezkUrl . '/app/uploads/tmp/'. $filename ;

        if(!is_writable($this->helpdezkPath . '/app/uploads/tmp/')) {

            if( !chmod($this->helpdezkPath . '/app/tmp/', 0777) )
                $this->logIt("Print ASO " . ' - Directory ' . $this->helpdezkPath . '/app/tmp/' . ' is not writable ' ,3,'general',__LINE__);

        }

        $pdf->SetFont('Arial','',8);
        $pdf->Output($fileNameWrite,'F');
        echo $fileNameUrl;

    }

    public function makeBodyDraft($pdf,$startline)
    {
        //header
        $pdf->SetY($startline + 5);
        $pdf->SetFont($this->pdfFontFamily,'B',12);
        $pdf->Cell(190,5,utf8_decode('ATESTADO DE SAÚDE OCUPACIONAL (ASO)'),0,1,'C');

        $pdf->SetFont($this->pdfFontFamily,'B',7);
        $pdf->Cell(190,5,utf8_decode('EM CUMPRIMENTO ÀS PORTARIAS Nº 3214/78, 3164/82, 24/94 E 08/96 DA NR-7 DO MINISTÉRIO DO TRABALHO'),0,1,'C');

        $pdf->SetFont($this->pdfFontFamily,'B',12);
        $pdf->SetY($startline + 17);
        $pdf->Cell(90,5,utf8_decode('DR.ª MARIA GORETE L. ZAGO'),0,1,'L');
        $pdf->SetFont($this->pdfFontFamily,'B',9);
        $pdf->Cell(90,5,utf8_decode('MEDICINA DO TRABALHO'),0,1,'L');
        $pdf->Cell(90,5,utf8_decode('CRM 18479 - RQE 30085'),0,1,'L');


        $pdf->SetFont($this->pdfFontFamily,'B',7);
        $pdf->SetY($startline + 17);
        $pdf->SetX(139);
        $pdf->Cell(32.5,5,utf8_decode('Exames:'),0,0,'L');
        $pdf->Cell(2.5,3,'',1,0,'C');
        $pdf->Cell(30,5,utf8_decode('Demissional'),0,1,'L');
        $pdf->SetX(139);
        $pdf->Cell(2.5,3,'',1,0,'C');
        $pdf->Cell(30,5,utf8_decode('Admissional'),0,0,'L');
        $pdf->Cell(2.5,3,'',1,0,'C');
        $pdf->Cell(30,5,utf8_decode('Periódico'),0,1,'L');
        $pdf->SetX(139);
        $pdf->Cell(2.5,3,'',1,0,'C');
        $pdf->Cell(30,5,utf8_decode('Mudança de função'),0,0,'L');
        $pdf->Cell(2.5,3,'',1,0,'C');
        $pdf->Cell(30,5,utf8_decode('Retorno ao trabalho'),0,1,'L');
        //header


        $pdf->SetFont($this->pdfFontFamily,'',9);
        $pdf->SetY($startline + 34);
        $pdf->Cell(150,5,utf8_decode('Nome: '),'LT',0,'L');
        $pdf->Cell(40,5,utf8_decode('RG: '),'RT',1,'L');
        $pdf->Cell(30,5,utf8_decode('Sexo: '),'L',0,'L');
        $pdf->Cell(30,5,utf8_decode('Nasc.: '),0,0,'L');
        $pdf->Cell(70,5,utf8_decode('Função: '),0,0,'L');
        $pdf->Cell(60,5,utf8_decode('Setor: '),'R',1,'L');
        $pdf->Cell(90,5,utf8_decode('Empresa: '),'LB',0,'L');
        $pdf->Cell(70,5,utf8_decode('Admissão: '),'B',0,'C');
        $pdf->Cell(30,5,utf8_decode('Grau de Risco: 2'),'RB',1,'R');

        $pdf->SetY($startline + 50);
        $pdf->SetFont($this->pdfFontFamily,'B',9);
        $pdf->Cell(190,5,utf8_decode('Procedimentos Médicos Realizados'),0,1,'L');

        //$pdf->Ln(20);
        $pdf->SetFont($this->pdfFontFamily,'B',9);
        $pdf->SetY($startline + 70);
        $pdf->Cell(190,5,utf8_decode('Riscos ocupacionais identificados'),0,1,'L');
        $pdf->SetFont($this->pdfFontFamily,'',9);
        $pdf->SetX(12);
        $pdf->Cell(2.5,3,utf8_decode(''),1,0,'L');
        $pdf->Cell(33,5,utf8_decode('Nenhum risco'),0,0,'L');
        $pdf->Cell(2.5,3,utf8_decode(''),1,0,'L');
        $pdf->Cell(50,5,utf8_decode('Físicos___________________'),0,0,'L');
        $pdf->Cell(2.5,3,utf8_decode(''),1,0,'L');
        $pdf->Cell(50,5,utf8_decode('Biológicos_________________'),0,0,'L');
        $pdf->Cell(2.5,3,utf8_decode(''),1,0,'L');
        $pdf->Cell(50,5,utf8_decode('Químicos________________'),0,1,'L');
        $pdf->SetY($startline + 82);
        $pdf->SetX(12);
        $pdf->Cell(2.5,3,utf8_decode(''),1,0,'L');
        $pdf->Cell(85.5,5,utf8_decode('Ergonômicos___________________________________'),0,0,'L');
        $pdf->Cell(2.5,3,utf8_decode(''),1,0,'L');
        $pdf->Cell(50,5,utf8_decode('Outros________________________________________________'),0,1,'L');

        $pdf->SetFont($this->pdfFontFamily,'B',9);
        $pdf->SetY($startline + 89);
        $pdf->Cell(95,5,utf8_decode('Conclusão'),0,1,'L');
        $pdf->SetFont($this->pdfFontFamily,'',9);
        $pdf->SetY($startline + 96);
        $pdf->SetX(12);
        $pdf->Cell(2.5,3,utf8_decode(''),1,0,'L');
        $pdf->Cell(52.5,5,utf8_decode('Apto(a) físico e mental para função'),0,0,'L');
        $pdf->Cell(2.5,3,utf8_decode(''),1,0,'L');
        $pdf->Cell(27.5,5,utf8_decode('Inapto(a)_____ '),0,1,'L');

        $pdf->SetY($startline + 89);
        $pdf->SetX(101);
        $pdf->SetFont($this->pdfFontFamily,'B',9);
        $pdf->Cell(90,5,utf8_decode('Observação:'),0,1,'L');

        $pdf->SetY($startline + 103);
        $pdf->SetFont($this->pdfFontFamily,'',7);
        $pdf->Cell(110,5,utf8_decode('Declaro que estou ciente dos resultados acima, e que recebi cópia deste Atestado de Saúde Ocupacional'),0,1,'L');
        $pdf->SetY($startline + 110);
        $pdf->Cell(110,5,utf8_decode('_________ de __________________________________de ____________'),0,1,'R');

        $pdf->SetY($startline + 126);
        $pdf->Cell(120,5,utf8_decode('Assinatura do Funcionário'),0,1,'C');

        $pdf->SetY($startline + 112);
        $pdf->SetX(131);
        $pdf->Cell(69,5,utf8_decode('Assinatura da Coordenadora'),0,1,'C');
        $pdf->SetY($startline + 115);
        $pdf->SetX(131);
        $pdf->Cell(69,5,utf8_decode('Dra. Maria Gorete Zago / CRM 18479 - RQE 30085'),0,1,'C');

        $pdf->SetY($startline + 128);
        $pdf->SetX(131);
        $pdf->Cell(69,5,utf8_decode('Assinatura do(a) Médico(a) Examinador(a)'),0,1,'C');

        $pdf->Cell(120,5,utf8_decode('Rua Marechal Deodoro, 800 sala 401 - Ed. Panoramic Center - CEP: 96020-220 - Fones: 53 - 3225 5554 / 3025 2050 / 98114 1000 - Pelotas/RS'),0,1,'L');

        //quadros
        $pdf->Rect(138,($startline + 16),62,17);

        $pdf->Rect(10,($startline + 50),190,19);
        $pdf->Rect(10,($startline + 70),190,18);

        $pdf->Rect(10,($startline + 89),90,13);
        $pdf->Rect(101,($startline + 89),99,13);

        $pdf->Rect(10,($startline + 103),120,30);
        $pdf->Rect(131,($startline + 103),69,30);

        //linhas
        $pdf->Line(11,($startline + 60),199,($startline + 60));
        $pdf->Line(11,($startline + 67),199,($startline + 67));

        $pdf->Line(102,($startline + 100),199,($startline + 100));

        $pdf->Line(40,($startline + 125),100,($startline + 125));

        $pdf->Line(132,($startline + 112),199,($startline + 112));
        $pdf->Line(132,($startline + 128),199,($startline + 128));
    }



}