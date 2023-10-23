<?php

use App\core\Controller;

use App\modules\admin\dao\mysql\loginDAO;
use App\modules\admin\dao\mysql\featureDAO;
use App\modules\admin\dao\mysql\personDAO;
use App\modules\helpdezk\dao\mysql\warningDAO;

use App\modules\admin\models\mysql\loginModel;
use App\modules\admin\models\mysql\popConfigModel;
use App\modules\admin\models\mysql\featureModel;
use App\modules\helpdezk\models\mysql\warningModel;

use App\modules\admin\src\loginServices;
use App\src\googleServices;


class Login extends Controller
{
    /**
     * @var bool
     */
    protected $googleAuth;

    /**
     * @var object
     */
    protected $googleSrc;

    /**
     * @var string
     */
    protected $googleAuthLink;

    public function __construct()
    {
        parent::__construct();
        
        $this->googleAuth = $this->isGoogleAuthentication();//set if authentication is by google
        if($this->googleAuth){
            $this->googleSrc = new googleServices();
            $this->googleSrc->init();
            $this->googleAuthLink = $this->googleSrc->generateAuthLink();
        }
        
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
        
        $ip = $this->appSrc->_getUserIpAddress();
        $this->logger->info("User IP: {$ip}", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
        
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
        
        $params['loginLogoUrl'] = $aLogo['image'];
        $params['loginheight'] = $aLogo['height'];
        $params['loginwidth'] = $aLogo['width'];
        $params['googleAuth'] = $this->googleAuth;
        $params['googleAuthUrl'] = ($this->googleAuth) ? $this->googleAuthLink : "";

        $params['warning'] = $this->getWarningList();

        // -- modals --
        $params['modalViewWarning'] = $this->appSrc->_getHelpdezkPath().'/app/modules/helpdezk/views/modals/warning/modal-warning-view.latte';
        $params['modalAlert'] = $this->appSrc->_getHelpdezkPath().'/app/modules/main/views/modals/main/modal-alert.latte';

        //vocabulary
        $params['vocab'] = $this->appSrc->_loadVocabulary();

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
        $loginModel = new loginModel();
        $personDAO = new personDAO();
        $featDAO = new featureDAO();
        $loginSrc = new loginServices();
        

        if($this->googleAuth && $_POST['login'] != 'admin'){//if authentication is by google          
            $error = filter_input(INPUT_GET,"error",FILTER_SANITIZE_STRING);// get error returned from google
            $code = filter_input(INPUT_GET,"code",FILTER_SANITIZE_STRING);// get code returned from google
            
            if($error){
                // display error message
                $this->loginErrorMessage($this->translator->translate('require_authorization_login'));
                return;
            }

            if($code){
                $retAuth = $this->googleSrc->authorized($code);
                $userData = $this->googleSrc->getData();
                $loginModel->setUserEmail($userData->email);

                //check if user exists in db
                $checkUser = $loginDAO->getUserByEmail($loginModel);
                if(!$checkUser['status'] || $checkUser['push']['object']->getIdPerson() == 0){
                    // display error message
                    $this->loginErrorMessage($this->translator->translate('user_not_exist_msg'));
                }else{
                    $loginTypeObj = $checkUser['push']['object'];
                    $loginTypeObj->setLoginType(1);
                    $isLogin = true; 
                    $idperson = ($checkUser['status']) ? $checkUser['push']['object']->getIdPerson() : '';
                    $idtypeperson = ($checkUser['status']) ? $checkUser['push']['object']->getIdTypePerson() : '';
                }
            }
        }else{
            $loginModel->setLogin($_POST['login'])
                        ->setFrmPassword($_POST['password'])
                        ->setPasswordEncrypted(md5($_POST['password']));
            if(isset($_POST['token'])) 
                $loginModel->setFrmToken($_POST['token']);

            $loginType = $loginDAO->getLoginType($loginModel);
            
            if(!$loginType['status'] || $loginType['push']['object']->getLoginType() == 0){
                // Return with error message
                $success = array(
                    "success" => 0,
                    "msg" => html_entity_decode($this->translator->translate('Login_user_not_exist'),ENT_COMPAT, 'UTF-8')
                );
                echo json_encode($success);
                return;
            }
            
            $loginTypeObj = $loginType['push']['object'];
            switch ($loginTypeObj->getLoginType()) {
                case '3': // HelpDEZk
                    
                    $isLogin = $this->helpdezkAuth($loginTypeObj); 
                    $loginUser = $loginDAO->getUser($loginTypeObj);

                    $idperson = ($loginUser['status']) ? $loginUser['push']['object']->getIdPerson() : '';
                    $idtypeperson = ($loginUser['status']) ? $loginUser['push']['object']->getIdTypePerson() : '';
                    break;
                
                case '1': // Pop/Imap Server
                    if (!function_exists('imap_open')) {
                        $isLogin = false ;
                        $msg = "IMAP functions are not available!!!";
                        $success = array(
                            "success" => 0,
                            "msg" => html_entity_decode($msg,ENT_COMPAT, 'UTF-8')
                        );
                        echo json_encode($success);
                        return;
                        break;
                    }
                    
                    $isLogin = $this->imapAuth($loginTypeObj);
                    $loginUser = $loginDAO->getUserByLogin($loginTypeObj);

                    $idperson = ($loginUser['status']) ? $loginUser['push']['object']->getIdPerson() : '';
                    $idtypeperson = ($loginUser['status']) ? $loginUser['push']['object']->getIdTypePerson() : '';
                    break;
            }
        }        
        
        if ($isLogin) {
            // inserts login details in DB
            $loginTypeObj->setLoginStatus(1);
            $insDetail = $loginDAO->insertLoginDetail($loginTypeObj);
            if(!$insDetail['status']){
                $this->logger->error("Can't insert login detail.", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error' => $insDetail['push']['message']]);
            }else{
                $this->logger->info("Login detail saved successfully.", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error' => ""]);
            }
            
            switch  ($idtypeperson) {
                case "1": // admin
                    $loginSrc->_startSession($idperson);
                    $loginSrc->_getConfigSession();

                    if($this->googleAuth && $_POST['login'] != 'admin'){
                        header("Location: {$this->appSrc->_getPath()}/admin/home");
                        die();
                    }else{
                        $success = array(
                            "success" => 1,
                            "redirect" => $this->appSrc->_getPath() .  "/admin/home"
                        );
                        echo json_encode($success);
                        return;
                    }
                    
                    break;

                case "2": // user
                    $loginSrc->_startSession($idperson);
                    $loginSrc->_getConfigSession();
                    if($_SESSION['SES_MAINTENANCE'] == 1){
                        if($this->googleAuth && $_POST['login'] != 'admin'){
                            // display error message
                            $this->loginErrorMessage($this->translator->translate($_SESSION['SES_MAINTENANCE_MSG']));
                            die();
                        }else{
                            $maintenance = array(
                                "success" => 0,
                                "msg" =>html_entity_decode($_SESSION['SES_MAINTENANCE_MSG'],ENT_COMPAT, 'UTF-8')
                            );		
                            echo json_encode($maintenance);
                            return;
                        }
                    }else{
                        if($this->googleAuth && $_POST['login'] != 'admin'){
                            header("Location: {$this->appSrc->_getPath()}/{$_SESSION['SES_ADM_MODULE_DEFAULT']}/home/index");
                            die();
                        }else{
                            $redirect = $this->appSrc->_getPath() .  "/" . $_SESSION['SES_ADM_MODULE_DEFAULT'] . "/home/index" ;
                        
                            $success = array(
                                "success" => 1,
                                "redirect" => $redirect
                            );
                            echo json_encode($success);

                            return;
                        }
                    }
                    break;

                case "3": // operator
                    $loginSrc->_startSession($idperson);  
                    $loginSrc->_getConfigSession();
                    if($_SESSION['SES_MAINTENANCE'] == 1){
                        if($this->googleAuth && $_POST['login'] != 'admin'){
                            // display error message
                            $this->loginErrorMessage($this->translator->translate($_SESSION['SES_MAINTENANCE_MSG']));
                            die();
                        }else{
                            $maintenance = array(
                                "success" => 0,
                                "msg" =>html_entity_decode($_SESSION['SES_MAINTENANCE_MSG'],ENT_COMPAT, 'UTF-8')
                            );		
                            echo json_encode($maintenance);
                            return;
                        }
                    }else{
                        if($this->googleAuth && $_POST['login'] != 'admin'){
                            header("Location: {$this->appSrc->_getPath()}/{$_SESSION['SES_ADM_MODULE_DEFAULT']}/home/index");
                            die();
                        }else{
                            $redirect = $this->appSrc->_getPath() .  "/" . $_SESSION['SES_ADM_MODULE_DEFAULT'] . "/home/index" ;
                        
                            $success = array(
                                "success" => 1,
                                "redirect" => $redirect
                            );
                            echo json_encode($success);

                            return;
                        }
                    }
                    break;

                default: // others types
                
                    $loginSrc->_startSession($idperson);
                    $loginSrc->_getConfigSession();
                    $featModel = new featureModel();
                    $featModel->setTableName('tbtypeperson_has_module');
                    
                    $retExistsTable = $featDAO->tableExists($featModel);
                    
                    if (!$retExistsTable['status'] || !$retExistsTable['push']['object']->getExistTable()) {
                        $error = array( "success" => 0,
                                         "msg" =>html_entity_decode('There is no table tbtypeperson_has_module.',ENT_COMPAT, 'UTF-8')
                                      );
                        echo json_encode($error);
                        return;
                    }
                    
                    $featModel->setUserType($idtypeperson);
                    $retPathModule = $featDAO->getPathModuleByTypePerson( $featModel);
                    if ($retPathModule['status']) {
                        $pathModule = $retPathModule['push']['object'];
                        $modPath = $pathModule->getPath();
                        if($_SESSION['SES_MAINTENANCE'] == 1){
                            if($this->googleAuth && $_POST['login'] != 'admin'){
                                // display error message
                                $this->loginErrorMessage($this->translator->translate($_SESSION['SES_MAINTENANCE_MSG']));
                                die();
                            }else{
                                $maintenance = array(
                                    "success" => 0,
                                    "msg" =>html_entity_decode($_SESSION['SES_MAINTENANCE_MSG'],ENT_COMPAT, 'UTF-8')
                                );
                                echo json_encode($maintenance);
                            }
                        }else{
                            if($this->googleAuth && $_POST['login'] != 'admin'){
                                header("Location: {$this->appSrc->_getPath()}/{$_SESSION['SES_ADM_MODULE_DEFAULT']}/home/index");
                                die();
                            }else{
                                $success = array(
                                    "success" => 1,
                                    "redirect" => $this->appSrc->_getPath() . "/{$modPath}/home/index"
                                );
                                echo json_encode($success);
                                return;
                            }
                        }
                    } else {
                        if($this->googleAuth && $_POST['login'] != 'admin'){
                            // display error message
                            $this->loginErrorMessage($this->translator->translate('User type has no linked module'));
                            die();
                        }else{
                            $error = array(
                                "success" => 0,
                                "msg" =>html_entity_decode('User type has no linked module',ENT_COMPAT, 'UTF-8')
                            );
                            echo json_encode($error);
                            return;
                        }
                    }
					return;
                    break;
            }
        } else {
            if (in_array($loginType['push']['object']->getLoginType(),array(1,3,4))) { // Pop, HD  ou REQUEST login
				$retUserSt = $loginDAO->checkUser($loginType['push']['object']); 
                if (!$retUserSt['status'] || $retUserSt['push']['object']->getUserStatus() == 'I'){
                    $msg = $this->translator->translate('Login_user_inactive');
				}elseif($retUserSt['push']['object']->getUserStatus() == "A"){ 
                    $msg = $this->translator->translate('Login_error_error');

                    // inserts login details in DB
                    $retUserSt['push']['object']->setLoginStatus(0);
                    $insDetail = $loginDAO->insertLoginDetail($retUserSt['push']['object']);
                    if(!$insDetail['status']){
                        $this->logger->error("Can't insert login detail.", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error' => $insDetail['push']['message']]);
                    }else{
                        $this->logger->info("Login detail saved successfully.", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error' => ""]);
                    }
                }
			}

			$success = array("success" => 0,
							 "msg" => $msg );

			echo json_encode($success);
			return;
        }
    }
    
    /**
     * Check if user exists in DB by request
     *
     * @param  loginModel $loginModel
     * @return bool
     */
    public function requestAuth(loginModel $loginModel): bool
    {
		$loginDAO = new loginDAO();
		$retUser = $loginDAO->getUserByLogin($loginModel);

        if($retUser['status'] && $retUser['push']['object']->getIdPerson() > 0){
            $retRequest = $loginDAO->getRequestsByUser($retUser['push']['object']);
			if ($retRequest['status'] && $retRequest['push']['object']->getTotalRequests() == 0) {
                return true;
            } else {
                $checkRequest = $loginDAO->getUserRequests($retUser['push']['object']);
                return ($checkRequest['status'] && $checkRequest['push']['object']->getTotalRequests() == 1) ? true : false;
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
            return $loginUser['push']['object']->getIdPerson() == 0 ? false : true;
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
    
    /**
     * isGoogleAuthentication
     * 
     * en_us Checks if authentication is by Google API
     * pt_br Verifica se a autenticação é por API do Google
     *
     * @return void
     */
    public function isGoogleAuthentication(){
        $featureDAO = new featureDAO();
        $featureDTO = new featureModel();
        
        //gets authentication method
        $featureDTO->setSettingCatId(5);
        $ret = $featureDAO->fetchConfigsByCategory($featureDTO);
        if(!$ret['status']){
            $this->logger->error("Can't get features data.", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error' => $ret['push']['message']]);
            return false;
        }else{
            $this->logger->info("Features data got successfully.", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            $aFeat = $ret['push']['object']->getSettingsList();
            return (isset($aFeat['SES_AUTH_METHOD']) && $aFeat['SES_AUTH_METHOD'] == 'google') ? true : false;
        }

       
    }
    
    /**
     * loginErrorMessage
     * 
     * en_us Displays error screen
     * pt_br Exibe a tela de erro
     *
     * @param  mixed $msgType
     * @return void
     */
    public function loginErrorMessage($msgType)
    {
		$params = $this->makeScreenLogin();
        $params['loginErrorMsg'] = $msgType;
        
        $this->view('admin','login-error-msg',$params);
    }
    
    /**
     * check
     * 
     * en_us Checks if login is admin when authentication with Google is enabled
     * pt_br Verifica se o login é admin quando a autenticação com o Google está ativada
     *
     * @return void
     */
    public function check()
    {
        if($_POST['login'] == 'admin'){
            $st = 1;
            $msg = "";
            $redirect = 'auth';
        }else{
            $st = 0;
            $msg = $this->translator->translate('click_login_google');
            $redirect = "";
        }

        $success = array(
            "success" => $st,
            "msg" => $msg,
            "redirect" => $redirect
        );
        echo json_encode($success);
        return;
    }
    
    /**
     * getWarningList
     * 
     * en_us Returns warnings list to show in login screen
     * pt_br Retorna lista de avisos para mostrar na tela de login
     *
     * @return array
     */
    public function getWarningList(): array
    {
        $warningDAO = new warningDAO();

        $aRet = array();
        $where = "AND (a.dtend > NOW() AND a.dtstart <= NOW() OR a.dtend = '0000-00-00 00:00:00') AND a.showin IN (2,3)";
        $order = "ORDER BY dtstart ASC";

        $ret = $warningDAO->queryWarnings($where,null,$order);
        if(!$ret['status']){
            $this->logger->error("Can't get warnings list.", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error' => $ret['push']['message']]);
        }else{
            $this->logger->info("Warnings list got successfully.", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            $aRet = $ret['push']['object']->getGridList();
        }        
        
        return $aRet;
    }
    
    /**
     * viewWarning
     * 
     * en_us Returns warning data to show in modal
     * pt_br Retorna lista de avisos para mostrar no modal
     *
     * @return void
     */
    public function viewWarning()
    {
        $warningDAO = new warningDAO();
        $warningDTO = new warningModel();
        $warningDTO->setWarningId($_POST['messageId']);
        
        $ret = $warningDAO->getWarning($warningDTO);
        if(!$ret['status']){
            $this->logger->error("Can't get warning data.", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__,'Error' => $ret['push']['message']]);

            $st = false;
            $topicTitle = "";
            $warningTitle = "";
            $warningDescription = "";
            $validity = "";
        }else{
            $this->logger->info("Warning data got successfully.", ['Class' => __CLASS__,'Method' => __METHOD__,'Line' => __LINE__]);
            
            $st = true;
            $topicTitle = $ret['push']['object']->getTopicTitle();
            $warningTitle = $ret['push']['object']->getWarningTitle();
            $warningDescription = $ret['push']['object']->getWarningDescription();
            $startDate = $this->appSrc->_formatDateHour($ret['push']['object']->getStartDate());

            $validity = "{$this->translator->translate('Valid')}: {$startDate} ";
            if(empty($ret['push']['object']->getEndDate()) || $ret['push']['object']->getEndDate() == '0000-00-00 00:00:00'){
                $validity .= "{$this->translator->translate('until_closed')}";
            }else{
                $validity .= "{$this->translator->translate('until')} {$this->appSrc->_formatDateHour($ret['push']['object']->getEndDate())}";
            }            
        }
        
        $aRet = array(
            "success" => $st,
            "topicTitle" => $topicTitle,
            "warningTitle" => $warningTitle,
            "warningDescription" => $warningDescription,
            "validity" => $validity
        );
        
        echo json_encode($aRet);
    }

}