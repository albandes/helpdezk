<?php
require_once(HELPDEZK_PATH . '/app/modules/emq/controllers/emqCommonController.php');

class emqEmailParent  extends emqCommon {

    public function __construct(){

        parent::__construct();
        session_start();
        $this->sessionValidate();

        // Log settings
        $this->log = parent::$_logStatus;

        $this->modulename = 'intranet' ;
        $this->program  = basename( __FILE__ );

        $this->databaseNow = ($this->database == 'oci8po' ? 'sysdate' : 'now()') ;

        $this->loadModel('emails_model');
        $dbEmails = new emails_model();
        $this->dbEmails = $dbEmails;

    }

    public function index()
    {

        $smarty = $this->retornaSmarty();

        $this->makeNavVariables($smarty,$this->modulename);
        $this->makeFooterVariables($smarty);
        $this->_makeNavEmq($smarty);

        $smarty->assign('token', $this->_makeToken()) ;
        $smarty->assign('lang_default', $this->getConfig('lang'));
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');
        $smarty->display('emq-email-parent.tpl');

    }

    public function checkEmail() {
        $arrSearch = array(".","_","-");
        $arrReplace = array("","","");

        $txtEmail = str_replace($arrSearch,$arrReplace,addslashes($_POST['txtEmail']));

        $where = "WHERE REPLACE(REPLACE(REPLACE(email,'.',''),'_',''),'-','') LIKE '$txtEmail'";

        $ret = $this->dbEmails->getEmailParent($where);
        if (!$ret) {
            if($this->log)
                $this->logIt('E-mail address verification  - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        if($ret->RecordCount() > 0){echo json_encode($this->getLanguageWord('emq_exists_email_address'));}
        else{echo json_encode(true);}

    }

    function createEmail()
    {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $enrollmentID = $_POST['enrollmentId'];
        $txtEmail = addslashes($_POST['txtEmail']);
        $txtEmail = $txtEmail;

        $this->dbEmails->BeginTrans();

        $ins = $this->dbEmails->insertEmailParent($enrollmentID,$txtEmail);

        if (!$ins) {
            $this->dbEmails->RollbackTrans();
            if($this->log)
                $this->logIt("Insert Parent's E-mail Address  - User: ".$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $this->dbEmails->CommitTrans();

        $aRet = array(
            "idhost" => $ins,
            "status" => 'OK'
        );

        echo json_encode($aRet);

    }



}