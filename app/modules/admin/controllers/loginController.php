<?php

require_once(HELPDEZK_PATH . '/app/modules/admin/controllers/admCommonController.php');

class Login extends admCommon {
    protected $dbIndex, $dbConfig;

    public function __construct()
    {
        parent::__construct();

        $this->program  = basename( __FILE__ );

        $this->loadModel('index_model');
        $dbIndex = new index_model();
        $this->dbIndex = $dbIndex ;

        $this->loadModel('features_model');
        $dbConfig = new features_model();
        $this->dbConfig = $dbConfig;

        /*
         * It's necessary because we need the global variables to set log method
         */
        $this->getGlobalSessionData();

        // Log settings
        $this->log = parent::$_logStatus;


    }

    public function index() {

        session_start();
        session_unset();
        session_destroy();

        $smarty = $this->retornaSmarty();

        $database = $this->getConfig('db_connect');
        if ($this->isMysql($database) ) {
            // mysql_connect is deprecated as of PHP 5.5 and was removed in PHP 7
            if (defined('PHP_MAJOR_VERSION') && PHP_MAJOR_VERSION >= 5.5) {
                if (!function_exists('mysqli_connect')) die("MYSQL functions are not available !!!");
            } else {
                if (!function_exists('mysql_connect')) die("MYSQL functions are not available !!!");
            }
        } elseif ($database == 'oci8po') {
            if (!function_exists('oci_connect')) die("ORACLE functions are not available !!!");
        }

        $this->loadModel('logos_model');
        $db = new logos_model();

        /*
        $rsLogo = $db->getLoginLogo();

        $smarty->assign('loginlogo', $this->getLoginLogoImage());
        $smarty->assign('height',    $this->getLoginLogoHeight());
        $smarty->assign('width',     $this->getLoginLogoWidth());
        */

        // Set Login Logo
        $smarty->assign('loginLogoUrl',$this->getLoginLogoFullUrl());
        $smarty->assign('loginheight', $this->getLoginLogoHeight());
        $smarty->assign('loginwidth', $this->getLoginLogoWidth());

        $smarty->assign('path',      path);
        $smarty->assign('lang_default', $this->getConfig('lang'));
        $smarty->assign('hdk_url', $this->getConfig('hdk_url'));
        $smarty->assign('theme', $this->getTheme());
        $smarty->assign('jquery_version', $this->jquery);

        // Log Directory
        if (!file_exists($this->helpdezkPath.'/logs'))
            mkdir($this->helpdezkPath.'/logs', 0777, true) ;

        $smarty->assign('version', $this->helpdezkName);

        // Demo version - Since January 29, 2020
        $demoVersion = (empty($this->getConfig('demo')) ? false : $this->getConfig('demo'));
        $smarty->assign('demoversion', $demoVersion);

		if($this->getConfig('enterprise')) {
			$smarty->assign('site', 'HelpDEZK.cc');
			$smarty->assign('urlsite', 'http://www.helpdezk.cc');
		} else {
			$smarty->assign('site', 'HelpDEZK.org');
			$smarty->assign('urlsite', 'http://helpdezk.org');		
		}

        $this->loadModel('warning_model');
		$dbWarning = new warning_model();

        $database = $this->getConfig('db_connect');

        if ($this->isMysql($database)) {
			$and = "AND (a.dtend > NOW() AND a.dtstart <= NOW() OR a.dtend = '0000-00-00 00:00:00') AND a.showin IN (2,3)" ;
		} elseif ($database == 'oci8po') {
			$and = "AND (a.dtend > SYSDATE AND a.dtstart <= SYSDATE OR a.dtend IS NULL) AND a.showin IN (2,3)" ;
		}

        $rsWarning = $dbWarning->selectWarning($and,"ORDER BY dtstart ASC");

		$i = 0;

		while (!$rsWarning->EOF) {
			$warning[$i]['idmessage']    = $rsWarning->fields['idmessage'];
			$warning[$i]['title_topic']   = $rsWarning->fields['title_topic'];
			$warning[$i]['title_warning'] = $rsWarning->fields['title_warning'];
			$i++;
            $rsWarning->MoveNext();	 	
		}
		
		$smarty->assign('warning', $warning);
        $smarty->assign('license', $this->getConfig("license"));


		$smarty->display('login_two_columns.tpl');

    }

