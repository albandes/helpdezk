<?php

require_once(HELPDEZK_PATH . '/app/modules/acd/controllers/acdCommonController.php');

class acdAttendance extends acdCommon
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
        $this->idprogram =  $this->getIdProgramByController('acdAttendance');

        // Set attachments storage local
        $this->saveMode = $this->_s3bucketStorage ? "aws-s3" : 'disk';
        if($this->saveMode == "aws-s3"){
            $this->requestStoragePath = 'lgp/requestattachs/' ;
            $this->noteStoragePath = 'lgp/noteattachs/' ;
        }elseif($this->saveMode == "disk"){
            if($this->_externalStorage) {
                $this->requestStoragePath = $this->_externalStoragePath.'/lgp/requestattachs/' ;
                $this->noteStoragePath = $this->_externalStoragePath.'/lgp/noteattachs/' ;
            } else {
                $moduleDir = $this->_setFolder($this->helpdezkPath.'/app/uploads/lgp/');
                if(!$moduleDir['success']) {
                    $this->logIt("{$moduleDir['message']}. Program: {$this->program}" ,3,'general',__LINE__);
                }
            
                $targetDir = $this->_setFolder($moduleDir['path'] . "requestattachs/");    
                if(!$targetDir['success']) {
                    $this->logIt("{$targetDir['message']}. Program: {$this->program}" ,7,'general',__LINE__);
                }
                $this->requestStoragePath = isset($targetDir['path']) ? $targetDir['path'] : "";

                $noteDir = $this->_setFolder($moduleDir['path'] . "noteattachs/");    
                if(!$noteDir['success']) {
                    $this->logIt("{$noteDir['message']}. Program: {$this->program}" ,7,'general',__LINE__);
                }
                $this->noteStoragePath = isset($noteDir['path']) ? $noteDir['path'] : "";
            }
        }

        $this->databaseNow = ($this->database == 'oci8po') ? 'sysdate' : 'NOW()' ;
        $this->currentYear = date('Y');

        $this->loadModel('acdattendance_model');
        $this->dbAttendance = new acdattendance_model();

        $this->loadModel('acdstudent_model');
        $this->dbStudent = new acdstudent_model();

    }

    public function index()
    {

        $smarty = $this->retornaSmarty();
        $permissions = array_values($this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']));
        if($permissions[0] != "Y")
            $this->accessDenied();

        $smarty->assign('token', $this->_makeToken());
        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavAcd($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');
        
        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        $this->datepickerSettings($smarty);//set up datepicker options by language

        $smarty->display('acd-attendance-grid.tpl');
    }

    public function jsonGrid()
    {
        $this->validasessao();
        $this->protectFormInput();
        $smarty = $this->retornaSmarty();

        $where = '';
        $entry_date = " DATE_FORMAT(a.dtstart, '".$this->getConfig('date_format')." ".$this->getConfig('hour_format')."') AS fmt_dtstart" ;

        // create the query.
        $page  = $_POST['page'];
        $rows  = $_POST['rows'];
        $sidx  = $_POST['sidx'];
        $sord  = $_POST['sord'];

        if(!$sidx)
            $sidx ='a.dtstart';
        if(!$sord)
            $sord ='DESC';

        if ($_POST['_search'] == 'true'){

            switch($_POST['searchField']){
                case 'a.dtstart':
                    $searchField = 'DATE(a.dtstart)';
                    $_POST['searchString'] = substr($this->formatSaveDate($_POST['searchString']),1,-1);
                    break;
                default:
                    $searchField = $_POST['searchField'];
                    break;
            }
            
            $where .= ($where != '' ? ' AND ' : 'WHERE ') . $this->getJqGridOperation($_POST['searchOper'],$searchField ,$_POST['searchString']);
        }

        if(isset($_POST['txtSearch'])){
            $txtSearch = str_replace(' ','%',trim($_POST['txtSearch']));
            $paramW = "(c.name LIKE '%{$txtSearch}%' OR f.name LIKE '%{$txtSearch}%' OR DATE_FORMAT(a.dtstart,'%d/%m/%Y') = '{$txtSearch}') ";
            $where .= ($where != '' ? ' AND ' : 'WHERE ') . $paramW;
        }
        
        $count = $this->dbAttendance->getAttendance($entry_date,$where);
        if(!$count['success']){
            if($this->log)
                $this->logIt("Can't get row's total - {$count['message']} - User: {$_SESSION['SES_LOGIN_PERSON']} - program: {$this->program}" ,3,'general',__LINE__);
            return false;
        }
        
        $totalRows = count($count['data']);
        
        if($totalRows > 0 && $rows > 0) {
            $total_pages = ceil($totalRows/$rows);
        } else {
            $total_pages = 0;
        }
        if ($page > $total_pages) $page=$total_pages;
        $start = $rows*$page - $rows;
        if($start <0) $start = 0;

        $order = "ORDER BY $sidx $sord";
        $limit = "LIMIT $start , $rows";
        //

        $rsAttendances = $this->dbAttendance->getAttendance($entry_date,$where,$order,$limit);
        if(!$rsAttendances['success']){
            if($this->log)
                $this->logIt("Can't get attendances data - {$rsAttendances['message']} - User: {$_SESSION['SES_LOGIN_PERSON']} - program: {$this->program}" ,3,'general',__LINE__);
            return false;
        }
        
        foreach($rsAttendances['data'] as $key=>$val){
            
            $aColumns[] = array(
                'id'            => $val['idattendance'],
                'student'       => $val['student_name'],
                'subject'       => strip_tags($val['subject']),
                'dtstart'        => $val['dtstart']/*,
                'status'        => "<span style='color:{$val['status_color']}'>{$val['status_name']}</span>"*/
            );
        }


        $data = array(
            'page' => $page,
            'total' => $total_pages,
            'records' => $totalRows,
            'rows' => $aColumns
        );

        echo json_encode($data);

    }

    public function formCreate(){
        $smarty = $this->retornaSmarty();
        $permissions = array_values($this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']));
        if($permissions[0] != "Y")
            $this->accessDenied();

        $smarty->assign('token', $this->_makeToken());
        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavAcd($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        $smarty->assign('summernote_version', $this->summernote);

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        $this->datepickerSettings($smarty);//set up datepicker options by language
        
        $this->makeScreenAttendance($smarty,'','add');

        $smarty->display('acd-attendance-create.tpl');
    }

    public function formUpdate()
    {
        $smarty = $this->retornaSmarty();
        $permissions = array_values($this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']));
        if($permissions[0] != "Y")
            $this->accessDenied();

        $id = $this->getParam('id');
        $entry_date = " DATE_FORMAT(a.dtstart, '".$this->getConfig('date_format')." ".$this->getConfig('hour_format')."') AS fmt_dtstart" ;

        $rsAttendance = $this->dbAttendance->getAttendance($entry_date,"WHERE a.idattendance = $id");

        $this->makeScreenAttendance($smarty,$rsAttendance['data'],'update');
        $smarty->assign('token', $this->_makeToken()) ;

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavAcd($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');
        $smarty->assign('hidden_idattendance', $id);

        $smarty->assign('summernote_version', $this->summernote);
        
        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        $this->datepickerSettings($smarty);//set up datepicker options by language
        
        $smarty->display('acd-attendance-update.tpl');

    }

    /**
     * Formatting fields for New Attendance, Preview forms
     *
     * @param  mixed $objSmarty Smarty template engine object
     * @param  mixed $rs        Record set of the selected record (View)
     * @param  mixed $oper      Form Action (add: New / update: Edit)
     * @return void
     */
    function makeScreenAttendance($objSmarty,$rs,$oper){
        
        // --- Student ---
        if ($oper == 'add') {
            $studentSelected = "";
        }elseif ($oper == 'update' || $oper == 'echo') {
            $studentSelected = $rs[0]['idstudent'];
        }

        $where = "AND e.`year` = {$this->currentYear} AND e.record_status = 'A'";
        $order = "ORDER BY ser.idcurso,ser.numero,p.name";
        $aStudent = $this->_comboStudentClass(true,$where,$order);
        $objSmarty->assign('studentids',$aStudent['ids']);
        $objSmarty->assign('studentvals',$aStudent['values']);
        $objSmarty->assign('idstudent', $studentSelected);

        // --- Parents ---
        if ($oper == 'update') {
            $parentIds = explode(',',$rs[0]['parent_ids']);
            $parentVals = explode(',',$rs[0]['parent_name']);

            $retParent = $this->dbStudent->getStudentParent("AND a.idstudent = {$rs[0]['idstudent']}");
            if(!$retParent['success']){
                if($this->log)
                    $this->logIt("Can't get student data. - {$retParent['message']} - User: {$_SESSION['SES_LOGIN_PERSON']} - program: {$this->program} - Method: ". __METHOD__ ,3,'general',__LINE__);
            }

            foreach($retParent['data'] as $k=>$v){
                $checked = in_array($v['idparent'],$parentIds) ? "checked=checked" : "";
                $html .= "<div class='checkbox i-checks col-sm-12 col-md-12 col-lg-12'><input type='checkbox' id='parent-{$v['idparent']}' name='parent[]' value='{$v['idparent']}' {$checked} /><i></i> &nbsp;{$v['name']}</div>";
            }
            
            $objSmarty->assign('parentlist',$html);
        }

        // --- Other People ---
        if ($oper == 'update' || $oper == 'echo') {
            $objSmarty->assign('otherpeople',$rs[0]['personlist']);
        }

        // --- Subject ---
        if ($oper == 'update' || $oper == 'echo') {
            $subject = !empty($rs[0]['subject']) ? $rs[0]['subject'] : "";
            $objSmarty->assign('subject',$subject);
        }

        // --- Description ---
        if ($oper == 'update' || $oper == 'echo') {
            $description = !empty($rs[0]['description']) ? $rs[0]['description'] : "";
            $objSmarty->assign('description',$description);
        }

        // --- Datetime start ---
        if ($oper == 'update' || $oper == 'echo') {
            if ($oper == 'update') {
                $minLeft = (strtotime($rs[0]['dtstart']) - strtotime('NOW'));
                $objSmarty->assign('flgUpdDate',($minLeft >= 1800) ? "1" : "0");
            }
            $objSmarty->assign('dtstart',$this->formatDate($rs[0]['dtstart']));
            $objSmarty->assign('timestart',$this->formatHour($rs[0]['dtstart']));
        }

        // --- Duration ---
        if ($oper == 'update' || $oper == 'echo') {
            $objSmarty->assign('strHour',$rs[0]['hourend']);
            $objSmarty->assign('strMin',$rs[0]['minend']);
        }

    }

    public function createAttendance(){
        $this->protectFormInput();

        $smarty = $this->retornaSmarty();
        $langVars = $this->getLangVars($smarty);

        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }
        
        $studentID = $_POST['cmbStudent'];
        $parent = $_POST['parent'];
        $subject 	 = str_replace("'", "`", $_POST["subject"]);
        $subject 	 = strip_tags($subject);
        $otherPeople = str_replace("'", "`", $_POST["otherPeople"]);
        $otherPeople 	 = strip_tags($otherPeople);
        $dtStart    = str_replace("'", "",$this->formatSaveDateHour("{$_POST['dtstart']} {$_POST['timestart']}"));
        $dtEstimated = $this->makeDateHourEstimated($_POST['dtstart'],$_POST['timestart'],$_POST['strHour'],$_POST['strMinute']);
       
        $ins = $this->dbAttendance->insertAttendace($studentID,$subject,$otherPeople,$dtStart,$dtEstimated,$_POST['strHour'],$_POST['strMinute']);
        if(!$ins['success']){
            if($this->log)
                $this->logIt("Can't insert attendance data. - {$ins['message']} - User: {$_SESSION['SES_LOGIN_PERSON']} - program: {$this->program}" ,3,'general',__LINE__);
            return false;
        }
        
        $attendanceID = $ins['id'];

        foreach($parent as $k){
            $insParent = $this->dbAttendance->insertAttParent($attendanceID,$k);
            if(!$insParent['success']){
                if($this->log)
                    $this->logIt("Can't link parent to attendance # $attendanceID. - {$insParent['message']} - User: {$_SESSION['SES_LOGIN_PERSON']} - program: {$this->program}" ,3,'general',__LINE__);
                return false;
            }
        }

        $aRet = array(
            "success" => true,
            "attendanceID" => $attendanceID
        );

        echo json_encode($aRet);

    }

    public function updateAttendance(){
        $this->protectFormInput();

        $smarty = $this->retornaSmarty();
        $langVars = $this->getLangVars($smarty);

        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }
        
        $attendanceID = $_POST['idattendance'];
        $parent = $_POST['parent'];
        $subject 	 = str_replace("'", "`", $_POST["subject"]);
        $subject 	 = strip_tags($subject);
        $otherPeople = str_replace("'", "`", $_POST["otherPeople"]);
        $otherPeople 	 = strip_tags($otherPeople);
        $description = str_replace("'", "`", $_POST["description"]);
        $dtStart    = str_replace("'", "",$this->formatSaveDateHour("{$_POST['dtstart']} {$_POST['timestart']}"));
        $dtEstimated = $this->makeDateHourEstimated($_POST['dtstart'],$_POST['timestart'],$_POST['strHour'],$_POST['strMinute']);
       
        $upd = $this->dbAttendance->updateAttendace($attendanceID,$subject,$description,$otherPeople,$dtStart,$dtEstimated,$_POST['strHour'],$_POST['strMinute']);
        if(!$upd['success']){
            if($this->log)
                $this->logIt("Can't update attendance data. - {$upd['message']} - User: {$_SESSION['SES_LOGIN_PERSON']} - program: {$this->program}" ,3,'general',__LINE__);
            return false;
        }
        
        $del = $this->dbAttendance->removeAttParent($attendanceID);
        if(!$del['success']){
            if($this->log)
                $this->logIt("Can't delete attendance parent. - {$del['message']} - User: {$_SESSION['SES_LOGIN_PERSON']} - program: {$this->program}" ,3,'general',__LINE__);
            return false;
        }

        foreach($parent as $k){
            $insParent = $this->dbAttendance->insertAttParent($attendanceID,$k);
            if(!$insParent['success']){
                if($this->log)
                    $this->logIt("Can't link parent to attendance # $attendanceID. - {$insParent['message']} - User: {$_SESSION['SES_LOGIN_PERSON']} - program: {$this->program}" ,3,'general',__LINE__);
                return false;
            }
        }

        $aRet = array(
            "success" => true,
            "attendanceID" => $attendanceID
        );

        echo json_encode($aRet);

    }

    public function ajaxParents()
    {
        $this->protectFormInput();
        
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $studentID = $_POST['studentID'];
        
        $retParent = $this->dbStudent->getStudentParent("AND a.idstudent = {$studentID}");
        if(!$retParent['success']){
            if($this->log)
                $this->logIt("Can't get student data. - {$retParent['message']} - User: {$_SESSION['SES_LOGIN_PERSON']} - program: {$this->program} - Method: ". __METHOD__ ,3,'general',__LINE__);
        }

        foreach($retParent['data'] as $k=>$v){
            $html .= "<div class='checkbox i-checks col-sm-12 col-md-12 col-lg-12'><label><input type='checkbox' id='parent-{$v['idparent']}' name='parent[]' value='{$v['idparent']}'><i></i> &nbsp;{$v['name']}</label></div>";
        }

        echo $html;
    }    

    public function checkSchedule()
    {
        $this->protectFormInput();

        $smarty = $this->retornaSmarty();
        $langVars = $this->getLangVars($smarty);

        if(!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }    

        $dtStart    = $this->formatSaveDateHour("{$_POST['dtstart']} {$_POST['timestart']}");
        
        $entry_date = " DATE_FORMAT(a.dtstart, '".$this->getConfig('date_format')." ".$this->getConfig('hour_format')."') AS fmt_dtstart" ;
        $where = "WHERE a.dtstart = {$dtStart}";
        $where .= isset($_POST['attendanceID']) ? "AND a.idattendance != {$_POST['attendanceID']}" : "";

        $check = $this->dbAttendance->getAttendance($entry_date,$where);
        if(!$check['success']){
            if($this->log)
                $this->logIt("Can't get attendance data. - {$check['message']} - User: {$_SESSION['SES_LOGIN_PERSON']} - program: {$this->program}" ,3,'general',__LINE__);
            return false;
        }

        $success = count($check['data']) > 0 ? false : true;
        $msg = count($check['data']) > 0 ? $this->getLanguageWord('alert_attendance_exist') : "";
        $typeAlert = count($check['success']) > 0 ? "warning" : "";

        $aRet = array(
            "success" => $success,
            "message" => $msg,
            "type" => $typeAlert
        );

        echo json_encode($aRet);

    }

    public function makeDateHourEstimated($dtStart,$timeStart,$strHour,$strMin){
        $aDate = explode('/',$dtStart);
        $aTime = explode(':',$timeStart);
        
        return date("Y-m-d H:i:s",mktime(($aTime[0]+$strHour),($aTime[1]+$strMin),0,$aDate[1],$aDate[0],$aDate[2]));
    }

}