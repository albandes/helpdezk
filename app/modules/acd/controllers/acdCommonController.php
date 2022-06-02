<?php

require_once(HELPDEZK_PATH . '/includes/classes/pipegrep/syslog.php');

/*
 *  Common methods - Academic Module
 */


class acdCommon extends Controllers  {


    public static $_logStatus;

    public function __construct()
    {
        parent::__construct();

        $this->program  = basename( __FILE__ );

        $this->loadModel('acdindicadoresnotas_model');
        $dbInd = new acdindicadoresnotas_model();
        $this->dbIndicador = $dbInd;

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
        if (!$this->_serverApi)
            die('erro');

        $this->loadModel('admin/tracker_model');
        $this->dbTracker = $dbTracker = new tracker_model();

        // Tracker Settings
        if($_SESSION['TRACKER_STATUS'] == 1) {
            $this->tracker = true;
        }  else {
            $this->tracker = false;
        }

        $this->modulename = 'Academico';
        $this->idmodule = $this->getIdModule($this->modulename);

        $this->loadModel('acdstudent_model');
        $dbStudent = new acdstudent_model();
        $this->dbStudent = $dbStudent;

        $this->loadModel('acdclass_model');
        $dbClass = new acdclass_model();
        $this->dbClass = $dbClass;


    }

    public function _makeNavAcd($smarty)
    {
        $idPerson = $_SESSION['SES_COD_USUARIO'];
        $listRecords = $this->makeMenuByModule($idPerson,$this->idmodule);
        $moduleinfo = $this->getModuleInfo($this->idmodule);

        //$smarty->assign('displayMenu_1',1);
        $smarty->assign('listMenu_1',$listRecords);
        $smarty->assign('moduleLogo',$moduleinfo->fields['headerlogo']);
        $smarty->assign('modulePath',$moduleinfo->fields['path']);

    }

    function _getServerApi()
    {

        $sessionVal = $_SESSION['acd']['server_api'] ;
        if (isset($sessionVal) && !empty($sessionVal)) {
            return $sessionVal;
        } else {
            if ($this->log)
                $this->logIt('Url da API da Dominio sem valor - Variavel de sessao: $_SESSION[\'acd\'][\'server_api\']' . $_SESSION['SES_LOGIN_PERSON'] . ' - program: ' . $this->program . ' - method: ' . __METHOD__, 3, 'general', __LINE__);
            return false ;
        }

    }