    public function getWarning()
    {

        $id = $this->getParam('id');

        $this->loadModel('warning_model');
        $dbWarning = new warning_model();

        $rsWarning = $dbWarning->selectWarning("AND a.idmessage = $id");
        $database = $this->getConfig('db_connect');

        //$smarty->assign('title_topic', $rsWarning->fields['title_topic']);
        //$smarty->assign('title_warning', $rsWarning->fields['title_warning']);
        //$smarty->assign('description', $rsWarning->fields['description']);


        if ($this->isMysql($database)) {
            $datestart =  $this->formatDate($rsWarning->fields['dtstart']);

            $timestart = $this->formatHour($rsWarning->fields['dtstart']);

        } elseif ($database == 'oci8po') {
            $datestart = $rsWarning->fields['dtstart'];
            $timestart = '';
        }

        if($rsWarning->fields['dtend'] == "0000-00-00 00:00:00" || empty($rsWarning->fields['dtend'])){
            $until = true;

        }else{
            if ($this->isMysql($database)) {
                $dateend = $this->formatDate($rsWarning->fields['dtend']);
                $timeend = $this->formatHour($rsWarning->fields['dtend']);
            } elseif ($database == 'oci8po') {
                $dateend = $rsWarning->fields['dtend'];
                $timeend = '';
            }

        }

        $validMessage = $this->getLanguageWord('Valid') . ': ' . $datestart . ' ' . $timestart . ' ';
        if ($until) {
            $validMessage .=  $this->getLanguageWord('until_closed') ;
        } else {
            $validMessage .= $this->getLanguageWord('until') . ' ' .$dateend . ' ' . $timeend;
        }

        $arrJson[] = array("title_topic"    => $rsWarning->fields['title_topic'],
                           "title_warning"  => $rsWarning->fields['title_warning'],
                           "description"    => $rsWarning->fields['description'],
                           "valid_msg"      => $validMessage);

        echo json_encode($arrJson);
    }

    public function getWarningInfo(){
		$smarty = $this->retornaSmarty();
		$id = $this->getParam('id');
        $this->loadModel('warning_model');
		$bdw = new warning_model();
        $rsWarning = $bdw->selectWarning("AND a.idmessage = $id");
		$database = $this->getConfig('db_connect');
		
		$smarty->assign('title_topic', $rsWarning->fields['title_topic']);
		$smarty->assign('title_warning', $rsWarning->fields['title_warning']);
		$smarty->assign('description', $rsWarning->fields['description']);
		
        
        if ($database == 'mysqlt') {
            $smarty->assign('datestart', $this->formatDate($rsWarning->fields['dtstart']));
            $smarty->assign('timestart', $this->formatHour($rsWarning->fields['dtstart']));
        } elseif ($database == 'oci8po') {
            $smarty->assign('datestart', $rsWarning->fields['dtstart']);
            $smarty->assign('timestart', '');
        }
        		
		if($rsWarning->fields['dtend'] == "0000-00-00 00:00:00" || empty($rsWarning->fields['dtend'])){
			$smarty->assign('until', 'S');
		}else{
		  if ($database == 'mysqlt') {
                $smarty->assign('dateend', $this->formatDate($rsWarning->fields['dtend']));
                $smarty->assign('timeend', $this->formatHour($rsWarning->fields['dtend']));
            } elseif ($database == 'oci8po') {
                $smarty->assign('dateend', $rsWarning->fields['dtend']);
                $smarty->assign('timeend', '');
            }
			$smarty->assign('until', 'N');
		}

		
		$smarty->assign('showin', $rsWarning->fields['showin']);
		
		
		$smarty->display('modais/login/warning.tpl.html');
	}

