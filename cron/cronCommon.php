<?php

require_once(HELPDEZK_PATH . '/includes/classes/pipegrep/syslog.php');


/*
 *  Common methods - Information Technology Management Module
 */


class cronCommon extends cronSystem  {


    public static $_logStatus;

    public function __construct()
    {

        parent::__construct();

        /**
         * Here's the models most used
         */
        $this->loadModel('admin/index_model');
        $dbIndex = new index_model();
        $this->dbIndex = $dbIndex;

        $this->loadModel('helpdezk/ticket_model');
        $this->_dbTicket = new ticket_model();

        $this->loadModel('helpdezk/emailconfig_model');
        $this->_dbEmailConfig = new  emailconfig_model();


        $this->getGlobalSessionData();

        // Log settings
        $objSyslog = new Syslog();
        $this->log  = $objSyslog->setLogStatus() ;
        self::$_logStatus = $objSyslog->setLogStatus() ;
        if ($this->log) {
            $objSyslog->SetFacility(18);
            $this->_logLevel = $objSyslog->setLogLevel();
            $this->_logHost = $objSyslog->setLogHost();
            if($this->_logHost == 'remote')
                $this->_logRemoteServer = $objSyslog->setLogRemoteServer();
        }

        $this->_serverApi = $this->_getServerApi();
        $this->databaseNow = ($this->database == 'oci8po' ? 'sysdate' : 'now()') ;

        $this->loadModel('admin/tracker_model');
        $this->dbTracker = $dbTracker = new tracker_model();

        $this->modulename = 'helpdezk' ;
        $this->idmodule =  $this->getIdModule($this->modulename);

        // Tracker Settings
        if($_SESSION['TRACKER_STATUS'] == 1) {
            $this->tracker = true;
        }  else {
            $this->tracker = false;
        }


    }

    function _getServerApi()
    {
       return $this->getConfig('server_api');

    }

    public function getGlobalSessionData()
    {
        session_start();
        // Global Config Data
        $rsConfig = $this->dbIndex->getConfigGlobalData();
        while (!$rsConfig->EOF) {
            $ses = $rsConfig->fields['session_name'];
            $val = $rsConfig->fields['value'];
            $_SESSION[$ses] = $val;
            $rsConfig->MoveNext();
        }

    }

    public function getHelpdezkSessionData()
    {
        session_start();
        // Global Config Data
        $rsConfig = $this->dbIndex->getConfigData();
        while (!$rsConfig->EOF) {
            $ses = $rsConfig->fields['session_name'];
            $val = $rsConfig->fields['value'];
            $_SESSION['hdk'][$ses] = $val;
            $rsConfig->MoveNext();
        }

    }

