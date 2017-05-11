<?php

class Login extends Controllers {

    public function index() {
        session_start();
        session_unset();
        session_destroy();
        
        $smarty = $this->retornaSmarty();
        $database = $this->getConfig('db_connect');
        if ($database == 'mysqlt') {
            if (!function_exists('mysql_connect')) die("MYSQL functions are not available !!!");
        } elseif ($database == 'oci8po') {
            if (!function_exists('oci_connect')) die("ORACLE functions are not available !!!");
        }

        $db = new logos_model();

        $loginlogo = $db->getLoginLogo();
        $smarty->assign('loginlogo', $loginlogo->fields['file_name']);
        $smarty->assign('height', $loginlogo->fields['height']);
        $smarty->assign('width', $loginlogo->fields['width']);
        $smarty->assign('erro', 'login_errado');
        //$smarty->config_load(PATH . '/lang/' . $dir . '/lang.conf', $idioma);
        $smarty->assign('bgcolor1', '#00CCFF');
        $smarty->assign('bgcolor2', '#FF9900');
        $smarty->assign('versao', 'Parracho');
        $smarty->assign('Company_name', 'Sistema Helpdezk - Pipegrep ');
        $smarty->assign('release', '1.0');
        $smarty->assign('proj', 'login');
        $smarty->assign('ip', getenv("REMOTE_ADDR"));
		
		$smarty->assign('lang_default', $this->getConfig('lang'));
		$smarty->assign('path', path);
		$smarty->assign('hdk_url', $this->getConfig('hdk_url'));
		
        //le o arquivo de versao para printar na tela de login
        $csvFile = DOCUMENT_ROOT . path . "/version.txt";
        if ($arquivo = fopen($csvFile, "r")) {
            while (!feof($arquivo)) {
                $i++;
                $version = fgets($arquivo, 4096);
            }
            $smarty->assign('version', $version);
        } else {
            $smarty->assign('version', '');
        }
		if($enterprise) {
			$smarty->assign('site', 'HelpDEZK.com.br');
			$smarty->assign('urlsite', 'http://www.helpdezk.com.br');
		} else {
			$smarty->assign('site', 'HelpDEZK.org');
			$smarty->assign('urlsite', 'http://helpdezk.org');		
		}
		
		$bdw = new warning_model();
		$database = $this->getConfig('db_connect');
		if ($database == 'mysqlt') {
			$and = "AND (a.dtend > NOW() AND a.dtstart <= NOW() OR a.dtend = '0000-00-00 00:00:00') AND a.showin IN (2,3)" ;
		} elseif ($database == 'oci8po') {
			$and = "AND (a.dtend > SYSDATE AND a.dtstart <= SYSDATE OR a.dtend IS NULL) AND a.showin IN (2,3)" ;
		}
        $rsWarning = $bdw->selectWarning($and,"ORDER BY dtstart ASC");

		$i = 0;
		//print_r($rsWarning->fields) ;
		
		while (!$rsWarning->EOF) {
			$warning[$i]['idmessage'] = $rsWarning->fields['idmessage'];
			$warning[$i]['title_topic'] = $rsWarning->fields['title_topic'];
			$warning[$i]['title_warning'] = $rsWarning->fields['title_warning'];
			$i++;
            $rsWarning->MoveNext();	 	
		}
		
		$smarty->assign('warning', $warning);
        $smarty->assign('license', $this->getConfig("license"));
		
        //$smarty->display('login.tpl.html');
		$smarty->display('loginv2.tpl.html');
        //$this->view('login.tpl.html');
    }

