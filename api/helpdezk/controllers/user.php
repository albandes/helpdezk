<?php
class user extends apiController
{

    public function __construct()
    {

        parent::__construct();

        $this->_log = true ;
        $this->_logFile  = $this->getApiLog();

    }

    public function get_info($arrParam)
    {

        if($this->_log)
            $this->log("Remote Addr: " . $this->_getIpAddress() . " - Run /api/user/info " , 'INFO', $this->_logFile);

        $token = $arrParam['token'];
        $idPerson = $this->_isLoged($arrParam['token']);


        if(!$idPerson) {
            if ($this->_log)
                $this->log("Remote Addr: " . $this->_getIpAddress() . " - /api/user/info - Invalid token, user not loged.", 'ERROR', $this->_logFile);
            return array('error' => 'Invalid token, user not loged.');
        }

        if($this->_log)
            $this->log("Remote Addr: " . $this->_getIpAddress() . " - /api/user/info - Login: " . $this->_getLoginByToken($token) , 'INFO', $this->_logFile);


        $rsPerson = $this->_getPersonData("tbp.idperson = $idPerson");

        if (!$rsPerson) {
            if ($this->_log)
                $this->log("Remote Addr: " . $this->_getIpAddress() . " - /api/user/info - Error when get person data .", 'ERROR', $this->_logFile);
            return array('error' => 'Error when get person data.');
        }

        $check = array(
            "id" => $rsPerson->fields['idperson'],
            "name" => $rsPerson->fields['name'],
            "email" => $rsPerson->fields['email'],
            "status" => $rsPerson->fields['status'],
            "phone_number" => $rsPerson->fields['telephone'],
            "cel_phone" => $rsPerson->fields['status'],
            "company" => $rsPerson->fields['company'],
            "city" => $rsPerson->fields['city'],
            "state" => $rsPerson->fields['state'],
            "state_abbr" => $rsPerson->fields['state_abbr'],
            "country" => $rsPerson->fields['country']


        );


        return $check;


    }

    public function get_requests($arrParam)
    {

        if($this->_log)
            $this->log("Remote Addr: " . $this->_getIpAddress() . " - Run /api/user/requests " , 'INFO', $this->_logFile);

        $idPerson = $this->_isLoged($arrParam['token']);
        if(!$idPerson) {
            if ($this->_log)
                $this->log("Remote Addr: " . $this->_getIpAddress() . " - /api/user/requests - Invalid token, user not loged.", 'ERROR', $this->_logFile);
            return array('error' => 'Invalid token, user not loged.');
        }

        $aQuery = array(
            'idStatus'      => $arrParam['idStatus'],
            'page'          => $arrParam['page'],
            'sortName'      => $arrParam['sortName'],
            'sortOrder'     => $arrParam['sortOrder'],
            'limit'         => $arrParam['limit'],
            'idPerson'      => $idPerson
        );

        $data['rows'] = $this->_getRequest($aQuery);
        return $data;


    }