    public function auth() {

        $smarty = $this->retornaSmarty();

        $langVars = $this->getLangVars($smarty);


		//$ProtectSql = $this->returnProtectSql();
		//$ProtectSql->start("aio","all");

        $frm_login = $_POST['login'];
        $frm_password = $_POST['password'];
        $passwordMd5 = md5($_POST['password']);
        $form_token = $_POST['token'];

        $this->loadModel('person_model');
        $dbPerson = new person_model();

        $rsLogintype = $this->dbIndex->getTypeLogin($frm_login);
		$logintype = $rsLogintype->fields['idtypelogin'];

        if($this->log)
            $this->logIt("Login type: ". $logintype . ' - User: '.$frm_login .' - program: '.$this->program ,5,'general',__LINE__);


		if(!$logintype){

            if($_SESSION['SES_LDAP_ADD_USER'] == 1){
                /*
                 *  If the user logs in to LDAP/AD:
                 *    1. Creates a new user if it doesn't exist;
                 *    2. Set type login to 2
                 */

                
                $login = $this->ldapAuth($frm_login,$frm_password);
                
                if (!$login) {
                    if($this->log)
                        $this->logIt("User unknow in LDAP/AD: " . $frm_login .' - program: '.$this->program ,3,'general',__LINE__);
        
                    $success = array(
                        "success" => 0,
                        "msg" => html_entity_decode($langVars['Login_user_not_exist'],ENT_COMPAT, 'UTF-8')
                    );
                    echo json_encode($success);
                    return;
                }

                

                $ldapData = $this->ldapGetData($frm_login,$frm_password);
                
                $email =  $ldapData['email'];
                $name  =  $ldapData['displayname']   ;

                $department  =  $ldapData['department']   ;
                if (empty($department))
                    $department = "Departamento default";                
                
                $license =  $this->getConfig("license");    
                if($license == '202201001')     
                    $idCompany = 1038;

                $this->loadModel('helpdezk/department_model');
                $dbDepartment = new department_model();
                            
                $where = "WHERE name = pipeLatinToUtf8('$department')";

                $rsDepartment = $dbDepartment->getDepartmentData($where);

                //print_r($rsDepartment->fields);
                if ($rsDepartment->RecordCount() == 0) {

                    $ret = $dbDepartment->insertDepartment($idCompany,$department);

                    $idDepartment = $dbDepartment->InsertID();
                    if($this->log)
                        $this->logIt("Department created: ". $department . ' - Id: '. $idDepartment .' - program: '.$this->program ,5,'general',__LINE__);    

                } else {
                    $idDepartment = $rsDepartment->fields['iddepartment'];
                }   
                

                $dbPerson->BeginTrans();

                $dtcreate = date('Y-m-d H:i:s');
                $logintype = 2 ; // Need in the first access

                
                $idNewPerson = $dbPerson->insertPerson($logintype,'2','1','1',$name,$email,$dtcreate,'A','N','','','',$frm_login);
                if (!$idNewPerson) {
                    $error = true ;
                    if($this->log)
                        $this->logIt("Error insert user: " . $frm_login .' - program: '.$this->program ,3,'general',__LINE__);
                }
                if (!$error) {
                    $insNatural = $dbPerson->insertNaturalData($idNewPerson, '', '', '');
                    if (!$insNatural) {
                        $error = true;
                        if($this->log)
                            $this->logIt("Error insert user: " . $frm_login .' - program: '.$this->program ,3,'general',__LINE__);
                    }
                }
                
                if (!$error) {
                    $depart = $dbPerson->insertInDepartment($idNewPerson, $idDepartment);
                    if (!$depart) {
                        $error = true ;
                        if($this->log)
                            $this->logIt("Error insert user: " . $frm_login .' - program: '.$this->program ,3,'general',__LINE__);
                    }
                }
                    

                if($error){
                    $dbPerson->RollbackTrans();
                    $success = array(
                        "success" => 0,
                        "msg" => html_entity_decode($langVars['Login_cant_create_user'],ENT_COMPAT, 'UTF-8')
                    );
                    echo json_encode($success);
                    return;
                } else {
                    if($this->log)
                        $this->logIt("User LDAP/AD add in Helpdezk: " . $frm_login .' - program: '.$this->program ,5,'general',__LINE__);
 
                    $dbPerson->CommitTrans();
                }

            
            } else {
                // Return with error message
                $success = array(
                    "success" => 0,
                    "msg" => html_entity_decode($langVars['Login_user_not_exist'],ENT_COMPAT, 'UTF-8')
                );
                echo json_encode($success);
                return;            
            }

		}

        switch ($logintype) {
            case '4': // Request

                $login = $this->requestAuth($frm_login,$frm_password);
                $idperson = $this->dbIndex->getIdPerson($frm_login);
                break;

            case '3': // HelpDEZk   

                $login = $this->helpdezkAuth($frm_login,$passwordMd5);
                $idperson = $this->dbIndex->selectDataLogin($frm_login, $passwordMd5);
                break;

            case '1': // Pop/Imap Server
                if (function_exists('imap_open')) {
                    $login = false ;
                    $msg = "IMAP functions are not available!!!";
                    break;
                }
                $login = $this->imapAuth($frm_login,$frm_password);
                $idperson = $this->dbIndex->getIdPerson($frm_login);
                break;

            case '2': // AD/LDAP				
				if (!function_exists('ldap_connect')) {
					$login = false ;
					$msg = "LDAP functions are not available!!!";
					break;
				}

                $login = $this->ldapAuth($frm_login,$frm_password);
                $idperson = $this->dbIndex->getIdPerson($frm_login);
                break;
        }

        if ($login) {

            $idtypeperson =  $dbPerson->getIdTypePerson($idperson);

            /*
             *
             *   Login with google authenticator
             *   Second authentication
             *
             */

            $google2fa = $this->dbIndex->getConfigValue('SES_GOOGLE_2FA');

            if (empty($google2fa))  // if don't exists in hdk_tbconfig [old versions before 1.02]
                $google2fa = 0 ;

            /*
             *
             */
            if ($google2fa) {
                if ($idperson != 1)
                {
                    if($this->getConfig("license") == '200701006') {
                        $iddepartment = $this->dbIndex->getIdPersonDepartment($frm_login) ; //die('id: ' . $iddepartment) ;
                        //$typePerson   = $dbPerson->getIdTypePerson($idperson) ;
                        if($iddepartment == '314') {
                            $makeSecret = true;
                        } else {
                            $makeSecret = false;
                        }
                        //elseif($typePerson == 3) {
                        //    $makeSecret = false;
                        // }
                    } else {
                        $makeSecret = true ;
                    }
                }
                elseif ($idperson == 1)  // root user
                {
                    $makeSecret = false ;
                }

                if ($makeSecret)
                {
                    if ((include 'includes/classes/GoogleAuthenticator/GoogleAuthenticator.php') == false) {
                        die("Don't include the class GoogleAuthenticator.php, line ".__LINE__ . "!!!");
                    }

                    $ga = new PHPGangsta_GoogleAuthenticator();

                    $oneCode = $form_token ;
                    $token = $dbPerson->getPersonSecret($idperson) ;

                    if ($token) {
                        $checkResult = $ga->verifyCode($token, $oneCode, 2);

                        if(!$checkResult){
                            $success = array(
                                "success" => 0,
                                "msg"     => html_entity_decode($langVars['Login_error_secret'],ENT_COMPAT, 'UTF-8')
                            );
                            echo json_encode($success);
                            return;
                        }
                    }
                    else
                    {
                        // Person don´t have secret in database,
                        //  On your screen will show the modal to record the secret
                    }
                }
            }

            switch  ($idtypeperson) {
                case "1":
                    $this->_startSession($idperson);
                    $this->_getConfigSession();
                    $success = array(
                        "success" => 1,
                        "redirect" => path . "/admin/home"
                    );
                    echo json_encode($success);
                    return;
                    break;

                case "2":
                    $this->_startSession($idperson);
                    $this->_getConfigSession();
                    if($_SESSION['SES_MAINTENANCE'] == 1){						
						$maintenance = array(
										"success" => 0,
										"msg" =>html_entity_decode($_SESSION['SES_MAINTENANCE_MSG'],ENT_COMPAT, 'UTF-8')
									);					
						echo json_encode($maintenance);
						return;
                    }else{
                        $redirect = path . "/" . $_SESSION['SES_ADM_MODULE_DEFAULT'] . "/home/index" ;

                        $success = array( "success" => 1,
                                          "redirect" => $redirect	);
                        echo json_encode($success);

                        return;
                    }
                    break;

                case "3":
                    $this->_startSession($idperson);
                    $this->_getConfigSession();
                    if($_SESSION['SES_MAINTENANCE'] == 1){
						$maintenance = array(
										"success" => 0,
										"msg" =>html_entity_decode($_SESSION['SES_MAINTENANCE_MSG'],ENT_COMPAT, 'UTF-8')
									);
						echo json_encode($maintenance);
					}else{
						$success = array(
										"success" => 1,
										"redirect" => path . "/helpdezk/home/index"
									);

						echo json_encode($success);
						return;
					}
                    break;

                //  Another modules
                default:

                    $this->_startSession($idperson);
                    $this->_getConfigSession();

                    if (!$this->dbConfig->tableExists('tbtypeperson_has_module')) {
                        $error = array( "success" => 0,
                                         "msg" =>html_entity_decode('There is no table tbtypeperson_has_module.',ENT_COMPAT, 'UTF-8')
                                      );
                        echo json_encode($error);
                        return;
                    }
                    $ret = $this->dbIndex->getPathModuleByTypePerson($idtypeperson);
                    if ($ret) {
                        if($_SESSION['SES_MAINTENANCE'] == 1){
                            $maintenance = array(
                                "success" => 0,
                                "msg" =>html_entity_decode($_SESSION['SES_MAINTENANCE_MSG'],ENT_COMPAT, 'UTF-8')
                            );
                            echo json_encode($maintenance);
                        }else{
                            $success = array(
                                "success" => 1,
                                "redirect" => path . "/{$ret}/home/index"
                            );
                            echo json_encode($success);
                            return;
                        }

                    } else {
                        $error = array(
                            "success" => 0,
                            "msg" =>html_entity_decode('User type has no linked module',ENT_COMPAT, 'UTF-8')
                        );
                        echo json_encode($error);
                        return;

                    }
					return;
                    break;
            }
        } else {
			if ($logintype == 1 or $logintype == 3 or $logintype == 4) { // Pop, HD  ou REQUEST login
				$rs = $this->dbIndex->checkUser($login);
				if($rs == "A") $msg = $langVars['Login_error_error'];
				elseif($rs == "I") $msg = $langVars['Login_user_inactive'];
			}
            if($logintype == 2){ // LDAP
                $msg = $langVars['Login_error_error'];
            }
			$success = array("success" => 0,
							 "msg" => $msg );
			echo json_encode($success);
			return;
        }        
    }





