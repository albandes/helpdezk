<?php

use App\core\Controller;
use App\modules\admin\dao\mysql\LoginDAO;
use App\modules\admin\dao\mysql\FeatureDAO;
use App\modules\admin\dao\mysql\PersonDAO;

class Login extends Controller
{
    /*
	* chama a view index.php do  /home   ou somente   /
	*/
	public function index()
	{
		session_start();
        session_unset();
        session_destroy();
		
		$params = $this->getDefaultParams();
		$params['warning'] = "";
        
		$this->view(
			'admin',
			'login',
			$params
		);
		
	}

	public function auth() {

        $frm_login = $_POST['login'];
        $frm_password = $_POST['password'];
        $passwordMd5 = md5($_POST['password']);
        $form_token = $_POST['token'];

        $loginDAO = new LoginDAO();
        $personDAO = new PersonDAO();
        $featDAO = new FeatureDAO();

        $rsLogintype = $loginDAO->getLoginType($frm_login);
		$logintype = $rsLogintype['idtypelogin'];

		if(!$logintype){
            $license =  $_ENV["LICENSE"];

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

                $idNewPerson = $dbPerson->insertPerson('4','2','1','1',$login,$login,$dtcreate,'A','N','','','',$login);
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

                $login = $this->requestAuth($frm_login,$frm_password);
				$rsUser = $loginDAO->getUserByLogin($frm_login);
                $idperson = $rsUser['data']['idperson'];
                $idtypeperson = $rsUser['data']['idtypeperson'];
                break;

            case '3': // HelpDEZk   

                $login = $this->helpdezkAuth($frm_login,$passwordMd5);
                $rsUser = $loginDAO->getUser($frm_login, $passwordMd5);
                $idperson = $rsUser['data']['idperson'];
                $idtypeperson = $rsUser['data']['idtypeperson'];
                break;

            case '1': // Pop/Imap Server
                if (!function_exists('imap_open')) {
                    $login = false ;
                    $msg = "IMAP functions are not available!!!";
                    break;
                } 
                $login = $this->imapAuth($frm_login,$frm_password);
                $rsUser = $loginDAO->getUserByLogin($frm_login);
                $idperson = $rsUser['data']['idperson'];
                $idtypeperson = $rsUser['data']['idtypeperson'];
                break;

            case '2': // AD/LDAP				
				if (!function_exists('ldap_connect')) {
					$login = false ;
					$msg = "LDAP functions are not available!!!";
					break;
				}

                $login = $this->ldapAuth($frm_login,$frm_password);
                $rsUser = $loginDAO->getUserByLogin($frm_login);
                $idperson = $rsUser['data']['idperson'];
                $idtypeperson = $rsUser['data']['idtypeperson'];
                break;
        }

        if ($login) {

            /*
             *
             *   Login with google authenticator
             *   Second authentication
             *
             */

            $retGoogle2fa = $featDAO->getConfigValue('SES_GOOGLE_2FA'); 
            $google2fa = $retGoogle2fa['data']['value'];

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
				$rs = $loginDAO->checkUser($login);
				if($rs['data'] == "A") $msg = $langVars['Login_error_error'];
				elseif($rs['data'] == "I") $msg = $langVars['Login_user_inactive'];
			}
			$success = array("success" => 0,
							 "msg" => $msg );
			echo json_encode($success);
			return;
        }        
    }

	public function requestAuth($login,$password)
    {
		$loginDAO = new LoginDAO();
		$rsUser = $loginDAO->getUserByLogin($login);
        $idperson = $rsUser['data']['idperson'];

        if($idperson){
            $rsRequest = $loginDAO->getRequestsByUser($idperson);
			if ($rsRequest['data']['amount'] == 0) {
                return true ;
            } else {
                $checkRequest = $loginDAO->getUserRequests($idperson,$password);
                return ($checkRequest['data']['amount'] == 1) ? true : false;
            }
        }else{
            return false ;
        }
    }

    public function helpdezkAuth($login,$passwordMd5)
    {
        $loginDAO = new LoginDAO();
        $rsUser = $loginDAO->getUser($login,$passwordMd5);
        $idperson = $rsUser['data']['idperson'];
        return ($idperson) ? true : false;
    }

    public function imapAuth($login,$password)
    {
        $featDAO = new FeatureDAO();
        $popconfigs = $featDAO->getPopConfigs() ;

        $host = $popconfigs['data']['POP_HOST'];
        $port = $popconfigs['data']['POP_PORT'];
        $type = $popconfigs['data']['POP_TYPE'];
        $domain = $popconfigs['data']['POP_DOMAIN'];
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
        $featDAO = new FeatureDAO();
        $ldapconfigs = $featDAO->getArrayConfigs(13);

        $type 	= $ldapconfigs['data']['SES_LDAP_AD']; //1 LDAP / 2 AD
        $server = $ldapconfigs['data']['SES_LDAP_SERVER'];
        $dn     = $ldapconfigs['data']['SES_LDAP_DN'];
        $domain = $ldapconfigs['data']['SES_LDAP_DOMAIN'];
        $object = $ldapconfigs['data']['SES_LDAP_FIELD'];

        //$server ="ldap.testathon.net";
        //$user   = "carol";
        //$senha  = "carol";
        //$dn     = "OU=users,DC=testathon,DC=net";

        // =================

        $dn  = $object."=".$login.",$dn";

        $userdomain = $login."@".$domain;
        //$AD = @ldap_connect($server) ;

        if (!($AD = @ldap_connect($server))) {
            $msg = "Can't connecto to LDAP server !";
            return false;
        }


        ldap_set_option($AD, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($AD, LDAP_OPT_REFERRALS, 0);

        /**
         ** The only way to test the connection is to actually call ldap_bind( $ds, $username, $password ).
         ** But if that fails, is it because you have the wrong username/password or is it because the connection is down?
         ** As far as I can see there isn't any way to tell.
         **/

        $ret =  $this->LdapValidate($AD, $dn, $password, $userdomain, $type) ;

        /*
         * Search for user informations in ldap
         *
        $busca = ldap_search($AD, $dn , "(".$object."=".$_POST['login'].")");
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
            return false;
        } else {
            return true;
        }

    }
}