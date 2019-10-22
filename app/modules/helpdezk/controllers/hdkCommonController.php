<?php

require_once(HELPDEZK_PATH . '/includes/classes/pipegrep/syslog.php');

/*
 *  Common methods - Helpdezk Module
 */


class hdkCommon extends Controllers  {

    public static $_logStatus;

    public function __construct()
    {
        parent::__construct();

        $this->loadModel('ticket_model');
        $dbTicket = new ticket_model();
        $this->dbTicket = $dbTicket;

        $this->loadModel('admin/tracker_model');
        $this->dbTracker = $dbTracker = new tracker_model();

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

        // Tracker Settings
        if($_SESSION['TRACKER_STATUS'] == 1) {
            $this->modulename = 'helpdezk' ;
            $this->idmodule = $this->getIdModule($this->modulename) ;
            $this->tracker = true;
        }  else {
            $this->tracker = false;
        }

        $this->modulename = 'helpdezk' ;
        $this->idmodule =  $this->getIdModule($this->modulename);

        $this->loadModel('groups_model');
        $dbGroups = new groups_model();
        $this->dbGroups = $dbGroups;

        $this->loadModel('admin/person_model');
        $dbPerson = new person_model();
        $this->dbPerson = $dbPerson;

    }

    public function _saveNote($aParam)
    {
        $idPerson        = $_SESSION['SES_COD_USUARIO'];
        $codeRequest     = $aParam['code_request'];
        $noteContent     = $aParam['notecontent'];

        $serviceVal = $aParam['serviceval'];
        $public     = $aParam['public'];
        $typeNote   = $aParam['typenote'];
        $callback   = $aParam['callback'];
        $execDate   = $aParam['execdate'];

        $totalminutes   = $aParam['totalminutes'];
        $starthour      = $aParam['starthour'];
        $finishour      = $aParam['finishhour'];
        $hourtype       = $aParam['hourtype'] ;

        $flgopen = $aParam['flgopen'] ;

        $ipAddress = $_SERVER['REMOTE_ADDR'];

        $ins = $this->dbTicket->insertNote($codeRequest, $idPerson, $noteContent, $this->databaseNow, $totalminutes, $starthour, $finishour, $execDate, $hourtype, $serviceVal, $public, $typeNote, $ipAddress, $callback, $flgopen,'NULL' );
        if(!$ins){
            return false;
        }

        $idNote = $this->dbTicket->insertNoteLastID();

        return $idNote;

    }

    public function _makeNavHdk($smarty)
    {
        $smarty->assign('featured_1', true);
        $smarty->assign('lnk_featured_1',$this->helpdezkUrl . '/helpdezk/hdkTicket/index');
        $smarty->assign('featured_label_1', $this->getLanguageWord('Tck_title'));

        $smarty->assign('featured_2', true);
        $smarty->assign('lnk_featured_2','javascript:;');
        $smarty->assign('featured_label_2', $this->getLanguageWord('Tck_new_ticket'));

        if($_SESSION['SES_TYPE_PERSON'] == 3){
            $smarty->assign('featured_3', true);
            $smarty->assign('lnk_featured_3',$this->helpdezkUrl . '/helpdezk/hdkTicket/index/mytickets/1');
            $smarty->assign('featured_label_3', $this->getLanguageWord('My_Tickets'));
        }

        $idPerson = $_SESSION['SES_COD_USUARIO'];
        $listRecords = $this->makeMenuByModule($idPerson,$this->idmodule);
        $moduleinfo = $this->getModuleInfo($this->idmodule);

        //$smarty->assign('displayMenu_1',1);
        $smarty->assign('listMenu_1',$listRecords);
        $smarty->assign('moduleLogo',$this->getHeaderLogoImage());
        $smarty->assign('modulePath',$moduleinfo->fields['path']);

    }

    public function _editRequest($codeRequest)
    {
        return substr($codeRequest,0,4).'-'.substr($codeRequest,4,2).'.'.substr($codeRequest,6,6);
    }