	/**
	* Create the condition for the query of mysql from the dates generated by the calendar of the form
	*
	* @access public
	* @param string $login User login.
	* @return string true|false 
	*/
    public function lostPassword() {

        $login = addslashes($_POST['username']);

        $this->loadModel('index_model');
        $dbIndex = new index_model();
        $logintype = $dbIndex->getTypeLogin($login);
        
		$idperson = $dbIndex->getIdPerson($login);
		if($idperson == 1) {
			echo "MASTER";
			exit;
		}

        if ($logintype->fields) 
		{
            if ($logintype->fields['idtypelogin'] == 1 ) // POP  
			{
				echo 1;
				exit;
			}
            if ($logintype->fields['idtypelogin'] == 2 ) // AD  
			{
				echo 2;
				exit;
			}

            $pass = $this->generateRandomPassword(8, false, true, false);

			$idperson = $dbIndex->getIdPerson($login);
			$password = md5($pass);

            $this->loadModel('admin/person_model');
			$data = new person_model();
			$change = $data->changePassword($idperson, $password);
			
			if (!$change) {
				echo ('ERROR');
				exit;
			} 

			$smarty = $this->retornaSmarty();

			$subject = $smarty->getConfigVars('Lost_password_subject');
			$body = $smarty->getConfigVars('Lost_password_body');
			$log_text = $smarty->getConfigVars('Lost_password_log');
			
			eval("\$body = \"$body\";");

            $address = $dbIndex->getEmailPerson($login);

            $params = array("subject" => $subject,
                "contents" => $body,
                "address" => $address,
                "idmodule" => $this->idmodule,
                "tracker" => $this->tracker,
                "msg" => $log_text,
                "msg2" => $log_text
            );

            $done = $this->sendEmailDefault($params);

            if (!$done) {
                return false ;
            } else {
                echo $logintype->fields['idtypelogin'];
            }
        }
		else
		{
			echo "NOT_EXISTS" ;
			exit ;
		}
		
		
    }