    public function  _cronSendNotification($transaction=null,$midia='email',$code_request=null,$hasAttachment=null)
    {

        $this->getHelpdezkSessionData();

        if ($midia == 'email'){
            $cron = false;
            $smtp = false;
        }

        if(!$this->log)
            $this->logIt('entrou: ' . $code_request . ' - ' . $transaction . ' - ' . $midia ,7,'general');


        switch($transaction)
        {

            case 'addnote':
                if ($midia == 'email') {
                    if ($hasAttachment){
                        if ($_SESSION['hdk']['SEND_EMAILS'] == '1' &&
                            $_SESSION['hdk']['USER_NEW_NOTE_MAIL'] == '1' &&
                            $_SESSION['hdk']['SES_ATTACHMENT_OPERATOR_NOTE'] == '1') {  // Send e-mail

                            if ( $_SESSION['EM_BY_CRON'] == '1') {
                                $cron = true;
                            } else {
                                $smtp = true;
                            }
                        }

                    } else {
                        if ($_SESSION['hdk']['SEND_EMAILS'] == '1' &&
                            $_SESSION['USER_NEW_NOTE_MAIL'] == '1' ) {  // Send e-mail
                            if ( $_SESSION['EM_BY_CRON'] == '1') {
                                $cron = true;
                            } else {
                                $smtp = true;
                            }

                        }
                    }
                    $messageTo   = 'operator_note';
                    $messagePart = 'Add note in request # ';
                }

                break;

            case 'reopen-ticket':
                if ($midia == 'email') {
                    if ($_SESSION['hdk']['SEND_EMAILS'] == '1' &&
                        $_SESSION['hdk']['REQUEST_REOPENED'] == '1' ) {

                        if ( $_SESSION['EM_BY_CRON'] == '1') {
                            $cron = true;
                        } else {
                            $smtp =  true;
                        }

                        $messageTo   = 'reopen-ticket';
                        $messagePart = 'Reopen request # ';
                    }
                }

                break;

            case 'evaluate-ticket':
                if($midia == 'email'){
                    if ($_SESSION['hdk']['SEND_EMAILS'] == '1' &&
                        $_SESSION['hdk']['EM_EVALUATED']) {

                        if ( $_SESSION['EM_BY_CRON'] == '1') {
                            $cron = true ;
                        } else {
                            $smtp = true;
                        }

                        $messageTo   = 'evaluate-ticket';
                        $messagePart = 'Evaluate request # ';
                    }

                }

                break;

            case 'new-ticket-user':

                if($midia == 'email'){
                    if ($_SESSION['hdk']['SEND_EMAILS'] == '1' &&
                        $_SESSION['hdk']['NEW_REQUEST_OPERATOR_MAIL']) {

                        if ( $_SESSION['EM_BY_CRON'] == '1') {
                            $cron = true ;
                        } else {
                            $smtp = true;
                        }

                        $messageTo   = 'new-ticket-user';
                        $messagePart = 'Insert request # ';
                    }

                }
                break;

            default:
                return false;
        }


        if ($midia == 'email') {
            if ($cron) {
                $this->_dbTicket->saveEmailCron($code_request, $messageTo );
                if($this->log)
                    $this->logIt($messagePart . $code_request . ' - We will perform the method to send e-mail by cron' ,6,'general');
            } elseif($smtp){
                if($this->log)
                    $this->logIt($messagePart . $code_request . ' - We will perform the method to send e-mail' ,6,'general');
                $this->_cronSendEmail($messageTo , $code_request);
            }

        }

        return true ;
    }

    function _getHelpdezkPath()
    {
        $path_parts = pathinfo(dirname(__FILE__));
        return $path_parts['dirname'] ;
    }

    public function returnPhpMailer()
    {

        $phpMailerDir = $this->_getHelpdezkPath() . '/includes/classes/phpMailer/class.phpmailer.php';


        if (!file_exists($phpMailerDir)) {
            die ('ERROR: ' .$phpMailerDir . ' , does not exist  !!!!') ;
        }

        require_once($phpMailerDir);

        $mail = new phpmailer();

        return $mail;
    }

    public function makeLinkOperator($code_request)
    {
        return "<a href='".$this->helpdezkUrl."/helpdezk/hdkTicket/viewrequest/id/".$code_request."' target='_blank'>".$code_request."</a>";
    }

    public function makeLinkUser($code_request)
    {
        return "<a href='".$this->helpdezkUrl."/helpdezk/hdkTicket/viewrequest/id/".$code_request."' target='_blank'>".$code_request."</a>";;
    }

    public function makeLinkOperatorLikeUser($code_request)
    {
        return "<a href='".$this->helpdezkUrl."/helpdezk/hdkTicket/viewrequest/id/".$code_request."' target='_blank'>".$code_request."</a>";
    }

