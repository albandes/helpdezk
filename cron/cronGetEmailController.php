<?php
/**
 * Created by PhpStorm.
 * User: rogerio.albandes
 * Date: 23/09/2019
 * Time: 11:00
 */
// Report simple running errors
error_reporting(E_ERROR | E_WARNING | E_PARSE);

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
        $this->debug = true ;

        // Log settings
        $this->log = parent::$_logStatus;
        $this->program  = basename( __FILE__ );

        $this->loadModel('helpdezk/ticket_model');
        $dbTicket = new ticket_model();
        $this->dbTicket = $dbTicket;

        $this->loadModel('helpdezk/requestemail_model');
        $dbGetEmail = new requestemail_model();
        $this->dbGetEmail = $dbGetEmail;

        $this->loadModel('admin/person_model');
        $dbPerson = new person_model();
        $this->dbPerson = $dbPerson;

        $this->ticket = new ticket();

    }

    public function downloadEmail()
    {
        if (!function_exists('imap_open'))
            die("IMAP functions are not available.");

        $rsGetEmail = $this->dbGetEmail->getRequestEmail();

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

            for($i = 1;$i <= $nummsg; $i++)
            {
                $headers = imap_header($mbox, $i);
                $subject = $this->fixText($headers->subject);
                $udate = $headers->udate;
                $from = $headers->sender;
                $sender = $from[0]->mailbox . "@" .  $from[0]->host;

                $id = $udate."-".$sender;
                $EmailDate = $headers->date;
                $EmailDate = strtotime($EmailDate);
                $EmailDate = date("d/m/Y H:i:s", $EmailDate);

                if($this->log)
                    $this->logIt('Processing: Message: '.$i. ', From: ' .$sender . ', Subject: ' . $subject ,5,'general');

                // --- Body -------------------------------------------------------------------
                $body = $this->getBody($mbox,$i);
                // --- Attachments ------------------------------------------------------------
                unset($arrayAtt);
                unset($a_attachments);
                $arrayAtt = $this->extractAttachments($mbox, $i) ;
                $a_attachments = $this->parseAttachments($arrayAtt);

                if($this->log)
                    $this->logIt('layout: '. $rsGetEmail->fields['login_layout'] ,5,'general');

                $login = $this->makeLogin($rsGetEmail->fields['login_layout'],$from);

                if(!$login) {
                    if ($this->log)
                        $this->logIt('Login empty ! ', 5, 'general', __LINE__);
                }

                if($this->log)
                    $this->logIt("Getting ID from login : $login",5,'general');

                $idperson = $this->getIdPerson($login);
                if (!$idperson) {
                    if ($this->log)
                        $this->logIt("Login unknown ! idperson from  $login not found! ", 5, 'general');
                    continue;
                }

                if($this->log)
                    $this->logIt("$login, idperson : $idperson",5,'general');

                // ---
                
                // To check if there is already a request associated with the email id
                $rsCodeEmail = $this->dbGetEmail->getRequestCodEmail($id);
                if (!$rsCodeEmail->EOF){
                    if ($rsGetEmail->fields['ind_delete_server']) {
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
                    $this->logIt('Passei aqui : ',7,'general',__LINE__);

                    $code_request = $this->ticket->createRequestCode();

                    $this->logIt('Code Request By Class: '. $code_request,7,'general',__LINE__);


                    $idperson_juridical = $this->ticket->getIdPersonJuridical($idperson) ;
                    if(!$idperson_juridical) {
                        if($this->log)
                            $this->logIt('There is no user related company !!! ',3,'general',__LINE__);
                        continue;
                    }

                    $idStatus 	= 1;

                    $this->logIt('idcompany: '. $idperson_juridical,7,'general',__LINE__);


                    $rsCore = $this->ticket->getAreaTypeItemByService($rsGetEmail->fields['idservice']);
                    if(!$rsCore) {
                        if($this->log)
                            $this->logIt("Couldn't get Area, Type, Item by service id !!! ",3,'general',__LINE__);
                        continue;
                    }


                    $numRules = $this->ticket->getNumRules($rsCore->fields['iditem'],$rsGetEmail->fields['idservice']);
                    if($numRules > 0)  // If have approval rule, put the status of the ticket as repassed (2)
                        $idStatus = 2;


                    // pipetodo: Vip User
                    if($this->ticket->isVipUser($idperson)){
                        $this->logIt('is vip : '. $idperson,7,'general',__LINE__);
                    } else {
                        $this->logIt('is not vip : '. $idperson,7,'general',__LINE__);

                    }




                }

    
            }
		
            $rsGetEmail->MoveNext();
        }
        
        die('OK');
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


}