    public function _comboArea()
    {
        $rs = $this->dbTicket->selectArea();
        while (!$rs->EOF) {
            $fieldsID[] = $rs->fields['idarea'];
            $values[]   = $rs->fields['name'];
            $default[] = $rs->fields['default'];
            $rs->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;
        //$arrRet['default'] = $default;

        return $arrRet;
    }

    public function _getIdCoreDefault($table){
        return $this->dbTicket->selectIdCoreDefault($table);
    }

    public function _comboType($idArea)
    {
        $rs = $this->dbTicket->selectType($idArea);
        while (!$rs->EOF) {
            $fieldsID[] = $rs->fields['idtype'];
            $values[]   = $rs->fields['name'];
            $default[] = $rs->fields['default'];
            $rs->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;
        $arrRet['default'] = $default;

        return $arrRet;
    }

    public function _comboTypeHtml($idArea)
    {

        $arrType = $this->_comboType($idArea);
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

    public function _comboItem($idType)
    {
        $rs = $this->dbTicket->selectItem($idType);
        while (!$rs->EOF) {
            $fieldsID[] = $rs->fields['iditem'];
            $values[]   = $rs->fields['name'];
            $default[] = $rs->fields['default'];
            $rs->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;
        $arrRet['default'] = $default;

        return $arrRet;
    }

    public function _comboItemHtml($idType)
    {

        $arrItem = $this->_comboItem($idType);
        $select = '';
        foreach ( $arrItem['ids'] as $indexKey => $indexValue ) {
            if ($arrItem['default'][$indexKey] == 1) {
                $default = 'selected="selected"';
            } else {
                $default = '';
            }
            $select .= "<option value='$indexValue' $default>".$arrItem['values'][$indexKey]."</option>";
        }

        return $select;
    }

    public function _comboService($idItem)
    {
        $rs = $this->dbTicket->selectService($idItem);
        while (!$rs->EOF) {
            $fieldsID[] = $rs->fields['idservice'];
            $values[]   = $rs->fields['name'];
            $default[] = $rs->fields['default'];
            $rs->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;
        $arrRet['default'] = $default;

        return $arrRet;
    }

    public function _comboServiceHtml($idItem)
    {

        $arrService = $this->_comboService($idItem);
        $select = '';
        foreach ( $arrService['ids'] as $indexKey => $indexValue ) {
            if ($arrService['default'][$indexKey] == 1) {
                $default = 'selected="selected"';
            } else {
                $default = '';
            }
            $select .= "<option value='$indexValue' $default>".$arrService['values'][$indexKey]."</option>";
        }

        return $select;
    }

    public function _comboPriority()
    {
        $rs = $this->dbTicket->selectPriorities();
        while (!$rs->EOF) {
            $fieldsID[] = $rs->fields['idpriority'];
            $values[]   = $rs->fields['name'];
            $rs->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;
    }

    public function _comboReason($idService)
    {
        $rs = $this->dbTicket->selectReason($idService);

        if ($rs->RecordCount() == 0) {
            $arrRet['ZERO'] = true;
            return $arrRet;
        }

        while (!$rs->EOF) {
            $fieldsID[] = $rs->fields['idreason'];
            $values[]   = $rs->fields['name'];
            $default[] = $rs->fields['default'];
            $rs->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;
        $arrRet['default'] = $default;

        return $arrRet;
    }

    public function _comboReasonHtml($idService)
    {
        $smarty = $this->retornaSmarty();
        $langVars = $this->getLangVars($smarty);

        $arrReason = $this->_comboReason($idService);

        if ( $arrReason['ZERO'] == true)
            return "<option value='' > ".$langVars['Reason_no_registered']." </option>";

        $select = '';
        foreach ( $arrReason['ids'] as $indexKey => $indexValue ) {
            if ($arrReason['default'][$indexKey] == 1) {
                $default = 'selected="selected"';
            } else {
                $default = '';
            }
            $select .= "<option value='$indexValue' $default>".$arrReason['values'][$indexKey]."</option>";
        }

        return $select;
    }

    public function _comboWay()
    {
        $rs = $this->dbTicket->selectWay();
        while (!$rs->EOF) {
            $fieldsID[] = $rs->fields['idattendanceway'];
            $values[]   = $rs->fields['way'];
            $rs->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;
    }

    public function _comboGender()
    {

        $fieldsID[] = 'M';
        $values[]   = 'Masculino';
        $fieldsID[] = 'F';
        $values[]   = 'Feminino';

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;
    }

    public function _inchargeName($code_request)
    {
        $rsInCharge = $this->dbTicket->getInCharge($code_request);
        return $rsInCharge->fields['name'];
    }

    public function _cutSubject($string,$length,$dots = ' ... ')
    {
        $ret = substr($string,0,$length);
        if(strlen($string) > $length)
            $ret .= $dots;

        return $ret;
    }

    public function _sendEmail($operation, $code_request, $reason = NULL) {

        $mail = $this->returnPhpMailer();
        $this->loadModel('emailconfig_model');
        $dbEmailConfig = new emailconfig_model();


        if (!isset($operation)) {
            print("Email code not provided !!!");
            return false;
        }

        $sentTo = "";
        $arrAttach = array();

        // Common data
        $rsReqData = $this->dbTicket->getRequestData('WHERE code_request = '. $code_request);
        $EVALUATION = $this->dbTicket->getEvaluationGiven($code_request);
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
        $table = $this->makeNotesTable($code_request);
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

                $sentTo = $this->setSendTo($dbEmailConfig,$code_request);

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

                $table = $this->makeNotesTable($code_request);
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

                $table = $this->makeNotesTable($code_request);
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

                $table = $this->makeNotesTable($code_request);
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

                $sentTo = $this->setSendTo($dbEmailConfig,$code_request);

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

                $sentTo = $this->setSendTo($dbEmailConfig,$code_request);

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

                $sentTo = $this->setSendTo($dbEmailConfig,$code_request);

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

        $done = $this->sendEmailDefault($params);

        if (!$done) {
            return false ;
        } else {
            return true ;
        }

    }

    function _saveTracker($idmodule,$mail_sender,$sentTo,$subject,$body)
    {
        $ret = $this->dbTracker->insertEmail($idmodule,$mail_sender,$sentTo,$subject,$body);
        if(!$ret) {
            return false;
        } else {
            return $ret;
        }

    }

    function setTableNotes($code_request)
    {
        $notes = $this->dbTicket->getRequestNotes($code_request);

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

    function setSendTo($dbEmailConfig,$code_request)
    {
        $sentTo = '';

        $rsGroup = $dbEmailConfig->getGroupInCharge($code_request);
        $inchType = $rsGroup->fields['type'];
        $inchid = $rsGroup->fields['id_in_charge'];

        if ($inchType == 'G') {
            //$this->logIt("Entrou G " . ' - program: ' . $this->program, 7, 'email', __LINE__);
            $grpEmails = $dbEmailConfig->getEmailsfromGroupOperators($inchid);
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
            $userEmail = $dbEmailConfig->getUserEmail($inchid);
            $sentTo = $userEmail->fields['email'];
            //$this->logIt("Nao entrou G, sentTo:  " . $sentTo . ' - program: ' . $this->program, 7, 'email', __LINE__);
        }

        return $sentTo ;
    }

    public function makeLinkOperator($code_request)
    {
        return "<a href='".$this->helpdezkUrl."/helpdezk/hdkTicket/viewrequest/id/".$code_request."' target='_blank'>".$code_request."</a>";
    }

    public function makeLinkUser($code_request)
    {
        return "<a href='".$this->helpdezkUrl."/helpdezk/hdkTicket/viewrequest/id/".$code_request."' target='_blank'>".$code_request."</a>";;
    }

    public function makeSentTo($mail,$sentTo)
    {
        //$this->logIt('sentTo: ' . $sentTo,7,'email');
        $jaExiste = array();
        if (preg_match("/;/", $sentTo)) {
            //$this->logIt('Entrou',7,'email');
            $email_destino = explode(";", $sentTo);
            if (is_array($email_destino)) {
                for ($i = 0; $i < count($email_destino); $i++) {
                    // If the e-mail address is NOT in the array, it sends e-mail and puts it in the array
                    // If the email already has the array, do not send again, avoiding duplicate emails
                    if (!in_array($email_destino[$i], $jaExiste)) {
                        $mail->AddAddress($email_destino[$i]);
                        $jaExiste[] = $email_destino[$i];
                    }
                }
            } else {
                //$this->logIt('Entrou ' . $email_destino,7,'email');
                $mail->AddAddress($email_destino);
            }
        } else {
            //$this->logIt('Nao Entrou ' . $sentTo,7,'email');
            $mail->AddAddress($sentTo);
        }
    }

    public function makeNotesTable($code_request)
    {
        $notes = $this->dbTicket->getRequestNotes($code_request);

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

    public function _getNoteAttMaxFiles()
    {
        if (version_compare($this->helpdezkVersionNumber, '1.0.1', '>' )) {
            return 5;
        } else {
            return 1;
        }
    }

    public function _getNoteAcceptedFiles()
    {
        // Images
        $images = '.jpg, .jpeg, .png, .gif';
        // Documents
        $documents = '.pdf, .doc, .docx, .ppt, .pptx, .pps, .ppsx, .odt, .xls, .xlsx, .zip';
        // Audio
        $audio = '.mp3, .m4a, .ogg, .wav';
        // Video
        $video = '.mp4, .m4v, .mov, .wmv, .avi, .mpg, .ogv, .3gp, .3g2';

        return $images .','.$documents.','.$audio.','.$video ;
    }

    public function _getTicketAttMaxFiles()
    {
        if (version_compare($this->helpdezkVersionNumber, '1.0.1', '>' )) {
            return 10;
        } else {
            return 1;
        }
    }

    public function _getTicketAcceptedFiles()
    {
        // Images
        $images = '.jpg, .jpeg, .png, .gif';
        // Documents
        $documents = '.pdf, .doc, .docx, .ppt, .pptx, .pps, .ppsx, .odt, .xls, .xlsx, .zip';
        // Audio
        $audio = '.mp3, .m4a, .ogg, .wav';
        // Video
        $video = '.mp4, .m4v, .mov, .wmv, .avi, .mpg, .ogv, .3gp, .3g2';

        return $images .','.$documents.','.$audio.','.$video ;
    }

    public function oracleDate( $date )
    {
        $date = explode(" ",$date);
        if ( ! strstr( $date[0], '/' ) )
        {
            // If $date in ISO format (yyyy-mm-dd), convert to dd/mm/yyyy
            sscanf( $date[0], '%d-%d-%d', $y, $m, $d );
            return sprintf( '%d/%d/%d', $d, $m, $y )." ".$date[1];
        }
        else
        {
            // if $date is in brasilian format, convert to ISO
            sscanf( $date[0], '%d/%d/%d', $d, $m, $y );
            return sprintf( '%d-%d-%d', $y, $m, $d )." ".$date[1];
        }

        return false;
    }

    public function  _sendNotification($transaction=null,$midia='email',$code_request=null,$hasAttachment=null)
    {
        if ($midia == 'email'){
            $cron = false;
            $smtp = false;
        }

        $this->logIt('entrou: ' . $code_request . ' - ' . $transaction . ' - ' . $midia ,7,'general');


        switch($transaction){

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

                        $messageTo   = 'reopen';
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

                        $messageTo   = 'afterevaluate';
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

                        $messageTo   = 'record';
                        $messagePart = 'Insert request # ';
                    }

                }
                break;
            default:
                return false;
        }


        if ($midia == 'email') {
            if ($cron) {
                $this->dbTicket->saveEmailCron($code_request, $messageTo );
                if($this->log)
                    $this->logIt($messagePart . $code_request . ' - We will perform the method to send e-mail by cron' ,6,'general');
            } elseif($smtp){
                if($this->log)
                    $this->logIt($messagePart . $code_request . ' - We will perform the method to send e-mail' ,6,'general');
                //$this->loadModel('hdkCommon');
                //$hdkCommon = new hdkCommon();
                //$hdkCommon->_sendEmail($messageTo , $code_request);
                $this->_sendEmail($messageTo , $code_request);
            }

        }

        return true ;
    }

    public function _comboRequestUser()
    {
        $order = "ORDER BY person.name";
        $rs = $this->dbTicket->selectUser('',$order);
        while (!$rs->EOF) {
            $fieldsID[] = $rs->fields['idperson'];
            $values[]   = $rs->fields['pname'];
            $rs->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;
    }
    
    public function _comboSource()
    {
        $rs = $this->dbTicket->selectSource();
        while (!$rs->EOF) {
            $fieldsID[] = $rs->fields['idsource'];
            $values[]   = $rs->fields['name'];
            $rs->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;
    }

    public function _comboRepassListHtml($type)
    {

        $arrService = $this->_comboRepassUsers($type);
        $select = '';
        foreach ( $arrService['ids'] as $indexKey => $indexValue ) {
            if ($arrService['default'][$indexKey] == 1) {
                $default = 'selected="selected"';
            } else {
                $default = '';
            }
            $select .= "<option value='$indexValue' $default>".$arrService['values'][$indexKey]."</option>";
        }

        return $select;
    }

    public function _comboRepassUsers($type)
    {
        switch($type){
            case "group":
                $rs = $this->dbTicket->getRepassGroups();
                while (!$rs->EOF) {
                    $fieldsID[] = $rs->fields['idperson'];
                    $values[]   = "(".$rs->fields['level'].") ".$rs->fields['name'];
                    $rs->MoveNext();
                }
                break;
            case "operator":
                $rs = $this->dbTicket->getRepassOperators();
                while (!$rs->EOF) {
                    $fieldsID[] = $rs->fields['idperson'];
                    $values[]   = $rs->fields['name'];
                    $rs->MoveNext();
                }
                break;
            default:
                $rs = $this->dbTicket->getRepassPartners();
                while (!$rs->EOF) {
                    $fieldsID[] = $rs->fields['idperson'];
                    $values[]   = $rs->fields['name'];
                    $rs->MoveNext();
                }
                break;
        }
        

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;
        $arrRet['default'] = $fieldsID[0];

        return $arrRet;
    }

    public function _abilitiesListHtml($type,$rep) {	
        $abilities = "";
		if ($type == 'group') {
            $ret = $this->dbTicket->getAbilityGroup($rep);
            if ($ret->fields) {
                $abilities = "<table class='table'>";
                while (!$ret->EOF) {
                    $abilities .= '<tr><td>'.$ret->fields['service'].'</td></tr>';
                    $ret->MoveNext();
                }
                $abilities .= "</table>";
            } 
        }
		elseif ($type == 'operator') {
            $ret = $this->dbTicket->getAbilityOperator($rep);
            if ($ret->fields) {
                $abilities = "<table class='table'>";
                while (!$ret->EOF) {
                	$abilities .= '<tr><td>'.$ret->fields['service'].'</td></tr>';
                    $ret->MoveNext();
                }
                $abilities .= "</table>";
            }
        }
		return $abilities;
    }

	public function _groupsListHtml($type,$rep) {  
        $groups = "";
        if ($type == 'group') {
            $ret = $this->dbTicket->getGroupOperators($rep);
            
            if ($ret->fields) {
                $groups = "<table class='table'>";
                while (!$ret->EOF) {
                	$groups .= '<tr><td>'.$ret->fields['name'].'</td></tr>';
                    $ret->MoveNext();
                }
                $groups .= "</table>";
            } 
        } elseif ($type == 'operator') {
            $ret = $this->dbTicket->getOperatorGroups($rep);
            if ($ret->fields) {
                $groups = "<table class='table'>";
                while (!$ret->EOF) {
                    $groups .= '<tr><td>'.$ret->fields['pername'].'</td></tr>';
                    $ret->MoveNext();
                }
                $groups .= "</table>";
            }
        }
		return $groups;
    }

    public function _comboTypeNote()
    {
        /*$arrSearch = array("{","}");
        $arrReplace = array("","");
        $rs = $this->dbTicket->getTypeNote("WHERE available = 1");
        while (!$rs->EOF) {
            $descr = str_replace($arrSearch,$arrReplace,$rs->fields['description']);
            $descr = explode(".",$descr);

            $fieldsID[] = $rs->fields['idtypenote'];
            $values[]   = $this->getLanguageWord($descr[2]);
            $rs->MoveNext();
        }*/
        $fieldsID[] = 1;
        $values[]   = $this->getLanguageWord('User');

        $fieldsID[] = 2;
        $values[]   = $this->getLanguageWord('Only_operator');

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;
    }

    public function _comboTypeHour()
    {
        $arrRet['ids'] = array(1,2);
        $arrRet['values'] = array("Normal","Extra");

        return $arrRet;
    }

    public function _comboAuxOperators($code_request,$in_notin)
    {
        $rs = $this->dbTicket->getOperatorAuxCombo($code_request,$in_notin);
        while (!$rs->EOF) {
            $fieldsID[] = $rs->fields['idperson'];
            $values[]   = $rs->fields['name'];
            $rs->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;
        $arrRet['default'] = $fieldsID[0];

        return $arrRet;
    }

    public function _comboOperatorGroups($idperson)
    {
        $rs = $this->dbTicket->getOperatorGroups($idperson);
        while (!$rs->EOF) {
            $fieldsID[] = $rs->fields['idpergroup'];
            $values[]   = $rs->fields['pername'];
            $rs->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;
    }

    public function _dif_date($start, $end){
        $StartDate = getdate(strtotime($start));
        $EndDate = getdate(strtotime($end));
        $Dif = ($EndDate[0] - $StartDate[0]) / 60;
        return number_format($Dif, 0, '', '');
    }

    public function makeLinkOperatorLikeUser($code_request)
    {
        return "<a href='".$this->helpdezkUrl."/helpdezk/hdkTicket/viewrequest/id/".$code_request."' target='_blank'>".$code_request."</a>";
    }

    public function _comboAttWayHtml()
    {
        $smarty = $this->retornaSmarty();
        $langVars = $this->getLangVars($smarty);

        $arrWay = $this->_comboWay();

        $select = '';
        foreach ( $arrWay['ids'] as $indexKey => $indexValue ) {
            if ($arrWay['default'][$indexKey] == 1) {
                $default = 'selected="selected"';
            } else {
                $default = '';
            }
            $select .= "<option value='$indexValue' $default>".$arrWay['values'][$indexKey]."</option>";
        }

        return $select;
    }

    public function _comboTypeExpireDate()
    {
        $arrRet['ids'] = array(0,1,2,3,4);
        $arrRet['values'] = array($this->getLanguageWord('Expire_date'),$this->getLanguageWord('grd_expiring'),$this->getLanguageWord('grd_expiring_today'),$this->getLanguageWord('grd_expired'),$this->getLanguageWord('grd_expired_n_assumed'));

        return $arrRet;
    }

    public function _comboTypeView()
    {
        $arrRet['ids'] = array(1,2,3);
        $arrRet['values'] = array($this->getLanguageWord('grd_show_all'),$this->getLanguageWord('grd_show_only_mine'),$this->getLanguageWord('grd_show_group'));

        return $arrRet;
    }

    public function _checkapproval(){

        $iduser = $_SESSION['SES_COD_USUARIO'];
        $where = "";
        if ($this->getConfig('license') == '200701006') {
            $where .= " AND iditem <> 124";
        }

        if($_SESSION['hdk']['SES_OPEN_NEW_REQUEST']){

            if($_SESSION['SES_LICENSE'] == 201301014 && $_SESSION['SES_COD_EMPRESA'] == 93){ //SE FOR COINPEL E EMPRESA "SANEP"
                $reqs = $this->dbTicket->getWaitingApprovalRequestsCountByDate($iduser);
                $total = 0;
                while (!$reqs->EOF) {
                    $dt_req = strtotime("+2 day", strtotime($reqs->fields['dt_approval']));
                    $now = strtotime(date("Y-m-d H:i:s"));
                    if($dt_req <= $now){
                        $total++;
                    }
                    $reqs->MoveNext();
                }
            }else{
                $total = $this->dbTicket->getWaitingApprovalRequestsCount($where,$iduser);
            }
            return $total;

        }else{
            return 0;
        }
    }

    public function _comboGroups($where=NULL,$order=NULL,$limit=NULL)
    {
        $rs = $this->dbGroups->selectGroup($where,$order,$limit);

        if($rs->RecordCount() > 0){
            $fieldsID[] = $rs->fields[''];
            $values[]   = $rs->fields[''];
            while (!$rs->EOF) {
                $fieldsID[] = $rs->fields['idgroup'];
                $values[]   = $rs->fields['name'];
                $rs->MoveNext();
            }
        }else{
            $fieldsID[] = "";
            $values[]   = $this->getLanguageWord('No_result');
        }


        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;
    }

    public function _comboCompanies()
    {
        $rs = $this->dbPerson->getErpCompanies("WHERE idtypeperson IN (4) AND status = 'A'","ORDER BY name ASC");
        while (!$rs->EOF) {
            $fieldsID[] = $rs->fields['idcompany'];
            $values[]   = $rs->fields['name'];
            $rs->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;
    }

    public function _getTicketAttMaxFileSize()
    {
        return ini_get(upload_max_filesize);
    }

    public function _comboServerType()
    {
        $arrRet['ids'] = array("","pop","imap","pop-gmail","imap-gmail","imap-ssl");
        $arrRet['values'] = array($this->getLanguageWord('Select'),"POP","IMAP","POP - Gmail","IMAP - Gmail");

        return $arrRet;
    }

    public function _comboLoginLayout()
    {
        $arrRet['ids'] = array("","U","E");
        $arrRet['values'] = array($this->getLanguageWord('Select'),$this->getLanguageWord('User'),"Full E-mail");

        return $arrRet;
    }

    public function _comboDepartment($idcompany)
    {
        $rs = $this->dbPerson->getDepartment("WHERE idperson = $idcompany AND status = 'A'","ORDER BY name ASC");
        while (!$rs->EOF) {
            $fieldsID[] = $rs->fields['iddepartment'];
            $values[]   = $rs->fields['name'];
            $rs->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;
    }

    public function _comboDepartmentHtml($companyID)
    {

        $arrDepartment = $this->_comboDepartment($companyID);
        $select = '';
        foreach ( $arrDepartment['ids'] as $indexKey => $indexValue ) {
            $select .= "<option value='$indexValue'>".$arrDepartment['values'][$indexKey]."</option>";
        }

        return $select;
    }

}