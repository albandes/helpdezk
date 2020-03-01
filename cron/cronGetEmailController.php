<?php
/**
 * Created by PhpStorm.
 * User: rogerio.albandes
 * Date: 23/09/2019
 * Time: 11:00
 */
// Report simple running errors
//error_reporting(E_ERROR | E_WARNING | E_PARSE);
error_reporting(E_ERROR | E_PARSE);

require_once(HELPDEZK_PATH . '/cron/cronCommon.php');
require_once(HELPDEZK_PATH . '/includes/classes/pipegrep/ticket.php');

class cronGetEmail extends cronCommon {
    /**
     * Create an instance, check session time
     * usage: /home/htdocs/git/helpdezk/cron/index.php getEmail/downloadEmail
     * @access public
     */
    public function __construct()
    {

        parent::__construct();

        // Debug Settings
        $this->debug = false ;

        // Log settings
        $this->log = parent::$_logStatus;
        $this->program  = basename( __FILE__ );

        //
        $this->attachPath = $this->getAttachPath();

        $this->loadModel('helpdezk/ticket_model');
        $dbTicket = new ticket_model();
        $this->dbTicket = $dbTicket;

        $this->loadModel('helpdezk/requestemail_model');
        $dbGetEmail = new requestemail_model();
        $this->dbGetEmail = $dbGetEmail;

        $this->loadModel('admin/person_model');
        $dbPerson = new person_model();
        $this->dbPerson = $dbPerson;

        $this->loadModel('helpdezk/ticketrules_model');
        $dbTicketRules = new ticketrules_model();
        $this->dbTicketRules = $dbTicketRules;


        $this->ticket = new ticket($this->debug);

    }