    function _setSendTo($dbEmailConfig,$code_request)
    {
        $sentTo = '';

        $rsGroup = $this->_dbEmailConfig->getGroupInCharge($code_request);

        $inchType = $rsGroup->fields['type'];
        $inchid = $rsGroup->fields['id_in_charge'];

        if ($inchType == 'G') {
            //$this->logIt("Entrou G " . ' - program: ' . $this->program, 7, 'email', __LINE__);
            $grpEmails = $this->_dbEmailConfig->getEmailsfromGroupOperators($inchid);
            while (!$grpEmails->EOF) {
                if (!$sentTo) {
                    $sentTo = $grpEmails->fields['email'];
                    //$this->logIt("Entrou G, sentTo:  " . $sentTo . ' - program: ' . $this->program, 7, 'email', __LINE__);
                } else {
                    $sentTo .= ";" . $grpEmails->fields['email'];
                    //$this->logIt("Entrou G, sentTo:  " . $sentTo . ' - program: ' . $this->program, 7, 'email', __LINE__);
                }
                $grpEmails->MoveNext();
            }
        } else {
            //$this->logIt("NAO entrou G " . ' - program: ' . $this->program, 7, 'email', __LINE__);
            $userEmail = $this->_dbEmailConfig->getUserEmail($inchid);
            $sentTo = $userEmail->fields['email'];
            //$this->logIt("Nao entrou G, sentTo:  " . $sentTo . ' - program: ' . $this->program, 7, 'email', __LINE__);
        }

        return $sentTo ;
    }

    public function _makeNotesTable($code_request)
    {
        $notes = $this->_dbTicket->getRequestNotes($code_request);

        $table = "<table width='100%'  border='0' cellspacing='3' cellpadding='0'>";
        while (!$notes->EOF) {
            $table.= "<tr><td height=28><font size=2 face=arial>";
            $table.= $this->formatDate($notes->fields['entry_date']) . " [" . $notes->fields["name"] . "] " . str_replace(chr(10), "<BR>", strip_tags($notes->fields["description"]));
            $table.= "</font><br></td></tr>";
            $notes->MoveNext();
        }
        $table.= "</table>";
        return $table;
    }

