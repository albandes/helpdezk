<?php
require_once(HELPDEZK_PATH . '/app/modules/admin/controllers/admCommonController.php');

class features  extends admCommon {

    public function __construct(){

        parent::__construct();
        session_start();
        $this->sessionValidate();

        // Log settings
        $this->log = parent::$_logStatus;

        $this->program  = basename( __FILE__ );

        $this->databaseNow = ($this->database == 'oci8po' ? 'sysdate' : 'now()') ;
        

        $this->loadModel('features_model'); 
        $dbConfig = new features_model();
        $this->dbConfig = $dbConfig;

        $this->loadModel('modules_model');
        $dbModule = new modules_model();
        $this->dbModule = $dbModule;

        /*$this->loadModel('programs_model');
        $dbProgram = new programs_model();
        $this->dbProgram = $dbProgram;

        $this->loadModel('permissions_model');
        $dbPermissions = new permissions_model();
        $this->dbPermissions = $dbPermissions;

        $this->loadModel('helpdezk/groups_model');
        $dbGroups = new groups_model();
        $this->dbGroups = $dbGroups;*/

        $this->logIt("entrou  :".$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program ,7,'general',__LINE__);
    }

    public function index()
    {
        $token = $this->_makeToken();
        $this->logIt('token gerado: '.$token.' - program: '.$this->program.' - method: '. __METHOD__ ,7,'general',__LINE__);
        
        $smarty = $this->retornaSmarty();
        
        $this->makeNavVariables($smarty,'admin');
        $this->makeFooterVariables($smarty);
        $this->_makeNavAdm($smarty);
        $smarty->assign('lang_default', $this->getConfig('lang'));
        $smarty->assign('navBar', 'file:'.$this->helpdezkPath.'/app/modules/main/views/nav-main.tpl');
        $smarty->assign('token', $token) ;

        $arrModules = $this->_comboModule( "WHERE `status` = 'A'","ORDER BY `name`");
        $smarty->assign('moduleids', $arrModules['ids']);
        $smarty->assign('modulevals', $arrModules['values']);
        $smarty->assign('idmodule', 1);

        $emconfigs = $this->dbConfig->getArrayConfigs(5);
        $tempconfs = $this->dbConfig->getTempEmail();
        $popconfs = $this->dbConfig->getArrayConfigs(12);
		$ldapconfs = $this->dbConfig->getArrayConfigs(13);
        $sysconfs = $this->dbConfig->getArrayConfigs(1);

		$smarty->assign('emtitle', $emconfigs['EM_TITLE']);
        $smarty->assign('emhost', $emconfigs['EM_HOSTNAME']);
        $smarty->assign('domain', $emconfigs['EM_DOMAIN']);
        $smarty->assign('emuser', $emconfigs['EM_USER']);
        $smarty->assign('mainmsg', $emconfigs['SES_MAINTENANCE_MSG']);
        $smarty->assign('empassword', $emconfigs['EM_PASSWORD']);
        $smarty->assign('emsender', $emconfigs['EM_SENDER']);
        $smarty->assign('emport', $emconfigs['EM_PORT']);
        
        //echo"<pre>"; print_r($sysconfs); echo"</pre>";

        $smarty->assign('auth', ($emconfigs['EM_AUTH'] == 1 ? 'checked' : '' ));
        $smarty->assign('successcheck', ($emconfigs['EM_SUCCESS_LOG'] == 1 ? 'checked' : '' ));
        $smarty->assign('failurecheck', ($emconfigs['EM_FAILURE_LOG'] == 1 ? 'checked' : '' ));

        $smarty->assign('emailbycroncheck', ($emconfigs['EM_BY_CRON'] == 1 ? 'checked' : '' ));
        $smarty->assign('trackercheck', ($emconfigs['TRACKER_STATUS'] == 1 ? 'checked' : '' ));

        $smarty->assign('maintenancecheck', ($emconfigs['SES_MAINTENANCE'] == 1 ? 'checked' : '' ));
        $smarty->assign('emheader', $tempconfs['EM_HEADER']);
        $smarty->assign('emfooter', $tempconfs['EM_FOOTER']);
        $smarty->assign('pophost', $popconfs['POP_HOST']);
        $smarty->assign('popport', $popconfs['POP_PORT']);
        $smarty->assign('popdomain', $popconfs['POP_DOMAIN']);
        
        $smarty->assign('ldaptype', $ldapconfs['SES_LDAP_AD']);
		$smarty->assign('ldapserver', $ldapconfs['SES_LDAP_SERVER']);
		$smarty->assign('ldapdn', $ldapconfs['SES_LDAP_DN']);
		$smarty->assign('ldapdomain', $ldapconfs['SES_LDAP_DOMAIN']);
        $smarty->assign('ldapfield', $ldapconfs['SES_LDAP_FIELD']);
        
        $smarty->assign('summernote_version', $this->summernote);

        $arrPop['ids'] = array(0,'POP','IMAP','GMAIL');
        $arrPop['values'] = array($this->getLanguageWord('Select'),'POP','IMAP','Gmail');

        $arrLdap['ids'] = array(1,2);
        $arrLdap['values'] = array('LDAP','AD');

        $smarty->assign('poptypeids', $arrPop['ids']);
		$smarty->assign('poptypevals', $arrPop['values']);
		$smarty->assign('idpoptype', $popconfs['POP_TYPE']);
		$smarty->assign('ldaptypeids', $arrLdap['ids']);
        $smarty->assign('ldaptypevals', $arrLdap['values']);

        $smarty->assign('logGeneralChkd', ($emconfigs['LOG_GENERAL'] == 1 ? 'checked' : '' ));
        $smarty->assign('logEmailChkd', ($emconfigs['LOG_EMAIL'] == 1 ? 'checked' : '' ));
        $arrSrvLog['ids'] = array('local','remote');
        $arrSrvLog['values'] = array('Local','Remote');
        $smarty->assign('loghosttypeids', $arrSrvLog['ids']);
        $smarty->assign('loghosttypevals', $arrSrvLog['values']);
        $smarty->assign('idloghosttype', $emconfigs['LOG_HOST']);
        $smarty->assign('srvremote', $emconfigs['LOG_REMOTE_SERVER']);
        $smarty->assign('srvremoteflg', ($emconfigs['LOG_HOST'] == 'local' ? 'disabled' : '' ));
        $smarty->assign('loglevel', $emconfigs['LOG_LEVEL']);


        $smarty->assign('TwoFAuthChkd', ($sysconfs['SES_GOOGLE_2FA'] == 1 ? 'checked' : '' ));
        $smarty->assign('timesession', $sysconfs['SES_TIME_SESSION']);
        $smarty->assign('iddefcountry', $emconfigs['COUNTRY_DEFAULT']);
        $arrCountries = $this->comboCountries();
        $smarty->assign('defcountryids', $arrCountries['ids']);
        $smarty->assign('defcountryvals', $arrCountries['values']);

        $arrFieldType['ids'] = array('','checkbox','input','link');
        $arrFieldType['values'] = array($this->getLanguageWord('Select'),'Checkbox','Input','Link');
        $smarty->assign('fieldtypeids', $arrFieldType['ids']);
        $smarty->assign('fieldtypevals', $arrFieldType['values']);


        $smarty->display('features.tpl');

    }

