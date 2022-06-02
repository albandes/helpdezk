<?php
/**
 * Created by PhpStorm.
 * User: rogerio
 * Date: 20/01/2018
 * Time: 17:37
 */

require_once(HELPDEZK_PATH . '/app/modules/acd/controllers/acdCommonController.php');

class acdEnrollmentReport extends acdCommon
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

        $this->loadModel('acdstudent_model');
        $dbStudent = new acdstudent_model();
        $this->dbStudent = $dbStudent;

        $this->loadModel('admin/logos_model');
        $dbLogo = new logos_model();
        $this->dbLogo = $dbLogo;

    }

    public function index()
    {

        $smarty = $this->retornaSmarty();

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavAcd($smarty);

        $arrStatus = $this->_comboStatusEnrollment(null,null,"ORDER BY description");
        $smarty->assign('statusids', $arrStatus['ids']);
        $smarty->assign('statusvals', $arrStatus['values']);

        $arrYear = $this->_comboAcdYear(2019,(date("Y")+1));
        $smarty->assign('yearids', $arrYear['ids']);
        $smarty->assign('yearvals', $arrYear['values']);

        $arrCourse = $this->_comboCourse("WHERE idcurso NOT IN (6,7,8)",null,"ORDER BY descricao",null);
        $smarty->assign('courseids', $arrCourse['ids']);
        $smarty->assign('coursevals', $arrCourse['values']);

        $reportslogo = $this->dbLogo->getReportsLogo();
        $smarty->assign('reportslogo', $this->helpdezkUrl . '/app/uploads/logos/' .  $this->getReportsLogoImage());
        $smarty->assign('reportsheight', $reportslogo->fields['height']);
        $smarty->assign('reportswidth', $reportslogo->fields['width']);

        $smarty->assign('token', $this->_makeToken()) ;
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');
        $smarty->display('acd-enrollment-report.tpl');

    }

    public function ajaxSerie()
    {
        echo $this->_comboSerieHtml($_POST['courseId']);
    }

    public function ajaxClass()
    {
        echo $this->_comboClassHtml($_POST['serieId']);
    }

    public function getReport()
    {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $enrollmentStatus = $_POST['cmbEnrollmentStatus'];
        $year = $_POST['cmbYear'];
        $courseID = $_POST['cmbCourse'];
        $serieID = $_POST['cmbSerie'];
        $classID = $_POST['cmbClass'];
        $dtstart = str_replace("'", "", $this->formatSaveDate($_POST['dtstart']));
        $dtfinish = str_replace("'", "", $this->formatSaveDate($_POST['dtfinish']));

        $arrSerie =  explode('|',$serieID);

        $where = "AND a.idstatusenrollment = $enrollmentStatus ";
        $where .= "AND `year` = $year ";
        $where .= $courseID != 'X' ? "AND c.idcurso = $courseID " : '';
        $where .= ($arrSerie[0] != 'X') ? "AND b.idserie = {$arrSerie[0]} " : '';
        $where .= $classID != 'X' ? "AND b.idturma = $classID " : '';
        $where .= "AND DATE_FORMAT(a.dtentry,'%Y-%m-%d') BETWEEN '$dtstart' AND '$dtfinish'";

        $order = "ORDER BY pipeLatinToUtf8(d.descricao),DATE_FORMAT(a.dtentry,'%Y-%m-%d'), c.numero, b.numero, f.`name`";

        $ret = $this->dbStudent->getEnrollmentData($where,null,$order);
        if (!$ret) {
            if($this->log)
                $this->logIt("Get Enrollment Report Data  - User: ".$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $tmpCourseID = 0;
        while(!$ret->EOF){
            if($tmpCourseID != $ret->fields['idcurso']){
                $tmpCourseID = $ret->fields['idcurso'];
                $arrCourse[$tmpCourseID]['coursename'] = $ret->fields['coursename'];
                $arrCourse[$tmpCourseID]['data'] = array();
                $arrCourse[$tmpCourseID]['total'] = 0;
            }

            $bus = array("enrollmentID" => $ret->fields['idperseus'],
                         "studentName" => $ret->fields['name'],
                         "className" => $ret->fields['abrev'],
                         "enrollmentStatus" => $ret->fields['description'],
                         "enrollmentDate" => str_replace("'", "", $this->formatDate($ret->fields['dtentry'])));

            array_push($arrCourse[$tmpCourseID]['data'],$bus);

            $arrCourse[$tmpCourseID]['total']++;

            $ret->MoveNext();
        }

        $retStName = $this->dbStudent->getEnrollmentStatusData("WHERE idstatusenrollment = $enrollmentStatus");
        if (!$retStName) {
            if($this->log)
                $this->logIt("Get Enrollment Status Data  - User: ".$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $aRet = array(
            "year" => $year,
            "enrollmentStatus" => $retStName->fields['description'],
            "period" => str_replace("'", "", $this->formatDate($dtstart)).' '.$this->getLanguageWord('Lbl_to').' '.str_replace("'", "", $this->formatDate($dtfinish)),
            "data" => $arrCourse
        );

        $_SESSION['reportData'] = $aRet;
        echo json_encode($aRet);

    }

    public function exportReport()
    {

        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $arrRelData = $_SESSION['reportData'] ;

        switch ($_POST['typeFile']) {
            case "CSV":
                $csv = array();

                foreach ($arrRelData['data'] as $row=>$val) {
                    foreach ($val as $r=>$v) {
                        if($r == 'coursename'){
                            $courseName = $v;
                        }elseif($r == 'data'){
                            foreach ($v as $sr=>$sv) {
                                array_push($csv, str_replace('&nbsp;', '', $sv['enrollmentID']) . '|' . str_replace('&nbsp;', '',$this->clearAccent($sv['studentName'])) . '|' . str_replace('&nbsp;', '', $row) . '|' . str_replace('&nbsp;', '', $this->clearAccent($courseName)) . '|' . str_replace('&nbsp;', '', $this->clearAccent($sv['className'])). '|' . str_replace('&nbsp;', '', $sv['enrollmentStatus']). '|' . str_replace('&nbsp;', '', $sv['enrollmentDate'])) ;
                            }
                        }
                    }
                }

                $filename = $_SESSION['SES_LOGIN_PERSON'] . "_report_".time().".csv";
                $fileNameWrite = $this->helpdezkPath . '/app/downloads/tmp/'. $filename ;

                if(!is_writable($this->helpdezkPath . '/app/downloads/tmp')) {
                    if( !chmod($this->helpdezkPath . '/app/downloads/tmp', 0777) )
                        $this->logIt("Export Enrollment Report " . ' - Directory ' . $this->helpdezkPath . '/app/downloads/tmp' . ' is not writable ' ,3,'general',__LINE__);

                }

                $fp = fopen($fileNameWrite, 'w');

                if(!$_POST['txtDelimiter'])	$_POST['txtDelimiter'] = ",";
                foreach ($csv as $line) {
                    fputcsv($fp, explode('|', $line), $_POST['txtDelimiter']);
                }
                fclose($fp);

                $fileNameUrl = $this->helpdezkUrl . '/app/downloads/tmp/'. $filename ;
                header('Set-Cookie: fileDownload=true; path=/');
                header('Cache-Control: max-age=60, must-revalidate');
                header("Content-type: text/csv");
                header('Content-Disposition: attachment; filename="'.$fileNameUrl.'"');

                echo $fileNameUrl;
                break;

            case "XLS":
                $filename = $_SESSION['SES_LOGIN_PERSON'] ."_report_".time().".xls";
                $fileNameWrite = $this->helpdezkPath . '/app/downloads/tmp/'. $filename ;
                $fileNameUrl = $this->helpdezkUrl . '/app/downloads/tmp/'. $filename ;

                if(!is_writable($this->helpdezkPath . '/app/downloads/tmp')) {
                    if( !chmod($this->helpdezkPath . '/app/downloads/tmp', 0777) )
                        $this->logIt("Export Enrollment Report " . ' - Directory ' . $this->helpdezkPath . '/app/downloads/tmp' . ' is not writable ' ,3,'general',__LINE__);

                }

                $table = "<table>";
                $table .= "<tr>
                                <td>{$this->getLanguageWord('TMS_Matricula')}</td>
                                <td>{$this->getLanguageWord('TMS_Aluno')}</td>
                                <td>{$this->getLanguageWord('tms_turma')}</td>
                                <td>{$this->getLanguageWord('Grid_status')}</td>
                                <td>{$this->getLanguageWord('Date')}</td>
                            </tr>";
                
                foreach ($arrRelData['data'] as $row=>$val) {
                    foreach ($val as $r=>$v) {
                        if($r == 'coursename'){
                            $table .= "<tr><td colspan='5'>$v</td></tr>";
                        }elseif($r == 'data'){
                            foreach ($v as $sr=>$sv) {
                                $table .= "<tr>
                                                <td>{$sv['enrollmentID']}</td>
                                                <td>{$sv['studentName']}</td>
                                                <td>{$sv['className']}</td>
                                                <td>{$sv['enrollmentStatus']}</td>
                                                <td>{$sv['enrollmentDate']}</td>
                                            </tr>";
                            }
                        }elseif($r == 'total'){
                            $table .= "<tr><td colspan='5'>Total: $v</td></tr>";
                        }
                    }
                }
                $table .= "</tr>";

                header('Set-Cookie: fileDownload=true; path=/');
                header('Cache-Control: max-age=60, must-revalidate');
                header('Content-type: application/x-msexcel');
                header('Content-Length: ' . filesize($fileNameWrite));
                header('Content-Transfer-Encoding: binary');
                header('Content-Disposition: attachment; filename="'.$fileNameUrl.'"');

                echo $table;

                break;

            case "PDF":
                // class FPDF with extension to parsehtml
                // Create a instance of library
                $pdf = $this->_returnfpdfhdk();

                //Font parameters to be used in the report.
                $FontFamily = 'Arial';
                $FontStyle  = '';
                $FontSize   = 10;
                $CelHeight = 4;

                $title =  html_entity_decode(utf8_decode($this->getLanguageWord('pgr_enrollment_report')),ENT_QUOTES, "ISO8859-1"); //Title
                $PdfPage = (utf8_decode($this->getLanguageWord('PDF_Page'))) ; //Page numbering
                $leftMargin = 10;

                $logo = array("file" => $this->helpdezkPath . '/app/uploads/logos/' .  $this->getReportsLogoImage(),
                    "posx" => $leftMargin + 10,
                    "posy" => 8
                );

                $h2 = array(
                    array("txt"=>html_entity_decode(utf8_decode($this->getLanguageWord('acd_academic_year').': '),ENT_QUOTES, "ISO8859-1"),
                        "width"=>30,
                        "height" => $CelHeight,
                        "border" => 0,
                        "ln"=>0,
                        "fill"=>0,
                        "align" => 'R'),
                    array("txt"=>html_entity_decode(utf8_decode($arrRelData['year']),ENT_QUOTES, "ISO8859-1"),
                        "width"=>40,
                        "height" => $CelHeight,
                        "border" => 0,
                        "ln"=>1,
                        "fill"=>0,
                        "align" => 'L'),
                    array("txt"=>html_entity_decode(utf8_decode($this->getLanguageWord('Lbl_period_from').': '),ENT_QUOTES, "ISO8859-1"),
                        "width"=>30,
                        "height" => $CelHeight,
                        "border" => 0,
                        "ln"=>0,
                        "fill"=>0,
                        "align" => 'R'),
                    array("txt"=>html_entity_decode(utf8_decode($arrRelData['period']),ENT_QUOTES, "ISO8859-1"),
                        "width"=>40,
                        "height" => $CelHeight,
                        "border" => 0,
                        "ln"=>1,
                        "fill"=>0,
                        "align" => 'L'),
                    array("txt"=>html_entity_decode(utf8_decode($this->getLanguageWord('Grid_status').': '),ENT_QUOTES, "ISO8859-1"),
                        "width"=>30,
                        "height" => $CelHeight,
                        "border" => 0,
                        "ln"=>0,
                        "fill"=>0,
                        "align" => 'R'),
                    array("txt"=>html_entity_decode(utf8_decode($arrRelData['enrollmentStatus']),ENT_QUOTES, "ISO8859-1"),
                        "width"=>60,
                        "height" => $CelHeight,
                        "border" => 0,
                        "ln"=>1,
                        "fill"=>0,
                        "align" => 'L')
                );

                $headerparams = array(
                    "leftMargin" => $leftMargin,
                    "pdfpage" => $PdfPage,
                    "FontFamily" => $FontFamily,
                    "FontStyle"  => $FontStyle,
                    "FontSyze"  => $FontSize,
                    "logo" => $logo,
                    "title" => $title,
                    "h2" => $h2
                );

                $pdf->AliasNbPages();

                foreach ($arrRelData['data'] as $row=>$val) {
                    $pdf->SetLineWidth(0.2);
                    $pdf->AddPage('P','A4',$headerparams); //Add new page in file
                    $pdf->SetLineWidth(0.5);
                    foreach ($val as $r=>$v) {
                        if($r == 'coursename'){
                            $pdf->Cell($leftMargin);
                            $pdf->SetFont('Arial','B',10);
                            $pdf->Cell(100,5,html_entity_decode(utf8_decode($v),ENT_QUOTES, "ISO8859-1"),0,0,'L');
                            $pdf->Ln(10);

                            $pdf->Cell($leftMargin);
                            $pdf->Cell(20,5,html_entity_decode(utf8_decode($this->getLanguageWord('TMS_Matricula')),ENT_QUOTES, "ISO8859-1"),'B',0,'C');
                            $pdf->Cell(80,5,html_entity_decode(utf8_decode($this->getLanguageWord('TMS_Aluno')),ENT_QUOTES, "ISO8859-1"),'B',0,'C');
                            $pdf->Cell(20,5,html_entity_decode(utf8_decode($this->getLanguageWord('tms_turma')),ENT_QUOTES, "ISO8859-1"),'B',0,'C');
                            $pdf->Cell(35,5,html_entity_decode(utf8_decode($this->getLanguageWord('Grid_status')),ENT_QUOTES, "ISO8859-1"),'B',0,'C');
                            $pdf->Cell(20,5,html_entity_decode(utf8_decode($this->getLanguageWord('Date')),ENT_QUOTES, "ISO8859-1"),'B',1,'C');
                            
                            $pdf->Cell($leftMargin);
                            $pdf->Cell(180,5,'',0,1,'C');
                            $pdf->SetFont('Arial','',9);
                        }elseif($r == 'data'){
                            foreach ($v as $sr=>$sv) {
                                $pdf->Cell($leftMargin);
                                $pdf->Cell(20,5,html_entity_decode(utf8_decode($sv['enrollmentID']),ENT_QUOTES, "ISO8859-1"),0,0,'C');
                                $pdf->Cell(80,5,html_entity_decode(utf8_decode($sv['studentName']),ENT_QUOTES, "ISO8859-1"),0,0,'L');
                                $pdf->Cell(20,5,html_entity_decode(utf8_decode($sv['className']),ENT_QUOTES, "ISO8859-1"),0,0,'C');
                                $pdf->Cell(35,5,html_entity_decode(utf8_decode($sv['enrollmentStatus']),ENT_QUOTES, "ISO8859-1"),0,0,'C');
                                $pdf->Cell(20,5,html_entity_decode(utf8_decode($sv['enrollmentDate']),ENT_QUOTES, "ISO8859-1"),0,1,'C');
                            }
                        }elseif($r == 'total'){
                            $pdf->Cell($leftMargin);
                            $pdf->Cell(175,5,'','B',1,'C');
                            $pdf->Cell($leftMargin);
                            $pdf->SetFont('Arial','B',10);
                            $pdf->Cell(100,5,html_entity_decode(utf8_decode($this->getLanguageWord('Total')),ENT_QUOTES, "ISO8859-1").": ".$v,0,0,'L');
                            $pdf->Ln(10);
                        }
                    }
                }

                $filename = $_SESSION['SES_LOGIN_PERSON'] . "_report_".time().".pdf";
                $fileNameWrite = $this->helpdezkPath . '/app/downloads/tmp/'. $filename ;
                $fileNameUrl = $this->helpdezkUrl . '/app/downloads/tmp/'. $filename ;

                if(!is_writable($this->helpdezkPath . '/app/downloads/tmp')) {
                    if( !chmod($this->helpdezkPath . '/app/downloads/tmp', 0777) )
                        $this->logIt("Export Enrollment Report " . ' - Directory ' . $this->helpdezkPath . '/app/downloads/tmp' . ' is not writable ' ,3,'general',__LINE__);

                }

                $pdf->Output($fileNameWrite,'F');

                echo $fileNameUrl;

                break;
        }
    }


}