<?php

require_once(HELPDEZK_PATH . '/app/modules/admin/controllers/admCommonController.php');

class personReport extends admCommon
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

        $this->modulename = 'admin' ;
        $this->idmodule =  $this->getIdModule($this->modulename);

        // Log settings
        $this->log = parent::$_logStatus;
        $this->program = basename(__FILE__);
        $this->idprogram =  $this->getIdProgramByController('personReport');

        $this->loadModel('person_model');
        $dbPerson = new person_model();
        $this->dbPerson = $dbPerson;

        $this->loadModel('logos_model');
        $dbLogo = new logos_model();
        $this->dbLogo = $dbLogo;

    }

    public function index()
    {

        $smarty = $this->retornaSmarty();
        // Check the access permission
        $permissions = array_values($this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']));
        if($permissions[0] != "Y")
            $this->accessDenied();

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->makeNavAdmin($smarty);

        $arrTypePerson = $this->_comboTypePerson(null,null,"ORDER BY `name`");
        $smarty->assign('typepersonids', $arrTypePerson['ids']);
        $smarty->assign('typepersonvals', $arrTypePerson['values']);

        $reportslogo = $this->dbLogo->getReportsLogo();
        $smarty->assign('reportslogo', $reportslogo->fields['file_name']);
        $smarty->assign('reportsheight', $reportslogo->fields['height']);
        $smarty->assign('reportswidth', $reportslogo->fields['width']);

        $smarty->assign('token', $this->_makeToken()) ;
        $smarty->assign('lang_default', $this->getConfig('lang'));
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');
        $smarty->display('person-report.tpl');

    }

    public function getReport()
    {
        $this->protectFormInput();

        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $typePersonID = $_POST['cmbTypePerson'];

        $where = $typePersonID != 'ALL' ? "WHERE a.idtypeperson  = $typePersonID " : '';

        $order = "ORDER BY typeperson,`name` ASC";

        $ret = $this->dbPerson->getPersonReportData($where,null,$order);
        if (!$ret) {
            if($this->log)
                $this->logIt("Get Person Report Data  - User: ".$_SESSION['SES_NAME_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $arrPerson = array();
        while(!$ret->EOF){
            $lblTypePerson = !$this->getLanguageWord('type_user_'.$ret->fields['typeperson']) ? $ret->fields['typeperson'] : $this->getLanguageWord('type_user_'.$ret->fields['typeperson']);
            $bus = array("login" => $ret->fields['login'] ?? '',
                "name" => $ret->fields['name'] ?? '',
                "typeperson" => $ret->fields['typeperson'] ? $lblTypePerson : '',
                "company" => $ret->fields['company'] ?? '');

            array_push($arrPerson,$bus);

            $ret->MoveNext();
        }

        $aRet = array(
            "data" => $arrPerson
        );

        $_SESSION['reportData'] = $aRet;
        echo json_encode($aRet);

    }

    public function exportReport()
    {
        $this->protectFormInput();

        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $arrRelData = $_SESSION['reportData'] ;

        switch ($_POST['typeFile']) {
            case "CSV":
                $fileNameUrl = $this->makeCsvFile($arrRelData['data']);

                header('Set-Cookie: fileDownload=true; path=/');
                header('Cache-Control: max-age=60, must-revalidate');
                header("Content-type: text/csv");
                header('Content-Disposition: attachment; filename="'.$fileNameUrl.'"');

                echo $fileNameUrl;
                break;

            case "XLS":


                header('Set-Cookie: fileDownload=true; path=/');
                header('Cache-Control: max-age=60, must-revalidate');
                header('Content-type: application/x-msexcel');
                header('Content-Length: ' . filesize($fileNameWrite));
                header('Content-Transfer-Encoding: binary');
                header('Content-Disposition: attachment; filename="'.$fileNameUrl.'"');

                echo $table;

                break;

            case "PDF":

                echo $this->makePdfFile($arrRelData['data']);
                break;
        }
    }

    public function makeCsvFile($data)
    {
        $csv = array();

        foreach ($data as $row=>$val) {
            foreach ($val as $r=>$v) {
                array_push($csv, str_replace('&nbsp;', '', $val['login']) . '|' . str_replace('&nbsp;', '',$this->clearAccent($val['name'])) . '|' . str_replace('&nbsp;', '', $this->clearAccent($val['typeperson'])) . '|' . str_replace('&nbsp;', '', $this->clearAccent($val['company']))) ;
            }
        }

        $filename = $_SESSION['SES_NAME_PERSON'] . "_report_".time().".csv";
        $fileNameWrite = $this->helpdezkPath . '/app/downloads/tmp/'. $filename ;

        if(!is_writable($this->helpdezkPath . '/app/downloads/tmp')) {
            if( !chmod($this->helpdezkPath . '/app/downloads/tmp', 0777) )
                $this->logIt("Export Enrollment Report " . ' - Directory ' . $this->helpdezkPath . '/app/tmp' . ' is not writable ' ,3,'general',__LINE__);

        }

        $fp = fopen($fileNameWrite, 'w');

        if(!$_POST['txtDelimiter'])	$_POST['txtDelimiter'] = ",";
        foreach ($csv as $line) {
            fputcsv($fp, explode('|', $line), $_POST['txtDelimiter']);
        }
        fclose($fp);

        $fileNameUrl = $this->helpdezkUrl . '/app/downloads/tmp/'. $filename ;

        return $fileNameUrl;

    }

    public function makeXlsFile($data)
    {
        $filename = $_SESSION['SES_LOGIN_PERSON'] ."_report_".time().".xls";
        $fileNameWrite = $this->helpdezkPath . '/app/downloads/tmp/'. $filename ;
        $fileNameUrl = $this->helpdezkUrl . '/app/downloads/tmp/'. $filename ;

        if(!is_writable($this->helpdezkPath . '/app/downloads/tmp')) {
            if( !chmod($this->helpdezkPath . '/app/downloads/tmp', 0777) )
                $this->logIt("Export Enrollment Report " . ' - Directory ' . $this->helpdezkPath . '/app/downloads/tmp' . ' is not writable ' ,3,'general',__LINE__);

        }

        $table = "<table>";
        $table .= "<tr>
                                <td>{$this->getLanguageWord('Login')}</td>
                                <td>{$this->getLanguageWord('Name')}</td>
                                <td>{$this->getLanguageWord('type')}</td>
                                <td>{$this->getLanguageWord('Comapny')}</td>
                            </tr>";

        foreach ($data as $row=>$val) {
            foreach ($val as $r=>$v) {
                $table .= "<tr>
                                                <td>{$val['login']}</td>
                                                <td>{$val['name']}</td>
                                                <td>{$val['typeperson']}</td>
                                                <td>{$val['company']}</td>
                                            </tr>";
            }
        }
        $table .= "</tr>";


        if(!file_exists($fileNameWrite)){return false;}

        return $fileNameUrl;

    }

    public function makePdfFile($data)
    {
        // class FPDF with extension to parsehtml
        // Create a instance of library
        $pdf = $this->_returnfpdfhdk();

        //Font parameters to be used in the report.
        $FontFamily = 'Arial';
        $FontStyle  = '';
        $FontSize   = 8;
        $CelHeight = 4;

        $title =  html_entity_decode(utf8_decode($this->getLanguageWord('PDF_person_report')),ENT_QUOTES, "ISO8859-1"); //Title
        $PdfPage = (utf8_decode($this->getLanguageWord('PDF_Page'))) ; //Page numbering
        $leftMargin = 1;

        $logo = array("file" => $this->helpdezkPath . '/app/uploads/logos/' .  $this->getReportsLogoImage(),
            "posx" => $leftMargin + 10,
            "posy" => 8
        );

        $h2 = array(
            array("txt"=>html_entity_decode(utf8_decode(date("d/m/Y")),ENT_QUOTES, "ISO8859-1"),
                "width"=>180,
                "height" => $CelHeight,
                "border" => 0,
                "ln"=>1,
                "fill"=>0,
                "align" => 'C')
        );

        //table header
        $th = array(
            array("txt"=>html_entity_decode(utf8_decode($this->getLanguageWord('Login')),ENT_QUOTES, "ISO8859-1"),
                "width"=>30,
                "height" => $CelHeight,
                "border" => 1,
                "ln"=>0,
                "fill"=>1,
                "align" => 'C'),
            array("txt"=>html_entity_decode(utf8_decode($this->getLanguageWord('Name')),ENT_QUOTES, "ISO8859-1"),
                "width"=>65,
                "height" => $CelHeight,
                "border" => 1,
                "ln"=>0,
                "fill"=>1,
                "align" => 'C'),
            array("txt"=>html_entity_decode(utf8_decode($this->getLanguageWord('type')),ENT_QUOTES, "ISO8859-1"),
                "width"=>30,
                "height" => $CelHeight,
                "border" => 1,
                "ln"=>0,
                "fill"=>1,
                "align" => 'C'),
            array("txt"=>html_entity_decode(utf8_decode($this->getLanguageWord('Company')),ENT_QUOTES, "ISO8859-1"),
                "width"=>64,
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
        $pdf->AddPage('P','A4',$headerparams); //Add new page in file

        foreach ($data as $row=>$val) {

            $pdf->Cell($leftMargin);
            $pdf->Cell(30,5,html_entity_decode(utf8_decode($val['login']),ENT_QUOTES, "ISO8859-1"),1,0,'C');
            $pdf->Cell(65,5,html_entity_decode(utf8_decode($val['name']),ENT_QUOTES, "ISO8859-1"),1,0,'L');
            $pdf->Cell(30,5,html_entity_decode(utf8_decode($val['typeperson']),ENT_QUOTES, "ISO8859-1"),1,0,'C');
            $pdf->Cell(64,5,html_entity_decode(utf8_decode($val['company']),ENT_QUOTES, "ISO8859-1"),1,1,'C');
        }

        $filename = $_SESSION['SES_NAME_PERSON'] . "_report_".time().".pdf";
        $fileNameWrite = $this->helpdezkPath . '/app/downloads/tmp/'. $filename ;
        $fileNameUrl = $this->helpdezkUrl . '/app/downloads/tmp/'. $filename ;

        if(!is_writable($this->helpdezkPath . '/app/downloads/tmp')) {
            if( !chmod($this->helpdezkPath . '/app/downloads/tmp', 0777) )
                $this->logIt("Export Person Report " . ' - Directory ' . $this->helpdezkPath . '/app/downloads/tmp' . ' is not writable ' ,3,'general',__LINE__);

        }

        $pdf->Output($fileNameWrite,'F');

        return $fileNameUrl;

    }




}