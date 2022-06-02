<?php
require_once(HELPDEZK_PATH . '/app/modules/emq/controllers/emqCommonController.php');

class emqEmail  extends emqCommon {

    public function __construct(){

        parent::__construct();
        session_start();
        $this->sessionValidate();

        // Log settings
        $this->log = parent::$_logStatus;

        $this->modulename = 'intranet' ;
        $this->program  = basename( __FILE__ );
        $dbCommon = new common();
        $id = $dbCommon->getIdModule($this->modulename) ;
        if(!$id) {
            die('Module don\'t exists in tbmodule !!!') ;
        } else {
            $this->idmodule = $id ;
        }

        $this->databaseNow = ($this->database == 'oci8po' ? 'sysdate' : 'now()') ;

        $this->loadModel('emails_model');
        $dbEmails = new emails_model();
        $this->dbEmails = $dbEmails;

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
        //echo "<pre>"; print_r($_SESSION); echo "</pre>";
        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavEmq($smarty);

        $smarty->assign('token', $this->_makeToken()) ;
        $smarty->assign('lang_default', $this->getConfig('lang'));
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);

        $smarty->display('emq-emails.tpl');

    }

    public function jsonGrid()
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
            $sidx ='status';
        if(!$sord)
            $sord ='asc';

        switch ($sidx){
            case 'status':
                $order = "sidx_state ".$sord.", ts DESC";
                break;
            case 'sender':
                $order = "email ".$sord.", sidx_state ".$sord.", ts DESC";
                break;
            case 'toname':
                $order = "recipient_email ".$sord.", sidx_state ".$sord.", ts DESC";
                break;
            case 'subject':
                $order = "subject ".$sord.", sidx_state ".$sord.", ts DESC";
                break;
            default:
                $order = $sidx.' '.$sord;
                break;
        }

        $where = "WHERE b.idperson = {$_SESSION['SES_COD_USUARIO']}";

        if ($_POST['_search'] == 'true'){
            if (empty($where))
                $oper = ' WHERE ';
            else
                $oper = ' AND ';

            switch ($_POST['searchField']){
                case 'sender':
                    $searchField = "c.email";
                    break;
                case 'toname':
                    $searchField = "a.recipient_email";
                    break;
                case 'ts':
                    $searchField = "IFNULL(FROM_UNIXTIME(f.ts,'%Y-%m-%d'),a.dtentry)";
                    $_POST['searchString'] = str_replace("'", "", $this->formatSaveDate($_POST['searchString']));
                    break;
                case 'status':
                    //when status is waiting process
                    if($_POST['searchString'] == 0){
                        $searchField = "d.idemail_status";
                        $_POST['searchString'] = 1;
                    }else{
                        //$searchField = "idstate";
                        $searchField = "(SELECT idstate FROM emq_tbemail_history WHERE (idemail = h.idemail) ORDER BY ts DESC LIMIT 1)";
                    }
                    break;
                case 'subject':
                    $searchField = "b.subject";
                    break;
                default:
                    $searchField = $_POST['searchField'];
                    break;
            }

            if($_POST['searchField'] == 'status' && $_POST['searchString'] == 1){
                $where .= $oper . "(". $this->getJqGridOperation($_POST['searchOper'],$searchField ,$_POST['searchString']).
                            " OR (". $this->getJqGridOperation($_POST['searchOper'],"d.idemail_status" ,2)."
                                    AND (SELECT idstate FROM emq_tbemail_history WHERE (idemail = h.idemail) ORDER BY ts DESC LIMIT 1) IS NULL)) ";
            }else{
                $where .= $oper . $this->getJqGridOperation($_POST['searchOper'],$searchField ,$_POST['searchString']);
            }



        }

        //$group = "GROUP BY d.idemail";
        $group = "";

        $retCount = $this->dbEmails->countEmails($where);
        $count = $retCount->fields['total'];

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

        $rsEmails = $this->dbEmails->getEmailsStats($where,$group,$order,$limit);

        while (!$rsEmails->EOF) {
            if(!$rsEmails->fields['idstate']){
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

    public function formSendEmail()
    {
        $smarty = $this->retornaSmarty();

        $arrType['ids'] = array(1,2,3,4,5);
        $arrType['values'] = array(
            $this->getLanguageWord('emq_type_employees'),
            $this->getLanguageWord('emq_type_parents'),
            $this->getLanguageWord('emq_type_parents_new'),
            $this->getLanguageWord('emq_type_teachers'),
            $this->getLanguageWord('emq_type_sports_experiences')
        );

        $smarty->assign('typesendids',  $arrType['ids']);
        $smarty->assign('typesendvals', $arrType['values']);
        $smarty->assign('idtypesend', 2 );


        $smarty->assign('token', $this->_makeToken()) ;
        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavEmq($smarty);

        $smarty->assign('noteattmaxfiles', $this->_getNoteAttMaxFiles());
        $smarty->assign('noteacceptedfiles', $this->_getNoteAcceptedFiles());
        $smarty->assign('ticketattmaxfiles', $this->_getTicketAttMaxFiles());
        $smarty->assign('summernote_version', $this->summernote);
        $smarty->assign('hdkMaxSize', substr($this->_getTicketAttMaxFileSize(),0,-1) );

        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');

        // Demo version
        $smarty->assign('demoversion', $this->demoVersion);
        
        $smarty->display('emq-send-email.tpl');
    }

    public function sectionsList()
    {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }
        $typeSend = $_POST['idtypesend'];

        $tabBody = $this->makeList($typeSend);

        $aRet = array(
            "tabList" => $tabBody
        );

        echo json_encode($aRet);

    }

    public function makeList($type){
        switch ($type){
            case 1:
                $sectionlist = $this->makeSectorsList($type);
                break;
            case 2:
            case 3:
            case 6:
                $year = ($type == 2 || $type == 6) ? date('Y') : date('Y') + 1;
                $sectionlist = $this->makeClassesList($year,$type);
                break;
            case 4:
                $sectionlist = $this->makeCoursesList($type);
                break;
            default:
                break;
        }

        return $sectionlist;


    }

    public function makeClassesList($year,$type){
        $where = "AND `year` = $year AND record_status = 'A'";
        $where .= $type == 6 ? " AND b.idcurso IN (4,5)" : " AND b.idcurso IN (1,2,3)";
        $where .= $type == 3 ? " AND c.idstatusenrollment = 1" : "";
        $order = "ORDER BY b.idcurso, b.numero, a.numero";
        $ret = $this->dbClasses->getClassesByEnrollment($where,$order);

        if(!$ret){
            if($this->log)
                $this->logIt('Error get Classes - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
            return false;
        }

        $tabBody = '';
        if($ret->RecordCount() > 0){
            $lbl = $type == 6 ? $this->getLanguageWord('emq_type_bilingual').' - '.$this->getLanguageWord('acd_academic_year').' '.$year : $this->getLanguageWord('acd_academic_year').' '.$year;
            $tabBody .= "<tr class='section_{$type} bg-info'><td><strong>{$lbl}</strong></td></tr>";
        }

        while(!$ret->EOF) {
            $tabBody .= "<tr class='section_{$type}'>
                                <td>
                                    <div>
                                        <input type='checkbox' onclick='viewRecipients()'  name='section' value='{$type}|{$ret->fields['idturma']}' id='{$type}_{$ret->fields['idturma']}'>&nbsp;
                                        <span>{$ret->fields['abrev']}</span>
                                    </div>
                                </td>
                            </tr>";

            $ret->MoveNext();
        }

        return $tabBody;
    }

    public function makeSectorsList($type){
        $ret = $this->_comboSectors();

        $tabBody = "<tr class='section_{$type} bg-info'><td><strong>{$this->getLanguageWord('emq_sectors')}</strong></td></tr>";
        for($i=0;$i < sizeof($ret['ids']);$i++) {
            $tabBody .= "<tr class='section_{$type}'>
                                <td>
                                    <div>
                                        <input type='checkbox' onclick='viewRecipients()' name='section' value='{$type}|{$ret['ids'][$i]}' id='{$type}_{$ret['ids'][$i]}'>&nbsp;
                                        <span>{$ret['values'][$i]}</span>
                                    </div>
                                </td>
                            </tr>";
        }

        return $tabBody;
    }

    public function makeCoursesList($type){
        $ret['ids'] = array('EI','EF1','EF2','EM','BIL');
        $ret['values'] = array('Preescola','Fundamental I','Fundamental II','M&eacute;dio','Bilíngue');

        $tabBody = "<tr class='section_{$type} bg-info'><td><strong>{$this->getLanguageWord('emq_courses_lbl')}</strong></td></tr>";
        for($i=0;$i < sizeof($ret['ids']);$i++) {
            $tabBody .= "<tr class='section_{$type}'>
                                <td>
                                    <div>
                                        <input type='checkbox' onclick='viewRecipients()' name='section' value='{$type}|{$ret['ids'][$i]}' id='{$type}_{$ret['ids'][$i]}'>&nbsp;
                                        <span>{$ret['values'][$i]}</span>
                                    </div>
                                </td>
                            </tr>";
        }

        return $tabBody;
    }

    public function recipientsList()
    {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $idsection = $_POST['idsection'];
        $recipientlist = "";

        foreach ($idsection as $v){
            $arrSection = explode('|',$v);

            switch($arrSection[0]){
                case 1:
                    $recipientlist .= $this->makeSectorsRecipList($arrSection[1],$arrSection[0]);
                    break;
                case 2:
                case 3:
                case 6:
                    $year = ($arrSection[0] == 2 || $arrSection[0] == 6) ? date('Y') : date('Y') + 1;
                    $recipientlist .= $this->makeClassesRecipList($arrSection[1],$arrSection[0],$year);
                    break;
                case 4:
                    $recipientlist .= $this->makeTeachersRecipList($arrSection[1],$arrSection[0]);
                    break;
                default:
                    break;
            }
        }

        $aRet = array(
            "tabList" => $recipientlist
        );

        echo json_encode($aRet);

    }

    public function makeClassesRecipList($classID,$type,$year){
        $where = "AND a.year = $year AND a.record_status = 'A' AND a.idturma = $classID";
        $where .= $type == 3 ? " AND a.idstatusenrollment = 1" : '';
        $order = "ORDER BY c.name";
        $ret = $this->dbClasses->getStudentByClass($where,$order);

        if(!$ret){
            if($this->log)
                $this->logIt('Error get Students - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
            return false;
        }

        $tabBody = '';
        while(!$ret->EOF) {
            $name = ucwords(strtolower($ret->fields['name']));
            $tabBody .= "<tr class='recip_{$type}'>
                                <td>
                                     <span>{$name}</span>
                                </td>";

            $retRecip = $this->dbClasses->getParentIDByBind("AND a.idstudent = {$ret->fields['idstudent']} AND e.email != '' AND a.email_sms = 'Y'");

            while(!$retRecip->EOF){
                $tabBody .= "<td>
                                    <div>
                                        <input type='checkbox' name='recipient' value='{$type}|{$ret->fields['idstudent']}|{$retRecip->fields['idparent']}' id='{$type}_{$ret->field['idstudent']}_{$retRecip->field['idparent']}'>&nbsp;
                                        <span>{$retRecip->fields['email']}</span>
                                    </div>
                                </td>";

                $retRecip->MoveNext();
            }

            $tabBody .= "</tr>";

            $ret->MoveNext();
        }

        return $tabBody;
    }

    public function makeSectorsRecipList($sectorID,$type){
        $ret = $this->_personsBySector($sectorID);

        $tabBody = "";
        for($i=0;$i < sizeof($ret);$i++) {
            $name = ucwords($ret[$i]['NoPessoa']);
            $tabBody .= "<tr class='recip_{$type}'>
                                <td>
                                    <span>{$name}</span>
                                </td>
                                <td>
                                    <div>
                                        <input type='checkbox' name='recipient' value='{$type}|{$ret[$i]['CoPessoa']}' id='{$type}_{$ret[$i]['CoPessoa']}'>&nbsp;
                                        <span>{$ret[$i]['postemail']}</span>
                                    </div>
                                </td>
                            </tr>";
        }

        return $tabBody;
    }

    public function makeTeachersRecipList($courseID,$type){
        $ret = $this->_teachersByCourse($courseID);

        $tabBody = "";
        for($i=0;$i < sizeof($ret);$i++) {
            $name = ucwords($ret[$i]['NoPessoa']);
            $tabBody .= "<tr class='recip_{$type}'>
                                <td>
                                    <span>{$name}</span>
                                </td>
                                <td>
                                    <div>
                                        <input type='checkbox' name='recipient' value='{$type}|{$ret[$i]['CoPessoa']}' id='{$type}_{$ret[$i]['CoPessoa']}'>&nbsp;
                                        <span>{$ret[$i]['postemail']}</span>
                                    </div>
                                </td>
                            </tr>";
        }

        return $tabBody;
    }

    public function saveEmailMessage()
    {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $subject = addslashes($_POST['subject']);
        $body = addslashes($_POST['body']);
        $recipient = $_POST['recipient'];
        $sendertitle = "Escola Mario Quintana";
        $aAttachs 	= $_POST["attachments"]; // Attachments
        $aSize = count($aAttachs); // count attachs files
        
        $userData = $this->dbPerson->selectPerson("AND tbp.idperson = {$_SESSION["SES_COD_USUARIO"]}");

        $this->dbEmails->BeginTrans();
        
        $ret = $this->dbEmails->insertSpool($_SESSION["SES_COD_USUARIO"],$sendertitle,$subject,$body);

        if(!$ret){
            if($this->log)
                $this->logIt('Insert E-mail Spool - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            $this->dbEmails->RollbackTrans();
            return false;
        }

        // link attachments to the request
        if($aSize > 0){
            $retAttachs = $this->linkSpoolAttachments($ret,$aAttachs);
            
            if(!$retAttachs['success']){
                if($this->log)
                    $this->logIt("{$retAttachs['message']} - User: {$_SESSION['SES_LOGIN_PERSON']} - program: {$this->program}" ,3,'general',__LINE__);
                $this->dbEmails->RollbackTrans();
                return false;
            }
        }

        foreach ($recipient as $v){
            $arrRecipient = explode('|',$v);

            switch($arrRecipient[0]){
                case 1: //Employees
                case 4: //Teachers
                    $retRecip = $this->_getEmqPersonData($arrRecipient[1]);

                    $recipname = addslashes($retRecip[0]['NoPessoa']);
                    $recipemail = $retRecip[0]['postemail'];
                    $idstudent = 'NULL';
                    $idserver = 1;
                    break;
                case 2:
                case 3:
                case 6:
                    $year = ($arrRecipient[0] == 2 || $arrRecipient[0] == 6) ? date('Y') : date('Y') + 1;
                    $where = "AND a.idparent = ".$arrRecipient[2];
                    $retRecip = $this->dbClasses->getParentIDByBind($where);

                    $recipname = addslashes($retRecip->fields['name']);
                    $recipemail = $retRecip->fields['email'];
                    $idstudent = $arrRecipient[1];
                    $idserver = 1;

                    break;
                default:
                    break;
            }

            $insRecipient = $this->dbEmails->insertSpoolRecipient($ret,$recipname,$recipemail,$idserver);
            if(!$insRecipient){
                $this->dbEmails->RollbackTrans();
                if($this->log)
                    $this->logIt('Insert E-mail Recipient - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                return false;
            }

            $makeEmail = $this->saveTracker($this->idmodule,$userData->fields['email'],$recipemail,$subject,$body);
            if(!$makeEmail){
                $this->dbEmails->RollbackTrans();
                if($this->log)
                    $this->logIt('Make E-mail - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                return false;
            }

            $bindEmail = $this->dbEmails->insertBindEmail($makeEmail,$insRecipient,1);
            if(!$bindEmail){
                $this->dbEmails->RollbackTrans();
                if($this->log)
                    $this->logIt('Insert Bind E-mail/Recipient - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                return false;
            }

            //when recipient is a student's parent
            if($arrRecipient[0] == 2 || $arrRecipient[0] == 3){
                $insRecipientStudent = $this->dbEmails->insertRecipientStudentBind($insRecipient,$idstudent);
                if(!$insRecipientStudent){
                    $this->dbEmails->RollbackTrans();
                    if($this->log)
                        $this->logIt('Insert E-mail Recipient Student - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                    return false;
                }
            }
        }

        $this->dbEmails->CommitTrans();

        $aRet = array(
            "idspool" => $ret,
            "status" => 'ok'
        );

        echo json_encode($aRet);

    }

    public function saveAttachments()
    {
        if (!empty($_FILES) && ($_FILES['file']['error'] == 0)) {

            $fileName = $_FILES['file']['name'];
            $tempFile = $_FILES['file']['tmp_name'];
            $extension = strrchr($fileName, ".");            
            if($this->_externalStorage) {
                $targetPath = $this->_externalStoragePath . '/emq/attachments/' ;
            } else {
                $targetPath = $this->helpdezkPath . '/app/uploads/emq/attachments/' ;
            }

            if(!is_dir($targetPath)) {
                $this->logIt('Directory: '. $targetPath.' does not exists, I will try to create it. - program: '.$this->program ,7,'general',__LINE__);
                if (!mkdir ($targetPath, 0777 )) {
                    $this->logIt('I could not create the directory: '.$targetPath.' - program: '.$this->program ,3,'general',__LINE__);
                    echo json_encode(array("success"=>false,"message"=>"{$this->getLanguageWord('Alert_directory_not_create')}: {$targetPath}"));
                    exit;
                }
            }

            if (!is_writable($targetPath)) {
                $this->logIt('Directory: '. $targetPath.' Is not writable, I will try to make it writable - program: '.$this->program ,7,'general',__LINE__);
                if (!chmod($targetPath,0777)){
                    $this->logIt('Directory: '.$targetPath.' Is not writable !! - program: '.$this->program ,3,'general',__LINE__);
                    echo json_encode(array("success"=>false,"message"=>"{$this->getLanguageWord('Alert_directory_not_writable')}: {$targetPath}"));
                    exit;
                }
            }

            $targetFile =  $targetPath.$fileName;

            if (move_uploaded_file($tempFile,$targetFile)){
                $this->logIt("Move file: {$targetFile} - User: {$_SESSION['SES_LOGIN_PERSON']} - Program: {$this->program} - Method: ". __METHOD__,7,'general',__LINE__);

                if(!file_exists($targetFile)){
                    if($this->log)
                        $this->logIt("The {$targetFile} file has not been moved - User: {$_SESSION['SES_LOGIN_PERSON']} - Program: {$this->program} - Method: ". __METHOD__,3,'general',__LINE__);
                    echo json_encode(array("success"=>false,"message"=>"{$this->getLanguageWord('Alert_failure')}"));
                    exit;
                }

                echo json_encode(array("success"=>true,"message"=>""));
            } else {
                $this->logIt("Can't move file: {$targetFile} - User: {$_SESSION['SES_LOGIN_PERSON']} - Program: {$this->program} - Method: ". __METHOD__,3,'general',__LINE__);
                echo json_encode(array("success"=>false,"message"=>"{$this->getLanguageWord('Alert_failure')}"));
            }

        }else{
            echo json_encode(array("success"=>false,"message"=>"{$this->getLanguageWord('Alert_failure')}"));
        }
        exit;
    }

    public function viewEmail()
    {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $idemail = $_POST['idemail'];

        $ret = $this->dbEmails->getEmail($idemail);
        if(!$ret){
            $this->dbEmails->RollbackTrans();
            if($this->log)
                $this->logIt('Get E-mail Data - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $confStyle = "style='color:{$ret->fields['color']};'";
        $status_fmt = "<span $confStyle>{$ret->fields['description']}</span>";
        $ts_fmt = $ret->fields['ts'] ? date('d/m/Y H:i:s',$ret->fields['ts']) : '';
        $student = $ret->fields['idenrollment'] ? $ret->fields['idenrollment']. " - " .$ret->fields['student_name'] : '';

        $attachs = '';
        if($ret->fields['attachs']){
            $arrAttch = explode(',',$ret->fields['attachs']);
            foreach ($arrAttch as $v){
                $attachs .= "<span class='label label-default'><i class='fa fa-file'></i>   $v</span>";
            }
        }

        $arrRet = array(
            "subject" => $ret->fields['subject'],
            "to" => $ret->fields['recipient_name'].' - '.$ret->fields['recipient_email'],
            "body" => $ret->fields['body'],
            "status" => $status_fmt,
            "sent_date" => $ts_fmt,
            "attachaments" => $attachs,
            "student" => $student
        );

        if($ret->fields['idmandrill']){
            $retLog = $this->dbEmails->getLogMandrill($ret->fields['idmandrill']);
            if(!$retLog){
                if($this->log)
                    $this->logIt('Error to Get E-mail Data - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            }
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
            $arrRet['senderserver'] = 'MANDRILL';
        }else{
            $arrRet['senderserver'] = 'GOOGLE';
        }

        echo json_encode($arrRet);

    }

    private function getFileError($code)
    {
        switch($code){
            case 2:
                $message = $this->getLanguageWord('EMQ_UPLOAD_ERR_FORM_SIZE');
                break;
            case 3:
                $message = $this->getLanguageWord('EMQ_UPLOAD_ERR_PARTIAL');
                break;
            case 4:
                $message = $this->getLanguageWord('EMQ_UPLOAD_ERR_NO_FILE');
                break;
            case 6:
                $message = $this->getLanguageWord('EMQ_UPLOAD_ERR_NO_TMP_DIR');
                break;
            case 7:
                $message = $this->getLanguageWord('EMQ_UPLOAD_ERR_CANT_WRITE');
                break;
            case 8:
                $message = $this->getLanguageWord('EMQ_UPLOAD_ERR_EXTENSION');
                break;
            default:
                $message = $this->getLanguageWord('EMQ_UPLOAD_ERR_INI_SIZE');
                break;
        }

    }

    function deleteSpool()
    {
        $idspool = addslashes($_POST['id']);

        $this->dbEmails->BeginTrans();

        $ret = $this->dbEmails->deleteSpoolByID($idspool);

        if (!$ret) {
            $this->dbEmails->RollbackTrans();
            if($this->log)
                $this->logIt("Can't delete Spool: {$idspool} - program: {$this->program} - method:". __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $aRet = array(
            "status" => 'ok'
        );

        $this->dbEmails->CommitTrans();

        echo json_encode($aRet);

    }

    public function linkSpoolAttachments($spoolID,$aAttachs)
    {
        foreach($aAttachs as $key=>$val){
            $extension = strrchr($val, ".");
            if($this->_externalStorage) {
                $targetPath = $this->_externalStoragePath . '/emq/attachments/' ;
            } else {
                $targetPath = $this->helpdezkPath . '/app/uploads/emq/attachments/' ;
            }
            $targetOld = $targetPath.$val;
            
            $idAtt = $this->dbEmails->saveAttachment($spoolID,$val);
            if (!$idAtt) {
                if($this->log)
                    $this->logIt('Can\'t save into DB - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                return array("success"=>false,"message"=>"Can't link file {$val} to spool # {$spoolID}");
            }

            $targetNew =  $targetPath.$idAtt.$extension;

            if(!rename($targetOld,$targetNew)){
                return array("success"=>false,"message"=>"Can't link file {$val} to spool # {$spoolID}");
            }            
            
        }
        return array("success"=>true,"message"=>"");

    }


}