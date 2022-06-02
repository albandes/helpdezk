<?php
/**
 * Created by PhpStorm.
 * User: rogerio
 * Date: 20/01/2018
 * Time: 17:37
 */

require_once(HELPDEZK_PATH . '/app/modules/acd/controllers/acdCommonController.php');

class acdIndicadoresNotas extends acdCommon
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

        $this->modulename = 'Academico' ;
        $this->idmodule =  $this->getIdModule($this->modulename);

        // Log settings
        $this->log = parent::$_logStatus;
        $this->program = basename(__FILE__);
        $this->idprogram =  $this->getIdProgramByController('acdIndicadoresNotas');

        $this->loadModel('acdindicadoresnotas_model');
        $dbInd = new acdindicadoresnotas_model();
        $this->dbIndicador = $dbInd;


    }

    public function index()
    {

        $smarty = $this->retornaSmarty();
        $permissions = array_values($this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']));

        $this->makeNavVariables($smarty,'Academico');
        $this->makeFooterVariables($smarty);
        $this->_makeNavAcd($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        /*if($this->_erroServerDominio)
            $status = "Sem conexão com o servidor da Domínio.";
        else {
            $status = "Atualizado em " . $this->_getDataAtualizacao() ;
        }
        $smarty->assign('status_dominio', $status );*/

        /** combo Tipo Relatório */
        $typeData = array('Gerais','Aluno/Area');
        $arrType = $this->_comboReportType($typeData);
        $smarty->assign('reptypeids',  $arrType['ids']);
        $smarty->assign('reptypevals', $arrType['values']);
        $smarty->assign('idreptype', $arrType['ids'][0]);

        /** combo Ano Letivo */
        $arrYear = $this->_comboAcdYear(2013);
        $smarty->assign('acdyearids',  $arrYear['ids']);
        $smarty->assign('acdyearvals', $arrYear['values']);
        $smarty->assign('idacdyear', date("Y") );

        /** combo Curso */
        $arrCourse = $this->_comboCourse();
        $smarty->assign('courseids',  $arrCourse['ids']);
        $smarty->assign('coursevals', $arrCourse['values']);
        $smarty->assign('idcourse', 1);

        /** combo Curso */
        $arrCourse = $this->_comboArea();
        $smarty->assign('areaids',  $arrCourse['ids']);
        $smarty->assign('areavals', $arrCourse['values']);
        $smarty->assign('idarea', 'X');

        /** combo Curso */
        $arrDisc = $this->_comboDisciplina('X');
        $smarty->assign('discids',  $arrDisc['ids']);
        $smarty->assign('discvals', $arrDisc['values']);

        /** combo Série */
        $arrSerie = $this->_comboSerie(1);
        $smarty->assign('serieids',  $arrSerie['ids']);
        $smarty->assign('serievals', $arrSerie['values']);
        $smarty->assign('idserie', 5);

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);

        $this->_displayButtons($smarty,$permissions);
        if($permissions[0] == "Y"){
            $smarty->display('acd-report.tpl');
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
            if ( $_POST['searchField'] == 'rg') $searchField = 'rg';
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
                'dtnasc'        => $rsFuncionario->fields['dtnasc_fmt'],
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
        $cmbTypeReport = $_POST['cmbReportType'];
        $cmbAcdYear = $_POST['cmbAcdYear'];
        $cmbCourse = $_POST['cmbCourse'];
        $cmbArea = $_POST['cmbArea'];
        $cmbDisciplina = $_POST['cmbDisciplina'];
        $cmbSerie = $_POST['cmbSerie'];
        $flagrec = $_POST['flagrec'];

        switch ($cmbTypeReport){
            case 2:
                $this->makeAreaReport($cmbAcdYear,$cmbCourse,$cmbSerie,$flagrec);
                break;
            default:
                $this->makeGeneralReport($cmbAcdYear,$cmbCourse,$cmbArea,$cmbDisciplina,$cmbSerie,$flagrec);
                break;

        }

    }
	
	public function makeBodyReport($pdf,$orientation,$langVars,$params,$numpag,$where,$wnota,$nmod)
    {
        /*
         *  Variables
         */
        $this->SetPdfLogo($this->helpdezkPath . '/app/uploads/logos/' .  $this->getReportsLogoImage() );
        $leftMargin   = .5;
        $this->SetPdfTitle(utf8_decode('RELATÓRIO DE MÉDIAS'));
        $this->SetPdfPage(utf8_decode($langVars['PDF_Page'])) ;
        $this->SetPdfleftMargin($leftMargin);

        $this->pdfFontFamily = 'Arial';
        $this->pdfFontStyle  = '';
        $this->pdfFontSyze   = 8;

        $pdf->SetFont($this->pdfFontFamily,$this->pdfFontStyle,$this->pdfFontSyze);

        $pdf->AliasNbPages();

        $rsTabHead = $this->dbIndicador->getTempDisc("AND ".$where);

        for($i = 1; $i <= $numpag; $i++){
            $th[$i] = array();
        }

        if($orientation == 'P'){$endline = array(292.00125); $startarea = 265.00125;}
        else{$endline = array(208.00125,206.00125); $startarea = 193.00125;}

        $np = 1;
        $c = 1;
        $arrDisc = array();

        while(!$rsTabHead->EOF){
            array_push($th[$np],$rsTabHead->fields['sigla']) ;
            array_push($arrDisc,$rsTabHead->fields['sigla']) ;
            if(($c%$nmod) == 0){$np++;}
            $c++;
            $rsTabHead->MoveNext();
        }

        $rsCurso = $this->dbIndicador->getCurso("WHERE idcurso = ".$params['idcurso']);
        $rsSerie = $this->dbIndicador->getSerie("WHERE idcurso = ".$params['idcurso']." AND numero = ".$params['serie']);

        $arrayArea = array();
        for($k = 1;$k <= $numpag;$k++){
            $pdf->AddPage();
            $pdf->SetAutoPageBreak(true,2);
            $pdf = $this->_ReportPdfHeader($pdf,$orientation);

            $CelHeight = 4;

            $pdf->SetFont($this->pdfFontFamily,'B',$this->pdfFontSyze);
            $pdf = $this->subHeader($pdf,$CelHeight,$rsCurso->fields['descricao'],$rsSerie->fields['descricaoabrev'],$params['anoletivo'],$orientation,$params['flagrec']);
            $pdf = $this->tableHeader($pdf,$th,$CelHeight,$wnota,$k);

            $order = "ORDER BY ALUNome";

            $rsAlunos = $this->dbIndicador->getTempAlunos("AND ".$where,$order);
            $totalAlunos = $rsAlunos->RecordCount();
            $i = 1;
            $tanual = array();

            foreach ($th[$k] as $v){
                $avg[$v] = $this->getAverageReport("AND ".$where." AND DISSigla = '".$v."'",$params);
                $arrStDev[$v]['etapa1'] = array();
                $arrStDev[$v]['etapa2'] = array();
                $arrStDev[$v]['etapa3'] = array();
                $arrStDev[$v]['tanual'] = array();
            }

            while(!$rsAlunos->EOF){

                $pdf->SetFont($this->pdfFontFamily,'',7);
                $pdf->SetTextColor(0,0,0);
                $pdf->Cell(7,$CelHeight,$i,'LRB',0,'R');
                $pdf->Cell(12,$CelHeight,utf8_decode($rsAlunos->fields['ALUMatricula']),'LRB',0,'R');
                $pdf->Cell(60,$CelHeight,ucwords($rsAlunos->fields['ALUNome']),'LRB',0,'L');
                $pdf->Cell(15,$CelHeight,$rsAlunos->fields['TURNome'],'LRB',0,'C');

                foreach ($th[$k] as $v){
                    $condnota = "AND ".$where." AND ALUMatricula = ".$rsAlunos->fields['ALUMatricula']." AND DISSigla = '".$v."' AND TURNome = '".$rsAlunos->fields['TURNome']."'";
                    if($params['flagrec'] == 'S'){
                        $fields = "IF(mediaetapa1 > recetapa1,mediaetapa1,recetapa1) mediaetapa1, 
	                                IF(mediaetapa2 > recetapa2,mediaetapa2,recetapa2) mediaetapa2, 
	                                IF(mediaetapa3 > recetapa3,mediaetapa3,recetapa3) mediaetapa3,";
                    }else{
                        $fields = "mediaetapa1, 
	                               mediaetapa2, 
	                               mediaetapa3,";
                    }

                    $rsnotatmp = $this->dbIndicador->getTempNotas($fields,$condnota);

                    if($rsnotatmp->RecordCount() == 0){
                        $pdf->Cell($wnota,$CelHeight,'-','LRB',0,'C');
                        $pdf->Cell($wnota,$CelHeight,'-','LRB',0,'C');
                        $pdf->Cell($wnota,$CelHeight,'-','LRB',0,'C');
                    }else{
                        while(!$rsnotatmp->EOF){
                            if($rsnotatmp->fields['mediaetapa1'] <  $avg[$v]['total1']){$pdf->SetTextColor(255,102,0);}
                            else{$pdf->SetTextColor(0,0,0);}
                            $pdf->Cell($wnota,$CelHeight,$rsnotatmp->fields['mediaetapa1'],'LRB',0,'C');
                            array_push($arrStDev[$v]['etapa1'],$rsnotatmp->fields['mediaetapa1']);

                            if($rsnotatmp->fields['mediaetapa2'] <  $avg[$v]['total2']){$pdf->SetTextColor(255,102,0);}
                            else{$pdf->SetTextColor(0,0,0);}
                            $pdf->Cell($wnota,$CelHeight,$rsnotatmp->fields['mediaetapa2'],'LRB',0,'C');
                            array_push($arrStDev[$v]['etapa2'],$rsnotatmp->fields['mediaetapa2']);

                            if($rsnotatmp->fields['mediaetapa3'] <  $avg[$v]['total3']){$pdf->SetTextColor(255,102,0);}
                            else{$pdf->SetTextColor(0,0,0);}
                            $pdf->Cell($wnota,$CelHeight,$rsnotatmp->fields['mediaetapa3'],'LRB',0,'C');
                            array_push($arrStDev[$v]['etapa3'],$rsnotatmp->fields['mediaetapa3']);

                            $tanual = $rsnotatmp->fields['mediaetapa1'] + $rsnotatmp->fields['mediaetapa2'] + $rsnotatmp->fields['mediaetapa3'];
                            array_push($arrStDev[$v]['tanual'],$tanual);

                            $pdf->SetTextColor(0,0,0);
                            $rsnotatmp->MoveNext();
                        }
                    }
                }

                $pdf->Ln();
                //echo $k.' - '.$rsAlunos->fields['ALUNome'].' - '.$pdf->GetY().'<br>';
                if(in_array($pdf->GetY(),$endline) && !$rsAlunos->EOF){
                    $pdf = $this->_ReportPdfHeader($pdf,$orientation);
                    $pdf->SetFont($this->pdfFontFamily,'B',$this->pdfFontSyze);
                    $pdf = $this->subHeader($pdf,$CelHeight,$rsCurso->fields['descricao'],$rsSerie->fields['descricaoabrev'],$params['anoletivo'],$orientation,$params['flagrec']);
                    $pdf = $this->tableHeader($pdf,$th,$CelHeight,$wnota,$k);
                }

                $i++;
                $rsAlunos->MoveNext();
            }

            $pdf->Cell(94,$CelHeight,utf8_decode('Médias Trimestres'),'LRTB',0,'C');

            foreach ($th[$k] as $v){
                $total1 = $avg[$v]['total1'];
                $total2 = $avg[$v]['total2'];
                $total3 = $avg[$v]['total3'];

                $pdf->Cell($wnota,$CelHeight,number_format($total1,2,',','.'),'LRTB',0,'C');
                $pdf->Cell($wnota,$CelHeight,number_format($total2,2,',','.'),'LRTB',0,'C');
                $pdf->Cell($wnota,$CelHeight,number_format($total3,2,',','.'),'LRTB',0,'C');
            }

            $pdf->Ln();
            $pdf->Cell(94,$CelHeight,utf8_decode('Desvio Padrão Trimestres'),'LRTB',0,'C');

            foreach ($th[$k] as $v){
                $stdDev1 = $this->standardDeviation($arrStDev[$v]['etapa1']);
                $stdDev2 = $this->standardDeviation($arrStDev[$v]['etapa2']);
                $stdDev3 = $this->standardDeviation($arrStDev[$v]['etapa3']);

                $pdf->Cell($wnota,$CelHeight,number_format($stdDev1,2,',','.'),'LRTB',0,'C');
                $pdf->Cell($wnota,$CelHeight,number_format($stdDev2,2,',','.'),'LRTB',0,'C');
                $pdf->Cell($wnota,$CelHeight,number_format($stdDev3,2,',','.'),'LRTB',0,'C');
            }

            $pdf->Ln();
            $pdf->Cell(94,$CelHeight,utf8_decode('Médias Disciplina Anual'),'LRTB',0,'C');

            foreach ($th[$k] as $v){
                $totalano = $avg[$v]['totalano'];
                $rsDisc = $this->dbIndicador->getDisciplina("WHERE sigla = '$v'");
                $arrayArea[$rsDisc->fields['idareaconhecimento']]['total'] += $totalano;
                $arrayArea[$rsDisc->fields['idareaconhecimento']]['tdisc'] += 1;

                $pdf->Cell(($wnota*3),$CelHeight,number_format($totalano,2,',','.'),'LRTB',0,'C');
            }

            $pdf->Ln();
            $pdf->Cell(94,$CelHeight,utf8_decode('Desvio Padrão Disciplina Anual'),'LRTB',0,'C');

            foreach ($th[$k] as $v){
                $stdDevAnual = $this->standardDeviation($arrStDev[$v]['tanual']);
                $pdf->Cell(($wnota*3),$CelHeight,number_format($stdDevAnual,2,',','.'),'LRTB',0,'C');
            }

        }

        $pdf->Ln(15);

        if($pdf->GetY() >= $startarea){
            $pdf->AddPage();
            $pdf = $this->_ReportPdfHeader($pdf,$orientation);
            $pdf->SetFont($this->pdfFontFamily,'B',$this->pdfFontSyze);
            $pdf = $this->subHeader($pdf,$CelHeight,$rsCurso->fields['descricao'],$rsSerie->fields['descricaoabrev'],$params['anoletivo'],$orientation,$params['flagrec']);
        }

        $y = $pdf->GetY();
        $x = (40 + ($wnota*3)) + 80;
        $pdf->SetFont($this->pdfFontFamily,'B',$this->pdfFontSyze);
        $this->makePdfLineBlur($pdf,utf8_decode('Médias Area Anual'),$CelHeight, (40 + ($wnota*3)));

        $pdf->SetFont($this->pdfFontFamily,$this->pdfFontStyle,$this->pdfFontSyze);
        $arrAvgArea = array();
        foreach ($arrayArea as $key=>$val){
            $totalarea = $val['total'] / $val['tdisc'];
            $rsArea = $this->dbIndicador->getArea("WHERE idareaconhecimento = $key");
            $pdf->Cell(40,$CelHeight,utf8_decode($rsArea->fields['descricaoabrev']),0,0,'L');
            $pdf->Cell(($wnota*3),$CelHeight,number_format($totalarea,2,',','.'),0,1,'R');
            array_push($arrAvgArea,round($totalarea,2));
        }

        $avgTurma = array_sum($arrAvgArea) / count($arrAvgArea);
        $stdDev = $this->standardDeviation($arrAvgArea);
        $pdf->SetY($y);
        $pdf->SetX($x);
        $pdf->SetFont($this->pdfFontFamily,'B',$this->pdfFontSyze);
        $pdf->Cell(40,$CelHeight,utf8_decode('MÉDIA TURMA: ').number_format($avgTurma,2,',','.'),0,0,'L');
        $pdf->Ln();
        //$pdf->SetY($y);
        $pdf->SetX($x);
        $pdf->Cell(40,$CelHeight,utf8_decode('DESVIO PADRÃO: ').number_format($stdDev,2,',','.'),0,0,'L');

        $pdf->AddPage();
        $pdf = $this->_ReportPdfHeader($pdf,$orientation);
        $pdf->SetFont($this->pdfFontFamily,'B',$this->pdfFontSyze);
        $pdf = $this->subHeader($pdf,$CelHeight,$rsCurso->fields['descricao'],$rsSerie->fields['descricaoabrev'],$params['anoletivo'],$orientation,$params['flagrec']);
        $pdf->SetFont($this->pdfFontFamily,'B',$this->pdfFontSyze);
        $this->makePdfLineBlur($pdf,utf8_decode('Professor por Disciplina'),$CelHeight, 120);
        $this->displayProfessorDisc($pdf,$orientation,$langVars,$params,$arrDisc);

	}

    function ajaxDisciplina()
    {
        echo $this->selectHtml('Disciplina',$_POST['areaId']);
    }

    function ajaxSerie()
    {
        echo $this->selectHtml('Serie',$_POST['courseId']);
    }

    public function selectHtml($type,$id=null)
    {
        $methodName = "_combo{$type}";
        $arrType = $this->$methodName($id);
        $select = '';

        foreach ( $arrType['ids'] as $indexKey => $indexValue ) {
            if ($arrType['default'][$indexKey] == 1) {
                $default = 'selected="selected"';
            } else {
                $default = '';
            }
            $select .= "<option value='$indexValue' $default>".$arrType['values'][$indexKey]."</option>";
        }
        return $select;
    }

    public function subHeader($pdf,$CelHeight,$titlecurso,$titleserie,$titleano,$orientation,$flagrec)
    {
        if($orientation == 'P'){$wcurso = 64; $wserie = 64; $wano = 62; $wrec = 198;}
        else{$wcurso = 94.5; $wserie = 89; $wano = 89.5; $wrec = 283;}

        if($flagrec == 'S'){
            $pdf->SetFont($this->pdfFontFamily,'B',10);
            $pdf->Cell($wrec,5,utf8_decode('Considera Média Recuperação'),0,0,'C');
            $pdf->Ln(6);
            $pdf->SetFont($this->pdfFontFamily,'B',$this->pdfFontSyze);
        }

        $pdf->Cell($wcurso,$CelHeight,'Curso: '.utf8_decode($titlecurso),0,0,'L');
        $pdf->Cell($wserie,$CelHeight,utf8_decode('Série: '.$titleserie),0,0,'C');
        $pdf->Cell($wano,$CelHeight,'Ano: '.utf8_decode($titleano),0,0,'R');
        $pdf->Ln(8);

        return $pdf;
    }

    public function tableHeader($pdf,$th,$CelHeight,$wnota,$k)
    {
        $pdf->SetFillColor(150,150,150);
        $pdf->Cell(7,$CelHeight,'','LTB',0,'R',1);
        $pdf->Cell(12,$CelHeight,'','RTB',0,'R',1);
        $pdf->Cell(60,$CelHeight,'DISCIPLINAS',1,0,'C',1);
        $pdf->Cell(15,$CelHeight,'',1,0,'C',1);

        foreach ($th[$k] as $v){
            $pdf->Cell(($wnota*3),$CelHeight,$v,1,0,'C',1);
        }

        $pdf->Ln();

        $pdf->Cell(7,$CelHeight,utf8_decode('Nº'),1,0,'C',1);
        $pdf->Cell(12,$CelHeight,'MAT.',1,0,'C',1);
        $pdf->Cell(60,$CelHeight,'ALUNO',1,0,'C',1);
        $pdf->Cell(15,$CelHeight,'TURMA',1,0,'C',1);

        foreach ($th[$k] as $v){
            $pdf->Cell($wnota,$CelHeight,utf8_decode('1º TRI'),'LRB',0,'C',1);
            $pdf->Cell($wnota,$CelHeight,utf8_decode('2º TRI'),'LRB',0,'C',1);
            $pdf->Cell($wnota,$CelHeight,utf8_decode('3º TRI'),'LRB',0,'C',1);
        }

        $pdf->Ln();

        return $pdf;
    }

    public function makeGeneralReport($year,$idcourse,$idarea,$iddisciplina,$idserie,$flagrec){
        if(!isset($flagrec)){$flagrec = 'N';}
        else{$flagrec = 'S';}

        if($iddisciplina == 'X'){$iddisc = 'ALL';}

        $ret = $this->_makeTempTableNotas($year,$idcourse,$idserie,$iddisc);
        $ret2 = $this->_makeTempProfDisc($year,$idcourse,$idserie);

        $smarty = $this->retornaSmarty();
        $langVars = $smarty->getConfigVars();

        $where = "PERCodigo = '".$year."'";

        switch ($idcourse){
            case 1:
                $where .= " AND CURCodigo IN ('EF8','EF9')";
                break;
            case 2:
                $where .= " AND CURCodigo IN ('EM')";
                break;
            case 3:
                $where .= " AND CURCodigo IN ('EI')";
                break;
            default:
                $where .= "";
                break;
        }

        if($idarea != 'X') $where .= " AND b.idareaconhecimento = ".$idarea;
        if($iddisciplina != 'X') $where .= " AND a.sigla = '".$iddisciplina."'";
        if($idserie != 'X') $where .= " AND SERNumero = ".$idserie;

        $rsTabHead = $this->dbIndicador->getTempDisc("AND ".$where);
        $rows = $rsTabHead->RecordCount();

        if($rows > 3){
            $numpag = $rows / 5;
            $orientation = 'L';
            $wnota = 12;
            $nmod = 5;
        }
        else{
            $numpag = $rows / 3;
            $orientation = 'P';
            $wnota = 10.5;
            $nmod = 3;
        }

        $Temp = explode('.', $numpag);
        if($Temp[1]) $numpag = $Temp[0] + 1;

        $pdf = $this->_returnHtml2pdf($orientation,'mm','A4');

        $params = array(
            'anoletivo'     => $year,
            'idcurso'       => $idcourse,
            'idarea'        => $idarea,
            'iddisciplina'  => $iddisciplina,
            'serie'         => $idserie,
            'flagrec'       => $flagrec
        );
        $this->makeBodyReport($pdf,$orientation,$langVars,$params,$numpag,$where,$wnota,$nmod);

        $filename = $_SESSION['SES_LOGIN_PERSON'] . "_report_".time().".pdf";
        $fileNameWrite = $this->helpdezkPath . '/app/downloads/tmp/'. $filename ;
        $fileNameUrl   = $this->helpdezkUrl . '/app/downloads/tmp/'. $filename ;

        if(!is_writable($this->helpdezkPath . '/app/downloads/tmp/')) {

            if( !chmod($this->helpdezkPath . '/app/downloads/tmp/', 0777) )
                $this->logIt("Print ACD-Indicadores " . ' - Directory ' . $this->helpdezkPath . '/app/downloads/tmp/' . ' is not writable ' ,3,'general',__LINE__);

        }

        $pdf->SetFont('Arial','',8);
        $pdf->Output($fileNameWrite,'F');
        echo $fileNameUrl;
    }

    public function getAverageReport($where,$params)
    {
        if($params['flagrec'] == 'S'){
            $fields = "ROUND(AVG(IF(mediaetapa1 > recetapa1,mediaetapa1,recetapa1)),2) metapa1, 
                       ROUND(AVG(IF(mediaetapa2 > recetapa2,mediaetapa2,recetapa2)),2) metapa2, 
                       ROUND(AVG(IF(mediaetapa3 > recetapa3,mediaetapa3,recetapa3)),2) metapa3,
                       (ROUND(AVG(IF(mediaetapa1 > recetapa1,mediaetapa1,recetapa1)),2) + ROUND(AVG(IF(mediaetapa2 > recetapa2,mediaetapa2,recetapa2)),2) + ROUND(AVG(IF(mediaetapa3 > recetapa3,mediaetapa3,recetapa3)),2)) manual";
        }else{
            $fields = "ROUND(AVG(mediaetapa1),2) metapa1, 
                       ROUND(AVG(mediaetapa2),2) metapa2, 
                       ROUND(AVG(mediaetapa3),2) metapa3,
                       (ROUND(AVG(mediaetapa1),2) + ROUND(AVG(mediaetapa2),2) + ROUND(AVG(mediaetapa3),2)) manual";
        }

        $ret = $this->dbIndicador->getAverage($fields,$where);

        $arr['total1'] = $ret->fields['metapa1'];
        $arr['total2'] = $ret->fields['metapa2'];
        $arr['total3'] = $ret->fields['metapa3'];
        $arr['totalano'] = $ret->fields['manual'];

        return $arr;
    }

    public function makeAreaReport($year,$idcourse,$idserie,$flagrec){
        if(!isset($flagrec)){$flagrec = 'N';}
        else{$flagrec = 'S';}

        $ret = $this->_makeTempTableNotas($year,$idcourse,$idserie,'ALL');

        $smarty = $this->retornaSmarty();
        $langVars = $smarty->getConfigVars();

        $where = "PERCodigo = '".$year."'";

        switch ($idcourse){
            case 1:
                $where .= " AND CURCodigo IN ('EF8','EF9')";
                break;
            case 2:
                $where .= " AND CURCodigo IN ('EM')";
                break;
            case 3:
                $where .= " AND CURCodigo IN ('EI')";
                break;
            default:
                $where .= "";
                break;
        }

        if($idserie != 'X') $where .= " AND SERNumero = ".$_POST['cmbSerie'];
        $orientation = 'P';

        $pdf = $this->_returnHtml2pdf($orientation,'mm','A4');

        $params = array(
            'anoletivo'     => $year,
            'idcurso'       => $idcourse,
            'serie'         => $idserie,
            'flagrec'       => $flagrec
        );

        $wnota = 20;
        $nmod = 3;

        $this->makeBodyAreaReport($pdf,$orientation,$langVars,$params,$where,$wnota,$nmod);

        $filename = $_SESSION['SES_LOGIN_PERSON'] . "_report_".time().".pdf";
        $fileNameWrite = $this->helpdezkPath . '/app/downloads/tmp/'. $filename ;
        $fileNameUrl   = $this->helpdezkUrl . '/app/downloads/tmp/'. $filename ;

        if(!is_writable($this->helpdezkPath . '/app/downloads/tmp/')) {

            if( !chmod($this->helpdezkPath . '/app/downloads/tmp/', 0777) )
                $this->logIt("Print ACD-Indicadores " . ' - Directory ' . $this->helpdezkPath . '/app/downloads/tmp/' . ' is not writable ' ,3,'general',__LINE__);

        }

        $pdf->SetFont('Arial','',8);
        $pdf->Output($fileNameWrite,'F');
        echo $fileNameUrl;

    }

    public function makeBodyAreaReport($pdf,$orientation,$langVars,$params,$where,$wnota,$nmod){
        /*
         *  Variables
         */
        $this->SetPdfLogo($this->helpdezkPath . '/app/uploads/logos/' .  $this->getReportsLogoImage() );
        $leftMargin   = .5;
        $this->SetPdfTitle(utf8_decode('RELATÓRIO DE MÉDIAS POR ÁREA'));
        $this->SetPdfPage(utf8_decode($langVars['PDF_Page'])) ;
        $this->SetPdfleftMargin($leftMargin);

        $this->pdfFontFamily = 'Arial';
        $this->pdfFontStyle  = '';
        $this->pdfFontSyze   = 8;

        $pdf->SetFont($this->pdfFontFamily,$this->pdfFontStyle,$this->pdfFontSyze);

        $pdf->AliasNbPages();

        $th = array();
        $ids = array();
        $rsTabHead = $this->dbIndicador->getTempArea("AND ".$where);

        while(!$rsTabHead->EOF){
            array_push($th,$rsTabHead->fields['descricaoabrev']) ;
            array_push($ids,$rsTabHead->fields['idareaconhecimento']) ;
            $rsTabHead->MoveNext();
        }

        if($orientation == 'P'){$endline = 292.00125; $startarea = 265.00125;}
        else{$endline = 208.00125; $startarea = 193.00125;}

        $rsCurso = $this->dbIndicador->getCurso("WHERE idcurso = ".$params['idcurso']);
        $rsSerie = $this->dbIndicador->getSerie("WHERE idcurso = ".$params['idcurso']." AND numero = ".$params['serie']);

        $pdf->AddPage();
        $pdf->SetAutoPageBreak(true,2);
        $pdf = $this->_ReportPdfHeader($pdf,$orientation);

        $CelHeight = 4;

        $pdf->SetFont($this->pdfFontFamily,'B',$this->pdfFontSyze);
        $pdf = $this->subHeader($pdf,$CelHeight,$rsCurso->fields['descricao'],$rsSerie->fields['descricaoabrev'],$params['anoletivo'],$orientation,$params['flagrec']);
        $pdf = $this->tableHeaderArea($pdf,$th,$CelHeight,$wnota);

        if($params['flagrec'] == 'S'){
            $fields = "(ROUND(AVG(IF(mediaetapa1 > recetapa1,mediaetapa1,recetapa1)),2) + ROUND(AVG(IF(mediaetapa2 > recetapa2,mediaetapa2,recetapa2)),2) + ROUND(AVG(IF(mediaetapa3 > recetapa3,mediaetapa3,recetapa3)),2)) media";
        }else{
            $fields = "(ROUND(AVG(mediaetapa1),2) + ROUND(AVG(mediaetapa2),2) + ROUND(AVG(mediaetapa3),2)) media";
        }

        /* Media Geral por Área e Media Geral por Turma */
        foreach ($ids as $d){
            $ret = $this->dbIndicador->getAvgMediaArea($fields,"AND ".$where." AND b.idareaconhecimento = '".$d."'");
            $avg[$d] = $ret->fields['media'];
        }
        $avgGeral = array_sum($avg) / count($avg);

        $order = "ORDER BY ALUNome";

        $rsAlunos = $this->dbIndicador->getTempAlunos("AND ".$where,$order);
        $totalAlunos = $rsAlunos->RecordCount();
        $qtarea = sizeof($ids);
        $i = 1;

        while(!$rsAlunos->EOF){

            $pdf->SetFont($this->pdfFontFamily,'',7);
            $pdf->SetTextColor(0,0,0);
            $pdf->Cell(7,$CelHeight,$i,'LRB',0,'R');
            $pdf->Cell(12,$CelHeight,utf8_decode($rsAlunos->fields['ALUMatricula']),'LRB',0,'R');
            $pdf->Cell(60,$CelHeight,ucwords($rsAlunos->fields['ALUNome']),'LRB',0,'L');
            $pdf->Cell(15,$CelHeight,$rsAlunos->fields['TURNome'],'LRB',0,'C');

            $sum = 0;

            foreach ($ids as $v){
                $condnota = "AND ".$where." AND ALUMatricula = ".$rsAlunos->fields['ALUMatricula']." AND b.idareaconhecimento = '".$v."' AND TURNome = '".$rsAlunos->fields['TURNome']."'";
                $rsnotatmp = $this->dbIndicador->getTempMediaArea($fields,$condnota);

                while(!$rsnotatmp->EOF){
                    $sum += $rsnotatmp->fields['media'];
                    if($rsnotatmp->fields['media'] <  $avg[$v]){$pdf->SetTextColor(255,102,0);}
                    else{$pdf->SetTextColor(0,0,0);}
                    $pdf->Cell($wnota,$CelHeight,number_format($rsnotatmp->fields['media'],2,',','.'),'LRB',0,'C');

                    $rsnotatmp->MoveNext();
                }
            }

            $total = $sum / $qtarea;
            if($total <  $avgGeral){$pdf->SetTextColor(255,102,0);}
            else{$pdf->SetTextColor(0,0,0);}
            $pdf->Cell(15,$CelHeight,number_format($total,2,',','.'),'LRB',0,'C');
            $pdf->Ln();
            $pdf->SetTextColor(0,0,0);

            if($pdf->GetY() == $endline && !$rsAlunos->EOF){
                $pdf = $this->_ReportPdfHeader($pdf,$orientation);
                $pdf->SetFont($this->pdfFontFamily,'B',$this->pdfFontSyze);
                $pdf = $this->subHeader($pdf,$CelHeight,$rsCurso->fields['descricao'],$rsSerie->fields['descricaoabrev'],$params['anoletivo'],$orientation,$params['flagrec']);
                $pdf = $this->tableHeaderArea($pdf,$th,$CelHeight,$wnota);
            }

            $i++;
            $rsAlunos->MoveNext();
        }

        $pdf->SetFont($this->pdfFontFamily,'B',7);
        $pdf->Cell(94,$CelHeight,utf8_decode('MÉDIA GERAL'),0,0,'R');
        foreach ($ids as $d){
            $pdf->Cell($wnota,$CelHeight,number_format($avg[$d],2,',','.'),'LRB',0,'C');
        }
        $pdf->Cell(15,$CelHeight,number_format($avgGeral,2,',','.'),'LRB',0,'C');
        $pdf->Ln();

    }

    public function tableHeaderArea($pdf,$th,$CelHeight,$wnota)
    {
        $pdf->SetFillColor(150,150,150);
        $pdf->Cell(7,$CelHeight,'','LTB',0,'R',1);
        $pdf->Cell(12,$CelHeight,'','RTB',0,'R',1);
        $pdf->Cell(60,$CelHeight,'AREAS',1,0,'C',1);
        $pdf->Cell(15,$CelHeight,'',1,0,'C',1);

        foreach ($th as $v){
            $pdf->Cell($wnota,$CelHeight,$v,1,0,'C',1);
        }

        $pdf->Cell(15,$CelHeight,'Geral',1,0,'C',1);
        $pdf->Ln();

        $pdf->Cell(7,$CelHeight,utf8_decode('Nº'),1,0,'C',1);
        $pdf->Cell(12,$CelHeight,'MAT.',1,0,'C',1);
        $pdf->Cell(60,$CelHeight,'ALUNO',1,0,'C',1);
        $pdf->Cell(15,$CelHeight,'TURMA',1,0,'C',1);

        foreach ($th as $v){
            $pdf->Cell($wnota,$CelHeight,'','LRB',0,'C',1);
        }

        $pdf->Cell(15,$CelHeight,'',1,0,'C',1);
        $pdf->Ln();

        return $pdf;
    }

    public function standardDeviation($arr)
    {
        $avg = array_sum($arr) / count($arr);
        $arrTmp = array();

        foreach($arr as $k){
            $tmp = pow(round(($k - $avg),2),2);
            array_push($arrTmp,$tmp);
        }

        $stdev = sqrt((array_sum($arrTmp) / count($arrTmp)));
        return $stdev;
    }

    public function displayProfessorDisc($pdf,$orientation,$langVars,$params,$arrdisc)
    {
        $pdf->SetFillColor(150,150,150);
        $pdf->SetTextColor(0,0,0);
        $pdf->SetFont($this->pdfFontFamily,'B',$this->pdfFontSyze);
        $pdf->Cell(80,4,'Professor',0,0,'C',1);
        $pdf->Cell(40,4,utf8_decode('Período'),0,1,'C',1);

        if($params['idcurso'] == 1){$curso = "'EF8','EF9'";}
        elseif($params['idcurso'] == 2){$curso = "'EM'";}
        else{$curso = "'EI'";}

        foreach ($arrdisc as $v){
            $ret = $this->dbIndicador->getProfessorDisciplina($params['anoletivo'],$curso,$params['serie'],$v);
            //echo "<pre>"; print_r($ret); echo "</pre>";
            $rsDisc = $this->dbIndicador->getDisciplina("WHERE sigla = '$v'");

            $pdf->SetFillColor(0,0,0);
            $pdf->SetTextColor(255,255,255);
            $pdf->SetFont($this->pdfFontFamily,'B',$this->pdfFontSyze);
            $pdf->Cell(120,4,utf8_decode($rsDisc->fields['nome']),0,1,'L',1);

            $pdf->SetTextColor(0,0,0);
            $pdf->SetFont($this->pdfFontFamily,'',$this->pdfFontSyze);
            while(!$ret->EOF){
                $pdf->Cell(80,4,$ret->fields['PRONome'],0,0,'L');
                $pdf->Cell(40,4,$ret->fields['dtstart'].' - '.$ret->fields['dtend'],0,0,'L');
                $pdf->Ln();
                $ret->MoveNext();
            }
        }
    }

}