    public function post_addnote($arrParam)
    {


        if($this->_log)
            $this->log("Remote Addr: " . $this->_getIpAddress() . " - Run /api/user/addnote - Code: " . $arrParam['code'] , 'INFO', $this->_logFile);

        $idPerson = $this->_isLoged($arrParam['token']);
        if(!$idPerson) {
            if ($this->_log)
                $this->log("Remote Addr: " . $this->_getIpAddress() . " - /api/user/addnote - Invalid token, user not loged.", 'ERROR', $this->_logFile);
            return array('error' => 'Invalid token, user not loged.');
        }
        if(!$this->_isRequest($arrParam['code'])){
            if ($this->_log)
                $this->log("Remote Addr: " . $this->_getIpAddress() . " - /api/user/addnote - Request not exist.", 'ERROR', $this->_logFile);
            return array('error' => 'Request not exist.');

        }
        if (empty($arrParam['note'])){
            if ($this->_log)
                $this->log("Remote Addr: " . $this->_getIpAddress() . " - /api/user/addnote - Empty note.", 'ERROR', $this->_logFile);
            return array('error' => 'Empty note.');
        }

        $codeRequest    = $arrParam['code'] ;
        $note           = $arrParam['note'] ;
        $date           = 'NOW()';
        $minutes        = 0;
        $startHour      = '0';
        $finishHour     = '0';
        $execDate       = '0000-00-00 00:00:00';
        $hourType       = 0;
        $serviceVal     = 0;
        $public         = '1';
        $idtype         = '1';
        $ipAddress      = $this->_getIpAddress();
        $callBack       = '0';
        $idAnexo        = 'NULL';
        $flgOpen        = 'NULL';

        $ret = $this->_insertNote($codeRequest, $idPerson, $note, $date, $minutes, $startHour, $finishHour, $execDate, $hourType, $serviceVal, $public, $idtype, $ipAddress, $callBack, $flgOpen,$idAnexo);
        if (!$ret){
            if ($this->_log)
                $this->log("Remote Addr: " . $this->_getIpAddress() . " - /api/user/addnote - Error inserting into database.", 'ERROR', $this->_logFile);
            return array('error' => 'Error inserting into database.');
        }

        $idNote = $this->_getLastInsertId('ticket_model','hdk_tbnote', 'idnote' );


        // https://stackoverflow.com/questions/28185300/how-to-send-multiple-files-in-postman-restful-web-service

        if (!empty($_FILES)) {

            foreach ($_FILES["attachment"]["error"] as $key => $error) {
                if ($error == UPLOAD_ERR_OK) {
                    $fileName = $_FILES['attachment']['name'][$key];
                    $tempFile = $_FILES['attachment']['tmp_name'][$key];
                    $extension = strrchr($fileName, ".");
                    if($this->_externalStorage) {
                        $targetPath = $this->_externalStoragePath . '/helpdezk/noteattachments/' ;
                    } else {
                        $targetPath = $this->_helpdezkPath . 'app/uploads/helpdezk/noteattachments/';
                    }

                    if(!is_dir($targetPath))
                        mkdir ($targetPath, 0777 );

                    $idNoteAttachments = $this->_saveNoteAttachment($idNote,$fileName);
                    if (!$idNoteAttachments){
                        if ($this->_log)
                            $this->log("Remote Addr: " . $this->_getIpAddress() . " - /api/user/addnote - Error inserting attachments into database.", 'ERROR', $this->_logFile);
                        return array('error' => 'Error inserting attachments into database.');
                    }

                    $targetFile =  $targetPath.$idNoteAttachments.$extension;

                    if (!move_uploaded_file($tempFile,$targetFile)){
                        if ($this->_log)
                            $this->log("Remote Addr: " . $this->_getIpAddress() . " - /api/user/addnote - Error move attachment, file: " . $tempFile . " , dest: " . $targetFile, 'ERROR', $this->_logFile);
                        return array('error' => 'Error move attachment.');
                    }

                }
            }
        }


        if ($this->_log)
            $this->log("Remote Addr: " . $this->_getIpAddress() . " - /api/user/addnote - Note inserted successfuly.", 'INFO', $this->_logFile);

        return array('success' => 'Note inserted successfuly.');



    }