    public function downloadEmail()
    {
        if (!function_exists('imap_open'))
            die("IMAP functions are not available.");


        $rsGetEmail = $this->dbGetEmail->getRequestEmail();

        $idSource = 3;
        $idWay    = 3;

        $deleteFromServer = $rsGetEmail->fields['ind_delete_server'];

        while(!$rsGetEmail->EOF){

            $authHost = $this->setServerType($rsGetEmail->fields['servertype'],$rsGetEmail->fields['serverurl'],$rsGetEmail->fields['serverport']);

            if($this->log)
                $this->logIt('Connecting to: '.$authHost,5,'general');

            $mbox = imap_open( $authHost, $rsGetEmail->fields['user'], $rsGetEmail->fields['password'] );

            if ($mbox) {
                $check = imap_mailboxmsginfo($mbox);
                if ($check) {
                    if ($this->log)
                        $this->listMailboxLog($rsGetEmail->fields['user'],$check);
                } else {
                    if ($this->log)
                        $this->logIt('Get E-mail Error: '.imap_last_error().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                }
            } else {
                $error = imap_errors();
                //imap_close($mbox);
                if($this->log)
                    $this->logIt('Get E-mail Error: '.$error[0].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                $rsGetEmail->MoveNext();
                continue ;
            }

            $nummsg = 	imap_num_msg($mbox) ;
            if ($nummsg == 0) {
                if($this->log) {
                    $this->logIt($rsGetEmail->fields['user'] .' maibox is empty',5,'general');
                }
                $rsGetEmail->MoveNext();
                continue;
            }


            $idService = $rsGetEmail->fields['idservice'] ;

            for($i = 1;$i <= $nummsg; $i++)
            {

                $hasAttachments = false ;

                $headers = imap_header($mbox, $i);
                $subject = $this->fixText($headers->subject);
                $udate = $headers->udate;
                $from = $headers->sender;
                $sender = $from[0]->mailbox . "@" .  $from[0]->host;

                $idEmail = $udate."-".$sender;
                $EmailDate = $headers->date;
                $EmailDate = strtotime($EmailDate);
                $EmailDate = date("d/m/Y H:i:s", $EmailDate);

                if($this->log)
                    $this->logIt('Processing: Message: '.$i. ', From: ' .$sender . ', Subject: ' . $subject ,5,'general');

                // --- Body -------------------------------------------------------------------
                $body = $this->getBody($mbox,$i);
                // --- Attachments ------------------------------------------------------------
                $a_attachments = $this->getAttachments($mbox,$i);
                // ----------------------------------------------------------------------------

                if(count($a_attachments) > 0)
                    $hasAttachments = true;

                if($this->log)
                    $this->logIt('layout: '. $rsGetEmail->fields['login_layout'] ,5,'general');

                $login = $this->makeLogin($rsGetEmail->fields['login_layout'],$from);

                // Used only by the client 202001002 that uses uses Wix to send email from a form
                if ($this->getConfig("license") == '202001002' and $from[0]->host == 'crm.wix.com') {
                    $login = 'usuario.email';
                    $this->logIt("Client 202001002, default login : $login",5,'general');
                }

                if(!$login) {
                    if ($this->log)
                        $this->logIt('Login empty ! ', 5, 'general', __LINE__);
                }

                if($this->log)
                    $this->logIt("Getting ID from login : $login",5,'general');




                $idPerson = $this->getIdPerson($login);
                if (!$idPerson) {
                    if ($this->log)
                        $this->logIt("Login unknown ! idperson from  $login not found! ", 5, 'general');
                    continue;
                }

                // To check if there is already a request associated with the email id
                $rsCodeEmail = $this->dbGetEmail->getRequestCodEmail($idEmail);
                if (!$rsCodeEmail->EOF){
                    if ($deleteFromServer) {
                        imap_delete($mbox, $i);	 // Mark email to delete
                    }
                    if($this->log)
                        $this->logIt('Already have request associated : '. $rsCodeEmail->fields['code_request']." Sender: "  . $sender ,3,'general',__LINE__);
                    continue;
                }

                // Filters
                // pipetodo [albandes]: Test this filters
                if(!$this->filterEmailSender($rsGetEmail, $sender)) {
                    continue;
                }

                if(!$this->filterEmailSubject($rsGetEmail, $sender)){
                    continue;
                }

                $INSERT_SQL = "true";

                // pipetodo [albandes]: Check if you can use the email header.
                $codeRequestAtt = $this->getCodeRequestFromSubject($subject);
                if($codeRequestAtt)
                    $rsCount = $this->dbGetEmail->getCountRequest($codeRequestAtt);

                if (($codeRequestAtt) and (is_numeric($codeRequestAtt)) and ($rsGetEmail->fields['email_response_as_note'] == 1) and ($rsCount->fields['total'] > 0) ) {

                }

                if ($INSERT_SQL == "true")  {

                    $code_request = $this->ticket->createRequestCode();

                    $this->logIt('Code Request By Class: '. $code_request,7,'general',__LINE__);

                    $idCompany = $this->ticket->getIdPersonJuridical($idPerson) ;
                    if(!$idCompany) {
                        if($this->log)
                            $this->logIt('There is no user related company !!! ',3,'general',__LINE__);
                        continue;
                    }

                    $rsCore = $this->ticket->getAreaTypeItemByService($idService);
                    if(!$rsCore) {
                        if($this->log)
                            $this->logIt("Couldn't get Area, Type, Item by service id !!! ",3,'general',__LINE__);
                        continue;
                    }

                    $idItem = $rsCore->fields['iditem'] ;
                    $idType = $rsCore->fields['idtype'] ;

                    $idStatus 	= 1;

                    $numRules = $this->ticket->getNumRules($idItem,$idService);
                    if($numRules > 0)  // If have approval rule, put the status of the ticket as repassed (2)
                        $idStatus = 2;

                    $this->logIt('numRules: '. $numRules,7,'general',__LINE__);

                    if ( $this->ticket->isVipUser($idPerson)  &&  $this->ticket->hasVipPriority()  ) {
                        $idPriority = $this->ticket->getVipPriority();
                    } else {
                        $idPriority = $this->ticket->getServicePriority($idService);
                    }

                    $this->logIt('idpriority: '. $idPriority,7,'general',__LINE__);

                    $startDate = $this->ticket->getStartDate();
                    $expireDate = $this->ticket->getDueDate($idPriority, $idService);
                    $this->logIt('expireDate: '. $expireDate,7,'general',__LINE__);

                    $idPersonAuthor = $idPerson;

                    $idReason = 'NULL';
                    // pipetodo [albandes]: Put "reason" in the form and databse

                    $this->dbTicket->BeginTrans();

                    $rs = $this->dbTicket->insertRequest($idPersonAuthor,$idSource,$startDate,$idType,$idItem,$idService,$idReason,$idWay,$subject,addslashes($body),'',$idPriority,'','',$idCompany,$expireDate,$idPerson,$idStatus,$code_request,$idEmail);
                    if(!$rs){
                        $this->dbTicket->RollbackTrans();
                        if($this->log)
                            $this->logIt("Did not insert ticket by email # ". $code_request . ' - User: '.$sender.' - program: '.$this->program ,3,'general',__LINE__);
                        continue;
                    }

                    $idGroup = $this->ticket->getServiceGroup($idService);
                    if (!$idGroup){
                        $this->dbTicket->RollbackTrans();
                        $this->logIt("Failed to get the service group. idService: ". $idService .' - program: '.$this->program ,3,'general',__LINE__);
                        continue;
                    }


                    if($numRules > 0)  {

                        $values = $this->ticket->getApprovalOrder($code_request,$idItem, $idService);
                        $ret = $this->dbTicketRules->insertApproval($values);
                        if($ret) {

                            $onlyRep = $this->ticket->checkGroupOnlyRepass($idGroup);

                            if($onlyRep){
                                $idGroupRepass = $this->ticket->getNewGroupOnlyRepass($idGroup,$idCompany);
                                if($idGroupRepass) {
                                    $save = $this->dbTicket->insertRequestCharge($code_request,$idGroupRepass,'G', '0');
                                } else {
                                    $save = $this->dbTicket->insertRequestCharge($code_request, $idGroup, 'G', '0');
                                }
                            } else {
                                $save = $this->dbTicket->insertRequestCharge($code_request, $idGroup, 'G', '0');
                            }

                            $idPersonApprover = $this->ticket->getIdPersonApprover($idItem,$idService);

                            if(!$idPersonApprover) {
                                if ($this->log)
                                    $this->logIt("Failed to get idPersonApprover, by email. "  . ' Program: ' . $this->program, 3, 'general', __LINE__);
                                $this->dbTicket->RollbackTrans();
                                continue;
                            }

                            $write = $this->dbTicket->insertRequestCharge($code_request, $idPersonApprover, 'P', '1');

                            if(!$save || !$write) {
                                if ($this->log)
                                    $this->logIt("Failed to insert in insert request charge, by email.  "  . ' Program: ' . $this->program, 3, 'general', __LINE__);
                                $this->dbTicket->RollbackTrans();
                                continue;
                            }

                        } else {
                            if($this->log)
                                $this->logIt("Failed to insert in hdk_tbrequest_approval table, by email !!! " .' Program: '.$this->program ,3,'general',__LINE__);
                            $this->dbTicket->RollbackTrans();
                            continue;
                        }

                    } else {
                        $onlyRep = $this->ticket->checkGroupOnlyRepass($idGroup);
                        if($onlyRep){
                            $idGroupRepass = $this->ticket->getNewGroupOnlyRepass($idGroup,$idCompany);
                            if($idGroupRepass) {
                                $save = $this->dbTicket->insertRequestCharge($code_request,$idGroupRepass,'G', '1');
                            } else {
                                $save = $this->dbTicket->insertRequestCharge($code_request, $idGroup, 'G', '1');
                            }
                            if(!$save) {
                                if ($this->log)
                                    $this->logIt("Failed to insert in insert request charge, by email.  "  . ' Program: ' . $this->program, 3, 'general', __LINE__);
                                $this->dbTicket->RollbackTrans();
                                continue;
                            }
                        } else {
                            $write = $this->dbTicket->insertRequestCharge($code_request, $idGroup, 'G', '1');
                            if (!$write) {
                                if ($this->log)
                                    $this->logIt("Failed to insert in insert request charge, by email.  " . ' Program: ' . $this->program, 3, 'general', __LINE__);
                                $this->dbTicket->RollbackTrans();
                                continue;
                            }
                        }

                    }

                    /*
                    $ret = $this->setRequestInCharge($numRules,$idCompany,$idGroup,$code_request,$idItem, $idService);
                    if(!$ret){
                        continue;
                    }
                    */

                    $ret = $this->dbTicket->insertRequestTimesNew($code_request);
                    if(!$ret){
                        $this->dbTicket->RollbackTrans();
                        if($this->log)
                            $this->logIt("Did not insert Request Time, by email . Program: ".$this->program ,3,'general',__LINE__);
                        continue;
                    }

                    $ret = $this->dbTicket->insertRequestLog($code_request,date("Y-m-d H:i:s"),$idStatus,$idPerson);
                    if(!$ret){
                        $this->dbTicket->RollbackTrans();
                        if($this->log)
                            $this->logIt("Did not insert Request Log, by email. Program: ".$this->program ,3,'general',__LINE__);
                        continue;
                    }

                    $description = "<p><b>" . $this->_getLanguageWord('Request_opened') . "</b></p>";

                    $ret = $this->dbTicket->insertNote($code_request, $idPerson, $description, $this->databaseNow, 0, 0, 0, '0000-00-00 00:00:00', 0, NULL, 1, 3, '', 0, NULL );
                    if(!$ret){
                        $this->dbTicket->RollbackTrans();
                        if($this->log)
                            $this->logIt("Did not insert Request Note, by email. Program: ".$this->program ,3,'general',__LINE__);
                        continue;
                    }

                    $this->dbTicket->CommitTrans();

                    /**
                     **  Attachments
                     **/

                    $attach_err = 0;
                    
                    if( $hasAttachments ) {
                        if($this->log)
                            $this->logIt("Email has: " . count($a_attachments) . " attachment(s) !"  ,5,'general',__LINE__);
                        if (!is_writeable($this->attachPath))
                        {
                            $this->dbTicket->RollbackTrans();
                            if($this->log)
                                $this->logIt("Error directory : " .$this->attachPath . ", is not writable, request  not inserted !!! " . ' Program: ' . $this->program, 3, 'general', __LINE__);
                            continue;
                        }

                        $ret = $this->ticket->saveRequestAttachments($a_attachments,$code_request,$this->attachPath);
                        if(!$ret){
                            $this->dbTicket->RollbackTrans();
                            if($this->log)
                                $this->logIt("There were errors with attachments, request was not generated ! Program: ".$this->program ,3,'general',__LINE__);
                            continue;
                        }

                    }

                    // pipetodo [albandes]: Need delete files and database register if have problem in attachs

                    $this->_cronSendNotification('new-ticket-user','email',$code_request);

                    if($this->log)
                        $this->logIt("Insert ticket by email# ". $code_request . ' - User: '.$sender ,5,'general');

                }

    
            }

            if ($rsGetEmail->fields['ind_delete_server'])
            {
                imap_expunge($mbox);
            }

            imap_close($mbox);


            $rsGetEmail->MoveNext();
        }
        
        die(PHP_EOL.'OK'.PHP_EOL);
    }


    function setRequestInCharge($numRules,$idCompany,$idGroup,$code_request,$idItem, $idService)
    {

        if($numRules > 0)  {

            $values = $this->ticket->getApprovalOrder($code_request,$idItem, $idService);
            $ret = $this->dbTicketRules->insertApproval($values);
            if($ret) {

                $onlyRep = $this->ticket->checkGroupOnlyRepass($idGroup);

                if($onlyRep){
                    $idGroupRepass = $this->ticket->getNewGroupOnlyRepass($idGroup,$idCompany);
                    if($idGroupRepass) {
                        $save = $this->dbTicket->insertRequestCharge($code_request,$idGroupRepass,'G', '0');
                    } else {
                        $save = $this->dbTicket->insertRequestCharge($code_request, $idGroup, 'G', '0');
                    }
                } else {
                    $save = $this->dbTicket->insertRequestCharge($code_request, $idGroup, 'G', '0');
                }

                $idPersonApprover = $this->ticket->getIdPersonApprover($idItem,$idService);

                if(!$idPersonApprover) {
                    if ($this->log)
                        $this->logIt("Failed to get idPersonApprover, by email. "  . ' Program: ' . $this->program, 3, 'general', __LINE__);
                    $this->dbTicket->RollbackTrans();
                    return false;
                }


                $write = $this->dbTicket->insertRequestCharge($code_request, $idPersonApprover, 'P', '1');
                if(!$save || !$write) {
                    if ($this->log)
                        $this->logIt("Failed to insert in insert request charge, by email.  "  . ' Program: ' . $this->program, 3, 'general', __LINE__);
                    $this->dbTicket->RollbackTrans();
                    return false;
                }

            } else {
                if($this->log)
                    $this->logIt("Failed to insert in hdk_tbrequest_approval table, by email !!! " .' Program: '.$this->program ,3,'general',__LINE__);
                $this->dbTicket->RollbackTrans();
                return false;
            }

        } else {
            $onlyRep = $this->ticket->checkGroupOnlyRepass($idGroup);
            if($onlyRep){
                $idGroupRepass = $this->ticket->getNewGroupOnlyRepass($idGroup,$idCompany);
                if($idGroupRepass) {
                    $save = $this->dbTicket->insertRequestCharge($code_request,$idGroupRepass,'G', '1');
                } else {
                    $save = $this->dbTicket->insertRequestCharge($code_request, $idGroup, 'G', '1');
                }
                if(!$save) {
                    if ($this->log)
                        $this->logIt("Failed to insert in insert request charge, by email.  "  . ' Program: ' . $this->program, 3, 'general', __LINE__);
                    $this->dbTicket->RollbackTrans();
                    return false ;
                }
            } else {
                $write = $this->dbTicket->insertRequestCharge($code_request, $idGroup, 'G', '1');
                if (!$write) {
                    if ($this->log)
                        $this->logIt("Failed to insert in insert request charge, by email.  " . ' Program: ' . $this->program, 3, 'general', __LINE__);
                    $this->dbTicket->RollbackTrans();
                    return false;
                }
            }

        }

        return true;

    }

    function getAttachments($mbox, $i)
    {
        unset($arrayAtt);
        unset($a_attachments);
        $arrayAtt = $this->extractAttachments($mbox, $i) ;
        return $this->parseAttachments($arrayAtt);

    }

    function  getCodeRequestFromSubject($subject)
    {
        preg_match('/.*# (\d+).*$/', $subject, $cod_solicitacao);
        if (isset($cod_solicitacao[1])) {
            return $cod_solicitacao[1];
        } else {
            return  false;
        }
    }

    function makeLogin($layout,$objFrom)
    {
        if($layout == 'U') {
            return $objFrom[0]->mailbox ;
        } elseif ($layout == 'E') {
            return $objFrom[0]->mailbox."@".$objFrom[0]->host;;
        }

        return false;

    }

    function filterEmailSubject($rs, $subject)
    {
        if( $rs->fields['filter_subject'] ) {
            if ($rs->fields['filter_subject'] != $subject) {
                if($this->log)
                    $this->logIt('Filter by subject enabled, subject does not match: '.  $subject ,5,'general');
                return false;
            }
        }

        return true;
    }

    function filterEmailSender($rs, $sender)
    {
        if( $rs->fields['filter_from'] ) {
            if ($rs->fields['filter_from'] != $sender) {
                if($this->log)
                    $this->logIt('Filter by sender enabled, sender does not match: '.  $sender ,5,'general');
                return false;
            }
        }
        return true ;
    }

    function getIdPerson($login)
    {
        $rsPerson = $this->dbPerson->selectPerson(" AND login = '$login'");
        if(!$rsPerson) {
            return false;
        } else {
            return $rsPerson->fields['idperson'];
        }
    }

    function parseAttachments ($a_att)
    {
        $k=0;
        foreach ($a_att as $v1) {
            foreach ($v1 as $key=>$value) {
                if($key == "is_attachment" and $value == "1") {
                    $grava = true ;
                }
                if($key == "is_attachment" and $value != "1") {
                    $grava = false ;
                }
                if($grava) {
                    $a_attachments[$k][$key]=$value;
                }
            }
            $k++;
        }

        return $a_attachments;
    }

    function getBody($bx,$mid)
    {
        // Get Message Body
        $body = $this->getPart($bx, $mid, "TEXT/HTML");
        if ($body == "")
            $body = $this->getPart($bx, $mid, "TEXT/PLAIN");
        if ($body == "") {
            return "";
        }
        return $body;
    }

    function listMailboxLog($user,$mailboxInfo)
    {
        $this->logIt("Mailbox: " . $user, 5, 'general');
        $this->logIt("Driver: " . $mailboxInfo->Driver, 5, 'general');
        $this->logIt("Messages: " . $mailboxInfo->Messages, 5, 'general');
        $this->logIt("Recent: " . $mailboxInfo->Recent, 5, 'general');
        $this->logIt("Unread:: " . $mailboxInfo->Unread, 5, 'general');
        $this->logIt("Deleted: " . $mailboxInfo->Deleted, 5, 'general');
        $this->logIt("Size: " . $mailboxInfo->Size, 5, 'general');

    }

    function setServerType ($serverType, $serverUrl,$serverPort)
    {
        if ( $serverType == 'gmail') {
            $authhost="{imap.gmail.com:993/imap/ssl/novalidate-cert}INBOX";
        } else if ( $serverType == 'pop') {
            $authhost="{".$serverUrl.":".$serverPort."/pop3/notls}INBOX";
        } else if  ( $serverType == 'pop-gmail') {
            $authhost="{imap.gmail.com:993/imap/ssl/novalidate-cert}INBOX";
        } else if ( $serverType == 'imap-ssl') {
            $authhost="{".$serverUrl.":".$serverPort."/imap/ssl/novalidate-cert}INBOX";
        }
        return $authhost;
    }

    function fixText($str)
    {
        $subject = '';
        $subject_array = imap_mime_header_decode($str);
        foreach ($subject_array AS $obj){
            $charset = strtoupper($obj->charset);
            if($charset == "ISO-8859-1" || $charset == "WINDOWS-1252"){
                $subject .= utf8_encode(rtrim($obj->text, "\t"));
            }else{
                $subject .= rtrim($obj->text, "\t");
            }
            //if($this->log)
            //    $this->logIt("Text: ".$obj->text . " - Charset: ".$obj->charset,5,'general');

        }

        return $subject;
    }

    function extractAttachments($connection, $message_number)
    {
        $attachments = array();
        $structure = imap_fetchstructure($connection, $message_number);

        if(isset($structure->parts) && count($structure->parts)) {
            for($i = 0; $i < count($structure->parts); $i++) {
                $attachments[$i] = array(
                    'is_attachment' => false,
                    'filename' => '',
                    'name' => '',
                    'attachment' => ''
                );
                if($structure->parts[$i]->ifdparameters) {
                    foreach($structure->parts[$i]->dparameters as $object) {
                        if(strtolower($object->attribute) == 'filename') {
                            $attachments[$i]['is_attachment'] = true;
                            $attachments[$i]['filename'] = $object->value;
                        }
                    }
                }
                if($structure->parts[$i]->ifparameters) {
                    foreach($structure->parts[$i]->parameters as $object) {
                        if(strtolower($object->attribute) == 'name') {
                            $attachments[$i]['is_attachment'] = true;
                            $attachments[$i]['name'] = $object->value;
                        }
                    }
                }
                if($attachments[$i]['is_attachment']) {
                    $attachments[$i]['attachment'] = imap_fetchbody($connection, $message_number, $i+1);
                    if($structure->parts[$i]->encoding == 3) { // 3 = BASE64
                        $attachments[$i]['attachment'] = base64_decode($attachments[$i]['attachment']);
                    }
                    elseif($structure->parts[$i]->encoding == 4) { // 4 = QUOTED-PRINTABLE
                        $attachments[$i]['attachment'] = quoted_printable_decode($attachments[$i]['attachment']);
                    }
                }
            }
        }

        return $attachments;
    }

    function getPart($stream, $msg_number, $mime_type, $structure = false, $part_number = false) //Get Part Of Message Internal Private Use
    {
        if(!$structure) {
            $structure = imap_fetchstructure($stream, $msg_number);
        }
        if($structure) {
            if($mime_type == $this->get_mime_type($structure))
            {
                if(!$part_number)
                {
                    $part_number = "1";
                }
                $text = imap_fetchbody($stream, $msg_number, $part_number);
                if($structure->encoding == 3)
                {
                    return imap_base64($text);
                }
                else if($structure->encoding == 4)
                {
                    return imap_qprint($text);
                }
                else
                {
                    return $text;
                }
            }
            if($structure->type == 1) /* multipart */
            {
                while(list($index, $sub_structure) = each($structure->parts))
                {
                    if($part_number)
                    {
                        $prefix = $part_number . '.';
                    }
                    $data = $this->getPart($stream, $msg_number, $mime_type, $sub_structure, $prefix . ($index + 1));
                    if($data)
                    {
                        return $data;
                    }
                }
            }
        }
        return false;
    }

    function get_mime_type(&$structure) //Get Mime type Internal Private Use
    {
        $primary_mime_type = array("TEXT", "MULTIPART", "MESSAGE", "APPLICATION", "AUDIO", "IMAGE", "VIDEO", "OTHER");

        if($structure->subtype) {
            return $primary_mime_type[(int) $structure->type] . '/' . $structure->subtype;
        }
        return "TEXT/PLAIN";
    }

    function getAttachPath()
    {
        $path_parts = pathinfo(dirname(__FILE__));
        $cron_path = $path_parts['dirname'] ;
        return  str_replace("\\","/",$cron_path) . "/app/uploads/helpdezk/attachments/" ;
    }


}