    public function _cronSendEmail($operation, $code_request, $reason = NULL) {

        $mail = $this->returnPhpMailer();
        $this->loadModel('helpdezk/emailconfig_model');
        $dbEmailConfig = new emailconfig_model();

        if (!isset($operation)) {
            print("Email code not provided !!!");
            return false;
        }

        $sentTo = "";
        $arrAttach = array();

        // Common data
        $rsReqData = $this->_dbTicket->getRequestData('WHERE code_request = '. $code_request);
        $EVALUATION = $this->_dbTicket->getEvaluationGiven($code_request);
        $REQUEST = $code_request;
        $SUBJECT = $rsReqData->fields['subject'];
        $REQUESTER = $rsReqData->fields['personname'];
        $RECORD = $this->formatDate($rsReqData->fields['entry_date']);
        $DESCRIPTION = $rsReqData->fields['description'];
        $INCHARGE = $rsReqData->fields['in_charge'];
        $PHONE = $rsReqData->fields['phone'];
        $BRANCH = $rsReqData->fields['branch'];
        $LINK_OPERATOR = $this->makeLinkOperator($code_request);
        $LINK_USER = $this->makeLinkUser($code_request);
        // Notes
        $table = $this->_makeNotesTable($code_request);
        $NT_OPERATOR = $table;

        switch ($operation) {
            // New request
            case "record":

                $templateId = $dbEmailConfig->getEmailIdBySession("NEW_REQUEST_OPERATOR_MAIL");
                if($this->log) {
                    if (empty($templateId)) {
                        $this->logIt("Send email, request # " . $REQUEST . ', do not get Template - program: ' . $this->program, 7, 'email', __LINE__);
                    }
                }

                $rsTemplate = $dbEmailConfig->getTemplateData($templateId);
                $contents = str_replace('"', "'", $rsTemplate->fields['description']) . "<br/>";
                eval("\$contents = \"$contents\";");

                $subject = $rsTemplate->fields['name'];
                eval("\$subject = \"$subject\";");

                $sentTo = $this->_setSendTo($dbEmailConfig,$code_request);

                break;

            case 'assume':
                $templateId = $dbEmailConfig->getEmailIdBySession("NEW_ASSUMED_MAIL");
                if($this->log) {
                    if (empty($templateId)) {
                        $this->logIt("Send email, request # " . $REQUEST . ', do not get Template - program: ' . $this->program, 7, 'email', __LINE__);
                    }
                }
                $rsTemplate = $dbEmailConfig->getTemplateData($templateId);


                $reqdata = $this->dbTicket->getRequestData("WHERE code_request = $code_request");

                $reqEmail = $dbEmailConfig->getRequesterEmail($code_request);
                $sentTo = $reqEmail->fields['email'];
                $typeuser = $reqEmail->fields['idtypeperson'];

                $REQUEST = $code_request;
                $SUBJECT = $reqdata->fields['subject'];
                $REQUESTER = $reqdata->fields['personname'];
                $RECORD = $this->formatDate($reqdata->fields['entry_date']);
                $DESCRIPTION = $reqdata->fields['description'];
                $INCHARGE = $reqdata->fields['in_charge'];
                $PHONE = $reqdata->fields['phone'];
                $BRANCH = $reqdata->fields['branch'];
                $LINK_OPERATOR = $this->makeLinkOperator($code_request);
                if($typeuser == 2)
                    $LINK_USER     = $this->makeLinkUser($code_request);
                else
                    $LINK_USER = $this->makeLinkOperatorLikeUser($code_request);

                $date = date('Y-m-d H:i');
                $ASSUME = $this->formatDate($date);

                $table = $this->_makeNotesTable($code_request);
                $NT_USER = $table;

                $contents = str_replace('"', "'", $rsTemplate->fields['description']) . "<br/>";
                eval("\$contents = \"$contents\";");

                $subject = $rsTemplate->fields['name'];
                eval("\$subject = \"$subject\";");

                break;

            case 'close':
                $templateId = $dbEmailConfig->getEmailIdBySession("FINISH_MAIL");
                if($this->log) {
                    if (empty($templateId)) {
                        $this->logIt("Send email, request # " . $REQUEST . ', do not get Template - program: ' . $this->program, 7, 'email', __LINE__);
                    }
                }
                $rsTemplate = $dbEmailConfig->getTemplateData($templateId);

                //$bdop = new operatorview_model();
                $reqdata = $this->_dbTicket->getRequestData("WHERE code_request = $code_request");

                $reqEmail = $dbEmailConfig->getRequesterEmail($code_request);
                $sentTo = $reqEmail->fields['email'];
                $typeuser = $reqEmail->fields['idtypeperson'];

                $REQUEST = $code_request;
                $SUBJECT = $reqdata->fields['subject'];
                $REQUESTER = $reqdata->fields['personname'];
                $RECORD = $this->formatDate($reqdata->fields['entry_date']);
                $DESCRIPTION = $reqdata->fields['description'];
                $INCHARGE = $reqdata->fields['in_charge'];
                $PHONE = $reqdata->fields['phone'];
                $BRANCH = $reqdata->fields['branch'];
                $date = date('Y-m-d H:i');
                $FINISH_DATE = $this->formatDate($date);

                $this->loadModel('evaluation_model');
                $ev = new evaluation_model();
                $tk = $ev->getToken($code_request);
                $token = $tk->fields['token'];
                if($token)
                    $LINK_EVALUATE =  $this->helpdezkUrl."/helpdezk/evaluate/index/token/".$token;

                $LINK_OPERATOR = $this->makeLinkOperator($code_request);
                if($typeuser == 2)
                    $LINK_USER     = $this->makeLinkUser($code_request);
                else
                    $LINK_USER = $this->makeLinkOperatorLikeUser($code_request);

                $table = $this->_makeNotesTable($code_request);
                $NT_USER = $table;

                $contents = str_replace('"', "'", $rsTemplate->fields['description']) . "<br/>";
                eval("\$contents = \"$contents\";");

                $subject = $rsTemplate->fields['name'];
                eval("\$subject = \"$subject\";");

                break;

            case 'reject':
                $templateId = $dbEmailConfig->getEmailIdBySession("REJECTED_MAIL");
                if($this->log) {
                    if (empty($templateId)) {
                        $this->logIt("Send email, request # " . $REQUEST . ', do not get Template - program: ' . $this->program, 7, 'email', __LINE__);
                    }
                }
                $rsTemplate = $dbEmailConfig->getTemplateData($templateId);

                //$bdop = new operatorview_model();
                $reqdata = $this->dbTicket->getRequestData("WHERE code_request = $code_request");

                $reqEmail = $dbEmailConfig->getRequesterEmail($code_request);
                $sentTo = $reqEmail->fields['email'];

                $typeuser = $reqEmail->fields['idtypeperson'];

                $REQUEST = $code_request;
                $SUBJECT = $reqdata->fields['subject'];
                $REQUESTER = $reqdata->fields['personname'];
                $RECORD = $this->formatDate($reqdata->fields['entry_date']);
                $DESCRIPTION = $reqdata->fields['description'];
                $INCHARGE = $reqdata->fields['in_charge'];
                $PHONE = $reqdata->fields['phone'];
                $BRANCH = $reqdata->fields['branch'];
                $date = date('Y-m-d H:i');
                $REJECTION = $this->formatDate($date);
                $LINK_OPERATOR = $this->makeLinkOperator($code_request);

                if($typeuser == 2)
                    $LINK_USER     = $this->makeLinkUser($code_request);
                else
                    $LINK_USER = $this->makeLinkOperatorLikeUser($code_request);

                $table = $this->_makeNotesTable($code_request);
                $NT_USER = $table;

                $contents = str_replace('"', "'", $rsTemplate->fields['description']) . "<br/>";
                eval("\$contents = \"$contents\";");

                $motivo = "<u>" . $l_eml["lb_motivo_rejeicao"] . "</u> " . $reason;
                $goto = ('/helpdezk/hdkTicket/viewrequest/id/' . $code_request);
                $url = '<a href="' . $this->helpdezkUrl . urlencode($goto) . '">' . $l_eml["link_solicitacao"] . '</a>';

                $subject = $rsTemplate->fields['name'];
                eval("\$subject = \"$subject\";");

                break;

            case 'user_note' :

                $templateId = $dbEmailConfig->getEmailIdBySession("USER_NEW_NOTE_MAIL");

                if($this->log) {
                    if (empty($templateId)) {
                        $this->logIt("Send email, request # " . $REQUEST . ', do not get Template - program: ' . $this->program, 7, 'email', __LINE__);
                    }
                }
                $rsTemplate = $dbEmailConfig->getTemplateData($templateId);
                $contents = str_replace('"', "'", $rsTemplate->fields['description']) . "<br/>";
                eval("\$contents = \"$contents\";");

                $subject = $rsTemplate->fields['name'];
                eval("\$subject = \"$subject\";");


                //
                $FINISH_DATE = $this->formatDate(date('Y-m-d H:i'));
                $LINK_OPERATOR = $this->makeLinkOperator($code_request);

                $reqEmail = $dbEmailConfig->getRequesterEmail($code_request);
                $typeuser = $reqEmail->fields['idtypeperson'];

                if($typeuser == 2)
                    $LINK_USER = $this->makeLinkUser($code_request);
                else
                    $LINK_USER = $this->makeLinkOperatorLikeUser($code_request);


                $sentTo = $reqEmail->fields['email'];


                if($_SESSION['hdk']['SES_ATTACHMENT_OPERATOR_NOTE']){
                    $rsAttachs = $this->dbTicket->getNoteAttchByCodeRequest($code_request);
                    if($rsAttachs) {
                        $att_path = $this->helpdezkPath . '/app/uploads/helpdezk/noteattachments/' ;
                        while (!$rsAttachs->EOF) {
                            $ext = strrchr($rsAttachs->fields['filename'], '.');
                            $attachment_dest = $att_path . $rsAttachs->fields['idnote_attachments'] . $ext;

                            $bus = array("filepath" => $attachment_dest,
                                "filename" => $rsAttachs->fields['filename']);
                            array_push($arrAttach,$bus);

                            $rsAttachs->MoveNext();
                        }
                    }

                }

                break;

            case 'operator_note' :

                $templateId = $dbEmailConfig->getEmailIdBySession("OPERATOR_NEW_NOTE");
                if($this->log) {
                    if (empty($templateId)) {
                        $this->logIt("Send email, request # " . $REQUEST . ', do not get Template - program: ' . $this->program, 7, 'email', __LINE__);
                    }
                }
                $rsTemplate = $dbEmailConfig->getTemplateData($templateId);

                $reqdata = $this->dbTicket->getRequestData($code_request);

                $reqEmail = $dbEmailConfig->getRequesterEmail($code_request);
                $sentTo = $reqEmail->fields['email'];
                $typeuser = $reqEmail->fields['idtypeperson'];

                $FINISH_DATE = $this->formatDate(date('Y-m-d H:i'));
                $LINK_OPERATOR = $this->makeLinkOperator($code_request);
                if($typeuser == 2)
                    $LINK_USER     = $this->makeLinkUser($code_request);
                else
                    $LINK_USER = $this->makeLinkOperatorLikeUser($code_request);

                $contents = str_replace('"', "'", $rsTemplate->fields['description']) . "<br/>";
                eval("\$contents = \"$contents\";");

                $subject = $rsTemplate->fields['name'];
                eval("\$subject = \"$subject\";");

                $sentTo = $this->_setSendTo($dbEmailConfig,$code_request);

                break;

            case 'reopen':

                $templateId = $dbEmailConfig->getEmailIdBySession("REQUEST_REOPENED");
                if($this->log) {
                    if (empty($templateId)) {
                        $this->logIt("Send email, request # " . $REQUEST . ', do not get Template - program: ' . $this->program, 7, 'email', __LINE__);
                    }
                }
                $rsTemplate = $dbEmailConfig->getTemplateData($templateId);
                $contents = str_replace('"', "'", $rsTemplate->fields['description']) . "<br/>";
                eval("\$contents = \"$contents\";");

                $subject = $rsTemplate->fields['name'];
                eval("\$subject = \"$subject\";");

                $sentTo = $this->_setSendTo($dbEmailConfig,$code_request);

                break;

            case "afterevaluate":

                $templateId = $dbEmailConfig->getEmailIdBySession("EM_EVALUATED");

                if($this->log) {
                    if (empty($templateId)) {
                        $this->logIt("Send email, request # " . $REQUEST . ', do not get Template - program: ' . $this->program, 7, 'email', __LINE__);
                    }

                }

                $rsTemplate = $dbEmailConfig->getTemplateData($templateId);
                $contents = str_replace('"', "'", $rsTemplate->fields['description']) . "<br/>";
                eval("\$contents = \"$contents\";");

                $subject = $rsTemplate->fields['name'];
                eval("\$subject = \"$subject\";");

                $sentTo = $this->_setSendTo($dbEmailConfig,$code_request);

                break;

            case "repass":
                $templateId = $dbEmailConfig->getEmailIdBySession("REPASS_REQUEST_OPERATOR_MAIL");
                if($this->log) {
                    if (empty($templateId)) {
                        $this->logIt("Send email, request # " . $REQUEST . ', do not get Template - program: ' . $this->program, 7, 'email', __LINE__);
                    }
                }
                $rsTemplate = $dbEmailConfig->getTemplateData($templateId);

                $reqdata = $this->dbTicket->getRequestData("WHERE code_request = $code_request");

                $REQUEST = $code_request;
                $SUBJECT = $reqdata->fields['subject'];
                $REQUESTER = $reqdata->fields['personname'];
                $RECORD = $this->formatDate($reqdata->fields['entry_date']);
                $DESCRIPTION = $reqdata->fields['description'];
                $INCHARGE = $reqdata->fields['in_charge'];
                $PHONE = $reqdata->fields['phone'];
                $BRANCH = $reqdata->fields['branch'];
                $LINK_OPERATOR = $this->makeLinkOperator($code_request);
                $LINK_USER     = $this->makeLinkUser($code_request);

                $notes = $this->dbTicket->getRequestNotes($code_request);

                $table = "<table width='100%'  border='0' cellspacing='3' cellpadding='0'>";
                while (!$notes->EOF) {
                    $table.= "<tr><td height=28><font size=2 face=arial>";
                    $table.= $this->formatDate($notes->fields['entry_date']) . " [" . $notes->fields["name"] . "] " . str_replace(chr(10), "<BR>", strip_tags($notes->fields["description"]));
                    $table.= "</font><br></td></tr>";
                    $notes->MoveNext();
                }
                $table.= "</table>";

                $NT_OPERATOR = $table;

                $contents = str_replace('"', "'", $rsTemplate->fields['description']) . "<br/>";
                eval("\$contents = \"$contents\";");

                $rsGroup = $dbEmailConfig->getGroupInCharge($code_request);
                $inchType = $rsGroup->fields['type'];
                $inchid = $rsGroup->fields['id_in_charge'];

                if ($inchType == 'G') {
                    $grpEmails = $dbEmailConfig->getEmailsfromGroupOperators($inchid);
                    while (!$grpEmails->EOF) {
                        if (!$sentTo) {
                            $sentTo = $grpEmails->fields['email'];
                        } else {
                            $sentTo .= ";" . $grpEmails->fields['email'];
                        }
                        $grpEmails->MoveNext();
                    }
                } else {
                    $userEmail = $dbEmailConfig->getUserEmail($inchid);
                    $sentTo = $userEmail->fields['email'];
                }

                $subject = $rsTemplate->fields['name'];
                eval("\$subject = \"$subject\";");


                break;

            case "approve":
                $templateId = $dbEmailConfig->getEmailIdBySession("SES_REQUEST_APPROVE");
                if($this->log) {
                    if (empty($templateId)) {
                        $this->logIt("Send email, request # " . $REQUEST . ', do not get Template - program: ' . $this->program, 7, 'email', __LINE__);
                    }
                }
                $rsTemplate = $dbEmailConfig->getTemplateData($templateId);

                //$bdop = new operatorview_model();
                $reqdata = $this->dbTicket->getRequestData("WHERE code_request = $code_request");

                $REQUEST = $code_request;
                $SUBJECT = $reqdata->fields['subject'];
                $REQUESTER = $reqdata->fields['personname'];
                $RECORD = $this->formatDate($reqdata->fields['entry_date']);
                $DESCRIPTION = $reqdata->fields['description'];
                $INCHARGE = $reqdata->fields['in_charge'];
                $PHONE = $reqdata->fields['phone'];
                $BRANCH = $reqdata->fields['branch'];
                $LINK_OPERATOR = $this->makeLinkOperator($code_request);
                $LINK_USER     = $this->makeLinkUser($code_request);

                $notes = $this->dbTicket->getRequestNotes($code_request);

                $table = "<table width='100%'  border='0' cellspacing='3' cellpadding='0'>";
                while (!$notes->EOF) {
                    $table.= "<tr><td height=28><font size=2 face=arial>";
                    $table.= $this->formatDate($notes->fields['entry_date']) . " [" . $notes->fields["name"] . "] " . str_replace(chr(10), "<BR>", strip_tags($notes->fields["description"]));
                    $table.= "</font><br></td></tr>";
                    $notes->MoveNext();
                }
                $table.= "</table>";

                $NT_OPERATOR = $table;

                $contents = str_replace('"', "'", $rsTemplate->fields['description']) . "<br/>";
                eval("\$contents = \"$contents\";");

                $rsGroup = $dbEmailConfig->getGroupInCharge($code_request);
                $inchType = $rsGroup->fields['type'];
                $inchid = $rsGroup->fields['id_in_charge'];

                if ($inchType == 'G') {
                    $grpEmails = $dbEmailConfig->getEmailsfromGroupOperators($inchid);
                    while (!$grpEmails->EOF) {
                        if (!$sentTo) {
                            $sentTo = $grpEmails->Fields('email');
                        } else {
                            $sentTo .= ";" . $grpEmails->Fields('email');
                        }
                        $grpEmails->MoveNext();
                    }
                } else {
                    $userEmail = $bd->getUserEmail($inchid);
                    $sentTo = $userEmail->Fields('email');
                }

                $subject = $rsTemplate->fields['name'];
                eval("\$subject = \"$subject\";");


                break;

            case "operator_reject":
                $templateId = $dbEmailConfig->getEmailIdBySession("SES_MAIL_OPERATOR_REJECT");
                if($this->log) {
                    if (empty($templateId)) {
                        $this->logIt("Send email, request # " . $REQUEST . ', do not get Template - program: ' . $this->program, 7, 'email', __LINE__);
                    }
                }
                $rsTemplate = $dbEmailConfig->getTemplateData($templateId);

                //$bdop = new operatorview_model();
                $reqdata = $this->dbTicket->getRequestData("WHERE code_request = $code_request");

                $grpEmails = $dbEmailConfig->getEmailsfromGroupOperators($_SESSION['hdk']['SES_MAIL_OPERATOR_REJECT_ID']);
                while (!$grpEmails->EOF) {
                    if (!$sentTo) {
                        $sentTo = $grpEmails->Fields('email');
                    } else {
                        $sentTo .= ";" . $grpEmails->Fields('email');
                    }
                    $grpEmails->MoveNext();
                }

                //$typeuser = $reqEmail->fields['idtypeperson'];

                $REQUEST = $code_request;
                $SUBJECT = $reqdata->fields['subject'];
                $REQUESTER = $reqdata->fields['personname'];
                $RECORD = $this->formatDate($reqdata->fields['entry_date']);
                $DESCRIPTION = $reqdata->fields['description'];
                $INCHARGE = $reqdata->fields['in_charge'];
                $PHONE = $reqdata->fields['phone'];
                $BRANCH = $reqdata->fields['branch'];
                $date = date('Y-m-d H:i');
                $REJECTION = $this->formatDate($date);
                $LINK_OPERATOR = $this->makeLinkOperator($code_request);
                /*if($typeuser == 2)
                    $LINK_USER     = $this->makeLinkUser($code_request);
                else
                    $LINK_USER = $this->makeLinkOperatorLikeUser($code_request);*/

                $notes = $this->dbTicket->getRequestNotes($code_request);

                $table = "<table width='100%'  border='0' cellspacing='3' cellpadding='0'>";
                $USER = $notes->fields["name"];
                while (!$notes->EOF) {
                    $table.= "<tr><td height=28><font size=2 face=arial>";
                    $table.= $this->formatDate($notes->fields['entry_date']) . " [" . $notes->fields["name"] . "] " . str_replace(chr(10), "<BR>", strip_tags($notes->fields["description"]));
                    $table.= "</font><br></td></tr>";
                    $notes->MoveNext();
                }
                $table.= "</table>";

                $NT_OPERATOR = $table;

                $contents = str_replace('"', "'", $rsTemplate->fields['description']) . "<br/>";
                eval("\$contents = \"$contents\";");

                $motivo = "<u>" . $l_eml["lb_motivo_rejeicao"] . "</u> " . $reason;

                $subject = $rsTemplate->fields['name'];
                eval("\$subject = \"$subject\";");

                break;

        }

        $customHeader = 'X-hdkRequest: '. $REQUEST;

        $msgLog = "request # ".$REQUEST." - Operation: ".$operation;
        $msgLog2 = "request # ".$REQUEST;

        $params = array("subject" => $subject,
            "contents" => $contents,
            "address" => $sentTo,
            "attachment" => $arrAttach,
            "idmodule" => $this->idmodule,
            "tracker" => $this->tracker,
            "msg" => $msgLog,
            "msg2" => $msgLog2,
            "customHeader" => $customHeader
        );



        $done = $this->sendEmailDefaultNew($params);

        if (!$done) {
            return false ;
        } else {
            return true ;
        }

    }




}