<?php

use App\core\Controller;

use App\modules\admin\dao\mysql\loginDAO;
use App\modules\admin\dao\mysql\featureDAO;
use App\modules\admin\dao\mysql\personDAO;

use App\modules\admin\models\mysql\loginModel;
use App\modules\admin\models\mysql\popConfigModel;

use App\modules\admin\src\loginServices;
use App\src\appServices;
use App\src\localeServices;


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

    /**
	 *  en_us Configure program screens
	 * 
	 *  pt_br Configura as telas do programa
	 */
    public function makeScreenLogin()
    {
        $loginSrc = new loginServices();
        $aLogo = $loginSrc->_getLoginLogoData();
        $params = $this->appSrc->_getDefaultParams();
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
        $loginDAO = new loginDAO();
        $personDAO = new personDAO();
        $featDAO = new featureDAO();
        $loginSrc = new loginServices();

        $loginModel = new loginModel();
        $loginModel->setLogin($_POST['login'])
                   ->setFrmPassword($_POST['password'])
                   ->setPasswordEncrypted(md5($_POST['password']));
        if(isset($_POST['token'])) 
            $loginModel->setFrmToken($_POST['token']);

        $loginType = $loginDAO->getLoginType($loginModel);
        
        if(!$loginType['status']){
            // Return with error message
            $success = array(
                "success" => 0,
                "msg" => html_entity_decode($this->translator->translate('Login_user_not_exist'),ENT_COMPAT, 'UTF-8')
            );
            echo json_encode($success);
            return;
		}
        
        $loginTypeObj = $loginType['push']['object'];
        switch ($loginTypeObj->getLogintype()) {
            case '3': // HelpDEZk
                
                $isLogin = $this->helpdezkAuth($loginTypeObj); 
                $loginUser = $loginDAO->getUser($loginTypeObj);

                $idperson = ($loginUser['status']) ? $loginUser['push']['object']->getIdperson() : '';
                $idtypeperson = ($loginUser['status']) ? $loginUser['push']['object']->getIdtypeperson() : '';
                break;
            
            case '1': // Pop/Imap Server
                if (!function_exists('imap_open')) {
                    $login = false ;
                    $msg = "IMAP functions are not available!!!";
                    break;
                }
                
                $isLogin = $this->imapAuth($loginTypeObj);
                $loginUser = $loginDAO->getUserByLogin($loginTypeObj);

                $idperson = ($loginUser['status']) ? $loginUser['push']['object']->getIdperson() : '';
                $idtypeperson = ($loginUser['status']) ? $loginUser['push']['object']->getIdtypeperson() : '';
                break;
        }
        
        if ($isLogin) {
            
            switch  ($idtypeperson) {
                case "1": // admin
                    $loginSrc->_startSession($idperson);
                    $loginSrc->_getConfigSession();
                    $success = array(
                        "success" => 1,
                        "redirect" => $this->appSrc->_getPath() .  "/admin/home"
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
                        $redirect = $this->appSrc->_getPath() .  "/" . $_SESSION['SES_ADM_MODULE_DEFAULT'] . "/home/index" ;
                        
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
										"redirect" => $this->appSrc->_getPath() . "/helpdezk/home/index"
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
                                "redirect" => $this->appSrc->_getPath() . "/{$modPath}/home/index"
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
				$userStatus = $loginDAO->checkUser($frm_login); 
                if (is_null($userStatus) || empty($userStatus) || $userStatus->getUserStatus() == 'I'){
                    $msg = $this->translator->translate('Login_user_inactive');
				}elseif($userStatus->getUserStatus() == "A") 
                    $msg = $this->translator->translate('Login_error_error');
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
    
    /**
     * Check if user exists in DB
     *
     * @param  loginModel $loginModel
     * @return bool
     */
    public function helpdezkAuth(loginModel $loginModel): bool
    {
        $loginDAO = new LoginDAO();
        $loginUser = $loginDAO->getUser($loginModel);
        
        if(!$loginUser['status']){
            return false;
        }else{
            return $loginUser['push']['object']->getIdperson() == 0 ? false : true;
        }
    }

    /**
     * Auth user with google account
     *
     * @param  loginModel $loginModel
     * @return bool
     */
    public function imapAuth(loginModel $loginModel): bool
    {
        $featDAO = new featureDAO();
        $popConfMod = new popConfigModel();
        $popConfigs = $featDAO->fetchPopConfigs($popConfMod); 
        
        if ($popConfigs['status']){
            $host = $popConfigs['push']['object']->getHost();
            $port = $popConfigs['push']['object']->getPort();
            $type = $popConfigs['push']['object']->getType();
            $domain = $popConfigs['push']['object']->getDomain();
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
            $mbox = imap_open($hostname,$loginModel->getLogin().$domain,$loginModel->getFrmPassword()) ;
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