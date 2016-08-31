<?php

class features extends Controllers {
	
	public function __construct(){
		parent::__construct();
		session_start();
		$this->validasessao();
	}

    public function index() {
        $user = $_SESSION['SES_COD_USUARIO'];
        $bd = new home_model();
        $typeperson = $bd->selectTypePerson($user);
		$program = $bd->selectProgramIDByController("features/");
        $access = $this->access($user, $program, $typeperson);
        
        $smarty = $this->retornaSmarty();
        $bd = new features_model();
        $langVars = $smarty->get_config_vars();
        $get = $bd->getConfigs('1,7,10');
        $emconfigs = $bd->getArrayConfigs(5);
        $tempconfs = $bd->getTempEmail();
        $popconfs = $bd->getArrayConfigs(12);
		$ldapconfs = $bd->getArrayConfigs(13);
        while (!$get->EOF) { 
        	$features[$langVars[$get->fields['cat_smarty']]][] = array(
        		"idconfig" => $get->fields['idconfig'],
				"value" => $get->fields['value'],
				"field_type" => $get->fields['field_type'],
				"desc" => $langVars[$get->fields['smarty']]				
			);
            $get->MoveNext();
        }

        $get->Close();
		$smarty->assign('features', $features);
		$smarty->assign('emtitle', $emconfigs['EM_TITLE']);
        $smarty->assign('emhost', $emconfigs['EM_HOSTNAME']);
        $smarty->assign('domain', $emconfigs['EM_DOMAIN']);
        $smarty->assign('emuser', $emconfigs['EM_USER']);
        $smarty->assign('mainmsg', $emconfigs['SES_MAINTENANCE_MSG']);
        $smarty->assign('empassword', $emconfigs['EM_PASSWORD']);
        $smarty->assign('emsender', $emconfigs['EM_SENDER']);
        $smarty->assign('emport', $emconfigs['EM_PORT']);
        if ($emconfigs['EM_AUTH'] == 1) {
            $checked2 = 'checked';
        } else {
            $checked2 = '';
        }
        if ($emconfigs['EM_SUCCESS_LOG'] == 1) {
            $checked3 = 'checked';
        } else {
            $checked3 = '';
        }
        if ($emconfigs['EM_FAILURE_LOG'] == 1) {
            $checked4 = 'checked';
        } else {
            $checked4 = '';
        }
        if ($emconfigs['SES_MAINTENANCE'] == 1) {
            $main_chk = 1;
        } else {
            $main_chk = 0;
        }
        
        $smarty->assign('auth', $checked2);
        $smarty->assign('successcheck', $checked3);
        $smarty->assign('failurecheck', $checked4);
        $smarty->assign('idsuccesscheck', $bd->getIdBySessionName("EM_SUCCESS_LOG"));
        $smarty->assign('idfailurecheck', $bd->getIdBySessionName("EM_FAILURE_LOG"));
        $smarty->assign('maintenancecheck', $main_chk);
        $smarty->assign('emheader', $tempconfs['EM_HEADER']);
        $smarty->assign('emfooter', $tempconfs['EM_FOOTER']);
        $smarty->assign('pophost', $popconfs['POP_HOST']);
        $smarty->assign('popport', $popconfs['POP_PORT']);
        $smarty->assign('popdomain', $popconfs['POP_DOMAIN']);
        
        if ($popconfs['POP_TYPE'] == 'GMAIL') {
            $smarty->assign('poptypeg', 'selected');
            $smarty->assign('poptypep', '');
            $smarty->assign('poptypei', '');
        } else {
            if ($popconfs['POP_TYPE'] == 'POP') {
                $smarty->assign('poptypeg', '');
                $smarty->assign('poptypep', 'selected');
                $smarty->assign('poptypei', '');
            } else {
                $smarty->assign('poptypeg', '');
                $smarty->assign('poptypep', '');
                $smarty->assign('poptypei', 'selected');
            }
        }
		
		
		$smarty->assign('ldaptype', $ldapconfs['SES_LDAP_AD']);
		$smarty->assign('ldapserver', $ldapconfs['SES_LDAP_SERVER']);
		$smarty->assign('ldapdn', $ldapconfs['SES_LDAP_DN']);
		$smarty->assign('ldapdomain', $ldapconfs['SES_LDAP_DOMAIN']);
		$smarty->assign('ldapfield', $ldapconfs['SES_LDAP_FIELD']);
		
        $smarty->display('features.tpl.html');
    }

    public function configActivate() {
        extract($_POST);

        $bd = new features_model();
        $conf = $bd->activateConfig($id);
        if ($conf) {
            echo "OK";
        } else {
            return false;
        }
    }

    public function configDeactivate() {
        extract($_POST);

        $bd = new features_model();
        $conf = $bd->deactivateConfig($id);
        if ($conf) {
            echo "OK";
        } else {
            return false;
        }
    }

    public function configChangeVal() {
        extract($_POST);

        $bd = new features_model();
        if ($value == '') {
            $value = 'NULL';
        }
		
        $conf = $bd->changeVal($id, $value);
        if ($conf) {
            echo "OK";
        } else {
            return false;
        }
    }

    public function saveEmailChanges() {
        extract($_POST);

        $bd = new features_model();
        $updt = $bd->updateEmailConfigs($mailtitle, $mailhost, $maildomain, $mailuser, $mailpass, $authcheck, $mailsender, $header2, $footer2, $mailport);
        if ($updt) {
            echo "ok";
        } else {
            return false;
        }
    }
    
    public function savePopChanges() {
        extract($_POST);

        $bd = new features_model();
        $updt = $bd->updatePopConfigs($pophost, $popport, $poptype, $popdomain);
        if ($updt) {
            echo "ok";
        } else {
            return false;
        }
    }
	
	public function saveLdapChanges() {
        extract($_POST);

        $bd = new features_model();
        $updt = $bd->updateLdapConfigs($ldapserver, $ldapdn, $ldapdomain, $ldapfield, $ldaptype);
        if ($updt) {
            echo "ok";
        } else {
            return false;
        }
    }
	
    
    public function saveMaintenance(){
        extract($_POST);		
        $bd = new features_model();
		$bd->BeginTrans();
        if(!$checkMain) $checkMain = '0';
		$chk = $bd->updateMaintenance($checkMain,'SES_MAINTENANCE');
		if(!$chk){
			$bd->RollbackTrans();
			return false;
		}		
        $add = $bd->updateMaintenance(strip_tags($mainmessage),'SES_MAINTENANCE_MSG');
        if (!$add) {
            $bd->RollbackTrans();
			return false;
        }
		$bd->CommitTrans();
		echo "ok";
    }
}