    public function saveNoteAttach($idNote)
    {

        $idNote = $_POST['idNote'];

        if (!empty($_FILES)) {
            $fileName = $_FILES['file']['name'];
            $tempFile = $_FILES['file']['tmp_name'];
            $extension = strrchr($fileName, ".");
            if($this->_externalStorage) {
                $targetPath = $this->_externalStoragePath . '/helpdezk/noteattachments/' ;
            } else {
                $targetPath = $this->helpdezkPath . '/app/uploads/helpdezk/noteattachments/';
            }

            //$targetPath = $this->helpdezkPath . '/app/uploads/helpdezk/noteattachments/' ;

            $idNoteAttachments = $this->dbTicket->saveNoteAttachment($idNote,$fileName);

            $targetFile =  $targetPath.$idNoteAttachments.$extension;

            if (move_uploaded_file($tempFile,$targetFile)){
                $this->logIt('Add attachment #  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,7,'general',__LINE__);
                return 'OK';
            } else {
                $this->logIt('Error attachment #  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,3,'general',__LINE__);
                return false;
            }

        }



    }

    public function get_request($arrParam)
    {
        if($this->_log)
            $this->log("Remote Addr: " . $this->_getIpAddress() . " - Run /api/user/request - Code: " . $arrParam['code'] , 'INFO', $this->_logFile);

        $idPerson = $this->_isLoged($arrParam['token']);
        if(!$idPerson) {
            if ($this->_log)
                $this->log("Remote Addr: " . $this->_getIpAddress() . " - /api/user/request - Invalid token, user not loged.", 'ERROR', $this->_logFile);
            return array('error' => 'Invalid token, user not loged.');
        }

        if (!function_exists('mb_convert_encoding')){
            if($this->_log)
                $this->log("Remote Addr: " . $this->_getIpAddress() . " - PHP does not have mb_convert_encoding function " , 'ERROR', $this->_logFile);
            return array('error'=> 'PHP does not have mb_convert_encoding function, please install php-mbstring');
        }

        $idRequest = $arrParam['code'];
        $notes = $this->_getRequestNotes($idRequest);

        if ($notes->RecordCount() == 0) {
            $note[] = array();
        } else {
            while (!$notes->EOF) {
                if($notes->fields['idtype'] != '2'){

                    // Note Attachments
                    $rsNoteAttach = $this->_getRequestNoteAttachment($notes->fields['idnote']);
                    if ($rsNoteAttach->RecordCount() > 0) {
                        while(!$rsNoteAttach->EOF){
                            $idNoteAttach = $rsNoteAttach->fields['idnote_attachments'] ;
                            $filename = $this->_getTicketFile($idNoteAttach,'note');
                            $url = $this->_helpdezkUrl . "/app/uploads/helpdezk/noteattachments/". $idNoteAttach . strrchr($filename, '.');
                            $noteAttachments[] = array( "filename" => $filename, "attach_url" => $url );
                            $rsNoteAttach->MoveNext();
                        }
                    } else {
                        $noteAttachments = array();
                    }

                    $desc = strip_tags(html_entity_decode(mb_convert_encoding($notes->fields['description'], 'UTF-8', 'UTF-8'), ENT_QUOTES, 'UTF-8'));
                    $note[] = array(
                        "note_description"  => $desc,
                        "idperson"          => $notes->fields['idperson'],
                        "person_name"       => $this->_getPersonName($notes->fields['idperson']),
                        "ip_address"        => $notes->fields['ip_adress'],
                        "date"              => $notes->fields['entry_date'],
                        "date_fmtd"         => $this->formatDateHour($notes->fields['entry_date']),
                        //"time"              => $notes->fields['entry_date'],
                        //"time_fmtd"         => $this->formatHour($notes->fields['entry_date']),
                        "execution_date"    => $this->formatDate($notes->fields['execution_date']),
                        "service_time"      => $notes->fields['minutes'],
                        "service_time_fmtd" => $notes->fields['diferenca'],
                        "start_hour"        => $notes->fields['start_hour'],

                        "finish_hour"       => $notes->fields['finish_hour'],

                        "callback"          => $notes->fields['callback'],
                        "hour_type"         => $notes->fields['hour_type'],
                        "flag_opened"       => $notes->fields['flag_opened'],
                        "attachments"       => $noteAttachments

                    );
                }
                $notes->MoveNext();
            }
        }

        $req = $this->_getRequestData($idRequest);

        $questions = $evaluate = array();
        $arrSession = $this->_getSessionValues($idPerson);

        if($req->fields['idstatus'] == 4){
            if( $arrSession['SES_EVALUATE']) {

                $questions = $this->_getQuestions();

                $evaluate[] = array(
                    "question" => $this->_getLangVar('Approve_text'),
                    "answers"  => array(
                        array(
                            "value"     => "A",
                            "name"      => $this->_getLangVar('Approve_yes'),
                            "icon"      => null,
                            "checked"   => 1
                        ),
                        array(
                            "value"     => "N",
                            "name"      => $this->_getLangVar('Approve_no'),
                            "icon"      => null,
                            "checked"   => 0
                        ),
                        array(
                            "value"     => "O",
                            "name"      => $this->_getLangVar('Approve_obs'),
                            "icon"      => null,
                            "checked"   => 0
                        )
                    )
                );

                while (!$questions->EOF) {
                    $idquestion = $questions->fields['idquestion'];
                    $question   = $questions->fields['question'];
                    $answers    = $this->_getAnswers($idquestion);

                    $ans = null;
                    while (!$answers->EOF) {
                        $ans[] = array(
                            "value"     => $answers->fields['idevaluation'],
                            "name"      => $answers->fields['name'],
                            "icon"      => $this->_helpdezkUrl."/app/uploads/icons/".$answers->fields['icon_name'],
                            "checked"   => $answers->fields['checked']
                        );
                        $answers->MoveNext();
                    }

                    $evaluate[] = array(
                        "question" => $question,
                        "answers"  => $ans
                    );

                    $questions->MoveNext();
                }

            }
        }

        // Attach

        $rsAttach = $this->_getRequestAttachment($idRequest);
        if ($rsAttach->fields) {
            $hasAttach = 1;
            while (!$rsAttach->EOF) {
                $filename = $rsAttach->fields['file_name'];
                $ext = strrchr($filename, '.');
                $idAttach = $rsAttach->fields['idrequest_attachment'];
                $attachments[] = array( "filename"   => $filename ,
                                        "attach_url" =>  $this->_helpdezkUrl .  "/app/uploads/helpdezk/attachments/" . $idAttach . $ext
                                      );
                $rsAttach->MoveNext();
            }

        } else {
            $attachments = array();
        }

        $description = strip_tags(html_entity_decode(mb_convert_encoding($req->fields['description'], 'UTF-8', 'UTF-8'), ENT_QUOTES, 'UTF-8'));
        $data = array(
            "code"              => $idRequest,
            "code_fmt"          => $this->_editRequest($idRequest),
            "status"            => $req->fields['status'],
            "idstatus"          => $req->fields['idstatus'],
            "entry_date"        => $this->formatDateHour($req->fields['entry_date']),
            "in_charge"         => $req->fields['in_charge'],
            "subject"           => $req->fields['subject'],
            "description"       => $description,
            "idperson"          => $idPerson,
            "person_name"       => $req->fields['personname'],
            "possibletoreopen"  => $this->_getEspecificValueSession('SES_IND_REOPEN'),
            "evaluate"          => $evaluate,
            "idarea"            => $req->fields['idarea'],
            "area_name"         => $req->fields['AREA'],
            "idtype"            => $req->fields['idtype'],
            "type_name"         => $req->fields['type'],
            "iditem"            => $req->fields['iditem'],
            "item_name"         => $req->fields['item'],
            "idservice"         => $req->fields['idservice'],
            "service_name"      => $req->fields['service'],
            "idreason"          => $req->fields['idreason'],
            "reason_name"       => $req->fields['reason'],
            "attachments"       => $attachments,
            "notes"             => $note
        );
        return $data;
    }

    public function post_saverequest($arrParam)
    {

        if($this->_log)
            $this->log("Remote Addr: " . $this->_getIpAddress() . " - Run /api/user/saverequest", 'INFO', $this->_logFile);

        $idPerson = $this->_isLoged($arrParam['token']);

        if(!$idPerson) {
            if ($this->_log)
                $this->log("Remote Addr: " . $this->_getIpAddress() . " - /api/user/saverequest - " . $this->_getLangVar('API_Error_token'), 'ERROR', $this->_logFile);
            return array('error' => $this->_getLangVar('API_Error_token'));
        }

        $arrParam['idperson']       = $idPerson;
        $arrParam['num_serial']     = NULL;
        $arrParam['num_os']         = NULL;
        $arrParam['num_tag']        = NULL;

        $ret = $this->_saveRequest($arrParam);

        if(!$ret) {
            if ($this->_log)
                $this->log("Remote Addr: " . $this->_getIpAddress() . " - /api/user/saverequest - Error processing request save.", 'ERROR', $this->_logFile);
            return array('error' => 'Error processing save request .');

        } else {
            if ($this->_log)
                $this->log("Remote Addr: " . $this->_getIpAddress() . " - /api/user/saverequest - Request included successfully: ".$ret['coderequest'], 'INFO', $this->_logFile);
            return array('success'=> $ret);
        }


    }

    public function post_changepassword($arrParam)
    {
        if($this->_log)
            $this->log("Remote Addr: " . $this->_getIpAddress() . " - Run /api/user/changepassword", 'INFO', $this->_logFile);


        $idPerson = $this->_isLoged($arrParam['token']);

        if(!$idPerson) {
            if ($this->_log)
                $this->log("Remote Addr: " . $this->_getIpAddress() . " - /api/user/change - " . $this->_getLangVar('API_Error_token'), 'ERROR', $this->_logFile);
            return array('error' => $this->_getLangVar('API_Error_token'));
        }

        $change = $this->_changePassword($idPerson, $arrParam['new_password']);

        if (!$change) {
            if($this->_log)
                $this->log("Remote Addr: " . $this->_getIpAddress() . " - Error: Error recording the new password." , 'ERROR', $this->_logFile);
            $check['error'] = 'Error recording the new password.';
            return $check;
        }

        return array('success'=> array("id" => $idPerson, "message" => "Password changed successfully."));

    }

    public function get_lostpassword($arrParam) {

        if($this->_log)
            $this->log("Remote Addr: " . $this->_getIpAddress() . " - Run /api/user/lostpassword " , 'INFO', $this->_logFile);

        if ( empty($arrParam['login']) ) {
            if($this->_log)
                $this->log("Remote Addr: " . $this->_getIpAddress() . " - Error: Username is empty" , 'ERROR', $this->_logFile);
            $check['error'] = 'Username must be informed.';
            return $check;
        }

        $login = $arrParam['login'];

        $dbIndex = new index_model();
        $logintype = $dbIndex->getTypeLogin($login);
        $idPerson = $dbIndex->getIdPerson($login);

        if($idPerson == 1) {
            if($this->_log)
                    $this->log("Remote Addr: " . $this->_getIpAddress() . " - Error: Administrator user cannot change password" , 'ERROR', $this->_logFile);
            $check['error'] = 'Administrator user cannot change password.';
            return $check;
        }

        if ($logintype->fields)
        {

            if ($logintype->fields['idtypelogin'] == 1 ) // POP
            {
                if($this->_log)
                    $this->log("Remote Addr: " . $this->_getIpAddress() . " - Error: System configured for POP authentication, it is not possible to create a new password." , 'ERROR', $this->_logFile);
                $check['error'] = 'System configured for POP authentication, it is not possible to create a new password.';
                return $check;
            }

            if ($logintype->fields['idtypelogin'] == 2 ) // AD
            {
                if($this->_log)
                    $this->log("Remote Addr: " . $this->_getIpAddress() . " - Error: System configured for AD authentication, it is not possible to create a new password." , 'ERROR', $this->_logFile);
                $check['error'] = 'System configured for AD authentication, it is not possible to create a new password.';
                return $check;
            }

            $pass = $this->_generateRandomPassword(8, false, true, false);

            $password = md5($pass);

            $change = $this->_changePassword($idPerson, $password);
            if (!$change) {
                if($this->_log)
                    $this->log("Remote Addr: " . $this->_getIpAddress() . " - Error: Error generating/recording the new password." , 'ERROR', $this->_logFile);
                $check['error'] = 'Error generating/recording the new password.';
                return $check;
            }

            $smarty = $this->retornaSmarty();

            $subject = $smarty->getConfigVars('Lost_password_subject');
            $body = $smarty->getConfigVars('Lost_password_body');
            $log_text = $smarty->getConfigVars('Lost_password_log');

            eval("\$body = \"$body\";");

            $address = $dbIndex->getEmailPerson($login);

            $params = array("subject" => $subject,
                "contents"      => $body,
                "address"       => $address,
                "customHeader"  => '',
                "sender"        => '',
                "sender_name"   => '',
                "attachment"    => array(),
                "tracker"       => '',
                "msg"           => $log_text,
                "msg2"          => $log_text
            );

            $done = $this->sendEmailDefault($params);

            if (!$done) {
                $check['error'] = 'Did not send email.';
            } else {
                return array('success'=> array("id" => $idPerson, "message" => "Password sent by email."));
            }
        }
        else
        {
            if($this->_log)
                $this->log("Remote Addr: " . $this->_getIpAddress() . " - Error: User Unknown: " . $login , 'INFO', $this->_logFile);
            $check['error'] = 'User Unknown.';
            return $check;
        }


    }

    /*
     * Old routes
     */

    public function post_requests_all($arrParam)
    {
        if($this->_log)
            $this->log("Remote Addr: " . $this->_getIpAddress() . " - Run /api/user/requests_all " , 'INFO', $this->_logFile);

        $idPerson = $this->_isLoged($arrParam['token']);
        if(!$idPerson) {
            if ($this->_log)
                $this->log("Remote Addr: " . $this->_getIpAddress() . " - /api/user/requests_all - Invalid token, user not loged.", 'ERROR', $this->_logFile);
            return array('error' => 'Invalid token, user not loged.');
        }

        $aQuery = array(
                            'filterType'    => null,
                            'whereStatus'   => '',
                            'sortName'      => 'code_request',
                            'sortOrder'     => 'desc',
                            'limit'         => null,
                            'idPerson'      => $idPerson
                        );
        $rs = $this->_getRequest($aQuery);
        $rows = array();
        while (!$rs->EOF) {
            $rows[] = array(
                "id" => $rs->fields['code_request'],
                "subject" => addslashes($rs->fields['subject']),
                "idstatus" => $rs->fields['idstatus']
            );
            $rs->MoveNext();
        }

        $data['rows'] = $rows;
        return $data;
    }

    public function post_requestsbystatus($arrParam)
    {
        if($this->_log)
            $this->log("Remote Addr: " . $this->_getIpAddress() . " - Run /api/user/requestsbystatus " , 'INFO', $this->_logFile);

        $idPerson = $this->_isLoged($arrParam['token']);
        if(!$idPerson) {
            if ($this->_log)
                $this->log("Remote Addr: " . $this->_getIpAddress() . " - /api/user/requestsbystatus - Invalid token, user not loged.", 'ERROR', $this->_logFile);
            return array('error' => 'Invalid token, user not loged.');
        }
        $idStatus = $arrParam['status'];
        if(!$idStatus) {
            return array('error' => 'Invalid status id or empty status.');
        }
        $aQuery = array(
            'filterType'    => null,
            'whereStatus'   => ' AND stat.idstatus_source = ' . $idStatus,
            'sortName'      => 'code_request',
            'sortOrder'     => 'desc',
            'limit'         => null,
            'idPerson'      => $idPerson
        );
        $rs = $this->_getRequest($aQuery);
        $rows = array();
        while (!$rs->EOF) {
            $rows[] = array(
                "id" => $rs->fields['code_request'],
                "subject" => addslashes($rs->fields['subject']),
                "idstatus" => $rs->fields['idstatus']
            );
            $rs->MoveNext();
        }

        $data['rows'] = $rows;
        return $data;
    }

    public function post_showrequest($arrParam)
    {
        if($this->_log)
            $this->log("Remote Addr: " . $this->_getIpAddress() . " - Run /api/user/showrequest - Code: " . $arrParam['code'] , 'INFO', $this->_logFile);

        $idPerson = $this->_isLoged($arrParam['token']);
        if(!$idPerson) {
            if ($this->_log)
                $this->log("Remote Addr: " . $this->_getIpAddress() . " - /api/user/showrequest - Invalid token, user not loged.", 'ERROR', $this->_logFile);
            return array('error' => 'Invalid token, user not loged.');
        }

        if (!function_exists('mb_convert_encoding')){
            if($this->_log)
                $this->log("Remote Addr: " . $this->_getIpAddress() . " - PHP does not have mb_convert_encoding function " , 'ERROR', $this->_logFile);
            return array('error'=> 'PHP does not have mb_convert_encoding function, yum install php-mbstring');
        }

        $idRequest = $arrParam['code'];
        $notes = $this->_getRequestNotes($idRequest);

        while (!$notes->EOF) {
            if($notes->fields['idtype'] != '2'){
                $desc = strip_tags(html_entity_decode(mb_convert_encoding($notes->fields['description'], 'UTF-8', 'UTF-8'), ENT_QUOTES, 'UTF-8'));
                $note[] = array(
                                "desc"          => $desc,
                                "username"      => $this->_getPersonName($idPerson),
                                "ip_address"    => $notes->fields['ip_adress'],
                                "time"          => $this->formatHour($notes->fields['entry_date']),
                                "date"          => $this->formatDateHour($notes->fields['entry_date'])
                );
            }
            $notes->MoveNext();
        }

        $req = $this->_getRequestData($idRequest);
        $questions = $evaluate = array();
        $arrSession = $this->_getSessionValues($idPerson);

        if($req->fields['idstatus'] == 4){
            if( $arrSession['SES_EVALUATE']) {

                $questions = $this->_getQuestions();

                $evaluate[] = array(
                    "question" => $this->_getLangVar('Approve_text'),
                    "answers"  => array(
                        array(
                            "value"     => "A",
                            "name"      => $this->_getLangVar('Approve_yes'),
                            "icon"      => null,
                            "checked"   => 1
                        ),
                        array(
                            "value"     => "N",
                            "name"      => $this->_getLangVar('Approve_no'),
                            "icon"      => null,
                            "checked"   => 0
                        ),
                        array(
                            "value"     => "O",
                            "name"      => $this->_getLangVar('Approve_obs'),
                            "icon"      => null,
                            "checked"   => 0
                        )
                    )
                );

                while (!$questions->EOF) {
                    $idquestion = $questions->fields['idquestion'];
                    $question   = $questions->fields['question'];
                    $answers    = $this->_getAnswers($idquestion);

                    $ans = null;
                    while (!$answers->EOF) {
                        $ans[] = array(
                            "value"     => $answers->fields['idevaluation'],
                            "name"      => $answers->fields['name'],
                            "icon"      => $this->getUrl()."app/uploads/icons/".$answers->fields['icon_name'],
                            "checked"   => $answers->fields['checked']
                        );
                        $answers->MoveNext();
                    }

                    $evaluate[] = array(
                        "question" => $question,
                        "answers"  => $ans
                    );

                    $questions->MoveNext();
                }

            }
        }
        $description = strip_tags(html_entity_decode(mb_convert_encoding($req->fields['description'], 'UTF-8', 'UTF-8'), ENT_QUOTES, 'UTF-8'));
        $data = array(
                    "id"            => $idRequest,
                    "status"        => $req->fields['status'],
                    "idstatus"      => $req->fields['idstatus'],
                    "dt_abertura"   => $this->formatDateHour($req->fields['entry_date']),
                    "responsavel"   => $req->fields['in_charge'],
                    "subject"       => $req->fields['subject'],
                    "description"   => $description,
                    "idperson"      => $idPerson,
                    //
                    "possibletoreopen" => $this->_getEspecificValueSession('SES_IND_REOPEN'),
                    //
                    "notes"         => $note,
                    "evaluate"      => $evaluate
                    );
        return $data;
    }

    public function post_cancelrequest($arrParam)
    {

        if($this->_log)
            $this->log("Remote Addr: " . $this->_getIpAddress() . " - Run /api/user/cancelrequest - Code: " . $arrParam['code'] , 'INFO', $this->_logFile);

        $idPerson = $this->_isLoged($arrParam['token']);
        if(!$idPerson) {
            if ($this->_log)
                $this->log("Remote Addr: " . $this->_getIpAddress() . " - /api/user/cancelrequest - Invalid token, user not loged.", 'ERROR', $this->_logFile);
            return array('error' => 'Invalid token, user not loged.');
        }
        if(!$this->_isRequest($arrParam['code'])){
            if ($this->_log)
                $this->log("Remote Addr: " . $this->_getIpAddress() . " - /api/user/cancelrequest - Request not exist.", 'ERROR', $this->_logFile);
            return array('error' => 'Request not exist.');

        }
        if($this->_getRequestStatus($arrParam['code']) != 1){
            if ($this->_log)
                $this->log("Remote Addr: " . $this->_getIpAddress() . " - /api/user/cancelrequest - Only is possible to cancel request new .", 'ERROR', $this->_logFile);
            return array('error' => 'Only is possible to cancel request new');

        }
        $code       = $arrParam['code'];
        $idStatus   = '11';

        $ret = $this->_cancelRequest($code,$idStatus,$idPerson);

        if ($this->_log) {
            if ($ret) {
                $this->log("Remote Addr: " . $this->_getIpAddress() . " - /api/user/cancelrequest- Request canceled successfuly.", 'INFO', $this->_logFile);
            } else {
                $this->log("Remote Addr: " . $this->_getIpAddress() . " - /api/user/cancelrequest- Error when canceling request.", 'ERROR', $this->_logFile);
            }

        }
        return ($ret) ?  array('success'=> 'Request canceled successfuly' ) :  array('error'=> 'Error when canceling request');

    }

    public function post_reopenrequest($arrParam)
    {
        if($this->_log)
            $this->log("Remote Addr: " . $this->_getIpAddress() . " - Run /api/user/reopenrequest - Code: " . $arrParam['code'] , 'INFO', $this->_logFile);

        $idPerson = $this->_isLoged($arrParam['token']);
        if(!$idPerson) {
            if ($this->_log)
                $this->log("Remote Addr: " . $this->_getIpAddress() . " - /api/user/reopenrequest - Invalid token, user not loged.", 'ERROR', $this->_logFile);
            return array('error' => 'Invalid token, user not loged.');
        }

        if(!$this->_isRequest($arrParam['code'])){
            if ($this->_log)
                $this->log("Remote Addr: " . $this->_getIpAddress() . " - /api/user/reopenrequest - Request not exist.", 'ERROR', $this->_logFile);
            return array('error' => 'Request not exist.');

        }

        if(!$this->_getEspecificValueSession('SES_IND_REOPEN')){
            if ($this->_log)
                $this->log("Remote Addr: " . $this->_getIpAddress() . " - /api/user/reopenrequest - Can not reopen requests, configured by admin.", 'ERROR', $this->_logFile);
            return array('error' => 'Can not reopen requests, configured by admin');

        }

        if($this->_getRequestStatus($arrParam['code']) != 5){
            if ($this->_log)
                $this->log("Remote Addr: " . $this->_getIpAddress() . " - /api/user/reopenrequest - Only is possible to reopen one request finished .", 'ERROR', $this->_logFile);
            return array('error' => 'Only is possible to reopen one request finished');
        }

        $code       = $arrParam['code'];
        $idStatus   = '1';

        $ret = $this->_reopenRequest($code,$idStatus,$idPerson);
        if ($this->_log) {
            if ($ret) {
                $this->log("Remote Addr: " . $this->_getIpAddress() . " - /api/user/reopenrequest- Request reopened successfuly.", 'INFO', $this->_logFile);
            } else {
                $this->log("Remote Addr: " . $this->_getIpAddress() . " - /api/user/reopenrequest- Error when reopen request.", 'ERROR', $this->_logFile);
            }

        }
        return ($ret) ?  array('success'=> 'Request reopened successfuly' ) :  array('error'=> 'Error when reopen request');


    }

    public function get_version()
    {
        return  array('success' => $this->_getHelpdezkVersion());
    }

    public function post_evaluatequestions($arrParam)
    {

        if($this->_log)
            $this->log("Remote Addr: " . $this->_getIpAddress() . " - Run /api/user/evaluatequestions " , 'INFO', $this->_logFile);

        $idPerson = $this->_isLoged($arrParam['token']);
        if(!$idPerson) {
            if ($this->_log)
                $this->log("Remote Addr: " . $this->_getIpAddress() . " - /api/user/evaluatequestions - Invalid token, user not loged.", 'ERROR', $this->_logFile);
            return array('error' => 'Invalid token, user not loged.');
        }

        //$rs = $this->_getEvaluationQuestions();

        /*
        while (!$rs->EOF) {
            $rows[] = array(
                "idquestion" => $rs->fields['idquestion'],
                "question" => addslashes($rs->fields['question']),
                "idevaluation" => $rs->fields['idevaluation'],
                "name" => addslashes($rs->fields['name']),
                "icon" => $this->hdkUrl.'/app/uploads/icons/'.$rs->fields['icon_name']
            );
            $rs->MoveNext();
        }
        */
        $rs = $this->_getQuestions();
        while (!$rs->EOF) {
            unset($evals);
            $rsEval = $this->_getAnswers($rs->fields['idquestion']);
            while(!$rsEval->EOF) {
                $evals[] = array(
                    "idevaluation" => $rsEval->fields['idevaluation'],
                    "name" => addslashes($rsEval->fields['name']),
                    "checked" => $rsEval->fields['checked'],
                    "icon" => $this->hdkUrl.'/app/uploads/icons/'.$rsEval->fields['icon_name'],
                );
                $rsEval->MoveNext();
            }
            if ($rsEval->RecordCount() > 0) {
                $rows[] = array(
                    "idquestion" => $rs->fields['idquestion'],
                    "question" => addslashes($rs->fields['question']),
                    "evaluation" => $evals
                );
            }

            $rs->MoveNext();
        }


        //print_r($rows);
        //echo json_encode($rows);
        return $rows;
    }

    public function post_evaluaterequest($arrParam)
    {

        if($this->_log)
            $this->log("Remote Addr: " . $this->_getIpAddress() . " - Run /api/user/requestevaluate - Code: " . $arrParam['code'] , 'INFO', $this->_logFile);

        $idPerson = $this->_isLoged($arrParam['token']);
        if(!$idPerson) {
            if ($this->_log)
                $this->log("Remote Addr: " . $this->_getIpAddress() . " - /api/user/requestevaluate - Invalid token, user not loged.", 'ERROR', $this->_logFile);
            return array('error' => 'Invalid token, user not loged.');
        }

        if(!$this->_isRequest($arrParam['code'])){
            if ($this->_log)
                $this->log("Remote Addr: " . $this->_getIpAddress() . " - /api/user/requestevaluate - Request not exist.", 'ERROR', $this->_logFile);
            return array('error' => 'Request not exist.');

        }

        $arrEval = json_decode($arrParam['answers']);

        if($this->_checkEvaluation($arrEval)){
            if ($this->_log)
                $this->log("Remote Addr: " . $this->_getIpAddress() . " - /api/user/requestevaluate - Evaluations Ids do not exists.", 'ERROR', $this->_logFile);
            return array('error' => 'Evaluations Ids do not exists.');
        }

        $code = $arrParam['code'];
        $evaluation = $arrParam['evaluation'] ;

        $callback = '0';
        $idType = '3';
        $public = '1';
        $date = "now()";

        switch($evaluation){
            case 'A':
                $idStatus = '5';
                $reopened = '0';
                $note = '<p><b>' . html_entity_decode($this->_getLangVar('Request_closed'), ENT_COMPAT, 'UTF-8') . '</b></p>';
                break;
            case 'N':
                $idStatus = '3';
                $reopened = '1';
                $reason = $arrParam['reason'] ;
                $note = "<p><b><span style=\"color: #FF0000;\">" . html_entity_decode($this->_getLangVar('Request_not_approve'), ENT_COMPAT, 'UTF-8') . "</span></b></p>";
                $note .= "<p><strong>" . html_entity_decode($this->_getLangVar('Reason'), ENT_COMPAT, 'UTF-8') . ":</strong> " . nl2br($reason) . "</p>";
                break;
            case 'O':
                $idStatus = '5';
                $reopened = '0';
                $observation = $arrParam['observation'];
                $note = '<p><b>' . html_entity_decode($this->_getLangVar('Request_closed'), ENT_COMPAT, 'UTF-8') . '</b></p>';
                $note .= "<p><strong>" . html_entity_decode($this->_getLangVar('Observation'), ENT_COMPAT, 'UTF-8') . ":</strong> " . nl2br($observation) . "</p>";
                break;
            default:
                return array('error' => 'Evaluation type is incorrect. Valid only A,N or O. .');
                break;
        }

        $ret = $this->_evaluateRequest($code,$idPerson,$arrEval,$idStatus,$reopened,$callback,$idType,$public,$note,$date);
        if(!$ret) {
            if ($this->_log)
                $this->log("Remote Addr: " . $this->_getIpAddress() . " - /api/user/requestevaluate - Error processing request evaluation.", 'ERROR', $this->_logFile);
            return array('error' => 'Error processing request evaluation.');

        } else {
            return array('success'=> $this->_getLangVar('API_Success_approval_request'));
        }

    }

    public function post_index($arrParam)
    {
        if($this->_log)
            $this->log("Remote Addr: " . $this->_getIpAddress() . " - Run /api/user/index " , 'INFO', $this->_logFile);

        $token = $arrParam['token'];
        $idPerson = $this->_isLoged($arrParam['token']);
        if(!$idPerson) {
            if ($this->_log)
                $this->log("Remote Addr: " . $this->_getIpAddress() . " - /api/user/index - Invalid token, user not loged.", 'ERROR', $this->_logFile);
            return array('error' => 'Invalid token, user not loged.');
        }

        if($this->_log)
            $this->log("Remote Addr: " . $this->_getIpAddress() . " - /api/user/index - Login: " . $this->_getLoginByToken($token) , 'INFO', $this->_logFile);

        $rsStatus = $this->_getStatus();

        while (!$rsStatus->EOF) {

            if ($rsStatus->fields['idstatus'] > 10 and  $rsStatus->fields['count_req'] == 0 )
            {
                $rsStatus->MoveNext();
                continue ;
            }


            $rows[] = array(
                "id" => $rsStatus->fields['idstatus'],
                "name" => $rsStatus->fields['name'] ,
                "count" => $rsStatus->fields['count_req'],
                "color" => $rsStatus->fields['color']
            );

            $rsStatus->MoveNext();
        }

        $data['status'] = $rows;
        return $rows;


    }



}