	public function LdapValidate($AdConnect, $dn, $pass, $userdomain, $type)
	{	

		$dn = utf8_encode($dn);
		$pass = utf8_encode($pass);
		
		if($type == 1)
			$bind = @ldap_bind($AdConnect,$dn,"$pass");
		elseif($type == 2)
			$bind = @ldap_bind($AdConnect,$userdomain,"$pass");

		if($bind) 
		{
			return 0; 
		} 
		else 
		{ 
			/* Login failed. Return false, together with the error code and text from
			** the LDAP server. The common error codes and reasons are listed below :
			** (for iPlanet, other servers may differ)
			** 19 - Account locked out (too many invalid login attempts)
			** 32 - User does not exist
			** 49 - Wrong username or password
			** 53 - Account inactive (manually locked out by administrator)
			*/
			$ldapErrorCode = ldap_errno($AdConnect);
			$ldapErrorText = ldap_error($AdConnect);

			return "[LDAP] Error: " . $ldapErrorCode . " - " . $ldapErrorText ;
		} 
	}

    public function getGoogle2fa() {
        $dbFeatures = new features_model();
        $success = array("success" => $dbFeatures->getValueBySessionName('SES_GOOGLE_2FA'));
        echo json_encode($success);
        return;
    }

    public function requestAuth($login,$password)
    {

        $idperson = $this->dbIndex->getIdPerson($login);

        if($idperson)
        {
            if ($this->dbIndex->getRequestsByPerson($idperson) == 0) {
                return true ;
            } else {
                if($this->dbIndex->checkPersonRequest($idperson,$password) == 1) {
                   return true ;
                } else {
                    return false ;
                }
            }
        }
        else
        {
            return false ;
        }
    }