    public function updateConfig()
    {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $arrID = explode("_",$_POST['id']);
        $prefix = $arrID[0];
        $id = $arrID[1];
        $newVal = $_POST['newVal'];

        $updt = $this->dbConfig->updateConfig($prefix,$id, $newVal);
        if ($updt) {
            echo 'OK';
        } else {
            if($this->log)
                $this->logIt('Update Config Status - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }


    }

    function saveEmailChanges()
    {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $mailtitle = addslashes($_POST['mailtitle']);
        $mailhost = addslashes($_POST['mailhost']);
        $maildomain = addslashes($_POST['maildomain']);
        $mailuser = addslashes($_POST['mailuser']);
        $mailpass = addslashes($_POST['mailpass']);
        $mailsender = addslashes($_POST['mailsender']);
        $mailport = addslashes($_POST['mailport']);
        $authcheck = isset($_POST['authcheck']) ? 1 : 0;
        $successcheck = isset($_POST['successcheck']) ? 1 : 0;
        $failurecheck = isset($_POST['failurecheck']) ? 1 : 0;
        $trackercheck = isset($_POST['trackercheck']) ? 1 : 0;
        $emailbycron = isset($_POST['emailbycron']) ? 1 : 0;
        $mailHeader = addslashes($_POST['mailHeader']);
        $mailFooter = addslashes($_POST['mailFooter']);
        
        $this->dbConfig->BeginTrans();

        $retHost = $this->dbConfig->updateConfigsVals('EM_HOSTNAME', $mailhost);
        if(!$retHost){
            $this->dbConfig->RollbackTrans();
            if($this->log)
                $this->logIt('Update Email Config: [EM_HOSTNAME] - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $retDomain = $this->dbConfig->updateConfigsVals('EM_DOMAIN', $maildomain);
        if(!$retDomain){
            $this->dbConfig->RollbackTrans();
            if($this->log)
                $this->logIt('Update Email Config: [EM_DOMAIN] - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $retUser = $this->dbConfig->updateConfigsVals('EM_USER', $mailuser);
        if(!$retUser){
            $this->dbConfig->RollbackTrans();
            if($this->log)
                $this->logIt('Update Email Config: [EM_USER] - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $retPass = $this->dbConfig->updateConfigsVals('EM_PASSWORD', $mailpass);
        if(!$retPass){
            $this->dbConfig->RollbackTrans();
            if($this->log)
                $this->logIt('Update Email Config: [EM_PASSWORD] - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $retSender = $this->dbConfig->updateConfigsVals('EM_SENDER', $mailsender);
        if(!$retSender){
            $this->dbConfig->RollbackTrans();
            if($this->log)
                $this->logIt('Update Email Config: [EM_SENDER] - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $retAuth = $this->dbConfig->updateConfigsVals('EM_AUTH', $authcheck);
        if(!$retAuth){
            $this->dbConfig->RollbackTrans();
            if($this->log)
                $this->logIt('Update Email Config: [EM_AUTH] - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $retHeader = $this->dbConfig->updateEmailConfigsHF('EM_HEADER', $mailHeader, 'description');
        if(!$retHeader){
            $this->dbConfig->RollbackTrans();
            if($this->log)
                $this->logIt('Update Email Config: [EM_HEADER] - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $retTitle = $this->dbConfig->updateConfigsVals('EM_TITLE', $mailtitle);
        if(!$retTitle){
            $this->dbConfig->RollbackTrans();
            if($this->log)
                $this->logIt('Update Email Config: [EM_TITLE] - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $retPort = $this->dbConfig->updateConfigsVals('EM_PORT', $mailport);
        if(!$retPort){
            $this->dbConfig->RollbackTrans();
            if($this->log)
                $this->logIt('Update Email Config: [EM_PORT] - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $retFooter = $this->dbConfig->updateEmailConfigsHF('EM_FOOTER',$mailFooter);
        if(!$retFooter){
            $this->dbConfig->RollbackTrans();
            if($this->log)
                $this->logIt('Update Email Config: [EM_FOOTER] - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $retSuccess = $this->dbConfig->updateConfigsVals('EM_SUCCESS_LOG', $successcheck);
        if(!$retSuccess){
            $this->dbConfig->RollbackTrans();
            if($this->log)
                $this->logIt('Update Email Config: [EM_SUCCESS_LOG] - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $retFailure = $this->dbConfig->updateConfigsVals('EM_FAILURE_LOG', $failurecheck);
        if(!$retFailure){
            $this->dbConfig->RollbackTrans();
            if($this->log)
                $this->logIt('Update Email Config: [EM_FAILURE_LOG] - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $retTracker = $this->dbConfig->updateConfigsVals('TRACKER_STATUS', $trackercheck);
        if(!$retTracker){
            $this->dbConfig->RollbackTrans();
            if($this->log)
                $this->logIt('Update Email Config: [TRACKER_STATUS] - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $retCron = $this->dbConfig->updateConfigsVals('EM_BY_CRON', $emailbycron);
        if(!$retCron){
            $this->dbConfig->RollbackTrans();
            if($this->log)
                $this->logIt('Update Email Config: [EM_BY_CRON] - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }        


        $this->dbConfig->CommitTrans();
        
        $aRet = array(
            "status"   => 'OK'
        );

        echo json_encode($aRet);

    }

    public function savePopChanges() {
        //echo"<pre>"; print_r($_POST); echo"</pre>";
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $pophost = addslashes($_POST['pophost']);
        $popType = addslashes($_POST['popType']);
        $popport = addslashes($_POST['popport']);
        $popdomain = addslashes($_POST['popdomain']);
        
        $this->dbConfig->BeginTrans();

        $retHost = $this->dbConfig->updateConfigsVals('POP_HOST', $pophost);
        if(!$retHost){
            $this->dbConfig->RollbackTrans();
            if($this->log)
                $this->logIt('Update POP Config: [POP_HOST] - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $retPort = $this->dbConfig->updateConfigsVals('POP_PORT', $popport);
        if(!$retPort){
            $this->dbConfig->RollbackTrans();
            if($this->log)
                $this->logIt('Update POP Config: [POP_PORT] - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $retDomain = $this->dbConfig->updateConfigsVals('POP_DOMAIN', $popdomain);
        if(!$retDomain){
            $this->dbConfig->RollbackTrans();
            if($this->log)
                $this->logIt('Update POP Config: [POP_DOMAIN] - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $retType = $this->dbConfig->updateConfigsVals('POP_TYPE', $popType);
        if(!$retType){
            $this->dbConfig->RollbackTrans();
            if($this->log)
                $this->logIt('Update POP Config: [POP_TYPE] - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $this->dbConfig->CommitTrans();
        
        $aRet = array(
            "status"   => 'OK'
        );

        echo json_encode($aRet);
    }
	
	public function saveLdapChanges() {
        //echo"<pre>"; print_r($_POST); echo"</pre>";
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $ldaptype = addslashes($_POST['ldaptype']);
        $ldapserver = addslashes($_POST['ldapserver']);
        $ldapdn = addslashes($_POST['ldapdn']);
        $ldapdomain = addslashes($_POST['ldapdomain']);
        $ldapfield = addslashes($_POST['ldapfield']);
        
        $this->dbConfig->BeginTrans();

        $retType = $this->dbConfig->updateConfigsVals('SES_LDAP_AD', $ldaptype);
        if(!$retType){
            $this->dbConfig->RollbackTrans();
            if($this->log)
                $this->logIt('Update LDAP Config: [SES_LDAP_AD] - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $retServer = $this->dbConfig->updateConfigsVals('SES_LDAP_SERVER', $ldapserver);
        if(!$retServer){
            $this->dbConfig->RollbackTrans();
            if($this->log)
                $this->logIt('Update LDAP Config: [SES_LDAP_SERVER] - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $retDN = $this->dbConfig->updateConfigsVals('SES_LDAP_DN', $ldapdn);
        if(!$retDN){
            $this->dbConfig->RollbackTrans();
            if($this->log)
                $this->logIt('Update LDAP Config: [SES_LDAP_DN] - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $retDomain = $this->dbConfig->updateConfigsVals('SES_LDAP_DOMAIN', $ldapdomain);
        if(!$retDomain){
            $this->dbConfig->RollbackTrans();
            if($this->log)
                $this->logIt('Update LDAP Config: [SES_LDAP_DOMAIN] - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $retField = $this->dbConfig->updateConfigsVals('SES_LDAP_FIELD', $ldapfield);
        if(!$retField){
            $this->dbConfig->RollbackTrans();
            if($this->log)
                $this->logIt('Update LDAP Config: [SES_LDAP_FIELD] - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $this->dbConfig->CommitTrans();
        
        $aRet = array(
            "status"   => 'OK'
        );

        echo json_encode($aRet);
    }
    
    public function saveMaintenance(){
        //echo"<pre>"; print_r($_POST); echo"</pre>";
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $maintenanceChk = isset($_POST['maintenanceChk']) ? 1 : 0;
        $maintenanceMsg = addslashes($_POST['maintenanceMsg']);
        
        $this->dbConfig->BeginTrans();

        $retCheck = $this->dbConfig->updateConfigsVals('SES_MAINTENANCE', $maintenanceChk);
        if(!$retCheck){
            $this->dbConfig->RollbackTrans();
            if($this->log)
                $this->logIt('Update MAINTENANCE Config: [SES_MAINTENANCE] - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $retMsg = $this->dbConfig->updateConfigsVals('SES_MAINTENANCE_MSG', strip_tags($maintenanceMsg));
        if(!$retMsg){
            $this->dbConfig->RollbackTrans();
            if($this->log)
                $this->logIt('Update MAINTENANCE Config: [SES_MAINTENANCE_MSG] - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $this->dbConfig->CommitTrans();
        
        $aRet = array(
            "status"   => 'OK'
        );

        echo json_encode($aRet);
    }

    public function saveLogChange(){
        //echo"<pre>"; print_r($_POST); echo"</pre>";
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $logGeneralChk = isset($_POST['logGeneralChk']) ? 1 : 0;
        $logEmailChk = isset($_POST['logEmailChk']) ? 1 : 0;
        $logHostType = addslashes($_POST['logHostType']);
        $logServer = addslashes($_POST['logServer']);
        $logLevel = addslashes($_POST['logLevel']);

        $this->dbConfig->BeginTrans();

        $retLogGen = $this->dbConfig->updateConfigsVals('LOG_GENERAL', $logGeneralChk);
        if(!$retLogGen){
            $this->dbConfig->RollbackTrans();
            if($this->log)
                $this->logIt('Update MAINTENANCE Config: [LOG_GENERAL] - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $retLogMail = $this->dbConfig->updateConfigsVals('LOG_EMAIL', $logEmailChk);
        if(!$retLogMail){
            $this->dbConfig->RollbackTrans();
            if($this->log)
                $this->logIt('Update MAINTENANCE Config: [LOG_EMAIL] - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $retHostType = $this->dbConfig->updateConfigsVals('LOG_HOST', $logHostType);
        if(!$retHostType){
            $this->dbConfig->RollbackTrans();
            if($this->log)
                $this->logIt('Update MAINTENANCE Config: [LOG_HOST] - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        if($logServer != ''){
            $retLogSrv = $this->dbConfig->updateConfigsVals('LOG_REMOTE_SERVER', $logServer);
            if(!$retLogSrv){
                $this->dbConfig->RollbackTrans();
                if($this->log)
                    $this->logIt('Update MAINTENANCE Config: [LOG_REMOTE_SERVER] - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                return false;
            }
        }

        $retLogLvl = $this->dbConfig->updateConfigsVals('LOG_LEVEL', $logLevel);
        if(!$retLogLvl){
            $this->dbConfig->RollbackTrans();
            if($this->log)
                $this->logIt('Update MAINTENANCE Config: [LOG_LEVEL] - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $this->dbConfig->CommitTrans();

        $aRet = array(
            "status"   => 'OK'
        );

        echo json_encode($aRet);
    }

    public function saveMiscChange(){
        //echo"<pre>"; print_r($_POST); echo"</pre>";
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $TwoFAuthChk = isset($_POST['TwoFAuthChk']) ? 1 : 0;
        $cbmDefCountry = addslashes($_POST['cbmDefCountry']);
        $timeSession = $_POST['timeSession'] == '' ? '600' : addslashes($_POST['timeSession']);

        $this->dbConfig->BeginTrans();

        $ret2FAuth = $this->dbConfig->updateConfigsVals('SES_GOOGLE_2FA', $TwoFAuthChk);
        if(!$ret2FAuth){
            $this->dbConfig->RollbackTrans();
            if($this->log)
                $this->logIt('Update MAINTENANCE Config: [SES_GOOGLE_2FA] - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $retDefCountry = $this->dbConfig->updateConfigsVals('COUNTRY_DEFAULT', $cbmDefCountry);
        if(!$retDefCountry){
            $this->dbConfig->RollbackTrans();
            if($this->log)
                $this->logIt('Update MAINTENANCE Config: [COUNTRY_DEFAULT] - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $retTime = $this->dbConfig->updateConfigsVals('SES_TIME_SESSION', $timeSession);
        if(!$retTime){
            $this->dbConfig->RollbackTrans();
            if($this->log)
                $this->logIt('Update MAINTENANCE Config: [SES_TIME_SESSION] - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $this->dbConfig->CommitTrans();

        $aRet = array(
            "status"   => 'OK'
        );

        echo json_encode($aRet);
    }

    public function loadModuleConfs(){

        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $idmodule = $_POST['idmodule'];
        $rsModule = $this->dbModule->selectModuleData($idmodule);
        if(!$rsModule){
            if($this->log)
                $this->logIt("Get Module's Data - User: ".$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $prefix = $rsModule->fields['tableprefix'];

        if ( $this->dbConfig->tableExists($prefix . '_tbconfig') == 0 ){
            echo "<div class='alert-warning'>{$this->getLanguageWord('No_result')}</div>";
            exit;
        }

        $table = $prefix.'_tbconfig_category';
        $where = "WHERE flgsetup = 'Y'";
        $order = "ORDER BY idconfigcategory";

        $rsCategories = $this->dbConfig->getConfigCategories($table,$where,$order);
        if(!$rsCategories){
            if($this->log)
                $this->logIt("Get Module's Confs Categories - User: ".$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $categoriesID = '';
        while(!$rsCategories->EOF){
            $categoriesID .= $rsCategories->fields['idconfigcategory'].',';
            $rsCategories->MoveNext();
        }

        $categoriesID = substr($categoriesID,0,-1);

        if ( $categoriesID == '' ){
            echo "<div class='alert-warning'>{$this->getLanguageWord('No_result')}</div>";
            exit;
        }

        $get = $this->dbConfig->getConfigs($prefix,$categoriesID);
        if(!$get){
            if($this->log)
                $this->logIt("Get Module's Confs - User: ".$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }
        $idmoduletmp = 0;
        $i = 1;
        $list = "";

        while (!$get->EOF) {
            //echo $get->fields['idconfig'].': '.$get->fields['config_name'].'<br>';
            if ($idmoduletmp != $get->fields["cate"]){
                $catName = ($get->fields['cat_smarty'] && $get->fields['cat_smarty'] != '')
                            ? $this->getLanguageWord($get->fields['cat_smarty'])
                                ? $this->getLanguageWord($get->fields['cat_smarty']) : $get->fields['cat_name']
                            : $get->fields['cat_name'];
                
                if($i != 1)
                    $list .= "</tbody></table></div></div>";
                
                    $list .= "<div class='panel panel-default'>
                            <div class='panel-heading'>
                                <i class='fa fa-cog' aria-hidden='true'></i>&nbsp;".$catName."
                            </div>
                            <div class='panel-body'>
                                <table class='table table-striped'>
                                    <colgroup>
                                        <col class='col-sm-4'>
                                        <col class='col-sm-7'>
                                        <col class='col-sm-1'>
                                    </colgroup>
                                    <tbody>";
            }

            switch($get->fields["field_type"]){
                case 'input':
                    $content = "<div class='row text-center'>
                                    <div class='form-group '> 
                                        <div class='col-sm-4'>
                                            &nbsp;
                                        </div>
                                        <div class='col-sm-4 text-center'>
                                            <input type='text' value='{$get->fields['value']}' class='text-center form-control input-sm changeConfigValue' id='{$prefix}_{$get->fields['idconfig']}' data-id='{$prefix}{$get->fields['idconfig']}' />
                                        </div> 
                                        <div class='col-sm-4'>
                                            &nbsp;
                                        </div>
                                    </div>                                     
                                </div>";
                    break;
                
                case 'checkbox':
                    $checked = $get->fields['value'] == 1 ? "checked='checked'" : "";
                    $content = "<div class='i-checks'>
                                    <label>
                                        <input type='checkbox' id='{$prefix}_{$get->fields['value']}' $checked class='changeConfigStatus' value='{$prefix}_{$get->fields['idconfig']}' />
                                    </label>
                                </div>";
                    break;
                
                default:
                    $content = "<div>
                                    <input type='text' value='{$get->fields['value']}' class='text-center form-control input-sm changeConfigValue' id='{$prefix}_{$get->fields['idconfig']}' data-id='{$prefix}{$get->fields['idconfig']}' />                                     
                                </div>";
                    break;

            }

            $confName = ($get->fields['smarty'] && $get->fields['smarty'] != '')
                            ? $this->getLanguageWord($get->fields['smarty'])
                                ? $this->getLanguageWord($get->fields['smarty']) : $get->fields['config_name']
                            : $get->fields['config_name'];
            $removeIco = $get->fields['allowremove'] == 'Y'
                        ? '<a href="javascript:;" class="btn btn-danger btn-xs tooltip-buttons removeConfig" data-toggle="tooltip" data-placement="top" title="'.$this->getLanguageWord('Feature_remove').'" data-id="'.$prefix.'_'.$get->fields['idconfig'].'"><i class="fa fa-trash-alt"></i></a>'
                        : "";
            
            $list .= "<tr>
                        <td class='text-center'>
                            $content
                        </td>
                        <td >
                            {$confName}
                        </td>
                        <td class='text-center'>
                            $removeIco
                        </td>
                    </tr>";
            
            $idmoduletmp = $get->fields['cate'];
            $i++;
            $get->MoveNext();
        }

        echo $list;
    }

    public function ajaxFeatureCategory(){

        $idmodule = $_POST['moduleID'];
        
        $rsModule = $this->dbModule->selectModuleData($idmodule);
        if(!$rsModule){
            if($this->log)
                $this->logIt("Get Module's Data - User: ".$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $table = $idmodule == 1 ? "tbconfig_category" : $rsModule->fields['tableprefix']."_tbconfig_category";

        echo $this->comboFeatureCategoryHtml($table,"WHERE flgsetup = 'Y'","ORDER BY `name`");
        
    }

    public function comboFeatureCategory($table,$where=null,$order=null)
    {
        $rs = $this->dbConfig->getConfigCategories($table,$where,$order);
        while (!$rs->EOF) {
            $fieldsID[] = $rs->fields['idconfigcategory'];
            $values[]   = $rs->fields['name'];
            $rs->MoveNext();
        }

        $arrRet['ids'] = $fieldsID;
        $arrRet['values'] = $values;

        return $arrRet;
    }

    public function comboFeatureCategoryHtml($table,$where=null,$order=null)
    {

        $arrType = $this->comboFeatureCategory($table,$where,$order);
        $select = '';

        foreach ( $arrType['ids'] as $indexKey => $indexValue ) {
            $select .= "<option value='$indexValue'>".$arrType['values'][$indexKey]."</option>";
        }
        return $select;
    }

    public function saveNewFeature(){

        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $idmodule = $_POST['idmoduleAddFeat'];
        $idconfigcategory = $_POST['cmbFeatureCat'];
        $name = addslashes($_POST['txtNewFeature']);
        $description = addslashes($_POST['newFeatureDesc']);
        $sessionName = addslashes(str_replace(' ','_',$_POST['newFeatureSessionName']));
        $smartyVar = addslashes(str_replace(' ','_',$_POST['newFeatureSmartyVar']));
        $fieldType = $_POST['cmbFieldTypeMod'] == 'X' ? '' : $_POST['cmbFieldTypeMod'];
        $value = $_POST['cmbFieldTypeMod'] == "checkbox"
                 ? isset($_POST['valCheckFeature']) ? 1 : 0
                 : addslashes($_POST['valInputFeature']);
        $flgDefault = isset($_POST['featureDefault']) ? 'N' : 'Y';

        if($idmodule == 1){
            $table = "tbconfig";
        }else{
            $rsModule = $this->dbModule->selectModuleData($idmodule);
            if(!$rsModule){
                if($this->log)
                    $this->logIt("Get Module's Data - User: ".$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                return false;
            }

            $table = $rsModule->fields['tableprefix']."_tbconfig";

            if ( $this->dbConfig->tableExists($table) == 0 ){
                if($this->log)
                    $this->logIt("Table " . $table . " doesn't exist - program: ".$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                return false;
            }
        }

        $this->dbConfig->BeginTrans();

        $ins = $this->dbConfig->insertFeature($table,$name,$description,$idconfigcategory,$sessionName,$fieldType,$smartyVar,$value,$flgDefault);
        if(!$ins){
            $this->dbConfig->RollbackTrans();
            if($this->log)
                $this->logIt('Error to try insert New Feature - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $this->dbConfig->CommitTrans();

        $aRet = array(
            "status"   => 'OK',
            "idfeature" => $ins
        );

        echo json_encode($aRet);
    }

    public function removeConfig()
    {
        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $arrID = explode("_",$_POST['id']);
        $prefix = $arrID[0];
        $id = $arrID[1];

        $del = $this->dbConfig->removeConfig($prefix,$id);
        if ($del) {
            echo 'OK';
        } else {
            if($this->log)
                $this->logIt('Remove Config - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }


    }

    public function checkField() {
        $value = $_POST['searchval'];
        $idmodule = $_POST['moduleId'];
        $fieldName = $_POST['fieldName'];

        $rsModule = $this->dbModule->selectModuleData($idmodule);
        if(!$rsModule){
            if($this->log)
                $this->logIt("Get Module's Data - User: ".$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $prefix = $rsModule->fields['tableprefix'];

        $check = $this->dbConfig->checkField($prefix,$fieldName,$value);
        if ($check->fields) {
            echo json_encode($this->getLanguageWord('Value_exists'));
        } else {
            echo json_encode(true);
        }
    }

    public function checkCategory() {
        $value = $_POST['txtNewCategory'];
        $idmodule = $_POST['moduleId'];

        $rsModule = $this->dbModule->selectModuleData($idmodule);
        if(!$rsModule){
            if($this->log)
                $this->logIt("Get Module's Data - User: ".$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $prefix = $rsModule->fields['tableprefix'];

        $check = $this->dbConfig->checkCategory($prefix,$value);
        if ($check->fields) {
            echo json_encode($this->getLanguageWord('Value_exists'));
        } else {
            echo json_encode(true);
        }
    }

    public function saveNewCategory(){

        if (!$this->_checkToken()) {
            if($this->log)
                $this->logIt('Error Token: '.$this->_getToken().' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $idmodule = $_POST['idmoduleAddCateg'];
        $name = addslashes($_POST['txtNewCategory']);
        $smartyVar = addslashes($_POST['newCategorySmartyVar']);

        if($idmodule == 1){
            $table = "tbconfig_category";
        }else{
            $rsModule = $this->dbModule->selectModuleData($idmodule);
            if(!$rsModule){
                if($this->log)
                    $this->logIt("Get Module's Data - User: ".$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                return false;
            }

            $table = $rsModule->fields['tableprefix']."_tbconfig_category";

            if ( $this->dbConfig->tableExists($table) == 0 ){
                if($this->log)
                    $this->logIt("Table " . $table . " doesn't exist - program: ".$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
                return false;
            }
        }

        $this->dbConfig->BeginTrans();

        $ins = $this->dbConfig->insertFeatureCategory($table,$name,$smartyVar,'Y');
        if(!$ins){
            $this->dbConfig->RollbackTrans();
            if($this->log)
                $this->logIt('Error to try insert New Feature - User: '.$_SESSION['SES_LOGIN_PERSON'].' - program: '.$this->program.' - method: '. __METHOD__ ,3,'general',__LINE__);
            return false;
        }

        $this->dbConfig->CommitTrans();

        $aRet = array(
            "status"   => 'OK',
            "idcategory" => $ins
        );

        echo json_encode($aRet);
    }

}