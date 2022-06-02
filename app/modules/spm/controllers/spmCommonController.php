<?php

require_once(HELPDEZK_PATH . '/includes/classes/pipegrep/syslog.php');

/*
 *  Common methods - Spm Module
 */


class spmCommon extends Controllers  {


    public static $_logStatus;

    public function __construct()
    {

        parent::__construct();

        $this->loadModel('atleta_model');
        $dbAtleta = new atleta_model();
        $this->dbAtleta = $dbAtleta;

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

    }

    public function _makeNavSpm($smarty)
    {
        $idPerson = $_SESSION['SES_COD_USUARIO'];
        $listRecords = $this->makeMenuBycategory($idPerson,$this->idmodule,9);

        $smarty->assign('displayMenu_1',1);
        $smarty->assign('listMenu_1',$listRecords);
    }

    public function _getNumAtletas($where = null)
    {

        $rs = $this->dbAtleta->getAtleta($where);
        return $rs->RecordCount();

    }

    public function _getAtleta($where = null, $order = null , $group = null , $limit = null)
    {
        $rs = $this->dbAtleta->getAtleta($where, $order , $group , $limit);
        return $rs;

    }
    public function _comboAtletaPosicao()
    {
        $this->loadModel('atleta_model');
        $dbAtleta = new atleta_model();

        $rs = $dbAtleta->getAtletaPosicao();
        while (!$rs->EOF) {
            $fieldsID[] = $rs->fields['idposicao'];
            $values[]   = $rs->fields['nome'];
            $rs->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;

    }

    public function _comboAtletaCondicao()
    {
        $this->loadModel('atleta_model');
        $dbAtleta = new atleta_model();

        $rs = $dbAtleta->getAtletaCondicao();
        while (!$rs->EOF) {
            $fieldsID[] = $rs->fields['idcondicao'];
            $values[]   = $rs->fields['nome'];
            $rs->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;

    }

    public function _comboAtletaDepartamento()
    {
        $this->loadModel('atleta_model');
        $dbAtleta = new atleta_model();

        $rs = $dbAtleta->getAtletaDepartamento();
        while (!$rs->EOF) {
            $fieldsID[] = $rs->fields['iddepartamento'];
            $values[]   = $rs->fields['nome'];
            $rs->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;

    }

    public function _sendNotification($transaction=null,$midia='email',$code_request=null,$hasAttachment=null)
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
                        if ($_SESSION['SEND_EMAILS'] == '1' &&
                            $_SESSION['USER_NEW_NOTE_MAIL'] == '1' &&
                            $_SESSION['SES_ATTACHMENT_OPERATOR_NOTE'] == '1') {  // Send e-mail
                            if ( $_SESSION['EM_BY_CRON'] == '1') {
                                $cron = true;
                            } else {
                                $smtp = true;
                            }
                        }

                    } else {
                        if ($_SESSION['SEND_EMAILS'] == '1' &&
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
                    if ($_SESSION['SEND_EMAILS'] == '1' &&
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
                    if ($_SESSION['SEND_EMAILS'] == '1' &&
                        $_SESSION['EM_EVALUATED']) {

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
                    if ($_SESSION['SEND_EMAILS'] == '1' &&
                        $_SESSION['NEW_REQUEST_OPERATOR_MAIL']) {

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
                $this->loadModel('hdkCommon');
                $hdkCommon = new hdkCommon();
                $hdkCommon->_sendEmail($messageTo , $code_request);
            }

        }

        return true ;
    }
}