    public function helpdezkAuth($login,$passwordMd5)
    {

        $idperson = $this->dbIndex->selectDataLogin($login, $passwordMd5);
        if ($idperson) {
            return true;
        } else {
            return false ;
        }
    }

    public function imapAuth($login,$password)
    {

        $popconfigs = $this->dbConfig->getPopConfigs() ;

        $host = $popconfigs['POP_HOST'];
        $port = $popconfigs['POP_PORT'];
        $type = $popconfigs['POP_TYPE'];
        $domain = $popconfigs['POP_DOMAIN'];
        if(!empty($domain)) $domain = '@'.$domain;

        if ($type == 'POP'){
            $hostname = '{' . $host . ':'.$port.'/pop3}INBOX' ;
        } elseif ($type == 'GMAIL') {
            $hostname = '{imap.gmail.com:'.$port.'/imap/ssl/novalidate-cert}INBOX';
        }
        //function_exists()
        if (!function_exists('imap_open'))
            die("IMAP functions are not available.<br />\n");

        /* try to connect */
        $mbox = imap_open($hostname,$login.$domain,$password) ;
        if($mbox) {
            imap_close($mbox);
            return true;
        } else {
            return false ;
        }

    }

    public function ldapAuth($login,$password)
    {
        $ldapconfigs = $this->dbConfig->getArrayConfigs(13);

        $type 	= $ldapconfigs['SES_LDAP_AD']; //1 LDAP / 2 AD
        $server = $ldapconfigs['SES_LDAP_SERVER'];
        $dn     = $ldapconfigs['SES_LDAP_DN'];
        $domain = $ldapconfigs['SES_LDAP_DOMAIN'];
        $object = $ldapconfigs['SES_LDAP_FIELD'];

        //$server ="ldap.testathon.net";
        //$user   = "carol";
        //$senha  = "carol";
        //$dn     = "OU=users,DC=testathon,DC=net";

        
        $userdomain = $login."@".$domain;
        
        if (!($AD = @ldap_connect($server))) {
            $msg = "Can't connecto to LDAP server !";
            if($this->log)
                $this->logIt("Can't connecto to LDAP server: ". $server .' - program: '.$this->program ,3,'general',__LINE__);
            return false;
        }

        ldap_set_option($AD, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($AD, LDAP_OPT_REFERRALS, 0);

        /**
         ** The only way to test the connection is to actually call ldap_bind( $ds, $username, $password ).
         ** But if that fails, is it because you have the wrong username/password or is it because the connection is down?
         ** As far as I can see there isn't any way to tell.
         **/
        $teste = $password;
        //die('aqui: ' . $teste);
        
        $ret =  $this->LdapValidate($AD, $dn, $password, $userdomain, $type) ;

        if ( $ret != '0') {
            $msg = $ret ;
            if($this->log)
                $this->logIt("Error validating user in LDAP/AD : ". $ret .' - program: '.$this->program ,5,'general',__LINE__);


            return false;
        } else {
            return true;
        }

    }

    public function ldapGetData($login,$password)
    {
        $ldapconfigs = $this->dbConfig->getArrayConfigs(13);

        $type 	= $ldapconfigs['SES_LDAP_AD']; //1 LDAP / 2 AD
        $server = $ldapconfigs['SES_LDAP_SERVER'];
        $dn     = $ldapconfigs['SES_LDAP_DN'];
        $domain = $ldapconfigs['SES_LDAP_DOMAIN'];
        $object = $ldapconfigs['SES_LDAP_FIELD'];
       
        $dn  = $object."=".$login.",$dn";

        $userdomain = $login."@".$domain;
        
        if (!($AD = @ldap_connect($server))) {
            $msg = "Can't connecto to LDAP server !";
            if($this->log)
                $this->logIt("Can't connecto to LDAP server: ". $server .' - program: '.$this->program ,3,'general',__LINE__);
            return false;
        }

        ldap_set_option($AD, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($AD, LDAP_OPT_REFERRALS, 0);

        /**
         ** The only way to test the connection is to actually call ldap_bind( $ds, $username, $password ).
         ** But if that fails, is it because you have the wrong username/password or is it because the connection is down?
         ** As far as I can see there isn't any way to tell.
         **/

        $type=2;

        $dn = $ldapconfigs['SES_LDAP_DN'];
        $dn = utf8_encode($dn);
		$pass = utf8_encode($password);
		
		if($type == 1)
			$bind = @ldap_bind($AD,$dn,"$pass");
		elseif($type == 2)
			$bind = @ldap_bind($AD,$userdomain,"$pass");

		if($bind) 
		{
            if ($type == 2) {
                $search = "sAMAccountName=" . $login;
            }
                        
            $sr = @ldap_search($AD,$dn,$search);
            
            $data = ldap_get_entries($AD, $sr);
            if(empty($data)) {
                if($this->log)
                    $this->logIt("Error get LDAP/AD data : ". $login .' - program: '.$this->program ,5,'general',__LINE__);
                return false;    
            }
            $aRet = array("email" =>  $data[0]['mail'][0],
                          "displayname" => $data[0]['displayname'][0],
                          "department" => $data[0]['department'][0],
                          "displayname" => $data[0]['displayname'][0]);

            return $aRet;
		}  else {
            $ldapErrorCode = ldap_errno($AD);
            $ldapErrorText = ldap_error($AD);
            if($this->log)
                $this->logIt("[LDAP] Error: " . $ldapErrorCode . " - " . $ldapErrorText . " - program: ".$this->program ,5,'general',__LINE__);
            return false;
        }
        
        


    }

    public function getGlobalSessionData()
    {

        // Global Config Data
        $rsConfig = $this->dbIndex->getConfigGlobalData();
        while (!$rsConfig->EOF) {
            $ses = $rsConfig->fields['session_name'];
            $val = $rsConfig->fields['value'];
            $_SESSION[$ses] = $val;
            $rsConfig->MoveNext();
        }

    }

}

?>