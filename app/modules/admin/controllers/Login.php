<?php

use App\core\Controller;

use App\modules\admin\dao\mysql\loginDAO;
use App\modules\admin\dao\mysql\featureDAO;
use App\modules\admin\dao\mysql\personDAO;
use App\modules\admin\src\loginServices;
use App\src\appServices;


class Login extends Controller
{
    public function __construct()
    {
        parent::__construct();
        
    }

    /**
     * en_us Renders the login screen template
     *
     * pt_br Renderiza o template da tela de login
     */
    public function index()
    {
        session_start();
        session_unset();
        session_destroy();
        
        $params = $this->makeScreenLogin();
        
        $this->view('admin','login',$params);
    }

    public function makeScreenLogin()
    {
        $loginSrc = new loginServices();
        $appSrc = new appServices();
        $aLogo = $loginSrc->_getLoginLogoData();
        $params = $appSrc->_getDefaultParams();
        $params['warning'] = "";
        $params['loginLogoUrl'] = $aLogo['image'];
        $params['loginheight'] = $aLogo['height'];
        $params['loginwidth'] = $aLogo['width'];

        return $params;
    }
        
    /**
     * auth
     *
     * @return void
     */
    public function auth()
    {
        $frm_login = $_POST['login'];
        $frm_password = $_POST['password'];
        $passwordMd5 = md5($_POST['password']);
        $form_token = $_POST['token'];
        
        $loginDAO = new loginDAO();
        $personDAO = new personDAO();
        $featDAO = new featureDAO();
        $loginSrc = new loginServices();
        $appSrc = new appServices();
        
        $loginType = $loginDAO->getLoginType($frm_login);
        
        if(is_null($loginType)){
            // Return with error message
            $success = array(
                "success" => 0,
                "msg" => html_entity_decode($langVars['Login_user_not_exist'],ENT_COMPAT, 'UTF-8')
            );
            echo json_encode($success);
            return;
		}
        
        switch ($loginType->getLogintype()) {
            case '3': // HelpDEZk
                
                $isLogin = $this->helpdezkAuth($frm_login,$passwordMd5); 
                $loginUser = $loginDAO->getUser($frm_login, $passwordMd5); 
                $idperson = (!is_null($loginUser) && !empty($loginUser)) ? $loginUser->getIdperson() : '';
                $idtypeperson = (!is_null($loginUser) && !empty($loginUser)) ? $loginUser->getIdtypeperson() : '';
                break;
            
            case '1': // Pop/Imap Server
                if (!function_exists('imap_open')) {
                    $login = false ;
                    $msg = "IMAP functions are not available!!!";
                    break;
                }
                
                $isLogin = $this->imapAuth($frm_login,$frm_password);
                $loginUser = $loginDAO->getUserByLogin($frm_login);
                $idperson = (!is_null($loginUser) && !empty($loginUser)) ? $loginUser->getIdperson() : '';
                $idtypeperson = (!is_null($loginUser) && !empty($loginUser)) ? $loginUser->getIdtypeperson() : ''; 
                break;
        }
        
        if ($isLogin) {
            
            switch  ($idtypeperson) {
                case "1": // admin
                    $loginSrc->_startSession($idperson);
                    $loginSrc->_getConfigSession();
                    $success = array(
                        "success" => 1,
                        "redirect" => $appSrc->_getPath() .  "/admin/home"
                    );
                    echo json_encode($success);
                    return;
                    break;

                case "2": // user
                    $loginSrc->_startSession($idperson);
                    $loginSrc->_getConfigSession();
                    if($_SESSION['SES_MAINTENANCE'] == 1){
                        $maintenance = array(
                            "success" => 0,
                            "msg" =>html_entity_decode($_SESSION['SES_MAINTENANCE_MSG'],ENT_COMPAT, 'UTF-8')
                        );		
                        echo json_encode($maintenance);
                        return;
                    }else{
                        $redirect = $appSrc->_getPath() .  "/" . $_SESSION['SES_ADM_MODULE_DEFAULT'] . "/home/index" ;
                        
                        $success = array(
                            "success" => 1,
                            "redirect" => $redirect
                        );
                        echo json_encode($success);

                        return;
                    }
                    break;

                case "3": // operator
                    $loginSrc->_startSession($idperson);  
                    $loginSrc->_getConfigSession();
                    if($_SESSION['SES_MAINTENANCE'] == 1){
						$maintenance = array(
										"success" => 0,
										"msg" =>html_entity_decode($_SESSION['SES_MAINTENANCE_MSG'],ENT_COMPAT, 'UTF-8')
									);
						echo json_encode($maintenance);
					}else{
						$success = array(
										"success" => 1,
										"redirect" => $appSrc->_getPath() . "/helpdezk/home/index"
									);

						echo json_encode($success);
						return;
					}
                    break;

                default: // others types
                
                    $loginSrc->_startSession($idperson);
                    $loginSrc->_getConfigSession();
                    
                    $existsTable = $featDAO->tableExists('tbtypeperson_has_module');
                    
                    if (is_null($existsTable) || empty($existsTable) || !$existsTable) {
                        $error = array( "success" => 0,
                                         "msg" =>html_entity_decode('There is no table tbtypeperson_has_module.',ENT_COMPAT, 'UTF-8')
                                      );
                        echo json_encode($error);
                        return;
                    }
                    
                    $pathModule = $featDAO->getPathModuleByTypePerson($idtypeperson);
                    if (!is_null($pathModule) && !empty($pathModule)) {
                        $modPath = $pathModule->getPath();

                        if($_SESSION['SES_MAINTENANCE'] == 1){
                            $maintenance = array(
                                "success" => 0,
                                "msg" =>html_entity_decode($_SESSION['SES_MAINTENANCE_MSG'],ENT_COMPAT, 'UTF-8')
                            );
                            echo json_encode($maintenance);
                        }else{
                            $success = array(
                                "success" => 1,
                                "redirect" => $appSrc->_getPath() . "/{$modPath}/home/index"
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
			if (in_array($loginType->getLogintype(),array(1,3,4))) { // Pop, HD  ou REQUEST login
				$userStatus = $loginDAO->checkUser($login); 
                if (is_null($userStatus) || empty($userStatus) || $userStatus->getUserStatus() == 'I'){
                    $msg = $langVars['Login_user_inactive'];
				}elseif($userStatus->getUserStatus() == "A") 
                    $msg = $langVars['Login_error_error'];
			}
			$success = array("success" => 0,
							 "msg" => $msg );
			echo json_encode($success);
			return;
        }
    }
    
    public function requestAuth($login,$password)
    {
		$loginDAO = new loginDAO();
		$rsUser = $loginDAO->getUserByLogin($login);
        $idperson = $rsUser['data']->getIdperson();

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
        $loginUser = $loginDAO->getUser($login,$passwordMd5);
        
        return (!is_null($loginUser) && !empty($loginUser)) ? true : false;
    }

    public function imapAuth($login,$password)
    {
        $featDAO = new featureDAO(); 
        $popConfigs = $featDAO->fetchPopConfigs() ;
        
        if (!is_null($popConfigs) && !empty($popConfigs)){
            $host = $popConfigs->getHost();
            $port = $popConfigs->getPort();
            $type = $popConfigs->getType();
            $domain = $popConfigs->getDomain();
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
        }else{
            return false;
        }

    }

}