    public function _comboAcdYear($startyear, $endyear=null)
    {
        $endyear = !$endyear ? date("Y") : $endyear;
        $startyear = ($startyear > $endyear) ? $endyear : $startyear;

        for($i = $startyear; $i <= $endyear; $i++){
            $fieldsID[] = $i;
            $values[]   = $i;
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;

    }

    public function _comboCourse($where=null,$group=null,$order=null,$limit=null)
    {
        $ret = $this->dbIndicador->getCurso($where,$group,$order,$limit);

        while(!$ret->EOF){
            $fieldsID[] = $ret->fields['idcurso'];
            $values[]   = $ret->fields['descricao'];
            $ret->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;

    }

    public function _comboArea()
    {
        $ret = $this->dbIndicador->getArea();

        $fieldsID[] = "X";
        $values[]   = "Todas";

        while(!$ret->EOF){
            $fieldsID[] = $ret->fields['idareaconhecimento'];
            $values[]   = $ret->fields['descricao'];
            $ret->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;

    }

    public function _comboDisciplina($idarea)
    {
        if($idarea != 'X')$where = "WHERE idareaconhecimento = $idarea";

        $ret = $this->dbIndicador->getDisciplina($where);

        $fieldsID[] = "X";
        $values[]   = "Todas";

        while(!$ret->EOF){
            $fieldsID[] = $ret->fields['sigla'];
            $values[]   = $ret->fields['nomeabrev'];
            $ret->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;

    }

    public function _comboSerie($idcourse,$type=null)
    {
        if($idcourse != 'X') $where = "WHERE idcurso = $idcourse";

        if($idcourse == 1){
            $where .= ($type && $type == 'ind') ? " AND numero IN (5,6,7,8,9)" : '';
        }

        $ret = $this->dbIndicador->getSerie($where);

        /*$fieldsID[] = "X";
        $values[]   = "Todas";*/

        while(!$ret->EOF){
            $fieldsID[] = ($type && $type == 'ind') ? $ret->fields['numero'] : $ret->fields['idserie'];
            $values[]   = $ret->fields['descricaoabrev'];
            $ret->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;

    }

    public function _makeTempTableNotas($year,$idcourse,$serie,$disc)
    {
        $ctx = stream_context_create(array(
                'http' => array(
                    'timeout' => 30
                )
            )
        );

        $response = file_get_contents($this->_serverApi.'/api/src/public/notas/'.$year.'/'.$idcourse.'/'.$serie.'/'.$disc,false,$ctx);
        //echo $this->_serverApi.'/api/src/public/notas/'.$year.'/'.$idcourse.'/'.$serie.'/'.$disc;
        if(!$response) {
            if ($this->log)
                $this->logIt('Sem conexao com o servidor da Perseus - User: ' . $_SESSION['SES_LOGIN_PERSON'] . ' - program: ' . $this->program . ' - method: ' . __METHOD__, 3, 'general', __LINE__);
            return false;
        }

        $response = json_decode($response, true);

        if (!$response['status']){
            if ($this->log)
                $this->logIt('Nao retornou dados do servidor da Perseus - User: ' . $_SESSION['SES_LOGIN_PERSON'] . ' - program: ' . $this->program . ' - method: ' . __METHOD__, 3, 'general', __LINE__);
            return false;
        }

        $retdrop = $this->dbIndicador->dropTempTableNotas();

        if (!$retdrop) {
            if ($this->log)
                $this->logIt('Delete Temporary Table - User: ' . $_SESSION['SES_LOGIN_PERSON'] . ' - program: ' . $this->program . ' - method: ' . __METHOD__, 3, 'general', __LINE__);
            return false;
        }

        $retcreate = $this->dbIndicador->createTempTableNotas();

        if (!$retcreate) {
            if ($this->log)
                $this->logIt('New Temporary Table - User: ' . $_SESSION['SES_LOGIN_PERSON'] . ' - program: ' . $this->program . ' - method: ' . __METHOD__, 3, 'general', __LINE__);
            return false;
        }

        $a = $response['result'];

        foreach ($a as $item){

            $retins = $this->dbIndicador->insertTempTableNotas($item['matricula'],addslashes(utf8_decode($item['nome'])),$item['periodo'],$item['idcurso'],$item['serie'],$item['turma'],$item['discnome'],$item['discsigla'],$item['mediaetapa1'],$item['mediaetapa2'],$item['mediaetapa3'],$item['recetapa1'],$item['recetapa2'],$item['recetapa3'],$item['mediaanual']);

            if (!$retins) {
                if ($this->log)
                    $this->logIt('Insert data Temporary Table - User: ' . $_SESSION['SES_LOGIN_PERSON'] . ' - program: ' . $this->program . ' - method: ' . __METHOD__, 3, 'general', __LINE__);
                return false;
            }
        }

        return true;

    }

    // class FPDF with extention to parsehtml
    public function _returnHtml2pdf($orientation,$unit,$size) {
        require_once(FPDF . 'html2pdf.php');
        $pdf = new html2Pdf($orientation,$unit,$size);
        return $pdf;

    }

    public function _ReportPdfHeader($pdf,$orientation){

        if(file_exists($this->pdfLogo)) {
            $pdf->Image($this->pdfLogo, 10 + $this->pdfLeftMargin, 8);
        }

        if($orientation == 'P'){$finalX = 198;}
        else{$finalX = 283;}

        $pdf->Ln(2);

        $pdf->SetFont($this->pdfFontFamily,'B',10);
        $pdf->Cell($this->pdfLeftMargin);
        $pdf->Cell(0, 5, $this->pdfTitle, 0, 0, 'C');

        $pdf->SetFont($this->pdfFontFamily,'I',6);
        $pdf->Cell(0, 5, $this->pdfPage . ' ' . $pdf->PageNo() . '/{nb}', 0, 0, 'R');
        $pdf->Ln(7);
        $pdf->Cell($this->pdfLeftMargin);
        $pdf->Line($pdf->GetX(), $pdf->GetY(), $finalX, $pdf->GetY());

        $pdf->SetFont($this->pdfFontFamily,$this->pdfFontStyle,$this->pdfFontSyze);
        $pdf->Cell($this->pdfLeftMargin);

        $pdf->Ln(5);
        return $pdf ;
    }

    /* Convert hexdec color string to rgb(a) string */
    public function hex2rgba($color, $opacity = false) {

        $default = 'rgb(0,0,0)';

        //Return default if no color provided
        if(empty($color))
            return $default;

        //Sanitize $color if "#" is provided
        if ($color[0] == '#' ) {
            $color = substr( $color, 1 );
        }

        //Check if color has 6 or 3 characters and get values
        if (strlen($color) == 6) {
            $hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
        } elseif ( strlen( $color ) == 3 ) {
            $hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
        } else {
            return $default;
        }

        //Convert hexadec to rgb
        $rgb =  array_map('hexdec', $hex);

        //Check if opacity is set(rgba or rgb)
        if($opacity){
            $output = 'rgba('.implode(",",$rgb).','.$opacity.')';
        } else {
            $output = 'rgb('.implode(",",$rgb).')';
        }

        //Return rgb(a) color string
        return $output;
    }

    public function _comboReportType($arr)
    {
        $i = 1;

        foreach($arr as $op){
            $fieldsID[] = $i;
            $values[]   = $op;
            $i++;
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;

    }

    public function _makeTempProfDisc($year,$course,$serie)
    {
        $ctx = stream_context_create(array(
                'http' => array(
                    'timeout' => 30
                )
            )
        );

        $response = file_get_contents($this->_serverApi.'/api/src/public/professordisciplina/'.$year.'/'.$course.'/'.$serie,false,$ctx);
        //echo $this->_serverApi.'/api/src/public/professordisciplina/'.$year.'/'.$course.'/'.$serie;
        if(!$response) {
            if ($this->log)
                $this->logIt('Sem conexao com o servidor da Perseus - User: ' . $_SESSION['SES_LOGIN_PERSON'] . ' - program: ' . $this->program . ' - method: ' . __METHOD__, 3, 'general', __LINE__);
            return false;
        }

        $response = json_decode($response, true);

        if (!$response['status']){
            if ($this->log)
                $this->logIt('Nao retornou dados do servidor da Perseus - User: ' . $_SESSION['SES_LOGIN_PERSON'] . ' - program: ' . $this->program . ' - method: ' . __METHOD__, 3, 'general', __LINE__);
            return false;
        }

        $retdrop = $this->dbIndicador->dropTempProfDisc();

        if (!$retdrop) {
            if ($this->log)
                $this->logIt('Delete Temporary Table - User: ' . $_SESSION['SES_LOGIN_PERSON'] . ' - program: ' . $this->program . ' - method: ' . __METHOD__, 3, 'general', __LINE__);
            return false;
        }

        $retcreate = $this->dbIndicador->createTempProfDisc();

        if (!$retcreate) {
            if ($this->log)
                $this->logIt('New Temporary Table - User: ' . $_SESSION['SES_LOGIN_PERSON'] . ' - program: ' . $this->program . ' - method: ' . __METHOD__, 3, 'general', __LINE__);
            return false;
        }

        $a = $response['result'];

        foreach ($a as $item){
            $dtstart = str_replace('/','-',$item['dtstart']);
            $dtend = str_replace('/','-',$item['dtend']);

            $retins = $this->dbIndicador->insertTempProfDisc($item['idprofessor'],addslashes(utf8_decode($item['professor'])),$item['periodo'],$item['idcurso'],$item['serie'],$item['iddisciplina'],$item['discsigla'],$dtstart,$dtend);

            if (!$retins) {
                if ($this->log)
                    $this->logIt('Insert data Temporary Table - User: ' . $_SESSION['SES_LOGIN_PERSON'] . ' - program: ' . $this->program . ' - method: ' . __METHOD__, 3, 'general', __LINE__);
                return false;
            }
        }

        return true;

    }

    public function _comboStatusEnrollment($where=null,$group=null, $order=null,$limit=null)
    {
        $ret = $this->dbStudent->getEnrollmentStatus($where,$group,$order,$limit);
        if(!ret){
            if($this->log)
                $this->logIt("Can't get enrollment status - program: ".$this->program ,3,'general',__LINE__);
        }

        while(!$ret->EOF){
            $fieldsID[] = $ret->fields['idstatusenrollment'];
            $values[] = $ret->fields['description'];
            $ret->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;

    }

    public function _comboSerieHtml($courseID)
    {

        $select = '';
        $select .= $courseID != '' ? "<option value='X|$courseID' selected='selected'>".$this->getLanguageWord('all')."</option>": "";

        if($courseID != 'X' && $courseID != ''){
            $arrType = $this->_comboSerie($courseID);
            foreach ( $arrType['ids'] as $indexKey => $indexValue ) {
                $select .= "<option value='$indexValue'>".$arrType['values'][$indexKey]."</option>";
            }
        }

        return $select;
    }

    public function _comboClassHtml($serieID)
    {

        $select = '';
        $select .= $serieID != '' ? "<option value='X' selected='selected'>".$this->getLanguageWord('all')."</option>": "";

        if ($serieID != 'X|X' && $serieID != '') {
            $arrType = $this->_comboClass($serieID);
            foreach ($arrType['ids'] as $indexKey => $indexValue) {
                $select .= "<option value='$indexValue'>" . $arrType['values'][$indexKey] . "</option>";
            }
        }

        return $select;
    }

    public function _comboClass($serieID)
    {
        $arrSerie = explode('|',$serieID);
        $where = ($arrSerie[1] && $arrSerie[0] == 'X') ? "AND b.idcurso = $arrSerie[1] " : "AND a.idserie = $arrSerie[0] ";

        $ret = $this->dbClass->getClass($where,null,"ORDER BY b.numero, a.numero");

        /*$fieldsID[] = "X";
        $values[]   = "Todas";*/

        while(!$ret->EOF){
            $fieldsID[] = $ret->fields['idturma'];
            $values[]   = $ret->fields['nome'];
            $ret->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;

    }

    // class FPDF with extention to parsehtml
    public function _returnfpdfhdk() {
        require_once(FPDF . 'fpdfhdk.php');
        $pdf = new fpdfhdk();
        return $pdf;
    }

    public function _comboStudentClass($flgClass=false,$where=null,$order=null,$limit=null,$group=null)
    {

        $ret = $this->dbStudent->getStudent($where,$order,$limit,$group);
        if(!$ret['success']){
            if($this->log)
                $this->logIt("Can't get student data. - {$ret['message']} - User: {$_SESSION['SES_LOGIN_PERSON']} - program: {$this->program} - Method: ". __METHOD__ ,3,'general',__LINE__);
        }

        foreach($ret['data'] as $k=>$v){
            $fieldsID[] = $v['idstudent'];
            $values[]   = (!$flgClass) ? $v['name'] : "{$v['name']} - {$v['class_name']}";
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;

    }

}