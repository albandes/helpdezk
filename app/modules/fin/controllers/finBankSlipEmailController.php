<?php

require_once(HELPDEZK_PATH . '/app/modules/fin/controllers/finCommonController.php');

class finBankSlipEmail extends finCommon
{
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
        $this->idprogram =  $this->getIdProgramByController('finBankSlipEmail');

        $this->loadModel('bankslipemail_model');
        $dbEmails = new bankslipemail_model();
        $this->dbEmails = $dbEmails;

        $this->loadModel('company_model');
        $dbCompany = new company_model();
        $this->dbCompany = $dbCompany;

        $this->loadModel('acd/acdclass_model');
        $dbClasses = new acdclass_model();
        $this->dbClasses = $dbClasses;

        $this->loadModel('admin/person_model');
        $dbPerson = new person_model();
        $this->dbPerson = $dbPerson;
    }

    public function index()
    {
        $smarty = $this->retornaSmarty();
        $permissions = array_values($this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']));

        $this->makeNavVariables($smarty,$this->idmodule);
        $this->makeFooterVariables($smarty);
        $this->_makeNavFin($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // --- Company ---
        $arrCompany = $this->_comboCompanies();
        $smarty->assign('companyids',  $arrCompany['ids']);
        $smarty->assign('companyvals', $arrCompany['values']);
        $smarty->assign('idcompany', 0 );

        // --- Month / Year ---
        $competence = date("m/Y");
        $smarty->assign('competence', $competence);

        $smarty->assign('token', $this->_makeToken()) ;

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);

        $this->_displayButtons($smarty,$permissions);
        if($permissions[0] == "Y"){
            $smarty->display('fin-bankslip-schedule.tpl');
        }else{
            $smarty->assign('href', $this->helpdezkUrl.'/fin/home');
            $smarty->display($this->helpdezkPath.'/app/modules/main/views/access_denied.tpl');
        }
    }

    public function jsonScheduleGrid()
    {
        $this->validasessao();
        $smarty = $this->retornaSmarty();

        $where = '';

        // create the query.
        $page  = $_POST['page'];
        $rows  = $_POST['rows'];
        $sidx  = $_POST['sidx'];
        $sord  = $_POST['sord'];

        if(!$sidx)
            $sidx ='competence';
        if(!$sord)
            $sord ='DESC';

        $where = "AND a.idperson = {$_SESSION['SES_COD_USUARIO']}";

        if ($_POST['_search'] == 'true'){
            if (empty($where))
                $oper = ' WHERE ';
            else
                $oper = ' AND ';

            switch ($_POST['searchField']){
                case 'company':
                    $searchField = "b.name";
                    break;
                case 'competence':
                    $searchField = "competence";
                    break;
                default:
                    $searchField = $_POST['searchField'];
                    break;
            }

            if($_POST['searchField'] == 'status' && $_POST['searchString'] == 1){
                $where .= $oper . "(". $this->getJqGridOperation($_POST['searchOper'],$searchField ,$_POST['searchString']).
                    " OR ". $this->getJqGridOperation($_POST['searchOper'],"idemail_status" ,2).") ";
            }else{
                $where .= $oper . $this->getJqGridOperation($_POST['searchOper'],$searchField ,$_POST['searchString']);
            }



        }

        $retCount = $this->dbEmails->getSchedule($where);
        if (!$retCount['success']) {
            if($this->log)
                $this->logIt($retCount['message'] . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
            return false;
        }

        $count = $retCount['data']->RecordCount();

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

        $rsEmails = $this->dbEmails->getSchedule($where,null,$order,$limit);
        if (!$rsEmails['success']) {
            if($this->log)
                $this->logIt($rsEmails['message'] . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
            return false;
        }

        while (!$rsEmails['data']->EOF) {
            $aColumns[] = array(
                'idschedule'   => $rsEmails['data']->fields['idschedule'],
                'company'   => $rsEmails['data']->fields['company'],
                'competence'    => $rsEmails['data']->fields['competence'],
                'dtentry'     => $rsEmails['data']->fields['fmt_dtentry']

            );
            $rsEmails['data']->MoveNext();
        }

        $data = array(
            'page' => $page,
            'total' => $total_pages,
            'records' => $count,
            'rows' => $aColumns
        );

        echo json_encode($data);
    }

    public function makeScheduleList($competence,$smarty)
    {
        $where = "AND competence = '{$competence}'";
        $order = "ORDER BY idcompany";
        $rs = $this->dbEmails->getSchedule($where,null,$order);
        if (!$rs['success']) {
            if($this->log)
                $this->logIt($rs['message'] . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
        }

        $list = "<div class='col-md-12 col-lg-12'><strong><u>{$this->getLanguageWord('registered_schedules')}</u><strong></div>";
        $smarty->assign('competence', $competence);
        $list .= "<div class='col-md-12 col-lg-12'><strong>{$this->getLanguageWord('fin_competence')} {$competence}<strong></div>";

        if($rs['data']->RecordCount() <= 0)
            $list .= "<div class='col-md-12 col-lg-12'><strong>{$this->getLanguageWord('fin_no_schedule')}<strong></div>";

        while(!$rs['data']->EOF){
            $list .= "<div class='col-md-12 col-lg-12'>
                           <div class='col-md-8 col-lg-8'>{$rs['data']->fields['company']}</div>
                           <div class='col-md-4 col-lg-4'>{$rs['data']->fields['fmt_dtentry']}</div>
                      </div>";
            $rs['data']->MoveNext();
        }

        $smarty->assign('scheduleList', $list);

    }

    public function listScheduleEmail()
    {
        $smarty = $this->retornaSmarty();
        $permissions = array_values($this->access($smarty,$_SESSION['SES_COD_USUARIO'],$this->idprogram,$_SESSION['SES_TYPE_PERSON']));

        $scheduleID = $this->getParam('idschedule');
        $smarty->assign('hidden_idschedule', $scheduleID);

        $this->makeNavVariables($smarty,$this->idmodule);
        $this->makeFooterVariables($smarty);
        $this->_makeNavFin($smarty);
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        $smarty->assign('token', $this->_makeToken()) ;
        $rs = $this->dbEmails->getSchedule("AND idschedule = {$scheduleID}");
        if (!$rs['success']) {
            if($this->log)
                $this->logIt($rs['message'] . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
        }

        $list = "<div class='col-md-12 col-lg-12'>
                           <div class='col-md-8 col-lg-8'>{$rs['data']->fields['company']}</div>
                           <div class='col-md-4 col-lg-4'>{$rs['data']->fields['competence']}</div>
                      </div>";
        $smarty->assign('scheduleList', $list);

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);

        $this->_displayButtons($smarty,$permissions);
        if($permissions[0] == "Y"){
            $smarty->display('fin-bankslip-email.tpl');
        }else{
            $smarty->assign('href', $this->helpdezkUrl.'/fin/home');
            $smarty->display($this->helpdezkPath.'/app/modules/main/views/access_denied.tpl');
        }
    }

    public function jsonGrid()
    {
        $this->validasessao();
        $scheduleID = $this->getParam('idschedule');

        // create the query.
        $page  = $_POST['page'];
        $rows  = $_POST['rows'];
        $sidx  = $_POST['sidx'];
        $sord  = $_POST['sord'];

        if(!$sidx)
            $sidx ='status';
        if(!$sord)
            $sord ='asc';

        switch ($sidx){
            case 'status':
                $order = "sidx_state ".$sord.", ts DESC";
                break;
            case 'sender':
                $order = "a.email ".$sord.", sidx_state ".$sord.", ts DESC";
                break;
            case 'toname':
                $order = "recipient_email ".$sord.", sidx_state ".$sord.", ts DESC";
                break;
            case 'subject':
                $order = "subject ".$sord.", sidx_state ".$sord.", ts DESC";
                break;
            case 'student':
                $order = "student_name ".$sord.", sidx_state ".$sord.", ts DESC";
                break;
            default:
                $order = $sidx.' '.$sord;
                break;
        }

        $where = "AND idschedule = {$scheduleID}";

        if ($_POST['_search'] == 'true'){
            if (empty($where))
                $oper = ' WHERE ';
            else
                $oper = ' AND ';

            switch ($_POST['searchField']){
                case 'sender':
                    $searchField = "c.name";
                    break;
                case 'toname':
                    $searchField = "recipient_email";
                    break;
                case 'ts':
                    $searchField = "FROM_UNIXTIME(ts,'%Y-%m-%d')";
                    $_POST['searchString'] = str_replace("'", "", $this->formatSaveDate($_POST['searchString']));
                    break;
                case 'status':
                    //when status is waiting process
                    if($_POST['searchString'] == 0){
                        $searchField = "idemail_status";
                        $_POST['searchString'] = 1;
                    }else{
                        $searchField = "idstate";
                    }
                    break;
                case 'subject':
                    $searchField = "subject";
                    break;
                case 'student':
                    $searchField = "d.name";
                    break;
                default:
                    $searchField = $_POST['searchField'];
                    break;
            }

            if($_POST['searchField'] == 'status' && $_POST['searchString'] == 1){
                $where .= $oper . "(". $this->getJqGridOperation($_POST['searchOper'],$searchField ,$_POST['searchString']).
                    " OR ". $this->getJqGridOperation($_POST['searchOper'],"idemail_status" ,2).") ";
            }else{
                $where .= $oper . $this->getJqGridOperation($_POST['searchOper'],$searchField ,$_POST['searchString']);
            }



        }

        //$group = "GROUP BY d.idemail";
        //$count = $this->dbEmails->countEmails($where);
        $retCount = $this->dbEmails->getEmailsStats($where);
        $count = $retCount->RecordCount();

        if( $count > 0 && $rows > 0) {
            $total_pages = ceil($count/$rows);
        } else {
            $total_pages = 0;
        }

        if ($page > $total_pages) $page=$total_pages;
        $start = $rows*$page - $rows;
        if($start <0) $start = 0;


        $order = "ORDER BY $order";
        $limit = "LIMIT $start , $rows";
        //

        $rsEmails = $this->dbEmails->getEmailsStats($where,null,$order,$limit);

        while (!$rsEmails->EOF) {
            if(!$rsEmails->fields['idstate'] || $rsEmails->fields['idemail_status'] == '5'){
                $confStyle = "style='color:{$rsEmails->fields['color']};'";
                $status_fmt = "<span $confStyle>{$rsEmails->fields['description']}</span>";
                $ts_fmt = $rsEmails->fields['ts'] ? date('d/m/Y H:i:s',$rsEmails->fields['ts']) : '00/00/0000 00:00:00';
            }else{
                switch ($rsEmails->fields['idstate']){
                    case 2:
                        $confStyle = "style='color:#708090;'";
                        break;
                    case 3:
                        $confStyle = "style='color:#A0522D;'";
                        break;
                    case 4:
                        $confStyle = "style='color:#9ACD32;'";
                        break;
                    case 5:
                        $confStyle = "style='color:#4169E1;'";
                        break;
                    case 6:
                        $confStyle = "style='color:#B8860B;'";
                        break;
                    case 7:
                        $confStyle = "style='color:#ed5565;'";
                        break;
                    default:
                        $confStyle = "style='color:#19aa8d;'";
                        break;

                }

                $stname = $this->dbEmails->getEmailStateName($rsEmails->fields['idstate']);
                $lbl = $this->getLanguageWord('emq_state_'.$stname->fields['state-name']);
                $txt = !$lbl ? $stname->fields['state-name'] : $lbl;

                $status_fmt = "<span $confStyle>$txt</span>";
                $ts_fmt = date('d/m/Y H:i:s',$rsEmails->fields['ts_state']);
            }



            $aColumns[] = array(
                'idemail'   => $rsEmails->fields['idemail'],
                'subject'   => $rsEmails->fields['subject'],
                'sender'    => $rsEmails->fields['email'],
                'email'     => $rsEmails->fields['recipient_email'],
                'student'   => $rsEmails->fields['student_name'],
                'status'    => $status_fmt,
                'ts'        => $ts_fmt,
                'statusval' => $rsEmails->fields['idemail_status']

            );
            $rsEmails->MoveNext();
        }


        $data = array(
            'page' => $page,
            'total' => $total_pages,
            'records' => $count,
            'rows' => $aColumns
        );

        echo json_encode($data);
    }

    public function saveSchedule()
    {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $companyID = $_POST['companyID'];
        $competence = $this->_protect($_POST['competence']);

        $where = "AND a.idcompany = {$companyID} AND competence = '{$competence}'";
        $check = $this->dbEmails->getSchedule($where);
        if (!$check['success']) {
            if($this->log)
            $this->logIt($check['message'] . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);

            echo json_encode(array("status"=>"Error","message"=>$this->getLanguageWord("Alert_failure"),"alerttype"=>"danger"));
            exit;
        }

        if($check['data']->RecordCount() > 0){
            echo json_encode(array("status"=>"Error","message"=>$this->getLanguageWord("Value_exists"),"alerttype"=>"danger"));
            exit;
        }

        $this->dbEmails->BeginTrans();

        $ret = $this->dbEmails->insertSchedule($this->idPerson,$companyID,$competence);
        if (!$ret['success']) {
            $this->dbEmails->RollbackTrans();
            if($this->log)
                $this->logIt($ret['message'] . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);

            echo json_encode(array("status"=>"Error","message"=>$this->getLanguageWord("Alert_failure"),"alerttype"=>"danger"));
            exit;
        }

        $this->dbEmails->CommitTrans();

        echo json_encode(array("status"=>"success"));
    }

    public function viewEmail()
    {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $idemail = $_POST['idemail'];

        $rs = $this->dbEmails->getEmail($idemail);
        if(!$rs['success']){
            if($this->log)
                $this->logIt("Can't get E-mail Data. ID {$idemail} - User: {$_SESSION['SES_LOGIN_PERSON']} - Program: {$this->program} - Method: ". __METHOD__."\nError: {$rs['message']}" ,3,'general',__LINE__);
            return false;
        }

        $ret = $rs['data'];

        $confStyle = "style='color:{$ret->fields['color']};'";
        $status_fmt = "<span $confStyle>{$ret->fields['description']}</span>";
        $ts_fmt = $ret->fields['ts'] ? date('d/m/Y H:i:s',$ret->fields['ts']) : '';
        $student = $ret->fields['idenrollment'] ? $ret->fields['idenrollment']. " - " .$ret->fields['student_name'] : '';
        $aTmp = explode('/',$ret->fields['competence']);

        $attachs = '';
        if($ret->fields['attachs']){
            $arrAttch = explode(',',$ret->fields['attachs']);
            $aAttchLink = explode(',',$ret->fields['attachslinks']);
            foreach ($arrAttch as $k=>$v){
                $fileLink = "{$this->helpdezkUrl}{$aAttchLink[$k]}";
                $attachs .= "<a href='{$fileLink}' target='_blank'>
                                <span class='label label-default'><i class='fa fa-file'></i>   $v</span>
                             </a>";
            }
        }

        $arrRet = array(
            "subject" => $ret->fields['subject'],
            "to" => $ret->fields['recipient_name'].' - '.$ret->fields['recipient_email'],
            "body" => $ret->fields['body'],
            "status" => $status_fmt,
            "sent_date" => $ts_fmt,
            "attachaments" => $attachs,
            "student" => $student,
            "statusID" => $ret->fields['idemail_status']
        );

        if($ret->fields['idmandrill']){
            $rsLog = $this->dbEmails->getLogMandrill($ret->fields['idmandrill']);
            if(!$rs['success']){
                if($this->log)
                    $this->logIt("Can't get Mandrill's log. ID {$idemail} - User: {$_SESSION['SES_LOGIN_PERSON']} - Program: {$this->program} - Method: ". __METHOD__."\nError: {$rsLog['message']}" ,3,'general',__LINE__);
                return false;
            }

            $retLog = $rsLog['data'];
            $msg = '';
            while(!$retLog->EOF){
                switch ($retLog->fields['idstate']){
                    case 2:
                        $icone = '';
                        $ts = $retLog->fields['ts_deferral'];
                        $description = '';
                        $diag = $retLog->fields['def_diag'];
                        $flgmobile = '';
                        $city = '';
                        $country = '';
                        $ua = '';
                        $uafamily = '';
                        break;
                    case 3:
                    case 4:
                        $icone = '';
                        $ts = $retLog->fields['ts_bounce'];
                        $description = $retLog->fields['bounce_description'];
                        $diag = $retLog->fields['bounce_diag'];
                        $flgmobile = '';
                        $city = '';
                        $country = '';
                        $ua = '';
                        $uafamily = '';
                        break;
                    case 5:
                        $icone = "<img src='{$retLog->fields['os_icon']}'>";
                        $ts = $retLog->fields['ts_open'];
                        $description = '';
                        $diag = '';
                        $flgmobile = $retLog->fields['mobile'] == 1 ? "<i class='fa fa-check'>" : '';
                        $city = $retLog->fields['city'];
                        $country = $retLog->fields['country'];
                        $ua = $retLog->fields['user_agent'];
                        $uafamily = $retLog->fields['ua_family'];
                        break;
                    case 6:
                        $icone = '';
                        $ts = $retLog->fields['ts_spam'];
                        $description = '';
                        $diag = '';
                        $flgmobile = '';
                        $city = '';
                        $country = '';
                        $ua = '';
                        $uafamily = '';
                        break;
                    case 7:
                        $icone = '';
                        $ts = $retLog->fields['ts_reject'];
                        $description = '';
                        $diag = '';
                        $flgmobile = '';
                        $city = '';
                        $country = '';
                        $ua = '';
                        $uafamily = '';
                        break;
                    default:
                        $icone = '';
                        $ts = $retLog->fields['ts_send'];
                        $description = '';
                        $diag = '';
                        $flgmobile = '';
                        $city = '';
                        $country = '';
                        $ua = '';
                        $uafamily = '';
                        break;

                }

                $msg .= "<tr>
                               <td>{$retLog->fields['state-name']}</td>
                               <td class='text-center'>$icone</td>
                               <td>$ts</td>
                               <td>$description</td>
                               <td>$diag</td>
                               <td class='text-center'>$flgmobile</td>
                               <td>$city</td>
                               <td>$country</td>
                               <td>$ua</td>
                               <td>$uafamily</td>
                         </tr>";

                $retLog->MoveNext();
            }

            $arrRet['logmandrill'] = $msg;
        }

        $arrRet['senderserver'] = $ret->fields['srvsend'] == 'SMTP' ? 'GOOGLE' : strtoupper($ret->fields['srvsend']);

        echo json_encode($arrRet);

    }

    public function encodeString($string)
    {
        return urlencode(urlencode($string));

    }

    /**
     * Return a string / Array protected against SQL / Blind / XSS Injection
     *
     * @param $str
     * @return array|string
     */
    function _protect($str) {
        if( !is_array( $str ) ) {
            $str = preg_replace( '/(from|select|insert|delete|where|drop|union|order|update|database|FROM|SELECT|INSERT|DELETE|WHERE|DROP|UNION|ORDER|UPDATE|DATABASE|AND|and|HAVING|having|SLEEP|sleep|OR|or)/i', '', $str );
            $str = preg_replace( '/(&lt;|<)?script(\/?(&gt;|>(.*))?)/i', '', $str );
            $tbl = get_html_translation_table( HTML_ENTITIES );
            $tbl = array_flip( $tbl );
            $str = addslashes( $str );
            $str = strip_tags( $str );
            return strtr( $str, $tbl );
        } else {
            return array_filter( $str, "_protect" );
        }
    }

    public function resend()
    {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $idemail = $_POST['emailID'];
        $sendertitle = "Escola Mario Quintana";

        $rs = $this->dbEmails->getEmail($idemail);
        if(!$rs['success']){
            if($this->log)
                $this->logIt("Can't get E-mail Data. ID {$idemail} - User: {$_SESSION['SES_LOGIN_PERSON']} - Program: {$this->program} - Method: ". __METHOD__."\nError: {$rs['message']}" ,3,'general',__LINE__);
            return false;
        }

        $ret = $rs['data'];
        $this->dbEmails->BeginTrans();

        $retSpool = $this->dbEmails->insertSpool($this->idPerson,$sendertitle,$ret->fields['subject'],addslashes($ret->fields['body']),addslashes($ret->fields['body_push']),$ret->fields['competence'],$ret->fields['idcompany']);
        if (!$retSpool['success']) {
            $this->dbEmails->RollbackTrans();
            if($this->log)
                $this->logIt($retSpool['message'] . ' - program: '.$this->program ,3,'general',__LINE__);
            return false;
        }

        $retAtt = $this->dbEmails->saveAttachment($retSpool['id'],$ret->fields['attachs'],$ret->fields['attachsdir']);
        if (!$retAtt['success']) {
            $this->dbEmails->RollbackTrans();
            if($this->log)
                $this->logIt($retAtt['message'] . ' - program: '.$this->program ,3,'general',__LINE__);
            return false;
        }

        $insRecipient = $this->dbEmails->insertSpoolRecipient($retSpool['id'],addslashes($ret->fields['recipient_name']),$ret->fields['recipient_email'],$ret->fields['idemail_server'],$ret->fields['idrecipient_push']);
        if (!$insRecipient['success']) {
            $this->dbEmails->RollbackTrans();
            if($this->log)
                $this->logIt($insRecipient['message'] . ' - program: '.$this->program ,3,'general',__LINE__);
            return false;
        }

        $makeEmail = $this->saveTracker($this->idmodule,$ret->fields['email'],$ret->fields['recipient_email'],$ret->fields['subject'],addslashes($ret->fields['body']));
        if(!$makeEmail){
            $this->dbEmails->RollbackTrans();
            if($this->log)
                $this->logIt('Can\'t make E-mail tracker - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $bindEmail = $this->dbEmails->insertBindEmail($makeEmail,$insRecipient['id'],1);
        if (!$bindEmail['success']) {
            $this->dbEmails->RollbackTrans();
            if($this->log)
                $this->logIt($bindEmail['message'] . ' - program: '.$this->program ,3,'general',__LINE__);
            return false;
        }

        $insRecipientStudent = $this->dbEmails->insertRecipientStudentBind($insRecipient['id'],$ret->fields['idstudent']);
        if (!$insRecipientStudent['success']) {
            $this->dbEmails->RollbackTrans();
            if($this->log)
                $this->logIt($insRecipientStudent['message'] . ' - program: '.$this->program ,3,'general',__LINE__);
            return false;
        }

        $retSend = $this->sendNewEmail($retSpool['id']);

        if($retSend['success']){
            $retUpd = $this->dbEmails->updateRecipientStatus($insRecipient['id'],5);

            if (!$retUpd['success']) {
                $this->dbEmails->RollbackTrans();
                if($this->log)
                    $this->logIt($retUpd['message'] . " - recipient {$insRecipient['id']}".' - program: '.$this->program ,3,'general',__LINE__);
                return false;
            }
            $this->dbEmails->CommitTrans();
            echo json_encode(array("success"=>true,"message"=>$this->getLanguageWord('sucess_resend_email'),"alerttype"=>"success"));
        }else{
            $this->dbEmails->RollbackTrans();
            $this->dbEmails->CommitTrans();
            echo json_encode(array("success"=>false,"message"=>$this->getLanguageWord('failure_resend_email'),"alerttype"=>"danger"));
        }

    }

    public function sendNewEmail($emailID){
        $aNotSend = array();
        $flgSend = false;
        $ret = $this->dbEmails->getSpoolToSend("WHERE a.idspool = {$emailID}");
        if(!$ret['success']){
            if($this->log)
                $this->logIt("Can't get E-mail Data. ID {$emailID} - User: {$_SESSION['SES_LOGIN_PERSON']} - Program: {$this->program} - Method: ". __METHOD__."\nError: {$ret['message']}" ,3,'general',__LINE__);
            return false;
        }

        $rs = $ret['data'];

        while(!$rs->EOF){
            $atts = explode(',',$rs->fields['attachments']);
            $idatts = explode(',',$rs->fields['idattachments']);
            $diratts = explode(',',$rs->fields['attach_dir']);
            $sendAvailable = true;

            $arrAtt = array();

            if($rs->fields['attachments'] != ''){
                $fileAvailable = $this->existAttachment($atts,$idatts,$diratts);

                if($fileAvailable == 0)
                    $sendAvailable = false;

                foreach ($atts as $k=>$v){
                    $extension = strrchr($v, ".");

                    $bus = array(
                        "filepath" => HELPDEZK_PATH . $diratts[$k] ."/". $v,
                        "filename" => $v
                    );

                    array_push($arrAtt,$bus);

                }
            }

            $bodyFinal = $this->formatBody($rs->fields['body']);
            $params = array("subject" => $rs->fields['subject'],
                "contents" => $bodyFinal,
                "sender_name" => $rs->fields['sender_title'],
                "sender" => $rs->fields['sender'],
                "address" => array(
                    array('to_name'=> $rs->fields['recipient_name'],
                        'to_address' => $rs->fields['recipient_email'])
                ),
                "attachment" => $arrAtt,
                "idemail" => $rs->fields['idemail'],
                "idmodule" => $rs->fields['idmodule'],
                "modulename" => $rs->fields['module_name'],
                "msg" => "",
                "msg2" => ""
            );

            if($rs->fields['idemail_server'] == 2)
                $params['tracker'] = $this->tracker;

            if($sendAvailable){
                $done = $this->_sendEmailDefault($params,$rs->fields['server_name']);

                if($done){
                    $upd = $this->dbEmails->updateRecipientSent($rs->fields['idspool_recipient']);
                    if (!$upd['success']) {
                        if($this->log)
                            $this->logIt($upd['message'] . ' - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
                    }
                    $flgSend = true;
                }else{
                    if($this->log)
                        $this->logIt('Send E-mail Spool - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                }

            }else{
                if(!in_array($rs->fields['idspool'],$aNotSend))
                    array_push($aNotSend,$rs->fields['idspool']);

                $updNotSend = $this->updateStatusNotSend($rs->fields['idspool_recipient']);
                if(!$updNotSend){
                    if($this->log)
                        $this->logIt("Status not updated. ID recipient {$rs->fields['idspool_recipient']}  - program: {$this->program} - method: ". __METHOD__ ,3,'general',__LINE__);
                }
            }


            $rs->MoveNext();
        }

        if($flgSend){
            return array('success'=>true,'message'=>'');
        }
        else{
            return array('success'=>false,'message'=>"Can't send email");
        }
    }

    function existAttachment($arrAttName,$arrAttId,$arrAttDir)
    {
        $send = 1;
        foreach ($arrAttName as $k=>$v){
            $extension = strrchr($v, ".");
            $findPath = HELPDEZK_PATH . $arrAttDir[$k] ."/". $v;

            if(!file_exists($findPath))
                $send = 0;
        }

        return $send;
    }

    public function formatBody($body)
    {
        $html = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
                    <html xmlns='http://www.w3.org/1999/xhtml'>
                    <head>
                        <meta name='viewport' content='width=device-width' />
                        <meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
                        <style type='text/css'>
                            /* -------------------------------------
                                GLOBAL
                                A very basic CSS reset
                            ------------------------------------- */
                            * {
                                margin: 0;
                                padding: 0;
                                font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
                                box-sizing: border-box;
                                font-size: 14px;
                            }
                    
                            img {
                                max-width: 100%;
                            }
                    
                            body {
                                -webkit-font-smoothing: antialiased;
                                -webkit-text-size-adjust: none;
                                width: 100% !important;
                                height: 100%;
                                line-height: 1.6;
                            }
                    
                            /* Let's make sure all tables have defaults */
                            table td {
                                vertical-align: top;
                            }
                    
                            /* -------------------------------------
                                BODY & CONTAINER
                            ------------------------------------- */
                            body {
                                background-color: #f6f6f6;
                            }
                    
                            .body-wrap {
                                background-color: #f6f6f6;
                                width: 100%;
                            }
                    
                            .container {
                                display: block !important;
                                max-width: 600px !important;
                                margin: 0 auto !important;
                                /* makes it centered */
                                clear: both !important;
                            }
                    
                            .content {
                                max-width: 600px;
                                margin: 0 auto;
                                display: block;
                                padding: 20px;
                            }
                    
                            /* -------------------------------------
                                HEADER, FOOTER, MAIN
                            ------------------------------------- */
                            .main {
                                background: #fff;
                                border: 1px solid #e9e9e9;
                                border-radius: 3px;
                            }
                    
                            .content-wrap {
                                padding: 20px;
                            }
                    
                            .content-block {
                                padding: 0 0 20px;
                            }
                    
                            .header {
                                width: 100%;
                                margin-bottom: 20px;
                            }
                    
                            .footer {
                                width: 100%;
                                clear: both;
                                color: #999;
                                padding: 20px;
                            }
                            .footer a {
                                color: #999;
                            }
                            .footer p, .footer a, .footer unsubscribe, .footer td {
                                font-size: 12px;
                            }
                    
                            /* -------------------------------------
                                TYPOGRAPHY
                            ------------------------------------- */
                            h1, h2, h3 {
                                font-family: 'Helvetica Neue', Helvetica, Arial, 'Lucida Grande', sans-serif;
                                color: #000;
                                margin: 40px 0 0;
                                line-height: 1.2;
                                font-weight: 400;
                            }
                    
                            h1 {
                                font-size: 32px;
                                font-weight: 500;
                            }
                    
                            h2 {
                                font-size: 24px;
                            }
                    
                            h3 {
                                font-size: 18px;
                            }
                    
                            h4 {
                                font-size: 14px;
                                font-weight: 600;
                            }
                    
                            p, ul, ol {
                                margin-bottom: 10px;
                                font-weight: normal;
                            }
                            p li, ul li, ol li {
                                margin-left: 5px;
                                list-style-position: inside;
                            }
                    
                            /* -------------------------------------
                                LINKS & BUTTONS
                            ------------------------------------- */
                            a {
                                color: #1ab394;
                                text-decoration: underline;
                            }
                    
                            .btn-primary {
                                text-decoration: none;
                                color: #FFF;
                                background-color: #1ab394;
                                border: solid #1ab394;
                                border-width: 5px 10px;
                                line-height: 2;
                                font-weight: bold;
                                text-align: center;
                                cursor: pointer;
                                display: inline-block;
                                border-radius: 5px;
                                text-transform: capitalize;
                            }
                    
                            /* -------------------------------------
                                OTHER STYLES THAT MIGHT BE USEFUL
                            ------------------------------------- */
                            .last {
                                margin-bottom: 0;
                            }
                    
                            .first {
                                margin-top: 0;
                            }
                    
                            .aligncenter {
                                text-align: center;
                            }
                    
                            .alignright {
                                text-align: right;
                            }
                    
                            .alignleft {
                                text-align: left;
                            }
                    
                            .clear {
                                clear: both;
                            }
                            
                            .alignjustify {
                                text-align: justify;
                                text-justify: inter-word;
                            }	
                            
                    
                            /* -------------------------------------
                                ALERTS
                                Change the class depending on warning email, good email or bad email
                            ------------------------------------- */
                            .alert {
                                font-size: 16px;
                                color: #fff;
                                font-weight: 500;
                                padding: 20px;
                                text-align: center;
                                border-radius: 3px 3px 0 0;
                            }
                            .alert a {
                                color: #fff;
                                text-decoration: none;
                                font-weight: 500;
                                font-size: 16px;
                            }
                            .alert.alert-warning {
                                background: #f8ac59;
                            }
                            .alert.alert-bad {
                                background: #ed5565;
                            }
                            .alert.alert-good {
                                background: #1ab394;
                            }
                    
                            /* -------------------------------------
                                INVOICE
                                Styles for the billing table
                            ------------------------------------- */
                            .invoice {
                                margin: 40px auto;
                                text-align: left;
                                width: 80%;
                            }
                            .invoice td {
                                padding: 5px 0;
                            }
                            .invoice .invoice-items {
                                width: 100%;
                            }
                            .invoice .invoice-items td {
                                border-top: #eee 1px solid;
                            }
                            .invoice .invoice-items .total td {
                                border-top: 2px solid #333;
                                border-bottom: 2px solid #333;
                                font-weight: 700;
                            }
                    
                            /* -------------------------------------
                                RESPONSIVE AND MOBILE FRIENDLY STYLES
                            ------------------------------------- */
                            @media only screen and (max-width: 640px) {
                                h1, h2, h3, h4 {
                                    font-weight: 600 !important;
                                    margin: 20px 0 5px !important;
                                }
                    
                                h1 {
                                    font-size: 22px !important;
                                }
                    
                                h2 {
                                    font-size: 18px !important;
                                }
                    
                                h3 {
                                    font-size: 16px !important;
                                }
                    
                                .container {
                                    width: 100% !important;
                                }
                    
                                .content, .content-wrap {
                                    padding: 10px !important;
                                }
                    
                                .invoice {
                                    width: 100% !important;
                                }
                            }
                        </style>
                    </head>
                    
                    <body>";

        $html .= $body;

        $html .= "</body>
                  </html>";

        return $html;
    }

    function updateStatusNotSend($idrecipient)
    {
        $ret = $this->dbEmails->updateRecipientStatus($idrecipient,4);

        if (!$ret['success']) {
            if($this->log)
                $this->logIt($ret['message'] . "recipient {$idrecipient}".' - program: '.$this->program ,3,'general',__LINE__);
            return false;
        }

        return;
    }

}