	public function getWarningInfo(){
		$smarty = $this->retornaSmarty();
		$id = $this->getParam('id');
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
        $langVars = $smarty->get_config_vars();		
		include 'includes/classes/ProtectSql/ProtectSql.php';
		$ProtectSql = new sqlinj;
		$ProtectSql->start("aio","all");

        $F_LOGIN = $_POST['F_LOGIN'];		
        $F_SENHA_MD5 = md5($_POST['F_SENHA']);
        
        $bd = new index_model();
        $dbPerson = new person_model();

        $logintype = $bd->getTypeLogin($F_LOGIN);
		$logintype = $logintype->fields['idtypelogin'];

		if(!$logintype){
            $license =  $this->getConfig("license");

            if($license != '201601001') {
                // Return with error message
                $success = array(
                    "success" => 0,
                    "msg" => html_entity_decode($langVars['Login_user_not_exist'],ENT_COMPAT, 'UTF-8')
                );
                echo json_encode($success);
                return;

           } else {
                /*
                 *  Client 201601001
                 *  Create a new user, if don't exists
                 *  Set type login to 4 [autenticate by request number]
                 */

                $dbPerson->BeginTrans();

                $dtcreate = date('Y-m-d H:i:s');
                $logintype = 4 ; // Need in the first access
                $iddepartment =  '72' ;

                $idNewPerson = $dbPerson->insertPerson('4','2','1','1',$F_LOGIN,$F_LOGIN,$dtcreate,'A','N','','','',$F_LOGIN);
                if (!$idNewPerson) {
                    $error = true ;
                }
                if (!$error) {
                    $insNatural = $dbPerson->insertNaturalData($idNewPerson, '', '', '');
                    if (!$insNatural) {
                        $error = true;
                    }
                }
                if (!$error) {
                    $depart = $dbPerson->insertInDepartment($idNewPerson, $iddepartment);
                    if (!$depart) {
                        $error = true ;
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

                    $dbPerson->CommitTrans();
                }

            }
		}

        switch ($logintype) {
            case '4': // Request
                $idperson = $bd->getIdPerson($F_LOGIN);

                if($idperson)
                {
                    if ($bd->getRequestsByPerson($idperson) == 0) {
                        $login = true ;
                    } else {
                        if($bd->checkPersonRequest($idperson,$_POST['F_SENHA']) == 1) {
                            $login =true ;
                        } else {
                            $login = false ;
                        }
                    }
                }
                else
                {
                    $login = false ;
                }

                break;

            case '3': // HelpDEZk   

                $idperson = $bd->selectDataLogin($F_LOGIN, $F_SENHA_MD5);
                if ($idperson) {
                    $login = true;
                } else {
                    $login = false ;
                }
				
                break;

            case '1': // Pop/Imap Server
                $bd_cfg = new features_model();
                $popconfigs = $bd_cfg->getPopConfigs();

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

                /* try to connect */
                $mbox = imap_open($hostname,$_POST['F_LOGIN'].$domain,$_POST['F_SENHA']) ;
                if($mbox) {
                    $idperson = $bd->getIdPerson($F_LOGIN);
                    imap_close($mbox);
                    $login = true;
                } else {
                    $login = false ;
                }                
                break;

            case '2': // AD/LDAP				
				if (!function_exists('ldap_connect')) {
					$login = false ;
					$msg = "LDAP functions are not available!!!";
					break;
				}				
                $bd_cfg = new features_model();
                $ldapconfigs = $bd_cfg->getArrayConfigs(13);
				
				$type 	= $ldapconfigs['SES_LDAP_AD']; //1 LDAP / 2 AD
                $server = $ldapconfigs['SES_LDAP_SERVER'];
                $dn     = $ldapconfigs['SES_LDAP_DN'];
				$domain = $ldapconfigs['SES_LDAP_DOMAIN'];
				$object = $ldapconfigs['SES_LDAP_FIELD'];
					
				//$server ="ldap.testathon.net";
				//$user   = "carol";
				//$senha  = "carol";
				//$dn     = "OU=users,DC=testathon,DC=net";
				
				// =================
				
				$dn  = $object."=".$_POST['F_LOGIN'].",$dn";
				
				$userdomain = $_POST['F_LOGIN']."@".$domain;
				//$AD = @ldap_connect($server) ;
				
				
				if (!($AD = @ldap_connect($server))) {
					$msg = "Can't connecto to LDAP server !";
					$login = false;
				}
				
				
				ldap_set_option($AD, LDAP_OPT_PROTOCOL_VERSION, 3);
				ldap_set_option($AD, LDAP_OPT_REFERRALS, 0);
				
				/**
				 ** The only way to test the connection is to actually call ldap_bind( $ds, $username, $password ). 
				 ** But if that fails, is it because you have the wrong username/password or is it because the connection is down? 
				 ** As far as I can see there isn't any way to tell. 
				 **/
				
				$ret =  $this->LdapValidate($AD, $dn, $_POST['F_SENHA'], $userdomain, $type) ;
				
				/*
				 * Search for user informations in ldap
				 * 
				$busca = ldap_search($AD, $dn , "(".$object."=".$_POST['F_LOGIN'].")");
				$result = ldap_get_entries($AD, $busca);				
				for ($item = 0; $item < $result['count']; $item++){
					for ($attribute = 0; $attribute < $result[$item]['count']; $attribute++){
				  		$data = $result[$item][$attribute];
						echo $data. ": ".$result[$item][$data][0]."<br>";
					}
				}
				*/
				
				if ( $ret != '0') {
					$msg = $ret ;
					$login = false;
				} else {
					$idperson = $bd->getIdPerson($F_LOGIN);
					$login = true;
				}				
                break;
        }

        if ($login) {
            /*
            $type = $bd->selectTypePerson($idperson);
            if(!$type){
                die("#".__LINE__);
                return false;
            }
			$idtypeperson = ($type->fields['idtypeperson']) ;
            */
            $idtypeperson =  $dbPerson->getIdTypePerson($idperson);

            /*
             *
             *   Login with google authenticator
             *   Second authentication
             *
             */

            $google2fa = $bd->getConfigValue('SES_GOOGLE_2FA');
            // Mario Quintana
            if (empty($google2fa)) { // if don't exists in hdk_tbconfig [old versions before 1.02]
                $google2fa = 0 ;
            }
            /*
             *
             */
            if ($google2fa) {
                if ($idperson != 1)
                {
                    if($this->getConfig("license") == '200701006') {
                        $iddepartment = $bd->getIdPersonDepartment($F_LOGIN) ; //die('id: ' . $iddepartment) ;
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

                //var_dump($makeSecret);
                //die('aqui: ' . $iddepartment);

                if ($makeSecret)
                {
                    if ((include 'includes/classes/GoogleAuthenticator/GoogleAuthenticator.php') == false) {
                        die("Don't include the class GoogleAuthenticator.php, line ".__LINE__ . "!!!");
                    }

                    $ga = new PHPGangsta_GoogleAuthenticator();

                    $oneCode = $_POST['F_SECRET'] ;
                    $token = $dbPerson->getPersonSecret($idperson) ;

                     //die(__LINE__ . ": ". $token) ;
                    if ($token)
                    {


                        $checkResult = $ga->verifyCode($token, $oneCode, 2);
                        //die(__LINE__ . ": " . $checkResult);

                        if(!$checkResult){
                            //die(__LINE__ . ": " . $token);
                            $success = array(
                                "success" => 0,
                                "msg"     => html_entity_decode($langVars['Login_error_secret'],ENT_COMPAT, 'UTF-8')
                            );
                            echo json_encode($success);
                            return;
                        }
                        //die(__LINE__ . ": " . $token);
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
                    $this->startSession($idperson);
                    $this->getConfigSession();
                    $success = array(
                        "success" => 1,
                        "redirect" => path . "/admin/home"
                    );
                    echo json_encode($success);
                    return;
                    break;

                case "2":
                    $this->startSession($idperson);
                    $this->getConfigSession();
                    if($_SESSION['SES_MAINTENANCE'] == 1){						
						$maintenance = array(
										"success" => 0,
										"msg" =>html_entity_decode($_SESSION['SES_MAINTENANCE_MSG'],ENT_COMPAT, 'UTF-8')
									);					
						echo json_encode($maintenance);
						return;
                    }else{
                        $success = array(
										"success" => 1,
										"redirect" => path . "/helpdezk/user"
									);
						echo json_encode($success);
						return;
                    }
                    break;

                case "3":
                    $this->startSession($idperson);
                    $this->getConfigSession();
                    if($_SESSION['SES_MAINTENANCE'] == 1){
						$maintenance = array(
										"success" => 0,
										"msg" =>html_entity_decode($_SESSION['SES_MAINTENANCE_MSG'],ENT_COMPAT, 'UTF-8')
									);
						echo json_encode($maintenance);
					}else{
						$success = array(
										"success" => 1,
										"redirect" => path . "/helpdezk/operator"
									);
						echo json_encode($success);
						return;
					}
                    break;

                //  Another modules
                case "9":      // ERP_User
                    $this->startSession($idperson);

                    $this->getConfigSession();
                    if($_SESSION['SES_MAINTENANCE'] == 1){
                        $maintenance = array(
                            "success" => 0,
                            "msg" =>html_entity_decode($_SESSION['SES_MAINTENANCE_MSG'],ENT_COMPAT, 'UTF-8')
                        );
                        echo json_encode($maintenance);
                    }else{
                        // Precisa pegar do banco para saber para onde redirecionar
                        $success = array(
                            "success" => 1,
                            "redirect" => path . "/erp/home"
                        );
                        echo json_encode($success);
                        return;
                    }
                    break;

                default:					
                    //$this->startSession($idperson);
                    //$this->getConfigSession();
					$success = array(
										"success" => false,
										"redirect" => path . "/admin/login"
									);
					echo json_encode($success);
					return;
                    break;
            }
        } else {
			if ($logintype == 1 or $logintype == 3 or $logintype == 4) { // Pop, HD  ou REQUEST login
				$rs = $bd->checkUser($F_LOGIN);
				if($rs == "A") $msg = $langVars['Login_error_error'];
				elseif($rs == "I") $msg = $langVars['Login_user_inactive'];
			}
			$success = array(
								"success" => 0,
								"msg" => $msg
							);
			echo json_encode($success);
			return;
        }        
    }

    public function startSession($idperson)
    {
    session_start();
        $_SESSION['SES_COD_USUARIO'] = $idperson;
        $_SESSION['REFRESH'] = false;

        //SAVE THE CUSTOMER'S LICENSE
        $_SESSION['SES_LICENSE'] = $this->getConfig('license');
        $_SESSION['SES_ENTERPRISE'] = $this->getConfig('enterprise');
        
        $bd = new index_model();
        if ($_SESSION['SES_COD_USUARIO'] != 1) {
            $typeuser = $bd->selectDataSession($idperson);

            //print_r($typeuser) ; die() ;

			$_SESSION['SES_NAME_PERSON'] = $typeuser->fields['name'];
			$_SESSION['SES_TYPE_PERSON'] = $typeuser->fields['idtypeperson'];
			$_SESSION['SES_IND_CODIGO_ANOMES'] = true;
			$_SESSION['SES_COD_EMPRESA'] = $typeuser->fields['idjuridical'];
			$_SESSION['SES_COD_TIPO'] = $typeuser->fields['idtypeperson'];
            $groups = $bd->selectPersonGroups($idperson);
            $i = "0";
            while (!$groups->EOF) {
				$arr[$i] = $groups->fields['idgroup'];
                $i++;
                $groups->MoveNext();
            }
            $groups = implode(',', $arr);
            $_SESSION['SES_PERSON_GROUPS'] = $groups;
        } else {
            $_SESSION['SES_NAME_PERSON'] = 'Root';
            $_SESSION['SES_TYPE_PERSON'] = 1;
            $_SESSION['SES_IND_CODIGO_ANOMES'] = true;
            $_SESSION['SES_COD_EMPRESA'] = 1;
            $_SESSION['SES_COD_TIPO'] = 1;

            $groups = $bd->selectAllGroups();
            $i = "0";
            while (!$groups->EOF) {
				$arr[$i] = $groups->fields['idgroup'];
                $i++;
                $groups->MoveNext();
            }
            $groups = implode(',', $arr);
            $_SESSION['SES_PERSON_GROUPS'] = $groups;
        }
		
    }

    public function getConfigSession()
    {

        session_start();
        $bd = new index_model();
        $data = $bd->getConfigData();

		$idperson = $_SESSION['SES_COD_USUARIO'];
        while (!$data->EOF) {
			$ses = $data->fields['session_name'];
			$val = $data->fields['value'];
            $_SESSION[$ses] = $val;
            $data->MoveNext();
        }

		$cf = new userconfig_model();
		$columns = $cf->getColumns(); //GET COLUMNS OF THE TABLE

        $database = $this->getConfig('db_connect');

		while (!$columns->EOF) {
            if($database == 'mysqlt') {
                $cols[] = strtolower($columns->fields['Field']);
            } elseif($database == 'oci8po') {
                $cols[] = strtolower($columns->fields['column_name']);
            }
            $columns->MoveNext();
        }


		$idconf = $cf->checkConf($idperson); //CHECK IF USER HAVE PERSONAL CONFIG, IF DNO'T HAVE IT'S CREATE

		$getUserConfig = $cf->getConf($cols,$idconf);
		foreach ($cols as $key => $value) {
			$_SESSION['SES_PERSONAL_USER_CONFIG'][$value] = $getUserConfig->fields[$value];
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
        $login = addslashes($_POST['txtUser']);
        
        $bd = new index_model();
        $logintype = $bd->getTypeLogin($login);
        
		$idperson = $bd->getIdPerson($login);
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
			include 'includes/classes/pipegrep/pipegrep.php';
			$pipe = new pipegrep();
			$pass = $pipe->generateRandomPassword(8, false, true, false);
			
			$idperson = $bd->getIdPerson($login);
			$password = md5($pass);
			
			$data = new person_model();
			$change = $data->changePassword($idperson, $password);
			
			if (!$change) {
				echo ('ERROR');
				exit;
			} 
			
			$smarty = $this->retornaSmarty();
			$subject = $smarty->get_config_vars('Lost_password_subject');
			$body = $smarty->get_config_vars('Lost_password_body');
			$log_text = $smarty->get_config_vars('Lost_password_log');
			
			eval("\$body = \"$body\";");
			
			$address = array($bd->getEmailPerson($login));
			
			$ret = $this->sendEmailDefault($subject,$body,$address, true, $log_text  . $login) ;
			echo $logintype->fields['idtypelogin'];
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


}
?>