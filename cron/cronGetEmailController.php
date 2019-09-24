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

var_dump($this->log);

        $this->loadModel('helpdezk/ticket_model');
        $dbTicket = new ticket_model();
        $this->dbTicket = $dbTicket;

        $this->loadModel('helpdezk/requestemail_model');
        $dbGetEmail = new requestemail_model();
        $this->dbGetEmail = $dbGetEmail;

        $this->loadModel('admin/person_model');
        $dbPerson = new person_model();
        $this->dbPerson = $dbPerson;

    }

    public function downloadEmail()
    {
        if (!function_exists('imap_open'))
            die("IMAP functions are not available.");

        $rsGetEmail = $this->dbGetEmail->getRequestEmail();

        while(!$rsGetEmail->EOF){

            $authHost = $this->setServerType($rsGetEmail->fields['servertype'],$rsGetEmail->fields['serverurl'],$rsGetEmail->fields['serverport']);
            $mbox = imap_open( $authHost, $rsGetEmail->fields['user'], $rsGetEmail->fields['password'] );
            if ($mbox) {
                $check = imap_mailboxmsginfo($mbox);
                if ($check) {
                    if ($this->log) {
                        $this->logIt("Mailbox: " . $rsGetEmail->fields['user'], 5, 'general');
                        $this->logIt("Driver: " . $check->Driver, 5, 'general');
                        $this->logIt("Messages: " . $check->Messages, 5, 'general');
                        $this->logIt("Recent: " . $check->Recent, 5, 'general');
                        $this->logIt("Unread:: " . $check->Unread, 5, 'general');
                        $this->logIt("Deleted: " . $check->Deleted, 5, 'general');
                        $this->logIt("Size: " . $check->Size, 5, 'general');
                    }
                } else {
                    echo "imap_check() failed: " . imap_last_error();
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
                $subject = fix_text($headers->subject);
                $udate = $headers->udate;
                $from = $headers->sender;
                $sender = $from[0]->mailbox . "@" .  $from[0]->host;

                $id = $udate."-".$sender;
                $EmailDate = $headers->date;
                $EmailDate = strtotime($EmailDate);
                $EmailDate = date("d/m/Y H:i:s", $EmailDate);


                if($this->log)
                    $this->logIt('Processing: Message: '.$i. 'From $sender, Subject: $subject ' ,3,'general',__LINE__);

                // --- Body -------------------------------------------------------------------
                $body = getBody($mbox,$i);
                // --- Attachments ------------------------------------------------------------
                unset($a_att);
                unset($a_attachments);
                $a_att = extract_attachments($mbox, $i) ;
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
                // -----------------------------------------------------------------------------

                if($rsGetEmail->fields['login_layout'] == 'U') {
                    $login = $from[0]->mailbox ;
                } elseif ($rsGetEmail->fields['login_layout'] == 'E') {
                    $login = $sender;
                }

                if($this->log)
                    $this->logIt('Login empty ! ',5,'general',__LINE__);

                $rsPerson = $this->dbPerson->selectPerson(' where login = ' . $login);
                if(!$rsPerson) {
                    if($this->log)
                        $this->logIt('Login unknow: '.$login ,3,'general');

                    continue;
                } else {
                    $idperson = $rsPerson->fields['idperson'];
                }

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
                if( $rsGetEmail->fields['filter_from'] ) {
                    if ($rsGetEmail->fields['filter_from'] != $sender) {
                        if($this->log)
                            $this->logIt('Filter by sender enabled, sender does not match: '.  $sender ,5,'general',__LINE__);

                        continue;
                    }
                }
                if( $rsGetEmail->fields['filter_subject'] ) {
                    if ($rsGetEmail->fields['filter_subject'] != $subject) {
                        if($this->log)
                            $this->logIt('Filter by subject enabled, subject does not match: '.  $subject ,5,'general',__LINE__);

                        continue;
                    }
                }

                $INSERT_SQL = "true";
                preg_match('/.*# (\d+).*$/', $subject, $cod_solicitacao);

                if (isset($cod_solicitacao[1])) {
                    $COD_SOLICITACAO_APONT =  $cod_solicitacao[1];
                    $resposta = "R";
                } else {
                    $resposta = "NAO";
                }

                $sql_checkreq = $this->dbGetEmail->getCountRequest($COD_SOLICITACAO_APON);
                if (($resposta == "R") and (is_numeric($COD_SOLICITACAO_APONT)) and ($rsGetEmail->fields['email_response_as_note'] == 1) and ($sql_checkreq->fields['total'] > 0) )
                {
                    if($this->log)
                        $this->logIt('Create new note: '.  $COD_SOLICITACAO_APONT ,5,'general',__LINE__);

                    $rsSol = $this->dbTicket->getRequestData(' where code_request = ' . $COD_SOLICITACAO_APONT);

                    if (strlen($id) < 4) {
                        $ja_add = false;
                    } else {
                        $sql_result_verifica = $this->dbGetEmail->getRequestFromNote(' where code_request = '.$COD_SOLICITACAO_APONT.' AND code_email = "'.$id.'"');
                        $ja_add = (bool)$sql_result_verifica->RecordCount();
                    }

                    if (!$rsSol->EOF AND !$ja_add) {
                        //divisores de mensagens outlook 2000 e outlook express
                        $array = array("-----Mensagem original-----",
                            "----- Mensagem Original -----",
                            "-----Original message-----",
                            "-----Original Message-----",
                            "----- Original Message -----");

                        //percorre todos os divisores de mensagens
                        foreach ($array as $value) {
                            $str = explode($value, $body);
                            if (count($str) > 1)
                                break;
                        }

                        if (isset($str[0]) && !empty($str[0]))  {
                            $body = $str[0];
                        }

                        $resposta = $body;
                        $resposta = strip_tags($resposta,"<br><br/><br />");
                        $resposta = addslashes($resposta);
                        $retCreateNote = $this->dbTicket->insertNote($COD_SOLICITACAO_APONT,$idperson,$resposta,NOW(),'','','',NOW(),'','',1,1,'Generated by e-mail',0,NULL,NULL,$id);



                    }


                }
            }


            $rsGetEmail->MoveNext();
        }
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

}